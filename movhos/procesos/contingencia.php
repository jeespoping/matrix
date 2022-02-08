<script>
/******************************************************************
 * AJAX
 ******************************************************************/

/******************************************************************
 * Realiza una llamada ajax a una pagina
 * 
 * met:		Medtodo Post o Get
 * pag:		Página a la que se realizará la llamada
 * param:	Parametros de la consulta
 * as:		Asincronro? true para asincrono, false para sincrono
 * fn:		Función de retorno del Ajax, no requerido si el ajax es sincrono
 *
 * Notaa: 
 * - Si la llamada es GET las opciones deben ir con la pagina.
 * - Si el ajax es sincrono la funcion retorna la respuesta ajax (responseText)
 * - La funcion fn recibe un parametro, el cual es el objeto ajax
 ******************************************************************/
function consultasAjax( met, pag, param, as, fn ){
	
	this.metodo = met;
	this.parametros = param; 
	this.pagina = pag;
	this.asc = as;
	this.fnchange = fn; 

	try{
		this.ajax=nuevoAjax();

		this.ajax.open( this.metodo, this.pagina, this.asc );
		this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		this.ajax.send(this.parametros);

		if( this.asc ){
			var xajax = this.ajax;
//			this.ajax.onreadystatechange = this.fnchange;
			this.ajax.onreadystatechange = function(){ fn( xajax ) };
			
			if ( !estaEnProceso(this.ajax) ) {
				this.ajax.send(null);
			}
		}
		else{
			return this.ajax.responseText;
		}
	}catch(e){	}
}

window.onload = function(){
	return;
	document.forms[0].submit();
}
/************************************************************************/
</script>
<?php
include_once("conex.php");

/************************************************************************************************************
 * INICIO FUNCION APLICACION IPODS
 ************************************************************************************************************/
/************************************************************************************
 * Segun la condicion de suministro del articulo, dice si dicha condición 
 * es considerada a necesidad o no
 *
 * Septiembre 2011-09-11
 ************************************************************************************/
function esANecesidad( $conex, $wbasedato, $condicion ){

	$val = false;

	if( !empty( $condicion ) ){

		$sql = "SELECT 
					Contip
				FROM 
					{$wbasedato}_000042
				WHERE 
					concod = '$condicion'
				";
					
		$resAN = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrowsAN = mysql_num_rows( $resAN );
				
		if( $numrowsAN ){
		
			$rowsAN = mysql_fetch_array( $resAN );
		
			if( $rowsAN[ 'Contip' ] == 'AN' ){
				$val = true;				
			}
		}
	}
	
	return $val;
}
 
function actualizarCampoContingenciaKardex( $conex, $wbasedato, $id, $campo, $valor ){

	$val = false;
	
	$sql = "UPDATE
				log_contingencia_kardex
			SET
				$campo = '$valor'
			WHERE
				Conide = '$id'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}
 
function crearTablaContingenciaKardex( $conex, $wbasedato ){

	// $sql = "CREATE TABLE IF NOT EXISTS log_contingencia_Kardex
			// (INDEX idx_ide( Conide ) )
			// SELECT
				// id as Conide, 'off' as Concre, 'off' as Conexi, 'off' as Conapr, 'off' as Concar, 'off' as Conapl 
			// FROM
				// {$wbasedato}_000123
			// ";
			
	$sql = "INSERT INTO log_contingencia_kardex
			SELECT
				id, 'off', 'off', 'off', 'off', 'off'
			FROM
				{$wbasedato}_000123
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
}

//FUNCION PARA SACAR HORA PAR 
function hora_par($hora)
    {
	$hora;
	$whora_Actual_arr = explode(":", $hora);
	$whora_Actual=$whora_Actual_arr[0]; 
	$whora_Act=($whora_Actual/2);

	 
	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="00";
	    } 
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="00";
			else
		       $whora_par_actual=$whora_Actual; 
	     }
	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual; 

	return $whora_par_actual;
	}

//FUNCION PARA SELCCIONAR LOS MEDICAMENTOS QUE SE LES REALIZARA APLICARA    
function aplicar_medicamentos($conex, $fech_ini_contig, $fech_fin_contig, $hora_ini_contig, $hora_fin_contig, $marca, $bd, $wemp_pmla, $usuario)
{
     //$fecha_actual = date("Y-m-d"); 
     $fecha_actual = $fech_ini_contig; 
    
    // Sacar todos medicamentos que esten a partir de la fecha y hora de contingencia y que esten con la marca C y PC
    $q1 = " (SELECT Fdenum, Fdelin, Fdeart, Hora_data "
        ."FROM ".$bd."_000003 "
        ."WHERE Fecha_data >='".$fech_ini_contig."' "  
        ."AND Fdedis = '".$marca."' "
        ."AND Fdelot = '') "
        ."UNION "
        ."(SELECT Fdenum, 0 as Fdelin, Fdeari as Fdeart, Hora_data "
        ."FROM ".$bd."_000003 "
        ."WHERE Fecha_data >='".$fech_ini_contig."' "  
        ."AND Fdedis = '".$marca."' "
        ."AND Fdelot != '' "
        ."GROUP BY Fdenum) "
        .""; 
        
    $res = mysql_query($q1, $conex) or die ("Error 1: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
    $num = mysql_num_rows($res);
  
    while( $lista_med= mysql_fetch_array($res) )
    {
        //Consultar Historia, ingreso y centro de costos del paciente
        $q2 = "SELECT Fenhis, Fening, Ubisac, Ubihac  "
            ."FROM ".$bd."_000002, ".$bd."_000018 "
            ."WHERE Fennum ='".$lista_med['Fdenum']."' "
            ."AND Fenhis= Ubihis "
            ."AND Fening= Ubiing "
            .""; 
        $res2 = mysql_query($q2, $conex) or die ("Error 2: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
        $result2= mysql_fetch_array($res2);
        
        $historia=$result2[0];                        //Historia
        $ingreso=$result2[1];                        //Ingreso
        $habitacion=$result2[3];                    //Habitacion
        /*echo "<br><br>CENTRO DE COSTO: ".*/$wco=$result2[2];                            //Centro Costo
        /*echo "<br>ARTICULO: ".*/$articulo=$lista_med['Fdeart'];                //Codigo Articulo
        /*echo "<br>HORA DATA: ".*/$lista_med['Hora_data'];
        
        //consultar frecuencia, fecha de inicio de administracion, hora de inicio de administracion en la 54 
        $q3 = "SELECT Kadido, Kadcnd "
            ."FROM ".$bd."_000054 "
            ."WHERE Kadfec= '".$fecha_actual."' "
            ."AND Kadhis='".$historia."' "
            ."AND Kading='".$ingreso."' "
            ."AND Kadart= '".$articulo."' "
            ."";        
        $res3 = mysql_query($q3, $conex) or die ("Error 3: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
        $result3=mysql_fetch_array($res3);                
        $wido=$result3[0];                                    //Kadido
        $ronda_ini_conti= strtotime( $fech_ini_contig." ".$hora_ini_contig );         // Hora de inicio de la contigencia
        $ronda_fin_conti= strtotime( $fech_fin_contig." ".$hora_fin_contig );         // Hora fin de la contigencia
        $contigen="ok";
        $wpac = "";
        $wfecha_actual=date("Y-m-d");
        
		echo "<br>IDO: $wido";
        //////////// Manejo de las rondas si la contigencia se demora mas de 1 dia
		
		$aplicar = true;
		
		if( !empty( $result3[1] ) ){
			if( esANecesidad( $conex, $bd, $result3[1] ) ){
				$aplicar = false;
			}
		}
		
		if( $aplicar ){
		
			for($y=$ronda_ini_conti; $y<=$ronda_fin_conti; $y=$y+2*3600)
			{
				ECHO "<BR><a href='".$url="Aplicacion_ipods.php?contigen=".$contigen."&wemp_pmla=".$wemp_pmla."&wcco=".$wco."&whis=".$historia."&wing=".$ingreso."&wfecha_actual=".date( "Y-m-d", $y )."&whora_par_actual=".date( "H", $y )."&whab=".$habitacion."&wpac=".$wpac."&wido[1]=".$wido."&wapl[1]=on&usuario=".$usuario."&ido=".$wido."' >Aqui</a>";
					?>
						<script>
						//consultasAjax( 'GET', '<?php echo $url;?>', '', false, '' );
						</script>
					<?php
			}
		}
    }
}    
//FIN FUNCION APLICAR MEDICAMENTOS


/************************************************************************************************************
 * FIN FUNCION APLICAR MEDICAMENTOS	
 ************************************************************************************************************/


/************************************************************************************************
 * FUNCIONES PARA CREAR EL KARDEX
 ************************************************************************************************/

function crearKardexAutomaticamente( $conex, $wbd, $pac, $fecha ){

	global $wbasedato;
	global $usuario;
	
	$wbasedato = $wbd;

	//Obtengo la fehca del día anterior
	$ayer = date( "Y-m-d", strtotime( $fecha." 00:00:00" ) - 24*3600 );

	//Consulto si se ha generado kardex el día de hoy, es decir si tiene encabezado
	$sql = "SELECT * 
			FROM
				{$wbd}_000053
			WHERE
				karhis = '{$pac['his']}'
				AND karing = '{$pac['ing']}'
				AND fecha_data = '$fecha'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	//Si no existe kardex
	if( $numrows == 0 ){
	
		//verifico que no halla articulos en la temporal
		$sql = "SELECT * 
				FROM
					{$wbd}_000060
				WHERE
					kadhis = '{$pac['his']}'
					AND kading = '{$pac['ing']}'
					AND kadfec = '$fecha'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows == 0 ){	//No se ha generado kardex ni esta abierto
		
			$sql = "SELECT * 
					FROM
						{$wbd}_000053
					WHERE
						karhis = '{$pac['his']}'
						AND karing = '{$pac['ing']}'
						AND fecha_data = '$ayer'
						AND kargra != 'on'
					";
				
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numrows = mysql_num_rows( $res );
		
			if( $numrows == 0 ){
			
				//Verifico que no se halla pasado la hora de corte kardex
				$corteKardex = true; //consultarHoraCorteKardex( $conex );
			
				if( $corteKardex ){
				
					//if( true || time() < strtotime( "$fecha $corteKardex" ) ){
					if( true ){
					
						//Si la hora actual es menor a la hora corte del kardex
						
						//Creo kardex nuevo para el día actual
						$auxUsuario = $usuario;
						
						$usuario = consultarUsuarioKardex($auxUsuario->codigo);
						
						$usuario->esUsuarioLactario = false;
						$usuario->gruposMedicamentos = false;
						$usuario->centroCostosGrabacion = "*";
						
						$paciente = consultarInfoPacienteKardex( $pac['his'], '' );
						$kardexAc = consultarKardexPorFechaPaciente( $fecha, $paciente );
						cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "N", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
						cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "Q", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
						cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "A", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
						//cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "U", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
						
						//global $wbasedato;
						//global $conex;
						//global $usuario;		//Información de usuario

						//global $centroCostosServicioFarmaceutico; //esta
						//global $codigoServicioFarmaceutico; //esta
						//global $centroCostosCentralMezclas; //esta
						cargarArticulosADefinitivo( $pac['his'], $pac['ing'], $fecha, false );
						
						/************************************************************************************************
						 * Agosto 27 de 2011
						 ************************************************************************************************/
						cargarExamenesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
						cargarInfusionesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
						cargarMedicosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
						cargarDietasAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
						
						
						cargarExamenesADefinitivo( $pac['his'], $pac['ing'], $fecha );
						cargarInfusionesADefinitivo( $pac['his'], $pac['ing'], $fecha );
						cargarMedicoADefinitivo( $pac['his'], $pac['ing'], $fecha );
						cargarDietasADefinitivo( $pac['his'], $pac['ing'], $fecha );
						/************************************************************************************************/
						
						//Creo encabezado del kardex tal cual esta el día anterior
						$sql = "INSERT INTO
									{$wbd}_000053(Medico,Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karfir,Karsuc,Karaut,Seguridad)
								SELECT
												  Medico,'".$fecha."','".date( "H:i:s" )."',Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,karare,Karcco,Karusu,Karfir,Karsuc,'on',Seguridad
									FROM
										{$wbd}_000053
									WHERE
										Karhis = '{$pac['his']}'
										AND karing = '{$pac['ing']}'
										AND fecha_data = '$ayer'
										AND karcco = '*'
								";
						
						$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
						
						$usuario = $auxUsuario;
						
						if( mysql_affected_rows() > 0 ){
							//Dejo todos los registros del kardex como estaban antes
							$sql = "SELECT
										*
									FROM
										{$wbd}_000054
									WHERE
										kadhis = '{$pac['his']}'
										AND kading = '{$pac['ing']}'
										AND kadfec = '$fecha'										
									";
							
							$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
							$numrows = mysql_num_rows( $res );
							
							if( $numrows > 0 ){
							
								for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
								
									$sqlAnt = "SELECT
													*
												FROM
													{$wbd}_000054
												WHERE
													id = '{$rows['Kadreg']}'
												";
							
									$resAnt = mysql_query( $sqlAnt, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
									
									if( $rowsAnt = mysql_fetch_array($resAnt) ){
								
										$sqlAct = "UPDATE
														{$wbd}_000054
													SET
														kadare = '{$rowsAnt['Kadare']}',
														kadcon = '{$rowsAnt['Kadcon']}'
													WHERE
														id = '{$rows['id']}'
													";
								
										$resAct = mysql_query( $sqlAct, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );							
									}
								}
							}
							
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
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}


/******************************************************************************************
 * Aprueba el perfil y coloca en el campo kadusu el usuario que lo realiza
 ******************************************************************************************/
function aprobarPerfil( $conex, $wbasedato, $idRegistro ){

	$val = false;
	
	$sql = "UPDATE 
				{$wbasedato}_000054 
			SET
				Kadusu = '$wbasedato',
				Kadare = 'on'
			WHERE
				id = '$idRegistro'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_affected_rows( );
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}
 

/********************************************************************************
 * Consulta la frecuencia en horas
 ********************************************************************************/
function consultarFrecuenciaHoras( $conex, $wbasedato, $codFrecuencia ){

	$val = false;

	$sql = "SELECT 
				*
			FROM 
				{$wbasedato}_000043
			WHERE 
				Percod = '{$codFrecuencia}'
			;";
				 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Perequ' ];
	}
	
	return $val;
}

/************************************************************************************
 * Busco el protocolo del medicamento para el kardex, solo hay tres posibilidades
 * N, Q ó A. El protocolo N es por defecto.  Recordar que los protocolos solo indican
 * en que pestaña del kardex aparece.
 *
 * N: Pestaña de medicamentos
 * Q: Pestaña de mezclas
 * A: Pestaña de analgesias
 ************************************************************************************/
function consultarProtocolo( $conex, $wbasedato, $codArticulo, $cco ){

	$val = "N";

	//Tipo del protocolo
	$q2 = "SELECT
				Arktip
			FROM
				".$wbasedato."_000068
			WHERE
				 Arkcod = '$codArticulo'
				 AND Arkest = 'on'
				 AND Arkcco = '$cco'
				 AND Arktip IN ( 'Q', 'A' )
		   ";

	$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);
	
	if( $rows = mysql_fetch_array( $res2 ) ){
		$val = $rows[ 'Arktip' ];
	}
	
	return $val;
}

/******************************************************************************************************************************
 * Verifica que un articulo exista para un paciente en una fecha dada
 ******************************************************************************************************************************/
function existeArticuloMatrix( $conex, $wbasedato, $historia, $ingreso, $codArticulo, $fechaKardex, $tipoProtocolo, $fInicio, $hInicio ){
	
	$val = false;
	
	$sql = "SELECT
				Kadart, Kadcfr, Kadufr, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadvia, Kadfec, Kadcon, Kadobs, Kadsus, Kadori, Kadcnd, Kaddma, Kadcan, Kaduma, Kadcma, Kadsad, Kaddis, Kadcdi, Kadreg, Kadcpx, Kadron, Kadfro, Kadaan, Kadcda, Kadcdt,Kaddan,Kadfum,Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf, id
			FROM
				{$wbasedato}_000054
			WHERE
				Kadart = '$codArticulo'
				AND Kadfec = '$fechaKardex'
				AND Kadhis = '$historia'
				AND Kading = '$ingreso'
		";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		if( $rows = mysql_fetch_array( $res ) ){
			$val = $rows[ 'id' ];
		}
	}
	
	return $val;
}


/**************************************************************************************************
 * valida si la historia esta activa en matrix
 **************************************************************************************************/
function validarHistoria( $conex, $wbasedato, $historia, &$ingreso, &$datosPacientes, &$activo ){

	global $wemp_pmla;
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				root_000036 b, root_000037 c, {$wbasedato}_000018 a
			WHERE
				Ubihis = Orihis
				AND Ubiald != 'on'
				AND Pacced = Oriced
				AND Pactid = Oritid
				AND Oriori = '$wemp_pmla'
				AND Orihis = '$historia'
				AND Oriing = Ubiing
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$activo = true;
		$val = true;
		$ingreso = $rows[ 'Oriing' ];
		
		$datosPacientes = $rows;
	}
	
	return $val;
}

/************************************************************************************************************
 * Si el paciente no tiene kardex, crea el kardex y los articulos correspondientes para el kardex, segun los datos en
 * datosArticulos
 *
 * datosArticulos:	$datosArticulos
 * []				filas de articulos
 * [][]				datos individuales articulos
 ************************************************************************************************************/
function generarKardex( $conex, $wbasedato, $wcenmez, $historia, $datosArticulos ){

	global $usuario;
	
	if( empty( $usuario->codigo ) ){ 
		$usuario->codigo;
		$mensajes .= "<br><br>Usuario no valido para crear kardex";
		return $mensajes;
	}

	//Variable creada para depuracion
	$mensajes = "<br><br>Historia: $historia";
	
	$fechaKardex = date( "Y-m-d" );
	
	//verifico si el paciente esta activo
	validarHistoria( $conex, $wbasedato, $historia, &$ingreso, &$datosPacientes, &$activo );
	
	$mensajes .= "<br>Ingreso: $ingreso";
	
	//Si el paciente esta activo, procedo a crear los registros respectivos del kardex
	if( $activo ){
		
		//Si no existe encabezado del kardex lo creo
		$existeEncabezadoKardex = existeEncabezadoKardex( $historia, $ingreso, $fechaKardex );
		
		if( !$existeEncabezadoKardex ){
		
			//Trato de crear el kardex automaticamente
			$kardexCreadoAutomaticamente = crearKardexAutomaticamente( $conex,  $wbasedato, Array( 'his' => $historia, 'ing' => $ingreso ), $fechaKardex );
			
			if( !$kardexCreadoAutomaticamente ){	//Si el kardex no se crea automaticamente
				
				//No tiene encabezado del kardex y por tanto lo creo.
				$encabezadoCreado = crearEncabezadoKardexAnterior( $historia, $ingreso, $fechaKardex );
				
				
				$kardexGrabar = new kardexDTO();

				//Captura de parametros. Encabezado del kardex
				$kardexGrabar->historia = $historia;
				$kardexGrabar->ingreso = $ingreso;
				$kardexGrabar->fechaCreacion = $fechaKardex;
				$kardexGrabar->horaCreacion = date("H:i:s");
				$kardexGrabar->fechaGrabacion = $fechaKardex;
				$kardexGrabar->usuario = $usuario->codigo;
				$kardexGrabar->confirmado = 'on';
				$kardexGrabar->centroCostos = '*';
				$kardexGrabar->usuarioQueModifica = $usuario->codigo;
				
				crearKardex($kardexGrabar);
				
				$encabezadoCreado = existeEncabezadoKardex( $historia, $ingreso, $fechaKardex );
				
				if( $encabezadoCreado ){
					$existeEncabezadoKardex = true;
					$mensajes .= "<br>Encabezado del kardex creado";
				}
				else{
					$existeEncabezadoKardex = false;
					$mensajes .= "<br>No se pudo crear el encabezado del kardex";
				}
			}
			else{
				$existeEncabezadoKardex = true;
				$mensajes .= "<br>Kardex creado automaticamente";
			}
		}
		else{
			$mensajes .= "<br>Existe encabezado del kardex";
		}
				
		if( $existeEncabezadoKardex ){
			
			$mensajes .= "<br>Procesando los datos....";
			
			//Paso los articulo de la temporal a la definitiva por si los hay
			cargarArticulosADefinitivo( $historia, $ingreso, $fechaKardex, true );	//El ultimo parametro no afecta el kardex
			
			for( $i = 0; $i < count( $datosArticulos ); $i++ ){
			
				//Coloco en Nulo las variables que puedan ser cambiadas en el programa para el articulo
				$formaFarmaceutica = $cantidadFraccion = $unidadFraccion = $origenArticulo = $ccoArticulo = $unidadManejo = '';
			
				$tipoProtocolo = consultarProtocolo( $conex, $wbasedato, $datosArticulos[ $i ][ 'Conart' ], $ccoArticulo );
				
				$frecuenciaHoras = consultarFrecuenciaHoras( $conex, $wbasedato, $datosArticulos[ $i ][ 'Confre' ] );
			
				//Busco si un articulo existe en el kardex
				$existeArticulo = existeArticuloMatrix( $conex, $wbasedato, $historia, $ingreso, $datosArticulos[ $i ][ 'Conart' ], $fechaKardex, $tipoProtocolo, $datosArticulos[ $i ][ 'Confin' ], $datosArticulos[ $i ][ 'Conhin' ] );
				
				//Si el articulo no existe lo creo
				if( !$existeArticulo ){
					
					//Consulto el medicamento en el kardex
					$resArticulo = consultarMedicamentosPorCodigoContingencia($conex, $wbasedato,$datosArticulos[ $i ][ 'Conart' ],'','%','1050','*',$tipoProtocolo, '');
				
					if( !empty( $resArticulo ) ){
						
						if( $datosArticulos[ $i ][ 'Confin' ] != '00:00:00' ){
					
							$resArticulo = explode( "','", $resArticulo );
								
							if( $resArticulo[ 7 ] == $datosArticulos[ $i ][ 'Conufr' ] ){
							
								if( trim( $datosArticulos[ $i ][ 'Confre' ] ) != '' ){
									
									//Creo el articulo del kardex
									//Esta funcion lo crea en la temporal
									grabarArticuloDetalle($wbasedato,
														  $historia,
														  $ingreso,
														  $fechaKardex,
														  $datosArticulos[ $i ][ 'Conart' ], //$codArticulo,
														  $datosArticulos[ $i ][ 'Concfr' ], //$cantDosis,
														  $resArticulo[ 7 ], //$unidadFraccion, //$unDosis,
														  $datosArticulos[ $i ][ 'Confre' ], //$per,
														  $resArticulo[ 4 ], //$formaFarmaceutica, //$fmaFtica,
														  $datosArticulos[ $i ][ 'Confin' ], //$fini,
														  $datosArticulos[ $i ][ 'Conhin' ], //$hini,
														  $datosArticulos[ $i ][ 'Convia' ], //$via,
														  'on', //$conf, Lo dejo activo ya que no afecta si es de SF y se requiere para CM
														  '', //$dtto,
														  $datosArticulos[ $i ][ 'Conobs' ], //$obs,
														  $resArticulo[ 2 ], //$origenArticulo,
														  $wbasedato,//$codUsuario,
														  $datosArticulos[ $i ][ 'Concnd' ], //$condicion,
														  '', //$dosisMax,
														  0, //$cantGrabar,
														  $resArticulo[ 5 ], //$unidadManejo,
														  $resArticulo[ 8 ], //$cantidadFraccion, //$cantidadManejo,
														  'N', //$primerKardex,
														  $frecuenciaHoras, //$horasFrecuencia,
														  '', //$fInicioAnt,
														  '', //$hInicioAnt,
														  $resArticulo[ 8 ] == 'on' ? 'true' : 'false', //'false', //$noDispensar,
														  $tipoProtocolo,
														  '*', //$centroCostosGrabacion,
														  'off'//$prioridad
														  );
									
									//Paso los articulo de la temporal a la definitiva
									cargarArticulosADefinitivo( $historia, $ingreso, $fechaKardex, true );	//El ultimo campo no afecta el kardex
									
									//Busco si el articulo fue creado
									$existeArticulo = existeArticuloMatrix( $conex, $wbasedato, $historia, $ingreso, $datosArticulos[ $i ][ 'Conart' ], $fechaKardex, $tipoProtocolo, $datosArticulos[ $i ][ 'Confin' ], $datosArticulos[ $i ][ 'Conhin' ] );
									
									if( $existeArticulo ){
										actualizarCampoContingenciaKardex( $conex, $wbasedato, $datosArticulos[ $i ][ 'id' ], 'Concre', 'on' );
										$mensajes .= "<br>Articulo :".$datosArticulos[ $i ][ 'Conart' ]." - ".$datosArticulos[ $i ][ 'Confin' ]." ".$datosArticulos[ $i ][ 'Conhin' ]." - ".$datosArticulos[ $i ][ 'Confre' ]." - ".$datosArticulos[ $i ][ 'Concfr' ]." Creado";
									}
									else{
										$mensajes .= "<br>Articulo :".$datosArticulos[ $i ][ 'Conart' ]." - ".$datosArticulos[ $i ][ 'Confin' ]." ".$datosArticulos[ $i ][ 'Conhin' ]." - ".$datosArticulos[ $i ][ 'Confre' ]." - ".$datosArticulos[ $i ][ 'Concfr' ]." No se creo Creado";
									}
								}
								else{
									$mensajes .= "<br>Articulo :".$datosArticulos[ $i ][ 'Conart' ]." - ".$datosArticulos[ $i ][ 'Confin' ]." ".$datosArticulos[ $i ][ 'Conhin' ]." - ".$datosArticulos[ $i ][ 'Confre' ]." - ".$datosArticulos[ $i ][ 'Concfr' ]." No se crea, no hay frecuencia";
								}
							}
							else{
								$mensajes .= "<br>Articulo :".$datosArticulos[ $i ][ 'Conart' ]." - ".$datosArticulos[ $i ][ 'Confin' ]." ".$datosArticulos[ $i ][ 'Conhin' ]." - ".$datosArticulos[ $i ][ 'Confre' ]." - ".$datosArticulos[ $i ][ 'Concfr' ]." No se crea, unidad de fraccion distinta para el kardex";
							}
						}
						else{
							$mensajes .= "<br>Articulo :".$datosArticulos[ $i ][ 'Conart' ]." - ".$datosArticulos[ $i ][ 'Confin' ]." ".$datosArticulos[ $i ][ 'Conhin' ]." - ".$datosArticulos[ $i ][ 'Confre' ]." - ".$datosArticulos[ $i ][ 'Concfr' ]." No se crea, fecha de inicio incorrecta";
						}
					}
					else{
						$mensajes .= "<br>Articulo :".$datosArticulos[ $i ][ 'Conart' ].": No se encontro el medicamento para el kardex";
					}
				}
				else{
					$mensajes .= "<br>Articulo :".$datosArticulos[ $i ][ 'Conart' ]." - ".$datosArticulos[ $i ][ 'Confin' ]." ".$datosArticulos[ $i ][ 'Conhin' ]." - ".$datosArticulos[ $i ][ 'Confre' ]." - ".$datosArticulos[ $i ][ 'Concfr' ]." Ya existe";
				}
				
				
				if( $existeArticulo ){
				
					actualizarCampoContingenciaKardex( $conex, $wbasedato, $datosArticulos[ $i ][ 'id' ], 'Conexi', 'on' );
					
					//Si el medicamento existe apruebo el perfil y en el campo kadusu coloco el usuario que lo afecta ( usuario = movhos )
					$aprobado = aprobarPerfil( $conex, $wbasedato, $existeArticulo );
					
					if( $aprobado ){
						$mensajes .= "<br>id del Registro Aprobado: $existeArticulo";
					}
					else{
						$mensajes .= "<br>No se pudo aprobar el medicamento";
					}
				}
			}
		}
	}
	else{
		$mensajes .= "<br>Paciente de alta";
	}
	
	return $mensajes;
}

/****************************************************************************************
 * FUNCION PARA RECIBIR EL CARRO
 ****************************************************************************************/
 
/******************************************************************************************
 * Recibe el carro a partir de una fecha dada de acuerdo a la marca
 ******************************************************************************************/
function recibirCarro( $conex, $wbasedato, $fecha, $marca ){

	$val = false;
	
	$sql = "UPDATE {$wbasedato}_000003
			SET
				fdedis = 'off'
			WHERE
				fecha_data >= '$fecha'
				AND fdedis = '$marca'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows( ) > 0 ){
		
		$val = true;
	}
	
	return $val;
}


/******************************************************************************************
 * Lee todos los datos necesarios para el kardex
 ******************************************************************************************/
function leerDatosKardex(){
	
	global $wemp_pmla;
	global $user;
	global $wuser;
	global $usuario;
	global $wbasedato;
	global $wcenmez;
	global $conex;
	
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	@$usuario = consultarUsuarioKardex($wuser);
	

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	
	//Inserto valores a la tabla de contingencia kardex
	crearTablaContingenciaKardex( $conex, $wbasedato );

	$mensajes = "<br>Comenzando:";

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000123
			ORDER BY 
				Conhis, Conart
			";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	$terminar = true;
	$i = 0;
	
	if( $num > 0 ){
		
		$mensajes .= "<br>leyendo datos.....";
	
		while( $terminar ){
		
			if( !empty( $historia ) && $historia != $rows[ 'Conhis' ] ){
				//generando los articulos en el kardex de ser necesario
				//echo "<br>....total articulos a grabar: ".count( $articulos );
				$mensajes .= "<br>Generando Kardex";
				$mensajes .= generarKardex( $conex, $wbasedato, $wcenmez, $historia, $articulos );
				
				$articulos = Array();
				
				$historia = $rows[ 'Conhis' ];
				$articulos[] = $rows;
				
				if( !$rows ){
					$terminar = false;
				}
			}
				
			if( $rows = mysql_fetch_array( $res ) ){
			
				if( empty( $historia ) || $historia == $rows[ 'Conhis' ] ){
					$historia = $rows[ 'Conhis' ];
					$articulos[] = $rows;

					$mensajes .= "<br>registro $i: $historia";
				}
				else{
					
				}
				$i++;
			}
		}
	}
	else{
		$mensajes .= "<br>Sin datos para leer";
	}
	
	echo $mensajes;
}

/************************************************************************************************
 * FIN DE FUNCIONES PARA CREAR EL KARDEX
 ************************************************************************************************/
 
 
 
 
 
 
/************************************************************************************************
 * FUNCIONES PARA CARGAR ARTICULOS
 ************************************************************************************************/
 
function consultarUnidadArticulo( $conex, $wbasedato, $codigo ){

	$val = '';

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000026
			WHERE
				artcod = '$codigo'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		if( $rows = mysql_fetch_array( $res ) ){
			$val = $rows[ 'Artuni' ];
		}
	}
	
	return $val;
}
 
/******************************************************************************************************************************
 * Verifica que un articulo exista para un paciente en una fecha dada
 ******************************************************************************************************************************/
function existeArticuloMatrixActivo( $conex, $wbasedato, $historia, $ingreso, $codArticulo, $fechaKardex ){
	
	$val = false;
	
	$sql = "SELECT
				Kadart, Kadcfr, Kadufr, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadvia, Kadfec, Kadcon, Kadobs, Kadsus, Kadori, Kadcnd, Kaddma, Kadcan, Kaduma, Kadcma, Kadsad, Kaddis, Kadcdi, Kadreg, Kadcpx, Kadron, Kadfro, Kadaan, Kadcda, Kadcdt,Kaddan,Kadfum,Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf, id
			FROM
				{$wbasedato}_000054
			WHERE
				Kadart = '$codArticulo'
				AND Kadfec = '$fechaKardex'
				AND Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND kadsus != 'on'
		";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		if( $rows = mysql_fetch_array( $res ) ){
			$val = $rows[ 'id' ];
		}
	}
	
	return $val;
}
 

/************************************************************************************************
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
 ************************************************************************************************/
function esMMQ( $conex, $wbasedato, $art ){
	
	$esmmq = false;

	$sql = "SELECT 
				artcom, artgen, artgru, melgru, meltip
			FROM 
				{$wbasedato}_000026 LEFT OUTER JOIN {$wbasedato}_000066 
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
 
 
/******************************************************************************************
 * Busca si un centro de costos maneja ciclos de producción o no
 ******************************************************************************************/
function tieneCpxContingencia( $conex, $wbasedato, $cco ){
		
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000011
			WHERE
				ccocod = '$cco'
				AND ccocpx = 'on'
				AND ccoest = 'on'
				AND ccourg != 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en elquery $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	
	return $val;
}
 

/******************************************************************************************
 * Carga los articulos a los pacientes correspondientes segun la tabla de contingencia
 ******************************************************************************************/
function cargarArticulos( $conex, $wbasedato, $historia, $articulo, $fechaKardex ){

	global $wemp_pmla;
	global $user;
	global $wuser;
	global $usuario;
	global $wbasedato;
	global $wcenmez;
	global $conex;
	
	$mensajes = "";
	
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	@$usuario = consultarUsuarioKardex($wuser);
	
	validarHistoria( $conex, $wbasedato, $historia, &$ingreso, &$datosPacientes, &$activo );
	
	$ccoOrigen = '1050';
	$fecha = date( "Y-m-d" );
	
	if( $activo ){
	
		$cargar = true;
		
		//Valido si tiene cpx
		$tieneCpx = tieneCpxContingencia( $conex, $wbasedato, $datosPacientes['Ubisac'] );		
		
		$esMMQ = esMMQ( $conex, $wbasedato, $articulo['Conart'] );
		
		$mensajes .= "<br>Articulo a cargar: {$articulo['Conart']}";
		
		//Si no es material medico quirurgico el medicamento debe estar en el kardex activo
		if( !$esMMQ ){
			
			//Verifico que este en el medicamento este en el kardex
			$existe = existeArticuloMatrixActivo( $conex, $wbasedato, $historia, $ingreso, $articulo['Conart'], $fechaKardex );
			
			if( $existe ){
			
				$mensajes .= "<br>Medicamento activo en el kardex";
				
				//Apruebo el medicamento antes de cargar
				$sql = "UPDATE 
							{$wbasedato}_000054 
						SET
							Kadare = 'on'
						WHERE
							Kadart = '{$articulo['Conart']}'
							AND Kadfec = '$fechaKardex'
							AND Kadhis = '$historia'
							AND Kading = '$ingreso'
							AND kadsus != 'on'
						";
						
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_affected_rows();
				
				actualizarCampoContingenciaKardex( $conex, $wbasedato, $articulo[ 'id' ], 'Conapr', 'on' );
				
				$mensajes .= "<br>Medicamento aprobado";
			}
			else{
				$mensajes.= "<br>El medicamento no se encuentra en el kardex o esta suspendido";
				$cargar = false;
			}
		}
		else{
			$mensajes .= "<br>El medicamento es MMQ";
		}

		if( $cargar ){
		
			actualizarCampoContingenciaKardex( $conex, $wbasedato, $articulo[ 'id' ], 'Concar', 'on' );
		
			$unidad = consultarUnidadArticulo( $conex, $wbasedato, $articulo['Conart'] );
		
			$mensajes.= "<br>cargando articulo";
			
			for( $i = 0; $i < $articulo[ 'Condis' ]; $i++ ){
				//Se carga de a uno para no validar cantidades maximas de medicamentos ni especiales
				if( $tieneCpx ){
					$url = "/matrix/movhos/procesos/cargoscpx.php?emp=$wemp_pmla&bd=$wbasedato&tipTrans=C&wemp=$wemp_pmla&cco[cod]={$ccoOrigen}&historia=$historia&fecDispensacion=$fecha&artcod={$articulo['Conart']}&art[can]=1&art[max]=1&artValido=1&art[cva]=0&art[ini]={$articulo['Conart']}&ke=1&procesoContingencia=on&marcaContingencia=C&art[uni]=$unidad";
				}
				else{
					$url = "/matrix/movhos/procesos/cargos.php?emp=$wemp_pmla&bd=$wbasedato&tipTrans=C&wemp=$wemp_pmla&cco[cod]={$ccoOrigen}&historia=$historia&fecDispensacion=$fecha&artcod={$articulo['Conart']}&art[can]=1&art[max]=1&artValido=1&art[cva]=0&art[ini]={$articulo['Conart']}&ke=1&procesoContingencia=on&marcaContingencia=C&art[uni]=$unidad";
				}
				
				//$mensajes .= "<br><br>historia: $historia. Url enviado por ajax: ".$url;
				?>
				<script>
					consultasAjax( 'GET', '<?php echo $url;?>', '', false, '' );
				</script>
				<?php
			}
		}
	}
	
	echo $mensajes;
}

/****************************************************************************************************************
 * Lee todos los datos correspondientes de la tabla de contingencia para cargarlos al paciente
 ****************************************************************************************************************/
function leerDatosCargarArticulos( $fecha ){

	global $wemp_pmla;
	global $wbasedato;
	global $wcenmez;
	global $conex;
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	
	$val = "";
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000123
			ORDER BY Conhis
			";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	$terminar = true;
	$i = 0;
	
	if( $num > 0 ){
		
		$mensajes = "<br><br>leyendo datos para cargar.....";
		
		for( $i = 0; $i < $rows = mysql_fetch_array( $res ); $i++ ){
			cargarArticulos( $conex, $wbasedato, $rows[ 'Conhis' ], $rows, $fecha );
		}
	}
	else{
		$mensajes .= "<br>Sin datos para leer";
	}
	
	echo $mensajes;
}
 
/************************************************************************************************
 * FIN FUNCIONES PARA CARGAR ARTICULOS
 ************************************************************************************************/
 
/************************************************************************************************************
 * REPORTE CONTINGENCIA
 ************************************************************************************************************/
function reporteArticulosCreadosPorContingencia( $fecha ){

	global $conex;
	global $wemp_pmla;
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	
	
	$sql = "SELECT 
                Habcco, Habcod, Habhis, Habing, count(*) as Total
            FROM 
                {$wbasedato}_000054, {$wbasedato}_000020
            WHERE 
                Kadusu = '$wbasedato'
				AND Kadfec = '$fecha'
				AND kadhis = habhis
				AND kading = habing
			GROUP BY
				Habcco, Habcod, Kadhis, Kading
			ORDER BY
				Habcco, habcod
            ;";
                 
    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		echo "<center><h2>ARTICULOS CREADOS POR CONTINGENCIA</h2></center>";
	
		echo "<table align='center'>";
		
		echo "<tr class='encabezadotabla'>";
		echo "<td>Cco</td>";
		echo "<td>Hab.</td>";
		echo "<td>Historia</td>";
		echo "<td>Ingreso</td>";
		echo "<td>Total</td>";
		echo "</tr>";
	
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			$class = "fila".( ($i%2) + 1 )."";
		
			echo "<tr class='$class' align='center'>";
			
			echo "<td>"; 
			echo $rows[ 'Habcco' ];
			echo "</td>";
			
			echo "<td>";
			echo $rows[ 'Habcod' ];
			echo "</td>";
			
			echo "<td>";
			echo $rows[ 'Habhis' ];
			echo "</td>";
			
			echo "<td>";
			echo $rows[ 'Habing' ];
			echo "</td>";
			
			echo "<td>";
			echo $rows[ 'Total' ];
			echo "</td>";
			
			echo "</tr>";
		}
		
		echo "<table align='center'>";
	}
	else{
		echo "<br>";
		echo "<center><b>Sin Resultados</b></center>";
	}
	
}
/************************************************************************************************************/
	
include_once( "movhos/kardex.inc.php" );
include_once( "movhos/registro_tablas.php" );
include_once( "root/comun.php" );




// $conex = obtenerConexionBD("matrix");

encabezado( "CONTINGENCIA KARDEX", "1.0 Abril 25 de 2012" ,"clinica" );

if( !isset($mostrar) or empty($mostrar) ){
	$mostrar = 'off';
}

echo "<form>";

if( $mostrar == 'off' ){

	echo "<table align='center'>";
	
	echo "<tr class='fila1'>";
	echo "<td><b>Fecha Inicio Contingencia</b></td>";
	echo "<td class='fila2'><INPUT type='text' name='fechaContingencia'  value='".date( "Y-m-d" )."'></td>";
	echo "</tr>";
	
	echo "<tr class='fila1'>";
	echo "<td><B>Hora Incio Contingencia</B></td>";
	
	echo "<td class='fila2'>";
	
	echo "<select name='horaContingencia'>";
	
	for( $i = 0; $i < 24; $i += 2 ){
		echo "<option>".gmdate( "H:i:s", $i*3600 )."</option>";
	}
	
	echo "</select>";
	
	echo "</td>";
	
	echo "</tr>";
	
	
	echo "<tr class='fila1'>";
	echo "<td><b>Fecha Final Contingencia</b></td>";
	echo "<td class='fila2'><INPUT type='text' name='fechaFinContingencia' value='".date( "Y-m-d" )."'></td>";
	echo "</tr>";
	
	echo "<tr class='fila1'>";
	echo "<td><B>Hora Final Contingencia</B></td>";
	
	echo "<td class='fila2'>";
	
	echo "<select name='horaFinContingencia'>";
	
	for( $i = 0; $i < 24; $i += 2 ){
		echo "<option>".gmdate( "H:i:s", $i*3600 )."</option>";
	}
	
	echo "</select>";
	
	echo "</td>";
	
	echo "</tr>";
	
	//Tipo de proceso
	echo "<tr class='fila1'>";
	echo "<td><b>Proceso</b></td>";
	echo "<td  class='fila2'>"; 
	
	echo "<select name='proceso'>";
	echo "<option value='R'>R-Reporte</option>";
	echo "<option value='D'>D-Cargar articulos por contingencia</option>";
	echo "<option value='E'>E-Recibir y aplicar por Contingencia</option>";
	echo "<option value='PC'>PC-Precontingencia</option>";
	echo "<option value='C'>C-Grabar articulos al Kardex por Contingencia</option>";
	echo "</select>";
	
	echo "</td>";
	echo "</tr>";
	
	echo "<table align='center'>";
	
	echo "<input type='hidden' name='mostrar' value='on'>";
	echo "<input type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	echo "<br><br>";
	echo "<table align='center'>";
	echo "<tr><td align='center' colspan='2'><INPUT type='submit' value='Procesar' style='width:100'> | <INPUT type='submit' value='Cerrar' style='width:100' onClick='cerrarVentana();'></td><tr>";
	echo "</table>";
	
}
else{

	switch( $proceso ){
	
		case 'PC':
			
			$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		
			aplicar_medicamentos($conex, $fechaContingencia , $fechaFinContingencia, $horaContingencia, $horaFinContingencia, $proceso, $wbasedato, $wemp_pmla, $wbasedato );
			//aplicar_medicamentos($conex, $fech_ini_contig, $fech_fin_contig, $hora_ini_contig, $hora_fin_contig, $marca, $bd, $wemp_pmla, $usuario)
			//recibirCarro( $conex, $wbasedato, $fechaContingencia, $proceso );
			break;
		
		case 'C':
			leerDatosKardex();
			break;
			
		case 'D':
			leerDatosCargarArticulos( $fechaFinContingencia );
			echo "<INPUT TYPE='hidden' name='proceso' value='E'>";
			break;
		
		case 'E':
			$proceso = 'C';
			
			$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
			
			// aplicar_medicamentos($conex, $fechaContingencia, $horaContingencia, $horaFinContingencia, $proceso, $wbasedato, $wemp_pmla, $wbasedato );
			aplicar_medicamentos($conex, $fechaContingencia , $fechaFinContingencia, $horaContingencia, $horaFinContingencia, $proceso, $wbasedato, $wemp_pmla, $wbasedato );
			recibirCarro( $conex, $wbasedato, $fechaContingencia, $proceso );
			break;
		
		default: reporteArticulosCreadosPorContingencia( $fechaContingencia ); break;
	}
	
	echo "<INPUT TYPE='hidden' name='mostrar' value='off'>";
	echo "<INPUT TYPE='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	echo "<center><INPUT type='submit' value='RETORNAR' style='witdh:100'></center>";
}

echo "</form>";

?>







