<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Estadisticas
 * Fecha		:	2013-08-12
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Muestra las estadisticas necesarias sobre el uso del centro de impresion
 * Condiciones  :   
 *********************************************************************************************************
 
 Actualizaciones:
 2014-01-20	(Frederick Aguirre) Se agregan los formularios externos en las estadisticas.
 2013-09-24 (Frederick Aguirre) En el query ppal se quitan las tablas usuarios y hce_000020 porque estan retardando la respuesta del query
								al hacer concat en el AND. Los datos de estas tablas se consultan aparte
			
 **********************************************************************************************************/ 
$wactualiz = "2014-01-20";
 
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";
	echo "<title>Estadisticas centro de impresion</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo ' <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenimp");
$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliameCenimp");
$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wcenimp = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenimp");

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "consultarestadisticas"){
		mostrarFiltrosDeConsulta( $_REQUEST['wfechai'], $_REQUEST['wfechaf'] );
		mostrarEstadisticas($_REQUEST['wfechai'], $_REQUEST['wfechaf']);
	}else if( $action == 'consultarestadisticasfiltros' ){
		mostrarFiltrosDeConsulta( $_REQUEST['wfechai'], $_REQUEST['wfechaf'] );
		mostrarEstadisticas($_REQUEST['wfechai'], $_REQUEST['wfechaf'], @$_REQUEST['piso'], @$_REQUEST['rol'], @$_REQUEST['usuario'], @$_REQUEST['formulario'], @$_REQUEST['empresa'], @$_REQUEST['modalidad']);		
	}else if( $action == "consultartabla" ){
		consultarTablaDetalle($_REQUEST['datos']);
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
	
	function consultarTablaDetalle( $wdatos ){
		global $conex;
        global $wbasedatohce;
		global $wemp_pmla;
		
		$query = " SELECT codigo, nombre, sum( cantidad ) as cantidad, count(*) as veces FROM (";
	
		//La variable $wdatos es un JSON, se convierte en un arreglo PHP
		$wdatos = str_replace("\\", "", $wdatos);
		$wdatos = str_replace("\"[", "[", $wdatos);
		$wdatos = str_replace("]\"", "]", $wdatos);
		$wdatos = json_decode( $wdatos, true );
		
		$ind = 0;
		foreach($wdatos as $solicitud){
			if( $ind > 0 )
				$query.=" UNION ALL ";
				
			$formularios = explode(",", $solicitud['forms']);
			foreach($formularios as &$form ){
				$form = "'".$form."'";
			}
			$formularios = implode(",", $formularios );
			
			$query.= "SELECT firpro as codigo, encdes as nombre, count(*) as cantidad "
			."			FROM ".$wbasedatohce."_000036 A, ".$wbasedatohce."_000001 "
			."		   WHERE firhis='".$solicitud['historia']."' "
			."			 AND firing='".$solicitud['ingreso']."' "
			." 			 AND A.Fecha_data BETWEEN '".$solicitud['fecha_i']."' AND '".$solicitud['fecha_f']."' "
			." 			 AND firpro=encpro "
			."			 AND firfir='on' "
			."     		 AND firpro IN ( ".$formularios." ) "
			."	GROUP BY firpro ";

			$ind++;
		}
		$query.=") as a GROUP BY codigo ORDER BY cantidad DESC";
		echo  "<table align='center' class='tabla_formularios'>";
		echo  "<tr class='encabezadotabla'  style='background-color:#999999;'><td align='center'>Formulario</td><td align='center'>Ordenes<br>de impresión</td><td align='center'>Diligenciados<br>e impresos</td></tr>";
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		if ($num>0){
			while( $row = mysql_fetch_assoc($res) ){
				echo "<tr class='fila1'  style='background-color:#cccccc;'><td>".$row['nombre']."</td><td>".$row['veces']."</td><td align='center'>".$row['cantidad']."</td></tr>";
			}
		}else{
			echo "<tr class='fila1'><td colspan=3>NO HAY DATOS</td></tr>";
		}
		echo "</table>";
	}
	
	function consultarPisos($wfecha_i='', $wfecha_f=''){
		global $conex;
        global $wbasedato;
        global $wbasedatomovhos;
		global $wemp_pmla;
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	S.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";
		
		$q = " SELECT Ccocod as codigo, Cconom as nombre "
				."   FROM ".$wbasedatomovhos."_000011, ".$wbasedato."_000005 S, ".$wbasedato."_000006 "
				."  WHERE S.id = Impsol "
				.$q_fechas
				."    AND Ccocod = Solcco"				
				."    AND Impest = 'on' "
				."    AND Solest = 'on' "
				."    GROUP BY 1 ORDER BY 2";
				
			
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			while($row = mysql_fetch_assoc($res)){
				array_push( $result, $row);
			}
		}
		return $result;				
	}
	
	function consultarRoles($wfecha_i='', $wfecha_f=''){
		global $conex;
        global $wbasedato;
        global $wbasedatohce;
		global $wemp_pmla;
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	S.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";
		
		$q = " SELECT rolcod as codigo, roldes as nombre "
				."   FROM ".$wbasedatohce."_000019, ".$wbasedatohce."_000020 U, ".$wbasedato."_000005 S, ".$wbasedato."_000006"
				."  WHERE  S.Seguridad = CONCAT('C-', U.usucod) " 
				."	  AND  usurol = rolcod "
				.$q_fechas
				."    AND S.id = Impsol "
				."    AND Impest = 'on' "
				."    AND Solest = 'on' "
				."    GROUP BY 1 ORDER BY 2";
				
				
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			while($row = mysql_fetch_assoc($res)){
				array_push( $result, $row);
			}
		}
		return $result;				
	}
	
	function consultarUsuarios($wfecha_i='', $wfecha_f=''){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	S.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";
		
		$q = " SELECT codigo, descripcion as nombre "
				."   FROM usuarios U, ".$wbasedato."_000005 S, ".$wbasedato."_000006"
				."  WHERE S.id = Impsol" 
				.$q_fechas
				."    AND S.Seguridad = CONCAT('C-', U.codigo) "
				."    AND Impest = 'on' "
				."    AND Solest = 'on' "
				."    GROUP BY 1 ORDER BY 2";
				
				
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			while($row = mysql_fetch_assoc($res)){
				array_push( $result, $row);
			}
		}
		return $result;				
	}

	function consultarModalidades($wfecha_i='', $wfecha_f=''){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	S.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";
		
		$q = " SELECT Modcod as codigo, Moddes as nombre "
				."   FROM ".$wbasedato."_000001, ".$wbasedato."_000005 S, ".$wbasedato."_000006"
				."  WHERE S.id = Impsol "
				.$q_fechas
				."    AND Modcod = Solmod"
				."    AND Impest = 'on' "
				."    AND Solest = 'on' "
				."    GROUP BY 1 ORDER BY 2";
				
				
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			while($row = mysql_fetch_assoc($res)){
				array_push( $result, $row);
			}
		}
		return $result;				
	}
	
	function consultarGruposDeEmpresas($wfecha_i='', $wfecha_f=''){
		global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wbasedatomovhos;
		global $wemp_pmla;
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	S.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";
		
		$q = " SELECT Ingres as responsable "
				."   FROM ".$wbasedatomovhos."_000016, ".$wbasedato."_000005 S ,".$wbasedato."_000006"
				."  WHERE Solhis = Inghis "
				."    AND Soling = Inging "
				.$q_fechas
				."    AND S.id = Impsol "	
				."    AND Impest = 'on' "
				."    AND Solest = 'on' "
				."    GROUP BY 1";
				
				
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			$empresas_agregadas = array();
			while($row = mysql_fetch_assoc($res)){
				$empresa = $row['responsable'];
				if( in_array($empresa, $empresas_agregadas) == false ){
					array_push( $empresas_agregadas, $empresa);
				}
			}
			
			$grupos_agregados = array();
			
			$q2  = "SELECT Empcod as codigo, Empdes as nombre, Empemp as empresas"
				."	  FROM ".$wbasedatohce."_000025 "
				."	 WHERE  Empest='on' ";
			$res2 = mysql_query($q2, $conex);
			$num2 = mysql_num_rows($res);
			if( $num2 > 0 ){
				while($row2 = mysql_fetch_assoc($res2)){
					$empresas_grupo = $row2['empresas'];
					$empresas_grupo = explode(",", $empresas_grupo);
					foreach( $empresas_agregadas as $empresa ){
						if( in_array($empresa, $empresas_grupo ) == true && in_array($row2['codigo'], $grupos_agregados) == false ){
							array_push( $result, $row2);
							array_push($grupos_agregados, $row2['codigo']);
							break;
						}
					}					
				}
				if( count($result) > 0 ){
					foreach ($result as $key => $row) {
						$volume[$key]  = $row['nombre'];
					}
					array_multisort($volume, SORT_ASC, $result);
				}
			}
		}		
		
		return $result;				
	}
	
	function consultarFormularios($wfecha_i='', $wfecha_f=''){
		global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wbasedatomovhos;
		global $wemp_pmla;
		global $wcenimp;
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	S.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";
		
		$q = " SELECT Solfpa as forms_paquete, Solfor as forms_manual "
				."   FROM ".$wbasedato."_000005 S ,".$wbasedato."_000006"
				."  WHERE S.id = Impsol "
				.$q_fechas
				."    AND Impest = 'on' "
				."    AND Solest = 'on' ";
					
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			$formularios_agregados = array();
			while($row = mysql_fetch_assoc($res)){
				//array_push( $result, $row);
				$formularios = $row['forms_paquete'];
				$formularios = str_replace("|",",",$formularios);
				if($formularios == ""){
					$formularios = $row['forms_manual'];
				}else{
					if($row['forms_manual'] != '' )
						$formularios.= $row['forms_manual'];				
				}
				$formularios = explode( ",", $formularios );
				foreach($formularios as $formulario ){
					if( in_array($formulario, $formularios_agregados) == false ){
						array_push( $formularios_agregados, "'".$formulario."'");
					}
				}				
			}
			$formularios = implode(",", $formularios_agregados );
			
			//2014-01-20
			$forms_externos = array();
			$arr_aux = array();
			foreach($formularios_agregados as $formu ){
				if(preg_match('/EXT/i',$formu)){
					array_push( $forms_externos, $formu );
				}else{
					array_push( $arr_aux, $formu );
				}
			}
			$formularios = implode(",",$arr_aux);
			/*****/
			
			$q2  = "SELECT Encpro as codigo,Encdes as nombre "
				."	  FROM ".$wbasedatohce."_000001 "
				."	 WHERE  Encpro in (".$formularios.") "
				."  ORDER BY 2";
			$res2 = mysql_query($q2, $conex);
			$num2 = mysql_num_rows($res2);
			if( $num2 > 0 ){
				while($row2 = mysql_fetch_assoc($res2)){
					array_push( $result, $row2);
				}
			}			
			//2014-01-20
			$forms_externos = implode(",",$forms_externos);
			$q2 = " SELECT Fexcod as codigo,Fexdes as nombre
					 FROM ".$wcenimp."_000009
					WHERE Fexcod IN (".$forms_externos.")
					  AND Fexest = 'on'
					ORDER BY 1 ";
			$res23 = mysql_query($q2, $conex);
			$num23 = mysql_num_rows($res);
			if( $num23 > 0 ){
				while($row23 = mysql_fetch_assoc($res23)){
					array_push( $result, $row23);
				}
			}
		}
		return $result;				
	}
	
	function mostrarFiltrosDeConsulta($wfecha_i='', $wfecha_f=''){
	
		$wccos = consultarPisos($wfecha_i, $wfecha_f);
		$wroles = consultarRoles($wfecha_i, $wfecha_f);
		$wusuarios = consultarUsuarios($wfecha_i, $wfecha_f);
		$wmodalidades = consultarModalidades($wfecha_i, $wfecha_f);
		$wempresas = consultarGruposDeEmpresas($wfecha_i, $wfecha_f);
		$wformularios = consultarFormularios($wfecha_i, $wfecha_f);
		
		$width_sel = " width: 95%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
		
				//------------TABLA DE PARAMETROS-------------
		echo '<table align="center">';
		
		if( count($wccos) > 0 ){
			echo "<tr>";
			echo '<td class="encabezadotabla">Piso</td>';
			echo '<td class="fila1">';
			echo "<div align='center'>";
			echo "<select id='lista_pisos' onchange='usarFiltros()'  align='center' style='".$width_sel." margin:5px;'>";
			echo "<option></option>";
			foreach ($wccos as $centroCostos)
				echo "<option value='".$centroCostos['codigo']."'>".$centroCostos['nombre']."</option>";
			echo '</select>';
			echo '</div>';
			echo "</td>";
			echo "</tr>";
		}		
		if( count($wroles) > 0 ){
			echo "<tr>";
			echo '<td class="encabezadotabla">Rol</td>';
			echo '<td class="fila1">';
			echo "<div align='center'>";
			echo "<select id='lista_roles' onchange='usarFiltros()'  align='center' style='".$width_sel." margin:5px;'>";
			echo "<option></option>";
			foreach ($wroles as $wrol)
				echo "<option value='".$wrol['codigo']."'>".$wrol['nombre']."</option>";
			echo '</select>';
			echo '</div>';
			echo "</td>";
			echo "</tr>";
		}		
		if( count($wusuarios) > 0 ){
			echo "<tr>";
			echo '<td class="encabezadotabla">Usuario</td>';
			echo '<td class="fila1">';
			echo "<div align='center'>";
			echo "<select id='lista_usuarios' onchange='usarFiltros()'  align='center' style='".$width_sel." margin:5px;'>";
			echo "<option></option>";
			foreach ($wusuarios as $wusuario)
				echo "<option value='".$wusuario['codigo']."'>".$wusuario['nombre']."</option>";
			echo '</select>';
			echo '</div>';
			echo "</td>";
			echo "</tr>";
		}		
		if( count($wformularios) > 0 ){
			echo "<tr>";
			echo '<td class="encabezadotabla">Formulario</td>';
			echo '<td class="fila1">';
			echo "<div align='center'>";
			echo "<select id='lista_formularios' onchange='usarFiltros()'  align='center' style='".$width_sel." margin:5px;'>";
			echo "<option></option>";
			foreach ($wformularios as $wformulario)
				echo "<option value='".$wformulario['codigo']."'>".$wformulario['nombre']."</option>";
			echo '</select>';
			echo '</div>';
			echo "</td>";
			echo "</tr>";
		}		
		if( count($wempresas) > 0 ){
			echo "<tr>";
			echo '<td class="encabezadotabla">Grupo</td>';
			echo '<td class="fila1">';
			echo "<div align='center'>";
			echo "<select id='lista_empresas' onchange='usarFiltros()'  align='center' style='".$width_sel." margin:5px;'>";
			echo "<option></option>";
			foreach ($wempresas as $wempresa)
				echo "<option value='".$wempresa['codigo']."'>".$wempresa['codigo']." - ".$wempresa['nombre']."</option>";
			echo '</select>';
			echo '</div>';
			echo "</td>";
			echo "</tr>";
		}		
		if( count($wmodalidades) > 0 ){
			echo "<tr>";
			echo '<td class="encabezadotabla">Modalidad</td>';
			echo '<td class="fila1">';
			echo "<div align='center'>";
			echo "<select id='lista_modalidades' onchange='usarFiltros()'  align='center' style='".$width_sel." margin:5px;'>";
			echo "<option></option>";
			foreach ($wmodalidades as $wmodalidad)
				echo "<option value='".$wmodalidad['codigo']."'>".$wmodalidad['nombre']."</option>";
			echo '</select>';
			echo '</div>';
			echo "</td>";
			echo "</tr>";
		}		
		echo "</table>";
		echo "<br><br>";
		//------------FIN TABLA DE PARAMETROS-------------	
	}
	
	function mostrarEstadisticas($wfecha_i='', $wfecha_f='', $wpiso='', $wrol='', $wusuario='', $wformulario='', $wempresa='', $wmodalidad=''){
		global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wbasedatomovhos;
        global $wbasedatocliame;
		global $wemp_pmla;
		global $wcenimp;

		$datos_cco = array();
		$datos_rol = array();
		$datos_usuario = array();
		$datos_gruposemp = array();
		$datos_modalidades = array();
		$datos_formularios = array();

		//Cargar todos los grupos de empresas para no hacer un llamado a la base de datos por cada uno
		$grupos_de_empresas = array();
		$q2  = "SELECT Empcod as codigo, Empdes as nombre, Empemp as empresas"
				."	  FROM ".$wbasedatohce."_000025 "
				."	 WHERE  Empest='on' ";
		$res2 = mysql_query($q2, $conex);
		$num2 = mysql_num_rows($res2);
		if( $num2 > 0 ){
			while($row2 = mysql_fetch_assoc($res2)){
				if( array_key_exists( $row2['codigo'], $grupos_de_empresas ) == false ){
					$grupos_de_empresas[ $row2['codigo'] ] = array();
					$grupos_de_empresas[ $row2['codigo'] ]['entidades']= explode(",", $row2['empresas'] );
					$grupos_de_empresas[ $row2['codigo'] ]['nombre']= $row2['nombre'];
					$grupos_de_empresas[ $row2['codigo'] ]['codigo']= $row2['codigo'];
				}
			}
		}
		//Cargar todos los formularios para no hacer un llamado a la base de datos por cada uno
		$formularios_hce = array();
		$q2  = "SELECT Encpro as codigo, Encdes as nombre"
				."	  FROM ".$wbasedatohce."_000001 ";
		$res2 = mysql_query($q2, $conex);
		$num2 = mysql_num_rows($res2);
		if( $num2 > 0 ){
			while($row2 = mysql_fetch_assoc($res2)){
				if( array_key_exists( $row2['codigo'], $formularios_hce ) == false ){
					$formularios_hce[ $row2['codigo'] ] = array();
					$formularios_hce[ $row2['codigo'] ]['nombre']= $row2['nombre'];
				}
			}
		}
		
		//2014-01-20: Se agregan los formularios externos
		$q2  = "SELECT Fexcod as codigo,Fexdes as nombre
					 FROM ".$wcenimp."_000009
					ORDER BY 1";
		$res2 = mysql_query($q2, $conex);
		$num2 = mysql_num_rows($res2);
		if( $num2 > 0 ){
			while($row2 = mysql_fetch_assoc($res2)){
				if( array_key_exists( $row2['codigo'], $formularios_hce ) == false ){
					$formularios_hce[ $row2['codigo'] ] = array();
					$formularios_hce[ $row2['codigo'] ]['nombre']= $row2['nombre'];
				}
			}
		}
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	S.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";
		
		$and_piso = '';
		if( $wpiso != '' ) $and_piso = "	AND Ccocod = '".$wpiso."'";
		
		$and_rol = '';
		if( $wrol != '' ) $and_rol = "	AND rolcod = '".$wrol."'";
		
		$and_usuario = '';
		if( $wusuario != '' ) $and_usuario = "	AND usucod = '".$wusuario."'";
			
		$and_modalidad = '';
		if( $wmodalidad != '' ) $and_modalidad = "	AND Modcod = '".$wmodalidad."'";
		
		$arr_datos = array();//2013-09-24	
		$arr_usuarios = array(); //2013-09-24		

		$q = " SELECT 	Solfpa as forms_paquetes, Solfor as formularios, Solnuf as forms_diligenciados, Solnuh as num_hojas, Solhis as historia, Soling as ingreso "
			." 			,Solfei as fecha_i, Solfef as fecha_f, Ccocod as cod_cco, Cconom as nom_cco, S.Seguridad as cod_user"
			." 			,Modcod as cod_modalidad, Moddes as nom_modalidad, Ingres as cod_responsable, Empnom  as nom_responsable, CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente "
			."   FROM ".$wbasedato."_000005 S, ".$wbasedato."_000006, ".$wbasedatomovhos."_000011, ".$wbasedato."_000001, root_000036, root_000037, ".$wbasedatomovhos."_000016 LEFT JOIN ".$wbasedatocliame."_000024 ON (ingres  = empcod)"
			."  WHERE S.id = Impsol "
			.$q_fechas
			."    AND Ccocod = Solcco "
			."    AND Modcod = Solmod "
			."    AND Solhis = Inghis "
			."    AND Soling = Inging "
			."    AND Solhis  = orihis "
            ."    AND Soling  = oriing "
			."    AND oriori  = '".$wemp_pmla."'"
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid "
			.$and_piso
			//.$and_rol
			.$and_modalidad
			//.$and_usuario
			."    AND Impest = 'on' "
			."    AND Solest = 'on' ";
		
		$res = mysql_query($q, $conex) or die( 'error '.mysql_error().' en el query ');
		$num = mysql_num_rows($res);
		if( $num > 0 ){
			while($row2 = mysql_fetch_assoc($res)){
				$row2['ok'] = 'off';
				$row2['cod_user'] = str_replace("C-", "", $row2['cod_user']);
				$row2['cod_user'] = str_replace("c-", "", $row2['cod_user']);
				if( in_array( "'".$row2['cod_user']."'", $arr_usuarios ) == false ) //Si el codigo del usuario no existe en arr_usuarios se guarda
					array_push( $arr_usuarios, "'".$row2['cod_user']."'" );
					
				array_push ( $arr_datos, $row2 ); //Se guarda la fila en arr_datos
			}
		
			$chain_usuarios = implode(",", $arr_usuarios );
			
			$q_usuarios = "SELECT  rolcod as cod_rol, roldes as nom_rol, codigo as cod_usu, descripcion as nom_usu 
							 FROM ".$wbasedatohce."_000019, ".$wbasedatohce."_000020 U, usuarios
							WHERE codigo IN (".$chain_usuarios.")
							  AND usurol = rolcod 
							  AND codigo = usucod "
							  .$and_rol
							  .$and_usuario;
		
			$err1 = mysql_query($q_usuarios,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuarios." - ".mysql_error());
			$num1 = mysql_num_rows($err1);	
			if( $num1 > 0 ){
				while( $row1 = mysql_fetch_assoc($err1) ){
					foreach( $arr_datos as $pos=>&$dato ){
						if( $dato['cod_user'] == $row1['cod_usu'] ){
							$dato['nom_rol']  = $row1['nom_rol'];
							$dato['cod_usu']  = $row1['cod_usu'];
							$dato['nom_usu']  = $row1['nom_usu'];
							$dato['cod_rol'] = $row1['cod_rol'];
							$dato['ok'] = "on";
						}
					}
				}
			}

			foreach ($arr_datos as $row ){
				if( $row['ok'] != 'on' )
					continue;
				//Si seleccionaron un grupo de empresas en el filtro, solo muestro los datos de las historias cuya entidad pertenezca al grupo
				if( $wempresa != '' ){ 
					if( in_array( $row['cod_responsable'], $grupos_de_empresas[$wempresa]['entidades']) == false ){
						continue;
					}
				}				
				//Formularios de la solicitud
				$formularios = $row['forms_paquetes'];
				$formularios = str_replace("|",",",$formularios);
				if($formularios == ""){
					$formularios = $row['formularios'];
				}else{
					if($row['formularios'] != '' )
						$formularios.= $row['formularios'];				
				}
				$formularios = explode( ",", $formularios );				
				if( $wformulario != '' ){
					if( in_array( $wformulario, $formularios) == false ){
						continue;
					}
				}				
				//En una solicitud, puedo elegir varios paquetes, y un formulario puede estar repetido en ambos
				//Para la tarea, una solicitud en donde esta el formulario varias veces, no cuenta como si lo hubiesen pedido imprimir varias veces
				$formularios_unicos = array();
				foreach($formularios as $form ){
					if( in_array( $form, $formularios_unicos ) == false )
						array_push( $formularios_unicos, $form );
				}
				$formularios = $formularios_unicos;
			
				//------------------------------PARA CENTRO DE COSTOS---------------------------------
				if( array_key_exists( $row['cod_cco'], $datos_cco ) == false ){
					$datos_cco[ $row['cod_cco'] ] = array();
				}
				if( array_key_exists( 'historias', $datos_cco[ $row['cod_cco'] ] ) == false ){
					$datos_cco[ $row['cod_cco'] ]['historias'] = array();
				}
				if( array_key_exists( 'solicitudes', $datos_cco[ $row['cod_cco'] ] ) == false ){
					$datos_cco[ $row['cod_cco'] ]['solicitudes'] = array();
				}
				if( array_key_exists( 'formularios', $datos_cco[ $row['cod_cco'] ] ) == false ){
					$datos_cco[ $row['cod_cco'] ]['formularios'] = 0;
				}
				if( array_key_exists( 'paginas', $datos_cco[ $row['cod_cco'] ] ) == false ){
					$datos_cco[ $row['cod_cco'] ]['paginas'] = 0;
				}
				if( array_key_exists( 'nombre_cco', $datos_cco[ $row['cod_cco'] ] ) == false ){
					$datos_cco[ $row['cod_cco'] ]['nombre_cco'] = $row['nom_cco'];
				}				
				$datos_cco[ $row['cod_cco'] ]['formularios'] += $row['forms_diligenciados'];
				$datos_cco[ $row['cod_cco'] ]['paginas'] += $row['num_hojas'];
				
				/*if( in_array( $row['historia']."-".$row['ingreso'], $datos_cco[ $row['cod_cco'] ]['historias'] ) == false ){
					array_push( $datos_cco[ $row['cod_cco'] ]['historias'], $row['historia']."-".$row['ingreso'] );
				}*/
				
				//DATOS PARA EL JSON OCULTO NECESARIOS PARA LA LISTA DE FORMULARIOS DILIGENCIADOS
				$soli = array();
				$soli['fecha_i'] = $row['fecha_i'];
				$soli['fecha_f'] = $row['fecha_f'];
				$soli['historia'] = $row['historia'];
				$soli['ingreso'] = $row['ingreso'];
				$soli['forms'] = implode(",", $formularios );
				array_push($datos_cco[ $row['cod_cco'] ]['solicitudes'], $soli );
				
				if( array_key_exists( $row['historia']."-".$row['ingreso'], $datos_cco[ $row['cod_cco'] ]['historias'] ) == false ){
					$rowsave = array();
					$rowsave['forms'] = $row['forms_diligenciados'];
					$rowsave['pags'] = $row['num_hojas'];
					$rowsave['pac'] = $row['paciente'];		
					$datos_cco[ $row['cod_cco'] ]['historias'][$row['historia']."-".$row['ingreso']] = $rowsave;
				}else{
					$datos_cco[ $row['cod_cco'] ]['historias'][$row['historia']."-".$row['ingreso']]['forms']+=$row['forms_diligenciados'];
					$datos_cco[ $row['cod_cco'] ]['historias'][$row['historia']."-".$row['ingreso']]['pags']+=$row['num_hojas'];
				}
				
				//------------------------------PARA ROLES---------------------------------
				if( array_key_exists( $row['cod_rol'], $datos_rol ) == false ){
					$datos_rol[ $row['cod_rol'] ] = array();
				}
				if( array_key_exists( 'historias', $datos_rol[ $row['cod_rol'] ] ) == false ){
					$datos_rol[ $row['cod_rol'] ]['historias'] = array();
				}
				if( array_key_exists( 'solicitudes', $datos_rol[ $row['cod_rol'] ] ) == false ){
					$datos_rol[ $row['cod_rol'] ]['solicitudes'] = array();
				}
				if( array_key_exists( 'usuarios', $datos_rol[ $row['cod_rol'] ] ) == false ){
					$datos_rol[ $row['cod_rol'] ]['usuarios'] = array();
				}
				if( array_key_exists( 'formularios', $datos_rol[ $row['cod_rol'] ] ) == false ){
					$datos_rol[ $row['cod_rol'] ]['formularios'] = 0;
				}
				if( array_key_exists( 'paginas', $datos_rol[ $row['cod_rol'] ] ) == false ){
					$datos_rol[ $row['cod_rol'] ]['paginas'] = 0;
				}
				if( array_key_exists( 'nombre_rol', $datos_rol[ $row['cod_rol'] ] ) == false ){
					$datos_rol[ $row['cod_rol'] ]['nombre_rol'] = $row['nom_rol'];
				}				
				$datos_rol[ $row['cod_rol'] ]['formularios'] += $row['forms_diligenciados'];
				$datos_rol[ $row['cod_rol'] ]['paginas'] += $row['num_hojas'];
				
				/*if( in_array( $row['historia']."-".$row['ingreso'], $datos_rol[ $row['cod_rol'] ]['historias'] ) == false ){
					array_push( $datos_rol[ $row['cod_rol'] ]['historias'], $row['historia']."-".$row['ingreso'] );
				}	*/
				
				//DATOS PARA EL JSON OCULTO NECESARIOS PARA LA LISTA DE FORMULARIOS DILIGENCIADOS
				$soli = array();
				$soli['fecha_i'] = $row['fecha_i'];
				$soli['fecha_f'] = $row['fecha_f'];
				$soli['historia'] = $row['historia'];
				$soli['ingreso'] = $row['ingreso'];
				$soli['forms'] = implode(",", $formularios );
				array_push($datos_rol[ $row['cod_rol'] ]['solicitudes'], $soli );
				
				//DATOS CUANDO DAN CLICK EN HISTORIAS
				if( array_key_exists( $row['historia']."-".$row['ingreso'], $datos_rol[ $row['cod_rol'] ]['historias'] ) == false ){					
					$rowsave = array();
					$rowsave['forms'] = $row['forms_diligenciados'];
					$rowsave['pags'] = $row['num_hojas'];
					$rowsave['pac'] = $row['paciente'];
					$datos_rol[ $row['cod_rol'] ]['historias'][$row['historia']."-".$row['ingreso']] = $rowsave;
				}else{
					$datos_rol[ $row['cod_rol'] ]['historias'][$row['historia']."-".$row['ingreso']]['forms']+=$row['forms_diligenciados'];
					$datos_rol[ $row['cod_rol'] ]['historias'][$row['historia']."-".$row['ingreso']]['pags']+=$row['num_hojas'];
				}
				//DATOS CUANDO DAN CLICK EN ROLES -- mostrar los usuarios de ese rol
				if( array_key_exists( $row['cod_usu'], $datos_rol[ $row['cod_rol'] ]['usuarios'] ) == false ){					
					$rowsave = array();
					$rowsave['forms'] = $row['forms_diligenciados'];
					$rowsave['pags'] = $row['num_hojas'];
					$rowsave['usuario'] = $row['nom_usu'];
					$datos_rol[ $row['cod_rol'] ]['usuarios'][$row['cod_usu']] = $rowsave;
				}else{
					$datos_rol[ $row['cod_rol'] ]['usuarios'][$row['cod_usu']]['forms']+=$row['forms_diligenciados'];
					$datos_rol[ $row['cod_rol'] ]['usuarios'][$row['cod_usu']]['pags']+=$row['num_hojas'];
				}
				//------------------------------PARA USUARIOS---------------------------------
				if( array_key_exists( $row['cod_usu'], $datos_usuario ) == false ){
					$datos_usuario[ $row['cod_usu'] ] = array();
				}
				if( array_key_exists( 'historias', $datos_usuario[ $row['cod_usu'] ] ) == false ){
					$datos_usuario[ $row['cod_usu'] ]['historias'] = array();
				}
				if( array_key_exists( 'solicitudes', $datos_usuario[ $row['cod_usu'] ] ) == false ){
					$datos_usuario[ $row['cod_usu'] ]['solicitudes'] = array();
				}
				if( array_key_exists( 'formularios', $datos_usuario[ $row['cod_usu'] ] ) == false ){
					$datos_usuario[ $row['cod_usu'] ]['formularios'] = 0;
				}
				if( array_key_exists( 'paginas', $datos_usuario[ $row['cod_usu'] ] ) == false ){
					$datos_usuario[ $row['cod_usu'] ]['paginas'] = 0;
				}
				if( array_key_exists( 'nombre_usuario', $datos_usuario[ $row['cod_usu'] ] ) == false ){
					$datos_usuario[ $row['cod_usu'] ]['nombre_usuario'] = $row['nom_usu'];
				}				
				$datos_usuario[ $row['cod_usu'] ]['formularios'] += $row['forms_diligenciados'];
				$datos_usuario[ $row['cod_usu'] ]['paginas'] += $row['num_hojas'];
				
				/*if( in_array( $row['historia']."-".$row['ingreso'], $datos_usuario[ $row['cod_usu'] ]['historias'] ) == false ){
					array_push( $datos_usuario[ $row['cod_usu'] ]['historias'], $row['historia']."-".$row['ingreso'] );
				}*/		
				//DATOS PARA EL JSON OCULTO NECESARIOS PARA LA LISTA DE FORMULARIOS DILIGENCIADOS
				$soli = array();
				$soli['fecha_i'] = $row['fecha_i'];
				$soli['fecha_f'] = $row['fecha_f'];
				$soli['historia'] = $row['historia'];
				$soli['ingreso'] = $row['ingreso'];
				$soli['forms'] = implode(",", $formularios );
				array_push($datos_usuario[ $row['cod_usu'] ]['solicitudes'], $soli );
				
				if( array_key_exists( $row['historia']."-".$row['ingreso'], $datos_usuario[ $row['cod_usu'] ]['historias'] ) == false ){					
					$rowsave = array();
					$rowsave['forms'] = $row['forms_diligenciados'];
					$rowsave['pags'] = $row['num_hojas'];
					$rowsave['pac'] = $row['paciente'];
					$datos_usuario[ $row['cod_usu'] ]['historias'][$row['historia']."-".$row['ingreso']] = $rowsave;
				}else{
					$datos_usuario[ $row['cod_usu'] ]['historias'][$row['historia']."-".$row['ingreso']]['forms']+=$row['forms_diligenciados'];
					$datos_usuario[ $row['cod_usu'] ]['historias'][$row['historia']."-".$row['ingreso']]['pags']+=$row['num_hojas'];
				}
				
				//------------------------------PARA MODALIDADES---------------------------------
				if( array_key_exists( $row['cod_modalidad'], $datos_modalidades ) == false ){
					$datos_modalidades[ $row['cod_modalidad'] ] = array();
				}
				if( array_key_exists( 'historias', $datos_modalidades[ $row['cod_modalidad'] ] ) == false ){
					$datos_modalidades[ $row['cod_modalidad'] ]['historias'] = array();
				}
				if( array_key_exists( 'solicitudes', $datos_modalidades[ $row['cod_modalidad'] ] ) == false ){
					$datos_modalidades[ $row['cod_modalidad'] ]['solicitudes'] = array();
				}
				if( array_key_exists( 'formularios', $datos_modalidades[ $row['cod_modalidad'] ] ) == false ){
					$datos_modalidades[ $row['cod_modalidad'] ]['formularios'] = 0;
				}
				if( array_key_exists( 'paginas', $datos_modalidades[ $row['cod_modalidad'] ] ) == false ){
					$datos_modalidades[ $row['cod_modalidad'] ]['paginas'] = 0;
				}
				if( array_key_exists( 'nombre_modalidad', $datos_modalidades[ $row['cod_modalidad'] ] ) == false ){
					$datos_modalidades[ $row['cod_modalidad'] ]['nombre_modalidad'] = $row['nom_modalidad'];
				}				
				$datos_modalidades[ $row['cod_modalidad'] ]['formularios'] += $row['forms_diligenciados'];
				$datos_modalidades[ $row['cod_modalidad'] ]['paginas'] += $row['num_hojas'];
				
				/*if( in_array( $row['historia']."-".$row['ingreso'], $datos_modalidades[ $row['cod_modalidad'] ]['historias'] ) == false ){
					array_push( $datos_modalidades[ $row['cod_modalidad'] ]['historias'], $row['historia']."-".$row['ingreso'] );
				}*/
				//DATOS PARA EL JSON OCULTO NECESARIOS PARA LA LISTA DE FORMULARIOS DILIGENCIADOS
				$soli = array();
				$soli['fecha_i'] = $row['fecha_i'];
				$soli['fecha_f'] = $row['fecha_f'];
				$soli['historia'] = $row['historia'];
				$soli['ingreso'] = $row['ingreso'];
				$soli['forms'] = implode(",", $formularios );
				array_push($datos_modalidades[ $row['cod_modalidad'] ]['solicitudes'], $soli );
				
				if( array_key_exists( $row['historia']."-".$row['ingreso'], $datos_modalidades[ $row['cod_modalidad'] ]['historias'] ) == false ){					
					$rowsave = array();
					$rowsave['forms'] = $row['forms_diligenciados'];
					$rowsave['pags'] = $row['num_hojas'];
					$rowsave['pac'] = $row['paciente'];
					$datos_modalidades[ $row['cod_modalidad'] ]['historias'][$row['historia']."-".$row['ingreso']] = $rowsave;
				}else{
					$datos_modalidades[ $row['cod_modalidad'] ]['historias'][$row['historia']."-".$row['ingreso']]['forms']+=$row['forms_diligenciados'];
					$datos_modalidades[ $row['cod_modalidad'] ]['historias'][$row['historia']."-".$row['ingreso']]['pags']+=$row['num_hojas'];
				}
				
				//------------------------------PARA GRUPOS DE EMPRESAS---------------------------------				
				
				$existe_entidad_en_grupo = false;
				foreach( $grupos_de_empresas as $grupo_empresarial ){
					if( in_array( $row['cod_responsable'], $grupo_empresarial['entidades'] )){
						$existe_entidad_en_grupo = true;
						if( array_key_exists( $grupo_empresarial['codigo'], $datos_gruposemp ) == false ){
							$datos_gruposemp[ $grupo_empresarial['codigo'] ] = array();
						}
						if( array_key_exists( 'historias', $datos_gruposemp[ $grupo_empresarial['codigo'] ] ) == false ){
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['historias'] = array();
						}
						if( array_key_exists( 'solicitudes', $datos_gruposemp[ $grupo_empresarial['codigo'] ] ) == false ){
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['solicitudes'] = array();
						}
						if( array_key_exists( 'entidades', $datos_gruposemp[ $grupo_empresarial['codigo'] ] ) == false ){
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['entidades'] = array();
						}
						if( array_key_exists( 'formularios', $datos_gruposemp[ $grupo_empresarial['codigo'] ] ) == false ){
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['formularios'] = 0;
						}
						if( array_key_exists( 'paginas', $datos_gruposemp[ $grupo_empresarial['codigo'] ] ) == false ){
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['paginas'] = 0;
						}
						if( array_key_exists( 'nombre_grupo', $datos_gruposemp[ $grupo_empresarial['codigo'] ] ) == false ){
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['nombre_grupo'] = $grupo_empresarial['nombre'];
						}
						$datos_gruposemp[ $grupo_empresarial['codigo'] ]['formularios'] += $row['forms_diligenciados'];
						$datos_gruposemp[ $grupo_empresarial['codigo'] ]['paginas'] += $row['num_hojas'];
						
						/*if( in_array( $row['historia']."-".$row['ingreso'], $datos_gruposemp[ $grupo_empresarial['codigo'] ]['historias'] ) == false ){
							array_push( $datos_gruposemp[ $grupo_empresarial['codigo'] ]['historias'], $row['historia']."-".$row['ingreso'] );
						}*/
						//DATOS PARA EL JSON OCULTO NECESARIOS PARA LA LISTA DE FORMULARIOS DILIGENCIADOS
						$soli = array();
						$soli['fecha_i'] = $row['fecha_i'];
						$soli['fecha_f'] = $row['fecha_f'];
						$soli['historia'] = $row['historia'];
						$soli['ingreso'] = $row['ingreso'];
						$soli['forms'] = implode(",", $formularios );
						array_push($datos_gruposemp[ $grupo_empresarial['codigo'] ]['solicitudes'], $soli );
				
						//DATOS CUANDO DAN CLICK EN HISTORIAS
						if( array_key_exists( $row['historia']."-".$row['ingreso'], $datos_gruposemp[ $grupo_empresarial['codigo'] ]['historias'] ) == false ){					
							$rowsave = array();
							$rowsave['forms'] = $row['forms_diligenciados'];
							$rowsave['pags'] = $row['num_hojas'];
							$rowsave['pac'] = $row['paciente'];
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['historias'][$row['historia']."-".$row['ingreso']] = $rowsave;
						}else{
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['historias'][$row['historia']."-".$row['ingreso']]['forms']+=$row['forms_diligenciados'];
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['historias'][$row['historia']."-".$row['ingreso']]['pags']+=$row['num_hojas'];
						}
						//DATOS CUANDO DAN CLICK EN GRUPO -- mostrar las entidades de ese grupo
						if( array_key_exists( $row['cod_responsable'], $datos_gruposemp[ $grupo_empresarial['codigo'] ]['entidades'] ) == false ){					
							$rowsave = array();
							$rowsave['forms'] = $row['forms_diligenciados'];
							$rowsave['pags'] = $row['num_hojas'];
							$rowsave['entidad'] = $row['nom_responsable'];
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['entidades'][$row['cod_responsable']] = $rowsave;
						}else{
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['entidades'][$row['cod_responsable']]['forms']+=$row['forms_diligenciados'];
							$datos_gruposemp[ $grupo_empresarial['codigo'] ]['entidades'][$row['cod_responsable']]['pags']+=$row['num_hojas'];
						}
				
						break;
					}
				}
				if( $existe_entidad_en_grupo == false ){
					if( array_key_exists( 'singrupo', $datos_gruposemp ) == false ){
						$datos_gruposemp[ 'singrupo' ] = array();
					}
					if( array_key_exists( 'historias', $datos_gruposemp[ 'singrupo' ] ) == false ){
						$datos_gruposemp[ 'singrupo' ]['historias'] = array();
					}
					if( array_key_exists( 'solicitudes', $datos_gruposemp[ 'singrupo' ] ) == false ){
						$datos_gruposemp[ 'singrupo' ]['solicitudes'] = array();
					}
					if( array_key_exists( 'entidades', $datos_gruposemp[ 'singrupo' ] ) == false ){
						$datos_gruposemp[ 'singrupo' ]['entidades'] = array();
					}
					if( array_key_exists( 'formularios', $datos_gruposemp[ 'singrupo' ] ) == false ){
						$datos_gruposemp[ 'singrupo' ]['formularios'] = 0;
					}
					if( array_key_exists( 'paginas', $datos_gruposemp[ 'singrupo' ] ) == false ){
						$datos_gruposemp[ 'singrupo' ]['paginas'] = 0;
					}
					if( array_key_exists( 'nombre_grupo', $datos_gruposemp[ 'singrupo' ] ) == false ){
						$datos_gruposemp[ 'singrupo' ]['nombre_grupo'] = 'SIN GRUPO EMPRESARIAL';
					}				
					$datos_gruposemp[ 'singrupo' ]['formularios'] += $row['forms_diligenciados'];
					$datos_gruposemp[ 'singrupo' ]['paginas'] += $row['num_hojas'];
					
					/*if( in_array( $row['historia']."-".$row['ingreso'], $datos_gruposemp[ 'singrupo' ]['historias'] ) == false ){
						array_push( $datos_gruposemp[ 'singrupo' ]['historias'], $row['historia']."-".$row['ingreso'] );
					}*/
					//DATOS PARA EL JSON OCULTO NECESARIOS PARA LA LISTA DE FORMULARIOS DILIGENCIADOS
					$soli = array();
					$soli['fecha_i'] = $row['fecha_i'];
					$soli['fecha_f'] = $row['fecha_f'];
					$soli['historia'] = $row['historia'];
					$soli['ingreso'] = $row['ingreso'];
					$soli['forms'] = implode(",", $formularios );
					array_push($datos_gruposemp[ 'singrupo' ]['solicitudes'], $soli );
						
					if( array_key_exists( $row['historia']."-".$row['ingreso'], $datos_gruposemp[ 'singrupo' ]['historias'] ) == false ){					
						$rowsave = array();
						$rowsave['forms'] = $row['forms_diligenciados'];
						$rowsave['pags'] = $row['num_hojas'];
						$rowsave['pac'] = $row['paciente'];
						$datos_gruposemp[ 'singrupo' ]['historias'][$row['historia']."-".$row['ingreso']] = $rowsave;
					}else{
						$datos_gruposemp[ 'singrupo' ]['historias'][$row['historia']."-".$row['ingreso']]['forms']+=$row['forms_diligenciados'];
						$datos_gruposemp[ 'singrupo' ]['historias'][$row['historia']."-".$row['ingreso']]['pags']+=$row['num_hojas'];
					}
					//DATOS CUANDO DAN CLICK EN GRUPO -- mostrar las entidades de ese grupo
					if( array_key_exists( $row['cod_responsable'], $datos_gruposemp[ 'singrupo' ]['entidades'] ) == false ){					
						$rowsave = array();
						$rowsave['forms'] = $row['forms_diligenciados'];
						$rowsave['pags'] = $row['num_hojas'];
						$rowsave['entidad'] = $row['nom_responsable'];
						$datos_gruposemp[ 'singrupo' ]['entidades'][$row['cod_responsable']] = $rowsave;
					}else{
						$datos_gruposemp[ 'singrupo' ]['entidades'][$row['cod_responsable']]['forms']+=$row['forms_diligenciados'];
						$datos_gruposemp[ 'singrupo' ]['entidades'][$row['cod_responsable']]['pags']+=$row['num_hojas'];
					}
				}
				
				//------------------------------PARA FORMULARIOS---------------------------------
				//Si selecciono un formulario en el filtro, solo debe mostrar ese formulario
				//Puesto que al momento de crear una solicitud no solo se selecciona ese formulario
				if( $wformulario != '' ){
					$formularios = array();
					array_push( $formularios, $wformulario);
				}
					
				foreach( $formularios as $form ){
						if( array_key_exists( $form, $datos_formularios ) == false ){
							$datos_formularios[ $form ] = array();
						}
						if( array_key_exists( 'historias', $datos_formularios[ $form ] ) == false ){
							$datos_formularios[ $form ]['historias'] = array();
						}
						if( array_key_exists( 'cantidad', $datos_formularios[ $form ] ) == false ){
							$datos_formularios[ $form ]['cantidad'] = 0;
						}					
						if( array_key_exists( 'nombre_formulario', $datos_formularios[ $form ] ) == false ){
							$datos_formularios[ $form ]['nombre_formulario'] = $formularios_hce[$form]['nombre'];
						}
						$datos_formularios[ $form ]['cantidad']++;
						
						if( in_array( $row['historia']."-".$row['ingreso'], $datos_formularios[ $form ]['historias'] ) == false ){
							array_push( $datos_formularios[ $form ]['historias'], $row['historia']."-".$row['ingreso'] );
						}
						/*if( array_key_exists( $row['historia']."-".$row['ingreso'], $datos_formularios[ $form ]['historias'] ) == false ){					
							$rowsave = array();
							$rowsave['forms'] = 1;
							$rowsave['pac'] = $row['paciente'];
							$datos_formularios[ $form ]['historias'][$row['historia']."-".$row['ingreso']] = $rowsave;
						}else{
							$datos_formularios[ $form ]['historias'][$row['historia']."-".$row['ingreso']]['forms']++;
						}*/
				}
			}
		}
		
		if( $num == 0 ){
			echo "No hay datos con los parámetros ingresados";
			return;
		}
		
		echo "<div style='width: 1000px;'>";
		
		echo "<div class='desplegables' style='width:100%;'>";		
		echo "<h3><b>* CENTROS DE COSTOS *</b></h3>";
		echo "<div>";
		echo "<table class='tabla_r' align='center' width='85%'>";	
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Centro de costo</td>";		
		echo "<td align='center'>Historias</td>";		
		echo "<td align='center'>No de Formularios<br>Impresos</td>";		
		echo "<td align='center'>Páginas<br>Impresas</td>";		
		echo "</tr>";

		//Ordenar por paginas
		$volume = array();
		foreach ($datos_cco as $key => $row) {
			$volume[$key]  = $row['paginas'];
		}
		array_multisort($volume, SORT_DESC, $datos_cco);
		$c_his=0; $c_forms=0; $c_pags=0;
		foreach( $datos_cco as $cco ){
			echo "<tr class='fila1'>";
			echo "<td>".$cco['nombre_cco']."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_historias\")' style='cursor:pointer;'>".count($cco['historias'])."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_formularios\")' style='cursor:pointer;'>".$cco['formularios']."</td>";
			echo "<td align='center'>".$cco['paginas']."</td>";
			echo "</tr>";
			echo "<tr class='fila2 ocultable' style='display:none;'>";
			echo "<td colspan=4>";
			echo "<table  class='tabla_historias' align='center' width='75%'>";	
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";
			echo "<td align='center'>Historia<br>Ingreso</td>";		
			echo "<td align='center'>Paciente</td>";		
			echo "<td align='center'>No de Formularios<br>Impresos</td>";			
			echo "<td align='center'>Páginas<br>Impresas</td>";		
			echo "</tr>";
			foreach( $cco['historias'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='center'>".$clave."</td>";
				echo "<td align='left'>".$val['pac']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "<td align='center'>".$val['pags']."</td>";
				echo "</tr>";					
			}
			echo "</table>";
			echo "<input type='hidden' class='json_oculto' value='".json_encode($cco['solicitudes'])."' />";		
			echo "</td>";
			echo "</tr>";

			$c_his+=count($cco['historias']);
			$c_forms+=$cco['formularios'];
			$c_pags+=$cco['paginas'];
		}
		echo "<tr class='encabezadotabla'>";
		echo "<td>&nbsp;</td>";
		echo "<td align='center'>".$c_his."</td>";
		echo "<td align='center'>".$c_forms."</td>";
		echo "<td align='center'>".$c_pags."</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "</div>";
		//-------------------------------------
		echo "<div class='desplegables' style='width:100%;'>";
		echo "<h3><b>* ROLES *</b></h3>";
		echo "<div>";
		echo "<table class='tabla_r' align='center' width='85%'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Rol</td>";		
		echo "<td align='center'>Historias</td>";		
		echo "<td align='center'>No de Formularios<br>Impresos</td>";		
		echo "<td align='center'>Páginas<br>Impresas</td>";		
		echo "</tr>";
		//Ordenar por paginas
		$volume = array();
		foreach ($datos_rol as $key => $row) {
			$volume[$key]  = $row['paginas'];
		}
		array_multisort($volume, SORT_DESC, $datos_rol);
		$c_his=0; $c_forms=0; $c_pags=0;
		foreach( $datos_rol as $rol ){
			echo "<tr class='fila1'>";
			echo "<td onclick='mostrar_ocultar(this, \"tabla_roles\")' style='cursor:pointer;'>".$rol['nombre_rol']."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_historias\")' style='cursor:pointer;'>".count($rol['historias'])."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_formularios\")' style='cursor:pointer;'>".$rol['formularios']."</td>";
			echo "<td align='center'>".$rol['paginas']."</td>";
			echo "</tr>";
			
			echo "<tr class='fila2 ocultable' style='display:none;'>";
			echo "<td colspan=4>";
			
			
			echo "<table align='center' class='tabla_historias' width='75%'>";		//TABLA DE HISTORIAS
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";
			echo "<td align='center'>Historia<br>Ingreso</td>";		
			echo "<td align='center'>Paciente</td>";		
			echo "<td align='center'>No de Formularios<br>Impresos</td>";			
			echo "<td align='center'>Páginas<br>Impresas</td>";		
			echo "</tr>";
			foreach( $rol['historias'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='center'>".$clave."</td>";
				echo "<td align='left'>".$val['pac']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "<td align='center'>".$val['pags']."</td>";
				echo "</tr>";					
			}
			echo "</table>";
			
			echo "<table align='center' class='tabla_roles' width='75%'>";		//TABLA DE USUARIOS
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";
			echo "<td align='center'>Usuario</td>";				
			echo "<td align='center'>No de Formularios<br>Impresos</td>";			
			echo "<td align='center'>Páginas<br>Impresas</td>";		
			echo "</tr>";
			foreach( $rol['usuarios'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='left'>".$val['usuario']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "<td align='center'>".$val['pags']."</td>";
				echo "</tr>";					
			}
			echo "</table>";
			echo "<input type='hidden' class='json_oculto' value='".json_encode($rol['solicitudes'])."' />";
			
			echo "</td>";
			echo "</tr>";
			
			$c_his+=count($rol['historias']);
			$c_forms+=$rol['formularios'];
			$c_pags+=$rol['paginas'];
		}
		echo "<tr class='encabezadotabla'>";
		echo "<td>&nbsp;</td>";
		echo "<td align='center'>".$c_his."</td>";
		echo "<td align='center'>".$c_forms."</td>";
		echo "<td align='center'>".$c_pags."</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "</div>";
		//-------------------------------------
		echo "<div class='desplegables' style='width:100%;'>";
		echo "<h3><b>* USUARIOS *</b></h3>";
		echo "<div>";
		echo "<table class='tabla_r' align='center' width='85%'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Usuario</td>";		
		echo "<td align='center'>Historias</td>";		
		echo "<td align='center'>No de Formularios<br>Impresos</td>";		
		echo "<td align='center'>Páginas<br>Impresas</td>";		
		echo "</tr>";
		//Ordenar por paginas
		$volume = array();
		foreach ($datos_usuario as $key => $row) {
			$volume[$key]  = $row['paginas'];
		}
		array_multisort($volume, SORT_DESC, $datos_usuario);
		$c_his=0; $c_forms=0; $c_pags=0;
		foreach( $datos_usuario as $usuario ){
			echo "<tr class='fila1'>";
			echo "<td>".$usuario['nombre_usuario']."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_historias\")' style='cursor:pointer;'>".count($usuario['historias'])."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_formularios\")' style='cursor:pointer;'>".$usuario['formularios']."</td>";
			echo "<td align='center'>".$usuario['paginas']."</td>";
			echo "</tr>";

			echo "<tr class='fila2 ocultable' style='display:none;'>";
			echo "<td colspan=4>";
			echo "<table class='tabla_historias' align='center' width='75%'>";	
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";
			echo "<td align='center'>Historia<br>Ingreso</td>";		
			echo "<td align='center'>Paciente</td>";		
			echo "<td align='center'>No de Formularios<br>Impresos</td>";			
			echo "<td align='center'>Páginas<br>Impresas</td>";		
			echo "</tr>";
			foreach( $usuario['historias'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='center'>".$clave."</td>";
				echo "<td align='left'>".$val['pac']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "<td align='center'>".$val['pags']."</td>";
				echo "</tr>";					
			}
			echo "</table>";
			echo "<input type='hidden' class='json_oculto' value='".json_encode($usuario['solicitudes'])."' />";
			echo "</td>";
			echo "</tr>";
			
			$c_his+=count($usuario['historias']);
			$c_forms+=$usuario['formularios'];
			$c_pags+=$usuario['paginas'];
		}
		echo "<tr class='encabezadotabla'>";
		echo "<td>&nbsp;</td>";
		echo "<td align='center'>".$c_his."</td>";
		echo "<td align='center'>".$c_forms."</td>";
		echo "<td align='center'>".$c_pags."</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "</div>";
		//-------------------------------------
		echo "<div class='desplegables' style='width:100%;'>";
		echo "<h3><b>* GRUPOS DE EMPRESAS *</b></h3>";
		echo "<div>";
		echo "<table class='tabla_r' align='center' width='85%'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Grupo de empresas</td>";		
		echo "<td align='center'>Historias</td>";		
		echo "<td align='center'>No de Formularios<br>Impresos</td>";		
		echo "<td align='center'>Páginas<br>Impresas</td>";		
		echo "</tr>";
		//Ordenar por paginas
		$volume = array();
		foreach ($datos_gruposemp as $key => $row) {
			$volume[$key]  = $row['paginas'];
		}
		array_multisort($volume, SORT_DESC, $datos_gruposemp);
		$c_his=0; $c_forms=0; $c_pags=0;
		foreach( $datos_gruposemp as $grupo ){
			echo "<tr class='fila1'>";
			echo "<td onclick='mostrar_ocultar(this, \"tabla_entidades\")' style='cursor:pointer;'>".$grupo['nombre_grupo']."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_historias\")' style='cursor:pointer;'>".count($grupo['historias'])."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_formularios\")' style='cursor:pointer;'>".$grupo['formularios']."</td>";
			echo "<td align='center'>".$grupo['paginas']."</td>";
			echo "</tr>";
			
			echo "<tr class='fila2 ocultable' style='display:none;'>";
			echo "<td colspan=4>";
			
			echo "<table class='tabla_historias' align='center' width='75%'>";		//TABLA DE HISTORIAS
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";
			echo "<td align='center'>Historia<br>Ingreso</td>";		
			echo "<td align='center'>Paciente</td>";		
			echo "<td align='center'>No de Formularios<br>Impresos</td>";			
			echo "<td align='center'>Páginas<br>Impresas</td>";		
			echo "</tr>";
			foreach( $grupo['historias'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='center'>".$clave."</td>";
				echo "<td align='left'>".$val['pac']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "<td align='center'>".$val['pags']."</td>";
				echo "</tr>";					
			}
			echo "</table>";
			
	
			echo "<table class='tabla_entidades' align='center' width='75%'>";		//TABLA DE ENTIDADES
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";	
			echo "<td align='center'>Entidad</td>";		
			echo "<td align='center'>No de Formularios<br>Impresos</td>";			
			echo "<td align='center'>Páginas<br>Impresas</td>";		
			echo "</tr>";
			foreach( $grupo['entidades'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='left'>".$val['entidad']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "<td align='center'>".$val['pags']."</td>";
				echo "</tr>";					
			}
			echo "</table>";
			echo "<input type='hidden' class='json_oculto' value='".json_encode($grupo['solicitudes'])."' />";
			
			echo "</td>";
			echo "</tr>";
			
			$c_his+=count($grupo['historias']);
			$c_forms+=$grupo['formularios'];
			$c_pags+=$grupo['paginas'];
		}
		echo "<tr class='encabezadotabla'>";
		echo "<td>&nbsp;</td>";
		echo "<td align='center'>".$c_his."</td>";
		echo "<td align='center'>".$c_forms."</td>";
		echo "<td align='center'>".$c_pags."</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "</div>";
		//-------------------------------------
		echo "<div class='desplegables' style='width:100%;'>";
		echo "<h3><b>* MODALIDADES *</b></h3>";
		echo "<div>";
		echo "<table class='tabla_r' align='center' width='85%'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Modalidad</td>";		
		echo "<td align='center'>Historias</td>";		
		echo "<td align='center'>No de Formularios<br>Impresos</td>";		
		echo "<td align='center'>Páginas<br>Impresas</td>";		
		echo "</tr>";
		//Ordenar por paginas
		$volume = array();
		foreach ($datos_modalidades as $key => $row) {
			$volume[$key]  = $row['paginas'];
		}
		array_multisort($volume, SORT_DESC, $datos_modalidades);
		$c_his=0; $c_forms=0; $c_pags=0;
		foreach( $datos_modalidades as $modalidad ){
			echo "<tr class='fila1'>";
			echo "<td>".$modalidad['nombre_modalidad']."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_historias\")' style='cursor:pointer;'>".count($modalidad['historias'])."</td>";
			echo "<td align='center' onclick='mostrar_ocultar(this, \"tabla_formularios\")' style='cursor:pointer;'>".$modalidad['formularios']."</td>";
			echo "<td align='center'>".$modalidad['paginas']."</td>";
			echo "</tr>";
			echo "<tr class='fila2 ocultable' style='display:none;'>";
			echo "<td colspan=4>";
			echo "<table class='tabla_historias' align='center' width='75%'>";	
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";
			echo "<td align='center'>Historia<br>Ingreso</td>";		
			echo "<td align='center'>Paciente</td>";		
			echo "<td align='center'>No de Formularios<br>Impresos</td>";		
			echo "<td align='center'>Páginas<br>Impresas</td>";		
			echo "</tr>";
			foreach( $modalidad['historias'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='center'>".$clave."</td>";
				echo "<td align='left'>".$val['pac']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "<td align='center'>".$val['pags']."</td>";
				echo "</tr>";					
			}
			echo "</table>";
			echo "<input type='hidden' class='json_oculto' value='".json_encode($modalidad['solicitudes'])."' />";
			
			echo "</td>";
			echo "</tr>";
			
			$c_his+=count($modalidad['historias']);
			$c_forms+=$modalidad['formularios'];
			$c_pags+=$modalidad['paginas'];
		}
		echo "<tr class='encabezadotabla'>";
		echo "<td>&nbsp;</td>";
		echo "<td align='center'>".$c_his."</td>";
		echo "<td align='center'>".$c_forms."</td>";
		echo "<td align='center'>".$c_pags."</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "</div>";
		//-------------------------------------
		echo "<div class='desplegables' style='width:100%;'>";
		echo "<h3><b>* FORMULARIOS *</b></h3>";
		echo "<div>";
		echo "<table class='tabla_r' align='center' width='85%'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Formulario</td>";		
		echo "<td align='center'>Historias</td>";		
		echo "<td align='center'>No de veces que<br>se mando a imprimir</td>";		
		echo "</tr>";
		//Ordenar por cantidad
		$volume = array();
		foreach ($datos_formularios as $key => $row) {
			$volume[$key]  = $row['cantidad'];
		}
		array_multisort($volume, SORT_DESC, $datos_formularios);	
	
		foreach( $datos_formularios as $formu ){
			echo "<tr class='fila1'>";
			echo "<td>".$formu['nombre_formulario']."</td>";
			echo "<td align='center'>".count($formu['historias'])."</td>";
			echo "<td align='center'>".$formu['cantidad']."</td>";
			echo "</tr>";
			echo "<tr class='fila2 ocultable' style='display:none;'>";
			echo "<td colspan=4>";
			echo "<table align='center' width='75%'>";	
			echo "<tr class='encabezadotabla' style='background-color:#999999;'>";
			echo "<td align='center'>Historia<br>Ingreso</td>";		
			echo "<td align='center'>Paciente</td>";		
			echo "<td align='center'>Impresiones</td>";			
			echo "</tr>";
			/*foreach( $formu['historias'] as $clave=>$val ){
				echo "<tr class='fila1' style='background-color:#cccccc;'>";
				echo "<td align='center'>".$clave."</td>";
				echo "<td align='left'>".$val['pac']."</td>";
				echo "<td align='center'>".$val['forms']."</td>";
				echo "</tr>";					
			}*/
			echo "</table>";
			echo "</td>";
			echo "</tr>";
			
		}
		echo "</table>";
		echo "</div>";		
		echo "</div>";	
		
		echo "</div>";	
		
	}
	
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		global $conex;
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		
		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
		$empresa = strtolower($institucion->baseDeDatos);
		encabezado("ESTADISTICAS CENTRO DE IMPRESIÓN", $wactualiz, $empresa);
		
		echo "<center>";
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';

		echo '<br><br>';

		echo '<div style="width: 100%">';
		
		
		//---DIV PRINCIPAL
		echo '<table align="center">';
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center' class='encabezadotabla'>Fecha Inicial</td>";
		echo "<td align='center' class='encabezadotabla'>Fecha Final</td>";
		echo "</tr>";
		echo "<tr>";		
		echo "<td class='fila2'><input type='text' id='fecha_i' value='".date("Y-m")."-01' /></td>";		
		echo "<td class='fila2'><input type='text' id='fecha_f' value='".date("Y-m-d")."'/></td>";
		echo "</tr>";		
		echo "</table>";
		
		echo "<input type=button id='btn_consultarfechas' value='Consultar' onClick='javascript:consultarFechas()' />";
		echo "<br><br>";
		echo '<div id="resultados_lista" align="center"></div>';
		echo "<br><br>";

		//------FIN FORMULARIO------
		echo "</div>";//Gran contenedor
		
		
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "<br><br>"; 
		echo "<br><br>";
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		echo '</center>';
	}
?>
<style>	
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

	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>

<script>
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
	
</script>
<script>
	//Variable de estado que indica si se esta moviendo un producto
	var moviendo_global = false;
	var datos_paciente = new Object();
	var mostrandoMenu = false;
	
//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});
		
		$("#fecha_i, #fecha_f").datepicker({
		  showOn: "button",
		  buttonImage: "../../images/medical/root/calendar.gif",
		  buttonImageOnly: true,
		  maxDate:"+0D"
		});		
	});
	
	function mostrar_ocultar( ele, clase_tabla ){
		ele = jQuery(ele); //ele es el td
		ele = ele.parent(); //ele es el tr que contiene el td
		ele = ele.next(); //ele es el tr que le sigue
		//ele.toggle();
		if( ele.is(":visible") ){ //El tr con las tablas de los detalles esta visible
			if( ele.find("."+clase_tabla).is(":visible") ){ //Di click para cerrar la tabla del mismo detalle
				ele.hide(); //Oculto el tr que contiene las tablas
			}else{	//Di click para ver otro detalle
				ele.find("table").hide(); //oculto todas las tablas
				ele.find("."+clase_tabla).show(); //muestro la tabla del detalle que solicite
			}
		}else{
			ele.find("table").hide(); //oculto todas las tablas
			ele.show(); //Muestro el tr que contiene las tablas
			ele.find("."+clase_tabla).show();		
		}
		
		if( ele.find("."+clase_tabla).length == 0 ){		//La tabla no existe, hay que traerla con AJAX
			var datos_ocultos = ele.find(".json_oculto").val();
			var wemp_pmla = $("#wemp_pmla").val();
			$.blockUI({ message: $('#msjEspere') });
			
			$.post('estadisticas_cenimp.php', { wemp_pmla: wemp_pmla, action: "consultartabla", datos: datos_ocultos, consultaAjax: "0"} ,
				function(data) {
					$.unblockUI();
					ele.find("table").eq(0).after( data );	//Pegar el data (que debe contener la tabla que traje) luego de la primer tabla que haya en el tr oculto
					ele.find("."+clase_tabla).show();
				});
			return;
		}		
		ele.find("."+clase_tabla).show();
	}
	
	function consultarFechas(){
	
		$.blockUI({ message: $('#msjEspere') });
		var wfechai = $("#fecha_i").val();
		var wfechaf = $("#fecha_f").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		$.post('estadisticas_cenimp.php', { wemp_pmla: wemp_pmla, action: "consultarestadisticas", wfechai: wfechai, wfechaf: wfechaf, consultaAjax: "0"} ,
			function(data) {
				$.unblockUI();
				$('#resultados_lista').html(data);
				$("#resultados_lista").show();
				//$("#btn_consultarfechas").hide();
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$( ".desplegables" ).accordion({
					collapsible: true,
					active:0,
					heightStyle: "content",
					icons: null
				});
			});
	}
	
	function usarFiltros(){
		var wfechai = $("#fecha_i").val();
		var wfechaf = $("#fecha_f").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		var piso = $("#lista_pisos").val();
		var rol = $("#lista_roles").val();
		var usuario = $("#lista_usuarios").val();
		var formulario = $("#lista_formularios").val();
		var empresa = $("#lista_empresas").val();
		var modalidad = $("#lista_modalidades").val();
		
		$.blockUI({ message: $('#msjEspere') });
		
		$.post('estadisticas_cenimp.php', { wemp_pmla: wemp_pmla, action: "consultarestadisticasfiltros", wfechai: wfechai, wfechaf: wfechaf, piso:piso, rol:rol, usuario:usuario, formulario:formulario, empresa:empresa, modalidad:modalidad, consultaAjax: "0"} ,
			function(data) {
				$.unblockUI();
				$('#resultados_lista').html(data);
				$("#resultados_lista").show();
				//$("#btn_consultarfechas").hide();
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$( ".desplegables" ).accordion({
					collapsible: true,
					active:0,
					heightStyle: "content",
					icons: null
				});				
				$("#lista_pisos").val( piso );
				$("#lista_roles").val( rol );
				$("#lista_usuarios").val( usuario );
				$("#lista_formularios").val( formulario );
				$("#lista_empresas").val( empresa );
				$("#lista_modalidades").val( modalidad );
			});
	}
	
	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		//Si esta visible la tabla de menu...
		$("#lista_pisos option").eq(0).attr('selected',true); //llevar la opcion 0 a la lista de pisos
		$("#resultados_lista").hide( 'drop', {}, 500 ); //esconder lista de pacientes
		$("#enlace_retornar").hide(); //esconder enlace retornar
	}
	
	
	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 1600 );
	}
</script>
</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>