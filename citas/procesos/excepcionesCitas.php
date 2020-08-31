<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");



if (isset($accion) and $accion == 'enviar')
{

	$data = array('error'=>0,'mensaje'=>'','actualizacion'=>0);

	if ($tipoExc == 'No disponibilidad')
	{
		// $consul='NO APLICA';
		$unihora =0;

	}

	if ($tipoExc == 'Observaciones')
	{

		$unihora =1;
		$hf="";
		$hi="";

	}

	if (($hi != "" or $hi != "Seleccione...") and ($hf != "" or $hf != "Seleccione..."))
	{
		//para que le quite los dos puntos a la hora de la excepcion en cualquier tipo
		$hi1=explode(":",$hi);
		$min_hi=$hi1[0];
		$seg_hi=$hi1[1];
		$hi=$min_hi."".$seg_hi;
		$hf1=explode(":",$hf);
		$min_hf=$hf1[0];
		$seg_hf=$hf1[1];
		$hf=$min_hf."".$seg_hf;
	}



	if ($caso == 2)
	{
		if ($tipoExc != 'Observaciones')
		{
			$condicionSede        = ( $sede != "" ) ? " AND centrocostos = '{$sede}'" : "";
			$condicionSedeInsert1 = ( $sede != "" ) ? " centrocostos, " : "";
			$condicionSedeInsert2 = ( $sede != "" ) ? " '{$sede}', " : "";

			//se hace una consulta a la tabla de citas para saber si el medico ya tiene citas
			$query1="select COUNT(*)
					 from ".$solucionCitas."_000009
					 where Cod_equ ='".$valor1."'
					 and Fecha between '".$fechai."' and '".$fechaf."'
					 {$condicionSede}
					 and Activo = 'A'";
					 // and Hi >= '".$hi."'
					 // and Hf <= '".$hf."' pendiente para agregar
			$res1 = mysql_query( $query1,$conex) or die( mysql_errno()." - Error en el query $query1 - ".mysql_error() );
			$contados = mysql_result($res1,0);
			if($contados>0)
			{
				$data['error']=1;
				$data['mensaje'] =utf8_encode("No se puede crear la excepcion porque ya se tienen citas asignadas ");
			}
			else
			{
				//se hace select con los datos que se traen para verificar si el registro existe

				// $query ="select Fecha_I, Fecha_F, Codigo, id, Activo
						 // from ".$solucionCitas."_000012
						 // where Fecha_I = '".$fechai."'
						 // and Fecha_F ='".$fechaf."'
						 // and Codigo = '".$valor1."'
						 // and Activo = '".$act."'";

				// $res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
				// $row = mysql_fetch_array($res);
				// $num = mysql_num_rows($res);
				// if ($num>0)
				// {
					// $query2 ="UPDATE ".$solucionCitas."_000012
							  // SET Fecha_I = '".$fechai."', Fecha_F = '".$fechaf."', Uni_Hora = '".$unihora."', Hi='".$hi."', Hf='".$hf."', Consultorio='".$consul."', Activo='".$act."', Control='".$causa."'
							  // WHERE id = '".$row['id']."'";

					// $res2 = mysql_query( $query2,$conex ) or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );
				// }
				// else
				// {
					$query3 ="INSERT INTO ".$solucionCitas."_000012 ( Medico , Fecha_data , Hora_data , Fecha_I , Fecha_F , Codigo , Uni_Hora , Hi , Hf, Consultorio, Activo, Control, {$condicionSedeInsert1} Seguridad )
							VALUES ('".$solucionCitas."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$fechai."', '".$fechaf."', '".$valor1."', '".$unihora."', '".$hi."', '".$hf."', '".$consul."', '".$act."', '".utf8_decode($causa)."', {$condicionSedeInsert2} 'C-".$solucionCitas."');";

					$res3 = mysql_query( $query3,$conex ) or die( mysql_errno()." - Error en el query $query3 - ".mysql_error() );
				// }

				if ($res3)
				{
					$data['mensaje'] =utf8_encode("Cambio realizado con exito ");
				}
				else
				{
					$data['mensaje'] =utf8_encode("No se guardo la excepción ");
				}

			}
		}
		else
		{
			//se hace select con los datos que se traen para verificar si el registro existe

				$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000012
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 {$condicionSede}
						 and Activo = 'A'";

				$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
				$num = mysql_num_rows($res);
				if ($num>0)
				{
					$row = mysql_fetch_array($res);

					$query2 ="UPDATE ".$solucionCitas."_000012
							  SET Fecha_I = '".$fechai."', Fecha_F = '".$fechaf."', Uni_Hora = '".$unihora."', Hi='".$hi."', Hf='".$hf."', Consultorio='".$consul."', Activo='".$act."', Control='".utf8_decode($causa)."'
							  WHERE id = '".$row['id']."'";

					$res3 = mysql_query( $query2,$conex ) or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );


				}
				else
				{

					$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000012
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 {$condicionSede}
						 and Activo = 'I'";

					$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
					$num = mysql_num_rows($res);
					if ($num>0)
					{
						$row = mysql_fetch_array($res);

						$query2 ="UPDATE ".$solucionCitas."_000012
								  SET Fecha_I = '".$fechai."', Fecha_F = '".$fechaf."', Uni_Hora = '".$unihora."', Hi='".$hi."', Hf='".$hf."', Consultorio='".$consul."', Activo='".$act."', Control='".utf8_decode($causa)."'
								  WHERE id = '".$row['id']."'";

						$res3 = mysql_query( $query2,$conex ) or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );


					}
					else
					{
						$query3 ="INSERT INTO ".$solucionCitas."_000012 ( Medico , Fecha_data , Hora_data , Fecha_I , Fecha_F , Codigo , Uni_Hora , Hi , Hf, Consultorio, Activo, Control, {$condicionSedeInsert1} Seguridad )
										VALUES ('".$solucionCitas."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$fechai."', '".$fechaf."', '".$valor1."', '".$unihora."', '".$hi."', '".$hf."', '".$consul."', '".$act."', '".utf8_decode($causa)."', {$condicionSedeInsert2} 'C-".$solucionCitas."');";

						 $res3 = mysql_query( $query3,$conex ) or die( mysql_errno()." - Error en el query $query3 - ".mysql_error() );
					}


				}

				if ($res3)
				{
					$data['mensaje'] =utf8_encode("Cambio realizado con exito ");
				}
				else
				{
					$data['mensaje'] =utf8_encode("No se guardo la excepción ");
				}

		}


	}
    else if ($caso ==3 or $caso == 1)
    {
		if ($tipoExc != 'Observaciones')
		{
			//se hace una consulta a la tabla de citas para saber si el equipo ya tiene citas en el rango de la excepcion
			$query1="select Hi, Hf, Fecha
					 from ".$solucionCitas."_000001
					 where Cod_equ ='".$valor1."'
					 and Fecha between '".$fechai."' and '".$fechaf."'
					 and Hi >= '".$hi."'
					 and Hf <= '".$hf."'
					 and Activo = 'A'";
			$res1 = mysql_query( $query1,$conex ) or die( mysql_errno()." - Error en el query $query1 - ".mysql_error() );
			$contados = mysql_num_rows($res1);

			$citasDentroRango = '';
			while( $rows1 = mysql_fetch_array( $res1 ) )
			{
				$citasDentroRango[ $rows1[ 'Fecha' ]."-".$rows1[ 'Hi' ] ] = true;
			}

			$contadosExcepciones = 0;
			//Calculamos el total de citas que hay en el rango de fechas en las excepciones existentes
			for( $i = strtotime( $fechai ); $i <= strtotime( $fechaf ); $i += 24*3600 )
			{

				 $query5="select a.Hi, a.Hf, Fecha
						 from ".$solucionCitas."_000001 a, ".$solucionCitas."_000021 b
						 where Cod_equ ='".$valor1."'
						 and Codigo = Cod_equ
						 and Fecha = '".date( "Y-m-d", $i )."'
						 and Fecha between b.Fecha_I and b.Fecha_F
						 and a.Hi >= b.Hi
						 and a.Hf <= b.Hf
						 and a.Activo = 'A'
						 and b.Activo = 'A'
						 and b.Uni_hora > 0
						 ";

				$res5 = mysql_query( $query5,$conex ) or die( mysql_errno()." - Error en el query $query5 - ".mysql_error() );
				$contadosExcepciones += mysql_num_rows($res5);

				while( $rows5 = mysql_fetch_array( $res5 ) )
				{
					$citasDentroRango[ $rows5[ 'Fecha' ]."-".$rows5[ 'Hi' ] ] = true;
				}
			}


			// se hace una consulta a la tabla de citas para saber si el equipo ya tiene citas esa fecha
			$query4="select Hi, Hf, Fecha
					 from ".$solucionCitas."_000001
					 where Cod_equ ='".$valor1."'
					 and Fecha between '".$fechai."' and '".$fechaf."'
					 and Activo = 'A'";
			$res4 = mysql_query( $query4,$conex ) or die( mysql_errno()." - Error en el query $query4 - ".mysql_error() );
			$contadosTotal = mysql_num_rows($res4);
			// echo "".$contados."-".$contadosExcepciones."-".$contadosTotal."";
			if($contados + $contadosExcepciones != $contadosTotal)
			{
				$citasFueraRango = '';

				//Busco que citas no estan en el rango de las citas de excepciones
				while( $rows4 = mysql_fetch_array($res4) ){

					//Se busca la cita que no este dentro del array citasDentroRango
					if( !isset($citasDentroRango[ $rows4[ 'Fecha' ]."-".$rows4[ 'Hi' ] ]) ){
						$citasFueraRango .= $rows4[ 'Fecha' ]." a las ".$rows4[ 'Hi' ]."\n";
					}
				}

				$data['error']=1;
				$data['mensaje'] =utf8_encode("No se puede crear la excepcion porque ya se tienen citas asignadas\nen el horario fuera de la excepcion y se podrian perder\n$citasFueraRango");
			}
			else
			{
			//se hace select con los datos que se traen para verificar si un registro existe en ese rango de esas horas

				$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Hi, Hf
						 from ".$solucionCitas."_000021
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 and Activo = '".$act."'
						 and ( Hi < '".$hi."'
						 and ( Hf > '".$hi."')
						 or (Hi < '".$hf."'
						 and Hf > '".$hf."')
						 or Hi > '".$hi."'
						 and Hi < '".$hf."')
						 ";

				$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
				$row = mysql_fetch_array($res);
				$num = mysql_num_rows($res);
				if ($num > 0)
				{
					$data['error']=1;
					$data['mensaje'] =utf8_encode("No se puede crear la excepcion porque ya existe una en ese rango de hora\npor favor verifique ");
				}
				else
				{
					$query3 ="INSERT INTO ".$solucionCitas."_000021 ( Medico , Fecha_data , Hora_data , Fecha_I , Fecha_F , Codigo , Uni_Hora , Hi , Hf, Consultorio, Activo, Control, Seguridad )
							VALUES ('".$solucionCitas."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$fechai."', '".$fechaf."', '".$valor1."', '".$unihora."', '".$hi."', '".$hf."', '".$consul."', '".$act."', '".utf8_decode($causa)."', 'C-".$solucionCitas."');";

					// $res3 = mysql_query( $query3,$conex ) or die( mysql_errno()." - Error en el query $query3 - ".mysql_error() );
					if($res3 = mysql_query( $query3, $conex ))
					{
						$data['mensaje'] =utf8_encode("Cambio realizado con exito ");
					}
					else
					{
						$data['error']=1;
						$data['mensaje'] =utf8_encode("Se esta intentando crear una excepcion con la misma hora de inicio, equipo y fecha ".mysql_error());
					}

				}
			}
		}
		else
		{

			//se hace select con los datos que se traen para verificar si el registro existe

				$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000021
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 and Activo = 'A'";

				$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
				$num = mysql_num_rows($res);
				if ($num>0)
				{
					$row = mysql_fetch_array($res);

					$query2 ="UPDATE ".$solucionCitas."_000021
							  SET Fecha_I = '".$fechai."', Fecha_F = '".$fechaf."', Uni_Hora = '".$unihora."', Hi='".$hi."', Hf='".$hf."', Consultorio='".$consul."', Activo='".$act."', Control='".utf8_decode($causa)."'
							  WHERE id = '".$row['id']."'";

					$res3 = mysql_query( $query2,$conex ) ;


				}
				else
				{

					$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000021
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 and Activo = 'I'";

					$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
					$num = mysql_num_rows($res);
					if ($num>0)
					{
						$row = mysql_fetch_array($res);

						$query2 ="UPDATE ".$solucionCitas."_000021
								  SET Fecha_I = '".$fechai."', Fecha_F = '".$fechaf."', Uni_Hora = '".$unihora."', Hi='".$hi."', Hf='".$hf."', Consultorio='".$consul."', Activo='".$act."', Control='".utf8_decode($causa)."'
								  WHERE id = '".$row['id']."'";

						$res3 = mysql_query( $query2,$conex ) ;


					}
					else
					{

						$query3 ="INSERT INTO ".$solucionCitas."_000021 ( Medico , Fecha_data , Hora_data , Fecha_I , Fecha_F , Codigo , Uni_Hora , Hi , Hf, Consultorio, Activo, Control, Seguridad )
								VALUES ('".$solucionCitas."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$fechai."', '".$fechaf."', '".$valor1."', '".$unihora."', '".$hi."', '".$hf."', '".$consul."', '".$act."', '".utf8_decode($causa)."', 'C-".$solucionCitas."');";

						$res3 = mysql_query( $query3, $conex );
					}
				}


				if($res3)
				{
					$data['mensaje'] =utf8_encode("Cambio realizado con exito ");
				}
				else
				{
					$data['error']=1;
					$data['mensaje'] =utf8_encode("No se guardo la excepción ");
				}
		}
	}

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'llenarSelectHoras')
{





	$numFechai=date( "N", strtotime( $fechai." 00:00:00" ) );
	$numFechaf=date( "N", strtotime( $fechaf." 00:00:00" ) );
	$condicionSede        = ( $sede != "" ) ? " AND centrocostos = '{$sede}'" : "";
	$condicionSedeInsert1 = ( $sede != "" ) ? " centrocostos, " : "";
	$condicionSedeInsert2 = ( $sede != "" ) ? " '{$sede}', " : "";

	$data = array('error'=>0,'html'=>'','mensaje'=>'', 'unihora' => '');

			if ($caso=='1' or $caso == '3')
			{
				$sql = "select Uni_hora
						from ".$solucionCitas."_000003
						where Codigo = '".$medico."'
						and Activo='A'
						";
			}
			else if ($caso == '2' and $valCitas=='on')
			{
				$sql = "select Uni_hora, Dia
						from ".$wbasedato."_000051 a, ".$solucionCitas."_000010 b
						where b.Codigo = '".$medico."'
						and b.Codigo = a.Medcid
						{$condicionSede}
						and Activo='A'
						";
			}
			else if($caso=='2' and $valcitas!='on')
			{
				$sql = "select Uni_hora, Dia
						from ".$solucionCitas."_000010
						where Codigo = '".$medico."'
						{$condicionSede}
						and Activo='A'
						";
			}
		if($res = mysql_query( $sql, $conex ))
		{
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				if ($caso=='1' or $caso == '3')
				{

					$data['html'] .="<option value=''>Seleccione..</option>";

					$rows = mysql_fetch_array( $res );

					$data['unihora'] = $rows['Uni_hora']; //se llena el campo hidden

					for( $i = 0; $i < 24*3600 ; $i+=$rows['Uni_hora']*60)
					{
						$hora=gmdate( "H:i", $i );
						$data['html'] .= "<option value='".$hora."'>".$hora."</option>";

					}
				}
				else
				{
					/*se hace este cambio porque los medicos no atienden todos los dias entonces se busca la unidad de hora
					del dia anterio de la atencion para llenar los select de la hora inicial y final
					*/
					while( $rows = mysql_fetch_array( $res ) )
					{

									//posicion             valor
						$unihoras[ $rows[ 'Dia' ] ] = $rows[ 'Uni_hora' ]; //se guarda la unidad de hora en un array
					}

					//pregunta por la posicion
					if( isset( $unihoras[ $numFechai ] ) )
					{
						$unihora = $unihoras[ $numFechai ]; //el valor del array en la posicion del valor de $numFechai
					}
					else
					{ //se pregunta por la unidad del dia actual para atras hasta el dia 1 lunes

						for( $i = $numFechai - 1; $i > 0 ; $i-- )
						{
							if( isset( $unihoras[ $i ] ) ){
								$unihora = $unihoras[ $i ]; //el valor del array en la posicion del valor de $numFechai
								break;
							}
						}

						if( empty( $unihora ) )
						{ //si no se ha encontrado el valor se comienza a buscar desde el dia 7 domingo hasta el dia actual

							for( $i = 7; $i > $numFechai; $i-- ){

								if( isset( $unihoras[ $i ] ) ){
									$unihora = $unihoras[ $i ]; //el valor del array en la posicion del valor de $numFechai
									break;
								}
							}
						}
					}
					$data['unihora'] = $unihora; //se llena el campo hidden
					$data['html'] .="<option value=''>Seleccione..</option>";
					for( $i = 0; $i < 24*3600 ; $i+=$unihora*60)
					{
						$hora=gmdate( "H:i", $i );
						$data['html'] .= "<option value='".$hora."'>".$hora."</option>";
					}
				}
			}


		}
		else
		{
			$data['mensaje'] = "No se pudo realizar la consulta.";
			$data['error'] = 1;
		}
	//$data['mensaje'] = "mensaje:".$numFechai."";

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'validar')
 {


	$condicionSede        = ( $sede != "" ) ? " AND centrocostos = '{$sede}'" : "";
	$condicionSedeInsert1 = ( $sede != "" ) ? " centrocostos, " : "";
	$condicionSedeInsert2 = ( $sede != "" ) ? " '{$sede}', " : "";


	$data = array('error'=>0,'mensaje'=>'','obs'=>'');

    // echo $tipo."-".$fechai."-".$fechaf."-".$valor1."hola";

	if ($caso == 2)
	{

		//se hace select con los datos que se traen para verificar si el registro existe

				$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000012
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 {$condicionSede}
						 and Activo = 'A'";

				$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
				if ($res)
				{
					$num = mysql_num_rows($res);
					if ($num>0)
					{
						$row = mysql_fetch_array($res);

						if (($row['Codigo'] != 'Todos' or $row['Codigo'] != '') )
						{
							$sql = "select codigo,descripcion
								   from ".$solucionCitas."_000010
								   where codigo = '".$row['Codigo']."'
								   and activo='A'
								   group by descripcion";
							$res1 = mysql_query( $sql,$conex );
							if ($res1)
							{
								$num = mysql_num_rows($res1);
								if ($num > 0)
								{
									$rows=mysql_fetch_array($res1);
									$nombre=$rows['descripcion'];
								}
							}
							else
							{
								$data['mensaje']="No se realizo la consulta a la tabla ".$solucionCitas."_000010";
								$data['error']=1;
							}

						}

						$data['mensaje']=utf8_encode("Ya existe una observacion para: ".$row['Codigo']."-".$nombre." con fecha: ".$row['Fecha_I']." al ".$row['Fecha_F']." Mensaje: ".$row['Control'].". ¿Desea modificarla?");
						$data['obs']=utf8_encode($row['Control']);
					}
					else //posiblemente el registro este inactivo
					{
						$query1 ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000012
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 {$condicionSede}
						 and Activo = 'I'";

						$res1 = mysql_query( $query1,$conex ) or die( mysql_errno()." - Error en el query $query1 - ".mysql_error() );
						if ($res1)
						{
							$num1 = mysql_num_rows($res1);
							if ($num1>0)
							{
								$row1 = mysql_fetch_array($res1);

								if (($row1['Codigo'] != 'Todos' or $row1['Codigo'] != '') )
								{
									$sql = "select codigo,descripcion
									       from ".$solucionCitas."_000010
										   where codigo = '".$row1['Codigo']."'
										   and activo='A'
										   group by descripcion";
									$res = mysql_query( $sql,$conex );
									if ($res)
									{
										$num = mysql_num_rows($res);
										if ($num > 0)
										{
											$rows=mysql_fetch_array($res);
											$nombre=$rows['descripcion'];
										}
									}
									else
									{
										$data['mensaje']="No se realizo la consulta a la tabla ".$solucionCitas."_000010";
										$data['error']=1;
									}

								}
								// else
								// {
									// $sql = "select codigo,descripcion
									       // from ".$solucionCitas."_000010
										   // where codigo = ".$row1['Codigo']."
										   // and activo='A'
										   // group by descripcion";
									// $res = mysql_query( $sql,$conex );
									// if ($res)
									// {
										// $num = mysql_num_rows($res);
										// if ($num > 0)
										// {
											// $rows=mysql_fetch_array($res);
											// $nombre=$rows['descripcion'];
										// }
									// }
									// else
									// {
										// $data['mensaje']="No se realizo la consulta a la tabla ".$solucionCitas."_000010";
										// $data['error']=1;
									// }
								// }

								$data['mensaje']=utf8_encode("Ya existe una observación para: ".$row1['Codigo']."-".$nombre."  con fecha: ".$row1['Fecha_I']." al ".$row1['Fecha_F']." Mensaje:".$row1['Control']." en estado inactivo. ¿Desea activarla?");
								$data['obs']=utf8_encode($row1['Control']);
							}
						}
						else
						{
							$data['mensaje']="No se ejecuto la consulta a la tabla de excepciones";
							$data['error']=1;
						}



					}
				}
				else
				{
					$data['mensaje']="No se ejecuto la consulta a la tabla de excepciones";
					$data['error']=1;
				}



	}
    else if ($caso ==3 or $caso == 1)
    {

			//se hace select con los datos que se traen para verificar si un registro existe en ese rango de esas horas

				$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000021
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 and Activo = 'A'

						 ";
				$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
				if ($res)
				{
					$num = mysql_num_rows($res);
					if ($num>0)
					{
						$row = mysql_fetch_array($res);

						if ($row['Codigo'] != 'Todos' or $row['Codigo'] != '')
						{
							$sql = "select codigo,descripcion
									from ".$solucionCitas."_000003
									where codigo='".$row['Codigo']."'
									and activo='A'
									order by codigo ";
							$res1= mysql_query( $sql,$conex );
							if ($res1)
							{
								$num1 = mysql_num_rows($res1);
								if ($num1 > 0)
								{
									$rows=mysql_fetch_array($res1);
									$nombre=$rows['descripcion'];
								}
							}
							else
							{
								$data['mensaje']="No se realizo la consulta a la tabla ".$solucionCitas."_000003";
								$data['error']=1;
							}

						}
						if( $data['error'] == 0 ){
							$data['mensaje']=utf8_encode("Ya existe una observacion para: ".$row['Codigo']."-".$nombre." con fecha: ".$row['Fecha_I']." al ".$row['Fecha_F']." Mensaje:".$row['Control'].". ¿Desea modificarla?");
							$data['obs']=utf8_encode($row['Control']);
						}
					}
					else //posiblemente el registro este inactivo
					{
						$query ="select Fecha_I, Fecha_F, Codigo, id, Activo, Control
						 from ".$solucionCitas."_000021
						 where Fecha_I = '".$fechai."'
						 and Fecha_F ='".$fechaf."'
						 and Codigo = '".$valor1."'
						 and Activo = 'I' ";

						$res = mysql_query( $query,$conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
						if ($res)
						{
							$num = mysql_num_rows($res);
							if ($num>0)
							{
								$row = mysql_fetch_array($res);

								if ($row['Codigo'] != 'Todos' or $row['Codigo'] != '')
								{
									$sql = "select codigo,descripcion
											from ".$solucionCitas."_000003
											where codigo='".$row['Codigo']."'
											and activo='A'
											order by codigo ";
									$res1 = mysql_query( $sql,$conex );
									if ($res1)
									{
										$num = mysql_num_rows($res1);
										if ($num > 0)
										{
											$rows=mysql_fetch_array($res1);
											$nombre=$rows['descripcion'];
										}
									}
									else
									{
										$data['mensaje']="No se realizo la consulta a la tabla ".$solucionCitas."_000003";
										$data['error']=1;
									}

								}

								$data['mensaje']=utf8_encode("Ya existe una observación para: ".$row['Codigo']."-".$nombre."  con fecha: ".$row['Fecha_I']." al ".$row['Fecha_F']." Mensaje:".$row['Control']." en estado inactivo. ¿Desea activarla?");
								$data['obs']=utf8_encode($row['Control']);
							}
						}
					}
				}
				else
				{
					$data['mensaje']="No se ejecuto la consulta a la tabla de excepciones";
					$data['error']=1;
				}

    }
	echo json_encode($data);
	return;
}
?>
<html>
<head>
<title>Excepciones Citas</title>
<script src="../../../include/root/jquery-1.3.2.min.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />        <!-- Nucleo jquery -->
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script type="text/javascript">


$(document).ready(function() {


	$( "#fechai" ).val($( "#fechaAct" ).val() );
	$( "#fechaf" ).val($( "#fechaAct" ).val() );

	$( "#fechai,#fechaf" ).datepicker({
	dateFormat:"yy-mm-dd",
	fontFamily: "verdana",
	dayNames: [ "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo" ],
	monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
	dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
	dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
	monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
	changeMonth: true,
	changeYear: true,
	yearRange: "c-100:c+100"
	});



});

	function alerta( txt ){

		$("#textoAlerta2").text( txt  );
		$( '#msjAlerta2').dialog({
			width: "auto",
			height: 200,
			modal: true,
			dialogClass: 'noTitleStuff'
		});
		$(".ui-dialog-titlebar").hide();
		setTimeout( function(){
	       $( '#msjAlerta2').dialog('destroy');
	       $(".ui-dialog-titlebar").show();
	    }, 3000 );
	}

		function enviarDatos()
		{
			//alert("entro");
			var valor   = $("#slDoctor").val();  //el medcid que une la tabla 10 con la 51 en citascs faltan la otras
			var tipoExc = $("#tipo").val();
			var act     = '';
			var sede    = "";
			if($("#manejaSedes").val()){
				sede = $.trim($("#sede").val());
				if( sede == "" ){
					alerta( "Debe seleccionar la sede en la cual aplica la excepción ");
					return;
				}
			}

			if ($("#chactivo").is(':checked'))
			{
				act = 'A';
			}
			else
			{
				act = 'I';
			}



		   $.post("excepcionesCitas.php",
					{
						solucionCitas:  $('#solucionCitas').val(),
						consultaAjax:   '',
						accion:         'enviar',
						caso:         	$('#caso').val(),
						consul:       	$('#consul').val(),
						unihora:        $('#unihora').val(),
						valCitas:     	$('#valCitas').val(),
						fechai:       	$('#fechai').val(),
						fechaf:       	$('#fechaf').val(),
						hi:       		$('#hi').val(),
						hf:       		$('#hf').val(),
						causa:       	$('#causa').val(),
						act:            act,
						tipoExc:        tipoExc,
						valor1:          valor,   //valor que se inserta en la tabla 12 codigo del medico
						sede:           sede


					}
					,function(data) {
					if(data.error == 1)
					{
						alert(data.mensaje);
					}
					else
					{
						alert(data.mensaje);  // update Ok.

						var fecActual = new Date();

						var mes = fecActual.getMonth()*1+1;
						var dia = fecActual.getDate();

						if( mes < 10 ){
							mes = "0"+mes;
						}

						if( dia < 10 ){
							dia = "0"+dia;
						}

						var fecha = fecActual.getFullYear() + "-" + mes + "-" + dia;

						$("#slDoctor").val("");
						$("#consul").val("");
						$("#unihora").val("");
						$("#fechai").val(fecha);
						$("#fechaf").val(fecha);
						$("#hi").val("");
						$("#hf").val("");
						$("#causa").val("");
						$("#tipo").val("");
						$("#chactivo").removeAttr("checked");
						$("#slDoctor option[value='Todos']").remove();
					}
				},
				"json"
			);
		}

		function enviarTipo()
		{

			var index = document.forms.excepcionesCitas.tipo.value;
			//alert(index);
			var caso = document.getElementById( "caso" ).value;
			//alert(caso);

			div2 = document.getElementById('datos2');
			div3 = document.getElementById('datos3');

			if (caso == 2)
			{
				div = document.getElementById('datos');
				if(index!='No disponibilidad')
				{
					div.style.display = '';
				}
				else
				{
					div.style.display = 'none';
				}
			}


			if(index!='No disponibilidad')
			{
				div2.style.display = '';
				div3.style.display = '';
			}
			else
			{
				div2.style.display = 'none';
				div3.style.display = 'none';
			}


			if ($("#tipo").val() == 'Observaciones')
			{
				$('#slDoctor').append('<option value="Todos" selected="selected">Todos</option>');
				$("#datos2").css("display", "none");
				$("#datos3").css("display", "none");
				if (caso == 2)
				{
					$("#datos").css("display", "none");
				}

			}
			else
			{
				$("#slDoctor option[value='Todos']").remove();
				$("#datos2").css("display", "");
				$("#datos3").css("display", "");
			}


		}

function llenarHoras(solucionCitas, caso, wbasedato, valCitas,selectHijohi,selectHijohf)
{

	var valor  = $("#slDoctor").val();
	var fechai = $("#fechai").val();
	var fechaf = $("#fechaf").val();
	var sede   = "";
	if($("#manejaSedes").val()){
		sede = $.trim($("#sede").val());
		if( sede == "" ){
			alerta( "Debe seleccionar la sede en la cual aplica la excepción ");
			return;
		}
	}


	$.ajax(
	{
		url: "excepcionesCitas.php",
		context: document.body,
		type: "POST",
		data:
		{
				consultaAjax :    '',
				solucionCitas: solucionCitas,
				caso         : caso,
				accion       : 'llenarSelectHoras',
				wbasedato    : wbasedato,
				valCitas	 : valCitas,
				selectHijohi : selectHijohi,
				selectHijohf : selectHijohf,
				fechai		 : fechai,
				fechaf		 : fechaf,
				medico        : valor, //se paso valor de ultimo para que funcione
				sede:           sede
		},
		async: false,
		dataType: "json",
		success:function(data) {

			if(false && data.error == 1)
			{
				alert(data.mensaje);
			}
			else
			{
				// alert(data.mensaje);
				$("#"+selectHijohi).html(data.html); // update Ok.
				$("#"+selectHijohf).html(data.html);
				// $("#selectHijohi")[0].innerHTML = data.html; // update Ok.

			 	$( "#unihora" ).val( data.unihora );

			}
		}

	});
}

function validarObservacionSiExiste()
{
	var valor = $("#slDoctor").val();
	var fechai = $("#fechai").val();
	var fechaf = $("#fechaf").val();
	var tipo = $("#tipo").val();
	var caso = $("#caso").val();
	var solucionCitas = $("#solucionCitas").val();
	var valCitas = $("#valCitas").val();
	var sede    = "";
	if($("#manejaSedes").val()){
		sede = $.trim($("#sede").val());
		if( sede == "" ){
			alerta( "Debe seleccionar la sede en la cual aplica la excepción ");
			return;
		}
	}



	if ($("#tipo").val() == 'Observaciones')
	{
		$.post("excepcionesCitas.php",
		{
			tipo:    tipo,
			consultaAjax:   '',
			accion:  'validar',
			fechai:  fechai,
			fechaf:  fechaf,
			caso:    caso,
			solucionCitas: solucionCitas,
			valCitas:  valCitas,
			valor1:   valor,
			sede:   sede
		}
		,function(data)
		{
			if (data.error == 1)
			{
				if (data.mensaje != '')
				{
					alert(data.mensaje);
				}
			}
			else
			{
				if (data.mensaje != '')
				{
					var r=confirm(data.mensaje);
					if (r)
					{
						$("#causa").val(data.obs);

					}
					else
					{
						var fechai = $("#fechai").val($("#fechaAct").val());
						var fechaf = $("#fechaf").val($("#fechaAct").val());
					}
						// se agrega para que quede todo en blanco
					    // $("#slDoctor").val("");
						// $("#consul").val("");
						// $("#unihora").val("");
						// $("#fechai").val($("#fechaAct").val());
						// $("#fechaf").val($("#fechaAct").val());
						// $("#hi").val("");
						// $("#hf").val("");
						// $("#causa").val("");
						// $("#tipo").val("");
						// $("#chactivo").removeAttr("checked");

				}
			}
		},
		"json"

		);
	}
}


</script>
</head>

<?php
/*
Creacion: 2012-10-06 Este script se crea para la utilzacion de las excepciones de la citas, en las unidades de la Clinica y en la Clinica del Sur,
					 exitsten dos tipos de excepciones, no disponibilidad y disponibilidad, la primera guarda los datos cuando el medico por algun motivo
					 no puede asistir y saldra que no tiene atencion en la lista de citas, la segunda es cuando el horario habitual es modificado.
					 La tabla de excepciones en el caso 2 es la tabla 000012 y en el caso 1 o 3 es la 000021 estas excepciones guardan datos como:
					 fecha de la excepcion inicial y final, codigo del medico, unidad de hora(tiempo que dura la cita), hora inicial, hora final,
					 consultorio, estado activo o inactivo, causa

Modificacion:
2013-01-31 Se organiza la parte donde se llenan los select de horas, de la tabla 10, se coloco valCitas != on. Viviana Rodas
2013-11-26 Se modifica el script para permitir ingresar observaciones, que es una nota que aparece segun sea el tipo: si es para todos ya
			sean medicos o equipos aparece la nota en amarillo en es script dispMedicos.php o dispEquipos.php, o si se selecciona un medico o un
			equipo especifico aparece la nota en agendaEquipos.php o agendaMedicos.php, estas observaciones se pueden crear entre dos fechas,
			durante las cuales se mostrara el mensaje, estas observaciones se pueden modificar, tambien se pueden inactivar para no mostrarlas o
			activarlas, la busqueda de una observacion se realiza con los  4 campos, tipo: observacion, fecha inicial, fecha final, y medico o equipo = todos
			o al que se requiera, se mostrara un mensaje de si desea modificar o activar sea el caso.
2013-09-12 Se modifica el script cambiando el orden del envio de las variables valor y tipoexc para que envie en la variable valor el codigo del medico.
2013-07-03 Se modifica el script para permitir asignar varias excepciones en un dia para los equipos, las excepciones de medicos no se tocan. Viviana Rodas
2013-04-25 Se modifica el script para que antes de hacer la excepcion verifique que las citas que ya esten asignadas estan activas. Viviana Rodas
2013-04-04 Se midifica el script agregando una consulta para verificar si el equipo o el medico ya tienen citas asignadas y asi no permitir crear la excepcion. Viviana Rodas
2013-03-21 Se modifica la consulta que lista los medicos para que agrupe los medicos por nombre. Viviana Rodas
2012-11-23 Se modifica el script haciendo un select de la hora inicial y de la hora final de la atencion cuando es una excepcion de tipo
						 disponibilidad para que el usuario no digite la hora, ademas se crea un link de consulta de excepciones en la parte inferior
						 para que el usuario pueda vizualizar todas las excepciones.
*/


session_start();

if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
echo "<body>";
   $fechaAct=date("Y-m-d");
   echo "<input type='hidden' id='caso' name='caso' value='".$caso."'>";  //faltan las otras
   echo "<input type='hidden' id='solucionCitas' name='solucionCitas' value='".$solucionCitas."'>";
   echo "<input type='hidden' id='valCitas' name='valCitas' value='".@$valCitas."'>";
   echo "<input type='hidden' id='fechaAct' name='fechaAct' value='".$fechaAct."'>";


	include_once("root/comun.php");


	$conex = obtenerConexionBD("matrix");
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$wbasedato = strtolower( $institucion->baseDeDatos );
    $wentidad = $institucion->nombre;

	if (!isset($valCitas))
	{
		$valCitas = "off";
	}

	if ($wemp_pmla == 01)
	{
		encabezado("Excepciones Citas", "2014-02-03", $wbasedato );
	}
	else
	{
		encabezado("Excepciones Citas", "2014-02-03", "logo_".$wbasedato );
	}

	$wfec=date("Y-m-d");

	if ($caso == 2 and $valCitas=="on")
	{
		$sql = "SELECT
			Mednom, Medcod, Medcid
		FROM
			{$wbasedato}_000051
		WHERE
			Medcid <> ''
			AND Medcid <> 'NO APLICA'
			AND Medest = 'on'
		ORDER BY Mednom";
	}
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000003 where activo='A' order by codigo ";
	}

	else if ($caso == 2 and $valCitas!="on")
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000010 where activo='A' group by descripcion";
	}


	$res1 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	$query  = "SELECT detval
	             FROM root_000051
	            WHERE detemp = '{$wemp_pmla}'
	              AND detapl = 'manejaSedes'";
	$rs     = mysql_query( $query, $conex );
	$row    = mysql_fetch_assoc( $rs );

	$manejaSedes = ( $row['detval'] ) ? true : false;

	if( $manejaSedes ){
		$arregloSedes = array();
		$queryCco  = "SELECT ccocod, ccodes
		                FROM {$wbasedato}_000003
		               WHERE ccotip = 'A'
		                 AND ccoest = 'on'";
		$rs        = mysql_query( $queryCco, $conex );
		while( $row = mysql_fetch_assoc( $rs ) ){
			$arregloSedes[$row['ccocod']] = $row['ccodes'];
		}
	}


	echo "<form name='excepcionesCitas' method='post' action=''>";
	echo "<input type='hidden' name='manejaSedes' id='manejaSedes' value='{$manejaSedes}'>";
	echo "<div id=excepciones align='center'>";
	echo "<table>";
	echo "<th colspan='2' class='encabezadotabla'>Ingreso Excepcion</th>";
	if( $manejaSedes ){
		echo "<tr><td class='fila1'>Sede</td><td class='fila2'><select name='sede' id='sede' onChange=''>";
				echo "<option value=''>Seleccione..</option>";
				foreach( $arregloSedes as $ccoCod => $descripcion ){
					echo "<option value='{$ccoCod}'>{$ccoCod}-{$descripcion}</option>";
				}
	}
		echo "</td></tr>";
	echo "<tr><td class='fila1'>Tipo</td><td class='fila2'><select name='tipo' id='tipo' onChange='enviarTipo(),validarObservacionSiExiste()'>";
	echo "<option value='Seleccione..'>Seleccione..</option>";
	echo "<option value='Disponibilidad'>Disponibilidad</option>";
	echo "<option value='No disponibilidad'>No disponiblilidad</option>";
	echo "<option value='Observaciones'>Observaciones</option>";
	echo"</td></tr>";

	echo "<tr><td class='fila1'>Fecha Inicial</td><td class='fila2'><input type='text' id='fechai' name='fechai' size='10' onChange='validarObservacionSiExiste();'></td></tr>";
	echo "<tr><td class='fila1'>Fecha Final</td><td class='fila2'><input type='text' id='fechaf' name='fechaf' size='10' onChange='validarObservacionSiExiste();'></td></tr>";

	if ($caso == 2)
	{
		echo "<tr><td class='fila1'>Medico</td>";
	}
	else if ($caso == 1 or $caso == 3)
	{
		echo "<tr><td class='fila1'>Equipo</td>";
	}
	echo"<td class='fila2'><select name='slDoctor' id='slDoctor' onChange='llenarHoras(\"".$solucionCitas."\",\"".$caso."\",\"".$wbasedato."\",\"".$valCitas."\",\"hi\",\"hf\"),validarObservacionSiExiste()'>";
	echo "<option value=''>Seleccione..</option>";

	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){

		if ($caso == 2 and $valCitas=="on")
		{
			//if( $slDoctor != "{$rows['Medcod']} - {$rows['Mednom']}" )
			$rows['Medcod'] = trim( $rows['Medcod'] );
			$rows['Mednom'] = trim( $rows['Mednom'] );
			$rows['Medcid'] = trim( $rows['Medcid'] );
			if( $slDoctor != trim( $rows['Medcod'] )." - ".trim( $rows['Mednom'] ) )
			{
				echo "<option value='".$rows['Medcid']."'>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
			else
			{
				echo "<option value='".$rows['Medcid']."' selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
		}
		else if ($caso == 1 or $caso == 3)
		{
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );

			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			{
				echo "<option value='".$rows['codigo']."'>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option value='".$rows['codigo']."' selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		else if ($caso == 2 and $valCitas!= "on")
		{
			//if( $slDoctor != "{$rows['codigo']} - {$rows['descripcion']}" )

			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );


			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )

			{
				echo "<option value='".$rows['codigo']."'>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option value='".$rows['codigo']."' selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}

	}//for

	echo "</select>";
	echo"</td></tr>";

	if ($caso ==2)
	{
		echo "<tr id='datos' style='display:none'><td class='fila1'>Consultorio</td><td class='fila2'><input type='text' id='consul' name='consul' value='' ></td></tr>";
	}
	else
	{
		echo "<input type='hidden' id='consul' name='consul' value='' ></td></tr>";
	}
	 echo "<tr id='datos1' style='display:none'><td class='fila1'>Tiempo Atencion</td><td class='fila2'><input type='text' id='unihora' name='unihora' value='' ></td></tr>";

	echo "<tr id='datos2' ><td class='fila1'>Hora Inicial</td>";
	echo "<td class='fila2'><select name='hi' id='hi' onChange=''>";
	echo "<option value='Seleccione...'>Seleccione..</option>";
	echo "</select>";
	echo"</td></tr>";

	echo "<tr id='datos3' ><td class='fila1'>Hora Final</td>";
	echo "<td class='fila2'><select name='hf' id='hf' onChange=''>";
	echo "<option value='Seleccione...'>Seleccione..</option>";
	echo "</select>";
	echo"</td></tr>";

	echo "<tr><td class='fila1'>Causa</td><td class='fila2'>
	<textarea id='causa' name='causa' rows='6' cols='50' value=''></textarea></td></tr>";
	echo "<tr><td class='fila1'>Activo</td><td class='fila2'><input type='checkbox' id='chactivo' name='chactivo' value=''></td></tr>";
	echo "<tr><td colspan='2' class='fila2' align='center'><input type='button' value='Enviar' style='width:100' onclick='enviarDatos();'></td></tr>";
	echo "</table>";
	echo "</div>";
	if ($caso==1 or $caso==3)
	{
		$tabla="000021";
	}
	else
	{
		$tabla="000012";
	}

	echo "<div id='msjAlerta2' style='display:none;'>
	        <br>
	        <center><img src='../../images/medical/root/Advertencia.png'/></center>
	        <br><br><center><div id='textoAlerta2' style='font-size: 12pt;'></div></center><br>
	    </div>";

	echo "<div id='link' align='left'><A HREF='../../registro.php?call=1&Form=".$tabla."-".$solucionCitas."-C-EXCEPCIONES&Frm=0&tipo=P&key=".$solucionCitas." id='consulta' target='_blank'>Consultar Excepciones</A><br></div>";
	echo "</form>";

	echo "</body>";
	echo "</html>";


}
?>
