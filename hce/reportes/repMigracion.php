<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Titulo reporte prueba migracion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> repMigracion.php</b></font></tr></td></table>
</center>
<?php
 session_start();
 if(!$_SESSION["user"])
 echo "error";
 else
 { 
$key = substr($user,2,strlen($user));
include_once("conex.php");
include_once("root/comun.php");

$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");


mysql_select_db("matrix");
echo "<form action='repMigracion.php?wemp_pmla=".$wemp_pmla."' method=post>";
$query = "select ".$whce."_000001.Encpro,".$whce."_000001.Encdes from ".$whce."_000001";
$err = mysql_query($query,$conex);
$num = mysql_num_rows($err);
 echo "<table border=1>";
 echo "<tr><td colspan=2 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
 echo "<tr><td colspan=2 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
 echo "<tr><td colspan=2 align=center><b>Titulo reporte prueba migracion</b></td></tr>";
 echo "<tr><td colspan=2 align=center><b>Subtitulo reporte prueba migracion</b></td></tr>";
 echo "<tr>";
 echo "<td bgcolor=#cccccc><b>Consecutivo</b></td>";
 echo "<td bgcolor=#cccccc><b>Descripcion</b></td>";
 echo "</tr>"; 
$t=array();
$t[0] = 0;
$t[1] = 0;
for ($i=0;$i<$num;$i++)
{
$row = mysql_fetch_array($err);
 echo "<tr>";
 echo "<td>".$row[0]."</td>";
 echo "<td>".$row[1]."</td>";
 echo "</tr>"; 
}
 echo "</table>"; 
}
?>
</body>
</html>
