<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>FACTURAS X PROVEEDOR</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> facturasxp.php</b></font></tr></td></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { $key = substr($user,2,strlen($user));echo "<form action='facturasxp.php' method=post>";

echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";if(!isset($v0) or !isset($v1)){ echo  "<center><table border=0>"; echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>"; echo "<tr><td colspan=2 align=center><b>FACTURAS X PROVEEDOR</b></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";}else{$query = "select Mendoc,Menfec,Mencco,Mendan,Mennit,Pronom,Menfac from ".$empresa."_000010,".$empresa."_000006 ";$query .=" where menfec >= '".$v0."' and menfec <= '".$v1."' ";$query .="     and mencon ='001' and mennit = pronit  ";$query .="     order by mennit,menfec";$err = mysql_query($query,$conex);$num = mysql_num_rows($err); echo "<table border=1>"; echo "<tr><td colspan=7 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>"; echo "<tr><td colspan=7 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>"; echo "<tr><td colspan=7 align=center><b>FACTURAS X PROVEEDOR</b></td></tr>"; echo "<tr><td colspan=7 align=center><b>ENTRE FECHAS</b></td></tr>"; echo "<tr>"; echo "<td bgcolor=#cccccc><b>NRO DOCUMENTO</b></td>"; echo "<td bgcolor=#cccccc><b>FECHA</b></td>"; echo "<td bgcolor=#cccccc><b>C.C. ORIGEN</b></td>"; echo "<td bgcolor=#cccccc><b>DOC. ANEXO</b></td>"; echo "<td bgcolor=#cccccc><b>NIT</b></td>"; echo "<td bgcolor=#cccccc><b>PROVEEDOR</b></td>"; echo "<td bgcolor=#cccccc><b>NRO. FACTURA</b></td>"; echo "</tr>"; $t=array();$t[0] = 0;$t[1] = 0;$t[2] = 0;$t[3] = 0;$t[4] = 0;$t[5] = 0;$t[6] = 0;for ($i=0;$i<$num;$i++){$row = mysql_fetch_array($err); echo "<tr>"; echo "<td>".$row[0]."</td>"; echo "<td>".$row[1]."</td>"; echo "<td>".$row[2]."</td>"; echo "<td>".$row[3]."</td>"; echo "<td>".$row[4]."</td>"; echo "<td>".$row[5]."</td>"; echo "<td>".$row[6]."</td>"; echo "</tr>"; } echo "</table>"; }}?></body></html>