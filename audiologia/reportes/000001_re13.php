<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>TOTAL ACUFENOMETRIAS</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_re13.php</b></font></tr></td></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { $key = substr($user,2,strlen($user));

echo "<form action='000001_re13.php' method=post>";if(!isset($v0) or !isset($v1)){ echo  "<center><table border=0>"; echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>"; echo "<tr><td colspan=2 align=center><b>TOTAL ACUFENOMETRIAS</b></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";}else{$query = "select MONTHNAME(oir_000013.Fecha_Examen),count(*) from oir_000013 where oir_000013.fecha_examen >= '".$v0."' and oir_000013.fecha_examen <= '".$v1."' group by monthname(oir_000013.fecha_examen)  order by month(oir_000013.fecha_examen)";$err = mysql_query($query,$conex);$num = mysql_num_rows($err); echo "<table border=1>"; echo "<tr><td colspan=2 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>"; echo "<tr><td colspan=2 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>"; echo "<tr><td colspan=2 align=center><b>TOTAL ACUFENOMETRIAS</b></td></tr>"; echo "<tr><td colspan=2 align=center><b>POR MES</b></td></tr>"; echo "<tr>"; echo "<td bgcolor=#cccccc><b>MES</b></td>"; echo "<td align=right bgcolor=#cccccc><b>ACUFENOMETRIAS</b></td>"; echo "</tr>"; $t=array();$t[0] = 0;$t[1] = 0;for ($i=0;$i<$num;$i++){$row = mysql_fetch_array($err); echo "<tr>"; echo "<td>".$row[0]."</td>";$t[1]+=$row[1]; echo "<td align=right>".number_format($row[1],2,'.',',')."</td>"; echo "</tr>"; } echo "<tr><td  bgcolor=#FFCC66 colspan=2 align=center><b>TOTALES</b></td></tr>"; echo "<tr>"; echo "<td align=center bgcolor=#99CCFF><b> - </b></td>"; echo "<td bgcolor=#99CCFF align=right><b>".number_format($t[1],2,'.',',')."</b></td>"; echo "</tr>";  echo "</table>"; }}?></body></html>