<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=1 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>******** Actualizacion de Movimiento Hospitalario IDC ********</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> HCE_IDC.php Ver. 2016-02-23</b></font></tr></td>
<?php
include_once("conex.php");
	function encrypt($string, $key) 
	{
	   $result = '';
	   for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
		  $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)+ord($keychar));
		  $result.=$char;
	   }
	   return base64_encode($result);
	}

	if($wtip == 1)
	{
		//--> Jerson Trujillo, 2019-08-30: Obtener la direccion de la BD del IDC
		$hostIdc = "";
		$sqlHost = "
		SELECT Detval
		  FROM root_000051
		 WHERE Detemp = '01'
		   AND Detapl = 'parametrosDeConexionBaseDatosIDC'
		";		
		$resHost = mysql_query($sqlHost,$conex) or die(mysql_errno().":".mysql_error());
		if($rowHost = mysql_fetch_array($resHost))
			$hostIdc = json_decode(trim($rowHost['Detval']));
		
		if(!is_object($hostIdc)){
			echo "<tr><td>No se pudo obtener los paramaetros de conexion con la BD del IDC (root_51->direccionBaseDatosIDC)</tr></td>";
			return;
		}
		
		$conexidc = mysqli_connect($hostIdc->host,$hostIdc->username,$hostIdc->password,$hostIdc->dbname) or die('No se realizo Conexion con el IDC');
		//$conexidc = mysql_connect('190.248.93.238:3306','pmla','pmla800067065') or die("No se realizo Conexion con el IDC");
		// mysql_select_db("pacidc"); 	
		//                 0       1      2     3       4     5      6      7      8      9      10     11     12     13     14     15    16      17    18      19     20     21      22    23     24     25     26     27     28     29     30     31
		$query = "select pacced,pactid,pacno1,pacno2,pacap1,pacap2,pacnac,pacsex,orihis,ingnre,paceps,pacdir,pactep,pacest,mettdo,metdoc,pacces,estado,ingtel,pacmun,pacres,pacdep,restid,resced,resno1,resres,restel,resdep,resmun,pacnac,paceps,paceab from pacidc where estado = 'off' or pacclv is NULL ";
		$err = mysql_query($query,$conexidc) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<tr><td>CONEXION IDC OK</tr></td>";
		echo "<tr><td>".date("Y-m-d H:i:s")." Numero de Pacientes :".$num."</tr></td>";
		if($num > 0)
		{
			

			

			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[31] == "")
					$row[31] = " ";
				$query  = "select * ";
				$query .= "  from root_000109 "; 
				$query .= "   where Rychis = '".$row[8]."' ";
				$query .= "     and Rycing = '1' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				//echo "numero ".$num1." ".$query."<br>";
				if($num1 > 0)
				{
					$query =  " update root_000109 set Rycced = '".$row[0]."', Ryctid = '".$row[1]."', rycdip = '".$row[20]."', ryctep = '".$row[18]."', rycdep = '".$row[21]."', rycmup = '".$row[19]."', ryctdr = '".$row[22]."', ryccer = '".$row[23]."', rycnor = '".$row[24]."', rycdir = '".$row[25]."', rycter = '".$row[26]."', rycder = '".$row[27]."', rycmur = '".$row[28]."', rycnac = '".$row[29]."', ryceps = '".$row[30]."', ryccep = '".$row[31]."' ";
					$query .=  "  where Rycced = '".$row[0]."' and Ryctid = '".$row[1]."' ";
					$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ROOT 109 : ".mysql_errno().":".mysql_error());
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "root";
					$query = "insert root_000109 (medico, fecha_data, Hora_data, Rychis, Rycing, Rycced, Ryctid, Rycdip, Ryctep, Rycdep, Rycmup, Ryctdr, Ryccer, Rycnor, Rycdir, Rycter, Rycder, Rycmur, Rycnac, Ryceps, Ryccep, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $row[8]."','";
					$query .=  "1','";
					$query .=  $row[0]."','";
					$query .=  $row[1]."','";
					$query .=  $row[20]."','";
					$query .=  $row[18]."','";
					$query .=  $row[21]."','";
					$query .=  $row[19]."','";
					$query .=  $row[22]."','";
					$query .=  $row[23]."','";
					$query .=  $row[24]."','";
					$query .=  $row[25]."','";
					$query .=  $row[26]."','";
					$query .=  $row[27]."','";
					$query .=  $row[28]."','";
					$query .=  substr($row[29],0,10)."','";
					$query .=  $row[30]."','";
					$query .=  $row[31]."',";
					$query .=  "'C-".$empresa."')";
					//echo $query."<br>";
					$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ROOT 109 : ".mysql_errno().":".mysql_error());
				}
				// $query = "lock table root_000036 LOW_PRIORITY WRITE, root_000037 LOW_PRIORITY WRITE  ";
				// $err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE DATOS : ".mysql_errno().":".mysql_error());
				$query  = "select * ";
				$query .= "  from root_000036 "; 
				$query .= "   where Pacced = '".$row[0]."' ";
				$query .= "     and Pactid = '".$row[1]."' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$query =  " update root_000036 set pacno1 = '".$row[2]."', pacno2 = '".$row[3]."', pacap1 = '".$row[4]."', pacap2 = '".$row[5]."', pacnac = '".$row[6]."', pacsex = '".$row[7]."' ";
					$query .=  "  where Pacced = '".$row[0]."' and Pactid = '".$row[1]."' ";
					$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ROOT 36 : ".mysql_errno().":".mysql_error());
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
					$query .=  $row[0]."','";
					$query .=  $row[1]."','";
					$query .=  $row[2]."','";
					$query .=  $row[3]."','";
					$query .=  $row[4]."','";
					$query .=  $row[5]."','";
					$query .=  $row[6]."','";
					$query .=  $row[7]."',";
					$query .=  "'C-".$empresa."')";
					$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ROOT 36 : ".mysql_errno().":".mysql_error());
				}
				$query  = "select * ";
				$query .= "  from root_000037 "; 
				$query .= "   where Oriced = '".$row[0]."' ";
				$query .= "     and Oritid = '".$row[1]."' ";
				$query .= "     and Oriori = '10' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 == 0)
				{
					$query  = "select * ";
					$query .= "  from root_000037 "; 
					$query .= "   where Orihis = '".$row[8]."' ";
					$query .= "     and Oriori = '10' ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$empresa = "root";
						$query = "insert root_000037 (medico, fecha_data, Hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, Seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $fecha."','";
						$query .=  $hora."','";
						$query .=  $row[0]."','";
						$query .=  $row[1]."','";
						$query .=  $row[8]."',";
						$query .=  "'1',";
						$query .=  "'10',";
						$query .=  "'C-".$empresa."')";
						$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ROOT 37 : : ".mysql_errno().":".mysql_error());
					}
					else
					{
						$query =  " update root_000037 set Oriced = '".$row[0]."', Oritid = '".$row[1]."' ";
						$query .=  "  where Orihis = '".$row[8]."' and Oriing = '1' and Oriori = '10' ";
						$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO root_000037 : ".mysql_errno().":".mysql_error());
					}
				}
				// $query = " UNLOCK TABLES";
				// $err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());	
				$query  = "select * ";
				$query .= "  from mhosidc_000016 "; 
				$query .= "   where Inghis = '".$row[8]."' ";
				$query .= "     and Inging = '1' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$query =  " update mhosidc_000016 set Ingres = '".$row[10]."', Ingnre = '".$row[9]."', Ingtip = '".$row[12]."', Ingtel = '".$row[18]."', Ingdir = '".$row[11]."', Ingmun = '".$row[28]."' ";
					$query .=  "  where Inghis = '".$row[8]."' and Inging = '1' ";
					$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhosidc_000016 : ".mysql_errno().":".mysql_error());
				}
				else
				{
					
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "mhosidc";
					$query = "insert mhosidc_000016 (medico, fecha_data, Hora_data, Inghis, Inging, Ingres, Ingnre, Ingtip, Ingtel, Ingdir, Ingmun, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $row[8]."',";
					$query .=  "'1','";
					$query .=  $row[10]."','";
					$query .=  $row[9]."','";
					$query .=  $row[12]."','";
					$query .=  $row[18]."','";
					$query .=  $row[11]."','";
					$query .=  $row[28]."',";
					$query .=  "'C-".$empresa."')";
					$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO mhosidc_000016 : ".mysql_errno().":".mysql_error());
				}
				$query  = "select * ";
				$query .= "  from mhosidc_000018 "; 
				$query .= "   where Ubihis = '".$row[8]."' ";
				$query .= "     and Ubiing = '1' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$query =  " update mhosidc_000018 set Ubiald = 'off', Ubifad = '0000-00-00', Ubihad = '00:00:00' ";
					$query .=  "  where Ubihis = '".$row[8]."' and Ubiing = '1' ";
					$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhosidc_000018 : ".mysql_errno().":".mysql_error());
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "mhosidc";
					$query = "insert mhosidc_000018 (medico, fecha_data, Hora_data, Ubihis, Ubiing, Ubisac, Ubisan, Ubihac, Ubihan, Ubialp, Ubiald, Ubifap, Ubihap, Ubifad, Ubihad, Ubiptr, Ubitmp, Ubimue, ubiprg, ubifho, ubihho, ubihot, ubiuad, ubiamd, ubijus, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $row[8]."',";
					$query .=  "'1',";
					$query .=  "'2254',";
					$query .=  "' ',";
					$query .=  "'IDC',";
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
					$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO mhosidc_000018 : ".mysql_errno().":".mysql_error());
				}
				$query  = "select * ";
				$query .= "  from mhosidc_000047 "; 
				$query .= "   where Mettdo = '".$row[14]."' ";
				$query .= "     and Metdoc = '".$row[15]."' ";
				$query .= "     and Methis = '".$row[8]."' ";
				$query .= "     and Meting = '1' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$query =  " update mhosidc_000047 set Metest = 'on', Metfek = '".date("Y-m-d")."', Metesp = '".$row[16]."' ";
					$query .=  "  where Mettdo = '".$row[14]."' and Metdoc = '".$row[15]."' and Methis = '".$row[8]."' and Meting = '1' ";
					$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhosidc_000047 : ".mysql_errno().":".mysql_error());
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "mhosidc";
					$query = "insert mhosidc_000047 (medico, fecha_data, Hora_data, Mettdo, Metdoc, Methis, Meting, Metfek, Metest, Metint, Metesp, Metusu, Metfir, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $row[14]."','";
					$query .=  $row[15]."','";
					$query .=  $row[8]."',";
					$query .=  "'1','";
					$query .=  $fecha."',";
					$query .=  "'on','off','";
					$query .=  $row[16]."',' ',' ',";
					$query .=  "'C-".$empresa."')";
					$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO mhosidc_000047 : ".mysql_errno().":".mysql_error());
				}
				$chaink = "PMLA-IDC";
				$query =  " update pacidc set estado = 'on', pacclv='".$chaink."' ";
				$query .=  "  where orihis = '".$row[8]."' ";
				$err2 = mysql_query($query,$conexidc) or die("ERROR ACTUALIZANDO pacidc IDC : ".mysql_errno().":".mysql_error());
				//echo "PACIENTE NRO : ".$i." GRABADO<br>";
			}
			include_once("free.php");
		}
		echo "<tr><td>********** TOTAL PACIENTES : ".$num." EN TABLERO ************</tr></td></table></center>";
	}
	else
	{
		

		

		$query =  "update mhosidc_000018 set Ubiald = 'on' where 1 ";
		$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhosidc_000018 : ".mysql_errno().":".mysql_error());
		$query =  "update mhosidc_000047 set Metest = 'off' where 1 ";
		$err2 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO mhosidc_000047 : ".mysql_errno().":".mysql_error());
		echo "<tr><td>********** TOTAL PACIENTES EGRESADOS ************</tr></td></table></center>";
	}
?>
</body>
</html>
