<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		echo "<form action='000001_rc98B.php' method=post>";
		echo "<center><table border=1>";
		echo "<tr><td colspan=17 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
		echo "<tr><td colspan=17 align=center>DIRECCION DE INFORMATICA</td></tr>";
		echo "<tr><td colspan=17 align=center>COSTOS COMPARATIVOS X UNIDAD DE NEGOCIO</td></tr>";
		echo "<tr><td colspan=17 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " AÑO : ".$wanop."</td></tr>";
		$wdat=array();
		$wmeses=array();
		for ($i=$wper1;$i<=$wper2;$i++)
		{
			switch ($i)
				{
					case 1:
						$wmeses[$i]="ENERO";
						break;
					case 2:
						$wmeses[$i]="FEBRERO";
						break;
					case 3:
						$wmeses[$i]="MARZO";
						break;
					case 4:
						$wmeses[$i]="ABRIL";
						break;
					case 5:
						$wmeses[$i]="MAYO";
						break;
					case 6:
						$wmeses[$i]="JUNIO";
						break;
					case 7:
						$wmeses[$i]="JULIO";
						break;
					case 8:
						$wmeses[$i]="AGOSTO";
						break;
					case 9:
						$wmeses[$i]="SEPTIEMBRE";
						break;
					case 10:
						$wmeses[$i]="OCTUBRE";
						break;
					case 11:
						$wmeses[$i]="NOVIEMBRE";
						break;
					case 12:
						$wmeses[$i]="DICIEMBRE";
						break;
				}
		}
		echo "<tr><td><b>COD. PROCEDIMIENTO</b></td><td><b>NOM. PROCEDIMIENTO</b></td><td><b>% TERCERO</b></td><td><b>COSTO<BR>PROMEDIO</b></td><td><b>TMN</b></td>";
		for ($i=$wper1;$i<=$wper2;$i++)
			echo "<td align=center><b>".$wmeses[$i]."</b></td>";
		echo "</tr></table></center>";
	}
?>
</body>
</html>
