<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Insumos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro75.php Ver. 2011-05-19</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro75.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE CONVENIOS</td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION DE INSUMOS</td></tr>";
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
			$query = "SELECT ".$empresa."_000108.id, Moscco, Moscon, Empcin,Cfalin  from ".$empresa."_000108,".$empresa."_000061,".$empresa."_000060 ";
			$query = $query." where Mosano = ".$wanop;
			$query = $query."   and Mosmes = ".$wmesi;
			$query = $query."   and Mosent =  Epmcod ";
			$query = $query."   and Empeva =  'S' ";
			$query = $query."   and Moscon =  Cfacod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[2] != "2000" and $row[2] != "1017" and $row[2] != "4199")
					{
						if($row[2]  == "0001")
							$row[1] ="3081";
						if($row[2]  == "0167" and $row[1]  == "1016")
							$row[1] ="1188";
						if($row[2]  == "0167" and $row[1]  == "1191")
							$row[1] ="1188";
						if($row[2]  == "0167" and $row[1]  == "1330")
							$row[1] ="1180";
						if($row[2]  >= "2056" and $row[2]  <= "2059")
							$row[1] ="1016";
						if($row[2]  == "2044")
							$row[1] ="1187";
						if($row[2]  == "0035" and $row[1]  == "1191")
							$row[1] ="1188";
						if($row[2]  == "0117")
							$row[1] ="1250";
						if($row[2]  == "0128")
							$row[1] ="1320";
						if($row[2]  == "2062" or $row[2]  == "2066")
							$row[1] ="1251";
						if($row[1]  == "1189")
							$row[1] ="1185";
						if($row[1]  == "1051")
							$row[1] ="1050";
						if($row[1]  == "3051")
							$row[1] ="3050";
						if($row[1]  == "3060")
							$row[1] ="3050";
						if($row[1]  == "3061")
							$row[1] ="3050";
						if($row[1]  == "1192")
							$row[1] ="1195";
						if($row[1]  == "1240")
							$row[1] ="1241";
						if($row[2]  == "0035" and $row[1]  == "1016")
							$row[1] ="1186";
						if($row[2]  == "0167" and $row[1]  == "1191")
							$row[1] ="1188";
						if($row[2]  == "2009" and $row[1]  == "1016")
							$row[1] ="1020";
						if($row[2]  == "2061")
							$row[1] ="1016";
						if($row[2]  == "0122")
							$row[1] ="3080";
						if($row[2]  >= "0503" and $row[2]  <= "0515" and $row[2]  != "0511" and $row[1]  != "1031")
							$row[1] ="1030";
						$query = "SELECT ccoclas,Ccotip from ".$empresa."_000005 ";
						$query = $query." where ccocod = '".$row[1] ."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							if ( $row1[0] != "PR" and $row1[0] != "OGI")
								$row[1] ="1691";
						}
						$query = "update ".$empresa."_000108 set Moscco='".$row[1]."', Mosemp='".$row[3]."', Mostip='".$row1[1]."', Moslin='".$row[4]."'  where id=".$row[0];
               			$err2 = mysql_query($query,$conex);
               			$k++;
               			echo "REGISTROS ACTUALIZADO : ".$k."<br>";
           			}
               	}
			}
			echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
