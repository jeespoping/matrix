<html>
<head>
  	<title>MATRIX Generacion Automatica de Kardex</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Automatica de Kardex</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Tkardex.php Ver. 2015-08-15</b></font></tr></td></table>
</center>
<?php
/*****************************************************************************
 * Modificaciones:
 *
 * Diciembre 16 de 2020: Se agrega order by a la consulta
 *****************************************************************************/


include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[8] > $vec2[8])
		return 1;
	elseif ($vec1[8] < $vec2[8])
				return -1;
			else
				return 0;
}

function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
						$ls=$lm;
					else
						$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Tkardex' action='TKardex.php' method=post>";




	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes))
	{
		$wlinant="";
		echo "<input type='HIDDEN' name= 'wlinant' value='".$wlinant."'>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION AUTOMATICA DEL KARDEX</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "delete  from ".$empresa."_000012 ";
		$query .= "  where Kxmano = ".$wano;
		$query .= "       and Kxmmes = ".$wmes;
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
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
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION AUTOMATICA DEL KARDEX  Ver 2007-01-09</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>A&Ntilde;O DE PROCESO : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr></table><br><br>";
		$dsan=array();
		$query = "SELECT  Salcco, Salcod, Salexi, Salpro * Salexi  from ".$empresa."_000014 ";
		$query .= " where  Salano=".$wanoa;
		$query .= "     and   Salmes=".$wmesa;
		$query .= "     ORDER BY  Salcco, Salcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$totsan=$num;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$row[1]=strtoupper(str_replace("Ñ","N",$row[1]));
			$dsan[$i][0]=$row[0].$row[1];
			$dsan[$i][1]=$row[2];
			$dsan[$i][2]=$row[3];
		}
		$dsac=array();
		$query = "SELECT  Salcco, Salcod, Salexi, Salpro * Salexi, Salpro  from ".$empresa."_000014 ";
		$query .= " where  Salano=".$wano;
		$query .= "     and   Salmes=".$wmes;
		$query .= "     ORDER BY  Salcco, Salcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$totsac=$num;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$row[1]=strtoupper(str_replace("Ñ","N",$row[1]));
			$dsac[$i][0]=$row[0].$row[1];
			$dsac[$i][1]=$row[2];
			$dsac[$i][2]=$row[3];
			$dsac[$i][3]=$row[4];
		}
		$query = "SELECT MAX(LENGTH(Artcod)) from  ".$empresa."_000001 ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$wmaxL = $row[0];
		$data=array();
		$query = "SELECT  Mdeart, Conaca, Conaco, Conind, Mencco, Menccd, sum(Mdecan), sum(Mdevto)  from ".$empresa."_000010,".$empresa."_000011,".$empresa."_000008 ";
		$query .= "   where  Menano = ".$wano;
		$query .= "     and  Menmes = ".$wmes;
		$query .= "     and  Menest = 'on' ";
		$query .= "     and  Mendoc = Mdedoc ";
		$query .= "     and  Mencon = Mdecon ";
		$query .= "     and  Mencon = Concod ";
		$query .= " group by Mdeart, Conaca, Conaco, Conind, Mencco, Menccd ";
		$query .= " order by Mencco, Mdeart ";	//Linea agregada el: Diciembre 16 de 2020
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$tot=$num;
		$wart="";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$row[0]=strtoupper(str_replace("Ñ","N",$row[0]));
			$data[$i][0]=$row[0];
			$data[$i][1]=$row[1];
			$data[$i][2]=$row[2];
			$data[$i][3]=$row[3];
			$data[$i][4]=$row[4];
			$data[$i][5]=$row[5];
			$data[$i][6]=$row[6];
			$data[$i][7]=$row[7];
			if($row[0] != $wart)
			{
				$wart=$row[0];
				$ka=0;
			}
			$data[$i][8]=$row[0];
			while(strlen($data[$i][8]) < $wmaxL)
				$data[$i][8] .= ".";
			$data[$i][8] = $data[$i][8].$ka;
			$ka++;
		}
		usort($data,'comparacion');
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
			//$query .= "     ORDER BY  Karcco, Karcod  limit ".$ultreg.",5000 ";
			$query .= "     ORDER BY  Karcco, Karcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$wsalantc=0;
				$wsalantv=0;
				$row = mysql_fetch_array($err);
				$pos=bi($dsan,$totsan,$row[0].$row[1],0);
				if($pos != -1)
				{
					$wsalantc=$dsan[$pos][1];
					$wsalantv=$dsan[$pos][2];
				}
				$wenactc=0;
				$wenactv=0;
				$wsaactc=0;
				$wsaactv=0;
				$wcodmax=$row[1];
				while(strlen($wcodmax) < $wmaxL)
					$wcodmax .= ".";
				$wcodmax .= "0";
				$j=bi($data,$tot,$wcodmax,8);
				while( $j >= 0 and $j < $tot and strtoupper($data[$j][0]) == strtoupper($row[1]))
				//for ($j=0;$j<$tot;$j++)
				{
					if(strtoupper($data[$j][0]) == strtoupper($row[1]) and ($data[$j][1] == "on" or $data[$j][2] == "on") and (($data[$j][3] == "1" and $data[$j][4] == $row[0]) or ($data[$j][3] == "0" and $data[$j][5] == $row[0])))
					{
						if($data[$j][6] == "")
							$wenactc=0;
						else
							$wenactc +=$data[$j][6];
						if($data[$j][7] == "")
							$wenactv=0;
						else
							$wenactv +=$data[$j][7];
					}
					if(strtoupper($data[$j][0]) == strtoupper($row[1]) and ($data[$j][1] == "on" or $data[$j][2] == "on") and (($data[$j][3] == "-1" and $data[$j][4] == $row[0]) or ($data[$j][3] == "0" and $data[$j][4] == $row[0])))
					{
						if($data[$j][6] == "")
							$wsaactc=0;
						else
							$wsaactc +=$data[$j][6];
						if($data[$j][7] == "")
							$wsaactv=0;
						else
							$wsaactv +=$data[$j][7];
					}
					$j++;
				}
				$wsalactc=0;
				$wsalactv=0;
				$wsalactvv=0;
				$pos=bi($dsac,$totsac,$row[0].$row[1],0);
				if($pos != -1)
				{
					$wsalactc=$dsac[$pos][1];
					$wsalactv=$dsac[$pos][2];
					$wsalactvv=$dsac[$pos][3];
				}
				$wdifc=$wsalactc - ($wsalantc + $wenactc - $wsaactc);
				$wdifv=$wsalactv - ($wsalantv + $wenactv - $wsaactv);
				if($wsalactc == 0 and $wdifc == 0 and abs($wdifv) > 0.9 and ($wsalactvv - abs($wdifv)) < 0.1)
					$wdifv=$wsalactvv - ($wsalantv + $wenactv - $wsaactv);
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
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>A&Ntilde;O -- MES  : YA FUE GENERADO  !!!!</MARQUEE></FONT>";
			echo "<input type='submit' value='Continuar'></center>";
			echo "<br><br>";
		}
	}
}
?>
</body>
</html>
