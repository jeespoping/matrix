<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo4{color:#FFFFFF;background:green;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo5{color:#FFFFFF;background:purple;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>
     <script type="text/javascript">
     function redireccionar( url ){
     	window.location = url;	
      }
     
     function enter()
     {
     	document.producto.accion.value=1;
     	document.producto.submit();
     }

     function enter1()
     {
     	document.producto.lote.value='';
     	document.producto.submit();
     }

     function enter2(i)
     {
     	document.producto.eliminar.value=i;
     	document.producto.submit();
     }

     function enter3()
     {
     	document.producto.realizar.value='modificar';
     	document.producto.submit();
     }

     function enter4()
     {
     	document.producto.realizar.value='desactivar';
     	document.producto.submit();
     }

     function enters(valor)
     {
     	document.producto.estado.value='inicio';
     	document.producto.submit();
     }

     function enter7()
     {
     	document.producto2.submit();
     }

     function enter8()
     {
     	document.producto4.submit();
     }

     function enter9()
     {
     	document.producto.grabar.value='1';
     	document.producto.submit();
     }

     function hacerFoco()
     {
        try{
	     	if (document.producto.elements[1].value=='')
	     	{
	     		document.producto.elements[1].focus();
	     	}
	     	else
	     	{
	     		document.producto.elements[13].focus();
	     	}
        }
        catch(e){}

     	try{
         	document.producto.elements[ 'user' ].focus();
     	}
     	catch( e ){
     	}

     	try{
         	document.producto.elements[ 'historia' ].focus();
     	}
     	catch( e ){
     	}

     	try{
         	document.producto.elements[ 'parbus2' ].focus();
     	}
     	catch( e ){
     	}

     	try{
         	document.producto.elements[ 'txCanMMQ' ].focus();
     	}
     	catch( e ){
     	}

     	try{
     		document.producto.elements[ 'cantidad' ].focus();
     	}
     	catch( e ){
     	}
     }

     function recargar(){
         setTimeout( "document.producto.submit();", 2000 );
     }

     function cargarMultiMMQ( url ){

    	 if( document.getElementById( 'txCanMMQ' ).value*1 > 0 ){
    		document.producto.submit();
    		window.open( url+"&canMMQ="+document.getElementById( 'txCanMMQ' ).value );
    	 }
    	 else{
        	 alert( "La cantidad a ingresar debe ser mayor a 0" );
    	 }
         
     }

     function buscarPDA(){

         if( document.producto.elements[ "parbus2" ].value != ""  ){
        	 document.producto.submit();
         }
         else{
             alert( "Ingrese el código del articulo a buscar" );
         }
     }

    </script>
<title>CARGOS CENTRAL DE MEZCLAS</title>
</head>
<body onload="hacerFoco()">
<?php
include_once("conex.php");
//actualizacion: Agosto 09 de 2018	(Edwin)		Se corrige query en la función consultarLotes
//actualizacion: Noviembre 02 de 2017	(Edwin)		Entre el tiempo de dispensación por defecto de CM(10 horas al momento de la publicación) y el tiempo de dispensacion de cco del paciente se toma la de mayor valor.
//													Ejemplo: UCI tiene 8 horas y CM tiene 10 horas de tiempo de dispensación, se toma la de CM por ser mayor
//actualizacion: Septiembre 19 de 2017 (Edwin MG)  Se agregan variable $antHoras en lugares varios en la función Articulosxpaciente para corregir error de calculos
//actualizacion: Septiembre 14 de 2017 (Edwin MG)  Se agrega parametro para manejar los articulos con condición tipo a necesidad como articulos sin condición de tipo a necesidad para los pacientes
//											   	   que se encuentran ubicados en un cco con el parametros ciclos de 24 horas activo(ccoc24=on en movhos_000011)
//actualizacion: Septiembre 12 de 2017 (Jessica Madrid) Se agrega validacion en la funcion esKE para evitar que cuando el paciente tenga ordenes necesite confirmacion del kardex, ya que no se actualizaba la regleta.
//actualizacion: Diciembre 28 de 2016 	(Edwin MG) Se valida que no se puede dispensar articulos NO POS si la cantidad autorizada de CTC llego a su limite. (cambios varios)
//actualizacion: Febrero 09 de 2015 	(Edwin MG) Se desactiva la petición de camillero
//actualizacion: Septiembre 24 de 2013 	(Edwin MG)	Se crea campo nuevo en tabla de condiciones que permite cargar medicamentos a necesidad como si no lo fueran. Esto para poder
//													cargar medicamentos a necesidad suficiente según el tiempo de dispensación y no con la regla actual que dice: lo suficiente para una dosis.
//actualizacion: Septiembre 12 de 2013	(Edwin MG)	Se realizan cambios varios para que soporte dispensación a mas de 24 horas
//actualizacion: Agosto 14 de 2013	(Edwin MG)	Se valida que halla conexión unix en inventario desde matrix, si no hay conexión
//												con unix se activa la contigencia de dispensación.
//actualizacion: Junio 26 de 2013	(Mario Cadavid)	Se agrega la condición !esUrgenciasIncCM($row['Ubisan']
//									en la validación si está en proceso de traslado, de modo que si el centro de costo anterior 
//									es urgecias no valide si el paciente está en proceso de traslado 
//actualizacion: Junio 18 de 2013	(Mario Cadavid)	Se agrega la condición if(!esUrgenciasIncCM($row['Ubisac']) 
//									en la validación si está en proceso de traslado, de modo que si está en urgecias
//									no valide si el paciente está en proceso de traslado 
//actualizacion: Abril 1 de 2013	(Edwin MG)	Agrego campo Kadido en la consulta principal de la funcion articulosxpaciente.
//actualizacion: Marzo 18 de 2013	(Edwin MG)	Si un paciente esta en urgencia se puede cargar cualquier producto al paciente.
//												Si un pciente tuvo alta definitiva y el último servicio en que estuvo fue urgencias se puede cargar
//												cualquier producto siempre y cuando no halla pasado mas x horas (tiempo configurable en root_000051)
//												desde que se le dio alta definitiva.
//actualizacion: Marzo 11 de 2013		(Edwin MG)	Se corrige tiempo de dispensación para los pisos ya que no era correcto el tiempo que se mostraba.
//actualizacion: Agosto 28 de 2012		(Edwin MG)	Se deja dispensar de acuerdo al tiempo de dispensación  según el cco donde se encuentre el paciente si lo tiene.
//actualizacion: Marzo 14 de 2012		(Edwin MG)	Se agrega funcion crearKardexAutomaticamente.
//actualizacion: Noviembre 8 de 2011(Edwin M)	Al registrar una aplicacion de articulo, la fraccion y dosis corresponden a la tabla de fracciones por articulo (movhos_000059),
//												esto se hace con la funcion actualizandoAplicacionFraccion que se encuentra en cargosinc.php en el include, y se desactiva la funcion
//												actualizandoAplicacion por codigo.
//actualizacion 2011-07-25 Se cambia la dispensacion para cada ciertas horas, segun el parametro DISPENSACION de la tabla root_000051.  Segun este tiempo, el programa pide para cada paciente
//  				  	   una cantidad x del medicamento que cubre las dosis a aplicar al paciente durante ese rango de tiempo.  Para este modificacion se cambia la funcion ArticulosXPaciente
//  				   	   y registrarArticulosKE.  Nota: Tener en cuenta que este programa es basado en el programa cenpro/procesos/cargos.php y cenpro/procesos/cargo.php, se crea archivo include
//					   	   cargos.inc.php, el cual contiene funciones que comparten ambos programas para esta nueva dispensacion.
//actualizacion: Mayo 11 de 2011.	Si el paciente tiene kardex y el articulo se aplica, se registra la cantidad de dosis y unidad de dosis al registro correspondiente 
//actualizacion: 	2010-09-13 Se da la opcion de reemplazar el codigo generico NUXXXX por el articulo que se va a cargar
//					ya sea de quimioterapia, dosis adaptada o nutriciones. Se impide que los articulos de quimioterapia y dosis adaptadas sean
//					nka si el centro de costos donde se encuentra el usuario no meneja kardex.
//actualizacion: 2010-07-30 Se deja cargar al carro cualquier medicamento que no necesita estar en el kardex
//actualizacion: 2010-07-22 Se crea interfaz para PDA.  Para la PDA nose puede cargar productos no codificados, los cuales 
//							actualmente son: Dosis adaptadas(DA), Nutriciones Parenterales (Nu) y Quimioterapia (QT).
//actualizacion: 2008-01-21 se corrige la busqueda de articulos por codigo de central
//actualizacion: 2007-11-06 se crea la opcion del carro y se cargan automaticamente los codificados
//actualizacion: 2010-05-24 Se modifica programa para que pueda cargar Material Médico Quirugico por una cantidad ingresada por el usuario
/**
* ========================================================FUNCIONES==========================================================================
*/
// ----------------------------------------------------------funciones de persitencia------------------------------------------------

function esCcoCiclos24Horas( $conex, $wbasedato, $wcco ){
	
	$val = false;
	
	$sql = "SELECT *
			FROM ".$wbasedato."_000011
		   WHERE ccocod = '".$wcco."'
		     AND Ccoc24 = 'on'
		   ";

	$res = mysql_query( $sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 )
		$val = true;
	
	return $val;
}

/****************************************************************************************************************************************
 * Consulta el total de aplicaciones que ha tenido un medicamento
 ****************************************************************************************************************************************/
function consultarTotalAplicacionesEfectivas( $conex, $wbasedato, $his, $ing, $articulo, $fechorInicial, $fechorFinal, $ido = false, $fechorTraslado = 0 ){

	$val = 0;

	if( !$ido ){
		$sql = "SELECT
					Aplcan,Apldos,Aplufr,UNIX_TIMESTAMP( CONCAT( Aplfec,' ', SUBSTRING( Aplron, 1, 2 ) ) ) as Aplfec
				FROM
					{$wbasedato}_000015
				WHERE
					aplhis = '$his'
					AND apling = '$ing'
					AND aplart = '$articulo'
					AND aplfec >= '".date( "Y-m-d", $fechorInicial )."'
					AND aplido != ''
					AND aplest = 'on'
				";
	}
	else{
		$sql = "SELECT
					Aplcan,Apldos,Aplufr,UNIX_TIMESTAMP( CONCAT( Aplfec,' ', SUBSTRING( Aplron, 1, 2 ) ) ) as Aplfec
				FROM
					{$wbasedato}_000015
				WHERE
					aplhis = '$his'
					AND apling = '$ing'
					AND aplart = '$articulo'
					AND aplido = '$ido'
					AND aplest = 'on'
				";
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			if( $rows['Aplfec'] >= 0 && $rows['Aplfec'] > $fechorTraslado && $rows['Aplfec'] <= $fechorFinal ){
				$val += 1;	//Suma de articulos en unidades enteras
			}
		}
	}
	
	return $val;
}

/************************************************************************************
 * Devuelve el valor nuevo para una dosis máxima para un medicamento
 *
 * $rowsart		es un array asociativo con los campos necesarios para
 *				realizar la operación. Estos son:
 *				Kadfin: Fecha de inicio
 *				Kadhin: Fecha de inicio
 *				Kadido: Id original
 *				Kaddma:	Dosis maxima
 *				Perequ:	Frecuencia del articulo
 * $horAnt		Indica con cuantas horas antes se puede aplicar un medicamento
 ***********************************************************************************/
function recalcularDosisMaxima( $conex, $wbasedato, $his, $ing, $art, $rowsart, $horAnt, $esANecesidad ){
	
	$fechorInicial 	= strtotime( $rowsart[ 'Kadfin' ]." ".$rowsart[ 'Kadhin' ] );
	$frecuencia 	= $rowsart[ 'Perequ' ]*3600;
	$dosisMaxima 	= $rowsart[ 'Kaddma' ];
	$ido			= $rowsart[ 'Kadido' ];
	
	if( !$esANecesidad )
		$fechorFinal 	= time()-$horAnt*3600;
	else
		$fechorFinal 	= time()-2*3600*0;

	//Consulta el total de aplicaciones efectivas realizadas
	$aplicacionesEfectivas = consultarTotalAplicacionesEfectivas( $conex, $wbasedato, $his, $ing, $art, $fechorInicial, $fechorFinal, $ido, 0 );
	
	$dosisFaltantes = $dosisMaxima-$aplicacionesEfectivas;

	$aplicacionesTotales = 0;
	if( !$esANecesidad ){
		//Nunca las dosis faltantes pueden ser menores a 0
		if( $dosisFaltantes < 0 )
			$dosisFaltantes = 0;
		
		//Calculo cuantas aplicaciones totales hay
		$aplicacionesTotales = 0;
		
		if( $fechorFinal>=$fechorInicial )
			$aplicacionesTotales = floor( ( $fechorFinal-$fechorInicial )/$frecuencia )+1;
	}
	
	$aplicacionesTotales += $dosisFaltantes;
	
	return $aplicacionesTotales;
}

/**
 * 
 * @param $idKardex
 * @param $cco
 * @param $art
 * @param $num
 * @param $lin
 * @return unknown_type
 * 
 * Mayo 11 de 2011
 */
function actualizandoAplicacion( $idKardex, $cco, $art, $num, $lin ){
	return;	//Noviembre 8 de 2011
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
function ArticulosXPacienteSinSaldo( $his, $ing, $pda = false ){
	
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
		
		if( !$pda ){
			$articulos[$i]['cod'] = $rows['cod'];
			$articulos[$i]['nom'] = $rows['nom'];
			$articulos[$i]['cdi'] = $rows['cdi'];
			$articulos[$i]['dis'] = $rows['dis'];
			$articulos[$i]['sal'] = $rows['cdi'] - $rows['dis'];

			if( $articulos[$i]['sal'] > 0 ){
				return false;
			}
		}
		else{
			
			if( !esProductoNoCodificado( $rows['cod'] ) ){
				$articulos[$i]['cod'] = $rows['cod'];
				$articulos[$i]['nom'] = $rows['nom'];
				$articulos[$i]['cdi'] = $rows['cdi'];
				$articulos[$i]['dis'] = $rows['dis'];
				$articulos[$i]['sal'] = $rows['cdi'] - $rows['dis'];

				if( $articulos[$i]['sal'] > 0 ){
					return false;
				}
			}
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
	
	$bdCencam = "cencam";
	
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
	
	global $conex;
	global $bdCencam;
	
	$bdCencam = "cencam";
	
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
	
	$val = '';
	
	$sql = "SELECT
				Nombre
			FROM
				cencam_000004
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
		
		$val = crearPeticionCamillero( nombreCcoCentralCamilleros( $origen ), $motivo, "<b>".$hab."</b><br>".$paciente, $nomCcoDestino, $solicita, $nomCcoDestino );
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
    global $servicio;
    
    $nka = false;

    //Buscando si el articulo es Material Medico Quirurgico
    //MMQ
	$sql = "SELECT
				tipnka, tipcod, tipdis
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
		
		//No se permite que los articulos de quimioterapia o Dosis adaptadas sean nka, si en el piso no se esta usando el kardex electrónico por enfermeras
		if( !saltarValidacionReemplazo( $servicio ) && ( $row['tipcod'] == '03' || $row['tipcod'] == '11' ) ){
			$nka = false;
		}
				
//		$nka = true;			
	}
	else{
		$nka = false;
	}
	
	return $nka;
}


function validarUsuario( $usuario ){
	
	global $conex;
    global $wbasedato;
    
    //Buscando si el articulo es Material Medico Quirurgico
    //MMQ
	$sql = "SELECT
				*
			FROM
				root_000025, usuarios
			WHERE
				empleado = '$usuario' 
				AND codigo = empleado
				AND activo = 'A' 
			";
				
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numros = mysql_num_rows( $res );
	
	if( $numros > 0 ){
		return true;			
	}
	else{
		return false;
	}
}

function pedirUsuario(){
	
	global $pda;
	
	if( isset($pda) && $pda == 'on' ){
		echo "<table align='center' width='270'>";
		echo "<tr class='texto1'>";
		echo "<td>";
		echo "Usuario: <INPUT type='text' name='user' value=''>";
		echo "</td>";
		echo "</tr>";
		echo "<tr class='texto2'><td align='center'><INPUT type='submit' value='Aceptar'></td></tr>";
		echo "</table>";
	}
}

/**
 * Determina
 * 
 * @param $lista
 * @param $codigo
 * @return unknown_type
 */
function enLista( $lista, $codigo ){
	
	for( $i = 0; $i < count($lista); $i++ ){
		
		if( $lista[$i]['cod'] == $codigo ){
			if( $i < 3 ){
				return $i;
			}
			else{
				return -1;
			}
		}
	}
	
	return -1;
}

/**
 * Determinar si un articulo es un prodcuto no codificado
 * 
 * @param $codigo	Codigod del articulo
 * @return unknown_type
 */
function esProductoNoCodificado( $codigo ){
	
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
				artcod = '$codigo' 
				AND arttip = tipcod
				AND tippro = 'on'
				AND tipcdo = 'off' 
			";
				
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
			$mmq = true;			
	}
	else{
		$mmq = false;
	}
	
	return $mmq;
	
}

/**
 * Lista de articulos
 * @param $listart
 * @return unknown_type
 */
function pintarListaPDA( $listart ){
	
	$listart1 = Array();
	
	echo "<table align='center' style='width:95%;'>";
	
	for( $i = 0; $i < count($listart) && count($listart1) < 3; $i++ ){
		
		if( $listart[$i]['sal'] > 0 && !esProductoNoCodificado( $listart[$i]['cod'] ) ){
			$listart1[ count($listart1) ] = $listart[$i];
		}
		
	}

	//$listart1 = $listart;
	
	for( $i = 0; $i < 3; $i++ ){

		if( !empty( $listart1[$i] ) ){
			
			$class='texto3';
			
			if( $listart1[$i]['sal'] > 0 ){
				echo "<tr class='".$class."' style='font-size:9pt'>
					<td style='background-color:#6694E3' align='left' width='80%' title='{$listart1[$i]['cod']} (T:{$listart1[$i]['tot']},G:{$listart1[$i]['gra']})'>".substr( $listart1[$i]['nom'], 0, 22 )."</td>
					<td style='background-color:#6694E3' align='center' width='20%'>{$listart1[$i]['sal']}</td>
				</tr>";
			}
			else{
				echo "<tr  class='" . $class . "' >
					<td style='background-color:#6694E3' align='center'>&nbsp;</td>
					<td style='background-color:#6694E3' align='center'>&nbsp;</td>
				</tr>";
			}
		}
		else{
			$class='texto3';
			
			echo "<tr  class='" . $class . "' >
				<td style='background-color:#6694E3' align='center'>&nbsp;</td>
				<td style='background-color:#6694E3' align='center'>&nbsp;</td>
			</tr>";
		}
	}
	echo "</table>";
	
	return $listart1;
}


function pintarCargosPDA( $insumos, $unidades, $lotes, $tipo, $escogidos, $accion, $cco, $historia, $ingreso, $servicio, $confm, $confc, $carro ){

	global $cantidad;
	global $nombre;
	global $habitacion;
	global $user;
	global $wusuario;
	global $grabo;
	global $mart;
	global $numMovAnt;
	
	global $solCamillero;
	
	global $tmpDispensacion;
	
	if( !isset($grabo) ){
		$grabo = false;
	}
	
	if( empty($insumos[0]['lot']) ) {
		$insumos[0]['lot']='';		
	}
	
	if( empty($insumos[0]['cdo']) ) {
		$insumos[0]['cdo']='';		
	}
	
	if( empty($insumos[0]['cod']) ) {
		$insumos[0]['cod']='';		
	}
	
	if( empty($unidades[0]) ){
		$unidades[0] = '';
	}
	
	//Precondiciones de grabado pare el KE
	$packe = array();
	$packe['his'] = $historia;
	$packe['ing'] = $ingreso;
	condicionesKE( $packe, $insumos[0]['cod'] );
		
	/*******************************************************
	 * PACIENTE CON KARDEX ELECTRONICO
	 *******************************************************/
//	echo "<form name='producto' action='cargos.php' method=post>";
	
	if( $packe['ke'] ){
		
		//Indica cuantos fueron los movimientos anteriores realizados
		echo "<INPUT type='hidden' value='0' name='numMovAnt'>";
		
		//Febrero 06 de 2015. Se desactiva la peticion de camillero a solicitud de Beatriz.
		if( false && ( count($mart) > $numMovAnt ) && ArticulosXPacienteSinSaldo( $historia, $ingreso, true ) && @$solCamillero != 'on' ){
			
			$exp = explode( "-", $cco );

			$nomCcoDestino = nombreCcoCentralCamilleros( $servicio );
			$motivo = 'DESPACHO DE MEDICAMENTOS';
			crearPeticionCamillero( nombreCcoCentralCamilleros( $exp[0] ), $motivo, "<b>Hab: ".$habitacion."</b><br>".$nombre, $nomCcoDestino, $wusuario, $nomCcoDestino, buscarCodigoNombreCamillero() );

//			echo "<INPUT type='hidden' name='solCamillero' value='on'>";
			
			$solCamillero = 'on';
		}
		
		echo "<table width='270' border='0' align='center' style='font-family:verdana; font-size:10pt'>";
		
		echo "<tr>";
		echo "<td colspan='2'>";
		echo $cco;
		echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan='2'>";
		echo "USUARIO:".$wusuario;
		echo "</td>";
		echo "</tr>";
		$rondaActual = ceil( date( "H" )/$tmpDispensacion )*$tmpDispensacion;
		
		if( $rondaActual < 10 ){
			$rondaActual = "0".$rondaActual.":00:00";
		}
		else{
			if( $rondaActual == 24 ){
				$rondaActual = "00:00:00";
			}
			else{
				$rondaActual = $rondaActual.":00:00";
			}
		}
		
		echo "<tr><td class='tituloSup' align='center'><b>RONDA: $rondaActual</b>";
		echo "<tr>";
		echo "<td colspan='2' class='titulo1'>";
		echo "<center><b>$nombre ($habitacion)</b></center>";
		echo "</td>";
		echo "</tr>";
		
		if( $solCamillero == 'on' ){
			echo "<tr>";
			echo "<td class='titulo4' colspan='2'>SE HA SOLICITADO CAMILLERO</td>";
			echo "<INPUT type='hidden' name='solCamillero' value='on'>";
			echo "</tr>";
		}
		
		echo "<tr class='texto1'>";
		echo "<td align='center' colspan='2' style='font-size:9pt'>";
		echo "Articulos a dispendar";
		echo "</td>";
		echo "</tr>";
		
		echo "<tr class='texto1'>";
		echo "<td align='center' colspan='2'>";
		
		$listart = ArticulosXPaciente( $historia, $ingreso );
	
		$listaDispensacion = pintarListaPDA( $listart );
		
		echo "</td>";
		echo "</tr>";
		
		echo "<tr class='titulo3'>";
		echo "<td align='center' colspan='2'>";
		echo "CODIGO: <INPUT name='parbus2' value=''  style='width:35%'>";
//		echo "&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar'  class='texto5'	>";
		echo "&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar'  class='texto5' onclick='javascript: buscarPDA();'>";
		echo "</td>";
		echo "</tr>";
		
		?>
			<script type="text/javascript">
				document.producto.elements[ 'parbus2' ].focus();
			</script>
		<?php
		
		
		echo "<tr class='titulo3'>";
		echo "<td align='center' colspan='2'>";
		echo "Art: <select name='insumo' class='texto5' onchange='enter1()' style='width:75%'>";
		
		if ($insumos != '' || !empty($insumos) )
		{
			for ($i = 0;$i < count($insumos);$i++)
			{
				echo "<option value='" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "-" . $insumos[$i]['gen'] . "-" . $insumos[$i]['pre'] . "-" . $insumos[$i]['lot'] . "-" . $insumos[$i]['est'] . "'>" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "</option>";
			}
			echo "<input type='hidden' name='insumos[0]['lot']' value='" . $insumos[0]['lot'] . "'></td>";
			echo "<input type='hidden' name='insumos[0]['cdo']' value='" . $insumos[0]['cdo'] . "'></td>";
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select>";
		echo "</td></tr>";
		
		//Pide la cantidad a cargar del articulo
		echo "<tr class='titulo3'>";
		if( $insumos[0]['cdo']=='on' && $accion == 'Cargo' && $packe['ke'] ){
			if( $cantidad > $packe['cfd'] ){
				echo "<td class='titulo3' colspan='1' align='center' width='50%'>Cant: <INPUT type='text' name='cantidad' value='{$packe['cfd']}' class='texto5' style='width:25%;'>";
				$cantidad = 0;
			}
			else{
				echo "<td class='titulo3' colspan='1' align='center' width='50%'>Cant: <INPUT type='text' name='cantidad' value='{$packe['cfd']}' class='texto5' style='width:25%;'>";
			}
			?>
			<script type="text/javascript">
				document.producto.elements[ 'cantidad' ].focus();
			</script>
			<?php 
		}
		else{
			$cantidad = 1; 
		}
		
		//Presentación
		if ($insumos[0]['lot'] == 'on')
		{
			echo "<td colspan='1' align='center'>Lote: <select name='lote' class='texto5'>";
			if (is_array($lotes))
			{
				for ($i = 0;$i < count($lotes);$i++)
				{
					echo "<option>" . $lotes[$i] . "</option>";
				}
			}
			else
			{
				echo "<option ></option>";
			}
			echo "</select></td>";
			$ind = 0;
			
		}
		else if (is_array($unidades))
		{
			echo "<input type='hidden' name='lote' value=''>";
			echo "<td colspan='1' align='center'>Pre: <select name='prese' class='texto5' onchange='enter1()' style='width:75%'>";
			if ($unidades[0] != '')
			{
				for ($i = 0;$i < count($unidades);$i++)
				{
					echo "<option>" . $unidades[$i] . "</option>";
				}
			}
			else
			{
				echo "<option ></option>";
			}
			echo "</select></td>";
			$ind = 0;
		}
		else
		{
			echo "<td colspan='1' align='center'>&nbsp;</td>";
		}
		
		echo "</tr>";
		
		
		if( $insumos[0]['cdo']=='on' && $accion == 'Cargo' && $packe['ke'] && enLista($listaDispensacion, $insumos[0]['cod'] ) > -1 ){
			echo "<tr align=center class='titulo3'>"; 
			echo "<td colspan='2'>"; 
			echo "<INPUT type='submit' name='sbGrabar' value='Grabar' class='texto5'>";
			echo "</td>"; 
			echo "</tr>";
		}
		
		/**************************************************************************************************************
		 * De aqui en adelante es la validadción de grabado
		 **************************************************************************************************************/
		if ( ($cantidad > 0 and $insumos != '' and ($lotes[0] != '' or $unidades[0] != '') ) )
		{ 
			if ($lotes[0] != '')
			{
				$var = $lotes[0];
			}
			else
			{
				$var = urlencode($unidades[0]);
			}
			
			if( $insumos[0]['cdo']=='on' )
			{	//Si es articulo codificado
				if ($accion == 'Cargo')
				{
					//Verificando precondiciones para grabar en el KE
					if( $packe['act'] || $packe['gra'] || $packe['con'] || !$packe['nut'] )
					{
						if( $packe['gra'] || $packe['nut'] || $packe[''] || $packe['mmq'] )
						{
							if( $packe['con'] || $packe['nut'] || $packe['mmq'] )
							{
								if( $packe['act'] || $packe['nut'] || $packe['mmq'] )
								{
									if( $packe['art'] || $packe['nut'] || $packe['mmq'] )
									{
										if( $packe['sal'] || $packe['nut'] || $packe['mmq'] )
										{
											for( $i = 0; $i < $cantidad; $i++ ){
												
												if( enLista($listaDispensacion, $insumos[0]['cod'] ) > -1 ){
													$grab=cargoCM($insumos[0]['cod'], $cco, $var, $historia, $ingreso, $servicio, $error, $carro);
												}
												
												if( $grab ){
													echo "<input type='hidden' name='grabo' value='on'></td>"; 
													$val = registrarArticuloKE( $insumos[0]['cod'], $historia, $ingreso, false, $idKardex, $error['dronum'], $error['drolin'] );
													
													
													/************************************************************************************************
													 * Mayo 11 de 2011
													 ************************************************************************************************/
													if( $val && !empty($idKardex) && !empty( $error['dronum'] ) && !empty( $error['dronum'] ) ){
														$explodeCco = explode( '-', $cco );
														actualizandoAplicacion( $idKardex, $explodeCco[0], $insumos[0]['cod'], $error['dronum'], $error['drolin'] );
													}
													/************************************************************************************************/
												}
												
												if( $grab && $val )
												{
													echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
													echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
												}
												else
												{ 
													if( !$val && $grab ){
														$error['ok'] = 'NO SE PUDO REGISTRAR EL ARTICULO';
													}
													echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
													echo "<input type='hidden' name='confc' value='red'></td>";
													
													break;
												}
											}
											
//											if( $val && ArticulosXPacienteSinSaldo( $historia, $ingreso, true ) && $solCamillero != 'on' ){
//
//												$nomCcoDestino = nombreCcoCentralCamilleros( $servicio );
//												$motivo = 'DESPACHO DE MEDICAMENTOS';
//												crearPeticionCamillero( nombreCcoCentralCamilleros( $cco['cod'] ), $motivo, "<b>Hab: ".$habitacion."</b><br>".$nombre, $nomCcoDestino, $wusuario, $nomCcoDestino, buscarCodigoNombreCamillero() );
//												
//												echo "<INPUT type='hidden' name='solCamillero' value='on'>";
//											}
										}
										else{
											$error['ok'] = 'EL ARTICULO YA FUE DISPENSADO';
											echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
											echo "<input type='hidden' name='confc' value='red'></td>";
										}
									}
									else{
										$error['ok'] = 'EL ARTICULO NO ESTA CARGADO AL PACIENTE';
										echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
										echo "<input type='hidden' name='confc' value='red'></td>";
									}
									
								}
								else{
									$error['ok'] = 'EL PACIENTE NO TIENE KE ACTUALIZADO';
									echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
									echo "<input type='hidden' name='confc' value='red'></td>";
								}
							}
							else
							{
								$error['ok'] = 'EL KARDEX ELECTRONICO NO HA SIDO CONFIRMADO';
								echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
								echo "<input type='hidden' name='confc' value='red'></td>";
							}
						}
						else
						{
							$error['ok'] = 'EL PACIENTE NO TIENE KARDEX ELECTRONICO GRABADO';
							echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
							echo "<input type='hidden' name='confc' value='red'></td>";
						}
					}
					else{
						$error['ok'] = 'NO SE LE HA GENERADO KARDEX ELECTRONICO RECIENTEMENTE';
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
				}
				else
				{
					$exp = explode('-', $cco);
					$ccs['cod']=$exp[0];
					$ccs['nom']=$exp[1];
	
					$art['cod']=$insumos[0]['cod'];
					$art['nom']=$insumos[0]['nom'];
					$art['can']=1;
	
					$pac['his']=$historia;
					$pac['ing']=$ingreso;
					$pac['sac']=$servicio;
					$grab=devolucionCM($ccs, $art, $pac, $error, $dronum, $drolin, $carro);
	
					descargarCarro2( $art['cod'], $art['cod'], $historia, $ingreso, $cco );
					
					if($grab)
					{
						$tag = false;
						if( $packe['ke'] ){
							$val = registrarArticuloKE( $art['cod'], $historia, $ingreso, $dev = true, '', '' );
							$tag = true;
						}
						
	//					echo "Lote: ".$insumos[0]['lot']; echo "No. Lote: ".$lotes[0]; exit;
						if( !$packe['ke'] || ($packe['ke'] && $val) ){
							echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
							echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
						}
						else if( $packe['ke'] && !$val && $tag && $packe['sal'] ){
							echo "<input type='hidden' name='confm' value='EL ARTICULO NO SE PUDO DESCONTAR DEL KARDEX ELECTRONICO'></td>";
							echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
						}
					}
					else
					{
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
				}
	
			?>
			<script>
				document . producto . insumo.options[document.producto.insumo.selectedIndex].value='';
				document . producto . submit();
            </script >
            <?php
			}
			else
			{
				if( esMMQ( $insumos[0]['cod'] ) && $accion == 'Cargo' ){
					if( true || !isset( $txCanMMQ ) || empty($txCanMMQ) ){
						if( $solCamillero != 'on' ){
							echo "<tr><td colspan='2' align='center' class='titulo3'><b>Cantidad a cargar: </b><INPUT type='text' name='txCanMMQ' id='txCanMMQ' class='texto5'></td></tr>";
							echo "<tr><td colspan='2' align='center' class='titulo3'><INPUT type='button' class='texto5' value='Cargar' onClick='cargarMultiMMQ( \"cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "&user=$user\" )'></td></tr>";
						}
						else{
							echo "<tr><td colspan='2' align='center' class='titulo3'><b>Cantidad a cargar: </b><INPUT type='text' name='txCanMMQ' id='txCanMMQ' class='texto5'></td></tr>";
							echo "<tr><td colspan='2' align='center' class='titulo3'><INPUT type='button' class='texto5' value='Cargar' onClick='cargarMultiMMQ( \"cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "&user=$user&solicitudCamillero=on\" )'></td></tr>";
						}
						?>
						<script type="text/javascript">
							document.producto.elements[ 'txCanMMQ' ].focus();
						</script>
						<?php 
					}
				}
				else{
					if( $accion == 'Cargo' )
					{
						if( $packe['nka'] || (!esProductoNoCodificado( $insumos[0]['cod'] ) && enLista($listaDispensacion, $insumos[0]['cod'] ) > -1 ) ){
							if( ( !$packe['ke'] || $packe['nut'] || $packe['mmq'] || $packe['nka'] || ($packe['ke'] && $packe['act'] && $packe['con'] && $packe['gra']) && $packe['art'] )){
								echo "<td class='texto2' colspan='2' align='left'><a id='txCargar' href='cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "&user=$user' target='_blank' onClick='javascript: recargar();'><font size='4'>CARGAR</a></td></tr>";
							}
						}
					}
					else
					{
						echo "<td class='texto2' colspan='2' align='left'><a href='devolucioncpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . " ' target='_blank'><font size='4'>DEVOLVER</a></td></tr>";
					}
				}
			}
		}
		else if ($confm!='')
		{
			echo "<td bgcolor='".$confc."' colspan='2' align='center'><font color='#003366'><b>".$confm."</b></font></td></tr>";
		}
		
		
		$articulosSinSaldo = ArticulosXPacienteSinSaldo( $historia, $ingreso );
		
//		$cco['cod'] ), $motivo, "<b>Hab: ".$hab."</b><br>".$nom, $nomCcoDestino, $usuario, $nomCcoDestino, buscarCodigoNombreCamillero() 
		
		echo "<tr class='titulo3'>";
		echo "<td colspan='2'>";
//		echo "......".$numMovAnt;
		if( /*(!$articulosSinSaldo && $grabo == 'on') ||*/ ( count($mart) > $numMovAnt && !$articulosSinSaldo ) && ( @$solCamillero != 'on' ) ){
			
			$exp = explode( "-", $cco );
			
			echo "<a href='./cargoscm.php?pda=on&user=$user&solicitarCamillero=on&his=$historia&ing=$ingreso&ccoCencam={$exp[0]}&hab=$habitacion&nom=$nombre&usu=$wusuario&des=$servicio'>Retornar</a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='./cargoscm.php?pda=on&solicitarCamillero=on&his=$historia&ing=$ingreso&solicitarCamillero=on&his=$historia&ing=$ingreso&ccoCencam={$exp[0]}&hab=$habitacion&nom=$nombre&usu=$wusuario&des=$servicio'>Reiniciar</a>";
		}
		else{
			echo "<a href='./cargoscm.php?pda=on&user=$user'>Retornar</a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='./cargoscm.php?pda=on'>Reiniciar</a>";
		}
		echo "</td>";
		
		echo "</tr>";
		
		echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
		echo "<input type='hidden' name='realizar' value='0'></td>";
		echo "<input type='hidden' name='eliminar' value='0'></td>";
		
		echo "<INPUT type='hidden' name='forbus2' value='Codigo'>";
		
		echo "</table>";
		echo "</br></form>";

		return;
	}
}



/**
 * 
 * @param $cod
 * @param $codari
 * @param $his
 * @param $ing
 * @param $cco
 * @return unknown_type
 */
function descargarCarro2( $cod, $codari, $his, $ing, $cco ){
	
	global $conex;
	global $bd;
	
	$expcco = explode( "-", $cco );
	
	//se hace la devolucion del carro
	//if($tipTrans == 'Anulado')//se debe poner !=C
	$sql = "SELECT
				ccofca
			FROM
				movhos_000011
			WHERE
				ccocod = '".$expcco[0]."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$rows = mysql_fetch_array( $res );
	
	
	//aca voy a consultar cuantos elementos estan en el carro y son de la central
	$q = "SELECT Fdenum "
	."        FROM ".$bd."_000002, ".$bd."_000003 "
	."       WHERE Fenhis=".$his." "
	."         AND Fening=".$ing." "
	."         AND Fencco='".$expcco[0]."' "
	."         AND Fenfue='{$rows[0]}' "
	."         AND Fdenum=Fennum "
	."         AND Fdeari='".$codari."' "
	."         AND Fdeart='".$cod."' "
	."         AND Fdedis='on' "
	."         AND Fdeest='on' ";

	$err1 = mysql_query($q,$conex);
	echo mysql_error();
	$num1 = mysql_num_rows($err1);
	
	if($num1 >0)
	{
		$row1=mysql_fetch_array($err1);
		if($num1 >0)
		{
			$q = " UPDATE ".$bd."_000003 "
			."    SET fdedis = 'off', "
			."        fdecad = fdecan "
			."  WHERE Fdenum = ".$row1[0]
			."         AND Fdeari='".$codari."' "
			."         AND Fdedis='on' "
			."         AND Fdeest='on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
	}
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
				tipcod = '02'
			";
	
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
	
	for( ; $rows = mysql_fetch_array( $res ) ;){
		if( strtolower(substr($art,0,2)) == strtolower(trim( $rows[0] )) ){
			return true;			
		}
	}
	
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
 */
function condicionesKE( &$pac, $art ){
	
	global $conex;
    global $bd;
    
    $pac['sal'] = false;	//Indica si tiene saldo para poder dispensar
    $pac['art'] = false;	//Indica si el articulo existe para el paciente de ke
    $pac['ke'] = false;
	$pac['con'] = false;
	$pac['act'] = false;
	$pac['gra'] = false;
	$pac['cfd'] = 0;
	
	esKE( $pac['his'], $pac['ing'], $pacKE );
	
	$pac['ke'] = $pacKE['ke'];
	$pac['con'] = $pacKE['con'];
	$pac['act'] = $pacKE['keact'];
	$pac['gra'] = $pacKE['kegra'];
	$pac['nut'] = esNutricion( $art );
	$pac['mmq'] = esMMQ( $art );
	$pac['nka'] = esNoKardex( $art );
	
	//El articulo debe tener saldo antes de guardar
	$sql = "SELECT 
				SUM((kadcdi)-(kaddis)) as sal, kadart
			FROM 
				{$bd}_000054
			WHERE	
				kadhis = '{$pac['his']}' 
				AND kading = '{$pac['ing']}'
				AND kadart = '$art'
				AND kadfec = '".date("Y-m-d")."'
				AND kadcon = 'on'
				AND kadare = 'on'
			GROUP BY kadart";
				
	$sql = "SELECT 
				kadcdi-kaddis as sal, kadcpx
			FROM 
				{$bd}_000054
			WHERE	
				kadhis = '{$pac['his']}' 
				AND kading = '{$pac['ing']}'
				AND kadart = '$art'
				AND kadfec = '".date("Y-m-d")."'
				AND kadcon = 'on'
				AND kadare = 'on'
			ORDER BY kadart";
	
//	$sql = "SELECT 
//				SUM((kadcdi)-(kaddis)) as sal, kadart
//			FROM 
//				{$bd}_000054
//			WHERE	
//				kadhis = '{$pac['his']}' 
//				AND kading = '{$pac['ing']}'
//				AND kadart = '$art'
//				AND kadfec = '".date("Y-m-d")."'
//				AND kadcon = 'on'
//			GROUP BY kadart";
//	
//	$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
	
//	cantidadDispensadaRonda( $r[ 'Kadcpx' ], $ronda );

	$listaArcitulos = ArticulosXPaciente( $pac['his'], $pac['ing'] );
	
	for( $i = 0; $i < count( $listaArcitulos ); $i++ ){
	
		if( @$listaArcitulos[$i][ 'cod' ] == $art ){
			
			if( @$listaArcitulos[$i][ 'sal' ] > 0 ){
				$pac['sal'] = true;
				$pac['cfd'] += $listaArcitulos[$i][ 'sal' ];	//cantidad faltante por dispensar
			}
			else{
				$pac['cfd'] = 0;
			}
			$pac['art'] = true;
		}
	}
	
//	if ( $rows = mysql_fetch_array( $res ) ){
//		if( $rows['sal'] > 0 ){
//			$pac['sal'] = true;
//			$pac['cfd'] = $rows['sal'];	//cantidad faltante por dispensar
//		}
//		else{
//			$pac['cfd'] = 0;
//		}
//		$pac['art'] = true;
//	}
}

function consultarAliasPorAplicacionCM($conexion, $codigoInstitucion, $nombreAplicacion){

	$q = " SELECT 
				Detval  
			FROM 
				root_000051
			WHERE 
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";
	

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} 
	
	return $alias;
}

/**
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function ArticulosXPaciente( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	global $bd;
	global $tmpDispensacion;
	
	global $servicio;
	
	global $wemp_pmla;
	
	if( empty( $wemp_pmla ) )
		$wemp_pmla = "01";
	
	$horaCorteDispensacion 	= consultarAliasPorAplicacionCM( $conex, $wemp_pmla, "horaCorteDispensacion" );
	
	$articulos = array();		//Guarda los articulos con saldo positivos
	$vacios = array();		//Guarda los articulos con saldo en 0
	$numrows = false; 
	
	$fecDispensacion = date("Y-m-d");
	
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
	        GROUP BY kadart";
				
	$sql = "SELECT 
				Kadcdi, Kaddis, Kadart, Artcom, Kadsus, Perequ, Kadfin, Kadhin, Kadpro, Kadcfr, Kadcma, Kaddma, Kaddia, Kadcpx, a.id, Tiptpr, Kadsad, Kadreg, Kadcnd, Kadido
	        FROM 
				{$bd}_000054 a, 
				{$wbasedato}_000002 b, 
				{$wbasedato}_000001 c,
				{$bd}_000043 d
	        WHERE 
	        	kadhis='$his'  
	        	AND kading='$ing'
	        	AND a.kadfec = '".date("Y-m-d")."'
	        	AND kadcon = 'on'
	        	AND kadsus != 'on'
	        	AND kadori = 'CM'
	        	AND artcod = kadart
	        	AND kadare = 'on'
	        	AND kadper = percod
	        	AND arttip = tipcod
	        ORDER BY kadart
	        "; //echo ".......<pre>".$sql."</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - en el query: ".$sql." - ".mysql_error() );
	
	$info = consultarInfoTipoArticulos( $conex, $bd );
	
	$articulosADispensar = Array();
	
	for($j = 0; $r = mysql_fetch_array( $res ) ; $j++ ){
		//echo "<br>........$i"; //$r[ 'Kadpro' ] = "DA";
		$esGenerico = esArticuloGenericoIncCM( $conex, $bd, $wbasedato, $r[ 'Kadart' ] );
				
		//Busco proxima ronda
		if( $esGenerico ){
			$r[ 'Kadpro' ] = $esGenerico;
		}
		
		
		if( !( empty( $r[ 'Tiptpr' ] ) || $r[ 'Tiptpr' ] == "" || strtoupper( $r[ 'Tiptpr' ] ) == "NO" ) ){
			$r[ 'Kadpro' ] = $r[ 'Tiptpr' ];
		}
		
		$tiempoPreparacion = $tmpDispensacion;
		$corteProduccion   = $info[ $r[ 'Kadpro' ] ]['horaCorteProduccion'];
		$corteDispensacion = $info[ $r[ 'Kadpro' ] ]['horaCaroteDispensacion'];
		$corteDispensacion = strtotime( "1970-01-01 ".$info[ $r[ 'Kadpro' ] ]['horaCaroteDispensacion'] ) - strtotime( "1970-01-01 00:00:00" );
		
		/****************************************************************************************************
		 * Agosto 27 de 2012
		 ****************************************************************************************************/
		$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $servicio );
		
		if( $disCco && $corteDispensacion < $disCco ){
			$corteDispensacion = $disCco;
		}
		/****************************************************************************************************/
		
		$temprxRonda = ( intval( date( "H" )/$tiempoPreparacion )*$tiempoPreparacion+$tiempoPreparacion );
		$temprxRonda = ( intval( ( date( "H" )+intval( $corteDispensacion/3600 ) )/$tiempoPreparacion )*$tiempoPreparacion );
		
		//Marzo 11 de 2013. Aquí estaba el cambio de Agosto 27 de 2012.
		
		// if( $temprxRonda >= 24 ){
			
			// $info[ $r[ 'Kadpro' ] ]['proximaRonda'] = strtotime( date( "Y-m-d" )." ".($temprxRonda-24).":00:00" )+24*3600;
		// }
		// else{
			// $info[ $r[ 'Kadpro' ] ]['proximaRonda'] = strtotime( date( "Y-m-d" )." $temprxRonda:00:00" );
		// }
		
		//Limito la proxima ronda hasta la hora de corte del día siguiente
		$info[ $r[ 'Kadpro' ] ]['proximaRonda'] = strtotime( date( "Y-m-d" )." 00:00:00" ) + $temprxRonda*3600;
		
		$info[ $r[ 'Kadpro' ] ]['proximaRonda'] = min( $info[ $r[ 'Kadpro' ] ]['proximaRonda'], strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 );
		
		list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s", $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) );
		
		$horaSiguiente = "";
		
		$perteneceRonda = perteneceRonda( $fecpxr, $ronda, $tiempoPreparacion, $r['Kadfin'], $r['Kadhin'], $r['Perequ'], $horaSiguiente, $despuesHoraCortePx, $r['Kaddma'], $r['Kaddia'] );
		
		if( false &&  empty( $r[ 'Kadcpx' ] ) ){
		
			$stringAnt = "";
			$fecDispensacion = date( "Y-m-d" );
			//Creo la regleta del dia actual
			$ar = obtenerVectorAplicacionMedicamentosIncCM( date( "Y-m-d" ), $r['Kadfin'], $r['Kadhin'], $r['Perequ'] );
			$ar2 = obtenerVectorAplicacionMedicamentosIncCM( date( "Y-m-d", time()+24*3600 ), $r['Kadfin'], $r['Kadhin'], $r['Perequ'] );
			$ar2 = arreglarVector( $ar2 );
			
			$quitarUnoARegleta = 25;
			if( count($ar2) == 25 ){
				$quitarUnoARegleta = 24;
			}
				
			foreach( $ar2 as $keyB => $valueB ){
				$ar[$quitarUnoARegleta] = $valueB;
				$quitarUnoARegleta++;
			}
			
			$horasAAplicar = crearVectorAplicacionesIncCM( $ar, $tiempoPreparacion, $r['Kadcfr']/$r['Kadcma'], intval( $corteDispensacion/3600 ) );
			
			
			//Si es trasladado de urgencia o cirugia, se debe dispensar hasta la hora par anterior todos los medicamentos
			//debido a que hasta a esa hora ya le debiero haber aplicado en urgencia todos los medicamentos necesarios
			$horaTraslado = consultarHoraTrasladoUrgenciasIncCM( $conex, $bd, $his, $ing, date( "Y-m-d" ) );
			
			
			//Si es falso busco el dia anterior
			if( !$horaTraslado && $r['Kadsad'] == 0 ){
			
				$diaAnterior = date( "Y-m-d", time()-24*3600 );
				$horaTraslado = consultarHoraTrasladoUrgenciasIncCM( $conex, $bd, $his, $ing, $diaAnterior );
				
				//Creo la regleta del dia anterior
				$ar3 = obtenerVectorAplicacionMedicamentosIncCM( $diaAnterior, $r['Kadfin'], $r['Kadhin'], $r['Perequ'] );
				$ar4 = obtenerVectorAplicacionMedicamentosIncCM( $fecDispensacion, $r['Kadfin'], $r['Kadhin'], $r['Perequ'] );
				$ar4 = arreglarVector( $ar4 );

				$quitarUnoARegleta = 25;
				if( count($ar4) == 25 ){
					$quitarUnoARegleta = 24;
				}
				
				foreach( $ar4 as $keyB => $valueB ){
					$ar3[ $quitarUnoARegleta ] = $valueB;
					$quitarUnoARegleta++;
				}

				$horasAAplicarTemp = crearVectorAplicacionesIncCM( $ar3, $tiempoPreparacion, $r['Kadcfr']/$r['Kadcma'], intval( $corteDispensacion/3600 ) );
				
				//Consulto la hora par mas cercana anterior
				$horaTraslado = intval( $horaTraslado/$tmpDispensacion )*$tmpDispensacion;
				
				//Creo la hora correctamente
				if( $horaTraslado < 10 ){
					if( $horaTraslado != 0 ){
						$horaTraslado = "0$horaTraslado:00:00";
					}
					else{
						$horaTraslado = false;
					}
				}
				else{
					if( $horaTraslado == 24 ){
						$horaTraslado .= "00:00:00";
					}
					else{
						$horaTraslado .= ":00:00";
					}
				}
				
				//Calculo cuanto se debe dispensar hasta la hora de corte
				$canADispensarTemp1 = cantidadTotalADispensarRondaIncCM( $horasAAplicarTemp, "00:00:00" );
				
				//Calculo cuanto se debe dispensar hasta la hora par mas cercana
				$canADispensarAntesRecibo = cantidadTotalADispensarRondaIncCM( $horasAAplicarTemp, $horaTraslado );
				
				//Calculo la cantidad que no se dispensó el día anterior
				$cantidadFaltante = $canADispensarTemp1 - $canADispensarAntesRecibo;
				
				//Creo string que indica cuanto se dejo de dispensar el dia anterior
				if( $cantidadFaltante > 0 ){
					$stringAnt = "Ant-".$cantidadFaltante."-0,";
				}

				//Como hubo traslado el dia anterior dejo la hora en 0
				//Esto para calcular la cantidad anterior correcta
				if( $horaTraslado ){
					$horaTraslado = "00:00:00";
				}
			}
			if( $horaTraslado ){	//Si viene de urgencias, cirugia o un centro de costos de ingreso
					
				//Busco solo la hora, sin minutos y segundos
				list( $horaTraslado ) = explode( ":", $horaTraslado );
				
				//Consulto la hora par mas cercana anterior
				$horaTraslado = intval( $horaTraslado/$tmpDispensacion )*$tmpDispensacion;
				
				//Creo la hora correctamente
				if( $horaTraslado < 10 ){
					if( $horaTraslado != 0 ){
						$horaTraslado = "0$horaTraslado:00:00";
					}
					else{
						$horaTraslado = false;
					}
				}
				else{
					if( $horaTraslado == 24 ){
						$horaTraslado .= "00:00:00";
					}
					else{
						$horaTraslado .= ":00:00";
					}
				}
				
				//Calculo cuanto se debe dispensar hasta la hora de corte
				$canDispensadaHastaTraslado = 0;
				if( $horaTraslado ){
					$canDispensadaHastaTraslado = cantidadTotalADispensarRondaIncCM( $horasAAplicar, $horaTraslado );
				}
				
				$horasAAplicar = crearAplicacionesCargadasPorHoras( $horasAAplicar, $r['Kaddis'] + $canDispensadaHastaTraslado );
				$horasAAplicar = $stringAnt.$horasAAplicar;
			}
			else{
			
				//Calculo cuanto se debe dispensar hasta la hora de corte
				$canADispensarTemp1 = cantidadTotalADispensarRondaIncCM( $horasAAplicar, "$horaCorteDispensacion:00:00" );
					
				//Se resta la cantidad sin dispensar del dia anterior a lo calculodo hasta la hora de corte				
				if( false && !empty( $info['Kadreg'] )  ){
					$horasAAplicar = crearAplicacionesCargadasPorHoras( $horasAAplicar, $r['Kaddis'] + ( $canADispensarTemp1 - $r['Kadsad'] ) );
				}
				else{
					$horasAAplicar = crearAplicacionesCargadasPorHoras( $horasAAplicar, $r['Kaddis'] );
				}
			}
			
			list( $kadRondaCpx, $kadFechaRondaCpx ) = explode( "|", consultarUltimaRondaDispensada( $horasAAplicar ) );
			
			if( $kadRondaCpx > 0 ){
				
				if( empty( $kadFechaRondaCpx ) ){
					$kadFechaRondaCpx = date( "Y-m-d", time()+24*3600 );
				}
				else{
					$kadFechaRondaCpx = date( "Y-m-d" );
				}
			}
			
			//actualizo campo cpx
			$slqcpx = "UPDATE
							{$bd}_000054
					   SET
					   		Kadcpx = '$horasAAplicar',
							Kadron = '$kadRondaCpx',
							Kadfro = '$kadFechaRondaCpx'
					   WHERE
					   		id = '{$r['id']}'
					   ";
			
			$rescpx = mysql_query( $slqcpx, $conex ) or die( mysql_errno()." - Error en el query $slqcpx - ".mysql_error() );
			
			$r[ 'Kadcpx' ] = $horasAAplicar;
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$ori = 'CM';
		$pac['his'] = $his;
		$pac['ing'] = $ing;
		
		//Para todos los pisos por defecto es 4 horas
		$antHoras = $corteDispensacion/3600;	//Controla cuantas horas antes puede dispensar
		// if( $cco['urg'] ){
			// //Para urgencias se deja 22
			// $antHoras = 8;	//Controla cuantas horas antes puede dispensar
		// }
		
		/********************************************************************************
		 * Septiembre 12 de 2011
		 *
		 * - Verifico si el articulo es a necesidad
		 * - Si es a necesidad, busco si el articulo no tiene saldo en piso
		 * - Si no tiene saldo en piso se muestra
		 * - Si no es a necesidad no se muestra
		 ********************************************************************************/
		$esANecesidad = esANecesidad( $bd, $conex, $r['Kadcnd'] );
		
		$esCiclos24 = esCcoCiclos24Horas( $conex, $bd, $servicio );
			
		if( $esCiclos24 )
			$esANecesidad = false;
		
		$mostrarArticulo = true;
		$requiereSaldo = false;
		if( $esANecesidad ){
		
			$fraccion = consultarFraccion( Array( "cod"=>$r[ 'Kadart' ] ), $cco );
			$saldo = tieneSaldoEnPiso(  $bd, $conex, $ori, $pac['his'], $pac['ing'], $r['Kadart'] );
			
			if( !empty($procesoContingencia) && $procesoContingencia == 'on' ){
				// $val = true;
				$saldo = 0;
			}
			
			if( $saldo/$fraccion >= ($r['Kadcfr']/$r['Kadcma']) ){
				$requiereSaldo = false;
				$mostrarArticulo = false;
				
				// $r['Kadsus'] = "off";	//Suspendo para que no se vea
			}
			else{
				$requiereSaldo = true;
				$perteneceRonda = true;	//Siempre debe pedir si es a necesidad y no hay saldo					
				
				$r['Kadcpx'] = "$ronda-".( ceil( $r['Kadcfr']/$r['Kadcma']-$saldo/$fraccion ) )."-0";
				
				$r['Kadcdi'] = $r['Kaddis']+ceil( $r['Kadcfr']/$r['Kadcma'] );
			}
		}
		/********************************************************************************/
		
		
		
		/****************************************************************************
		 * Agosto 15 de 2012
		 ****************************************************************************/
		$regletas = Array();
		
		$fechorTrasladoRep = 0;
		
		//consulto la fecha y hora de traslado desde urgencia
		$horaTrasladoRep = consultarHoraTrasladoUrgenciasIncCM( $conex, $bd, $pac['his'], $pac['ing'], $fecDispensacion );
		
		if( !$horaTrasladoRep ){
			$horaTrasladoRep = consultarHoraTrasladoUrgenciasIncCM( $conex, $bd, $pac['his'], $pac['ing'], date( "Y-m-d", strtotime( $fecDispensacion )-24*3600 ) );
			
			if( $horaTrasladoRep ){
				$fechorTrasladoRep = strtotime( date( "Y-m-d", strtotime( $fecDispensacion )-24*3600 )." ".$horaTrasladoRep );
			}
		}
		else{
			$fechorTrasladoRep = strtotime( $fecDispensacion." ".$horaTrasladoRep );
		}
		
		$corteDispensacionRep = $corteDispensacion;
		
		/******************************************************************************************
		 * Agosto 28 de 2012
		 *
		 * No permito crear una regleta mas alla de la hora de corte del día siguiente
		 ******************************************************************************************/
		$corteDispensacionRep = min( $corteDispensacionRep, $horaCorteDispensacion*3600+2*3600 );
		/******************************************************************************************/
		
		$canAplicada = 0;
		
		
		//Busco si hay articulos repetidos
		$hayRepetidos = false;
		for( $jj = 0; $r2 = mysql_fetch_array( $res ); $jj++ ){
		
			if( $r2['Kadart'] == $r['Kadart'] ){
				
				$j++;
				
				if( $r2['Kadsus'] != 'on' ){
					
					//Si es a necesidad y tiene saldo no lo muestro
					//De lo contrario, le coloco frecuencia cada 24 horas y fecha y hora de inicio
					//la cambio para que muestro lo necesario
					$esANecesidadRep = esANecesidad( $bd, $conex, $r2['Kadcnd'] );
					
					if( $esCiclos24 )
						$esANecesidadRep = false;
					
					//Recalculo dosis máxima, esto por que se requiere que un medicamento se dispense hasta que cumpla el total de dosis a aplicar
					//y el sistema hasta ahora calculo hasta cumplir la dosis maxima por frecuencia
					if( trim( $r2[ 'Kaddma' ] ) != '' )
						$r2[ 'Kaddma' ] = recalcularDosisMaxima( $conex, $bd, $pac['his'], $pac['ing'], $r2['Kadart'], $r2, $antHoras, $esANecesidadRep );
				
					$agregarRepetido = true;	//Indica si el medicamento es repetido
					
					$r['Kadcdi'] += $r2['Kadcdi'];
					$r['Kaddis'] += $r2['Kaddis'];
					
					//Consulto la cantidad aplicada para este medicamento, hasta maximo la hora de corte de dispensación del día siguiente
					$canAplicadaRep = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r2['Kadart'], max( strtotime( $r2[ 'Kadfin' ]." ".$r2[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), $r2[ 'Kadido' ], $fechorTrasladoRep );
					$canAplicada += $canAplicadaRep;
				
					if( $esANecesidadRep ){
					
						$fraccionRep = consultarFraccion( Array( "cod"=>$r2[ 'Kadart' ] ), $cco );
						$saldoRep = tieneSaldoEnPiso(  $bd, $conex, $ori, $pac['his'], $pac['ing'], $r2['Kadart'] );
						
						if( $saldoRep/$fraccionRep >= ($r2['Kadcfr']/$r2['Kadcma']) ){
							$agregarRepetido = false;
						}
						else{
							//cambio la fecha y hora de inicio y la frecuencia para que salga lo suficiente para una dosis
							//Primero verifico si la fecha y hora de dispensacion sea mayor a la hora de traslado
							//esto por que no se puede dispensar nada en la ronda en que se recibe un paciente desde urgencias
							if( strtotime( date( "Y-m-d" )." ".gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 ) ) >= $fechorTrasladoRep ){
								$r2['Kadfin'] = date( "Y-m-d" );
								$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 );
							}
							else{
								$r2['Kadfin'] = date( "Y-m-d" );
								$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H", $fechorTrasladoRep + 2*3600)/2 )*2*3600 );
							}
							
							$r2['Perequ'] = 48;
							
							//Aumento dosis tanto como se halla dispensado las ultimas dosis para que siempre pueda dispensar
							$r2['Kadcfr'] += $canAplicadaRep*$r2['Kadcma'];
						}
					}
					
					if( $agregarRepetido ){
						//Con las funciones virtuales
						$fhFinMed = consultarFechaHoraFinalMedicamento( $horaCorteDispensacion, $fecDispensacion, $r2[ 'Kadfin' ], $r2[ 'Kadhin' ], $r2[ 'Kaddma' ], $r2[ 'Kaddia' ], $r2[ 'Perequ' ] );
						
						$regletas[] = Array( 0 			 => crearRegletaVirtual( strtotime( $fecDispensacion." 00:00:00" )-($antHoras-2)*3600, strtotime( $fecDispensacion." 00:00:00" )+22*3600+$corteDispensacionRep, strtotime( $r2['Kadfin']." ".$r2['Kadhin'] ), $r2[ 'Perequ' ], $r2[ 'Kadcfr' ]/$r2[ 'Kadcma' ], $fhFinMed, $fechorTrasladoRep ),
											 'id' 		 => $r2[ 'id' ],
											 'fecUltDis' => 0
											);
					}

					$hayRepetidos = true;
				}
			}
			else{
				break;
			}
		}
		
		if( true || $hayRepetidos ){

			if( $r2 ){
				@mysql_data_seek( $res, $j+1 );
			}
			
			if( $r['Kadsus'] != 'on' ){
				
				//Recalculo dosis máxima, esto por que se requiere que un medicamento se dispense hasta que cumpla el total de dosis a aplicar
				//y el sistema hasta ahora calculo hasta cumplir la dosis maxima por frecuencia
				if( trim( $r[ 'Kaddma' ] ) != '' )
					$r[ 'Kaddma' ] = recalcularDosisMaxima( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], $r, $antHoras, $esANecesidad );
			
				//Consulto la cantidad aplicada para este medicamento, hasta maximo la hora de corte de dispensación del día siguiente
				$canAplicadaRep = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], max( strtotime( $r[ 'Kadfin' ]." ".$r[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), $r[ 'Kadido' ], $fechorTrasladoRep );
				$canAplicada += $canAplicadaRep;
			
				$agregarRepetido = true;
			
				if( $esANecesidad ){
					
					if( false && $saldo/$fraccion >= ($r['Kadcfr']/$r['Kadcma']) ){
						$agregarRepetido = false;
					}
					else{
						//cambio la fecha y hora de inicio y la frecuencia para que salgo lo suficiente para una dosis
						//Primero verifico si la fecha y hora de dispensacion sea mayor a la hora de traslado
						//esto por que no se puede dispensar nada en la ronda en que se recibe un paciente desde urgencias
						if( strtotime( date( "Y-m-d" )." ".gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 ) ) >= $fechorTrasladoRep ){
							$r['Kadfin'] = date( "Y-m-d" );
							$r['Kadhin'] = gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 );
						}
						else{
							$r2['Kadfin'] = date( "Y-m-d" );
							$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H", $fechorTrasladoRep+2*3600 )/2 )*2*3600 );
						}
						
						$r['Perequ'] = 48;
						
						//Aumento dosis tanto como se halla dispensado las ultimas dosis para que siempre pueda dispensar
						$r['Kadcfr'] += $canAplicadaRep*$r['Kadcma'];
					}
				}
				
				if( $agregarRepetido ){
					$fhFinMed = consultarFechaHoraFinalMedicamento( $horaCorteDispensacion, $fecDispensacion, $r[ 'Kadfin' ], $r[ 'Kadhin' ], $r[ 'Kaddma' ], $r[ 'Kaddia' ], $r[ 'Perequ' ] );
					
					$regletas[] = Array( 0    		 => crearRegletaVirtual( strtotime( $fecDispensacion." 00:00:00" )-($antHoras-2)*3600, strtotime( $fecDispensacion." 00:00:00" )+22*3600+$corteDispensacionRep,strtotime( $r['Kadfin']." ".$r['Kadhin'] ), $r[ 'Perequ' ], $r[ 'Kadcfr' ]/$r[ 'Kadcma' ], $fhFinMed, $fechorTrasladoRep ),
										 'id' 		 => $r[ 'id' ],
										 'fecUltDis' => 0
										 );
				}
			}
			else{
				$r['Kadsus'] = 'off';
			}
			
			//Consulto si hay saldo en piso
			$saldoPiso = tieneSaldoEnPiso( $bd, $conex, $ori, $pac['his'], $pac['ing'], $r['Kadart'] );
			
			//Si esta en 0 es que esta vacio
			if( !$saldoPiso ){
				$saldoPiso = 0;
			}
			
			$fraccionArticulo = consultarFraccion( Array( "cod"=>$r[ 'Kadart' ] ), $cco );
			
			//Consulto la cantidad aplicada de las ultimas dos rondas
			// $canAplicada = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], time()-4*3600, time(), false, $fechorTrasladoRep );
			
			$canDisVirtual = cantidadADispensarRegletasVirtual( $regletas, time()-$antHoras*3600 );
			
			//Carga una cantidad a la regletas
			cargarCantidadRegletasVirtuales( $regletas, $canDisVirtual+$canAplicada/$fraccionArticulo+round( $saldoPiso/$fraccionArticulo, 3 ) );
			
			//Combino las regletas
			$regletaCombinada = combinarRegletasVirtuales( $regletas, false );
			
			//la convierto a string
			$stRegletaCombinada = regletaVirtualToString( $regletaCombinada, strtotime( $fecDispensacion." 00:00:00") );
			
			$r[ 'Kadcpx' ] = $stRegletaCombinada;
			
			//verifico que halla algo que cargar para las rondas correspondientes
			$mostrarArticulo = $perteneceRonda = perteneceARondaPorRegletaVirtual( $regletaCombinada, $info[ $r[ 'Kadpro' ] ]['proximaRonda'] );
			
			actualizarRegletas( $conex, $bd, $regletas, strtotime( $fecDispensacion." 00:00:00" ) );
		}
		else{
			//devuelvo a la posicion en que inicio originalmente
			@mysql_data_seek( $res, $j+1 );
		}
		/****************************************************************************/
		
		$sindis = cantidadSinDispensarRondas( $r[ 'Kadcpx' ], $ronda, date( "Y-m-d" ) == $fecpxr );
		
		if( ( $perteneceRonda && time() >= $info[ $r[ 'Kadpro' ] ]['proximaRonda']-$corteDispensacion && time() <= $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) ){
			
			$can = cantidadADispensarRonda( $r['Kadcpx'], $ronda, date( "Y-m-d" ) == $fecpxr );			
			
			@$articulosADispensar[ $r['Kadart'] ][0] += $can+$sindis;	//Cantidad a dispensar
			@$articulosADispensar[ $r['Kadart'] ][1] += cantidadDispensadaRonda( $r[ 'Kadcpx' ], $ronda, date( "Y-m-d" ) == $fecpxr ); //$r['Kaddis'];	//Cantidad dispensada
			@$articulosADispensar[ $r['Kadart'] ][2] = $r['Kadart'];	//Codigo del articulo
			@$articulosADispensar[ $r['Kadart'] ][3] = $r['Artcom'];	//Nombre comercial
			@$articulosADispensar[ $r['Kadart'] ][4] = $r['Kadsus'];	//Articulo supendido
			@$articulosADispensar[ $r['Kadart'] ][5] = ""; //$r['Artpos'];	//Posicion
			@$articulosADispensar[ $r['Kadart'] ][6] += $r['Kadcdi'];	//Posicion
			@$articulosADispensar[ $r['Kadart'] ][7] += $r['Kaddis'];	//Posicion
			@$articulosADispensar[ $r['Kadart'] ][8] = consultarCTCAgotadoCM( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], $wemp_pmla );
		}
		elseif( $sindis > 0 ){
			
			@$articulosADispensar[ $r['Kadart'] ][0] += $sindis;		//Cantidad a dispensar
			@$articulosADispensar[ $r['Kadart'] ][1] += 0;				//cantidadDispensadaRonda( $r[ 'Kadcpx' ], $ronda ); //$r['Kaddis'];	//Cantidad dispensada
			@$articulosADispensar[ $r['Kadart'] ][2] = $r['Kadart'];	//Codigo del articulo
			@$articulosADispensar[ $r['Kadart'] ][3] = $r['Artcom'];	//Nombre comercial
			@$articulosADispensar[ $r['Kadart'] ][4] = $r['Kadsus'];	//Articulo supendido
			@$articulosADispensar[ $r['Kadart'] ][5] = ""; //$r['Artpos'];	//Posicion
			@$articulosADispensar[ $r['Kadart'] ][6] += $r['Kadcdi'];	//Posicion
			@$articulosADispensar[ $r['Kadart'] ][7] += $r['Kaddis'];	//Posicion
			@$articulosADispensar[ $r['Kadart'] ][8] = consultarCTCAgotadoCM( $conex, $bd, $pac['his'], $pac['ing'],  $r['Kadart'], $wemp_pmla );
		}
	}
	
	$arts = array();
	$i = 0;
	foreach( $articulosADispensar as $key => $value ){
		@$arts[ $i ] = $value;
		$i++;
	}
	
	for($i = 0, $j = 0; $i < count( $arts ) ; $i++ ){
		$rows = $arts[$i];
		if( $rows[0]-$rows[1] > 0 /*&& $rows[6] - $rows[7] > 0*/ && $rows[4] != 'on' ){
			$articulos[$j]['cod'] = $rows[2];
			$articulos[$j]['nom'] = $rows[3];
			$articulos[$j]['cdi'] = $rows[0];
			$articulos[$j]['dis'] = $rows[1];
			$articulos[$j]['sal'] = $rows[0] - $rows[1];
			$articulos[$j]['tot'] = $rows[6];
			$articulos[$j]['gra'] = $rows[7];
			$articulos[$j]['ctc'] = $rows[8];
			$j++;
		}
	}
	
	return $articulos;
}

/**
 * Registra un articulo al paciente en el kardex electrónico
 * 
 * @param $art		Codigo del Aritculo del paciente
 * @param $his		Historia del paciente
 * @param $ing		Ingreso del paciente
 * @return bool		True si es verdaderos
 */
function registrarArticuloKE( $art, $his, $ing, $dev = false, &$idKardex, $num, $lin ){
	
	global $conex;
	global $bd;
	global $tempRonda;
	global $tmpDispensacion;
	
	global $servicio;
	
	if( !$dev ){
					
		$sqlid="SELECT 
					id, kadcpx 
				FROM 
					{$bd}_000054 
				WHERE 
					kadart = '$art'
					AND kadcdi > kaddis+0
					AND kadfec = '".date("Y-m-d")."'
					AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadsus != 'on'
		       		AND kadcon = 'on'
		       		AND kadare = 'on'
				ORDER BY kadart
				";
		
		$resid = mysql_query( $sqlid, $conex ) or die(mysql_errno()." - en el query: ".$sqlid." - ".mysql_error());;
		
		/************************************************************************************************************
		 * Octubre 4 de 2011
		 ************************************************************************************************************/
		consultarInfoTipoArticulos( $conex, $bd );
		
		/****************************************************************************************************
		 * Agosto 28 de 2012
		 ****************************************************************************************************/
		$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $servicio );
		
		if( $disCco && $tmpDispensacion < $disCco ){
			$tmpDispensacion = $disCco;
		}
		/****************************************************************************************************/
		
		$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );			

		if( $tempRonda >= 24 ){
				
			if( $tempRonda-24 < 10 ){
				$tempRonda = "0".($tempRonda-24).":00:00";
			}
			else{
				$tempRonda = ($tempRonda-24).":00:00";
			}
		}
		else{
			if( $tempRonda < 10 ){
				$tempRonda = "0$tempRonda:00:00";
			}
			else{
				$tempRonda = "$tempRonda:00:00";
			}
		}
		$rondaActual = $tempRonda;
		/************************************************************************************************************/
		
		$cancdi = 0;
		for( $i = 0;$row = mysql_fetch_array( $resid ); $i++ ){
			
			$exp = explode( ",",$row[1] );

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
		
		@$row[0] = $id;
		
		if( empty($row[0]) ){
			$row[0] = "";
		}
		
		$sql = "SELECT
					id, kadcdi-kaddis, kadcpx, kadcfr, kadcma
				FROM
					{$bd}_000054
				WHERE
					id = '{$row[0]}'"; //echo "........<pre>$sql</pre>"; exit;
					
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
		
		/************************************************************************************
		 * Noviembre 1 de 2011
		 ************************************************************************************/
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
		/************************************************************************************/
		
		//Actualizando registro con el articulo cargado
		$sql = "UPDATE 
					{$bd}_000054 
		       	SET 
		       		kaddis = kaddis+1,
		       		kadhdi = '".date("H:i:s")."',
		       		kadcpx = '$nuevoAplicaciones',
					kadron = '$kadRondaCpx',
					kadfro = '$kadFechaRondaCpx'
		        WHERE 
		        	kadcdi >= kaddis+1  
		       		AND kadart = '$art' 
		       		AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadfec = '".date("Y-m-d")."'
		       		AND kadsus != 'on'
		       		AND kadcon = 'on'
		       		AND kadori = 'CM' 
		       		AND kadare = 'on'
		       		AND id = {$row[0]}";
					
		$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
		
		if( $res && mysql_affected_rows() > 0 ){
		
			/************************************************************************
			 * Abril 23 de 2012
			 ************************************************************************/
			global $contingencia;
			global $marcaContingencia;
			global $fhGrabacionContingencia;
			global $fhContingencia;
			global $procesoContingencia;
			
			$fecDispensacion = date("Y-m-d");
			
			if( !empty( $procesoContingencia ) && $procesoContingencia == 'on' ){
				actualizandoCargo( $conex, $bd, $num, $lin, $marcaContingencia );
			}
			elseif( !empty( $contingencia ) && $contingencia == 'on' ){
				
				if( time() > $fhGrabacionContingencia ){
					
					actualizandoCargo( $conex, $bd, $num, $lin, $marcaContingencia );
				}
			}
			/************************************************************************/
		
		
			return true;
		}
		else
			return false;
	}
	else{
		$sqlid="SELECT 
					max(id), kaddis 
				FROM 
					{$bd}_000054 
				WHERE 
					kadart = '$art'
					AND kaddis > 0
					AND kadfec = '".date("Y-m-d")."'
					AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadcon = 'on'
				GROUP BY kadart";
					
		$sqlid="SELECT 
					id, kaddis, kadcpx 
				FROM 
					{$bd}_000054 
				WHERE 
					kadart = '$art'
					AND kaddis > 0
					AND kadfec = '".date("Y-m-d")."'
					AND kadhis = '$his'  
		       		AND kading = '$ing'
		       		AND kadcon = 'on'
				ORDER BY kadart";
		
		$resid = mysql_query( $sqlid, $conex ) or die(mysql_errno()." - en el query: ".$sqlid." - ".mysql_error());;
		
		if( $row = mysql_fetch_array( $resid ) ){
			$id = $row[0];
		}
		else{
			$row[0] = "";
			return false;
		}
		
		$cpx = crearAplicacionesDevueltasPorHoras( $row[2], 1 );
		
		if( $row[1] > 1 ){
			//Actualizando registro con el articulo cargado
			$sql = "UPDATE 
						{$bd}_000054 
			       	SET 
			       		kaddis = kaddis-1,
			       		kadcpx = '$cpx'
			        WHERE 
			        	kaddis > 0  
			       		AND kadart = '$art' 
			       		AND kadhis = '$his'  
			       		AND kading = '$ing'
			       		AND kadfec = '".date("Y-m-d")."'
			       		AND kadcon = 'on'
			       		AND kadori = 'CM' 
			       		AND id = {$row[0]}";
		}
		else{
			//Actualizando registro con el articulo cargado
			$sql = "UPDATE 
						{$bd}_000054 
			       	SET 
			       		kaddis = kaddis-1,
			       		kadhdi = '00:00:00',
			       		kadcpx = '$cpx'
			        WHERE 
			        	kaddis > 0  
			       		AND kadart = '$art' 
			       		AND kadhis = '$his'  
			       		AND kading = '$ing'
			       		AND kadfec = '".date("Y-m-d")."'
			       		AND kadcon = 'on'
			       		AND kadori = 'CM' 
			       		AND id = {$row[0]}";
		}
		
		$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
		
		if( $res && mysql_affected_rows() > 0 )
			return true;
		else
			return false;
	}
}

/**
 * Indica si el paciente se encuentra en Kardex Electronico o no
 * 
 * @param array $his	Paciente al que se le va a cargar los articulos
 * @param array $ing	Ingreso del paciente al que se le va a cargar los articulos
 * @return bool $ke		Devuelve true en caso de se Kardex electronico, en caso contrario false
 */

/* 
 * - Tiene KE si esta en la tabla 53
 * - Esta grabado si el campo gra esta en on
 * - ESta confirmado el KE si con esta en on
 * - Esta actualizado las fechas son iguales en 53 y 54 (kadfec) 
 */
function esKE( $his, $ing, &$packe ){
	
	global $conex;
	global $bd;
	global $ser;
	
	$ke = 0;
	$pac = array();
	$pac['his'] = $his;
	$pac['ing'] = $ing;
	// $pac['keact']=false;
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
	        	AND karcon like 'on'
	        GROUP BY a.Fecha_Data
	        ORDER BY a.Fecha_Data DESC
	        ";
	
	$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());;
	
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
				
				$result = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());;
		
				if( mysql_num_rows($result) > 0 ){
					$pac['keact']=true;
				}
			}
		}
	}	
	
	if( irAOrdenes( $conex, $bd, $ser['cod'] ) == 'on' && pacienteKardexOrdenes( $conex, $bd, $pac['his'], $pac['ing'], date("Y-m-d") ) ){
		$pac['con'] = true;
		$pac['keact']=true;
	}
	
	$packe = $pac;	
	return $ke;
}

function consultarCentros($cco)
{
	global $conex;
	global $wbasedato;

	if ($cco != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$ccos[0] = $cco;
		$cadena = "A.Ccocod != mid('" . $cco . "',1,instr('" . $cco . "','-')-1) AND";
		$inicio = 1;
	}
	else
	{
		$cadena = '';
		$inicio = 0;
	}
	// consulto los conceptos
	$q = " SELECT A.Ccocod as codigo, B.Cconom as nombre"
	. "        FROM movhos_000011 A, costosyp_000005 B "
	. "      WHERE " . $cadena . " "
	. "        A.Ccoima = 'on' "
	. "        AND A.Ccocod = B.Ccocod "
	. "        AND A.Ccoest='on' ";

	$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0;$i < $num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$ccos[$inicio] = $row1['codigo'] . '-' . $row1['nombre'];
			$inicio++;
		}
	}
	else if ($inicio == 0)
	{
		$ccos = '';
	}

	return $ccos;
}

/**
* Se consulta el concsecutivo del movimiento
* 
* @param number $signo , -1 si es una salida, 1 si es entrada
* @return caracter $resultado, consecutivo de acuerdo al concepto que cumple con los parametro recibidos
*/
function consultarConsecutivo($signo)
{
	global $conex;
	global $wbasedato;

	$q = " SELECT Concod, Concon "
	. "       FROM " . $wbasedato . "_000008  "
	. "    WHERE Conind = '" . $signo . "' "
	. "       AND Concar = 'on' "
	. "       AND Conest = 'on' ";

	$res = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	$resultado = $row[0] . '-' . ($row[1] + 1);
	return $resultado;
}

/**
* consultamos el insumo, el nombre y si es un insumo producto codificado o no codificado
* 
* @param caracter $parbus valor que se busca
* @param caractere $forbus la forma de busqueda
* @return vector lista de insumos que cumplen con el criterio de busqueda
*/
function consultarInsumos($parbus, $forbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'Rotulo':
		$q = " SELECT Tippro "
		. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001 "
		. "    WHERE  Artcod = '" . $parbus . "' "
		. "       AND Artest = 'on' "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip ";
		$res = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;
		$row = mysql_fetch_array($res);
		// $exp=explode('-', $parbus);
		// if (isset($exp[1]))
		if (isset($row[0]) and $row[0] == 'on')
		{
			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo, Arttnc "
			. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, movhos_000027 "
			. "    WHERE Artcod = '" . $parbus . "' "
			. "       AND Artest = 'on' "
			. "       AND Artuni= Unicod "
			. "       AND Tipest = 'on' "
			. "       AND Tipcod = Arttip "
			. "       AND Uniest='on' "
			. "    Order by 1 ";
		}
		else
		{
			$q = " SELECT C.Artcod, C.Artcom, C.Artgen, C.Artuni, B.Unides, D.Tippro, D.Tipcdo, Arttnc  "
			. "       FROM movhos_000026 A, movhos_000027 B, " . $wbasedato . "_000002 C, " . $wbasedato . "_000001 D, " . $wbasedato . "_000009 E"
			. "    WHERE A. Artcod = '" . $parbus . "' "
			. "       AND A. Artest = 'on' "
			. "       AND A. Artcod = E.Apppre "
			. "       AND E. Appest = 'on' "
			. "       AND E. Appcod = C.Artcod "
			. "       AND C. Artest = 'on' "
			. "       AND C. Artuni= B.Unicod "
			. "       AND B.Uniest='on' "
			. "       AND D.Tipcod = C.Arttip "
			. "       AND D.Tipest = 'on' "
			. "    Order by 1 ";
		}
		break;

		case 'Codigo':
		$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo, Arttnc "
		. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, movhos_000027 "
		. "    WHERE Artcod like '%" . $parbus . "%' "
		. "       AND Artest = 'on' "
		. "       AND Artuni= Unicod "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip "
		. "       AND Uniest='on' "
		. "    Order by 1 ";

		break;

		case 'Nombre comercial':

		$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo, Arttnc "
		. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001, movhos_000027 "
		. "    WHERE Artcom like '%" . $parbus . "%' "
		. "       AND Artest = 'on' "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip "
		. "       AND Artuni= Unicod "
		. "       AND Uniest='on' "
		. "    Order by 1 ";

		break;
		case 'Nombre genérico':

		$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo, Arttnc "
		. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001, movhos_000027 "
		. "    WHERE Artgen like '%" . $parbus . "%' "
		. "       AND Artest = 'on' "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip "
		. "       AND Artuni= Unicod "
		. "       AND Uniest='on' "
		. "    Order by 1 ";

		break;
	}

	$res = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod'] = $row[0];
			$productos[$i]['nom'] = str_replace('-', ' ', $row[1]);
			$productos[$i]['gen'] = str_replace('-', ' ', $row[2]);
			$productos[$i]['pre'] = $row[3] . '-' . $row[4];
			$productos[$i]['est'] = 'on';
			if ($row[5] == 'on')
			{
				$productos[$i]['lot'] = 'on';
			}
			else
			{
				$productos[$i]['lot'] = 'off';
			}

			if ($row[6] == 'on')
			{
				//Febrero 21 de 2013
				if( $row[7] == 'on' ){
					$productos[$i]['cdo'] = 'off';
				}
				else{
					$productos[$i]['cdo'] = 'on';
				}
			}
			else
			{
				$productos[$i]['cdo'] = 'off';
			}
		}
	}
	else
	{
		$productos = false;
	}
	return $productos;
}

/**
* se consultan todos los posibles lotes para el producto, si ya se ha ingresado un lote se manda a la funcion
* 
* @param caracter $parbus codigo del insumo
* @param caracter $cco centro de costos (codigo-nombre)
* @param caracter $lote lote que ha sido escogido previamente
* @param caracter $crear Si es devolver, solo se deben mostrar los lotes que han sido cargados a paciente
* @param caracter $tipo si es tipo cargar aplica lo de mostrar solo los lotes cargados previamente
* @param caracter $destino si es tipo C aca vendra la historia-ingreso del paciente
* @return vector lista de lotes para mostrar en drop down
*/
function consultarLotes($parbus, $cco, $lote, $accion, $destino)
{
	global $conex;
	global $wbasedato;

	if ($lote != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		if ($accion != 'Cargo')
		{
			$q = "   SELECT Concod from " . $wbasedato . "_000008 "
			. "    WHERE Conind = '-1' "
			. "      AND Concar = 'on' "
			. "      AND Conest = 'on' ";

			$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row2 = mysql_fetch_array($res1);

			$q = " SELECT distinct Plocod "
			. "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007, " . $wbasedato . "_000004 "
			. "    WHERE   Mencon= '" . $row2[0] . "' "
			. "            and Mencco =mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
			. "            and Menccd = '" . $destino . "' "
			. "            and Mdecon = Mencon "
			. "            and Mdedoc = Mendoc "
			. "  		  and Mdeart = '" . $parbus . "' "
			. "            and Mdeest = 'on' "
			. "            and Menest = 'on' "
			. "            and Plocod= mid(Mdenlo,1,instr(Mdenlo,'-')-1) "
			. "            and mid(Mdenlo,1,instr(Mdenlo,'-')-1)= '" . $lote . "' "
			. "            and Plopro= '" . $parbus . "' "
			. "        ORDER BY " . $wbasedato . "_000004.id asc";

			$res = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$consultas[0] = $lote;
				$cadena = "Plocod != '" . $lote . "' AND";
				$inicio = 1;
			}
			else
			{
				$cadena = '';
				$inicio = 0;
			}
		}
		else
		{
			$consultas[0] = $lote;
			$cadena = "Plocod != '" . $lote . "' AND";
			$inicio = 1;
		}
	}
	else
	{
		$cadena = '';
		$inicio = 0;
	}

	$dias = date('d')-20;
	if ($dias > 0)
	{
		$fecha = date('Y') . '-' . date('m') . '-' . $dias;
	}
	else
	{
		$dias = 31 + $dias;
		$fecha = mktime(0, 0, 0, date("m")-1, $dias, date("Y"));
		$fecha = date('Y-m-d', $fecha);
	}

	if ($accion == 'Cargo')
	{
		$q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
		. "       FROM " . $wbasedato . "_000004 "
		. "    WHERE " . $cadena . " "
		. "       Plopro = '" . $parbus . "' "
		. "       AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "       AND Ploest = 'on' "
		. "       AND Plosal > 0 "
		. "       AND Plofve >= '" . date('Y-m-d') . "' "
		. "    Order by 1 asc  ";
	}
	else
	{
		$q = "   SELECT Concod from " . $wbasedato . "_000008 "
		. "    WHERE Conind = '-1' "
		. "      AND Concar = 'on' "
		. "      AND Conest = 'on' ";

		$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;
		$row2 = mysql_fetch_array($res1);

		//Agosto 9 de 2018	Se corrige query
		$q = " SELECT distinct Plocod "
		. "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007, " . $wbasedato . "_000004 "
		. "    WHERE " . $cadena . " "
		. "            Mencon= '" . $row2[0] . "' "
		. "            and Mencco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "            and Menccd = '" . $destino . "' "
		. "            and Mdecon = Mencon "
		. "            and Mdedoc = Mendoc "
		. "  		   and Mdeart = Plopro "
		. "            and Mdeest = 'on' "
		. "            and Menest = 'on' "
		. "            and Mdenlo = CONCAT( Plocod, '-', Plopro ) "
		. "            and Plopro = '" . $parbus . "' "
		. "        ORDER BY " . $wbasedato . "_000004.id desc";
	}

	$res = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);

			$consultas[$inicio] = $row['Plocod'];
			$inicio++;
		}
		return $consultas;
	}

	if (!isset($consultas[0]))
	{
		return false;
	}
	else
	{
		return $consultas;
	}
}

/**
    * A partir de un numero de movimiento se consulta toda la info de ese movimiento
    * 
    * Pide:
    * 
    * @param caracter $concepto concepto del movimeitno a consultar
    * @param caracter $consecutivo numero del movimiento a consultar
    * 
    * devuelve:
    * @param date $fecha , fecha del movimeinto
    * @param caracter $tipo tipo de movimiento
    * @param vector $ccos vector con centro de costos desde donde se carga
    * @param vector $destinos o centro de costos de la averia o historia clinica con ingreso
    * @param caracter $historia numero de historia clinica
    * @param caracter $estado si se anulo o esta activo
    * @param caracter $cco centro de costos desde donde se carga
    * @param caracter $ingreso nuemro de ingreso
    * @param vector $insumos producto que se cargo
    * @param vector $lotes lista de lotes o lote que se cargo o devolvio
    * @param vector $unidades lista de unidad minima de trabajo del insumo
    * @param vector $inslis lista de insumos del producto
    */
function consultarTraslado($concepto, $consecutivo, $fecha, &$tipo, &$ccos, &$destinos, &$historia, &$estado, &$cco, &$ingreso, &$mart, &$mlot, &$mpre, &$mcan, &$mpaj, &$mcaj, &$mmov1, &$mmov2, $y)
{
	global $conex;
	global $wbasedato;

	$q = "SELECT Mdeart, Mdecan, Mdepre, Mdenlo, Mencco, Menccd, Menest, Artcom, Artgen, Artuni, Unides, Mdecaj, Mdepaj, Mendan "
	. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000006, " . $wbasedato . "_000002, movhos_000027 "
	. "   WHERE Mdecon = '" . $concepto . "' "
	. "     AND Mdedoc = '" . $consecutivo . "' "
	. "     AND Mdecon = Mencon "
	. "     AND Mdedoc = Mendoc "
	. "     AND Mdeart = Artcod "
	. "     AND Artest = 'on' "
	. "     AND Unicod = Artuni "
	. "     AND Uniest = 'on'";

	$res = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = $y;$i < $num + $y;$i++)
		{
			$row = mysql_fetch_array($res);

			$exp = explode('-', $row['Mencco']);
			if (isset($exp[1]))
			{
				$historia = $exp[0];
				$ingreso = $exp[1];

				$q = "SELECT Cconom  "
				. "     FROM   movhos_000011 "
				. "   WHERE Ccocod = '" . $row['Menccd'] . "' "
				. "     AND Ccoest = 'on' ";
				$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
				$row1 = mysql_fetch_array($res1);

				$destinos[0] = $row['Menccd'] . '-' . $row1['Cconom'];
				$cco = $row['Menccd'] . '-' . $row1['Cconom'];
			}
			else
			{
				$q = "SELECT Cconom  "
				. "     FROM   movhos_000011 "
				. "   WHERE Ccocod = '" . $row['Mencco'] . "' "
				. "     AND Ccoest = 'on' ";
				$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
				$row1 = mysql_fetch_array($res1);

				$ccos[0] = $row['Mencco'] . '-' . $row1['Cconom'];
				$cco = $row['Mencco'] . '-' . $row1['Cconom'];

				$exp = explode('-', $row['Menccd']);
				$historia = $exp[0];
				$ingreso = $exp[1];
			}

			if ($row['Menest'] == 'on')
			{
				$estado = 'Creado';
			}
			else
			{
				$estado = 'Desactivado';
			}

			$exp = explode('-', $row['Mdenlo']);
			if (isset($exp[1]))
			{
				$mart[$i] = $exp[1];
				$mlot[$i] = $exp[0];
			}
			else
			{
				$mart [$i] = $row['Mdeart'];
				$mlot [$i] = '';
			}

			$mpre[$i] = $row['Mdepre'];

			$q = "SELECT Artcom "
			. "     FROM   " . $wbasedato . "_000002, movhos_000027, " . $wbasedato . "_000001 "
			. "   WHERE Artcod='" . $mart[$i] . "' "
			. "     AND Unicod = Artuni "
			. "     AND Tipcod = Arttip "
			. "     AND Tipest = 'on' ";

			$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
			$num1 = mysql_num_rows($res1);
			$row1 = mysql_fetch_array($res1);

			$mart[$i] = $mart[$i] . '-' . $row1[0];
			$mcan[$i] = $row['Mdecan'];
			$mcaj[$i] = $row['Mdecaj'];
			$mpaj[$i] = $row['Mdepaj'];
			$mmov1[$i] = $row['Mendan'];

			$exp = explode('-', $mmov1[$i]);
			$q = "SELECT Mendan "
			. "     FROM    " . $wbasedato . "_000006 "
			. "   WHERE Mencon='" . $exp[0] . "' "
			. "     AND Mendoc = '" . $exp[1] . "' ";

			$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
			$num1 = mysql_num_rows($res1);
			$row1 = mysql_fetch_array($res1);

			$mmov2[$i] = $row1['Mendan'];
		}
		return true;
	}
	else
	{
		return false;
	}
}

/**
    * Consulta las difernetes presntaciones para un insumo
    * 
    * @param caracter $codigo codigo del insumo
    * @param caracter $cco centro de costos(codigo-descripcion)
    * @param caracter $unidad presentacion del insumo previamente seleccionado por el usuario
    * @return vector lista de presentaciones del insumo
    */
function consultarUnidades($codigo, $cco, $unidad)
{
	global $conex;
	global $wbasedato;

	if ($unidad != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		// consulto los conceptos
		$q = " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi "
		. "        FROM  " . $wbasedato . "_000009, movhos_000026 "
		. "      WHERE Apppre='" . $unidad . "' "
		. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "            and Appest='on' "
		. "            and Apppre=Artcod "
		. "            and Appcod='". $codigo ."' ";

		$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
		$num1 = mysql_num_rows($res1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($res1);
			$enteras = floor($row1['Appexi'] / $row1['Appcnv']);
			$fracciones = $row1['Appexi'] % $row1['Appcnv'];
			$unidades[0] = $row1['Apppre'] . '-' . str_replace('-', ' ', $row1['Artcom']) . '-' . str_replace('-', ' ', $row1['Artgen']);
			$cadena = "Apppre != '" . $unidad . "' AND";
			$inicio = 1;
		}
		else
		{
			$cadena = '';
			$inicio = 0;
		}
	}
	else
	{
		$cadena = '';
		$inicio = 0;
	}
	// consulto los conceptos
	$q = " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi "
	. "        FROM  " . $wbasedato . "_000009, movhos_000026 "
	. "      WHERE " . $cadena . " "
	. "             Appcod='" . $codigo . "' "
	. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
	. "            and Appest='on' "
	. "            and Apppre=Artcod ";

	$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0;$i < $num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);

			$enteras = floor($row1['Appexi'] / $row1['Appcnv']);
			$fracciones = $row1['Appexi'] % $row1['Appcnv'];
			$unidades[$inicio] = $row1['Apppre'] . '-' . str_replace('-', ' ', $row1['Artcom']) . '-' . str_replace('-', ' ', $row1['Artgen']);
			$inicio++;
		}
		return $unidades;
	}

	if (!isset($unidades[0]))
	{
		return false;
	}
	else
	{
		return $unidades;
	}
}

/**
    * Se buscan los movimientos realizados segun parametros de busqueda ingresados por el usuario
    * 
    * @param caracter $parcon valor de busqueda 1
    * @param caracter $parcon2 valor de busqueda dos (por ejemplo si se busca por historia es fehca incial)
    * @param caracter $parcon3 valor de busqueda tres (por ejemplo si se busca por historia es fehca final)
    * @param caracter $insfor si se busca por articulo, es forma de busqueda del articulo
    * @param caracter $forcon forma de busqueda del movimiento
    * @return vector , lista de movimeitos encontrados para desplegar en dropdown
    */
function BuscarTraslado($parcon, $parcon2, $parcon3, $insfor, $forcon, $accion)
{
	global $conex;
	global $wbasedato;

	if ($accion == 'Cargo')
	{
		$signo = -1;
	}
	else
	{
		$signo = 1;
	}

	switch ($forcon)
	{
		case 'Numero de movimiento':
		$exp = explode('-', $parcon);
		if (isset($exp[1]))
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdedoc = '" . $exp[1] . "' "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on'  "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdedoc = '" . $parcon . "' "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Articulo':
		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon2 = date('Y-m') . '-01';
		}

		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon3 = date('Y-m-d');
		}

		if ($insfor == 'Codigo')
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdeart like '%" . $parcon . "%' "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND  Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor == 'Nombre comercial')
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data"
			. "     FROM   " . $wbasedato . "_000007 A , " . $wbasedato . "_000002, " . $wbasedato . "_000008 "
			. "   WHERE Artcom like '%" . $parcon . "%' "
			. "   AND Artest = 'on' "
			. "   AND Mdeart = Artcod "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data"
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000002, " . $wbasedato . "_000008 "
			. "   AND Artgen like '%" . $parcon . "%' "
			. "   AND Artest = 'on' "
			. "   AND Mdeart = Artcod "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Historia':
		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon2 = date('Y-m') . '-01';
		}

		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon3 = date('Y-m-d');
		}

		$q = "SELECT Mdecon, Mdedoc, Menfec "
		. "     FROM   " . $wbasedato . "_000007, " . $wbasedato . "_000006, " . $wbasedato . "_000008 "
		. "   WHERE (mid(Mencco,1,instr(Mencco,'-')-1) = '" . $parcon . "' OR mid(Menccd,1,instr(Menccd,'-')-1) = '" . $parcon . "') "
		. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
		. "     AND Menest = 'on' "
		. "     AND Mencon = Mdecon "
		. "     AND Mendoc = Mdedoc "
		. "     AND Mdeest = 'on' "
		. "     AND Mdecon = Concod "
		. "     AND Concar = 'on' "
		. "    AND Conind = '" . $signo . "'  "
		. "     GROUP BY 1, 2, 3 ";

		break;

		default:
		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon2 = date('Y-m') . '-01';
		}

		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon3 = date('Y-m-d');
		}

		if ($insfor == 'Codigo')
		{
			$q = "SELECT Mdecon, Mdedoc, Menfec "
			. "     FROM  " . $wbasedato . "_000007, " . $wbasedato . "_000006, " . $wbasedato . "_000008 "
			. "   WHERE ( Menccd = '" . $parcon . "' or Mencco = '" . $parcon . "') "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor == 'Nombre')
		{
			$q = "SELECT Mdecon, Mdedoc, Menfec "
			. "     FROM   " . $wbasedato . "_000007, " . $wbasedato . "_000006, movhos_000011, " . $wbasedato . "_000008 "
			. "   WHERE Cconom like '%" . $parcon . "%' "
			. "     AND Ccoest = 'on' "
			. "     AND (Menccd = Ccocod or Mencco = Ccocod) "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;
	}

	$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0; $i < $num1; $i++)
		{
			$row1 = mysql_fetch_array($res1);

			$q = "SELECT Mencon, Mendoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
			. "   WHERE Mendan = '" . $row1[0] . "-" . $row1[1] . "' "
			. "     AND Menest = 'on' "
			. "     AND Mencon = Concod "
			. "     AND Conane = 'on'  "
			. "    AND Conind = '" . ($signo * -1) . "'  ";

			$res3 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
			$num3 = mysql_num_rows($res3);
			$row3 = mysql_fetch_array($res3);

			if ($num3 > 0)
			{
				$consultas[$i] = $row3[0] . '-' . $row3[1] . '-(' . $row1[2] . ')';
			}
			else
			{
				return '';
			}
		}
		return $consultas;
	}
	else
	{
		return '';
	}
}

/**
    * Se buscan los movimientos realizados segun parametros de busqueda ingresados por el usuario
    * 
    * @param caracter $parcon valor de busqueda 1
    * @param caracter $parcon2 valor de busqueda dos (por ejemplo si se busca por historia es fehca incial)
    * @param caracter $parcon3 valor de busqueda tres (por ejemplo si se busca por historia es fehca final)
    * @param caracter $insfor si se busca por articulo, es forma de busqueda del articulo
    * @param caracter $forcon forma de busqueda del movimiento
    * @return vector , lista de movimeitos encontrados para desplegar en dropdown
    */
function BuscarTraslado2($numtra, $accion, $historia, $ingreso, $wusuario, &$fecha, &$mov)
{
	global $pda;
//	if( isset($pda) && $pda == 'on' ){
//		return;
//	}
	
	global $conex;
	global $wbasedato;

	if ($accion == 'Cargo')
	{
		$signo = -1;
	}
	else
	{
		$signo = 1;
	}

	$exp = explode('-', $numtra);

	$q = "SELECT A.id "
	. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
	. "   WHERE Mendoc = '" . $exp[1] . "' "
	. "     AND Menest = 'on' "
	. "     AND Mencon = Concod "
	. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
	. "     AND Concar = 'on'  "
	. "    AND Conind = '" . $signo . "'  ";

	$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
	$row = mysql_fetch_array($res1);

	if ($row[0] != '')
	{
		$q = "SELECT Mencon, Mendoc, A.Fecha_data "
		. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
		. "   WHERE A.id >= '" . $row[0] . "' "
		. "     AND Menest = 'on' "
		. "     AND Mencon = Concod "
		. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
		. "     AND A.Seguridad = 'C-" . $wusuario . "' "
		. "     AND Concar = 'on'  "
		. "    AND Conind = '" . $signo . "'  ";

		$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
		$num1 = mysql_num_rows($res1);
		if ($num1 > 0)
		{
			for ($i = 0; $i < $num1; $i++)
			{
				$row = mysql_fetch_array($res1);
				$fecha[$i] = $row[2];

				$q = "SELECT Mencon, Mendoc "
				. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
				. "   WHERE  Menest = 'on' "
				. "     AND Mencon = Concod "
				. "     AND Mendan = '" . $row[0] . "-" . $row[1] . "' "
				. "     AND A.Seguridad = 'C-" . $wusuario . "' "
				. "     AND Conane = 'on'  "
				. "    AND Conind = '" . - $signo . "'  ";

				$res3 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
				$row3 = mysql_fetch_array($res3);
				$mov[$i] = $row3[0] . '-' . $row3[1];
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		$mov = '';
		return true;
	}
}

function BuscarTraslado3($accion, $historia, $ingreso, $wusuario, &$fecha, &$mov)
{	
	global $pda;
	if( isset($pda) && $pda == 'on' ){
		return;
	}
	
	global $conex;
	global $wbasedato;

	if ($accion == 'Cargo')
	{
		$signo = -1;
	}
	else
	{
		$signo = 1;
	}

	$q = "SELECT A.id "
	. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
	. "   WHERE A.Fecha_data = '" . date('Y-m-d') . "' "
	. "     AND Menest = 'on' "
	. "     AND Mencon = Concod "
	. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
	. "     AND Concar = 'on'  "
	. "    AND Conind = '" . $signo . "'  ";

	$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;
	$row = mysql_fetch_array($res1);

	if ($row[0] != '')
	{
		$q = "SELECT Mencon, Mendoc, A.Fecha_data "
		. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
		. "   WHERE A.Fecha_data = '" . date('Y-m-d') . "' "
		. "     AND Menest = 'on' "
		. "     AND Mencon = Concod "
		. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
		. "     AND Concar = 'on'  "
		. "    AND Conind = '" . $signo . "'  "
		. "    order by  A.id  desc ";

		$res1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;;
		$num1 = mysql_num_rows($res1);
		if ($num1 > 0)
		{
			for ($i = 0; $i < $num1; $i++)
			{
				$row = mysql_fetch_array($res1);
				$fecha[$i] = $row[2];

				$q = "SELECT Mencon, Mendoc "
				. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
				. "   WHERE  Menest = 'on' "
				. "     AND Mencon = Concod "
				. "     AND Mendan = '" . $row[0] . "-" . $row[1] . "' "
				. "     AND Conane = 'on'  "
				. "    AND Conind = '" . - $signo . "'  ";

				$res3 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;;
				$row3 = mysql_fetch_array($res3);
				$mov[$i] = $row3[0] . '-' . $row3[1];
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		$mov = '';
		return true;
	}
}

/**
    * Se valida que la historia este activa y que no este en procesos de alta
    * 
    * @param caracter $cco centro de costos (codigo-nombre)
    * @param caracter $historia numero de historia clinica
    * @param caracter $ingreso numero de ingreso, se consulta a partir de la validacion
    * @param caracter $mensaje mensaje que devuelve la funcion en caso de no encontrar la historia
    * @param caracter $nombre nombre del paciente que se extrae de la validacion
    * @param caracter $habitacion habitacion del paciente que se extrae de la validacion
    * @return boolean si encuentra la historia reotrna true
    */
function validarHistoria($cco, $historia, &$ingreso, &$mensaje, &$nombre, &$habitacion, &$servicio)
{
	global $conex;
	global $wbasedato;

	if( empty($wemp_pmla) ){
		$wemp_pmla = '01';
	}
	
	if ($historia == '0')
	{
		$q = " SELECT Ccohcr "
		. "     FROM movhos_000011 "
		. "   WHERE	Ccocod = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "     AND	Ccoest = 'on'";

		$err = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;;
		$row = mysql_fetch_array($err);

		if ($row['hcr'] == 'on')
		{
			$ingreso = '';
			$habitacion = '';
			$servicio = '';
			$val = true;
		}
		else
		{
			$mensaje = "ESTE CENTRO DE COSTOS NO PERMITE CARGOS A  LA HISTORIA CERO";
			return (false);
		}
	}
	else if (is_numeric($historia))
	{
		$q = "SELECT Oriing, Pacno1, Pacno2, Pacap1, Pacap2 "
		. "      FROM root_000037, root_000036 "
		. "     WHERE Orihis = '" . $historia . "' "
		. "       AND Oriori = '01' "
		. "       AND Oriced = Pacced ";

		$err = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;;
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$ingreso = $row['Oriing'];
			$nombre = $row['Pacno1'] . ' ' . $row['Pacno2'] . ' ' . $row['Pacap1'] . ' ' . $row['Pacap2'];

			/****************************************************************************************
			 * Marzo 18 de 2013
			 ****************************************************************************************/
			$q = "SELECT * "
			. "      FROM movhos_000018 "
			. "     WHERE Ubihis = '" . $historia . "' "
			. "       AND Ubiing = '" . $ingreso . "' ";
			//. "       AND Ubiald <> 'on' ";

			$err1 = mysql_query($q, $conex) or die(mysql_errno()." - en el query: ".$q." - ".mysql_error());;;;
			$num1 = mysql_num_rows($err1);
			
			if( $num1 > 0 ){
				$row = mysql_fetch_array($err1);
				
				if( $row['Ubiald'] == 'on' ){
					 
					if( esUrgenciasIncCM($row['Ubisac']) && $row[ 'Ubifad' ] != "0000-00-00" ){
						//convierto la fecha y hora de alta en tiempo Unix
						$fechorAlta = strtotime( $row[ 'Ubifad' ]." ".$row[ 'Ubihad' ] )+consultarValorPorAplicacion( $conex, $wemp_pmla, 'tiempoEgresoUrgencia' )*3600;
						
						if( time() <= $fechorAlta ){
							$row['Ubiald'] = 'off';
						}
						else{
							$num1 = 0;
						}
					}
					else{
						$num1 = 0;
					}
				}
			}
			/****************************************************************************************/
			
			if ($num1 > 0)
			{
				//$row = mysql_fetch_array($err1);
				if ($row['Ubiptr'] != 'on')
				{
					$habitacion = $row['Ubihac'];
					$servicio = $row['Ubisac'];
					if ($row['Ubialp'] == 'on')
					{
						$mensaje = "EL PACIENTE ESTA EN PROCESO DE ALTA";
					}
					else
					{
						$mensaje = '';
					}
					return (true);
				}
				else
				{
					// 2013-06-18
					// Si está en urgencias no valide si el paciente está en proceso de traslado 
					if( !esUrgenciasIncCM($row['Ubisac']) && !esUrgenciasIncCM($row['Ubisan']) ){
						$mensaje = "EL PACIENTE ESTA EN PROCESO DE TRASLADO";
						return(false);
					} else {
						if(esUrgenciasIncCM($row['Ubisac']))
							$servicio = $row['Ubisac'];
						elseif(esUrgenciasIncCM($row['Ubisan']))
							$servicio = $row['Ubisan'];
						return (true);
					}
				}
			}
			else
			{
				$mensaje = "EL PACIENTE TIENE ALTA DEFINITIVA";
				return(false);
			}
		}
		else
		{
			$mensaje = "EL PACIENTE NO SE ENCUENTRA ACTIVO";
			return (false);
		}
	}
	else if (!is_numeric($historia))
	{
		$mensaje = "LAS HISTORIAS CLINICAS DEBEN SER NUMERICAS";
		return (false);
	}
	return(true);
}
// ----------------------------------------------------------funciones de presentacion------------------------------------------------
/**
    * Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
    */
function pintarTitulo($tipo)
{
	global $pda;
	
	if( isset($pda) && $pda == 'on' ){
		return;
	}
	
	echo "<table ALIGN=CENTER width='50%'>";
	// echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='titulo1'>PRODUCCION CENTRAL DE MEZCLAS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";

	if ($tipo == 'C')
	{
		echo "<table ALIGN=CENTER width='90%' >";
		// echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
		echo "<tr><td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='cen_Mez.php?wbasedato=cen_mez'>PRODUCTOS</a></td>";
		echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='lotes.php?wbasedato=lotes.php'>LOTES</a></td>";
		echo "<td class='texto6' width='15%'><a style='text-decoration:none;color:white' href='cargoscm.php?wbasedato=lotes.php&tipo=C'>CARGOS A PACIENTES</a></td>";
		echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='pos.php?wbasedato=lotes.php&tipo=A'>VENTA EXTERNA</a></td></TR>";
		// echo "<a href='cargos.php?wbasedato=lotes.php&tipo=A'><td class='texto5' width='15%'>AVERIAS</td></a>";
		// echo "<a href='descarte.php?wbasedato=cenmez'><td class='texto5' width='15%'>DESCARTES</td></TR></a>";
		echo "<tr><td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td></tr></table>";
	}
}

/**
    * funcion que pinta formulario de html para la busqueda de un documento determinado
    * 
    * @param vector $consultas vectorpara desplegar dropdown con lista de movimientos encontrados sgun parametros de usuario
    * @param caracter $forcon forma de busqueda escogida anterirormente por el usuario
    * @param carater $tipo si es un cargo a paciente o en una averia
    */
function pintarBusqueda( $consultas, $forcon, $tipo, $accion)
{
	global $pda;
	
	if( isset($pda) && $pda == 'on' ){
		return;
	}
	
	if( empty($consultas) ){
		$consultas[0]= array();
	}
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<form name='producto2' action='cargoscpxcm.php' method=post>";
	echo "<tr><td class='titulo3' colspan='3' align='center'>Consulta: ";
	echo "<select name='forcon' class='texto5' onchange='enter7()'>";
	echo "<option>" . $forcon . "</option>";
	if ($forcon != 'Numero de movimiento')
	echo "<option>Numero de movimiento</option>";

	if ($forcon != 'Historia')
	echo "<option>Historia</option>";

	if ($forcon != 'Articulo')
	echo "<option>Articulo</option>";
	echo "</select>";
	switch ($forcon)
	{
		case '':
		echo "&nbsp";
		break;

		case 'Numero de movimiento':
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		break;

		case 'Articulo':
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de " . $forcon . ": ";
		echo "<select name='insfor' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre comercial</option>";
		echo "<option>Nombre genérico</option>";
		echo "</select>";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;

		case 'Historia':
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de " . $forcon . ": ";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;

		default:
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de " . $forcon . ": ";
		echo "<select name='insfor' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre</option>";
		echo "</select>";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;
	}

	echo "&nbsp; Resultados: <select name='consulta' class='texto5' onchange='enter7()'>";
	
	if ($consultas[0]['cod'] != '')
	{
		for ($i = 0;$i < count($consultas);$i++)
		{
			echo "<option>" . $consultas[$i] . "</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<input type='hidden' name='tipo' value='" . $tipo . "'>";
	echo "<input type='hidden' name='accion' value='" . $accion . "'>";
	echo "</form>";
}

/**
    * Se pinta el formulario de ingreso de historia clinica
    * 
    * @param unknown_type $estado 
    * @param unknown_type $ccos 
    * @param unknown_type $numtra 
    * @param unknown_type $tipo 
    * @param unknown_type $historia 
    * @param unknown_type $destinos 
    * @param unknown_type $fecha 
    * @param unknown_type $ingreso 
    * @param unknown_type $nombre 
    * @param unknown_type $habitacion 
    * @param unknown_type $crear 
    * @param unknown_type $numcan 
    */
function pintarFormulario($estado, $ccos, $historia, $destinos, $fecha, $ingreso, $nombre, $habitacion, $tipo, $accion, $boton, $numtra, $aplica, $carro)
{
	global $pda;
	global $user;
	global $wusuario;
	global $mostrarMensajeSolicitudCamillero;

	
	if( isset($pda) && $pda == 'on' ){
		
		if( $wusuario != '' && validarUsuario( $wusuario ) ){
		
			echo "<form name='producto' action='cargoscpxcm.php' method=post>";
			echo "<INPUT type='hidden' value='on' name='pda'>";
			echo "<INPUT type='hidden' name='user' value='$user'>";
			
			if( $boton == 1 )
			{
				echo "<table border=0 ALIGN=CENTER width=270>";
				echo "<td class='texto1' colspan='1' align='center'>Numero de historia: <input type='TEXT' name='historia' value='" . $historia . "' size=10 class='texto5'> ";
			
				echo "<tr>";
			
				echo "<td class='texto2' colspan='3' align='left'><input type='SUBMIT' name='ok' value='OK'></td></tr>";
			}
			else{
				echo "<INPUT type='hidden' name='historia' value='$historia'>";
			}
			
			echo "</table>";
		}
		else{
			?>
			<script>
				alert( "El usuario no se encuentra registrado" );
				location.href = "cargoscm.php?pda=on";
			</script>
			<?php 
//			echo "<form name='producto' action='cargos.php' method=post>";
//			echo "<INPUT type='hidden' value='on' name='pda'>";
//			pedirUsuario();
			
		}
		
		echo "<input type='hidden' name='numtra' value='" . $numtra . "'></td>";
	}
	else{
	
		echo "<form name='producto3' action='cargoscpxcm.php' method=post>";
		echo "<input type='hidden' name='tipo' value='" . $tipo . "'>";
		$url = "cargoscm.php?wbasedato=lotes&tipo=$tipo&accion=$accion";
		echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='BUTTON' NAME='NUEVO' VALUE='Nuevo' class='texto5' onClick='javascript:redireccionar(\"$url\")'></td></tr>";
		echo "<input type='hidden' name='tipo' value='" . $tipo . "'></td>";
		echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
		echo "</table></form>";
	
		echo "<form name='producto' action='cargoscpxcm.php' method=post>";
		echo "<table border=0 ALIGN=CENTER width=90%>";
	
		echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general del " . $accion . "</b></td></tr>";
		echo "<tr><td class='texto1' colspan='2' align='center'>Centro de costos de origen: ";
	
		echo "<select name='cco' class='texto5' onchange='enter8()'>";
		if ($ccos[0] != '')
		{
			for ($i = 0;$i < count($ccos);$i++)
			{
				echo "<option >" . $ccos[$i] . "</option>";
			}
		}
		else
		{
			echo "<option value=''></option>";
		}
		echo "</select>";
		echo "</td>";
	
		echo "<td class='texto1' colspan='1' align='center'>Numero de historia: <input type='TEXT' name='historia' value='" . $historia . "' size=10 class='texto5'> ";
	
		echo "</td></tr>";
	
		echo "<tr><td class='texto2' colspan='1' align='left'>Numero de Ingreso: <input type='TEXT' name='ingreso' value='" . $ingreso . "' readonly='readonly' class='texto2' size='5'></td>";
		echo "<td class='texto2' colspan='1' align='left'>Habitacion: <input type='TEXT' name='habitacion' value='" . $habitacion . "' readonly='readonly' class='texto2' size='5'></td>";
		if($aplica)
		{
			echo "<td class='titulo5' colspan='1' align='left'>Nombre: <input type='TEXT' name='nombre' value='" . $nombre . "' readonly='readonly' class='texto2' size='40'></td></tr>";
		}
		else
		{
			echo "<td class='titulo4' colspan='1' align='left'>Nombre: <input type='TEXT' name='nombre' value='" . $nombre . "' readonly='readonly' class='texto2' size='40'></td></tr>";
		}
		echo "<tr>";
		if ($boton == 1)
		{
			echo "<td class='texto2' colspan='3' align='left'><input type='SUBMIT' name='ok' value='OK'></td></tr>";
		}
		
		/*if($accion=='Cargo')
		{
			if($carro=='on')
			{
				echo "<tr><td colspan=3 class='titulo3' align='center'><b><input type='checkbox' name='carro' checked>Para carro de dispensacion</b></td></tr>";
		
			}
			else 
			{
				echo "<tr><td colspan=3 class='titulo3' align='center'><b><input type='checkbox' name='carro'>Para carro de dispensacion</b></td></tr>";
			}
		}
		else 
		{
			echo "<input type='hidden' name='nada' value=''></td>";
		}*/
	
		echo "<input type='hidden' name='estado' value='" . $estado . "'></td>";
		echo "<input type='hidden' name='tipo' value='" . $tipo . "'></td>";
		echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
		echo "<input type='hidden' name='numtra' value='" . $numtra . "'></td>";
		echo "<input type='hidden' name='tvh' value='0'></td>";
		echo "<input type='hidden' name='grabar' value='0'></td>";
		echo "<input type='hidden' name='grabar' value='0'></td>";
		echo "</table></br>";
		
		if( $mostrarMensajeSolicitudCamillero == 'on' ){
			
			echo "<table align='center'>";
			echo "<tr>";
			echo "<td class='titulo4'>";
			echo "<font size='4'>SE HA SOLICITADO CAMILLERO</font>";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
		} 
	
	}
	
	
}

/**
    * Se pienta formulario de lista de insumos
    * 
    * @param unknown_type $insumos 
    * @param unknown_type $inslis 
    * @param unknown_type $unidades 
    * @param unknown_type $lotes 
    * @param unknown_type $presentaciones 
    * @param unknown_type $tipo 
    * @param unknown_type $preparacion 
    * @param unknown_type $escogidos 
    * @param unknown_type $numcan 
    */
function pintarInsumos($insumos, $unidades, $lotes, $tipo, $escogidos, $accion, $cco, $historia, $ingreso, $servicio, $confm, $confc, $carro)
{
	global $cantidad;
	global $wusuario;
	global $habitacion;
	global $nombre; 
	global $solCamilleroPedidoOn;
	global $mart;
	global $numMovAnt;
	global $conex;
	global $bd;
	global $tempRonda;
	global $tmpDispensacion;
//	global $txCanMMQ;
	
	if( empty($insumos[0]['lot']) ) {
		$insumos[0]['lot']='';		
	}
	
	if( empty($insumos[0]['cdo']) ) {
		$insumos[0]['cdo']='';		
	}
	
	if( empty($insumos[0]['cod']) ) {
		$insumos[0]['cod']='';		
	}

	if( empty($unidades[0]) ){
		$unidades[0] = '';
	}
	
	//Precondiciones de grabado pare el KE
	$packe = array();
	$packe['his'] = $historia;
	$packe['ing'] = $ingreso;
	condicionesKE( $packe, $insumos[0]['cod'] );
	
//	if( $packe['ke'] ){
//		$listart = ArticulosXPaciente( $historia, $ingreso );
//	}
	
	if( !$packe['ke'] ){
		
		global $solCamillero;
		
		//Indica cuantos fueron los movimientos anteriores realizados
		echo "<INPUT type='hidden' value='".count($mart)."' name='numMovAnt'>";
		
		if( $solCamillero == 'on' ){
			echo "<INPUT type='hidden' value='on' name='solCamillero'>";
		}
		
		/*******************************************************
		 * PACIENTE SIN KARDEX ELECTRONICO
		 *******************************************************/
		
		echo "<table border=0 ALIGN=CENTER width=90%>";
		echo "<tr><td colspan='6' class='titulo3' align='center'><b>DETALLE DEL ARTICULO</b></td></tr>";
	
		echo "<tr><td class='texto1' colspan='6' align='center'>Buscar Articulo por: ";
		echo "<select name='forbus2' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Rotulo</option>";
		echo "<option>Nombre comercial</option>";
		echo "<option>Nombre generico</option>";
		echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar'  class='texto5'></td> ";
		echo "<tr><td class='texto1' colspan='4' align='center'>Articulo: <select name='insumo' class='texto5' onchange='enter1()'>";
		if ($insumos != '' || !empty($insumos) )
		{
			for ($i = 0;$i < count($insumos);$i++)
			{
				echo "<option value='" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "-" . $insumos[$i]['gen'] . "-" . $insumos[$i]['pre'] . "-" . $insumos[$i]['lot'] . "-" . $insumos[$i]['est'] . "'>" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "</option>";
			}
			echo "<input type='hidden' name='insumos[0]['lot']' value='" . $insumos[0]['lot'] . "'></td>";
			echo "<input type='hidden' name='insumos[0]['cdo']' value='" . $insumos[0]['cdo'] . "'></td>";
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td>";
		
		if ($insumos[0]['lot'] == 'on')
		{
			echo "<td class='texto1' colspan='2' align='center'>Lote: <select name='lote' class='texto5'>";
			if (is_array($lotes))
			{
				for ($i = 0;$i < count($lotes);$i++)
				{
					echo "<option>" . $lotes[$i] . "</option>";
				}
			}
			else
			{
				echo "<option ></option>";
			}
			echo "</select></td></tr>";
			$ind = 0;
			
		}
		else if (is_array($unidades))
		{
			echo "<input type='hidden' name='lote' value=''>";
			echo "<td class='texto1' colspan='2' align='center'>Presentacion: <select name='prese' class='texto5' onchange='enter1()'>";
			if ($unidades[0] != '')
			{
				for ($i = 0;$i < count($unidades);$i++)
				{
					echo "<option>" . $unidades[$i] . "</option>";
				}
			}
			else
			{
				echo "<option ></option>";
			}
			echo "</select></td></tr>";
			$ind = 0;
		}
		else
		{
			echo "<td class='texto1' colspan='2' align='center'>&nbsp;</td></tr>";
		}
		
		echo "<tr><td colspan='6' class='titulo3' align='center'>&nbsp</td></tr>";
	
		if ( ($insumos != '' and ($lotes[0] != '' or $unidades[0] != '')))
		{ 
			if ($lotes[0] != '')
			{
				$var = $lotes[0];
			}
			else
			{
				$var = urlencode($unidades[0]);
			}
			if( $insumos[0]['cdo']=='on' )
			{
				if ($accion == 'Cargo')
				{
					$grab=cargoCM($insumos[0]['cod'], $cco, $var, $historia, $ingreso, $servicio, $error, $carro);
					if($grab)
					{
						echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
						echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
					}
					else
					{
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
					
//					if( $grab && $solCamillero != 'on' ){
//
//						$nomCcoDestino = nombreCcoCentralCamilleros( $pac['sac'] );
//						$motivo = 'DESPACHO DE MEDICAMENTOS';
//						crearPeticionCamillero( nombreCcoCentralCamilleros( $cco['cod'] ), $motivo, "<b>Hab: ".$habitacion."</b><br>".$nombre, $nomCcoDestino, $wusuario, $nomCcoDestino, buscarCodigoNombreCamillero() );
//						echo "<INPUT type='hidden' value='on' name='solCamillero'>";
//					}
				}
				else
				{
					$exp = explode('-', $cco);
					$ccs['cod']=$exp[0];
					$ccs['nom']=$exp[1];
	
					$art['cod']=$insumos[0]['cod'];
					$art['nom']=$insumos[0]['nom'];
					$art['can']=1;
	
					$pac['his']=$historia;
					$pac['ing']=$ingreso;
					$pac['sac']=$servicio;
					
					descargarCarro2( $art['cod'], $art['cod'], $historia, $ingreso, $cco );
					
					$grab=devolucionCM($ccs, $art, $pac, $error, $dronum, $drolin, $carro);
	
					if($grab)
					{
						echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
						echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
					}
					else
					{
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
				}
	
										?>
										<script>
										document . producto . insumo.options[document.producto.insumo.selectedIndex].value='';
										document . producto . submit();
	                                     </script >
	                                    <?php
			}
			else
			{
				if( esMMQ( $insumos[0]['cod'] ) && $accion == 'Cargo' ){
					if( true || !isset( $txCanMMQ ) || empty($txCanMMQ) ){
						echo "<tr><td colspan='8' align='center' class='texto1'><b>Cantidad a cargar: </b><INPUT type='text' name='txCanMMQ' id='txCanMMQ' class='texto5'></td></tr>";
						echo "<tr><td colspan='8' align='center' class='texto1'><INPUT type='button' class='texto5' value='Cargar' onClick='cargarMultiMMQ( \"cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "\" )'></td></tr>";
					}
//					else{
//						echo "<script>";
//						echo "window.open('cargo.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "&canMMQ=$txCanMMQ');";
//						echo "docuemnt.producto.submit();";
//						echo "</script>";
//					}
				}
				else{
					if($accion == 'Cargo' )
					{ 
						echo "<td class='texto2' colspan='6' align='left'><a href='cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "' target='_blank'><font size='4'>CARGAR</a></td></tr>";
					}
					else
					{
						echo "<td class='texto2' colspan='6' align='left'><a href='devolucioncpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . " ' target='_blank'><font size='4'>DEVOLVER</a></td></tr>";
					}
				}
			}
		}
		else if ($confm!='')
		{
			echo "<td bgcolor='".$confc."' colspan='6' align='center'><font color='#003366'><b>".$confm."</b></font></td></tr>";
		}
		
		echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
		echo "<input type='hidden' name='realizar' value='0'></td>";
		echo "<input type='hidden' name='eliminar' value='0'></td>";
		echo "</table></br></form>";
	
	}
	else{
		
		$listart = ArticulosXPaciente( $historia, $ingreso );
		
		//Indica cuantos fueron los movimientos anteriores realizados
		echo "<INPUT type='hidden' value='0' name='numMovAnt'>";
		
		/*******************************************************
		 * PACIENTE CON KARDEX ELECTRONICO
		 *******************************************************/
		
		echo "<table border=0 ALIGN=CENTER width=90%>";
		echo "<tr><td colspan='6' class='titulo3' align='center'><b>DETALLE DEL ARTICULO</b></td></tr>";
	
		echo "<tr><td class='texto1' colspan='6' align='center'>Buscar Articulo por: ";
		echo "<select name='forbus2' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Rotulo</option>";
		echo "<option>Nombre comercial</option>";
		echo "<option>Nombre generico</option>";
		echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar'  class='texto5'></td> ";
		echo "<tr><td class='texto1' colspan='2' align='center'>Articulo: <select name='insumo' class='texto5' onchange='enter1()'>";
		if ($insumos != '' || !empty($insumos) )
		{
			for ($i = 0;$i < count($insumos);$i++)
			{
				echo "<option value='" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "-" . $insumos[$i]['gen'] . "-" . $insumos[$i]['pre'] . "-" . $insumos[$i]['lot'] . "-" . $insumos[$i]['est'] . "'>" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "</option>";
			}
			echo "<input type='hidden' name='insumos[0]['lot']' value='" . $insumos[0]['lot'] . "'></td>";
			echo "<input type='hidden' name='insumos[0]['cdo']' value='" . $insumos[0]['cdo'] . "'></td>";
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td>";
		
		if( $insumos[0]['cdo']=='on' && $accion == 'Cargo' && $packe['ke'] ){
			if( $cantidad > $packe['cfd'] ){
				echo "<td class='texto1' colspan='2' align='center'>Cantidad: <INPUT type='text' name='cantidad' value='{$packe['cfd']}' class='texto5'>";
				$cantidad = 0;
			}
			else{
				echo "<td class='texto1' colspan='2' align='center'>Cantidad: <INPUT type='text' name='cantidad' value='{$packe['cfd']}' class='texto5'>";
			}
		}
		else{
			$cantidad = 1; 
		}
		
		if ($insumos[0]['lot'] == 'on')
		{
			echo "<td class='texto1' colspan='2' align='center'>Lote: <select name='lote' class='texto5'>";
			if (is_array($lotes))
			{
				for ($i = 0;$i < count($lotes);$i++)
				{
					echo "<option>" . $lotes[$i] . "</option>";
				}
			}
			else
			{
				echo "<option ></option>";
			}
			echo "</select></td></tr>";
			$ind = 0;
			
		}
		else if (is_array($unidades))
		{
			echo "<input type='hidden' name='lote' value=''>";
			echo "<td class='texto1' colspan='4' align='center'>Presentacion: <select name='prese' class='texto5' onchange='enter1()'>";
			if ($unidades[0] != '')
			{
				for ($i = 0;$i < count($unidades);$i++)
				{
					echo "<option>" . $unidades[$i] . "</option>";
				}
			}
			else
			{
				echo "<option ></option>";
			}
			echo "</select></td></tr>";
			$ind = 0;
		}
		else
		{
			echo "<td class='texto1' colspan='2' align='center'>&nbsp;</td></tr>";
		}
		
		if( $insumos[0]['cdo']=='on' && $accion == 'Cargo' && $packe['ke'] ){
			echo "<tr align=center class='texto1'><td colspan='10'><INPUT type='submit' name='sbGrabar' value='Grabar' class='texto5'>";
		}
	
		echo "<tr><td colspan='6' class='titulo3' align='center'>&nbsp</td></tr>";
	
		if ( ($cantidad > 0 and $insumos != '' and ($lotes[0] != '' or $unidades[0] != '')))
		{
			if ($lotes[0] != '')
			{
				$var = $lotes[0];
			}
			else
			{
				$var = urlencode($unidades[0]);
			}
			
			if( $insumos[0]['cdo']=='on' )
			{
				if ($accion == 'Cargo')
				{
					//Verificando precondiciones para grabar en el KE
					if( $packe['act'] || $packe['gra'] || $packe['con'] || !$packe['nut'] )
					{
						if( $packe['gra'] || $packe['nut'] || $packe[''] || $packe['mmq'] )
						{
							if( $packe['con'] || $packe['nut'] || $packe['mmq'] )
							{
								if( $packe['act'] || $packe['nut'] || $packe['mmq'] )
								{
									if( $packe['art'] || $packe['nut'] || $packe['mmq'] )
									{
										if( $packe['sal'] || $packe['nut'] || $packe['mmq'] )
										{
											for( $i = 0; $i < $cantidad; $i++ ){
											
												$grab=cargoCM($insumos[0]['cod'], $cco, $var, $historia, $ingreso, $servicio, $error, $carro);
												
												if( $grab ){
													$val = registrarArticuloKE( $insumos[0]['cod'], $historia, $ingreso, false, $idKardex, $error['dronum'], $error['drolin'] );
													
													/************************************************************************************************
													 * Mayo 11 de 2011
													 ************************************************************************************************/
													if( $val && !empty($idKardex) && !empty( $error['dronum'] ) && !empty( $error['dronum'] ) ){
														$explodeCco = explode( '-', $cco );
														actualizandoAplicacion( $idKardex, $explodeCco[0], $insumos[0]['cod'], $error['dronum'], $error['drolin'] );
													}
													/************************************************************************************************/
												}
												
												if( $grab && $val )
												{						
													echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
													echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
												}
												else
												{ 
													if( !$val && $grab ){
														$error['ok'] = 'NO SE PUDO REGISTRAR EL ARTICULO';
													}
													echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
													echo "<input type='hidden' name='confc' value='red'></td>";
													
													break;
												}
											}
											
										}
										else{
											$error['ok'] = 'EL ARTICULO YA FUE DISPENSADO';
											echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
											echo "<input type='hidden' name='confc' value='red'></td>";
										}
									}
									else{
										$error['ok'] = 'EL ARTICULO NO ESTA CARGADO AL PACIENTE';
										echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
										echo "<input type='hidden' name='confc' value='red'></td>";
									}
									
								}
								else{
									$error['ok'] = 'EL PACIENTE NO TIENE KE ACTUALIZADO';
									echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
									echo "<input type='hidden' name='confc' value='red'></td>";
								}
							}
							else
							{
								$error['ok'] = 'EL KARDEX ELECTRONICO NO HA SIDO CONFIRMADO';
								echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
								echo "<input type='hidden' name='confc' value='red'></td>";
							}
						}
						else
						{
							$error['ok'] = 'EL PACIENTE NO TIENE KARDEX ELECTRONICO GRABADO';
							echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
							echo "<input type='hidden' name='confc' value='red'></td>";
						}
					}
					else{
						$error['ok'] = 'NO SE LE HA GENERADO KARDEX ELECTRONICO RECIENTEMENTE';
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
				}
				else
				{
					$exp = explode('-', $cco);
					$ccs['cod']=$exp[0];
					$ccs['nom']=$exp[1];
	
					$art['cod']=$insumos[0]['cod'];
					$art['nom']=$insumos[0]['nom'];
					$art['can']=1;
	
					$pac['his']=$historia;
					$pac['ing']=$ingreso;
					$pac['sac']=$servicio;
					$grab=devolucionCM($ccs, $art, $pac, $error, $dronum, $drolin, $carro);
	
					descargarCarro2( $art['cod'], $art['cod'], $historia, $ingreso, $cco );
					
					if($grab)
					{
						$tag = false;
						if( $packe['ke'] ){
							$val = registrarArticuloKE( $art['cod'], $historia, $ingreso, $dev = true, $asdf, '', '' );
							$tag = true;
						}
						
	//					echo "Lote: ".$insumos[0]['lot']; echo "No. Lote: ".$lotes[0]; exit;
						if( !$packe['ke'] || ($packe['ke'] && $val) ){
							echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
							echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
						}
						else if( $packe['ke'] && !$val && $tag && $packe['sal'] ){
							echo "<input type='hidden' name='confm' value='EL ARTICULO NO SE PUDO DESCONTAR DEL KARDEX ELECTRONICO'></td>";
							echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
						}
					}
					else
					{
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
				}
	
										?>
										<script>
										document . producto . insumo.options[document.producto.insumo.selectedIndex].value='';
										document . producto . submit();
	                                     </script >
	                                    <?php 
	                                    return;
			}
			else
			{
				if( esMMQ( $insumos[0]['cod'] ) && $accion == 'Cargo' ){
					if( true || !isset( $txCanMMQ ) || empty($txCanMMQ) ){
						if( $solCamilleroPedidoOn != 'on' ){
							echo "<tr><td colspan='8' align='center' class='texto1'><b>Cantidad a cargar: </b><INPUT type='text' name='txCanMMQ' id='txCanMMQ' class='texto5'></td></tr>";
							echo "<tr><td colspan='8' align='center' class='texto1'><INPUT type='button' class='texto5' value='Cargar' onClick='cargarMultiMMQ( \"cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "\" )'></td></tr>";
						}
						else{
							echo "<tr><td colspan='8' align='center' class='texto1'><b>Cantidad a cargar: </b><INPUT type='text' name='txCanMMQ' id='txCanMMQ' class='texto5'></td></tr>";
							echo "<tr><td colspan='8' align='center' class='texto1'><INPUT type='button' class='texto5' value='Cargar' onClick='cargarMultiMMQ( \"cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "&solicitudCamillero=on\" )'></td></tr>";
						}
					}
				}
				else{
					
					if( $accion == 'Cargo' )
					{ 
						if( ( !$packe['ke'] || $packe['nut'] || $packe['mmq'] || $packe['nka'] || ($packe['ke'] && $packe['act'] && $packe['con'] && $packe['gra']) && $packe['art'] )){

							$posicionLista = enLista($listart, $insumos[0]['cod'] );
							echo "<td class='texto2' colspan='6' align='left'>"; 
							if( $listart[ $posicionLista ]['ctc'] >= 100 )
								echo "<b style='font-size:14pt;'>ES ARTICULO NO POS Y LA CANTIDAD AUTORIZADA YA LLEGO A SU LIMITE</b>";
							else
								echo "<a id='txCargar' href='cargocpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "' target='_blank'><font size='4'>CARGAR</a>"; 
							echo "</td></tr>";
						}
					}
					else
					{
						echo "<td class='texto2' colspan='6' align='left'><a href='devolucioncpx.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . " ' target='_blank'><font size='4'>DEVOLVER</a></td></tr>";
					}
				}
			}
		}
		else if ($confm!='')
		{
			echo "<td bgcolor='".$confc."' colspan='6' align='center'><font color='#003366'><b>".$confm."</b></font></td></tr>";
		}
		
		//Mostrando la lista de articulos para un paciente con KE
		
		if( empty($confm) ){
			if( $packe['act'] || $packe['gra'] || $packe['con'] )
			{
				if( $packe['gra'] ){
	
					if( $packe['con'] ){
						if( !$packe['act'] ){
							$error['okke'] = 'EL PACIENTE NO TIENE KARDEX ELECTRONICO ACTUALIZADO';
							echo "<td bgcolor='red' colspan='6' align='center'><font color='#003366'><b>".$error['okke']."</b></font></td></tr>";
						}
					}
					else{
						$error['okke'] = 'EL PACIENTE NO TIENE KARDEX ELECTRONICO CONFIRMADO';
						echo "<td bgcolor='red' colspan='6' align='center'><font color='#003366'><b>".$error['okke']."</b></font></td></tr>";
					}
				}
				else{
					$error['okke'] = 'EL PACIENTE NO TIENE KARDEX ELECTRONICO GRABADO';
					echo "<td bgcolor='red' colspan='6' align='center'><font color='#003366'><b>".$error['okke']."</b></font></td></tr>";
				}
			}
			else{
				$error['okke'] = 'AL PACIENTE NO SE LE HA GENERADO KARDEX ELECTRONICO RECIENTEMENTE';
				echo "<td bgcolor='red' colspan='6' align='center'><font color='#003366'><b>".$error['okke']."</b></font></td></tr>";
			}
		}
		
		//Febrero 06 de 2015. Se desactiva la petición de camilleros a solicitud de Beatriz.
		if( false && count($mart) > $numMovAnt && ArticulosXPacienteSinSaldo( $historia, $ingreso ) && $solCamilleroPedidoOn != 'on' && !isset( $solCamillero ) ){
			
			$exp = explode('-', $cco);

			$nomCcoDestino = nombreCcoCentralCamilleros( $servicio );
			$motivo = 'DESPACHO DE MEDICAMENTOS';
			crearPeticionCamillero( nombreCcoCentralCamilleros( $exp[0] ), $motivo, "<b>Hab: ".$habitacion."</b><br>".$nombre, $nomCcoDestino, $wusuario, $nomCcoDestino, buscarCodigoNombreCamillero() );

//			echo "<INPUT type='hidden' value='on' name='solCamilleroPedidoOn'>";
			
			$solCamilleroPedidoOn = 'on';
		}
		
		//Febrero 06 de 2015. Se desactiva la petición de camilleros a solicitud de Beatriz.
		if( false && $solCamilleroPedidoOn == 'on' ){
			
			echo "<tr>";
			echo "<td class='titulo4' colspan='8'>SE HA SOLICITADO CAMILLERO</td>";
			echo "</tr>";
			
			echo "<INPUT type='hidden' value='on' name='solCamilleroPedidoOn'>";
		}

		//Febrero 06 de 2015. Se desactiva la petición de camilleros a solicitud de Beatriz.
		//url para solicitar camillero
		if( false && count($mart) > $numMovAnt && @$solCamilleroPedidoOn != 'on' ){
			$exp = explode('-', $cco);
			echo "<tr><td colspan='8' align='center'>";
			echo "<a href='./cargoscpxcm.php?user=".@$user."&solicitarCamillero=on&his=$historia&ing=$ingreso&ccoCencam={$exp[0]}&hab=$habitacion&nom=$nombre&usu=$wusuario&des=$servicio'><b>Solicitar camillero</b></a>";
			echo "</td></tr>";
			
//			echo "<INPUT type='hidden' value='on' name='solicitarCamillero'>";
			echo "<INPUT type='hidden' value='$historia' name='his'>";
			echo "<INPUT type='hidden' value='$ingreso' name='ing'>";
			echo "<INPUT type='hidden' value='{$cco['cod']}' name='ccoCencam'>";
			echo "<INPUT type='hidden' value='$habitacion' name='hab'>";
			echo "<INPUT type='hidden' value='$nombre' name='nom'>";
			echo "<INPUT type='hidden' value='$wusuario' name='usu'>";
			echo "<INPUT type='hidden' value='$servicio' name='des'>";
		}
		
		echo "<tr><td colspan=8 align='center' class='titulo3'><font size='3'>PACIENTE CON KARDEX ELECTRONICO</font></td></tr>";
		
		// $tiempoDispensacion = consultarTiempoDispensacion( $conex, '01' );
		
		// $rondaActual = ( ceil( date("H")/$tiempoDispensacion )*$tiempoDispensacion );
			
		// if( $rondaActual < 10 ){
			// $rondaActual = "0".$rondaActual.":00:00";
		// }
		// else{
			// if( $rondaActual == 24 ){
				// $rondaActual = "00:00:00";
			// }
			// else{
				// $rondaActual = $rondaActual.":00:00";
			// }
		// }
		
		/************************************************************************************************************/
		consultarInfoTipoArticulos( $conex, $bd );
		
		/****************************************************************************************************
		 * Agosto 28 de 2012
		 ****************************************************************************************************/
		$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $servicio );
		
		if( $disCco && $tempRonda < $disCco ){
			$tempRonda = $disCco;	//Marzo 11 de 2013. Originalmente estaba: $tmpDispensacion = $disCco;
		}
		/****************************************************************************************************/
		
		$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );			

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
		// $rondaActual = $tempRonda;
		/************************************************************************************************************/
		$horaCorteDispensacion = consultarAliasPorAplicacionCM( $conex, "01", "horaCorteDispensacion" );
		// echo "<tr><td colspan=8 align='center' class='titulo3'><font size='3'>RONDA PARA LAS $rondaActual</font></td></tr>";
		echo "<tr><td colspan=8 align='center' class='titulo3'><font size='3'>RONDA PARA LAS ".gmdate( "H:i:s", min( ($horaCorteDispensacion+24)*3600,$tempRonda*3600 ) )."</font></td></tr>";
		
		// $listart = ArticulosXPaciente( $historia, $ingreso );

		for( $i = 0; $i < count($listart); $i++ ){
			if( $i == 0 ){
				echo "<tr><td colspan='8' class='titulo3' align='center'><font size='3'>ARTICULOS REGISTRADOS EN EL KARDEX ELECTRONICO</font></td></tr>
				<tr class='texto1' align='center'>
					<td>Codigo</td>
					<td>Nombre</td>
					<td>Cantidad a dispensar</td>
					<td>Cantidad dispensada</td>					
					<td>Saldo</td>
				</tr>";
			}
			$class='texto3';
			if( $listart[$i]['sal'] > 0 ){
			echo "<tr>
				<td class='" . $class . "' align='center'>{$listart[$i]['cod']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['nom']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['cdi']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['dis']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['sal']}</td>
			</tr>";
			}
		}
	
		echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
		echo "<input type='hidden' name='realizar' value='0'></td>";
		echo "<input type='hidden' name='eliminar' value='0'></td>";
		echo "</table></br></form>";
	}
}

/**
    * Se pinta lo que se ha cargado en el dia, modificaicion 2007-07-18
    * 
    * @param vector $cargado informacion de lo que se ha cargado en el dia
    */
function pintarCargos($mart, $mlot, $mpre, $mcan, $mpaj, $mcaj, $mmov1, $mmov2, $text)
{
	$ant = '';
	$cont = 0;
	for ($i = 0;$i < count($mart);$i++)
	{
		if ($ant != $mmov1[$i])
		{
			$cont++;
			$ant = $mmov1[$i];
		}
	}

	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=8 class='titulo3' align='center'><b><font size='3'>NUMERO DE MOVIMIENTOS ".$text.": </font><font size='5'>" . $cont . "</font></b></td></tr>";
	echo "<tr><td class='texto1' align='center'>MOVIMIENTO CENTRAL</td>";
	echo "<td class='texto1' align='center'>MOVIMIENTO INTEGRADOR</td>";
	echo "<td class='texto1' align='center'>ARTICULO</td>";
	echo "<td class='texto1' align='center'>LOTE</td>";
	echo "<td class='texto1' align='center'>PRESENTACION</td>";
	echo "<td class='texto1' align='center'>CANTIDAD</td>";
	echo "<td class='texto1' align='center'>AJUSTE</td>";
	echo "<td class='texto1' align='center'>CANTIDAD</td></tr>";

	$ant = '';
	$class1 = 'texto3';
	$class = 'texto4';
	for ($i = 0;$i < count($mart);$i++)
	{
		if ($ant != $mmov1)
		{
			$class2 = $class;
			$class = $class1;
			$class1 = $class2;
			$ant = $mmov1;
		}

		echo "<tr><td class='" . $class . "' align='center'>" . $mmov1 [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mmov2 [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mart [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mlot [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mpre [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mcan [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mpaj [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mcaj [$i] . "</td></tr>";
	}
	echo "</table></br>";
}

/**
    * =========================================================PROGRAMA==========================================================================
    */
if( !isset($user) ){
	$user = "";
}

if(  isset($pda) && $pda == 'on' ){
	if( @strpos($user, "-") || @strpos($user, "-") === 0 ){
		$user = substr($user, strpos($user, "-") + 1, strlen($user));
	}
}

session_start();
if ( !isset($user) || $user == "" )
{
	if ( !isset($_SESSION['user']) && isset($pda) ){
		
		$wbasedato = 'cenpro';
		$conex = mysql_connect('localhost', 'root', '')
		or die("No se ralizo Conexion");
		

		
		if( isset($pda) && $pda == 'on' ){
			
			if(  @$user != '' && @validarUsuario( $user )  ){
				$user = "1-".$user;
//				session_register("user");
			}
			else{
				echo "<form name='producto' action='cargoscpxcm.php' method=post>";
				echo "<INPUT type='hidden' value='on' name='pda'>";
				pedirUsuario();
				echo "</form>";
			}
			
		}
	}
	else if (!isset($_SESSION['user'])){
//		session_register("user");
	}
}
else{
}

//if (!isset($_SESSION['user'])){
if ( @$user == "" ){
	
	if( isset($pda) && $pda == 'on' ){
	}
	else{
		echo "error";
	}
}
else
{
	if( !isset($cantidad) )
		$cantidad = 0;
		
	include_once( "conex.php" );
	include_once( "cenpro/cargos.inc.php" );
	
	$wbasedato = 'cenpro';
//	$conex = mysql_connect('localhost', 'root', '')
//	or die("No se ralizo Conexion");
	
	

	
	/******************************************************************
	 * Abril 23 de 2012
	 ******************************************************************/
	contingencia( $conex );
	/******************************************************************/
	
	$tmpDispensacion = consultarTiempoDispensacionIncCM( $conex, '01' );
	
	if (!isset($tipo))
	{
		$tipo = 'A';
	}
	pintarTitulo($tipo); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
	if (!isset($accion))
	{
		$accion = 'Cargo';
	}
	$bd = 'movhos';
	// invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
	// este programa no debe usarse
	// include_once("pda/tablas.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("cenpro/funciones.php");
	include_once("cenpro/cargoCM.php");
	include_once("cenpro/devolucionCM.php");
	connectOdbc($conex_o, 'facturacion');
	
	/**********************************************************************
	 * Agosto 14 de 2013
	 **********************************************************************/
	if( !consultarConexionUnix() ){
		$conex_o = 0;
	}
	/**********************************************************************/
	
	$mostrarMensajeSolicitudCamillero = 'off';
	
	//Febrero 06 de 2015. Se desactiva la petición de camilleros a solicitud de Beatriz.
	if( false && @$solicitarCamillero == 'on' ){
		$motivo = "DESPACHO DE MEDICAMENTOS";
		$nomCcoDestino = nombreCcoCentralCamilleros( $des );
		crearPeticionCamillero( nombreCcoCentralCamilleros( $ccoCencam ), $motivo, "<b>Hab: ".$hab."</b><br>".$nom, $nomCcoDestino, $usu, $nomCcoDestino, buscarCodigoNombreCamillero() );
		
		if( @$pda == 'on' ){
			echo "<table align='center'>";
			echo "<tr>";
			echo "<td class='titulo4'>SE HA SOLICITADO CAMILLERO</td>";
			echo "</tr>";
			echo "</table>";
		}
		
		$mostrarMensajeSolicitudCamillero = 'on';
	}
	else{
		echo "<input type='hidden' value='' name='".@$solicitarCamillero."'>";
	}
	
	if ( true || $conex_o != 0)		//Enero 2 de 2013. Dejo pasar tenga o no conexion con Unix
	{
		if( isset($pda) && $pda == 'on' ){
			if( strpos($user, "-") === false ){
				$user = "1-".$user;
			}
		}
		
		// consulto los datos del usuario de la sesion
		$pos = strpos($user, "-");
		$wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario
		// consulto los centros de costos que se administran con esta aplicacion
		// estos se cargan en un select llamado ccos.
		if (isset($cco))
		{
			$ccos = consultarCentros($cco);
		}
		else
		{
			$ccos = consultarCentros('');
		}
		// si el tipo es un cargo a paciente se pregunta la historia del paciente
		// y se valida que esta historia exita.
		if ((isset($historia) and $historia != ''))
		{
			$historia=trim($historia);
			$val = validarHistoria(@$cco, $historia, $ingreso, $mensaje, $nombre, $habitacion, $servicio);
			if (!$val)
			{
				pintarAlert1($mensaje);
				$historia = '';
				$ingreso = '';
				$nombre = '';
				$habitacion = '';
			}
			else if ($mensaje!='')
			{
				pintarAlert1($mensaje);
			}
			
			if( $val ){
				/********************************************************************************
				 * Marzo 13 de 2012
				 ********************************************************************************/
				$informacionPaciente[ 'his' ] = $historia;
				$informacionPaciente[ 'ing' ] = $ingreso;
				 
				$auxwbasedato = $wbasedato;
				crearKardexAutomaticamente( $conex, $bd, $informacionPaciente, date( "Y-m-d" ) );
				$wbasedato = $auxwbasedato;
				/********************************************************************************/
			}
			// 2007-07-18
			/**
                * if ((!isset($consulta) or $consulta == '') and (!isset($grabar) or $grabar != '1'))
                * {
                * $cargado = consultarCargos($historia, $ingreso, $crear, date('Y-m-d'), $cco);
                * }
                */
		}
		else // si aun no se ha ingresado historia se incializa en cero
		{
			$historia = '';
			$ingreso = '';
			$nombre = '';
			$habitacion = '';
		}

		if (!isset($estado))
		{
			// para saber que mensaje desplegar al pintar el formulario
			$estado = 'inicio';
		}
		// se estamos buscando un movimiento determinado, se realiza la busqueda con los parametros ingresados por usuario
		if (isset ($parcon) and $parcon != '')
		{
			if (!isset ($insfor))
			{
				$insfor = '';
			}
			if (!isset ($parcon2))
			{
				$parcon2 = '';
			}
			if (!isset ($parcon3))
			{
				$parcon3 = '';
			}
			$consultas = BuscarTraslado($parcon, $parcon2, $parcon3, $insfor, $forcon, $accion);
			$consulta = $consultas[0];
		}
		// si esta setiado consulta se busca un cargo determinado
		if (isset($consulta) and $consulta != '')
		{
			$exp = explode('-', $consulta);
			$numtra = $exp[0] . '-' . $exp[1];
			$exp2 = explode('(', $consulta);
			$fecha = substr($exp2[1], 0, (strlen($exp2[1])-1));
			$res = consultarTraslado($exp[0], $exp[1], $fecha, $tipo, $ccos, $destinos, $historia, $estado, $cco, $ingreso, $mart, $mlot, $mpre, $mcan, $mpaj, $mcaj, $mmov1, $mmov2, 0);
			if (!$res)
			{
				pintarAlert1('No se encuentra un movimiento realizado con las carcteristicas ingresadas');
				$mart = '';
			}
			else
			{
				$ingres = $ingreso;
				validarHistoria($cco, $historia, $ingres, $mensaje, $nombre, $habitacion, $servicio);
				if (!isset($servicio))
				{
					$servicio = '';
				}
			}
		}
		else if ((!isset ($numtra) or $numtra == '')and isset($historia) and $historia != '')
		{
			if ($accion == 'Cargo')
			{
				$numtra = consultarConsecutivo(-1);
			}
			else
			{
				$numtra = consultarConsecutivo(1);
			}
			
			$parad = BuscarTraslado3($accion, $historia, $ingreso, $wusuario, $fechad, $movd);

			if ($parad)
			{
				$z = 0;
				for($i = 0; $i < count($movd); $i++)
				{
					if ($movd[$i] != '')
					{
						$exp = explode('-', $movd[$i]);
						$res = consultarTraslado($exp[0], $exp[1], $fechad, $tipod, $ccosd, $destinosd, $historiad, $estadod, $ccod, $ingresod, $martd, $mlotd, $mpred, $mcand, $mpajd, $mcajd, $mmov1d, $mmov2d, $z);
						$z = count($martd);
					}
				}
			}
		}
		else if (isset ($numtra) and isset($historia) and $historia != '')
		{
			// consulto todos los movimientos desde el consecutivo inicial hasta el momento para la historia y el responsable
			$para = BuscarTraslado2($numtra, $accion, $historia, $ingreso, $wusuario, $fecha, $mov);
			
			if ($para)
			{
				$y = 0;
				for($i = 0; $i < count($mov); $i++)
				{
					if ($mov[$i] != '')
					{
						$exp = explode('-', $mov[$i]);
						$res = consultarTraslado($exp[0], $exp[1], $fecha, $tipo, $ccos, $destinos, $historia, $estado, $cco, $ingreso, $mart, $mlot, $mpre, $mcan, $mpaj, $mcaj, $mmov1, $mmov2, $y);
						$y = count($mart);
					}
				}
			}

			$parad = BuscarTraslado3($accion, $historia, $ingreso, $wusuario, $fechad, $movd);

			if ($parad)
			{
				$z = 0;
				for($i = 0; $i < count($movd); $i++)
				{
					if ($movd[$i] != '')
					{
						$exp = explode('-', $movd[$i]);
						$res = consultarTraslado($exp[0], $exp[1], $fechad, $tipod, $ccosd, $destinosd, $historiad, $estadod, $ccod, $ingresod, $martd, $mlotd, $mpred, $mcand, $mpajd, $mcajd, $mmov1d, $mmov2d, $z);
						$z = count($martd);
					}
				}
			}
		}
		// si la busqueda de movimentos no se utilizo se pnene las variables en vacio
		if (!isset($forcon))
		{
			$forcon = '';
		}
		if (!isset($consultas) || empty($consultas) )
		{
			$consultas = array();
			$consultas = '';
		}
		// se pinta html de formulario para buscar un movimiento determinado
		pintarBusqueda($consultas, $forcon, $tipo, $accion);

		if (isset($parbus2) and $parbus2 != '')
		{
			// se desetean las variable por si se ha cambiado el producto
			// no queden cargados los insumos que lo conforman
			if (isset($insumos))
			{
				unset($insumos);
			}

			if (isset($inslis))
			{
				unset($inslis);
			}
		}
		
		if (isset($insumo) and $insumo == '')
		{
			if (isset($insumos))
			{
				unset($insumos);
			}
		}
		// si ya tenemos un insumo seleccionado, llenamos las variables de busqueda para volver a buscar el dato
		if (isset($insumo) and $insumo != '' and (!isset($accion) or $accion != 1) and (!isset($parbus2) or $parbus2 == '') and (!isset($consulta) or $consulta == ''))
		{
			$parbus2 = explode('-', $insumo);
			$parbus2 = $parbus2[0];
			$forbus2 = 'Codigo';
		}

		if (isset($parbus2) and $parbus2 != '')
		{
			// consultamos el insumo, el nombre y si es un insumo producto codificado o no codificado
			$insumos = consultarInsumos($parbus2, $forbus2);
			// si es un prducto
			if ($insumos[0]['lot'] == 'on')
			{
				$nueva = $historia . '-' . $ingreso;
				$lotes = consultarLotes($insumos[0]['cod'], $ccos[0], '', $accion, $nueva);

				if (!$lotes)
				{
					if ($accion == 'Cargo')
					{
						pintarAlert1('NO EXISTE LOTE CON SALDO PARA DEL PRODUCTO');
					}
					else
					{
						pintarAlert1('PACIENTE SIN CARGOS DEL PRODUCTO');
					}
				}

				$unidades = [];
			}
			else // si es un insumo consultamos las diferentes presentaciones para este
			{
				if ($forbus2 == 'rotulo') // depende de si ya se ingreso una presentacion o no, si se busca por rotulo lo que se busca es la presentacion
				{
					$unidades = consultarUnidades($insumos[0]['cod'], $ccos[0], $parbus2);

				}
				else
				{
					// $unidades=consultarUnidades($insumos[0]['cod'], $ccos[0], '');
					if (isset($prese) and $prese != '')
					{
						$prese = explode('-', $prese);
						$prese = $prese[0];
						$unidades = consultarUnidades($insumos[0]['cod'], $ccos[0], $prese);
					}
					else
					{
						$unidades = consultarUnidades($insumos[0]['cod'], $ccos[0], '');
						//echo $ccos[0];
					}
				}
			}
		}
		// se incializan en vacio todas las variables en caso de que no este setiadas
		if (!isset ($unidades))
		{
			$unidades = [];
			$unidades[0] = '';
		}
		if (!isset ($escogidos))
		{
			$escogidos = '';
		}

		if (!isset ($insumos) || empty($insumos) )
		{
			$insumos = array();
			// $insumos = '';
//			$insumos[0]['cdo'] = '';
//			$insumos[0]['lot'] = '';
//			$insumos[0]['cod'] = ''; 
		}
		if (!isset($lotes))
		{
			$lotes = [];
			$lotes[0] ='';
		}
		// incializamos variables sin inicializar
		if (!isset ($fecha))
		{
			$fecha = date('Y-m-d');
		}
		
		if (!isset($carro))
		{
			$carro='off';
		}
		else 
		{
			$carro='on';
		}
		// pintamos el formulario donde se ingresa la historia clinica
		if ((isset ($historia) and $historia != ''))
		{
			$ser['cod']=$servicio;
			getCco($ser, 'C', '01');
			pintarFormulario($estado, $ccos, $historia, '', $fecha, $ingreso, $nombre, $habitacion, $tipo, $accion, 0, $numtra, $ser['apl'], $carro);
		}
		else
		{	
			pintarFormulario($estado, $ccos, $historia, '', $fecha, $ingreso, $nombre, $habitacion, $tipo, $accion, 1, '', false, $carro);
		}
		// pintamos el formulario donde se ingresan los insumos de un producto y la lista a cargar
		
		if (!isset($confm))
		{
			$confm='';
			$confc='';
		}
		
		if( !isset( $pda ) || $pda != 'on' ){
			
			if( (isset ($historia) and $historia != '') )
			{
				pintarInsumos( $insumos, $unidades, $lotes, $tipo, $escogidos, $accion, $ccos[0], $historia, $ingreso, $servicio, $confm, $confc, $carro );
			}
	
			if( isset($mart) and $mart != '' )
			{
				pintarCargos($mart, $mlot, $mpre, $mcan, $mpaj, $mcaj, $mmov1, $mmov2, '');
			}
	
			if (isset($martd) and $martd != '')
			{
				pintarCargos($martd, $mlotd, $mpred, $mcand, $mpajd, $mcajd, $mmov1d, $mmov2d, 'DEL DIA');
			}
		}
		else{
			if( !empty($historia) ){
				pintarCargosPDA( $insumos, $unidades, $lotes, $tipo, $escogidos, $accion, $ccos[0], $historia, $ingreso, @$servicio, $confm, $confc, $carro );
			}
//			pintarCargos($mart, $mlot, $mpre, $mcan, $mpaj, $mcaj, $mmov1, $mmov2, '');
		}
	}
	else
	{
		pintarAlert2('EN ESTE MOMENTO NO ES POSIBLE CONECTARSE CON UNIX PARA REALIZAR EL CARGO, POR FAVOR INGRESE MAS TARDE');
	}
}

        ?>
</body>
</html>
