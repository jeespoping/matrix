<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>RESULTADO DE ENCUESTA</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> encuesta.php</b></font></tr></td></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { $key = substr($user,2,strlen($user));

echo "<form action='encuesta.php' method=post>";if(!isset($v0) or !isset($v1) or !isset($v2)){ echo  "<center><table border=0>"; echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>"; echo "<tr><td colspan=2 align=center><b>RESULTADO DE ENCUESTA</b></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>A�O</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=4 maxlength=4></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>MES</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=2 maxlength=2></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>ENCUESTA</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v2' size=4 maxlength=4></td></tr>";echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";}else{$query = "select proceso_000007.Coduob,proceso_000008.Descripcion,proceso_000007.Codpre,sum(proceso_000007.Respuesta),count(*),sum(proceso_000007.Respuesta)/count(*) from proceso_000007,proceso_000008 ";$query .= "  where proceso_000007.ano = ".$v0;$query .= "  and proceso_000007.mes = ".$v1;$query .= "  and proceso_000007.codenc = ".$v2;$query .= "  and proceso_000007.Respuesta < 6 ";$query .= "  and proceso_000007.Coduob = proceso_000008.Codigo ";$query .= "  group by proceso_000007.coduob,proceso_000008.descripcion,proceso_000007.codpre  order by proceso_000007.coduob,proceso_000008.descripcion,proceso_000007.codpre";$err = mysql_query($query,$conex);$num = mysql_num_rows($err); echo "<table border=1>"; echo "<tr><td colspan=6 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>"; echo "<tr><td colspan=6 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>"; echo "<tr><td colspan=6 align=center><b>RESULTADO DE ENCUESTA</b></td></tr>"; echo "<tr><td colspan=6 align=center><b>Gestion de la Calidad</b></td></tr>"; echo "<tr>"; echo "<td bgcolor=#cccccc><b>Unidad</b></td>"; echo "<td bgcolor=#cccccc><b>Descripcion</b></td>"; echo "<td align=right bgcolor=#cccccc><b>Nro Pregunta</b></td>"; echo "<td align=right bgcolor=#cccccc><b>Total  x Pregunta</b></td>"; echo "<td align=right bgcolor=#cccccc><b>Nro Evaluaciones</b></td>"; echo "<td align=right bgcolor=#cccccc><b>Promedio</b></td>"; echo "</tr>"; $t=array();$t[0] = 0;$t[1] = 0;$t[2] = 0;$t[3] = 0;$t[4] = 0;$t[5] = 0;for ($i=0;$i<$num;$i++){$row = mysql_fetch_array($err); echo "<tr>"; echo "<td>".$row[0]."</td>"; echo "<td>".$row[1]."</td>"; echo "<td align=right>".number_format($row[2],2,'.',',')."</td>"; echo "<td align=right>".number_format($row[3],2,'.',',')."</td>"; echo "<td align=right>".number_format($row[4],2,'.',',')."</td>"; echo "<td align=right>".number_format($row[5],2,'.',',')."</td>"; echo "</tr>"; } echo "</table>"; }}?></body></html>