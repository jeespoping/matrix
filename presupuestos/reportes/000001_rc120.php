<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparacion Procedimientos Presupuestados vs Real Anterior</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc120.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc120.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1)  or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPARACION PROCEDIMIENTOS PRESUPUESTADOS VS REAL ANTERIOR</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuesto</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wanopa = $wanop - 1;
			//                  1        2       3       4       5      6           7      8
			$query  = "select Mopcco, Cconom, Mopcod, Prodes, Mopano, Mopmes, sum(Mopcan),'0',Mopcco from ".$empresa."_000031, ".$empresa."_000005, ".$empresa."_000059 ";
			$query .= "  where mopano = ".$wanop;
			$query .= "    and Mopemp = '".$wemp."' ";
			$query .= "    and Mopcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Mopcco = Ccocod";
			$query .= "    and Mopemp = Ccoemp";
			$query .= "    and Mopcod = Procod";
			$query .= "    and Mopemp = Proemp";
			$query .= "  group by 1,2,3,4,5,6,8,9 ";
			$query .= "  union ";
			$query .= " select Mopcco, Cconom, Mopcod, Prodes, Mopano, Mopmes, Mopcan,Moptip,Mopcco from ".$empresa."_000031, ".$empresa."_000005, ".$empresa."_000059 ";
			$query .= "  where mopano = ".$wanop;
			$query .= "    and Mopemp = '".$wemp."' ";
			$query .= "    and Mopcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Mopcco = Ccocod";
			$query .= "    and Mopemp = Ccoemp";
			$query .= "    and Mopcod = Procod";
			$query .= "    and Mopemp = Proemp";
			$query .= "  union ";
			$query .= "  select Morcco, Cconom, Morcod, Prodes, Morano, Mormes, Morcan,'0',Morcco from ".$empresa."_000032, ".$empresa."_000005, ".$empresa."_000059 ";
			$query .= "  where morano = ".$wanopa; 
			$query .= "    and Moremp = '".$wemp."' ";
			$query .= "    and Morcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Morcco = Ccocod";
			$query .= "    and Moremp = Ccoemp";
			$query .= "    and Moremp = Proemp";
			$query .= "    and Morcod = Procod";
			$query .= "  union ";
			$query .= "  select Aprcco, Cconom, Aprcod, Prodes, Aprano, Aprmes,Aprcan,'0',Aprcco from ".$empresa."_000021, ".$empresa."_000005, ".$empresa."_000059 ";
			$query .= "  where aprano = ".$wanopa; 
			$query .= "    and Apremp = '".$wemp."' ";
			$query .= "    and Aprcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Aprcco = Ccocod";
			$query .= "    and Apremp = Ccoemp";
			$query .= "    and Aprcod = Procod";
			$query .= "    and Apremp = Procod";
			$query .= "  order by 1,3,5 desc,8,6 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=0 align=center>";
			echo "<tr><td colspan=15 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=15 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=15 align=center>COMPARACION PROCEDIMIENTOS PRESUPUESTADOS VS REAL ANTERIOR</td></tr>";
			echo "<tr><td colspan=15 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=15 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>A&Ntilde;O</b></td><td bgcolor=#cccccc><b>ENE</b></td><td bgcolor=#cccccc><b>FEB</b></td><td bgcolor=#cccccc><b>MAR</b></td><td bgcolor=#cccccc><b>ABR</b></td><td bgcolor=#cccccc><b>MAY</b></td><td bgcolor=#cccccc><b>JUN</b></td><td bgcolor=#cccccc><b>JUL</b></td><td bgcolor=#cccccc><b>AGO</b></td><td bgcolor=#cccccc><b>SEP</b></td><td bgcolor=#cccccc><b>OCT</b></td><td bgcolor=#cccccc><b>NOV</b></td><td bgcolor=#cccccc><b>DIC</b></td><td bgcolor=#cccccc><b>TOTAL</b></td><td bgcolor=#cccccc><b>PROMEDIO</b></td></tr>";
			$wtotal=0;
			$woccur=0;
			$wccoa="";
			$wlina="";
			$wanoa=0;
			$wtipa="";
			$wl=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[4] != $wanoa or $row[8]."-".$row[1] != $wccoa or $row[2]."-".$row[3] != $wlina or $row[7] != $wtipa)
				{
					if($i != 0)
					{
						$wpor=$wtotal / $woccur;
						if($wanoa == $wanop)
							$wanot = $wanoa." - Presupuesto ";
						else
							$wanot = $wanoa." - Real ";
						if($wl % 2 == 0)
							$color="#dddddd";
						else
							$color="#ffffff";
						$wl++;
						echo "<tr><td bgcolor=".$color.">".$wanot."-".$wtipa."</td>";
						for ($j=1;$j<=12;$j++)
							echo "<td bgcolor=".$color." align=right>".number_format((double)$wdat[$j],0,'.',',')."</td>"; 
						echo "<td bgcolor=".$color." align=right>".number_format((double)$wtotal,0,'.',',')."</td>"; 
						echo "<td bgcolor=".$color." align=right>".number_format((double)$wpor,0,'.',',')."</td></tr>"; 
					}
					for ($j=1;$j<=12;$j++)
						$wdat[$j]=0;
					$wtotal=0;
					$woccur=0;
					$wanoa=$row[4];
					$wtipa=$row[7];
				}
			
				if($row[8]."-".$row[1] != $wccoa)
				{
					$wccoa = $row[8]."-".$row[1];
					echo "<tr><td bgcolor=#FFCC66 colspan=15><b>UNIDAD : ".$wccoa."</b></td></tr>";
					$wlina = $row[2]."-".$row[3];
					echo "<tr><td bgcolor=#99CCFF colspan=15><b>&nbsp&nbspLINEA : ".$wlina."</b></td></tr>";
				}
				if($row[2]."-".$row[3] != $wlina)
				{
					$wlina = $row[2]."-".$row[3];
					echo "<tr><td bgcolor=#99CCFF colspan=15><b>&nbsp&nbspLINEA : ".$wlina."</b></td></tr>";
				}
				$wdat[$row[5]] = $row[6];
				$wtotal += $row[6];
				$woccur += 1;
			}
			$wpor=$wtotal / $woccur;
			if($wanoa == $wanop)
				$wanot = $wanoa." - Presupuesto ";
			else
				$wanot = $wanoa." - Real ";
			if($wl % 2 == 0)
				$color="#dddddd";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color.">".$wanot."-".$wtipa."</td>";
			for ($j=1;$j<=12;$j++)
				echo "<td bgcolor=".$color." align=right>".number_format((double)$wdat[$j],0,'.',',')."</td>"; 
			echo "<td bgcolor=".$color." align=right>".number_format((double)$wtotal,0,'.',',')."</td>"; 
			echo "<td bgcolor=".$color." align=right>".number_format((double)$wpor,0,'.',',')."</td></tr>";
		}
	}
?>
</body>
</html>
