<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Estado de Resultados Presupuestado Mensual NIIF</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc179.php Ver. 2017-07-03</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc179.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or  ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or !isset($wtip) or (strtoupper ($wtip) != "C" and strtoupper ($wtip) != "A") or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 )
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ESTADO DE RESULTADOS PRESUPUESTADO MENSUAL NIIF</td></tr>";
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
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
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
			echo "<tr><td bgcolor=#cccccc align=center>Administrativo o Contable ? (A/C)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centros de Servicio ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			$wres=strtoupper ($wres);
			$wserv=strtoupper ($wserv);
			$wtip=strtoupper ($wtip);
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
			$wanopa=$wanop-1;
			if($wtip == "C")
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
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
			}
			else
			{
				$query = "SELECT mgacoa,resper,mganom,sum(resmon) as wmonto from ".$empresa."_000043,".$empresa."_000028 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resemp = '".$wemp."' ";
				$query = $query."    and rescco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and resper between ".$wper1." and ".$wper2;
				$query = $query."    and rescpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by mgacoa,resper,mganom";
				$query = $query."   order by mgacoa,resper";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
			}
			$ncol = $wper2 - $wper1 + 4;
			echo "<table border=1>";
			echo "<tr><td colspan=".$ncol." align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>ESTADO DE RESULTADOS PRESUPUESTADO MENSUAL<b>NIIF</b></td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>OPERACIONES CONTINUADAS</td></tr>";
			echo "<tr><td><b>CODIGO</b></td><td><b>RUBRO</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td align=right><b>".$wmese[$i]."</b></td>";
			echo "<td align=right><b>TOTAL</b></td>";
			echo "</tr>";
			$wdata=array();
			$kcpr="0";
			$num=-1;
			for ($i=0;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				if($row1[0] != $kcpr)
				{
					$num++;
					$kcpr = $row1[0];
					$wdata[$num][0] = $row1[0];
					$wdata[$num][13] = $row1[2];
					for ($j=$wper1;$j<=$wper2;$j++)
							$wdata[$num][$j] = 0;
				}
				$wdata[$num][$row1[1]] = $row1[3];
			}
			echo "<tr><td colspan=".$ncol."><b>INGRESOS DE OPERACIONES ORDINARIAS</B></td></tr>";
			$wtotal = array();
			$it = 1;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "100" and $wdata[$i][0] <= "129")
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if((integer)$wdata[$i][0] == 100)
					{
						for ($j=$wper1;$j<=$wper2;$j++)
							$wtotal[100][$j] += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}

			echo"<tr><td colspan=2><b>TOTAL INGRESOS DE OPERACIONES ORDINARIAS</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[$it][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			if($wtip == "C")
				echo "<tr><td colspan=".$ncol."><b>COSTOS DE OPERACION</B></td></tr>";
			else
				echo "<tr><td colspan=".$ncol."><b>COSTOS  Y GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
			$it = 2;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 2)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}

			if($wtip == "C")
				echo"<tr><td colspan=2><b>TOTAL COSTOS DE OPERACION</b></td>";
			else
				echo"<tr><td colspan=2><b>TOTAL COSTOS Y GASTOS DE ADMINISTRACION Y VENTAS</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[$it][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			if($wtip == "C")
				echo "<tr><td colspan=".$ncol."><b>GASTOS DE ADMINISTRACION</B></td></tr>";
			$it = 3;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 3)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			if($wtip == "C")
			{
				echo"<tr><td colspan=2><b>TOTAL GASTOS DE ADMINISTRACION</b></td>";
					$wtotcol = 0;
				for ($j=$wper1;$j<=$wper2;$j++)
				{
					echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
					$wtotcol += $wtotal[$it][$j];
				}
				echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
				echo "</tr>";
				echo "<tr><td colspan=".$ncol."><b>GASTOS DE VENTAS</B></td></tr>";
			}
			
			$it = 8;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 8)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}

			if($wtip == "C")
			{
				echo"<tr><td colspan=2><b>TOTAL GASTOS DE VENTAS</b></td>";
				$wtotcol = 0;
				for ($j=$wper1;$j<=$wper2;$j++)
				{
					echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
					$wtotcol += $wtotal[$it][$j];
				}
				echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
				echo "</tr>";
			}
			
			echo "<tr><td colspan=".$ncol."><b>OTROS INGRESOS</B></td></tr>";
			$it = 99;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "130" and $wdata[$i][0] <= "199")
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			echo"<tr><td colspan=2><b>TOTAL OTROS INGRESOS</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[$it][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			
			echo "<tr><td colspan=".$ncol."><b>OTROS GASTOS DE OPERACION</B></td></tr>";
			$it = 5;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 5)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			echo"<tr><td colspan=2><b>TOTAL OTROS GASTOS DE OPERACION</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[$it][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			
			//*** TOTAL 10 ***
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[10][$j] = $wtotal[1][$j] - $wtotal[2][$j] - $wtotal[3][$j] - $wtotal[8][$j] - $wtotal[5][$j] + $wtotal[99][$j];
			echo"<tr><td colspan=2><b>RESULTADOS DE ACTIVIDADES DE LA OPERACION</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[10][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[10][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			
			echo "<tr><td colspan=".$ncol."><b>INGRESO FINANCIERO</B></td></tr>";
			$it = 4;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 4)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			echo"<tr><td colspan=2><b>TOTAL INGRESO FINANCIERO</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[$it][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			echo "<tr><td colspan=".$ncol."><b>GASTO FINANCIERO</B></td></tr>";
			$it = 6;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 6)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			echo"<tr><td colspan=2><b>TOTAL GASTO FINANCIERO</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[$it][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			//*** TOTAL 11 ***
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[11][$j] = $wtotal[4][$j] - $wtotal[6][$j];
			echo"<tr><td colspan=2><b>COSTO FINANCIERO NETO</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[11][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[11][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			//*** TOTAL 12 ***
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[12][$j] = $wtotal[10][$j] + $wtotal[11][$j];
			echo"<tr><td colspan=2><b>GANANCIAS ANTES DE IMPUESTOS</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[12][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[12][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			//*** LINEA 760 ***
			$it = 760;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 760)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			//*** LINEA 770 ***
			$it = 770;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 770)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			
			//*** TOTAL 13 ***
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[13][$j] = $wtotal[12][$j] - $wtotal[760][$j] - $wtotal[770][$j];
			echo"<tr><td colspan=2><b>RESULTADOS PROCEDENTES DE OPERACIONES CONTINUADAS</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				echo "<td align=right><b>".number_format((double)$wtotal[13][$j],0,'.',',')."</b></td>";
				$wtotcol += $wtotal[13][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			//*** LINEA 900 ***
			$it = 900;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 900)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
			
			//*** TOTAL 14 ***
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[14][$j] = $wtotal[100][$j] + $wtotal[900][$j];
			echo"<tr><td colspan=2><b>FACTURACION TOTAL POR PRESTACION DE SERVICIOS</b></td>";
			$wtotcol = 0;
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				//echo "<td align=right><b>".number_format((double)$wtotal[$it][$j],0,'.',',')."</b></td>";
				echo "<td align=right><b>".number_format((double)$wtotal[14][$j],0,'.',',')."</b></td>";
				//$wtotcol += $wtotal[$it][$j];
				$wtotcol += $wtotal[14][$j];
			}
			echo "<td align=right><b>".number_format((double)$wtotcol ,0,'.',',')."</b></td>";
			echo "</tr>";
			
			//*** LINEA 750 ***
			$it = 750;
			for ($j=$wper1;$j<=$wper2;$j++)
				$wtotal[$it][$j]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 750)
				{
					$wtotcol = 0;
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wtotal[$it][$j] += $wdata[$i][$j];
						$wtotcol += $wdata[$i][$j];
					}
					if($wres == "D")
					{
						echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][13]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right>".number_format((double)$wdata[$i][$j],0,'.',',')."</td>";
						echo "<td align=right>".number_format((double)$wtotcol ,0,'.',',')."</td>";
						echo "</tr>";
					}
				}
			}
		}
	}
?>
</body>
</html>
