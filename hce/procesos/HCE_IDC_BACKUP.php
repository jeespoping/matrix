<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Diaria de Informacion de Contingencia IDC</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> HCE_IDC_BACKUP.php Ver. 2015-03-20</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
	
	/**
	 * Este archivo permite generar información de contingencia de historias clínicas de los pacientes para el IDC.
	 * El motivo de estar quemados los nombres de las tablas es por ser exclusivo del IDC.
	 */
	

	$conexidc = mysqli_connect('192.168.0.2:3306','pmla','pmla800067065',"pacidc") or die("No se realizo Conexion con el IDC");
	//$conexidc = mysql_connect('190.248.93.238:3306','pmla','pmla800067065') or die("No se realizo Conexion con el IDC");
	// mysql_select_db("pacidc"); 	
	
	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");
	$empresa = "root";

	echo "CONEXION IDC OK<br>";	
	
	$query = "DROP TABLE IF EXISTS tesp";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$query  = "CREATE TEMPORARY TABLE if not exists tesp as ";
	$query .= "select mhosidc_000048.meduma as med,hceidc_000012.Selnda as esp ";
	$query .= "   from mhosidc_000047,mhosidc_000048,hceidc_000012 ";
	$query .= "   where mhosidc_000047.metest = 'on' "; 
	$query .= "	    and mhosidc_000047.mettdo = mhosidc_000048.medtdo "; 
	$query .= "	    and mhosidc_000047.metdoc = mhosidc_000048.meddoc "; 
	$query .= "	    and mhosidc_000048.meduma = hceidc_000012.Selcda ";
	$query .= "		and hceidc_000012.Seltab  = '1034' ";
	$query .= " group by 1,2 ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	
	$query = "CREATE UNIQUE INDEX Indice on tesp (med(8))";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	//                                  0                   1                    2                      3         4
	$query  = "select hceidc_000036.firusu,hceidc_000036.Firhis,hceidc_000036.Firing,hceidc_000036.Fecha_data,tesp.esp "; 
	$query .= "    from mhosidc_000047,mhosidc_000048,hceidc_000036,tesp ";
	$query .= "    where mhosidc_000047.metest = 'on' "; 
	$query .= " 	 and mhosidc_000047.mettdo = mhosidc_000048.medtdo "; 
	$query .= " 	 and mhosidc_000047.metdoc = mhosidc_000048.meddoc "; 
	$query .= " 	 and mhosidc_000047.methis = hceidc_000036.firhis "; 
	$query .= " 	 and mhosidc_000047.meting = hceidc_000036.firing "; 
	$query .= " 	 and hceidc_000036.firpro in ('000051','000052','000063','000053','000088','000064','000089','000084','000066','000107','000100','000105','000085','000101','000108') "; 
	$query .= " 	 and mhosidc_000048.meduma = tesp.med ";
	$query .= " 	 and hceidc_000036.firusu in (select hceidc_000012.Selcda from hceidc_000012 where Seltab='1034' and Selnda= tesp.esp) ";
	$query .= "  order by 2,3,5,4 desc ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		$k="";
		$j=-1;
		$data=array();
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($k != $row[1].$row[2])
			{
				$j++;
				$data[$j][0] = $row[0];
				$data[$j][1] = $row[1];
				$data[$j][2] = $row[2];
				$data[$j][3] = $row[3];
				$data[$j][4] = $row[4];
				$k = $row[1].$row[2];
				
			}
			else
			{
				$fecha_1 = strtotime($row[3]);
				$fecha_2 = strtotime($data[$j][3]);

				if($fecha_1 < $fecha_2 and $data[$j][4] != $row[4])
				{
					$data[$j][3] = $row[3];
					$data[$j][4] = $row[4];
				}
			}
		}
	}
	$query  = "delete from ctgidc ";
	$err = mysql_query($query,$conexidc) or die("ERROR BORRANDO TABLA ctgidc ".mysql_errno().":".mysql_error());
	
	/**
	 * Se registra el información básica del cron y el total de registros encontrados para enviar la contingencia
	 * 
	 * @author	Joel David Payares Hernández
	 * @since	2021-11-11
	 */
	$descripcion = "> cron contingencia HCE IDC, TOTAL REGISTROS : $j SELECCIONADOS, ";
	
	echo "<b>TOTAL REGISTROS : ".$j." SELECCIONADOS</b><br>";
	$querytime_before = array_sum(explode(' ', microtime()));
	$k = 0;
	for ($i=0;$i<=$j;$i++)
	//for ($i=0;$i<=2;$i++)
	{
		echo "REGISTRO : ".$data[$i][0]." ".$data[$i][1]." ".$data[$i][2]." ".$data[$i][3]."<br> ";
		$query  = " Select hceidc_000051.Fecha_data, hceidc_000051.Hora_data, hceidc_000002.Detdes, hceidc_000051.movdat, hceidc_000051.movusu, '000051', hceidc_000051.movhis ";
		$query .= " from hceidc_000051, hceidc_000002 ";
		$query .= " where hceidc_000051.movcon in (6,191,195) ";
		$query .= "   and hceidc_000051.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'"; 
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000051'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000052.Fecha_data, hceidc_000052.Hora_data, hceidc_000002.Detdes, hceidc_000052.movdat, hceidc_000052.movusu, '000052', hceidc_000052.movhis ";
		$query .= " from hceidc_000052, hceidc_000002  ";
		$query .= " where hceidc_000052.movcon in (6,149,151) "; 
		$query .= "   and hceidc_000052.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'"; 
		$query .= "   and Detpro= '000052'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000053.Fecha_data, hceidc_000053.Hora_data, hceidc_000002.Detdes, hceidc_000053.movdat, hceidc_000053.movusu, '000053', hceidc_000053.movhis "; 
		$query .= " from hceidc_000053, hceidc_000002  ";
		$query .= " where hceidc_000053.movcon in (11,26,17) ";
		$query .= "   and hceidc_000053.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'"; 
		$query .= "   and moving= '".$data[$i][2]."'"; 
		$query .= "   and Detpro= '000053'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000063.Fecha_data, hceidc_000063.Hora_data, hceidc_000002.Detdes, hceidc_000063.movdat, hceidc_000063.movusu, '000063', hceidc_000063.movhis ";
		$query .= " from hceidc_000063, hceidc_000002 ";
		$query .= " where hceidc_000063.movcon in (10,252,255) ";
		$query .= "   and hceidc_000063.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000063'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000088.Fecha_data, hceidc_000088.Hora_data, hceidc_000002.Detdes, hceidc_000088.movdat, hceidc_000088.movusu, '000088', hceidc_000088.movhis ";
		$query .= " from hceidc_000088, hceidc_000002 ";
		$query .= " where hceidc_000088.movcon in (31,19) ";
		$query .= "   and hceidc_000088.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000088'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000089.Fecha_data, hceidc_000089.Hora_data, hceidc_000002.Detdes, hceidc_000089.movdat, hceidc_000089.movusu, '000089', hceidc_000089.movhis ";
		$query .= " from hceidc_000089, hceidc_000002 ";
		$query .= " where hceidc_000089.movcon in (18,9) ";
		$query .= "   and hceidc_000089.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000089'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000084.Fecha_data, hceidc_000084.Hora_data, hceidc_000002.Detdes, hceidc_000084.movdat, hceidc_000084.movusu, '000084', hceidc_000084.movhis ";
		$query .= " from hceidc_000084, hceidc_000002 ";
		$query .= " where hceidc_000084.movcon in (8,11,19) ";
		$query .= "   and hceidc_000084.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000084'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000066.Fecha_data, hceidc_000066.Hora_data, hceidc_000002.Detdes, hceidc_000066.movdat, hceidc_000066.movusu, '000066', hceidc_000066.movhis ";
		$query .= " from hceidc_000066, hceidc_000002 ";
		$query .= " where hceidc_000066.movcon in (8,179) ";
		$query .= "   and hceidc_000066.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000066'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000064.Fecha_data, hceidc_000064.Hora_data, hceidc_000002.Detdes, hceidc_000064.movdat, hceidc_000064.movusu, '000064', hceidc_000064.movhis ";
		$query .= " from hceidc_000064, hceidc_000002 ";
		$query .= " where hceidc_000064.movcon in (23,95) ";
		$query .= "   and hceidc_000064.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000064'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000100.Fecha_data, hceidc_000100.Hora_data, hceidc_000002.Detdes, hceidc_000100.movdat, hceidc_000100.movusu, '000100', hceidc_000100.movhis ";
		$query .= " from hceidc_000100, hceidc_000002 ";
		$query .= " where hceidc_000100.movcon in (5,17) ";
		$query .= "   and hceidc_000100.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000100'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000101.Fecha_data, hceidc_000101.Hora_data, hceidc_000002.Detdes, hceidc_000101.movdat, hceidc_000101.movusu, '000101', hceidc_000101.movhis ";
		$query .= " from hceidc_000101, hceidc_000002 ";
		$query .= " where hceidc_000101.movcon in (18,13) ";
		$query .= "   and hceidc_000101.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000101'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000105.Fecha_data, hceidc_000105.Hora_data, hceidc_000002.Detdes, hceidc_000105.movdat, hceidc_000105.movusu, '000105', hceidc_000105.movhis ";
		$query .= " from hceidc_000105, hceidc_000002 ";
		$query .= " where hceidc_000105.movcon in (8,183,186) ";
		$query .= "   and hceidc_000105.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000105'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000108.Fecha_data, hceidc_000108.Hora_data, hceidc_000002.Detdes, hceidc_000108.movdat, hceidc_000108.movusu, '000108', hceidc_000108.movhis ";
		$query .= " from hceidc_000108, hceidc_000002 ";
		$query .= " where hceidc_000108.movcon in (8,137,140) ";
		$query .= "   and hceidc_000108.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000108'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000107.Fecha_data, hceidc_000107.Hora_data, hceidc_000002.Detdes, hceidc_000107.movdat, hceidc_000107.movusu, '000107', hceidc_000107.movhis ";
		$query .= " from hceidc_000107, hceidc_000002 ";
		$query .= " where hceidc_000107.movcon in (41,31) ";
		$query .= "   and hceidc_000107.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000107'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select hceidc_000085.Fecha_data, hceidc_000085.Hora_data, hceidc_000002.Detdes, hceidc_000085.movdat, hceidc_000085.movusu, '000085', hceidc_000085.movhis ";
		$query .= " from hceidc_000085, hceidc_000002 ";
		$query .= " where hceidc_000085.movcon in (10,12) ";
		$query .= "   and hceidc_000085.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000085'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " order by Fecha_data DESC, Hora_data DESC ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($w=0;$w<$num;$w++)
			{
				$row = mysql_fetch_array($err);
				$row[3] = str_replace("'","",$row[3]);
				$query = "insert ctgidc (idcfec, idchor, idcdes, idcval, idcusr, idctbl, idchce) values ('";
				$query .=  $row[0]."','";
				$query .=  $row[1]."','";
				$query .=  $row[2]."','";
				$query .=  $row[3]."','";
				$query .=  $row[4]."','";
				$query .=  $row[5]."',";
				$query .=  $row[6].")";
				//echo $query."<br>";
				$err2 = mysql_query($query,$conexidc) or die("ERROR GRABANDO CONTINGENCIA IDC : ".mysql_errno().":".mysql_error());
				$k++;
				echo "REGISTRO : ".$k." GRABADO <br>";
			}
		}
	}
	$querytime_after = array_sum(explode(' ', microtime(true)));
	$DIFF=$querytime_after - $querytime_before;

	$descripcion .= "Tiempo de ejecución: $DIFF Segundo(s)";

	/**
	 * Se inserta la información de la ejecución del cron en la tabla de logs
	 * 
	 * @author	Joel David Payares Hernández
	 * @since	2021-11-11
	 */
	$query_log = "
		INSERT INTO root_000118 (Medico  ,  Fecha_data ,  Hora_data ,		 		Logfun				 , 	 Logdes		 ,  Logfue , Loghue , Logema, Logest,   Seguridad )
		VALUES(				   '$empresa', 	 '$fecha'  ,   '$hora'  , 'Contingencia HCE IDC $fecha $hora', '$descripcion', '$fecha', '$hora',  'on' ,  'on' , 'C-$empresa');
	";
	$result_log = mysql_query( $query_log, $conex ) or die("Error insertando los datos del log en la tabla root_000118 : ".mysql_errno().":".mysql_error());
	echo "Tiempo : ".$DIFF." Segundo(s) <br>";

?>
</body>
</html>
