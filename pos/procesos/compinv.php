<html>
<head>
  	<title>MATRIX</title>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion del Archivo Para el Comprobante Contable de Inventarios</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Cominv.php Ver 2008-06-18</b></font></td></tr></table>
</center>
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
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfeci) or !isset($wfecf) or !isset($wano) or !isset($wmes))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION ARCHIVO MENSUAL DEL COMPROBANTE CONTABLE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfeci' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfecf' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "SELECT Icllin, Iclcue  from ".$empresa."_000039  order by Icllin ";
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
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION DE COMPROBANTE CONTABLE</font> Ver 2008-06-18</b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wfeci."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wfecf."</td></tr>";	
		echo "</tr></table><br><br>";
		$ultreg=1;
		$query = "Select * from  ".$empresa."_000056 ";
		$query .= " where cinano = ".$wano; 
		$query .= "     and cinmes = ".$wmes;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num == 0)
		{
			//                   0       1       2       3       4       5       6       7       8       9       10      11      12      13      14     15
			$query = "SELECT   Mencon, Mennit, Mendoc, Mencco, Menccd, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, conpro, Iccnig, Iccbad, Iccbac   from ".$empresa."_000010,".$empresa."_000008,".$empresa."_000038 ";
			$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
			$query .= "     and   Mencon=Concod ";
			$query .= "     and   Congec='on' ";
			$query .= "     and   Mencon=Icccon ";
			$query .= "    Order by Iccfue,Mencon, Mendoc ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			echo "<b>Registros Totales  : ".$num."</b><br><br>";
			$wtotd=0;
			$wtotc=0;
			$k=0;
			$wconant="";
			$wfueant="";
			$cl=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wsw=1;
				$wconant = $row[0];
				for ($j=0;$j<$numl;$j++)
					$lineas[$j][2]=0;
				$wtotd=0;
				$wtotc=0;
				if($row[14] == "on")
					$wbased="S";
				else
					$wbased="N";
				if($row[15] == "on")
					$wbasec="S";
				else
					$wbasec="N";
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
				$query = "SELECT Mdeart, Mdevto,artgru   from ".$empresa."_000011,".$empresa."_000001  ";
				$query .= " where  Mdecon='".$row[0]."'";
				$query .= "     and   Mdedoc='".$row[2]."'";
				$query .= "     and 	Mdeart=artcod ";
				$query .= "     and 	Mdevto >= 0 ";
				$query .= "  order by artgru ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					// RECLASIFICACION DE CUENTAS EN VENTA DE PRODUCTOS FABRICADOS EN LA EMPRESA 
					if(substr($row1[2],0,3) == "021")
					{
						if($wcdb == "14350590")
							$wcdb="14300590";
						if($wccr == "14350590")
							$wccr="14300590";
					}
					if($wtip==1)
					{
						$wtotd +=$row1[1];
						$wtotc +=$row1[1];
					}
					elseif($wtip == 2)
							{
								$wtotc +=$row1[1];
								$lineas[bt(substr($row1[2],0,3),$lineas,$numl)][2] += $row1[1];
							}
							else
							{
								$wtotd +=$row1[1];
								$lineas[bt(substr($row1[2],0,3),$lineas,$numl)][2] += $row1[1];
							}
				}
				if($num1 > 0)
				{
					if($wtip == 1)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
						$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$wcdb."',".number_format((double)$wtotd,2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
						$err3 = mysql_query($query,$conex);
			   			if ($err3 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
						$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$wccr."',".number_format((double)$wtotc,2,'.','').",'2','".$wbasec."','on','C-".$empresa."')";
						$err3 = mysql_query($query,$conex);
			   			if ($err3 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
					}
					elseif($wtip == 2)
							{
								for ($j=0;$j<$numl;$j++)
								{
									if($lineas[$j][2] != 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
										$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$lineas[$j][1]."',".number_format((double)$lineas[$j][2],2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
										$err3 = mysql_query($query,$conex);
							   			if ($err3 != 1)
											echo mysql_errno().":".mysql_error()."<br>";
									}
								}
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
								$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$wccr."',".number_format((double)$wtotc,2,'.','').",'2','".$wbasec."','on','C-".$empresa."')";
								$err3 = mysql_query($query,$conex);
					   			if ($err3 != 1)
									echo mysql_errno().":".mysql_error()."<br>";
							}
							else
							{
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
								$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$wcdb."',".number_format((double)$wtotd,2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
								$err3 = mysql_query($query,$conex);
					   			if ($err3 != 1)
									echo mysql_errno().":".mysql_error()."<br>";
								for ($j=0;$j<$numl;$j++)
								{
									if($lineas[$j][2] != 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
										$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$lineas[$j][1]."',".number_format((double)$lineas[$j][2],2,'.','').",'2','".$wbasec."','on','C-".$empresa."')";
										$err3 = mysql_query($query,$conex);
							   			if ($err3 != 1)
											echo mysql_errno().":".mysql_error()."<br>";
									}
								}
							}
					echo "REGISTRO INSERTADO NRo : ".$ultreg."<br>";
					$ultreg++;
				}
				$wtotd=0;
				$wtotc=0;
				for ($j=0;$j<$numl;$j++)
					$lineas[$j][2]=0;
				$query = "SELECT Mdeart, Mdevto,artgru   from ".$empresa."_000011,".$empresa."_000001  ";
				$query .= " where  Mdecon='".$row[0]."'";
				$query .= "     and   Mdedoc='".$row[2]."'";
				$query .= "     and 	Mdeart=artcod ";
				$query .= "     and 	Mdevto < 0 ";
				$query .= "  order by artgru ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					if($row[6] == "XL" or  $row[9] == "XL")
						if($row[6] == "XL")
						{
							$wtip=3;
						}
						else
						{
							$wtip=2;
						}
					$waux1=$wcdb;
					$waux2=$wccr;
					$wcdb=$waux2;
					$wccr=$waux1;
					$waux1=$wbased;
					$waux2=$wbasec;
					$wbased=$waux2;
					$wbasec=$waux1;
					$waux1=$wccde;
					$waux2=$wcccr;
					$wccde=$waux2;
					$wcccr=$waux1;
					$waux1=$wnitde;
					$waux2=$wnitcr;
					$wnitde=$waux2;
					$wnitcr=$waux1;
				}
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					// RECLASIFICACION DE CUENTAS EN VENTA DE PRODUCTOS FABRICADOS EN LA EMPRESA 
					if(substr($row1[2],0,3) == "021")
					{
						if($wcdb == "14350590")
							$wcdb="14300590";
						if($wccr == "14350590")
							$wccr="14300590";
					}
					$row1[1]=$row1[1]*(-1);
					if($wtip==1)
					{
						$wtotd +=$row1[1];
						$wtotc +=$row1[1];
					}
					elseif($wtip == 2)
							{
								$wtotc +=$row1[1];
								$lineas[bt(substr($row1[2],0,3),$lineas,$numl)][2] += $row1[1];
							}
							else
							{
								$wtotd +=$row1[1];
								$lineas[bt(substr($row1[2],0,3),$lineas,$numl)][2] += $row1[1];
							}
				}
				if($num1 > 0)
				{
					if($wtip == 1)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
						$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$wcdb."',".number_format((double)$wtotd,2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
						$err3 = mysql_query($query,$conex);
			   			if ($err3 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
						$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$wccr."',".number_format((double)$wtotc,2,'.','').",'2','".$wbasec."','on','C-".$empresa."')";
						$err3 = mysql_query($query,$conex);
			   			if ($err3 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
					}
					elseif($wtip == 2)
							{
								for ($j=0;$j<$numl;$j++)
								{
									if($lineas[$j][2] != 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
										$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$lineas[$j][1]."',".number_format((double)$lineas[$j][2],2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
										$err3 = mysql_query($query,$conex);
							   			if ($err3 != 1)
											echo mysql_errno().":".mysql_error()."<br>";
									}
								}
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
								$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$wccr."',".number_format((double)$wtotc,2,'.','').",'2','".$wbasec."','on','C-".$empresa."')";
								$err3 = mysql_query($query,$conex);
					   			if ($err3 != 1)
									echo mysql_errno().":".mysql_error()."<br>";
							}
							else
							{
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
								$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$wcdb."',".number_format((double)$wtotd,2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
								$err3 = mysql_query($query,$conex);
					   			if ($err3 != 1)
									echo mysql_errno().":".mysql_error()."<br>";
								for ($j=0;$j<$numl;$j++)
								{
									if($lineas[$j][2] != 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
										$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$lineas[$j][1]."',".number_format((double)$lineas[$j][2],2,'.','').",'2','".$wbasec."','on','C-".$empresa."')";
										$err3 = mysql_query($query,$conex);
							   			if ($err3 != 1)
											echo mysql_errno().":".mysql_error()."<br>";
									}
								}
							}
					echo "REGISTRO INSERTADO NRo : ".$ultreg."<br>";
					$ultreg++;
				}
			}
			echo "<b>TOTAL REGISTROS INSERTADOS  : </b>".$ultreg."<br>";
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