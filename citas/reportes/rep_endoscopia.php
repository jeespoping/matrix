<?php
include_once("conex.php");
	session_start();
	

	include_once("root/comun.php");
	include_once("citas/funcionesAgendaCitas.php");
	$wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
	

	$conex = obtenerConexionBD("matrix");
	
	function getEncabezadoTabla($conex, $solucionCitas, $usaTurnero){
		
		$string .= "<tr class='encabezadoTabla'>
		<th align='center'>Documento</th>
		<th>Nombre</th>
		<th>Examen</th>
		<th>Hora cita</th>
		<th>Hora turno</th>";
		
		if(isset($usaTurnero) && $usaTurnero == "on"){
			$string .= "<th><b style='color:red'>*</b> Diferencia <br>Hora cita - Hora turno</th>
			<th><b style='color:red'>*</b> Diferencia<br>hora turno y atención</th>";
		}
			
		$sqlAc = "SELECT Prodes
				FROM citasen_000025 ac 
				WHERE Promer = 'on'
				AND Proest = 'on'";
			$resAc =  mysql_query($sqlAc, $conex);
			
			$cont = 0;
			$Act = "";
			$newAct = "";
			$arrAct = array();
			$arrCodes = array();
			$strCon = "";
			
			while ($row = mysql_fetch_assoc($resAc)){
				$string .= "<th>Duracion " . $row["Prodes"] ."</th>"; 
			}
		
		$string .="<th>Duracion total</th></tr>";
		echo $string;
	}
	
	function sumar($hora1, $hora2)
	{		
		$hora1 = conversorSegundos($hora1);
		$hora2 = conversorSegundos($hora2);
		$sum = $hora1 + $hora2;
		
		return conversorSegundosHoras($sum) ;	  
	}
	
	function RestarHoras($horaini,$horafin)
	{
		$horai=substr($horaini,0,2);

		$mini=substr($horaini,3,2);

		$segi=substr($horaini,6,2);

		$horaf=substr($horafin,0,2);

		$minf=substr($horafin,3,2);

		$segf=substr($horafin,6,2);

		$ini=((($horai*60)*60)+($mini*60)+$segi);

		$fin=((($horaf*60)*60)+($minf*60)+$segf);

		$dif=$fin-$ini;

		$difh=floor($dif/3600);

		$difm=floor(($dif-($difh*3600))/60);

		$difs=$dif-($difm*60)-($difh*3600);

		return date("H-i-s",mktime($difh,$difm,$difs));
	}

	function conversorSegundosHoras($tiempo_en_segundos)
	{		
		$tiempo_en_segundos = round($tiempo_en_segundos);
		$horas = floor($tiempo_en_segundos / 3600);
		$minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
		$segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);
	 
		// se les pone el 0 al principio
		$horas    = $horas >= 10 ? $horas : "0".$horas;
		$minutos  = $minutos >= 10 ? $minutos : "0".$minutos;
		$segundos = $segundos >= 10 ? $segundos : "0".$segundos;
		
		return $horas . ':' . $minutos . ":" . $segundos;
	}
	
	function conversorSegundos($hora, $seg = "")
	{
		list($horas, $minutos, $segundos) = explode(':', $hora);
		$hora_en_segundos = ($horas * 3600 ) + ($minutos * 60 ) + $segundos;
		
		return $hora_en_segundos;  
	}
		
	///////////////////////////////////////       CONSULTAS AJAX          //////////////////////////////////////////////////
	if(isset($consultaAjax)){
		if($action == "buscarPacientes"){
			$arrayAllInfo = array("tabla" => array() , "promedios" => array(), "contador" => 0, "actividades" => array(), "promTotalAt" => "");
			
			$newPrefix = getPrefixTables($solucionCitas);
			$sqlAdd = "";
			$params = array();
			parse_str($valueForm, $params);	
		
			if(isset($params["documento"]) && $params["documento"] != ""){
				$sqlAdd = "AND ".$newPrefix."doc = '".$params["documento"]."'";
			}
			
			if(isset($params["examen"]) && $params["examen"] != ""){
				$sqlAdd = "AND c.Cod_exa = '".$params["examen"]."'";
			}
			$sqlAc = "SELECT 
				raccod, racact, racord, ac.Prodes
				FROM citasen_000032 a
				INNER JOIN citasen_000025 ac ON ac.Procod = a.Racact
				WHERE Promer = 'on'
				AND Proest = 'on'
				AND racest = 'on'
				GROUP BY racact,raccod ASC";
			$resAc =  mysql_query($sqlAc, $conex);
			
			$cont = 0;
			$Act = "";
			$newAct = "";
			$arrAct = array();
			$arrCodes = array();
			$strCon = "";
			
			while ($row = mysql_fetch_assoc($resAc)){
			
				$arrCodes[$cont] = $row["raccod"];
							
				if($cont === 0){
					$Act = $row["racact"];	
					$strCon .=  ",  timediff(".$newPrefix.$arrCodes[($cont)].$Act."h, l.Hora_data) as difTurnoFirtAction";	
				}else{					
					$newAct = $row["racact"];
				}
				
				if($Act != $newAct && $newAct != ""){								
					$arrAct[$Act]["max"] = $newPrefix.$arrCodes[($cont-1)].$Act."h";
					
					//para encontrar la duración de la actividad se busca en el arreglo anterior antes de que se cambiara a la actividad nuevamente				
					$strCon .= 	", timediff(".$arrAct[$Act]["max"].",".$arrAct[$Act]["min"].") as dur".$Act;
					
					$Act = $newAct;	
					
					if($row["racord"] == 1){
						$strCon .= 	", timediff(TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') , ".$newPrefix.$arrCodes[($cont)].$Act."h) as efec".$Act;
						$arrAct[$Act]["min"] = $newPrefix . $arrCodes[$cont] .	$Act."h";						
					}					
				}else{
					if($row["racord"] == 1){
						$strCon .= 	", timediff(TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') , ".$newPrefix.$arrCodes[($cont)].$Act."h) as efec".$Act;
						$arrAct[$Act]["min"] = $newPrefix . $arrCodes[$cont] .	$Act."h";	
					}
				}
				
				$arrAct[$Act]["name"]= $row["Prodes"];	
				
				$cont++;
			}
			
			$arrAct[$Act]["max"] = $newPrefix.$arrCodes[($cont-1)].$Act."h";
					
			//para encontrar la duración de la actividad se busca en el arreglo anterior antes de que se cambiara a la actividad nuevamente				
			$strCon .= 	", timediff(`".$arrAct[$Act]["max"]."`,`".$arrAct[$Act]["min"]."`) as dur".$Act;
					
					
			
			if ($caso == 2 and $valCitas =="on") {
				$sql ="";
			} else if ($caso == 3 or $caso == 1) {
				$sql = "SELECT 
						  ".$newPrefix."doc  as documento
						, nom_pac  
						, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as horacita
						, l.Hora_data horaturno
						,  timediff(l.Hora_data, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i')) as difTurnoCita
						" . $strCon . "				
						, ex.Descripcion as examen							
					FROM ".$solucionCitas."_000023 l
					INNER JOIN ".$solucionCitas."_000001 c ON c.Cedula = l.".$newPrefix."doc
					INNER JOIN ".$solucionCitas."_000006 ex ON ex.codigo = c.Cod_exa AND ex.Cod_equipo = c.Cod_equ
					WHERE l.Fecha_data BETWEEN '".$params["fechini"]."' and '".$params["fechfin"]."'
					AND c.Fecha BETWEEN '".$params["fechini"]."' and '".$params["fechfin"]."'
					AND c.Asistida = 'on'
					AND ".$newPrefix."est = 'on'
					AND c.Activo = 'A'
					".$sqlAdd;	
					
			} else if ($caso == 2 and $valCitas != "on") 	{			
				$sql = "SELECT 
						  ".$newPrefix."doc  as documento
						, nom_pac  
						, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as horacita
						, l.Hora_data horaturno
						,  timediff(TIME_FORMAT( CONCAT(hi,'00'), '%H:%i'),l.Hora_data) as difTurnoCita
						" . $strCon . "				
						, ex.Descripcion as examen							
					FROM ".$solucionCitas."_000023 l
					INNER JOIN ".$solucionCitas."_000009 c ON c.Cedula = l.".$newPrefix."doc
					INNER JOIN ".$solucionCitas."_000011 ex ON ex.codigo = c.Cod_exa AND ex.Cod_equipo = c.Cod_equ
					WHERE l.Fecha_data BETWEEN '".$params["fechini"]."' and '".$params["fechfin"]."'
					AND c.Fecha BETWEEN '".$params["fechini"]."' and '".$params["fechfin"]."'
					AND c.Asistida = 'on'
					AND ".$newPrefix."est = 'on'
					AND c.Activo = 'A'
					".$sqlAdd;	
			}
			
			if($sql != ""){
				
				$res = mysql_query($sql, $conex);
				$arrInfo = array();
				$cont = 0;
				$cont2 = 0;	
				
				$sumTiempos = array();
				foreach($arrAct as $key=>$value){					
					$sumTiempos[$key]["suma"] = "00:00:00";	
					$sumTiempos[$key]["cont"] = 0;	
				}
				
				$sumTotalAt = "00:00:00";
				while ($row = mysql_fetch_assoc($res)){	
								
					$totAtenc = "00:00:00";
					$newArr = array();
					$contFor = 0;
					foreach($arrAct as $key=>$value){						
						$dur = strrpos($row["dur".$key], "-") === false ? $row["dur".$key] : "00:00:00";
						$efec = strrpos($row["efec".$key], "-") === false ? $row["efec".$key] : "00:00:00";	
											
						if($dur != "00:00:00" && $dur  != null){
							$sumTiempos[$key]["suma"] = sumar($sumTiempos[$key]["suma"], $dur);							
							$sumTiempos[$key]["cont"] = $sumTiempos[$key]["cont"]+1;
						}
						
						$newArr[$value["name"]] = array("dur" => $dur,
											  "efec" => $efec,
											  "namAc" => $value["name"]										  
						);
						$totAtenc = sumar($totAtenc, $dur) ;		
		
					}
					$sumTotalAt = sumar($sumTotalAt, $totAtenc) ;	
					
					if($row["horaturno"] != ""){
						$difTurnoFirtAction = $row["difTurnoFirtAction"];
						$difTurnoCita = $row["difTurnoCita"];						
					}else{
						$difTurnoFirtAction = "";
						$difTurnoCita = "";;		
					}
					
					$arrInfo[] = array ("documento" => $row["documento"],
										"nombre" => utf8_encode($row["nom_pac"]),
										"examen" => utf8_encode($row["examen"]),
										"horacita" => $row["horacita"],
										"horaturno" => $row["horaturno"],
										"difTurnoFirtAction" => $row["difTurnoFirtAction"],
										"difTurnoCita" => $row["difTurnoCita"],
										"tiempos" => $newArr, 
										"totAtenc" => $totAtenc
										);		
									
					$cont++;
				}
			
				if(count($arrInfo) >= 1){	
					foreach($arrAct as $key=>$value){	
						$arrPromedios[$value["name"]]["prom"] = $sumTiempos[$key]["cont"] > 0 ? conversorSegundosHoras(conversorSegundos($sumTiempos[$key]["suma"])/$sumTiempos[$key]["cont"]) : "";	
						$arrPromedios[$value["name"]]["cont"] = $sumTiempos[$key]["cont"];	
					}
					$promTotalAt = conversorSegundosHoras(conversorSegundos($sumTotalAt)/$cont);
				}
		
				$arrayAllInfo = array("tabla" => $arrInfo , "promedios" => $arrPromedios, "actividades" => $arrAct , "promTotalAt" => $promTotalAt);
			}
			echo json_encode($arrayAllInfo);
			exit();
		}
	} else {
		
		if(!isset($_SESSION['user']) ){
			echo "<br /><br /><br /><br />
					  <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
						  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
					 </div>";
			return;
		}
		
		$wactualiz = " 2017-06-14 ";
		$wtitulo = "REPORTE DE TIEMPOS ATENCIÓN ENDOSCOPIA";
		encabezado($wtitulo, $wactualiz, 'clinica');
		$infoCco = getInfoCentroCosto($solucionCitas);
		$usaTurnero = isset($infoCco["usaTurnero"]) ? $infoCco["usaTurnero"] : "";
				
		echo "<input type='hidden' id='solucionCitas' value='".$solucionCitas."'>";	
		echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";	
		echo "<input type='HIDDEN' name= 'valCitas' id='valCitas' value='".@$valCitas."'>";
		echo "<input type='HIDDEN' name= 'caso' id='caso' value='".$caso."'>";
		echo "<input type='HIDDEN' name= 'usaTurnero' id='usaTurnero' value='".$usaTurnero."'>";
		
	
		function getListaExamenes($conex){
			$sql = "SELECT distinct(codigo) as codeEx, descripcion
					FROM citasen_000011 c
					WHERE Activo = 'A'
					ORDER BY descripcion DESC";
			$res = mysql_query($sql, $conex);
			
			$strOptions = "<select name='examen' id='examen'>";
			$strOptions .= "<option value=''>Seleccione</option>";
			
			while ($row = mysql_fetch_assoc($res)){	
				$strOptions .= "<option value='".$row["codeEx"]."'> ".$row["descripcion"]."</optino>";			
			}
			
			$strOptions .= "</select>";
			echo $strOptions;
		}
?>

<html>
<head>
<title>REPORTE DE TIEMPOS ATENCIÓN ENDOSCOPIA</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script>   

<script type="text/javascript">

$(document).ready( function (){		

	$("#tblpacientes").stickyTableHeaders();
	

	$('#btnBuscar').click(function(){	
		var valueForm = $("#formBusqueda").serialize();;
					
		$.post("rep_endoscopia.php",
		{
			consultaAjax:   		'on',
			action:         		'buscarPacientes',
			valueForm:         		valueForm,
			wemp_pmla:        		$("#wemp_pmla").val(),
			solucionCitas:   	    $("#solucionCitas").val(),		
			valCitas	:   	    $("#valCitas").val(),	
			caso:   	   			$("#caso").val()		
			}, function(respuesta){
				$("#resultado").empty();
				$("#nameCols").empty();
				var cont = 0;
				var fila = "fila1";
				var stringTrC = "";
				var strinPr = "";
				var stringTr = ''; 
				
				if(respuesta.tabla.length >= 1){
					jQuery.each(respuesta.tabla, function(){	
						if($("#usaTurnero").val() === "on") {
							strAdd = '<td align="center">'+this.difTurnoCita+'</td><td align="center">'+this.difTurnoFirtAction+'</td>';
						}else{
							strAdd = "";
						}
						stringTrC = stringTrC + '<tr class="'+fila+'"><td  align="center">'+this.documento+'</td><td>'+this.nombre+'</td><td>'+this.examen+'</td><td align="center">'+this.horacita+'</td><td align="center">'+this.horaturno+'</td>'+strAdd;
						
						jQuery.each(this.tiempos, function(){				
							stringTrC = stringTrC+'<td align="center">'+this.dur+'</td>';					
						});
						
						stringTrC = stringTrC+'<td align="center">'+this.totAtenc+'</td>';	
						stringTrC = stringTrC + "</tr>";
						fila = fila == "fila1" ? "fila2" : "fila1";
						cont++;
					});	
					
					//Para los promedios
					strinPr = strinPr + '<tr class="encabezadoTabla"><td><b>Total pacientes</b></td><td align="center">'+cont+'</td><td colspan="3" align="right"> Promedio: Suma tiempos/Cantidad pacientes con todos los registros completos </td>';
					jQuery.each(respuesta.promedios, function(i, val){						
						strinPr = strinPr+'<td align="center">'+val.prom+'<br> (' + this.cont + ' )</td>';					
					});
							
					strinPr = strinPr + "<td  align='center'>"+respuesta.promTotalAt+"<br> (" + cont + " )</td></tr>";				
					stringTr = stringTr + stringTrC + strinPr;	
				}else{
					var cols = $("#tblpacientes .tableFloatingHeaderOriginal .encabezadoTabla >th").length;
					stringTr = "<tr class='encabezadoTabla' ><td align='center' colspan="+cols+">No se encontraron resultados</td></tr>";
				}
				
				$('#tblpacientes tbody:last').append(stringTr);
				
		}, 'json');								
	});		
});
</script>
</head>

<body style='width: 97%'>

	<div style="text-align:right; padding-right:8%"><input name="button" style="width:100" onclick="window.close();" value="Cerrar" type="button"></div>
	
	<form id="formBusqueda">
		<table align="center" width="60%">
			<tbody>
				<tr class="encabezadotabla">
					<td colspan="4" align="center"><font size="4">Parámetros de búsqueda</font></td>
				</tr>
				<tr>
					<td class="fila1" width="20%">
						<b>Fecha inicial:</b>
					</td>
					<td class="fila2" width="30%">
						<?php campoFechaDefecto("fechini", date("Y-m-d")); ?>
					</td>			
					<td class="fila1"  width="20%">
						<b>Fecha final:</b>
					</td>
					<td class="fila2"  width="30%">
						<?php campoFechaDefecto("fechfin", date("Y-m-d")); ?>
					</td>
				</tr>
				<tr>
					<td class="fila1" width="80px">
						<b>Documento:</b>
					</td>
					<td class="fila2">
						<input type="text" name="documento" id="documento">
					</td>				
					<td class="fila1" width="80px">
						<b>Exámen:</b>
					</td>
					<td class="fila2" colspan="3">						
						<?php getListaExamenes($conex); ?>					
					</td>
				</tr>
				<tr>								
					<td class="encabezadotabla" align="center" colspan="4">
						<input type="button" id="btnBuscar" value="Buscar">					
					</td>
				</tr>										
			</tbody>
		</table>								
	</form>
	
	<br>
	<?php if($usaTurnero == "on"){ ?>		
		<p style="color:red">* Diferencia Hora cita - Hora turno puede tener valor negativo cuando el turno se tomó a una hora posterior a la de la cita.
		<br>* Diferencia hora turno y atención es el tiempo entre la hora del turno y la hora en que se inicia la atención</p>		
	<?php } ?>
	
	<br>
	<table align="center" border="0" id="tblpacientes" width="100%">
		<thead id="Encabezado">	
			<?php getEncabezadoTabla($conex, $solucionCitas, $usaTurnero); ?>
		</thead>
		<tbody id="resultado">
		</tbody>
	</table>
	<br><br>
</body>
</html>
<?php } ?>
