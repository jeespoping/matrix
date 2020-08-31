 <html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
  	$key = substr($user,2,strlen($user));
  	

	

	echo date("YmdHis");
  	echo "<form action='etqcal.php' method=post>";
	echo "<center><font face='Tahoma'><table border=0>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/laboratorio/labmed.gif' ></td><tr>";
	echo "<tr><td align=center>LABORATORIO MEDICO</td><tr>";
	echo "<tr><td align=center><font size=1>Dg 75b Nº 2A - 120 Tel: 3419092 Medellin</font></td><tr>";
	echo "<tr><td align=center><b>BANCO DE SANGRE CODIGO O5-001-9</b></td><tr>";
	echo "<tr><td align=center><font size=5><b>Nº 35236</b></font></td><tr>";
	echo "<tr><td align=center><font size=1>REPUBLICA DE COLOMBIA</font></td><tr>";
	echo "<tr><td align=center><font size=1>MINISTERIO DE SALUD</font></td><tr>";
	echo "<tr><td align=center><font size=3 face='Tahoma bold'><b>SELLO NACIONAL DE CALIDAD DE SANGRE</b></td><tr>";
	echo "<tr><td><font size=2 face='Tahoma bold'><tt><b>".chr(34)."Esta  &nbsp unidad &nbsp ha &nbsp sido  &nbsp analizada &nbsp  para</b></tt></font></td><tr>";
	echo "<tr><td><font size=2 face='Tahoma bold'><tt><b>detectar Antígeno &nbsp contra el &nbsp virus de &nbsp la</b></tt></font></td><tr>";
	echo "<tr><td><font size=2 face='Tahoma bold'><tt><b>HEPATITIS B, anticuerpos  para  el  virus&nbsp de  la</b></tt></font></td><tr>";
	echo "<tr><td><font size=2 face='Tahoma bold'><tt><b>Inmunodeficiencia&nbsp  &nbsphumana&nbsp  -  &nbspVIH,  virus&nbsp  de</b></tt></font></td><tr>";
	echo "<tr><td><font size=2 face='Tahoma bold'><tt><b>HEPATITIS C,  Treponema&nbsp  Pallidum -  SIFILIS&nbsp  y</b></tt></font></td><tr>";
	echo "<tr><td><font size=2 face='Tahoma bold'><tt><b>Tripanozoma cruzi - CHAGAS, con resultados NO</tt></font></b></td><tr>";
	echo "<tr><td><font size=2 face='Tahoma bold'><tt><b>REACTIVOS.</b></td><tr>";
	echo "<tr><td><font size=2><tt><b><u>Puede&nbsp  ser&nbsp&nbsp  utilizada.  Su&nbsp  aplicacion&nbsp   puede</b></tt></font></td><tr>";
	echo "<tr><td><font size=2><tt><b><u>ocasionar efectos no previsibles</b>".chr(34)."</tt></font></td><tr></table>";
}
?>
</body>
</html>