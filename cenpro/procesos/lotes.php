<?php
include_once("conex.php");



include_once("root/comun.php");



if( empty( $wemp_pmla ) )
	$wemp_pmla = "01";

include_once( "movhos/kardex.inc.php" );

// consulto los datos del usuario de la sesion
$pos = strpos($user, "-");
$wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario  


/**
* ========================================================DOCUMENTACION PROGRAMA================================================================================
*/
/**
* 1. AREA DE VERSIONAMIENTO
* 
* Nombre del programa: lotes.php
* Fecha de creacion:  2007-06-15
* Autor: Carolina Castano P
* Ultima actualizacion:
* Enero 29 de 2020 		Edwin 	- Se corrige variables de historia e ingreso en la función pintarBotonNPT ya que al realizar el query para buscar
*								  el cco del paciente no lo consultaba correctamente.
* Octubre 30 de 2019 	Jessica - Se agrega validación antes de reemplazar las dosis adaptadas para evitar que los reemplazos queden 
* Octubre 30 de 2019 	Jessica - Se agrega validación antes de reemplazar las dosis adaptadas para evitar que los reemplazos queden 
*								  incorrectos si el producto esta inactivo en movhos_000059
*								- Se agrega opción para imprimir el rotulo de las NPT despues de crear el lote (Para NPT externas)
* Mayo 22 de 2018 		Jessica - Se agrega order by por nombre en la función consultarPersonas() para que los select Elaborado por 
*								  y Revisado por se muestren en orden alfabético.
* Mayo 15 de 2018 		Jessica - Al realizar el reemplazo de las dosis adaptadas con la funcion reemplazarArticuloDetallePerfil() 
*								  de kardex.inc.php se envia la unidad de la dosis adaptada (campo Deffru de movhos_000059) en vez de 
*								  la unidad del medicamento ordenado, ya que si se realizaba el reemplazo de una dosis adaptada 
*								  generica quedaba con unidad bolsa (Ejemplo: 180 Bolsas en vez de 180 Miligramos).
* Febrero 16 de 2018 	Jessica - Se agrega validación para corregir error ya que si no tiene productos se recibe una variable vacia 
*								y no un array por lo que genera error.
* Febrero 15 de 2018 	Jessica - Se agrega select de usuarios que revisan el producto y se guarda en el campo Plorev de cenpro_000004
*								- Permite imprimir el rotulo de las NPT despues de crear el lote
* Septiembre 20 de 2017 Jessica Se imprime el sticker del paciente en la funcion cargarDAaPaciente() 
* Septiembre 12 de 2017 Jessica Cuando se crea el lote de una dosis adaptada la cantidad siempre debe ser uno,despues de crear el 
*								lote se muestra el boton para imprimir el sticker, activar la preparacion de la dosis adaptada y 
*								hacer el cargo al paciente.
* Septiembre 19 de 2016 Jessica	Se agrega boton para aprobar las npt despues de crear el lote y abrir el cargo a pacientes.
* Octubre 10 de 2013 (Edwin MG) Se deshabilita boton derecho del mouse y se cambia el tipo del boton aceptar de submit a button
* Febrero 28 de 2012 (Edwin MG) Al crear el lote, se valida que si un insumo vehículo de dilución (tipvdi cenpro_000001) sea 0 o vacío o
*								igual a la cantidad requerida para crear el lote, de lo contrario no se deja grabar el lote
* Julio 11 de 2012	(Edwin MG)	Deshabilito el boton de aceptar al momento de crear un lote para el producto
* Octubre 7 de 2011	(Edwin MG)	Se deshabilita los botones backspace y F5, para evitar duplicación de lotes
* 2007-07-19  Carolina Castano  Se puede dejar en blanco las cantidades para aquellos articulos con tipvdi en el maestro
* 2007-07-12  Carolina Castano  Se puede ingresar la cantidad de dosis del producto a integrar
* 2007-07-10  Carolina Castano  Se crea la opcion de incluir en un lote un producto ya creado de otro lote
* 2007-07-06  Carolina Castano  Publicacion del documento
* 
* 
* 2. AREA DE DESCRIPCION:
* 
* Este script realiza las historias:
* 
* 8. Creacion de lotes
* 9. Anulacion de lotes
* 11. Consulta de lotes
* 
* 3. AREA DE VARIABLES DE TRABAJO
* 
* $anular            numerico            si esta setiado y es igual a 1, se ha decido anular un lote
* $cantidades1       vector              lista de las cantidades ingresadas por el usuario para cada presentacion de los insumos
* $cco               caracter            centro de costos seleccionado por el usuario
* $ccos              vector              lista de centros de costos que pueden crear lotes
* $codigo            caracter            concepto de movimientos de creacion de lotes
* $codlot            caracter            codigo del lote
* $codpro            caracter            codigo del producto
* $con               caracter            concepto de salida de insumos que se pretende anular
* $conpro            caracter            codigo del producto
* $consecutivo       caracter            consecutivo  de movimientos de creacion de lotes
* $consulta          vector/carater      es vector cuando el usuario no ha seleccionado de la lista de lotes encontrados
* alguno pues se carga en consulta el primer lote encontrado,
* cuando se selecciona el sistema manda como consulta un carater, lo que se despliega en el select
* $consultas         vector              lista de lotes encontrados bajo un parametro de busqueda
* $contador          numerico            Cuenta cuantos lotes existen con saldo para un producto
* $crear             boolean             Indica si se ha activado un checkbox para crear el lote
* $estado            caracter            Estado del prodcuto (Activo o desactivado)
* $existencias       boolean             Si existen lotes con saldo para el producto
* $feccre            date                Fecha de creacion del lote
* $fecha             date                Fecha de creacion del producto
* $fecven            date                Fecha de vencimiento del lote
* $forcon            caracter            Foram de busqueda del lote (por codigo, producto, etc)
* $foto              boolean             Si el producto es fotosensible o no
* $genpro            caracter            Nombre generico del producto
* $inslis            vector              Lista de insumos y cantidades del producto
* $lotcan            numerico            Cantidad de productos para el lote
* $lotsal            numercio            Saldo de productos del lote
* $neve              boolean             Debe conservarse en nevera el producto
* $nompro            caracter            Nombre del prodcuto
* $parcon            caracter            Parametro de busqueda del lote (el codigo o el producto)
* $persona           caracter            Persona que elabora el lote
* $personas          vector              Lista de personas que pueden elaborar el lote
* $pintar            caracter            Lleva el flujo del programa, si no existe no se ha seleccionado produto
* si es uno, se esta creanso, mayor a 3 ya ha sido creado
* $presentacion      caracter            Unidad de trabajo del producto (BO-BOLSA por ejemplo)
* $productos         vector              Productos[0] contiene las caraterisiticas generales del producto
* $tfd               numerico            tienpo de infusion en dias del producto
* $tfh               numerico            tiempo de infusion en horas del producto, adicional a los dias
* $tippro            caracter            tipo de producto (codigo-descripcion-codificado o no)
* $tvd               numerico            tiempo de vencimiento en dias
* $tvh               numerico            tiempo de vencimiento en horas adicional a los dias
* $unidades          vector              Lista de presentaciones para los insumos del producto
* $val               numerico            cantidad que realmente puede hacerse de productos segun existencias
* $val2              boolean             true si pasa validaciones de existencias de presentaciones
* $via               caracter            via de administracion del producto
* 
* 4. AREA DE TABLAS
* 
* 000001 select
* 000002 select, update
* 000003 select
* 000004 select, insert, update
* 000005 select, insert, update
* 000006 update, insert, update
* 000007 select, insert, update
* 000008 select, update
* 000009 select, update
* 
* usuarios select
* det_selecciones select
* farstore_00002  select
* movhos_000011 select
* costosyp_00005 select
*/

/**
* ========================================================FUNCIONES==========================================================================
*/

function consultarEstadoFraccion($conex,$bdMovhos,$codigo,$centroCostos)
{
	$qEstadoFraccion = "   SELECT Defest
							 FROM ".$bdMovhos."_000059
							WHERE Defart = '".$codigo."'
							  AND Defcco = '".$centroCostos."'
							  AND Defest = 'on';";
	
	$resEstadoFraccion = mysql_query($qEstadoFraccion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qEstadoFraccion . " - " . mysql_error());
	$numEstadoFraccion = mysql_num_rows($resEstadoFraccion);
	
	$estadoActivo = false;
	if($numEstadoFraccion > 0)
	{
		$estadoActivo = true;
	}
	
	return $estadoActivo;
}

function consultarEstadoProducto($conex,$wbasedato,$codigo,$centroCostos)
{
	$qEstadoProducto = "   SELECT Artest
							 FROM ".$wbasedato."_000002
							WHERE Artcod = '".$codigo."'
							  AND Artest = 'on';";
	
	$resEstadoProducto = mysql_query($qEstadoProducto, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qEstadoProducto . " - " . mysql_error());
	$numEstadoProducto = mysql_num_rows($resEstadoProducto);
	
	$estadoActivo = false;
	if($numEstadoProducto > 0)
	{
		$estadoActivo = true;
	}
	
	return $estadoActivo;
}

function consultarDatosNutricion($codigoProducto)
{
	global $conex;
    global $wbasedato;
	
	$qDatosNutricion = "  SELECT Arthis,Arttin,Artins 
							FROM ".$wbasedato."_000002,".$wbasedato."_000001
						   WHERE Artcod='".$codigoProducto."' 
							 AND Artest='on' 
							 AND Tipcod=Arttip
							 AND Tippro='on'
							 AND Tipcdo='off' 
							 AND Tipnco='off'
							 AND Tipest='on';";
	
	$resDatosNutricion = mysql_query($qDatosNutricion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDatosNutricion . " - " . mysql_error());
	$numDatosNutricion = mysql_num_rows($resDatosNutricion);
	
	$datosNutricion = array();
	if($numDatosNutricion > 0)
	{
		$rowDatosNutricion = mysql_fetch_array($resDatosNutricion);
		
		$datosNutricion['historia'] = $rowDatosNutricion['Arthis'];
		$datosNutricion['tiempoInfusion'] = floor($rowDatosNutricion['Arttin']/60);
		$datosNutricion['institucion'] = $rowDatosNutricion['Artins'];
	}
	
	return $datosNutricion;
}

function consultarUnidadDA($conex,$bdMovhos,$codDA,$centroCostos)
{
	$qUnidadDA = " SELECT Deffru
					 FROM ".$bdMovhos."_000059
				    WHERE Defart = '".$codDA."'
					  AND Defcco = '".$centroCostos."'
					  AND Defest = 'on';";
	
	$resUnidadDA = mysql_query($qUnidadDA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qUnidadDA . " - " . mysql_error());
	$numUnidadDA = mysql_num_rows($resUnidadDA);
	
	$unidadDA = 0;
	if($numUnidadDA > 0)
	{
		$rowUnidadDA = mysql_fetch_array($resUnidadDA);
		$unidadDA = $rowUnidadDA['Deffru'];
	}
	
	return $unidadDA;
}

function consultarHorarioDispensacion($cco,$codProducto,$bdMovhos)
{
	global $conex;
	
	$qDispensacionCco = " SELECT Ccotdi
							FROM ".$bdMovhos."_000011
						   WHERE Ccocod='".$cco."'
							 AND Ccoest='on';";
	
	$resDispensacionCco = mysql_query($qDispensacionCco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDispensacionCco . " - " . mysql_error());
	$numDispensacionCco = mysql_num_rows($resDispensacionCco);
	
	$horarioDispensacion = "";
	if($numDispensacionCco > 0)
	{
		$rowsDispensacionCco = mysql_fetch_array($resDispensacionCco);
		if($rowsDispensacionCco['Ccotdi']!="00:00:00")
		{
			$horarioDispensacion = $rowsDispensacionCco['Ccotdi'];
		}
		else
		{
			$qDispensacionProd = "SELECT Tarhcd
									FROM ".$bdMovhos."_000099
								   WHERE Tarcod='".$codProducto."'
									 AND Tarest='on';";
			
			$resDispensacionProd = mysql_query($qDispensacionProd, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDispensacionProd . " - " . mysql_error());
			$numDispensacionProd = mysql_num_rows($resDispensacionProd);
			
			if($numDispensacionProd > 0)
			{
				$rowsDispensacionProd = mysql_fetch_array($resDispensacionProd);
				$horarioDispensacion = $rowsDispensacionProd['Tarhcd'];
			}
		}
	}		
	return $horarioDispensacion;
}

function consultarRondaPreparacionDA($historia,$ingreso,$codDA,$ido,$cco)
{
	global $conex;
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	$data = array('error'=>"",'mensaje'=>"");
	
	$queryDA = " SELECT Kadcpx,Kadron,Kadsus,Kadfin,Kadhin,Kadcnd
				   FROM ".$bdMovhos."_000054 
				  WHERE Kadhis='".$historia."' 
					AND Kading='".$ingreso."' 
					AND Kadart='".$codDA."' 
					AND Kadido='".$ido."' 
					AND Kadfec='".date('Y-m-d')."' 
					AND Kadest='on';";

	$resDA = mysql_query($queryDA,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryDA." - ".mysql_error());
	$numDA = mysql_num_rows($resDA);
	
	$idoNuevo = "";
	if($numDA > 0)
	{
		if($rowsDA = mysql_fetch_array($resDA))
		{
			if($rowsDA['Kadsus']=="off")
			{
				$codProducto = substr($codDA,0,2);
				$horarioDispensacion = consultarHorarioDispensacion($cco,$codProducto,$bdMovhos);
				$regleta = $rowsDA['Kadcpx'];
				
				$horaDispensacion = explode(":",$horarioDispensacion);
				$rondasRegleta = explode(",",$regleta);
				
				$rondaActual = floor(date("H")/2)*2;
				$rondaCorteDispensacion = $rondaActual+(($horaDispensacion[0])-2);
				
				$rondaAdispensar = "";
				$fechaAdispensar = date('Y-m-d');
				for($i=0;$i<count($rondasRegleta);$i++)
				{
					$rondaReglet = explode("-",$rondasRegleta[$i]);
					
					if($rondaReglet[1]>$rondaReglet[2] && $rondaActual<=$rondaCorteDispensacion)
					{
						$nroRonda = explode(":",$rondaReglet[0]);
						$rondaAdispensar = $nroRonda[0];
						
						if($rondaReglet[0]=="00:00:00")
						{
							$fechaAdispensar= date('Y-m-d',strtotime ( '+1 day' , strtotime ( date('Y-m-d') ) ));
						}
						break;
					}
					
				}
				
				if($rondaAdispensar!="")
				{
					$data['error'] = "0";
					$data['mensaje'] = "";
					$data['ronda'] = $rondaAdispensar;
					$data['fecha'] = $fechaAdispensar;
				}
				else
				{
					$data['error'] = "1";
					$data['mensaje'] = "No tienen rondas pendientes de dispensar";
				}
					
			}
			else
			{
				$data['error'] = "1";
				$data['mensaje'] = "La ".$codDA." con fecha y hora de inicio: ".$rowsDA['Kadfin']." ".$rowsDA['Kadhin']." esta suspendida";
			}
			
			
		}
	}		
	return $data;	
}

function consultarIdoDAreemplazada($bdMovhos,$historia,$ingreso,$codArticulo,$ido,$codigoProducto)
{
	global $conex;
	
	$queryIdoNuevo ="SELECT Rdaidn
					   FROM ".$bdMovhos."_000224 
					  WHERE Rdahis='".$historia."' 
						AND Rdaing='".$ingreso."' 
						AND Rdaart='".$codArticulo."' 
						AND Rdaido='".$ido."' 
						AND Rdacda='".$codDA."' 
						AND Rdaest='on';";
	
	$resIdoNuevo = mysql_query($queryIdoNuevo,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryIdoNuevo." - ".mysql_error());
	$numIdoNuevo = mysql_num_rows($resIdoNuevo);
	
	$idoNuevo = "";
	if($numIdoNuevo > 0)
	{
		if($rowsIdoNuevo = mysql_fetch_array($resIdoNuevo))
		{
			$idoNuevo = $rowsIdoNuevo['Rdaidn'];
		}
	}		
	
	return $idoNuevo;	
}

function consultarServicioPaciente($whistoria,$wingreso)
{
	global $conex;
	
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	
	$qServicio = "SELECT Habcco
					FROM ".$bdMovhos."_000020
				   WHERE Habhis='".$whistoria."'
					 AND Habing='".$wingreso."';";
	
	$resServicio = mysql_query($qServicio, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qServicio . " - " . mysql_error());
	$numServicio = mysql_num_rows($resServicio);
	
	$servicioPaciente = "";
	if($numServicio > 0)
	{
		if($rowsServicio = mysql_fetch_array($resServicio))
		{
			$servicioPaciente = $rowsServicio['Habcco'];
		}
	}		
	
	return $servicioPaciente;
}

function actualizarNuevoIdo($bdMovhos,$historia,$ingreso,$codArticulo,$codDA,$ido,$idoNuevo)
{
	global $conex;
	
	$qUpdateIdoNuevo = " UPDATE ".$bdMovhos."_000224 
							SET Rdaidn='".$idoNuevo."'
						  WHERE Rdahis='".$historia."' 
							AND Rdaing='".$ingreso."' 
							AND Rdaart='".$codArticulo."' 
							AND Rdaido='".$ido."' 
							AND Rdacda='".$codDA."' 
							AND Rdaest='on';";
	
	$resultadoUpdateIdoNuevo = mysql_query($qUpdateIdoNuevo,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateIdoNuevo." - ".mysql_error());
				
}

function aprobarDA($historia,$ingreso,$codArticulo,$ido,$codDA,$cco,$wbasedato,$servicioPaciente,$wronda,$wfecharonda)
{
	global $conex;
	global $wusuario;
	
	$data = array('error'=>"",'mensaje'=>"");
	
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	
	$cCostos= explode("-",$cco);
	
	$productoActivo = consultarEstadoProducto($conex, $wbasedato, $codDA, $cCostos[0]);
	
	if($productoActivo)
	{
		$fraccionActiva = consultarEstadoFraccion($conex, $bdMovhos, $codDA, $cCostos[0]);
		if($fraccionActiva)
		{
			$q = "SELECT Kargra,Karusu,Descripcion 
					FROM ".$bdMovhos."_000053,usuarios 
				   WHERE karhis='".$historia."' 
					 AND karing='".$ingreso."' 
					 AND Fecha_data='".date('Y-m-d')."'
					 AND Karusu=Codigo;";
			
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			
			if($num > 0)
			{
				if($rows = mysql_fetch_array($res))
				{
					if($rows['Kargra']=="on")
					{
						// Aprobar
						$q = "SELECT Kadart,Kadori,Kadfin,Kadhin,Kadido,Kadsus,Kadcon,Kadfec,Kadobs,Kaddia,Kadufr,Kadffa,Kadori 
								FROM ".$bdMovhos."_000054 
							   WHERE Kadhis='".$historia."' 
								 AND Kading='".$ingreso."' 
								 AND Kadfec='".date('Y-m-d')."'
								 AND Kadido='".$ido."';";
						
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						$num = mysql_num_rows($res);
						if($num > 0)
						{
							if($rows = mysql_fetch_array($res))
							{
								$suspendido = $rows['Kadsus'];
							}
							
							if($suspendido!="on")
							{
								// antes de reemplazar se debe validar si el producto ya esta registrado en movhos_000059, sino se debe utilizar la funcion RegistrarFracciones() de kardex.inc.php
								registrarFraccion( $conex, $bdMovhos, $wbasedato, $codDA, $cCostos[0], $wusuario );
								
								$unidadDA = consultarUnidadDA($conex,$bdMovhos,$codDA, $cCostos[0]);
								
								// $reemplazado = reemplazarArticuloDetallePerfil($bdMovhos,$historia,$ingreso,$rows['Kadfec'],$codArticulo,$codDA,$rows['Kaddia'],$rows['Kadobs'],$rows['Kadufr'],$rows['Kadffa'],"CM",$rows['Kadfin'],$rows['Kadhin'],$wusuario, $ido );
								$reemplazado = reemplazarArticuloDetallePerfil($bdMovhos,$historia,$ingreso,$rows['Kadfec'],$codArticulo,$codDA,$rows['Kaddia'],$rows['Kadobs'],$unidadDA,$rows['Kadffa'],"CM",$rows['Kadfin'],$rows['Kadhin'],$wusuario, $ido );
								
								$reemplazo = explode("|",$reemplazado);
								
								if($reemplazo[0]=="1" || $reemplazo[0]=="8")
								{
									actualizarNuevoIdo($bdMovhos,$historia,$ingreso,$codArticulo,$codDA,$ido,$reemplazo[6]);
									
									$data['error'] = "0";
									$data['mensaje'] = "La ".$codDA." ha sido reemplazada y aprobada";
									$data['ido'] = $reemplazo[6];
								}
								else
								{
									$data['error'] = "1";
									$data['mensaje'] = "La ".$codDA." NO se ha reemplazado ni aprobado";
								}
							}
							else
							{
								$data['error'] = "1";
								$data['mensaje'] = "La DA esta suspendida";
							}
						}
					}
					else
					{
						$data['error'] = "1";
						$data['mensaje'] = "La DA NO se ha aprobado, el kardex se encuentra actualmente en uso. Por el usuario: ".$rows['Karusu']." - ".$rows['Descripcion'].", intente en un momento.";
					}
					
				}
			}
			else
			{
				$data['error'] = "1";
				$data['mensaje'] = "La DA NO se ha aprobado, debe generar el kardex para el dia de hoy";
			}
		}
		else
		{
			$data['error'] = "1";
			$data['mensaje'] = "La ".$codDA." esta inactiva en el MAESTRO DE FRACCIONES POR ARTICULO (movhos_000059)";
		}
	}
	else
	{
		$data['error'] = "1";
		$data['mensaje'] = "La ".$codDA." esta inactiva en el MAESTRO DE ARTICULOS DE LA CENTRAL (cenpro_000002)";
	}
	
	
		
	
	return json_encode($data);
}

function consultarDAactiva($historia,$ingreso,$codigoProducto)
{
	global $conex;
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	
	// Consultar si esta suspendido
	$qDAactiva = " SELECT *  
					FROM ".$bdMovhos."_000054
				   WHERE Kadhis='".$historia."' 
					 AND Kading='".$ingreso."' 
					 AND Kadart='".$codigoProducto."' 
					 AND Kadfec='".date("Y-m-d")."'
					 AND Kadest='on'
					 AND Kadsus='off'
					 ;";
	
	$resDAactiva = mysql_query($qDAactiva, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDAactiva . " - " . mysql_error());
	$numDAactiva = mysql_num_rows($resDAactiva);
	
	$DAactiva=false;
	if($numDAactiva > 0)
	{
		$DAactiva=true;
	}
	
	return $DAactiva;
}
function consultarSiSupendido($historia,$ingreso,$warticuloda,$idoda)
{
	global $conex;
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	
	// Consultar si esta suspendido
	$qSuspendido  = " SELECT *  
						FROM ".$bdMovhos."_000054
					   WHERE Kadhis='".$historia."' 
						 AND Kading='".$ingreso."' 
						 AND Kadart='".$warticuloda."' 
						 AND Kadido='".$idoda."' 
						 AND Kadfec='".date("Y-m-d")."'
						 AND Kadest='on'
						 AND Kadsus='on'
						 ;";
	
	$resSuspendido = mysql_query($qSuspendido, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qSuspendido . " - " . mysql_error());
	$numSuspendido = mysql_num_rows($resSuspendido);
	
	$articuloSuspendido=false;
	if($numSuspendido > 0)
	{
		$articuloSuspendido=true;
	}
	
	return $articuloSuspendido;
}

function consultarSiEsDA($historia,$ingreso,$codigoProducto,$lote,$codCco,$warticuloda,$idoda,$cadena,$wbasedato,$wronda,$wfecharonda)
{
	global $conex;
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	
	// Consultar si es DA
	$qDA  = " SELECT Rdaart,Rdaido,Habcco,Cconom  
				FROM ".$bdMovhos."_000224,".$bdMovhos."_000011,".$bdMovhos."_000020 
			   WHERE Rdahis='".$historia."' 
			     AND Rdaing='".$ingreso."' 
			     AND Rdaart='".$warticuloda."' 
			     AND Rdaido='".$idoda."' 
			     AND Rdacda='".$codigoProducto."' 
			     AND Rdaest='on'
				 AND Habhis=Rdahis 
				 AND Habing=Rdaing
				 AND Habcco=Ccocod;";
	
	$resDA = mysql_query($qDA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDA . " - " . mysql_error());
	$numDA = mysql_num_rows($resDA);
	
	$botonCargos="";
	if($numDA > 0)
	{
		if($rowsDA = mysql_fetch_array($resDA))
		{
			$articuloSuspendido = consultarSiSupendido($historia,$ingreso,$warticuloda,$idoda);
			$DAactiva = consultarDAactiva($historia,$ingreso,$codigoProducto);
			
			if($articuloSuspendido && $DAactiva)
			{
				$nuevoIdoDA = consultarIdoDAreemplazada($bdMovhos,$historia,$ingreso,$warticuloda,$idoda,$codigoProducto);
				
				$cadena = $cadena."&wido=".$nuevoIdoDA;
				// mandar ido de la DA
				$botonCargos = "<input type='button' id='CargosDA' name='CargosDA' value='Imprimir Sticker y cargar DA al paciente' onclick='cargarDAaPaciente(\"".$historia."\",\"".$ingreso."\",\"".$rowsDA['Rdaart']."\",\"".$rowsDA['Rdaido']."\",\"".$codigoProducto."\",\"".$lote."\",\"".$codCco."\",\"".$rowsDA['Habcco']."\",\"off\",\"".$cadena."\",\"".$wbasedato."\",\"".$wronda."\",\"".$wfecharonda."\")'>";
			}
			elseif(!$articuloSuspendido)
			{
				$botonCargos = "<input type='button' id='CargosDA' name='CargosDA' value='Aprobar, reemplazar, Imprimir Sticker y cargar DA al paciente' onclick='cargarDAaPaciente(\"".$historia."\",\"".$ingreso."\",\"".$rowsDA['Rdaart']."\",\"".$rowsDA['Rdaido']."\",\"".$codigoProducto."\",\"".$lote."\",\"".$codCco."\",\"".$rowsDA['Habcco']."\",\"on\",\"".$cadena."\",\"".$wbasedato."\",\"".$wronda."\",\"".$wfecharonda."\")'>";
			}
			else
			{
				$botonCargos="<input type='button' name='porque' value='Imprimir' onclick='" . $cadena . "'>";;
			}
		}
	}
	
	return $botonCargos;
}


function aprobarNPT($historia,$ingreso,$NPTgenerica,$ido,$NPTreemplazada)
{
	global $conex;
	global $wusuario;
	
	$data = array('error'=>"",'mensaje'=>"");
	
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	$q = "SELECT Kargra,Karusu,Descripcion 
			FROM ".$bdMovhos."_000053,usuarios 
		   WHERE karhis='".$historia."' 
		     AND karing='".$ingreso."' 
			 AND Fecha_data='".date('Y-m-d')."'
			 AND Karusu=Codigo;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if($num > 0)
	{
		if($rows = mysql_fetch_array($res))
		{
			if($rows['Kargra']=="on")
			{
				// Aprobar
				$q = "SELECT Kadart,Kadori,Kadfin,Kadhin,Kadido,Kadsus,Kadcon 
						FROM ".$bdMovhos."_000054 
					   WHERE Kadhis='".$historia."' 
						 AND Kading='".$ingreso."' 
						 AND Kadfec='".date('Y-m-d')."'
						 AND Kadido='".$ido."';";
				
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num = mysql_num_rows($res);
				if($num > 0)
				{
					if($rows = mysql_fetch_array($res))
					{
						$articuloAprobar = $rows['Kadart'].";".$rows['Kadori'].";".$rows['Kadfin'].";".$rows['Kadhin'].";".$rows['Kadido']."|";
						$suspendido = $rows['Kadsus'];
						$confirmado = $rows['Kadcon'];
					}
					
					if($suspendido!="on")
					{
						if($confirmado=="on")
						{
							$aprobado = grabarEstadoAprobacionArticulos($bdMovhos,$historia,$ingreso,date('Y-m-d'),$articuloAprobar,"on",$wusuario);
					
							if($aprobado == "1")
							{
								$data['error'] = "0";
								$data['mensaje'] = "La NPT ha sido aprobada";
							}
							else
							{
								$data['error'] = "1";
								$data['mensaje'] = "La NPT NO se ha aprobado";
							}
						}
						else
						{
							$data['error'] = "1";
							$data['mensaje'] = "La NPT no esta confirmada";
						}
						
					}
					else
					{
						$data['error'] = "1";
						$data['mensaje'] = "La NPT esta suspendida";
					}
					
				}
			}
			else
			{
				$data['error'] = "1";
				$data['mensaje'] = "La NPT NO se ha aprobado, el kardex se encuentra actualmente en uso. Por el usuario: ".$rows['Karusu']." - ".$rows['Descripcion'].", intente en un momento.";
			}
			
		}
	}
	else
	{
		$data['error'] = "1";
		$data['mensaje'] = "La NPT NO se ha aprobado, debe generar el kardex para el dia de hoy";
	}	
	
	return json_encode($data);
}

function consultarSiEsNPT($codigoProducto)
{
	global $conex;
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	
	// Consultar si es NPT
	$qNPT = " SELECT Enuhis,Enuing,Enuart,Enuido,Enucnu,Enuord 
				FROM ".$bdMovhos."_000214 
			   WHERE Enucnu='".$codigoProducto."' 
			     AND Enuest='on'
			     AND Enurea='on';";
	
	$resNPT = mysql_query($qNPT, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qNPT . " - " . mysql_error());
	$numNPT = mysql_num_rows($resNPT);
	
	$arrayNPT = array();
	if($numNPT > 0)
	{
		if($rowsNPT = mysql_fetch_array($resNPT))
		{
			$arrayNPT['Enuhis'] = $rowsNPT['Enuhis'];
			$arrayNPT['Enuing'] = $rowsNPT['Enuing'];
			$arrayNPT['Enuart'] = $rowsNPT['Enuart'];
			$arrayNPT['Enuido'] = $rowsNPT['Enuido'];
			$arrayNPT['Enucnu'] = $rowsNPT['Enucnu'];
			$arrayNPT['Enuord'] = $rowsNPT['Enuord'];
		}
	}
	
	return $arrayNPT;
}
function pintarBotonNPT($codigoProducto,$lote,$codCco)
{
	global $conex;
	$bdMovhos = consultarAliasPorAplicacion($conex,"01", 'movhos');
	
	
	$arrayNPT = consultarSiEsNPT($codigoProducto);
	
	$botonCargos = "";
	if(count($arrayNPT)>0)
	{
		//Enero 29 de 2020
		//Se corrige las variables historia e ingreso
		$q = "SELECT Habcco,Cconom 
				FROM ".$bdMovhos."_000020,".$bdMovhos."_000011 
			   WHERE Habhis='".$arrayNPT['Enuhis']."' 
				 AND Habing='".$arrayNPT['Enuing']."'
				 AND Habcco=Ccocod;";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		
		if($num>0)
		{
			$row=mysql_fetch_array($res);
			
			$botonCargos = "<input type='button' id='CargosNPT' name='CargosNPT' value='Aprobar y cargar NPT al paciente' onclick='cargarNPTAPaciente(\"".$arrayNPT['Enuhis']."\",\"".$arrayNPT['Enuing']."\",\"".$arrayNPT['Enuart']."\",\"".$arrayNPT['Enuido']."\",\"".$arrayNPT['Enucnu']."\",\"".$lote."\",\"".$codCco."\",\"".$row['Habcco']."\");'>";
		}
	}
	
	return $botonCargos;
}

function grabarEncabezadoEntradaDeDescarte(&$codigo, &$consecutivo, $cco, $usuario)
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE"; 
    // $errlock = mysql_query($q,$conex);
    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '1' "
     . "      AND Condes = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '1'"
     . "      AND Condes = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ,       '', $usuario,      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
} 

function grabarEncabezadoSalidaDeDescarte(&$codigo, &$consecutivo, $cco, $usuario)
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE"; 
    // $errlock = mysql_query($q,$conex);
    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '-1' "
     . "      AND Condes = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1'"
     . "      AND Condes = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ,       '', $usuario,      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
} 

/**
 * 
 * @param $inscod
 * @param $codigo
 * @param $consecutivo
 * @param $usuario
 * @param $codlot
 * @param $codpro
 * @param $unidades
 * @param $cantidades1
 * @param $cco
 * @param $inscan
 * @return unknown_type
 */
function grabarDetalleSalidaDeDescarte($inscod, $codigo, $consecutivo, $usuario, $codlot, $codpro, $unidades, $cantidades1, $cco, $inscan)
{
    global $conex;
    global $wbasedato;


    if ($inscan > 0)
    {

        $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico           ,   Fecha_data            ,                  Hora_data     ,         Mdecon   ,             Mdedoc    ,     Mdeart      ,            Mdecan      ,     Mdefve  ,       Mdenlo         ,    Mdepre   ,  Mdeest,  Seguridad) "
             . "                                  VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $inscod . "', '" . ($inscan) . "', '0000-00-00',   '$codlot-$codpro'  ,  '$unidades',   'on' , 'C-" . $usuario . "') ";

        $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN INSUMO " . mysql_error());
    } 
}

function esInsumoQuimio( $insumo, $codpro ){
	
	global $conex;
    global $wbasedato;
//    global $codpro;
    
    $val = false;
    
//    $sql = "SELECT
//    			*
//    		FROM
//    			{$wbasedato}_000002
//    		WHERE
//    			artest = 'on'
//    			AND artcod = '$insumo'
//    			AND artnap = 'on'
//    		"; //echo "Conversion......".$sql;
//    			
//    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
//    
//    if( $rows = mysql_fetch_array( $res ) ){
//    	$val = true;
//    }
//    
//    return $val;
    
    $val = false;
    
    $sql = "SELECT
    			*
    		FROM
    			{$wbasedato}_000002
    		WHERE
    			artest = 'on'
    			AND artcod = '$insumo'
    			AND artnap = 'on'
    		"; //echo "Conversion......".$sql;
    			
    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    
    if( $rows = mysql_fetch_array( $res ) ){
    	
    	$sql = "SELECT
	    			*
	    		FROM
	    			{$wbasedato}_000002, {$wbasedato}_000001
	    		WHERE
	    			artest = 'on'
	    			AND arttip = tipcod
	    			AND artcod = '$codpro'
	    			AND tipina = 'on'
	    			AND tipest = 'on'
	    		"; //echo "<br><br>Conversion......".$sql;
	    			
	    $res2 = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    			
    	if( $rows2 = mysql_fetch_array( $res2 ) ){		
    		$val = true;
    	}
    }
    
    return $val;
	
}

function consultarConversion( $insumo, $presentacion ){
	
	global $conex;
    global $wbasedato;
    
    $val = 0;
    
    $sql = "SELECT
    			Appcnv
    		FROM
    			{$wbasedato}_000009
    		WHERE
    			appest = 'on'
    			AND appcod = '$insumo'
    			AND apppre = '$presentacion'"; //echo "Conversion......".$sql;
    			
    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    
    if( $rows = mysql_fetch_array( $res ) ){
    	$val = $rows['Appcnv'];
    }
    
    return $val;
}

// ----------------------------------------------------------funciones de persitencia------------------------------------------------
/**
* Consulta la lista de empleados que pueden crear un lote, es decir que pertencen al centro de costos. Si ya se ha seleccionado una
* persona esta se pone de primera en la lista para desplegar el el select
* 
* @param caracter $persona codigo-nombre de la personas que ya ha sido seleccionada como que elaborara el lote
* @param caracter $cco codigo-nombre del centro de costos donde se elabora el lote
* @return vector $personas, lista de personas que pueden elaborarar el lote, para desplegar en display
*/
function consultarPersonas($persona, $cco)
{
    global $conex;
    global $wbasedato;

    if ($persona != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
        {
            $personas[0] = $persona;
        $cadena = "Codigo != mid('" . $persona . "',1,instr('" . $persona . "','-')-1) AND";
        $inicio = 1;
    } 
    else
    {
        $personas[0] = '';
        $cadena = '';
        $inicio = 1;
    } 
    // consulto los conceptos
    $q = " 	   SELECT Codigo, Descripcion "
     . "         FROM  usuarios "
     . "        WHERE " . $cadena . " "
     . "              Empresa='01' "
     . "          and Ccostos=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "          and Activo='A' "
     . "     ORDER BY Descripcion; ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    if ($num1 > 0)
    {
        for ($i = 0;$i < $num1;$i++)
        {
            $row1 = mysql_fetch_array($res1);
            $personas[$inicio] = $row1['Codigo'] . '-' . $row1['Descripcion'];
            $inicio++;
        } 
    } 

    return $personas;
} 

/**
* Se consultan las presentacion de un insumo dado para el centro de costos
* 
* @param caracter $codigo codigo del insumo
* @param caracter $cco centro de costos (codigo-descripcion)
* @return vector $unidades vector con la lista de presentaciones del insumo (codigo, nombre comercial, nombre generico, unidades, fracciones, factor de conversion, fecha de vencimiento)
*/
function consultarUnidades($codigo, $cco)
{
    global $conex;
    global $wbasedato; 
    // consulto los conceptos
    $q = " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi, Appfve "
     . "        FROM  " . $wbasedato . "_000009, movhos_000026 "
     . "      WHERE Appcod='" . $codigo . "' "
     . "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "            and Appest='on' "
     . "            and Apppre=Artcod ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    if ($num1 > 0)
    {
        for ($i = 0;$i < $num1;$i++)
        {
            $row1 = mysql_fetch_array($res1);

            $enteras = floor($row1['Appexi'] / $row1['Appcnv']);
            $fracciones = $row1['Appexi'] - $enteras * $row1['Appcnv'];
            $unidades[$i] = $row1['Apppre'] . '-' . str_replace('-', ' ', $row1['Artcom']) . '-' . str_replace('-', ' ', $row1['Artgen']) . '-' . $enteras . '-' . $fracciones . '-' . $row1['Appcnv'] . '-' . $row1['Appfve'];
        } 
        return $unidades;
    } 
    else
    {
        return false;
    } 
} 

/**
* Se consultan las presentaciones que se utilizaron en la creacion del lote a través del movimiento
* 
* @param caracter $codlot codigo del lote
* @param caracter $codpro codigo del producto
* @param caracter $inscod codigo del insumo
* 
* retorna:
* @param vector $cantidades1 cantidad de cada una de las presentaciones
* @return vector $unidades       presentaciones usadas (codigo-nombre comercial-nombre generico)
*/
function consultarUnidades2($codlot, $codpro, &$cantidades1, $inscod)
{
    global $conex;
    global $wbasedato;

    $q = "   SELECT Mdepre, Mdecan, Artcom, Artgen "
     . " from " . $wbasedato . "_000007, movhos_000026 "
     . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
     . "      AND Mdepre=Artcod "
     . "      AND Mdeart='" . $inscod . "' ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);

    for ($i = 0;$i < $num1;$i++)
    {
        $row1 = mysql_fetch_array($res1);
        $unidades[$i] = $row1['Mdepre'] . '-' . str_replace('-', ' ', $row1['Artcom']) . '-' . str_replace('-', ' ', $row1['Artgen']);
        $cantidades1[$i] = $row1['Mdecan'];
    } 

    if (isset ($unidades))
    {
        return $unidades;
    } 
    else
    {
        return false;
    } 
} 

function consultarProductoA($codlot, $codpro, &$loteA, &$canA)
{
    global $conex;
    global $wbasedato;

    $q = "   SELECT Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1' "
     . "      AND Congas = 'on' "
     . "      AND Conest = 'on' ";
    $res1 = mysql_query($q, $conex);
    $row1 = mysql_fetch_array($res1);

    $q = "   SELECT Mdeart, Mdecan "
     . " from " . $wbasedato . "_000007 "
     . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
     . "      AND Mdecon='" . $row1[0] . "' ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);

    for ($i = 0;$i < $num1;$i++)
    {
        $row1 = mysql_fetch_array($res1);
        $exp = explode('-', $row1[0]); 
        // echo $exp[1];
        if (isset($exp[1]))
        {
            $q = "   SELECT Artcom "
             . " from " . $wbasedato . "_000002 "
             . "    WHERE Artcod = '" . $exp[1] . "' "
             . "      AND Artest='on' ";

            $res = mysql_query($q, $conex);
            $num = mysql_num_rows($res);

            if ($num > 0)
            {
                $row = mysql_fetch_array($res);
                $productoA = $exp[1] . '-' . $row[0];
                $loteA = $exp[0];
                $canA = $row1[1];
            } 
        } 
    } 

    if (isset ($productoA))
    {
        return $productoA;
    } 
    else
    {
        $loteA = '';
        $canA = '';
        return '';
    } 
} 
/**
* Cargar los centros de costos que crean lotes o pueden usar el programa, estan condfigurados en el maestro de centros de costos de movhos 
* como ccoima. Si el usuario ya habia ingresado un centro de costos, este deberia aparecer de primero en el vector para desplegarse en el select
* 
* @param caracter $cco codigo-nombre del centro de costos que ya ha sido seleccionado por usuario
* @return vector $ccos lista de centros de costos, que crean lotes
*/
function consultarCcos($cco)
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

    $res1 = mysql_query($q, $conex);
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

function consultarImpresora($wip, $cco)
{
    global $conex;
    global $wbasedato;

    if ($wip != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
        { 
            $exp = explode("-",$wip);
        $wips[0] = $wip;
        $cadena = " Impcod != " . $exp[0] . " AND";
        $inicio = 1;
    } 
    else
    {
        $cadena = '';
        $inicio = 0;
    } 
    // consulto los conceptos
    $q = " SELECT Impcod, Impnom, Impnip "
     . "        FROM root_000053 "
     . "      WHERE " . $cadena . " "
     . "        Impcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "        AND Impest='on' ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    if ($num1 > 0)
    {
        for ($i = 0;$i < $num1;$i++)
        {
            $row1 = mysql_fetch_array($res1);
            $wips[$inicio] = $row1['Impcod'] . '-' . $row1['Impnom'] . '-' . $row1['Impnip'];
            $inicio++;
        } 
    } 
    else if ($inicio == 0)
    {
        $wips = '';
    } 
    return $wips;
} 

function calcularAdaptacion($insumo, $producto, $cantidad)
{
    global $conex;
    global $wbasedato;

    $exp = explode('-', $producto);

    $q = " SELECT Pdecan "
     . "       FROM " . $wbasedato . "_000003 "
     . "    WHERE  Pdepro = '" . $exp[0] . "' "
     . "       AND Pdeest = 'on' "
     . "       AND Pdeins= '" . $insumo . "' ";

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        $row = mysql_fetch_array($res);
        return ($row[0] * $cantidad);
    } 
    else
    {
        return 0;
    } 
} 

/**
* A partir del codigo del producto, se buscan todas sus caraterisiticas y los insumos que lo conforman con sus cantidades
* 
* Recibe:
* 
* @param caracter $codigo codigo del producto, a partir del cual se realiza la busqueda
* 
* Retorna:
* @param caracter $via via de administracion del producto
* @param numerico $tfd tiempo de infusion en dias del producto
* @param numerico $tfh tiempo de infusion en horas del producto adicionales a los dias (no se utiliza por ahora)
* @param numerico $tvd tiempo de vencimiento en dias del producto
* @param numerico $tvh tiempo de vencimiento en horas del producto adicionales a los dias (aun no se utiliza)
* @param date $fecha fecha de creacion del producto
* @param vector $inslis lista de insumos del producto con sus cantidades
* @param caracter $tippro tipo de producto (codigo-descripcion-codificado o no)
* @param caracter $estado Estado del producto Activo o Desactivado
* @param boolean $foto Si es fotosensible o no el producto
* @param boolean $neve Si se debe guardar en nevera o no inmediatemente
* @param boolean $conpro Consecutivo del producto para ponerle al lote
* @param caracter $nompro Nombre comercial del producto
* @param caracter $genpro Nombre generico del producto
* @param caracter $prepro Unidad de trabajo del producto (codigo-descripcion)
*/
function consultarProducto($codigo, &$via, &$tfd, &$tfh, &$tvd, &$tvh, &$fecha, &$inslis, &$tippro, &$estado, &$foto, &$neve, &$conpro, &$nompro, &$genpro, &$prepro)
{
    global $conex;
    global $wbasedato;

    $q = " SELECT Artvia, Arttin, Arttve, Artfec, Arttip, Artest, Artfot, Artnev, Artcon, Artcom, Artgen, Artuni, Tipdes, Tipcdo, Unides "
     . "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, movhos_000027 "
     . "    WHERE Artcod = '" . $codigo . "' "
     . "       AND Artest = 'on' "
     . "       AND Arttip= Tipcod "
     . "       AND Artuni= Unicod "
     . "       AND Uniest= 'on' "
     . "    Order by 1 ";

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        $row = mysql_fetch_array($res);
        $via = $row['Artvia']; 
        // $tfd=floor($row['Arttin']/60);
        $tfd = $row['Arttin'];
        $tfh = $row['Arttin'] % 60;
        $tvd = floor($row['Arttve'] / 24);
        $tvh = $row['Arttve'] % 24;
        $fecha = $row['Artfec'];
        $foto = $row['Artfot'];
        $neve = $row['Artnev'];
        $conpro = $row['Artcon'];
        $nompro = $row['Artcom'];
        $genpro = $row['Artgen'];
        $prepro = $row['Artuni'] . '-' . $row['Unides'];

        if ($row['Tipcdo'] == 'on')
        {
            $row['Tipcdo'] = 'CODIFICADO';
        } 
        else
        {
            $row['Tipcdo'] = 'NO CODIFICADO';
        } 

        $tippro = $row['Arttip'] . '-' . $row['Tipdes'] . '-' . $row['Tipcdo'];
        if ($row['Artest'] == 'on')
        {
            $estado = 'Creado';
        } 
        else
        {
            $estado = 'Desactivado';
        } 
    } 

    $q = " SELECT Pdeins, Pdecan, Artcom, Artgen, Artuni, Unides "
     . "       FROM " . $wbasedato . "_000003, " . $wbasedato . "_000002, movhos_000027 "
     . "    WHERE  Pdepro = '" . $codigo . "' "
     . "       AND Pdeest = 'on' "
     . "       AND Pdeins= Artcod "
     . "       AND Artuni= Unicod "
     . "       AND Uniest='on' "
     . "    Order by 1 ";

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);
            $inslis[$i]['cod'] = $row[0];
            $inslis[$i]['nom'] = str_replace('-', ' ', $row[2]);
            $inslis[$i]['gen'] = str_replace('-', ' ', $row[3]);
            $inslis[$i]['pre'] = $row[4] . '-' . $row[5];
            $inslis[$i]['can'] = $row[1];
            $inslis[$i]['pri'] = '';
            $inslis[$i]['est'] = 'on';
        } 
    } 
} 

function consultarAdaptacion($parbus, $forbus)
{
    global $conex;
    global $wbasedato;

    switch ($forbus)
    {
        case 'Rotulo':

            $q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
             . "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, movhos_000027 "
             . "    WHERE Artcod like '%" . $parbus . "%' "
             . "       AND Artest = 'on' "
             . "       AND Artuni= Unicod "
             . "       AND Tipest = 'on' "
             . "       AND Tipcod = Arttip "
             . "       AND Uniest='on' "
             . "    Order by 1 ";
            break;

        case 'Codigo':
            $q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
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

            $q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
             . "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001, movhos_000027 "
             . "    WHERE Artcom like '%" . $parbus . "%' "
             . "       AND Artest = 'on' "
             . "       AND Tipest = 'on' "
             . "       AND Tipcod = Arttip "
             . "       AND Artuni= Unicod "
             . "       AND Uniest='on' "
             . "    Order by 1 ";

            break;

        case 'Nombre generico':

            $q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
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

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);

            $productosA[$i] = $row[0] . '-' . $row[1] . '-' . $row[2] . '-' . $row[3] . '-' . $row[4];
        } 
    } 
    else
    {
        $productosA = '';
    } 
    return $productosA;
} 
/**
* Se consulta un lote de acuerdo a una forma de busqueda y parametro ingresado por el usuario, los resultados se cargan en un vector
* 
* @param caracter $parbus paramentro de busqueda, el numero del lote, el codigo del producto etc
* @param caracter $forbus forma de busqueda si por codigo, nombre, etc. Alimenta un Switch
* @return vector $consultas con los datos de los lotes encontrados con el codigo de busqueda
* 
*               $consultas[$i]['cod']=codigo del lote
* $consultas[$i]['pro']=codigo del producto
* $consultas[$i]['fcr']=fecha de creacion
* $consultas[$i]['fve']=fecha de vencimiento
* $consultas[$i]['cin']=cantidad incial de producto
* $consultas[$i]['sal']=saldo de productos
* $consultas[$i]['est']=estado del lote, puede esta anulado
* $consultas[$i]['cco']=centro de costos que hizo el lote
*/
function consultarLotes($parbus, $forbus)
{
    global $conex;
    global $wbasedato;

    switch ($forbus)
    {
        case 'Codigo del Producto':
            $q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Artcom "
             . "       FROM " . $wbasedato . "_000004, " . $wbasedato . "_000002  "
             . "    WHERE Plopro like '%" . $parbus . "%' "
             . "    AND Plosal>0 "
             . "    AND Ploest='on' "
             . "    AND Plopro = Artcod "
             . "    AND Artest='on' "
             . "    Order by 2, 1 asc ";

            break;
        case 'Numero del lote':

            $q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Artcom   "
             . "       FROM " . $wbasedato . "_000004, " . $wbasedato . "_000002 "
             . "    WHERE Plocod like '%" . $parbus . "%' "
             . "    AND Plosal>0 "
             . "    AND Ploest='on' "
             . "    AND Plopro = Artcod "
             . "    AND Artest='on' "
             . "    Order by 2, 1 asc ";

            break;
        case 'Nombre comercial del Producto':

            $q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Artcom   "
             . "       FROM " . $wbasedato . "_000004, " . $wbasedato . "_000002 "
             . "    WHERE Artcom like '%" . $parbus . "%' "
             . "       AND Artest = 'on' "
             . "       AND Plopro = Artcod "
             . "    AND Plosal>0 "
             . "    AND Ploest='on' "
             . "    AND Artest='on' "
             . "    Order by 2, 1 asc ";

            break;
        case 'Nombre generico del Producto':

            $q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Artcom   "
             . "       FROM " . $wbasedato . "_000004, " . $wbasedato . "_000002 "
             . "    WHERE Artgen like '%" . $parbus . "%' "
             . "       AND Artest = 'on' "
             . "       AND Plopro = Artcod "
             . "    AND Plosal>0 "
             . "    AND Ploest='on' "
             . "    AND Artest='on' "
             . "    Order by 2, 1 asc ";

            break;

        case 'COMPLETO':
            $exp = explode('-', $parbus);
            $q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Artcom "
             . "       FROM " . $wbasedato . "_000004, " . $wbasedato . "_000002 "
             . "    WHERE Plopro = '" . $exp[0] . "' "
             . "       AND Plocod = '" . $exp[1] . "' "
             . "    AND Plosal>0 "
             . "    AND Ploest='on' "
             . "    AND Plopro = Artcod "
             . "    AND Artest='on' "
             . "    Order by 2, 1 asc ";

            break;
    } 

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);

            $consultas[$i]['cod'] = $row['Plocod'];
            $consultas[$i]['pro'] = $row['Plopro'];
            $consultas[$i]['fcr'] = $row['Plofcr'];
            $consultas[$i]['fve'] = $row['Plofve'];
            $consultas[$i]['cin'] = $row['Plocin'];
            $consultas[$i]['sal'] = $row['Plosal'];
            $consultas[$i]['est'] = $row['Artcom'];

            if ($i == 0)
            {
                $q = " SELECT Codigo, Descripcion "
                 . "        FROM  usuarios "
                 . "      WHERE Codigo='" . $row['Ploela'] . "' "
                 . "        AND Activo='A' ";

                $res1 = mysql_query($q, $conex);
                $row1 = mysql_fetch_array($res1);
                $consultas[$i]['per'] = $row1['Codigo'] . '-' . $row1['Descripcion'];

                $q = " SELECT Subcodigo, Descripcion "
                 . "        FROM  det_selecciones "
                 . "      WHERE Codigo='02' "
                 . "        AND Medico='" . $wbasedato . "' ";

                $res1 = mysql_query($q, $conex);
                $row1 = mysql_fetch_array($res1);
                $consultas[$i]['cco'] = $row1['Subcodigo'] . '-' . $row1['Descripcion'];
            } 
        } 
        return $consultas;
    } 
    else
    {
        return false;
    } 
} 

function consultarLotesA($parbus, $cco, $lote, &$canA)
{
    global $conex;
    global $wbasedato;
    $exp = explode('-', $parbus);

    if ($lote != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
        {
            $consultas[0] = $lote;
        $cadena = "Plocod != '" . $lote . "' AND";
        $inicio = 1;

        if (!isset($canA) or $canA == '')
        {
            $q = " SELECT Plosal "
             . "       FROM " . $wbasedato . "_000004 "
             . "    WHERE Plopro = '" . $exp[0] . "' "
             . "       AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
             . "       AND Plocod = '" . $lote . "' ";
            $res = mysql_query($q, $conex);
            $row = mysql_fetch_array($res);
            $canA = $row[0];
        } 
    } 
    else
    {
        $cadena = '';
        $inicio = 0;
    } 

    $q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
     . "       FROM " . $wbasedato . "_000004 "
     . "    WHERE " . $cadena . " "
     . "       Plopro = '" . $exp[0] . "' "
     . "       AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
     . "       AND Ploest = 'on' "
     . "       AND Plosal > 0 "
     . "    Order by 1 asc  ";

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 0;$i < $num;$i++)
        {
            $row = mysql_fetch_array($res);

            $consultas[$inicio] = $row['Plocod'];
            $inicio++;

            if ($inicio == 1)
            {
                $canA = $row[7];
            } 
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
* valida que el insumo este activo en el maestro de insumos y que existan las cantidades adecuadas en el kardex
* actualiza la cantidad necesitada por insumo
* 
* @param vector $inslis vector con la lista de insumos
* @param caracter $cco centro de costos
* @param numerico $can cantidad de productos a producir
* @return numerico $val indica la cantidad de insumo disponible, si es -1 el insumo no esta en maestro de articulos, si es cero no esta en el kardex
*/
function validarComposicion(&$inslis, $cco, $can)
{
    global $conex;
    global $wbasedato;

    $val = $can;

    for ($i = 0; $i < count($inslis); $i++)
    {
        $inslis[$i]['est'] = 'on';
        if ($inslis[$i]['lot'] == '')
        {
            $inslis[$i]['lot'] = 0;
        } 

        $q = " SELECT * FROM " . $wbasedato . "_000002 where Artcod='" . $inslis[$i]['cod'] . "' and Artest='on' ";
        $res1 = mysql_query($q, $conex);
        $num3 = mysql_num_rows($res1);
        if ($num3 > 0)
        {
            $q = " SELECT karexi FROM " . $wbasedato . "_000005 where karcod='" . $inslis[$i]['cod'] . "' and Karcco= mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
            $res1 = mysql_query($q, $conex);
            $num1 = mysql_num_rows($res1);
            if ($num1 > 0)
            {
                $row1 = mysql_fetch_array($res1);
                if ($row1[0] >= $inslis[$i]['lot'])
                {
                    $inslis[$i]['est'] = 'on';
                    $val = $val;
                } 
                else
                {
                    $inslis[$i]['est'] = 'off';
                    if ($val > $row1[0])
                    {
                        $val = $row1[0];
                    } 
                } 
            } 
            else
            {
                $inslis[$i]['est'] = 'off';
                $val = 0;
            } 
        } 
        else
        {
            $inslis[$i]['est'] = 'off';
            $val = -1;
        } 
    } 

    /**
    * if($val>0)
    * {
    * for ($i=0; $i<count($inslis); $i++)
    * {
    * $inslis[$i]['lot']=$inslis[$i]['can']*$val;
    * }
    * }
    */
    return $val;
} 

/**
* Valida que haya existencia para las presentaciones segun la cantidad ingresada por el usuario
* y que la cantidad para cada presentacion sume lo requerido para el insumo
* se actualiza la cantidad que puede hacerse de insumo
* 
* @param vector $inslis contiene el codigo y la cantidad de un insumo
* @param vector $unidades vector con las presentaciones existentes para un insumo
* @param vector $cantidades1 vector con las cantidades ingresadas para cada presentacion del insumo
* @param caracter $cco centro de costos (codigo-descripcion)
* @return boolean si pasa validacion (true) si no (false)
*/
function validarPresentaciones(&$inslis, $unidades, $cantidades1, $cco)
{
    global $conex;
    global $wbasedato;

    $sumador = 0;
    for ($i = 0; $i < count($unidades); $i++)
    {
        if ($cantidades1[$i] == '')
        {
            $cantidades1[$i] = 0;
        } 

        $inslis['est'] = 'on';

        $q = " SELECT Appcnv, Appexi "
         . "        FROM  " . $wbasedato . "_000009, movhos_000026 "
         . "      WHERE Appcod='" . $inslis['cod'] . "' "
         . "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
         . "            and Appest='on' "
         . "            and Apppre=mid('" . $unidades[$i] . "',1,instr('" . $unidades[$i] . "','-')-1) "
         . "            and Apppre=Artcod ";

        $res1 = mysql_query($q, $conex);
        $num1 = mysql_num_rows($res1);
        $row1 = mysql_fetch_array($res1);

        if ($cantidades1[$i] <= $row1[1])
        {
            $sumador = $sumador + $cantidades1[$i];
        } 
        else
        {
            $inslis['est'] = 'off';
            return false;
        } 
    } 

    $sumador = round($sumador, 3);
    $inslis['lot'] = round($inslis['lot'], 3);
    $var = $inslis['lot'] - $sumador;
    if ($sumador != $inslis['lot'])
    {  
        // modificacion 2007-07-19
        $q = " SELECT Arttip "
         . "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001 "
         . "    WHERE  Artcod ='" . $inslis['cod'] . "' "
         . "       AND Arttip = tipcod "
         . "       AND Tipvdi = 'on' "
         . "       AND Tipest = 'on' ";

        $res1 = mysql_query($q, $conex);
        $num1 = mysql_num_rows($res1);

        if ($num1 <= 0)
        {
            $inslis['est'] = 'off';
            return false;
        } 
        else
        {
			//2013-02-19
			//Válido que la cantidad ingresada sea 0 o vacía
			//Sí es así permito grabar el lote, de lo contraio no
			if( empty($sumador) || $sumador == 0 ){
				$inslis['lot'] = $sumador;
				return true;
			}
			else{
				$inslis['est'] = 'off';
				return false;
			}
        } 
    } 
    else
    {
        return true;
    } 
} 

function validarProductoA($productoA, $productoB)
{
    global $conex;
    global $wbasedato;

    $no = 0;
    $exp = explode('-', $productoA);

    $q = " SELECT Pdeins "
     . "       FROM " . $wbasedato . "_000003 "
     . "    WHERE  Pdepro = '" . $exp[0] . "' "
     . "       AND Pdeest = 'on' ";

    $res = mysql_query($q, $conex);
    $num = mysql_num_rows($res);

    for ($i = 0; $i < $num; $i++)
    {
        $row = mysql_fetch_array($res);

        $q = " SELECT * "
         . "       FROM " . $wbasedato . "_000003 "
         . "    WHERE  Pdepro = '" . $productoB . "' "
         . "       AND Pdeins = '" . $row[0] . "' "
         . "       AND Pdeest = 'on' ";

        $res1 = mysql_query($q, $conex);
        $num1 = mysql_num_rows($res);
        if ($num1 <= 0)
        {
            $no = $no++;
        } 
    } 

    if ($no == 0)
    {
        return 0;
    } 
    else if ($no == 1)
    { 
        // modificacion 2007-07-19
        $q = " SELECT Arttip, Tipvdi "
         . "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, " . $wbasedato . "_000003 "
         . "    WHERE  Pdepro = '" . $exp[0] . "' "
         . "       AND Pdeest = 'on' "
         . "       AND Artcod = Pdeins "
         . "       AND Arttip = tipcod "
         . "       AND Tipvdi = 'on' "
         . "       AND Tipest = 'on' ";

        $res1 = mysql_query($q, $conex);
        $num1 = mysql_num_rows($res);

        if ($num1 > 0)
        {
            return 0;
        } 
        else
        {
            return 1;
        } 
    } 
    else
    {
        return 1;
    } 
} 

function validarCanA($productoA, $can, $lote)
{
    global $conex;
    global $wbasedato;

    $exp = explode('-', $productoA);

    $q = " SELECT Plosal "
     . "       FROM " . $wbasedato . "_000004 "
     . "    WHERE  Plopro = '" . $exp[0] . "' "
     . "       AND Ploest = 'on' "
     . "       AND Plocod = '" . $lote . "' ";

    $res = mysql_query($q, $conex);
    $row = mysql_fetch_array($res);

    if ($row[0] < $can)
    {
        return 2;
    } 
    return 0;
} 

/**
* Graba el lote en la tabla de lotes con las cantidades indicadas de producto
* 
* @param caracter $codigo codigo del lote
* @param caracter $producto codigo del producto
* @param caracter $cco centro de costos (codigo-descripcion)
* @param date $feccre fecha de creacion del lote
* @param date $fecven fecha de vencimiento del lote
* @param numerico $can cantidad de productos para el lote
* @param numerico $sal saldo de producto (inicialmente igual a la cantidad)
* @param caracter $usuario codigo del usuario que graba
* @param caracter $persona persona que va a elaborar el lote (codigo-descripcion)
*/
// function grabarLote(&$codigo, $producto, $cco, $feccre, $fecven, $can, $sal, $usuario, $persona)
function grabarLote(&$codigo, $producto, $cco, $feccre, $fecven, $can, $sal, $usuario, $persona,$revisado="")
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000002 LOW_PRIORITY WRITE";
    $errlock = mysql_query($q, $conex);

    $q = "   UPDATE " . $wbasedato . "_000002 "
     . "      SET Artcon = Artcon + 1 "
     . "    WHERE Artcod = '" . $producto . "'"
     . "      AND Artest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Artcon from " . $wbasedato . "_000002 "
     . "    WHERE Artcod = '" . $producto . "'"
     . "      AND Artest = 'on' ";

    $res1 = mysql_query($q, $conex);
    $row2 = mysql_fetch_array($res1);

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $codigo = '';

    for ($i = 0; $i < (6 - strlen($row2[0])); $i++)
    {
        $codigo = $codigo . '0';
    } 
    $codigo = trim($codigo . $row2[0]);
	
	$usuarioRevisa = explode("-",$revisado);
	

    // $q = " INSERT INTO " . $wbasedato . "_000004 (   Medico       ,   Fecha_data,                  Hora_data,              Plocod,      Plopro ,     Plocco   ,   Plofcr  ,             Plofve,                        Plohve ,         Plocin    ,  Plosal,   Ploela,    Ploest,  Seguridad) "
     // . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $producto . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , '" . $feccre . "' , '" . $fecven . "', '" . (string)date("H:i:s") . "'  , '" . $can . "' ,  '" . $sal . "',mid('" . $persona . "',1,instr('" . $persona . "','-')-1),      'on', 'C-" . $usuario . "') ";

    $q = " INSERT INTO " . $wbasedato . "_000004 (   Medico       ,   Fecha_data,                  Hora_data,              Plocod,      Plopro ,     Plocco   ,   Plofcr  ,             Plofve,                        Plohve ,         Plocin    ,  Plosal,   Ploela,    Ploest , Plorev,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $producto . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , '" . $feccre . "' , '" . $fecven . "', '" . (string)date("H:i:s") . "'  , '" . $can . "' ,  '" . $sal . "',mid('" . $persona . "',1,instr('" . $persona . "','-')-1),      'on','".$usuarioRevisa[0]."', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL LOTE " . mysql_error());
} 

/**
* se graba el encabezado de salida de insumos por consumo
* 
* retorna:
* 
* @param caracter $codigo codigo del movimiento o concepto, se consulta con esta fucnion
* @param caracter $consecutivo consecutivo para el concepto 
* 
* recibe
* @param caracter $cco centro de costos
* @param caracter $usuario usuario que estar usando el sistema
*/
function grabarEncabezadoSalida(&$codigo, &$consecutivo, $cco, $usuario)
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE"; 
    // $errlock = mysql_query($q,$conex);
    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '-1' "
     . "      AND Congas = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1'"
     . "      AND Congas = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ,       '', $usuario,      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
} 

/**
* Se graba el detalle del movimiento salida de cada insumo, en realidad se realiza por cada insumo, por cada presentacion
* 
* @param caracter $inscod codigo del insumo
* @param caracter $codigo concepto del movimiento
* @param caracter $consecutivo consecutivo del movimeinto
* @param caracter $usuario usuario que graba
* @param caracter $codlot codigo del lote
* @param caracter $codpro codigo del producto
* @param vector $unidades presentaciones para el insumo
* @param vector $cantidades1 cantidad a utilizar de cada presentacion
* @param caracter $cco centro de costos (codigo-descripcion)
* @param numerico $inscan cantidad requerida del insumo
*/
function grabarDetalleSalida($inscod, $codigo, $consecutivo, $usuario, $codlot, $codpro, $unidades, $cantidades1, $cco, $inscan)
{
    global $conex;
    global $wbasedato;
    
    $restante = 0;

    for ($i = 0; $i < count($unidades); $i++)
    {
    	//2010-05-28
	    if( false && esInsumoQuimio( $inscod, $codpro ) ){
	    	
	    	$exp = explode( "-", $unidades[$i] );
	    	
	    	//$exp[4] son fracciones
	    	//$exp[5] Es la conversion
	    	if( $cantidades1[$i]%$exp[5] > 0 ){
	    		$adicional += $exp[5]-$cantidades1[$i]%$exp[5];
	    	}
	    	else{
	    		$adicional = 0;
	    	}
	    }
	    else{
	    	$adicional = 0;
	    }
	    
	    $restante += $adicional;
	    //fin 2010-05-28
    	
        if ($cantidades1[$i] == '')
        {
            $cantidades1[$i] = 0;
        } 

        if ($cantidades1[$i] > 0)
        {
            $q = " SELECT Appfve "
             . "        FROM  " . $wbasedato . "_000009 "
             . "    WHERE Apppre =  mid('" . $unidades[$i] . "',1,instr('" . $unidades[$i] . "','-')-1) "
             . "      AND Appcod ='" . $inscod . "' "
             . "      AND Appest ='on' "
             . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

            $res1 = mysql_query($q, $conex);
            $row1 = mysql_fetch_array($res1);

            $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico           ,   Fecha_data            ,                  Hora_data     ,         Mdecon   ,             Mdedoc    ,     Mdeart      ,            Mdecan                      ,           Mdefve  , Mdenlo                               , Mdepre                      ,  Mdeest,  Seguridad) "
             . "                                  VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $inscod . "', '" . ($cantidades1[$i]+$adicional) . "', '" . $row1[0] . "',     '" . $codlot . "-" . $codpro . "',  mid('" . $unidades[$i] . "',    1   , instr('" . $unidades[$i] . "','-')-1), 'on', 'C-" . $usuario . "') ";

            $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN INSUMO " . mysql_error());
        } 
    } 
} 

function grabarDetalleSalida2($inscod, $codigo, $consecutivo, $usuario, $codlot, $codpro, $unidades, $cantidades1, $cco, $inscan)
{
    global $conex;
    global $wbasedato;

    $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,      Mdecan,           Mdefve, Mdenlo, Mdepre,  Mdeest,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $inscod . "',  '" . $inscan . "',  '',   '" . $codlot . "-" . $codpro . "',  '', 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN INSUMO " . mysql_error());
} 

/**
* Descuente el kardex de insumo y la existencia de cada presentacion utilizada del insumo
* 
* @param caracter $inscod codigo del insumo
* @param numerico $inscan cantidad del insumo
* @param caracter $cco centro de costos (codigo-descripcion)
* @param vector $unidades lista de presentaciones para el insumo
* @param vector $cantidades1 lista de cantidades de cada presentacion
*/
function descontarInsumo($inscod, $inscan, $cco, $unidades, $cantidades1)
{
    global $conex;
    global $wbasedato;
    global $lotcan;
    global $codpro;
    global $wusuario;
    global $codigoDes;
    global $consecutivoDes;
    global $codlot; 
    
    $restante = 0;
    $adicional = 0;
    
    $q = "   UPDATE " . $wbasedato . "_000005 "
     . "      SET karexi = karexi - " . $inscan . " "
     . "    WHERE Karcod = '" . $inscod . "' "
     . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";// echo "<br><br>.......$q";
    
    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());

    for ($i = 0; $i < count($unidades); $i++)
    {
    	$adicional = 0;
    	
    	//2010-05-28
	    if( esInsumoQuimio( $inscod, $codpro ) ){
	    	
	    	$exp = explode( "-", $unidades[$i] );
	    	//$exp[4] son fracciones
	    	//$exp[5] Es la conversion
			$cantidades1[$i] = (float)$cantidades1[$i];
			$exp[5] = (float)$exp[5];
			
	    	if( $cantidades1[$i]%$exp[5] > 0 ){
	    		$adicional += $exp[5]-$cantidades1[$i]%$exp[5];
	    	}
	    	else{
	    		$adicional = 0;
	    	}
	    	
	    	if( true || !isset($codigoDes) && !isset($consecutivoDes) ){
	    		if( $adicional > 0 ){
	    			
	    			if( !isset($codigoDes) && !isset($consecutivoDes) ){
	    				grabarEncabezadoSalidaDeDescarte( $codigoDes, $consecutivoDes, $cco, $wusuario );
	    			}
	    			grabarDetalleSalidaDeDescarte( $inscod, $codigoDes, $consecutivoDes, $wusuario, $codlot, $codpro, "{$exp[0]}-{$exp[1]}", $cantidades1, $cco, $adicional );
	    		}
	    	}
	    }
	    else{
	    	$adicional = 0;
	    }
	    
	    $restante += $adicional;
	    //fin 2010-05-28
    	
        if ($cantidades1[$i] == '')
        {
            $cantidades1[$i] = 0;
        } 
        $q = " SELECT Appexi, Appcnv "
         . "        FROM  " . $wbasedato . "_000009 "
         . "    WHERE Apppre =  mid('" . $unidades[$i] . "',1,instr('" . $unidades[$i] . "','-')-1) "
         . "      AND Appcod ='" . $inscod . "' "
         . "      AND Appest ='on' "
         . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

        $res1 = mysql_query($q, $conex);
        $row1 = mysql_fetch_array($res1);
		
        $fra = $row1['Appexi'] % $row1['Appcnv'];
        $resta = $fra - $cantidades1[$i];
        if ($resta < 0)
        {
            if (abs($resta) != $row1['Appcnv'])
            {
                $q = " SELECT Arttve "
                 . "        FROM  " . $wbasedato . "_000002 "
                 . "    WHERE Artcod ='" . $inscod . "' "
                 . "      AND Artest ='on' ";

                $res1 = mysql_query($q, $conex);
                $row1 = mysql_fetch_array($res1);

                $tiempo = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + ($row1[0] * 24 * 60 * 60);
                $tiempo = date('Y-m-d', $tiempo);
            } 
            else
            {
                $tiempo = '0000-00-00';
            } 
            $q = "   UPDATE " . $wbasedato . "_000009 "
             . "      SET Appexi = Appexi-" . $cantidades1[$i] . "-$adicional, Appfve = '" . $tiempo . "' "
             . "    WHERE Apppre =  mid('" . $unidades[$i] . "',1,instr('" . $unidades[$i] . "','-')-1) "
             . "      AND Appcod ='" . $inscod . "' "
             . "      AND Appest ='on' "
             . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";  //echo "<pre><br>1.....$q<br></pre>";
        } 
        else
        {
            $q = "   UPDATE " . $wbasedato . "_000009 "
             . "      SET Appexi = Appexi-" . $cantidades1[$i] . "-$adicional "
             . "    WHERE Apppre =  mid('" . $unidades[$i] . "',1,instr('" . $unidades[$i] . "','-')-1) "
             . "      AND Appcod ='" . $inscod . "' "
             . "      AND Appest ='on' "
             . "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "; //echo "<pre><br>2.....$q<br></pre>";
        } 
        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR111 UN INSUMO " . mysql_error());
    }

    if( $restante > 0 ){
    	
	    $q = "   UPDATE " . $wbasedato . "_000005 "
	     . "      SET karexi = karexi-$restante "
	     . "    WHERE Karcod = '" . $inscod . "' "
	     . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "; //echo "<br><br>3:.......$q";
		
	    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());
    }
} 

/**
* Incrementa la cantidad de insumos y de las presentaciones que fueron utilizadas
* 
* @param caracter $inscod codigo del insumo
* @param numerico $inscan cantidad del insumo
* @param caracter $cco centro de costos (codigo-descripcion)
* @param vector $unidades lista de presentaciones utilizadas para el insumo
* @param vector $cantidades1 lista de las cantidades usadas para cada presentacion
* @param caracter $con concepto de salida de insumos
* @param caracter $codlot codigo del lote
* @param caracter $codpro codigo del producto
*/
function sumarInsumo($inscod, $inscan, $unidades, $cantidades1, $con, $codlot, $codpro)
{
    global $conex;
    global $wbasedato;
    global $wusuario;
    
    $restante = 0;
    $adicional = 0;

    for ($i = 0; $i < count($unidades); $i++)
    {
    	$adicional = 0;
    	
    	//2010-05-28
	    if( esInsumoQuimio( $inscod, $codpro  ) ){
	    	
	    	$exp = explode( "-", $unidades[$i] );
	    	
	    	$exp[5] = consultarConversion( $inscod, $exp[0] );
	    	
	    	//$exp[4] son fracciones
	    	//$exp[5] Es la conversion
	    	if( $exp[5] > 0 && $cantidades1[$i]%$exp[5] > 0 ){
	    		$adicional += $exp[5]-$cantidades1[$i]%$exp[5];
	    	}
	    	else{
	    		$adicional = 0;
	    	}
	    	
//	    	if( $adicional > 0 ){
//	    		grabarEncabezadoEntradaDeDescarte( $codigo, $consecutivo, $cco, $wusuario );
//	    		grabarDetalleSalidaDeDescarte( $inscod, $codigo, $consecutivo, $wusuario, '', '', "{$exp[0]}-{$exp[1]}", $cantidades1, $cco, $adicional );
//	    	}
	    }
	    else{
	    	$adicional = 0;
	    }
	    
	    $restante += $adicional;
	    //fin 2010-05-28
	    
	    
        if ($cantidades1[$i] == '')
        {
            $cantidades1[$i] = 0;
        } 

        $q = "   UPDATE " . $wbasedato . "_000005 "
         . "      SET karexi = karexi + " . $cantidades1[$i] . "+$adicional "
         . "    WHERE Karcod = '" . $inscod . "' "; //echo "<br><br>......$q";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());

        $q = "   SELECT  Mdefve from " . $wbasedato . "_000007 "
         . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
         . "      AND Mdecon='" . $con . "' "
         . "      AND Mdeart='" . $inscod . "' ";

        $res1 = mysql_query($q, $conex);
        $row1 = mysql_fetch_array($res1);

        $q = "   UPDATE " . $wbasedato . "_000009 "
         . "      SET Appexi = Appexi+" . $cantidades1[$i] . "+$adicional, Appfve = '" . $row1[0] . "' "
         . "    WHERE Apppre =  mid('" . $unidades[$i] . "',1,instr('" . $unidades[$i] . "','-')-1) "
         . "      AND Appcod ='" . $inscod . "' "
         . "      AND Appest ='on' "; //echo "<br><br>......$q";

        $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());
    } 
} 

/**
* Se graba el encabezado de entrada del producto
* 
* Retorna:
* 
* @param caracter $codigo codigo del concepto del movimiento de entrada
* @param caracter $consecutivo consecutivo del movimiento de entrada
* 
* Recibe
* @param carater $cco centro de costos (codigo-descripcion)
* @param carater $usuario codigo del suaurio que graba
*/
function grabarEncabezadoEntrada(&$codigo, &$consecutivo, $cco, $usuario)
{
    global $conex;
    global $wbasedato;

    $q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE";
    $errlock = mysql_query($q, $conex);

    $q = "   UPDATE " . $wbasedato . "_000008 "
     . "      SET Concon = (Concon + 1) "
     . "    WHERE Conind = '1'"
     . "      AND Congas = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);

    $q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '1'"
     . "      AND Congas = 'on' "
     . "      AND Conest = 'on' ";

    $res1 = mysql_query($q, $conex);
    $row2 = mysql_fetch_array($res1);
    $codigo = $row2[1];
    $consecutivo = $row2[0];

    $q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
    $errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

    $q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ,       '', '" . $usuario . "',      '' , 'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
} 

/**
* Se graba el detalle de entrada de producto
* 
* @param caracter $codpro codigo del prodcuto
* @param numerico $lotcan cantidad de producto a crear para el lote
* @param caracter $codigo concepto de entrada del producto
* @param caracter $consecutivo consecutivo del movmiento
* @param caracter $usuario codigo del usuario que graba
* @param caracter $codlot codigo del lote
* @param caracter $fecven fecha de vencimiento del lote
* @param caracter $codpro codigo del producto
*/
function grabarDetalleEntrada($codpro, $lotcan, $codigo, $consecutivo, $usuario, $codlot, $fecven)
{
    global $conex;
    global $wbasedato;

    $q = " INSERT INTO " . $wbasedato . "_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,      Mdecan,  Mdefve, Mdenlo, Mdeest,  Seguridad) "
     . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $codpro . "', '" . $lotcan . "' ,  '" . $fecven . "',     '" . $codlot . "-" . $codpro . "',   'on', 'C-" . $usuario . "') ";

    $err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN INSUMO " . mysql_error());
} 

/**
* Incrementa la existencia del producto en el kardex de inventario
* 
* @param caracter $codpro codigo del producto
* @param numerico $lotcan cantidad de producto para el lote
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $usuario codigo del usuario que graba
*/
function sumarProducto($codpro, $lotcan, $cco, $usuario)
{
    global $conex;
    global $wbasedato;

    $q = "   SELECT * from " . $wbasedato . "_000005 "
     . "    WHERE Karcod = '" . $codpro . "' "
     . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);

    IF ($num1 > 0)
    {
        $q = "   UPDATE " . $wbasedato . "_000005 "
         . "      SET karexi = karexi + " . $lotcan . " "
         . "    WHERE Karcod = '" . $codpro . "' "
         . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
    } 
    else
    {
        $q = " INSERT INTO " . $wbasedato . "_000005 (   Medico       ,   Fecha_data,                  Hora_data,              Karcod,              Karcco ,     Karexi   ,    Seguridad) "
         . "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codpro . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1),'" . $lotcan . "',  'C-" . $usuario . "') ";
    } 

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO SUMAR EL PRODUCTO " . mysql_error());
} 

/**
* Descuenta la cantidad de producto creado durante el lote
* 
* @param caracter $codpro codigo del producto
* @param unumerico $lotcan cantidad de producto para el lote
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caracter $usuario codigo de usuario que graba
*/
function descontarProducto($codpro, $lotcan, $usuario)
{
    global $conex;
    global $wbasedato;

    $q = "   UPDATE " . $wbasedato . "_000005 "
     . "      SET karexi = karexi - " . $lotcan . " "
     . "    WHERE Karcod = '" . $codpro . "' ";

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO SUMAR EL PRODUCTO " . mysql_error());
} 

function descontarArticuloMatrix($inscod, $cco, $lote, $canA)
{
    global $conex;
    global $wbasedato;

    $q = "   UPDATE " . $wbasedato . "_000005 "
     . "      SET karexi = karexi - " . $canA . " "
     . "    WHERE Karcod = '" . $inscod . "' "
     . "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR EL ARTICULO " . mysql_error());

    $q = "   UPDATE " . $wbasedato . "_000004 "
     . "      SET Plosal = Plosal-" . $canA . " "
     . "    WHERE Plocod =  '" . $lote . "' "
     . "      AND Plopro ='" . $inscod . "' "
     . "      AND Ploest ='on' "
     . "      AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());
} 

function sumarArticuloMatrix($inscod, $lote, $canA)
{
    global $conex;
    global $wbasedato;

    $q = "   UPDATE " . $wbasedato . "_000005 "
     . "      SET karexi = karexi + '" . $canA . "' "
     . "    WHERE Karcod = '" . $inscod . "' ";

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR EL ARTICULO " . mysql_error());

    $q = "   UPDATE " . $wbasedato . "_000004 "
     . "      SET Plosal = Plosal+'" . $canA . "' "
     . "    WHERE Plocod =  '" . $lote . "' "
     . "      AND Plopro ='" . $inscod . "' "
     . "      AND Ploest ='on' ";

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());
} 

/**
* Anula tanto los movmientos de salida de insumos como el movmientos de entrada de producto
* 
* @param caracter $codlot codigo del lote
* @param caracter $codpro codigo del producto
* 
* retorna
* @param caracter $con concepto de salida de insumos
*/
function anularMovimientos($codlot, $codpro, &$con)
{
    global $conex;
    global $wbasedato;

    $q = "   SELECT Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1' "
     . "      AND Congas = 'on' "
     . "      AND Conest = 'on' ";
    $res1 = mysql_query($q, $conex);
    $row1 = mysql_fetch_array($res1);
    $con = $row1[0];

    $q = "   SELECT distinct Mdedoc from " . $wbasedato . "_000007 "
     . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
     . "      AND Mdecon='" . $con . "' "
     . "      AND Mdeest='on' ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    $row1 = mysql_fetch_array($res1);

    for ($i = 0; $i < $num1; $i++)
    {
        $q = "   UPDATE " . $wbasedato . "_000007 "
         . "      SET Mdeest ='off' "
         . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
         . "    AND Mdedoc = '" . $row1[0] . "' "
         . "      AND Mdecon = '" . $con . "' "
         . "      AND Mdeest='on' ";

        $res = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HAN PODIDO ANULAR LOS MOVIMIENTOS " . mysql_error());
    } 

    $q = "   UPDATE " . $wbasedato . "_000006 "
     . "      SET Menest ='off' "
     . "    WHERE Mendoc = '" . $row1[0] . "' "
     . "      AND Mencon = '" . $con . "' ";
    $res2 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HAN PODIDO ANULAR LOS MOVIMIENTOS " . mysql_error());

    $q = "   SELECT Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '1' "
     . "      AND Congas = 'on' "
     . "      AND Conest = 'on' ";
    $res1 = mysql_query($q, $conex);
    $row1 = mysql_fetch_array($res1);
    $con2 = $row1[0];

    $q = "   SELECT distinct Mdedoc from " . $wbasedato . "_000007 "
     . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
     . "      AND Mdecon='" . $con2 . "' "
     . "      AND Mdeest='on' ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    $row1 = mysql_fetch_array($res1);

    $q = "   UPDATE " . $wbasedato . "_000007 "
     . "      SET Mdeest ='off' "
     . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
     . "    AND Mdedoc = '" . $row1[0] . "' "
     . "      AND Mdecon = '" . $con2 . "' "
     . "      AND Mdeest='on' ";

    $res = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HAN PODIDO ANULAR LOS MOVIMIENTOS " . mysql_error());

    $q = "   UPDATE " . $wbasedato . "_000006 "
     . "      SET Menest ='off' "
     . "    WHERE Mendoc = '" . $row1[0] . "' "
     . "      AND Mencon = '" . $con2 . "' ";
    $res2 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HAN PODIDO ANULAR LOS MOVIMIENTOS " . mysql_error());
    
    
    
    
    
    /**
     * Anula movimientos de descarte
     */
    $q = "   SELECT Concod from " . $wbasedato . "_000008 "
     . "    WHERE Conind = '-1' "
     . "      AND Condes = 'on' "
     . "      AND Conest = 'on' ";
    $res1 = mysql_query($q, $conex);
    $row1 = mysql_fetch_array($res1);
    $con3 = $row1[0];

    $q = "   SELECT distinct Mdedoc from " . $wbasedato . "_000007 "
     . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
     . "      AND Mdecon='" . $con3 . "' "
     . "      AND Mdeest='on' ";

    $res1 = mysql_query($q, $conex);
    $num1 = mysql_num_rows($res1);
    $row1 = mysql_fetch_array($res1);

    if( $num1 > 0 ){
	    $q = "   UPDATE " . $wbasedato . "_000007 "
	     . "      SET Mdeest ='off' "
	     . "    WHERE Mdenlo = '" . $codlot . "-" . $codpro . "' "
	     . "    AND Mdedoc = '" . $row1[0] . "' "
	     . "      AND Mdecon = '" . $con3 . "' "
	     . "      AND Mdeest='on' ";
	
	    $res = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HAN PODIDO ANULAR LOS MOVIMIENTOS " . mysql_error());
	
	    $q = "   UPDATE " . $wbasedato . "_000006 "
	     . "      SET Menest ='off' "
	     . "    WHERE Mendoc = '" . $row1[0] . "' "
	     . "      AND Mencon = '" . $con3 . "' ";
	    $res2 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HAN PODIDO ANULAR LOS MOVIMIENTOS " . mysql_error());
	}
} 

/**
* Pone en off el estado del lote en la tabla de almacenamiento
* 
* @param caracter $codlot codigo del lote
* @param caracter $codpro codigo del producto
*/
function anularLote($codlot, $codpro)
{
    global $conex;
    global $wbasedato;

    $q = "   UPDATE " . $wbasedato . "_000004 "
     . "      SET Ploest ='off' "
     . "    WHERE Plopro = '" . $codpro . "' "
     . "      AND Plocod = '" . $codlot . "' ";

    $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ANULAR EL LOTE " . mysql_error());
} 
// ----------------------------------------------------------funciones de presentacion------------------------------------------------
/**
* Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a los scripts consulta.php, seguimiento.php
* para consulta.php existen dos opciones mandandole el paramentro para=recibidos o para=enviados, asi ese Script consultara
* uno u otro tipo de requerimiento
* 
* Adicionalmente esta funcione se encarga de abrir la forma del Script que se llama informatica
* 
* No necesita ningun parametro ni devuelve
*/
function pintarTitulo()
{
	echo "<table ALIGN=CENTER width='50%'>"; 
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    echo "<tr><td class='titulo1'>PRODUCCION CENTRAL DE MEZCLAS</td></tr>";
    echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";

    echo "<table ALIGN=CENTER width='90%' >"; 
    // echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    echo "<tr><td class='texto5' width='15%'><a href='cen_Mez.php?wbasedato=cen_mez' style='text-decoration: none; color: black;'>PRODUCTOS</a></td>";
    echo "<td class='texto6' width='15%'><a href='lotes.php?wbasedato=lotes.php' style='text-decoration: none; color: white;'>LOTES</a></td>";
    echo "<td class='texto5' width='15%'><a href='cargoscm.php?wbasedato=lotes.php&tipo=C' style='text-decoration: none; color: black;'>CARGOS A PACIENTES</a></td>";
    echo "<td class='texto5' width='15%'><a href='pos.php?wbasedato=pos.php&tipo=A' style='text-decoration: none; color: black;'>VENTA EXTERNA</a></td></TR>"; 
    // echo "<a href='cargos.php?wbasedato=lotes.php&tipo=A'><td class='texto5' width='15%'>AVERIAS</td></a>";
    // echo "<a href='descarte.php?wbasedato=cenmez'><td class='texto5' width='15%'>DESCARTES</td></TR></a>";
    echo "<tr><td class='texto6' >&nbsp;</td>";
    echo "<td class='texto6' >&nbsp;</td>";
    echo "<td class='texto6' >&nbsp;</td>";
    echo "<td class='texto6' >&nbsp;</td></tr></table>";
} 

/**
* Despliega, la opcion de busqueda de lotes por diferentes formas, las caracterisitcas del lote:codigo, fecha de creacion,
* persona que elabora, cantidad y saldo y despliega las carateristicas del producto ingresadas con el programa de creacion
* de productos
* 
* @param caracter $forcon forma de busqueda de un lote (por codigo, por producto, por nombre del producto)
* @param vector $productos vector con la lista de productos encontrados en una busqueda por producto
*                                       en realidad solo se utiliza la primer posicion $productos[0]
* @param vector $consultas vector con los productos encontrados para una busqueda determinada, se depliegan en select
* @param caracter $codlot codigo del lote
* @param caracter $presentacion unidad minima de trabajo del producto
* @param caracter $via via de administrador del producto
* @param numerico $tfd tiempo en dias de infusion del producto
* @param numerico $tvd tiempo en dias de vencimiento del producto
* @param date $fecha fecha de creacion del producto
* @param boolean $foto si es fotosensible o no
* @param boolean $neve si se guarda en nevera o no
* @param numerico $lotcan cantidad de productos para el lote
* @param numerico $lotsal saldo de productos del lote
* @param date $feccre fecha de creacion del lote
* @param udate $fecven fecha de vencimiento del lote
* @param vector $personas vector de personas que pueden elaborar el lote, para el select
* @param boolean $pintar indica que controles despelgar si es uno da la opcion de crear lote, 2 indica que se creo y 3 cuando se anulo
* @param vector $ccos vector de centros de costos que pueden elaborar el lote
* @param unknown_type $val cantidad permitida para el lote segun las existencias de insumos
* @param boolean $existencias indica que se debe mostrar mensaje indicando que el producto ya existe para otros lotes
*/
// function pintarFormulario($forcon, $productos, $consultas, $codlot, $presentacion, $via, $tfd, $tvd, $fecha, $foto, $neve, $lotcan, $lotsal, $feccre, $fecven, $personas, $pintar, $ccos, $val, $existencias, $wips)
function pintarFormulario($forcon, $productos, $consultas, $codlot, $presentacion, $via, $tfd, $tvd, $fecha, $foto, $neve, $lotcan, $lotsal, $feccre, $fecven, $personas, $pintar, $ccos, $val, $existencias, $wips,$whistoria,$wingreso,$warticuloda,$idoda,$sinReemplazo,$wronda,$wfecharonda,$personasRevisan)
{
    global $conex;
    global $wbasedato;
    global $txHistoria;
    global $pre2;
    
	$arrayNPT = array();
	if($productos != "")
	{
		$arrayNPT = consultarSiEsNPT($productos[0]['cod']);
	}
	
    echo "<form name='producto2' action='lotes.php' method=post>";
    echo "<INPUT type='hidden' id='txHistoria' name='txHistoria'>";
    echo "<table border=0 ALIGN=CENTER width=90%>";
    
    echo "<tr><td class='titulo3' colspan='2' align='center'>Consulta de Lotes: ";
    echo "<select name='forcon' class='texto5'>";
    echo "<option>" . $forcon . "</option>";
    if ($forcon != 'Codigo del Producto')
        echo "<option >Codigo del Producto</option>";
    if ($forcon != 'Numero del lote')
        echo "<option>Numero del lote</option>";
    if ($forcon != 'Nombre comercial del Producto')
        echo "<option >Nombre comercial del Producto</option>";
    if ($forcon != 'Nombre generico del Producto')
        echo "<option >Nombre generico del Producto</option>";
    echo "</select><input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'></td></tr> ";
    echo "<tr><td class='titulo3' colspan='2' align='center'><select name='consulta' id='consulta' class='texto5' onchange='enter7()'>";

    if (is_array($consultas) && $consultas[0]['cod'] != '')
    {
        for ($i = 0;$i < count($consultas);$i++)
        {
            $ver = 'ACTIVO';

            echo "<option>" . $consultas[$i]['pro'] . "-" . $consultas[$i]['cod'] . "-" . $consultas[$i]['sal'] . "-" . $presentacion . "-" . $consultas[$i]['est'] . "</option>";
        } 
    } 
    else
    {
        echo "<option value=''></option>";
        for ($i = 1;$i < count($consultas);$i++)
        {
            $ver = 'ACTIVO';
            echo "<option>" . $consultas[$i]['pro'] . "-" . $consultas[$i]['cod'] . "-" . $consultas[$i]['sal'] . "-" . $presentacion . "-" . $consultas[$i]['est'] . "</option>";
        } 
    } 

    echo "</select>";
    echo "</td></tr>";
    echo "<INPUT TYPE='hidden' NAME='anular' VALUE='0' > ";
    // echo "</form>";

    if ($existencias)
    {
        echo "<tr><td class='titulo3' colspan='2' align='center'><font color='red'>Existen otros lotes con saldo de producto</font></td></tr>";
    } 
    echo "</table></form>";

    echo "<form name='producto' action='lotes.php' method=post>";
    echo "<INPUT type='hidden' id='txHistoria1' name='txHistoria'>";
    echo "<table border=0 ALIGN=CENTER width=90%>";
    echo "<tr><td colspan=2 class='titulo3' align='center'><b>Informacion general del Lote</b></td></tr>";

    echo "<tr><td class='texto1' colspan='1' align='center'>Numero de lote: <input type='TEXT' name='codlot' value='" . $codlot . "'  readonly='readonly' class='texto1' size='5'></td>";
    $cad = 'validarFormulario5("' . $feccre . '")';
    echo "<td class='texto1' colspan='1' align='center'>Fecha de creación: <input type='TEXT' name='feccre' value='" . $feccre . "' class='texto1'  readonly='readonly' size='10' onchange='" . $cad . "'></td></tr>";
    /**
    * if ($val==$lotcan)
    * {
    */
    echo "<tr><td class='texto1' colspan='1' align='center'>Cantidad de productos: <input type='TEXT' name='lotcan' value='" . $lotcan . "'  class='texto5' size='5' onchange='validarFormulario2()'>&nbsp;<input type='TEXT' name='pre1' value='" . $presentacion . "'  class='texto5' readonly='readonly' size='15'></td>";
    echo "<td class='texto1' colspan='1' align='center'>Saldo de productos: <input type='TEXT' name='lotsal' value='" . $lotsal . "'  class='texto1' size='5'>&nbsp;<input type='TEXT' name='pre2' value='" . $presentacion . "'  class='texto1' readonly='readonly' size='15'></td></tr>";
    /**
    * }
    * else
    * {
    * echo "<tr><td bgcolor='red' colspan='1' align='center'>Cantidad de productos: <input type='TEXT' name='lotcan' value='".$val."'  class='texto5' size='5' onchange='validarFormulario()'>&nbsp;<input type='TEXT' name='pre1' value='".$presentacion."'  class='texto5' readonly='readonly' size='15'></td>";
    * echo "<td bgcolor='red' colspan='1' align='center'>Saldo de productos: <input type='TEXT' name='lotsal' value='".$lotsal."'  class='texto1' size='5'>&nbsp;<input type='TEXT' name='pre2' value='".$presentacion."'  class='texto1' readonly='readonly' size='15'></td></tr>";
    * }
    */

    echo "<tr><td class='texto1' colspan='1' align='center'>Fecha de vencimiento: <input type='TEXT' name='fecven' value='" . $fecven . "' class='texto1' readonly='readonly' size='10'></td>";
    echo "<td class='texto1' colspan='1' align='center'>Centro de costos: <select name='cco' class='texto5'>";
    if ($ccos != '')
    {
        for ($i = 0;$i < count($ccos);$i++)
        {
            echo "<option>" . $ccos[$i] . "</option>";
        } 
    } 
    else
    {
        echo "<option value=''></option>";
    } 
    echo "</select></td></tr>";
    echo "<td class='texto1' colspan='2' align='center'>Elaborado por: <select name='persona' class='texto5'>";
    if ($personas != '')
    {
        for ($i = 0;$i < count($personas);$i++)
        {
            echo "<option>" . $personas[$i] . "</option>";
        } 
    } 
    else
    {
        echo "<option value=''></option>";
    } 
    echo "</select></td></tr>";

	
	echo "<td class='texto1' colspan='2' align='center'>Revisado por: <select name='revisado' class='texto5'>";
	if ($personasRevisan != '')
	{
		for ($i = 0;$i < count($personasRevisan);$i++)
		{
			echo "<option>" . $personasRevisan[$i] . "</option>";
		} 
	} 
	else
	{
		echo "<option value=''></option>";
	} 
	echo "</select></td></tr>";

    

    if ($pintar == 1)
    {
        echo "<tr><td colspan=2 class='titulo3' align='center'><input type='checkbox' name='crear' class='titulo3'>Crear &nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Aceptar' class='texto5' onClick='aceptarSubmit( this );'></td></tr>";
    } 
    else if ($pintar == 2)
    {
        echo "<td class='texto1' colspan='2' align='center'>Impresora: <select name='wip' id='wip' class='texto5'>";
        
        $sql = "SELECT
        			Tipimp
        		FROM
        			{$wbasedato}_000002 a,
        			{$wbasedato}_000001 b	
        		WHERE
        			artcod = '{$productos[0]['cod']}'
        			AND tipcod = arttip
        		";
        			
        $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        $numrows = mysql_num_rows( $res );
        
        $codImp = "";
        
        if( $numrows > 0 ){
        	$codImp = mysql_fetch_array( $res );
        }
        
        if ($wips != '')
        {
            for ($i = 0;$i < count($wips);$i++)
            {
            	list( $tempImp ) = explode( "-", $wips[$i] );
            	
            	if( !empty($codImp[0]) && trim( $tempImp ) == $codImp[0] ){
                	echo "<option selected>" . $wips[$i] . "</option>";
            	}
            	else{
            		echo "<option>" . $wips[$i] . "</option>";
            	}
            } 
        } 
        else
        {
            echo "<option value=''></option>";
        } 
        echo "</select></td></tr>";

        $exp = explode('-', $wips[0]);

        $q = " SELECT Artdes, Arttip, Tipiar "
         . "       FROM " . $wbasedato . "_000002, {$wbasedato}_000001 "
         . "    WHERE Artcod = '" . $productos[0]['cod'] . "' "
         . "       AND Arttip = tipcod "
         . "       AND Artest = 'on' ";

        $res = mysql_query($q, $conex);
        $row = mysql_fetch_array($res);
        $des = explode('-', $row[0]);
        if (isset($des[1]))
        {
            $nombre = $productos[0]['nom'] . ' ' . $des[1];
        } 
        else
        {
            $nombre = $productos[0]['nom'];
        } 

		$imp=$lotsal*2;
        $cadena = 'imprimir("etq_socket.php?wlot=' . $codlot . '&wnom=' . $nombre . '&wcod=' . $productos[0]['cod'] . '&wfev=' . $fecven . '&wetq=' . $imp .'")';
        
        /***************************************************************************************************************************************
         * Creando impresion para articulos de alto riegos 
         ***************************************************************************************************************************************/
        if( $row[2] == 'on' ){
			
			if( $txHistoria != '' ){
		        //Buscando el cco
		        $sql = "SELECT
		        			ubisac, ubihis, ubiing*1
		        		FROM
		        			movhos_000018
		        		WHERE
		        			ubihis = '{$txHistoria}'
		        		ORDER BY
		        			2,3 desc
		        		"; //echo "........<pre>$sql</pre>";
		        
//		        $sql = "SELECT
//		        			Habcod
//		        		FROM
//		        			movhos_000020
//		        		WHERE
//		        			habhis = '{$txHistoria}'
//		        		"; //echo "........<pre>$sql</pre>";
		        			
		        $resHis = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		        $numrowsHis = mysql_num_rows( $resHis );
		        $rowHis = mysql_fetch_array($resHis);
			}
			else{
				$rowHis[0] = '';
			}
			//echo "$ccos[0]";
			$expCco = explode( "-", $ccos[0] );
	        
	        $cadena2 = "imprimirEtiquetasAltoRiesgo( \"rotuloElectrolitos.php?wemp_pmla=01&whis={$txHistoria}&warticulo={$productos[0]['cod']}&wlote=$codlot&wcco={$expCco[0]}\" )";
	        
	        $msj = "No se encuentra la historia";
	        if( @$numrowsHis > 0 ){
	        	$msj = "Historia Encontrada";
	        }
	        
	        
	        echo "<tr><td align='center' colspan='2' class='texto1'>Historia: <INPUT Type='text' class='texto5' id='idHis' value='".@$txHistoria."'>&nbsp;&nbsp;$msj</td></tr>";
	        if( $txHistoria != '' && $numrowsHis > 0 ){
	        	echo "<tr><td colspan=2 class='titulo3' align='center'>&nbsp;&nbsp;<input type='button' name='anular' value='Anular' onclick='enter4()'>&nbsp;&nbsp;<input type='button' name='porque' value='Imprimir' onclick='" . $cadena2 . "'></td></tr>";
	        }
	        else{
	        	echo "<tr><td colspan=2 class='titulo3' align='center'>&nbsp;&nbsp;<input type='button' name='anular' value='Anular' onclick='enter4()'>&nbsp;&nbsp;<input type='button' name='porque' value='Validar Historia' onclick='validarHis2(idHis.value, \"{$productos[0]['cod']}-$codlot-$lotsal-$pre2-{$productos[0]['nom']}\");' style='display:'></td></tr>";
	        }
	        
		}else{
			$uno = $productos[0]['cod'];
			$cadena2 = 'enter9("' . $uno . '-' . $codlot . '")';
			
			
			$botonImprimir = "<input type='button' name='porque' value='Imprimir' onclick='" . $cadena . "'>";
			$botonCargos = "";
			
			// Cargo sin reemplazo (solo cuando es DA o NPT), indica si reemplaza o no desde el monitor de medicamentos x ronda
			if($sinReemplazo=="on")
			{
				$servicioPaciente =  consultarServicioPaciente($whistoria,$wingreso);
				
				if($whistoria!="" && $wingreso!="") // validacion para Cargar sin reemplazar DA y NPT
				{
					if($warticuloda!="" && $idoda!="") // es DA
					{
						$cadena = 'etq_socket.php?wlot=' . $codlot . '&wnom=' . $nombre . '&wcod=' . $productos[0]['cod'] . '&wfev=' . $fecven . '&wetq=' . $imp .'&whistoria='.$whistoria.'&wingreso='.$wingreso.'&wronda='.$wronda.'&wfecharonda='.$wfecharonda."&wido=".$idoda;
						$botonImprimir = "<input type='button' id='CargosDA' name='CargosDA' value='Imprimir Sticker y cargar DA al paciente' onclick='cargarDAaPaciente(\"".$whistoria."\",\"".$wingreso."\",\"".$productos[0]['cod']."\",\"".$idoda."\",\"".$warticuloda."\",\"".$codlot."\",\"".$ccos[0]."\",\"".$servicioPaciente."\",\"off\",\"".$cadena."\",\"".$wbasedato."\",\"".$wronda."\",\"".$wfecharonda."\")'>";
						$botonCargos="";
					}
					else // es NPT
					{
						$botonImprimir = "<input type='button' name='porque' value='Imprimir' onclick='" . $cadena . "'>";
						$botonCargos="<input type='button' id='CargosDA' name='CargosDA' value='Cargar al paciente' onclick='cargarProductoaPaciente(\"".$whistoria."\",\"".$wingreso."\",\"".$productos[0]['cod']."\",\"".$codlot."\",\"".$ccos[0]."\",\"".$servicioPaciente."\")'>";
					}
				}
				else //Impresion normal y sin cargos, ya que no hay relacion de historia e ingreso
				{
					$botonImprimir = "<input type='button' name='porque' value='Imprimir' onclick='" . $cadena . "'>";
					$botonCargos="";
				}
			}
			else
			{
				
				if($whistoria!="" && $wingreso!="") // validacion para DA, cuando las NPT son con reemplazo no se envía la historia ni ingreso ya que al ser unicas se consulta en movhos_000214
				{
					$cadena = 'etq_socket.php?wlot=' . $codlot . '&wnom=' . $nombre . '&wcod=' . $productos[0]['cod'] . '&wfev=' . $fecven . '&wetq=' . $imp .'&whistoria='.$whistoria.'&wingreso='.$wingreso.'&wronda='.$wronda.'&wfecharonda='.$wfecharonda;
					$botonImprimir = consultarSiEsDA($whistoria,$wingreso,$productos[0]['cod'],$codlot,$ccos[0],$warticuloda,$idoda,$cadena,$wbasedato,$wronda,$wfecharonda);
					$botonCargos="";
				}
				else
				{
					$botonImprimir = "<input type='button' name='porque' value='Imprimir' onclick='" . $cadena . "'>";
					$botonCargos = pintarBotonNPT($productos[0]['cod'],$codlot,$ccos[0]);
					// $botonCargos = consultarSiEsNPT($productos[0]['cod'],$codlot,$ccos[0]);
				}
			}
			
			
			echo "<tr><td colspan=2 class='titulo3' align='center'>EL LOTE HA SIDO CREADO EXITOSAMENTE &nbsp;&nbsp;<input type='button' name='anular' value='Anular' onclick='" . $cadena2 . "'>&nbsp;&nbsp;".$botonImprimir."&nbsp;&nbsp;".$botonCargos."</td></tr>";	
			
			// si es nutricion boton para imprimir el rotulo npt
			
			// $arrayNPT = consultarSiEsNPT($codigoProducto);
			
			if(count($arrayNPT)>0)
			{
				$rotuloNPT = "<a href='rotulo2.php?historia=".$arrayNPT['Enuhis']."&codigo=".$productos[0]['cod']."' target='new'>";
				if($arrayNPT['Enuord']=="on")
				{
					$rotuloNPT = "<a href='rotuloNPT.php?wemp_pmla=01&historia=".$arrayNPT['Enuhis']."&codigo=".$productos[0]['cod']."' target='new'>";
				}
				
				echo "<tr><td colspan=2 class='titulo3' align='center'>".$rotuloNPT."IMPRIMIR ROTULO</a></td></tr>";
			}
			else
			{
				// consultar si es nutricion (esta validación aplica para las nutriciones para instituciones diferentes a 01: Clínica Las Américas)
				$datosNutricion = consultarDatosNutricion($productos[0]['cod']);
				if(count($datosNutricion)>0)
				{
					$rotuloNPT = "<a href='rotulo2.php?historia=".$datosNutricion['historia']."&codigo=".$productos[0]['cod']."&horas=".$datosNutricion['tiempoInfusion']."&insti=".$datosNutricion['institucion']."' target='new'>";
					echo "<tr><td colspan=2 class='titulo3' align='center'>".$rotuloNPT."IMPRIMIR ROTULO</a></td></tr>";
				}
			}				
			
        }
        /***************************************************************************************************************************************/
        
        
    } 
    else if ($pintar == 3)
    {
        echo "<tr><td colspan=2 class='titulo3' align='center'>EL LOTE HA SIDO ANULADO EXITOSAMENTE</td></tr>";
    } 
    else if ($pintar == 4)
    {
        echo "<tr><td colspan=2 class='titulo3' align='center'>LOTE ANULADO</td></tr>";
    } 
    else
    {
        echo "<td class='texto1' colspan='2' align='center'>Impresora: <select name='wip' id='wip' class='texto5'>";
        
        $artcod_tmp = '';
        if (is_array($productos))
        { $artcod_tmp = $productos[0]['cod']; }
        
     	$sql = "SELECT
        			Tipimp
        		FROM
        			{$wbasedato}_000002 a,
        			{$wbasedato}_000001 b	
        		WHERE
        			artcod = '{$artcod_tmp}'
        			AND tipcod = arttip
        		";
        			
        $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        $numrows = mysql_num_rows( $res );
        
        $codImp = "";
        
        if( $numrows > 0 ){
        	$codImp = mysql_fetch_array( $res );
        }
        
        if ($wips != '')
        {
            for ($i = 0;$i < count($wips);$i++)
            {
            	list( $tempImp ) = explode( "-", $wips[$i] );
            	
            	if( !empty($codImp[0]) && trim( $tempImp ) == $codImp[0] ){
                	echo "<option selected>" . $wips[$i] . "</option>";
            	}
            	else{
            		echo "<option>" . $wips[$i] . "</option>";
            	}
            } 
        } 
        else
        {
            echo "<option value=''></option>";
        } 
        $exp = explode('-', $wips[0]);
        echo "</select></td></tr>";

		$artcod_tmp = '';
        if (is_array($productos))
        { $artcod_tmp = $productos[0]['cod']; }
        
        $q = " SELECT Artdes, Arttip, Tipiar "
         . "       FROM " . $wbasedato . "_000002, {$wbasedato}_000001 "
         . "    WHERE Artcod = '" .$artcod_tmp. "' "
         . "       AND Arttip = tipcod "
         . "       AND Artest = 'on' ";

        $res = mysql_query($q, $conex);
        $row = mysql_fetch_array($res);
        $des = explode('-', $row[0]);
        if (isset($des[1]))
        {
            $nombre = substr($productos[0]['nom'],0,21) . ' ' . $des[1];
        } 
        else
        {
			$nombre = '';
			if (is_array($productos))
			{ $nombre = $productos[0]['nom']; }
        } 

		$imp=$lotsal*2;
		
		
		/***************************************************************************************************************************************
         * Creando impresion para articulos de alto riegos 
         ***************************************************************************************************************************************/
		
		if( $row[2] == 'on' ){
			
			if( $txHistoria != '' ){
		        //Buscando el cco
		        $sql = "SELECT
		        			ubisac, ubihis, ubiing*1
		        		FROM
		        			movhos_000018
		        		WHERE
		        			ubihis = '{$txHistoria}'
		        		ORDER BY
		        			2,3 desc
		        		"; //echo "........<pre>$sql</pre>";
		        
		        $sql = "SELECT
		        			Habcod
		        		FROM
		        			movhos_000020
		        		WHERE
		        			habhis = '{$txHistoria}'
		        		"; //echo "........<pre>$sql</pre>";
		        			
		        $resHis = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		        $numrowsHis = mysql_num_rows( $resHis );
		        $rowHis = mysql_fetch_array($resHis);
			}
			else{
				$rowHis[0] = '';
			}
	        
			$expCco = explode( "-", $ccos[0] );
	        $cadena2 = "imprimirEtiquetasAltoRiesgo( \"rotuloElectrolitos.php?wemp_pmla=01&whis={$txHistoria}&warticulo={$productos[0]['cod']}&wlote=$codlot&wcco={$expCco[0]}\" )";
	        
	        $msj = "No se encuentra la historia";
	        if( @$numrowsHis > 0 ){
	        	$msj = "Historia Encontrada";
	        }
	        
	        
	        echo "<tr><td align='center' colspan='2' class='texto1'>Historia: <INPUT Type='text' class='texto5' id='idHis' value='".@$txHistoria."'>&nbsp;&nbsp;$msj</td></tr>";
	        if( $txHistoria != '' && $numrowsHis > 0 ){
	        	echo "<tr><td colspan=2 class='titulo3' align='center'>&nbsp;&nbsp;<input type='button' name='anular' value='Anular' onclick='enter4()'>&nbsp;&nbsp;<input type='button' name='porque' value='Imprimir' onclick='" . $cadena2 . "'></td></tr>";
	        }
	        else{
	        	echo "<tr><td colspan=2 class='titulo3' align='center'>&nbsp;&nbsp;<input type='button' name='anular' value='Anular' onclick='enter4()'>&nbsp;&nbsp;<input type='button' name='porque' value='Validar Historia' onclick='validarHis(idHis.value);' style='display:'></td></tr>";
	        }
	        
		}else{
			$wcod_tmp = '';
			if (is_array($productos))
			{ $wcod_tmp = $productos[0]['cod'];}
			$cadena = 'imprimir("etq_socket.php?wlot=' . $codlot . '&wnom=' . $nombre . '&wcod=' .$wcod_tmp. '&wfev=' . $fecven . '&wetq=' . $imp .'")';
        	echo "<tr><td colspan=2 class='titulo3' align='center'>&nbsp;&nbsp;<input type='button' name='anular' value='Anular' onclick='enter4()'>&nbsp;&nbsp;<input type='button' name='porque' value='Imprimir' onclick='" . $cadena . "'></td></tr>";	
		}
        /***************************************************************************************************************************************/
		
		
        
    } 

    echo "</table><table border=0 ALIGN=CENTER width=90%></br>";

	$datos = array('cod'=>'','nom'=>'','gen'=>'');
	if (is_array($productos))
	{
		$datos['cod'] = $productos[0]['cod'];
		$datos['nom'] = $productos[0]['nom'];
		$datos['gen'] = $productos[0]['gen'];
	}

    echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general del Producto</b></td></tr>";
    echo "<tr><td class='texto2' colspan='1' align='left'>Codigo del producto: <input type='TEXT' name='codpro' align='center' value='" . $datos['cod'] . "'  readonly='readonly' class='texto2' size='10'></td>";
    echo "<td class='texto2' colspan='2' align='left'>Nombre comercial: <input type='TEXT' name='nompro' value='" . $datos['nom'] . "' readonly='readonly' class='texto2' size='50'></td></tr>";
    echo "<tr><td class='texto2' colspan='2' align='left'>Nombre genérico: <input type='TEXT' name='genpro' value='" . $datos['gen'] . "' readonly='readonly' class='texto2' size='50'></td>";
    echo "<td class='texto2' colspan='1' align='left'>Presentacion: <input type='TEXT' name='presentacion' value='" . $presentacion . "' readonly='readonly' class='texto2' ></td></tr>";
    echo "<tr><td class='texto2' colspan='1' align='left'>Vía de administración: <input type='TEXT' name='via' value='" . $via . "' readonly='readonly' class='texto2' ></td>";
    echo "<td class='texto2' colspan='1' align='left'>Tiempo de infusión: <input type='TEXT' name='tfd' value='" . $tfd . "' readonly='readonly' class='texto2' size='10'>&nbsp; min</td>";
    echo "<td class='texto2' colspan='1' align='left'>Tiempo de vencimiento: <input type='TEXT' name='tvd' value='" . $tvd . "' readonly='readonly' class='texto2' size='10'>&nbsp; dias</td></tr>";
    echo "<tr><td class='texto2' colspan='1' align='left'>Fecha de creacion: <input type='TEXT' name='fecha' value='" . $fecha . "' readonly='readonly' class='texto2'></td>";
    if ($foto == 'on')
    {
        echo "<td class='texto2' colspan='1' align='left'>Material fotosensible: <input type='checkbox' name='foto' value='on'  checked class='texto2' size='5'></td>";
    } 
    else
    {
        echo "<td class='texto2' colspan='1' align='left'>Material fotosensible <input type='checkbox' name='foto' value='on'  class='texto2'></td>";
    } 
    if ($neve == 'on')
    {
        echo "<td class='texto2' colspan='1' align='left'>Conservar en nevera: <input type='checkbox' name='neve' value='on' checked class='texto2'></td></tr>";
    } 
    else
    {
        echo "<td class='texto2' colspan='1' align='left'>Conservar en nevera: <input type='checkbox' name='neve' value='on' class='texto2'></td></tr>";
    } 

    if ($pintar and $pintar < 3)
    {
        echo "<INPUT TYPE='hidden' NAME='pintar' VALUE='1' > ";
    } 

    echo "</table></br>";
} 

function pintarAdaptacion($productosA, $lotesA, $canA)
{
    echo "<table border=0 ALIGN=CENTER width=90%>";
    echo "<tr><td colspan='6' class='titulo3' align='center'><b>Producto para adaptar</b></td></tr>";

    echo "<tr><td class='texto1' colspan='6' align='center'>Buscar producto por: ";
    echo "<select name='forbusA' class='texto5'>";
    echo "<option>Rotulo</option>";
    echo "<option>Codigo</option>";
    echo "<option>Nombre comercial</option>";
    echo "<option>Nombre generico</option>";
    echo "</select><input type='TEXT' name='parbusA' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar'  class='texto5'></td> ";
    echo "<tr><td class='texto1' colspan='4' align='center'>Producto: <select name='productoA' class='texto5' onchange='enter1()'>";

    if ($productosA != '')
    {
        for ($i = 0;$i < count($productosA);$i++)
        {
            $exp = explode('-', $productosA[$i]);
            echo "<option>" . $exp[0] . "-" . $exp[1] . "</option>";
        } 
    } 
    else
    {
        echo "<option ></option>";
    } 
    echo "</select></td>";

    echo "<td class='texto1' colspan='2' align='center'>Lote: <select name='loteA' class='texto5' onchange='enterA()'>";
    if (is_array($lotesA))
    {
        for ($i = 0;$i < count($lotesA);$i++)
        {
            echo "<option>" . $lotesA[$i] . "</option>";
        } 
    } 
    else
    {
        echo "<option ></option>";
    } 

    echo "</select>&nbsp; Cantidad:<input type='TEXT' name='canA' value='" . $canA . "' size=10 class='texto5' onchange='enter1()'></td></tr> ";

    echo "</table></br>";
}

/**
* pinta la lista de insumos que componen el producto, la cantidad requerida de insumo para el producto, la cantidad requerida de 
* insumo para el lote, las presentaciones que forman el insumo, la existencia de cada presentacion, la fecha de vencimiento de la porcion
* destapada de insumo y la cantidad que se utilizara de esa presentacion en el lote.
* 
* @param vector $inslis lista de insumos que componene el producto y sus cantidades para el lote
* @param vector $unidades lista de presentaciones existentes por insumo
* @param vector $cantidades1 cantidades que se utilizaran de cada presentacion para preparar el lote
* @param boolean $pintar indica si se debe pintar o no las existencias de cada presentacion (solo cuando se esta creando)
*/
function pintarInsumos($inslis, $unidades, $cantidades1, $pintar)
{
    echo "<table border=0 ALIGN=CENTER width=90%>";
    if ($pintar and $pintar < 3)
    {
        echo "<tr><td colspan=7 class='titulo3' align='center'><b>Informacion detallada del Lote</b></td></tr>";
    } 
    else
    {
        echo "<tr><td colspan=5 class='titulo3' align='center'><b>Informacion detallada del Lote</b></td></tr>";
    } 
    if ($inslis != '')
    {
        echo "<tr><td class='texto2' colspan='1' align='center'>Insumo</td>";
        echo "<td class='texto2' colspan='1' align='center'>Cantidad/Producto</td>";
        echo "<td class='texto2' colspan='1' align='center'>Cantidad/Lote</td>";
        echo "<td class='texto2' colspan='1' align='center'>Presentacion</td>";
        if ($pintar and $pintar < 3)
        {
            echo "<td class='texto2' colspan='1' align='center'>Existencias</td>";
            echo "<td class='texto2' colspan='1' align='center'>Descartar</td>";
        } 
        echo "<td class='texto2' colspan='1' align='center'>Consumo/lote</td></tr>";

        for ($i = 0;$i < count($inslis);$i++)
        {
            if (is_int($i / 2))
            {
                $class = 'texto1';
            } 
            else
            {
                $class = 'texto4';
            } 

            $rowspan = count($unidades[$i]);
            echo "<tr><td class='" . $class . "' colspan='1' rowspan='" . $rowspan . "' align='center'>" . $inslis[$i]['cod'] . "-" . $inslis[$i]['nom'] . "</td>";
            $exp1 = explode('-', $inslis[$i]['pre']);
            echo "<td class='" . $class . "' colspan='1' rowspan='" . $rowspan . "' align='center'>" . $inslis[$i]['can'] . "-" . $exp1[0] . "</td>";
            if ($inslis[$i]['est'] == 'on')
            {
                echo "<td class='" . $class . "' colspan='1' rowspan='" . $rowspan . "' align='center'><input type='TEXT' size='5' readonly='readonly' name='lot' value='" . $inslis[$i]['lot'] . "'  class='texto3'>" . $exp1[0] . "</td>";
            } 
            else
            {
                echo "<td bgcolor='red' colspan='1' rowspan='" . $rowspan . "' align='center'><input type='TEXT' size='5' readonly='readonly' name='lot' value='" . $inslis[$i]['lot'] . "'  class='texto3'>'" . $exp1[0] . "</td>";
            } 

            if ($unidades[$i] != '')
            {
                for ($j = 0;$j < count($unidades[$i]);$j++)
                {
                    if ($j != 0)
                    {
                        echo "<tr>";
                    } 
                    $exp = explode('-', $unidades[$i][$j]);
                    echo "<td class='" . $class . "' colspan='1' align='center'>" . $exp[0] . "-" . $exp[1] . "</td>";
                    if ($pintar and $pintar < 3)
                    {
                        echo "<td class='" . $class . "' colspan='1' align='center'>(" . ($exp[5] * $exp[3] + $exp[4]) . " " . $exp1[0] . ") " . $exp[3] . " UN-" . $exp[4] . " FR</td>";
                        echo "<td class='" . $class . "' colspan='1' align='center'><a href='descarte.php?parbus2=" . $exp[0] . "&forbus2=rotulo&canti=" . $exp[4] . "' target='new'>" . $exp[6] . "-" . $exp[7] . "-" . $exp[8] . "</a></td>";
                    } 

                    if (($cantidades1[$i][$j] == '' or $cantidades1[$i][$j] == 0)and count($unidades[$i]) == 1)
                    {
                        echo "<td class='" . $class . "' colspan='1' align='center'><input type='TEXT' size='5'  name='cantidades1[" . $i . "][" . $j . "]' value='" . $inslis[$i]['lot'] . "'  class='texto3'>" . $exp1[0] . "</td>";
                    } 
                    else
                    {
                        echo "<td class='" . $class . "' colspan='1' align='center'><input type='TEXT' size='5'  name='cantidades1[" . $i . "][" . $j . "]' value='" . $cantidades1[$i][$j] . "'  class='texto3'>" . $exp1[0] . "</td>";
                    } 
                    echo "</tr>";
                } 
            } 
            else
            {
                echo "<td class='" . $class . "' colspan='1' align='center'>&nbsp;</td>";
                echo "<td class='" . $class . "' colspan='1' align='center'>&nbsp;</td>";
                echo "</tr>";
            } 
        } 
    } 
    echo "</table></br></form>";
}  

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
$noEsAjax = false;
if(isset($accion)) 
{	
	switch($accion)
	{
		case 'aprobarNPT':
			echo aprobarNPT($historia,$ingreso,$NPTgenerica,$ido,$NPTreemplazada);
			break;
		case 'aprobarDA':
			echo aprobarDA($historia,$ingreso,$codArticulo,$ido,$codDA,$cco,$wbasedato,$servicioPaciente,$wronda,$wfecharonda);
			break;
		case 'consultarRondaPreparacionDA':
			echo consultarRondaPreparacionDA($historia,$ingreso,$codDA,$ido,$cco);
			break;
		default: 
			$noEsAjax = true;
			break;
	}
}
else
{
	$noEsAjax = true;
}

if($noEsAjax)
{
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	

?>

<head>
  <title>APLICACION DE CENTRAL DE MEZCLAS</title>
  
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	
	 
	 
	 
	
	<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
	
	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	 
  
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>
  
   <script type="text/javascript">
   document.onkeydown = mykeyhandler; 
   
   function aceptarSubmit( campo ){
	campo.disabled = true;
	document.producto.submit();
   }
   
   
function imprimirStickerPaciente(historia,ronda,codCco)
{
	var impresora = $("#wip").val();
	impresora = impresora.split("-");
	
	$.post("../../movhos/reportes/stickers_Dispensacion.php",
		{
			consultaAjax:   	'stick_historia',
			wemp_pmla:      	"01",
			wbasedato:          "movhos",
			whis:       		historia,
			whora_par_actual:   ronda,
			wccoo: 				codCco,
			wipimpresora  :     impresora[2],
			wcant: 			    "1"
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
function imprimirOrden(path)
{
	window.open(path,'_blank','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
}
   
	
function cargarProductoaPaciente(historia,ingreso,codProducto,lote,codCco,servicio)
{
	window.open ("cargocpx.php?cod="+codProducto+"&cco="+codCco+"&var="+lote+"&historia="+historia+"&ingreso="+ingreso+"&servicio="+servicio+"&carro=off","_self")
}

function cargarDAaPaciente(historia,ingreso,codArticulo,ido,codDA,lote,codCco,servicio,reemplazar,cadena,wbasedato,wronda,wfecharonda)
{
	var wemp_pmla = "01";
	var orden = "desc";
	var origen = "";
	var impMedicamentos = "medtos";
	
	
	if(reemplazar=="on")
	{
		//aprobar en perfil
		$.post("lotes.php",
		{
			consultaAjax 	: '',
			accion			: 'aprobarDA',
			historia		: historia,
			ingreso			: ingreso,
			codArticulo		: codArticulo,
			ido				: ido,
			codDA			: codDA,
			cco				: codCco,
			wbasedato		: wbasedato,
			servicioPaciente: servicio,
			wronda			: wronda,
			wfecharonda			: wfecharonda
		}
		, function(data) {
			
			alert(data.mensaje);
			if(data.error==0)
			{
				// var path = "/matrix/HCE/procesos/ordenes_imp.php?wemp_pmla="+ wemp_pmla +"&whistoria=" + historia + "&wingreso=" +ingreso+ "&tipoimp=imp&alt=off&pacEps=off&wtodos_ordenes=on&orden=" +orden+ "&origen=" +origen+ ""+"&arrOrden="+impMedicamentos+"&desdeImpOrden=on"; 
				// imprimirOrden(path);
				
				cadena = cadena + "&wido=" +data.ido;
				imprimir(cadena);
				window.open ("cargocpx.php?cod="+codDA+"&cco="+codCco+"&var="+lote+"&historia="+historia+"&ingreso="+ingreso+"&servicio="+servicio+"&carro=off","_self");
			}
			
		},'json');
	}
	else
	{
		// var path = "/matrix/HCE/procesos/ordenes_imp.php?wemp_pmla="+ wemp_pmla +"&whistoria=" + historia + "&wingreso=" +ingreso+ "&tipoimp=imp&alt=off&pacEps=off&wtodos_ordenes=on&orden=" +orden+ "&origen=" +origen+ ""+"&arrOrden="+impMedicamentos+"&desdeImpOrden=on"; 
		// imprimirOrden(path);
		
		imprimir(cadena);
		window.open ("cargocpx.php?cod="+codDA+"&cco="+codCco+"&var="+lote+"&historia="+historia+"&ingreso="+ingreso+"&servicio="+servicio+"&carro=off","_self");
	}
	
	imprimirStickerPaciente(historia,wronda,codCco);
	
}
   
function cargarNPTAPaciente(historia,ingreso,NPTgenerica,ido,NPTreemplazada,lote,codCco,servicio)
{
	//aprobar en perfil
	$.post("lotes.php",
	{
		consultaAjax 	: '',
		accion			: 'aprobarNPT',
		historia		: historia,
		ingreso			: ingreso,
		NPTgenerica		: NPTgenerica,
		ido				: ido,
		NPTreemplazada	: NPTreemplazada
	}
	, function(data) {
		
		alert(data.mensaje);
		if(data.error==0)
		{
			window.open ("cargocpx.php?cod="+NPTreemplazada+"&cco="+codCco+"&var="+lote+"&historia="+historia+"&ingreso="+ingreso+"&servicio="+servicio+"&carro=off","_self")
		}
		
	},'json');
	
}
   
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



function deshabilitar_teclas()
//document.onkeydown = function()
  { 
	if(window.event && window.event.keyCode == 116 )
	  {
	   window.event.keyCode = 505; 
	  }
	if(window.event && window.event.keyCode == 505)
	  { 
	   return false; 
	  } 
 }
   
   
	function validarHis( his ){

		document.producto2.txHistoria.value = his;
		
		enter7();
	}

	function validarHis2( his, con ){

		document.producto.txHistoria1.value = his;

		document.producto2.consulta.options[0].value = con;
		
		validarHis( his );
		//enter1();
	}
   
   function enter()
   {
   	document.informatica.resreq.options[document.informatica.resreq.selectedIndex].text='';
   	document.informatica.submit();
   }

   function enter1()
   {
   	document.producto.submit();
   }

   function enterA()
   {
   	document.producto.canA.value='';
   	document.producto.submit();
   }

   function enter7()
   {
   	document.producto2.submit();
   }

   function enter4()
   {
   	document.producto2.anular.value=1;
   	document.producto2.submit();
   }

   function enter9(valor)
   {

   	document.producto2.anular.value=1;
   	document.producto2.consulta.options[document.producto2.consulta.selectedIndex].value=valor;
   	document.producto2.submit();
   }

   function imprimir(cadena)
   {
	   var slWip=document.getElementById( "wip" );

	   var val = slWip.options[ slWip.selectedIndex ].text.split( "-" );
	   
	   cadena = cadena+"&wip="+val[2];
   	open(cadena);
   }

   function imprimirEtiquetasAltoRiesgo(cadena)
   {
	   //var val = slWip.options[ slWip.selectedIndex ].text.split( "-" );
	   
	   //cadena = cadena+"&wip="+val[2];
   		open(cadena);
   }

   function validarFormulario()
   {
   	textoCampo = window.document.producto.lotcan.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.lotcan.value = textoCampo;
   }

   function validarFormulario2()
   {
		textoCampo = window.document.producto.lotcan.value;
		textoCampo = validarEntero(textoCampo);
		
		if($("#warticuloda").val()!==undefined && textoCampo!="1")
		{
			alert('Para las dosis adaptadas la cantidad del lote debe ser 1');
			textoCampo = "1";
		}
		
		window.document.producto.lotcan.value = textoCampo;
		document.producto.submit();
   }

   function validarFormulario5(valor)
   {
   	textoCampo = window.document.producto.feccre.value;
   	textoCampo = validarFecha(textoCampo);
   	if (!textoCampo)
   	{
   		window.document.producto.feccre.value = valor;
   	}
   }

   function validarEntero(valor)
   {
   	if (isNaN(valor))
   	{
   		alert('Debe ingresar un numero entero');
   		return '';
   	}else
   	{
   		return valor;
   	}
   }

   function validarFecha(Fecha)
   {
   	if (Fecha.length!=10)
   	{
   		alert('Fecha invalida');
   		return false;
   	}
   	else
   	{
   		// Cadena Año
   		Ano= Fecha.substring(0,Fecha.indexOf("-"));

   		// Cadena Mes
   		Mes= Fecha.substring(Fecha.indexOf("-")+1,Fecha.lastIndexOf("-"));

   		// Cadena Día
   		Dia= Fecha.substring(Fecha.lastIndexOf("-")+1,Fecha.length);

   		// Valido el año
   		if (isNaN(Ano) || Ano.length<4 || parseFloat(Ano)<1900)
   		{
   			alert('Año inválido');
   			return false;
   		}

   		// Valido el Mes
   		if (isNaN(Mes) || parseFloat(Mes)<1 || parseFloat(Mes)>12)
   		{
   			alert('Mes inválido');
   			return false;
   		}

   		// Valido el Dia
   		if (isNaN(Dia) || parseInt(Dia, 10)<1 || parseInt(Dia, 10)>31)
   		{
   			alert('Día inválido');
   			return false;
   		}
   	}
   	return true   ;
   }
    </script>
  
</head>

<body oncontextmenu='return false'>

<?php


/**
* ===========================================================================================================================================
*/
/**
* =========================================================PROGRAMA==========================================================================
*/

//2010-06-03
//$codigoDes = '';
//$consecutivoDes = '';
//fin de 2010-06-03

session_start();

if (!isset($user))
{
    if (!isset($_SESSION['user']))
        session_register("user");
} 

if (!isset($_SESSION['user']))
    echo "error";
else
{
	
	// include_once("root/comun.php");

    $wbasedato = 'cenpro';
    
    // include_once( "conex.php" );
    // // include_once( "movhos/kardex.inc.php" );
// //    $conex = obtenerConexionBD("matrix");
// //    $conex = mysql_connect('localhost', 'root', '')
// //    or die("No se ralizo Conexion");
    // 


    include_once("cenpro/funciones.php"); 
    // pintarVersion(); //Escribe en el programa el autor y la version del Script.
	$wactualiz = "Octubre 30 de 2019";
	encabezado("PRODUCCION CENTRAL DE MEZCLAS",$wactualiz,"clinica");
    pintarTitulo(); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts    
    // consulto los datos del usuario de la sesion
    $pos = strpos($user, "-");
    $wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario    
    // consulto los centros de costos que crean lotes o producen en matrix (movhos_00011)
    // si ya se ha seleccionado un centro de costos, se manda a funcion para que quede de primero en el select
    if (isset($cco))
    {
        $ccos = consultarCcos($cco);
    } 
    else // si no existe aun $cco, se carga como el primer centro de la lista de centros de costos
        {
            $ccos = consultarCcos('');
        $cco = $ccos[0];
    } 
    // si nos pasan un parametro de busqueda de lote, como numero o codigo del producto, se consultan los lotes para esa forma de busqueda
    // con el parametro ingresado
    if (isset($parcon) and $parcon != '')
    {
        $consultas = consultarLotes($parcon, $forcon);
        $consulta = $consultas[0];
    } 
    // El programa cuando se llama desde cenmez.php es invocado pasandole el parametro pintar=1, lo que indica que
    // se le pasa como parametro de busqueda de lote, el codigo del producto
    // en caso de que no se pase pintar, es porque aun no se tiene definido el parametro de busqueda de lote, en este caso
    // el usuario debe ingresar un parametro de busqueda.
    if (isset($pintar) and $pintar == 1)
    { 
        // ya se han encontrado un lotes segun el codigo del producto
        if (isset($consultas) and $consultas)
        { 
            // revisamos si de la busqueda existen lotes con saldo mayor a cero
            // se setea la variable existencia porque mas tarde el sistema debe avisar
            // debe avisar que ya existen lotes activos para el producto
            $contador = 0;
            for ($i = 0; $i < count($consultas); $i++)
            {
                $consultas2[$i + 1] = $consultas[$i];
                /**
                * If ($consultas[$i]['sal']>0 and $consultas[$i]['est']=='on')
                * {
                */
                $contador++; 
                // }
            } 
            $consultas = $consultas2;
            $consultas[0] = '';
            if ($contador > 0)
            {
                $existencias = 1;
            } 
        } 
        // si aun no existe el codigo del producto, debe consultarse la infomacion del producto, el codigo del producto
        // se pasa en $parcon
        if (!isset($codpro))
        {
            consultarProducto($parcon, $via, $tfd, $tfh, $tvd, $tvh, $fecha, $inslis, $tippro, $estado, $foto, $neve, $conpro, $nompro, $genpro, $presentacion);
            $productos[0]['cod'] = $parcon;
            $codlot = ''; 
            // se incrementa el consecutivo del lote que se guarda por producto
            // si esta setiado crear no se incrementa porque esto se realiza durante la creacion
            if (!isset($crear))
            {
                $conpro = $conpro + 1;
            } 
            // el consecutivo se organiza de manera que conste de 5 digitos
            // que pasa si el numero ya supera los 5 digitos?
            for ($i = 0; $i < (6 - strlen($conpro)); $i++)
            {
                $codlot = $codlot . '0';
            } 

            $codlot = trim($codlot . $conpro);
        } 
        else
        { 
            // si ya se tiene el codigo del producto, se puede consultar las caracterisiticas enviado directamente esta variable
            consultarProducto($codpro, $via, $tfd, $tfh, $tvd, $tvh, $fecha, $inslis, $tippro, $estado, $foto, $neve, $conpro, $nompro, $genpro, $presentacion);
            $productos[0]['cod'] = $codpro;
        } 

        $productos[0]['nom'] = $nompro;
        $productos[0]['gen'] = $genpro; 
        // si no se ha definido la cantidad de producto para el lote se incializan en cero
        if (!isset($lotcan))
        {
            $lotcan = 0;
            $lotsal = 0;
            $val = 0;
        } 
        else // si existe, incializamos el saldo igual a la cantidad
            {
                $lotsal = $lotcan;
        } 
        if (!isset($val))
        {
            $val = $lotcan;
        } 
        // aca voy a organizar el producto de adaptacion si lo tiene
        if (isset($parbusA) and $parbusA != '')
        {
            $productosA = consultarAdaptacion($parbusA, $forbusA);
            $productoA = $productosA[0];
            $loteA = '';
            $canA = '';
        } 
        else if (isset($productoA) and $productoA != '')
        {
            $productosA[0] = $productoA;
        } 

        if (isset($productoA) and $productoA != '')
        {
            if (isset($loteA))
            {
                $lotesA = consultarLotesA($productoA, $cco, $loteA, $canA);
            } 
            else
            {
                $lotesA = consultarLotesA($productoA, $cco, '', $canA);
            } 
            if (is_array($lotesA))
            {
                $loteA = $lotesA[0];
            } 
            else
            {
                $loteA = '';
            } 
        } 
        // si no se tiene lista de insumos se incializa en vacio
        if (!isset($inslis))
        {
            $inslis = '';
        } 
        else // si existe se incializa la cantidad de insumo como la cantidad para el producto por la cantidad de producto
            {
                for ($i = 0;$i < count($inslis);$i++)
            {
                if (isset($productoA) and $productoA != '' and isset($loteA) and $loteA != '')
                {
                    $adaptacion = calcularAdaptacion($inslis[$i]['cod'], $productoA, $canA);
                    $inslis[$i]['lot'] = $inslis[$i]['can'] * $lotcan - $adaptacion;
                    if ($inslis[$i]['lot'] < 0)
                    {
                        if ($lotcan > 0)
                        {
                            pintarAlert1('La cantidad de los insumos del producto a adaptar sobrepasan las cantidades necesarias para el lote');
                        } 
                        $inslis[$i]['lot'] = 0;
                    } 
                } 
                else
                {
                    $inslis[$i]['lot'] = $inslis[$i]['can'] * $lotcan;
                } 
                // adicionalmente se consultan las unidades por insumo
                $unidades[$i] = consultarUnidades($inslis[$i]['cod'], $cco);
                if (!$unidades[$i])
                { 
                    // para esto deben existir las presentaciones realcionadas de cada insumo en el maestro
                    // si no, se muestra alerta
                    pintarAlert1('Existen insumos sin relacionar con presentaciones');
                    $unidades[$i] = '';
                } 
            } 
        } 
		
        // si esta setiado crear, se activo el checkbox
        // se va a guardar el lote
        if (isset($crear))
        { 
            // se valida que todos los datos del lote esten ingresados
            if ($lotcan == '' or $feccre == '' or $persona == '')
            {
                pintarAlert1('Debe ingresar todos los datos generales del lote con cantidades de producto superiores a cero');
            } 
            else
            { 
                // se valida que la cantidad de producto sea mayor a cero
                if ($lotcan == 0)
                {
                    pintarAlert1('Debe ingresar cantidades de producto superiores a cero');
                } 
                else
                { 
                    // valida que el insumo este activo en el maestro de insumos y que existan las cantidades adecuadas en el kardex
                    // actualiza la cantidad necesitada por insumo
                    $val = validarComposicion($inslis, $cco, $lotcan); 
                    // si es mayor a cero, quiere decir que los insumos si estan en el maestro de articulos de central
                    if ($val >= 0)
                    { 
                        // si es igual a cero, algun insumo no esta en el kardex
                        if ($val == 0)
                        {
                            pintarAlert1('Sin existencia de los insumos señalados en rojo, en el kardex de inventario');
                        } 
                        else
                        { 
                            // si la cantidad disponible es menor a la solicitada para el lote,se depliega aviso
                            // y se indicara todo para la cantidad disponible
                            if ($val < $lotcan)
                            {
                                pintarAlert1('Los insumos requeridos tienen menos existencias de las requeridas, se propone el maximo de productos para la disponibilidad de los insumos');
                            } 
                            else // en caso de que los insumos esten disponibles para la cantidad de producto
                                {
                                    // para cada presentacion se valida que si haya la cantidad suficiente
                                    // y que las presentaciones de un insumo si sumen lo requerido de insumo
                                    $val = 0;
									
									for ($i = 0; $i < count($inslis); $i++)
									{ 
										$val2 = validarPresentaciones($inslis[$i], $unidades[$i], $cantidades1[$i], $cco); 
										// si no pasa presentacion, $val sera diferente de cero
										if (!$val2)
										{
											$val = 1;
										} 
									} 
									// si val es difernete de cero, se indica que hay presentaciones que no suman la canridad necesaria o no
									// tienen suficiente existencias
									if ($val != 0)
									{
										pintarAlert1('Las cantidad indicadas de las presentaciones no son equivalentes a las cantidades requeridas de los insumos o sobrepasan las cantidades permitidas por presentacion');
									} 
									else // una vez pasadas todas las validaciones, se puede creal el lote
                                    {
                                        if (isset($productoA) and $productoA != '' and isset($loteA) and $loteA != '')
                                        {
                                            $val = validarProductoA($productoA, $codpro);
                                            $val = validarCanA($productoA, $canA, $loteA);
                                        } 
                                        if ($val == 0)
                                        { 
                                            // se graba el encabezado de salida de insumos por consumo
                                            grabarEncabezadoSalida($codigo, $consecutivo, $cco, $wusuario); 
                                            // grabamos el detalle de salida de cada insumo
                                            // descontamos el  insumo de kardex
                                            for ($i = 0; $i < count($inslis); $i++)
                                            {
                                                grabarDetalleSalida($inslis[$i]['cod'], $codigo, $consecutivo, $wusuario, $codlot, $codpro, $unidades[$i], $cantidades1[$i], $cco, $inslis[$i]['lot']);
                                                descontarInsumo($inslis[$i]['cod'], $inslis[$i]['lot'], $cco, $unidades[$i], $cantidades1[$i]);
                                            } 

                                            if (isset($productoA) and $productoA != '' and isset($loteA) and $loteA != '')
                                            {
                                                $exp = explode('-', $productoA);
                                                grabarDetalleSalida2($loteA . '-' . $exp[0], $codigo, $consecutivo, $wusuario, $codlot, $codpro, '', 1, $cco, $canA);
                                                descontarArticuloMatrix($exp[0], $cco, $loteA, $canA);
                                            } 
                                            // se graba la entrada del lote y de los productos
                                            grabarEncabezadoEntrada($codigo, $consecutivo, $cco, $wusuario);
                                            grabarDetalleEntrada($codpro, $lotcan, $codigo, $consecutivo, $wusuario, $codlot, $fecven);
                                            sumarProducto($codpro, $lotcan, $cco, $wusuario);
                                            // grabarLote(&$codlot, $codpro, $cco, $feccre, $fecven, $lotcan, $lotsal, $wusuario, $persona);
                                            grabarLote($codlot, $codpro, $cco, $feccre, $fecven, $lotcan, $lotsal, $wusuario, $persona,$revisado);
                                            $pintar = 2;
                                            $val = $lotcan;
                                        } 
                                        else if ($val == 1)
                                        {
                                            pintarAlert1('El producto a adaptar no esta compuesto de los mismos insumos que el compuesto con el que se va a cerar el lote');
                                        } 
                                        else
                                        {
                                            pintarAlert1('No existe tanta cantidad de ese producto a adaptar');
                                        } 
                                    } 
                                } 
                            } 
                        } 
                        else
                        {
                            pintarAlert1('Verifique la existencia de los insumos señalados en rojo, en el maestro de insumos');
                        } 
                    } 
                } 
            } 
        } //si se ha encontrado un lote por algun parametro de busqueda
        else if (isset($consulta) and $consulta != '')
        {
            $pintar = false; 
            // muchas veces el usuario puedo haber seleccionado uno de los lotes encontrados con el paramentro de busqueda
            // y en ese caso consulta es un string que solo esta formado por el codigo del producto y del lote, por lo que se debe volver a buscar
            // toda la informacion del lote
            if (!is_array($consulta))
            {
                $exp = explode('-', $consulta);
                $consultas = consultarLotes($consulta, 'COMPLETO');
                $consulta = $consultas[0];
            } 
            // se da valor a todas la variables con los datos encontrados
            $productos[0]['cod'] = $consulta['pro'];
            consultarProducto($consulta['pro'], $via, $tfd, $tfh, $tvd, $tvh, $fecha, $inslis, $tippro, $estado, $foto, $neve, $conpro, $nompro, $genpro, $presentacion);
            $productos[0]['cod'] = $consulta['pro'];
            $productos[0]['nom'] = $nompro;
            $productos[0]['gen'] = $genpro;
            $codlot = $consulta['cod'];
            $lotsal = $consulta['sal'];
            $lotcan = $consulta['cin'];
            $val = $lotcan;
            $persona = $consulta['per'];
            $cco = $consulta['cco'];
            if (!isset($inslis))
            {
                $inslis = '';
            } 
            else // cuando se optiene el vector de insumos se determinan las catindades por insumo para el lote y se consultan
                {
                    // las difernes presentaciones por insumo
                    for ($i = 0;$i < count($inslis);$i++)
                { 
                    // para cada insumo se consultan en el movmiento las presentaciones usadas y sus cantidades
                    $inslis[$i]['lot'] = $inslis[$i]['can'] * $lotcan;
                    $unidades[$i] = consultarUnidades2($codlot, $productos[0]['cod'], $cantidades1[$i], $inslis[$i]['cod']);
                } 
            } 

            $productoA = consultarProductoA($codlot, $productos[0]['cod'], $loteA, $canA);
            if ($productoA == '')
            {
                $productosA = $productoA;
                $lotesA = $loteA;
            } 
            else
            {
                $productosA[0] = $productoA;
                $lotesA[0] = $loteA;
            } 

            if ($consulta['est'] == 'off') // en caso de que el lote haya sido anulado
            {
            	$pintar = 4;
            } 
            // si se ha dado click sobre anular
            if (isset($anular) and $anular == 1)
            { 
                // para anular el lote no debe haberse consumido ningun producto
                if ($lotsal == $lotcan)
                {
                    anularMovimientos($codlot, $productos[0]['cod'], $con);
                    for ($i = 0; $i < count($inslis); $i++)
                    {
                        sumarInsumo($inslis[$i]['cod'], $inslis[$i]['lot'], $unidades[$i], $cantidades1[$i], $con, $codlot, $productos[0]['cod']);
                    } 
                    descontarProducto($productos[0]['cod'], $lotcan, $wusuario);
                    if (isset($productoA) and $productoA != '' and isset($loteA) and $loteA != '')
                    {
                        $exp = explode('-', $productoA);
                        sumarArticuloMatrix($exp[0], $loteA, $canA);
                    } 
                    anularLote($codlot, $productos[0]['cod']);
                    $pintar = 3;
                } 
                else
                {
                    pintarAlert1('El lote ya no puede ser anulado, pues ya se ha consumido producto');
                } 
            } 
        } 
        else
        {
            $pintar = false;
        } 
        // si no se ha seleccionado una forma de busqueda del lote, se incializa en busqueda por numero de lote
        if (!isset($forcon))
        {
            $forcon = 'Codigo del Producto';
        } 
        // si no se han hecho consultas, se incializa en vacio, para que el select muestre vacio
        if (!isset($consultas))
        {
            $consultas = '';
//            $consultas = array();
        } 
        // si bo se ha ingresdo la cantidad a crear del lote, se incializa la cantidad del lote y el saldo en 0
        if (!isset ($lotcan))
        {
            $lotcan = 0;
            $val = 0;
            $lotsal = 0;
        } 
        // si no se ha determinado el producto por lo que la lista de insumos no se ha creado, se incializa en vacio, al igual que las diferentes
        // presentaciones para el insumo (unidades)
        if (!isset($inslis))
        {
            $inslis = '';
            $unidades[0] = '';
        } 
        else if (!isset($crear) and (!isset($consulta)or $consulta == '')) // en caso de que si existe, se calcula la cantidad de insumo requerida para el lote, como la cantidad para un producto por el
        {
            // numero de productos a realizar
            for ($i = 0;$i < count($inslis);$i++)
            {
                if (isset($productoA) and $productoA != '' and isset($loteA) and $loteA != '')
                {
                    $adaptacion = calcularAdaptacion($inslis[$i]['cod'], $productoA, $canA);
                    $inslis[$i]['lot'] = $inslis[$i]['can'] * $lotcan - $adaptacion;
                    if ($inslis[$i]['lot'] < 0)
                    {
                        $inslis[$i]['lot'] = 0;
                    } 
                } 
                else
                {
                    $inslis[$i]['lot'] = $inslis[$i]['can'] * $lotcan;
                } 
            } 
        } 
        // si no existe aun la fecha de creacion, se incializa en la fecha del momento
        if (!isset ($feccre))
        {
            $feccre = date('Y-m-d');
        } 
        // si no existe aun el tiempo de vencimiento porque no se ha dicho que producto es, se incializa la fecha de vencimiento con la fecha del momento
        if (!isset ($tvd))
        {
            $fecven = date('Y-m-d');
        } 
        else // si ya se cuenta con el tiempo de vencimiento para el producto del que se hizo el lote, se calcula como la fecha de creacion mas
        {
            // el tiempo de vencimiento en dias
            $tiempo = mktime(0, 0, 0, substr($feccre, 5, 2), substr($feccre, 8, 2), substr($feccre, 0, 4)) + ($tvd * 24 * 60 * 60);
            $fecven = date('Y-m-d', $tiempo);
        } 
        // si ya se ha seleccionado la persona que va a elaborar el lote, se consulta la lista de personaas que pueden hacerlo
        // poniendo de primera la seleccionada
        if (isset($persona))
        {
            $personas = consultarPersonas($persona, $ccos[0]);
        } 
        else
        {
            $personas = consultarPersonas('', $ccos[0]);
        }
        
        // existencias indica si ya existen lotes para un producto determinado, si aun no existe la variable
        // se incializa en false
        if (!isset($existencias))
        {
            $existencias = false;
        } 
        // consultar impresoras
        if (!isset ($wip))
        {
            $wips = consultarImpresora('', $ccos[0]);
        } 
        else
        {
            $wips = consultarImpresora($wip, $ccos[0]);
        } 
		
		// si ya se ha seleccionado la persona que va a revisar el lote, se consulta la lista de personas que pueden hacerlo
        // poniendo de primera la seleccionada
        if (isset($revisado))
        {
            $personasRevisan = consultarPersonas($revisado, $ccos[0]);
        } 
        else
        {
            $personasRevisan = consultarPersonas('', $ccos[0]);
        } 
		
        // si no existe una lista de productos, resultado de una busqueda, se manda ete parametro vacio a la funcion que pinta el formulario pricipal
        // al igual que las caraterisiticas principales del producto
        // pintar formulario despliega, la opcion de busqueda de lotes por diferentes formas, las caracterisitcas del lote:codigo, fecha de creacion,
        // persona que elabora, cantidad y saldo y despliega las carateristicas del producto ingresadas con el programa de creacionde productos
        if (!isset($productos))
        {
            pintarFormulario($forcon, '', $consultas, '', '', '', '', '', '', 'off', 'off', $lotcan, $lotsal, $feccre, $fecven, $personas, $pintar, $ccos, @$val, $existencias, $wips,$whistoria,$wingreso,$warticuloda,$idoda,$sinReemplazo,$wronda,$wfecharonda,$personasRevisan);
            // pintarFormulario($forcon, '', $consultas, '', '', '', '', '', '', 'off', 'off', $lotcan, $lotsal, $feccre, $fecven, $personas, $pintar, $ccos, @$val, $existencias, $wips);
//            pintarFormulario($forcon, Array(), $consultas, '', '', '', '', '', '', 'off', 'off', $lotcan, $lotsal, $feccre, $fecven, $personas, $pintar, $ccos, $val, $existencias, $wips);
        } 
        else
        {
            // pintarFormulario($forcon, $productos, $consultas, $codlot, $presentacion, $via, $tfd, $tvd, $fecha, $foto, $neve, $lotcan, $lotsal, $feccre, $fecven, $personas, $pintar, $ccos, $val, $existencias, $wips);
            pintarFormulario($forcon, $productos, $consultas, $codlot, $presentacion, $via, $tfd, $tvd, $fecha, $foto, $neve, $lotcan, $lotsal, $feccre, $fecven, $personas, $pintar, $ccos, $val, $existencias, $wips,$whistoria,$wingreso,$warticuloda,$idoda,$sinReemplazo,$wronda,$wfecharonda,$personasRevisan);
        } 
		
		if($whistoria!=null)
        {
			echo "<input type='hidden' name='whistoria' value='".$whistoria."'>";
			echo "<input type='hidden' name='wingreso' value='".$wingreso."'>";
			echo "<input type='hidden' name='warticuloda' id='warticuloda' value='".$warticuloda."'>";
			echo "<input type='hidden' name='idoda' value='".$idoda."'>";
			echo "<input type='hidden' name='sinReemplazo' value='".$sinReemplazo."'>";
			echo "<input type='hidden' name='wronda' value='".$wronda."'>";
			echo "<input type='hidden' name='wfecharonda' value='".$wfecharonda."'>";
		}
		
		if (!isset($productosA))
        {
            $productosA = '';
        } 
        if (!isset($lotesA))
        {
            $lotesA = '';
            $canA = '';
        } 
        pintarAdaptacion($productosA, $lotesA, $canA); 
        // para cada presentacion de un insumo se deben determinar en un vector de cantidades, las cantidades en la unidad minima que se van a
        // utiliza, si este vector aun no existe, para cada presentacion se incializa la cantidad en cero
        if (!isset ($cantidades1))
        {
            for ($i = 0; $i < count($unidades); $i++)
            {
                if ($unidades[$i] != '')
                {
                    for ($j = 0; $j < count($unidades[$i]); $j++)
                    {
                        $cantidades1[$i][$j] = '';
                    } 
                } 
                else
                {
                    $cantidades1[$i][0] = 0;
                } 
            } 
        } 
        // pinta la lista de insumos que componen el producto, la cantidad requerida de insumo para el producto, la cantidad requerida de
        // insumo para el lote, las presentaciones que forman el insumo, la existencia de cada presentacion, la fecha de vencimiento de la porcion
        // destapada de insumo y la cantidad que se utilizara de esa presentacion en el lote.
        pintarInsumos($inslis, $unidades, $cantidades1, $pintar);
    } 
    /**
    * ===========================================================================================================================================
    */

    ?>


</body >
</html >

<?php
}
?>
