<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Depuracion de Ingresos Para Costos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro144.php Ver. 2016-04-29</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro144.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>DEPURACION DE INGRESOS PARA COSTOS</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Ano de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
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
			$wres=strtoupper ($wres);
			$k=0;
			#INICIO PROGRAMA
			$wtable= date("YmdHis");
			$wtable=" temp_".$wtable;
			$query = "create table IF NOT EXISTS ".$wtable." ( Medico varchar(8) not null,Fecha_data date not null ,Hora_data time not null ,Fddano int not null,Fddmes int not null,Fddcco VARCHAR(80) not null,Fddcon VARCHAR(80) not null,Fddcod VARCHAR(80) not null,Fddnit VARCHAR(80) not null,Fddcan Double not null,Fddite Double not null,Fddipr Double not null,Fddtip VARCHAR(80) not null, Seguridad varchar(10) not null, id bigint not null auto_increment, primary key(id))";
			$err = mysql_query($query,$conex);
			//                 0       1        2       3       4       5          6             7           8        9
			$query = "SELECT Fddano, Fddmes, Fddcco, Fddcon, Fddcod, Fddnit, sum(Fddcan), sum(Fddite), sum(Fddipr), Fddtip  from ".$empresa."_000137 ";
			$query = $query." where Fddano = ".$wanop;
			$query = $query."   and Fddemp = '".$wemp."' ";
			$query = $query."   and Fddmes = ".$wmesi;
			$query = $query."   Group by Fddano, Fddmes, Fddcco, Fddcon, Fddcod, Fddnit, Fddtip ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[3] != "2000" and $row[3] != "1017" and $row[3] != "4199")
					{
						$query = "SELECT count(*) as num from ".$empresa."_000061 ";
						$query = $query." where Empcin = '".$row[5]."'";
						$query = $query."   and Empemp = '".$wemp."' ";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] == 0)
							$row[5]="99";
						if($row[3] == "0001")
							$row[2]="3081";
						if($row[3] == "2090")
							$row[2]="1016";
						if($row[3] == "2091")
							$row[2]="1016";
						if($row[3] == "2044")
							$row[2]="1190";
						//if($row[3] == "2031" or $row[3] == "2035" or $row[3] == "2036" or $row[3] == "2037" or $row[3] == "2038" or $row[3] == "2039" or $row[3] == "2040" or $row[3] == "2042")
						//	$row[2]="1191";
						if($row[3] == "0167" and $row[2] == "1016")
							$row[2]="1188";
						if($row[3] == "0167" and $row[2] == "1191")
							$row[2]="1188";
						if($row[3] == "0167" and $row[2] == "1330")
							$row[2]="1180";
						if($row[3] >= "2056" and $row[3] <= "2059")
							$row[2]="1016";
						if($row[3] == "0035" and $row[2] == "1191")
							$row[2]="1188";
						if($row[3] == "0607")
							$row[2]="1350";
						if($row[3] == "0117")
							$row[2]="1250";
						if($row[3] == "0128")
							$row[2]="1320";
						if($row[3] == "2062" or $row[3] == "2066")
							$row[2]="1135";
						if($row[2] == "1251")
							$row[2]="1135";
						if($row[2] == "1310")
							$row[2]="1300";
						if($row[2] == "1750")
							$row[2]="2240";
						if($row[2] == "1051")
							$row[2]="1050";
						if($row[2] == "3160")
							$row[2]="1135";
						if($row[2] == "3051")
							$row[2]="3050";
						if($row[2] == "3060")
							$row[2]="3050";
						if($row[2] == "3061")
							$row[2]="3050";
						if($row[2] == "3053" or $row[2] == "3054")
							$row[2]="1052";
						if($row[2] == "1192")
							$row[2]="1195";
						if($row[2] == "1380")
							$row[2]="1691";
						if($row[3] == "2025" and $row[2] == "1240")
							$row[2]="1241";
						if($row[2] == "1240")
							$row[2]="1691";
						if($row[3] == "0035" and $row[2] == "1016")
							$row[2]="1186";
						if($row[3] == "0167" and $row[2] == "1191")
							$row[2]="1188";
						if($row[3] == "2009" and $row[2] == "1016")
							$row[2]="1020";
						if($row[3] == "2061")
							$row[2]="1016";
						if($row[3] == "0122")
							$row[2]="3080";
						if($row[3] >= "0503" and $row[3] < "0515" and $row[3] != "0511" and $row[2] != "1031" and $row[2] != "1032")
							$row[2]="1030";
						$query = "SELECT ccoclas from ".$empresa."_000005 ";
						$query = $query." where ccocod = '".$row[2]."'";
						$query = $query."   and ccoemp = '".$wemp."' ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							if ( $row1[0] != "PR" and $row1[0] != "OGI")
								$row[2]="1691";
						}
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$wtable." (medico,fecha_data,hora_data,Fddano, Fddmes, Fddcco, Fddcon, Fddcod, Fddnit, Fddcan, Fddite, Fddipr, Fddtip,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."',".$row[6].",".$row[7].",".$row[8].",'".$row[9]."','C-".$empresa."')";
               			$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
           			}
               	}
			}
			$query = "DELETE from ".$empresa."_000137 ";
			$query = $query." where Fddano = ".$wanop;
			$query = $query."   and Fddemp = '".$wemp."' ";
			$query = $query."   and Fddmes = ".$wmesi;
			$err = mysql_query($query,$conex);
			//                 0       1        2       3       4       5          6             7           8        9
			$query = "SELECT Fddano, Fddmes, Fddcco, Fddcon, Fddcod, Fddnit, sum(Fddcan), sum(Fddite), sum(Fddipr), Fddtip from ".$wtable." ";
			$query = $query." where Fddano = ".$wanop;
			$query = $query."   and Fddmes = ".$wmesi;
			$query = $query."   Group by Fddano, Fddmes, Fddcco, Fddcon, Fddcod, Fddnit, Fddtip ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000137 (medico,fecha_data,hora_data,Fddemp, Fddano, Fddmes, Fddcco, Fddcon, Fddcod, Fddnit, Fddcan, Fddite, Fddipr, Fddtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmesi.",'".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."',".$row[6].",".$row[7].",".$row[8].",'".$row[9]."','C-".$empresa."')";
           			$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
           			$k++;
           			echo "REGISTROS INSERTADOS : ".$k."<br>";
       			}
   			}
   			$query = "DROP table ".$wtable;
			$err = mysql_query($query,$conex);
			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
