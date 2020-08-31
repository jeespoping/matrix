<html>
<head>
  	<title>MATRIX Programa de Cargos a Nomina</title>
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
<body BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : carnom.php
	   Fecha de Liberación : 2005-09-26
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 1.01
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar las novedades a las nominas manejadas x Promotora en Servinte.
	   Los prestamos son descontados quincenalmente en un maximo de 4 cuotas.
	   
	   REGISTRO DE MODIFICACIONES :
			
	   .2005-09-26
	   		Inicio desarrollo del Programa.
	   		
	   .2005-11-21
	   		Se corrigio el programa para eliminar una inserción de un registro inadecuado en la tabla 46.
	   		
	   	.2005-11-21
	   		Se corrigio el programa para seleccionar solamente los prestamos en estado 'on' en la tabla 46.
	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='carnom' action='Carnom.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		$conex_o = odbc_connect(substr($wemp,0,strpos($wemp,"-")),'','') or die("No se ralizo Conexion");
		for ($i=0;$i<$num;$i++)
		{
			if(isset($fac[$i]))
			{
				$cup=$data[$i][6] - 1;
				$query = "update ".$empresa."_000046 set Pnocup =".$cup." where Pnocon=".$data[$i][0]." and Pnocuo > 0" ;
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$cup=$data[$i][5] - $cup;
				$val=$data[$i][4] / $data[$i][5];
				$date=date("Y-m-d");
				$query= "insert into nonov values ('".substr($date,0,4)."','".substr($date,5,2)."','Q','".$wsec."','1','".$data[$i][1]."','".$data[$i][3]."','5450','','1',0.00,".number_format((double)$val,2,'.','').",'S','N','','') ";
				$err_o = odbc_do($conex_o,$query) or die("Error en Grabacion nonov ODBC");
			}
		}
		unset($wano);
	}
	if (!isset($wano) or !isset($wmes) or !isset($wsec) or !isset($wemp) or (($wsec != $wmes * 2) and ($wsec != $wmes * 2 - 1)) or ($wano < date("Y")) or ($wmes < date("m")))
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX</font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>CARGO DE NOVEDADES A NOMINA</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 1.01</font></b></font></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Año (AAAA) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wano' value = '".date("Y")."'></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Mes (MM) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wmes' value = '".date("m")."'></td></tr>";
		$sec=date("m") * 2;
		if($sec < 10)
			$sec="0".$sec;
		echo "<tr><td  bgcolor=".$wcolor.">Quincena (QQ) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wsec' value = '".$sec."'></td></tr>";
		$query =  " SELECT Nomnom, Nomdes FROM ".$empresa."_000036 where Nomusu='".$key."' ORDER BY Nomnom";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=".$wcolor.">Empresa : </td><td bgcolor=".$wcolor."><select name='wemp'>";
		for ($i=0;$i<$num;$i++)
		{
		      $row = mysql_fetch_array($err); 
		      echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		 echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Ok'></td></tr>"; 
		 echo "</table>";  
	}
	else
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>CARGO DE NOVEDADES A NOMINA</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 1.00</font></b></font></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Año : </td><td bgcolor=".$wcolor.">".$wano."</td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Mes : </td><td bgcolor=".$wcolor.">".$wmes."</td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Quincena : </td><td bgcolor=".$wcolor.">".$wsec."</td></tr>";
		echo "<tr><td bgcolor=".$wcolor.">Empresa : </td><td bgcolor=".$wcolor.">".$wemp."</td></tr>";
		echo "</table><br><br>";  
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=12><font color=#000066 size=5><b>DETALLE DE NOVEDADES</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=12><font color=#000066 size=5><b>Marcar Todos</b><input type='checkbox' name='all'></font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >PASAR A <BR> NOMINA</font></td><td align=center bgcolor=#000066><font color=#ffffff >PRESTAMO<BR> NRo.</font></td><td align=center bgcolor=#000066><font color=#ffffff >CODIGO</font></td><td align=center bgcolor=#000066><font color=#ffffff >NOMBRE</font></td><td align=center bgcolor=#000066><font color=#ffffff >C. C.</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR</font><td align=center bgcolor=#000066><font color=#ffffff >CUOTAS <BR> TOTALES</font></td><td align=center bgcolor=#000066><font color=#ffffff >CUOTAS <BR> PENDIENTES</font></td></tr>";
		if(!isset($dta))
 		{
	 		$dta=0;
			$query = "select Pnocon, Pnocod, Pnonom, Pnocco, Pnoval, Pnocuo, Pnocup  from  ".$empresa."_000046 ";
	 		$query .= " where Pnoemp = '".substr($wemp,0,strpos($wemp,"-"))."'";
			$query .= "      and Pnocup > 0 ";
			$query .= "      and Pnocuo > 0 ";
			$query .= "      and Pnoest = 'on' ";
			$query .= "     order by  Pnocon ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$dta=1;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i][0]=$row[0];
					$data[$i][1]=$row[1];
					$data[$i][2]=$row[2];
					$data[$i][3]=$row[3];
					$data[$i][4]=$row[4];
					$data[$i][5]=$row[5];
					$data[$i][6]=$row[6];
				}
			}
		}
		if($dta == 1)
		{
			$wtotal = 0;
			$wsel = 0;
			for ($i=0;$i<$num;$i++)
			{
				$wtotal += 1;
				if($i % 2 == 0)
					$color="#dddddd";
				else
					$color="#cccccc";
				echo "<tr>";
				if(isset($fac[$i]) or isset($all))
				{
					$wsel += 1;
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]' checked></td>";
				}
				else
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]'></td>";
				echo "<td bgcolor=".$color.">".$data[$i][0]."</td>";	
				echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";	
				echo "<td bgcolor=".$color.">".$data[$i][2]."</td>";
				echo "<td bgcolor=".$color.">".$data[$i][3]."</td>";
				echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][4],2,'.',',')."</td>";	
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][5],0,'.',',')."</td>";	
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][6],0,'.',',')."</td>";	
				echo "</tr>";
				echo "<input type='HIDDEN' name= 'wano' value='".$wano."'>";
				echo "<input type='HIDDEN' name= 'wmes' value='".$wmes."'>";
				echo "<input type='HIDDEN' name= 'wsec' value='".$wsec."'>";
				echo "<input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<input type='HIDDEN' name= 'dta' value=".$dta.">";
				echo "<input type='HIDDEN' name= 'num' value=".$num.">";
				echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][2]' value='".$data[$i][2]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][3]' value='".$data[$i][3]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][4]' value=".$data[$i][4].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][5]' value=".$data[$i][5].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][6]' value=".$data[$i][6].">";
			}
			echo "<tr><td  bgcolor=#999999 colspan=6><b>TOTAL NOVEDADES SELECCIONADAS : </b></td><td bgcolor=#999999 align=right><b>".number_format((double)$wsel,0,'.',',')."</b></td><td align=center bgcolor=#999999 colspan=5>&nbsp</td></tr>"; 
			echo "<tr><td  bgcolor=#999999 colspan=6><b>TOTAL NOVEDADES  : </td><td bgcolor=#999999 align=right><b>".number_format((double)$wtotal,0,'.',',')."</b></td><td align=center bgcolor=#999999 colspan=5>&nbsp</td></tr>"; 
			echo "<tr><td align=center bgcolor=#dddddd colspan=12><font color=#000066 size=5><b>GENERAR</b><input type='checkbox' name='ok'></font></td></tr>";
		}
		echo "<tr><td align=center bgcolor=#999999 colspan=12><input type='submit' value='Ok'></td></tr>"; 
		echo "</table><br><br>";
	}
}
?>