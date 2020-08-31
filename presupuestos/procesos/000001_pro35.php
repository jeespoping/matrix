<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion Movimiento de Ingresos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro35.php Ver. 2015-09-11</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
function buscar_parametros($PAR,$num,$con,$cco)
{
	for ($i=0;$i<$num;$i++)
	{
		if($PAR[$i][0] > 0 and $PAR[$i][1] > 0 and $PAR[$i][0] == $cco and $PAR[$i][1] == $con)
			return $i;
		elseif($PAR[$i][0] == 0 and $PAR[$i][1] > 0 and $PAR[$i][1] == $con)
				return $i;
			elseif($PAR[$i][0] > 0 and $PAR[$i][1] == 0 and $PAR[$i][0] == $cco)
					return $i;
	}
	return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro35.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION MOVIMIENTO DE INGRESOS</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
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
			$k=0;
			$wemp = substr($wemp,0,2);
			#INICIO PROGRAMA
			$query = "DELETE from ".$empresa."_000063 ";
			$query = $query." where mioano = ".$wanop;
			$query = $query."   and miomes = ".$wmesi;
			$query = $query."   and mioemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			
			$query = "SELECT Parcci, Parcon, Parccf  from ".$empresa."_000146 ";
			$query = $query." where Parest = 'on' ";
			$query = $query."   and Paremp = '".$wemp."' ";
			$query = $query." Order by Parseg ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$PAR=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$PAR[$i][0] = $row[0];
					$PAR[$i][1] = $row[1];
					$PAR[$i][2] = $row[2];
				}
			}
			$numP = $num;
			$wtable= date("YmdHis");
			$wtable=" temp_".$wtable;
			$query = "create table IF NOT EXISTS ".$wtable." ( Medico varchar(8) not null,Fecha_data date not null ,Hora_data time not null ,mioano int not null,miomes int not null,mionit VARCHAR(80) not null,miocfa VARCHAR(80) not null,miocco VARCHAR(80) not null,miocla VARCHAR(80) not null,mioint Double not null,mioinp Double not null,mioito Double not null, Seguridad varchar(10) not null, id bigint not null auto_increment, primary key(id))";
			$err = mysql_query($query,$conex);
			$query = "SELECT mifano,mifmes,mifnit,mifcfa,mifcco,mifcla,sum(mifint),sum(mifinp),sum(mifito) from ".$empresa."_000062 ";
			$query = $query." where mifano = ".$wanop;
			$query = $query."   and mifmes = ".$wmesi;
			$query = $query."   and mifemp = '".$wemp."' ";
			$query = $query."   Group by mifano,mifmes,mifnit,mifcfa,mifcco,mifcla ";
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
						$query = $query." where epmcod = '".$row[2]."'";
						$query = $query."   and empemp = '".$wemp."' ";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] == 0)
							$row[2]="99";
						$pos = buscar_parametros($PAR,$numP,$row[3],$row[4]);
						if($pos != -1)
							$row[4] = $PAR[$pos][2];
/*
						if($row[3] == "0001")
							$row[4]="3081";
						if($row[3] == "2090")
							$row[4]="1016";
						if($row[3] == "2091")
							$row[4]="1016";
						if($row[3] == "2044")
							$row[4]="1190";
						if($row[3] == "0167" and $row[4] == "1016")
							$row[4]="1188";
						if($row[3] == "0167" and $row[4] == "1330")
							$row[4]="1180";
						if($row[3] >= "2056" and $row[3] <= "2059")
							$row[4]="1016";
						if($row[3] == "0607")
							$row[4]="1350";
						if($row[3] == "0117")
							$row[4]="1250";
						if($row[3] == "0128")
							$row[4]="1320";
						if($row[3] == "2062" or $row[3] == "2066")
							$row[4]="1135";
						if($row[4] == "1251")
							$row[4]="1135";
						if($row[4] == "1310")
							$row[4]="1300";
						if($row[4] == "1750")
							$row[4]="2240";
						if($row[4] == "1051")
							$row[4]="1050";
						if($row[4] == "3160")
							$row[4]="1135";
						if($row[4] == "3051")
							$row[4]="3050";
						if($row[4] == "3060")
							$row[4]="3050";
						if($row[4] == "3061")
							$row[4]="3050";
						if($row[4] == "3053" or $row[4] == "3054")
							$row[4]="1052";
						if($row[4] == "1192")
							$row[4]="1195";
						if($row[4] == "1380")
							$row[4]="1691";
						if($row[3] == "2025" and $row[4] == "1240")
							$row[4]="1241";
						if($row[4] == "1240")
							$row[4]="1691";
						if($row[4] == "1191")
							$row[4]="1017";
						if($row[4] == "1082")
							$row[4]="1691";
						if($row[3] == "0035" and $row[4] == "1016")
							$row[4]="1186";
						if($row[3] == "2009" and $row[4] == "1016")
							$row[4]="1020";
						if($row[3] == "0136" and $row[4] == "1195")
							$row[4]="1135";
						if($row[3] == "0139" and $row[4] == "1335")
							$row[4]="1336";
						if($row[3] == "2061")
							$row[4]="1016";
						if($row[3] == "0122")
							$row[4]="3080";
						if($row[3] >= "0503" and $row[3] < "0515" and $row[3] != "0511" and $row[4] != "1031" and $row[4] != "1032")
							$row[4]="1030";
*/
						$query = "SELECT ccoclas from ".$empresa."_000005 ";
						$query = $query." where ccocod = '".$row[4]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							if ( $row1[0] != "PR" and $row1[0] != "OGI")
								$row[4]="1691";
						}
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$wtable." (medico,fecha_data,hora_data,mioano,miomes,mionit,miocfa,miocco,miocla,mioint,mioinp,mioito,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."',".$row[6].",".$row[7].",".$row[8].",'C-".$empresa."')";
               			$err2 = mysql_query($query,$conex);
           			}
               	}
			}
			$query = "SELECT mioano,miomes,mionit,miocfa,miocco,miocla,sum(mioint),sum(mioinp),sum(mioito) from ".$wtable." ";
			$query = $query." where mioano = ".$wanop;
			$query = $query."   and miomes = ".$wmesi;
			$query = $query."   Group by mioano,miomes,mionit,miocfa,miocco,miocla ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000063 (medico,fecha_data,hora_data,mioemp,mioano,miomes,mionit,miocfa,miocco,miocla,mioint,mioinp,mioito,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmesi.",'".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."',".$row[6].",".$row[7].",".$row[8].",'C-".$empresa."')";
           			$err2 = mysql_query($query,$conex);
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
