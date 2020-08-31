<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Costo de un Conjunto x Tipo de Componente</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc101.php Ver. 2016-03-18</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc101.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1) or !isset($wgru) or !isset($wpro) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROTOCOLO DE UN  PROCEDIMIENTO VALORADO PARA UN A&Ntilde;O MES</td></tr>";
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
						echo "<option>".$row[0]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
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
			$query = "SELECT mprnom,mprpro,mprnot,mprpor from ".$empresa."_000095 ";
			$query = $query." where Mpremp = '".$wemp."' ";
		    $query = $query."   and mprcco = '".$wcco1."'";
		    $query = $query."   and mprpro = '".$wpro."'";
		    $query = $query."   and Mprgru = '".$wgru."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wtotg=0;
				$row = mysql_fetch_array($err);
				echo "<CENTER><table border=1>";
				echo "<tr><td colspan=6 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=6 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=6 align=center>COSTO DE UN CONJUNTO X TIPO DE COMPONENTE</td></tr>";
				echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=6 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=6 align=center>UNIDAD  : ".$wcco1."</td></tr>";
				echo "<tr><td colspan=6 align=center>PROTOCOLO  : ".$row[1]. " - ".$row[0]."</td></tr>";
				$query = " CREATE TEMPORARY TABLE if not exists temp1 as ";
				$query = $query." SELECT Pqutin,Mtides,sum(Pqucan * cxapro) as monto from ".$empresa."_000099,".$empresa."_000083,".$empresa."_000096 ";
				$query = $query." where Pquemp = '".$wemp."' ";
			    $query = $query."   and Pqucco = '".$wcco1."'";
			    $query = $query."   and Pqupro = '".$wpro."'";
			    $query = $query."   and Pqugru = '".$wgru."'";
			    $query = $query."   and Pqutip = '1' ";
			    $query = $query."   and Pquemp = cxaemp ";
			    $query = $query."   and Pqucod = cxasub ";
			    $query = $query."   and Pquccp = cxacco ";
			    $query = $query."   and cxaano = ".$wanop;
			    $query = $query."   and cxames = ".$wper1;
			    $query = $query."   and Pqutin = Mticod ";	
			    $query = $query."   GROUP BY  Pqutin,Mtides ";
			    $query = $query."   UNION ALL ";
			    $query = $query." SELECT Pqutin,Mtides,sum(Pqucan *mincpr) as monto from ".$empresa."_000099,".$empresa."_000093,".$empresa."_000096 ";
			    $query = $query." where Pquemp = '".$wemp."' ";
			    $query = $query."   and Pqucco = '".$wcco1."'";
			    $query = $query."   and Pqupro = '".$wpro."'";
			    $query = $query."   and Pqugru = '".$wgru."'";
			    $query = $query."   and Pqutip = '2' ";
			    $query = $query."   and Pqucod = mincod ";
			    $query = $query."   and Pquemp = minemp ";
			    $query = $query."   and minano = ".$wanop;
			    $query = $query."   and minmes = ".$wper1;
			    $query = $query."   and Pqutin = Mticod ";	
			    $query = $query."   GROUP BY  Pqutin,Mtides ";
			    $query = $query."   UNION ALL ";
			    $query = $query." SELECT Pqutin,Mtides,SUM(Pqucan * fijmon) as monto from ".$empresa."_000099,".$empresa."_000086,".$empresa."_000096 ";
			    $query = $query." where Pquemp = '".$wemp."' ";
			    $query = $query."   and Pqucco = '".$wcco1."'";
			    $query = $query."   and Pqupro = '".$wpro."'";
			    $query = $query."   and Pqugru = '".$wgru."'";
			    $query = $query."   and Pqutip = '4' ";
			    $query = $query."   and Pquemp = fijemp ";
			    $query = $query."   and Pquccp = fijcco ";
			    $query = $query."   and Pqucod = fijcod ";
			    $query = $query."   and Pqutin = Mticod ";	
			    $query = $query."   GROUP BY  Pqutin,Mtides ";
			    $query = $query."   UNION ALL ";
			    $query = $query." SELECT Pqutin,Mtides,SUM(Pqucan * pcapro) as monto from ".$empresa."_000099,".$empresa."_000097,".$empresa."_000096 ";
			    $query = $query." where Pquemp = '".$wemp."' ";
				$query = $query."   and Pqucco = '".$wcco1."'";
				$query = $query."   and Pqupro =  '".$wpro."'";
				$query = $query."   and Pqutip = '3' ";
				$query = $query."   and Pquemp = Pcaemp ";
				$query = $query."   and Pquccp = Pcacco ";
				$query = $query."   and Pqucod = Pcacod ";
				$query = $query."   and Pqugrp = Pcagru ";
				$query = $query."   and pcaano = ".$wanop;
			    $query = $query."   and pcames = ".$wper1;
				$query = $query."   and Pqutin = Mticod ";
				$query = $query."   GROUP BY  Pqutin,Mtides ";
				$query = $query."   ORDER BY  Pqutin,Mtides ";
	    		$err1 = mysql_query($query,$conex);
	    		$query = " SELECT SUM(Monto)  from temp1 ";
	    		$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$gtotal = $row1[0];
				$query = " SELECT  Pqutin,Mtides,SUM(Monto)  from temp1 ";
				$query = $query."  GROUP BY  Pqutin,Mtides ";
				$query = $query."  ORDER BY  Pqutin,Mtides ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					echo "<tr><td bgcolor=#cccccc ><b>COMPONENTE</b></td><td  bgcolor=#cccccc ><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc ><b>MONTO</b></td><td align=right bgcolor=#cccccc ><b>PROPORCION</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						if($gtotal > 0)
							$wpor = $row1[2] / $gtotal * 100;
						echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td align=right>".number_format((double)$row1[2],0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."%</td></tr>";
					}
					echo "<tr><td colspan=2>TOTAL</td><td align=right colspan=2>".number_format((double)$gtotal,0,'.',',')."</td></tr>";
					echo "<tr><td colspan=2>PORCENTAJE DE TERCEROS</td><td align=right colspan=2>".number_format((double)$row[3],2,'.',',')."</td></tr>";
					$wtmn = $gtotal / (1 - $row[3]);
					echo "<tr><td colspan=2>TARIFA MINIMA DE NEGOCIACION</td><td align=right colspan=2>".number_format((double)$wtmn,0,'.',',')."</td></tr></TABLE></CENTER>";
				}
			}
		}
	}
?>
</body>
</html>
