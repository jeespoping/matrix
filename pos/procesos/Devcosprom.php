<html>
<head>
  	<title>MATRIX Programa de Ajuste a la Devolucion Al Costo Promedio</title>
  	      <link rel="stylesheet" href="/styles.css" type="text/css">
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #99CCFF;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #CCCCCC;
		}
	
	//-->
	</style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Devcosprom.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");


/**********************************************************************************************************************  
	   PROGRAMA : Devcosprom.php
	   Fecha de Liberación : 2008-04-02
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2009-07-08
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite Realizar un movimiento de ajuste en el inventario
	   de las devoluciones hechas al proveedor al costo promedio.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2009-07-08
	   		Se modifico el programa para que el ajuste a la devolucion al costo promedio se base en el valor del costo promedio
	   		del concepto 201 y  no en el del concepto 203.
	   		
	   .2009-06-05
	   		Se modifico el programa para que el ajuste a la devolucion al costo promedio se base en el concepto 203 de
	   		ajuste parcial a la devolucion al costo promedio y no en el 201.
	   		
	   .2008-09-30
	   		Se modifico el programa para que grabara el nit del documento 201 en el documento 202.
	   		Igualmente se muestra el concecutivo de los docimentos 202.
	   		
	   .2008-04-02
	   		Release de Versión. Version 2008-04-02

	   
***********************************************************************************************************************/
function validar($chain)
{
	$regular="^(\+|-)?([[:digit:]]+)$";
	if (ereg($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
		
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Devcosprom' action='Devcosprom.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		$wsw=0;
		for ($i=0;$i<$numr;$i++)
		{
			if($data[$i][5] == 0)
			{
				$wsw=1;
			}
		}
		if($wsw == 0)
		{
			$query = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE,".$empresa."_000010 LOW_PRIORITY WRITE,".$empresa."_000011 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO ".mysql_errno().":".mysql_error());
			$query = "select Concon, Concod from ".$empresa."_000008 where Condes LIKE '%AJUSTE A LA DEVOLUCION AL COSTO PROMEDIO%' ";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO ".mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err1);
			$wdoct=$row[0] + 1;
			$wcont=$row[1];
			$wanot=substr(date("Y-m-d"),0,4);
			$wmest=substr(date("Y-m-d"),5,2);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query =  " update ".$empresa."_000008 set Concon = Concon + 1 where Concod='".$wcont."'";
			$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO ".mysql_errno().":".mysql_error());
			$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data, Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit,Menusu, Menfac, Menest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanot.",".$wmest.",".$wdoct.",'".$wcont."','".date("Y-m-d")."','".$data[0][7]."','".$data[0][7]."','".$wnum."',0,'".$data[0][9]."','".$key."','.','on ','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ENCABEZADO DE MOVIMIENTO ".mysql_errno().":".mysql_error());
			for ($i=0;$i<$numr;$i++)
			{
				$data[$i][6]=$data[$i][6]*$data[$i][3];
				$query = "insert ".$empresa."_000011 (medico,fecha_data,hora_data, Mdecon, Mdedoc, Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdeest, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wcont."',".$wdoct.",'".$data[$i][1]."',".$data[$i][3].",".$data[$i][6].",".$data[$i][8].",'0000-00-00','.','on ','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DETALLE DE MOVIMIENTO ".mysql_errno().":".mysql_error());
			}
			$query = " UNLOCK TABLES";													
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS ".mysql_errno().":".mysql_error());
			$color5="#99CCFF";
			echo "<table border=0 align=center>";
			echo "<tr><td bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'><b>CORRECTO !!!</b></font></TD><TD bgcolor=".$color5."><font size=6 color=#000000 face='tahoma'><b>Doc : ".$wdoct."&nbsp &nbsp  Cpto : 202-AJUSTE A LA DEVOLUCION AL COSTO PROMEDIO </b></font></td></tr></table><br><br><br>";
			unset($ok);
			unset($wnum);
		}
	}
	if (!isset($wnum) or $wnum == "" or !isset($wsel) or $wsel == "off")
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=2><font size=2>Powered by : MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>AJUSTE A LA DEVOLUCION AL COSTO PROMEDIO</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2009-07-08</font></b></font></td></tr>";
		if (isset($wnum))
			echo "<tr><td align=center bgcolor=".$wcolor.">Documento Para Ajuste : </td><td align=center bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wnum' size=10 maxlength=10 value='".$wnum."' ></td></tr>";
		else
			echo "<tr><td align=center bgcolor=".$wcolor.">Documento Para Ajuste : </td><td align=center bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wnum' size=10 maxlength=10 ></td></tr>";
		$wnom="NO ESPECIFICO";
		$wsel="off";
		if (isset($wnum) and $wnum != "")
		{
			$query =  " SELECT Mendoc  FROM ".$empresa."_000010 where Mencon = '202' and Mendan='".$wnum."' ";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$num = mysql_num_rows($err);
			if($num == 0)
			{
				$query =  " SELECT Mendoc  FROM ".$empresa."_000010 where Mencon = '203' and Mendoc='".$wnum."' ";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$wnom=$row[0];
					$wsel="on";
					echo "<tr><td align=center bgcolor=#999999 colspan=2>".$wnum." : MOVIMIENTO VALIDADO</td></tr>";
				}
				else
				{
					echo "<tr><td align=center bgcolor=#999999 colspan=2>".$wnum." : MOVIMIENTO INCORRECTO REVISE!!!!!</td></tr>";
				}
			}
			else
			{
				echo "<tr><td align=center bgcolor=#999999 colspan=2>".$wnum." : MOVIMIENTO YA FUE AJUSTADO REVISE!!!!!</td></tr>";
			}
		}
		echo "<input type='HIDDEN' name= 'wsel' value=".$wsel.">";
		echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Ok'></td></tr>"; 
		echo "</table>";  
	}
	else
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>AJUSTE A LA DEVOLUCION AL COSTO PROMEDIO</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2009-07-08</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=".$wcolor." colspan=2>Documento De Devolucion x Costo Promedio : ".$wnum."</td></tr>";
		echo "</table><br><br>";  
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=9><font color=#000066 size=5><b>DETALLE DE ARTICULOS A AJUSTAR</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >NRO ITEM</font></td><td align=center bgcolor=#000066><font color=#ffffff >CODIGO</font></td><td align=center bgcolor=#000066><font color=#ffffff >DESCRIPCION</font></td><td align=center bgcolor=#000066><font color=#ffffff >CANTIDAD</font></td><td align=center bgcolor=#000066><font color=#ffffff >COSTO<BR>PROMEDIO</font></td><td align=center bgcolor=#000066><font color=#ffffff >COSTO<BR>REAL</font></td><td align=center bgcolor=#000066 ><font color=#ffffff >DIFERENCIA</font></td><td align=center bgcolor=#000066 ><font color=#ffffff >BODEGA</font></td><td align=center bgcolor=#000066 ><font color=#ffffff >PROVEEDOR</font></td></tr>";
		if(!isset($dta))
 		{
	 		$dta=0;
	 		
	 		//                  0   
	 		$query = "select Mendan from ".$empresa."_000010 ";
	 		$query .= " where Mendoc = '".$wnum."' ";
			$query .= "   and Mencon = '203' "; 
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$row = mysql_fetch_array($err);
			$wdanex = $row[0];
			$query = "select Mdeart from ".$empresa."_000011 ";
	 		$query .= " where Mdedoc = '".$wnum."' ";
			$query .= "   and Mdecon = '203' "; 
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$win="(".chr(34).$row[0].chr(34);
			for ($i=1;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$win .= ",".chr(34).$row[0].chr(34);
			}
			$win .= ")"; 
	 		//                  0      1       2       3       4       5       6       7
	 		$query = "select Mencco, Mdeart, Artnom, Mdecan, Mdevto, Artiva, Mennit, Pronom from ".$empresa."_000010,".$empresa."_000011,".$empresa."_000001,".$empresa."_000006 ";
	 		$query .= " where Mendoc = '".$wdanex."' ";
			$query .= "   and Mencon = '201' "; 
			$query .= "   and Mendoc = Mdedoc ";
			$query .= "   and Mencon = Mdecon ";
			$query .= "   and Mdeart in ".$win." ";
			$query .= "   and Mdeart = Artcod ";
			$query .= "   and Mennit = Pronit ";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$numr = mysql_num_rows($err);
			if($numr > 0)
			{
				$data=array();
				$dta=1;
				for ($i=0;$i<$numr;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i][0]=$i + 1;
					$data[$i][1]=$row[1];
					$data[$i][2]=$row[2];
					$data[$i][3]=$row[3];
					$data[$i][4]=$row[4] / $row[3];
					$data[$i][5]=0;
					$data[$i][6]=0;
					$data[$i][7]=$row[0];
					$data[$i][8]=$row[5];
					$data[$i][9]=$row[6];
					$data[$i][10]=$row[7];
					echo "<input type='HIDDEN' name= 'data[".$i."][5]' value=".$data[$i][5].">";
				}
			}
		}
		if($dta == 1)
		{
			for ($i=0;$i<$numr;$i++)
			{
				if($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				echo "<tr>";
				$data[$i][6] = $data[$i][5] - $data[$i][4];
				echo "<td bgcolor=".$color." align=center>".$data[$i][0]."</td>";
				echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";	
				echo "<td bgcolor=".$color.">".$data[$i][2]."</td>";	
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][3],2,'.',',')."</td>";		
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][4],4,'.',',')."</td>";
				echo "<td bgcolor=".$color." align=right><INPUT TYPE='text' NAME='data[".$i."][5]' size=6 maxlength=6 value=".$data[$i][5]." onblur='enter()'></td>";
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][6],4,'.',',')."</td>";
				echo "<td bgcolor=".$color.">".$data[$i][7]."</td>";
				echo "<td bgcolor=".$color.">".$data[$i][9]."-".$data[$i][10]."</td>";
				echo "</tr>";
			}
			echo "<tr><td align=center bgcolor=#dddddd colspan=9><font color=#000066 size=5><b>AJUSTAR</b><input type='checkbox' name='ok' OnClick='enter()'></font></td></tr>";
			echo "</table><br><br>"; 
		}
		echo "<input type='HIDDEN' name= 'numr' value='".$numr."'>";
		echo "<input type='HIDDEN' name= 'dta' value='".$dta."'>";
		echo "<input type='HIDDEN' name= 'wnum' value='".$wnum."'>";
		echo "<input type='HIDDEN' name= 'wsel' value='".$wsel."'>";
		for ($i=0;$i<$numr;$i++)
		{
			echo "<input type='HIDDEN' name= 'data[".$i."][0]' value=".$data[$i][0].">";
			echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
			echo "<input type='HIDDEN' name= 'data[".$i."][2]' value='".$data[$i][2]."'>";
			echo "<input type='HIDDEN' name= 'data[".$i."][3]' value=".$data[$i][3].">";
			echo "<input type='HIDDEN' name= 'data[".$i."][4]' value=".$data[$i][4].">";
			echo "<input type='HIDDEN' name= 'data[".$i."][6]' value=".$data[$i][6].">";
			echo "<input type='HIDDEN' name= 'data[".$i."][7]' value=".$data[$i][7].">";
			echo "<input type='HIDDEN' name= 'data[".$i."][8]' value=".$data[$i][8].">";
			echo "<input type='HIDDEN' name= 'data[".$i."][9]' value=".$data[$i][9].">";
			echo "<input type='HIDDEN' name= 'data[".$i."][10]' value=".$data[$i][10].">";
		}
	}
}
?>
