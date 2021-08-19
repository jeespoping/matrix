<?php
	// ------------------------------------------------------------------------	
	/*
			EXTRACCION DE DATOS PARA EL CONJUNTO MINIMO BASICO DE DATOS
	*/	
	// ------------------------------------------------------------------------
	
	$wactualiz = '31-Ago-2020';	
	include_once("conex.php");
	include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
	
	// --> Consultar ultima programacion
	$sqlS = "
	SELECT Detval
	  FROM root_000051
	 WHERE Detemp = '01'
	   AND Detapl = 'ejecucionCronETL_GRD'				 
	";
	$resS = mysqli_query($conex, $sqlS) or die("ERROR EN QUERY (sqlS):/n MENSAJE:".mysqli_error($conex));
	if($rowS = mysqli_fetch_array($resS))
		$rJson = json_decode($rowS['Detval'], true);
	else
		$rJson = array();
	
	// foreach($_POST as $nP => $vP)
		// $$nP = trim($vP);
	
	// --> Pintar formulario
	if(!isset($accionPost) || $accionPost == ""){
		
		if(!isset($_SESSION['user']))
		{
			echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
						[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
					</div>';
			return;
		}
		
		?>
		<html>
			<head>
				<script src="../../../include/root/jquery.min.js"></script>
				<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
				<script src="../../../include/root/bootstrap.min.js"></script>
				
				<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
				<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
				<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>		
				
				<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
			</head>
			<script type="text/javascript">
				$(function(){
					// --> Parametrización del datapicker
					cargar_elementos_datapicker();		
					$("#fechaIn").datepicker({
						maxDate: +0,
						onSelect: function(){
							var d = new Date($("#fechaIn").val());
							d.setDate(d.getDate()+31);
							mes = d.getMonth()+1;
							mes = (mes < 10) ? "0"+mes : mes;
							dia = (d.getDate() < 10) ? "0"+d.getDate() : d.getDate();
							fechaFin = d.getFullYear()+"-"+mes+"-"+dia;
							
							$("#fechaFi").val(fechaFin);
							$("#fechaFi").datepicker( "option", "minDate", $("#fechaIn").val());
							$("#fechaFi").datepicker( "option", "maxDate", fechaFin);						
							
						}
					});
					$("#fechaFi").datepicker({
						maxDate: +31,
						minDate: -0
					});
					$("#fechaPo").datepicker({
						minDate: +1,
					});					
				});
				//--------------------------------------------------------
				//	--> Activar datapicker
				//---------------------------------------------------------
				function cargar_elementos_datapicker()
				{
					$.datepicker.regional['esp'] = {
						closeText: 'Cerrar',
						prevText: 'Antes',
						nextText: 'Despues',
						monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
						'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
						monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
						'Jul','Ago','Sep','Oct','Nov','Dic'],
						dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
						dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
						dayNamesMin: ['D','L','M','M','J','V','S'],
						weekHeader: 'Sem.',
						dateFormat: 'yy-mm-dd',
						yearSuffix: ''
					};
					$.datepicker.setDefaults($.datepicker.regional['esp']);
				}
				//--------------------------------------------------------
				//	--> Guardar programacion
				//---------------------------------------------------------
				function guardarProg(){
					
					$.post("ETL_GRD.php",
					{
						consultaAjax	: '',
						accionPost		:   'guardarProg',
						fechaIn			:	$('#fechaIn').val(),
						fechaFi			:	$('#fechaFi').val(),
						fechaPo			:	$('#fechaPo').val()

					}, function(respuesta){
						$("#spanProg").html("<b>Programacion actual:</b> Periodo: "+$('#fechaIn').val()+" al "+$('#fechaFi').val()+" | Ejecucion: "+$('#fechaPo').val());
						alert(respuesta);
					});	
					
				}
			</script>
			<body>
				<?php encabezado("Extractor de CMBD", $wactualiz, 'clinica'); ?>
				<meta charset="UTF-8">
				<div class="container" style="width:50%;padding:0px;" align="left" border>
					<div id="divMsj" class="" role="alert" style="width:90%;display:none;padding:0px;">						
					</div>
					<div class="form-inline text-left" style="width:100%; padding:0px;">
						<br>
						<form class="form-inline text">
							<table class="table small table-bordered table-condensed" style="width:100%; padding:0px;" class="small">
								<tr>
									<td class="text-center bg-success" colspan='2' style="width:100%; padding:0px;">										
										<b>Extractor de CMBD (Conjunto M&iacute;nimo B&aacute;sico de Datos) para el GRD:</b><br>
										Seleccione el periodo a extraer y la fecha a ejecutar la extracci&oacute;n.<br>										
									</td>
								</tr>
								<tr>
									<td class="text-center">										
										<b>Fecha Inicial: &nbsp;&nbsp;</b>
										<div class="input-group">
											<div class="input-group-addon">
												<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
											</div>
											<input type="text" style="cursor:pointer" readonly class="form-control input-sm" id="fechaIn" 	value="<?php echo date("Y-m-d") ?>">
										</div>										
									</td>
									<td class="text-center">										
										<b>Fecha Final: &nbsp;&nbsp;</b>
										<div class="input-group">
											<div class="input-group-addon">
												<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
											</div>
											<input type="text" style="cursor:pointer" readonly class="form-control input-sm" id="fechaFi" 	value="<?php echo date("Y-m-d") ?>">
										</div>										
									</td>
								</tr>
								<tr>
									<td class="text-center" colspan='2'>	
										<b>Programar ejecuci&oacute;n para el: &nbsp;&nbsp;</b>
										<div class="input-group">
											<div class="input-group-addon">
												<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
											</div>
											<input type="text" style="cursor:pointer" readonly class="form-control input-sm" id="fechaPo" 	value="<?php echo date("Y-m-d",strtotime($fecha_actual."+ 1 day")) ?>">
										</div>
									</td>
								</tr>								
								<tr>
									<td class="text-center" colspan='4' style="width:100%; padding:6px;">										
										<button type="button" class="btn btn-primary btn-sm" onClick="guardarProg()">Guardar <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
									</td>
								</tr>
								<tr>
									<td class="text-center bg-info" colspan='4' style="width:100%; padding:0px;">
										<span id="spanProg">
											<b>Programaci&oacute;n actual:</b> Periodo:<?php echo $rJson['periodoConsulta']['fechaIn']." al ".$rJson['periodoConsulta']['fechaFi']." | Ejecuci&oacute;n: ".$rJson['fechaNuevaEjecucion']; ?>
										</span>
										<br>
										Ultimo archivo generado
										<a href="GRD.xls"><span class="btn-sm glyphicon glyphicon-folder-open text-default" aria-hidden="true" ></span></a>
										Ejecutado el: <?php echo $rJson['fechaUltimaGeneracion'].", ".$rJson['tiempoEjecucion']; ?>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</body>
		</html>
		<?php		
	}
	else
	{
		switch($accionPost){
			
			case 'guardarProg':
			{			
				$rJson['periodoConsulta']['fechaIn'] 	= $fechaIn;
				$rJson['periodoConsulta']['fechaFi'] 	= $fechaFi;
				$rJson['fechaNuevaEjecucion']			= $fechaPo;
				
				$sqlG = "
				UPDATE root_000051
				   SET Detval = '".json_encode($rJson)."'
				 WHERE Detemp = '01'
				   AND Detapl = 'ejecucionCronETL_GRD'				 
				";
				mysqli_query($conex, $sqlG) or die("ERROR EN QUERY (sqlG):/n MENSAJE:".mysqli_error($conex));
				
				echo "Cambios guardados";
				
				break;
				return;
			}
			case 'ejecutarETL':
			{
				$horaIE	 = date("H:i:s");
				$fechaIn = $rJson['periodoConsulta']['fechaIn'];	
				$fechaFi = $rJson['periodoConsulta']['fechaFi'];
				
				if($rJson['fechaNuevaEjecucion'] != date("Y-m-d"))
					return;
				
				$dateA 	= new DateTime($fechaIn);
				$dateB 	= new DateTime($fechaFi);
				$diffD 	= $dateA->diff($dateB);
				if($diffD->days > 31){			
					echo "--> Maximo 31 dias de consulta";
					return;
				}
					
				
				// --> Conex costos
				$parametros 			= new stdClass();
				// $parametros->ipMatrix	= '132.1.18.12';
				$parametros->ipCostos	= '132.1.18.80';
				$parametros->us			= 'root';
				$parametros->pa			= 'q6@nt6m';
				$parametros->bd			= 'matrix';
				
				// --> Matrix
				// $conex = @mysqli_connect($parametros->ipMatrix, $parametros->us, $parametros->pa, $parametros->bd);
				
				// if(!$conex){
					// echo "Conexion fallida a ".$parametros->ipMatrix.": ".mysqli_connect_error();
					// return;
				// }
				
				// --> Costos
				$conexCo = @mysqli_connect($parametros->ipCostos, $parametros->us, $parametros->pa, $parametros->bd);
				
				if(!$conexCo){
					echo "Conexion fallida a ".$parametros->ipCostos.": ".mysqli_connect_error();
					return;
				}
				
				// --> Conex a unix:
				$conexUnix = odbc_connect('facturacion','informix','sco');
				
				//$fechaIn = '2018-12-01';
				//$fechaFi = '2018-12-31';
				
				// --> Traer maestro de empresas con el plan(BD costos), para evitar hacer cada query dentro del ciclo
				$arrMaeEmpresas = array();
				$sqlMaeEmpresas = "
				SELECT Epmcod, Segcod, B.id
				  FROM costosyp_000061 AS A INNER JOIN costosyp_000045 AS B ON(Empseg = Segcod)
				 WHERE Empemp = '01' 
				";
				$resMaeEmpresas = mysqli_query($conexCo, $sqlMaeEmpresas) or die("ERROR EN QUERY (sqlMaeEmpresas):/n MENSAJE:".mysqli_error($conex));
				while($rowMaeEmpresas = mysqli_fetch_array($resMaeEmpresas, MYSQLI_ASSOC)){
					$arrMaeEmpresas[trim($rowMaeEmpresas['Epmcod'])] = trim($rowMaeEmpresas['Segcod']); 
				}
					
				
				
				$datos = array();
				
				// --> Traer datos demograficos
				$sql1 = "
				SELECT Pactdo Tipodoc, Pacdoc Doc, Pachis HC, Ingnin Ing, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS Nombres,
					   Pacfna Fnacim, Pacsex Sexo, Pacest EstCivil, Pacpah Pais, Pacdeh Dpto, Pacmuh Mpio, Pacbar Barrio, Paczon Zona, Pacned Escolaridad, 
					   Pacofi Ocup, Pactus CobSalud, Pactaf TipoAfiliacion, Ingcai TipoAtencion, '' Pesonac, '' Nitpag1,
					   '' Plan1, Ingfei Fing, Inghin HoraIng, Ingsei Serving, IF(Ingsei = '1130' , 'U', 'P') Tipoing, Egrfee Fegr,
					   Egrhoe HoraEgr, Sercod Servegr, Egrcae Alta
				  FROM cliame_000108 AS A INNER JOIN cliame_000112 AS B ON(Egrhis = Serhis AND Egring = Sering AND Seregr = 'on') 
					   INNER JOIN movhos_000011 AS C ON(Sercod = Ccocod  AND Ccohos = 'on')
					   INNER JOIN cliame_000101 AS D ON(Egrhis = Inghis AND Egring = Ingnin)
					   INNER JOIN cliame_000100 AS E ON(Inghis = Pachis)
				 WHERE A.Egrfee BETWEEN '".$fechaIn."' AND '".$fechaFi."'
				";
				//AND	A.Egrhis = '708448' 
				$res1 = mysqli_query($conex, $sql1) or die("ERROR EN QUERY (sql1):/n MENSAJE:".mysqli_error($conex));
				while($rowSql1 = mysqli_fetch_array($res1, MYSQLI_ASSOC)){
					$datos[] = $rowSql1;
				}
				
				foreach($datos as $keyDat => $valDat){
					
					// --> Limpiar ocupacion
					$ocupacion = trim($datos[$keyDat]['Ocup']);
					$datos[$keyDat]['Ocup'] = (($ocupacion == 'Empleado') ? '9999' : $ocupacion);
					
					// --> Limpiar Alta
					$priCar = substr(trim($datos[$keyDat]['Alta']), 0, 1);
					if($priCar == "+" || $priCar == "-")
						$datos[$keyDat]['Alta'] = $priCar;
					
					if(trim($datos[$keyDat]['TipoAfiliacion']) == "")
						$datos[$keyDat]['TipoAfiliacion'] = "9";
					
					// --> Calcular edad en dias		
					$date1 	= new DateTime($valDat['Fnacim']);
					$date2 	= new DateTime($valDat['Fing']);
					$diff 	= $date1->diff($date2);
					
					// --> Si la edad al ingreso es menor o igual a 28 días, obtener perso al nacer
					if($diff->days <= 28){
						$sqlPeso = "
						SELECT movdat
						  FROM hce_000366		       
						 WHERE movhis = '".$valDat['HC']."'
						   AND moving = '".$valDat['Ing']."'
						   AND movcon = '57'
						   AND movdat != ''
						 ORDER BY Fecha_data
						";
						$resPeso = mysqli_query($conex, $sqlPeso) or die("ERROR EN QUERY (sqlPeso):/n MENSAJE:".mysqli_error($conex));
						if($rowPeso = mysqli_fetch_array($resPeso)){
							// --> Ejm de como viene el dato: 1*2020-03-18|10:27:00|002-Masculino|3667|50|||
							$arrDatosPes = explode('|', $rowPeso['movdat']);
							$datos[$keyDat]['Pesonac'] = trim($arrDatosPes[3]);
						}
						
						if($datos[$keyDat]['Pesonac'] == "")
							$datos[$keyDat]['Pesonac'] = '2500';
					}
					
					// --> Obtener diagnosticos (Hasta 30)
					$d = 2;
					$sqlDiag = "
					SELECT Diacod, Diatip, Descripcion, Diamed, CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) AS NomMed,
						   Diaesm, Espnom	
					  FROM cliame_000109 INNER JOIN root_000011 ON(Diacod = Codigo)
						   LEFT JOIN movhos_000048 ON(Diamed = Meddoc)
						   LEFT JOIN movhos_000044 ON(Diaesm = Espcod)
					 WHERE Diahis = '".$valDat['HC']."' 
					   AND Diaing = '".$valDat['Ing']."'
					 GROUP BY Diacod
					 ORDER BY Diatip
					";
					$resDiag = mysqli_query($conex, $sqlDiag) or die("ERROR EN QUERY (sqlDiag):/n MENSAJE:".mysqli_error($conex));
					while($rowDiag = mysqli_fetch_array($resDiag, MYSQLI_ASSOC)){
						
						if($d > 30)
							break;
						 
						// --> Diagnostico principal
						if(trim($rowDiag['Diatip']) == 'P'){
							
							$datos[$keyDat]['DiagMuerte'] 		= ($datos[$keyDat]['Alta'] == "+" || $datos[$keyDat]['Alta'] == "-") ? $rowDiag['Diacod'] : ""; 
							$datos[$keyDat]['Dxppal'] 			= $rowDiag['Diacod']; 
							$datos[$keyDat]['NomDx1'] 			= $rowDiag['Descripcion']; 
							$datos[$keyDat]['MdEgreso'] 		= $rowDiag['Diamed']; 
							$datos[$keyDat]['NomMedDx1'] 		= $rowDiag['NomMed']; 
							$datos[$keyDat]['EspMdEgreso'] 		= $rowDiag['Diaesm']; 
							$datos[$keyDat]['NomEspecialidad'] 	= $rowDiag['Espnom']; 
						}
						else{
							$datos[$keyDat]['Diag'.$d] 			= $rowDiag['Diacod']; 
							$datos[$keyDat]['NomDx'.$d] 		= $rowDiag['Descripcion'];
							$d++;
						}
					}
					
					// --> Completo diagnosticos en blanco hasta 30
					while($d <= 30){
						$datos[$keyDat]['Diag'.$d] 			= ""; 
						$datos[$keyDat]['NomDx'.$d] 		= "";
						$d++;
					}
					
					// --> Obtener procedimientos (hasta 23)
					$p = 1;
					$sqlPro = "
					SELECT Procod, Nombre, Profec, Promed, Proser, Homco9  
					  FROM cliame_000110 LEFT JOIN root_000012 ON(Procod = Codigo)
						   LEFT JOIN sgc_000031 ON(Procod = Hompro)
					 WHERE Prohis = '".$valDat['HC']."'
					   AND Proing = '".$valDat['Ing']."'
					";
					$resPro = mysqli_query($conex, $sqlPro) or die("ERROR EN QUERY (sqlPro):/n MENSAJE:".mysqli_error($conex));
					while($rowPro = mysqli_fetch_array($resPro, MYSQLI_ASSOC)){
						if($p > 23)
							break;
						
						$datos[$keyDat]['Proced'.$p] 			= $rowPro['Procod']; 
						$datos[$keyDat]['NomProc'.$p] 			= $rowPro['Nombre']; 
						$datos[$keyDat]['CIE9-P'.$p] 			= $rowPro['Homco9']; 
						$datos[$keyDat]['Fproced'.$p] 			= $rowPro['Profec']; 
						$datos[$keyDat]['Docmd'.$p] 			= $rowPro['Promed']; 
						$datos[$keyDat]['Servproc'.$p] 			= $rowPro['Proser'];
						
						$p++;
					}
					
					// --> Completar procedimientos en blanco hasta 23
					while($p <= 23){
						$datos[$keyDat]['Proced'.$p] 			= ""; 
						$datos[$keyDat]['NomProc'.$p] 			= "";
						$datos[$keyDat]['CIE9-P'.$p] 			= "";
						$datos[$keyDat]['Fproced'.$p] 			= "";
						$datos[$keyDat]['Docmd'.$p] 			= "";
						$datos[$keyDat]['Servproc'.$p] 			= "";			
						$p++;
					}

					// --> Si tuvo ventilacion mecanica (movdat = 01-Ventilacion mecanica invasiva (VMI))
					$datos[$keyDat]['CIE9-VM'] = '';
					
					$sqlVen = "
					SELECT Fecha_data
					  FROM hce_000112		       
					 WHERE movhis = '".$valDat['HC']."'
					   AND moving = '".$valDat['Ing']."'
					   AND movcon = '12'
					   AND movdat LIKE '01-%'
					";
					//GROUP BY Fecha_data
					$resVen  = mysqli_query($conex, $sqlVen) or die("ERROR EN QUERY (sqlVen):/n MENSAJE:".mysqli_error($conex));
					$numDias = mysqli_num_rows($resVen);
					if($numDias > 0 && $numDias < 4)
						$datos[$keyDat]['CIE9-VM'] = '9671';
					elseif($numDias >= 4)
						$datos[$keyDat]['CIE9-VM'] = '9672';
						
					// --> Registro de infecciones (sgc_000031: Se carga manualmente, la info viene desde la BD de captacion de INVECLA)
					$i = 1;
					$sqlInf = "
					SELECT Inffec, Inftip, Infcla, Inforg
					  FROM sgc_000032		       
					 WHERE Infhis = '".$valDat['HC']."'
					   AND Infing = '".$valDat['Ing']."'
					 LIMIT 3
					";
					$resInf = mysqli_query($conex, $sqlInf) or die("ERROR EN QUERY (sqlInf):/n MENSAJE:".mysqli_error($conex));
					while($rowInf = mysqli_fetch_array($resInf, MYSQLI_ASSOC)){
						$datos[$keyDat]['Fiaas'.$i] 	= trim($rowInf['Inffec']); 
						$datos[$keyDat]['Tiaas'.$i] 	= trim($rowInf['Inftip']); 
						$datos[$keyDat]['Ciaas'.$i] 	= trim($rowInf['Infcla']); 
						$datos[$keyDat]['oeiaas'.$i] 	= trim($rowInf['Inforg']); 
						$i++;
					}
					
					while($i < 4){
						$datos[$keyDat]['Fiaas'.$i] 	= ''; 
						$datos[$keyDat]['Tiaas'.$i] 	= '';
						$datos[$keyDat]['Ciaas'.$i] 	= '';
						$datos[$keyDat]['oeiaas'.$i] 	= '';
						$i++;
					}
					
					// --> Número total de días estancia en la UCI
					$sqlDiasUci = "
					SELECT SUM(Dias_estan_serv) as EstanciaUCI
					  FROM movhos_000033 INNER JOIN movhos_000011 ON(Servicio = Ccocod AND Ccouci = 'on')
					 WHERE Historia_clinica = '".$valDat['HC']."' 
					   AND Num_ingreso 		= '".$valDat['Ing']."'
					";
					$resDiasUci = mysqli_query($conex, $sqlDiasUci) or die("ERROR EN QUERY (sqlDiasUci):/n MENSAJE:".mysqli_error($conex));
					if($rowDiasUci = mysqli_fetch_array($resDiasUci, MYSQLI_ASSOC))
						$datos[$keyDat]['EstanciaUCI'] = $rowDiasUci['EstanciaUCI'];
					else
						$datos[$keyDat]['EstanciaUCI'] = '';
					
					// --> Número total de días estancia en la UCE
					$sqlDiasUce = "
					SELECT SUM(Dias_estan_serv) as EstanciaUCE
					  FROM movhos_000033 INNER JOIN movhos_000011 ON(Servicio = Ccocod AND Ccouce = 'on')
					 WHERE Historia_clinica = '".$valDat['HC']."' 
					   AND Num_ingreso 		= '".$valDat['Ing']."'
					";
					$resDiasUce = mysqli_query($conex, $sqlDiasUce) or die("ERROR EN QUERY (sqlDiasUce):/n MENSAJE:".mysqli_error($conex));
					if($rowDiasUce = mysqli_fetch_array($resDiasUce, MYSQLI_ASSOC))
						$datos[$keyDat]['EstanciaUCE'] = $rowDiasUce['EstanciaUCE'];
					else
						$datos[$keyDat]['EstanciaUCE'] = '';
					
					// --> Número total de días estancia en URGENCIAS
					$sqlDiasUrg = "
					SELECT SUM(Dias_estan_serv) as EstanciaUrg
					  FROM movhos_000033 INNER JOIN movhos_000011 ON(Servicio = Ccocod AND Ccourg = 'on')
					 WHERE Historia_clinica = '".$valDat['HC']."' 
					   AND Num_ingreso 		= '".$valDat['Ing']."'
					";
					$resDiasUrg = mysqli_query($conex, $sqlDiasUrg) or die("ERROR EN QUERY (sqlDiasUrg):/n MENSAJE:".mysqli_error($conex));
					if($rowDiasUrg = mysqli_fetch_array($resDiasUrg, MYSQLI_ASSOC))
						$datos[$keyDat]['EstanciaUrg'] = $rowDiasUrg['EstanciaUrg'];
					else
						$datos[$keyDat]['EstanciaUrg'] = '';
					
					// --> Número total de días estancia en Hospitalizacion 
					$sqlDiasHos = "
					SELECT SUM(Dias_estan_serv) as EstanciaHosp
					  FROM movhos_000033 INNER JOIN movhos_000011 ON(Servicio = Ccocod AND Ccohos = 'on' AND Ccouci != 'on' AND Ccouce != 'on')
					 WHERE Historia_clinica = '".$valDat['HC']."' 
					   AND Num_ingreso 		= '".$valDat['Ing']."'
					";
					$resDiasHos = mysqli_query($conex, $sqlDiasHos) or die("ERROR EN QUERY (sqlDiasHos):/n MENSAJE:".mysqli_error($conex));
					if($rowDiasHos = mysqli_fetch_array($resDiasHos, MYSQLI_ASSOC))
						$datos[$keyDat]['EstanciaHosp'] = $rowDiasHos['EstanciaHosp'];
					else
						$datos[$keyDat]['EstanciaHosp'] = '';
					
					// --> Número total de días estancia en el ingreso 
					
					$sqlDiasTot = "
					SELECT Egrest 
					  FROM cliame_000108 
					 WHERE Egrhis = '".$valDat['HC']."' 
					   AND Egring = '".$valDat['Ing']."'
					";
					$resDiasTot = mysqli_query($conex, $sqlDiasTot) or die("ERROR EN QUERY (sqlDiasTot):/n MENSAJE:".mysqli_error($conex));
					if($rowDiasTot = mysqli_fetch_array($resDiasTot, MYSQLI_ASSOC))
						$datos[$keyDat]['TotalEstancia'] = $rowDiasTot['Egrest'];
					else
						$datos[$keyDat]['TotalEstancia'] = '';
					
					
					// --> Totalizar valores de facturacion por grupos de conceptos
					// --> Ayudas
					$gruposCon['FacAyudasDx'] 		= array('0003','0004','0005','0006','0098','0502','0503','0504','0505','0506','0511','0516','0530','0569','0600','0601','0603','0604','0605','0608','0611','0612','0613','1522','2045','2047','2051','2081');
					// --> Dispositivos medicos y medicamentos
					$gruposCon['FacMyMedic'] 		= array('0015','0163','0164','0165','0168','0169','0580','0616','0626','4116','4151','4160');
					// --> Derechos de sala
					$gruposCon['FacDerechoSala']	= array('0007','0022','0076','0512');
					// --> Estancia
					$gruposCon['Factestancia']		= array('0035','0137','0143','0167','2009');
					// --> Honorarios
					$gruposCon['FacHonorarios']		= array('0002','0024','0026','0029','0037','0039','0071','0072','0075','0079','0086','0101','0102','0110','0111','0117','0136','0150',
													   '0151','0155','0158','0159','0501','0507','0520','0521','0631','0635','0650','0672','0675','0676','2015','2034','2043','2048','2058','2059','2061',
													   '2066','2067','2082','2084','2090','2091','3003','3030','3031','3032','3033','3041','3042','4216');
					// --> Laboratorios
					$gruposCon['FacLaboratorio']	= array('0001','0122');
					// --> Uso de equipos
					$gruposCon['FacUsoEquipos']		= array('0008','0009','0034','0062','3010');
					// --> Otros
					$gruposCon['FacOtros']			= array('0010','0011','0017','0020','0023','0036','0040','0041','0042','0043','0135','0160','0166','0306','0571','0602','1525','1526','2025','2064','2085','2086');
					
					
					// --> Responsable del paciente (Desde Unix)
					$sqlRes = "
					SELECT egremp, egrcer
					  FROM INMEGR
					 WHERE egrhis = '".$valDat['HC']."' 
					   AND egrnum = '".$valDat['Ing']."'
					";
					$resRes = odbc_do($conexUnix, $sqlRes);          
					if(odbc_fetch_row($resRes))
						$datos[$keyDat]['Nitpag1'] = trim((odbc_result($resRes, 'egremp') == "P") ? '999999' : odbc_result($resRes, 'egrcer'));
					
					// --> Plan segun costos
					if($datos[$keyDat]['Nitpag1'] != '999999')
						$datos[$keyDat]['Plan1'] = $arrMaeEmpresas[$datos[$keyDat]['Nitpag1']];
					else
						$datos[$keyDat]['Plan1'] = 'PPN'; //PARTICULARES P. NATURAL
					
					// --> FACTURACION: Datos de desde UNIX
					$arrFacCon = array();
					$sqlFac = "
					SELECT cardetcon, SUM(cardettot) valor 
					  FROM FACARDET
					 WHERE cardethis = '".$valDat['HC']."' 
					   AND cardetnum = '".$valDat['Ing']."'
					   AND cardetcon NOT IN ('2101','2105','2106','0171') 
					   AND cardetvfa <> 0 
					   AND cardetfac = 'S' 
					   AND cardetanu = 0 
					GROUP BY cardetcon ";
					
					$resFac = odbc_do($conexUnix, $sqlFac);          
					while(odbc_fetch_row($resFac))
						$arrFacCon[odbc_result($resFac, 'cardetcon')] = odbc_result($resFac, 'valor');
					
					foreach($gruposCon as $nomGru => $valGru){
						$total = 0;
						foreach($valGru as $conGru){
							if(array_key_exists($conGru, $arrFacCon))
								$total+= $arrFacCon[$conGru]; 		
						}
						$datos[$keyDat][$nomGru] = $total;
					}
					
					$datos[$keyDat]['Factbruta'] = array_sum($arrFacCon);
					
					// --> Programa cardiovascular (1 = Paf sura, 2 = Paf Salud Total, 3 = Paf nueva, 4 = Paf sanitas, 9 = No aplica)
					$datos[$keyDat]['Pcar'] = '9';
					
					$empPaf = array('800088702CV' => 1, '800088702CS' => 1,
									'800130907CV' => 2, '800130907CS' => 2,
									'900156264CV' => 3, '900156264CS' => 3,
									'800251440CV' => 4);
					
					
					// --> Paquete paf (1=Cx, 2=Electrofis, 3=Hemodi, 4=Cx-Electro, 5=Cx-Hemodina, 6=Electro-Hemodina, 7=Cx-Electr-Hemodi, 9=No aplica)
					$datos[$keyDat]['PaquetePAFF'] 	= '9';
					$arrPaq = array("CX" => false, "HE" => false, "EL" => false);
					
					// --> Primero busco en el registro paf
					$sqlPaf = "
					SELECT responsable, fecha_Cx, fecha_Hemod, fecha_Electrof
					  FROM paf_000004
					 WHERE hc 				= '".$valDat['HC']."'
					   AND ingreso 			= '".$valDat['Ing']."'
					   AND (fecha_Cx != '' OR fecha_Hemod != '' OR fecha_Electrof 	!= '')
					";
					$resPaf = mysqli_query($conex, $sqlPaf) or die("ERROR EN QUERY (sqlPaf):/n MENSAJE:".mysqli_error($conex));
					while($rowPaf = @mysqli_fetch_array($resPaf, MYSQLI_ASSOC)){
						$resPaf = trim($rowPaf['responsable']);
						// --> Busco el nombre en la cadena, ya que en la tabla no se guarda el codigo
						if(stristr($resPaf, "sura") !== FALSE)
							$datos[$keyDat]['Pcar'] = '1';
						if(stristr($resPaf, "total") !== FALSE)
							$datos[$keyDat]['Pcar'] = '2';
						if(stristr($resPaf, "nueva") !== FALSE)
							$datos[$keyDat]['Pcar'] = '3';
						if(stristr($resPaf, "sanitas") !== FALSE)
							$datos[$keyDat]['Pcar'] = '4';
						
						if(array_key_exists($resPaf, $empPaf) !== FALSE)
							$datos[$keyDat]['Pcar'] = $empPaf[$resPaf];
						
						if($rowPaf['fecha_Cx'] != "")
							$arrPaq["CX"] = true;
						
						if($rowPaf['fecha_Hemod'] != "")
							$arrPaq["HE"] = true;
						
						if($rowPaf['fecha_Electrof'] != "")
							$arrPaq["EL"] = true;
					}
					
					// --> Segundo busco en los cargos grabados
					$sqlPafCar = "
					SELECT Empcod, Tcarser  
					  FROM cliame_000106 INNER JOIN cliame_000024 ON(Tcarres = Empcod)
					 WHERE Tcarhis = '".$valDat['HC']."'
					   AND Tcaring = '".$valDat['Ing']."'
					   AND Tcarser IN('1335', '1330', '1016') 
					   AND Tcarconcod = '0076'
					   AND Tcarest = 'on' 
					";
					$resPafCar = mysqli_query($conex, $sqlPafCar) or die("ERROR EN QUERY (sqlPafCar):/n MENSAJE:".mysqli_error($conex));
					while($rowPafCar = mysqli_fetch_array($resPafCar, MYSQLI_ASSOC)){
						
						if($datos[$keyDat]['Pcar'] == '9'){
							
							$resPaf = trim($rowPafCar['Empcod']);
							if(array_key_exists($resPaf, $empPaf))
								$datos[$keyDat]['Pcar'] = $empPaf[$resPaf];
						}
						
						if($rowPafCar['Tcarser'] == "1016")
							$arrPaq["CX"] = true;
						
						if($rowPafCar['Tcarser'] == "1330")
							$arrPaq["HE"] = true;
						
						if($rowPafCar['Tcarser'] == "1335")
							$arrPaq["EL"] = true;
					}
					
					// --> Paquete paf
					// --> Paquete paf (1=Cx, 2=Electrofis, 3=Hemodi, 4=Cx-Electro, 5=Cx-Hemodina, 6=Electro-Hemodina, 7=Cx-Electr-Hemodi, 9=No aplica)
					
					if($datos[$keyDat]['Pcar'] != '9'){
						if($arrPaq["CX"] && $arrPaq["HE"] && $arrPaq["EL"])
							$datos[$keyDat]['PaquetePAFF'] = '7';
						elseif($arrPaq["HE"] && $arrPaq["EL"])
								$datos[$keyDat]['PaquetePAFF'] = '6';
							elseif($arrPaq["CX"] && $arrPaq["HE"])
									$datos[$keyDat]['PaquetePAFF'] = '5';
									elseif($arrPaq["CX"] && $arrPaq["EL"])
										$datos[$keyDat]['PaquetePAFF'] = '4';
										elseif($arrPaq["HE"])
											$datos[$keyDat]['PaquetePAFF'] = '3';
											elseif($arrPaq["EL"])
												$datos[$keyDat]['PaquetePAFF'] = '2';
												elseif($arrPaq["CX"])
													$datos[$keyDat]['PaquetePAFF'] = '1';
					}
				
				}
				// --> Crear excel anterior
				// header("Content-Disposition: attachment; filename=\"GRD.xls\"");
				// header("Content-Type: application/vnd.ms-excel;");
				// header("Pragma: no-cache");
				// header("Expires: 0");
				// $out = fopen("php://output", 'w');		
				// --> Encabezado
				// fputcsv($out, array_keys($datos[0]),"\t");
				// --> Datos
				// foreach ($datos as $valores)
					// fputcsv($out, $valores,"\t");
			
				// --> Variables con formato numero
				$arrVarInt = array(	'Pesonac', 'EstanciaUCI', 'EstanciaUCE', 'EstanciaUrg', 'EstanciaHosp', 
									'TotalEstancia', 'FacAyudasDx', 'FacMyMedic', 'FacDerechoSala', 'Factestancia', 
									'FacHonorarios', 'FacLaboratorio', 'FacUsoEquipos', 'FacOtros', 'Factbruta');
				$arrVarFec = array('Fnacim', 'Fing', 'Fegr', 'Fiaas1', 'Fiaas2');
				
				$html =  "
				<style>
				table,td {
					border: 1px solid gray;
				}
				</style>
				<table style='border-collapse: collapse'>
					<tr>";
				// --> Encabezado
				if(is_array($datos[0]))
					foreach ($datos[0] as $nomCam =>$valores)
						$html.= "<td>".$nomCam."</td>";
				
				$html.= "</tr>";
				
				// --> Encabezado
				foreach ($datos as $arrDat){
					$html.= "<tr>";
					foreach ($arrDat as $nomVar => $dato){
						// --> Variables con formato numero
						if(in_array($nomVar, $arrVarInt)){
							$dato = (trim($dato) != "") ? ceil($dato) : $dato;
							$html.= "<td style='mso-number-format:0'>".$dato."</td>";
						}
						// --> Variables con formato fecha dd/mm/aaaa
						elseif(in_array($nomVar, $arrVarFec) ||  strpos($nomVar, 'Fproced') !== false){
							$html.= "<td style='mso-number-format:\"dd\/mm\/yyyy\"'>".$dato."</td>";	
						}
						// --> Variables con formato texto
						else{
							
							$html.= "<td style='mso-number-format:\"\@\"'>".$dato."</td>";
						}
					}
					$html.= "</tr>";
				}			
				$html.="
				</table>
				";
				
				// header("Content-Disposition: attachment; filename=\"GRD.xls\"");
				// header("Content-Type: application/vnd.ms-excel;");
				// header("Pragma: no-cache");
				// header("Expires: 0");
				// $out = fopen("php://output", 'w');

				$out = fopen("GRD.xls", 'w');
				fputcsv($out, array($html),"\t", " ");	
				fclose($out);
				
				// echo "<pre>";
				// print_r($datos);
				// echo "</pre>";	
				
				// --> Guardar registro de ejecucion
				$rJson['fechaUltimaGeneracion']	= date("Y-m-d");
				$rJson['tiempoEjecucion']		= "Inicio: ".$horaIE."  Fin: ".date("H:i:s");
				
				$sqlG = "
				UPDATE root_000051
				   SET Detval = '".json_encode($rJson)."'
				 WHERE Detemp = '01'
				   AND Detapl = 'ejecucionCronETL_GRD'				 
				";
				mysqli_query($conex, $sqlG) or die("ERROR EN QUERY (sqlG):/n MENSAJE:".mysqli_error($conex));
				
				
				odbc_close($conexUnix);
				mysqli_close($conexCo);
				unset($_POST);
				
				break;
				return;
			}
		}
	}
	mysqli_close($conex);
?>