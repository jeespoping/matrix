<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle de Inversiones Presupuestadas</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc109.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc109.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or (!isset($t1) and !isset($t2) and !isset($t3)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DETALLE DE INVERSIONES PRESUPUESTADAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIC") or (isset($call) and $call == "SIG"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
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
				echo "</td></tr>";
			}
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";	 
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=2 align=center><input type='checkbox' name='t3'> X CENTROS DE COSTOS <input type='checkbox' name='t1'>X MESES <input type='checkbox' name='t2'> X GRUPOS </td></tr>";
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
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$wres=strtoupper ($wres);
			if($wres == "D")
			{
				$pos=3;
				$mon=7;
				$mes=2;
				$query = "select  Invcco,cconom,Invmes,Invcod,Mginom, Invact, Invdes, Invmon   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
			}
			elseif(isset($t3) and isset($t1) and !isset($t2))
					{
						$mon=3;
						$mes=2;
						$query = "select  Invcco,cconom,Invmes, sum(Invmon)   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
					}
					elseif(isset($t3) and !isset($t1) and isset($t2))
							{
								$pos=2;
								$mon=4;
								$query = "select  Invcco,cconom,Invcod,Mginom, sum(Invmon)   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
							}
							elseif(isset($t3) and isset($t1) and isset($t2))
									{
										$pos=3;
										$mon=5;
										$mes=2;
										$query = "select  Invcco,cconom,Invmes,Invcod,Mginom, sum(Invmon)   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
									}
									elseif(isset($t3) and !isset($t1) and !isset($t2))
											{
												$mon=2;
												$query = "select  Invcco,cconom, sum(Invmon)   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
											}
											elseif(!isset($t3) and isset($t1) and isset($t2))
													{
														$pos=1;
														$mon=3;
														$mes=0;
														$query = "select Invmes,Invcod,Mginom, sum(Invmon)   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
													}
													elseif(!isset($t3) and !isset($t1) and isset($t2))
															{
																$pos=0;
																$mon=2;
																$query = "select Invcod,Mginom, sum(Invmon)   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
															}
															else
															{
																$mon=1;
																$mes=0;
																$query = "select Invmes,sum(Invmon)   from ".$empresa."_000019,".$empresa."_000005,".$empresa."_000029 ";
															}
			$query = $query."  where Invano = ".$wanop;
			$query = $query."    and Invemp = '".$wemp."' ";
			$query = $query."    and Invcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and Invcco = ccocod ";
			$query = $query."    and Invemp = ccoemp ";
			$query = $query."    and Invcod = Mgicod ";
			if($wres == "D")
				if(isset($t3) and !isset($t1) and !isset($t2))
					$query = $query."   order by Invcco ";
					elseif(isset($t3) and isset($t1) and !isset($t2))
								$query = $query."   order by Invcco,Invmes ";
							elseif(isset($t3) and !isset($t1) and isset($t2))
										$query = $query."   order by Invcco,Invcod ";
									elseif(isset($t3) and isset($t1) and isset($t2))
												$query = $query."   order by Invcco,Invmes,Invcod ";
											elseif(!isset($t3) and isset($t1) and isset($t2))
														$query = $query."   order by Invmes,Invcod ";
													elseif(!isset($t3) and isset($t1) and !isset($t2))
																$query = $query."   order by Invmes ";
															else
																$query = $query."   order by Invcod ";
			else
				if(isset($t3) and isset($t1) and !isset($t2))
				{
					$query = $query."   group  by Invcco,cconom,Invmes ";
					$query = $query."   order by Invcco,Invmes ";
				}
				elseif(isset($t3) and !isset($t1) and isset($t2))
						{
							$query = $query."   group  by Invcco,cconom,Invcod,Mginom ";
							$query = $query."   order by Invcco,Invcod ";
						}
						elseif(isset($t3) and isset($t1) and isset($t2))
								{
									$query = $query."   group  by Invcco, cconom, Invmes, Invcod, Mginom ";
									$query = $query."   order by Invcco, Invmes, Invcod ";
								}
								elseif(isset($t3) and !isset($t1) and !isset($t2))
										{
											$query = $query."   group  by Invcco, cconom ";
											$query = $query."   order by Invcco ";
										}
										elseif(!isset($t3) and isset($t1) and isset($t2))
												{
													$query = $query."   group  by Invmes, Invcod, Mginom ";
													$query = $query."   order by Invmes, Invcod ";
												}
												elseif(!isset($t3) and !isset($t1) and isset($t2))
														{
															$query = $query."   group  by Invcod, Mginom ";
															$query = $query."   order by  Invcod ";
														}
														else
														{
															$query = $query."   group  by  Invmes ";
															$query = $query."   order by Invmes ";
														}
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wcco="";
			$wccodes="";
			$wtip="";
			$wtipdes="";
			$wmes=0;
			$wtotmes=0;
			$wtottip=0;
			$wtotcco=0;
			$wtotgen=0;
			$wmeses=array();
			for ($i=1;$i<=12;$i++)
			{
				switch ($i)
					{
						case 1:
							$wmeses[$i]="ENERO";
							break;
						case 2:
							$wmeses[$i]="FEBRERO";
							break;
						case 3:
							$wmeses[$i]="MARZO";
							break;
						case 4:
							$wmeses[$i]="ABRIL";
							break;
						case 5:
							$wmeses[$i]="MAYO";
							break;
						case 6:
							$wmeses[$i]="JUNIO";
							break;
						case 7:
							$wmeses[$i]="JULIO";
							break;
						case 8:
							$wmeses[$i]="AGOSTO";
							break;
						case 9:
							$wmeses[$i]="SEPTIEMBRE";
							break;
						case 10:
							$wmeses[$i]="OCTUBRE";
							break;
						case 11:
							$wmeses[$i]="NOVIEMBRE";
							break;
						case 12:
							$wmeses[$i]="DICIEMBRE";
							break;
						}
			}
			echo "<center><table border=1>";
			echo "<tr><td colspan=3 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=3 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=3 align=center>DETALLE DE INVERSIONES PRESUPUESTADAS</td></tr>";
			echo "<tr><td colspan=3 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=3 align=center>A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=3 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			if($wres == "D")
				echo "<tr><td bgcolor=#cccccc><b>CODIGO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>MONTO</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if(isset($t3) and $wcco != $row[0])
				{
					if($wcco != "")
					{
						if(isset($t2) and $wres == "R")
							echo "<tr><td colspan=2 bgcolor=#ffff33>TOTAL GRUPO ".$wtip."-".$wtipdes."</td><td bgcolor=#ffff33 align=right>".number_format((double)$wtottip,0,'.',',')."</td></tr>";
						if(isset($t1) and $wres == "R")
							echo "<tr><td colspan=2 bgcolor=#99ccff>TOTAL MES DE ".$wmeses[$wmes]."</td><td bgcolor=#99ccff align=right>".number_format((double)$wtotmes,0,'.',',')."</td></tr>";
						echo "<tr><td colspan=2 bgcolor=#dddddd>TOTAL C.C. ".$wcco."-".$wccodes."</td><td bgcolor=#dddddd align=right> ".number_format((double)$wtotcco,0,'.',',')."</td></tr>";
					}
					if($wres == "D")
						echo "<tr><td colspan=3 bgcolor=#ffcc66 align=center>C.C. ".$row[0]."-".$row[1]."</td></tr>";
					$wcco=$row[0];
					$wccodes=$row[1];
					$wmes=0;
					$wtip="";
					$wtotcco=0;
					$wtotmes=0;
					$wtottip=0;
				}
				if(isset($t1) and $row[$mes] != $wmes)
				{
					if($wmes != 0)
					{
						if(isset($t2) and $wres == "R")
							echo "<tr><td colspan=2 bgcolor=#ffff33>TOTAL GRUPO ".$wtip."-".$wtipdes."</td><td bgcolor=#ffff33 align=right>".number_format((double)$wtottip,0,'.',',')."</td></tr>";
						if($wres == "R")
							echo "<tr><td colspan=2 bgcolor=#99ccff>TOTAL MES DE ".$wmeses[$wmes]."</td><td bgcolor=#99ccff align=right>".number_format((double)$wtotmes,0,'.',',')."</td></tr>";
						$wtotmes=0;
						$wtip="";
						$wtottip=0;
					}
					if($wres == "D")
						echo "<tr><td colspan=3 align=center bgcolor=#99ccff>MES DE ".$wmeses[$row[2]]."</td></tr>";
					$wmes=$row[$mes];
				}
				if(isset($t2) and $row[$pos] != $wtip)
				{
					if($wtip != "")
					{
						if($wres == "R")
							echo "<tr><td colspan=2 bgcolor=#ffff33>TOTAL GRUPO ".$wtip."-".$wtipdes."</td><td bgcolor=#ffff33 align=right>".number_format((double)$wtottip,0,'.',',')."</td></tr>";
						$wtottip=0;
					}
					if($wres == "D")
						echo "<tr><td colspan=3 align=center bgcolor=#ffff33>GRUPO ".$row[$pos]."-".$row[$pos+1]."</td></tr>";
					$wtip=$row[$pos];
					$wtipdes=$row[$pos+1];
				}
				$wtotcco+=$row[$mon];
				$wtottip+=$row[$mon];
				$wtotmes+=$row[$mon];
				$wtotgen+=$row[$mon];
				if($wres == "D")
					echo "<tr><td>".$row[5]."</td><td>".$row[6]."</td><td align=right>".number_format((double)$row[7],0,'.',',')."</td></tr>";
			}
			if(isset($t1) and $wres == "R")
				echo "<tr><td colspan=2 bgcolor=#99ccff>TOTAL MES DE ".$wmeses[$wmes]."</td><td bgcolor=#99ccff align=right>".number_format((double)$wtotmes,0,'.',',')."</td></tr>";
			if(isset($t2) and $wres == "R")
				echo "<tr><td colspan=2 bgcolor=#ffff33>TOTAL GRUPO ".$wtip."-".$wtipdes."</td><td bgcolor=#ffff33 align=right>".number_format((double)$wtottip,0,'.',',')."</td></tr>";
			if(isset($t3))
				echo "<tr><td colspan=2 bgcolor=#dddddd>TOTAL C.C. ".$wcco."-".$wccodes."</td><td bgcolor=#dddddd align=right>".number_format((double)$wtotcco,0,'.',',')."</td></tr>";
			echo "<tr><td colspan=2  bgcolor=#cccccc>TOTAL INVERSIONES </td><td bgcolor=#cccccc align=right>".number_format((double)$wtotgen,0,'.',',')."</td></tr>";
		}
	}
?>
</body>
</html>
