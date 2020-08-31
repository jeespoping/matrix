<html>
<head>
  	<title>MATRIX Programa de Facturacion a Empresas</title>
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
		document.forms.facemp.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");


/**********************************************************************************************************************  
	   PROGRAMA : facemp.php
	   Fecha de Liberación : 2006-05-19
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2010-06-16
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar la facturación de empresas o facturacion x lotes para una empresa
	   entre dos fecha determinadas y para un centro de costos en especial o para todos.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2010-06-16
	   		Se rectifica el programa para inicializar la variable wfecha con la que se valida la fecha de cambio de la resolucion.
	   		
	   .2010-05-11
	   		Se modifica el programa para grabar en la tabla 18 (Encabezado de Facturas) en el campo Fentip (Tipo de Empresa) el valor de Emptem
	   		del la tabla 24 (Maestro de Empresas) que corresponde al tipo de empresa.
	   		
	   .2010-04-06
	   		Se modifica el programa para permitir la facturacion de empresas segun el modulo hospitario (Homologacion).
	   		
	   .2006-11-08
	   		Se modifica el algoritmo de incremento del consecutivo actualizando primero y consultando despues.
	   		
	   .2007-06-13
	   		Se modifica le programa para que agrupe las ventas por numero de venta y no las repita por cada articulo.
	   		
	   .2007-06-07
	   		Se modifica le programa para que al traer las ventas de la tabla 16 verifique la presencia de datos en la tabla 17
	   		de detalle de ventas.
	   		
	   .2006-08-23
	   		Se corrige el metodo de de incremento de consecutivo de facturas en las tablas 18 y 19.
	   		
	   .2006-08-23
	   		Se corrige el programa para modificar el fuente de factura en el archivo 16 de Ventas.
	   		
	   .2006-05-19
	   		Se modifica el algoritmo de incremento del consecutivo actualizando primero y consultando despues.
	   
	   .2005-12-27
	  	 	Release de Versión 1.03
	   		Se modifico la seleccion de las ventas excluyendo las que aparecieran en el archivo  numero 55 de notas credito y anulaciones.
			
	   .2005-08-24
	   		Release de Versión 1.02
	   		Inicio del Programa a Produccion.
	   		
	   	.2005-08-24
	   		Release de Versión 1.00 1.01
	   		Release de Versión Beta. Versiones 1.00 y 1.01
	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='facemp' action='facemp.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		$query = "lock table ".$empresa."_000016 LOW_PRIORITY WRITE ";
		$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
		$query = "select Vennfa  from ".$empresa."_000016 where Vennum='".$data[0][10]."'";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS");
		$row = mysql_fetch_array($err1);
		$nrovta=$row[0];
		$query = " UNLOCK TABLES";													
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");
		if($nrovta == "")
		{
			$query = "lock table ".$empresa."_000003 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
			$query =  " update ".$empresa."_000003 set Ccofai = Ccofai + 1 where Ccocod='".substr($wcco,0,strpos($wcco,"-"))."'";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$query = "select Ccofai,Ccofaf,Ccoffa,Ccopfa from ".$empresa."_000003 where Ccocod='".substr($wcco,0,strpos($wcco,"-"))."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
			$row = mysql_fetch_array($err1);
			$wdoct=$row[0];
			$wdoctf=$row[1];
			$wfuente=$row[2];
			$wprefijo=$row[3];
			$wfecha=date("Y-m-d");
			$query = " UNLOCK TABLES";													
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");
			if($wdoct <= $wdoctf and $wfac > 0)
			{
				$query  = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom ";
				$query .= "   FROM ".$empresa."_000049 ";
				$query .= "  WHERE cfgcco = '".substr($wcco,0,strpos($wcco,"-"))."'";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row1 = mysql_fetch_array($err1);
				if ($row1[9] > $wfecha)
				{
					$wnrores=$row1[8];  //Nro Resolucion Anterior
					$wfecres=$row1[5];  //Fecha Resolucion Anterior
					$wfacini=$row1[6];  //Factura Inicial Anterior 
					$wfacfin=$row1[7];  //Factura Final Anterior 
				}
				else
				{
					$wnrores=$row1[13];  //Nro Resolucion Actual
					$wfecres=$row1[10];  //Fecha Resolucion Actual
					$wfacini=$row1[11];  //Factura Inicial Anterior 
					$wfacfin=$row1[12];  //Factura Final Anterior 
				}
				 $wresolucion= "Resolución Nro: ".$wnrores." del ".$wfecres.", <BR>"
		                   ."factura ".$wfacini." a la factura ".$wfacfin."<BR>"
		                   ."Esta factura cambiaria de compraventa se asimila en <BR>"
		                   ."todos sus efectos a una letra de cambio, Art. 621 y <BR>"
		                   ."SS, 671 y SS 772, 773, 770 y SS del código de comercio.<BR>"
		                   ."Factura impresa por computador cumpliendo con los<BR>"
		                   ."requisitos del Art. 617 del E.T.<BR>";
		                   
				$wsw=0;
				$ventas=array();
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				//INSTRUCCION MODIFICADA EN 2010-05-11  se adiciono Emptem
				$query = "select Empnit, Empres, Emptem  from ".$empresa."_000024 where Empcod='".substr($wemp,0,strpos($wemp,"-"))."'";
				$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO EMPRESAS");
				$row = mysql_fetch_array($err1);
				//INSTRUCCION MODIFICADA EN 2010-05-11
				$query = "insert ".$empresa."_000018 (medico,fecha_data,hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fensal, Fenest, Fencre, Fenpde, Fenrec, Fentop, Fenhis, Fening, Fenesf, Fenrln, Fencco, Fenrbo, Fenimp, Fendpa, Fennpa, Fendev, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".substr($fecha,0,4).",".substr($fecha,5,2).",'".$fecha."','".$wfuente."','".$wprefijo."-".$wdoct."','".$row[2]."','".$row[0]."','".substr($wemp,0,strpos($wemp,"-"))."','".$row[1]."',".$wfac.",".$wiva.",".$wcop.",".$wcmo.",".$wdes.",0,0,0,".$wfac.",'on',1,0,100,0,'','','GE','".$wresolucion."','".substr($wcco,0,strpos($wcco,"-"))."',0,'on','".$row[0]."','VARIOS CLIENTES',0,'C-".$key."')";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				for ($i=0;$i<$num;$i++)
				{
					if(isset($fac[$i]))
					{
						$wsw++;
						$ventas[$wsw]=$data[$i][10];
						$query = "insert ".$empresa."_000019 (medico,fecha_data,hora_data, Fdeffa, Fdefac, Fdenve, Fdeest, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wfuente."','".$wprefijo."-".$wdoct."','".$data[$i][10]."','on','C-".$key."')";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$query =  " update ".$empresa."_000016 set Vennfa = '".$wprefijo."-".$wdoct."', Venffa='".$wfuente."'  where Vennum='".$data[$i][10]."'";
						$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
				}
				if($wsw > 0)
				{
					$items=" IN (".chr(34).$ventas[1].chr(34);
					for ($i=2;$i<=$wsw;$i++)
					{
						$items .= ",".chr(34).$ventas[$i].chr(34);
					}
					$items .= ")";
					$query = "select farpmla_000016.Vencco,mid(farpmla_000001.Artgru,1,3),sum(farpmla_000017.Vdevun*farpmla_000017.Vdecan),round(sum(((farpmla_000017.Vdevun*farpmla_000017.Vdecan)/(1+(farpmla_000017.Vdepiv/100)))*(farpmla_000017.Vdepiv/100)),0) ";
					$query .= " from farpmla_000017,farpmla_000016,farpmla_000001 "; 
					$query .= " where farpmla_000017.vdenum ".$items;
					$query .= "   and farpmla_000017.vdenum = farpmla_000016.Vennum  ";
					$query .= "   and farpmla_000017.vdeart = farpmla_000001.artcod  ";
					$query .= "  group by 1,2 ";
					$query .= "  order by 1 ";
					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTASX CPTO");
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($err1);
							$query = "insert ".$empresa."_000065 (medico,fecha_data,hora_data, Fdefue, Fdedoc, Fdecco, Fdecon, Fdevco, Fdeter, Fdepte, Fdevde, Fdesal, Fdeest, fdeffa, fdefac, fdeviv, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wfuente."','".$wprefijo."-".$wdoct."','".$row1[0]."','".$row1[1]."',".$row1[2].",'',0,0,0,'on','".$wfuente."','".$wprefijo."-".$wdoct."',".$row1[3].",'C-".$key."')";
							$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						}
					}
				}
				if($wsw > 0)
				{
					$items=" IN (".chr(34).$ventas[1].chr(34);
					for ($i=2;$i<=$wsw;$i++)
					{
						$items .= ",".chr(34).$ventas[$i].chr(34);
					}
					$items .= ")";
					$query = "select farpmla_000017.id,sum(farpmla_000017.Vdevun*farpmla_000017.Vdecan) ";
					$query .= " from farpmla_000017 "; 
					$query .= " where farpmla_000017.vdenum ".$items;
					$query .= "  group by 1 ";
					$query .= "  order by 1 ";
					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS X ID");
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($err1);
							$query = "insert ".$empresa."_000066 (medico,fecha_data,hora_data, Rcfffa, Rcffac, Rcfreg, Rcfval, Rcfest, Rcftip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wfuente."','".$wprefijo."-".$wdoct."','".$row1[0]."',".$row1[1].",'on','R','C-".$key."')";
							$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						}
					}
				}
				echo "<center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1><B><font size=5>FACTURA ".$wprefijo."-".$wdoct." GENERADA</font></B></MARQUEE></FONT>";
				echo "<center><br><br>";
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>ERROR !!! ESTAS VENTAS YA FUERON FACTURADAS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
			echo "<br><br>";
		}
		unset($ok);
		unset($wemp);
	}
	if (!isset($wfini) or !isset($wffin) or !isset($wcco) or !isset($wemp) or (isset($wfini) and isset($wffin) and isset($wcco) and isset($wemp) and substr($wemp,strrpos($wemp,"-")+1) == "on" and !isset($wtip)))
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Powered by : MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>FACTURACION POR EMPRESA</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2010-06-16</font></b></font></td></tr>";
		if(isset($wfini))
			echo "<tr><td  bgcolor=".$wcolor.">Fecha Inicial (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wfini' value = '".$wfini."'></td></tr>";
		else
			echo "<tr><td  bgcolor=".$wcolor.">Fecha Inicial (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wfini' value = '".date("Y-m-d")."'></td></tr>";
		if(isset($wffin))
			echo "<tr><td  bgcolor=".$wcolor.">Fecha Final (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wffin' value = '".$wffin."'></td></tr>";
		else
			echo "<tr><td  bgcolor=".$wcolor.">Fecha Final (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wffin' value = '".date("Y-m-d")."'></td></tr>";
		$query =  " SELECT Empres, Empnom, Emptde FROM ".$empresa."_000024  where Empfac= 'off' ORDER BY empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=".$wcolor.">Empresa : </td><td bgcolor=".$wcolor."><select name='wemp'>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err); 
			if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."-".$row[2]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."-".$row[2]."</option>";
		}
		echo "</select></td></tr>";
		if(isset($wemp) and substr($wemp,strrpos($wemp,"-")+1) == "on")
		{
			$query =  " SELECT Subcodigo, Descripcion FROM det_selecciones  where Medico= '".$empresa."' and codigo='014' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<tr><td bgcolor=".$wcolor.">Tipo de Envio : </td><td bgcolor=".$wcolor."><select name='wtip'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err); 
				if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
					echo "<option selected>".$row[0]."-".$row[1]."-".$row[2]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select></td></tr>";
		}
		$query =  " SELECT ccocod, ccodes  FROM ".$empresa."_000003  ORDER BY ccocod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=".$wcolor.">Centro de Costo : </td><td bgcolor=".$wcolor."><select name='wcco'>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err); 
			if(isset($wcco) and substr($wcco,0,strpos($wcco,"-")) == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Ok'></td></tr>"; 
		echo "</table>";  
	}
	else
	{
		if(isset($wtip))
			echo "<input type='HIDDEN' name= 'wtip' value='".$wtip."'>";
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>FACTURACION POR EMPRESA</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2010-06-16</font></b></font></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Fecha Inicial </td><td bgcolor=".$wcolor.">".$wfini."</td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Fecha Final </td><td bgcolor=".$wcolor.">".$wffin."</td></tr>";
		echo "<tr><td bgcolor=".$wcolor.">Empresa : </td><td bgcolor=".$wcolor.">".$wemp."</td></tr>";
		echo "<tr><td bgcolor=".$wcolor.">Centro de Costo : </td><td bgcolor=".$wcolor.">".$wcco."</td></tr>";
		echo "</table><br><br>";  
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=13><font color=#000066 size=5><b>DETALLE DE FACTURAS</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >NRO ITEM</font></td><td align=center bgcolor=#000066><font color=#ffffff >FACTURAR</font></td><td align=center bgcolor=#000066><font color=#ffffff >VENTA <BR> NRo.</font></td><td align=center bgcolor=#000066><font color=#ffffff >C. DE C.</font></td><td align=center bgcolor=#000066><font color=#ffffff >FECHA</font></td><td align=center bgcolor=#000066><font color=#ffffff >CEDULA</font></td><td align=center bgcolor=#000066 ><font color=#ffffff >PACIENTE</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR<BR>TOTAL </font><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> IVA</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> COPAGO</font></td></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> CUOTA<BR>MODERADORA</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> DESCUENTO</font></td><td align=center bgcolor=#000066><font color=#ffffff >VALOR <BR> RECARGO</font></td></tr>";
 		echo "<tr><td align=center bgcolor=#999999 colspan=13><font color=#000066 size=5><b>Marcar Todos</b><input type='checkbox' name='all'  onclick='enter()'></font></td></tr>";
		if(!isset($dta))
 		{
	 		$dta=0;
	 		if(isset($wtip))
	 			$query = "select Vencco, Venfec, Vennit, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Vennum from  ".$empresa."_000016, ".$empresa."_000017, ".$empresa."_000050 ";
	 		else
				$query = "select Vencco, Venfec, Vennit, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Vennum from  ".$empresa."_000016, ".$empresa."_000017 ";
	 		$query .= " where Vencco = '".substr($wcco,0,strpos($wcco,"-"))."'";
			$query .= "   and Venfec between '".$wfini."'  and '".$wffin."'";
			$query .= "   and Vencod = '".substr($wemp,0,strpos($wemp,"-"))."'";
			$query .= "   and Venest = 'on' ";
			$query .= "   and Vennfa = '' ";
			$query .= "   and Vennum = Vdenum ";
			$query .= "   and Vennum not in (select Traven from ".$empresa."_000055) ";
			if(isset($wtip))
			{
				$query .= "   and Vennum = Vmpvta ";
				$query .= "   and Vmptde = '".substr($wtip,strpos($wtip,"-")+1)."' ";
			}
			$query .= "   group by Vennum ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$dta=1;
				for ($i=0;$i<$num;$i++)
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
			$wfac = 0;
			$wiva = 0;
			$wcop = 0;
			$wcmo = 0;
			$wdes = 0;
			for ($i=0;$i<$num;$i++)
			{
				$wtotal += $data[$i][4];
				if($i % 2 == 0)
					$color="#dddddd";
				else
					$color="#cccccc";
				echo "<tr>";
				echo "<td bgcolor=".$color." align=center>".$i."</td>";
				if(isset($fac[$i]) or isset($all))
				{
					$wfac += $data[$i][4];
					$wiva += $data[$i][5];
					$wcop += $data[$i][6];
					$wcmo += $data[$i][7];
					$wdes += $data[$i][8];
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]' checked></td>";
				}
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
				echo "<input type='HIDDEN' name= 'wfini' value='".$wfini."'>";
				echo "<input type='HIDDEN' name= 'wffin' value='".$wffin."'>";
				echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
				echo "<input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<input type='HIDDEN' name= 'dta' value=".$dta.">";
				echo "<input type='HIDDEN' name= 'num' value=".$num.">";
				echo "<input type='HIDDEN' name= 'wfac' value=".$wfac.">";
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
			echo "<tr><td  bgcolor=#999999 colspan=7><b>TOTAL FACTURAS SELECCIONADAS : </b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wfac,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wiva,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wcop,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wcmo,2,'.',',')."</b></td><td bgcolor=#999999 align=right><b>$".number_format((double)$wdes,2,'.',',')."</b></td><td align=center bgcolor=#999999>&nbsp</td></tr>"; 
			echo "<tr><td  bgcolor=#999999 colspan=7><b>TOTAL FACTURAS  : </td><td bgcolor=#999999 align=right><b>$".number_format((double)$wtotal,2,'.',',')."</b></td><td align=center bgcolor=#999999 colspan=5>&nbsp</td></tr>"; 
			echo "<tr><td align=center bgcolor=#dddddd colspan=13><font color=#000066 size=5><b>FACTURAR</b><input type='checkbox' name='ok'></font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=13><input type='submit' value='Ok'></td></tr>"; 
			echo "</table><br><br>"; 
		}
	}
}
?>