<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");






define("NOMBRE_BORRAR",'Eliminar');
define("NOMBRE_ADICIONAR",'Adicionar');

if (isset($accion) and $accion == 'buscar')
 {  
	
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	$query = '';
	
	
	$query = "select Ccocod, Cconom
			  from ".$cco."
			  where Cconom like '%".$valor."%'
			  and Ccoest = 'on' ";
			
	$res2 = mysql_query( $query ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	$option = '<option value="">Seleccione..</option>';
	while($rows2 = mysql_fetch_array( $res2 ))
	{
		$option .= '<option value="'.$rows2['Ccocod'].'">'.$rows2['Ccocod'].'-'.utf8_encode($rows2['Cconom']).'</option>';
	}
	$data['options'] = $option;
	echo json_encode($data);
	return;
} 

if (isset($accion) and $accion == 'enviar')
 {

	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	
	$ccod1=explode("-",$ccod);
	$ccod=$ccod1[0];
	
	//llama a la funcion perfiles para traer los datos del usuario con su respectivo jefe y el centro de costo al que pertenecen
	$perfil=array();  
	$perfil=perfiles($datos,$wemp_pmla,$key); //no preguntar el cc porque no siempre es el mismo
	//$info=$perfil['Ajeuco']." ".$perfil['Ajeucr']." ".$perfil['Ajecco'];
			
	$coad=explode("-",$perfil['Ajeucr']);
	$coad=$coad[0];
	$niv=1;
	
	//se consulta el consecutivo para guardarlo en la tabla de los eventos gescal_000001
	$sqlcon="select Connum
			from ".$wbasedato."_000006
			where Concod='".$evento."'
			and Conest='on'";
	$rescon = mysql_query( $sqlcon ) or die( mysql_errno()." - Error en el query $sqlcon - ".mysql_error() ); //mirar conex
	$rowscon = mysql_fetch_array($rescon);
	$conse=$rowscon['Connum'];
	$conse++;
		
	if ($evento=="NC")
	{
																				
		$query = "INSERT INTO ".$wbasedato."_000001 (Medico, Fecha_data, Hora_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, 
							Nceest, Ncecne, Nceniv, Ncenum, Seguridad) 
				  VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$ccod."', '".$key."', '".$fechad."', '".$horad."', '".$ccog."', '".utf8_decode($desc)."', '".$coad."', '".$fuente."', 
				 '".$est."', '".$evento."', '".$niv."', '".$conse."', 'C-".$wbasedato."')";
	
		$res2 = mysql_query( $query ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
		
		if (mysql_affected_rows()>0)
		{
			$id_evento=mysql_insert_id();
		}
		/*****Se inserta en la tabla de log**/
		$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
				VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$coad."', '".$key."', '".$est."','".$id_evento."','".$niv."','C-".$wbasedato."' )";
		$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex
		/*******/
	}
	else
	{
		
		//se consulta el codigo de la persona encargada de hacer la seleccion - usuario que controla
		$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Control'
			and Rolest='on'";
		
		$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
		$usu="";
		
			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-"; //Ncecag ccog
			}
		 
		$query = "INSERT INTO ".$wbasedato."_000001 (Medico, Fecha_data, Hora_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, Ncetaf,
									Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, Ncepre, Ncecen, Nceana, 
									Ncecne, Ncecon, Nceres, Nceniv, Ncenum, Seguridad) 
				   VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$ccod."', '".$key."', '".$fechad."', '".$horad."', '".$ccog."', '".utf8_decode($desc)."', '".$coad."', '".$inf."', '".$est."', '".utf8_decode($afec)."', 
							'".utf8_decode($nom_afec)."', '".$ed_afec."', '".$id_afec."', '".$hc."', '".utf8_decode($desc_acc)."', '', '', '', '', '', '', '', 
							'".$evento."', '".$usu."', '".$resp."', '".$niv."', '".$conse."', 'C-".$wbasedato."')";
		
		$res2 = mysql_query( $query ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
	
		if (mysql_affected_rows()>0)
		{
			$id_evento=mysql_insert_id();
		}
		
		/*****Se inserta en la tabla de log**/
		$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
				VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id_evento."','".$niv."','C-".$wbasedato."' )";
		$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex
		/*******/
		
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
	$rescon_update = mysql_query( $sqlcon_update ) or die( mysql_errno()." - Error en el query $sqlcon_update - ".mysql_error() ); //mirar conex
	
	$data['mensaje'] .= utf8_encode("Se guardó correctamente "); //".$query." -".$qInsert."
	echo json_encode($data);
	return;
} 

if (isset($accion) and $accion == 'adicionar_fila')
{
    $data = array('error'=>0,'html'=>'','mensaje'=>'');
    
                $data['html'] = "<tr id='".$id_fila."' class='fila2'>
							<td>
								<input type='hidden' id='".$id_fila."_id' name='".$id_fila."_id' value='' >";

								//se trae la clase de factor
								$sql = "select Tipcod,Tipdes 
											from root_000094 
											where Tiptip='EA' 
											and Tipest='on'
											";		
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
									
								$data['html'] .="<SELECT NAME='".$id_fila."_fact_1' id='".$id_fila."_fact_1' onchange='llenar_hijo(\"".$id_fila."_fact_1\",\"".$id_fila."_cuales\",\"".$key."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla."\",\"".$cco."\")'>"; //llenar_hijo(\"".$id_fila."_cuales\")
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

if (isset($accion) and $accion == 'guardar_planes')
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

if (isset($accion) and $accion == 'rechazada')
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
//se consultan las enviadas
if (isset($accion) and $accion == 'lista_enviadas')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	
		if ($consultar=="enviados") //las enviadas
		{
			
			$sql = "select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,id, Fecha_data, Nceniv,Ncecla,Nceobs, Ncenum,Ncecar, Ncecor,Nceacc,Ncetaf,
					Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana
					from ".$wbasedato."_000001
					where Ncecpd='".$key."'
					and Ncecne='".$evento."'
					order by Ncenum";   
		}
		else if($consultar=="analizados") //las analizadas
		{
			$sql = "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
						Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncecls,Ncetip,Nceeve,Ncesev,Ncepre,Ncecen,Nceana
						FROM  ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
						WHERE  Segant = '".$key."'
						AND  Segest !=  '01'
						AND segeid = a.id
						AND ncecne = '".$evento."'
						GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17
						ORDER BY Ncenum
						";
		}
		else if($consultar=="todos") //todas: las enviadas y las analizadas
		{
			
				$sql = "SELECT Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest, a.id, a.Fecha_data, Nceniv, Ncecla, Nceobs, Ncenum, Ncecar, Ncecor,Nceacc,Ncetaf,
						Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres
						FROM  ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
						WHERE  Segant = '".$key."'
						AND segeid = a.id
						AND ncecne = '".$evento."'
						GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17
						ORDER BY Ncenum
						";
			
		}
		//$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );	
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
					$titulo_tb .="<table align='center' width='100%'><tr class='encabezadotabla' align=center ><td>NOTIFICACIONES ENVIADAS</td></tr></table>";		  
				}
				else if ($consultar =="analizados")
				{
					$titulo_tb .="<table align='center' width='100%'><tr class='encabezadotabla' align=center ><td>NOTIFICACIONES ANALIZADAS</td></tr></table>";
				}
				else if ($consultar =="todos")
				{
					$titulo_tb .="<table align='center' width='100%'><tr class='encabezadotabla' align=center ><td>TODAS LAS NOTIFICACIONES</td></tr></table>";
				}
				
				$tabla .="<tr><td>".$titulo_tb;
				$tabla .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
				$tabla .= "<table align='center' width='100%' >"; //interna
				$caso="enviadas";
				for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
				{
					//nombre del centro de costo que detecto
					$sql1 = "select Cconom
						from ".$cco."
						where Ccocod='".$rows['Nceccd']."'
						and Ccoest='on'";	
			
					$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
					$rows1 = mysql_fetch_array( $res1 );
					
					//nombre de la persona que detecto
					  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
						from ".$datos."_000013,root_000079, ".$cco."
						where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
						and Idecco='".$rows['Nceccd']."'
						and Ideest='on'
						and Ideccg=Carcod
						and Idecco=Ccocod";	
			
					$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
					$rows2 = mysql_fetch_array( $res2 );
					
					$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];

					//nombre del centro de costo que genero
					$sql3 = "select Cconom
						from ".$cco."
						where Ccocod='".$rows['Nceccg']."'
						and Ccoest='on'";	

					$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
					$rows3 = mysql_fetch_array( $res3 );
					
					if ($evento=='NC')
					{
						//se consultan las causas
						$sql4="select Caucod
								from ".$wbasedato."_000002
								where Cautip='".$evento."'
								and Caneid='".$rows['id']."'
								and Cauest='on'";
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
						// else
						// {
							// $data['mensaje'] = "".$sql4."";
							// $data['error'] = 1;
						// }
					}
					else
					{
						//se consultan las causas o factores para llenar el o los select de factores
						$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.id 
						from ".$wbasedato."_000002, root_000091,root_000094 
						where Caneid = '".$rows['id']."'
						and ".$wbasedato."_000002.Caucod=root_000091.Caucod
						and root_000091.Caucla=root_000094.Tipcod
						and root_000091.Cautip=root_000094.Tiptip
						and root_000094.Tipest='on'
						and ".$wbasedato."_000002.Cauest='on'";
						$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
						//$rows4 = mysql_fetch_array( $res4 );
						$causa=array();
						
						for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
						{
							$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['id'];
						}
					}

					//se trae el estado
					$sql5="select Estdes,Estcol
					from root_000093
					where ".$rows['Nceest']."=Estcod
					and Estest='on'";
					$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
					$rows5 = mysql_fetch_array( $res5 );
					
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
							$tabla .= "<tr class='encabezadotabla' align=center>";
								$tabla .= "<td align='center' style=''>Numero</td>";
								$tabla .= "<td align='center' style=''>Fecha Ingreso</td>";
								$tabla .= "<td align='center' style=''>Unidad que detecto</td>";
								$tabla .= "<td align='center' style=''>Unidad que genero</td>";
								$tabla .= "<td align='center' style=''>Estado</td>";
							$tabla .= "</tr>";
						}
						 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est, Ncesev
							// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50 1
						
						$tabla .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
							\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
							\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\" 
							,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
							,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
							,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
							,\"".$rows['id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
							,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\")'>";
							
							$tabla .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
							$tabla .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
							$tabla .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
							$tabla .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";  
							$tabla .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
							//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )." json_encode( $rows['Ncedes'] )
						$tabla .= "</tr>";	
					}//for                     
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
						
				$tabla .="</table></div>"; //tabla interna
				$tabla .="</td></tr></table>"; //tabla externa
				
				
				
				if ($tipo=="menu")
				{
					$data['html'] .= "
						<table align='center' width='100%'>
							<tr><td class='sel_enviadas_color' align='center'>
							Enviadas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_enviados' value='enviadas' checked onclick='nc_enviadas(\"".$datos."\",\"".$key."\",\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"enviados\",\"div_tabla\")'>&nbsp
							Analizadas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_analizados' value='analizadas' onclick='nc_enviadas(\"".$datos."\",\"".$key."\",\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"analizados\",\"div_tabla\")'>&nbsp
							Todas&nbsp<INPUT type='radio' name='sel_enviadas' id='e_todos' value='todos' onclick='nc_enviadas(\"".$datos."\",\"".$key."\",\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$cco."\",\"radio\",\"todos\",\"div_tabla\")'>
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

if (isset($accion) and $accion == 'lista_recibidos')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	if ($evento=="NC")
	{
		$sql = "select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncenum
			from ".$wbasedato."_000001
			where Ncecad='".$key."'
			and Nceest='01'
			and Nceniv='1'
			order by Ncenum";  //jefe inmediato persona que creo el evento
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows($res);
		
	 
	}
	else
	{
		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
								   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
								   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs,Ncenum
				from ".$wbasedato."_000001
				where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
				and Nceest='01'
				order by Ncenum";  //le llega a magda cuando se crea
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );

	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES RECIBIDAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
		
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			if ($evento=="EA")
			{
				//se consultan las causas o factores para llenar el o los select de factores
				$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
				from ".$wbasedato."_000002, root_000091,root_000094 
				where Caneid = '".$rows['Id']."'
				and ".$wbasedato."_000002.Caucod=root_000091.Caucod
				and root_000091.Caucla=root_000094.Tipcod
				and root_000091.Cautip=root_000094.Tiptip
				and root_000094.Tipest='on'
				and ".$wbasedato."_000002.Cauest='on'
				order by ".$wbasedato."_000002.Id"; 
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				//$rows4 = mysql_fetch_array( $res4 );
				$causa=array();
				
				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
			}
			else
			{
				$causa="";
			}
		
			$caso="recibidos";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				// nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
				// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"\",".json_encode( $causa ).",\"".$caso."\")'>";

					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";  
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS</th></table>";
		}
				
		$data['html'] .="</table></div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
		
	
	echo json_encode($data);
	return;
} 

if (isset($accion) and $accion == 'lista_recibidos_u_genero')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	if ($evento=="NC")
	{
			$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncenum
			from ".$wbasedato."_000001
			where Ncecag='".$key."'
			and Nceest='02' 
			and (Nceniv='2' or Nceniv='4')
			order by Ncenum";  //coordinador area que genero
			
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows($res);
			// if ($num==0)
			// {
				// $sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncenum
				// from ".$wbasedato."_000001
				// where Ncecag='".$key."'
				// and Nceest='02'
				// and Nceniv='4'
				// order by Ncenum"; //coordinador area genero le llega por segunda vez
				// $res= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				// $num = mysql_num_rows($res);
				
			// }
			
		
		
	 
	}
	else
	{
					$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs,Ncenum
							from ".$wbasedato."_000001
							where Ncecag='".$key."'
							and Nceest='02'
							and Nceniv='3'
							order by Ncenum";  //le llega al coordinador de la unidad que genero
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					$num = mysql_num_rows( $res );
					if ($num==0)
					{
						$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs,Ncenum
								from ".$wbasedato."_000001
								where Ncecag='".$key."'
								and Nceest='02'
								and Nceniv='5'
								order by Ncenum";  //le llega al coordinador de la unidad que genero segunda
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$num = mysql_num_rows( $res );
						
					}	
			
	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES RECIBIDAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
		
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			if ($evento=="EA")
			{
				//se consultan las causas o factores para llenar el o los select de factores
				$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
				from ".$wbasedato."_000002, root_000091,root_000094 
				where Caneid = '".$rows['Id']."'
				and ".$wbasedato."_000002.Caucod=root_000091.Caucod
				and root_000091.Caucla=root_000094.Tipcod
				and root_000091.Cautip=root_000094.Tiptip
				and root_000094.Tipest='on'
				and ".$wbasedato."_000002.Cauest='on'
				order by ".$wbasedato."_000002.Id"; 
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				
				$causa=array();
				
				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
			}
			else
			{
				$causa="";
			}
			$caso="recibidos_u_genero";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50 
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"\",\"\",".json_encode( $causa ).",\"".$caso."\")'>";
					
					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS</th></table>";
		}
				
		$data['html'] .="</table></div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
		
	
	echo json_encode($data);
	return;
} 

if (isset($accion) and $accion == 'lista_recibidos_u_control')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	
	if($evento=="EA")
	{
		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
								   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
								   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncenum
						from ".$wbasedato."_000001
						where (Nceuvi like '%-".$key."-%' OR Nceuvi LIKE  '%".$key."%')
						and Nceest='02'
						and Nceniv='2'
						order by Ncenum";  //le llega a invecla/santiago/magda/alejandro 
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );				
			
	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div' style='".(($num>10) ? 'height: 500px;overflow:scroll;' : '')."'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES RECIBIDAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
		
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			//se consultan las causas o factores para llenar el o los select de factores
			$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
			from ".$wbasedato."_000002, root_000091,root_000094 
			where Caneid = '".$rows['Id']."'
			and ".$wbasedato."_000002.Caucod=root_000091.Caucod
			and root_000091.Caucla=root_000094.Tipcod
			and root_000091.Cautip=root_000094.Tiptip
			and root_000094.Tipest='on'
			and ".$wbasedato."_000002.Cauest='on'
			order by ".$wbasedato."_000002.Id"; 
			$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
			//$rows4 = mysql_fetch_array( $res4 );
			$causa=array();
			
			for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
			{
				$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
			}
			
			$caso="recibidos_u_control";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"\",\"\",".json_encode( $causa ).",\"".$caso."\")'>";
					
					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";   
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS</th></table>";
		}
				
		$data['html'] .="</table></div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
		
	
	echo json_encode($data);
	return;
} 

if (isset($accion) and $accion == 'lista_recibidos_correccion')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	//$opciones = array();
	
	if ($evento=="NC")
	{
		
		$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecor,Nceacc, Ncenum
		from ".$wbasedato."_000001
		where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
		and Nceest='04'
		and Nceniv='5'
		order by Ncenum";  //alejandro correccion ya se hizo la correccion por el coordinador area que genero
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows($res);
		
		
		/********************************************************************************************/
	 
	}
	else
	{
							
		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
				   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
				   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncecor, Nceacc,Ncenum
				from ".$wbasedato."_000001
				where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
				and Nceest='04'
				and Nceniv='6'
				order by Ncenum";  //le llega a magda por correccion
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		
	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES RECIBIDAS EN CORRECCION</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
		
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			
			if ($evento=='NC')
			{
				//se consultan las causas
				$sql4="select Caucod
						from ".$wbasedato."_000002
						where Cautip='".$evento."'
						and Caneid='".$rows['Id']."'
						and Cauest='on'";
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
			}
			else
			{
				//se consultan las causas o factores para llenar el o los select de factores
				$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
				from ".$wbasedato."_000002, root_000091,root_000094 
				where Caneid = '".$rows['Id']."'
				and ".$wbasedato."_000002.Caucod=root_000091.Caucod
				and root_000091.Caucla=root_000094.Tipcod
				and root_000091.Cautip=root_000094.Tiptip
				and root_000094.Tipest='on'
				and ".$wbasedato."_000002.Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				//$rows4 = mysql_fetch_array( $res4 );
				$causa=array();
				
				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
			}
			$caso="recibidos_co";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50 7
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos( this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\")'>";
					
					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";  
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS EN CORRECCION</th></table>";
		}
				
		$data['html'] .="</table></div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
	
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'lista_recibidos_accion')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	if ($evento=="NC")
	{
		$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecor,Nceacc, Ncenum
			from ".$wbasedato."_000001
			where (Nceuvi like '%-".$key."-%' OR Nceuvi LIKE  '%".$key."%')
			and Nceest='05'
			and Nceniv='6'
			and Nceacc='Si'
			order by Ncenum";  //angela acciones si la nc tiene acciones 
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num= mysql_num_rows($res);
			
	}
	else
	{					
		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
				   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
				   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs,Nceobs,Nceacc,Ncecor, Ncenum
				from ".$wbasedato."_000001
				where (Nceuvi like '%-".$key."-%' OR Nceuvi LIKE  '%".$key."%')
				and Nceest='05'
				and Nceniv='7'
				and Nceacc='Si'
				order by Ncenum";  //le llega magda acciones
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
			
	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES RECIBIDAS EN ACCION</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
		
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			if ($evento=='NC')
			{
				//se consultan las causas
				$sql4="select Caucod
						from ".$wbasedato."_000002
						where Cautip='".$evento."'
						and Caneid='".$rows['Id']."'
						and Cauest='on'";
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
			}
			else
			{
				//se consultan las causas o factores para llenar el o los select de factores
				$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
				from ".$wbasedato."_000002, root_000091,root_000094 
				where Caneid = '".$rows['Id']."'
				and ".$wbasedato."_000002.Caucod=root_000091.Caucod
				and root_000091.Caucla=root_000094.Tipcod
				and root_000091.Cautip=root_000094.Tiptip
				and root_000094.Tipest='on'
				and ".$wbasedato."_000002.Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				//$rows4 = mysql_fetch_array( $res4 );
				$causa=array();
				
				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
			}
			$caso="recibidos_ac";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"\",".json_encode( $causa ).",\"".$caso."\")'>";
					
					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";  
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECIBIDAS EN ACCION</th></table>";
		}
				
		$data['html'] .="</table><div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
	
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'lista_recibidas_rechazadas')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	if ($evento=="NC")
	{
		
		$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecar, Ncenum
				from ".$wbasedato."_000001
				where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
				and Nceest='03'
				and Nceniv != '2'
				and Nceniv != '9'
				order by Ncenum";  //coordinador area que genero rechaza
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);
					//se coloco el nivel 2 o 9 porque es donde muere el proceso
	 
	}
	else
	{
							
		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncenum, Ncecar
								from ".$wbasedato."_000001
								where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
								and Nceest='03'
								and Nceniv != '2'
								and Nceniv != '9'
								order by Ncenum";  //le llega a magda porque el coordinador de la unidad rechaza
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		 //se coloco el nivel 2 y nivel 9
	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES RECIBIDAS RECHAZADAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
			
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			$caso="recibidas_re";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
			if ($evento=="EA")
			{
				//se consultan las causas o factores para llenar el o los select de factores
				$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
				from ".$wbasedato."_000002, root_000091,root_000094 
				where Caneid = '".$rows['Id']."'
				and ".$wbasedato."_000002.Caucod=root_000091.Caucod
				and root_000091.Caucla=root_000094.Tipcod
				and root_000091.Cautip=root_000094.Tiptip
				and root_000094.Tipest='on'
				and ".$wbasedato."_000002.Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				
				$causa=array();
			
				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
			}
			else
			{
				$causa="";
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50 **
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"\",\"\",".json_encode( $causa ).",\"".$caso."\")'>";
					
					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";   
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECHAZADAS</th></table>";
		}
				
		$data['html'] .="</table><div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
	
	echo json_encode($data);
	return;
}


if (isset($accion) and $accion == 'lista_cerradas')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	if ($evento=="NC")
	{
		
		$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,a.Id, a.Fecha_data, Nceniv,Ncecla,Nceobs,Ncecar, Ncenum, Nceacc, Ncecor
				from ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
				where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
				and Nceest='06'
				and Nceniv='7'
				and Ncecne = '".$evento."'
				AND TIMESTAMPDIFF( MONTH , b.fecha_data,  '".date("Y-m-d")."' ) <=2
				AND Segest = Nceest
				AND Segeid = a.id
				order by Ncenum";  //mostrar las cerradas
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);
					
	 
	}
	else
	{
							
		$sql = "select a.Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, a.Id, Nceres, Nceniv, Nceobs, Ncenum, Nceacc, Ncecor
								from ".$wbasedato."_000001 a, ".$wbasedato."_000005 b
								where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
								and Nceest='06'
								and Nceniv='8'
								and Ncecne = '".$evento."'
								AND TIMESTAMPDIFF( MONTH , b.fecha_data,  '".date("Y-m-d")."' ) <=2
								AND Segest = Nceest
								AND Segeid = a.id
								order by Ncenum";  //se muestran las cerradas
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES CERRADAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
			
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			$caso="cerradas";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
			if ($evento=='NC')
			{
				//se consultan las causas
				$sql4="select Caucod
						from ".$wbasedato."_000002
						where Cautip='".$evento."'
						and Caneid='".$rows['Id']."'
						and Cauest='on'";
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
			}
			else
			{
				//se consultan las causas o factores para llenar el o los select de factores
				$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
				from ".$wbasedato."_000002, root_000091,root_000094 
				where Caneid = '".$rows['Id']."'
				and ".$wbasedato."_000002.Caucod=root_000091.Caucod
				and root_000091.Caucla=root_000094.Tipcod
				and root_000091.Cautip=root_000094.Tiptip
				and root_000094.Tipest='on'
				and ".$wbasedato."_000002.Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				//$rows4 = mysql_fetch_array( $res4 );
				$causa=array();
				
				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"".$opciones."\",\"".$rows['Nceacc']."\",".json_encode( $causa ).",\"".$caso."\")'>";
					
					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";   
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES CERRADAS</th></table>";
		}
				
		$data['html'] .="</table></div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
	
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'lista_revisadas')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	if ($evento=="NC")
	{
		
		$sql="select Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncecad, Ncefue, Nceest,Id, Fecha_data, Nceniv,Ncecla,Nceobs,Ncecar, Ncenum
				from ".$wbasedato."_000001
				where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
				and Nceest='03'
				and Nceniv='3'
				order by Ncenum";  //coordinador area que genero rechaza
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);
					
	 
	}
	else
	{
							
		$sql = "select Fecha_data, Nceccd, Ncecpd, Ncefed, Ncehod, Nceccg, Ncedes, Ncefue, Nceest, Ncetaf,
									   Ncenaf, Nceeaf, Nceiaf, Ncehca, Nceaci, Ncecls, Ncetip, Nceeve, Ncesev, 
									   Ncepre, Ncecen, Nceana, Ncecne, Ncecon, Id, Nceres, Nceniv, Nceobs, Ncenum, Ncecar
								from ".$wbasedato."_000001
								where (Ncecon like '%-".$key."-%' OR Ncecon LIKE  '%".$key."%')
								and Nceest='03'
								and Nceniv='4'
								order by Ncenum";  //le llega a magda porque el coordinador de la unidad rechaza
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
	}
	
	if ($num > 0)
	{
	
		$data['html'] = ""; 
		$data['html'] .= "<div id='recididas' class='class_div'>";
		$data['html'] .="<INPUT type='hidden' name='niv_id' id='niv_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='est_id' id='est_id' value=''>";
		$data['html'] .="<INPUT type='hidden' name='estadoA' id='estadoA' value=''>";
		$data['html'] .= "<table align='center' width='1150' >"; //externa
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>NOTIFICACIONES RECIBIDAS RECHAZADAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div style='width: 100%;".(($num>10) ? 'height: 180px;overflow:scroll;' : '')."'>";
		$data['html'] .= "<table align='center' width='100%' >"; //interna
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ )
		{
			
			//nombre del centro de costo que detecto
			$sql1 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccd']."'
				and Ccoest='on'";	
	
			$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows1 = mysql_fetch_array( $res1 );
			
			//nombre de la persona que detecto
			  $sql2 = "select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
				from ".$datos."_000013,root_000079, ".$cco."
				where Ideuse='".substr($rows['Ncecpd'],-5)."-".$wemp_pmla."' 
				and Idecco='".$rows['Nceccd']."'
				and Ideest='on'
				and Ideccg=Carcod
				and Idecco=Ccocod";	
	
			$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows2 = mysql_fetch_array( $res2 );
			
			$nom=$rows2['Ideno1']." ".$rows2['Ideno2']." ".$rows2['Ideap1']." ".$rows2['Ideap2'];
			
			//nombre del centro de costo que genero
			$sql3 = "select Cconom
				from ".$cco."
				where Ccocod='".$rows['Nceccg']."'
				and Ccoest='on'";	
	
			$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows3 = mysql_fetch_array( $res3 );
			
			$caso="recibidas_re";
			
			//se trae el estado
			$sql5="select Estdes,Estcol
			from root_000093
			where ".$rows['Nceest']."=Estcod
			and Estest='on'";
			$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			$rows5 = mysql_fetch_array( $res5 );
			
			if ($evento=="EA")
			{
				//se consultan las causas o factores para llenar el o los select de factores
				$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id 
				from ".$wbasedato."_000002, root_000091,root_000094 
				where Caneid = '".$rows['Id']."'
				and ".$wbasedato."_000002.Caucod=root_000091.Caucod
				and root_000091.Caucla=root_000094.Tipcod
				and root_000091.Cautip=root_000094.Tiptip
				and root_000094.Tipest='on'
				and ".$wbasedato."_000002.Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				
				$causa=array();
			
				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
			}
			else
			{
				$causa="";
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
					$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Numero</td>";
						$data['html'] .= "<td align='center' style=''>Fecha Ingreso</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que detecto</td>";
						$data['html'] .= "<td align='center' style=''>Unidad que genero</td>";
						$data['html'] .= "<td align='center' style=''>Estado</td>";
					$data['html'] .= "</tr>";
				}
				 // nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					// tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,cent,anali,evento,ucon,id width:50
				
				$data['html'] .= "<tr style='cursor:pointer' $class align=center onclick='llenarDatos(this,\"".utf8_encode($nom)."\",\"".$rows['Nceccd']."\",\"".$rows1['Cconom']."\",\"".$rows2['Cardes']."\",\"".$rows2['Ideext']."\",
					\"".$rows2['Ideeml']."\",\"".$rows['Ncefed']."\",\"".$rows['Ncehod']."\",\"".$rows['Ncefue']."\",\"".$rows['Nceccg']."\",\"".$rows3['Cconom']."\",
					\"".utf8_encode( $rows['Ncedes'] )."\",\"".$rows['Nceest']."\"
					,\"".utf8_encode($rows['Ncetaf'])."\",\"".$rows['Ncenaf']."\",\"".$rows['Nceeaf']."\",\"".$rows['Nceiaf']."\",\"".$rows['Ncehca']."\"
					,\"".utf8_encode($rows['Nceaci'])."\",\"".$rows['Ncecls']."\",\"".$rows['Ncetip']."\",\"".$rows['Nceeve']."\",\"".utf8_encode($rows['Ncesev'])."\"
					,\"".$rows['Ncepre']."\",\"".$rows['Ncecen']."\",\"".str_replace( "\n","\\n", utf8_encode($rows['Nceana']))."\",\"".$rows['Ncecne']."\",\"".$rows['Ncecon']."\"
					,\"".$rows['Id']."\",\"".$rows['Nceres']."\",\"".$rows['Nceniv']."\",\"".$rows['Ncecla']."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode( $rows['Nceobs'] ) ) )."\"
					,\"".utf8_encode($rows['Ncecor'])."\",\"".str_replace( "\r","\\r",str_replace( "\n","\\n", utf8_encode($rows['Ncecar'] ) ) )."\",\"\",\"\",".json_encode( $causa ).",\"".$caso."\")'>";
					
					$data['html'] .= "<td align='center' style=''>".$rows['Ncenum']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Fecha_data']."</td>";
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccd']."-".$rows1['Cconom']."</td>"; 
					$data['html'] .= "<td align='center' style=''>".$rows['Nceccg']."-".$rows3['Cconom']."</td>";   
					$data['html'] .= "<td align='center' style='' bgcolor=\"".$rows5['Estcol']."\">".$rows5['Estdes']."</td>";
					//se hace el str_replace para que a js le llegue \n e interprete el enter  ".str_replace( "\n","\\n", $rows['Nceobs'] )."
				$data['html'] .= "</tr>";			        					
			}//for                     
		}//$num
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE NOTIFICACIONES RECHAZADAS</th></table>";
		}
				
		$data['html'] .="</table></div>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";   
	
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'editar') //Ncetaf
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	
	//se hace una consulta a la tabla usuarios para traer el nombre
	$queryU = "select Descripcion
			   from Usuarios
			   where Codigo = '".$key."'";
	$resU = mysql_query($queryU) or die( mysql_errno()." - Error en el query $queryU - ".mysql_error() );
    if (mysql_num_rows($resU)>0)
	{
		$rowsU = mysql_fetch_array($resU);
		$nombre=$rowsU['Descripcion'];
		
	}
    /* concatenar observaciones*/	
	$fecha=date("Y-m-d");
	$hora=date("H:i:s");
	$pieComen="-------------------------------------------------------------------------------";
	$cadena=utf8_decode( $nombre )." ".$fecha." ".$hora."\n".utf8_decode($obs)."\n".$pieComen;
	$obs=$cadena;
	/* fin concatenar observaciones*/
	
	/* concatenar causas rechazo*/	
	$fecha=date("Y-m-d");
	$hora=date("H:i:s");
	$pieComen="-------------------------------------------------------------------------------";
	$cadena1=utf8_decode( $nombre )." ".$fecha." ".$hora."\n".utf8_decode($rechazada)."\n".$pieComen;
	$rechazada=$cadena1;
	/* fin causas rechazo rechazada*/
	
	if ($evento=="NC")
	{
	 	
		if ($est=='02' and $niv=='2')  //se le manda coordinador unidad que genero 
		{	
			//se busca el coordinador del area que genero
			$q="select Ideuse,Idecco,Ajeucr
				from ".$datos."_000008, ".$datos."_000013
				where Ideuse=Ajeucr
				and Forest='on'
				and Ideest='on'
				and Ajecoo='C'
				and Idecco='".$ccg."'
				group by Ideuse";
			
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
			if (mysql_num_rows($res)>0) 
			{
				$rows1 = mysql_fetch_array($res);
				
				$cag=explode("-",$rows1['Ideuse']);
				$cag=$cag[0];
			}
			
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'),  
			Nceest='".$est."', 
			Ncecla='".$clasif."', 
			Ncecag='".$cag."', 
			Nceniv='".$niv."' 
			WHERE Id='".$id."' ";
			
			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$cag."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		if ($est=='03' and $niv=='2')  //el jefe inmediato rechaza
		{	
			
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
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
		if ($est=='03' and $niv=='3') //coordinador del area que genero rechaza se le envia a alejandro
		{	
		
			$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Control'
			and Rolest='on'"; //se le envia a alejandro
		
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$usu="";
		
			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
			
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
			Nceest='".$est."', 
			Ncecon='".$usu."', 
			Nceniv='".$niv."',
			Ncecar=concat( Ncecar,'\n', '".$rechazada."')
			WHERE Id='".$id."' ";  
			
			/*****Se inserta en la tabla de log**/
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		// if ($est=='03' and $niv=='4') //alejandro rechaza
		// {	
		
			// $query="UPDATE ".$wbasedato."_000001 
			// SET  Nceobs = '".$obs."', 
			// Nceest='".$est."', 
			// Nceniv='".$niv."',
			// Ncecar='".$rechazada."'
			// WHERE Id='".$id."' ";
			
			// /*****Se inserta en la tabla de log    NO SE ENVIA USUARIO ACTUAL PORQUE AHI MUERE EL PROCESO**/
			// $sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					// VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			// $resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			// /*******/
		// }
		if ($est=='03' and $niv=='9')  //rechaza alejandro despues que rechazo unidad que genero
		{	
			
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
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
		if ($est=='02' and $niv=='4' )  //se le manda al coordinador del area que genero segunda vez
		{	
			
			//se busca el coordinador del area que genero
			// $q="select Ideuse,Idecco,Ajeucr
				// from ".$datos."_000008, ".$datos."_000013
				// where Ideuse=Ajeucr
				// and Forest='on'
				// and Ideest='on'
				// and Ajecoo='C'
				// and Idecco='".$ccg."'
				// group by Ideuse";
			
			// $res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
			// if (mysql_num_rows($res)>0) 
			// {
				// $rows1 = mysql_fetch_array($res);
				
				// $cag=explode("-",$rows1['Ideuse']);
				// $cag=$cag[0];
			// }
			
			
			//ya tiene el codigo del coordinador del area que genero
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'),
			Nceest='".$est."',
			Nceniv='".$niv."' 
			WHERE Id='".$id."' ";
			
			/*****Se inserta en la tabla de log**/ //PROBAR SI LE LLEGA EL $cag y si no se envia otra vez
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$cag."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/
		}
		if ($est=='04' and $niv=='5')  //se le manda a alejandro en correccion
		{	
			$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Control'
			and Rolest='on'"; //se le envia a alejandro
		
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$usu="";
		
			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
			
			//ya tiene el codigo de alejando Ncecon
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
			Nceest='".$est."', 
			Nceniv='".$niv."', 
			Ncecon='".$usu."',
			Ncecor='".utf8_decode($correc)."',
			Nceacc='".utf8_decode($accionc)."'
			WHERE Id='".$id."' "; 
			
			/*****Se inserta en la tabla de log**/ 
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
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
		if ($est=='05' and $niv=='6')  //se le manda a angela con la accion
		{	
		
			$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Accion'
			and Rolest='on'";
		
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$usu="";
		
			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
			//ya tiene el codigo de alejando Ncecon
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
			Nceest='".$est."', 
			Nceuvi='".$usu."', 
			Nceniv='".$niv."' 
			WHERE Id='".$id."' ";
			
			/*****Se inserta en la tabla de log**/ 
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
			/*******/	
		}
		if ($est=='06' and $niv=='7') //se cierra
		{
			$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Control'
			and Rolest='on'";
		
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$usu="";
		
			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
			//ya tiene el codigo de alejando Ncecon
			$query="UPDATE ".$wbasedato."_000001 
			SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
			Nceest='".$est."', 
			Nceuvi='".$usu."', 
			Nceniv='".$niv."',
			Nceacc='".utf8_decode($accionc)."'
			WHERE Id='".$id."' ";
			
			/*****Se inserta en la tabla de log**/ 
			$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
			$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); //mirar conex
			/*******/
		}
	}  
	else
	{ //eventos adversos Ncecag
		
		if ($est=='02' and $niv=='2') //le manda a las unidades que controla invecla/santiago/magda
		{
			if ($inf=='Infeccioso') //se busca el encargado de controlar lo infeccioso
			{
				$q="select Rolcod
				from ".$wbasedato."_000004
				where Roltip='".$evento."'
				and Roldes='".$inf."'
				and Rolest='on'";
				
				$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{
					$usu.="-".$rows1['Rolcod']."-";
				}	
		
				$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Nceuvi='".$usu."', 
				Nceniv='".$niv."',
				Ncedes='".utf8_decode($desc)."',
				Nceaci='".utf8_decode($desc_acc)."',
				Ncecls='".utf8_decode($clase_even)."',
				Ncetip='".utf8_decode($tipo_even)."',
				Nceeve='".utf8_decode($even)."',
				Ncesev='".utf8_decode($sev)."',
				Ncepre='".$prev."',
				Ncecen='".$cent."',
				Ncefue='".$inf."',
				Nceana='".utf8_decode($analiz)."',
				Ncetaf='".utf8_decode($afec)."',
				Ncenaf='".utf8_decode($nom_afec)."',
				Nceeaf='".$ed_afec."',
				Nceiaf='".$id_afec."',
				Ncehca='".$hc."',
				Nceres='".utf8_decode($resp)."'
				WHERE Id='".$id."' "; 
				
				/*****Se inserta en la tabla de log**/ 
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
				
				
			}
			else if($inf=='No Infeccioso')
			{
				$q="select Rolcod
				from ".$wbasedato."_000004
				where Roltip='".$evento."'
				and Roldes='".$inf."'
				and Rolest='on'";
				
				$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{
					$usu.="-".$rows1['Rolcod']."-";
				}	
				
				$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Nceuvi='".$usu."', 
				Nceniv='".$niv."',
				Ncedes='".utf8_decode($desc)."',
				Nceaci='".utf8_decode($desc_acc)."',
				Ncecls='".utf8_decode($clase_even)."',
				Ncetip='".utf8_decode($tipo_even)."',
				Nceeve='".utf8_decode($even)."',
				Ncesev='".utf8_decode($sev)."',
				Ncepre='".$prev."',
				Ncecen='".$cent."',
				Ncefue='".$inf."',
				Nceana='".utf8_decode($analiz)."',
				Ncetaf='".utf8_decode($afec)."',
				Ncenaf='".utf8_decode($nom_afec)."',
				Nceeaf='".$ed_afec."',
				Nceiaf='".$id_afec."',
				Ncehca='".$hc."',
				Nceres='".utf8_decode($resp)."'
				WHERE Id='".$id."' "; 
				
				/*****Se inserta en la tabla de log**/ 
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
				
			}
			else if($inf=='Relacionado con medicamentos o medios')
			{
				$q="select Rolcod
				from ".$wbasedato."_000004
				where Roltip='".$evento."'
				and Roldes='Medicamentos'
				and Rolest='on'";
				
				$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{
					$usu.="-".$rows1['Rolcod']."-";
				}	
				
				$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Nceuvi='".$usu."', 
				Nceniv='".$niv."',
				Ncedes='".utf8_decode($desc)."',
				Nceaci='".utf8_decode($desc_acc)."',
				Ncecls='".utf8_decode($clase_even)."',
				Ncetip='".utf8_decode($tipo_even)."',
				Nceeve='".utf8_decode($even)."',
				Ncesev='".utf8_decode($sev)."',
				Ncepre='".$prev."',
				Ncecen='".$cent."',
				Ncefue='".$inf."',
				Nceana='".utf8_decode($analiz)."',
				Ncetaf='".utf8_decode($afec)."',
				Ncenaf='".utf8_decode($nom_afec)."',
				Nceeaf='".$ed_afec."',
				Nceiaf='".$id_afec."',
				Ncehca='".$hc."',
				Nceres='".utf8_decode($resp)."'
				WHERE Id='".$id."' "; 
				
				/*****Se inserta en la tabla de log**/ 
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
			}
			
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
						WHERE id='".$id_causa."'
						"; 
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
		if ($est=='02' and $niv=='3') //le manda a unidad que genero
		{
			//se busca el coordinador del area que genero
			$q="select Ideuse,Idecco,Ajeucr
				from ".$datos."_000008, ".$datos."_000013
				where Ideuse=Ajeucr
				and Forest='on'
				and Ideest='on'
				and Ajecoo='C'
				and Idecco='".$ccg."'
				group by Ideuse";
			
			$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
			if (mysql_num_rows($res1)>0) 
			{
				$rows1 = mysql_fetch_array($res1);
				
				$cag=explode("-",$rows1['Ideuse']);
				$cag=$cag[0];
			}
			
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Ncecag='".$cag."', 
				Nceniv='".$niv."'
				WHERE Id='".$id."' ";
				
				/*****Se inserta en la tabla de log**/ 
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$cag."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		
		if ($est=='03' and $niv=='2') //magda rechaza
		{
			
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
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
		if ($est=='03' and $niv=='3') //unidad control rechaza
		{
			$q="select Rolcod
				from ".$wbasedato."_000004
				where Roltip='".$evento."'
				and Roldes='Control'
				and Rolest='on'";
				
				$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{
					$usu.="-".$rows1['Rolcod']."-";
				}	
			
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Ncecon='".$usu."', 
				Nceniv='".$niv."',
				Ncecar=concat( Ncecar,'\n', '".$rechazada."')
				WHERE Id='".$id."' ";
				
				/*****Se inserta en la tabla de log**/ 
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='03' and $niv=='4') //le manda a magda porque la unidad rechazo
		{
			$q="select Rolcod
				from ".$wbasedato."_000004
				where Roltip='".$evento."'
				and Roldes='Control'
				and Rolest='on'";
				
				$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );	
			
				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{
					$usu.="-".$rows1['Rolcod']."-";
				}	
			
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Ncecon='".$usu."', 
				Nceniv='".$niv."',
				Ncecar=concat( Ncecar,'\n', '".$rechazada."')
				WHERE Id='".$id."' ";
				
				/*****Se inserta en la tabla de log**/ 
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='03' and $niv=='9') //magda rechaza despues de que unidad genero rechaza
		{
			
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
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
		if ($est=='02' and $niv=='5') //le manda a la unidad que genero por segunda vez
		{
			//ya se tiene el codigo del coordinador	
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Nceniv='".$niv."'
				WHERE Id='".$id."' ";
				
				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mandarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$cag."', '".$key."', '".$est."','".$id."','".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='04' and $niv=='6') //le manda a magda con correccion
		{
			$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Control'
			and Rolest='on'"; 
		
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$usu="";
		
			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
				
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Ncecon='".$usu."',
				Nceniv='".$niv."',
				Ncecor='".utf8_decode($correc)."',
				Nceacc='".utf8_decode($accionc)."'
				WHERE Id='".$id."' ";
				
				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mandarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv,Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."', '".$niv."','C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='05' and $niv=='7') //le manda a magda con accion
		{
			$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Accion'
			and Rolest='on'"; 
		
			$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
		
			for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
				
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Nceuvi='".$usu."',
				Nceniv='".$niv."',
				Nceacc='".utf8_decode($accionc)."'
				WHERE Id='".$id."' ";
				
				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mandarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."', '".$niv."', 'C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
		if ($est=='06' and $niv=='8') //cerrar
		{
			$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Control'
			and Rolest='on'"; 
		
			$res1 = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
		
			for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
				
			$query="UPDATE ".$wbasedato."_000001 
				SET  Nceobs = concat( Nceobs,'\n', '".$obs."'), 
				Nceest='".$est."', 
				Nceuvi='".$usu."',
				Nceniv='".$niv."',
				Nceacc='".utf8_decode($accionc)."'
				WHERE Id='".$id."' ";
				
				/*****Se inserta en la tabla de log**/  //REVISAR SE LE LLEGA EL $cag o si no mandarlo
				$sql=" INSERT INTO ".$wbasedato."_000005 (Medico, Fecha_data, Hora_data, Segant, Segact, Segusu, Segest, Segeid, Segniv, Seguridad)
						VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".$usu."', '".$key."', '".$est."','".$id."', '".$niv."', 'C-".$wbasedato."' )";
				$resSql = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() ); //mirar conex
				/*******/
		}
	} //EA
	
		if($query != "")
		{
			if($res2 = mysql_query( $query ))
			{
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

	
	// if (mysql_affected_rows()>0)
	// {
		  //".$est."-".$niv."-".$query."-".$output." ".$instruccion_insert." ".$qUpdate."-".$qInsert."
	// }
	  
	
	echo json_encode($data);
	return;
}


if (isset($accion) and $accion == 'cargar')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	
	
		$data['mensaje'] = "Se ha ingresado correctamente";
	
	
	
	echo json_encode($data);
	return;
} 

if (isset($accion) and $accion == 'llenar_select_hijo')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	
		//se trae la clase de factor
								$sql = "select Caucod,Caudes 
											from root_000091 
											where Caucla='".$id_padre."' 
											and Cauest='on'
											and Cautip='".$evento."'
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

if (isset($accion) and $accion == 'llenar_select_evento')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	
		
								$sql = "select Caucod,Caudes 
											from root_000091 
											where Caucla='".$id_padre."' 
											and Cauest='on'
											and Cautip='EAG'
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


if (isset($accion) and $accion == 'evaluarEstado')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'','validacion'=>0);
	
								
				if($evento=="NC" )  
				{
					if( $nivel=='6') //nivel 6 acciones puede cerrar
					{
						//se consultan los estados de las acciones de ese evento
						$sql = "SELECT COUNT( * ) 
							FROM  ".$wbasedato."_000003, ".$wbasedato."_000002
							WHERE accest !='10'
							AND ".$wbasedato."_000003.accaid = ".$wbasedato."_000002.Id
							AND ".$wbasedato."_000002.caneid ='".$id_evento."'
							and ".$wbasedato."_000003.accesa='on'
							";		
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
							";		
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$rows = mysql_fetch_array( $res );
						if($rows['Nceacc']=="Si" and $radio_acciones=="Si")
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
							FROM  ".$wbasedato."_000003, ".$wbasedato."_000002
							WHERE accest !='10'
							AND ".$wbasedato."_000003.accaid = ".$wbasedato."_000002.Id
							AND ".$wbasedato."_000002.caneid ='".$id_evento."'
							and ".$wbasedato."_000003.accesa='on'
							";		
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
							";		
						$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$rows = mysql_fetch_array( $res );
						if($rows['Nceacc']=="Si" and $radio_acciones=="Si")
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
			
	
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'eliminar_factores')
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


if (isset($accion) and $accion == 'consultar_roles')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','menus'=>'');
	
	$tipo=0;
		//se consulta el tipo de usuario y despues se consulta si ademas tiene rol
			$sql = "select Ajeucr
					from ".$datos."_000008 
					where Ajeucr='".substr($key,-5)."-".$wemp_pmla."'
					and Ajecoo ='C'
					"; //si es coordinador
										
			$res = mysql_query( $sql ,$conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
			$num = mysql_num_rows($res);
			$tipo=1;
			if ($num==0)
			{
				$sql="select Ajeucr
					from ".$datos."_000008 
					where Ajeucr='".substr($key,-5)."-".$wemp_pmla."'
					and Ajecoo !='C'";  //si es jefe
				
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows($res);
				$tipo=2;
				if ($num==0)
				{
					$sql="select Ajeuco
					from ".$datos."_000008 
					where Ajeuco='".substr($key,-5)."-".$wemp_pmla."'
					"; //si es empleado
					$res= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					$num = mysql_num_rows($res);
					$tipo=3;
				}
				
			
			}
		
		if ($num > 0)
		{
			$tipo_usu=$tipo;
		}
		
				
		if($evento=="NC")
		{
			//se agregan los menus que ve de pendiendo del tipo de usuario
			if($tipo_usu==1) //coordinador
			{
				$data['menus'][]="td_nc_ingresar";
				$data['menus'][]="td_nc_enviadas";
				$data['menus'][]="td_nc_recibidas";
				$data['menus'][]="td_nc_recibidas_u_genero";
			}
			elseif($tipo_usu==2) //jefe inmediato
			{
				$data['menus'][]="td_nc_ingresar";
				$data['menus'][]="td_nc_enviadas";
				$data['menus'][]="td_nc_recibidas";
			}
			elseif($tipo_usu==3) //empleado
			{
				$data['menus'][]="td_nc_ingresar";
				$data['menus'][]="td_nc_enviadas";
			}
			
			//se consulta el rol
			$sql1="select Roldes
				from ".$wbasedato."_000004 
				where Roltip='".$evento."'
				and Rolcod ='".$key."'
				";
			$res1= mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num1 = mysql_num_rows($res1);
			$rol="";
			
			if ($num1 > 0)
			{
				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{
					// if($rows1['Roldes']=="Auditoria")
					// {
						 // $data['menus'][]="td_nc_rechazadas"; //se agregan todas 
						 // $data['menus'][]="td_nc_correccion";
						 // $data['menus'][]="td_nc_accion"; 
						 // $data['menus'][]="td_nc_cerradas";
					// }
					if($rows1['Roldes']=="Control")
					{
						 $data['menus'][]="td_nc_rechazadas";
						 $data['menus'][]="td_nc_correccion";
						// $data['menus'][]="td_nc_accion"; //se agrega accion
						 $data['menus'][]="td_nc_cerradas";
					}
					elseif($rows1['Roldes']=="Accion")
					{
						  // $data['menus'][]="td_nc_rechazadas"; //solo tenia accion se agregan las demas
						  // $data['menus'][]="td_nc_correccion";
						  $data['menus'][]="td_nc_accion";
						  // $data['menus'][]="td_nc_cerradas";
					}
					
				}
			}
		
		}
		else
		{
			//se agregan los menus que se le muestra a cada tipo de usuario
			if($tipo_usu==1) //coordinador
			{
				$data['menus'][]="td_ea_ingresar";
				$data['menus'][]="td_ea_enviadas";
				$data['menus'][]="td_ea_recibidas";
				$data['menus'][]="td_ea_recibidas_u_genero";
			}
			elseif($tipo_usu==2) //jefe inmediato
			{
				$data['menus'][]="td_ea_ingresar";
				$data['menus'][]="td_ea_enviadas";
				//$data['menus'][]="td_ea_recibidas";
			}
			elseif($tipo_usu==3) //empleado
			{
				$data['menus'][]="td_ea_ingresar";
				$data['menus'][]="td_ea_enviadas";
			}
			
			//se consulta el rol
			$sql1="select Roldes
				from ".$wbasedato."_000004 
				where Roltip='".$evento."'
				and Rolcod ='".$key."'
				";
			$res1= mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num1 = mysql_num_rows($res1);
			$rol="";
			
			
			if ($num1 > 0)
			{
				for( $i = 0; $rows1 = mysql_fetch_array($res1); $i++ )
				{
					
					if($rows1['Roldes']=="Control")
					{
						$data['menus'][]="td_ea_rechazadas";
						$data['menus'][]="td_ea_correccion";
						$data['menus'][]="td_ea_recibidas";
						$data['menus'][]="td_ea_cerradas";
					}
					elseif($rows1['Roldes']=="Accion")
					{
						  $data['menus'][]="td_ea_accion";
					}
					elseif($rows1['Roldes']=="Medicamentos")
					{
						  $data['menus'][]="td_ea_recibidas_u_control";
					}
					elseif($rows1['Roldes']=="Infeccioso")
					{
						  $data['menus'][]="td_ea_recibidas_u_control";
					}
					elseif($rows1['Roldes']=="No Infeccioso")
					{
						  $data['menus'][]="td_ea_recibidas_u_control";
					}
				}
			}
			
		}
			
	echo json_encode($data);
	return;
}
?>
<html>
<head>
<title>Ingreso no conormidad o evento adverso</title>
<script src="../../../include/root/jquery-1.3.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
function cambiarColor( cmp ){
	//ultimoMenu = cmp;
	// var fila = cmp.parentNode;	//halla el tr

	// var tds = fila.getElementsByTagName( "td" );	//busco todos los tag TD que hallan en la fila
	
	// for( var i = 0; i < tds.length; i++ ){
		// tds[ i ].bgColor = '';		//Cambio el color de fondo cada td a vacio
	// }
	
	// cmp.bgColor = '#00CCCC';
	//cmp="td_nc_recibidas";
	//alert(cmp);
	var evento =$("#tipo_even").val();
	//alert(evento);
	if (evento=="NC")
	{
		$("#tabla_navegacion_nc").find("td").removeClass("j2");
	}
	else
	{
		$("#tabla_navegacion_ea").find("td").removeClass("j2");
	}
	$("#"+cmp).addClass("j2");
	
}

function buscarCco(cco)
{
	var valor = $("#u_genero").val();
	//alert(valor);
	$.post("ingreso_nce.php",
			{
				cco:  			cco,               
				consultaAjax:   '',
				accion:         'buscar',
				valor:          valor
						
			}
			,function(data) {
				if(data.error == 1)
			{
				
			}
			else
			{
				$("#ccos").html(data.options); //al select se le envia el array data en la posicion options
				
			}
		},
		"json"
	);
}

function enviar(key,wbasedato,datos,wemp_pmla,cco,tabla_referencia)
{ 
	//que encuentre los campos input con la clase camporRequerido y le remueva esa clase
	$("#contenedor_eventos").find(":input.campoRequerido").removeClass('campoRequerido'); 
	
	var todo_ok=validarCampos(); 	
	if (todo_ok==true)
	{
			
				//crear evento
				var evento = $("#tipo_even").val(); //para diferenciar si es NC o EA  
				
				var ccod = $("#ccod").val();
				var fechad = $("#fechai").val();
				var horad = $("#hora").val();
				var ccog = $("#ccos").val();
				var desc = $("#desc").val();
				var est = $("#estados_nc").val();
				
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
					
					/**************
					trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
					
					//busca consecutivo mayor
					if(trs > 0)
					{
						var campos = '';
						var separador_bloque = '';
						$("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
							var id_tr = $(this).attr('id'); // El this es el id de cada fila de la tabla
							
							
							var cuales = $("#"+id_tr+"_cuales").val();
							
							var directo = $("#"+id_tr+"_indi").val();
							

							campos = campos+separador_bloque+cuales+"|"+directo;
							separador_bloque = '|-|';
								
						});

					}
					*************/
					
					
					var afec = $("input:radio[name='clasifique']:checked").val();
					var nom_afec = $("#nom_afec").val();
					var ed_afec = $("#edad").val();
					var id_afec = $("#ide").val();
					var hc = $("#hc").val();
					var desc_acc = $("#desc_acc").val();
					//var clase_even=$("input:radio[name='clase']:checked").val(); 
					//var tipo_even = $("#tipo_even1").val();
					//var even = $("#evento_hijo").val();
					//var sev = $("input:radio[name='sev']:checked").val();
					//var prev=$("input:radio[name='prevenible']:checked").val();
					//var cent=$("input:radio[name='centinela']:checked").val();
					var inf="";
					//var analiz = $("#analiz").val();
					var resp = $("#responsables").val();
					
					var fuente=""; 
					
				}
				
			   
				$.post("ingreso_nce.php",
						{
							
							evento:     	evento,
							consultaAjax:   '',
							accion:         'enviar',
							ccod: 			ccod, 
							fechad: 		fechad, 
							horad: 			horad,
							ccog: 			ccog,
							desc:			desc,
							est:			est,
						
							fuente: 		fuente,
							
							afec:     	afec,
							nom_afec:   nom_afec,
							ed_afec:    ed_afec,
							id_afec:    id_afec,
							hc:         hc,
							desc_acc:   desc_acc,
							//clase_even: clase_even,
							tipo_even:  tipo_even,
							//even:       even,
							//sev:        sev,
							//prev:       prev,
							//cent:       cent,
							inf:	    inf,
							//analiz:     analiz,
							resp:		resp,
							
							key:		key,
							wbasedato:	wbasedato,
							datos:		datos,
							wemp_pmla:  wemp_pmla,
							cco:		cco
							//array_campos: campos
						}
						,function(data) {
							if(data.error == 1)
						{
							
						}
						else
						{
							alert(data.mensaje); // update Ok.
							ocultarTodo();
							var abrir_menu=""; //se necesita que cuando ingrese un evento, redirija a enviadas
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
							
						}
					},
					"json"
				);
		//	}
	}
	else
	{
		alert("Debe llenar los campos requeridos *");
	}
	
}

function editar(key,wbasedato,datos,wemp_pmla,cco,tabla_referencia)
{ 
	//que encuentre los campos input con la clase camporRequerido y le remueva esa clase
	$("#contenedor_eventos").find(":input.campoRequerido").removeClass('campoRequerido'); 
	
	var est = $("#estados_nc").val();
	var niv = $( "#nivel" ).val();
	
	var todo_ok=validarCampos(est,niv);	
	if (todo_ok==true)
	{ 
			var evento = $("#tipo_even").val(); //para diferenciar si es NC o EA  
			

			var obs = $("#obs").val();
			//var est = $("#estados_nc").val();
			var id = $( "#id_Evento" ).val();
			var ccg = $( "#ccge" ).val();
			// var niv = $( "#nivel" ).val();
			//alert(niv +" -" +est + "--"+evento);
			var clasif = ""; //se ponen las variables vacias luego se llenan
			var inf="";
			var correc="";
			var rechazada="";
			var accionc="";
			var campos="";
			
			//alert(ccg+"ccg"+est+"est"+niv+"niv");
			
			if (evento=="NC")
			{ 
				if ( est=='02') //cuando el jefe inmediato aprueba
				{
					
					if (niv=='1')
					{
						var clasif = $("#clasificacion").val(); //depronto la observacion
						niv++;
					}
					else //segunda vez que se aprueba - alejandro
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
					// else if (niv=='3') //alejandro rechaza
					// {
						// niv='4';
					// }
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
				//var validacion = true;
				//busca consecutivo mayor
				if(trs > 0)
				{
					//var campos = ''; //se coloca en comentario para prueba
					var separador_bloque = '';
					$("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
						var id_tr = $(this).attr('id'); // El this es el id de cada fila de la tabla
						
						// var id_accion  = $("#"+id_tr+"_bd").val(); //id de la accion que trae la bd
						// var accion_val  = $("#"+id_tr+"_accion").val();
						// var fechar      = $("#"+id_tr+"_fechar").val();
						// var fechapr     = $("#"+id_tr+"_fechapr").val();
						// var seguimiento = $("#"+id_tr+"_seguimiento").val();
						// var responsable = $("#"+id_tr+"_responsable").val();
						var factor = $("#"+id_tr+"_fact_1").val(); 
						var cuales = $("#"+id_tr+"_cuales").val();
						
						var directo = $("#"+id_tr+"_indi").val();
						var id_causa = $("#"+id_tr+"_id").val();
						var relacionado = $("#"+id_tr+"_fact_s_n").val();
						// var ex = causa.split("-");
						// var id_plan = ex[0];
						//var causa_cod = ex[1];  //si se necesita mandar el codigo de la causa

						campos = campos+separador_bloque+factor+"|"+cuales+"|"+directo+"|"+id_causa+"|"+relacionado;
						separador_bloque = '|-|';
						
						
						// if(accion_val == "" || fechar == "" || fechapr == "" || seguimiento == "" || responsable == "" || estado == "" || causa == "")
						// { validacion = false; }
						
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
						//var even = $("#evento").val();
						var sev = $("input:radio[name='sev']:checked").val();
						var prev=$("input:radio[name='prevenible']:checked").val();
						var cent=$("input:radio[name='centinela']:checked").val();
						var analiz = $("#analiz").val();
						var resp = $("#responsables").val();
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
					//alert(correc+" correccion");
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
			
		   
			$.post("ingreso_nce.php",
					{
						
						evento:     	evento,
						consultaAjax:   '',
						accion:         'editar',
						obs:			obs,	 
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
						key:			key,
						wbasedato:		wbasedato,
						datos:			datos,
						wemp_pmla:      wemp_pmla,
						cco:			cco,
						id:				id,
						output:		    output,
						accionc:		accionc,
						array_campos:   campos
						
						  
					}
					,function(data) {
						if(data.error == 1)
					{
						alert(data.mensaje);
					}
					else
					{
						alert(data.mensaje); // update Ok.
						var abrir_menu="";
						ocultarTodo();
								if (evento=="NC")
								{    
									$("#navegacion_nc").css("display", "");  //mostrar divs
									 ver_menu = $("#tabla_navegacion_nc").find('.j2').attr("id"); 
									 abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
									 abrir_menu=$("#"+ver_menu)[0].onclick();
									
								}
								else
								{
									$("#navegacion_ea").css("display", "");  //mostrar divs
									ver_menu = $("#tabla_navegacion_ea").find('.j2').attr("id");
									abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
									abrir_menu=$("#"+ver_menu)[0].onclick();
								}
					}
				},
				"json"
			);
	}
	else
	{
		alert("Debe llenar los campos requeridos *");
	}
	
}

function addFila(tabla_referencia,key,wbasedato,datos,wemp_pmla,cco)
    {
        //alert("entro");
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
			//alert("entro2");
        }
        else
        { value_id = '1_tr_'+tabla_referencia; }
//alert("entro3");

		$.ajax({
				url: "ingreso_nce.php",
				context: document.body,
				type: "POST",
				data: {
					accion      : 'adicionar_fila',
					consultaAjax: '',
					id_fila     : value_id,
					key         : key,
					wbasedato   : wbasedato,
					datos       : datos,
					wemp_pmla   : wemp_pmla,
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

		
        // $.post("ingreso_nce.php",
            // {
                // accion      : 'adicionar_fila',
                // consultaAjax: '',
                // id_fila     : value_id,
				// key        :key,
				// wbasedato   :wbasedato,
				// datos       :datos,
				// wemp_pmla   :wemp_pmla,
				// cco         :cco
            // },
            // function(data){
                // if(data.error == 1)
                // {
                    // alert(data.mensaje);
                // }
                // else
                // {
                    // $("#tabla_factores > tbody").append(data.html);
                // }
            // },
            // "json"
        // );
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
						accion      : 'eliminar_factores',
						consultaAjax: '',
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
							//alert(data.mensaje);
						}
					},
					"json"
				);
			}
			$("#"+id_fila).empty();
			$("#"+id_fila).remove();
		}
    }
	
function seleccion(datos,key,wemp_pmla,wbasedato,cco)
{
	
	ocultarTodo();
	
	var evento = $("#tipo_even").val();
	
	$.post("ingreso_nce.php",
            {
                accion      : 'consultar_roles',
                consultaAjax: '',
                evento      :evento,
				key         :key,
				wbasedato   :wbasedato,
				datos       :datos,
				wemp_pmla   :wemp_pmla,
				cco         :cco
            },
            function(data){
                if(data.error == 1)
                {
                    alert(data.mensaje);
                }
                else
                {
					var ver_menu=""; //se necesita que cuando ingrese a los eventos depediendo del rol se le muestre un menu por defecto
					ocultarTd();//alert(data.menus);
					$.each( data.menus, function( key, value ) {
						//alert(value);
						$("#"+value).show();
						ver_menu=value;
					});
					//alert(ver_menu+" ver_menu");
					var abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
					 abrir_menu=$("#"+ver_menu)[0].onclick();
					//alert(abrir_menu);
					//abrir_menu.click();
					
                }
            },
            "json"
        );

}

window.onload = function()
	{
		ocultarTodo(); //se oculta todo al cargar la pagina menos el select de evento
		calendario('','');
	}
	
	function getMonth(month) {
		//var month = date.getMonth();
		return month < 10 ? '0' + month : month; 
	}
	
	function habilitarCampos()
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
		$("#responsables").attr("disabled", false);  
		$("#hc").attr("readonly", false);
		$("#desc_acc").attr("readonly", false);
		$('input[name="fuente"]').attr('disabled', ''); 
		$('input[name="clasifique"]').attr('disabled', ''); 
		$( "#1_tr_tabla_factores_fact_1" ).attr("disabled", false); 
		$( "#1_tr_tabla_factores_fact_s_n" ).attr("disabled", false); 
		$( "#1_tr_tabla_factores_cuales" ).attr("disabled", false); 
		$( "#1_tr_tabla_factores_indi" ).attr("disabled", false);
		$('input[name="clase"]').attr('disabled', ''); 
		$('input[name="infec"]').attr('disabled', ''); 
		$( "#tipo_even1" ).attr("disabled", false); 
		$( "#evento_hijo" ).attr("disabled", false); 
		$('input[name="sev"]').attr('disabled', '');
		$('input[name="prevenible"]').attr('disabled', ''); 
		$('input[name="centinela"]').attr('disabled', ''); 
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
		$("#cor").attr("readonly", false);
		$( "#causas_nc" ).attr("disabled", false);
		//$('input[name="fuente"]').attr('disabled', ''); 
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
		$("#responsables").attr("disabled", true);  
		$("#hc").attr("readonly", true);
		$("#desc_acc").attr("readonly", true);
		$('input[name="fuente"]').attr('disabled', 'disabled'); 
		$('input[name="clasifique"]').attr('disabled', 'disabled'); 
		// $( "#1_tr_tabla_factores_fact_1" ).attr("disabled", true); 
		// $( "#1_tr_tabla_factores_fact_s_n" ).attr("disabled", true); 
		// $( "#1_tr_tabla_factores_cuales" ).attr("disabled", true); 
		// $( "#1_tr_tabla_factores_indi" ).attr("disabled", true);
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
		//$("#obs").attr("readonly", true);
		//$('input[name="fuente"]').attr('disabled', ''); 
	//falta causas nc, cor, clasificacion
		$("#cor").attr("readonly", true);
		$( "#causas_nc" ).attr("disabled", true);
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
		var f = new Date();
		$("#fechai").val(f.getFullYear() + "-" + (getMonth(f.getMonth()+1)) + "-" + getMonth(f.getDate()));
	}
	
	function formu_ingresar_nc()
	{ 
		
		ocultarTodo();  //se muestran solo los divs para ingresar la no conformidad
		resetearCampos(); //se deben resetear todos menos los campos de la persona que ingresa la no conformidad
		habilitarCampos();//habilitar los campos porque si ya visitaron otra opcion del menu los trae readonly
		datosPorDefecto(); //llenar datos del usuario de matrix
		$("#tarea").html(""); //para que no muestre la causa de rechazo
		//mostrar divs
		$("#contenedor_eventos").css("display", "");
		$("#navegacion_nc").css("display", "");
		$("#uni_detecto").css("display", "");
		$("#descripcion_conf").css("display", "");
		$("#fuente").css("display", "");
		$("#unidad_genero").css("display", "");
		$("#estados").css("display", "");
		$("#enviar").css("display", "");
		$("#hora").attr("readonly", true); //deshabilitar la hora
		
		//se llama a la funcion que llena los estados
		var select = document.getElementById( 'estados_nc' );
		llenarEstados(select,'');
		
	}
	
	function formu_ingresar_ea()
	{ 
		ocultarTodo(); //se muestran solo los divs para ingresar el evento adverso
		resetearCampos();
		habilitarCampos(); //habilitar los campos porque si ya visitaron otra opcion del menu los trae readonly
		datosPorDefecto(); //llenar datos por defecto usuario matrix
		$("#tarea").html("");//para que no muestre la causa de rechazo
		//mostrar divs
		$("#contenedor_eventos").css("display", "");
		$("#navegacion_ea").css("display", "");
		$("#uni_detecto").css("display", "");
		$("#per_afec").css("display", "");
		$("#unidad_genero").css("display", "");
		$("#descripcion_conf").css("display", "");
		$("#acciones_ins").css("display", "");
	//	$("#factores").css("display", "");
	//	$("#conclusiones").css("display", "");
	//	$("#analizado").css("display", "");
		//$("#infeccion").css("display", "");
		$("#estados").css("display", "");
		$("#enviar").css("display", "");
		
		/*
		//se ponen los select de factores en seleccione..
		//consulto primero la tabla
		var tb = document.getElementById( "tabla_factores" ).tBodies[0];
		
		//Consulto las filas que se van a borrar
		while( tb.rows.length > 3 ){ 
			tb.removeChild( tb.rows[3] );
		}
		
			$( "#1_tr_tabla_factores_fact_1" ).val("");
			$( "#1_td_tabla_factores_fact_1" )[0].onchange();
			$( "#1_tr_tabla_factores_cuales" ).val("");
			$( "#1_tr_tabla_factores_indi" ).val("");
			$( "#1_tr_tabla_factores_id" ).val("");
		*/
			//se ponen los valores por defecto de los radio button despues de resetearlos
			$('#paci').attr('checked','checked');    
			$('#eventoA').attr('checked','checked');
			$('#noda').attr('checked','checked');
			$('#si_p').attr('checked','checked');
			$('#si_c').attr('checked','checked');
			$("#hora").attr("readonly", true); //deshabilitar la hora
		//se llama a la funcion que llena los estados
		var select = document.getElementById( 'estados_nc' );
		llenarEstados(select,'');
	}
	
	function nc_enviadas(datos,key,wemp_pmla,wbasedato,cco,tipo,consultar,div_resp)
	{
		debugger;
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
				accion:         'lista_enviadas',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
				cco:			cco,
				evento:         evento,
				tipo:			tipo,
				consultar:		consultar,
				div_resp:		div_resp
						
			}
			,function(data) {
			if(data.error == 1)
			{
				alert(data.mensaje);
			}
			else
			{ 
				$("#div_lista_re").css("display", "");
				$("#"+div_resp).html(data.html); // update Ok.
				//$("#"+div_resp)[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
}
	
	function nc_recibidas(datos,key,wemp_pmla,wbasedato,cco)
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
				accion:         'lista_recibidos',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
}

function nc_recibidas_u_genero(datos,key,wemp_pmla,wbasedato,cco)
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
				accion:         'lista_recibidos_u_genero',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
}

function nc_recibidas_u_control(datos,key,wemp_pmla,wbasedato,cco)
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
				accion:         'lista_recibidos_u_control',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
}


function nc_recibidas_correccion(datos,key,wemp_pmla,wbasedato,cco)
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
				accion:         'lista_recibidos_correccion',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
}

function nc_recibidas_accion(datos,key,wemp_pmla,wbasedato,cco)
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
				accion:         'lista_recibidos_accion',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				
				
			}
		},
		"json"
	);
}

function nc_recibidas_rechazadas(datos,key,wemp_pmla,wbasedato,cco)
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
				accion:         'lista_recibidas_rechazadas',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
} 

function nc_cerradas(datos,key,wemp_pmla,wbasedato,cco)
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
				accion:         'lista_cerradas',
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
				$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		},
		"json"
	);
}

function resetearCampos()
{
	//que encuentre los campos input con la clase camporRequerido y le remueva esa clase
	$("#contenedor_eventos").find(":input.campoRequerido").removeClass('campoRequerido'); 
	
	$("#nom").val('');	//recetea campos que podrian estar llenos
	$("#ccod").val('');	
	$("#car").val('');	
	$("#ext").val('');	
	$("#ema").val('');	
	$("#fechai").val('');
	$("#hora").val('');	
	$("#u_genero").val('');	
	$("#desc").val('');	 
	$("#obs").val('');	 
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
	   
}

function llenarEstados(select,opciones)
{
	//var opcionesEstados='';
	opciones = opcionesEstados;
	
	var niv= $("#niv_id").val();//se llenan los campos hidden de nivel y estado
	var est=$("#est_id").val();
	var evento = $("#tipo_even").val();
	
	// alert(niv+" nivel ");
	var opc = new Array();
	opc[0] = "-Seleccione...-";
	if (evento=="NC")
	{
		if ((niv == undefined && est == undefined) || (niv == "" && est == ""))
		{
			//cuando se ingresa
			opc[0] = opciones[0]; //se cambio la opc[1] por opc[0] para que salga de una vez pendiente aprobacion
			
		}
		else if(niv == 1 || niv == 3)
		{
			//jefe inmediato
			opc[1] = opciones[1];
			opc[2] = opciones[2];
			
		}
		else if(niv == 2 || niv == 4)
		{
			//unidad que genero
			opc[1] = opciones[3];
			opc[2] = opciones[2];
			
		}
		else if(niv ==5 || niv ==6)
		{
			//llega a alejandro correccion y acciones
			opc[1] = opciones[4];  //tenia 3 se le coloca 4
			opc[2] = opciones[5];
			
		}
		// else if(niv ==6 )
		// {
			//llega angela acciones
			// opc[1] = opciones[4];
			// opc[2] = opciones[5];
		// }
		
		
	}
	else  //eventos adversos
	{  
		if ((niv == undefined && est == undefined) || (niv == "" && est == ""))
		{
			//cuando se ingresa
			opc[0] = opciones[0]; //se cambio la opc[1] por opc[0] para que salga de una vez pendiente aprobacion
			
		}
		else if(niv == 1 || niv == 2 || niv == 4)
		{
			//le llega a unidades de control
			opc[1] = opciones[1];
			opc[2] = opciones[2];
			
		}
		else if(niv == 3 || niv == 5)
		{
			if(est=='03')
			{
				opc[1] = opciones[1];
				opc[2] = opciones[2];
			}
			else
			{//unidad que genero
				opc[1] = opciones[3];
				opc[2] = opciones[2];
			}
			
		}
		else if(niv==6 || niv==7)
		{
			//llega  correccion a magda y acciones
			opc[1] = opciones[4]; //tenia 3, se le coloca 4
			opc[2] = opciones[5];
			
		}
		// else if(niv ==6 )
		// {
			//llega magada acciones
			// opc[1] = opciones[4];
			// opc[2] = opciones[5];
			
		// }
	}
	creandoOptions( select, opc );
}

/**
 * opciones debe ser un array
 */
function creandoOptions( slCampos, opciones )
{
	
              //opciones debe ser un array
               if( slCampos.tagName.toLowerCase() == "select" )
			   {
					opciones.reverse();

                      //Borrando los options anteriores
                       var numOptions = slCampos.options.length;
                       
                       for( var i = 0; i <  numOptions; i++ )
					   {
                               slCampos.removeChild( slCampos.options[0] );
                       }

                      //agrengando options
                       for( var i = 0; i < opciones.length; i++ )
					   {
								//Una posicion tiene el value y el texto a mostar
								//La primera posicion es el value y la segunda es el texto
							   var arOpciones = opciones[i].split( '-' );
							   
                               var auxOpt = document.createElement( "option" );
                               slCampos.options.add( auxOpt, 0 );
                               auxOpt.innerHTML = arOpciones[1];
							   auxOpt.value = arOpciones[0];

                               slCampos.options.selectedIndex = 0;
                       }
               }
}
	   
function llenarDatos(cmp, nom,codccd,nomccd,cargo,ext,email,fechad,horad,fuente,codccg,nomccg,desc,est,
					tpeafec,npeafec,epeafec,ipeafec,hcpeafec,accins,clainc,tipoinc,even,seve,prev,
					cent,anali,evento,ucon,id,resp,nivel,clasi,obs,cor,caurechazo,arOptions,accionc,
					causas,caso)  //falta enviarle las variables wbasedato, datos, wemp_pmla y otra**    
{	        
	// alert(caurechazo)
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
	//$("#tarea").html("");
	//buscar todos los td del tr que llego y a toda la tabla que los contiene se le pone tamaño 10
	$( "td", cmp.parentNode ).css( "fontSize", "10pt" );
	$( "td", cmp ).css( "fontSize", "14pt" );//el tr que le llego ponerle tamaño 14
	
	if( $("#id_Evento").val() != id )
		$( "#contenedor_eventos")[0].style.display = 'none';
	
	resetearCampos();
	//ocultarTodo();
	habilitarCampos();
	//habilitarFactores(causas);
	
	var evento = $("#tipo_even").val();  
	$("#id_Evento").val(id); //se llena el campo hidden id_evento con el id del evento que estoy llenando los campos
	$("#ccge").val(codccg);
	$("#nivel").val(nivel);
	
	$("#niv_id").val(nivel);//se llenan los campos hidden de nivel y estado
	$("#est_id").val(est);
	$("#estadoA").val(est);
	
	if (evento=="NC")
	{
		if (caso!="enviadas")
		{ 
				//alert(nivel +"-"+est);
				$("#navegacion_nc").css("display", "");  //mostrar divs
				$("#div_lista_re").css("display", "");     
				//$("#unidad_detecto_mostrar").css("display", "");
				$("#descripcion_conf").css("display", "");
				$("#fuente").css("display", "");
				//$("#unidad_genero").css("display", ""); mostrar donde se necesite
				//$("#clasificacion_conf").css("display", "");
				$("#observaciones").css("display", "");
				$("#estados").css("display", "");
				$("#editar").css("display", "");
				// $("#tratamiento_conf").css("display", "");  //probar
				// $("#causas").css("display", ""); //probar
				// $("#link_accion").css("display", ""); //probar
				
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
					$( "#causas_nc" ).attr("disabled", false);
					if (est=='03' && nivel=='4')
					{
						$("#tratamiento_conf").css("display", "none"); //por el nivel no se ha llenado la correccion
						$("#causas").css("display", "none");//por el nivel no se han llenado las causas
						$("#link_accion").css("display", "none"); //no se ha llegado a las acciones
					}
					
				}
				
				if(nivel=='5') //se muestra a usuario control alejandro correccion
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
					
					// alert(typeof arOptions);
					// alert(arOptions+"arreglo");
					// arOptions = String(arOptions).split("-");
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
					//$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					$("#rechazo").css("display", ""); //se muestra el div de porque
					$("#unidad_genero_mostrar").css("display", "");
					
					$("#clasificacion").val(clasi); //se llena la calsificacion que trae del jefe
					$("#obs_ant").val(obs); //se llena la observacion
					$("#cor").val(cor); //se llena la correccion
					$("#caurechazo_ant").val(caurechazo); //se llena porque rechazo probar si colocandolo al principio se llena para todos
					
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
		$("#desc").val(desc);	                           
		$("#estados_nc").val(est);
		
		$("#obs_ant").val(obs);	
		$("#clasificacion").val(clasi);	//caracterizacion
		$("#caurechazo_ant").val(caurechazo);	
		$("#cor").val(cor);	 //correccion
		
			
		
		//fuente
		if (fuente == "NC Interna")
		{
			$('#nc_inter').attr('checked','checked');
		}
		else if (fuente == "Auditoria Interna")
		{
			$('#au_inter').attr('checked','checked');  
		}
		else if (fuente == "Auditoria Externa")
		{
			$('#au_ext').attr('checked','checked'); 
		}
		else if (fuente == "Autocontrol")
		{	
			$('#auto').attr('checked','checked'); 
		}
		else if(fuente == "Cliente Externo")
		{
			$('#cli_ext').attr('checked','checked'); 
		}
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
			deshabilitarCampos(); //se le pone obs solo cuando se necesite para no ponerlo en la funcion
			$("#obs_ant").attr("readonly", true);
			//se resetea al selet al principio
					var slNc = document.getElementById( "causas_nc" );
					
					for( i = 0; i < slNc.options.length; i++ ){
						slNc.options[ i ].selected = false;
					}
						
					arOptions = String(arOptions).split("-");
					//arOptions = arOptions.split( '-' );	
						
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
		$("#u_genero").val(codccg+"-"+nomccg);
		$("#u_genero_mostrar").val(codccg+"-"+nomccg);			
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
		//$("#evento").val(even);                            
		$("#analiz").val(anali);                           
		$("#responsables").val(resp);
		$("#cor").val(cor);
		$("#obs_ant").val(obs);
		$("#caurechazo_ant").val(caurechazo);
		
		
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
					//$( "#1_td_tabla_factores_fact_1" )[0].onchange();
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
				//$("#unidad_genero").css("display", "");
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
				
				
				if (nivel=='1')
				{ 
					//se llama a habilitarCampos 
					habilitarCampos();
					//mostrar los datos de la persona que la creo
					$("#uni_detecto").css("display", "");
					$("#unidad_genero").css("display", ""); //se muestra el div con el boton
					
					
				}      
				if (nivel=='2')
				{	
					//invecla/santi/magada solo se muestra la unidad que detecto
					$("#unidad_detecto_mostrar").css("display", ""); //mostrar div unidad detecto
					//$("#tratamiento_conf").css("display", ""); //se muestra el div de correccion
					
					$("#unidad_genero_mostrar").css("display", ""); //mostrar el div sin el boton
					
					//deshabilitar los campos
					deshabilitarCampos();
					
					
					//factores deshabilitados
					deshabilitarFactores(causas);
					
					
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
						deshabilitarFactores(causas);//para que cuando muestre las rechazadas
					}
					//deshabilitar los campos
					deshabilitarCampos();
					deshabilitarFactores(causas);
					$("#cor").attr("readonly", false); //se habilita el campo coreccion
					
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
					//$("#accion_correctiva").css("display", ""); //lleva al formulario de acciones correctivas
					$("#unidad_genero_mostrar").css("display", ""); //mostrar div unidad genero
					//$("#accion_correctiva").css("display", ""); //accion correctiva
					
				
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
					//$("#unidad_detecto_mostrar").css("display", ""); //mostrar div unidad detecto
					
				
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
		
		if (fuente == "Infeccioso")   //infeccioso   por que es la primera vez
		{
			$('#infec').attr('checked','checked');
		}
		else if (fuente == "No Infeccioso")
		{
			$('#no_infec').attr('checked','checked');  
		}
		else if (fuente == "Relacionado con medicamentos o medios")
		{
			$('#med').attr('checked','checked');  
		}
		else if(fuente == "")
		{
			$('#no_infec').attr('checked','checked');  //porque la primera vez llegaria vacio ya en la segunda si iria lleno
		}
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
			deshabilitarCampos(); //se le pone obs solo cuando se necesite para no ponerlo en la funcion
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
	llenarEstados(select,'');	
	/*poner vacios otra vez estado y nivel que son hidden que se crearon
	para hacer las validaciones de los estados que se muestran en cada nivel
	*/ 
	 $("#niv_id").val(""); 
	 $("#est_id").val("");
		
	mostrarOcultar( $( "#contenedor_eventos")[0] );
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
	var estado =$("#estados_nc").val();
	
	//alert(estado);
	if(estado == 03)
	{
		$.post("ingreso_nce.php",
				{
					consultaAjax:   '',
					accion:         'rechazada',
					estado : estado,
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
							// $("#tarea")[0].innerHTML = data.html; // update Ok.
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
					
function llenar_evento(select_padre,select_hijo,key,wbasedato,datos,wemp_pmla,cco)
	{
		
		var evento = $("#tipo_even").val();
		var id_padre = $("#"+select_padre).val();   
	    //select_padre: nombre del select padre
		//select_hijo: nombre del select hijo
		//id_padre: lo que seleccionaron en el select padre
	
	//alert (id_fila_hijo); 
		
		
	$.ajax(
	{
				url: "ingreso_nce.php",
				context: document.body,
				type: "POST",
				data: 
				{
					consultaAjax:   '',
					accion:         'llenar_select_evento',
					key:			key,
					wbasedato:		wbasedato,
					datos:			datos,
					wemp_pmla:      wemp_pmla,
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

function llenar_hijo(id_fila_padre,id_fila_hijo,key,wbasedato,datos,wemp_pmla,cco)
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
					key:			key,
					wbasedato:		wbasedato,
					datos:			datos,
					wemp_pmla:      wemp_pmla,
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
		
		//var evento = $("#tipo_even").val();
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

	//est1=06;		// var output = result.join(", ");
	//alert(est);//alert(id_causa+"-"+est+"-"+evento);
	var v = window.open( 'acciones.php?key='+key+'&wbasedato='+wbasedato+'&datos='+datos+'&wemp_pmla='+wemp_pmla+'&cco='+cco+'&id_evento='+id_evento+'&est='+est+'&evento='+evento+'');
}

function evaluarEstado(wbasedato)
{ 
	var est = $("#estados_nc").val(); //el estado del evento
	var id_evento = $( "#id_Evento" ).val(); //se le envia el id del evento
	var evento = $("#tipo_even").val(); //tipo evento
	var nivel = $("#nivel").val(); //nivel en el que esta
	var radio_acciones = $("input:radio[name='accion']:checked").val();
	
	if (est=='06')
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
							// if (evento=="NC")
							// {    
								// $("#navegacion_nc").css("display", "");  //mostrar divs
								 // ver_menu = $("#tabla_navegacion_nc").find('.j2').attr("id"); 
								 // abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
								 // abrir_menu=$("#"+ver_menu)[0].onclick();
							// }
							// else
							// {
								// $("#navegacion_ea").css("display", "");  //mostrar divs
								// ver_menu = $("#tabla_navegacion_ea").find('.j2').attr("id");
								// abrir_menu=$("#"+ver_menu)[0].firstChild.onclick();
								// abrir_menu=$("#"+ver_menu)[0].onclick();
							// }
						
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
       Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'fechai'+tipoProtocolo+idx,button:'btnfechai'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',timeInterval:1,dateStatusFunc:'',onSelect:alSeleccionarFecha});              
}

function alSeleccionarFecha( date, stringFecha ){
//%Y-%m-%d a las:%H:00:00
	if( date.dateClicked ){
	   date.params.inputField.value = date.currentDate.print(date.params.ifFormat);
	   document.getElementById( "hora" ).value = date.currentDate.print( "%H:%M:00" );
	   if( date.params.inputField.onchange ){
		   date.params.inputField.onchange();
	   }
	   date.callCloseHandler();
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
		$("#contenedor_eventos").find(":input."+tipo_form+":visible").each(function(){
			var campo_id = $(this).attr("id");
			var valor = $("#"+campo_id).val(); //alert(valor+" valor"+ campo_id+" id");
			
			if(valor == '' || valor == null)
			{
				todo_ok = false;
				$(this).addClass('campoRequerido');
			}
			else
			{
				$(this).removeClass('campoRequerido');
			}
		});
	}
//alert(todo_ok+"sale");
    return todo_ok;
}

function revisadas(datos,key,wemp_pmla,wbasedato,cco)
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
				key:			key,
				wbasedato:		wbasedato,
				datos:			datos,
				wemp_pmla:      wemp_pmla,
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
				// $("#div_lista_re").html(data.html); // update Ok.
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
		//var arCausas = causas[i].split( " " );
		
		$( "#"+(i+1)+"_tr_tabla_factores_fact_1" ).attr("disabled", true);
		//$( "#"+(i+1)+"_tr_tabla_factores_fact_1" )[0].onchange();
		$( "#"+(i+1)+"_tr_tabla_factores_cuales" ).attr("disabled", true);
		$( "#"+(i+1)+"_tr_tabla_factores_indi" ).attr("disabled", true);
		//$( "#"+(i+1)+"_tr_tabla_factores_id" ).val( arCausas[3] );
		$( "#"+(i+1)+"_tr_tabla_factores_fact_s_n" ).attr("disabled", true);
		
		
	}
}

//factores deshabilitados
function habilitarFactores(causas)
{ //alert("entro");
	for( var i = 0; i < causas.length; i++ )
	{
		//var arCausas = causas[i].split( " " );
		
		$( "#"+(i+1)+"_tr_tabla_factores_fact_1" ).attr("disabled", false);
		//$( "#"+(i+1)+"_tr_tabla_factores_fact_1" )[0].onchange();
		$( "#"+(i+1)+"_tr_tabla_factores_cuales" ).attr("disabled", false);
		$( "#"+(i+1)+"_tr_tabla_factores_indi" ).attr("disabled", false);
		//$( "#"+(i+1)+"_tr_tabla_factores_id" ).val( arCausas[3] );
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
		overflow-x: scroll
		overflow-y: scroll
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
    </style>
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

/****************************************************************************
* Funciones
*****************************************************************************/
//Funcion que muestra el select de la caracterizacion de las no conformidades
function clasificacion()
{
	global $conex;
	
	$sql = "select Clacod,Clades 
			from root_000092 
			where Clatip='NC' 
			and Claest='on'";		
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

//funcion que muestra el select con los estados de la no conformidades eventos - eventos adversos
function estados()
{
	global $conex;
	global $wbasedato;
	
	$sql = "select Estcod,Estdes,Esttex 
			from root_000093 
			where Esttip='NC' 
			and Estest='on'";		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	$tex=array();
	echo "<SELECT NAME='estados' id='estados_nc'  class='validarEA validarNC' onChange='estRechazada();evaluarEstado(\"$wbasedato\");'>"; //se le envia el codigo,text  
	echo "<option value=''>Seleccione..</option>";
	
			$causa=array();
				
				for( $k = 0; $rows = mysql_fetch_array($res); $k++ )
				{
					if($rows['Esttex']=='on') { $tex[$rows['Estcod']]=$rows['Estcod']; }
					$causa[]=$rows['Estcod']."-".$rows['Estdes']."-".$rows['Esttex'];
				}
			// for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
			// {
			
				 // if($rows['Esttex']=='on') { $tex[$rows['Estcod']]=$rows['Estcod']; }
			
			
				// if( $clasificacion != trim( $rows['Estcod'] ) )
				// {
					// echo "<option value='".$rows['Estcod']."'>".$rows['Estdes']."</option>";
				// }
				// else
				// {
					// echo "<option value='".$rows['Estcod']."' selected>".$rows['Estdes']."</option>";
				// }
			// }			
	
	echo"</SELECT>";
	echo "<script type='text/javascript'>";
	echo "var opcionesEstados = ".json_encode( $causa )."";
	echo ";llenarEstados( document.getElementById( 'estados_nc'),".json_encode( $causa ).")";
	echo "</script>";
	$tex2 = base64_encode(serialize($tex));
	echo"<input type='hidden' value='".$tex2."' id='cod_rech' name='cod_rech'>";
}

//funcion que muestra los datos de la persona que van a ingresar la no conformidad o el evento adverso
function datosPersonaDetecto()
{
	global $conex;
	global $datos;
	global $key;
	global $wemp_pmla;
	global $cco;
	
	//Se seleccionan los datos del usuario
	$sql = "select Ideno1,Ideno2, Ideap1, Ideext, Ideeml, Cardes, Cconom, Ccocod
			from ".$datos."_000013, root_000079, ".$cco."
			where Ideuse='".substr($key,-5)."-".$wemp_pmla."' 
			and Ideest='on'
			and Ideccg=Carcod
			and Idecco=Ccocod";		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	$rows = mysql_fetch_array( $res );
			
	return $rows;
}

//esta funcion busca el usuario, con su respectivo jefe y el centro de costos al que pertenecen en la tabla talhuma_000008
function perfiles($datos,$wemp_pmla,$key)
{
	global $conex;
	global $datos;
	global $key;
	global $wemp_pmla;
	
	$sql = "select Ajeuco,Ajeucr,Ajecco
			from ".$datos."_000008
			where Ajeuco='".substr($key,-5)."-".$wemp_pmla."' 
			";		
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
	
	$sql = "select Epsnit,Epsnom 
			from root_000073 
			where Epsest='on'
			and Epsnit != '0'
			and Epsnit != ' '
			order by Epsnom";		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	echo "<SELECT NAME='responsables' id='responsables' onChange='' class=''>"; 
	echo "<option value=''>Seleccione..</option>";
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
			{
				if( $responsables != trim( $rows['Epsnit'] ) )
				{
					echo "<option value='".$rows['Epsnit']."-".$rows['Epsnom']."'>".$rows['Epsnom']."</option>";
				}
				else
				{
					echo "<option value='".$rows['Epsnit']."-".$rows['Epsnom']."' selected>".$rows['Epsnom']."</option>";
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

	$q="select Rolcod
			from ".$wbasedato."_000004
			where Roltip='".$evento."' 
			and Roldes='Control'
			and Rolest='on'"; 
		
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$usu="";
		
			for( $i = 0; $rows1 = mysql_fetch_array($res); $i++ )
			{
				$usu.="-".$rows1['Rolcod']."-";
			}
	return $usu;
}



/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/

include_once("root/comun.php");
//



	
$conex = obtenerConexionBD("matrix");

$wactualiz="(2013-02-06)";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$key = substr($user, 2, strlen($user)); //04760-admi   04910-magda  04851-alejo 22701 johana gil 05970 coor cardiologia

//santiago-33985  luis mercadeo-03500 angela-01173
//$key ='03150';

$conex = obtenerConexionBD("matrix");

//$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

//$wbasedato = strtolower( $institucion->baseDeDatos );
$wentidad = $institucion->nombre;

$datos= consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
$cco= consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "gescal");
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );


$wfec=date("Y-m-d");

session_start();  
//el usuario se encuentra registrado
if(!isset($_SESSION['user']))
    echo "error";
else
{
encabezado("NO CONFORMIDADES - EVENTOS ADVERSOS ",$wactualiz, "".$wbasedato1);


echo"<FORM METHOD='POST' ACTION='' >";
			
		echo"<div id='seleccion'>";
			echo"<table align='center'>";
				echo"<th colspan='2' class='encabezadotabla'>TIPO DE EVENTO</th>";
				echo"<tr>";
				echo"<td class='fila1'>Seleccione</td>";
				echo"<td class='fila2'>";
					echo"<SELECT NAME='tipo_even' id='tipo_even' onchange=\"seleccion('$datos','$key','$wemp_pmla','$wbasedato','$cco');\">"; 
					echo"<OPTION VALUE='seleccione'>Seleccione...</OPTION>";
					echo"<OPTION VALUE='NC'>No Conformidad</OPTION>";
					echo"<OPTION VALUE='EA'>Evento Adverso</OPTION>";
					echo'</SELECT>'; 
				echo"</td>";
				echo'<tr>';
			echo'</table>';
		echo"</div>";
		echo"<br>";
		
		echo "<div id='navegacion_nc' class='divs' width='' style='display:none'>";
			echo"<table align='center' width='' id='tabla_navegacion_nc' >";
				echo"<tr class='encabezadotabla'>";
					echo"<td align='center' id='td_nc_ingresar' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick='formu_ingresar_nc()'><font color='white'>Ingresar No Conformidades</font></a></td>";
					echo"<td align='center' id='td_nc_enviadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_enviadas('$datos','$key','$wemp_pmla','$wbasedato','$cco','menu','enviados','div_lista_re');\"><font color='white'>Consultar No Conformidades Enviadas</a></font></td>";
					echo"<td align='center' id='td_nc_recibidas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar No Conformidades Recibidas</font></a></td>";	
					echo"<td align='center' id='td_nc_recibidas_u_genero' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_u_genero('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar No Conformidades Recibidas Unidad Genero</font></a></td>";	
					echo"<td align='center' id='td_nc_correccion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_correccion('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar No Conformidades en Correccion</a></font></td>";
					echo"<td align='center' id='td_nc_rechazadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_rechazadas('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar No Conformidades Rechazadas</a></font></td>";
					echo"<td align='center' id='td_nc_accion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_accion('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar No Conformidades en Accion</a></font></td>";
					echo"<td align='center' id='td_nc_cerradas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_cerradas('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar No Conformidades Cerradas</a></font></td>";
				echo"</tr>";
			echo"</table>";
		echo"</div>";
		
		echo "<div id='navegacion_ea' class='divs' width='' style='display:none'>";
			echo"<table align='center' width='' id='tabla_navegacion_ea'>";
				echo"<tr class='encabezadotabla'>";
					echo"<td align='center' id='td_ea_ingresar' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick='formu_ingresar_ea()'><font color='white'>Ingresar Evento Adverso</font></a></td>";
					echo"<td align='center' id='td_ea_enviadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_enviadas('$datos','$key','$wemp_pmla','$wbasedato','$cco','menu','enviados','div_lista_re');\"><font color='white'>Consultar Eventos Adversos Enviados</a></font></td>";
					echo"<td align='center' id='td_ea_recibidas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar Eventos Adversos Recibidos</font></a></td>";	
					echo"<td align='center' id='td_ea_recibidas_u_genero' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_u_genero('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar Eventos Adversos Recibidos Unidad Genero</font></a></td>";
					echo"<td align='center' id='td_ea_recibidas_u_control' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_u_control('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar Eventos Adversos Recibidos Unidad Control</font></a></td>";
					echo"<td align='center' id='td_ea_correccion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_correccion('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar Eventos Adversos en Correccion</a></font></td>";
					echo"<td align='center' id='td_ea_rechazadas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_rechazadas('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar Eventos Adversos Rechazados</a></font></td>";
					echo"<td align='center' id='td_ea_accion' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_recibidas_accion('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar Eventos Adversos en Accion</a></font></td>";
					echo"<td align='center' id='td_ea_cerradas' class='tds' onClick='cambiarColor( this.id );'><a href='#' onClick=\"nc_cerradas('$datos','$key','$wemp_pmla','$wbasedato','$cco');\"><font color='white'>Consultar Eventos Adversos Cerrados</a></font></td>";
				echo"</tr>";
			echo"</table>";
		echo"</div>";
		echo"<br>";
		
		echo "<div id='div_lista_re' class='divs'></div>
			  <br>";

 //echo"<div id='contenedor_eventos' style='display:none;'>";
echo"<div id='contenedor_eventos' >";
			$infor=array();
			$infor=datosPersonaDetecto();
			echo "<div id='uni_detecto' class='divs'>";
				echo"<table align='center' width='1100'>"; 
					echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>INFORMACION UNIDAD QUE DETECTO</td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td align='left' width='180' class='fila1'>Persona que detecto</td>";
						echo"<td align='left' width='150' class='fila2'><INPUT type='text' name='nom' id='nom' class='validarEA validarNC' title='pruebas' size='40' value='".$infor['Ideno1']." ".$infor['Ideno2']." ".$infor['Ideap1']."'></td>";	
						echo"<td class='fila1'>Unidad que detecto</td>";
						echo"<td class='fila2' colspan='3'><INPUT type='text' name='ccod' id='ccod' class='validarEA validarNC' size='50' value='".$infor['Ccocod']."-".$infor['Cconom']."'></td>";
					echo"</tr>";
					echo"<tr>";
						echo"<td class='fila1'>Cargo</td><td class='fila2'><INPUT type='text' name='car' id='car' class='validarEA validarNC' size='40' value='".$infor['Cardes']."'></td>";
						echo"<td class='fila1'>Extension</td><td class='fila2'><INPUT type='text' name='ext' id='ext' class='validarEA validarNC' size='10' value='".$infor['Ideext']."'></td>";
						echo"<td class='fila1'>Email</td><td class='fila2'><INPUT type='text' name='ema' id='ema' class='validarEA validarNC' size='40' value='".$infor['Ideeml']."'></td>";
					echo"</tr>";
					echo"</tr>";
					echo"<tr>";
						echo"<td width='200' class='fila1'>Fecha y Hora en que ocurrio el evento</td>";
						echo"<td class='fila2' colspan='5'><INPUT type='text' name='fechai' id='fechai' class='validarEA validarNC'>";
						echo"&nbsp<INPUT type='text' name='hora' id='hora' class='validarEA validarNC'>";
						echo "<BUTTON id='btnfechai'>...</BUTTON></td>";
						
						
					echo"</tr>";
				echo"</table>";
			echo"</div>";
			
			echo "<script>";
			echo "var datosUsuario = ".json_encode($infor);
			echo "</script>";
			
			echo"<div id='unidad_detecto_mostrar' class='divs'>"; 
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6' >INFORMACION UNIDAD QUE DETECTO</td>";
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
						echo"<td class='encabezadotabla' colspan='10'>INFORMACION PERSONA AFECTADA</td>";
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
						echo"<td class='fila1' width='150'>Identificacion</td><td class='fila2'><INPUT type='text' name='ide' id='ide' class='' value=''></td></tr></table>";
						echo"</td>";
						echo"</tr>";
					echo"<tr>";
						echo"<td class='fila1' width='200'>Responsable</td><td class='fila2'>";
						responsables();
						echo"</td>";
						echo"<td colspan='3'><table width='100%'><tr><td class='fila1' width='100'>Historia Clinica</td><td class='fila2'><INPUT type='text' name='hc' id='hc' class='' value=''></td></tr></table></td>";
						
					echo"</tr>";
				echo"</table>";
			echo"</div>";
			
			echo"<div id='fuente' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>Fuente</td>";
					echo"</tr>";
						echo"<tr>";
							echo"<td class='fila1'>";
								echo"<table border='0'>"; //tabla interna para fuente
									echo"<tr>";
										echo"<td'>Fuente </td>";
									echo"</tr>";
								echo"</table>";
							echo"</td>";
							echo"<td class='fila2'>";
								echo"<table border='0'>"; //tabla interna para fuente
									echo"<tr>";
										echo"<td align='right' width='100'>NC Interna:</td><td align='left'><INPUT type='radio' name='fuente' id='nc_inter' value='NC Interna' checked>";
									echo"</tr>";
								echo"</table>";
							echo"</td>";
							echo"<td class='fila2'>";
								echo"<table border='0'>"; //tabla interna para fuente
									echo"<tr>";
										echo"<td align='right' width='100'>Auditoria interna:</td><td align='left'><INPUT type='radio' name='fuente' id='au_inter' value='Auditoria Interna'>";
									echo"</tr>";
								echo"</table>";
							echo"</td>";
							echo"<td class='fila2'>";
								echo"<table border='0'>"; //tabla interna para fuente
									echo"<tr>";
										echo"<td align='right' width='100'>Auditoria externa:</td><td align='left'><INPUT type='radio' name='fuente' id='au_ext' value='Auditoria Externa'>";
									echo"</tr>";
								echo"</table>";
							echo"</td>";
							echo"<td class='fila2'>";
								echo"<table border='0'>"; //tabla interna para fuente
									echo"<tr>";
										echo"<td align='right' width='100'>Autocontrol:</td><td align='left'><INPUT type='radio' name='fuente' id='auto' value='Autocontrol'>";
									echo"</tr>";
								echo"</table>";
							echo"</td>";
							echo"<td class='fila2'>";
								echo"<table border='0'>"; //tabla interna para fuente
									echo"<tr>";
										echo"<td align='right' width='100'>Cliente externo:</td><td align='left'><INPUT type='radio' name='fuente' id='cli_ext' value='Cliente Externo'>";
									echo"</tr>";
							echo"</table>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";
				
			echo"<div id='unidad_genero' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6' >INFORMACION UNIDAD QUE GENERO</td>";
					echo"</tr>";				
					echo"<tr>";
						echo"<td class='fila1' width='200'>Unidad que genero</td><td class='fila2'><INPUT type='text' name='u_genero' id='u_genero' class='validarEA validarNC' size='30'>
								<button type='button' onClick=\"buscarCco('$cco');\">Buscar</button>";
							echo"<SELECT NAME='ccos' id='ccos'>"; 
							echo"<OPTION VALUE=''>Seleccione..</OPTION>";
							echo"</SELECT>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";
			
			
			echo"<div id='unidad_genero_mostrar' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6' >INFORMACION UNIDAD QUE GENERO</td>";
					echo"</tr>";				
					echo"<tr>";
						echo"<td class='fila1' width='200'>Unidad que genero</td><td class='fila2'><INPUT type='text' name='u_genero_mostrar' id='u_genero_mostrar' class='validarEA validarNC' size='50'>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>";
				

			echo"<div id='descripcion_conf' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>DESCRIPCION DEL EVENTO</td>";
					echo"</tr>";
						echo"<td align='left' width='100' class='fila1'>Descripcion:</td><td class='fila2'><TEXTAREA NAME='desc' id='desc' class='validarEA validarNC' ROWS=4 COLS=120></TEXTAREA></td>";
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
						echo"<td align='left' width='100' class='fila1'>Descripcion:</td><td class='fila2'><TEXTAREA NAME='desc_acc' id='desc_acc' class='validarEA' ROWS=4 COLS=120></TEXTAREA></td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>"; 
			
			
			echo"<div id='clasificacion_conf' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>CLASIFICACION DE LA NO CONFORMIDAD</td>";
				echo"</tr>";
				echo"<tr>";
					echo"<td align='left' width='100' class='fila1'>Caracterizacion de problemas</td>";
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
				echo"</table>";
			echo"</div>";
			
			echo"<div id='factores' class='divs'>";
				echo"<table align='center' width='1100' border='0' id='tabla_factores'>";
				echo"<tr>";
					echo"<td class='encabezadotabla' colspan='6'>FACTORES DETERMINANTES Y CONTRIBUTIVOS</td>";
				echo"</tr>";
				echo"<tr class='encabezadotabla'>";
					echo"<td>FACTORES</td><td>RELACIONADOS</td><td>CUALES</td><td>DIRECTO-INDIRECTO</td>";
					echo"<td><span onclick=\"addFila('tabla_factores','".$key."','".$wbasedato."','".$datos."','".$wemp_pmla."','".$cco."');\" class='efecto_boton' >".NOMBRE_ADICIONAR."</span></td>";
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
								$sql = "select Tipcod,Tipdes 
											from root_000094 
											where Tiptip='EA' 
											and Tipest='on'
											";		
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
									
								echo"<SELECT NAME='".$id_fila."_fact_1' id='".$id_fila."_fact_1' class='validarEA' onchange='llenar_hijo(\"".$id_fila."_fact_1\",\"".$id_fila."_cuales\",\"".$key."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla."\",\"".$cco."\")'>"; //llenar_hijo(\"".$id_fila."_cuales\")
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
					$sql = "select Tipcod,Tipdes 
											from root_000094 
											where Tiptip='EAG' 
											and Tipest='on'
											";		
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
									
					echo"<SELECT NAME='tipo_even1' id='tipo_even1' class='validarEA' onchange='llenar_evento(\"tipo_even1\",\"evento_hijo\",\"".$key."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla."\",\"".$cco."\" )'>"; 
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
			
			echo"<div id='infeccion' class='divs'>";
				echo"<table align='center' width='1100'>";
					echo"<tr>";
						echo"<td class='encabezadotabla' colspan='6'>TIPO DE EVENTO</td>"; 
					echo"</tr>";
					echo"<tr>";
						echo"<td align='center' width='200' class='fila2'>Infeccioso:<INPUT type='radio' name='infec' id='infec' value='Infeccioso' checked></td>";
						echo"<td align='center' width='200' class='fila2'>No Infeccioso:<INPUT type='radio' name='infec' id='no_infec' value='No Infeccioso'></td>";
						echo"<td align='center' width='300' class='fila2'>Relacionado con medicamentos o medios:<INPUT type='radio' name='infec' id='med' value='Relacionado con medicamentos o medios'></td>";
						echo"</td>";
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
					$sql = "select Caucod,Caudes 
											from root_000091 
											where Cautip='NC' 
											and Cauest='on'
											";		
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
									
					//echo"<SELECT multiple='multiple' NAME='causas_nc' id='causas_nc' onchange='select_causas(\"".$key."\",\"".$wbasedato."\",\"".$datos."\",\"".$wemp_pmla."\",\"".$cco."\")'>"; 
					echo"<SELECT multiple='multiple' NAME='causas_nc' id='causas_nc' class='validarNC' onchange='' style='width:250px; height:120px'>"; 
					//echo"<option value=''>Seleccione..</option>";
											for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
											{
												
												if( $clasificacion != trim( $rows['Tipcod'] ) )
												{
													echo "
																	<option value='".$rows['Caucod']."'>".utf8_encode($rows['Caudes'])."</option>";
												}
												else
												{
													echo "
																	<option value='".$rows['Caucod']."' >".utf8_encode($rows['Caudes'])."</option>";
												}
											}
					
					echo"</SELECT>";
					echo"</td>";
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
						echo"<td align='left' width='100' class='fila1'>Accion Correctiva</td>"; 
						echo"<td align='left' class='fila2'>&nbsp;&nbsp;&nbsp;&nbsp;
						Si<INPUT type='radio' name='accion' id='acc_si' value='Si'checked>&nbsp;&nbsp;
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
						estados();
						echo"<div id='tarea'></div>";
						echo"</td>";
					echo"</tr>";
				echo"</table>";
			echo"</div>"; 
		
			echo"<div id='enviar' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr class='fila2'>"; 	
					echo"<td colspan='5' align='center'><button type='button' onClick=\"enviar('$key','$wbasedato','$datos','$wemp_pmla','$cco','tabla_factores');\">Enviar</button></td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";
			
			echo"<div id='editar' class='divs'>";
				echo"<table align='center' width='1100'>";
				echo"<tr class='fila2'>"; 	
					echo"<td colspan='5' align='center'><button type='button' onClick=\"editar('$key','$wbasedato','$datos','$wemp_pmla','$cco','tabla_factores');\">Enviar</button></td>";
				echo"</tr>";
				echo"</table>";
			echo"</div>";
echo"</div>";
			//boton de cerrar
			echo"<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' /></center>";
			
echo"</form>";
echo"</body>";
echo"</html>";
}
?>