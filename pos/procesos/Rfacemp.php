<html>
<head>
  	<title>MATRIX Programa de Refacturacion de Empresas</title>
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
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Rfacemp.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Refacturacion De Empresas</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Rfacemp.php Ver. 2006-08-23</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");


/**********************************************************************************************************************  
	   PROGRAMA : facemp.php
	   Fecha de Liberación : 2006-02-21
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2006-06-30
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar la facturación de empresas o facturacion x lotes para una empresa
	   entre dos fecha determinadas y para un centro de costos en especial o para todos.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2006-08-23
	   	Se corrigio el programa para modificar el tipo de mepresa al cambiar el codigo de la misma.
	   	
	   .2006-06-30
	   	Se incluye al archivo Nro. 50 de VENTAS-MEDICOS-PROGRAMAS y el archivo Nro.68 de RELACION DE VENTAS REFACTURADAS
	   	
	   .2006-02-21
	  	 Programa Nuevo
	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Rfacemp' action='Rfacemp.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		echo "<table border=0 align=center>";
		echo "<tr><td colspan=3  bgcolor=#dddddd align=center><font size=5>PROCESO</td></tr>";
		echo "<tr>";
		$k=0;
		for ($i=0;$i<$num_1;$i++)
		{
			if(isset($fac[$i]))
			{
				$query = "lock table ".$empresa."_000003 LOW_PRIORITY WRITE ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
				$query = "select Ccopve, Ccovei, Ccovef  from ".$empresa."_000003 where Ccocod='".substr($wcco,0,strpos($wcco,"-"))."'";
				$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
				$row = mysql_fetch_array($err1);
				$wdoct=$row[1];
				$wdoct=$wdoct + 1;
				$query =  " update ".$empresa."_000003 set Ccovei = ".$wdoct." where Ccocod='".substr($wcco,0,strpos($wcco,"-"))."'";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$wdoctf=$row[2];
				$wprefijo=$row[0];
				$query = " UNLOCK TABLES";													
				$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");
				if($wdoct <= $wdoctf)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "select Venano, Venmes, Vennum, Venfec, Vencon, Vencco, Vencaj, Vencod, Vennit, Ventcl, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Venffa, Vennfa, Vennmo, Venusu, Ventve, Venmsj, Venest   from ".$empresa."_000016 where Vennum='".$data[$i][10]."' ";
					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS");
					$row = mysql_fetch_array($err1);
					$query = " insert ".$empresa."_000016(Medico,Fecha_data,Hora_data,venano,venmes,venfec,vennum,vencon,vencco,vencaj,vencod,vennit,ventcl,venvto,venviv,vencop,vencmo,vendes,venrec,venffa,vennfa,vennmo,venusu,ventve,venmsj,venest,Seguridad) values ('".$empresa."','".$fecha."','".$hora."','".date("Y")."','".date("m")."','".$fecha."','".$wprefijo."-".$wdoct."',".$row[4].",'".$row[5]."','".$row[6]."','".substr($wemp,0,strpos($wemp,"-"))."','".$row[8]."','".$wtipemp."',".number_format($row[10],0,'.','')." ,".number_format($row[11],0,'.','')." , '". $row[12]."'   ,". $row[13].",".number_format($row[14],0,'.','')." ,".number_format($row[15],0,'.','')." ,'".$row[16]."','',".$row[18].",'".$row[19]."','".$row[20]."','".$row[21]."', 'on'  , 'C-".$key."')";
					$err2 = mysql_query($query,$conex) or die("Tabla 16 : ".mysql_errno().":".mysql_error());
					$query = "select Vdenum, Vdeart, Vdevun, Vdecan, Vdepiv, Vdedes, Vdeest, Vdepun    from ".$empresa."_000017 where Vdenum='".$data[$i][10]."' ";
					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO DETALLE DE VENTAS");
					$num1 = mysql_num_rows($err1);
					for ($j=0;$j<$num1;$j++)
					{
						$row = mysql_fetch_array($err1);
						$query = " insert  ".$empresa."_000017 (Medico,Fecha_data,Hora_data,vdenum,vdeart,vdevun,vdecan,vdepiv,vdedes,vdeest,Vdepun,Seguridad) values ('".$empresa."','".$fecha."','".$hora."' ,'".$wprefijo."-".$wdoct."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",".$row[5].", 'on',".$row[7].",'C-".$key."')";
						$err2 = mysql_query($query,$conex) or die("Tabla 17 : ".mysql_errno().":".mysql_error());
					}
					$query = "select Vmpvta, Vmpmed, Vmppro, Vmprem   from ".$empresa."_000050 where Vmpvta='".$data[$i][10]."' ";
					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS-MEDICO-PROGRAMAS");
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row = mysql_fetch_array($err1);
						$query = " insert ".$empresa."_000050(Medico,Fecha_data,Hora_data,Vmpvta, Vmpmed, Vmppro, Vmprem, Seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wprefijo."-".$wdoct."','".$row[1]."','".$row[2]."','".$row[3]."', 'C-".$key."')";
						$err2 = mysql_query($query,$conex) or die("Tabla 50 : ".mysql_errno().":".mysql_error());
					}
					$query = " insert ".$empresa."_000068(Medico,Fecha_data,Hora_data, Rvrvan, Rvrvac, Seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$data[$i][10]."','".$wprefijo."-".$wdoct."','C-".$key."')";
					$err2 = mysql_query($query,$conex) or die("Tabla 68 : ".mysql_errno().":".mysql_error());
					$k++;
					echo "<td><font size=2>&nbsp&nbsp VENTA : <b>".$data[$i][10]."</b> REGRABADA COMO :<b> ".$wprefijo."-".$wdoct."</b>&nbsp&nbsp</font></td>";
					if($k == 3)
					{
						echo "</tr><tr>";
						$k=0;
					}
				}
			}
		}
		echo "</table><br><br>";
		unset($ok);
		unset($wfac);
	}
	if (!isset($wfac))
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Author :  Ing. Pedro Ortiz Tamayo</font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>REFACTURACION DE EMPRESAS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2006-06-30</font></b></font></td></tr>";
		$query =  " SELECT Empres, empnom FROM ".$empresa."_000024  where Empfac= 'off' ORDER BY empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=".$wcolor.">Empresa Correcta : </td><td bgcolor=".$wcolor."><select name='wemp'>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err); 
		    echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		 $query =  " SELECT ccocod, ccodes  FROM ".$empresa."_000003  ORDER BY ccocod ";
		 $err = mysql_query($query,$conex);
		 $num = mysql_num_rows($err);
		 echo "<tr><td bgcolor=".$wcolor.">Centro de Costo : </td><td bgcolor=".$wcolor."><select name='wcco'>";
		 for ($i=0;$i<$num;$i++)
		 {
			$row = mysql_fetch_array($err); 
			echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		 echo "<tr><td bgcolor=#cccccc align=center>Nro. Factura a Refacturar : </td>";
		 echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfac' size=10 maxlength=10></td></tr>";
		 echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Ok'></td></tr>"; 
		 echo "</table>";  
	}
	else
	{
		$query = "select Emptem  from ".$empresa."_000024 ";
 		$query .= " where Empcod = '".substr($wemp,0,strpos($wemp,"-"))."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$wtipemp=$row[0];
		echo "<input type='HIDDEN' name= 'wtipemp' value='".$wtipemp."'>";
		
		$query = "select Rdefac  from  ".$empresa."_000021 ";
 		$query .= " where Rdefac = '".$wfac."'";
		$query .= "      and Rdeest = 'on' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num == 0)
		{
			$wea="";
			$query = "select Fennit,Empnom  from  ".$empresa."_000018,".$empresa."_000024 ";
	 		$query .= " where Fenfac = '".$wfac."'";
			$query .= "      and Fennit = Empnit ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wea=$row[0]."-".$row[1];
			}
			$wcolor="#cccccc";
			echo "<table border=0 align=center>";
			echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
			echo "<tr><td align=right colspan=2><font size=2>Author :  Ing. Pedro Ortiz Tamayo</font></td></tr>";
			echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>REFACTURACION POR EMPRESA</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2006-02-21</font></b></font></td></tr>";
			echo "<tr><td bgcolor=".$wcolor.">Nro. Factura : </td><td bgcolor=".$wcolor.">".$wfac."</td></tr>";
			echo "<tr><td bgcolor=".$wcolor.">Empresa Anterior : </td><td bgcolor=".$wcolor.">".$wea."</td></tr>";
			echo "<tr><td bgcolor=".$wcolor.">Empresa Correcta : </td><td bgcolor=".$wcolor.">".$wemp."</td></tr>";
			echo "<tr><td bgcolor=".$wcolor.">Centro de Costo : </td><td bgcolor=".$wcolor.">".$wcco."</td></tr>";
			echo "</table><br><br>";  
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor=#999999 colspan=13><font color=#000066 size=5><b>DETALLE DE VENTAS</b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >NRO ITEM</font></td><td align=center bgcolor=#000066><font color=#ffffff >REFACTURAR</font></td><td align=center bgcolor=#000066><font color=#ffffff >VENTA <BR> NRo.</font></td><td align=center bgcolor=#000066><font color=#ffffff >C. DE C.</font></td><td align=center bgcolor=#000066><font color=#ffffff >FECHA</font></td><td align=center bgcolor=#000066><font color=#ffffff >CEDULA</font></td><td align=center bgcolor=#000066 ><font color=#ffffff >PACIENTE</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR<BR>TOTAL </font><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> IVA</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> COPAGO</font></td></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> CUOTA<BR>MODERADORA</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> DESCUENTO</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> RECARGO</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=13><font color=#000066 size=5><b>Marcar Todos</b><input type='checkbox' name='all'  onclick='enter()'></font></td></tr>";
			if(!isset($dta))
	 		{
		 		$dta=0;
				$query = "select Vencco, Venfec, Vennit, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec,Vennum from  ".$empresa."_000016 ";
		 		$query .= " where Vencco = '".substr($wcco,0,strpos($wcco,"-"))."'";
				$query .= "      and Venest = 'on' ";
				$query .= "      and Vennfa = '".$wfac."' ";
				$err = mysql_query($query,$conex);
				$num_1 = mysql_num_rows($err);
				if($num_1 > 0)
				{
					$data=array();
					$dta=1;
					for ($i=0;$i<$num_1;$i++)
					{
						$row = mysql_fetch_array($err);
						$query = "select Clinom  from  ".$empresa."_000041 ";
				 		$query .= " where Clidoc = '".$row[2]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$data[$i][0]=$row[0];
						$data[$i][1]=$row[1];
						$data[$i][2]=$row[2];
						if($num1 == 1)
						{
							$row1 = mysql_fetch_array($err1);
							$data[$i][3]=$row1[0];
						}
						else
							$data[$i][3]="NO ESPECIFICO";
						$data[$i][4]=$row[3];
						$data[$i][5]=$row[4];
						$data[$i][6]=$row[5];
						$data[$i][7]=$row[6];
						$data[$i][8]=$row[7];
						$data[$i][9]=$row[8];
						$data[$i][10]=$row[9];
					}
				}
			}
			if($dta == 1)
			{
				$wtotal = 0;
				$wiva = 0;
				$wcop = 0;
				$wcmo = 0;
				$wdes = 0;
				$wrec = 0;
				for ($i=0;$i<$num_1;$i++)
				{
					$wtotal += $data[$i][4];
					if($i % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					$wiva += $data[$i][5];
					$wcop += $data[$i][6];
					$wcmo += $data[$i][7];
					$wdes += $data[$i][8];
					$wrec += $data[$i][9];
					echo "<tr>";
					echo "<td bgcolor=".$color." align=center>".$i."</td>";
					if(isset($fac[$i]) or isset($all))
						echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]' checked></td>";
					else
						echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]'></td>";
					echo "<td bgcolor=".$color.">".$data[$i][10]."</td>";	
					echo "<td bgcolor=".$color.">".$data[$i][0]."</td>";	
					echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";	
					echo "<td bgcolor=".$color.">".$data[$i][2]."</td>";
					echo "<td bgcolor=".$color.">".$data[$i][3]."</td>";
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][4],2,'.',',')."</td>";	
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][5],2,'.',',')."</td>";	
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][6],2,'.',',')."</td>";	
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][7],2,'.',',')."</td>";	
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][8],2,'.',',')."</td>";	
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][9],2,'.',',')."</td>";	
					echo "</tr>";
					echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
					echo "<input type='HIDDEN' name= 'wfac' value='".$wfac."'>";
					echo "<input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
					echo "<input type='HIDDEN' name= 'dta' value=".$dta.">";
					echo "<input type='HIDDEN' name= 'num_1' value=".$num_1.">";
					echo "<input type='HIDDEN' name= 'wiva' value=".$wiva.">";
					echo "<input type='HIDDEN' name= 'wcop' value=".$wcop.">";
					echo "<input type='HIDDEN' name= 'wcmo' value=".$wcmo.">";
					echo "<input type='HIDDEN' name= 'wdes' value=".$wdes.">";
					echo "<input type='HIDDEN' name= 'data[".$i."][0]' value=".$data[$i][0].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][1]' value=".$data[$i][1].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][2]' value=".$data[$i][2].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][3]' value='".$data[$i][3]."'>";
					echo "<input type='HIDDEN' name= 'data[".$i."][4]' value=".$data[$i][4].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][5]' value=".$data[$i][5].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][6]' value=".$data[$i][6].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][7]' value=".$data[$i][7].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][8]' value=".$data[$i][8].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][9]' value=".$data[$i][9].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][10]' value=".$data[$i][10].">";
				}
				echo "<tr><td  bgcolor=#999999 colspan=7><b>TOTAL VENTAS : </b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wtotal,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wiva,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wcop,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wcmo,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wdes,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wrec,2,'.',',')."</b></td></tr>"; 
				echo "<tr><td align=center bgcolor=#dddddd colspan=13><font color=#000066 size=5><b>REFACTURAR</b><input type='checkbox' name='ok'></font></td></tr>";
				echo "<tr><td align=center bgcolor=#999999 colspan=13><input type='submit' value='Ok'></td></tr>"; 
				echo "</table><br><br>"; 
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>LA FACTURA TIENE MOVIMIENTO DE CARTERA No!! SE PUEDE REFACTURAR -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
			echo "<br><br>";
		}
	}
}
?>