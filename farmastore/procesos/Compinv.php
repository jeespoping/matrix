<html>
<head>
  	<title>MATRIX</title>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function bt($lin,$arr,$numl)
{
	$bt=0;
	for ($j=0;$j<$numl;$j++)
		if($lin == $arr[$j][0])
			$bt=$j;
	return $bt;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='compinv' action='compinv.php' method=post>";
	

	

	if(!isset($wfeci) or !isset($wfecf) or !isset($wcons) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N"))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE COMPROBANTE CONTABLE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfeci' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfecf' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Consecutivo Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcons' size=6 maxlength=6></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Comprobante Definitivo? (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wserv=strtoupper ($wserv);
		$query = "SELECT Icllin, Iclcue  from farstore_000039 ";
		$err = mysql_query($query,$conex);
		$numl = mysql_num_rows($err);
		$lineas=array();
		for ($i=0;$i<$numl;$i++)
		{
			$row = mysql_fetch_array($err);
			$lineas[$i][0]=$row[0];
			$lineas[$i][1]=$row[1];
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/FARMASTORE/logo farmastore.png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION DE COMPROBANTE CONTABLE</font> Ver 1.02</b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wfeci."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wfecf."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Consecutivo Inicial : </b>".$wcons."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Comprobante Definitivo : </b>".$wserv."</td></tr>";	
		echo "</tr></table><br><br>";
		$query = "SELECT  Mencon, Mennit, Mendoc, Mencco, Menccd, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, conpro, Iccnig  from farstore_000010,farstore_000008,farstore_000038 ";
		$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
		$query .= "     and   Mencon=Concod ";
		$query .= "     and   Congec='on' ";
		$query .= "     and   Mencon=Icccon ";
		$query .= "    Order by Iccfue,Mencon ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotd=0;
		$wtotc=0;
		$wtotgd=0;
		$wtotgc=0;
		$wconant="";
		$wfueant="";
		$cl=0;
		$wcini=$wcons;
		$wcons=$wcons - 1;
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FUENTE</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOCUMENTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C. DE C.</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>NIT</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CUENTA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DEBITO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CREDITO</b></font></td></tr>";
		if($wserv == "S")
		{
			$conex_o = odbc_connect('confar','','');
			$date=$wfecf;
		}
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			
			$wsw=1;
			if($wconant != $row[0])
			{
				if($wconant != "")
				{
					if($wtip == 1)
					{
						$cl++;
						$wcons++;
						$wc=(string)$wcons;
						while(strlen($wc) < 7)
							$wc = "0".$wc;
						if($cl % 2 == 0)
							$color="#9999FF";
						else
							$color="#ffffff";
						$wexis=0;
						if($wserv == "S")
						{
							$query="select * from comovenc where movencano = '".substr($date,0,4)."' and movencmes = '".substr($date,5,2)."' and movencfue = '".$wfuente."' and  movencdoc = '".$wc."'";
							$err_o = odbc_do($conex_o,$query);
							while (odbc_fetch_row($err_o) and $wexis == 0)
								$wexis=1;
							if($wexis == 0)
							{
								$query= "insert into comovenc values ('".substr($date,0,4)."','".substr($date,5,2)."','".$wfuente."','".$wc."', '".$key."', '0') ";
								$err_o = odbc_do($conex_o,$query);
							}
						}
						else
							$wexis=1;
						if($wexis == 0)
						{
							$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',1,'".str_replace("-","/",$date)."','".$wcdb."','".$wccde."', '".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','1',".number_format((double)$wtotd,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
							$err_o = odbc_do($conex_o,$query);
						}
						echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccde."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitde."</font></td>";
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcdb."</font></td>";	
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotd,2,'.',',')."</font></td>";	
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
						$cl++;
						if($cl % 2 == 0)
							$color="#9999FF";
						else
							$color="#ffffff";
						if($wexis == 0)
						{
							$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',2,'".str_replace("-","/",$date)."','".$wccr."','".$wcccr."', '".$wnitcr."' , 'COMPROBANTE DE INVENTARIOS','2',".number_format((double)$wtotc,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
							$err_o = odbc_do($conex_o,$query);
						}
						echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";		
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcccr."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitcr."</font></td>";
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccr."</font></td>";	
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotc,2,'.',',')."</font></td></tr>";	
					}
					elseif($wtip == 2)
							{
								$wcons++;
								$wc=(string)$wcons;
								while(strlen($wc) < 7)
									$wc = "0".$wc;
								$wexis=0;
								if($wserv == "S")
								{
									$query="select * from comovenc where movencano = '".substr($date,0,4)."' and movencmes = '".substr($date,5,2)."' and movencfue = '".$wfuente."' and  movencdoc = '".$wc."'";
									$err_o = odbc_do($conex_o,$query);
									while (odbc_fetch_row($err_o) and $wexis == 0)
										$wexis=1;
									if($wexis == 0)
									{
										$query= "insert into comovenc values ('".substr($date,0,4)."','".substr($date,5,2)."','".$wfuente."','".$wc."', '".$key."', '0') ";
										$err_o = odbc_do($conex_o,$query);
									}
								}
								else
									$wexis=1;
								$lin=0;
								for ($j=0;$j<$numl;$j++)
								{
									if($lineas[$j][2] != 0)
									{
										$cl++;
										if($cl % 2 == 0)
											$color="#9999FF";
										else
											$color="#ffffff";
										if($wexis == 0)
										{
											$lin++;
											$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',".$lin.",'".str_replace("-","/",$date)."','".$lineas[$j][1]."','".$wccde."', '".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','1',".number_format((double)$lineas[$j][2],2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
											$err_o = odbc_do($conex_o,$query);
										}
										echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";	
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccde."</font></td>";	
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitde."</font></td>";
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$lineas[$j][1]."</font></td>";	
										echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$lineas[$j][2],2,'.',',')."</font></td>";	
										echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
									}
								}
								$cl++;
								if($cl % 2 == 0)
									$color="#9999FF";
								else
									$color="#ffffff";
								if($wexis == 0)
								{
									$lin++;
									$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',".$lin.",'".str_replace("-","/",$date)."','".$wccr."','".$wcccr."', '".$wnitcr."' , 'COMPROBANTE DE INVENTARIOS','2',".number_format((double)$wtotc,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
									$err_o = odbc_do($conex_o,$query);
								}
								echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";		
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcccr."</font></td>";	
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitcr."</font></td>";
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccr."</font></td>";	
								echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
								echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotc,2,'.',',')."</font></td></tr>";	
							}
							else
							{
								$cl++;
								$wcons++;
								$wc=(string)$wcons;
								while(strlen($wc) < 7)
									$wc = "0".$wc;
								$wexis=0;
								if($wserv == "S")
								{
									$query="select * from comovenc where movencano = '".substr($date,0,4)."' and movencmes = '".substr($date,5,2)."' and movencfue = '".$wfuente."' and  movencdoc = '".$wc."'";
									$err_o = odbc_do($conex_o,$query);
									while (odbc_fetch_row($err_o) and $wexis == 0)
										$wexis=1;
									if($wexis == 0)
									{
										$query= "insert into comovenc values ('".substr($date,0,4)."','".substr($date,5,2)."','".$wfuente."','".$wc."', '".$key."', '0') ";
										$err_o = odbc_do($conex_o,$query);
									}
								}
								else
									$wexis=1;
								if($cl % 2 == 0)
									$color="#9999FF";
								else
									$color="#ffffff";
								if($wexis == 0)
								{
									$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',1,'".str_replace("-","/",$date)."','".$wcdb."','".$wccde."', '".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','1',".number_format((double)$wtotd,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
									$err_o = odbc_do($conex_o,$query);
								}
								echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";		
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccde."</font></td>";	
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitde."</font></td>";
								echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcdb."</font></td>";	
								echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotd,2,'.',',')."</font></td>";	
								echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
								$lin=1;
								for ($j=0;$j<$numl;$j++)
								{
									if($lineas[$j][2] != 0)
									{
										$cl++;
										if($cl % 2 == 0)
											$color="#9999FF";
										else
											$color="#ffffff";
										if($wexis == 0)
										{
											$lin++;
											$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',".$lin.",'".str_replace("-","/",$date)."','".$lineas[$j][1]."','".$wcccr."', '".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','2',".number_format((double)$lineas[$j][2],2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
											$err_o = odbc_do($conex_o,$query);
										}
										echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";		
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcccr."</font></td>";	
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitcr."</font></td>";
										echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$lineas[$j][1]."</font></td>";	
										echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
										echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$lineas[$j][2],2,'.',',')."</font></td></tr>";	
									}
								}
							}
							if($wfueant != $row[5])
							{
								if($wfueant != "")
									$wcons=$wcini - 1;
								$wfueant = $row[5];
							}
				}
				$wconant = $row[0];
				for ($j=0;$j<$numl;$j++)
					$lineas[$j][2]=0;
				$wtotd=0;
				$wtotc=0;
				$wfuente=$row[5];
				switch ($row[7])
				{
					case "origen":
						$wccde=$row[3];
					break;
					case "destino":
						$wccde=$row[4];
					break;
					case "no":
						$wccde="00";
					break;
				}
				$wnitde="0";
				if($row[8] == "on" and $row[12] == "off")
					$wnitde=$row[13];
				if($row[8] == "on" and $row[12] == "on")
					$wnitde=$row[1];
				switch ($row[10])
				{
					case "origen":
						$wcccr=$row[3];
					break;
					case "destino":
						$wcccr=$row[4];
					break;
					case "no":
						$wcccr="00";
					break;
				}
				$wnitcr="0";
				if($row[11] == "on" and $row[12] == "off")
					$wnitcr=$row[13];
				if($row[11] == "on" and $row[12] == "on")
					$wnitcr=$row[1];
				if($row[6] != "XL" and  $row[9] != "XL")
				{
					$wtip=1;
					$wcdb=$row[6];
					$wccr=$row[9];
				}
				elseif($row[6] == "XL")
						{
							$wtip=2;
							$wccr=$row[9];
						}
						else
						{
							$wtip=3;
							$wcdb=$row[6];
						}
			}
			$query = "SELECT Mdeart, Mdevto,artgru   from farstore_000011,farstore_000001  ";
			$query .= " where  Mdecon='".$row[0]."'";
			$query .= "     and   Mdedoc=".$row[2];
			$query .= "     and Mdeart=artcod ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($wtip==1)
				{
					$wtotd +=$row1[1];
					$wtotc +=$row1[1];
					$wtotgd +=$row1[1];
					$wtotgc +=$row1[1];
				}
				elseif($wtip == 2)
						{
							$wtotc +=$row1[1];
							$lineas[bt(substr($row1[2],0,3),$lineas,$numl)][2] += $row1[1];
							$wtotgd +=$row1[1];
							$wtotgc +=$row1[1];
						}
						else
						{
							$wtotd +=$row1[1];
							$lineas[bt(substr($row1[2],0,3),$lineas,$numl)][2] += $row1[1];
							$wtotgd +=$row1[1];
							$wtotgc +=$row1[1];
						}
			}
		}
		if($wtip == 1)
		{
			$cl++;
			$wcons++;
			$wc=(string)$wcons;
			while(strlen($wc) < 7)
				$wc = "0".$wc;
			$wexis=0;
			if($wserv == "S")
			{
				$query="select * from comovenc where movencano = '".substr($date,0,4)."' and movencmes = '".substr($date,5,2)."' and movencfue = '".$wfuente."' and  movencdoc = '".$wc."'";
				$err_o = odbc_do($conex_o,$query);
				while (odbc_fetch_row($err_o) and $wexis == 0)
					$wexis=1;
				if($wexis == 0)
				{
					$query= "insert into comovenc values ('".substr($date,0,4)."','".substr($date,5,2)."','".$wfuente."','".$wc."', '".$key."', '0') ";
					$err_o = odbc_do($conex_o,$query);
				}
			}
			else
				$wexis=1;
			if($cl % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			if($wexis == 0)
			{
				$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',1,'".str_replace("-","/",$date)."','".$wcdb."','".$wccde."', '".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','1',".number_format((double)$wtotd,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
				$err_o = odbc_do($conex_o,$query);
			}
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";		
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccde."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitde."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcdb."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotd,2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
			$cl++;
			if($cl % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			if($wexis == 0)
			{
				$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',2,'".str_replace("-","/",$date)."','".$wccr."','".$wcccr."', '".$wnitcr."' , 'COMPROBANTE DE INVENTARIOS','2',".number_format((double)$wtotc,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
				$err_o = odbc_do($conex_o,$query);
			}
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";		
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcccr."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitcr."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccr."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotc,2,'.',',')."</font></td></tr>";	
		}
		elseif($wtip == 2)
				{
					$wcons++;
					$wc=(string)$wcons;
					while(strlen($wc) < 7)
						$wc = "0".$wc;
					$wexis=0;
					if($wserv == "S")
					{
						$query="select * from comovenc where movencano = '".substr($date,0,4)."' and movencmes = '".substr($date,5,2)."' and movencfue = '".$wfuente."' and  movencdoc = '".$wc."'";
						$err_o = odbc_do($conex_o,$query);
						while (odbc_fetch_row($err_o) and $wexis == 0)
							$wexis=1;
						if($wexis == 0)
						{
							$query= "insert into comovenc values ('".substr($date,0,4)."','".substr($date,5,2)."','".$wfuente."','".$wc."', '".$key."', '0') ";
							$err_o = odbc_do($conex_o,$query);
						}
					}
					else
						$wexis=1;
					$lin=0;
					for ($j=0;$j<$numl;$j++)
					{
						if($lineas[$j][2] != 0)
						{
							$cl++;
							if($cl % 2 == 0)
								$color="#9999FF";
							else
								$color="#ffffff";
							if($wexis == 0)
							{
								$lin++;
								$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',".$lin.",'".str_replace("-","/",$date)."','".$lineas[$j][1]."','".$wccde."', '".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','1',".number_format((double)$lineas[$j][2],2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
								$err_o = odbc_do($conex_o,$query);
							}
							echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";		
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccde."</font></td>";	
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitde."</font></td>";
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$lineas[$j][1]."</font></td>";	
							echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$lineas[$j][2],2,'.',',')."</font></td>";	
							echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
						}
					}
					$cl++;
					if($cl % 2 == 0)
						$color="#9999FF";
					else
						$color="#ffffff";
					if($wexis == 0)
					{
						$lin++;
						$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',".$lin.",'".str_replace("-","/",$date)."','".$wccr."','".$wcccr."', '".$wnitcr."' , 'COMPROBANTE DE INVENTARIOS','2',".number_format((double)$wtotc,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
						$err_o = odbc_do($conex_o,$query);
					}
					echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";		
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";		
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcccr."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitcr."</font></td>";
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccr."</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotc,2,'.',',')."</font></td></tr>";	
				}
				else
				{
					$cl++;
					$wcons++;
					$wc=(string)$wcons;
					while(strlen($wc) < 7)
						$wc = "0".$wc;
					$wexis=0;
					if($wserv == "S")
					{
						$query="select * from comovenc where movencano = '".substr($date,0,4)."' and movencmes = '".substr($date,5,2)."' and movencfue = '".$wfuente."' and  movencdoc = '".$wc."'";
						$err_o = odbc_do($conex_o,$query);
						while (odbc_fetch_row($err_o) and $wexis == 0)
							$wexis=1;
						if($wexis == 0)
						{
							$query= "insert into comovenc values ('".substr($date,0,4)."','".substr($date,5,2)."','".$wfuente."','".$wc."', '".$key."', '0') ";
							$err_o = odbc_do($conex_o,$query);
						}
					}
					else
						$wexis=1;
					if($cl % 2 == 0)
						$color="#9999FF";
					else
						$color="#ffffff";
					if($wexis == 0)
					{
						$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',1,'".str_replace("-","/",$date)."','".$wcdb."','".$wccde."', '".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','1',".number_format((double)$wtotd,2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
						$err_o = odbc_do($conex_o,$query);
					}
					echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wccde."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitde."</font></td>";
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcdb."</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotd,2,'.',',')."</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
					$lin=1;
					for ($j=0;$j<$numl;$j++)
					{
						if($lineas[$j][2] != 0)
						{
							$cl++;
							if($cl % 2 == 0)
								$color="#9999FF";
							else
								$color="#ffffff";
							if($wexis == 0)
							{
								$lin++;
								$query= "insert into comov values ('".$wfuente."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',".$lin.",'".str_replace("-","/",$date)."','".$lineas[$j][1]."','".$wcccr."','".$wnitde."' , 'COMPROBANTE DE INVENTARIOS','2',".number_format((double)$lineas[$j][2],2,'.','').",'',0.00,'0',0.00,0.00,'N','0') ";
								$err_o = odbc_do($conex_o,$query);
							}
							echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$wconant."</font></td>";	
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wfuente."</font></td>";		
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";		
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcccr."</font></td>";	
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnitcr."</font></td>";
							echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$lineas[$j][1]."</font></td>";	
							echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
							echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$lineas[$j][2],2,'.',',')."</font></td></tr>";	
						}
					}
				}
		echo "<tr><td bgcolor=#999999 align=center colspan=6><font face='tahoma' size=2><b>TOTAL COMPROBANTE</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotgd,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotgc,2,'.',',')."</b></font></td></tr>";	
		echo"</table>";
	}
}
?>
</body>
</html>