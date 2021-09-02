<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
include_once("root/comun.php");
$wactualiz = "1";
$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
encabezado( "CIRUGIAS PROGRAMADAS X FECHA", $wactualiz, $institucion->baseDeDatos );
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
$key = substr($user,2,strlen($user));


echo "<form action='Repcirxfec.php' method=post>";
echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
if(!isset($v0))
{
 echo  "<center><table border=0>";
 //echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
 //echo "<tr><td colspan=2 align=center><b>CIRUGIAS PROGRAMADAS X FECHA</b></td></tr>";
 echo  "<tr><td bgcolor=#cccccc align=center>Fecha</td>";
 echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
}
else
{
$query = "select tcx_000011.Turtur,tcx_000011.Turqui,tcx_000011.Turhin,tcx_000011.Turhfi,tcx_000011.Turfec,tcx_000011.Turdoc,tcx_000011.Turnom,tcx_000011.Tureps,tcx_000011.Turcir,tcx_000011.Turmed,tcx_000011.Turequ,tcx_000011.Turusg,tcx_000011.Turcom from tcx_000011 where tcx_000011.turndt = '".$v0."' ";
$err = mysql_query($query,$conex);
$num = mysql_num_rows($err);
 echo "<table border=1>";
 //echo "<tr><td colspan=13 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
 //echo "<tr><td colspan=13 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
 //echo "<tr><td colspan=13 align=center><b>CIRUGIAS PROGRAMADAS X FECHA</b></td></tr>";
 echo "<tr><td colspan=13 align=center><b>X FECHA : ".$v0."</b></td></tr>";
 echo "<tr>";
 echo "<td bgcolor=#cccccc><b>Codigo<BR>Turno</b></td>";
 echo "<td bgcolor=#cccccc><b>Quirofano</b></td>";
 echo "<td bgcolor=#cccccc><b>Hora<BR>Inicio</b></td>";
 echo "<td bgcolor=#cccccc><b>Hora<BR>Final</b></td>";
 echo "<td bgcolor=#cccccc><b>Fecha</b></td>";
 echo "<td bgcolor=#cccccc><b>Documento</b></td>";
 echo "<td bgcolor=#cccccc><b>Paciente</b></td>";
 echo "<td bgcolor=#cccccc><b>Responsable</b></td>";
 echo "<td bgcolor=#cccccc><b>Cirugias</b></td>";
 echo "<td bgcolor=#cccccc><b>Medicos</b></td>";
 echo "<td bgcolor=#cccccc><b>Equipos</b></td>";
 echo "<td bgcolor=#cccccc><b>Usuario</b></td>";
 echo "<td bgcolor=#cccccc><b>Comentario</b></td>";
 echo "</tr>"; 
$t=array();
$t[0] = 0;
$t[1] = 0;
$t[2] = 0;
$t[3] = 0;
$t[4] = 0;
$t[5] = 0;
$t[6] = 0;
$t[7] = 0;
$t[8] = 0;
$t[9] = 0;
$t[10] = 0;
$t[11] = 0;
$t[12] = 0;
for ($i=0;$i<$num;$i++)
{
$row = mysql_fetch_array($err);
 echo "<tr>";
 echo "<td>".$row[0]."</td>";
 echo "<td>".$row[1]."</td>";
 echo "<td>".$row[2]."</td>";
 echo "<td>".$row[3]."</td>";
 echo "<td>".$row[4]."</td>";
 echo "<td>".$row[5]."</td>";
 echo "<td>".$row[6]."</td>";
 echo "<td>".$row[7]."</td>";
 echo "<td>".$row[8]."</td>";
 echo "<td>".$row[9]."</td>";
 echo "<td>".$row[10]."</td>";
 echo "<td>".$row[11]."</td>";
 echo "<td>".$row[12]."</td>";
 echo "</tr>"; 
}
 echo "</table>"; 
}
}
?>
</body>
</html>
