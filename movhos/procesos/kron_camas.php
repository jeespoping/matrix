<html>
<head>
  <title>MATRIX KRON_CAMAS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

	include_once("root/comun.php");
	/*Enero 17 de 2017 Jonatan Lopez
		Se agrega el campo habcpa en el insert a la tabla 20.	
	*/
	//==============================================================================================================================
	//Abril 28 de 2016 Jonatan Lopez
	//Se actualiza el centro de costos de una habitacion segun la informacion de unix (tabla inhab), pero solo si no hay paciente en la habitacion.
	//==============================================================================================================================
	//Enero 16 de 2013 Jonatan Lopez
	//Se agrega una consulta para que verifique el tipo de cama, y cuando haga el registro, la cama creada quede con el tipo de cama.
	//==============================================================================================================================
	

	

	$conex_o = odbc_connect('admisiones','','')
		or die("No se realizó Conexión");
	echo "<form action='kron_camas.php' method=post>";
	echo "<table border=0 align=center>";
	echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/movhos/logo_movhos.png'></td></tr>";
	echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>INFORME DE CAMAS ACTUALIZADAS</font></b></font></td></tr>";
	echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma'><b>NRO HABITACION</font></b></font></td><td align=center bgcolor=#dddddd><font face='tahoma'><b>CENTRO DE COSTOS</font></b></font></td></tr>";

	$query = " SELECT habcod, habcco, habact "
	        ."   FROM inhab "
	        //."  WHERE habact = 'S'"
	        ."  ORDER BY habcco,habcod";
	$err_o = odbc_do($conex_o,$query);
	$campos= odbc_num_fields($err_o);
	while (odbc_fetch_row($err_o))
	{
		$row=array();
		for($i=1;$i<=$campos;$i++)
		  {
			$row[$i-1]=odbc_result($err_o,$i);
		  }

		$query = "SELECT Habcod "
		        ."  FROM movhos_000020 "
		        ." WHERE Habcod='".$row[0]."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num == 0 and $row[2]=="S")   //Solo inserta las habitaciones que no existan en la 20 y que vengan Activas desde Unix
		  {
            //Consulta el tipo de cama
            $query = "SELECT Rhttip, Rhtzon "
                    ."  FROM movhos_000144 "
                    ." WHERE Rhtcod='".$row[0]."'";
            $res = mysql_query($query,$conex);
            $rowhab = mysql_fetch_array($res);

			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "INSERT movhos_000020 (medico, fecha_data, hora_data, Habcod, Habcco, Habhis, Habing, Habali, Habdis, Habest, Habtip, Habzon, Habcpa, Seguridad) values (";
			$query .=  "'movhos','";
			$query .=  $fecha."','";
			$query .=  $hora."','";
			$query .=  $row[0]."','";
			$query .=  $row[1]."',";
			$query .=  "'','','off','on','on','".$rowhab['Rhttip']."','".$rowhab['Rhtzon']."','".$row[0]."','C-movhos')";

			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO MOVIMIENTO DE HABITACIONES : ".mysql_errno().":".mysql_error());
			echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma'>".$row[0]."</font></td><td align=center bgcolor=#dddddd><font face='tahoma'>".$row[1]."</font></td></tr>";
		  }
		 else
		{
			// --> Si ya existe la cama, entonces le actualizo el estado segun como este en unix y siempre y cuando no tenga una historia asignada
			$estCama 	= ($row[2]=="N") ? "off" : "on";
			$desEst 	= ($row[2]=="N") ? "Inactivada" : "Activada";
				
			$q = "
			UPDATE movhos_000020
			   SET Habest = '".$estCama."'
			 WHERE habcod = '".$row[0]."'
			   AND habhis in ('',' ','NO APLICA') ";
				
			$err1 = mysql_query($q,$conex) or die("ERROR ELIMINANDO LA HABITACION ".$row[0]." DE LA TABLA 20 : ".mysql_errno().":".mysql_error());
			$wbor=mysql_affected_rows();

			if ($wbor>0)  //Si la cantidad borrada es mayor a cero
			   echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma'>".$row[0]."</font></td><td align=center bgcolor=#dddddd><font face='tahoma'>".$row[1]." ".$desEst." la tabla 000020 en Matrix</font></td></tr>";
		   
		}
	}
	echo "</table>";
	
		
	// Actualizacion de centro de costos de la habitacion segun la que se encuentre en unix.
	
	echo "<table border=0 align=center>";
	echo "<tr><td align=center bgcolor=#999999 colspan=4><font size=6 face='tahoma'><b>INFORME DE CAMBIOS DE CENTRO DE COSTOS</font></b></td></tr>";
	echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma'><b>NRO HABITACION</font></b></font></td>
	<td align=center bgcolor=#dddddd><font face='tahoma'><b>CENTRO DE COSTOS ANTERIOR</font></b></font></td>
	<td align=center bgcolor=#dddddd><font face='tahoma'><b>CENTRO DE COSTOS ACTUAL</font></b></font></td>
	<td align=center bgcolor=#dddddd><font face='tahoma'><b>ESTADO</font></b></td></tr>";
	
	$query = " SELECT habcod, habcco "
	        ."   FROM inhab "
	        ."  WHERE habact = 'S'"
	        ."  ORDER BY habcco,habcod";
	$err_o = odbc_do($conex_o,$query);
	$campos= odbc_num_fields($err_o);
	while (odbc_fetch_row($err_o))
	{
		$row=array();
		$row_aux=array();
		for($i=1;$i<=$campos;$i++)
		  {
			$row[$i-1]=odbc_result($err_o,$i);
			
		  if(!array_key_exists($row[0],$row_aux )){
			  
			  $row_aux[$row[0]] = $row[0];
			}
		  }

		$query_hab = "SELECT habcco, habcod, habhis, habing "
					."  FROM movhos_000020 "
		            ." WHERE Habcod='".trim($row[0])."'";
		$err = mysql_query($query_hab,$conex);	
		$row_hab = mysql_fetch_array($err);
		
		//Si la habitacion existe en la tabla 20 de movhos y la habitacion esta vacia, se hace la actualzacion del nuevo centro de costos que trae desde unix.
		if ( $row_hab['habcco'] != $row[1]){		
		
		if(trim($row_hab['habhis']) == '' and trim($row_hab['habing'] == ''))
		  {			
			//Se atualiza el centro de costos de la habitacion.
			$q = " UPDATE movhos_000020 "
				."    SET habcco = '".$row[1]."'"
				."  WHERE habcod = '".$row_hab['habcod']."'";
			$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
			echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma'>".$row_hab['habcod']."</font></td><td align=center bgcolor=#dddddd><font face='tahoma'>".$row_hab['habcco']."</font></td><td align=center bgcolor=#dddddd><font face='tahoma'>".$row[1]."</font></td><td bgcolor=#67D542>ACTUALIZADA</td></tr>";
			
			  
		  }
		 else{
			 
			 echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma'>".$row_hab['habcod']."</font></td><td align=center bgcolor=#dddddd><font face='tahoma'>".$row_hab['habcco']."</font></td><td align=center bgcolor=#dddddd><font face='tahoma'>".$row[1]."</font></td><td bgcolor=#EA3939>NO SE PUDO ACTUALIZAR YA QUE LA HABITACION ESTA OCUPADA</td><</tr>";
			 
		 }
		}
	}
		
	echo "</table>";

	// --> Actualizar espacios en blanco que se generan en el codigo de la cama
	$sqlTrim	= "
	UPDATE movhos_000020
	   SET Habcod = TRIM(Habcod)";
		
	mysql_query($sqlTrim,$conex);

	//Liberacion de conexion Unix
	odbc_close($conex_o);
	odbc_close_all();

?>
</body>
</html>
