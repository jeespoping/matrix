<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
	<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion Movimiento Operativo Presupuestado</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro16.php Ver. 2011-10-20</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro16.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi) or !isset($wmesf))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION MOVIMIENTO OPERATIVO PRESUPUESTADO</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$k=0;
			#INICIO PROGRAMA
			$query = "DELETE from ".$empresa."_000031 ";
			$query = $query." where mopano = ".$wanop;
			$query = $query."   and mopmes >= ".$wmesi;
			$query = $query."   and mopmes <= ".$wmesf;
			$err = mysql_query($query,$conex);

			#UNIDADES AMBULATORIAS
			$query = "SELECT inlcco,inllin,inlper,inlprp,lindes from ".$empresa."_000017,".$empresa."_000021 ";
			$query = $query." where inlano = ".$wanop;
			$query = $query."    and inllin = lincod ";
			$query = $query."  order by inlcco,inlper";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$k++;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,mopano,mopmes,mopcco,moplin,mopcan,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row1[2].",'".$row1[0]."','".$row1[4]."',".$row1[3].",'C-".$empresa."')";
               		$err2 = mysql_query($query,$conex);
               	}
			}
			#UNIDADES QUIRURGICAS
			$query = "SELECT mcicco,mcicir,mciper,mcican,cirdes from ".$empresa."_000023,".$empresa."_000007 ";
			$query = $query." where mciano = ".$wanop;
			$query = $query."    and mcicir = circod ";
			$query = $query."  order by mcicco,mciper";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$k++;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,mopano,mopmes,mopcco,moplin,mopcan,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row1[2].",'".$row1[0]."','".$row1[4]."',".$row1[3].",'C-".$empresa."')";
               		$err2 = mysql_query($query,$conex);
               	}
			}
			#UNIDADES HOSPITALARIAS
			$query = "SELECT tcacco,caeper,perdia,tcaind,sum(caecan) as cantidad from ".$empresa."_000046,".$empresa."_000003,".$empresa."_000040 ";
			$query = $query." where tcaano = ".$wanop;
			$query = $query."    and tcacco = caecco ";
			$query = $query."    and tcaano = caeano ";
			$query = $query."    and caeper = perper ";
			$query = $query."    and tcacam <> 'C1' ";
			$query = $query."  group by tcacco,caeper,perdia,tcaind ";
			$query = $query."  order by tcacco,caeper,perdia,tcaind ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$k++;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,mopano,mopmes,mopcco,moplin,mopcan,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row1[1].",'".$row1[0]."','NUMERO DE CAMAS',".$row1[4].",'C-".$empresa."')";
					$err2 = mysql_query($query,$conex);
					$k++;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,mopano,mopmes,mopcco,moplin,mopcan,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row1[1].",'".$row1[0]."','INDICE DE OCUPACION EN %',".$row1[3].",'C-".$empresa."')";
               		$err2 = mysql_query($query,$conex);
               		$k++;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wmon=$row1[4] * $row1[2] * ($row1[3] / 100);
					$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,mopano,mopmes,mopcco,moplin,mopcan,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row1[1].",'".$row1[0]."','DIAS CAMA',".$wmon.",'C-".$empresa."')";
               		$err2 = mysql_query($query,$conex);
               		
               	}
			}
			echo "NUMERO DE REGISTROS INSERTADOS : ".$k."<br>";
        }
}		
?>
</body>
</html>
