
<?php
include_once("conex.php");
  /***********************************************
   *             MONITOR DEL KARDEX              *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
  include_once("root/magenta.php");
  include_once("root/comun.php");
  include_once("movhos/kardex.inc.php");
  
  $conex = obtenerConexionBD("matrix");
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           

  $wactivolactario = consultarAliasPorAplicacion( $conex, $wemp_pmla, "ProyectoLactario" );

                                                   // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz=" Enero 20 de 2022 ";               // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                               // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Junio 30 de 2009                                                                                                            \\
//=========================================================================================================================================\\
//Este programa muestra la situación en linea de todo lo que ocurre con el Kardex en la clinica.                                           \\
//muestra que Kardex no se han generado, que kardex han cambiado luego de haberse dispensado y que Kardex estan pedientes de dispensar.    \\
//                                                                                                                                         \\
//=========================================================================================================================================\\	                                                         
	                                                           
	                                                             
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES
//=========================================================================================================================================\\
// Enero 20 del 2022 - Sebastian Alvarez Barona 
// Se realiza validacion en la consulta que nos trae los aritulos en la seccion soporte nutricional adicionandole que solo nos traigan articulos de lactario.
//=========================================================================================================================================\\
// Diciembre 19 de 2021 Marlon Osorio:
// Se reemplaza la funcion consultarCcoOrigen por consultarCcoOrigenUnificado que esta en el comun.php
//=========================================================================================================================================\\
// Diciembre 19 de 2021 Marlon Osorio:
// Se agrega el filtro por sede con la funcion consultarsedeFiltro() que está en el comun.php
//=========================================================================================================================================\\
// Octubre 27 del 2021 - Sebastian Alvarez Barona
// Se hace modificación en el caso 5 (Kardex con articulos del lactario), basicamente lo que se hizo fue lo siguiente:
// Estados: Se hicieron unos estados para validar que pacientes llegaban como nuevos, modificados o suspendidos esto 
// para llevar un mejor manejo de los pacientes.
// Pacientes con soporte nutricional (Parte operativa), esta sección en el monitor nos esta mosrando todos los pacientes que tengan algun tipo de nutricion
// por otro lado tambien una vez el paciente sea cargado, se seguira viendo en la seccion pacientes con soporte nutricional ya que se requiere dejar alli
// los datos para tener una mejor trazabulidad o manipulación de los datos.
//=========================================================================================================================================\\
// Mayo 21 de 2018	Jessica	
// En la función consultarSiDAexiste() se agrega a la consulta el filtro con cenpro_000002 para saber si la dosis adaptada esta activa
// de esta forma, si la inactivaron permite crear otra, es decir, se muestra Crear producto ya que antes de este cambio si creaban una 
// dosis adaptada y la desactivaban solo se mostraba ir al perfil. 
//=========================================================================================================================================\\
// Mayo 15 de 2018	Jessica	
// En la funcion consultarArticulosCM() se corrige la validacion de unidades ya que sin importar si las unidades eran iguales, siempre
// estaba haciendo el calculo de la dosis equivalente.
//=========================================================================================================================================\\// Abril 3 de 2018 
// Abril 3 de 2018	
// Edwin	- Si la unidad del medicamento prescrito por el medico es diferente a la unidad del insumo en central de mezclas
// 			  se modifica el calculo para que adicional al valor de conversión se tenga en cuenta la concentración del articulo.
// Jessica	- Para las dosis adaptadas se redondea la dosis prescrita por el medico a dos decimales, de esta forma si hubo 
// 			  conversiones el resultado del calculo sea más exacto y no afecte el inventario.
//=========================================================================================================================================\\
// Septiembre 20 de 2017 Jessica
// - Para la opción 11 de articulos nuevos sin aprobar de central de mezclas se agrega la opción de quitar marca de dosis adaptadas, se 
// muestra la dosis, la fecha de hora de inicio, se agrega el fondo violeta para los articulos no pos (como en el perfil), se corrige la 
// hora de dispensación en la funcion consultarHoraDispensacionPorCco() ya que si era mayor a 24 horas no se tenía en cuenta ese centro de 
// costos.
//=========================================================================================================================================\\
// Septiembre 12 de 2017 Jessica
// Para la opción 11 de articulos nuevos sin aprobar de central de mezclas se muestran cada uno de los articulos ordenados por paciente y así 
// mostrar crear producto, crear lote o ir al perfil segun el articulo. Se utiliza el query de la opción 11 en la funcion nueva 
// consultarQueryArticulosNuevosSinAprobarCM() para así mostrar la lista de articulos.
// Se agrega la descarga de la contingencia.
// Se corrige para que se muestren las notificaciones de la columna derecha (si hay más de 6 registros) ya que solo se estaban teniendo en
// cuenta las historias de la columna izquierda.
//=========================================================================================================================================\\
//Junio 22 de 2017 Jonatan
//Se agrega notificacion de mensajes generados por la enfermera desde kardex u ordenes.
//=========================================================================================================================================\\
//Mayo 20 de 2016
//Para central de mezclas, en (articulos nuevos opción 11) se cambia el query de los antibióticos, ahora se muestran los antibióticos
//ordenados para el día actual sin importar el estado
//=========================================================================================================================================\\
//Mayo 13 de 2016
//Se quita filtro Karaut != 'on' de la consulta para CM para aritculos nuevos(opción 11)
//=========================================================================================================================================\\
//Abril 21 de 2016
//Para Central de mezclas, en (articulos nuevos opción 11) también sale los antibióticos que están marcado como nuevos
//=========================================================================================================================================\\
//Noviembre 25 de 2015
//Se agrega filtro kadnes = 'on' para articulos nuevos o 1ra vez para central de mezclas 
//No se tienen en cuenta para articulos nuevos o 1ra vez para central de mezclas los articulos genericos que sean de LEV e IC 
//=========================================================================================================================================\\
//Marzo 2 de 2015 Jonatan
//Se agrega filtro kadori = 'SF' cuando se consultan los articulos con Kaddoa marcados en on. 
//=========================================================================================================================================\\
//Enero 30 de 2015 Jonatan
//Se agrega UNION con la consulta de articulos marcados como dosis adaptadas en la opcion 11 (central de mezclas).                                                                                                                     \\
//=========================================================================================================================================\\
//Abril 18 de 2013                                                                                                                        \\
//=========================================================================================================================================\\
//Se agregó la función esArticuloGenericoIncCM que permite consultar el tipo para los artículos de central de mezcla, y se adicionó el llamado
//a ésta desde la función estado_sin_dispensar, siempre y cuando el origen del artículo sea central de mezclas. Esto porque para los artículos
//de central de mezcla no se estaba trayendo el tipo correcto y tomaba el mismo horario de dispensación de servicio farmacéutico
//=========================================================================================================================================\\
//Octubre 12 de 2012                                                                                                                        \\
//=========================================================================================================================================\\
//Para los pacientes con kardex con articulos de lactario se agregó la condición Kadess != 'on' de modo que se valide que el artículo 
//no esté como no enviar
//=========================================================================================================================================\\
//Octubre 4 de 2012                                                                                                                        \\
//=========================================================================================================================================\\
//Se adicionaron las funciones consultarTipoProtocoloPorArticulo y consultarCcoOrigen para la consulta correcta a los articulos de lactario
//se muestren realmente con el horario que se debe
//=========================================================================================================================================\\
//Octubre 3 de 2012                                                                                                                        \\
//=========================================================================================================================================\\
//Se cambio el calculo de tiempo de dispensasión con base en el centro de costo si está especificado sino en el tipo de articulo, y no con base en la hora de corte como estaba.
//=========================================================================================================================================\\
//Abril 13 de 2012                                                                                                                          \\
//=========================================================================================================================================\\
//Se repara el estilo para que se muestre la alerta amarilla o azul solo para la historia que tiene proceso de traslado o alta en proceso. 
//=========================================================================================================================================\\
//Abril 4 de 2012                                                                                                                          \\
//=========================================================================================================================================\\
//Para las listas que tienen enlace a cargos de PDA, se agrega el cco de costos de dispensacion de SF									   \\
//=========================================================================================================================================\\
//Marzo 27 de 2012                                                                                                                          \\
//=========================================================================================================================================\\
//Se cren los campos $wmat_estado[$j][6] y $wmat_estado[$j][6] para determinar en el ciclo si el usuario tiene alta en proceso o   \\
//está en proceso de traslado																								                       \\
//=========================================================================================================================================\\
//Marzo 20 de 2012                                                                                                                          \\
//=========================================================================================================================================\\
//Se adiciona un monitor opcion=12, en el cual se muestran q medicamentos de la Central de Mezclas se han suspendido en la fecha (hoy),    \\
//se muestra cada uno de los pacientes y debajo de estos los medicamentos suspendidos																								                       \\
//=========================================================================================================================================\\
//Marzo 6 de 2012                                                                                                                          \\
//=========================================================================================================================================\\
//Se cambia orden en las tablas de algunos querys, tomaba inicialmente el encabezado del kardex, siendo su tabla principal el 			   \\
//detalle del kardex																								                       \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//Enero 7 de 2011                                                                                                                          \\
//=========================================================================================================================================\\
//Se crea el Monitor de CTC's, para ver que pacientes se pasaron de lo autorizado o como va el consumo de lo autorizado                    \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//Agosto 5 de 2010                                                                                                                         \\
//=========================================================================================================================================\\
//Se adicionan los monitores 6 y 7. 6: Monitor de Antibioticos sin confirmar y 7: Perfiles sin aporbar para dispensacion                   \\
//Se modifico el monitor 2, antes era kardex sin dispensar, paso a ser Perfil aprobado SIN dispensar                                       \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//Junio 17 de 2010                                                                                                                         \\
//=========================================================================================================================================\\
//Se modifica la opcion 3 para solo tenga en cuenta los Kardex no generados con articulos de dispensación.                                 \\
//=========================================================================================================================================\\
//Mayo 27 de 2010                                                                                                                          \\
//=========================================================================================================================================\\
//Se adiciona una opcion para las historias que tengan articulos del lactario en el Kardex, se define como la opcion=5                     \\
//                                                                                                                                         \\
//=========================================================================================================================================\\

	               
  
  //=====================================================================================================================================================================     
  // F U N C I O N E S
  //=====================================================================================================================================================================
  
   //funcion
	function consultarConcentracionArticuloSF( $conex, $wmovhos, $artcod )
	{

		$val = 1;

		//Consulto el codigo correspondiente en CM
		$sql = "SELECT Relcon
				FROM ".$wmovhos."_000026 a, ".$wmovhos."_000115 b, ".$wmovhos."_000059 c, ".$wmovhos."_000011 d
			   WHERE a.artcod = '".$artcod."'
				 AND a.artcod = b.relart
				 AND c.defart = a.artcod
				 AND a.artuni != c.deffru
				 AND c.defcco = d.ccocod
				 AND d.ccofac LIKE 'on' 
				 AND d.ccotra LIKE 'on' 
				 AND d.ccoima !='on' 
				 AND d.ccodom !='on'
			  ";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			$val = $rows[ 'Relcon' ];
		}
		
		return $val;
	}
	
	function quitarMarcaDA($wbasedato,$wemp_pmla,$wusuario,$historia,$ingreso,$codArticulo,$ido,$marcado)
	{
		global $conex;
		
		$mensajeMarca = "Se activo la preparacion de la DA";
		$mensajeAuditoria = "DA activada por central de mezclas";
		$prepararDA="on";
		if($marcado=="true")
		{
			$prepararDA="off";
			$mensajeMarca = "Se inactivo la preparacion de la DA";
			$mensajeAuditoria = "DA desactivada por central de mezclas";
		}
		
		// validar que ordenes este cerrado
		$queryOrdenes = "SELECT Kargra 
						   FROM ".$wbasedato."_000053 
						  WHERE Karhis='".$historia."' 
						    AND Karing='".$ingreso."' 
							AND Fecha_data='".date("Y-m-d")."' 
							AND Kargra='on';";

		$resOrdenes = mysql_query($queryOrdenes,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar ordenes abierto): ".$queryOrdenes." - ".mysql_error());
		$numOrdenes = mysql_num_rows($resOrdenes);
				
		$ordenesCerrado = false;
		if($numOrdenes > 0)
		{
			$ordenesCerrado = true;
		}
		
		
		if($ordenesCerrado)
		{
			
			$queryDA = "SELECT Kadart,Kadcfr,Kadufr,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadcon,Kaddia,Kadobs,Kadori,Kadcnd,Kaddma,Kadcan,Kaduma,Kadcma,Kaddoa,Kadnes 
						  FROM ".$wbasedato."_000054 
						 WHERE Kadhis='".$historia."' 
						   AND Kading='".$ingreso."' 
						   AND Kadart='".$codArticulo."' 
						   AND Kadido='".$ido."' 
						   AND Kadfec='".date("Y-m-d")."';";

			$resDA = mysql_query($queryDA,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar articulo marcado como DA): ".$queryDA." - ".mysql_error());
			$numDA = mysql_num_rows($resDA);
			
			if($numDA > 0)
			{
				$rowDA = mysql_fetch_array($resDA);
				
				if($rowDA['Kaddoa']==$prepararDA)
				{
					if($rowDA['Kaddoa']=="on")
					{
						$data['error'] = 1;
						$data['mensaje'] = "El articulo ya estaba marcado como DA";
			
					}
					elseif($rowDA['Kaddoa']=="off")
					{
						$data['error'] = 1;
						$data['mensaje'] = "El articulo no esta marcado como DA";
			
					}
				}
				else
				{
					// cambiar Kaddoa
					$qUpdateDA = "  UPDATE ".$wbasedato."_000054 
									   SET Kaddoa='".$prepararDA."'
									 WHERE Kadhis='".$historia."' 
									   AND Kading='".$ingreso."' 
									   AND Kadart='".$codArticulo."' 
									   AND Kadido='".$ido."' 
									   AND Kadfec='".date("Y-m-d")."';";
									   
					$resUpdateDA = mysql_query( $qUpdateDA, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $qUpdateDA . " - " . mysql_error() );		   
					
					if( mysql_affected_rows() > 0 )
					{
						$data['error'] = 0;
						$data['mensaje'] = $mensajeMarca;
						
						// crear auditoria
						$audAnterior = "A:".$rowDA['Kadart'].",".$rowDA['Kadcfr'].",".$rowDA['Kadufr'].",".$rowDA['Kadper'].",".$rowDA['Kadffa'].",".$rowDA['Kadfin'].",".$rowDA['Kadhin'].",".$rowDA['Kadvia'].",".$rowDA['Kadcon'].",".$rowDA['Kaddia'].",".$rowDA['Kadobs'].",".$rowDA['Kadori'].",".$rowDA['Kadcnd'].",".$rowDA['Kaddma'].",".$rowDA['Kadcan'].",".$rowDA['Kaduma'].",".$rowDA['Kadcma'].",".$rowDA['Kaddoa'].",".$rowDA['Kadnes'].",".$wusuario;
						$audNuevo	 = "N:".$rowDA['Kadart'].",".$rowDA['Kadcfr'].",".$rowDA['Kadufr'].",".$rowDA['Kadper'].",".$rowDA['Kadffa'].",".$rowDA['Kadfin'].",".$rowDA['Kadhin'].",".$rowDA['Kadvia'].",".$rowDA['Kadcon'].",".$rowDA['Kaddia'].",".$rowDA['Kadobs'].",".$rowDA['Kadori'].",".$rowDA['Kadcnd'].",".$rowDA['Kaddma'].",".$rowDA['Kadcan'].",".$rowDA['Kaduma'].",".$rowDA['Kadcma'].",".$prepararDA.",".$rowDA['Kadnes'].",".$wusuario;
						
						
						//Registro de auditoria
						$auditoria = new AuditoriaDTO();

						$auditoria->historia = $historia;
						$auditoria->ingreso = $ingreso;
						$auditoria->descripcion = "$audAnterior \n\n$audNuevo";
						$auditoria->fechaKardex = date("Y-m-d");
						$auditoria->mensaje = $mensajeAuditoria;
						$auditoria->seguridad = $wusuario;
						$auditoria->idOriginal = $ido;

						registrarAuditoriaKardex($conex,$wbasedato,$auditoria);

					}
		
				}
			}
			
		}
		else
		{
			$data['error'] = 1;
			$data['mensaje'] = "El programa de ordenes medicas esta siendo editado en estos momentos, espere por favor.";
		}

		return $data;
	}
	
	function ImprimirSticker($dataPac, $wemp_pmla){
		global $conex;
		
		$wcliame=consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
		$wip=consultarAliasPorAplicacion( $conex, $wemp_pmla, "IPImpStrickLactarios" );
		
		for ($i = 0; $i < count($dataPac); $i++){
			$sql = "SELECT Pacdoc 
				FROM ".$wcliame."_000100 
				WHERE Pachis = '".$dataPac[$i]["historia"]."' LIMIT 1";
    
			$res = mysql_query($sql, $conex);
			
			if($row = mysql_fetch_row($res))
			{
				$paciente = $row[0];
			}
			$impresionZPL ="^XA
							^FX Codigo de barras
							^FO12,10
							^BCN,70,N,N^FD".$dataPac[$i]["historia"]."^FS

							^FX Codigo ,lote y fecha de vencimiento
							^CFR
							^FO240,40^FD".$dataPac[$i]["historia"]."^FS
							^CFP
							^FO240,20^FDHISTORIA:^FS

							^FX Nombre del producto
							^CFR,1
							^FO10,90^A0,34,21^FD".$dataPac[$i]["nombre"]."^FS
							^CFP
							^FO10,120^FDDOC NRO: ".$paciente."^FS

							^FX Fecha de preparación
							^CFP
							^FO10,150^FDF. PREP: ".$dataPac[$i]["fecha"]."^FS

							^FX Hora de preparación
							^CFP
							^FO10,171^FDH. PREP: ".$dataPac[$i]["hora"]."^FS

							^FX Preparado por
							^CFP
							^FO10,190^FDPREPARADO POR :^FS
							^FO145,190^FDAUX CME^FS

							^FX Habitacion
							^CFP
							^FO10,209^FDHAB: ".$dataPac[$i]["habitacion"]."^FS

							^FX Hora de toma
							^CFQ
							^FO10,240^A0,22,20^FDHORA DE TOMA: ".$dataPac[$i]["frecuencia"]."s^FS
							^FO10,262^A0,22,20^FD".$dataPac[$i]["articulo"]."^FS
							^FO10,284^A0,22,20^FD".$dataPac[$i]["dosis"]." - Via: ".$dataPac[$i]["via"]."^FS
							^FO10,265^FDConservar en nevera de 2° a 6° C C^FS

							^FX Cantidad de etiquetas a imprimir
							^PQ1

							^XZ";
						
			$fp = fsockopen($wip, 9100, $errno, $errstr, 30);
			if(!$fp) 
				echo "ERROR : "."$errstr ($errno)<br>\n";
			else 
			{
				fputs($fp,$impresionZPL);
				echo "PAQUETE ENVIADO <br>\n";
				fclose($fp);
			}
			sleep(5);
		}
	}			

	function consultarPurgaDA($cco)
	{
		global $conex;
		global $wbasedato;
		
		//Consulta el valor de la purga para las dosis adaptadas por centro de costo
		$queryPurga = "SELECT Ccopda 
						 FROM ".$wbasedato."_000011 
						WHERE Ccocod='".$cco."' 
						  AND Ccoest='on';";

		$resPurga = mysql_query($queryPurga,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar purga DA): ".$queryPurga." - ".mysql_error());
		$numPurga = mysql_num_rows($resPurga);
				
		$purga = 0;
		if($numPurga > 0)
		{
			$rowPurga = mysql_fetch_array($resPurga);
			$purga = $rowPurga['Ccopda'];
		}
		
		return $purga;
	}
	
	function consultarTipoProductoCM($codProdCM)
	{
		global $conex;
		global $wbasedato;
		global $wcenmez;
		
		$qDA = 	" SELECT Arttip,Tiptpr 
					FROM ".$wcenmez."_000002,".$wcenmez."_000001
				   WHERE Artcod='".$codProdCM."' 
					 AND Artest='on'
					 AND Arttip=Tipcod
					 AND Tiptpr IN ('DA','DS','DD');";
							 
		$resDA = mysql_query($qDA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDA . " - " . mysql_error());
		$numDA = mysql_num_rows($resDA);
		
		$esDA = false;
		if($numDA > 0)
		{
			$esDA = true;
		}
		
		return $esDA;
	}
	
	function consultarDosisSiMedicamentoCompuesto($historia,$ingreso,$articulo,$ido)
	{
		global $conex;
		global $wbasedato;
		
		$queryMedCompuesto = "SELECT Defcmp,Defcpa,Defcsa 
								FROM ".$wbasedato."_000059 
							   WHERE Defart='".$articulo."' 
								 AND Defest='on';";
								 
		$resMedCompuesto = mysql_query($queryMedCompuesto, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryMedCompuesto . " - " . mysql_error());
		$numMedCompuesto = mysql_num_rows($resMedCompuesto);
		
		$dosisAntibiotico = "";
		if($numMedCompuesto > 0)
		{
			while($rowsMedCompuesto = mysql_fetch_array($resMedCompuesto))
			{
				if($rowsMedCompuesto['Defcmp']=="on" && $rowsMedCompuesto['Defcpa']!="" && $rowsMedCompuesto['Defcsa']!="")
				{
					// consultar la dosis de antibiotico
					$queryDosisAntibiotico = " SELECT Ekxin1 
												 FROM ".$wbasedato."_000208 
												WHERE Ekxhis='".$historia."' 
												  AND Ekxing='".$ingreso."' 
												  AND Ekxart='".$articulo."' 
												  AND Ekxido='".$ido."' 
												  AND Ekxfec='".date("Y-m-d")."'
												  AND Ekxest='on'

												UNION

											   SELECT Ekxin1 
												 FROM ".$wbasedato."_000209 
												WHERE Ekxhis='".$historia."' 
												  AND Ekxing='".$ingreso."' 
												  AND Ekxart='".$articulo."' 
												  AND Ekxido='".$ido."' 
												  AND Ekxfec='".date("Y-m-d")."'
												  AND Ekxest='on';";
											 
					$resDosisAntibiotico = mysql_query($queryDosisAntibiotico, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDosisAntibiotico . " - " . mysql_error());
					$numDosisAntibiotico = mysql_num_rows($resDosisAntibiotico);
					
					if($numDosisAntibiotico>0)
					{
						while($rowsDosisAntibiotico = mysql_fetch_array($resDosisAntibiotico))
						{
							$dosisAntibiotico = $rowsDosisAntibiotico['Ekxin1'];
						}
					}
				}
			}
		}

		return $dosisAntibiotico;	
	}
	
	function consultarEdad($historia,$completa)
	{
		global $conex;
		
		global $wemp_pmla;
		
		
		$q = "SELECT Pacnac 
				FROM root_000037,root_000036 
			   WHERE Orihis='".$historia."' 
				 AND Oriori='".$wemp_pmla."' 
				 AND Oriced=Pacced;";

		$res=mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		
		$edad = "";
		$anos = "";
		if($num>0)
		{
			$row=mysql_fetch_array($res);

			$fechaNacimiento = $row['Pacnac'];
			
			//Edad
			$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$ann1=($aa - $ann)/360;
			$meses=(($aa - $ann) % 360)/30;
			if ($ann1<1){
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
				$anos = 0;
			} else {
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
				$anos = (integer)$ann1;
			}
			
			$edad = $wedad; 
		
		}
		
		if($completa=="on")
		{
			return $edad;
		}
		else
		{
			return $anos;
		}
		
	}
	
	function consultarConcentracionParaInfusion($articuloCM,$historia)
	{
		global $conex;
		global $wbasedato;
		global $wcenmez;
		
		$qDatos = 	" SELECT Edainf,Edadex,Edaemi,Edaema 
						FROM ".$wcenmez."_000021 
					   WHERE Edains='".$articuloCM."' 
						 AND Edaest='on';";
							 
		$resDatos = mysql_query($qDatos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDatos . " - " . mysql_error());
		$numDatos = mysql_num_rows($resDatos);
		
		$wedad = consultarEdad($historia,"off");
		
		$arrayDatos = array();
		if($numDatos > 0)
		{
			while($rowsDatos = mysql_fetch_array($resDatos))
			{
				if($wedad>=$rowsDatos['Edaemi'] && $wedad<=$rowsDatos['Edaema'])
				{
					$arrayDatos['concInfusion'] = $rowsDatos['Edainf'];
					
					break;
				}
			}
		}
		
		return $arrayDatos;
	}
	
	function consultarProtocoloNPT()
	{
		global $wbasedato;
		global $conex;
		global $wcenmez;
		
		$qNPT = "SELECT Tiptpr 
				  FROM ".$wcenmez."_000001,".$wcenmez."_000002,".$wbasedato."_000068
				 WHERE Tipnco = 'off' 
				   AND Tipcdo != 'on'
				   AND Tipest = 'on'
				   AND Arttip = Tipcod
				   AND Artcod = Arkcod
				   AND Tiptpr = Arktip;";
				   
		$resNPT = mysql_query($qNPT,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qNPT." - ".mysql_error());
		$numNPT = mysql_num_rows($resNPT);
		
		$tipoProtocoloNPT = "";
		if($numNPT > 0)
		{
			$rowsNPT = mysql_fetch_array($resNPT); 
			$tipoProtocoloNPT = $rowsNPT['Tiptpr'];
		}

		return $tipoProtocoloNPT;		
	}
	
	function consultarProtocoloDA()
	{
		global $wbasedato;
		global $conex;
		global $wcenmez;
		
		$qDA = "SELECT Tiptpr 
				  FROM ".$wcenmez."_000001,".$wcenmez."_000002,".$wbasedato."_000068
				 WHERE tippro = 'on' 
				   AND Tipnco = 'on' 
				   AND Tipcdo != 'on'
				   AND Tipest = 'on'
				   AND Arttip = Tipcod
				   AND Artcod = Arkcod
				   AND Artest = 'on'
				   AND Tiptpr = Arktip
				   AND Tiptpr IN ('DA','DD','DS');";
				   
		$resDA = mysql_query($qDA,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qDA." - ".mysql_error());
		$numDA = mysql_num_rows($resDA);
						
		$arrayTipoProtocoloDA = array();
		if($numDA > 0)
		{
			while($rowsDA = mysql_fetch_array($resDA))
			{
				$arrayTipoProtocoloDA[] = $rowsDA['Tiptpr'];
			}
		}
		
		return $arrayTipoProtocoloDA;
	}
	
	function consultarCodigoNPT($historia,$ingreso,$articulo,$ido)
	{
		global $wbasedato;
		global $conex;
		
		$qExisteNPT = " SELECT Enucnu 
						 FROM ".$wbasedato."_000214
						WHERE Enuhis='".$historia."'
						  AND Enuing='".$ingreso."'
						  AND Enuart='".$articulo."'
						  AND Enuido='".$ido."'
						  AND Enuest='on'
						  AND Enurea='on';";
		
		$resExisteNPT = mysql_query($qExisteNPT, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qExisteNPT . " - " . mysql_error());
		$numExisteNPT = mysql_num_rows($resExisteNPT);	
		
		$codNPT = "";
		if($numExisteNPT>0)
		{
			$rowsExisteNPT = mysql_fetch_array($resExisteNPT);
			
			$codNPT = $rowsExisteNPT['Enucnu'];
		}
		
		return $codNPT;
	}
	
	function consultarSiDAexiste($historia,$ingreso,$articulo,$ido)
	{
		global $wbasedato;
		global $wcenmez;
		global $conex;
		
		$qExisteDA = " SELECT Rdacda 
						 FROM ".$wbasedato."_000224,".$wcenmez."_000002
						WHERE Rdahis='".$historia."'
						  AND Rdaing='".$ingreso."'
						  AND Rdaart='".$articulo."'
						  AND Rdaido='".$ido."'
						  AND Rdaest='on'
						  AND Artcod=Rdacda
						  AND Artest='on';";
		
		$resExisteDA = mysql_query($qExisteDA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qExisteDA . " - " . mysql_error());
		$numExisteDA = mysql_num_rows($resExisteDA);	
		
		$codDA = "";
		if($numExisteDA>0)
		{
			$rowsExisteDA = mysql_fetch_array($resExisteDA);
			
			$codDA = $rowsExisteDA['Rdacda'];
			
		}
		
		return $codDA;
	}
	
	function consultarEquivalenteCM($articulo)
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		
		$arrayCM = array();
		
		$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		
		$qArticuloEquivalenteCM = "SELECT Appcod,Appcnv 
									 FROM ".$wcenpro."_000009 
									 WHERE apppre='".$articulo."' 
									   AND Appest='on';";
		
		$resArticuloEquivalenteCM = mysql_query($qArticuloEquivalenteCM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qArticuloEquivalenteCM . " - " . mysql_error());
		$numArticuloEquivalenteCM = mysql_num_rows($resArticuloEquivalenteCM);	
		
		$codArtCM = "";
		if($numArticuloEquivalenteCM>0)
		{
			$rowsArticuloEquivalenteCM = mysql_fetch_array($resArticuloEquivalenteCM);
			
			if($rowsArticuloEquivalenteCM['Appcod']!="")
			{
				$codArtCM = $rowsArticuloEquivalenteCM['Appcod'];
				$cantidadEquivalenteCM = $rowsArticuloEquivalenteCM['Appcnv'];
			}
		}
		
		$unidadArtCM = "";
		if($codArtCM!="")
		{
			$qUnidadArticuloEquivalenteCM = "SELECT Artuni 
											   FROM ".$wcenpro."_000002 
											  WHERE Artcod='".$codArtCM."' 
												AND Artest='on';";
			
			$resUnidadArticuloEquivalenteCM = mysql_query($qUnidadArticuloEquivalenteCM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qUnidadArticuloEquivalenteCM . " - " . mysql_error());
			$numUnidadArticuloEquivalenteCM = mysql_num_rows($resUnidadArticuloEquivalenteCM);	
			
			if($numUnidadArticuloEquivalenteCM>0)
			{
				$rowsUnidadArticuloEquivalenteCM = mysql_fetch_array($resUnidadArticuloEquivalenteCM);
				
				if($rowsUnidadArticuloEquivalenteCM['Artuni']!="")
				{
					$unidadArtCM = $rowsUnidadArticuloEquivalenteCM['Artuni'];
				}
			}
		}
		
		$arrayCM['codigo'] = $codArtCM;
		$arrayCM['unidad'] = $unidadArtCM;
		$arrayCM['conversion'] = $cantidadEquivalenteCM;
		
		return $arrayCM;
	}
	
	function consultarArticulosCM($whis,$wing)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wfecha;
		global $wcenmez;
		
		
		
		$queryArtNuevosSinAprobarCM = consultarQueryArticulosNuevosSinAprobarCM($whis,$wing); 
		
		$gruposAntibioticos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'gruposMedicamentosAntibioticos');
		$gpAntibioticos = "'".str_replace( ",", "','", $gruposAntibioticos )."'";
		 
	  //========================================================================================================================================================================
	  //Kardex con Articulo(s) de CENTRAL DE MEZCLAS ** Nuevos ** o de 1ra Vez Sin Aprobar
	  //========================================================================================================================================================================
	  // $q= " SELECT kadcpx, kadpro, kadart, kadori, 'Central de mezclas' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin "
		  // ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, cenpro_000002 C "
		  // ." WHERE kadhis        = '".$whis."'"
		  // ."   AND kading        = '".$wing."'"
		  // ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  // ."   AND kadfec        = A.fecha_data "
		  // ."   AND kadfec        = B.fecha_data "
		  // ."   AND kadsus       != 'on' "
// //		          ."   AND kadcdi-kaddis > 0 "		// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		  // ."   AND kadare       != 'on' "
		  // ."   AND kadori        = 'CM' "
		  // ."   AND kadhis        = karhis "
		  // ."   AND kading        = karing "
		  // ."   AND karcco        = '*' "                //Solo hospitalizacion
		  // ."   AND Artcod        = Kadart "
		  // // ."   AND karaut       != 'on' "			//Mayo 13 de 2016. Se quita este filtro. Este filtro indica que el kardex fue generado automaticamente desde la PDA
		  // //2015-11-25
		  // //no se tiene en cuenta los de CM que sean LEV
		  // //Esto para que no salga los pacientes que tienen articulos genericos LQ0000 e IC0000
		  // ."   AND A.kadlev		!= 'on' "
		  // ." UNION "
		  // //Articulos marcados como Dosis Adaptada
		  // ." SELECT kadcpx, kadpro, kadart, kadori, 'Dosis Adaptada' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin "
		  // ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C "
		  // ." WHERE kadhis        = '".$whis."'"
		  // ."   AND kading        = '".$wing."'"
		  // ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  // ."   AND kadfec        = B.fecha_data "
		  // ."   AND kadori        = 'SF' "
		  // ."   AND kadsus       != 'on' "	          
		  // ."   AND kaddoa        = 'on' "  //Campo que marca el articulo como dosis adaptada.
		  // ."   AND kadhis        = karhis "
		  // ."   AND kading        = karing "
		  // ."   AND karcco        = '*' "
		  // ."   AND Artcod        = Kadart "
		  // ." UNION "
		  // //Articulos marcados como NE (No enteral)
		  // ." SELECT kadcpx, kadpro, kadart, kadori, 'No esteril' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin "
		  // ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C "
		  // ." WHERE kadhis        = '".$whis."'"
		  // ."   AND kading        = '".$wing."'"
		  // ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  // ."   AND kadfec        = B.fecha_data "
		  // ."   AND kadori        = 'SF' "
		  // ."   AND kadsus       != 'on' "	          
		  // ."   AND kadnes        = 'on' "  //Campo que marca el articulo como dosis adaptada.
		  // ."   AND kadhis        = karhis "
		  // ."   AND kading        = karing "
		  // ."   AND karcco        = '*' "
		  // ."   AND Artcod        = Kadart "
		  // ." UNION "
		  // //Articulos que son antibióticos nuevos
		  // ." SELECT kadcpx, kadpro, kadart, kadori, 'Antibiotico' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin "
		  // ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C "
		  // ." WHERE kadhis        = '".$whis."'"
		  // ."   AND kading        = '".$wing."'"
		  // ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  // ."   AND kadfec        = B.fecha_data "
		  // ."   AND kadori        = 'SF' "
		  // ."   AND kadsus       != 'on' "	          
		  // ."   AND kadhis        = karhis "
		  // ."   AND kading        = karing "
		  // ."   AND karcco        = '*' "
		  // ."   AND Artcod        = Kadart "
		  // // ."   AND Kadlog        = Logcod "
		  // // ."   AND Logsus        != 'on' "
		  // ."   AND kadfec        = A.fecha_data "
		  // ."   AND SUBSTRING( Artgru, 1, LOCATE( '-', Artgru )-1 ) IN ( $gpAntibioticos ) "
		  
		  // ."ORDER BY tipo;";
				  			 
		$q= " SELECT kadcpx, kadpro, kadart, kadori, 'Central de mezclas' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin,Habcco,'' AS Artpos  "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wcenmez."_000002 C, ".$wbasedato."_000020 D "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = A.fecha_data "
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadsus       != 'on' "
//		          ."   AND kadcdi-kaddis > 0 "		// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		  ."   AND kadare       != 'on' "
		  ."   AND kadori        = 'CM' "
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "                //Solo hospitalizacion
		  ."   AND Artcod        = Kadart "
		  // ."   AND karaut       != 'on' "			//Mayo 13 de 2016. Se quita este filtro. Este filtro indica que el kardex fue generado automaticamente desde la PDA
		  //2015-11-25
		  //no se tiene en cuenta los de CM que sean LEV
		  //Esto para que no salga los pacientes que tienen articulos genericos LQ0000 e IC0000
		  ."   AND A.kadlev		!= 'on' "
		  ."   AND Habhis=kadhis
			   AND Habing=kading"
		  ." UNION "
		  //Articulos marcados como Dosis Adaptada
		  ." SELECT kadcpx, kadpro, kadart, kadori, 'Dosis Adaptada' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin,Habcco,Artpos  "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C, ".$wbasedato."_000020 D "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadori        = 'SF' "
		  ."   AND kadsus       != 'on' "	          
		  ."   AND kaddoa        = 'on' "  //Campo que marca el articulo como dosis adaptada.
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "
		  ."   AND Artcod        = Kadart "
		  ."   AND Habhis=kadhis
			   AND Habing=kading"
		  ." UNION "
		  //Articulos marcados como NE (No enteral)
		  ." SELECT kadcpx, kadpro, kadart, kadori, 'No esteril' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin,Habcco,Artpos  "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C, ".$wbasedato."_000020 D "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadori        = 'SF' "
		  ."   AND kadsus       != 'on' "	          
		  ."   AND kadnes        = 'on' "  //Campo que marca el articulo como dosis adaptada.
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "
		  ."   AND Artcod        = Kadart "
		  ."   AND Habhis=kadhis
			   AND Habing=kading"
		  ." UNION "
		  //Articulos que son antibióticos nuevos
		  ." SELECT kadcpx, kadpro, kadart, kadori, 'Antibiotico' AS Tipo,Kadido,Artgen,Kadcfr,Kadufr,Kadobs,Kadfin,Kadhin,Habcco,Artpos  "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C, ".$wbasedato."_000020 D "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadori        = 'SF' "
		  ."   AND kadsus       != 'on' "	          
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "
		  ."   AND Artcod        = Kadart "
		  // ."   AND Kadlog        = Logcod "
		  // ."   AND Logsus        != 'on' "
		  ."   AND kadfec        = A.fecha_data "
		  ."   AND SUBSTRING( Artgru, 1, LOCATE( '-', Artgru )-1 ) IN ( $gpAntibioticos ) "
		  ."   AND Habhis=kadhis
			   AND Habing=kading "
		  
		  ."ORDER BY tipo;";
				  					

			// echo "<pre>".print_r($q,true)."</pre>";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $num = mysql_num_rows($res);
			
			$arrayArticulosCM = array();
			if($num > 0)
			{
				while ($rows = mysql_fetch_array($res)) 
				{
					$info = "";
					$info = $rows['Tipo'];
					$horaInicio = explode(":",$rows['Kadhin']);
					$ronda = $horaInicio[0];
					$fecharonda = $rows['Kadfin'];
					
					
					if($rows['Tipo']=="Dosis Adaptada" || $rows['Tipo']=="Antibiotico")
					{
						$existeDA = consultarSiDAexiste($whis,$wing,$rows['kadart'],$rows['Kadido']);
					
						$arrayEquivalenteCM = consultarEquivalenteCM($rows['kadart']);
						
						$equivalenteCM = $arrayEquivalenteCM['codigo'];
						$unidadArtCM = $arrayEquivalenteCM['unidad'];
						$conversionEquivalenteCM = $arrayEquivalenteCM['conversion'];
						
						$dosis = $rows['Kadcfr'];
						
						// // Valida si el medicamento esta marcado como compuesto en movhos_000059, si es así consultar en movhos_000208 o movhos_000209 la dosis del antibiotico
						// $dosisMedicamentoCompuesto = consultarDosisSiMedicamentoCompuesto($whis,$wing,$rows['kadart'],$rows['Kadido']);
						
						// if($dosisMedicamentoCompuesto!="")
						// {
							// $dosis = $dosisMedicamentoCompuesto;
						// }
						

						// if($rows['Kadufr']!=$unidadArtCM)
						// {
							// $dosis = $dosis*$conversionEquivalenteCM;
						// }
						
						//calculo
						if( $rows['Kadufr'] != $unidadArtCM )
						{
							//Se busca la concentración del articulo, para dejar la dosis del médico en las unidades en que se factura
							$concentracionArticuloSF = consultarConcentracionArticuloSF( $conex, $wbasedato, $rows['kadart'] );
							$dosis 					 = $dosis/$concentracionArticuloSF*$conversionEquivalenteCM;
						}
						
						$dosisConPurga=$dosis;
						// tener en cuenta la purga
						$purgaDA = consultarPurgaDA($rows['Habcco']);
						
						if($purgaDA!="0" && $equivalenteCM!="")
						{
							$concentracionParaInfusion = consultarConcentracionParaInfusion($equivalenteCM,$whis);
							$dosisConPurga = ($purgaDA*$concentracionParaInfusion['concInfusion'])+ $dosis;
						}
						
						$dosis = round($dosis*1,2);
						$dosisConPurga = round($dosisConPurga,2);
						
						if($existeDA=="")
						{
							// crear producto DA
							if($equivalenteCM=="")
							{
								$urlCM = "Sin equivalente en central de mezclas (cenpro_000009)"; 
								$urlCM = "El insumo debe estar activo en el maestro de relacion de insumos con presentaciones"; 
							}
							else
							{
								$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&DA_historia=".$whis."&DA_ingreso=".$wing."&DA_articulo=".$rows['kadart']."&DA_ido=".$rows['Kadido']."&DA_articuloCM=".$equivalenteCM ."&DA_cantidad=".$dosisConPurga."&DA_cantidadSinPurga=".$dosis."&DA_tipo=".$rows['Tipo']."&tippro=03-Dosis adaptada-NO CODIFICADO&pintarListaDAPendientes=true&wronda=".$ronda."&wfecharonda=".$fecharonda."&DA_cco=".$rows['Habcco']."' target=_blank> Crear producto </A>"; 
							}
						}
						else
						{
							// validar si el producto es dosis adaptada
							$productoEsDA = consultarTipoProductoCM($existeDA);
							
							if($productoEsDA)
							{
								$info = "Dosis adaptada creada: ".$existeDA;
								$codigoDA = $existeDA;
								// crear lote
								$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoDA."&forcon=Codigo del Producto&pintar=1&whistoria=".$whis."&wingreso=".$wing."&warticuloda=".$rows['kadart']."&idoda=".$rows['Kadido']."&wronda=".$ronda."&wfecharonda=".$fecharonda."' target=_blank> Crear lote </A>"; 
							}
							else
							{
								// Ir al perfil
								$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$whis."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A>"; 
							}
							
						}
					}
					elseif($rows['Tipo']=="Central de mezclas")
					{
						$rows['kadart'] = strtoupper($rows['kadart']);
						$codProdCM = substr($rows['kadart'],0,2);
						
						// validar segun el codigo
						$tipoProtocoloDA = consultarProtocoloDA();
						$tipoProtocoloNPT = consultarProtocoloNPT();
						
						if(in_array(strtoupper($codProdCM),$tipoProtocoloDA))
						{
							$existeDA = consultarSiDAexiste($whis,$wing,$rows['kadart'],$rows['Kadido']);
					
							
							
							
							
							if($existeDA=="")
							{
								$equivalenteCM = "";
								$dosis = $rows['Kadcfr'];
								$dosisConPurga=$dosis;
								
								
								// $arrayEquivalenteCM = consultarEquivalenteCM($rows['kadart']);
								
								// $equivalenteCM = $arrayEquivalenteCM['codigo'];
								// $unidadArtCM = $arrayEquivalenteCM['unidad'];
								// $conversionEquivalenteCM = $arrayEquivalenteCM['conversion'];
								
								// $dosis = $rows['Kadcfr'];
								
								// // Valida si el medicamento esta marcado como compuesto en revisa_000059, si es así consultar en movhos_000208 o movhos_000209 la dosis del antibiotico
								// $dosisMedicamentoCompuesto = consultarDosisSiMedicamentoCompuesto($whis,$wing,$rows['kadart'],$rows['Kadido']);
								
								// if($dosisMedicamentoCompuesto!="")
								// {
									// $dosis = $dosisMedicamentoCompuesto;
								// }
								
								
								// //calculo
								// if( $rows['Kadufr'] != $unidadArtCM )
								// {
									// //Se busca la concentración del articulo, para dejar la dosis del médico en las unidades en que se factura
									// $concentracionArticuloSF = consultarConcentracionArticuloSF( $conex, $wbasedato, $rows['kadart'] );
									// $dosis 					 = $dosis/$concentracionArticuloSF*$conversionEquivalenteCM;
								// }
								
								// $dosisConPurga=$dosis;
								// // tener en cuenta la purga
								// $purgaDA = consultarPurgaDA($rows['Habcco']);
								
								// if($purgaDA!="0" && $equivalenteCM!="")
								// {
									// $concentracionParaInfusion = consultarConcentracionParaInfusion($equivalenteCM,$whis);
									// $dosisConPurga = ($purgaDA*$concentracionParaInfusion['concInfusion'])+ $dosis;
								// }
								
								// $dosis = round($dosis*1,2);
								// $dosisConPurga = round($dosisConPurga,2);
							
								$info = "Dosis adaptada genérica";
								// crear producto DA
								$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&DA_historia=".$whis."&DA_ingreso=".$wing."&DA_articulo=".$rows['kadart']."&DA_ido=".$rows['Kadido']."&DA_articuloCM=".$equivalenteCM ."&DA_cantidad=".$dosisConPurga."&DA_cantidadSinPurga=".$dosis."&DA_tipo=Generica&tippro=03-Dosis adaptada-NO CODIFICADO&pintarListaDAPendientes=true&wronda=".$ronda."&wfecharonda=".$fecharonda."&DA_cco=".$rows['Habcco']."' target=_blank> Crear producto </A>"; 
							}
							else
							{
								
								$info = "Dosis adaptada genérica creada: ".$existeDA;
								// crear lote
								$codigoDA = $existeDA;
								$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoDA."&forcon=Codigo del Producto&pintar=1&whistoria=".$whis."&wingreso=".$wing."&warticuloda=".$rows['kadart']."&idoda=".$rows['Kadido']."&wronda=".$ronda."&wfecharonda=".$fecharonda."' target=_blank> Crear lote </A>"; 
							}
						}
						elseif(strtoupper($codProdCM)==$tipoProtocoloNPT)
						{
							$codigoNPT = consultarCodigoNPT($whis,$wing,$rows['kadart'],$rows['Kadido']);
							
							if($codigoNPT=="")
							{
								// consultar datos npt
								$qDatosNPT = "SELECT Enupes,Enutin,Enupur,Enuvol 
												FROM ".$wbasedato."_000214
											   WHERE Enuhis='".$whis."'
												 AND Enuing='".$wing."'
												 AND Enuart='".$rows['kadart']."'
												 AND Enuido='".$rows['Kadido']."'
												 AND Enuest='on'
												 AND Enurea='off';";
										   
								$resDatosNPT = mysql_query($qDatosNPT,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qDatosNPT." - ".mysql_error());
								$numDatosNPT = mysql_num_rows($resDatosNPT);
								
								$tipoProtocoloNPT="";
								if($numDatosNPT > 0)
								{
									$info = "Nutrición parenteral";
									$rowsDatosNPT = mysql_fetch_array($resDatosNPT); 
									$tipoProtocoloNPT=$rowsDatosNPT['Tiptpr'];
									
									// crear NPT
									$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&historia=".$whis."&NPT_historia=".$whis."&NPT_ingreso=".$wing."&NPT_articulo=".$rows['kadart']."&NPT_ido=".$rows['Kadido']."&peso=".$rowsDatosNPT['Enupes']."&purga=".$rowsDatosNPT['Enupur']."&volumen=".$rowsDatosNPT['Enuvol']."&NPT_tiempoInfusion=".$rowsDatosNPT['Enutin']."&NPT_origen=ordenes&tippro=02-Nutricion Parenteral-NO CODIFICADO&pintarListaNPTPendientes=true' target=_blank> Crear producto </A>"; 
									
								}
								else
								{
									$info = "Nutrición parenteral genérica";
									// crear NPT generica
									$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&historia=".$whis."&NPT_historia=".$whis."&NPT_ingreso=".$wing."&NPT_articulo=".$rows['kadart']."&NPT_ido=".$rows['Kadido']."&&NPT_origen=kardex&tippro=02-Nutricion Parenteral-NO CODIFICADO&pintarListaNPTPendientes=true' target=_blank> Crear producto </A>"; 
									
								}
							}
							else
							{
								$info = "Nutrición parenteral creada: ".$codigoNPT;
								$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoNPT."&forcon=Codigo del Producto&pintar=1' target=_blank> Crear lote </A>"; 
							}
						}
						else
						{
							// Ir al perfil
							$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$whis."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A>"; 
						}
					}
					else
					{
						// Ir al perfil
						$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$whis."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A>"; 
					}
					
					$kadobs1="";
					if(trim($rows['Kadobs'])!="")
					{
						$observaciones=explode("<div",$rows['Kadobs']);
					
						for($s=1;$s<count($observaciones);$s++)
						{
							$observacion="<div".$observaciones[$s];
							
							$obs = nl2br(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ));
							
							if(trim($obs)!="")
							{
								$kadobs1 .= "- ".$obs."<br>";
							}
							
						}
					}
					
					$esNoPos = false;
					// validar si es no pos
					if($rows['Tipo']=="Central de mezclas")
					{
						$esNoPos = esProductoNoPOSCM( $conex, $wbasedato, "cenpro", $rows['kadart']);
					}
					else
					{
						if($rows['Artpos']=="N")
						{
							$esNoPos = true;
						}
					}
					// var_dump($esNoPos);
					$ido = $rows['Kadido'];
					$arrayArticulosCM[$ido]['articulo'] = $rows['kadart'];
					$arrayArticulosCM[$ido]['ido'] = $rows['Kadido'];
					$arrayArticulosCM[$ido]['tipo'] = $rows['Tipo'];
					$arrayArticulosCM[$ido]['generico'] = $rows['Artgen'];
					$arrayArticulosCM[$ido]['dosis'] = $rows['Kadcfr'];
					$arrayArticulosCM[$ido]['unidad'] = $rows['Kadufr'];
					$arrayArticulosCM[$ido]['equivalente'] = $equivalenteCM ;
					$arrayArticulosCM[$ido]['existe'] = $existeDA;
					$arrayArticulosCM[$ido]['url'] = $urlCM;
					$arrayArticulosCM[$ido]['observaciones'] = $kadobs1;
					$arrayArticulosCM[$ido]['info'] = $info;
					$arrayArticulosCM[$ido]['fechaHorainicio'] = $rows['Kadfin']." ".$rows['Kadhin'];
					$arrayArticulosCM[$ido]['nopos'] = $esNoPos;
					
				} 
			}
		    
		return $arrayArticulosCM;

	}
	
	function consultarQueryArticulosNuevosSinAprobarCM($whis,$wing)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wfecha;
		
		$gruposAntibioticos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'gruposMedicamentosAntibioticos');
		$gpAntibioticos = "'".str_replace( ",", "','", $gruposAntibioticos )."'";
		 
	  //========================================================================================================================================================================
	  //Kardex con Articulo(s) de CENTRAL DE MEZCLAS ** Nuevos ** o de 1ra Vez Sin Aprobar
	  //========================================================================================================================================================================
	  $q= " SELECT kadcpx, kadpro, kadart, kadori "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = A.fecha_data "
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadsus       != 'on' "
//		          ."   AND kadcdi-kaddis > 0 "		// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		  ."   AND kadare       != 'on' "
		  ."   AND kadori        = 'CM' "
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "                //Solo hospitalizacion
		  // ."   AND karaut       != 'on' "			//Mayo 13 de 2016. Se quita este filtro. Este filtro indica que el kardex fue generado automaticamente desde la PDA
		  //2015-11-25
		  //no se tiene en cuenta los de CM que sean LEV
		  //Esto para que no salga los pacientes que tienen articulos genericos LQ0000 e IC0000
		  ."   AND A.kadlev		!= 'on' "
		  ." UNION "
		  //Articulos marcados como Dosis Adaptada
		  ." SELECT kadcpx, kadpro, kadart, kadori "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadori        = 'SF' "
		  ."   AND kadsus       != 'on' "	          
		  ."   AND kaddoa        = 'on' "  //Campo que marca el articulo como dosis adaptada.
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "
		  ." UNION "
		  //Articulos marcados como NE (No enteral)
		  ." SELECT kadcpx, kadpro, kadart, kadori "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadori        = 'SF' "
		  ."   AND kadsus       != 'on' "	          
		  ."   AND kadnes        = 'on' "  //Campo que marca el articulo como dosis adaptada.
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "
		  ." UNION "
		  //Articulos que son antibióticos nuevos
		  ." SELECT kadcpx, kadpro, kadart, kadori "
		  ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C "
		  ." WHERE kadhis        = '".$whis."'"
		  ."   AND kading        = '".$wing."'"
		  ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		  ."   AND kadfec        = B.fecha_data "
		  ."   AND kadori        = 'SF' "
		  ."   AND kadsus       != 'on' "	          
		  ."   AND kadhis        = karhis "
		  ."   AND kading        = karing "
		  ."   AND karcco        = '*' "
		  ."   AND Artcod        = Kadart "
		  // ."   AND Kadlog        = Logcod "
		  // ."   AND Logsus        != 'on' "
		  ."   AND kadfec        = A.fecha_data "
		  ."   AND SUBSTRING( Artgru, 1, LOCATE( '-', Artgru )-1 ) IN ( $gpAntibioticos ) ";
			
		// echo "<pre>".print_r($q,true)."</pre>" ;
		
		return $q;
		
	}

	  /********************************************************************************
	 * Consulta el total de gestion de procedimientos sin leer para un paciente
	 ********************************************************************************/
	// function consultarMensajesSinLeer( $conex, $wbasedato, $historia, $ingreso ){

		// $val = false;

		// echo$sql = "SELECT count(*)
				  // FROM {$wbasedato}_000117
				  // WHERE Menhis = '$historia'
					// AND Mening = '$ingreso'
					// AND Menprg != '$programa'
					// AND Menlei != 'on'";					 
		// $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		// if( $rows = mysql_fetch_array($res) ){
			// $val = $rows[ 0 ];
		// }
		
		// return $val;
	// } 
  
  //Se consigue la hora PAR anterior a la hora actual(si es hora impar) si no se deja la hora PAR actual
  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;
	 global $wfecha_actual;
	    
	 
	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);
	 
	 $wfecha_actual=date("Y-m-d");
	 
	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
	    } 
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
		    else
		       $whora_par_actual=$whora_Actual; 
	     }
	     
	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	      {
		   if (($whora_par_actual-2) == "00")               //Abril 12 de 2011
		      $whora_par_anterior="24";
		     else
	            $whora_par_anterior = $whora_par_actual-2;
		  }
	      
	  if (strlen($whora_par_anterior) == 1)
	     $whora_par_anterior="0".$whora_par_anterior;
	     
	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual;  
    }
  
  
  function ctc($res)
    {
	 global $wbasedato;
	 global $conex;
	 global $wemp_pmla;
	 global $wfecha;
	 
	 $wnum = mysql_num_rows($res);
	 
	 $wfila = mysql_fetch_array($res);
	 
	 echo "<table>";
	 
	 $i=1;
	 while ($i<=$wnum)
	    {
		 $wauxcco = $wfila[0];
		 
		 echo "<tr class='tituloPagina'>";
		 echo "<td colspan=8>".$wfila[0]." : ".$wfila[1]."</td>";                  //Centro de Costo
		 echo "</tr>";
		 
		 while ($i <= $wnum and $wauxcco == $wfila[0])
		   {
			$wauxhis = $wfila[3]."-".$wfila[4];
			
			//==========================================================================================================
			//Traigo los datos demograficos del paciente
			//==========================================================================================================
            $q = " SELECT pacno1, pacno2, pacap1, pacap2, pacced, pactid "
                ."   FROM root_000036, root_000037 "
                ."  WHERE orihis = '".$wfila[3]."'"
		        ."    AND oriing = '".$wfila[4]."'"
		        ."    AND oriori = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
		        ."    AND oriced = pacced "; 
            $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowpac = mysql_fetch_array($respac); 
               
		    $wpac = $rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3];    //Nombre
		    $wdpa = $rowpac[4];                                                 //Documento del Paciente
	        $wtid = $rowpac[5]; 
			//==========================================================================================================
			
			
	        //==========================================================================================================
	        //Busco si tiene el KARDEX actualizado a la fecha
	        //==========================================================================================================
	        $q = " SELECT COUNT(*) "
	            ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000011 B"
	            ."  WHERE karhis       = '".$wfila[3]."'"
	            ."    AND karing       = '".$wfila[4]."'"
	            ."    AND A.fecha_data = '".$wfecha."'"
	            ."    AND karcon       = 'on' "
	            ."    AND ((karcco     = ccocod "
	            ."    AND  ccolac     != 'on') "
	            ."     OR   karcco     = '*' ) ";
	        $reskar = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowkar = mysql_fetch_array($reskar); 
            
            if ($rowkar[0] > 0)
               $wkardexActualizado="Actualizado";
              else
                 $wkardexActualizado="Sin Actualizar";
            //==========================================================================================================
            
            
            echo "<tr class='encabezadoTabla'>";
			echo "<td align=center class='fondoAmarillo'>Habitación<br>".$wfila[2]."</td>";        //Habitación
		    echo "<td align=center>Historia<br>".$wfila[3]."-".$wfila[4]."</td>";                  //Historia - Ingreso
		    echo "<td align=center colspan=1>Paciente<br>".$wpac."</td>";                          //Nombre paciente
		    echo "<td align=center>Documento<br>".$wdpa."</td>";                                   //Dcto Identificacion
		    echo "<td align=center colspan=3>Responsable<br>".$wfila[7]."-".$wfila[8]."</td>";     //Responsable
		    echo "<td align=center class='fondoAmarillo'>Kardex<br>".$wkardexActualizado."</td>";  //Estado del Kardex
		    echo "</tr>";
			   
		    echo "<tr class='fila1'>";
		    echo "<td><b>Código</b></td>";
		    echo "<td><b>Descripción</b></td>";
		    //echo "<td align=center><b>Envia</b></td>";
		    echo "<td align=center><b>Unidad</b></td>";
		    echo "<td align=center><b>Fecha CTC</b></td>";
		    echo "<td align=center><b>Cantidad<br>Autorizada</b></td>";
		    echo "<td align=center><b>Cantidad<br>Consumida</b></td>";
		    echo "<td align=center><b>Cantidad<br>Restante</b></td>";
		    echo "<td align=center><b>Cantidad<br>Dispensada</b></td>";
		    echo "</tr>";
		    
			while ($i <= $wnum and $wauxcco == $wfila[0] and $wauxhis == $wfila[3]."-".$wfila[4])
			  {
			   if ($i%2==0)
			      $wclass="fila1";
			     else
			       $wclass="fila2";
			   $wcolorAlerta="";     
			       
			       
			       
			   //==========================================================================================================
	           //Traigo la Cantidad Dispensada Real
	           //==========================================================================================================
	           $q = " CREATE TEMPORARY TABLE if not exists TEMPO1 "
		           ." SELECT spauen AS cant "
		           ."   FROM ".$wbasedato."_000004 "
		           ."  WHERE spahis = '".$wfila[3]."'"
		           ."    AND spaing = '".$wfila[4]."'"
		           ."    AND spaart = '".$wfila[5]."'"
		           ."  UNION "
	               ." SELECT spluen AS cant "
	               ."   FROM ".$wbasedato."_000030 "
		           ."  WHERE splhis = '".$wfila[3]."'"
		           ."    AND spling = '".$wfila[4]."'"
		           ."    AND splart = '".$wfila[5]."'";
		       $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		        
		       $q = " SELECT SUM(cant) "
			       ."   FROM TEMPO1 ";
			   $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           $rowdes = mysql_fetch_array($resdes); 
	            
	           if ($rowdes[0] > 0)
	              $wcantidadDispensada=$rowdes[0];
	             else 
	                $wcantidadDispensada=0;
	                
	           $q = " DELETE FROM TEMPO1 ";
	           $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           //==========================================================================================================    
			               
	           
			   echo "<tr class=".$wclass.">";
		       echo "<td>".$wfila[5]."</td>";                                      //Codigo articulo
		       echo "<td>".$wfila[6]."</td>";                                      //Nombre articulo
		       
		       /*
		       //Evaluo si se envia o no
		       if ($wfila[9] == "on")
		          $wenvia="No Enviar";
		         else
		            $wenvia="Enviar"; 
		       echo "<td align=center>".$wenvia."</td>";                           //Envia 
		       */
		       
			   			   
			   //BUSCO SI LA HISTORIA CON EL ARTICULO TIENE CTC
			   $q = " SELECT fecha_data, ctccau, ctccus, ctcuca "                  //cau: Cantidad Autorizada, cus: Cantidad Usuada, uca: Unidad de medida
			       ."   FROM ".$wbasedato."_000095 "
			       ."  WHERE ctchis = '".$wfila[3]."'"                             //Historia
			       ."    AND ctcing = '".$wfila[4]."'"                             //Ingreso
			       ."    AND ctcart = '".$wfila[5]."'";                            //Código Articulo
			   $resctc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           $wnumctc = mysql_num_rows($resctc);
	           
	           if ($wnumctc > 0)
	              {
		           $rowctc = mysql_fetch_array($resctc);
		           
		           echo "<td align=center>".$rowctc[3]."</td>";                    //Unidad de Medida   
		           echo "<td align=center>".$rowctc[0]."</td>";                    //Fecha de Autorizacion  
		           echo "<td align=center>".$rowctc[1]."</td>";                    //Cantidad Autorizada  
		           echo "<td align=center>".$rowctc[2]."</td>";                    //Cantidad Consumida 
		           echo "<td align=center>".((float)$rowctc[1]-(float)$rowctc[2])."</td>";       //Cantidad Restante
		           
		           if ($wcantidadDispensada > $rowctc[1])
			          $wcolorAlerta="fondoRojo";
			         else
			           $wcolorAlerta=""; 
		          }      
		         else
		           {
			        echo "<td align=center colspan=5><b>Sin CTC</b></td>";
			       }   
			   
			   echo "<td align=center class='".$wcolorAlerta."'>".$wcantidadDispensada."</td>";              //Cantidad Dispensada    
		           
		       $wfila = mysql_fetch_array($res);
		       $i++;  
		       echo "</tr>";  
			  }
		   }
		}    
	 echo "</table>";
	}
    
  function mostrar_suspendidos_CM($wsuspendidos, $whis)
    {
    
	 global $conex;
	 global $wsuspendidos;
	 global $wcenmez;
 
     $wclass="fondoVerde";
	 
 	 for ($i=1;$i<=count($wsuspendidos);$i++)
	   {
	    if (isset($wsuspendidos[$whis][$i]))
		   {
		    $q = " SELECT artcom "
				."   FROM ".$wcenmez."_000002 "
				."  WHERE artcod = '".$wsuspendidos[$whis][$i]."'"; 
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);
			$wnomart=$row1[0];
			
			echo "<tr class=".$wclass.">";
			echo "<td> </td>";
			echo "<td><b>".$wsuspendidos[$whis][$i]."</b></td>";    //Codigo articulo
			echo "<td><b>".$wnomart."</b></td>";                    //Nombre articulo
			echo "</tr>";
		   }
	   }
	}	
 
	 
 
	/**********************************************************************************
	 * Consulta
	 **********************************************************************************/
	function consultarTipoProtocoloPorArticulo( $conex, $wbasedato, $cco, $articulo ){

		$sql = "SELECT 
					Arktip
				 FROM
					{$wbasedato}_000068
				 WHERE
					arkcco = '$cco'
					AND arkcod = '$articulo'
					AND arkest = 'on'
				 ";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			if( $row = mysql_fetch_array( $res ) ){
				return $row[ 'Arktip' ];
			}
		}
		
		return false;
	}

		
	// Abril 18 de 2013
	function esArticuloGenericoIncCM( $conexion, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
		
		//Consulto de que tipo es el articulo
		$sql = " SELECT *
				   FROM {$wbasedatoMH}_000068, {$wbasedatoCM}_000002, {$wbasedatoCM}_000001
				  WHERE artcod = '$codArticulo'
					AND arttip = tipcod
					AND tiptpr = arktip
					AND artest = 'on'
					AND arkest = 'on'
					AND tipest = 'on' 
		";
		$res = mysql_query( $sql, $conexion ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
		$numrows = mysql_num_rows( $res );
		if( $numrows > 0 ){
			$rows = mysql_fetch_array( $res );
			return $rows['Arktip'];
		}
	}	
	
	/**********************************************************************************
	 * Consulta código del centro de costos de origen
	 **********************************************************************************/
	/**
	 * Se debe reemplazar la funcion consultarCcoOrigen por consultarCcoOrigenUnificado en el comun.php
	 */
	function consultarCcoOrigen( $conex, $wbasedato, $ori ){

		$sql = "SELECT 
					Ccocod
				 FROM
					{$wbasedato}_000011
				 WHERE
					Ccotim = '$ori'
					AND Ccoest = 'on'
				 ";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			if( $row = mysql_fetch_array( $res ) ){
				return $row[ 'Ccocod' ];
			}
		}
		
		return false;
	}

	/************************************************************************************************************************
	 * Proxima ronda de produccion
	 * 
	 * @return unknown_type
	 ************************************************************************************************************************/
	function proximaRondaProduccion( $fecha, $hora, $tiempo ){
		
		$val = strtotime( "$fecha $hora" )+3600*$tiempo;
		
		return $val;
	}
	/********************************************************************************************************************************************
	 * Consulta la ultima ronda creada para un tipo de articulo
	 * 
	 * @param $tipo
	 * @return unknown_type
	 ********************************************************************************************************************************************/
	function consultarUltimaRonda( $conex, $wbasedato, $tipo, $timeAdd ){
		
		$val = date("Y-m-d 00:00:00");
		
		$fecha = date( "Y-m-d", strtotime( date("Y-m-d H:i:s") )-24*3600 );
		
		/************************************************************************
		 * Agosto 18 de 2011
		 ************************************************************************/
		$proximaHora = ( intval( date( "H", time()+$timeAdd*3600 )/2 )*2 ).":00:00";
		
		return $proximaHora;
		/************************************************************************/
		
		$proximaHora = ( ceil( date( "H" )/2 )*2 ).":00:00";
		
		//Consulto la ultima ronda que se hizo para un tipo de articulo
		//desde el día anterior, esto por que la siguiente ronda puede ser a la medianoche

		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000106
				WHERE
					ecptar = '$tipo'
					AND ecpfec >= '$fecha'
					AND ecpest = 'on'
					AND ( ecpmod = 'P'
					OR ( 
						ecpmod = 'N'
						AND  UNIX_TIMESTAMP( NOW() )  > UNIX_TIMESTAMP(CONCAT( ecpfec,' ',ecpron ) ) 
					)
					OR ( 
						ecpmod = 'N'
						AND  ecpron = '$proximaHora' 
					) )				
				ORDER BY
					ecpfec desc, ecpron desc 
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res) ){
			$val = $rows[ 'Ecpfec' ]." ".$rows[ 'Ecpron' ];
		}
		
		return $val;
	}

	/************************************************************************************************
	 * Trae la informacion correspondiente al tipo de articulo
	 * 
	 * @param $conex
	 * @param $wbasedato
	 * @param $tipo
	 * @return unknown_type
	 ************************************************************************************************/
	function consultarInfoTipoArticulos( $conex, $wbasedato ){

		global $tempRonda;
		
		$val = "";
		
		$tiposAriculos = Array();
		
		//Consultando los tipos de protocolo
		$sql = "SELECT 
					* 
				FROM
					{$wbasedato}_000099 a
				WHERE
					Tarest = 'on'
					AND tarhcp != '00:00:00'
					AND tarhcd != '00:00:00'
				";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
					
		if( $numrows > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['codigo'] = $rows[ 'Tarcod' ];
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['nombre'] = $rows[ 'Tardes' ];
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['tiempoPreparacion'] = $rows[ 'Tarpre' ];
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCorteProduccion'] = $rows[ 'Tarhcp' ];
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCaroteDispensacion'] = $rows[ 'Tarhcd' ];
				
				$aux = consultarUltimaRonda( $conex, $wbasedato, $rows[ 'Tarcod' ], ( strtotime( "1970-01-01 ".$rows[ 'Tarhcp' ] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 );
				
				$auxfec = ""; 
				@list( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'] ) = explode( " ", $aux );
				
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] = proximaRondaProduccion( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $rows[ 'Tarpre' ] );
				
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['tieneArticulos'] = 0;
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['totalArticulosSinFiltro'] = 0;


				if( $rows[ 'Tarcod' ] == "N" ){
					//Agosto 29 de 2012
					$tempRonda = substr( $tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCaroteDispensacion'],0,2 )*3600;
				}
			}
		}
		
		return $tiposAriculos;
	}

	/****************************************************************************************************************
	 * Consulta el tiempo de dispensación para un cco, si no se encuentra tiempo de dispensación,
	 * esta función devuelve false
	 *
	 * Nota: El tiempo de dispensación es devuelta en segundos
	 ****************************************************************************************************************/ 
	function consultarHoraDispensacionPorCco( $conex, $wbasedato, $cco ){

		$val = false;
		
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011
				WHERE
					ccocod = '".$cco."'
					AND ccoest = 'on'
					AND ccotdi != '00:00:00'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			
			// $val = strtotime( date("Y-m-d {$rows['Ccotdi']}") ) - strtotime( date("Y-m-d 00:00:00" ) );
				
			$val = explode( ":", $rows['Ccotdi'] );
			$val = $val[0];
			$val = $val*3600;
		}
		
		return $val;
	}

   function estado_sin_dispensar($regleta_str, $disCco, $kadpro, $wori, $wart)
    {
	  global $wbasedato;
	  global $wcenmez;
	  global $conex;
	  global $wfecha;
	  global $whora_par_actual;

	  $wcco = consultarCcoOrigenUnificado( $conex, $wbasedato, $wori );

	  $info = consultarInfoTipoArticulos( $conex, $wbasedato );
	  //$corteDispensacion = $info[ $kadpro ]['horaCaroteDispensacion'];
	  
	  // Abril 18 de 2013
	  if($wori!='CM')
		$auxProtocoloNew = consultarTipoProtocoloPorArticulo( $conex, $wbasedato, $wcco, $wart );
	  else
		$auxProtocoloNew = esArticuloGenericoIncCM( $conex, $wbasedato, $wcenmez, $wart );

	  if( !empty( $auxProtocoloNew ) ){
		$kadpro = $auxProtocoloNew;
	  }

	  $corteDispensacion = substr( $info[ $kadpro ]['horaCaroteDispensacion'],0 , 2 )*3600;
		
	  if( strtoupper( $kadpro ) != 'LC' && $disCco ){
			$corteDispensacion = $disCco;
	  }
	  $sin_dispensar = 0;

	  if($whora_par_actual!="24")
		$whora_par_actual_fecha = $wfecha." ".$whora_par_actual.":00:00";
	  else
		$whora_par_actual_fecha = $wfecha." 00:00:00";
	  
	  $whora_par_actual_unix = strtotime($whora_par_actual_fecha);
	  
	  $whora_par_anterior_unix = strtotime($whora_par_actual_fecha) - (60*60*2);
	  $whora_par_siguiente_unix = strtotime($whora_par_actual_fecha) + ($corteDispensacion);

	  //echo gmdate( "Y-m-d H:i:s", $whora_par_siguiente_unix )."-";
	  
	  $dia_siguiente = 0;
	  
	  $tamreg = count($regleta_str);
	  
	  for($i=0;$i<$tamreg;$i++)
	  {
		$regleta_ronda = explode("-",$regleta_str[$i]);

		if($regleta_ronda[0]=="00:00:00")
			$dia_siguiente = 60*60*24;		// Sumo un día cuando la regleta pasa al siguiente día
		
		if($regleta_ronda[0]=="Ant")
			$regleta_ronda[0] = "00:00:00";
		
		$regleta_ronda_fecha = $wfecha." ".$regleta_ronda[0];
		$regleta_ronda_fecha_unix = strtotime($regleta_ronda_fecha) + $dia_siguiente;
		
		//echo date("Y-m-d H:i:s", $whora_par_anterior_unix)." - ".date("Y-m-d H:i:s", $regleta_ronda_fecha_unix)." - ".date("Y-m-d H:i:s", $whora_par_siguiente_unix)."<br>";
	  
		if($regleta_ronda_fecha_unix>=$whora_par_anterior_unix && $regleta_ronda_fecha_unix<=$whora_par_siguiente_unix)
		{
			$sin_dispensar = ($regleta_ronda[1]*1)-($regleta_ronda[2]*1);
			if($sin_dispensar>0)
				break;
		}
	  }
	  
	  return $sin_dispensar;
	}
 
  function estado_del_Kardex($whis, $wing, &$westado, $wmuerte, &$wcolor, &$wactual, $wsac, &$esOrdenes, $sCodigoSede = '' )
    {
	  global $wbasedato;
	  global $conex;
	  global $wespera;
	  global $wfecha;
	  global $waltadm;
	  global $wopcion;
	  global $whora_par_actual;
	  global $wemp_pmla;
	  global $selectsede;
	  
	  global $wsuspendidos;
	  
	  global $servicioDomiciliario;

	  //$disCco = false; 
	  $disCco = consultarHoraDispensacionPorCco( $conex, $wbasedato, $wsac );	//consulto el tiempo de dispensación por cco
	  
	  
	  $westado="off";  //Apago el estado, que indica segun la opcion si la historia si esta en el estado que indica la opcion.
	  $wactual="Sin Kardex Hoy";  //Indica si el Kardex esta actualizado a la fecha, off = actualizado al día anterior, on= Actualizado a la fecha
	  $esOrdenes = false;
	  
	  
		$estadosede=consultarAliasPorAplicacion($conexion, $wemp_pmla, "filtrarSede");
		$sFiltroSede="";
		if($estadosede=='on')
		{
			$codigoSede = (isset($sCodigoSede)) ? $sCodigoSede : consultarsedeFiltro();
			$sFiltroSede = (isset($codigoSede) && $codigoSede != '') ? " AND Ccosed = '{$codigoSede}' " : "";
		}

	  switch ($wopcion)   
        {
	     case ("1"):
	         {
              //========================================================================================================================================================================
			  //Dispensado modificado
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que ya fueron dispensadas, pero que tuvieron alguna modificación en sus articulos
			  //luego de la dispensación.
			  $q= " SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 DetKar, ".$wbasedato."_000055 AudKar, ".$wbasedato."_000053 A"
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadhdi       != '00:00:00' "         //Esto me indica que ya fue dispensado
		          ."   AND kadfec        = AudKar.fecha_data "  //Esto me valida que el Kardex sea del día en la tabla de Auditoria
		          ."   AND kadhdi        < AudKar.hora_data "   //Esto me indica que el Kardex tienen alguna modificación despues de dispensado
		          ."   AND kadhis        = kauhis "
		          ."   AND kading        = kauing "
		          ."   AND kadsus       != 'on' "
		          ."   AND kadhdi        > '00:00:00' "
//		          ."   AND kadcdi-kaddis > 0 "					// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadori        = 'SF' " 
		          ."   AND kaumen     LIKE 'Articulo%' "        //Me indica si la modiifcación se hizo en la pestaña de medicamentos.
		          ."   AND Karcon        = 'on' "
		          ."   AND karhis        = Kadhis "
		          ."   AND karing        = Kading "
		          ."   AND Karcco        = Kadcco "
		          ."   AND A.Fecha_data  = kadfec "
		          ."   AND kadare        = 'on' "              //Articulo aprobado para dispensar
				  ."   AND karaut       != 'on' ";              //Que el kardex no sea generado autoamticamente
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1); 
		      
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
			  break;   
	         }     
	  		  
           
	      case ("2"):
	         {
			 
			  //=========================================================================================================================================
			  //Perfil Aprobado sin Dispensar parcial o totalmente
			  //=========================================================================================================================================
			  //Aca muestra todas las historias que no se han dispensado nada
			  $q=  " SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B" 
		          ." WHERE kadhis        = '".$whis."'"
				  ."   AND kading        = '".$wing."'"
				  ."   AND kadfec        = '".$wfecha."'"
				  ."   AND kadsus       != 'on' "
				  ."   AND kadori        = 'SF' "
//				  ."   AND kadcdi-kaddis > 0 "			// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
				  ."   AND Karcon        = 'on' "
				  ."   AND karhis        = Kadhis "
				  ."   AND karing        = Kading "
				  ."   AND Karcco        = Kadcco "
				  ."   AND B.Fecha_data  = kadfec "
				  ."   AND kadare        = 'on' "
				  ."   AND karaut       != 'on' ";
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
	          break;   
	         }

	         
		  case ("3"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes sin Kardex
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que no tienen Kardex
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054, ".$wbasedato."_000011 "
		          ." WHERE karhis            = '".$whis."'"
		          ."   AND karing            = '".$wing."'"
		          ."   AND EncKar.Fecha_data = '".$wfecha."'"     //Esto me valida que el Kardex sea del día
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND EncKar.Fecha_data = kadfec "
		          ."   AND ((kadcco          = ccocod "
		          ."   AND  ccolac          != 'on') "
		          ."    OR kadcco            = '*') "
		          ."   AND kadori            = 'SF' "
				  ."   AND karaut           != 'on' {$sFiltroSede} ";            //Busca que sea generado automaticamente
			  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1); 
		      
		      $wcan=mysql_fetch_array($res1);
			  
		      if ($wcan[0] > 0)   //Indica que existe kardex pero puede que no este confirmado entonces verifico esa condicion
		         {
			      $q=  " SELECT COUNT(*) "
			          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054, ".$wbasedato."_000011 "
			          ." WHERE karhis            = '".$whis."'"
			          ."   AND karing            = '".$wing."'"
			          ."   AND EncKar.Fecha_data = '".$wfecha."'"     //Esto me valida que el Kardex sea del día
			          ."   AND karhis            = kadhis "
			          ."   AND karing            = kading "
			          ."   AND EncKar.Fecha_data = kadfec "
			          ."   AND ((kadcco          = ccocod "
			          ."   AND  ccolac          != 'on') "
			          ."    OR kadcco            = '*') "
			          ."   AND kadori            = 'SF' "
			          ."   AND (karcon            = 'off' "            //Indica que no esta confirmado
					  ."    OR  karaut            = 'on' )";           //Indica que es automatico, si exite pero es autoamtico, quiere decir que no se ha generado
			      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $wnum = mysql_num_rows($res1);    
			      
			      $row=mysql_fetch_array($res1); 
			      
			      if ($row[0] > 0)   //indica que esta creado el kardex pero sin confirmar por el estado debe ser 'on' para que salga esta historia en la lista
			         $wcan[0]=0;
		         }    
		      
		      if ($wcan[0] == 0)     
		         {
			      $westado="on";     //Indica que la historia si esta sin generar el Kardex, es decir en 'off'  
			      $wactual="Actualizado";     //Indica que esta actualizado a la fecha
				  break;
			     } 
			        
			  break;   
	         }
	       
	       case ("4"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes con Kardex y con Antibioticos Confirmados
			  //========================================================================================================================================================================
			  $q= " SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 DetKar, ".$wbasedato."_000053 EncKar "
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
//		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente	// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadcon            = 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'CM' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
				  ."   AND karaut           != 'on' "               //Que no sea generado automaticamente
		          ." UNION "
			      ."SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 DetKar, ".$wbasedato."_000053 EncKar, ".$wbasedato."_000059 deffra"
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
//		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente	// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadcon            = 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'SF' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
		          ."   AND kadart            = defart "
		          ."   AND defcon            = 'on' "
				  ."   AND karaut           != 'on' ";              //Que no sea generado automaticamente
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
	           break;   
	         }   
	         
	         
	       case ("5"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes con Kardex con Articulos del Lactario
			  //========================================================================================================================================================================
			  $q= " SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054, ".$wbasedato."_000053 A, ".$wbasedato."_000011, ".$wbasedato."_000026 "
		          ." WHERE karhis         = '".$whis."'"
		          ."   AND karing         = '".$wing."'"
		          ."   AND karhis         = kadhis "
		          ."   AND karing         = kading "
		          ."   AND kadfec         = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus        != 'on' "
				  ."   AND Kadess        != 'on' "				 //Valida que no esté como no enviar
//		          ."   AND kadcdi-kaddis  > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente	// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadart         = artcod "
		          ."   AND INSTR(ccogka, mid(artgru,1,INSTR(artgru,'-')-1)) "
		          ."   AND ccolac         = 'on' "
		          ."   AND karcon         = 'on' "
		          ."   AND A.Fecha_data   = kadfec ";
		          //."   AND kadare         = 'on' "; 
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
	           break;   
	         } 
	         
	       case ("8"):  //Con esto lo que hace es ejecutar el sigte case
	       case ("6"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes con Antibioticos SIN Confirmar
			  //========================================================================================================================================================================   
			  $q= " SELECT kadcpx, kadpro, kadart, kadori, karord "
		          ."  FROM ".$wbasedato."_000054 DetKar, ".$wbasedato."_000053 EncKar "
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
//		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente	// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadcon           != 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'CM' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
				  ."   AND karaut           != 'on' "               //Que el kardex no sea generado automaticamente
		          ." UNION "
			      ."SELECT kadcpx, kadpro, kadart, kadori, karord "
		          ."  FROM ".$wbasedato."_000054 DetKar, ".$wbasedato."_000053 EncKar, ".$wbasedato."_000059 deffra"
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
//		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente		// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadcon           != 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'SF' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
		          ."   AND kadart            = defart "
		          ."   AND defcon            = 'on' "
				  ."   AND karaut           != 'on' ";               //Que el kardex no sea generado automaticamente

		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  if( !$esOrdenes ){
					$esOrdenes = $wcan['karord'] == 'on';
				  }
					
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
	           break;   
	         }
	         
	       case ("7"):
	         {
			  //========================================================================================================================================================================
			  //Perfil con Articulo(s) Sin Aprobar
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que no se han dispensado nada
			  $q= " SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B" 
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus       != 'on' "
		          ."   AND kadori        = 'SF' "
//		          ."   AND kadcdi-kaddis > 0 "		// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          //."   AND kadcon        = 'on' "             //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND Karcon        = 'on' "
		          ."   AND karhis        = Kadhis "
		          ."   AND karing        = Kading "
		          ."   AND Karcco        = Kadcco "
		          ."   AND B.Fecha_data  = kadfec "
		          ."   AND kadare       != 'on' "
				  ."   AND karaut       != 'on' ";             //Que el kardex no sea generado automaticamente
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
	          break;   
	         }   
			 
		   case ("10"):
	         {
			  //========================================================================================================================================================================
			  //Kardex con Articulo(s) de DISPENSACION ** Nuevos ** o de 1ra Vez Sin Aprobar
			  //Kardex con Articulo(s) de DISPENSACION ** Nuevos o Modificados ** Sin Arobar	//Octubre 05 de 2016
			  //========================================================================================================================================================================
			  $q= " SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B"
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND B.fecha_data  = kadfec "
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
				  // ."   AND kadfec        = A.fecha_data "	//Se debe mirar si es nuevo o modificado, por este motivo, esta condición no cumple
		          ."   AND kadsus       != 'on' "
//		          ."   AND kadcdi-kaddis > 0 "		// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadare       != 'on' "
				  ."   AND kadori        = 'SF' "			//Este filtro descarta medicamentos génericos (LQ0000, NE0000, DA0000, NU0000)
				  ."   AND kadhis        = karhis "
				  ."   AND kading        = karing "
				  ."   AND kadcco		 = karcco "                //Solo hospitalizacion
				  ."   AND karcco        = '*' "                //Solo hospitalizacion
				  // ."   AND karaut       != 'on' "              //Que el kardex no sea generado automaticamente
				  ."   AND kadess       != 'on' "				//Que el medicamento no este marcado cómo enviar. Este filtro descarta insulinas, no enviar, y de stock
				  ."   AND kadlog       IN( 'N','M' )";			//Que el medicamento sea nuevo o modificado

		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
			  break;   
	         }
			 
		   case ("11"):
	         {
				$gruposAntibioticos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'gruposMedicamentosAntibioticos');
				$gpAntibioticos = "'".str_replace( ",", "','", $gruposAntibioticos )."'";
				 
			  //========================================================================================================================================================================
			  //Kardex con Articulo(s) de CENTRAL DE MEZCLAS ** Nuevos ** o de 1ra Vez Sin Aprobar
			  //========================================================================================================================================================================
			  $q= " SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, " . $wbasedato."_000011 " 
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
				  ."   AND kadfec        = A.fecha_data "
				  ."   AND kadfec        = B.fecha_data "
		          ."   AND kadsus       != 'on' "
//		          ."   AND kadcdi-kaddis > 0 "		// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
		          ."   AND kadare       != 'on' "
				  ."   AND kadori        = 'CM' "
				  ."   AND kadhis        = karhis "
				  ."   AND kading        = karing "
				  ."   AND karcco        = '*' "                //Solo hospitalizacion
				  // ."   AND karaut       != 'on' "			//Mayo 13 de 2016. Se quita este filtro. Este filtro indica que el kardex fue generado automaticamente desde la PDA
				  //2015-11-25
				  //no se tiene en cuenta los de CM que sean LEV
				  //Esto para que no salga los pacientes que tienen articulos genericos LQ0000 e IC0000
				  ."   AND A.kadlev		!= 'on' {$sFiltroSede} "
				  ." UNION "
				  //Articulos marcados como Dosis Adaptada
				  ." SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, " . $wbasedato."_000011 "
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
				  ."   AND kadfec        = B.fecha_data "
				  ."   AND kadori        = 'SF' "
		          ."   AND kadsus       != 'on' "	          
				  ."   AND kaddoa        = 'on' "  //Campo que marca el articulo como dosis adaptada.
				  ."   AND kadhis        = karhis "
				  ."   AND kading        = karing "
				  ."   AND karcco        = '*' {$sFiltroSede} "
				  ." UNION "
				  //Articulos marcados como NE (No enteral)
				  ." SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, " . $wbasedato."_000011 "
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
				  ."   AND kadfec        = B.fecha_data "
				  ."   AND kadori        = 'SF' "
		          ."   AND kadsus       != 'on' "	          
				  ."   AND kadnes        = 'on' "  //Campo que marca el articulo como dosis adaptada.
				  ."   AND kadhis        = karhis "
				  ."   AND kading        = karing "
				  ."   AND karcco        = '*' {$sFiltroSede} "
				  ." UNION "
				  //Articulos que son antibióticos nuevos
				  ." SELECT kadcpx, kadpro, kadart, kadori "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B, ".$wbasedato."_000026 C, " . $wbasedato."_000011 "
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
				  ."   AND kadfec        = B.fecha_data "
				  ."   AND kadori        = 'SF' "
		          ."   AND kadsus       != 'on' "	          
				  ."   AND kadhis        = karhis "
				  ."   AND kading        = karing "
				  ."   AND karcco        = '*' "
				  ."   AND Artcod        = Kadart "
				  // ."   AND Kadlog        = Logcod "
				  // ."   AND Logsus        != 'on' "
				  ."   AND kadfec        = A.fecha_data "
				  ."   AND SUBSTRING( Artgru, 1, LOCATE( '-', Artgru )-1 ) IN ( $gpAntibioticos ) {$sFiltroSede} ";
				    
				// echo "<pre>".print_r($q,true)."</pre>" ;
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		    
		      while ($wcan = mysql_fetch_array($res1)) 
		        {
				  $regleta_str = explode(",",$wcan['kadcpx']);
				  
				  $sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
				  
				  if($sin_dispensar>0)
				  {
					  $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
					  $wactual="Actualizado";        //Indica que esta actualizado a la fecha
					  break;
				  }
			    } 
			  break;   
	         }
		
           case ("12"):
		     {
			  $wres="";
			  
			  //========================================================================================================================================================================
			  //Pacientes con Articulos de la Central SUSPENDIDOS
			  //========================================================================================================================================================================
			  $q= " SELECT kadart, kadcfr "
		          ."  FROM ".$wbasedato."_000054 "
		          ." WHERE kadhis                     = '".$whis."'"
		          ."   AND kading                     = '".$wing."'"
		          ."   AND kadfec                     = '".$wfecha."'"                //Esto me valida que el Kardex sea del día
		          ."   AND kadsus                     = 'on' "                        //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori                     = 'CM' "                        //Solo de Central de Mezclas
		          ."   AND CONCAT(kadart,',',kadfec)  NOT IN ( SELECT kadaan "        //Que el articulo no haya sido reemplazado por otro el mismo dia
				  ."                                             FROM ".$wbasedato."_000054 "
				  ."                                            WHERE kadhis = '".$whis."'"
				  ."                                              AND kading = '".$wing."'"
				  ."                                              AND kadfec = '".$wfecha."')";
			  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
			  
			  if ($wnum > 0)         
		         {
			      $westado="on";
			      $wactual="Actualizado";   //Indica que esta actualizado a la fecha
				  
				  for ($i=1;$i<=$wnum;$i++)
				      {
					   $row1 = mysql_fetch_array($res1);
				       $wsuspendidos[$whis][$i]=$row1[0];
					  }
				 } 
	           break;   
	         }		
			 
			
	    }//Aca termina el switch	         
		       
        if ($wmuerte=="on")
		   $westado="Falleció";     
	} 
  	
	
  //=====================================================================================================================================================================	
  //**************************** Aca termina la funcion estado_del_kardex *******************
  
 
//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

	//=======================================================================================================================================================
	//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
	//=======================================================================================================================================================
	if (isset($accion)) {
		switch ($accion) {
			case 'cancelarPreparacionDA': {
					$data = quitarMarcaDA($wbasedato, $wemp_pmla, $wusuario, $historia, $ingreso, $codArticulo, $ido, $marcado);
					echo json_encode($data);
					break;
					return;
				}
			
			case 'ImprimirSticker': {
					ImprimirSticker($dataPac,$wemp_pmla);
					echo json_encode("Exito!");
					break;
					return;
				}

			default:
				break;
		}
	}
	//=======================================================================================================================================================
	//		F I N   F I L T R O S   A J A X 
	//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{ 
?>
  <html>
<head>
  <title>MONITOR KARDEX</title>
  	<!--
  <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
  
  <script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>  
  <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
	-->

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
	
	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		

		</head>
		<script type="text/javascript">
			$(document).ready(function() {
				// -------------------------------------
				//	Tooltip
				// -------------------------------------
				var cadenaTooltipDetalle = $("#tooltipDetalle").val();

				cadenaTooltipDetalle = cadenaTooltipDetalle.split("|");

				for (var i = 0; i < cadenaTooltipDetalle.length - 1; i++) {
					$("#" + cadenaTooltipDetalle[i]).tooltip();
				}
				// -------------------------------------
			});

			function cancelarPreparacionDA(historia, ingreso, codArticulo, ido, elemento) {
				var marcado = $(elemento).prop("checked");


				var mensajeConfirm = "";
				if (marcado) {
					mensajeConfirm = "Desea cancelar la preparacion de la dosis adaptada";
				} else {
					mensajeConfirm = "Desea activar la preparacion de la dosis adaptada";
				}

				jConfirm(mensajeConfirm, "ALERTA", function(resp) {
					if (resp) {

						$.post("Monitor_Kardex.php", {
							consultaAjax: '',
							accion: 'cancelarPreparacionDA',
							wbasedato: $('#wbasedato').val(),
							wemp_pmla: $('#wemp_pmla').val(),
							wusuario: $('#wusuario').val(),
							historia: historia,
							ingreso: ingreso,
							codArticulo: codArticulo,
							ido: ido,
							marcado: marcado
						}, function(data) {
							console.log(data);
							jAlert(data.mensaje, "ALERTA");


							// si hubo error quitar check
							if (data.error == 1) {
								$(elemento).prop("checked", false);
							}


						}, 'json');
					} else {
						$(elemento).prop("checked", false);
					}
				});

			}

			function descargarArchivoContingencia(nameFile) {
				// grab the content of the form field and place it into a variable
				var textToWrite = $(document.documentElement).clone();
				$("meta", textToWrite).remove();
				textToWrite = $(textToWrite).html();

				if (textToWrite != '') {

					//  create a new Blob (html5 magic) that conatins the data from your form feild
					var textFileAsBlob = new Blob([textToWrite], {
						type: 'text/plain'
					});
					// Specify the name of the file to be saved
					var fileNameToSaveAs = nameFile + ".html";

					// create a link for our script to 'click'
					// var downloadLink = document.createElement("a");
					var downloadLink = $("#aDownload")[0];
					//  supply the name of the file (from the var above).
					// you could create the name here but using a var
					// allows more flexability later.
					downloadLink.download = fileNameToSaveAs;

					// allow our code to work in webkit & Gecko based browsers
					// without the need for a if / else block.
					window.URL = window.URL || window.webkitURL;

					// Create the link Object.
					downloadLink.href = window.URL.createObjectURL(textFileAsBlob);

					// click the new link
					downloadLink.click();

				}
			}

			function enter() {
				document.forms.monkardex.submit();
			}

			function cerrarVentana() {
				window.close();
			}
			
			function imprimirSticker(){
				var dataPacs = [];
				$("#PacSoporteNuticional input[type=checkbox]:checked").each(function () {
					var dataPac = $(this).data("info");
					if(dataPac != ""){
						dataPacs.push(dataPac);
					}
				});
				if(dataPacs.length == 0){
					alert("Debe seleccionar por lo menos un registro");
				}
				$.post("Monitor_Kardex.php", {
					consultaAjax: '',
					accion: 'ImprimirSticker',
					dataPac: dataPacs,
					wemp_pmla: $('#wemp_pmla').val(),
				}, function(data) {
					console.log(data);
				}, 'json');
			}

			window.onload = function() {

				setInterval(function() {

					$('.blink').effect("pulsate", {}, 5000);

				}, 1000);

				$(".tooltip").tooltip({
					track: true,
					delay: 0,
					showURL: false,
					opacity: 0.95,
					left: 0
				});
			}
			
			$(function () {
				$("#checkAll").change(function(){
					$('input:checkbox').not(this).prop('checked', this.checked);
				});
			});

			$(document).on('change','#selectsede',function(){
				window.location.href = "Monitor_Kardex.php?wemp_pmla="+$('#wemp_pmla').val()+"&wuser="+$('#wusuario').val()+"&wopcion="+$('#wopcion').val()+"&selectsede="+$('#selectsede').val();
			});

		</script>
		<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
		<style type="text/css">
			A {
				text-decoration: none;
				color: #000066;
			}

			BODY {
				font-family: verdana;
				font-size: 10pt;
				/* height: 1024px; */
				/* width: 1280px; */
				width: auto;
				height: auto;
			}

			.encabezadoTabla {
				background-color: #2A5DB0;
				color: #FFFFFF;
				font-size: 10pt;
				font-weight: bold;

			}

			.fila1 {
				background-color: #C3D9FF;
				color: #000000;
				font-size: 10pt;
			}

			.fila2 {
				background-color: #E8EEF7;
				color: #000000;
				font-size: 10pt;
			}

			.igual

			/* Aqui asignamos de color gris claro el estado IGUAL*/
				{
				background-color: #F0F0F0;
				text-align: center;
				animation-name: igual;
				animation-duration: 1s;
				animation-timing-function: linear;
				animation-iteration-count: infinite;

				-webkit-animation-name: igual;
				-webkit-animation-duration: 1s;
				-webkit-animation-timing-function: linear;
				-webkit-animation-iteration-count: infinite;
			}

			.modificado

			/* Aqui asignamos de color amarillo claro el estado MODIFICADO*/
				{
				background-color: #FAF6E2;
				text-align: center;
				animation-name: modificado;
				animation-duration: 1s;
				animation-timing-function: linear;
				animation-iteration-count: infinite;

				-webkit-animation-name: modificado;
				-webkit-animation-duration: 1s;
				-webkit-animation-timing-function: linear;
				-webkit-animation-iteration-count: infinite;
			}

			.nuevo

			/* Aqui asignamos de color verde claro el estado NUEVO*/
				{
				background-color: #DEFFCF;
				text-align: center;
				animation-name: nuevo;
				animation-duration: 1s;
				animation-timing-function: linear;
				animation-iteration-count: infinite;

				-webkit-animation-name: nuevo;
				-webkit-animation-duration: 1s;
				-webkit-animation-timing-function: linear;
				-webkit-animation-iteration-count: infinite;
			}

			.suspendido {

				/* Aqui asiganamos de color rojo claro el estado SUSPENDIDO */
				background-color: #FFD2D2;
				text-align: center;
				animation-name: suspendido;
				animation-duration: 1s;
				animation-timing-function: linear;
				animation-iteration-count: infinite;

				-webkit-animation-name: suspendido;
				-webkit-animation-duration: 1s;
				-webkit-animation-timing-function: linear;
				-webkit-animation-iteration-count: infinite;
			}

			/*Desde aca comienza el estado suspendido */
			@-moz-keyframes suspendido {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@-webkit-keyframes suspendido {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@keyframes suspendido {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			/*Desde aca comienza el keyframes del estado nuevo */

			@-moz-keyframes nuevo {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@-webkit-keyframes nuevo {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@keyframes nuevo {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}	
			}

			/* Desde aca comienza el keyframes del modificado */
			@-moz-keyframes modificado {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@-webkit-keyframes modificado {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@keyframes modificado {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			/*Desde aca comienza el keyframes del estado igual */
			@-moz-keyframes igual {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@-webkit-keyframes igual {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}

			@keyframes igual {
				0% {
					opacity: 1.0;
				}

				50% {
					opacity: 0.0;
				}

				100% {
					opacity: 1.0;
				}
			}
			

			.tituloPagina {
				font-family: verdana;
				font-size: 18pt;
				overflow: hidden;
				text-transform: uppercase;
				font-weight: bold;
				height: 30px;
				border-top-color: #2A5DB0;
				border-top-width: 1px;
				border-left-color: #2A5DB0;
				border-left-width: 1px;
				border-right-color: #2A5DB0;
				border-bottom-color: #2A5DB0;
				border-bottom-width: 1px;
				margin: 2pt;
			}

			.imprimir-sticker{
				margin-left: 80%;
			}
		</style>
		<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->

  <?php
  
$estadosede=consultarAliasPorAplicacion($conexion, $wemp_pmla, "filtrarSede");
$sFiltroSede="";
$codigoSede = '';
if($estadosede=='on')
{	  
	$codigoSede = (isset($selectsede)) ? $selectsede : consultarsedeFiltro();
	$sFiltroSede = (isset($codigoSede) && ($codigoSede != '')) ? " AND Ccosed = '{$codigoSede}' " : "";
}		                                                                                                       
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp "; 
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res); 
  
  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {   
	      $row = mysql_fetch_array($res);
	      
	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];
	         
	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];
	         
	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];
	         
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
	         
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];   
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
	{
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
		
	}
  
  
  
  
  //Dependiendo de la opcion enviada en los parametros del programa, definido en la tabla root_000021, se coloca el TITULO en la pantalla
  switch ($wopcion)
    {
	 case "0":
	    $wtitulo = "MONITOR DEL KARDEX Y PERFIL FARMACOTERAPEUTICO";
	    break;
	 case "1":
	    $wtitulo = "KARDEX ** DISPENSANDO Y LUEGO MODIFICADO ** ";
	    break;    
	 case "2":
	    $wtitulo = "PERFIL con Articulos ** APROBADOS SIN DISPENSAR ** PARCIAL O TOTALMENTE";
	    break;
	 case "3":
	    $wtitulo = "PACIENTES ** SIN GENERAR ** KARDEX";
	    break;
	 case "4":
	    $wtitulo = "KARDEX CON ANTIBIOTICOS CONFIRMADOS";
	    break;
	 case "5":
	    $wtitulo = "KARDEX CON ARTICULOS DEL LACTARIO";
	    break;
	 case "6":
	    $wtitulo = "KARDEX CON ANTIBIOTICOS ** SIN CONFIRMAR **";
	    break;
	 case "7":
	    $wtitulo = "PERFIL CON Articulos ** SIN APROBAR ** ";
	    break; 
	 case "8":
	    $wtitulo = "KARDEX CON ANTIBIOTICOS ** SIN CONFIRMAR **";   //Esta opcion es la misma que la 6, pero hubo que crearla para el monitor de Enfermeria
	    break;     
	 case "9":
	    $wtitulo = "KARDEX CON ARTICULOS ** NO POS ** "; 
	    break;
     case "10":
	    $wtitulo = "KARDEX CON ARTICULOS ** NUEVOS (1RA VEZ) y MODIFICADOS Sin Aprobar ** DE DISPENSACION "; 
	    break;
     case "11":
	    $wtitulo = "KARDEX CON ARTICULOS ** NUEVOS (1RA VEZ) Sin Aprobar ** DE CENTRAL DE MEZCLAS "; 
	    break;
     case "12":
	    $wtitulo = "KARDEX CON ARTICULOS DE CENTRAL DE MEZCLAS ** SUSPENDIDOS HOY**"; 
	    break;		
    }    
	
  
  if ($wopcion != "0")
    {
	
		$ccoDisCM = '';
		$ccoDisSF = '';

			if($wopcion == 5){ //Opcion 5 equivale al monitor para el lactario
				//Busco los centros de costos a donde van a dispensar, es decir SF o CM
				//Ccodla - > Dispensa al lactario (dla)
				$sql = "SELECT Ccocod, Ccoima
				FROM " . $wbasedato . "_000011
				WHERE
					Ccotra = 'on'
					AND Ccofac = 'on'
					AND Ccoest = 'on'
					AND Ccodla = 'on'  
				";

				$resCcoDis = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());

				while ($rows = mysql_fetch_array($resCcoDis)) {
					if ($rows['Ccoima'] == 'on') {
						$ccoDisCM = $rows['Ccocod'];
					} else {
						$ccoDisSF = $rows['Ccocod'];

					}
				}
			}else{
					//Busco los centros de costos a donde van a dispensar, es decir SF o CM
					$sql = "SELECT Ccocod, Ccoima
					FROM " . $wbasedato . "_000011
					WHERE
						Ccotra = 'on'
						AND Ccofac = 'on'
						AND Ccoest = 'on'
					";

				$resCcoDis = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());

				while ($rows = mysql_fetch_array($resCcoDis)) {
					if ($rows['Ccoima'] == 'on') {
						$ccoDisCM = $rows['Ccocod'];
					} else {
						$ccoDisSF = $rows['Ccocod'];

					}
				}
			}
			
			//FORMA ================================================================
			echo "<form name='monkardex' action='Monitor_Kardex.php?wemp_pmla=" . $wemp_pmla . "' method=post>";

			$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
			encabezado( $wtitulo, $wactualiz, $institucion->baseDeDatos, TRUE );


			echo "<input type='HIDDEN' id='wemp_pmla' name='wemp_pmla' value='" . $wemp_pmla . "'>";
			echo "<input type='HIDDEN' id='wbasedato' name='wbasedato' value='" . $wbasedato . "'>";
			echo "<input type='HIDDEN' id='wcencam' name='wcencam' value='" . $wcencam . "'>";

			if (strpos($user, "-") > 0)
				$wusuario = substr($user, (strpos($user, "-") + 1), strlen($user));

			echo "<input type='HIDDEN' id='wusuario' name='wusuario' value='" . $wusuario . "'>";

			echo "<input type='hidden' id='sede' name= 'sede' value='".$selectsede."'>";

			echo "<input type='hidden' id='wopcion' name= 'wopcion'  value='".$_GET['wopcion']."'>";

	  $usuario = consultarUsuario($conex,$wusuario);   
	  $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');

	  
	  //traer Centro de costo desde root_000025
	  $q = " SELECT cc "
	      ."   FROM root_000025 "
	      ."  WHERE Empleado = '".$wusuario."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  if ($num > 0)
	     {
	      $row = mysql_fetch_array($res);   
	      $wccousu = $row[0];
	     }
	    else
	       {
		    echo "<script type='text/javascript'>"; 
	        echo "alert ('El usuario no existe en la tabla, root_000025 favor comunicarse con Soporte de Sistemas');";
	        echo "</script>";
	       }  
	  
	       
	  //===============================================================================================================================================
	  //ACA COMIENZA EL MAIN DEL PROGRAMA   
	  //===============================================================================================================================================
	  
	  switch ($wopcion)
	    {
		 case "9":    //CTC's
		    {
			  //Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
			  
			  $dia = time()-(1*24*60*60);   //Te resta un dia
	          $wayer = date('Y-m-d', $dia); //Formatea dia 
			  
			  $q = " CREATE TEMPORARY TABLE if not exists TEMPO "
			      ." SELECT ccocod, cconom, ubihac, ubihis, ubiing, kadart, artcom, Ingres, Ingnre, ubisac "
			      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000016, ".$wbasedato."_000054, ".$wbasedato."_000026 "
			      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
			      ."    AND ubisac  = ccocod "
			      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
			      ."    AND ccourg != 'on' "             //Que no se de Urgencias
			      ."    AND ccocir != 'on' "             //Que no se de Cirugia
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND ubihis != '' "
			      ."    AND ubihis  = inghis "
			      ."    AND ubiing  = inging "
			      ."    AND ingtip != '02' "             // 02 = Particular
			      ."    AND ubihis  = kadhis "
			      ."    AND ubiing  = kading "
			      ."    AND kadfec  = '".$wfecha."'"     //HOY
			      ."    AND kadart  = artcod "
			      ."    AND artpos  = 'N' {$sFiltroSede} "
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
			      
	              ."  UNION "
	              
	              ." SELECT ccocod, cconom, ubihac, ubihis, ubiing, kadart, artcom, Ingres, Ingnre, ubisac "
			      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000016, ".$wbasedato."_000054, ".$wbasedato."_000026 "
			      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
			      ."    AND ubisac  = ccocod "
			      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
			      ."    AND ccourg != 'on' "             //Que no se de Urgencias
			      ."    AND ccocir != 'on' "             //Que no se de Cirugia
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND ubihis != '' "
			      ."    AND ubihis  = inghis "
			      ."    AND ubiing  = inging "
			      ."    AND ingtip != '02' "             // 02 = Particular
			      ."    AND ubihis  = kadhis "
			      ."    AND ubiing  = kading "
			      ."    AND kadfec  = '".$wayer."'"      //AYER
			      ."    AND kadart  = artcod "
			      ."    AND artpos  = 'N' {$sFiltroSede} "
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "; 
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  
			  
			  $q = " SELECT ccocod, cconom, ubihac, ubihis, ubiing, kadart, artcom, Ingres, Ingnre, ubisac "
			      ."   FROM TEMPO "
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
			      ."  ORDER BY 1,3,6 ";
			   
			  break;
		 	}
		 default:
		    {
				$tablaHabitaciones = $wbasedato."_000020";
				if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
					$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, "9050" );
				}
				
			  hora_par();
			  
			  if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
				  
				   $q =" SELECT ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac  "
					  ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011 "
					  ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
					  ."    AND ubisac  = ccocod "
					  ."    AND ccodom  = 'on' "             //Que el CC sea domiciliario
					  ."    AND ubihis != '' {$sFiltroSede} "
					  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10  ";
			  }
			  else{
				  
				  //Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
				  $q = " CREATE TEMPORARY TABLE IF NOT EXISTS tempo_ART "
					  ." SELECT ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac  "
					  ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000017 "
					  ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
					  //."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
					  ."    AND ubisac  = ccocod "
					  ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
					  ."    AND ccourg != 'on' "             //Que no se de Urgencias
					  ."    AND ccocir != 'on' "             //Que no se de Cirugia
					  ."    AND ubihis  = habhis "
					  ."    AND ubiing  = habing "
					  ."    AND ubihis != '' "
					  ."    AND ubihis  = eyrhis "            //Estas cuatro linea que siguen son temporales
					  ."    AND ubiing  = eyring "             
					  ."    AND eyrsde  = ccocod "
					  ."    AND eyrtip  = 'Recibo' "
					  ."    AND eyrest  = 'on' {$sFiltroSede} "
					  //."    AND ccocpx != 'on' "              //CICLOS de Producción 'OFF'
					  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10 "
					  ."  UNION ALL "
					  //Solo traigo los pacientes que esten en un servicio que sea con el MODELO DE CICLOS DE PRODUCCION Y DISPENSACION FRECUENTE
					  ." SELECT ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac  "
					  ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000017, ".$wbasedato."_000054, "
					  ."        ".$wbasedato."_000043, ".$wbasedato."_000099 "
					  ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
					  //."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
					  ."    AND ubisac  = ccocod "
					  ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
					  ."    AND ccourg != 'on' "             //Que no se de Urgencias
					  ."    AND ccocir != 'on' "             //Que no se de Cirugia
					  ."    AND ubihis  = habhis "
					  ."    AND ubiing  = habing "
					  ."    AND ubihis != '' "
					  ."    AND ubihis  = eyrhis "            //Estas cuatro linea que siguen son temporales
					  ."    AND ubiing  = eyring "             
					  ."    AND eyrsde  = ccocod "
					  ."    AND eyrtip  = 'Recibo' "
					  ."    AND eyrest  = 'on' "
					  //."    AND ccocpx  = 'on' "              //CICLOS de Producción 'ON'
					  ."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('$wfecha',' ','$whora_par_actual',':00:00')),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
					  ."    AND ubihis  = kadhis "
					  ."    AND ubiing  = kading "
					  ."    AND kadare  = 'on' "
					  ."    AND kadfec  = '".$wfecha."'"
					  ."    AND kadpro  = tarcod "
					  ."    AND tarpdx  = 'off' "            //Tipo de Articulo no se produce en ciclos osea, que son de dispensacion
					  ."    AND kadart  NOT IN ( SELECT artcod FROM ".$wcenmez."_000002 WHERE artcod = kadart) "
					  ."    AND kadper  = percod {$sFiltroSede} "
					  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10 "
					  
					   ."  UNION "
					  
					  ." SELECT CONCAT(habzon,' - ',habcpa) as ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac "
					  ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000020, ".$wbasedato."_000054, ".$wbasedato."_000026, ".$wbasedato."_000011 "
					  ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
					  ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
					  ."    AND ubihis  = habhis "
					  ."    AND ubiing  = habing "
					  ."    AND ubihis != '' "		    
					  ."    AND ubihis  = kadhis "
					  ."    AND ubiing  = kading "
					  ."    AND kadfec  = '".$wfecha."'"      //Hoy
					  ."    AND kadart  = artcod "
					  ."	AND ubisac  = ccocod "
					  ."	AND ccourg  != 'off' {$sFiltroSede} "
					  ."  GROUP BY 1,2,3,4,5,6,7,8,9 ";
					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					 // echo "<pre>".print_r($q,true)."</pre>";
					$q = " SELECT ubihac, ubihis, ubiing, id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac "
						."   FROM tempo_ART "
						."  GROUP BY 1, 2, 5, 6, 7, 8, 9, 10 ";
			  }
			    
			   break;
		 	}	
	 	}	  
	       
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);  
	           	
	  if ($num > 0)
	    {
		  if ($wopcion == "9")   //Entra si es el monitor de CTC's
		     {
			  ctc($res);         // <==== /// * * * C T C ' s * * * \\\
		     } 
		   else
		    {
			  $cadenaTooltipDetalle = "";
		      $j=1;  //Indica cuantas historias tienen estado == "on" y son las unicas llevadas a la matriz   
			  for($i=1;$i<=$num;$i++)
				{
				  $row = mysql_fetch_array($res);  	  
					  
				  $whab = $row[0];   //habitación actual
				  $whis = $row[1];   
				  $wing = $row[2];  
				  $wreg = $row[3];   //Id
			      $whap = $row[5];   //Hora Alta en Proceso
			      $wmue = $row[6];   //Indicador de Muerte
			      $whan = $row[7];   //Habitación Anterior
				  $walp = $row[8];   //Alta en proceso
				  $wptr = $row[9];   //En proceso de traslado
				  $wsac = $row[10];   //Centro de costo actual
			      
			      
		               
			      //Traigo los datos demograficos del paciente
		          $q = " SELECT pacno1, pacno2, pacap1, pacap2, pacced, pactid "
		              ."   FROM root_000036, root_000037 "
		              ."  WHERE orihis = '".$whis."'"
				      ."    AND oriing = '".$wing."'"
				      ."    AND oriori = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
				      ."    AND oriced = pacced "; 
		          $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $rowpac = mysql_fetch_array($respac); 
		               
				  $wpac = $rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3];    //Nombre
				  $wdpa = $rowpac[4];                                                 //Documento del Paciente
			      $wtid = $rowpac[5];                                                 //Tipo de Documento o Identificacion

			      estado_del_kardex($whis, $wing, $westado, $wmue, $wcolor, $wactual, $wsac, $esOrdenes, $codigoSede );     
			      
				  if ($wmue=="on")
			         {
				      $whab=$whan;
				     } 
			      
				  //Llevo los registros con estado=="on" a una matrix, para luego imprimirla por el orden (worden)
			      if ($westado=="on")
			         {
				      $wmat_estado[$j][0]=$whab;
				      $wmat_estado[$j][1]=$whis;
				      $wmat_estado[$j][2]=$wing;
				      $wmat_estado[$j][3]=$wpac;
				      $wmat_estado[$j][4]=$westado;
				      $wmat_estado[$j][5]=$wcolor;
				      $wmat_estado[$j][6]=$walp;
				      $wmat_estado[$j][7]=$wptr;
				      $wmat_estado[$j][8]=$esOrdenes;
				      $wmat_estado[$j][9]=$wdpa;
				      $wmat_estado[$j][10]=$wtid;

					  $j++;
				     } 
			    }	
			     
			     // usort($wmat_estado,'comparacion'); Esto ya no hay que hacerlo

			     echo "<center><table>";
				 echo "<tr><td align=left bgcolor=#fffffff><font size=2 text color=#CC0000><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#fffffff><font size=2 text color=#CC0000><b>Cantidad de Registros: ".($j-1)."</b></font></td></tr>";
				 echo "</table></center>";
				 echo "<a href='#1' onClick='descargarArchivoContingencia(\"".trim($wopcion)."_".date('Y-m-d')."_".date('H')."\"); return false'>Descargar Archivo de Contingencia</a><br>";
				 echo "<a id='aDownload' style='display:none'></a>";
				 
				if ($wopcion=="11" && count($wmat_estado)>0)
				{
					 echo "	<br>
							<div id='divConvensiones' align='center' width='80%'>
								<table align='center' style='border: 1px solid black;border-radius: 5px;' width='25%'>
									<tr>
									<td align='center' style='font-size:8pt' colspan='6'><b>Convenciones</b></td>
									</tr>
									<tr>
									
										<td class='fondoAmarillo' style='border-radius: 5px;font-size:7pt;border: 1px solid black;' >&nbsp;&nbsp;&nbsp;</td>
										<td style='font-size:7pt;vertical-align:top;'>Alta en proceso</td>
									
										<td class='colorAzul4' style='border-radius: 5px;font-size:7pt;border: 1px solid black;'>&nbsp;&nbsp;&nbsp;</td>
										<td style='font-size:7pt;vertical-align:top;'>Proceso de traslado</td>
									
										<td class='fondoVioleta' style='border-radius: 5px;font-size:7pt;border: 1px solid black;' >&nbsp;&nbsp;&nbsp;</td>
										<td style='font-size:7pt;vertical-align:top;'>No pos</td>
									</tr>
								</table>
							</div>
							<br>";
					}

					echo "<center><table style='height:20;'>";
					if ($j > 6)     //Si el numero de Hrias es mayor a 6 entonces lo muestro en dos grupos de columnas (Izq. y Derecha)
					{
						switch ($wopcion) {
							case "2":       //Perfil con articulos aprobados sin dispensar 
							case "3":       //Pacientes SIN Kardex
								{
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th width='101'>Ver</th>";
									echo "<th width='41' bgcolor='#ffffff'>&nbsp</th>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th width='101'>Ver</th>";
									echo "</tr>";

									break;
								}
							case "8": {
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th width='101'>Ver</th>";
									echo "<th width='41' bgcolor='#ffffff'>&nbsp;</th>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th width='101'>Ver</th>";
									echo "<th bgcolor='#ffffff'>&nbsp;</th>";
									echo "</tr>";

									break;
								}
							case "11":       //Articulos de Central de Mezclas NUEVOS (1RA VEZ) Sin Aprobar
								{
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th width='130px'>Paciente</th>";
									echo "<th>Articulos</th>";
									// echo "<th width='101'>Ver</th>";
									echo "<th width='41' bgcolor='#ffffff'>&nbsp</th>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th width='130px'>Paciente</th>";
									echo "<th>Articulos</th>";
									// echo "<th width='101'>Ver</th>";
									echo "</tr>";

									break;
								}
							case "12":       //Articulos de Central de Mezclas Suspendidos
								{
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "</tr>";

									break;
								}

							case "5": //KARDEX CON ARTICULOS DEL LACTARIO
								{
									if($wactivolactario == 'on'){
										echo "<tr class='encabezadoTabla'>";
										echo "<th>Habitacion</th>";
										echo "<th>Historia</th>";
										echo "<th>Paciente</th>";
										echo "<th>Estado</th>";
										// echo "<th>Imprimir todos<input type='checkbox' id='checkAll' data-info='' /></th>";
										echo "<th colspan='2' width='101'>Acción</th>";
										echo "<th width='41' bgcolor='#ffffff'>&nbsp</th>";
										echo "<th>Habitacion</th>";
										echo "<th>Historia</th>";
										echo "<th>Paciente</th>";
										echo "<th>Estado</th>";
										// echo "<th>Imprimir todos</th>";
										echo "<th colspan='2' width='101'>Acción</th>";
										echo "</tr>";
									}else {
										echo "<tr class='encabezadoTabla'>";
										echo "<th>Habitacion</th>";
										echo "<th>Historia</th>";
										echo "<th>Paciente</th>";
										echo "<th width='101'>Ver</th>";
										echo "<th width='41' bgcolor='#ffffff'>&nbsp;</th>";
										echo "<th bgcolor='#ffffff'>&nbsp;</th>";
										echo "<th>Habitacion</th>";
										echo "<th>Historia</th>";
										echo "<th>Paciente</th>";
										echo "<th width='101'>Ver</th>";
										echo "<th bgcolor='#ffffff'>&nbsp;</th>";
										echo "</tr>";
									}

									break;
								}

							default: {
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th width='101'>Ver</th>";
									echo "<th width='41' bgcolor='#ffffff'>&nbsp;</th>";
									echo "<th bgcolor='#ffffff'>&nbsp;</th>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th width='101'>Ver</th>";
									echo "<th bgcolor='#ffffff'>&nbsp;</th>";
									echo "</tr>";

									break;
								}
						}
					} else {
						switch ($wopcion) {
							case "12":       //Articulos de Central de Mezclas Suspendidos
								{
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "</tr>";

									break;
								}
							case "11":       //Articulos de Central de Mezclas NUEVOS (1RA VEZ) Sin Aprobar
								{
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th>Articulos</th>";
									// echo "<th width='101'>Ver</th>";
									echo "<th width='41' bgcolor='#ffffff'>&nbsp</th>";
									echo "</tr>";

									break;
								}

							case "5": //KARDEX CON ARTICULOS DEL LACTARIO

								{
									if($wactivolactario == 'on'){
										echo "<tr class='encabezadoTabla'>";
										echo "<th>Habitacion</th>";
										echo "<th>Historia</th>";
										echo "<th>Paciente</th>";
										echo "<th>Estado</th>";
										// echo "<th>Imprimir todos<input type='checkbox' id='checkAll' data-info='' /></th>";
										echo "<th colspan='2' width='101'>Acción</th>";
										echo "<th width='41' bgcolor='#ffffff'>&nbsp</th>";
										// echo "<th>Habitacion</th>";
										// echo "<th>Historia</th>";
										// echo "<th>Paciente</th>";
										// echo "<th>Estado</th>";
										// echo "<th>Imprimir todos<input type='checkbox' id='checkAll' data-info='' /></th>";
										// echo "<th colspan='2' width='101'>Acción</th>";
										
										echo "</tr>";
									}else{
										echo "<tr class='encabezadoTabla'>";
										echo "<th>Habitacion</th>";
										echo "<th>Historia</th>";
										echo "<th>Paciente</th>";
										echo "<th width='101'>Ver</th>";
										echo "<th width='41' bgcolor='#ffffff'>&nbsp;</th>";
										echo "<th bgcolor='#ffffff'>&nbsp;</th>";
										// echo "<th>Habitacion</th>";
										// echo "<th>Historia</th>";
										// echo "<th>Paciente</th>";
										// echo "<th width='101'>Ver</th>";
										// echo "<th bgcolor='#ffffff'>&nbsp;</th>";
										echo "</tr>";
									}
							
									break;
								}


							default:         //Pacientes SIN Kardex y Resto
								{
									echo "<tr class='encabezadoTabla'>";
									echo "<th>Habitacion</th>";
									echo "<th>Historia</th>";
									echo "<th>Paciente</th>";
									echo "<th width='101'>Ver</th>";
									echo "<th width='41' bgcolor='#ffffff'>&nbsp</th>";
									echo "</tr>";

									break;
								}
						}
					}

					$wsw = 0;

					for ($i = 1; $i <= $j - 1; $i++) {

						$procedimientosSinLeer = "";
						$blink_sin_leer = "";
						$texto_sin_leer = "";

						// $procedimientosSinLeer = consultarMensajesSinLeer( $conex, $wbasedato, $wmat_estado[$i][1], $wmat_estado[$i][2] );						 
						$procedimientosSinLeer = consultarMensajesSinLeer($conex, $wbasedato, "", $wmat_estado[$i][1], $wmat_estado[$i][2], "", "");

						if ($procedimientosSinLeer > 0) {

							$blink_sin_leer = "fondoRojo blink tooltip";
							$texto_sin_leer = "Mensajes sin leer";
						}

						if ($j > 6)  //Numero de lineas que se muestran en pantalla por defecto si es mayor lo muestro en dos grupos de columnas
						{
							if ($wsw == 0) {
								$wclass = "fila1";
								$wsw = 1;
							} else {
								$wclass = "fila2";
								$wsw = 0;
							}

							$walp = $wmat_estado[$i][6];
							$wptr = $wmat_estado[$i][7];

							$walta_tras = "";

							if ($walp == "on")
								$walta_tras = "fondoAmarillo";

							if ($wptr == "on")
								$walta_tras = "colorAzul4";

								if ($wopcion == 5 && $wactivolactario == 'on') //Se hace condición para que en esta opción 5 muestre una columna de más llamada Estado.
								{
									//print_r($wmat_estado);
									echo "<tr class=" . $wclass . " id='".$wmat_estado[$i][1]."'>";
									echo "<td class='" . $walta_tras . " " . $blink_sin_leer . "' title='" . $texto_sin_leer . "' align=center>&nbsp;" . $wmat_estado[$i][0] . "</td>"; //N Habitación
									echo "<td class=" . $walta_tras . " align=center>" . $wmat_estado[$i][1] . " - " . $wmat_estado[$i][2] . "</td>"; //Historia - ingreso
									echo "<td class=" . $walta_tras . " align=left  >" . $wmat_estado[$i][3] . "</td>"; //Nombre de paciente
									echo "<td class=" . $walta_tras . " align=left  id='estado_".$wmat_estado[$i][1]."' >". $estado ."</td>"; //Estado
									// echo "<td align='center' ><input type='checkbox' data-info='" . json_encode($dataPac) . "'/>&nbsp; Imprimir</td>"; //Imprimir
								}else{
									echo "<tr class=" . $wclass . ">";
									echo "<td class='" . $walta_tras . " " . $blink_sin_leer . "' title='" . $texto_sin_leer . "' align=center>&nbsp;" . $wmat_estado[$i][0] . "</td>"; //N Habitación
									echo "<td class=" . $walta_tras . " align=center>" . $wmat_estado[$i][1] . " - " . $wmat_estado[$i][2] . "</td>"; //Historia - ingreso
									echo "<td class=" . $walta_tras . " align=left  >" . $wmat_estado[$i][3] . "</td>"; //Nombre de paciente
								}
								

							if ($wopcion != "12") {
								if ($wopcion == "3" or $wopcion == "8")      //Esta es la opcion de historias que no tienen kardex actualizado
									if ($wmat_estado[$i][8])
										echo "<td class=" . $walta_tras . " align=center><A href='../../hce/procesos/ordenes.php?wemp_pmla=" . $wemp_pmla . "&wcedula=" . $wmat_estado[$i][9] . "&wtipodoc=" . $wmat_estado[$i][10] . "&hce=on&programa=gestionEnfermeria&et=on&pgr_origen=gestionEnfermeria&esDeAyuda=off' target=_blank> Ir a Ordenes</A></td>";
									else
										echo "<td class=" . $walta_tras . "><div align='center'><A href='generarKardex.php?wemp_pmla=" . $wemp_pmla . "&waccion=b&whistoria=" . $wmat_estado[$i][1] . "&wingreso=" . $wmat_estado[$i][2] . "&et=on&wfecha=" . $wfecha . "' target=_blank> Ir al Kardex </A></td>";
								else {
									if ($wopcion == "11") {
										// pintar articulos y opcion de ir a central de mezclas
										$articulosCM = consultarArticulosCM($wmat_estado[$i][1],$wmat_estado[$i][2]);
											
										if(count($articulosCM)>0)
										{
											echo "	<td class=".$walta_tras." style='width:100%;height:100%;'>";
												echo "	<table style='width:100%;height:100%;'>";
												foreach($articulosCM as $keyArtCM => $valueArtCM)
												{
													if ($fila_lista=='Fila1')
														$fila_lista = "Fila2";
													else
														$fila_lista = "Fila1";
													
													$observacionesCM = "";
													if($valueArtCM['observaciones']!="")
													{
														$observacionesCM = "Observaciones: <br>".trim($valueArtCM['observaciones']);
													}
													// ------------------------------------------
													// Tooltip
													// ------------------------------------------	
														$infoTooltip = "Tipo: ".$valueArtCM['info']."<br> Fecha y hora de inicio: ".$valueArtCM['fechaHorainicio']."<br>".$observacionesCM;
														$tooltip = "<div id=\"dvTooltip_".$valueArtCM['ido']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltip."</div>";
														$cadenaTooltipDetalle .= "tooltipDetalle_".$valueArtCM['ido']."|";
													// ------------------------------------------					
												
													$urlCM = $valueArtCM['url'];
													
													$cancelarDA = "";
													if($valueArtCM['tipo']=="Dosis Adaptada")
													{
														$cancelarDA = "<span style='font-size:8pt;'><input type='checkbox' id='noPrepararDA' name='noPrepararDA' onclick='cancelarPreparacionDA(\"".$wmat_estado[$i][1]."\",\"".$wmat_estado[$i][2]."\",\"".$valueArtCM['articulo']."\",\"".$valueArtCM['ido']."\",this);'>No preparar como DA</span>";
													}
													
													$fondoFila = "";
													if($valueArtCM['nopos']==true)
													{
														$fondoFila = "class='fondoVioleta'";
													}
													
													echo "  <tr ".$fondoFila." id='tooltipDetalle_".$valueArtCM['ido']."' title='".$tooltip."' style='cursor:pointer;'>
																<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:112px;'>".$valueArtCM['articulo']." - ".$valueArtCM['generico']."</td>
																<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:50px;'>".$valueArtCM['dosis']." ".$valueArtCM['unidad']."</td>
																<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:90px;'>".$valueArtCM['fechaHorainicio']."</td>
																<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:100px;' align='center'>".$urlCM."</td>
																<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:50px;' align='center'>".$cancelarDA."</td>
															</tr>";
												}
												
												echo "	</table>";
													
											echo "	</td>";
										}
										
									}
									elseif ($wopcion != "2")
								    {
									   echo "<td class=".$walta_tras."><div align='center'><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>"; 
								    }
									
									//Cargar
									if ($wopcion == "11")
								    {
										// echo "<td class=".$walta_tras."><div align='center'><A href='../../".$wcenmez."/procesos/cargoscm.php?wbasedato=lotes.php&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
									}
									elseif ($wopcion!="4" and $wopcion!="6" and $wopcion!="7")  // 4 = es la opcion de antibioticos, debe ejecutar es el programa de cargos de la central de mezclas
									{
									   echo "<td class=".$walta_tras."><div align='center'><A href='cargos.php?wemp_pmla=".$wemp_pmla."&bd=".$wbasedato."&tipTrans=C&wemp=".$wemp_pmla."&cco[cod]=".$ccoDisSF."&historia=".$wmat_estado[$i][1]."&fecDispensacion=".$wfecha."' target=_blank> Cargar </A></td>";
									}
									else
									{
										if ($wopcion != "7")
											echo "<td class=".$walta_tras."><div align='center'><A href='../../".$wcenmez."/procesos/cargoscm.php?wemp_pmla=".$wemp_pmla."&wbasedato=lotes.php?wemp_pmla=".$wemp_pmla."&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
										else
										   echo "<td bgcolor='#ffffff'>&nbsp;</td>";
									}
								}


								echo "<td align='center' bgcolor='#ffffff'>&nbsp;</td>";
								$i++;

								$procedimientosSinLeer = "";
								$blink_sin_leer = "";
								$texto_sin_leer = "";

								// $procedimientosSinLeer = consultarMensajesSinLeer( $conex, $wbasedato, $wmat_estado[$i][1], $wmat_estado[$i][2] );						 
								$procedimientosSinLeer = consultarMensajesSinLeer($conex, $wbasedato, "", $wmat_estado[$i][1], $wmat_estado[$i][2], "", "");

								if ($procedimientosSinLeer > 0) {

									$blink_sin_leer = "fondoRojo blink tooltip";
									$texto_sin_leer = "Mensajes sin leer";
								}

								$walta_tras = "";
								if (isset($wmat_estado[$i][6])) $walp = $wmat_estado[$i][6];
								if (isset($wmat_estado[$i][7])) $wptr = $wmat_estado[$i][7];

								if ($walp == "on")
									$walta_tras = "fondoAmarillo";

								if ($wptr == "on")
									$walta_tras = "colorAzul4";

								if ($i <= ($j - 1)) {
									
									echo "<td class='" . $walta_tras . " " . $blink_sin_leer . "' title='" . $texto_sin_leer . "' align=center>&nbsp;" . $wmat_estado[$i][0] . "</td>"; //Habitacion 
									echo "<td class=" . $walta_tras . " align=center>" . $wmat_estado[$i][1] . " - " . $wmat_estado[$i][2] . "</td>";  //Historia-Ingreso
									echo "<td class=" . $walta_tras . " align=left  >" . $wmat_estado[$i][3] . "</td>";                            //Paciente

									if ($wopcion == 5 && $wactivolactario == 'on') //Se hace condición para que en esta opción 5 muestre una columna de más llamada Estado.
									{
										echo "<td class=" . $walta_tras . " align=left  id='estado_".$wmat_estado[$i][1]."' >". $estado ."</td>"; //Estado
										// echo "<td align='center' ><input type='checkbox' data-info='" . json_encode($dataPac) . "'/>&nbsp; Imprimir</td>"; //Imprimir
									}
									
									if ($wopcion == "3" or $wopcion == "8" or $wopcion == "12")  //Esta es la opcion de historias que no tienen kardex actualizado
										if ($wmat_estado[$i][8])
											echo "<td class=" . $walta_tras . " align=center><A href='../../hce/procesos/ordenes.php?wemp_pmla=" . $wemp_pmla . "&wcedula=" . $wmat_estado[$i][9] . "&wtipodoc=" . $wmat_estado[$i][10] . "&hce=on&programa=gestionEnfermeria&et=on&pgr_origen=gestionEnfermeria&esDeAyuda=off' target=_blank> Ir a Ordenes</A></td>";
										else
											echo "<td class=" . $walta_tras . "><div align='center'><A href='generarKardex.php?wemp_pmla=" . $wemp_pmla . "&waccion=b&whistoria=" . $wmat_estado[$i][1] . "&wingreso=" . $wmat_estado[$i][2] . "&et=on&wfecha=" . $wfecha . "' target=_blank> Ir al Kardex </A></td>";
									else {
										if ($wopcion == "11") {
											// pintar articulos y opcion de ir a central de mezclas
											$articulosCM = consultarArticulosCM($wmat_estado[$i][1],$wmat_estado[$i][2]);
																					
											if(count($articulosCM)>0)
											{
												echo "	<td class='".$walta_tras."'>";
													echo "	<table  style='width:100%;height:100%;'>";
													
													foreach($articulosCM as $keyArtCM => $valueArtCM)
													{
														if ($fila_lista=='Fila1')
															$fila_lista = "Fila2";
														else
															$fila_lista = "Fila1";
														
														$observacionesCM = "";
														if($valueArtCM['observaciones']!="")
														{
															$observacionesCM = "Observaciones: <br>".trim($valueArtCM['observaciones']);
														}
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
															$infoTooltip = "Tipo: ".$valueArtCM['info']."<br> Fecha y hora de inicio: ".$valueArtCM['fechaHorainicio']."<br>".$observacionesCM;
															$tooltip = "<div id=\"dvTooltip_".$valueArtCM['ido']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltip."</div>";
															$cadenaTooltipDetalle .= "tooltipDetalle_".$valueArtCM['ido']."|";
														// ------------------------------------------					
													
														
														$urlCM = $valueArtCM['url'];
														
														$cancelarDA = "";
														if($valueArtCM['tipo']=="Dosis Adaptada")
														{
															$cancelarDA = "<span style='font-size:8pt;'><input type='checkbox' id='noPrepararDA' name='noPrepararDA' onclick='cancelarPreparacionDA(\"".$wmat_estado[$i][1]."\",\"".$wmat_estado[$i][2]."\",\"".$valueArtCM['articulo']."\",\"".$valueArtCM['ido']."\",this);'>No preparar como DA</span>";
														}
														
														$fondoFila = "";
														if($valueArtCM['nopos']==true)
														{
															$fondoFila = "class='fondoVioleta'";
														}
														
														echo "  <tr ".$fondoFila." id='tooltipDetalle_".$valueArtCM['ido']."' title='".$tooltip."' style='cursor:pointer;'>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:112px;'>".$valueArtCM['articulo']." - ".$valueArtCM['generico']."</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:50px;'>".$valueArtCM['dosis']." ".$valueArtCM['unidad']."</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:90px;'>".$valueArtCM['fechaHorainicio']."</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:100px;' align='center'>".$urlCM."</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:50px;' align='center'>".$cancelarDA."</td>
																</tr>";
																
													}
														
													echo "	</table>";
														
												echo "	</td>";
											}
											
										}
										elseif ($wopcion != "2")    
										   echo "<td class=".$walta_tras."><div align='center'><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>"; 

										//Cargar
										if ($wopcion == "11")
										{
											// echo "<td class=".$walta_tras."><div align='center'><A href='../../".$wcenmez."/procesos/cargoscm.php?wbasedato=lotes.php&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
										}
										elseif ($wopcion!="4" and $wopcion!="6" and $wopcion!="7")  // 4 = es la opcion de antibioticos, debe ejecutar es el programa de cargos de la central de mezclas
										   echo "<td class=".$walta_tras."><div align='center'><A href='cargos.php?wemp_pmla=".$wemp_pmla."&bd=".$wbasedato."&tipTrans=C&wemp=".$wemp_pmla."&cco[cod]=".$ccoDisSF."&historia=".$wmat_estado[$i][1]."&fecDispensacion=".$wfecha."' target=_blank> Cargar </A></td>";
										  else
											{
											 if ($wopcion != "7")
												echo "<td class=".$walta_tras."><div align='center'><A href='../../".$wcenmez."/procesos/cargoscm.php?wemp_pmla=".$wemp_pmla."&wbasedato=lotes.php&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
											   else
												  echo "<td bgcolor='#ffffff' align=center>&nbsp;</td>";
											}
									  } 
								}    
							 echo "</tr>";   
							}
						   else
                              {
							   //*** S U S P E N D I D O S ***
							   if (count($wsuspendidos) > 0)
							      {                   //  Arreglo de suspen., Historia     
                                   mostrar_suspendidos_CM($wsuspendidos     , $wmat_estado[$i][1]);
								  } 
							   echo "</tr>";
                              }							  
		                }
		               else 
		                  {  

							$walp = $wmat_estado[$i][6];
							$wptr = $wmat_estado[$i][7];

							if (is_integer($i / 2))
								$wclass = "fila1";
							else
								$wclass = "fila2";

							$walta_tras = "";

							if ($walp == "on")
								$walta_tras = "fondoAmarillo";

							if ($wptr == "on")
								$walta_tras = "colorAzul4";

							if ($wopcion == 5 && $wactivolactario == 'on') //Se hace condición para que en esta opción 5 muestre una columna de más llamada Estado.
							{
								//print_r($wmat_estado);
								echo "<tr class=" . $wclass . " id='".$wmat_estado[$i][1]."'>";
								echo "<td class='" . $walta_tras . " " . $blink_sin_leer . "' title='" . $texto_sin_leer . "' align=center>&nbsp;" . $wmat_estado[$i][0] . "</td>"; //N Habitación
								echo "<td class=" . $walta_tras . " align=center>" . $wmat_estado[$i][1] . " - " . $wmat_estado[$i][2] . "</td>"; //Historia - ingreso
								echo "<td class=" . $walta_tras . " align=left  >" . $wmat_estado[$i][3] . "</td>"; //Nombre de paciente
								echo "<td class=" . $walta_tras . " align=left  id='estado_".$wmat_estado[$i][1]."' >". $estado ."</td>"; //Estado
								// echo "<td align='center' ><input type='checkbox' data-info='" . json_encode($dataPac) . "'/>&nbsp; Imprimir</td>"; //Imprimir
							} else {
								echo "<tr class=" . $wclass . ">";
								echo "<td class='" . $walta_tras . " " . $blink_sin_leer . "' title='" . $texto_sin_leer . "' align=center>&nbsp;" . $wmat_estado[$i][0] . "</td>"; //N Habitación
								echo "<td class=" . $walta_tras . " align=center>" . $wmat_estado[$i][1] . " - " . $wmat_estado[$i][2] . "</td>"; //Historia - ingreso
								echo "<td class=" . $walta_tras . " align=left  >" . $wmat_estado[$i][3] . "</td>"; //Nombre de paciente
							}

							if ($wopcion != "12") {
								if ($wopcion == "3" or $wopcion == "8")   //Esta es la opcion de historias que no tienen kardex actualizado
									if ($wmat_estado[$i][8])
										echo "<td class=" . $walta_tras . " align=center><A href='../../hce/procesos/ordenes.php?wemp_pmla=" . $wemp_pmla . "&wcedula=" . $wmat_estado[$i][9] . "&wtipodoc=" . $wmat_estado[$i][10] . "&hce=on&programa=gestionEnfermeria&et=on&pgr_origen=gestionEnfermeria&esDeAyuda=off' target=_blank> Ir a Ordenes</A></td>";
									else
										echo "<td class=" . $walta_tras . " align=center><A href='generarKardex.php?wemp_pmla=" . $wemp_pmla . "&waccion=b&whistoria=" . $wmat_estado[$i][1] . "&wingreso=" . $wmat_estado[$i][2] . "&et=on&wfecha=" . $wfecha . "' target=_blank> Ir al Kardex </A></td>";
								else {
									if ($wopcion == "11") {
										// pintar articulos y opcion de ir a central de mezclas
										$articulosCM = consultarArticulosCM($wmat_estado[$i][1], $wmat_estado[$i][2]);

										if (count($articulosCM) > 0) {
											echo "	<td class='" . $walta_tras . "'>";
											echo "	<table style='width:100%;height:100%;'>";

											foreach ($articulosCM as $keyArtCM => $valueArtCM) {
												if ($fila_lista == 'Fila1')
													$fila_lista = "Fila2";
												else
													$fila_lista = "Fila1";

												$observacionesCM = "";
												if ($valueArtCM['observaciones'] != "") {
													$observacionesCM = "Observaciones: <br>" . trim($valueArtCM['observaciones']);
												}
												// ------------------------------------------
												// Tooltip
												// ------------------------------------------	
												$infoTooltip = "Tipo: " . $valueArtCM['info'] . "<br> Fecha y hora de inicio: " . $valueArtCM['fechaHorainicio'] . "<br>" . $observacionesCM;
												$tooltip = "<div id=\"dvTooltip_" . $valueArtCM['ido'] . "\" style=\"font-family:verdana;font-size:10pt\">" . $infoTooltip . "</div>";
												$cadenaTooltipDetalle .= "tooltipDetalle_" . $valueArtCM['ido'] . "|";
												// ------------------------------------------					


												$urlCM = $valueArtCM['url'];

												$cancelarDA = "";
												if ($valueArtCM['tipo'] == "Dosis Adaptada") {
													$cancelarDA = "<span style='font-size:8pt;'><input type='checkbox' id='noPrepararDA' name='noPrepararDA' onclick='cancelarPreparacionDA(\"" . $wmat_estado[$i][1] . "\",\"" . $wmat_estado[$i][2] . "\",\"" . $valueArtCM['articulo'] . "\",\"" . $valueArtCM['ido'] . "\",this);'>No preparar como DA</span>";
												}

												$fondoFila = "";
												if ($valueArtCM['nopos'] == true) {
													$fondoFila = "class='fondoVioleta'";
												}

												echo "  <tr " . $fondoFila . " id='tooltipDetalle_" . $valueArtCM['ido'] . "' title='" . $tooltip . "' style='cursor:pointer;'>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:150px;'>" . $valueArtCM['articulo'] . " - " . $valueArtCM['generico'] . "</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:50px;'>" . $valueArtCM['dosis'] . " " . $valueArtCM['unidad'] . "</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:80px;'>" . $valueArtCM['fechaHorainicio'] . "</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:100px;' align='center'>" . $urlCM . "</td>
																	<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:90px;' align='center'> " . $cancelarDA . "</td>
																</tr>";
																
													}
													echo "	</table>";
														
												echo "	</td>";
											}
										}
										else
										{
											echo "<td class=".$walta_tras." align=center><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>"; 
										}
									    
									    //Cargar
									    if ($wopcion == "11")
										{
											// echo "<td class=".$walta_tras."><div align='center'><A href='../../".$wcenmez."/procesos/cargoscm.php?wbasedato=lotes.php&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
										}
										elseif ($wopcion!="4" and $wopcion!="6" and $wopcion!="7")  // 4 = es la opcion de antibioticos, debe ejecutar es el programa de cargos de la central de mezclas
											echo "<td class=".$walta_tras." align=center><A href='cargos.php?wemp_pmla=".$wemp_pmla."&bd=".$wbasedato."&tipTrans=C&wemp=".$wemp_pmla."&cco[cod]=".$ccoDisSF."&historia=".$wmat_estado[$i][1]."&fecDispensacion=".$wfecha."' target=_blank> Cargar </A></td>";
										else
										    if ($wopcion!="6" and $wopcion!="7")  //Con Antibioticos SIN Confirmar no debe salir Cargar
											  echo "<td class=".$walta_tras." align=center><A href='../../".$wcenmez."/procesos/cargoscm.php?wemp_pmla=".$wemp_pmla."wbasedato=lotes.php?wemp_pmla=".$wemp_pmla."&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
											else
												echo "<td align='center' bgcolor='#ffffff'>&nbsp;</td>"; 
									}
							  }
							 else
							    {
								 //*** S U S P E N D I D O S ***
								 if (count($wsuspendidos) > 0)
                                    {                   //  Arreglo de suspen., Hístoria     
                                     mostrar_suspendidos_CM($wsuspendidos     , $wmat_estado[$i][1]);
									}							 
								}
					       echo "</tr>";
				          } 
		            }
			}  // fin del else que no es CTC
		}
		 else
		    echo "NO HAY HABITACIONES OCUPADAS"; 
	  echo "</table>";
		//window.location.href = "Monitor_Kardex.php?wemp_pmla="+$('#wemp_pmla').val()+"&wuser="+$('#wusuario').val()+"&wopcion="+$('#wopcion').val()+"&selectsede="+$('#selectsede').val();
		$sUrlCodigoSede = ($estadosede=='on') ? '&selectsede='.$codigoSede : '';
	  if ($wopcion=="9")
	     echo "<meta http-equiv='refresh' content='300;url=Monitor_Kardex.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wopcion=".$wopcion.$sUrlCodigoSede."'>";
	    else
			if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' )
				echo "<meta http-equiv='refresh' content='240;url=Monitor_Kardex.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wopcion=".$wopcion."&servicioDomiciliario=on".$sUrlCodigoSede."'>";
			else
				echo "<meta http-equiv='refresh' content='240;url=Monitor_Kardex.php?wemp_pmla=" . $wemp_pmla . "&wuser=" . $user . "&wopcion=" . $wopcion . $sUrlCodigoSede."'>";


			echo "</form>";
			
			echo "<br>";
			echo "<table>";
			echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'>";
			// if($wopcion == "5"){
			// 	echo "&nbsp;<input type=button value='Imprimir sticker' onclick='imprimirSticker()'>";
			// }
			echo "</td></tr>";
			echo "<input type='hidden' id='tooltipDetalle' value='" . $cadenaTooltipDetalle . "'>";
			echo "</table>";

			//Desde aca comienza el detalle del kardex que tiene cada paciente
			if ($wopcion == 5 && $wactivolactario == 'on') //se hace condición para que solo muestre la tabla de pacientes con la informacion de medicamento
			{
				function estado_del_Kardex_lactario($whis, $wing, &$westado, $wmuerte, &$wcolor, &$wactual, $wsac, &$esOrdenes)
				{
					global $wbasedato;
					global $conex;
					global $wespera;
					global $wfecha;
					global $waltadm;
					global $wopcion;
					global $whora_par_actual;
					global $wemp_pmla;

					global $wsuspendidos;

					global $servicioDomiciliario;

					//$disCco = false; 
					$disCco = consultarHoraDispensacionPorCco($conex, $wbasedato, $wsac);	//consulto el tiempo de dispensación por cco


					$westado = "off";  //Apago el estado, que indica segun la opcion si la historia si esta en el estado que indica la opcion.
					$wactual = "Sin Kardex Hoy";  //Indica si el Kardex esta actualizado a la fecha, off = actualizado al día anterior, on= Actualizado a la fecha
					$esOrdenes = false;


					switch ($wopcion) {
						case ("5"): {
								//========================================================================================================================================================================
								//Pacientes con Kardex con Articulos del Lactario
								//========================================================================================================================================================================
								$q = " SELECT kadcpx, kadpro, kadart, kadori "
									. "  FROM " . $wbasedato . "_000054, " . $wbasedato . "_000053 A, " . $wbasedato . "_000011, " . $wbasedato . "_000026 "
									. " WHERE karhis         = '" . $whis . "'"
									. "   AND karing         = '" . $wing . "'"
									. "   AND karhis         = kadhis "
									. "   AND karing         = kading "
									. "   AND kadfec         = '" . $wfecha . "'"       //Esto me valida que el Kardex sea del día
									//. "  # AND kadsus        != 'on' "
									. "   AND Kadess        != 'on' "				 //Valida que no esté como no enviar
									//		          ."   AND kadcdi-kaddis  > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente	// Se comenta porque ya no se mira lo dispensado hasta la hora de corte sino seg{un la frecuencia del centro de costo actual
									. "   AND kadart         = artcod "
									. "   AND INSTR(ccogka, mid(artgru,1,INSTR(artgru,'-')-1)) "
									. "   AND ccolac         = 'on' "
									. "   AND karcon         = 'on' "
									. "   AND A.Fecha_data   = kadfec ";
								//."   AND kadare         = 'on' "; 
								$res1 = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
								$wnum = mysql_num_rows($res1);
								//   print_r(mysql_fetch_array($res1));
								//   die();
								while ($wcan = mysql_fetch_array($res1)) {
									$regleta_str = explode(",", $wcan['kadcpx']);

									$sin_dispensar = estado_sin_dispensar($regleta_str, $disCco, $wcan['kadpro'], $wcan['kadori'], $wcan['kadart']);
									if ($sin_dispensar >= 0) {
										$westado = "on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
										$wactual = "Actualizado";        //Indica que esta actualizado a la fecha
										
									}
								}
								
							}
					} //Aca termina el switch	         

					if ($wmuerte == "on")
						$westado = "Falleció";
				}

				// Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
				$q = " CREATE TEMPORARY TABLE IF NOT EXISTS tempo_ART "
					. " SELECT ubihac, ubihis, ubiing, " . $wbasedato . "_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac  "
					. "   FROM " . $wbasedato . "_000018, " . $wbasedato . "_000011, " . $wbasedato . "_000020, " . $wbasedato . "_000017 "
					. "  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
					//."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
					. "    AND ubisac  = ccocod "
					. "    AND ccohos  = 'on' "             //Que el CC sea hospitalario
					. "    AND ccourg != 'on' "             //Que no se de Urgencias
					. "    AND ccocir != 'on' "             //Que no se de Cirugia
					. "    AND ubihis  = habhis "
					. "    AND ubiing  = habing "
					. "    AND ubihis != '' "
					. "    AND ubihis  = eyrhis "            //Estas cuatro linea que siguen son temporales
					. "    AND ubiing  = eyring "
					. "    AND eyrsde  = ccocod "
					. "    AND eyrtip  = 'Recibo' "
					. "    AND eyrest  = 'on' "
					//."    AND ccocpx != 'on' "              //CICLOS de Producción 'OFF'
					. "  GROUP BY 1,2,3,4,5,6,7,8,9,10 "
					. "  UNION ALL "
					//Solo traigo los pacientes que esten en un servicio que sea con el MODELO DE CICLOS DE PRODUCCION Y DISPENSACION FRECUENTE
					. " SELECT ubihac, ubihis, ubiing, " . $wbasedato . "_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac  "
					. "   FROM " . $wbasedato . "_000018, " . $wbasedato . "_000011, " . $wbasedato . "_000020, " . $wbasedato . "_000017, " . $wbasedato . "_000054, "
					. "        " . $wbasedato . "_000043, " . $wbasedato . "_000099 "
					. "  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
					//."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
					. "    AND ubisac  = ccocod "
					. "    AND ccohos  = 'on' "             //Que el CC sea hospitalario
					. "    AND ccourg != 'on' "             //Que no se de Urgencias
					. "    AND ccocir != 'on' "             //Que no se de Cirugia
					. "    AND ubihis  = habhis "
					. "    AND ubiing  = habing "
					. "    AND ubihis != '' "
					. "    AND ubihis  = eyrhis "            //Estas cuatro linea que siguen son temporales
					. "    AND ubiing  = eyring "
					. "    AND eyrsde  = ccocod "
					. "    AND eyrtip  = 'Recibo' "
					. "    AND eyrest  = 'on' "
					//."    AND ccocpx  = 'on' "              //CICLOS de Producción 'ON'
					. "    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('$wfecha',' ','$whora_par_actual',':00:00')),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
					. "    AND ubihis  = kadhis "
					. "    AND ubiing  = kading "
					. "    AND kadare  = 'on' "
					. "    AND kadfec  = '" . $wfecha . "'"
					. "    AND kadpro  = tarcod "
					. "    AND tarpdx  = 'off' "            //Tipo de Articulo no se produce en ciclos osea, que son de dispensacion
					. "    AND kadart  NOT IN ( SELECT artcod FROM " . $wcenmez . "_000002 WHERE artcod = kadart) "
					. "    AND kadper  = percod "
					. "  GROUP BY 1,2,3,4,5,6,7,8,9,10 "

					. "  UNION "

					. " SELECT CONCAT(habzon,' - ',habcpa) as ubihac, ubihis, ubiing, " . $wbasedato . "_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac "
					. "   FROM " . $wbasedato . "_000018, " . $wbasedato . "_000020, " . $wbasedato . "_000054, " . $wbasedato . "_000026, " . $wbasedato . "_000011 "
					. "  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
					. "    AND ubialp != 'on' "             //Que no este en Alta en Proceso
					. "    AND ubihis  = habhis "
					. "    AND ubiing  = habing "
					. "    AND ubihis != '' "
					. "    AND ubihis  = kadhis "
					. "    AND ubiing  = kading "
					. "    AND kadfec  = '" . $wfecha . "'"      //Hoy
					. "    AND kadart  = artcod "
					. "	AND ubisac  = ccocod "
					. "	AND ccourg  != 'off'"
					. "  GROUP BY 1,2,3,4,5,6,7,8,9 ";
				$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				//    echo "<pre>".print_r($q,true)."</pre>";
				$q = " SELECT ubihac, ubihis, ubiing, id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr, ubisac "
					. "   FROM tempo_ART "
					. "  GROUP BY 1, 2, 5, 6, 7, 8, 9, 10 ";
				$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num_pacientes = mysql_num_rows($res);

				$j = 1;
				for ($i = 1; $i <= $num_pacientes; $i++) {
					$row = mysql_fetch_array($res);
					$whab = $row[0];      //habitación actual
					$whis = $row[1];     //Historia
					$wing = $row[2];    //Ingreso
					$wreg = $row[3];   //Id
					$whap = $row[5];  //Hora Alta en Proceso
					$wmue = $row[6]; //Indicador de Muerte
					$whan = $row[7]; //Habitación Anterior
					$walp = $row[8]; //Alta en proceso
					$wptr = $row[9]; //En proceso de traslado
					$wsac = $row[10];

					//Traigo los datos demograficos del paciente
					$q = " SELECT pacno1, pacno2, pacap1, pacap2, pacced, pactid "
						. "   FROM root_000036, root_000037 "
						. "  WHERE orihis = '" . $whis . "'"
						. "    AND oriing = '" . $wing . "'"
						. "    AND oriori = '" . $wemp_pmla . "'"  //Empresa Origen de la historia, 
						. "    AND oriced = pacced ";
					$respac = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$rowpac = mysql_fetch_array($respac);

					$wpac = $rowpac[0] . " " . $rowpac[1] . " " . $rowpac[2] . " " . $rowpac[3]; //Nombre
					$wdpa = $rowpac[4];  //Documento del Paciente
					$wtid = $rowpac[5]; //Tipo de Documento o Identificacion

					estado_del_Kardex_lactario($whis, $wing, $westado, $wmue, $wcolor, $wactual, $wsac, $esOrdenes);

					if ($wmue == "on") {
						$whab = $whan;
					}

					//Llevo los registros con estado=="on" a una matrix, para luego imprimirla por el orden (worden)
					if ($westado == "on") {

						$CcoLactario = consultarCcoLactarioUnificado( $conex, $wbasedato );
						// Consulta detalle del kardex, es la que nos trae los arituclos de cada pacientes
						$q = "
						SELECT	kadcpx, Artcom, CONCAT_WS( ' ', Percan, Peruni ) as Frecuencia, Kadfin, Kadhin, Viades, Kadobs, CONCAT_WS( ' ', Kadcfr, Kadufr ) as Dosis, Kadsus, K.Fecha_data, K.Hora_data, K.Kadfle, K.Kadhle, kadfpv, kadhpv
							FROM movhos_000054 K
							JOIN movhos_000040 ON Kadvia = Viacod
							JOIN movhos_000043 ON Kadper = Percod 
							JOIN movhos_000026 ON Kadart = Artcod
						WHERE	Kadhis = '" . $whis . "'
							AND	Kading = '" . $wing . "'
							AND	Kadfec = '" . $wfecha . "'
							AND	Kadvia = Viacod
							AND	Percod = Kadper
							AND	kadart = Artcod	
							AND Kadcco = '".$CcoLactario."'						
							";

						$result = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						$num_art = mysql_num_rows($result);
						// $kar = mysql_fetch_array($result);

						$articulos = array();

						$wmat_estado[$j][0] = $whab;
						$wmat_estado[$j][1] = $whis;
						$wmat_estado[$j][2] = $wing;
						$wmat_estado[$j][3] = $wpac;
						$wmat_estado[$j][4] = $westado;
						$wmat_estado[$j][5] = $wcolor;
						$wmat_estado[$j][6] = $walp;
						$wmat_estado[$j][7] = $wptr;
						$wmat_estado[$j][8] = $esOrdenes;
						$wmat_estado[$j][9] = $wdpa;
						$wmat_estado[$j][10] = $wtid;

						if ($num_art > 1) {
							while ($fila = mysql_fetch_array($result)) {
								$articulos[] = $fila;
							}							
							$wmat_estado[$j][11] = $articulos;
						} else {
							$fila = mysql_fetch_array($result);
							
							$wmat_estado[$j][11] = $fila[1];
							$wmat_estado[$j][12] = $fila[2];
							$wmat_estado[$j][13] = $fila[3];
							$wmat_estado[$j][14] = $fila[4];
							$wmat_estado[$j][15] = $fila[5];
							$wmat_estado[$j][16] = $fila[6];
							$wmat_estado[$j][17] = $fila[7];
							$wmat_estado[$j][18] = $fila[8];
							$wmat_estado[$j][19] = $fila[9];
							$wmat_estado[$j][20] = $fila[10];
							$wmat_estado[$j][21] = $fila[11];
							$wmat_estado[$j][22] = $fila[12];
							$wmat_estado[$j][23] = $fila[13];
							$wmat_estado[$j][24] = $fila[14];
						}
						$j++;
					}
				}
				
				echo "<hr style='height:3px;border-width:0;color:#cc0000;background-color:#cc0000'>";

				echo "<td class='fila1' width='90%'>
						<div class='titulopagina' align='center'>PACIENTES CON SOPORTE NUTRICIONAL</div>
					</td>";

				echo "&nbsp;<input class='imprimir-sticker' type=button value='Imprimir sticker' onclick='imprimirSticker()'>";

				//Encabezado de tabla
				echo "<table id='PacSoporteNuticional'>";
				// echo "<hr style='height:3px;border-width:0;color:#cc0000;background-color:#cc0000'>";
				echo "<tr class='encabezadoTabla'>";
				echo "<th>Habitacion</th>";
				echo "<th>Historia</th>";
				echo "<th>Paciente</th>";
				echo "<th>Estado</th>";
				echo "<th>Artículo</th>";
				echo "<th>Dosis</th>";
				echo "<th>Via de administración</th>";
				echo "<th>Frecuencia</th>";
				echo "<th>Fecha y hora de inicio</th>";
				echo "<th>Observación</th>";
				echo "<th>Sticker - Imprimir todos<input type='checkbox' id='checkAll' data-info='' /></th>";
				// echo "<th colspan='2' width='101'>Acción</th>"; //Se comenta porque no es necesario la accion en la parte informativa
				echo "</tr>";

				//Condicional while para pintar los datos que vienen desde la consulta
				// while ($kar = mysql_fetch_row($result)){  

				$color_filas = 0; //Inicializamos variable en 0 para el color de las filas
				$arrayActivos= array();			 // indica si hay un articulo activo
				
				foreach ($wmat_estado as $index => $fila) {
					$estado = '';
					$clase_estado = '';
					
					$arrayActivos[$fila[1]]= array(
													"activos" => 0,
													"nuevo" => 0,
													"modificado" => 0,
													"igual" => 0
												);
					
					if (count($fila[11]) > 1) {
						for ($k = 0; $k < count($fila[11]); $k++) {
							
							//Condicional para que se intercale el color de las filas en cada registro
							if (($k % 2) != 0) {
								$wclass = "fila1";
							} else {
								$wclass = "fila2";
							}


							$pos_art = $fila[11][$k];

							

							//Aqui asignamos el estado suspendido si el articulo esta en on, quiere decir que no esta activo.
							if ($pos_art[8] == 'on') {
								$estado = 'SUSPENDIDO';
								$clase_estado = 'suspendido';
										
							}else{
								$arrayActivos[$fila[1]]['activos'] = 1+$arrayActivos[$fila[1]]['activos'];
								$estado = "";
								$clase_estado = '';

								if(($pos_art[9] >= $pos_art[13] && $pos_art[10] > $pos_art[14]) || ($pos_art[13] == '0000-00-00')){
									
									$estado = "NUEVO";
									$clase_estado = 'nuevo';
									$arrayActivos[$fila[1]]['nuevo'] = 1+$arrayActivos[$fila[1]]['nuevo'];
								}
								if(($pos_art[13] >= $pos_art[9] && $pos_art[14] > $pos_art[10]) && ($pos_art[13] >= $pos_art[11] && $pos_art[14] > $pos_art[12])){
									//$estado = "IGUAL";
									//$clase_estado = 'igual';
									$arrayActivos[$fila[1]]['igual'] = 1+$arrayActivos[$fila[1]]['igual'];
								}
								if($pos_art[11] >= $pos_art[13] && $pos_art[12] > $pos_art[14]){
									$estado = "MODIFICADO";
									$clase_estado = 'modificado';
									$arrayActivos[$fila[1]]['modificado'] = 1+$arrayActivos[$fila[1]]['modificado'];
								}
							}
					
							
							//Pintamos los datos 
							$dataPac = array(
								"historia" => $fila[1],
								"ingreso" => $fila[2],
								"nombre" => $fila[3],
								"fecha" => $pos_art[3],
								"hora" => $pos_art[4],
								"habitacion" => $fila[0],
								// "frecuencia" => $pos_art[0],
								"frecuencia" => $pos_art[2],
								"articulo" => $pos_art[1],
								"dosis" => $pos_art[7],
								"via" => $pos_art[5]
							);
							echo "<tr align='center' class=" . $wclass . ">";
							echo "<td align='center' >" . $fila[0] . "</td>"; //Habitación pac
							echo "<td align='center' >" . $fila[1] . "-" . $fila[2] . "</td>"; //Historia pac
							echo "<td align='center' >" . $fila[3] . "</td>"; //Name pac
							echo "<td align='center' class=" . $clase_estado . " >" . $estado . "</td>"; //Estado
							echo "<td align='center' >" . $pos_art[1] . "</td>"; //Articulo, Producto
							echo "<td align='center' >" . $pos_art[7] . "</td>"; //Dosis
							echo "<td align='center' >" . $pos_art[5] . "</td>"; //Via de administración
							echo "<td align='center' >" . $pos_art[2] . "</td>"; //Frecuencia
							echo "<td align='center' >" . $pos_art[3] . " <br>a las</br> " . $pos_art[4] . "</td>";	//Fecha y hora de inicio 
							echo "<td align='center' >" . $pos_art[6] . "</td>"; //Obervación
							echo "<td align='center' ><input type='checkbox' data-info='" . json_encode($dataPac) . "'/>&nbsp; Imprimir Sticker</td>"; //Imprimir
							echo "</tr>";
						}
					} else {

						// Condicional de la manera corta para asignar color a cada fila
						$color_filas % 2 != 0 ? $wclass = 'fila1' : $wclass = 'fila2';

						
						if ($fila[18] == 'on') {
							$estado = 'SUSPENDIDO';
							$clase_estado = 'suspendido';
							
						}else{
							$arrayActivos[$fila[1]]['activos'] = 1+$arrayActivos[$fila[1]]['activos'];
								$estado = "";
								$clase_estado = '';
								if(($fila[19] >= $fila[23] && $fila[20] > $fila[24]) || ($fila[23] == '0000-00-00')){
									
									$estado = "NUEVO";
									$clase_estado = 'nuevo';
									$arrayActivos[$fila[1]]['nuevo'] = 1+$arrayActivos[$fila[1]]['nuevo'];
								}
								if(($fila[23] >= $fila[19] && $fila[24] > $fila[20]) && ($fila[23] >= $fila[21] && $fila[24] > $fila[22])){
									//$estado = "IGUAL";
									//$clase_estado = 'igual';
									$arrayActivos[$fila[1]]['igual'] = 1+$arrayActivos[$fila[1]]['igual'];
								}
								if($fila[21] >= $fila[23] && $fila[22] > $fila[24]){
									$estado = "MODIFICADO";
									$clase_estado = 'modificado';
									$arrayActivos[$fila[1]]['modificado'] = 1+$arrayActivos[$fila[1]]['modificado'];
								}
						}

						//Pintamos los datos 
						$dataPac = array(
							"historia" => $fila[1],
							"ingreso" => $fila[2],
							"nombre" => $fila[3],
							"fecha" => $fila[13],
							"hora" => $fila[14],
							"habitacion" => $fila[0],
							"frecuencia" => $fila[12],
							"articulo" => $fila[11],
							"dosis" => $fila[17],
							"via" => $fila[15]
						);
						echo "<tr align='center' class=" . $wclass . ">";
						echo "<td align='center' >" . $fila[0] . "</td>"; //Habitación pac
						echo "<td align='center' >" . $fila[1] . "-" . $fila[2] . "</td>"; //Historia pac
						echo "<td align='center' >" . $fila[3] . "</td>"; //Name pac
						echo "<td align='center' class=" . $clase_estado . " >" . $estado . "</td>"; //Estado
						echo "<td align='center' >" . $fila[11] . "</td>"; //Articulo, Producto
						echo "<td align='center' >" . $fila[17] . "</td>"; //Dosis
						echo "<td align='center' >" . $fila[15] . "</td>"; //Via de administración
						echo "<td align='center' >" . $fila[12] . "</td>"; //Frecuencia
						echo "<td align='center' >" . $fila[13] . " <br>a las</br> " . $fila[14] . "</td>";	//Fecha y hora de inicio 
						echo "<td align='center' >" . $fila[16] . "</td>"; //Obervación
						echo "<td align='center' ><input type='checkbox' data-info='" . json_encode($dataPac) . "'/>&nbsp; Imprimir Sticker</td>"; //Imprimir
						echo "</tr>";

						$color_filas++; //Vamos incrementando
					}
				}
				 // print_r($arrayActivos);
				echo '<script type="text/javascript">';
				
				foreach ($arrayActivos as $key => $value) {
					$estado = "estado_".$key;
					//echo 'alert("'.$estado.'");';
					if($value['nuevo'] != 0 && ($value['modificado'] == 0 || $value['modificado'] != 0) && ($value['igual'] == 0 || ($value['modificado'] == 0 || $value['igual'] != 0))){
						echo '$("#'.$estado.'").html("NUEVO");'	;
						echo '$("#'.$estado.'").addClass("nuevo");';
					}elseif($value['igual'] != 0 && $value['modificado'] == 0){
						//echo '$("#'.$estado.'").html("IGUAL");';
						//echo '$("#'.$estado.'").addClass("igual");';	
					}
					elseif($value['modificado'] != 0 && value['nuevo'] == 0 ){

						echo '$("#'.$estado.'").html("MODIFICADO");';
						echo '$("#'.$estado.'").addClass("modificado");';
					}
					if($value['activos'] == 0){
						echo 'var node = document.getElementById("'.$key.'");';
						echo 'node.parentNode.removeChild(node);';
					}
				}
				echo '</script>';
			}
		}
	}

include_once("free.php");
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
} // if de register
?>
    
</script>
