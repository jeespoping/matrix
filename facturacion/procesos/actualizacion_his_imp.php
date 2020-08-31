<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        ACTUALIZACION DE HISTORIAS PARA LA IMPRESION HCE
//=========================================================================================================================================\\
//DESCRIPCION:			Dada una historia e ingreso, se actualiza la informacion del paciente en matrix
//						con respecto a como este en Unix.
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	2013-02-06
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='28-enero-2017';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//	28-01-2020: Jerson Trujillo
//				Se cambia el tipo de la hce (Dettta = i)
//	28-04-2017: Jerson Trujillo
//				Se cambia el formulario de la hce y el consecutivo de los campos
//				Nuevo formulario 000360 
//	24-08-2016: Jerson trujillo
//				Se hace un replace del campo paclug por pacmun, por solicitud de soporte ya que era una incosistencia de los querys
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");

//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------------------
	//	Nombre:			Obtener querys
	//	Descripcion:	Funcion donde se crean los querys de los campos a consultar
	//					en unix, depediendo si se consulta en 'inpac' o en 'inpaci'
	//	Entradas:
	//	Salidas:		Retorna un array con los querys, con la siguiente estructura,
	//					array_querys[Detcon][inpac] =   Query a ejecutar;
	//					array_querys[Detcon][inpaci]=	Query a ejecutar;
	//--------------------------------------------------------------------------------
	function obtener_querys()
	{
		$array_querys = array();
		//----------------------------------------------
		// --> Detcon = 89 (Direccion de residencia)
		//----------------------------------------------
		// --> Query inpac
			$q = "select ' '
				   from inpac
				  where pachis=HIS
					and pacnum=ING
					and pacdir is null
				  union
				 select pacdir
				   from inpac
				  where pachis=HIS
					and pacnum=ING
					and pacdir is not null
				";
			$array_querys['89']['inpac'] = $q;
		// --> Query inpaci
			$q = "select ' '
				   from inpaci
				  where pachis=HIS
					and pacdir is null
				  union
				 select pacdir
				   from inpaci
				  where pachis=HIS
					and pacdir is not null
				";
			$array_querys['89']['inpaci'] = $q;
		//----------------------------------------------
		// --> Detcon = 90 (Ciudad)
		//----------------------------------------------
		// --> Query inpac
			$q = "select ' '
				   from inpac
				  where pachis=HIS
					and pacnum=ING
					and pacmun is null
				  union
				 select munnom
				   from inpac,inmun
				  where pachis=HIS
					and pacnum=ING
					and pacmun is not null
					and pacmun=muncod
				";
			$array_querys['90']['inpac'] = $q;
		// --> Query inpaci
			$q = "select ' '
				   from inpaci
				  where pachis=HIS
					and pacmun is null
				  union
				 select munnom
				   from inpaci,inmun
				  where pachis=HIS
					and pacmun is not null
					and pacmun=muncod
				";
			$array_querys['90']['inpaci'] = $q;
		//----------------------------------------------
		// --> Detcon = 91 (Departamento)
		//----------------------------------------------
		// --> Query inpac
			$q = " select ' '
				     from inpac
				    where pachis=HIS
					  and pacnum=ING
					  and pacmun is null
				    union
				   select depnom
				     from inpac,inmun,indep
				    where pachis=HIS
					  and pacnum=ING
					  and pacmun is not null
					  and pacmun=muncod
					  and mundep=depcod
				";
			$array_querys['91']['inpac'] = $q;
		// --> Query inpaci
			$q = " select ' '
				     from inpaci
				    where pachis=HIS
					  and pacmun is null
				    union
				   select depnom
				     from inpaci,inmun,indep
				    where pachis=HIS
					  and pacmun is not null
					  and pacmun=muncod
					  and mundep=depcod
				";
			$array_querys['91']['inpaci'] = $q;
		//----------------------------------------------
		// --> Detcon = 92 (Telefono)
		//----------------------------------------------
		// --> Query inpac
			$q = "select ' '
				    from inpac
				   where pachis=HIS
					 and pacnum=ING
					 and pactel is null
				   union
				  select pactel
				    from inpac
				   where pachis=HIS
					 and pacnum=ING
					 and pactel is not null
					";
			$array_querys['92']['inpac'] = $q;
		// --> Query inpaci
			$q = "select ' '
				    from inpaci
				   where pachis=HIS
					 and pactel is null
				   union
				  select pactel
				    from inpaci
				   where pachis=HIS
					 and pactel is not null
					";
			$array_querys['92']['inpaci'] = $q;
		//----------------------------------------------
		// --> Detcon = 80 (CC)
		//----------------------------------------------
		// --> Query inpac
			$q = " select ' '
				     from inpac
				    where pachis=HIS
					  and pacnum=ING
					  and pacced is null
				    union
				   select pacced
				     from inpac
				    where pachis=HIS
					  and pacnum=ING
					  and pacced is not null
				";
			$array_querys['80']['inpac'] = $q;
		// --> Query inpaci
			$q = " select ' '
				     from inpaci
				    where pachis=HIS
					  and pacced is null
				    union
				   select pacced
				     from inpaci
				    where pachis=HIS
					  and pacced is not null
				";
			$array_querys['80']['inpaci'] = $q;
		//----------------------------------------------
		// --> Detcon = 94 (El dia)
		//----------------------------------------------
		// --> Query inpac
			$q = "   select DATE('00/00/000') ,accacc
					   from inpac,inacc,inaccdet
					  where pachis=HIS
						and pacnum=ING
						and pachis=acchis
						and pacnum=accnum
						and pachis=accdethis
						and accacc=accdetacc
						and accdetfec is null
					  union
					 select accdetfec,accacc
					   from inpac,inacc,inaccdet
					  where pachis=HIS
						and pacnum=ING
						and pachis=acchis
						and pacnum=accnum
						and pachis=accdethis
						and accacc=accdetacc
						and accdetfec is not null
					  order by 2 desc
				";
			$array_querys['94']['inpac'] = $q;
		// --> Query inpaci
			$q = "   select DATE('00/00/000') ,accacc
					   from inpaci,inacc,inaccdet
					  where pachis=HIS
						and pachis=acchis
						and accnum=ING
						and pachis=accdethis
						and accacc=accdetacc
						and accdetfec is null
					  union
					 select accdetfec,accacc
					   from inpaci,inacc,inaccdet
					  where pachis=HIS
						and pachis=acchis
						and accnum=ING
						and pachis=accdethis
						and accacc=accdetacc
						and accdetfec is not null
					  order by 2 desc
				";
			$array_querys['94']['inpaci'] = $q;
		//----------------------------------------------
		// --> Detcon = 95 (a las)
		//----------------------------------------------
		// --> Query inpac
			$q = "   select 0.0 ,accacc
					   from inpac,inacc,inaccdet
					  where pachis=HIS
						and pacnum=ING
						and pachis=acchis
						and pacnum=accnum
						and pachis=accdethis
						and accacc=accdetacc
						and accdethor is null
					  union
					 select accdethor,accacc
					   from inpac,inacc,inaccdet
					  where pachis=HIS
						and pacnum=ING
						and pachis=acchis
						and pacnum=accnum
						and pachis=accdethis
						and accacc=accdetacc
						and accdethor is not null
					order by 2 desc
				";
			$array_querys['95']['inpac'] = $q;
		// --> Query inpaci
			$q = "   select 0.0 ,accacc
					   from inpaci,inacc,inaccdet
					  where pachis=HIS
						and pachis=acchis
						and accnum=ING
						and pachis=accdethis
						and accacc=accdetacc
						and accdethor is null
					  union
					 select accdethor,accacc
					   from inpaci,inacc,inaccdet
					  where pachis=HIS
						and pachis=acchis
						and accnum=ING
						and pachis=accdethis
						and accacc=accdetacc
						and accdethor is not null
					order by 2 desc
				";
			$array_querys['95']['inpaci'] = $q;
		//----------------------------------------------------------------
		// --> Detcon = 97 (Tipo de consulta (P=Primera vez, C=Control))
		//----------------------------------------------------------------
		// --> Query inpac
			$q = "   select ' ' ,accacc
					   from inpac,inacc
					  where pachis=HIS
						and pacnum=ING
						and pachis=acchis
						and pacnum=accnum
						and accind is null
					  union
					 select accind,accacc
					   from inpac,inacc
					  where pachis=HIS
						and pacnum=ING
						and pachis=acchis
						and pacnum=accnum
						and accind is not null
					order by 2 desc
				";
			$array_querys['97']['inpac'] = $q;
		// --> Query inpaci
			$q = "   select ' ' ,accacc
					   from inpaci,inacc
					  where pachis=HIS
						and pachis=acchis
						and accnum=ING
						and accind is null
					  union
					 select accind,accacc
					   from inpaci,inacc
					  where pachis=HIS
						and pachis=acchis
						and accnum=ING
						and accind is not null
					order by 2 desc
				";
			$array_querys['97']['inpaci'] = $q;
		//----------------------------------------------------------------------------------
		// --> Detcon = 96 (Ingresando al servicio de urgencias de esta institucion el dia)
		//----------------------------------------------------------------------------------
		// --> Query inpac
			$q = "   select DATE('00/00/000')
					   from inpac
					  where pachis=HIS
						and pacnum=ING
						and pacfec is null
					  union
					 select pacfec
					   from inpac
					  where pachis=HIS
						and pacnum=ING
						and pacfec is not null
				";
			$array_querys['96']['inpac'] = $q;
		// --> Query inpaci
			$q = "   select DATE('00/00/000')
					   from inmegr
					  where egrhis=HIS
					    and egrnum=ING
						and egring is null
					  union
					 select egring
					   from inmegr
					  where egrhis=HIS
					    and egrnum=ING
						and egring is not null
				";
			$array_querys['96']['inpaci'] = $q;
		//----------------------------------------------------------------------------------
		// --> Detcon = 98 (a las)
		//----------------------------------------------------------------------------------
		// --> Query inpac
			$q = "   select 0.0
					   from inpac
					  where pachis=HIS
						and pacnum=ING
						and pachor is null
					  union
					 select pachor
					   from inpac
					  where pachis=HIS
						and pacnum=ING
						and pachor is not null
				";
			$array_querys['98']['inpac'] = $q;
		// --> Query inpaci
			$q = "   select 0.0
					   from inmegr
					  where egrhis=HIS
					    and egrnum=ING
						and egrhoi is null
					  union
					 select egrhoi
					   from inmegr
					  where egrhis=HIS
					    and egrnum=ING
						and egrhoi is not null
				";
			$array_querys['98']['inpaci'] = $q;
		//----------------------------------------------------------------------------------
		// --> Detcon = 227 (Direccion del lugar del accidente)
		//----------------------------------------------------------------------------------
		// --> Query inpac
			$q = "   select accdetlug
					   from inpac,inaccdet,inmun,indep,inacc
					  where pachis=HIS
						and pacnum=ING
						and pacmun=muncod
						and mundep=depcod
						and pachis=acchis
						and pacnum=accnum
						and pachis=accdethis
						and accacc=accdetacc
					order by 1 desc
				";
			$array_querys['227']['inpac'] = $q;
		// --> Query inpaci
			$q = "   select accdetlug
					   from inpaci,inaccdet,inmun,indep,inacc
					  where pachis=HIS
						and pacmun=muncod
						and mundep=depcod
						and pachis=acchis
						and accnum=ING
						and pachis=accdethis
						and accacc=accdetacc
					order by 1 desc
				";
			$array_querys['227']['inpaci'] = $q;
		//----------------------------------------------------------------------------------
		// --> Detcon = 100 (Ubicacion)
		//----------------------------------------------------------------------------------
		// --> Query inpac
			$q = ' select CONCAT(movhos_000018.Ubisac,"-",movhos_000011.Cconom," Hab. ",movhos_000018.Ubihac) from movhos_000018,movhos_000011 where movhos_000018.ubihis=HIS   and  movhos_000018.ubiing=ING  and movhos_000018.ubisac= movhos_000011.ccocod
				';
			$array_querys['100']['inpac'] = $q;
		// --> Query inpaci
			$q = ' select CONCAT(movhos_000018.Ubisac,"-",movhos_000011.Cconom," Hab. ",movhos_000018.Ubihac) from movhos_000018,movhos_000011 where movhos_000018.ubihis=HIS   and  movhos_000018.ubiing=ING  and movhos_000018.ubisac= movhos_000011.ccocod
				';
			$array_querys['100']['inpaci'] = $q;


		return $array_querys;
	}

//=======================================================================================================================================================
//	F I N  F U N C I O N E S  P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//	F I L T R O S  D E  L L A M A D O S  P O R  J Q U E R Y  O  A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	$cod_formulario = '000360';

	switch($accion)
	{
		case 'obtener_ingresos':
		{
			$q_ingresos = "SELECT Firing
							 FROM ".$wbasedato."_000036
							WHERE Firhis='".$fhistoria."'
						 GROUP BY 1
						 ORDER BY 1 ";
			$res_igresos = mysql_query($q_ingresos,$conex) or die ("Query (Lista de ingresos): ".$q_ingresos." - ".mysql_error());

			if(mysql_num_rows($res_igresos) > 0)
			{
				echo "<select name='fingreso' id='fingreso' onChange='analizar_actualizacion(\"\");'>";
				while($row_ingresos = mysql_fetch_array($res_igresos))
				{
					echo "<option>".$row_ingresos['Firing']."</option>";
				}
				echo "</select>";
			}
			else
			{
				echo "No se registran ingresos";
			}
			break;
			return;
		}
		case 'analizar_actualizacion':
		{
			$arr_querys = obtener_querys();
			$arr_actualizar = array();
			$arr_insertar = array();

			// --> Obtengo los campos hce que se van a revisar
			$q_campos_hce = " SELECT Detcon, Detdes, Detorp, Detarc
								FROM ".$wbasedato."_000002
							   WHERE Detpro = '".$cod_formulario."'
							     AND Dettip = 'memo'
								 AND Dettta IN ('i')
								 AND Detest = 'on'
							ORDER BY Detorp
							";
			$res_campos_hce = mysql_query($q_campos_hce,$conex) or die ("Query (Campos del formulario 137 hce): ".$q_campos_hce." - ".mysql_error());

			$array_campos = array();
			while($row_campos_hce = mysql_fetch_array($res_campos_hce))
			{
				$array_campos[$row_campos_hce['Detcon']]['descripcion'] = $row_campos_hce['Detdes'];
				$array_campos[$row_campos_hce['Detcon']]['orden'] 		= $row_campos_hce['Detorp'];
				$array_campos[$row_campos_hce['Detcon']]['origen_datos']= $row_campos_hce['Detarc'];
			}

			// --> Recorro el array de los campos
			echo "<table>
					<tr class='Encabezadotabla'>
						<td>Descripción</td>
						<td>Valor matrix</td>
						<td>Valor unix</td>
						<td>Actualizado</td>
					</tr>";
			foreach ($array_campos as $detcon => $arr_valores)
			{
				$descripcion = $arr_valores['descripcion'];
				$query 	 	 = $arr_querys[$detcon];
				$orden 	 	 = $arr_valores['orden'];
				$origen_datos= $arr_valores['origen_datos'];
				$valor_matrix= '';
				$valor_unix  = '';
				//--------------------------------------------
				// --> Obtengo el valor del campo en matrix
				//--------------------------------------------
				$q_valmatrix = " SELECT movdat, id
								   FROM ".$wbasedato."_".$cod_formulario."
								  WHERE movpro = '".$cod_formulario."'
								    AND movcon = '".$detcon."'
									AND movhis = '".$fhistoria."'
									AND moving = '".$fingreso."'
								";
				$res_valmatrix = mysql_query($q_valmatrix,$conex) or die ("Query (Obtener valor en matrix): ".$q_valmatrix." - ".mysql_error());
				if($row_valmatrix = mysql_fetch_array($res_valmatrix))
				{
					$valor_matrix = $row_valmatrix['movdat'];
					$id_matrix    = $row_valmatrix['id'];
				}
				else
				{
					$valor_matrix = '';
					$id_matrix    = '';
				}
				//--------------------------------------------
				// <-- Fin valor del campo en matrix
				//--------------------------------------------

				//--------------------------------------------
				// --> Obtengo el valor del campo en unix
				//--------------------------------------------

				// --> Validar si el paciente esta activo en unix,
				//     para determinar si se conuslta en inpac o en inpaci
				$q_activo = "SELECT count(*)
							   FROM inpac
							  WHERE pachis = '".$fhistoria."'
							    AND pacnum = '".$fingreso."'
							   ";
				$conex_admis = odbc_connect('admisiones','informix','sco');
				$res_admis = odbc_exec($conex_admis, $q_activo);
				odbc_fetch_row($res_admis);
				$activo = odbc_result($res_admis, 1);
				if($activo > 0)
					$tabla_unix = 'inpac';
				else
					$tabla_unix = 'inpaci';
				// <-- FIn validación

				$formula   = $query[$tabla_unix];
				$q_valunix = str_replace('HIS', $fhistoria, $formula);
				$q_valunix = str_replace('ING', $fingreso, $q_valunix);

				// --> Es un query de unix
				if($origen_datos != '')
				{
					if($q_valunix != ""){
						$conexunix = odbc_connect($origen_datos,'informix','sco');
						$res = odbc_exec($conexunix,$q_valunix);
						if (!$res)
						{
							echo "Error en el query: ".$q_valunix;
							echo "<br>Tipo Error:".odbc_errormsg();
						}
						else
						{
							odbc_fetch_row($res);
							$valor_unix = odbc_result($res, 1);
						}
						
						odbc_close($conexunix);
						odbc_close_all();
					}
				}
				// --> Es un query de matrix
				else
				{
					$res = mysql_query($q_valunix, $conex);
					if (!$res)
					{
						echo "Error en el query: ".$q_valunix;
						echo "<br>Tipo Error:".mysql_error();
					}
					else
					{
						$row_res = mysql_fetch_array($res);
						$valor_unix = $row_res[0];
					}
				}
				//--------------------------------------------
				// <-- Fin valor del campo en unix
				//--------------------------------------------
				if($valor_unix !='')
				{
					$actualizar = 'no';
					if(trim($valor_matrix) != trim($valor_unix))
					{
						$actualizar = 'si';
						if($id_matrix != '')
						{
							// --> Array donde va quedando el id del campo a actualizar con su correspondiente valor
							$arr_actualizar[$id_matrix] = $valor_unix;
						}
						// Si el id es igual a vacio, indica que el campo no existe en matrix, lo que implica que lo debo crear
						else
						{
							// --> Array para guardar los campos que debo insertar, con su correspondiente valor
							$arr_insertar[$detcon] = $valor_unix;
						}
					}
					// --> Pintar Simulacion
					echo "<tr class='Fila2'>
							<td>".$descripcion."</td>
							<td>".$valor_matrix."</td>
							<td>".$valor_unix."</td>
							<td align='center'>".(($actualizar == 'si') ? '<img width="10" height="10" border="0" src="../../images/medical/eliminar1.png" />' : '<img width="10" height="10" border="0" src="../../images/medical/root/grabar.png" />') ."</td>
						</tr>";
				}
				
				//odbc_close($conex_admis);
				//odbc_close_all();
			}
			if (count($arr_actualizar) > 0 || count($arr_insertar) > 0) // Si hay campos por actualizar activo el boton
			{
				echo "	<tr><td colspan='4' align='center'><br><button>Actualizar</button></td></tr>";
			}
			echo "</table><br>";

			// --> Creo un hidden con un json del array de los id a actualizar
			echo "<input type='hidden' id='hidden_update' value='".json_encode($arr_actualizar)."'>";
			// --> Creo un hidden con un json del array de los campos a insertar
			echo "<input type='hidden' id='hidden_insert' value='".json_encode($arr_insertar)."'>";
			// --> hidden con la historia
			echo "<input type='hidden' id='hidden_historia' value='".$fhistoria."'>";
			// --> hidden con el ingreso
			echo "<input type='hidden' id='hidden_ingreso' value='".$fingreso."'>";

			break;
			return;
		}

		case 'realizar_actualizacion':
		{
			$contador = 0;
			// --> Decodifico los json, para convertirlos en arrays.
			$arr_campos_update = json_decode(str_replace('\\', '', $campos_update), true);
			$arr_campos_insert = json_decode(str_replace('\\', '', $campos_insert), true);

			// --> Realizo los updates
			foreach($arr_campos_update as $id => $valor)
			{
				$q_update = "UPDATE ".$wbasedato."_000360
								SET movdat = '".$valor."'
							  WHERE id = '".$id."'
							";
				mysql_query($q_update,$conex) or die ("Query (Actualizar campo en matrix): ".$q_update." - ".mysql_error());
				$contador++;
			}

			// --> Realizo los insert
			//Primero obtengo el Fecha_data y Hora_data de algun campo que ya tenga el paciente
			if (count($arr_campos_insert) > 0)
			{
				$q_fec_hor = "SELECT Medico, Fecha_data, Hora_data, movusu, Seguridad
								FROM ".$wbasedato."_".$cod_formulario."
							   WHERE movhis = '".$historia."'
								 AND moving = '".$ingreso."'
							GROUP BY movhis, moving
								";
				$res_fec_hor = mysql_query($q_fec_hor,$conex) or die ("Query (Obtener Fecha_data, Hora_data): ".$q_fec_hor." - ".mysql_error());

				if($row_inf = mysql_fetch_array($res_fec_hor))
				{
					// --> Obtener los tipos de los campos, segun la hce_000002
					$in_detcon = "";
					$arr_tip_cam = array();
					// Armo una variable con los movcon para relizar un in en el query
					foreach ($arr_campos_insert as $movcon => $valor)
					{
						if ($in_detcon == "")
							$in_detcon = "'".$movcon."'";
						else
							$in_detcon.= ", '".$movcon."'";
					}

					$q_tip_cam = "SELECT Detcon, Dettip
									FROM ".$wbasedato."_000002
								   WHERE Detpro = '".$cod_formulario."'
									 AND Detcon IN (".$in_detcon.")
								   ORDER BY Detcon
										";
					$res_tip_cam = mysql_query($q_tip_cam,$conex) or die ("Query (Obtener tipos de campos): ".$q_tip_cam." - ".mysql_error());
					while($row_tip_cam = mysql_fetch_array ($res_tip_cam))
					{
						$arr_tip_cam[$row_tip_cam['Detcon']] = $row_tip_cam['Dettip'];
					}
					// <-- Fin obtener los tipos de los campos

					// --> Realizo los insert
					foreach($arr_campos_insert as $movcon => $valor)
					{

						$q_insert = "INSERT INTO ".$wbasedato."_".$cod_formulario."
											(			Medico, 			Fecha_data, 						Hora_data, 					movpro, 		movcon, 		movhis, 		moving, 				movtip, 			movdat, 				movusu, 				Seguridad )
									 VALUES ('".$row_inf['Medico']."', '".$row_inf['Fecha_data']."', '".$row_inf['Hora_data']."', '".$cod_formulario."', '".$movcon."', '".$historia."', '".$ingreso."', '".$arr_tip_cam[$movcon]."', '".$valor."', '".$row_inf['movusu']."', '".$row_inf['Seguridad']."')
									";
						mysql_query($q_insert,$conex) or die ("Query (Insertar campo en matrix): ".$q_insert." - ".mysql_error());
						$contador++;
					}
				}
			}
			echo "Actualización finalizada.<br> ".$contador." Registros actualizados.";
			break;
			return;
		}
	}
}
//=======================================================================================================================================================
//	F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='wuse' name='wuse' value='".$wuse."'>";
	?>
	<html>
	<head>
	  <title>Actualizacion formulario accidentes de transito desde Unix</title>
	</head>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	$(document).ready(function() {
		cargar_buttons('cerrar');
	});

	//-------------------------------------------------------------------------------
	//	Nombre:			Analizar actualizacion
	//	Descripcion:	Pinta los campos que se actualizaran
	//	Entradas:
	//	Salidas:
	//--------------------------------------------------------------------------------
	function realizar_actualizacion()
	{
		var historia       = $('#hidden_historia').val();
		var ingreso        = $('#hidden_ingreso').val();
		var campos_update  = $('#hidden_update').val();
		var campos_insert  = $('#hidden_insert').val();

		$.post("actualizacion_his_imp.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				accion:         		'realizar_actualizacion',
				historia:				historia,
				ingreso:				ingreso,
				campos_update: 			campos_update,
				campos_insert:			campos_insert
			}
			,function(respuesta) {
				analizar_actualizacion(respuesta);
			}
		);
	}
	//-------------------------------------------------------------------------------
	//	Nombre:			Analizar actualizacion
	//	Descripcion:	Pinta los campos que se actualizaran
	//	Entradas:
	//	Salidas:
	//--------------------------------------------------------------------------------
	function analizar_actualizacion(texto)
	{
		$.post("actualizacion_his_imp.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				accion:         		'analizar_actualizacion',
				fhistoria:				$('#fhistoria').val(),
				fingreso:				$('#fingreso').val()
			}
			,function(respuesta) {

				$('#contenedor').hide();

				if(texto != '')
				{
					$('#analisis').html(respuesta+'<table class=Fondoamarillo style="border: 1px solid #2A5DB0;"><tr><td align=center>'+texto+'</td></tr></table>');
					$('#contenedor').show();
				}
				else
				{
					$('#analisis').html(respuesta);
					$('#contenedor').show(500);
				}

				//Cerrar el boton de analizar
				$( "button" ).each(function( index ) {
					if ( $(this).text() == 'Analizar')
					{
						$(this).hide();
					}
				});

				//Crear el boton de actualizar
				$( "button" ).each(function( index ) {
					if ( $(this).text() == 'Actualizar')
					{
						$(this)
							.button({icons:	{
											primary: "ui-icon ui-icon-refresh"
											}
									})
							.click(function( event ){
										realizar_actualizacion();
									});
					}
				});
			}
		);
	}
	//-------------------------------------------------------------------------------
	//	Nombre:			Cargar buttons
	//	Descripcion:	Activa el plug-in jquery para la visualizacion de los button
	//	Entradas:
	//	Salidas:
	//--------------------------------------------------------------------------------
	function cargar_buttons(boton)
	{
		$( "button" ).each(function( index ) {

			switch ($(this).text())
			{
				case 'Analizar':
						if(boton == 'analizar')
						{
							$(this)
								.button({icons:	{
												primary: "ui-icon-power"
												}
										})
								.click(function( event ){
											analizar_actualizacion("");
										});
						}
						break;
				case 'Cerrar Ventana':
						if(boton == 'cerrar')
						{
							$(this)
								.button({icons:	{
												primary: "ui-icon-circle-close"
												}
										})
								.click(function( event ){
											window.close();
										});
						}
						break;
			}
		});
	}
	//-------------------------------------------------
	//	Nombre:			obtener ingresos
	//	Descripcion:	Hace el llamado para obtener los ingresos que ha tenido una historia, y pinta un select de dichos ingresos
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------
	function obtener_ingresos()
	{
		if ($('#fhistoria').val() != '')
		{
			$.post("actualizacion_his_imp.php",
				{
					consultaAjax:   		'',
					wemp_pmla:      		$('#wemp_pmla').val(),
					wuse:           		$('#wuse').val(),
					wbasedato:				$('#wbasedato').val(),
					accion:         		'obtener_ingresos',
					fhistoria:				$('#fhistoria').val()
				}
				,function(respuesta) {
					if(respuesta == 'No se registran ingresos')
					{
						$('#td_ingreso').html('<b>'+respuesta+'<b>');
						$('#tr_ingreso').show(300);
						//Cerrar el boton de analizar
						$( "button" ).each(function( index ) {
							if ( $(this).text() == 'Analizar')
							{
								$(this).hide();
							}
						});
						$('#contenedor').hide();
					}
					else
					{
						$('#td_ingreso').html(respuesta);
						$('#tr_ingreso').show(300);
						$('#td_actualizar').html('<br><button>Analizar</button>');
						$('#td_actualizar').show(300);

						//Cargar la visualizacion de los botones
						cargar_buttons('analizar');
					}
				}
			);
		}
		else
		{
			$('#td_ingreso').html('Debe ingresar la historia.');
			$('#tr_ingreso').show(300);
		}
	}
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.Titulo_azul{
			color:#000066;
			font-weight: bold;
			font-family: verdana;
			font-size: 11pt;
		}
		.borderDiv2{
			border: 2px solid #2A5DB0;
			padding: 15px;
		}
		.parrafo_text{
			background-color: #666666;
			color: #FFFFFF;
			font-family: verdana;
			font-size: 10pt;
			font-weight: bold;
		}
		.borderDiv {
			border: 1px solid #2A5DB0;
			padding: 5px;
		}
		.Titulo_azul{
			color:#000066;
			font-weight: bold;
			font-family: verdana;
			font-size: 11pt;
		}
	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<body>
	<?php

	// -->	ENCABEZADO

	encabezado("Actualizacion formulario accidentes de transito desde Unix", $wactualiz, 'clinica');
	echo "
	<div align='center'>
		<table width='81%' border='0' cellpadding='3' cellspacing='3'>
			<tr>
				<td align='left'>
					<div align='center'>
						<table width='30%'>
							<tr><td align='center' class='parrafo_text' colspan='2'>Datos del paciente</td></tr>
							<tr>
								<td class='Encabezadotabla'>Historia:</td><td class='Fila2' align='center'><input type='text' size=20 name='fhistoria' id='fhistoria' onBlur='obtener_ingresos();'></td>
							</tr>
							<tr style='display:none;' id='tr_ingreso'>
								<td class='Encabezadotabla'>Ingreso:</td><td class='Fila2' align='center' id='td_ingreso'></td>
							</tr>
							<tr>
								<td id='td_actualizar' style='display:none;' colspan='2' align='center'></td>
							</tr>
						</table>
					</div>
					<div align='center' class='borderDiv2' style='Display:none' id='contenedor'>
						<div class='borderDiv Titulo_azul' align=center>
							Información a actualizar
						</div><br>
						<div id='analisis' align=center>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<br><br>
		<div align=center>
			<button>Cerrar Ventana</button>
		</div><br>
	</div>
	";



	?>
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}

}//Fin de session
?>
