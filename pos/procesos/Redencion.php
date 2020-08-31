<html>
<head>
  	<title>MATRIX Programa de Redencion de Puntos</title>
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
		document.forms.redencion.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");


/**********************************************************************************************************************  
	   PROGRAMA : redencion.php
	   Fecha de Liberación : 2006-11-27
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2008-03-07
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite redimir en articulos especificos los puntos
	   acumulados en las compras hechas a traves de los puestos de venta (POS).
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2008-03-07
	   		Se modifico el programa para calcular correctamente el valor total multiplicando Costo Promedio X Cantidad
	   		la correccion anterior se hubico en el lugar incorrecto.
	   		
	   .2008-01-04
	   		Se modifico el programa para calcular correctamente el valor total multiplicando Costo Promedio X Cantidad.
	   		
	   .2006-11-27
	   		Release de Versión. Version 2006-11-27
	   	
	   .2007-11-07
	   		Se modifico la validacion de la entrada de datos para no dejar pasar cedulas en nulo.
	   
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
	echo "<form name='redencion' action='Redencion.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		$query = "lock table ".$empresa."_000007 LOW_PRIORITY WRITE, ".$empresa."_000008 LOW_PRIORITY WRITE,".$empresa."_000010 LOW_PRIORITY WRITE,".$empresa."_000011 LOW_PRIORITY WRITE,".$empresa."_000060 LOW_PRIORITY WRITE,".$empresa."_000079 LOW_PRIORITY WRITE ";
		$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO ".mysql_errno().":".mysql_error());
		$query = "select Concon, Concod from ".$empresa."_000008 where Condes LIKE '%REDENCION%' ";
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
		$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data, Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit,Menusu, Menfac, Menest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanot.",".$wmest.",".$wdoct.",'".$wcont."','".date("Y-m-d")."','".$bodrep."','".$bodrep."','.',0,'0','".$key."','.','on ','C-".$empresa."')";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ENCABEZADO DE MOVIMIENTO ".mysql_errno().":".mysql_error());
		for ($i=0;$i<$numr;$i++)
		{
			if(isset($fac[$i]))
			{
				$query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_000007 where Karcod='".$data[$i][1]."' and Karcco='".$bodrep."'";
				$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ".mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$exi=$row1[0];
					$exi=$exi - $data[$i][3];
					$pro=$row1[1];
					if($exi >= 0 and $pro >= 0)
					{
						$query =  " update ".$empresa."_000007 set Karexi = ".number_format((double)$exi,4,'.','').",Karpro=".number_format((double)$pro,4,'.','')."  where Karcod='".$data[$i][1]."' and Karcco='".$bodrep."'";
						$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO KARDEX ".mysql_errno().":".mysql_error());
					}
					$data[$i][9]=$data[$i][9]*$data[$i][3];
					$query = "insert ".$empresa."_000011 (medico,fecha_data,hora_data, Mdecon, Mdedoc, Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdeest, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wcont."',".$wdoct.",'".$data[$i][1]."',".$data[$i][3].",".$data[$i][9].",".$data[$i][13].",'0000-00-00','.','on ','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DETALLE DE MOVIMIENTO ".mysql_errno().":".mysql_error());
					if(date("Y-m-d") > $data[$i][11])
						$wtar=$data[$i][12];
					else
						$wtar=$data[$i][10];
					$query = "insert ".$empresa."_000079 (medico,fecha_data,hora_data, Redcon, Redcto, Reddoc, Redart, Redcan, Redpun, Redcos, Redtar, Redest, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wdoct."','".$wcont."',".$wced.",'".$data[$i][1]."',".$data[$i][3].",".$data[$i][5].",".$data[$i][9].",".$wtar.",'on ','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO MOVIMIENTO DE REDENCIONES ".mysql_errno().":".mysql_error());
				}
			}
		}
		$query =  " update ".$empresa."_000060 set Salsal = Salsal - ".$wtot1.", Salred = Salred + ".$wtot1."  where Saldto='".$wced."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO SALDOS DE PUNTOS ".mysql_errno().":".mysql_error());
		$query = " UNLOCK TABLES";													
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS ".mysql_errno().":".mysql_error());
		unset($ok);
		unset($wced);
		echo "<ol>";
		echo "<li><A HREF='/matrix/pos/reportes/Impred.php?empresa=".$empresa."&wcon=".$wdoct."' target='_blank'><B>Imprimir Documento Nro. ".$wdoct."</B></A>";
		echo "</ol>";
	}
	if (!isset($wced) or $wced == "" or !isset($wsel) or $wsel == "off")
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Powered by : MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>REDENCION DE PUNTOS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-03-07</font></b></font></td></tr>";
		if (isset($wced))
			echo "<tr><td  bgcolor=".$wcolor.">Cedula : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wced' size=20 maxlength=20 value='".$wced."' ></td></tr>";
		else
			echo "<tr><td  bgcolor=".$wcolor.">Cedula : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wced' size=20 maxlength=20 ></td></tr>";
		$wnom="NO ESPECIFICO";
		$wsel="off";
		if (isset($wced) and $wced != "")
		{
			$query =  " SELECT Clidoc, Clinom  FROM ".$empresa."_000041 where Clidoc= '".$wced."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wnom=$row[1];
				$wsel="on";
			}
			else
			{
				$query =  " SELECT Meddoc, Mednom  FROM ".$empresa."_000051 where Meddoc= '".$wced."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$wnom=$row[1];
					$wsel="on";
				}
			}
		}
		echo "<tr><td  bgcolor=".$wcolor.">Nombre : </td><td bgcolor=".$wcolor.">".$wnom."</td></tr>";
		echo "<input type='HIDDEN' name= 'wsel' value=".$wsel.">";
		echo "<input type='HIDDEN' name= 'wnom' value='".$wnom."'>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Ok'></td></tr>"; 
		echo "</table>";  
	}
	else
	{
		$wtot=0;
		$query =  " SELECT Saldto, Salsal   FROM ".$empresa."_000060  where Saldto= '".$wced."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wtot=$row[1];
		}
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>REDENCION DE PUNTOS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-03-07</font></b></font></td></tr>";
		echo "<tr><td bgcolor=".$wcolor.">Cliente : </td><td bgcolor=".$wcolor.">".$wced."-".$wnom."</td></tr>";
		echo "<tr><td bgcolor=".$wcolor.">Puntos Acumulados : </td><td bgcolor=".$wcolor.">".$wtot."</td></tr>";
		echo "</table><br><br>";  
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=11><font color=#000066 size=5><b>DETALLE DE ARTICULOS A REDIMIR</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >NRO ITEM</font></td><td align=center bgcolor=#000066><font color=#ffffff >CAPACIDAD</font></td><td align=center bgcolor=#000066><font color=#ffffff >REDIMIR</font></td><td align=center bgcolor=#000066><font color=#ffffff >CODIGO</font></td><td align=center bgcolor=#000066><font color=#ffffff >DESCRIPCION</font></td><td align=center bgcolor=#000066><font color=#ffffff >CANTIDAD</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXISTENCIAS</font></td><td align=center bgcolor=#000066><font color=#ffffff >PUNTOS X <BR>ARTICULO</font></td><td align=center bgcolor=#000066 ><font color=#ffffff >PUNTOS<BR>TOTALES</font></td></tr>";
		if(!isset($dta))
 		{
	 		$dta=0;
	 		$query = "select Ccocod from ".$empresa."_000003 WHERE Ccotip= 'R' ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$bodrep=$row[0];
	 		$query = "select Catcod,Artnom,Artgru,Grudes,Catpun,Karexi,Karpro,Mtavan,Mtafec,Mtavac,Artiva  from ".$empresa."_000061,".$empresa."_000001,".$empresa."_000004,".$empresa."_000007,".$empresa."_000026 ";
	 		$query .= " where catest = 'on' ";
			$query .= "       and catcod = Artcod "; 
			$query .= "       and mid(Artgru,1,3) = Grucod ";
			$query .= "       and Catcod = Karcod ";
			$query .= "       and Karcco = '".$bodrep."' ";
			$query .= "       and Catcod = mid(Mtaart,1,locate('-',Mtaart) - 1) ";
			$query .= "       and mid(Mtatar,1,2) = '01' ";
			$query .= "       and mid(Mtacco,1,4) = '3062' ";
			$query .= " order by artgru,catcod "; 
			$err = mysql_query($query,$conex);
			$numr = mysql_num_rows($err);
			//echo $query;
			
			if($numr > 0)
			{
				$data=array();
				$dta=1;
				for ($i=0;$i<$numr;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i][0]=$i + 1;
					$data[$i][1]=$row[0];
					$data[$i][2]=$row[1];
					$data[$i][3]=1;
					$data[$i][4]=$row[4];
					$data[$i][5]=0;
					$data[$i][6]=$row[2];
					$data[$i][7]=$row[3];
					$data[$i][8]=$row[5];
					$data[$i][9]=$row[6];
					$data[$i][10]=$row[7];
					$data[$i][11]=$row[8];
					$data[$i][12]=$row[9];
					$data[$i][13]=$row[10];
				}
			}
		}
		if($dta == 1)
		{
			$wgru="";
			$wtot1 = 0;
			for ($j=0;$j<$numr;$j++)
				if(isset($fac[$j]) and $data[$j][8] > 0)
					$wtot1 += $data[$j][3] * $data[$j][4];
			for ($i=0;$i<$numr;$i++)
			{
				if($wgru != substr($data[$i][6],0,3))
				{
					$wgru = substr($data[$i][6],0,3);
					echo "<tr><td align=center bgcolor=#CCCCFF colspan=9><font color=#000066 size=3><b>LINEA : ".$data[$i][7]."</b></font></td></tr>";
				}
				if(!validar($data[$i][3]))
					$data[$i][3]=1;
				if($data[$i][3] > $data[$i][8])
					$data[$i][3]=$data[$i][8];
				$data[$i][5] = $data[$i][3] * $data[$i][4];
				if($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				echo "<tr>";
				echo "<td bgcolor=".$color." align=center>".$data[$i][0]."</td>";
				$wsaldo=$wtot - $wtot1;
				if((!isset($fac[$i]) and $data[$i][5] <= $wsaldo) or (isset($fac[$i]) and $data[$i][5] <= $wtot) )
					echo "<td align=center bgcolor=".$color."><IMG SRC='/matrix/images/medical/Pos/si.png'></td>";
				else
					echo "<td align=center bgcolor=".$color."><IMG SRC='/matrix/images/medical/Pos/no.png'></td>";
				if(isset($fac[$i]))
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]' checked onclick='enter()'></td>";
				else
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]' onclick='enter()'></td>";
				echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";	
				echo "<td bgcolor=".$color.">".$data[$i][2]."</td>";	
				echo "<td bgcolor=".$color." align=right><INPUT TYPE='text' NAME='data[".$i."][3]' size=6 maxlength=6 value='".$data[$i][3]."' onblur='enter()'></td>";
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][8],2,'.',',')."</td>";		
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][4],0,'.',',')."</td>";	
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][5],0,'.',',')."</td>";	
				echo "</tr>";
				echo "<input type='HIDDEN' name= 'bodrep' value='".$bodrep."'>";
				echo "<input type='HIDDEN' name= 'numr' value='".$numr."'>";
				echo "<input type='HIDDEN' name= 'dta' value='".$dta."'>";
				echo "<input type='HIDDEN' name= 'wced' value='".$wced."'>";
				echo "<input type='HIDDEN' name= 'wtot' value='".$wtot."'>";
				echo "<input type='HIDDEN' name= 'wtot1' value='".$wtot1."'>";
				echo "<input type='HIDDEN' name= 'wsel' value='".$wsel."'>";
				echo "<input type='HIDDEN' name= 'wnom' value='".$wnom."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][0]' value=".$data[$i][0].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][2]' value='".$data[$i][2]."'>";
				//echo "<input type='HIDDEN' name= 'data[".$i."][3]' value=".$data[$i][3].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][4]' value=".$data[$i][4].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][5]' value=".$data[$i][5].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][6]' value='".$data[$i][6]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][7]' value='".$data[$i][7]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][8]' value='".$data[$i][8]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][9]' value='".$data[$i][9]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][10]' value='".$data[$i][10]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][11]' value='".$data[$i][11]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][12]' value='".$data[$i][12]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][13]' value='".$data[$i][13]."'>";
			}
			echo "<tr><td  bgcolor=#999999 colspan=8><b>TOTAL PUNTOS A REDIMIR : </td><td bgcolor=#999999 align=right><b>".number_format((double)$wtot1,0,'.',',')."</b></td></tr>"; 
			if ($wsaldo < 0)
				echo "<tr><td  bgcolor=#999999 colspan=8><b>SALDO EN PUNTOS : </td><td bgcolor=#999999 align=right><font color=#FF0000><b>".number_format((double)$wsaldo,2,'.',',')."</b></font></td></tr>"; 
			else
				echo "<tr><td  bgcolor=#999999 colspan=8><b>SALDO EN PUNTOS : </td><td bgcolor=#999999 align=right><b>".number_format((double)$wsaldo,2,'.',',')."</b></td></tr>"; 
			if($wtot1 <= $wtot and $wtot1 > 0)
				echo "<tr><td align=center bgcolor=#dddddd colspan=9><font color=#000066 size=5><b>REDIMIR</b><input type='checkbox' name='ok'></font></td></tr>";
			else
				echo "<tr><td align=center bgcolor=#dddddd colspan=9><font color=#FF0000 size=5><b>LOS PUNTOS SELECIONADOS SUPERAN LOS PUNTOS ACUMULADOS O NO HA HECHO SELECCION</b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=9><input type='submit' value='Ok'></td></tr>"; 
			echo "</table><br><br>"; 
		}
	}
}
?>
