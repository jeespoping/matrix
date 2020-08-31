<html>
<head>
<title>Dev. Aplicacion Automática</title>
<style type="text/css">
    	<!--body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloPeq{color:#006699;background:#FFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#336666;background:#AAFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.errorTitulo{color:#FF0000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	
    	.alert{background:#FFFFAA;color:#000000;font-size:9pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#000000;font-size:9pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#000000;font-size:9pt;font-family:Arial;text-align:center;}   	
    	
    	.tituloA1{color:#FFFFFF;background:#660099;font-family:Arial;font-weight:bold;text-align:center;}
    	.textoA{color:#660066;background:#FFFFFF;font-size:10pt;font-family:Arial;}
    	    
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

   function validar(nombre)
   {
   	textoCampo = window.document.devApl.elements[nombre].value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.devApl.elements[nombre].value = textoCampo;
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

    </script>

</head>
<body>
<?php
include_once("conex.php");

/**
* Se asume que sí cco['ima']==true todos los cargos y devoluciones se hacen por aprovechamiento.<br>
* 
* Las transacciones de los descartes son:</br>
* * Movimiento de saldos<br>
* * Registro del descarte<br>
* * Registro de la aplicación, si aplica automáticamente.<br>
* De los tres el mas importante es el de saldos.
* 
* Orden en que se empieza a devolver:</br>
* * El saldo de fuente simple NO aplicado (esta en la tabla 000004)</br>
* * El saldo de fuente de aprovechamiento NO aplicado (esta en la tabla 000004)</br>
* * El saldo de fuente simple Aplicado (esta en la tabla 000030)</br>
* * El saldo de fuente de aprovechamiento Aplicado (esta en la tabla 000030)</br>
* En donde encuetre cantidad devuleve lo máximo que se pueda.</br></br>
* 
* Orden en que se descarta:</br>
* * El saldo de fuente de aprovechamiento NO aplicado (esta en la tabla 000004)</br>
* * El saldo de fuente de aprovechamiento Aplicado (esta en la tabla 000030)</br>
* * El saldo de fuente simple NO aplicado (esta en la tabla 000004)</br>
* * El saldo de fuente simple Aplicado (esta en la tabla 000030)</br></br>
* 
* Convenciones:<br>
* * Cuando se habla en el programa de Simple se refiere a que el cargo original que se le hizo al paciente fue hecho con una fuente de facturación normal que mueve inventarios.<br>
* * Cuando se habla en el programa de Aprovechamiento se refiere a que el cargo original que se le hizo al paciente fue hecho con una fuente de facturación de aprovechamiento, es decir que mueve la cuanta del paciente tal y como lo hace la fuente normal pero que <b>NO</b> mueve inventarios.<br>
* 
* @version 2008-03-26
* @modified Febrero 25 de 2013 (Edwin MG). Cambios varios para cuando no hay conexión con UNIX. Entre ellos se registra el movimiento en tabla de paso
*									       y se mira los saldos en matrix y no en UNIX.
* @modified 2012-12-03 Se añade funciones en javascript para deshabilitar teclas especiales (F5, control derecho, backspace, etc) para no permitir recarga ya que si se hace recarga puede crear
 * 					   registros con cantidad 0 (fdecan) en la tabla de cargos de pacientes (movhos_000003) y el integrador genera inconvenientes si este campo (fdecan) se encuentra en 0.
* @modified 2008-03-26 Se modifica para que solo permita hacer devolución de lo grabado en el propio centro de costo o de lo grabado por dispensación o central de mezclas
* @modified 2007-11-06 No se permite devolver sino el minimo facturable. Carolina Castaño. Se pone la opcion de retornar
* 						Se quita el sobrante
* modified 2007-09-27 Se consulta permiso para la ejecucion de esta funcion 
* @modified 2007-09-27 Se hace la conexión odbc a facturación antes de llamar a ValidacionHistoriaUnix
* @modified 2007-09-26 cada llamado a la función registrarAplicacion(), para que en vez de enviar como ronda date("g-A") envíe date("g:i - A").
* @modified 2007-09-25 Cambian los parámetros de devCons().
* @modified 2007-09-24 Sale la función devCons() -
* @modified 2007-09-24 Sale la funciondevCons y se ubica en el include registrarTablas, adicionalmente se le incluyen dos parámetros ($pac, $usuario) que deben ser añadidos en el respectivo llamado.
* @modified 2007-09-24 Se crea la función registrarSobrante() que hace el registro en la nueva tabla 000035, por lo que ya no se necesitan los queries correspondientes a los descartes en la 000028
* @modified 2007-09-24 Se crea la función registrarDevFact() que hace el registro en la tabla 000028, por lo que ya no se necesita ese query.
* @modified 2007-09-23 en procesoDev()por cada registro en la tabla 000028 se estab registrando la cantidad que el usuario habia elegido en el programa lo que podia resultar en una duplicación en la cantidad de faltantes, se modifique paraque registre como faltante unicamente la cantidad digitada pro el usuario.
* @modified 2007-09-21 Como se crea el campo 000028.Devart se modifica el registro dela devolución para que lo tome en cuenta, en el programa en general y en la función procesoDev().
* @modified 2007-09-20 A partír de la creación del campo 000028.Devapv se modifican los queries de insert a esa tabla, en la función procesoDev() y en el programa en general.
* @modified 2007-09-20 Se comienza el usao de la función registrarDescarte() en la función procesoDev() y en el programa en genral.
* @modified 2007-09-20 Dada la modificación sufrida en la función registrarAplicacion() se cambian los llamdos a la función registrarAplicacion() propias de los descartes,en la función procesoDev().
* @modified 2007-09-19 Se modifica la función inFoArtículo para que funcione con la nueva tabla de saldos de aplicación 000030
* @modified 2007-09-19 Se cambian los llamdos a la función registrarSaldos() propias de las devoluciones,en la función procesoDev() por las funciones registrarSalsosNoApl() o registrarSaldosAplicacion() según corresponda.
* @modified 2007-09-19 Dada la modificación sufrida en la función registrarAplicacion() se cambian los llamdos a la función registrarAplicacion() propias de las devoluciones,en la función procesoDev().
* @modified 2007-09-19 Se cambian los llamdos a la función registrarSaldos() propias de las devoluciones,en la función procesoDev() por las funciones registrarSalsosNoApl() o registrarSaldosAplicacion() según corresponda.
* @modified 2007-09-13 Se modifica la función validacionBasica() para que tome en cuenta el sobrante y deje devolver si el sobrante es mayor que cero, así la devolución y el descarte sea cero.
* @modified 2007-09-12 Se crea la posibilidad de ingresar informacion de sobrante en la tabla 000028, a través de $listaDev[$i]['cco'][$j]['sob']
* @modified 2007-09-12 se creo un nuevo parámetro en la funcion registrarAplicacion() del include registro_tablas.php, es necesario envíar la ronda en la cual se va a aplicar el artículo, se modifican los llamados a la función
* @modified 2007-09-05 Se implementan cambios necesarios en la lógica de las funciones las funciones infoArticulo y procesoDev, para que si el cuando se cargo el artículo se hizo una aplicación entonces al devolver se haga una contraria, pero que si no, no.
* @modified 2007-09-04 Se empieza a unar las variables $pac['ptr'] (paciente en traslado, $pac['ald'] paciente de alta. $pac['alp'] (paciente con alta en proceso para restringir el acceso a la aplicación.
* @created 2007-08-31
*/


/**
* Por cada registro en la 000003 es un registro en la 000028
* 
* Para las devoluciones:
* Primero fuente simple, 
* 	a. Que no aplico automaticamente.
* 	b. Que si quedo aplicada
* Segundo Fuente de Aprovechamiento,
* 
* Descarte
* 	a. Aprovechamiento
*    b. Simple
* 
* @modified 2007-07-21 Como se crea el campo 000028.Devart se modifica el registro dela devolución para que lo tome en cuenta.
* @modified 2007-07-19 Como se crea el campo 000028.Devapv se modifica el registro dela devolución para que lo tome en cuenta.
* @modified 2007-07-19 Seempiezan a usar las funciones registrarSaldosNoAplica y registrarSaldosAplicacion() en las devoluciones, como reemplazo a la función registrarSaldos().
* @param Integer $devCons 
* @param Array $pac Información del paciente</br>
* 								[his]:Historia del paciente.</br>
* 								[ing]:
* @param Array $art [cod]:Código del artículo.</br>
* 								[can]:cantidad del artículo.</br>
* 								[neg]:Si el artículo permite o no generar negativos en el inventario.
* @param String $jusD 
* @param Doubble $faltante 
* @param String $jusF 
* @param array $cco Información del centro de costos</br>
* 								Debe ingresar:</br>
* 								[cod]:String[4].Código del centro de costos.</br>
* 								[neg]:Boolean.El Centro de costos permite generar negativos en el inventario para todos los artículso.</br>
* 								[apl]:Boolean. Los artículos que se cargan de este centrod e costos se graban como aplicados inmediatamente.
* @param String $ [1]	$tipTrans	Tipo de transacción
* 								C: cargo a cuenta de pacientes
* 								D: Devolución.
* @param Boolean $aprov Si es un aprovechamiento o no.
* @paramArray $usu		Información del usuario.
* 								[usuM]:Código de MAtrix del usuario.
* @param unknown_type $error Almacena todo lo referente con códigos y mensajes de error
* 								[ok]:Descripción corta.</br>
* 								[codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010</br>
* 								[codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.</br>
* 								[descSis]:Descripción del error del sistema.</br>
* @return unknown 
*
* Modificacion: Enero 8 de 2013
*/
function procesoDevMatrix($devCons, $pac, $art, $datos, $jusD, $faltante, $jusF, $descarte, $ddes, $cco, $usuario, &$error, &$msg, $ccoServicioOrigen )
{
	global $bd;
	global $conex;
	global $conex_o;
	global $solicitudCamillero;
	
	// Cantidad a devolver total del artículo
	$cdv = $datos['cdv'];
	// Cantidad cargada por aprovechamiento suceptible de ser devuelta
	$cia = $datos['cia'];
	// Catidad cargada por fuente Simple suceptible de ser devuelta.
	$cis = $datos['cit'] - $datos['cia'];
	// Cantidad cargada por fuente simple que ya fue aplicada cuando se cargo al paciente
	$cantSimpleApl = $datos['csa'];
	// Cantidad cargada por fuente simple NO aplicada
	$cantSimpleNoApl = $datos['csna'];
	// Cantidad cargada por fuente de aprovechamiento que ya fue aplicada cuando se cargo al paciente
	$cantAprovApl = $datos['caa'];
	// Cantidad cargada por fuente de aprovechamiento NO aplicada
	$cantAprovNoApl = $datos['cana'];
	// Inicializo Variables
	$msg = "";
	$return = true;
	$noErroresDeTarifaSaldo = true;
	$exito = 0;

	/**
    * Primero devuelvo por la cantidad simple
    */
	if ($cis > 0 and $cdv > 0)
	{
		if ($cdv >= $cis)
		{
			/**
            * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
            */
			$cdv = $cdv - $cis;
			$art['can'] = $cis;
			$cis = 0;
		}
		else
		{
			/**
            * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
            * Todo se devuelve por Devolución simple
            */
			$art['can'] = $cdv;
			$cdv = 0;
			$cis = $cis - $cdv;
		}
		// echo "<br><b>Entro al cis>0</b><br>";
		// echo "art[can]=".$art['can']."<br>";
		// echo "cis=$cis<br>";
		// echo "cdv=$cdv<br>";
		if (validacionDevolucion($cco, $pac, $art, false, true, &$error))
		{
			if (TarifaSaldoMatrix($art, $cco, "D", false, &$error))
			{
				$dronum = "";
				$drolin = "";
				$date = date("Y-m-d");
				$fuente = $cco['fue'];
				if (Numeracion($pac, $fuente, 'D', false, $cco, &$date, &$cns, &$dronum, &$drolin, $pac['dxv'], $usuario, &$error))
				{
					if( true )
					{
						$art['ubi'] = 'US';
						$art['ini'] = $art['cod'];
						$art['lot'] = " ";
						if (registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario, &$error, "000143" ))
						{
							/***************************************************************************
							 * Enero 8 de 2013
							 *
							 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
							 ***************************************************************************/
							realizarMovimientoSaldos( $conex, $bd, "D", $cco[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
							/***************************************************************************/
						
							/**
                            * Cambio del 2007-09-05
                            */ 
							// La cantidad esta en $art['can']
							$cdvSimple = $art['can'];
							if ($cantSimpleNoApl > 0)
							{
								// echo "<br><i>Simple No aplica</i><br>";
								if ($cdvSimple >= $cantSimpleNoApl)
								{
									/**
                                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple que no aplica
                                    * se deja la cantida restante para que se devuelva pero aplicando auntomaticamente.
                                    */
									$cdvSimple = $cdvSimple - $cantSimpleNoApl;
									$art['can'] = $cantSimpleNoApl;
									$cantSimpleNoApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                                    * Todo se devuelve por Devolución simple
                                    */
									$art['can'] = $cdvSimple;
									$cantSimpleNoApl = $cantSimpleNoApl - $cdvSimple;
									$cdvSimple = 0;
								}
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantSimpleNoApl=$cantSimpleNoApl<br>";
								// echo "cdvSimple=$cdvSimple<br>";
								$cco['apl'] = false;
								if (registrarSaldosNoApl($pac, $art, $cco, false, $usuario, "D", false, &$error))
								{
									$exito = $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvSimple = $cdvSimple + $art['can'];
									$error['ok'] = "LA DEVOLUCION SIMPLE SE EFECTUO. <br>Pero hubo un problema grabando<br>el Movimiento de Aplicación con la cantidad" . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
									/**
                                    * No se pone $return=false por que cabe la posibilidad de que $cdvSimple <= $cantSimpleApl), es decir que
                                    * se pueda hacer la devolución por fuente simple aplicando automáticamente.
                                    * Si no se puede mas adelante el programa pone $return=false.
                                    */
								}
							}

							if ($cantSimpleApl > 0 and $cdvSimple > 0)
							{
								/**
                                * El artículo fue cargado al paciente por fuente simple y se aplico automáticamente
                                */
								if ($cdvSimple >= $cantSimpleApl)
								{
									/**
                                    * La cantidad a devolver es mayor o igual que la cantidad existente grabada por cargo simple que plica automaticamente.
                                    * Sí es mayor significa que hubo un error en el paso anterior, y queda pendiente una cantidad a devolver que debe ser informada al usuario
                                    */
									$cdvSimple = $cdvSimple - $cantSimpleApl; // lo que no se va a poder mover en saldos pero ya se movio en cargos (000002-000003)
									$art['can'] = $cantSimpleApl;
									$cantSimpleApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo simple que aplica automaticamente al paciente es mayor a la cantidad a devolver.
                                    * Lo que falta por devolver se devuelve por cargo simple que aplica automáticamente
                                    */
									$art['can'] = $cdvSimple;
									$cantSimpleApl = $cantSimpleApl - $cdvSimple;
									$cdvSimple = 0;
								}
								// echo "<br><i>Simple Aplica</i><br>";
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantSimpleApl=$cantSimpleApl<br>";
								// echo "cdvSimple=$cdvSimple<br>";
								$cco['apl'] = true; //para que aplique automáticamente
								if (registrarSaldosAplicacion($pac, $art, $cco, false, $usuario, "D", false, &$error))
								{
									// Por aplicar automáticamente es necesario generar el registro en la aplicación
									if (registrarAplicacion($pac, $art, $cco, false, $date, date("g:i - A"), $usuario, "D", $dronum, $drolin, &$error))
									{
										$error['ok'] = $error['ok'] . "Pero hubo un problema grabando<br>el Movimiento de Aplicación de " . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
										$return = false;
									}

									$exito = $exito + $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvSimple = $cdvSimple + $art['can'];
								}
							}

							if ($exito > 0)
							{
								/**
                                * Se realizo la devolución exitosa de la cantidad almacenada en $exito
                                * Esta cantidad que se modifico exitosamente en las tablas de saldos (000004) deve ser ingrsada a la
                                * tabla de devoluciones.
                                */
								if ($faltante > $exito)
								{
									$faltante = $faltante - $exito;
									$fal = $exito;
								}
								else
								{
									$fal = $faltante;
									$faltante = 0;
								}

								if (!registrarDevFact($devCons, $dronum, $drolin, $exito, $jusD, $fal, $jusF, $usuario, &$error))
								{
									$error['ok'] = $error['ok'] . "Hubo un problema grabando<br>el Movimiento de devolucion a<br>" . $cco['cod'] . "-" . $cco['nom'];
								}
								else{
									if( !$solicitudCamillero ){
										if( esTraslado( $cco['cod'] ) && true ){ //echo "paso11111....$ccoCod";
											peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoServicioOrigen, $cco['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
										}
										$solicitudCamillero = true;
									}
								}
								// Informo al usuario como qudo la devolución
								$msg = "<IMG SRC='/matrix/images/medical/root/feliz.ico'>LA DEVOLUCIÓN SIMPLE DE " . $exito . " " . $art['uni'] . " FUE REALIZADA CON EXITO.";
								$return = true;
							}

							if ($cdvSimple > 0 or !$return)
							{
								/**
                                * Hubo un fallo y no se devolvio toda la cantidad de fuente simple que se debia resolver
                                * o no se realizo el insert de la aplicación
                                */
								$msg = $msg . "<br>" . $error['ok'];
								$return = false;
							}
							/**
                            * Fin de los cambios de 2007-09-05
                            */

						} // if de registrarDetalleCargo()
						else
						{
							$return = false;
						}
					} //if de registrarItdro()
					else
					{
						$return = false;
					}
				} //if de Numeración()
				else
				{
					$return = false;
				}
			}
			else
			{
				// Sin tarifa, saldo
				$noErroresDeTarifaSaldo = false;
			}
		}
		else
		{
			/**
            * Sin  cantidad SIMPLE a devolver.
            * Ponemos toda la cantidad pendiente de devolver para que
            * lo intente devolver por aprovechamientos.
            */
			$cdv = $datos['cdv'];
		}
	}
	if (!$return)
	{
		$msg = "LA DEVOLUCIÓN SIMPLE DE " . $art['can'] . " " . $art['uni'] . " TUVO UN INCONVENIENTE:<br> " . $error['ok'] . "";
	}

	if ($cdv > 0 and $noErroresDeTarifaSaldo)
	{
		if ($cdv >= $cia)
		{
			/**
            * La cantidad a devolver es igual que la cantidad grabada por cargo simple
            * NO debería ser nunca mayor a cia por que eso querria decir que va a devolver mas cantidad de la suceptible a devolver
            */
			$cdv = $cdv - $cia;
			$art['can'] = $cia;
			$cia = 0;
		}
		else
		{
			/**
            * La cantidad grabada por cargo de aprovechamiento es mayor a la cantidad a devolver.
            */
			$art['can'] = $cdv;
			$cia = $cia - $cdv;
			$cdv = 0;
		}
		// echo "<br><b>Entro al cdv>0</b><br>";
		// echo "art[can]=".$art['can']."<br>";
		// echo "cis=$cis<br>";
		// echo "cdv=$cdv<br>";
		if (validacionDevolucion($cco, $pac, $art, true, true, &$error))
		{
			if (TarifaSaldo($art, $cco, "D", true, &$error))
			{
				$dronum = "";
				$drolin = "";
				$date = date("Y-m-d");
				$fuente = $cco['fap'];
				if (Numeracion($pac, $fuente, 'D', true, $cco, &$date, &$cns, &$dronum, &$drolin, $pac['dxv'], $usuario, &$error))
				{
					if (registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, &$error))
					{
						$art['ubi'] = 'US';
						$art['ini'] = $art['cod'];
						$art['lot'] = " ";
						if (registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario, &$error))
						{
							/**
                            * Cambio del 2007-09-06
                            */ 
							// La cantidad esta en $art['can']
							$cdvAprov = $art['can'];
							$exito = 0;
							if ($cantAprovNoApl > 0)
							{
								if ($cdvAprov >= $cantAprovNoApl)
								{
									/**
                                    * La cantidad a devolver es mayor que la cantidad grabada por cargo de aprovechamiento que NO aplica
                                    * se deja la cantidad restante para que se devuelva pero aplicando auntomaticamente.
                                    */
									$cdvAprov = $cdvAprov - $cantAprovNoApl;
									$art['can'] = $cantAprovNoApl;
									$cantAprovNoApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo por aprovechamiento al paciente mayor a la cantidad a devolver.
                                    * Todo se devuelve por Devolución por aprovechamiento que NO aplica.
                                    */
									$art['can'] = $cdvAprov;
									$cantAprovNoApl = $cantAprovNoApl - $cdvAprov;
									$cdvAprov = 0;
								}
								// echo "<br><i>APROV No aplica</i><br>";
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantAprovNoApl=$cantAprovNoApl<br>";
								// echo "cdvAprov=$cdvAprov<br>";
								$cco['apl'] = false;

								/**
                                * Se mueve el saldo de la la tabla de saldos del paciente
                                */
								if (registrarSaldosNoApl($pac, $art, $cco, true, $usuario, "D", false, &$error))
								{
									$exito = $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvAprov = $cdvAprov + $art['can'];
									$error['ok'] = "LA DEVOLUCION SIMPLE SE EFECTUO. <br>Pero hubo un problema grabando<br>el Movimiento de Aplicación con la cantidad" . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
									/**
                                    * No se pone $return=false por que cabe la posibilidad de que $cdvAprov <= $cantAprovApl), es decir que
                                    * se pueda hacer la devolución por fuente simple aplicando automáticamente.
                                    * Si no se puede mas adelante el programa pone $return=false.
                                    */
								}
							}

							if ($cantAprovApl > 0 and $cdvAprov > 0)
							{
								/**
                                * El artículo fue cargado al paciente por fuente de aprovechamiento y se aplico automáticamente
                                */
								if ($cdvAprov >= $cantAprovApl)
								{
									/**
                                    * La cantidad a devolver es mayor o igual que la cantidad existente grabada por cargo de aprovechamiento que aplica automaticamente.
                                    * Sí es mayor significa que hubo un error en el paso anterior, y queda pendiente una cantidad a devolver que debe ser informada al usuario
                                    */
									$cdvAprov = $cdvAprov - $cantAprovApl; // lo que no se va a poder mover en saldos pero ya se movio en cargos (000002-000003)
									$art['can'] = $cantAprovApl;
									$cantAprovApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo de aprovechamiento que aplica automaticamente es mayor a la cantidad a devolver.
                                    * Lo que falta por devolver se devuelve por cargo de aprovechamiento que aplica automáticamente
                                    */
									$art['can'] = $cdvAprov;
									$cantAprovApl = $cantAprovApl - $cdvAprov;
									$cdvAprov = 0;
								}
								// echo "<br><i>APROV  Aplica</i><br>";
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantAprovApl=$cantAprovApl<br>";
								// echo "cdvAprov=$cdvAprov<br>";
								$cco['apl'] = true; //para que aplique automáticamente
								if (registrarSaldosAplicacion($pac, $art, $cco, true, $usuario, "D", false, &$error))
								{
									// Por aplicar automáticamente es necesario generar el registro en la aplicación
									if (registrarAplicacion($pac, $art, $cco, true, $date, date("g:i - A"), $usuario, "D", $dronum, $drolin, &$error))
									{
										$error['ok'] = $error['ok'] . "Pero hubo un problema grabando<br>el Movimiento de Aplicación de " . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
										$return = false;
									}

									$exito = $exito + $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvAprov = $cdvAprov + $art['can'];
									/**
                                    * Error realizando el cambio en cargos
                                    * $error['ok'] = "LA DEVOLUCION SIMPLE SE EFECTUO. <br>Pero hubo un problema grabando<br>el Movimiento de Aplicación con la cantidad".$art['can']." ".$art['uni']."<br>Comunique esta infomrmación a Sistemas";
                                    * $return=false;
                                    */
								}
							}

							if ($exito > 0)
							{
								/**
                                * Se realizo la devolución exitosa de la cantidad almacenada en $exito
                                * Esta cantidad que se modifico exitosamente en las tablas de saldos (000004) deve ser ingrsada a la
                                * tabla de devoluciones.
                                */
								if ($faltante <= 0)
								{
									$jusF = " ";
								}
								if (!registrarDevFact($devCons, $dronum, $drolin, $exito, $jusD, $faltante, $jusF, $usuario, &$error))
								{
									$error['ok'] = $error['ok'] . "Hubo un problema grabando<br>el Movimiento de devolucion a<br>" . $cco['cod'] . "-" . $cco['nom'];
								}
								else{
									if( !$solicitudCamillero ){
										
										if( esTraslado( $cco['cod'] ) && true ){ //echo "paso2222....";
											peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoServicioOrigen, $cco['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
										}
										$solicitudCamillero = true;
									}
								}
								// Informo al usuario como qudo la devolución
								$msg = "<IMG SRC='/matrix/images/medical/root/feliz.ico'>LA DEVOLUCIÓN SIMPLE DE " . $exito . " " . $art['uni'] . " FUE REALIZADA CON EXITO.";
								$return = true;
							}

							if ($cdvAprov > 0 or !$return)
							{
								/**
                                * Hubo un fallo y no se devolvio toda la cantidad de fuente simple que se debia resolver
                                * o no se realizo el insert de la aplicación
                                */
								$msg = $msg . "<br>" . $error['ok'];
								$return = false;
							}
							/**
                            * Fin de los cambios de 2007-09-06
                            */
						} // if de registrarDetalleCargo()
						else
						{
							$return = false;
						}
					} //if de registrarItdro()
					else
					{
						$return = false;
					}
				} //if de Numeración()
				else
				{
					$return = false;
				}
			}
			else
			{
				// Sin tarifa, saldo
				$return = false;
			}
			// echo "si dio la validación SIMPLE cco['cod'=".$cco['cod'];
		}
		else
		{
			// Sin  cantidad a devolver
			$error['ok'] = "ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN, O UN DESCARTE, DE ESTE ARTÍCULO A ESTE PACIENTE, MIENTRAS UD. HACIA ESTA DEVOLUCION. Por este motivo " . $art['can'] . " " . $art['uni'] . " no pudieron ser devultas.";
			// echo "<b>ya si se piofio del todo la validación</b><br>";
			$return = false;
		}
	}

	if (!$return)
	{
		$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>LA DEVOLUCIÓN SIMPLE DE " . $art['can'] . " " . $art['uni'] . " TUVO UN INCONVENIENTE: " . $error['ok'] . "";
	}

	if ($descarte > 0)
	{
		/**
        * La transacción mas importente de los descartes es en movimiento de los saldos, 
        * por que el registro en la tabla de desacartes en con fines informativos, 
        * y el registro de la aplicación es solo para que haya concordancia en las tablas 
        * pero que no se realize no implica problemas en los rotos procesos del sistema,
        * como silo hace un aml movimiento de saldos
        */ 
		// echo "<br><b>Descarte Mayor que Cero</b><br>";
		$exito = 0;
		$noDescarte = 0;
		$noAplicado = 0;
		/**
        * Realizar el descarte normal o por aprovechamiento.
        */
		if ($cia > 0)
		{
			// echo "<b>IniciandoAprov NoApl</b></br>";
			// echo "cantAprovNoApl=".$cantAprovNoApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantAprovNoApl > 0)
			{
				// Descartar primero lo que hay en saldo no aplicado
				if ($descarte >= $cantAprovNoApl)
				{
					/**
                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantAprovNoApl;
					$art['can'] = $cantAprovNoApl;
					$cantAprovNoApl = 0;
				}
				else
				{
					/**
                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$descarte = 0;
					$cantAprovNoApl = $cantAprovNoApl - $descarte;
				}
				// echo "Despues de repartir<br>";
				// echo "cantAprovNoApl=".$cantAprovNoApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				// echo "cia=$cia<br>";
				$cco['apl'] = false;
				if (validacionDevolucion($cco, $pac, $art, true, false, &$error))
				{
					if (registrarSaldosNoApl($pac, $art, $cco, true, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, true, false, $art, $ddes, $usuario, &$error))
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/feliz.ico'>EL DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." FUE REALIZADA CON EXITO.";
							$exito = $exito + $art['can'];
						}
						else
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/Malo.ico'LA DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." TUVO UN INCONVENIENTE: ".$error['ok'].">";
							// $return=false;
							/**
                            * El descarte si se realizo hay que informar 
                            * y mostrar el error de que no sera visible en el reporte ni de la devolución ni de recepción de la devolución
                            */
							$noDescarte = $art['can'];
							// $descarte=$descarte+$cantAprovNoApl;
						}
					}
					else
					{
						/**
                        * No se pudo modifcar el saldo y no se hizo ninguna transacción
                        * Se restaura la cantidad a la cantidad que habia para intentar hacer el descarte por fuente simple que aplica.
                        */
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que NO aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descrte
                    */
					$descarte = $descarte + $art['can'];
				}
			}
			// echo "<br><b>Iniciando AprovApl</b></br>";
			// echo "cantAprovApl=".$cantAprovApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantAprovApl > 0 and $descarte > 0)
			{
				// Descartar  lo que hay en saldo no aplicado
				if ($descarte >= $cantAprovApl)
				{
					/**
                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantAprovApl;
					$art['can'] = $cantAprovApl;
					$cantAprovApl = 0;
				}
				else
				{
					/**
                    * La cantidad rabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$descarte = 0;
					$cantAprovApl = $cantAprovApl - $descarte;
				}
				// echo "Despues de repartir<br>";
				// echo "cantAprovApl=".$cantAprovApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				// echo "cia=$cia<br>";
				$cco['apl'] = true;
				if (validacionDevolucion($cco, $pac, $art, true, false, &$error))
				{
					// El registro de saldos es la transacción mas importante
					if (registrarSaldosAplicacion($pac, $art, $cco, true, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, true, true, $art, $ddes, $usuario, &$error))
						{
							$exito = $exito + $art['can'];
							$exitoDescarte = true;
						}
						else
						{
							/**
                            * Hubo un error, se movio el saldo pero no hizo el registro del descarte.
                            * Lo que indica que no va a ser visible en la recepción de la devolución, ni en el reporte
                            * de la devolución.
                            */
							$noDescarte = $noDescarte + $art['can'];
							$exitoDescarte = false;
						}

						if (!registrarAplicacion($pac, $art, $cco, true, date('Y-m-d'), date("g:i - A"), $usuario, "D", 0, 0, &$error))
						{
							/**
                            * Hubo un error, no se registro la aplicación.
                            * Lo que no es mayor cosa por que si se movio el saldo.
                            * Lo único que no se hizo fue el registro de la aplicación.
                            */
							$noAplicado = $noAplicado + $art['can'];
							if ($exitoDescarte)
							{
								$exito = $exito - $art['can'];
							}
						}
					}
					else
					{
						// Hubo un prblema con el saldo
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descrte
                    */
					$descarte = $descarte + $art['can'];
				}
			}
		}

		if ($descarte > 0)
		{
			/**
            * Primero se descartan las cantidades Simples no aplicadas, sí las hay
            */ 
			// echo "<br><b>Iniciando SimpleNoApl</b></br>";
			// echo "cantSimpleNoApl=".$cantSimpleNoApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantSimpleNoApl > 0)
			{
				// Hay cantidades Simples no aplicadas suceptibles de ser devueltas.
				if ($descarte >= $cantSimpleNoApl)
				{
					/**
                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantSimpleNoApl;
					$art['can'] = $cantSimpleNoApl;
					$cantSimpleNoApl = 0;
				}
				else
				{
					/**
                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$cantSimpleNoApl = $cantSimpleNoApl - $descarte;
					$descarte = 0;
				}
				// echo "Despues de repartir<br>";
				// echo "cantSimpleNoApl=".$cantSimpleNoApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				$cco['apl'] = false;
				if (validacionDevolucion($cco, $pac, $art, false, false, &$error))
				{
					if (registrarSaldosNoApl($pac, $art, $cco, false, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, false, false, $art, $ddes, $usuario, &$error))
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/feliz.ico'>EL DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." FUE REALIZADA CON EXITO.";
							$exito = $exito + $art['can'];
						}
						else
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/Malo.ico'LA DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." TUVO UN INCONVENIENTE: ".$error['ok'].">";
							// $return=false;
							$noDescarte = $noDescarte + $art['can'];
							// echo "<b>no se hizo el descarte</b></br>";
							// $descarte=$descarte+$cantSimpleNoApl;
						}
					}
					else
					{
						/**
                        * No se pudo modifcar el saldo y no se hizo ninguna transacción
                        * Se restaura la cantidad a la cantidad que habia para intentar hacer el descarte por fuente simple que aplica.
                        */ 
						// echo "<b>no se hicioron los saldos</b></br>";
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descarte.
                    */ 
					// echo "<b>mala la validación de la dev</b></br>";
					$descarte = $descarte + $art['can'];
				}
			}

			/**
            * Segundo se descartan las cantidades Simples Aplicadas, sí las hay
            */
			// echo "<br><b>Iniciando Simple Apl</b></br>";
			// echo "cantSimpleApl=".$cantSimpleApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantSimpleApl > 0 and $descarte > 0)
			{
				// Descartar primero lo que hay en saldo no aplicado
				if ($descarte >= $cantSimpleApl)
				{
					/**
                    * La cantidad a devolver IGUAL que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantSimpleApl;
					$art['can'] = $cantSimpleApl;
					$cantSimpleApl = 0;
				}
				else
				{
					/**
                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$descarte = 0;
					$cantSimpleApl = $cantSimpleApl - $descarte;
				}
				// echo "Despues de repartir<br>";
				// echo "cantSimpleApl=".$cantSimpleApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				$cco['apl'] = true;
				if (validacionDevolucion($cco, $pac, $art, false, false, &$error))
				{
					// El registro de saldos es la transacción mas importante
					if (registrarSaldosAplicacion($pac, $art, $cco, false, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, false, true, $art, $ddes, $usuario, &$error))
						{
							$exito = $exito + $art['can'];
							$exitoDescarte = true;
						}
						else
						{
							/**
                            * Hubo un error, se movio el saldo pero no hizo el registro del descarte ni la aplicación.
                            * Lo que indica que no va a ser visible en la recepción de la devolución., ni en el reporte
                            * de la devolución.
                            */ 
							// echo "<b>no se hizo el descarte</b></br>";
							$noDescarte = $noDescarte + $art['can'];
							$exitoDescarte = false;
						}

						if (!registrarAplicacion($pac, $art, $cco, false, date('Y-m-d'), date("g:i - A"), $usuario, "D", 0, 0, &$error))
						{
							/**
                            * Hubo un error, no se registro la aplicación.
                            * Lo que no es mayor cosa por que si se movio el saldo.
                            * Lo único que no se hizo fue el registro de la aplicación.
                            */

							$noAplicado = $noAplicado + $art['can'];
							if ($exitoDescarte)
							{
								$exito = $exito - $art['can'];
							}
						}
					}
					else
					{
						// echo "<b>no se hicioron los saldos</b></br>";
						// Hubo un prblema con el saldo
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descrte
                    */ 
					// echo "<b>mala la validación de la dev</b></br>";
					$descarte = $descarte + $art['can'];
				}
			}

			if ($exito > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/feliz.ico'>EL DESCARTE DE " . $exito . " " . $art['uni'] . " FUE REALIZADO CON EXITO.";
			}
			if ($descarte > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>El descarte de " . $descarte . " " . $art['uni'] . " tuvo uno o mas inconvenientes. INTENTELO NUEVAMENTE>";
				$return = false;
			}
			if ($noAplicado > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>El descarte de " . $noAplicado . " " . $art['uni'] . " se hizo, sinembargo presento un problema de aplicación, por favor comuniquelo a sistemas.>";
				$return = false;
			}

			if ($noDescarte > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>El descarte de " . $noDescarte . " " . $art['uni'] . " se hizo, sinembargo presento un problema de por lo tanto no sera vicible en el reporte de devolución ni en el reporte de recibo de devolución, por favor comuniquelo a sistemas.>";
				$return = false;
			}
		}
	}
	return ($return);
}




function buscarCodigoNombreCamillero( ){
	
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
		
		$val = crearPeticionCamillero( nombreCcoCentralCamilleros( $origen ), $motivo, "<b>Hab: ".$hab."</b><br>".$paciente, $nomCcoDestino, $solicita, $nomCcoDestino, buscarCodigoNombreCamillero() );
		
		if( $val ){
			$seHizoSolicitudCamillero = true;
		}
	}
}


/**
* Enter description here...
* 
* @modified 2007-09-19 Dada la creación de la tabla 000030 se modifica la función para que la use como es debido, lo que resulta en una simplificación de la función.
* @param unknown_type $art 
* @param unknown_type $ccoArr 
* @param unknown_type $cco 
* @param unknown_type $pac 
* @param unknown_type $fecRecibo 
* @param unknown_type $horRecibo 
* @param unknown_type $datos 
* @param unknown_type $errorMsg 
* @return unknown 
*/

function infoArticulo(&$art, &$ccoArr, $cco, &$pac, $fecRecibo, $horRecibo, &$datos, &$errorMsg)
{
	global $conex;
	global $bd;
	global $emp;

	$datos['art']['cod'] = $art;
	$msg = "";

	/**
    * Buscar el nombre del artículo.
    */
	$datos['class'] = "texto";
	if (!ArticuloExiste(&$datos['art'], &$erroMsg))
	{

		/**
        * Si no existe en el maestro normal puede que exista en la central de mezclas.
        * Hay que buscarlo ahi.
        */
		$datos['class'] = "texto";
		$q = " SELECT * "
		. "      FROM cenpro_000002 "
		. "     WHERE Artcod = '" . $art . "' ";
		$err1 = mysql_query($q, $conex);
		echo mysql_error();
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$datos['art']['act'] = true;
			$datos['art']['cmz'] = true;
			$datos['art']['class'] = "titulo1";
			$datos['art']['nom'] = $row1['Artcom'];
			$datos['art']['uni'] = $row1['Artuni'];
			$datos['art']['jdv'] = true;
			$datos['art']['apl'] = false;
		}
		else
		{

			$errorMsg = "EL ARTICULO NO EXISTE ";
			return(0);
		}
	}
	else
	{

		/**
        * Buscar si el grupo necesita Justificación de la devolución.
        */
		$gru = explode("-", $datos['art']['gru']);
		$q = " SELECT * "
		. "      FROM " . $bd . "_000029 "
		. "     WHERE Gjugru = '" . $gru[0] . "' ";
		$err1 = mysql_query($q, $conex);
		echo mysql_error();
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			if ($row1['Gjujus'] == "on")
			{
				$datos['art']['jdv'] = true;
			}
			else
			{
				$datos['art']['jdv'] = false;
			}
		}
		else
		{
			$datos['art']['jdv'] = false;
		}
		$datos['art']['act'] = true;
		$datos['art']['class'] = "titulo1";
		$datos['art']['cmz'] = false ;
	}
	$datos['art']['neg'] = true;

	$q = " SELECT DISTINCT Spacco as scco "
	. "       FROM " . $bd . "_000004 "
	. "      WHERE Spahis = '" . $pac['his'] . "' "
	. "        AND Spaing = '" . $pac['ing'] . "' "
	. "        AND Spaart = '" . $art . "' "
	. "      UNION "
	. "     SELECT DISTINCT Splcco as scco "
	. "       FROM " . $bd . "_000030, ".$bd."_000011 "      //Modificacion del 26 de Marzo de 2008
	. "      WHERE Splhis  = '" . $pac['his'] . "' "
	. "        AND Spling  = '" . $pac['ing'] . "' "
	. "        AND Splart  = '" . $art . "' "
	. "        AND Splcco  = Ccocod "
	. "        AND Ccourg != 'on' ";
	$err = mysql_query($q, $conex);
	echo mysql_error();
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$artCco = 0;
		$devolver = false;
		for($i = 0;$i < $num;$i++)
		{
			/**
            * Buscar la información del centro de costos.
            * Sea que ya este dentro del arrya de centros de costos
            * o que sea necesario trarerla.
            */
			$row = mysql_fetch_array($err);

			$saldoSimple = 0;
			$saldoAprov = 0;
			$saldoSimpleNoAplicado = 0;
			$saldoSimpleAplicado = 0;
			$saldoAprovNoAplicado = 0;
			$saldoAprovAplicado = 0;

			$ccoOk = false;
			$countCco = count($ccoArr);

			for($j = 0;$j < $countCco;$j++)
			{
				// Busco si el cco al que pertenece el saldo del paciente ya se encuentra en el arreglo de centros de costos o no.
				if ($row['scco'] == $ccoArr[$j]['cod'])
				{
					$ccoOk = true;
					$codCco = $j;
				}
			}

			if (!$ccoOk)
			{
				/**
                * El centro de costos del saldo del paciente no estaba en el array de centro de costos por lo que es necesario buscar 
                * los datos del centro de costos
                */
				$ccoArr[$countCco]['cod'] = $row['scco'];
				$codCco = $countCco;
				// Para que el centro de costos quede cargado con las fuentes de devolución
				$ccoArrOk = getCco($ccoArr[$countCco], "D", $emp);

				if (!$ccoArrOk)
				{
					$ccoArr[$countCco]['nom'] = "NO ENCONTRADO, NO ES fACTURABLE ";
					$ccoArr[$countCco]['act'] = false;
					$ccoArr[$countCco]['apl'] = false;
					$ccoArr[$countCco]['ima'] = false;
					$ccoArr[$countCco]['neg'] = false;
					$ccoArr[$countCco]['class'] = "errorTit1";
					$datos['cco'][$artCco]['msg'] = "El Centro de costos no existe <br> o no esta activado para facturar<br>o el artículo no existe";
					$datos['cco'][$artCco]['class'] = "error";
				}
				else
				{
					$ccoArr[$countCco]['act'] = true;
					$ccoArr[$countCco]['class'] = "acumulado2";
				}
				$ccoArr[$countCco]['apl'] = true;
			} //fin del if(!$ccoArrOk)
			/**
            * Todos los saldos en la tabla 000004 cuyo spacco="Centro de costos del paciente" deben ser igual a cero, pues esta devolución
            * debe implicarprincipalmente a los  sin embargo si busco allí y no
            * encuentro un registro es por que ese articulo no ha sido cargado a ese paciente.
            * Los saldos de la tabla 000030 son los saldos de las aplicaciones automaticas,
            * se deben tomar en cuenta los saldos al paciente del centro de costos donde esta el paciente,
            * y de los centros de costos no hospitalarios que le han cargado al paciente.
            * 
            * Para los otros centros de costos hospitalarios, sea que apliquen o no, como durante el traslado le pueden haber entregado artículos al centro 
            * esto aparecera en los saldos 000004.
            */

			$q1 = " SELECT * "
			. "       FROM " . $bd . "_000004 "
			. "      WHERE Spahis = '" . $pac['his'] . "' "
			. "        AND Spaing = '" . $pac['ing'] . "' "
			. "        AND Spacco = '" . $ccoArr[$codCco]['cod'] . "' "
			. "        AND Spaart = '" . $art . "' "
			. "   ORDER BY Spaart ";
			$err1 = mysql_query($q1, $conex);
			echo mysql_error();
			$num1 = mysql_num_rows($err1);
			if ($num1 > 0)
			{
				$row = mysql_fetch_array($err1);

				/**
                * Inicializo las variables de saldos.  
                * Es necesario saber la cantidad de aprovechamiento y la simple
                * La cantidad encontrada en la tabla 000004 es que no ha sido aplicada
                * por eso la debo guardar aparte.
                */
				$saldoAprov = $row['Spaaen'] - $row['Spaasa']; //Cantidad por fuente aprovechamiento
				$saldoSimple = $row['Spauen'] - $row['Spausa'] - $saldoAprov;

				$saldoAprovNoAplicado = $row['Spaaen'] - $row['Spaasa']; //Cantidad por fuente aprovechamiento
				$saldoSimpleNoAplicado = $row['Spauen'] - $row['Spausa'] - $saldoAprovNoAplicado;
			}

			if ($ccoArr[$codCco]['cod'] == $cco['cod'] or !$ccoArr[$codCco]['hos'])
			{
				/**
                * Sí: $ccoArr[$countCco]['cod'] == $cco['cod']
                * El articulo fue grabado en le CCo donde esta el paciente.
                * Tanto el $saldo como el saldo aprov deben ser cero, a no ser de que:
                * haya estado antes en el servicio, y le hayan quedado cosas pendientes y por mediodel programa derecibo y entrega de pacientes
                * se haya anulado la aplicación de esos artículos y por lo tanto haya quedado un saldo al paciente, y no se haya ni aplicado ni devuelto 
                * en ese otro servicio a donde fue transferido y al volver nuevamente al servicio con centro de costos $cco[cod] este todavía el saldo.
                */

				/**
                * Sí: $ccoArr[$countCco]['hos'] == false =>"off"
                * El centro de costos del saldo no es un centro de costos hospitalario.  
                * Es decir, es un centro de costos donde cargan articulos a pacientes pero que no atiende pacientes.
                * Como por ejemplo un área de Dispensación o central de mezclas en un servicio farmaceutico.
                * Cuando el paciente es trasladado de otro centro de costos a donde se encuantra actualmente es posible que 
                * hubiera sido trasladado con artículos pendientes por aplicar, incluso el artículo que esta buscando la función,
                * la cantidad que hay pendiente del articulo queda en la tabla de saldos (00004).
                * Sin embargo todos los artículos que hayan sido cargados del centro de costos no hospitalario ($ccoArr[$countCco]['cod']
                * mientras el paciente esta en el centro de costos $cco['cod'] han quedado aplicados por que en este
                * se aplica automáticamente todo lo que se carga al paciente. Es decir, no han dejado una diferencia en la tabla de
                * saldos, por esto es necesario buscar la cantidad suceptible de ser devuelta en la tabla de aplicación (000015).			 
                * 
                * Sí $ccoArr[$countCco]['hos'] == "on" NO ENTRA AQUI!!!
                * El centro de costos es un centro de costos hospitalario donde el paciente NO esta.
                * No tiene importancia si es un centro de costos que aplica automáticamente o no, con el saldo encontrado en la tabla de saldos es 
                * suficiente, no hay necesidad de revisar en la tabla de aplicaciones.
                */

				$q1 = " SELECT * "
				. "       FROM " . $bd . "_000030 "
				. "      WHERE Splhis = '" . $pac['his'] . "' "
				. "        AND Spling = '" . $pac['ing'] . "' "
				. "        AND Splart = '" . $art . "' "
				. "        AND Splcco = '" . $ccoArr[$codCco]['cod'] . "' ";
				$err1 = mysql_query($q1, $conex);
				echo mysql_error();
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);

					$saldoAprov = $saldoAprov + $row1['Splaen'] - $row1['Splasa']; //Cantidad por fuente aprovechamiento
					$saldoSimple = $saldoSimple + $row1['Spluen'] - $row1['Splusa'] - ($row1['Splaen'] - $row1['Splasa']);

					$saldoAprovAplicado = $row1['Splaen'] - $row1['Splasa']; //Cantidad por fuente aprovechamiento
					$saldoSimpleAplicado = $row1['Spluen'] - $row1['Splusa'] - $saldoAprovAplicado;
				}
				else
				{
					$saldoSimpleAplicado = 0;
					$saldoAprovAplicado = 0;
				}
			}
			else
			{
				$saldoSimpleAplicado = 0;
				$saldoAprovAplicado = 0;
			}

			if ($saldoAprov + $saldoSimple > 0)
			{
				$datos['cco'][$artCco]['sid'] = '';//;
				$datos['cco'][$artCco]['cid'] = $codCco; //Número del consecutivo del Array ($arrCco) en donde esta el centro de costos
				$datos['cco'][$artCco]['ckb'] = true;
				$datos['cco'][$artCco]['cia'] = $saldoAprov; //Cantidad inicial Aprovechamiento
				$datos['cco'][$artCco]['cit'] = $saldoAprov + $saldoSimple; //
				$datos['cco'][$artCco]['caa'] = $saldoAprovAplicado; //Cantidad aprovechamiento aplicada
				$datos['cco'][$artCco]['csa'] = $saldoSimpleAplicado; //Cantidad Simple aplicada
				$datos['cco'][$artCco]['cana'] = $saldoAprovNoAplicado; //Cantidad aprovechamiento sin aplicar
				$datos['cco'][$artCco]['csna'] = $saldoSimpleNoAplicado; //Cantidad Simple Sin aplicar
				$datos['cco'][$artCco]['cdv'] = 0;
				$datos['cco'][$artCco]['jdv'] = "";
				$datos['cco'][$artCco]['jde'] = "";
				$datos['cco'][$artCco]['cfa'] = 0;
				$datos['cco'][$artCco]['jfa'] = "";
				$datos['cco'][$artCco]['dds'] = "";
				$datos['cco'][$artCco]['cds'] = 0;
				$datos['cco'][$artCco]['msg'] = "";
				$datos['cco'][$artCco]['sob'] = 0;
				$datos['cco'][$artCco]['class'] = "texto";
				$artCco = $artCco + 1;
				$devolver = true;
			}
		}

		if ($devolver)
		{
			return(1);
		}
		else
		{
			return(2);
		}
	}
	else
	{
		$errorMsg = "EL PACIENTE NO TIENE CARGOS DEL ARTICULO";
		return(0);
	}
}

/**
* Por cada registro en la 000003 es un registro en la 000028
* 
* Para las devoluciones:
* Primero fuente simple, 
* 	a. Que no aplico automaticamente.
* 	b. Que si quedo aplicada
* Segundo Fuente de Aprovechamiento,
* 
* Descarte
* 	a. Aprovechamiento
*    b. Simple
* 
* @modified 2007-07-21 Como se crea el campo 000028.Devart se modifica el registro dela devolución para que lo tome en cuenta.
* @modified 2007-07-19 Como se crea el campo 000028.Devapv se modifica el registro dela devolución para que lo tome en cuenta.
* @modified 2007-07-19 Seempiezan a usar las funciones registrarSaldosNoAplica y registrarSaldosAplicacion() en las devoluciones, como reemplazo a la función registrarSaldos().
* @param Integer $devCons 
* @param Array $pac Información del paciente</br>
* 								[his]:Historia del paciente.</br>
* 								[ing]:
* @param Array $art [cod]:Código del artículo.</br>
* 								[can]:cantidad del artículo.</br>
* 								[neg]:Si el artículo permite o no generar negativos en el inventario.
* @param String $jusD 
* @param Doubble $faltante 
* @param String $jusF 
* @param array $cco Información del centro de costos</br>
* 								Debe ingresar:</br>
* 								[cod]:String[4].Código del centro de costos.</br>
* 								[neg]:Boolean.El Centro de costos permite generar negativos en el inventario para todos los artículso.</br>
* 								[apl]:Boolean. Los artículos que se cargan de este centrod e costos se graban como aplicados inmediatamente.
* @param String $ [1]	$tipTrans	Tipo de transacción
* 								C: cargo a cuenta de pacientes
* 								D: Devolución.
* @param Boolean $aprov Si es un aprovechamiento o no.
* @paramArray $usu		Información del usuario.
* 								[usuM]:Código de MAtrix del usuario.
* @param unknown_type $error Almacena todo lo referente con códigos y mensajes de error
* 								[ok]:Descripción corta.</br>
* 								[codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010</br>
* 								[codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.</br>
* 								[descSis]:Descripción del error del sistema.</br>
* @return unknown 
*/
function procesoDev($devCons, $pac, $art, $datos, $jusD, $faltante, $jusF, $descarte, $ddes, $cco, $usuario, &$error, &$msg, $ccoServicioOrigen )
{
	global $bd;
	global $conex;
	global $conex_o;
	global $solicitudCamillero;
	
	// Cantidad a devolver total del artículo
	$cdv = $datos['cdv'];
	// Cantidad cargada por aprovechamiento suceptible de ser devuelta
	$cia = $datos['cia'];
	// Catidad cargada por fuente Simple suceptible de ser devuelta.
	$cis = $datos['cit'] - $datos['cia'];
	// Cantidad cargada por fuente simple que ya fue aplicada cuando se cargo al paciente
	$cantSimpleApl = $datos['csa'];
	// Cantidad cargada por fuente simple NO aplicada
	$cantSimpleNoApl = $datos['csna'];
	// Cantidad cargada por fuente de aprovechamiento que ya fue aplicada cuando se cargo al paciente
	$cantAprovApl = $datos['caa'];
	// Cantidad cargada por fuente de aprovechamiento NO aplicada
	$cantAprovNoApl = $datos['cana'];
	// Inicializo Variables
	$msg = "";
	$return = true;
	$noErroresDeTarifaSaldo = true;
	$exito = 0;

	/**
    * Primero devuelvo por la cantidad simple
    */
	if ($cis > 0 and $cdv > 0)
	{
		if ($cdv >= $cis)
		{
			/**
            * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
            */
			$cdv = $cdv - $cis;
			$art['can'] = $cis;
			$cis = 0;
		}
		else
		{
			/**
            * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
            * Todo se devuelve por Devolución simple
            */
			$art['can'] = $cdv;
			$cdv = 0;
			$cis = $cis - $cdv;
		}
		// echo "<br><b>Entro al cis>0</b><br>";
		// echo "art[can]=".$art['can']."<br>";
		// echo "cis=$cis<br>";
		// echo "cdv=$cdv<br>";
		if (validacionDevolucion($cco, $pac, $art, false, true, &$error))
		{
			if (TarifaSaldo($art, $cco, "D", false, &$error))
			{
				$dronum = "";
				$drolin = "";
				$date = date("Y-m-d");
				$fuente = $cco['fue'];
				if (Numeracion($pac, $fuente, 'D', false, $cco, &$date, &$cns, &$dronum, &$drolin, $pac['dxv'], $usuario, &$error))
				{
					if (registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, &$error))
					{
						$art['ubi'] = 'US';
						$art['ini'] = $art['cod'];
						$art['lot'] = " ";
						if (registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario, &$error))
						{
							/**
                            * Cambio del 2007-09-05
                            */ 
							// La cantidad esta en $art['can']
							$cdvSimple = $art['can'];
							if ($cantSimpleNoApl > 0)
							{
								// echo "<br><i>Simple No aplica</i><br>";
								if ($cdvSimple >= $cantSimpleNoApl)
								{
									/**
                                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple que no aplica
                                    * se deja la cantida restante para que se devuelva pero aplicando auntomaticamente.
                                    */
									$cdvSimple = $cdvSimple - $cantSimpleNoApl;
									$art['can'] = $cantSimpleNoApl;
									$cantSimpleNoApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                                    * Todo se devuelve por Devolución simple
                                    */
									$art['can'] = $cdvSimple;
									$cantSimpleNoApl = $cantSimpleNoApl - $cdvSimple;
									$cdvSimple = 0;
								}
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantSimpleNoApl=$cantSimpleNoApl<br>";
								// echo "cdvSimple=$cdvSimple<br>";
								$cco['apl'] = false;
								if (registrarSaldosNoApl($pac, $art, $cco, false, $usuario, "D", false, &$error))
								{
									$exito = $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvSimple = $cdvSimple + $art['can'];
									$error['ok'] = "LA DEVOLUCION SIMPLE SE EFECTUO. <br>Pero hubo un problema grabando<br>el Movimiento de Aplicación con la cantidad" . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
									/**
                                    * No se pone $return=false por que cabe la posibilidad de que $cdvSimple <= $cantSimpleApl), es decir que
                                    * se pueda hacer la devolución por fuente simple aplicando automáticamente.
                                    * Si no se puede mas adelante el programa pone $return=false.
                                    */
								}
							}

							if ($cantSimpleApl > 0 and $cdvSimple > 0)
							{
								/**
                                * El artículo fue cargado al paciente por fuente simple y se aplico automáticamente
                                */
								if ($cdvSimple >= $cantSimpleApl)
								{
									/**
                                    * La cantidad a devolver es mayor o igual que la cantidad existente grabada por cargo simple que plica automaticamente.
                                    * Sí es mayor significa que hubo un error en el paso anterior, y queda pendiente una cantidad a devolver que debe ser informada al usuario
                                    */
									$cdvSimple = $cdvSimple - $cantSimpleApl; // lo que no se va a poder mover en saldos pero ya se movio en cargos (000002-000003)
									$art['can'] = $cantSimpleApl;
									$cantSimpleApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo simple que aplica automaticamente al paciente es mayor a la cantidad a devolver.
                                    * Lo que falta por devolver se devuelve por cargo simple que aplica automáticamente
                                    */
									$art['can'] = $cdvSimple;
									$cantSimpleApl = $cantSimpleApl - $cdvSimple;
									$cdvSimple = 0;
								}
								// echo "<br><i>Simple Aplica</i><br>";
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantSimpleApl=$cantSimpleApl<br>";
								// echo "cdvSimple=$cdvSimple<br>";
								$cco['apl'] = true; //para que aplique automáticamente
								if (registrarSaldosAplicacion($pac, $art, $cco, false, $usuario, "D", false, &$error))
								{
									// Por aplicar automáticamente es necesario generar el registro en la aplicación
									if (registrarAplicacion($pac, $art, $cco, false, $date, date("g:i - A"), $usuario, "D", $dronum, $drolin, &$error))
									{
										$error['ok'] = $error['ok'] . "Pero hubo un problema grabando<br>el Movimiento de Aplicación de " . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
										$return = false;
									}

									$exito = $exito + $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvSimple = $cdvSimple + $art['can'];
								}
							}

							if ($exito > 0)
							{
								/**
                                * Se realizo la devolución exitosa de la cantidad almacenada en $exito
                                * Esta cantidad que se modifico exitosamente en las tablas de saldos (000004) deve ser ingrsada a la
                                * tabla de devoluciones.
                                */
								if ($faltante > $exito)
								{
									$faltante = $faltante - $exito;
									$fal = $exito;
								}
								else
								{
									$fal = $faltante;
									$faltante = 0;
								}

								if (!registrarDevFact($devCons, $dronum, $drolin, $exito, $jusD, $fal, $jusF, $usuario, &$error))
								{
									$error['ok'] = $error['ok'] . "Hubo un problema grabando<br>el Movimiento de devolucion a<br>" . $cco['cod'] . "-" . $cco['nom'];
								}
								else{
									if( !$solicitudCamillero ){
										if( esTraslado( $cco['cod'] ) && true ){ //echo "paso11111....$ccoCod";
											peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoServicioOrigen, $cco['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
										}
										$solicitudCamillero = true;
									}
								}
								// Informo al usuario como qudo la devolución
								$msg = "<IMG SRC='/matrix/images/medical/root/feliz.ico'>LA DEVOLUCIÓN SIMPLE DE " . $exito . " " . $art['uni'] . " FUE REALIZADA CON EXITO.";
								$return = true;
							}

							if ($cdvSimple > 0 or !$return)
							{
								/**
                                * Hubo un fallo y no se devolvio toda la cantidad de fuente simple que se debia resolver
                                * o no se realizo el insert de la aplicación
                                */
								$msg = $msg . "<br>" . $error['ok'];
								$return = false;
							}
							/**
                            * Fin de los cambios de 2007-09-05
                            */

						} // if de registrarDetalleCargo()
						else
						{
							$return = false;
						}
					} //if de registrarItdro()
					else
					{
						$return = false;
					}
				} //if de Numeración()
				else
				{
					$return = false;
				}
			}
			else
			{
				// Sin tarifa, saldo
				$noErroresDeTarifaSaldo = false;
			}
		}
		else
		{
			/**
            * Sin  cantidad SIMPLE a devolver.
            * Ponemos toda la cantidad pendiente de devolver para que
            * lo intente devolver por aprovechamientos.
            */
			$cdv = $datos['cdv'];
		}
	}
	if (!$return)
	{
		$msg = "LA DEVOLUCIÓN SIMPLE DE " . $art['can'] . " " . $art['uni'] . " TUVO UN INCONVENIENTE:<br> " . $error['ok'] . "";
	}

	if ($cdv > 0 and $noErroresDeTarifaSaldo)
	{
		if ($cdv >= $cia)
		{
			/**
            * La cantidad a devolver es igual que la cantidad grabada por cargo simple
            * NO debería ser nunca mayor a cia por que eso querria decir que va a devolver mas cantidad de la suceptible a devolver
            */
			$cdv = $cdv - $cia;
			$art['can'] = $cia;
			$cia = 0;
		}
		else
		{
			/**
            * La cantidad grabada por cargo de aprovechamiento es mayor a la cantidad a devolver.
            */
			$art['can'] = $cdv;
			$cia = $cia - $cdv;
			$cdv = 0;
		}
		// echo "<br><b>Entro al cdv>0</b><br>";
		// echo "art[can]=".$art['can']."<br>";
		// echo "cis=$cis<br>";
		// echo "cdv=$cdv<br>";
		if (validacionDevolucion($cco, $pac, $art, true, true, &$error))
		{
			if (TarifaSaldo($art, $cco, "D", true, &$error))
			{
				$dronum = "";
				$drolin = "";
				$date = date("Y-m-d");
				$fuente = $cco['fap'];
				if (Numeracion($pac, $fuente, 'D', true, $cco, &$date, &$cns, &$dronum, &$drolin, $pac['dxv'], $usuario, &$error))
				{
					if (registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, &$error))
					{
						$art['ubi'] = 'US';
						$art['ini'] = $art['cod'];
						$art['lot'] = " ";
						if (registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario, &$error))
						{
							/**
                            * Cambio del 2007-09-06
                            */ 
							// La cantidad esta en $art['can']
							$cdvAprov = $art['can'];
							$exito = 0;
							if ($cantAprovNoApl > 0)
							{
								if ($cdvAprov >= $cantAprovNoApl)
								{
									/**
                                    * La cantidad a devolver es mayor que la cantidad grabada por cargo de aprovechamiento que NO aplica
                                    * se deja la cantidad restante para que se devuelva pero aplicando auntomaticamente.
                                    */
									$cdvAprov = $cdvAprov - $cantAprovNoApl;
									$art['can'] = $cantAprovNoApl;
									$cantAprovNoApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo por aprovechamiento al paciente mayor a la cantidad a devolver.
                                    * Todo se devuelve por Devolución por aprovechamiento que NO aplica.
                                    */
									$art['can'] = $cdvAprov;
									$cantAprovNoApl = $cantAprovNoApl - $cdvAprov;
									$cdvAprov = 0;
								}
								// echo "<br><i>APROV No aplica</i><br>";
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantAprovNoApl=$cantAprovNoApl<br>";
								// echo "cdvAprov=$cdvAprov<br>";
								$cco['apl'] = false;

								/**
                                * Se mueve el saldo de la la tabla de saldos del paciente
                                */
								if (registrarSaldosNoApl($pac, $art, $cco, true, $usuario, "D", false, &$error))
								{
									$exito = $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvAprov = $cdvAprov + $art['can'];
									$error['ok'] = "LA DEVOLUCION SIMPLE SE EFECTUO. <br>Pero hubo un problema grabando<br>el Movimiento de Aplicación con la cantidad" . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
									/**
                                    * No se pone $return=false por que cabe la posibilidad de que $cdvAprov <= $cantAprovApl), es decir que
                                    * se pueda hacer la devolución por fuente simple aplicando automáticamente.
                                    * Si no se puede mas adelante el programa pone $return=false.
                                    */
								}
							}

							if ($cantAprovApl > 0 and $cdvAprov > 0)
							{
								/**
                                * El artículo fue cargado al paciente por fuente de aprovechamiento y se aplico automáticamente
                                */
								if ($cdvAprov >= $cantAprovApl)
								{
									/**
                                    * La cantidad a devolver es mayor o igual que la cantidad existente grabada por cargo de aprovechamiento que aplica automaticamente.
                                    * Sí es mayor significa que hubo un error en el paso anterior, y queda pendiente una cantidad a devolver que debe ser informada al usuario
                                    */
									$cdvAprov = $cdvAprov - $cantAprovApl; // lo que no se va a poder mover en saldos pero ya se movio en cargos (000002-000003)
									$art['can'] = $cantAprovApl;
									$cantAprovApl = 0;
								}
								else
								{
									/**
                                    * La cantidad grabada por cargo de aprovechamiento que aplica automaticamente es mayor a la cantidad a devolver.
                                    * Lo que falta por devolver se devuelve por cargo de aprovechamiento que aplica automáticamente
                                    */
									$art['can'] = $cdvAprov;
									$cantAprovApl = $cantAprovApl - $cdvAprov;
									$cdvAprov = 0;
								}
								// echo "<br><i>APROV  Aplica</i><br>";
								// echo "art[can]=".$art['can']."<br>";
								// echo "cantAprovApl=$cantAprovApl<br>";
								// echo "cdvAprov=$cdvAprov<br>";
								$cco['apl'] = true; //para que aplique automáticamente
								if (registrarSaldosAplicacion($pac, $art, $cco, true, $usuario, "D", false, &$error))
								{
									// Por aplicar automáticamente es necesario generar el registro en la aplicación
									if (registrarAplicacion($pac, $art, $cco, true, $date, date("g:i - A"), $usuario, "D", $dronum, $drolin, &$error))
									{
										$error['ok'] = $error['ok'] . "Pero hubo un problema grabando<br>el Movimiento de Aplicación de " . $art['can'] . " " . $art['uni'] . "<br>Comunique esta infomrmación a Sistemas";
										$return = false;
									}

									$exito = $exito + $art['can'];
								}
								else
								{
									/**
                                    * No se efectuo el movimiento en los saldos.
                                    * Así que se pone la cantidad simple a devolver en el máximo
                                    */
									$cdvAprov = $cdvAprov + $art['can'];
									/**
                                    * Error realizando el cambio en cargos
                                    * $error['ok'] = "LA DEVOLUCION SIMPLE SE EFECTUO. <br>Pero hubo un problema grabando<br>el Movimiento de Aplicación con la cantidad".$art['can']." ".$art['uni']."<br>Comunique esta infomrmación a Sistemas";
                                    * $return=false;
                                    */
								}
							}

							if ($exito > 0)
							{
								/**
                                * Se realizo la devolución exitosa de la cantidad almacenada en $exito
                                * Esta cantidad que se modifico exitosamente en las tablas de saldos (000004) deve ser ingrsada a la
                                * tabla de devoluciones.
                                */
								if ($faltante <= 0)
								{
									$jusF = " ";
								}
								if (!registrarDevFact($devCons, $dronum, $drolin, $exito, $jusD, $faltante, $jusF, $usuario, &$error))
								{
									$error['ok'] = $error['ok'] . "Hubo un problema grabando<br>el Movimiento de devolucion a<br>" . $cco['cod'] . "-" . $cco['nom'];
								}
								else{
									if( !$solicitudCamillero ){
										
										if( esTraslado( $cco['cod'] ) && true ){ //echo "paso2222....";
											peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $ccoServicioOrigen, $cco['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
										}
										$solicitudCamillero = true;
									}
								}
								// Informo al usuario como qudo la devolución
								$msg = "<IMG SRC='/matrix/images/medical/root/feliz.ico'>LA DEVOLUCIÓN SIMPLE DE " . $exito . " " . $art['uni'] . " FUE REALIZADA CON EXITO.";
								$return = true;
							}

							if ($cdvAprov > 0 or !$return)
							{
								/**
                                * Hubo un fallo y no se devolvio toda la cantidad de fuente simple que se debia resolver
                                * o no se realizo el insert de la aplicación
                                */
								$msg = $msg . "<br>" . $error['ok'];
								$return = false;
							}
							/**
                            * Fin de los cambios de 2007-09-06
                            */
						} // if de registrarDetalleCargo()
						else
						{
							$return = false;
						}
					} //if de registrarItdro()
					else
					{
						$return = false;
					}
				} //if de Numeración()
				else
				{
					$return = false;
				}
			}
			else
			{
				// Sin tarifa, saldo
				$return = false;
			}
			// echo "si dio la validación SIMPLE cco['cod'=".$cco['cod'];
		}
		else
		{
			// Sin  cantidad a devolver
			$error['ok'] = "ALGUIEN HIZO UNA DEVOLUCION, UNA APLICACIÓN, O UN DESCARTE, DE ESTE ARTÍCULO A ESTE PACIENTE, MIENTRAS UD. HACIA ESTA DEVOLUCION. Por este motivo " . $art['can'] . " " . $art['uni'] . " no pudieron ser devultas.";
			// echo "<b>ya si se piofio del todo la validación</b><br>";
			$return = false;
		}
	}

	if (!$return)
	{
		$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>LA DEVOLUCIÓN SIMPLE DE " . $art['can'] . " " . $art['uni'] . " TUVO UN INCONVENIENTE: " . $error['ok'] . "";
	}

	if ($descarte > 0)
	{
		/**
        * La transacción mas importente de los descartes es en movimiento de los saldos, 
        * por que el registro en la tabla de desacartes en con fines informativos, 
        * y el registro de la aplicación es solo para que haya concordancia en las tablas 
        * pero que no se realize no implica problemas en los rotos procesos del sistema,
        * como silo hace un aml movimiento de saldos
        */ 
		// echo "<br><b>Descarte Mayor que Cero</b><br>";
		$exito = 0;
		$noDescarte = 0;
		$noAplicado = 0;
		/**
        * Realizar el descarte normal o por aprovechamiento.
        */
		if ($cia > 0)
		{
			// echo "<b>IniciandoAprov NoApl</b></br>";
			// echo "cantAprovNoApl=".$cantAprovNoApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantAprovNoApl > 0)
			{
				// Descartar primero lo que hay en saldo no aplicado
				if ($descarte >= $cantAprovNoApl)
				{
					/**
                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantAprovNoApl;
					$art['can'] = $cantAprovNoApl;
					$cantAprovNoApl = 0;
				}
				else
				{
					/**
                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$descarte = 0;
					$cantAprovNoApl = $cantAprovNoApl - $descarte;
				}
				// echo "Despues de repartir<br>";
				// echo "cantAprovNoApl=".$cantAprovNoApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				// echo "cia=$cia<br>";
				$cco['apl'] = false;
				if (validacionDevolucion($cco, $pac, $art, true, false, &$error))
				{
					if (registrarSaldosNoApl($pac, $art, $cco, true, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, true, false, $art, $ddes, $usuario, &$error))
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/feliz.ico'>EL DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." FUE REALIZADA CON EXITO.";
							$exito = $exito + $art['can'];
						}
						else
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/Malo.ico'LA DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." TUVO UN INCONVENIENTE: ".$error['ok'].">";
							// $return=false;
							/**
                            * El descarte si se realizo hay que informar 
                            * y mostrar el error de que no sera visible en el reporte ni de la devolución ni de recepción de la devolución
                            */
							$noDescarte = $art['can'];
							// $descarte=$descarte+$cantAprovNoApl;
						}
					}
					else
					{
						/**
                        * No se pudo modifcar el saldo y no se hizo ninguna transacción
                        * Se restaura la cantidad a la cantidad que habia para intentar hacer el descarte por fuente simple que aplica.
                        */
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que NO aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descrte
                    */
					$descarte = $descarte + $art['can'];
				}
			}
			// echo "<br><b>Iniciando AprovApl</b></br>";
			// echo "cantAprovApl=".$cantAprovApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantAprovApl > 0 and $descarte > 0)
			{
				// Descartar  lo que hay en saldo no aplicado
				if ($descarte >= $cantAprovApl)
				{
					/**
                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantAprovApl;
					$art['can'] = $cantAprovApl;
					$cantAprovApl = 0;
				}
				else
				{
					/**
                    * La cantidad rabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$descarte = 0;
					$cantAprovApl = $cantAprovApl - $descarte;
				}
				// echo "Despues de repartir<br>";
				// echo "cantAprovApl=".$cantAprovApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				// echo "cia=$cia<br>";
				$cco['apl'] = true;
				if (validacionDevolucion($cco, $pac, $art, true, false, &$error))
				{
					// El registro de saldos es la transacción mas importante
					if (registrarSaldosAplicacion($pac, $art, $cco, true, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, true, true, $art, $ddes, $usuario, &$error))
						{
							$exito = $exito + $art['can'];
							$exitoDescarte = true;
						}
						else
						{
							/**
                            * Hubo un error, se movio el saldo pero no hizo el registro del descarte.
                            * Lo que indica que no va a ser visible en la recepción de la devolución, ni en el reporte
                            * de la devolución.
                            */
							$noDescarte = $noDescarte + $art['can'];
							$exitoDescarte = false;
						}

						if (!registrarAplicacion($pac, $art, $cco, true, date('Y-m-d'), date("g:i - A"), $usuario, "D", 0, 0, &$error))
						{
							/**
                            * Hubo un error, no se registro la aplicación.
                            * Lo que no es mayor cosa por que si se movio el saldo.
                            * Lo único que no se hizo fue el registro de la aplicación.
                            */
							$noAplicado = $noAplicado + $art['can'];
							if ($exitoDescarte)
							{
								$exito = $exito - $art['can'];
							}
						}
					}
					else
					{
						// Hubo un prblema con el saldo
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descrte
                    */
					$descarte = $descarte + $art['can'];
				}
			}
		}

		if ($descarte > 0)
		{
			/**
            * Primero se descartan las cantidades Simples no aplicadas, sí las hay
            */ 
			// echo "<br><b>Iniciando SimpleNoApl</b></br>";
			// echo "cantSimpleNoApl=".$cantSimpleNoApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantSimpleNoApl > 0)
			{
				// Hay cantidades Simples no aplicadas suceptibles de ser devueltas.
				if ($descarte >= $cantSimpleNoApl)
				{
					/**
                    * La cantidad a devolver es mayor que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantSimpleNoApl;
					$art['can'] = $cantSimpleNoApl;
					$cantSimpleNoApl = 0;
				}
				else
				{
					/**
                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$cantSimpleNoApl = $cantSimpleNoApl - $descarte;
					$descarte = 0;
				}
				// echo "Despues de repartir<br>";
				// echo "cantSimpleNoApl=".$cantSimpleNoApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				$cco['apl'] = false;
				if (validacionDevolucion($cco, $pac, $art, false, false, &$error))
				{
					if (registrarSaldosNoApl($pac, $art, $cco, false, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, false, false, $art, $ddes, $usuario, &$error))
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/feliz.ico'>EL DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." FUE REALIZADA CON EXITO.";
							$exito = $exito + $art['can'];
						}
						else
						{
							// $msg=$msg."<IMG SRC='/matrix/images/medical/root/Malo.ico'LA DESCARTE POR APROVECHAMIENTO DE ".$art['can']." ".$art['uni']." TUVO UN INCONVENIENTE: ".$error['ok'].">";
							// $return=false;
							$noDescarte = $noDescarte + $art['can'];
							// echo "<b>no se hizo el descarte</b></br>";
							// $descarte=$descarte+$cantSimpleNoApl;
						}
					}
					else
					{
						/**
                        * No se pudo modifcar el saldo y no se hizo ninguna transacción
                        * Se restaura la cantidad a la cantidad que habia para intentar hacer el descarte por fuente simple que aplica.
                        */ 
						// echo "<b>no se hicioron los saldos</b></br>";
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descarte.
                    */ 
					// echo "<b>mala la validación de la dev</b></br>";
					$descarte = $descarte + $art['can'];
				}
			}

			/**
            * Segundo se descartan las cantidades Simples Aplicadas, sí las hay
            */
			// echo "<br><b>Iniciando Simple Apl</b></br>";
			// echo "cantSimpleApl=".$cantSimpleApl."<br>";
			// echo "descarte=$descarte<br>";
			if ($cantSimpleApl > 0 and $descarte > 0)
			{
				// Descartar primero lo que hay en saldo no aplicado
				if ($descarte >= $cantSimpleApl)
				{
					/**
                    * La cantidad a devolver IGUAL que la cantidad grabada por cargo simple
                    */
					$descarte = $descarte - $cantSimpleApl;
					$art['can'] = $cantSimpleApl;
					$cantSimpleApl = 0;
				}
				else
				{
					/**
                    * La cantidad grabada por cargo simple es mayor a la cantidad a devolver.
                    * Todo se devuelve por Devolución simple
                    */
					$art['can'] = $descarte;
					$descarte = 0;
					$cantSimpleApl = $cantSimpleApl - $descarte;
				}
				// echo "Despues de repartir<br>";
				// echo "cantSimpleApl=".$cantSimpleApl."<br>";
				// echo "art[can]=".$art['can']."<br>";
				// echo "descarte=$descarte<br>";
				$cco['apl'] = true;
				if (validacionDevolucion($cco, $pac, $art, false, false, &$error))
				{
					// El registro de saldos es la transacción mas importante
					if (registrarSaldosAplicacion($pac, $art, $cco, false, $usuario, "D", false, &$error))
					{
						if (registrarDescarte($devCons, $pac, $cco, false, true, $art, $ddes, $usuario, &$error))
						{
							$exito = $exito + $art['can'];
							$exitoDescarte = true;
						}
						else
						{
							/**
                            * Hubo un error, se movio el saldo pero no hizo el registro del descarte ni la aplicación.
                            * Lo que indica que no va a ser visible en la recepción de la devolución., ni en el reporte
                            * de la devolución.
                            */ 
							// echo "<b>no se hizo el descarte</b></br>";
							$noDescarte = $noDescarte + $art['can'];
							$exitoDescarte = false;
						}

						if (!registrarAplicacion($pac, $art, $cco, false, date('Y-m-d'), date("g:i - A"), $usuario, "D", 0, 0, &$error))
						{
							/**
                            * Hubo un error, no se registro la aplicación.
                            * Lo que no es mayor cosa por que si se movio el saldo.
                            * Lo único que no se hizo fue el registro de la aplicación.
                            */

							$noAplicado = $noAplicado + $art['can'];
							if ($exitoDescarte)
							{
								$exito = $exito - $art['can'];
							}
						}
					}
					else
					{
						// echo "<b>no se hicioron los saldos</b></br>";
						// Hubo un prblema con el saldo
						$descarte = $descarte + $art['can'];
					}
				}
				else
				{
					/**
                    * No hay suficiente cantidad para hacer el descarte por aprovechamiento que aplica, 
                    * las condiciones ambiaron entre el momento en que se leyo el artículo y 
                    * el mometo en que se va a efectuar el descrte
                    */ 
					// echo "<b>mala la validación de la dev</b></br>";
					$descarte = $descarte + $art['can'];
				}
			}

			if ($exito > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/feliz.ico'>EL DESCARTE DE " . $exito . " " . $art['uni'] . " FUE REALIZADO CON EXITO.";
			}
			if ($descarte > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>El descarte de " . $descarte . " " . $art['uni'] . " tuvo uno o mas inconvenientes. INTENTELO NUEVAMENTE>";
				$return = false;
			}
			if ($noAplicado > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>El descarte de " . $noAplicado . " " . $art['uni'] . " se hizo, sinembargo presento un problema de aplicación, por favor comuniquelo a sistemas.>";
				$return = false;
			}

			if ($noDescarte > 0)
			{
				$msg = $msg . "<IMG SRC='/matrix/images/medical/root/Malo.ico'>El descarte de " . $noDescarte . " " . $art['uni'] . " se hizo, sinembargo presento un problema de por lo tanto no sera vicible en el reporte de devolución ni en el reporte de recibo de devolución, por favor comuniquelo a sistemas.>";
				$return = false;
			}
		}
	}
	return ($return);
}

/**
* Valida que
*    * La cantidad a descartar no sea mayor que el saldo disponible.</br>
*    * La cantidad a devolver no sea mayor que el saldo disponible.</br>
*    * La cantidad a faltante no sea mayor que la cantidad a devolver.</br>
*    * Si la cantidad faltante es mayor que cero (0) la justificación no sea vacía.</br>.
*    * La suma de la devolución y el descarte sea menor que el saldo disponible.</br>
* 
* @mofified 2007-08-13 Se introduce el uso de la variable $datos['sob'] la cual contiene el valor del sobrante
* @created 2007-06-07
* @author Ana María Betancur V. 
* @param Array $datos Información de las cantidades:</br>
* 						Información que ingresa a la función.
* 						[csa]:Saldo disponible para devolver o descartar.</br>
* 						[cdv]:Cantidad a devolver.</br>
* 						[cfa]:Cantidad faltante, es decir que no sera enviada al servicio qu graba.</br>
* 						[jfa]:Justificación del faltante.</br>
* 						[cds]:Cantida a descartar.</br>
* 						[sob]:Cantidad Sobrante.
* @return boolean 
*/
function validacionBasica(&$datos, $jdv, $ima, $art, $cco)
{

	global $conex;
	global $bd;
	global $emp;

	if (($datos['cdv'] - floor($datos['cdv'])) != 0)
	{
		$datos['msg'] = "Este centro de costos SOLO permite devolver<br>cantidades enteras.La fracción debe ir en el Descarte.";
		$datos['cdv'] = floor($datos['cdv']);
		$datos['cds'] = $datos['cdv'] - floor($datos['cdv']);
		return (false);
	}

	if (($datos['cfa'] - floor($datos['cfa'])) != 0)
	{
		$datos['msg'] = "Este centro de costos SOLO permite ingresar<br>faltantes enteros.La fracción debe ir en el Descarte.";
		return (false);
	}

	if($datos['cdv']!=0)
	{
		//consultamos que el articulo a devolver no haga parte de un articulo que tenga cantidad minima facturable
		$q = " SELECT Arecde "
		. "      FROM " . $bd . "_000008 "
		. "     WHERE Areces = '" . $art . "' "
		. "     And   Arecco= '" .  $cco . "' ";
		$err1 = mysql_query($q, $conex);

		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			if($datos['cdv']-$row1[0] < 0 or ($datos['cdv']%$row1[0])!=0)
			{
				$mod=$datos['cdv']%$row1[0];
				$ent=$datos['cdv']-$mod;
				
				$datos['msg'] = "La cantidad a devolver debe ser ".$ent."<br>. En el descarte debe ir el resto (".$mod.")";
				return (false);
			}
		}
	}

	if ($datos['cdv'] + $datos['cds'] + $datos['sob'] > 0)
	{
		// Cantidad a descartar menor que el saldo.
		if ($datos['cds'] <= $datos['cit'])
		{
			// Cantidad a devolver menor que el saldo.
			if ($datos['cdv'] <= $datos['cit'])
			{
				// cantidad Faltante mayor que cero
				if ($datos['cfa'] > 0)
				{
					// Cantidad faltante menor que la devolución??
					if ($datos['cfa'] <= $datos['cdv'])
					{
						// Existe justificación del faltante
						if ($datos['jfa'] == "")
						{
							$datos['msg'] = "Debe seleccionar una<br>justificación para el faltante";
							return (false);
						}
					}
					else
					{
						$datos['msg'] = "El Faltante NO <br>puede superar la Devolución";
						return (false);
					}
				} //cantidad Faltante mayor que cero
				if ($jdv and $datos['cdv'] > 0 and $datos['jdv'] == "")
				{
					$datos['msg'] = "Debe seleccionar una<br>justificación para la Devolución";
					return (false);
				}
				if ($datos['cds'] > 0 and $datos['dds'] == "")
				{
					$datos['msg'] = "Debe seleccionar un<br>destino para el descarte";
					return (false);
				}
				// La suma del Descarte y la devolución debe ser menor al saldo disponible
				if (($datos['cdv'] + $datos['cds']) > $datos['cit'])
				{
					$datos['msg'] = "La suma de la devolución y el <br>descarte no debe ser mayor a " . $datos['cit'];
					return (false);
				}
				else
				{
					return (true);
				}
			} //Cantidad a devolver menor que el saldo.
			else
			{
				$datos['msg'] = "No puede Devolver <br>mas de " . $datos['cit'];
				return (false);
			}
		} //Cantidad a descartar menor que el saldo.
		else
		{
			$datos['msg'] = "No puede Descartar <br>mas de." . $datos['cit'];
			return (false);
		}
	}
	else
	{
		$datos['msg'] = "Como ha seleccionado el artículo<br>La suma de la cantidad a devolver y la cantidad a descartar debe ser mayor que cero y menor a " . $datos['cit'];
		return (false);
	}
}

function crearArrayJus(&$jus)
{
	global $bd;
	global $conex;

	$q = "SELECT * "
	. "      FROM " . $bd . "_000023 "
	. "     WHERE Jusest = 'on' ";
	$err = mysql_query($q, $conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$f = 0;
		$d = 0;
		$s = 0;
		for($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($err);
			if ($row['Justip'] == "F")
			{
				$jus['F'][$f] = $row['Juscod'] . "-" . $row['Jusdes'];
				$f = $f + 1;
			} elseif ($row['Justip'] == "D")
			{
				$jus['D'][$d] = $row['Juscod'] . "-" . $row['Jusdes'];
				$d = $d + 1;
			} elseif ($row['Justip'] == "S")
			{
				$jus['S'][$s] = $row['Juscod'] . "-" . $row['Jusdes'];
				$s = $s + 1;
			}
		}
	}
}

function recuperarDatosIngresoPacCco ($pac, $cco, &$fecRecibo, &$horRecibo, &$errMsg)
{
	global $bd;
	global $conex;

	/*  $q = " SELECT MAX(Eyrnum) as Eyrnum "
	. "       FROM " . $bd . "_000017 "
	. "      WHERE Eyrhis = '" . $pac['his'] . "' "
	. "        AND Eyring = '" . $pac['ing'] . "' "
	. "        AND Eyrsde = '" . $cco['cod'] . "' "
	. "        AND Eyrtip = 'Recibo' ";
	$err = mysql_query($q, $conex);
	echo mysql_error();
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
	$row = mysql_fetch_array($err);
	if ($row[0] == "")
	{
	$errMsg = "Es posible que el paciente se encuentre en proceso de traslado a otro servicio.<br>Verifique y vuelva a intentarlo";
	}
	else
	{
	$q1 = " SELECT Fecha_data,Hora_data "
	. "       FROM " . $bd . "_000017 "
	. "      WHERE Eyrnum = '" . $row['Eyrnum'] . "' ";
	$err1 = mysql_query($q1, $conex);
	echo mysql_error();
	$num1 = mysql_num_rows($err1);
	if ($num1 > 0)
	{
	$row1 = mysql_fetch_array($err1);

	$fecRecibo = $row1['Fecha_data'];
	$horRecibo = $row1['Hora_data'];
	return (true);
	}
	else
	{
	$errMsg = "NO fue posible encontrar el traslado del paciente al Servicio, intentelo nuevamente <br> y si no tiene exito comuniquese con Sistemas.";
	}
	}
	}
	else
	{
	$errMsg = "NO fue posible encontrar el traslado del paciente al Servicio,<br> intentelo nuevamente.<br>";
	return (false);
	} */
	return true;
}

function hiddenArt($art, $prefijo)
{
	echo "<input type='hidden' name ='" . $prefijo . "[cod]' value='" . $art['cod'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[nom]' value='" . $art['nom'] . "'>";

	if ($art['neg'])
	{
		echo "<input type='hidden' name ='" . $prefijo . "[neg]' value='1'>";
	}
	else
	{
		echo "<input type='hidden' name ='" . $prefijo . "[aneg]' value=''>";
	}
	if ($art['act'])
	{
		echo "<input type='hidden' name ='" . $prefijo . "[act]' value='1'>";
	}
	else
	{
		echo "<input type='hidden' name ='" . $prefijo . "[act]' value=''>";
	}
	if ($art['jdv'])
	{
		echo "<input type='hidden' name ='" . $prefijo . "[jdv]' value='1'>";
	}
	else
	{
		echo "<input type='hidden' name ='" . $prefijo . "[jdv]' value=''>";
	}

	if ($art['cmz'])
	{
		echo "<input type='hidden' name ='" . $prefijo . "[cmz]' value='1'>";
	}
	else
	{
		echo "<input type='hidden' name ='" . $prefijo . "[cmz]' value=''>";
	}

	echo "<input type='hidden' name ='" . $prefijo . "[class]' value='" . $art['class'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[uni]' value='" . $art['uni'] . "'>";
}

function hiddenPac($pac, $prefijo)
{
	echo "<input type='hidden' name ='" . $prefijo . "[his]' value='" . $pac['his'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[ing]' value='" . $pac['ing'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[nom]' value='" . $pac['nom'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[doc]' value='" . $pac['doc'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[tid]' value='" . $pac['tid'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[dxv]' value='" . $pac['dxv'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[sac]' value='" . $pac['sac'] . "'>";
	echo "<input type='hidden' name ='" . $prefijo . "[act]' value='1'>";
}

function hiddenCco($cco, $prefijo)
{
	echo "<input type='hidden' name='" . $prefijo . "[cod]' value='" . $cco['cod'] . "'>";
	echo "<input type='hidden' name='" . $prefijo . "[nom]' value='" . $cco['nom'] . "'>";
	if ($cco['neg'])
	{
		echo "<input type='hidden' name='" . $prefijo . "[neg]' value='1'>";
	}
	else
	{
		echo "<input type='hidden' name='" . $prefijo . "[neg]' value=''>";
	}
	if ($cco['apl'])
	{
		echo "<input type='hidden' name='" . $prefijo . "[apl]' value='1'>"; //Se aplica automáticamente
	}
	else
	{
		echo "<input type='hidden' name='" . $prefijo . "[apl]' value=''>"; // NO Se aplica automáticamente
	}

	if ($cco['sel'])
	{
		echo "<input type='hidden' name='" . $prefijo . "[sel]' value='1'>"; //Se aplica automáticamente
	}
	else
	{
		echo "<input type='hidden' name='" . $prefijo . "[sel]' value=''>"; // NO Se aplica automáticamente
	}
	if ($cco['apr'])
	{
		echo "<input type='hidden' name='" . $prefijo . "[apr]' value='1'>"; //Permite Aprovechamientos
	}
	else
	{
		echo "<input type='hidden' name='" . $prefijo . "[apr]' value=''>"; //NO Permite aprovechamientos
	}
	if ($cco['ima'])
	{
		echo "<input type='hidden' name='" . $prefijo . "[ima]' value='1'>"; //Permite Aprovechamientos
	}
	else
	{
		echo "<input type='hidden' name='" . $prefijo . "[ima]' value=''>"; //NO Permite aprovechamientos
	}
	if ($cco['hcr'])
	{
		echo "<input type='hidden' name='" . $prefijo . "[hcr]' value='1'>"; //Permite Aprovechamientos
	}
	else
	{
		echo "<input type='hidden' name='" . $prefijo . "[hcr]' value=''>"; //NO Permite aprovechamientos
	}
	if ($cco['hos'])
	{
		echo "<input type='hidden' name='" . $prefijo . "[hos]' value='1'>"; //Permite Aprovechamientos
	}
	else
	{
		echo "<input type='hidden' name='" . $prefijo . "[hos]' value=''>"; //NO Permite aprovechamientos
	}
	// echo "<input type='hidden' name='".$prefijo."[phm]' value='".$cco['phm']."'>";*//Prefijo Historia por Matrix 2007-06-18 No hace falta enviarlo
	echo "<input type='hidden' name='" . $prefijo . "[fue]' value='" . $cco['fue'] . "'>"; //Fuente sencilla
	echo "<input type='hidden' name='" . $prefijo . "[fap]' value='" . $cco['fap'] . "'>"; //Fuente de aprovechamientos
}

/**
* AQUI EMPIEZA EL PROGRAMA
*/
if (!isset($_SESSION['user']))
echo "error";
else
{
	include_once("cenpro/devolucionCM.php");
	include_once("movhos/validacion_hist.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("root/empresa.php");
	include_once("root/magenta.php");
	include_once("root/barcod.php");

	

	

	
	$solicitudCamillero = false;
	$seHizoSolicitudCamillero = false;

	//modificacion 2007-11-21 pregunto si se puede utilizar el programa, se restringen unas horas en la tabla 50 de root

	$horario='';
	if (ConsultarHorario(&$horario, $emp))
	{

		connectOdbc(&$conex_o, 'inventarios');

		echo "<table align='center' border='0'>";
		echo "<td class='tituloSup' >DEVOLUCIONES</td></tr>";
		echo "<td class='tituloPeq' >devAplAutomatica.php Versión 2012-12-03<br><br></td></tr>";
		echo "<tr>";

		if (true || $conex_o != 0)
		{
			if (isset($user) and !isset($usuario))
			{
				/**
				* Se llamo por el portal de matrix y el $user es una variable de sesiión
				*/
				$usuario = substr($user, 2);
			}

			echo "<center><table border='0'>";
			echo "<form name='devApl' action='' method='POST'> ";
			if (!isset($usuario))
			{
				// No se ha ingresado el código del usuario.
				echo "<tr><td class='titulo1'>CÓDIGO NOMINA: </font>";

				?>
					<input type='text' cols='10' name='usuario'></td></tr>	
					<script language="JAVASCRIPT" type="text/javascript">
					document.carga.usuario.focus();
					</script>
				<?php

				echo"<tr><td align=center class='titulo1'><input type='submit' value='ACEPTAR'></td></tr><tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr></form>";
			}
			elseif (!isset($pac['his']) and !isset($historia))
			{
				// Se ha ingresado el código de nomina mas no el número de la historia
				if (!isset($ccoCod))
				{
					// Busqueda del centro de costos al que pertenece el usuario.
					$q = "SELECT Cc "
					. "FROM 	root_000025 "
					. "WHERE	Empleado = '" . $usuario . "' ";
					$err = mysql_query($q, $conex);
					$num = mysql_num_rows($err);
					if ($num > 0)
					{
						$row = mysql_fetch_array($err);
						$cco['cod'] = $row['Cc'];
						$ok = getCco(&$cco, "D", $emp);
					}
					else
					{
						/**
                    * No esta el usuario en Matrix
                    */
						$pac['his'] = '0';
						$art['cod'] = "NO APLICA";
						$error['codInt'] = "0002";
						$cco['cod'] = '0000';
						if ($err == "")
						{
							$error['codSis'] = mysql_errno($conex);
							$error['descSis'] = str_replace("'", "*", mysql_error($conex));
						}
						else
						{
							$error['codSis'] = $err;
							$error['descSis'] = $err;
						}
						// registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
						registrarError('NO INFO', $cco, 'NO INFO', '0', '0', $pac, $art, $error, &$color, $warning, $usuario);
						$printError = "<CENTER>EL CODIGO DE USUARIO NO EXISTE";
						$ok = false;
					}
				}
				else
				{
					// Determinar que el centro de costos haya sido leido.
					$pos = strpos(strtoupper($ccoCod), "UN.");
					if ($pos === 0) // Tiene que ser triple igual por que si no no funciona
					{
						$cco['cod'] = substr($ccoCod, 3);
						if (!getCco(&$cco, "D", $emp))
						{
							$printError = "EL CENTRO DE COSTOS NO EXISTE O NO ESTA HABILITADO PARA REALIZAR CARGOS";
							$ok = false;
						}
						else
						{
							$cco['sel'] = false;
							$ok = true;
						}
					}
					else
					{
						$printError = "EL CENTRO DE COSTOS NO FUE LEIDO POR EL CODIGO DE BARRAS ADECUADO";
						$ok = false;
					}
				}

				if ($ok)
				{
					if ($cco['sel'])
					{
						echo "<tr><td class='titulo1'>USUARIO: " . $usuario;
						echo "<tr><td class='titulo1' ><b>CC: ";

						?>	
							<input type='password' size='7' name='ccoCod' onload=''>	
							<script language="JAVASCRIPT" type="text/javascript">
							document.devApl.ccoCod.focus();
							</script>
						<?php
						echo "</td></tr>";
						hiddenCco($cco, "cco");
						echo"<tr><td  class='titulo2'><input type='submit' value='ACEPTAR'></td></tr><tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr></form>";
					}
					else
					{
						if ($cco['apl'])
						{
							echo "<tr><td class='tituloSup'>" . $cco['cod'] . "-" . $cco['nom'] . "</td></tr>";
							echo "<tr><td class='tituloSup'>USUARIO: " . $usuario . "</b>";
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
							echo "<input type='hidden' name='cco[cod]' value='" . $cco['cod'] . "'>";
							hiddenCco($cco, "cco");
							echo"<tr><td  class='titulo2'><input type='submit' value='ACEPTAR'></td></tr><tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr></form>";
						}
						else
						{
							$printError = "EL CENTRO DE COSTOS NO ES HOSPITALARIO O NO ES DE APLICACICÓN AUTOMÁTICA<br>"
							. "Este programa solo puede ser desde centros de costos hospitalarios con aplicación automática.";
							$ok = false;
						}
					}
					echo "<input type='hidden' name ='usuario' value='" . $usuario . "' >";
				}

				if (!$ok)
				{
					echo "<tr><td class='errorTitulo'>" . $printError;
					echo "</td></tr></table>";
				}
			}
			else
			{
				if (!isset($pac['act']))
				{
					/**
                * Aqui empiezan las devoluciones
                */
					if (!isset($pac['his']))
					$pac['his'] = ltrim(rtrim($historia));
					// Validación por Matrix
					if ($pac['his'] == 0)
					{
						$pac['dxv'] = true;
					}
					else
					{
						$pac['dxv'] = false;
					}
					
					if( $conex_o != 0 ){
						// Valida que esté ativo
						$conex_f = odbc_connect('facturacion', '', '');
						$pac['act'] = ValidacionHistoriaUnix(&$pac, &$warning, &$error);
						odbc_close($conex_f);
					}
					else
					{
						/************************************************************************
						 * Enero 8 de 2013
						 ************************************************************************/
						$pac['act'] = HistoriaMatrix( $cco, &$pac, &$warning, &$error );
						
						if( true || !isset($pac['nom']))
						{
							$pac['act']=infoPaciente(&$pac,$emp);
							
							$pac['dxv']=false;
							if(!isset($pac['nom']))
							{
								$pac['nom']="NO ENCONTRADO (".$pac['his'].")";
							}
						}
						/************************************************************************/
					}

					if ($pac['act'])
					{
						if (!$pac['ptr'])
						{
							if (!$pac['ald'])
							{
								if ($pac['sac'] == $cco['cod'])
								{
									// El paciente esta ubicado en el mismo centro de costos que eligio el usuario
									if (!$pac['alp'] or ($pac['alp'] and isset($alp)))
									{
										/**
                                    * Debo buscar la fecha y hora de entrada del paciente al servicio
                                    */
										$fecRecibo = "";
										$horRecibo = "";
										$pac['act'] = recuperarDatosIngresoPacCco($pac, $cco, &$fecRecibo, &$horRecibo, &$errMsg);
										if (!$pac['act'])
										{
											// No se encontro la fecha y la hora de entrada del paciente al servicio
											echo "<tr><td class='errorTitulo'>EL PACIENTE CON HISTORIA " . $pac['his'] . " E INGRESO " . $pac['ing'] . " <br> Es posible que el paciente se encuentre en proceso de traslado a otro servicio.<br>Verifique y vuelva a intentarlo</td></tr>";
										}
									}
									else
									{
										// Paciente con en proceso de Alta y la persona no selecciono el checkbox de alta en proceso
										echo "<tr><td class='errorTitulo'>EL PACIENTE CON HISTORIA " . $pac['his'] . " E INGRESO " . $pac['ing'] . " ESTA EN PROCESO DE ALTA</td></tr>";
										$pac['act'] = false;
									}
								}
								else
								{
									// El paciente no esta ubicado en el centro de costos que el usuario selecciono
									$sac['cod'] = $pac['sac'];
									getCco(&$sac, "C", $emp);
									echo "<tr><td class='errorTitulo'>EL PACIENTE NO ESTA UBICADO EN EL CENTRO DE COSTOS SELECCIONADO";
									echo "El paciente " . $pac['nom'] . " con " . $pac['his'] . " e ingreso " . $pac['ing'] . " <BR>";
									echo " no se encuentra en el centro de costos " . $cco['cod'] . " " . $cco['nom'] . ", <br> ";
									echo " si no en el " . $sac['cod'] . " " . $sac['nom'] . ". <br> ";
									echo " Por este motivo no es posible hacerle la devolución</td></tr>";
									$pac['act'] = false;
								}
							}
							else
							{
								// Paciente con Alta definitiva
								echo "<tr><td class='errorTitulo'>EL PACIENTE CON HISTORIA " . $pac['his'] . " E INGRESO " . $pac['ing'] . " FUE DADO DE ALTA</td></tr>";
								$pac['act'] = false;
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

							if($pac['sac'] == $cco['cod'])
							{
								$pac['act']=true;
								$fecRecibo = "";
								$horRecibo = "";
							}
							else
							{
								//El paciente esta en proceso de traslado
								echo "<tr><td class='errorTitulo'>EL PACIENTE CON HISTORIA " . $pac['his'] . " ESTA EN PROCESO DE TRASLADO.<BR> Mientras el paciente no sea recibido en el centro de costos de destino<br>no se pueden hacer devoluciones</td></tr>";
								$pac['act'] = false;
							}
						}
					}
					else
					{
						// El paciente no esta activo
						echo "<tr><td class='errorTitulo'>EL PACIENTE CON HISTORIA " . $pac['his'] . " NO ESTA ACTIVO EN UNIX</td></tr>";
					}
				}

				if ($pac['act'])
				{
					crearArrayJus(&$jus);
					$afin = clienteMagenta($pac['doc'], $pac['tid'], &$pac['tip'], &$pac['color']);
					echo "<td class='tituloSup' >" . $pac['nom'];
					if ($afin)
					{
						echo "<font color='#" . $pac['color'] . "' size='12' face='arial'>" . $pac['tip'] . "</font>";
					}
					echo "</td></tr>";
					echo "<td class='tituloSup' >Historia:" . $pac['his'] . "&nbsp;&nbsp;&nbsp;Ingreso:" . $pac['ing'] . "</td></tr>";

					echo "<form name='devApl' action='' method='POST'>";

					if (!isset($listaDev))
					{
						$listaDev = array();
					}

					if (!isset($ccoArr))
					{
						$ccoArr = array();
					}

					$restarleACountDev = 0;

					if (isset($articulo))
					{
						$articulo = ltrim(rtrim(strtoupper($articulo)));
						if ($articulo != "")
						{
							$art1['cod'] = BARCOD($articulo);

							ArticuloCba(&$art1);
							$art = $art1['cod'];

							$noEsUnArtRepetido = true;

							/**
                            * Es necesario buscar que el artículo no sea repetido para la devolución
                            */
							$countDev = count($listaDev);
							for($i = 0;$i < $countDev;$i++)
							{
								if ($art == $listaDev[$i]['art']['cod'])
								{
									echo "<tr><td class='errorTitulo'>Ya ingreso articulo con código " . $articulo . " y nombre " . $listaDev[$i]['art']['nom'] . ". La lista no debe tener artículos repetidos.<br>Si desea ingrese otro artículo.</td></tr>";
									$noEsUnArtRepetido = false;
								}
							}
							if ($noEsUnArtRepetido)
							{
								if (!isset($listaDev))
								{
									$k = 0;
								}
								else
								{
									$k = count($listaDev);
								}
								$listaDev[$k] = array();

								/**
                                * Es necesario preguntar al usuario por los datos asociados al artículo.
                                */
								switch (infoArticulo($art, $ccoArr, $cco, &$pac, $fecRecibo, $horRecibo, $listaDev[$k], &$errorMsg))
								{
									case 0:
									unset($listaDev[$k]);
									echo "<tr><td class='errorTitulo'>El articulos con código " . $articulo . " presento el siguiente problema: <BR>" . $errorMsg . "</td></tr>";
									break;

									case 1:
									$restarleACountDev = 1;
									break;

									case 2:
									echo "<tr><td class='errorTitulo'>El articulo con código " . $articulo . " y nombre " . $listaDev[$k]['art']['nom'] . "<br> no tiene una cantidad suceptible de ser devuelta de este paciente.</td></tr>";
									unset($listaDev[$k]);
									break;
								}
							}
							if (isset($terminar))
							{
								echo "<tr><td class='errorTitulo'>Digito un Código de Artículo<br>Primero debe llenar la información para este artículo y despues seleccionar 'Devolver los artículos Elegidos'</td></tr>";
								unset($terminar);
							}

						}
					}

					echo "<tr>";
					echo "<td class='titulo2'>Código Artículo:";

                ?>
				<input type="text" name="articulo" >
				<script language="JAVASCRIPT" type="text/javascript">
				document.devApl.articulo.focus();
				</script>
				<?php
				echo "</tr>";

				$countDev = count($listaDev);

				if (isset($listaDev) and $countDev > 0)
				{
					$procesar = true;
					$eligioAlMenosUno = false;

					/**
                    * Realizar las validaciones de los diferentres artículos a devolver
                    * Se hacen primero en este for para revisarlos todos antes de empezar a hacer las devoluciones
                    */
					for($i = 0;$i < ($countDev - $restarleACountDev);$i++)
					{
						$countCco = count($listaDev[$i]['cco']);

						for($j = 0;$j < $countCco;$j++)
						{
							$codCco = $listaDev[$i]['cco'][$j]['cid'];

							if (isset($listaDev[$i]['cco'][$j]['cit']))
							{
								// Poner los campos que muestran los warnings en el estado inicial o default
								$listaDev[$i]['cco'][$j]['class'] = "texto";
								$listaDev[$i]['cco'][$j]['msg'] = "";
								if (isset($listaDev[$i]['cco'][$j]['ckb']))
								{
									$listaDev[$i]['cco'][$j]['ckb'] = true;
									if (!validacionBasica($listaDev[$i]['cco'][$j], $listaDev[$i]['art']['jdv'], $ccoArr[$codCco]['ima'], $listaDev[$i]['art']['cod'], $ccoArr[$codCco]['cod']))
									{
										/**
                                        * Hay algun error de validación sencilla
                                        */
										$procesar = false;
										$listaDev[$i]['cco'][$j]['class'] = "alert";
										$listaDev[$i]['cco'][$j]['msg'] = $listaDev[$i]['cco'][$j]['msg'] . "<br><IMG SRC='/matrix/images/medical/root/Malo.ico'>";
									}
									$eligioAlMenosUno = true;
								}
								else
								{
									$listaDev[$i]['cco'][$j]['ckb'] = false;
								}
							}
						}
					}

					if ($eligioAlMenosUno and $procesar and isset($terminar))
					{
						// Generar el número de la devolucion
						devCons(&$devCons, $cco, $pac, $usuario);
						// Si se va ha acer la devolución ninguno de los campos debe quedar modificables, deben quedar fijos, para eso es la variable $disabled
						$disabled = "disabled";
						echo "<tr><td class='tituloSup1'><a href='/matrix/" . $bd . "/procesos/Recibo_devoluciones.php?wnde=" . $devCons . "&wbasedato=movhos&wemp_pmla=01&wcco=x&reporte=1&historia=" . $pac['his'] . "&ingreso=" . $pac['ing'] . "' target='_blank'>Se ha realizado la devolución Número " . $devCons . "</a></td><tr>";
					} elseif (!$procesar)
					{
						echo "<tr><td class='errorTitulo'>Debe resolver un problema con alguno de los artìculos a devolver</td><tr>";
					} elseif (isset($terminar) and !$eligioAlMenosUno)
					{
						echo "<tr><td class='errorTitulo'>Debe Elegir al menos un artículo para devolver</td><tr>";
						unset($aceptar);
					}

					if (!isset($disabled))
					{
						$disabled = "";
					}
					
					/**
                    * Mostrarlos en artículos seleccionados para devolver en una lista ordenada
                    * de último a primero con las posibilidades para digitar las cantidades
                    */
					echo "<tr>";
					echo "<td><br><br><td>";
					echo "<tr>" ;
					echo "<td><table border='0' width='700' >";

					$countDev = $countDev-1;
					// echo "countDev=$countDev<br>";
					/**
                    * Mostrar del último al primero
                    */
					for($i = ($countDev);$i >= 0;$i--)
					{
						if (($countDev - $i) % 5 == 0)
						{
							// Cada 5 registros poner un título
							echo "<tr><td class='titulo2'>Artículo</td>";
							echo "<td class='titulo2'></td>";
							echo "<td class='titulo2'>Cco</td>";
							echo "<td class='titulo2'></td>";
							echo "<td class='titulo2'>Cant. Máx</td>";
							echo "<td class='titulo2'>Devolución</td>";
							echo "<td class='titulo2'>Just. Devolución</td>";
							echo "<td class='titulo2'>Faltante</td>";
							echo "<td class='titulo2'>Just. Faltante</td>";
							echo "<td class='titulo2'>Descarte</td>";
							echo "<td class='titulo2'>Destino Descarte</td>";
							//echo "<td class='titulo2'>Sobrante</td>";
							echo "</tr>";
						}
						$countCco = count($listaDev[$i]['cco']);
						echo "<tr>";
						echo "<td class='" . $listaDev[$i]['art']['class'] . "' rowspan='" . $countCco . "'>" . $listaDev[$i]['art']['cod'] . "-" . $listaDev[$i]['art']['nom'] . "</td>";

						/**
                        * Para cada centro de costos en donde hay artículos suceptibles de ser devueltos hay que mostrar las opciones
                        */

						for($j = 0;$j < $countCco;$j++)
						{
							// Pongo el indice del $ccoArr donde se encuntra el centro de costos del servicio en una variable para facilidad de manejo.
							$codCco = $listaDev[$i]['cco'][$j]['cid'];

							if ($listaDev[$i]['cco'][$j]['ckb'] and $procesar and isset($terminar))
							{
								/**
                                * Si esta checkeado es por que el usuario eligio hacer una devolución sobre ese artículo.
                                * Sí $procesar=true es por que no hay problemas con ninguno de los artículos.
                                * Sí esta set $terminar es por que el usuario desea hacer la devolución ya.
                                */ 
								// se llama a la función que hace la devolución
								if ($ccoArr[$codCco]['ima'])
								{
									$k = 0;
									$ok = true;
									$listaDev[$i]['art']['can'] = 1;
									$contFaltante = 0;
									do
									{
										$wbasdat = "cenpro";
										$dronum = "";
										$drolin = "";
										$ok = devolucionCM($ccoArr[$codCco], $listaDev[$i]['art'], $pac, &$error, &$dronum, &$drolin);
										if ($ok)
										{
											if ($contFaltante >= $listaDev[$i]['cco'][$j]['cfa'])
											{
												$faltante = 0;
												$jusF = " ";
											}
											else
											{
												$faltante = 1;
												$jusF = $listaDev[$i]['cco'][$j]['jfa'];
												$contFaltante++;
											}
											// Movimiento de devoluciones
											if (!registrarDevFact($devCons, $dronum, $drolin, 1, $listaDev[$i]['cco'][$j]['jdv'], $faltante, $jusF, $usuario, &$error))
											{
												$error['ok'] = "Hubo un problema grabando<br>el Movimiento de devolucion a<br>" . $ccoArr[$codCco]['cod'] . "-" . $ccoArr[$codCco]['nom'];
												$ok = false;
											}
											else{
												if( !$solicitudCamillero ){
													if( esTraslado( $ccoArr[$codCco]['cod'] ) && true ){  //echo "paso3333....";
														peticionCamillero( 'on', '', consultarHabitacion( $pac['his'], $pac['ing'] ), $usuario, $cco['cod'], $ccoArr[$codCco]['cod'], $pac['nom'],$pac['his'], $pac['ing']  );
													}
													$solicitudCamillero = true;
												}
											}
										}
										$k++;
									}
									while ($k < $listaDev[$i]['cco'][$j]['cdv'] and $ok == true);

									if ($ok)
									{
										$listaDev[$i]['cco'][$j]['msg'] = $listaDev[$i]['cco'][$j]['msg'] . "<IMG SRC='/matrix/images/medical/root/feliz.ico'>SE REALIZO LA DEVOLUCIÓN EXITOSAMENTE";
									}
									else
									{
										// error
										$listaDev[$i]['cco'][$j]['msg'] = "<IMG SRC='/matrix/images/medical/root/Malo.ico'>Hubo un problema en la devolución " . $error['ok'] . ". Se logro devolver " . ($k-1) . $listaDev[$i]['art']['uni'] . " quedaron pendientes " . ($listaDev[$i]['cco'][$j]['cdv'] - $k + 1) . $listaDev[$i]['art']['uni'];
									}

									/**
                                    * Se realiza el descarte en caso de que haya.
                                    * el descarte siempre sera por aprovechamientos por que los cargos siempre son por aprovechamientos.
                                    */
									if ($listaDev[$i]['cco'][$j]['cds'] > 0)
									{
										$listaDev[$i]['art']['can'] = $listaDev[$i]['cco'][$j]['cds'];
										$trans['dev'] = $devCons;
										$trans['dds'] = $listaDev[$i]['cco'][$j]['dds'];

										if (registrarAplicacion($pac, $listaDev[$i]['art'], $ccoArr[$codCco], true, date('Y-m-d'), date("g:i - A"), $usuario, "D", true, $trans, &$error))
										{
											$listaDev[$i]['cco'][$j]['msg'] = $listaDev[$i]['cco'][$j]['msg'] . "<IMG SRC='/matrix/images/medical/root/feliz.ico'>SE REALIZO EL DESCARTE EXITOSAMENTE";
										}
										else
										{
											// Error al registrar el descarte.
											$listaDev[$i]['cco'][$j]['msg'] = $listaDev[$i]['cco'][$j]['msg'] . "<IMG SRC='/matrix/images/medical/root/malo.ico'>EL DESCARTE NO SE REALIZO.";
										}
									}
								}
								else
								{
									// El centro de costos no tiene inventario en MATRIX
									$datos['cdv'] = $listaDev[$i]['cco'][$j]['cdv'];
									$datos['cit'] = $listaDev[$i]['cco'][$j]['cit'];
									$datos['cia'] = $listaDev[$i]['cco'][$j]['cia'];
									$datos['caa'] = $listaDev[$i]['cco'][$j]['caa'];
									$datos['csa'] = $listaDev[$i]['cco'][$j]['csa'];
									$datos['cana'] = $listaDev[$i]['cco'][$j]['cana'];
									$datos['csna'] = $listaDev[$i]['cco'][$j]['csna'];

									if( $conex_o != 0 ){
										$ok = procesoDev($devCons, $pac, $listaDev[$i]['art'], $datos, $listaDev[$i]['cco'][$j]['jdv'], $listaDev[$i]['cco'][$j]['cfa'], $listaDev[$i]['cco'][$j]['jfa'], $listaDev[$i]['cco'][$j]['cds'], $listaDev[$i]['cco'][$j]['dds'], $ccoArr[$codCco], $usuario, &$error, &$listaDev[$i]['cco'][$j]['msg'], $cco['cod']);
									}
									else{
										/************************************************************************
										 * Enero 8 de 2013
										 ************************************************************************/
										$ok = procesoDevMatrix($devCons, $pac, $listaDev[$i]['art'], $datos, $listaDev[$i]['cco'][$j]['jdv'], $listaDev[$i]['cco'][$j]['cfa'], $listaDev[$i]['cco'][$j]['jfa'], $listaDev[$i]['cco'][$j]['cds'], $listaDev[$i]['cco'][$j]['dds'], $ccoArr[$codCco], $usuario, &$error, &$listaDev[$i]['cco'][$j]['msg'], $cco['cod']);
										/************************************************************************/
									}

									if (isset($ok) and !$ok)
									{
										$listaDev[$i]['cco'][$j]['class'] = "error";
										$listaDev[$i]['cco'][$j]['msg'] = "<IMG SRC='/matrix/images/medical/root/malo.ico'><br>" . $listaDev[$i]['cco'][$j]['msg'];
									}
								}

								/**
                                * Sobrante
                                */
								if (!registrarSobranteDevolucion($devCons, $ccoArr[$codCco], $listaDev[$i]['art'], $listaDev[$i]['cco'][$j]['sob'], $usuario, &$error))
								{
									$listaDev[$i]['cco'][$j]['msg'] = $listaDev[$i]['cco'][$j]['msg'] . "<IMG SRC='/matrix/images/medical/root/malo.ico'>EL TRASLADO DEL SOBRANTE NO SE REALIZO.";
									$OK = false;
								}
							}

							if ($listaDev[$i]['cco'][$j]['ckb']) // and $listaDev[$i]['cco'][$j]['ckb'])
							{
								$chk = "checked";
							}
							else
							{
								$chk = "";
							}

							if ($j > 0)
							{
								echo "<tr>";
							}

							/**
                            * Mostrar los registros y premitir que estos sean modificados
                            */
							echo "<td class='" . $ccoArr[$codCco]['class'] . "'><input type='checkbox' name ='listaDev[$i][cco][$j][ckb]' $chk " . $disabled . "></td>";
							echo "<td class='" . $ccoArr[$codCco]['class'] . "'>" . $ccoArr[$codCco]['cod'] . "-" . $ccoArr[$codCco]['nom'] . "</td>";
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'>" . $listaDev[$i]['cco'][$j]['msg'] . "</td>";
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'>" . $listaDev[$i]['cco'][$j]['cit'] . " " . $listaDev[$i]['art']['uni'] . "</td>";
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'><input type='text' size='3' name='listaDev[" . $i . "][cco][" . $j . "][cdv]' value='" . $listaDev[$i]['cco'][$j]['cdv'] . "'  " . $disabled . " onblur=validar('listaDev[" . $i . "][cco][" . $j . "][cdv]')> </td>";
							// Justificación de la devolución
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'>";
							if ($listaDev[$i]['art']['jdv'])
							{
								echo "<select class='texto1'  name='listaDev[" . $i . "][cco][" . $j . "][jdv]'  " . $disabled . "> ";
								$countJusD = count($jus['D']);
								echo "<option value=''>Seleccionar...</option>";
								for($k = 0;$k < $countJusD;$k++)
								{
									if ($listaDev[$i]['cco'][$j]['jdv'] == $jus['D'][$k])
									{
										echo "<option selected value='" . $jus['D'][$k] . "'>" . $jus['D'][$k] . "</option>";
									}
									else
									{
										echo "<option value='" . $jus['D'][$k] . "'>" . $jus['D'][$k] . "</option>";
									}
								}
								echo "</select>";
								echo "<input type='hidden' name='listaDev[" . $i . "][art][jdv]' value='1'>";
							}
							else
							{
								echo "<input type='hidden' name='listaDev[" . $i . "][art][jdv]' value='0'>";
								echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][jdv]' value=''>";
							}
							echo "</td>";
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'><input type='text' size='3' name='listaDev[" . $i . "][cco][" . $j . "][cfa]' value='" . $listaDev[$i]['cco'][$j]['cfa'] . "' " . $disabled . "> </td>";
							// Justificación del Faltante
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'><select class='texto1'  name='listaDev[" . $i . "][cco][" . $j . "][jfa]' " . $disabled . "> ";
							$countJusF = count($jus['F']);
							echo "<option value=''>Seleccionar...</option>";
							for($k = 0;$k < $countJusF;$k++)
							{
								if ($listaDev[$i]['cco'][$j]['jfa'] == $jus['F'][$k])
								{
									echo "<option selected>" . $jus['F'][$k] . "</option>";
								}
								else
								{
									echo "<option>" . $jus['F'][$k] . "</option>";
								}
							}
							echo "</select></td>";
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'><input type='text' size='3' name='listaDev[" . $i . "][cco][" . $j . "][cds]' value='" . $listaDev[$i]['cco'][$j]['cds'] . "' " . $disabled . "> </td>";
							// Destino del descarte
							echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'><select class='texto1'  name='listaDev[" . $i . "][cco][" . $j . "][dds]' " . $disabled . "> ";
							$countJusS = count($jus['S']);
							echo "<option value=''>Seleccionar...</option>";
							for($k = 0;$k < $countJusS;$k++)
							{
								if ($listaDev[$i]['cco'][$j]['dds'] == $jus['S'][$k])
								{
									echo "<option selected>" . $jus['S'][$k] . "</option>";
								}
								else
								{
									echo "<option>" . $jus['S'][$k] . "</option>";
								}
							}
							echo "</select></td>";

							//echo "<td class='" . $listaDev[$i]['cco'][$j]['class'] . "'><input type='text' size='3' name='listaDev[" . $i . "][cco][" . $j . "][sob]' value='" . $listaDev[$i]['cco'][$j]['sob'] . "' " . $disabled . "> </td>";
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][sob]' value='0'>";
							// Hidden que son propios de est programa y no clasicos de todos
							// cantidad total suceptible de ser devuelta
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][cit]' value='" . $listaDev[$i]['cco'][$j]['cit'] . "'>";
							// cantidad aprovechamientos total del paciente
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][cia]' value='" . $listaDev[$i]['cco'][$j]['cia'] . "'>";
							// Número del consecutivo del Array ($arrCco) en donde esta el centro de costos
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][cid]' value='" . $listaDev[$i]['cco'][$j]['cid'] . "'>";
							// id del registro correspondiente en la tabla de saldos
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][sid]' value='" . $listaDev[$i]['cco'][$j]['sid'] . "'>";
							// cantidad aprovechamiento aplicada
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][caa]' value='" . $listaDev[$i]['cco'][$j]['caa'] . "'>";
							// cantidad simple aplicada
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][csa]' value='" . $listaDev[$i]['cco'][$j]['csa'] . "'>";
							// Cantidad aprovechamiento no aplicada
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][cana]' value='" . $listaDev[$i]['cco'][$j]['cana'] . "'>";
							// Cantidad simple no aplicada
							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][csna]' value='" . $listaDev[$i]['cco'][$j]['csna'] . "'>";

							echo "<input type='hidden' name='listaDev[" . $i . "][cco][" . $j . "][class]' value='" . $listaDev[$i]['cco'][$j]['class'] . "'>";
							// echo "<input type='hidden' name='listaDev[".$i."][cco][".$j."][msg]' value='".str_replace('>','\>',str_replace('<','\<',$listaDev[$i]['cco'][$j]['msg']))."'>";
							echo "</tr>";
						}

						echo "</tr>";
						// Enviar la información del artículo
						hiddenArt($listaDev[$i]['art'], "listaDev[" . $i . "][art]");
					}
					echo "</table></td></tr>";
				}

				echo "<tr>";
				echo "<td class='tituloSup'>";
				if (!isset($devCons))
				{
					if (isset($listaDev) and count($listaDev) > 0)
					{
						echo "Devolver los artículos Elegidos <input type='checkbox' name='terminar'><br>";
					}
					echo "<input type='submit' value='ACEPTAR' name='aceptar'>";
					
					echo "</td></tr>";
					echo "<tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";

					hiddenCco($cco, "cco");
					hiddenPac($pac, "pac");
					if (isset($horRecibo))
					{
						echo "<input type='hidden' name='horRecibo' value='" . $horRecibo . "'>";
						echo "<input type='hidden' name='fecRecibo' value='" . $fecRecibo . "'>";
					}
				}
				// Información de los centros de costos
				$countCco = count($ccoArr);
				for($i = 0;$i < $countCco;$i++)
				{
					hiddenCco($ccoArr[$i], "ccoArr[" . $i . "]");
					echo "<input type='hidden' name='ccoArr[" . $i . "][class]' value='" . $ccoArr[$i]['class'] . "'> ";
				}

				echo "</form>";

				}
				
				if( $seHizoSolicitudCamillero ){
//					echo "<table align='center'>";
					echo "<tr>";
					echo "<td align='center'>";
					echo "<b>SE HA SOLICITADO CAMILLERO</b>";
					echo "</td>";
					echo "</tr>";
//					echo "</table>";
				}
				
				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr><td align='center'><A HREF='devAplAutomatica.php?usuario=".$usuario."&amp;emp=".$emp."&bd=".$bd."'>Retornar</a>&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<A HREF='devAplAutomatica.php?usuario=".$usuario."&amp;emp=".$emp."&bd=".$bd."&ccoCod=UN.".$cco['cod']."'>Retornar con CC</a></font></td></tr>";
				echo "</table></center>";
//				echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
			}
			odbc_close_all();
		}
		else
		{
			echo "<tr><td class='errorTitulo'>No hay conexión con UNIX, no puede efectuarce la transacción</td></tr>";
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