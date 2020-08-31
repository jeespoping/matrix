<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Movimiento Hospitalario CLISUR</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> HCE_CLISUR.php Ver. 2014-03-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
	if($wtip == 1)
	{
		

		


		$query = "DELETE FROM clisur_000255 WHERE 1 > 0 ";
		$querytime_before = array_sum(explode(' ', microtime()));
      	$err = mysql_query($query,$conex) or die("q2 ".mysql_errno().":".mysql_error());
      	$querytime_after = array_sum(explode(' ', microtime(true)));
      	$DIFF=$querytime_after - $querytime_before;
		echo "Tiempo1 : ".$DIFF." Segundo(s)<br>";

		$query  = "insert into clisur_	 (medico,fecha_data,hora_data,acthis, acting,seguridad) ";
		$query .= "  select 'clisur',CURRENT_DATE(),CURRENT_TIME(), pachis,max(cast(ingnin as unsigned)),'C-clisur' ";
		$query .= "    from clisur_000100, root_000037 ";
		$query .= "   where Pacact = 'on' ";
		$query .= " 	and orihis = pachis ";
		$query .= " 	and oriori = '02' ";
		$query .= "   group by 4 ";

		$querytime_before = array_sum(explode(' ', microtime()));
		$err = mysql_query($query,$conex) or die (mysql_errno()." : ".mysql_error());
		$querytime_after = array_sum(explode(' ', microtime(true)));
		$DIFF=$querytime_after - $querytime_before;
		echo "Tiempo2 : ".$DIFF." Segundo(s)<br>";


		//                             0                    					 1                       2                   3      4      5      6      7      8      9      10    11      12     13     14     15    16     17    18      19    20
		$query  = "select concat(lpad(Pachis,12,'0'),lpad(Ingnin,8,'0')),cast(pachis as unsigned),cast(ingnin as unsigned),Pactdo,Pacdoc,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,Paciu,Ingfei,Ingsei,Ingcem,Ingent,Emptem";
		$query .= " from clisur_000100,clisur_000101,clisur_000255,clisur_000024 ";
		$query .= " where pachis=inghis ";
		$query .= "  and inghis=acthis ";
		$query .= "  and ingnin=acting ";
		$query .= "  and Pacact='on' ";
		$query .= "  and Ingcem=Empcod ";
		$query .= " order by 2,3 ";
		$querytime_before = array_sum(explode(' ', microtime()));
		$err1 = mysql_query($query,$conex);
		$querytime_after = array_sum(explode(' ', microtime(true)));
		$DIFF=$querytime_after - $querytime_before;
		echo "Tiempo3 : ".$DIFF." Segundo(s)<br>";
		$num1 = mysql_num_rows($err1);

		$query  = "select concat(lpad(Ubihis,12,'0'),lpad(Ubiing,8,'0')),cast(Ubihis as unsigned),cast(Ubiing as unsigned) ";
		$query .= " from mhoscs_000018 ";
		$query .= " where Ubiald = 'off' ";
		$query .= " order by 2,3 ";
		$querytime_before = array_sum(explode(' ', microtime()));
		$err2 = mysql_query($query,$conex);
		$querytime_after = array_sum(explode(' ', microtime(true)));
		$DIFF=$querytime_after - $querytime_before;
		echo "Tiempo4 : ".$DIFF." Segundo(s)<br>";
		$num2 = mysql_num_rows($err2);
		$SUMAEQ=0;
		$SUMAAD=0;
		$SUMAEG=0;
		$SUMAupR36=0;
		$SUMAinR36=0;
		$SUMAupR37=0;
		$SUMAinR37=0;
		$SUMAupM16=0;
		$SUMAinM16=0;
		$SUMAupM18=0;
		$SUMAinM18=0;
		$SUMAupM47=0;
		$SUMAinM47=0;
		$wtotin=0;
		$k1=0;
		$k2=0;
		$num=-1;
		echo "REGISTROS : ".$num1." ".$num2."<br>";
		if ($num1 ==  0)
		{
			$k1=1;
			$row1[0]="zzzzzzzzzzzzzzzzzzzz";
		}
		else
		{
			$row1 = mysql_fetch_array($err1);
			$k1++;
		}
		if ($num2 ==  0)
		{
			$k2=1;
			$row2[0]="zzzzzzzzzzzzzzzzzzzz";
		}
		else
		{
			$row2 = mysql_fetch_array($err2);
			$k2++;
		}
		while ($k1 <= $num1 or $k2 <= $num2)
		{
			if($row1[0] == $row2[0])
			{
				$SUMAEQ++;
				$k1++;
				$k2++;
				if($k1 > $num1)
					$row1[0]="zzzzzzzzzzzzzzzzzzzz";
				else
					$row1 = mysql_fetch_array($err1);
				if($k2 > $num2)
					$row2[0]="zzzzzzzzzzzzzzzzzzzz";
				else
					$row2 = mysql_fetch_array($err2);
			}
			else if($row1[0] < $row2[0])
			{
				$SUMAAD++;
				$query  = "select * ";
				$query .= "  from root_000036 ";
				$query .= "   where Pacced = '".$row1[4]."' ";
				$query .= "     and Pactid = '".$row1[3]."' ";
				$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num3 = mysql_num_rows($err3);
				if($num3 > 0)
				{
					$query =  " update root_000036 set pacno1 = '".$row1[8]."', pacno2 = '".$row1[9]."', pacap1 = '".$row1[6]."', pacap2 = '".$row1[7]."', pacnac = '".$row1[10]."', pacsex = '".$row1[11]."' ";
					$query .=  "  where Pacced = '".$row1[4]."' and Pactid = '".$row1[3]."' ";
					$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ROOT 36 : ".mysql_errno().":".mysql_error());
					$SUMAupR36++;
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "root";
					$query = "insert root_000036 (medico, fecha_data, Hora_data, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $row1[4]."','";
					$query .=  $row1[3]."','";
					$query .=  $row1[8]."','";
					$query .=  $row1[9]."','";
					$query .=  $row1[6]."','";
					$query .=  $row1[7]."','";
					$query .=  $row1[10]."','";
					$query .=  $row1[11]."',";
					$query .=  "'C-".$empresa."')";
					$err3 = mysql_query($query,$conex) or die("ERROR GRABANDO ROOT 36 : ".mysql_errno().":".mysql_error());
					$SUMAinR36++;
				}
				$query  = "select * ";
				$query .= "  from root_000037 ";
				$query .= "   where Oriced = '".$row1[4]."' ";
				$query .= "     and Oritid = '".$row1[3]."' ";
				$query .= "     and Oriori = '02' ";
				$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num3 = mysql_num_rows($err3);
				if($num3 == 0)
				{
					$query  = "select * ";
					$query .= "  from root_000037 ";
					$query .= "   where Orihis = '".$row1[1]."' ";
					$query .= "     and Oriori = '02' ";
					$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num3 = mysql_num_rows($err3);
					if($num3 == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$empresa = "root";
						$query = "insert root_000037 (medico, fecha_data, Hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, Seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $fecha."','";
						$query .=  $hora."','";
						$query .=  $row1[4]."','";
						$query .=  $row1[3]."','";
						$query .=  $row1[1]."','";
						$query .=  $row1[2]."',";
						$query .=  "'02',";
						$query .=  "'C-".$empresa."')";
						$err3 = mysql_query($query,$conex) or die("ERROR GRABANDO ROOT 37 : : ".mysql_errno().":".mysql_error());
						$SUMAinR37++;
					}
					else
					{
						$query =  " update root_000037 set Oriced = '".$row1[4]."', Oritid = '".$row1[3]."' ";
						$query .=  "  where Orihis = '".$row1[1]."' and Oriing = '".$row1[2]."' and Oriori = '02' ";
						$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO root_000037 : ".mysql_errno().":".mysql_error());
						$SUMAupR37++;
					}
				}
				else
				{
					$query =  " update root_000037 set Oriced = '".$row1[4]."', Oritid = '".$row1[3]."' ";
					$query .=  "  where Orihis = '".$row1[1]."' and Oriing = '".$row1[2]."' and Oriori = '02' ";
					$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO root_000037 : ".mysql_errno().":".mysql_error());
					$SUMAupR37++;
				}
				$query  = "select * ";
				$query .= "  from mhoscs_000016 ";
				$query .= "   where Inghis = '".$row1[1]."' ";
				$query .= "     and Inging = '".$row1[2]."' ";
				$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num3 = mysql_num_rows($err3);
				if($num3 > 0)
				{
					$query =  " update mhoscs_000016 set Ingres = '".$row1[18]."', Ingnre = '".$row1[19]."', Ingtip = '".$row1[20]."', Ingtel = '".$row1[14]."', Ingdir = '".$row1[13]."', Ingmun = '".$row1[15]."' ";
					$query .=  "  where Inghis = '".$row1[1]."' and Inging = '".$row1[2]."' ";
					$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhoscs_000016 : ".mysql_errno().":".mysql_error());
					$SUMAupM16++;
				}
				else
				{

					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "mhoscs";
					$query = "insert mhoscs_000016 (medico, fecha_data, Hora_data, Inghis, Inging, Ingres, Ingnre, Ingtip, Ingtel, Ingdir, Ingmun, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $row1[1]."','";
					$query .=  $row1[2]."','";
					$query .=  $row1[18]."','";
					$query .=  $row1[19]."','";
					$query .=  $row1[20]."','";
					$query .=  $row1[14]."','";
					$query .=  $row1[13]."','";
					$query .=  $row1[15]."',";
					$query .=  "'C-".$empresa."')";
					$err3 = mysql_query($query,$conex) or die("ERROR GRABANDO mhoscs_000016 : ".mysql_errno().":".mysql_error());
					$SUMAinM16++;
				}
				$query  = "select * ";
				$query .= "  from mhoscs_000018 ";
				$query .= "   where Ubihis = '".$row1[1]."' ";
				$query .= "     and Ubiing = '".$row1[2]."' ";
				$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num3 = mysql_num_rows($err3);
				if($num3 > 0)
				{
					$query =  " update mhoscs_000018 set Ubiald = 'off', Ubifad = '0000-00-00', Ubihad = '00:00:00' ";
					$query .=  "  where Ubihis = '".$row1[1]."' and Ubiing = '".$row1[2]."' ";
					$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhoscs_000018 : ".mysql_errno().":".mysql_error());
					$SUMAupM18++;
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "mhoscs";
					$query = "insert mhoscs_000018 (medico, fecha_data, Hora_data, Ubihis, Ubiing, Ubisac, Ubisan, Ubihac, Ubihan, Ubialp, Ubiald, Ubifap, Ubihap, Ubifad, Ubihad, Ubiptr, Ubitmp, Ubimue, ubiprg, ubifho, ubihho, ubihot, ubiuad, ubiamd, ubijus, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $row1[1]."','";
					$query .=  $row1[2]."','";
					$query .=  $row1[17]."',";
					$query .=  "' ','";
					$query .=  $row1[17]."',";
					$query .=  "' ',";
					$query .=  "'off',";
					$query .=  "'off',";
					$query .=  "'0000-00-00',";
					$query .=  "'00:00:00',";
					$query .=  "'0000-00-00',";
					$query .=  "'00:00:00',";
					$query .=  "'off',";
					$query .=  "' ',";
					$query .=  "'off',";
					$query .=  "' ',";
					$query .=  "'0000-00-00',";
					$query .=  "'00:00:00',";
					$query .=  "' ',' ',' ',' ',";
					$query .=  "'C-".$empresa."')";
					$err3 = mysql_query($query,$conex) or die("ERROR GRABANDO mhoscs_000018 : ".mysql_errno().":".mysql_error());
					$SUMAinM18++;
				}
				$query  = "select * ";
				$query .= "  from mhoscs_000047 ";
				$query .= "   where Mettdo = 'CC' ";
				$query .= "     and Metdoc = 'PLANTA' ";
				$query .= "     and Methis = '".$row1[1]."' ";
				$query .= "     and Meting = '".$row1[2]."' ";
				$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num3 = mysql_num_rows($err3);
				if($num3 > 0)
				{
					$query =  " update mhoscs_000047 set Metest = 'on', Metfek = '".date("Y-m-d")."', Metesp = '100112' ";
					$query .=  "  where Mettdo = 'CC' and Metdoc = 'PLANTA' and Methis = '".$row1[1]."' and Meting = '".$row1[2]."' ";
					$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhoscs_000047 : ".mysql_errno().":".mysql_error());
					$SUMAupM47++;
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "mhoscs";
					$query = "insert mhoscs_000047 (medico, fecha_data, Hora_data, Mettdo, Metdoc, Methis, Meting, Metfek, Metest, Metint, Metesp, Metusu, Metfir, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."',";
					$query .=  "'CC',";
					$query .=  "'PLANTA','";
					$query .=  $row1[1]."','";
					$query .=  $row1[2]."','";
					$query .=  $fecha."',";
					$query .=  "'on','off',";
					$query .=  "'100112',' ',' ',";
					$query .=  "'C-".$empresa."')";
					$err3 = mysql_query($query,$conex) or die("ERROR GRABANDO mhoscs_000047 : ".mysql_errno().":".mysql_error());
					$SUMAinM47++;
				}
				$k1++;
				if($k1 > $num1)
					$row1[0]="zzzzzzzzzzzzzzzzzzzz";
				else
					$row1 = mysql_fetch_array($err1);
			}
			else
			{
				$SUMAEG++;
				$query =  "update mhoscs_000018 set Ubiald = 'on',Ubifad='".date("Y-m-d")."',Ubihad='".date("H:i:s")."' where Ubihis = '".$row2[1]."' and Ubiing = '".$row2[2]."' ";
				$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhoscs_000018 : ".mysql_errno().":".mysql_error());
				$query =  "update mhoscs_000047 set Metest = 'off' where Mettdo = 'CC' and Metdoc = 'PLANTA' and Methis = '".$row2[1]."' and Meting = '".$row2[2]."' ";
				$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhoscs_000047 : ".mysql_errno().":".mysql_error());
				$k2++;
				if($k2 > $num2)
					$row2[0]="zzzzzzzzzzzzzzzzzzzz";
				else
					$row2 = mysql_fetch_array($err2);
			}
		}
		echo "<b>PACIENTES ACTUALIZADOS : ".$SUMAEQ." </b><br>";
		echo "<b>PACIENTES INGRESADOS : ".$SUMAAD." </b><br>";
		echo "REGISTROS ACTUALIZADOS root 36 : ".$SUMAupR36."<br>";
		echo "REGISTROS INGRESADOS root 36 : ".$SUMAinR36."<br>";
		echo "REGISTROS ACTUALIZADOS root 37 : ".$SUMAupR37."<br>";
		echo "REGISTROS INGRESADOS root 37 : ".$SUMAinR37."<br>";
		echo "REGISTROS ACTUALIZADOS mhos 16 : ".$SUMAupM16."<br>";
		echo "REGISTROS INGRESADOS mhos 16 : ".$SUMAinM16."<br>";
		echo "REGISTROS ACTUALIZADOS mhos 18 : ".$SUMAupM18."<br>";
		echo "REGISTROS INGRESADOS mhos 18 : ".$SUMAinM18."<br>";
		echo "REGISTROS ACTUALIZADOS mhos 47 : ".$SUMAupM47."<br>";
		echo "REGISTROS INGRESADOS mhos 47 : ".$SUMAinM47."<br>";
		echo "<b>PACIENTES EGRESADOS : ".$SUMAEG." </b><br>";
	}
?>
</body>
</html>
