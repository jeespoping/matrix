<?php
include_once("conex.php");
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");


if(!isset($_SESSION['user']))
	echo "error";
else
{

  header('Content-Type: text/html; charset=UTF-8');

  

  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  include_once("movhos/classRegleta.php");
  

  
  
    function pintarPaciente2( $pacientes ){
	
	global $wemp_pmla;
	global $wfecha;
	
	echo "<table align=center>";
	echo "<tr class=encabezadoTabla>";
	echo "<th>Historia</th>";
	echo "<th>Ingreso</th>";
	echo "<th>Habitación</th>";
	echo "<th>Paciente</th>";
	echo "<th>Dosis</th>";
	echo "<th>Condición</th>";
	echo "<th>Acción</th>";
	echo "</tr>";
	
	//Pinto los pacientes que están asociados a los medicamentos
	foreach( $pacientes as $keyPacientes => $valuePacientes ){
		
		if( $wclass=="fila1" )
			$wclass="fila2";
		else
			$wclass="fila1";
		
		echo "<tr class='".$wclass."'>";
		echo "<td align=center>".$valuePacientes['historia']."</td>";
		echo "<td align=center>".$valuePacientes['ingreso']."</td>";
		echo "<td align=center>".$valuePacientes['habitacion']."</td>";
		echo "<td>".$valuePacientes['nombre']."</td>";
		echo "<td>".$valuePacientes['dosis']."</td>";
		echo "<td><b>".$valuePacientes['condicion']."</b></td>";
		echo "<td align=center><A href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valuePacientes['historia']."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>";
		echo "</tr>";
	}
	
	echo "</table>";
}

function pintarAritculos( $articulos ){
							
	global $wccotim;
	global $totalRondas;
	global $whora_par_act;
	
	echo "<center><table>";
	echo "<tr class=encabezadoTabla>";
	echo "<th colspan=1><font size=4>Ronda</font></th>";
	echo "<th colspan=3><font size=4>Medicamento</font></th>";
	echo "<th colspan=2><font size=4>Dosis<br>según Kardex</font></th>";
	echo "<th colspan=2><font size=4>Cantidad Unidades<br>según Perfil</font></th>";
	echo "<th colspan=1><font size=4>No Pos</font></th>";
	echo "<th colspan=1><font size=4>Es de<br>Control</font></th>";
	echo "</tr>";
	
	$i = 0;
	//Reocorro todas las rondas posibles
	for( $j = 0; $j < $totalRondas; $j += 2 ){
		
		$ronda = gmdate( "H", $whora_par_act*3600+$j*3600 );
		
		//Se muestra los datos correspondientes a las rondas correspondientes
		if( !empty( $articulos[ $ronda ] ) ){
			
			$valueDatos = $articulos[ $ronda ];	//Son los datos correspondientes a los articulos
				
			if($wclass=="fila1")
				$wclass="fila2";
			else
				$wclass="fila1";
			
			// $ronda = $keyDatos;
			$mostrarRonda = true;
			
			foreach( $valueDatos as $keyArts => $valueArts ){
				
				$idTablaPacientes = $i.$valueArts['codigoArticulo'];
				
				echo "<tr class='".$wclass."' onclick=\"intercalar('".$idTablaPacientes."','".$ronda."')\">";
				
				if( $mostrarRonda )
					echo "<td id=tdRonda$ronda rowspan='".( count( $valueDatos ) )."' align=center><b style='font-size:12pt'>".$ronda."</b></td>";		//Ronda
				
				echo "<td>".$valueArts['ubicacion']."</td>";                    		//Ubicación del medicamento
				echo "<td>".$valueArts['codigoArticulo']."</td>";                    	//Codigo Medicamento
				echo "<td>".$valueArts['nombreArticulo']."</td>";                    	//Nombre Medicamento
				
				if ($valueArts['aprobado'] == "on")                            			//Indica si esta aprobado en el perfil
				{
					echo "<td align=center>".$valueArts['cantidadDosis']."</td>";   	//Cantidad de Dosis
					echo "<td align=center>".$valueArts['fraccionDosis']."</td>";   	//Fraccion de la dosis
					echo "<td align=center>".$valueArts['cantidadUnidades']."</td>";   	//Can. Unidades
					echo "<td align=center>".$valueArts['presentacion']."</td>";   		//Presentacion
				}
				else{
					if( $wccotim == "SF" )
						echo "<td align=center colspan=4 bgcolor=FFFFCC>Sin Aprobar en el perfil</td>";
					else{
						if( $valueArts['confirmado'] == "off" )               			//No confirmado. Es antibiotico
							echo "<td align=center colspan=4 bgcolor=FFFFCC>Medicamento Sin Confirmar en el Kardex</td>";
						else
							echo "<td align=center colspan=4 bgcolor=FFFFCC>Sin Aprobar en el perfil</td>";
					}
				}

				echo "<td align=center>".$valueArts['noPos']."</td>";     				//Es Pos
				echo "<td align=center>".$valueArts['control']."</td>";   				//Es de Control
				echo "</tr>";

				echo "<tr id='".$idTablaPacientes."' style='display:none'>";
				echo "<td colspan=10 align=center>";
					pintarPaciente2( $valueArts['pacientes'] );	//Pinta los pacientes que hay por articulo
				echo "</td>";
				echo "</tr>";
				
				$mostrarRonda = false;
				$i++;
			}
		}
	}
	
	echo "</table></center>";
}




  //FUNCION QUE REGISTRA O EL LOG DE IMPRESIONES DE STICKERS Y VERIFICA SI YA ESTAN IMPRESOS
  function verificar_impr_stickers($wbasedato, $whis, $wing, $whora_par_inicial, $whora_par_final, $wcco, $wusuario)
  {

		global $conex;

		$wfecha = date("Y-m-d");
		$whora = (string) date("H:i:s");
		$wusuario = explode("-",$wusuario);
	    $wcco = explode("-",$wcco);

		$query =  " SELECT * "
				. "   FROM ".$wbasedato."_000122"
				. "  WHERE Stihis = '" . $whis . "'"
				. "    AND Stiing = '" . $wing . "'"
				. "    AND Stifec = '" . $wfecha. "'"
				. "    AND Stiroi = '".$whora_par_inicial."'"
				. "	   AND Stirof = '".$whora_par_final."'";
		$res = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$west = $row['Stiest'];

			if($west != 'on')
				{
					//Funcion que inserta las historias e ingresos para los stickers impresos.
					$query = "INSERT INTO ".$wbasedato."_000122 ( medico, Fecha_data , Hora_data, Stihis, Stiing, Stiusu, Sticco, Stiroi, Stirof, Stifec, Stiest, Seguridad ) "
					 ."VALUES ('".$wbasedato."',
								 '".$wfecha."' ,
								 '".$whora."',
								 '".$whis."',
								 '".$wing."',
								 '".$wusuario[1]."',
								 '".$wcco[0]."',
								 '".$whora_par_inicial."',
								 '".$whora_par_final."',
								 '".$wfecha."',
								 'on',
								 'C-".$wusuario[1]."') ";

					$res = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error());

				}


  }


  //funcion que imprime los sickers que se muestran en la interfaz
  function stickers_paquete($wemp_pmla,$wbasedatos, $wdatos_pacientes, $wccoo, $whora_inicial, $whora_final, $reimpirmir, $wconfirmacion, $wcconom)
  {

	  global $conex;
	  global $wipimpresora;

	  $datamensaje = array('mensaje'=>'', 'error'=>0, 'wdatos_paciente'=>'', 'wccoo'=>'', 'whora_inicial'=>'', 'whora_final'=>'');

	  $dato_hora_ini = explode(" ",$whora_inicial);
	  $dato_hora_fin = explode(" ",$whora_final);

	  $wdatos_pacientes_aux = $wdatos_pacientes; //Esta variable sirve para ser reenviada con los datos de los pacientes serializados.

	  //$wipimpresora = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ImpresoraStickersDispen'); //Traigo la ip de la impresora d ela root_000051
	  $wdatos_pacientes = unserialize(base64_decode($wdatos_pacientes)); //Hago unserialize a la variable $wdatos_pacientes, para poder recorrerlo con el foreach.

	//Valida si la ronda ya fue impresa.
	$q_ron =" SELECT id
				FROM ".$wbasedatos."_000122
			   WHERE Stiest = 'on'
				 AND Stifec = CURDATE( )
				 AND Sticco = '".$wccoo."'
				 AND Stiroi = '".$dato_hora_ini[0]."'
				 AND Stirof = '".$dato_hora_fin[0]."'
			GROUP BY Stiroi, Stirof";
	$resRon = mysql_query($q_ron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_ron." - ".mysql_error());
	$numRon = mysql_num_rows($resRon);




	//Si ya esya impresa la ronsa y la variable $reimprimir esta declarada en no, entonces muestra mensaje js pidiendo la confirmacion de volver a imprimir.
	if($numRon > 0 and $reimpirmir == 'no')
		{
		  //Se signan las variables al arreglo $datamensaje para que muestre la alerta al usuario y al aceptar vuelva a pasar por esta funcion pero con la variable $reimprimir = 'si', para que no ingrese
		  //a esta validacion, sino que imprima los datos del arreglo $wdatos_pacientes.
		  $datamensaje['mensaje'] = '¿Ya imprimió esta ronda para este centro de costos, desesa imprimirla de nuevo?';
		  $datamensaje['error'] = '1';
		  $datamensaje['wdatos_paciente'] = $wdatos_pacientes_aux;
		  $datamensaje['wccoo'] = $wccoo;
		  $datamensaje['whora_inicial'] = $dato_hora_ini[0];
		  $datamensaje['whora_final'] = $dato_hora_fin[0];
		  $datamensaje['wemp_pmla'] = $wemp_pmla;
		  $datamensaje['wbasedatos'] = $wbasedatos;

		}
	else
		{

		  //Se reservan los datos para que despues de confirmar la impresion sean enviados de nuevo a esta funcion por medio de ajax, la posicion $datamensaje['error'] = '2', valida si el
			//usuario desea imprimir o no los stickers.
		  $datamensaje['mensaje'] = "¿Confirma que desea imprimir esta ronda para el centro de costos ".$wcconom."?";
		  $datamensaje['error'] = '2';
		  $datamensaje['wdatos_paciente'] = $wdatos_pacientes_aux;
		  $datamensaje['wccoo'] = $wccoo;
		  $datamensaje['whora_inicial'] = $dato_hora_ini[0];
		  $datamensaje['whora_final'] = $dato_hora_fin[0];
		  $datamensaje['wemp_pmla'] = $wemp_pmla;
		  $datamensaje['wbasedatos'] = $wbasedatos;

		  //Al llegar esta variable con el valor 'ok', seran impresos los stickers, este valor toma el valor de la funcion js stickers_paquete, buscar variable wconfirmacion.
		  if($wconfirmacion == 'ok')
			{
			  $fp = fsockopen($wipimpresora,9100, $errno, $errstr, 30);
				
			  //Se recorre el arreglo $wdatos_pacientes para hacer la impresion de los datos.
			  foreach($wdatos_pacientes as $key => $values)
				 {

					   $whab = $values['habitacion'];
					   $whis = $values['historia'];
					   $wing = $values['ingreso'];
					   $wpac = $values['nombre'];
					   $wrondas = str_replace(' ','',$values['ronda']);
					   $whora_ini = explode(" ",$values['hora_inicial']);
					   $whora_fin = explode(" ",$values['hora_final']);
					   $whora_par_inicial = $whora_ini[0];
					   $whora_par_final = $whora_fin[0];
					   $wcoo = $values['cooo'];
					   $wusuario = $values['usuario'];
					   $wcco_aux = explode("-", $wcoo);
					   $wcco_aux = trim($wcco_aux[0]);

					   //Esta funcion verifica si el registro ya esta en la tabla movhos_000122, si no esta hara el registro de los datos del paciente.
					   $verifica_impresion = verificar_impr_stickers($wbasedatos, $values['historia'], $values['ingreso'], $whora_par_inicial, $whora_par_final, $wcoo, $wusuario);

					   //Busco los datos del paciente.
						$query_pac =  " SELECT Pactid, Pacced, Pacnac, Pacsex "
								   . "   FROM root_000036, root_000037"
								   . "  WHERE oriced  = pacced "
								   ."     AND oritid  = pactid "
								   ."     AND orihis  = '".$whis."'"
								   ."     AND oriing  = '".$wing."'";
						$res_pac = mysql_query($query_pac, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $query_pac . " - " . mysql_error());
						$row_pac = mysql_fetch_array($res_pac);

					   $wsex = $row_pac['Pacsex'];
					   $wtid = $row_pac['Pactid'];
					   $wced = $row_pac['Pacced'];
					   $wnac = $row_pac['Pacnac'];
					   $wdato_x = "";

					   //Calculo la edad
					   $wfnac = (integer) substr($wnac, 0, 4) * 365 + (integer) substr($wnac, 5, 2) * 30 + (integer) substr($wnac, 8, 2);
					   $wfhoy = (integer) date("Y") * 365 + (integer) date("m") * 30 + (integer) date("d");
					   $weda = (($wfhoy - $wfnac) / 365);
					   $weda = number_format($weda, 0, '.', ',');

						//Busco el responsable del paciente
						$queryeps =  " SELECT Ingnre "
								   . "   FROM ".$wbasedatos."_000016"
								   . "  WHERE Inghis = '" . $whis . "'"
								   . "    AND Inging = '" . $wing . "'";
						$reseps = mysql_query($queryeps, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						$roweps = mysql_fetch_array($reseps);
						$wresp = $roweps['Ingnre'];

						$wnum='1';
						$wtar='1';
						$paquete="";
						$paquete=$paquete."N".chr(13).chr(10);
						$paquete=$paquete."FK".chr(34)."CARGO".chr(34).chr(13).chr(10);
						$paquete=$paquete."FS".chr(34)."CARGO".chr(34).chr(13).chr(10);
						$paquete=$paquete."V00,7,L,".chr(34)."HISTORIA".chr(34).chr(13).chr(10);
						$paquete=$paquete."V01,38,L,".chr(34)."NOMBRE".chr(34).chr(13).chr(10);
						$paquete=$paquete."V02,2,L,".chr(34)."TARIFA".chr(34).chr(13).chr(10);
						$paquete=$paquete."V03,27,L,".chr(34)."EPS".chr(34).chr(13).chr(10);
						$paquete=$paquete."V04,15,L,".chr(34)."CC".chr(34).chr(13).chr(10);
						$paquete=$paquete."V05,3,L,".chr(34)."EDAD".chr(34).chr(13).chr(10);
						$paquete=$paquete."V06,1,L,".chr(34)."SEXO".chr(34).chr(13).chr(10);
						$paquete=$paquete."V07,20,L,".chr(34)."RONDAI".chr(34).chr(13).chr(10);
						$paquete=$paquete."V08,10,L,".chr(34)."RONDAF".chr(34).chr(13).chr(10);
						$paquete=$paquete."V09,10,L,".chr(34)."HAB".chr(34).chr(13).chr(10);
						$paquete=$paquete."Q265,24".chr(13).chr(10);
						$paquete=$paquete."q400".chr(13).chr(10);
						$paquete=$paquete."B115,10,0,1,2,6,70,N,V00".chr(13).chr(10);
						$paquete=$paquete."A98,90,0,3,1,1,N,".chr(34)."HISTORIA:".chr(34)."V00".chr(13).chr(10);
						$paquete=$paquete."A35,115,0,2,1,1,N,V01".chr(13).chr(10);
						$paquete=$paquete."A40,135,0,2,1,1,N,V04".chr(13).chr(10);
						$paquete=$paquete."A190,135,0,2,1,1,N,".chr(34)."EDAD:".chr(34)."V05".chr(13).chr(10);
						$paquete=$paquete."A285,135,0,2,1,1,N,".chr(34)."SEX:".chr(34)."V06".chr(13).chr(10);
						$paquete=$paquete."A73,155,0,2,1,1,N,".chr(34)."RONDAS:".chr(34)."V07".chr(13).chr(10);
						$paquete=$paquete."A55,180,0,2,1,1,N,V03".chr(13).chr(10);
						$paquete=$paquete."A155,210,0,4,1,1,N,".chr(34)."HAB:".chr(34)."V09".chr(13).chr(10);
						$paquete=$paquete."FE".chr(13).chr(10);
						$paquete=$paquete.".".chr(13).chr(10);
						$paquete=$paquete."FR".chr(34)."CARGO".chr(34).chr(13).chr(10);
						$paquete=$paquete."?".chr(13).chr(10);
						$paquete=$paquete.$whis.chr(13).chr(10);
						$paquete=$paquete.$wpac.chr(13).chr(10);
						$paquete=$paquete.$wtar.chr(13).chr(10);
						$paquete=$paquete.substr($wresp,0,27).chr(13).chr(10);
						$paquete=$paquete.$wtid.":".$wced.chr(13).chr(10);
						$paquete=$paquete.$weda.chr(13).chr(10);
						$paquete=$paquete.$wsex.chr(13).chr(10);
						$paquete=$paquete.$wrondas.chr(13).chr(10);
						$paquete=$paquete.$wdato_x.chr(13).chr(10);
						$paquete=$paquete.$whab.chr(13).chr(10);
						$paquete=$paquete."P".$wnum.chr(13).chr(10);
						$paquete=$paquete.".".chr(13).chr(10);
						$paquete."<br>";

						if(!$fp)
							echo "ERROR : "."$errstr ($errno)<br>\n";
						else
						{
							 fputs($fp,$paquete);
						}
				}

				fclose($fp);
				 
		$datamensaje['mensaje'] = 'Los stickers han sido impresos.';
		}
	}

	echo json_encode($datamensaje);
  }


  //Funcion que imprime un sticker ingresando una historia en la interfaz.
  function stick_historia($wbasedato,$whis, $wemp_pmla, $whora_par_actual, $wcco, $wccoo, $wcant)
     {


		global $conex;
		global $wipimpresora;

	    $datamensaje = array('mensaje'=>'', 'error'=>0, 'tabla'=>'');
		
		$ingreso_paciente 	= consultarUltimoIngresoHistoria( $conex, $whis, $wemp_pmla );
		$paciente 			= consultarUbicacionPaciente($conex, $wbasedato, $whis, $ingreso_paciente );

		$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $paciente->servicioActual );

	   //Busco los datos del paciente.
		$query_pac =  " SELECT habcco "
				   . "   FROM ".$tablaHabitaciones." "
				   . "  WHERE habhis  = '".$whis."'";
		$res_pac = mysql_query($query_pac, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $query_pac . " - " . mysql_error());
		$row_pac = mysql_fetch_array($res_pac);

	    $wcco1=trim($row_pac['habcco']);

	    $infoTipo = consultarInfoTipoArticulosInc( "N" );
		$wccoo1 = explode("-",$wccoo);

		/************************************************************************************************************
		 * Consulto las rondas a mostrar por si el cco destino seleccionado tiene dispensacion diferente
		 ************************************************************************************************************/
		if( $wccoo1[0] != '%' ){

			$aux = consultarHoraDispensacionPorCcoInc( $conex, $wbasedato, $wcco1 );

			if( $aux ){
				$infoTipo[ 'horaCorteDispensacion' ] = $aux/3600;
			}
		}

	    //Esto para sacar cuantas horas de dispensacion son
	    list( $totalRondas ) = explode( ":",$infoTipo[ 'horaCorteDispensacion' ] );


		for( $i = 0; $i < $totalRondas/2; $i++ ){
			if( $i == 0 ){
				$rondasAMostrar = gmdate( "H A", ($whora_par_actual+$i*2)*3600 );
			}
			else{
				$rondasAMostrar .= "-".gmdate( "H A", ($whora_par_actual+$i*2)*3600 );
			}
		}
	  $wrondas = str_replace(' ','',$rondasAMostrar);
      //$wipimpresora = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ImpresoraStickersDispen');

	  $wccorigen=explode("-",$worigen);
	  $wcentroc_origen = $wccorigen[1];

	  //Selecciono el paciente segun la historia
	    $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2 , pactid, pacced, Pacnac, Pacsex"
	        ."   FROM ".$tablaHabitaciones.", ".$wbasedato."_000018, root_000036, root_000037"
	        ."  WHERE habali != 'on' "            //Que no este para alistar
	        ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
	        ."    AND habcod  = ubihac "
	        ."    AND ubihis  = orihis "
	        ."    AND ubiing  = oriing "
	        ."    AND ubiald != 'on' "
	        ."    AND ubiptr != 'on' "
	        ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia
	        ."    AND oriced  = pacced "
		    ."    AND oritid  = pactid "
	        ."    AND habhis  = ubihis "
	        ."    AND habing  = ubiing "
		    ."    AND habhis  = '".$whis."'"
			."	  UNION"
		    ." SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, Pacnac,  Pacsex"
			."  FROM ".$tablaHabitaciones.", ".$wbasedato."_000018, root_000036, root_000037, ".$wbasedato."_000017"
			." WHERE habali != 'on' "		//Que no este para alistar
			."   AND habdis != 'on' "		//Que no este disponible, osea que este ocupada
			."   AND habcod = ubihac "
			."   AND ubihis = orihis "
			."   AND ubiing = oriing "
			."   AND ubiald != 'on' "
			."   AND oriori = '".$wemp_pmla."'" //Empresa Origen de la historia
			."   AND oriced = pacced "
			."   AND oritid = pactid "
			."   AND habhis = ubihis "
			."   AND habing = ubiing "
			."   AND ubiptr = 'on'"
			."   AND Eyrhis = habhis "
			."   AND Eyring = habing "
			."   AND habhis  = '".$whis."'"
			."  GROUP BY 1,2,3,4,5,6,7 "
			."  ORDER BY 1 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  if ($num > 0)
		{
		  $tabla = "<br><br>";
		  $tabla.= "<table>";
		  $tabla.= "<tr><th><font size=3>Datos impresos</font></th></tr>";
		  $tabla.= "</table>";
		  $tabla.= "<table>";
		  $tabla.= "<tr class=encabezadoTabla>";
		  $tabla.= "<th><font size=2>Habitacion</font></th>";
		  $tabla.= "<th><font size=2>Historia</font></th>";
		  $tabla.= "<th><font size=2>Paciente</font></th>";
		  $tabla.= "</tr>";

	  	}

	  if ($num > 0)
	     {

		  for($i=1;$i<=$num;$i++)
			 {
			   $row = mysql_fetch_array($res);

			   $whab = $row[0];
			   $whis = $row[1];
			   $wing = $row[2];
			   $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			   $wtid = $row[7];
			   $wced = $row[8];
			   $wnac = $row[9];
			   //Calculo la edad
			   $wfnac = (integer) substr($wnac, 0, 4) * 365 + (integer) substr($wnac, 5, 2) * 30 + (integer) substr($wnac, 8, 2);
			   $wfhoy = (integer) date("Y") * 365 + (integer) date("m") * 30 + (integer) date("d");
			   $weda = (($wfhoy - $wfnac) / 365);
               $weda = number_format($weda, 0, '.', ',');
			   $wsex = $row[10];

				$tabla.= "<td align=center><font size=3><b>".$whab."</b></font></td>";
				$tabla.= "<td align=center><font size=3><b>".$whis."</b></font></td>";
				$tabla.= "<td align=left  ><font size=3><b>".$wpac."</b></font></td>";

				//Busco el responsable del paciente
				$queryeps =  " SELECT Ingnre "
						   . "   FROM ".$wbasedato."_000016"
						   . "  WHERE Inghis = '" . $whis . "'"
						   . "    AND Inging = '" . $wing . "'";
				$reseps = mysql_query($queryeps, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$roweps = mysql_fetch_array($reseps);
				$wresp = $roweps['Ingnre'];

				$wnum='1';
				$wtar='1';
				$paquete="";
				$paquete=$paquete."N".chr(13).chr(10);
				$paquete=$paquete."FK".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."FS".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V00,7,L,".chr(34)."HISTORIA".chr(34).chr(13).chr(10);
				$paquete=$paquete."V01,38,L,".chr(34)."NOMBRE".chr(34).chr(13).chr(10);
				$paquete=$paquete."V02,2,L,".chr(34)."TARIFA".chr(34).chr(13).chr(10);
				$paquete=$paquete."V03,27,L,".chr(34)."EPS".chr(34).chr(13).chr(10);
				$paquete=$paquete."V04,15,L,".chr(34)."CC".chr(34).chr(13).chr(10);
				$paquete=$paquete."V05,3,L,".chr(34)."EDAD".chr(34).chr(13).chr(10);
				$paquete=$paquete."V06,1,L,".chr(34)."SEXO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V07,20,L,".chr(34)."RONDAI".chr(34).chr(13).chr(10);
				$paquete=$paquete."V08,10,L,".chr(34)."RONDAF".chr(34).chr(13).chr(10);
				$paquete=$paquete."V09,10,L,".chr(34)."HAB".chr(34).chr(13).chr(10);
				$paquete=$paquete."Q265,24".chr(13).chr(10);
				$paquete=$paquete."q400".chr(13).chr(10);
				$paquete=$paquete."ZT".chr(13).chr(10);  
				$paquete=$paquete."B115,10,0,1,2,6,70,N,V00".chr(13).chr(10);
				$paquete=$paquete."A98,90,0,3,1,1,N,".chr(34)."HISTORIA:".chr(34)."V00".chr(13).chr(10);
				$paquete=$paquete."A35,115,0,2,1,1,N,V01".chr(13).chr(10);
				$paquete=$paquete."A40,135,0,2,1,1,N,V04".chr(13).chr(10);
				$paquete=$paquete."A190,135,0,2,1,1,N,".chr(34)."EDA:".chr(34)."V05".chr(13).chr(10);
				$paquete=$paquete."A285,135,0,2,1,1,N,".chr(34)."SEX:".chr(34)."V06".chr(13).chr(10);
				$paquete=$paquete."A73,155,0,2,1,1,N,".chr(34)."RONDAS:".chr(34)."V07".chr(13).chr(10);
				$paquete=$paquete."A55,180,0,2,1,1,N,V03".chr(13).chr(10);
				$paquete=$paquete."A155,210,0,4,1,1,N,".chr(34)."HAB:".chr(34)."V09".chr(13).chr(10);
				$paquete=$paquete."FE".chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				$paquete=$paquete."FR".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."?".chr(13).chr(10);
				$paquete=$paquete.$whis.chr(13).chr(10);
				$paquete=$paquete.$wpac.chr(13).chr(10);
				$paquete=$paquete.$wtar.chr(13).chr(10);
				$paquete=$paquete.substr($wresp,0,27).chr(13).chr(10);
				$paquete=$paquete.$wtid.":".$wced.chr(13).chr(10);
				$paquete=$paquete.$weda.chr(13).chr(10);
				$paquete=$paquete.$wsex.chr(13).chr(10);
				$paquete=$paquete.$wrondas.chr(13).chr(10);
				$paquete=$paquete.$whora_final.chr(13).chr(10);
				$paquete=$paquete.$whab.chr(13).chr(10);
				$paquete=$paquete."P".$wnum.chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				$paquete."<br>";

// ---
			for($j=0;$j<$wcant;$j++)	
			{
				$fp = fsockopen($wipimpresora,9100, $errno, $errstr, 30);

				 if(!$fp)
				$tabla.= "ERROR : "."$errstr ($errno)<br>\n";
				else
				{
					 fputs($fp,$paquete);

				}

				fclose($fp);
			}	
// ---
		 }

		 $tabla.= "</table>";

		 $datamensaje['mensaje'] = 'El sticker para la historia '.$whis.' ha sido impreso';
		 $datamensaje['error'] = 0;
		 $datamensaje['tabla'] = $tabla;

		 }
	 else
		{
		$datamensaje['mensaje'] = 'La historia no existe o no esta activa.';
		$datamensaje['error'] = 1;

		}

	echo json_encode($datamensaje);
 }

if (!isset($consultaAjax)){
?>
<head>
  <title>STICKERS DISPENSACION</title>

  <style type="text/css">

    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}

    </style>

</head>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<script type="text/javascript">


	function soloNumeros(e){
		var key = window.Event ? e.which : e.keyCode;
		return ((key >= 48 && key <= 57) || key<= 8);
		// return (key >= 48 && key <= 57);
	}

    //Funcion que imprime un paquete de stickers, dependiendo de los datos en el arreglo wdatos_pacientes, se valida si ya estan impresos y vuelve a reenviar la informacion pero con
	//valor reimprimir en si
    function stickers_paquete(wbasedato,wemp_pmla,wdatos_pacientes, wccoo, whora_inicial, whora_final, wcconom)
    {
    	var wipimpresora = $("#wipimpresora").val();
		$.post("stickers_Dispensacion.php",
				{

                    consultaAjax:   	'stickers_paquete',
					wemp_pmla:      	wemp_pmla,
					wbasedatos:         wbasedato,
					wdatos_pacientes:   wdatos_pacientes,
					wccoo:				wccoo,
					whora_inicial:		whora_inicial,
					whora_final:		whora_final,
					reimpirmir:			'no',
					wcconom:			wcconom,
					wipimpresora:       wipimpresora


				}
				,function(data_json) {

					//Si entra aqui es porque los stickers ya estan impresos.
					if (data_json.error == 1)
					{

						var answer = confirm(data_json.mensaje);
                        if (answer){

							$.post("stickers_Dispensacion.php",
								{

									consultaAjax:   	'stickers_paquete',
									wemp_pmla:      	data_json.wemp_pmla,
									wbasedatos:         data_json.wbasedatos,
									wdatos_pacientes:   data_json.wdatos_paciente,
									wccoo:				data_json.wccoo,
									whora_inicial:		data_json.whora_inicial,
									whora_final:		data_json.whora_final,
									reimpirmir:			'si',
									wipimpresora:       wipimpresora

								}
								,function(data_json) {

									if (data_json.error == 1)
									{

										alert(data_json.mensaje);

									}
									else
									{

										alert(data_json.mensaje);

									}

								},
								"json"
							);
						}
					}
					else
					{
						if (data_json.error == 2)
							{

								var answer = confirm(data_json.mensaje);
								if (answer){

									$.post("stickers_Dispensacion.php",
										{

											consultaAjax:   	'stickers_paquete',
											wemp_pmla:      	data_json.wemp_pmla,
											wbasedatos:         data_json.wbasedatos,
											wdatos_pacientes:   data_json.wdatos_paciente,
											wccoo:				data_json.wccoo,
											whora_inicial:		data_json.whora_inicial,
											whora_final:		data_json.whora_final,
											wconfirmacion:		'ok',
											wipimpresora:       wipimpresora

										}
										,function(data_json) {

											if (data_json.error == 1)
											{

												alert(data_json.mensaje);

											}
											else
											{

												alert(data_json.mensaje);

											}

										},
										"json"
									);
								}
							}

					}

				},
				"json"
			);
	}

  //Esta funcion permite a impresion de un solo sticker, segun la historia digitada.
 function sticker_historia(wbasedato, wemp_pmla, whis)
 {

    var whis= document.getElementById("whistoria").value;
	var whora_par_actual = document.getElementById("whora_par_actual").value;
	var wccoo = document.getElementById("wccoo").value;
	var wipimpresora = $("#wipimpresora > option:selected").val();

	var wcant= document.getElementById("wcantidad").value;
	
	if(wcant=="0" || wcant=="")
	{
		wcant = 1;
	}
	// alert(wcant);
	if(whis == '')
		{
		alert('Debe ingresar una historia.');
		return;
		}

    $.post("stickers_Dispensacion.php",
            {
                consultaAjax:   	'stick_historia',
                wemp_pmla:      	wemp_pmla,
                wbasedato:          wbasedato,
                whis:       		whis,
                whora_par_actual:   whora_par_actual,
				wccoo: 				wccoo,
				wipimpresora  :     wipimpresora,
				wcant: 			    wcant
            }
            ,function(data_json) {

                if (data_json.error == 1)
                {
                    alert(data_json.mensaje);
                }
                else
                {
                  alert(data_json.mensaje);
				  $('#whistoria').val('');
				  $('#wcantidad').val('');
				  $("#historias").html(data_json.tabla);
                }

        },
        "json"
    );
}

function intercalar(idElemento, ronda )
{
	var rowspan = $( "#tdRonda"+ronda ).attr( "rowspan" );
	
	if( document.getElementById(idElemento).style.display=='')
	{
		document.getElementById(idElemento).style.display='none';
		$( "#tdRonda"+ronda ).attr( "rowspan", --rowspan );
	}
	else
	{
		 document.getElementById(idElemento).style.display='';
		 $( "#tdRonda"+ronda ).attr( "rowspan", ++rowspan );
	}
}


function enter()
	{
	 document.forms.separacionXRonda.submit();
	}

function cerrarVentana()
	 {
      window.close()
     }

	 
window.onload = function(){
	
	$( "#wccoo" ).change(function(){
		if( $( this ).val().substr( 0,4 ) == '1050' ){
			//Este es la opcion todos
			//Si se seleccionar 1050 esta opción no debe aparecer
			$( "#wcco option" ).eq(1)
				.css({display: "none" })
				.attr({disabled: true });
		}
		else{
			$( "#wcco option" ).eq(1)
				.css({display: "" })
				.attr({disabled: false });
		}
	});
	
	$( "#wccoo" ).change();
}
	 
</script>

<body>

<?php
 /*     * *******************************************************
     *              STICKERS DISPENSACION   					*
     * ******************************************************* */
//==================================================================================================================================
//PROGRAMA                   : entrega_turnos_secretaria.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : Marzo 14 de 2012
//FECHA ULTIMA ACTUALIZACION : Septiembre 27 de 2012

//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa que permite imprimir los stickers para los pacientes con historias e ingresos que tengan aplicacion de medicamentos                                                                              \\
//     ** Funcionamiento General:                                                                                                         \\
// Este programa inicialmente evalua los pacientes activos en el centro de costos seleccionado, luego analiza con la funcion query_articulos_stickers
// cuales de las historias e ingresos activos que tienen aplicacion de medicamentos, generando uno o varios sticker. Ademas es posible la impresion de
// stickers de forma individual ingresando un historia en el formulario inicial, al seleccionar el boton consultar se generara el sticker y se mostrara
// en la parte inferior la informacion del sticker impreso, teniendo en cuanta la hora inicial y la hora final del formulario.
// El programa valida si la ronda seleccionada ya fue impresa, generando un mensaje de alerta, si el usuario selecciona aceptar en la alerta
// el programa imprime de nuevo los stickers, en caso de cancelar el programa devolverá al usuario a la página prinicipal y detendrá la
// ejecución.
/*
 **   ACTUALIZACIONES **
 
    2018-09-13 Edwin MG: 			Se abre una sola conexión socket con conexión a la impresora para imprimir los stickers
    2018-04-09 Jessica Madrid: 		Se modifica la orientacion del sticker con la instruccion ZT para que los stickers de central de 
									mezclas y este sticker de paciente queden con la misma orientacion
	Octubre 23 de 2017 Edwin MG 	Se hacen cambios varios para mostrar el reporte:
									- Se crea funciones nuevas en movhos.inc.php(informacionPacienteM18 y query_todos_articulos_cco) para no usar repetitivamente 
									  las consultas de las funciones detalleArticulo y query_articulos_cco
									- Se usa la clase clRegleta el cual está en el include classRegleta para saber si un articulo para un paciente pertenece a una ronda
									- Se elimina la función detalleArticulo ya que hacía algo similar a query_articulos_cco pero por articulo
    2017-05-17 Jessica Madrid: Se agrega 1 al parametro wcant para que imprima por defecto un solo sticker si no se envía la cantidad.
    2017-05-15 Jessica Madrid: Se agrega la opcion de ingresar la cantidad de stickers a imprimir cuando se digita una historia.
    2017-02-13 Edwin MG : Para CM se agrega opción en el cco para que muestre todos los cco.
    2017-02-09 Arleyda Insignares : Se agrega condición con el campo Ccoemp en caso de que el Query utilice la tabla costosyp_000005
 
 	2016-06-13 camilo zapata: modificaciones necesarias para que se imprima desde una impresora seleccionada. buscar wipimpresora y realizar seguimiento si es necesario
	9 de Septiembre de 2015: Se agrega un union a la consulta de la funcion detalleArticulo para que muestre los articulos que hayan sido solicitados el dia anterior
							pero teniendo en cuenta la fecha actual, esto para que se mantenga la validacion de la ronda.
 	14 Noviembre de 2014: Se modifica el script para que excluya los pacientes que provienen de urgencias y que aún estan en proceso de traslado, buscar la variable $tabla_pacientes
	23 Octubre de 2013: Se agrega un mensaje de confirmacion de impresion cuando seleccionan el boton imprimir, ademas se corrige la hora final en el sticker para que imprima
						correctamente cuando son stickers de la media noche. Jonatan

	27 Septiembre de 2013: Se renueva el script para que funcione con los mismo datos del programa MedicamentosXServicioXronda.php, esto para que los datos que muestra
							este programa sean iguales en la impresion de stickers y estas historias sean impresas. Jonatan

	//Este comentario ya no aplica porque el script y esta funcion ya no se encuentra en este programa.
    24 Septiembre de 2013: Se pone entre comentarios el llamado a la función "mostrar_stickers_anteriores" porque con la actualización realizada a la función
                              "query_articulos_stickers" en include/movhos/movhos.inc.php, dicha función consulta los pendientes por aplicar en rondas anteriores.
                              El centro de costo que llega a la función "mostrar_stickers_anteriores" debe ser el cco destino y no el cco origen

    18 Septiembre de 2013: La validación que se suprimió el "16 Mayo 2012" se revierte porque en la impresión de stickers de servicio farmaceutico
                            dicen que se estan perdiendo stickers porque se imprimen algunos que no se necesitan, los stickers que no se necesitan
                            corresponden a los medicamentos que aún tienen saldo para el paciente. En este momento de la actualización se encuentra
                            que una habitación no salió en la impresión de sticker pero se queda a espera de que ocurra un caso puntual de este tipo
                            para tratar de corregirlo.

    Modificaciones 16 Mayo 2012:
    Se suprime la validacion de saldo en el piso para que imprima todos los stickers que tengan aplicacion en la ronda seleccionada.

    Modificaciones 03 abril 2012:
    Se valida si el medicamento tiene saldo en el piso para la historia e ingreso, si no tiene saldo se genera sticker.
    Ademas se muestran los pacientes que tuvieron ingresos en rondas anteriores.
*/

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

		                                      // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2018-04-09";   		          // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                          // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //


  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================

  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;
	 global $wfecha_actual;


	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);

	 $wfecha_actual=date("Y-m-d");

	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
	    }
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
		    else
		       $whora_par_actual=$whora_Actual;
	     }

	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	      {
		   if (($whora_par_actual-2) == "00")               //Abril 12 de 2011
		      $whora_par_anterior="24";
		     else
	            $whora_par_anterior = $whora_par_actual-2;
		  }

	  if (strlen($whora_par_anterior) == 1)
	     $whora_par_anterior="0".$whora_par_anterior;

	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual;
    }

  //Muestra los datos de los pacientes.
  function pintarPaciente($res, $num, $codigo, $medicamento)
     {
	  global $conex;
	  global $wbasedato;
	  global $wemp_pmla;
	  global $wfecha;

	  global $arPacientes;
	  global $arHabitaciones;


	  if ($num > 0)
	     {
		  $wclass="fila2";

		  echo "<table align=center>";
		  echo "<tr class=encabezadoTabla>";
		  echo "<th>Historia</th>";
		  echo "<th>Ingreso</th>";
		  echo "<th>Habitación</th>";
		  echo "<th>Paciente</th>";
		  echo "<th>Dosis</th>";
		  echo "<th>Condición</th>";
		  echo "<th>Acción</th>";
		  echo "</tr>";
		  for ($i=1; $i<=$num; $i++)
		     {
			  if ($wclass=="fila1")
			     $wclass="fila2";
				else
                  $wclass="fila1";

			  $row = mysql_fetch_array($res);


			   // Creo Array de pacientes para mostrar al terminar el reporte
			  $arPacientes[ $row[2] ]['Historia'] = $row[0];
			  $arPacientes[ $row[2] ]['Ingreso'] = $row[1];
			  $arPacientes[ $row[2] ]['Nombre'] = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			  $arPacientes[ $row[2] ]['Habitacion'] = $row[2];
			  $nom_medica= $codigo."-".$medicamento."|";
			  @$ya_existe=strpos($arPacientes[ $row[2] ]['Medicamento'], $nom_medica);
			  $ya_existe;
			  if($ya_existe!== false)
			  {
			  // No lo almaceno porque ya existe dentro del array
			  }
			  else
			{
			  @$arPacientes[ $row[2] ]['Medicamento'] = $arPacientes[ $row[2] ]['Medicamento'].$nom_medica;
			  @$arPacientes[ $row[2] ]['NumMed'] =$arPacientes[ $row[2] ]['NumMed']+1;
			}
			  $arHabitaciones[ $row[2] ] = $row[2];

			  echo "<tr class='".$wclass."'>";
			  echo "<td align=center>".$row[0]."</td>";
			  echo "<td align=center>".$row[1]."</td>";
			  echo "<td align=center>".$row[2]."</td>";
			  echo "<td>".$row[3]." ".$row[4]." ".$row[5]." ".$row[6]."</td>";
			  echo "<td>".$row[7]." ".$row[8]."</td>";

			  $wcond="";
			  if ($row[9] != "")
			     {
				  $q = " SELECT condes, contip "
					  ."   FROM ".$wbasedato."_000042 "
					  ."  WHERE concod = '".$row[9]."'";
				  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row1 = mysql_fetch_array($res1);

				  $wcond=$row1[0]." - ".$row1[1];
				 }
			  echo "<td><b>".$wcond."</b></td>";
			  echo "<td align=center><A href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$row[0]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>";
			  echo "</tr>";
			 }
		  echo "</table>";
		 }
	 }

	//Muestra el detalle de los articulos por aplicar.
	function detalleArticulo($wart, $wcco, $whora_par_actual, $wfecha, $wtim, $waprobado, $wronda, $wcondicion, $wAN, &$wmostrar, &$respac, &$numpac)
     {
	  global $conex;
	  global $wbasedato;
	  global $wemp_pmla;
	  $historiasTrasladoUrgencias = array();
	  $condicionHistoriasTraslados = "";

	  $ayer = strtotime ( '-1 day' , strtotime ( $wfecha ) ) ;
	  $ayer = date ( 'Y-m-j' , $ayer );


	  /**************************************************************************************************
	   * Parte del query que saca el filtro por rondas necesarias
	   **************************************************************************************************/
	  //--> centro de costos de urgencias
	  $q  = " select Ccocod from {$wbasedato}_000011 where ccourg = 'on' and Ccoest = 'on'";
	  $rs = mysql_query( $q, $conex );
	  while( $row = mysql_fetch_assoc( $rs ) ){
	      $ccourg = $row['Ccocod'];
	  }

	  $query = " SELECT concat( Ubihis,Ubiing ) as hising
	  			   FROM {$wbasedato}_000018
	  			  WHERE Ubisan = '{$ccourg}'
	  			    AND Ubiptr = 'on'
	  			    AND Ubiald = 'off'";
	  $rs = mysql_query($query);
	  while( $row = mysql_fetch_assoc( $rs ) ){
	      array_push( $historiasTrasladoUrgencias, "'".$row['hising']."'" );
	  }

	  if( count( $historiasTrasladoUrgencias ) > 0 ){
	  	$condicionHistoriasTraslados = " AND concat( habhis, habing ) NOT IN (".implode(",", $historiasTrasladoUrgencias).") ";
		//Nov 19 2014 - $tabla_pacientes = ", ".$wbasedato."_000018 mv18";
		$tabla_pacientes = "";
	  }

//	print_r();

	  $infoTipo = consultarInfoTipoArticulosInc( "N" );

	  if( $wcco != '%' ){

		$aux = consultarHoraDispensacionPorCcoInc( $conex, $wbasedato, $wcco );

		if( $aux ){
			$infoTipo[ 'horaCorteDispensacion' ] = $aux/3600;
		}
	  }

	  //Esto para sacar cuantas horas de dispensacion son
	  list( $totalRondas ) = explode( ":",$infoTipo[ 'horaCorteDispensacion' ] );
	  /**************************************************************************************************/

	  global $numtemp;
	  $numtemp++;
	  $temp = "temp".date( "Ymd" ).$numtemp;

	  $q = "CREATE TEMPORARY TABLE IF NOT EXISTS ".$temp;

	  for( $i = $whora_par_actual; $i < $totalRondas+$whora_par_actual; $i += 2 )
	   {
		  $fechaCompleta = date( "Y-m-d H:i:s", strtotime( "$wfecha 00:00:00" )+$i*3600 );

		  if( $i > $whora_par_actual ){
			$q .= "    UNION ALL ";
		  }

		  if ($wtim == "SF")
			 {
			  @$q .= " SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000054 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 F, root_000036, root_000037 $tabla_pacientes "
				  ."  WHERE A.fecha_data = '".$wfecha."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'SF'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				 // ."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced "
				  ."    AND oritid       = pactid "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
					   //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) ";
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }

				  $q = $q."  UNION ALL "
				  //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION que esten para la ronda indicada
				  //y que solo sean de dispensacion no de CM
				  ." SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000060 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 F, root_000036, root_000037 $tabla_pacientes "
				  ."  WHERE A.fecha_data = '".$wfecha."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'SF'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "        //No Enviar en off, osea que si se envia
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				  //."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced "
				  ."    AND oritid       = pactid "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
					  //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) ";
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }
					 
					 
				///////////////// Articulos de ayer con datos de hoy para que funcione a las 00  //////////////////////
				 $q.=  "  UNION ALL ";
				 $q.= " SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000054 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 F, root_000036, root_000037 $tabla_pacientes "
				  ."  WHERE A.fecha_data = '".$ayer."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'SF'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				 // ."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced " 
				  ."    AND oritid       = pactid "
				  ."	AND concat( Kadhis,kading ) NOT IN (SELECT concat( Karhis,Karing ) FROM ".$wbasedato."_000053 a, ".$wbasedato."_000020 b WHERE a.Fecha_data = '".$wfecha."' AND karhis = habhis AND karing = habing AND habcco = '".trim($wcco)."') "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
					  //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) ";
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }

				  $q = $q."  UNION ALL "
				  //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION que esten para la ronda indicada
				  //y que solo sean de dispensacion no de CM
				  ." SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000060 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 F, root_000036, root_000037 $tabla_pacientes "
				  ."  WHERE A.fecha_data = '".$ayer."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'SF'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "        //No Enviar en off, osea que si se envia
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				  //."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced "
				  ."    AND oritid       = pactid "
				  ."	AND concat( Kadhis,kading ) NOT IN (SELECT concat( Karhis,Karing ) FROM ".$wbasedato."_000053 a, ".$wbasedato."_000020 b WHERE a.Fecha_data = '".$wfecha."' AND karhis = habhis AND karing = habing AND habcco = '".trim($wcco)."') "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
					  //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) ";
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }
					 
				/////////////////////////
			 }
			else
			 {
			  $q .= " SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000054 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 D, root_000036, root_000037 $tabla_pacientes "
				  ."  WHERE A.fecha_data = '".$wfecha."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'CM'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				  //."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced "
				  ."    AND oritid       = pactid "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
					  //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) ";
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }

				  $q = $q."  UNION ALL "
				  //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION que esten para la ronda indicada
				  //y que solo sean de dispensacion no de CM
				  ." SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000060 B, ".$wbasedato."_000043 E, ".$wbasedato."_000020 D, root_000036, root_000037 "
				  ."  WHERE A.fecha_data = '".$wfecha."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'CM'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "        //No Enviar en off, osea que si se envia
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				  //."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced "
				  ."    AND oritid       = pactid "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
					  //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) "; 
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }
					 
				///////////////// Articulos de ayer con datos de hoy para que funcione a las 00  //////////////////////
				$q = $q."  UNION ALL ";
				$q .= " SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000054 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 D, root_000036, root_000037 $tabla_pacientes "
				  ."  WHERE A.fecha_data = '".$ayer."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'CM'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				  //."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced "
				  ."    AND oritid       = pactid "
				  ."	AND concat( Kadhis,kading ) NOT IN (SELECT concat( Karhis,Karing ) FROM ".$wbasedato."_000053 a, ".$wbasedato."_000020 b WHERE a.Fecha_data = '".$wfecha."' AND karhis = habhis AND karing = habing AND habcco = '".trim($wcco)."') "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
						 
					  //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) ";
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }

				  $q = $q."  UNION ALL "
				  //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION que esten para la ronda indicada
				  //y que solo sean de dispensacion no de CM
				  ." SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd, habcco, '".gmdate( "H", $i*3600 )."' kadron "
				  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000060 B, ".$wbasedato."_000043 E, ".$wbasedato."_000020 D, root_000036, root_000037 "
				  ."  WHERE A.fecha_data = '".$ayer."'"
				  ."    AND kadest       = 'on' "
				  ."    AND kadart       = '".$wart."'"
				  ."    AND kadori       = 'CM'"
				  ."    AND karhis       = kadhis "
				  ."    AND karing       = kading "
				  ."    AND kadsus      != 'on' "
				  ."    AND kadess      != 'on' "        //No Enviar en off, osea que si se envia
				  ."    AND A.fecha_data = kadfec "
				  ."    AND kadper       = percod "
				  ."    AND karcco       = '*' "
				  ."    AND karhis       = habhis "
				  ."    AND karing       = habing "
				  ."    AND habcco       LIKE '".trim($wcco)."' "
				  .$condicionHistoriasTraslados
				  //."    AND ubiptr       != 'on'"
				  ."    AND karhis       = orihis "
				  ."    AND karing       = oriing "
				  ."    AND oriori       = '".$wemp_pmla."'"
				  ."    AND oriced       = pacced "
				  ."    AND oritid       = pactid "
				  ."	AND concat( Kadhis,kading ) NOT IN (SELECT concat( Karhis,Karing ) FROM ".$wbasedato."_000053 a, ".$wbasedato."_000020 b WHERE a.Fecha_data = '".$wfecha."' AND karhis = habhis AND karing = habing AND habcco = '".trim($wcco)."') "
				  ."    AND kadare       = '".$waprobado."'"
				  ."    AND kadcnd       = '".$wcondicion."' ";

				  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
					 {
					  //Con las sigtes lineas se asegura que solo traiga
					  $q = $q."   AND CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 ."                                                     FROM ".$wbasedato."_000004 "
							 ."                                                    WHERE spahis = kadhis "
							 ."                                                      AND spaing = kading "
							 ."                                                      AND spaart = kadart "
							 ."                                                      AND (spauen-spausa) > 0) ";
					  // //Con las sigtes lineas se asegura que solo traiga
					  // $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart "
							 // ."                                                      AND (spauen-spausa) = 0) "
							 // ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
							 // ."                                                     FROM ".$wbasedato."_000004 "
							 // ."                                                    WHERE spahis = kadhis "
							 // ."                                                      AND spaing = kading "
							 // ."                                                      AND spaart = kadart)) ";
					 }
					 else{
						 $q = $q."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),'$fechaCompleta'),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
								."    AND ( kadron = '' OR UNIX_TIMESTAMP( CONCAT( kadfro,' ', kadron ) ) < UNIX_TIMESTAMP( '$fechaCompleta' ) )" //cambio linea para mostrar solo los articulos mayores a la ronda dispensada(."    AND kadron       = '".$wronda."'")
								."    AND UNIX_TIMESTAMP( CONCAT( kadfin,' ', kadhin ) ) <= UNIX_TIMESTAMP( '$fechaCompleta' )";
					 }
				
				////////////////////
			 }
		}
	  $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query 1: ".$q." - ".mysql_error());

	  $q = "  SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, SUM( kadcfr ), kadufr, kadcnd, habcco, kadron "
		  ."    FROM $temp, ".$wbasedato."_000011 "
		  ."   WHERE ccocod = habcco "
		  ."     AND ccocpx = 'on' "
		  ."GROUP BY 1,2,3,4,5,6,7,9,10,11 ";

	  $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  $numpac = mysql_num_rows($respac);
	  if ($numpac > 0)
	     $wmostrar="on";

		$q = "DROP TABLE $temp";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 }


  function elegir_centro_de_costo()
     {
	  global $user;
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wcco;
	  global $wemp_pmla;
	  global $ccoCentral;
	  global $wipimpresora;

	  global $whora_par_actual;
	  global $whora_par_sigte;
	  global $whora_par_anterior;


	  hora_par();
	  //Seleccionar RONDA
	  echo "<center><table>";
      echo "<tr class=fila1><td align=center><font size=4>Seleccione la Ronda:</font> </td></tr>";
	  echo "</table>";

	  echo "<center><table>";
	  echo "<tr><td align=rigth><select name='whora_par_actual' id='whora_par_actual'  size='1' style='font-family:Verdana, Arial, Helvetica, sans-serif; '>";
	  echo "<option selected id='dato_ronda'>".$whora_par_actual."</option>";
	  echo "<option value=2>2</option>";
	  echo "<option value=4>4</option>";
	  echo "<option value=6>6</option>";
	  echo "<option value=8>8</option>";
	  echo "<option value=10>10</option>";
	  echo "<option value=12>12</option>";
	  echo "<option value=14>14</option>";
	  echo "<option value=16>16</option>";
	  echo "<option value=18>18</option>";
	  echo "<option value=20>20</option>";
	  echo "<option value=22>22</option>";
	  echo "<option value=00>00</option>";
	  echo "</select></td></tr>";
	  echo "</table>";

	  echo "<br>";
	  //=======================================================================================================
	  //Seleccionar CENTRO DE COSTO Origen

	  echo "<table align='center' border=0>";
	  echo "<tr>";
	  echo "<td class='fila1'><font size=4>Centro de costos origen:</font></td><tr>";
	  //Buscando los centro de costos de traslado (SF y CM)
	  //Estos son los de origen
	  $sql = "SELECT  *
		    	FROM  ".$wbasedato."_000011
			   WHERE  ccotra = 'on'
				 AND  ccofac = 'on'";
	  $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	  echo "<tr><td class='fila2'>";
	  echo "<select id='wccoo' name='wccoo'>";
	  
	  for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
	    {
			echo "<option value='".$rows['Ccocod']."-".$rows['Cconom']."'>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
		}

	  echo "</select>";
      echo "</td>";
	  echo "</tr>";
	  echo "</table>";
	  echo "<br>";
	  //=======================================================================================================

	  //=======================================================================================================
	  //Seleccionar CENTRO DE COSTO Destino
	    // Centros de costos destino
	    
      if ($wtabcco == 'costosyp_000005'){
	      
	      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
	          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
	          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
	          ."    AND ".$wbasedato."_000011.ccohos = 'on' "
	          ."    AND ".$wtabcco.".ccoemp = '".$wemp_pmla."' "
			  ." ORDER BY ".$wtabcco.".ccocod";
	  }
	  else{  

	      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
	          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
	          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
	          ."    AND ".$wbasedato."_000011.ccohos = 'on'"
			  ."ORDER BY ".$wtabcco.".ccocod";
      }

      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
	  echo "<table>";
	  echo "<tr><td class='fila1' align=center><font size=4>Centro de costos destino:</font></td></tr>";

	  echo "<tr><td align=center><select name='wcco' id='wcco' size='1' style=' font-family:Verdana, Arial, Helvetica, sans-serif;' onchange='enter()'>";
	  echo "<option></option>";
	  echo "<option value='%-Todos'>%-Todos</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]." - ".$row[1]."'>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";


      $q = " SELECT Impcod, Impnom, Impnip
			   FROM root_000053
			  WHERE Impcco = '$ccoCentral'
			    AND Impest='on'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
      if( !isset($wipimpresora) )
      	$wipimpresora = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ImpresoraStickersDispen');
      echo "<br><table>";
	  echo "<tr><td class='fila1' align=center><font size=4>IMPRESORA:</font></td></tr>";

	  echo "<tr><td align=center><select name='wipimpresora' id='wipimpresora' size='1' style=' font-family:Verdana, Arial, Helvetica, sans-serif;'>";
	  ( !isset($wipimpresora) ) ? $selected = "" : $selected = " selected ";
	  echo "<option {$selected} value='{$wipimpresora}'>Defecto: $wipimpresora</option>";
	  for ($i=1;$i<=$num;$i++)
	     {

	      $row1 = mysql_fetch_array($res);
	      ( $wipimpresora == $row1['Impnip'] ) ? $selected = "" : $selected = " selected ";
	      $row1['Impcod'] . '-' . $row1['Impnom'] . '-' . $row1['Impnip'];
	      echo "<option value='{$row1['Impnip']}'>".$row1[0]." - ".$row1[1]." - ".$row1['Impnip']."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";


		echo "<center><table>";
		echo "<br><br>";                         
		echo "<tr class=encabezadotabla><td colspan=2 align=center>Imprimir Sticker individual</td></tr>";
		echo "<tr class=fila1><td align=center><font size=4>Historia :</font> </td><td><input type=text id=whistoria onkeypress='return event.keyCode!=13'></td></tr>";
		echo "<tr class=fila1><td align=center><font size=4>Cantidad :</font> </td><td><input type=text id=wcantidad onKeyPress='return soloNumeros(event);'></td></tr>";
		echo "<tr class=fila1><td align=center colspan=3><input type=button value=Imprimir onclick='sticker_historia(\"$wbasedato\", \"$wemp_pmla\")'></td></tr>";
		echo "</table>";
		echo "<center><div id=historias></div>";
	  //=======================================================================================================
	 }
  //===========================================================================================================================================
  //*******************************************************************************************************************************************



  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='separacionXRonda' action='' method=post>";

  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  
	$unixFechaActual = strtotime( $wfecha." 00:00:00" );
   

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

  encabezado("Sticker dispensacion",$wactualiz, "clinica");

  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wtabcco   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
  $wcenmez   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
  
  $query     = " SELECT ccocod
				   FROM {$wbasedato}_000011
				  WHERE `Ccofac` = 'on'
					AND `Ccotra` = 'on'
					AND `Ccoima` = 'on'";
	$rs = mysql_query( $query, $conex );
	$row = mysql_fetch_assoc($rs);
	$ccoCentral = $row['ccocod'];


  if (!isset($wcco) or !isset($wccoo))
     {
      elegir_centro_de_costo();
     }
	else
       {
	    echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
		echo "<input type='HIDDEN' name='wccoo' VALUE='".$wccoo."'>";
		echo "<input type='HIDDEN' name='whora_par_actual' VALUE='".$whora_par_actual."'>";
		echo "<input type='hidden' name='wipimpresora' id='wipimpresora' value='$wipimpresora'>";

		$arPacientes = Array();	//Array que contiene los pacientes que se muestran
		$arHabitaciones = Array();	//Array que contiene los pacientes que se muestran

	    $wcco1=explode("-",$wcco);
	    if (trim($wcco1[0])=="%")
	       $wcco1[0]="%";
	      else
	         $wcco1[0]=trim($wcco1[0]);

		$wccoo1=explode("-",$wccoo);
	    $wccoo1[0]=trim($wccoo1[0]);


		if ($whora_par_actual < 13)
           $whora_par_act=$whora_par_actual." AM";
		  else
             $whora_par_act=$whora_par_actual." PM";

		echo '<table align=center>';
		echo "<tr class=seccion1>";
	    echo "<th align=center colspan=2><font size=3>Centro de Costos Origen: </font><b><font size=4>".$wccoo."</b></font></th>";
		echo "</tr>";
	    echo "<tr class=seccion1>";
	    echo "<th align=center colspan=2><font size=3>Centro de Costos Destino: </font><b><font size=4>".$wcco."</b></font></th>";
		echo "</tr><tr class=seccion1>";
		
		
		$gruposControl = consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposControl" );
		$gruposControl = explode( ",", $gruposControl );
		
		/**************************************************************************
		 * Consulta de articulos generico que no se deben mostrar
		 **************************************************************************/
		 
		$sql = "SELECT Artcod
				  FROM ".$wcenmez."_000001 a, ".$wcenmez."_000002 b, ".$wbasedato."_000068 c
			     WHERE arttip = tipcod
			       AND tiptpr = arktip
				   AND arkcod = artcod 
				   AND tiptpr = 'LQ'
				   ";
		
		$resGen = mysql_query( $sql, $conex ) or die ( "Error: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error() );
		
		$arrGenLQIC = Array();
		while( $rows = mysql_fetch_array( $resGen) ){
			$arrGenLQIC[] = $rows['Artcod'];
		}
		/**************************************************************************/

		/**************************************************************************************************
	     * Marzo 7 de 2012
	     **************************************************************************************************/
	    $infoTipo = consultarInfoTipoArticulosInc( "N" );


		/************************************************************************************************************
		 * Consulto las rondas a mostrar por si el cco destino seleccionado tiene dispensacion diferente
		 ************************************************************************************************************/
		if( $wccoo1[0] != '%' ){

			$aux = consultarHoraDispensacionPorCcoInc( $conex, $wbasedato, $wcco1[0] );

			if( $aux ){
				$infoTipo[ 'horaCorteDispensacion' ] = $aux/3600;
			}
		}

	    //Esto para sacar cuantas horas de dispensacion son
	    list( $totalRondas ) = explode( ":",$infoTipo[ 'horaCorteDispensacion' ] );

		for( $i = 0; $i < $totalRondas/2; $i++ ){
			if( $i == 0 ){
				$rondasAMostrar = gmdate( "H A", ($whora_par_act+$i*2)*3600 );
				$whora_inicial = gmdate( "H A", ($whora_par_act+$i*2)*3600 );
			}
			else{
				$rondasAMostrar .= "-".gmdate( "H A", ($whora_par_act+$i*2)*3600 );
				$whora_final = gmdate( "H A", ($whora_par_act+$i*2)*3600 );
			}
		}


		echo "<th align=center><font size=3>Ronda: </font><b><font size=4>".$rondasAMostrar."</b></font></th>";
		echo "<th align=center><font size=3>Fecha y Hora: <b>".date("Y-m-d H:i:s")."</b></font></th>";
	    echo "</tr>";
	    echo "</table>";
        echo "<br>";

		//Valida si la ronda es mayor o igual a 8 para que al imprimir las rondas en el stickers si ocupe el espacio
		if( $totalRondas >= 8 )
		{
			$rondasAMostrar = $whora_inicial."....".$whora_final;
		}

	    // query_articulos_cco($wfecha, &$res, $wcco1[0], $wccoo1[0], &$wccotim);
		$res = query_todos_articulos_cco( $conex, $wbasedato, $wcenmez, $wfecha, $wcco1[0], $wccoo1[0] );
		$num = mysql_num_rows($res);
		
		$datos = array();
		
		
		
		
		
		/**************************************************************************************************
		 * Parte del query que saca el filtro por rondas necesarias
		 **************************************************************************************************/
		//--> centro de costos de urgencias
		$q  = " select Ccocod from {$wbasedato}_000011 where ccourg = 'on' and Ccoest = 'on'";
		$rs = mysql_query( $q, $conex );
		while( $row = mysql_fetch_assoc( $rs ) ){
		  $ccourg = $row['Ccocod'];
		}

		$historiasTrasladoUrgencias = array();
		$query = " SELECT concat( Ubihis,Ubiing ) as hising
				   FROM {$wbasedato}_000018
				  WHERE Ubisan = '{$ccourg}'
					AND Ubiptr = 'on'
					AND Ubiald = 'off'";
		$rs = mysql_query($query);
		while( $row = mysql_fetch_assoc( $rs ) ){
			array_push( $historiasTrasladoUrgencias, $row['hising'] );
		}
		
		
		
		
		$infoTipo = consultarInfoTipoArticulosInc( "N" );

		if( $wcco != '%' ){
			$aux = consultarHoraDispensacionPorCcoInc( $conex, $wbasedato, $wcco1[0] );

			if( $aux ){
				$infoTipo[ 'horaCorteDispensacion' ] = $aux/3600;
			}
		}
		
		
		
		$j=1;
		$wclass = "fila2";
		
		for( $i=1; $i<=$num; $i++ )   //Recorre cada uno de los medicamentos
		{
		    $row = mysql_fetch_array($res);
			
			if( in_array( $row['kadhis'].$row['kading'], $historiasTrasladoUrgencias ) ){
				continue;
			}

			//Si es articulo genercio de infusion continua o liquido endovenoso no se muestra
			if( !in_array( $row[0], $arrGenLQIC ) ){
				
				$wultRonda  = $row[9];    //Ultima ronda grabada
				$wcondicion = $row[10];   //Condicion de administracion
				$wAN	  	= $row['Contip'] == 'AN' ? 'on': 'off';

				//Esto para sacar cuantas horas de dispensacion son
				list( $totalRondas ) = explode( ":",$infoTipo[ 'horaCorteDispensacion' ] );
				/**************************************************************************************************/

				$unixInicioDispensacion = $unixFechaActual+$whora_par_actual*3600;
				$unixFinDispensacion = $unixFechaActual+($totalRondas+$whora_par_actual)*3600;
				for( $j = $unixInicioDispensacion; $j < $unixFinDispensacion; $j += 2*3600 )
				{
					$fechaRonda = date( "Y-m-d", $j );
					$horaRonda  = date( "H:i:s", $j );
					$perteneceRonda = clRegleta::perteneceRondaADispensarPorArray( $conex, $wbasedato, $fechaRonda, $horaRonda, $row );
					
					if( $perteneceRonda ){
						
						if( !isset( $articulos[ $row['kadart']."-".$row['kadare'] ] ) ){
							
							$wctrl=explode("-",$row[12]);
							$articulos[ $row['kadart']."-".$row['kadare'] ] = array(
								'ubicacion' 		=> trim( $row[5] ),
								'codigoArticulo' 	=> $row[0],
								'nombreArticulo' 	=> $row[1],
								'aprobado' 			=> $row[7],
								'cantidadDosis' 	=> 0,	//$row[2],
								'fraccionDosis' 	=> $row[6],
								'cantidadUnidades' 	=> 0,
								'presentacion' 		=> $row[4],
								'confirmado' 		=> $row[8],
								'noPos' 			=> ( strtoupper( trim($row[11]) ) == "N" ) ? "Si" : "",
								// 'control'			=> ( strtoupper(trim($wctrl[0])) == "CTR" ) ? "Si" : "",
								'control'			=> in_array( strtoupper(trim($wctrl[0]) ), $gruposControl ) ? "Si" : "",
								'pacientes' 		=> array(),
								'rondaMenor' 		=> $j,
							);
						}
						
						$wcond = "";
						if( $row['kadcnd'] != "" ){
							if( $wAN == 'on' ){
								$wcond = $row['condes']." - AN";
							}
						}
						
						$rowsPac = informacionPacienteM18( $conex, $wbasedato, $wemp_pmla, $row['kadhis'], $row['kading'] );
						
						$habitacion = $rowsPac['habitacionActual'];
						$historia 	= $rowsPac['historia'];
						$ingreso 	= $rowsPac['ingreso'];
						$nombre 	= $rowsPac['primerNombre']." ".$rowsPac['segundoNombre']." ".$rowsPac['primerApellido']." ".$rowsPac['segundoApellido'];
						$rondaMenor = $articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'];
						
						$articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'] = $j < $rondaMenor ? $j : $rondaMenor;
						$articulos[ $row['kadart']."-".$row['kadare'] ]['cantidadDosis'] += $row[2];
						$articulos[ $row['kadart']."-".$row['kadare'] ]['cantidadUnidades'] += ceil( $row['kadcfr']/$row['kadcma'] );
						
						if( !isset( $articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia] ) ){
						
							$articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia] = array(
								'ronda' 		=> date( "H" ,$articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'] ),
								'historia' 		=> $historia,
								'ingreso' 		=> $ingreso,
								'habitacion' 	=> $habitacion,
								'nombre' 		=> $nombre,
								'dosis' 		=> $row['kadcfr']." ".$row['kadufr'],
								'dosisFraccion' => $row['kadcfr'],
								'condicion' 	=> $wcond,
								'accion' 		=> "'../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$rowsPac[0]."&wfecha=".$wfecha."'",
								'fecharonda' 	=> date( "Y-m-d" ,$articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'] ),
							);
						}
						else{
							$dFrac = $articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia]['dosisFraccion'] += $row['kadcfr'];
							$articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia]['dosis'] = $dFrac." ".$row['kadufr'];
						}
						
						
						
						// Creo Array de pacientes para mostrar al terminar el reporte
						$arPacientes[ $habitacion ]['Historia'] 	= $historia;
						$arPacientes[ $habitacion ]['Ingreso'] 		= $ingreso;
						$arPacientes[ $habitacion ]['Nombre'] 		= $nombre;
						$arPacientes[ $habitacion ]['Habitacion'] 	= $habitacion;
						$nom_medica= $row[0]."-".$row[1]."|";
						@$ya_existe=strpos($arPacientes[ $habitacion ]['Medicamento'], $nom_medica);
						if( $ya_existe === false )
						{
							@$arPacientes[ $habitacion ]['Medicamento'] = $arPacientes[ $habitacion ]['Medicamento'].$nom_medica;
							@$arPacientes[ $habitacion ]['ronda'] .= date( "H", $j )."|";
							@$arPacientes[ $habitacion ]['NumMed']		= $arPacientes[ $habitacion ]['NumMed']+1;
						}
						$arHabitaciones[ $habitacion ] = $habitacion;
						
						
						// var_dump( $articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'] );
					}
					
					//Si es a necesidad no se verifica la ronda siguiente por que ya pertence a esa ronda
					if( $perteneceRonda && $wAN == 'on' ){
						break;
					}
				}
			}
		}

		$datos = array();
		if( is_array($articulos) ){
			foreach( $articulos as $key => $articulo ){
				$datos[ date( "H", $articulo['rondaMenor'] ) ][] = $articulo;
			}
		}
		
		pintarAritculos( $datos );


		/////////////////////////////////////////////////////////////////////////////////////////////
		//MUESTRO TODOS LOS PACIENTES DE LA RONDA ESPECIFICADA
		/////////////////////////////////////////////////////////////////////////////////////////////
		// query_pacientes_cco($wfecha, &$res, $wcco1[0], $wccoo1[0], &$wccotim);
		// $num = mysql_num_rows($res);

		$num = count( $arPacientes );

		echo "<br><br>";
		echo "<center><table>";
		echo "<tr class=fila1>";
		echo "<th colspan=6><font size=4>PACIENTES CON MEDICAMENTOS EN ESTA RONDA</font></th>";
		echo "</tr>";
		echo "<tr class=encabezadoTabla>";
		echo "<th><font size=4>Habitacion</font></th>";
		echo "<th><font size=4>Historia</font></th>";
		echo "<th><font size=4>Ingreso</font></th>";
		echo "<th colspan=2><font size=4>Paciente</font></th>";
		echo "<th><font size=3>Medicamentos<br>Pendientes</font></th>";
		echo "</tr>";

		$j=1;
		$wclass="fila2";

		sort( $arHabitaciones );

		$array_datos = array();	 //Se declara este array para que los datos del arreglo $arHabitaciones, sean asociados.

		foreach( $arHabitaciones as $keyHabitaciones => $valueHabitaciones ){

			if ($wclass=="fila1")
			   $wclass="fila2";
	          else
	             $wclass="fila1";

            $q = " SELECT COUNT(*) "
			    ."   FROM ".$wbasedato."_000018 "
				."  WHERE ubihis = '".$arPacientes[ $valueHabitaciones ]['Historia']."' "
				."    AND ubiing = '".$arPacientes[ $valueHabitaciones ]['Ingreso']."'"
				."    AND ubialp = 'on' ";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);
			if ($row1[0] > 0)
               $wclass = "fondoAmarillo";

			$NumMedi=$arPacientes[ $valueHabitaciones ]['NumMed'];
			$medicam = explode("|",$arPacientes[ $valueHabitaciones ]['Medicamento']);

			if ($NumMedi>1)
			{
				echo "<tr class=".$wclass.">";
				echo "<td align=center rowspan='".$NumMedi."' >".$arPacientes[ $valueHabitaciones ]['Habitacion']."</td>"; 	    //Habitacion
				echo "<td align=center rowspan='".$NumMedi."'>".$arPacientes[ $valueHabitaciones ]['Historia']."</td>";  		//Historia
				echo "<td align=center rowspan='".$NumMedi."'>".$arPacientes[ $valueHabitaciones ]['Ingreso']."</td>";  		//Ingreso
				echo "<td align=left rowspan='".$NumMedi."'>".$arPacientes[ $valueHabitaciones ]['Nombre']."</td>";  			//Paciente
				if ($row1[0] > 0)
					echo "<td align=left rowspan='".$NumMedi."'><blink id=blink>Paciente de Alta</blink></td>";  			//Paciente
				else
					  echo "<td rowspan='".$NumMedi."'> </td>";
				echo "</td>";
				echo "<td align=left>".$medicam[0]."</td></tr>";      //Medicamentos Pendientes
					for($i=1; $i<$NumMedi; $i++)
					{
						echo "<tr class=".$wclass."><td align=left>".$medicam[$i]."</td></tr>"; 	//Medicamentos Pendientes
					}
				echo "</tr>";
			}

			else
			{
				echo "<tr class=".$wclass.">";
				echo "<td align=center>".$arPacientes[ $valueHabitaciones ]['Habitacion']."</td>"; 	    //Habitacion
				echo "<td align=center>".$arPacientes[ $valueHabitaciones ]['Historia']."</td>";  		//Historia
				echo "<td align=center>".$arPacientes[ $valueHabitaciones ]['Ingreso']."</td>";  		//Ingreso
				echo "<td align=left>".$arPacientes[ $valueHabitaciones ]['Nombre']."</td>";  			//Paciente
				if ($row1[0] > 0)
					echo "<td align=left><blink id=blink>Paciente de Alta</blink></td>";  			//Paciente
				   else
					  echo "<td> </td>";
				echo "</td>";
					for($i=0; $i<$NumMedi; $i++)
						echo "<td align=left>".$medicam[$i]."</td>";      //Medicamentos Pendientes
				echo "<tr>";
			}

			//Se crea ese nuevo array con los datos de los pacientes que tienen aplicaciones para la ronda seleccionada.
			if(!array_key_exists($arPacientes[ $valueHabitaciones ]['Habitacion'], $array_datos))
				{
				$array_datos[$arPacientes[ $valueHabitaciones ]['Habitacion']] = array('habitacion'=>$arPacientes[ $valueHabitaciones ]['Habitacion'],'historia'=>$arPacientes[ $valueHabitaciones ]['Historia'], 'ingreso'=>$arPacientes[ $valueHabitaciones ]['Ingreso'],'ronda'=>$rondasAMostrar, 'nombre'=>$arPacientes[ $valueHabitaciones ]['Nombre'], 'hora_inicial'=>$whora_inicial,'hora_final'=>$whora_final,
							'cooo'=>$wcco, 'usuario'=>$user);
				}

		}

		echo "</table>";

		/////////////////////////////////////////////////////////////////////////////////////////////
		$wdatos_pacientes = base64_encode(serialize($array_datos));
		echo "<br>";
		echo "<table>";
		echo "<tr><td><input type=button value=Imprimir onclick='stickers_paquete(\"".$wbasedato."\", \"".$wemp_pmla."\", \"".$wdatos_pacientes."\", \"".$wcco1[0]."\", \"".$whora_inicial."\", \"".$whora_final."\", \"".$wcco."\")'></td></tr>";
		echo "</table>";
		echo "<br><br>";
		echo "<table>";
		echo "<tr><td><A HREF='stickers_Dispensacion.php?wemp_pmla=".$wemp_pmla."&user=".$user."' class=tipo4V>Retornar</A></td></tr>";
		echo "</table>";

        echo "<meta http-equiv='refresh' content='120;url=stickers_Dispensacion.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wcco=".$wcco."&wccoo=".$wccoo."&whora_par_actual=".$whora_par_actual."'>";
	   }

  echo "<br><br><br>";
  echo "<table>";
  echo "<tr><td align=center colspan=4><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
  echo "</body>";

	}
 } // if de register

 if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {
				case 'stick_historia':
					{
					echo stick_historia($wbasedato,$whis, $wemp_pmla, $whora_par_actual, $wcco, $wccoo, $wcant = 1);
					}
				break;

				case 'stickers_paquete':
					{
					echo stickers_paquete($wemp_pmla,$wbasedatos, $wdatos_pacientes, $wccoo, $whora_inicial, $whora_final, $reimpirmir, $wconfirmacion, $wcconom);
					}
				break;

				default : break;
                }
            return;
            }

?>
