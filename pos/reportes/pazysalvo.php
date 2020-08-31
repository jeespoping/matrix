<html>
<head>
  	<title>MATRIX Extracto de Cartera x Nomina</title>
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
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Extracto De Cartera X Nomina </font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> pazysalvo.php Ver. 2007-09-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : Pazysalvo.php
	   Fecha de Liberación : 2007-09-05
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-09-05
	   
	   OBJETIVO GENERAL : 
	   Reporte del estado de la cartera x usuario x empresa con el punto de venta para expedicion de Paz y Salvo.
	   
	   REGISTRO DE MODIFICACIONES :
	   		
	   .2007-09-05
	   		Inicio desarrollo del Programa.
	   		
	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Pazysalvo' action='pazysalvo.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if (!isset($codigo) or !isset($wemp) or !isset($wtip) or (strtoupper ($wtip) != "S" and strtoupper ($wtip) != "T"))
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX</font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>EXTRACTO DE CARTERA X NOMINA </font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 1.00</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#999999 size=4><b>PRESTAMOS EN FARMASTORE</font></b></font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Identificacion del Usuario</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='codigo' size=8 maxlength=8></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Nomnom, Nomdes    from ".$empresa."_000036 where Nomusu='".$empresa."' order by Nomnom ";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";	
		echo "<tr><td bgcolor=#cccccc align=center>Con Saldo o Todos ? (S/T)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Ok'></td></tr>"; 
		echo "</table>";  
	}
	else
	{
		$wsw=0;
		$wtip=strtoupper ($wtip);
		$wcolor="#cccccc";
		$conex_o = odbc_connect(substr($wemp,0,strpos($wemp,"-")),'','') or die("No se ralizo Conexion");
		$query = "Select percod,perap1,perap2,perno1,perno2,percco,cconom,perced,perofi,ofinom From noper,cocco,noofi  ";
		$query=$query."  Where percod = '".$codigo."'";
		$query=$query."  And peretr ='A'";
		$query=$query."  And percco = ccocod ";
		$query=$query."  And perofi = oficod  ";
		$err = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err);
		if (odbc_fetch_row($err))
		{
			$wsw=1;
			$row=array();
			for($i=1;$i<=$campos;$i++)
			{
				$row[$i-1]=odbc_result($err,$i);
			}
		}
		if($wsw == 1)
		{
			$nombre=$row[3]." ".$row[4]." ".$row[1]." ".$row[2];	
			echo "<table border=0 align=center>";
			echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/FARMASTORE/logo farmastore.png'></td></tr>";
			echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
			echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>EXTRACTO DE CARTERA X NOMINA </font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 1.01</font></b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#999999 size=4><b>PRESTAMOS EN FARMASTORE</font></b></font></td></tr>";
			echo "<tr><td bgcolor=#cccccc colspan=2 align=center>EMPLEADO : ".$row[0]."-".$nombre." CEDULA : ".$row[7]." OFICIO : ".$row[8]."-".$row[9]."</td></tr>";
			echo "<tr><td bgcolor=#cccccc colspan=2 align=center>CENTRO DE COSTOS : ".$row[5]."-".$row[6]."</td></tr>";
			echo "<tr><td  bgcolor=#cccccc colspan=2 align=center>Empresa : ".$wemp."</td></tr>";
			echo "</table><br><br>";  
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor=#999999 colspan=6><font color=#000066 size=5><b>DETALLE DE PRESTAMOS</b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >PRESTAMO <BR> NRo.</font></td><td align=center bgcolor=#000066><font color=#ffffff >FECHA</font></td><td align=center bgcolor=#000066><font color=#ffffff >MONTO</font></td><td align=center bgcolor=#000066><font color=#ffffff >PLAZO</font></td><td align=center bgcolor=#000066><font color=#ffffff >CUOTAS<BR>PENDIENTES</font></td><td align=center bgcolor=#000066><font color=#ffffff >SALDO</font></td></tr>";
			if(!isset($dta))
	 		{
		 		$dta=0;
				$query = "select Pnocon, Pnocup, Pnocuo, Pnoval, Pnofec from  ".$empresa."_000046 ";
		 		$query .= " where Pnoemp = '".substr($wemp,0,strpos($wemp,"-"))."'";
		 		$query .= "      and Pnocod = '".$codigo."'";
		 		$query .= "      and Pnoest = 'on' ";
				$query .= "     order by  Pnocon, Pnocuo desc, Pnocup ";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$data=array();
					$dta=1;
					$j=-1;
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$j=$j + 1;
						$data[$j][0]=$row[0];
						$data[$j][2]=$row[2];
						$data[$j][3]=$row[3];
						$data[$j][5]=$row[4];
						$data[$j][4]=$row[3] / $row[2] * $row[1];
						$data[$j][1]=$row[1];
					}
				}
			}
			if($dta == 1)
			{
				$wtotal = 0;
				$wt=0;
				$ws=0;
				$wsel = 0;
				for ($i=0;$i<=$j;$i++)
				{
					if(($wtip == "S" and $data[$i][1] > 0) or $wtip == "T")
					{
						$wtotal += 1;
						if($data[$i][4] < 1)
							$data[$i][4]=0;
						$wt += $data[$i][3];
						$ws += $data[$i][4];
						if($i % 2 == 0)
							$color="#ffffff";
						else
							$color="#99CCFF";
						echo "<tr>";
						echo "<td bgcolor=".$color." align=center>".$data[$i][0]."</td>";	
						echo "<td bgcolor=".$color." align=center>".$data[$i][5]."</td>";	
						echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][3],2,'.',',')."</td>";	
						echo "<td bgcolor=".$color." align=center>".$data[$i][2]."</td>";
						echo "<td bgcolor=".$color." align=center>".$data[$i][1]."</td>";	
						echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][4],2,'.',',')."</td>";	
						echo "</tr>";
					}
				}
				echo "<tr><td  bgcolor=#999999 colspan=2><b>TOTAL PRESTAMOS  : ".number_format((double)$wtotal,0,'.',',')."</b></td><td bgcolor=#999999 align=right>$".number_format((double)$wt,2,'.',',')."</td><td bgcolor=#999999 align=right colspan=2></td><td bgcolor=#999999 align=right>$".number_format((double)$ws,2,'.',',')."</td></tr>"; 
			}
			echo "</table><br><br>";
		}
		else
		{
			echo "<br><br><center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL EMPLEADO EN ESTA EMPRESA !!!!</MARQUEE></FONT>";
			echo "<input type='submit' value='Continuar'></center>";
			echo "<br><br>";
		}
	}
}
?>
