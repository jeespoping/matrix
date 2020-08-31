<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Reclasificacion Movimiento  de Insumos Convenios (Origen)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro77.php Ver. 2011-05-19</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro77.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE CONVENIOS</td></tr>";
			echo "<tr><td align=center colspan=2>RECLASIFICACION MOVIMIENTO  DE INSUMOS CONVENIOS (ORIGEN)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Ano de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$k=0;
			#INICIO PROGRAMA
			$query = "DELETE from ".$empresa."_000111 ";
			$query = $query." where Mitano = ".$wanop;
			$query = $query."   and Mitmes = ".$wmesi;
			$err = mysql_query($query,$conex);
			$wtable= date("YmdHis");
			$wtable=" temp_".$wtable;
			$query = "create table IF NOT EXISTS ".$wtable." ( Medico varchar(8) not null,Fecha_data date not null ,Hora_data time not null ,Mitano int not null,Mitmes int not null,Mitent VARCHAR(80) not null,Mitcco VARCHAR(80) not null,Mitins VARCHAR(80) not null,Mitcan Double not null, Seguridad varchar(10) not null, id bigint not null auto_increment, primary key(id))";
			$err = mysql_query($query,$conex);
			$query = " SELECT Misano, Mismes, Empcin, Miscco, Misins, SUM(Miscan)   from ".$empresa."_000110,".$empresa."_000061 ";
			$query = $query." where Misano = ".$wanop;
			$query = $query."   and Mismes = ".$wmesi;
			$query = $query."   and Misent =  Epmcod ";
			$query = $query."   and Empeva =  'S' ";
			$query = $query."   Group By Misano, Mismes, Empcin, Miscco, Misins ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[3] == "1251")
						$row[3]="1135";
					if($row[3] == "1310")
						$row[3]="1300";
					if($row[3] == "1750")
						$row[3]="2240";
					if($row[3] == "1051")
						$row[3]="1050";
					if($row[3] == "3160")
						$row[3]="1135";
					if($row[3] == "3051")
						$row[3]="3050";
					if($row[3] == "3060")
						$row[3]="3050";
					if($row[3] == "3061")
						$row[3]="3050";
					if($row[3] == "3053" or $row[3] == "3054")
						$row[3]="1052";
					if($row[3] == "1192")
						$row[3]="1195";
					if($row[3] == "1380")
						$row[3]="1691";
					if($row[3] == "1240")
						$row[3]="1691";
					$query = "SELECT ccoclas,Ccotip from ".$empresa."_000005 ";
					$query = $query." where ccocod = '".$row[3] ."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						if ( $row1[0] != "PR" and $row1[0] != "OGI")
							$row[3] ="1691";
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$wtable." (medico,fecha_data,hora_data, Mitano, Mitmes, Mitent, Mitcco, Mitins, Mitcan, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'".$row[2]."','".$row[3]."','".$row[4]."',".$row[5].",'C-".$empresa."')";
           			$err2 = mysql_query($query,$conex);
           			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
               	}
               	$query = "SELECT Mitano, Mitmes, Mitent, Mitcco, Mitins, SUM(Mitcan) from ".$wtable." ";
				$query = $query." where Mitano = ".$wanop;
				$query = $query."   and Mitmes = ".$wmesi;
				$query = $query."   Group by Mitano, Mitmes, Mitent, Mitcco, Mitins ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000111 (medico,fecha_data,hora_data, Mitano, Mitmes, Mitent, Mitcco, Mitins, Mitcan, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'".$row[2]."','".$row[3]."','".$row[4]."',".$row[5].",'C-".$empresa."')";
	           			$err2 = mysql_query($query,$conex);
	           			$k++;
	           			echo "REGISTROS INSERTADOS : ".$k."<br>";
	       			}
	   			}
	   			$query = "DROP table ".$wtable;
				$err = mysql_query($query,$conex);
			}
			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
