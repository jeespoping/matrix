<?php
    include_once("root/comun.php");

    function HC_e_ingresos_de_Egresos_automaticos(){
        global $conex;
        global $wcliame;
        $historias_ingresos = array();
    
        $query = "SELECT historia, ingreso FROM {$wcliame}_000343 WHERE egresado = 1 and ingresado is null";
		//$query = "SELECT historia, ingreso FROM {$wcliame}_000343 WHERE egresado = 1 and ingresado = 1";
        $res = mysql_query($query, $conex);
        while ($row = mysql_fetch_assoc($res)) {
            array_push($historias_ingresos, array($row['historia'], $row['ingreso']));
        }
        return $historias_ingresos;
    }

    function consultarColumnasTabla($tabla, $c_tabla){
        global $conex;
        $arrayColumnas = array();

        $query = "SHOW FIELDS FROM {$tabla}{$c_tabla}";
        $res = mysql_query($query, $conex);
        $num = mysql_num_rows($res);
        if ($num > 0){
            while( $info = mysql_fetch_array( $res ) ){
                array_push($arrayColumnas,$info['Field']); 
            }
        }
        return $arrayColumnas;
    }

    function obtenerDatosPaciente($historia, $ingreso){
        global $conex;
        global $wcliame;

        $query = "SELECT tipo_documento, documento_paciente, paciente 
				FROM {$wcliame}_000343 WHERE historia = {$historia} AND ingreso = {$ingreso}";
        $res = mysql_query($query, $conex);
        $row = mysql_fetch_assoc($res);

        return $row;

    }
	
	function actualizaEstado($historia, $ingreso){
        global $conex;
        global $wcliame;
		global $wmovhos;
		$ingresoAumentado = $ingreso + 1;
		$fecha = date('Y-m-d');
        $hora = date('H:i:s');

        $query = "UPDATE {$wcliame}_000100 SET `Pacact` = 'on' WHERE `Pachis` = '{$historia}'";
        $res1 = mysql_query($query, $conex);
		$query = "UPDATE {$wmovhos}_000018 SET `Ubiald` = 'off' 
			WHERE `Ubihis` = '{$historia}' AND `Ubiing` = '{$ingresoAumentado}' AND `ubiald` = 'on'";
        $res2 = mysql_query($query, $conex);
		$query = "UPDATE {$wmovhos}_000285 SET `Habing` = '{$ingresoAumentado}', Fecha_data = '{$fecha}', Hora_data = '{$hora}' 
			WHERE `Habhis` = '{$historia}' AND `Habing` = '{$ingreso}'";
        $res3 = mysql_query($query, $conex);

		return $res3;
    }

    function obtener_datos($historias_ingresos, $tabla, $c_tabla, $historia, $ingreso){
        global $conex;
        $historias = array();

        $query = "SELECT * FROM {$tabla}{$c_tabla} WHERE {$historia} = '{$historias_ingresos[0]}' AND {$ingreso} = '{$historias_ingresos[1]}'";
        $res = mysql_query($query, $conex);
        $num = mysql_num_rows($res);
        if ($num > 0){
            while ($row = mysql_fetch_assoc($res)) {
                array_push($historias, $row);
            }
        }

        return $historias;
    }

	function autoIncremento_movhos_000016($datos, $historiaIngreso){
        global $conex;
        global $wmovhos;
        $error = array();
        $consulta = consultarColumnasTabla($wmovhos, '_000016');
        array_pop($consulta);
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $columnasTabla = implode(', ', $consulta);

        if(count($datos) > 0){
			/* Funcion que verifica insersión en Unix */ 
			$ingresoEnUnix = InsertarDatosUnixInpac($historiaIngreso[0]);
			//$ingresoEnUnix = true;
			if (!isset($ingresoEnUnix['error'])) {
				$ingresoAumentado = $datos[0]['Inging'] + 1;
				$query = "INSERT INTO {$wmovhos}_000016 ({$columnasTabla})
						VALUES ('{$datos[0]['Medico']}', '{$fecha}', 
						'{$hora}', '{$datos[0]['Inghis']}', '{$ingresoAumentado}', 
						'{$datos[0]['Ingres']}', '{$datos[0]['Ingnre']}', '{$datos[0]['Ingtip']}', 
						'{$datos[0]['Ingtel']}', '{$datos[0]['Ingdir']}', '{$datos[0]['Ingmun']}', 
						'{$datos[0]['Seguridad']}')";
				$res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
				if (!$res) {
					$datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
					array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
					$result['finalizados'] = 0;
					$result['fallidos'] = $error[0];
					rollbackIngreso($historiaIngreso);
					return $result;
				}else{
					$datos = obtener_datos($historiaIngreso, $wmovhos, '_000018', 'Ubihis', 'Ubiing');
					$result = autoIncremento_movhos_000018($datos, $historiaIngreso);
					return $result;
				}
			}else{
				$datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
				$descripcion = $ingresoEnUnix['error'];
				array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
				$result['finalizados'] = 0;
				$result['fallidos'] = $error[0];
				return $result;
			}

        }else{
            $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
			array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => 'No existe ingreso indicado para duplicar.'));
			$result['finalizados'] = 0;
			$result['fallidos'] = $error[0];
			rollbackIngreso($historiaIngreso);
			return $result;
		}

    }

	function autoIncremento_movhos_000018($datos, $historiaIngreso){
        global $conex;
        global $wmovhos;
		global $wcliame;
        $error = array();
        $consulta = consultarColumnasTabla($wmovhos, '_000018');
        array_pop($consulta);
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $columnasTabla = implode(', ', $consulta);

        if(count($datos) > 0){
            $ingresoAumentado = $datos[0]['Ubiing'] + 1;
            $query = "INSERT INTO {$wmovhos}_000018 ({$columnasTabla})
                    VALUES ('{$datos[0]['Medico']}', '{$fecha}', 
                    '{$hora}', '{$datos[0]['Ubihis']}', '{$ingresoAumentado}', 
                    '{$datos[0]['Ubisac']}', '{$datos[0]['Ubisan']}', '{$datos[0]['Ubihac']}', 
                    '{$datos[0]['Ubihan']}', '{$datos[0]['Ubialp']}', '{$datos[0]['Ubiald']}', 
					'0000-00-00', '00:00:00', '0000-00-00', '00:00:00', '{$datos[0]['Ubiptr']}', 
					'{$datos[0]['Ubitmp']}', '{$datos[0]['Ubimue']}', '{$datos[0]['Ubiprg']}', 
					'0000-00-00', '00:00:00', '{$datos[0]['Ubihot']}', '{$datos[0]['Ubiuad']}', 
					'{$datos[0]['Ubiamd']}', '{$datos[0]['Ubijus']}', '{$datos[0]['Ubidie']}', 
					'{$datos[0]['Ubiste']}', '{$datos[0]['Seguridad']}')";
			$res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            if (!$res) {
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
                array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
                $result['finalizados'] = 0;
                $result['fallidos'] = $error[0];
                rollbackIngreso($historiaIngreso);
                return $result;
            }else{
                $datos = obtener_datos($historiaIngreso, $wcliame, '_000101', 'Inghis', 'Ingnin');
                $result = autoIncremento_historiasPor_Admitir($datos, $historiaIngreso);
                return $result;
            }
        }else{
            $datos = obtener_datos($historiaIngreso, $wcliame, '_000101', 'Inghis', 'Ingnin');
            $result = autoIncremento_historiasPor_Admitir($datos, $historiaIngreso);
            return $result;
        }

    }

    function autoIncremento_historiasPor_Admitir($datos, $historiaIngreso){
        global $conex;
        global $wcliame;
        $error = array();
        $result = array();
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $consulta = consultarColumnasTabla($wcliame, '_000101');
        array_pop($consulta);
        $columnasTabla = implode(', ', $consulta);

		if(count($datos) > 0){
			$ingresoAumentado = $datos[0]['Ingnin'] + 1;
			$datos[0]['Ingunx'] = "on";
			$query = "INSERT INTO {$wcliame}_000101 ({$columnasTabla})
					VALUES ('{$datos[0]['Medico']}', '{$fecha}', 
					'{$hora}', '{$datos[0]['Inghis']}', '{$ingresoAumentado}', 
					'{$fecha}', '{$hora}', '{$datos[0]['Ingsei']}', 
					'{$datos[0]['Ingtin']}', '{$datos[0]['Ingcai']}', '{$datos[0]['Ingtpa']}', 
					'{$datos[0]['Ingcem']}', '{$datos[0]['Ingent']}', '{$datos[0]['Ingord']}', 
					'{$datos[0]['Ingpol']}', '{$datos[0]['Ingnco']}', '{$datos[0]['Ingdie']}', 
					'{$datos[0]['Ingtee']}', '{$datos[0]['Ingtar']}', '{$datos[0]['Ingusu']}', 
					'{$datos[0]['Inglug']}', '{$datos[0]['Ingdig']}', '{$datos[0]['Ingdes']}', 
					'{$datos[0]['Ingpla']}', '{$fecha}', '{$datos[0]['Ingnpa']}', 
					'{$datos[0]['Ingcac']}', '{$datos[0]['Ingpco']}', '{$hora}', 
					'{$datos[0]['Ingcla']}', '{$datos[0]['Ingvre']}', '{$datos[0]['Ingunx']}', 
					'{$datos[0]['Ingmei']}', '{$datos[0]['Ingtut']}', '{$datos[0]['Ingniu']}', 
					'0000-00-00', '00:00:00', '{$datos[0]['Seguridad']}')";
			$res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
			if (!$res) {
				$datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
				array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
				$result['finalizados'] = 0;
				$result['fallidos'] = $error[0];
				rollbackIngreso($historiaIngreso);
				return $result;
			}else{
				$datos = obtener_datos($historiaIngreso, $wcliame, '_000205', 'Reshis', 'Resing');
				$result = autoIncremento_responsables($datos, $historiaIngreso);
				return $result;
			}
		}else{
            $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
			array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => 'No existe ingreso indicado para duplicar.'));
			$result['finalizados'] = 0;
			$result['fallidos'] = $error[0];
			rollbackIngreso($historiaIngreso);
			return $result;
        }

    }

    function autoIncremento_responsables($datos, $historiaIngreso){
        global $conex;
        global $wcliame;
        $error = array();
        $consulta = consultarColumnasTabla($wcliame, '_000205');
        array_pop($consulta);
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $columnasTabla = implode(', ', $consulta);
		$cantidad = count($datos);
		$contador = 0;
        if($cantidad > 0){
			for ($i=0; $i < $cantidad; $i++) { 
				$ingresoAumentado = $datos[$i]['Resing'] + 1;
		
				$query = "INSERT INTO {$wcliame}_000205 ({$columnasTabla})
						VALUES ('{$datos[$i]['Medico']}', '{$fecha}', 
						'{$hora}', '{$datos[$i]['Reshis']}', '{$ingresoAumentado}', 
						'{$datos[$i]['Restdo']}', '{$datos[$i]['Resnit']}', '{$datos[$i]['Resnom']}', 
						'{$datos[$i]['Resord']}', '{$fecha}', '0000-00-00', '{$datos[$i]['Resest']}', 
						'{$datos[$i]['Restpa']}', '{$datos[$i]['Respla']}', '{$datos[$i]['Respol']}', 
						'{$datos[$i]['Resnco']}', '{$datos[$i]['Resaut']}', '{$fecha}', '{$hora}', 
						'{$datos[$i]['Resnpa']}', '{$datos[$i]['Respco']}', '{$datos[$i]['Rescom']}', 
						'{$datos[$i]['Resdes']}', '{$datos[$i]['Seguridad']}')";
				$res = mysql_query($query,$conex)  or ($descripcion[$i] = utf8_encode("Error: " . mysql_error()));
				if (!$res) {
					$contador++;
				}
			}
			if ($contador>0) {
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
                array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion[0]));
                $result['finalizados'] = 0;
                $result['fallidos'] = $error[0];
                rollbackIngreso($historiaIngreso);
                return $result;
            }else{
                $datos = obtener_datos($historiaIngreso, $wcliame, '_000209', 'Cprhis', 'Cpring');
                $result = autoIncremento_CUPS($datos, $historiaIngreso);
                return $result;
            }
        }else{
            $datos = obtener_datos($historiaIngreso, $wcliame, '_000209', 'Cprhis', 'Cpring');
            $result = autoIncremento_CUPS($datos, $historiaIngreso);
            return $result;
        }

    }

    function autoIncremento_CUPS($datos, $historiaIngreso){
        global $conex;
        global $wcliame;
        $error = array();
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $consulta = consultarColumnasTabla($wcliame, '_000209');
        array_pop($consulta);
        $columnasTabla = implode(', ', $consulta);

        if(count($datos) > 0){
            $ingresoAumentado = $datos[0]['Cpring'] + 1;
    
            $query = "INSERT INTO {$wcliame}_000209 ({$columnasTabla})
                    VALUES ('{$datos[0]['Medico']}', '{$fecha}', 
                    '{$hora}', '{$datos[0]['Cprhis']}', '{$ingresoAumentado}', 
                    '{$datos[0]['Cprnit']}', '{$datos[0]['Cpraut']}', '{$datos[0]['Cprcup']}', 
                    '{$datos[0]['Cprest']}', '{$datos[0]['Seguridad']}')";
			$res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            if (!$res) {
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
                array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
                $result['finalizados'] = 0;
                $result['fallidos'] = $error[0];
                rollbackIngreso($historiaIngreso);
                return $result;
            }else{
                $datos = obtener_datos($historiaIngreso, $wcliame, '_000204', 'Tophis', 'Toping');
                $result = autoIncremento_topes($datos, $historiaIngreso);
                return $result;
            }
        }else{
            $datos = obtener_datos($historiaIngreso, $wcliame, '_000204', 'Tophis', 'Toping');
            $result = autoIncremento_topes($datos, $historiaIngreso);
            return $result;
        }

    }

    function autoIncremento_topes($datos, $historiaIngreso){
        global $conex;
        global $wcliame;
        $error = array();
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $consulta = consultarColumnasTabla($wcliame, '_000204');
        array_pop($consulta);
        $columnasTabla = implode(', ', $consulta);

        if(count($datos) > 0){
            $ingresoAumentado = $datos[0]['Toping'] + 1;
    
            $query = "INSERT INTO {$wcliame}_000204 ({$columnasTabla})
                    VALUES ('{$datos[0]['Medico']}', '{$fecha}', 
                    '{$hora}', '{$datos[0]['Tophis']}', '{$ingresoAumentado}', 
                    '{$datos[0]['Topres']}', '{$datos[0]['Toptco']}', '{$datos[0]['Topcla']}', 
                    '{$datos[0]['Topcco']}', '{$datos[0]['Toptop']}', '{$datos[0]['Toprec']}',
                    '{$datos[0]['Topdia']}', '{$datos[0]['Topsal']}', '{$datos[0]['Topest']}',
                    '0000-00-00', '{$datos[0]['Seguridad']}')";
			$res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            if (!$res) {
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
                array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
                $result['finalizados'] = 0;
                $result['fallidos'] = $error[0];
                rollbackIngreso($historiaIngreso);
                return $result;
            }else{
                $datos = obtener_datos($historiaIngreso, $wcliame, '_000148', 'Acchis', 'Accing');
                $result = autoIncremento_accidenteTransito($datos, $historiaIngreso);
                return $result;
            }
        }else{
            $datos = obtener_datos($historiaIngreso, $wcliame, '_000148', 'Acchis', 'Accing');
            $result = autoIncremento_accidenteTransito($datos, $historiaIngreso);
            return $result;
        }

    }

    function autoIncremento_accidenteTransito($datos, $historiaIngreso){
        global $conex;
        global $wcliame;
        $error = array();
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $consulta = consultarColumnasTabla($wcliame, '_000148');
        array_pop($consulta);
        $columnasTabla = implode(', ', $consulta);

        if(count($datos) > 0){
            $ingresoAumentado = $datos[0]['Accing'] + 1;
    
            $query = "INSERT INTO {$wcliame}_000148 ({$columnasTabla})
                    VALUES ('{$datos[0]['Medico']}', '{$fecha}', 
                    '{$hora}', '{$datos[0]['Acchis']}', '{$ingresoAumentado}', 
                    '{$datos[0]['Acccon']}', '{$datos[0]['Accdir']}', '{$datos[0]['Accdtd']}', 
                    '{$datos[0]['Accfec']}', '{$datos[0]['Acchor']}', '{$datos[0]['Accdep']}', 
                    '{$datos[0]['Accmun']}', '{$datos[0]['Acczon']}', '{$datos[0]['Accdes']}', 
                    '{$datos[0]['Accase']}', '{$datos[0]['Accmar']}', '{$datos[0]['Accpla']}', 
                    '{$datos[0]['Acctse']}', '{$datos[0]['Acccas']}', '{$datos[0]['Accpol']}', 
                    '{$datos[0]['Accvfi']}', '{$datos[0]['Accvff']}', '{$datos[0]['Accaut']}', 
                    '{$datos[0]['Acccep']}', '{$datos[0]['Accap1']}', '{$datos[0]['Accap2']}',
                    '{$datos[0]['Accno1']}', '{$datos[0]['Accno2']}', '{$datos[0]['Accnid']}', 
                    '{$datos[0]['Acctid']}', '{$datos[0]['Accpdi']}', '{$datos[0]['Accpdd']}',
                    '{$datos[0]['Accpdp']}', '{$datos[0]['Accpmn']}', '{$datos[0]['Acctel']}',
                    '{$datos[0]['Accca1']}', '{$datos[0]['Accca2']}', '{$datos[0]['Acccn1']}', 
                    '{$datos[0]['Acccn2']}', '{$datos[0]['Acccni']}', '{$datos[0]['Acccti']}', 
                    '{$datos[0]['Acccdi']}', '{$datos[0]['Acccdd']}', '{$datos[0]['Acccdp']}', 
                    '{$datos[0]['Acccmn']}', '{$datos[0]['Accctl']}', '{$datos[0]['Accest']}', 
                    '{$datos[0]['Accres']}', '{$datos[0]['Acctar']}', '{$datos[0]['Accre2']}', 
                    '{$datos[0]['Acctop']}', '{$datos[0]['Accvsm']}', '{$datos[0]['Accemp']}', 
                    '{$datos[0]['Accre3']}', '{$datos[0]['Accno3']}', '{$datos[0]['Accrei']}', 
                    '{$datos[0]['Acctpr']}', '{$datos[0]['Seguridad']}')";
			$res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            if (!$res) {
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
                array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
                $result['finalizados'] = 0;
                $result['fallidos'] = $error[0];
                rollbackIngreso($historiaIngreso);
                return $result;
            }else{
                $datos = obtener_datos($historias, $wcliame, '_000150', 'Evnhis', 'Evning');
                $result = autoIncremento_eventoCatastrofico($datos, $historiaIngreso);
				return $result;
            }
        }else{
            $datos = obtener_datos($historiaIngreso, $wcliame, '_000150', 'Evnhis', 'Evning');
            $result = autoIncremento_eventoCatastrofico($datos, $historiaIngreso);
            return $result;
        }

    }

    function autoIncremento_eventoCatastrofico($datos, $historiaIngreso){
        global $conex;
        global $wcliame;
        $error = array();
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $consulta = consultarColumnasTabla($wcliame, '_000150');
        array_pop($consulta);
        $columnasTabla = implode(', ', $consulta);
        if(count($datos) > 0){
            $ingresoAumentado = $datos[0]['Evning'] + 1;
    
            $query = "INSERT INTO {$wcliame}_000150 ({$columnasTabla})
                    VALUES ('{$datos[0]['Medico']}', '{$fecha}', 
                    '{$hora}', '{$datos[0]['Evnhis']}', '{$ingresoAumentado}', 
                    '{$datos[0]['Evncod']}', '{$datos[0]['Evnest']}', '{$datos[0]['Seguridad']}')";
            $res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            if (!$res) {
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
                array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
                $result['finalizados'] = 0;
                $result['fallidos'] = $error[0];
                rollbackIngreso($historiaIngreso);
                return $result;
            }else{
                $datos = obtener_datos($historiaIngreso, 'root', '_000037', 'Orihis', 'Oriing');
				$root_000037 = autoIncremento_root_000037($datos, $historiaIngreso);
				return $result;
            }
        }else{
            $datos = obtener_datos($historiaIngreso, 'root', '_000037', 'Orihis', 'Oriing');
			$result = autoIncremento_root_000037($datos, $historiaIngreso);
			return $result;
        }

    }

	function autoIncremento_root_000037($datos, $historiaIngreso){
        global $conex;
        global $wcliame;
		global $wmovhos;
		$wemp_pmla = $_REQUEST['wemp_pmla'];
        $error = array();
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
		$realizada = array();
        if(count($datos) > 0){
            $ingresoAumentado = $datos[0]['Oriing'] + 1;
            $query = "UPDATE root_000037 SET Fecha_data = '{$fecha}', Hora_data = '{$hora}', Oriing = '{$ingresoAumentado}', Oriori = '{$wemp_pmla}'
                    WHERE Orihis = '{$datos[0]['Orihis']}' AND Oriing = '{$datos[0]['Oriing']}' AND Oriori = '{$wemp_pmla}'";
			$res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            if (!$res) {
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
                array_push($error, array('historia' => $historiaIngreso[0], 'ingreso' => $historiaIngreso[1], 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => $descripcion));
                $result['finalizados'] = 0;
                $result['fallidos'] = $error[0];
                rollbackIngreso($historiaIngreso);
                return $result;
            }else{
				actualizar_log($historiaIngreso[0], $historiaIngreso[1]);
				actualizaEstado($historiaIngreso[0], $historiaIngreso[1]);
                $datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
				$ing = $historiaIngreso[1] + 1;
                array_push($realizada, array('historia' => $historiaIngreso[0], 'ingreso' => $ing, 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => 'Admisión exitosa'));
                $result['finalizados'] = $realizada[0];
                $result['fallidos'] = $error[0];
                return $result;
            }
        }else{
			actualizar_log($historiaIngreso[0], $historiaIngreso[1]);
			actualizaEstado($historiaIngreso[0], $historiaIngreso[1]);
			$datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
			$ing = $historiaIngreso[1] + 1;
            array_push($realizada, array('historia' => $historiaIngreso[0], 'ingreso' => $ing, 'tipo_documento' => $datosPaciente['tipo_documento'], 'documento' => $datosPaciente['documento_paciente'], 'paciente' => $datosPaciente['paciente'], 'descripcion' => 'Admisión exitosa'));
            $result['finalizados'] = $realizada[0];
            $result['fallidos'] = $error[0];
            return $result;
        }

    }

    function actualizar_log($historia, $ingreso){
        global $conex;
        global $wcliame;
        $query = "UPDATE {$wcliame}_000343 SET ingresado = 1 WHERE historia = {$historia} and ingreso = {$ingreso}";
        $res = mysql_query($query,$conex);
        return $res;
    }

    function rollbackIngreso($historiaIngreso){
        global $conex;
        global $wcliame;
		global $wmovhos;
		$wemp_pmla = $_REQUEST['wemp_pmla'];
        $ingreso = $historiaIngreso[1] + 1;
        $fecha = date('Y-m-d');

		$query = "UPDATE root_000037 SET Oriing = {$historiaIngreso[1]} WHERE Orihis = {$historiaIngreso[0]} AND Oriing = {$ingreso} AND Oriori = '{$wemp_pmla}'";
        $res6 = mysql_query($query,$conex);
		$query = "DELETE FROM {$wmovhos}_000016 WHERE Inghis = '{$historiaIngreso[0]}' and Inging = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res = mysql_query($query,$conex);
        $query = "DELETE FROM {$wmovhos}_000018 WHERE Ubihis = '{$historiaIngreso[0]}' and Ubiing = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res = mysql_query($query,$conex);
		$query = "DELETE FROM {$wcliame}_000101 WHERE Inghis = '{$historiaIngreso[0]}' and Ingnin = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res = mysql_query($query,$conex);
        $query = "DELETE FROM {$wcliame}_000205 WHERE Reshis = '{$historiaIngreso[0]}' and Resing = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res1 = mysql_query($query,$conex);
        $query = "DELETE FROM {$wcliame}_000209 WHERE Cprhis = '{$historiaIngreso[0]}' and Cpring = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res2 = mysql_query($query,$conex);
        $query = "DELETE FROM {$wcliame}_000204 WHERE Tophis = '{$historiaIngreso[0]}' and Toping = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res3 = mysql_query($query,$conex);
        $query = "DELETE FROM {$wcliame}_000148 WHERE Acchis = '{$historiaIngreso[0]}' and Accing = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res4 = mysql_query($query,$conex);
        $query = "DELETE FROM {$wcliame}_000150 WHERE Evnhis = '{$historiaIngreso[0]}' and Evning = '{$ingreso}' and Fecha_data = '{$fecha}'";
        $res5 = mysql_query($query,$conex);
        $query = "UPDATE {$wcliame}_000343 SET ingresado = NULL WHERE historia = {$historiaIngreso[0]} and ingreso = {$historiaIngreso[1]}";
        $res6 = mysql_query($query,$conex);
		$query = "UPDATE {$wmovhos}_000285 SET `Habing` = '{$historiaIngreso[1]}' WHERE `Habhis` = '{$historia}' AND `Habing` = '{$ingreso}'";
        $res7 = mysql_query($query, $conex);
		$query = "UPDATE {$wcliame}_000100 SET `Pacact` = 'off' WHERE `Pachis` = '{$historia}'";
        $res8 = mysql_query($query, $conex);
		return $res8;
    }

    /* FUNCIONES UNIX */

    function ping_unix(){
        global $conex;
        global $wemp_pmla;
    
        $ret = false;
    
        $direccion_ipunix = consultarAliasPorAplicacion($conex, $wemp_pmla, "ipdbunix" );
        if( $direccion_ipunix != "" ){
            $cmd_result = shell_exec("ping -c 1 -w 1 ". $direccion_ipunix);
            $result = explode(",",$cmd_result);
            // la función "eregi" ya está en desuso por eso se cambia a preg_match que es soportada en versiones posteriores de PHP
            // if(eregi("1 received", $result[1])){
            if(preg_match('/(1 received)/', $result[1])){
                $ret = true;
            }
        }
        return $ret;
    }

    function verificarConexionUnix(){
        global $conex;
        global $wemp_pmla;
        $tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
        $ping_unix = ping_unix();
		$res = false;

        if($tieneConexionUnix == 'on' && $ping_unix ){
            $res = true;
        }
		
        return $res;

    }

    function obtenerDatosDeMatrix($historiaIngreso){
        global $conex;
        global $wcliame;
        $historias = array();

        $query = "SELECT * FROM {$wcliame}_000101 WHERE Inghis = '{$historiaIngreso[0]}' AND Ingnin = '{$historiaIngreso[1][0]}'";
        $res = mysql_query($query, $conex);
        $num = mysql_num_rows($res);
        if ($num > 0){
            while ($row = mysql_fetch_assoc($res)) {
                array_push($historias, $row);
            }
        }

        return $historias[0];
    }

	function obtenerCod($historiaIngreso){
        global $conex;
        global $wmovhos;
        $result = array();

        $query = "SELECT Ubisac FROM {$wmovhos}_000018 WHERE Ubihis = '{$historiaIngreso[0]}' AND Ubiing = '{$historiaIngreso[1]}'";
        $res = mysql_query($query, $conex);
        $num = mysql_num_rows($res);
        if ($num > 0){
            while ($row = mysql_fetch_assoc($res)) {
                array_push($result, $row);
            }
        }

        return $result['Ubisac'];
    }

	function obtenerCodidoTipoUsuario($historiaIngreso){
        global $conex;
        global $wcliame;
        $result = array();

        $query = "SELECT Pactus FROM {$wcliame}_000100 WHERE Pachis = '{$historiaIngreso[0]}'";
        $res = mysql_query($query, $conex);
        $num = mysql_num_rows($res);
        if ($num > 0){
            while ($row = mysql_fetch_assoc($res)) {
                array_push($result, $row);
            }
        }

        return $result['Pactus'];
    }

	function obtenerPacser($cod){
        global $conex;
        global $wmovhos;
        $result = array();

        $query = "SELECT Ccoseu FROM {$wmovhos}_000011 WHERE Ccocod = '{$cod}' AND Ccoest = 'on'";
        $res = mysql_query($query, $conex);
        $num = mysql_num_rows($res);
        if ($num > 0){
            while ($row = mysql_fetch_assoc($res)) {
                array_push($result, $row);
            }
        }

        return $result['Ccoseu'];
    }

    function InsertarDatosUnixInpac($historia){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix == true) {
            $a = new admisiones_erp('traerDatosInpaci', $historia);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$ingresoAumentado = $dataU['data'][19] + 1;
				$historiaIngreso=array($historia, $dataU['data'][19]);
				$dataMatrix = obtenerDatosDeMatrix($historiaIngreso);
				$cod = obtenerCod($historiaIngreso);
				$pacser = obtenerPacser($cod);
				$pacase = obtenerCodidoTipoUsuario($historiaIngreso);
				
				$datos['pachis']= trim($historia);
				$datos['pacced']= trim($dataU['data'][1]);
				$datos['pactid']= trim($dataU['data'][2]);
				$datos['pacnui']= trim($dataU['data'][3]);
				$datos['pacap1']= trim($dataU['data'][4]);
				$datos['pacap2']= trim($dataU['data'][5]);
				$datos['pacnom']= trim($dataU['data'][6]);
				$datos['pacsex']= trim($dataU['data'][7]);
				$datos['paclug']= trim($dataU['data'][9]);
				$datos['pacnac']= trim($dataU['data'][8]);
				$datos['pacest']= trim($dataU['data'][10]);
				$datos['pacdir']= trim($dataU['data'][11]);
				$datos['pactel']= trim($dataU['data'][12]);
				$datos['paczon']= trim($dataU['data'][13]);
				$datos['pacmun']= trim($dataU['data'][14]);
				$datos['pactra']= trim($dataU['data'][15]);
				$datos['pacase']= trim($pacase);
				$datos['pacemp']= trim($dataMatrix['Ingtpa']);
				$datos['paccer']= trim($dataMatrix['Ingcem']); 
				$datos['pacres']= trim($dataMatrix['Ingent']); 
				$datos['pacdre']= trim($dataMatrix['Ingdie']); 
				$datos['pactre']= trim($dataMatrix['Ingtee']); 
				$datos['pactar']= trim($dataMatrix['Ingtar']); 
				$anno = date('Y');
				$datos['pacano']= trim($anno); 
				$mes = date('m');
				$datos['pacmes']= trim($mes); 
				$datos['pacnum']= trim($ingresoAumentado); 
				$datos['pacfec']= trim(date('Y-m-d')); 
				$hora = date('H.i');
				$datos['pachor']= trim($hora); 
				$datos['pachos']= trim($dataMatrix['Ingtin']); 
				$datos['pacser']= trim($pacser);
				$datos['pachab']= trim('');
				$datos['pactse']= trim('P');
				$datos['pacniv']= trim($dataU['data'][16]);
				$datos['pacrem']= trim('800067065-9');
				$datos['pacpad']= trim($dataU['data'][17]);
				$datos['pacmed']= trim($dataMatrix['Ingmei']);
				$datos['pacdin']= trim($dataMatrix['Ingdig']); 
				$datos['pacein']= ''; 
				$datos['paccin']= trim($dataMatrix['Ingcai']);
				empty($dataMatrix['Ingdig'])?$poliza = '.':$poliza = $dataMatrix['Ingdig'];
				$datos['pacpol']= trim($poliza);
				
				$_POST['datos'] = $datos;
				$a = new admisiones_erp('insertarEnInpac');
				$data['data'] = $a->data;
				
				if ($data['data'] == true) {
					$inacc = InsertarDatosUnixInacc($historia, $dataU['data'][19]);
					return $inacc;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpaci de Unix';
					return $error;
				}
			}else{
				$error['error'] = 'No hay información de paciente en la tabla Inpaci de Unix';
				return $error; 
			}

        }else{
			$error['error'] = 'No hay conexión a Unix';
			return $error; 
		}
			
    }
	
	function InsertarDatosUnixInacc($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInacc', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['acchis']= $historia;
				$datos['accnum']= $ingreso + 1;
				$datos['accfuo']= $dataU['data'][2];
				$datos['accdoo']= $dataU['data'][3];
				$datos['accacc']= $dataU['data'][4];
				$datos['accfec']= $dataU['data'][5];
				$datos['accind']= $dataU['data'][6];
				
				$_POST['datosInacc'] = $datos;
				$a = new admisiones_erp('insertarEnInacc');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inaccdet = InsertarDatosUnixInaccdet($historia, $ingreso);
					return $inaccdet;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inacc de Unix';
					return $error;
				}
			}else{
				$inaccdet = InsertarDatosUnixInaccdet($historia, $ingreso);
				return $inaccdet;
			}
        }
    }
	
	function InsertarDatosUnixInaccdet($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInaccdet', $historia, $ingreso);
            $dataU = $a->data;
 
			if(count($dataU['data'])){
				$datos['accdethis']= $historia;
				$datos['accdetnum']= $ingreso + 1;
				$datos['accdetacc']= $dataU['data'][2];
				$datos['accdetfec']= $dataU['data'][3];
				$datos['accdethor']= $dataU['data'][4];
				$datos['accdetmun']= $dataU['data'][5];
				$datos['accdetzon']= $dataU['data'][6];
				$datos['accdetlug']= $dataU['data'][7];
				$datos['accdetocu']= $dataU['data'][8];
				$datos['accdetase']= $dataU['data'][9];
				$datos['accdetasn']= $dataU['data'][10];
				$datos['accdetpol']= $dataU['data'][11];
				$datos['accdetsuc']= $dataU['data'][12];
				$datos['accdetpof']= $dataU['data'][13];
				$datos['accdetfin']= $dataU['data'][14];
				$datos['accdetffi']= $dataU['data'][15];
				$datos['accdetmar']= $dataU['data'][16];
				$datos['accdetpla']= $dataU['data'][17];
				$datos['accdettip']= $dataU['data'][18];
				$datos['accdetnom']= $dataU['data'][19];
				$datos['accdetced']= $dataU['data'][20];
				$datos['accdetmuc']= $dataU['data'][21];
				$datos['accdetdir']= $dataU['data'][22];
				$datos['accdettel']= $dataU['data'][23];
				$datos['accdetdof']= $dataU['data'][24];
				$datos['accdettof']= $dataU['data'][25];
				$datos['accdetres']= $dataU['data'][26];
				$datos['accdettar']= $dataU['data'][27];
				$datos['accdettop']= $dataU['data'][28];
				$datos['accdetvsm']= $dataU['data'][29];
				$datos['accdetob1']= $dataU['data'][30];
				$datos['accdetob2']= $dataU['data'][31];
				$datos['accdetre2']= $dataU['data'][32];
				$datos['accdetemp']= $dataU['data'][33];
				$datos['accdetre3']= $dataU['data'][34];
				$datos['accdetno3']= $dataU['data'][35];
				$datos['accdetaut']= $dataU['data'][36];
				$datos['accdetcas']= $dataU['data'][37];
				$datos['accdetma2']= $dataU['data'][38];
				$datos['accdetpl2']= $dataU['data'][39];
				$datos['accdetti2']= $dataU['data'][40];
				$datos['accdetma3']= $dataU['data'][41];
				$datos['accdetpl3']= $dataU['data'][42];
				$datos['accdetti3']= $dataU['data'][43];
				
				$_POST['datosInaccdet'] = $datos;
				$a = new admisiones_erp('insertarEnInaccdet');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inaccpro = InsertarDatosUnixInaccpro($historia, $ingreso);
					return $inaccpro;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inaccdet de Unix';
					return $error;
				}
			}else{
				$inaccpro = InsertarDatosUnixInaccpro($historia, $ingreso);
				return $inaccpro;
			}
        }
    }
	
	function InsertarDatosUnixInaccpro($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInaccpro', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['accprohis']= $historia;
				$datos['accpronum']= $ingreso + 1;
				$datos['accproacc']= $dataU['data'][2];
				$datos['accproap1']= $dataU['data'][3];
				$datos['accproap2']= $dataU['data'][4];
				$datos['accprono1']= $dataU['data'][5];
				$datos['accprono2']= $dataU['data'][6];
				$datos['accprotid']= $dataU['data'][7];
				$datos['accproide']= $dataU['data'][8];
				$datos['accprodep']= $dataU['data'][9];
				$datos['accpromun']= $dataU['data'][10];
				$datos['accprodir']= $dataU['data'][11];
				$datos['accprotel']= $dataU['data'][12];
				$datos['accproac1']= $dataU['data'][13];
				$datos['accproac2']= $dataU['data'][14];
				$datos['accpronc1']= $dataU['data'][15];
				$datos['accpronc2']= $dataU['data'][16];
				$datos['accprotic']= $dataU['data'][17];
				$datos['accproti2']= $dataU['data'][18];
				$datos['accproid2']= $dataU['data'][19];
				$datos['accproae2']= $dataU['data'][20];
				$datos['accproal2']= $dataU['data'][21];
				$datos['accpronm2']= $dataU['data'][22];
				$datos['accpronb2']= $dataU['data'][23];
				$datos['accproti3']= $dataU['data'][24];
				$datos['accproid3']= $dataU['data'][25];
				$datos['accproae3']= $dataU['data'][26];
				$datos['accproal3']= $dataU['data'][27];
				$datos['accpronm3']= $dataU['data'][28];
				$datos['accpronb3']= $dataU['data'][29];
				
				$_POST['datosInaccpro'] = $datos;
				$a = new admisiones_erp('insertarEnInaccpro');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inaccobs = InsertarDatosUnixInaccobs($historia, $ingreso);
					return $inaccobs;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inaccpro de Unix';
					return $error;
				}
			}else{
				$inaccobs = InsertarDatosUnixInaccobs($historia, $ingreso);
				return $inaccobs;
			}
        }
    }
	
	function InsertarDatosUnixInaccobs($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInaccobs', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['accobshis']= $historia;
				$datos['accobsnum']= $ingreso + 1;
				$datos['accobsacc']= $dataU['data'][2];
				$datos['accobslin']= $dataU['data'][3] + 1;
				$datos['accobsobs']= $dataU['data'][4];
				
				$_POST['datosInaccobs'] = $datos;
				$a = new admisiones_erp('insertarEnInaccobs');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inpacevc = InsertarDatosUnixInpacevc($historia, $ingreso);
					return $inpacevc;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inaccobs de Unix';
					return $error;
				}
			}else{
				$inpacevc = InsertarDatosUnixInpacevc($historia, $ingreso);
				return $inpacevc;
			}
        }
    }
	
	function InsertarDatosUnixInpacevc($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInpacevc', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['pacevchis']= $historia;
				$datos['pacevcnum']= $ingreso + 1;
				$datos['pacevcfuo']= $dataU['data'][2];
				$datos['pacevcdoo']= $dataU['data'][3];
				$datos['pacevcevc']= $dataU['data'][4];
				
				$_POST['datosInpacevc'] = $datos;
				$a = new admisiones_erp('insertarEnInpacevc');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inpacinf = InsertarDatosUnixInpacinf($historia, $ingreso);
					return $inpacinf;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpacevc de Unix';
					return $error;
				}
			}else{
				$inpacinf = InsertarDatosUnixInpacinf($historia, $ingreso);
				return $inpacinf;
			}
        }
    }
	
	function InsertarDatosUnixInpacinf($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInpacinf', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['pacinfhis']= $historia;
				$datos['pacinfnum']= $ingreso + 1;
				$datos['pacinfcoc']= $dataU['data'][2];
				$datos['pacinfocu']= $dataU['data'][3];
				$datos['pacinftel']= $dataU['data'][4];
				$datos['pacinfavi']= $dataU['data'][5];
				$datos['pacinftav']= $dataU['data'][6];
				$datos['pacinfdav']= $dataU['data'][7];
				$datos['pacinfpar']= $dataU['data'][8];
				$datos['pacinfcon']= $dataU['data'][9];
				$datos['pacinfoco']= $dataU['data'][10];
				$datos['pacinfeco']= $dataU['data'][11];
				$datos['pacinfval']= $dataU['data'][12];
				$datos['pacinftip']= $dataU['data'][13];
				$datos['pacinfate']= $dataU['data'][14];
				$datos['pacinfsec']= $dataU['data'][15];
				$datos['pacinfmed']= $dataU['data'][16];
				$datos['pacinfesp']= $dataU['data'][17];
				$datos['pacinfnac']= $dataU['data'][18];
				$datos['pacinfmai']= $dataU['data'][19];
				$datos['pacinfrem']= $dataU['data'][20];
				$datos['pacinfree']= $dataU['data'][21];
				$datos['pacinfene']= $dataU['data'][22];
				$datos['pacinfrei']= $dataU['data'][23];
				$datos['pacinfser']= $dataU['data'][24];
				$datos['pacinfpro']= $dataU['data'][25];
				$datos['pacinfuad']= $dataU['data'][26];
				$datos['pacinffad']= $dataU['data'][27];
				$datos['pacinfumo']= $dataU['data'][28];
				$datos['pacinffmo']= $dataU['data'][29];
				$datos['pacinfpre']= $dataU['data'][30];
				$datos['pacinfcre']= $dataU['data'][31];
				$datos['pacinffre']= $dataU['data'][32];
				$datos['pacinfose']= $dataU['data'][33];
				$datos['pacinfprr']= $dataU['data'][34];
				$datos['pacinfcar']= $dataU['data'][35];
				$datos['pacinffea']= $dataU['data'][36];
				$datos['pacinfcin']= $dataU['data'][37];
				$datos['pacinfhre']= $dataU['data'][38];
				$datos['pacinfher']= $dataU['data'][39];

				$_POST['datosInpacinf'] = $datos;
				$a = new admisiones_erp('insertarEnInpacinf');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inpacusu = InsertarDatosUnixInpacusu($historia, $ingreso);
					return $inpacusu;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpacinf de Unix';
					return $error;
				}
			}else{
				$inpacusu = InsertarDatosUnixInpacusu($historia, $ingreso);
				return $inpacusu;
			}
        }
    }
	
	function InsertarDatosUnixInpacusu($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInpacusu', $historia, $ingreso);
            $dataU = $a->data;
			$fecha = date('Y/m/d');
        	$hora = date('H:i:s');
			if(count($dataU['data'])){
				$datos['pacusuhis']= $historia;
				$datos['pacusunum']= $ingreso + 1;
				$datos['pacusufec']= $fecha;
				$datos['pacusuhor']= $hora;
				$datos['pacusuusu']= $dataU['data'][4];
				
				$_POST['datosInpacusu'] = $datos;
				$a = new admisiones_erp('insertarEnInpacusu');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inpacotr = InsertarDatosUnixInpacotr($historia, $ingreso);
					return $inpacotr;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpacusu de Unix';
					return $error;
				}
			}else{
				$inpacotr = InsertarDatosUnixInpacotr($historia, $ingreso);
				return $inpacotr;
			}
        }
    }
	
	function InsertarDatosUnixInpacotr($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInpacotr', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['pacotrhis']= $historia;
				$datos['pacotrnum']= $ingreso + 1;
				$datos['pacotrbar']= $dataU['data'][2];
				$datos['pacotrest']= $dataU['data'][3];
				$datos['pacotrcea']= $dataU['data'][4];
				$datos['pacotrnoa']= $dataU['data'][5];
				$datos['pacotrdra']= $dataU['data'][6];
				$datos['pacotrtra']= $dataU['data'][7];
				$datos['pacotrciu']= $dataU['data'][8];
				$datos['pacotrpar']= $dataU['data'][9];
				$datos['pacotrdro']= $dataU['data'][10];
				$datos['pacotrtro']= $dataU['data'][11];
				$datos['pacotrlle']= $dataU['data'][12];
				$datos['pacotrppm']= $dataU['data'][13];
				$datos['pacotrmed']= $dataU['data'][14];
				
				$_POST['datosInpacotr'] = $datos;
				$a = new admisiones_erp('insertarEnInpacotr');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inpacmre = InsertarDatosUnixInpacmre($historia, $ingreso);
					return $inpacmre;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpacotr de Unix';
					return $error;
				}
			}else{
				$inpacmre = InsertarDatosUnixInpacmre($historia, $ingreso);
				return $inpacmre;
			}
        }
    }
	
	function InsertarDatosUnixInpacmre($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInpacmre', $historia, $ingreso);
            $dataU = $a->data;
			$fecha = date('Y/m/d');
        	$hora = date('H:i');
			if(count($dataU['data'])){
				$datos['pacmrehis']= $historia;
				$datos['pacmrenum']= $ingreso + 1;
				$datos['pacmreind']= $dataU['data'][2];
				$datos['pacmreemp']= $dataU['data'][3];
				$datos['pacmrecer']= $dataU['data'][4];
				$datos['pacmreres']= $dataU['data'][5];
				$datos['pacmrepla']= $dataU['data'][6];
				$datos['pacmretar']= $dataU['data'][7];
				$datos['pacmredir']= $dataU['data'][8];
				$datos['pacmretel']= $dataU['data'][9];
				$datos['pacmrectr']= $dataU['data'][10];
				$datos['pacmrefin']= $fecha;
				$datos['pacmrehin']= $hora;
				$datos['pacmreffi']= $fecha;
				$datos['pacmrehfi']= $hora;
				$datos['pacmredes']= $dataU['data'][15];
				
				$_POST['datosInpacmre'] = $datos;
				$a = new admisiones_erp('insertarEnInpacmre');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$msate = InsertarDatosUnixMsate($historia, $ingreso);
					return $msate;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpacmre de Unix';
					return $error;
				}
			}else{
				$msate = InsertarDatosUnixMsate($historia, $ingreso);
				return $msate;
			}
        }
    }
	
	function InsertarDatosUnixMsate($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosMsate', $historia, $ingreso);
            $dataU = $a->data;
			$fecha = date('Y/m/d');
        	$hora = date('H:i');
			$fechaCompleta = date('Y-m-d H:i');
			if(count($dataU['data'])){
				$datos['ateips']= $dataU['data'][0];
				$datos['atedoc']= ''; // Se trae dato en erp_unix.php, mediante funcion consultarConsecutivoRIPS
				$datos['ateemp']= $dataU['data'][2];
				$datos['atetif']= $dataU['data'][3];
				$datos['atehis']= $historia;
				$datos['ateing']= $ingreso + 1;
				$datos['atefue']= $dataU['data'][6];
				$datos['atedto']= $dataU['data'][7];
				$datos['atefso']= $fecha;
				$datos['atehso']= $hora;
				$datos['ateaut']= $dataU['data'][10];
				$datos['ateafi']= $dataU['data'][11];
				$datos['atepai']= $dataU['data'][12];
				$datos['atenpa']= $dataU['data'][13];
				$datos['ateocu']= $dataU['data'][14];
				$datos['ategpo']= $dataU['data'][15];
				$datos['ateest']= $dataU['data'][16];
				$datos['atefhe']= $fechaCompleta;
				$datos['ateuse']= $dataU['data'][18];
				$datos['atedil']= $dataU['data'][19];
				$datos['atefhg']= $fechaCompleta;
				$datos['ateusu']= $dataU['data'][21];
				
				$_POST['datosMsate'] = $datos;
				$a = new admisiones_erp('insertarEnMsate');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$msateid = InsertarDatosUnixMsateid($historia, $ingreso);
					return $msateid;
				}else{
					$error['error'] = 'Error al insertar en la tabla Msate de Unix';
					return $error;
				}
			}else{
				$msateid = InsertarDatosUnixMsateid($historia, $ingreso);
				return $msateid;
			}
        }
    }
	
	function InsertarDatosUnixMsateid($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
			$b = new admisiones_erp('traerDatosMsate', $historia, $ingreso);
            $data = $b->data;
			if(count($data['data'])){
				$_POST['ips'] = $data['data'][0];
				$_POST['doc'] = $data['data'][1];
				$a = new admisiones_erp('traerDatosMsateid');
				$dataU = $a->data;
				if(count($dataU['data'])){
					$ingresoAumentado = $ingreso + 1;
					$doc = new admisiones_erp('traerDatosMsate', $historia, $ingresoAumentado);
					$atedoc = $doc->data;
					$datos['ateideips']= $dataU['data'][0];
					$datos['ateidedoc']= $atedoc['data'][1];
					$datos['ateidetii']= $dataU['data'][2];
					$datos['ateideide']= $dataU['data'][3];
					$datos['ateideap1']= $dataU['data'][5];
					$datos['ateideap2']= $dataU['data'][6];
					$datos['ateideno1']= $dataU['data'][7];
					$datos['ateideno2']= $dataU['data'][8];
					$datos['ateidenac']= $dataU['data'][9];
					$datos['ateidesex']= $dataU['data'][10];
					$datos['ateidedir']= $dataU['data'][11];
					$datos['ateidetel']= $dataU['data'][12];
					$datos['ateidezon']= $dataU['data'][13];
					$datos['ateidebar']= $dataU['data'][14];
					$datos['ateidemun']= $dataU['data'][15];
					$datos['ateidetus']= $dataU['data'][16];
					$datos['ateideniv']= $dataU['data'][17];
					$datos['ateidecus']= $dataU['data'][18];
					$datos['ateidetdi']= $dataU['data'][19];
					$datos['ateidegdi']= $dataU['data'][20];

					$_POST['datosMsateid'] = $datos;
					$a = new admisiones_erp('insertarEnMsateid');
					$dataU = $a->data;

					if ($dataU['data'] == true) {
						$inpacars = InsertarDatosUnixMsurg($historia, $ingreso);
						return $inpacars;
					}else{
						$error['error'] = 'Error al insertar en la tabla Msateid de Unix';
						return $error;
					}
				}else{
					$inpacars = InsertarDatosUnixMsurg($historia, $ingreso);
					return $inpacars;
				}
			}else{
				$inpacars = InsertarDatosUnixMsurg($historia, $ingreso);
				return $inpacars;
			}
        }
    }

	function InsertarDatosUnixMsurg($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
			$b = new admisiones_erp('traerDatosMsate', $historia, $ingreso);
            $data = $b->data;
			if(count($data['data'])){
				$_POST['ips'] = $data['data'][0];
				$_POST['doc'] = $data['data'][1];
				$a = new admisiones_erp('traerDatosMsurg');
				$dataU = $a->data;
				if(count($dataU['data'])){
					$ingresoAumentado = $ingreso + 1;
					$doc = new admisiones_erp('traerDatosMsate', $historia, $ingresoAumentado);
					$atedoc = $doc->data;
					$fecha = date('Y/m/d');
					$hora = date('H:i');
					$fechaCompleta = date('Y-m-d H:i');
					$datos['urgips']= $dataU['data'][0];
					$datos['urgdoc']= $atedoc['data'][1];
					$datos['urgaut']= $dataU['data'][2];
					$datos['urgfin']= $fecha;
					$datos['urghin']= $hora;
					$datos['urgfsa']= $fecha;
					$datos['urghsa']= $hora;
					$datos['urghob']= $dataU['data'][7];
					$datos['urgest']= $dataU['data'][8];
					$datos['urgtdi']= $dataU['data'][9];
					$datos['urggdi']= $dataU['data'][10];
					$datos['urgcus']= $dataU['data'][11];
					$datos['urgext']= $dataU['data'][12];
					$datos['urgdin']= $dataU['data'][13];
					$datos['urgalt']= $dataU['data'][14];
					$datos['urgrni']= $dataU['data'][15];
					$datos['urghos']= $dataU['data'][16];
					$datos['urgdxi']= $dataU['data'][17];
					$datos['urgdxe']= $dataU['data'][18];
					$datos['urgcmu']= $dataU['data'][19];
					$datos['urgdx1']= $dataU['data'][20];
					$datos['urgdx2']= $dataU['data'][21];
					$datos['urgdx3']= $dataU['data'][22];
					$datos['urgmed']= $dataU['data'][23];
					$datos['urgser']= $dataU['data'][24];
					$datos['urgcco']= $dataU['data'][25];
					$datos['urgpen']= $dataU['data'][26];
					$datos['urgfhg']= $fechaCompleta;
					$datos['urgusu']= $dataU['data'][28];
					$datos['urgrem']= $dataU['data'][29];
					$datos['urgent']= $dataU['data'][30];
				
					$_POST['datosMsurg'] = $datos;
					$a = new admisiones_erp('insertarEnMsurg');
					$dataU = $a->data;
					if ($dataU['data'] == true) {
						$inpacars = InsertarDatosUnixMshos($historia, $ingreso);
						return $inpacars;
					}else{
						$error['error'] = 'Error al insertar en la tabla Msurg de Unix';
						return $error;
					}
				}else{
					$inpacars = InsertarDatosUnixMshos($historia, $ingreso);
					return $inpacars;
				}
			}else{
				$inpacars = InsertarDatosUnixMshos($historia, $ingreso);
				return $inpacars;
			}
        }
    }

	function InsertarDatosUnixMshos($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
			$b = new admisiones_erp('traerDatosMsate', $historia, $ingreso);
            $data = $b->data;
			if(count($data['data'])){
				$_POST['ips'] = $data['data'][0];
				$_POST['doc'] = $data['data'][1];
				$a = new admisiones_erp('traerDatosMshos');
				$dataU = $a->data;
				if(count($dataU['data'])){
					$ingresoAumentado = $ingreso + 1;
					$doc = new admisiones_erp('traerDatosMsate', $historia, $ingresoAumentado);
					$atedoc = $doc->data;
					$fecha = date('Y/m/d');
					$hora = date('H:i');
					$fechaCompleta = date('Y-m-d H:i');
					$datos['hosips']= $dataU['data'][0];
					$datos['hosdoc']= $atedoc['data'][1];
					$datos['hosaut']= $dataU['data'][2];
					$datos['hosvia']= $dataU['data'][3];
					$datos['hosfin']= $fecha;
					$datos['hoshin']= $hora;
					$datos['hosfeg']= $fecha;
					$datos['hosheg']= $hora;
					$datos['hosdes']= $dataU['data'][8];
					$datos['hosest']= $dataU['data'][9];
					$datos['hoscus']= $dataU['data'][10];
					$datos['hoscex']= $dataU['data'][11];
					$datos['hosdii']= $dataU['data'][12];
					$datos['hostdi']= $dataU['data'][13];
					$datos['hosgdi']= $dataU['data'][14];
					$datos['hosdxi']= $dataU['data'][15];
					$datos['hosdxe']= $dataU['data'][16];
					$datos['hoscom']= $dataU['data'][17];
					$datos['hosdxm']= $dataU['data'][18];
					$datos['hosdxf']= $dataU['data'][19];
					$datos['hosdx1']= $dataU['data'][20];
					$datos['hosdx2']= $dataU['data'][21];
					$datos['hosdx3']= $dataU['data'][22];
					$datos['hosmed']= $dataU['data'][23];
					$datos['hosser']= $dataU['data'][24];
					$datos['hoscco']= $dataU['data'][25];
					$datos['hospen']= $dataU['data'][26];
					$datos['hosfhg']= $fechaCompleta;
					$datos['hosusu']= $dataU['data'][28];				
					
					$_POST['datosMshos'] = $datos;
					$a = new admisiones_erp('insertarEnMshos');
					$dataU = $a->data;
					if ($dataU['data'] == true) {
						$inpacars = InsertarDatosUnixInpacars($historia, $ingreso);
						return $inpacars;
					}else{
						$error['error'] = 'Error al insertar en la tabla Mshos de Unix';
						return $error;
					}
				}else{
					$inpacars = InsertarDatosUnixInpacars($historia, $ingreso);
					return $inpacars;
				}
			}else{
				$inpacars = InsertarDatosUnixInpacars($historia, $ingreso);
				return $inpacars;
			}
        }
    }
	
	function InsertarDatosUnixInpacars($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInpacars', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['pacarshis']= $historia;
				$datos['pacarsnum']= $ingreso + 1;
				$datos['pacarsafi']= $dataU['data'][2];
				$datos['pacarsreg']= $dataU['data'][3];
				$datos['pacarsadm']= $dataU['data'][4];
				$datos['pacarsars']= $dataU['data'][5];
				$datos['pacarscar']= $dataU['data'][6];
				$datos['pacarsgru']= $dataU['data'][7];
				
				$_POST['datosInpacars'] = $datos;
				$a = new admisiones_erp('insertarEnInpacars');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inurg = InsertarDatosUnixInurg($historia, $ingreso);
					return $inurg;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpacars de Unix';
					return $error;
				}
			}else{
				$inurg = InsertarDatosUnixInurg($historia, $ingreso);
				return $inurg;
			}
        }
    }
	
	function InsertarDatosUnixInurg($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
            $a = new admisiones_erp('traerDatosInurg', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['urghis']= $historia;
				$datos['urgnum']= $ingreso + 1;
				$datos['urgfec']= $dataU['data'][2];
				$datos['urghor']= $dataU['data'][3];
				$datos['urglug']= $dataU['data'][4];
				
				$_POST['datosInurg'] = $datos;
				$a = new admisiones_erp('insertarEnInurg');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inpacord = InsertarDatosUnixInpacord($historia, $ingreso);
					return $inpacord;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inurg de Unix';
					return $error;
				}
			}else{
				$inpacord = InsertarDatosUnixInpacord($historia, $ingreso);
				return $inpacord;
			}
        }
    }
	
	function InsertarDatosUnixInpacord($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
			$a = new admisiones_erp('traerDatosInpacord', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['pacordhis']= $dataU['data'][0];
				$datos['pacordnum']= $dataU['data'][1] + 1; 
				$datos['pacordord']= $dataU['data'][2];
				$datos['pacordest']= $dataU['data'][3];
				$datos['pacordfec']= $dataU['data'][4];
				$datos['pacordven']= $dataU['data'][5];
				$datos['pacordde1']= $dataU['data'][6];
				$datos['pacordde2']= $dataU['data'][7];
				$datos['pacordpco']= $dataU['data'][8];
				$datos['pacordtco']= $dataU['data'][9];
				$datos['pacordpca']= $dataU['data'][10];
				$datos['pacordfci']= $dataU['data'][11];
				$datos['pacordfcf']= $dataU['data'][12];
				$datos['pacordhin']= $dataU['data'][13];
				$datos['pacordcac']= $dataU['data'][14];
				$datos['pacordser']= $dataU['data'][15];
				$datos['pacordtut']= $dataU['data'][16];
				$datos['pacordant']= $dataU['data'][17];
				$datos['pacordcia']= $dataU['data'][18];
				$datos['pacordsem']= $dataU['data'][19];
				$datos['pacordsed']= $dataU['data'][20];
				
				$_POST['datosInpacord'] = $datos;
				$a = new admisiones_erp('insertarEnInpacord');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$inorddet = InsertarDatosUnixInorddet($historia, $ingreso);
					return $inorddet;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inpacord de Unix';
					return $error;
				}
			}else{
				$inorddet = InsertarDatosUnixInorddet($historia, $ingreso);
				return $inorddet;
			}
        }
    }
	
	function InsertarDatosUnixInorddet($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
			$a = new admisiones_erp('traerDatosInorddet', $historia, $ingreso);
            $dataU = $a->data;
			if(count($dataU['data'])){
				$datos['orddethis']= $dataU['data'][0];
				$datos['orddetnum']= $dataU['data'][1] + 1; 
				$datos['orddetord']= $dataU['data'][2];
				$datos['orddetind']= $dataU['data'][3];
				$datos['orddettip']= $dataU['data'][4];
				$datos['orddetcla']= $dataU['data'][5];
				$datos['orddettho']= $dataU['data'][6];
				$datos['orddetdia']= $dataU['data'][7];
				$datos['orddetcer']= $dataU['data'][8];
				$datos['orddetde1']= $dataU['data'][9];
				$datos['orddetde2']= $dataU['data'][10];
				$datos['orddetfec']= $dataU['data'][11];
				$datos['orddetfve']= $dataU['data'][12];
				$datos['orddetpco']= $dataU['data'][13];
				$datos['orddettco']= $dataU['data'][14];
				$datos['orddetpca']= $dataU['data'][15];
				$datos['orddetfci']= $dataU['data'][16];
				$datos['orddetfcf']= $dataU['data'][17];
				$datos['orddetest']= $dataU['data'][18];
				$datos['orddetusu']= $dataU['data'][19];
				$datos['orddetcod']= $dataU['data'][20];
				$datos['orddetpex']= $dataU['data'][21];
				$datos['orddetvch']= $dataU['data'][22];
				$datos['orddetmau']= $dataU['data'][23];
				$datos['orddetduc']= $dataU['data'][24];
				$datos['orddettha']= $dataU['data'][25];
				$datos['orddethin']= $dataU['data'][26];
				$datos['orddetcac']= $dataU['data'][27];
				$datos['orddetser']= $dataU['data'][28];
				$datos['orddettut']= $dataU['data'][29];
				$datos['orddetant']= $dataU['data'][30];
				$datos['orddetobs']= $dataU['data'][31];
				$datos['orddetrpr']= $dataU['data'][32];
				$datos['orddetrpo']= $dataU['data'][33];
				$datos['orddetrvl']= $dataU['data'][34];
				$datos['orddetaut']= $dataU['data'][35];
				$datos['orddetcar']= $dataU['data'][36];
				$datos['orddettau']= $dataU['data'][37];
				$datos['orddetcau']= $dataU['data'][38];
				$datos['orddetsec']= $dataU['data'][39];
				$datos['orddetint']= $dataU['data'][40];
				$datos['orddetsol']= $dataU['data'][41];
				$datos['orddetfso']= $dataU['data'][42];
				$datos['orddethor']= $dataU['data'][43];
				$datos['orddetpse']= $dataU['data'][44];
				$datos['orddetret']= $dataU['data'][45];
				$datos['orddetvcm']= $dataU['data'][46];
				$datos['orddetpcm']= $dataU['data'][47];
				$datos['orddetvmm']= $dataU['data'][48];
				$datos['orddetvcr']= $dataU['data'][49];
				$datos['orddetpcr']= $dataU['data'][50];
				$datos['orddetvmr']= $dataU['data'][51];
				$datos['orddetvpo']= $dataU['data'][52];
				$datos['orddetpot']= $dataU['data'][53];
				$datos['orddetvmo']= $dataU['data'][54];
				$datos['orddetiau']= $dataU['data'][55];
				$datos['orddeteau']= $dataU['data'][56];
				$datos['orddetgui']= $dataU['data'][57];
				
				$_POST['datosInorddet'] = $datos;
				$a = new admisiones_erp('insertarEnInorddet');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$fanovacc = InsertarDatosUnixFanovacc($historia, $ingreso);
					return $fanovacc;
				}else{
					$error['error'] = 'Error al insertar en la tabla Inorddet de Unix';
					return $error;
				}
			}else{
				$fanovacc = InsertarDatosUnixFanovacc($historia, $ingreso);
				return $fanovacc;
			}
        }
    }
	
	function InsertarDatosUnixFanovacc($historia, $ingreso){
        $conexionUnix = verificarConexionUnix();
        if ($conexionUnix) {
			$a = new admisiones_erp('traerDatosFanovacc', $historia, $ingreso);
            $dataU = $a->data;
			$fecha = date('Y/m/d');
        	$fechaCompleta = date('Y-m-d H:i:s');
			if(count($dataU['data'])){
				$datos['novacchis']= $dataU['data'][0];
				$datos['novaccacc']= $dataU['data'][1] + 1; 
				$datos['novaccsec']= $dataU['data'][2];
				$datos['novacctip']= $dataU['data'][3];
				$datos['novaccfac']= $dataU['data'][4];
				$datos['novaccfec']= $fecha;
				$datos['novacccin']= $dataU['data'][6];
				$datos['novaccnin']= $dataU['data'][7];
				$datos['novaccnre']= $dataU['data'][8];
				$datos['novaccval']= $dataU['data'][9];
				$datos['novaccudu']= $dataU['data'][10];
				$datos['novaccfad']= $fechaCompleta;
				
				$_POST['datosFanovacc'] = $datos;
				$a = new admisiones_erp('insertarEnFanovacc');
				$dataU = $a->data;

				if ($dataU['data'] == true) {
					$fanovacc = InsertarDatosUnixFanovacc($historia, $ingreso);
					return $fanovacc;
				}else{
					$error['error'] = 'Error al insertar en la tabla Fanovacc de Unix';
					return $error;
				}
			}else{
				return true;
			}
        }
    }

?>