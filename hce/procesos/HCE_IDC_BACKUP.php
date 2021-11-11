<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
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
include_once("root/comun.php");
	

	

	$conexidc = mysqli_connect('192.168.0.2:3306','pmla','pmla800067065',"pacidc") or die("No se realizo Conexion con el IDC");
	//$conexidc = mysql_connect('190.248.93.238:3306','pmla','pmla800067065') or die("No se realizo Conexion con el IDC");
	// mysql_select_db("pacidc"); 	
	
	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");
	$empresa = "root";

	echo "CONEXION IDC OK<br>";	
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	
	$query = "DROP TABLE IF EXISTS tesp";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$query  = "CREATE TEMPORARY TABLE if not exists tesp as ";
	$query .= "select ".$wmovhos."_000048.meduma as med,".$whce."_000012.Selnda as esp ";
	$query .= "   from ".$wmovhos."_000047,".$wmovhos."_000048,".$whce."_000012 ";
	$query .= "   where ".$wmovhos."_000047.metest = 'on' "; 
	$query .= "	    and ".$wmovhos."_000047.mettdo = ".$wmovhos."_000048.medtdo "; 
	$query .= "	    and ".$wmovhos."_000047.metdoc = ".$wmovhos."_000048.meddoc "; 
	$query .= "	    and ".$wmovhos."_000048.meduma = ".$whce."_000012.Selcda ";
	$query .= "		and ".$whce."_000012.Seltab  = '1034' ";
	$query .= " group by 1,2 ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	
	$query = "CREATE UNIQUE INDEX Indice on tesp (med(8))";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	//                                  0                   1                    2                      3         4
	$query  = "select ".$whce."_000036.firusu,".$whce."_000036.Firhis,".$whce."_000036.Firing,".$whce."_000036.Fecha_data,tesp.esp "; 
	$query .= "    from ".$wmovhos."_000047,".$wmovhos."_000048,".$whce."_000036,tesp ";
	$query .= "    where ".$wmovhos."_000047.metest = 'on' "; 
	$query .= " 	 and ".$wmovhos."_000047.mettdo = ".$wmovhos."_000048.medtdo "; 
	$query .= " 	 and ".$wmovhos."_000047.metdoc = ".$wmovhos."_000048.meddoc "; 
	$query .= " 	 and ".$wmovhos."_000047.methis = ".$whce."_000036.firhis "; 
	$query .= " 	 and ".$wmovhos."_000047.meting = ".$whce."_000036.firing "; 
	$query .= " 	 and ".$whce."_000036.firpro in ('000051','000052','000063','000053','000088','000064','000089','000084','000066','000107','000100','000105','000085','000101','000108') "; 
	$query .= " 	 and ".$wmovhos."_000048.meduma = tesp.med ";
	$query .= " 	 and ".$whce."_000036.firusu in (select ".$whce."_000012.Selcda from ".$whce."_000012 where Seltab='1034' and Selnda= tesp.esp) ";
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
	$descripcion = "> cron contingencia HCE IDC, Numero de Pacientes: $j, ";
	
	echo "<b>TOTAL REGISTROS : ".$j." SELECCIONADOS</b><br>";
	$querytime_before = array_sum(explode(' ', microtime()));
	$k = 0;
	for ($i=0;$i<=$j;$i++)
	//for ($i=0;$i<=2;$i++)
	{
		/**
		 * Se registra el dato de la historia clínica en la posición actual del total de registros encontrados
		 * 
		 * @author	Joel David Payares Hernández
		 * @since	2021-11-11
		 */
		$descripcion .= "historia: ".$data[$i][1].", ";

		echo "REGISTRO : ".$data[$i][0]." ".$data[$i][1]." ".$data[$i][2]." ".$data[$i][3]."<br> ";
		$query  = " Select ".$whce."_000051.Fecha_data, ".$whce."_000051.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000051.movdat, ".$whce."_000051.movusu, '000051', ".$whce."_000051.movhis ";
		$query .= " from ".$whce."_000051, ".$whce."_000002 ";
		$query .= " where ".$whce."_000051.movcon in (6,191,195) ";
		$query .= "   and ".$whce."_000051.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'"; 
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000051'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000052.Fecha_data, ".$whce."_000052.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000052.movdat, ".$whce."_000052.movusu, '000052', ".$whce."_000052.movhis ";
		$query .= " from ".$whce."_000052, ".$whce."_000002  ";
		$query .= " where ".$whce."_000052.movcon in (6,149,151) "; 
		$query .= "   and ".$whce."_000052.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'"; 
		$query .= "   and Detpro= '000052'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000053.Fecha_data, ".$whce."_000053.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000053.movdat, ".$whce."_000053.movusu, '000053', ".$whce."_000053.movhis "; 
		$query .= " from ".$whce."_000053, ".$whce."_000002  ";
		$query .= " where ".$whce."_000053.movcon in (11,26,17) ";
		$query .= "   and ".$whce."_000053.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'"; 
		$query .= "   and moving= '".$data[$i][2]."'"; 
		$query .= "   and Detpro= '000053'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000063.Fecha_data, ".$whce."_000063.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000063.movdat, ".$whce."_000063.movusu, '000063', ".$whce."_000063.movhis ";
		$query .= " from ".$whce."_000063, ".$whce."_000002 ";
		$query .= " where ".$whce."_000063.movcon in (10,252,255) ";
		$query .= "   and ".$whce."_000063.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000063'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000088.Fecha_data, ".$whce."_000088.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000088.movdat, ".$whce."_000088.movusu, '000088', ".$whce."_000088.movhis ";
		$query .= " from ".$whce."_000088, ".$whce."_000002 ";
		$query .= " where ".$whce."_000088.movcon in (31,19) ";
		$query .= "   and ".$whce."_000088.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000088'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000089.Fecha_data, ".$whce."_000089.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000089.movdat, ".$whce."_000089.movusu, '000089', ".$whce."_000089.movhis ";
		$query .= " from ".$whce."_000089, ".$whce."_000002 ";
		$query .= " where ".$whce."_000089.movcon in (18,9) ";
		$query .= "   and ".$whce."_000089.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000089'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000084.Fecha_data, ".$whce."_000084.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000084.movdat, ".$whce."_000084.movusu, '000084', ".$whce."_000084.movhis ";
		$query .= " from ".$whce."_000084, ".$whce."_000002 ";
		$query .= " where ".$whce."_000084.movcon in (8,11,19) ";
		$query .= "   and ".$whce."_000084.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000084'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000066.Fecha_data, ".$whce."_000066.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000066.movdat, ".$whce."_000066.movusu, '000066', ".$whce."_000066.movhis ";
		$query .= " from ".$whce."_000066, ".$whce."_000002 ";
		$query .= " where ".$whce."_000066.movcon in (8,179) ";
		$query .= "   and ".$whce."_000066.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000066'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000064.Fecha_data, ".$whce."_000064.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000064.movdat, ".$whce."_000064.movusu, '000064', ".$whce."_000064.movhis ";
		$query .= " from ".$whce."_000064, ".$whce."_000002 ";
		$query .= " where ".$whce."_000064.movcon in (23,95) ";
		$query .= "   and ".$whce."_000064.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000064'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000100.Fecha_data, ".$whce."_000100.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000100.movdat, ".$whce."_000100.movusu, '000100', ".$whce."_000100.movhis ";
		$query .= " from ".$whce."_000100, ".$whce."_000002 ";
		$query .= " where ".$whce."_000100.movcon in (5,17) ";
		$query .= "   and ".$whce."_000100.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000100'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000101.Fecha_data, ".$whce."_000101.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000101.movdat, ".$whce."_000101.movusu, '000101', ".$whce."_000101.movhis ";
		$query .= " from ".$whce."_000101, ".$whce."_000002 ";
		$query .= " where ".$whce."_000101.movcon in (18,13) ";
		$query .= "   and ".$whce."_000101.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000101'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000105.Fecha_data, ".$whce."_000105.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000105.movdat, ".$whce."_000105.movusu, '000105', ".$whce."_000105.movhis ";
		$query .= " from ".$whce."_000105, ".$whce."_000002 ";
		$query .= " where ".$whce."_000105.movcon in (8,183,186) ";
		$query .= "   and ".$whce."_000105.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000105'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000108.Fecha_data, ".$whce."_000108.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000108.movdat, ".$whce."_000108.movusu, '000108', ".$whce."_000108.movhis ";
		$query .= " from ".$whce."_000108, ".$whce."_000002 ";
		$query .= " where ".$whce."_000108.movcon in (8,137,140) ";
		$query .= "   and ".$whce."_000108.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000108'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000107.Fecha_data, ".$whce."_000107.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000107.movdat, ".$whce."_000107.movusu, '000107', ".$whce."_000107.movhis ";
		$query .= " from ".$whce."_000107, ".$whce."_000002 ";
		$query .= " where ".$whce."_000107.movcon in (41,31) ";
		$query .= "   and ".$whce."_000107.Fecha_data >= '".$data[$i][3]."'";
		$query .= "   and movhis= '".$data[$i][1]."'";  
		$query .= "   and moving= '".$data[$i][2]."'";
		$query .= "   and Detpro= '000107'"; 
		$query .= "   and Detcon= Movcon";
		$query .= " UNION ALL ";
		$query .= " Select ".$whce."_000085.Fecha_data, ".$whce."_000085.Hora_data, ".$whce."_000002.Detdes, ".$whce."_000085.movdat, ".$whce."_000085.movusu, '000085', ".$whce."_000085.movhis ";
		$query .= " from ".$whce."_000085, ".$whce."_000002 ";
		$query .= " where ".$whce."_000085.movcon in (10,12) ";
		$query .= "   and ".$whce."_000085.Fecha_data >= '".$data[$i][3]."'";
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

	/**
	 * Se inserta la información de la ejecución del cron en la tabla de logs
	 * 
	 * @author	Joel David Payares Hernández
	 * @since	2021-11-11
	 */
	$query_log = "
		INSERT INTO root_000118 (Medico  ,  Fecha_data ,  Hora_data ,		 Logfun			, 	Logdes		,  Logfue , Loghue , Logema, Logest,   Seguridad )
		VALUES(				   '$empresa', 	 '$fecha'  ,   '$hora'  , 'Contingencia HCE IDC $fecha $hora', '$descripcion', '$fecha', '$hora',  'on' ,  'on' , 'C-$empresa');
	";
	$result_log = mysql_query( $query_log, $conex ) or die("Error insertando los datos del log en la tabla root_000118 : ".mysql_errno().":".mysql_error());
	echo "Tiempo : ".$DIFF." Segundo(s) <br>";

?>
</body>
</html>
