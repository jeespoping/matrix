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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Protocolo de Un  Conjunto Para Un A&ntilde;o Mes</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc92.php Ver. 2016-03-18</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc92.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or (strtoupper ($wtco) != "P" and strtoupper ($wtco) != "M") or !isset($wcco1)  or !isset($wpro) or !isset($wgru) or !isset($wcon) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROTOCOLO DE UN  CONJUNTO VALORADO PARA UN A&Ntilde;O MES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			if(isset($wanop))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' value=".$wanop." size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			if(isset($wper1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' value=".$wper1." size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad  de Proceso</td>";
			if(isset($wcco1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' value=".$wcco1." size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Protocolo a Analizar</td>";
			if(isset($wpro))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro' value=".$wpro." size=10 maxlength=10></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro' size=10 maxlength=10></td></tr>";
			if(isset($wcco1) and isset($wpro))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Grupo</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Mprgru from ".$empresa."_000095 ";
			    $query = $query." where mprcco = '".$wcco1."'";
			    $query = $query."   and mprpro = '".$wpro."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wgru'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wgru) and $wgru == $row[0])
							echo "<option selected>".$row[0]."</option>";
						else
							echo "<option>".$row[0]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			if(isset($wcco1) and isset($wpro))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Concepto</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Mprcon from ".$empresa."_000095 ";
			    $query = $query." where mprcco = '".$wcco1."'";
			    $query = $query."   and mprpro = '".$wpro."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wcon'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wcon) and $wcon == $row[0])
							echo "<option selected>".$row[0]."</option>";
						else
							echo "<option>".$row[0]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Valorado ? (S/N)</td>";
			if(isset($wserv))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' value=".$wserv." size=1 maxlength=1></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Costo ? (P (Promedio)/M (Mes))</td>";
			if(isset($wtco))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtco' value=".$wtco." size=1 maxlength=1></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtco' size=1 maxlength=1></td></tr>";
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
			$wpro=strtoupper ($wpro);
			$wserv=strtoupper ($wserv);
			$query = "SELECT mprnom,mprpro,mprnot,mprpor from ".$empresa."_000095 ";
			$query = $query." where Mpremp = '".$wemp."' ";
		    $query = $query."   and mprcco = '".$wcco1."'";
		    $query = $query."   and mprpro = '".$wpro."'";
		    $query = $query."   and Mprgru = '".$wgru."'";
		    $query = $query."   and Mprcon = '".$wcon."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wtotg=0;
				$row = mysql_fetch_array($err);
				echo "<table border=1>";
				echo "<tr><td colspan=6 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=6 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=6 align=center>PROTOCOLO DE UN  CONJUNTO PARA UN A&Ntilde;O MES</td></tr>";
				echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=6 align=center>PERIODO    : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=6 align=center>UNIDAD     : ".$wcco1."</td></tr>";
				echo "<tr><td colspan=6 align=center>GRUPO      : ".$wgru."</td></tr>";
				echo "<tr><td colspan=6 align=center>CONCEPTO  : ".$wcon."</td></tr>";
				echo "<tr><td colspan=6 align=center>PROTOCOLO  : ".$row[1]. " - ".$row[0]."</td></tr>";
				$query = "SELECT Pqucod,subdes,Pqucan,cxapro,Pquccp,Pqugrp,Pqucoa from ".$empresa."_000099,".$empresa."_000083,".$empresa."_000104 ";
				$query = $query." where Pquemp = '".$wemp."' ";
			    $query = $query."   and Pqucco = '".$wcco1."'";
			    $query = $query."   and Pqupro = '".$wpro."'";
			    $query = $query."   and Pqugru = '".$wgru."'";
			    $query = $query."   and Pqucon = '".$wcon."'";
			    $query = $query."   and Pqutip = '1' ";
			    $query = $query."   and Pquemp = cxaemp ";
			    $query = $query."   and Pqucod = cxasub ";
			    $query = $query."   and Pquccp = cxacco ";
			    $query = $query."   and cxaano = ".$wanop;
			    $query = $query."   and cxames = ".$wper1;
			    $query = $query."   and subcod = cxasub ";					
	    		$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$wsub=0;
				if($num1 > 0)
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td  bgcolor=#cccccc ><b>DESCRIPCION</b></td><td  bgcolor=#cccccc ><b>CC</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td  bgcolor=#cccccc ><b>DESCRIPCION</b></td><td  bgcolor=#cccccc ><b>CC</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$row1[2] * $row1[3];
						$wsub+=$wtot;
						$path="/matrix/presupuestos/reportes/000001_rc89.php?wanop=".$wanop."&wper1=".$wper1."&wcco1=".$wcco1."&wpro=".$wpro."&wgru=".$row1[5]."&wcon=".$row1[6]."&wserv=S&empresa=".$empresa;
						if($wserv == "S")
							echo "<tr><td>".$row1[0]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td>".$row1[4]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td><td align=right>".number_format((double)$row1[3],0,'.',',')."</td><td align=right>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$row1[0]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td>".$row1[4]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS X ACTIVIDAD</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				if($wtco == "P")
					$query = "SELECT inscod,insdes,insuni,Pqucan,minpro,Pqugrp,Pqucoa from ".$empresa."_000099,".$empresa."_000093,".$empresa."_000089 ";
				else
					$query = "SELECT inscod,insdes,insuni,Pqucan,mincpr,Pqugrp,Pqucoa from ".$empresa."_000099,".$empresa."_000093,".$empresa."_000089 ";
				$query = $query." where Pquemp = '".$wemp."' ";
			    $query = $query."   and Pqucco = '".$wcco1."'";
			    $query = $query."   and Pqupro = '".$wpro."'";
			    $query = $query."   and Pqugru = '".$wgru."'";
			    $query = $query."   and Pqucon = '".$wcon."'";
			    $query = $query."   and Pqutip = '2' ";
			    $query = $query."   and Pquemp = minemp ";
			    $query = $query."   and Pqucod = mincod ";
			    $query = $query."   and minano = ".$wanop;
			    $query = $query."   and minmes = ".$wper1;
			    $query = $query."   and minemp = insemp ";
			    $query = $query."   and mincod = inscod ";	
			    $query = $query." UNION ALL ";
				$query = $query." SELECT inscod,insdes,insuni,Pqucan,0,Pqugrp,Pqucoa from ".$empresa."_000099,".$empresa."_000089 ";
				$query = $query." where Pquemp = '".$wemp."' ";
				$query = $query."   and Pqucco = '".$wcco1."'";
			    $query = $query."   and Pqupro = '".$wpro."'";
			    $query = $query."   and Pqugru = '".$wgru."'";
			    $query = $query."   and Pqucon = '".$wcon."'";
			    $query = $query."   and Pqutip = '2' ";
				$query = $query."   and Pqucod  NOT IN (select mincod from ".$empresa."_000093 where minano = ".$wanop." and minmes = ".$wper1."  and minemp = '".$wemp."' group by 1)";
				$query = $query."   and Pquemp = insemp ";
				$query = $query."   and Pqucod = inscod ";			
	    		$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$wsub=0;
				if($num1 > 0)
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$row1[3] * $row1[4];
						$wsub+=$wtot;
						$path="/matrix/presupuestos/reportes/000001_rc89.php?wanop=".$wanop."&wper1=".$wper1."&wcco1=".$wcco1."&wpro=".$wpro."&wgru=".$row1[5]."&wcon=".$row1[6]."&wserv=S&empresa=".$empresa;
						if($wserv == "S")
							echo "<tr><td>".$row1[0]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td>".$row1[2]."</td><td align=right>".number_format((double)$row1[3],4,'.',',')."</td><td align=right>".number_format((double)$row1[4],0,'.',',')."</td><td align=right>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$row1[0]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td>".$row1[2]."</td><td align=right>".number_format((double)$row1[3],4,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS INSUMOS</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				$query = "SELECT Pqucod,fijdes,Pqucan,fijmon,Pqugrp,Pqucoa from ".$empresa."_000099,".$empresa."_000086 ";
				$query = $query." where Pquemp = '".$wemp."' ";
			    $query = $query."   and Pqucco = '".$wcco1."'";
			    $query = $query."   and Pqupro = '".$wpro."'";
			    $query = $query."   and Pqugru = '".$wgru."'";
			    $query = $query."   and Pqucon = '".$wcon."'";
			    $query = $query."   and Pqutip = '4' ";
			    $query = $query."   and Pquemp = fijemp ";
			    $query = $query."   and Pquccp = fijcco ";
			    $query = $query."   and Pqucod = fijcod ";	
			    $query = $query."   and Fijano = ".$wanop;		
	    		$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$wsub=0;
				if($num1 > 0)
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td colspan=2 bgcolor=#cccccc ><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td colspan=2 bgcolor=#cccccc ><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$row1[2] * $row1[3];
						$wsub+=$wtot;
						$path="/matrix/presupuestos/reportes/000001_rc89.php?wanop=".$wanop."&wper1=".$wper1."&wcco1=".$wcco1."&wpro=".$wpro."&wgru=".$row1[4]."&wcon=".$row1[5]."&wserv=S&empresa=".$empresa;
						if($wserv == "S")
							echo "<tr><td>".$row1[0]."</td><td colspan=2 onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td><td align=right>".number_format((double)$row1[3],0,'.',',')."</td><td align=right>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$row1[0]."</td><td colspan=2 onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS PROCEDIMIENTOS DE TERCEROS</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				if($wtco == "P")
					$query = "SELECT pcacod,mprnom,Pqucan,pcapro,Pqugrp,Pqucoa from ".$empresa."_000099,".$empresa."_000097,".$empresa."_000095 ";
				else
					$query = "SELECT pcacod,mprnom,Pqucan,pcactp,Pqugrp,Pqucoa from ".$empresa."_000099,".$empresa."_000097,".$empresa."_000095 ";
				$query = $query." where Pquemp = '".$wemp."' ";
				$query = $query."   and Pqucco = '".$wcco1."'";
				$query = $query."   and Pqupro = '".$wpro."'";
				$query = $query."   and Pqugru = '".$wgru."'";
				$query = $query."   and Pqucon = '".$wcon."'";
				$query = $query."   and Pqutip = '3' ";
				$query = $query."   and Pquemp = Pcaemp ";
				$query = $query."   and Pquccp = Pcacco ";
				$query = $query."   and Pqucod = Pcacod ";
				$query = $query."   and Pqugrp = Pcagru";
				$query = $query."   and Pqucoa = Pcacon";
				$query = $query."   and pcaano = ".$wanop;
			    $query = $query."   and pcames = ".$wper1;
			    $query = $query."   and Pquemp = mpremp ";
				$query = $query."   and Pquccp = mprcco ";
				$query = $query."   and Pqucod = mprpro ";
				$query = $query."   and Pqugrp = Mprgru";
				$query = $query."   and Pqucoa = Mprcon";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$wsub=0;
				if($num1 > 0)
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td colspan=2 bgcolor=#cccccc ><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td colspan=2 bgcolor=#cccccc ><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$row1[2] * $row1[3];
						$wsub+=$wtot;
						$path="/matrix/presupuestos/reportes/000001_rc89.php?wanop=".$wanop."&wper1=".$wper1."&wcco1=".$wcco1."&wpro=".$row1[0]."&wgru=".$row1[4]."&wcon=".$row1[5]."&wserv=S&empresa=".$empresa;
						if($wserv == "S")
							echo "<tr><td>".$row1[0]."</td><td colspan=2 onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td><td align=right>".number_format((double)$row1[3],0,'.',',')."</td><td align=right>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$row1[0]."</td><td colspan=2 onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row1[1]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS PROTOCOLOS</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				if($wserv == "S")
				{
					echo "<tr><td colspan=5><b>COSTO TOTAL PROPIO</b></td><td align=right><b>".number_format((double)$wtotg,0,'.',',')."</b></td></tr>";
					$wpor=$row[3] * 100;
					echo "<tr><td colspan=5><b>PORCENTAJE TERCERO</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."%</b></td></tr>";
					$wtotg=$wtotg/(1 - $row[3]);
					echo "<tr><td colspan=5 bgcolor=#FFCC66 ><b>TARIFA MINIMA DE NEGOCIACION</b></td><td align=right bgcolor=#FFCC66 ><b>".number_format((double)$wtotg,0,'.',',')."</b></td></tr>";
				}
				echo "<tr><td colspan=6 bgcolor=#99CCFF ><b>NOTA : </b></td></tr>";
				echo "<tr><td colspan=6><b>".$row[2]."</b></td></tr></TABLE>";
			}
		}
	}
?>
</body>
</html>
