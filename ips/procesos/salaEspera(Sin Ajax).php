<script>

	function historiaAbierta(){

		tbTables = document.getElementsByTagName( "table" );

		for( i = 2; i < tbTables.length; i++ ){

			for( j = 0; j < tbTables[i].rows.length; j++ ){

				if( tbTables[i].rows.cells[ tbTables[i].rows.length - 1 ].childNode[0].tagName != "INPUT" ){
					return true;
				}
			}
		}

		for( i = 0; i < document.forms[0].rdHistoria.length; i++ ){

			if( document.forms[0].rdHistoria[i].checked == true ){
				return true;
			}
		}

		return false;
		
	}

	function abrirVentana( key, medico, adicion, metodo, id, wrk ){

		if( metodo == 1 ){
			var ancho=screen.width;
			var alto=screen.availHeight;
			var v = window.open( '../../det_registro.php?id=0&pos1='+medico+'&pos2=0&pos3=0&pos4=000139&pos5=0&pos6='+key+'&tipo=P&Valor=&Form=000139-clisur-C-HistoriaClinica&call=1&change=0&key='+key+'&Pagina=1'+adicion+'&wrk='+wrk,'','scrollbars=1, width='+ancho+', height='+alto );
			v.moveTo(0,0);
		}
		else{
			var ancho=screen.width;
			var alto=screen.availHeight;
			var v = window.open( '../../det_registro.php?id='+id+'&pos1='+medico+'&pos2=2010-01-26&pos3=04:09:25&pos4=000139&pos5=0&pos6='+key+'&tipo=P&Valor=&Form=000139-'+medico+'-C-HistoriaClinica&call=1&change=0&key='+key+'&Pagina=1'+adicion+'&wrk='+wrk,'','scrollbars=1, width='+ancho+', height='+alto );
			v.moveTo(0,0);
		}

//		if( metodo == 1 ){
//			document.forms[0].elements[ 'rdHistoria' ].value = '../../det_registro.php?id=0&pos1='+medico+'&pos2=0&pos3=0&pos4=000139&pos5=0&pos6='+key+'&tipo=P&Valor=&Form=000139-clisur-C-HistoriaClinica&call=1&change=0&key='+key+'&Pagina=1'+adicion
//		}
//		else{
//			document.forms[0].elements[ 'rdHistoria' ].href = '../../det_registro.php?id='+id+'&pos1='+medico+'&pos2=2010-01-26&pos3=04:09:25&pos4=000139&pos5=0&pos6='+key+'&tipo=P&Valor=&Form=000139-'+medico+'-C-HistoriaClinica&call=1&change=0&key='+key+'&Pagina=1'+adicion
//		}

//		../../det_registro.php?id=1&pos1=medico&pos2=2010-01-26&pos3=04:09:25&pos4=000139&pos5=0&pos6=03150&tipo=P&Valor=&Form=000139-clisur-C-HistoriaClinica&call=1&change=0&key=03150&Pagina=1
	}

	function cambiarSelect(){
		document.forms[0].submit();
	}

	//Da de alta a un paciente
	function darAlta( pac ){

		if( confirm( "Esta seguro que quiere dar de alta al paciente?" ) ){
			var aux = document.createElement( "div" );

			aux.innerHTML = "<INPUT type='hidden' value='"+pac+"' name='alta'>";

			document.forms[0].appendChild( aux.firstChild );
			document.forms[0].submit();
		}
	}
</script>

<?php
/**
 * Programa:	salaEspera.php
 * Por:			Edwin Molina Grisales
 * Fecha:		2010-01-13
 * Descripcion:	
 */

/**
 * Variables del sistema
 * 
 * $add			Campos adicionales para crear el link a la historia clinica
 * $pos			Posicion del campo nro de documento de la historia clinica
 */

 /*Modificaciones
 2012-11-25: Se modifican las funciones actualizarTabla y crearTabla para que permita traer citas con ingreso que no sea del mismo dia de la cita, 
				ya que para domiciliaria es necesario que se permitan las citas las cuales tienen ingreso de dias atras.
 2012-07-27: Se modifican las consultas para separar la las consultas de medicina domiciliaria y de la atencion clinica. Viviana Rodas
 2012-07-30: Se agregan los parametros para separar los tipos de atencion y si es director medico (dm y medom). Viviana Rodas
 2012-08-03: Se modifica el programa para que los pacientes de la sala de espera domiciliaria no se borren. Viviana Rodas
 2012-08-09: Se agregan los campos Servicio y Tipo Servicio para mostrar si es tipo medicina domiciliaria y 
             si es el servicio conico o agudo u hospitalizado. Viviana Rodas.
 2012-08-15: Se modifica la consulta $opcion==2 && $medom==off para que muestre los de hoy y los de los dias pasados que no se hayan borrado. Viviana Rodas
 2012-09-03: Se agrega el boton de cerrar. Viviana Rodas.
 
 */
/********************************************************************************************************
 * FUNCIONES
 *******************************************************************************************************/

/****************************************************************************************************************
 * Da de alta a un paciente, tanto en la HCE como en la sala de espera
 * 
 * @param $id
 * @return unknown_type
 ****************************************************************************************************************/
function darAlta( $id ){
	
	global $conex;
	global $wbasedato;
	global $key;
	
	if( empty( $id ) ){
		return;
	}
	
	$val = false;
	
	$sql = "UPDATE
				{$wbasedato}_000141
			SET
				espalt = 'on'
			WHERE
				id = '$id'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		
		$sql = "UPDATE
					{$wbasedato}_000141 a, {$wbasedato}_000139 b 
				SET
					hclrem = '01-ALTA'
				WHERE
					hclhis = esphis
					AND hcling = esping
					AND a.id = '$id'
				";
	
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			//return true;
		}
		
		//Falta encontrar la historia y el ingreso
		$sql = "SELECT
					esphis, esping
				FROM
					{$wbasedato}_000141
				WHERE
					id = '$id'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			
			$his = $rows[ 'esphis' ];
			$ing = $rows[ 'esping' ];
		
			//Ingresando los datos necesarios en la tabla POSTCONSULTA
			$sql = "INSERT INTO {$wbasedato}_000142 
								(     medico    ,      fecha_data    ,      hora_data     , poshis,  posing, pospro,     posfec         ,       poshor       ,  posdes   ,    Seguridad  )
						VALUES  ( '{$wbasedato}', '".date("Y-m-d")."', '".date("H:i:s")."', '$his',  '$ing', '$key', '".date("Y-m-d")."', '".date("H:i:s")."', '01-ALTA' , '{$wbasedato}' )";
			
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			if( mysql_affected_rows() > 0 ){
				$val = true;
			}
		}
		
		return true;
	}
	
	return $val;
}

/**
 * Indica si un paciente se encuentra en sala de espera
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function enSalaDeEsperaHora( $his, $ing, $hi ){
	
	global $conex;
	global $wbasedato;
	
	$val = false;
	
	$sql = "SELECT 
				Esphis 
			FROM 
				{$wbasedato}_000141 
			WHERE 
				Esphis = '$his' 
				AND esphor = TIME_FORMAT( '$hi','%H:%i:%s') 
				AND espfec = '".date("Y-m-d")."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return true;
	}
	
	return $val;
}

/**
 * Indica si un paciente se encuentra en sala de espera
 * 
 * @param $his
 * @param $ing
 * @param $conFecha		indica si debe validar si esta en sala de espera por fecha
 * @return unknown_type
 * 
 * Nota:  La validaciòn por fecha se hace para los pacientes que tengan cita
 */
function enSalaDeEspera( $his, $ing, $conFecha ){
	
	global $conex;
	global $wbasedato;
	
	$val = false;
	
	if( $conFecha ){
	
		$sql = "SELECT 
					Esphis 
				FROM 
					{$wbasedato}_000141 
				WHERE 
					Esphis = '$his' 
					AND esping = '$ing'
					AND espfec = '".date( "Y-m-d" )."'
				";
				
	}
	else{
				
		$sql = "SELECT 
					Esphis 
				FROM 
					{$wbasedato}_000141 
				WHERE 
					Esphis = '$his' 
					AND esping = '$ing'
				";
	}
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return true;
	}
	
	return $val;
}

/**
 * 
 * @param $cod
 * @return unknown_type
 */
function nombreMedicoPorCodigo( $cod ){
	
	global $conex;
	global $wbasedato;
	
	$val = "GENERAL";
	
	$sql = "SELECT
				mednom
			FROM
				{$wbasedato}_000051
			WHERE
				medcod = '$cod'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$val = $rows['mednom'];
	}
	
	return $val;
}

/**
 * Busca el nombre del Medico por el codigo de usuario
 * 
 * @param $usu
 * @return unknown_type
 */
function nombreMedicoPorUsuario( $usu ){
	
	global $conex;
	global $wbasedato;
	
	$val = '.';
	
	if( !empty($usu) ){
		$sql = "SELECT
					mednom
				FROM
					{$wbasedato}_000051
				WHERE
					medusu = '$usu'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			
			$val = $rows['mednom'];
		}
	}
	
	return $val;
}

/**
 * Busca el id de la historia clinica de un paciente segun la historia y el ingreso
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function idHC( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = 'off';
	
	$sql = "SELECT 
				id 
			FROM 
				{$wbasedato}_000139 
			WHERE 
				hclhis LIKE '$his' 
				AND hcling LIKE '$ing'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$val = $rows['id'];
	}
	
	return $val;
}

/**
 * Se buscan registros que se encuentren en sala de espera del medico (usuario)
 * y que aparezcan con historia abierta y no se tengan abiertas en ese momento
 * 
 * @return unknown_type
 */
function desbloqueandoRegistros( $cod ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "UPDATE
				{$wbasedato}_000141 a, 
				{$wbasedato}_000051 b
			SET
				espest = 'on',
				esphin = '00:00:00'
			WHERE
				espfec = '".date("Y-m-d")."'
				AND espate = 'off'
				AND espest = 'off'
				AND esphin != '00:00:00'
				AND esphsa = '00:00:00'
				AND espmtr = medusu
				AND medcod = '$cod'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	
	$sql = "UPDATE
				{$wbasedato}_000139 a,
				{$wbasedato}_000141 b,
				{$wbasedato}_000051 c
			SET
				espest = 'on'
			WHERE
				hclhis = esphis
				AND hcling = esping
				AND hclrem like '04-%'
				AND espfec = '".date("Y-m-d")."'
				AND espest = 'off'
				AND espmtr = medusu
				AND medcod = '$cod'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
				
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else
	{
		return false;	
	}
				
}

/**
 * 
 * @param $filtro
 * @param $codMed
 * @return unknown_type
 */
function filtroPorMedico( $filtro ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				mednom, medcod, medusu
			FROM
				{$wbasedato}_000051
			WHERE
				medest = 'on'
				AND medcod like '%'
				AND medusu != ''
				AND medusu not like 'NO%'";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_num_rows( $res ) > 0 ){
		
		echo "<br><br>";
		echo "<table align='center'>";
		echo "	<tr class='encabezadotabla'>";
		echo "		<td align='center'>Filtro Por Profesional</td>";
		echo "	</tr>";
		
		echo "	<tr class='fila1'>";
		echo "<td>";
		
		echo "<select name='filtro' onChange='javascript: cambiarSelect();'>";
		echo "<option value='%'>% - Todos</option>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			if( $rows['medusu'] == "$filtro" ){
				echo "<option value='{$rows['medusu']}' selected>{$rows['medcod']}-{$rows['mednom']}</option>";
			}
			else{
				echo "<option value='{$rows['medusu']}'>{$rows['medcod']}-{$rows['mednom']}</option>";
			}
		}
		
		echo "</select>";
		
		echo "</td>";
		echo "	</tr>";
		
		echo "</table>";
	}
}

/**
 * Indica si el doctor tiene abierta una historia clinica que se encuentra en la sala de espera
 * 
 * @param $cod				Codigo del Medico que esta viendo la sala de espera
 * @return unknown_type
 */
function historiaAbierta( $cod ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				esphis
			FROM
				{$wbasedato}_000141 a, 
				{$wbasedato}_000051 b
			WHERE
				espfec = '".date("Y-m-d")."'
				AND espate = 'off'
				AND espest = 'off'
				AND esphin != '00:00:00'
				AND esphsa = '00:00:00'
				AND espmtr = medusu
				AND medcod = '$cod'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		echo "1";
	}
	else{
		
		$sql = "SELECT
					hclhis
				FROM
					{$wbasedato}_000139 a, 
					{$wbasedato}_000141 b, 
					{$wbasedato}_000051 c
				WHERE
					hclhis = esphis 
					AND hcling = esping  
					AND espfec = '".date("Y-m-d")."'
					AND hclrem like '04-%'
					AND espest = 'off'
					AND espmtr = medusu
					AND medcod = '$cod'
				";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			echo "1";
		}
		else{
			echo "0";
		}	
	}
}

/**
 * Devuelve la solucion de citas para el codigo de la empresa dada
 * @param $codEmp
 * @return unknown_type
 */
function solucionCitas( $codEmp ){
	
	global $conex;
	
	$solucionCitas = '';
	
	$sql = "SELECT
				detval
			FROM
				root_000051
			WHERE
				detapl = 'citas'
				AND detemp = '$codEmp'
			";
	
	$res = mysql_query( $sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$solucionCitas = $rows[0];
	}
	
	return $solucionCitas;
}

/**
 * Borra los registros no necesarios de la tabla 00141
 * @return unknown_type
 */
function borrarAnteriores(){
	
	global $wbasedato;
	global $conex;
	
	$fecini = mktime(0,0,0, date("m"), date("d")-2, date("Y") );
	$fecfin = mktime(0,0,0, date("m"), date("d")-1, date("Y") );
	$fecini = date( "Y-m-d", $fecini );
	$fecfin = date( "Y-m-d", $fecfin );
	
	//No se borra los registros de los ultimos dos dias
	$sql = "DELETE  
				{$wbasedato}_000141
			FROM {$wbasedato}_000141, {$wbasedato}_000100
			WHERE
				espfec < '$fecini'
				and pactat != 'M'
				AND pachis = esphis
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	
	//Borrando todos los registros que no esten activos en el sistema con fecha de hoy y no han sido atendidos
	$sql = "DELETE 
				{$wbasedato}_000141 
			FROM 
				{$wbasedato}_000141, {$wbasedato}_000100
			WHERE
				espfec = '".date("Y-m-d")."'
				AND espate = 'off'
				AND pacact != 'on'
				AND pachis = esphis
			";
				
	$sql = "DELETE 
				{$wbasedato}_000141 
			FROM 
				{$wbasedato}_000141, {$wbasedato}_000100
			WHERE
				pacact != 'on'
				AND pachis = esphis
			";
				
//				AND esphis IN (SELECT pachis FROM {$wbasedato}_000100 WHERE pacact != 'on' AND pachis = esphis)
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Inserta los datos en la tabla de sala de espera
 * 
 * @param $campos
 * @return unknown_type
 */
function insertarCampos( $campos, $nom = '' ){
	
	global $conex;
	global $wbasedato;
	
//	$nom = '';
	
	if( $nom == '' ){
		$nom = $campos['pacno1']." ".$campos['pacno2']." ".$campos['pacap1']." ".$campos['pacap2'];
	}
	
	$sql = "INSERT INTO {$wbasedato}_000141
					(   medico    ,    fecha_data       ,     hora_data       ,       esphis         ,       esping        ,       espdoc         , espnpa,       espmed         ,  esphor          , espest,      espfec        , espate,  esphin   ,   esphsa  ,espmtr,   Seguridad    )
			VALUES	( '$wbasedato', '".date( "Y-m-d" )."','".date( "H:i:s" )."', '{$campos['pachis']}','{$campos['ingnin']}', '{$campos['pacdoc']}', '$nom', '{$campos['medcod']}', '{$campos['hi']}',  'on' , '".date("Y-m-d")."', 'off', '00:00:00', '00:00:00',  ''  , 'C-$wbasedato' )";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Actualiza la tabla de sala de espera
 * 
 * @return unknown_type
 */
function actualizarTabla(){
	
	global $conex;
	global $wbasedato;
	global $wemp2;
	global $medom;
	
	//Calculo el día anterior
	$fecini = date( "Y-m-d", mktime(0, 0, 0, date("m"), date("d")-1, date("Y")) );
	
	$val = false;
	
	if ($medom=='off')
	{
	$sql = "(SELECT
					pachis,
					MAX(ingnin*1) as ingnin,
					fecha,
					pacno1,
					pacno2,
					pacap1,
					pacap2,
					concat(hi,'00') as hi,
					pacdoc,
					medcod,
					'1' as tipo
				FROM
					{$wbasedato}_000100 a,
					{$wbasedato}_000101 b,
					{$wbasedato}_000051 c,
					{$wemp2}_000009 d
				WHERE
					pacact = 'on'
					AND pacdoc = cedula
					AND fecha = '".date( "Y-m-d" )."'
					AND medcid = cod_equ
					AND fecha = ingfei
					AND inghis = pachis
					AND pactat != 'M'
				GROUP BY 1,3,4,5,6,7,8,9,10,11
				)
				
				UNION
				
				(SELECT
					pachis,
					MAX(ingnin*1) as ingnin,
					'".date( "Y-m-d" )."' as fecha,
					pacno1,
					pacno2,
					pacap1,
					pacap2,
					'00:00:00' as hi,
					pacdoc,
					'-' as medcod,
					'2' as tipo
				FROM
					{$wbasedato}_000100 a,
					{$wbasedato}_000101 b
				WHERE
					pacact = 'on'
					AND pacdoc NOT IN (SELECT cedula FROM {$wemp2}_000009 c WHERE c.fecha=ingfei )
					AND ingtin = 'C'
					AND ingfei >= '".$fecini."'
					AND inghis = pachis
					AND pactat != 'M'
				GROUP BY 1,3,4,5,6,7,8,9,10,11
				)
				";
	}
	else  //si es domiciliaria
	{
		$sql = "(SELECT
				pachis,
				MAX(ingnin*1) as ingnin,
				fecha,
				pacno1,
				pacno2,
				pacap1,
				pacap2,
				concat(hi,'00') as hi,
				pacdoc,
				medcod,
				'1' as tipo
			FROM
				{$wbasedato}_000100 a,
				{$wbasedato}_000101 b,
				{$wbasedato}_000051 c,
				{$wemp2}_000009 d
			WHERE
				pacact = 'on'
				AND pacdoc = cedula
				AND fecha = '".date( "Y-m-d" )."'
				AND medcid = cod_equ
				
				AND asistida ='on'
				AND atendido ='on'
				AND inghis = pachis
				AND pactat = 'M'
			GROUP BY 1,3,4,5,6,7,8,9,10,11
			)
			
			UNION
			
			(SELECT
				pachis,
				MAX(ingnin*1) as ingnin,
				'".date( "Y-m-d" )."' as fecha,
				pacno1,
				pacno2,
				pacap1,
				pacap2,
				'00:00:00' as hi,
				pacdoc,
				'-' as medcod,
				'2' as tipo
			FROM
				{$wbasedato}_000100 a,
				{$wbasedato}_000101 b
			WHERE
				pacact = 'on'
				AND pacdoc NOT IN (SELECT cedula FROM {$wemp2}_000009 c WHERE c.fecha='".date("Y-m-d")."' )
				AND ingtin = 'C'
				AND ingfei >='".$fecini."'
				AND inghis = pachis
				AND pactat = 'M'				
			GROUP BY 1,3,4,5,6,7,8,9,10,11
			)
			";
	}
	//AND fecha = ingfei linea 753
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
		if( $rows['tipo'] == '1' ){
			
			if( !enSalaDeEspera( $rows['pachis'], $rows['ingnin'], true ) 
				|| !enSalaDeEsperaHora( $rows['pachis'], $rows['ingnin'], $rows['hi'] ) ){
				$val = insertarCampos( $rows );
			}
		}
		else{
			
			if( !enSalaDeEspera( $rows['pachis'], $rows['ingnin'], false ) ){
				$val = insertarCampos( $rows );
			}
		}
	}
	
//	actualizarTablaInterconsulta();
	
	return $val;
	
}

/**
 * Devuelve el codigo del doctor e informacion adicional del medico
 * 
 * @param $key
 * @return unknown_type
 */
function codigoDoctor( $key, &$infoMedico ){
	
	global $conex;
	global $wbasedato;
	
	$cod = '';
	
	$infoMedico['nom'] = '';
	$infoMedico['cod'] = '';
	$infoMedico['reg'] = '';
	
	$sql = "SELECT
				medcod, mednom, medcod, medreg, espesp
			FROM
				{$wbasedato}_000051 a, {$wbasedato}_000053 b
			WHERE
				medusu = '$key'
				AND espcod = SUBSTRING_INDEX( medesp, '-', 1 )
				AND medest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$cod = $rows['medcod'];
		$infoMedico['nom'] = $rows['mednom'];
		$infoMedico['cod'] = $rows['medcod'];
		$infoMedico['reg'] = $rows['medreg'];
		$infoMedico['esp'] = $rows['espesp'];
	}
	
	return $cod;
}

function crearTabla( $opcion, $codMedico, $key, $dir ){
	
	global $conex;
	global $wbasedato;
//	global $key;
//	global $dir;
	global $wemp_pmla;
	global $wemp2;
	global $filtro;
	global $dm;
	global $medom;

	if( $opcion == 1  && $medom=='off'){
							
		$sql = "(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					b.id as hc,
					inghin as hin,
					'on' as obs,
					esphin,
					ingent,
					ingfei,
					d.pactat,
					d.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000139 b, {$wbasedato}_000101 c, {$wbasedato}_000100 d
				WHERE
					esphis = hclhis 
					AND esping = hcling
					AND hclrem LIKE '04-%'
					AND esphis = inghis 
					AND esping = ingnin
					AND espalt != 'on'
					AND espmed like '%'
					AND pachis=inghis
					AND pactat !='M'
					
				ORDER BY ingfei asc, hin asc)
				
				UNION
				
				(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					'off' as hc,
					inghin as hin,
					'off' as obs,
					esphin,
					ingent,
					ingfei,
					c.pactat,
					c.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000101 b, {$wbasedato}_000100 c
				WHERE
					espmed like '$codMedico'
					AND espate != 'on'
					AND espalt != 'on'
					AND esphis = inghis 
					AND esping = ingnin
					AND pachis=inghis
					AND pactat !='M')
				
				UNION
				
				(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					'off' as hc,
					inghin as hin,
					'off' as obs,
					esphin,
					ingent,
					ingfei,
					c.pactat,
					c.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000101 b, {$wbasedato}_000100 c
				WHERE
					espate != 'on'
					AND esphis = inghis
					AND esping = ingnin
					AND espalt != 'on'
					AND espmed = '-'
					AND pachis=inghis
					AND pactat !='M'
				ORDER BY ingfei asc, hin asc)  
				";
	}
	else if ($opcion == 2  && $medom=='off'){
					
		$sql = "(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					b.id as hc,
					inghin as hin,
					'on' as obs,
					esphin,
					ingent,
					ingfei,
					d.pactat,
					d.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000139 b, {$wbasedato}_000101 c, {$wbasedato}_000100 d 
				WHERE
					hclhis = esphis
					AND hcling = esping
					AND hclrem LIKE '04-%'
					AND esphis = inghis 
					AND esping = ingnin 
					AND espalt != 'on'
					AND espmed like '%'
					AND pachis=inghis
					AND pactat !='M'
					
				ORDER BY ingfei asc, hin asc)
				
				UNION
				
				(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest, 
					a.id,
					'off' as hc,
					inghin as hin,
					'off' as obs,
					esphin,
					ingent,
					ingfei,
					c.pactat,
					c.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000101 b, {$wbasedato}_000100 c
				WHERE
					esphis = inghis 
					AND esping = ingnin
					AND espalt != 'on'
					AND espmed = '$codMedico'
					AND espate != 'on' 
					
					AND pachis=inghis
					AND pactat !='M'
				ORDER BY ingfei asc, esphor asc)
				";  //AND espfec = '".date("Y-m-d")."'
	}
	else if ($opcion == 1  && $medom=='on')
	{
		$sql = "(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					b.id as hc,
					inghin as hin,
					'on' as obs,
					esphin,
					ingent,
					ingfei,
					d.pactat,
					d.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000139 b, {$wbasedato}_000101 c, {$wbasedato}_000100 d
				WHERE
					esphis = hclhis 
					AND esping = hcling
					AND hclrem LIKE '04-%'
					AND esphis = inghis 
					AND esping = ingnin
					AND espalt != 'on'
					AND espmed like '%'
					AND pachis=inghis
					AND pactat ='M')
				
				UNION
				
				(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					'off' as hc,
					inghin as hin,
					'off' as obs,
					esphin,
					ingent,
					ingfei,
					c.pactat,
					c.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000101 b, {$wbasedato}_000100 c
				WHERE
					espmed like '$codMedico'
					AND espate != 'on'
					AND espalt != 'on'
					AND esphis = inghis 
					AND esping = ingnin
					AND pachis=inghis
					AND pactat ='M')
				
				UNION
				
				(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					'off' as hc,
					inghin as hin,
					'off' as obs,
					esphin,
					ingent,
					ingfei,
					c.pactat,
					c.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000101 b, {$wbasedato}_000100 c
				WHERE
					espate != 'on'
					AND esphis = inghis
					AND esping = ingnin
					AND espalt != 'on'
					AND espmed = '-'
					AND pachis=inghis
                    AND pactat ='M')					
				ORDER BY obs desc, ingfei asc, esphor asc
				";
	}
	else if ($opcion == 2  && $medom=='on')
	{
		$sql = "(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest,
					a.id,
					b.id as hc,
					inghin as hin,
					'on' as obs,
					esphin,
					ingent,
					ingfei,
					d.pactat,
					d.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000139 b, {$wbasedato}_000101 c, {$wbasedato}_000100 d 
				WHERE
					hclhis = esphis
					AND hcling = esping
					AND hclrem LIKE '04-%'
					AND esphis = inghis 
					AND esping = ingnin 
					AND espalt != 'on'
					AND espmed like '%'
					AND pachis=inghis
					AND pactat ='M')
				
				UNION
				
				(SELECT
					esphis, 
					esping, 
					espdoc, 
					espnpa, 
					espmed, 
					esphor, 
					espfec,
					espmtr,
					espest, 
					a.id,
					'off' as hc,
					inghin as hin,
					'off' as obs,
					esphin,
					ingent,
					ingfei,
					c.pactat,
					c.pactam,
					a.Hora_data
				FROM
					{$wbasedato}_000141 a, {$wbasedato}_000101 b, {$wbasedato}_000100 c
				WHERE
					esphis = inghis 
					AND esping = ingnin
					AND espalt != 'on'
					AND espmed = '$codMedico'
					AND espate != 'on' 
					
					AND pachis=inghis
					AND pactat ='M')
				ORDER BY obs desc, ingfei asc, esphor asc
				";
	}
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){

//		echo "<table align='center'>";
		$change = false;
		$aux = '';
		for( $i = 0; $rows = mysql_fetch_array( $res ) ; $i++ ){
			
			$rows['espmed'] = nombreMedicoPorCodigo( $rows['espmed'] );
			$rows['medtra'] = nombreMedicoPorUsuario( $rows['espmtr'] );

			$add = "&pacHis=".$rows['esphis'];
			$add.= "&idSala=".$rows['id'];
			
			if( $rows['obs'] == 'on' ){
				$add .= "&horaini=".$rows['esphin'];
			}
				
			//Definiendo la clase por cada fila
			if( $i%2 == 0 ){
				$class = "class='fila1'";
			}
			else{
				$class = "class='fila2'";
			}
			
			if( $aux != $rows['obs'] ){
				$change = true;
			}
			else{
				$change = false;
			}
			
			$aux = $rows['obs'];
			
			if( $i == 0 || $change ){
				
				if( $change && $i != 0 ){
					echo "</table><br><br>";
				}
				
				if( $rows['obs'] == 'on' ){
					echo "<center><b>PACIENTES EN OBSERVACION</b><center><br><br>";
				}
				else{
					echo "<center><b>PACIENTES EN SALA DE ESPERA</b><center><br><br>";
				}
				
				echo "<table align='center'>";
				
				echo "	<tr class='encabezadotabla' align='center'>";
				echo "		<td style='width:90'>";
				echo "Fecha de Ingreso";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "Hora ingreso";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "Fecha de la cita";
				echo "		</td>";
				echo "		<td style='width:70'>";
				echo "Hora de la cita";
				echo "		</td>";
				echo "		<td width=100>";
				echo "Historia";
				echo "		</td>";
				echo "		<td width=100>";
				echo "Nro. de<br>Documento";
				echo "		</td>";
				echo "		<td>";
				echo "Nombre del Paciente";
				echo "		</td>";
				echo "		<td>";
				echo "Responsable";
				echo "		</td>";
				
				if( $dir == 'dir' ){
					echo "		<td>";
					echo "Medico tratante";
					echo "		</td>";
				}
				
				echo "		<td>";
				echo "Servicio";
				echo "		</td>";
				echo "		<td>";
				echo "Tipo Servicio";
				echo "		</td>";
				echo "		<td style='width:100' align='center'>";
				echo "Ir a Historia/En consulta con";
				echo "		</td>";
				
				echo "		<td style='width:100' align='center'>";
				echo "Dar alta";
				echo "		</td>";
				echo "	</tr>";
			}

			echo "	<tr $class align='center'>";
			echo "		<td>";
			echo "			{$rows['ingfei']}";
			echo "		</td>";
			echo "		<td>";
			echo "			{$rows['hin']}";
			echo "		</td>";
			echo "		<td>";
			echo "			{$rows['espfec']}";
			echo "		</td>";
			echo "		<td>";
						if ($rows['esphor']=='00:00:00')
						{
							
						echo "{$rows['Hora_data']}";
						}
						else
						{
			echo "			{$rows['esphor']}";
						}
			echo "		</td>";
			echo "		<td>";
			echo "			{$rows['esphis']}";
			echo "		</td>";
			echo "		<td>";
			echo "			{$rows['espdoc']}";
			echo "		</td>";
			echo "		<td align='left'>";
			echo "			{$rows['espnpa']}";
			echo "		</td>";
			echo "		<td align='left'>";
			echo "			{$rows['ingent']}";
			echo "		</td>";
			
			if( $dir == 'dir' ){
				echo "		<td>";
				echo "			{$rows['espmed']}";
				echo "		</td>";
			}
			
			$tipoA=$rows['pactat'];
			$tipoS=$rows['pactam'];
			$tipoS=explode("-",$tipoS);
			$serv=$tipoS[0];
			@$serv1=$tipoS[1];
			
			//consulta para traer el nombre del servicio y el tipo de servicio
			$q = "select seldes from {$wbasedato}_000105 where seltip='02' and selcod = '".$tipoA."' ";
			$resq = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$numq = mysql_num_rows( $resq );
			
				if( $numq > 0 )
				{
					
					$rows1 = mysql_fetch_array( $resq );
					$aten = $rows1['seldes'];
				}
			
			echo "		<td align='center'>";
			echo "			$aten";
			echo "		</td>";
			echo "		<td align='center'>";
			echo "			$serv1";
			echo "		</td>";
			if( $rows['espest'] == 'on' ){
				
				$rows['hc'] = idHC( $rows['esphis'], $rows['esping'] );
				
				if( $rows['hc'] == 'off' ){
					echo "		<td>";
					echo "			<input type='radio' name='rdHistoria' onclick=\"javascript:abrirVentana( '$key', '$wbasedato', '$add', '1', '0','".mt_rand()."' );\">";
					echo "		</td>";
				}
				else{
					echo "		<td>";
					echo "			<input type='radio' name='rdHistoria' onclick=\"javascript:abrirVentana( '$key', '$wbasedato', '$add', '2', '{$rows['hc']}','".mt_rand()."' );\">";
					echo "		</td>";
				}
			}
			else{
				echo "		<td bgcolor='yellow' style='width:100'>";
				echo "			{$rows['medtra']}";
				echo "		</td>";
			}
			
			echo "<td>";
			echo "<INPUT type='button' onClick='javascript: darAlta( {$rows['id']} );' value='Dar alta'>";
			echo "</td>";
			
			echo "	</tr>";
			
		}
		echo "</table>";
	}
	else{
		echo "<center>SIN PACIENTES POR ATENDER</center>";
	}
	
	echo "<meta name='met' id='met' http-equiv='refresh' content='60;url=salaEspera(Sin ajax).php?wemp_pmla=$wemp_pmla&wemp2=$wemp2&dm=$dm&medom=$medom&filtro=$filtro&wkr=".mt_rand()."'>";
}

/********************************************************************************************************
 * FIN DE FUNCIONES
 *******************************************************************************************************/

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/

include("root/comun.php");

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));
$key2 = $key;

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

if( !isset($wemp2) ){
	$wemp2 = solucionCitas( $wemp_pmla );
}

//El usuario se encuentra registrado
session_start();  //agregado
if( !session_is_registered("user") ){
	echo "Error: Usuario No registrado";
}
else{
     
	 
	if ($medom=='off')
	{
		$titulo = "SALA DE ESPERA";
	}
	else
	{
		$titulo = "SALA DE ESPERA DOMICILIARIA";
	}
	$add='';
	$pos='';
	
	if( (isset($dm) && $dm == 'dir' && isset($medom) && $medom == 'off'&& !isset($filtro)) ){
		$filtro = '%';
		$titulo = "SALA DE ESPERA DE DIRECTOR MEDICO";
	}
	
	if( (isset($dm) && $dm == 'dir' && isset($medom) && $medom == 'on'&& !isset($filtro)) ){
	//if( (isset($dm) && $dm == 'dir' && isset($medom) && $medom == 'on' )){
		$filtro = '%';
		$titulo = "SALA DE ESPERA DOMICILIARIA DE DIRECTOR MEDICO";
	}
	
	encabezado( $titulo, "2012-11-25", "logo_".$wbasedato );
	
	//Si filtro existe es por que es una directora medica
	if( isset($filtro) ){
		if( $filtro != '%' && $filtro != '' ){
			$key = $filtro;
		}
	}
	
	if( !isset( $codMedico ) ){
		$codMedico = codigoDoctor( $key, $infoMedico );
		codigoDoctor( $key2, $infoMedico2 );
	}
	
	if( !isset($wkr) ){
		desbloqueandoRegistros( $infoMedico2['cod'] );
	}
	
	if( isset($filtro) ){
		if( $filtro == '%' ){
			$codMedico = $filtro;
		}
	}
	
	if( !empty($codMedico) ){

		actualizarTabla();
        //borra los pacientes de la lista que no se ha tocado la historia tres dias atras
		if ($medom=='off')
		{
			borrarAnteriores();
        }
		
		if( isset($alta) ){
			darAlta( $alta );
		}
		
		echo "<form name='pantalla' method=post>";
		echo "<br><br>";

		//Aqui comienza la lista de pacientes
		if( $infoMedico['esp'] == 'off' || $codMedico == '%' ){
			$opcion = 1;
			//Medico General
			//Si el Medico es General debe aperecer todos los pacientes que 
			//tengan cita con el mas aquellos que no tiene cita
		}
		else{
			
			$opcion = 2;
			//Medico Especialista
			//Solo salen los pacientes que tengan cita con el
		}
		
		if( @$dm == 'dir' && @$medom == 'off'){
			echo "<center><B>DIRECTOR(A) MEDICO(A)</B></center><BR>";
		}
		
		if( @$dm == 'dir' && @$medom == 'on'){
			echo "<center><B>DIRECTOR(A) MEDICO(A) DOMICILIARIA</B></center><BR>";
		}
		
		//Informacion Básica del Médico
		echo "<table align='center'>";
		echo "	<tr>";
		echo "		<td class='encabezadotabla' style='width:100'>";
		echo "			Profesional: ";
		echo "		</td>";
		echo "		<td class='fila1'>";
		echo "			".$infoMedico2['nom'];
		echo "		</td>";
		echo "	</tr>";
		echo "	<tr>";
		echo "		<td class='encabezadotabla'>";
		echo "			Registro: ";
		echo "		</td>";
		echo "		<td class='fila1'>";
		echo "			".$infoMedico2['reg'];
		echo "		</td>";
		echo "	</tr>";
		echo "</table>";
		
		if( isset($dm) && $dm == 'dir' && @$medom == 'off'){
			filtroPorMedico( $filtro, $infoMedico );
			
		}
		
		if( isset($dm) && $dm == 'dir' && @$medom == 'on'){
			filtroPorMedico( $filtro, $infoMedico );
			
		}
		echo "<BR><BR>";
		
		if( !isset($dm) ){
			$dm = '';
		}
		
		crearTabla( $opcion, $codMedico, $key2, $dm );
	}
		
	echo "<br><br><center><input type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'>";
			
	echo "</form>";
	
}
?>

