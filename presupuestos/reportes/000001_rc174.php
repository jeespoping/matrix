<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
    	.tipotot{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;}
    	.tipotit{color:#000066;background:#dddddd;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tipouti{color:#000066;background:#C3D9FF;font-size:10pt;font-family:Arial;font-weight:bold;}
    	.tipolin1{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;}
    	.tipolin2{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:normal;}
    	.tiponak{color:#000066;background:#F5A9A9;font-size:9pt;font-family:Arial;font-weight:normal;}
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion de Tarifas Tipo P Costos Totales (CP)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc174.php Ver. 2017-08-23</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	//$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),30);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='000001_rc174.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes) or !isset($wemp) or !isset($wemp1) or $wemp1 == "Seleccione" or !isset($wcco))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=3><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=3>EVALUACION DE TARIFAS TIPO P COSTOS TOTALES (CP)</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2>A&ntilde;o de Proceso</td>";
		if(isset($wano))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' value=".$wano." size=4 maxlength=4></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2>Mes de Proceso</td>";
		if(isset($wmes))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' value=".$wmes." size=2 maxlength=2></td></tr>";	
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center colspan=2>";
		$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp1'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if(isset($wemp1) and substr($wemp1,0,strpos($wemp1,"-")) == $row[0])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		if(isset($wemp1) and  $wemp1 != "Seleccione")
		{
			echo "<tr><td bgcolor=#999999 align=center><b>Grupo</b></td><td bgcolor=#999999 align=center><b>Segmento</b></td><td bgcolor=#999999 align=center><b>Entidad</b></td></tr>";
			$query = "select Empgru,Empseg,Empcin,Empdes from ".$empresa."_000061 where Empemp='".substr($wemp1,0,2)."' and ".$empresa."_000061.Empeva='S' group by 1,2,3 order by 1,2,3 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<input type='HIDDEN' name= 'numE' value='".$num."'>";
				echo "<tr><td bgcolor=#cccccc align=center colspan=3><input type='checkbox' name='wempT'>&nbsp;&nbsp;TODAS LOS GRUPOS - SEGMENTOS Y EMPRESAS</td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td bgcolor=#dddddd><input type='checkbox' name='wemp[".$i."][0]'>&nbsp;&nbsp;".$row[0]."</td><td bgcolor=#dddddd align=center>".$row[1]."</td><td bgcolor=#dddddd>".$row[2]."-".$row[3]."</td></tr>";
					echo "<input type='HIDDEN' name= 'wemp[".$i."][1]' value='".$row[2]."'>";
				}
			}
			echo "<tr><td bgcolor=#999999 align=center><b>Centro Costos</b></td><td bgcolor=#999999 align=center colspan=2><b>Descripcion</b></td></tr>";
			$query = "SELECT Ccocod, cconom from ".$empresa."_000005 where ccoemp='".substr($wemp1,0,2)."' and ccoclas='PR' and ccoest='on' and ccocos='S' order by Ccocod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<input type='HIDDEN' name= 'numC' value='".$num."'>";
				echo "<tr><td bgcolor=#cccccc align=center colspan=3><input type='checkbox' name='wccoT'>&nbsp;&nbsp;TODAS LOS CENTROS DE COSTOS</td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td bgcolor=#dddddd><input type='checkbox' name='wcco[".$i."][0]'>&nbsp;&nbsp;".$row[0]."</td><td bgcolor=#dddddd colspan=2>".$row[1]."</td></tr>";
					echo "<input type='HIDDEN' name= 'wcco[".$i."][1]' value='".$row[0]."'>";
				}
			}
		}
		echo "<tr><td bgcolor=#999999  colspan=3 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wemp1t = $wemp1;
		$wemp1 = substr($wemp1,0,2);
		$wcin = "(";
		$kn = 0;
		if(!isset($wempT))
		{
			for ($i=0;$i<$numE;$i++)
			{
				if(isset($wemp[$i][0]))
				{
					if($kn == 0)
						$wcin .= "'".$wemp[$i][1]."'";
					else
						$wcin .= ",'".$wemp[$i][1]."'";
					$kn++;
				}
			}
			$wcin .= ")";
		}
					
					
					
		$wcc = "(";
		$kn = 0;
		if(!isset($wccoT))
		{
			for ($i=0;$i<$numC;$i++)
			{
				if(isset($wcco[$i][0]))
				{
					if($kn == 0)
						$wcc .= "'".$wcco[$i][1]."'";
					else
						$wcc .= ",'".$wcco[$i][1]."'";
					$kn++;
				}
			}
			$wcc .= ")";
		}
			
		//$query = "SELECT Ano, Mes from ".$empresa."_000048 where Cierre_costos='on'  and Emp = '".$wemp1."'order by 1 desc,2 desc";
		//$err = mysql_query($query,$conex);
		//$num = mysql_num_rows($err);
		//if ($num>0)
		//{
				//$row = mysql_fetch_array($err);
				//$wano = $row[0];
				//$wmes = $row[1];
		//}
		
		$COSVAR=array();
		$query  = "select cvpcco,LPAD(cvpcod,10,'0'),LPAD(cvpgru,4,'0'),cvpcon,cvppro from ".$empresa."_000156 ";
		$query .= " where cvpano = ".$wano;  
		$query .= "   and cvpemp = '".$wemp."'"; 
		$query .= "   and cvpmes = ".$wmes; 
		if(!isset($wccoT))
			$query .= "    and cvpcco IN ".$wcc;  
		$query .= " group by 1,2,3,4 ";
		$query .= " order by 1,2,3,4 ";
		$err = mysql_query($query,$conex);	
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$COSVAR[$i][0] = $row[0].$row[1].$row[2].$row[3];
				$COSVAR[$i][1] = $row[4];
			}
		}
		$tcv=$num;
		
		//                  0       1     2      3      4      5      6      7      8      9      10     11    12      13     14     15     16
		$query  = "select Empgru,Empseg,Empcin,pcpcco,Cconom,pcpcod,pcpcon,pcppor,pcppro,tarcta,Tarmon,Mprnom,ntades,cfades,Empdes,mprnom,pcpgru ";
		$query .= " from ".$empresa."_000105,".$empresa."_000155,".$empresa."_000060,".$empresa."_000095,".$empresa."_000005,".$empresa."_000140,".$empresa."_000061,".$empresa."_000150 ";
		$query .= "  where tarcco=pcpcco ";
		$query .= "    and ".$empresa."_000105.taremp = '".$wemp."'"; 
		$query .= "    and tarcod=pcpcod "; 
		$query .= "    and ".$empresa."_000105.taremp=pcpemp "; 
		$query .= "    and tarcon=pcpcon ";
		$query .= "    and pcpano=".$wano; 
		$query .= "    and pcpmes=".$wmes;  
		$query .= "    and pcptip IN ('P','O') ";
		$query .= "    and pcpcon=cfacod ";
		$query .= "    and pcpemp=cfaemp ";
		$query .= "    and pcpgru between '0000' and 'zzzz' ";
		$query .= "    and pcpemp=mpremp ";
		$query .= "    and pcpcco=mprcco ";
		$query .= "    and pcpcod=mprpro "; 
		$query .= "    and pcpcon=mprcon ";
		$query .= "    and pcpcco=ccocod ";
		$query .= "    and pcpemp=ccoemp ";
		if(!isset($wccoT))
			$query .= "    and ccocod IN ".$wcc;
		$query .= "    and ccoclas='PR' ";
		$query .= "    and ccoest='on' ";
		$query .= "    and ccocos='S' ";
		$query .= "    and tartar=tarcta "; 
		$query .= "    and ".$empresa."_000105.taremp=".$empresa."_000140.taremp "; 
		$query .= "    and tarent=empcin ";
		$query .= "    and ".$empresa."_000105.taremp=empemp "; 
		if(!isset($wempT))
			$query .= "    and empcin IN ".$wcin;
		$query .= "    and Empeva='S' ";  
		$query .= "    and tarcta=ntacod ";
		$query .= "    and ".$empresa."_000105.taremp=ntaemp ";
		$query .= " group by 4,6,7,10 ";
		$query .= " order by 1,2,3,4,10,7,6 ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex);	
		$num = mysql_num_rows($err);
		$clave = "";
		echo "<table border=1 class=tipoTABLEGRID>";
		echo "<tr><td colspan=18 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
		echo "<tr><td colspan=18 align=center>DIRECCION DE INFORMATICA</td></tr>";
		echo "<tr><td colspan=18 align=center>EVALUACION DE TARIFAS TIPO P COSTOS TOTALES (CP)</td></tr>";
		echo "<tr><td colspan=15 align=center>EMPRESA : ".$wemp1t."</td></tr>";
		echo "<tr class='tipotit'><td>CODIGO CC</td><td>DESCRIPCION</td><td>COD TARIFA</td><td>NOMBRE<br>TARIFA</td><td>COD CONCEPTO</td><td>NOMBRE<BR>CONCEPTO</td><td>PROCEDIMIENTO</td><td>DESCRIPCION<br>PROCEDIMIENTO</td><td>VALOR TARIFA</td><td>COSTOS<br>PROPIO</td><td>% TERCERO</td><td>COSTO<br>TERCERO</td><td>COSTO<br>TOTAL (TNM)</td><td>UTILIDAD</td><td>MARGEN<br>TOTAL</td><td>COSTO<br>VARIABLE</td><td>CONTRUBUCION</td><td>MARGEN<br>CONTRIBUCION</td></tr>";
		if ($num > 0)
		{
			
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0].$row[1].$row[2] != $clave)
				{
					echo "<tr class='tipouti'><td colspan=3>GRUPO : ".$row[0]."</td><td colspan=3>SEGMENTO : ".$row[1]."</td><td colspan=12>ENTIDAD : ".$row[2]."-".$row[14]."</td></tr>";
					if($i > 0)
						echo "<tr class='tipotit'><td>CODIGO CC</td><td>DESCRIPCION</td><td>COD TARIFA</td><td>NOMBRE<br>TARIFA</td><td>COD CONCEPTO</td><td>NOMBRE<BR>CONCEPTO</td><td>PROCEDIMIENTO</td><td>DESCRIPCION<br>PROCEDIMIENTO</td><td>VALOR TARIFA</td><td>COSTOS<br>PROPIO</td><td>% TERCERO</td><td>COSTO<br>TERCERO</td><td>COSTO<br>TOTAL (TNM)</td><td>UTILIDAD</td><td>MARGEN<br>TOTAL</td><td>COSTO<br>VARIABLE</td><td>CONTRUBUCION</td><td>MARGEN<br>CONTRIBUCION</td></tr>";
					$clave = $row[0].$row[1].$row[2];
				}
				if($i % 2 == 0)
					$clase = "tipolin1";
				else
					$clase = "tipolin2";
				$claseO = $clase;
				$wcoster = $row[7] * $row[10];
				$wcostot = $row[8] + $wcoster;
				$wutil = $row[10] - $wcostot;
				$wmargen = ($wutil / $row[10]) * 100;
				$pos=bi($COSVAR,$tcv,$row[3].str_pad($row[5],10,"0",STR_PAD_LEFT).str_pad($row[16],4,"0",STR_PAD_LEFT).$row[6],0);
				if($pos == -1)
					$row[16] = 0;
				else
					$row[16] = $COSVAR[$pos][1];
				echo "<tr class='".$clase."'><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[9]."</td><td>".$row[12]."</td><td>".$row[6]."</td><td>".$row[13]."</td><td>".$row[5]."</td><td>".$row[15]."</td><td align=right>".number_format((double)$row[10],0,'.',',')."</td><td align=right>".number_format((double)$row[8],0,'.',',')."</td><td align=right>".number_format((double)$row[7],2,'.',',')."</td><td align=right>".number_format((double)$wcoster,2,'.',',')."</td><td align=right>".number_format((double)$wcostot,0,'.',',')."</td>";
				if($wutil < 0)
					$clase = "tiponak";
				echo "<td class='".$clase."' align=right>".number_format((double)$wutil,0,'.',',')."</td>";
				if($wmargen < 0)
					$clase = "tiponak";
				echo "<td class='".$clase."' align=right>".number_format((double)$wmargen,2,'.',',')."%</td>";
				echo "<td class='".$claseO."' align=right>".number_format((double)$row[16],0,'.',',')."</td>";
				$wcontbc = $row[10] - $row[16] - $wcoster;
				$wmargenc = ($wcontbc / $row[10]) * 100;
				echo "<td class='".$claseO."' align=right>".number_format((double)$wcontbc,0,'.',',')."</td>";
				echo "<td class='".$claseO."' align=right>".number_format((double)$wmargenc,2,'.',',')."%</td></tr>";
					
			}
		}
		echo "</table>";
	}
}
?>
</body>
</html>
