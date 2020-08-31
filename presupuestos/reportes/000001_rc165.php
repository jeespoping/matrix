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
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Cirugias</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc165.php Ver. 2017-08-24</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_rc165.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wano) or !isset($wmarca) or !isset($wcco1) or !isset($wcco2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>EVALUACION DE CIRUGIAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Marca</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmarca' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 group by 1 order by Empcod";
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
		$CPTO=array();
		$query  = "select Cfacod,Cfades from ".$empresa."_000060 where Cfaemp = '".$wemp."' ";
		$query .= " Order by 1 ";
		$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				$CPTO[$i][0] = $row2[0];
				$CPTO[$i][1] = $row2[1];
			}
		}
		$numcpto = $num2;
		
		$EMPR=array();
		$query  = "select Empcin,Empdes from ".$empresa."_000061 where Empemp = '".$wemp."' "; 
		$query .= " Order by 1 ";
		$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				$EMPR[$i][0] = $row2[0];
				$EMPR[$i][1] = $row2[1];
			}
		}
		$numempr = $num2;
		
		$HIS="(";
		$query  = "select CONCAT(Hishis,'-',Hising) from ".$empresa."_000148 ";
		$query .= " where Hisano = ".$wano;
		$query .= "   and Hismar = '".$wmarca."'";
		$query .= "   and Hisemp = '".$wemp."'";
		$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				if($i == 0)
					$HIS .= "'".$row2[0]."'";
				else
					$HIS .= ",'".$row2[0]."'";
			}
		}
		$HIS .= ")";
		
		//                  0       1      2     3      4      5      6       7     8      9      10
		$query  = "select Moscco,Moscon,Mospro,Mosdes,Moshis,Mosing,Mosent,Mosipr,Mosite,Moscan,Mosctt ";
		$query .= "     from ".$empresa."_000108 ";
		$query .= "  	  where CONCAT(Moshis,'-',Mosing) in ".$HIS;
		$query .= "  	  and moscco between '".$wcco1."' and '".$wcco2."' ";
		$query .= "  order by 1,2,3,7,5 ";


		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			echo "<table border=1 class=tipoTABLEGRID>";
			echo "<tr><td colspan=14 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=14 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=14 align=center>EVALUACION DE CIRUGIAS</td></tr>";
			echo "<tr><td colspan=14 align=center>A&Ntilde;O : ".$wano."</td></tr>";
			echo "<tr><td colspan=14 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=14 align=center>MARCA : ".$wmarca."</td></tr>";
			echo "<tr><td colspan=14 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr class='tipotit'><td>CODIGO CC</td><td>CONCEPTO</td><td>DESCRIPCION</td><td>PROCEDIMIENTO</td><td>DESCRIPCION</td><td>ENTIDAD</td><td>NOMBRE</td><td>HISTORIA-INGRESO</td><td>INGRESO PROPIO</td><td>INGRESO TERCEROS</td><td>INGRESO TOTAL</td><td>CANTIDAD</td><td>COSTO UNITARIO</td><td>COSTO TOTAL</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pos=bi($CPTO,$numcpto,$row[1]);
				if($pos != -1)
					$wncpto = $CPTO[$pos][1];
				else
					$wncpto = " ";
					
				$pos=bi($EMPR,$numempr,$row[6]);
				if($pos != -1)
					$wnent = $EMPR[$pos][1];
				else
					$wnent = " ";
				if($i % 2 == 0)
					$clase = "tipolin1";
				else
					$clase = "tipolin2";
				$wingt = $row[7] + $row[8];
				$whising = $row[4]."-".$row[5];
				if($row[9] != 0)
					$wcosuni = $row[10] / $row[9];
				else
					$wcosuni = 0;
				echo "<tr class='".$clase."'><td>".$row[0]."</td><td>".$row[1]."</td><td>".$wncpto."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[6]."</td><td>".$wnent."</td><td>".$whising."</td><td align=right>".number_format((double)$row[7],0,'.',',')."</td><td align=right>".number_format((double)$row[8],0,'.',',')."</td><td align=right>".number_format((double)$wingt,0,'.',',')."</td><td align=right>".number_format((double)$row[9],0,'.',',')."</td><td align=right>".number_format((double)$wcosuni,2,'.',',')."</td><td align=right>".number_format((double)$row[10],2,'.',',')."</td></tr>";
				
			}
		}
	}
}
?>
</body>
</html>
