<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>CARGO A PACIENTE DESDE CENTRAL</title>
 <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo4{color:#FFFFFF;background:green;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo5{color:#FFFFFF;background:red;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto7{color:#003366;background:green;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>

   <script type="text/javascript">
document.onkeydown = mykeyhandler; 
	
function mykeyhandler(event) 
   {
      //keyCode 116 = F5
	  //keyCode 122 = F11
	  //keyCode 8 = Backspace
	  //keyCode 37 = LEFT ROW
	  //keyCode 78 = N
	  //keyCode 39 = RIGHT ROW
	  //keyCode 67 = C
	  //keyCode 86 = V
	  //keyCode 85 = U
	  //keyCode 45 = Insert
	 
	  event = event || window.event;
	  var tgt = event.target || event.srcElement;
	  if((event.altKey && event.keyCode==37) || (event.altKey && event.keyCode==39) ||
	  (event.ctrlKey && event.keyCode==78)|| (event.ctrlKey && event.keyCode==67)||
	  (event.ctrlKey && event.keyCode==86)|| (event.ctrlKey && event.keyCode==85)||
	  (event.ctrlKey && event.keyCode==45)|| (event.shiftKey && event.keyCode==45)){
	  event.cancelBubble = true;
	  event.returnValue = false;
	  alert("Función no permitida");
	  return false;
	  }
	 
	  if(event.keyCode==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	    {
	     return false;
	    }
	 
	  if (event.keyCode == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	    {
	     return false;
	    }
	 
	  if ((event.keyCode == 116) || (event.keyCode == 122)) 
	     {
	      if (navigator.appName == "Microsoft Internet Explorer")
	        {
	         window.event.keyCode=0;
	        }
	      return false;
	    }
   }
	
	document.oncontextmenu=new Function("return false");
	
	
   function enter()
   {
	   // try{
	   // a = window.opener;
		//	   window.opener.producto.submit();
		// a.document.producto.submit();
	   // }
	   // catch(e){}

	   try{
		   a = window.opener;
		   //	window.opener.producto.submit();
			a.document.producto.submit();
	   }
	   catch(e){}
	   window.close();
	   // window.onbeforeunload = '';
	   wemp_pmla =  $( "#wemp_pmla" ).val();
	   
	   window.open ("cen_mez.php?wbasedato=cenpro&wemp_pmla="+wemp_pmla+"","_self")
   }

   function desmarcarOpcionesArticulosGenericos(){

	   var tb = document.getElementById( 'tbArticuloGenericos' );

	   for( var i = 0; i < tb.rows.length; i++ ){

		   tb.rows[i].cells[0].firstChild.checked = "";
	   }
   }

//   window.onbeforeunload = function(){
//	   enter();
//   }
    </script>
    
</head>
<body>
<?php
include_once("conex.php");
$desde_CargosPDA = true;
$accion_iq = '';

//actualizacion: Dieciembre 02 de 2021  (Daniel CB) Se realiza corrección de parametro 01 quemado.
//actualizacion: Noviembre 02 de 2017	(Edwin)		Entre el tiempo de dispensación por defecto de CM(10 horas al momento de la publicación) y el tiempo de dispensacion de cco del paciente se toma la de mayor valor.
//													Ejemplo: UCI tiene 8 horas y CM tiene 10 horas de tiempo de dispensación, se toma la de CM por ser mayor
//actualizacion: Septiembre 19 de 2016	(Edwin)		Se corrige calculo de rondas en la función articulosGenericosKE
//actualizacion: Septiembre 12 de 2017	(Jessica)	Se agrega validacion en la funcion esKE para evitar que cuando el paciente tenga ordenes necesite confirmacion del kardex, ya que no se actualizaba la regleta.
//													Se agregan insumos seleccionados por defecto cuando es DA
// 													Al realizar el cargo si es DA muestra la historia, nombre del paciente y la habitacion 
//actualizacion: Septiembre 19 de 2016	(Jessica)	Se agregan insumos por defecto cuando es NPT y cuando es NPT se hace el reemplazo sin importar el centro de costos, al realizar 
// 													el cargo si es NPT muestra la historia, nombre del paciente y la habitacion
//actualizacion: Mayo 25 de 2016	(Edwin MG)		Se agrega validación para que al momento de cargar un paciente que tenga ordenes no quede aplicado automáticamente un producto
//actualizacion: Marzo 12 de 2015	(Edwin MG)		Se valida que halla conexión unix en inventario desde matrix, si no hay conexión
//actualizacion: Agosto 14 de 2013	(Edwin MG)		Se valida que halla conexión unix en inventario desde matrix, si no hay conexión
//													con unix se activa la contigencia de dispensación.
//actualizacion: Marzo 18 de 2013 		(Edwin MG)	Si un paciente se encuentra en urgencias se puede cargar cualquier producto.
//													Si un paciente fue dado de alta desde urgencias, se permite cargar medicamentos hasta a lo mas x horas desde que fue dado de alta. El tiempo x
//													es parametrizado desde root_000051 como tiempoEgresoUrgencia.
//actualizacion: Febrero 25 de 2013 	(Edwin MG)	Cambios varios para cuando no hay conexión con UNIX. Entre ellos se registra el movimiento en tabla de paso
//									        		y se mira los saldos en matrix y no en UNIX.
//actualizacion: Enero 25 de 2012	(Mario Cadavid)	En la función reemplazarMedicamentoKardex se incluyó la validación de saldo y ronda para los artículos que no tienen el mismo código y fecha y hora de inicio del artículo a reemplzar. Tambien en la funcion articulosGenericosKE se agreg{o la validacion que determina si el articulo tiene saldo pendiente para grabar
//actualizacion: Agosto 28 de 2012		(Edwin MG)	Si el pacinte se encuentra en un cco con tiempo de dispensación, se dispensa de acuerdo a dicho tiempo de dispensación
//actualizacion: Junio 28 de 2012		(Edwin MG)	Se organiza query que determina si un articulo tiene saldo para poder cargar medicamento. Había conflicto cuando el articulo estaba
//													Repetido para el paciente en el perfil con horas distintas.
//actualizacion: Mayo 9 de 2012			(Edwin MG)	La hora de aplicacion automatica queda siempre como {HoraMilitar} - {AM|PM}
//actualizacion: Abril 2 de 2012		(Edwin MG)	Se deshabilitra por javascript, click derecho del mouse y tecla F5, para evitar recarga de la pagina.
//												 	Esto con el fin de evitar recarga de pagina
//actualizacion: Marzo 14 de 2012		(Edwin MG)	Se agrega funcion crearKardexAutomaticamente.
//actualizacion: Febrero 21 de 2012		(Edwin M)	Se agrega a la auditoria del kardex el campo kauido(id original) al momento de hacer reemplazo de un articulo
//actualizacion: Noviembre 8 de 2011	(Edwin M)	Al registrar una aplicacion de articulo, la fraccion y dosis corresponden a la tabla de fracciones por articulo (movhos_000059),
//													esto se hace con la funcion actualizandoAplicacionFraccion que se encuentra en cargosinc.php en el include, y se desactiva la funcion
//													actualizandoAplicacion por codigo.
//actualizacion: Septiembre 16 de 2011 	(Edwin M)	Se corrige grabacion de medicamentos. En ocasiones dejaba grabar mas de lo que se debe.
//actualizacion: Mayo 11 de 2011 		(Edwin M)	Si el articulo se aplica automaticamente, en la aplicación colocar cantidad de fraccion y dosis del articulo
//actualizacion: Febrero 23 de 2011 	(Edwin M)	Se asegura que no existe un medicamento con el mismo codigo y con fecha y hora de inicio igual al que se va a reemplazar y cargar.	
//actualizacion: Febrero 21 de 2011 	(Edwin M)	Cuando se hace un reemplazo de articulos para DA o NU, se pide la bolsa completa
//actualizacion: Febrero 15 de 2011 	(Edwin M)	Para los MMQ adicionales que se cargan para DA o NU, no se muestran los que estan en 0
//actualizacion: Enero 7 de 2011 		(Edwin M)	Se corrige la aplicación de MMQ, para ello se realiza los cambios correspondientes sobre la tabla 4 de movhos
//actualizacion: 2010-09-13 Se da la opcion de reemplazar el codigo generico NUXXXX por el articulo que se va a cargar
//					ya sea de quimioterapia, dosis adaptada o nutriciones
//actualizacion: 2010-07-30 Se deja cargar al carro cualquier medicamento que no necesita estar en el kardex
//actualizacion: 2007-11-06 se crea la opcion del carro
//Actualización (sebastian.nevado): 2021-06-11 se realiza llamado de factura inteligente para insumos.
//Actualización (sebastian.nevado): 2021-07-22 reduzco el inventario para los insumos. NOTA: no se valida que tenga existencias para hacer movimiento debido a que no había sido reportado, por lo que el inventario puede quedar negativo.
//Actualización (sebastian.nevado): 2021-10-19 Se comenta el llamado a la función donde reduzco el inventario para los insumos por solicitud del usuario.


function consultarHabitacion($historia,$ingreso)
{
	global $conex;
	global $bd;
	
	$bdMovhos = "movhos";
	
	$paciente 			= consultarUbicacionPaciente($conex, $bdMovhos, $txHistoria, $ingreso_paciente );
	$tablaHabitaciones	= consultarTablaHabitaciones( $conex, $bdMovhos, $paciente->servicioActual );
	
	$q = "SELECT Habcpa
			FROM ".$bd."_000018,". $tablaHabitaciones ."
		   WHERE Ubihis = '".$historia."'
			 AND Ubiing = '".$ingreso."'
			 AND Ubihis = Habhis
			 AND Ubiing = Habing;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$habitacion="";
	if($num > 0)
	{
		if($rows = mysql_fetch_array($res))
		{
			$habitacion = $rows['Habcpa'];
		}
	}
	
	return $habitacion;
}

function consultarNombrePaciente($historia,$ingreso)
{
	global $conex;
	global $wemp_pmla;
	
	$q = "SELECT Pacno1,Pacno2,Pacap1,Pacap2 
			FROM root_000037,root_000036 
		   WHERE Orihis='".$historia."' 
			 AND Oriing='".$ingreso."' 
			 AND Oriori='".$wemp_pmla."' 
			 AND Oriced=Pacced 
			 AND Oritid=Pactid;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$paciente="";
	if($num > 0)
	{
		if($rows = mysql_fetch_array($res))
		{
			$paciente = $rows['Pacno1']." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'];
		}
	}
	
	return $paciente;
}
function centroCostosCM()
	{
		global $conex;
		global $bd;
		
		$sql = "SELECT
					Ccocod
				FROM
					".$bd."_000011
				WHERE
					ccofac LIKE 'on'
					AND ccotra LIKE 'on'
					AND ccoima !='off'
					AND ccodom !='on'
				";
		
		$res= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if ( mysql_num_rows($res) > 1 )
	{
		return "Hay más de 1 centro de costos con los mismos parámetros";
	}
	$rows = mysql_fetch_array( $res );
	return $rows[ 'Ccocod' ];
	}
/**********************************************************************************************
 * Si se hace una aplicacion se actualiza el campo Unidad de fraccion y cantidad de fraccion
 * segun el kardex en la tabla 15 de movhos
 * 
 * @return unknown_type
 * 
 * Mayo 11 de 2011
 **********************************************************************************************/
function actualizandoAplicacion( $idKardex, $cco, $art, $num, $lin ){
	return; //Noviembre 8 de 2011
	global $conex;
	global $bd;
	
	$sql = "SELECT
				Kadhis, Kading, Kadcfr, Kadufr
			FROM
				{$bd}_000054
			WHERE
				id = '$idKardex'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		$sql = "UPDATE
					{$bd}_000015
				SET
					Aplufr = '{$rows['Kadufr']}',
					Apldos = '{$rows['Kadcfr']}'
				WHERE
					Aplhis = '{$rows['Kadhis']}'
					AND Apling = '{$rows['Kading']}'
					AND Aplart = '{$art}'
					AND Aplcco = '{$cco}'
					AND Aplnum = '$num'
					AND Apllin = '$lin'					
				";
					
		$resApl = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

/**
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function ArticulosXPacienteSinSaldo( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	global $bd;
	
	$articulos = array();		//Guarda los articulos con saldo positivos
	$vacios = array();		//Guarda los articulos con saldo en 0
	$numrows = false; 
	
	$sql = "SELECT 
				sum(kadcdi) as cdi, sum(kaddis) as dis, 
				kadart as cod, artcom as nom
	        FROM 
				{$bd}_000054 a, {$wbasedato}_000002 b
	        WHERE 
	        	kadhis='$his'  
	        	AND kading='$ing'
	        	AND a.kadfec = '".date("Y-m-d")."'
	        	AND kadcon = 'on'
	        	AND kadsus != 'on'
	        	AND kadori = 'CM'
	        	AND artcod = kadart
	        	AND kadare = 'on'
	        GROUP BY kadart
	        ";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - en el query: ".$sql." - ".mysql_error() );
	
	for($i = 0; $rows = mysql_fetch_array( $res ) ; $i++ ){
		$articulos[$i]['cod'] = $rows['cod'];
		$articulos[$i]['nom'] = $rows['nom'];
		$articulos[$i]['cdi'] = $rows['cdi'];
		$articulos[$i]['dis'] = $rows['dis'];
		$articulos[$i]['sal'] = $rows['cdi'] - $rows['dis'];
		
		if( $articulos[$i]['sal'] > 0 ){
			return false;
		}
	}
	
	return true;
}


/**
 * Devuelve el el codigo de la central del camillero y el nombre del camillero de acuerdo al codigo de usuario de matrix
 */
function buscarCodigoNombreCamillero(){
	
	global $conex;
	global $bd;
	global $bdCencam;
	
	//$bdCencam = "cencam";
	
	$val = '';
	
	$sql = "SELECT
				codigo, nombre
			FROM
				{$bdCencam}_000002 a,{$bdCencam}_000006 b
			WHERE
				b.codcen = 'SERFAR'
				AND cenest = 'on'
				AND a.codced = cenope
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['codigo']." - ".$rows['nombre'];
	}
	
	return $val;
}



/**
 * Crea una petición a la Central de camilleros
 * 
 * @param $origen		Centro de costos de origen que pide el camillero
 * @param $motivo		Motivo de la petición
 * @param $hab			Habitación destino, debe aparecer la habitación y el nombre del paciente
 * @param $destino		Nombre cco destino
 * @param $solicita		Quien solicita el servicio
 * @param $cco			Nombre del sevicioq que solicita el servicio	
 * @return unknown_type
 */
function crearPeticionCamillero( $origen, $motivo, $hab, $destino, $solicita, $cco, $camillero ){
	return;
	global $conex;
	global $bdCencam;
	
	//$bdCencam = "cencam";
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO 
				{$bdCencam}_000003(    medico  , fecha_data, hora_data,   Origen ,  Motivo  , Habitacion, Observacion,   Destino ,  Solicito  , Ccosto, Camillero   , Hora_respuesta, Hora_llegada, Hora_cumplimiento, Anulada, Observ_central, Central, Usu_central,   Seguridad   )
							VALUES( '$bdCencam',  '$fecha' ,  '$hora' , '$origen', '$motivo',   '$hab'  ,     ''     , '$destino', '$solicita', '$cco', '$camillero',   '$hora'     ,  '00:00:00' ,     '00:00:00'   ,   'No' ,    ''         ,'SERFAR',   ''      , 'C-$bdCencam' )
			"; //echo "<br><br>$camillero.....".$sql;
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}


/**
 * Busca si un artículo existe y esta activo dentro de la tabla de artículos de MATRIX
 * en Central de produccion.
 * 
 * 
 * @table 000002 de CENPRO SELECT 
 * 
 * @version 2007-07-17
 * @param Array	$art	Información del artículo.</br>
 * 						Información que debe estar en el arreglo antes de llamar la función:
 * 						[cod]:Código del artículo.</br>
 * 						Información que la función ingresa al arreglo
 * 						[nom]:Nombre generico del artículo.</br>
 * 						[uni]:Uidades del artículo. 						
 * 						[gru]:grupo al que pertenece el artículo.
 * @param Array	$error	Información del error</br>
 * 						[ok]:Descripción corta.</br>
 * 						[codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010</br>
 * 						[codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.</br>
 * 						[descSis]:Descripción del error del sistema.
 * @return Boolean
 */


function consultarNombreHabitacion( $his, $ing, &$hab, &$nom ){
	
	global $conex;
	global $bd;
	
	$sql = "SELECT
				ubihac, CONCAT( pacno1,' ', pacno1, ' ', pacap1, ' ', pacap2 ) 
			FROM
				{$bd}_000018, root_000036, root_000037
			WHERE
				ubihis = '$his'
				AND ubiing = '$ing'
				AND orihis = ubihis
				AND oriced = pacced
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$hab = $rows[0];
		$nom = $rows[1];
	}
}

/************************************************************
 * Indica si el paciente tiene articulos aun para dispensar
 * 
 * @param $lista
 * @return unknown_type
 ************************************************************/
function hayArticulosConSaldo( $lista ){
	
	for( $i = 0; $i < count($lista); $i++ ){
		if( @$lista[$i][1] != 0 ){
			return true;
		}
	}
	
	return false;
}

function nombreCcoCentralCamilleros( $codigo ){
	
	global $conex;
	global $bd;
	global $bdCencam;
	
	$val = '';
	
	$sql = "SELECT
				Nombre
			FROM
				{$bdCencam}_000004
			WHERE
				SUBSTRING_INDEX( cco, '-', 1 ) = '$codigo'
				AND Estado = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Nombre'];
	}
	
	return $val;
}

function peticionCamillero( $cbCrearPeticion, $ccoCam, $hab, $solicita, $origen, $destino, $paciente ){
	
	$val = '';
	
	if( $cbCrearPeticion == 'on' ){
		
		$motivo = 'DESPACHO DE MEDICAMENTOS';
		
		$nomCcoDestino = nombreCcoCentralCamilleros( $destino ); 
		
//		$val = crearPeticionCamillero( nombreCcoCentralCamilleros( $origen ), $motivo, "<b>".$hab."</b><br>".$paciente, $nomCcoDestino, $solicita, $nomCcoDestino );
	}
}



/******************************************************************************************************************
 * Registra la aplicacion de un articulo
 * 
 * @param $his	Historia
 * @param $ing	Ingreso
 * @param $art	Articulo
 * @param $can	Cantidad
 * @param $cco	Centro de costos
 * @param $des	Descripcion del articulo
 * @return unknown_type
 ******************************************************************************************************************/
function registrarMovimientoAplicacion( $his, $ing, $art, $can, $cco, $des, $num, $lin ){
	
	global $conex;
    global $bd;
    global $wusuario;
    
    $fecha = date( "Y-m-d" );
    $hora = date( "H:i:s" );
    
    $ronda = gmdate( "H:00 - A", floor( date( "H" )/2 )*2*3600 );
    
    $sql = "INSERT INTO {$bd}_000015( medico , fecha_data, hora_data, Aplhis, Apling,  Aplron , Aplart, Apldes, Aplcan, Aplcco,   Aplusu    , Aplapr, Aplest, Aplaap, Aplnde, Aplfde, Apldde, Aplnum , Apllin, Aplapv, Aplfec  ,    Aplapl  ,   Seguridad  )
    						  VALUES( '{$bd}',  '$fecha' ,  '$hora' , '$his', '$ing', '$ronda', '$art', '$des', '$can', '$cco', '$wusuario' , 'off' ,  'on' ,   '0'  ,   ''  ,   ''  ,   '' ,  '$num', '$lin', 'off' , '$fecha', '$wusuario', 'C-$wusuario' )";
    
    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    	
	if( mysql_affected_rows() > 0 ){
    	return true;
    }
    else{
    	return false;
    }
}

/********************************************************************************
 * Registra saldo de insumos de preparacion
 * 
 * @param $his		Historia
 * @param $ing		Ingreso
 * @param $art		Articulo
 * @param $can		Cantidad a cargar del articulo
 * @param $cco		Centro de costos
 * @return unknown_type
 ********************************************************************************/
function registrarSaldoInsumosPreparacion( $his, $ing, $art, $can, $cco ){
	
	global $conex;
    global $bd;
    global $wusuario;
    
    $sql = "SELECT
    			id
    		FROM
    			{$bd}_000004
    		WHERE
    			spahis = '$his'
    			AND spaing = '$ing'
    			AND spaart = '$art'
    			AND spacco = '$cco'
    		";
    			
    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    $numrows = mysql_num_rows( $res );
	
	if( $numrows == 0 ){
		
		$fecha = date( "Y-m-d" );
    	$hora = date( "H:i:s" );
    
	    $sql = "INSERT INTO {$bd}_000004( medico , fecha_data, hora_data, Spahis, Spaing, Spacco, Spaart, Spamen, Spamsa, Spauen, Spausa, Spaaen, Spaasa,   Seguridad   )
	    						  VALUES( '{$bd}',  '$fecha' ,  '$hora' , '$his', '$ing', '$cco', '$art',   '0' ,   '0' , '$can', '$can', '$can', '$can', 'C-$wusuario' )";
	    
	    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	    if( mysql_affected_rows() > 0 ){
	    	return true;
	    }
	    else{
	    	return false;
	    }
	}
	else{
		
		$rows = mysql_fetch_array( $res );
		
	 	$sql = "UPDATE
	 				{$bd}_000004
	 			SET
	 				spaasa = spaasa + $can,
	 				spausa = spausa + $can
	 			WHERE
	 				id = '{$rows['id']}'
	    		";
	    
	    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	    if( mysql_affected_rows() > 0 ){
	    	return true;
	    }
	    else{
	    	return false;
	    }
	}
}


function registrarFraccion( $id, $cco ){
	
//	return;
	
	global $conex;
    global $bd;
    global $wusuario;
    global $wbasedato;
    
    $sql = "SELECT
    			*
    		FROM
    			{$bd}_000054
    		WHERE
    			id = '$id'
			";
    			
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
    $numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		$sql = "SELECT
	    			*
	    		FROM
	    			{$bd}_000059
	    		WHERE
	    			defcco = '$cco'
	    			AND defart = '{$rows[ 'Kadart' ]}'
				";
	    			
		$res1 =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
		$numrows1 = mysql_num_rows( $res1 );
		
		if( $numrows1 <= 0 ){
		
			$hora = date( "H:i:s" );
			$fecha = date( "Y-m-d" );
			
			$sql = "SELECT
						Arttip, Arttve
					FROM
						{$wbasedato}_000002 e
					WHERE
						e.artcod = '{$rows['Kadart']}'
						AND e.artest = 'on'
					";
						
			$res4 =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
			$rows4 = mysql_fetch_array( $res4 );
			
			$sql = "SELECT
						Artuni
					FROM
						{$wbasedato}_000002, {$bd}_000068, {$wbasedato}_000001
					WHERE
						artcod = arkcod
						AND arktip = tiptpr
						AND arttip = tipcod
						AND tipcod = '{$rows4['Arttip']}'
					";
											
			$res3 =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
			
			if( $rows3 = mysql_fetch_array( $res3 ) ){
				
				$tiempoVencimiento = intval( $rows4['Arttve']/24 );
		
				$sql = "INSERT INTO {$bd}_000059( Medico, fecha_data, hora_data, Defcco ,      Defart        , Deffra,	     Deffru       , Defest , Defven ,       Defdie        , Defdis, Defdup , Defcon, Defnka, Defdim , Defdom ,       Defvia       ,   Seguridad  )
										  VALUES( '$bd' , '$fecha'  , '$hora'  , '$cco' , '{$rows['Kadart']}',   '1' , '{$rows3['Artuni']}',  'on'  , 'on'  , '$tiempoVencimiento', 'on' ,  'on'  ,  'on' ,   ''  ,   ''   ,    ''  , '{$rows['Kadvia']}', 'C-$wusuario')
						";
			    			
				$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
				
				if( true || $rows2 = mysql_fetch_array($res1) ){
					
					if( true || $rows2['Deffru'] != $rows[ 'Kadufr' ] ){
						
						$cantidadFraccion = ($rows[ 'Kadcfr' ]/$rows[ 'Kadcma' ]);
	
						//kaduma = '{$rows2['Deffru']}',
						$sql = "UPDATE
									{$bd}_000054 a, {$wbasedato}_000002 b
								SET
									kaduma = artuni,
									kadcma = '1',
									kadcfr = '$cantidadFraccion',
									kadufr = '{$rows3['Artuni']}'
								WHERE
									a.id = '$id'
									AND artcod = '{$rows['Kadart']}' 
								";
	
						$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
						
						return true;
						
					}
				}
				else{
					return false;
				}
			}
		}
		else{
			
			if( $rows2 = mysql_fetch_array($res1) ){
				
				if( true || $rows2['Deffru'] != $rows[ 'Kadufr' ] ){
					
					$cantidadFraccion = ceil( $rows[ 'Kadcfr' ]/$rows[ 'Kadcma' ] )*$rows2['Deffra'];	//Febrero 21 de 2011
					
					//verifico si no hya otro medicamento con la misma fecha y hora de incio
					//De ser así se suspende el articulo actual y se activa el otro
					$sql = "SELECT
								id
							FROM
								{$bd}_000054
							WHERE
								kadart = '{$rows['Kadart']}'
								AND kadhis = '{$rows['Kadhis']}'
								AND kading = '{$rows['Kading']}'
								AND kadhin = '{$rows['Kadhin']}'
								AND kadfin = '{$rows['Kadfin']}'
								AND kadfec = '".date("Y-m-d")."'
							";
								
					$resRepetidos = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
					$numRepetidos = mysql_num_rows( $resRepetidos );
					
					if( true || $numRepetidos <= 0 ){

						//kaduma = '{$rows2['Deffru']}',
						$sql = "UPDATE
									{$bd}_000054 a, {$wbasedato}_000002 b
								SET
									kaduma = artuni,
									kadcma = '{$rows2['Deffra']}',
									kadcfr = '$cantidadFraccion',
									kadufr = '{$rows2['Deffru']}'
								WHERE
									a.id = '$id'
									AND artcod = '{$rows['Kadart']}' 
								";
	
						$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
						
						return true;
					}
					else{
						
						$rowsRepetidos = mysql_fetch_array( $res );
						
						$sql = "UPDATE
									{$bd}_000054 a, {$wbasedato}_000002 b
								SET
									kaduma = artuni,
									kadcma = '{$rows2['Deffra']}',
									kadcfr = '$cantidadFraccion',
									kadufr = '{$rows2['Deffru']}',
									kadsus = 'off'
								WHERE
									a.id = '{$rowsRepetidos['id']}'
									AND artcod = '{$rows['Kadart']}' 
								";
	
						$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
						
						$sql = "UPDATE
									{$bd}_000054 a
								SET
									kadsus = 'on'
								WHERE
									a.id = '$id'
								";
	
						$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
						
						return true;
					}
				}
			}
			
			return false;
		}
	}
	else{
		return false;
	}
}

/**
 * 
 * @param $id
 * @param $art
 * @param $suspendido	Código del articulo suspendido
 * @return unknown_type
 */
function registrarAuditoria( $id, $art, $suspendido = '' ){
	
	global $conex;
    global $bd;
    global $wusuario;
    
    $sql = "SELECT
    			*
    		FROM
    			{$bd}_000054
    		WHERE
    			id = '$id'
			";
    			
	$res1 =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numrows1 = mysql_num_rows( $res1 );
	
	$des = "A:A02AD2,,ML,00,SF, N:A02B04,,MG,16,SF,,";
	$desSus = "";
	
	if( $numrows1 > 0 ){
		
		if( !empty($suspendido) ){
			$desSus = ",Suspendido: $suspendido";
		}
		
		$rows = mysql_fetch_array( $res1 );
		
		$des = "A:{$rows['Kadart']},{$rows['Kadfin']},{$rows['Kadhin']},{$rows['Kadcfr']},{$rows['Kaduma']} N:{$art},{$rows['Kadfin']},{$rows['Kadhin']},{$rows['Kadcfr']},{$rows['Kaduma']}$desSus";
		
		$his = $rows[ 'Kadhis' ];
		$ing = $rows[ 'Kading' ];
		
		$men = "Articulo ha sido reemplazado desde cargos de central de mezclas";
	    
	    $hora = date( "H:i:s" );
		$fecha = date( "Y-m-d" );
	    
		$ido = $rows[ 'Kadido' ];
		
	    $sql = "INSERT INTO {$bd}_000055 ( Medico , Fecha_data, Hora_data, Kauhis, Kauing,  Kaufec , Kaudes, Kaumen, Kauido, Seguridad   )
	    							VALUE( '{$bd}',  '$fecha' ,  '$hora', '$his', '$ing', '$fecha', '$des', '$men' , '$ido','C-$wusuario')
				";
	    			
		$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	    
		if( mysql_affected_rows() > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

/**
 * Indica si se debe reemplazar el articulo en el kardex electronico si es un codigo genérico se debe ejecutar
 * 
 * @param $cco
 * @return unknown_type
 */
function saltarValidacionReemplazo( $cco ){
	
	global $conex;
    global $bd;
    global $wbasedato;
    
	$sql = "SELECT
    			*
    		FROM
				{$wbasedato}_000001
    		WHERE
    			tiptpr <> ''
    			AND tiptpr <> 'NO APLICA'
			";
    			
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows == 0 ){
		return false;
	}
    
    $sql = "SELECT
    			Ccokar
    		FROM
    			{$bd}_000011
    		WHERE
    			ccocod = '$cco'
    			AND ccokar != 'on'
			";
    			
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		return false;
	}
	else{
		return true;
	}
}

/**
 * Reemplza el articulo generico del kardex
 * @param $id
 * @param $art
 * @return unknown_type
 */
function reemplazarMedicamentoKardex( &$id, $art ){
	
	if( empty( $id ) ){
		return;
	}
	
	global $conex;
    global $bd;
    
    $nka = false;
    
    $sql = "SELECT
    			*
    		FROM
    			{$bd}_000054
    		WHERE
    			id = '$id'
			";
					
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	
	$registroOriginal = mysql_fetch_array( $res );

    //Busco si no hay un articulo con el mismo codigo con fecha y hora inicio igual para el paciente
    $sql = "SELECT
    			*
    		FROM
    			{$bd}_000054
    		WHERE
    			kadhis = '{$registroOriginal['Kadhis']}'
    			AND kading = '{$registroOriginal['Kading']}'
    			AND kadfec = '".date("Y-m-d")."'
    			AND kadart = '$art'
    			AND kadhin = '{$registroOriginal['Kadhin']}'
    			AND kadfin = '{$registroOriginal['Kadfin']}'
    			AND kadest = 'on'
			";
					
	$resRepetidos =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numRepetidos = mysql_num_rows($resRepetidos);

	// Si no hay artículo con el mismo código y fecha y hora de inicio
    if( $numRepetidos <= 0 )
	{
		
    	/*******************************************************************************************/
		// Enero 25 de 2012
		
		$sql = "SELECT
					*
				FROM
					{$bd}_000054
				WHERE
					id = '$id'
				";
						
		$resReemplazar =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
		$numReemplazar = mysql_num_rows($resReemplazar);

    	$val = false;
    	$rowsReemplazar = mysql_fetch_array( $resReemplazar );
    	
    	
    	$tiempoDispensacion = consultarTiempoDispensacionIncCM( $conex, '01' );
    	
    	$rondaActual = ( ceil( date("H")/$tiempoDispensacion )*$tiempoDispensacion );

    	if( $rondaActual < 10 ){
    		$rondaActual = "0".$rondaActual.":00:00";
    	}
    	else{
    		if( $rondaActual == 24 ){
    			$rondaActual = $rondaActual.":00:00";
    		}
    		else{
    			$rondaActual = "00:00:00";
    		}
    	}
    	
    	$sinDis = cantidadSinDispensarRondas( $rowsReemplazar['Kadcpx'], $rondaActual );
    	
    	$exp = explode(",", $rowsReemplazar['Kadcpx'] );
    	for($i = 0; $i < count( $exp ); $i++ ){
    		
    		list( $hor, $cdi, $dis ) = explode( "-", $exp[$i] );
    		
    		if( $hor == $rondaActual ){
    			$sinDis += $cdi - $dis;
    			break;
    		}
    	}

		/*******************************************************************************************/
    	
    	if( $rowsReemplazar['Kadcdi'] > $rowsReemplazar['Kaddis'] && $sinDis > 0 ){
    		
			registrarAuditoria( $id, $art );
			
			//Actualizo el articulo
			$sql = "UPDATE
						{$bd}_000054
					SET
						kadart = '$art',
						kadhdi = '".date( "H:i:s" )."'
					WHERE
						id = '{$id}'
					";
						
			$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
			
	    	$val = true;
			
			registrarAuditoria( $id, $art, $registroOriginal['Kadart'] );
    	}
		
		return $val;

    }
    // Si hay artículo con el mismo codigo y fecha y hora de incio
	else
	{
    	
    	$val = false;
    	$rowsRepetidos = mysql_fetch_array( $resRepetidos );
    	
    	
    	$tiempoDispensacion = consultarTiempoDispensacionIncCM( $conex, '01' );
    	
    	$rondaActual = ( ceil( date("H")/$tiempoDispensacion )*$tiempoDispensacion );

    	if( $rondaActual < 10 ){
    		$rondaActual = "0".$rondaActual.":00:00";
    	}
    	else{
    		if( $rondaActual == 24 ){
    			$rondaActual = $rondaActual.":00:00";
    		}
    		else{
    			$rondaActual = "00:00:00";
    		}
    	}
    	
    	$sinDis = cantidadSinDispensarRondas( $rowsRepetidos['Kadcpx'], $rondaActual );
    	
    	$exp = explode(",", $rowsRepetidos['Kadcpx'] );
    	for($i = 0; $i < count( $exp ); $i++ ){
    		
    		list( $hor, $cdi, $dis ) = explode( "-", $exp[$i] );
    		
    		if( $hor == $rondaActual ){
    			$sinDis += $cdi - $dis;
    			break;
    		}
    	}
    	
    	if( $rowsRepetidos['Kadcdi'] > $rowsRepetidos['Kaddis'] && $sinDis > 0 ){
    		
	    	//Suspendo articulo el original
	    	$sql = "UPDATE
						{$bd}_000054
					SET
						kadsus = 'on'
					WHERE
						id = '{$id}'
					";
						
			$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
			
			
			$sql = "UPDATE
						{$bd}_000054
					SET
						kadart = '$art',
						kadsus = 'off'
					WHERE
						id = '{$rowsRepetidos['id']}'
					";
						
			$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
			
	    	$val = true;
			
			$id = $rowsRepetidos['id'];
			
			registrarAuditoria( $id, $art, $registroOriginal['Kadart'] );
    	}
		
		return $val;
    }
}

function articulosGenericosKE( $his, $ing, $art ){

	global $conex;
    global $bd;
    global $wbasedato;
    global $cco;
    global $servicio;
	global $tempRonda;

    $exp = explode('-', $cco);
    $centro = $exp[0];
    
    $nka = false;

    //Buscando si el articulo es Material Medico Quirurgico
    //MMQ
	$sql = "SELECT
				kadart, a.id, kadfin, kadhin, artcom, kadobs
			FROM
				{$bd}_000054 a, {$wbasedato}_000002 b, {$wbasedato}_000001 c, {$bd}_000068 d
			WHERE
				kadhis = '$his'
				AND kading = '$ing'
				AND kadori = 'CM'
				AND kadfec = '".date( "Y-m-d" )."'
				AND kadest = 'on'
				AND artcod = arkcod
				AND artest = 'on'
				AND arttip = tipcod
				AND tipest = 'on'
				AND tiptpr != ''
				AND arkcod = kadart
				AND arkest = 'on'
				AND arkcco = '$centro'
				AND arktip = tiptpr
				AND kadare = 'on'
				AND kadcon = 'on'
				AND kadsus != 'on'
				AND tipcod IN(
					SELECT
						arttip
					FROM
						{$wbasedato}_000002 e
					WHERE
						e.artcod = '$art'
						AND e.artest = 'on'
				)
			UNION
			SELECT
				kadart, a.id, kadfin, kadhin, artcom, kadobs
			FROM
				{$bd}_000054 a, {$wbasedato}_000002 b, {$wbasedato}_000001 c
			WHERE
				kadhis = '$his'
				AND kading = '$ing'
				AND kadori = 'CM'
				AND arttip = '02'
				AND kadest = 'on'
				AND kadfec = '".date("Y-m-d")."'
				AND kadcdi-kaddis > 0
				AND artest = 'on'
				AND arttip = tipcod
				AND tipest = 'on'
				AND tiptpr != ''
				AND kadare = 'on'
				AND kadcon = 'on'
				AND kadsus != 'on'
				AND kadart = artcod
				AND tipcod IN(
					SELECT
						arttip
					FROM
						{$wbasedato}_000002 e
					WHERE
						e.artcod = '$art'
						AND e.artest = 'on'
				)
			GROUP BY
				kadart
			";
						

	//Busco los articulos genericos por el cual se puede reemplazar un producto
    //Tres union:
    //1. Articulos genericos diferentes a Dosis adaptadas y nutriciones parenterales
    //2. Articulos genericos que son dosis adaptadas
    //3. ARticulos genericos que son nutriciones
	$sql = "SELECT
				kadart, a.id, kadfin, kadhin, artcom, kadobs, Kadcpx
			FROM
				{$bd}_000054 a, {$wbasedato}_000002 b, {$wbasedato}_000001 c, {$bd}_000068 d
			WHERE
				kadhis = '$his'
				AND kading = '$ing'
				AND kadori = 'CM'
				AND kadfec = '".date( "Y-m-d" )."'
				AND kadest = 'on'
				AND artcod = arkcod
				AND artest = 'on'
				AND arttip = tipcod
				AND tippro = 'on'
				AND tipcdo = 'off'
				AND tipnco = 'on'
				AND tipina = 'on'
				AND tipest = 'on'
				AND tiptpr != ''
				AND arkcod = kadart
				AND arkest = 'on'
				AND arkcco = '$centro'
				AND arktip = tiptpr
				AND kadare = 'on'
				AND kadcon = 'on'
				AND kadsus != 'on'
				AND tipcod IN(
					SELECT
						arttip
					FROM
						{$wbasedato}_000002 e, {$wbasedato}_000001 f
					WHERE
						e.artcod = '$art'
						AND e.arttip = tipcod
						AND f.tippro = 'on'
						AND f.tipcdo = 'off'
						AND f.tipnco = 'on'
						AND f.tipina = 'on'
						AND e.artest = 'on'
				)
			UNION
			SELECT
				kadart, a.id, kadfin, kadhin, artcom, kadobs, Kadcpx
			FROM
				{$bd}_000054 a, {$wbasedato}_000002 b, {$wbasedato}_000001 c, {$bd}_000068 d
			WHERE
				kadhis = '$his'
				AND kading = '$ing'
				AND kadori = 'CM'
				AND kadfec = '".date( "Y-m-d" )."'
				AND kadest = 'on'
				AND artcod = arkcod
				AND artest = 'on'
				AND arttip = tipcod
				AND tippro = 'on'
				AND tipcdo = 'off'
				AND tipnco = 'on'
				AND tipina = 'off'
				AND tipest = 'on'
				AND tiptpr != ''
				AND arkcod = kadart
				AND arkest = 'on'
				AND arkcco = '$centro'
				AND arktip = tiptpr
				AND kadare = 'on'
				AND kadcon = 'on'
				AND kadsus != 'on'
				AND CONCAT( tippro, tipcdo, tipnco, tipina ) IN(
					SELECT
						CONCAT( tippro, tipcdo, tipnco, tipina )
					FROM
						{$wbasedato}_000002 e, {$wbasedato}_000001 f
					WHERE
						e.artcod = '$art'
						AND e.arttip = tipcod
						AND f.tippro = 'on'
						AND f.tipcdo = 'off'
						AND f.tipnco = 'on'
						AND f.tipina = 'off'
						AND e.artest = 'on'
				)
			UNION
			SELECT
				kadart, a.id, kadfin, kadhin, artcom, kadobs, Kadcpx
			FROM
				{$bd}_000054 a, {$wbasedato}_000002 b, {$wbasedato}_000001 c
			WHERE
				kadhis = '$his'
				AND kading = '$ing'
				AND kadori = 'CM'
				AND tippro = 'on'
				AND tipcdo = 'off'
				AND tipnco = 'off'
				AND kadest = 'on'
				AND kadfec = '".date("Y-m-d")."'
				AND artest = 'on'
				AND arttip = tipcod
				AND tipest = 'on'
				AND tiptpr != ''
				AND kadare = 'on'
				AND kadcon = 'on'
				AND kadsus != 'on'
				AND kadart = artcod
				AND CONCAT( tippro, tipcdo, tipnco) IN(
					SELECT
						CONCAT( tippro, tipcdo, tipnco)
					FROM
						{$wbasedato}_000002 e, {$wbasedato}_000001 f
					WHERE
						e.artcod = '$art'
						AND f.tippro = 'on'
						AND f.tipcdo = 'off'
						AND f.tipnco = 'off'
						AND e.arttip = tipcod
						AND e.artest = 'on'
				)
			GROUP BY
				kadart
			";
		
				
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	echo "<INPUT type='hidden' value='on' name='hiReemplazar'>";

	/******************************************************************************/
	
	consultarInfoTipoArticulos( $conex, $bd );
	
	// Enero 25 de 2012
	$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $servicio );
	
	$difAplicacion = 0;

	if( $disCco && $tempRonda < $disCco )
	{
		$tempRonda = $disCco;
		// $difAplicacion = intval((($tmpDispensacion-($tempRonda/3600))/2)*2);
	}
	
	// if( $disCco )
	// {
		// $tmpDispensacion = $disCco;
		// $difAplicacion = intval((($tmpDispensacion-($tempRonda/3600))/2)*2);
	// }
	
	// if( $rondaActual >= 10 ){
		// $rondaActual = $rondaActual.":00:00";
	// }
	// else{
		// $rondaActual = "0".$rondaActual.":00:00";
	// }

	
	//$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );			
	// $tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/2 )*2 ) + ( $difAplicacion );	

	$esPrimera = true;
	// if( $tempRonda >= 24 ){
		// if( $tempRonda-24 < 10 ){	
			// $tempRonda = "0".($tempRonda-24).":00:00";
		// }
		// else{
			// $tempRonda = ($tempRonda-24).":00:00";
		// }
		// $esPrimera = false;
	// }
	// else{
		// if( $tempRonda < 10 ){
			// $tempRonda = "0$tempRonda:00:00";
		// }
		// else{
			// $tempRonda = "$tempRonda:00:00";
		// }
	// }
	// $rondaActual = $tempRonda;
	$tempRonda = min( $tempRonda + floor( date( "H" )/2 )*2*3600, 42*3600 );
	
	$esPrimera = true;
	if( $tempRonda >= 24*3600 ){
		$esPrimera = false;
	}
		
	
	$rondaActual = gmdate( "H:i:s", $tempRonda );
	$tieneSaldo = false;

	$res2 =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numrows2 = mysql_num_rows( $res2 );
	$rows2 = mysql_fetch_array( $res2 );		

	$exp = explode( ",",$rows2['Kadcpx'] );

	if($rondaActual=="00:00:00")
		$rondaActual = "24:00:00";
	
	for( $i = 0; $i < count( $exp); $i++ ){
		
		list( $hor, $cdi, $dis ) = explode( "-", $exp[$i] );
		// echo $hor." - ".$cdi." - ".$dis." :::: ".$rondaActual." ::: dif: ".( $cdi - $dis )." :: esPrimera: ".$esPrimera."<br>";
		if( $cdi - $dis > 0 ){
			$tieneSaldo = true;
		}
		
		if( $hor == $rondaActual && $esPrimera ){
			break;
		}
		else if( $rondaActual == $hor ){
			$esPrimera = true;
		}
	}

	if( cantidadSinDispensarRondas( $rows[2], $rondaActual ) > 0 ){
			$tieneSaldo = true;
	}	
	
	/******************************************************************************/
	
	if( $tieneSaldo && $numrows > 0 ){
		
		echo "<table align='center' id='tbArticuloGenericos'>";
		
		echo "<tr class='titulo3'>";
		echo "<td colspan='4'>ARTICULOS A REEMPLAZAR EN EL PERFIL</td>";
		echo "</tr>";
		
		echo "<tr class='titulo3'>";
		echo "<td>Art&iacute;culo</td>";
		echo "<td colspan='2'>Fecha y hora de inicio</td>";
		echo "<td>Observaciones</td>";
//		echo "<td>Hora de inicio</td>";
		echo "</tr>";
		
		if( $numrows == 1 ){
			$rows = mysql_fetch_array( $res );
			
			echo "<tr class='texto1' align='center'>";
			
			echo "<td>";
			echo "<INPUT type='radio' name='rdArticulo' value='{$rows['id']}' checked> {$rows['kadart']} - {$rows['artcom']}";
			echo "</td>";
			
			echo "<td>";
			echo "{$rows['kadfin']}";
			echo "</td>";
				
			echo "<td>";
			echo "{$rows['kadhin']}";
			echo "</td>";
			
			echo "<td>";
			echo "<font size='3'>{$rows['kadobs']}</font>";
			echo "</td>";
				
			echo "</tr>";
			
		}
		else{
			
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
				
				echo "<tr class='texto1' align='center'>";
				
				echo "<td>";
				echo "<INPUT type='radio' name='rdArticulo' value='{$rows['id']}'> {$rows['kadart']}-{$rows['artcom']}";
				echo "</td>";
				
				echo "<td>";
				echo "{$rows['kadfin']}";
				echo "</td>";
				
				echo "<td>";
				echo "{$rows['kadhin']}";
				echo "</td>";
				
				echo "<td>";
				echo "<font size='4'>{$rows['kadobs']}</font>";
				echo "</td>";
				
				echo "</tr>";
			}
		}
		
		echo "</tr>";
		echo "</table>";
		echo "<br>";
	}
	else{
		echo "<INPUT type='hidden' value='on' name='hiNoReemplazo'>";
	}
	
	return $nka;
}

/**
 * Indica si un articulo de central de mezclas no necesita estar en el Kardex 
 * (Perfil farmacoterapeútico) para ser cargado al paciente
 * 
 * @param $art				Código del articulo
 * @return unknown_type
 * 
 * Nota: 	Se considera que que el artículo no necesita estar en el Kardex electrónico si el tipo de articulo
 * 			es de dilución y no es Material Médico Quirurgico (MMQ);
 */
function esNoKardex( $art ){
	
	global $conex;
    global $wbasedato;
	global $procesoContingencia;
	
	if( empty($procesoContingencia) || $procesoContingencia != 'on' ){
    
		$nka = false;

		//Buscando si el articulo es Material Medico Quirurgico
		//MMQ
		$sql = "SELECT
					tipnka
				FROM
					{$wbasedato}_000002, {$wbasedato}_000001
				WHERE
					artcod = '$art' 
					AND arttip = tipcod
					AND tipnka = 'on'
				";
					
		$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
		
		if( $row = mysql_fetch_array($res) ){
			$nka = true;			
		}
		else{
			$nka = false;
		}
	}
	else{
		$nka = true;
	}
	
	return $nka;
}

/**
 * Indica si un aritculo es Material Medico Quirurgico (MMQ)
 * 
 * @param $art
 * @return unknown_type
 */
function esMMQ( $art ){
	
	global $conex;
    global $wbasedato;
    
    $mmq = false;

    //Buscando si el articulo es Material Medico Quirurgico
    //MMQ
	$sql = "SELECT
				tipmat
			FROM
				{$wbasedato}_000002, {$wbasedato}_000001
			WHERE
				artcod = '$art' 
				AND arttip = tipcod";
				
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
		if( $row[0] == 'on' ){
			$mmq = true;			
		}
		else{
			$mmq = false;
		}
	}
	else{
		$mmq = false;
	}
	
	return $mmq;
}

/**
 * Inidica si un articulo es de Nutricion o no
 * 
 * @param $art				Codigo del articulo
 * @return bool
 */
function esNutricion( $art ){
	
	global $conex;
    global $wbasedato;
	
	$sql = "SELECT 
				tipdis
			FROM
				{$wbasedato}_000001
			WHERE
				tipcod = '02'";
				
	$sql = "SELECT 
				tipdis
			FROM
				{$wbasedato}_000001
			WHERE
				tippro = 'on'
				AND tipcdo = 'off'
				AND tipnco = 'off'
			";
	
	$res = mysql_query( $sql, $conex );
	
	for( ; $rows = mysql_fetch_array( $res ); ){
		if( substr($art,0,2) == trim( $rows[0] ) ){
			return true;			
		}
	}
	
	return false;
}

/**
 * Registra un articulo al paciente en el kardex electrónico
 * 
 * @param $art		Codigo del Aritculo del paciente
 * @param $his		Historia del paciente
 * @param $ing		Ingreso del paciente
 * @return bool		True si es verdaderos
 */
function registrarArticuloKE( $art, $his, $ing, &$idKardex, $num, $lin, $aplicado ){
	
	global $conex;
	global $bd;
	
	global $servicio;
	
	global $tmpDispensacion;
	global $tempRonda;
	
	//Busco los articulos posibles a los que se le puede cargar el articulo
	$sqlid="SELECT 
					id, kadcpx 
				FROM 
					{$bd}_000054 
				WHERE 
					kadart = '$art'
					
					AND kadfec = '".date("Y-m-d")."'
					AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadsus != 'on'
		       		AND kadcon = 'on'
		       		AND kadare = 'on'
				ORDER BY kadart
				";
	
	$resid = mysql_query( $sqlid, $conex )  or die(mysql_errno()." - en el query: ".$sqlid." - ".mysql_error());
	
	/************************************************************************************************************/
	consultarInfoTipoArticulos( $conex, $bd );
	
	/****************************************************************************************************
	 * Agosto 28 de 2012
	 ****************************************************************************************************/
	$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $servicio );
	
	$difAplicacion = 0;
	
	if( $disCco && $tempRonda < $disCco )
	{
		$tempRonda = $disCco;
		// $difAplicacion = intval((($tmpDispensacion-($tempRonda/3600))/2)*2);
	}
	/****************************************************************************************************/

	//$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );			
	// $tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/2 )*2 ) + ( $difAplicacion );
	$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );	//nuevo

	$esPrimera = true;
	if( $tempRonda >= 24 ){
		$esPrimera = false;
	}
	
	// if( $tempRonda >= 24 ){
			
		// if( $tempRonda-24 < 10 ){
			// $tempRonda = "0".($tempRonda-24).":00:00";
		// }
		// else{
			// $tempRonda = ($tempRonda-24).":00:00";
		// }
	// }
	// else{
		// if( $tempRonda < 10 ){
			// $tempRonda = "0$tempRonda:00:00";
		// }
		// else{
			// $tempRonda = "$tempRonda:00:00";
		// }
	// }
	$rondaActual = $tempRonda.":00:00";
	$rondaActual = gmdate( "H:i:s", $tempRonda*3600 );
	/************************************************************************************************************/
	
	$cancdi = 0;
	for( $i = 0;$row = mysql_fetch_array( $resid ); $i++ ){
			
		$exp = explode( ",",$row[1] );

		//echo "<br>.....i: ".$i;
		foreach( $exp as $key => $value ){

			$b = explode( "-", $value );

			if( $b[0] == $rondaActual && $b[1] - $b[2] > 0){
				$id = $row[0];
				$cancdi = $b[1] - $b[2]+cantidadSinDispensarRondas( $row[1], $rondaActual );
			}
		}
			
		//Si id es vacio significa que no pertenece a la ronda y por tanto va a grabar una cantida que no fue dispensada con anterioridad
		if( empty($id) && cantidadSinDispensarRondas( $row[1], $rondaActual ) > 0 ){
			$id = $row[0];
			$cancdi = cantidadSinDispensarRondas( $row[1], $rondaActual );
		}
	}

	$row[0] = $id;

	if( empty($row[0]) ){
		$row[0] = "";
	}
	
	
	
	$sql = "SELECT
				id, kadcdi-kaddis, kadcpx, kadcfr, kadcma
			FROM
			{$bd}_000054
			WHERE
				id = '{$row[0]}'";
					
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	$row = mysql_fetch_array( $res );


	if( empty( $row['kadcpx'] ) ){
		//echo "<br>......::<pre>"; var_dump( obtenerVectorAplicacionMedicamentos( date( "Y-m-d" ), date( "Y-m-d" ), "02:00:00", 4 ) ); echo"</pre>";
	}
	elseif( !empty($row['kadcpx']) ){
		//Obtengo las horas de aplicacion del medicamento para el paciente
		$exp = explode( ",", $row['kadcpx'] );
		$row[1] = ceil( $cancdi );
	}

	if( $cancdi <= 0 ){
		return false;
	}

	$nuevoAplicaciones = "";
	if( !empty($row['kadcpx']) ){

		$nuevoAplicaciones = crearAplicacionesCargadasPorHoras( $row['kadcpx'], 1 );
	}
	
	list( $kadRondaCpx, $kadFechaRondaCpx ) = explode( "|", consultarUltimaRondaDispensada( $nuevoAplicaciones ) );
	
	/********************************************************************************
	 * Noviembre 3 de 2011
	 * - Se guarda la fecha de ultima ronda dispensada
	 ********************************************************************************/
	if( !empty( $kadRondaCpx ) ){
		if( empty( $kadFechaRondaCpx ) ){
			$kadFechaRondaCpx = date( "Y-m-d", time()+24*3600 );
		}
		else{
			$kadFechaRondaCpx = date( "Y-m-d" );
		}
	}
	else{
		
		list( $kadRondaCpx ) = explode( ":", $rondaActual );
		
		if( date( "H" ) > $kadRondaCpx ){
			$kadFechaRondaCpx = date( "Y-m-d", time()+3600*24 );
		}
		else{
			$kadFechaRondaCpx = date( "Y-m-d" );
		}
	}
	/********************************************************************************/
	
	
	//Actualizando registro con el articulo cargado
	$sql = "UPDATE 
				{$bd}_000054 
	       	SET 
	       		kaddis = kaddis+1,
	       		kadhdi = '".date('H:i:s')."',
	       		kadcpx = '$nuevoAplicaciones',
				kadron = '$kadRondaCpx',
				kadfro = '$kadFechaRondaCpx'
	        WHERE 
	        	
	       		kadart = '$art' 
	       		AND kadhis = '$his'  
	       		AND kading = '$ing' 
	       		AND kadfec = '".date("Y-m-d")."'
	       		AND kadori = 'CM'
	       		AND kadcon = 'on'
	       		AND kadsus != 'on'
	       		AND kadare = 'on' 
	       		AND id = '{$row[0]}'";
		
	$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
	
	if( $res && mysql_affected_rows() > 0 ){
	
		/************************************************************************
		 * Abril 23 de 2012
		 ************************************************************************/
		if( !$aplicado ){ 
		
			$fecDispensacion = date("Y-m-d");
		
			global $contingencia;
			global $marcaContingencia;
			global $fhGrabacionContingencia;
			global $fhContingencia;
			global $procesoContingencia;
		
			if( !empty( $procesoContingencia ) && $procesoContingencia == 'on' ){
				actualizandoCargo( $conex, $bd, $num, $lin, $marcaContingencia );
			}
			elseif( !empty( $contingencia ) && $contingencia == 'on' ){
				
				if( time() > $fhGrabacionContingencia ){
					actualizandoCargo( $conex, $bd, $num, $lin, $marcaContingencia );
				}
			}
		}
		/************************************************************************/
	
		return true;
	}
	else
		return false;
}

/**
 * Condiciones para grabar en el KE
 * 
 * @param array $pac		informacion del paciente en el KE
 * @param array $art		informacion del articulo
 * @return 
 * 
 * Nota: Las condiciones son
 * - Tener KE (Si anteriormente tuvo un KE)
 * - Que la cantidad a dispensar (kadcdi) sea mayor a la cantidad dispensada (kaddis) 
 *   para los articulos, aunque hallan duplicados.
 * - Que tenga KE actualizado (Halla KE para el dia de hoy)
 * - Que el KE este confirmado (Que la doctora confirme el KE)
 * - Que el articulo a cargar este confirmado para CM
 */
function condicionesKE( &$pac, $art ){
	
	global $conex;
    global $bd;
	
	global $tmpDispensacion;
	global $tempRonda;
	
	global $servicio;
    
    $tiempoDispensacion = consultarTiempoDispensacionIncCM( $conex, '01' );
    
    $pac['sal'] = false;	//Indica si tiene saldo para poder dispensar
    $pac['art'] = false;	//Indica si el articulo existe para el paciente de ke
    $pac['ke'] = false;
	$pac['con'] = false;
	$pac['act'] = false;
	$pac['gra'] = false;
	
	esKE( $pac['his'], $pac['ing'], $pacKE);
	
	$pac['ke'] = $pacKE['ke'];
	$pac['con'] = $pacKE['con'];
	$pac['act'] = $pacKE['keact'];
	$pac['gra'] = $pacKE['kegra'];
	$pac['nut'] = esNutricion( $art );
	$pac['mmq'] = esMMQ( $art );
	$pac['nka'] = esNoKardex( $art );
	
	//Junio 28 de 2012 quito el group by lo dejo por order by
	//El articulo debe tener saldo antes de guardar
	$sql = "SELECT 
				1 as sal, kadart, kadcpx
			FROM 
				{$bd}_000054
			WHERE	
				kadhis = '{$pac['his']}' 
				AND kading = '{$pac['ing']}'
				AND kadart = '$art'
				AND kadfec = '".date("Y-m-d")."'
				AND kadcon = 'on'
				AND kadori = 'CM'
				AND kadsus != 'on'
				AND kadare = 'on'
			ORDER BY kadart";
	
	$res = mysql_query( $sql, $conex )  or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
	
	
	$rondaActual = ceil( ( date( "H", time() + $tiempoDispensacion*3600*0 )/$tiempoDispensacion ) )*$tiempoDispensacion;
	
	/****************************************************************************************************
	 * Agosto 28 de 2012
	 ****************************************************************************************************/
	$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $servicio );
	
	$difAplicacion = 0;
	
	consultarInfoTipoArticulos( $conex, $bd );

	if( $disCco && $tempRonda < $disCco )
	{
		$tempRonda = $disCco;
		// $difAplicacion = intval((($tmpDispensacion-($tempRonda/3600))/2)*2);
	}
	/****************************************************************************************************/

	$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );	//nuevo

	$esPrimera = true;
	if( $tempRonda >= 24 ){
		$esPrimera = false;
	}
	
	// if( $tempRonda >= 24 ){
		// if( $tempRonda-24 < 10 ){	
			// $tempRonda = "0".($tempRonda-24).":00:00";
		// }
		// else{
			// $tempRonda = ($tempRonda-24).":00:00";
		// }
		// $esPrimera = false;
	// }
	// else{
		// if( $tempRonda < 10 ){
			// $tempRonda = "0$tempRonda:00:00";
		// }
		// else{
			// $tempRonda = "$tempRonda:00:00";
		// }
	// }
	// $rondaActual = $tempRonda.":00:00";
	$rondaActual = gmdate( "H:i:s", $tempRonda*3600 );
	/************************************************************************************************************/
	
	$cancdi = 0;
	for( $i = 0 ; $rows = mysql_fetch_array( $res ); $i++ ){
		
		if( $rows['sal'] > 0 ){
			
			$exp = explode( ",",$rows[2] );

			for( $i = 0; $i < count( $exp); $i++ ){
				
				list( $hor, $cdi, $dis ) = explode( "-", $exp[$i] );
				
				if( $hor <= $rondaActual && $cdi - $dis > 0 ){
					
					$pac['sal'] = true;
					$pac['art'] = true;
					return;
				}
				
				if( $hor == $rondaActual && $esPrimera ){
					break;
				}
				else if( $rondaActual == $hor ){
					$esPrimera = true;
				}
			}
			
			if( cantidadSinDispensarRondas( $rows[2], $rondaActual ) > 0 ){
				$pac['sal'] = true;
				$pac['art'] = true;
				return;
			}
		}
		$pac['art'] = true;
	}
}

/**
 * Indica si el paciente se encuentra en Kardex Electronico o no
 * 
 * @param array $his	Paciente al que se le va a cargar los articulos
 * @param array $ing	Ingreso del paciente al que se le va a cargar los articulos
 * @return bool $ke		Devuelve true en caso de se Kardex electronico, en caso contrario false
 */

function esKE( $his, $ing, &$packe ){
	
	global $conex;
	global $bd;
	global $serv;
	
	$ke = 0;
	$pac = array();
	$pac['his'] = $his;
	$pac['ing'] = $ing;
	$pac['keact']=true;
	$pac['kegra']=true;
	$pac['con'] = false;
	$pac['ke'] = false;
	
	$sql = "SELECT 
				* 
			FROM 
				{$bd}_000053 a 
	        WHERE 
	        	karhis = '$his'
	        ";
	
	$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());;
	
	if( mysql_num_rows($res) > 0 ){
		
		//Tiene ke		
		$ke = 1;
		$pac['ke']=true;
	}
	
	//Busca kardex electronico para el paciente con la fecha mas reciente
	$sql = "SELECT
				Fecha_data, Kargra, Karcon, MAX(Karing) as Karing
			FROM
				{$bd}_000053 a
	        WHERE
	        	karhis = '$his'
	        	AND karcon = 'on'
	        GROUP BY a.Fecha_Data
	        ORDER BY a.Fecha_Data DESC";
	
	$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
	
	if( mysql_num_rows($res) > 0 ){
		
		//Tiene ke		
		$ke = 1;
		$pac['ke']=true;
		
		$rows = mysql_fetch_array( $res );

		if( $rows['Karing'] == $pac['ing'] ){
			if( $rows['Fecha_data'] == date("Y-m-d") ){
				
				//KE esta confirmado
				if( $rows['Karcon'] == "on" ){
					$pac['con'] = true;
				}
	
				//KE esta grabado
				if( $rows['Kargra'] == "on" ){
					$pac['kegra'] = true;
				}
			
				//Busca kardex electronico para el paciente con la fecha mas reciente
				$sql = "SELECT 
							Fecha_data, Kadfec
						FROM 
							{$bd}_000054 a 
				        WHERE 
				        	kadhis = '{$pac['his']}' AND
				        	kading = '{$pac['ing']}' AND
				        	kadfec = '{$rows['Fecha_data']}'  
				        GROUP BY a.Fecha_Data
				        ORDER BY a.Fecha_Data DESC"; 
				
				$result = mysql_query( $sql, $conex ) or die("Error en la consulta2");
		
				if( mysql_num_rows($result) > 0 ){
					$pac['keact']=true;
				}
			}
		}
	}	
	
	if( irAOrdenes( $conex, $bd, $serv['cod'] ) == 'on' && pacienteKardexOrdenes( $conex, $bd, $pac['his'], $pac['ing'], date("Y-m-d") ) ){
		$pac['con'] = true;
		$pac['keact']=true;
	}
	
	$packe = $pac;	
	return $ke;
}

/**
* Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts 
* existen dos opciones mandandole el paramentro tipo=C o para=A, asi ese Script realizara una u otra opcion
*/
function pintarTitulo()
{
    echo "<table ALIGN=CENTER width='50%'>"; 
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    echo "<tr><td class='titulo1'>CARGOS CENTRAL DE MEZCLAS</td></tr>";
    echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";
} 

function pintarConfi($cod, $var, $nom, $sub)
{
	global $wemp_pmla;
    echo "<form name='producto3' action='cargocpx.php' method=post>";
	echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'/>";

    echo "<table ALIGN=CENTER width='50%'>";
    echo "<tr><td class='titulo4'>SE HA REALIZADO EL MOVIMIENTO EXITOSAMENTE</td></tr>";
    echo "<tr><td class='titulo2'>ARTICULO: " . $cod . "-" . $nom . "</td></tr>";
    echo "<tr><td class='titulo2'>" . $sub . ": " . $var . "</td></tr>";
	
	$esDA = false;
	if(substr($cod,0,2)=="DA")
	{
		$esDA = true;
	}
	
	$esNPT = esNutricion( $cod );
	// if($esNPT)
	if($esNPT || $esDA)
	{
		global $historia;
		global $ingreso;
		global $pac;
		echo "<tr><td class='titulo2'>HISTORIA: " . $historia . "-" . $ingreso . "</td></tr>";
		echo "<tr><td class='titulo2'>PACIENTE: " . consultarNombrePaciente($historia,$ingreso). "</td></tr>";
		echo "<tr><td class='titulo2'>HABITACION: " . consultarHabitacion($historia,$ingreso) . "</td></tr>";
	}
} 

function pintarAlerta($mensaje)
{
	global $wemp_pmla;
    echo "<form name='producto3' action='cargocpx.php' method=post>";
	echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'/>";

    echo "<table ALIGN=CENTER width='50%'>";
    echo "<tr><td class='titulo5'>" . $mensaje . "</td></tr>";
} 

function pintarBoton()
{
	global $wemp_pmla;
	echo "<input type='hidden' name='wemp_pmla2' id='wemp_pmla2' value='".$wemp_pmla."'/>";
    echo "<tr><td >&nbsp;</td></tr>";
    echo "<tr><td ALIGN='CENTER' ><INPUT TYPE='button' NAME='ok' VALUE='ACEPTAR' onclick='enter();'></td></tr>";
    echo "</form>";
} 

function pintarPreparacion($preparacion, $escogidos, $carro)
{
	global $cod;
	global $conex;
	global $wemp_pmla;	
	$esNPT = esNutricion( $cod );
	if($esNPT)
	{
		$insumosPorDefecto = consultarAliasPorAplicacion($conex, $wemp_pmla, "insumosAdicionalesNPT");
	}
	
	
	$esDA = false;
	if(substr($cod,0,2)=="DA")
	{
		$esDA = true;
		$insumosPorDefecto = consultarAliasPorAplicacion($conex, $wemp_pmla, "insumosAdicionalesDA");
	}
	
	$insumoASeleccionar = explode(",",$insumosPorDefecto);	
	
	echo "<table align='center'>";
    echo "<tr><td colspan='6' class='titulo3' align='center'><b>INSUMOS DE PREPARACION ADICIONALES</b></td></tr>";
    echo "<td colspan='2' class='titulo3' align='center'><b>TIPO DE INSUMOS</b></td>";
    echo "<td colspan='4' class='titulo3' align='center'><b>PRESENTACION</b></td>";

    for ($i = 0; $i < count($preparacion); $i++)
    {
        $tam = count($preparacion[$i])-1;
        echo "<tr><td class='texto1' rowspan:'" . $tam . "' colspan='2' align='center'>" . $preparacion[$i]['nom'] . ": </td>";
        for ($j = 0;$j < $tam;$j++)
        {
			// Seleccionar insumos NPT por defecto
			$insumoChecked = "";
			// if($esNPT)
			if($esNPT || $esDA)
			{
				$codPreparacion = explode("-",$preparacion[$i][$j]);
				for($x=0;$x<count($insumoASeleccionar);$x++)
				{
					if($insumoASeleccionar[$x]==$codPreparacion[0])
					{
						$insumoChecked = "checked='checked'";
						break;
					}
				}
			}
						
			echo "<td class='texto1' colspan='1' align='left'><input type='checkbox' ".$insumoChecked." name='escogidos[" . $i . "][" . $j . "]' class='texto3' " . $escogidos[$i][$j] . "></td>";
			echo "<td class='texto1' colspan='3' align='left'>" . $preparacion[$i][$j] . " </td></tr><tr>";
			// --------------------------
			
            // echo "<td class='texto1' colspan='1' align='left'><input type='checkbox' name='escogidos[" . $i . "][" . $j . "]' class='texto3' " . $escogidos[$i][$j] . "></td>";
            // echo "<td class='texto1' colspan='3' align='left'>" . $preparacion[$i][$j] . " </td></tr><tr>";
			 if ($j + 1 != $tam)
            {
                echo "<td class='texto1' colspan='2' align='center'>&nbsp</td>";
            } 
            echo "<input type='hidden' name='preparacion[" . $i . "][" . $j . "]' value='" . $preparacion[$i][$j] . "'></td>";
        } 
    } 
    echo "</table>";
    echo "<input type='hidden' name='carro' value='".$carro."'></td>";
    echo "<input type='hidden' name='grabar' value='0'></td>";
    echo "</form>";
} 

function pintarInsumos($inslis, $presen, $cod, $cco, $historia, $ingreso, $var, $servicio)
{
	global $wemp_pmla;
	global $pac;
    echo "<form name='producto' action='cargocpx.php' method=post>";
	echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'/>";
    
    $packe = $pac;
    condicionesKE( $packe, $cod );
    $validacionReemplazo = saltarValidacionReemplazo( $servicio );
    
   $esNPT = esNutricion( $cod );
	if($esNPT && !$packe['sal'] )
	{
		//Reemplazar NPT sin importar el centro de costos
		articulosGenericosKE( $historia, $ingreso, $cod );
	}
	else
	{
		if( !esUrgenciasIncCM( $servicio ) ){	//Marzo 18 de 2013
			if( !( !$validacionReemplazo && $packe['nut'] ) ){
				
				if( $validacionReemplazo && !$packe['sal'] ){
					
					articulosGenericosKE( $historia, $ingreso, $cod );
				}
				elseif( !$packe['sal'] && !$packe['art'] ){
					
					echo "<INPUT type='hidden' value='on' name='hiReemplazar'>";
				}
			}
		}
	}	
	 
	 
	// if( !esUrgenciasIncCM( $servicio ) ){	//Marzo 18 de 2013
		// if( !( !$validacionReemplazo && $packe['nut'] ) ){
			
			// if( $validacionReemplazo && !$packe['sal'] ){
				
				// articulosGenericosKE( $historia, $ingreso, $cod );
			// }
	// //    	elseif( !$validacionReemplazo && !$packe['sal'] ){
	// //    		echo "<INPUT type='hidden' value='on' name='hiReemplazar'>";
	// //    	}
			// elseif( !$packe['sal'] && !$packe['art'] ){
				
				// echo "<INPUT type='hidden' value='on' name='hiReemplazar'>";
			// }
		// }
	// }
        
    echo "<table align='center'>";
    echo "<tr><td colspan='8' class='titulo3' align='center'><b>INSUMOS DEL PRODUCTO</b></td></tr>";
    echo "<td colspan='1' class='titulo3' align='center'><b>INSUMO</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>CANTIDAD</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>AJUSTE</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>FALTANTE</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>PRESENTACION</b></td>";
    echo "<td colspan='1' class='titulo4' align='center'><b>CAN. CARGO</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>AJUSTE</b></td>";
    echo "<td colspan='1' class='titulo3' align='center'><b>CAN. AJUSTE</b></td>";

    for ($i = 0; $i < count($inslis); $i++)
    {
        $tam = count($presen[$i]); 
        // echo $tam;
        echo "<tr><td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['cod'] . "-" . $inslis[$i]['nom'] . " (" . $inslis[$i]['pre'] . " ) </td>";
        echo "<td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['can'] . " </td>";
        echo "<td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['aju'] . " </td>";
        echo "<td class='texto1' rowspan:'" . $tam . "' colspan='1' align='center'>" . $inslis[$i]['fal'] . " </td>";

        echo "<input type='hidden' name='inslis[" . $i . "][cod]' value='" . $inslis[$i]['cod'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][nom]' value='" . $inslis[$i]['nom'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][pre]' value='" . $inslis[$i]['pre'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][can]' value='" . $inslis[$i]['can'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][aju]' value='" . $inslis[$i]['aju'] . "'></td>";
        echo "<input type='hidden' name='inslis[" . $i . "][fal]' value='" . $inslis[$i]['fal'] . "'></td>";
        for ($j = 0;$j < $tam;$j++)
        {
            if ($tam == 1)
            {
                $faltante = (float)$inslis[$i]['can'] - (float)$presen[$i][$j]['caj'];
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['nom'] . " </td>";
                echo "<td class='texto7' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][can]' class='texto3' value='" . $faltante . "' size='5'></td>";
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['aju'] . "</td>";
                echo "<td class='texto1' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][caj]' class='texto3' value='" . $presen[$i][$j]['caj'] . "' size='5'></td></tr><tr>";
            } 
            else
            {
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['nom'] . " </td>";
                echo "<td class='texto7' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][can]' class='texto3' value='" . $presen[$i][$j]['can'] . "' size='5'></td>";
                echo "<td class='texto1' colspan='1' align='center'>" . $presen[$i][$j]['aju'] . "</td>";
                echo "<td class='texto1' colspan='1' align='center'><input type='text' name='presen[" . $i . "][" . $j . "][caj]' class='texto3' value='" . $presen[$i][$j]['caj'] . "' size='5'></td></tr><tr>";
            } 
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][cod]' value='" . $presen[$i][$j]['cod'] . "'></td>";
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][nom]' value='" . $presen[$i][$j]['nom'] . "'></td>";
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][cnv]' value='" . $presen[$i][$j]['cnv'] . "'></td>";
            echo "<input type='hidden' name='presen[" . $i . "][" . $j . "][aju]' value='" . $presen[$i][$j]['aju'] . "'></td>";

            if ($j + 1 != $tam)
            {
                echo "<td class='texto1' colspan='2' align='center'>&nbsp</td>";
                echo "<td class='texto1' colspan='2' align='center'>&nbsp</td>";
            } 
        } 
    } 
    echo "<tr><td colspan=8 class='titulo3' align='center'><INPUT TYPE='submit' NAME='GRABAR' VALUE='GRABAR' ></td></tr>";
    echo "<input type='hidden' name='cod' value='" . $cod . "'></td>";
    echo "<input type='hidden' name='cco' value='" . $cco . "'></td>";
    echo "<input type='hidden' name='historia' value='" . $historia . "'></td>";
    echo "<input type='hidden' name='ingreso' value='" . $ingreso . "'></td>";
    echo "<input type='hidden' name='var' value='" . $var . "'></td>";
    echo "<input type='hidden' name='servicio' value='" . $servicio . "'></td>";
    echo "</table></br>";
} 
/**
* conusltamos los detalles del producto, sobretodo los insumos que lo componenen ($inslis)
* 
* Pide:
* 
* @param caracter $codigo , codigo del producto
* 
* Retorna:
* @param caracter $via , via de infusion
* @param caracter $tfd , tiempo de infusion en horas
* @param caracter $tfh , tienmpo de infusion en minutos
* @param caracter $tvd , tiempo de vencimiento en hras
* @param caracter $tvh , tiempo de vencimiento en minutos
* @param date $fecha ,  fecha de creacion
* @param vector $inslis , lista de insumos que lo componen
* @param caracter $tippro , tipo de porducto (codigo del tipo-descripcion-codificado o no)
* @param boolean $foto si es fotosensible
* @param boolean $neve si debe conservarse en nevera
*/
function consultarInsumos($codigo, &$inslis)
{
    global $conex;
    global $wbasedato;
	global $bd;

    $q = " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides "
     . "       FROM " . $wbasedato . "_000003, " . $wbasedato . "_000002, ".$bd."_000027 "
     . "    WHERE  Pdepro = '" . $codigo . "' "
     . "       AND Pdeest = 'on' "
     . "       AND Pdeins= Artcod "
     . "       AND Artuni= Unicod "
     . "       AND Uniest='on' "
     . "    Order by 1 ";

    $res = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);
            $inslis[$i]['cod'] = $row[0];
            $inslis[$i]['nom'] = str_replace('-', ' ', $row[2]);
            $inslis[$i]['pre'] = $row[4] . '-' . $row[5];
            $inslis[$i]['can'] = $row[1];
        } 
    } 
} 

function consultarPresentaciones(&$insumo, $cco, $historia, $ingreso)
{
    global $conex;
    global $wbasedato;
	global $bd;

    $q = " SELECT Apppre, Artcom, Appcnv "
     . "        FROM  " . $wbasedato . "_000009, ".$bd."_000026 "
     . "      WHERE  Appcod='" . $insumo['cod'] . "' "
     . "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "            and Appest='on' "
     . "            and Apppre=Artcod ";

    $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num1 = mysql_num_rows($res1);
    $insumo['aju'] = 0;
    $cuenta = $insumo['can'];
    if ($num1 > 0)
    {
        for ($i = 0;$i < $num1;$i++)
        {
            $row1 = mysql_fetch_array($res1);
            $presentacion[$i]['cod'] = $row1['Apppre'];
            $presentacion[$i]['nom'] = $row1['Artcom'];
            $presentacion[$i]['cnv'] = $row1['Appcnv']; 
            // consulto el ajuste que hay para la presentación
            $q = " SELECT Ajpart, Ajpcan, Ajpfve, Ajphve, Artcom, Artgen "
             . "        FROM " . $wbasedato . "_000010, " . $wbasedato . "_000009, ".$bd."_000026 "
             . "      WHERE Ajphis= '" . $historia . "' "
             . "            and Ajpest ='on' "
             . "            and Ajping = '" . $ingreso . "' "
             . "            and Ajpcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
             . "  		  and Ajpart = Apppre "
             . "            and Apppre = '" . $row1['Apppre'] . "' "
             . "            and Appest = 'on' "
             . "            and Artcod = Ajpart "
             . "           order by  Ajpfve desc";

            $res2 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num2 = mysql_num_rows($res1);
            if ($num2 > 0)
            {
                $row2 = mysql_fetch_array($res2);
                if ($row2['Ajpfve'] > date('Y-m-d'))
                {
                    $presentacion[$i]['aju'] = $row2['Ajpcan'];
                    $presentacion[$i]['can'] = '';
                    $insumo['aju'] = $insumo['aju'] + $row2['Ajpcan'];

                    if ($cuenta > 0 and $row2['Ajpcan'] > 0)
                    {
                        if ($cuenta > $row2['Ajpcan'])
                        {
                            $presentacion[$i]['caj'] = $row2['Ajpcan'];
                            $cuenta = $cuenta - $presentacion[$i]['caj'];
                        } 
                        else if ($cuenta <= $row2['Ajpcan'])
                        {
                            $presentacion[$i]['caj'] = $cuenta;
                            $cuenta = 0;
                        } 
                    } 
                    else
                    {
                        $presentacion[$i]['caj'] = '';
                    } 
                } 
                else
                {
                    $presentacion[$i]['aju'] = 0;
                    $presentacion[$i]['can'] = '';
                    $presentacion[$i]['caj'] = '';
                } 
            } 
            else
            {
                $presentacion[$i]['aju'] = 0;
                $presentacion[$i]['can'] = '';
                $presentacion[$i]['caj'] = '';
            } 
        } 
    } 
    return @$presentacion;
} 

/**
* Se consultan aquellos insumos tipo material quirurgico en el maestro de tipo de articulos
* ya que estos pueden ser cargados al paciente sin necesidad de pertenecer al lote
* 
* @param unknown_type $escogidos , se incializan todos los insumos como checkbox vacio
* @return unknown $preparacion, lista de insumos
*/
function consultarPreparacion(&$escogidos)
{
    global $conex;
    global $wbasedato;
	global $bd;

    $q = " SELECT Tipcod, Tipdes "
     . "        FROM " . $wbasedato . "_000001 "
     . "      WHERE Tipmmq= 'on' "
     . "            and Tipest ='on' ";

    $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num1 = mysql_num_rows($res1);
    for ($i = 0; $i < $num1; $i++)
    {
        $row = mysql_fetch_array($res1);
        $preparacion [$i]['nom'] = $row[1]; 
        // consulto los conceptos
        $q = " SELECT Apppre, C.Artcom, Appuni, Appcod"
         . "        FROM " . $wbasedato . "_000002 A, " . $wbasedato . "_000009 B, ".$bd."_000026 C "
         . "      WHERE A.Arttip = '" . $row[0] . "' "
         . "        AND A.Artcod = B.Appcod "
         . "        AND A.Artest='on' "
         . "        AND B.Appuni>0 "
         . "        AND B.Apppre=C.Artcod ";

        $res2 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num2 = mysql_num_rows($res2);

        for ($j = 0;$j < $num2;$j++)
        {
            $row2 = mysql_fetch_array($res2);
            $preparacion[$i][$j] = $row2[0] . '-' . $row2[1] . '-' . $row2[2] . '-' . $row2[3];
            if (!isset($escogidos[$i][$j]))
            {
                $escogidos[$i][$j] = '';
            } 
            else
            {
                $escogidos[$i][$j] = 'checked';
            } 
        } 
    } 

    return $preparacion;
} 

/**
* Se valida que haya existencia de lo que se va a cargar, si es un producto, que haya cantidad en el lote
* si es un insumo, que haya existencia de la presentacion
* 
* @param vector $insumo caracteriticas del producto o insumo que se va a cargar
* @param caracter $cco centro de costos (codig-descripcion)
* @param caracter $otro lote para producto, presentacion para insumo
* @param caracter $tipo si es cargo o averia
* @return numerico $val  segun el error en la validacion retorna un numero
*/
function validarMatrix($cod, $cco, $otro, $tip, &$mensaje)
{
    global $conex;
    global $wbasedato;

    $q = " SELECT karexi FROM " . $wbasedato . "_000005 where karcod='" . $cod . "' and Karcco= mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num1 = mysql_num_rows($res1);

    if ($num1 > 0)
    {
        $row1 = mysql_fetch_array($res1);
        if ($row1[0] > 0)
        {
            if ($tip == 'on')
            {
                $q = " SELECT Plosal, Plofve, plohve "
                 . "       FROM " . $wbasedato . "_000004 "
                 . "    WHERE Plopro = '" . $cod . "' "
                 . "       AND Plocod='" . $otro . "' "
                 . "       AND Ploest='on' "
                 . "       AND Plocco= mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
            } 
            else
            {
                $q = " SELECT Appexi "
                 . "        FROM  " . $wbasedato . "_000009 "
                 . "      WHERE Appcod='" . $cod . "' "
                 . "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
                 . "            and Appest='on' "
                 . "            and Apppre=mid('" . $otro . "',1,instr('" . $otro . "','-')-1)";
            } 

            $res2 = mysql_query($q, $conex);
            $num2 = mysql_num_rows($res1);
            if ($num2 > 0)
            {
                $row2 = mysql_fetch_array($res2);
                if ($row2[0] <= 0)
                {
                    $mensaje = 'SIN EXISTENCIAS ESPECIFICAS';
                    return false;
                } 
                else if ($tip == 'on' and $row2[1] < date('Y-m-d'))
                {
                    $mensaje = 'PRODUCTO VENCIDO';
                    return false;
                } 
                else
                {
                    return true;
                } 
            } 
            else
            {
                $mensaje = 'SIN EXISTENCIAS ESPECIFICAS';
                return false;
            } 
        } 
        else
        {
            $mensaje = 'SIN EXISTENCIAS EN EL KARDEX DE INVENTARIOS';
            return false;
        } 
    } 
    else
    {
        $mensaje = 'SIN EXISTENCIAS EN EL KARDEX DE INVENTARIOS';
        return false;
    } 
} 

/**
* Se graba el encabezado del movmiento
* 
* Retorna
* 
* @param caracter $codigo concepto del movmiento
* @param caracter $consecutivo numero del movmiento
* Pide
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $usuario codigo de usuario que realiza la operacion
* @param caracter $cco2 si es averia el centro de costos al que va, si es cargo historia-ingreso
* @param caracter $tipo C cargo A o Averia, segun eso se busca en concepto para el movmiento
*/
function grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $usuario, $cco2, $anexo)
{
    global $conex;
    global $wbasedato;
	
	$cco2 = str_replace( ' ', '', $cco2 );

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE"; 
    // $errlock = mysql_query($q,$conex);
    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '-1' "
     . "      AND Concar = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1'"
     . "      AND Concar = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , '" . $cco2 . "' ,       '" . $anexo . "', '" . $usuario . "',      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
} 

/**
* se graba un encabezado de entrada a matrix, cuando es f es un concepto que refleja la entrada de cargos a unix
* pero que realmente no mueve inventarios
* 
* Devuelve
* 
* @param caracter $codigo concepto de inventario
* @param caracter $consecutivo numero del documento
* 
* Pide
* @param caracter $cco centro de costos de origen
* @param caracter $cco2 centro de coostos destino para averias y historia-ingreso para cargos
* @param caracter $usuario usuario que graba
* @param caracter $tipo A averia o C cargos a paciente
*/
function grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $cco, $cco2, $usuario)
{
    global $conex;
    global $wbasedato;
	
	$cco2 = str_replace( ' ', '', $cco2 );

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE";
    $errlock = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());

    $anexo = $codigo . '-' . $consecutivo;
    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '1' "
     . "      AND Conane = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '1'"
     . "      AND Conane = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error()) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', '" . $cco . "' , '" . $cco2 . "' ,      '" . $anexo . "' , '" . $usuario . "',      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE ENTRADA DEL ARTICULO " . mysql_error());
} 

/**
* Grabamos el movimiento de salida de matrix
* 
* @param caracter $inscod codigo del insumo o producto
* @param caracter $codigo concepto del movmiento
* @param caracter $consecutivo numero del movimeinto
* @param caracter $usuario codigo del usuario que graba
* @param caracter $prese presentacion que se graba
* @param caracter $lote lote que se descuenta
* @param caracter $ajupre la presentacion si haya ajuste de presentacion
* @param caracter $ajucan en que cantidad el ajuste
* @param numerico $cantidad cantidad que se descarta
*/
function grabarDetalleSalidaMatrix($inscod, $codigo, $consecutivo, $usuario, $prese, $lote, $ajupre, $ajucan, $cantidad, $total)
{
    global $conex;
    global $wbasedato;

    if( empty($ajucan) ) {
    	$ajucan = 0;
    }
    $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico        ,   Fecha_data            ,     Hora_data                  ,   Mdecon         ,      Mdedoc            ,     Mdeart     ,             Mdecan  ,      Mdefve  ,  Mdenlo        , Mdepre            ,  Mdepaj          , Mdecaj          ,  Mdecto        , Mdeest ,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $inscod . "', '" . $cantidad . "' , '0000-00-00' , '" . $lote . "',   '" . $prese . "', '" . $ajupre . "','" . $ajucan . "','" . $total . "',  'on'  , 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO " . mysql_error());
} 

/**
* Elimina un articulo determinado del inventario
* 
* @param caracter $inscod codigo del articulo
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $lote es vacio si no tiene lote
* @param caracter $dato numero del lote si tienen lote o presentacion si es un insumo
*/
function descontarArticuloMatrix($inscod, $cco, $lote, $dato)
{
    global $conex;
    global $wbasedato;

    global $conex;
    global $wbasedato;

    if ($lote != '')
    {
        $q = "   UPDATE " . $wbasedato . "_000005 "
         . "      SET karexi = karexi - 1 "
         . "    WHERE Karcod = '" . $inscod . "' "
         . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR EL ARTICULO " . mysql_error());

        $q = "   UPDATE " . $wbasedato . "_000004 "
         . "      SET Plosal = Plosal-1 "
         . "    WHERE Plocod =  '" . $dato . "' "
         . "      AND Plopro ='" . $inscod . "' "
         . "      AND Ploest ='on' "
         . "      AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    } 
    else
    {
        $q = " SELECT Appcnv "
         . "      FROM " . $wbasedato . "_000009 "
         . "    WHERE Appcod =  '" . $inscod . "' "
         . "      AND Apppre='" . $dato . "' "
         . "      AND Appest ='on' "
         . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex);
        $row2 = mysql_fetch_array($res1);

        $q = "   UPDATE " . $wbasedato . "_000005 "
         . "      SET karexi = karexi - (1*" . $row2[0] . ") "
         . "    WHERE Karcod = '" . $inscod . "' "
         . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR EL ARTICULO1 " . mysql_error());

        $q = "   UPDATE " . $wbasedato . "_000009 "
         . "      SET Appexi = Appexi- 1*Appcnv "
         . "    WHERE Appcod =  '" . $inscod . "' "
         . "      AND Apppre='" . $dato . "' "
         . "      AND Appest ='on' "
         . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    } 

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());
} 

/**
* Se actualiza el ajuste que hay para un insumo
* 
* @param numerico $faltante ,  cantidad que falta de lo que se necsita de la dosis
* @param caracter $presentacion2 presentacion a cargar
* @param caracter $presentacion1 presentacion que habia antes en saldo
* @param numerico $cantidad cantidad para el producto
* @param caracter $historia numero de historia
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $usuario codigo del usuario que graba
* @param caracter $ingreso numero del ingreso
*/
function actualizarAjuste($presentacion, $ajuste, $cantidad, $historia, $cco, $usuario, $ingreso, $cnv, $total)
{
    global $conex;
    global $wbasedato;

    if ($ajuste > 0)
    {
        $q = "   UPDATE " . $wbasedato . "_000010 "
         . "      SET Ajpcan = (Ajpcan - " . $ajuste . ") "
         . "    WHERE Ajphis= '" . $historia . "' "
         . "      AND Ajping= '" . $ingreso . "' "
         . "      AND Ajpart= '" . $presentacion . "' "
         . "      AND Ajpcco= '" . $cco . "' "
         . "      AND Ajpest = 'on' ";

        $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
    } 

    if ($cantidad > 0)
    {
        $saldo = $cantidad * (float)$cnv + (float)$ajuste - (float)$total;

        $q = " SELECT Arttve "
         . "        FROM  " . $wbasedato . "_000009, " . $wbasedato . "_000002 "
         . "      WHERE Apppre='" . $presentacion . "' "
         . "            and Appcco='" . $cco . "' "
         . "            and Appest='on' "
         . "            and Appcod=Artcod ";

        $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row1 = mysql_fetch_array($res1);
        $tiempo = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + ($row1[0] * 24 * 60 * 60);
        $tiempo = date('Y-m-d', $tiempo);

        if ($saldo > 0)
        {
            $q = " SELECT * "
             . "        FROM " . $wbasedato . "_000010 "
             . "      WHERE Ajphis= '" . $historia . "' "
             . "            and Ajpest ='on' "
             . "            and Ajping = '" . $ingreso . "' "
             . "            and Ajpcco = '" . $cco . "' "
             . "  		  and Ajpart ='" . $presentacion . "'";

            $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num1 = mysql_num_rows($res1);
            if ($num1 > 0)
            {
                $q = "   UPDATE " . $wbasedato . "_000010 "
                 . "      SET Ajpcan = " . $saldo . ", "
                 . "          Ajpfve = '" . $tiempo . "' "
                 . "    WHERE Ajphis= '" . $historia . "' "
                 . "      AND Ajping= '" . $ingreso . "' "
                 . "      AND Ajpart= '" . $presentacion . "'"
                 . "      AND Ajpcco= '" . $cco . "' "
                 . "      AND Ajpest = 'on' ";
            } 
            else
            {
                $q = " INSERT INTO " . $wbasedato . "_000010 (   Medico       ,           Fecha_data,                  Hora_data,          Ajphis,          Ajping ,     Ajpcco,   Ajpfec  ,       Ajphor,     Ajpfve,        Ajphve ,                    Ajpart    ,       Ajpcan,  Ajpest, Seguridad) "
                 . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $historia . "', '" . $ingreso . "' , '" . $cco . "', '" . date('Y-m-d') . "' ,   '" . (string)date("H:i:s") . "',  '" . $tiempo . "' , '" . (string)date("H:i:s") . "',   '" . $presentacion . "', '" . $saldo . "', 'on', 'C-" . $usuario . "') ";
            } 

            $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ACTUALIZAR EL AJUSTE DE PRESENTACION " . mysql_error());
        } 
        // echo $saldo;
    } 
} 

/**
* ****************************************************PROGRAMA*************************************************************************
*/

session_start();

//if (!isset($user))
//{
//    if (!isset($_SESSION['user']))
//        session_register("user");
//} 
//
//if (!isset($_SESSION['user']))
//    echo "error";
    
if( isset($user) && $user == '' )
    echo "error";
else
{
	$aplicaron = false;
    //$wbasedato = 'cenpro';
    $wemp_pmla = $_REQUEST['wemp_pmla'];
    include_once( "cenpro/cargos.inc.php" );	//incluye las funciones para ciclos de produccion
    include_once( "conex.php" );
    
//    $conex = mysql_connect('localhost', 'root', '')
//    or die("No se ralizo Conexion");
     include_once("root/comun.php");
    


    pintarTitulo(); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
	
	/******************************************************************
	 * Abril 23 de 2012
	 ******************************************************************/
	contingencia( $conex );
	/******************************************************************/
	
    //$bd = 'movhos'; 
	
    // invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
    // este programa no debe usarse
    // include_once("pda/tablas.php");
    include_once("movhos/fxValidacionArticulo.php");
    include_once("movhos/registro_tablas.php");
    include_once("movhos/otros.php");
    include_once("cenpro/funciones.php");
    include_once("cenpro/carro.php");
	//include_once("movhos/cargosSF.inc.php");
	include_once("ips/funciones_facturacionERP.php");
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	$bd = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$bdCencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
	
	//$test = centroCostosCM(); echo $test;
	$tmpDispensacion = consultarTiempoDispensacionIncCM( $conex, $wemp_pmla );
	
    connectOdbc($conex_o, 'facturacion');
	
	/**********************************************************************
	 * Agosto 14 de 2013
	 **********************************************************************/
	if( !consultarConexionUnix() ){
		$conex_o = 0;
	}
	/**********************************************************************/

    if ( true ||  $conex_o != 0 )
    {
        $tipTrans = 'C'; //segun ana es una transaccion de cargo
        $aprov = true; //siempre es por aprovechamiento;
        $exp = explode('-', $cco);
        $centro['cod'] = $exp[0];
        $centro['neg'] = false;
        getCco($centro, $tipTrans, '01');
        $pac['his'] = trim( $historia );
        $pac['ing'] = trim( $ingreso );
        $cns = 0;
        $date = date('Y-m-d');
        $art['ini'] = $cod;
        $art['ubi'] = 'US';
        $serv['cod'] = $servicio;
        $art['ser'] = $servicio;
        getCco($serv, $tipTrans, '01');
        
        $hab = $nom = '';
        
        consultarNombreHabitacion( $pac['his'], $pac['ing'], $hab, $nom );
		
		if( irAOrdenes( $conex, $bd, $serv['cod'] ) == 'on' && pacienteKardexOrdenes( $conex, $bd, $pac['his'], $pac['ing'], $date ) ){
			$serv['apl'] = false;
		}
        
        if(!$serv['apl'])
		{
			$art['dis'] = $carro;
		}
		else 
		{
			$art['dis'] = 'off';
		}
		agregarAlCarro( $art, $serv, 'Cargo', $centro );
        // $ronApl = date("G:i - A"); 
		$ronApl=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );
        
		// consulto los datos del usuario de la sesion
        $pos = strpos($user, "-");
        $wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario        
        $usu = $wusuario; 
        // consulto los centros de costos que se administran con esta aplicacion
        // estos se cargan en un select llamado ccos.
        // consultamos si el producto es codificado o no
        $q = "SELECT Artcom, Tipcdo, Tippro, Arttnc "
         . "     FROM   " . $wbasedato . "_000002, ".$bd."_000027, ".$wbasedato."_000001 "
         . "   WHERE Artcod='" . $cod . "' "
         . "     AND Unicod = Artuni "
         . "     AND Tipcod = Arttip "
         . "     AND Tipest = 'on' ";

        $res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num1 = mysql_num_rows($res1);
        $row1 = mysql_fetch_array($res1);
        
		if( $conex_o != 0 ){
			
			/******************************************************************************
			 * Con conexión con Unix
			 ******************************************************************************/
			
			switch ($row1[2])
			{
				case 'on':

					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR EL LOTE QUE VA A CARGAR');
						pintarBoton();
					} 
					else
					{
						$art['lot'] = $var;
						$val = validarMatrix($cod, $cco, $var, 'on', $mensaje);
						if (!$val)
						{
							pintarAlerta($mensaje);
							pintarBoton();
						} 
						else
						{
							if ($row1[1] == 'on' && $row1[3] != 'on' )	//Producto codificado
							{//1
								$art['cod'] = $cod;
								$art['neg'] = false;
								$art['can'] = 1;

								$res = ArticuloExiste ($art, $error);
								if ($res)
								{//2
									$res = TarifaSaldo($art, $centro, $tipTrans, $aprov, $error);
									if ($res)
									{//3 
										//Busco la información del paciente con Kardex Electronico
	//                                	esKE( $pac['his'], $pac['ing'], $packe );
										$packe = $pac;
										condicionesKE( $packe, $cod );
										if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['gra'] ) || $packe['mmq'] )
										{
											if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['con'] ) || $packe['mmq'] )
											{
												if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['act'] ) || $packe['mmq'] )
												{
													if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['art'] ) || $packe['mmq'] )
													{
														if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['sal'] > 0 ) || $packe['mmq'] )
														{
															if( true || !isset( $hiReemplazar ) || ( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo)  ) ){
																
																$val = false;
																// grabo el encabezado del movimiento
																$dronum = '';			                      
																Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
																grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
																$numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
																$dato = $var . "-" . $cod;
																grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', 1, 1);
																descontarArticuloMatrix($cod, $cco, 'on', $var);
																grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $exp[0], $historia . '-' . $ingreso, $wusuario);
																grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', $art['can'], $art['can']);
																$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, $error);
																if (!$res)
																{
																	pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
																	$art['ubi'] = 'M';
																} 
																registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error);
							
																if (!$centro['apl'] and $serv['apl'])
																{
																	$centro['apl'] = true;
																} 
							
																if (!$centro['apl'] and !$serv['apl'])
																{
																	$val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																} 
																else
																{
																	$val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																	$trans['num'] = $dronum;
																	if ($drolin == 1)
																	{
																		$trans['lin'] = 1;
																	} 
																	else
																	{
																		$trans['lin'] = '';
																	} 
																	$val = registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
																	$ccoCM = centroCostosCM();
																	actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $centro, $art, $dronum, $drolin, $ccoCM );	//Noviembre 8 de 2011
																	$aplicaron = true;
																} 
																
																//Reemplazando el articulo
	//						                                    if( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo) ){
	//						                                    	reemplazarMedicamentoKardex( @$rdArticulo, $art['cod'] );						                                    	
	//						                                    }
																
																if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] ){
																	registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], $idKardex, $dronum, $drolin, $aplicaron );
																	
																	/****************************************************************
																	 * Mayo 11 de 2011 
																	 ****************************************************************/
																	if( $aplicaron ){
																		actualizandoAplicacion( $idKardex, $centro['cod'], $art['cod'], $dronum, $drolin );
																	}
																	/****************************************************************/
																}
																
																pintarConfi($cod, $var, $row1[0], 'LOTE');
																pintarBoton();
							
																?>
																<script>
																	//	window . opener . producto . submit();
																	//	window . close();
																 </script>
																<?php
															}
															else{
																pintarAlerta('DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR');
																pintarBoton();
															}
														}
														else{
															pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE');
															pintarBoton();
														}
													}
													else{
														pintarAlerta('EL ARTICULO NO HA SIDO CARGADO AL PACIENTE');
														pintarBoton();
													}
												}
												else{
													pintarAlerta('NO TIENE KARDEX ELECTRONICO ACTUALIZADO');
													pintarBoton();
												}
											}
											else{
												pintarAlerta('EL KARDEX ELECTRONICO NO SE HA CONFIRMADO');
												pintarBoton();
											}
										}
										else{
											pintarAlerta('EL KARDEX ELECTRONICO NO SE HA GRABADO');
											pintarBoton();
										}
									}//fin3 
									else
									{
										pintarAlerta('EL ARTICULO A CARGAR NO TIENE TARIFA EN UNIX');
										pintarBoton();
									} 
								}//fin2 
								else
								{
									pintarAlerta('EL ARTICULO A CARGAR NO EXISTE EN UNIX');
									pintarBoton();
								} 
							}//fin1 
							else
							{	//Producto NO CODIFICADO
								if (!isset($grabar))
								{ 
									// consulto la lista de insumos que componen el producto
									consultarInsumos($cod, $inslis); 
									// para cada insumo consultamos las presentaciones y su ajuste
									for ($i = 0; $i < count($inslis); $i++)
									{
										$presen[$i] = consultarPresentaciones($inslis[$i], $cco, $historia, $ingreso);
										$inslis[$i]['fal'] = $inslis[$i]['can'] - $inslis[$i]['aju'];
										if ($inslis[$i]['fal'] < 0)
										{
											$inslis[$i]['fal'] = 0;
										} 
									} 
									pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio); 
									// aca voy a investigar la lista de insumos que se pueden cargar con el producto
									$preparacion = consultarPreparacion($escogidos);
									pintarPreparacion($preparacion, $escogidos, $carro);
								} 
								else
								{
									$val = true;
									$mensaje = ''; 
									// validamos que la suma utilizada por las fracciones sea igual al faltante
									for ($i = 0; $i < count($inslis); $i++)
									{
										$faltante = 0;
										$cantidad = 0;
										for ($j = 0; $j < count($presen[$i]); $j++)
										{
											if ($presen[$i][$j]['caj'] != '' and $presen[$i][$j]['caj'] > $presen[$i][$j]['aju'])
											{
												$mensaje = $mensaje . ' Las cantidades ingresadas para el ajuste de ' . $presen[$i][$j]['cod'] . ' son mayores al saldo de ajuste.';
												$val = false;
											} 
											if ($presen[$i][$j]['can'] != '')
											{
												$faltante = $faltante + $presen[$i][$j]['can'];
											} 

											if ($presen[$i][$j]['aju'] > 0 and $presen[$i][$j]['can'] > 0 and $presen[$i][$j]['caj'] != $presen[$i][$j]['caj'])
											{
												$mensaje = $mensaje . 'Antes de agregar cantidad de la presentación ' . $presen[$i][$j]['cod'] . ' debe consumir el saldo de ajuste.';
												$val = false;
											} 

											$cantidad = $cantidad + (float)$presen[$i][$j]['can'] + (float)$presen[$i][$j]['caj'];
										} 

										if (round($faltante, 4) < round($inslis[$i]['fal'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades a cargar del insumo ' . $inslis[$i]['cod'] . ' son inferiores al faltante.';
											$val = false;
										} 

										if (round($cantidad, 4) < round($inslis[$i]['can'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades ingresadas del insumo ' . $inslis[$i]['cod'] . ' son inferiores a la cantidad en el Producto.';
											$val = false;
										} 

										if (round($faltante, 4) > round($inslis[$i]['fal'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades a cargar del insumo ' . $inslis[$i]['cod'] . ' son superiores al faltante.';
											$val = false;
										} 

										if (round($cantidad, 4) > round($inslis[$i]['can'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades ingresadas del insumo ' . $inslis[$i]['cod'] . ' son superiores a la cantidad en el Producto.';
											$val = false;
										} 
									} 

									if ($val)
									{
										$art['neg'] = false;

										for ($i = 0; $i < count($inslis); $i++)
										{
											for ($j = 0; $j < count($presen[$i]); $j++)
											{
												if ($presen[$i][$j]['can'] > 0)
												{
													$art['cod'] = $presen[$i][$j]['cod'];
													$art['can'] = $presen[$i][$j]['can'];
													$res = ArticuloExiste ($art, $error);
													if ($res)
													{
														$res = TarifaSaldo($art, $centro, $tipTrans, $aprov, $error);
														if (!$res)
														{
															$fin = 1;
															pintarAlert1('El articulo' . $presen[$i][$j]['cod'] . 'no  tiene saldo unix');
														} 
													} 
													else
													{
														$fin = 1;
														pintarAlert1('El articulo' . $presen[$i][$j]['cod'] . 'no existe e unix');
													} 
												} 
											} 
										} 
										if (!isset($fin))
										{
											//Busco la información del paciente con Kardex Electronico
											//esKE( $pac['his'], $pac['ing'], $packe );
											$packe = $pac;
											condicionesKE( $packe, $cod );

											if( ( $packe['ke'] && $packe['sal'] ) ){
												unset( $hiReemplazar );
												?>
												<script>
													desmarcarOpcionesArticulosGenericos();
												</script>
												<?php
											}
											
											if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['gra'] ) || $packe['mmq'] )
											{
												if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['con'] ) || $packe['mmq'] )
												{
													if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['act'] ) || $packe['mmq'] )
													{
														if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['art'] ) || $packe['mmq'] )
														{
															if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['sal'] ) || $packe['mmq'] )
															{
																if( !isset( $hiReemplazar ) || ( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo)  )
																  ){
																	//Reemplazando el articulo
																	if( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo) && $val ){
																		
																		if( reemplazarMedicamentoKardex( $rdArticulo, $cod ) ){
																			
																			registrarFraccion(  @$rdArticulo, $centro['cod'] );
																			
																			$packe['ke'] = true;
																			$packe['act'] = true;
																			$packe['con'] = true;
																			$packe['sal'] = true;
																		}
																		else{
																			
																			pintarAlerta('NO SE REALIZO EL REEMPLAZO DEL MEDICAMENTO<br>EL MEDICAMENTO YA EXISTE CON LA MISMA FECHA Y HORA DE INICIO<br>O ESTA SIN SALDO');
																			pintarBoton();
																			exit;
																		}
																	}
																	
																	// grabo el encabezado del movimiento
																	$dronum = '';
																	Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
																	$ind = 1;
																	grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
																	$numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
																	$dato = $var . "-" . $cod;
																	grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', 1, 1);
																	descontarArticuloMatrix($cod, $cco, 'on', $var);
																	grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $exp[0], $historia . '-' . $ingreso, $wusuario);
																	for ($i = 0; $i < count($inslis); $i++)
																	{
																		for ($j = 0; $j < count($presen[$i]); $j++)
																		{
																			if ($presen[$i][$j]['can'] > 0 or $presen[$i][$j]['caj'] > 0)
																			{
																				if ($presen[$i][$j]['can'] > 0 and $presen[$i][$j]['caj'] > 0)
																				{
																					$can = ceil($presen[$i][$j]['can'] / $presen[$i][$j]['cnv']);
																					$tot = $presen[$i][$j]['can'] + $presen[$i][$j]['caj'];
																					grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $dato, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $presen[$i][$j]['caj'], $can, $presen[$i][$j]['can'] + $presen[$i][$j]['caj']);
																				} 
																				else if ($presen[$i][$j]['can'] > 0)
																				{
																					$can = ceil($presen[$i][$j]['can'] / $presen[$i][$j]['cnv']);
							
																					$tot = $presen[$i][$j]['can'];
																					grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $dato, '', 0, $can, $presen[$i][$j]['can']);
																				} 
																				else if ($presen[$i][$j]['caj'] > 0)
																				{
																					$can = 0;
																					$tot = $presen[$i][$j]['caj'];
																					grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $presen[$i][$j]['caj'], 0, $presen[$i][$j]['caj']);
																				} 
																				
																				actualizarAjuste($presen[$i][$j]['cod'], $presen[$i][$j]['caj'], $can, $historia, $centro['cod'], $wusuario, $ingreso, $presen[$i][$j]['cnv'], $tot);
							
																				if ($presen[$i][$j]['can'] > 0)
																				{
																					$art['cod'] = $presen[$i][$j]['cod'];
																					$art['can'] = $can;
							
																					if ($ind == 1)
																					{
																						$ind = 0;
																					} 
																					else
																					{
																						Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, false, $usu, $error);
																					} 

																					/*
																					*Fecha: 2021-06-11
																					*Descripción: se realiza llamado de factura inteligente.
																					*Autor: sebastian.nevado
																					*/
																					$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $presen[$i][$j]['cod'], $presen[$i][$j]['nom'], $can, $tipTrans, $dronum, $drolin);
																					if(!$aResultadoFactInteligente->exito)
																					{
																						echo $aResultadoFactInteligente->mensaje;
																					}
																					// FIN MODIFICACION
																					
																					$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, $error);
																					if (!$res)
																					{
																						pintarAlerta('EL ARTICULO ' . $presen[$i][$j]['cod'] . ' NO HA PODIDO SER CARGADO A ITDRO');
																					}
																					
																					registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error);
																				} 
																			} 
																		} 
																	} 
																	// cargamos los insumos de preparacion
																	for ($i = 0; $i < count($preparacion); $i++)
																	{
																		$tam = count($preparacion[$i]);
																		for ($j = 0; $j < $tam; $j++)
																		{
																			if (isset($escogidos[$i][$j]) and $escogidos[$i][$j] != '')
																			{
																				$exp = explode('-', $preparacion[$i][$j]);
																				$art['cod'] = $exp[0];
																				$art['can'] = $exp[2];
																				
																				grabarDetalleSalidaMatrix($exp[3], $codigo, $consecutivo, $wusuario, $exp[0], '', '', 0, 1, $exp[2]);
																				Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, false, $usu, $error);
																				$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, $error);
																				if (!$res)
																				{
																					pintarAlerta('EL ARTICULO ' . $art['can'] . ' NO HA PODIDO SER CARGADO A ITDRO');
																					$art['ubi'] = 'M';
																				} 
																				registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error);
																				
																				/*
																				 *Fecha: 2021-06-11
																				 *Descripción: se realiza llamado de factura inteligente.
																				 *Autor: sebastian.nevado
																				*/
																				$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $art['cod'], $exp[1], $art['can'], $tipTrans, $dronum, $drolin);
																				if(!$aResultadoFactInteligente->exito)
																				{
																					echo $aResultadoFactInteligente->mensaje;
																				}
																				// FIN MODIFICACION

																				/*
																				*Fecha: 2021-07-22
																				*Descripción: reduzco el inventario para los insumos
																				*Autor: sebastian.nevado
																				*/
																				//descontarArticuloMatrix($exp[3], $cco, '', $art['cod']);
																				// FIN MODIFICACION
																				
	//						                                                    echo $art['cod']."......".$exp[1];
	//						                                                    registrarSaldoInsumosPreparacion( $pac['his'], $pac['ing'], $art['cod'], $art['can'], $centro['cod'] );
	//						                                                    registrarMovimientoAplicacion( $pac['his'], $pac['ing'], $art['cod'], $art['can'], $centro['cod'], $exp[1] );
																			} 
																		} 
																	} 
																	// grabamos ahora el saldo de producto
																	$art['cod'] = $cod;
																	$art['can'] = 1; 
																	$art['nom'] = $row1[0];
																	// cambiamos en la siguiente version
																	if (!$centro['apl'] and $serv['apl'])
																	{
																		$centro['apl'] = true;
																	} 
							
																	if (!$centro['apl'] and !$serv['apl'])
																	{
																		$val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																	} 
																	else
																	{
																		$val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																		$trans['num'] = $dronum;
																		if ( $drolin == 1 )
																		{
																			$trans['lin'] = 1;
																		} 
																		else
																		{
																			$trans['lin'] = '';
																		} 
																	   $val =  registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
																	   $ccoCM = centroCostosCM();
																	   actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $centro, $art, $dronum, $drolin, $ccoCM );	//Noviembre 8 de 2011
																	   $aplicaron = true;
																	}
																	
																	//Si todo esta bien, dispensa en KE
																	if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] && $val ){
																		registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], $idKardex, $dronum, $drolin, $aplicaron );
																		
																		/****************************************************************
																		* Mayo 11 de 2011 
																		****************************************************************/
																		if( $aplicaron ){
																			actualizandoAplicacion( $idKardex, $centro['cod'], $art['cod'], $dronum, $drolin );
																		}
																		/****************************************************************/
																	}
																	
																	pintarConfi($cod, $var, $row1[0], 'LOTE');
																	pintarBoton();
																}
																else{
	//			                                    				pintarAlerta('DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR');
	//		                                    					pintarBoton();
																	
																	if( $packe['ke'] && !$packe['sal'] && $packe['art'] ){
																		// pintarAlert1( 'NO HAY SALDO PARA EL ARTICULO' );
																		pintarAlert1( 'EL ARTICULO YA FUE DISPENSADO' );
																	}
																	elseif( isset( $hiNoReemplazo ) ){
																		pintarAlert1( 'NO HAY ARTICULO GENERICO A REEMPLAZAR. PUEDE QUE EL ARTICULO GENERICO NO ESTE CONFIRMADO O APROBADO POR EL REGENTE.' );
																	}
																	elseif( isset( $hiReemplazar ) ){
																		pintarAlert1( 'DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR' );
																	}
																	
	//		                                    					pintarAlert1( 'DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR' );
																	pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
																	$preparacion = consultarPreparacion($escogidos);
																	pintarPreparacion($preparacion, $escogidos, $carro);
																}
															//Mensajes del KE
															}
															else{
																pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE');
																pintarBoton();
															}
														}
														else{
															pintarAlerta('EL ARTICULO NO FUE CARGADO AL PACIENTE');
															pintarBoton();
														}
													}
													else{
														pintarAlerta('EL KARDEX ELECTRONICO NO SE HA ACTUALIZADO');
														pintarBoton();
													}
												}
												else{
													pintarAlerta('NO TIENE KARDEX ELECTRONICO CONFIRMADO');
													pintarBoton();
												}
											}
											else{
												pintarAlerta('NO TIENE KARDEX ELECTRONICO GRABADO');
												pintarBoton();
											}
										} 
										else
										{
											pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
											$preparacion = consultarPreparacion($escogidos);
											pintarPreparacion($preparacion, $escogidos, $carro);
										} 
									} 
									else
									{
										pintarAlert1($mensaje);
										pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
										$preparacion = consultarPreparacion($escogidos);
										pintarPreparacion($preparacion, $escogidos, $carro);
									} 
								} 
							} 
						} 
					} 
					break;
				default:
					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR LA PRESENTACION  QUE VA A CARGAR');
						pintarBoton();
					} 
					else
					{
						$var=urldecode($var);
						$val = validarMatrix($cod, $cco, $var, 'off', $mensaje);
						if (!$val)
						{
							pintarAlerta($mensaje);
						} 
						else
						{
							$art['lot'] = '';
							$exp = explode('-', $var);
							$art['cod'] = $exp[0];
							$art['neg'] = false;
							$art['can'] = 1;
							$res = ArticuloExiste ($art, $error);
							if ($res)
							{
								$res = TarifaSaldo($art, $centro, $tipTrans, $aprov, $error);
								if ($res)
								{//3
									//Busco la información del paciente con Kardex Electronico
									//esKE( $pac['his'], $pac['ing'], $packe );
									$packe = $pac;
	//                            	condicionesKE( $packe, $art['cod'] );
									condicionesKE( $packe, $cod );
									if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['gra'] ) || $packe['mmq'] )
									{
										if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['con'] ) || $packe['mmq'] )
										{
											if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['act'] ) || $packe['mmq'] )
											{
												if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['art'] ) || $packe['mmq'] )
												{
													if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['sal'] ) || $packe['mmq'] )
													{
														if( $packe['mmq'] ){
															
															if( !isset($canMMQ) || $canMMQ == 0 || empty( $canMMQ )  ){
																$canMMQ = 1;
															}
															
															$cantidadACargar = $canMMQ;
														}
														else{
															$cantidadACargar = 1;
														}
													
														if( !isset($solicitudCamillero) ){
															$solicitudCamillero = false;
														}
														
														for( $i = 0; $i < $cantidadACargar; $i++ ){
															$val = false;
															// grabo el encabezado del movimiento
															$dronum = '';
															Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
															grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
															$numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
															grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
															descontarArticuloMatrix($cod, $cco, '', $art['cod']);
															grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $centro['cod'], $historia . '-' . $ingreso, $wusuario);
															grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
															
															/*
															 *Fecha: 2021-06-11
															 *Descripción: se realiza llamado de factura inteligente.
															 *Autor: sebastian.nevado
															*/
															$sNombreArticulo = substr($var, strlen($exp[0]."-"), strlen($var)-1);
															$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $art['cod'], $sNombreArticulo, $art['can'], $tipTrans, $dronum, $drolin);
															if(!$aResultadoFactInteligente->exito)
															{
																echo $aResultadoFactInteligente->mensaje;
															}
															// FIN MODIFICACION
															
															$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, $error);
															if (!$res)
															{
																pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
																$art['ubi'] = 'M';
															} 
															registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error); 
															// cambiamos en la siguiente version
															if (!$centro['apl'] and $serv['apl'])
															{
																$centro['apl'] = true;
															} 
							
															if (!$centro['apl'] and !$serv['apl'])
															{
																$val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																
	//						                                    echo "antes de registrar....<br>";
																if( esMMQ($cod) ){
																	registrarSaldoInsumosPreparacion( $pac['his'], $pac['ing'], $art['cod'], 1, $centro['cod'] );	//Enero 7 de 2011
																	registrarMovimientoAplicacion( $pac['his'], $pac['ing'], $art['cod'], 1, $centro['cod'], $exp[1], $dronum, $drolin );
																}
															} 
															else
															{
																$val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																$trans['num'] = $dronum;
																if ($drolin == 1)
																{
																	$trans['lin'] = 1;
																} 
																else
																{
																	$trans['lin'] = '';
																} 
																$val = registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
																$ccoCM = centroCostosCM();
																actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $centro, $art, $dronum, $drolin, $ccoCM );	//Noviembre 8 de 2011
																$aplicaron = true;
															} 
															
															if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] ){
																registrarArticuloKE( $cod, $pac['his'], $pac['ing'], $idKardex, $dronum, $drolin, $aplicaron );
																
																/****************************************************************
																 * Mayo 11 de 2011 
																 ****************************************************************/
																if( $aplicaron ){
																	actualizandoAplicacion( $idKardex, $centro['cod'], $art['cod'], $dronum, $drolin );
																}
																/****************************************************************/
															}
														}
														
														echo "<table align='center'>";
														echo "<tr><td class='titulo4'>Cantidad cargada: ".($i)."<td></tr>";
														echo "</table>";
														
														pintarConfi($cod, $var, $row1[0], 'PRESENTACION');
														pintarBoton();
						
														?>
															<script>
	//					                                        window . opener . producto . submit();
	//					                                        window . close();
															 </script>
															<?php
													}
													else{
														pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE');
														pintarBoton();
													}
												}
												else{
													pintarAlerta('EL ARTICULO NO HA SIDO CARGADO AL PACIENTE');
													pintarBoton();
												}
											}
											else{
												pintarAlerta('NO TIENE KARDEX ELECTRONICO ACTUALIZADO');
												pintarBoton();
											}
										}
										else{
											pintarAlerta('NO TIENE KARDEX ELECTRONICO CONFIRMADO');
											pintarBoton();
										}
									}
									else{
										pintarAlerta('NO TIENE KARDEX ELECTRONICO GRABADO');
										pintarBoton();
									}
								}//fin 3 
								else
								{
									pintarAlerta('EL ARTICULO A CARGAR NO TIENE TARIFA EN UNIX');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL ARTICULO A CARGAR NO EXISTE EN UNIX');
								pintarBoton();
							} 
						} 
					} 
			} // switch
		}
		else{
			
			/******************************************************************************
			 * Sin conexión con Unix
			 ******************************************************************************/
			switch ($row1[2])
			{
				case 'on':
					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR EL LOTE QUE VA A CARGAR');
						pintarBoton();
					} 
					else
					{
						$art['lot'] = $var;
						$val = validarMatrix($cod, $cco, $var, 'on', $mensaje);
						if (!$val)
						{
							pintarAlerta($mensaje);
							pintarBoton();
						} 
						else
						{
							if ( $row1[1] == 'on' && $row1[3] != 'on' )	//Producto codificado
							{//1
								$art['cod'] = $cod;
								$art['neg'] = false;
								$art['can'] = 1;

								$res = ArticuloExiste ($art, $error);
								if ($res)
								{//2
									$res = TarifaSaldoMatrix($art, $centro, $tipTrans, $aprov, $error);
									if ($res)
									{//3 
										//Busco la información del paciente con Kardex Electronico
	//                                	esKE( $pac['his'], $pac['ing'], $packe );
										$packe = $pac;
										condicionesKE( $packe, $cod );
										if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['gra'] ) || $packe['mmq'] )
										{
											if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['con'] ) || $packe['mmq'] )
											{
												if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['act'] ) || $packe['mmq'] )
												{
													if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['art'] ) || $packe['mmq'] )
													{
														if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['sal'] > 0 ) || $packe['mmq'] )
														{
															if( true || !isset( $hiReemplazar ) || ( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo)  ) ){

																$val = false;
																// grabo el encabezado del movimiento
																$dronum = '';			                      
																Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
																grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
																$numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
																$dato = $var . "-" . $cod;
																grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', 1, 1);
																descontarArticuloMatrix($cod, $cco, 'on', $var);
																grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $exp[0], $historia . '-' . $ingreso, $wusuario);
																grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', $art['can'], $art['can']);
																//$res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);	//Enero 2 de 2013. Si no hay conexion con unix no se debe registrar en itdro
																// if (!$res)
																// {
																	// pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
																	// $art['ubi'] = 'M';
																// } 
																
																$validar = registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
																
																if( $validar ){
																	/***************************************************************************
																	 * Enero 2 de 2013
																	 *
																	 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
																	 ***************************************************************************/
																	realizarMovimientoSaldos( $conex, $bd, $tipTrans, $centro[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
																	/***************************************************************************/
																}
							
																if (!$centro['apl'] and $serv['apl'])
																{
																	$centro['apl'] = true;
																} 
							
																if (!$centro['apl'] and !$serv['apl'])
																{
																	$val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																} 
																else
																{
																	$val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																	$trans['num'] = $dronum;
																	if ($drolin == 1)
																	{
																		$trans['lin'] = 1;
																	} 
																	else
																	{
																		$trans['lin'] = '';
																	} 
																	$val = registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
																	$ccoCM = centroCostosCM();
																	actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $centro, $art, $dronum, $drolin, $ccoCM );	//Noviembre 8 de 2011
																	$aplicaron = true;
																} 
																
																//Reemplazando el articulo
	//						                                    if( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo) ){
	//						                                    	reemplazarMedicamentoKardex( @$rdArticulo, $art['cod'] );						                                    	
	//						                                    }
																
																if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] ){
																	registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], $idKardex, $dronum, $drolin, $aplicaron );
																	
																	/****************************************************************
																	 * Mayo 11 de 2011 
																	 ****************************************************************/
																	if( $aplicaron ){
																		actualizandoAplicacion( $idKardex, $centro['cod'], $art['cod'], $dronum, $drolin );
																	}
																	/****************************************************************/
																}
																
																pintarConfi($cod, $var, $row1[0], 'LOTE');
																pintarBoton();
							
																?>
																<script>
		//					                                        window . opener . producto . submit();
		//					                                        window . close();
																 </script >
																<?php
															}
															else{
																pintarAlerta('DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR');
																pintarBoton();
															}
														}
														else{
															pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE');
															pintarBoton();
														}
													}
													else{
														pintarAlerta('EL ARTICULO NO HA SIDO CARGADO AL PACIENTE');
														pintarBoton();
													}
												}
												else{
													pintarAlerta('NO TIENE KARDEX ELECTRONICO ACTUALIZADO');
													pintarBoton();
												}
											}
											else{
												pintarAlerta('EL KARDEX ELECTRONICO NO SE HA CONFIRMADO');
												pintarBoton();
											}
										}
										else{
											pintarAlerta('EL KARDEX ELECTRONICO NO SE HA GRABADO');
											pintarBoton();
										}
									}//fin3 
									else
									{
										pintarAlerta('EL ARTICULO A CARGAR NO TIENE TARIFA EN UNIX');
										pintarBoton();
									} 
								}//fin2 
								else
								{
									pintarAlerta('EL ARTICULO A CARGAR NO EXISTE EN UNIX');
									pintarBoton();
								} 
							}//fin1 
							else
							{	//Producto NO CODIFICADO
								if (!isset($grabar))
								{ 
									// consulto la lista de insumos que componen el producto
									consultarInsumos($cod, $inslis); 
									// para cada insumo consultamos las presentaciones y su ajuste
									for ($i = 0; $i < count($inslis); $i++)
									{
										$presen[$i] = consultarPresentaciones($inslis[$i], $cco, $historia, $ingreso);
										$inslis[$i]['fal'] = $inslis[$i]['can'] - $inslis[$i]['aju'];
										if ($inslis[$i]['fal'] < 0)
										{
											$inslis[$i]['fal'] = 0;
										} 
									} 
									pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio); 
									// aca voy a investigar la lista de insumos que se pueden cargar con el producto
									$preparacion = consultarPreparacion($escogidos);
									pintarPreparacion($preparacion, $escogidos, $carro);
								} 
								else
								{
									$val = true;
									$mensaje = ''; 
									// validamos que la suma utilizada por las fracciones sea igual al faltante
									for ($i = 0; $i < count($inslis); $i++)
									{
										$faltante = 0;
										$cantidad = 0;
										for ($j = 0; $j < count($presen[$i]); $j++)
										{
											if ($presen[$i][$j]['caj'] != '' and $presen[$i][$j]['caj'] > $presen[$i][$j]['aju'])
											{
												$mensaje = $mensaje . ' Las cantidades ingresadas para el ajuste de ' . $presen[$i][$j]['cod'] . ' son mayores al saldo de ajuste.';
												$val = false;
											} 
											if ($presen[$i][$j]['can'] != '')
											{
												$faltante = $faltante + $presen[$i][$j]['can'];
											} 

											if ($presen[$i][$j]['aju'] > 0 and $presen[$i][$j]['can'] > 0 and $presen[$i][$j]['caj'] != $presen[$i][$j]['caj'])
											{
												$mensaje = $mensaje . 'Antes de agregar cantidad de la presentación ' . $presen[$i][$j]['cod'] . ' debe consumir el saldo de ajuste.';
												$val = false;
											} 

											$cantidad = $cantidad + (float) $presen[$i][$j]['can'] + (float) $presen[$i][$j]['caj'];
										} 

										if (round($faltante, 4) < round($inslis[$i]['fal'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades a cargar del insumo ' . $inslis[$i]['cod'] . ' son inferiores al faltante.';
											$val = false;
										} 

										if (round($cantidad, 4) < round($inslis[$i]['can'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades ingresadas del insumo ' . $inslis[$i]['cod'] . ' son inferiores a la cantidad en el Producto.';
											$val = false;
										} 

										if (round($faltante, 4) > round($inslis[$i]['fal'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades a cargar del insumo ' . $inslis[$i]['cod'] . ' son superiores al faltante.';
											$val = false;
										} 

										if (round($cantidad, 4) > round($inslis[$i]['can'], 4))
										{
											$mensaje = $mensaje . ' Las cantidades ingresadas del insumo ' . $inslis[$i]['cod'] . ' son superiores a la cantidad en el Producto.';
											$val = false;
										} 
									} 

									if ($val)
									{
										$art['neg'] = false;

										for ($i = 0; $i < count($inslis); $i++)
										{
											for ($j = 0; $j < count($presen[$i]); $j++)
											{
												if ($presen[$i][$j]['can'] > 0)
												{
													$art['cod'] = $presen[$i][$j]['cod'];
													$art['can'] = $presen[$i][$j]['can'];
													$res = ArticuloExiste ($art, $error);
													if ($res)
													{
														$res = TarifaSaldoMatrix($art, $centro, $tipTrans, $aprov, $error);
														if (!$res)
														{
															$fin = 1;
															pintarAlert1('El articulo' . $presen[$i][$j]['cod'] . 'no tiene saldo unix');
														} 
													} 
													else
													{
														$fin = 1;
														pintarAlert1('El articulo' . $presen[$i][$j]['cod'] . 'no existe e unix');
													} 
												} 
											} 
										} 
										if (!isset($fin))
										{
											//Busco la información del paciente con Kardex Electronico
											//esKE( $pac['his'], $pac['ing'], $packe );
											$packe = $pac;
											condicionesKE( $packe, $cod );

											if( ( $packe['ke'] && $packe['sal'] ) ){
												unset( $hiReemplazar );
												?>
												<script>
													desmarcarOpcionesArticulosGenericos();
												</script>
												<?php
											}

											if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['gra'] ) || $packe['mmq'] )
											{
												if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['con'] ) || $packe['mmq'] )
												{
													if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['act'] ) || $packe['mmq'] )
													{
														if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['art'] ) || $packe['mmq'] )
														{
															if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['sal'] ) || $packe['mmq'] )
															{
																if( !isset( $hiReemplazar ) || ( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo)  ) )
																{
																	//Reemplazando el articulo
																	if( isset( $hiReemplazar ) && isset($rdArticulo) && !empty($rdArticulo) && $val ){
																		
																		if( reemplazarMedicamentoKardex( $rdArticulo, $cod ) ){
																			
																			registrarFraccion(  @$rdArticulo, $centro['cod'] );
																			
																			$packe['ke'] = true;
																			$packe['act'] = true;
																			$packe['con'] = true;
																			$packe['sal'] = true;
																		}
																		else{
																			
																			pintarAlerta('NO SE REALIZO EL REEMPLAZO DEL MEDICAMENTO<br>EL MEDICAMENTO YA EXISTE CON LA MISMA FECHA Y HORA DE INICIO<br>O ESTA SIN SALDO');
																			pintarBoton();
																			exit;
																		}
																	}
																	// grabo el encabezado del movimiento
																	$dronum = '';
																	Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
																	$ind = 1;
																	grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
																	$numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
																	$dato = $var . "-" . $cod;
																	grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, '', '', 1, 1);
																	descontarArticuloMatrix($cod, $cco, 'on', $var);
																	grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $exp[0], $historia . '-' . $ingreso, $wusuario);
																	for ($i = 0; $i < count($inslis); $i++)
																	{
																		for ($j = 0; $j < count($presen[$i]); $j++)
																		{
																			if ($presen[$i][$j]['can'] > 0 or $presen[$i][$j]['caj'] > 0)
																			{
																				if ($presen[$i][$j]['can'] > 0 and $presen[$i][$j]['caj'] > 0)
																				{
																					$can = ceil($presen[$i][$j]['can'] / $presen[$i][$j]['cnv']);
																					$tot = $presen[$i][$j]['can'] + $presen[$i][$j]['caj'];
																					grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $dato, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $presen[$i][$j]['caj'], $can, $presen[$i][$j]['can'] + $presen[$i][$j]['caj']);
																				} 
																				else if ($presen[$i][$j]['can'] > 0)
																				{
																					$can = ceil($presen[$i][$j]['can'] / $presen[$i][$j]['cnv']);
							
																					$tot = $presen[$i][$j]['can'];
																					grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $dato, '', 0, $can, $presen[$i][$j]['can']);
																				} 
																				else if ($presen[$i][$j]['caj'] > 0)
																				{
																					$can = 0;
																					$tot = $presen[$i][$j]['caj'];
																					grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, '', $dato, $presen[$i][$j]['cod'] . '-' . $presen[$i][$j]['nom'], $presen[$i][$j]['caj'], 0, $presen[$i][$j]['caj']);
																				}

																				actualizarAjuste($presen[$i][$j]['cod'], $presen[$i][$j]['caj'], $can, $historia, $centro['cod'], $wusuario, $ingreso, $presen[$i][$j]['cnv'], $tot);
							
																				if ($presen[$i][$j]['can'] > 0)
																				{
																					$art['cod'] = $presen[$i][$j]['cod'];
																					$art['can'] = $can;
							
																					if ($ind == 1)
																					{
																						$ind = 0;
																					} 
																					else
																					{
																						Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, false, $usu, $error);
																					} 

																					/*
																					*Fecha: 2021-06-11
																					*Descripción: se realiza llamado de factura inteligente.
																					*Autor: sebastian.nevado
																					*/
																					$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $presen[$i][$j]['cod'], $row1[0], $can, $tipTrans, $dronum, $drolin);
																					if(!$aResultadoFactInteligente->exito)
																					{
																						echo $aResultadoFactInteligente->mensaje;
																					}
																					// FIN MODIFICACION

																					// $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
																					// if (!$res)
																					// {
																						// pintarAlerta('EL ARTICULO ' . $presen[$i][$j]['cod'] . ' NO HA PODIDO SER CARGADO A ITDRO');
																					// } 
																					
																					$validar = registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
																					
																					if( $validar ){
																						/***************************************************************************
																						 * Enero 2 de 2013
																						 *
																						 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
																						 ***************************************************************************/
																						realizarMovimientoSaldos( $conex, $bd, $tipTrans, $centro[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
																						/***************************************************************************/
																					}
																				} 
																			} 
																		} 
																	} 
																	// cargamos los insumos de preparacion
																	for ($i = 0; $i < count($preparacion); $i++)
																	{
																		$tam = count($preparacion[$i]);
																		for ($j = 0; $j < $tam; $j++)
																		{
																			if (isset($escogidos[$i][$j]) and $escogidos[$i][$j] != '')
																			{
																				$exp = explode('-', $preparacion[$i][$j]);
																				$art['cod'] = $exp[0];
																				$art['can'] = $exp[2];
																				grabarDetalleSalidaMatrix($exp[3], $codigo, $consecutivo, $wusuario, $exp[0], '', '', 0, 1, $exp[2]);
																				Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, false, $usu, $error);
																				// $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
																				// if (!$res)
																				// {
																					// pintarAlerta('EL ARTICULO ' . $art['can'] . ' NO HA PODIDO SER CARGADO A ITDRO');
																					// $art['ubi'] = 'M';
																				// }
																				
																				$validar = registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
																				
																				if( $validar ){
																					/***************************************************************************
																					 * Enero 2 de 2013
																					 *
																					 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
																					 ***************************************************************************/
																					realizarMovimientoSaldos( $conex, $bd, $tipTrans, $centro[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
																					/***************************************************************************/
																				}
																				
																				/*
																				 *Fecha: 2021-06-11
																				 *Descripción: se realiza llamado de factura inteligente.
																				 *Autor: sebastian.nevado
																				*/
																				$aResultadoFactInteligente = llamarFacturacionInteligente($pac, $centro['cod'], $art['cod'], $exp[1], $art['can'], $tipTrans, $dronum, $drolin);
																				if(!$aResultadoFactInteligente->exito)
																				{
																					echo $aResultadoFactInteligente->mensaje;
																				}
																				// FIN MODIFICACION
																				
																				/*
																				*Fecha: 2021-07-22
																				*Descripción: reduzco el inventario para los insumos
																				*Autor: sebastian.nevado
																				*/
																				//descontarArticuloMatrix($exp[3], $cco, '', $art['cod']);
																				// FIN MODIFICACION
																				
	//						                                                    echo $art['cod']."......".$exp[1];
	//						                                                    registrarSaldoInsumosPreparacion( $pac['his'], $pac['ing'], $art['cod'], $art['can'], $centro['cod'] );
	//						                                                    registrarMovimientoAplicacion( $pac['his'], $pac['ing'], $art['cod'], $art['can'], $centro['cod'], $exp[1] );
																			} 
																		} 
																	} 
																	// grabamos ahora el saldo de producto
																	$art['cod'] = $cod;
																	$art['can'] = 1; 
																	$art['nom'] = $row1[0];
																	// cambiamos en la siguiente version
																	if (!$centro['apl'] and $serv['apl'])
																	{
																		$centro['apl'] = true;
																	} 
							
																	if (!$centro['apl'] and !$serv['apl'])
																	{
																		$val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																	} 
																	else
																	{
																		$val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																		$trans['num'] = $dronum;
																		if ( $drolin == 1 )
																		{
																			$trans['lin'] = 1;
																		} 
																		else
																		{
																			$trans['lin'] = '';
																		} 
																	   $val =  registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
																	   $ccoCM = centroCostosCM();
																	   actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $centro, $art, $dronum, $drolin, $ccoCM );	//Noviembre 8 de 2011
																	   $aplicaron = true;
																	}
																	
																	//Si todo esta bien, dispensa en KE
																	if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] && $val ){
																		registrarArticuloKE( $art['cod'], $pac['his'], $pac['ing'], $idKardex, $dronum, $drolin, $aplicaron );
																		
																		/****************************************************************
																		* Mayo 11 de 2011 
																		****************************************************************/
																		if( $aplicaron ){
																			actualizandoAplicacion( $idKardex, $centro['cod'], $art['cod'], $dronum, $drolin );
																		}
																		/****************************************************************/
																	}
																	
																	pintarConfi($cod, $var, $row1[0], 'LOTE');
																	pintarBoton();
																}
																else{
	//			                                    				pintarAlerta('DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR');
	//		                                    					pintarBoton();
																	
																	if( $packe['ke'] && !$packe['sal'] && $packe['art'] ){
																		// pintarAlert1( 'NO HAY SALDO PARA EL ARTICULO' );
																		pintarAlert1( 'EL ARTICULO YA FUE DISPENSADO' );
																	}
																	elseif( isset( $hiNoReemplazo ) ){
																		pintarAlert1( 'NO HAY ARTICULO GENERICO A REEMPLAZAR. PUEDE QUE EL ARTICULO GENERICO NO ESTE CONFIRMADO O APROBADO POR EL REGENTE.' );
																	}
																	elseif( isset( $hiReemplazar ) ){
																		pintarAlert1( 'DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR' );
																	}
																	
	//		                                    					pintarAlert1( 'DEBE SELECCIONAR UN ARTICULO A REEMPLAZAR' );
																	pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
																	$preparacion = consultarPreparacion($escogidos);
																	pintarPreparacion($preparacion, $escogidos, $carro);
																}
															//Mensajes del KE
															}
															else{
																pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE');
																pintarBoton();
															}
														}
														else{
															pintarAlerta('EL ARTICULO NO FUE CARGADO AL PACIENTE');
															pintarBoton();
														}
													}
													else{
														pintarAlerta('EL KARDEX ELECTRONICO NO SE HA ACTUALIZADO');
														pintarBoton();
													}
												}
												else{
													pintarAlerta('NO TIENE KARDEX ELECTRONICO CONFIRMADO');
													pintarBoton();
												}
											}
											else{
												pintarAlerta('NO TIENE KARDEX ELECTRONICO GRABADO');
												pintarBoton();
											}
										} 
										else
										{
											pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
											$preparacion = consultarPreparacion($escogidos);
											pintarPreparacion($preparacion, $escogidos, $carro);
										} 
									} 
									else
									{
										pintarAlert1($mensaje);
										pintarInsumos($inslis, $presen , $cod, $cco, $historia, $ingreso, $var, $servicio);
										$preparacion = consultarPreparacion($escogidos);
										pintarPreparacion($preparacion, $escogidos, $carro);
									} 
								} 
							} 
						} 
					} 
					break;
				default:
					if (!isset($var) or $var == '')
					{
						pintarAlerta('DEBE SELECCIONAR LA PRESENTACION  QUE VA A CARGAR');
						pintarBoton();
					} 
					else
					{
						$var=urldecode($var);
						$val = validarMatrix($cod, $cco, $var, 'off', $mensaje);
						if (!$val)
						{
							pintarAlerta($mensaje);
						} 
						else
						{
							$art['lot'] = '';
							$exp = explode('-', $var);
							$art['cod'] = $exp[0];
							$art['neg'] = false;
							$art['can'] = 1;
							$res = ArticuloExiste ($art, $error);
							if ($res)
							{
								$res = TarifaSaldoMatrix($art, $centro, $tipTrans, $aprov, $error);
								if ($res)
								{//3
									//Busco la información del paciente con Kardex Electronico
									//esKE( $pac['his'], $pac['ing'], $packe );
									$packe = $pac;
	//                            	condicionesKE( $packe, $art['cod'] );
									condicionesKE( $packe, $cod );
									if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['gra'] ) || $packe['mmq'] )
									{
										if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['con'] ) || $packe['mmq'] )
										{
											if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['act'] ) || $packe['mmq'] )
											{
												if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['art'] ) || $packe['mmq'] )
												{
													if( !$packe['ke'] || $packe['nut'] || $packe['nka'] || ( $packe['ke'] && $packe['sal'] ) || $packe['mmq'] )
													{
														if( $packe['mmq'] ){
															
															if( !isset($canMMQ) || $canMMQ == 0 || empty( $canMMQ )  ){
																$canMMQ = 1;
															}
															
															$cantidadACargar = $canMMQ;
														}
														else{
															$cantidadACargar = 1;
														}
													
														if( !isset($solicitudCamillero) ){
															$solicitudCamillero = false;
														}
														
														for( $i = 0; $i < $cantidadACargar; $i++ ){
															$val = false;
															// grabo el encabezado del movimiento
															$dronum = '';
															Numeracion($pac, $centro['fap'], $tipTrans, $aprov, $centro, $date, $cns, $dronum, $drolin, true, $usu, $error);
															grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $historia . '-' . $ingreso, $dronum);
															$numtra = $codigo . '-' . $consecutivo; //actualizamos el numero del movimeinto real
															grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
															descontarArticuloMatrix($cod, $cco, '', $art['cod']);
															grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $centro['cod'], $historia . '-' . $ingreso, $wusuario);
															grabarDetalleSalidaMatrix($cod, $codigo, $consecutivo, $wusuario, $var, '', '', '', 1, 1);
															// $res = registrarItdro($dronum, $drolin, $centro['fap'], date('Y-m-d'), $centro, $pac, $art, &$error);
															// if (!$res)
															// {
																// pintarAlerta('EL ARTICULO NO HA PODIDO SER CARGADO A ITDRO');
																// $art['ubi'] = 'M';
															// } 
															
															$validar = registrarDetalleCargo (date('Y-m-d'), $dronum, $drolin, $art, $usu, $error, "000143" );
															
															if( $validar ){
																/***************************************************************************
																 * Enero 2 de 2013
																 *
																 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
																 ***************************************************************************/
																realizarMovimientoSaldos( $conex, $bd, $tipTrans, $centro[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
																/***************************************************************************/
															}
																				
															// cambiamos en la siguiente version
															if (!$centro['apl'] and $serv['apl'])
															{
																$centro['apl'] = true;
															} 
							
															if (!$centro['apl'] and !$serv['apl'])
															{
																$val = registrarSaldosNoApl($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																
	//						                                    echo "antes de registrar....<br>";
																if( esMMQ($cod) ){
																	registrarSaldoInsumosPreparacion( $pac['his'], $pac['ing'], $art['cod'], 1, $centro['cod'] );	//Enero 7 de 2011
																	registrarMovimientoAplicacion( $pac['his'], $pac['ing'], $art['cod'], 1, $centro['cod'], $exp[1], $dronum, $drolin );
																}
															} 
															else
															{
																$val = registrarSaldosAplicacion($pac, $art, $centro, $aprov, $usu, $tipTrans, false, $error);
																$trans['num'] = $dronum;
																if ($drolin == 1)
																{
																	$trans['lin'] = 1;
																} 
																else
																{
																	$trans['lin'] = '';
																} 
																$val = registrarAplicacion($pac, $art, $centro, $aprov, date('Y-m-d'), $ronApl, $usu, $tipTrans, $dronum, $drolin, $error);
																$ccoCM = centroCostosCM();
																actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $centro, $art, $dronum, $drolin, $ccoCM );	//Noviembre 8 de 2011
																$aplicaron = true;
															} 
															
															if( $packe['ke'] && $packe['act'] && $packe['con'] && $packe['sal'] ){
																registrarArticuloKE( $cod, $pac['his'], $pac['ing'], $idKardex, $dronum, $drolin, $aplicaron );
																
																/****************************************************************
																 * Mayo 11 de 2011 
																 ****************************************************************/
																if( $aplicaron ){
																	actualizandoAplicacion( $idKardex, $centro['cod'], $art['cod'], $dronum, $drolin );
																}
																/****************************************************************/
															}
														}
														
														echo "<table align='center'>";
														echo "<tr><td class='titulo4'>Cantidad cargada: ".($i)."<td></tr>";
														echo "</table>";
														
														pintarConfi($cod, $var, $row1[0], 'PRESENTACION');
														pintarBoton();
						
														?>
															<script>
	//					                                        window . opener . producto . submit();
	//					                                        window . close();
															 </script>
															<?php
													}
													else{
														pintarAlerta('EL ARTICULO YA FUE DISPENSADO EN EL KE');
														pintarBoton();
													}
												}
												else{
													pintarAlerta('EL ARTICULO NO HA SIDO CARGADO AL PACIENTE');
													pintarBoton();
												}
											}
											else{
												pintarAlerta('NO TIENE KARDEX ELECTRONICO ACTUALIZADO');
												pintarBoton();
											}
										}
										else{
											pintarAlerta('NO TIENE KARDEX ELECTRONICO CONFIRMADO');
											pintarBoton();
										}
									}
									else{
										pintarAlerta('NO TIENE KARDEX ELECTRONICO GRABADO');
										pintarBoton();
									}
								}//fin 3 
								else
								{
									pintarAlerta('EL ARTICULO A CARGAR NO TIENE TARIFA EN UNIX');
									pintarBoton();
								} 
							} 
							else
							{
								pintarAlerta('EL ARTICULO A CARGAR NO EXISTE EN UNIX');
								pintarBoton();
							} 
						} 
					} 
			}
		}
    } 
} 

/**
 * Función para hacer el proceso de factura inteligente.
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: array
 */
function CargarCargosErp($conex, $pac, $wmovhos, $wcliame, $art, $tipTrans, $numCargoInv, $linCargoInv, $cCentroCosto )
{
	global $wemp_pmla;
	global $wbasedato;
	global $wusuario;
	global $wuse;
	global $cco;
	global $desde_CargosPDA;
	$desde_CargosPDA = true;
	global $accion_iq;
	$accion_iq = '';
	$sql = "SELECT Ccoerp
			  FROM ".$wmovhos."_000011
			 WHERE ccocod = '".$pac['sac']."'
		";
	
	$resCco = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numCco = mysql_num_rows( $resCco );
	$CcoErp = false;
	if( $rowsCco = mysql_fetch_array( $resCco) ){
		$CcoErp = $rowsCco[ 'Ccoerp' ] == 'on' ? true: false;
	}
	
	//Si el cco no maneja cargo ERP o no está activo los cargos ERP no se ejecuta esta acción
	$cargarEnErp = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cargosPDA_ERP" );
	if( !$CcoErp || $cargarEnErp != 'on' ){
		return;
	}
	
	$sql = "SELECT *
			  FROM ".$wmovhos."_000016
			 WHERE inghis = '".$pac['his']."'
			   AND inging = '".$pac['ing']."'
		";
	
	$resRes = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numRes = mysql_num_rows( $resRes );
	if( $rowsRes = mysql_fetch_array( $resRes) ){
		
				
		$sql = "SELECT *
				  FROM ".$wcliame."_000101
				 WHERE Inghis = '".$pac['his']."'
				   AND Ingnin = '".$pac['ing']."'
			";
		
		$resIng = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numIng = mysql_num_rows( $resIng );
	
		if( $rowsIng = mysql_fetch_array( $resIng) ){
		
			
			$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
		
			if( $rowsIng[ 'Ingtpa' ] == 'P' ){
				$empresa = $codEmpParticular;
			}
			else{
				$empresa = $rowsIng[ 'Ingcem' ];
			}
			
			$sql = "SELECT *
					  FROM ".$wcliame."_000024
					 WHERE empcod = '".$empresa."'
					";
		
			$resEmp = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numEmp = mysql_num_rows( $resEmp );
			
			if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
		
				//Información de empresa
				$wcodemp 	  = $rowsEmp[ 'Empcod' ];
				$wnomemp 	  = $rowsEmp[ 'Empnom' ];
				$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
				$nitEmpresa   = $rowsEmp[ 'Empnit' ];
				$wtar		  = $rowsEmp[ 'Emptar' ];
			
				//Información del paciente
				$tipoPaciente = $rowsIng[ 'Ingcla' ];
				$tipoIngreso  = $rowsIng[ 'Ingtin' ];
				$wser		  = $rowsIng[ 'Ingsei' ];
				$wfecing	  = $rowsIng[ 'Ingfei' ];
				
				//Consulta información de pacientes
				$infoPacienteCargos = consultarNombresPaciente( $conex, $pac['his'], $wemp_pmla );
				
				//Conceptos de grabación
				$wcodcon = consultarAliasPorAplicacion( $conex, $wemp_pmla, "concepto_medicamentos_mueven_inv" );
				if( esMMQServicioFarmaceutico($art['cod']) )
					$wcodcon = consultarAliasPorAplicacion( $conex, $wemp_pmla, "concepto_materiales_mueven_inv" );
				
				$wnomcon = consultarNombreConceptos( $conex, $wcliame, $wcodcon );
				
				$wexidev = 0;
				
				$wcantidad = $art['can'];
				
				$wfecha=date("Y-m-d");		
				$whora = date("H:i:s");
				
				//Reemplazo las variables necesarias para la función validar_y_grabar_cargo
				$auxWbasedato = $wbasedato;
				$wbasedato = $wcliame;
				$wuse = $wusuario;
				
				//$dosProc = datos_desde_procedimiento(codigoArticulo, codigoConcepto, wccogra    , ccoActualPac, wcodemp , wfeccar, '', '*', 'on', false, '', fecha  , hora  , '*', '*');
				$datosProc = datos_desde_procedimiento( $art['cod']  , $wcodcon      , $cCentroCosto, $pac['sac'] , $wcodemp, $wfecha, '', '*', 'on', false, '', $wfecha, $whora, '*', '*');
				
				$wvaltar = $datosProc[ 'wvaltar' ];
				
				$wdevol = 'off';
				if( $tipTrans != 'C' )
					$wdevol  = 'on';
				
				$datos=array();
				$datos['whistoria']		=$pac['his']; // $whistoria;
				$datos['wing']			=$pac['ing']; // $wing;
				$datos['wno1']			=$infoPacienteCargos['Pacno1']; // $wno1;
				$datos['wno2']			=$infoPacienteCargos['Pacno2']; // $wno2;
				$datos['wap1']			=$infoPacienteCargos['Pacap1'];
				$datos['wap2']			=$infoPacienteCargos['Pacap2'];
				$datos['wdoc']			=$pac['doc']; // $wdoc;
				$datos['wcodemp']		=$wcodemp;	//				--> cliame_000024
				$datos['wnomemp']		=$wnomemp;	//			--> cliame_000024
				$datos['tipoEmpresa']	=$tipoEmpresa;	//			--> cliame_000024
				$datos['nitEmpresa']	=$nitEmpresa;	//			--> cliame_000024
				$datos['tipoPaciente']	=$tipoPaciente;	//		--> cliame_000101 Ingcla
				$datos['tipoIngreso']	=$tipoIngreso;	//		--> cliame_000101 Ingtin
				$datos['wser']			=$wser;			//		--> cliame_000101 Ingsei
				$datos['wfecing']		=$wfecing;		//		--> cliame_000101 Ingfei
				$datos['wtar']			=$wtar;			//		--> cliame_000024
				$datos['wcodcon']		=$wcodcon;		//		--> Codigo del concepto (0626 = materiales, 0616 = medicamentos)
				$datos['wnomcon']		=$wnomcon;		//		--> Nombre del concepto Cliame 200
				$datos['wprocod']		=$art['cod']; // $wprocod;				--> Codigo del articulo o del medicamento
				$datos['wpronom']		=$art['nom'];// $wpronom;				--> Nombre del articulo Artcom
				$datos['wcodter']		=''; // $wcodter;				--> ''
				$datos['wnomter']		=''; //$wnomter;				--> ''
				$datos['wporter']		=''; // $wporter;				--> ''
				$datos['grupoMedico']	=''; // $grupoMedico;			--> ''
				$datos['wterunix']		=''; // $wterunix;				--> ''
				$datos['wcantidad']		=$wcantidad; //$wcantidad;			--> cantidad
				$datos['wvaltar']		=$wvaltar;	//			--> valor PENDIENTE FUNCION
				$datos['wrecexc']		='R'; // $wrecexc;				--> 'R'
				$datos['wfacturable']	='S'; // $wfacturable;			--> 'S'
				$datos['wcco']			=$cCentroCosto;	// $wcco;					--> Centro de costos graba
				$datos['wccogra']		=$cCentroCosto;// $wccogra;				--> cco paciente
				$datos['wfeccar']		=$wfecha; // $wfeccar;				--> Fecha del cargo
				$datos['whora_cargo']	=$whora; // $whora_cargo.':00';	-->	Hora del cargo
				$datos['wconinv']		='on'; //$wconinv;				--> 'on'
				$datos['wconabo']		=''; //$wconabo;				--> ''
				$datos['wdevol']		=$wdevol; // $wdevol;				--> 'off'
				$datos['waprovecha']	='off'; // $waprovecha;			--> 'off'
				$datos['wconmvto']		=''; //$wconmvto;				--> ''
				//$datos['wexiste']		=$wexiste;				--> cantidad existente PENDIENTE FUNCION
				$datos['wexiste']		=$datosProc[ 'wexiste' ];	//				--> cantidad existente PENDIENTE FUNCION
				$datos['wbod']			='off'; //$wbod;					--> 'off'
				$datos['wconser']		='H'; //$wconser;				--> 'H'
				//$datos['wtipfac']		=$wtipfac;				--> tipo facturacion PENDIENTE FUNCION
				$datos['wtipfac']		="CODIGO";	//			--> tipo facturacion PENDIENTE FUNCION
				$datos['wexidev']		=$wexidev;	//			--> 0 
				$datos['wfecha']		=$wfecha;	//				--> fecha act
				$datos['whora']			=$whora;	//			--> hora act
				$datos['nomCajero']		=''; //$nomCajero;			--> ''
				$datos['cobraHonorarios']		= ''; // $cobraHonorarios;			--> ''
				$datos['wespecialidad']			= '*';
				$datos['wgraba_varios_terceros']= ''; // $wgraba_varios_terceros;		''
				$datos['wcodcedula']			= ''; // $wcodcedula;					''
				$datos['estaEnTurno']			= ''; // $estaEnTurno;					''
				$datos['tipoCuadroTurno']		= ''; // $tipoCuadroTurno;				''
				$datos['ccoActualPac']			= $pac['sac']; //$ccoActualPac;				--> Centro de costos actual del paciente	
				$datos['codHomologar']			= ''; // $codHomologar;				--> ''	
				$datos['validarCondicMedic']	= true;	//						--> FALSE
				$datos['estadoMonitor']			= '';
				$datos['respuesta_array']			= 'on';
				$datos['numCargoInv']			= $numCargoInv;
				$datos['linCargoInv']			= $linCargoInv;
				
				//Esto es nuevo
				$datos['desde_CargosPDA']			= $desde_CargosPDA;

				//$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
				$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');

				// --> Si la empresa es particular esto se graba como excedente
				if($wcodemp == $codEmpParticular)
					$datos['wrecexc'] = 'R';	//Septiembre 11 de 2017

				// --> Valor excedente
				if($datos['wrecexc'] == 'E')
					$datos['wvaltarExce'] = round($wcantidad*$wvaltar);
				// --> Valor reconocido
				else
					$datos['wvaltarReco'] = round($wcantidad*$wvaltar);
				
				//Llamo la función de cargos de CARGOS DE ERP
				$respuesta = validar_y_grabar_cargo($datos, false);
				
				
				//echo "<h1>"; var_dump( $respuesta ); echo "</h1>";
				//Dejo las variables como estaban
				$wbasedato = $auxWbasedato;
			}
			//else{ echo "<h1>empresa</h1>" ;}
		}
		//else{ echo "<h1>ingreso cliame</h1>" ;}
	}
	//else{ echo "<h1>ingreso movhos</h1>" ;}
	
}

/**
 * Se realiza llamado de factura inteligente.
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: object
 */
function llamarFacturacionInteligente($pac, $cCentroCosto, $sCodigo, $sNombre, $dCantidad, $tipTrans, $numCargoInv = '', $linCargoInv = '')
{
	global $wemp_pmla;
	global $conex;

	//Obtengo el alias por aplicación y defino parámetros
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$pac['sac'] = consultarCcoPaciente($conex, $pac['his'], $pac['ing']);

	//Llamo facturación inteligente
	$artFactInteligente = array();
	// $artFactInteligente['cod'] = $presen[$i][$j]['cod'];
	// $artFactInteligente['nom'] = $row1[0];
	// $artFactInteligente['can'] = $can;
	
	$artFactInteligente['cod'] = $sCodigo;
	$artFactInteligente['nom'] = $sNombre;
	$artFactInteligente['can'] = $dCantidad;
	CargarCargosErp($conex, $pac, $wmovhos, $wcliame, $artFactInteligente, $tipTrans, $numCargoInv, $linCargoInv, $cCentroCosto);

	$aResultado = new stdClass();
	$aResultado->exito = true;
	$aResultado->mensaje = '';

	//Fin facturación inteligente
	return $aResultado;
}

/**
 * Se obtiene el nombre del paciente
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: array
 */
function consultarNombresPaciente( $conex, $his, $wemp_pmla ){

	$val = false;

	$sql = "SELECT *
			  FROM root_000036, root_000037
			 WHERE orihis = '".$his."'
			   AND pacced = oriced
			   AND pactid = oritid
			   AND oriori = '".$wemp_pmla."'
		";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}

/**
 * Consulta el nombre del concepto de acuerdo a su codigo
 * @by: sebastian.nevado
 * @date: 2021-06-11
 * @return: array
 */
function consultarNombreConceptos( $conex, $wcliame, $con ){

	$val = false;

	$sql = "SELECT *
			  FROM ".$wcliame."_000200
			 WHERE Grucod = '".$con."'
		";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Grudes' ];
	}
	
	return $val;
}

/**
 * Consulta el nombre del concepto de acuerdo a su codigo
 * @by: sebastian.nevado
 * @date: 2021-06-22
 * @return: array
 */
function consultarCcoPaciente( $conex, $sHistoria, $sIngreso ){
	
	global $wemp_pmla;
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$sQuery = "SELECT Ubisac
			FROM ".$wmovhos."_000018 
			WHERE Ubihis = ? AND Ubiing = ?
			ORDER BY id DESC
			LIMIT 1";
	
	//Preparo y envío los parámetros
	$sentencia = mysqli_prepare($conex, $sQuery);
	mysqli_stmt_bind_param($sentencia, "ss", $sHistoria, $sIngreso );
	mysqli_stmt_execute($sentencia);

	mysqli_stmt_bind_result($sentencia, $iCCo);
	mysqli_stmt_fetch($sentencia);
	
	$bResultado = isset($iCCo) ? $iCCo : null;

	return $bResultado;
}

/**
 * Dice si un articulo es Material Medico Quirurgico por medio de servicio farmacéutico
 *
 * @param $art
 * @return unknown_type
 * @author: sebastian.nevado
 * @date: 2021/06/25
 *
 * Nota: SE considera material medico quirurgico si el grupo del
 * articulo no se encuentra en la taba 66 o pertenezca al grupo E00 o V00.
 * Ya no se considera MMQ los articulos del grupo V00
 */
function esMMQServicioFarmaceutico( $art ){

	global $conex;
	global $bd;

	$esmmq = false;

	$sql = "SELECT
				artcom, artgen, artgru, melgru, meltip
			FROM
				{$bd}_000026 LEFT OUTER JOIN {$bd}_000066
				ON melgru = SUBSTRING_INDEX( artgru, '-', 1 )
			WHERE
				artcod = '$art'
			";

	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){
		if( (empty( $rows['melgru'] ) || $rows['melgru'] == 'E00' ) && !empty($rows['artcom']) ){
			$esmmq = true;
		}
		else{
			$esmmq = false;
		}
	}

	return $esmmq;
}

?>
</body>
</html>
