<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>TOTAL EXAMENES </font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_re14.php</b></font></tr></td></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { $key = substr($user,2,strlen($user));

echo "<form action='000001_re14.php' method=post>";if(!isset($v0) or !isset($v1)){ echo  "<center><table border=0>"; echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>"; echo "<tr><td colspan=2 align=center><b>TOTAL EXAMENES</b></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>"; echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>"; echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";}else{$wtable= date("YmdHis");$wtable=" temp_".$wtable;$query = "Create table  IF NOT EXISTS ".$wtable." as ";$query.= " select '1' as t,MONTH(oir_000002.Fecha_Examen) as i ,MONTHNAME(oir_000002.Fecha_Examen) as fecha,count(*) as k from oir_000002 ";$query.="   where oir_000002.fecha_examen >= '".$v0."' ";$query.="        and oir_000002.fecha_examen <= '".$v1."' ";$query.="     group by t,MONTH(oir_000002.Fecha_Examen),monthname(oir_000002.fecha_examen)  ";$query.="  union ";$query.= " select '2' as t,MONTH(oir_000008.Fecha_Examen) as i ,MONTHNAME(oir_000008.Fecha_Examen) as fecha,count(*) as k from oir_000008 ";$query.= "   where oir_000008.fecha_examen >= '".$v0."' ";$query.= "        and oir_000008.fecha_examen <= '".$v1."' ";$query.= "     group by t,MONTH(oir_000008.Fecha_Examen),monthname(oir_000008.fecha_examen) ";$query.="  union ";$query.= " select '3' as t,MONTH(oir_000009.Fecha_Examen) as i ,MONTHNAME(oir_000009.Fecha_Examen) as fecha,count(*) as k from oir_000009 ";$query.= "    where oir_000009.fecha_examen >= '".$v0."' ";$query.= "         and oir_000009.fecha_examen <= '".$v1."' ";$query.= "     group by t,MONTH(oir_000009.Fecha_Examen),monthname(oir_000009.fecha_examen) ";$query.="  union ";$query.= " select '4' as t,MONTH(oir_000010.Fecha_Examen) as i ,MONTHNAME(oir_000010.Fecha_Examen) as fecha,count(*) as k from oir_000010 ";$query.= "     where oir_000010.fecha_examen >= '".$v0."' ";$query.= "         and oir_000010.fecha_examen <= '".$v1."' ";$query.= "     group by t,MONTH(oir_000010.Fecha_Examen),monthname(oir_000010.fecha_examen) ";$query.="  union ";$query.= " select'5' as t,MONTH(oir_000013.Fecha_Examen) as i ,MONTHNAME(oir_000013.Fecha_Examen) as fecha,count(*) as k from oir_000013 ";$query.= "    where oir_000013.fecha_examen >= '".$v0."' ";$query.= "         and oir_000013.fecha_examen <= '".$v1."' ";$query.= "     group by t,MONTH(oir_000013.Fecha_Examen),monthname(oir_000013.fecha_examen) "; $query.="     order by month(fecha)";$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");$query = "select i,fecha,sum(k) from ".$wtable." group by i,fecha order by i ";$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");$num = mysql_num_rows($err); echo "<table border=1>"; echo "<tr><td colspan=2 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>"; echo "<tr><td colspan=2 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>"; echo "<tr><td colspan=2 align=center><b>TOTAL EXAMENES </b></td></tr>"; echo "<tr><td colspan=2 align=center><b>ENTRE FECHAS  ".$v0." - ".$v1."</b></td></tr>"; echo "<tr>"; echo "<td bgcolor=#cccccc><b>MES</b></td>"; echo "<td align=right bgcolor=#cccccc><b>EXAMENES</b></td>"; echo "</tr>"; $t=array();$t[0] = 0;$t[1] = 0;for ($i=0;$i<$num;$i++){$row = mysql_fetch_array($err); echo "<tr>"; echo "<td>".$row[1]."</td>";$t[1]+=$row[2]; echo "<td align=right>".number_format($row[2],2,'.',',')."</td>"; echo "</tr>"; } echo "<tr><td  bgcolor=#FFCC66 colspan=2 align=center><b>TOTALES</b></td></tr>"; echo "<tr>"; echo "<td align=center bgcolor=#99CCFF><b> - </b></td>"; echo "<td bgcolor=#99CCFF align=right><b>".number_format($t[1],2,'.',',')."</b></td>"; echo "</tr>";  echo "</table>";  $query = "DROP table ".$wtable; $err = mysql_query($query,$conex);}}?></body></html>