<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Distribucion Otros Costos x Subproceso x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro64.php Ver. 2016-09-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro64.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1) or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DISTRIBUCION OTROS COSTOS X SUBPROCESO X UNIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			$wemp = substr($wemp,0,2);
			$query = "SELECT ciccco from ".$empresa."_000131  ";
			$query = $query."  where cicano = ".$wanop;
			$query = $query."    and cicemp = '".$wemp."'";
			$query = $query."    and cicmes = ".$wper1;
			$query = $query."    and ciccco between '".$wcco1."' and '".$wcco2."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num == 0)
			{
				$count=0;
				$query = "delete from ".$empresa."_000087 ";
				$query = $query."  where gasano = ".$wanop;
				$query = $query."    and gasemp = '".$wemp."'";
				$query = $query."    and gasmes = ".$wper1;
				$query = $query."    and gascco between '".$wcco1."' and '".$wcco2."' ";
				$query = $query."    and (gastip= 'GENERALES' ";
				$query = $query."     or   gastip= 'INDIRECTOS' ";
				$query = $query."     or   gastip= 'EXPLICACIONES' ";
				$query = $query."     or   gastip= 'TRASLADOS' ";
				$query = $query."     or   gastip= 'SERVICIOS') ";
				$err = mysql_query($query,$conex);
				$query = "DROP TABLE IF EXISTS ELI1";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$query  = "CREATE TEMPORARY TABLE ELI1 SELECT Mgacco as a0, Mgagas as a1, Mdrsub as a2, Cogrub as a3, Mgatip as a4, 'D' as k, sum(Mgaval*Mdrpor*(Rcdpor / 100)) as suma from ".$empresa."_000092,".$empresa."_000101,".$empresa."_000091,".$empresa."_000079 "; 
				$query .= " where Mgaano = ".$wanop;
				$query .= "   and Mgaemp = '".$wemp."'";
				$query .= "	  and Mgaper = ".$wper1;
				$query .= "	  and Mgacco between '".$wcco1."' and '".$wcco2."' ";
				$query .= "	  and Rcdemp = Mgaemp ";
				$query .= "	  and Rcdano = Mgaano ";
				$query .= "	  and Rcdmes = Mgaper  ";
				$query .= "	  and Rcdcco = Mgacco  ";
				$query .= "	  and Rcdgas = Mgagas  ";
			    $query .= "   and Rcdsga = Mgasga  ";
			    $query .= "	  and Rcdgas = Cogcod "; 
				$query .= "	  and Rcdtip = 'D' "; 
				$query .= "	  and Mdremp = Rcdemp  ";
				$query .= "	  and Mdrano = Rcdano  ";
				$query .= "	  and Mdrmes = Rcdmes "; 
				$query .= "	  and Mdrcco = Rcdcco  ";
				$query .= "	  and Mdrcod = Rcddri "; 
				$query .= "	group by 1,2,3,4,5,6  "; 
				$query .= "	UNION ALL "; 
				$query .= " SELECT Mgacco as a0, Mgagas as a1, Rcddri as a2, Cogrub as a3, Mgatip as a4,'S' as k, sum(Mgaval*(Rcdpor / 100)) as suma from ".$empresa."_000092,".$empresa."_000101,".$empresa."_000079  ";
				$query .= " where Mgaano = ".$wanop;
				$query .= "   and Mgaemp = '".$wemp."'";
				$query .= "   and Mgaper = ".$wper1;
				$query .= "   and Mgacco between '".$wcco1."' and '".$wcco2."' ";
				$query .= "	  and Rcdemp = Mgaemp ";
				$query .= "   and Rcdano = Mgaano  ";
				$query .= "   and Rcdmes = Mgaper  ";
				$query .= "   and Rcdcco = Mgacco  ";
				$query .= "   and Rcdgas = Mgagas  ";
			    $query .= "   and Rcdsga = Mgasga  ";
				$query .= "   and Rcdtip = 'S' "; 
				$query .= "   and Rcdgas = Cogcod  ";
				$query .= " group by 1,2,3,4,5,6 ";
				$query .= " order by 1,2,3,4,5  ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				//$num = mysql_num_rows($err);
				$query  = "SELECT a0,a1,a2,a3,a4, sum(suma) from ELI1 GROUP BY 1,2,3,4,5 ORDER BY 1,2,3,4,5 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
	        		$query = "insert ".$empresa."_000087 (medico,fecha_data,hora_data,Gasemp, Gasano, Gasmes, Gascco, Gasgas, Gassub, Gasrub, Gasval, Gastip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[2]."','".$row[3]."',".$row[5].",'".$row[4]."','C-".$empresa."')";
	        		//echo $query."<br>";
					$err1 = mysql_query($query,$conex) or die("T87 ".mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTRO INSERTADO NRO : ".$count."<br>";
	    		}
				echo "TOTAL REGISTROS INSERTADOS : ".$count;
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL CCO ESTA CERRADO EN ESTE PERIODO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
