<?php
include_once("conex.php");
//=========================================================================================================================================
//
//			R E P O R T E  L I S T A  D I E T A S
//=========================================================================================================================================
//FECHA CREACION:       Agosto 24 de 2012.
//AUTOR:                Jerson Andres Trujillo.                                                                                                        \\
//=========================================================================================================================================\\
//OBJETIVO:		Visualizar los servicios de alimentacion asociados a los pacientes, a traves de diferentes filtros de consulta, 
//				para asi poder realizar la impresion de dicha informacion.   
//Descripcion:                                                                                                                                                                                                                                    \\
//==========================================================================================================================================\\	
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//22 de marzo de 2022: Sebastian Alvarez Barona: Se realiza filtro de sede(Sede80 y sedeSur) a los centros de costos del selector y a la información
//												 ofrecida cuando se consulta.
//=========================================================================================================================================\\
//2018-04-07: (Jonatan Lopez) Se elimina la columna ultimo movimiento y se agrega si el paciente es afin.
//2013-05-16: (Jonatan Lopez) Se cambia el metodo de envio de $.post a $.ajax en la funcion imprimir para que si se realice la actualizacion de la impresion.
//Abril 10 de 2014:	(Jonatan Lopez)	Se controla que no aparezca la nutricionista, el patron asociado a DSN y la observacion DSN cuando 
//				el patron sea diferente a DSN.
//2013-05-16: Se muestra el nombre del nutricionista bajo la observacion "movnut"
//2013-04-17: Se agrega la columna "Observacion Nutricionista" que muestra el campo "movods".
//2013-04-15: Se concatena el campo "movdsn" en la columna "patron" cuando es distinto de vacio
//2013-01-30: Se agrega una collumna nueva al listado para que identifique los pacientes tipo POS.                                                                                                                                             \\
//                                                                                                                                         \\
//=========================================================================================================================================\\
//=====================================
//		INICIO DE SESSION
//=====================================
@session_start();
header('Content-type: text/html;charset=ISO-8859-1');
if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
{
	echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

	include_once("root/comun.php");
	include_once("root/magenta.php");
	$conex = obtenerConexionBD("matrix");
	$wactualiz="(22 de marzo de 2022)";                      // ultima fecha de actualizacion               
	$wfecha	=	date("Y-m-d");   
	$whora 	= 	(string)date("H:i:s");                                                         

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	//====================================================================================================================================    
	// F U N C I O N E S   G E N E R A L E S
	//====================================================================================================================================
	// Consultar si el paciento es POS 
    function consultartipopos($whistoria, $wingreso, $wser)
     {
         global $conex;
         global $wbasedato;
         
         //Se consulta el tipo de empresa responsable del paciente
         $q_resp = "SELECT ingtip 
                      FROM ".$wbasedato."_000016 
                     WHERE Inghis = '".$whistoria."'
                       AND Inging = '".$wingreso."'";
         $resresp = mysql_query($q_resp,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_resp." - ".mysql_error());
         $rowresp =mysql_fetch_array($resresp);         
         $wtpo = $rowresp['ingtip'];         
         
         //Se cosulta si el tipo de empresa se encuentra en el listado de empresas tipo POS.
         $q = "   SELECT COUNT(*) 
                    FROM ".$wbasedato."_000076
                   WHERE Sertpo LIKE '%".$wtpo."%'
                     AND Serest = 'on' ";
         $restpo = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $rowtpo=mysql_fetch_array($restpo);
        
        if ($rowtpo[0] > 0)
        {
            $wtipopos = "on";
        }
        else
        {
            $wtipopos = "off";
        }
        
        return $wtipopos;
         
         
     }
    
    
    
    function fecha_y_hora_impresion ()
	{
		echo "<table width =100%>
				<tr class='mensaje'>
					<td >
						<font text color=#CC0000><b>(*)</b></font>: Solicitudes con movimientos posteriores al que se esta listando.
					</td>
					<td align=right>
						<b>Fecha y Hora de Impresión:</b> ".date("Y-m-d").", ".(string)date("H:i:s")." 
					</td>
				</tr>
                <tr class='mensaje'>
                <td>
                        <font text color=#CC0000><b>(P)</b></font>: Pacientes tipo POS.
                </td>
                </tr>
			</table><br>";
	}
	
	function calcular_edad ($wnac)
	{
		$wfnac=(integer)substr($wnac,0,4)*365 +(integer)substr($wnac,5,2)*30 + (integer)substr($wnac,8,2);
		$wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
		$wedad=(($wfhoy - $wfnac)/365);
			  
		if ($wedad < 1)
			$wedad = number_format(($wedad*12),0,'.',',')." Meses";
		else
			$wedad=number_format($wedad,0,'.',',')." Años"; 
		return $wedad;
	}
	/*function calcularAnioMesesDiasTranscurridos($fecha_inicio, $fecha_fin = '')
	{
		$datos = array('anios'=>0,'meses'=>0,'dias'=>0);
		$explodefi = explode('-',$fecha_inicio);
		$anio_ini = $explodefi[0];
		$mes_ini = $explodefi[1];
		$dia_ini = $explodefi[2];

		$anio_fin = date("Y");
		$mes_fin = date("m");
		$dia_fin = date("d");
		$explodeFIN = explode('-',$fecha_fin);
		if($fecha_fin != '' && count($explodeFIN)==3)
		{
			if ($explodeFIN[0] != '0000' && $explodeFIN[1] != '00' && $explodeFIN[2] != '00')
			{
			$anio_fin = $explodeFIN[0];
			$mes_fin = $explodeFIN[1];
			$dia_fin = $explodeFIN[2];
			}
		}

		$AInicio = $anio_ini;
		$AFinal = $anio_fin;

		$sumadiasBis = 0;

		for ($i = $AInicio; $i <= $AFinal; $i++)
		{
		
			// ( strtotime( $AInicio."-01-01 00:00:00" ) - strtotime( "$AInicio-12-31 23:59:59" ) )/(24*3600);
			
			//saber si ese año tiene año bisiesto.
			$sumadiasBis += (date( "L", strtotime( $AInicio."-01-01" ))==0) ? 86400 : 0;
		
			// $bis = (($i % 4) == 0 && ($i % 100) != 0 ) ? 86400 : 0;
			// $sumadiasBis += $bis;
		}

		// Calculamos los segundos entre las dos fechas
		$fechaInicio = mktime(0,0,0,$mes_ini,$dia_ini,$anio_ini);
		$fechaFinal = mktime(0,0,0,$mes_fin,$dia_fin,$anio_fin);

		$segundos = ($fechaFinal - $fechaInicio);
		$anyos = floor(($segundos-$sumadiasBis)/31536000);
		$datos['anios'] = $anyos;

		$segundosRestante = ($segundos-$sumadiasBis)%(31536000);
		$meses = floor($segundosRestante/2592000);
		$datos['meses'] = $meses;

		$segundosRestante = ($segundosRestante%2592000); // Suma un día mas por cada años bisiesto
		//$segundosRestante = (($segundosRestante-$sumadiasBis)%2592000); // No suma un día mas por cada año bisiesto
		$dias = floor($segundosRestante/86400);
		$datos['dias'] = $dias;
		return $datos;
	}*/
	//----------------------------------------
	//	Funciones para calcular edad
	//----------------------------------------
	function tiempo_transcurrido($fecha_nacimiento, $fecha_control)
	{
		$fecha_actual = $fecha_control;
	   
		if(!strlen($fecha_actual))
		{
			$fecha_actual = date('d/m/Y');
		}

		// separamos en partes las fechas 
		$array_nacimiento = explode ( "/", $fecha_nacimiento ); 
	    $array_actual = explode ( "/", $fecha_actual ); 

	    $anos =  $array_actual[2] - $array_nacimiento[2]; // calculamos años 
	    $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
	    $dias =  $array_actual[0] - $array_nacimiento[0]; // calculamos días 

	    //ajuste de posible negativo en $días 
	    if ($dias < 0) 
	    { 
		    --$meses;

		    //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual 
		    switch ($array_actual[1]) 
			{ 
				case 1: 
					$dias_mes_anterior=31;
					break; 
				case 2:     
					$dias_mes_anterior=31;
					break; 
				case 3:  
					if (bisiesto($array_actual[2])) 
					{ 
						$dias_mes_anterior=29;
						break; 
					} 
					else 
					{ 
						$dias_mes_anterior=28;
						break; 
					} 
				case 4:
					$dias_mes_anterior=31;
					break; 
				case 5:
					$dias_mes_anterior=30;
					break; 
				case 6:
					$dias_mes_anterior=31;
					break; 
				case 7:
					$dias_mes_anterior=30;
					break; 
				case 8:
					$dias_mes_anterior=31;
					break; 
				case 9:
					$dias_mes_anterior=31;
					break; 
				case 10:
					$dias_mes_anterior=30;
					break; 
				case 11:
					$dias_mes_anterior=31;
					break; 
				case 12:
					$dias_mes_anterior=30;
				break; 
			}	 

		$dias=$dias + $dias_mes_anterior;

			if ($dias < 0)
			{
				--$meses;
				if($dias == -1)
				{
					$dias = 30;
				}
				if($dias == -2)
				{
					$dias = 29;
				}
			}
		}

		//ajuste de posible negativo en $meses 
		if ($meses < 0) 
		{ 
			--$anos; 
			$meses=$meses + 12; 
		}

		$tiempo[0] = $anos;
		$tiempo[1] = $meses;
		$tiempo[2] = $dias;

		return $tiempo;
	}

	function bisiesto($anio_actual)
	{ 
		$bisiesto=false; 
		//probamos si el mes de febrero del año actual tiene 29 días 
		if (checkdate(2,29,$anio_actual)) 
		{ 
			$bisiesto=true; 
		} 
		return $bisiesto; 
	}
	//----------------------------------------
	//	Fin calcular edad
	//----------------------------------------
	function traer_tipos_estados()
	{
		global $wbasedato;
		global $conex;
		$q = " SELECT Tescod, Tesdes "
			."   FROM ".$wbasedato."_000130 "
			."  WHERE Tesest  = 'on' "
			."  Order by Tescod";
			  
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$array_res['%']='Todas';
		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res; 
	}
	
	function traer_estado ($tipo)
	{
		global $wbasedato;
		global $conex;
		
		$q = " SELECT Estdes, Estcod "
			."   FROM ".$wbasedato."_000129, ".$wbasedato."_000130 "
			."	WHERE Tescod LIKE '".$tipo."'"
			."	  AND Tesest = 'on' "
			."    AND Tescod = Esttes "
			."    AND Estest = 'on' "
			."	ORDER BY Estcod ";
			  
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		$options.= "<option>% - TODAS</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res); 
			$options.= "<OPTION>".$row['Estdes']."</OPTION>";
		}
		return $options;
	}
	
	function consultarNombreUsuario( $wcodigo ){
	
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
	
		$q = " SELECT descripcion "
			."   FROM usuarios "
			."  WHERE codigo = '".$wcodigo."' ";
	
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
			$row = mysql_fetch_array($res);
			return $row[0];
		}
		
		return "";
	}	
	
	function servicio_individual($pk_nom_patron)
	{
		global $wbasedato;
		global $conex;
		$q_servicio=" SELECT Dieind
					  FROM ".$wbasedato."_000041
					  WHERE Diecod='".$pk_nom_patron."'
					  AND 	Dieest='on'
					";
		$res_servicio= mysql_query($q_servicio,$conex) or die ("Error: ".mysql_errno()." - en el query (Servicio individual): ".$q_servicio." - ".mysql_error());
		$row_servicio= mysql_fetch_array($res_servicio);
		if ($row_servicio[0]=='on')
			return true;
		else
			return false;
	}
	
	function separarTexto( $texto ){
		if( $texto != '' ){
			$arr = explode(" ", $texto);
			$aux = "";
			foreach($arr as $p=>$val){
				(strlen($val) > 2 ) ? $aux.= $val."<br>" : $aux.= $val." ";
			}
			$auxq = substr($aux, -3);  //Si el ultimo caracter es una coma
			if( $auxq == "<br>" ){
				$aux = substr($aux, 0, -3); 
			}
			$texto = $aux;
		}
	}
		
	//==================================================================================================
	//FIN FUNCIONES
	//==================================================================================================	
	//==================================================================================================
	// FILTROS AJAX
	//==================================================================================================
	if ( isset($consultaAjax) && isset($accion) )
	{
		switch ($accion)
		{
			case 'RecargarAcciones':
									{
										$options=traer_estado($tipo);
										echo $options;
										break;
									}
			case 'marcar_impresos':	{
										$array_id = explode ('|',$id_77);
										foreach ($array_id as $valor_id)
										{
											$q_imprimir = " UPDATE ".$wbasedato."_000077
															SET Movimp = 'on'
															WHERE id = '".$valor_id."'			
														";
											$res_imprimir = mysql_query($q_imprimir,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_imprimir." - ".mysql_error());
										}
										break;
									}
		}
		return;
	}
	//==================================================================================================
	// FIN FILTROS AJAX
	//==================================================================================================
	//===============================
	// EJECUCION NORMAL DEL PROGRAMA
	//===============================
	else{	
?>
	<html>
	<head>
		<title>Listado de dietas por piso</title>
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<style type="text/css">
				.ocultar{
						display:none;
						}
				.mensaje{
						color: #676767;
						font-family: verdana;
						font-weight:bold;
						font-size: 10pt;
						}
				.mostrar{
						display:;
						}
		</style>
	
		<script type="text/javascript">
			function enter()
			{
				document.forms.listadietas.submit();
			}
			
			function cerrarVentana()
			{
				window.close();		  
			}
		  
			function imprimir(seleccion) 
			{
			
				//marcar los registro como ya impresos
				var id_77 = $("#id_77").val();
				
				$.ajax({
						url: "rep_lista_dietas.php",
						type: "POST",
						data:{

							consultaAjax:   '',
							wemp_pmla:      $('#wemp_pmla').val(),
							accion:         'marcar_impresos',
							id_77:			id_77


						},
						dataType: "html",
						async: true,
						success:function(data_json) {

							if (data_json.error == 1)
							{

							}
							else{

								var contenido = "<html><body onload='window.print();window.close();'>"
								+"<style type='text/css'> "
									+""
											+".fila1 {"
													+"color: #000000;"
													+"font-size: 8pt;"
													+"border-left: 1px #000000 dashed;"
													+"border-top: 1px #000000 dashed;"
												+"}"
											+".fila2 {"
													+"color: #000000;"
													+"font-size: 8pt;"
													+"border-left: 1px #000000 dashed;"
													+"border-top: 1px #000000 dashed;"
													+"}"
											+".encabezadoTabla {"
													+"color: #000000;"
													+"font-size: 9pt;"
													+"font-weight:bold;"
													+"}"
											+".mensaje{"
													+"color: #676767;"
													+"font-family: verdana;"
													+"font-weight:bold;"
													+"font-size: 8pt;"
													+"}";
									contenido +=""
								+"</style>";
							contenido = contenido + document.getElementById(seleccion).innerHTML + "</body></html>";
							var ventana = window.open('', '', '');
							ventana.document.open();
							ventana.document.write(contenido);
							ventana.document.close();
							enter();
							}
						}

					});
				
				
				// $.post("rep_lista_dietas.php",
				// {
					// consultaAjax:   '',
					// wemp_pmla:      $('#wemp_pmla').val(),
					// accion:         'marcar_impresos',
					// id_77:			id_77
				// }
				// ,function(data) {
				// }
				// );
				// //fin marcar los registro 
				
				// var contenido = "<html><body onload='window.print();window.close();'>"
					// +"<style type='text/css'> "
						// +""
								// +".fila1 {"
										// +"color: #000000;"
										// +"font-size: 8pt;"
										// +"border-left: 1px #000000 dashed;"
										// +"border-top: 1px #000000 dashed;"
									// +"}"
								// +".fila2 {"
										// +"color: #000000;"
										// +"font-size: 8pt;"
										// +"border-left: 1px #000000 dashed;"
										// +"border-top: 1px #000000 dashed;"
										// +"}"
								// +".encabezadoTabla {"
										// +"color: #000000;"
										// +"font-size: 9pt;"
										// +"font-weight:bold;"
										// +"}"
								// +".mensaje{"
										// +"color: #676767;"
										// +"font-family: verdana;"
										// +"font-weight:bold;"
										// +"font-size: 8pt;"
										// +"}";
						// contenido +=""
					// +"</style>";
				// // contenido = contenido + document.getElementById(seleccion).innerHTML + "</body></html>";
				// // var ventana = window.open('', '', '');
				// // ventana.document.open();
				// // ventana.document.write(contenido);
				// // ventana.document.close();
				// // enter();
			}
			
			function recargar_acciones(tipo_estado, select)
			{
				var todos_radio = document.getElementById("radios").getElementsByTagName("input");
				var num = todos_radio.length;
				for (y=0; y<num; y++)
				{
					todos_radio[y].checked = false;
				}
				document.getElementById(tipo_estado).checked = true;
				
				var tipo = document.getElementById(tipo_estado).value;
				$('#'+select).load("rep_lista_dietas.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion=RecargarAcciones&tipo="+tipo);
			}
			
			function ejecutar(path)
			{
				window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=0,titlebar=0');
			}

			$(document).on('change','#selectsede',function(){
				window.location.href = "rep_lista_dietas.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val();
			});
			
		</script>
	</head>
	<body>
<?php
	
	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/ajax-loader5.gif'/>";
	echo "<br><br> Por favor espere un momento ... <br><br>";
	echo '</div>';
	echo "<script>";
	echo "$.blockUI({ message: $('#msjEspere') });";
	echo "</script>";
	//================================================================
	//	FORMA 
	//================================================================
	echo "<form name='listadietas' action='' method=post>";
	//================================================================
	//	ENCABEZADO
	//================================================================
	encabezado("Reporte de Dietas por Piso", $wactualiz, 'clinica', TRUE);

	echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>"; 
	if (strpos($user,"-") > 0)
		$wusuario = substr($user,(strpos($user,"-")+1),strlen($user));
	//=================================================================
	//	FILTROS DE CONSULTA PARA EL REPORTE   
	//=================================================================
	echo "<center><table width='76%'>";
	//=================================
	// SELECCIONAR CENTRO DE COSTOS
	//=================================
	//Traigo los centros de costos
	$q = " SELECT Ccoord, Ccocod, Cconom "
		."   FROM ".$wbasedato."_000011"
		."  WHERE Ccoest = 'on' "
		."    AND ccohos  = 'on' "
		."	ORDER BY Ccoord, Ccocod ";
		
		  
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	echo "<tr class=seccion1>";
		echo "<td align=center colspan=2 ><b>CENTRO DE COSTOS: </b>&nbsp;
			 <SELECT name='wcco' id='wcco' >";
		if (isset($wcco))
		{
			echo "<OPTION SELECTED>".$wcco."</OPTION>";
		} 
		echo "<option>% - TODOS</option>";
		/** 
		 * Sebastian Alvarez Barona
		 * Date: 18-03-2022
		 * Descripcion: Se comenta para traernos del comun.php una función que nos trae los mismos datos
		 * esto para que sea mas eficiente y evitar centros de costos quemados.
		 */
		// for ($i=1;$i<=$num;$i++)
		// {
		// 	$row = mysql_fetch_array($res); 
		// 	echo "<OPTION>".$row['Ccocod']." - ".$row['Cconom']."</OPTION>";
		// }
		
		/** 
		 * Sebastian Alvarez Barona
		 * Date: 18-03-2022
		 * Descripcion: Se llama a la función del comun.php para traer los centros de costos que sean hospitalarios */
		centrosCostosHospitalariosTodos($selectsede);

		echo "</SELECT></td></tr>";
	//=================================
	// SELECCIONAR EL SERVICIO
	//=================================
	echo "<tr class=seccion1>";
	if (isset($wser))
	{
		if($wser=='%')
			$nom_ser_selec[0]='% - TODOS';
		else
		{
		  $q1 = " SELECT sernom "
			  ."   FROM ".$wbasedato."_000076 "
			  ."  WHERE sercod = '".$wser."' ";
		  $resser1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
		  $nom_ser_selec = mysql_fetch_array($resser1);
		}
	}
	//Consultar los servicios del maestro
	$q = " SELECT sernom, serhin, serhfi, sercod "
		."   FROM ".$wbasedato."_000076 "
		."  WHERE serest = 'on' ";
	$resser = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numser = mysql_num_rows($resser);

		echo "<td align=center ><b>SERVICIO DE ALIMENTACIÓN:</b>&nbsp;
			<SELECT name='wser' id='wser'>";
		if (isset($wser))
			echo "<OPTION SELECTED value=".$wser.">".$nom_ser_selec[0]."</OPTION>";
	 
		echo "<option value='%' >% - TODOS</option>";	 
		for ($i=1;$i<=$numser;$i++)
		{
			$rowser = mysql_fetch_array($resser); 
			echo "<OPTION value=".$rowser[3].">".$rowser[0]."</OPTION>";
		} 
	echo "</SELECT>";
	//=================================
	// SELECCIONAR SI ACTIVO O NO
	//=================================
	$checked1 = " ";
	$checked2 = " ";
	$checked3 = " ";
	$checked4 = " ";
	$checked5 = " ";
	if (isset($activo))
	{
		switch ($activo)
		{
			case 'on' :
						$checked1 = "checked='checked'";
						break;
			case 'off':
						$checked2 = "checked='checked'";
						break;
			case ''   :
						$checked3 = "checked='checked'";
						break;
		}
	}
	else
	{
		$checked1 = "checked='checked'";
	}
	if (isset($wtipo))
	{
		switch ($wtipo)
		{
			case 'pedido' :
						$checked4 = "checked='checked'";
						break;
			case 'adicion':
						$checked5 = "checked='checked'";
						break;
			case ''   :
						$checked6 = "checked='checked'";
						break;
		}
	}
	else
	{
		$checked4 = "checked='checked'";
	}
		
		echo "<td align=center ><b>SOLICITUDES:</b><br>
				Activas <input type='radio' name='activo'  value='on'    ".$checked1.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Canceladas <input type='radio' name='activo'  value='off' ".$checked2." >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Todas <input type='radio' name='activo'  value=''  		 ".$checked3." ><br>
				<b>TIPO:</b><br>
				Pedido <input type='radio' name='wtipo'  value='pedido'    ".$checked4.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Adiciones <input type='radio' name='wtipo'  value='adicion' ".$checked5." >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Todas <input type='radio' name='wtipo'  value=''  		 ".$checked6." ><br>
			</td></tr>";
	//=================================
	// FIN ACTIVOS
	//=================================
	//=================================
	// SELECCIONAR ESTADO
	//=================================
	/*
	echo "<tr class=seccion1>";
		echo "<td align=center>";
			echo "<b>TIPO DE ACCIÓN:</b>";
			$array_estados = traer_tipos_estados();
			echo "<table name='radios' id='radios'>";
			$z=1;
			foreach($array_estados as $clave => $valor)
			{
				//estas validaciones son para mostrar los resultados en 3 columnas dentro de la misma celda
				if($z==1 || ($z-1 % 3)==0)
					echo "<tr>";
				
				if (isset($tipo_estados) && $tipo_estados==$clave)
				{
					$chec ="checked='checked'";
				}
				else
				{
					$chec=" ";
				}	
				echo "<td class='fila1'>&nbsp;
					<input type='radio' id='".$clave ."' name='tipo_estados'  value='".$clave."'  ".$chec." onclick='recargar_acciones(\"".$clave ."\", \"wacc\");'>";
				echo "&nbsp;".$valor."</td>";
				
				if(($z % 3)==0)
					echo "</tr>";
				
				$z++;
			}
			echo "</table>";
		echo "</td>";
	//=================================
	// SELECCIONAR ACCION
	//=================================
	if(isset($tipo_estados))
		$cosultar_tipo = $tipo_estados;
	else
		$cosultar_tipo = '%';
		
	$options = traer_estado($cosultar_tipo);
		echo "<td align=center><b>ACCIÓN:</b>&nbsp;";
		echo 	"<SELECT name='wacc' id='wacc' >";
				if (isset($wacc))
				{
					if($wacc=='% - TODAS')
						$nom_estado[0]='% - TODAS';
					else
					{
						$q1 = " SELECT Estdes "
							."   FROM ".$wbasedato."_000129 "
							."  WHERE Estdes LIKE '".$wacc."' ";
						$resser1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
						$nom_estado = mysql_fetch_array($resser1);
					}
					echo "<OPTION SELECTED >".$nom_estado[0]."</OPTION>";
				}
				echo $options;
		echo	"</SELECT>";
		echo "</td>";
	echo "</tr>";*/
	
	//=================================
	// SELECCIONAR FECHAS A CONSULTAR
	//=================================
	echo "<tr class='seccion1'>
		<td align=center><b>FECHA INICIAL: </b>";  
		if(isset($wfec_i) && isset($wfec_f))
		{
			campoFechaDefecto("wfec_i", $wfec_i);
		}
		else
		{
			campoFechaDefecto("wfec_i", date("Y-m-d"));
		}
		echo "</td>";
		echo "<td align=center><b>FECHA FINAL: </b>"; 
		if(isset($wfec_i) && isset($wfec_f))
		{
			campoFechaDefecto("wfec_f", $wfec_f);
		}
		else
		{
			campoFechaDefecto("wfec_f", date("Y-m-d"));
		}
		echo "</td>";
	echo "</tr></table>";
	//====================================================================================
	$checked_imp1 = " ";
	$checked_imp2 = " ";
	if (isset($impresas))
	{
		switch ($impresas)
		{
			case 'on' :
						$checked_imp1 = "checked='checked'";
						break;
			case 'off':
						$checked_imp2 = "checked='checked'";
		}
	}
	else
	{
		$checked_imp1 = "checked='checked'";
	}	
	echo "<table width='76%'><tr class='fila2'>";
		echo "
			<td align='right'>
				<b><input type='submit' value='CONSULTAR'></b>
			</td>
			<td width='40%' align='center'>
				<b>Pendientes de Imprimir </b><input type='radio' name='impresas'  value='on'    ".$checked_imp1." onclick='enter();'>&nbsp;
				<b>Todas </b><input type='radio' name='impresas'  value='off' ".$checked_imp2."  onclick='enter();'>&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;<img src='../../images/medical/hce/impresora.jpg' id='imprimir' style='cursor:pointer;' title = 'Imprimir informe' border='0' onclick='imprimir(\"seleccion\")'/>
			</td>
			";
	echo "</tr>";
	echo "</table><br>";
	//====================================================================================
	//	FIN FILTROS DE CONSULTA
	//====================================================================================
	
	//====================================================================================
	//	REPORTE
	//====================================================================================
	if (isset($wcco) && isset($wser) && isset($activo) && isset($wfec_i) && isset($wfec_f)&& isset($wtipo) && $wfec_f>=$wfec_i ) // si ya seleccionaron los parametros para consultar
	{  


		$estadosede=consultarAliasPorAplicacion($conexion, $wemp_pmla, "filtrarSede");
		$sFiltroSede="";
		$codigoSede = '';
		if($estadosede=='on')
		{	  
			$codigoSede = (isset($selectsede)) ? $selectsede : consultarsedeFiltro();
			$sFiltroSede = (isset($codigoSede) && ($codigoSede != '')) ? " AND Ccosed = '{$codigoSede}' " : "";
		}	
		

		//--------------------------
		// Consulta de datos
		//--------------------------
		$wcco=explode("-",$wcco);
		$wcco=trim($wcco[0]);
		
		$wdatos_rol = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
		$winf_nutricion_dsn = explode("-", $wdatos_rol);		
		
		$condicion_cco = " AND  Movcco = '".trim($wcco)."' ";
		if( $wcco == '%' )
			$condicion_cco = "";
			
		$condicion_servicio = " AND	Movser = '".trim($wser)."' ";
		if( $wser == '%' )
			$condicion_servicio = "";
			
		//if ($wacc=='% - TODAS')
		//	$wacc="%";
		if (!isset($tipo_estados))
			$tipo_estados='%';
			
		$q_principal = "SELECT  movfec, movhis, moving, movdie, movcco, movobs, movint, movdsn, movods, movnut, movhab, movmpo, movser, movest, B.id, pacno1, pacno2, pacap1, 
								pacap2, pacnac, ccocod, cconom, sernom, A.hora_data as hora_accion, Audacc, pacced, pactid  
						  FROM  ".$wbasedato."_000077 B, ".$wbasedato."_000078 as A,  ".$wbasedato."_000011, root_000036, root_000037
								, ".$wbasedato."_000076
						 WHERE	( B.Fecha_data between '".$wfec_i."' AND '".$wfec_f."'  )
						 AND  B.Fecha_data = A.Fecha_data
						  AND  Movhis = Audhis
						   AND  Moving = Auding 
						   AND  Movser = Audser 
						  ".$condicion_cco."
						   ".$condicion_servicio."						   
						   AND 	Movcco = Audcco
						   AND  Movser = Sercod
						   {$sFiltroSede}";
						   
						if ($impresas=='on'){
		$q_principal.=	"  AND  Movimp != '".$impresas."'";		
						}
						if ($activo != ''){
		$q_principal.=	"  AND  Movest = '".$activo."'";	
						}
		
		/*$q_principal.="  AND  Audacc LIKE '".$wacc."'
						   AND  Audacc = Estdes
						   AND 	Esttes LIKE '".$tipo_estados."'";*/
					if ($wtipo != '') //Si es diferente a todos
					{
						if ($wtipo == 'pedido')
		$q_principal.=	"	AND  B.Hora_data <= Serhfi ";	//que se haya solicitado en el horario de pedido				
						else
		$q_principal.=	"	AND  B.Hora_data > Serhia ";	//que se haya solicitado en el horario de adicion					
					}
		$q_principal.=	"  AND  movhis = orihis
						   AND  moving = oriing
						   AND  oriori = '".$wemp_pmla."'
						   AND  oriced = pacced 
						   AND  oritid = pactid
						   AND  Movcco = Ccocod
					  GROUP BY  movhis, movser, movcco	   
					  ORDER BY  movfec ASC, movser, movcco, movhab
						";
		//echo $q_principal;
		$res_principal = mysql_query($q_principal,$conex) or die ("Error: ".mysql_errno()." - en el query(Principal): ".$q_principal." - ".mysql_error());
		$num_principal = mysql_num_rows($res_principal);
		//--------------------------
		// Fin Consulta de datos
		//--------------------------
		//------------------------
		// Pinta reporte
		//------------------------
		if ($num_principal>0)
		{
			//-------------------------------------------------
			//	Div apartir del cual se relizara la impresion
			//-------------------------------------------------	
			echo "<div id='seleccion'>";
			//--------------------------
			// Recorrer los resultados
			//--------------------------
			$wccoant = '';
			$wser_ant = '';
			$fecha_anterior = '';
			$wclass = "fila1";
			$id_77 = '';
			while ($row_principal = mysql_fetch_array($res_principal))
			{
				$wtpa = "";
				$wcolorpac = "";
				
				if ($wser_ant != $row_principal['sernom'])
				{
					if ($wccoant!='')
					{
						//Cierro el ultimo cuadro pintado y pinto la hora y fecha de impresion
						echo "</table><br>";
					}
					if ($row_principal["movfec"] != $fecha_anterior) // Si la fecha cambia: pinto un encabezado de la nueva fecha
					{
						echo '<div align=left style="color: #CC0000;font-family: verdana ;font-size: 15;" ><b>Fecha del Pedido: '.$row_principal["movfec"].'</b></div>';
						$fecha_anterior = $row_principal["movfec"]; 
					}
					//Nombre del servicio:
					echo '<div style="color: #000000;font-family: verdana ;background-color: #F7F8E0;font-size: 22;" >
							'.$row_principal["sernom"].'
						</div>';
					$wser_ant = $row_principal['sernom'];
					$wccoant = '0000';
				}
					

				if ($wccoant != $row_principal['ccocod']) //Si el centro de costos cambia: Pinto el nombre del centro de costos
				{
					//Pinto el encabezado	
					echo "<table>";
						echo "<tr>
								<td align=left bgcolor=#ffffff ><font size=2 text color=#CC0000><b>".$row_principal['ccocod']." - ".$row_principal['cconom']."</b></font></td>
							</tr>";
					$wccoant=$row_principal['ccocod'];
					 echo "</table>";
						
					echo "<table width =100%>";
					echo "<tr class='encabezadoTabla'>";
                        echo "<th></th>";
						echo "<th>Hab</th>";
						echo "<th>Edad</th>";
						echo "<th>Paciente</th>";
						echo "<th>Patrón</th>";
						echo "<th>Afin</th>";
						echo "<th>Media<br>porción</th>";
						echo "<th>Detalle</th>";
						echo "<th>Observaciones</th>";					 
						echo "<th>Historia de<br>Intolerancias</th>";
						echo "<th>Observacion<br>Nutricionista</th>";		
						echo "<th>Acción</th>";
					echo "</tr>";
				}
				
				if ($wclass=="fila2")
					$wclass="fila1";
	            else
					$wclass="fila2";

				$paciente = $row_principal['pacno1']." ".$row_principal['pacno2']."<br>".$row_principal['pacap1']." ".$row_principal['pacap2'];
				//$wedad = calcularAnioMesesDiasTranscurridos($row_principal['pacnac']);	
				$media_porcion = $row_principal['movmpo'];
                $whistoria = $row_principal['movhis'];
                $wingreso = $row_principal['moving'];
				$cedula = $row_principal['pacced'];
				$tipo_id = $row_principal['pactid'];
                
				$wafin = clienteMagenta($cedula,$tipo_id,$wtpa,$wcolorpac);
				
                $wtipopos = consultartipopos($whistoria, $wingreso, $wser);
                
                if ($wtipopos == 'on')
                {
                    $wpos = 'P';
                }
                else
                {
                 $wpos = '';   
                }
                
				//------------------------------------------------------------
				//Consultar si existen movimientos posteriores al actual
				//------------------------------------------------------------
				// $q_ultimo = " SELECT Audacc, MAX(id) as id "
							// ."  FROM ".$wbasedato."_000078 "
							// ." WHERE audhis 	= 	'".$row_principal['movhis']."'"
							// ."   AND auding 	= 	'".$row_principal['moving']."'"
							// ."   AND audser 	= 	'".$row_principal['movser']."'"
							// ."   AND Fecha_data = 	'".$row_principal['movfec']."'"
							// ."	 AND Hora_data 	>	'".$row_principal['hora_accion']."'" //Que sea posterior a la accion que se esta consultando
							// ."	 AND Audcco		=	'".$row_principal['movcco']."'"
							// ." GROUP BY id "
							// ." ORDER BY id DESC ";
						
				// $res_ultmo = mysql_query($q_ultimo,$conex) or die ("Error: ".mysql_errno()." - en el query: (Ultima accion auditoria) ".$q_ultimo." - ".mysql_error());
				// $num_ultimo = mysql_num_rows($res_ultmo);
				// if($num_ultimo>0)
				// {
					// $row_ultimo = mysql_fetch_array($res_ultmo);
					
					// if ($row_ultimo['Audacc'] != $row_principal['Audacc']) //Esto es para evitar la situacion de que se pinte: Accion=Pedido, Ultimo Movimiento=Pedido
					// {
						// $ultimo_mov = $row_ultimo['Audacc'];
						// //---------------------------------------------------------------------------------------------------------------------------------------
						// // EXCEPCION 2: Si el pedido esta cancelado y el ultimo movimiento es una modificacion de intolerancia u observacion;
						// // Esto lo hago porque cuando cancelan un pedido y luego insertan una observacion o intolerancia el ultimo registro en la tabla de auditoria 
						// // es 'Modifico Observacion' o 'intolerancia', entonces el patron saldra con este estado y no se visualizara el 'Cancelado', 
						// // que es lo que realmente se necesita visualizar.
						// //---------------------------------------------------------------------------------------------------------------------------------------		  
						// if ( $row_principal['movest']=='off' and ($ultimo_mov =='MODIFICO OBSERVACION' or $ultimo_mov == 'MODIFICO INTOLERANCIA' or $ultimo_mov == 'MODIFICO OBSERVACION DSN')) 
						// {
							// //Valido que exista un registro de 'cancelado' en la auditoria
							// $q3 = "SELECT Audacc "
								// ."   FROM ".$wbasedato."_000078 "
								// ."  WHERE audhis = '".$row_principal['movhis']."'"
								// ."    AND auding = '".$row_principal['moving']."'"
								// ."    AND audser = '".$row_principal['movser']."'"
								// ."    AND fecha_data = '".$row_principal['movfec']."'"
								// ."    AND Audacc = 'CANCELADO' ";
								
							// $resaud3 = mysql_query($q3,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q3." - ".mysql_error());
							// $rowaud3 = mysql_fetch_array($resaud3);
							// $num_rowaud3 = mysql_num_rows($resaud3);
							// if ($num_rowaud3>0)
							// {
								// $ultimo_mov= $rowaud3['Audacc'];
							// }
						// }
						// //-------------------------------------------
						// // FIN EXCEPCIONES
						// //-------------------------------------------
					// }
					// else
					// {
						// $ultimo_mov='';
					// }
				// }
				// else
				// {
					// $ultimo_mov='';
				// }
				//----------------------------------------------------
				// Fin movimiento posteriores
				//----------------------------------------------------
				
				//----------------------------------------------------
				// Consultar el detalle si hay servicios individuales
				//----------------------------------------------------
				// Debo mirar si entre los patrones que le programaron al patron existe alguno 
				// que sea de servicio inidvidual para consultarle el detalle.	
				$estado = $row_principal['Audacc'];
				$wdetalle="";
				$patron_con_productos="";
				$wpatronppal = $row_principal['movdie'];
				
				if (strpos($row_principal['movdie'],","))   
				{
					$wpatron=explode(",",$row_principal['movdie']);
					foreach($wpatron as $valor_patr)	//recorro todos los patrones
					{
						if (servicio_individual($valor_patr))
							$patron_con_productos=$valor_patr;
					}
				}
				else
				{
					if (servicio_individual($row_principal['movdie']))
						$patron_con_productos=$row_principal['movdie'];
					
				}
				if (isset($patron_con_productos) && $patron_con_productos!='')
				{
					$q = " SELECT Prodes, Detcan "
					  ."   FROM ".$wbasedato."_000084, ".$wbasedato."_000082 "
					  ."  WHERE detfec = '".$row_principal['movfec']."'"
					  ."    AND dethis = '".$row_principal['movhis']."'"
					  ."    AND deting = '".$row_principal['moving']."'"
					  ."    AND detser = '".$row_principal['movser']."'"
					  ."    AND detpat = '".$patron_con_productos."'";
					  //."	AND detcco = '".$cco_actual."'"
					if ($row_principal['movest']=='on')
					{
						$q.="    AND detest = 'on' ";
					}
					else
					{
						//Esta validacion es para diferenciar una cancelacion por registro de altas de una cancelacion normal del pedido
						if($estado =='CANCELACION X ALTA' || $estado == 'CANCELACION X MUERTE')  
						{
							$q.="   AND detest = 'off' 
									AND detcal = 'on'	";
						}
						else
						{
							$q.="   AND detest = 'off'";
						}
					}
					$q.="    AND Procod = detpro";
					$res_detalle = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query:(consultar detalle) ".$q." - ".mysql_error());
					$num_detalle = mysql_num_rows($res_detalle);
					for ($k=1;$k<=$num_detalle;$k++)
					{
						//wcolor_detalle="FF7F00";  //Naranja
						$row_detalle=mysql_fetch_array($res_detalle);
						$nombre_producto=$row_detalle['Prodes'];
						$cantidad=$row_detalle['Detcan'];
						
						if (trim($wdetalle) != "")
							$wdetalle=$wdetalle."<br>".$cantidad."-".$nombre_producto;
						else
							$wdetalle=$cantidad."-".$nombre_producto;
					}
				}
				//----------------------------------------------------
				// Fin Consultar el detalle 
				//----------------------------------------------------
				
				//------------------------------------------------------------------------------------------------------------------------------------
				// Este hidden es para armar id de los registros, para cuando se de click en el boton de impresion marcarlos como impresos (Movimp=on) 
				//------------------------------------------------------------------------------------------------------------------------------------
				if (!isset($id_77))
					$id_77=$row_principal['id'];
				else
					$id_77.='|'.$row_principal['id'];
				//------------------------------------------------------------------------------------------------------------------------------------
				//calcular edad
				//------------------------------------------------------------------------------------------------------------------------------------
				$fecha_nacimiento = $row_principal['pacnac'];
				$row_fecha_nacimiento = explode('-', $fecha_nacimiento);
				$fecha_nacimiento = $row_fecha_nacimiento[2].'/'.$row_fecha_nacimiento[1].'/'.$row_fecha_nacimiento[0]; // pasar fecha de formato 2012-10-09 a formato 09/10/2012
				$edad = tiempo_transcurrido($fecha_nacimiento, date('d/m/Y'));
				//------------------------------------------------------------------------------------------------------------------------------------
				
				//Si el patron es DSN, imprimira el patron asociado. Jonatan Lopez Aguirre 10 de Abril 2014.
				if($wpatronppal == $winf_nutricion_dsn[1]){					
					$row_principal['movdsn'] = $row_principal['movdsn'];					
				}else{				
					$row_principal['movdsn'] = "";				
				}
				
				if( $row_principal['movdsn'] != ""){
					$row_principal['movdie'].=" <br>(".$row_principal['movdsn'].")";
				}			
				separarTexto( $ultimo_mov );
				separarTexto( $row_principal['Audacc'] );
				//$ultimo_mov = str_replace(" ", "<br>", $ultimo_mov);
				//$row_principal['Audacc'] = str_replace(" ", "<br>", $row_principal['Audacc']);
				$row_principal['movdie'] = str_replace(",",",<br>",$row_principal['movdie']);
				
				if( $row_principal['movnut'] != "" ){
					$row_principal['movnut'] = consultarNombreUsuario( $row_principal['movnut'] );				
				}
				
				if($row_principal['movnut'] != ''){
					$row_principal['movods'].= "<br><b>(".($row_principal['movnut']).")</b>";
				}
				echo "<tr>";  
                echo "<td class='".$wclass."' align=center width = '2%' >".(($wtipopos == 'on') ? '<font text color=#CC0000><b>(P)</b></font> ' : '')."</td>"; 							//** Habitacion
			    echo "<td class='".$wclass."' align=center width = '5%' >".$row_principal['movhab']."</td>"; 							//** Habitacion
			    echo "<td class='".$wclass."' width = '5%' nowrap='nowrap'>".$edad[0].' Años,<br>'.$edad[1]." Meses</td>";                      																									//** Edad
			    echo "<td class='".$wclass."' width = '13%' nowrap='nowrap'><b>".ucwords(strtolower($paciente))."</b></td>";
                echo "<td class='".$wclass."' width = '3%'  style='text-align:justify'>".$row_principal['movdie']."</td>"; 
				echo "<td class='".$wclass."' width = '3%'  style='text-align:justify'><font color=".$wcolorpac."><b>".$wtpa."</b></font></td>";
	            echo "<td class='".$wclass."' width = '3%'  align='center' >".(($media_porcion != '')? $media_porcion : '&nbsp;')."</td>";  																		//** Patrones
				echo "<td class='".$wclass."' width = '10%' >".(($wdetalle != '') ? ucwords($wdetalle) : '&nbsp;')."</td>";  																		//** Media porcion
				echo "<td class='".$wclass."' width = '15%' style='text-align:justify'>".(($row_principal['movobs'] != '') ? $row_principal['movobs'] : '&nbsp;')."</td>";  										//** Observaciones 
				echo "<td class='".$wclass."' width = '15%' style='text-align:justify'>".(($row_principal['movint'] != '') ? $row_principal['movint'] : '&nbsp;')."</td>";  										//** Intolerancias
				
				//Si el patron es DSN, imprimirá la observacion DSN. Jonatan Lopez Aguirre 10 de Abril 2014.
				if($wpatronppal == $winf_nutricion_dsn[1]){					
					$row_principal['movods'] = $row_principal['movods'];					
				}else{				
					$row_principal['movods'] = "";				
				}
				
				echo "<td class='".$wclass."' width = '15%' style='text-align:justify'>".(($row_principal['movods'] != '') ? $row_principal['movods'] : '&nbsp;')."</td>";  										//** Observaciones 
				echo "<td class='".$wclass."' width = '8%' style='text-align:justify'>".(($row_principal['Audacc'] != '') ? ucwords(strtolower($row_principal['Audacc'])) : '&nbsp;')."</td>";  	//** Ultimo Movimiento
				
				echo "</tr>";     
			}
			echo "</table><br>";
			
			echo "<input type='HIDDEN' name='id_77' id='id_77' value='".$id_77."'>";
			fecha_y_hora_impresion ();
			//--------------------------
			// Fin Recorrer 
			//--------------------------
			//-------------------------------------------------
			//	Cierro Div impresion
			//-------------------------------------------------
			echo "</div>"; 
			//-------------------------------------------------
		}
		else
		{
			echo "<br><div style='color: #676767;font-family: verdana;background-color: #E4E4E4;width:55%;' >
					No se encontraron resultados.<br />Intente con otros datos de consulta.
				</div><br>";
		}
	}
	else
	{
		if($wfec_i>$wfec_f)
		{
			echo "<script type='text/javascript'>
			alert ('La fecha inicial NO puede ser mayor a la final');
			</script>	";
		}
	}
	echo "</form>";  
	echo "<br>";
	echo "<table>"; 
	echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	echo "</table>";
	echo "<script>";
	echo "$.unblockUI();";
	echo "</script>";
?>
	</body>
	</html>
<?php
} //Si no existen consultas ajax
