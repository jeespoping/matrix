<html>
<head>
<title>Orden Unidad Visual</title>
<style type="text/css">
BODY           
{   
    font-family: Verdana;
    font-size: 10pt;
    margin: 0px;
}
</style>
</body>
</html>

<?php
include_once("conex.php");
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inaviñitado el teclado.
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                           IMPRESION DE LA ORDEN DE LA UNIDAD VISUAL GLOBAL                                               *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Impresión orden.                                                                                             |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Febrero 15 DE 2008.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  :29 de Julio de 2008.                                                                                         |
//DESCRIPCION			      :Este programa sirve para imprimir la orden ingresada en la unidad visual global.                             |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000041       : Tabla de Pacientes.                                                                                                   | 
//uvglobal_000001   : Tabla Maestro de articulos.                                                                                           | 
//uvglobal_000133   : Tabla donde se ingresa la orden del laboratorio.                                                                      |
//uvglobal_000085   : Tabla del profesional.                                                                                                |
//uvglobal_000041   : Tabla de Factura.                                                                                                     |
//==========================================================================================================================================
$wactualiz="Ver. 2008-07-31";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
 

 

 

 $query = "Select Cjecco From uvglobal_000030 Where Cjeusu = '".substr($user,2,80)."'";
 $resultado = mysql_query($query);
 $registro = mysql_fetch_row($resultado);  
 $sede = $registro[0];

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION

$q = " CREATE TEMPORARY TABLE if not exists tempo2 as "
    ." SELECT ordfec,ordnro,orddoc,uvglobal_000041.clinom,uvglobal_000041.clite1,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,"
    ."        ordisi,ordies,ordici,ordiej,ordiad,ordite,ordled,artnom as nomled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,ordpin,"
    ."        ordde1,ordbra,ordde2,ordter,ordde3,ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordfac,ordvel,ordvem,uvglobal_000018.fenval,uvglobal_000018.fensal,fdenve"
    ."   FROM uvglobal_000041,uvglobal_000018,uvglobal_000019,uvglobal_000133 left join uvglobal_000001"
    ."     ON ordled = artcod "
    ."  WHERE ordnro = $wnro"
	."    AND orddoc = clidoc"
	."    AND ordfac = fenfac"
	."    AND fenffa = fdeffa"
	."    AND fenfac = fdefac";
//	."    AND fdenve = vmpvta";
	    
$res = mysql_query($q, $conex) or die("ERROR EN QUERY");

$q1 =" CREATE TEMPORARY TABLE if not exists tempo3 as "
    ." SELECT ordfec,ordnro,orddoc,clinom,clite1,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,ordled,"
    ."        nomled,ordlei,artnom as nomlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,ordpin,"
    ."        ordde1,ordbra,ordde2,ordter,ordde3,ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordfac,ordvel,ordvem,fenval,fensal,fdenve"
    ."   FROM tempo2 LEFT JOIN uvglobal_000001"
    ."     ON ordlei = artcod"; 
    
$res = mysql_query($q1, $conex) or die("ERROR EN QUERY temporal 2");

//echo $q1."<br>";

$query2 =" SELECT ordfec,ordnro,orddoc,clinom,clite1,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,ordled,"
        ."        nomled,ordlei,nomlei,ordedp,ordtra,ordbif,ordmon,ordref,artnom as nomref,ordmet,ordcom,ordcol,ordpin,"
        ."        ordde1,ordbra,ordde2,ordter,ordde3,ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordfac,ordvel,ordvem,fenval,fensal,fdenve"
        ."   FROM tempo3 LEFT JOIN uvglobal_000001"
        ."     ON ordref = artcod";
   
//echo $query2."<br>";
        
//echo mysql_errno() ."=". mysql_error();
	         
$err2 = mysql_query($query2,$conex);
$num2 = mysql_num_rows($err2);
$row2 = mysql_fetch_array($err2);


$fec=explode('-',$row2[0]);


$fecano=$fec[0];
$fecmes=$fec[1];
$fecdia=$fec[2];
   
$tipusu='';
switch ($row2[6])
{
 case "1":
 {
   $tipusu="Beneficiario";
   break;
 }  
 case "2":
 {
   $tipusu="Cotizante";
   break;
 }
 case "3":
 {
   $tipusu="Particular";
   break;
 }
 case "4":
 {
   $tipusu="Prepagada";
   break;
 }
}

//signo de la esfera
if ($row2[7]=='' &&  ($row2[8] != "N" && !empty($row2[8]) ) )
{
	$row2[7]='+';
}

if( $row2[9] > 0  ){
	$row2[9] = "-&nbsp;{$row2[9]}";
}
else{
	$row2[9] = "";
}

if ($row2[13]=='' && ($row2[14] != "N" && !empty($row2[14]) ) )
{
	$row2[13]='+';
}

if( $row2[15] > 0  ){
	$row2[15] = "-&nbsp;{$row2[15]}";
}
else{
	$row2[15] = "";
}

$tipo='';
switch ($row2[12])
{
 case "1":
 {
   $tipo="Terminado";
   break;
 }  
 case "2":
 {
   $tipo="Tallado";
   break;
 }
}

$tipoi='';
switch ($row2[18])
{
 case "1":
 {
   $tipoi="Terminado";
   break;
 }  
 case "2":
 {
   $tipoi="Tallado";
   break;
 }
}

$montur='';
switch ($row2[26])
{
 case "1":
 {
   $montur="Montura Propia.";
   break;
 }  
 case "2":
 {
   $montur="Montura U.V.G.";
   break;
 }
 case "3":
 {
   $montur="Solo Lentes.";
   break;
 }
}

$material='';
switch ($row2[29])
{
 case "1":
 {
   $material="Metal.";
   break;
 }  
 case "2":
 {
   $material="Pasta.";
   break;
 }
 case "3":
 {
   $material="Otro.";
   break;
 }
}


$dise='';
switch ($row2[30])
{
 case "1":
 {
   $dise="Completa.";
   break;
 }  
 case "2":
 {
   $dise="Sem AA.";
   break;
 }
 case "3":
 {
   $dise="AA.";
   break;
 }
}

$buenp='';
$malp='';
switch ($row2[32])
{
 case "1":
 {
   $buenp="X";
   break;
 }  
 case "2":
 {
   $malp="X";
   break;
 }
}

$buenb='';
$malb='';
switch ($row2[34])
{
 case "1":
 {
   $buenb="X";
   break;
 }  
 case "2":
 {
   $malb="X";
   break;
 }
}

$buent='';
$malt='';
switch ($row2[36])
{
 case "1":
 {
   $buent="X";
   break;
 }  
 case "2":
 {
   $malt="X";
   break;
 }
}

$buenpl='';
$malpl='';
switch ($row2[38])
{
 case "1":
 {
   $buenpl="X";
   break;
 }  
 case "2":
 {
   $malpl="X";
   break;
 }
}


$bueno='';
$malo='';
switch ($row2[40])
{
 case "1":
 {
   $bueno="X";
   break;
 }  
 case "2":
 {
   $malo="X";
   break;
 }
}


echo "<table border=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
echo "<tr>";
echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='1' color='#000000'><b>ORDEN LABORATORIO V.M.</b></font><br>";
echo "<font size='1' color='#000000'><b>".$sede."</b></font><br>";
echo "<font size='1' color='#000000'><b>NIT. 811.017.919-1</b></font><br>";
echo "<font size='1' color='#000000'><b>PBX: 4 44 00 15</b></font></td>";
echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='1' color='#000000'><b>FECHA:<i>".$fecdia."&nbsp;&nbsp;/&nbsp;&nbsp;".$fecmes."&nbsp;&nbsp;/&nbsp;&nbsp;".$fecano."</b></font></td>";
echo "</tr>";
echo "</table>";
   
echo "<table border=0>";	
echo "<tr>";
echo "<tr><td>&nbsp;</td></tr>";
echo "<tr><td>&nbsp;</td></tr>";
echo "<tr><td>&nbsp;</td></tr>";
echo "<tr><td>&nbsp;</td></tr>";
echo "<td align=LEFT bgcolor=#FFFFFF width=50><font size='1' color='#000000'><b>DOC.IDENTIDAD:</b></font></td>";  
echo "<td  bgcolor=#FFFFFF width=50><font size=1>&nbsp;$row2[2]</font></td>";
echo "<td align=LEFT bgcolor=#FFFFFF width=25><font size='1' color='#000000'><b>NOMBRE:</b></font></td>"; 
echo "<td  bgcolor=#FFFFFF align=center width=150><font size=1>&nbsp;$row2[3]</font></td>";
echo "<td align=LEFT bgcolor=#FFFFFF width=50><font size='1' color='#000000'><b>TELÉFONO:</b></font></td>"; 
echo "<td  bgcolor=#FFFFFF align=center width=50><font size=1>&nbsp;$row2[4]</font></td>";
echo "<td align=LEFT bgcolor=#FFFFFF width=10><font size='1' color='#000000'><b>RANGO:</b></font></td>"; 
echo "<td  bgcolor=#FFFFFF align=center width=10><font size=1>&nbsp;$row2[5]</font></td>";
echo "</tr>";

echo "<tr>";
echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>CLASIFICACIÓN:</b></font></td>";  
echo "<td  bgcolor=#FFFFFF ><font size=1>&nbsp;$tipusu</font></td>";
echo "<td align=LEFT bgcolor=#FFFFFF ><font size='1' color='#000000'><b>CAJA:</b></font></td>";  
echo "<td  bgcolor=#FFFFFF align=center ><font size=1>&nbsp;$row2[43]</font></td>";
echo "<td align=LEFT bgcolor=#FFFFFF ><font size='1' color='#000000'><b>ORDEN:</b></font></td>";  
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$wnro</font></td>";
echo "</tr>";
echo "</table>";

echo "<table border=0>";	
echo "<tr>";
echo "<td aling=left bgcolor=#FFFFFF><font size='1' color='#000000'><b>LENTES:</b></td>";
echo "</tr>";
echo "</table>";


echo "<table border=1 cellpadding='0' cellspacing='0' size='552'>";
echo "<tr>";
echo "<td bgcolor='#FFFFFF'align=center width=2><font text color=#000000 size=1></td>";
echo "<td bgcolor='#FFFFFF'align=center width=110><font text color=#000000 size=1>ESFERA</td>";
echo "<td bgcolor='#FFFFFF'align=center width=110><font text color=#000000 size=1>CILINDRO</td>";
echo "<td bgcolor='#FFFFFF'align=center width=110><font text color=#000000 size=1>EJE</td>";
echo "<td bgcolor='#FFFFFF'align=center width=110><font text color=#000000 size=1>ADD</td>";
echo "<td bgcolor='#FFFFFF'align=center width=110><font text color=#000000 size=1>TIPO</td>";
echo "</tr>";

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>O.D.</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[7]&nbsp;$row2[8]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[9]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[10]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>+&nbsp;$row2[11]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$tipo</font></td>";
echo "</tr >"; 

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>O.I.</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[13]&nbsp;$row2[14]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[15]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[16]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>+&nbsp;$row2[17]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$tipoi</font></td>";
echo "</tr >"; 
echo "</table>";


echo "<table border=0 size=80%>";
echo "<Tr >";
echo "<td  bgcolor=#FFFFFF ><font size=1 color='#000000'><b>Cod. Lente OD : </b></font></td>";
echo "<td  bgcolor=#FFFFFF ><font size=1><b>&nbsp;$row2[19]</b></font></td>";
echo "<td  bgcolor=#FFFFFF ><font size=1>&nbsp;$row2[20]</font></td>";
echo "</tr>";

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF ><font size=1 color='#000000'><b>Cod. Lente OI : </b></font></td>";
echo "<td  bgcolor=#FFFFFF ><font size=1><b>&nbsp;$row2[21]</b></font></td>";
echo "<td  bgcolor=#FFFFFF ><font size=1>&nbsp;$row2[22]</font></td>";
echo "</tr>";
echo "</table>";

echo "<table border=0 size=80%>";
echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=LEFT><font size=1 color='#000000'><b>Vendedor Lente : </b></font></td>";
echo "<td  bgcolor=#FFFFFF align=LEFT><font size=1>&nbsp;$row2[45]</font></td>";
echo "</tr>";
echo "</table>";

echo "<table border='1' cellpadding='0' cellspacing='0'>";
echo "<Tr >";
echo "<td bgcolor='#FFFFFF'align=center width=20%><font size=1 text color='#000000'><b>D.P. </b></font></td>";
echo "<td bgcolor='#FFFFFF'align=center width=30%><font size=1 text color='#000000'><b>Tratamiento: </b></font></td>";
echo "<td bgcolor='#FFFFFF'align=center width=30%><font size=1 text color='#000000'><b>Altura Bifocal en mm. </b></font></td>";
echo "</Tr >";

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[23]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[24]</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[25]</font></td>";
echo "</Tr >"; 
echo "</table>";

echo "<table border=0 >";
echo "<Tr >";
echo "<td bgcolor=#FFFFFF align=LEFT width=10%><font size='1' color='#000000'><b>Montura:</b></font></td>";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1'>&nbsp;$row2[27]</font></td>";
echo "<td bgcolor=#FFFFFF align=LEFT width=50%><font size='1'>&nbsp;$row2[28]</font></td>";
echo "</tr>";
echo "</table>";

echo "<table border=0 >";
echo "<Tr >";
echo "<td bgcolor=#FFFFFF ><font size='1' color='#000000'><b>Vend.Montura: </b></font></td>";   
echo "<td bgcolor=#FFFFFF ><font size='1'>&nbsp;$row2[46]</font></td>";
echo "</tr>";
echo "</table>";

echo "<table border=0 size=80%>";
echo "<Tr >";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1' color='#000000'><b>Clase Montura:</b></font></td>";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1'>&nbsp;$montur</font></td>";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1' color='#000000'><b>Material:</b></font></td>";   
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1'>&nbsp;$material</font></td>";
echo "</Tr >";
echo "</table>";

echo "<table border=0 size=80%>";
echo "<Tr >";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1' color='#000000'><b>Diseño:</b></font></td>";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1'>&nbsp;$dise</font></td>";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1' color='#000000'><b>Color:</b></font></td>";
echo "<td bgcolor=#FFFFFF align=LEFT width=20%><font size='1'>&nbsp;$row2[31]</font></td>";
echo "</Tr >";
echo "</table>";

echo "<table border='1' cellpadding='0' cellspacing='0'>";
echo "<Tr >";
echo "<td bgcolor='#FFFFFF' align=center width=11%><font size=1 text color=#000000><b>ESTADO</b></font></td>";
echo "<td bgcolor='#FFFFFF' align=center width=10%><font size=1 text color=#000000><b>BUENO</b></font></td>";
echo "<td bgcolor='#FFFFFF' align=center width=10%><font size=1 text color=#000000><b>MALO</b></font></td>";
echo "<td bgcolor='#FFFFFF' align=center width=49%><font size=1 text color=#000000><b>DESCRIPCION</b></font></td>";
echo "</Tr >";

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>Pintura</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$buenp</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$malp</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[33]</font></td>";
echo "<Tr >"; 

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>Brazos</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$buenb</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$malb</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[35]</font></td>";
echo "<Tr >"; 

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>Terminales</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$buent</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$malt</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[37]</font></td>";
echo "<Tr >"; 

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>Plaquetas</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$buenpl</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$malpl</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[39]</font></td>";
echo "<Tr >"; 

echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>Otro</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$bueno</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$malo</font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;$row2[41]</font></td>";
echo "<Tr >"; 
echo "</table>";

echo "<table border=0 >";
echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=left width=15%><font size=1><b>Observaciones : </b></font></td>";
echo "<td  bgcolor=#FFFFFF align=left width=65%><font size=1>&nbsp;$row2[42]</font></td>";
echo "<Tr >"; 
echo "</table>";

echo "<table border=0 >";
echo "<Tr >";
echo "<td  bgcolor=#FFFFFF width=14%><font size=1><b>Nro.Factura:</b></font></td>";
echo "<td  bgcolor=#FFFFFF width=12%><font size=1>$row2[44]</font></td>";
echo "<td  bgcolor=#FFFFFF width=12%><font size=1><b>Vlr.Factura:</b></font></td>";
echo "<td  bgcolor=#FFFFFF width=15%><font size=1>".number_format($row2[47],0,'.',',')."</font></td>";
echo "<td  bgcolor=#FFFFFF width=12%><font size=1><b>Vlr.Saldo:</b></font></td>";
echo "<td  bgcolor=#FFFFFF width=15%><font size=1>".number_format($row2[48],0,'.',',')."</font></td>";
echo "<Tr >"; 
echo "</table>";

$q="SELECT vmpmed"
  ."  FROM uvglobal_000050"
  ." WHERE vmpvta='".$row2[49]."'";

$err1 = mysql_query($q,$conex);
$num1 = mysql_num_rows($err1);
$row1 = mysql_fetch_array($err1);

$medi='';
if ($num1>0)
{
$medi=explode('-',$row1[0]);
echo "<table border=0 >";
echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>Dr(a): </b></font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>$medi[1]</font></td>";
echo "<Tr >"; 

echo "</table>"; 

}
else 
{
$medi='SIN MEDICO';
echo "<table border=0 >";
echo "<Tr >";
echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>Dr(a): </b></font></td>";
echo "<td  bgcolor=#FFFFFF align=center><font size=1>$medi</font></td>";
echo "<Tr >"; 

echo "</table>"; 

}

}

?>

