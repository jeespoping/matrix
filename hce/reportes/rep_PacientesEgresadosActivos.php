<?php
include_once("conex.php");
// if(!isset($accion))
// {
//     echo '<!DOCTYPE html>';
// }
/***************************************************
 PROGRAMA                   : rep_PacientesEgresadosActivos.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 22 de octubre de 2012

 DESCRIPCION:
 Muestra los pacientes que ingresan o egresan para un servicio seleccionado o todos.

 */$wactualiz = "2022-02-02";
/**

 CAMBIOS:
 		2018-05-24: camilo zapata: se adicionan en la url que dirige al programa de egreso, para llevar como fecha de egreso, la fecha de alta definitva
 		2017-05-10: * camilo zapata: intermitencia en opción de cambio de documentos, y restricción de dicha opción por centro de costos para que solo lo usen los usuarios de registros
 		2017-05-09: * Camilo Zapata: se adiciona la opción y el funcionamiento necesario para la gestión de las solicitudes del cambio de documento desde admisiones.
        2017-02-24: * (Arleyda Insignares C.) - Se agrega opción para visualizar los pacientes pendientes y una columna con un check para dar de Alta. Este proceso crea un registro en la tabla movhos_000033 y actualiza  movhos_000018 campo 'ubiald'.
 		2017-01-30: * El filtro por centro de costo realizado el 2016-12-02 se elimina para que sin importar la ayuda se puedan ver los funcionarios que realizaron admisión
	 					Un ejemplo específico es Imaginología para quien se solicito poder ver todos los funcionarios responsables de admisión, solicitud realizada en reunión
	 					de facturación inteligente el 25-Enero-2016 (Con Sandra Agudelo, Laura Velez).
 					* Se reorganiza todo el código php al inicio y el html al final del script.
 		2016-12-02: camilo zapata cambio de calendario, adicion de nombre de quien hizo el ingreso, y limite de cco que se pueden elegir dentro de la lista segun el usuario.
		2016-06-03: (Camilo zapata)  Se modifica para que pinte el fondo de distinto color segun la unidad que realiza el egreso.
		2013-12-09: (Frederick Aguirre)  Se actualiza la URL para ver la historia clinica electrónica
		2013-02-21: (Frederick Aguirre)  Para la consulta de egresos se agrega la condicion si es 1179 que no cuente los ambulatorios (ubihac == '')
*////////////////////

if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($accion) && !array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}



include_once("root/comun.php");

$conex               = obtenerConexionBD("matrix");
$wbasedato           = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wcliame             = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$whce                = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$ccoRegistrosMedicos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoRegistrosMedicos');
$pos                 = strpos($user,"-");
$user                = $_SESSION['user'];
$wuser               = explode("-",$user);

if (is_null($selectsede)){
    $selectsede = consultarsedeFiltro();
}


if (isset($_POST["accion"]) && $_POST["accion"] == "GrabarEgreso"){

		$wfecha    = date("Y-m-d");
		$whora     = (string)date("H:i:s");

        list($whistoria, $wingreso, $wccocod, $wfila) = explode('-', $wdatos);

	    $q = " SELECT COUNT(*) "
			."   FROM ".$wbasedato."_000032 "
			."  WHERE Historia_clinica = '".$whistoria."'"
			."    AND Num_ingreso      = '".$wingreso."'"
			."    AND Servicio         = '".$wccocod."'";

		$err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());
		$row = mysql_fetch_array($err);

		$wingser = $row[0] + 1; //Sumo un ingreso a lo que traigo el query

		// * * * * * Aca grabo el movimiento de -- EGRESO

		$wcco_origen = es_urgencias($wccocod);
		//Si el centro de costos de origen del paciente es de urgencias hara el calculo con los datos de la tabla movhos_000016, sino buscara en la tabla movhos_000032.
		if ($wcco_origen){

			// Aca calculo los días de estancia en el servicio  ************************
			$q =  " SELECT ROUND(TIMESTAMPDIFF(HOUR,Fecha_data,now())/24,2) "
				. "   FROM ".$wbasedato."_000016 "
				. "  WHERE inghis = '".$whistoria."'"
				. "    AND inging = '".$wingreso."'";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			$row = mysql_fetch_array($err);
			$wdiastan = $row[0];

		}else{

			// Aca calculo los días de estancia en el servicio  ************************
			$q =  " SELECT ROUND(TIMESTAMPDIFF(HOUR,Fecha_ing,now())/24,2) "
				. "   FROM ".$wbasedato."_000032 "
				. "  WHERE Historia_clinica = '".$whistoria."'"
				. "    AND Num_ingreso      = '".$wingreso."'"
				. "    AND Servicio         = '".$wccocod."'"
			   ." GROUP BY Num_ing_Serv DESC";

			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			$row = mysql_fetch_array($err);
			$wdiastan = $row[0];

		}

		if ($wdiastan == "" or $wdiastan == 0)
		   $wdiastan = 0;

        if ($wopcion == 'S'){

	        //Actualizo egreso en movhos_000018
		    $con1 = " UPDATE ".$wbasedato."_000018
	                           SET Ubiald='on', Ubifad='".$wfecha."',Ubihad='".$whora."',Ubiuad='".$wuser[1]."'
	                           WHERE Ubihis = '".$whistoria."'
	                               AND Ubiing = '".$wingreso."' ";

	        $err1 = mysql_query($con1, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	        //Inserto la nueva alta en movhos_000033
			$con2 =  " INSERT INTO ".$wbasedato."_000033(   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,   Tipo_Egre_Serv ,  Dias_estan_Serv, Seguridad     ) "
				. "                            VALUES('".$wbasedato."','".$wfecha."','".$whora."','".$whistoria."' ,'".$wingreso."'   ,'".$wccocod."' ,".$wingser."  ,'".$wfecha."' ,'".$whora."' ,'ALTA',".$wdiastan." , 'C-" . $wuser[1] . "')";

			$err1 = mysql_query($con2, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	    }
	    else{

			//Actualizo egreso en movhos_000018
		    $con1 = " UPDATE ".$wbasedato."_000018
	                              SET Ubiald='off',Ubifad='0000-00-00',Ubihad='00:00:00',Ubiuad=''
	                            WHERE Ubihis = '".$whistoria."'
	                              AND Ubiing = '".$wingreso."' ";
	        $err1 = mysql_query($con1, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	        //Inserto la nueva alta en movhos_000033
			$con2  = " Delete from  ".$wbasedato."_000033
	                            WHERE Historia_clinica = '".$whistoria."'
	                              AND Num_ingreso = '".$wingreso."'
	                              AND Servicio = '".$wccocod."'
	                              AND Tipo_Egre_Serv like '%ALTA%' ";

			$err1 = mysql_query($con2, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	    }

		echo $err1.'-'.$wfila;
		return;
}

function es_urgencias($wcco)
{
	global $wbasedato;
	global $conex;

	$q = " SELECT count(*) "
		."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$wcco."' "
		."    AND ccourg = 'on' ";
	$resurg = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rowurg = mysql_fetch_array($resurg);

	if($rowurg[0]>0)
		return true;
	else
		return false;
}

function ConsultaPendiente2($wfecha_i,$wfecha_f,$wcco0){

	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $whce;
	global $wcliame;
	global $selectsede;

	$centros_de_costo      = array();
	$funcionariosRegistros = array();
	$funcionariosIngreso   = array();

	$wtablacliame        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$ccoRegistrosMedicos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoRegistrosMedicos');

	$query_cco = "SELECT ccocod, cconom FROM ".$wbasedato."_000011 ";
	$rescco= mysql_query($query_cco, $conex);
	while ($row_cco = mysql_fetch_assoc($rescco)){
		if ( array_key_exists( $row_cco['ccocod'], $centros_de_costo) == false )
					$centros_de_costo[ $row_cco['ccocod'] ] = $row_cco['cconom'];
	}

	$query = " SELECT codigo
				 FROM usuarios
				WHERE Ccostos = '$ccoRegistrosMedicos'
				  AND Empresa = '{$wemp_pmla}'
				  AND Activo  = 'A' ";
	$rs    = mysql_query($query, $conex);

	while( $row = mysql_fetch_assoc( $rs ) ){
		array_push($funcionariosRegistros,$row['codigo']);
	}

	$wcco1 = explode("-",$wcco0);
	$wcco1[0] = trim($wcco1[0]);


	$query  = " SELECT descripcion, codigo
					  FROM {$wcliame}_000081 a, usuarios b
					 WHERE a.Perusu = b.codigo
					   AND b.activo = 'A'
					   AND b.empresa = '$wemp_pmla'";

	$rs     = mysql_query( $query, $conex );


	while( $row = mysql_fetch_assoc( $rs ) ){
		$funcionariosIngreso[$row['codigo']] = $row['descripcion'];
	}


	echo "<center class=titulo >PACIENTES PENDIENTES </center>";
	echo "<table><tr><td style='color:white; background-color:#DF7401;'>&nbsp;</td><td><font size='2'>Pacientes pendiente desde registros médicos</font></td><td style='color:white; background-color:#088A08;'>&nbsp;</td><td><font size='2'>Pacientes pendiente por otras unidades</font></td></tr></table>";
	echo "<br/>";

	$q = " SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod, G.cconom,"
		."          F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Fecha_egre_serv as fecha_egreso,"
		."          F.Hora_egr_serv as hora_egreso, F.Tipo_egre_serv as tipo_egreso, F.Num_ing_serv "
		."			,UB.Fecha_data as fecha_ingreso, UB.Hora_data as hora_ingreso, UB.Ubisac, UB.Ubihac, tbIng.seguridad as seguridad101, 			     UB.Ubifad, UB.Ubihad, UB.Ubimue, UB.Ubifap, UB.Ubihap "
		."   FROM  	".$wbasedato."_000033 F, ".$wbasedato."_000011 G,  root_000036 A, root_000037 B, ".$wbasedato."_000018 UB, {$wcliame}_000101 tbIng"
		."  WHERE 	F.Historia_clinica = B.Orihis "
		."    AND 	F.Fecha_egre_serv BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' "
		."    AND   F.Historia_clinica = UB.Ubihis "
		."    AND   F.Num_ingreso = UB.Ubiing "
		."    AND 	F.Servicio = G.Ccocod "
		."    AND 	A.Pacced = B.Oriced "
	    ."	  AND 	A.Pactid = B.Oritid "
	    ."    AND 	B.Oriori = '".$wemp_pmla."'"
	    ."    AND   F.Tipo_egre_serv != 'ALTA' "
	    ."    AND   UB.Ubiald = 'off' "
	    ."    AND 	tbIng.inghis = F.Historia_clinica"
	    ."    AND 	tbIng.ingnin = F.Num_ingreso";


	if($wcco0 != "todos") {
		$q.=" AND 	( F.Servicio = '".trim($wcco1[0])."'  ";
		$q.=" OR 	F.Tipo_egre_serv = '".trim($wcco1[0])."'  )";
	}else{
        $sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
        if ($sFiltrarSede == 'on' && $selectsede != '') $q.=" AND G.Ccosed = '".$selectsede."'";
    }
	if( $wcco0 != "todos" && trim($wcco1[0]) == '1179' ){
		$q.= " AND   UB.Ubihac != '' ";
	}

	$q.=" ORDER BY ccocod, fecha_egreso, hora_egreso";

	echo "<br/>";
	echo "<table align=center name='tablaRespuesta'>";
	echo "<tr><td colspan=10> Escriba para realizar busquedas: <input type='text' id='input_buscador'></td></tr>";

	$cco_mostrado = "";
	$i=0;
	$wtotal = 0;
	$wgtotal = 0;
	$res= mysql_query($q, $conex);
	$pacientes_repetidos = array();
    $contfila = 0;

	while($row = mysql_fetch_assoc($res)) {
		$seguir = true;

		if( $row['ccocod'] == '1179'  && $row['Ubihac'] == '' ){
			continue;
		}

		if( trim($row['ccocod']) != trim($wcco1[0]) and is_numeric($row['tipo_egreso']) == true  and trim($wcco0) != "todos"){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = false;
		}

		if(trim($wcco0) == "todos" and is_numeric($row['tipo_egreso']) == true ){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = true;
		}

		if( $seguir == true ){

			if($cco_mostrado != $row['ccocod'] ){
				$wdetalle = 0;
				if( $i != 0){
					if  ($wtotal>0)
					     $encabez = "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";
					$encabez .= "<tr><td colspan=10>&nbsp;</td></tr>";

				}
				$encabez .= "<tr class=titulo><td colspan=12>";
				$encabez .= $row['ccocod']." - ".$row["cconom"];
				$encabez .= "</td></tr>";
				$encabez .= "<tr class=encabezadoTabla>";
				$encabez .= "<td align=center>Historia</td>";
				$encabez .= "<td align=center>Ingreso</td>";
				$encabez .= "<td align=center>Paciente</td>";
				$encabez .= "<td align=center>Servicio <br/> Ingreso</td>";
				$encabez .= "<td align=center>Fecha de<br/> Ingreso</td>";
				$encabez .= "<td align=center>Hora<br/>de Ingreso</td>";
				$encabez .= "<td align=center>Fecha<br/>de Egreso</td>";
				$encabez .= "<td align=center>Hora <br/>de Egreso</td>";
				$encabez .= "<td align=center>Poner <br/>en Alta</td>";
				$encabez .= "<td align=center>Motivo Egreso</td>";
				$encabez .= "<td align=center>Funcionario<br>Ingreso</td>";
				$encabez .= "<td align=center>&nbsp;</td>";
				$encabez .= "</tr>";
				$cco_mostrado = $row['ccocod'];

			}

			$and_ing = "";
			$indi = 0;
			$indi2 = 0;
			foreach ($pacientes_repetidos as $clave){
				if( preg_match("/^".$row['ccocod']."-".$row['historia']."-".$row['ingreso']."/", $clave) ){
					$datos = explode("-",$clave);
					$and_ing.=" AND Num_ing_serv = ".$datos[4]." AND Procedencia='".$datos[3]."'";
					$indi2 = $indi;
				}
				$indi++;
			}
			$query_ingreso_32 = "  SELECT 	A.Fecha_ing as fecha_ingreso, A.Hora_ing as hora_ingreso, B.Cconom as cco_ingreso"
								."   FROM  	".$wbasedato."_000032 A, ".$wbasedato."_000011 B"
								."  WHERE 	A.Historia_clinica = '".$row['historia']."'"
								." 	  AND	A.Num_ingreso = '".$row['ingreso']."'"
								."    AND 	A.Servicio = '".$row['ccocod']."'"
								."    AND 	A.Procedencia = B.Ccocod ";

			if ( ! empty( $and_ing ) ){
				$pacientes_repetidos[$indi2] = "";
				$query_ingreso_32.=$and_ing;
			}

			$res32= mysql_query($query_ingreso_32, $conex);
			$num_ing = mysql_num_rows($res32);
			if( $num_ing > 0 ){
				$row_ing = mysql_fetch_assoc($res32);
			}else{
				$row_ing['fecha_ingreso'] = $row['fecha_ingreso'];
				$row_ing['hora_ingreso'] = $row['hora_ingreso'];
				$row_ing['cco_ingreso'] = $centros_de_costo[ $row['Ubisac'] ];
			}


			if( is_numeric( $row["tipo_egreso"] )){
				$row["tipo_egreso"] = $centros_de_costo[ $row["tipo_egreso"] ];
			}

			if($i % 2 == 0)
				$wclass="fila1";
			else
				$wclass="fila2";


			$i++;

			$row_ing['hora_ingreso'] = substr_replace( $row_ing['hora_ingreso'] ,"",-3 );
			$row['hora_egreso']      = substr_replace( $row['hora_egreso'] ,"",-3 );
			$wpos_Alta               = stripos($row["tipo_egreso"],"ALTA");
			$wpos_Muerte             = stripos($row["tipo_egreso"],"MUERTE");
			$title_btn               = "";
			$title2_btn              = "";
			$path2                   = "";
			$color                   = "";
            $num2                    = 0;
            $vopcion                 = 0;

            $fechaAltDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubifad'] : $row['Ubifap'] ;
            $horaAltaDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubihad'] : $row['Ubihap'] ;

			if ($wpos_Alta === false and $wpos_Muerte === false){
				$vopcion = 1;
				$title_btn  = "Ver HCE";
				$title2_btn = "Egresar";
				$path  = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$whce."&wcedula=".$row["Pacced"]."&wtipodoc=".$row["Pactid"]."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato;
				$path2 = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}";

			}else {
				$path = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}}";
				$title_btn = "Egresar";

				$vopcion = 1;

				//Si ya existe el egreso, cambia el path
				$qEg = "SELECT id, seguridad
						  FROM ".$wtablacliame."_000108
						 WHERE Egrhis='".$row["historia"]."'
						   AND Egring='".$row["ingreso"]."'
						   AND Egract='on'";

				$res2 = mysql_query( $qEg, $conex );

				if( $res2 ){
					$num2 = mysql_num_rows( $res2 );
					if( $num2 > 0 ){
						$vopcion = 0;
						$rowFunc     = mysql_fetch_assoc( $res2 );
						$funcionario = explode("-",$rowFunc['seguridad']);
						if( in_array($funcionario[1],$funcionariosRegistros ) ){
							$color="color:white; background-color:#DF7401;";
						}else{
							$color="color:white; background-color:#088A08;";
						}
						$title_btn = "Egresado";
						$path = "/matrix/admisiones/procesos/egreso_erp.php?c_param=1&wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}";
					}
				}
			}


				if ($vopcion === 1) {

					$wdetalle++;
				    $wgtotal ++;
	                $contfila++;

	                if ($wdetalle===1 ){
	                	echo $encabez;
	                	$wtotal = 0;
	                }

	                $wtotal++;
					echo "<tr class=".$wclass.">";
					echo "<td align=center style='".$color."'>".$row["historia"]."</td>"; //historia
					echo "<td align=center>".$row["ingreso"]."</td>"; //ingreso
					echo "<td align=left nowrap='nowrap'>".$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]."</td>"; //paciente
					echo "<td align=center>".$row_ing['cco_ingreso']."</td>"; //servicio ingreso
					echo "<td align=center nowrap='nowrap'>".$row_ing['fecha_ingreso']."</td>"; //fecha ingreso
					echo "<td align=center>".$row_ing['hora_ingreso']."</td>"; //hora ingreso
					echo "<td align=center nowrap='nowrap'><b>".$row["fecha_egreso"]."</b></td>";	//fecha egreso
					echo "<td align=center><b>".$row["hora_egreso"]."</b></td>";	//hora egreso
					echo "<td align=center nowrap='nowrap'><input type='checkbox' id='chkdaralta_".$contfila."' name='chkdaralta_".$contfila."' value=".$row["historia"]."-".$row["ingreso"]."-".$row['ccocod']."-".$contfila." onclick='Grabardaralta(this.value)'></td>";
					echo "<td align=center nowrap='nowrap' class='tipo_".$contfila."'>".trim($row["tipo_egreso"])."</td>";	//tipo egreso
					$codigoUsuario = explode("-", $row['seguridad101']);
					$funcionario_ing = (array_key_exists($codigoUsuario[1], $funcionariosIngreso)) ? $funcionariosIngreso[$codigoUsuario[1]] : '';
					$funcionario_ing = ucwords(mb_strtolower($funcionario_ing));
					echo "<td align=center nowrap='nowrap' style='text-align:left;'>".$funcionario_ing."</td>";	//tipo egreso
					echo "<td class='ruta_".$contfila."'><A id='refa_".$contfila."' style='cursor:pointer; ".$color."' onClick='ejecutar(\"".$path."\")'><b>".$title_btn."</b></A><A id='refb_".$contfila."' style='cursor:pointer;display:none;".$color."' onClick='ejecutar(\"".$path2."\")'><b>".$title2_btn."</b></A></td>"; //enlace hce
					echo "<td id='refc_".$contfila."' style='display:none;'><td>";
					echo "</tr>";
				}
		}
	 }
	 if  ($wtotal>0){
		 echo "<tr class='encabezadoTabla'><td colspan='11' align=right> Total Servicios : ".$wtotal."</td></tr>";
		 echo "<tr><td colspan=11>&nbsp;</td></tr>";
		 if($wgtotal == 0)
			$wgtotal=$wtotal;
	 }
	 echo "<tr class='encabezadoTabla'><td colspan='11' align=right>Gran Total de Servicios : ".$wgtotal."</td></tr>";
	 echo "</table>";
	 echo "<br/>";


}

function ConsultaEgreso2($wfecha_i,$wfecha_f,$wcco0){
	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $selectsede;
	global $whce;
	global $wcliame;
	global $ccoRegistrosMedicos;

	$centros_de_costo      = array();
	$funcionariosRegistros = array();
	$funcionariosIngreso   = array();

	$wtablacliame        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

	$query_cco = "SELECT ccocod, cconom FROM ".$wbasedato."_000011 ";
	$rescco= mysql_query($query_cco, $conex);
	while ($row_cco = mysql_fetch_assoc($rescco)){
		if ( array_key_exists( $row_cco['ccocod'], $centros_de_costo) == false )
					$centros_de_costo[ $row_cco['ccocod'] ] = $row_cco['cconom'];
	}

	$query = " SELECT codigo
				 FROM usuarios
				WHERE Ccostos = '$ccoRegistrosMedicos'
				  AND Empresa = '{$wemp_pmla}'
				  AND Activo  = 'A' ";
	$rs    = mysql_query($query, $conex);

	while( $row = mysql_fetch_assoc( $rs ) ){
		array_push($funcionariosRegistros,$row['codigo']);
	}

	$wcco1 = explode("-",$wcco0);
	$wcco1[0] = trim($wcco1[0]);

	// if( $wcco1[0] == "todos" ){

		$query  = " SELECT descripcion, codigo
					  FROM {$wcliame}_000081 a, usuarios b
					 WHERE a.Perusu = b.codigo
					   AND b.activo = 'A'
					   AND b.empresa = '$wemp_pmla'";

	$rs     = mysql_query( $query, $conex );


	while( $row = mysql_fetch_assoc( $rs ) ){
		$funcionariosIngreso[$row['codigo']] = $row['descripcion'];
	}

	$entidades = array();
	$query = " SELECT Empcod, Empnom
				 FROM {$wcliame}_000024";
	$rs    = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) ){
		$entidades[$row[0]] = $row[1];
	}
	$codigoempresaparticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
	$entidades[$codigoempresaparticular] = "PARTICULAR";

	echo "<center class=titulo >PACIENTES EGRESADOS </center>";
	echo "<table><tr><td style='color:white; background-color:#DF7401;'>&nbsp;</td><td><font size='2'>Pacientes egresados desde registros m&eacute;dicos</font></td><td style='color:white; background-color:#088A08;'>&nbsp;</td><td><font size='2'>Pacientes egresados por otras unidades</font></td></tr></table>";
	echo "<br/>";

	$q = " SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod, G.cconom,"
		."          F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Fecha_egre_serv as fecha_egreso,"
		."          F.Hora_egr_serv as hora_egreso, F.Tipo_egre_serv as tipo_egreso, F.Num_ing_serv "
		."			,UB.Fecha_data as fecha_ingreso, UB.Hora_data as hora_ingreso, UB.Ubisac, UB.Ubihac, tbIng.seguridad as seguridad101, tbIng.ingcem, A.Pacced as pacdoc, UB.Ubifad, UB.Ubihad, UB.Ubimue, UB.Ubifap, UB.Ubihap "
		."   FROM  	".$wbasedato."_000033 F, ".$wbasedato."_000011 G,  root_000036 A, root_000037 B, ".$wbasedato."_000018 UB, {$wcliame}_000101 tbIng"
		."  WHERE 	F.Historia_clinica = B.Orihis "
		."    AND 	F.Fecha_egre_serv BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' "
		."    AND   F.Historia_clinica = UB.Ubihis "
		."    AND   F.Num_ingreso = UB.Ubiing "
		."    AND 	F.Servicio = G.Ccocod "
		."    AND 	A.Pacced = B.Oriced "
	    ."	  AND 	A.Pactid = B.Oritid "
	    ."    AND 	B.Oriori = '".$wemp_pmla."'"
	    ."    AND 	tbIng.inghis = F.Historia_clinica"
	    ."    AND 	tbIng.ingnin = F.Num_ingreso";

	if($wcco0 != "todos") {
		$q.=" AND 	( F.Servicio = '".trim($wcco1[0])."'  ";
		$q.=" OR 	F.Tipo_egre_serv = '".trim($wcco1[0])."'  )";
	}else{
        $sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
        if ($sFiltrarSede == 'on' && $selectsede != '') $q.=" AND G.Ccosed = '".$selectsede."'";
    }
	if( $wcco0 != "todos" && trim($wcco1[0]) == '1179' ){
		$q.= " AND   UB.Ubihac != '' ";
	}

	$q.=" ORDER BY ccocod, fecha_egreso, hora_egreso";

	echo "<br/>";
	echo "<table align=center name='tablaRespuesta'>";
	echo "<tr><td colspan=10> Escriba para realizar busquedas: <input type='text' id='input_buscador'></td></tr>";

	$cco_mostrado = "";
	$i=0;
	$wtotal = 0;
	$wgtotal = 0;
	$res= mysql_query($q, $conex);

	$pacientes_repetidos = array();

	while($row = mysql_fetch_assoc($res)) {
		$seguir = true;

		if( $row['ccocod'] == '1179'  && $row['Ubihac'] == '' ){
			continue;
		}

		if( trim($row['ccocod']) != trim($wcco1[0]) and is_numeric($row['tipo_egreso']) == true  and trim($wcco0) != "todos"){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = false;
		}

		if(trim($wcco0) == "todos" and is_numeric($row['tipo_egreso']) == true ){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = true;
		}

		if( $seguir == true ){

			if($cco_mostrado != $row['ccocod'] ){
				if( $i != 0){
					echo "<tr class='encabezadoTabla'><td colspan='11' align=right> Total Servicios : ".$wtotal."</td></tr>";
					echo "<tr><td colspan=11>&nbsp;</td></tr>";
				}
				echo "<tr class=titulo><td colspan=12>";
				echo $row['ccocod']." - ".$row["cconom"];
				echo "</td></tr>";
				echo "<tr class=encabezadoTabla>";
				echo "<td align=center>Historia</td>";
				echo "<td align=center>Ingreso</td>";
				echo "<td align=center>Paciente</td>";
				echo "<td align=center>Entidad</td>";
				echo "<td align=center>Servicio <br/> Ingreso</td>";
				echo "<td align=center>Fecha de<br/> Ingreso</td>";
				echo "<td align=center>Hora<br/>de Ingreso</td>";
				echo "<td align=center>Fecha<br/>de Egreso</td>";
				echo "<td align=center>Hora <br/>de Egreso</td>";
				echo "<td align=center>Motivo Egreso</td>";
				echo "<td align=center>Funcionario<br>Ingreso</td>";
				echo "<td align=center>&nbsp;</td>";
				echo "</tr>";
				$cco_mostrado = $row['ccocod'];
				$wtotal = 0;
			}

			$and_ing = "";
			$indi = 0;
			$indi2 = 0;
			foreach ($pacientes_repetidos as $clave){
				if( preg_match("/^".$row['ccocod']."-".$row['historia']."-".$row['ingreso']."/", $clave) ){
					$datos = explode("-",$clave);
					$and_ing.=" AND Num_ing_serv = ".$datos[4]." AND Procedencia='".$datos[3]."'";
					$indi2 = $indi;
				}
				$indi++;
			}
			$query_ingreso_32 = "  SELECT 	A.Fecha_ing as fecha_ingreso, A.Hora_ing as hora_ingreso, B.Cconom as cco_ingreso"
								."   FROM  	".$wbasedato."_000032 A, ".$wbasedato."_000011 B"
								."  WHERE 	A.Historia_clinica = '".$row['historia']."'"
								." 	  AND	A.Num_ingreso = '".$row['ingreso']."'"
								."    AND 	A.Servicio = '".$row['ccocod']."'"
								."    AND 	A.Procedencia = B.Ccocod ";

			if ( ! empty( $and_ing ) ){
				$pacientes_repetidos[$indi2] = "";
				$query_ingreso_32.=$and_ing;
			}

			$res32= mysql_query($query_ingreso_32, $conex);
			$num_ing = mysql_num_rows($res32);
			if( $num_ing > 0 ){
				$row_ing = mysql_fetch_assoc($res32);
			}else{
				$row_ing['fecha_ingreso'] = $row['fecha_ingreso'];
				$row_ing['hora_ingreso'] = $row['hora_ingreso'];
				$row_ing['cco_ingreso'] = $centros_de_costo[ $row['Ubisac'] ];
			}


			if( is_numeric( $row["tipo_egreso"] )){
				$row["tipo_egreso"] = $centros_de_costo[ $row["tipo_egreso"] ];
			}

			if($i % 2 == 0)
				$wclass="fila1";
			else
				$wclass="fila2";

			$wtotal++;
			$wgtotal++;
			$i++;

			$row_ing['hora_ingreso'] = substr_replace( $row_ing['hora_ingreso'] ,"",-3 );
			$row['hora_egreso']      = substr_replace( $row['hora_egreso'] ,"",-3 );
			$wpos_Alta               = stripos($row["tipo_egreso"],"ALTA");
			$wpos_Muerte             = stripos($row["tipo_egreso"],"MUERTE");
			$title_btn               = "";
			$color                   = "";

			if ($wpos_Alta === false and $wpos_Muerte === false){
				$title_btn = "Ver HCE";
				$path = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$whce."&wcedula=".$row["Pacced"]."&wtipodoc=".$row["Pactid"]."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato;
			}else {
				$fechaAltDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubifad'] : $row['Ubifap'] ;
                $horaAltaDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubihad'] : $row['Ubihap'] ;
				//$path = "/matrix/hce/procesos/TableroAnt.php?empresa=".$wbasedato."&codemp=".$wemp_pmla."&historia=hce&accion=I&whis=".$row["historia"];
				$path = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}";
				$title_btn = "Egresar";
				//Si ya existe el egreso, cambia el path
				$qEg = "SELECT id, seguridad
						  FROM ".$wtablacliame."_000108
						 WHERE Egrhis='".$row["historia"]."'
						   AND Egring='".$row["ingreso"]."'
						   AND Egract='on'";
				$res2 = mysql_query( $qEg, $conex );
				if( $res2 ){
					$num2 = mysql_num_rows( $res2 );
					if( $num2 > 0 ){
						$rowFunc     = mysql_fetch_assoc( $res2 );
						$funcionario = explode("-",$rowFunc['seguridad']);
						if( in_array($funcionario[1],$funcionariosRegistros ) ){
							$color="color:white; background-color:#DF7401;";
						}else{
							$color="color:white; background-color:#088A08;";
						}
						$title_btn = "Egresado";
						$fechaAltDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubifad'] : $row['Ubifap'] ;
                		$horaAltaDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubihad'] : $row['Ubihap'] ;
						$path = "/matrix/admisiones/procesos/egreso_erp.php?c_param=1&wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}";
					}
				}
			}
			if( $row['pacdoc'] == $row["ingcem"])
				$row["ingcem"] = $codigoempresaparticular;
			echo "<tr class=".$wclass.">";
			echo "<td align=center style='".$color."'>".$row["historia"]."</td>"; //historia
			echo "<td align=center>".$row["ingreso"]."</td>"; //ingreso
			echo "<td align=left nowrap='nowrap'>".$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]."</td>"; //paciente
			echo "<td align=left nowrap='nowrap'>".$entidades[$row["ingcem"]]."</td>"; //entidad
			echo "<td align=center>".$row_ing['cco_ingreso']."</td>"; //servicio ingreso
			echo "<td align=center nowrap='nowrap'>".$row_ing['fecha_ingreso']."</td>"; //fecha ingreso
			echo "<td align=center>".$row_ing['hora_ingreso']."</td>"; //hora ingreso

			echo "<td align=center nowrap='nowrap'><b>".$row["fecha_egreso"]."</b></td>";	//fecha egreso
			echo "<td align=center><b>".$row["hora_egreso"]."</b></td>";	//hora egreso
			echo "<td align=center nowrap='nowrap'>".$row["tipo_egreso"]."</td>";	//tipo egreso
			$codigoUsuario = explode("-", $row['seguridad101']);
			$funcionario_ing = (array_key_exists($codigoUsuario[1], $funcionariosIngreso)) ? $funcionariosIngreso[$codigoUsuario[1]] : '';
			$funcionario_ing = ucwords(mb_strtolower($funcionario_ing));
			echo "<td align=center nowrap='nowrap' style='text-align:left;'>".$funcionario_ing."</td>";	//tipo egreso
			echo "<td><A style='cursor:pointer; ".$color."' onClick='ejecutar(\"".$path."\")'><b>".$title_btn."</b></A></td>"; //enlace hce
			echo "</tr>";
		}
	 }
	 echo "<tr class='encabezadoTabla'><td colspan='12' align=right> Total Servicios : ".$wtotal."</td></tr>";
	 echo "<tr><td colspan=11>&nbsp;</td></tr>";
	 if($wgtotal == 0)
		$wgtotal=$wtotal;
	 echo "<tr class='encabezadoTabla'><td colspan='12' align=right>Gran Total de Servicios : ".$wgtotal."</td></tr>";
	 echo "</table>";
	 echo "<br/>";
}

function ConsultaIngreso2($wfecha_i,$wfecha_f,$wcco0){
	global $wemp_pmla;
	global $conex;
	global $selectsede;
	global $wbasedato;
	global $whce;

	$wtablacliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

	echo "<center class=titulo >PACIENTES INGRESADOS </center>";
	echo "<br/>";
	$wcco1 = explode("-",$wcco0);

	$q = " SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod, G.cconom,"
		."          F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Fecha_ing as fecha_ingreso,"
		."          F.Hora_ing as hora_ingreso, F.Procedencia"
		."   FROM  	".$wbasedato."_000032 F, ".$wbasedato."_000011 G,  root_000036 A, root_000037 B "
		."  WHERE 	F.Historia_clinica = B.Orihis "
		."    AND 	F.Fecha_ing BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' "
		."    AND 	F.Servicio = G.Ccocod "
		."    AND 	A.Pacced = B.Oriced "
	    ."	  AND 	A.Pactid = B.Oritid "
	    ."    AND 	B.Oriori = '".$wemp_pmla."'";

		if($wcco0!= "todos") {
			$q.=" AND 	F.Servicio = '".$wcco1[0]."'  ";
		}else{
            $sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
            if ($sFiltrarSede == 'on' && $selectsede != '') $q.=" AND G.Ccosed = '".$selectsede."'";
        }

		$q.=" ORDER BY ccocod, fecha_ingreso, hora_ingreso";
		echo "<br/>";
	 //IMPRIMIMOS LA TABLA POR CENTRO DE COSTO DE LOS PACIENTES INGRESADOS
	 echo "<table align=center name='tablaRespuesta'>";
	 echo "<tr><td colspan=10> Escriba para realizar busquedas: <input type='text' id='input_buscador'></td></tr>";

	$cco_mostrado = "";
	 $i=0;
	 $wtotal = 0;
	 $wgtotal = 0;
	 $res= mysql_query($q, $conex);


	while($row = mysql_fetch_assoc($res)) {

		if($cco_mostrado != $row['ccocod'] ){
			if( $i != 0){
				echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";
				echo "<tr><td colspan=10>&nbsp;</td></tr>";
			}
			echo "<tr class=titulo><td colspan=7>";
			echo $row['ccocod']." - ".$row["cconom"];
			echo "</td></tr>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center>Historia</td>";
			echo "<td align=center>Ingreso</td>";
			echo "<td align=center>Paciente</td>";
			echo "<td align=center>Servicio <br/> Ingreso</td>";
			echo "<td align=center >Fecha de<br/> Ingreso</td>";
			echo "<td align=center >Hora<br/>de Ingreso</td>";
			echo "<td align=center>&nbsp;</td>";
			echo "</tr>";
			$cco_mostrado = $row['ccocod'];
			$wtotal = 0;
		}


		if($i % 2 == 0)
			$wclass="fila1";
		else
			$wclass="fila2";

		$wtotal++;
		$wgtotal++;
		$i++;

		$query_cco = "SELECT cconom FROM ".$wbasedato."_000011 WHERE Ccocod = '".$row["Procedencia"]."'";
		$rescco= mysql_query($query_cco, $conex);
		$row_cco = mysql_fetch_assoc($rescco);
		$row["procede"] = $row_cco['cconom'];


		$row['hora_ingreso']= substr_replace( $row['hora_ingreso'] ,"",-3 );

		echo "<tr class=".$wclass.">";
		echo "<td align=center>".$row["historia"]."</td>"; //historia
		echo "<td align=center>".$row["ingreso"]."</td>"; //ingreso
		echo "<td align=left nowrap='nowrap'>".$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]."</td>"; //paciente
		echo "<td align=center>".$row["procede"]."</td>"; //servicio ingreso
		echo "<td align=center nowrap='nowrap'>".$row['fecha_ingreso']."</td>"; //fecha ingreso
		echo "<td align=center>".$row['hora_ingreso']."</td>"; //hora ingreso

		$title_btn = "Ver HCE";
		$path = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$whce."&wcedula=".$row["Pacced"]."&wtipodoc=".$row["Pactid"]."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato;

		echo "<td><A style='cursor:pointer;' onClick='ejecutar(\"".$path."\")'><b>".$title_btn."</b></A></td>"; //enlace hce
		echo "</tr>";

	 }
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";
	 echo "<tr><td colspan=10>&nbsp;</td></tr>";
	 if($wgtotal == 0)
		$wgtotal=$wtotal;
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right>Gran Total de Servicios : ".$wgtotal."</td></tr>";
	 echo "</table>";
	 echo "<br/>";
}

function ConsultaIngreso3( $whis ){
	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $whce;

	$centros_de_costo = array();

	$wtablacliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

	$query_cco = "SELECT ccocod, cconom FROM ".$wbasedato."_000011 ";
	$rescco= mysql_query($query_cco, $conex);
	while ($row_cco = mysql_fetch_assoc($rescco)){
		if ( array_key_exists( $row_cco['ccocod'], $centros_de_costo) == false )
					$centros_de_costo[ $row_cco['ccocod'] ] = $row_cco['cconom'];
	}

	echo "<center class=titulo >PACIENTES EGRESADOS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</center><br>";
	echo "<br/>";
	$wcco1 = explode("-",$wcco0);

	$q = " SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod, G.cconom,"
		."          F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Fecha_egre_serv as fecha_egreso,"
		."          F.Hora_egr_serv as hora_egreso, F.Tipo_egre_serv as tipo_egreso, F.Num_ing_serv "
		."			,UB.Fecha_data as fecha_ingreso, UB.Hora_data as hora_ingreso, UB.Ubisac, UB.Ubihac, UB.Ubifad, UB.Ubihad, UB.Ubimue, UB.Ubifap, UB.Ubihap "
		."   FROM  	".$wbasedato."_000033 F, ".$wbasedato."_000011 G,  root_000036 A, root_000037 B, ".$wbasedato."_000018 UB "
		."  WHERE 	F.Historia_clinica = '{$whis}'"
		."    AND   F.Historia_clinica = B.Orihis "
		."    AND   F.Historia_clinica = UB.Ubihis "
		."    AND   F.Num_ingreso = UB.Ubiing "
		."    AND 	F.Servicio = G.Ccocod "
		."    AND 	A.Pacced = B.Oriced "
	    ."	  AND 	A.Pactid = B.Oritid "
	    ."    AND 	B.Oriori = '".$wemp_pmla."'";
	$q.=" ORDER BY ccocod, fecha_egreso, hora_egreso";

	echo "<br/>";
	//echo "<pre>".print_r( $q, true )."</pre>";
	echo "<table align=center name='tablaRespuesta'>";
	echo "<tr><td colspan=10> Escriba para realizar busquedas: <input type='text' id='input_buscador'></td></tr>";

	$cco_mostrado = "";
	$i=0;
	$wtotal = 0;
	$wgtotal = 0;
	$res= mysql_query($q, $conex);

	$pacientes_repetidos = array();

	while($row = mysql_fetch_assoc($res)) {
		$seguir = true;

		if( trim($row['ccocod']) != trim($wcco1[0]) and is_numeric($row['tipo_egreso']) == true  and trim($wcco0) != "todos"){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = false;
		}

		if(trim($wcco0) == "todos" and is_numeric($row['tipo_egreso']) == true ){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = true;
		}

		if( $seguir == true ){
			if($cco_mostrado != $row['ccocod'] ){
				if( $i != 0){
					echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";
					echo "<tr><td colspan=10>&nbsp;</td></tr>";
				}
				echo "<tr class=titulo><td colspan=10>";
				echo $row['ccocod']." - ".$row["cconom"];
				echo "</td></tr>";
				echo "<tr class=encabezadoTabla>";
				echo "<td align=center>Historia</td>";
				echo "<td align=center>Ingreso</td>";
				echo "<td align=center>Paciente</td>";
				echo "<td align=center>Servicio <br/> Ingreso</td>";
				echo "<td align=center>Fecha de<br/> Ingreso</td>";
				echo "<td align=center>Hora<br/>de Ingreso</td>";
				echo "<td align=center>Fecha<br/>de Egreso</td>";
				echo "<td align=center>Hora <br/>de Egreso</td>";
				echo "<td align=center>Motivo Egreso</td>";
				echo "<td align=center>&nbsp;</td>";
				echo "</tr>";
				$cco_mostrado = $row['ccocod'];
				$wtotal = 0;
			}

			$and_ing = "";
			$indi = 0;
			$indi2 = 0;
			foreach ($pacientes_repetidos as $clave){
				if( preg_match("/^".$row['ccocod']."-".$row['historia']."-".$row['ingreso']."/", $clave) ){
					$datos = explode("-",$clave);
					$and_ing.=" AND Num_ing_serv = ".$datos[4]." AND Procedencia='".$datos[3]."'";
					$indi2 = $indi;
				}
				$indi++;
			}
			$query_ingreso_32 = "  SELECT 	A.Fecha_ing as fecha_ingreso, A.Hora_ing as hora_ingreso, B.Cconom as cco_ingreso"
								."   FROM  	".$wbasedato."_000032 A, ".$wbasedato."_000011 B"
								."  WHERE 	A.Historia_clinica = '".$row['historia']."'"
								." 	  AND	A.Num_ingreso = '".$row['ingreso']."'"
								."    AND 	A.Servicio = '".$row['ccocod']."'"
								."    AND 	A.Procedencia = B.Ccocod ";

			if ( ! empty( $and_ing ) ){
				$pacientes_repetidos[$indi2] = "";
				$query_ingreso_32.=$and_ing;
			}

			$res32= mysql_query($query_ingreso_32, $conex);
			$num_ing = mysql_num_rows($res32);
			if( $num_ing > 0 ){
				$row_ing = mysql_fetch_assoc($res32);
			}else{
				$row_ing['fecha_ingreso'] = $row['fecha_ingreso'];
				$row_ing['hora_ingreso'] = $row['hora_ingreso'];
				$row_ing['cco_ingreso'] = $centros_de_costo[ $row['Ubisac'] ];
			}


			if( is_numeric( $row["tipo_egreso"] )){
				$row["tipo_egreso"] = $centros_de_costo[ $row["tipo_egreso"] ];
			}

			if($i % 2 == 0)
				$wclass="fila1";
			else
				$wclass="fila2";

			$wtotal++;
			$wgtotal++;
			$i++;

			$row_ing['hora_ingreso'] = substr_replace( $row_ing['hora_ingreso'] ,"",-3 );
			$row['hora_egreso']      = substr_replace( $row['hora_egreso'] ,"",-3 );
			$wpos_Alta               = stripos($row["tipo_egreso"],"ALTA");
			$wpos_Muerte             = stripos($row["tipo_egreso"],"MUERTE");
			$title_btn               = "";
			$color                   = "";

			if ($wpos_Alta === false and $wpos_Muerte === false){
				$title_btn = "Ver HCE";
				$path = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$whce."&wcedula=".$row["Pacced"]."&wtipodoc=".$row["Pactid"]."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato;
			}else {
				//$path = "/matrix/hce/procesos/TableroAnt.php?empresa=".$wbasedato."&codemp=".$wemp_pmla."&historia=hce&accion=I&whis=".$row["historia"];
				$fechaAltDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubifad'] : $row['Ubifap'] ;
                $horaAltaDefinitiva  = ( $row['Ubimue'] != "on" ) ? $row['Ubihad'] : $row['Ubihap'] ;
				$path = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}";
				$title_btn = "Egresar";
				//Si ya existe el egreso, cambia el path
				$qEg = "SELECT id
						  FROM ".$wtablacliame."_000108
						 WHERE Egrhis='".$row["historia"]."'
						   AND Egring='".$row["ingreso"]."'
						   AND Egract='on'";
				$res2 = mysql_query( $qEg, $conex );
				if( $res2 ){
					$num2 = mysql_num_rows( $res2 );
					if( $num2 > 0 ){
						$color="color:white; background-color:orange;";
						$title_btn = "Egresado";
						$path = "/matrix/admisiones/procesos/egreso_erp.php?c_param=1&wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}";
					}
				}
			}

			echo "<tr class=".$wclass.">";
			echo "<td align=center style='".$color."'>".$row["historia"]."</td>"; //historia
			echo "<td align=center>".$row["ingreso"]."</td>"; //ingreso
			echo "<td align=left nowrap='nowrap'>".$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]."</td>"; //paciente
			echo "<td align=center>".$row_ing['cco_ingreso']."</td>"; //servicio ingreso
			echo "<td align=center nowrap='nowrap'>".$row_ing['fecha_ingreso']."</td>"; //fecha ingreso
			echo "<td align=center>".$row_ing['hora_ingreso']."</td>"; //hora ingreso

			echo "<td align=center nowrap='nowrap'><b>".$row["fecha_egreso"]."</b></td>";	//fecha egreso
			echo "<td align=center><b>".$row["hora_egreso"]."</b></td>";	//hora egreso
			echo "<td align=center nowrap='nowrap'>".$row["tipo_egreso"]."</td>";	//tipo egreso

			echo "<td><A style='cursor:pointer; ".$color."' onClick='ejecutar(\"".$path."\")'><b>".$title_btn."</b></A></td>"; //enlace hce
			echo "</tr>";
		}
	 }
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";
	 echo "<tr><td colspan=10>&nbsp;</td></tr>";
	 if($wgtotal == 0)
		$wgtotal=$wtotal;
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right>Gran Total de Servicios : ".$wgtotal."</td></tr>";
	 echo "</table>";
	 echo "<br/>";
}

function verificarServicioAyuda( $cco ){

	global $conex, $wemp_pmla, $aplMovhos;

	$query = "SELECT ccoayu
			    FROM {$aplMovhos}_000011
			   WHERE ccocod = '{$ccoIngreso}'";
	$rs = mysql_query($query,$conex);
	$row = mysql_fetch_assoc($rs);
	$ccoAyu = ( $row['ccoayu'] == "on" ) ? true : false;
	return($ccoAyu);
}

function vistaInicial(){

	global $conex;
	global $whce;
	global $wbasedato;
	global $wemp_pmla;
	global $wactualiz;
	global $wfecha_i;
	global $wfecha_f;
	global $wcco0;
	global $whisconsultada;
	global $bandera;
	global $tipo;
	global $conex;
	global $selectsede;
	global $user;
	global $wcliame;
	global $ccoRegistrosMedicos;
	$funcionariosRegistros = array();

	$user2 = explode("-",$user);
	$query = " SELECT codigo
				 FROM usuarios
				WHERE Ccostos = '$ccoRegistrosMedicos'
				  AND Empresa = '{$wemp_pmla}'
				  AND Activo  = 'A' ";
	$rs    = mysql_query($query, $conex);

	while( $row = mysql_fetch_assoc( $rs ) ){
		array_push($funcionariosRegistros,$row['codigo']);
	}
	if( in_array( $user2[1], $funcionariosRegistros )  ){
		$mostrarCambioDocumento = " display:block; ";
	}else{
		$mostrarCambioDocumento = " display:none; ";
	}

	 $titulo = "Reporte Pacientes Egresados y Activos";
     echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' name='selectsede' id='sede' value='".$selectsede."'>";
	 encabezado($titulo, $wactualiz, "clinica", true);
	 echo "<br/>";

	 echo "<form action='rep_PacientesEgresadosActivos.php' name='historias' method='post'>";
     if( (!isset($wfecha_i ) or !isset($wfecha_i ) or !isset($wcco0) or isset($bandera))  )
		{
			if(!isset($wfecha_i ) && !isset($wfecha_i ))
			   {
					$wfecha_i = date("Y-m-d");
					$wfecha_f = date("Y-m-d");
			   }

			$condicionCcoPermitidos = "1";
			$limitacionAyudas       = false;

			$querycco = " SELECT Percca, Perccd
						    FROM {$wcliame}_000081
						   WHERE Perfue = '01'
						     AND Perusu ='{$user2[1]}'";

            $sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");

			$rsAux = mysql_query( $querycco, $conex );
			$numRs = mysql_num_rows( $rsAux );
			while( $rowRs = mysql_fetch_assoc( $rsAux ) ){

				$ccoPermitidos = $rowRs['Percca'];

				if( $ccoPermitidos != "" ){
					$limitacionAyudas = true;


					$ccoPermitidos = explode(",",$ccoPermitidos);
					foreach ($ccoPermitidos as $i => $value) {
						$ccoPermitidos[$i] = "'$value'";
					}
					$ccoPermitidos          = implode( ",", $ccoPermitidos );
					$ccoPerccd              = $rowRs['Perccd'];
					$ccoPermitidos          .= ",'{$ccoPerccd}'";
					$condicionCcoPermitidos = "Ccocod in ($ccoPermitidos) ";

					$q = " SELECT Ccocod,Cconom, Ccocod, Ccosei
							 FROM ".$wbasedato."_000011
						    WHERE {$condicionCcoPermitidos}
							ORDER by Cconom";

                    if ($sFiltrarSede == 'on' && $selectsede != '') {
                        $q = " SELECT Ccocod,Cconom, Ccocod, Ccosei
							 FROM ".$wbasedato."_000011
						    WHERE {$condicionCcoPermitidos} AND Ccosed = '".$selectsede."'
							ORDER by Cconom";
                    }
				}
			}

			if( !$limitacionAyudas ){
				$q = "( SELECT Ccocod, Cconom"
				."    FROM ".$wbasedato."_000011 G "
				."   WHERE Ccohos = 'on' ) "
				." UNION "
				."( SELECT Ccocod, Cconom"
				."    FROM ".$wbasedato."_000011 G "
				."   WHERE Ccoing = 'on' ) ORDER BY Cconom ";

                if ($sFiltrarSede == 'on' && $selectsede != '') {
                    $q = "( SELECT Ccocod, Cconom"
                        ."    FROM ".$wbasedato."_000011 G "
                        ."   WHERE Ccohos = 'on' AND Ccosed = '".$selectsede."' ) "
                        ." UNION "
                        ."( SELECT Ccocod, Cconom"
                        ."    FROM ".$wbasedato."_000011 G "
                        ."   WHERE Ccoing = 'on' AND Ccosed = '".$selectsede."' ) ORDER BY Cconom ";
                }
			}

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

			$solCamDoc =  solicitudesCambioDocumento();

			echo "<center><table border=0>";
			echo "<tr><td class=fila1 align=center><b>Fecha Inicial</b></td>";
			echo "<td class=fila1 align=center colspan=3>";
				echo "<input tipo='fecha' name='wfecha_i' id='wfecha_i' value='$wfecha_i'>";
			//campoFechaDefecto("wfecha_i", $wfecha_i);
			echo "</td></tr>";
			echo "<tr><td class=fila1 align=center><b>Fecha Final</b></td>";
			echo "<td class=fila1 align=center colspan=3>";
				echo "<input tipo='fecha' name='wfecha_f' id='wfecha_f' value='$wfecha_f'>";
			//campoFechaDefecto("wfecha_f", $wfecha_f);
			echo "</td></tr>";
			echo "<tr><td colspan=6 class=fila1 align=center><b> Servicio</b></td></tr>";
			echo "<tr><td colspan= 6 align =center class=fila1>";
			echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
            echo "<input type='HIDDEN' id='sede' name='selectsede' value='".$selectsede."'>";
			echo "<select name='wcco0' id='wcco0'>";
			echo "<option value ='todos'>todos</option>";

			for($i = 1; $i <= $num; $i++)
			{
			 $row = mysql_fetch_array($res);

			 if(isset($wcco0) && $row[0]==$wcco0)
				echo "<option selected value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
			 else
				echo "<option value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
			}

			echo "</select>";
			echo "</td></tr>";
			echo "<tr class='fila1'><td align='center' colspan='6'><b> Historia: </b><input type='text' name='whisconsultada' id='whisconsultada' value='{$whisconsultada}'></td></tr>";
			echo "<tr><td class = fila1  align= center><input type=radio name=tipo value=ingreso onclick='enter()' /> <b>Ingreso</b></td> <td class = fila1  align= center> <input type=radio name=tipo value=egreso onclick='enter()' /> <b>Egreso</b> </td><td class = fila1  align= center> <input type=radio name=tipo value=pendiente onclick='enter()' /> <b>Pendiente de alta</b> </td><td class = fila1 id='td_sol_cambio_documento' style='{$mostrarCambioDocumento}' numSolicitudes='{$solCamDoc}' align= center> <input type=radio name=tipo value=cambioDocumento onclick='enter()' /> <b>Cambio de documento</b> </td></tr>";
			echo "</table>";
			echo "</center>";
			echo "</form>";
			echo "<center><input type=button name ='btn_cerrar2' value='Cerrar Ventana' onclick='cerrarVentana()'></center>";

		}
		else
		{

			echo "<center>";
			echo "<table border=0>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Centro Costo </td>";
			echo "<td  class=fila1 align='left'>".$wcco0."</td>";
			echo "</tr>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Fecha Inicial </td>";
			echo "<td  class=fila1 align='left'>".$wfecha_i."</td>";
			echo "</tr>";
			echo "<tr class=encabezadoTabla	>";
			echo "<td align=center >Fecha Final </td>";
			echo "<td  class=fila1 align='left'>".$wfecha_f."</td>";
			echo "</tr>";
			echo "</table>";
			echo "</center>";
			echo "<br/>";

			$wcco1 = explode("-",$wcco0);
			$bandera=1;
			echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\", \"".$wcco1[0]."\",\"".$whisconsultada."\",\"".$selectsede."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
			echo "<br/>";
			if( $whisconsultada ){
				if( $tipo == "cambioDocumento" )
					consultarSolicitudesCambioDocumento();
				else
					ConsultaIngreso3($whisconsultada);
			}else{
				if( $tipo == "egreso" and trim( $whisconsultada ) == "" ){
						ConsultaEgreso2($wfecha_i,$wfecha_f,$wcco0);
				}else{
				    if  ( $tipo == "pendiente" and trim( $whisconsultada ) == "" ){
					    ConsultaPendiente2($wfecha_i,$wfecha_f,$wcco0);
				    }else if( $tipo == "cambioDocumento" and trim( $whisconsultada ) == "" ){
				    	consultarSolicitudesCambioDocumento();
				    }else{
				    	ConsultaIngreso2($wfecha_i,$wfecha_f,$wcco0);
				    }
				}
			}
			echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\", \"".$wcco1[0]."\",\"".$whisconsultada."\",\"".$selectsede."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";

        }
}

function consultarSolicitudesCambioDocumento(){

	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $wfecha_i;
	global $wfecha_f;
	global $wcliame;
	global $whisconsultada;

	$condicionBusqueda = ( $whisconsultada == "" ) ? "" : " cl288.Scdhis = '{$whisconsultada}' AND ";

	echo "<center class=titulo >SOLICITUDES DE CAMBIO DE DOCUMENTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</center><br>";echo "<br/>";
	/* consultar, historia, ingreso, documento anterior, nuevo documento, nombre del paciente */
	$query = " SELECT Scdhis historia, Scding ingreso, Scdtda tipoAnterior, Scddoa documentoAnterior, Scdtdn tipoNuevo, Scddon nuevoDocumento, concat( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) nombre, Scdjus justificacion
	             FROM {$wcliame}_000288 cl288
	            INNER JOIN
	            	  {$wcliame}_000100 cl100 on ( {$condicionBusqueda}  cl100.pachis = cl288.Scdhis AND cl288.Scdest = 'on' )";
	$res   = mysql_query( $query, $conex );
	$num   = mysql_num_rows( $res );
	if( $num > 0 ){
		echo "<br>";
		echo "<center>";
		echo "<table>";
			echo "<tr class='encabezadoTabla'>";
				echo "<td align='center'> HISTORIA </td>";
				echo "<td align='center'> INGRESO </td>";
				echo "<td align='center'> NOMBRE </td>";
				echo "<td align='center'> DOCUMENTO ACTUAL </td>";
				echo "<td align='center'> NUEVO TIPO <BR> DOCUMENTO</td>";
				echo "<td align='center'> NUEVO DOCUMENTO</td>";
				echo "<td align='center'> JUSTIFICACI&Oacute;N </td>";
				echo "<td align='center'> CAMBIAR <br> DOCUMENTO </td>";
			echo "</tr>";
			$i = 0;
			while( $row = mysql_fetch_array($res) ){
				$class = ( is_int( $i/2) ) ? "fila1" : "fila2";
				echo "<tr class='{$class}'>";
					echo "<td align='center'> {$row['historia']} </td>";
					echo "<td align='center'> {$row['ingreso']} </td>";
					echo "<td align='left'> {$row['nombre']} </td>";
					echo "<td align='center'> {$row['tipoAnterior']} - {$row['documentoAnterior']} </td>";
					echo "<td align='center'> {$row['tipoNuevo']} </td>";
					echo "<td align='center'> {$row['nuevoDocumento']} </td>";
					echo "<td align='left' width='150'> {$row['justificacion']} </td>";
					echo "<td align='center'> <input type='radio' name='rd_cambioDoc' onclick='abrirProgramaCambioDocumento( this, \"".$row['historia']."\", \"".$row['tipoNuevo']."\", \"".$row['nuevoDocumento']."\")' > </td>";
				echo "</tr>";
				$i++;
			}
		echo "</table>";
		echo "</center>";
	}else{

	}
}

function solicitudesCambioDocumento(){

	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $wcliame;
	$query = " SELECT Scdhis historia, Scding ingreso, Scdtda tipoAnterior, Scddoa documentoAnterior, Scdtdn tipoNuevo, Scddon nuevoDocumento, concat( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) nombre, Scdjus justificacion
	             FROM {$wcliame}_000288 cl288
	            INNER JOIN
	            	  {$wcliame}_000100 cl100 on ( cl100.pachis = cl288.Scdhis AND cl288.Scdest = 'on' )";
	$res   = mysql_query( $query, $conex );
	$num   = mysql_num_rows( $res );
	return( $num );
}

?>
<html>
<head>
<title>REPORTE PACIENTES EGRESADOS Y ACTIVOS</title>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
<style type="text/css" media="screen">
	.amarilloSuave{
		background-color: #F7D358;
	}
</style>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script> <!-- buscador -->
<script type='text/javascript'>
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

	$(document).ready(function(){
		$("#input_buscador").quicksearch("table[name='tablaRespuesta'] tbody tr[class^='fila']" );
		$("input[tipo='fecha']").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			maxDate:"+0D"
		});
		if( $("#td_sol_cambio_documento").attr("numSolicitudes")*1 > 0 ){
			setInterval(function(){
			  var el = $("#td_sol_cambio_documento");
			  if(el.hasClass('amarilloSuave')){
			      el.removeClass("amarilloSuave");
			  }else{
			      el.addClass("amarilloSuave");
			  }
			},700);
		}
	});

function retornar(wemp_pmla,wfecha_i,wfecha_f,bandera,wcco0, whistoria, sede){

		location.href = "rep_PacientesEgresadosActivos.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&bandera="+bandera+"&wcco0="+wcco0+"&whisconsultada="+whistoria+"&selectsede="+sede;
}


function cerrar_ventana(cant_inic){
		window.close();
}


function enter(){

	 document.historias.submit();
}

function Grabardaralta(datos){

    if(confirm("Realmente desea cambiar el alta del paciente"))
	{

		var wemp_pmla = $("#wemp_pmla").val();
		var sede = $("#sede").val();
		var vfila     = datos.split('-');
		var vcampo    = 'chkdaralta_'+vfila[3];

	    if (document.getElementById(vcampo).checked==true)
	        var wopcion = 'S';
	    else
	    	var wopcion = 'N';

		$.post("rep_PacientesEgresadosActivos.php",
		   {
		    consultaAjax:   true,
		    accion      :  'GrabarEgreso',
		    wemp_pmla   :   wemp_pmla,
            selectsede  :   sede,
		    wopcion     :   wopcion,
		    wdatos      :   datos
		   }, function(resultado){

	          var vresul  = resultado.split('-');

		   	  if (vresul[0]==1){

		   	  	 if (document.getElementById(vcampo).checked==true){
			   	  	 $("#refa_"+vresul[1]).hide();
			   	  	 $("#refb_"+vresul[1]).show();
			   	  	 $("#refc_"+vresul[1]).text($(".tipo_"+vresul[1]).text());
			   	     $(".tipo_"+vresul[1]).text('ALTA');
		   	     }
		   	     else{
		   	     	 $("#refa_"+vresul[1]).show();
			   	  	 $("#refb_"+vresul[1]).hide();
			   	  	 $(".tipo_"+vresul[1]).text($("#refc_"+vresul[1]).text());
		   	     }
		   	  }

		   });

	}
}

function abrirProgramaCambioDocumento( obj, historia, nuevoTD, nuevoDocumento ){

	$(obj).removeAttr("checked");
	wemp_pmla = $("#wemp_pmla").val();
	var path = "/matrix/hce/procesos/unificarHistorias.php?wemp_pmla="+wemp_pmla+"&cambioDocumento=on&historia1="+historia+"&nuevoTd="+nuevoTD+"&nuevoDocumento="+nuevoDocumento;
	window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
}

function ejecutar(path){
	window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
}

$(document).on('change','#selectsede',function(){
    window.location.href = "rep_PacientesEgresadosActivos.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val()
});

</script>
</head>
<body BGCOLOR="" TEXT="#000000">
<?php
	vistaInicial();
?>
</body>
</html>
