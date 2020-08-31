<?php
include_once("conex.php");
if (!isset($consultaAjax))
{
?>
<html>

<head>
<title>MATRIX - [PREENTREGA DE PACIENTES]</title>

</head>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>

<body>

<script type="text/javascript">

function descartar(wemp_pmla, wbasedato, codart, cantidad, whis, wing, ccoorigen, id, obj){

	$.ajax({
			url: "admisionPreentrega.php",
			type: "POST",
			data:{
				consultaAjax 	: 'registrarDescarte',
				wemp_pmla		: wemp_pmla,
				wbasedato		: wbasedato,
				codart			: codart,
				whis 			: whis,
				wing			: wing,
				ccoorigen		: ccoorigen,
				cantidad		: cantidad,
				id				: id				
				
			},
			dataType: "json",			
			async: false,
			success:function(data_json) {
			
				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{
					
					$(obj).css({'display' : 'none'});
					
						
				}
			}			
			
		});

}

function enter() { document.forms.forma.submit(); }

function volver(accion){

	document.location.href='admisionPreentrega.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=a';
}

function inicio()
{
	document.location.href='admisionPreentrega.php?wemp_pmla='+document.forma.wemp_pmla.value;
}

function mostrarMensajePantalla(texto){
	document.getElementById('mensajePantalla').style.display = "block";
	document.getElementById('mensajePantalla').innerHTML = "  ::MENSAJE::  "+texto;
}

function ocultarMensajePantalla(){
	document.getElementById('mensajePantalla').style.display = "none";
}

function marcarEntrega(historia, ingreso, ccoOrigen){
	if(confirm("Desea confirmar la entrega del paciente desde urgencias?")){
		document.location.href='admisionPreentrega.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=c&whistoria='+historia+'&wingreso='+ingreso+'&ccoOrigen='+ccoOrigen;
	}
}

function marcarEntregaUrgencias(historia, ingreso, wnum_art, warr_art){

	document.forms.forma.wactualizar.value = '*';
	document.forms.forma.whistoria.value = historia;
	document.forms.forma.wingreso.value = ingreso;
	document.forms.forma.wnum_art.value = wnum_art;
	document.forms.forma.warr_art.value = warr_art;

	document.forms.forma.waccion.value = 'a';  //Ir a la lista de pacientes urgencias

	document.forms.forma.submit();
}

function marcarEntregaCirugia(historia, ingreso, cco){
	if(confirm("Desea seleccionar la nueva ubicación del paciente?")){
//		window.open('admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=e'+'&wselcco='+cco+'&historia='+historia+'&ingreso='+ingreso,'window','width=650,height=550,scrollbars=yes,resizable=yes');
		document.location.href = 'admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=e'+'&wselcco='+cco+'&historia='+historia+'&ingreso='+ingreso;
	}
}

function verificarUsuario(historia,ingreso){
	var contenedor = document.getElementById('cntUsuario');
	var contenedor2 = document.getElementById('cntEntrega');
	var codigoUsuario = document.forms.forma.codigo.value;
//	alert(codigoUsuario);
	var parametros = "";

	parametros = "consultaAjax=05&basedatos="+document.forms.forma.wbasedato.value+"&codigo=" + document.forms.forma.codigo.value + '&clave='+document.forms.forma.clave.value;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "../../../include/root/comun.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				if(ajax.responseText != ''){
					contenedor.innerHTML = "<center><span class='subtituloPagina2'>Usuario identificado: "+ajax.responseText+"</span></center>";
//					contenedor2.innerHTML = "<input type=button value='Entregar paciente' onclick='javascript:marcarEntregaUrgencias("+historia+","+ingreso+","+codigoUsuario+");'>";
					document.forms.forma.wusuario.value = document.forms.forma.codigo.value;
					document.getElementById("btnEntregar").disabled = false;
				} else {
					contenedor.innerHTML = "<center><span class='subtituloPagina2'>Usuario '"+document.forms.forma.codigo.value + "' NO identificado, por favor revise usuario y contraseña</span></center>";
					contenedor2.innerHTML = "";
					document.getElementById("btnEntregar").disabled = true;
				}
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

function mostrarAyuda(){
	window.open('../manuales/PreentregaUrgencias.pdf', 'window','width=650,height=550,scrollbars=yes,resizable=yes');
}

// Llamado ajax para dar de alta al paciente
function altaPacienteCirugia(i)
{
	if(confirm("Realmente desea dar de alta el paciente: "+document.getElementById('alta'+i).value))
	{
		var pac = document.getElementById('alta'+i).value.split('-');
		var hisPac = pac[0];
		var ingHisPac = pac[1];
		document.forms.forma.whistoria_alta.value = hisPac;
		document.forms.forma.wingreso_alta.value = ingHisPac;
		document.forms.forma.walta.value = 'on';
		document.forms.forma.ccoConsultado.value = document.getElementById('ccoConsultado').value;

		document.forms.forma.submit();
	}
	else
	{
		document.getElementById('alta'+i).checked = false;
		return false;
	}
}

function RegresarPacienteCirugia(i){//cambio_devolucion
	if(confirm("Realmente desea regresar al paciente: "+document.getElementById('dev'+i).value) )
	{
		var pac = document.getElementById('dev'+i).value.split('-');
		var hisPac = pac[0];
		var ingHisPac = pac[1];
		document.forms.forma.whistoria_dev.value = hisPac;
		document.forms.forma.wingreso_dev.value = ingHisPac;
		document.forms.forma.wdev.value = 'on';
		document.forms.forma.wservicio_dev.value = document.getElementById('wservicio_dev'+i).value;
		document.forms.forma.ccoConsultado.value = document.getElementById('ccoConsultado').value;
		document.forms.forma.submit();
	}
	else
	{
		document.getElementById('dev'+i).checked = false;
		return false;
	}
}

</script>

<?php
}
/*BS'D
 * Entrega de pacientes en urgencias
 * Autor: Mauricio Sánchez Castaño.
 */

////// MODIFICACIONES //////
//2015-01-28: Se validan de forma correcta los articulos que tienen saldo pendiente y asi poder realizar el traslado.
//2014-12-26: Se agrega validacion de articulos con unidades completas, si es asi no deja entregar el paciente, ademas si hay algun sobrante de un articulo,
//			  permite el descarte.
//2014-11-21: se agregó la funcionalidad de regresar a cirugía a los pacientes que se haya dado de alta por error( buscar: cambio_devolucion ). (Camilo)
//2014-11-14: se agregaron columnas de fecha de ingreso y centro de costos actual, se modificó para que consulten por el centro de costos de ingreso buscado. (Camilo)
//2014-11-12: Si el paciente tiene cubiculo o camilla asociada la libera. (Jonatan)
//2014-10-30: Se agrega la lista de los medicamentos para los pacientes de urgencias cuando las ordenes esten activas, y cuando haga la entrega
//			  hará el registro de entrega de los esos medicamentos. (Jonatan Lopez)
//2014-10-06: Se agregaron las columnas de fecha y hora de la entrega para facilitar la priorización de recibo de los usuarios.(Camilo Zapata)
//2012-06-19: Se cambio la consulta de pacientes de cirugia por pacientes de admisiones ya que los pacientes de cirugia son ambulatorios y se necesita
//			  mostrar los que van para hospitalización
//			  Se agregó Oriori = '".$wemp_pmla."' en las consultas a la tabla root_000037 para que se filtrara también por empresa y no se vaya a mostrar
//			  historias duplicadas ya que el mismo número puede estar en varias empresas
//			  Se adicionó " AND Ccoest = 'on' " en las consultas a la tabla movhos_000011
//			  Mario Cadavid
//2011-11-08: Se adicionó Ubiald != 'on' en las consultas principales de modo que si el paciente tiene alta definitiva no se muestra la lista
//			  También se quito Ubisan='' y Ubisan='' en las consultas principales debido a que ya se pueden trasladar de otros centros de costos a cirugia
//2011-11-08: Se agrega la columna "Alta" de la lista de pacientes la cual permite dar de alta manualmente a los pacientes actualmente en cirugia

class pacientesPreentregadosDTO {
	var $historiaPaciente;
	var $ingresoHistoriaPaciente;
	var $ccoActual;
	var $nombrePaciente;
	var $ccoDestino;
	var $habitacionDestino;
	var $fechaEntrega;
	var $HoraEntrega;
}

class movimientoHospitalarioDTO {
	var $consecutivo;
	var $historia;
	var $ingreso;
	var $servicioOrigen;
	var $servicioDestino;
	var $habitacionOrigen;
	var $habitacionDestino;
	var $tipoMovimiento;
}

//$id
function registrarSaldosNoApl($wbasedato, $usuario, $tipTrans, $id, $cantidad)
{
	
		$conex = obtenerConexionBD("matrix");

		/**
		 * Existe saldo para el artículo, para esa historia en ese ingreso en ese centro de costos.
		 * Por lo cual se hace un update del saldo, no es necesario crear un registro nuevo.
		 */

		// Si es un aprovechamiento, hay que aumentar el contenido de los aprovechamientos.
		$campoAprov="Spaa";
		$campo = "Spau"	;
		
		/**
		 * Sí NO es una inactivación debe SUMAR la cantidad de $art['can'] en
		 * las entradas o las salidas según sea un cargo o una devoluciónr, respectivamente.
		 */
		//Es una entrada para el paciente
		$campoEn = $campo."en"." = ".$campo."en + ".$cantidad	;
		$campoAprovEn = $campoAprov."en = ".$campoAprov."en + ".$cantidad;

		//Es una salida para el paciente osea una devolución.
		$campoSa = $campo."sa = ".$campo."sa + ".$cantidad;
		$campoAprovSa = $campoAprov."sa = ".$campoAprov."sa + ".$cantidad;

		if($tipTrans == 'C')
		{
			$set=$campoEn;
			if($aprov)
			{
				$set=$set.", ".$campoAprovEn;
			}
		}
		else
		{
			$set=$campoSa;
			if($aprov)
			{
				$set=$set.", ".$campoAprovSa;
			}
		}


		/**
		 * Se realiza el update en la tabla de saldos así:
		 * *Si se esta cargando a la cuenta del paciente la cantidad de artículo se suma a Spauen.
		 *  Si además es un aprovechamiento entonces tambien se suma la cantidad a Spaaen.
		 * *Si se esta devolviendo a la cuenta del paciente la cantidad de artículo se suma a Spausa.
		 *  Si además es un aprovechamiento entonces tambien se suma la cantidad a Spaasa.
		 * *Sí es una incactivación funciona igual que en los dos pasos anteriores solo que en vez de sumar resta.
		 */
		$q = " UPDATE ".$wbasedato."_000004 "
			."    SET ".$set." "
			."  WHERE id = ".$id." ";
		$err1 = mysql_query($q,$conex);
		echo mysql_error();
		$num=mysql_affected_rows();
		if($num<1)
		{
			$error['ok']	 ="NO INGRESADO A MATRIX";
			$error['color']  ="#ff0000";
			$error['codInt'] ="1007";
			$error['codSis'] =mysql_errno();
			$error['descSis']=mysql_error();
			return (false);
		}
		else
		{
			return (true);
		}
}



/**
 * Crear el encabezado de la devolución y retornar en la variable devCons el valor del consecutivo de la devolución
 *
 * @created 2007-09-25
 *
 * @table 000001 SELECT
 * @table 000035 INSERT
 *
 * @param Integer	$devCons	Consecutivo de la devolución, es el dato que llena la función.
 * @param Array 	$cco		Información del centro de costos
 * 								Debe ingresar:
 * 								[cod]:String[4].Código del centro de costos.</br>
 * @param Array		$pac		Información del paciente
 * 								La información que debe estar en el arreglo cunado se llama la función es:</br>
 * 								[his]:Historia del paciente.</br>
 * 								[ing]:Ingreso del paciente.</br>
 * @param String	$usuario	Código de MAtrix del usuario responsable de la transaccion.
 */
function devCons($cco, $whis, $wing, $usuario, $wbasedato, $id, $cantidad)
{
	
	$conex = obtenerConexionBD("matrix");
	
	if(registrarSaldosNoApl($wbasedato, $usuario, "D", $id, $cantidad )){
		
		$q = "LOCK TABLE ".$wbasedato."_000001 WRITE";
		$err = mysql_query($q,$conex);

		$q = " UPDATE ".$wbasedato."_000001 "
				."SET   Connum = Connum +1 "
				."WHERE Contip = 'devcon' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		if($err == "") {
			return(false);
			$error['codInt']="1008";
			$error['codSis']=mysql_errno();
			$error['descSis']=mysql_error();

		}
		else
		{

			$q = "  SELECT Connum "
					."FROM  ".$wbasedato."_000001 "
					."WHERE Contip = 'devcon' ";
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			echo mysql_error();
			if($num>0)
			{
				$row= mysql_fetch_array($err);
				$devCons= $row['Connum'];

				$q = "UNLOCK TABLES";
				$err = mysql_query($q,$conex);

				/**
				 * Crear el encabezado de la la devolución en 000035
				 */
				$q= " INSERT INTO ".$wbasedato."_000035 (    medico,           Fecha_data,           Hora_data,       Dencon,         Denhis,              Dening,            Denori,         Denusu, Seguridad) "
				."                        VALUES ( '".$wbasedato."',  '".date("Y-m-d")."', '".date('H:i:s')."', ".$devCons.", '".$whis."','".$wing."', '".$cco."', '".$usuario."','A-".$usuario."') ";
				$err=mysql_query($q,$conex);
				echo mysql_error();
				$num=mysql_affected_rows();
				if($num==1)
				{
					return $devCons;
				}
				else
				{
					$error['codInt']="1021";
					$error['codSis']=mysql_errno();
					$error['descSis']=mysql_error();
					return(false);
				}
				
			}
			else
			{
				/*Error no existe consecutivo*/
				$error['codInt']="";
				$error['codSis']=mysql_errno();
				$error['descSis']=mysql_error();
				return(false);
			}
		}
	}
}


/**
 * Registra un descarte en la tabla 000031
 *
 *  *
 * @table 000031 INSERT
 *
 * @param Integer	$nde		Consecutivo de la devolución.
 * @param Array		$pac 		Información del paciente:
 * 								[his]:Historia del paciente.</br>
 * 								[ing]:Ingreso del paciente.
 * @param Array 	$cco		Información del centro de costos
 * 								[cod]:String[4].Código del centro de costos.</br>
 * @param Boolean	$aprov		Si es un aprovechamiento o no.
 * @param Boolean	$aplica		Indica si el desarte fue registrado en los saldos de aplicación (0000030) o en los saldos normales del paciente (000004)
 * @param Array		$art		Información del Artículo
 * 								[cod]:Código del artículo.</br>
 * 								[can]:cantidad del artículo.
 * @param String	$destino	Lugar hacia donde va el descarte, los primeros 2 carácteres con el código del destino que esta almacenado en la tabla 000023.
 * @param String	$usuario	Código del usuario que responsable de la transacción
 * @param Array		$error 		Almacena todo lo referente con códigos y mensajes de error</br>
 * 								[ok]:Descripción corta.</br>
 * 								[codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010</br>
 * 								[codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.</br>
 * 								[descSis]:Descripción del error del sistema.
 * @return Boolean
 */
						 //$cco, $aplica, $codart,  $destino
					     //$wemp_pmla, $wbasedato, $codart, $cantidad, $whis, $wing, $ccoorigen
function registrarDescarte($wemp_pmla, $wbasedato, $codart, $whis, $wing, $ccoorigen, $cantidad, $id)
{
	
	$datamensaje = array('mensaje'=>'', 'error'=>0);
	
	$conex = obtenerConexionBD("matrix");	
	
	$user_session = explode('-',$_SESSION['user']);
	$wuser = $user_session[1];
	
	$consecutivo = devCons($ccoorigen, $whis, $wing, $wuser, $wbasedato, $id, $cantidad);
	$destino = consultarAliasPorAplicacion($conex, $wemp_pmla, "justificacionDescarteUrgencias");	//Justificacion del descarte
 
	$q = " INSERT INTO ".$wbasedato."_000031 (    medico,      Fecha_data     ,     Hora_data      ,  Descon   ,     Descco       , Desapv ,  Desapl,    Desart    ,   Descan     ,    Desdes,       Seguridad ) "
	."                        VALUES (  '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$consecutivo."', '".$ccoorigen."', 'off'  ,   'off', '".$codart."', '".$cantidad."', '".$destino."', 'A-".$wuser."')";
	$err = mysql_query($q,$conex);
	echo mysql_error();
	$num=mysql_affected_rows();
	if($num<1)
	{
		$error['color']="#ff0000";
		$error['codInt']="1023";
		$error['codSis']=mysql_errno();
		$error['descSis']=mysql_error();
		$datamensaje['error'] = 1;
		$datamensaje['mensaje'] = "No se pudo descartar el articulo";
	}
	else
	{
		
		$datamensaje['mensaje'] = "El articulo se descarto con exito.";		
		
	}
	
	echo json_encode($datamensaje);
	
}

function validar_unidad($art){

	global $wbasedato;
	global $conex;
	
	$dividir = 1;
	
	//Si la unidad de la tabla 26 es igual a la unidad de la tabla 115 entonces tomara la concentracion de la tabla 115.	
	$q = "  SELECT Relcon
			  FROM ".$wbasedato."_000026, ".$wbasedato."_000115
			 WHERE Relart = Artcod
			   AND Reluni = Artuni
			   AND Relart = '".$art."'" ;
	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	
	if($num > 0){
	
		$dividir = $row['Relcon'];
		
	}else{
		
		//Revisar si la unidad de presentacion es diferente de la unidad de fracccion, ademas se revisa si la fraccion es igual a 1,
		//en este caso se tomara la concentracion (Ej: un PUFF es igual a una DO)
		$q = "  SELECT Relcon
			      FROM ".$wbasedato."_000059, ".$wbasedato."_000115
				 WHERE Relart = Defart
			       AND Relpre != Deffru
			       AND Relart = '".$art."'
			       AND Deffra = '1'" ;
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$row = mysql_fetch_array($res);
		
		if($num > 0){	
		
			$dividir = $row['Relcon'];
		
		}	
	}
	
	return $dividir;

}


function ir_a_ordenes($wemp_pmla, $wcco){


	global $wbasedato;
	global $conex;

	$q = "  SELECT Ccoior
			  FROM ".$wbasedato."_000011
			 WHERE Ccocod = '".$wcco."'" ;
	$res = mysql_query($q, $conex);
	$row = mysql_fetch_array($res);

	return $row['Ccoior'];

}


function AnularAplicacion($whis, $wing, $wcco, $wart, $wartnom, $wcta, $wuser, $apv)
{
	//AnularAplicacion($whis, $wing, $wcco, strtoupper($warr_art[$i][0]), $wartnom, $wcta, $wuser, 'off')
	global $wbasedato;
	global $conex;

	//lo que se hace aca es una anulacion de la aplicacion hasta que sea necesario
	//selecciono las aplicaciones de la tabla 15
	$q = " SELECT Aplcan, A.id, Aplron,  Aplusu,  Aplapr, Aplnum, Apllin, Aplfec, Aplufr, Apldos "
	. "   FROM " . $wbasedato . "_000015 A "
	. "  WHERE Aplhis = '" . $whis . "'"
	. "    AND Apling = '" . $wing . "'"
	. "    AND Aplart = '" . $wart . "'"
	. "    AND Aplest = 'on' ";

/*
	IF($apv=='on')
	{
		$q = $q."    AND Aplapv = 'on' ";
	}
	else
	{
		$q = $q."    AND Aplapv != 'on' ";
	}
*/
	$q = $q."     Order by 2 desc";

	$rest = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	$num = mysql_num_rows($rest);

	$can=$wcta;
	for ($j = 1;$j <= $num;$j++)
	{
		$row = mysql_fetch_array($rest);

		if( $j == 1 ){
			$dosis = $row['Apldos']/$row['Aplcan'];
		}

		//se anula la aplicacion
		$q = " UPDATE " . $wbasedato . "_000015 "
		. "    SET aplest = 'off' "
		. "  WHERE id='" .$row[1]. "' ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		if($row[0]<=$can)
		{
			$can=$can-$row[0];
		}
		else
		{
			$saldo=$row[0]-$can;
			//se graba la aplicacion con el saldo de lo que habia menos lo devuelto
			$q= " INSERT INTO ".$wbasedato."_000015 (    medico       ,          fecha_data,           hora_data,      Aplhis,       Apling,         Aplron,      Aplart,                                                        Apldes,     Aplcan,      Aplcco,        Aplusu,        Aplapr, Aplest,        Aplnum,        Apllin,        Aplfec,               Aplufr,                Apldos,     Seguridad ) "
			."                        		 VALUES ( '".$wbasedato."', '".date('Y:m:d')."', '".date('H:i:s')."', '".$whis."',  '".$wing."',  '".$row[2]."', '".$wart."', '".str_replace("'","\'",str_replace("\\","\\\\",$wartnom))."', ".$saldo.", '".$wcco."', '".$row[3]."', '".$row[4]."',   'on', '".$row[5]."', '".$row[6]."', '".$row[7]."', '".$row['Aplufr']."', '".($saldo*$dosis)."', 'A-".$wuser."')";
			$err = mysql_query($q,$conex);
			echo mysql_error();
			$num=mysql_affected_rows();
			if($num<1)
			{
				$error['color']="#ff0000";
				$error['codInt']="1019";
				$error['codSis']=mysql_errno();
				$error['descSis']=mysql_error();
				return (false);
			}

			$can=0;
		}

		if($can==0)
		{
			$j=$num+1;
		}
	}
	return(true);
}

//Consulta el tipo de habitacion a facturar.
function consultartipohab($conex, $whab, $wbasedato){

	$q = " SELECT Habtfa "
		."   FROM ".$wbasedato."_000020 "
		."  WHERE habcod = '".$whab."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	return $row['Habtfa'];


}

function entregarArticulos($whis, $wing, $servicioOrigen, $habitacionOrigen, $servicioDestino, $habitacionDestino, $wnum_art, $warr_art){

	global $wbasedato;
	global $conex;

	$dato_user = explode("-",$_SESSION['user']);
	$wuser = $dato_user[1];
	$warr_art = unserialize(base64_decode($warr_art));

	$wfecha = date('Y-m-d');
	$whora = date('H:i:s');

	$wtipo_hab_fac_e = consultartipohab($conex, $habitacionOrigen, $wbasedato);
	$wtipo_hab_fac_r = consultartipohab($conex, $habitacionDestino ,$wbasedato);

	$q = " SELECT Eyrnum "
		. "  FROM ".$wbasedato."_000017 "
		. " WHERE Eyrtip = 'Entrega' "
		. "	  AND Eyrsor = '".$servicioOrigen."'"		
		. "	  AND Eyrhde = '".$habitacionDestino."'"		
		. "   AND Eyrhis = '".$whis."'"
		. "   AND Eyring = '".$wing."'"
		. "	  AND Eyrest = 'on' ";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	$row = mysql_fetch_array($err);
	$wconsec = $row[0];


if (isset($wnum_art))
{

	$wcan_art = $wnum_art;

	//averiguo que clase de centro de costos es el destino
	// Traigo el INDICADOR de si el centro de costo es hospitalario o No
	$q = " SELECT ccoapl "
		."   FROM " . $wbasedato . "_000011 "
		."  WHERE ccocod = '".$servicioDestino."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		if ($row[0] == "on")
		$wdesapl= "on";
		else
		$wdesapl = "off";
	}
	else
	{
		$wdesapl = "off";
	}


	//Averiguo que clase de centro de costos es el destino
	//Traigo el INDICADOR de si el centro de costo es hospitalario o No
	$q = " SELECT ccoapl "
		."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$servicioOrigen."'";
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	   {
		$row = mysql_fetch_array($res);
		if ($row[0] == "on")
		   $woriapl= "on";
		  else
			$woriapl = "off";
	   }
	  else
		 {
		  $woriapl = "off";
		 }

	if(count($warr_art) == 0 or !isset($warr_art)){
		$warr_art = array();
	}

	foreach ($warr_art as $key => $value)
	{
		$q = " INSERT INTO ".$wbasedato."_000019 (   Medico       ,   Fecha_data,   Hora_data,   Detnum     ,   Detart             ,  Detcan            , Detest, Seguridad     ) "
			."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wconsec."','".$value[0]."',".$value[1].", 'on'  , 'C-".$wuser."')";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

		$wctr = $warr_art[$i][1];     //Cantidad a trasladar
		$wronda = (string)date("H");
		buscar_articulo($value[0]);

		if($wdesapl=='off' and $woriapl=='on')
			{
				// =========================================================================================================================================
				// Aca hago el traslado de los saldos de la tabla 000030 a la 000004, si el centro de costo aplica automaticamente cuando se factura. Ej:UCI
				// =========================================================================================================================================
				$q = " SELECT spluen, splusa, splaen, splasa, splcco, Ccopap "
					."   FROM ".$wbasedato."_000030, ".$wbasedato."_000011 "
					."  WHERE splhis = '".$whis."'"
					."    AND spling = '".$wing."'"
					."    AND splart = '".$value[0]."'"
					."    AND (splcco = '".$servicioOrigen."'"      //2 de Mayo de 2008
					."     OR  splcco = ccocod "                 //2 de Mayo de 2008
					."    AND  ccotra = 'on') "                  //2 de Mayo de 2008
					."    Order by 6";
				$rest = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
				$num = mysql_num_rows($rest);

				for ($j = 1;$j <= $num;$j++)
				{
					$row = mysql_fetch_array($rest);

					$wuen = $row['spluen']; //Unix entradas
					$wusa = $row['splusa']; //Unix salidas
					$waen = $row['splaen']; //Aprovechamientos entradas
					$wasa = $row['splasa']; //Aprovechamientos salidas
					$wscc = $row['splcco']; //centro de costos que grabo

					if(($wuen-$wusa)>0)
					{
						if(($wuen-$wusa)<$wctr)
						  {
							$wcta=$wuen-$wusa;
							$wctr=$wctr-$wcta;
						  }
						 else
						   {
							$wcta=$wctr;
							$wctr=0;
						   }

						if($wctr<=0)
						  {
							$j=$num+1;
							$j=$num+1;
						  }

						if($wcta != ''){

							if (($wuen-$wusa-$waen+$wasa) >= $wcta) // La cantidad en la 000030 es mayor a lo que se va a trasladar
							  {
								$q=  " SELECT id "
									."   FROM ".$wbasedato."_000004 "
									."  WHERE Spahis = '".$whis."' "
									."    AND Spaing = '".$wing."' "
									."    AND Spacco = '".$wscc."' "
									."    AND Spaart = '".$value[0]."' ";
								$errs = mysql_query($q,$conex);
								$nums = mysql_num_rows($errs);

								if ($nums > 0)
								   {
									$q = " UPDATE ".$wbasedato."_000004 "
										."    SET spauen = spauen+ ".$wcta
										."  WHERE spahis = '".$whis."'"
										."    AND spaing = '".$wing."'"
										."    AND spacco = '".$wscc."'"
										."    AND spaart = '".$value[0]."'";
									$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
								   }
								  else
									{
									 $q=  " INSERT INTO ".$wbasedato."_000004 (   medico       ,    Fecha_data      ,    Hora_data       ,    Spahis  ,    Spaing ,    Spacco  ,    Spaart              ,   Spauen , Spausa, Spaaen, Spaasa, Seguridad     ) "
										 ."                            VALUES ('".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$value[0]."', ".$wcta.", 0     , 0     , 0     , 'A-".$wuser."') ";

									 $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									}

								$q = " UPDATE ".$wbasedato."_000030 "
									."    SET spluen = spluen-".$wcta
									."  WHERE splhis = '".$whis."'"
									."    AND spling = '".$wing."'"
									."    AND splcco = '".$wscc."'"
									."    AND splart = '".$value[0]. "'";
								$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

								AnularAplicacion($whis, $wing, $wcco, strtoupper($value[0]), $wartnom, $wcta, $wuser, 'off');

							  }

							if (($wuen - $wusa - $waen) < $wcta) // La cantidad en la 000030 es menor a lo que se va a trasladar
							{
								$q= " SELECT id "
								."      FROM ".$wbasedato."_000004 "
								."     WHERE Spahis	= '".$whis."' "
								."       AND Spaing	= '".$wing."' "
								."       AND Spacco	= '".$wscc."' "
								."       AND Spaart	= '".$value[0]."' ";
								$errs = mysql_query($q,$conex);
								$nums = mysql_num_rows($errs);

								if ($nums > 0)
								  {
									$q = " UPDATE ".$wbasedato."_000004 "
										."    SET spauen = spauen+".$wcta.", "
										."        spaaen = spaaen+".($wcta - ($wuen - $wusa - $waen+ $wasa))
										."  WHERE spahis = '".$whis."'"
										."    AND spaing = '".$wing."'"
										."    AND spacco = '".$wscc."'"
										."    AND spaart = '".$value[0]."'";
									$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								  }
								else
								   {
									$q=  " INSERT INTO ".$wbasedato."_000004 (    medico,          Fecha_data,           Hora_data,            Spahis,          Spaing,             Spacco,            Spaart,   Spauen,   Spausa,   Spaaen,  Spaasa,         Seguridad) "
										."                            VALUES ( '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$value[0]."', ".$wcta.", 0, ".($wcta - ($wuen - $wusa - $waen + $wasa)).", 0, 'A-".$wuser."')";

									$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
								   }


								$q = " UPDATE ".$wbasedato."_000030 "
									."    SET spluen = spluen-".$wcta.", "
									."        splaen = splaen-".($wcta - ($wuen - $wusa - $waen + $wasa))
									."  WHERE splhis = '".$whis."'"
									."    AND spling = '".$wing."'"
									."    AND splcco = '".$wscc."'"
									."    AND splart = '".$value[0]. "'";
								$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

								if (($wuen - $wusa - $waen + $wasa)>0)
								{
									AnularAplicacion($whis, $wing, $wcco, strtoupper($value[0]), $wartnom, ($wuen - $wusa - $waen + $wasa), $wuser, 'off');
								}

								if (($wcta - ($wuen - $wusa - $waen + $wasa))>0)
								{
									AnularAplicacion($whis, $wing, $wcco, strtoupper($value[0]), $wartnom, ($wcta - ($wuen - $wusa - $waen + $wasa)), $wuser, 'on');
								}
							}
						}
					}
				}
			}
		}
	}	// Fin grabación de detalle


}


function buscar_articulo(&$wcodart)
{
	global $wbasedato;
	global $wcenmez;
	global $conex;
	global $wok;
	global $wartnom;
	global $wartuni;
	global $wunides;
	global $wemp_pmla;
	
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	// Busco el nombre del articulo en el maestro de  articulos de movhos
	$q = " SELECT artcom, artuni, unides "
	. "   FROM " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
	. "  WHERE artcod = '" . $wcodart . "'"
	. "    AND artuni = unicod ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$wartnom = $row[0];
		$wartuni = $row[1];
		$wunides = $row[2];
		$wok = "on";
	}
	else
	{
		// Busco el nombre del articulo en la base de datos de central de mezclas
		$q = " SELECT artcom, artuni, unides "
		. "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000027 "
		. "  WHERE artcod = '" . $wcodart . "'"
		. "    AND artuni = unicod ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$row = mysql_fetch_array($res);
			$wartnom = $row[0];
			$wartuni = $row[1];
			$wunides = $row[2];
			$wok = "on";
		}
		else
		{
			// Busco el nombre del articulo en la base de datos de central de 'movhos', pero buscando con el
			// codigo del proveedor en la tabla movhos_000009
			$wcodart=BARCOD($wcodart);
			$q = " SELECT artcom, artuni, unides, " . $wbasedato . "_000009.artcod "
			. "   FROM " . $wbasedato . "_000009, " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
			. "  WHERE artcba                       = '" . $wcodart . "'"
			. "    AND " . $wbasedato . "_000009.artcod = " . $wbasedato . "_000026.artcod "
			. "    AND artuni                       = unicod ";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				$wartnom = $row[0];
				$wartuni = $row[1];
				$wunides = $row[2];
				$wcodart = $row[3];
				$wok = "on";
			}
			else
			{
				$wartnom = "Codigo no existe";
				$wartuni = "";
				$wunides = "";
				$wok = "off";
			}
		}
	}
}


function Detalle_ent_rec($wtip, $whis, $wing, &$wnum_art, &$warr_art, &$wunidad_completa)
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	global $wartnom;
	global $wartuni;
	global $wunides;

	global $wnum_art;
	global $warr_art;
	
	global $ccoOrigen;

	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	if($wtip=='NoApl')
	{
	 $q = " SELECT spaart, spauen-spausa, id, spacco "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),3) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	else
	{
		// 2008-03-13
		$q = " SELECT Eyrnum, Fecha_data, Hora_data "
		. "   FROM " . $wbasedato . "_000017 "
		. "  WHERE eyrhis                            = '" . $whis . "'"
		. "    AND eyring                            = '" . $wing . "'"
		. "    AND eyrtip= 'Entrega' "
		. "  ORDER BY 2 desc, 3 desc";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " SELECT Detart, sum(Detcan) "
		. "   FROM " . $wbasedato . "_000019 "
		. "  WHERE detnum                        = '" . $row[0]  . "'"
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num >= 1)
	{
		$wnum_art = $num;

		echo "<input type='HIDDEN' name='wnum_art' value='" . $wnum_art . "'>";

		echo "<center><table border=0>";
		echo "<tr class=encabezadoTabla>";
		echo "<td>Pendiente</font></td>";
		echo "<td>Grabado desde</font></td>";
		echo "<td>Articulo</font></td>";
		echo "<td>Descripción</font></td>";
		echo "<td>Presentación</font></td>";
		echo "<td title='Cantidades pendientes de aplicar o cantidad a trasladar'>Cantidad</font></td>";
		echo "<td>Descartar</font></td>";
		echo "</tr>";
		
		$wunidad_completa = array();
		
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);		
			$cajon_descarte = "";
			$color_cajon = "";
			$id = $row['id'];

			if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			    $wclass = "fila2";
			
			$validar_unidad = validar_unidad($row[0]);
			
			buscar_articulo($row[0]);
			
			//Validar si el saldo actual tiene unidades completas, en ese caso no permite la entrega del paciente.
			if(floor($row[1]/$validar_unidad) >= 1){
				
				array_push($wunidad_completa,$row[0]); // Agrego los articulos con unidad completa al arreglo.
								
				$color_cajon = "style='background-color:red'";
			
			}
			elseif( ($row[1]/$validar_unidad - floor($row[1]/$validar_unidad)) > 0){
								
				$cajon_descarte = "<input type=checkbox id='check_descarte' onclick='descartar(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$row[0]."\",\"".$row[1]."\",\"".$whis."\",\"".$wing."\",\"".$ccoOrigen."\",\"".$id."\", this)'>";				
			}
			
			echo "<tr class=".$wclass.">";
			echo "<td $color_cajon></td>";
			echo "<td align=center>".$row['spacco']."</td>";			
			echo "<td>".$row['spaart']."</td>";			
			echo "<td>".$wartnom."</td>";
			echo "<td>".$wunides."</td>";
			echo "<td align=center>".$row[1]."</td>";
			echo "<td align=center>".$cajon_descarte."</td>";
			echo "</tr>";

			$warr_art[$i][0] = $row[0];
			$warr_art[$i][1] = $row[1];

			echo "<input type='HIDDEN' name='warr_art[" . $i . "][0]' value='" . $warr_art[$i][0] . "'>";
			echo "<input type='HIDDEN' name='warr_art[" . $i . "][1]' value='" . $warr_art[$i][1] . "'>";
		}	
	}
}


// Función que permite consultar el código actual de el centro de costos de Urgencias
function consultarCcoUrgencias(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod
		FROM
			".$wbasedato."_000011
		WHERE
			Ccourg = 'on'
		AND Ccoest = 'on'; ";

	//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){
		$fila = mysql_fetch_row($res);

		$cco = $fila[0];
	}
	return $cco;
}

// Función que permite consultar el código actual de el centro de costos de Cirugia
function consultarCcoCirugia(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod
		FROM
			".$wbasedato."_000011
		WHERE
			Ccocir = 'on'
		AND Ccoest = 'on'; ";

	//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){
		$fila = mysql_fetch_row($res);

		$cco = $fila[0];
	}
	return $cco;
}

// 2012-06-19
// Función que permite consultar el código actual de el centro de costos de Admisiones
function consultarCcoAdmision(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod
		FROM
			".$wbasedato."_000011
		WHERE
			Ccoadm = 'on'
		AND Ccoest = 'on'; ";

	//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){
		$fila = mysql_fetch_row($res);

		$cco = $fila[0];
	}
	return $cco;
}

function consultarPacientesPreentregados($centroCostos){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$q = " SELECT
				 b.Fecha_data fechaEntrega, b.Hora_data HoraEntrega, Ubihis, Ubiing, (SELECT CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) FROM root_000036 WHERE Pacced = Oriced AND Pactid = Oritid ) Nombre,
				Ubisac, Eyrsde, (SELECT Cconom FROM ".$wbasedato."_000011 WHERE Ccocod = Eyrsde) Cconom, Eyrhde
		FROM
				".$wbasedato."_000018, ".$wbasedato."_000017 b, root_000037
		WHERE
				Ubisac = '".$centroCostos."'
				AND Ubihis = Eyrhis
				AND Ubiing = Eyring
				AND Eyrhis = Orihis
				AND Eyring = Oriing
				AND Ubihac = ''
				AND Ubiptr = 'on'
				AND Ubiald != 'on'
				AND Eyrest = 'on'
				AND Oriori = '".$wemp_pmla."'
				AND Eyrtip = 'Entrega'
		GROUP BY Ubihis, Ubiing
		ORDER BY 1 asc, 2 asc";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$coleccion = array();

	if($filas > 0){
		$cont1 = 0;

		while($cont1 < $filas){
			$fila = mysql_fetch_array($res);
			$info = new pacientesPreentregadosDTO();

			$info->historiaPaciente        = $fila['Ubihis'];
			$info->ingresoHistoriaPaciente = $fila['Ubiing'];
			$info->nombrePaciente          = $fila['Nombre'];
			$info->ccoActual               = $fila['Ubisac'];
			$info->ccoDestino              = $fila['Eyrsde']." - ".$fila['Cconom'];
			$info->habitacionDestino       = $fila['Eyrhde'];
			$info->fechaEntrega            = $fila['fechaEntrega'];
			$info->HoraEntrega             = $fila['HoraEntrega'];

			$coleccion[] = $info;
			$cont1++;
		}
	}
	return $coleccion;
}

function consultarPacientesEnRecuperacion($centroCostos){
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	// Consulta los pacientes de un centro de costo determindo
	$q = " SELECT Ubihis, Ubiing, (SELECT CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) FROM root_000036 WHERE Pacced = Oriced AND Pactid = Oritid ) Nombre, Ubisac,(SELECT Cconom FROM ".$wbasedato."_000011 WHERE Ccocod = Ubisac) Cconom, c.Fecha_data fechaIngreso "
		."   FROM ".$wbasedato."_000018 a, ".$wbasedato."_000016 c, root_000037 "
		."  WHERE Ubisac = '".$centroCostos."' "
		."    AND Inghis = Ubihis"
		."    AND Inging = Ubiing"
		."	  AND Ubihis = Orihis "
		."	  AND Ubiing = Oriing "
		."	  AND Ubihac = '' "
//		."	  AND Ubiptr = 'on' "	// Se comenta porque se van a listar los pacientes de admisiones no de cirugia  // 2012-06-19
		."	  AND Ubiald != 'on' "
		."	  AND Oriori = '".$wemp_pmla."' ";

//		echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$coleccion = array();

	if($filas > 0){
		$cont1 = 0;

		while($cont1 < $filas){
			$fila = mysql_fetch_array($res);

			$info = new pacientesPreentregadosDTO();

			$info->historiaPaciente        = $fila['Ubihis'];
			$info->ingresoHistoriaPaciente = $fila['Ubiing'];
			$info->nombrePaciente          = $fila['Nombre'];
			$info->ccoActual               = $fila['Ubisac'];
			$info->fechaIngreso            = $fila['fechaIngreso'];


			$coleccion[] = $info;
			$cont1++;
		}
	}
	return $coleccion;
}

function pacientesEgresados( $servicioBuscado ){//cambio_devolucion
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$hoy       = date('Y-m-d');
	$limite    = ( strtotime( $hoy ) ) - 24*60*60;
	$fechaLimiteinferior = date( 'Y-m-d', $limite );

	// Consulta los pacientes de un centro de costo determindo
	$q = " SELECT Historia_clinica, Num_ingreso,  CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) Nombre, Servicio
		     FROM {$wbasedato}_000033 a, root_000037, root_000036
		    WHERE Fecha_egre_serv between '{$fechaLimiteinferior}' and '$hoy'
		      AND Tipo_egre_serv = 'ALTA'
			  AND Num_ing_serv = '1'
			  AND Servicio     = '$servicioBuscado'
			  AND Orihis       = Historia_clinica
			  AND Oriing       = Num_ingreso
			  AND Oriori       = '01'
			  AND Pactid = Oritid
			  AND Pacced = Oriced
			GROUP BY 1,2,3,4";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$coleccion = array();

	if($filas > 0){
		$cont1 = 0;

		while($cont1 < $filas){
			$fila = mysql_fetch_array($res);

			$info = new pacientesPreentregadosDTO();

			$info->historiaPaciente        = $fila['Historia_clinica'];
			$info->ingresoHistoriaPaciente = $fila['Num_ingreso'];
			$info->nombrePaciente          = $fila['Nombre'];
			$info->ccoActual               = $fila['Servicio'];


			$coleccion[] = $info;
			$cont1++;
		}
	}
	return $coleccion;
}

function consultarUltimoMovimientoPaciente($whistoria,$wingreso){

	global $wbasedato;
	global $conex;

	$q = "SELECT
			Eyrhis, Eyring, Eyrsor, Eyrsde, Eyrhor, Eyrhde, Eyrtip, Eyrest
		FROM
			".$wbasedato."_000017
		WHERE
			Eyrhis = '".$whistoria."'
			AND Eyring = '".$wingreso."'
			AND Eyrtip = 'Entrega'
			AND Eyrest = 'on'
		";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$fila = mysql_fetch_array($res);

		$info = new movimientoHospitalarioDTO();

		$info->historia = $fila['Eyrhis'];
		$info->ingreso = $fila['Eyring'];
		$info->servicioDestino = $fila['Eyrsde'];
		$info->servicioOrigen = $fila['Eyrsor'];
		$info->habitacionOrigen = $fila['Eyrhor'];
		$info->habitacionDestino = $fila['Eyrhde'];
		$info->tipoMovimiento = $fila['Eyrtip'];
	}
	return $info;
}

function modificarUbicacionActualPaciente($whistoria, $wingreso, $servicioOrigen, $habitacionOrigen, $servicioDestino, $habitacionDestino) {

	global $wbasedato;
	global $conex;

	$q = "UPDATE
			".$wbasedato."_000018
		SET
			Ubisac = '".$servicioDestino."',
			Ubihac = '".$habitacionDestino."',
			Ubisan = '".$servicioOrigen."',
			Ubihan = '".$habitacionOrigen."'
		WHERE
			Ubihis = '".$whistoria."'
			AND Ubiing = '".$wingreso."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$num = mysql_affected_rows();
}

function modificarHistoriaHabitacion($whistoria, $wingreso, $wcodigoHabitacion){
	global $wbasedato;
	global $conex;


	//Desocupar el cubiculo
	$q_cub = " UPDATE ".$wbasedato."_000020
		          SET Habhis = '', Habing = '', habdis = 'on'
		        WHERE Habhis = '".$whistoria."'
		          AND Habing = '".$wingreso."'";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cub . " - " . mysql_error());

	//Ocupa la habitacion asignada.
	$q = "UPDATE ".$wbasedato."_000020
			 SET Habhis = '".$whistoria."',
			     Habing = '".$wingreso."',
			     Habdis = 'off'
		   WHERE Habcod = '".$wcodigoHabitacion."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();

}


function consultarHabitacion($conex,$cdHabitacion){
	global $wbasedato;

	$q = "SELECT
			Habcod,Habcco,Habhis,Habing,Habdis,Habest
		FROM
			".$wbasedato."_000020
		WHERE
			Habcod = '".$cdHabitacion."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$fila = mysql_fetch_array($res);

		$info = new habitacionDTO();

		$info->codigo = $fila['Habcod'];
		$info->disponible = $fila['Habdis'];
		$info->historiaClinica = $fila['Habhis'];
		$info->ingresoHistoriaClinica = $fila['Habing'];
		$info->servicio = $fila['Habcco'];
		$info->estado = $fila['Habest'];
	}
	return $info;
}

function modificarUsuarioMovimientoHospitalario($whistoria,$wingreso,$wusuario){
	global $wbasedato;
	global $conex;

	$q = "UPDATE
			".$wbasedato."_000017
		SET
			Seguridad = 'C-".$wusuario."'
		WHERE
			Eyrhis = '".$whistoria."'
			AND Eyring = '".$wingreso."'
			AND Eyrest = 'on'";

//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}

/*
 * Inicio aplicacion
 */
include_once("root/comun.php");

$wactualiz = "Enero 28 de 2015";

// if (!isset($user)){
// 	if (!isset($_SESSION['user'])) {
// 		session_register("user");
// 	}
// }
if(array_key_exists('user', $_SESSION))
{
	$user_session = explode('-',$_SESSION['user']);
	$wuser = $user_session[1];
}

// if (strpos($user, "-") > 0)
// $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

if (!isset($consultaAjax))
	{
if (!isset($_SESSION['user'])){
	terminarEjecucion("usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar.");
}else{
	//Conexion base de datos
	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");	
	$ccoOrigenUrg = consultarCcoUrgencias();	
	
	if(!isset($ccoConsultado)){	
		$ccoConsultado = "";		
	}else{		
		$ccoConsultado = $ccoConsultado;	
	}

	//////////////////////////////////////////////////////////////
	//////////// ALTA DE PACIENTE	//////////////////////////////		// 2011-11-08
	//////////////////////////////////////////////////////////////
	if(isset($walta) && $walta=="on" && isset($whistoria_alta) && $whistoria_alta!="" && isset($wingreso_alta) && $wingreso_alta!="" )
	{
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");

		//Consulto el centro de costo actual del paciente
		$qcen = "	SELECT Ubisac
					FROM ".$wbasedato."_000018
					WHERE Ubihis = '".$whistoria_alta."'
					AND	Ubiing = '".$wingreso_alta."'";
		$rescen = mysql_query($qcen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcen . " - " . mysql_error());
		$rowcen = mysql_fetch_array($rescen);

		//Actualizo tabla 18 de Movhos asignandole los parametros del alta
		$q = "	UPDATE ".$wbasedato."_000018
				SET Ubiald='on', Ubifad='".$fecha."', Ubihad='".$hora."', Ubiuad='".$user."'
				WHERE Ubihis = '".$whistoria_alta."'
				AND	Ubiing = '".$wingreso_alta."'";

		if($res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - No se pudo dar de alta el paciente. Query: " . $q . " - " . mysql_error()))
		{
			//Registro el egreso en la tabla 33 de Movhos
			$q = "	INSERT INTO
					".$wbasedato."_000033
						(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
					VALUES
						('".$wbasedato."','".$fecha."','".$hora."','".$whistoria_alta."','".$wingreso_alta."','".$rowcen['Ubisac']."','1','".$fecha."','".$hora."','ALTA','1','C-".$user."')";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}

	//////////////////////////////////////////////////////////////
	//////////// REGRESO DEL PACIENTE	//////////////////////////////		// 2014-11-21 ---> cambio_devolucion
	//////////////////////////////////////////////////////////////
	if(isset($wdev) && $wdev=="on" && isset($whistoria_dev) && $whistoria_dev!="" && isset($wingreso_dev) && $wingreso_dev!="" )
	{
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");

		//Actualizo tabla 18 de Movhos asignandole los parametros del alta
		$q = "	UPDATE ".$wbasedato."_000018
				SET Ubiald='off', Ubifad='0000-00-00', Ubihad='00:00:00', Ubiuad='', Ubisac='$wservicio_dev'
				WHERE Ubihis = '".$whistoria_dev."'
				AND	Ubiing = '".$wingreso_dev."'";
		$rs = mysql_query( $q, $conex );
		//echo "<pre>".print_r( $q )."</pre>";

		$q = "	DELETE FROM ".$wbasedato."_000033
				WHERE Historia_clinica = '".$whistoria_dev."'
				  AND Num_ingreso      = '".$wingreso_dev."'
				  AND Servicio         = '$wservicio_dev'
				  AND Num_ing_serv     ='1'";
		$rs = mysql_query( $q, $conex );
		//echo "<pre>".print_r( $q )."</pre>";
	}

	//Forma
	echo "<form name='forma' action='admisionPreentrega.php' method=post>";
	echo "<input type='HIDDEN' NAME='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' NAME='wbasedato' value='".$wbasedato."'>";

	//Refrescar pantalla cada minuto
	if(!isset($whistoria)){
		$whistoria = "";
	}
	if(!isset($wingreso)){
		$wingreso = "";
	}

	//************INICIO LOG::: Log de todas las admisiones
	$debug = true;
	if($debug){
		$fechaLog = date("Y-m-d");
		$horaLog = date("H:i:s");

		//Creacion de un archivo plano para tomar una imagen de la informacion de las camas en ese momento
		$nombreArchivo = "admisionPreentrega.txt";

		//Apuntador en modo de adicion si no existe el archivo se intenta crear...
		$archivo = fopen($nombreArchivo, "a");
		if(!$archivo){
			$archivo = fopen($nombreArchivo, "w");
		}

		@$contenidoLog = "****Admision PREENTREGA..$fechaLog - $horaLog. Para historia: $whistoria. Usuario:$usuario->codigo \r\n";
	}
	//************FIN LOG::: de admisiones

	echo "<meta http-equiv='refresh' content='90;url=admisionPreentrega.php?wemp_pmla=".$wemp_pmla."&waccion=".$waccion."&whistoria=$whistoria&wingreso=$wingreso&ccoConsultado=$ccoConsultado&ccoOrigen=$ccoOrigenUrg'>";
	echo "<input type='HIDDEN' NAME='wactualizar'>";
	echo "<input type='HIDDEN' NAME='whistoria'>";
	echo "<input type='HIDDEN' NAME='wingreso'>";
	echo "<input type='HIDDEN' NAME='wusuario'>";

	// Campos que definen alta de paciente
	echo "<input type='HIDDEN' name='walta' value=''>";
	echo "<input type='HIDDEN' name='whistoria_alta' value=''>";
	echo "<input type='HIDDEN' name='wingreso_alta' value=''>";
	echo "<input type='HIDDEN' name='wdev' value=''>";
	echo "<input type='HIDDEN' name='whistoria_dev' value=''>";
	echo "<input type='HIDDEN' name='wingreso_dev' value=''>";
	echo "<input type='HIDDEN' name='wservicio_dev' value=''>";

	//Simulacion pauperrima de un FrontController
	if(isset($wactualizar) && $wactualizar == '*'){
		/*
		 * Se debe realizar lo siguiente:
		 * 0-Si la habitacion NO ESTA Ocupada con un paciente PUEDE REALIZARSE la operacion
		 * 1-Actualizar el servicio anterior y actual, habitación anterior y actual. Debe estar ptr en on.
		 * 2-Verificar que el movimiento en la 17 este en on al servicio y habitacion destino.
		 * 3-Marcar habitación con historia
		 */
		if($wusuario != ''){
			$contenidoLog = $contenidoLog."--->Accion: Entregando desde urgencias \r\n";
			//Se consulta el registro de la habitación en la cual fue preentregado el paciente
			$infoUbicacion = consultarUltimoMovimientoPaciente($whistoria,$wingreso);

			@$contenidoLog = $contenidoLog."Ubicacion inicial del paciente: Ubisan: $infoUbicacion->servicioAnterior. Ubisac: $infoUbicacion->servicioActual. Ubihan:$infoUbicacion->habitacionAnterior. Ubihac:$infoUbicacion->habitacionActual Ubiptr: $infoUbicacion->enProcesoTraslado \r\n";
			$habitacion = consultarHabitacion($conex,$infoUbicacion->habitacionDestino);

			if($habitacion->disponible == 'on'){
				//Marcar el servicio y la historia con los servicios seleccionados en la admision
				modificarUbicacionActualPaciente($whistoria, $wingreso, $infoUbicacion->servicioOrigen, $infoUbicacion->habitacionOrigen,$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino);
				$contenidoLog = $contenidoLog."Ubicacion actual paciente \r\n";

				$ir_a_ordenes = ir_a_ordenes($wemp_pmla, $infoUbicacion->servicioOrigen);

				if($ir_a_ordenes == 'on'){
				entregarArticulos($whistoria, $wingreso, $infoUbicacion->servicioOrigen, $infoUbicacion->habitacionOrigen,$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino,$wnum_art,$warr_art);
				$contenidoLog = $contenidoLog."Articulos entregados \r\n";
				}

				//Marcar habitacion seleccionada con historia clinica e ingreso
				modificarHistoriaHabitacion($whistoria, $wingreso, $infoUbicacion->habitacionDestino);
				$contenidoLog = $contenidoLog."Marcar habitacion \r\n";

				//Marcar usuario que entrega
				modificarUsuarioMovimientoHospitalario($whistoria,$wingreso,$wusuario);
				$contenidoLog = $contenidoLog."Usuario que entrega: $wusuario \r\n";

				mensajeEmergente("El paciente ha sido entregado con éxito");
			} else {
				mensajeEmergente("El paciente no puede ser entregado ya que la habitación $habitacion->codigo se encuentra ocupada por otro paciente con historia $habitacion->historiaClinica-$habitacion->ingresoHistoriaClinica");
			}
		} else {
			mensajeEmergente("El código de usuario matrix no fue capturado.");
		}
		$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $whistoria, $wingreso);
		$contenidoLog = $contenidoLog."Ubicacion final del paciente: Ubisan: $ubicacionPaciente->servicioAnterior. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:$ubicacionPaciente->habitacionAnterior. Ubihac:$ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";
		unset($wactualizar);
	}

	//Nombre centro de costos
	$cco = new centroCostosDTO();
	$ccoCirugia = new centroCostosDTO();
	$ccoAdmision = new centroCostosDTO();

	$codCcoUrgencias = consultarCcoUrgencias();

	$codCcoCirugia = consultarCcoCirugia();

	// 2012-06-19
	// Se establece que los pacientes que se deben mostrar son los correspondientes
	// al centro de costos de admisiones, ya que los asociados a cirugia, son ambulatorios
	// por esto se crea la funcion consultarCcoAdmision
	$codCcoAdmision = consultarCcoAdmision();

	$cco = consultarCentroCosto($conex,$codCcoUrgencias, $wbasedato);
	$ccoCirugia = consultarCentroCosto($conex,$codCcoCirugia, $wbasedato);
	$ccoAdmision = consultarCentroCosto($conex,$codCcoAdmision, $wbasedato);

	if(!empty($cco->nombre)){
		if(!isset($waccion)){
			$waccion = "";
		}

		echo "<input type='HIDDEN' NAME='waccion' value='".$waccion."'>";

		//a:  Listado de pacientes preentregados urgencias
		//b:  Listado de pacientes preentregados cirugia
		//c:  Ingreso de clave y contraseña para grabar entrega

		switch ($waccion){
			case 'a':
				encabezado("ENTREGA PACIENTES DE URGENCIAS", $wactualiz, "clinica");

				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				echo '<span class="subtituloPagina2">';
				echo "Pacientes admitidos pendientes de entrega en el servicio $codCcoUrgencias - $cco->nombre";
				echo "</span>";
				echo "<br>";
				echo "<br>";

				//Consultar pacientes que se encuentren en preentrega en urgencias.
				$consulta = consultarPacientesPreentregados($codCcoUrgencias);

				//Si hay datos muestra la informacion
				if(sizeof($consulta) > 0){
					echo "<table align='center'>";

					//Encabezados de la tabla
					echo "<tr class='encabezadoTabla'>";
					echo "<td colspan='2' align='center'>Asignaci&oacute;n Cama</td>";
					echo "<td rowspan='2'>Historia clinica</td>";
					echo "<td rowspan='2'>Paciente</td>";
					echo "<td rowspan='2'>Servicio destino</td>";
					echo "<td rowspan='2'>Habitacion destino</td>";
					echo "<td rowspan='2'>Acciones</td>";
					echo "</tr>";
					echo "<tr align='center' class='encabezadoTabla'>";
						echo "<td>Fecha</td>";
						echo "<td>Hora</td>";
					echo "</tr>";

					$cont1=0;
					foreach ($consulta as $pacientePreadmision){

						if(!empty($pacientePreadmision->nombrePaciente)){
							$cont1 % 2 == 0 ? $clase = "fila1": $clase = "fila2";

							$cont1++;

							echo "<tr align='center' class=$clase>";
							echo "<td>$pacientePreadmision->fechaEntrega</td>";
							echo "<td>$pacientePreadmision->HoraEntrega</td>";
							echo "<td>$pacientePreadmision->historiaPaciente - $pacientePreadmision->ingresoHistoriaPaciente</td>";
							echo "<td>$pacientePreadmision->nombrePaciente</td>";
							echo "<td>$pacientePreadmision->ccoDestino </td>";
							echo "<td>$pacientePreadmision->habitacionDestino</td>";
							//marcarEntregaUrgencias(historia, ingreso, usuario)
							echo "<td><a href='javascript:marcarEntrega($pacientePreadmision->historiaPaciente,$pacientePreadmision->ingresoHistoriaPaciente, $pacientePreadmision->ccoActual)'>Entregar</a></td>";
							echo "</tr>";
						}
					}
					echo "</table>";
				} else {
					echo "<center><span class='subtituloPagina2'>No se encontraron pacientes pendientes de entrega</span></center>";
				}
			break;
			case 'b':
				encabezado("ENTREGA PACIENTES DE CIRUGIA", $wactualiz, "clinica");

				//Cuerpo de la pagina
				$query = " SELECT Ccocod codigo, Cconom  nombre, 'cirugia' centroCostos
							 FROM {$wbasedato}_000011
							WHERE Ccocir = 'on'
						    UNION
						   SELECT Ccocod codigo, Cconom  nombre, 'admision' centroCostos
							 FROM {$wbasedato}_000011
							WHERE ccoing = 'on'
							  and ccocir != 'on'
							  and ccourg != 'on'
							  and ccoayu != 'on'
							  and ccohos != 'on'
							  and ccoest = 'on'
							  and ccoadm = 'on'
							  AND Ccoest = 'on'";
				$rs2    = mysql_query( $query, $conex );

				//echo "<div style='height:70%;' align='center'>";
					echo "<br><br><center><table>";
						echo "<tr class='encabezadoTabla'><td> SELECCIONAR CENTRO DE COSTOS </td></tr>";
						echo "<tr><td> ";
							echo "<select  id='ccoConsultado' name='ccoConsultado' value='{$ccoConsultado}' style='width:100%' onchange='enter();'>";
								$option = "<option value=''>&nbsp;</option>";
								while( $row2 = mysql_fetch_assoc( $rs2 ) ){
									( $row2['codigo'] == $ccoConsultado ) ? $option .= "<option selected value='{$row2['codigo']}'>{$row2['codigo']}  -  {$row2['nombre']}</option>" : $option .= "<option value='".$row2['codigo']."'>{$row2['codigo']}  -  {$row2['nombre']}</option>";
									//( $row2['centroCostos'] == "admision" ) ? $option = "<option selected value='on'>{$row2['codigo']}  -  {$row2['nombre']}</option>" : $option = "<option value='".$row2['codigo']."'>{$row2['codigo']}  -  {$row2['nombre']}</option>";
								}
								echo $option;
							echo "</select>";
						echo "</td></tr>";
					echo "</table></center><br>";
					//echo "</div><br><br>";
				echo "<table align='center' border=0>";

				echo '<span class="subtituloPagina2">';
				echo "Pacientes en recuperación pendientes de entrega en el servicio";
				echo "</span>";
				echo "<br>";
				echo "<br>";

				//Seccion pacientes en recuperacion de cirugia
				$cirugia = true;
				if($cirugia){

					//Consultar pacientes que se encuentren en preentrega en urgencias.
					// 2012-06-19
					// Ya no se llama con el coddigo de cirugia sino con el de admisiones
					// para que no consulte los pacientes de cirugia ambulatorios

					if( ( ( !isset( $ccoConsultado ) ) or trim( $ccoConsultado ) == "" ) )
						$ccoConsultado = $codCcoAdmision;

					$consulta = consultarPacientesEnRecuperacion($ccoConsultado);
					/*if( $consultaCirugia == "on" ){
						$consulta = consultarPacientesEnRecuperacion($codCcoCirugia);
					}else{
						echo $codCcoAdmision;
						$consulta = consultarPacientesEnRecuperacion($codCcoAdmision);
					}*/

					//Si hay datos muestra la informacion
					if(sizeof($consulta) > 0){
						echo "<table align='center'>";

						//Encabezados de la tabla
						echo "<tr align='center' class='encabezadoTabla'>";
						echo "<td>Alta</td>";
						echo "<td>Historia clinica</td>";
						echo "<td>Paciente</td>";
						echo "<td>Fecha de ingreso</td>";
						echo "<td>Centro Costos Actual</td>";
						echo "<td>Acciones</td>";
						echo "</tr>";

						$cont1=0;
						foreach ($consulta as $pacienteRecuperacion){

							if(!empty($pacienteRecuperacion->nombrePaciente)){
								$cont1 % 2 == 0 ? $clase = "fila1": $clase = "fila2";

								$cont1++;
								$histPac = $pacienteRecuperacion->historiaPaciente;
								$ingHisPac = $pacienteRecuperacion->ingresoHistoriaPaciente;

								echo "<tr align='center' class=".$clase.">";
								echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:altaPacienteCirugia(".$cont1.");' name='alta".$cont1."' id='alta".$cont1."' value='".$histPac."-".$ingHisPac."'>&nbsp;</td>";
								echo "<td>$histPac - $ingHisPac</td>";
								echo "<td>$pacienteRecuperacion->nombrePaciente</td>";
								echo "<td>$pacienteRecuperacion->fechaIngreso</td>";
								echo "<td>$pacienteRecuperacion->ccoActual</td>";
								echo "<td><a href='javascript:marcarEntregaCirugia($pacienteRecuperacion->historiaPaciente,$pacienteRecuperacion->ingresoHistoriaPaciente,$codCcoAdmision)'>Entregar</a></td>";
								echo "</tr>";
							}
						}
						echo "</table><br>";
					} else {
						echo "<center><span class='subtituloPagina2'>No se encontraron pacientes en recuperación.</span></center>";
					}

					//---> cambio_devolucion ---> pacientes con 2 dias de egresados, para devolver.
					$consultadosEgresados = pacientesEgresados($ccoConsultado);
					//Si hay datos muestra la informacion
					if(sizeof($consultadosEgresados) > 0){

						echo '<span class="subtituloPagina2">';
						echo "Pacientes dados de alta en los últimos dos dias";
						echo "</span><br><br>";
						echo "<table align='center'>";

						//Encabezados de la tabla
						echo "<tr align='center' class='encabezadoTabla'>";
						echo "<td>Regresar</td>";
						echo "<td>Historia clinica</td>";
						echo "<td>Paciente</td>";
						echo "<td>Centro Costos</td>";
						echo "</tr>";

						$cont1=0;
						foreach ($consultadosEgresados as $pacienteRecuperacion){

							if(!empty($pacienteRecuperacion->nombrePaciente)){
								$cont1 % 2 == 0 ? $clase = "fila1": $clase = "fila2";

								$cont1++;
								$histPac = $pacienteRecuperacion->historiaPaciente;
								$ingHisPac = $pacienteRecuperacion->ingresoHistoriaPaciente;

								echo "<tr align='center' class=".$clase.">";
								echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:RegresarPacienteCirugia(".$cont1.");' name='dev".$cont1."' id='dev".$cont1."' value='".$histPac."-".$ingHisPac."'>&nbsp;</td>";
								echo "<td>$histPac - $ingHisPac</td>";
								echo "<td>$pacienteRecuperacion->nombrePaciente</td>";
								echo "<td>$pacienteRecuperacion->ccoActual</td>";
								echo "</tr>";
								echo "<input type='hidden' name='wservicio_dev".$cont1."' id='wservicio_dev".$cont1."' value='$pacienteRecuperacion->ccoActual'>";
							}
						}
						echo "</table><br>";
					} else {
						echo "<center><span class='subtituloPagina2'>No se encontraron pacientes dados de alta en los últimos dos dias</span></center>";
					}
				}
				break;
				case 'c':
					encabezado("ENTREGA PACIENTES DE URGENCIAS", $wactualiz, "clinica");

					echo "<span class='subtituloPagina2'>";
					echo "Ingrese el codigo y clave de usuario en matrix que entrega.  Historia $whistoria-$wingreso";
					echo "</span>";
					echo "<br>";
					echo "<br>";

					//Cuerpo de la pagina
					echo "<table align='center' border=0>";

					//Codigo para registrar la entrega
					echo "<tr><td class='fila1' width=150>C&oacute;digo</td>";
					echo "<td class='fila2' align='center' width=180>";
					echo "<input type=text name=codigo class='textoNormal'>";
					echo "</td>";
					echo "</tr>";

					//Clave para registrar la entrega
					echo "<tr><td class='fila1'>Clave</td>";
					echo "<td class='fila2' align='center'>";
					echo "<input type=password name=clave class='textoNormal'>";
					echo "</td>";
					echo "</tr>";

					echo "</table>";
					echo "<br>";

					$ir_a_ordenes = ir_a_ordenes($wemp_pmla, $ccoOrigen);
					$wnum_art = 0;
					$warr_art = array();
					//Detalle de articulos por entregar.
					if($ir_a_ordenes == 'on'){

						echo "<center><table>";

						echo "<th align=center class=titulo>DETALLE DE ARTICULOS</th>";

						Detalle_ent_rec('NoApl', $whistoria, $wingreso, $wnum_art, $warr_art, $wunidad_completa); //En esta funcion se muestran todos los articulos que tiene saldo

						echo "</table>";
						echo "<HR align=center></hr>";

						$warr_art = base64_encode(serialize($warr_art));

					}
					echo "<input type='HIDDEN' NAME='whistoria' value='".$whistoria."'>";
					echo "<input type='HIDDEN' NAME='wingreso' value='".$wingreso."'>";
					echo "<input type='HIDDEN' id='warr_art' NAME='warr_art' value='".$warr_art."'>";
					echo "<input type='HIDDEN' id='wnum_art' NAME='wnum_art' value='".$wnum_art."'>";
					
					//Si tiene almenos un articulo con unidad completa no mostrara el boton de entregar.
					if(count($wunidad_completa) == 0){
						
					echo "<center>";
					echo "<input type=button name=verificar onclick='javascript:verificarUsuario($whistoria,$wingreso);' value='Verificar usuario'>";
					echo "&nbsp;|&nbsp;<input type=button id='btnEntregar' value='Entregar paciente' disabled onclick='marcarEntregaUrgencias($whistoria,$wingreso, $wnum_art, \"$warr_art\");'>";

					echo "</center>";
					
					}else{
					
						echo "<b>Debe aplicar o devolver los articulos marcados en rojo <br>para poder entregar el paciente.</b>";						
					}
					
					echo "<br><br><input type=button onclick='volver()' value='Retornar'>";
					echo "<br/><div id=cntEntrega></div>";
					echo "<div id=cntUsuario></div>";
				break;
			default:
				echo "<div id='header'>";
				echo "<div id='logo'>";
				echo "<h1>ENTREGA PACIENTES DE CIRUGIA</h1>";
				echo "<h2><b>".$winstitucion."</b>".$wactualiz."</h2>";
				echo "</div>";
				echo "</div></br></br></br>";
				echo "<div id='page' align='center'>";
				echo "Error: no se especificó el tipo de listado.";
				break;
		}
	}
	//Msanchez:**************GRABA LOG**************
	if($debug){
		if($archivo){
			// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
			if (is_writable($nombreArchivo)) {
				// Escribir $contenido a nuestro arcivo abierto.
				fwrite($archivo, $contenidoLog);
				fclose($archivo);
			}
		}
	}
	//Msanchez::***************FIN GRABA LOG*************
	//echo "<br/><center><a href='javascript:mostrarAyuda();'><font><size = 1><u>Ver manual de usuario</u></size></font></a>";
	echo "<br/><br/><center><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></center>";
	liberarConexionBD($conex);
	
	}
	
	echo "</body>
		</html>";
}

if(isset($consultaAjax))
    {
	switch($consultaAjax)
	  {
	    case 'registrarDescarte':
		   echo registrarDescarte($wemp_pmla, $wbasedato, $codart, $whis, $wing, $ccoorigen, $cantidad, $id);
		 break;		
        default :
            break;
      }
    }
?>

