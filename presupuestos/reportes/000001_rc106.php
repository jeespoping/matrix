<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Rentabilidad de Un Convenio para Un Grupo</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc106.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc106.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wgru) or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or !isset($wtip)  or (strtoupper ($wtip) != "C" and strtoupper ($wtip) != "L") or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>RENTABILIDAD DE UN CONVENIO PARA UN GRUPO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Grupo</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT empgru from ".$empresa."_000061 where Empeva = 'S' group by empgru order by empgru";
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
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Por Centro de Costos o Linea ? (C/L)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wres=strtoupper ($wres);
			$wtip=strtoupper ($wtip);
			$query = "select Motemp,sum(Motfto),sum(Motfte),sum(Motcos) from ".$empresa."_000109 ";
			$query = $query."  where Motano = ".$wanop;
			$query = $query."      and Motmes  between ".$wper1." and ".$wper2;
			$query = $query."      and Motemp in(select Empcin from ".$empresa."_000061 where empgru = '".$wgru."')   ";
			$query = $query."      and ((Motlin = '1' ";
			$query = $query."      and    Motest = '1') ";
			$query = $query."         or    Motlin > '1') ";
			$query = $query."    group by Motemp  ";
			$query = $query."    order by Motemp ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=9 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=9 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=9 align=center>RENTABILIDAD DE UN CONVENIO PARA UN GRUPO</td></tr>";
			echo "<tr><td colspan=9 align=center>GRUPO : ".$wgru."</td></tr>";
			echo "<tr><td colspan=9 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A�O : ".$wanop."</td></tr>";
			$wdat=array();
			for ($i=0;$i<8;$i++)
				$wdat[$i]=0;
			echo "<tr><td bgcolor=#999999><b>ENTIDAD</b></td><td bgcolor=#999999 align=right><b>FACT. TOTAL</b></td><td bgcolor=#999999 align=right><b>FACT. TERCEROS</b></td><td bgcolor=#999999 align=right><b>COSTO</b></td><td bgcolor=#999999 align=right><b>UTIL. OPER.</b></td><td bgcolor=#999999 align=right><b>NOTAS</b></td><td bgcolor=#999999 align=right><b>COST.  ADMON</b></td><td bgcolor=#999999 align=right><b>UTIL. CONVENIO</b></td><td bgcolor=#999999 align=right><b>MARGEN UTILIDAD</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "select Empcin, Empdes, Empseg  from ".$empresa."_000061 ";
				$query = $query."  where Empcin = '".$row[0]."'";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
					$codemp=$row1[0]."-".$row1[1]."-".$row1[2];
				else
					$codemp="NO DETERMINADA";
				$utilo = $row[1] - $row[2] - $row[3];
				$query = "select sum(Notmon) from ".$empresa."_000118 ";
				$query = $query."  where Notano = ".$wanop;
				$query = $query."      and Notmes  between ".$wper1." and ".$wper2;
				$query = $query."      and Notent = '".$row[0]."'";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
					$notas=$row1[0];
				else
					$notas=0;
				$query = "select sum(Cadmon) from ".$empresa."_000119 ";
				$query = $query."  where Cadano = ".$wanop;
				$query = $query."      and Cadmes  between ".$wper1." and ".$wper2;
				$query = $query."      and Cadent = '".$row[0]."'";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
					$cadmin=$row1[0];
				else
					$cadmin=0;
				$utilc=$utilo + $notas - $cadmin;
				if(($row[1] - $row[2]) != 0)
					$margen = ($utilc / ($row[1] - $row[2])) * 100;
				else
					$margen = 0;
				echo "<tr><td bgcolor=#FFCC66><b>".$codemp."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($row[1],0,'.',',')."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($row[2],0,'.',',')."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($row[3],0,'.',',')."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($utilo,0,'.',',')."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($notas,0,'.',',')."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($cadmin,0,'.',',')."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($utilc,0,'.',',')."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format($margen,2,'.',',')."%</b></td></tr>";
				$wdat[0]+=$row[1];
				$wdat[1]+=$row[2];
				$wdat[2]+=$row[3];
				$wdat[3]+=$utilo;
				$wdat[4]+=$notas;
				$wdat[5]+=$cadmin;
				$wdat[6]+=$utilc;
				if($wres == "D")
				{
					if($wtip == "L")
					{
						$query = "select Motlin,lindes,sum(Motfto),sum(Motfte),sum(Motcos) from ".$empresa."_000109,".$empresa."_000107 ";
						$query = $query."  where Motano = ".$wanop;
						$query = $query."      and Motmes  between ".$wper1." and ".$wper2;
						$query = $query."      and Motemp = '".$row[0]."' ";
						$query = $query."      and Motlin = lincod ";
						$query = $query."      and ((Motlin = '1' ";
						$query = $query."      and    Motest = '1') ";
						$query = $query."         or    Motlin > '1') ";
						$query = $query."    group by Motlin,lindes  ";
						$query = $query."    order by Motlin ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							$utilo = $row1[2] - $row1[3] - $row1[4];
							if ($row1[2] - $row1[3] != 0)
								$margen=$utilo /( $row1[2] - $row1[3]) * 100;
							else
								$margen = 0;
							echo "<tr><td bgcolor=#dddddd>".$row1[0]."-".$row1[1]."</td><td bgcolor=#dddddd align=right>".number_format($row1[2],0,'.',',')."</td><td bgcolor=#dddddd align=right>".number_format($row1[3],0,'.',',')."</td><td bgcolor=#dddddd align=right>".number_format($row1[4],0,'.',',')."</td><td bgcolor=#dddddd align=right>".number_format($utilo,0,'.',',')."</td><td bgcolor=#dddddd align=right colspan=4>".number_format($margen,2,'.',',')."%</td></tr>";
						}
					}
					else
					{
						$query = "select Motcco,Cconom,sum(Motfto),sum(Motfte),sum(Motcos) from ".$empresa."_000109,".$empresa."_000005 ";
						$query = $query."  where Motano = ".$wanop;
						$query = $query."      and Motmes  between ".$wper1." and ".$wper2;
						$query = $query."      and Motemp = '".$row[0]."' ";
						$query = $query."      and Motcco = Ccocod ";
						$query = $query."      and ((Motlin = '1' ";
						$query = $query."      and    Motest = '1') ";
						$query = $query."         or    Motlin > '1') ";
						$query = $query."    group by Motcco,Cconom  ";
						$query = $query."    order by Motcco ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							$utilo = $row1[2] - $row1[3] - $row1[4];
							if ($row1[2] - $row1[3] != 0)
								$margen=$utilo /( $row1[2] - $row1[3]) * 100;
							else
								$margen = 0;
							echo "<tr><td bgcolor=#dddddd>".$row1[0]."-".$row1[1]."</td><td bgcolor=#dddddd align=right>".number_format($row1[2],0,'.',',')."</td><td bgcolor=#dddddd align=right>".number_format($row1[3],0,'.',',')."</td><td bgcolor=#dddddd align=right>".number_format($row1[4],0,'.',',')."</td><td bgcolor=#dddddd align=right>".number_format($utilo,0,'.',',')."</td><td bgcolor=#dddddd align=right colspan=4>".number_format($margen,2,'.',',')."%</td></tr>";
						}
					}
				}
			}
			if(($wdat[0] - $wdat[1]) != 0)
				$wdat[7] =($wdat[6] /($wdat[0] - $wdat[1]))*100;
			echo "<tr><td bgcolor=#99CCFF><b>TOTAL GRUPO</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[0],0,'.',',')."</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[1],0,'.',',')."</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[2],0,'.',',')."</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[3],0,'.',',')."</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[4],0,'.',',')."</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[5],0,'.',',')."</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[6],0,'.',',')."</b></td><td bgcolor=#99CCFF align=right><b>".number_format($wdat[7],2,'.',',')."%</b></td></tr></table>";
		}
	}
?>
</body>
</html>
