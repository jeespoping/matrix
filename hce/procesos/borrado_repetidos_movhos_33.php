<html><head>  <title></title></head><body><?php
include_once("conex.php");  include_once("root/comun.php");  
  $wbasedato_mov = consultarAliasPorAplicacion($conex, '09', "movhos");  conexionOdbc($conex, $wbasedato_mov, &$conexUnix, 'facturacion');  function formato_hora($hora)  {	$hora_real = $hora;		if($hora=='0')		$hora_real =  '00';	if($hora=='1')		$hora_real =  '01';	if($hora=='2')		$hora_real =  '02';	if($hora=='3')		$hora_real =  '03';	if($hora=='4')		$hora_real =  '04';	if($hora=='5')		$hora_real =  '05';	if($hora=='6')		$hora_real =  '06';	if($hora=='7')		$hora_real =  '07';	if($hora=='8')		$hora_real =  '08';	if($hora=='9')		$hora_real =  '09';		return $hora_real;  }  		  // Asignaci�n de fecha y hora actual  $wfecha = date("Y-m-d");  $whora  = (string)date("H:i:s");  	// Consulto todas las historias con egresos repetidos	 $q = "SELECT historia_clinica, num_ingreso, COUNT( * ) AS veces, servicio, fecha_egre_serv, hora_egr_serv, seguridad			 FROM movhos_000033			WHERE tipo_egre_serv = 'ALTA'			GROUP BY 1 , 2		   HAVING COUNT( * ) > 1			ORDER BY fecha_egre_serv DESC ";	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	$cont1=0;	$cont2=0;	$cont3=0;	$cont4=0;		// Recorro las historias	while($row = mysql_fetch_array($res))	{		// Imprimo datos de historia		echo "<br>".$row[0]." | ".$row[1]." | ".$row[2]." | ".$row[3]." | ".$row[4]." | ".$row[5]." | ".$row[6]."<br>";		// Busco en unix la fecha de egreso real correspondiente a la historia e ingreso		$query = " SELECT egregr, egrhoe 					 FROM inmegr 					WHERE egrhis = '".$row[0]."' 					  AND egrnum = '".$row[1]."'";		$err_o = odbc_do($conexUnix,$query);		odbc_fetch_row($err_o);		$fec_egre_real = odbc_result($err_o,1);		$hor_egre_real_str = explode(".",odbc_result($err_o,2));		$hora_real = formato_hora($hor_egre_real_str[0]);		$minutos_real = formato_hora($hor_egre_real_str[1]);		$hor_egre_real = $hora_real.":".$minutos_real.":00";		//echo "Unix 1: ".odbc_result($err_o,2)." - Unix 2: ".$hor_egre_real."<br>";				// Busco si hay registro de egreso en movhos 33 con la misma fecha de unix		$qcom = "  SELECT id, Fecha_egre_serv, Hora_egr_serv					 FROM movhos_000033					WHERE tipo_egre_serv = 'ALTA'					  AND historia_clinica = '".$row[0]."'					  AND num_ingreso = '".$row[1]."'					  AND Fecha_egre_serv = '".$fec_egre_real."' ";		$rescom = mysql_query($qcom,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcom." - ".mysql_error());		$numcom = mysql_num_rows($rescom);				// Si coincide fecha egreso unix con fecha egreso movhos 33		// Conservo el registro y actualizo fecha y hora de alta en movhos 18		// Tambien borro los egresos de movhos 33 diferente a �ste		if($numcom>0)		{			$rowcom = mysql_fetch_array($rescom);						// Busco registros a eliminar			$qdel = "  SELECT movhos_000033.id						 FROM movhos_000033						WHERE tipo_egre_serv = 'ALTA'						  AND historia_clinica = '".$row[0]."'						  AND num_ingreso = '".$row[1]."'						  AND movhos_000033.id NOT IN (".$rowcom[0].") ";			$resdel = mysql_query($qdel,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qdel." - ".mysql_error());			$numdel = mysql_num_rows($resdel);			if($numdel>0)			{				while($rowdel = mysql_fetch_array($resdel))				{								echo "<b>Borrar id ".$rowdel[0]."</b><br>";					$qdel1 = " DELETE FROM movhos_000033 								WHERE id = '".$rowdel[0]."'";					$resdel1 = mysql_query($qdel1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qdel1." - ".mysql_error());				}			}			else			{				echo "<b>No encontr� registros a borrar</b><br>";			}			// Actualizo fecha alta definitiva en movhos 18			$qupd = "  UPDATE movhos_000018						  SET Ubifad='".$rowcom[1]."',Ubihad='".$rowcom[2]."'						WHERE ubihis = '".$row[0]."'						  AND ubiing = '".$row[1]."' ";			$resupd = mysql_query($qupd,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qupd." - ".mysql_error());			echo "<b>Actualizar movhos 18 $qupd </b><br>";			$cont2++;		} 		// Si no coincide fecha egreso unix con fecha egreso movhos 33		else		{			// Busco resgitro en la tabla movhos 18			$qubi = "  SELECT Ubifap, Ubihap, Ubifad, Ubihad						 FROM movhos_000018						WHERE ubihis = '".$row[0]."'						  AND ubiing = '".$row[1]."' ";			$resubi = mysql_query($qubi,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qubi." - ".mysql_error());			$rowubi = mysql_fetch_array($resubi);			// Busco el menor registro de egreso en movhos 33 para obtener id que no se borra			$qcom = "  SELECT movhos_000033.id, Fecha_egre_serv, Hora_egr_serv						 FROM movhos_000033						WHERE tipo_egre_serv = 'ALTA'						  AND historia_clinica = '".$row[0]."'						  AND num_ingreso = '".$row[1]."'						ORDER BY id ASC ";			$rescom = mysql_query($qcom,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcom." - ".mysql_error());			$numcom = mysql_num_rows($rescom);			$rowcom = mysql_fetch_array($rescom);			// Busco registros a eliminar			$qdel = "  SELECT id						 FROM movhos_000033						WHERE tipo_egre_serv = 'ALTA'						  AND historia_clinica = '".$row[0]."'						  AND num_ingreso = '".$row[1]."'						  AND id NOT IN (".$rowcom[0].") ";			$resdel = mysql_query($qdel,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qdel." - ".mysql_error());			$numdel = mysql_num_rows($resdel);						if($numdel>0)			{				while($rowdel = mysql_fetch_array($resdel))				{								echo "<b>Borrar id ".$rowdel[0]."</b><br>";					$qdel1 = " DELETE FROM movhos_000033 								WHERE id = '".$rowdel[0]."'";					$resdel1 = mysql_query($qdel1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qdel1." - ".mysql_error());				}			}			else			{				echo "<b>No encontr� registros a borrar</b><br>";			}			// Si la fecha de alta en proceso en movhos 18 es igual o menor que la fecha de alta en unix			if($rowubi[0]<=$fec_egre_real)			{				// Actualizo fecha alta definitiva en movhos 18 con datos Unix				$qupd = "  UPDATE movhos_000018							  SET Ubifad='".$fec_egre_real."',Ubihad='".$hor_egre_real."'							WHERE ubihis = '".$row[0]."'							  AND ubiing = '".$row[1]."' ";				$resupd = mysql_query($qupd,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qupd." - ".mysql_error());				echo "<b>Actualizar 1 movhos 18 $qupd </b><br>";				// Actualizo tambi�n fecha egreso en movhos 33 con datos Unix				$qupd = "  UPDATE movhos_000033							  SET Fecha_egre_serv='".$fec_egre_real."',Hora_egr_serv='".$hor_egre_real."'							WHERE Historia_clinica = '".$row[0]."'							  AND Num_ingreso = '".$row[1]."' 							  AND Tipo_egre_serv = 'ALTA'";				$resupd = mysql_query($qupd,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qupd." - ".mysql_error());				echo "<b>Actualizar movhos 33 $qupd </b><br>";				echo "<b>No coincide con fecha unix<br></b>";				$cont3++;			}			else			{				// Actualizo fecha alta definitiva en movhos 18 con datos movhos 33				$qupd = "  UPDATE movhos_000018							  SET Ubifad='".$rowcom[1]."',Ubihad='".$rowcom[2]."'							WHERE ubihis = '".$row[0]."'							  AND ubiing = '".$row[1]."' ";				$resupd = mysql_query($qupd,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qupd." - ".mysql_error());				echo "<b>Actualizar 2 movhos 18 $qupd </b><br>";				echo "<b>No coincide con fecha unix<br></b>";				$cont4++;			}		}				$cont1++;	}	echo "<br><br><b>Registros totales: $cont1 | Cont2: $cont2 | Cont3: $cont3  | Cont3: $cont4 </b>";?></body></html>