<head>
  <title>REQUERIMIENTOS</title>

 
  <style type="text/css">

    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#DDDDDD;font-size:11pt;font-family:Tahoma;}
    	.texto2{color:#003366;background:#DDDDDD;font-size:9pt;font-family:Tahoma;}
    	.texto4{color:#003366;background:#C0C0C0;font-size:9pt;font-family:Tahoma;}
    	.texto3{color:#003366;background:#C0C0C0;font-size:9pt;font-family:Tahoma;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
      	.texto5{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
      	.texto7{background:#FFFFFF;font-size:9pt;font-family:Arial;}
      	.texto8{background:#DDDDDD;font-size:9pt;font-family:Arial;}
   </style>
   <link rel="stylesheet" src="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css">
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
   <script type="text/javascript">
			// $(document).ready(function() {   
			// 		var tipo = document.informatica.tipreq.value;
			// 		var cco  = document.informatica.cco.value;		
			// 		var select = $('#clareq');	
			// 		$.ajax({
			// 			type: "GET",
			// 			url: "clases.php",
			// 			data: {tipo, cco},				    
			// 			success: function(response) {	
			// 			var result = $.parseJSON(response);							
			// 			if(select.prop) {
			// 				var options = select.prop('options');
			// 				}
			// 				else {
			// 				var options = select.attr('options');
			// 				}
			// 				$('option', select).remove();
			// 				$("#clareq").append($("<option value=''>Todos</option>"));
			// 				$.each(result , function( index, obj ) {			
			// 					document.getElementById('clareq').innerHTML +='<option value="'+index+'">'+obj+'</option>';
			// 			});								
						
			// 			}
			// 		});				
			// 		var value = document.informatica.clasehidden.value;		
			// 		document.getElementById("clareq").value = value;
					
			// 		$("#select_id").change();
			// 		$('#clareq').on('change', function() {
			// 			  $('#clasehidden').val(this.value);
						  
			// 		});

					
							    					
			// });
		
		// function clases() {
		// 		var tipo = document.informatica.tipreq.value;
		// 		var cco  = document.informatica.cco.value;
								
		// 		var select = $('#clareq');	
		// 		$.ajax({
		// 			type: "GET",
		// 			url: "clases.php",
		// 			data: {tipo, cco},				    
		// 			success: function(response) {						
		// 				var result = $.parseJSON(response);
		// 				if(result.length<=0) 
		// 				{
							
		// 					if(select.prop) {
		// 							var options = select.prop('options');
		// 						}
		// 						else {
		// 							var options = select.attr('options');
		// 						}
		// 						$('option', select).remove();
		// 						$("#clareq").append($("<option value=''>Todos</option>"));
		// 					$.each(result , function( index, obj ) {							
		// 						document.getElementById('clareq').innerHTML +='<option value="'+index+'">'+obj+'</option>';
		// 					});								
		// 				}
		// 			}
		// 		});
		// }
	

	 $(document).ready(function(){
			
		$( "#fecha_fin" ).blur(function() { 			 
			var startDate = $('#fecha_inicio').val().replace(/-/g,'/');
			var endDate = $('#fecha_fin').val().replace(/-/g,'/');

			change_date_end = '!La fecha de inicio debe ser inferior a la fecha fin¡';
			if(new Date(startDate).getTime() > new Date(endDate).getTime()){
				    console.log(new Date(endDate).getTime());
					$('#consultar').prop('disabled',true);
					alert('Fecha inicio debe ser inferior a fecha final');
					document.getElementById("change_fecha_inicio").innerHTML = change_date_end;
					$('#fecha_inicio').focus();
			}else{
				$('#consultar').prop('disabled',false);
				document.getElementById("change_fecha_inicio").innerHTML = "";
			}
		});

		$( "#fecha_inicio" ).blur(function() { 			 
			var startDate = $('#fecha_inicio').val().replace(/-/g,'/');
			var endDate = $('#fecha_fin').val().replace(/-/g,'/');
			if(new Date(startDate).getTime() > new Date(endDate).getTime()){
				    console.log(new Date(endDate).getTime());
					$('#consultar').prop('disabled',true);
					alert('Fecha inicio debe ser inferior a fecha final');
					document.getElementById("change_fecha_inicio").innerHTML = change_date_end;
					$('#fecha_inicio').focus();
			}else{
				document.getElementById("change_fecha_inicio").innerHTML = "";
				$('#consultar').prop('disabled',false);
			}
		});

	 });

	
   function enter()
   {
	   //document.informatica.clareq.options[document.informatica.clareq.selectedIndex].text='';
	   		var startDate = $('#fecha_inicio').val().replace(/-/g,'/');
			var endDate = $('#fecha_fin').val().replace(/-/g,'/');
			var date1 = new Date(startDate).getTime();
			var date2=  new Date(endDate).getTime();
			 if(date1 > date2){
					console.log(date1 , date2);		
					$('#consultar').prop('disabled',true);
					alert('Fecha inicio debe ser inferior a fecha finals');
					$('#fecha_inicio').focus();
			}else{
				$('#consultar').prop('disabled',false);
				document.informatica.submit(); 
		}	
		
	
			
		   

   }
   function enter1()
   {
	document.informatica.clareq.options[document.informatica.clareq.selectedIndex].text='';
	document.informatica.submit();
   }
   function enter2()
   {	
   	document.informatica.tipreq.options[document.informatica.tipreq.selectedIndex].text='';
   	document.informatica.clareq.options[document.informatica.clareq.selectedIndex].text='';
   	document.informatica.submit();
   }

    function enter3()
   {
   	document.informatica.tipreq.options[document.informatica.tipreq.selectedIndex].text='';
   	document.informatica.submit();
   }


    </script>

</head>

<body >

<?php
include_once("conex.php");
/**
 * ACTUALIZACIONES:
 * 2019-10-11
 * Andres Alvarez: Se realiza modificación para generar reporte incluyendo centro de costos origen y clase de requerimiento, esto se realiza inicialmente para el area de central de esterilización. se ruta independiente con su respectivo opción de menu CONSULTA REQUERIMIENTOS CENTRAL DE ESTERILIZACION ruta: matrix/root/procesos/consultaP.php?wbasedato=root&ccodestino=
 * 
 * 2019-10-30
 * Andres Alvarez : se realiza filtro de fechas para optimizar la consulta con un rango de tiempo.
 */

//----------------------------------------------------------funciones de persitencia------------------------------------------------

function consultarCentroCostosUsuario($conex,$wbasedato,$codigo)
{
	global $ccu_usuario;
	$queryCcoUsuario = "SELECT Usucco           
						  FROM ".$wbasedato."_000039
						 WHERE Usucod='".$codigo."' 
						   AND Usuest='on'";
	
	$resCcoUsuario = mysql_query($queryCcoUsuario, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCcoUsuario . " - " . mysql_error());		   
	$numCcoUsuario = mysql_num_rows($resCcoUsuario);



	$ccoUsuario = "";
	if($numCcoUsuario>0)
	{
		$rowsCcoUsuario = mysql_fetch_array($resCcoUsuario);
		$cco = explode("-",$rowsCcoUsuario['Usucco']);
		$ccoUsuario = $cco[0];
		$ccu_usuario = $rowsCcoUsuario['Usucco'];
	}
	
	return $ccoUsuario;
}
function consultarCentroCostos($codigo,$cco)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT distinct Usucco"
	."         FROM ".$wbasedato."_000041, ".$wbasedato."_000039 "
	."      WHERE Mtrest='on' "
	."         AND mid(Usucco,1,instr(Usucco,'-')-1)=Mtrcco
		   AND Usuest = 'on'";
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			//2007-06-20 se despliega tambien el nombre de la empresa
			$exp = explode(')',$row1[0]);
			$emp=substr($exp[0], 1, strlen($exp[0]));

			$q =  " SELECT empdes"
			."         FROM ".$wbasedato."_000050 "
			."      WHERE Empcod='".$emp."' "
			."         AND Empest='on' ";

			$resemp = mysql_query($q,$conex);
			$rowemp= mysql_fetch_array($resemp);

			$costos[$i]=$row1[0].'-'.$rowemp[0];

		}
	}
	else if ($costos[0]=='')
	{
		$costos= false;
	}

	return $costos;
}


function consultarCentroCostosOrigen($cco)
{
		global $conex;
		global $wbasedato;
			
		$query = "SELECT Reqccs 
					FROM root_000040
				   WHERE Reqcco='".$cco."'
				     AND Reqccs!='' 
					 AND Reqccs NOT LIKE '%NO%'
				GROUP BY Reqccs;";
			
		// $query = "SELECT Reqccs 
					// FROM root_000040
				   // WHERE Reqcco='(01)1082'
				     // AND Reqccs!='' 
				     // AND Reqsat!='on' 
					 // AND Reqccs NOT LIKE '%NO%'
				// GROUP BY Reqccs;";
					 
		$resultado = mysql_query($query,$conex);
		
		$cantCcos = mysql_num_rows($resultado);
		
		$centros = array();

		while ($row = mysql_fetch_array($resultado)) 
		{
			$empcco= explode(')',$row['Reqccs']);

			$emp= substr($empcco[0], 1, strlen($empcco[0]));
			
			$queryCco = " SELECT Cconom 
							FROM costosyp_000005 
						   WHERE Ccoemp='".$emp."' 
							 AND Ccocod='".$empcco[1]."';";

												 
			$resultadoCco = mysql_query($queryCco,$conex);

		
			$cantCco = mysql_num_rows($resultadoCco);

						
			if($cantCco>0)
			{
				$rowCco = mysql_fetch_array($resultadoCco);
				
				 
				$centros[$row['Reqccs']] = $row['Reqccs']."-".$rowCco[0];
			}
		}

		return $centros;
}


function consultarTipos($cco)
{
	global $conex;
	global $wbasedato;
	
	$filtroCco = "";
	if($cco!="")
	{
		$filtroCco = "AND Mtrcco = '".$cco."'";
	}
	
	$queryTipo = "	SELECT Mtrcod,Mtrdes
					  FROM ".$wbasedato."_000041
					 WHERE Mtrest='on'
						".$filtroCco.";";
	
	$resTipo =  mysql_query($queryTipo,$conex) or die ("Error: ".mysql_errno()." - en el query consultar tipo de requerimiento: ".$queryTipo." - ".mysql_error());
	$numTipo = mysql_num_rows($resTipo);	
	
	$arrayTipo = array();
	if($numTipo > 0)
	{
		while($rowTipo = mysql_fetch_array($resTipo))
		{
			$arrayTipo[$rowTipo['Mtrcod']] = $rowTipo['Mtrdes'];
		}
	}
	
	return $arrayTipo;
}

function consultarTipoPorUsuario($codigo, $cco)
{
	global $conex;
	global $wbasedato;	
	$queryTipoUsuario = "SELECT Pertip 
						   FROM ".$wbasedato."_000042 
						  WHERE Perusu='".$codigo."'
							AND Percco='".$cco."'
							AND perest='on'
					   GROUP BY Pertip;";
	
	$resTipoUsuario =  mysql_query($queryTipoUsuario,$conex) or die ("Error: ".mysql_errno()." - en el query consultar tipo de requerimiento: ".$queryTipoUsuario." - ".mysql_error());
	$numTipoUsuario = mysql_num_rows($resTipoUsuario);	
	
	$tipoUsuario = "";
	if($numTipoUsuario > 0)
	{
		$rowTipoUsuario = mysql_fetch_array($resTipoUsuario);
		$tipoUsuario = $rowTipoUsuario['Pertip'];
		
	}	
	return $tipoUsuario;
}


function consultarEstados()
{
	global $conex;
	global $wbasedato;
	
	$queryEstados = " SELECT Estcod,Estnom  
						FROM ".$wbasedato."_000049 
					   WHERE Estest='on'
					";
	
	$resEstados =  mysql_query($queryEstados,$conex) or die ("Error: ".mysql_errno()." - en el query consultar estados: ".$queryEstados." - ".mysql_error());
	$numEstados = mysql_num_rows($resEstados);	
	
	$arrayEstados = array();
	if($numEstados > 0)
	{
		while($rowEstados = mysql_fetch_array($resEstados))
		{
			$arrayEstados[$rowEstados['Estcod']] = $rowEstados['Estnom'];
		}
	}
	
	return $arrayEstados;
}

function pintarSelectTipo($conex,$wbasedato,$codigo, $cco, $ccodestino = null, 		$tipreq)
{
	$tipos = consultarTipos($cco);
	if(is_null($ccodestino)){
	
		$tipos = consultarTipos($cco);
		
		$html = "";
		if(count($tipos)>0)
		{
			$tipoUsuario = $tipreq == "" ?  consultarTipoPorUsuario($codigo, $cco) : $tipreq;
		
			foreach($tipos as $keyTipo => $valueTipo)
			{
				$opcionTipo = "";
				if($keyTipo== $tipoUsuario)
				{
					$opcionTipo = "selected";
				}
				$html .= "<option value='".$keyTipo."' ".$opcionTipo.">".$valueTipo."</option>";								
			}
		}

	}else{
		$tipoUsuario = consultarTipoPorCcodestino($ccodestino);
		
		foreach($tipos as $keyTipo => $valueTipo)
			{
				$opcionTipo = "";
				if($keyTipo== $tipoUsuario)
				{
					$opcionTipo = "selected";
				}
				$html .= "<option value='".$keyTipo."' ".$opcionTipo.">".$valueTipo."</option>";								
			}

	}

	return $html;
}

function pintarCostoOrigen($costos_origen, $cco_origen)
{
		
	$html = "";
	if(count($costos_origen)>0)
	{
		
		foreach($costos_origen as $keyCco => $valueCCo)
		{
			$ccoOption = explode("-",$valueCCo);					
			
			$ccoUsuario = "";
			
			if($ccoOption[0] == $cco_origen)
			{
				$ccoUsuario = "selected";
			}
			$html .= "<option value='".$ccoOption[0]."' ".$ccoUsuario.">".$valueCCo."</option>";
	
						
		}
	}
	
	return $html;
}


function pintarSelectEstado($conex,$wbasedato, $estado)
{
	$estados = consultarEstados();
	
	$html = "";
	if(count($estados)>0)
	{
		foreach($estados as $keyEstado => $valueEstado)
		{
			$opcionEstado = "";
			if($keyEstado== $estado)
			{
				$opcionEstado = "selected";
			}
			$html .= "<option value='".$keyEstado."' ".$opcionEstado.">".$valueEstado."</option>";								
		}
	}
	
	return $html;
}

function pintarSelectClase($conex, $wbasedato, $clases, $clareq)
{
	
	$html = "";

	if( $clases && count($clases) > 0)
	{
		$html .= "<option value=''>Todos</option>";
		foreach($clases as $valueClase)
		{
			$key = explode('-',$valueClase);
			
			$opcionEstado = "";			
			
			if($key[0] == $clareq)
			{
				$opcionEstado = "selected";
			}
			$html .= "<option value='".$key[0]."' ".$opcionEstado.">".$valueClase."</option>";								
		}
	}
	
	return $html;
}

function consultarClases($tipo ,$cco, $tipreq, $clase = null, $ccodestino = null)
{
	global $conex;
	
	global $wbasedato, $req_varios_resp;		
	
	$inicio=0;	

   
	if(is_null($ccodestino)){
		if($tipreq == "" || $tipreq != "todos"){
			$tipreq = $tipreq == "" ? $tipo : 
			$tipreq;	
			 
			$q =  "SELECT Rctcla, Rctesp, Clades 
						 FROM ".$wbasedato."_000044, ".$wbasedato."_000043 
				  WHERE rctcco='".$cco."' AND rcttip = '".$tipreq."' AND rctest = 'on' AND rctcla = clacod    AND  claest = 'on'"	;

		}
		else{
			$q =  "SELECT Rctcla, Rctesp, Clades 
			FROM root_000044, root_000043 
	 		WHERE rctcco='".$cco."' AND rctest = 'on' AND rctcla = clacod    AND  claest = 'on' order By root_000044.id"	;
		}
	}else{	
		if($tipreq == "" || $tipreq != "todos"){
			$tipreq = $tipreq == "" ? $tipo : 
			$tipreq;					 
			$q =  "SELECT Rctcla, Rctesp, Clades 
						 FROM ".$wbasedato."_000044, ".$wbasedato."_000043 
				  WHERE rctcco='".$ccodestino."' AND rcttip = '".$tipreq."' AND rctest = 'on' AND rctcla = clacod    AND  claest = 'on'"	;
		}else{
			$q =  "SELECT Rctcla, Rctesp, Clades 
			FROM ".$wbasedato."_000044, ".$wbasedato."_000043 
			 WHERE rctcco='".$ccodestino."' AND rcttip = '".$tipreq."' AND rctest = 'on' AND rctcla = clacod  AND claest = 'on'"	;
		}
		

	}
	
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$clases[$inicio]=$row1['Rctcla'].'-'.$row1['Clades'];
			$inicio++;
			
            if($req_varios_resp == '')
            { $req_varios_resp = $row1['Rctcla'];}
		}

	}
	else
	{
		$clases = false;
	}
  
   return $clases;
}


function pintarFiltros($conex,$wbasedato, $codigo, $cco, $estado, $clase, $cco_origen, $tipreq, $clareq, $fecha_inicio, $fecha_fin)
{
	global $ccodestino;


	$costos = consultarCentroCostos($codigo, $cco);
	
	$costos_origen = consultarCentroCostosOrigen($cco);

	$opcionesSelectTipo = pintarSelectTipo($conex, $wbasedato, $codigo, $cco, $ccodestino, $tipreq);
	
	$mostrarTrTipo = "";

	if($opcionesSelectTipo=="")
	{
		$mostrarTrTipo = "display:none;";
	}	
	if($ccodestino){
		$tipo = consultarTipoPorCcodestino($ccodestino);
	}else{

		$tipo = consultarTipoPorUsuario($codigo, $cco); 
	}
	

	$clases = consultarClases($tipo ,$cco, $tipreq, null, $ccodestino);	
	
	
	$pintarCostoOrigen = pintarCostoOrigen($costos_origen, $cco_origen);


	$opcionesSelectEstado = pintarSelectEstado($conex, $wbasedato, $estado);

	$opcionesSelectClase = pintarSelectClase($conex, $wbasedato, $clases, $clareq);
	
	$html = "";
	
	if ($clases!='')
	{
		$change = 'enter2()';
	}else{
		$change = 'enter3()';
	}

	$html .= "	<div id='divFiltros'>
	<p id='change_fecha_inicio' style='color:red;' align='center' class='parpadea'><p>
					<table id='tablaFiltros' align='center'>
						<tr class='EncabezadoTabla'>
							<td colspan='2' align='center'>Filtrar requerimientos</td>
						</tr>
						<tr>
							<td class='fila1'>Centro de costos destino</td>
							<td class='fila2'>
							<select id='cco' name='cco' onchange='".$change."'>";
			
		$url = '';
	
		if(!isset($ccodestino)){
			$html .= "<option value=''>SELECCIONE UN CENTRO DE COSTOS DESTINO</option>";				
			foreach($costos as $keyCco => $valueCco)
			{										
				$ccoOption = explode("-",$valueCco);					
				$ccoUsuario = "";
				if($cco == $ccoOption[0])
				{
					$ccoUsuario = "selected";
				}
				$html .= "<option value='".$ccoOption[0]."' ".$ccoUsuario.">".$valueCco."</option>";	
				
				 
			}
		}else{
	
			$url = '?wbasedato=root&ccodestino='.$ccodestino;
			foreach($costos as $keyCco => $valueCco){
					$ccoOption = explode("-",$valueCco);
					$ccoUsuario = "";				
					if($ccodestino == $ccoOption[0])
					{			
						$value = $valueCco;
					}
			}
			$html .= "<option value='".$ccodestino."' selected>".$value."</option>";
			
		}
		$fecha_inicio = $fecha_inicio == '' ? date('Y-m-d') : $fecha_inicio;
		$fecha_fin  =  $fecha_fin == '' ? date('Y-m-d') : $fecha_fin; 
		

	$html.="<tr>
	<td class='fila1'>Centro de costos origen</td>
	<td class='fila2'>
		<select id='cco_origen' name='cco_origen'>
		
		<option value=''>Todos</option>".

		$pintarCostoOrigen."</select></td></tr>";
								
		$html .= "</select>
							</td>
						</tr>
						<tr id='trTipoRequerimiento' style='".$mostrarTrTipo."'>
							<td class='fila1'>Tipo de requerimiento</td>
							<td class='fila2'>
								<select id='tipreq' name='tipreq' onchange='enter1()'>
									<option value='todos'>Todos</option>
									".$opcionesSelectTipo."									
								</select>
							</td>
						</tr>";
					if($clases){
						$html.="
							<tr>
							<td class='fila1'>Clase de requerimiento</td>
							<td class='fila2'>
								<select id='clase' 
								name='clareq'>".$opcionesSelectClase.
								"																				
								</select>
							</td>
						</tr>";
					}	
						
					$html.="
					<tr>
						<tr id='trTipoRequerimiento' style='".$mostrarTrTipo."'>
							<td class='fila1'>Estado</td>
							<td class='fila2'>
								<select id='estado' name='estado'>
									<option value=''>Todos</option>
									".$opcionesSelectEstado."									
								</select>
							</td>
						</tr>
						<tr><td class='fila1'>Fecha incio</td><td class='fila2'><INPUT TYPE='date' dateformat='YYY-M-d' NAME='fecha_inicio' id='fecha_inicio' value=".$fecha_inicio." max=".date('Y-m-d')." size=11  class='textoNormal' required></td><td></td></tr>
						<tr><td class='fila1'>Fecha fin</td><td class='fila2'><INPUT TYPE='date'  NAME='fecha_fin' id='fecha_fin' value=".$fecha_fin." max=".date('Y-m-d')." size=11 class='textoNormal'  required>
						</td><td></td></tr>
						<tr>

						<td class='fila1' colspan='2' align='center'>				<input type='hidden' id='clasehidden' name='clasehidden' value='".$clareq."'>			
							<INPUT TYPE='submit' id='consultar' NAME='consultar' VALUE='Consultar' formaction='".$url."' onclick='return enter();'></td>
					</tr>
					</table>
				</div>
				<br><br>
				
				
				";
	
	echo $html;
	
}

function consultarRequerimientos($codigo, $para,$ccoreq,$tipreq,$estado, $clareq, $cco_origen,$fecha_inicio, $fecha_fin)
{
	global $conex;
	global $wbasedato;

	
	
	
	// if ($para=='recibidos')
	// {
		// $q= " SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Reqtpn, Reqsat, id AS id_req "
		// ."       FROM ".$wbasedato."_000040 "
		// ."    WHERE Reqest NOT IN ( SELECT Estcod FROM ".$wbasedato."_000049 WHERE Estfin='on') "
		// //."       OR Reqfec > '".date('Y')."-".date('m')."-01') "
		// ."    ORDER BY 10, 9, 4 desc, 12 desc";
	// }
	// else if ($para=='enviados')
	// {
		// $q= " SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Reqtpn, Reqsat, id AS id_req "
		// ."       FROM ".$wbasedato."_000040 "
		// ."    WHERE Requso = '".$codigo."' "
		// //."       OR  Reqpurs = '".$codigo."' "
		// ."       AND Reqest NOT IN ( SELECT Estcod FROM ".$wbasedato."_000049 WHERE Estfin='on') "
		// ."    ORDER BY 10, 9, 4 desc, 12 desc";
	// }
		
	$tipreq = $tipreq == 'todos' ? "" : $tipreq;
	$filtroTipo = "";
	if($tipreq != "" )
	{
		$filtroTipo = "AND Reqtip='".$tipreq."'";
	}
	
	$filtroCco = "";
	if($ccoreq!="" )
	{
		$filtroCco =   "where Reqcco='".$ccoreq."'";
	}
	
	$filtroClase = "";
	if($clareq != ""){
		$filtroClase = " AND Reqcla='".$clareq."'";
	}
	
	$filtroCco_Origen = "";
	if($cco_origen !=''){
		$filtroCco_Origen = "AND Reqccs ='".$cco_origen."'";
	}
	$filtroEstado = "";
	if($estado!="")	{
		$filtroEstado = "AND Reqest='".$estado."'";
	}

	if($fecha_inicio != $fecha_fin){
		$filtroFecha = "AND Fecha_data >='".$fecha_inicio."' And  Fecha_data <= '".$fecha_fin."'"; 	
	}else{		
		$filtroFecha = "AND Fecha_data = '".$fecha_inicio."'";
	}



	if ($para=='recibidos')	
	{
		$q = "SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Reqtpn, Reqsat, Reqccs, id AS id_req        
				FROM ".$wbasedato."_000040 
				".$filtroCco."
				".$filtroTipo."
				".$filtroEstado."
				".$filtroClase."
				".$filtroCco_Origen."
				".$filtroFecha."
				ORDER BY 10, 9, 4 desc, 12 desc";		

		// $q = "SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Hora_data, Reqtpn, Reqsat, id AS id_req        
		// 		FROM ".$wbasedato."_000040     
		// 		WHERE Reqest NOT IN ( SELECT Estcod FROM ".$wbasedato."_000049 WHERE Estfin='on')
		// 		".$filtroTipo."
		// 		".$filtroCco."
		// 		".$filtroEstado."
		// 		ORDER BY 10, 9, 4 desc, 12 desc";	
		$dd = 0;	
		
	}
	
	if($ccoreq!="")
	{
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($res);

				$requerimientos[$i]['id_req'] = $row['id_req'];
				$requerimientos[$i]['cco']=$row['Reqcco'];
				$requerimientos[$i]['wcodigo_caso'] = $row['Reqtpn'];
				
				$q =  " SELECT distinct Usucco  "
				."         FROM ".$wbasedato."_000039  "
				."      WHERE mid(Usucco,1,instr(Usucco,'-')-1)='".$row['Reqcco']."' ";

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);


				$requerimientos[$i]['cconom']=$row1['Usucco'];
				$requerimientos[$i]['num']=$row['Reqnum'];

				$q= " SELECT Mtrdes "
				."      FROM ".$wbasedato."_000041 "
				."    WHERE Mtrcco = '".$row['Reqcco']."' "
				."      AND Mtrcod = '".$row['Reqtip']."' "
				."      AND Mtrest = 'on' ";

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['tip']=$row1['Mtrdes'];
				$requerimientos[$i]['id']=$row['Reqtip'];
				$requerimientos[$i]['fec']=$row['Reqfec'];

				$q= " SELECT Descripcion  "
				."       FROM usuarios "
				."    WHERE Codigo = '".$row['Requso']."' ";
				

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['uso']=$row1['Descripcion'];

				if(!isset($row['Reqccs'])){
					$q= " SELECT Usucco   "
					."       FROM ".$wbasedato."_000039"
					."    WHERE Usucod = '".$row['Requso']."' "
					."       AND Usuest  = 'on' ";	
					$res1 = mysql_query($q,$conex);
					$row1 = mysql_fetch_array($res1);	
					$requerimientos[$i]['usocco']=$row1['Usucco'];
				}else{					
					$exp = explode(')',$row['Reqccs']);
					$emp=substr($exp[0], 1, strlen($exp[0]));
					$q = "Select Cconom from costosyp_000005 where Ccoemp ='".$emp."'  And Ccocod = '".$exp[1]."'";
					$result = mysql_query($q,$conex);
					$rowx = mysql_fetch_array($result);
					$requerimientos[$i]['usocco']= $row['Reqccs'].'-'.$rowx['Cconom'];
					if(!isset($rowx['Cconom'])){
							$q= " SELECT Usucco   "
						."       FROM ".$wbasedato."_000039"
						."    WHERE Usucod = '".$row['Requso']."' "
						."       AND Usuest  = 'on' ";	
						$res1 = mysql_query($q,$conex);
						$row1 = mysql_fetch_array($res1);	
						$requerimientos[$i]['usocco']=$row1['Usucco'];
					}
				}

				$q= " SELECT Descripcion  "
				."       FROM usuarios "
				."    WHERE Codigo = '".$row['Reqpurs']."' ";
				
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['urs']=$row1['Descripcion'];

				$requerimientos[$i]['des']=substr($row['Reqdes'], 0,20).'...';

				$q =  " SELECT Descripcion "
				."        FROM det_selecciones "
				."      WHERE Medico='".$wbasedato."' "
				."        AND Codigo='16' "
				."        AND Activo = 'A' "
				."        AND Subcodigo = '".$row['Reqpri']."' ";
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['pri']=$row1['Descripcion'];

				//consulto los conceptos
				$q =  " SELECT Estnom, Estcol "
				."        FROM ".$wbasedato."_000049 "
				."      WHERE Estest = 'on' "
				."      and Estcod = '".$row['Reqest']."' ";

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['est']=$row1['Estnom'];
				$requerimientos[$i]['col']=$row1['Estcol'];

				$q =  " SELECT Clades "
				."        FROM ".$wbasedato."_000043 "
				."      WHERE Claest = 'on' "
				."      and Clacod = '".$row['Reqcla']."' ";
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$requerimientos[$i]['cla']=$row1['Clades'];

				// Para buscar si el requerimiento actual tiene pendiente nuevos mensajes de respuesta para leer por parte del que está viendo la lista en determinado momento.
				$q = '';
				$msj_para_creador = '';
				if($row['Requso'] == $codigo)
				{
					$msj_para_creador = 'on';
					$q = "  SELECT  seg.id
							FROM    ".$wbasedato."_000045 AS seg
							WHERE   seg.Segtpn = '".$row['Reqtpn']."'
									AND seg.Segmcr = 'on'";
				}
				elseif($row['Reqpurs'] == $codigo)
				{
					$q = "  SELECT  seg.id
							FROM    ".$wbasedato."_000045 AS seg
							WHERE   seg.Segtpn = '".$row['Reqtpn']."'
									AND seg.Segmen = 'on'";
				}

				$ids_segs_msjs = '';
				if(!empty($q))
				{
					$res1 = mysql_query($q, $conex);
					$arr_ids = array();
					while ($rw_msj = mysql_fetch_array($res1)) {
						$arr_ids[] = $rw_msj['id'];
					}
					$ids_segs_msjs = implode(",", $arr_ids);
				}
				$requerimientos[$i]['mensajes_seguimiento'] = $ids_segs_msjs;
				$requerimientos[$i]['msj_para_creador'] = $msj_para_creador;
			}
		}
		else
		{
			$requerimientos='';
		}

	}
	else
	{
		echo "<script>alert('Debe seleccionar un centro de costos')</script>";
	}
	
	return $requerimientos;
}

function  consultarTipoPorCcodestino($ccodestino){
	global $conex;
	global $wbasedato;	
	$queryTipoUsuario = "SELECT Pertip 
						   FROM ".$wbasedato."_000042 						  
							where Percco='".$ccodestino."'
							AND perest='on'
					   GROUP BY Pertip;";
					  
	$resTipoUsuario =  mysql_query($queryTipoUsuario, $conex) or die ("Error: ".mysql_errno()." - en el query consultar tipo de requerimiento: ".$queryTipoUsuario." - ".mysql_error());
	$numTipoUsuario = mysql_num_rows($resTipoUsuario);	
	
	$tipoUsuario = "";
	if($numTipoUsuario > 0)
	{
		$rowTipoUsuario = mysql_fetch_array($resTipoUsuario);
		$tipoUsuario = $rowTipoUsuario['Pertip'];
		
	}	
	return $tipoUsuario;
}

//----------------------------------------------------------funciones de presentacion------------------------------------------------

function pintarVersion()
{
	$wautor="Carolina Castaño P.";
	$wversion="2019-10-17";
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;
}

function pintarTitulo($wacutaliza, $wemp_pmla )
{
	echo encabezado("<div class='titulopagina2'>SISTEMA DE REQUERIMIENTOS</div>", $wacutaliza, 'clinica');
	echo "<form name='informatica' action='consultaP.php' method=post>";
	echo "<input type=hidden id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	// echo "<tr><td class='titulo1'>SISTEMA DE REQUERIMIENTOS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";
}

function pintarAlert2($mensaje)
{
	echo "</br></table>";
	echo"<CENTER>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
	echo "</table>";
}

function pintarRequerimientos($requerimientos, $para, $wemp_pmla )
{
	$fila='enter()';
	$fila2='enter()';
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr>";
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;NUMERO&nbsp;</b><a onclick='".$fila2."'></a></td>";
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;FECHA</b><a onclick='".$fila2."'></a></td>";
	if($para=='recibidos')
	{
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;SOLICITANTE</b><a onclick='".$fila2."'></a></td>";
	}
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;UNIDAD</b><a onclick='".$fila2."'></a></td>";
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;TIPO</b><a onclick='".$fila2."'></a></td>";
	if($para=='recibidos')
	{
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;CLASE</b><a onclick='".$fila2."'></a></td>";
	}
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;DESCRIPCION</b><a onclick='".$fila2."'></a></td>";
	if($para=='recibidos')
	{
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;RESPONSABLE</b><a onclick='".$fila2."'></a></td>";
		echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;PRIORIDAD</b><a onclick='".$fila2."'></a></td>";
	}
	echo "<td class='encabezadoTabla' align='center' height='50'><a onclick='".$fila."'></a><b>&nbsp;ESTADO</b><a onclick='".$fila2."'></a></td>";
	echo "</tr>";

	for ($i=0;$i<count($requerimientos);$i++)
	{
		if (is_int($i/2))
		{
			$class='fila1';
		}
		else
		{
			$class='fila2';
		}
		echo "<tr>";
		echo "<td class='".$class."' align='center' ><a href='seguimiento.php?wemp_pmla=".$wemp_pmla."&wcodigo_caso=".$requerimientos[$i]['wcodigo_caso']."&cco=".$requerimientos[$i]['cco']."&req=".$requerimientos[$i]['num']."&id_req=".$requerimientos[$i]['id_req']."&id=".$requerimientos[$i]['id']."&ids_segs_pte=".$requerimientos[$i]['mensajes_seguimiento']."&msj_para_creador=".$requerimientos[$i]['msj_para_creador']."' target='new' width='80%' class='numero' >".$requerimientos[$i]['cco']."-".$requerimientos[$i]['num']."</a></td>";
		echo "<td class='".$class."' align='center' >".$requerimientos[$i]['fec']."</td>";
		if($para=='recibidos')
		{
			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['uso']."</td>";

			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['usocco']."</td>";
		}
		else
		{
			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['cconom']."</td>";
		}
		echo "<td class='".$class."' align='center' >".$requerimientos[$i]['tip']."</td>";
		if($para=='recibidos')
		{
			echo "<td class='".$class."' align='center' >".$requerimientos[$i]['cla']."</td>";
		}
		echo "<td class='".$class."' >".$requerimientos[$i]['des']."</td>";
		if($para=='recibidos')
		{
			echo "<td class='".$class."' align='center'>".$requerimientos[$i]['urs']."</td>";
			echo "<td class='".$class."' align='center'>".$requerimientos[$i]['pri']."</td>";
		}
		echo "<td bgcolor='".$requerimientos[$i]['col']."' align='center'>".$requerimientos[$i]['est']."</td>";

		echo "</tr>";
	}
	echo "</table>";
}
/*=========================================================PROGRAMA==========================================================================*/
session_start();

if (!isset($user))
{
	if(!isset($_SESSION['user']))
	session_register("user");
}

if(!isset($_SESSION['user']))
echo "error";
else
{	

	?> <input type='hidden' id='ccodestino' value='<?php echo $ccodestino; ?>' name="ccodestino"><?php
	$wacutaliza = "2013-10-21";
	if (!isset ($para))
	{
		$para='recibidos';
	}
	$wbasedato='root';
		
	include_once("root/comun.php");	// pintarVersion();
	pintarTitulo($wacutaliza, $wemp_pmla );

	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));

	if(!isset($cco))
	{
		$cco = consultarCentroCostosUsuario($conex, $wbasedato, $wusuario);
	}
	
	if(!isset($estado))
	{
		$estado = "";
	}

	if(!isset($cco_origen))
	{
		$cco_origen = "";
	}
	
	
	if(!isset($clase))
	{
		$clase = "";
	}
	if(!isset($fecha_inicio)){
		$fecha_inicio = "";
	}

	if(!isset($fecha_fin)){
		$fecha_fin = "";
	}

	if(!isset($clareq)){
		$clareq = "";
	}

	if(!isset($tipreq)){
		$tipreq = "";
	}
	pintarFiltros($conex,$wbasedato,$wusuario,$cco,$estado, $clase, $cco_origen,$tipreq, $clareq, $fecha_inicio, $fecha_fin);
	
	
	if(isset($consultar))
	{

		$requerimientos= consultarRequerimientos($wusuario, $para,$cco, $tipreq, $estado, $clareq, $cco_origen, $fecha_inicio, $fecha_fin);
		if (is_array($requerimientos))
		{
			pintarRequerimientos($requerimientos, $para, $wemp_pmla );
		}
		else
		{
			pintarAlert2('NO TIENE REQUERIMIENTOS PENDIENTES');
		}

		// echo "<meta http-equiv='refresh' content='40;url=consultaP.php?para=".$para."'>";
	}
}
/*===========================================================================================================================================*/
?>


</body >
</html >
