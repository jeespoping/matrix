<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>CIRUGIAS PROGRAMADAS CON MATERIAL</font></a></td></tr><tr><td align=center bgcolor="#cccccc"><font size=2> <b> Repcirmat.php</b></font></td></tr></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { $key = substr($user,2,strlen($user));

echo "<form action='Repcirmat.php' method=post>";if(!isset($v0) or !isset($v1)){ echo  "<center><table border=0>"; echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>"; echo "<tr><td colspan=2 align=center><b>CIRUGIAS PROGRAMADAS CON MATERIAL</b></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>Fecha Incial</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
 echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
 echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";}else{$query  = "select '1',Turtur,Turqui,Turhin,Turhfi,Turfec,Turdoc,Turnom,Tureps,Turcir,Turmed,Turequ,Turusg,Turcom from tcx_000011 ";
$query .= " where turfec between '".$v0."' and '".$v1."' ";
$query .= "   and Turmat = 'on' ";
$query .= "   and Turmok = 'off' ";
$query .= "   UNION ";
$query .= " select '2',Turtur,Turqui,Turhin,Turhfi,Turfec,Turdoc,Turnom,Tureps,Turcir,Turmed,Turequ,Turusg,Turcom from tcx_000011 ";
$query .= " where turfec between '".$v0."' and '".$v1."' ";
$query .= "   and Turmat = 'on' ";
$query .= "   and Turcom like '%Turno Cambiado%' ";
$query .= "   UNION ";
$query .= " select '3',Mcatur,Mcaqui,Mcahin,Mcahfi,Mcafec,Mcadoc,Mcanom,Mcaeps,Mcacir,Mcamed,Mcaequ,Mcausg,Mcacom from tcx_000007 ";
$query .= " where Mcafec between '".$v0."' and '".$v1."' ";
$query .= "   and Mcamat = 'on' ";
$query .= "   order by 6 ";
$err = mysql_query($query,$conex);$num = mysql_num_rows($err); echo "<table border=1>"; echo "<tr><td colspan=14 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>"; echo "<tr><td colspan=14 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>"; echo "<tr><td colspan=14 align=center><b>CIRUGIAS PROGRAMADAS CON MATERIAL</b></td></tr>"; echo "<tr><td colspan=14 align=center><b>X FECHA : ".$v0."</b></td></tr>"; echo "<tr>";
 echo "<td bgcolor=#cccccc><b>Tipo</b></td>"; echo "<td bgcolor=#cccccc><b>Codigo<BR>Turno</b></td>"; echo "<td bgcolor=#cccccc><b>Quirofano</b></td>"; echo "<td bgcolor=#cccccc><b>Hora<BR>Inicio</b></td>"; echo "<td bgcolor=#cccccc><b>Hora<BR>Final</b></td>"; echo "<td bgcolor=#cccccc><b>Fecha</b></td>"; echo "<td bgcolor=#cccccc><b>Documento</b></td>"; echo "<td bgcolor=#cccccc><b>Paciente</b></td>"; echo "<td bgcolor=#cccccc><b>Responsable</b></td>"; echo "<td bgcolor=#cccccc><b>Cirugias</b></td>"; echo "<td bgcolor=#cccccc><b>Medicos</b></td>"; echo "<td bgcolor=#cccccc><b>Equipos</b></td>"; echo "<td bgcolor=#cccccc><b>Usuario</b></td>"; echo "<td bgcolor=#cccccc><b>Comentario</b></td>"; echo "</tr>"; $t=array();$t[0] = 0;$t[1] = 0;$t[2] = 0;$t[3] = 0;$t[4] = 0;$t[5] = 0;$t[6] = 0;$t[7] = 0;$t[8] = 0;$t[9] = 0;$t[10] = 0;$t[11] = 0;$t[12] = 0;for ($i=0;$i<$num;$i++){$row = mysql_fetch_array($err); echo "<tr>"; echo "<td>".$row[0]."</td>"; echo "<td>".$row[1]."</td>"; echo "<td>".$row[2]."</td>"; echo "<td>".$row[3]."</td>"; echo "<td>".$row[4]."</td>"; echo "<td>".$row[5]."</td>"; echo "<td>".$row[6]."</td>"; echo "<td>".$row[7]."</td>"; echo "<td>".$row[8]."</td>"; echo "<td>".$row[9]."</td>"; echo "<td>".$row[10]."</td>"; echo "<td>".$row[11]."</td>"; echo "<td>".$row[12]."</td>";
 echo "<td>".$row[13]."</td>"; echo "</tr>"; } echo "</table>"; }}?></body></html>