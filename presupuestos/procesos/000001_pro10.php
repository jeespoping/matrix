<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro10.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesp) or !isset($warch))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE ARCHIVO PLANO COSTOS</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesp' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Ruta y Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='text' name='warch'  size=60 maxlength=60 ></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$query = "SELECT meccco,meccpr,mecmes,mecval from ".$empresa."_000026 ";
			$query = $query."  where mecano = ".$wanop;
    		$query = $query."    and mecmes = ".$wmesp;
   			$query = $query."  order by meccco,meccpr ";
   			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
   			if ($num>0)
			{
				$k=0;
				$file = fopen($warch,"w+");
		 		for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);	
					$registro=$row[0].",".$row[1].",".$row[2].",".$row[3].chr(13).chr(10);
  					 fwrite ($file,$registro);
  					$k++;
  				}
   				fclose ($file);
   				echo "NUMERO DE REGISTROS GRABADOS : ".$k."<br>";
            }
          }
}
?>
</body>
</html>
