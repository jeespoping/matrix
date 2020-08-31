<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Distribucion Nomina x Subproceso x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro56.php Ver. 2018-03-27</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro56.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DISTRIBUCION NOMINA X SUBPROCESO X UNIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes  de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			//echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso</td>";
			//echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
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
			$count=0;
			$query = "delete from ".$empresa."_000087 ";
			$query = $query."  where gasano = ".$wanop;
			$query = $query."    and gasemp = '".$wemp."'";
			$query = $query."    and gasmes = ".$wper1;
			$query = $query."    and gastip= 'NOMINA'";
			$err = mysql_query($query,$conex);
			//                 0      1      2       3       4              5
			$query = "SELECT  Mnocco,Pdisub, sum(Mnopag*Pdipor)  from ".$empresa."_000094,".$empresa."_000098 ";
			$query = $query."  where Mnoano = ".$wanop;
			$query = $query."    and Mnoemp = '".$wemp."'";
			$query = $query."    and Mnomes = ".$wper1;
			$query = $query."    and Mnoemp = Pdiemp";
			$query = $query."    and Mnoano = Pdiano";
			$query = $query."    and Mnomes = Pdimes";
			$query = $query."    and Mnocco = Pdicco";
			$query = $query."    and Mnoofi = Pdiofi";
			$query = $query."    and Pditip = 'S'";
			$query = $query."   group by  1,2 ";
			$query = $query."	UNION ALL "; 
			$query = $query." SELECT Mnocco, Mdrsub, sum(Mnopag*Pdipor*Mdrpor)  from ".$empresa."_000094,".$empresa."_000098,".$empresa."_000091 ";
			$query = $query."  where Mnoano = ".$wanop;
			$query = $query."    and Mnoemp = '".$wemp."'";
			$query = $query."    and Mnomes = ".$wper1;
			$query = $query."    and Mnoemp = Pdiemp";
			$query = $query."    and Mnoano = Pdiano";
			$query = $query."    and Mnomes = Pdimes";
			$query = $query."    and Mnocco = Pdicco";
			$query = $query."    and Mnoofi = Pdiofi";
			$query = $query."    and Pditip = 'D'";
			$query = $query."    and Pdiemp = Mdremp";
			$query = $query."    and Pdiano = Mdrano";
			$query = $query."    and Pdimes = Mdrmes";
			$query = $query."    and Pdicco = Mdrcco";
			$query = $query."    and Pdisub = Mdrcod";
			$query = $query."   group by  1,2 ";
			$query = $query."   Order by  1,2 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$clave = "";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($clave != $row[0].$row[1])
				{
					if($i > 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000087 (medico,fecha_data,hora_data,Gasemp, Gasano, Gasmes, Gascco, Gasgas, Gassub, Gasrub, Gasval, Gastip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$cco."','201','".$sub."','COPE',".$val.",'NOMINA','C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
						$count++; 
						echo "REGISTRO INSERTADO NRO : ".$count."<br>";
					}
					$cco = $row[0];
					$sub = $row[1];
					$clave = $row[0].$row[1];
					echo "Clave:".$clave." long:".strlen($clave)."<br>";
					$val = 0;
				}
				$val += $row[2];
			}
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000087 (medico,fecha_data,hora_data,Gasemp, Gasano, Gasmes, Gascco, Gasgas, Gassub, Gasrub, Gasval, Gastip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$cco."','201','".$sub."','COPE',".$val.",'NOMINA','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$count++; 
			echo "REGISTRO INSERTADO NRO : ".$count."<br>";
			echo "<b>TOTAL REGISTROS INSERTADOS : ".$count."<b>";
		}
	}
?>
</body>
</html>
