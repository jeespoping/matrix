<html>
<head>
  	<title>MATRIX</title>
  	
  	 <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>
  	 
</head>
<body>
<BODY>
<?php
include_once("conex.php");

session_start();
if (!isset($_SESSION['user']))
    echo "error";
else
{
    $key = substr($user, 2, strlen($user));
    echo "<form name='Mov2' action='Mov2.php' method=post>";
    

    


    $query = "  SELECT  plopro, Artcom, plocod, plofcr, plofve, plocin, plosal, ploela, descripcion  from " . $empresa . "_000002, " . $empresa . "_000004, usuarios  ";
    $query .= "    where  plosal > 0 ";
    $query .= "     and   Plofve < '" . date('Y-m-d') . "' ";
    $query .= "     and   Plopro = Artcod ";
    $query .= "    and   Ploest = 'on' ";
    $query .= "     and   ploela = codigo order by 1, 3 ";

    $err = mysql_query($query, $conex) or die (mysql_errno() . ":" . mysql_error());
    $num = mysql_num_rows($err);

    echo "<table border=0 align=center>";
    echo "<tr><td class='titulo1'><b>REPORTE DE PRODUCTOS VENCIDOS</font> Ver 1.0</b></font></td></tr>";
    echo "</tr></table><br><br>";

    echo "<table border=0 align=center>";
    echo "<tr><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>PRODUCTO</b></font></td>";
    echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>LOTE</b></font></td>";
    echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>FECHA DE CREACION</b></font></td>";
    echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>FECHA DE VENCIMIENTO</b></font></td>";
    echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD INCIAL</b></font></td>";
    echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>SALDO</b></font></td>";
    echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>ELABORO</b></font></td>";

    for ($i = 0;$i < $num;$i++)
    {
        $row = mysql_fetch_array($err);

        if (is_integer($i / 2))
            $wcolor = "#ffffcc";
        else
            $wcolor = "FFFFFF";
        echo "<tr>";
        echo "<td bgcolor='" . $wcolor . "' align=left><font face='tahoma' size=2><b>" . $row[0] . "-" . $row[1] . "</b></font></td>";
        echo "<td bgcolor='" . $wcolor . "' align=left><font face='tahoma' size=2><b>" . $row[2] . "</b></font></td>";
        echo "<td bgcolor='" . $wcolor . "' align=left><font face='tahoma' size=2><b>" . $row[3] . "</b></font></td>";
        echo "<td bgcolor='" . $wcolor . "' align=left><font face='tahoma' size=2><b>" . $row[4] . "</b></font></td>";
        echo "<td bgcolor='" . $wcolor . "' align=left><font face='tahoma' size=2><b>" . $row[5] . "</b></font></td>";
        echo "<td bgcolor='" . $wcolor . "' align=left><font face='tahoma' size=2><b>" . $row[6] . "</b></font></td>";
        echo "<td bgcolor='" . $wcolor . "' align=left><font face='tahoma' size=2><b>" . $row[7] . "-" . $row[8] . "</b></font></td>";
        echo "</tr>";
    } 
    echo"</table>";
} 

?>
</body>
</html>