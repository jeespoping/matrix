<html>
<head>
  	<title>MATRIX Movimiento de Articulos Ver. 2009-01-06</title>
  	 <style type="text/css">
    	#tipoG00{color:#000066;background:#000066;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;vertical-align:top;height:8em;}
    	#tipoG02{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;vertical-align:top;height:8em;}
    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='impmov' action='Impmov.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if (!isset($doc) or !isset($con)) 
	{
		echo "<br><br><br>"; 
		echo "<center><table border=0>"; 
		echo "<tr><td align=center colspan=2><font size=5>IMPRESION DE MOVIMIENTO DE INVENTARIO</font></td></tr>"; 
		echo "<tr><td td bgcolor=#dddddd align=center colspan=2><b>Conceptos:</b> <select name='con'>"; 
		$query = " SELECT Concod, Condes FROM ".$empresa."_000008 ORDER BY Concod"; 
		$err = mysql_query($query,$conex); 
		$num = mysql_num_rows($err); 
		for ($i=0;$i<$num;$i++) 
		{
			$row = mysql_fetch_array($err); 
			echo "<option>".$row[0]."-".$row[1]."</option>"; 
		}
		echo "</select></td></tr>"; 
		echo "<tr><td bgcolor=#dddddd align=center><b>Numero de Documento: </b><INPUT TYPE='text' NAME='doc' SIZE=10></td>"; 
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>"; 
	}
	else
	{
		//                   0       1       2       3       4       5      6       7       8       9       10      11      12
		$querys = "SELECT Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit, Menest, Menfac, Menobs  from ".$empresa."_000010 ";
		$querys .="   where Mendoc = '".$doc."'";
		$querys .="        and Mencon = '".substr($con,0,strpos($con,"-"))."'";
		$err = mysql_query($querys,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$wanot=$row[0];
		$wmest=$row[1];
		$wdoct=$row[2];
		$wfechat=$row[4];
		$wdant=$row[7];
		$wrett=$row[8];
		$west=$row[10];
		$wsop=$row[11];
		$wobser=$row[12];
		$query = "SELECT Concod, Condes, Conaca, Conaco from ".$empresa."_000008  where  Conmin = 'on' and Concod ='".$row[3]."'";
		$err1 = mysql_query($query,$conex);
		$row1 = mysql_fetch_array($err1);
		$wcont=$row1[1];
		$waca=$row1[2];
		$waco=$row1[3];
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003  where Ccocod='".$row[5]."'";
		$err1 = mysql_query($query,$conex);
		$row1 = mysql_fetch_array($err1);
		$wccoo=$row1[0]."-".$row1[1];
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003  where Ccocod='".$row[6]."'";
		$err1 = mysql_query($query,$conex);
		$row1 = mysql_fetch_array($err1);
		$wccod=$row1[0]."-".$row1[1];
		$query = "SELECT Pronit, Pronom, Proeml, Protel, Prodir      from ".$empresa."_000006  where Pronit='".$row[9]."'";
		$err1 = mysql_query($query,$conex);
		$row1 = mysql_fetch_array($err1);
		$wprovt=$row1[0];
		$wprovtn=$row1[1];
		$wpeml=$row1[2];
		$wpdir=$row1[4];
		$wptel=$row1[3];
		$query = "SELECT Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo  from ".$empresa."_000011  where Mdecon='".$row[3]."' and Mdedoc ='".$row[2]."' order by id ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		$data=array();
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($err1);
			$data[$i][0]=$row1[0];
			$query = "SELECT Artnom, Artima from ".$empresa."_000001  where Artcod='".$row1[0]."'";
			$err2 = mysql_query($query,$conex);
			$row2 = mysql_fetch_array($err2);
			$data[$i][1]=$row2[0];
			$data[$i][2]=$row1[1];
			$data[$i][3]=$row1[3];
			$data[$i][4]=$row1[2];
			if($data[$i][2] != 0)
				$data[$i][5]=$data[$i][4] / $data[$i][2];
			else
				$data[$i][5]=$data[$i][4];
			$data[$i][6]=($data[$i][3] / 100) * $data[$i][4];
			$data[$i][7]=$row1[4];
			$data[$i][8]=$row1[5];
			$data[$i][9]=$row2[1];
		}
		$j=$num1-1;
		// Cfgnit Cfgnom Cfgtre Cfgtel Cfgdir Cfgcco
		$querys = "SELECT Cfgnit, Cfgnom, Cfgtel, Cfgdir from ".$empresa."_000049 ";
		$querys .="   where Cfgcco = '".$row[5]."'";
		$err3 = mysql_query($querys,$conex);
		$num3 = mysql_num_rows($err3);
		if($num3 > 0)
		{
			$row3 = mysql_fetch_array($err3);
			$wtitx  = $row3[0]." ".$row3[1]."<br>";
			$wtitx .= $row3[2]." ".$row3[3]."<br>";
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=4><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=4><font color=#000000 size=4><b>".$wtitx."</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=4><font color=#000000 size=6><b>".$wcont."</font></b></font></td></tr>";
		echo "<tr><td align=right bgcolor=#999999 colspan=4><font color=#000000 size=2><b>Ver. 2009-01-06</font></b></font></td></tr>";
		$colora="#dddddd";
		$colorb="#ffffff";
		$color2="#006600";
		$color3="#cc0000";
		echo "<tr>";
		echo "<td bgcolor=".$colora." align=center><b>Nro : <font size=6>".$wdoct."</b></font></td>";	
		echo "<td bgcolor=".$colora." align=center><b>Año : </b>".$wanot."</td>";
		echo "<td bgcolor=".$colora." align=center><b>Mes : </b>".$wmest."</td>";	
		echo "<td bgcolor=".$colora." align=center><b>Fecha : </b>".$wfechat."</td>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td bgcolor=".$colorb." align=center><b>Nit : </b>".$wprovt."</td>";
		echo "<td bgcolor=".$colorb." align=center><b>Nombre : </b>".$wprovtn."</td>";
		echo "<td bgcolor=".$colorb." align=center><b>% Ret. : </b>".$wrett."</td>";
		echo "<td bgcolor=".$colorb." align=center><b>E-mail : </b>".$wpeml."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=".$colora." align=center><b>Direccion : </b></td>";
		echo "<td bgcolor=".$colora." align=center>".$wpdir."</td>";
		echo "<td bgcolor=".$colora." align=center><b>Telefonos : </b></td>";
		echo "<td bgcolor=".$colora." align=center>".$wptel."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=".$colorb." align=center><b>C.C. Origen : </b>".$row[5]."</td>";
		echo "<td bgcolor=".$colorb." align=center><b>C.C. Destino : </b>".$row[6]."</td>";
		echo "<td bgcolor=".$colorb." align=center><b>Anexo : </b>".$wdant." <b>Soporte : </b>".$wsop."</td>";
		switch ($west)
		{
			case "on":
				echo "<td bgcolor=".$colorb." align=center><b>Estado : </b><font  size=6><b>A</b></td>";
			break;
			case "off":
				echo "<td bgcolor=".$colorb." align=center><b>Estado : </b><font  size=6><b>I</b></td>";
			break;
		}
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font color=#000000><b>NRO ITEM</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>CODIGO</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999 ><font color=#000000><b>CANTIDAD</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>% IVA </b></font><td align=center bgcolor=#999999><font color=#000000><b>VLR UNITARIO</b></font></td></td><td align=center bgcolor=#999999><font color=#000000><b>VLR IVA</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>VLR TOTAL</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>VENCIMIENTO</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>NRo. LOTE</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>REG. INVIMA</b></font></td></tr>";
		$wtotg=0;
		$wtotiva=0;
		for ($i=0;$i<=$j;$i++)
		{
			if($i % 2 == 0)
				$color="#dddddd";
			else
				$color="#ffffff";
			$wtotg += $data[$i][4];
			$wtotiva += $data[$i][6];
			$w=$i+1;
			echo "<tr><td bgcolor=".$color." align=center>".$w."</td>";	
			echo "<td bgcolor=".$color.">".$data[$i][0]."</td>";	
			echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";	
			echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][2],2,'.',',')."</td>";	
			echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][3],2,'.',',')."</td>";	
			echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][5],2,'.',',')."</td>";	
			echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][6],2,'.',',')."</td>";	
			echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][4],2,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=center>".$data[$i][7]."</td>";
			echo "<td bgcolor=".$color." align=right>".$data[$i][8]."</td>";
			echo "<td bgcolor=".$color." align=right>".$data[$i][9]."</td></tr>";
		}
		echo "<tr><td bgcolor=#999999 align=center colspan=6><font color=#000000><b>TOTALES</b></font></td><td bgcolor=#999999 align=right><font color=#000000><b>$".number_format((double)$wtotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font color=#000000><b>$".number_format((double)$wtotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=3>&nbsp</td></tr>";	
		echo"</table>";
		echo "<br><br><center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoG01>OBSERVACIONES: ".$wobser."</td></tr>";
		echo"</table>";
		echo "<br><br><center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoG02>ELABORADO: </td><td id=tipoG02>APROBADO: </td><td id=tipoG02>RECIBIDO: </td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>