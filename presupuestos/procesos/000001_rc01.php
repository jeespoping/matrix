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
		document.forms.rc01.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Estado de Resultados Acumulado Presupuestado</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc01.php Ver. 2016-08-19</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		
		echo "<form name= 'rc01' action='000001_rc01.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ESTADO DE RESULTADOS ACUMULADO PRESUPUESTADO</td></tr>";
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
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
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
			$query = "SELECT cierre_pptal from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
			$query = "SELECT rescpr,mganom,sum(resmon) as wmonto from ".$empresa."_000043,".$empresa."_000028 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and rescco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and resper between ".$wper1." and ".$wper2;
			$query = $query."    and rescpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by rescpr,mganom";
			$query = $query."   order by rescpr";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=3 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=3 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=3 align=center>ESTADO DE RESULTADOS ACUMULADO PRESUPUESTADO</td></tr>";
			echo "<tr><td colspan=3 align=center><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td colspan=3 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=3 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td><b>RUBRO</b></td><td><b>MONTO</b></td><td><b>PARTICIPACION</b></td></tr>";
			$wtotal=array();
			$ita=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$it=(integer)substr($row[0],0,1);
				if(!isset($wtotal[$it]))
				{
					$wtotal[$it]=0;
					switch ($ita)
					{
						case 1:
						$ita=7;
						$wpor=0;
						echo"<tr><td><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotal[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";;
						break;
						case 2:
						$ita=7;
						if(isset($wtotal[1]) and $wtotal[1] != 0)
                    		$wpor=$wtotal[2]/$wtotal[1] * 100;
                    	else
                    		$wpor=0;
                    	echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotal[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
                    	if(isset($wtotal[1]))
                    		$wtotal[9] = $wtotal[1] - $wtotal[2];
                    	else
                    		$wtotal[9] = 0 - $wtotal[2];
                    	if(isset($wtotal[1]) and $wtotal[1] != 0)
                    		$wpor=$wtotal[9]/$wtotal[1] * 100;
                    	else
                    		$wpor=0;
                    	echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
                    	echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotal[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
						break;
						case 3:
						break;
						case 4:
						$ita=7;
						if(isset($wtotal[1]) and $wtotal[1] != 0)
                    		$wpor=$wtotal[4]/$wtotal[1] * 100;
                    	else
                    		$wpor=0;
                    	echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
						case 5:
						$ita=7;
						if(isset($wtotal[1]) and $wtotal[1] != 0)
                    		$wpor=$wtotal[5]/$wtotal[1] * 100;
                    	else
                    		$wpor=0;
                    	echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
						case 6:
						$ita=7;
						if(isset($wtotal[1]) and $wtotal[1] != 0)
                    		$wpor=$wtotal[6]/$wtotal[1] * 100;
                    	else
                    		$wpor=0;
                    	echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotal[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
					}
					switch ($it)
					{
						case 1:
						echo "<tr><td colspan=3><b>INGRESOS</B></td></tr>";
						$ita=1;
						break;
						case 2:
						echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=3><b>COSTOS DEL SERVICIO</B></td></tr>";
						$ita=2;
						break;
						case 3:
						echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=3><b>GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
						$ita=3;
						break;
						case 4:
						echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=3><b>INGRESOS NO OPERACIONALES</B></td></tr>";
						$ita=4;
						break;
						case 5:
						echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=3><b>GASTOS NO OPERACIONALES</B></td></tr>";
						$ita=5;
						break;
						case 6:
						echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=3><b>GASTOS FINANCIEROS</B></td></tr>";
						$ita=6;
						break;
					}
				}
				if($it < 7)
				{
					$wtotal[$it]=$wtotal[$it]+$row[2];
					if($it > 1)
					{
						if(isset($wtotal[1]) and $wtotal[1] != 0)
                    		$wpor=$row[2]/$wtotal[1] * 100;
                    	else
                    		$wpor=0;
                    	if($wres == "D")
                    		echo"<tr><td>".$row[1]."</td><td align=right>".number_format((double)$row[2],0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
                    }
                    else
                    {
	                    $wpor=0;
	                    if($wres == "D")
                    		echo"<tr><td>".$row[1]."</td><td align=right>".number_format((double)$row[2],0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
                    }
                 }
                 else
                 	if($row[0] == "700")
                 		$wtotal[7]=$wtotal[7]+$row[2];
                 	else if($row[0] == "750")
                 			{
	                 			if(!isset($wtotal[8]))
	                 				$wtotal[8]=0;
                 				$wtotal[8]=$wtotal[8]+$row[2];
                 			}
                 			else if($row[0] == "760")
                 				{
	                 				if(!isset($wtotal[12]))
	                 					$wtotal[12]=0;
                 					$wtotal[12]=$wtotal[12]+$row[2];
                 				}
                 				else if($row[0] == "900")
									{
										if(!isset($wtotal[90]))
											$wtotal[90]=0;
										$wtotal[90]=$wtotal[90]+$row[2];
									}
    		}
    		#echo $ita."<br>";
    		switch ($ita)
			{
				case 1:
				$wpor=0;
				echo"<tr><td><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotal[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";;
				break;
				case 2:
				if(isset($wtotal[1]) and $wtotal[1] != 0)
            		$wpor=$wtotal[2]/$wtotal[1] * 100;
            	else
            		$wpor=0;
            	echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotal[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
            	if(isset($wtotal[1]))
            		$wtotal[9] = $wtotal[1] - $wtotal[2];
            	else
            		$wtotal[9] = 0 - $wtotal[2];
            	if(isset($wtotal[1]) and $wtotal[1] != 0)
            		$wpor=$wtotal[9]/$wtotal[1] * 100;
            	else
            		$wpor=0;
            	echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
            	echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotal[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
				break;
				case 3:
				break;
				case 4:
				if(isset($wtotal[1]) and $wtotal[1] != 0)
            		$wpor=$wtotal[4]/$wtotal[1] * 100;
            	else
            		$wpor=0;
            	echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
				case 5:
				if(isset($wtotal[1]) and $wtotal[1] != 0)
            		$wpor=$wtotal[5]/$wtotal[1] * 100;
            	else
            		$wpor=0;
            	echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
				case 6:
				if(isset($wtotal[1]) and $wtotal[1] != 0)
            		$wpor=$wtotal[6]/$wtotal[1] * 100;
            	else
            		$wpor=0;
            	echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotal[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
			}
    		echo "<tr><td colspan=3 align=center>--------------------------------------------------</td></tr>";
    		for ($i=0;$i<13;$i++)
    			if(!isset($wtotal[$i+1]))
    				$wtotal[$i+1]=0;
    		if(isset($wtotal[7]))
    		{
    			if(isset($wtotal[1]) and $wtotal[1] != 0)
                    $wpor=$wtotal[7]/$wtotal[1] * 100;
                else
                    $wpor=0;
                echo"<tr><td><b>CORRECCION MONETARIA</b></td><td align=right><b>".number_format((double)$wtotal[7],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }
             if(isset($wtotal[4]) and isset($wtotal[5]) and isset($wtotal[6]) and isset($wtotal[7]) and isset($wtotal[9]))
    		{
	    		$wtotal[10] = $wtotal[9] + $wtotal[4] - $wtotal[5] - $wtotal[6] + $wtotal[7];
    			if(isset($wtotal[1]) and $wtotal[1] != 0)
                    $wpor=$wtotal[10]/$wtotal[1] * 100;
                else
                    $wpor=0;
                echo"<tr><td><b>UTILIDAD ANTES IMPUESTOS TOTAL</b></td><td align=right><b>".number_format((double)$wtotal[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }
             if(isset($wtotal[12]))
    		{
    			if(isset($wtotal[1]) and $wtotal[1] != 0)
                    $wpor=$wtotal[12]/$wtotal[1] * 100;
                else
                    $wpor=0;
                echo"<tr><td><b>PROVISION IMPUESTO DE RENTA</b></td><td align=right><b>".number_format((double)$wtotal[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }  
             else
             	$wtotal[12]=0;
             if(isset($wtotal[10]))
    		{
	    		$wtotal[13]=$wtotal[10]-$wtotal[12];
    			if(isset($wtotal[1]) and $wtotal[1] != 0)
                    $wpor=$wtotal[13]/$wtotal[1] * 100;
                else
                    $wpor=0;
                echo"<tr><td><b>UTILIDAD NETA DESPUES DE IMPUESTOS</b></td><td align=right><b>".number_format((double)$wtotal[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }   
             if(!isset($wtotal[8]))
             	$wtotal[8]=0;
             if(isset($wtotal[8]))
    		{
    			if(isset($wtotal[1]) and $wtotal[1] != 0)
                    $wpor=$wtotal[8]/$wtotal[1] * 100;
                else
                    $wpor=0;
                echo"<tr><td><b>PARTICIPACION TERCEROS EN UNIDAD</b></td><td align=right><b>".number_format((double)$wtotal[8],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }
             else
             	$wtotal[8]=0;  
			 if(isset($wtotal[8]) and isset($wtotal[10]))
    		{
	    		$wtotal[11] = $wtotal[13] - $wtotal[8];
    			if(isset($wtotal[1]) and $wtotal[1] != 0)
                    $wpor=$wtotal[11]/$wtotal[1] * 100;
                else
                    $wpor=0;
                echo"<tr><td><b>UTILIDAD DESPUES DE  IMPUESTOS NETO PROMOTORA</b></td><td align=right><b>".number_format((double)$wtotal[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }
              if(isset($wtotal[90]))
    		{
    			if(isset($wtotal[1]) and $wtotal[1] != 0)
                    $wpor=$wtotal[90]/$wtotal[1] * 100;
                else
                    $wpor=0;
                echo"<tr><td bgcolor=#999999><b>INGRESOS RECIBIDOS PARA TERCEROS</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wtotal[90],0,'.',',')."</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
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
