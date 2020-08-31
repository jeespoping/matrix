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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Gastos Distribuidos x Subproceso y Rubro</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc85.php Ver. 2016-03-10</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc85.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2)  or ((!isset($wcco1) or !isset($wcco2)) and !isset($wccof))  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GASTOS DISTRIBUIDOS X SUBPROCESO Y RUBRO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and strtoupper ($call) == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Inicial</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Final</td>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion (1 - SRG / 2 - SG / 3 - S / 4 - RG)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>SubProceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Subcod, Subdes from ".$empresa."_000104 order by Subcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wsubp'>";
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
			for ($wcco=$wcco1;$wcco<=$wcco2;$wcco++)
			{
				if(isset($wccof))
				{
					$ini=strpos($wccof,"-");
					$wcco1=substr($wccof,0,$ini);
				}
				while(strlen($wcco) < 4)
					$wcco = "0".$wcco;
				$query = "SELECT ccocod, cconom from ".$empresa."_000005 where ccocod='".$wcco."' and ccoest='on' and ccocos='S' and ccoemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$wuni=$row[0]."-".$row[1];
					$wres=strtoupper ($wres);
					switch ($wres)
					{
						case 1:
							$query = "select Gassub,Subdes,Gasrub,Rubdes, Gasgas,Cognom,Gasmes,sum(Gasval)  as monto from ".$empresa."_000087,".$empresa."_000104,".$empresa."_000103,".$empresa."_000079 ";
							$query = $query."  where Gasano  = ".$wanop;
							$query = $query."    and Gasemp = '".$wemp."' ";
							$query = $query."    and Gasmes  between ".$wper1." and ".$wper2;
							$query = $query."    and Gascco = '".$wcco."'";
							$query = $query."    and Gassub = Subcod ";
							$query = $query."    and Gasrub = Rubcod ";
							$query = $query."    and Gasgas =Cogcod ";
							$query = $query."   group by  Gassub,Subdes,Gasrub,Rubdes, Gasgas,Cognom,Gasmes     ";
							$query = $query."   order by Gassub,Gasrub, Gasgas,Gasmes ";
						break;
						case 2:
							$query = "select Gassub,Subdes, Gasgas,Cognom,Gasmes,sum(Gasval)  as monto from ".$empresa."_000087,".$empresa."_000104,".$empresa."_000079 ";
							$query = $query."  where Gasano  = ".$wanop;
							$query = $query."    and Gasemp = '".$wemp."' ";
							$query = $query."    and Gasmes  between ".$wper1." and ".$wper2;
							$query = $query."    and Gascco = '".$wcco."'";
							$query = $query."    and Gassub = Subcod ";
							if(substr($wsubp,0,strpos($wsubp,"-")) != "*")
								$query = $query." and Subcod = '".substr($wsubp,0,strpos($wsubp,"-"))."'";
							$query = $query."    and Gasgas = Cogcod ";
							$query = $query."   group by Gassub,Subdes, Gasgas,Cognom,Gasmes     ";
							$query = $query."   order by Gassub, Gasgas,Gasmes ";
						break;
						case 3:
							$query = "select Gassub,Subdes,Gasmes,sum(Gasval)  as monto from ".$empresa."_000087,".$empresa."_000104 ";
							$query = $query."  where Gasano  = ".$wanop;
							$query = $query."    and Gasemp = '".$wemp."' ";
							$query = $query."    and Gasmes  between ".$wper1." and ".$wper2;
							$query = $query."    and Gascco = '".$wcco."'";
							$query = $query."    and Gassub = Subcod ";
							$query = $query."   group by Gassub,Subdes,Gasmes    ";
							$query = $query."   order by Gassub,Gasmes ";
						break;
						case 4:
							$query = "select Gasrub,Rubdes, Gasgas,Cognom,Gasmes,sum(Gasval)  as monto from ".$empresa."_000087,".$empresa."_000103,".$empresa."_000079 ";
							$query = $query."  where Gasano  = ".$wanop;
							$query = $query."    and Gasemp = '".$wemp."' ";
							$query = $query."    and Gasmes  between ".$wper1." and ".$wper2;
							$query = $query."    and Gascco = '".$wcco."'";
							$query = $query."    and Gasrub = Rubcod ";
							$query = $query."    and Gasgas = Cogcod ";
							$query = $query."   group by Gasrub,Rubdes, Gasgas,Cognom,Gasmes     ";
							$query = $query."   order by Gasrub, Gasgas,Gasmes";
						break;
					}
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					echo "<table border=1>";
					echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
					echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
					echo "<tr><td colspan=16 align=center>GASTOS DISTRIBUIDOS X SUBPROCESO Y RUBRO</td></tr>";
					echo "<tr><td colspan=16 align=center>EMPRESA : ".$wempt."</td></tr>";
					echo "<tr><td colspan=16 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
					echo "<tr><td colspan=16 align=center>UNIDAD : ".$wuni."</td></tr>";
					$wdat=array();
					$wtot1=array();
					$wtot2=array();
					for ($i=$wper1;$i<=$wper2;$i++)
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
					$seg=-1;
					$segn="";
					$wdatt[0]="TOTAL";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						switch ($wres)
						{
							case 1:
								if($row[0].$row[2].$row[4] != $segn)
								{
									$seg++;
									$segn=$row[0].$row[2].$row[4];
									$wdat[$seg][0]=$row[0]."_".$row[2]."_".$row[4];
									$wdat[$seg][1]=$row[1]."_".$row[3]."_".$row[5];
									for ($j=2;$j<14;$j++)
										$wdat[$seg][$j]=0;
								}
							break;
							case 2:
								if($row[0].$row[2] != $segn)
								{
									$seg++;
									$segn=$row[0].$row[2];
									$wdat[$seg][0]=$row[0]."_".$row[2];
									$wdat[$seg][1]=$row[1]."_".$row[3];
									for ($j=2;$j<14;$j++)
										$wdat[$seg][$j]=0;
								}
							break;
							case 3:
								if($row[0] != $segn)
								{
									$seg++;
									$segn=$row[0];
									$wdat[$seg][0]=$row[0];
									$wdat[$seg][1]=$row[1];
									for ($j=2;$j<14;$j++)
										$wdat[$seg][$j]=0;
								}
							break;	
							case 4:
								if($row[0].$row[2] != $segn)
								{
									$seg++;
									$segn=$row[0].$row[2];
									$wdat[$seg][0]=$row[0]."_".$row[2];
									$wdat[$seg][1]=$row[1]."_".$row[3];
									for ($j=2;$j<14;$j++)
										$wdat[$seg][$j]=0;
								}
							break;
						}
						switch ($wres)
						{
							case 1:
								$wpos=7;
							break;
							case 2:
								$wpos=5;
							break;
							case 3:
								$wpos=3;
							break;
							case 4:
								$wpos=5;
							break;
						}
						$wdat[$seg][$row[$wpos-1]+1]+=$row[$wpos];
					}
					if($num > 0)
					{
						echo "<tr><td><b>ITEM</b></td><td><b>DESCRIPCION</b></td>";
						for ($i=$wper1;$i<=$wper2;$i++)
							echo "<td align=center><b>".$wmeses[$i]."</b></td>";
						echo "</tr>";
						switch ($wres)
						{
							case 1:
								
								$ini1=strpos($wdat[0][0],"_");
								$ini2=strrpos($wdat[0][0],"_");
								$key1=substr($wdat[0][0],0,$ini1);
								$key2=substr($wdat[0][0],$ini1+1,$ini2-($ini1+1));
								$ini1=strpos($wdat[0][1],"_");
								$ini2=strrpos($wdat[0][1],"_");
								$tit1=substr($wdat[0][1],0,$ini1);
								$tit2=substr($wdat[0][1],$ini1+1,$ini2-($ini1+1));
								echo "<tr><td colspan=16><b>SUBPROCESO: ".$key1."_".$tit1."</b></td></tr>";
								echo "<tr><td colspan=16><b>RUBRO: ".$key2."_".$tit2."</b></td></tr>";
								for ($j=1;$j<13;$j++)
									$wtot1[$j]=0;
								for ($j=1;$j<13;$j++)
									$wtot2[$j]=0;
							break;
							case 2:
								$ini1=strpos($wdat[0][0],"_");
								$key1=substr($wdat[0][0],0,$ini1);
								$ini1=strpos($wdat[0][1],"_");
								$tit1=substr($wdat[0][1],0,$ini1);
								echo "<tr><td colspan=16><b>SUBPROCESO: ".$key1."_".$tit1."</b></td></tr>";
								for ($j=1;$j<13;$j++)
									$wtot1[$j]=0;
							break;
							case 4:
								$ini1=strpos($wdat[0][0],"_");
								$key1=substr($wdat[0][0],0,$ini1);
								$ini1=strpos($wdat[0][1],"_");
								$tit1=substr($wdat[0][1],0,$ini1);
								echo "<tr><td colspan=16><b>RUBRO: ".$key1."_".$tit1."</b></td></tr>";
								for ($j=1;$j<13;$j++)
									$wtot1[$j]=0;
							break;
						}
						for ($i=0;$i<=$seg;$i++)
						{
							switch ($wres)
							{
								case 1:
									$ini1=strpos($wdat[$i][0],"_");
									if(substr($wdat[$i][0],0,$ini1) != $key1)
									{
										echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL RUBRO</b></td>";
										for ($j=$wper1;$j<=$wper2;$j++)
											echo "<td align=right bgcolor='#FFCC66'><b>".number_format((double)$wtot2[$j],2,'.',',')."</b></td>";	
										echo "</tr>";	
										echo"<tr><td bgcolor='#99CCFF' colspan=2><b>TOTAL SUBPROCESO</b></td>";
										for ($j=$wper1;$j<=$wper2;$j++)
											echo "<td align=right bgcolor='#99CCFF'><b>".number_format((double)$wtot1[$j],2,'.',',')."</b></td>";	
										echo "</tr>";	
										$ini1=strpos($wdat[$i][0],"_");
										$ini2=strrpos($wdat[$i][0],"_");
										$key1=substr($wdat[$i][0],0,$ini1);
										$key2=substr($wdat[$i][0],$ini1+1,$ini2-($ini1+1));
										$ini1=strpos($wdat[$i][1],"_");
										$ini2=strrpos($wdat[$i][1],"_");
										$tit1=substr($wdat[$i][1],0,$ini1);
										$tit2=substr($wdat[$i][1],$ini1+1,$ini2-($ini1+1));
										echo "<tr><td colspan=16><b>SUBPROCESO: ".$key1."_".$tit1."</b></td></tr>";
										echo "<tr><td colspan=16><b>RUBRO: ".$key2."_".$tit2."</b></td></tr>";
										for ($j=1;$j<13;$j++)
											$wtot1[$j]=0;
										for ($j=1;$j<13;$j++)
											$wtot2[$j]=0;
									}
									$ini1=strpos($wdat[$i][0],"_");
									$ini2=strrpos($wdat[$i][0],"_");
									if(substr($wdat[$i][0],$ini1+1,$ini2-($ini1+1)) != $key2)
									{
										echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL RUBRO</b></td>";
										for ($j=$wper1;$j<=$wper2;$j++)
											echo "<td align=right bgcolor='#FFCC66'><b>".number_format((double)$wtot2[$j],2,'.',',')."</b></td>";	
										echo "</tr>";	
										$ini1=strpos($wdat[$i][0],"_");
										$ini2=strrpos($wdat[$i][0],"_");
										$key2=substr($wdat[$i][0],$ini1+1,$ini2-($ini1+1));
										$ini1=strpos($wdat[$i][1],"_");
										$ini2=strrpos($wdat[$i][1],"_");
										$tit2=substr($wdat[$i][1],$ini1+1,$ini2-($ini1+1));
										echo "<tr><td colspan=16><b>RUBRO: ".$key2."_".$tit2."</b></td></tr>";
										for ($j=1;$j<13;$j++)
											$wtot2[$j]=0;
									}
									$ini2=strrpos($wdat[$i][1],"_");
									$ini1=strrpos($wdat[$i][0],"_");
									echo "<tr><td><b>GASTO: ".substr($wdat[$i][0],$ini1+1)."</b></td><td><b>".substr($wdat[$i][1],$ini2+1)."</b></td>";
									for ($j=$wper1;$j<=$wper2;$j++)
									{
										$wtot1[$j]+=$wdat[$i][$j+1];
										$wtot2[$j]+=$wdat[$i][$j+1];
										$path="/matrix/presupuestos/reportes/000001_rc143.php?wanop=".$wanop."&wper1=".$j."&wper2=".$j."&wcco1=".$wcco1."&wgas=".substr($wdat[$i][0],$ini1+1)."&wsub=".str_replace("_","-",$wdat[$i][0])."&wgasn=".substr($wdat[$i][0],$ini1+1)."&wsubn=0&empresa=".$empresa."&wemp=".$wempt;
										echo "<td align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	
									}			
									echo "</tr>";	
								break;
								case 2:
									$ini1=strpos($wdat[$i][0],"_");
									if(substr($wdat[$i][0],0,$ini1) != $key1)
									{
										echo"<tr><td bgcolor='#99CCFF' colspan=2><b>TOTAL SUBPROCESO</b></td>";
										for ($j=$wper1;$j<=$wper2;$j++)
											echo "<td align=right bgcolor='#99CCFF'><b>".number_format((double)$wtot1[$j],2,'.',',')."</b></td>";	
										echo "</tr>";	
										$ini1=strpos($wdat[$i][0],"_");
										$key1=substr($wdat[$i][0],0,$ini1);
										$ini1=strpos($wdat[$i][1],"_");
										$tit1=substr($wdat[$i][1],0,$ini1);
										echo "<tr><td colspan=16><b>SUBPROCESO: ".$key1."_".$tit1."</b></td></tr>";
										for ($j=1;$j<13;$j++)
											$wtot1[$j]=0;
									}
									$ini2=strrpos($wdat[$i][1],"_");
									$ini1=strrpos($wdat[$i][0],"_");
									echo "<tr><td><b>GASTO: ".substr($wdat[$i][0],$ini1+1)."</b></td><td><b>".substr($wdat[$i][1],$ini2+1)."</b></td>";
									for ($j=$wper1;$j<=$wper2;$j++)
									{
										$wtot1[$j]+=$wdat[$i][$j+1];
										$path="/matrix/presupuestos/reportes/000001_rc143.php?wanop=".$wanop."&wper1=".$j."&wper2=".$j."&wcco1=".$wcco1."&wgas=".substr($wdat[$i][0],$ini1+1)."&wsub=".str_replace("_","-",$wdat[$i][0])."&wgasn=".substr($wdat[$i][0],$ini1+1)."&wsubn=0&empresa=".$empresa."&wemp=".$wempt;
										echo "<td align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	
									}			
									echo "</tr>";
								break;
								case 3:
									echo "<tr><td><b>SUBPROCESO: ".$wdat[$i][0]."</b></td><td><b>".$wdat[$i][1]."</b></td>";
									for ($j=$wper1;$j<=$wper2;$j++)
										echo "<td align=right>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	
									echo "</tr>";
								break;
								case 4:
									$ini1=strpos($wdat[$i][0],"_");
									if(substr($wdat[$i][0],0,$ini1) != $key1)
									{
										echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL RUBRO</b></td>";
										for ($j=$wper1;$j<=$wper2;$j++)
											echo "<td align=right bgcolor='#FFCC66'><b>".number_format((double)$wtot1[$j],2,'.',',')."</b></td>";	
										echo "</tr>";	
										$ini1=strpos($wdat[$i][0],"_");
										$key1=substr($wdat[$i][0],0,$ini1);
										$ini1=strpos($wdat[$i][1],"_");
										$tit1=substr($wdat[$i][1],0,$ini1);
										echo "<tr><td colspan=16><b>RUBRO: ".$key1."_".$tit1."</b></td></tr>";
										for ($j=1;$j<13;$j++)
											$wtot1[$j]=0;
									}
									$ini2=strrpos($wdat[$i][1],"_");
									$ini1=strrpos($wdat[$i][0],"_");
									echo "<tr><td><b>GASTO: ".substr($wdat[$i][0],$ini1+1)."</b></td><td><b>".substr($wdat[$i][1],$ini2+1)."</b></td>";
									for ($j=$wper1;$j<=$wper2;$j++)
									{
										$wtot1[$j]+=$wdat[$i][$j+1];
										$path="/matrix/presupuestos/reportes/000001_rc143.php?wanop=".$wanop."&wper1=".$j."&wper2=".$j."&wcco1=".$wcco1."&wgas=".substr($wdat[$i][0],$ini1+1)."&wsub=*-TODOS&wgasn=".substr($wdat[$i][0],$ini1+1)."&wsubn=0&empresa=".$empresa."&wemp=".$wempt;
										echo "<td align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	
									}			
									echo "</tr>";
								break;
							}
					}
					switch ($wres)
					{
						case 1:
							echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL RUBRO</b></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td align=right bgcolor='#FFCC66'><b>".number_format((double)$wtot2[$j],2,'.',',')."</b></td>";	
							echo "</tr>";	
							echo"<tr><td bgcolor='#99CCFF' colspan=2><b>TOTAL SUBPROCESO</b></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td align=right bgcolor='#99CCFF'><b>".number_format((double)$wtot1[$j],2,'.',',')."</b></td>";	
							echo "</tr>";	
						break;
						case 2:
							echo"<tr><td bgcolor='#99CCFF' colspan=2><b>TOTAL SUBPROCESO</b></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td align=right bgcolor='#99CCFF'><b>".number_format((double)$wtot1[$j],2,'.',',')."</b></td>";	
							echo "</tr>";	
						break;
						case 4:
							echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL RUBRO</b></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td align=right bgcolor='#FFCC66'><b>".number_format((double)$wtot1[$j],2,'.',',')."</b></td>";	
							echo "</tr>";	
						break;
					}
					echo "</table>";	
				}
			}
		}
	}
}
?>
</body>
</html>
