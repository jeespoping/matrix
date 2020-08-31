<html>
<head>
  	<title>MATRIX Listado de Asistencia de Socios Ver. 2009-03-09</title>
  	<style>
		.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;}
		.tipo1{color:#000000;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo2{color:#000000;background:#DDDDDD;font-size:6pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo3{color:#000000;background:#FFFFFF;font-size:6pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo4{color:#000000;background:#FFFFFF;font-size:6pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo5{color:#000000;background:#FFFFFF;font-size:6pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo6{color:#000000;background:#FFFFFF;font-size:6pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:25em;}
	    .tipo7{color:#000000;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
  	</style>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Listado' action='listado.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wemp) or !isset($wtip) or !isset($wpar) or !isset($wfec))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=2><b>IMPRESION DE VOTOS ASAMBLEAS DE LAS AMERICAS</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Empresa</td>";			
		echo "<td bgcolor=#cccccc>";
		echo "<select name='wemp'>";
		$query = "select Empcod, Empdes from ".$empresa."_000004 order by Empcod";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		for ($i=0;$i<$num1;$i++)
		{	
			$row1 = mysql_fetch_array($err1);
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc>TIPO DE ASAMBLEA</td>";			
		echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA <input type='RADIO' name='wtip' value='2'> ACUERDO DE ACCIONISTAS </td></tr>";
		echo "<tr><td bgcolor=#cccccc>TIPO DE PARTICIPACION</td>";			
		echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0' checked> ACCIONES <input type='RADIO' name='wpar' value='1'> % COPROPIEDAD </td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de la Asamblea</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wfec' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		switch ($wtip)
		{
			case "0":
				$wtit="ORDINARIA";
			break;
			case "1":
				$wtit="EXTRAORDINARIA";
			break;
			case "2":
				$wtit="DE ACCIONISTAS FIRMANTES DEL ACUERDO";
			break;
		}
		switch ($wpar)
		{
			case "0":
				$wdec=0;
				$wtit2="ACCIONES";
			break;
			case "1":
				$wdec=4;
				$wtit2="% COPROPIEDAD";
			break;
		}
		
		// $query = "SELECT  Socced, CONCAT(Socap1,' ',Socap2,' ',Socnom), Soctac, Socvot, Socdel  from socios_000001  where Socact='A' and Socfir='S' order by 2";
		$codigoEmpresa = substr($wemp,0,2);
		$query = "SELECT Acccod,Accnom,Accva3,Accvxp,Accpde
					FROM ".$empresa."_000001 
				   WHERE Accact='on' 
					AND Accemp='".$codigoEmpresa."' 
			   ORDER BY 2;";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$soc=0;
		$k=0;
		$p="";
		$wsw=0;
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$k++;
				$row = mysql_fetch_array($err);
				$soc += $row[2];
				$color="#FFFFFF";
				if(substr($row[1],0,1) != $p and $wsw == 0)
				{
					$p=substr($row[1],0,1);
					$k=0;
					echo "</table>";
					echo "</div>";
					echo "<div style='page-break-before: always'>";	
					echo "<table align=center class=tipoTABLE1>";
					echo "<tr><td colspan=10 class=tipo1>".substr($wemp,strpos($wemp,"-")+1)."</td></tr>";
					echo "<tr><td colspan=10 class=tipo7>ASAMBLEA ".$wtit." DE SOCIOS</td></tr>";
					echo "<tr><td colspan=10 class=tipo7>Fecha : ".$wfec."</td></tr>";
					echo "<tr><td class=tipo2><font face='courier new' size=2><b>CEDULA</b></font></td><td class=tipo2>NOMBRE</td><td class=tipo2>TOTAL<BR>".$wtit2."</td></td><td class=tipo2>FIRMA</td><td class=tipo2>FIRMA<br>DELEGADO</td><td class=tipo2>NECESITA<br>PODER</td><td class=tipo2>PUEDE<br>REPRESENTAR</td></tr>";
				}
				$wsw=0;
				echo "<tr><td class=tipo3>".number_format((double)$row[0],0,',','.')."&nbsp;</td>";	
				echo "<td class=tipo6>&nbsp;".$row[1]."</td>";	
				echo "<td class=tipo4>".number_format((double)$row[2],$wdec,'.',',')."</td>";
				echo "<td class=tipo5></td>";
				echo "<td class=tipo5></td>";	
				$poder="NO";
				// Socvot
				if ($row[3] == "on")
					$poder="SI";
				$delega="NO";
				// Socdel
				if ($row[4] == "on")
					$delega="SI";
				echo "<td class=tipo4>".$poder."</td>";
				echo "<td class=tipo4>".$delega."</td>";
				if($k == 24)
				{
					$wsw=1;
					$k=0;
					echo "</table>";
					echo "</div>";
					echo "<div style='page-break-before: always'>";	
					echo "<table align=center class=tipoTABLE1>";
					echo "<tr><td colspan=10 class=tipo1>".substr($wemp,strpos($wemp,"-")+1)."</td></tr>";
					echo "<tr><td colspan=10 class=tipo2>ASAMBLEA ".$wtit." DE SOCIOS</td></tr>";
					echo "<tr><td colspan=10 class=tipo2>Fecha : ".$wfec."</td></tr>";
					echo "<tr><td class=tipo2><font face='courier new' size=2><b>CEDULA</b></font></td><td class=tipo2>NOMBRE</td><td class=tipo2>TOTAL<BR>".$wtit2."</td></td><td class=tipo2>FIRMA</td><td class=tipo2>FIRMA<br>DELEGADO</td><td class=tipo2>NECESITA<br>PODER</td><td class=tipo2>PUEDE<br>REPRESENTAR</td></tr>";
				}
			}
			echo "<tr><td colspan=4 class=tipo2>TOTAL ACCIONISTAS : ".number_format((double)$num,0,'.',',')."</td><td class=tipo2 colspan=4>TOTAL ACCIONES : ".number_format((double)$soc,$wdec,'.',',')."</td></tr>";	
			echo"</table>";
		}
	}
}
?>
</body>
</html>
