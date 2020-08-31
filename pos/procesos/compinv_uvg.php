<html>
<head>
  	<title>MATRIX</title>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion del Archivo Para el Comprobante Contable de Inventarios(UVG)</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Cominv.php Ver 2009-02-10</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='compinv_uvg' action='compinv_uvg.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfeci) or !isset($wfecf) or !isset($wano) or !isset($wmes))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION ARCHIVO MENSUAL DEL COMPROBANTE CONTABLE(UVG)</td></tr>";
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
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION DE COMPROBANTE CONTABLE(UVG)</font> Ver 2009-02-10</b></font></td></tr>";
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
			//                   0       1       2       3       4       5       6       7       8       9       10      11      12      13      14      15      16      17      18
			$query = "SELECT   Mencon, Mennit, Mendoc, Mencco, Menccd, Mdeart, Mdevto, artgru, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, conpro, Iccnig, Iccbad, Iccbac   from ".$empresa."_000010, ".$empresa."_000008, ".$empresa."_000038, ".$empresa."_000011, ".$empresa."_000001 ";
			$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
			$query .= "   and  Mencon=Concod ";
			$query .= "   and  Congec='on' ";
			$query .= "   and  Mdecon=Mencon";
			$query .= "   and  Mdedoc=Mendoc";
			$query .= "   and  Mdeart=artcod ";
			$query .= "   and  Mdecon=Icccon ";
			$query .= "   and  mid(artgru,1,2)=Iccfue ";
			$query .= "  Order by Mencon, Iccfue, Mencco, Mennit ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			echo "<b>Registros Totales  : ".$num."</b><br><br>";
			$wtotd=0;
			$wtotc=0;
			$k=0;
			$wclave="";
			$cl=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wclave != $row[0].$row[8].$row[3].$row[1])
				{
					if($i > 0)
					{
						for ($j=0;$j<count($wcdb);$j++)
						{
							if(count($wcdb) > 1)
							{
								$wnitdeST=$wnitde;
								$wccdeST=$wccde;
								if(substr($wcdb[$j],0,2) == "SN")
									$wnitde="00000000000";
								if(substr($wcdb[$j],2,2) == "SC")
									$wccde="000";
								$wcdb[$j]=substr($wcdb[$j],4);
							}
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
							$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$wcdb[$j]."',".number_format((double)$wtotd,2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
							$err3 = mysql_query($query,$conex);
				   			if ($err3 != 1)
								echo mysql_errno().":".mysql_error()."<br>";
							else
							{
								if(count($wcdb) > 1)
								{
									$wnitde=$wnitdeST;
									$wccde=$wccdeST;
								}
								echo "REGISTRO INSERTADO NRo : ".$ultreg."<br>";
								$ultreg++;
							}
						}
						for ($j=0;$j<count($wccr);$j++)
						{
							if(count($wccr) > 1)
							{
								$wnitcrST=$wnitcr;
								$wcccrST=$wcccr;
								if(substr($wccr[$j],0,2) == "SN")
									$wnitcr="00000000000";
								if(substr($wccr[$j],2,2) == "SC")
									$wcccr="000";
								$wccr[$j]=substr($wccr[$j],4);
							}
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
							$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$wccr[$j]."',".number_format((double)$wtotc,2,'.','').",'2','".$wbased."','on','C-".$empresa."')";
							$err3 = mysql_query($query,$conex);
				   			if ($err3 != 1)
								echo mysql_errno().":".mysql_error()."<br>";
							else
							{
								if(count($wccr) > 1)
								{
									$wnitcr=$wnitcrST;
									$wcccr=$wcccrST;
								}
								echo "REGISTRO INSERTADO NRo : ".$ultreg."<br>";
								$ultreg++;
							}
						}
					}
					$wconant=$row[0];
					$wclave = $row[0].$row[8].$row[3].$row[1];
					$wtotd=0;
					$wtotc=0;
					if($row[17] == "on")
						$wbased="S";
					else
						$wbased="N";
					if($row[18] == "on")
						$wbasec="S";
					else
						$wbasec="N";
					$wfuente=$row[8];
					switch ($row[10])
					{
						case "origen":
							$wccde=$row[3];
						break;
						case "destino":
							$wccde=$row[4];
						break;
						case "no":
							$wccde="000";
						break;
					}
					$wnitde="00000000000";
					if($row[11] == "on" and $row[15] == "off")
						$wnitde=$row[16];
					if($row[11] == "on" and $row[15] == "on")
						$wnitde=$row[1];
					switch ($row[13])
					{
						case "origen":
							$wcccr=$row[3];
						break;
						case "destino":
							$wcccr=$row[4];
						break;
						case "no":
							$wcccr="000";
						break;
					}
					$wnitcr="00000000000";
					if($row[14] == "on" and $row[15] == "off")
						$wnitcr=$row[16];
					if($row[14] == "on" and $row[15] == "on")
						$wnitcr=$row[1];
					$wcdb=explode("-",$row[9]);
					$wccr=explode("-",$row[12]);
				}
				$wtotd +=$row[6];
				$wtotc +=$row[6];
			}
			for ($j=0;$j<count($wcdb);$j++)
			{
				if(count($wcdb) > 1)
				{
					$wnitdeST=$wnitde;
					$wccdeST=$wccde;
					if(substr($wcdb[$j],0,2) == "SN")
						$wnitde="00000000000";
					if(substr($wcdb[$j],2,2) == "SC")
						$wccde="000";
					$wcdb[$j]=substr($wcdb[$j],4);
				}
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
				$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wccde."','".$wnitde."','".$wcdb[$j]."',".number_format((double)$wtotd,2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
				$err3 = mysql_query($query,$conex);
	   			if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
					if(count($wcdb) > 1)
					{
						$wnitde=$wnitdeST;
						$wccde=$wccdeST;
					}
					echo "REGISTRO INSERTADO NRo : ".$ultreg."<br>";
					$ultreg++;
				}
			}
			for ($j=0;$j<count($wccr);$j++)
			{
				if(count($wccr) > 1)
				{
					$wnitcrST=$wnitcr;
					$wcccrST=$wcccr;
					if(substr($wccr[$j],0,2) == "SN")
						$wnitcr="00000000000";
					if(substr($wccr[$j],2,2) == "SC")
						$wcccr="000";
					$wccr[$j]=substr($wccr[$j],4);
				}
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000056 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
				$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente."','".$wconant."','".$wcccr."','".$wnitcr."','".$wccr[$j]."',".number_format((double)$wtotc,2,'.','').",'2','".$wbased."','on','C-".$empresa."')";
				$err3 = mysql_query($query,$conex);
	   			if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
					if(count($wccr) > 1)
					{
						$wnitcr=$wnitcrST;
						$wcccr=$wcccrST;
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