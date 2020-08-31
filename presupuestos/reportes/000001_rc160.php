<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
    	.tipotot{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;}
    	.tipotit{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;}
    	.tipouti{color:#000066;background:#81F781;font-size:12pt;font-family:Arial;font-weight:bold;}
    	.tiposub{color:#000066;background:#E8EEF7;font-size:12pt;font-family:Arial;font-weight:bold;}
    </style>
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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Estado de Resultados Consolidado Comparativo por A&ntilde;os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc160.php Ver. 2018-03-01</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc160.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wccoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wccof) or !isset($wserv) or !isset($wcif) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or !isset($wtip)  or (strtoupper ($wtip) != "A" and strtoupper ($wtip) != "C") or !isset($wgru) or !isset($wanoi) or !isset($wperi1)  or !isset($wperi2) or $wperi1 < 1 or $wperi1 > 12 or $wperi2 < 1 or $wperi2 > 12 or $wperi1 > $wperi2 or !isset($wanof) or !isset($wperf1)  or !isset($wperf2) or $wperf1 < 1 or $wperf1 > 12 or $wperf2 < 1 or $wperf2 > 12 or $wperf1 > $wperf2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ESTADO DE RESULTADOS CONSOLIDADO COMPARATIVO POR A&Ntilde;OS</td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS INICIALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o</td>";
			if(isset($wanoi))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' value='".$wanoi."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			if(isset($wperi1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi1' value='".$wperi1."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			if(isset($wperi2))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi2' value='".$wperi2."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS FINALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o</td>";
			if(isset($wanof))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' value='".$wanof."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			if(isset($wperf1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf1' value='".$wperf1."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			if(isset($wperf2))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf2' value='".$wperf2."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS</td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
				if(isset($wccoi))
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' value='".$wccoi."' size=4 maxlength=4></td></tr>";
				else
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
				if(isset($wccof))
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' value='".$wccof."' size=4 maxlength=4></td></tr>";
				else
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
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
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccoi'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				$wccof="NO";
				echo "<center><input type='HIDDEN' name= 'wccof' value='".$wccof."'>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Grupo de Unidades</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT ccouni  from ".$empresa."_000005 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgru'>";
				echo "<option>Todos</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centros de Servicio ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Administrativo o Contable ? (A/C)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Cifras Expresadas En ?</td>";
			echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=wcif value=1>Pesos<input type='RADIO' name=wcif value=1000>Miles<input type='RADIO' name=wcif value=1000000>Millones</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			if(!isset($wccof) or $wccof == "NO")
			{
				$wccoi = ver($wccoi);
				$wccof = $wccoi;
			}
			$wres=strtoupper ($wres);
			$wtip=strtoupper ($wtip);
			$wserv=strtoupper ($wserv);
			if($wtip == "C")
			{
				//                  0     1           2           3
				$query = "SELECT rvpcpr,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
				$query = $query."  where rvpano = ".$wanoi;
				$query = $query."    and rvpemp = '".$wemp."' ";
				$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
				$query = $query."    and rvpcco = Ccocod";
				$query = $query."    and ccoemp = '".$wemp."' ";
				if($wccoi == $wccof)
					$query = $query."    and ((ccoclas = 'PR' and rvpcpr != '290') or ccoclas != 'PR') ";
				if($wgru != "Todos")             
					$query = $query."    and Ccouni = '".$wgru."' ";
				$query = $query."    and rvpper between ".$wperi1." and ".$wperi2;
				$query = $query."    and rvpcpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by rvpcpr,mganom";
				$query = $query."   order by rvpcpr";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$query = "SELECT rvpcpr,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
				$query = $query."  where rvpano = ".$wanof;
				$query = $query."    and rvpemp = '".$wemp."' ";
				$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
				$query = $query."    and rvpcco = Ccocod";
				$query = $query."    and ccoemp = '".$wemp."' ";
				if($wccoi == $wccof)
					$query = $query."    and ((ccoclas = 'PR' and rvpcpr != '290') or ccoclas != 'PR') ";
				if($wgru != "Todos")
					$query = $query."    and Ccouni = '".$wgru."' ";
				$query = $query."    and rvpper between ".$wperf1." and ".$wperf2;
				$query = $query."    and rvpcpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by rvpcpr,mganom";
				$query = $query."   order by rvpcpr";
			}
			else
			{
				//                  0     1           2           3
				$query = "SELECT mgacoa,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
				$query = $query."  where rvpano = ".$wanoi;
				$query = $query."    and rvpemp = '".$wemp."' ";
				$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
				$query = $query."    and rvpcco = Ccocod";
				$query = $query."    and ccoemp = '".$wemp."' ";
				if($wccoi == $wccof)
					$query = $query."    and ((ccoclas = 'PR' and mgacoa != '290') or ccoclas != 'PR') ";
				if($wgru != "Todos")             
					$query = $query."    and Ccouni = '".$wgru."' ";
				$query = $query."    and rvpper between ".$wperi1." and ".$wperi2;
				$query = $query."    and rvpcpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by mgacoa,mganom";
				$query = $query."   order by mgacoa";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$query = "SELECT mgacoa,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
				$query = $query."  where rvpano = ".$wanof;
				$query = $query."    and rvpemp = '".$wemp."' ";
				$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
				$query = $query."    and rvpcco = Ccocod";
				$query = $query."    and ccoemp = '".$wemp."' ";
				if($wccoi == $wccof)
					$query = $query."    and ((ccoclas = 'PR' and mgacoa != '290') or ccoclas != 'PR') ";
				if($wgru != "Todos")
					$query = $query."    and Ccouni = '".$wgru."' ";
				$query = $query."    and rvpper between ".$wperf1." and ".$wperf2;
				$query = $query."    and rvpcpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by mgacoa,mganom";
				$query = $query."   order by mgacoa";
			}
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			switch ($wcif)
			{
				case 1:
					$wcift = "PESOS";
				break;
				case 1000:
					$wcift = "MILES DE PESOS";
				break;
				case 1000000:
					$wcift = "MILLONES DE PESOS";
				break;
			}
			echo "<table border=1 class=tipoTABLEGRID>";
			echo "<tr><td colspan=11 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=11 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=11 align=center>ESTADO DE RESULTADOS CONSOLIDADO COMPARATIVO POR A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=11 align=center>Datos Iniciales</td></tr>";
			echo "<tr><td colspan=11 align=center>A&Ntilde;O : ".$wanoi." MES INICIAL : ".$wperi1. " MES FINAL : ".$wperi2."</td></tr>";
			echo "<tr><td colspan=11 align=center>Datos Finales</td></tr>";
			echo "<tr><td colspan=11 align=center>A&Ntilde;O : ".$wanof." MES INICIAL : ".$wperf1. " MES FINAL : ".$wperf2."</td></tr>";
			echo "<tr><td colspan=11 align=center>UNIDAD INICIAL : ".$wccoi. " UNIDAD FINAL : ".$wccof."</td></tr>";
			echo "<tr><td colspan=11 align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
			echo "<tr><td colspan=11 align=center>CIFRAS EXPRESADAS EN  : ".$wcift."</td></tr>";
			echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr class='tipotit'><td><b>CODIGO</b></td><td><b>RUBRO</b></td><td><b>REAL : ".$wanoi."/".$wperi1."-".$wperi2."</b></td><td><b>%PART</b></td><td><b>REAL : ".$wanof."/".$wperf1."-".$wperf2."</b></td><td><b>%PART</b></td><td><b>%VAR</b></td><td><b>PPTO : ".$wanof."/".$wperf1."-".$wperf2."</b></td><td><b>%PART</b></td><td align=right><b>DIFERENCIA (R-P)</b></td><td align=right><b>EJECUCION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			$ingresos1 = 0;
			$ingresos2 = 0;
			if ($num1 ==  0)
			{
				$k1++;
				$row1[0]='zzz';
				$row1[1]="";
				$row1[2]=0;
				$row1[3]=0;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2++;
				$row2[0]='zzz';
				$row2[1]="";
				$row2[2]=0;
				$row2[3]=0;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				#echo $k1."-".$k2."-".$num1."-".$num2."<br>";
				if($row1[0] == $row2[0])
				{
					$row1[2] = $row1[2] / $wcif;
					$row1[3] = $row1[3] / $wcif;
					$row2[2] = $row2[2] / $wcif;
					$row2[3] = $row2[3] / $wcif;
					if ($row1[0] >= "100" and $row1[0] <= "129")
					{
						$ingresos1 += $row1[2];
						$ingresos2 += $row2[2];
						$ingresos3 += $row2[3];
					}
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[2]-$row2[3];
					if($row2[3] != 0)
						$wdata[$num][6]=($row2[2]/$row2[3])*100;
					else
						$wdata[$num][6]=0;
					$wdata[$num][7]=0;
					$wdata[$num][8]=0;
					$wdata[$num][9]=0;
					if($wdata[$num][2] != 0)
						$wdata[$num][10]=(($wdata[$num][3] / $wdata[$num][2])-1)*100;
					else
						$wdata[$num][10]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="zzz";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="zzz";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0] < $row2[0])
				{
					$row1[2] = $row1[2] / $wcif;
					$row1[3] = $row1[3] / $wcif;
					if ($row1[0] >= "100" and $row1[0] <= "129")
					{
						$ingresos1 += $row1[2];
						$ingresos2 += 0;
						$ingresos3 += 0;
					}
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0;
					$wdata[$num][5]=0;
					$wdata[$num][6]=0;
					$wdata[$num][7]=0;
					$wdata[$num][8]=0;
					$wdata[$num][9]=0;
					if($wdata[$num][2] != 0)
						$wdata[$num][10]=(($wdata[$num][3] / $wdata[$num][2])-1)*100;
					else
						$wdata[$num][10]=0;
					$k1++;
					if($k1 > $num1)
						$row1[0]="zzz";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					$row2[2] = $row2[2] / $wcif;
					$row2[3] = $row2[3] / $wcif;
					if ($row1[0] >= "100" and $row1[0] <= "129")
					{
						$ingresos1 += 0;
						$ingresos2 += $row2[2];
						$ingresos3 += $row2[3];
					}
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[2]-$row2[3];
					if($row2[3] != 0)
						$wdata[$num][6]=($row2[2]/$row2[3])*100;
					else
						$wdata[$num][6]=0;
					$wdata[$num][7]=0;
					$wdata[$num][8]=0;
					$wdata[$num][9]=0;
					if($wdata[$num][2] != 0)
						$wdata[$num][10]=(($wdata[$num][3] / $wdata[$num][2])-1)*100;
					else
						$wdata[$num][10]=0;
					$k2++;
					if($k2 > $num2)
						$row2[0]="999";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			if($ingresos1 == 0 and $ingresos2 == 0 and $ingresos3 == 0)
			{
				for ($i=0;$i<=$num;$i++)
				{
					if (substr($wdata[$i][0],0,1) == "2")
					{
						$ingresos1+=$wdata[$i][2];
						$ingresos2+=$wdata[$i][3];
						$ingresos3+=$wdata[$i][4];
					}
				}
			}
			$wtotal=array();
			$ita=0;
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESOS DE OPERACIONES ORDINARIAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "100" and $wdata[$i][0] <= "129")
					$it=(integer)substr($wdata[$i][0],0,1);
				else 
					$it=0;
				if($it == 1)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal[100][0] += $wdata[$i][2];
						$wtotal[100][1] += $wdata[$i][3];
						$wtotal[100][2] += $wdata[$i][4];
					}
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 1;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESOS DE OPERACIONES ORDINARIAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			if($wtip == "C")
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>COSTOS DE OPERACION</B></td></tr>";
			else
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>COSTOS Y GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 2)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 2;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			if($wtip == "C")
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL COSTOS DE OPERACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			else
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL COSTOS Y GASTOS DE ADMINISTARCION Y VENTAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			if($wtip == "C")
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS DE ADMINISTRACION</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 3)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 3;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			if($wtip == "C")
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS DE ADMINISTRACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			if($wtip == "C")
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS DE VENTAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 8)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 8;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			if($wtip == "C")
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS DE VENTAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";

			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>OTROS INGRESOS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "130" and $wdata[$i][0] <= "199")
					$it=99;
				else 
					$it=0;
				if($it == 99)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal[100][0] += $wdata[$i][2];
						$wtotal[100][1] += $wdata[$i][3];
						$wtotal[100][2] += $wdata[$i][4];
					}
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 99;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL OTROS INGRESOS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>OTROS GASTOS DE OPERACION</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 5)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 5;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL OTROS GASTOS DE OPERACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			$wtotal[10][0] = $wtotal[1][0] - $wtotal[2][0] - $wtotal[3][0] - $wtotal[8][0] - $wtotal[5][0] + $wtotal[99][0];
			$wtotal[10][1] = $wtotal[1][1] - $wtotal[2][1] - $wtotal[3][1] - $wtotal[8][1] - $wtotal[5][1] + $wtotal[99][1];
			$wtotal[10][2] = $wtotal[1][2] - $wtotal[2][2] - $wtotal[3][2] - $wtotal[8][2] - $wtotal[5][2] + $wtotal[99][2];
			
			$dif=$wtotal[10][1]-$wtotal[10][2];
			$cum=0;
			if($wtotal[10][2] != 0)
				$cum=($wtotal[10][1] / $wtotal[10][2])*100;
			$var=0;
			if($wtotal[10][0] != 0)
				$var=(($wtotal[10][1]/$wtotal[10][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[10][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[10][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[10][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipouti'><td colspan=2><b>RESULTADOS DE ACTIVIDADES DE LA OPERACION</b></td><td align=right>".number_format((double)$wtotal[10][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[10][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[10][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESO FINANCIERO</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 4)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 4;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESO FINANCIERO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTO FINANCIERO</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 6)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 6;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTO FINANCIERO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[11][0] = $wtotal[4][0] - $wtotal[6][0];
			$wtotal[11][1] = $wtotal[4][1] - $wtotal[6][1];
			$wtotal[11][2] = $wtotal[4][2] - $wtotal[6][2];
			
			$dif=$wtotal[11][1]-$wtotal[11][2];
			$cum=0;
			if($wtotal[11][2] != 0)
				$cum=($wtotal[11][1] / $wtotal[11][2])*100;
			$var=0;
			if($wtotal[11][0] != 0)
				$var=(($wtotal[11][1]/$wtotal[11][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[11][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[11][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[11][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tiposub'><td colspan=2><b>COSTO FINANCIERO NETO</b></td><td align=right>".number_format((double)$wtotal[11][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[11][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[11][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[12][0] = $wtotal[10][0] + $wtotal[11][0];
			$wtotal[12][1] = $wtotal[10][1] + $wtotal[11][1];
			$wtotal[12][2] = $wtotal[10][2] + $wtotal[11][2];
			
			$dif=$wtotal[12][1]-$wtotal[12][2];
			$cum=0;
			if($wtotal[12][2] != 0)
				$cum=($wtotal[12][1] / $wtotal[12][2])*100;
			$var=0;
			if($wtotal[12][0] != 0)
				$var=(($wtotal[12][1]/$wtotal[12][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[12][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[12][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[12][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipouti'><td colspan=2><b>GANANCIAS ANTES DE IMPUESTOS</b></td><td align=right>".number_format((double)$wtotal[12][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[12][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[12][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 760)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
				}
			}
			$it = 760;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>PROVISION IMPUESTO DE RENTA Y CREE</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 770)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
				}
			}
			$it = 770;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>IMPUESTO RENTA DIFERIDO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[13][0] = $wtotal[12][0] - $wtotal[760][0] - $wtotal[770][0];
			$wtotal[13][1] = $wtotal[12][1] - $wtotal[760][1] - $wtotal[770][1];
			$wtotal[13][2] = $wtotal[12][2] - $wtotal[760][2] - $wtotal[770][2];
			
			$dif=$wtotal[13][1]-$wtotal[13][2];
			$cum=0;
			if($wtotal[13][2] != 0)
				$cum=($wtotal[13][1] / $wtotal[13][2])*100;
			$var=0;
			if($wtotal[13][0] != 0)
				$var=(($wtotal[13][1]/$wtotal[13][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[13][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[13][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[13][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipouti'><td colspan=2><font size=2.5><b>RESULTADOS PROCEDENTES DE OPERACIONES CONTINUADAS</b></font></td><td align=right>".number_format((double)$wtotal[13][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[13][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[13][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 900)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
				}
			}
			$it = 900;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>INGRESOS PARA TERCEROS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[14][0] = $wtotal[100][0] + $wtotal[900][0];
			$wtotal[14][1] = $wtotal[100][1] + $wtotal[900][1];
			$wtotal[14][2] = $wtotal[100][2] + $wtotal[900][2];
			
			$dif=$wtotal[14][1]-$wtotal[14][2];
			$cum=0;
			if($wtotal[14][2] != 0)
				$cum=($wtotal[14][1] / $wtotal[14][2])*100;
			$var=0;
			if($wtotal[14][0] != 0)
				$var=(($wtotal[14][1]/$wtotal[14][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[14][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[14][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[14][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotit'><td colspan=2><b>FACTURACION TOTAL</b></td><td align=right>".number_format((double)$wtotal[14][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[14][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[14][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 750)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
				}
			}
			$it = 750;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotit'><td colspan=2><b>PARTICIPACION TERCEROS EN UNIDAD</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
            echo "</tabla>";
		}
	}
?>
</body>
</html>
