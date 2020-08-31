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
		document.forms.rc04.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Estado de Resultados Comparativo Mes (Presupuestado)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc04.php Ver. 2016-08-19</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc04' action='000001_rc04.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ESTADO DE RESULTADOS COMPARATIVO MES (PRESUPUESTADO)</td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				if(isset($wemp) and $wemp != "Seleccione")
				{
					$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod and Empresa = '".substr($wemp,0,strpos($wemp,"-"))."' order by Cc";
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
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Centros de Servicio ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wres=strtoupper ($wres);
			$wserv=strtoupper ($wserv);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$wmeses=array();
			for ($i=$wper1;$i<=$wper2;$i++)
			{
				switch ($i)
					{
						case 1:
							$wmese[$i]="ENERO";
							break;
						case 2:
							$wmese[$i]="FEBRERO";
							break;
						case 3:
							$wmese[$i]="MARZO";
							break;
						case 4:
							$wmese[$i]="ABRIL";
							break;
						case 5:
							$wmese[$i]="MAYO";
							break;
						case 6:
							$wmese[$i]="JUNIO";
							break;
						case 7:
							$wmese[$i]="JULIO";
							break;
						case 8:
							$wmese[$i]="AGOSTO";
							break;
						case 9:
							$wmese[$i]="SEPTIEMBRE";
							break;
						case 10:
							$wmese[$i]="OCTUBRE";
							break;
						case 11:
							$wmese[$i]="NOVIEMBRE";
							break;
						case 12:
							$wmese[$i]="DICIEMBRE";
							break;
					}
			}
			$query = "SELECT cierre_pptal from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
			$query = "SELECT rescpr,resper,mganom,sum(resmon) as wmonto from ".$empresa."_000043,".$empresa."_000028 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and rescco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and resper between ".$wper1." and ".$wper2;
			$query = $query."    and rescpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by rescpr,resper,mganom";
			$query = $query."   order by rescpr,resper";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=14 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=14 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=14 align=center>ESTADO DE RESULTADOS COMPARATIVO MES (PRESUPUESTADO)</td></tr>";
			echo "<tr><td colspan=14 align=center><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td colspan=14 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=14 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td><b>RUBRO</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td align=right><b>".$wmese[$i]."</b></td>";
			echo "<td align=right><b>TOTAL</b></td><tr>";
			//echo "<tr><td><b>RUBRO</b></td><td><b>ENERO</b></td><td><b>FEBRERO</b></td><td><b>MARZO</b></td><td><b>ABRIL</b></td><td><b>MAYO</b></td><td><b>JUNIO</b></td><td><b>JULIO</b></td><td><b>AGOSTO</b></td><td><b>SEPTIEMBRE</b></td><td><b>OCTUBRE</b></td><td><b>NOVIEMBRE</b></td><td><b>DICIEMBRE</b></td><td><b>TOTAL</b></td></tr>";
			$wtotal=array();
			$wtotalI=array();
			$ita=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($i == 0)
				{
					$cprant=$row[0];
					$nomant=$row[2];
					for ($j=0;$j<12;$j++)
					{
						$wtotalI[$j]=0;
					}
				}
				$it=(integer)substr($row[0],0,1);
				if($cprant != $row[0] and substr($cprant,0,1) < 7)
				{
					if($wres == "D")
                    	echo"<tr><td>".$nomant."</td>";
                    $WT=0;
                    for ($j=$wper1-1;$j<$wper2;$j++)
					{
						$WT=$WT+$wtotalI[$j];
						if($wres == "D")
							echo "<td align=right>".number_format((double)$wtotalI[$j],0,'.',',')."</td>";
					}
					if($wres == "D")
						echo "<td align=right>".number_format((double)$WT,0,'.',',')."</td></tr>";
					$cprant=$row[0];
					$nomant=$row[2];
					for ($j=0;$j<12;$j++)
					{
						$wtotalI[$j]=0;
					}
				}
				$wtotalI[$row[1]-1]=$wtotalI[$row[1]-1]+$row[3];
				if(!isset($wtotal[$it][$wper1-1]))
				{
					for ($j=0;$j<12;$j++)
					{
						$wtotal[$it][$j]=0;
					}
					switch ($ita)
					{
						case 1:
						$ita=7;
						echo"<tr><td><b>INGRESOS NETOS</b></td>";
						$WT=0;
						for ($j=$wper1-1;$j<$wper2;$j++)
						{
							$WT=$WT+$wtotal[1][$j];
							echo "<td align=right><b>".number_format((double)$wtotal[1][$j],0,'.',',')."</b></td>";
						}
						echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
						break;
						case 2:
						$ita=7;
                    	echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td>";
                    	$WT=0;
                    	for ($j=$wper1-1;$j<$wper2;$j++)
						{
							$WT=$WT+$wtotal[2][$j];
							echo "<td align=right><b>".number_format((double)$wtotal[2][$j],0,'.',',')."</b></td>";
						}
						echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
						echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
						 for ($j=$wper1-1;$j<$wper2;$j++)
						{
							if(isset($wtotal[1][$j] ))
                    			$wtotal[9][$j] = $wtotal[1][$j] - $wtotal[2][$j];
                    		else
                    			$wtotal[9][$j] = 0 - $wtotal[2][$j];
               			}
                    	echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td>";
                    	$WT=0;
                    	for ($j=$wper1-1;$j<$wper2;$j++)
						{
							$WT=$WT+$wtotal[9][$j];
							echo "<td align=right><b>".number_format((double)$wtotal[9][$j],0,'.',',')."</b></td>";
						}
						echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
						break;
						case 3:
						break;
						case 4:
						$ita=7;
                    	echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td>";
                    	$WT=0;
                    	for ($j=$wper1-1;$j<$wper2;$j++)
						{
							$WT=$WT+$wtotal[4][$j];
							echo "<td align=right><b>".number_format((double)$wtotal[4][$j],0,'.',',')."</b></td>";
						}
						echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
						break;
						case 5:
						$ita=7;
                    	echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td>";
                    	$WT=0;
                    	for ($j=$wper1-1;$j<$wper2;$j++)
						{
							$WT=$WT+$wtotal[5][$j];
							echo "<td align=right><b>".number_format((double)$wtotal[5][$j],0,'.',',')."</b></td>";
						}
						echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
						break;
						case 6:
						$ita=7;
                    	echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td>";
                    	$WT=0;
                    	for ($j=$wper1-1;$j<$wper2;$j++)
						{
							$WT=$WT+$wtotal[6][$j];
							echo "<td align=right><b>".number_format((double)$wtotal[6][$j],0,'.',',')."</b></td>";
						}
						echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
						break;
					}
					switch ($it)
					{
						case 1:
						echo "<tr><td colspan=14><b>INGRESOS</B></td></tr>";
						$ita=1;
						break;
						case 2:
						echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=14><b>COSTOS DEL SERVICIO</B></td></tr>";
						$ita=2;
						break;
						case 3:
						echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=14><b>GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
						$ita=3;
						break;
						case 4:
						echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=14><b>INGRESOS NO OPERACIONALES</B></td></tr>";
						$ita=4;
						break;
						case 5:
						echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=14><b>GASTOS NO OPERACIONALES</B></td></tr>";
						$ita=5;
						break;
						case 6:
						echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=14><b>GASTOS FINANCIEROS</B></td></tr>";
						$ita=6;
						break;
					}
				}
				if($it < 7)
				{
					$wtotal[$it][$row[1]-1]=$wtotal[$it][$row[1]-1]+$row[3];
                 }
                 else
                 	if($row[0] == "700")
                 		$wtotal[7][$row[1]-1]=$wtotal[7][$row[1]-1]+$row[3];
                 	else if($row[0] == "750")
                 			{
	                 			if(!isset($wtotal[8][$row[1]-1]))
	                 			{
	                 				for ($j=0;$j<12;$j++)
									{
										$wtotal[8][$j]=0;
									}
								}
                 				$wtotal[8][$row[1]-1]=$wtotal[8][$row[1]-1]+$row[3];
                 			}
                 			else if($row[0] == "760")
                 					{
	                 					if(!isset($wtotal[12][$row[1]-1]))
	                 					{
	                 						for ($j=0;$j<12;$j++)
											{
												$wtotal[12][$j]=0;
											}
										}
                 						$wtotal[12][$row[1]-1]=$wtotal[12][$row[1]-1]+$row[3];
                 					}
                 					else if($row[0] == "900")
										{
											if(!isset($wtotal[90][$row[1]-1]))
											{
												for ($j=0;$j<12;$j++)
												{
													$wtotal[90][$j]=0;
												}
											}
											$wtotal[90][$row[1]-1]=$wtotal[90][$row[1]-1]+$row[3];
										}
    		}
    		if($it < 7)
    		{	
	    		if($wres == "D")
                    echo"<tr><td>".$nomant."</td>";
                 $WT=0;
    			 for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotalI[$j];
					if($wres == "D")
						echo "<td align=right>".number_format((double)$wtotalI[$j],0,'.',',')."</td>";
				}
				if($wres == "D")
					echo "<td align=right>".number_format((double)$WT,0,'.',',')."</td></tr>";
			}
    		switch ($ita)
			{
				case 1:
				echo"<tr><td><b>INGRESOS NETOS</b></td>";
				$WT=0;
				for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[1][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[1][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
				break;
				case 2:
            	echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td>";
            	$WT=0;
            	for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[2][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[2][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
				echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
				 for ($j=$wper1-1;$j<$wper2;$j++)
				{
            		if(isset($wtotal[1][$j] ))
                    	$wtotal[9][$j] = $wtotal[1][$j] - $wtotal[2][$j];
                    else
                    	$wtotal[9][$j] = 0 - $wtotal[2][$j];
       			}
            	echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td>";
            	$WT=0;
            	for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[9][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[9][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
				break;
				case 3:
				break;
				case 4:
            	echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td>";
            	$WT=0;
            	for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[4][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[4][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
				break;
				case 5:
            	echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td>";
            	$WT=0;
            	for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[5][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[5][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
				break;
				case 6:
            	echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td>";
            	$WT=0;
            	for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[6][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[6][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
				break;
			}
    		echo "<tr><td colspan=14 align=center>--------------------------------------------------</td></tr>";
    		for ($i=0;$i<13;$i++)
    			for ($j=$wper1-1;$j<$wper2;$j++)
    				if(!isset($wtotal[$i][$j]))
						$wtotal[$i][$j]=0;
    		if(isset($wtotal[7][$wper1-1]))
    		{
                echo"<tr><td><b>CORRECCION MONETARIA</b></td>";
                $WT=0;
                for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[7][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[7][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
             }
              if(!isset($wtotal[4][$wper1-1]))
    			 for ($j=$wper1-1;$j<$wper2;$j++)
					$wtotal[4][$j]=0;
			  if(!isset($wtotal[5][$wper1-1]))
    			 for ($j=$wper1-1;$j<$wper2;$j++)
					$wtotal[5][$j]=0;
			  if(!isset($wtotal[6][$wper1-1]))
    			 for ($j=$wper1-1;$j<$wper2;$j++)
					$wtotal[6][$j]=0;
			  if(!isset($wtotal[7][$wper1-1]))
    			 for ($j=$wper1-1;$j<$wper2;$j++)
					$wtotal[7][$j]=0;
			  if(!isset($wtotal[9][$wper1-1]))
    			 for ($j=$wper1-1;$j<$wper2;$j++)
					$wtotal[9][$j]=0;
             if(isset($wtotal[4][$wper1-1]) and isset($wtotal[5][$wper1-1]) and isset($wtotal[6][$wper1-1]) and isset($wtotal[7][$wper1-1]) and isset($wtotal[9][$wper1-1]))
    		{
	    		 for ($j=$wper1-1;$j<$wper2;$j++)
				{
	    			$wtotal[10][$j] = $wtotal[9][$j] + $wtotal[4][$j] - $wtotal[5][$j] - $wtotal[6][$j] + $wtotal[7][$j];
				}
                echo"<tr><td><b>UTILIDAD ANTES IMPUESTOS TOTAL</b></td>";
                $WT=0;
                for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[10][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[10][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
             }
             if(!isset($wtotal[12][$wper1-1]))
    			for ($j=0;$j<12;$j++)
					$wtotal[12][$j]=0;
             if(isset($wtotal[12][$wper1-1]))
    		{
                echo"<tr><td><b>PROVISION IMPUESTO DE RENTA</b></td>";
                $WT=0;
                 for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[12][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[12][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
             }  
              if(isset($wtotal[10][$wper1-1]) and isset($wtotal[12][$wper1-1]))
    		{
	    		 for ($j=$wper1-1;$j<$wper2;$j++)
				{
	    			$wtotal[13][$j] = $wtotal[10][$j] - $wtotal[12][$j];
				}
                echo"<tr><td><b>UTILIDAD NETA DESPUES DE IMPUESTOS</b></td>";
                $WT=0;
                for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[13][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[13][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
             }
              if(!isset($wtotal[8][0]))
    			for ($j=0;$j<12;$j++)
					$wtotal[8][$j]=0;
             if(isset($wtotal[8][$wper1-1]))
    		{
                echo"<tr><td><b>PARTICIPACION TERCEROS EN UNIDAD</b></td>";
                $WT=0;
                 for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[8][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[8][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
             }   
			 if(isset($wtotal[8][$wper1-1]) and isset($wtotal[13][$wper1-1]))
    		{
	    		for ($j=$wper1-1;$j<$wper2;$j++)
				{
	    			$wtotal[11][$j] = $wtotal[13][$j] - $wtotal[8][$j];
				}
                echo"<tr><td><b>UTILIDAD DESPUES IMPUESTOS NETO PROMOTORA</b></td>";
                $WT=0;
                for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[11][$j];
					echo "<td align=right><b>".number_format((double)$wtotal[11][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
             }
             if(isset($wtotal[90][$wper1-1]))
    		{
                echo"<tr><td bgcolor=#999999><b>INGRESOS RECIBIDOS PARA TERCEROS</b></td>";
                $WT=0;
                for ($j=$wper1-1;$j<$wper2;$j++)
				{
					$WT=$WT+$wtotal[90][$j];
					echo "<td align=right bgcolor=#999999><b>".number_format((double)$wtotal[90][$j],0,'.',',')."</b></td>";
				}
				echo "<td align=right bgcolor=#999999><b>".number_format((double)$WT,0,'.',',')."</b></td></tr>";
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
		include_once("free.php");
	}
?>
</body>
</html>
