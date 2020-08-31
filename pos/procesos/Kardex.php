<html>
<head>
  	<title>MATRIX Generacion Automatica de Kardex</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Automatica de Kardex</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> kardex.php Ver. 1.01</b></font></tr></td></table>
</center> 
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='kardex' action='Kardex.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes))
	{
		$wlinant="";
		echo "<input type='HIDDEN' name= 'wlinant' value='".$wlinant."'>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION AUTOMATICA DEL KARDEX</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$k=0;
		if($wmes == 1)
		{
			$wmesa = 12;
			$wanoa = $wano - 1;
		}
		else
		{
			$wmesa = $wmes -1;
			$wanoa = $wano;
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION AUTOMATICA DEL KARDEX</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>AÑO DE PROCESO : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr></table><br><br>";	
		$query = "SELECT count(*)  from  ".$empresa."_000007 ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$totreg=$row[0];
		$query = "SELECT max(Kxmcon),count(*)  from  ".$empresa."_000012 ";
		$query .= " where  Kxmano=".$wano;
		$query .= "     and   Kxmmes=".$wmes;
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		if($row[1] == 0)
			$ultreg=0;
		else
			$ultreg=$row[0];
		echo "<b>Registros Totales Kardex : ".$totreg." Registros Generados : ".$ultreg."</b><br><br>";
		if($ultreg < $totreg)
		{
			$ultreg++;
			$query = "SELECT Karcco, Karcod, Artnom, Artuni, Artgru  from  ".$empresa."_000007,".$empresa."_000001 ";
			$query .= "  where   Karcod = Artcod ";
			//$query .= "      and   Artest = 'on' ";
			$query .= "     ORDER BY  Karcco, Karcod  limit ".$ultreg.",2000 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$wsalantc=0;
				$wsalantv=0;
				$row = mysql_fetch_array($err);
				$query = "SELECT  Salexi, Salpro * Salexi  from ".$empresa."_000014 ";
				$query .= " where  Salano=".$wanoa;
				$query .= "     and   Salmes=".$wmesa;
				$query .= "     and   Salcco='".$row[0]."' ";
				$query .= "     and   Salcod='".$row[1]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wsalantc=$row1[0];
					$wsalantv=$row1[1];
				}
				$wenactc=0;
				$wenactv=0;
				$wsaactc=0;
				$wsaactv=0;
				$query = "SELECT  Conaca, Conaco, Conind, Mencco, Menccd, sum(Mdecan), sum(Mdevto)  from ".$empresa."_000010,".$empresa."_000008,".$empresa."_000011 ";
				$query .= " where  Menano=".$wano;
				$query .= "     and   Menmes=".$wmes;
				$query .= "     and   Mencon= Concod ";
				$query .= "     and   Mendoc= Mdedoc ";
				$query .= "     and   Mencon= Mdecon ";
				$query .= "     and   Mdeart= '".$row[1]."'";
				$query .= " group by  Conaca, Conaco, Conind, Mencco, Menccd ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						if(($row1[0] == "on" or $row1[1] == "on") and (($row1[2] == "1" and $row1[3] == $row[0]) or ($row1[2] == "0" and $row1[4] == $row[0])))
						{
							if($row1[5] == "")
								$wenactc=0;
							else
								$wenactc +=$row1[5];
							if($row1[6] == "")
								$wenactv=0;
							else
								$wenactv +=$row1[6];
						}
						if(($row1[0] == "on" or $row1[1] == "on") and (($row1[2] == "-1" and $row1[3] == $row[0]) or ($row1[2] == "0" and $row1[3] == $row[0])))
						{
							if($row1[5] == "")
								$wsaactc=0;
							else
								$wsaactc +=$row1[5];
							if($row1[6] == "")
								$wsaactv=0;
							else
								$wsaactv +=$row1[6];
						}
					}
				}
				$wsalactc=0;
				$wsalactv=0;
				$query = "SELECT  Salexi, Salpro * Salexi  from ".$empresa."_000014 ";
				$query .= " where  Salano=".$wano;
				$query .= "     and   Salmes=".$wmes;
				$query .= "     and   Salcco='".$row[0]."'";
				$query .= "     and   Salcod='".$row[1]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wsalactc=$row1[0];
					$wsalactv=$row1[1];
				}
				$wdifc=$wsalactc - ($wsalantc + $wenactc - $wsaactc);
				$wdifv=$wsalactv - ($wsalantv + $wenactv - $wsaactv);
				$wind="on";
				if($wdifc != 0 or $wdifv != 0 or $wsalantc < 0 or $wsalantv < 0 or $wenactc < 0 or $wenactv < 0 or $wsaactc < 0 or $wsaactv < 0 or $wsalactc < 0 or $wsalactv < 0)
					$wind="off";
				$wrotnv=0;
				if(($wsalantv + $wsalactv) > 0)
					$wrotnv=($wsalantv +  $wenactv - $wsalactv) / (($wsalantv + $wsalactv) / 2 );
				if($wrotnv < 0.1)
				{
					$wrotnv=0;
					$wrotdi=0;
				}
				else
					$wrotdi = 30 / $wrotnv;
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data, Kxmcon, Kxmano, Kxmmes, Kxmcco, Kxmcod, Kxmdes, Kxmuni, Kxmgru, Kxmcsi, Kxmvsi, Kxmcen, Kxmven, Kxmcsa, Kxmvsa, Kxmcsf, Kxmvsf, Kxmcdi, Kxmvdi, Kxmvro, Kxmdro, Kxmind, seguridad) ";
				$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$row[0]."','".$row[1]."','".$row[2]."','".$row[3]."','".$row[4]."',".$wsalantc.",".$wsalantv.",".$wenactc.",".$wenactv.",".$wsaactc.",".$wsaactv.",".$wsalactc.",".$wsalactv.",".$wdifc.",".$wdifv.",".$wrotnv.",".$wrotdi.",'".$wind."','C-".$empresa."')";
				$err3 = mysql_query($query,$conex);
	   			if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
	       			$k++;
	       			echo "REGISTRO INSERTADO NRo : ".$k."<br>";
				}
				$ultreg++;
			}
			echo "<b>TOTAL REGISTROS INSERTADOS  : </b>".$k."<br>";
		}
		else
		{
			echo "<br><br><center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>AÑO -- MES  : YA FUE GENERADO  !!!!</MARQUEE></FONT>";
			echo "<input type='submit' value='Continuar'></center>";
			echo "<br><br>";
		}
	}
}
?>
</body>
</html>