<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion Estancia Hospitalaria</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro84.php Ver. 1.01</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro84.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE CONVENIOS</td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION ESTANCIA HOSPITALARIA</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$k=0;
			#INICIO PROGRAMA
			$query = "SELECT Motano,Motmes,Motcco,Motemp,sum(Mhoest*Pcapro)  from ".$empresa."_000097,".$empresa."_000109,".$empresa."_000113 ";
			$query = $query." where Motano = ".$wanop;
			$query = $query."   and Motmes = ".$wmesi;
			$query = $query."   and Motlin =  '3' ";
			$query = $query."   and Motano =  Pcaano";
			$query = $query."   and Motmes =  Pcames";
			$query = $query."   and Motcco =  Pcacco";
			$query = $query."   and Pcaent =  '999' ";
			$query = $query."   and Motano =  Mhoano";
			$query = $query."   and Motmes =  Mhomes";
			$query = $query."   and Motcco =  Mhocco";
			$query = $query."   and Motemp =  Mhoent";
			$query = $query."   and Pcacod =  Mhohab";
			$query = $query."   group by Motano,Motmes,Motcco,Motemp ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "update ".$empresa."_000109 set Motcos=".$row[4]."  where Motano=".$row[0]." and Motmes=".$row[1]." and Motcco='".$row[2]."' and Motemp='".$row[3]."' and Motlin='3' ";
           			$err2 = mysql_query($query,$conex);
           			$k++;
           			echo "REGISTROS ACTUALIZADO : ".$k."<br>";
               	}
			}
			echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
