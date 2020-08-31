<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
include_once("root/comun.php");
 // if(!isset($accion))
// {
   // echo '<!DOCTYPE html>';
// }
if(isset($_SESSION['user']))
	{
		$key=substr($_SESSION['user'],2,strlen($_SESSION['user']));
	}
	else
	{
		if( !isset($accion) )
		{
			echo "<script language='JavaScript'>
						alert(\"Ingresar a Matrix nuevamente. Se cerro la session.\");
						window.location.href = '../../../matrix/f1.php';

				</script>";
		}
		else
		{
			$data = array('error'=>1,'mensaje'=>'Ingresar a Matrix nuevamente. Se cerro la session','html'=>'');
			echo json_encode($data);
		}
		EXIT;
	}

$key = substr($user, 2, strlen($user)); //se eliminan los dos primeros digitos







define("NOMBRE_BORRAR",'Eliminar');
define("NOMBRE_ADICIONAR",'Adicionar');

//Busca el codigo de la fuente para seleccionarla.
if (isset($accion) AND $accion == 'dato_tipo_evento'){


	$data = array('error'=>0,'mensaje'=>'','html'=>'','cod_fuente'=>'');

	$sql = "Select Tienid
			  FROM ".$wbasedato."_000015
			 WHERE Tieval ='".$fuente_reg."'
			   AND Tieest ='on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array($res);

	$data['cod_fuente'] = $row['Tienid'];

	echo json_encode($data);
	return;

}

//Busca el codigo de la fuente para seleccionarla.
if (isset($accion) AND $accion == 'fuente_registrada'){


	$data = array('error'=>0,'mensaje'=>'','html'=>'','cod_fuente'=>'');

	$sql = "Select Fuenid
			  FROM ".$wbasedato."_000014
			 WHERE Fueval ='".$fuente_reg."'
			   AND Fueest ='on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array($res);

	$data['cod_fuente'] = $row['Fuenid'];

	echo json_encode($data);
	return;

}

//Funcion que permite grabar solamente observaciones para eun evento sin cambiarle estado.
if (isset($accion) AND $accion == 'select_estados')
 {


	$data = array('error'=>0,'mensaje'=>'','html'=>'','observaciones_actuales'=>'','sql'=>'' );

	$sql = "Select Estcod,Estdes,Esttex
			  FROM root_000093
			 WHERE Esttip='NC'
			   AND Estest='on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	$array_estados = array();
	$data['sql'] = $sql;
	while($row = mysql_fetch_array($res)){

		if(!array_key_exists($row['Estcod'], $array_estados)){

			$array_estados[$row['Estcod']] = array('codigo'=> $row['Estcod'], 'descripcion'=>$row['Estdes']);

		}

	}

	//opc[0] = "-Seleccione...-";
	$data['html'] .= "<select onchange='estRechazada();evaluarEstado(&quot;gescal&quot;);' class='validarEA validarNC' id='estados_nc' name='estados'>";

	if ($evento=="NC")
	{
		if (($niv == 'undefined' && $est == 'undefined') || ($niv == "" && $est == ""))
		{
			//cuando se ingresa
			$data['html'] .= "<option value='".$array_estados['01']['codigo']."'>".$array_estados['01']['descripcion']."</option>"; //Pendiente de aprobacion

		}
		else if($niv == 1 || $niv == 3)
		{
			//jefe inmediato
			$data['html'] .= "<option value=''>Seleccione...</option>";
			$data['html'] .= "<option value='".$array_estados['02']['codigo']."'>".$array_estados['02']['descripcion']."</option>"; //Aprobada
			$data['html'] .= "<option value='".$array_estados['03']['codigo']."'>".$array_estados['03']['descripcion']."</option>"; //Rechazada

		}
		else if($niv == 2 || $niv == 4)
		{

			if($est=='03')
			{
				$data['html'] .= "<option value=''>Seleccione...</option>";
				$data['html'] .= "<option value='".$array_estados['02']['codigo']."'>".$array_estados['02']['descripcion']."</option>"; //Aprobada
				$data['html'] .= "<option value='".$array_estados['03']['codigo']."'>".$array_estados['03']['descripcion']."</option>"; //Rechazada

			}
			else
			{//unidad que genero
				$data['html'] .= "<option value=''>Seleccione...</option>";
				$data['html'] .= "<option value='".$array_estados['04']['codigo']."'>".$array_estados['04']['descripcion']."</option>"; //Aprobada (Correccion)
				$data['html'] .= "<option value='".$array_estados['03']['codigo']."'>".$array_estados['03']['descripcion']."</option>"; //Rechazada (Rechazada)

			}

		}
		else if($niv == 5 || $niv == 6)
		{
			//llega a alejANDro correccion y acciones
			$data['html'] .= "<option value=''>Seleccione...</option>";
			$data['html'] .= "<option value='".$array_estados['05']['codigo']."'>".$array_estados['05']['descripcion']."</option>"; //Aprobada (Analizada)
			$data['html'] .= "<option value='".$array_estados['06']['codigo']."'>".$array_estados['06']['descripcion']."</option>"; //Rechzada (Cerrada)


		}

	}
	else  //EVENTOS ADVERSOS
	{
		if (($niv == 'undefined' && $est == 'undefined') || ($niv == "" && $est == ""))
		{
			//Al ingresar una EA
			$data['html'] .= "<option value='".$array_estados['01']['codigo']."'>".$array_estados['01']['descripcion']."</option>"; //Pendiente de aprobacion


		}
		else if($niv == 1 || $niv == 2 || $niv == 4)
		{
			//le llega a unidades de control
			$data['html'] .= "<option value=''>Seleccione...</option>";
			$data['html'] .= "<option value='".$array_estados['02']['codigo']."'>".$array_estados['02']['descripcion']."</option>"; //Aprobada
			$data['html'] .= "<option value='".$array_estados['03']['codigo']."'>".$array_estados['03']['descripcion']."</option>"; //Rechazada


		}
		else if($niv == 3 || $niv == 5)
		{
			if($est=='03')
			{
				$data['html'] .= "<option value=''>Seleccione...</option>";
				$data['html'] .= "<option value='".$array_estados['02']['codigo']."'>".$array_estados['02']['descripcion']."</option>"; //Aprobada
				$data['html'] .= "<option value='".$array_estados['03']['codigo']."'>".$array_estados['03']['descripcion']."</option>"; //Rechazada

			}
			else
			{
				//unidad que genero
				$data['html'] .= "<option value=''>Seleccione...</option>";
				$data['html'] .= "<option value='".$array_estados['04']['codigo']."'>".$array_estados['04']['descripcion']."</option>"; //Correccion
				$data['html'] .= "<option value='".$array_estados['03']['codigo']."'>".$array_estados['03']['descripcion']."</option>"; //Rechazada

			}

		}
		else if($niv==6 || $niv==7)
		{
			//llega  correccion a magda y acciones
			$data['html'] .= "<option value=''>Seleccione...</option>";
			$data['html'] .= "<option value='".$array_estados['05']['codigo']."'>".$array_estados['05']['descripcion']."</option>"; //Analizada
			$data['html'] .= "<option value='".$array_estados['06']['codigo']."'>".$array_estados['06']['descripcion']."</option>"; //Cerrada

		}

	}

	$data['html'] .= "</select>";

	echo json_encode($data);
	return;
}


//Funcion que permite grabar solamente observaciones para eun evento sin cambiarle estado.
if (isset($accion) AND $accion == 'grabar_solo_observacion')
 {

	$data = array('error'=>0,'mensaje'=>'','html'=>'','observaciones_actuales'=>'');

	$sql_update = "UPDATE ".$wbasedato."_000001
						  SET  Nceobs = concat( '".$obs."','\n', Nceobs )
						WHERE  Id='".$id_Evento."' ";
	$update_cco = mysql_query( $sql_update );
	$update = mysql_affected_rows();

	if($update > 0){

		$sql = "SELECT Nceobs
				  FROM ".$wbasedato."_000001
				 WHERE  Id='".$id_Evento."' ";
		$res = mysql_query( $sql );
		$row = mysql_fetch_array($res);

		$data['observaciones_actuales'] = utf8_decode($row['Nceobs']);
		$data['mensaje'] = 'Se ha agregado observacion al evento.';


	}else{
		$data['mensaje'] = 'No se ha agregado observacion al evento.';
		$data['error'] = 1;
	}




	echo json_encode($data);
	return;
}

//Funcion que permite la actualizacion del centro de costos que generó.
if (isset($accion) AND $accion == 'cambiar_cco_genero')
 {

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$nuevo_cco_genero = explode("-",$cco_genero_nuevo);

	$sql_update_cco = " UPDATE ".$wbasedato."_000001
					       SET  Nceccg = '".$nuevo_cco_genero[0]."', Ncepro = '".$procesos_cco_modal."'
				         WHERE  id = '".$id_Evento."'";
	$update_cco = mysql_query( $sql_update_cco );
	$update = mysql_affected_rows();

	if($update > 0){
	$data['mensaje'] = 'El centro de costos que genero ha sido actualizado.';
	}else{
	$data['mensaje'] = 'El centro de costos que genero no ha sido actualizado.';
	$data['error'] = 1;
	}

	echo json_encode($data);
	return;
}

//Este bloque despliega los procesos asociados a un centro de costos.
if (isset($accion) AND $accion == 'mostrar_procesos_cco')
 {

	$data = array('error'=>0,'mensaje'=>'','procesos'=>'');
	$query = '';

	$array_cco = explode("-",$cco);
	$cco = $array_cco[0];

	$query = "Select Procod, Prodes
			    FROM ".$wbasedato."_000008
			   WHERE Procco = '".$cco."'
			     AND Proest = 'on' ";
	$res2 = mysql_query( $query ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	$num_procesos = mysql_num_rows($res2);

	$obligatorio = ($modal != 'ok') ? 'class="campoRequerido"' : "";

	$option .= '<select id="select_procesos_cco" '.$obligatorio.' >';
	$option .= '<option value="">Seleccione..</option>';

	while($rows2 = mysql_fetch_array( $res2 ))
	{
		$option .= '<option value="'.$rows2['Procod'].'">'.$rows2['Prodes'].'</option>';
	}
	$option .= '</select>';

	//Si el cco tiene procesos asociados, devolvera el desplegable con los procesos, sino devolvera vacio.
	if($num_procesos > 0){
	$data['procesos'] = $option;
	}else{
	$data['procesos'] = '';
	}

	echo json_encode($data);
	return;
}


if (isset($accion) && isset($wbasedato) && isset($evento))
{
	/*Se hace la parte de consultar si el usuario que ingreso tiene rol y cual es*/
	$q="select Rnccod,Roldes
			FROM ".$wbasedato."_000007,".$wbasedato."_000004
			WHERE Rnctip ='".$evento."'
			AND Rncest ='on'
			AND Rnccod = Rolcod
			AND Rolest ='on'
			AND Roltip =Rnctip
			AND Roldes = '".$key."'";

		if ($evento == 'NC')
		{
			$q.=" AND Rncanc='on'";
		}
		else
		{
			$q.=" AND Rncaea='on'";
		}

			$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );

			for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
			{
				if( !isset( $ARR2[ $rows1['Rnccod'] ] ) )
				{
					$usu=",".$rows1['Rnccod'];
					$ARR2[ $rows1['Rnccod'] ] = 1;
				}

				if($i == 0)
				{
					$usu1.=$rows1['Roldes']; //Ncecag ccog
				}
				else
				{
					$usu1.=",".$rows1['Roldes']; //Ncecag ccog
				}

			}
			if (substr($usu, 0, 1) == ",")
			{
				$usu = substr( $usu, 1 ); //para quitar la , del principio
			}

	/*Fin de la parte de consultar si el usuario que ingreso tiene rol y cual es*/
}

if (isset($accion) AND $accion == 'buscar')
 {

	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	$query = '';

    if ($cco == 'costosyp_000005'){
			$query = "Select Ccocod, Cconom
			    FROM ".$cco."
			   WHERE (Cconom like '%".$valor."%' or Ccocod like '%".$valor."%')
			     AND Ccoest = 'on' AND Ccoemp = '01' ";
    }
    else{
			$query = "Select Ccocod, Cconom
			    FROM ".$cco."
			   WHERE (Cconom like '%".$valor."%' or Ccocod like '%".$valor."%')
			     AND Ccoest = 'on' ";
	}

	$res2 = mysql_query( $query ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	$option = '<option value="">Seleccione..</option>';
	if ($filtro=='todos')
	{
		$option = '<option value="%">Todos</option>';
	}
	while($rows2 = mysql_fetch_array( $res2 ))
	{
		$option .= '<option value="'.$rows2['Ccocod'].'">'.$rows2['Ccocod'].'-'.utf8_encode($rows2['Cconom']).'</option>';
	}
	$data['options'] = $option;
	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'enviar')
 {

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$ccod1=explode("-",$ccod); //centro de costo que detecto
	$ccod=$ccod1[0];

	//llama a la funcion perfiles para traer los datos del usuario con su respectivo jefe y el centro de costo al que pertenecen
	$perfil=array();
	$perfil=perfiles($datos,$wemp_pmla1,$key2); //no preguntar el cc porque no siempre es el mismo
	//$info=$perfil['Ajeuco']." ".$perfil['Ajeucr']." ".$perfil['Ajecco'];

	$coad=explode("-",$perfil['Ajeucr']);  //JEFE INMEDIATO DE LA PERSONA QUE CREA EL EVENTO SOLO SE ENVIA AL LOG
	$coad=$coad[0];
	$niv=1;

	//se consulta el consecutivo para guardarlo en la tabla de los eventos gescal_000001
	$sqlcon="Select Connum
			   FROM ".$wbasedato."_000006
			  WHERE Concod='".$evento."'
			    AND Conest='on'";
	$rescon = mysql_query( $sqlcon ) ;
	if ($rescon)
	{
		$rowscon = mysql_fetch_array($rescon);
		$conse=$rowscon['Connum'];
		$conse++;
	}
	else
	{
		$data[ 'error' ] = 1;
		$data[ 'mensaje' ] = utf8_encode( "Error consultando el consecutivo ");
	}



	if ($evento=="NC")
	{

        //Si el usuario tiene rol de coordinador guarda el registro con estado aprobado.
        if($rol_coor == 'on')
        {

            $q="Select Ajeucr,Ajecoo,Ajeccc
                  FROM ".$datos."_000008,".$datos."_000013
                 WHERE Ideuse=Ajeucr
                   AND Forest='on'
                   AND Ideest='on'
                   AND Ajecoo='on'
                   AND FIND_IN_SET( '".$ccog."',Ajeccc ) > 0
              group by Ajeucr"; //(find_in_set) encuentre el cc que trae en una lista separadas por ,
            $res = mysql_query($q);

			if($res)
			{
				if (mysql_num_rows($res)>0)
				{
					for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
					{
						$cag1=explode("-",$rows1['Ajeucr']);
						$cag1=$cag1[0];
						if($i == 0)
						{
							$cag.=$cag1; //Ncecag ccog
						}
						else
						{
							$cag.=",".$cag1; //Ncecag ccog
						}
					}
				}
			}
			else
			{
				$data[ 'error' ] = 1;
				$data[ 'mensaje' ] = utf8_encode( "Error consultando si el usuario es coordinador ");
			}


			if ($fuente != 'Auditoria_Interna')
				{
				//Nivel por defecto cuANDo un coordinador o jefe registra
				$niv = $wnivel;
				//Estado que se asigna cuANDo es coordinador
				$est = $estnc_coord;
				}
        }

		$proceso_cco = ($proceso_cco != 'undefined') ? $proceso_cco : "";

		$query = "INSERT INTO ".$wbasedato."_000001 (Medico, Fecha_data, Hora_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue,
							Nceest, Ncecne, Nceniv, Ncenum, Nceact, Ncecag, Ncecla, Ncepro, Seguridad)
				  VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$ccod."', '".$key."', '".$fechad."', '".$horad."', '".$ccog."', '".utf8_decode($desc)."', '', '".$fuente."',
				 '".$est."', '".$evento."', '".$niv."', '".$conse."', 'on', '".$cag."', '".$clasificacion."','".$proceso_cco."','C-".$key."')";

		 $res2 = mysql_query( $query ) ; //mirar conex
			if( $res2 )
			{

				if (mysql_affected_rows()>0)
				{
					$id_evento=mysql_insert_id();
				}
				/*****Se inserta en la tabla de log**/
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$coad."', '".$key."', '".$est."','".$id_evento."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex
				if( !$resSql )
				{
					$data[ 'error' ] = 1;
					$data[ 'mensaje' ] = utf8_encode( "Error insertando el evento en la tabla de log ");
				}
				/*******/
			}
			else
			{
				$data[ 'error' ] = 1;
				$data[ 'mensaje' ] = utf8_encode( "Error insertando el evento en la tabla gescal_000001 ");
			}
	}
	else
	{

		//se consulta el codigo de la persona encargada de hacer la seleccion - usuario que controla
		$q="Select Rnccod
			  FROM ".$wbasedato."_000007
			 WHERE Rnctip='".$evento."'
			   AND Rncaea='on'
			   AND Rncest='on'
			   ";
		$res = mysql_query($q) ;
		if ($res)
		{
			$usu="";

			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				if($i == 0)
				{
					$usu=$rows1['Rnccod'];
				}
				else
				{
					$usu.=",".$rows1['Rnccod'];
				}

			}
		}
		else
		{
			$data[ 'error' ] = 1;
			$data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla de roles ");
		}
		//nceccd - ncecad
		$query = "INSERT INTO ".$wbasedato."_000001 (Medico, Fecha_data, Hora_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, Ncetaf,
									Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, Ncepre, Ncecen, Nceana,
									Ncecne, Ncecon, Nceres, Nceniv, Ncenum, Nceact,Seguridad)
				   VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$ccod."', '".$key."', '".$fechad."', '".$horad."', '".$ccog."', '".utf8_decode($desc)."', '', '".$inf."', '".$est."', '".utf8_decode($afec)."',
							'".utf8_decode($nom_afec)."', '".$ed_afec."', '".$id_afec."', '".$hc."', '".utf8_decode($desc_acc)."', '', '', '', '', '', '', '',
							'".$evento."', '".$usu."', '".$resp."', '".$niv."', '".$conse."','on','C-".$key."')";

		$res2 = mysql_query( $query ) ; //mirar conex

		if ($res2)
		{
			if (mysql_affected_rows()>0)
			{
				$id_evento=mysql_insert_id();
			}

			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$key."', '".$key."', '".$est."','".$id_evento."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) ; //mirar conex
			if(!$resSql)
			{
				$data[ 'error' ] = 1;
				$data[ 'mensaje' ] = utf8_encode( "Error insertando el evento en la tabla gescal_000005 ");
			}
			/*******/
		}
		else
		{
			$data[ 'error' ] = 1;
			$data[ 'mensaje' ] = utf8_encode( "Error insertando el evento en la tabla gescal_000001 ");
		}


		/***** Para la insercion de causas ****
		$arr_inserts = array();
		$filas = explode("|-|",$array_campos);
		foreach($filas as $key1 => $valores_campos)
			{
				//id_accion+"|"+id_plan+"|"+accion_val+"|"+fechar+"|"+fechapr+"|"+seguimiento+"|"+responsable+"|"+estado
				$campos = explode("|",$valores_campos);
				// print_r($campos);
				$cuales 	= $campos[0]; //lo que seleccionaron en el select
				$directo    = $campos[1]; //si es directo o indirecto


				//INSERT
				$arr_inserts[] = "('".$wbasedato."','".date("Y-m-d")."', '".date("H:i:s")."','".$cuales."','".$evento."','".$directo."','".$id_evento."','on','C-".$wbasedato."')";

			}

		if(count($arr_inserts) > 0)
			{
				$qInsert = "INSERT INTO ".$wbasedato."_000002
							(Medico,Fecha_data,Hora_data,Caucod,Cautip,Caucon,Caneid,Cauest,Seguridad)
							VALUES	".implode(",",$arr_inserts).';';


			  	$res3 = mysql_query( $qInsert ) or die( mysql_errno()." - Error en el query $qInsert - ".mysql_error() ); //mirar conex

			}


		*********/
	}

	//se actualiza la tabla de consecutivo
	$sqlcon_update="UPDATE ".$wbasedato."_000006
					   SET  Connum = '".$conse."'
				     WHERE Concod='".$evento."'";
	$rescon_update = mysql_query( $sqlcon_update ) ; //mirar conex
	if (!$rescon_update)
	{
		$data[ 'error' ] = 1;
		$data[ 'mensaje' ] = utf8_encode( "Error actualizando consecutivo de los eventos ");
	}

    if ($data[ 'error' ] == 0)
	{
		$data['mensaje'] .= utf8_encode("Se guardó correctamente "); //".$query." -".$qInsert."
	}

	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'adicionar_fila')
{
    $data = array('error'=>0,'html'=>'','mensaje'=>'');

                $data['html'] = "<tr id='".$id_fila."' class='fila2'>
							<td>
								<input type='hidden' id='".$id_fila."_id' name='".$id_fila."_id' value='' >";

								//se trae la clase de factor
								$sql = "Select Tipcod,Tipdes
										  FROM root_000094
										 WHERE Tiptip='EA'
										   AND Tipest='on'";
							    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

								$data['html'] .="<SELECT NAME='".$id_fila."_fact_1' id='".$id_fila."_fact_1' onchange='llenar_hijo(\"".$id_fila."_fact_1\",\"".$id_fila."_cuales\",\"".$key2."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla1."\",\"".$cco."\")'>"; //llenar_hijo(\"".$id_fila."_cuales\")
								$data['html'] .="<option value=''>Seleccione..</option>";
											for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
											{

												if( $clasificacion != trim( $rows['Tipcod'] ) )
												{
													$data['html'] .= "
																	<option value='".$rows['Tipcod']."'>".utf8_encode($rows['Tipdes'])."</option>";
												}
												else
												{
													$data['html'] .= "
																	<option value='".$rows['Tipcod']."' selected>".utf8_encode($rows['Tipdes'])."</option>";
												}
											}
								$data['html'] .="</SELECT>";
								$data['html'] .="</td>

								<td><SELECT NAME='".$id_fila."_fact_s_n' id='".$id_fila."_fact_s_n' onchange='ocultar_mostrar(\"".$id_fila."_cuales\",\"".$id_fila."_fact_s_n\")'>
									<OPTION VALUE=''>Seleccione...</OPTION>
									<OPTION VALUE='Si' selected='selected'>Si</OPTION>
									<OPTION VALUE='No'>No</OPTION>
									</SELECT>
								</td>
								<td><SELECT NAME='".$id_fila."_cuales' id='".$id_fila."_cuales'>
									<OPTION VALUE=''>Seleccione</OPTION>

									</SELECT>
								</td>
								<td><SELECT NAME='".$id_fila."_indi' id='".$id_fila."_indi'>
									<OPTION VALUE=''>Seleccione...</OPTION>
									<OPTION VALUE='Directo'>Directo</OPTION>
									<OPTION VALUE='Indirecto'>Indirecto</OPTION>
									</SELECT>
								</td>
								<td align='center'><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\");'>".NOMBRE_BORRAR."</span></td>
							</tr>";

        echo json_encode($data);
		return;
}

if (isset($accion) AND $accion == 'guardar_planes')
{

                $filas = explode("||",$array_campos);
                foreach($filas as $key => $valores_campos)
                {;
                    $campos = explode("|",$valores_campos); // id_registro|accion|fecha_reunion|fecha_proxima_reunion|seguimiento
                    print_r($campos);
                    $id_registro= $campos[0];
                    $accion_plan= $campos[1];
                    $fechar     = $campos[2];
                    $fechapr    = $campos[3];
                    $seguimiento= $campos[4];
                    if($id_registro != '')
                    {
                        //UPDATE

                    }
                    else
                    {
                        //INSERT
                    }
                }
                $data['mensaje'] = utf8_encode("Se guardó correctamente");
                echo json_encode($data);

    return;
}

if (isset($accion) AND $accion == 'rechazada')
{
	// 

	// 


	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	$text1 = unserialize(base64_decode($tex));
	//$data['cod'] = $text1['cod']

	if(array_key_exists($estado,$text1))
	{
		$data['html'] = utf8_encode("
		<table>
			<tr>
				<td>¿Porque rechaza?</td><td><textarea name='est_rechazada' id='est_rechazada' rows='4' cols='100'></textarea></td>
			</tr>
		</table>");
	}
	echo json_encode($data);
	return;
}


//======== Consulto los procesos asociados a los cco y creo un arreglo  $array_procesos_cco[codigo] = descripcion; Jonatan Lopez 19 Marzo 2014 =======================

if($wbasedato != ''){
	$sql_procesos_cco = "  SELECT Procod, Prodes
							 FROM ".$wbasedato."_000008
							WHERE Proest = 'on'";
	$res_procesos_cco = mysql_query( $sql_procesos_cco, $conex );

	$array_procesos_cco = array();

	while($row_procesos_cco = mysql_fetch_array($res_procesos_cco)){

		if(!array_key_exists($row_procesos_cco['Procod'], $array_procesos_cco)){

			$array_procesos_cco[$row_procesos_cco['Procod']] = array('Procod'=>$row_procesos_cco['Procod'], 'Prodes'=>$row_procesos_cco['Prodes']);
		}
	}
}
//========================================================================



//se consultan las enviadas por el usuario y por los subalternos del usuario.
if (isset($accion) AND $accion == 'lista_enviadas')
{

	include_once("root/comun.php");
	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	if($wemp_pmla1 !='01')
	{
		$wemp_pmla1='01';
	}
	$user_aut_aud_inter_nc = consultarAliasPorAplicacion($conex, $wemp_pmla1, "AutAudInterna");
	//Se crea un array con los codigos autorizados para ver las pendientes por aprobar, y asi puedan aprobar nc de aud. interna.
	$datos_usuarios_aud_int = explode("-", $user_aut_aud_inter_nc );

	//Esta funcion trae los subalternos en un arreglo hasta el tercer nivel.
	$wsubalternos = buscar_subalternos($key2, $key);	 //Este arreglo trae los subalternos del usuario que esta activo y los subalternos de esos subalternos.
												 // Ejm:
												 /*    Array([05000] =>() //Jefe
															 [03150] => //Subalterno
																		Array
																		   ( //Subalternos de 03150.
																			[00174] => 00174
																			[00271] => 00271
																			[03636] => 03636
																			[03997] => 03997
																			[04686] => 04686
																			[06295] => 06295
																			[07535] => 07535
																			[08230] => 08230
																			[10731] => 10731
																			) */
	$wsubalternosN1 = implode("','", array_keys($wsubalternos)); //Subalternos en el primer nivel agrupados por ','

		if ($consultar=="enviados") //las enviadas
		{

			 $sql .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,id, Fecha_data, Nceniv,Ncecla,Nceobs, Ncenum,Ncecar, Ncecor,Nceacc,Ncetaf,
					        Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana
					   FROM ".$wbasedato."_000001
					  WHERE (Ncecpd='".$key."' or Ncecpd='".$key2."')
					    AND Ncecne='".$evento."'
					    AND Nceact='on'
					    AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , fecha_data,  '".date("Y-m-d")."' ) <=2 )  )";

			//Este union solo aplica para no conformidades
			if($evento == 'NC')
				{
				$sql .= " UNION "	;
				//Se agrega esta consulta para que muestre las no conformidades de los subalternos.
				$sql .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,id, Fecha_data, Nceniv,Ncecla,Nceobs, Ncenum,Ncecar, Ncecor,Nceacc,Ncetaf,
								Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana
						   FROM ".$wbasedato."_000001
						  WHERE Ncecpd in ('".$wsubalternosN1."')
							AND Ncecne='".$evento."'
							AND Nceact='on'
							AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , fecha_data,  '".date("Y-m-d")."' ) <=2 )  )";

				  }
			$sql .= "order by Ncenum";
			//echo $sql;
		}
		else if($consultar=="analizados") //las analizadas
		{
			 $sql .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
						    Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana, Segant
					   FROM ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
					  WHERE (Segant = '".$key."' or Segant = '".$key2."')
						AND Segest !=  '01'
						AND segeid = a.id
						AND ncecne = '".$evento."'
						AND Nceact='on'
						AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )";

		//Este union solo aplica para no conformidades
			 if($evento == 'NC')
				{
				$sql .= " UNION "	;

				//Se agrega esta consulta para que muestre las no conformidades de los subalternos.
				$sql .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
						    Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana, Segant
					   FROM ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
					  WHERE Segant in ('".$wsubalternosN1."')
						AND Segest !=  '01'
						AND segeid = a.id
						AND ncecne = '".$evento."'
						AND Nceact='on'
						AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )";

				}
			 $sql .="GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17
				   ORDER BY Ncenum";

		}
		else if($consultar=="todos") //todas: las enviadas y las analizadas
		{

				$sql .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
                               Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres, Segant
						  FROM  ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
						 WHERE  (Segant = '".$key."' or Segant = '".$key2."')
						   AND segeid = a.id
						   AND ncecne = '".$evento."'
						   AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )
						   AND Nceact='on'";

			//Este union solo aplica para no conformidades
			if($evento == 'NC')
				{
				$sql .= " UNION "	;
				//Se agrega esta consulta para que muestre las no conformidades de los subalternos.

				$sql .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
                               Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres, Segant
						  FROM  ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
						 WHERE Segant in ('".$wsubalternosN1."')
						   AND segeid = a.id
						   AND ncecne = '".$evento."'
						   AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )
						   AND Nceact='on'";

				}
				$sql .="GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17
					   ORDER BY Ncenum";

		}

		//echo $sql;
		if($res = mysql_query( $sql, $conex ))
		{
			$tabla = '';
			$num = mysql_num_rows($res);

			if ($num > 0)
			{

				$tabla .= "<table align='center' width='1150' border='0'>"; //externa
				$titulo_tb = "";
				if ($consultar =="enviados")
				{
					$titulo_tb .="<table align='center' width='100%'><tr class='encabezadotabla' align=center ><td>".$num." NOTIFICACIONES ENVIADAS </td></tr></table>";

				}
				else if ($consultar =="analizados")
				{
					$titulo_tb .="<table align='center' width='100%'><tr class='encabezadotabla' align=center ><td>".$num." NOTIFICACIONES ANALIZADAS </td></tr></table>";
				}
				else if ($consultar =="todos")
				{
					$titulo_tb .="<table align='center' width='100%'><tr class='encabezadotabla' align=center ><td>".$num." TODAS LAS NOTIFICACIONES </td></tr></table>";
				}


				$k = 1; //Variable para controlar los estilo (fila1, fila2)

				$arr_soli1 = array(); //Arreglo de codigos de jefes y subordinados de ese jefe, primer y segundo nivel de subordinacion.

				//En este while se crea un arreglo con los usuarios jefes y sus subordinados (Pedro Ortiz (05000), Juan Carlos(03150) y los subordinados de Pedro Ortiz que tengan solicitudes)
				//con estos codigos se busca quienes son los subordinados de Juan Carlos (03150) y que tengan solicitudes, estas se agruparan de acuerdo al nivel de subordinacion.

				switch( $consultar ){


						case 'enviados':

							while($row_solicitudes = mysql_fetch_array($res))
								{
								//Se verifica si ya se encuentra en el arreglo, si no esta lo agrega.
								if(!array_key_exists($row_solicitudes['Ncecpd'], $arr_soli1))
								{
									$arr_soli1[$row_solicitudes['Ncecpd']] = array();
								}

								//Aqui se forma el arreglo.
								$arr_soli1[$row_solicitudes['Ncecpd']] = $row_solicitudes['Ncecpd'];

								}

						break;

						case 'analizados':

							while($row_solicitudes = mysql_fetch_array($res))
								{
								//Se verifica si ya se encuentra en el arreglo, si no esta lo agrega.
								if(!array_key_exists($row_solicitudes['Segant'], $arr_soli1))
								{
									$arr_soli1[$row_solicitudes['Segant']] = array();
								}

								//Aqui se forma el arreglo.
								$arr_soli1[$row_solicitudes['Segant']] = $row_solicitudes['Segant'];

								}

						break;

						case 'todos':

							while($row_solicitudes = mysql_fetch_array($res))
								{
								//Se verifica si ya se encuentra en el arreglo, si no esta lo agrega.
								if(!array_key_exists($row_solicitudes['Segant'], $arr_soli1))
								{
									$arr_soli1[$row_solicitudes['Segant']] = array();
								}

								//Aqui se forma el arreglo.
								$arr_soli1[$row_solicitudes['Segant']] = $row_solicitudes['Segant'];

								}


						break;



						default:
						break;
						}

					$detalle = '';

					foreach($arr_soli1 as $key => $value)
							{

							if(array_key_exists($value,$wsubalternos))
								{

								$wsubalternosN2 = implode("','",$wsubalternos[$value]);

								//echo '|'.$key.'-'.$wsubalternosN2;

								 if (is_integer($k/2))
								   $wclass="fila1";
								else
								   $wclass="fila2";

								// echo "<br>value: ".$value;
								$queryU = "select Descripcion
											 FROM usuarios
											WHERE Codigo = '".$value."'
											and Activo = 'A'"; //SE AGREGO EL ACTIVO
								$resU = mysql_query($queryU) or die( mysql_errno()." - Error en el query $queryU - ".mysql_error() );
								$rowU = mysql_fetch_array($resU);

								/**se consulta si es coordinador para colocarle al lado del nombre**/
								$valor=strlen($value);
								if ($valor > 5)
								{
									$valor1 = substr($value,2);
								}
								else
								{
									$valor1 = $value;
								}


								$sqlcoor = "  SELECT Ajecoo
												FROM ".$datos."_000008
												WHERE Ajeucr = '".$valor1."-".$wemp_pmla1."'
												AND Ajecoo = 'on' LIMIT 1";
								$rescoor = mysql_query($sqlcoor, $conex) or die( mysql_errno()." - Error en el query $sqlcoor - ".mysql_error());
								$numcoor = mysql_num_rows($rescoor);

								if ($numcoor > 0)
								{
									$rowcoor = mysql_fetch_array($rescoor);
									$coor=" - COORDINADOR";
								}
								else
								{
									$coor="";
								}
								/**fin busqueda es coordinador**/


								$detalle .="<table align='center' width='100%'><tr class='".$wclass."' align=left ><td>
											<div style='font-size: 8pt;cursor:pointer; font-weight:bold;' onClick='detalle_solicitudes(\"solicitudes_$value\", \"$value\");'>
											<img id='imgmenos_$value' style='display:none;' src='../../images/medical/hce/menos.PNG' > <img id='imgmas_$value' src='../../images/medical/hce/mas.PNG' >&nbsp&nbsp".strtoupper(utf8_encode($rowU['Descripcion']))."".$coor."</div>";

								//Si ingresa por eventos adversos vera el listado, pero si ingresa por no conformidades los vera agrupados por los subordinados
								//que hayan solicitado.
								if($evento == 'EA')
									{
									 if(count($arr_soli1) == 1)
										{
										$display = "";
										}
									else
										{
										$display = "display:none";
										}
									}
								else
									{
									if(count($arr_soli1) == 1)
										{
										$display = "";
										}
									else
										{
										$display = "display:none";
										}
									}

								$detalle .= "<table style='$display; width: 100%;' id='solicitudes_$value'><tr><td>";
								$detalle .= "<div style='width: 100%; height: 150px;overflow:scroll;'>";

								$detalle .= "<center><table width='100%'>"; //interna
								$caso="enviadas";
								$sql_datos = ''; //Se inicia inicializa la variable para que en el ciclo no se a utilizar la consulta.

								switch( $consultar ){


									  case 'enviados':
												//Consulto de nuevo las solicitudes pero con el codigo del usuario.
												$sql_datos .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,id, Fecha_data, Nceniv,Ncecla,Nceobs, Ncenum,Ncecar, Ncecor,Nceacc,Ncetaf,
																	 Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana,Ncepro
															   FROM ".$wbasedato."_000001
															  WHERE Ncecpd = '".$value."'
																AND Ncecne='".$evento."'
																AND Nceact='on'
																AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , fecha_data,  '".date("Y-m-d")."' ) <=2 ) ) ";
																//Este union solo aplica para no conformidades
												if($evento == 'NC')
													{
													$sql_datos .= " UNION "	;
													//Se agrega esta consulta para que muestre las no conformidades de los subalternos.
													$sql_datos .= "Select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,id, Fecha_data, Nceniv,Ncecla,Nceobs, Ncenum,Ncecar, Ncecor,Nceacc,Ncetaf,
																		  Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana,Ncepro
																     FROM ".$wbasedato."_000001
																    WHERE Ncecpd in ('".$wsubalternosN2."')
																	  AND Ncecne='".$evento."'
																	  AND Nceact='on'
																	  AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , fecha_data,  '".date("Y-m-d")."' ) <=2 )  )";

													  }

													$sql_datos .= " order by Ncenum";

													break;

									   case 'analizados':

												 $sql_datos .="SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
																	Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana,Ncepro
															   FROM ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
															  WHERE Segant = '".$value."'
																AND Segest !=  '01'
																AND segeid = a.id
																AND ncecne = '".$evento."'
																AND Nceact='on'
																AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )";

													//Este union solo aplica para no conformidades
														if($evento == 'NC')
															{
															$sql_datos .= " UNION "	;
															//Se agrega esta consulta para que muestre las no conformidades de los subalternos.

															$sql_datos .= " SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
																			Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana,Ncepro
																	   FROM ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
																	  WHERE Segant in ('".$wsubalternosN1."')
																		AND Segest !=  '01'
																		AND segeid = a.id
																		AND ncecne = '".$evento."'
																		AND Nceact='on'
																		AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )";
															}
														$sql_datos .="GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17
															   ORDER BY Ncenum";

															   break;
									  case 'todos':

													$sql_datos .= "  SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
																		   Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres
																	  FROM  ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
																	 WHERE Segant = '".$value."'
																	   AND segeid = a.id
																	   AND ncecne = '".$evento."'
																	   AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )
																	   AND Nceact='on'";

													//Este union solo aplica para no conformidades
													if($evento == 'NC')
														{
														$sql_datos .= " UNION "	;
														//Se agrega esta consulta para que muestre las no conformidades de los subalternos.

														$sql_datos .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
																	   Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres
																  FROM  ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
																 WHERE Segant in ('".$wsubalternosN2."')
																   AND segeid = a.id
																   AND ncecne = '".$evento."'
																   AND ( ( Nceest != '06' ) or ( (Nceest = '06' ) AND TIMESTAMPDIFF( MONTH , a.fecha_data,  '".date("Y-m-d")."' ) <=2 )  )
																   AND Nceact='on'";
														}
														$sql_datos .="GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17
															   ORDER BY Ncenum";
															   //se cambio en la consulta $wsubalternosN1 por $wsubalternosN2 porque a cada persona le mostraba lo de todos
															 break;
									  default: break;
									}


											$res_datos = mysql_query( $sql_datos, $conex );

											//Recorro la respuesta de nuevo pero con el filtro del codigo del usuario.
											for($i = 0; $rows_datos = mysql_fetch_array($res_datos,MYSQL_ASSOC); $i++) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
												{
													//nombre del centro de costo que detecto
													$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows_datos['Nceccd'],$wemp_pmla));

													//nombre de la persona que detecto
													$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows_datos['Ncecpd'],$wemp_pmla1,$rows_datos['Nceccd']) );
													$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

													//nombre del centro de costo que genero
													$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows_datos['Nceccg'],$wemp_pmla));

													if ($evento=='NC')
													{
														$opciones = consultarCausasNC($wbasedato,$evento,$rows_datos['id']);
													}
													else
													{
														$causa = consultarCausasEA($wbasedato,$rows_datos['id']);
													}

													//se trae el estado
													$rows5 = mysql_fetch_array(consultarEstadoEvento($rows_datos['Nceest'])  );

													//$j=$i+1;
													if( $i%2 == 0 )
													{
														$class = "class='fila1'";
													}
													else
													{
														$class = "class='fila2'";
													}

														if( $i == 0)
														{
															$detalle .= "<tr class='encabezadotabla' align=center>";
															$detalle .= "<td align='center' style=''>Numero</td>";
															$detalle .= "<td align='center' style=''>Fecha Ingreso</td>";
															$detalle .= "<td align='center' style=''>Unidad que detecto</td>";
															$detalle .= "<td align='center' style=''>Unidad que genero</td>";
															$detalle .= "<td align='center' style=''>Estado</td>";
															$detalle .= "</tr>";
														}

															/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
															para la comilla simple se coloca el ascii porque presento error*/
															foreach( $rows_datos as $key => $value)
															{
																$rows_datos[ $key ] = str_replace("\\","\\\\",$rows_datos[ $key ] ); //se escapa el \ nceres - nceccd
																$rows_datos[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows_datos[ $key ] ));
															}

															$wtexto_proceso = ($array_procesos_cco[$rows_datos['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows_datos['Ncepro']]['Prodes']) : "";

															$detalle .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows_datos['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows_datos['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
																."\"".$rows2['Ideeml']."\",\"".$rows_datos['Ncefed']."\",\"".$rows_datos['Ncehod']."\",\"".$rows_datos['Ncefue']."\",\"".$rows_datos['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
																."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows_datos['Ncedes'] ) ) )."\",\"".$rows_datos['Nceest']."\""
																.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows_datos['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows_datos['Ncenaf'])))."\",\"".$rows_datos['Nceeaf']."\",\"".$rows_datos['Nceiaf']."\",\"".$rows_datos['Ncehca']."\""
																.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows_datos['Nceaci']) ) )."\",\"".$rows_datos['Ncecls']."\",\"".$rows_datos['Ncetip']."\",\"".$rows_datos['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows_datos['Ncesev'])))."\""
																.",\"".$rows_datos['Ncepre']."\",\"".$rows_datos['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows_datos['Nceana']))."\",\"".$rows_datos['Ncecne']."\",\"".$rows_datos['Ncecon']."\""
																.",\"".$rows_datos['id']."\",\"".utf8_encode($rows_datos['Nceres'])."\",\"".$rows_datos['Nceniv']."\",\"".$rows_datos['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows_datos['Nceobs'] ) ) )."\""
																.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows_datos['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows_datos['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows_datos['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\""
																.",\"\",\"".$array_procesos_cco[$rows_datos['Ncepro']]['Prodes']."\")'>";

															$detalle .= "<td align='center' style=''>".$rows_datos['Ncenum']."</td>";
															$detalle .= "<td align='center' style=''>".$rows_datos['Fecha_data']."</td>";
															$detalle .= "<td align='center' style=''>".utf8_encode($rows_datos['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
															$detalle .= "<td align='center' style=''>".utf8_encode($rows_datos['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
															$detalle .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
															//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows_datos['Nceobs'] )." json_encode( $rows_datos['Ncedes'] )
															$detalle .= "</tr>";
												}//for

										$detalle .="</table></center></div>"; //tabla interna
										$detalle .="</td></tr></table>"; //tabla externa

								$k++;
								$detalle .= "</td></tr></table>";
								$i++;
								}

							}

					$detalle .= "</td></tr></table>";
					$tabla .="<tr><td>".$titulo_tb.$detalle;


				}//$num
				else
				{
					if ($consultar=="enviados")
					{
						 $tabla .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES ENVIADAS</th></table>";
					}
					else if ($consultar=="analizados")
					{
						$tabla .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES ANALIZADAS</th></table>";
					}
					else if ($consultar=="todos")
					{
						$tabla .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES</th></table>";
					}

				}



				if ($tipo=="menu")
				{
					if($wtipo_even =='NC')
					{

						$data['html'] .= "
							<table align='center' width='100%'>
								<tr><td class='sel_enviadas_color' align='center'>
								Enviadas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_enviados' value='enviadas' checked onclick='nc_enviadas(\"".$datos."\",\"".$key2."\",\"".$wemp_pmla1."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"enviados\",\"div_tabla\")'>&nbsp
								Analizadas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_analizados' value='analizadas' onclick='nc_enviadas(\"".$datos."\",\"".$key2."\",\"".$wemp_pmla1."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"analizados\",\"div_tabla\")'>&nbsp
								Todas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_todos' value='todos' onclick='nc_enviadas(\"".$datos."\",\"".$key2."\",\"".$wemp_pmla1."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"todos\",\"div_tabla\")'>
								</td></tr>
							</table>
							<INPUT type='hidden' name='niv_id' id='niv_id' value=''>
							<INPUT type='hidden' name='est_id' id='est_id' value=''>
							<INPUT type='hidden' name='estadoA' id='estadoA' value=''>
							<div id='div_tabla'>
							".$tabla."
							</div>";

					}
					else
					{

							//--

							$sql_rol = "  SELECT Ajecoo, Ajeucr
											FROM ".$datos."_000008,".$datos."_000013
										   WHERE Ajeucr='".$key2."-".$wemp_pmla1."'
											 AND Ideuse=Ajeucr
											 AND Ideest = 'on'
										GROUP BY Ajeucr ";


							 $res_rol = mysql_query( $sql_rol ,$conex) or die( mysql_errno()." - Error en el query $sql_rol - ".mysql_error() );
							 $num_rol = mysql_num_rows($res_rol);
							 $row_rol = mysql_fetch_assoc($res_rol);
							 $data['sql'] =$sql_rol;

							//Coordinador
							if($row_rol['Ajecoo'] == 'on'){
								$tipo_usu = 1;
							}else{
								//Jefe
								//Aqui varifica que no es coordinador pero maneja empleados.
								if($row_rol['Ajecoo'] != 'on' and $num_rol > 0){
									$tipo_usu = 2;
								}else{
									//Empleado.
									 //Verifica si tiene registro en talento humani, si no tiene muestra un mensaje de alerta.
									 $sql_carac = " SELECT id
													FROM ".$datos."_000013
												   WHERE Ideuse='".$key2."-".$wemp_pmla1."'
													 AND Ideest = 'on'";
									 $res_carac = mysql_query( $sql_carac ,$conex) or die( mysql_errno()." - Error en el query $sql_carac - ".mysql_error() );
									 $num_carac = mysql_num_rows($res_carac);

									if($num_carac > 0)
									{

										$tipo_usu = 3;

										//Se agrega esta validacion para que los empleados que esten registrados para autorizar
										//nc de aud. interna puedan ver las nc recibidas y aprobarlas o rechazarlas. (Jonatan Lopez 14 Agosto 2013)
										if(in_array($key2, $datos_usuarios_aud_int))
										{
											$wautoriza_nc_aud_inter =  "td_nc_recibidas";
										}

									}else
									{
										$tipo_usu = 3;
									}
								}
							}
							//--

								$data['html'] .= "<table align='center' width='100%'>
								<tr><td class='sel_enviadas_color' align='center'>
								Enviadas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_enviados' value='enviadas' checked onclick='nc_enviadas(\"".$datos."\",\"".$key2."\",\"".$wemp_pmla1."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"enviados\",\"div_tabla\")'>&nbsp";

							if($tipo_usu !=3)
							{
							  $data['html'] .= "Analizadas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_analizados' value='analizadas' onclick='nc_enviadas(\"".$datos."\",\"".$key2."\",\"".$wemp_pmla1."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"analizados\",\"div_tabla\")'>&nbsp";
							}
							$data['html'] .= "Todas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_todos' value='todos' onclick='nc_enviadas(\"".$datos."\",\"".$key2."\",\"".$wemp_pmla1."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"todos\",\"div_tabla\")'>
								</td></tr>
							</table>
							<INPUT type='hidden' name='niv_id' id='niv_id' value=''>
							<INPUT type='hidden' name='est_id' id='est_id' value=''>
							<INPUT type='hidden' name='estadoA' id='estadoA' value=''>
							<div id='div_tabla'>
							".$tabla."
							</div>";





					}

				}
				else
				{
					$data['html'] .=$tabla;
				}

		}
		else
		{
			$data['mensaje'] = "No se pudo realizar la consulta.";
			$data['error'] = 1;
		}


	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'lista_recibidos')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	$datos_usuarios_aud_int = explode("-", $user_aut_aud_inter_nc );


	if ($evento=="NC")
	{

				//Se restringue esta consulta para que los coordinadores no puedan ver no conformidades de auditoria interna, solo la pueden ver usuarios
				//autorizados, estos usuarios estan en la tabla root_000051 con el valor AutAudInterna (Jonatan Lopez 14 Agosto 2013).
				//se cambia la consulta para consultar si el jefe tiene no conformidades para aprobar de sus subalternos
			   $sql .= "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncenum, Ncepro, Nceaca
						  FROM ".$wbasedato."_000001
						 WHERE SUBSTRING(Ncecpd, -5,5) IN (Select SUBSTRING( Ajeuco, 1, 5 ) as Ajeuco
							   FROM ".$datos."_000008,".$datos."_000013
							  WHERE Ajeucr='".$key2."-".$wemp_pmla1."'
								AND Ideuse=Ajeucr
								AND Ideest='on'
								AND Forest='on')
						   AND Nceest='01'
						   AND Nceniv='1'
						   AND Nceact='on'
						   AND Ncecne='".$evento."'
						   AND Ncefue != 'Auditoria_Interna'";

				//Aqui verifica si el usuario que esta activo en el sistema puede ver las no conformidades que tiene marcada la opcion de
				//auditoria interna, estos usuarios estan en la tabla root_000051 con el valor AutAudInterna (Jonatan Lopez 14 Agosto 2013).
				if(in_array($key, $datos_usuarios_aud_int) or in_array($key2, $datos_usuarios_aud_int))
					{

					$sql .=	" UNION
							Select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncenum, Ncepro, Nceaca
							  FROM ".$wbasedato."_000001
							 WHERE Nceest='01'
							   AND Nceniv='1'
							   AND Nceact='on'
							   AND Ncefue = 'Auditoria_Interna'
							   AND Ncecne='".$evento."'";
					}

				$sql .="order by Ncenum";  //jefe inmediato persona que creo el evento
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);


	}
	else
	{
		$sql = "SELECT Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
								   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
								   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs,Ncenum, Ncepro, Nceaca
				   FROM ".$wbasedato."_000001
				   WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
				   AND Nceest='01'
				   AND Nceact='on'
				   AND Ncecne='".$evento."'
			  order by Ncenum";  //le llega a magda cuANDo se crea
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res ); //(Ncecon like '%-".$key2."-%' OR Ncecon LIKE  '%".$key2."%') AND

	}

	if ($num > 0)
	{
		$rows_est = mysql_fetch_array(consultarEstadoEvento($rows_datos['Nceest'])  );

		$data['html'] = "";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>".$num." PENDIENTES POR APROBAR</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><font size='2.5'>Buscar:</font><input id='id_search' type='text' autocomplete='off' value='' onkeypress='return pulsar(event);' ></center>";
		$data['html'] .= "<center><table width='100%' id='registros'>"; //interna

		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));

			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			if ($evento=="EA")
			{
				$causa = consultarCausasEA($wbasedato,$rows['Id']);
			}
			else
			{
				$causa="";
			}

			$caso="recibidos";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1 find'";
			}
			else
			{
				$class = "class='fila2 find'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<thead>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</thead>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode( $rows['Ncenaf'] ))) ."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"\",".json_encode( $causa ).",\"".$caso."\""
					.",\"\",\"".$array_procesos_cco[$rows['Ncepro']]['Prodes']."\")'>";

					$wtexto_proceso = ($array_procesos_cco[$rows['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows['Ncepro']]['Prodes']) : "";

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";


	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'lista_recibidos_u_genero')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	/*Se agrega a las consultas para buscar el codigo del coordinador de la unidad que genero porque ya no se guarda*/
	if ($evento=="NC") //nceccg
	{

			 $sql="Select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,a.Id, a.Fecha_data, Nceniv,Ncecla,Nceobs,Ncenum,
					Ajeucr, Ncepro, Nceaca
				FROM ".$wbasedato."_000001 a, ".$datos."_000008 b ,".$datos."_000013 c
			   WHERE Nceest='02'
				 AND (Nceniv='2' or Nceniv='4')
				 AND Nceact='on'
				 AND Ncecne='".$evento."'
				 AND FIND_IN_SET( Nceccg,Ajeccc ) > 0
				 AND Ideuse=Ajeucr
				 AND Forest='on'
				 AND Ideest='on'
				 AND Ajecoo='on'
				 AND SUBSTRING(Ajeucr,1,5)='".$key2."'
				group by Ajeucr,Ncenum
                order by Ncenum";  //coordinador area que genero

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows($res); //FIND_IN_SET( '".$key2."',Ncecon ) > 0

	}
	else
	{

						$sql = "Select a.Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, a.Id, Nceres, Nceniv, Nceobs,Ncenum,
									   Ajeucr, Ncepro, Nceaca
							  FROM ".$wbasedato."_000001 a, ".$datos."_000008 b,".$datos."_000013 c
							 WHERE Nceest='02'
							   AND Nceniv='3'
							   AND Nceact='on'
							   AND Ncecne='".$evento."'
							   AND Ideuse=Ajeucr
								AND Forest='on'
								AND Ideest='on'
								AND Ajecoo='on'
								AND FIND_IN_SET( Nceccg,Ajeccc ) > 0
								AND SUBSTRING(Ajeucr,1,5)='".$key2."'
						  group by Ajeucr,Ncenum
						  order by Ncenum";  //le llega al coordinador de la unidad que genero
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					$num = mysql_num_rows( $res );
					if ($num==0)
					{

						$sql = "Select a.Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, a.Id, Nceres, Nceniv, Nceobs,Ncenum,
									   Ajeucr, Ncepro, Nceaca
								  FROM ".$wbasedato."_000001 a, ".$datos."_000008 b,".$datos."_000013 c
								 WHERE Nceest='02'
								   AND Nceniv='5'
								   AND Nceact='on'
								   AND Ncecne='".$evento."'
								   AND Ideuse=Ajeucr
									AND Forest='on'
									AND Ideest='on'
									AND Ajecoo='on'
									AND FIND_IN_SET( Nceccg,Ajeccc ) > 0
									AND SUBSTRING(Ajeucr,1,5)='".$key2."'
							  group by Ajeucr,Ncenum
						      order by Ncenum";  //le llega al coordinador de la unidad que genero segunda
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$num = mysql_num_rows( $res );

					}

	}


	if ($num > 0)
	{

		$data['html'] = "";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>".$num." NOTIFICACIONES RECIBIDAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><font size='2.5'>Buscar:</font><input id='id_search' type='text' autocomplete='off' value='' onkeypress='return pulsar(event);' ></center>";
		$data['html'] .="<center><table width='100%' id='registros'>"; //interna

		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));


			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			if ($evento=="EA")
			{

				$causa = consultarCausasEA($wbasedato,$rows['Id']);
			}
			else
			{
				$causa="";
			}
			$caso="recibidos_u_genero";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1 find'";
			}
			else
			{
				$class = "class='fila2 find'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<thead>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</thead>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd cconom
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}


				$wtexto_proceso = ($array_procesos_cco[$rows['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows['Ncepro']]['Prodes']) : "";

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncenaf'])))."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"\",\"\",".json_encode( $causa ).",\"".$caso."\""
					.",\"\",\"".$array_procesos_cco[$rows['Ncepro']]['Prodes']."\")'>";

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";


	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'lista_recibidos_u_control')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	// /*Se hace la parte de consultar si el usuario que ingreso tiene rol y cual es*/
	$q="select Rnccod,Roldes
			FROM ".$wbasedato."_000007,".$wbasedato."_000004
			WHERE Rnctip ='".$evento."'
			AND Rncest ='on'
			AND Rnccod = Rolcod
			AND Rolest ='on'
			AND Roltip =Rnctip
			AND Roldes = '".$key."'
			AND (Rnccme='on' OR Rnccin='on' OR Rnccni='on' )";
	//$data['html'] .= "---".$q."--";
			$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );

			for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
			{
				if( !isset( $ARR2[ $rows1['Rnccod'] ] ) )
				{
					$usu=",".$rows1['Rnccod'];
					$ARR2[ $rows1['Rnccod'] ] = 1;
				}

				if($i == 0)
				{
					$usu1.=$rows1['Roldes']; //Ncecag ccog
				}
				else
				{
					$usu1.=",".$rows1['Roldes']; //Ncecag ccog
				}

			}
			if (substr($usu, 0, 1) == ",")
			{
				$usu = substr( $usu, 1 ); //para quitar la , del principio
			}

	// /*Fin de la parte de consultar si el usuario que ingreso tiene rol y cual es*/



	if($evento=="EA")
	{
		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
								   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
								   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncenum, Nceaca
						FROM ".$wbasedato."_000001
						WHERE FIND_IN_SET( '".$usu."',Nceuvi ) > 0
						AND Nceest='02'
						AND Nceniv='2'
						AND Nceact='on'
						AND Ncecne='".$evento."'
						order by Ncenum";  //le llega a invecla/santiago/magda/alejANDro
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		//$data['html'] .= "---".$sql."--";

	}

	if ($num > 0)
	{

		$data['html'] .= "";
		$data['html'] .= "<div id='recididas' class='class_div' style='".(($num>10) ? 'height: 500px;overflow:scroll;' : '')."'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>".$num." NOTIFICACIONES RECIBIDAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><font size='2.5'>Buscar:</font><input id='id_search' type='text' autocomplete='off' value='' onkeypress='return pulsar(event);' ></center>";
		$data['html'] .="<center><table width='100%' id='registros'>"; //interna

		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));

			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			$causa = consultarCausasEA($wbasedato,$rows['Id']);

			$caso="recibidos_u_control";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1 find'";
			}
			else
			{
				$class = "class='fila2 find'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<head>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</head>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}

				$wtexto_proceso = ($array_procesos_cco[$rows['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows['Ncepro']]['Prodes']) : "";

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncenaf'])))."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"\",\"\",".json_encode( $causa ).",\"".$caso."\""
					.",\"\",\"".$array_procesos_cco[$rows['Ncepro']]['Prodes']."\")'>";

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS</th></table>";
		}

		$data['html'] .="</table></center></div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";


	echo json_encode($data);
	return;
}


if (isset($accion) AND $accion == 'mostrar_relacionados'){

	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	//Consulto los registros que se encuentren relacionados a alguna NC o EA.
	$sql_rel = "SELECT Regide, Regire
				  FROM ".$wbasedato."_000001, ".$wbasedato."_000012
			     WHERE Regide = '".$id."'
				   AND Regest = 'on'
				   AND Regire = ".$wbasedato."_000001.id
				   AND Ncecne = '".$evento."'
			  ORDER BY Ncefed DESC";
	$res_rel = mysql_query( $sql_rel, $conex ) or die( mysql_errno()." - Error en el query $sql_rel - ".mysql_error() );
	$num_rel = mysql_num_rows( $res_rel );

	$array_relacionados_id = array();

	//Creo un arreglo con esos identificadores.
	while($row_rel = mysql_fetch_array($res_rel))
		{
		//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row_rel['Regire'], $array_relacionados_id))
			{
			$array_relacionados_id[$row_rel['Regire']] = $row_rel['Regire'];
			}

		}

	$dato = implode(",", $array_relacionados_id);

	if($num_rel > 0){

		foreach($array_relacionados_id as $key => $value){

		//Funcion que estructura las tablas con cada uno de los registros relacionados.
		$info = mostrar_datos_relacionado($wbasedato, $key);

		$data['html'] .= $info." - ";

		}

	}else{

	$data['html'] = "";

	}

	echo json_encode($data);
	return;

}



//Funcion que permite retornar las nc o ea que se encuentren en correccion para relacionarlas con otra nc o ea.
if (isset($accion) AND $accion == 'registros_en_correccion'){

	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	//========= Consulto los centros de costos de la tabla costosyp_000005, para listarlos en el div oculto del nuevo cco que genero, este se mostrara cuando necesiten cambiarlo ====
	if ($cco == 'costosyp_000005'){
	   		$query_cco1 = "SELECT Ccocod, Cconom
		        FROM ".$cco."
		        WHERE Ccoest = 'on'
		          AND Ccoemp = '01'
			    ORDER BY Cconom ";
	}
	else
	{
			$query_cco1 = "SELECT Ccocod, Cconom
		        FROM ".$cco."
		        WHERE Ccoest = 'on'
			    ORDER BY Cconom ";
	}


	$res_cco1 = mysql_query( $query_cco1 ) or die( mysql_errno()." - Error en el query $query_cco1 - ".mysql_error() );

	$arr_cco1 = array();
	while($row_cco1 = mysql_fetch_array($res_cco1))
	{
		//Se verifica si el cco ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row_cco1['Ccocod'], $arr_cco1))
		{
			$arr_cco[$row_cco1['Ccocod']] = array();
		}

		$row_cco1['Cconom'] = str_replace( $caracteres, $caracteres2, $row_cco1['Cconom'] );
		$row_cco1['Cconom'] = utf8_decode( $row_cco1['Cconom'] );
		//Aqui se forma el arreglo, con clave el servicio => codigo del cco y su INFORMACIÓN.
		$arr_cco1[$row_cco1['Ccocod']] = trim($row_cco1['Ccocod'])."-".trim($row_cco1['Cconom']);

	}

	//Consulto los registros que se encuentren relacionados a alguna NC o EA.
	$sql_rel = "SELECT Regide, Regire
				      FROM ".$wbasedato."_000012
				     WHERE Regest = 'on'";
	$res_rel = mysql_query( $sql_rel, $conex ) or die( mysql_errno()." - Error en el query $sql_rel - ".mysql_error() );
	$num_rel = mysql_num_rows( $res_rel );

	$array_relacionados = array();

	//Creo un arreglo con esos identificadores.
	while($row_rel = mysql_fetch_array($res_rel))
		{
		//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row_rel['Regire'], $array_relacionados))
			{
			$array_relacionados[$row_rel['Regire']] = $row_rel['Regire'];
			}

		}

	//Uno los identificadores por ',', para luego utilizar el resultado en la consulta con un NOT IN.
	$id_relacionados = implode("','",$array_relacionados);

	if ($evento=="NC")
	{

		$sql="SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecor,Nceacc, Ncenum, Ncepro, Nceaca
				FROM ".$wbasedato."_000001
			   WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
				 AND Nceest='04'
				 AND Nceniv='5'
				 AND Nceact='on'
				 AND Ncecne='".$evento."'
				 AND id NOT IN ('".$id_relacionados."')
		    ORDER BY Ncenum";  //alejANDro correccion ya se hizo la correccion por el coordinador area que genero
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows($res);


		/********************************************************************************************/

	}
	else
	{

		$sql = "SELECT Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
				   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
				   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncecor, Nceacc,Ncenum, Ncepro, Nceaca
				 FROM ".$wbasedato."_000001
				WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
				  AND Nceest='04'
				  AND Nceniv='6'
				  AND Nceact='on'
				  AND Ncecne='".$evento."'
				  AND id NOT IN ('".$id_relacionados."')
		     ORDER BY Ncenum";  //le llega a magda por correccion
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );


	}

	if($num > 0){

		while($row = mysql_fetch_array($res)){

		$data['html'] .= "<option value='".$row['Id']."'>".$row['Ncenum']." (".utf8_encode($arr_cco1[$row['Nceccd']])." | ".utf8_encode($arr_cco1[$row['Nceccg']])."</option>";

		}
	}else{

	$data['html'] = "<option value=''></option>";

	}

	echo json_encode($data);
	return;

}


if (isset($accion) AND $accion == 'lista_recibidos_correccion')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	//$opciones = array();

	//Consulto los registros que se encuentren relacionados a alguna NC o EA.
	$sql_rel = "SELECT Regide, Regire
				      FROM ".$wbasedato."_000012
				     WHERE Regest = 'on'";  //le llega a magda por correccion
	$res_rel = mysql_query( $sql_rel, $conex ) or die( mysql_errno()." - Error en el query $sql_rel - ".mysql_error() );
	$num_rel = mysql_num_rows( $res_rel );

	$array_relacionados = array();

	//Creo un arreglo con esos identificadores.
	while($row_rel = mysql_fetch_array($res_rel))
		{
		//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row_rel['Regire'], $array_relacionados))
			{
			$array_relacionados[$row_rel['Regire']] = $row_rel['Regire'];
			}

		}

	//Uno los identificadores por ',', para luego utilizar el resultado en la consulta con un NOT IN.
	$id_relacionados = implode("','",$array_relacionados);


	if ($evento=="NC")
	{

		$sql="SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecor,Nceacc, Ncenum, Ncepro, Nceaca
				FROM ".$wbasedato."_000001
			   WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
				 AND Nceest='04'
				 AND Nceniv='5'
				 AND Nceact='on'
				 AND Ncecne='".$evento."'
				 AND Id NOT IN ('".$id_relacionados."')
		    ORDER BY Ncenum";  //alejANDro correccion ya se hizo la correccion por el coordinador area que genero
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows($res);


		/********************************************************************************************/

	}
	else
	{

		$sql = "SELECT 	Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
						Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
						Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncecor, Nceacc,Ncenum, Ncepro, Nceaca
				FROM ".$wbasedato."_000001
				WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
				AND Nceest='04'
				AND Nceniv='6'
				AND Nceact='on'
				AND Ncecne='".$evento."'
			    AND Id NOT IN ('".$id_relacionados."')
		   ORDER BY Ncenum";  //le llega a magda por correccion
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );


	}

	if ($num > 0)
	{

		$data['html'] = "";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>".$num." NOTIFICACIONES RECIBIDAS EN CORRECCION</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><font size='2.5'>Buscar:</font><input id='id_search' type='text' autocomplete='off' value='' onkeypress='return pulsar(event);' ></center>";
		$data['html'] .="<center><table width='100%' id='registros'>"; //interna

		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));

			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			if ($evento=='NC')
			{

				$opciones = consultarCausasNC($wbasedato,$evento,$rows['Id']);
			}
			else
			{

				$causa = consultarCausasEA($wbasedato,$rows['Id']);
			}
			$caso="recibidos_co";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1 find'";
			}
			else
			{
				$class = "class='fila2 find'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<thead>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
					$data['html'] .= "<td align='center' style=''>Numero</td>";
					$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
					$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
					$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
					$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</thead>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}

				$wtexto_proceso = ($array_procesos_cco[$rows['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows['Ncepro']]['Prodes']) : "";

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncenaf'])))."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\""
					.",\"\",\"".$array_procesos_cco[$rows['Ncepro']]['Prodes']."\")'>";

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS EN CORRECCION</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";

	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'lista_recibidos_accion')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	if ($evento=="NC")
	{
		$sql="SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecor,Nceacc, Ncenum, Nceaca
			    FROM ".$wbasedato."_000001
			   WHERE FIND_IN_SET( '".$usu."',Nceuvi ) > 0
			     AND Nceest='05'
			     AND Nceniv='6'
			     AND Nceact='on'
			ORDER BY Ncenum";  //angela acciones si la nc tiene acciones
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num= mysql_num_rows($res);

	}
	else
	{
		$sql = "SELECT Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
				       Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
				       Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs,Nceobs,Nceacc,Ncecor, Ncenum, Nceaca
				FROM ".$wbasedato."_000001
				WHERE FIND_IN_SET( '".$usu."',Nceuvi ) > 0
				AND Nceest='05'
				AND Nceniv='7'
				AND Nceact='on'
		   ORDER BY Ncenum";  //le llega magda acciones
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

	}

	if ($num > 0)
	{

		$data['html'] = "";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>".$num." NOTIFICACIONES RECIBIDAS EN ACCION</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><font size='2.5'>Buscar:</font><input id='id_search' type='text' value='' autocomplete='off' onkeypress='return pulsar(event);' ><center>";
		$data['html'] .='<a href="../reportes/rep_acciones_correctivas.php?wemp_pmla='.$wemp_pmla1.'" target="_blank"><font size="2.5">Reporte acciones correctivas</font></a>';
		//$data['html'] .="<p align=right style='display:inline;'>Enlace</p>";
		$data['html'] .= "<center><table width='100%' id='registros' >"; //interna


		//Consulto los registros que se encuentren relacionados a alguna NC o EA.
		$sql_rel = "SELECT Regide, Regire
				      FROM ".$wbasedato."_000012
				     WHERE Regest = 'on'";  //le llega a magda por correccion
		$res_rel = mysql_query( $sql_rel, $conex ) or die( mysql_errno()." - Error en el query $sql_rel - ".mysql_error() );
		$num_rel = mysql_num_rows( $res_rel );

		$array_relacionados = array();

		//Creo un arreglo con esos identificadores.
		while($row_rel = mysql_fetch_array($res_rel))
			{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_rel['Regide'], $array_relacionados))
				{
				$array_relacionados[$row_rel['Regide']] = $row_rel['Regire'];
				}

			}


		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));

			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			if ($evento=='NC')
			{

				$opciones = consultarCausasNC($wbasedato,$evento,$rows['Id']);
			}
			else
			{

				$causa = consultarCausasEA($wbasedato,$rows['Id']);
			}
			$caso="recibidos_ac";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1 find'";
			}
			else
			{
				$class = "class='fila2 find'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<thead>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
						$data['html'] .= "<td align='center' style=''>Ver Relacionados</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</thead>";
				}

				$wcon_relacionados = "";

				//Verifica si el registro tiene otros registros asociados.
				if(array_key_exists($rows['Id'], $array_relacionados)){

					$wcon_relacionados = "<img border='0' src='../../images/medical/root/vp.png'>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}

				$wtexto_proceso = ($array_procesos_cco[$rows_datos['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows_datos['Ncepro']]['Prodes']) : "";

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncenaf'])))."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\""
					.",\"\",\"".$array_procesos_cco[$rows_datos['Ncepro']]['Prodes']."\")'>"; //se agrega el campo Nceacc para que no se pierda cuando se le vuelva a colocar en estado analizada

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					$data['html'] .= "<td align='center' style=''>".$wcon_relacionados."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS EN ACCION</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";

	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'lista_recibidas_rechazadas')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	if ($evento=="NC")
	{

		$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecar, Ncenum, Ncepro, Nceaca
				FROM ".$wbasedato."_000001
				WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
				AND Nceest='03'
				AND Nceniv != '2'
				AND Nceniv != '9'
				AND Nceact='on'
				AND Ncecne='".$evento."'
				order by Ncenum";  //coordinador area que genero rechaza
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);
					//se coloco el nivel 2 o 9 porque es donde muere el proceso

	}
	else
	{

		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncenum, Ncecar, Ncepro, Nceaca
								FROM ".$wbasedato."_000001
								WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
								AND Nceest='03'
								AND Nceniv != '2'
								AND Nceniv != '9'
								AND Nceact='on'
								AND Ncecne='".$evento."'
								order by Ncenum";  //le llega a magda porque el coordinador de la unidad rechaza
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		 //se coloco el nivel 2 y nivel 9
	}

	if ($num > 0)
	{



		$data['html'] = "";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>".$num." NOTIFICACIONES RECIBIDAS RECHAZADAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><font size='2.5'>Buscar:</font><input id='id_search' type='text' value='' autocomplete='off' onkeypress='return pulsar(event);' ><center>";
		$data['html'] .= "<center><table width='100%' id='registros' >"; //interna

		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));

			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			$caso="recibidas_re";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			if ($evento=="EA")
			{
				$causa = consultarCausasEA($wbasedato,$rows['Id']);
			}
			else
			{
				$causa="";
			}

			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1 find'";
			}
			else
			{
				$class = "class='fila2 find'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<thead>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</thead>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}

				$wtexto_proceso = ($array_procesos_cco[$rows_datos['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows_datos['Ncepro']]['Prodes']) : "";

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncenaf'])))."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"\",\"\",".json_encode( $causa ).",\"".$caso."\""
					.",\"\",\"".$array_procesos_cco[$rows['Ncepro']]['Prodes']."\")'>";

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECHAZADAS</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";

	echo json_encode($data);
	return;
}


if (isset($accion) AND $accion == 'lista_cerradas')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	if ($evento=="NC")
	{

		$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,a.Id, a.Fecha_data, Nceniv,Ncecla,Nceobs,Ncecar, Ncenum, Nceacc, Ncecor, Nceaca
				FROM ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
				WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
				AND Nceest='06'
				AND Nceniv='7'
				AND Ncecne = '".$evento."'
				AND TIMESTAMPDIFF( MONTH , b.fecha_data,  '".date("Y-m-d")."' ) <=2
				AND Segest = Nceest
				AND Segeid = a.id
				AND Nceact='on'
				order by Ncenum";  //mostrar las cerradas
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);


	}
	else
	{

		$sql = "select a.Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev,
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, a.Id, Nceres, Nceniv, Nceobs, Ncenum, Nceacc, Ncecor, Nceaca
								FROM ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
								WHERE FIND_IN_SET( '".$usu."',Ncecon ) > 0
								AND Nceest='06'
								AND Nceniv='8'
								AND Ncecne = '".$evento."'
								AND TIMESTAMPDIFF( MONTH , b.fecha_data,  '".date("Y-m-d")."' ) <=2
								AND Segest = Nceest
								AND Segeid = a.id
								AND Nceact='on'
								order by Ncenum";  //se muestran las cerradas
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

	}

	if ($num > 0)
	{

		$data['html'] = "";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>".$num." NOTIFICACIONES CERRADAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><style font-size=3><font size='2.5'>Buscar:</font></style><input id='id_search' type='text' value='' autocomplete='off' onkeypress='return pulsar(event);' ><center>";
		$data['html'] .= "<center><table width='100%' id='registros' >"; //interna

		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));

			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			$caso="cerradas";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			if ($evento=='NC')
			{

				$opciones = consultarCausasNC($wbasedato,$evento,$rows['Id']);
			}
			else
			{

				 $causa = consultarCausasEA($wbasedato,$rows['Id']);

			}
			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1 find'";
			}
			else
			{
				$class = "class='fila2 find'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<thead>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</thead>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}

				$wtexto_proceso = ($array_procesos_cco[$rows_datos['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows_datos['Ncepro']]['Prodes']) : "";

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncenaf'])))."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\""
					.",\"\",\"".$array_procesos_cco[$rows_datos['Ncepro']]['Prodes']."\")'>";

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style=''>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES CERRADAS</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";

	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'lista_todas')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	if($claseEvento=='generado')
	{
		$campo="Nceccg";
	}
	else
	{
		$campo="Nceccd";
	}


		  $wfiltro_fecha = ($wfec_i != '' and $wfec_f != '') ? $filtro_fecha = "AND Fecha_data BETWEEN '".$wfec_i."' AND '".$wfec_f."'" : "";

		  $sql="SELECT 	Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecar, Ncenum, Nceacc, Ncecor, Ncecne,
						Nceaci,Nceana,Ncefue,Ncetaf,Ncenaf,Nceeaf, Nceiaf,Ncehca,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceres, Nceaca
				  FROM ".$wbasedato."_000001
				 WHERE ".$campo." like '".$centroCosto."'
				   AND Nceccg like '".$centroCostoG."'
				   AND Nceest like '".$estadoEvento."'
				   AND Ncecne like '".$tipoEvento."'
				   AND Ncefue like '".$fuentes."'
				   AND Nceact='on'
				   $wfiltro_fecha
			  ORDER BY Ncenum";  //mostrar los eventos dependiendo de lo que ingresen en la busqueda
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);

				//Se crea un arreglo con los coordinadores para poder extraer el nombre y mostrarlo en pantalla.
				$sql_arbol ="SELECT *
							   FROM ".$datos."_000008, ".$datos."_000013
							  WHERE Ideuse = Ajeucr
								AND Ideest = 'on'";
				$res_arbol = mysql_query( $sql_arbol, $conex ) or die( mysql_errno()." - Error en el query $sql_arbol - ".mysql_error() );

				$array_arbol_coor = array();

				while($row_arbol = mysql_fetch_assoc($res_arbol)){

					$centros_costo = explode(",",$row_arbol['Ajeccc']);

					//Si hay varios centros de costos, los separa.
					if(count($centros_costo) > 1){

						foreach($centros_costo as $key => $value){

							if(!array_key_exists($value, $array_arbol_coor)){

							$array_arbol_coor[$value] = array('jefe'=>$row_arbol['Ajeucr'], 'nombre'=>$row_arbol['Ideno1']." ".$row_arbol['Ideno2']." ".$row_arbol['Ideap1']." ".$row_arbol['Ideap2']);

							}
						}

					}else{

						if(!array_key_exists($row_arbol['Ajeucr'], $array_arbol_coor)){

							$array_arbol_coor[$row_arbol['Ajeccc']] = array('jefe'=>$row_arbol['Ajeucr'], 'nombre'=>$row_arbol['Ideno1']." ".$row_arbol['Ideno2']." ".$row_arbol['Ideap1']." ".$row_arbol['Ideap2']);

						}
					}

				}


				//Array de subordinados
				$sql_arbol ="SELECT *
							   FROM ".$datos."_000008, ".$datos."_000013
							  WHERE Ideuse = Ajeuco
								AND Ideest = 'on'";
				$res_arbol = mysql_query( $sql_arbol, $conex ) or die( mysql_errno()." - Error en el query $sql_arbol - ".mysql_error() );
				$res_arbol_coor_sub = mysql_query( $sql_arbol, $conex ) or die( mysql_errno()." - Error en el query $sql_arbol - ".mysql_error() );

				//Array coordinadores con subordinados
				$array_arbol_coor_sub = array();

				while($row_arbol_coor_sub = mysql_fetch_assoc($res_arbol_coor_sub)){

					if(!array_key_exists($row_arbol_coor_sub['Ajeuco'], $array_arbol_coor_sub)){

					$array_arbol_coor_sub[$row_arbol_coor_sub['Ajeuco']] = array('jefe'=>$row_arbol_coor_sub['Ajeucr'], 'nombre'=>$row_arbol_coor_sub['Ideno1']." ".$row_arbol_coor_sub['Ideno2']." ".$row_arbol_coor_sub['Ideap1']." ".$row_arbol_coor_sub['Ideap2']);


					}
				}

				//Array de todos los empleados
				 $sql_empleados = " SELECT *
									  FROM ".$datos."_000013";
				 $res_empleados = mysql_query( $sql_empleados ,$conex) or die( mysql_errno()." - Error en el query $sql_empleados - ".mysql_error() );

				 $array_empleados = array();

					while($row_empleados = mysql_fetch_assoc($res_empleados)){

						if(!array_key_exists($row_empleados['Ideuse'], $array_empleados)){

						$array_empleados[$row_empleados['Ideuse']] = array('cod_empleado'=>$row_empleados['Ideuse'], 'estado'=>$row_empleados['Ideest'], 'nombre'=>$row_empleados['Ideno1']." ".$row_empleados['Ideno2']." ".$row_empleados['Ideap1']." ".$row_empleados['Ideap2']);


						}
					}


	if ($num > 0)
	{

		$data['html'] = "";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .="<INPUT type='hidden' id='cambiar_cco_genero' value=''>";
		$data['html'] .="<INPUT type='hidden' id='grabar_obs' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>$num NOTIFICACIONES</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 350px;overflow:scroll;' : '')."'>";
		$data['html'] .="<center><table width='100%'>"; //interna

		for( $i = 0; $rows = mysql_fetch_array($res,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{

			//nombre del centro de costo que detecto
			$rows1 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccd'],$wemp_pmla));

			//nombre de la persona que detecto
			$rows2 = mysql_fetch_array( consultarNombrePersonaDetecto($datos,$cco,$rows['Ncecpd'],$wemp_pmla1,$rows['Nceccd']) );

			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

			//nombre del centro de costo que genero
			$rows3 = mysql_fetch_array( consultarNombreCC($cco,$rows['Nceccg'],$wemp_pmla) );

			$caso="todas";

			//se trae el estado
			$rows5 = mysql_fetch_array(consultarEstadoEvento($rows['Nceest'])  );

			if ($evento=='NC')
			{

				$opciones = consultarCausasNC($wbasedato,$evento,$rows['Id']);
			}
			else
			{

				$causa = consultarCausasEA($wbasedato,$rows['Id']);
			}
			//$j=$i+1;
			if( $i%2 == 0 )
			{
				$class = "class='fila1'";
			}
			else
			{
				$class = "class='fila2'";
			}

				if( $i == 0)
				{
					$data['html'] .= "<head>";
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Persona que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Jefe que aprueba</td>";
						$data['html'] .= "<td align='center' style=''>Jefe Unidad <br>que genero</td>";
						$data['html'] .= "<td align='center' style=''>Tipo Evento</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
					$data['html'] .= "</head>";
				}

				/*esto se coloca para escapar las comillas dobles y las comillas sencillas en todos los resultados $rows para no tocar cada uno,
				   para la comilla simple se coloca el ascii porque presento error*/
				foreach( $rows as $key => $value)
				{
					$rows[ $key ] = str_replace("\\","\\\\",$rows[ $key ] ); //se escapa el \ nceres - nceccd
					$rows[ $key ] = str_replace('"','\"',str_replace("'","&#39;",$rows[ $key ] ));
				}

				$wtexto_proceso = ($array_procesos_cco[$rows_datos['Ncepro']]['Prodes'] != '') ? " - ".STRTOUPPER($array_procesos_cco[$rows_datos['Ncepro']]['Prodes']) : "";

				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( \"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Nceaca'])))."\", this,\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($nom)))."\",\"".utf8_encode($rows['Nceccd'])."\",\"".$rows1['Cconom']."\",\"".utf8_encode($rows2['Cardes'])."\",\"".$rows2['Ideext']."\","
					."\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".utf8_encode($rows3['Cconom'])."\","
					."\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Ncedes'] ) ) )."\",\"".$rows['Nceest']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncetaf'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncenaf'])))."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Nceaci']) ) )."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncesev'])))."\""
					.",\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\""
					.",\"".$rows['Id']."\",\"".utf8_encode($rows['Nceres'])."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\""
					.",\"".str_replace( "\r","\\r",str_replace( "\n","\\n",utf8_encode($rows['Ncecor'])))."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\""
					.",\"".$rows['Ncecne']."\",\"".$array_procesos_cco[$rows_datos['Ncepro']]['Prodes']."\")'>";

					$data['html'] .= "<td align='center' style='' class='numero' title='N&uacutemero'>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style='' class='fecha' title='Fecha de generaci&oacuten'>".$rows['Fecha_data']."</td>";

					$codigo_detecto = $rows['Ncecpd'];

					if(strlen($codigo_detecto) == 7 AND substr($codigo_detecto, 2) != $wemp_pmla1)
						{

							$wemp_pmla_aux=(substr($codigo_detecto, 0,2)); //el wemp_pmla son los dos primeros digitos
							$key_aux = substr( $codigo_detecto, -5 );
						}
						else
						{
							$wemp_pmla_aux=$wemp_pmla1;
							$key_aux = substr( $codigo_detecto, -5 );
						}

					$persona_detecto = $array_empleados[$key_aux."-".$wemp_pmla_aux]['nombre'];
					$estado_persona = $array_empleados[$key_aux."-".$wemp_pmla_aux]['estado'];

					if($persona_detecto != ""){

						if($estado_persona == 'on'){
							$persona_detecto = $array_empleados[$key_aux."-".$wemp_pmla_aux]['nombre'];
						}else{
							$persona_detecto_inactiva = $array_empleados[$key_aux."-".$wemp_pmla_aux]['nombre'];
							$persona_detecto = $persona_detecto_inactiva." <font color='red'><b><br>(INACTIVO EN EL SISTEMA)</b></font>";
						}
					}



					$data['html'] .= "<td align='center' style='' class='persona_detecto' title='Persona que detect&oacute'>".$persona_detecto."</td>";
					$data['html'] .= "<td align='center' style='' class='unidad_detecto' title='Unidad que detect&oacute'>".utf8_encode($rows['Nceccd'])."-".utf8_encode($rows1['Cconom'])."</td>";
					$data['html'] .= "<td align='center' style='' class='unidad_genero' title='Unidad que gener&oacute'>".utf8_encode($rows['Nceccg'])."-".utf8_encode($rows3['Cconom']).$wtexto_proceso."</td>";

					//Se busca en el arreglo el jefe de la unidad que genero.
					if($array_arbol_coor[$rows['Nceccg']]['nombre'] == ''){
						$jefe = "SIN JEFE EN EL ARBOL DE RELACION";
					}else{

						$jefe = $array_arbol_coor[$rows['Nceccg']]['nombre'];
					}

					$jefe_aprueba = $array_arbol_coor_sub[$key_aux."-".$wemp_pmla_aux]['jefe'];
					$nombre_jefe = $array_empleados[$jefe_aprueba]['nombre'];

					if($nombre_jefe == ""){

						$nombre_jefe = "SIN JEFE EN EL ARBOL DE RELACION";
					}

					$data['html'] .= "<td align='center' style='' class='jefe_aprueba' title='Jefe que aprueba'>".$nombre_jefe."</td>";
					$data['html'] .= "<td align='center' style='' class='jefe_unidad_genero' title='Jefe unidad que genero'>".$jefe."</td>";
					$data['html'] .= "<td align='center' style='' class='tipo_evento' title='Tipo de evento'>".$rows['Ncecne']."</td>";
					$data['html'] .= "<td align='center' style='' class='estado' title='Estado' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";

					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";
			}//for
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";

	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'editar')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	if(isset($_SESSION['user']))
	{
		$key=substr($_SESSION['user'],2,strlen($_SESSION['user']));
	}
	else
	{
		$data['error'] = 1;
		$data['mensaje'] = "Ingresar a Matrix nuevamente.\n\nSe cerro la session.";
		echo json_encode($data);
		return;
	}


	//Consulto los registros que se encuentren relacionados a alguna NC o EA.
	$sql_rel = "SELECT Regide, Regire
				  FROM ".$wbasedato."_000012
				 WHERE Regide = '".$id."'
				   AND Regest = 'on'";
	$res_rel = mysql_query( $sql_rel, $conex ) or die( mysql_errno()." - Error en el query $sql_rel - ".mysql_error() );
	$num_rel = mysql_num_rows( $res_rel );

	$array_relacionados_id = array();

	//Creo un arreglo con esos identificadores.
	while($row_rel = mysql_fetch_array($res_rel))
		{
		//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row_rel['Regire'], $array_relacionados_id))
			{
			$array_relacionados_id[$row_rel['Regire']] = $row_rel['Regire'];
			}

		}

	//se hace una consulta a la tabla usuarios para traer el nombre
    $queryU = "select Descripcion
			   FROM usuarios
			   WHERE Codigo = '".$key."'";
	$resU = mysql_query($queryU) or die( mysql_errno()." - Error en el query $queryU - ".mysql_error() );
    if (mysql_num_rows($resU)>0)
	{
		$rowsU = mysql_fetch_array($resU);
		$nombre=$rowsU['Descripcion'];

	}
    /* concatenar observaciones*/
	if($obs != "")
	{
		$fecha=date("Y-m-d");
		$hora=date("H:i:s");
		$pieComen="-------------------------------------------------------------------------------";
		$cadena=utf8_decode( $nombre )." ".$fecha." ".$hora."\n".utf8_decode($obs)."\n".$pieComen;
		$obs=$cadena;
	}
	/* fin concatenar observaciones*/

	/* concatenar causas rechazo*/
	if($rechazada != "")
	{
		$fecha=date("Y-m-d");
		$hora=date("H:i:s");
		$pieComen="-------------------------------------------------------------------------------";
		$cadena1=utf8_decode( $nombre )." ".$fecha." ".$hora."\n".utf8_decode($rechazada)."\n".$pieComen;
		$rechazada=$cadena1;
	}
	/* fin causas rechazo rechazada*/

	/*Se hace la parte de consultar si el usuario que ingreso tiene rol y cual es*/
	$q=" SELECT Rnccod,Roldes
		   FROM ".$wbasedato."_000007,".$wbasedato."_000004
		  WHERE Rnctip ='".$evento."'
			AND Rncest ='on'
			AND Rnccod = Rolcod
			AND Rolest ='on'
			AND Roltip =Rnctip";

		if ($evento == 'NC')
		{
			$q.=" AND Rncanc='on'";
		}
		else
		{
			$q.=" AND Rncaea='on'";
		}

			$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );

			for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
			{
				if( !isset( $ARR2[ $rows1['Rnccod'] ] ) )
				{
					$usu.=",".$rows1['Rnccod'];
					$ARR2[ $rows1['Rnccod'] ] = 1;
				}

				if($i == 0)
				{
					$usu1.=$rows1['Roldes']; //Ncecag ccog
				}
				else
				{
					$usu1.=",".$rows1['Roldes']; //Ncecag ccog
				}

			}
			if (substr($usu, 0, 1) == ",")
			{
				$usu = substr( $usu, 1 ); //para quitar la , del principio
			}
	/*Fin de la parte de consultar si el usuario que ingreso tiene rol y cual es*/


	if ($evento=="NC")
	{

		if ($est=='02' AND $niv=='2')  //se le manda coordinador unidad que genero
		{

			$query=" UPDATE ".$wbasedato."_000001
                        SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
                        Nceest='".$est."',
                        Ncecla='".$clasif."',
                        Ncecag='',
                        Nceniv='".$niv."'
                        WHERE Id='".$id."' ";
			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		if ($est=='03' AND $niv=='2')  //el jefe inmediato rechaza
		{

			$query=" UPDATE ".$wbasedato."_000001
						SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
							 Nceest='".$est."',
							 Nceniv='".$niv."',
							 Ncecar=concat( Ncecar,'\n', '".$rechazada."')
					  WHERE Id='".$id."' ";

			/*****Se inserta en la tabla de log    NO SE ENVIA USUARIO ACTUAL PORQUE AHI MUERE EL PROCESO**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		if ($est=='03' AND $niv=='3') //coordinador del area que genero rechaza se le envia a alejANDro
		{

			$query=" UPDATE ".$wbasedato."_000001
						SET Nceobs = concat( '".$obs."','\n', Nceobs ),
							Nceest='".$est."',
							Ncecon='".$usu."',
							Nceniv='".$niv."',
							Ncecar=concat( Ncecar,'\n', '".$rechazada."')
					  WHERE Id='".$id."' ";

			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}

		if ($est=='03' AND $niv=='9')  //rechaza alejANDro despues que rechazo unidad que genero
		{

			$query=" UPDATE ".$wbasedato."_000001
						SET Nceobs = concat( '".$obs."','\n', Nceobs ),
							Nceest='".$est."',
							Nceniv='".$niv."',
							Ncecar=concat( Ncecar,'\n', '".$rechazada."')
					 WHERE  Id='".$id."' ";

			/*****Se inserta en la tabla de log    NO SE ENVIA USUARIO ACTUAL PORQUE AHI MUERE EL PROCESO**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		if ($est=='02' AND $niv=='4' )  //se le mANDa al coordinador del area que genero segunda vez
		{


			//ya tiene el codigo del coordinador del area que genero
			$query=" UPDATE ".$wbasedato."_000001
						SET Nceobs = concat( '".$obs."','\n', Nceobs ),
							Nceest='".$est."',
							Nceniv='".$niv."'
					  WHERE Id='".$id."' ";

			/*****Se inserta en la tabla de log**/ //PROBAR SI LE LLEGA EL $cag y si no se envia otra vez
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		if ($est=='04' AND $niv=='5')  //se le mANDa a alejANDro en correccion
		{

			//ya tiene el codigo de alejANDo Ncecon
			$query=" UPDATE ".$wbasedato."_000001
						SET Nceobs = concat( '".$obs."','\n', Nceobs ),
							Nceest='".$est."',
							Nceniv='".$niv."',
							Ncecon='".$usu."',
							Ncecor='".utf8_decode($correc)."',
							Nceacc='".utf8_decode($accionc)."'
					  WHERE Id='".$id."' ";

			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/

			/******************** guarda la causa ****************/
			if($output != '')
			{
				$array_codigos = explode(",", $output);

				$instruccion_insert = "
					INSERT INTO ".$wbasedato."_000002
					(Medico,Fecha_data,Hora_data,Caucod,Cautip,Caneid,Cauest,Seguridad)
					 VALUES ";

				$arr_values = array();
				foreach ($array_codigos as $key => $codigo) //$key = posicion $codigo = dato
				{
				   $arr_values[] = " ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."','".trim($codigo)."','".$evento."','".$id."','on','C-".$wbasedato."') ";
				}

				if(count($arr_values) > 0)
				{
				   $instruccion_insert .= implode(",",$arr_values).";";
				   $resCN = mysql_query($instruccion_insert,$conex)or die( mysql_errno()." - Error en el query $instruccion_insert - ".mysql_error() );

				}
			}

		}
		if ($est=='05' AND $niv=='6')  //se le mANDa a angela con la accion
		{


			//ya tiene el codigo de alejANDo Ncecon
			 $query=" UPDATE ".$wbasedato."_000001
                         SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
							  Nceest = '".$est."',
							  Nceuvi = '".$usu."',
							  Nceniv = '".$niv."',
							  Nceacc = '".$accionc."'
                       WHERE  id = '".$id."' ";

			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		if ($est=='06' AND $niv=='7') //se cierra
		{

			//ya tiene el codigo de alejANDo Ncecon
			$query=" UPDATE ".$wbasedato."_000001
						SET Nceobs = concat( '".$obs."','\n', Nceobs ),
							Nceest='".$est."',
							Nceuvi='".$usu."',
							Nceniv='".$niv."',
							Nceacc='".utf8_decode($accionc)."'
					  WHERE Id='".$id."' ";

			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex
			/*******/

			//Se recorren los identificadores asociados y se les hace la misma modificacion.
			foreach($array_relacionados_id as $llave => $value){

			//ya tiene el codigo de alejANDo Ncecon
			$query_rel =" UPDATE ".$wbasedato."_000001
                         SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
							  Nceest = '".$est."',
							  Nceuvi = '".$usu."',
							  Nceniv = '".$niv."',
							  Nceacc = '".utf8_decode($accionc)."'
                       WHERE  id = '".$llave."' ";
			$res = mysql_query( $query_rel, $conex ) or die( mysql_errno()." - Error en el query $query_rel - ".mysql_error());

			/*****Se inserta en la tabla de log**/
			$sql = " INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
					                      VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$llave."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex
			/*******/

			}
		}

		$queryAnCaus=" UPDATE ".$wbasedato."_000001
					SET Nceaca = '".$anaCausal."'
				  WHERE Id='".$id."' ";
		$resSql = mysql_query( $queryAnCaus, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex

	}
	else
	{ //eventos adversos

		if ($est=='02' AND $niv=='2') //le mANDa a las unidades que controla invecla/santiago/magda
		{
		   //Se bsuca el cmapo por el cual se debe filtrar la consulta dependiendo del tipo de evento.
			$q_aux =" SELECT Tiecam
						FROM ".$wbasedato."_000015
					   WHERE Tieval = '".$inf."'";
			$res_aux = mysql_query($q_aux) or die( mysql_errno()." - Error en el query $q_aux - ".mysql_error() );
			$row_campo = mysql_fetch_array($res_aux);
			$dato_campo = $row_campo['Tiecam'];	//Campo con el cual se hara el filtro de quien administra que tipo de evento.

			//se consulta el rol
			$q = "SELECT Rnccod,Roldes
					FROM ".$wbasedato."_000007,".$wbasedato."_000004
					WHERE Rnctip ='".$evento."'
					AND Rncest ='on'
					AND Rnccod = Rolcod
					AND Rolest ='on'
					AND Roltip = Rnctip";

			//Se agrega a la consulta el filtro de quien coordina, el campo es traido de tabla gescal_000015.
			$q.= " AND ".$dato_campo." = 'on'";

				// if ($inf=='Infeccioso' )
				// {
					// $q.=" AND Rnccin='on'";
				// }
				// else if ($inf=='No Infeccioso')
				// {
					// $q.=" AND Rnccni='on'";
				// }
				// else if($inf=='Relacionado con medicamentos o medios')
				// {
					// $q.=" AND Rnccme='on'";
				// }


				$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );

				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{

					if( !isset( $ARR3[ $rows1['Rnccod'] ] ) )
					{
						$usu.=",".$rows1['Rnccod'];
						$ARR3[ $rows1['Rnccod'] ] = 1;
					}
					if($i == 0)
					{
						$usu1.=$rows1['Roldes']; //Ncecag ccog
					}
					else
					{
						$usu1.=",".$rows1['Roldes']; //Ncecag ccog
					}
				}

			if (substr($usu, 0, 1) == ",")
			{
				$usu = substr( $usu, 1 ); //para quitar la , del principio
			}

			// if ($inf=='Infeccioso' )  //se busca el encargado de controlar lo infeccioso
			// {

			$query=" UPDATE ".$wbasedato."_000001 SET
							Nceobs = concat( '".$obs."','\n', Nceobs ),
							Nceest ='".$est."',
							Nceuvi ='".$usu."',
							Nceniv ='".$niv."',
							Ncedes ='".utf8_decode($desc)."',
							Nceaci ='".utf8_decode($desc_acc)."',
							Ncecls ='".utf8_decode($clase_even)."',
							Ncetip ='".utf8_decode($tipo_even)."',
							Nceeve ='".utf8_decode($even)."',
							Ncesev ='".utf8_decode($sev)."',
							Ncepre ='".$prev."',
							Ncecen ='".$cent."',
							Ncefue ='".$inf."',
							Nceana ='".utf8_decode($analiz)."',
							Ncetaf ='".utf8_decode($afec)."',
							Ncenaf ='".utf8_decode($nom_afec)."',
							Nceeaf ='".$ed_afec."',
							Nceiaf ='".$id_afec."',
							Ncehca ='".$hc."',
							Nceres ='".utf8_decode($resp)."'
						 WHERE id='".$id."' ";

			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/


			// }
			// else if($inf=='No Infeccioso')
			// {


				// $query="UPDATE ".$wbasedato."_000001
				// SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
				// Nceest='".$est."',
				// Nceuvi='".$usu."',
				// Nceniv='".$niv."',
				// Ncedes='".utf8_decode($desc)."',
				// Nceaci='".utf8_decode($desc_acc)."',
				// Ncecls='".utf8_decode($clase_even)."',
				// Ncetip='".utf8_decode($tipo_even)."',
				// Nceeve='".utf8_decode($even)."',
				// Ncesev='".utf8_decode($sev)."',
				// Ncepre='".$prev."',
				// Ncecen='".$cent."',
				// Ncefue='".$inf."',
				// Nceana='".utf8_decode($analiz)."',
				// Ncetaf='".utf8_decode($afec)."',
				// Ncenaf='".utf8_decode($nom_afec)."',
				// Nceeaf='".$ed_afec."',
				// Nceiaf='".$id_afec."',
				// Ncehca='".$hc."',
				// Nceres='".utf8_decode($resp)."'
				// WHERE Id='".$id."' ";

				// /*****Se inserta en la tabla de log**/
				// $sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						// VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				// $resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				// /*******/

			// }
			// else if($inf=='Relacionado con medicamentos o medios')
			// {


				// $query="UPDATE ".$wbasedato."_000001
				// SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
				// Nceest='".$est."',
				// Nceuvi='".$usu."',
				// Nceniv='".$niv."',
				// Ncedes='".utf8_decode($desc)."',
				// Nceaci='".utf8_decode($desc_acc)."',
				// Ncecls='".utf8_decode($clase_even)."',
				// Ncetip='".utf8_decode($tipo_even)."',
				// Nceeve='".utf8_decode($even)."',
				// Ncesev='".utf8_decode($sev)."',
				// Ncepre='".$prev."',
				// Ncecen='".$cent."',
				// Ncefue='".$inf."',
				// Nceana='".utf8_decode($analiz)."',
				// Ncetaf='".utf8_decode($afec)."',
				// Ncenaf='".utf8_decode($nom_afec)."',
				// Nceeaf='".$ed_afec."',
				// Nceiaf='".$id_afec."',
				// Ncehca='".$hc."',
				// Nceres='".utf8_decode($resp)."'
				// WHERE Id='".$id."' ";

				// /*****Se inserta en la tabla de log**/
				// $sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						// VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				// $resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				// /*******/
			// }

			/******* Se le envian los factores**/
			$filas = explode("|-|",$array_campos);
				$arr_inserts = array();
				$arr_update = array();
				$error_update = false;
				$qUpdate = '';
				foreach($filas as $key => $valores_campos)
				{
					//id_accion+"|"+id_plan+"|"+accion_val+"|"+fechar+"|"+fechapr+"|"+seguimiento+"|"+responsable+"|"+estado
					$campos = explode("|",$valores_campos);
					// print_r($campos);
					$factor 	= $campos[0]; //primer select
					$cuales     = $campos[1]; //segundo select
					$directo	= $campos[2]; //tercer select
					$id_causa   = $campos[3]; //id causa
					$relacionado   = $campos[4]; //select relacionados

					if($id_causa != '')
					{
						//UPDATE
						$qUpdate = "UPDATE ".$wbasedato."_000002
						SET Caucod='".$cuales."',
						Cautip='".$evento."',
						Caucon='".$directo."',
						Caudes='".$relacionado."'
						WHERE id='".$id_causa."'";
					 $qUpdate = mysql_query( $qUpdate ,$conex) or die( mysql_errno()." - Error en el query $qUpdate - ".mysql_error() );

					}
					else
					{
						//INSERT
						//id_accion+"|"+id_plan+"|"+accion_val+"|"+fechar+"|"+fechapr+"|"+seguimiento+"|"+responsable+"|"+estado
						$arr_inserts[] = "('".$wbasedato."','".date("Y-m-d")."', '".date("H:i:s")."','".$cuales."','".$relacionado."','".$evento."','".$directo."','".$id."','on','C-".$wbasedato."')";

					}
				}


				if(count($arr_inserts) > 0)
				{
					$qInsert = "INSERT 	INTO ".$wbasedato."_000002
										(Medico,Fecha_Data,Hora_data,Caucod,Caudes,Cautip,Caucon,Caneid,Cauest,Seguridad)
								VALUES	".implode(",",$arr_inserts).';';

					 $rqInsert = mysql_query( $qInsert ,$conex) or die( mysql_errno()." - Error en el query $qInsert - ".mysql_error() );

				}
		/*********/



		}

		if ($est=='02' AND $niv=='3') //le mANDa a unidad que genero
		{

			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
				Nceest='".$est."',
				Ncecag='',
				Nceniv='".$niv."'
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='03' AND $niv=='2') //magda rechaza
		{

			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
				Nceest='".$est."',
				Nceniv='".$niv."',
				Ncecar=concat( Ncecar,'\n', '".$rechazada."')
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}



		if ($est=='03' AND $niv=='3') //unidad control rechaza
		{

			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
				Nceest='".$est."',
				Ncecon='".$usu."',
				Nceniv='".$niv."',
				Ncecar=concat( Ncecar,'\n', '".$rechazada."')
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='03' AND $niv=='4') //le mANDa a magda porque la unidad rechazo
		{

			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs),
				Nceest='".$est."',
				Ncecon='".$usu."',
				Nceniv='".$niv."',
				Ncecar=concat( Ncecar,'\n', '".$rechazada."')
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='03' AND $niv=='9') //magda rechaza despues de que unidad genero rechaza
		{

			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs),
				Nceest='".$est."',
				Nceniv='".$niv."',
				Ncecar=concat( Ncecar,'\n', '".$rechazada."')
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='02' AND $niv=='5') //le mANDa a la unidad que genero por segunda vez
		{
			//ya se tiene el codigo del coordinador
			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs),
				Nceest='".$est."',
				Nceniv='".$niv."'
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mANDarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='04' AND $niv=='6') //le mANDa a magda con correccion
		{

			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs),
				Nceest='".$est."',
				Ncecon='".$usu."',
				Nceniv='".$niv."',
				Ncecor='".utf8_decode($correc)."',
				Nceacc='".utf8_decode($accionc)."'
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mANDarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."', '".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='05' AND $niv=='7') //le mANDa a magda con accion
		{

			$query="UPDATE ".$wbasedato."_000001
				SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
				Nceest='".$est."',
				Nceuvi='".$usu."',
				Nceniv='".$niv."',
				Nceacc='".utf8_decode($accionc)."'
				WHERE Id='".$id."' ";

				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mANDarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."', '".$niv."', 'C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='06' AND $niv=='8') //cerrar
		{

			$query=" UPDATE ".$wbasedato."_000001
                        SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
                        Nceest='".$est."',
                        Nceuvi='".$usu."',
                        Nceniv='".$niv."',
                        Nceacc='".utf8_decode($accionc)."'
                        WHERE Id='".$id."' ";
				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mANDarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$id."', '".$niv."', 'C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/


				//Se recorren los identificadores asociados y se les hace la misma modificacion.
			foreach($array_relacionados_id as $llave => $value){

			//ya tiene el codigo de alejANDo Ncecon
			$query_rel =" UPDATE ".$wbasedato."_000001
                         SET  Nceobs = concat( '".$obs."','\n', Nceobs ),
							  Nceest = '".$est."',
							  Nceuvi = '".$usu."',
							  Nceniv = '".$niv."',
							  Nceacc = '".utf8_decode($accionc)."'
                       WHERE  id = '".$llave."' ";
			$res = mysql_query( $query_rel, $conex ) or die( mysql_errno()." - Error en el query $query_rel - ".mysql_error());

			/*****Se inserta en la tabla de log**/
			$sql = " INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
					                      VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu1."', '".$key."', '".$est."','".$llave."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex
			/*******/

			}


		}
	} //EA


		if($query != "")
		{
			if($res2 = mysql_query( $query ))
			{

				array_unique($registros_a_relacionar);


				//Registro de identificadores asociados a la accion (NC o EA) Jonatan Lopez 25 Marzo de 2014
				if(count($registros_a_relacionar) > 0){

					foreach($registros_a_relacionar as $key => $value){

					//Esta validacion evita que se guarden registros en la tabla 12 de gescal, se da cuando cierran el evento o nc.
					if($value != 'null'){

						$sql_reg =" INSERT INTO ".$wbasedato."_000012 (     Medico     ,       Fecha_data   ,       Hora_data    ,  Regide  ,    Regire   , Regest, Seguridad)
															   VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$id."', '".$value."',  'on', 'C-".$wbasedato."' )";
						$res_reg = mysql_query( $sql_reg ) or die( mysql_errno()." - Error en el query $sql_reg - ".mysql_error() );

						//Actualiza el estado de los registro a estado relacionada
						$sql = "UPDATE ".$wbasedato."_000001
								   SET Nceest = '".$estado_relacionadas."'
								 WHERE Id = '".$value."' ";
						$res = mysql_query( $sql ,$conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

						}

					}

				}

				$data['mensaje'] = "Se ha ingresado correctamente ";

			}
			else
			{
				$data['error'] = 1;
				$data['mensaje'] = mysql_errno()." - Error en el query $query - ".mysql_error(); //mirar conex
			}
		}
		else
		{
			$data['error'] = 1;
			$data['mensaje'] = "No hay consulta para ejecutar "; //mirar conex
		}

	echo json_encode($data);
	return;
}


if (isset($accion) AND $accion == 'cargar')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');


		$data['mensaje'] = "Se ha ingresado correctamente";



	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'llenar_select_hijo')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');

		//se trae la clase de factor
								$sql = "select Caucod,Caudes
											FROM root_000091
											WHERE Caucla='".$id_padre."'
											AND Cauest='on'
											AND Cautip='".$evento."'
											";
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

								//$data['html'] .="<SELECT NAME='".$id_fila."_fact_1' id='".$id_fila."_fact_1' onchange='llenar_hijo(\"".$id_fila."_cuales\")'>"; //llenar_hijo(\"".$id_fila."_cuales\")
								$data['html'] .="<option value=''>Seleccione..</option>";
											for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
											{
												$data['html'] .= "
																<option value='".$rows['Caucod']."'>".utf8_encode($rows['Caudes'])."</option>";

											}
								//$data['html'] .="</SELECT>";


	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'llenar_select_evento')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');


								$sql = "select Caucod,Caudes
											FROM root_000091
											WHERE Caucla='".$id_padre."'
											AND Cauest='on'
											AND Cautip='EAG'
											";
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

								//$data['html'] .="<SELECT NAME='".$id_fila."_fact_1' id='".$id_fila."_fact_1' onchange='llenar_hijo(\"".$id_fila."_cuales\")'>"; //llenar_hijo(\"".$id_fila."_cuales\")
								$data['html'] .="<option value=''>Seleccione..</option>";
											for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
											{
												$data['html'] .= "
																<option value='".$rows['Caucod']."'>".utf8_encode($rows['Caudes'])."</option>";

											}
								//$data['html'] .="</SELECT>";


	echo json_encode($data);
	return;
}


if (isset($accion) AND $accion == 'evaluarEstado')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'','validacion'=>0);

			if($est=='06') //cerrar o analizada
			{
				if($evento=="NC" )
				{
					if( $nivel=='6') //nivel 6 acciones puede cerrar
					{
						//se consultan los estados de las acciones de ese evento
						$sql = "SELECT COUNT( * )
								  FROM  ".$wbasedato."_000003, ".$wbasedato."_000002, root_000093
								 WHERE ".$wbasedato."_000003.accaid = ".$wbasedato."_000002.Id
								   AND ".$wbasedato."_000002.caneid ='".$id_evento."'
								   AND accest = estcod
								   AND estcie != 'on'
								   AND ".$wbasedato."_000003.accesa='on'";
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$contados = mysql_result($res,0); //se le quita el count
						if($contados>0)
						{
							$data['mensaje'] ="No se puede cerrar el evento tiene acciones pendientes";
						}
						else
						{
							$data['validacion'] =1;
							$data['mensaje'] ="Se cerrara el evento";
						}
					}
					if( $nivel=='5') //nivel 5 correccion puede cerrar
					{
						//se consultan si el campo acciones de la tabla si necesita acciones
						$sql = "SELECT Nceacc
							FROM  ".$wbasedato."_000001
							WHERE id = '".$id_evento."'
							AND Nceact='on'
							";
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$rows = mysql_fetch_array( $res );
						if($rows['Nceacc']=="Si" AND $radio_acciones=="Si")
						{
							$data['mensaje'] ="No se puede cerrar el evento se necesitan acciones correctivas";
						}
						else
						{
							$data['validacion'] =1;
							$data['mensaje'] ="Se cerrara el evento";
						}
					}
				}
				elseif($evento=="EA")
				{
					if($nivel=='7') //nivel 7 acciones puede cerrar
					{

						//se consultan los estados de las acciones de ese evento
						$sql = "SELECT COUNT( * )
							      FROM  ".$wbasedato."_000003, ".$wbasedato."_000002, root_000093
							     WHERE  ".$wbasedato."_000003.accaid = ".$wbasedato."_000002.Id
							       AND ".$wbasedato."_000002.caneid ='".$id_evento."'
								   AND accest = estcod
								   AND estcie != 'on'
						 	       AND ".$wbasedato."_000003.accesa='on'";
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$contados = mysql_result($res,0); //se quito el count
						if($contados>0)
						{
							$data['mensaje'] ="No se puede cerrar el evento tiene acciones pendientes ";
						}
						else
						{
							$data['validacion'] =1;
							$data['mensaje'] ="Se cerrara el evento";
						}
					}
					if( $nivel=='6') //nivel 5 correccion puede cerrar
					{
						//se consultan los estados de las acciones de ese evento
						$sql = "SELECT Nceacc
							FROM  ".$wbasedato."_000001
							WHERE id = '".$id_evento."'
							AND Nceact='on'
							";
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$rows = mysql_fetch_array( $res );
						if($rows['Nceacc']=="Si" AND $radio_acciones=="Si")
						{
							$data['mensaje'] ="No se puede cerrar el evento se necesitan acciones correctivas";
						}
						else
						{
							$data['validacion'] =1;
							$data['mensaje'] ="Se cerrara el evento";
						}
					}
				}
			} //se agrega validacion para estado = 5 para que no permita cerrar si necesita acciones y no permita poner en analiza si no necesita acciones
			elseif($est=='05') //accion
			{
				if($evento=="NC" )
				{
					if( $nivel=='5') //nivel 5 correccion puede cerrar
						{

							if($radio_acciones!="Si")
							{
								$data['mensaje'] ="No se puede poner el evento en estado analizada porque no necesita acciones correctivas";
							}
							else
							{
								$data['validacion'] =1;
								$data['mensaje'] ="Se pondra el evento en estado analizada";
							}
						}
					if ($nivel=='6') //para que saque no saque el alert vacio
					{
						$data['validacion'] =1;
						$data['mensaje'] ="Se pondra el evento en estado analizada";
					}
				}
				elseif($evento=="EA")
				{

					if( $nivel=='6') //nivel 5 correccion puede cerrar
					{

						if($radio_acciones!="Si")
						{
							$data['mensaje'] ="No se puede poner el evento en estado analizada porque no necesita acciones correctivas";
						}
						else
						{
							$data['validacion'] =1;
							$data['mensaje'] ="Se pondra el evento en estado analizada";
						}
					}
					if ($nivel=='7') //para que saque no saque el alert vacio acciones puede cerrar
					{
						$data['validacion'] =1;
						$data['mensaje'] ="Se pondra el evento en estado analizada";
					}
				}
			}



	echo json_encode($data);
	return;
}

if (isset($accion) AND $accion == 'eliminar_factores')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');

		//se actualiza el estado(activo) de la accion a off para que no lo muestre
						$sql = "UPDATE ".$wbasedato."_000002
							SET  Cauest = 'off'
							WHERE Id='".$id_eliminar."' ";

						$res = mysql_query( $sql ,$conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );


	echo json_encode($data);
	return;
}

//Cambios en el control de opciones por perfil Jonatan Lopez 19 Marzo 2014
if (isset($accion) AND $accion == 'consultar_roles')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','menus'=>'', 'session'=>'' , 'sql'=>'');

	//Se crea un array con los codigos autorizados para ver las pendientes por aprobar, y asi puedan aprobar nc de aud. interna.
	$datos_usuarios_aud_int = explode("-", $user_aut_aud_inter_nc );

	if(isset($_SESSION['user']))
	{
		$key=substr($_SESSION['user'],2,strlen($_SESSION['user']));
	}
	else
	{
		$data['error'] = 1;
		$data['mensaje'] = "Ingresar a Matrix nuevamente.\n\nSe cerro la session.";
		echo json_encode($data);
		return;
	}


		//Consulto si el usuario tiene perfil de coordinaro o jefe y creo un arreglo.
		//if($wtipo_even !='NC')
		if($wtipo_even !='NC')
		{

			//
			$sql_rol = "  SELECT Ajecoo, Ajeucr
							FROM ".$datos."_000008,".$datos."_000013
						   WHERE Ajeucr='".$key2."-".$wemp_pmla1."'
							 AND Ideuse=Ajeucr
							 AND Ideest = 'on'
						GROUP BY Ajeucr ";


			 $res_rol = mysql_query( $sql_rol ,$conex) or die( mysql_errno()." - Error en el query $sql_rol - ".mysql_error() );
			 $num_rol = mysql_num_rows($res_rol);
			 $row_rol = mysql_fetch_assoc($res_rol);
			 $data['sql'] =$sql_rol;

			//Coordinador
			if($row_rol['Ajecoo'] == 'on'){
				$tipo_usu = 1;
			}else{
				//Jefe
				//Aqui varifica que no es coordinador pero maneja empleados.
				if($row_rol['Ajecoo'] != 'on' and $num_rol > 0){
					$tipo_usu = 2;
				}else{
					//Empleado.
					 //Verifica si tiene registro en talento humani, si no tiene muestra un mensaje de alerta.
					 $sql_carac = " SELECT id
									FROM ".$datos."_000013
								   WHERE Ideuse='".$key2."-".$wemp_pmla1."'
									 AND Ideest = 'on'";
					 $res_carac = mysql_query( $sql_carac ,$conex) or die( mysql_errno()." - Error en el query $sql_carac - ".mysql_error() );
					 $num_carac = mysql_num_rows($res_carac);

					if($num_carac > 0)
					{

						$tipo_usu = 3;

						//Se agrega esta validacion para que los empleados que esten registrados para autorizar
						//nc de aud. interna puedan ver las nc recibidas y aprobarlas o rechazarlas. (Jonatan Lopez 14 Agosto 2013)
						if(in_array($key2, $datos_usuarios_aud_int))
						{
							$wautoriza_nc_aud_inter =  "td_nc_recibidas";
						}

					}else
					{
						$tipo_usu = 3;
					}
				}
			}
			//

		}
		else
		{
			$sql_rol = "  SELECT Ajecoo, Ajeucr
							FROM ".$datos."_000008,".$datos."_000013
						   WHERE Ajeucr='".$key2."-".$wemp_pmla1."'
							 AND Ideuse=Ajeucr
							 AND Ideest = 'on'
						GROUP BY Ajeucr ";


			 $res_rol = mysql_query( $sql_rol ,$conex) or die( mysql_errno()." - Error en el query $sql_rol - ".mysql_error() );
			 $num_rol = mysql_num_rows($res_rol);
			 $row_rol = mysql_fetch_assoc($res_rol);
			 $data['sql'] =$sql_rol;

			//Coordinador
			if($row_rol['Ajecoo'] == 'on'){
				$tipo_usu = 1;
			}else{
				//Jefe
				//Aqui varifica que no es coordinador pero maneja empleados.
				if($row_rol['Ajecoo'] != 'on' and $num_rol > 0){
					$tipo_usu = 2;
				}else{
					//Empleado.
					 //Verifica si tiene registro en talento humani, si no tiene muestra un mensaje de alerta.
					 $sql_carac = " SELECT id
									FROM ".$datos."_000013
								   WHERE Ideuse='".$key2."-".$wemp_pmla1."'
									 AND Ideest = 'on'";
					 $res_carac = mysql_query( $sql_carac ,$conex) or die( mysql_errno()." - Error en el query $sql_carac - ".mysql_error() );
					 $num_carac = mysql_num_rows($res_carac);

					if($num_carac > 0)
					{

						$tipo_usu = 3;

						//Se agrega esta validacion para que los empleados que esten registrados para autorizar
						//nc de aud. interna puedan ver las nc recibidas y aprobarlas o rechazarlas. (Jonatan Lopez 14 Agosto 2013)
						if(in_array($key2, $datos_usuarios_aud_int))
						{
							$wautoriza_nc_aud_inter =  "td_nc_recibidas";
						}

					}
					else
					{


						// cambio
						/*$data['mensaje'] = "No tiene registros en talento humano, favor llenar la caracterizacion.".$sql_rol."segundo query".$sql_carac;
						$data['error'] = 1;
						echo json_encode($data);
						return false;*/

						$tipo_usu = 3;

					}
				}
			}
	    }
		//======================== Configuracion del menu =============================================

		//Verifico las opciones que tiene relacionado el evento y el tipo de usuario.
		 $sql_conf = " SELECT Menper, Menopc, Opccon
						 FROM {$wbasedato}_000009, {$wbasedato}_000010, {$wbasedato}_000011
						WHERE Mencla = '".$evento."'
						  AND Menper = '".$tipo_usu."'
						  AND Menopc = Opccod
						  AND Percod = Menper
						  AND Perest = 'on'
						  AND Menest = 'on'
						   UNION
						SELECT '{$tipo_usu}', upeopc, Opccon
						  FROM {$wbasedato}_000022, {$wbasedato}_000010
						 WHERE opccod = upeopc
						   AND upecod = '{$key}'
						 GROUP by 1, 2, 3";

		 $res_conf = mysql_query( $sql_conf ,$conex) or die( mysql_errno()." - Error en el query $sql_conf - ".mysql_error() );

		 $array_opciones_menu = array();
		 $data['sql'] =$sql_conf;

		while($row_conf = mysql_fetch_assoc($res_conf))
		{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_conf['Menopc'], $array_opciones_menu))
			{
				$array_opciones_menu[$row_conf['Menopc']] = $row_conf['Opccon'];
			}
		}

		//Asocio a la posicion menu el arreglo resultante en donde se dan los permisos de ver las opciones.
		$data['menus'] = $array_opciones_menu;

		//Agrego al arreglo la posicion td_nc_recibidas para los usuario tipo empleados pero que pueden aprobar nc de auditoria interna.
		array_push($data['menus'], $wautoriza_nc_aud_inter );

		//Se consulta el rol administrador para nc y ea.
		$sql_admin = " SELECT Rncanc, Rncaea, Rnccme, Rnccin, Rnccni
						 FROM ".$wbasedato."_000004,".$wbasedato."_000007
						WHERE Roltip='".$evento."'
						  AND Rolest = 'on'
						  AND Roldes = '".$key."'
						  AND Rnccod = Rolcod
						  AND Rnctip = Roltip
						  AND Rncest = 'on'";
		$res_admin = mysql_query( $sql_admin, $conex ) or die( mysql_errno()." - Error en el query $sql_admin - ".mysql_error() );
		$row_admin = mysql_fetch_assoc($res_admin);

		//Si el usuario es administrador de NC se le agregara al array $data['menus'] las opciones de administracion de NC.
		if($row_admin['Rncanc'] == 'on'){

			$rol_admin = 4;

			//Busco las opciones de administrador NC.
			 $sql_rol_admin_a = " SELECT Menper, Menopc, Opccon
									FROM ".$wbasedato."_000009, ".$wbasedato."_000010, ".$wbasedato."_000011
								   WHERE Mencla = '".$evento."'
									 AND Menper = '".$rol_admin."'
									 AND Menopc = Opccod
									 AND Percod = Menper
									 AND Perest = 'on'
									 AND Menest = 'on'";
			 $res_rol_admin_nc = mysql_query( $sql_rol_admin_a ,$conex) or die( mysql_errno()." - Error en el query $sql_rol_admin_a - ".mysql_error() );

			while($row_conf_admin_a = mysql_fetch_assoc($res_rol_admin_nc)){

				$data['menus'][] = $row_conf_admin_a['Opccon'];	//Se llena el arreglo $data['menus'][] con las configuraciones para administrar la accion.
				$data['error'] = 0;
			}

		}else{

			//Verifico si es administrador de EA.
			if($row_admin['Rncaea'] == 'on'){

			$rol_admin = 5;

			//Si el usuario es administrador de NC se le agregara al array $data['menus'] las opciones de administracion de NC.
			 $sql_rol_admin_b = " SELECT Menper, Menopc, Opccon
								   FROM ".$wbasedato."_000009, ".$wbasedato."_000010, ".$wbasedato."_000011
								  WHERE Mencla = '".$evento."'
									AND Menper = '".$rol_admin."'
									AND Menopc = Opccod
									AND Percod = Menper
									AND Perest = 'on'
									AND Menest = 'on'";
			 $res_rol_admin_b = mysql_query( $sql_rol_admin_b ,$conex) or die( mysql_errno()." - Error en el query $sql_rol_admin_b - ".mysql_error() );

			 $array_opc_admin = array();

			while($row_conf_admin_b = mysql_fetch_assoc($res_rol_admin_b)){

					$data['menus'][] = $row_conf_admin_b['Opccon'];		//Se llena el arreglo $data['menus'][] con las configuraciones para administrar la accion.
					$data['error'] = 0;

				}
			}else{

				if($row_admin['Rnccme'] == 'on' or $row_admin['Rnccin'] == 'on' or $row_admin['Rnccni'] == 'on'){
				//Si el usuario es administrador de NC se le agregara al array $data['menus'] las opciones de administracion de NC.
					$sql_rol_admin_b = " SELECT Menper, Menopc, Opccon
										   FROM ".$wbasedato."_000009, ".$wbasedato."_000010, ".$wbasedato."_000011
										  WHERE Mencla = '".$evento."'
											AND Menper in ('6','7','8')
											AND Menopc = Opccod
											AND Percod = Menper
											AND Perest = 'on'
											AND Menest = 'on'";
					 $res_rol_admin_b = mysql_query( $sql_rol_admin_b ,$conex) or die( mysql_errno()." - Error en el query $sql_rol_admin_b - ".mysql_error() );

					 $array_opc_admin = array();

					while($row_conf_admin_b = mysql_fetch_assoc($res_rol_admin_b)){

							$data['menus'][] = $row_conf_admin_b['Opccon'];		//Se llena el arreglo $data['menus'][] con las configuraciones para administrar la accion.
							$data['error'] = 0;

						}
					}
				}

		}


	// echo "<pre>";
	// print_r($data);
	// echo "<pre>";
	echo json_encode($data);
	return;
}
?>


<html lang="es-ES">
<!DOCTYPE html>
<head>
<title>Ingreso no conformidad o evento adverso</title>
<meta charset="utf-8">

<!--<meta charset="ISO-8859-1"> -->
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link rel="stylesheet" href="../../../include/root/jquery-ui-timepicker-addon.css" />
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>


<!--< Plugin para el select con buscador > -->

<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
<script type="text/javascript" src="../../../include/root/prettify.js"></script>
<script src="../../../include/root/jquery-ui-timepicker-addon.js" type="text/javascript" ></script>
<script src="../../../include/root/jquery-ui-sliderAccess.js" type="text/javascript" ></script>



<script type="text/javascript">

function pulsar(e) {
	tecla=(document.all) ? e.keyCode : e.which;
  if(tecla==13) return false;
}

//Funcion que permite el registro de observaciones cuando la accion se encuentra en estado correccion. Jonatan 19 marzo 2014
function grabar_solo_observacion(){

	var obs = $('#obs').val();
	var id_Evento = $('#id_Evento').val();
	var wemp_pmla = $('#wemp_pmla1').val();
	var wbasedato = $('#wbasedato_aux').val();

	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     		'grabar_solo_observacion',
					wemp_pmla:			wemp_pmla,
					wbasedato:			wbasedato,
					obs:				obs,
					id_Evento:			id_Evento

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					alert(data_json.mensaje);
					}
					else
					{

					$('#obs').val('');
					$('#obs_ant').val(data_json.observaciones_actuales);
					$('#tr_obs_ant').show();
					alert(data_json.mensaje);

					}

			},
			"json"
		);

}


//Funcion que guarda el centro de costos por el cual se cambio la accion. Jonatan 19 marzo 2014
function cambiar_unidad_genero(){

	var cco_genero_nuevo = $('#cco_genero_nuevo').val();
	var nom_cco_genero =  $("#cco_genero_nuevo option:selected").text();
	var id_Evento = $('#id_Evento').val();
	var wemp_pmla = $('#wemp_pmla1').val();
	var wbasedato = $('#wbasedato_aux').val();
	var procesos_cco_modal = $('#select_procesos_cco').val();
	var nom_procesos_cco_modal =  $("#select_procesos_cco option:selected").text();

	if(cco_genero_nuevo != ''){

	if ($('#procesos_cco_modal').is(':visible')){
		if(procesos_cco_modal == '')	{
		alert('Debe seleccionar un proceso para este centro de costos');
		return false;
		}
	}

	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     		'cambiar_cco_genero',
					wemp_pmla:			wemp_pmla,
					wbasedato:			wbasedato,
					cco_genero_nuevo:	cco_genero_nuevo,
					id_Evento:			id_Evento,
					procesos_cco_modal:	procesos_cco_modal

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					alert(data_json.mensaje);
					}
					else
					{

					$('#u_genero_mostrar').val(nom_cco_genero);
					$('#centros_costo').val(nom_cco_genero);
					$('#nuevo_cco_genero').dialog('close');

					//Si selecciona proceso, los mostrara en la interfaz, sino lo oculta.
					if ($('#procesos_cco_modal').is(':visible')){
					$("#procAsociadoCco").html(nom_procesos_cco_modal);
					}else{
					$("#procAsociadoCco").hide();
					}

					alert(data_json.mensaje);

					}

			},
			"json"
		);
	}else{

	alert('Debe seleccionar un centro de costos');
	}

}

//Al seleccionar la imagen de actualizacion de cco, con esta funcion se abre la vnetana modal. Jonatan 19 marzo 2014
function abrir_modal_cco_genero(){


	$("#nuevo_cco_genero").dialog( "open" );
	$("#procesos_cco_modal").hide();
	$("#cco_genero_nuevo option[value='']").attr("selected",true);



	}


//Se marcara el radio button de la fuente como autocontrol si el centro de costos que genero es el mismo que detecto, en caso contrario lo vuelve NC interna.
function marcar_fuente_autocontrol(cco_genero, origen){

	var cco_detecto = $('#ccod').val();
	var cco_detecto = cco_detecto.split("-");
	var cco_genero = cco_genero.split("-");


	switch(origen)
		{
		//Si el usuario selecciona el cco que causa el evento ingresa por esta opcion.
		case 'desdeselect':

		 if(cco_detecto[0] == cco_genero[0]){
				$("#auto").attr('checked', true);
				alert('Se marcará la fuente como Autocontrol ya que la unidad que detectó es la misma unidad que generó');
			}

		  break;

		//Si el usuario intenta cambiar la fuente y el cco que genero es igual al cco que detecto, mantiene la fuente autocontrol marcada.
		case 'desdechecked':

		  var cco_genero = $('#centros_costo').val();
		  var cco_detecto = $('#ccod').val();

		  if(cco_genero == cco_detecto){
			$("#auto").attr('checked', true);
			alert('Se marcará la fuente como Autocontrol ya que la unidad que detectó es la misma unidad que generó');
		  }

		  break;

		}



}


//Al seleccionar un centro de costos, se mostrara el listado de procesos, en caso de tener asociados. Jonatan 19 marzo 2014
function activar_select_clases(){

	var ccos_filtro = $("#ccos_filtro").val();

	if(ccos_filtro != '%'){

	var clases = $("#clases").removeAttr('disabled');
	$("#select_procesos_cco").show();
	}else{

	$("#clases").attr('disabled','disabled');

	}

}


function detalle_solicitudes(id, codid)
{

if($("#"+id).is(':visible'))
		{
		$("#"+id).hide('1000');
		$('#imgmenos_'+codid).hide();
		$('#imgmas_'+codid).show();
		}
	else
		{
		$("#"+id).css('display','');
		$('#imgmas_'+codid).hide();
		$('#imgmenos_'+codid).show();
		}

}

//Muestra los procesos relacionados con el centro de costos seleccionado. Jonatan 19 marzo 2014
function mostrar_procesos_cco_modal(){


	var wemp_pmla = $('#wemp_pmla1').val();
	var wbasedato = $('#wbasedato_aux').val();
	var cco = $('#cco_genero_nuevo').val();
	var modal = 'ok';

	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     'mostrar_procesos_cco',
					wemp_pmla:	wemp_pmla,
					wbasedato:	wbasedato,
					cco:		cco,
					modal:		modal

				}
				,function(data_json) {

					if (data_json.error == 1)
					{

					}
					else
					{

					if(data_json.procesos != ''){
					$('#procesos_cco_modal').show();
					$('#procesos_cco_modal').html(data_json.procesos);
					}

					}

			},
			"json"
		);

}


//Muestra el select con los procesos asociados al cco. Jonatan 19 marzo 2014
function mostrar_procesos_cco(cco){


	var wemp_pmla = $('#wemp_pmla1').val();
	var wbasedato = $('#wbasedato_aux').val();

	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     'mostrar_procesos_cco',
					wemp_pmla:	wemp_pmla,
					wbasedato:	wbasedato,
					cco:		cco

				}
				,function(data_json) {

					if (data_json.error == 1)
					{

					}
					else
					{

					if(data_json.procesos != ''){
					$('#procesos_cco').html(data_json.procesos);
					}

					}

			},
			"json"
		);

}

//Activa el plug-in jquery para cargar los autocomplete
function cargar_autocomplete()
	{

        var wempresas = $("#master_empresas");
        var arr_empresas = new Array();
        var datos_empresas = eval('(' + $("#hidden_empresas").val() + ')');

        for( i in datos_empresas ){
            arr_empresas.push( datos_empresas[i] );
        }
        wempresas.autocomplete({
                source: arr_empresas, minLength : 1

        });


        var wcco = $("#centros_costo");
        var arr_coo = new Array();
        var datos_cco = eval('(' + $("#hidden_cco").val() + ')');

        for( i in datos_cco ){

            arr_coo.push( datos_cco[i] );
        }
        wcco.autocomplete({
                source: arr_coo, minLength : 1,
                select: function( event, ui ) {
                               // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                               $("#ccos").val(ui.item.value);
							   mostrar_procesos_cco(ui.item.value);
							   marcar_fuente_autocontrol(ui.item.value, 'desdeselect');
                           }

        });


	}


function cambiarColor( cmp ){


	var evento =$("#tipo_even").val();

	if (evento=="NC")
	{
		$("#tabla_navegacion_nc").find("li").removeClass("active");
	}
	else
	{
		$("#tabla_navegacion_ea").find("li").removeClass("active");
	}
	$("#"+cmp).addClass("active");

}

function buscarCco(cco,filtro)
{

	var valor = $("#centro_costo").val();

	$.post("ingreso_nce.php",
			{
				consultaAjax:   '',
				cco:  			cco,
				accion:         'buscar',
				filtro:			filtro,
				valor:          valor


			}
			,function(data) {
				if(data.error == 1)
			{

			}
			else
			{
				if (filtro=='ingresar')
				{
					$("#ccos").html(data.options); //al select se le envia el array data en la posicion options
				}
				else
				{
					$("#ccos_filtro").html(data.options);
				}
			}
		},
		"json"
	);
}

function buscarCcoG(cco,filtro)
{

	var valor = $("#centro_costoG").val();

	$.post("ingreso_nce.php",
			{
				consultaAjax:   '',
				cco:  			cco,
				accion:         'buscar',
				filtro:			filtro,
				valor:          valor


			}
			,function(data) {
				if(data.error == 1)
			{

			}
			else
			{
				if (filtro=='ingresar')
				{
					$("#ccos").html(data.options); //al select se le envia el array data en la posicion options
				}
				else
				{
					$("#ccos_filtroG").html(data.options);
				}
			}
		},
		"json"
	);
}

function enviar(key2,wbasedato,datos,wemp_pmla1,cco,tabla_referencia, rol_coor, estnc_coord, wnivel)
{

    $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

	//que encuentre los campos input con la clase camporRequerido y le remueva esa clase
	$("#contenedor_eventos").find(":input.campoRequerido").removeClass('campoRequerido');
	var evento = $("#tipo_even").val(); //para diferenciar si es NC o EA

	var todo_ok=validarCampos();
	var txtdesc = $("#desc").val();
	//var txtdesc_acc = $("#desc_acc").val();
	if (evento=='EA')
	{
		if(txtdesc.length<15)
		{
			todo_ok=false;
			alert("Debe escribir como minimo 15 caracteres en la descripcion");
		}
	}
	else
	{
		if(txtdesc.length<15)
		{
			todo_ok=false;
			alert("Debe escribir como minimo 15 caracteres en la descripcion");
		}
	}


	if (todo_ok==true && datosUsuarioOK == 0)
	{

				//crear evento


				var ccod = $("#ccod").val();
				var fechad = $("#fechai").val();
				var horad = $("#hora").val();
				var ccog1 = $("#ccos").val();
                var ccog2 = ccog1.split("-"); //Separo por guion el cco del nombre
                var ccog = ccog2[0]; // Tomo solo el cco
				var desc = $("#desc").val();
				var est = $("#estados_nc").val();
                var clasificacion = $("#clasificacion").val();
				var proceso_cco = $("#select_procesos_cco").val(); //Proceso asociado al centro de costos

				if (evento=="NC")
				{
					var fuente=$("input:radio[name='fuente']:checked").val();

					var afec = "";  //los demas se ponen en blanco
					var nom_afec = "";
					var ed_afec = "";
					var id_afec = "";
					var hc = "";
					var desc_acc = "";
					var clase_even="";
					var tipo_even = "";
					var even = "";
					var sev = "";
					var prev="";
					var cent="";
					var inf="";
					var analiz = "";
					var res="";
				}
				else
				{

					var afec = $("input:radio[name='clasifique']:checked").val();
					var nom_afec = $("#nom_afec").val();
					var ed_afec = $("#edad").val();
					var id_afec = $("#ide").val();
					var hc = $("#hc").val();
					var desc_acc = $("#desc_acc").val();
					var inf="";
					var resp = $("#master_empresas").val();

					var fuente="";

				}


				$.post("ingreso_nce.php",
						{

							consultaAjax:   '',
							evento:     	evento,
							accion:         'enviar',
							ccod: 			ccod,
							fechad: 		fechad,
							horad: 			horad,
							ccog: 			ccog,
							desc:			desc,
							est:			est,

							fuente: 		fuente,

							afec:     	 afec,
							nom_afec:    nom_afec,
							ed_afec:     ed_afec,
							id_afec:     id_afec,
							hc:          hc,
							desc_acc:    desc_acc,
							tipo_even:   tipo_even,
							inf:	     inf,
							resp:		 resp,
							key2:		 key2,
							wbasedato:	 wbasedato,
							datos:		 datos,
							wemp_pmla1:  wemp_pmla1,
							cco:		 cco,
                            rol_coor:    rol_coor,
                            estnc_coord: estnc_coord,
                            wnivel: wnivel,
                            clasificacion: clasificacion,
							proceso_cco: proceso_cco
						}
						,function(data) {
							if(data.error == 1)
						{
							alert(data.mensaje);
							$.unblockUI();
						}
						else
						{
							alert(data.mensaje); // update Ok.
							ocultarTodo();
                            $.unblockUI();
							var abrir_menu=""; //se necesita que cuANDo ingrese un evento, redirija a enviadas
							if (evento=="NC")
							{
								$("#navegacion_nc").css("display", "");  //mostrar divs
								abrir_menu=$("#td_nc_enviadas")[0].firstChild.onclick();
								abrir_menu=$("#td_nc_enviadas")[0].onclick();
							}
							else
							{
								$("#navegacion_ea").css("display", "");  //mostrar divs
								abrir_menu=$("#td_ea_enviadas")[0].firstChild.onclick();
								abrir_menu=$("#td_ea_enviadas")[0].onclick();
							}

							$('#contenedor_eventos').dialog('close');

						}
					},
					"json"
				);

	}
	else
	{
		if (datosUsuarioOK != 0)
		{
			alert("Para poder enviar eventos debe llenar los datos de la caracterización de Talento Humano y/o Comunicarse con Desarrollo Organizacional1");
			$.unblockUI();
		}
		if (todo_ok!=true)
		{
			alert("Los campos marcados en el fomulario estan errados o son obligatorios, favor verificar.");
			$.unblockUI();
		}
	}

}


function a_unico(ar){

    //Declaramos las variables
    var ya=false,v="",aux=[].concat(ar),r=Array();
    //Buscamos en el mismo Array si
    //cada elemento tiene uno repetido
    for (var i in aux){ //
        v=aux[i];
        ya=false;
        for (var a in aux){
            //Preguntamos si es el primer elemento
            //o si ya se recorrió otro igual
            //Si es el primero se asigna true a la variable "ya"
            //Si no es el primero, se le da valor vacio
            if (v==aux[a]){
                if (ya==false){
                    ya=true;
                }
                else{
                    aux[a]="";
                }
            }
        }
    }
    //Aquí ya tenemos los valores duplicados
    //convertidos en valores vacios
    //Solo falta crear otro Array con los valores
    //que quedaron sin contar los vacios
    for (var a in aux){
        if (aux[a]!=""){
            r.push(aux[a]);
        }
    }
    //Retornamos el Array creado
    return r;
}


function editar(key2,wbasedato,datos,wemp_pmla1,cco,tabla_referencia)
{

     $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

	//Que encuentre los campos input con la clase camporRequerido y le remueva esa clase
	$("#contenedor_eventos").find(":input.campoRequerido").removeClass('campoRequerido');

	var est = $("#estados_nc").val();
	var niv = $( "#nivel" ).val();

	var todo_ok=validarCampos(est,niv);
	var msg = '';

	if( !todo_ok ){ //mensaje de campos requeridos
		msg = "Debe llenar los campos requeridos marcados en amarillo.";
	}

	//si no se ha seleccionado si necesita acciones correctivas o no
	if( $("input:radio[name='accion']").is( ":visible" ) == true && $("input:radio[name='accion']:checked").length == 0 && est != '03' )
    {
			if(msg == "") //se concatena al mensaje de campo requeridos o no segun el caso
			{
			   msg+="Debe seleccionar si el evento necesita acciones correctivas o no";
			}
			else
			{
				msg+=" y debe seleccionar si el evento necesita acciones correctivas o no";
			}
				todo_ok=false;

    }

	if (todo_ok==true)
	{
			var evento = $("#tipo_even").val(); //para diferenciar si es NC o EA


			var obs = $("#obs").val();
			var anaCausal = $("#anaCausal").val();
			var id = $( "#id_Evento" ).val();
			var ccg = $( "#ccge" ).val();
			var clasif = ""; //se ponen las variables vacias luego se llenan
			var inf="";
			var correc="";
			var rechazada="";
			var accionc="";
			var campos="";

			if (evento=="NC")
			{
				if ( est=='02') //cuANDo el jefe inmediato aprueba
				{

					if (niv=='1')
					{
						var clasif = $("#clasificacion").val(); //depronto la observacion
						niv++;
					}
					else //segunda vez que se aprueba - alejANDro
					{
						niv='4';
					}
				}
				if ( est=='03')
				{
					if (niv=='1') //jefe inmediato rechaza
					{
						niv++
					}
					else if (niv=='2') //coord area que genero rechaza
					{
						niv='3';
					}

					else if(niv=='4' || niv=='3') //rechaza alejo despues de que se rechazo por unidad que genero
					{
						niv='9';
					}

					if ($("#est_rechazada").length>0)
					{
						rechazada =$("#est_rechazada").val();
					}
				}
				if ( est=='04')
				{
					niv='5'; //o niv =5
					correc=$("#cor").val(); //se envia la correccion

					var result = new Array(); //array que guarda todo las causas que seleccionen
					$("#causas_nc option:selected").each(function() {
					result.push($(this).val());
					});

					var output = result.join(", ");
					//alert(output);
					accionc=$("input:radio[name='accion']:checked").val(); //se le envia si tiene accion o no

				}
				if ( est=='05')
				{
					niv=6; //o niv=6
                    accionc=$("input:radio[name='accion']:checked").val(); //se le envia si tiene accion o no
					//se envia lo de la accion
				}
				if ( est=='06')
				{
					niv=7 //o niv=7
					accionc=$("input:radio[name='accion']:checked").val(); //se le envia si tiene accion o no
					//se cierra la no conformidad
				}

			}
			else
			{
				//eventos adversos
				var clasif = "";

				/**************/
				trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;

				//busca consecutivo mayor
				if(trs > 0)
				{
					//var campos = ''; //se coloca en comentario para prueba
					var separador_bloque = '';
					$("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
						var id_tr = $(this).attr('id'); // El this es el id de cada fila de la tabla
						var factor = $("#"+id_tr+"_fact_1").val();
						var cuales = $("#"+id_tr+"_cuales").val();
						var directo = $("#"+id_tr+"_indi").val();
						var id_causa = $("#"+id_tr+"_id").val();
						var relacionado = $("#"+id_tr+"_fact_s_n").val();

						campos = campos+separador_bloque+factor+"|"+cuales+"|"+directo+"|"+id_causa+"|"+relacionado;
						separador_bloque = '|-|';

					});

				}
				/*************/



				if ( est=='02') //magda aprueba se le envia casi todo
				{
					if (niv=='1')
					{
						var inf=$("input:radio[name='infec']:checked").val();
						var afec = $("input:radio[name='clasifique']:checked").val();
						var nom_afec = $("#nom_afec").val();
						var desc = $("#desc").val();
						var ed_afec = $("#edad").val();
						var id_afec = $("#ide").val();
						var hc = $("#hc").val();
						var desc_acc = $("#desc_acc").val();
						var clase_even=$("input:radio[name='clase']:checked").val();
						var tipo_even = $("#tipo_even1").val();
						var even = $("#evento_hijo").val();
						var sev = $("input:radio[name='sev']:checked").val();
						var prev=$("input:radio[name='prevenible']:checked").val();
						var cent=$("input:radio[name='centinela']:checked").val();
						var analiz = $("#analiz").val();
						var resp = $("#master_empresas").val();
						niv++;
					}
					else if(niv=='2')//segunda vez aprueba unidad control invecla/santiago/magda
					{
						niv='3';
					}
					else if(niv=='4')//aprueba segunda vez magda
					{
						niv='5';
					}
				}
				if (est=='03')
				{
					if (niv=='1') //magda rechaza
					{
						niv++;
					}
					else if (niv=='2') //unidad control rechaza
					{
						niv='3';
					}
					else if(niv=='3') //unidad que genero rechaza
					{
						niv='4';
					}
					else if(niv=='4') //rechaza magda despues de que se rechazo por unidad que genero
					{
						niv='9';
					}
					if ($("#est_rechazada").length>0)
					{
						rechazada =$("#est_rechazada").val();
					}
				}

				if (est=='04')
				{
					niv='6';
					correc=$("#cor").val(); //se envia la correccion
					accionc=$("input:radio[name='accion']:checked").val(); //se le envia si tiene accion o no
				}
				if (est=='05')
				{
					niv='7';
					accionc=$("input:radio[name='accion']:checked").val(); //se le envia si tiene accion o no

				}
				if (est=='06')
				{
					niv='8';
					accionc=$("input:radio[name='accion']:checked").val(); //se le envia si tiene accion o no
				}


			}

			//Captura los registros que se relacionarán a la accion.
			var registros_a_relacionar = $('select#registros_relacionados').val();   //Jonatan Lopez 21 Marzo 2014

			//Identificadores unicos en el arreglo.
			registros_a_relacionar = a_unico(registros_a_relacionar);

			var estado_relacionadas = $("#estado_relacionadas").val();

			$.post("ingreso_nce.php",
					{

						consultaAjax:   '',
						evento:     	evento,
						accion:         'editar',
						obs:			obs,
						anaCausal:      anaCausal,
						est:			est,
						inf:			inf,
						clasif:         clasif,
						ccg:			ccg,
						niv:			niv,
						correc:			correc,
						rechazada:		rechazada,
						inf:            inf,
						afec: 			afec,
						nom_afec:       nom_afec,
						desc: 			desc,
						ed_afec:        ed_afec,
						id_afec: 		id_afec,
						hc:  			hc,
						desc_acc:  		desc_acc,
						clase_even:		clase_even,
						tipo_even: 		tipo_even,
						even: 			even,
						sev: 			sev,
						prev:			prev,
						cent:     		cent,
						analiz:			analiz,
						resp:			resp,
						key2:			key2,
						wbasedato:		wbasedato,
						datos:			datos,
						wemp_pmla1:      wemp_pmla1,
						cco:			cco,
						id:				id,
						output:		    output,
						accionc:		accionc,
						array_campos:   campos,
						registros_a_relacionar:	registros_a_relacionar,
						estado_relacionadas:estado_relacionadas

					}
					,function(data) {
					if(data.error == 1)
					{
						alert(data.mensaje);
						 $.unblockUI();

					}
					else
					{
						alert(data.mensaje); // update Ok.
						var abrir_menu="";
						ocultarTodo();
                        $.unblockUI();
                        if (evento=="NC")
                        {
                            $("#navegacion_nc").css("display", "");  //mostrar divs
                                ver_menu = $("#tabla_navegacion_nc").find('.active').attr("id");
                                abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
                                abrir_menu=$("#"+ver_menu)[0].onclick();


                        }
                        else
                        {
                            $("#navegacion_ea").css("display", "");  //mostrar divs
                            ver_menu = $("#tabla_navegacion_ea").find('.active').attr("id");
                            abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
                            abrir_menu=$("#"+ver_menu)[0].onclick();

                        }

						$('#contenedor_eventos').dialog('close');
					}
				},
				"json"
			);
	}
	else
	{
		alert(msg);
        $.unblockUI();
	}

}

function addFila(tabla_referencia,key2,wbasedato,datos,wemp_pmla1,cco)
    {

		// para saber si la tabla tiene filas o no
        trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
        var value_id = 0;

        //busca consecutivo mayor
        if(trs > 0)
        {
            id_mayor = 0;
            // buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
            $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
                id_ = $(this).attr('id');
                id_splt = id_.split('_');
                id_this = (id_splt[0])*1;
                if(id_this >= id_mayor)
                {
                    id_mayor = id_this;
                }
            });
            id_mayor++;
            value_id = id_mayor+'_tr_'+tabla_referencia;

        }
        else
        { value_id = '1_tr_'+tabla_referencia; }

		$.ajax({
				url: "ingreso_nce.php",
				context: document.body,
				type: "POST",
				data: {
					consultaAjax: '',
					accion      : 'adicionar_fila',
					id_fila     : value_id,
					key2         : key2,
					wbasedato   : wbasedato,
					datos       : datos,
					wemp_pmla1   : wemp_pmla1,
					cco         : cco
				},
				async: false,
				dataType: "json",
				success: function(data){


					if(data.error == 1)
					{
						alert(data.mensaje);
					}
					else
					{
						$("#tabla_factores > tbody").append(data.html);
					}
				}

			});

    }

function removerFila(id_fila,wbasedato)
    {
        var id_eliminar = $("#"+id_fila+"_id").val(); //1_tr_tabla_factores_id
        acc_confirm = 'Confirma que desea eliminar?';
		if(confirm(acc_confirm))
		{
			if(id_eliminar != '')
			{
				$.post("ingreso_nce.php",
					{
						consultaAjax: '',
						accion      : 'eliminar_factores',
						id_eliminar : id_eliminar,
						wbasedato	: wbasedato
					},
					function(data){
						if(data.error == 1)
						{
							alert(data.mensaje);
						}
						else
						{

						}
					},
					"json"
				);
			}
			$("#"+id_fila).empty();
			$("#"+id_fila).remove();
		}
    }

function seleccion(datos,key2,wemp_pmla1,wbasedato,cco,wemp_pmla,user_aut_aud_inter_nc)
{
	//alert("hola");
    $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });

	ocultarTodo();

	var evento = $("#tipo_even").val();



	if(evento != 'seleccione'){

	// alert($("#tipo_even").val());

	$.post("ingreso_nce.php",
            {
                consultaAjax: '',
				accion      : 'consultar_roles',
                evento      :evento,
				key2        :key2,
				wbasedato   :wbasedato,
				datos       :datos,
				wemp_pmla1  :wemp_pmla1,
				wemp_pmla   :wemp_pmla,
				cco         :cco,
				user_aut_aud_inter_nc : user_aut_aud_inter_nc,
				wtipo_even  : $("#tipo_even").val()
            },
            function(data){
				// alert("entro");
				// alert(data.sql);
                if(data.error == 1)
                {
                    if (data.mensaje != "")
					{
						alert(data.mensaje);
					}else{

					alert(data.mensaje);
					}

					$.unblockUI();
                }
                else
                {

					var ver_menu=""; //se necesita que cuANDo ingrese a los eventos depediendo del rol se le muestre un menu por defecto
					ocultarTd();//alert(data.menus);
					$.each( data.menus, function( key2, value ) {

						$("#"+value).show();
						ver_menu=value;
					});

					var abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
					 abrir_menu=$("#"+ver_menu)[0].onclick();

					 $.unblockUI();
                }
            },
            "json"
        );
	}
	else
	{
		$.unblockUI();
	}


}

$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};

	$.datepicker.setDefaults($.datepicker.regional['esp']);


$(document).ready( function ()
	{

		$('[placeholder]').focus(function() {
		  var input = $(this);
		  if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		  }
		}).blur(function() {
		  var input = $(this);
		  if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		  }
		}).blur();

		ocultarTodo(); //se oculta todo al cargar la pagina menos el select de evento
		calendario('','');
        cargar_autocomplete();

		$("#wfec_i").datepicker({
			  showOn: "button",
			  buttonImage: "../../images/medical/root/calendar.gif",
			  buttonImageOnly: true
			});

		$("#wfec_f").datepicker({
			  showOn: "button",
			  buttonImage: "../../images/medical/root/calendar.gif",
			  buttonImageOnly: true
			});

	});

	function getMonth(month) {
		return month < 10 ? '0' + month : month;
	}

	function habilitarCampos(wcoordinador)
	{
		$("#nom").attr("readonly", false);
		$("#u_detecto_mostrar").attr("readonly", false);
		$("#car").attr("readonly", false);
		$("#ext").attr("readonly", false);
		$("#ema").attr("readonly", false);
		$("#fechai").attr("readonly", false);
		$("#hora").attr("readonly", false);
		$("#u_genero").attr("readonly", false);
		$("#desc").attr("readonly", false);
		$("#ccod").attr("readonly", false);
		$("#nom_afec").attr("readonly", false);
		$("#edad").attr("readonly", false);
		$("#ide").attr("readonly", false);
		$("#master_empresas").attr("readonly", false);
		$("#hc").attr("readonly", false);
		$("#desc_acc").attr("readonly", false);
		$('input[name="fuente"]').attr('disabled', false);
		$('input[name="clasifique"]').attr('disabled', false);
		$( "#1_tr_tabla_factores_fact_1" ).attr("disabled", false);
		$( "#1_tr_tabla_factores_fact_s_n" ).attr("disabled", false);
		$( "#1_tr_tabla_factores_cuales" ).attr("disabled", false);
		$( "#1_tr_tabla_factores_indi" ).attr("disabled", false);
		$('input[name="clase"]').attr('disabled', false);
		$('input[name="infec"]').attr('disabled', false);
		$( "#tipo_even1" ).attr("disabled", false);
		$( "#evento_hijo" ).attr("disabled", false);
		$('input[name="sev"]').attr('disabled', false);
		$('input[name="prevenible"]').attr('disabled', false);
		$('input[name="centinela"]').attr('disabled', false);
		$( "#analiz" ).attr("readonly", false);
		$("#nom").attr("readonly", false);
		$("#u_detecto_mostrar").attr("readonly", false);
		$("#car").attr("readonly", false);
		$("#ext").attr("readonly", false);
		$("#ema").attr("readonly", false);
		$("#fechai").attr("readonly", false);
		$("#hora").attr("readonly", false);
		$("#u_genero").attr("readonly", false);
		$("#desc").attr("readonly", false);
		$("#ccod").attr("readonly", false);
		$("#obs").attr("readonly", false);
		$("#anaCausal").attr("readonly", false);
		$("#cor").attr("readonly", false);
		$("#causas_nc" ).attr("readonly", false); //acom

        //Habilita la seleccion de caracterizacion del problema, solo si es coordinador, ademas activa el drop down de clasificacion.
        if(wcoordinador == 'on')
            {
            $("#clasificacion_conf").css("display", "");
            $("#clasificacion").removeAttr("disabled");
            }
	}

	function deshabilitarCampos()
	{
		$("#nom").attr("readonly", true);
		$("#u_detecto_mostrar").attr("readonly", true);
		$("#car").attr("readonly", true);
		$("#ext").attr("readonly", true);
		$("#ema").attr("readonly", true);
		$("#fechai").attr("readonly", true);
		$("#hora").attr("readonly", true);
		$("#u_genero").attr("readonly", true);
		$("#desc").attr("readonly", true);
		$("#ccod").attr("readonly", true);
		$("#nom_afec").attr("readonly", true);
		$("#edad").attr("readonly", true);
		$("#ide").attr("readonly", true);
		$("#master_empresas").attr("readonly", true);
		$("#hc").attr("readonly", true);
		$("#desc_acc").attr("readonly", true);
		$('input[name="fuente"]').attr('disabled', 'disabled');
		$('input[name="clasifique"]').attr('disabled', 'disabled');
		$('input[name="clase"]').attr('disabled', 'disabled');
		$('input[name="infec"]').attr('disabled', 'disabled');
		$( "#tipo_even1" ).attr("disabled", true);
		$( "#evento_hijo" ).attr("disabled", true);
		$('input[name="sev"]').attr('disabled', 'disabled');
		$('input[name="prevenible"]').attr('disabled', 'disabled');
		$('input[name="centinela"]').attr('disabled', 'disabled');
		$( "#analiz" ).attr("readonly", true);
		$("#nom").attr("readonly", true);
		$("#u_detecto_mostrar").attr("readonly", true);
		$("#car").attr("readonly", true);
		$("#ext").attr("readonly", true);
		$("#ema").attr("readonly", true);
		$("#fechai").attr("readonly", true);
		$("#hora").attr("readonly", true);
		$("#u_genero").attr("readonly", true);
		$("#desc").attr("readonly", true);
		$("#ccod").attr("readonly", true);
		$("#u_genero_mostrar").attr("readonly", true);
		$("#cor").attr("readonly", true);
		$( "#causas_nc" ).attr("readonly", true);
		$( "#clasificacion" ).attr("disabled", true);
		$("#caurechazo").attr("readonly", true);

	}

	function datosPorDefecto()
	{
		$("#nom").val( datosUsuario.Ideno1 + " " + datosUsuario.Ideno2 + " " + datosUsuario.Ideap1 ); //se muestran los datos por defecto del usuario
		$("#ccod").val( datosUsuario.Ccocod + "-" + datosUsuario.Cconom );
		$("#car").val( datosUsuario.Cardes );
		$("#ext").val( datosUsuario.Ideext );
		$("#ema").val( datosUsuario.Ideeml );
        //----------------------------------------
        //Segmento para imprimir la fecha actual por defecto
		var f = new Date();
		$("#fechai").val(f.getFullYear() + "-" + (getMonth(f.getMonth()+1)) + "-" + getMonth(f.getDate()));
        //----------------------------------------
        //----------------------------------------
        //Segmento para imprimir la hora actual por defecto
        var Digital=new Date();
        var hours=Digital.getHours();
        var minutes=Digital.getMinutes();
        var seconds=Digital.getSeconds();
        var dn="AM";

        if (minutes<=9)
            var minutes="0"+minutes
        if (seconds<=9)
            seconds="0"+seconds

        $("#hora").val(hours+ ":" + minutes + ":" + seconds);
        //----------------------------------------
		if (datosUsuarioOK != 0) //variable para saber si respondio bien la consulta de talhuma_13 con los datos basicos
		{
			$("#nom").val(""); //se muestran los datos por defecto del usuario
			$("#ccod").val("");
			$("#car").val("");
			$("#ext").val("");
			$("#ema").val("");
			alert("Para poder enviar eventos debe llenar los datos de la caracterización de Talento Humano y/o Comunicarse con Desarrollo Organizacional2");
			$.unblockUI();
		}
	}

	function formu_ingresar_nc(wcoordinador, textoevento)
	{

		if ($('#ccod').val() =='ninguno-ninguno')
		{
			jAlert("<span>El Usuario no tiene definido ningun centro de costos, no puede ingresar No conformidad</span>", "Mensaje");

			return;
		}

		ocultarTodo();  //se muestran solo los divs para ingresar la no conformidad
		resetearCampos(); //se deben resetear todos menos los campos de la persona que ingresa la no conformidad
		habilitarCampos(wcoordinador);//habilitar los campos porque si ya visitaron otra opcion del menu los trae readonly
		datosPorDefecto(); //llenar datos del usuario de matrix
		$("#tarea").html(""); //para que no muestre la causa de rechazo

		var evento = $("#tipo_even").val();
		var wemp_pmla = $('#wemp_pmla1').val();
		var wbasedato = $('#wbasedato_aux').val();

		$("#contenedor_eventos" ).dialog({
				show: {
				effect: "blind",
				duration: 100
				},
				hide: {
				effect: "blind",
				duration: 100
				},
				autoOpen: false,
				height: 600,
				width: 1150,
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Informacion de la acción"

				});

		//Estados

	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     		'select_estados',
					wemp_pmla:			wemp_pmla,
					wbasedato:			wbasedato,
					evento:				evento

				}
				,function(data_json) {

					//Imagen que muestra el dato cargando
					$('#select_estados').html( "3<img width='auto' height='auto' border='0' src='../../images/medical/cargando.gif'>" );

					if (data_json.error == 1)
					{
					alert(data_json.mensaje);
					}
					else
					{
						if(data_json.html != ''){
							$('#select_estados').html(data_json.html);
							}else{

						}

					}

			},
			"json"
		);



		$("#img_cambiar_cco_genero_a").css("display", "none");
		//mostrar divs
		$("#contenedor_eventos").dialog( "open" );
		//$("#contenedor_eventos").css("display", "");
		$("#navegacion_nc").css("display", "");
		$("#uni_detecto").css("display", "");
		$("#descripcion_conf").css("display", "");
		$("#fuente").css("display", "");
		$("#unidad_genero").css("display", "");
		$("#estados").css("display", "");
		$("#enviar").css("display", "");
		$("#hora").attr("readonly", true); //deshabilitar la hora //
		$(".mensajeevento").html("Causante de la "+textoevento);
        if(wcoordinador == 'on')
            {
            $("#clasificacion_conf").css("display", "");
            }

		//se llama a la funcion que llena los estados
		var select = document.getElementById( 'estados_nc' );
		//llenarEstados(select,'');
		validarCampos('','');
		$ ("[placeholder]").blur(); //para que se muestre la marca de agua

	}

	function formu_ingresar_ea(wcoordinador, textoevento)
	{
		if ($('#ccod').val() =='ninguno-ninguno')
		{
			jAlert("<span>El usuario no tiene definido ningun centro de costos, no puede ingresar Eventos adversos</span>", "Mensaje");

			//alert("");
			return;
		}
		ocultarTodo(); //se muestran solo los divs para ingresar el evento adverso
		resetearCampos();
		habilitarCampos(); //habilitar los campos porque si ya visitaron otra opcion del menu los trae readonly
		datosPorDefecto(); //llenar datos por defecto usuario matrix
		$("#tarea").html("");//para que no muestre la causa de rechazo

		var evento = $("#tipo_even").val();
		var wemp_pmla = $('#wemp_pmla1').val();
		var wbasedato = $('#wbasedato_aux').val();

		$("#contenedor_eventos" ).dialog({
				show: {
				effect: "blind",
				duration: 100
				},
				hide: {
				effect: "blind",
				duration: 100
				},
				autoOpen: false,
				// maxHeight:600,
				height: 700,
				width: 1150,
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Informacion de la acción"

				});

		//Estados

		$.post("ingreso_nce.php",
					{
						consultaAjax:       '',
						accion:     		'select_estados',
						wemp_pmla:			wemp_pmla,
						wbasedato:			wbasedato,
						evento:				evento

					}
					,function(data_json) {

						//Imagen que muestra el dato cargando
						$('#select_estados').html( "<img width='auto' height='auto' border='0' src='../../images/medical/cargando.gif'>" );

						if (data_json.error == 1)
						{
						alert(data_json.mensaje);
						}
						else
						{
							if(data_json.html != ''){
								$('#select_estados').html(data_json.html);
								}else{

							}

						}

				},
				"json"
			);

		//Ocultar el boton para cambiar el centro de costos que generó.
		$("#img_cambiar_cco_genero_a").css("display", "none");
		$("#img_cambiar_cco_genero_b").css("display", "none");

		//mostrar divs
		$("#contenedor_eventos").dialog( "open" );
		$("#navegacion_ea").css("display", "");
		$("#uni_detecto").css("display", "");
		$("#per_afec").css("display", "");
		$("#unidad_genero").css("display", "");
		$("#descripcion_conf").css("display", "");
		$("#acciones_ins").css("display", "");
		$("#estados").css("display", "");
		$("#enviar").css("display", "");
		$(".mensajeevento").html("Causante del "+textoevento);
		//se ponen los valores por defecto de los radio button despues de resetearlos
		$('#paci').attr('checked','checked');
		$('#eventoA').attr('checked','checked');
		$('#noda').attr('checked','checked');
		$('#si_p').attr('checked','checked');
		$('#si_c').attr('checked','checked');
		$("#hora").attr("readonly", true); //deshabilitar la hora
		//se llama a la funcion que llena los estados
		var select = document.getElementById( 'estados_nc' );
		//llenarEstados(select,'');
		validarCampos('','');
		$ ("[placeholder]").blur(); //para que se muestre la marca de agua
	}

	function nc_enviadas(datos,key2,wemp_pmla1,wbasedato,cco,tipo,consultar,div_resp)
	{
		//alert("hoy");
		  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

		//debugger;
		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}

		//alert("datos:"+datos+"--cco:"+cco+"--evento:"+evento+"--tipo:"+tipo+"--consultar:"+consultar+"--div_resp:"+div_resp);

		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_enviadas',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:     wemp_pmla1,
				cco:			cco,
				evento:         evento,
				tipo:			tipo,
				consultar:		consultar,
				div_resp:		div_resp

			}
			,function(data) {

			//alert(data);
			if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
				$.unblockUI();

			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#"+div_resp).html(data.html); // update Ok.
				$.unblockUI();
			}
			$.unblockUI();
		},
		"json"
	).done(function(){

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});
}

	function nc_recibidas(datos,key2,wemp_pmla1,wbasedato,cco, user_aut_aud_inter_nc)
	{
		  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}


		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_recibidos',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:     wemp_pmla1,
				cco:			cco,
				evento:         evento,
				user_aut_aud_inter_nc: user_aut_aud_inter_nc

			}
			,function(data) {
				if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				$.unblockUI();
			}
		},
		"json"
	).done(function(){

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});
}

function nc_recibidas_u_genero(datos,key2,wemp_pmla1,wbasedato,cco)
	{

	  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });


		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}


		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_recibidos_u_genero',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:      wemp_pmla1,
				cco:			cco,
				evento:         evento

			}
			,function(data) {
				if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				$.unblockUI();
			}
		},
		"json"
	).done(function(){

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});;
}

function nc_recibidas_u_control(datos,key2,wemp_pmla1,wbasedato,cco)
	{

	  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}

		//alert(evento);

		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_recibidos_u_control',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:      wemp_pmla1,
				cco:			cco,
				evento:         evento

			}
			,function(data) {
				if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				$.unblockUI();
			}
		},
		"json"
	).done(function(){

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});;
}



function nc_recibidas_correccion(datos,key2,wemp_pmla1,wbasedato,cco)
	{
		//alert("entor");
	  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });

		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}


		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_recibidos_correccion',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:      wemp_pmla1,
				cco:			cco,
				evento:         evento

			}
			,function(data) {
			if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
				$.unblockUI();
			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				$.unblockUI();
			}
		},
		"json"
	).done(function(){

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});
	$.unblockUI();


}

function nc_recibidas_accion(datos,key2,wemp_pmla1,wbasedato,cco)
	{

	  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}


		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_recibidos_accion',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:      wemp_pmla1,
				cco:			cco,
				evento:         evento

			}
			,function(data) {
				if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				$.unblockUI();
			}
		},
		"json"
	).done(function(){

		//Div que contiene boton para listar las no conformidades. Jonatan Lopez 21 Marzo de 2014.
		$("#relacionar_nc").css("display", "");

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});;
}

function nc_recibidas_rechazadas(datos,key2,wemp_pmla1,wbasedato,cco)
	{

	  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}


		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_recibidas_rechazadas',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:      wemp_pmla1,
				cco:			cco,
				evento:         evento

			}
			,function(data) {
				if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				$.unblockUI();
			}
		},
		"json"
	).done(function(){

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});
}

function nc_cerradas(datos,key2,wemp_pmla1,wbasedato,cco)
	{

	  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}


		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_cerradas',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:      wemp_pmla1,
				cco:			cco,
				evento:         evento

			}
			,function(data) {
				if(data.error == 1)
			{
				if (data.mensaje != "")
				{
					alert(data.mensaje);
				}
			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				$.unblockUI();
			}
		},
		"json"
	).done(function(){

		//Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#registros .find');

	});
}

function nc_todas(key2,wbasedato,datos,wemp_pmla1,cco)
	{

		ocultarTodo();

		 $.blockUI({ message:	'Procesando...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });


		$("#filtros").css("display", ""); //se muestra el div para filtrar los eventos a buscar
		var evento = $("#tipo_even").val();
		var validacion = true;
		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}

		var centroCosto = $("#ccos_filtro").val();
		var centroCostoG = $("#ccos_filtroG").val();
		var claseEvento = $("#clases").val();
		var estadoEvento = $("#estados_filtro").val();
		var tipoEvento = $("#tipos").val();
		//Jonatan - Marzo 12 - 2014
		var wfec_i = $("#wfec_i").val();
		var wfec_f = $("#wfec_f").val();
		var fuentes = $("#fuentes").val();


		if (centroCosto == "" || claseEvento == "" || estadoEvento == "" || tipoEvento == "")
		{validacion = false; $.unblockUI();	}

		if(validacion)
		{
			$.post("ingreso_nce.php",
				{

					consultaAjax:   '',
					accion:         'lista_todas',
					key2:			key2,
					wbasedato:		wbasedato,
					datos:			datos,
					wemp_pmla1:      wemp_pmla1,
					cco:			cco,
					evento:         evento,
					centroCosto:    centroCosto,
					centroCostoG:	centroCostoG,
					claseEvento:    claseEvento,
					estadoEvento:   estadoEvento,
					tipoEvento:     tipoEvento,
					wfec_i:			wfec_i,
					wfec_f:			wfec_f,
					fuentes:		fuentes

				}
				,function(data) {
					if(data.error == 1)
				{
					if (data.mensaje != "")
					{
						alert(data.mensaje);
						$.unblockUI();
					}
				}
				else
				{

					$("#div_lista_re").css("display", "");
					 $("#div_lista_re").html(data.html); // update Ok.
					$.unblockUI();
				}
			},
			"json"
		).done(function(){

			$(".numero").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".fecha").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".persona_detecto").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".unidad_detecto").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".unidad_genero").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".jefe_aprueba").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".jefe_unidad_genero").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".tipo_evento").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
			$(".estado").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		});
	}
	else
	{
		alert("Debe ingresar los datos para la busqueda");
	}

}

function mostrar_filtro(datos,key2,wemp_pmla1,wbasedato,cco)
{
		ocultarTodo();



		$( "select,input", $("#filtros") ).val(''); //busca todo lo que este dentro del div y lo resetea

		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}

		$("#filtros").css("display", "");

}

function resetearCampos()
{
	//que encuentre los campos input con la clase camporRequerido y le remueva esa clase
	$("#contenedor_eventos").find(":input.campoRequerido").removeClass('campoRequerido');

	$("#nom").val('');	//recetea campos que podrian estar llenos
	$("#ccod").val('');
	$("#car").val('');
    $('#centros_costo').val('');
    $('#cccos').val('');
	$("#ext").val('');
	$("#ema").val('');
	$("#fechai").val('');
	$("#hora").val('');
	$("#u_genero").val('');
	$("#desc").val('');
	$("#obs").val('');
	$("#anaCausal").val('');
	$("#est_rechazada").val('');
	$('#estados_nc').find('option:first').attr('selected', 'selected').parent('select'); //resetea el select
	$('#clasificacion').find('option:first').attr('selected', 'selected').parent('select'); //resetea el select
	$('input[name="fuente"]').attr('checked', false); //resetea radio buttons
	$('#nc_inter').attr('checked','checked'); //chequea el que se necesita
	$("#nom_afec").val('');
	$("#edad").val('');
	$("#ide").val('');
	$("#hc").val('');
	$("#desc_acc").val('');
	$("#analiz").val('');
	$('#responsables').find('option:first').attr('selected', 'selected').parent('select');
	$('#tipo_even1').find('option:first').attr('selected', 'selected').parent('select');
	$('#evento_hijo').find('option:first').attr('selected', 'selected').parent('select');
	$('input[name="clase"]' ).attr('checked', false);
	$('input[name="sev"]').attr('checked', false);
	$('input[name="prevenible"]').attr('checked', false);
	$('input[name="centinela"]').attr('checked', false);
	$('input[name="clasifique"]').attr('checked', false);
    $("#master_empresas").val('');
	$("#select_procesos_cco").hide();
	$("#select_procesos_cco").addClass('campoRequerido');

	$( "[placeholder]" ).blur(); //se coloca para que se ejecute el blur y coloque el mensaje de placeholder
}


function creANDoOptions( slCampos, opciones, validar )
{

              //opciones debe ser un array
               if( slCampos.tagName.toLowerCase() == "select" )
			   {
					opciones.reverse();

                      //BorrANDo los options anteriores
                       var numOptions = slCampos.options.length;

                       for( var i = 0; i <  numOptions; i++ )
					   {
                               slCampos.removeChild( slCampos.options[0] );
                       }

                      //agrengANDo options
                       for( var i = 0; i < opciones.length; i++ )
					   {
								//Una posicion tiene el value y el texto a mostar
								//La primera posicion es el value y la segunda es el texto
							   var arOpciones = opciones[i].split( '-' );

                               var auxOpt = document.createElement( "option" );
                               slCampos.options.add( auxOpt, 0 );

							   if( !validar ){
									auxOpt.innerHTML = arOpciones[1];
							   }
							   else{ //substring apartir del primer - hasta el final
									auxOpt.innerHTML = opciones[i].substr( arOpciones[0].length+1 );
							   }

							   auxOpt.value = arOpciones[0];

                               slCampos.options.selectedIndex = 0;
                       }
               }
}


//Funcion ppal que pinta la informacion del registro en la ventana modal.
function llenarDatos( anaCausal, cmp, nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,
					cent,anali,evento,ucon,id,resp,nivel,clasi,obs,cor,caurechazo,arOptions,accionc,
					causas,caso, claseEvento, procAsociadoCco)  //falta enviarle las variables wbasedato, datos, wemp_pmla y otra**
{
	//alert(codccd+'---'+nomccd);
	calendario('','');
	var evento = $("#tipo_even").val();
	var wemp_pmla = $('#wemp_pmla1').val();
	var wbasedato = $('#wbasedato_aux').val();
	var tabla_cco = $('#tabla_cco').val();

	//Activa los radio button de accion correctivo si o no (Esto se da para las NC, en EA se inactiva mas abajo)
	$('input[name="accion"]').prop("disabled", false);

	//Inicializar el div de la ventana modal Jonatan Lopez 17 marzo 2014
	$("#nuevo_cco_genero" ).dialog({
				show: {
				effect: "blind",
				duration: 100
				},
				hide: {
				effect: "blind",
				duration: 100
				},
				autoOpen: false,
				// maxHeight:600,
				height:'auto',
				width: 'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Seleccionar el nuevo centro de costos que generó"

				});

	//Busca cuales son los registros que se pueden listar para ser relacionados con registro seleccionado.
	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     		'registros_en_correccion',
					wemp_pmla:			wemp_pmla,
					wbasedato:			wbasedato,
					evento:				evento,
					cco:				tabla_cco

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					alert(data_json.mensaje);
					}
					else
					{

					$('#registros_relacionados').html(data_json.html);



					//Esta linea permite que el dropdownlist de registros relacionados se convierta en un dropdownlist con filtro y con multiple seleccion. Jonatan Lopez 21 Marzo 2014
					$('#registros_relacionados').multiselect({
															   position: {
																  my: 'left bottom',
																  at: 'left top'

															   },

															}).multiselectfilter();

					$('#registros_relacionados').multiselect("refresh");
					//Desactiva todos los cajones de seleccion
					$('[name=multiselect_registros_relacionados]').attr('checked',($(this).is(':checked')) ? true:false);

					//Tamaño para el seleccionador con filtro.
					$('#ui-multiselect-menu').width("700");

					}

			},
			"json"
		);

	//Imagen que muestra el dato cargando
	$('#mostrar_relacionados').html( "<img width='auto' height='auto' border='0' src='../../images/medical/cargando.gif'>" );

	//Buscar cuales registros tiene relacionado.
	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     		'mostrar_relacionados',
					wemp_pmla:			wemp_pmla,
					wbasedato:			wbasedato,
					id:					id,
					evento:				evento

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					alert(data_json.mensaje);
					}
					else
					{
						if(data_json.html != ''){
							$("#ver_relaciones").css("display", "");
							$('#mostrar_relacionados').html(data_json.html);
							}else{
							$("#ver_relaciones").css("display", "none");
							$('#mostrar_relacionados').html("");
						}

					}

			},
			"json"
		);

	//Imagen que muestra el dato cargando
	$('#select_estados').html( "<img width='auto' height='auto' border='0' src='../../images/medical/cargando.gif'>" );


	//Estados
	$.post("ingreso_nce.php",
				{
					consultaAjax:       '',
					accion:     		'select_estados',
					wemp_pmla:			wemp_pmla,
					wbasedato:			wbasedato,
					id:					id,
					evento:				evento,
					niv:				nivel,
					est:				est

				}
				,function(data_json) {


					$("#prueba").html("hodafdasdfla");

					//$("#prueba").html(data_json.sql);
					if (data_json.error == 1)
					{
					alert(data_json.mensaje);
					}
					else
					{
						if(data_json.html != ''){
							$('#select_estados').html(data_json.html);
							}else{
									alert("nada");
							}

					}

			},
			"json"
		);


	//Imprimira el proceso asociado al cco en caso de tenerlo. Jonatan Lopez 17 marzo 2014
	var procAsociadoCco = procAsociadoCco.toUpperCase();
	$('#procAsociadoCco').html(procAsociadoCco);


	obs=$.trim(obs);
	if (obs=="") //si no tiene observaciones anteriores no muestre el primer tr
	{
		$("#tr_obs_ant").css("display", "none");
	}
	else{
		$("#tr_obs_ant").css("display", "");
	}

	if (caurechazo=="")//si no tiene causas anteriores no muestre el primer tr
	{
		$("#tr_causa_ant").css("display", "none");
	}
	else{
		$("#tr_causa_ant").css("display", "");
	}


	//se deja el div tarea en blanco para que no muestre el porque rechaza despues de rechazar un evento anterior
	//buscar todos los td del tr que llego y a toda la tabla que los contiene se le pone tamaño 10
	$( "td", cmp.parentNode ).css( "fontSize", "10pt" );
	$( "td", cmp ).css( "fontSize", "14pt" );//el tr que le llego ponerle tamaño 14

	if( $("#id_Evento").val() != id )
		$( "#contenedor_eventos")[0].style.display = 'none';

	resetearCampos();
	habilitarCampos();

	if (caso=="todas")
	{
		evento=claseEvento;
	}
	else
	{
		var evento = $("#tipo_even").val();
	}


	$("#id_Evento").val(id); //se llena el campo hidden id_evento con el id del evento que estoy llenANDo los campos
	$("#ccge").val(codccg);
	$("#nivel").val(nivel);

	$("#niv_id").val(nivel);//se llenan los campos hidden de nivel y estado
	$("#est_id").val(est);
	$("#estadoA").val(est);



	//Dependiendo del estado permitira el cambio de centro de costos que genero, ademas de permitir grabar observaciones sin cambiar de estado.
	switch(est)
		{
		case '03':
			$("#img_cambiar_cco_genero_a").hide();
			$("#img_cambiar_cco_genero_b").hide();
			$("#grabar_solamente_observ").hide();
		  break;
		case '04':
			$("#img_cambiar_cco_genero_a").hide();
			$("#img_cambiar_cco_genero_b").show();
			$("#grabar_solamente_observ").show();
		  break;
		default:
			$("#img_cambiar_cco_genero_a").css( "display", "inline");
			$("#img_cambiar_cco_genero_b").css( "display", "inline");
			$("#grabar_solamente_observ").hide();

		}

	var estadoEvento = $("#estados_filtro").val();

	if (evento=="NC")
	{
		if (caso!="enviadas")
		{

				$("#navegacion_nc").css("display", "");  //mostrar divs
				$("#div_lista_re").css("display", "");
				$("#descripcion_conf").css("display", "");
				$("#fuente").css("display", "");
				$("#observaciones").css("display", "");
				$("#estados").css("display", "");
				$("#editar").css("display", "");

			  if (caso == 'todas')
				 {
					//Si el estado de la accion es correccion mostrará el seleccionador de estados y el boton de editar, sino seguiran estando ocultos. Jonatan Lopez 19 Marzo 2014
					if(estadoEvento == '04'){
					$("#estados").css("display", ""); // mostrar div estados
					$("#editar").css("display", ""); // mostrar div editar
					}else{
					$("#estados").css("display", "none");
					$("#editar").css("display", "none");
					}
				 }

				if (nivel=='1')
				{
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");


						$("#clasificacion_conf").css("display", ""); //mostrar div
						$("#unidad_genero_mostrar").css("display", "");

						//deshabilitar los campos
						deshabilitarCampos();
						$( "#clasificacion" ).attr("disabled", false);



				}

				if (nivel=='2' || nivel=='4') //si es coordinador unidad que genero
				{
					//no mostrar los datos de la persona que la creo
					$("#unidad_detecto_mostrar").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#causas").css("display", "");//se muestran las causas
					$("#link_accion").css("display", ""); //se muestra para seleccionar si tiene accion o no
					$("#clasificacion_conf").css("display", "");//la caracterizacion de problemas


					$("#clasificacion").val(clasi); //se llena la calsificacion que trae del jefe
					$("#obs_ant").val(obs); //se llena la observacion
					$("#cor").val(cor);

					//deshabilitar los campos
					deshabilitarCampos();
					$("#cor").attr("readonly", false);
					$( "#causas_nc" ).attr("readonly", false);
					if (est=='03' && nivel=='4')
					{
						$("#tratamiento_conf").css("display", "none"); //por el nivel no se ha llenado la correccion
						$("#causas").css("display", "none");//por el nivel no se han llenado las causas
						$("#link_accion").css("display", "none"); //no se ha llegado a las acciones
						$("#rechazo").css("display", ""); //se muestra el div de porque
					}
					else if(est=='03' && nivel=='2')
					{
						$("#causas").css("display", "none");
						$("#tratamiento_conf").css("display", "none");
						$("#accion_correctiva").css("display", "none");
						$("#clasificacion_conf").css("display", "none");
						$("#link_accion").css("display", "none");
						$("#rechazo").css("display", ""); //se muestra el div de porque
					}

				}

				if(nivel=='5') //se muestra a usuario control alejANDro correccion
				{
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#causas").css("display", ""); //se muestran las causas
					$("#link_accion").css("display", ""); //se muestra el link de acciones


					$("#clasificacion").val(clasi); //se llena la calsificacion que trae del jefe
					$("#obs_ant").val(obs); //se llena la observacion
					$("#cor").val(cor);

					//deshabilitar los campos
					deshabilitarCampos();


					//se resetea al selet al principio
					var slNc = document.getElementById( "causas_nc" );

					for( i = 0; i < slNc.options.length; i++ ){ //arOptions
						slNc.options[ i ].selected = false;
					}

					arOptions = arOptions.split("-");

					//para llenar el select multiple
					for( j = 0; j < arOptions.length; j++ ){

						var slNc = document.getElementById( "causas_nc" );

						for( i = 0; i < slNc.options.length; i++ ){
							if( slNc.options[ i ].value == arOptions[j] ){
								slNc.options[ i ].selected = true;
								break;
							}
						}
					}
				}

				if (nivel=='6') //nivel de acciones
				{
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#causas").css("display", "");//se muestran las causas
					$("#link_accion").css("display", ""); //mostrar si tiene que hacer accion correctiva
					$("#accion_correctiva").css("display", "");


					$("#clasificacion").val(clasi); //se llena la calsificacion que trae del jefe
					$("#obs_ant").val(obs); //se llena la observacion
					$("#cor").val(cor);

					//deshabilitar los campos
					deshabilitarCampos();


					//se resetea al selet al principio
					var slNc = document.getElementById( "causas_nc" );

					for( i = 0; i < slNc.options.length; i++ ){
						slNc.options[ i ].selected = false;
					}

					arOptions = arOptions.split( '-' );

					//para llenar el select multiple
					for( j = 0; j < arOptions.length; j++ ){

						var slNc = document.getElementById( "causas_nc" );

						for( i = 0; i < slNc.options.length; i++ ){
							if( slNc.options[ i ].value == arOptions[j] ){
								slNc.options[ i ].selected = true;
								break;
							}
						}
					}
				}

				if (nivel=='3') //si es coordinador unidad que genero
				{
					$("#uni_detecto").css("display", "");
					$("#rechazo").css("display", ""); //se muestra el div de porque
					$("#unidad_genero_mostrar").css("display", "");

					$("#clasificacion").val(clasi); //se llena la calsificacion que trae del jefe
					$("#obs_ant").val(obs); //se llena la observacion
					$("#cor").val(cor); //se llena la correccion
					$("#caurechazo_ant").val(caurechazo); //se llena porque rechazo probar si colocANDolo al principio se llena para todos

					//deshabilitar los campos
					deshabilitarCampos();
					if (est=='03')
					{
						$("#tratamiento_conf").css("display", "none"); //por el nivel no se ha llenado la correccion
						$("#causas").css("display", "none");//por el nivel no se han llenado las causas
						$("#link_accion").css("display", "none"); //no se ha llegado a las acciones
					}


				}
				if (nivel=='7') //nivel cerrada
				{
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#causas").css("display", "");//se muestran las causas
					$("#link_accion").css("display", ""); //mostrar si tiene que hacer accion correctiva
					$("#clasificacion_conf").css("display", ""); //caracterizacion
					$("#estados").css("display", "none"); //estados
					$("#editar").css("display", "none"); //editar - enviar
					if (accionc=='Si')
					{
						$("#accion_correctiva").css("display", "");
					}
					else
					{
						$("#accion_correctiva").css("display", "none");
					}

					$("#clasificacion").val(clasi); //se llena la calsificacion que trae del jefe
					$("#obs_ant").val(obs); //se llena la observacion
					$("#cor").val(cor);

					//deshabilitar los campos
					deshabilitarCampos();


					//se resetea al selet al principio
					var slNc = document.getElementById( "causas_nc" );

					for( i = 0; i < slNc.options.length; i++ ){
						slNc.options[ i ].selected = false;
					}


					arOptions = arOptions.split( '-' );

					//para llenar el select multiple
					for( j = 0; j < arOptions.length; j++ ){

						var slNc = document.getElementById( "causas_nc" );

						for( i = 0; i < slNc.options.length; i++ ){
							if( slNc.options[ i ].value == arOptions[j] ){
								slNc.options[ i ].selected = true;
								break;
							}
						}
					}
				}

				if(nivel == '9')
				{
				//no mostrar los datos de la persona que la creo
					$("#unidad_detecto_mostrar").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#causas").css("display", "");//se muestran las causas
					$("#link_accion").css("display", ""); //se muestra para seleccionar si tiene accion o no
					$("#clasificacion_conf").css("display", "");//la caracterizacion de problemas


					$("#clasificacion").val(clasi); //se llena la calsificacion que trae del jefe
					$("#obs_ant").val(obs); //se llena la observacion
					$("#cor").val(cor);

					//deshabilitar los campos
					deshabilitarCampos();
					$("#cor").attr("readonly", false);
					$( "#causas_nc" ).attr("readonly", false);
					if (est=='03')
					{
						$("#tratamiento_conf").css("display", "none"); //por el nivel no se ha llenado la correccion
						$("#causas").css("display", "none");//por el nivel no se han llenado las causas
						$("#link_accion").css("display", "none"); //no se ha llegado a las acciones
						$("#rechazo").css("display", ""); //se muestra el div de porque
					}

				}


		}

		//llenar campos
		$("#nom").val(nom);
		$("#u_detecto_mostrar").val(codccd+"-"+nomccd);
		$("#ccod").val(codccd+"-"+nomccd);
		$("#car").val(cargo);
		$("#ext").val(ext);
		$("#ema").val(email);
		$("#fechai").val(fechad);
		$("#hora").val(horad);
		$("#u_genero").val(codccg+"-"+nomccg);
		$("#u_genero_mostrar").val(codccg+"-"+nomccg);
		$("#ccos_mostrar").val(codccg);
		$("#desc").val(desc);
		$("#estados_nc").val(est);

		$("#obs_ant").val(obs);
		$("#clasificacion").val(clasi);	//caracterizacion
		$("#caurechazo_ant").val(caurechazo);
		$("#cor").val(cor);	 //correccion

		//Fuente
		//Selecion la fuente de forma dinamica segun el dato que viene en la funcion.
		$.post("ingreso_nce.php",
					{
						consultaAjax:       '',
						accion:     		'fuente_registrada',
						wemp_pmla:			wemp_pmla,
						wbasedato:			wbasedato,
						id:					id,
						fuente_reg:			fuente

					}
					,function(data_json) {

						if (data_json.error == 1)
						{
							alert(data_json.mensaje);
						}
						else
						{
							$('#'+data_json.cod_fuente).attr('checked','checked');

						}

				},
				"json"
			);


		//si nececita accion
		if (accionc == "Si")
		{
			$('#acc_si').attr('checked','checked');
		}
		else if (accionc == "No")
		{
			$('#acc_no').attr('checked','checked');
		}

		//alert(accionc);
		/**************** Se hace la parte de enviadas *******************/
		if (caso=="enviadas")
		{
			deshabilitarCampos(); //se le pone obs solo cuANDo se necesite para no ponerlo en la funcion
			$("#obs_ant").attr("readonly", true);
			//se resetea al selet al principio
					var slNc = document.getElementById( "causas_nc" );

					for( i = 0; i < slNc.options.length; i++ ){
						slNc.options[ i ].selected = false;
					}

					arOptions = String(arOptions).split("-");

			//para llenar el select multiple
					for( j = 0; j < arOptions.length; j++ ){

						var slNc = document.getElementById( "causas_nc" );

						for( i = 0; i < slNc.options.length; i++ ){
							if( slNc.options[ i ].value == arOptions[j] ){
								slNc.options[ i ].selected = true;
								break;
							}
						}
					}


		//ya estan llenos todos los datos, se muestran divs dependiendo del estado
			$("#uni_detecto").css("display", "");
			$("#fuente").css("display", "");
			$("#descripcion_conf").css("display", "");
			$("#unidad_genero_mostrar").css("display", "");
			$("#uni_detecto").css("display", "");

			if (est==01) //pendiente aprobacion
			{
				$("#rechazo").css("display", "none");
				$("#causas").css("display", "none");
				$("#tratamiento_conf").css("display", "none");
				$("#clasificacion_conf").css("display", "none");
				$("#accion_correctiva").css("display", "none");
			}
			else if (est==02) //aprobada
			{
				$("#observaciones").css("display", "");
				$("#clasificacion_conf").css("display", "");
				$("#rechazo").css("display", "none");
				$("#causas").css("display", "none");
				$("#tratamiento_conf").css("display", "none");
				$("#accion_correctiva").css("display", "none");
				$("#clasificacion_conf").css("display", "none");
			}
			else if (est==03) //rechazada
			{
				$("#observaciones").css("display", "");
				$("#rechazo").css("display", "");
				$("#causas").css("display", "none");
				$("#tratamiento_conf").css("display", "none");
				$("#accion_correctiva").css("display", "none");
				$("#clasificacion_conf").css("display", "none");

			}
			else if (est==04) //correccion
			{
				$("#observaciones").css("display", "");
				$("#clasificacion_conf").css("display", "");
				$("#causas").css("display", "");
				$("#tratamiento_conf").css("display", "");
				$("#rechazo").css("display", "none"); //falta llenar causas
			}
			else if (est==05 || est==06) //analizada y cerrada
			{
				$("#observaciones").css("display", "");
				$("#clasificacion_conf").css("display", "");
				$("#causas").css("display", "");
				$("#tratamiento_conf").css("display", "");
				if (accionc=='Si')
				{
					$("#accion_correctiva").css("display", "");
				}
				else
				{
					$("#accion_correctiva").css("display", "none");
				}
				$("#rechazo").css("display", "none"); //falta llenar causas
			}

		}
		$("#anaCausal").val(anaCausal);

		/******************* fin enviadas ******************/
	}
	else
	{
		//eventos adversos

		//llenar campos
		$("#nom").val(nom);
		$("#u_detecto_mostrar").val(codccd+"-"+nomccd);
		$("#ccod").val(codccd+"-"+nomccd);
		$("#car").val(cargo);
		$("#ext").val(ext);
		$("#ema").val(email);
		$("#fechai").val(fechad);
		$("#hora").val(horad);
		$("#u_genero_mostrar").val(codccg+"-"+nomccg);
        $("#ccos_mostrar").val(codccg+"-"+nomccg);
		$("#desc").val(desc);
		$("#estados_nc").val(est);
		$("#nom_afec").val(npeafec);
		$("#edad").val(epeafec);
		$("#ide").val(ipeafec);
		$("#hc").val(hcpeafec);
		$("#desc_acc").val(accins);
		$("#tipo_even1").val(tipoinc); //se llena el select de tipo
		$( "#tipo_even1" )[0].onchange(); //se llama la funcion onchange para llenar el select hijo
		$("#evento_hijo").val(even); //se llena el select hijo
		$("#analiz").val(anali);
		$("#master_empresas").val(resp);
		$("#cor").val(cor);
		$("#obs_ant").val(obs);
		$("#caurechazo_ant").val(caurechazo);
		$("#centros_costo").val(nomccg);
		$("#ccos").val(codccg);



				//consulto primero la tabla
				var tb = document.getElementById( "tabla_factores" ).tBodies[0];

				if(nivel =='1') //para que muestre la primera fila para seleccionar
				{
					//Consulto las filas que se van a borrar
					while( tb.rows.length > 3 ){ //los 1 se cambiaron por 2 para que muestre el primer tr
						tb.removeChild( tb.rows[3] );
					}
				}
				else //muestre las que ya trae de la base de datos
				{
					//Consulto las filas que se van a borrar
					while( tb.rows.length > 2 ){
						tb.removeChild( tb.rows[2] );
					}
				}

                if(causas == null)
                    {
                        causas = 0;
                    }

				if (causas.length>0) //if trae algo la variable causas
				{
					//se llenan los select de factores, evento y tipo de evento
					for( var i = 0; i < causas.length; i++ ){
						addFila('tabla_factores','04910','gescal','talhuma','01','costosyp_000005'); //cambiar a las variables
					}


					for( var i = 0; i < causas.length; i++ ){
						var arCausas = causas[i].split( " " );

						$( "#"+(i+1)+"_tr_tabla_factores_fact_1" ).val( arCausas[2] );
						$( "#"+(i+1)+"_tr_tabla_factores_fact_1" )[0].onchange();
						$( "#"+(i+1)+"_tr_tabla_factores_cuales" ).val( arCausas[0] );
						$( "#"+(i+1)+"_tr_tabla_factores_indi" ).val( arCausas[1] );
						$( "#"+(i+1)+"_tr_tabla_factores_id" ).val( arCausas[3] );
					}
				}
				else //if no trae nada causas se pone en seleccione
				{

					$( "#1_tr_tabla_factores_fact_1" ).val("");
					$( "#1_tr_tabla_factores_fact_s_n" ).val("");
					$( "#1_tr_tabla_factores_cuales" ).val("");
					$( "#1_tr_tabla_factores_indi" ).val("");
					$( "#1_tr_tabla_factores_id" ).val("");
					//se ponen valores por defecto
					$('#paci').attr('checked','checked');
					$('#eventoA').attr('checked','checked');
					$('#noda').attr('checked','checked');
					$('#si_p').attr('checked','checked');
					$('#si_c').attr('checked','checked');
				}


		if (caso!="enviadas")
		{

				$("#navegacion_ea").css("display", "");  //mostrar divs
				$("#div_lista_re").css("display", "");
				$("#per_afec").css("display", "");
				$("#descripcion_conf").css("display", "");
				$("#acciones_ins").css("display", "");
				$("#factores").css("display", "");
				$("#conclusiones").css("display", "");
				$("#infeccion").css("display", "");
				$("#analizado").css("display", "");
				$("#observaciones").css("display", "");
				$("#estados").css("display", "");
				$("#editar").css("display", "");
				$("#rechazo").css("display", "none");

				if (caso == 'todas')
				 {
					//Si el estado de la accion es correccion mostrará el seleccionador de estados y el boton de editar, sino seguiran estando ocultos. Jonatan Lopez 19 Marzo 2014
					if(estadoEvento == '04'){
					$("#estados").css("display", ""); // mostrar div estados
					$("#editar").css("display", ""); // mostrar div editar
					}else{
					$("#estados").css("display", "none");
					$("#editar").css("display", "none");
					}
				 }

				if (nivel=='1')
				{
					//se llama a habilitarCampos
					habilitarCampos();
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");
					$("#unidad_genero").css("display", ""); //se muestra el div con el boton


					opc =new Array( codccg+"-"+nomccg );
					var select=document.getElementById('centros_costo');
					creANDoOptions( select, opc, true );

				}
				if (nivel=='2')
				{
					//invecla/santi/magda solo se muestra la unidad que detecto
					$("#unidad_detecto_mostrar").css("display", ""); //mostrar div unidad detecto
					$("#unidad_genero_mostrar").css("display", ""); //mostrar el div sin el boton

					//deshabilitar los campos
					deshabilitarCampos();


					//factores deshabilitados
					deshabilitarFactores(causas);
					if(est=='03')
					{
						$("#observaciones").css("display", "");
						$("#rechazo").css("display", "");
						$("#tratamiento_conf").css("display", "none");
						$("#accion_correctiva").css("display", "none");
						$("#conclusiones").css("display", "none");
						$("#infeccion").css("display", "none");
						$("#analizado").css("display", "none");
						$("#factores").css("display", "none");
					}


				}
				if ( nivel=='3' || nivel=='5')
				{
					//coordinador unidad que genero
					$("#unidad_detecto_mostrar").css("display", ""); //mostrar div unidad detecto
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#unidad_genero_mostrar").css("display", ""); //mostrar div unidad genero
					$("#link_accion").css("display", ""); //se muestra para seleccionar si tiene accion o no
					if (est=='03')
					{  //para que no muestre estos div en este estado
						$("#tratamiento_conf").css("display", "none");
						$("#link_accion").css("display", "none");
						$("#rechazo").css("display", "");
						deshabilitarFactores(causas);//para que cuANDo muestre las rechazadas
					}
					//deshabilitar los campos
					deshabilitarCampos();
					deshabilitarFactores(causas);
					$("#cor").attr("readonly", false); //se habilita el campo correccion

				}
				if (nivel=='4')
				{
				//a invecla
					$("#unidad_detecto_mostrar").css("display", "");
					$("#unidad_genero_mostrar").css("display", ""); //mostrar div unidad genero


					//deshabilitar los campos
					deshabilitarCampos();
					deshabilitarFactores(causas);
					if (est=='03')
					{
						$("#rechazo").css("display", "");
					}


				}
				if (nivel=='6')
				{
				//magda correccion
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#link_accion").css("display", ""); //se muestra el link de acciones
					$("#unidad_genero_mostrar").css("display", ""); //mostrar div unidad genero

					//deshabilitar los campos
					deshabilitarCampos();
					deshabilitarFactores(causas);



				}
				if (nivel=='7')
				{
				//magda  accion
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#link_accion").css("display", ""); //se muestra el link de acciones
					$("#accion_correctiva").css("display", ""); //lleva al formulario de acciones correctivas
					$("#unidad_genero_mostrar").css("display", ""); //mostrar div unidad genero

					//deshabilitar los campos
					deshabilitarCampos();
					deshabilitarFactores(causas); //deshabilitar los factores
					$('input[name="accion"]').attr('disabled', 'disabled');

				}
				if (nivel=='8')
				{
				//cerradas
					//mostrar los datos de la persona
					$("#uni_detecto").css("display", "");
					$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#link_accion").css("display", ""); //se muestra el link de acciones
					if (accionc=='Si') //si trae si en accion correctiva si se muestra
					{
						$("#accion_correctiva").css("display", ""); //lleva al formulario de acciones correctivas
					}
					else
					{
						$("#accion_correctiva").css("display", "none");
					}
					$("#unidad_genero_mostrar").css("display", ""); //mostrar div unidad genero
					$("#estados").css("display", "none"); //no mostrar div estados
					$("#editar").css("display", "none"); //no mostrar div editar


					//deshabilitar los campos
					deshabilitarCampos();

				}
		}

		//llenar radio buttons
		if (tpeafec == "Paciente")   //tipo persona afectada
		{
			$('#paci').attr('checked','checked');
		}
		else if (tpeafec == "Acompañante")
		{
			$('#acom').attr('checked','checked');
		}
		else if (tpeafec == "Visitante")
		{
			$('#visi').attr('checked','checked');
		}

		if (clainc == "Casi Falla")   //clase de evento
		{
			$('#falla').attr('checked','checked');
		}
		else if (clainc == "Incidente")
		{
			$('#incidente').attr('checked','checked');
		}
		else if (clainc == "Complicacion")
		{
			$('#complicacion').attr('checked','checked');
		}
		else if (clainc == "Evento Adverso")
		{
			$('#eventoA').attr('checked','checked');
		}

		if (seve == "Grave")   //severidad
		{
			$('#gra').attr('checked','checked');
		}
		else if (seve == "Moderado")
		{
			$('#mod').attr('checked','checked');
		}
		else if (seve == "Leve")
		{
			$('#lev').attr('checked','checked');
		}
		else if (seve == "No Daño")
		{
			$('#noda').attr('checked','checked');
		}

		if (prev == "Si")   //prevenible
		{
			$('#si_p').attr('checked','checked');
		}
		else if (prev == "No")
		{
			$('#no_p').attr('checked','checked');
		}

		if (cent == "Si")   //centinela
		{
			$('#si_c').attr('checked','checked');
		}
		else if (cent == "No")
		{
			$('#no_c').attr('checked','checked');
		}


		//Tipo de evento
		//Selecion el tipo de fuente de forma dinamica segun el dato que viene en la funcion.
		$.post("ingreso_nce.php",
					{
						consultaAjax:		'',
						accion:     		'dato_tipo_evento',
						wemp_pmla:			wemp_pmla,
						wbasedato:			wbasedato,
						id:					id,
						fuente_reg:			fuente


					}
					,function(data_json) {

						if (data_json.error == 1)
						{
							alert(data_json.mensaje);
						}
						else
						{
							$('#'+data_json.cod_fuente).attr('checked','checked');

						}

				},
				"json"
			);

		//si nececita accion
		if (accionc == "Si")
		{
			$('#acc_si').attr('checked','checked');
		}
		else if (accionc == "No")
		{
			$('#acc_no').attr('checked','checked');
		}

		/**************** Se hace la parte de enviadas *******************/
		if (caso=="enviadas")
		{
			deshabilitarCampos(); //se le pone obs solo cuANDo se necesite para no ponerlo en la funcion
			$("#obs_ant").attr("readonly", true);
			deshabilitarFactores(causas);

		//ya estan llenos todos los datos, se muestran divs dependiendo del estado
			$("#uni_detecto").css("display", "");
			$("#per_afec").css("display", "");
			$("#descripcion_conf").css("display", "");
			$("#unidad_genero_mostrar").css("display", "");
			$("#uni_detecto").css("display", "");
			$("#acciones_ins").css("display", "");
			$("#conclusiones").css("display", "");
			$("#infeccion").css("display", "");
			$("#analizado").css("display", "");
			$("#factores").css("display", "");

			if (est==01) //pendiente aprobacion
			{
				$("#rechazo").css("display", "none");
				$("#observaciones").css("display", "none");
				$("#tratamiento_conf").css("display", "none");
				$("#accion_correctiva").css("display", "none");
				$("#factores").css("display", "none");
				$("#conclusiones").css("display", "none");
				$("#infeccion").css("display", "none");
				$("#analizado").css("display", "none");
			}
			else if (est==02) //aprobada
			{
				$("#observaciones").css("display", "");
				$("#rechazo").css("display", "none");
				$("#tratamiento_conf").css("display", "none");
				$("#accion_correctiva").css("display", "none");
			}
			else if (est==03) //rechazada
			{
				$("#observaciones").css("display", "");
				$("#rechazo").css("display", "");
				$("#tratamiento_conf").css("display", "none");
				$("#accion_correctiva").css("display", "none");
				if (nivel =='2')
				{
					$("#conclusiones").css("display", "none");
					$("#infeccion").css("display", "none");
					$("#analizado").css("display", "none");
					$("#factores").css("display", "none");
				}
			}
			else if (est==04) //correccion
			{
				$("#observaciones").css("display", "");
				$("#tratamiento_conf").css("display", "");
				$("#rechazo").css("display", "none");
				$("#accion_correctiva").css("display", "none");
			}
			else if (est==05 || est==06) //analizada y cerrada
			{
				$("#observaciones").css("display", "");
				$("#tratamiento_conf").css("display", "");
				$("#rechazo").css("display", "none");
				if (accionc=='Si')
				{
					$("#accion_correctiva").css("display", "");
				}
				else
				{
					$("#accion_correctiva").css("display", "none");
				}
			}

		}

		/******************* fin enviadas ******************/


	}



	var select = document.getElementById( 'estados_nc' );
	//llenarEstados(select,'');
	/*poner vacios otra vez estado y nivel que son hidden que se crearon
	para hacer las validaciones de los estados que se muestran en cada nivel
	*/
	 $("#niv_id").val("");
	 $("#est_id").val("");

	mostrarOcultar( $( "#contenedor_eventos")[0] );
	if (caso!='todas')
	{
		validarCampos('',''); //para que pinte amarillos los campos obligatorios
	}

	//Inicializar el div de la ventana modal Jonatan Lopez 17 marzo 2014
	$("#contenedor_eventos" ).dialog({
				show: {
				effect: "blind",
				duration: 100
				},
				hide: {
				effect: "blind",
				duration: 100
				},
				autoOpen: false,
				// maxHeight:600,
				height:'auto',
				width: 'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Informacion de la acción"

				});

	$("#contenedor_eventos").dialog( "open" );


}

function ocultarTodo()
{
	$(".divs").hide();
}

function ocultarTd()
{
	$(".tds").each(function(){
		$(this).hide();
	});

}

function estRechazada()
{
	var tex =$("#cod_rech").val();
	var estado = $("#estados_nc").val();

	if(estado == 03)
	{
		$.post("ingreso_nce.php",
				{
					consultaAjax:   '',
					accion:         'rechazada',
					estado : 		estado,
					tex:			tex
				}
				,function(data) {
					if(data.error == 1)
					{

					}
					else
					{
						$("#estados").css("display", "");
						 if (estado == 03)
						 {
							$("#caurechazo").attr("readonly", false); //porque esta deshabilitada con la funcion deshabilitarCampos
							$("#tarea").html(data.html); // update Ok.
						 }
					}
			},
			"json"
		);
	}
	else{
		$("#tarea").html("");
	}
}

function llenar_evento(select_padre,select_hijo,key2,wbasedato,datos,wemp_pmla1,cco)
	{

		var evento = $("#tipo_even").val();
		var id_padre = $("#"+select_padre).val();
	    //select_padre: nombre del select padre
		//select_hijo: nombre del select hijo
		//id_padre: lo que seleccionaron en el select padre

	$.ajax(
	{
				url: "ingreso_nce.php",
				context: document.body,
				type: "POST",
				data:
				{
					consultaAjax:   '',
					accion:         'llenar_select_evento',
					key2:			key2,
					wbasedato:		wbasedato,
					datos:			datos,
					wemp_pmla1:      wemp_pmla1,
					cco:			cco,
					evento:         evento,
					id_padre:		id_padre
				},
				async: false,
				dataType: "json",
				success:function(data) {

				if(data.error == 1)
				{

				}
				else
				{
					$("#"+select_hijo).html(data.html); // update Ok.
					//$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				}
		}
	});
}

function llenar_hijo(id_fila_padre,id_fila_hijo,key2,wbasedato,datos,wemp_pmla1,cco)
	{

		var evento = $("#tipo_even").val();
		var id_padre = $("#"+id_fila_padre).val();
	    //id_fila_padre: nombre del select padre
		//id_fila_hijo: nombre del select hijo
		//id_padre: lo que seleccionaron en el select padre


	$.ajax(
	{
				url: "ingreso_nce.php",
				context: document.body,
				type: "POST",
				data:
				{
					consultaAjax:   '',
					accion:         'llenar_select_hijo',
					key2:			key2,
					wbasedato:		wbasedato,
					datos:			datos,
					wemp_pmla1:      wemp_pmla1,
					cco:			cco,
					evento:         evento,
					id_padre:		id_padre,
					id_fila_hijo:   id_fila_hijo
				},
				async: false,
				dataType: "json",
				success:function(data) {

				if(data.error == 1)
				{

				}
				else
				{
					$("#"+id_fila_hijo).html(data.html); // update Ok.
					//$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				}
		}

	});
}

function ocultar_mostrar(id_fila_hijo,id_fila_sn)
	{

		var valor_sn = $("#"+id_fila_sn).val();
	    //id_fila_sn: id del select si - no
		//id_fila_hijo: nombre del select hijo
		//valor_sn: lo que seleccionaron del select

		if(valor_sn=='Si')
		{
			 $("#"+id_fila_hijo).show();
		}
		else
		{
			$("#"+id_fila_hijo).val();
			$("#"+id_fila_hijo).hide();
		}

}

function abrirVentana( key,wbasedato,datos,wemp_pmla,cco )
{
	var id_evento = $( "#id_Evento" ).val(); //se le envia el id del evento
	var est = $("#estadoA").val(); //el estado del evento, se guarda en un hidden solo para su uso aqui
	var evento = $("#tipo_even").val(); //tipo evento

	var v = window.open( 'acciones.php?key='+key+'&wbasedato='+wbasedato+'&datos='+datos+'&wemp_pmla='+wemp_pmla+'&cco='+cco+'&id_evento='+id_evento+'&est='+est+'&evento='+evento+'');
}

//Selecciona elementos sin necesidad de utilizar la tecla control.
todos = new Array();
function marcar(s)
{

    cual=s.selectedIndex;
    for(y=0;y<s.options.length;y++){
    if(y==cual){
    s.options[y].selected=(todos[y]==true)?false:true;
    todos[y]=(todos[y]==true)?false:true;
    }else{
    s.options[y].selected=todos[y];
    }}
}

function evaluarEstado(wbasedato)
{
	var est = $("#estados_nc").val(); //el estado del evento
	var id_evento = $( "#id_Evento" ).val(); //se le envia el id del evento
	var evento = $("#tipo_even").val(); //tipo evento
	var nivel = $("#nivel").val(); //nivel en el que esta
	var radio_acciones = $("input:radio[name='accion']:checked").val();


	if (est=='06' || est=='05')
	{
		$.post("ingreso_nce.php",
				{
					consultaAjax:'',
					accion      :'evaluarEstado',
					est         :est,
					id_evento   :id_evento,
					evento		:evento,
					nivel		:nivel,
					wbasedato   :wbasedato,
					radio_acciones: radio_acciones
				}
				,function(data) {
					if(data.error == 1)
					{

					}
					else
					{
						alert(data.mensaje); // update Ok.

						if (data.validacion==0)
						{
							$("#estados_nc").val(""); //lo pone en seleccione
						}
						else
						{


						}
					}
			},
			"json"
		);
	}
}

/***************************************************************************************************************************
* Invocación generica del calendario para fecha inicial de suministro
****************************************************************************************************************************/
function calendario(idx,tipoProtocolo){

	  $('#fechai').datetimepicker({
			altField: "#hora",
			timeFormat: 'HH:mm:ss',
			stepHour: 1,
			stepMinute: 1,
			stepSecond: 1,
			hourText: 'Hora',
			minuteText: 'Minuto',
			secondText: 'Segundo',
			currentText: 'Ahora',
			closeText: 'Cerrar'
		});
}

function alSeleccionarFecha( date, stringFecha ){
//%Y-%m-%d a las:%H:00:00
	if( date.dateClicked ){
	   date.params.inputField.value = date.currentDate.print(date.params.ifFormat);
	   document.getElementById( "hora" ).value = date.currentDate.print( "%H:%M:00" );
	   if( date.params.inputField.onchange ){
		   date.params.inputField.onchange();
	   }
	   date.callCloseHANDler();
   }
   else{
	   date.params.inputField.ultimaFechaSeleccionada = date.currentDate;
   }
}

function mostrarOcultar( cmp ){

	$( "#"+cmp.id ).toggle(500);
}

function validarCampos(est,niv)
{
    var tipo_form_valor = $("#tipo_even").val();
    var tipo_form = '';
    if(tipo_form_valor == 'EA') { tipo_form = 'validarEA'; }
    if(tipo_form_valor == 'NC') { tipo_form = 'validarNC'; }


    var todo_ok = true;
	if (est!='03') //para validar si el estado es rechazada no valide los campos
	{
		//se agrega validacion para que si no seleccionan reponsable no envie tampoco el mensaje de la marca de agua
        if($("#centros_costo").is(':visible')  && $("#centros_costo").val() == '' || $("#centros_costo").val() == $("#centros_costo").attr("placeholder"))
            {
                $("#ccos").val('');
            }
        if($("#centros_costo").is(':visible') && $("#ccos").val() == '')
            {
                $("#centros_costo").addClass('campoRequerido');
                todo_ok = false;
            }

        if($("#u_genero_mostrar").is(':visible')  && $("#u_genero_mostrar").val() == '')
            {
                $("#ccos_mostrar").val('');
            }
        if($("#u_genero_mostrar").is(':visible') && $("#ccos_mostrar").val() == '')
            {
                $("#u_genero_mostrar").addClass('campoRequerido');
                todo_ok = false;
            }
			//se agrega validacion para que si no seleccionan reponsable no envie tampoco el mensaje de la marca de agua
			if($("#master_empresas").is(':visible')  && $("#master_empresas").val() == '' || $("#master_empresas").val() == $("#master_empresas").attr("placeholder"))
            {
                $("#master_empresas").val('');
            }
		if($("#select_procesos_cco").is(':visible') && $("#select_procesos_cco").val() == '')
            {
                $("#select_procesos_cco").addClass('campoRequerido');
                todo_ok = false;
            }

		$("#contenedor_eventos").find(":input."+tipo_form+":visible").each(function(){
			var campo_id = $(this).attr("id");
			var valor = $("#"+campo_id).val(); //alert(valor+" valor"+ campo_id+" id");

			if(valor == '' || valor == null )
			{
				todo_ok = false;
				$(this).addClass('campoRequerido');

			}
			else
			{
				$(this).removeClass('campoRequerido');
			}

		});



	}else{

		if($("#obs").val() == '')
            {
                $("#obs").addClass('campoRequerido');
                todo_ok = false;
            }

	}

    return todo_ok;

}

function revisadas(datos,key2,wemp_pmla1,wbasedato,cco)
{

		ocultarTodo();
		var evento = $("#tipo_even").val();

		if (evento=="NC")
		{
			$("#navegacion_nc").css("display", "");
		}
		else
		{
			$("#navegacion_ea").css("display", "");
		}


		$.post("ingreso_nce.php",
			{

				consultaAjax:   '',
				accion:         'lista_revisadas',
				key2:			key2,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla1:      wemp_pmla1,
				cco:			cco,
				evento:         evento

			}
			,function(data) {
				if(data.error == 1)
			{

			}
			else
			{
				$("#div_lista_re").css("display", "");
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
}

//factores deshabilitados
function deshabilitarFactores(causas)
{
	for( var i = 0; i < causas.length; i++ )
	{
		$( "#"+(i+1)+"_tr_tabla_factores_fact_1" ).attr("disabled", true);
		$( "#"+(i+1)+"_tr_tabla_factores_cuales" ).attr("disabled", true);
		$( "#"+(i+1)+"_tr_tabla_factores_indi" ).attr("disabled", true);
		$( "#"+(i+1)+"_tr_tabla_factores_fact_s_n" ).attr("disabled", true);


	}
}

//factores deshabilitados
function habilitarFactores(causas)
{ //alert("entro");
	for( var i = 0; i < causas.length; i++ )
	{
		$( "#"+(i+1)+"_tr_tabla_factores_fact_1" ).attr("disabled", false);
		$( "#"+(i+1)+"_tr_tabla_factores_cuales" ).attr("disabled", false);
		$( "#"+(i+1)+"_tr_tabla_factores_indi" ).attr("disabled", false);
		$( "#"+(i+1)+"_tr_tabla_factores_fact_s_n" ).attr("disabled", false);

	}
}



</script>
<style type="text/css">
    .efecto_boton
	{
		cursor:pointer;
		border-bottom: 1px solid orange;
		color:orange;
		font-weight:bold;
    }

	.class_div
	{
		overflow-x: scroll;
		overflow-y: scroll;
	}
	.j2
	{
		background-color:#00CCCC;
	}
	.campoRequerido
	{
            border: 1px orange solid;
            background-color:lightyellow;
    }
	.sel_enviadas_color
	{
		background-color:#E8EEF7;
		font-size: 10pt;
	}

	/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
	.ui-datepicker {font-size:12px;}
	/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
	.ui-datepicker-cover {
		display: none; /*sorry for IE5*/
		display/**/: block; /*sorry for IE5*/
		position: absolute; /*must have*/
		z-index: -1; /*must have*/
		filter: mask(); /*must have*/
		top: -4px; /*must have*/
		left: -4px; /*must have*/
		width: 200px; /*must have*/
		height: 200px; /*must have*/
	}


    </style>
    <link href="../../../include/root/estilo_menu_horizontal.css" rel="stylesheet">
</head>
<body>
<?php
/*======================================================DOCUMENTACION APLICACION==========================================================================

APLICACION PARA LA GESTION DE LA CALIDAD

1. DESCRIPCION:
Este software se crea para la unidad desarrollo organizacional con el fin de sistematizar el ingreso de las no conformidades
y eventos adversos presentados en las unidades de la clinica, las no conformidades y los eventos adversos son eventos que ocurren entre las
unidades o que afectan a los pacientes, entre los cuales se podrian mencionar como no conformidades mala cadena de frio, mala rotulacion, entre
los eventos adversos se puede tener la caida de un paciente, entre otros.

Este formulario permite el ingreso de los datos de la no conformidad y de los eventos adversos, datos como, unidad que detecto, persona que detecto,
unidad que genero, fecha, hora, descripcion, fuente de la nc, estado, este mismo formulario se le muestra al coordinador de la unidad que detecto,
para que el de la aprobacion o el rechazo, si se rechaza el proceso queda cerrado, en caso contrario sera enviada a la unidad que la genero, hay un
campo para observaciones, tambien este podra modificar la descripcion de la no conformidad de ser necesario.
//***************************************************************************
// 2019-04-15 Jessica Madrid Mejía:	Se agrega utf8_encode() en la descripción del cargo para solucionar problema de tildes que 
// 									dañaba la respuesta ajax y por tal motivo no se mostraban los resultados de las consultas.
//***************************************************************************
//2018-07-24 camilo zapata: se modifica la consulta de opciones en eventos adversos para que incluya los permisos especiales asignados a través de la tabla _000022 para los usuarios que no son coordinadores pero pueden ver la pestaña de reporte de eventos.
//ABRIL 05 de 2017 camilo zapata
* se incluye el campo para analisis detallado de causas, en la tabla 101 y en la interfaz de usuario.
//Marzo 30 de 2016 Felipe Alvarez
* Se incluye desde el inicio el comun  , esto obliga a que todas las funciones llamadas por ajax deban de llevar el parametro consultaAjax = ''
//*
//***************************************************************************
//Marzo 30 de 2016 Felipe Alvarez
* Se cambia la logica para que los eventos adversos vayan a la tabla de usuarios de matrix si no estan en talhuma_000013 , esto con la finalidad de permitir
* a los medicos , que no estan en nomina puedan llenar los eventos adversos
//*
//***************************************************************************
//Mayo 12 de 2015 Jonatan
* Se agregan Fuentes de forma automatica en el formulario de NC, en el de EA se agregan tipos de eventos de forma automatica, la forma automatica es
* desde las tablas gescal_000014 para NC y gescal_000015 para EA, ademas se agrega el filtro de centro de costos que genero y las fuentes.
//***************************************************************************
* Mayo 6 de 2015 Jonatan
* Se muestran en el resporte de listar todas 2 columnas, persona que genero el evento y el jefe que debe aprobar el evento,
* ademas tooltips para cada columna.
//***************************************************************************
* Febrero 18 de 2015 Jonatan
* Se valida que no pueda rechazar no conformidades si no ha ingresado observaciones o justificacion.
//***************************************************************************
* Octubre 02 de 2014 Jonatan
* Se crea un arreglo con los coordinadores para poder extraer el nombre y mostrarlo en pantalla en la opcion de consultar todas.
//***************************************************************************
* Septiembre 10 de 2014 Jonatan
* Se agrega el control del perfil para Coordinador Medicamentos, Coordinador Eventos Infecciosos, Coordiandor Eventos No Infecciosos
//***************************************************************************
//***************************************************************************
* Agosto 12 de 2014 Jonatan
* Se agrega el filtro de tipo de estado para verificar si el registro se puede cerrar (campo estcie(Estado de cierre) root_000093), si esta en on
* el evento o la no conformidad se puede cerrar.
//***************************************************************************
* Mayo 8 de 2014 Jonatan
* Correccion de algunas funciones por la actualizacion de la version jquery, ademas de cambio en el plugin que maneja la fecha y hora en los formularios.
//***************************************************************************
* Mayo 5 de 2014 Jonatan
* Se asocian varias no conformidades a una desde la opcion accion correctiva.
* Se agrega rango de fechas en la opcion de consultar todas, ademas de mostrar la cantidad de registros.
* Se permite el cambio de unidad que generó en las NC y EA, solo s epuede cambiar esete cco si el registro
  esta en pendiente de aproacoion o aprobado.
* Si la unidad que genera es la misma que detecto, se marcara la fuente autocontrol de forma automatica.
* Asociar procesos a centros de costo, osea, el centro de costos 1081-SOPORTE LOGISTICO tiene relacion de camilleros, hoteleria, Aseo, el sistema permitirá
  elegir uno de estos.
* Se pueden grabar observaciones sin cambiar el estado del registro.
* Se cambia el control de menu desde la base de datos, y dependiendo del perfil.
* Se crea un reporte de acciones correctivas, ademas los segumientos a las acciones correctivas se separan, quedando registro aparte de cada una en una tabla (gescal_000013).
//***************************************************************************
* Enero 09 de 2014
* Se coloca una validacion que ejecute la consulta general de roles si existe $accion - $wbasedato y $evento Viviana Rodas
//***************************************************************************
* Diciembre 23 de 2013
* Se hacen las siguientes modificaciones en el script: Se creo una tabla de roles gescal_000007, esta tabla contiene los roles del programa
* y la tabla gescal_000004 es la union de roles con los usuarios, ya en la tabla gescal_000001 no se guardan los codigos de usuario que tienen
* rol sino que se guarda el codigo del rol, una persona puede tener varios roles, asi sin importar si una persona con rol ya no esta y llega
* otra nueva con solo agregarle el rol puede ver las opciones que tiene ese rol que le asignen, de igual forma ya no se guardan los codigos
* de los coordinadores de unidad que genero, solo se guarda el centro de costo y cuando se le va a mostrar a los
* coordinadores simplemente se consulta el codigo de dicho coordinador dependiendo del centro de costo, que se trae de talhuma_000008, asi
* si el coordinador cambia se le muestra sin problema al nuevo. Asi mismo no se guarda el codigo del jefe inmediato se consulta cuando la persona
* ingresa al sistema si es jefe y si lo es busca si sus subalternos han creado no conformidades. Viviana Rodas
*
//***************************************************************************
* Octubre 10 de 2013
* A la consulta de la tabla usuarios se le coloca que este activo, ademas cuando se crea el evento en seguridad ahora guarda el codigo del usuario Viviana Rodas
//****************************************************************************
* Octubre 09 de 2013
* Se le quita la clase validarEA y validarNC a los campos de cargo, extencion y correo para que no esten validados como obligatorios y se permita enviar los eventos sin ellos
* ademas en la lista de subalternos se coloca al lado del nombre si es coordinador Viviana Rodas
//****************************************************************************
* Septiembre 19 de 2013
* Se agrega la clase tds a primer td de cada menu, ademas en la validacion de si necesita acciones correctivas o no que sea estado diferente de rechazada.
*Se coloca validacion en la funcion evaluarEstado para que si necesita acciones correctivas no deje cerrar y si no necesita acciones
*correctivas no permita colocar en estado analizada, en los dos casos EA y NC, tambien se valida a la hora de enviar la correccion que
* haya seleccionado algo en el radio button de acciones correctivas, de igual forma se le muestra un mensaje al usuario indicandole que
*debe seleccionar. Viviana Rodas.
//*****************************************************************************
* Septiembre 17 de 2013
* Se realiza cambio en la funcion datosPersonaDetecto: se coloco una validacion si la consulta no trae resultados se activa una variable y en la funcion datosPorDefecto
* sale un mensaje de que debe llenar la caracterizacion o comunicarse con desarrollo organizacional, tambien en la funcion enviar se coloco el mismo mensaje y no le permite enviar
* si no tiene los datos bien traidos de la tabla talhuma _13 con root_79 y con costosyp_000005 que son los datos basicos.  Viviana Rodas.
//******************************************************************************
* Septiembre 13 de 2013 Viviana Rodas
* Se realizan los siguientes cambios en el script: En todas las variables que se escapan las comillas dobles, la comilla y el \
												   Se coloco la validacion de sesion al inicio del programa
												   En el array buscar_subalternos se colocaron $key y $key2 para que busque con 01 y sin 01
												   En la consulta a la tabla de roles se le coloco que Rolest = on
												   Se agrega marca de agua para los campos que utilizan el autocomplete
												   Cuando se selecciona la empresa se envia es el codigo porque el nit se repite en la tabla
//*********************************************************************************************************************
 * Agosto 21 de 2013 Jonatan Lopez
 * Se agrupan las no conformidades por los subalternos que tenga el usuario, por lo tanto podra ver todas las nc o es que hayan ingresado.
 //*********************************************************************************************************************
 //*********************************************************************************************************************
 * Agosto 15 de 2013 Jonatan Lopez
 * Se validan las no conformidades con fuente auditoria interna, para que al ser ingresadas por un coordinador no se aprueben automaticamente.
 //*********************************************************************************************************************
 *  Agosto 14 de 2013 Jonatan Lopez
 *- Se agrega validacion para que los empleados que esten registrados para autorizar nc de aud. interna puedan ver las nc recibidas
 *	y aprobarlas o rechazarlas
 *- Se restringue consulta para que los coordinadores no puedan ver no conformidades de auditoria interna, solo la pueden ver usuarios
 *	autorizados, estos usuarios estan en la tabla root_000051 con el valor AutAudInterna
 //*********************************************************************************************************************
 * Julio 31 de 2013 (Jonatan)
 * Al validar que el usuario no tenga registros en la caracterizacion (talhuma_000013), le mostrara un mensaje al usuario diciendole que
 * llene la caracterizacion.
//*********************************************************************************************************************
 * Julio 5 de 2013 Jonatan Lopez
 * Se valida que el centro de costos que genero si sea un centro de costos, ademas se corrige el campo de responsable
 * para que si ponga el valor de la base de datos.
 //*********************************************************************************************************************
 * Junio 11 de 2013 Jonatan Lopez
 * Se modifica eventos adversos para que la opcion de cerradas sea un rol, ademas se cambian a autocompletar algunos campos.
 //*********************************************************************************************************************
 * Junio 08 de 2013 Jonatan Lopez
 * Se modifican las no conformidades para que al momento de ser registradas por un coordinador o jefe, sean guardadas como activas y no tengan
 * que pasar por su jefe superior.
 //*********************************************************************************************************************


/****************************************************************************
* Funciones
*****************************************************************************/
 include_once("root/comun.php");
//Esta funcion crea un arreglo con los subordinados del usuario que se encuentra activo en el sistema hasta un tercer nivel,
//esto permite que el pueda ver las solicitudes que han realizado en la opcion "Consultar No Conformidades"
function buscar_subalternos($key2, $key)
	{

	global $conex;
	global $datos;
	global $wemp_pmla1;

	$query_sub = "Select Ajeuco
			        FROM ".$datos."_000008
			       WHERE Ajeucr = '".$key2."-".$wemp_pmla1."'";
    $res_sub = mysql_query( $query_sub ) or die( mysql_errno()." - Error en el query $query_sub - ".mysql_error() );

	$arr_sub = array($key2=>array(), $key=>array() );

	while($row_sub = mysql_fetch_array($res_sub))
    {
		$wcodigoN1 = explode("-", $row_sub['Ajeuco']);

		if($wcodigoN1[0] != '')
		{

			//Se verifica si el cod. de matrix ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($wcodigoN1[0], $arr_sub))
			{
				$arr_sub[$wcodigoN1[0]] = array();
				$arr_sub[$wemp_pmla1.$wcodigoN1[0]] = array();
			}

				 $query_sub2 = "Select Ajeuco
								FROM ".$datos."_000008
							   WHERE Ajeucr = '".$row_sub['Ajeuco']."'";
				$res_sub2 = mysql_query( $query_sub2 ) or die( mysql_errno()." - Error en el query $query_sub2 - ".mysql_error() );

				while($row_sub2 = mysql_fetch_array($res_sub2))
				{

					$wcodigoN2 = explode("-", $row_sub2['Ajeuco']);

					if($wcodigoN2[0] != '')
					{
						//Se verifica si el cod. de matrix ya se encuentra en el arreglo, si no esta lo agrega.
						if(!array_key_exists($wcodigoN2[0], $arr_sub[$wcodigoN1[0]]))
						{
							$arr_sub[$wcodigoN1[0]][$wcodigoN2[0]] = $wcodigoN2[0];
							$arr_sub[$wcodigoN1[0]][$wemp_pmla1.$wcodigoN2[0]] = $wemp_pmla1.$wcodigoN2[0];
						}

					}
				}
		}
    }

	return $arr_sub;

	}


//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Crar hiddens
	//	Descripcion:	Funcion que consulta el maestro de scripts, para luego crear un json
	//					con los resultados de la consulta y posteriormente se crea una variable hidden
	//					con dicho json.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
  function crear_hiddens()
	{

		global $conex;
        global $wcliame;
        global $cco;

        $caracteres = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'");
        $caracteres2 = array("&aacute","&eacute","&iacute","&oacute","&uacute","&Aacute","&Eacute","&Iacute","&Oacute","&Uacute","&ntilde","&Ntilde","u","U","-","&agrave","&egrave","&igrave","&ograve","&ugrave","&Agrave","&Egrave","&Igrave","&Ograve","&Ugrave","A","S"," ","");

        //Busca todas las empresas de la tabla cliame_000024, las cuales se actualizan de unix.
		$q_empresas = "SELECT Empnit, Empnom, Empcod
					     FROM ".$wcliame."_000024
					 ORDER BY Empnom";
		$res_empresas = mysql_query($q_empresas,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar scripts): ".$q_empresas." - ".mysql_error());

		$arr_empresas = array();
		while($row_empresas = mysql_fetch_array($res_empresas))
		{
			//Se verifica si el cco ya se encuentra en el arreglo, si no esta lo agrega.
            if(!array_key_exists($row_empresas['Empcod'], $arr_empresas))
            {
                $arr_empresas[$row_empresas['Empcod']] = array();
            }

            $row_empresas['Empnom'] = str_replace( $caracteres, $caracteres2, $row_empresas['Empnom'] );
            $row_empresas['Empnom'] = utf8_decode( $row_empresas['Empnom'] );
            //Aqui se forma el arreglo, con clave el servicio => codigo del cco y su INFORMACIÓN.
            $arr_empresas[$row_empresas['Empcod']] = trim($row_empresas['Empcod'])."|".trim($row_empresas['Empnom']);

		}
		/* echo count($arr_empresas);
		echo "<div>";
		echo "<pre>";
		print_r($arr_empresas);
		echo "</pre>";
		echo "<div>"; */
		echo "<input type='hidden' id='hidden_empresas' name='hidden_empresas' value='".json_encode($arr_empresas)."'>";

        if ($cco == 'costosyp_000005'){
	        $query_cco = "Select Ccocod, Cconom
				         FROM ".$cco."
				         WHERE Ccoest = 'on'
					      AND Ccosgc = 'on'
					      AND Ccoemp = '01'";
		}
		else{
		    $query_cco = "Select Ccocod, Cconom
				         FROM ".$cco."
				         WHERE Ccoest = 'on'
					      AND Ccosgc = 'on' ";
		}

        $res_cco = mysql_query( $query_cco ) or die( mysql_errno()." - Error en el query $query_cco - ".mysql_error() );

		$arr_cco = array();
		while($row_cco = mysql_fetch_array($res_cco))
		{
			//Se verifica si el cco ya se encuentra en el arreglo, si no esta lo agrega.
            if(!array_key_exists($row_cco['Ccocod'], $arr_cco))
            {
                $arr_cco[$row_cco['Ccocod']] = array();
            }

            $row_cco['Cconom'] = str_replace( $caracteres, $caracteres2, $row_cco['Cconom'] );
            $row_cco['Cconom'] = utf8_decode( $row_cco['Cconom'] );
            //Aqui se forma el arreglo, con clave el servicio => codigo del cco y su INFORMACIÓN.
            $arr_cco[$row_cco['Ccocod']] = trim($row_cco['Ccocod'])."-".trim($row_cco['Cconom']);

		}

		echo "<input type='hidden' id='hidden_cco' name='hidden_cco' value='".json_encode($arr_cco)."'>";


	}




//Funcion que muestra el select de la caracterizacion de las no conformidades
function clasificacion()
{
	global $conex;

	$sql = "Select Clacod,Clades
			  FROM root_000092
			 WHERE Clatip='NC'
			   AND Claest='on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	echo "<SELECT NAME='clasificacion' id='clasificacion' class='validarNC' onChange=''>";
	echo "<option value=''>Seleccione..</option>";
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
			{
				if( $clasificacion != trim( $rows['Clacod'] ) )
				{
					echo "<option value='".$rows['Clacod']."'>".$rows['Clades']."</option>";
				}
				else
				{
					echo "<option value='".$rows['Clacod']."' selected>".$rows['Clades']."</option>";
				}
			}
	echo"</SELECT>";
}


//funcion que muestra los datos de la persona que van a ingresar la no conformidad o el evento adverso
function datosPersonaDetecto()
{
	global $conex;
	global $datos;
	global $key2;
	global $wemp_pmla1;
	global $cco;
	global $key;
	$rows="";

	//Se seleccionan los datos del usuario
	if ($cco == 'costosyp_000005'){
		$sql = "Select Ideno1,Ideno2, Ideap1, Ideext, Ideeml, Cardes, Cconom, Ccocod
				  FROM ".$datos."_000013, root_000079, ".$cco."
				 WHERE Ideuse='".$key2."-".$wemp_pmla1."'
				   AND Ideest='on'
				   AND Ideccg=Carcod
				   AND Idecco=Ccocod
				   AND Ccoemp = '01'";
    }
    else{
    	$sql = "Select Ideno1,Ideno2, Ideap1, Ideext, Ideeml, Cardes, Cconom, Ccocod
				  FROM ".$datos."_000013, root_000079, ".$cco."
				 WHERE Ideuse='".$key2."-".$wemp_pmla1."'
				   AND Ideest='on'
				   AND Ideccg=Carcod
				   AND Idecco=Ccocod";
    }


	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	//se agrega validacion de la consulta si trae resultados o no para sacar el mensaje de que debe llenar la caracterizacion
    if ($res)
	{
		$num=mysql_num_rows($res);
		if ($num>0)
		{
			$rows = mysql_fetch_array( $res );
			//variable datos ok
			echo "<script>";
			echo "var datosUsuarioOK = 0";
			echo "</script>";
		}
		else
		{

			if($wemp_pmla1 !='01')
			{
				$wemp_pmla1='01'; // la empresa 08  no exite , en la que estan registrado los medicos en la tabla usuarios , entonces se reemplaza por 01 que es el de la clinica
			}
			$empresahce= consultarAliasPorAplicacion($conex, $wemp_pmla1, "hce");
			// Marzo 30 de 2016 Felipe Alvarez
			// se hace para traer los datos del usuario que registra el evento adverso y no esta en nomina.

			$sql = "Select Descripcion as Ideno1 , '' AS Ideno2 , '' AS Ideap1 , '' AS Ideap2 , Roldes AS Cardes , Cconom  AS Cconom, Ccocod AS Ccocod
					  FROM usuarios LEFT JOIN  costosyp_000005 ON ( Ccocod = Ccostos ) , ".$empresahce."_000020 , ".$empresahce."_000019
					 WHERE Codigo = '".$key."'
					   AND Codigo = Usucod
					   AND Usurol = Rolcod";
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			if($res)
			{
				$num=mysql_num_rows($res);
				if ($num>0)
				{
					$rows = mysql_fetch_array( $res );
					//variable datos ok
						echo "<script>";
						echo "var datosUsuarioOK = 0";
						echo "</script>";

				}
				else
				{

						echo "<script>";
						echo "var datosUsuarioOK = 1"; //error no encontro datos
						echo "</script>";

				}
			}
			else
			{
				//variable datos ok
				echo "<script>";
				echo "var datosUsuarioOK = 1"; //error no encontro datos
				echo "</script>";
			}
		}
	}
	else
	{
		//variable datos ok
		echo "<script>";
		echo "var datosUsuarioOK = 1"; //error en la consulta
		echo "</script>";
	}



	return $rows;
}

//esta funcion busca el usuario, con su respectivo jefe en la tabla talhuma_000008
function perfiles($datos,$wemp_pmla1,$key2)
{
	global $conex;
	global $datos;
	global $key2;
	global $wemp_pmla1;

	 $sql = "Select Ajeuco,Ajeucr,Ajecco
			   FROM ".$datos."_000008,".$datos."_000013
			  WHERE Ajeuco='".$key2."-".$wemp_pmla1."'
			    AND Ideuse=Ajeucr
			    AND Ideest='on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if ($num > 0)
	{
		$rows = mysql_fetch_array($res);
	}

	return $rows;
}

//Funcion que muestra el select de las entidades responsables
function responsables()
{
	global $conex;
    global $wcliame;

	$sql = "Select Empnit as nit, Empnom as Resnom
			  FROM ".$wcliame."_000024
          Group by Resnit
		  Order by Resnom";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	echo "<SELECT NAME='responsables' id='responsables' onChange='' class=''>";
	echo "<option value=''>Seleccione..</option>";
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
			{
				if( $responsables != trim( $rows['nit'] ) )
				{
					echo "<option value='".$rows['nit']."-".$rows['Resnom']."'>".$rows['Resnom']."</option>";
				}
				else
				{
					echo "<option value='".$rows['nit']."-".$rows['Resnom']."' selected>".$rows['Resnom']."</option>";
				}
			}
	echo"</SELECT>";
}

//retorna el rol de cada persona falta terminarla
function roles()
{
	global $conex;
	global $wbasedato;
	global $evento;

	$q="Select Rolcod
	      FROM ".$wbasedato."_000004
		 WHERE Roltip='".$evento."'
		   AND Roldes='Control'
		   AND Rolest='on'";
    $res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $usu="";

    for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
        {
            $usu.="-".$rows1['Rolcod']."-";
        }
	return $usu;
}
//fucion que consulta los estados para filtrar la busqueda de todos los eventos
function buscar_estados()
{
	global $conex;
	global $wbasedato;

	$sql = "Select Estcod,Estdes
			  FROM root_000093
			 WHERE Esttip='NC'
			   AND Estest='on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	echo "<SELECT NAME='estados_filtro' id='estados_filtro'  class='' onChange=''>";
	echo "<option value='%'>Todos</option>";

    for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
    {

        if( $clasificacion != trim( $rows['Estcod'] ) )
        {
            echo "<option value='".$rows['Estcod']."'>".$rows['Estdes']."</option>";
        }
        else
        {
            echo "<option value='".$rows['Estcod']."' selected>".$rows['Estdes']."</option>";
        }
    }

	echo"</SELECT>";
}

function consultarCausasNC($wbasedato,$evento,$id)
{
	global $conex;

	$sql4="select Caucod
						FROM ".$wbasedato."_000002
						WHERE Cautip='".$evento."'
						AND Caneid='".$id."'
						AND Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num4 = mysql_num_rows( $res4 );

				$opciones = '';
				if ($num4>0)
				{ //$opciones = array();
					for( $j = 0; $rows4 = mysql_fetch_array($res4); $j++ )
					{
						$opciones.= '-'.trim($rows4['Caucod']);
					}
					$opciones = substr( $opciones, 1 );
				}

return $opciones;
}

function consultarCausasEA($wbasedato,$id)
{
	global $conex;

	$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id
				FROM ".$wbasedato."_000002, root_000091,root_000094
				WHERE Caneid = '".$id."'
				AND ".$wbasedato."_000002.Caucod=root_000091.Caucod
				AND root_000091.Caucla=root_000094.Tipcod
				AND root_000091.Cautip=root_000094.Tiptip
				AND root_000094.Tipest='on'
				AND ".$wbasedato."_000002.Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				//$rows4 = mysql_fetch_array( $res4 );
				$causa=array();

				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}

	return $causa;
}


function consultarNombreCC($cco, $cc, $wemp_pmla)
{
	global $conex;
	if ($cco == 'costosyp_000005'){
		$sql1 = "Select Cconom
	                    FROM ".$cco."
	                    WHERE Ccocod='".$cc."'
	                    AND Ccoest='on'
	                    AND Ccoemp = '01' ";
    }
    else{
		$sql1 = "Select Cconom
	                    FROM ".$cco."
	                    WHERE Ccocod='".$cc."'
	                    AND Ccoest='on'";
    }

	$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
	return $res1;
}

function consultarNombrePersonaDetecto($datos,$cco,$codUsu,$wemp_pmla1,$ccDet)
{
	global $conex;

    if ($cco == 'costosyp_000005'){
		$sql2 = "Select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
	                         FROM ".$datos."_000013,root_000079, ".$cco."
	                        WHERE Ideuse='".substr($codUsu,-5)."-".$wemp_pmla1."'
	                          AND Idecco='".$ccDet."'
	                          AND Ideest='on'
	                          AND Ideccg=Carcod
	                          AND Idecco=Ccocod
	                          AND Ccoemp = '01'";
    }
    else{
    	$sql2 = "Select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
	                         FROM ".$datos."_000013,root_000079, ".$cco."
	                        WHERE Ideuse='".substr($codUsu,-5)."-".$wemp_pmla1."'
	                          AND Idecco='".$ccDet."'
	                          AND Ideest='on'
	                          AND Ideccg=Carcod
	                          AND Idecco=Ccocod";
    }

	$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
	$num = 0;
	$num = mysql_num_rows($res2);
	if ($num > 0)
	{

	}
	else
	{
		if($wemp_pmla1 =='08')
		{
			$wemp_pmla1='01';
		}
		//include_once("root/comun.php");
		$empresahce= consultarAliasPorAplicacion($conex, $wemp_pmla1, "hce");
		//$empresahce= consultarAliasPorAplicacion($conex, "01", "hce");
		// Marzo 30 de 2016 Felipe Alvarez
		// se hace para traer los datos del usuario que registra el evento adverso y no esta en nomina.
		$sql2 = "   SELECT Descripcion as Ideno1 , '' AS Ideno2 , '' AS Ideap1 , '' AS Ideap2 , Roldes AS Cardes , Cconom  AS Cconom, Ccocod AS Ccocod
					  FROM usuarios LEFT JOIN  costosyp_000005 ON ( Ccocod = Ccostos ) , ".$empresahce."_000020 , ".$empresahce."_000019
					 WHERE Codigo = '".$codUsu."'
					   AND Codigo = Usucod
					   AND Usurol = Rolcod
					   AND Ccoemp = '01'";
		$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );

	}

return $res2;
}

function consultarEstadoEvento($est)
{
global $conex;
	$sql5="select Estdes,Estcol
			FROM root_000093
			WHERE Estcod='".$est."'
			AND Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
return $res5;
}

//Muestra la informacion de los registros relacionados a la no conformidad o evento adverso.
function mostrar_datos_relacionado($wbasedato, $id)
{

	global $conex;

	$sql = " SELECT *
			   FROM ".$wbasedato."_000001
			  WHERE id = '".$id."'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	while($row = mysql_fetch_array($res)){

		$winformacion .= "<div class=fila1 style='display: table; border-style: solid; border-width: 2px; width: 100%; '>Número: ".$row['Ncenum']."</div>";
		$winformacion .= "<div class=fila1 style='display: table; border-style: solid; border-width: 2px; width: 100%; '>Fecha del evento: ".$row['Ncefed']."</div>";
		$winformacion .= "<div class=fila1 style='display: table; border-style: solid; border-width: 2px; width: 100%; '>Centro de costos que detectó: ".$row['Nceccd']."</div>";
		$winformacion .= "<div class=fila2 style='display: table; border-style: solid; border-width: 2px; width: 100%; '>Centro de costos que generó: ".$row['Nceccg']."</div>";
		$winformacion .= "<div style='display: table; border-style: solid; border-width: 2px; width: 100%; '>".$row['Ncedes']."</div>";

	}

	return utf8_encode($winformacion);
}

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/
//=======================================================================================================
include_once("root/comun.php");
//




$conex = obtenerConexionBD("matrix");

$wactualiz="2018-07-24";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wentidad = $institucion->nombre;

$datos= consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
$cco= consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "gescal");
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
$westadocoor = consultarAliasPorAplicacion($conex, $wemp_pmla, "EstadoNCcoordinador");
$wnivelcoord = consultarAliasPorAplicacion($conex, $wemp_pmla, "NivelNCcoordinador");
$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$user_aut_aud_inter_nc = consultarAliasPorAplicacion($conex, $wemp_pmla, "AutAudInterna");
$estado_relacionadas = consultarAliasPorAplicacion($conex, $wemp_pmla, "estadorelacion");
//Crear hidden para la lectura por jquery y poder activar los autocomplete
crear_hiddens();
// $key = substr($user, 2, strlen($user)); //se eliminan los dos primeros digitos
//echo "<br>wemp_pmla primero".$wemp_pmla;
// validar si es numero is_numeric dejar los ultimos 5 y si no se envia todo
if(is_numeric($key))
{
	if(strlen($key) == 7 AND substr($key, 2) != $wemp_pmla)
	{

		$wemp_pmla1=(substr($key, 0,2)); //el wemp_pmla son los dos primeros digitos
	    $key2 = substr( $key, -5 );
	}
	else
	{
		$wemp_pmla1=$wemp_pmla;
		$key2 = substr( $key, -5 );
	}

}
else
{
	$key2=$key;
	$wemp_pmla1=$wemp_pmla;
}

$conex = obtenerConexionBD("matrix");
$wfec=date("Y-m-d");

session_start();
//el usuario se encuentra registrado
if(!isset($_SESSION['user']))
    echo "error";
else
{

if(!isset($accion))
{
   echo '<!DOCTYPE html>';
}


encabezado("NO CONFORMIDADES - EVENTOS ADVERSOS ",$wactualiz, "".$wbasedato1);

//Consulto si el usuario es coordinador, en ese caso en el campo oculto rol_coor tendra on o vacio, esto permitira controlar los estados
//de las no conformidades de este usuario, dejANDolas activas automaticamente, o sea, sin necesidad de aprobacion.
$sql_coor = "  SELECT Ajecoo
                    FROM ".$datos."_000008
                WHERE Ajeucr = '".$key2."-".$wemp_pmla."'
                    AND Ajecoo = 'on' LIMIT 1";
$res_coor = mysql_query($sql_coor, $conex) or die( mysql_errno()." - Error en el query $sql_coor - ".mysql_error());
$row_coor = mysql_fetch_array($res_coor);

$sql_tipoaccion = "  SELECT Concod, Condes
                       FROM ".$wbasedato."_000006
                      WHERE Conest = 'on'";
$res_tipoaccion = mysql_query($sql_tipoaccion, $conex) or die( mysql_errno()." - Error en el query $sql_coor - ".mysql_error());


echo"<FORM METHOD='POST' ACTION='' >";
		echo"<div id='seleccion'>";
			echo "<input type=hidden id='wemp_pmla1' value='".$wemp_pmla1."'>";
			echo "<input type=hidden id='wbasedato_aux' value='".$wbasedato."'>";
			echo "<input type=hidden id='tabla_cco' value='".$cco."'>";
			echo "<input type=hidden id='estado_relacionadas' value='".$estado_relacionadas."'>";
			echo"<table align='center'>";
				echo"<th colspan='2' class='encabezadotabla'>TIPO DE EVENTO</th>";
				echo"<tr>";
				echo"<td class='fila1'>Seleccione</td>";
				echo"<td class='fila2'>";
					echo"<SELECT NAME='tipo_even' id='tipo_even' onchange=\"seleccion('$datos','$key2','$wemp_pmla1','$wbasedato','$cco','$wemp_pmla', '$user_aut_aud_inter_nc');\">";
					echo"<OPTION VALUE='seleccione'>Seleccione...</OPTION>";

					while($row_tipoaccion = mysql_fetch_array($res_tipoaccion)){
					echo"<OPTION VALUE='".$row_tipoaccion['Concod']."'>".$row_tipoaccion['Condes']."</OPTION>";
					}

					echo'</SELECT>';
				echo"</td>";
				echo'<tr>';
			echo'</table>';
		echo"</div>";

        //Menu para No Conformidades
        echo "<div id='navegacion_nc' class='divs' width='' style='display:none'>";
        echo "<table align='center' width='auto' id='tabla_navegacion_nc' >";
            echo "<tr>";
            echo "<td>";
            echo "<div id='cssmenu'>
                    <ul>
                    <li class='active tds' id='td_nc_ingresar' onClick='cambiarColor( this.id );'><a href='#' onClick='formu_ingresar_nc(\"".$row_coor['Ajecoo']."\", \"No Conformidad\"); return false;'><span>Ingresar <br> No Conformidades</span></a></li>
                    <li id='td_nc_enviadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_enviadas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco','menu','enviados','div_lista_re'); return false;\"><span>Consultar <br>No Conformidades</span></a></li>
                    <li id='td_nc_recibidas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco', '$user_aut_aud_inter_nc'); return false;\"><span>Pendientes <br>Por Aprobar</span></a></li>
                    <li id='td_nc_recibidas_u_genero' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_u_genero('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Recibidas <br> Unidad que Generó</span></a></li>
                    <li id='td_nc_correccion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_correccion('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>En Corrección</span></a></li>
                    <li id='td_nc_rechazadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_rechazadas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Rechazadas</span></a></li>
                    <li id='td_nc_accion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_accion('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>En Acción<br>Correctiva</span></a></li>
                    <li id='td_nc_cerradas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_cerradas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Cerradas</span></a></li>
                    <li id='td_nc_todas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"mostrar_filtro('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Consultar Todas</span></a></li>
                    </ul>
                    </div>";
            echo "</div>";
            echo "<td>";
            echo "<tr>";
        echo"</table>";
        echo "</div>";

        //Menu para eventos adversos
        echo "<div id='navegacion_ea' class='divs' width='' style='display:none'>";
        echo "<table align='center' width='auto' id='tabla_navegacion_ea' >";
            echo "<tr>";
            echo "<td>";
            echo "<div id='cssmenu'>
                    <ul>
                    <li id='td_ea_ingresar' class='tds' onClick='cambiarColor( this.id );' ><a href='#' onClick='formu_ingresar_ea(\"".$row_coor['Ajecoo']."\",\"Evento Adverso\"); return false;'><span>Ingresar<br>Evento Adverso</span></a></li>
                    <li id='td_ea_enviadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_enviadas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco','menu','enviados','div_lista_re'); return false;\"><span>Consultar <br>Eventos Adversos</span></a></li>
                    <li id='td_ea_recibidas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Pendientes <br>Por Aprobar</span></a></li>
                    <li id='td_ea_recibidas_u_genero' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_u_genero('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Recibidos <br> Unidad Genero</span></a></li>
                    <li id='td_ea_recibidas_u_control' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_u_control('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Recibidos<br>Unidad Control</span></a></li>
                    <li id='td_ea_correccion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_correccion('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>En Corrección</span></a></li>
                    <li id='td_ea_rechazadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_rechazadas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Rechazados</span></a></li>
                    <li id='td_ea_accion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_accion('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>En Acción<br>Correctiva</span></a></li>
                    <li id='td_ea_cerradas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_cerradas('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Cerradas</span></a></li>
                    <li id='td_ea_todas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"mostrar_filtro('$datos','$key2','$wemp_pmla1','$wbasedato','$cco'); return false;\"><span>Consultar Todas</span></a></li>
                    </ul>
                    </div>";
            echo "</div>";
            echo "<td>";
            echo "<tr>";
        echo"</table>";
        echo "</div>";
		echo"<br>";

		echo"<div id='filtros' class='divs' style='display:none;'>";
				echo"<table align='center' width='900'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6' align='center'>Filtros para la busqueda</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='200' class='fila1'>Centro de costo que detectó:</td>";
						echo"<td class='fila2'>
						<INPUT type='text' name='centro_costo' id='centro_costo' class='' size='30'>
								<button type='button' onClick=\"buscarCco('$cco','todos');\">Buscar</button>";
							echo"<SELECT NAME='ccos_filtro' id='ccos_filtro' onChange='activar_select_clases();'>";
							//echo"<OPTION VALUE=''>Seleccione..</OPTION>";
							echo"<OPTION VALUE='%'>Todos</OPTION>";
							echo"</SELECT>";
						echo"</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='150' class='fila1'>Centro de costo que generó:</td>";
						echo"<td class='fila2'>
						<INPUT type='text' name='centro_costo' id='centro_costoG' class='' size='30'>
								<button type='button' onClick=\"buscarCcoG('$cco','todos');\">Buscar</button>";
							echo"<SELECT NAME='ccos_filtroG' id='ccos_filtroG' onChange='activar_select_clases();'>";
							//echo"<OPTION VALUE=''>Seleccione..</OPTION>";
							echo"<OPTION VALUE='%'>Todos</OPTION>";
							echo"</SELECT>";
						echo"</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='100' class='fila1'>Clase de evento:</td>";
						echo"<td class='fila2'>";
						echo"<SELECT NAME='clases' id='clases' disabled='disabled'>";
							echo"<OPTION VALUE='%'>Todos</OPTION>";
							echo"<OPTION VALUE='generado'>Generado</OPTION>";
							echo"<OPTION VALUE='detectado'>Detectado</OPTION>";
							echo"</SELECT>";
						echo"</td>";
					echo"<tr>";
						echo"<td align='left' width='100' class='fila1'>Estado:</td>";
						echo"<td class='fila2'>";
						buscar_estados();
						echo"</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='100' class='fila1'>Tipo de evento:</td>";
						echo"<td class='fila2'>";

						$sql_tipoaccion = "  SELECT Concod, Condes
											   FROM ".$wbasedato."_000006
											  WHERE Conest = 'on'";
						$res_tipoaccion = mysql_query($sql_tipoaccion, $conex) or die( mysql_errno()." - Error en el query $sql_coor - ".mysql_error());

						echo"<SELECT NAME='tipos' id='tipos'>";
							echo"<OPTION VALUE='%'>Todos</OPTION>";

							while($row_tipoaccion = mysql_fetch_array($res_tipoaccion)){
							echo"<OPTION VALUE='".$row_tipoaccion['Concod']."'>".$row_tipoaccion['Condes']."</OPTION>";
							}

							echo"</SELECT>";
						echo"</td>";
					echo"</tr>";

					echo"<tr>";
						echo"<td align='left' width='100' class='fila1'>Fuente:</td>";
						echo"<td class='fila2'>";

						//Fuentes de NC y EA
						$sql_fuente = "  SELECT Fueval as valor, Fuedes as descripcion
										   FROM ".$wbasedato."_000014
										  UNION
										 SELECT Tieval as valor, Tiedes as descripcion
										   FROM ".$wbasedato."_000015";
						$res_fuente = mysql_query($sql_fuente, $conex) or die( mysql_errno()." - Error en el query $sql_fuente - ".mysql_error());

						echo"<SELECT NAME='fuentes' id='fuentes'>";
							echo"<OPTION VALUE='%'>Todos</OPTION>";

							while($row_fuente = mysql_fetch_array($res_fuente)){
							echo"<OPTION VALUE='".$row_fuente['valor']."'>".$row_fuente['descripcion']."</OPTION>";
							}

							echo"</SELECT>";
						echo"</td>";
					echo"</tr>";

					//Marzo 12 de 2014 Jonatan Lopez
					echo "<tr>";
						echo "<td class='fila1'>";
						echo "Fecha Inicial";
						echo "</td>";
						echo "<td class='fila2'>";
						echo "<input type='text' id='wfec_i' name='wfec_i' value='".date("Y-m-d")."' />";
						echo "</td>";
						echo "</tr>";
					echo "<tr>";
						echo "<td class='fila1'>";
						echo "Fecha Final";
						echo "</td>";
						echo "<td class='fila2'>";
						echo "<input type='text' id='wfec_f' name='wfec_i' value='".date("Y-m-d")."' />";
						echo "</td>";
						echo "</tr>";
					echo "<tr>";
						echo"<td align='center' width='' colspan='2' class='fila2'>
						<button type='button' onClick=\"nc_todas('$key2','$wbasedato','$datos','$wemp_pmla1','$cco');\">Enviar</button>
						</td>";
					echo"</tr>";
				echo"</table>";
				echo"<br>";
			echo"</div>";

		echo "<div id='div_lista_re' class='divs'></div>";


        echo "<div id='contenedor_eventos' style='display:none;'>";
			$infor=array();
			$infor=datosPersonaDetecto();
            $whora =(string)date("H:i:s");
			echo "<INPUT type='hidden' id='niv_id' value=''>";
			echo "<INPUT type='hidden' id='est_id' value=''>";
			echo "<div id='uni_detecto' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>INFORMACIÓN UNIDAD QUE DETECTÓ</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='180' class='fila1'>Persona que detectó</td>";
						echo"<td align='left' width='150' class='fila2'><INPUT type='text' name='nom' id='nom' class='validarEA validarNC' title='pruebas' size='40' value='".$infor['Ideno1']." ".$infor['Ideno2']." ".$infor['Ideap1']."'></td>";
						echo"<td class='fila1'>Unidad que detectó</td>";
						if($infor['Ccocod']=='')
						{
							$infor['Ccocod']='ninguno';
							$infor['Cconom']='ninguno';

						}
						echo"<td class='fila2' colspan='3'><INPUT type='text' name='ccod' id='ccod' class='validarEA validarNC' size='50' value='".$infor['Ccocod']."-".$infor['Cconom']."'></div></td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td class='fila1'>Cargo</td><td class='fila2'><INPUT type='text' name='car' id='car' class='' size='40' value='".$infor['Cardes']."'></td>";
						echo"<td class='fila1'>Extensión</td><td class='fila2'><INPUT type='text' name='ext' id='ext' class='' size='10' value='".$infor['Ideext']."'></td>";
						echo"<td class='fila1'>Email</td><td class='fila2'><INPUT type='text' name='ema' id='ema' class='' size='40' value='".$infor['Ideeml']."'></td>";
					echo"</tr>";
					echo"</tr>";
					echo"<tr>";
						echo"<td width='200' class='fila1'>Fecha y Hora en que ocurrio el evento</td>";
						echo"<td class='fila2' colspan='5'><INPUT type='text' name='fechai' id='fechai' class='validarEA validarNC'>";
						echo"&nbsp<INPUT type='text' name='hora' id='hora' class='validarEA validarNC'>";

					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo "<script>";
			echo "var datosUsuario = ".json_encode($infor);
			echo "</script>";

			echo"<div id='unidad_detecto_mostrar' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6' >INFORMACIÓN UNIDAD QUE DETECTÓ</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td class='fila1' width='200'>Unidad que detecto</td><td class='fila2'><INPUT type='text' name='u_detecto_mostrar' id='u_detecto_mostrar' class='validarEA validarNC' size='50'>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo "<div id='per_afec' class='divs'>";
				echo"<table align='center' width='1100' border='0'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='10'>INFORMACIÓN PERSONA AFECTADA</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td class='fila1' width='82'>Clasifique: </td>";
						echo"<td align='center' width='100' class='fila2'>Paciente:<INPUT type='radio' name='clasifique' id='paci' value='Paciente' checked>";
						echo"<td align='center' width='250' class='fila2'>Acompañante:<INPUT type='radio' name='clasifique' id='acom' value='Acompañante'>";
						echo"<td align='center' width='100' class='fila2' colspan='2'>Visitante:<INPUT type='radio' name='clasifique' id='visi' value='Visitante'>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='200' class='fila1'>Nombre</td>";
						echo"<td align='left' width='150' class='fila2'><INPUT type='text' name='nom_afec' id='nom_afec' class='' title='pruebas' size='40' value=''></td>";
						echo"<td class='fila2' colspan='3'><table width='100%'><tr><td class='fila1' width='50'>Edad</td>";
						echo"<td class='fila2'><INPUT type='text' name='edad' id='edad' class='' size='10' value=''> &nbsp; &nbsp; </td>";
						echo"<td class='fila1' width='150'>Identificacion</td><td class='fila2'><INPUT type='text' name='ide' id='ide' value='' class='validarEA'></td></tr></table>";
						echo"</td>";
						echo"</tr>";
					echo"<tr>";
						echo"<td class='fila1' width='200'>Entidad Responsable</td><td class='fila2'>";
                        echo "<input type='text' size=60 name='master_empresas' id='master_empresas' placeholder='Digite la entidad y seleccionela de la lista'>";
						echo"</td>";
						echo"<td colspan='3'><table width='100%'><tr><td class='fila1' width='100'>Historia Clinica</td><td class='fila2'><INPUT type='text' name='hc' id='hc' class='' value=''></td></tr></table></td>";

					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='fuente' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";

					//Maestro de fuentes.
					$sql_fuentes = "  SELECT Fuenid, Fuedes, Fueval
										FROM ".$wbasedato."_000014
									   WHERE Fueest = 'on'";
					$res_fuentes = mysql_query($sql_fuentes, $conex) or die( mysql_errno()." - Error en el query $sql_fuentes - ".mysql_error());
					$num_fuentes = mysql_num_rows($res_fuentes);

					echo"<td class='encabezadotabla' colspan='".($num_fuentes+1)."'>Fuente</td>";
					echo"</tr>";
						echo"<tr>";
						echo"<td class='fila1'>";
								echo"<table border='0'>"; //tabla interna para fuente
									echo"<tr>";
										echo"<td'>Fuente </td>";
									echo"</tr>";
								echo"</table>";
							echo"</td>";

						while($row_fuentes = mysql_fetch_array($res_fuentes)){

							echo"<td class='fila2'>";
								echo"<table border='0'>";
									echo"<tr>";
										echo"<td align='right' width='100'>".$row_fuentes['Fuedes']."</td><td align='left'><INPUT type='radio' name='fuente' onclick='marcar_fuente_autocontrol(\"\", \"desdechecked\");' id='".$row_fuentes['Fuenid']."' value='".$row_fuentes['Fueval']."'>";
									echo"</tr>";
								echo"</table>";
							echo"</td>";


						}

					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='unidad_genero' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6' >INFORMACIÓN UNIDAD QUE GENERÓ</td>";
					echo"</tr>";
					echo"<tr>"; //se coloca placeholder para sacar un mensaje de marca de agua
						echo"<td class='fila1 mensajeevento' width='200'>Causante del evento</td><td class='fila2'><INPUT type='text' name='centros_costo' id='centros_costo' size='60' placeholder='Digite la unidad o el centro de costo y seleccionelo de la lista'><INPUT type='hidden' name='ccos' id='ccos' class='validarEA validarNC' size='30'>&nbsp;&nbsp;&nbsp;&nbsp;<div style='display:inline;' id='procesos_cco'></div><div style='display:inline;' id='img_cambiar_cco_genero_a'><img width='15' height='15' border='0' onclick='abrir_modal_cco_genero();' title='Cambiar unidad que generó' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></div>";

						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";


			echo"<div id='unidad_genero_mostrar' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6' >INFORMACIÓN UNIDAD QUE GENERÓ</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td class='fila1 mensajeevento' width='200'>Causante del evento</td><td class='fila2'><INPUT type='text' name='u_genero_mostrar' id='u_genero_mostrar' class='validarEA validarNC' size='70'><INPUT type='hidden' name='ccos_mostrar' id='ccos_mostrar'class='validarEA validarNC' size='30'>&nbsp;&nbsp;&nbsp;&nbsp;<div style='display:inline; font-weight:bold;' id='procAsociadoCco'></div>&nbsp;&nbsp;&nbsp;&nbsp;<div style='display:inline;' id='img_cambiar_cco_genero_b'><img width='15' height='15' border='0' onclick='abrir_modal_cco_genero();' title='Cambiar unidad que generó' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></div>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";


			echo"<div id='descripcion_conf' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>DESCRIPCIÓN DEL EVENTO</td>";
					echo"</tr>";
						echo"<td align='left' width='100' class='fila1'>Descripción:</td><td class='fila2'><TEXTAREA NAME='desc' id='desc' class='validarEA validarNC' ROWS=4 COLS=120></TEXTAREA></td>";
					echo"</tr>";
				echo"</table>";
				echo "<INPUT type='hidden' name='id_Evento' id='id_Evento' value=''>"; //se crea el hidden para utilizarlos en la funcion editar
				echo "<INPUT type='hidden' name='ccge' id='ccge' value=''>";
				echo "<INPUT type='hidden' name='nivel' id='nivel' value=''>";
			echo"</div>";

			echo"<div id='acciones_ins' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>ACCIONES INSEGURAS</td>";
					echo"</tr>";
						echo"<td align='left' width='100' class='fila1'>Descripción:</td><td class='fila2'><TEXTAREA NAME='desc_acc' id='desc_acc' ROWS=4 COLS=120></TEXTAREA></td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";


			echo"<div id='clasificacion_conf' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>CLASIFICACIÓN DE LA NO CONFORMIDAD</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>Caracterización del problema</td>";
					echo"<td class='fila2'>";
					clasificacion();
					echo"</td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";


			echo"<div id='tratamiento_conf' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>TRATAMIENTO DEL EVENTO - CORRECCION</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>Correccion</td>";
					echo"<td class='fila2'><TEXTAREA name='cor' id='cor' class='validarEA validarNC' rows='4' cols='120'></TEXTAREA></td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";


			echo"<div id='observaciones' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>OBSERVACIONES</td>";
				echo"</tr>";
				echo"<tr id='tr_obs_ant'>";
					echo"<td align='left' width='100' class='fila1'>Observacion Anterior</td>";
					echo"<td class='fila2'><TEXTAREA name='obs_ant' id='obs_ant' rows='4' cols='120' readonly></TEXTAREA></td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>Observacion</td>";
					echo"<td class='fila2'><TEXTAREA name='obs' id='obs' rows='4' cols='120'></TEXTAREA></td>";
				echo"</tr>";
				echo "<tr id='grabar_solamente_observ'><td colspan=2 class='fila2' align=center><input type='button' value='Grabar observaciones' onclick='grabar_solo_observacion()'></td></tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='factores' class='divs'>";
				echo"<table align='center' width='1100' border='0' id='tabla_factores'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>FACTORES DETERMINANTES Y CONTRIBUTIVOS</td>";
				echo"</tr>";
				echo"<tr class='encabezadotabla'>";
					echo"<td>FACTORES</td><td>RELACIONADOS</td><td>CUALES</td><td>DIRECTO-INDIRECTO</td>";
					echo"<td><span onclick=\"addFila('tabla_factores','".$key2."','".$wbasedato."','".$datos."','".$wemp_pmla1."','".$cco."');\" class='efecto_boton' >".NOMBRE_ADICIONAR."</span></td>";
				echo"</tr>";


		$hay_factores = 0;

        if($hay_factores == 0)
        {
            $id_fila = "1_tr_tabla_factores";
            echo "
            <tr id='".$id_fila."' class='fila2'>
                <td>
                    <input type='hidden' id='".$id_fila."_id' name='".$id_fila."_id' value='' >";

					//se trae la clase de factor
								$sql = "Select Tipcod,Tipdes
										  FROM root_000094
										 WHERE Tiptip='EA'
										   AND Tipest='on'";
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

								echo"<SELECT NAME='".$id_fila."_fact_1' id='".$id_fila."_fact_1' class='validarEA' onchange='llenar_hijo(\"".$id_fila."_fact_1\",\"".$id_fila."_cuales\",\"".$key2."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla1."\",\"".$cco."\")'>"; //llenar_hijo(\"".$id_fila."_cuales\")
								echo"<option value=''>Seleccione..</option>";
                                    for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
                                    {

                                        if( $clasificacion != trim( $rows['Tipcod'] ) )
                                        {
                                            echo "
                                                            <option value='".$rows['Tipcod']."'>".utf8_encode($rows['Tipdes'])."</option>";
                                        }
                                        else
                                        {
                                            echo "
                                                            <option value='".$rows['Tipcod']."' selected>".utf8_encode($rows['Tipdes'])."</option>";
                                        }
                                    }
								echo"</SELECT>";
								//mirar si en el directo o en el si se le quita el seleccione

				echo"</td>
                <td><SELECT NAME='".$id_fila."_fact_s_n' id='".$id_fila."_fact_s_n' class='validarEA' onchange='ocultar_mostrar(\"".$id_fila."_cuales\",\"".$id_fila."_fact_s_n\")'>
					<OPTION VALUE=''>Seleccione...</OPTION>
					<OPTION VALUE='Si'>Si</OPTION>
					<OPTION VALUE='No'>No</OPTION>
					</SELECT>
				</td>
                <td><SELECT NAME='".$id_fila."_cuales' id='".$id_fila."_cuales' class='validarEA'>
					<OPTION VALUE=''>Seleccione...</OPTION>

					</SELECT>
				</td>
                <td><SELECT NAME='".$id_fila."_indi' id='".$id_fila."_indi' class='validarEA'>
					<OPTION VALUE=''>Seleccione...</OPTION>
					<OPTION VALUE='Directo'>Directo</OPTION>
					<OPTION VALUE='Indirecto'>Indirecto</OPTION>
					</SELECT>
				</td>
                <td align='center'><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\");'>".NOMBRE_BORRAR."</span></td>
            </tr>";
        }
			echo"</table>";
		echo"</div>";

//Tipo de evento (Fuente)
			echo"<div id='infeccion' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>TIPO DE EVENTO</td>";
					echo"</tr>";
					echo"<tr>";
					$sql_tie = "  SELECT Tienid, Tiedes, Tieval
										FROM ".$wbasedato."_000015
									   WHERE Tieest = 'on'";
					$res_tie = mysql_query($sql_tie, $conex) or die( mysql_errno()." - Error en el query $sql_tie - ".mysql_error());
					$num_tie = mysql_num_rows($res_tie);

					while($row_tie = mysql_fetch_array($res_tie)){

						echo"<td align='center' width='200' class='fila2'>".$row_tie['Tiedes'].":<INPUT type='radio' name='infec' id='".$row_tie['Tienid']."' value='".$row_tie['Tieval']."'></td>";

						}

					echo"</tr>";
				echo"</table>";
			echo"</div>";
			echo"<div id='conclusiones' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='8'>CONCLUSIONES</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='' class='fila1'>Clase</td>";
					echo"<td align='center' width='200' class='fila2'>Casi Falla:<INPUT type='radio' name='clase' id='falla' value='Casi Falla'></td>";
					echo"<td align='center' width='200' class='fila2'>Incidente:<INPUT type='radio' name='clase' id='incidente' value='Incidente'></td>";
					echo"<td align='center' width='' class='fila2'>Complicacion:<INPUT type='radio' name='clase' id='complicacion' value='Complicacion'></td>";
					echo"<td align='center' width='' class='fila2'>Evento Adverso:<INPUT type='radio' name='clase' id='eventoA' value='Evento Adverso' ></td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>Tipo</td><td class='fila2'>";
					//se trae el tipo de evento
					$sql = "Select Tipcod,Tipdes
                              FROM root_000094
                             WHERE Tiptip='EAG'
                               AND Tipest='on'";
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

					echo"<SELECT NAME='tipo_even1' id='tipo_even1' class='validarEA' onchange='llenar_evento(\"tipo_even1\",\"evento_hijo\",\"".$key2."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla1."\",\"".$cco."\" )'>";
					echo"<option value=''>Seleccione..</option>";
                    for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
                    {

                        if( $clasificacion != trim( $rows['Tipcod'] ) )
                        {
                            echo "
                                            <option value='".$rows['Tipcod']."'>".utf8_encode($rows['Tipdes'])."</option>";
                        }
                        else
                        {
                            echo "
                                            <option value='".$rows['Tipcod']."' selected>".utf8_encode($rows['Tipdes'])."</option>";
                        }
                    }

					echo"</SELECT>";
					echo"</td>";
					echo"<td align='left' width='100' class='fila1'>Evento</td>";
					echo"<td class='fila2' colspan='2'>";
					echo"<SELECT NAME='evento_hijo' id='evento_hijo' onchange='' class='validarEA'>";
					echo"<option value=''>Seleccione..</option>";

					echo"</SELECT>";
					echo"</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='' class='fila1'>Severidad</td>";
					echo"<td align='center' width='200' class='fila2'>Grave:<INPUT type='radio' name='sev' id='gra' value='Grave'></td>";
					echo"<td align='center' width='200' class='fila2'>Moderado:<INPUT type='radio' name='sev' id='mod' value='Moderado'></td>";
					echo"<td align='center' width='' class='fila2'>Leve:<INPUT type='radio' name='sev' id='lev' value='Leve'></td>";
					echo"<td align='center' width='' class='fila2'>No Daño:<INPUT type='radio' name='sev' id='noda' value='No Daño' checked></td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>¿Es evento prevenible?</td>";
					echo"<td align='left' class='fila2' colspan='4'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					Si<INPUT type='radio' name='prevenible' id='si_p' value='Si' checked>&nbsp&nbsp&nbsp
					No<INPUT type='radio' name='prevenible' id='no_p' value='No'></td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>¿Es evento centinela?</td>";
					echo"<td align='left' class='fila2' colspan='4'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					Si<INPUT type='radio' name='centinela' id='si_c' value='Si' checked>&nbsp&nbsp&nbsp
					No<INPUT type='radio' name='centinela' id='no_c' value='No'></td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='causas' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>CAUSAS</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>Causa</td>";
					echo"<td class='fila2'>";
						//se trae el tipo de evento
						$sql = "Select Caucod,Caudes
	                              FROM root_000091
	                             WHERE Cautip='NC'
	                               AND Cauest='on'";
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

						//echo"<SELECT multiple='multiple' NAME='causas_nc' id='causas_nc' onchange='select_causas(\"".$key2."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla."\",\"".$cco."\")'>";
						echo"<SELECT multiple='multiple' NAME='causas_nc' id='causas_nc' class='validarNC' onclick='marcar(this)' style='width:250px; height:120px'>";

	                    for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
	                    {

	                        if( $clasificacion != trim( $rows['Tipcod'] ) )
	                        {
	                            echo "
	                                            <option value='".$rows['Caucod']."'>".$rows['Caudes']."</option>";
	                        }
	                        else
	                        {
	                            echo "
	                                            <option value='".$rows['Caucod']."' >".$rows['Caudes']."</option>";
	                        }
	                    }

						echo"</SELECT>";
					echo"</td>";
					echo"<td align='left'>
							<span class='subtituloPagina2'><font size='2' >An&aacute;lisis de causas detallado</font></span><br>
							<TEXTAREA name='anaCausal' id='anaCausal' class='validarEA' rows='4' cols='120'></TEXTAREA>
					    </td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='analizado' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>ANALISIS</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>Analizado por</td>";
					echo"<td class='fila2'><TEXTAREA name='analiz' id='analiz' class='validarEA' rows='4' cols='120'></TEXTAREA></td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='rechazo' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>CAUSA RECHAZO</td>";
					echo"</tr>";
					echo"<tr id='tr_causa_ant'>";
						echo"<td align='left' width='100' class='fila1'>Causa de rechazo anterior</td>";
						echo"<td class='fila2'><TEXTAREA name='caurechazo_ant' id='caurechazo_ant' rows='4' cols='120' readonly></TEXTAREA></td>";
					echo"</tr>";
					echo"<tr id='tr_causa_t' style='display:none'>";
						echo"<td align='left' width='100' class='fila1'>Causa porque rechazo el evento</td>";
						echo"<td class='fila2'><TEXTAREA name='caurechazo' id='caurechazo' rows='4' cols='120'></TEXTAREA></td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='link_accion' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>TRATAMIENTO DE LA NO CONFORMIDAD - ACCION</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' style='width:100px' class='fila1'>Accion Correctiva</td>";
						echo"<td align='left' class='fila2'>&nbsp;&nbsp;&nbsp;&nbsp;
						Si<INPUT type='radio' name='accion' id='acc_si' value='Si'>&nbsp;&nbsp;
						No<INPUT type='radio' name='accion' id='acc_no' value='No'>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='accion_correctiva' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>SEGUIMIENTO ACCION CORRECTIVA</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='200' class='fila1'>Acciones Correctivas</td>";
						echo"<td align='left' class='fila2'>
						<a href='#' onclick=\"javascript:abrirVentana( '".$key."', '".$wbasedato."', '".$datos."', '".$wemp_pmla."', '".$cco."');\">Accion Correctiva</a>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='estados' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>ESTADO DEL EVENTO</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='100' class='fila1'>Estado</td>";
						echo"<td class='fila2'>";
						//estados();
						echo "<div id='select_estados'>0</div>";
						echo"<div id='tarea'></div>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='relacionar_nc' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>RELACIONAR</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='100' class='fila1'></td>";
						echo"<td class='fila2'><select id='registros_relacionados' multiple='multiple'>";
						echo "</select>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='ver_relaciones' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>RELACIONES</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='100' class='fila1'></td>";
						echo"<td class='fila2'>";
						echo"<div id='mostrar_relacionados'></div>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='enviar' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr class='fila2'>";
					echo"<td colspan='5' align='center'><button type='button' onClick=\"enviar('$key2','$wbasedato','$datos','$wemp_pmla1','$cco','tabla_factores', '".$row_coor['Ajecoo']."', '".$westadocoor."', '".$wnivelcoord."');\">Enviar</button></td>";

				echo"</tr>";
				echo"</table>";
			echo"</div>";

			echo"<div id='editar' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr class='fila2'>";
					echo"<td colspan='5' align='center'><button type='button' onClick=\"editar('$key2','$wbasedato','$datos','$wemp_pmla1','$cco','tabla_factores');\">Enviar</button></td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";
    echo"</div>";

	//========= Consulto los centros de costos de la tabla costosyp_000005, para listarlos en el div oculto del nuevo cco que genero, este se mostrara cuando necesiten cambiarlo ====
	if ($cco == 'costosyp_000005'){
		$query_cco1 = "SELECT Ccocod, Cconom
				         FROM ".$cco."
				        WHERE Ccoest = 'on'
				          AND Ccoemp = '01'
				     ORDER BY Cconom ";
    }
    else{
    	$query_cco1 = "SELECT Ccocod, Cconom
				         FROM ".$cco."
				        WHERE Ccoest = 'on'
				     ORDER BY Cconom ";
    }
	$res_cco1 = mysql_query( $query_cco1 ) or die( mysql_errno()." - Error en el query $query_cco1 - ".mysql_error() );

	$arr_cco1 = array();
	while($row_cco1 = mysql_fetch_array($res_cco1))
	{
		//Se verifica si el cco ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row_cco1['Ccocod'], $arr_cco1))
		{
			$arr_cco[$row_cco1['Ccocod']] = array();
		}

		$row_cco1['Cconom'] = str_replace( $caracteres, $caracteres2, $row_cco1['Cconom'] );
		$row_cco1['Cconom'] = utf8_decode( $row_cco1['Cconom'] );
		//Aqui se forma el arreglo, con clave el servicio => codigo del cco y su INFORMACIÓN.
		$arr_cco1[$row_cco1['Ccocod']] = trim($row_cco1['Ccocod'])."-".trim($row_cco1['Cconom']);

	}

//Bloque que se muestra cuando necesiten cambiar de centro de costos que generó. Jonatan Lopez 19 Marzo 2014
echo "<div id='nuevo_cco_genero' style='display:none;' title='Seleccionar el nuevo centro de costos que generó'>
		<table border='0' >
		  <tbody>
			<tr>
			  <td align=center class=encabezadotabla>Nuevo centro de costos que generó:</td>
			</tr>
			<tr>
			  <td>";

echo 		"<select id='cco_genero_nuevo' onchange='mostrar_procesos_cco_modal();'>";
echo 		"<option value=''></option>";
			foreach($arr_cco1 as $key => $value){

			echo "<option value='".$key."'>".$value."";

			}

echo 		"</select>";

echo		"<div style='display:inline;' id='procesos_cco_modal'></div></td>

			</tr>

			<tr>
			  <td align=center colspan='3' rowspan='1'><input type='button' value='Guardar' onclick='cambiar_unidad_genero()' /></td>
			</tr>
		  </tbody>
		</table>
	  </div>";

	//==============================================================

//boton de cerrar
echo"<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' /></center>";
echo"</form>";
echo"</body>";
echo"</html>";
}
?>