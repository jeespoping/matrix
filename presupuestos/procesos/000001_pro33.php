<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Grabacion Indirectos Presupuestados Distribuidos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro33.php Ver. 2015-11-17</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro33.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GRABACION INDIRECTOS PRESIPUESTADOS DISTRIBUIDOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "delete from ".$empresa."_000043 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and resind = '13'";
			$err2 = mysql_query($query,$conex);
			$count=0;
			//                 0      1      2      3      4        5
			$query = "select Mdiano,Mdimes,Mdicco,Midcpr,Middes,sum(Mdimon) ";
			$query = $query."from ".$empresa."_000054,".$empresa."_000050,".$empresa."_000005 ";
			$query = $query." where mdiano = ".$wanop;
			$query = $query."   and mdiemp = '".$wemp."' ";
			$query = $query."   and mditip = 'P'";
			$query = $query."   and mdiind = midcod  ";
			$query = $query."   and mdiemp = midemp  ";
			$query = $query."   and mdicco = ccocod ";
			$query = $query."   and mdiemp = ccoemp ";
			$query = $query."   and ccoclas != 'IND'  ";
			$query = $query." group by mdiano,mdimes,mdicco,midcpr,Middes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000122 (medico,fecha_data,hora_data,Gahemp,Gahano, Gahmes, Gahcco, Gahcpr, Gahval, Gahdes, Gahtip, Gahprg, Gahccr, Gahmic,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",".$row[1].",'".$row[2]."','".$row[3]."',".$row[5].",'INDIRECTO-".$row[4]."','3','00','2046',13,'C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
    		}
			echo "REGISTROS INSERTADOS UNIDADES NO INDEPENDIENTES : ".$count."<br>";
			$count=0;
			//                 0      1      2      3      4        5
			$query = "select Mdiano,Mdimes,Mdicco,Midcpr,Middes,sum(Mdimon) ";
			$query = $query."from ".$empresa."_000054,".$empresa."_000050,".$empresa."_000005 ";
			$query = $query." where mdiano = ".$wanop;
			$query = $query."   and mdiemp = '".$wemp."' ";
			$query = $query."   and mditip = 'P'";
			$query = $query."   and mdiind = midcod  ";
			$query = $query."   and mdiemp = midemp  ";
			$query = $query."   and mdicco = ccocod ";
			$query = $query."   and mdiemp = ccoemp ";
			$query = $query."   and ccoclas = 'IND'  ";
			$query = $query." group by mdiano,mdimes,mdicco";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000122 (medico,fecha_data,hora_data,Gahemp, Gahano, Gahmes, Gahcco, Gahcpr, Gahval, Gahdes, Gahtip, Gahprg, Gahccr, Gahmic,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",".$row[1].",'".$row[2]."','516',".$row[5].",'INDIRECTO-".$row[4]."','3','00','2046',13,'C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
    		}
			echo "REGISTROS INSERTADOS UNIDADES INDEPENDIENTES : ".$count."<br>";
		}
	}
?>
</body>
</html>
