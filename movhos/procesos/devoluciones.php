<html>
<head>
<title>Devoluciones</title>
<style type="text/css">
 		<style>
        //body{background:white url(portal.gif) transparent center no-repeat scroll;}
        <!--Fondo Azul no muy oscuro y letra blanca -->
        .tituloSup{color:#006699;background:#FFFFF;font-size:13pt;font-family:Arial;font-weight:bold;text-align:center;}
        .tituloPeq{color:#006699;background:#FFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
        .tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}


         .titulo1-anterior{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
         .titulo1{font-size: 9pt;font-family:Arial;font-weight:bold;color:white;background:#638cb5;border:0px;}
         .titulo1b{font-size: 13pt;font-family:Arial;font-weight:bold;color:white;background:#638cb5;border:0px;}
        <!-- -->
        <!--.titulo2-viejo{color:#003366;background:#57C8D5;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}-->

        .titulo3{color:#0A3D6F;background:#61D2DF;font-size:8pt;font-family:Arial;text-align:center;}
        .texto{background-color: #E8EEF7;color: #000000;font-size:8pt;font-family:Tahoma;text-align:center;}
        .acumulado1{color:#003366;background:#FFCC66;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
        .acumulado2-viejo{color:#003366;background:#FFDBA8;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
        .acumulado2{background-color: #C3D9FF;color: #000000;font-size: 10pt;}
        .errorTitulo{color:#FF0000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
        <!--.titulo2-viejo{color:#003366;background:#4DBECB;font-size:8pt;font-family:Arial;text-align:center;}-->
        .titulo2{background-color: #2A5DB0;color: #FFFFFF;font-size: 10pt;font-weight: bold; };
        .errorTit{background:#FFAAAA;color:#FF0000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
        .errorTit1{background:#FFAAAA;color:#FF0000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
        .alert{background:#FFFFAA;color:#FF9900;font-size:8pt;font-family:Arial;text-align:center;}
        .warning{background:#FFCC99;color:#FF6600;font-size:8pt;font-family:Arial;text-align:center;}
        .error{background:#FFAAAA;color:#FF0000;font-size:8pt;font-family:Arial;text-align:center;}

        .tituloA1{color:#FFFFFF;background:#660099;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
        .texto1{color:#660066;background:#FFFFFF;font-size:8pt;font-family:Arial;}
        .exito{color:#006699;background:#AADDDD;font-size:8pt;font-family:Tahoma;text-align:center;font-weight:bold}

    </style>

    <script type="text/javascript">

	function ejecutarSubmit( campo ){
		campo.disabled = true;
	}

    function validar(nombre)
    {
    	textoCampo = window.document.devApl2.elements[nombre].value;
    	textoCampo = validarEntero(textoCampo);
    	window.document.devApl2.elements[nombre].value = textoCampo;
    }

    function validarEntero(valor)
    {
    	var res=true;
    	for (var i=0; i<valor.length; i++)
    	{
    		var letra=valor.substr(i,1);
    		letra= parseInt(letra);
    		if (isNaN(letra))
    		{
    			res=false;
    		}
    	}

    	if (!res)
    	{
    		alert('Debe ingresar un numero entero positivo');
    		return 0;
    	}
    	{
    		return valor;
    	}
    }

	window.onload = function(){
		window.document.devApl2.onsubmit = function(){ window.document.devApl2.aceptar.style.display = 'none' };
	}

    </script>
</head>
<body>
<?php
include_once("conex.php");
/****************************************************************************************************************************************
 *
 * Modificaciones.
 * Julio 4 de 2019. 	(Edwin MG)  Se hace redondeo de la cantidad del saldo
 * Febrero 12 de 2019 	(Edwin MG)  Cuando no hay conexión a Unix y se carga una minibolsa no se mueve el número de línea del medicamento a devolver
 * Diciembre 12 de 2018 (Jessica) 	Se comenta la validación de pacientes particulares que evita que los productos codificados o minibolsas se desglosen (se facturan sus insumos)
 * Noviembre 27 de 2017	(Edwin MG)	Cuando se devuelve una minibolsa se desglosa sus insumos en la factura. 
 *									Esto se hace por que a todos los pacientes que no son particulares al dispensar una miniblosa se desglosa sus insumos en la factura 
 *									y al devolver el insumo debe hacer los mismo.
 * Julio 21 de 2017   	(Edwin MG)	Se hacen cambios para que se tenga el cuenta el cco de donde se dispensa el articulo y no el cco con el que se entra. Esto se hace
 *									para cuando un articulo se dispensa de urgencia y el paciente pase a piso se haga el descarte correctamente.
 * Abril 12 de 2017   	(Edwin MG) Para urgencias, se permite descartar articulos así el paciente no este activo. Nota: Para urgencias solo se permiten descartes
 * Abril 10 de 2017   	(Edwin MG) Se consulta la fraccion del articulo tal como se hace en gestion de enfermería para el cco de urgencias.
 * Febrero 21 de 2017   (Jonatan Lopez) Se agrega informacion del paciente a la solicitud automatica de camillero.
 * Marzo 25 de 2015		(Edwin MG)	Se valida que el paciente tenga ordenes con respecto a la última fecha del kardex
 * Marzo 20 de 2015		(Edwin MG)	Se ponen cantidades por defecto a descartar
 * Marzo 19 de 2015 	(Edwin MG)	El campo de cantidad de descarte no se puede modificar
 * Marzo 17 de 2015 	(Edwin MG)	Para urgencias no se permite hacer devoluciones, solo descarte
 * noviembre 19 de 2013 (camilo ZZ)	se organiza el query que calcula los saldos para que incluya los cargos que estén en la 143, ademas de tener en cuenta la resta de lo dispensado menos lo recibido
 * Abril 15 de 2013		(Edwin MG)	Se tiene en cuenta al devolver un medicamento el articulo que se cargó automáticamente
 * Abril 8 de 2013		(Edwin MG)	Se valida que se puedan descartar fracciones pero no unidades enteras
 * Julio 19 de 2012		(Edwin MG)	Oculto el boton aceptar cuando se le da click al realizar una devolucion
 * Agosto 16 de 2011	(Edwin MG)	Al realizar una devolución, si el articulo pertenece al kardex electronico, este descuenta del kardex
 *****************************************************************************************************************************************/

/**
 * Enero 8 de 2013
 * Enter description here...
 *
 * @modified Diciembre 11 de 2014	Edwin MG.	Se realizan cambios para poder descartar medicamentos desde urgencias si el paciente tiene ordenes y el cco de urgencia maneja ordenes.
 * @modified Febrero 25 de 2013 (Edwin MG). Cambios varios para cuando no hay conexión con UNIX. Entre ellos se registra el movimiento en tabla de paso
 *											y se mira los saldos en matrix y no en UNIX.
 * @modified 2007-09-24 Se crea la función registrarDevFact() que hace el registro en la tabla 000028, por lo que ya no se necesita ese query.
 * @modified 2007-09-21 Con motivo de la creación del campo 000028.Devart se modifica el query que registra la devolución
 * @modified 2007-09-20 Con motivo de la creación del campo 000028.Devapv se crea la variable $apv y se modifica el query que registra la devolución
 * @modified 2007-09-18 Desaparece el arreglo $usu y lo reemplaza la variable string $usuario
 * @modified 2007-09-18 No se llama a la función registrarSaldos() si no a registrarSaldosNoApl
 *
 * @param Integer       $devCons
 * @param Array         $pac            Información del paciente</br>
 *                                                              [his]:Historia del paciente.</br>
 *                                                              [ing]:
 * @param Array         $art                    [cod]:Código del artículo.</br>
 *                                                              [can]:cantidad del artículo.</br>
 *                                                              [neg]:Si el artículo permite o no generar negativos en ele inventario.
 * @param String        $jusD
 * @param Doubble       $faltante
 * @param String        $jusF
 * @param array         $cco                    Información del centro de costos</br>
 *                                                              Debe ingresar:</br>
 *                                                              [cod]:String[4].Código del centro de costos.</br>
 *                                                              [neg]:Boolean.El Centro de costos permite generar negativos en el inventario para todos los artículso.</br>
 *                                                              [apl]:Boolean. Los artículos que se cargan de este centrod e costos se graban como aplicados inmediatamente.
 * @param String[1]     $tipTrans       Tipo de transacción
 *                                                              C: cargo a cuenta de pacientes
 *                                                              D: Devolución.
 * @param Boolean       $aprov          Si es un aprovechamiento o no.
 * @paramArray          $usu            Información del usuario.
 *                                                              [usuM]:Código de MAtrix del usuario.
 * @param unknown_type $error           Almacena todo lo referente con códigos y mensajes de error
 *                                                              [ok]:Descripción corta.</br>
 *                                                              [codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010</br>
 *                                                              [codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.</br>
 *                                                              [descSis]:Descripción del error del sistema.</br>
 * @return unknown
 */
 

/**
 * Dice si un articulo es Material Medico Quirurgico
 *
 * @param $art
 * @return unknown_type
 *
 * Nota: SE considera material medico quirurgico si el grupo del
 * articulo no se encuentra en la taba 66 o pertenezca al grupo E00 o V00
 *
 * Modificacion:
 * Septiembre 8 de 2011.	Ya no se considera MMQ los articulos del grupo V00
 */
function esMMQ( $art ){

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
 
 

/********************************************************************************************************************************
 * Consulto el articulo equivalente a cargar si tiene
 ********************************************************************************************************************************/
function consultarArticuloEquivalente( $cco, $art ){

	global $conex;
	global $bd;

	$val = false;

	$sql = "SELECT
				Areaeq, Areceq, c.Artuni as Artuni
			FROM
				{$bd}_000008 a, {$bd}_000026 b, {$bd}_000026 c
			WHERE
				areces = b.artcod
				AND b.artest = 'on'
				AND arecco = '$cco'
				AND areces = '$art'
				AND areaeq != ''
				AND c.artcod = areaeq
				AND c.artest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){

		if( !empty( $rows['Areaeq'] ) ){
			$val = $rows;

			//Debo consultar la cantidad equivalente del articulo equivalente
			// $val[ 'Arefra' ] = consultarFraccion( Array( "cod" => $rows['Areaeq'] ), Array( "cod" => $cco ) );
			$val[ 'Arefra' ] = cantidadFraccion( $conex, $bd, $art['cod'], $cco['cod'] );
		}
	}

	return $val;
}


 
function validarFraccionOrdenes( $conex, $wbasedato, $art, $cco ){
	
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
	
	if( $num > 0 ){
		$dividir = $row['Relcon'];
	}
	else{
		
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
		else{
			
			$q = "  SELECT Arecde
					  FROM ".$wbasedato."_000008
					 WHERE Areces = '".$art."'
					   AND Arecco = '".$cco."'" ;
					   
			$res = mysql_query($q, $conex);
			$num = mysql_num_rows($res);
			$row = mysql_fetch_array($res);
			
			if($num > 0){
				$dividir = $row['Arecde'];
			}
		}	
	}
	
	return $dividir;
}
 
/******************************************************************************************
 * Indica si un paciente le fue creado el kardex desde el programa de ordenes
 ******************************************************************************************/
function pacienteKardexOrdenesDev( $conex, $wbasedato, $his, $ing ){

	$val = false;
	
	$sql = "SELECT Fecha_data, Karord
			  FROM ".$wbasedato."_000053
			 WHERE karhis = '$his'
			   AND karing = '$ing'
			   AND karord = 'on'
		  ORDER BY Fecha_data DESC
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		if( $rows[ 'Karord' ] == 'on' ){
			$val = true;
		}
	}
	
	return $val;
}

 
function cantidadFraccion( $conex, $bd, $art, $cco ){ 

	$val = 1;

	//NO SE PUDE DEVOLVER SINO MULTIPLOS DEL MINIMO FACTURABLE, SI ES ASI PONER EN DESCARTE
	$q = " SELECT Arecde "
	. "      FROM " . $bd . "_000008 "
	. "     WHERE Areces = '" . $art . "' "
	. "     And   Arecco= '" .  $cco . "' ";
	$err1 = mysql_query($q, $conex);

	$num1 = mysql_num_rows($err1);
	if ($num1 > 0)
	{
		$row1 = mysql_fetch_array($err1);
		$val = $row1[0];
	}
	
	return $val;
}

/********************************************************************************************************************************
 * Consulta si un medicamento a cargar tiene mas articulos asociados a dispensar automáticamente
 ********************************************************************************************************************************/
function consultarArticulosACargarAutomaticamente( $pro ){

	global $conex;
	global $bd;
	global $conex_o;

	$sql = "SELECT *
			FROM
				{$bd}_000153, {$bd}_000026
			WHERE
				Acppro = '$pro'
				AND Acpest = 'on'
				AND artcod = acpart
				AND artest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return $res;
}

/***************************************************************************************************************************************
 * Consulta si el medicamento puede ser cargado automáticamente por un producto
 * De ser así devuelve la cantidad total que no puede devolverse segund el saldo
 * o saldos de los productos del que puede ser cargado automáticamente
 ***************************************************************************************************************************************/
function consultarCantidadANoDevolver( $conex, $wbasedato, $art, $his, $ing, $cco ){

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000004, {$wbasedato}_000153
			WHERE
				spahis = '$his'
				AND spaing = '$ing'
				AND spacco = '$cco'
				AND spaart = acppro
				AND acpart = '$art'
				AND acpest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	$suma = false;

	if( $num > 0 ){

		while( $row = mysql_fetch_array( $res ) ){

			$fraccion = 1;

			//Consutlo la fraccion del articulo
			$q = " SELECT Arecde "
			. "      FROM " . $wbasedato . "_000008 "
			. "     WHERE Areces = '" . $row[ 'Spaart' ] . "' "
			. "       And Arecco= '" .  $row[ 'Spacco' ] . "' ";

			$err1 = mysql_query($q, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num1 = mysql_num_rows($err1);

			if( $num1 > 0 ){
				if( $row1 = mysql_fetch_array($err1) )
				{
					$fraccion = $row1[ 'Arecde' ];
				}
			}

			$suma += ( $row[ 'Spauen' ] - $row[ 'Spausa' ] )/$fraccion*$row[ 'Acpcan' ];
		}
	}

	return $suma;
}

function procesoDevMatrix($devCons, $pac, $art, $jusD, $faltante, $jusF, $cco, $tipTrans, $aprov,$usuario,&$error)
{
	global $bd;
	global $conex;
	global $ccoUsu;
	global $solicitudCamillero;
	
	global $emp;

	if( validacionDevolucion($cco,$pac,$art,$aprov,false,$error))
	{
		if(TarifaSaldoMatrix($art,$cco,"D",$aprov,$error))
		{
			$dronum="";
			$drolin="";
			$date=date("Y-m-d");
			$cns="";
			if($aprov)
			{
				$fuente=$cco['fap'];
			}
			else
			{
				$fuente=$cco['fue'];
			}

			if(Numeracion($pac, $fuente, 'D', $aprov, $cco, $date, $cns, $dronum, $drolin, $pac['dxv'], $usuario, $error ))
			{	
				CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );
				
				$wcenpro = consultarAliasPorAplicacion( $conex, $emp, "cenmez" );
				
				$reInsProducto = consultarInsumosProducto( $wcenpro, $bd, $art[ 'cod' ] );
				
				if( mysql_num_rows( $reInsProducto ) > 0 ){

					//Solo muevo la numeración tantas veces sea necesario
					//Esto por que se debe mantener el valor de los drolin correctamente
					//al momento de procesar la contingencia del kardex
					$drolinAux = 0;
					for( $i = 0; $rowsIns =  mysql_fetch_array( $reInsProducto ); $i++ ){
						if( $i > 0 ){
							Numeracion($pac, $fuente, 'D', $aprov, $cco, $date, $cns, $dronum, $drolinAux, $pac['dxv'], $usuario, $error );
						}
					}
				}
				if( true )
				{
					$art['ubi']='US';
					$art['ini']=$art['cod'];
					//Mover elsaldo en la tabla de saldos normales.
					$art['lot']=" ";
					if(registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario,$error, "000143" ))
					{

						/***************************************************************************
						 * Enero 2 de 2013
						 *
						 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
						 ***************************************************************************/
						realizarMovimientoSaldos( $conex, $bd, $tipTrans, $cco[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
						/***************************************************************************/

						if(registrarSaldosNoApl($pac,$art,$cco,$aprov,$usuario,"D",false,$error))
						{
							/******************************************************************************************************
							 * Abril 15 de 2013
							 ******************************************************************************************************/
							$resAut = consultarArticulosACargarAutomaticamente( $art['cod'] );

							if( $resAut ){

								$numResAut = mysql_num_rows( $resAut );

								if( $numResAut > 0 ){

									while( $rowsResAut = mysql_fetch_array( $resAut ) ){

										$art2['cod'] = $rowsResAut[ 'Artcod' ];
										$art2['can'] = $rowsResAut[ 'Acpcan' ]*$art['can'];
										$art2['ini'] = $rowsResAut[ 'Artcod' ];
										$art2['ser'] = $art['ser'];
										$art2['ubi'] = $art['ubi'];
										$art2['nom'] = $rowsResAut[ 'Artcom' ];
										$art2['uni'] = $rowsResAut[ 'Artuni' ];

										$artValido = Numeracion($pac, $fuente, 'D', $aprov, $cco, $date, $cns, $dronum, $drolin2, $pac['dxv'], $usuario, $error );
										$artValido = registrarItdro($dronum, $drolin2, $fuente, $date, $cco, $pac, $art2, $error);
										$artValido = registrarDetalleCargo($date, $dronum, $drolin2, $art2, $usuario,$error, "000143");
										// $artValido = registrarSaldosNoApl($pac,$art2,$cco,$aprov,$usuario,"D",false,&$error);

										$ardrolin2[ $art2['cod'] ] = $drolin2;

										$fecApl2=$date;
										// $ronApl=date("G:i - A");
										$ronApl2=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );
										registrarAplicacion($pac, $art2, $cco,$aprov,$fecApl2,$ronApl2, $usuario, $tipTrans, $dronum,$ardrolin2[ $art2['cod'] ], $error);
									}

									mysql_data_seek( $resAut, 0 );	//reseteo nuevamente la consulta por si toca hacer la aplicación automática
								}
							}
							/******************************************************************************************************/

							//Registrar movimiento de la devolución de facturación
							if(registrarDevFact($devCons,$dronum,$drolin,$art['can'], $jusD,$faltante,$jusF,$usuario,$error))
							{
								if( !$solicitudCamillero ){
									if( esTraslado( $cco['cod'] ) && true ){ //echo "paso1111.... ".$ccoUsu['cod'];
										peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoUsu['cod'], $cco['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
									}
									$solicitudCamillero = true;
								}
								return (true);
							}
							else
							{
								return(false);
							}
						}
					}// if de registrarDetalleCargo()
					else
					{
						return(false);
					}
				}//if de registrarItdro()
				else
				{
					return(false);
				}
			}//if de Numeración()
			else
			{
				return(false);
			}
		}
		else
		{
			//Sin tarifa, saldo
			return(false);
		}
	}
	else
	{
		//Sin  cantidad a devolver
		$error['ok']="ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN, O UN DESCARTE DE ESTE ARTÍCULO A ESTE PACIENTE MIENTRAS UD. HACIA ESTA DEVOLUCION.";
		return(false);
	}
}

/****************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 *
 * @return unknown_type
 ****************************************************************************************/
function cantidadDispensadaRonda( $horasAplicar, $ronda ){

	$val = 0;

	if( empty( $horasAplicar ) ){
		return $val;
	}

	$exp = explode( ",", $horasAplicar );

	for( $i = 0; $i < count( $exp ); $i++ ){

		$valores = explode( "-", $exp[$i] );

		if( $ronda == $valores[0] ){

			$val = $valores[2];
			break;
		}
	}

	return $val;
}

/************************************************************************************************
 * crea un vector con las cantidades cargadas durante el dia seguna la hora de aplicaciones
 * @return unknown_type
 *
 * Julio 14 de 2011
 ************************************************************************************************/
function crearAplicacionesDevueltasPorHoras( $vector, $cantidad ){

	if( $cantidad == 0 ){
		return $vector;
	}

	if( empty( $vector ) ){
		return "";
	}
	elseif( !empty( $vector ) ){
		//Obtengo las horas de aplicacion del medicamento para el paciente
		$exp = explode( ",", $vector );
	}

	$canDispensada = cantidadDispensadaRonda( $vector, "00:00:00" );

	$cantidad = $cantidad -( ceil($canDispensada) - $canDispensada );

	//Busco la ultima hora dispensada
	$ultimoDispensado = -1;
	if( !empty($vector) ){

		for( $i = 0; $i < count( $exp ); $i++ ){
			$valores = explode( "-", $exp[$i] );

			if( $valores[2] > 0 ){
				$ultimoDispensado =  $i;
			}
		}
	}

	$nuevoAplicaciones = "";

	if( !empty($vector) ){

		$acumuloFraccionCargada = 0;
//		echo "<br>.........$cantidad..";
		for( $i =$ultimoDispensado; $i >= 0; $i-- ){

			if( $cantidad > 0 ){

				$valores = explode( "-", $exp[$i] );

				if( $valores[2] > 0 ){

					if( $valores[2] >= $cantidad ){
						$restar = $cantidad;
					}
					else{
						$restar = $valores[2];
					}

					$exp[$i] = $valores[0]."-".$valores[1]."-".( round ( $valores[2]-$restar, 1 ) );
//					echo "<br>.....r....$restar..".( round ( $valores[2]-$restar, 1 ) )."........";
					$cantidad -= $restar;
//					echo "<br>......=...$cantidad..";
					$i++;
				}
			}
			else{
				break;
			}
		}

		for( $i = 0; $i < count($exp); $i++ ){

			if( $i == 0 ){
				$nuevoAplicaciones = $exp[$i];
			}
			else{
				$nuevoAplicaciones .= ",".$exp[$i];
			}
		}
	}

	return $nuevoAplicaciones;
}

function devolucionesKE( $art, $pac, $cco, &$idRegistro, $tras = true ){

	global $conex;
	global $bd;
	global $centraldemezclas;
	global $fecDispensacion;
	global $usuario;

	$ori = 'SF';

	if( $cco['cod'] == $centraldemezclas ){
		$ori = 'CM';
	}

	/******************************************************************************************
	 * Solo para pacientes del 7 piso y que este entre las 4 y 7 de la mañana
	 ******************************************************************************************/
	if( time() >= strtotime( "2011-08-24 04:00:00" ) && time() <= strtotime( "2011-08-24 07:00:00" ) ){

		$sql = "SELECT
					*
				FROM
					{$bd}_000011 a, {$bd}_000020 b
				WHERE
					habhis = '{$pac['his']}' AND
					habing = '{$pac['ing']}' AND
					ccocod = habcco AND
					ccocod = '1186'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );

		if( $numrows ){
			$permitir = true;
		}
		else{
			$permitir = false;
		}
	}
	else{
		$permitir = false;
	}

	if( !$permitir ){
		return;
	}
	/******************************************************************************************/

	if( true ){

		$dev2 = false;

		// $sql = "SELECT
					// id, '100' as kaddis, kadcpx
				// FROM
					// {$bd}_000054
				// WHERE
					// kadart = '{$art['cod']}' AND
					// kadhis = '{$pac['his']}' AND
					// kading = '{$pac['ing']}' AND
					// kadfec = '".date( "Y-m-d" )."'
				// GROUP BY
					// kadart, kaddis
				// ORDER BY
					// id DESC
				// ";

		$sql = "SELECT
					id, kaddis, kadcpx
				FROM
					{$bd}_000054
				WHERE
					kaddis > 0 AND
					kadart = '{$art['cod']}' AND
					kadhis = '{$pac['his']}' AND
					kading = '{$pac['ing']}' AND
					kadfec = '".date( "Y-m-d", time()-24*3600 )."'
				GROUP BY
					kadart, kaddis
				ORDER BY
					id DESC
				";

		$res = mysql_query( $sql, $conex );

		if( $rows = mysql_fetch_array( $res ) ){

			$art['fra'] = 1;		//La fraccion se deja en 1

			if( $rows[1] < $art['can']/$art['fra'] ){

				$dev2 = true;
				$art1 = $art;
				$art1['can'] = ($art['can']/$art['fra'] - $rows[1])*$art['fra'];

				$art['can'] = $rows[1]*$art['fra'];
			}


			$cpx = crearAplicacionesDevueltasPorHoras( $rows[2], $art['can']/$art['fra'] );


			//Si la cantidad a dispensar queda en 0
			//La hora de dispensacion debe quedar en 00:00:00
			if( $rows[1] == $art['can']/$art['fra'] ){

				$sql = "UPDATE
							{$bd}_000054
						SET
							kaddis = kaddis-".$art['can']/$art['fra'].",
							kadhdi = '00:00:00',
							kadcpx = '$cpx'
						WHERE
							id='{$rows[0]}';";
			}
			else{
				$sql = "UPDATE
							{$bd}_000054
						SET
							kaddis = kaddis-".$art['can']/$art['fra'].",
							kadcpx = '$cpx'
						WHERE
							id='{$rows[0]}';";
			}

			$result = mysql_query( $sql, $conex );

			if( $result && mysql_affected_rows() > 0 ){

				$sql2 = "UPDATE
							{$bd}_000054
						SET
							kadcdi = kadcdi+".$art['can']/$art['fra'].",
							kadsad = kadsad+".$art['can']/$art['fra']."
						WHERE
							kadfec = '".date( "Y-m-d" )."' AND
							kadreg='{$rows[0]}';"; //echo "<br>El unico.......<pre>$sql</pre>";

				$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );

				if( $dev2 ){
					return devolucionesKE( $art1, $pac, $cco, $a, true );
				}
				else{
					return true;
				}
			}
			else{
				return false;
			}
		}
	}
}

// function devolucionesKE( $art, $pac, $cco, &$idRegistro, $tras = true ){

	// global $conex;
	// global $bd;
	// global $centraldemezclas;
	// global $fecDispensacion;
	// global $usuario;

	// $ori = 'SF';

	// if( $cco['cod'] == $centraldemezclas ){
		// $ori = 'CM';
	// }

	// if( true ){

		// $dev2 = false;

		// $sql = "SELECT
					// id, kaddis, kadcpx
				// FROM
					// {$bd}_000054
				// WHERE
					// kaddis > 0 AND
					// kadart = '{$art['cod']}' AND
					// kadhis = '{$pac['his']}' AND
					// kading = '{$pac['ing']}' AND
					// kadfec = '".date( "Y-m-d" )."'
				// GROUP BY
					// kadart, kaddis
				// ORDER BY
					// id DESC
				// ";

		// $res = mysql_query( $sql, $conex );

		// if( $rows = mysql_fetch_array( $res ) ){

			// $art['fra'] = 1;		//La fraccion se deja en 1

			// if( $rows[1] < $art['can']/$art['fra'] ){

				// $dev2 = true;
				// $art1 = $art;
				// $art1['can'] = ($art['can']/$art['fra'] - $rows[1])*$art['fra'];

				// $art['can'] = $rows[1]*$art['fra'];
			// }


			// $cpx = crearAplicacionesDevueltasPorHoras( $rows[2], $art['can']/$art['fra'] );


			// //Si la cantidad a dispensar queda en 0
			// //La hora de dispensacion debe quedar en 00:00:00
			// if( $rows[1] == $art['can']/$art['fra'] ){

				// $sql = "UPDATE
							// {$bd}_000054
						// SET
							// kaddis = kaddis-".$art['can']/$art['fra'].",
							// kadhdi = '00:00:00',
							// kadcpx = '$cpx'
						// WHERE
							// id='{$rows[0]}'";
			// }
			// else{
				// $sql = "UPDATE
							// {$bd}_000054
						// SET
							// kaddis = kaddis-".$art['can']/$art['fra'].",
							// kadcpx = '$cpx'
						// WHERE
							// id='{$rows[0]}'";
			// }

			// $result = mysql_query( $sql, $conex );

			// if( $result && mysql_affected_rows() > 0 ){

				// if( $dev2 ){
					// return devolucionesKE( $art1, $pac, $cco, $a, true );
				// }
				// else{
					// return true;
				// }
			// }
			// else{
				// return false;
			// }
		// }
	// }
// }

function descontarConsecutivoDevolucion( ){

	global $conex;
	global $bd;

	$sql = "UPDATE {$bd}_000001
				SET connum = connum-1
			WHERE
				contip = 'devcon'
			"; //echo ".....".$sql;

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Borra el encabezado de un movimiento
 *
 * @param $consecutivo
 * @return unknown_type
 */
function eliminarMovimientoDeDevolucion( $consecutivo ){

	global $conex;
	global $bd;

	$sql = "DELETE FROM
				{$bd}_000035
			WHERE
				dencon = '$consecutivo'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

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

function enAlta( $his, $ing){

	global $conex;
	global $bd;

	$val = '';

	$sql = "SELECT
				ubiald
			FROM
				{$bd}_000018
			WHERE
				ubihis = '$his'
				AND ubiing = '$ing'
				AND ubiald = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Cconom'];
	}

	return $val;
}

function esTraslado( $cod ){

	global $conex;
	global $bd;

	$sql = "SELECT
				ccotra
			FROM
				{$bd}_000011
			WHERE
				ccocod = '$cod'
				AND ccotra = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		return true;
	}

	return false;
}

function consultarHabitacion( $his, $ing ){

	global $conex;
	global $bd;

	$val = '';

	$sql = "SELECT
				ubihac
			FROM
				{$bd}_000018
			WHERE
				ubihis = '$his'
				AND ubiing = '$ing'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['ubihac'];
	}

	return $val;
}

function nombreCcoCentralCamilleros( $codigo ){

	global $conex;
	global $bd;

	global $bdCencam;

	$bdCencam = "cencam";

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
				{$bdCencam}_000003(    medico  , fecha_data, hora_data,   Origen ,  Motivo  , Habitacion, Observacion,   Destino ,  Solicito  , Ccosto, Camillero   , Hora_respuesta, Hora_llegada, Hora_cumplimiento, Anulada, Observ_central, Central,   Usu_central    ,   Seguridad   )
							VALUES( '$bdCencam',  '$fecha' ,  '$hora' , '$origen', '$motivo',   '$hab'  ,     ''     , '$destino', '$solicita', '$cco', '$camillero',    '$hora'    ,  '00:00:00' ,     '00:00:00'   ,   'No' ,    ''         ,'SERFAR', '', 'C-$bdCencam' )
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

// Funcion que permite extraer la edad del paciente en años, meses y dias.
  function calcularAnioMesesDiasTranscurridos($fecha_inicio, $fecha_fin = '')
    {
        $datos = array('anios'=>0,'meses'=>0,'dias'=>0);

        if($fecha_inicio != '' && $fecha_inicio != '0000-00-00')
        {
            $fecha_de_nacimiento = $fecha_inicio;

            $fecha_actual = date ("Y-m-d");
            if($fecha_fin != '' && $fecha_fin != '0000-00-00')
            {
                $fecha_actual = $fecha_fin;
            }
            // echo "<br>Fecha final: $fecha_actual";
            // echo "<br>Fecha inicio: $fecha_de_nacimiento";

            // separamos en partes las fechas
            $array_nacimiento = explode ( "-", $fecha_de_nacimiento );
            $array_actual = explode ( "-", $fecha_actual );

            $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
            $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
            $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días

            //ajuste de posible negativo en $días
            if ($dias < 0)
            {
                --$meses;

                //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
                switch ($array_actual[1]) {
                    case 1:     $dias_mes_anterior=31; break;
                    case 2:     $dias_mes_anterior=31; break;
                    case 3:
                            if (checkdate(2,29,$array_actual[0]))
                            {
                                $dias_mes_anterior=29; break;
                            } else {
                                $dias_mes_anterior=28; break;
                            }
                    case 4:     $dias_mes_anterior=31; break;
                    case 5:     $dias_mes_anterior=30; break;
                    case 6:     $dias_mes_anterior=31; break;
                    case 7:     $dias_mes_anterior=30; break;
                    case 8:     $dias_mes_anterior=31; break;
                    case 9:     $dias_mes_anterior=31; break;
                    case 10:     $dias_mes_anterior=30; break;
                    case 11:     $dias_mes_anterior=31; break;
                    case 12:     $dias_mes_anterior=30; break;
                }
                $dias=$dias + $dias_mes_anterior;
            }

            //ajuste de posible negativo en $meses
            if ($meses < 0)
            {
                --$anos;
                $meses=$meses + 12;
            }
            //echo "<br>Tu edad es: $anos años con $meses meses y $dias días";
            $datos['anios'] = $anos;

        }

        return $datos;
    }


   function traerresponsable($whis, $tipo_consulta, $emp)
    {

         global $conex;
		 
		 $wemp_pmla = $emp;
		
		 $bd = consultarAliasPorAplicacion($conex, $emp, "movhos");
		 
         switch ($tipo_consulta) {
              case 'habitacion':

                                $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
                                    ."   FROM ".$bd."_000020, root_000036, root_000037, ".$bd."_000018, ".$bd."_000016"
                                    ."  WHERE Habcod = '".$whis."'"
                                    ."    AND habhis  = Inghis"
                                    ."    AND habing  = Inging"
                                    ."    AND habhis  = orihis "
                                    ."    AND habing  = oriing "
                                    ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
                                    ."    AND oriced  = pacced "
                                    ."    AND oritid  = pactid "
                                    ."    AND habhis  = ubihis "
                                    ."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
                                    ."  ORDER BY Habord, Habcod ";
                                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $row = mysql_fetch_array($res);


                 break;

              case 'historia':

                                $q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
                                    ."   FROM root_000036, root_000037, ".$bd."_000018, ".$bd."_000016"
                                    ."  WHERE Inghis = '".$whis."'"
                                    ."    AND ubihis  = Inghis"
                                    ."    AND ubiing  = Inging"
                                    ."    AND ubihis  = orihis "
                                    ."    AND ubiing  = oriing "
                                    ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
                                    ."    AND oriced  = pacced "
                                    ."    AND oritid  = pactid "
                                    ."  GROUP BY 1, 2, 3, 4, 5, 6, 7 "
                                    ."  ORDER BY Inghis, Inging ";
                                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $row = mysql_fetch_array($res);

                 break;

              case 'cedula':

                                $q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
                                    ."   FROM root_000036, root_000037, ".$bd."_000016"
                                    ."  WHERE Oriced = '".$whis."'"
                                    ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
                                    ."    AND oriced  = pacced "
                                    ."    AND oritid  = pactid "
                                    ."    AND inghis  = orihis "
                                    ."    AND inging  = oriing "
                                    ."  GROUP BY 1, 2, 3, 4, 5, 6, 7 ";
                                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $row = mysql_fetch_array($res);


                 break;
             default:
                 break;
         }


        $wresponsable = $row['ingnre'];

        return $wresponsable;
    }


/**
 *
 * @param $cbCrearPeticion1	Indica si debe crear la petición del camillero. on para si de lo contrario no se crea
 * @param $ccoCam			No requerido
 * @param $hab				Habitación
 * @param $solicita			Código de quien solicita el servicio
 * @param $origen			Centro de costos origen
 * @param $destino			Centro de costos destino
 * @param $paciente			Nombre del paciente
 * @param $his				Historia del paciente
 * @param $ing				Ingereso del paciente
 * @return unknown_type
 */
function peticionCamillero( $cbCrearPeticion1, $ccoCam, $hab, $solicita, $origen, $destino, $paciente, $his, $ing ){

	global $seHizoSolicitudCamillero;
	global $emp;
	global $conex;
	
	$bd = consultarAliasPorAplicacion($conex, $emp, "movhos");

	if( $cbCrearPeticion1 == 'off' ){

	}
	elseif( $cbCrearPeticion1 == 'on' ){

		$nomCcoDestino = nombreCcoCentralCamilleros( $destino );

		if( enAlta( $his, $ing ) ){
			$motivo = 'DEVOLUCION POR ALTA';
		}
		else{
			$motivo = 'DEVOLUCION MEDICAMENTOS';
		}
		
		$q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex,orihis"
			."   FROM root_000036, root_000037, ".$bd."_000020 "
			."  WHERE habcod = '".$hab."'"           //Como Habitacion
			."    AND habhis = orihis "
			."    AND habing = oriing "
			."    AND oriori = '".$emp."'"
			."    AND oriced = pacced "
			."    AND oritid = pactid ";
		$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
		$rowhab = mysql_fetch_array($reshab);

		$whis = $rowhab['orihis'];
		$numhab = mysql_num_rows($reshab);
		$wedad = calcularAnioMesesDiasTranscurridos($rowhab[4], $fecha_fin = '');
		$wresponsable = traerresponsable($hab, 'habitacion', $emp);
		
		switch ($rowhab['Pacsex']) {
						case 'M':
								$wgenero = "Masculino";
						break;

						case 'F':
								$wgenero = "Femenino";
						break;

						default:
							break;
					}
		
		$val = crearPeticionCamillero( nombreCcoCentralCamilleros( $origen ), $motivo, "<b>Hab: ".$hab."</b><b><br>His: ".$whis."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3]."<br>Edad:".$wedad['anios']."<br>Genero:".$wgenero."<br>Responsable:".$wresponsable, $nomCcoDestino, $solicita, $nomCcoDestino, buscarCodigoNombreCamillero() );

		if( $val ){
			$seHizoSolicitudCamillero = true;
		}
	}
}


/**
 * @version 2007-11-27
 *
 * @modified 2007-11-06 No se permite devolver sino el minimo facturable. Carolina Castaño. Se pone la opcion de retornar, se organizan las cosas en orden alfabetico
 * 						Se quita el sobrante
 * @modified 2007-11-06 Validaciones de carro de dispensacion
 * @modified 2007-09-27 Se hace la conexión odbc a facturación antes de llamar a ValidacionHistoriaUnix
 * @modified 2007-09-24 Sale la funciondevCons y se ubica en el include registrarTablas, adicionalmente se le incluyen dos parámetros ($pac, $usuario) que deben ser añadidos en el respectivo llamado.
 * @modified 2007-09-24 Se crea la función registrarSobrante() que hace el registro en la nueva tabla 000035, por lo que ya no se necesitan los queries correspondientes a los descartes en la 000028
 * @modified 2007-09-24 Se crea la función registrarDevFact() que hace el registro en la tabla 000028, por lo que ya no se necesita ese query.
 * @modified 2007-09-21 Se crea el campo 000028.Devapv, que indica si la devolución es por aprovechamiento o no, así que se modifica un querie en la función procesoDev(), y otros 4 queries, uno de sobrante fuente simple, movimiento de devolución por fuente simple de centro de costos con inventario en MATRIX, sobrante de fuente de aprovechamiento (que así sea aprovechamiento por ser sobrante se carga como off), y finalmente en el movimiento de devolución por fuente aprovechamientos de centro de costos con inventario en MATRIX.
 * @modified 2007-09-20 Se crea el campo 000028.Devapv, que indica si la devolución es por aprovechamiento o no, así que se modifica un querie en la función procesoDev(), y otros 4 queries, uno de sobrante fuente simple, movimiento de devolución por fuente simple de centro de costos con inventario en MATRIX, sobrante de fuente de aprovechamiento (que así sea aprovechamiento por ser sobrante se carga como off), y finalmente en el movimiento de devolución por fuente aprovechamientos de centro de costos con inventario en MATRIX.
 * @modified 2007-09-18 La funcion MostrarModificable tenia un problema, estaba comparando las opciones del descarte con con la variable que almacenaba la justificación del faltante. Se soluciona.
 * @modified 2007-09-18 Desaparecen los 2 llamados en los descartes a la función registrarAplicacion() y se cambian por llamados a la función registrarDescartes().
 * @modified 2007-09-18 Desaparecen los 3 llamados a la función registrarSaldos() y se cambian por llamados a la función registrarSaldosNoApl().
 * @modified 2007-09-18 Desaparace el array $usu y se cambia por la variable $usuario.
 * @modified 2007-09-14 Como se cambio el programa de  entrega y recibod e pacients para que los centros de costros puedan trasladar pacientes con artículos pendientes de aplicar, ya es posible que se devuelvan cosas de centrops de costos que aplican por este programa, para ello se pone "a las malas" que ningun centro de costos plica osea $cco['apl']=false, ademas que ya no se valida si es un centro de costos que no aplica para no dejar hacer la devolución.
 * @modified 2007-09-12 se creo un nuevo parámetro en la funcion registrarAplicacion() del include registro_tablas.php, es necesario envíar la ronda en la cual se va a aplicar el artículo, se modifican los llamados a la función
 * @modified 2007-09-03 Se modifica para que pida centro de costos leido por código de barras, pata que no deje hacer devoluciones a pacientes en proceso de traslado, tampoco si el centro de costos leido es diferente de hospitalario o si es hospitalario pero aplica automáticamente.
 */

/**
 * Enter description here...
 *
 * @param unknown_type $jus
 */
function crearArrayJus(&$jus)
{
	global $bd;
	global $conex;

	$q = "SELECT * "
	."      FROM ".$bd."_000023 "
	."     WHERE Jusest = 'on' ";
	$err= mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		$f=0;
		$d=0;
		$s=0;
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_array($err);
			if($row['Justip'] == "F")
			{
				$jus['F'][$f]=$row['Juscod']."-".$row['Jusdes'];
				$f=$f+1;
			}
			elseif($row['Justip'] == "D")
			{
				$jus['D'][$d]=$row['Juscod']."-".$row['Jusdes'];
				$d=$d+1;
			}
			elseif($row['Justip'] == "S")
			{
				$jus['S'][$s]=$row['Juscod']."-".$row['Jusdes'];
				$s=$s+1;
			}
		}
	}
}
/**
 * Valida que
 *  * La cantidad a descartar no sea mayor que el saldo disponible.</br>
 *  * La cantidad a devolver no sea mayor que el saldo disponible.</br>
 *  * La cantidad a faltante no sea mayor que la cantidad a devolver.</br>
 *  * Si la cantidad faltante es mayor que cero (0) la justificación no sea vacía.</br>.
 *  * La suma de la devolución y el descarte sea menor que el saldo disponible.</br>
 *
 * @verion 2007-06-07
 * @author Ana María Betancur V.
 *
 * @param Array $datos  Información de las cantidades:</br>
 *                                              Información que ingresa a la función.
 *                                              [csa]:Saldo disponible para devolver o descartar.</br>
 *                                              [cdv]:Cantidad a devolver.</br>
 *                                              [cfa]:Cantidad faltante, es decir que no sera enviada al servicio qu graba.</br>
 *                                              [jfa]:Justificación del faltante.</br>
 *                                              [cds]:Cantida a descartar.
 *
 * @return boolean
 */
function validacionBasica(&$datos, $jdv, $carro, $art, $cco)
{
	global $bd;
	global $conex;
	global $ccoUsu;

	$row1[0] = 1;


	//NO SE PUDE DEVOLVER SINO MULTIPLOS DEL MINIMO FACTURABLE, SI ES ASI PONER EN DESCARTE
	$q = " SELECT Arecde "
	. "      FROM " . $bd . "_000008 "
	. "     WHERE Areces = '" . $art . "' "
	. "     And   Arecco= '" .  $cco . "' ";
	$err1 = mysql_query($q, $conex);

	$num1 = mysql_num_rows($err1);
	if ($num1 > 0)
	{
		$row1 = mysql_fetch_array($err1);
		
		$esUrg = esUrgencias($conex, $cco );
		
		if( $esUrg || $ccoUsu['urg'] ){
			$row1[0] = validarFraccionOrdenes( $conex, $bd, $art, $cco );
		}
		
		if($datos['cdv']!=0)	//Abril 8 de 2013
		{
			if($datos['cdv']-$row1[0] < 0 or ($datos['cdv']%$row1[0])!=0)
			{
				$mod=$datos['cdv']%$row1[0];
				$ent=$datos['cdv']-$mod;

				$datos['msg'] = "La cantidad a devolver debe ser ".$ent."<br>. En el descarte debe ir el resto (".$mod.")";
				return (false);
			}
		}
	}

	//Cantidad a descartar menor que el saldo.
	if($datos['cds'] <= $datos['csa']-$carro)
	{
		//Cantidad a devolver menor que el saldo.
		if($datos['cdv'] <= $datos['csa']-$carro)
		{
			//cantidad Faltante mayor que cero
			if($datos['cfa'] >0)
			{
				//Cantidad faltante menor que la devolución??
				if($datos['cfa'] <= $datos['cdv'])
				{
					//Existe justificación del faltante
					if($datos['jfa'] == "")
					{
						$datos['msg']="Debe seleccionar una<br>justificación para el faltante";
						return (false);
					}
				}
				else
				{
					$datos['msg']="El Faltante NO <br>puede superar la Devolución";
					return (false);
				}
			}//cantidad Faltante mayor que cero


			/****************************************************************************
			 * Abril 8 de 2013
			 ****************************************************************************/
			if( $datos['cds'] >= $row1[0] ){
				$datos['msg']="La cantidad a descartar debe ser menor a ".$row1[0];
				return (false);
			}
			/****************************************************************************/

			if($jdv and $datos['cdv']>0 and $datos['jde'] == "")
			{
				$datos['msg']="Debe seleccionar una<br>justificación para la Devolución";
				return (false);
			}
			if($datos['cds']>0 and $datos['dds'] == "")
			{
				$datos['msg']="Debe seleccionar un<br>destino para el descarte";
				return (false);
			}
			//La suma del Descarte y la devolución debe ser menor al saldo disponible
			if (($datos['cdv']+$datos['cds'])> $datos['csa']-$carro)
			{
				$datos['msg']="La suma de la devolución y el <br>descarte no debe ser mayor a ".($datos['csa']-$carro);
				return (false);
			}
			else
			{
				return (true);
			}

		}//Cantidad a devolver menor que el saldo.
		else
		{
			$datos['msg']="No puede Devolver <br>mas de ".($datos['csa']-$carro);
			return (false);
		}
	}//Cantidad a descartar menor que el saldo.
	else
	{
		$datos['msg']="No puede Descartar <br>mas de.".($datos['csa']-$carro);
		return (false);
	}
}

function MostrarModificable($datos, $fue,$fueTip, $prefijo, $jus, $jdv, $carro, $ccoUsu )
{
	if(isset($datos['ckb']))
	{
		$check='checked';
	}
	else
	{
		$check='';
	}

	echo "<td class='".$datos['class']."'>";
	if(isset($datos['msg']))
	{
		if ($carro>0)
		{
			$datos['msg'].=" <font color='purple'> EN DISPENSACION HAY: <B>".$carro. "</B></br>";
		}

	}
	else
	{
		if ($carro>0)
		{
			$datos['msg']=" <font color='purple'> EN DISPENSACION HAY: <B>".$carro. "</B></br>";
		}
	}

	if(isset($datos['msg']))
	{
		echo $datos['msg'];
	}
	echo "<input type='checkbox' ".$check." name='".$prefijo."[ckb]'></td>";
	echo "<td class='".$datos['class']."' colspan='1'>".$fueTip." ".$fue."</td>";

	if(!is_int($datos['cdv']))
	{
		if($datos['cds']==0)
		{
			$datos['cds']=$datos['cdv']-floor($datos['cdv']);
		}
		$datos['cdv']=floor($datos['cdv']);
	}



	if( $ccoUsu['urg'] ){
		echo "<td class='".$datos['class']."'><input readonly type='text' size='3' name='".$prefijo."[cdv]' value='0' class='texto1' onblur=validar('".$prefijo."[cdv]')></td>";
		echo "<td class='".$datos['class']."'><select name='".$prefijo."[jde]' class='texto1'>";
		echo "<option value=''>Seleccionar...</option>";
		echo "</select></td>";
	}
	else{
		echo "<td class='".$datos['class']."'><input type='text' size='3' name='".$prefijo."[cdv]' value='".$datos['cdv']."' class='texto1' onblur=validar('".$prefijo."[cdv]')></td>";
		if($jdv)
		{
			echo "<td class='".$datos['class']."'><select name='".$prefijo."[jde]' class='texto1'>";
			echo "<option value=''>Seleccionar...</option>";
			$countJusD=count($jus['D']);
			for($k=0;$k<$countJusD;$k++)
			{
				if($datos['jde'] == $jus['D'][$k])
				{
					echo "<option selected>".$jus['D'][$k]."</option>";
				}
				else
				{
					echo "<option>".$jus['D'][$k]."</option>";
				}
			}
			echo "</select></td>";
		}
		else
		{
			echo "<input type='hidden' name='".$prefijo."[jde]' value='".$datos['jde']."'>";
			echo "<td class='".$datos['class']."'></td>";
		}
	}
	echo "<td class='".$datos['class']."'><input type='text' size='3' name='".$prefijo."[cfa]' value='".$datos['cfa']."' class='texto1'></td>";//cantidad faltante
	echo "<td class='".$datos['class']."'><select name='".$prefijo."[jfa]' class='texto1'>";
	echo "<option value=''>Seleccionar...</option>";
	$countJusF=count($jus['F']);

	for($k=0;$k<$countJusF;$k++)
	{
		if($datos['jfa'] == $jus['F'][$k])
		{
			echo "<option selected>".$jus['F'][$k]."</option>";
		}
		else
		{
			echo "<option>".$jus['F'][$k]."</option>";
		}
	}
	echo "</select></td>";

	echo "<td class='".$datos['class']."'><input type='text' readonly size='3' name='".$prefijo."[cds]' value='".$datos['cds']."' class='texto1'></td>";//cantidad descarte
	echo "<td class='".$datos['class']."'><select name='".$prefijo."[dds]' class='texto1'>";
	echo "<option value=''>Seleccionar...</option>";
	$countJusS=count($jus['S']);
	for($k=0;$k<$countJusS;$k++)
	{
		if($datos['dds'] == $jus['S'][$k])
		{
			echo "<option selected>".$jus['S'][$k]."</option>";
		}
		else
		{
			echo "<option>".$jus['S'][$k]."</option>";
		}
	}
	echo "</td>";//destino descarte
	//echo "<td class='".$datos['class']."'><input type='text' size='3' name='".$prefijo."[sob]' value='".$datos['sob']."' class='texto1'></td>";//cantidad sobrante
	echo "<input type='hidden' name='".$prefijo."[sob]' value='0'>";
	//Hidden
	echo "<input type='hidden' name='".$prefijo."[csa]' value='".$datos['csa']."'>";
	//echo "<input type='hidden' name='".$prefijo."[msg]' value='".$datos['msg']."'>";
	echo "<input type='hidden' name='".$prefijo."[class]' value='".$datos['class']."'>";
	if($datos['mod'])
	{
		echo "<input type='hidden' name='".$prefijo."[mod]' value='1'>";
	}
	else
	{
		echo "<input type='hidden' name='".$prefijo."[mod]' value=''>";
	}
}

function  MostrarNoModificable($datos, $fue,$fueTip, $prefijo, $jdv)
{

	echo "<td class='".$datos['class']."'>";
	if(isset($datos['msg']))
	echo $datos['msg'];
	echo "</td>";
	echo "<td class='".$datos['class']."' colspan='1'>".$fueTip." ".$fue."</td>";
	echo "<td class='".$datos['class']."'>".$datos['cdv']."</td>";
	if($jdv)
	{
		echo "<td class='".$datos['class']."'>".$datos['jde']."</td>";
	}
	else
	{
		echo "<td class='".$datos['class']."'></td>";
	}
	echo "<td class='".$datos['class']."'>".$datos['cfa']."</td>";
	echo "<td class='".$datos['class']."'>".$datos['jfa']."</td>";//justificación faltante
	echo "<td class='".$datos['class']."'>".$datos['cds']."</td>";
	echo "<td class='".$datos['class']."'>".$datos['dds']."</td>";
	//echo "<td class='".$datos['class']."'>".$datos['sob']."</td>";
	//echo "<input type='hidden' name='".$prefijo."[msg]' value='".str_replace("'","/'",$datos['msg'])."'>";
	echo "<input type='hidden' name='".$prefijo."[cdv]' value='".$datos['cdv']."'>";
	echo "<input type='hidden' name='".$prefijo."[jde]' value='".$datos['jde']."'>";
	echo "<input type='hidden' name='".$prefijo."[cfa]' value='".$datos['cfa']."'>";
	echo "<input type='hidden' name='".$prefijo."[jfa]' value='".$datos['jfa']."'>";
	echo "<input type='hidden' name='".$prefijo."[cds]' value='".$datos['cds']."'>";
	echo "<input type='hidden' name='".$prefijo."[dds]' value='".$datos['dds']."'>";
	echo "<input type='hidden' name='".$prefijo."[csa]' value='".$datos['csa']."'>";
	echo "<input type='hidden' name='".$prefijo."[sob]' value='".$datos['sob']."'>";
	echo "<input type='hidden' name='".$prefijo."[class]' value='".$datos['class']."'>";
	if($datos['mod'])
	{
		echo "<input type='hidden' name='".$prefijo."[mod]' value='1'>";
	}
	else
	{
		echo "<input type='hidden' name='".$prefijo."[mod]' value=''>";
	}
}

//Consulta si un centro de costos es de urgencias
function esUrgencias($conex, $servicio){

	global $wbasedato;
	global $conex;
	global $bd;

	$es = false;

	$q = "SELECT Ccourg
		 	FROM ".$bd."_000011
		   WHERE Ccocod = '".$servicio."' ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccourg'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}

/**
 * Enter description here...
 *
 * @param unknown_type $pac
 * @param unknown_type $cco
 * @param unknown_type $datos
 */
function crearArray($pac,&$cco,&$datos)
{
	global $bd;
	global $emp;
	global $conex;
	global $ccoUsu;

	$q = " SELECT * "
	."       FROM ".$bd."_000004 LEFT JOIN ".$bd."_000026  "
	."      ON Spaart = Artcod  "
	."      Where Spahis = '".$pac['his']."' "
	."        AND Spaing = '".$pac['ing']."' "
	."   ORDER BY Artcom, Spaart ";

	$err = mysql_query($q,$conex);
	echo mysql_error();
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		$cco;
		$artCod="";
		$k=-1;
		for($i=0;$i<$num;$i++)
		{
			$row= mysql_fetch_array($err);

//			$a=$row['Spaaen']-$row['Spaasa'];//Cantidad por fuente aprovechamiento
			$a = 0;
			//Julio 4 de 2019. Se hace redondeo de la cantidad del saldo
			$s=round( $row['Spauen']-$row['Spausa']-$a, 3 ); //Cantidad por fuente Simple
			
			$fraccion = cantidadFraccion( $conex, $bd, $row['Spaart'], $row['Spacco'] );
			
			//echo "<b>Articulo:</b>".$row['Spaart']."<br>a:".$a."<br>s:".$s."<br>";
			/**
             * Primero revisa si hay saldo para no hacer demasiados procesos si no lo hay
             */
			// if( $ccoUsu['urg'] ){
				// // $fraccion = 1;
				// // $s = $s - floor($s);
				// $s = $s - floor($s/$fraccion)*$fraccion;
			// }
			
			$esUrg = esUrgencias($conex, $row['Spacco']);
			
			if( $esUrg || $ccoUsu['urg'] ){
				$fraccion = validarFraccionOrdenes( $conex, $bd, $row['Spaart'], $row['Spacco'] );
				if( $ccoUsu['urg'] )
					$s = $s - floor($s/$fraccion)*$fraccion;
			}
			 
			if($a>0 or $s>0)
			{
				/**
                 * Es el mismo artículo
                 */
				if($row['Spaart']!= $artCod)
				{
					$k=$k+1;
					$artCod = $row['Spaart'];
					$datos[$k]['art']['cod']=$row['Spaart'];
					$msg="";
					$artCco=0;
					$datos[$k]['art']['tot']=0;
					$datos[$k]['art']['rsp']=0;

					/**
                     * Buscar el nombre del artículo.
                     */
					if(!ArticuloExiste($datos[$k]['art'], $error))
					{
						/**
                         * Si no existe en el maestro normal puede que exista en la central de mezclas.
                         * Hay que buscarlo ahi.
                         */
						$q = " SELECT * "
						."      FROM cenpro_000002 "
						."     WHERE Artcod = '".$artCod."' ";
						$err1 = mysql_query($q,$conex);
						echo mysql_error();
						$num1 = mysql_num_rows($err1);
						if($num1 >0)
						{
							$row1=mysql_fetch_array($err1);
							$datos[$k]['art']['act']=true;
							$datos[$k]['art']['cmz']=true;
							$datos[$k]['art']['class']="titulo1";
							$datos[$k]['art']['nom']=$row1['Artcom'];
							$datos[$k]['art']['uni']=$row1['Artuni'];
							$datos[$k]['art']['jdv']=true;
						}
						else
						{
							$datos[$k]['art']['nom']="NO ENCONTRADO";
							$datos[$k]['art']['uni']="NN";
							$datos[$k]['art']['act']=false;
							$datos[$k]['art']['class']="errorTit";
							$datos[$k]['art']['cmz']=false;
							$datos[$k]['art']['jdv']=false;
						}
					}
					else
					{
						/**
                   		 * Buscar si el grupo necesita Justificación de la devolución.
                         */
						$gru=explode("-",$datos[$k]['art']['gru']);
						$q = " SELECT * "
						."      FROM ".$bd."_000029 "
						."     WHERE Gjugru = '".$gru[0]."' ";
						$err1 = mysql_query($q,$conex);
						echo mysql_error();
						$num1 = mysql_num_rows($err1);
						if($num1 >0)
						{
							$row1=mysql_fetch_array($err1);
							if($row1['Gjujus'] == "on")
							{
								$datos[$k]['art']['jdv']=true;
							}
							else
							{
								$datos[$k]['art']['jdv']=false;
							}
						}
						else
						{
							$datos[$k]['art']['jdv']=false;
						}
						$datos[$k]['art']['act']=true;
						$datos[$k]['art']['class']="titulo1";
						$datos[$k]['art']['cmz']=false  ;
					}
					/**
					 * Para no tener que llamar a la función de artículos especiales, se asu me que todos permiten negativos,
					 * pues por ser devoluciones no pueden generar negativos, así que da igual.
					 */
					$datos[$k]['art']['neg']=true;
				}
				else
				{
					$artCco=$artCco+1;
				}

				$datos[$k]['cco'][$artCco]['sid']=$row['id'];
				$datos[$k]['cco'][$artCco]['rsp']=0;


				$ccoOk=false;
				$countCco=count($cco);

				for($j=0;$j<$countCco;$j++)
				{

					if($row['Spacco'] == $cco[$j]['cod'])
					{
						$datos[$k]['cco'][$artCco]['cod']=$j;
						$ccoOk=true;

					}
				}

				if(!$ccoOk)
				{
					$cco[$countCco]['cod']=$row['Spacco'];
					$ccoOk=getCco($cco[$countCco],"C",$emp);
					$cco[$countCco]['fapC']=$cco[$countCco]['fap'];
					$cco[$countCco]['fueC']=$cco[$countCco]['fue'];

					//Para que el centro de costos quede cargado con las fuentes de devolución
					$ccoOk=getCco($cco[$countCco],"D",$emp);

					$datos[$k]['cco'][$artCco]['cod']=$countCco;
					if(!$ccoOk)
					{
						$cco[$countCco]['nom']="NO ENCONTRADO, NO ES fACTURABLE ";
						$cco[$countCco]['act']=false;
						$cco[$countCco]['ima']=false;
						$cco[$countCco]['neg']=false;
						$cco[$countCco]['class']="errorTit1";
					}
					else
					{
						$cco[$countCco]['act']=true;
						$cco[$countCco]['class']="acumulado2";
					}
					/**
					 * Todos quedan como si no aplicara por que vamos a tomar en cuenta los saldos que tienen pendientes de aplicar
					 * por lo tanto nunca se deben quegerar registros de aplicación(000015) asociados a la devolución en este programa
					 */
					$cco[$countCco]['apl']=false;
				}


				if($a > 0)
				{
					$datos[$k]['cco'][$artCco]['rsp']++;
					if($cco[$datos[$k]['cco'][$artCco]['cod']]['act'] and $datos[$k]['art']['act'])
					{
						if($datos[$k]['art']['cmz'])
						{
							if($cco[$datos[$k]['cco'][$artCco]['cod']]['ima'])
							{
								$datos[$k]['cco'][$artCco]['fueA']['mod']=true;
								$datos[$k]['cco'][$artCco]['fueA']['class']="texto";
								$datos[$k]['cco'][$artCco]['fueA']['msg']="";
							}
							else
							{
								/**
								 * El artículo no esta en el maestro de artículos de la clinica,
								 * pero el centro de costos no tiene inventario por MATRIX.                                                                      *
								 */
								$datos[$k]['cco'][$artCco]['fueA']['mod']=false;
								$datos[$k]['cco'][$artCco]['fueA']['msg']="El Artículo no esta en el maestro de artículos <br> solo debe cargarse por un centro de costos <br>con inventarío en MATRIX, y este Centro de costos no es así.";
								$datos[$k]['cco'][$artCco]['fueA']['class']="error";
							}
						}
						else
						{
							$datos[$k]['cco'][$artCco]['fueA']['mod']=true;
							$datos[$k]['cco'][$artCco]['fueA']['class']="texto";
							$datos[$k]['cco'][$artCco]['fueA']['msg']="";
						}
					}
					else
					{
						$datos[$k]['cco'][$artCco]['fueA']['mod']=false;
						$datos[$k]['cco'][$artCco]['fueA']['msg']="El Centro de costos no existe <br> o no esta activado para facturar<br>o el artículo no existe";
						$datos[$k]['cco'][$artCco]['fueA']['class']="error";
					}

					/**
					 * Si el centro de costos tiene inventario en matrix (como la centrald e Maezclas)
					 * Las devoluciones deben ser enteros y las fracciones deben ser descartadas.
					 */
					if($cco[$datos[$k]['cco'][$artCco]['cod']]['ima'])
					{
						$datos[$k]['cco'][$artCco]['fueA']['cdv']=floor($a);//cantidad a devolver
						$datos[$k]['cco'][$artCco]['fueA']['cds']=$a-floor($a);//Cantidad de descarte
					}
					else
					{
						$datos[$k]['cco'][$artCco]['fueA']['cdv']=$a;//cantidad a devolver
						$datos[$k]['cco'][$artCco]['fueA']['cds']=0;//Cantidad de descarte
					}

					$datos[$k]['cco'][$artCco]['fueA']['csa']=$a;//cantidad total
					$datos[$k]['cco'][$artCco]['fueA']['jde']="";//Justificación devolución
					$datos[$k]['cco'][$artCco]['fueA']['jfa']="";//Justificación faltante
					$datos[$k]['cco'][$artCco]['fueA']['dds']="";//Destino descarte
					$datos[$k]['cco'][$artCco]['fueA']['cfa']=0;//Cantidad Faltante
					$datos[$k]['cco'][$artCco]['fueA']['sob']=0;//Sobrante
				}

				if(($s )>0)
				{
					$datos[$k]['cco'][$artCco]['rsp']++;
					if($cco[$datos[$k]['cco'][$artCco]['cod']]['act'] and $datos[$k]['art']['act'])
					{
						/*

						if($cco[$datos[$k]['cco'][$artCco]['cod']]['apl'])
						{
						$datos[$k]['cco'][$artCco]['fueS']['mod']=false;
						$datos[$k]['cco'][$artCco]['fueS']['class']="error";
						$datos[$k]['cco'][$artCco]['fueS']['msg']="El centro de costos es de aplicación automática,<br>por lo tanto no debe haber saldo disponible<br> contactar el centro de costos";
						}
						else
						{
						*/
						if($datos[$k]['art']['cmz'])
						{
							if($cco[$datos[$k]['cco'][$artCco]['cod']]['ima'])
							{
								$datos[$k]['cco'][$artCco]['fueS']['mod']=true;
								$datos[$k]['cco'][$artCco]['fueS']['class']="texto";
								$datos[$k]['cco'][$artCco]['fueS']['msg']="";
							}
							else
							{
								/**
                                     * El artículo no esta en el maestro de artículos de la clinica,
                                     * pero el centro de costos no tiene inventario por MATRIX.                                                                      *
                                     */
								$datos[$k]['cco'][$artCco]['fueS']['mod']=false;
								$datos[$k]['cco'][$artCco]['fueS']['msg']="El Artículo no esta en el maestro de artículos <br> solo debe cargarse por un centro de costos <br>con inventarío en MATRIX, y este Centro de costos no es así.";
								$datos[$k]['cco'][$artCco]['fueS']['class']="error";
							}
						}
						else
						{
							$datos[$k]['cco'][$artCco]['fueS']['mod']=true;
							$datos[$k]['cco'][$artCco]['fueS']['msg']="";
							$datos[$k]['cco'][$artCco]['fueS']['class']="texto";
						}
						//}
					}
					else
					{
						$datos[$k]['cco'][$artCco]['fueS']['mod']=false;
						$datos[$k]['cco'][$artCco]['fueS']['msg']="El Centro de costos no existe <br> o no esta activado para facturar<br>o el artículo no existe";
						$datos[$k]['cco'][$artCco]['fueS']['class']="error";
					}

					/**
					 * Si el centro de costos tiene inventario en matrix (como la centrald e Maezclas)
					 * Las devoluciones deben ser enteros y las fracciones deben ser descartadas.
					 */
					if($cco[$datos[$k]['cco'][$artCco]['cod']]['ima'])
					{
						$datos[$k]['cco'][$artCco]['fueS']['cdv']=floor($s);//cantidad a devolver
						$datos[$k]['cco'][$artCco]['fueS']['cds']=$s-floor($s);//Cantidad de descarte
					}
					else
					{
						$datos[$k]['cco'][$artCco]['fueS']['cdv']=floor($s/$fraccion)*$fraccion;//cantidad a devolver
						$datos[$k]['cco'][$artCco]['fueS']['cds']=$s-floor($s/$fraccion)*$fraccion;//Cantidad de descarte
					}

					$datos[$k]['cco'][$artCco]['fueS']['csa']=$s;//cantidad total
					$datos[$k]['cco'][$artCco]['fueS']['jde']="";//justificadión de la devolución
					$datos[$k]['cco'][$artCco]['fueS']['cfa']=0;//cantidad faltante
					$datos[$k]['cco'][$artCco]['fueS']['jfa']="";//Justificación del faltante
					$datos[$k]['cco'][$artCco]['fueS']['dds']="";//destino del descate

					$datos[$k]['cco'][$artCco]['fueS']['sob']=0;//Sobrante
				}

				$datos[$k]['art']['tot']=$datos[$k]['art']['tot']+$a+$s;
				$datos[$k]['art']['rsp']= $datos[$k]['art']['rsp']+$datos[$k]['cco'][$artCco]['rsp'];
			}
		}
	}
}

function Carro($pac, $cco, $aprov, $art)
{
	global $bd;
	global $conex;

	//aca voy a consultar cuantos elementos estan en el carro
	$q = "SELECT sum( fdecan ) FROM
	    ( SELECT sum(Fdecan - fdecar) fdecan"
	."      FROM ".$bd."_000002, ".$bd."_000003 "
	."     WHERE Fenhis=".$pac['his']." "
	."       AND Fening=".$pac['ing']." "
	."       AND Fencco='".$cco."' "
	."       AND Fentip='C".$aprov."' "
	."       AND Fdenum=Fennum "
	."       AND Fdeart='".$art."' "
	."       AND Fdedis='on' "
	."       AND Fdeest='on' "
	."     UNION All"
	."    SELECT sum(Fdecan - fdecar) fdecan"
	."      FROM ".$bd."_000002, ".$bd."_000143 "
	."     WHERE Fenhis=".$pac['his']." "
	."       AND Fening=".$pac['ing']." "
	."       AND Fencco='".$cco."' "
	."       AND Fentip='C".$aprov."' "
	."       AND Fdenum=Fennum "
	."       AND Fdeart='".$art."' "
	."       AND Fdedis='on' "
	."       AND Fdeest='on' ) as a";
	$err1 = mysql_query($q,$conex);
	echo mysql_error();
	$num1 = mysql_num_rows($err1);
	$row1=mysql_fetch_array($err1);
	if($num1 >0 and $row1[0]>0)
	{
		return $row1[0];
	}
	else
	{
		//aca voy a consultar cuantos elementos estan en el carro y son de la central
		$q = "SELECT count(distinct(Fdenum)) FROM (
		    SELECT Fdenum"
		."    FROM ".$bd."_000002, ".$bd."_000003 "
		."   WHERE Fenhis=".$pac['his']." "
		."     AND Fening=".$pac['ing']." "
		."     AND Fencco='".$cco."' "
		."     AND Fentip='C".$aprov."' "
		."     AND Fdenum=Fennum "
		."     AND Fdeari='".$art."' "
		."     AND Fdedis='on' "
		."     AND Fdeest='on' "
		." 	 UNION ALL
			SELECT Fdenum"
		."    FROM ".$bd."_000002, ".$bd."_000143 "
		."   WHERE Fenhis=".$pac['his']." "
		."     AND Fening=".$pac['ing']." "
		."     AND Fencco='".$cco."' "
		."     AND Fentip='C".$aprov."' "
		."     AND Fdenum=Fennum "
		."     AND Fdeari='".$art."' "
		."     AND Fdedis='on' "
		."     AND Fdeest='on' ) AS a";

		$err1 = mysql_query($q,$conex);
		echo mysql_error();
		$num1 = mysql_num_rows($err1);
		$row1=mysql_fetch_array($err1);
		if($num1 >0 and $row1[0]>0)
		{
			return $row1[0];
		}
		else
		{
			return 0;
		}
	}
}
/**
 * Enter description here...
 *
 * @modified 2007-09-24 Se crea la función registrarDevFact() que hace el registro en la tabla 000028, por lo que ya no se necesita ese query.
 * @modified 2007-09-21 Con motivo de la creación del campo 000028.Devart se modifica el query que registra la devolución
 * @modified 2007-09-20 Con motivo de la creación del campo 000028.Devapv se crea la variable $apv y se modifica el query que registra la devolución
 * @modified 2007-09-18 Desaparece el arreglo $usu y lo reemplaza la variable string $usuario
 * @modified 2007-09-18 No se llama a la función registrarSaldos() si no a registrarSaldosNoApl
 *
 * @param Integer       $devCons
 * @param Array         $pac            Información del paciente</br>
 *                                                              [his]:Historia del paciente.</br>
 *                                                              [ing]:
 * @param Array         $art                    [cod]:Código del artículo.</br>
 *                                                              [can]:cantidad del artículo.</br>
 *                                                              [neg]:Si el artículo permite o no generar negativos en ele inventario.
 * @param String        $jusD
 * @param Doubble       $faltante
 * @param String        $jusF
 * @param array         $cco                    Información del centro de costos</br>
 *                                                              Debe ingresar:</br>
 *                                                              [cod]:String[4].Código del centro de costos.</br>
 *                                                              [neg]:Boolean.El Centro de costos permite generar negativos en el inventario para todos los artículso.</br>
 *                                                              [apl]:Boolean. Los artículos que se cargan de este centrod e costos se graban como aplicados inmediatamente.
 * @param String[1]     $tipTrans       Tipo de transacción
 *                                                              C: cargo a cuenta de pacientes
 *                                                              D: Devolución.
 * @param Boolean       $aprov          Si es un aprovechamiento o no.
 * @paramArray          $usu            Información del usuario.
 *                                                              [usuM]:Código de MAtrix del usuario.
 * @param unknown_type $error           Almacena todo lo referente con códigos y mensajes de error
 *                                                              [ok]:Descripción corta.</br>
 *                                                              [codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010</br>
 *                                                              [codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.</br>
 *                                                              [descSis]:Descripción del error del sistema.</br>
 * @return unknown
 */
function procesoDev($devCons, $pac, $art, $jusD, $faltante, $jusF, $cco, $tipTrans, $aprov,$usuario,&$error)
{
	global $bd;
	global $conex;
	global $ccoUsu;
	global $solicitudCamillero;
	
	global $conex_o;
	global $emp;

	if( validacionDevolucion($cco,$pac,$art,$aprov,false,$error))
	{
		if(TarifaSaldo($art,$cco,"D",$aprov,$error))
		{
			$dronum="";
			$drolin="";
			$date=date("Y-m-d");
			$cns="";
			if($aprov)
			{
				$fuente=$cco['fap'];
			}
			else
			{
				$fuente=$cco['fue'];
			}

			if(Numeracion($pac, $fuente, 'D', $aprov, $cco, $date, $cns, $dronum, $drolin, $pac['dxv'], $usuario, $error ))
			{
				$wcenpro = consultarAliasPorAplicacion( $conex, $emp, "cenmez" );
				$art['fra'] = 1;
				
				//Consulto las empresas a las que se requiere el cambio de articulo equivalente
				$responsablesEq = consultarAliasPorAplicacion( $conex, $emp, "empresaConEquivalenciaMedEInsumos" );
				// $tipoEmpresaParticular = consultarAliasPorAplicacion( $conex, $emp, "tipoempresaparticular" );
				$resPaciente = consultarResponsable( $conex, $pac['his'], $pac['ing'] );
				$admiteEquivalencia = false;

				// if( $tipoEmpresaParticular != $resPaciente['tipoEmpresa'] ){
					
					$responsablesEq = explode( ",", $responsablesEq );

					$admiteEquivalencia = array_search( $resPaciente['responsable'], $responsablesEq ) === false ? false: true;
					$admiteEquivalencia = $admiteEquivalencia === false && array_search( '*', $responsablesEq ) !== false ? true: false;							

					$reInsProducto = consultarInsumosProducto( $wcenpro, $bd, $art[ 'cod' ] );
				// }

				if( @mysql_num_rows( $reInsProducto ) == 0 || !$admiteEquivalencia ){

					/****************************************************************************
					 * Noviembre 12 de 2013
					 ****************************************************************************/
					//Consulto código equivalente
					$artEq = consultarArticuloEquivalente( $cco[ 'cod' ], $art[ 'cod' ] );
					$auxArtEq = $art;
					if( !empty( $artEq ) && $admiteEquivalencia ){
						$art['uni'] = $artEq[ 'Artuni' ];
						$art['can'] = $artEq['Areceq']*$artEq['Arefra']*$art['can']/$art['fra'];		//Convierto la cantidad a cargar en la nueva para el medicamento equivalente
						$art['cod'] = $artEq['Areaeq'];													//Reemplazo el código del articulo por el código equivalente
						$art['fra'] = $artEq['Arefra'];
					}
					/****************************************************************************/
					$artValido =registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, $error);
					//Octubre 13 de 2015. Cargos ERp
					CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );

					/************************************************************************************
					 * Febrero 27 de 2014
					 ************************************************************************************/
					if( !empty( $artEq ) && $artValido && $admiteEquivalencia ){

						registrarLogArticuloEquivalente( $conex, $bd, $auxArtEq, $art, $dronum, $drolin, 'off' );

						//Se hace un ajuste de entrada para cada uno de los insumos iguale a la cantidad dispensado
						list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntrada" ) );
						if( $tipTrans != 'C' ){
							list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalidaDevolucion" ) );
						}
						ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
					}
					/************************************************************************************/

					$art = $auxArtEq;			//Noviembre 12 de 2013

					/************************************************************************************
					 * Febrero 27 de 2014
					 ************************************************************************************/
					if( !empty( $artEq ) && $artValido && $admiteEquivalencia ){
						//Se hace un ajuste de Salida de inventario para el articulo que se va dispensar
						list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalida" ) );
						if( $tipTrans != 'C' ){
							list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntradaDevolucion" ) );
						}
						ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
					}
					/************************************************************************************/
				}
				else{
					registrarInsumosProducto( $reInsProducto, $cco, $dronum, $drolin, $fuente, $date, $pac, $art, $error, $tipTrans, $aprov );

					//Se hace un ajuste de entrada para cada uno de los insumos igual a la cantidad dispensado
					list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalida" ) );
					if( $tipTrans != 'C' ){
						list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntradaDevolucion" ) );
					}
					ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
					
					CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );
					
					$artValido = true;
				}
				
				// if(registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, &$error))
				if( $artValido )
				{
					$art['ubi']='US';
					$art['ini']=$art['cod'];
					//Mover elsaldo en la tabla de saldos normales.
					$art['lot']=" ";
					if(registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario,$error))
					{
						if(registrarSaldosNoApl($pac,$art,$cco,$aprov,$usuario,"D",false,$error))
						{
							/******************************************************************************************************
							 * Abril 15 de 2013
							 ******************************************************************************************************/
							$resAut = consultarArticulosACargarAutomaticamente( $art['cod'] );

							if( $resAut ){

								$numResAut = mysql_num_rows( $resAut );

								if( $numResAut > 0 ){

									while( $rowsResAut = mysql_fetch_array( $resAut ) ){

										$art2['cod'] = $rowsResAut[ 'Artcod' ];
										$art2['can'] = $rowsResAut[ 'Acpcan' ]*$art['can'];
										$art2['ini'] = $rowsResAut[ 'Artcod' ];
										$art2['ser'] = $art['ser'];
										$art2['ubi'] = $art['ubi'];
										$art2['nom'] = $rowsResAut[ 'Artcom' ];
										$art2['uni'] = $rowsResAut[ 'Artuni' ];

										$artValido = Numeracion($pac, $fuente, 'D', $aprov, $cco, $date, $cns, $dronum, $drolin2, $pac['dxv'], $usuario, $error );
										$artValido = registrarItdro($dronum, $drolin2, $fuente, $date, $cco, $pac, $art2, $error);
										$artValido = registrarDetalleCargo($date, $dronum, $drolin2, $art2, $usuario,$error);
										// $artValido = registrarSaldosNoApl($pac,$art2,$cco,$aprov,$usuario,"D",false,&$error);

										$ardrolin2[ $art2['cod'] ] = $drolin2;

										$fecApl2=$date;
										// $ronApl=date("G:i - A");
										$ronApl2=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );
										registrarAplicacion($pac, $art2, $cco,$aprov,$fecApl2,$ronApl2, $usuario, $tipTrans, $dronum,$ardrolin2[ $art2['cod'] ], $error);
									}

									mysql_data_seek( $resAut, 0 );	//reseteo nuevamente la consulta por si toca hacer la aplicación automática
								}
							}
							/******************************************************************************************************/

							//Registrar movimiento de la devolución de facturación
							if(registrarDevFact($devCons,$dronum,$drolin,$art['can'], $jusD,$faltante,$jusF,$usuario,$error))
							{
								if( !$solicitudCamillero ){
									if( esTraslado( $cco['cod'] ) && true ){ //echo "paso1111.... ".$ccoUsu['cod'];
										peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoUsu['cod'], $cco['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
									}
									$solicitudCamillero = true;
								}
								return (true);
							}
							else
							{
								return(false);
							}
						}
						
					}// if de registrarDetalleCargo()
					else
					{
						return(false);
					}
				}//if de registrarItdro()
				else
				{
					return(false);
				}
				
			}//if de Numeración()
			else
			{
				return(false);
			}
		}
		else
		{
			//Sin tarifa, saldo
			return(false);
		}
	}
	else
	{
		//Sin  cantidad a devolver
		$error['ok']="ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN, O UN DESCARTE DE ESTE ARTÍCULO A ESTE PACIENTE MIENTRAS UD. HACIA ESTA DEVOLUCION.";
		return(false);
	}
}


/**
 * PROGRAMA DE DEVOLUCIÓN DE ARTÍCULOS DE UN PACIENTE
 *
 * Este programa pide la historría y  el ingreso. Para la histori valida: </br>
 * Que este activa en UNIX.</br>
 * Que no este de alta definitiva, si esta con alta en cproceso puede funcionar perfectamente.</br>
 *
 * Una vez se comprueban la historia  ingreso se muestra las cantidades de los articulos que estan pendientes
 * por cargar o es posible devolver, para cada artículo se muestra la cantidad por centro de costos y por fuente,
 * pues pudo haber sido grabada como devolución o como aprovechamiento.</br>
 *
 * Para ello se va a manejar dos arreglos uno donde  estan los centros de costos con su respectiva información, así:
 * $cco[$i]['cod']=...
 *
 *
 * Y otro arreglo en donde cada artículo tiene asociada mucha informpación.</br>
 * Primero la información propia del artículo:</br>
 *  * [$i]['art']['cod']
 *  * [$i]['art']['nom']
 *  * [$i]['art']['uni']</br>
 *  * [$i]['art']['cmz']:Boolean. Pertenece a la central de Mezclas.</br>
 *  * [$i]['art']['class']:</br>
 *  * [$i]['art']['act']</br>
 *  * [$i]['art']['rsp']
 *  * [$i]['art']['jde']:Boleean. Requiere justificación en para la devolución.
 *
 * Segundo por cada centro de costos donde tiene saldo disponible el artículo:</br>
 *  * [$i]['cco']['con']: el número del arreglo del centro de costos conde esta la info del centro de costos.</br>
 *  * Para las dos fuentes de carga si hay un saldo pendiente (si no no, lo que hace que no queden los registros de los centros de costos que se aplicanautomaticamente),
 *    simple(?=S) y aprovechamiento(?=A), se guarda dentro del arreglo:</br>
 *  ** ['cco'][$j]['fue?']['csa']:Real. Saldo del paciente= cantidad suceptible de ser devuelta o descartada.
 *  ** ['cco'][$j]['fue?']['cdv']:Real. Cantidad a Devolver.
 *  ** ['cco'][$j]['fue?']['cds']:Real. Cantidad a descartar.
 *  ** ['cco'][$j]['fue?']['jfa']:String. Justificación del faltante.
 *  ** ['cco'][$j]['fue?']['msg']:String. Mensaje de exito o fracazo de las transacciones del registro.</br>
 *  ** ['cco'][$j]['fue?']['mod']:Boolean. Se puede modificar o no, inicialmente el único motivo para que no sea modificable es que
 *     el centro de costos no este activo.  Una vez se ejecuta el programa si el usuario efectua una transacción sobre el registro el
 *     registro queda inmodificable
 *  ** ['cco'][$j]['fue']
 *
 * Una vez se tiene el arreglo se le dan al usuario para cada artículo las siguientes opciones:</br>
 *  * Cantidad a devolver de la cuanta del paciente.
 *      * Cantidad a devolver al servicio que cargo el artículo, si no corresponde a la cantidad a devolver debe elegirse una justificación.
 *  * Cantidad que se desacarta, es una cantidad que no se puede aplicar al paciente pero corresponde solo a una fracción de la presentación que se cobra a la entidad, por lo tanto no se devuelve de la cuenta.
 *
 * Una vez el usuario ingresa información y da aceptar el sistema Valida que por medio de la opción validaciionBasica() :</br>
 *  * La cantidad a descartar no sea mayor que el saldo disponible.</br>
 *  * La cantidad a devolver no sea mayor que el saldo disponible.</br>
 *  * La cantidad a faltante no sea mayor que la cantidad a devolver.</br>
 *  * Si la cantidad faltante es mayor que cero (0) la justificación no sea vacía.</br>.
 *  * La suma de la devolución y el descarte sea menor que el saldo disponible.</br>
 * Si no hay exito en alguna de las validaciones [$i]['cco']['fue?']['mod']=true para que el usuario vuelva a intentarlo.</br></br>
 *
 * Una vez se realiza la delas opciones, y cantidades elegidas, para el artículo, fuente y centro de costos:
 *      * Si la cantidad a devolver es mayor que cero entonces se llama a la función procesoDevolucion()(ver función).</br>
 *  * Si la cantidad a descartar es mayor que cero se llama a la función procesoDescarte() (ver función).</br>
 *
 * Finalmente se muestra al usuario los resultados de sus trabnsacciones y la posibilidad de hacer mas sobre los artículos que no haya modificado.</br>
 *
 * @wvar Array $array
 *                                                              [$i]['cco'][$j]['fue?']['mod']:Boolean. Se puede modificar o no, si no se puede es por que ya se modifico.
 *                                                              [$i]['cco'][$j]['fue?']['csa']:Real. Saldo del paciente= cantidad suceptible de ser devuelta o descartada.
 *                                                              [$i]['cco'][$j]['fue?']['cdv']:Real. Cantidad a Devolver.
 *                                                              [$i]['cco'][$j]['fue?']['cds']:Real. Cantidad a descartar.
 *                                                              [$i]['cco'][$j]['fue?']['cfa']:Real. Cantidad a Faltante.
 *                                                              [$i]['cco'][$j]['fue?']['jfa']:String. Justificación del faltante.
 *                                                              [$i]['cco'][$j]['fue?']['msg']:String. Mensaje de exito o fracazo de las transacciones del registro.</br>
 *                                                              [$i]['cco'][$j]['rsp']
 * @wvar Array $pac
 *
 *
 */

global $wemp_pmla; 
$wemp_pmla = $emp; 

$accion_iq = "";
$desde_CargosPDA = true;
$existeFacturacionERP = false;
 
include_once("cenpro/devolucionCM.php");
include_once("movhos/validacion_hist.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/registro_tablas.php");
include_once("movhos/otros.php");
include_once("root/empresa.php");
include_once("root/magenta.php");
include_once("root/comun.php");
include_once("movhos/cargosSF.inc.php");

include_once("ips/funciones_facturacionERP.php");




$serviciofarmaceutico = '1050';
$centraldemezclas = '1051';


if(!isset($_SESSION['user']))
echo "error";
else
{	
	$solicitudCamillero = false;
	$seHizoSolicitudCamillero = false;
	$articulosDevueltos = 0;	//indica si se ha hecho correctamente una devolucion, un descarte o un faltante
	//modificacion 2007-11-21 pregunto si se puede utilizar el programa, se restringen unas horas en la tabla 50 de root

	$horario='';
	$wactualiz =  "2018-12-12";
	if (ConsultarHorario($horario, $emp))
	{

		connectOdbc($conex_o, 'inventarios');
		$usuario=substr($user,2);
		encabezado(" Devoluci&oacute;n ",$wactualiz,"clinica");
		echo "<br><br>";
		echo "<table align='center' border='0'>";
		/*echo "<tr><td class='tituloSup' >DEVOLUCIONES</td></tr>";
		echo "<tr><td class='tituloPeq' >devoluciones.php Version 2013-11-19<br><br></td></tr>";
		echo "<tr>";*/
		if($conex_o != 0)
		{

			/****************************************************************************************
			 * Con conexion con Unix
			 ****************************************************************************************/

			if(!isset($historia))
			{
				echo "<center><table border='0' width='300' align='center'>";
				echo "<form name='devApl' action='' method='POST'> ";
				if(!isset($ccoCod))
				{
					//Busqueda del centro de costos al que pertenece el usuario.

					$q="SELECT Cc "
					."FROM  root_000025 "
					."WHERE Empleado = '".$usuario."' ";
					$err = mysql_query($q,$conex);
					$num = mysql_num_rows($err);
					if($num >0)
					{
						$row=mysql_fetch_array($err);
						$cco['cod']=$row['Cc'];
						$ok=getCco($cco,"D",$emp);
					}
					else
					{
						/*No esta el usuario en Matrix*/
						$pac['his']='0';
						$art['cod']="NO APLICA";
						$error['codInt']="0002";
						$cco['cod']='0000';
						if($err == "")
						{
							$error['codSis']=mysql_errno($conex);
							$error['descSis']=str_replace("'","*",mysql_error($conex));
						}
						else
						{
							$error['codSis']=$err;
							$error['descSis']=$err;
						}

						//registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
						registrarError('NO INFO',$cco,'NO INFO','0', '0',$pac,$art,$error, $color,$warning,$usuario);
						$printError="<CENTER>EL CODIGO DE USUARIO NO EXISTE";
						$ok=false;
					}
				}
				else
				{
					//Determinar que el centro de costos haya sido leido.
					$pos=strpos(strtoupper($ccoCod),"UN.");
					if($pos === 0)//Tiene que ser triple igual por que si no no funciona
					{
						$cco['cod']=substr($ccoCod,3);
						if(!getCco($cco,"D",$emp))
						{
							$printError="EL CENTRO DE COSTOS NO EXISTE O NO ESTA HABILITADO PARA REALIZAR CARGOS";
							$ok=false;
						}
						else
						{
							$cco['sel']=false;
							$ok=true;
						}
					}
					else
					{
						$printError="EL CENTRO DE COSTOS NO FUE LEIDO POR EL CODIGO DE BARRAS ADECUADO";
						$ok=false;
					}
				}
				
				/************************************************************************************************
				 * Diciembre 11 de 2014
				 ************************************************************************************************/
				if( $cco[ 'urg' ] ){
					$cco['hos'] = true;
					$cco['apl'] = false;
				}
				/************************************************************************************************/

				if($ok)
				{

					if($cco['sel'])
					{
						echo "<tr><td class='titulo1'>USUARIO: ".$usuario;
						echo "<tr><td class='titulo1' ><b>CC: ";
                                ?>      <input type='password' size='7' name='ccoCod' onload=''>
                                <script language="JAVASCRIPT" type="text/javascript">
                                document.devApl.ccoCod.focus();
                                </script>
                                <?php
                                echo "</td></tr>";
                                echo "<input type='hidden' name='ccoUsu[cod]' value='".$cco['cod']."'>";
                                echo "<input type='hidden' name='ccoUsu[nom]' value='".$cco['nom']."'>";
                                echo"<tr><td  class='titulo2'><input type='submit' value='ACEPTAR'></td></tr></form>";
					}
					else
					{
						if($cco['hos'] and !$cco['apl'])
						{
							echo "<tr><td class='tituloSup'>".$cco['cod']."-".$cco['nom']."</td></tr>";
							echo "<tr><td class='tituloSup'>USUARIO: ".$usuario."</b>";
							echo "<tr>";
							echo "<td class='titulo2' ><b>N° HISTORIA: ";
                                ?>
                                <input type='text' size='9' name='historia'>
                                <script language="JAVASCRIPT" type="text/javascript">
                                document.devApl.historia.focus();
                                </script>
                                <?php
                                echo "</td></tr>";
                                echo "<td class='titulo2' ><b>Alta en Proceso: <input type='checkbox' name='alp'></td></tr>";
                                echo "<input type='hidden' name='ccoUsu[cod]' value='".$cco['cod']."'>";
                                echo "<input type='hidden' name='ccoUsu[nom]' value='".$cco['nom']."'>";
                                echo"<tr><td  class='titulo2'><input type='submit' value='ACEPTAR'></td></tr></form>";
						}
						else
						{
							$printError="EL CENTRO DE COSTOS NO ES HOSPITALARIO O ES DE APLICACICÓN AUTOMÁTICA<br>"
							."Este programa solo puede ser desde centros de costos hospitalarios que no apliquen automáticamente.";
							$ok=false;
						}
					}
					echo "<input type='hidden' name ='usuario' value='".$usuario."' >";
				}

				if(!$ok)
				{
					echo "<tr><td class='errorTitulo'>".$printError;
					echo "</td></tr></table>";
				}
			}
			else
			{
				if($historia != "")
				{
					$pac['his']=ltrim(rtrim($historia));

					//Calidación por Matrix

					if($pac['his'] == 0)
					{
						$pac['dxv']=true;
					}
					else
					{
						$pac['dxv']=false;
					}

					//Valida que esté activo en UNIX
					$conex_f = odbc_connect('facturacion','','');

					$pac['act'] = ValidacionHistoriaUnix($pac, $warning, $error);
					odbc_close($conex_f);
					
					$ccoOri = $ccoUsu;
										
					getCco($ccoOri, "D", $emp);
					if( !$pac['act'] && $ccoOri[ 'urg' ] ){
						$pac['act']=infoPacientePrima($pac,$emp);
					}
					$ccoOrdenes = ( irAOrdenes( $conex, $bd, $emp, $pac['sac'] ) == 'on' ) ? true: false;
					$pacOrdentes = pacienteKardexOrdenesDev( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ] );
					if( $pac['act'] && $ccoOri[ 'urg' ] && ( !$ccoOrdenes || !$pacOrdentes ) ){
						die( "<tr><td class='errorTitulo'>EL PACIENTE NO TIENE ORDENES</br></br>" );
					}

					$ccoUsu['urg'] = $ccoOri[ 'urg' ];
					
					if( $pac['act'] )
					{
						/************************************************************************************
						 * Marzo 17 de 2015
						 ************************************************************************************/
						 if( $ccoUsu[ 'urg' ] ){
							 echo "<center><span class='titulo1b' style='color:yellow'>&nbsp;&nbsp;SOLO SE PUEDEN DESCARTES&nbsp;&nbsp;</span></center>";
							 echo "<br>";
						}
						/************************************************************************************/

						/**
						 * Si el paciente ya tiene el alta es por que todos los registros estan en P.
						 * Entonces no se hace la actualización de registros.
						 */
						$pac['act']=false;
						if(!$pac['ald'] )
						{
							if(!$pac['ptr'])
							{
								if(!$pac['alp'] or ($pac['alp'] and isset($alp)))
								{
									if($pac['sac'] == $ccoUsu['cod'])
									{
										$pac['act']=true;
									}
									else
									{
										//El paciente no esta ubicado en el centro de costos que el usuario selecciono
										$sac['cod']=$pac['sac'];
										getCco($sac, "C", $emp);
										echo "<tr><td class='errorTitulo'>EL PACIENTE NO ESTA UBICADO EN EL CENTRO DE COSTOS SELECCIONADO</br></br>";
										echo "El paciente <i>".$pac['nom']."</i> con <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i> </BR>";
										echo " no se encuentra en el centro de costos <i>".$ccoUsu['cod']."-".$ccoUsu['nom']."</i>, </br> ";
										echo " si no en el <i>".$sac['cod']."-".$sac['nom']."</i>. </br> ";
										echo " Por este motivo no es posible hacerle la devolución</td></tr>";
									}
								}
								else
								{
									echo "<tr><td class='errorTitulo'>EL PACIENTE  <i>".$pac['nom']."</i> CON HISTORIA <i>".$pac['his']."</i> ESTA EN PROCESO DE ALTA</td></tr>";
								}
							}
							else
							{
								//revisamos cual es el servicio anterior del paciente y que ese sea desde el que se va ha devolver
								$q = "SELECT Ubisan, Ubihan "
								."      FROM ".$bd."_000018 "
								."     WHERE Ubihis = '".$pac['his']."' "
								."       AND Ubiing = '".$pac['ing']."' ";
								$err=mysql_query($q,$conex);
								echo mysql_error();
								$row=mysql_fetch_array($err);
								$pac['sac']=$row['Ubisan'];
								$pac['hac']=$row['Ubihan'];

								if($pac['sac'] == $ccoUsu['cod'])
								{
									$pac['act']=true;
								}
								else
								{
									//El paciente esta en proceso de traslado
									echo "<tr><td class='errorTitulo'>EL PACIENTE  <i>".$pac['nom']."</i> CON HISTORIA <i>".$pac['his']."</i> ESTA EN PROCESO DE TRASLADO.<BR> Mientras el paciente no sea recibido en el centro de costos de destino<br>no se pueden hacer devoluciones</td></tr>";
								}


							}
						}
						else
						{
							//El paciente esta de alta
							echo "<tr><td class='errorTitulo'>YA FUE DADA EL ALTA DEFINITIVA PARA EL PACIENTE <i><i>".$pac['nom']."</i></i> CON HISTORIA <i>".$pac['his']."</i> E INGRESO <i>".$pac['ing']."</i></td></tr>";
						}
					}
					else
					{
						//El paciente no esta activo
						echo "<tr><td class='errorTitulo'>EL PACIENTE CON HISTORIA ".$pac['his']." NO ESTA ACTIVO EN UNIX</td></tr>";
					}
				}
				else
				{
					echo "<tr><td class='errorTitulo'>NO DIGITO NINGUNA HISTORIA. VUELVA A INTENTARLO</td></tr>";
				}


				if($pac['act'])
				{
					$afin=clienteMagenta($pac['doc'],$pac['tid'],$pac['tip'], $pac['color']);
					echo "<td class='titulo1b' >".$pac['nom'];
					if($afin)
					{
						echo "<font color='#".$pac['color']."' size='12' face='arial'>".$pac['tip']."</font>";
					}
					echo "</td></tr>";
					echo "<td class='titulo1b' >Historia:".$pac['his']."&nbsp;&nbsp;&nbsp;Ingreso:".$pac['ing']."</td></tr>";

					echo "<form action='' name='devApl2' method='POST'>";
					if(!isset($array))
					{
						crearArray($pac,$cco,$array);
					}
					$total = count($array);
					crearArrayJus($jus);

					/**
					 * Se recorre el array para conocer su estado, es decir buscar errores en el
					 */

					if($total>0)
					{
						$procesar=true;
						$eligioAlMenosUno=false;//Mínimodebe haber elegido un artículo para devolver.
						//Ciclo que recorre los artículos
						for($i=0; $i<$total;$i++)
						{
							$countCco=count($array[$i]['cco']);
							//ciclo que recorre los centros de costos que han cargado los artículos de $i
							for($j=0; $j<$countCco; $j++)
							{
								//Verificar los datos en la fuente de aprovechamiento
								if(isset($array[$i]['cco'][$j]['fueA']['csa']))
								{
									$array[$i]['cco'][$j]['fueA']['class'] = "texto";
									$array[$i]['cco'][$j]['fueA']['msg']="";
									if(isset($array[$i]['cco'][$j]['fueA']['ckb']))
									{
										$carro=Carro($pac, $cco[$array[$i]['cco'][$j]['cod']]['cod'], 'A', $array[$i]['art']['cod']);
										//Selecciono el checkbox de la fuente de aprovechamiento del centro de costos $j del artículo $i
										if( !validacionBasica($array[$i]['cco'][$j]['fueA'], $array[$i]['art']['jdv'], $carro, $array[$i]['art']['cod'], $cco[$array[$i]['cco'][$j]['cod']]['cod'] ))
										{
											/*Hay algun error de validación sencilla*/
											$procesar=false;
											$array[$i]['cco'][$j]['fueA']['class'] = "alert";
											$array[$i]['cco'][$j]['fueA']['msg']=$array[$i]['cco'][$j]['fueA']['msg']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
										}
										$eligioAlMenosUno=true;
									}
								}
								//Verificar los datos en la fuente simple
								if(isset($array[$i]['cco'][$j]['fueS']['csa']))
								{
									$array[$i]['cco'][$j]['fueS']['class'] = "texto";
									$array[$i]['cco'][$j]['fueS']['msg']="";

									if(isset($array[$i]['cco'][$j]['fueS']['ckb']))
									{
										$carro=Carro($pac, $cco[$array[$i]['cco'][$j]['cod']]['cod'], 'P', $array[$i]['art']['cod']);
										//Selecciono el checkbox de la fuente simple del centro de costos $j del artículo $i
										if( !validacionBasica($array[$i]['cco'][$j]['fueS'], $array[$i]['art']['jdv'], $carro, $array[$i]['art']['cod'], $cco[$array[$i]['cco'][$j]['cod']]['cod'] ))
										{
											/*Hay algun error de validación sencilla*/
											$procesar=false;
											$array[$i]['cco'][$j]['fueS']['class'] = "alert";
											$array[$i]['cco'][$j]['fueS']['msg']=$array[$i]['cco'][$j]['fueS']['msg']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
										}
										$eligioAlMenosUno=true;
									}
								}
							}
						}
					}


					if($total>0)
					{
						if($eligioAlMenosUno and $procesar and isset($aceptar))
						{
							//Generar el número de la devolucion
							$okDevCons = devCons($devCons,$ccoUsu,$pac,$usuario);
							if($okDevCons)
							{
								echo "<tr><td class='tituloSup1'><a href='/matrix/".$bd."/procesos/Recibo_devoluciones.php?wnde=".$devCons."&wbasedato=movhos&wemp_pmla=01&wcco=x&reporte=1&historia=".$pac['his']."&ingreso=".$pac['ing']."' target='_blank' id='consecutivo'>Se ha realizado la devolución Número ".$devCons."</a></td><tr>";
							}
							else
							{
								echo "<tr><td class='errorTitulo'><IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>NO SE PUDO EFECTUAR LA DEVOLUCIÓN. INENTELO NUEVAMENTE</td><tr>";
							}
						}
						elseif(!$procesar)
						{
							echo "<tr><td class='errorTitulo'>Debe resolver un problema con alguno de los artìculos a devolver</td><tr>";
						}
						elseif(isset($aceptar) and !$eligioAlMenosUno)
						{
							echo "<tr><td class='errorTitulo'>Debe Elegir al menos un artículo para devolver</td><tr>";
							unset($aceptar);
							$aceptar2=1;
						}
						echo "<tr><td><center><table width='700' border='0'>";

						/**
                     * Ciclo para el total de los artículos
                     */
						for($i=0; $i<$total;$i++)
						{
							if($i%5 ==0)
							{
								echo "<tr><td class='titulo2'>Artículo</td>";
								echo "<td class='titulo2'>Cco</td>";
								echo "<td class='titulo2'></td>";
								echo "<td class='titulo2'>Fuente</td>";
								echo "<td class='titulo2'>Devolución</td>";
								echo "<td class='titulo2'>Just. Devolución</td>";
								echo "<td class='titulo2'>Faltante</td>";
								echo "<td class='titulo2'>Just. Faltante</td>";
								echo "<td class='titulo2'>Descarte</td>";
								echo "<td class='titulo2'>Destino Descarte</td>";
								//echo "<td class='titulo2'>Sobrante</td>";
								echo "</tr>";
							}

							echo "<tr><td class='".$array[$i]['art']['class']."' rowspan='".$array[$i]['art']['rsp']."' width='200'>".$array[$i]['art']['cod']." - ".$array[$i]['art']['nom']." (".$array[$i]['art']['tot']." ".$array[$i]['art']['uni'].")</td>";

							$countCco=count($array[$i]['cco']);

							$countJus=count($jus);

							/**
							 * Ciclo por cada centro de costos que ha grabado el artículo $i
							 */
							for($j=0; $j<$countCco; $j++)
							{
								$codCco=$array[$i]['cco'][$j]['cod'];

								//busco el color del centro de costos

								echo "<td class='".$cco[$codCco]['class']."' rowspan='".$array[$i]['cco'][$j]['rsp']."'  >".$cco[$codCco]['cod']."-".$cco[$codCco]['nom']."</td>";

								if(isset($array[$i]['cco'][$j]['fueA']['csa']))
								{
									//anitalavalatina echo "entro fueA csa<br>";
									if($procesar and isset($array[$i]['cco'][$j]['fueA']['ckb']) and $okDevCons)
									{
										//El usuario desea que se haga el proceso con los valores digitados.

										$array[$i]['cco'][$j]['fueA']['msg']="";


										/***********************************************************************************************
										* Devolución
										*/
										if( $array[$i]['cco'][$j]['fueA']['cdv']>0)
										{
											if( $array[$i]['art']['cmz'] or $cco[$codCco]['ima'])
											{
												/**
												 * Si el código del artículo fue encontrado en la central o el centro de costos
												 * tiene inventario por MATRIX entonces la devolución se realiza de otra forma.
												 */

												//La devolución de los artículos de la central solo sa hace por cantidades enteras
												if(( $array[$i]['cco'][$j]['fueA']['cdv']%1)==0)
												{ //echo "paso2222......";
													//La función de devolución solo permite de
													$k=0;
													$ok=true;
													$array[$i]['art']['can']=1;
													$contFaltante=0;
													do {
														$wbasdat="cenpro";
														$dronum="";
														$drolin="";
														$ok=devolucionCM($cco[$codCco],$array[$i]['art'],$pac,$error,$dronum,$drolin);

														if( $ok ){

															 devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
														}

														if($ok)
														{
															if($contFaltante >= $array[$i]['cco'][$j]['fueA']['cfa'])
															{
																$faltante=0;
																$jusF=" ";
															}
															else
															{
																$faltante=1;
																$jusF=$array[$i]['cco'][$j]['fueA']['jfa'];
																$contFaltante++;
															}
															$jusD=$array[$i]['cco'][$j]['fueA']['jde'];

															//Movimiento de devoluciones
															if(!registrarDevFact($devCons,$dronum,$drolin,1,$array[$i]['cco'][$j]['fueA']['jde'],$array[$i]['cco'][$j]['fueA']['cfa'],$array[$i]['cco'][$j]['fueA']['jfa'],$usuario,$error))
															{
																$error['ok']="Hubo un problema grabando<br>el Movimiento de devolucion a<br>".$cco[$codCco]['cod']."-".$cco[$codCco]['nom'];
																$ok=false;
															}
															else{
																$articulosDevueltos++;
																if( !$solicitudCamillero ){
																	if( esTraslado( $cco[$codCco]['cod'] ) && true ){
//																		peticionCamillero( 'on', '', '', $usuario, substr($ccoCod,3), $cco[$codCco]['cod'], '',$pac['his'], $pac['ing']  );
																		peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoUsu['cod'], $cco[$codCco]['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
																	}
																	$solicitudCamillero = true;
																}
															}
														}
														$k++;
													}while($k < $array[$i]['cco'][$j]['fueA']['cdv']  and $ok == true);
												}
												else
												{
													//Debe volver a intentar
													$ok=false;
													$array[$i]['cco'][$j]['fueA']['mod'] = true;
													$error['ok'] = "Este articulo solo permite devolver enteros</br>Si desea puede descartar la fracción restante.";
												}
											}
											else
											{
												$array[$i]['art']['can'] = $array[$i]['cco'][$j]['fueA']['cdv'];
												$ok=procesoDev($devCons, $pac,$array[$i]['art'],$array[$i]['cco'][$j]['fueA']['jde'],$array[$i]['cco'][$j]['fueA']['cfa'],$array[$i]['cco'][$j]['fueA']['jfa'],$cco[$codCco],"D",true,$usuario,$error);

												//Agosto 16 de 2011
												if( $ok ){
													 devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
												}

												if( $ok ){
													$articulosDevueltos++;
												}
											}

											if(!$ok)
											{
												//Hubo un error

												$array[$i]['cco'][$j]['fueA']['class'] = "error";
												$array[$i]['cco'][$j]['fueA']['msg'] = $error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueA']['msg']="LA DEVOLUCIÓN FUE REALIZADA CON EXITO.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
												$array[$i]['cco'][$j]['fueA']['class'] = "exito";
											}
										}


										/**
										 * Descarte
										 */
										if($array[$i]['cco'][$j]['fueA']['cds']>0)
										{
											/**
                                         * El decarte debe ser un movimiento en el saldo del artículo para el centro de costos y la fuente y
                                         * ademas debe ser un registro de ajuste de aplicación, es decir un registro en la tabla de aplicación donde Aplaap='on'
                       	                 */
											$array[$i]['art']['can']=$array[$i]['cco'][$j]['fueA']['cds'];
											if(validacionDevolucion($cco[$codCco], $pac, $array[$i]['art'],true, false, $error))
											{

												if(!registrarSaldosNoApl($pac,$array[$i]['art'],$cco[$codCco],true,$usuario,"D",false, $error))
												{
													//Hubo un error
													$array[$i]['cco'][$j]['fueA']['class'] = $error['color'];
													$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg'].$error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
												}
												else
												{
													if(registrarDescarte($devCons,$pac,$cco[$codCco],true,false,$array[$i]['art'],$array[$i]['cco'][$j]['fueA']['dds'],$usuario,$error))
													{
														$array[$i]['cco'][$j]['fueA']['msg']=$array[$i]['cco'][$j]['fueA']['msg']." SE REALIZO EL DESCARTE EXITOSAMENTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
														if($array[$i]['cco'][$j]['fueA']['class'] == 'texto')
														{
															$array[$i]['cco'][$j]['fueA']['class'] ="exito";
														}
														$articulosDevueltos++;
													}
													else
													{
														$array[$i]['cco'][$j]['fueA']['class'] = "error";
														$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg']."SE MODIFICO EL SALDO PERO NO SE HIZO LA APLICACIÓN DURANTE EL DESACARTE<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
													}
												}
											}
											else
											{
												$array[$i]['cco'][$j]['fueA']['class'] = "error";
												$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg']."ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN , O UN DESCARTE DE ESTE ARTÍCULO A ESTE PACIENTE MIENTRAS UD. HACIA ESTE DESCARTE.<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
										}
										/**
										 * Sobrante
										 */
										if($array[$i]['cco'][$j]['fueA']['sob']>0)
										{
											if(registrarSobranteDevolucion($devCons,$cco[$codCco],$array[$i]['art'],$array[$i]['cco'][$j]['fueA']['sob'],$usuario,$error))
											{
												$articulosDevueltos++;
												$array[$i]['cco'][$j]['fueA']['msg']=$array[$i]['cco'][$j]['fueA']['msg']." SE REGISTRO EL SOBRANTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueA']['class'] = "error";
												$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg']."NO SE REALIZO EL REGISTRO DEL SOBRANTE.<IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>";
											}
										}

										//Si hubo algun problema en grabación o modificación debe salir y volver  intentarlo.
										$array[$i]['cco'][$j]['fueA']['mod'] = false;

									}
									elseif ($procesar and isset($aceptar))
									{
										$array[$i]['cco'][$j]['fueA']['mod']=false;
									}

									$carro=Carro($pac, $cco[$codCco]['cod'], 'A', $array[$i]['art']['cod']);
									if(!isset($aceptar) and !isset($aceptar2))
									{
										$array[$i]['cco'][$j]['fueA']['cdv']=$array[$i]['cco'][$j]['fueA']['cdv']-$carro;
									}

									if($array[$i]['cco'][$j]['fueA']['mod'])
									{
										MostrarModificable($array[$i]['cco'][$j]['fueA'],$cco[$codCco]['fapC'],"Aprov.","array[".$i."][cco][".$j."][fueA]",$jus, $array[$i]['art']['jdv'], $carro, $ccoUsu );
										echo "</tr><tr>";
									}
									else
									{
										MostrarNoModificable($array[$i]['cco'][$j]['fueA'],$cco[$codCco]['fapC'],"Aprov.","array[".$i."][cco][".$j."][fueA]", $array[$i]['art']['jdv']);
										echo "</tr><tr>";
									}
								}//if Aprov

								/**
								 * Aqui empieza para la funte simple del centro de costos
								 */
								if(isset($array[$i]['cco'][$j]['fueS']['csa']))
								{
									//anitalavalatina echo "entro fue S csa<br>";
									if($procesar and isset($array[$i]['cco'][$j]['fueS']['ckb']) and $okDevCons)
									{
										//Quieren que se haga el proceso con los valores digitados.

										$array[$i]['cco'][$j]['fueS']['msg']="";
										/**
										 * Devolución
										 */
										if($array[$i]['cco'][$j]['fueS']['cdv']>0)
										{
											if( $array[$i]['art']['cmz'] or $cco[$codCco]['ima'])
											{
												/**
                                             * Si el código del artículo fue encontrado en la central o el centro de costos
                                             * tiene inventario por MATRIX entonces la devolución se realiza de otra forma.
                                             */

												//La devolución solo sa hace por cantidades enteras. En el if si el modulo es cero, es decir si la cantidad es entera entronces entrará por ahi.
												if(($array[$i]['cco'][$j]['fueS']['cdv']%1) == 0)
												{ //echo "paso33333......";
													//La función de devolución solo permite de
													$k=0;
													$ok=true;
													$array[$i]['art']['can'] =1;
													$contFaltante=0;
													do {
														$wbasdat="cenpro";
														$dronum="";
														$drolin="";
														$ok=devolucionCM($cco[$codCco],$array[$i]['art'],$pac,$error,$dronum,$drolin);

														if( $ok ){															
															devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
														}

														if($ok)
														{
															/**
                                                         * La cantidad de faltantes dificilmente será igual a la cantida a devolver, es por eso que en
                                                         */
															if($contFaltante >= $array[$i]['cco'][$j]['fueS']['cfa'])
															{
																$faltante=0;
																$jusF=" ";
															}
															else
															{
																$faltante=1;
																$jusF=$array[$i]['cco'][$j]['fueS']['jfa'];
																$contFaltante++;
															}
															//Movimiento de devoluciones
															$array[$i]['art']['can']=1;
//															echo "<b>antes</b><br>";
															if(!registrarDevFact($devCons,$dronum,$drolin,1,$array[$i]['cco'][$j]['fueS']['jde'],$array[$i]['cco'][$j]['fueS']['cfa'],$array[$i]['cco'][$j]['fueS']['jfa'],$usuario,$error))
															{
																$error['ok']="Hubo un problema grabando<br>el Movimiento de devolucion a<br>".$cco[$codCco]['cod']."-".$cco[$codCco]['nom'];
																$ok=false;
															}
															else{
																$articulosDevueltos++;

																if( !$solicitudCamillero ){
																	if( esTraslado( $cco[$codCco]['cod'] ) && true ){
																		peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoUsu['cod'], $cco[$codCco]['cod'], $pac['nom'], $pac['his'], $pac['ing']  );
																		$solicitudCamillero = true;
//																		peticionCamillero( 'on', '', '', $usuario, substr($ccoCod,3), $cco[$codCco]['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
																	}
																}
															}

//															echo "<b>despues</b><br>";
														}
														$k++;
													}while($k < $array[$i]['cco'][$j]['fueS']['cdv']  and $ok == true);
												}
												else
												{
													//Debe volver a intentar
													$ok=false;
													$array[$i]['cco'][$j]['fueS']['mod'] = true;
													$error['ok'] = "Este articulo solo permite devolver enteros</br>Si desea puede descartar la fracción restante.";
												}
											}
											else
											{
												$array[$i]['art']['can'] = $array[$i]['cco'][$j]['fueS']['cdv'];
												$ok=procesoDev($devCons, $pac,$array[$i]['art'],$array[$i]['cco'][$j]['fueS']['jde'],$array[$i]['cco'][$j]['fueS']['cfa'],$array[$i]['cco'][$j]['fueS']['jfa'],$cco[$codCco],"D",false,$usuario,$error);

												//Agosto 16 de 2011
												if( $ok ){
													// echo "222222.......<pre>"; var_dump( $pac ); echo "</pre> .....codCod $codCco";
													// echo "<br>aaaa.......".$cco[$codCco]['cod'];
													 devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
												}

												if( $ok ){
													$articulosDevueltos++;
												}

											}

											if(!$ok)
											{
												//Hubo un error
												$array[$i]['cco'][$j]['fueS']['class'] = "error";
												$array[$i]['cco'][$j]['fueS']['msg'] = $error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueS']['msg']="LA DEVOLUCIÓN FUE REALIZADA CON EXITO <br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
												$array[$i]['cco'][$j]['fueS']['class'] = "exito";
											}
										}

										/**
                                     	 * Descarte
                                      	 */
										if($array[$i]['cco'][$j]['fueS']['cds']>0)
										{
											/**
                                             * El decarte debe ser un movimiento en el saldo del artículo para el centro de costos y la fuente y
                                             * ademas debe ser un registro de ajuste de aplicación, es decir un registro en la tabla de aplicación donde Aplaap='on'
                                             */
											$array[$i]['art']['can']=$array[$i]['cco'][$j]['fueS']['cds'];
											if(validacionDevolucion($cco[$codCco],$pac,$array[$i]['art'],false,false,$error))
											{
												//No interesa la fuente
												if(!registrarSaldosNoApl($pac,$array[$i]['art'],$cco[$codCco],false,$usuario,"D",false,$error))
												{
													//Hubo un error
													$array[$i]['cco'][$j]['fueS']['class'] = $error['color'];
													$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg'].$error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
												}
												else
												{
													if(registrarDescarte($devCons,$pac,$cco[$codCco],false,false,$array[$i]['art'],$array[$i]['cco'][$j]['fueS']['dds'],$usuario,$error))
													{
														$array[$i]['cco'][$j]['fueS']['msg']=$array[$i]['cco'][$j]['fueS']['msg']." SE REALIZO EL DESCARTE EXITOSAMENTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";

														if($array[$i]['cco'][$j]['fueS']['class'] == 'texto')
														{
															$array[$i]['cco'][$j]['fueS']['class'] ="exito";
														}
														$articulosDevueltos++;
													}
													else
													{
														$array[$i]['cco'][$j]['fueS']['class'] = "error";
														$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."SE MODIFICO EL SALDO PERO NO SE HIZO LA APLICACIÓN DURANTE EL DESACARTE<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
													}
												}
											}
											else
											{
												$array[$i]['cco'][$j]['fueS']['class'] = "error";
												$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN , O UN DESCARTE DE ESTE ARTÍCULO A ESTE PACIENTE MIENTRAS UD. HACIA ESTE DESCARTE.<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
										}

										//Sobrante
										if($array[$i]['cco'][$j]['fueS']['sob']>0)
										{
											if(registrarSobranteDevolucion($devCons,$cco[$codCco],$array[$i]['art'],$array[$i]['cco'][$j]['fueS']['sob'],$usuario,$error))
											{
												$articulosDevueltos++;
												$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."SE REALIZO EL REGISTRO DEL SOBRANTE EXITOSAMENTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueS']['class'] = "error";
												$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."NO SE REALIZO EL REGISTRO DEL SOBRANTE<IMG SRC='/matrix/images/medical/root/Malo.ico'>.<BR>";
											}
										}
										//Si hubo algun problema en grabación o modificación debe salir y volver  intentarlo.
										$array[$i]['cco'][$j]['fueS']['mod'] = false;

									}//Fin if Simple
									elseif ($procesar and isset($aceptar))
									{
										$array[$i]['cco'][$j]['fueS']['mod']=false;
									}

									$carro=Carro($pac, $cco[$codCco]['cod'], 'P', $array[$i]['art']['cod']);
									if(!isset($aceptar) and !isset($aceptar2))
									{
										$array[$i]['cco'][$j]['fueS']['cdv']=$array[$i]['cco'][$j]['fueS']['cdv']-$carro;
									}

									if($array[$i]['cco'][$j]['fueS']['mod'])
									{
										MostrarModificable($array[$i]['cco'][$j]['fueS'],$cco[$codCco]['fueC'],"Simple","array[".$i."][cco][".$j."][fueS]",$jus,$array[$i]['art']['jdv'], $carro, $ccoUsu );
										echo "</tr><tr>";
									}
									else
									{
										MostrarNoModificable($array[$i]['cco'][$j]['fueS'],$cco[$codCco]['fueC'],"Simple","array[".$i."][cco][".$j."][fueS]", $array[$i]['art']['jdv']);
										echo "</tr><tr>";
									}
								}

								//Zona de hidden para las cosas propias del artículo
								echo "<input type='hidden' name='array[".$i."][cco][".$j."][sid]' value='".$array[$i]['cco'][$j]['sid']."'>";
								echo "<input type='hidden' name='array[".$i."][cco][".$j."][rsp]' value='".$array[$i]['cco'][$j]['rsp']."'>";
								echo "<input type='hidden' name='array[".$i."][cco][".$j."][cod]' value='".$codCco."'>";
							}//fin del for de los centros de costos para el artículo $i

							//Hidden de la información del artículo
							echo "<input type='hidden' name ='array[".$i."][art][cod]' value='".$array[$i]['art']['cod']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][nom]' value='".$array[$i]['art']['nom']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][tot]' value='".$array[$i]['art']['tot']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][rsp]' value='".$array[$i]['art']['rsp']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][neg]' value=''>";

							if($array[$i]['art']['act'])
							{
								echo "<input type='hidden' name ='array[".$i."][art][act]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name ='array[".$i."][art][act]' value=''>";
							}
							if($array[$i]['art']['jdv'])
							{
								echo "<input type='hidden' name ='array[".$i."][art][jdv]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name ='array[".$i."][art][jdv]' value=''>";
							}

							if($array[$i]['art']['cmz'])
							{
								echo "<input type='hidden' name ='array[".$i."][art][cmz]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name ='array[".$i."][art][cmz]' value=''>";
							}
							echo "<input type='hidden' name ='array[".$i."][art][class]' value='".$array[$i]['art']['class']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][uni]' value='".$array[$i]['art']['uni']."'>";
							/*
							echo "<input type='hidden' name ='array[".$i."][art][]' value='".$array[$i]['art']['']."'>";
							*/
						}//fin del for de todos los artículos

						//Si $articulosDevueltos es igual a 0, indica que no se realizo ni una devolucion, ni un descarte o ningun falante
						if( $articulosDevueltos == 0 && !empty($devCons) ){
							eliminarMovimientoDeDevolucion( $devCons );
							descontarConsecutivoDevolucion();
							?>
							<script>document.getElementById( "consecutivo" ).style.display = 'none';</script>;
							<?php
						}

						//Información Básica del paciente y otros
						echo "<input type='hidden' name ='bd' value='".$bd."'>";
						echo "<input type='hidden' name ='emp' value='".$emp."'>";

						echo "<input type='hidden' name ='historia' value='".$pac['his']."'>";
						echo "<input type='hidden' name ='ing' value='".$pac['ing']."'>";
						echo "<input type='hidden' name ='pac[his]' value='".$pac['his']."'>";
						echo "<input type='hidden' name ='pac[ing]' value='".$pac['ing']."'>";
						echo "<input type='hidden' name ='pac[nom]' value='".$pac['nom']."'>";
						echo "<input type='hidden' name ='pac[doc]' value='".$pac['doc']."'>";
						echo "<input type='hidden' name ='pac[tid]' value='".$pac['tid']."'>";
						echo "<input type='hidden' name ='pac[dxv]' value='".$pac['dxv']."'>";
						echo "<input type='hidden' name ='pac[act]' value='1'>";

						echo "<input type='hidden' name='ccoUsu[cod]' value='".$ccoUsu['cod']."'>";
						echo "<input type='hidden' name='ccoUsu[nom]' value='".$ccoUsu['nom']."'>";

						if(isset($alp))
						{
							echo "<input type='hidden' name ='alp' value='1'>";
						}

						/*
						echo "<input type='hidden' name ='array[".$i."]' value='".$array[$i] ."'>";
						*/

						//Información de los centros de costos
						$countCco=count($cco);
						for($i=0;$i<$countCco;$i++)
						{

							echo "<input type='hidden' name='cco[".$i."][cod]' value='".$cco[$i]['cod']."'>";
							echo "<input type='hidden' name='cco[".$i."][nom]' value='".$cco[$i]['nom']."'>";
							echo "<input type='hidden' name='cco[".$i."][fap]' value='".$cco[$i]['fap']."'>";
							echo "<input type='hidden' name='cco[".$i."][fue]' value='".$cco[$i]['fue']."'>";
							echo "<input type='hidden' name='cco[".$i."][fapC]' value='".$cco[$i]['fapC']."'>";
							echo "<input type='hidden' name='cco[".$i."][fueC]' value='".$cco[$i]['fueC']."'>";

							if($cco[$i]['act'])
							{
								echo "<input type='hidden' name='cco[".$i."][act]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name='cco[".$i."][act]' value=''>";
							}

							if($cco[$i]['apl'])
							{
								echo "<input type='hidden' name='cco[".$i."][apl]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name='cco[".$i."][apl]' value=''>";
							}

							if($cco[$i]['ima'])
							{
								echo "<input type='hidden' name='cco[".$i."][ima]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name='cco[".$i."][ima]' value=''>";
							}


							echo "<input type='hidden' name='cco[".$i."][class]' value='".$cco[$i]['class']."'>";

						}
						echo "</table></td></tr>";

						if( $seHizoSolicitudCamillero ){
							echo "<tr><td><table align='center'>";
							echo "<tr>";
							echo "<td>";
							echo "<B>SE HA SOLICITADO CAMILLERO</B>";
							echo "</td>";
							echo "</tr>";
							echo "</table></td></tr>";
						}

						if(!isset($aceptar) or  !$procesar)
						{
							echo "<tr><td align='center' ><br><br><input type='submit' name='aceptar' value='ACEPTAR'></TD></tr>";
							// echo "<tr><td align='center' ><br><br><input type='submit' name='aceptar' value='ACEPTAR' onClick='ejecutarSubmit( this )'></TD></tr>";
						}
						echo "</form>";
						echo "<tr><td align='center'>&nbsp;</td></td>";
						echo "<tr><td align='center'><A HREF='devoluciones.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'>Retornar</a>&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<A HREF='devoluciones.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ccoCod=UN.".$ccoUsu['cod']."'>Retornar con CC</a></font></td></tr>";
					}
					else
					{
						echo "<tr><td class='tituloSup'>ESTE PACIENTE NO TIENE ARTÍCULOS SUCEPTIBLES DE SER DEVUELTOS</td></tr>";
					}
				}
			}
		}
		else
		{
			/****************************************************************************************
			 * Sin conexion con Unix
			 ****************************************************************************************/
			//echo "<center><b>SIN CONEXION CON UNIX</b></center>";
			if(!isset($historia))
			{
				echo "<center><table border='0' width='300' align='center'>";
				echo "<form name='devApl' action='' method='POST'> ";
				if(!isset($ccoCod))
				{
					//Busqueda del centro de costos al que pertenece el usuario.

					$q="SELECT Cc "
					."FROM  root_000025 "
					."WHERE Empleado = '".$usuario."' ";
					$err = mysql_query($q,$conex);
					$num = mysql_num_rows($err);
					if($num >0)
					{
						$row=mysql_fetch_array($err);
						$cco['cod']=$row['Cc'];
						$ok=getCco($cco,"D",$emp);
					}
					else
					{
						/*No esta el usuario en Matrix*/
						$pac['his']='0';
						$art['cod']="NO APLICA";
						$error['codInt']="0002";
						$cco['cod']='0000';
						if($err == "")
						{
							$error['codSis']=mysql_errno($conex);
							$error['descSis']=str_replace("'","*",mysql_error($conex));
						}
						else
						{
							$error['codSis']=$err;
							$error['descSis']=$err;
						}

						//registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
						registrarError('NO INFO',$cco,'NO INFO','0', '0',$pac,$art,$error, $color,$warning,$usuario);
						$printError="<CENTER>EL CODIGO DE USUARIO NO EXISTE";
						$ok=false;
					}
				}
				else
				{
					//Determinar que el centro de costos haya sido leido.
					$pos=strpos(strtoupper($ccoCod),"UN.");
					if($pos === 0)//Tiene que ser triple igual por que si no no funciona
					{
						$cco['cod']=substr($ccoCod,3);
						if(!getCco($cco,"D",$emp))
						{
							$printError="EL CENTRO DE COSTOS NO EXISTE O NO ESTA HABILITADO PARA REALIZAR CARGOS";
							$ok=false;
						}
						else
						{
							$cco['sel']=false;
							$ok=true;
						}
					}
					else
					{
						$printError="EL CENTRO DE COSTOS NO FUE LEIDO POR EL CODIGO DE BARRAS ADECUADO";
						$ok=false;
					}
				}
				
				/************************************************************************************************
				 * Diciembre 11 de 2014
				 ************************************************************************************************/
				if( $cco[ 'urg' ] ){
					$cco['hos'] = true;
					$cco['apl'] = false;
				}
				/************************************************************************************************/
				
				if($ok)
				{

					if($cco['sel'])
					{
						echo "<tr><td class='titulo1'>USUARIO: ".$usuario;
						echo "<tr><td class='titulo1' ><b>CC: ";
                                ?>      <input type='password' size='7' name='ccoCod' onload=''>
                                <script language="JAVASCRIPT" type="text/javascript">
                                document.devApl.ccoCod.focus();
                                </script>
                                <?php
                                echo "</td></tr>";
                                echo "<input type='hidden' name='ccoUsu[cod]' value='".$cco['cod']."'>";
                                echo "<input type='hidden' name='ccoUsu[nom]' value='".$cco['nom']."'>";
                                echo"<tr><td  class='titulo2'><input type='submit' value='ACEPTAR'></td></tr></form>";
					}
					else
					{
						if($cco['hos'] and !$cco['apl'])
						{
							echo "<tr><td class='tituloSup'>".$cco['cod']."-".$cco['nom']."</td></tr>";
							echo "<tr><td class='tituloSup'>USUARIO: ".$usuario."</b>";
							echo "<tr>";
							echo "<td class='titulo2' ><b>N° HISTORIA: ";
                                ?>
                                <input type='text' size='9' name='historia'>
                                <script language="JAVASCRIPT" type="text/javascript">
                                document.devApl.historia.focus();
                                </script>
                                <?php
                                echo "</td></tr>";
                                echo "<td class='titulo2' ><b>Alta en Proceso: <input type='checkbox' name='alp'></td></tr>";
                                echo "<input type='hidden' name='ccoUsu[cod]' value='".$cco['cod']."'>";
                                echo "<input type='hidden' name='ccoUsu[nom]' value='".$cco['nom']."'>";
                                echo"<tr><td  class='titulo2'><input type='submit' value='ACEPTAR'></td></tr></form>";
						}
						else
						{
							$printError="EL CENTRO DE COSTOS NO ES HOSPITALARIO O ES DE APLICACICÓN AUTOMÁTICA<br>"
							."Este programa solo puede ser desde centros de costos hospitalarios que no apliquen automáticamente.";
							$ok=false;
						}
					}
					echo "<input type='hidden' name ='usuario' value='".$usuario."' >";
				}

				if(!$ok)
				{
					echo "<tr><td class='errorTitulo'>".$printError;
					echo "</td></tr></table>";
				}
			}
			else
			{
				if($historia != "")
				{
					$pac['his']=ltrim(rtrim($historia));

					//Calidación por Matrix

					if($pac['his'] == 0)
					{
						$pac['dxv']=true;
					}
					else
					{
						$pac['dxv']=false;
					}

					//Valida que esté activo en UNIX
					//$conex_f = odbc_connect('facturacion','','');

					//$pac['act'] = ValidacionHistoriaUnix(&$pac, &$warning, &$error);
					$pac['act'] = HistoriaMatrix($cco, $pac, $warning, $error);
					//odbc_close($conex_f);

					if( $pac['act'] )
					{
						/************************************************************
						 * Enero 8 de 2013
						 ************************************************************/
						if( true || !isset($pac['nom']))
						{
							$pac['act']=infoPaciente($pac,$emp);

							$pac['dxv']=false;
							if(!isset($pac['nom']))
							{
								$pac['nom']="NO ENCONTRADO (".$pac['his'].")";
							}
						}
						/************************************************************/
						
						$ccoOri = $ccoUsu;
						getCco($ccoOri, "D", $emp);
						$ccoOrdenes = ( irAOrdenes( $conex, $bd, $emp, $pac['sac'] ) == 'on' ) ? true: false;
						$pacOrdentes = pacienteKardexOrdenes( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ], date( "Y-m-d" ) );
						if( $pac['act'] && $ccoOri[ 'urg' ] && ( !$ccoOrdenes || !$pacOrdentes ) ){
							die( "<tr><td class='errorTitulo'>EL PACIENTE NO TIENE ORDENES</br></br>" );
						}

						$ccoUsu['urg'] = $ccoOri[ 'urg' ];
						
						/**
						 * Si el paciente ya tiene el alta es por que todos los registros estan en P.
						 * Entonces no se hace la actualización de registros.
						 */
						$pac['act']=false;
						if(!$pac['ald'] )
						{
							if(!$pac['ptr'])
							{
								if(!$pac['alp'] or ($pac['alp'] and isset($alp)))
								{
									if($pac['sac'] == $ccoUsu['cod'])
									{
										$pac['act']=true;
									}
									else
									{
										//El paciente no esta ubicado en el centro de costos que el usuario selecciono
										$sac['cod']=$pac['sac'];
										getCco($sac, "C", $emp);
										echo "<tr><td class='errorTitulo'>EL PACIENTE NO ESTA UBICADO EN EL CENTRO DE COSTOS SELECCIONADO</br></br>";
										echo "El paciente <i>".$pac['nom']."</i> con <i>".$pac['his']."</i> e ingreso <i>".$pac['ing']."</i> </BR>";
										echo " no se encuentra en el centro de costos <i>".$ccoUsu['cod']."-".$ccoUsu['nom']."</i>, </br> ";
										echo " si no en el <i>".$sac['cod']."-".$sac['nom']."</i>. </br> ";
										echo " Por este motivo no es posible hacerle la devolución</td></tr>";
									}
								}
								else
								{
									echo "<tr><td class='errorTitulo'>EL PACIENTE  <i>".$pac['nom']."</i> CON HISTORIA <i>".$pac['his']."</i> ESTA EN PROCESO DE ALTA</td></tr>";
								}
							}
							else
							{
								//revisamos cual es el servicio anterior del paciente y que ese sea desde el que se va ha devolver
								$q = "SELECT Ubisan, Ubihan "
								."      FROM ".$bd."_000018 "
								."     WHERE Ubihis = '".$pac['his']."' "
								."       AND Ubiing = '".$pac['ing']."' ";
								$err=mysql_query($q,$conex);
								echo mysql_error();
								$row=mysql_fetch_array($err);
								$pac['sac']=$row['Ubisan'];
								$pac['hac']=$row['Ubihan'];

								if($pac['sac'] == $ccoUsu['cod'])
								{
									$pac['act']=true;
								}
								else
								{
									//El paciente esta en proceso de traslado
									echo "<tr><td class='errorTitulo'>EL PACIENTE  <i>".$pac['nom']."</i> CON HISTORIA <i>".$pac['his']."</i> ESTA EN PROCESO DE TRASLADO.<BR> Mientras el paciente no sea recibido en el centro de costos de destino<br>no se pueden hacer devoluciones</td></tr>";
								}


							}
						}
						else
						{
							//El paciente esta de alta
							echo "<tr><td class='errorTitulo'>YA FUE DADA EL ALTA DEFINITIVA PARA EL PACIENTE <i><i>".$pac['nom']."</i></i> CON HISTORIA <i>".$pac['his']."</i> E INGRESO <i>".$pac['ing']."</i></td></tr>";
						}
					}
					else
					{
						//El paciente no esta activo
						echo "<tr><td class='errorTitulo'>EL PACIENTE CON HISTORIA ".$pac['his']." NO ESTA ACTIVO EN UNIX</td></tr>";
					}
				}
				else
				{
					echo "<tr><td class='errorTitulo'>NO DIGITO NINGUNA HISTORIA. VUELVA A INTENTARLO</td></tr>";
				}

				if($pac['act'])
				{
					/************************************************************************************
					 * Marzo 17 de 2015
					 ************************************************************************************/
					 if( $ccoUsu[ 'urg' ] ){
						 echo "<center><span class='titulo1b' style='color:yellow'>&nbsp;&nbsp;SOLO SE PUEDEN DESCARTES&nbsp;&nbsp;</span></center>";
						 echo "<br>";
					}
					/************************************************************************************/
				
					$afin=clienteMagenta($pac['doc'],$pac['tid'],$pac['tip'], $pac['color']);
					echo "<td class='titulo1b' >".$pac['nom'];
					if($afin)
					{
						echo "<font color='#".$pac['color']."' size='12' face='arial'>".$pac['tip']."</font>";
					}
					echo "</td></tr>";
					echo "<td class='titulo1b' >Historia:".$pac['his']."&nbsp;&nbsp;&nbsp;Ingreso:".$pac['ing']."</td></tr>";

					echo "<form action='' name='devApl2' method='POST'>";
					if(!isset($array))
					{
						crearArray($pac,$cco,$array);
					}
					$total = count($array);
					crearArrayJus($jus);

					/**
					 * Se recorre el array para conocer su estado, es decir buscar errores en el
					 */

					if($total>0)
					{
						$procesar=true;
						$eligioAlMenosUno=false;//Mínimodebe haber elegido un artículo para devolver.
						//Ciclo que recorre los artículos
						for($i=0; $i<$total;$i++)
						{
							$countCco=count($array[$i]['cco']);
							//ciclo que recorre los centros de costos que han cargado los artículos de $i
							for($j=0; $j<$countCco; $j++)
							{
								//Verificar los datos en la fuente de aprovechamiento
								if(isset($array[$i]['cco'][$j]['fueA']['csa']))
								{
									$array[$i]['cco'][$j]['fueA']['class'] = "texto";
									$array[$i]['cco'][$j]['fueA']['msg']="";
									if(isset($array[$i]['cco'][$j]['fueA']['ckb']))
									{
										$carro=Carro($pac, $cco[$array[$i]['cco'][$j]['cod']]['cod'], 'A', $array[$i]['art']['cod']);
										//Selecciono el checkbox de la fuente de aprovechamiento del centro de costos $j del artículo $i
										if( !validacionBasica($array[$i]['cco'][$j]['fueA'], $array[$i]['art']['jdv'], $carro, $array[$i]['art']['cod'], $cco[$array[$i]['cco'][$j]['cod']]['cod'] ))
										{
											/*Hay algun error de validación sencilla*/
											$procesar=false;
											$array[$i]['cco'][$j]['fueA']['class'] = "alert";
											$array[$i]['cco'][$j]['fueA']['msg']=$array[$i]['cco'][$j]['fueA']['msg']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
										}
										$eligioAlMenosUno=true;
									}
								}
								//Verificar los datos en la fuente simple
								if(isset($array[$i]['cco'][$j]['fueS']['csa']))
								{
									$array[$i]['cco'][$j]['fueS']['class'] = "texto";
									$array[$i]['cco'][$j]['fueS']['msg']="";

									if(isset($array[$i]['cco'][$j]['fueS']['ckb']))
									{
										$carro=Carro($pac, $cco[$array[$i]['cco'][$j]['cod']]['cod'], 'P', $array[$i]['art']['cod']);
										//Selecciono el checkbox de la fuente simple del centro de costos $j del artículo $i
										if( !validacionBasica($array[$i]['cco'][$j]['fueS'], $array[$i]['art']['jdv'], $carro, $array[$i]['art']['cod'], $cco[$array[$i]['cco'][$j]['cod']]['cod'] ))
										{
											/*Hay algun error de validación sencilla*/
											$procesar=false;
											$array[$i]['cco'][$j]['fueS']['class'] = "alert";
											$array[$i]['cco'][$j]['fueS']['msg']=$array[$i]['cco'][$j]['fueS']['msg']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
										}
										$eligioAlMenosUno=true;
									}
								}
							}
						}
					}


					if($total>0)
					{
						if($eligioAlMenosUno and $procesar and isset($aceptar))
						{
							//Generar el número de la devolucion
							$okDevCons=devCons($devCons,$ccoUsu,$pac,$usuario);
							if($okDevCons)
							{
								echo "<tr><td class='tituloSup1'><a href='/matrix/".$bd."/procesos/Recibo_devoluciones.php?wnde=".$devCons."&wbasedato=movhos&wemp_pmla=01&wcco=x&reporte=1&historia=".$pac['his']."&ingreso=".$pac['ing']."' target='_blank' id='consecutivo'>Se ha realizado la devolución Número ".$devCons."</a></td><tr>";
							}
							else
							{
								echo "<tr><td class='errorTitulo'><IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>NO SE PUDO EFECTUAR LA DEVOLUCIÓN. INENTELO NUEVAMENTE</td><tr>";
							}
						}
						elseif(!$procesar)
						{
							echo "<tr><td class='errorTitulo'>Debe resolver un problema con alguno de los artìculos a devolver</td><tr>";
						}
						elseif(isset($aceptar) and !$eligioAlMenosUno)
						{
							echo "<tr><td class='errorTitulo'>Debe Elegir al menos un artículo para devolver</td><tr>";
							unset($aceptar);
							$aceptar2=1;
						}
						echo "<tr><td><center><table width='700' border='0'>";

						/**
						 * Ciclo para el total de los artículos
						 */
						for($i=0; $i<$total;$i++)
						{
							if($i%5 ==0)
							{
								echo "<tr><td class='titulo2'>Artículo</td>";
								echo "<td class='titulo2'>Cco</td>";
								echo "<td class='titulo2'></td>";
								echo "<td class='titulo2'>Fuente</td>";
								echo "<td class='titulo2'>Devolución</td>";
								echo "<td class='titulo2'>Just. Devolución</td>";
								echo "<td class='titulo2'>Faltante</td>";
								echo "<td class='titulo2'>Just. Faltante</td>";
								echo "<td class='titulo2'>Descarte</td>";
								echo "<td class='titulo2'>Destino Descarte</td>";
								//echo "<td class='titulo2'>Sobrante</td>";
								echo "</tr>";
							}

							echo "<tr><td class='".$array[$i]['art']['class']."' rowspan='".$array[$i]['art']['rsp']."' width='200'>".$array[$i]['art']['cod']." - ".$array[$i]['art']['nom']." (".$array[$i]['art']['tot']." ".$array[$i]['art']['uni'].")</td>";

							$countCco=count($array[$i]['cco']);

							$countJus=count($jus);

							/**
							 * Ciclo por cada centro de costos que ha grabado el artículo $i
							 */
							for($j=0; $j<$countCco; $j++)
							{
								$codCco=$array[$i]['cco'][$j]['cod'];

								//busco el color del centro de costos

								echo "<td class='".$cco[$codCco]['class']."' rowspan='".$array[$i]['cco'][$j]['rsp']."'  >".$cco[$codCco]['cod']."-".$cco[$codCco]['nom']."</td>";

								if(isset($array[$i]['cco'][$j]['fueA']['csa']))
								{
									//anitalavalatina echo "entro fueA csa<br>";
									if($procesar and isset($array[$i]['cco'][$j]['fueA']['ckb']) and $okDevCons)
									{
										//El usuario desea que se haga el proceso con los valores digitados.

										$array[$i]['cco'][$j]['fueA']['msg']="";


										/***********************************************************************************************
										* Devolución
										*/
										if( $array[$i]['cco'][$j]['fueA']['cdv']>0)
										{
											if( $array[$i]['art']['cmz'] or $cco[$codCco]['ima'])
											{
												/**
												 * Si el código del artículo fue encontrado en la central o el centro de costos
												 * tiene inventario por MATRIX entonces la devolución se realiza de otra forma.
												 */

												//La devolución de los artículos de la central solo sa hace por cantidades enteras
												if(( $array[$i]['cco'][$j]['fueA']['cdv']%1)==0)
												{
													//La función de devolución solo permite de
													$k=0;
													$ok=true;
													$array[$i]['art']['can']=1;
													$contFaltante=0;
													do {
														$wbasdat="cenpro";
														$dronum="";
														$drolin="";
														$ok=devolucionCM($cco[$codCco],$array[$i]['art'],$pac,$error,$dronum,$drolin);

														if( $ok ){
															// echo "11111111111CM.......<pre>"; var_dump( $pac ); echo "</pre>";
															// echo "<br>aaaa.......".$cco[$codCco];
																																	//traslado
															 devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
														}

														if($ok)
														{
															if($contFaltante >= $array[$i]['cco'][$j]['fueA']['cfa'])
															{
																$faltante=0;
																$jusF=" ";
															}
															else
															{
																$faltante=1;
																$jusF=$array[$i]['cco'][$j]['fueA']['jfa'];
																$contFaltante++;
															}
															$jusD=$array[$i]['cco'][$j]['fueA']['jde'];

															//Movimiento de devoluciones
															if(!registrarDevFact($devCons,$dronum,$drolin,1,$array[$i]['cco'][$j]['fueA']['jde'],$array[$i]['cco'][$j]['fueA']['cfa'],$array[$i]['cco'][$j]['fueA']['jfa'],$usuario,$error))
															{
																$error['ok']="Hubo un problema grabando<br>el Movimiento de devolucion a<br>".$cco[$codCco]['cod']."-".$cco[$codCco]['nom'];
																$ok=false;
															}
															else{
																$articulosDevueltos++;
																if( !$solicitudCamillero ){
																	if( esTraslado( $cco[$codCco]['cod'] ) && true ){
//																		peticionCamillero( 'on', '', '', $usuario, substr($ccoCod,3), $cco[$codCco]['cod'], '',$pac['his'], $pac['ing']  );
																		peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoUsu['cod'], $cco[$codCco]['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
																	}
																	$solicitudCamillero = true;
																}
															}
														}
														$k++;
													}while($k < $array[$i]['cco'][$j]['fueA']['cdv']  and $ok == true);
												}
												else
												{
													//Debe volver a intentar
													$ok=false;
													$array[$i]['cco'][$j]['fueA']['mod'] = true;
													$error['ok'] = "Este articulo solo permite devolver enteros</br>Si desea puede descartar la fracción restante.";
												}
											}
											else
											{
												$array[$i]['art']['can'] = $array[$i]['cco'][$j]['fueA']['cdv'];
												$ok=procesoDevMatrix($devCons, $pac,$array[$i]['art'],$array[$i]['cco'][$j]['fueA']['jde'],$array[$i]['cco'][$j]['fueA']['cfa'],$array[$i]['cco'][$j]['fueA']['jfa'],$cco[$codCco],"D",true,$usuario,$error);

												//Agosto 16 de 2011
												if( $ok ){
													 devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
												}

												if( $ok ){
													$articulosDevueltos++;
												}
											}

											if(!$ok)
											{
												//Hubo un error

												$array[$i]['cco'][$j]['fueA']['class'] = "error";
												$array[$i]['cco'][$j]['fueA']['msg'] = $error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueA']['msg']="LA DEVOLUCIÓN FUE REALIZADA CON EXITO.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
												$array[$i]['cco'][$j]['fueA']['class'] = "exito";
											}
										}


										/**
										 * Descarte
										 */
										if($array[$i]['cco'][$j]['fueA']['cds']>0)
										{
											/**
											 * El decarte debe ser un movimiento en el saldo del artículo para el centro de costos y la fuente y
											 * ademas debe ser un registro de ajuste de aplicación, es decir un registro en la tabla de aplicación donde Aplaap='on'
											 */
											$array[$i]['art']['can']=$array[$i]['cco'][$j]['fueA']['cds'];
											if(validacionDevolucion($cco[$codCco], $pac, $array[$i]['art'],true, false, $error))
											{

												if(!registrarSaldosNoApl($pac,$array[$i]['art'],$cco[$codCco],true,$usuario,"D",false,$error))
												{
													//Hubo un error
													$array[$i]['cco'][$j]['fueA']['class'] = $error['color'];
													$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg'].$error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
												}
												else
												{
													if(registrarDescarte($devCons,$pac,$cco[$codCco],true,false,$array[$i]['art'],$array[$i]['cco'][$j]['fueA']['dds'],$usuario,$error))
													{
														$array[$i]['cco'][$j]['fueA']['msg']=$array[$i]['cco'][$j]['fueA']['msg']." SE REALIZO EL DESCARTE EXITOSAMENTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
														if($array[$i]['cco'][$j]['fueA']['class'] == 'texto')
														{
															$array[$i]['cco'][$j]['fueA']['class'] ="exito";
														}
														$articulosDevueltos++;
													}
													else
													{
														$array[$i]['cco'][$j]['fueA']['class'] = "error";
														$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg']."SE MODIFICO EL SALDO PERO NO SE HIZO LA APLICACIÓN DURANTE EL DESACARTE<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
													}
												}
											}
											else
											{
												$array[$i]['cco'][$j]['fueA']['class'] = "error";
												$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg']."ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN , O UN DESCARTE DE ESTE ARTÍCULO A ESTE PACIENTE MIENTRAS UD. HACIA ESTE DESCARTE.<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
										}
										/**
										 * Sobrante
										 */
										if($array[$i]['cco'][$j]['fueA']['sob']>0)
										{
											if(registrarSobranteDevolucion($devCons,$cco[$codCco],$array[$i]['art'],$array[$i]['cco'][$j]['fueA']['sob'],$usuario,$error))
											{
												$articulosDevueltos++;
												$array[$i]['cco'][$j]['fueA']['msg']=$array[$i]['cco'][$j]['fueA']['msg']." SE REGISTRO EL SOBRANTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueA']['class'] = "error";
												$array[$i]['cco'][$j]['fueA']['msg'] = $array[$i]['cco'][$j]['fueA']['msg']."NO SE REALIZO EL REGISTRO DEL SOBRANTE.<IMG SRC='/matrix/images/medical/root/Malo.ico'><BR>";
											}
										}

										//Si hubo algun problema en grabación o modificación debe salir y volver  intentarlo.
										$array[$i]['cco'][$j]['fueA']['mod'] = false;

									}
									elseif ($procesar and isset($aceptar))
									{
										$array[$i]['cco'][$j]['fueA']['mod']=false;
									}

									$carro=Carro($pac, $cco[$codCco]['cod'], 'A', $array[$i]['art']['cod']);
									if(!isset($aceptar) and !isset($aceptar2))
									{
										$array[$i]['cco'][$j]['fueA']['cdv']=$array[$i]['cco'][$j]['fueA']['cdv']-$carro;
									}

									if($array[$i]['cco'][$j]['fueA']['mod'])
									{
										MostrarModificable($array[$i]['cco'][$j]['fueA'],$cco[$codCco]['fapC'],"Aprov.","array[".$i."][cco][".$j."][fueA]",$jus, $array[$i]['art']['jdv'], $carro, $ccoUsu );
										echo "</tr><tr>";
									}
									else
									{
										MostrarNoModificable($array[$i]['cco'][$j]['fueA'],$cco[$codCco]['fapC'],"Aprov.","array[".$i."][cco][".$j."][fueA]", $array[$i]['art']['jdv']);
										echo "</tr><tr>";
									}
								}//if Aprov

								/**
								 * Aqui empieza para la funte simple del centro de costos
								 */
								if(isset($array[$i]['cco'][$j]['fueS']['csa']))
								{
									//anitalavalatina echo "entro fue S csa<br>";
									if($procesar and isset($array[$i]['cco'][$j]['fueS']['ckb']) and $okDevCons)
									{
										//Quieren que se haga el proceso con los valores digitados.

										$array[$i]['cco'][$j]['fueS']['msg']="";
										/**
										 * Devolución
										 */
										if($array[$i]['cco'][$j]['fueS']['cdv']>0)
										{
											if( $array[$i]['art']['cmz'] or $cco[$codCco]['ima'])
											{
												/**
                                             * Si el código del artículo fue encontrado en la central o el centro de costos
                                             * tiene inventario por MATRIX entonces la devolución se realiza de otra forma.
                                             */

												//La devolución solo sa hace por cantidades enteras. En el if si el modulo es cero, es decir si la cantidad es entera entronces entrará por ahi.
												if(($array[$i]['cco'][$j]['fueS']['cdv']%1) == 0)
												{ //echo "paso33333......";
													//La función de devolución solo permite de
													$k=0;
													$ok=true;
													$array[$i]['art']['can'] =1;
													$contFaltante=0;
													do {
														$wbasdat="cenpro";
														$dronum="";
														$drolin="";
														$ok=devolucionCM($cco[$codCco],$array[$i]['art'],$pac,$error,$dronum,$drolin);

														if( $ok ){
															// echo "2222222CM.......<pre>"; var_dump( $pac ); echo "</pre>";
															// echo "<br>aaaa.......".$cco[$codCco];
																																	//traslado
															 devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
														}

														if($ok)
														{
															/**
															 * La cantidad de faltantes dificilmente será igual a la cantida a devolver, es por eso que en
															 */
															if($contFaltante >= $array[$i]['cco'][$j]['fueS']['cfa'])
															{
																$faltante=0;
																$jusF=" ";
															}
															else
															{
																$faltante=1;
																$jusF=$array[$i]['cco'][$j]['fueS']['jfa'];
																$contFaltante++;
															}
															//Movimiento de devoluciones
															$array[$i]['art']['can']=1;
//															echo "<b>antes</b><br>";
															if(!registrarDevFact($devCons,$dronum,$drolin,1,$array[$i]['cco'][$j]['fueS']['jde'],$array[$i]['cco'][$j]['fueS']['cfa'],$array[$i]['cco'][$j]['fueS']['jfa'],$usuario,$error))
															{
																$error['ok']="Hubo un problema grabando<br>el Movimiento de devolucion a<br>".$cco[$codCco]['cod']."-".$cco[$codCco]['nom'];
																$ok=false;
															}
															else{
																$articulosDevueltos++;

																if( !$solicitudCamillero ){
																	if( esTraslado( $cco[$codCco]['cod'] ) && true ){
																		peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoUsu['cod'], $cco[$codCco]['cod'], $pac['nom'], $pac['his'], $pac['ing']  );
																		$solicitudCamillero = true;
//																		peticionCamillero( 'on', '', '', $usuario, substr($ccoCod,3), $cco[$codCco]['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
																	}
																}
															}

//															echo "<b>despues</b><br>";
														}
														$k++;
													}while($k < $array[$i]['cco'][$j]['fueS']['cdv']  and $ok == true);
												}
												else
												{
													//Debe volver a intentar
													$ok=false;
													$array[$i]['cco'][$j]['fueS']['mod'] = true;
													$error['ok'] = "Este articulo solo permite devolver enteros</br>Si desea puede descartar la fracción restante.";
												}
											}
											else
											{
												$array[$i]['art']['can'] = $array[$i]['cco'][$j]['fueS']['cdv'];
												$ok=procesoDevMatrix($devCons, $pac,$array[$i]['art'],$array[$i]['cco'][$j]['fueS']['jde'],$array[$i]['cco'][$j]['fueS']['cfa'],$array[$i]['cco'][$j]['fueS']['jfa'],$cco[$codCco],"D",false,$usuario,$error);

												//Agosto 16 de 2011
												if( $ok ){
													 devolucionesKE( $array[$i]['art'], $pac, $cco[$codCco], $idRegistro, false );
												}

												if( $ok ){
													$articulosDevueltos++;
												}

											}

											if(!$ok)
											{
												//Hubo un error
												$array[$i]['cco'][$j]['fueS']['class'] = "error";
												$array[$i]['cco'][$j]['fueS']['msg'] = $error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueS']['msg']="LA DEVOLUCIÓN FUE REALIZADA CON EXITO <br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
												$array[$i]['cco'][$j]['fueS']['class'] = "exito";
											}
										}

										/**
                                     	 * Descarte
                                      	 */
										if($array[$i]['cco'][$j]['fueS']['cds']>0)
										{
											/**
                                             * El decarte debe ser un movimiento en el saldo del artículo para el centro de costos y la fuente y
                                             * ademas debe ser un registro de ajuste de aplicación, es decir un registro en la tabla de aplicación donde Aplaap='on'
                                             */
											$array[$i]['art']['can']=$array[$i]['cco'][$j]['fueS']['cds'];
											if(validacionDevolucion($cco[$codCco],$pac,$array[$i]['art'],false,false,$error))
											{
												//No interesa la fuente
												if(!registrarSaldosNoApl($pac,$array[$i]['art'],$cco[$codCco],false,$usuario,"D",false,$error))
												{
													//Hubo un error
													$array[$i]['cco'][$j]['fueS']['class'] = $error['color'];
													$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg'].$error['ok']."<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
												}
												else
												{
													if(registrarDescarte($devCons,$pac,$cco[$codCco],false,false,$array[$i]['art'],$array[$i]['cco'][$j]['fueS']['dds'],$usuario,$error))
													{
														$array[$i]['cco'][$j]['fueS']['msg']=$array[$i]['cco'][$j]['fueS']['msg']." SE REALIZO EL DESCARTE EXITOSAMENTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";

														if($array[$i]['cco'][$j]['fueS']['class'] == 'texto')
														{
															$array[$i]['cco'][$j]['fueS']['class'] ="exito";
														}
														$articulosDevueltos++;
													}
													else
													{
														$array[$i]['cco'][$j]['fueS']['class'] = "error";
														$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."SE MODIFICO EL SALDO PERO NO SE HIZO LA APLICACIÓN DURANTE EL DESACARTE<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
													}
												}
											}
											else
											{
												$array[$i]['cco'][$j]['fueS']['class'] = "error";
												$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN , O UN DESCARTE DE ESTE ARTÍCULO A ESTE PACIENTE MIENTRAS UD. HACIA ESTE DESCARTE.<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
											}
										}

										//Sobrante
										if($array[$i]['cco'][$j]['fueS']['sob']>0)
										{
											if(registrarSobranteDevolucion($devCons,$cco[$codCco],$array[$i]['art'],$array[$i]['cco'][$j]['fueS']['sob'],$usuario,$error))
											{
												$articulosDevueltos++;
												$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."SE REALIZO EL REGISTRO DEL SOBRANTE EXITOSAMENTE.<br><IMG SRC='/matrix/images/medical/root/feliz.ico'>";
											}
											else
											{
												$array[$i]['cco'][$j]['fueS']['class'] = "error";
												$array[$i]['cco'][$j]['fueS']['msg'] = $array[$i]['cco'][$j]['fueS']['msg']."NO SE REALIZO EL REGISTRO DEL SOBRANTE<IMG SRC='/matrix/images/medical/root/Malo.ico'>.<BR>";
											}
										}
										//Si hubo algun problema en grabación o modificación debe salir y volver  intentarlo.
										$array[$i]['cco'][$j]['fueS']['mod'] = false;

									}//Fin if Simple
									elseif ($procesar and isset($aceptar))
									{
										$array[$i]['cco'][$j]['fueS']['mod']=false;
									}

									$carro=Carro($pac, $cco[$codCco]['cod'], 'P', $array[$i]['art']['cod']);
									if(!isset($aceptar) and !isset($aceptar2))
									{
										$array[$i]['cco'][$j]['fueS']['cdv']=$array[$i]['cco'][$j]['fueS']['cdv']-$carro;
									}

									if($array[$i]['cco'][$j]['fueS']['mod'])
									{
										MostrarModificable($array[$i]['cco'][$j]['fueS'],$cco[$codCco]['fueC'],"Simple","array[".$i."][cco][".$j."][fueS]",$jus,$array[$i]['art']['jdv'], $carro, $ccoUsu );
										echo "</tr><tr>";
									}
									else
									{
										MostrarNoModificable($array[$i]['cco'][$j]['fueS'],$cco[$codCco]['fueC'],"Simple","array[".$i."][cco][".$j."][fueS]", $array[$i]['art']['jdv']);
										echo "</tr><tr>";
									}
								}

								//Zona de hidden para las cosas propias del artículo
								echo "<input type='hidden' name='array[".$i."][cco][".$j."][sid]' value='".$array[$i]['cco'][$j]['sid']."'>";
								echo "<input type='hidden' name='array[".$i."][cco][".$j."][rsp]' value='".$array[$i]['cco'][$j]['rsp']."'>";
								echo "<input type='hidden' name='array[".$i."][cco][".$j."][cod]' value='".$codCco."'>";
							}//fin del for de los centros de costos para el artículo $i

							//Hidden de la información del artículo
							echo "<input type='hidden' name ='array[".$i."][art][cod]' value='".$array[$i]['art']['cod']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][nom]' value='".$array[$i]['art']['nom']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][tot]' value='".$array[$i]['art']['tot']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][rsp]' value='".$array[$i]['art']['rsp']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][neg]' value=''>";

							if($array[$i]['art']['act'])
							{
								echo "<input type='hidden' name ='array[".$i."][art][act]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name ='array[".$i."][art][act]' value=''>";
							}
							if($array[$i]['art']['jdv'])
							{
								echo "<input type='hidden' name ='array[".$i."][art][jdv]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name ='array[".$i."][art][jdv]' value=''>";
							}

							if($array[$i]['art']['cmz'])
							{
								echo "<input type='hidden' name ='array[".$i."][art][cmz]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name ='array[".$i."][art][cmz]' value=''>";
							}
							echo "<input type='hidden' name ='array[".$i."][art][class]' value='".$array[$i]['art']['class']."'>";
							echo "<input type='hidden' name ='array[".$i."][art][uni]' value='".$array[$i]['art']['uni']."'>";
							/*
							echo "<input type='hidden' name ='array[".$i."][art][]' value='".$array[$i]['art']['']."'>";
							*/
						}//fin del for de todos los artículos

						//Si $articulosDevueltos es igual a 0, indica que no se realizo ni una devolucion, ni un descarte o ningun falante
						if( $articulosDevueltos == 0 && !empty($devCons) ){
							eliminarMovimientoDeDevolucion( $devCons );
							descontarConsecutivoDevolucion();
							?>
							<script>document.getElementById( "consecutivo" ).style.display = 'none';</script>;
							<?php
						}

						//Información Básica del paciente y otros
						echo "<input type='hidden' name ='bd' value='".$bd."'>";
						echo "<input type='hidden' name ='emp' value='".$emp."'>";

						echo "<input type='hidden' name ='historia' value='".$pac['his']."'>";
						echo "<input type='hidden' name ='ing' value='".$pac['ing']."'>";
						echo "<input type='hidden' name ='pac[his]' value='".$pac['his']."'>";
						echo "<input type='hidden' name ='pac[ing]' value='".$pac['ing']."'>";
						echo "<input type='hidden' name ='pac[nom]' value='".$pac['nom']."'>";
						echo "<input type='hidden' name ='pac[doc]' value='".$pac['doc']."'>";
						echo "<input type='hidden' name ='pac[tid]' value='".$pac['tid']."'>";
						echo "<input type='hidden' name ='pac[dxv]' value='".$pac['dxv']."'>";
						echo "<input type='hidden' name ='pac[act]' value='1'>";

						echo "<input type='hidden' name='ccoUsu[cod]' value='".$ccoUsu['cod']."'>";
						echo "<input type='hidden' name='ccoUsu[nom]' value='".$ccoUsu['nom']."'>";

						if(isset($alp))
						{
							echo "<input type='hidden' name ='alp' value='1'>";
						}

						/*
						echo "<input type='hidden' name ='array[".$i."]' value='".$array[$i] ."'>";
						*/

						//Información de los centros de costos
						$countCco=count($cco);
						for($i=0;$i<$countCco;$i++)
						{

							echo "<input type='hidden' name='cco[".$i."][cod]' value='".$cco[$i]['cod']."'>";
							echo "<input type='hidden' name='cco[".$i."][nom]' value='".$cco[$i]['nom']."'>";
							echo "<input type='hidden' name='cco[".$i."][fap]' value='".$cco[$i]['fap']."'>";
							echo "<input type='hidden' name='cco[".$i."][fue]' value='".$cco[$i]['fue']."'>";
							echo "<input type='hidden' name='cco[".$i."][fapC]' value='".$cco[$i]['fapC']."'>";
							echo "<input type='hidden' name='cco[".$i."][fueC]' value='".$cco[$i]['fueC']."'>";

							if($cco[$i]['act'])
							{
								echo "<input type='hidden' name='cco[".$i."][act]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name='cco[".$i."][act]' value=''>";
							}

							if($cco[$i]['apl'])
							{
								echo "<input type='hidden' name='cco[".$i."][apl]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name='cco[".$i."][apl]' value=''>";
							}

							if($cco[$i]['ima'])
							{
								echo "<input type='hidden' name='cco[".$i."][ima]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name='cco[".$i."][ima]' value=''>";
							}


							echo "<input type='hidden' name='cco[".$i."][class]' value='".$cco[$i]['class']."'>";

						}
						echo "</table></td></tr>";

						if( $seHizoSolicitudCamillero ){
							echo "<tr><td><table align='center'>";
							echo "<tr>";
							echo "<td>";
							echo "<B>SE HA SOLICITADO CAMILLERO</B>";
							echo "</td>";
							echo "</tr>";
							echo "</table></td></tr>";
						}

						if(!isset($aceptar) or  !$procesar)
						{
							echo "<tr><td align='center' ><br><br><input type='submit' name='aceptar' value='ACEPTAR'></TD></tr>";
							// echo "<tr><td align='center' ><br><br><input type='submit' name='aceptar' value='ACEPTAR' onClick='ejecutarSubmit( this )'></TD></tr>";
						}
						echo "</form>";
						echo "<tr><td align='center'>&nbsp;</td></td>";
						echo "<tr><td align='center'><A HREF='devoluciones.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'>Retornar</a>&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<A HREF='devoluciones.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ccoCod=UN.".$ccoUsu['cod']."'>Retornar con CC</a></font></td></tr>";
					}
					else
					{
						echo "<tr><td class='tituloSup'>ESTE PACIENTE NO TIENE ARTÍCULOS SUCEPTIBLES DE SER DEVUELTOS</td></tr>";
					}
				}
			}







			//echo "<tr><td class='errorTitulo'>No hay conexión con UNIX o Esta inactivo en la tabla 12 de Altas, no puede efectuarce la transacción</td></tr>";


		}
	}
	else
	{
		echo '<table>';
		echo "<tr><td class='errorTitulo'>EN EL MOMENTO NO TIENE AUTORIZACION PARA REALIZAR LA DEVOLUCION, INTENTE NUEVAMENTE A LAS ".$horario."</td></tr>";
	}
	echo "</td></tr></table>";
}
?>
</body>
</html>
