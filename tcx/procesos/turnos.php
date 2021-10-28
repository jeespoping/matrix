<?php
include_once("conex.php");

if(isset($accionAjax)) 
{
	$respuesta 	= array("error" => false, "msj" => "");
	
	if(!isset($_SESSION['user'])){		
		$respuesta['error'] = true;
		$respuesta['msj'] 	= utf8_encode('Primero recargue la página principal de Matrix ó inicie sesión nuevamente, para poder relizar esta acción.');
	}
	else{
		
		$user_session 	= explode('-',$_SESSION['user']);
		$wuse 			= $user_session[1];
		
		switch($accionAjax)
		{
			case 'iniciarCx':
			{
				$respuesta['pedirCausa'] 	= "off";
				$respuesta['arrCausas'] 	= array();
				
				$sql51 = "
				SELECT Detval
				  FROM root_000051
				 WHERE Detemp = '".$wemp_pmla."'
				   AND Detapl = 'minutosDeHolguraParaIniciarCx'
				";
				$res51 = mysql_query($sql51, $conex) or die("<b>ERROR EN QUERY MATRIX(sql51):</b><br>".mysql_error());
				if($row51 = mysql_fetch_array($res51))
					$minHolgura = (int) $row51['Detval'];
				else
					$minHolgura = 0;
					
				
				// --> Obtener hora del turno
				$sqlInfoTur = "
				SELECT Turfec, Turhin
				  FROM ".$empresa."_000011
				 WHERE Turtur = '".$turno."'			
				";
				$resInfoTur = mysql_query($sqlInfoTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoTur):</b><br>".mysql_error());
				if($rowInfoTur = mysql_fetch_array($resInfoTur))
					$horaIniCx = $rowInfoTur['Turfec']." ".$rowInfoTur['Turhin'].":00";
				
				$minAct	= ceil(strtotime(date("Y-m-d H:i:s")) / 60);
				$minCx	= ceil(strtotime($horaIniCx)/ 60)+$minHolgura;
				
				$respuesta['minAct'] 		= date("Y-m-d H:i:s");
				$respuesta['minCx'] 		= $horaIniCx;
				$respuesta['minHolgura'] 	= $minHolgura;
				
				if($minAct > $minCx && $causaDemora == ""){
					
					$respuesta['pedirCausa'] 	= "on";		
					$respuesta['arrCausas'] 	= traerCausas("I");
				
				}
				else{					
					$sqlIniCx = "
					UPDATE ".$empresa."_000011
					   SET Turfhi = '".date("Y-m-d H:i:s")."',
						   Turusi = '".$wuse."',
						   Turcdi = '".$causaDemora."',
						   Turepc = 'P'
					WHERE  Turtur = '".$turno."'				   
					";
					mysql_query($sqlIniCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIniCx):</b><br>".mysql_error());
				}
				break;
			}
			case 'terminarCx':
			{
				$respuesta['pedirCausa'] = "off";
				
				$sql51 = "
				SELECT Detval
				  FROM root_000051
				 WHERE Detemp = '".$wemp_pmla."'
				   AND Detapl = 'minutosDeHolguraParaIniciarCx'
				";
				$res51 = mysql_query($sql51, $conex) or die("<b>ERROR EN QUERY MATRIX(sql51):</b><br>".mysql_error());
				if($row51 = mysql_fetch_array($res51))
					$minHolgura = (int) $row51['Detval'];
				else
					$minHolgura = 0;
					
				
				// --> Obtener hora del turno
				$sqlInfoTur = "
				SELECT Turfec, Turhfi
				  FROM ".$empresa."_000011
				 WHERE Turtur = '".$turno."'			
				";
				$resInfoTur = mysql_query($sqlInfoTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoTur):</b><br>".mysql_error());
				if($rowInfoTur = mysql_fetch_array($resInfoTur))
					$horaFinCx = $rowInfoTur['Turfec']." ".$rowInfoTur['Turhfi'].":00";
				
				$minAct	= ceil(strtotime(date("Y-m-d H:i:s")) / 60);
				$minCx	= ceil(strtotime($horaFinCx)/ 60)+$minHolgura;
				
				$respuesta['minAct'] 		= date("Y-m-d H:i:s");
				$respuesta['minCx'] 		= $horaFinCx;
				$respuesta['minHolgura'] 	= $minHolgura;
				
				if($minAct > $minCx && $causaDemora == ""){
					$respuesta['pedirCausa'] 	= "on";
					$respuesta['arrCausas'] 	= traerCausas("T");					
				}
				else{					
					$sqlIniCx = "
					UPDATE ".$empresa."_000011
					   SET Turfhf = '".date("Y-m-d H:i:s")."',
						   Turusf = '".$wuse."',
						   Turcdt = '".$causaDemora."',
						   Turepc = 'T'
					WHERE  Turtur = '".$turno."'				   
					";
					mysql_query($sqlIniCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIniCx):</b><br>".mysql_error());
				}
				break;
			}
		}
	}
	
	echo json_encode($respuesta);
	return;
}

?>
<html>
<head>
  	<title>MATRIX Programa de Gestion de Cirugia</title>
  	<!-- UTF-8 is the recommended encoding for your pages -->

    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <style type="text/css">
        #tooltip{
            color: #2A5DB0;
            font-family: Arial,Helvetica,sans-serif;
            position:absolute;
            z-index:3000;
            border:1px solid #2A5DB0;
            background-color:#FFFFFF;
            padding:5px;
            opacity:0.5;}
        #tooltip div{margin:0; width:400px}
        .subtitle{
            font-weight: bold;
        }
    </style>

<!-- Loading Calendar JavaScript files 
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script> -->
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	A	{text-decoration: none;color: #000066;}
    	#tipo1A{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo3A{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.tipoG11W{color:#000066;background:#99CCFF;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	.tipoG54W{color:#FFFFFF;background:#5F5F5F;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	.tipoCOM{color:#000066;background:#EAEAEA;font-size:8pt;font-family:Tahoma;font-weight:bold;}
    	.tipoTAB{color:#000066;background:#CCCCCC;font-size:9pt;font-family:Tahoma;font-weight:normal;width:50em;text-align:left;height:10em;}
    	.tipoLIN1{color:#000066;background:#E8EEF7;}
    	.tipoLIN2{color:#000066;background:#C3D9FF;}

    	#tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG000{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:left;}
    	#tipoG001{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG11{color:#000066;background:#99CCFF;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG51{color:#FF0000;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG52{color:#FFFFFF;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG53{color:#000066;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG61{color:#FF0000;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG62{color:#FFFFFF;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG63{color:#000066;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}

    </style>
	 
	 
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	
	var arrCausas = new Array();
	
    $(document).ready(function(){
		
		if($("#operativo").val() == "on")
			vistaOperativa();
		
        $("td[tieneTitle='on']").tooltip({track: true, delay: 0, showURL: false, opacity: 0.50 });
		
    });
	
	function vistaOperativa(){
		
		$.blockUI({
			message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Cargando...</div>",
			css:{"border": "2pt solid #7F7F7F"}
		});	
		
		arrTurOrd 	= new Array();
		arrTurnos 	= {};
		cantQui		= $("#cantQui").val();
		
		// --> Envio de la variable "operativo" en todos los llamados GET
		$('[href*="turnos.php"]').each(function(){
			$(this).attr("href", $(this).attr("href")+"&operativo="+$("#operativo").val());
		});
		
		for(x=1;x<=cantQui;x++){
			
			$("[numQuirofano="+x+"]").each(function(){
				turno = $(this).attr("idTurno");
				if (arrTurnos[turno] == undefined){
					arrTurnos[turno] = 1;
					arrTurOrd.push(turno);					
				}
				else
					arrTurnos[turno]++;
			});
		}
		
		colorTd = "#C3D9FF";
		jQuery.each(arrTurOrd, function(idx, valTurno){
			
			cant 	= arrTurnos[valTurno];
			
			$("td[idTurno="+valTurno+"]").eq(0).attr("idTurno", "Temp"+valTurno);
			$("td[idTurno="+valTurno+"]").remove();
			$("td[idTurno=Temp"+valTurno+"]").attr("idTurno", valTurno);
			
			if(arrTurOrd[idx-1] != undefined && $("td[idTurno="+valTurno+"]").attr("numQuirofano") == $("td[idTurno="+arrTurOrd[idx-1]+"]").attr("numQuirofano"))
				$("td[idTurno="+valTurno+"]").attr("turnoAnt", arrTurOrd[idx-1]);
			
			estProcesoOper = $("td[idTurno="+valTurno+"]").attr("estProcesoOper");
			
			switch(estProcesoOper){
				case "P":{
					colorTd = "#7ED18F";
					btnShow	= "btnFinCx";
					break;
				}
				case "T":{
					colorTd = "#FF6262";
					btnShow	= "";
					break;
				}
				default :{
					colorTd = "#7FAFFF";
					btnShow	= "btnIniCx";
					break;
				}
			}
			
			html 	= "	<img src='../../images/medical/sgc/play10.png' width='20' height='20' id='btnIniCx"+valTurno+"' onClick='iniciarCx(\""+valTurno+"\", true)' style='cursor:pointer;display:none' title='Iniciar cx'>"+
					"	<img src='../../images/medical/sgc/stop10.png' width='20' height='20' id='btnFinCx"+valTurno+"' onClick='terminarCx(\""+valTurno+"\", true)' style='cursor:pointer;display:none' title='Terminar cx'><br><br>";
			
			// --> Quirofano fuera de uso
			if($("td[idTurno="+valTurno+"]").attr("id") == "tipoG21"){
				html 	= "";
				colorTd = "#FFFA95";
				btnShow = "";
			}
			
			$("td[idTurno="+valTurno+"]").attr("rowspan", cant).css({"background-color":colorTd}).find("a").before(html);
			
			if(btnShow != "")
				$("#"+btnShow+valTurno).show();
			
			//colorTd = ((colorTd == "#C3D9FF") ? "#E8EEF7" : "#C3D9FF");			
		});
		
		$("[tablaPrincipal]").css({"color":"#000000", "font-family":"verdana", "border-collapse":"collapse"});		
		$("td[sinTurno]").css({"color":"#000000", "font-family":"verdana", "border": "1px solid #000000","background-color":"#E1E1E1"});	//#B9DCFE	
		$("td[idTurno]").css({"color":"#000000", "font-family":"verdana", "border": "1px solid #000000"});			
		$("td[esUrgente=on]").css({"color":"#FF0000;"});			
		$("#tablaColores").after("<div id='divPedirCausa' style='display:none;font-family: verdana;font-size: 9pt;' align='center'>"
								+"La cx ha <span id='textMsj'></span> más tarde de lo programado.<br><br>"
								+"Por favor seleccione la causa del retraso:<br><br><select id='causaDemora' style='border-radius: 4px;border:1px solid #AFAFAF;'><option value=''>Seleccione..</option></select></div>");
		
		$("#tablaColores").hide();
		
		c ='border:1px solid #999999;padding:2px';
		convenciones = "<table style='font-size: 10pt;font-family: verdana;'><tr><td style='"+c+";background-color:#FEFFF6'>Sin programación</td><td style='"+c+";background-color:#FFFA95'>Fuera de uso</td><td style='"+c+";background-color:#7FAFFF'>Cx sin iniciar</td><td style='"+c+";background-color:#7ED18F'>Cx en proceso</td><td style='"+c+";background-color:#FF6262'>Cx terminada</td><td style='color:red'>Urgentes</td></tr></table>";
		$("[tablaPrincipal]").before(convenciones);
		
		$.unblockUI();
	}
	
	function iniciarCx(turno, primeraVez){
		
		if($("td[idTurno="+turno+"]").attr("turnoAnt") != undefined){
			
			turnoAnt = $("td[idTurno="+turno+"]").attr("turnoAnt");
			
			if($("td[idTurno="+turnoAnt+"]").attr("estProcesoOper") != "T"){
				alert("Antes de iniciar esta cx, primero debe terminar la anterior.");
				return;
			}
		}
		
		if(primeraVez){
			if(!confirm("Está seguro en INICIAR la cirugía?"))
				return;
		}
		
		$.post("turnos.php",
		{	
			accionAjax		:   'iniciarCx',
			empresa			:	$('#empresa').val(),
			wemp_pmla		:	$('#origen').val(),
			turno			:	turno,
			causaDemora		: 	$("#causaDemora").val()			

		}, function(respuesta){
			
			if(respuesta.pedirCausa == "on"){
				
				$("#causaDemora").find("option").remove();
				$("#causaDemora").append(new Option("Seleccione..", ""));
				
				$.each(respuesta.arrCausas, function(index, item) {
					$("#causaDemora").append(new Option(item, index));
				});
			
				$("#textMsj").text("iniciado");
				$("#divPedirCausa").show().dialog({
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "<div align='center' style='font-size:10pt'>Causa de retraso:</div>",
					width: "350px",
					buttons: {
						"Guardar": function() {
							//$("div [class=ui-dialog-buttonset]").find("button").attr("disabled", "disabled");
							iniciarCx(turno, false);
						}
					},
					close: function(){
						$("#causaDemora").val("");
					}
				});				
			}
			else{			
				// $("#btnIniCx"+turno).hide();
				// $("#btnFinCx"+turno).show().parent().css({"background-color":"#7ED18F"});
				// $("td[idTurno="+turno+"]").attr("estProcesoOper", "P");
				// $("#divPedirCausa").dialog("close");
				// $("#causaDemora").val("");
				enter();
			}
			
		}, 'json');		
	}
	
	function terminarCx(turno, primeraVez){
		
		if(primeraVez){
			if(!confirm("Está seguro en TERMINAR la cirugía?"))
				return;
		}
		
		$.post("turnos.php",
		{	
			accionAjax		:   'terminarCx',
			empresa			:	$('#empresa').val(),
			wemp_pmla		:	$('#origen').val(),
			turno			:	turno,
			causaDemora		: 	$("#causaDemora").val()			

		}, function(respuesta){
			
			if(respuesta.pedirCausa == "on"){
				
				$("#causaDemora").find("option").remove();
				$("#causaDemora").append(new Option("Seleccione..", ""));
				
				$.each(respuesta.arrCausas, function(index, item) {
					$("#causaDemora").append(new Option(item, index));
				});
				
				$("#textMsj").text("terminado");
				$("#divPedirCausa").show().dialog({
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "<div align='center' style='font-size:10pt'>Causa de retraso:</div>",
					width: "350px",
					buttons: {
						"Guardar": function() {
							terminarCx(turno, false);
						}
					},
					close: function(){
						$("#causaDemora").val("");
					}
				});				
			}
			else{
				// $("#btnFinCx"+turno).hide().parent().css({"background-color":"#FF6262"});				
				// $("#divPedirCausa").dialog("close");
				// $("td[idTurno="+turno+"]").attr("estProcesoOper", "T");
				// $("#causaDemora").val("");
				enter();
			}
			
		}, 'json');		
	}
	
	
	function enter()
	{
		document.forms.turnos.submit();
	}
	function ejecutar(path,tipo)
	{
		if(tipo == 1)
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=425');
		else
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=580');
	}
	function teclado()
	{
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}
	function tooltipAlertas(pos)
	{
		$('#ALERT[pos] *').tooltip();
	}
	function teclado6(e)
	{
		if (event.keyCode != 8) event.returnValue = false;
	}

//-->
</script>
<?php

/**********************************************************************************************************************
[DOC]
	   PROGRAMA : turnos.php
	   Fecha de Liberación : 2007-05-03
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2020-01-22

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite grabar los  de las
	   cirugias en los diversos quirofanos y en las horas especificadas por los cirujanos.
	   El programa valida :
	   						1. Que el quirofano este disponible para la cirugia.
	   						2. Que el cirujano NO este ocupado en otras cirugias (Cirugias Montadas).
	   						3. Que los equipos necesarios esten disponibles.
	   						4. Que las cirugias esten grabadas.

	   Esta informacion sirvira para generar informacion en los distintos procesos de gestion en la unidad de cirugia


	   REGISTRO DE MODIFICACIONES :
	   .2020-01-22: Jerson Trujillo, se modifican colores, no se pide causa cuando es tablero admon, a quirofanos fuera de uso no
					se les pide iniciar y finalizar.
	   .2020-01-22: Jerson Trujillo, Nueva visualizacion (Cambio de colores, turnos con rowspan, botones para iniciar y finalizar cx) dependiendo si existe el parametro operativo=on (enviado por url)
					- Se empieza a registrar las horas de inicio y fin de la cx asi como las causas de demora.
					- Cuando se modifica una cx (Quirofano, fecha, hora) se solicita la causa de la reprogramacion.
	   .2016-03-08
			1. Se modifica el programa para mostrar la Historia e Ingreso asi el turno no esta programado para la
			   fecha de proceso.

	   .2016-02-01
			1. Se modifica el programa para grabar Codigos CUPS codificados y Otros Codigos a partir de las tablas de
			cliame (103,70 y 254).
			2. Se crea un textarea con la informacion de codigos no incluidos en la codigicacion CUPS.
			3. Se crea un textarea con la informacion de insumos y dispositivos necesarios para la cirugia.
			4. Se cambia la validacion de empresa responsable de la cuenta de tcx_000003 a cliame_000024

	   .2015-05-29
			Se modifica el programa para grabar en cero la historia y el ingreso de los pacientes inactivos en movhos_000018.
			Igualmente se pone en cero la historia y el ingreso de un paciente que cambia de fecha en la cirugia, independientemente
			de que se encuentre activo.

	   .2015-03-31
			Se modifica el programa para permitir la parametrizacion del todos los campos CHECKBOX.

	   .2015-02-24
			** Se modifico el query inicial sobre la tabla tcx 11 para hacer union entre los que hacen acceso a la tabla 3 de tcx
			y los que hacen acceso a la tabla 24 de cliame.
			Se elimino de la busqueda el item de tipo de documento.
			El la Grabacion y modificacion la eps se guarda del caracter 0 al ultimo caracter "-".

	   .2015-02-06
			** Se agrego al programa el campo en la base de dato tipo de documento Turtdo y Mcatdo y variable wtdo en las
			opciones validacion, insercion, modificacion, busqueda y cancelacion.
			Se cambio la tabla de empresas por la tabla de empresas del grupo cliame tabla 24.

	   .2015-02-03
			** Se valida la inicializacion del campo tipo de documento, para evitar errores en la identificacion de pacientes.

	   .2014-09-03
			** Se adicionan codigos CUPS autorizados y material de Alto Costo Autorizado mediante campos de control en donde
			se lleva los registros anteriores NO modificables con fecha, hora y usuario.

	   .2014-05-19
	   		** Se adicionan los campos de codigos CUPS autorizados y material de Alto Costo Autorizado en las tablas 7 y 11.

	   .2012-02-20
	   		** Se modifica en el programa para hacer los hipervinculos del grid dinamicos por empresa, ya que estaban asociados
	   		de forma exclusiva a la empresa txc.

	   .2011-11-10
	   		** Se modifica en el programa para incluir en el proceso la variable spa (Seguimiento a Paciente Ambulatorio) tanto
	   		en la grabacion del turno como en la cancelacion.

	   .2010-12-15
	   		** Se modifica en el programa todo el modulo de cancelaciones con el proposito de obtener mejor control de este proceso.
	   		se modifica tambien la seleccion de equipos ya que no estaba teniendo en cuenta los equipos inactivos.
	   		se modifica el campo de cedula para permitir caracteres como en las cedulas de extranjeria.

	   .2009-10-08
			** Se modifico en la estructura de la base de datos las tablas 7 (Movimiento de cancelaciones) y 11 (movimiento de turnos)
			para incluir los campos de paciente en preparacion (wpep), paciente en alta (wpea) y ubicacion del paciente en alta (wubi).
			Lo anterior con el proposito de crear un programa que muestre en todo el ciclo quirurgico la ubicacion del paciente desde
			su llegada a la sala hasta el alta.
			Se modifico igualmente el programa para que diera la posibilidad a este manejo, igualmente la capa de presentación se
			modifico, ubicando en el pantalla en un area resaltada el ciclo quirurgico desde la llegada del paciente hasta el alta.
			** Se implemento la grabacion automatica de comentarios en la Bitacora de movimiento hospitalario.

	   .2008-09-29
			** Se modifico el programa para permitir la digitacion de la letra P en el campo de cedula cuando esta no se conoce.

	   .2008-07-08
			** Se amplio el campo de telefonos del paciente para dar la posibilidad de digitar 2 fijos y un celular.

	   .2008-05-22
			** Se corrigio en el programa en la opcion de consulta por empresas responsables que tenia errores.
			** Se modifico la consulta para que los usuarios que no tengan privilegios para dar turnos puedan realizar
			   consultas x medico,cirugias y equipos.

	   .2008-05-21
			** Se modifico el programa en la opcion de consulta para dar la posibilidad de modificar los comentarios sin perder los
			turnos consultados. Se agrego en la celda de consulta un icono de archivador que abre el programa Cambcom.php que realiza esta
			opcion.
			** Se amplio la consulta para que se pudiera seleccionar turnos por empresa, digitando en el campo auxiliar de responsable
			una parte del nombre o el nombre completo separado por signos %.

	   .2008-04-11
			** Se corigio en el la modificacion de 2008-04-10 ya que las dos opciones de la instruccion if inhabilitaban el CheckBox.

	   .2008-04-10
			** Se modifico el programa para cambiar la propiedad de los Check Box de Disable a onclick='return false'. la propiedad
			disabled no permite el correcto funcionamiento ya que desabilita los checkbox.

	   .2008-03-12
	   		** Se modifico el programa en la funcion de modificacion de contenido del turno para agregar al los comentarios de forma especifica
	   		cambios en documento, nombre del paciente, fecha de nacimiento, telefonos, sexo, instrumentadora, tipo de cirugia, tipo de programacion,
	   		tipo de anestesia y en los check box uci, biopsia x congelacion, infectada, material, componentes sanguineos, preadmision y responsable.
	   		Los comentarios reflejaran los cambios en estas variables mostrando el valor anterior y el actual.

	   .2008-02-15
	   		** Se modifica el procedimiento para poner el estado gris (modificado despues de orden) para que solo opere despues de que el estado este en
	   		verde y no antes.
	   		** Se incluye un hipervinculo en la version que abre una ventana con el registro de cambios y modificacion del programa.
	   		** Se agrego la funcion Bisiesto (Evaluacion de años Bisiestos) que no se encontraba en el codigo.

	   .2008-02-13
	   		**Se modifico el programa para validar tanto en el ingreso como en el cambio de turno y la modificacion de contenido si la hora inicial
	   		es menor que la hora final.
	   		**Se modifico el mecanismo para poner turno modificado despues de orden solamente si el turno tiene orden y  se ha modificado el checkbox de material
	   		prendiendolo o si se ha modificado los comentarios y son diferentes a la clave "OK".
	   		Colocando en comentarios un OK el turno regresa a un estado Verde.

	   .2008-02-10
	   		**Se modifico el programa para incluir los campos de Gestion de Banco de Sangre, Preadmision, Preanestesia, Paciente en Sala,
	   		Paciente en Quirofano, Paciente en Recuperacion y datos Modificados Despues de Orden en la tabla 11 de movimiento de turnos y
	   		en la tabla 7 de movimiento de cancelaciones. Se incorporaron a la interface grafica los Checkbox de Preadmision, Paciente en Sala,
	   		Paciente en Quirofano y Paciente en Recuperacion.
	   		Los campos nuevos en la tablas 7 y 11 son :
	   		Mcabok Mcapre Mcapan Mcapes Mcapeq Mcaper Mcamdo
	   		Turbok Turpre Turpan Turpes Turpeq Turper Turmdo
	   		respectivamente.

	   		**Se incorporaron tambien al programa los colores magenta y gris que indican paciente con preadmision y datos de la cirugia modificados
	   		despues de la orden respectivamente. Un paciente modificado con orden cambia de verde a gris, para regresar a verde debe ser modificado
	   		nuevamente.

	   		**El codigo de colores del programa es el siguiente :
	   		CODIGO DE COLORES
			AZUL HORARIO DISPONIBLE
			BLANCO FUERA DE USO
			ROJO URGENTE - SIN ORDEN - SIN PREADMISION
			VERDE ESPECIAL - SIN ORDEN - SIN PREADMISION
			AZUL NORMAL - SIN ORDEN - SIN PREADMISION
			ROJO URGENTE - SIN ORDEN - CON PREADMISION
			BLANCO ESPECIAL - SIN ORDEN - CON PREADMISION
			AZUL NORMAL - SIN ORDEN - CON PREADMISION
			ROJO URGENTE - CON ORDEN - SIN MODIFICACIONES DESPUES DE LA ORDEN
			BLANCO ESPECIAL - CON ORDEN - SIN MODIFICACIONES DESPUES DE LA ORDEN
			AZUL NORMAL - CON ORDEN - SIN MODIFICACIONES DESPUES DE LA ORDEN
			ROJO URGENTE - CON ORDEN - CON MODIFICACIONES DESPUES DE LA ORDEN
			BLANCO ESPECIAL - CON ORDEN - CON MODIFICACIONES DESPUES DE LA ORDEN
			AZUL NORMAL - CON ORDEN - CON MODIFICACIONES DESPUES DE LA ORDEN

			Esta tabla se agrego al final del grid de turnos.

			**Se modifico la tabla 19 al agregarle el campo Perchk donde se encuentra una secuencia de ceros y unos separados por guion que indican
			los privilegios sobre los 11 checkbox que aparecen en la pantalla.  1 indica checkbox abilitado y 0 indica checkbox desabilitado.

			**Se modifico el algoritmo de cambio de turno quirurgico, el cual en adelante verificara de forma automatica la disponibilidad de equipos
			por grupos para reasignar otros que esten disponibles del mismo grupo o declarar la cirugia como NO VIABLE.

			**Se modifico la consulta de cirugias para poder realizarlas entre fechas, x medico, x cirugia y por equipos

			**Se modifico la validacion de la hora final ya que no permitia asignas turnos hasta las 24:00. Function validar7

	   .2008-01-04
	   		Se modifico el programa para que si el paciente esta en el nucleo deje modificar la fecha de nacimiento.

	   .2007-12-12
	   		Ultimo release Beta se corrigio el hipervinculo de las cirugias especiales y se cambio el proceso x el cual
	   		se cambian - modifican y cancelan turnos de cirugia segun la prioridad del usuario.
	   		Se modifico el color de la primera linea de la pantalla de ingreso de turnos.

	   .2007-12-10
	   		Ultimo release Beta con grabacion de anestesiologos en bloque.

	   .2007-11-19
	   		Ultimo release Beta con control de grabacion en turnos nuevos.

	   .2007-11-13
	   		Ultimo release Beta con inteligencia en equipos x cirugia.

	   .2007-05-03
	   		Release de Versión Beta.
[*DOC]
***********************************************************************************************************************/
function traerCausas($tipo){
	
	global $conex;
	global $empresa;
	
	$arrCausas = array();
	$sqlCausas = "
	SELECT Caucod, Caunom
	  FROM ".$empresa."_000024
	 WHERE Cauest = 'on'
	   AND (Cautip = '".$tipo."' OR Cautip = '*')	 
	";
	$resCausas = mysql_query($sqlCausas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausas):</b><br>".mysql_error());
	while($rowCausas = mysql_fetch_array($resCausas))
		$arrCausas[$rowCausas['Caucod']] = utf8_encode($rowCausas['Caunom']);
	
	return $arrCausas;
}

function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

function Deep(&$menu,$limit,$nivel,$exp,$empresa,$wfecha,$swiches,$origen)
{
	global $key;
	for($i=1;$i<=$limit;$i++)
	{
		if($exp % 2 == 0)
			$tipM="tipoM01";
		else
			$tipM="tipoM02";
		$blank="";
		for($j=1;$j<=$exp*2;$j++)
			$blank .= "&nbsp";
		$root=($nivel*10)+$i;
		$itemx=explode(".",$swiches);
		$itemy=array();
		for ($j=1;$j<count($itemx);$j++)
			$itemy[(integer)substr($itemx[$j],0,strpos($itemx[$j],"-"))]=substr($itemx[$j],strpos($itemx[$j],"-")+1);
		if($menu[$nivel*10+$i][0] > 0 and $itemy[$nivel*10+$i] == 1)
		{
			echo "<tr><td id=".$tipM.">".$blank."-<A HREF='/MATRIX/tcx/Procesos/Turnos.php?ok=99&root=".$root."&origen=".$origen."&swiches=".$swiches."&empresa=".$empresa."&wfecha=".$wfecha."'>".$menu[$nivel*10+$i][1]."</td></tr>";
			Deep($menu,$menu[$nivel*10+$i][0],$nivel*10+$i,$exp+1,$empresa,$wfecha,$swiches,$origen);
		}
		else
			if($menu[$nivel*10+$i][0] > 0)
				echo "<tr><td id=".$tipM.">".$blank."+<A HREF='/MATRIX/tcx/Procesos/Turnos.php?ok=99&root=".$root."&origen=".$origen."&swiches=".$swiches."&empresa=".$empresa."&wfecha=".$wfecha."'>".$menu[$nivel*10+$i][1]."</td></tr>";
			else
			{
				$blank .= "&nbsp&nbsp";
				$users=explode("-",$menu[$nivel*10+$i][3]);
				$wswu=0;
				for($u=0;$u < count($users);$u++)
					if($users[$u] == $key)
					{
						$wswu=1;
						$u=count($users);
					}
				if($wswu == 1)
					echo "<tr><td id=".$tipM.">".$blank."<A HREF='/MATRIX/tcx/".$menu[$nivel*10+$i][2]."' target='_blank'>".$menu[$nivel*10+$i][1]."</td></tr>";
				else
					echo "<tr><td id=".$tipM.">".$blank.$menu[$nivel*10+$i][1]."</td></tr>";
			}
	}
}
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return 1;
	elseif ($vec1[0] < $vec2[0])
				return -1;
			else
				return 0;
}

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function ver1($chain)
{
	if(strrpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strrpos($chain,"-"));
}
function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/";
	if (preg_match($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="/^(\+|-)?([[:digit:]]+)$/";
	if (preg_match($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="/^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$/";
	if(preg_match($fecha,$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or
		  ($occur[2] == 4 and  $occur[3] > 30) or
		  ($occur[2] == 6 and  $occur[3] > 30) or
		  ($occur[2] == 9 and  $occur[3] > 30) or
		  ($occur[2] == 11 and $occur[3] > 30) or
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="/^([=a-zA-Z0-9' 'ñÑ@?\/*#-.:;_<>])+$/";
	return (preg_match($regular,$chain));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="/^([0-9:])+$/";
	return (preg_match($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
	if(preg_match($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >23 or $occur[2]<0 or $occur[2]>59)
			return false;
		else
			return true;
	else
		return false;
}
function validar7($chain)
{
	// Funcion que permite validar la estructura de un campo Hora Especial
	$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
	if(preg_match($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >24 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}
function validar8($chain)
{
	// Funcion que permite validar la estructura de una identificacion
	$regular="/^([a-zA-Z0-9' '-])+$/";
	return (preg_match($regular,$chain));
}

function valgen($ok,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$weps,$wtel,$west,$wubi,$dataC,$NC,$dataM,$NM,$dataE,$NE,&$werr,&$e)
{
	global $empresa;
	//VALIDACION DE DATOS GENERALES
	if($wqui == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO QUIROFANO";
		$wsw=1;
	}
	if(!validar7($whin))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO HORA INICIAL O ESTA INCORRECTA";
		$wsw=1;
	}
	if(!validar7($whfi))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO HORA FINAL O ESTA INCORRECTA";
		$wsw=1;
	}
	if($whfi <= $whin)
	{
		$e=$e+1;
		$werr[$e]="ERROR EN HORARIO HORA FINAL ESTA INCORRECTA DEBE SER MAYOR A HORA INICIAL";
		$wsw=1;
	}
	if(!validar3($wfec))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO FECHA DE CIRUGIA O ESTA INCORRECTA";
	}
	if(!validar3($wndt))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO NRO DE DIAS PARA EL TURNO O ESTA INCORRECTO";
	}
	if(!validar8($wtdo))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO TIPO DOCUMENTO DE IDENTIDAD O ESTA INCORRECTO";
	}
	if(!validar8($wdoc) and strtoupper($wdoc) != "P")
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO DOCUMENTO DE IDENTIDAD O ESTA INCORRECTO";
	}
	if(!validar2($whis))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO HISTORIA O ESTA INCORRECTO";
	}
	if(!validar4($wnom))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO NOMBRE O ESTA INCORRECTO";
	}
	if(!validar3($wfna))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO FECHA DE NACIMIENTO O ESTA INCORRECTA";
	}
	if(!validar4($weps) or $weps == "0-NO APLICA"  or $weps == "-" or $weps == "SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR NO ESCOGIO RESPONSABLE";
	}
	if(!validar4($wtel))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO TELEFONO O ESTA INCORRECTO";
	}
	if(strlen($wubi) > 0 and !validar4($wubi))
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO LA UBICACION O ESTA INCORRECTO";
	}
	if($NC < 0 and $west == "on")
	{
		$e=$e+1;
		$werr[$e]="ERROR NO ESCOGIO NINGUNA CIRUGIA";
	}
	if($NM < 0 and $west == "on")
	{
		$e=$e+1;
		$werr[$e]="ERROR NO ESCOGIO NINGUN MEDICO";
	}
	/*if($NE < 0 and $west == "on")
	{
		$e=$e+1;
		$werr[$e]="ERROR NO ESCOGIO NINGUN EQUIPO";
	}*/
	$wsw=0;
	//VALIDACION DE EXISTENCIA DEL TURNO
	$query = "SELECT Turhin, Turhfi FROM ".$empresa."_000011 ";
	$query .= "where Turqui = ".substr($wqui,0,strpos($wqui,"-"));
	$query .= "  and Turfec = '".$wfec."' ";
	if($wnci != 0)
		$query .= "  and TurTur != ".$wnci;
	$query .= "  and ((Turhin <= '".$whin."'";
	$query .= "  and   Turhfi >= '".$whin."')";
	$query .= "   or  (Turhin <= '".$whfi."'";
	$query .= "  and   Turhfi >= '".$whfi."')";
	$query .= "   or  (Turhin >= '".$whin."'";
	$query .= "  and   Turhfi <= '".$whfi."'))";
	$err = mysql_query($query,$conex) or die ( mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
        	if ($row[0] != $whfi And $row[1] != $whin)
        		$wsw = 1;
        }
	}
	if($wsw > 0)
	{
		$e=$e+1;
		$werr[$e]="LOS DATOS ESTAN INCORRECTOS O YA EXISTE UN TURNO EN ESE HORARIO O EN PARTE DE EL ";
	}

	//VALIDAR MEDICO
	for ($i=0;$i<=$NM;$i++)
	{
		$wsw=0;
		$query = "SELECT Mmehin, Mmehfi  FROM ".$empresa."_000010 ";
		$query .= " where Mmefec = '".$wfec."' ";
		if($wnci != 0)
			$query .= "  and Mmetur != ".$wnci;
		$query .= "  and Mmemed = '".$dataM[$i][0]."'";
		$query .= "  and ((Mmehin <= '".$whin."'";
		$query .= "  and   Mmehfi >= '".$whin."')";
		$query .= "   or  (Mmehin <= '".$whfi."'";
		$query .= "  and   Mmehfi >= '".$whfi."')";
		$query .= "   or  (Mmehin >= '".$whin."'";
		$query .= "  and   Mmehfi <= '".$whfi."'))";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
	        	if ($row[0] != $whfi And $row[1] != $whin)
	        		$wsw = 1;
	        }
		}
		if($wsw > 0)
		{
			$e=$e+1;
			$werr[$e]="EL DR. ".$dataM[$i][1]." YA TIENE UNA CIRUGIA EN ESAS HORAS O PARTE DE ELLAS ";
		}
	}

	//VALIDACION DEL EQUIPO
	for ($i=0;$i<=$NE;$i++)
	{
		$wsw=0;
		$query = "SELECT Meqhin,Meqhfi  FROM ".$empresa."_000009 ";
		$query .= " where Meqfec = '".$wfec."' ";
		if($wnci != 0)
			$query .= "  and Meqtur != ".$wnci;
		$query .= "  and Meqequ = '".$dataE[$i][0]."'";
		$query .= "  and ((Meqhin <= '".$whin."'";
		$query .= "  and   Meqhfi >= '".$whin."')";
		$query .= "   or  (Meqhin <= '".$whfi."'";
		$query .= "  and   Meqhfi >= '".$whfi."')";
		$query .= "   or  (Meqhin >= '".$whin."'";
		$query .= "  and   Meqhfi <= '".$whfi."'))";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
	        	if ($row[0] != $whfi And $row[1] != $whin)
	        		$wsw = 1;
	        }
		}
		if($wsw > 0)
		{
			$e=$e+1;
			$werr[$e]="EL EQUIPO ".$dataE[$i][1]." YA ESTA ASIGNADO EN ESAS HORAS O PARTE DE ELLAS ";
		}
	}

	//VALIDACION DE EQUIPOS REQUERIDOS
	for ($i=0;$i<=$NC;$i++)
	{
		$query = "SELECT Excequ, Geqdes FROM ".$empresa."_000018, ".$empresa."_000016 ";
		$query .= " where Exccir = '".ver($dataC[$i][2])."' ";
		$query .= "   and Exctip = 'R' ";
		$query .= "   and Excest = 'on' ";
		$query .= "   and Excequ = Geqcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				$viable="NO";
				for ($w=0;$w<=$NE;$w++)
					if(ver($dataE[$w][2]) == $row[0])
						$viable="SI";
				if($viable == "NO")
				{
					$e=$e+1;
					$werr[$e]="LA CIRUGIA ".$dataC[$i][1]." NO TIENE ASIGNADOS EQUIPOS REQUERIDOS DEL GRUPO  ".$row[0]."-".$row[1]." REVISE !!! ";
				}
			}
		}
	}

	//VALIDAR EQUIPOS REQUERIDOS SIN CIRUGIA ASOCIADA
	for ($w=0;$w<=$NE;$w++)
		$dataE[$w][3]=0;
	for ($i=0;$i<=$NC;$i++)
	{
		$query = "SELECT Excequ FROM ".$empresa."_000018 ";
		$query .= " where Exccir = '".ver($dataC[$i][2])."'";
		$query .= "   and Exctip = 'R'";
		$query .= "   and Excest = 'on'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				$WPOS=-1;
				for ($w=0;$w<=$NE;$w++)
					if(ver($dataE[$w][2]) == $row[0])
						$WPOS=$w;
				if($WPOS > -1)
					$dataE[$WPOS][3]=1;
			}
		}
	}
	for ($w=0;$w<=$NE;$w++)
		if(substr($dataE[$w][1],0,strpos($dataE[$w][1],"-")) == "R" and $dataE[$w][3] == 0)
		{
			$e=$e+1;
			$werr[$e]="EL EQUIPO ".$dataE[$w][1]." ES REQUERIDO Y NO TIENE UNA CIRUGIA ASOCIADA REVISE !!! ";
		}

	if($e == -1)
		return true;
	else
		return false;
}

function validar_cirugias($conex,$wquix,$whinx,$whfix,$wfecx,&$dataE,&$NE,$cir,&$werr,&$e)
{
	global $empresa;
	$query = "SELECT Circpr,Cirdes from ".$empresa."_000002 where Circod='".substr($cir,0,strpos($cir,"-"))."' ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	$data=$row[0]."-".$row[1];
	$equipos=array();
	$nequ=-1;
	$viable="SI";
	$query = "SELECT Excequ FROM ".$empresa."_000018 ";
	$query .= " where Exccir = '".ver($data)."'";
	$query .= "   and Exctip = 'R'";
	$query .= "   and Excest = 'on'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$viable="NO";
			for ($w=0;$w<=$NE;$w++)
			{
				if(ver($dataE[$w][2]) == $row[0])
				{
					$viable="SI";
					$w=$NE+1;
				}
			}
			if($viable == "NO")
			{
				$query = "SELECT Equcod, Equdes, Geqcod, Geqdes FROM ".$empresa."_000004,".$empresa."_000016 ";
				$query .= " where Equgru = '".$row[0]."' ";
				$query .= "   and Equest = 'on'";
				$query .= "   and Equgru = Geqcod ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				for ($j=0;$j<$num1;$j++)
				{
					$wsw=0;
					$row1 = mysql_fetch_array($err1);
					$query = "SELECT Meqqui, Meqhin, Meqhfi, Meqfec   FROM ".$empresa."_000009 ";
					$query .= " where Meqfec = '".$wfecx."' ";
					$query .= "  and Meqequ = '".$row1[0]."'";
					$query .= "  and ((Meqhin <= '".$whinx."'";
					$query .= "  and   Meqhfi >= '".$whinx."')";
					$query .= "   or  (Meqhin <= '".$whfix."'";
					$query .= "  and   Meqhfi >= '".$whfix."')";
					$query .= "   or  (Meqhin >= '".$whinx."'";
					$query .= "  and   Meqhfi <= '".$whfix."'))";
					$err2 = mysql_query($query,$conex);
					$num2 = mysql_num_rows($err2);
					if($num2 > 0)
					{
						for ($k=0;$k<$num2;$k++)
						{
							$row2 = mysql_fetch_array($err2);
				        	if (($row2[0] != substr($wquix,0,strpos($wquix,"-")) or $row2[1] != $whinx or $row2[2] != $whfix or $row2[3] != $wfecx) And $row2[2] != $whinx And $row2[1] != $whfix)
				        		$wsw = 1;
				        }
					}
					if($wsw == 0)
			        {
				        $viable="SI";
				        $nequ = $nequ + 1;
				        $equipos[$nequ][0] = $row1[0];
				        $equipos[$nequ][1] = $row1[1];
				        $equipos[$nequ][2] = $row1[2]."-".$row1[3];
				        $equipos[$nequ][3] = "SI";
				        $j=$num1;
			        }
		        }
		        if($viable == "NO")
				{
					$query = "SELECT Geqcod, Geqdes  from ".$empresa."_000016 where Geqcod = '".$row[0]."' ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$nequ = $nequ + 1;
				        $equipos[$nequ][2] = $row1[0]."-".$row1[1];
				        $equipos[$nequ][3] = "NO";
			        }
			        $i=$num;
				}
			}
		}
	}
	if($viable == "SI")
	{
		for ($i=0;$i<=$nequ;$i++)
		{
			$NE=$NE+1;
			$dataE[$NE][0]=$equipos[$i][0];
			$dataE[$NE][1]="R-".$equipos[$i][1];
			$dataE[$NE][2]=$equipos[$i][2];
		}
		return true;
	}
	else
	{
		for ($i=0;$i<=$nequ;$i++)
		{
			if($equipos[$i][3] == "NO")
			{
				$e=$e+1;
				$werr[$e]="EL GRUPO DE EQUIPOS ".$equipos[$i][2]." NO TEINE AL MENOS UNO DISPONIBLE. LA CIRUGIA NO ES VIABLE !!!  ";
			}
		}
		return false;
	}
}

function validar_equipos_sugeridos($conex,$wquix,$whinx,$whfix,$wfecx,&$dataE,&$NE,$equ,&$werr,&$e)
{
	global $empresa;
	$equipos=array();
	$nequ=-1;
	$viable="NO";
	for ($w=0;$w<=$NE;$w++)
	{
		if(ver($dataE[$w][2]) == $equ)
		{
			$viable="SI";
			$i=$NE+1;
		}
	}
	if($viable == "NO")
	{
		$query = "SELECT Equcod, Equdes, Geqcod, Geqdes FROM ".$empresa."_000004,".$empresa."_000016 ";
		$query .= " where Equgru = '".$equ."' ";
		$query .= "   and Equest = 'on'";
		$query .= "   and Equgru = Geqcod ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		for ($j=0;$j<$num1;$j++)
		{
			$wsw=0;
			$row1 = mysql_fetch_array($err1);
			$query = "SELECT Meqqui, Meqhin, Meqhfi, Meqfec   FROM ".$empresa."_000009 ";
			$query .= " where Meqfec = '".$wfecx."' ";
			$query .= "  and Meqequ = '".$row1[0]."'";
			$query .= "  and ((Meqhin <= '".$whinx."'";
			$query .= "  and   Meqhfi >= '".$whinx."')";
			$query .= "   or  (Meqhin <= '".$whfix."'";
			$query .= "  and   Meqhfi >= '".$whfix."')";
			$query .= "   or  (Meqhin >= '".$whinx."'";
			$query .= "  and   Meqhfi <= '".$whfix."'))";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			if($num2 > 0)
			{
				for ($k=0;$k<$num2;$k++)
				{
					$row2 = mysql_fetch_array($err2);
		        	if (($row2[0] != substr($wquix,0,strpos($wquix,"-")) or $row2[1] != $whinx or $row2[2] != $whfix or $row2[3] != $wfecx) And $row2[2] != $whinx And $row2[1] != $whfix)
		        		$wsw = 1;
		        }
			}
			if($wsw == 0)
	        {
		        $viable="SI";
		        $nequ = $nequ + 1;
		        $equipos[$nequ][0] = $row1[0];
		        $equipos[$nequ][1] = $row1[1];
		        $equipos[$nequ][2] = $row1[2]."-".$row1[3];
		        $equipos[$nequ][3] = "SI";
		        $j=$num1;
	        }
        }
        if($viable == "NO")
		{
			$query = "SELECT Geqcod, Geqdes  from ".$empresa."_000016 where Geqcod = '".$row[0]."' ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$nequ = $nequ + 1;
		        $equipos[$nequ][2] = $row1[0]."-".$row1[1];
		        $equipos[$nequ][3] = "NO";
	        }
	        $i=$num;
		}
	}
	if($viable == "SI")
	{
		for ($i=0;$i<=$nequ;$i++)
		{

			$NE=$NE+1;
			$dataE[$NE][0]=$equipos[$i][0];
			$dataE[$NE][1]="S-".$equipos[$i][1];
			$dataE[$NE][2]=$equipos[$i][2];
		}
		return true;
	}
	else
	{
		for ($i=0;$i<=$nequ;$i++)
		{
			if($equipos[$i][3] == "NO")
			{
				$e=$e+1;
				$werr[$e]="EL GRUPO DE EQUIPOS ".$equipos[$i][2]." NO TEINE AL MENOS UNO DISPONIBLE. LA CIRUGIA NO ES VIABLE !!!  ";
			}
		}
		return false;
	}
}

function validar_medicos($conex,$wquix,$whinx,$whfix,$wfecx,$dataM,$NM,$med,&$werr,&$e)
{
	global $empresa;
	$data=$med;
	$med=ver($med);
	$wsw=0;
	for ($i=0;$i<=$NM;$i++)
	{
		if(ver($dataM[$i][0]) == $med)
		{
			$wsw=1;
			$i=$NM+1;
		}
	}
	if($wsw == 0)
	{
		$query = "SELECT Mmehin, Mmehfi  FROM ".$empresa."_000010 ";
		$query .= " where Mmefec = '".$wfecx."' ";
		$query .= "  and Mmemed = '".$med."'";
		$query .= "  and ((Mmehin <= '".$whinx."'";
		$query .= "  and   Mmehfi >= '".$whinx."')";
		$query .= "   or  (Mmehin <= '".$whfix."'";
		$query .= "  and   Mmehfi >= '".$whfix."')";
		$query .= "   or  (Mmehin >= '".$whinx."'";
		$query .= "  and   Mmehfi <= '".$whfix."'))";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
	        	if ($row[0] != $whfix And $row[1] != $whinx)
	        		$wsw = 1;
	        }
		}
	}
	if($wsw > 0)
	{
		$e=$e+1;
		$werr[$e]="EL DR. ".$data." YA TIENE UNA CIRUGIA EN ESAS HORAS O PARTE DE ELLAS O YA FUE SELECIONADO EN ESTE TURNO QUIRURGICO";
		return false;
	}
	else
		return true;
}

function validar_equipos($conex,$wquix,$whinx,$whfix,$wfecx,$dataE,$NE,$equ,&$werr,&$e)
{
	global $empresa;
	$data=$equ;
	$equ=ver($equ);
	$wsw=0;
	$query = "SELECT Equgru   FROM ".$empresa."_000004 ";
	$query .= " where Equcod = '".$equ."' ";
	$err = mysql_query($query,$conex);
	$row = mysql_fetch_array($err);
	$equ=$row[0];
	for ($i=0;$i<=$NE;$i++)
	{
		if(ver($dataE[$i][2]) == $equ)
		{
			$wsw=1;
			$i=$NE+1;
		}
	}
	if($wsw == 0)
	{
		$query = "SELECT Meqqui, Meqhin, Meqhfi, Meqfec   FROM ".$empresa."_000009 ";
		$query .= " where Meqfec = '".$wfecx."' ";
		$query .= "  and Meqequ = '".ver($data)."'";
		$query .= "  and ((Meqhin <= '".$whinx."'";
		$query .= "  and   Meqhfi >= '".$whinx."')";
		$query .= "   or  (Meqhin <= '".$whfix."'";
		$query .= "  and   Meqhfi >= '".$whfix."')";
		$query .= "   or  (Meqhin >= '".$whinx."'";
		$query .= "  and   Meqhfi <= '".$whfix."'))";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
	        	if (($row[0] != substr($wquix,0,strpos($wquix,"-")) or $row[1] != $whinx or $row[2] != $whfix or $row[3] != $wfecx) And $row[2] != $whinx And $row[1] != $whfix)
	        		$wsw = 1;
	        }
		}
	}
	if($wsw > 0)
	{
		$e=$e+1;
		$werr[$e]="EL EQUIPO ".$data." YA ESTA ASIGNADO EN ESAS HORAS O PARTE DE ELLAS. O OTRO EQUIPO DE ESE GRUPO YA ESTA ASIGNADO !!!  ";
		return false;
	}
	else
		return true;
}

function validar_quirofano($conex,$wquix,$whinx,$whfix,$wfecx,&$werr,&$e)
{
	global $empresa;
	$wsw=0;
	$query = "SELECT Turqui, Turhin, Turhfi, Turfec FROM ".$empresa."_000011 ";
	$query .= "where Turqui = ".substr($wquix,0,strpos($wquix,"-"));
	$query .= "  and Turfec = '".$wfecx."' ";
	$query .= "  and ((Turhin <= '".$whinx."'";
	$query .= "  and   Turhfi >= '".$whinx."')";
	$query .= "   or  (Turhin <= '".$whfix."'";
	$query .= "  and   Turhfi >= '".$whfix."')";
	$query .= "   or  (Turhin >= '".$whinx."'";
	$query .= "  and   Turhfi <= '".$whfix."'))";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
        	if (($row[0] != substr($wquix,0,strpos($wquix,"-")) or $row[1] != $whinx or $row[2] != $whfix or $row[3] != $wfecx) And $row[2] != $whinx And $row[1] != $whfix)
        		$wsw = 1;
        }
	}
	if($wsw > 0)
	{
		$e=$e+1;
		$werr[$e]="YA EXISTE UN TURNO EN ESE HORARIO O EN PARTE DE EL. REVISE !!! ";
	}
}

function validar_especial_equipos($conex,$wquix,$whinx,$whfix,$wfecx,&$dataE,&$NE,$pos)
{
	global $empresa;
	$viable="NO";
	$query = "SELECT Equcod, Equdes, Geqcod, Geqdes FROM ".$empresa."_000004,".$empresa."_000016 ";
	$query .= " where Equgru = '".substr($dataE[$pos][2],0,strpos($dataE[$pos][2],"-"))."' ";
	$query .= "   and Equest = 'on'";
	$query .= "   and Equgru = Geqcod ";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($j=0;$j<$num1;$j++)
	{
		$wsw=0;
		$row1 = mysql_fetch_array($err1);
		$query = "SELECT Meqqui, Meqhin, Meqhfi, Meqfec   FROM ".$empresa."_000009 ";
		$query .= " where Meqfec = '".$wfecx."' ";
		$query .= "  and Meqequ = '".$row1[0]."'";
		$query .= "  and ((Meqhin <= '".$whinx."'";
		$query .= "  and   Meqhfi >= '".$whinx."')";
		$query .= "   or  (Meqhin <= '".$whfix."'";
		$query .= "  and   Meqhfi >= '".$whfix."')";
		$query .= "   or  (Meqhin >= '".$whinx."'";
		$query .= "  and   Meqhfi <= '".$whfix."'))";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($k=0;$k<$num2;$k++)
			{
				$row2 = mysql_fetch_array($err2);
	        	if (($row2[0] != substr($wquix,0,strpos($wquix,"-")) or $row2[1] != $whinx or $row2[2] != $whfix or $row2[3] != $wfecx) And $row2[2] != $whinx And $row2[1] != $whfix)
	        		$wsw = 1;
	        }
		}
		if($wsw == 0)
        {
			$dataE[$pos][0]=$row1[0];
			$dataE[$pos][1]="R-".$row1[1];
			$dataE[$pos][2]=$row1[2]."-".$row1[3];
	        $j=$num1;
	        return true;
        }
    }
	return false;
}


function valcam($ok,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wquix,$whinx,$whfix,$wfecx,$dataC,$NC,$dataM,$NM,&$dataE,&$NE,&$werr,&$e, $causaModificacion)
{
	global $empresa;
	global $operativo;
	if($whfix <= $whinx)
	{
		$e=$e+1;
		$werr[$e]="ERROR EN HORARIO HORA FINAL ESTA INCORRECTA DEBE SER MAYOR A HORA INICIAL";
	}
	
	if(trim($causaModificacion) == "" && $operativo == 'on')
	{
		$e=$e+1;
		$werr[$e]="ERROR, DEBE SELECCIONAR UNA CAUSA DE MODIFICACIÓN";
	}
	
	
	$query = "select Turtur from  ".$empresa."_000011 where Turtur=".$wnci." and Turqui='".substr($wqui,0,strpos($wqui,"-"))."' and Turhin='".$whin."' and Turhfi='".$whfi."' and Turfec='".$wfec."'";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE TURNOS : ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$query = "SELECT Mcicod  from ".$empresa."_000008 where Mcitur=".$wnci;
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0 and ($num - 1) < $NC)
		{
			$NC=-1;
			$dataC=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT Ciresp, Cirdes  from ".$empresa."_000002 where Circod = '".$row[0]."' ";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$query = "SELECT Espdet  from ".$empresa."_000005 where Espcod = '".$row1[0]."' ";
				$err2 = mysql_query($query,$conex);
				$row2 = mysql_fetch_array($err2);
				$NC=$NC+1;
				$dataC[$NC][0]=$row[0];
				$dataC[$NC][1]=$row1[1];
				$dataC[$NC][2]=$row2[0];
			}
		}
		$query = "SELECT Mmemed  from ".$empresa."_000010 where Mmetur=".$wnci;
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0 and ($num - 1) < $NM)
		{
			$NM=-1;
			$dataM=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT Mednom, Medane  from ".$empresa."_000006 where Medcod = '".$row[0]."' ";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$NM=$NM+1;
				$dataM[$NM][0]=$row[0];
				$dataM[$NM][1]=$row1[0];
				$dataM[$NM][2]=$row1[1];
			}
		}
		$query = "SELECT Meqequ from ".$empresa."_000009 where Meqtur=".$wnci;
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0 and ($num - 1) < $NE)
		{
			$NE=-1;
			$dataE=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT Equdes from ".$empresa."_000004 where Equcod = '".$row[0]."' ";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$NE=$NE+1;
				$dataE[$NE][0]=$row[0];
				$dataE[$NE][1]=$row1[0];
			}
		}
		$wsw=0;
		//VALIDACION DE EXISTENCIA DEL TURNO
		$query = "SELECT Turqui, Turhin, Turhfi, Turfec FROM ".$empresa."_000011 ";
		$query .= "where Turqui = ".substr($wquix,0,strpos($wquix,"-"));
		$query .= "  and Turfec = '".$wfecx."' ";
		$query .= "  and ((Turhin <= '".$whinx."'";
		$query .= "  and   Turhfi >= '".$whinx."')";
		$query .= "   or  (Turhin <= '".$whfix."'";
		$query .= "  and   Turhfi >= '".$whfix."')";
		$query .= "   or  (Turhin >= '".$whinx."'";
		$query .= "  and   Turhfi <= '".$whfix."'))";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
	        	if (($row[0] != substr($wqui,0,strpos($wqui,"-")) or $row[1] != $whin or $row[2] != $whfi or $row[3] != $wfec) And $row[2] != $whinx And $row[1] != $whfix)
	        		$wsw = 1;
	        }
		}
		if($wsw > 0)
		{
			$e=$e+1;
			$werr[$e]="YA EXISTE UN TURNO EN ESE HORARIO O EN PARTE DE EL. EL TURNO NO PUEDE CAMBIARSE REVISE !!! ";
		}

		//VALIDAR MEDICO
		for ($i=0;$i<=$NM;$i++)
		{
			$wsw=0;
			$query = "SELECT Mmequi, Mmehin, Mmehfi, Mmefec FROM ".$empresa."_000010 ";
			$query .= " where Mmefec = '".$wfecx."' ";
			$query .= "  and Mmemed = '".$dataM[$i][0]."'";
			$query .= "  and ((Mmehin <= '".$whinx."'";
			$query .= "  and   Mmehfi >= '".$whinx."')";
			$query .= "   or  (Mmehin <= '".$whfix."'";
			$query .= "  and   Mmehfi >= '".$whfix."')";
			$query .= "   or  (Mmehin >= '".$whinx."'";
			$query .= "  and   Mmehfi <= '".$whfix."'))";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
		        	if (($row[0] != substr($wqui,0,strpos($wqui,"-")) or $row[1] != $whin or $row[2] != $whfi or $row[3] != $wfec) And $row[2] != $whinx And $row[1] != $whfix)
		        	{
		        		$wsw = 1;
	        		}
		        }
			}
			if($wsw > 0)
			{
				$e=$e+1;
				$werr[$e]="EL DR. ".$dataM[$i][1]." YA TIENE UNA CIRUGIA EN ESAS HORAS O PARTE DE ELLAS. EL TURNO NO PUEDE CAMBIARSE REVISE !!!  ";
			}
		}

		//VALIDACION DEL EQUIPO
		for ($i=0;$i<=$NE;$i++)
		{
			$wsw=0;
			$query = "SELECT Meqqui, Meqhin, Meqhfi, Meqfec   FROM ".$empresa."_000009 ";
			$query .= " where Meqfec = '".$wfecx."' ";
			$query .= "  and Meqequ = '".$dataE[$i][0]."'";
			$query .= "  and ((Meqhin <= '".$whinx."'";
			$query .= "  and   Meqhfi >= '".$whinx."')";
			$query .= "   or  (Meqhin <= '".$whfix."'";
			$query .= "  and   Meqhfi >= '".$whfix."')";
			$query .= "   or  (Meqhin >= '".$whinx."'";
			$query .= "  and   Meqhfi <= '".$whfix."'))";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
		        	if (($row[0] != substr($wqui,0,strpos($wqui,"-")) or $row[1] != $whin or $row[2] != $whfi or $row[3] != $wfec) And $row[2] != $whinx And $row[1] != $whfix)
		        		$wsw = 1;
		        }
			}
			if($wsw > 0)
			{
				if(!validar_especial_equipos($conex,$wquix,$whinx,$whfix,$wfecx,$dataE,$NE,$i))
				{
					$e=$e+1;
					$werr[$e]="EL GRUPO DE EQUIPOS ".$dataE[$i][2]." NO TEINE AL MENOS UNO DISPONIBLE. LA CIRUGIA NO ES VIABLE !!!  ";;
				}
			}
		}
	}
	else
	{
		$e=$e+1;
		$werr[$e]="EL TURNO NO EXISTE NO PUEDE SER CAMBIADO REVISE!!!!! ";
	}

	if($e == -1)
		return true;
	else
		return false;
}


// FUNCION DE CANCELACION DE TURNOS
function MCA_TUR($key,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$wsex,$wins,$wtci,$wtip,$wtan,$weps,$wuci,$wbio,$winf,$wmat,$wban,$wpre,$wpes,$wpep,$wpeq,$wper,$wpea,$wubi,$wtel,$word,$wcom,$wcups,$wmata,$wcupsa,$wmataa,$wcoma,$west,$wturc,$wturm,$wture,$wcac,$dataC,$NC,$dataM,$NM,$dataE,$NE,&$werr,&$e,$wpcan,$regcups)
{
	if(substr($wcac,0,strpos($wcac,"-")) != 0 and strlen($wpcan) > 0)
	{
		global $empresa;
		$query = "select Turtur, Turmok, Turbok, Turpan, Turmdo, Turspa, Turaud from  ".$empresa."_000011 where Turtur=".$wnci." and Turqui='".substr($wqui,0,strpos($wqui,"-"))."' and Turhin='".$whin."' and Turhfi='".$whfi."' and Turfec='".$wfec."'";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE TURNOS : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wmok=$row[1];
			$wbok=$row[2];
			$wpan=$row[3];
			$wmdo=$row[4];
			$wspa=$row[5];
			$waud=$row[6];
			$query = "SELECT Mcicod  from ".$empresa."_000008 where Mcitur=".$wnci;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wcom .= "*".$row[0];
				}
			}
			$query = "SELECT Mmemed  from ".$empresa."_000010 where Mmetur=".$wnci;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wcom .= "*".$row[0];
				}
			}
			$query = "SELECT Meqequ from ".$empresa."_000009 where Meqtur=".$wnci;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wcom .= "*".$row[0];
				}
			}
			$wcom=$wcoma.chr(10).chr(13).date("Y-m-d H:i")." |Cancelado x : ".$key."|".$wcom;
			$wcups=$wcupsa.chr(10).chr(13).date("Y-m-d H:i")." |Cancelado x : ".$key."|".$wcups;
			$wmata=$wmataa.chr(10).chr(13).date("Y-m-d H:i")." |Cancelado x : ".$key."|".$wmata;
			$query =  "DELETE  from ".$empresa."_000011 where Turtur=".$wnci." and Turqui='".substr($wqui,0,strpos($wqui,"-"))."' and Turhin='".$whin."' and Turhfi='".$whfi."' and Turfec='".$wfec."'";
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO TURNOS : ".mysql_errno().":".mysql_error());
			$query =  "DELETE  from ".$empresa."_000008 where Mcitur=".$wnci." and Mciqui='".substr($wqui,0,strpos($wqui,"-"))."' and Mcihin='".$whin."' and Mcihfi='".$whfi."' and Mcifec='".$wfec."'";
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO CIRUGIAS : ".mysql_errno().":".mysql_error());
			$query =  "DELETE  from ".$empresa."_000010 where Mmetur=".$wnci." and Mmequi='".substr($wqui,0,strpos($wqui,"-"))."' and Mmehin='".$whin."' and Mmehfi='".$whfi."' and Mmefec='".$wfec."'";
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO MEDICOS : ".mysql_errno().":".mysql_error());
			$query =  "DELETE  from ".$empresa."_000009 where Meqtur=".$wnci." and Meqqui='".substr($wqui,0,strpos($wqui,"-"))."' and Meqhin='".$whin."' and Meqhfi='".$whfi."' and Meqfec='".$wfec."'";
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO EQUIPOS : ".mysql_errno().":".mysql_error());
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000007 (medico,fecha_data,hora_data, Mcatur, Mcaqui, Mcahin, Mcahfi, Mcafec, Mcandt, Mcatdo, Mcadoc, Mcahis, Mcanin, Mcanom, Mcafna, Mcasex, Mcains, Mcatcx, Mcatip, Mcatan, Mcaeps, Mcauci, Mcabio, Mcainf, Mcamat, Mcamok, Mcaban, Mcabok, Mcapre, Mcapan, Mcapes, Mcapep, Mcapeq, Mcaper, Mcapea, Mcaubi, Mcamdo, Mcatel, Mcaord, Mcacom, Mcacir, Mcamed, Mcaequ, Mcacau, Mcapca, Mcausg, Mcausm, Mcacup, Mcamaa, Mcaspa, Mcarcu, Mcaest, Mcaaud, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $wndt."','";
			$query .=  substr($wtdo,0,strpos($wtdo,"-"))."','";
			$query .=  $wdoc."','";
			$query .=  $whis."',";
			$query .=  $wnin.",'";
			$query .=  $wnom."','";
			$query .=  $wfna."','";
			$query .=  substr($wsex,0,strpos($wsex,"-"))."','";
			$query .=  substr($wins,0,strpos($wins,"-"))."','";
			$query .=  substr($wtci,0,strpos($wtci,"-"))."','";
			$query .=  substr($wtip,0,strpos($wtip,"-"))."','";
			$query .=  substr($wtan,0,strpos($wtan,"-"))."','";
			$query .=  substr($weps,0,strpos($weps,"-"))."','";
			$query .=  $wuci."','";
			$query .=  $wbio."','";
			$query .=  $winf."','";
			$query .=  $wmat."','";
			$query .=  $wmok."','";
			$query .=  $wban."','";
			$query .=  $wbok."','";
			$query .=  $wpre."','";
			$query .=  $wpan."','";
			$query .=  $wpes."','";
			$query .=  $wpep."','";
			$query .=  $wpeq."','";
			$query .=  $wper."','";
			$query .=  $wpea."','";
			$query .=  $wubi."','";
			$query .=  $wmdo."','";
			$query .=  $wtel."','";
			$query .=  $word."','";
			$query .=  $wcom."','";
			$query .=  $wturc."','";
			$query .=  $wturm."','";
			$query .=  $wture."','";
			$query .=  substr($wcac,0,strpos($wcac,"-"))."','";
			$query .=  $wpcan."','";
			$query .=  $key."','','";
			$query .=  $wcups."','";
			$query .=  $wmata."',";
			$query .=  "'".$wspa."','".$regcups."','".$west."','".$waud."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO CANCELACION : ".mysql_errno().":".mysql_error());
			$e=$e+1;
			$werr[$e]="OK! TURNO CANCELADO";
			return true;
		}
		else
		{
			$e=$e+1;
			$werr[$e]="EL TURNO NO EXISTE NO PUEDE SER CANCELADO REVISE!!!!! ";
			return false;
		}
	}
	else
	{
		$e=$e+1;
		$werr[$e]="ERROR NO ESCOGIO CAUSA DE CANCELACION  O NO ESPECIFICO QUIEN CANCELO EL TURNO REVISE!!!!! ";
		return false;
	}
}

//FUNCION DE MODIFICACION DEL CONTENIDO DEL TURNO
function MOD_TUR($key,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$wsex,$wins,$wtci,$wtip,$wtan,$weps,$wuci,$wbio,$winf,$wmat,$wban,$wpre,$wpes,$wpep,$wpeq,$wper,$wpea,$wubi,$wtel,$word,$wcom,$wcups,$wmata,$wcupsa,$wmataa,$west,$wcoma,$wturc,$wturm,$wture,$dataC,$NC,$dataM,$NM,$dataE,$NE,&$werr,&$e,$regcups)
{
	global $wmovhos;
	global $empresa;
	//                 0        1       2      3        4      5       6      7        8       9       10      11       12     13      14      15      16      17      18      19
	$query = "select Turmat, Turord, Turdoc, Turnom, Turfna, Tursex, Turins, Turtcx, Turtip, Turtan, Turuci, Turbio, Turinf, Turmat, Turban, Turpre, Turord, Tureps, Turtdo, Turrcu from  ".$empresa."_000011 where Turtur=".$wnci." and Turqui='".substr($wqui,0,strpos($wqui,"-"))."' and Turhin='".$whin."' and Turhfi='".$whfi."' and Turfec='".$wfec."'";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE TURNOS : ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
		if(strlen($wcom) > 0 and strtoupper(substr($wcom,0,4)) == "BIT:")
		{
			$query = "select Bithis, Biting  from  ".$wmovhos."_000021 where Bithis=".$whis." and Biting='".$wnin."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE BITACORA : ".mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if ($num1 > 0)
			{
				// $empresaM="movhos";
				$wusr="1016";
				$wreg="CX";
				$wkey="TCX";
				$query = "select Connum from ".$wmovhos."_000001 where Contip='Bitacora'";
				$err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
				$row2 = mysql_fetch_array($err2);
				$wncix=$row2[0] + 1;
				$query =  " update ".$wmovhos."_000001 set Connum = Connum + 1 where Contip='Bitacora'";
				$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$wmovhos."_000021 (medico,fecha_data,hora_data, Bithis, Biting, Bitnum, Bitser, Bitobs, Bitusr, Bittem, Seguridad) values ('";
				$query .=  $wmovhos."','";
				$query .=  $fecha."','";
				$query .=  $hora."','";
				$query .=  $whis."','";
				$query .=  $wnin."',";
				$query .=  $wncix.",'";
				$query .=  $wusr."','";
				$query .=  $wcom."','";
				$query .=  $wkey."','";
				$query .=  $wreg."','C-".$wmovhos."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO BITACORA : ".mysql_errno().":".mysql_error());
				$e=$e+1;
				$werr[$e]="OK! BITACORA GRABADA";
			}
			else
			{
				$wcom .= chr(10).chr(13)."ESTE COMENTARIO NO SE REGISTRO EN LA BITACORA - EL PACIENTE NO TIENE AL MENOS UN REGISTRO EN MOVIMIENTO HOSPITALARIO CON LA HISTORIA ".$whis." E INGRESO ".$wnin."";
				$e=$e+1;
				$werr[$e]="OK! BITACORA NO!!!!! GRABADA";
			}
		}
		$wcamb="";
		if($row[2] != $wdoc)
			$wcamb .= "CAMBIO EN IDENTIFICACION DE ".$row[2]." A ".$wdoc.chr(10).chr(13);
		if($row[18] != $wtdo)
			$wcamb .= "CAMBIO EN TIPO DE DOCUMENTO ".$row[18]." A ".$wtdo.chr(10).chr(13);
		if($row[3] != $wnom)
			$wcamb .= "CAMBIO EN NOMBRE DE ".$row[3]." A ".$wnom.chr(10).chr(13);
		if($row[4] != $wfna)
			$wcamb .= "CAMBIO EN FECHA DE NACIMIENTO DE ".$row[4]." A ".$wfna.chr(10).chr(13);
		if($row[5] != substr($wsex,0,strpos($wsex,"-")))
			$wcamb .= "CAMBIO EN SEXO DE ".$row[5]." A ".substr($wsex,0,strpos($wsex,"-")).chr(10).chr(13);
		if($row[6] != substr($wins,0,strpos($wins,"-")))
			$wcamb .= "CAMBIO EN INSTRUMENTADORA DE ".$row[6]." A ".substr($wins,0,strpos($wins,"-")).chr(10).chr(13);
		if($row[7] != substr($wtci,0,strpos($wtci,"-")))
			$wcamb .= "CAMBIO EN TIPO CIRUGIA DE ".$row[7]." A ".substr($wtci,0,strpos($wtci,"-")).chr(10).chr(13);
		if($row[8] != substr($wtip,0,strpos($wtip,"-")))
			$wcamb .= "CAMBIO EN TIPO PROGRAMACION DE ".$row[8]." A ".substr($wtip,0,strpos($wtip,"-")).chr(10).chr(13);
		if($row[9] != substr($wtan,0,strpos($wtan,"-")))
			$wcamb .= "CAMBIO EN TIPO ANESTESIA DE ".$row[9]." A ".substr($wtan,0,strpos($wtan,"-")).chr(10).chr(13);
		if($row[10] != $wuci)
			$wcamb .= "CAMBIO EN UCI DE ".$row[10]." A ".$wuci.chr(10).chr(13);
		if($row[11] != $wbio)
			$wcamb .= "CAMBIO EN BIOPSIA DE ".$row[11]." A ".$wbio.chr(10).chr(13);
		if($row[12] != $winf)
			$wcamb .= "CAMBIO EN INFECTADA DE ".$row[12]." A ".$winf.chr(10).chr(13);
		if($row[13] != $wmat)
			$wcamb .= "CAMBIO EN MATERIAL DE ".$row[13]." A ".$wmat.chr(10).chr(13);
		if($row[14] != $wban)
			$wcamb .= "CAMBIO EN BANCO DE ".$row[14]." A ".$wban.chr(10).chr(13);
		if($row[15] != $wpre)
			$wcamb .= "CAMBIO EN PREADMISION DE ".$row[15]." A ".$wpre.chr(10).chr(13);
		if($row[16] != $word)
			$wcamb .= "CAMBIO EN ORDENES DE ".$row[16]." A ".$word.chr(10).chr(13);
		if($row[17] != substr($weps,0,strpos($weps,"-")))
			$wcamb .= "CAMBIO EN RESPONSABLE DE ".$row[17]." A ".substr($weps,0,strpos($weps,"-")).chr(10).chr(13);
		if($row[19] != $regcups)
			$wcamb .= "CAMBIO EN CUPS CODIFICADOS DE ".$row[19]." A ".$regcups.chr(10).chr(13);
		$wcom = $wcom.chr(10).chr(13)."------------------------------------------".chr(10).chr(13).$wcamb;
		if($wpea == "on")
		{
			$wpes="off";
			$wpep="off";
			$wpeq="off";
			$wper="off";
		}
		elseif($wper == "on")
			{
				$wpes="off";
				$wpep="off";
				$wpeq="off";
				$wpea="off";
				$wubi="";
			}
			elseif($wpeq == "on")
				{
					$wpes="off";
					$wpep="off";
					$wper="off";
					$wpea="off";
					$wubi="";
				}
				elseif($wpep == "on")
					{
						$wpes="off";
						$wpeq="off";
						$wper="off";
						$wpea="off";
						$wubi="";
					}
					elseif($wpes == "on")
						{
							$wpep="off";
							$wpeq="off";
							$wper="off";
							$wpea="off";
							$wubi="";
						}
						else
						{
							$wpes="off";
							$wpep="off";
							$wpeq="off";
							$wper="off";
							$wpea="off";
							$wubi="";
						}
		$wcomA=$wcom;
		if(strlen($wcom) > 0)
			$wcom=$wcoma.chr(10).chr(13).date("Y-m-d H:i")." : ".$wcom." |Modificado x : ".$key."|";
		else
			$wcom=$wcoma.chr(10).chr(13).date("Y-m-d H:i")." |Modificado x : ".$key."|";
		if(strlen($wcups) > 0)
			$wcups=$wcupsa.chr(10).chr(13).date("Y-m-d H:i")." : ".$wcups." |Modificado x : ".$key."|";
		else
			$wcups=$wcupsa;
		if(strlen($wmata) > 0)
			$wmata=$wmataa.chr(10).chr(13).date("Y-m-d H:i")." : ".$wmata." |Modificado x : ".$key."|";
		else
			$wmata=$wmataa;
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query =  " update ".$empresa."_000011 set Turndt = '".$wndt."',";
		$query .=  "  Turtdo = '".substr($wtdo,0,strpos($wtdo,"-"))."',";
		$query .=  "  Turdoc = '".$wdoc."',";
		$query .=  "  Turhis = '".$whis."',";
		$query .=  "  Turnin =  ".$wnin.",";
		$query .=  "  Turnom = '".$wnom."',";
		$query .=  "  Turfna = '".$wfna."',";
		$query .=  "  Tursex = '".substr($wsex,0,strpos($wsex,"-"))."',";
		$query .=  "  Turins = '".substr($wins,0,strpos($wins,"-"))."',";
		$query .=  "  Turtcx = '".substr($wtci,0,strpos($wtci,"-"))."',";
		$query .=  "  Turtip = '".substr($wtip,0,strpos($wtip,"-"))."',";
		$query .=  "  Turtan = '".substr($wtan,0,strpos($wtan,"-"))."',";
		$query .=  "  Tureps = '".substr($weps,0,strrpos($weps,"-"))."',";
		$query .=  "  Turuci = '".$wuci."',";
		$query .=  "  Turbio = '".$wbio."',";
		$query .=  "  Turinf = '".$winf."',";
		$query .=  "  Turmat = '".$wmat."',";
		$query .=  "  Turban = '".$wban."',";
		$query .=  "  Turpre = '".$wpre."',";
		$query .=  "  Turpes = '".$wpes."',";
		$query .=  "  Turpep = '".$wpep."',";
		$query .=  "  Turpeq = '".$wpeq."',";
		$query .=  "  Turper = '".$wper."',";
		$query .=  "  Turpea = '".$wpea."',";
		$query .=  "  Turubi = '".$wubi."',";
		if($word == "on" and $row[1] == "on")
		{
			if(($wmat != $row[0] and $wmat == "on") or (strlen($wcomA) > 0 and strtoupper($wcomA) != "OK"))
				$query .=  "  Turmdo = 'on',";
			elseif($wmat == $row[0] and strtoupper($wcomA) == "OK")
					$query .=  "  Turmdo = 'off',";
		}
		else
			$query .=  "  Turmdo = 'off',";
		$query .=  "  Turtel = '".$wtel."',";
		$query .=  "  Turord = '".$word."',";
		$query .=  "  Turcom = '".$wcom."',";
		$query .=  "  Turcir = '".$wturc."',";
		$query .=  "  Turmed = '".$wturm."',";
		$query .=  "  Turequ = '".$wture."',";
		$query .=  "  Turest = '".$west."',";
		$query .=  "  Turcup = '".$wcups."',";
		$query .=  "  Turmaa = '".$wmata."',";
		$query .=  "  Turrcu = '".$regcups."',";
		$query .=  "  Turusm = '".$key."' ";
		$query .=  "  where Turtur=".$wnci." and Turqui='".substr($wqui,0,strpos($wqui,"-"))."' and Turhin='".$whin."' and Turhfi='".$whfi."' and Turfec='".$wfec."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO TURNOS : ".mysql_errno().":".mysql_error());
		$query =  "DELETE  from ".$empresa."_000008 where Mcitur=".$wnci." and Mciqui='".substr($wqui,0,strpos($wqui,"-"))."' and Mcihin='".$whin."' and Mcihfi='".$whfi."' and Mcifec='".$wfec."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO CIRUGIAS : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$NC;$i++)
		{
			$query = "insert ".$empresa."_000008 (medico,fecha_data,hora_data, Mcitur, Mciqui, Mcihin, Mcihfi, Mcifec, Mcicod, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $dataC[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO CIRUGIAS : ".mysql_errno().":".mysql_error());
		}
		$query =  "DELETE  from ".$empresa."_000010 where Mmetur=".$wnci." and Mmequi='".substr($wqui,0,strpos($wqui,"-"))."' and Mmehin='".$whin."' and Mmehfi='".$whfi."' and Mmefec='".$wfec."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO MEDICOS : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$NM;$i++)
		{
			$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data, Mmetur, Mmequi, Mmehin, Mmehfi, Mmefec, Mmemed, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $dataM[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO MEDICOS : ".mysql_errno().":".mysql_error());
		}
		$query =  "DELETE  from ".$empresa."_000009 where Meqtur=".$wnci." and Meqqui='".substr($wqui,0,strpos($wqui,"-"))."' and Meqhin='".$whin."' and Meqhfi='".$whfi."' and Meqfec='".$wfec."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO EQUIPOS : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$NE;$i++)
		{
			$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data, Meqtur, Meqqui, Meqhin, Meqhfi, Meqfec, Meqequ, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $dataE[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EQUIPOS : ".mysql_errno().":".mysql_error());
		}
		$e=$e+1;
		$werr[$e]="OK! TURNO ACTUALIZADO ";
		return true;
	}
	else
	{
		$e=$e+1;
		$werr[$e]="EL TURNO NO EXISTE NO PUEDE SER MODIFICADO REVISE!!!!!";
		return false;
	}
}


//FUNCION DE CAMBIO DE TURNO
function CAM_TUR($key,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wquix,$whinx,$whfix,$wfecx,$wcom,$wcups,$wmata,$wcupsa,$wmataa,$wcoma,$dataC,$NC,$dataM,$NM,$dataE,$NE,&$werr,&$e, $causaModificacion)
{
	global $empresa;
	$query = "select Turtur from  ".$empresa."_000011 where Turtur=".$wnci." and Turqui='".substr($wqui,0,strpos($wqui,"-"))."' and Turhin='".$whin."' and Turhfi='".$whfi."' and Turfec='".$wfec."'";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE TURNOS : ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		if(strlen($wcom) > 0)
			$wcom=$wcoma.chr(10).chr(13).date("Y-m-d H:i")." : ".$wcom." |Turno Cambiado  de Qui:".substr($wqui,0,strpos($wqui,"-"))." - Hin:".$whin." - Hfi:".$whfi." - Fec:".$wfec."  a  Qui:".substr($wquix,0,strpos($wquix,"-"))." - Hin:".$whinx." - Hfi:".$whfix." - Fec:".$wfecx." x : ".$key."|";
		else
			$wcom=$wcoma.chr(10).chr(13).date("Y-m-d H:i")." |Turno Cambiado  de Qui:".substr($wqui,0,strpos($wqui,"-"))." - Hin:".$whin." - Hfi:".$whfi." - Fec:".$wfec."  a  Qui:".substr($wquix,0,strpos($wquix,"-"))." - Hin:".$whinx." - Hfi:".$whfix." - Fec:".$wfecx." x : ".$key."|";
		
		if($causaModificacion != ""){
			$sqlCausa = "
			SELECT Caunom
			  FROM ".$empresa."_000024
			 WHERE Caucod = '".$causaModificacion."' 		
			";
			$resCausa = mysql_query($sqlCausa, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausa):</b><br>".mysql_error());
			if($rowCausa = mysql_fetch_array($resCausa))
				$wcom.= " Causa de modificacion: ".utf8_encode($rowCausa['Caunom'])." |";
		}
		
		if(strlen($wcups) > 0)
			$wcups=$wcupsa.chr(10).chr(13).date("Y-m-d H:i")." : ".$wcups;
		else
			$wcups=$wcupsa;
		if(strlen($wmata) > 0)
			$wmata=$wmataa.chr(10).chr(13).date("Y-m-d H:i")." : ".$wmata;
		else
			$wmata=$wmataa;
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query =  " update ".$empresa."_000011 set ";
		$query .=  "  Turqui = '".substr($wquix,0,strpos($wquix,"-"))."',";
		$query .=  "  Turhin = '".$whinx."',";
		$query .=  "  Turhfi = '".$whfix."',";
		$query .=  "  Turfec = '".$wfecx."',";
		$query .=  "  Turcdr = '".$causaModificacion."',";
		if($wfec != $wfecx)
		{
			$query .=  "  Turhis = '0',";
			$query .=  "  Turnin = 0,";
		}
		$query .=  "  Turcom = '".$wcom."',";
		$query .=  "  Turcup = '".$wcups."',";
		$query .=  "  Turmaa = '".$wmata."',";
		$query .=  "  Turusm = '".$key."' ";
		$query .=  "  where Turtur=".$wnci." and Turqui='".substr($wqui,0,strpos($wqui,"-"))."' and Turhin='".$whin."' and Turhfi='".$whfi."' and Turfec='".$wfec."'";
		// echo $query;
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO TURNOS : ".mysql_errno().":".mysql_error());
		$query =  "DELETE  from ".$empresa."_000008 where Mcitur=".$wnci." and Mciqui='".substr($wqui,0,strpos($wqui,"-"))."' and Mcihin='".$whin."' and Mcihfi='".$whfi."' and Mcifec='".$wfec."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO CIRUGIAS : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$NC;$i++)
		{
			$query = "insert ".$empresa."_000008 (medico,fecha_data,hora_data, Mcitur, Mciqui, Mcihin, Mcihfi, Mcifec, Mcicod, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wquix,0,strpos($wquix,"-"))."','";
			$query .=  $whinx."','";
			$query .=  $whfix."','";
			$query .=  $wfecx."','";
			$query .=  $dataC[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO CIRUGIAS : ".mysql_errno().":".mysql_error());
		}
		$query =  "DELETE  from ".$empresa."_000010 where Mmetur=".$wnci." and Mmequi='".substr($wqui,0,strpos($wqui,"-"))."' and Mmehin='".$whin."' and Mmehfi='".$whfi."' and Mmefec='".$wfec."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO MEDICOS : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$NM;$i++)
		{
			$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data, Mmetur, Mmequi, Mmehin, Mmehfi, Mmefec, Mmemed, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wquix,0,strpos($wquix,"-"))."','";
			$query .=  $whinx."','";
			$query .=  $whfix."','";
			$query .=  $wfecx."','";
			$query .=  $dataM[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO MEDICOS : ".mysql_errno().":".mysql_error());
		}
		$query =  "DELETE  from ".$empresa."_000009 where Meqtur=".$wnci." and Meqqui='".substr($wqui,0,strpos($wqui,"-"))."' and Meqhin='".$whin."' and Meqhfi='".$whfi."' and Meqfec='".$wfec."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO EQUIPOS : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$NE;$i++)
		{
			$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data, Meqtur, Meqqui, Meqhin, Meqhfi, Meqfec, Meqequ, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wquix,0,strpos($wquix,"-"))."','";
			$query .=  $whinx."','";
			$query .=  $whfix."','";
			$query .=  $wfecx."','";
			$query .=  $dataE[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EQUIPOS : ".mysql_errno().":".mysql_error());
		}
		$e=$e+1;
		$werr[$e]="OK! TURNO CAMBIADO ";
		return true;
	}
	else
	{
		$e=$e+1;
		$werr[$e]="EL TURNO NO EXISTE NO PUEDE SER CAMBIADO REVISE!!!!!";
		return false;
	}
}

//FUNCION DE INGRESO DE TURNOS
function ING_TUR($key,$conex,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$wsex,$wins,$wtci,$wtip,$wtan,$weps,$wuci,$wbio,$winf,$wmat,$wban,$wpre,$wpes,$wpep,$wpeq,$wper,$wpea,$wubi,$wtel,$word,$wcom,$wcups,$wmata,$west,$wturc,$wturm,$wture,$dataC,$NC,$dataM,$NM,$dataE,$NE,&$werr,&$e,$regcups)
{
	global $empresa;
	$wsw=0;
	//VALIDACION DE EXISTENCIA DEL TURNO
	$query = "SELECT Turhin, Turhfi FROM ".$empresa."_000011 ";
	$query .= "where Turqui = ".substr($wqui,0,strpos($wqui,"-"));
	$query .= "  and Turfec = '".$wfec."' ";
	$query .= "  and ((Turhin <= '".$whin."'";
	$query .= "  and   Turhfi >= '".$whin."')";
	$query .= "   or  (Turhin <= '".$whfi."'";
	$query .= "  and   Turhfi >= '".$whfi."')";
	$query .= "   or  (Turhin >= '".$whin."'";
	$query .= "  and   Turhfi <= '".$whfi."'))";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
        	if ($row[0] != $whfi And $row[1] != $whin)
        		$wsw = 1;
        }
	}
	if($wsw == 0)
	{
		$wcom=date("Y-m-d H:i")." : ".$wcom." |Ingresado x : ".$key."|";
		$wcups=date("Y-m-d H:i")." : ".$wcups." |Ingresado x : ".$key."|";
		$wmata=date("Y-m-d H:i")." : ".$wmata." |Ingresado x : ".$key."|";
		$query = "select Parnum from ".$empresa."_000014 where Parcod='".$empresa."'";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
		$row = mysql_fetch_array($err);
		$wnci=$row[0] + 1;
		$query =  " update ".$empresa."_000014 set Parnum = Parnum + 1 where Parcod='".$empresa."'";
		$err = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000011 (medico,fecha_data,hora_data, Turtur, Turqui, Turhin, Turhfi, Turfec, Turndt, Turtdo, Turdoc, Turhis, Turnin, Turnom, Turfna, Tursex, Turins, Turtcx, Turtip, Turtan, Tureps, Turuci, Turbio, Turinf, Turmat, Turmok, Turban, Turbok, Turpre, Turpan, Turpes, Turpep, Turpeq, Turper, Turpea, Turubi, Turmdo, Turtel, Turord, Turcom, Turcir, Turmed, Turequ, Turusg, Turusm, Turcup, Turmaa, Turspa, Turrcu, Turest, Turaud, Seguridad) values ('";
		$query .=  $empresa."','";
		$query .=  $fecha."','";
		$query .=  $hora."',";
		$query .=  $wnci.",'";
		$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
		$query .=  $whin."','";
		$query .=  $whfi."','";
		$query .=  $wfec."','";
		$query .=  $wndt."','";
		$query .=  substr($wtdo,0,strpos($wtdo,"-"))."','";
		$query .=  $wdoc."','";
		$query .=  $whis."',";
		$query .=  $wnin.",'";
		$query .=  $wnom."','";
		$query .=  $wfna."','";
		$query .=  substr($wsex,0,strpos($wsex,"-"))."','";
		$query .=  substr($wins,0,strpos($wins,"-"))."','";
		$query .=  substr($wtci,0,strpos($wtci,"-"))."','";
		$query .=  substr($wtip,0,strpos($wtip,"-"))."','";
		$query .=  substr($wtan,0,strpos($wtan,"-"))."','";
		$query .=  substr($weps,0,strrpos($weps,"-"))."','";
		$query .=  $wuci."','";
		$query .=  $wbio."','";
		$query .=  $winf."','";
		$query .=  $wmat."','off','";
		$query .=  $wban."','off','";
		$query .=  $wpre."','off','";
		$query .=  $wpes."','";
		$query .=  $wpep."','";
		$query .=  $wpeq."','";
		$query .=  $wper."','";
		$query .=  $wpea."','";
		$query .=  $wubi."','off','";
		$query .=  $wtel."','";
		$query .=  $word."','";
		$query .=  $wcom."','";
		$query .=  $wturc."','";
		$query .=  $wturm."','";
		$query .=  $wture."','";
		$query .=  $key."','','";
		$query .=  $wcups."','";
		$query .=  $wmata."',";
		$query .=  "'off','".$regcups."','".$west."','off','C-".$empresa."')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO TURNOS : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$NC;$i++)
		{
			$query = "insert ".$empresa."_000008 (medico,fecha_data,hora_data, Mcitur, Mciqui, Mcihin, Mcihfi, Mcifec, Mcicod, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $dataC[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO CIRUGIAS : ".mysql_errno().":".mysql_error());
		}
		for ($i=0;$i<=$NM;$i++)
		{
			$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data, Mmetur, Mmequi, Mmehin, Mmehfi, Mmefec, Mmemed, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $dataM[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO MEDICOS : ".mysql_errno().":".mysql_error());
		}
		for ($i=0;$i<=$NE;$i++)
		{
			$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data, Meqtur, Meqqui, Meqhin, Meqhfi, Meqfec, Meqequ, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  substr($wqui,0,strpos($wqui,"-"))."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $dataE[$i][0]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EQUIPOS : ".mysql_errno().":".mysql_error());
		}
		$e=$e+1;
		$werr[$e]="OK! TURNO GRABADO";
		return true;
	}
	else
	{
		$e=$e+1;
		$werr[$e]="YA EXISTE UN TURNO EN ESE HORARIO O EN PARTE DE EL- NO SE PUEDE GRABAR EL TURNO O DECLARAR AL QUIROFANO FUERA DE USO ";
		return false;
	}
}
global $origen;
@session_start();
if(!isset($_SESSION["user"]))
	echo "error";
else
{
	// echo "<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>";
	// echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
	// echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
	// echo "<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>";	
	
	$key = substr($user,2,strlen($user));
	include_once("root/comun.php");
	$origen=$_REQUEST['origen'];
	$wactualiz = "2020-05-04";
	$institucion = consultarInstitucionPorCodigo( $conex, $origen );
	encabezado( "TURNOS EN CIRUGIA", $wactualiz, $institucion->baseDeDatos );
	$wmovhos = consultarAliasPorAplicacion($conex, $origen, "movhos");
	$wcliame = consultarAliasPorAplicacion($conex, $origen, "cliame");
	
	echo "<form name='turnos' action='turnos.php?origen=".$origen."' method=post>";
	echo "<input type='HIDDEN' NAME= 'origen' value='".$origen."'>";


	$operativo = ((!isset($operativo)) ? "off" : $operativo);			
	echo "<input type='hidden' id='operativo' name='operativo' value='".$operativo."'>";
	echo "<center><input type='HIDDEN' name='empresa' 	id='empresa' 	value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name='origen'	id='origen' 	value='".$origen."'>";
	$query = "SELECT Perniv, Perchk from ".$empresa."_000019 where Percod='".$key."' and Perest='on' ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
		$wniv=$row[0];
		$CHECKBOX=explode("-",$row[1]);
		for ($i=0;$i<13;$i++)
		{
			if($CHECKBOX[$i] == 0)
				$CHECKBOX[$i]="onclick='return false'";
			else
				$CHECKBOX[$i]="enabled";
		}
		if($ok == 99)
		{
			echo "<table border=0 align=center id=tipo5>";
			?>
			<script>
				function ira(){document.turnos.wfecha.focus();}
			</script>
			<?php
			echo "<tr><td align=center colspan=5 id=tipo19><A HREF='/MATRIX/root/Reportes/DOC.php?files=../../tcx/procesos/turnos.php?origen=".$origen."'"; //target='_blank'>Ver. 2020-05-04</A></td></tr>";
			//echo "<tr>
			//<td align=center rowspan=2><img  width='60%' height='60%' SRC='../../images/medical/root/clinica.jpg'></td>
			//<td align=center colspan=5 id=tipo14>TURNOS EN CIRUGIA</td>
			//<td align=center rowspan=2><img  width='90%' height='90%' SRC='../../images/medical/root/fmatrix.jpg'></td>
			//</tr>";
			if (!isset($wfecha))
				$wfecha=date("Y-m-d");
			$year = (integer)substr($wfecha,0,4);
			$month = (integer)substr($wfecha,5,2);
			$day = (integer)substr($wfecha,8,2);
			$nomdia=mktime(0,0,0,$month,$day,$year);
			$nomdia = strftime("%w",$nomdia);
			$wsw=0;
			switch ($nomdia)
			{
				case 0:
					$diasem = "DOMINGO";
					break;
				case 1:
					$diasem = "LUNES";
					break;
				case 2:
					$diasem = "MARTES";
					break;
				case 3:
					$diasem = "MIERCOLES";
					break;
				case 4:
					$diasem = "JUEVES";
					break;
				case 5:
					$diasem = "VIERNES";
					break;
				case 6:
					$diasem = "SABADO";
					break;
			}
			echo "<tr><td rowspan=1 bgcolor='#cccccc' align=center><b>FECHA :</b></td>";
			echo "<td rowspan=1 bgcolor='#cccccc' align=center>Dia de la Semana<br><b>".$diasem."</b></td>";
			echo "<td rowspan=1 bgcolor='#cccccc' align=center valign=center>Año - Mes - Dia<br><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td>";
			?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
			//]]></script>
			<?php
			global $wmovhos;
			global $wcliame;
			echo "<td rowspan=1 bgcolor='#cccccc'><input type='submit' value='IR'></td></tr>";
			echo "</table><br>";
			echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
			echo "<input type='HIDDEN' name= 'wniv' value='".$wniv."'>";
			echo "<table border=0 align=center id=tipo2>";
			echo "<tr><td bgcolor='#CCCCCC' rowspan=2><IMG SRC='/matrix/images/medical/tcx/procesos.png'  alt='PROCESOS'><td align=center bgcolor='#999999'>Turno Nuevo</td><td align=center bgcolor='#999999'>Consulta de Turnos</td><td align=center bgcolor='#999999'>Impresion Turnos</td><td align=center bgcolor='#999999'>Visor de Turnos</td><td align=center bgcolor='#999999'>Anestesiologos</td></tr>";
			if($wniv == 1)
				echo "<tr><td align=center bgcolor='#dddddd'><A HREF='/MATRIX/tcx/Procesos/turnos.php?ok=9&empresa=".$empresa."&wfecha=".$wfecha."&origen=".$origen."&wnew=1'><IMG SRC='/matrix/images/medical/TCX/icono.png' alt='Nuevo'></A></td><td align=center bgcolor='#dddddd'><A HREF='/MATRIX/tcx/Procesos/turnos.php?ok=9&empresa=".$empresa."&wfecha=".$wfecha."&origen=".$origen."&wnew=0'><IMG SRC='/matrix/images/medical/TCX/Consulta.png' alt='Consulta'></A></td><td align=center bgcolor='#dddddd'><A HREF='/MATRIX/tcx/Reportes/impretur.php?wemp_pmla=".$origen."&empresa=".$empresa."&wfecha=".$wfecha."&origen=".$origen."'  target='_Blank'><IMG SRC='/matrix/images/medical/TCX/Informe.png' alt='Impresion'></A></td><td align=center bgcolor='#dddddd'><A HREF='/MATRIX/tcx/Reportes/VisorT.php?wemp_pmla=".$origen."&empresa=".$empresa."&wfecha=".$wfecha."'  target='_Blank'><IMG SRC='/matrix/images/medical/TCX/Visor.png' alt='Visor'></A></td><td align=center bgcolor='#dddddd'><A HREF='/MATRIX/tcx/Procesos/Anestesiologos.php?wemp_pmla=".$origen."&empresa=".$empresa."&wfecha=".$wfecha."'  target='_Blank'><IMG SRC='/matrix/images/medical/TCX/anestesia.png' alt='Anestesia'></A></td></tr></table>";
			else
				echo "<tr><td align=center bgcolor='#dddddd'><IMG SRC='/matrix/images/medical/TCX/icono.png' alt='Nuevo'></td><td align=center bgcolor='#dddddd'><A HREF='/MATRIX/tcx/Procesos/turnos.php?ok=9&empresa=".$empresa."&wfecha=".$wfecha."&origen=".$origen."&wnew=0'><IMG SRC='/matrix/images/medical/TCX/Consulta.png' alt='Consulta'></A></td><td align=center bgcolor='#dddddd'><IMG SRC='/matrix/images/medical/TCX/Informe.png' alt='Impresion'></td><td align=center bgcolor='#dddddd'><IMG SRC='/matrix/images/medical/TCX/Visor.png' alt='Visor'></td><td align=center bgcolor='#dddddd'><IMG SRC='/matrix/images/medical/TCX/anestesia.png' alt='Anestesia'></td></tr></table>";
			$Grid=array();
			$Grid[0][0]["tur"]=0;
			$Grid[0][0]["tex"]="";
			$Grid[0][0]["bac"]=0;
			$Grid[0][0]["for"]=1;
			$Grid[0][0]["alt"]="";
			$Grid[0][0]["url"]="";
			$fase="00:00-00:30";
			for ($i=1;$i<49;$i++)
			{
				$Grid[$i][0]["tur"]=0;
				$Grid[$i][0]["tex"]=$fase;
				$Grid[$i][0]["bac"]=5;
				$Grid[$i][0]["for"]=4;
				$Grid[$i][0]["alt"]="";
				$Grid[$i][0]["url"]="";
				if(substr($fase,3,2) == "30")
				{
					$hor1=(integer)substr($fase,0,2) + 1;
					if($hor1 < 10)
						$hor1="0".$hor1;
					$min1="00";
				}
				else
				{
					$hor1=substr($fase,0,2);
					$min1="30";
				}
				if(substr($fase,9,2) == "30")
				{
					$hor2=(integer)substr($fase,6,2) + 1;
					if($hor2 < 10)
						$hor2="0".$hor2;
					$min2="00";
				}
				else
				{
					$hor2=substr($fase,6,2);
					$min2="30";
				}
				$fase=$hor1.":".$min1."-".$hor2.":".$min2;
			}
			// Quicod Quides Quiest
			$query = "SELECT Quicod,Quides  from ".$empresa."_000012 where Quiest='on' order by quicod ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$nroqui=$num;
			
			echo "<input type='hidden' id='cantQui' value='".$nroqui."'>";
			
			$arrCausas = array();
			$sqlCausas = "
			SELECT Caucod, Caunom
			  FROM ".$empresa."_000024
			 WHERE Cauest = 'on' 
			";
			$resCausas = mysql_query($sqlCausas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausas):</b><br>".mysql_error());
			while($rowCausas = mysql_fetch_array($resCausas))
				$arrCausas[$rowCausas['Caucod']] = utf8_encode($rowCausas['Caunom']);
			
			if ($num>0)
			{
				for ($i=1;$i<=$nroqui;$i++)
				{
					$row = mysql_fetch_array($err);
					$Grid[0][$i]["tur"]=0;
					$Grid[0][$i]["tex"]=$row[0];
					$Grid[0][$i]["bac"]=5;
					$Grid[0][$i]["for"]=4;
					$Grid[0][$i]["alt"]=$row[1];
					$Grid[0][$i]["url"]="";
				}
			}
			for ($i=1;$i<=48;$i++)
			{
				for ($j=1;$j<=$num;$j++)
				{
					$Grid[$i][$j]["tur"]=0;
					$Grid[$i][$j]["tex"]="";
					$Grid[$i][$j]["bac"]=1;
					$Grid[$i][$j]["for"]=1;
					$Grid[$i][$j]["alt"]="";
					$Grid[$i][$j]["url"]="";
				}
			}
			//                 0      1       2         3       4      5      6        7       8       9       10      11     12       13     14       15     16      17       18     19      20      21      22      23       24      25      26
			//$query = "SELECT Turtur, Turqui, Turhin, Turhfi, Turest, Turord, Turcir, Turtcx, Turtip, Turnom, Turfna, Turtel, Turtcx, Turtip, Turtan, Tureps, Turmed, Turequ, Turuci, Turbio, Turinf, Turmat, Turban, Turins, Entdes, Turpre, Turmdo  from ".$empresa."_000011, ".$empresa."_000003 ";
			$query  = "SELECT Turtur, Turqui, Turhin, Turhfi, Turest, Turord, Turcir, Turtcx, Turtip, Turnom, Turfna, Turtel, Turtcx, Turtip, Turtan, Tureps, Turmed, Turequ, Turuci, Turbio, Turinf, Turmat, Turban, Turins, Empnom, Turpre, Turmdo, Turepc, Turcdi, Turcdt, Turcdr  from ".$empresa."_000011, ".$wcliame."_000024 ";
			$query .= " where turfec = '".$wfecha."' ";
			$query .= "   and Tureps = Empcod ";
			$query .= " UNION ";
			$query .= " SELECT Turtur, Turqui, Turhin, Turhfi, Turest, Turord, Turcir, Turtcx, Turtip, Turnom, Turfna, Turtel, Turtcx, Turtip, Turtan, Tureps, Turmed, Turequ, Turuci, Turbio, Turinf, Turmat, Turban, Turins, Entdes, Turpre, Turmdo, Turepc, Turcdi, Turcdt, Turcdr  from ".$empresa."_000011, ".$empresa."_000003 ";
			$query .= " where turfec = '".$wfecha."' ";
			$query .= "   and Tureps = Entcod ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
                    $title = "";
                    $title .= "<div align='center' class='fila1'>
                                <table>
                                <tr class='encabezadoTabla'>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                </tr>";
					$row = mysql_fetch_array($err);
                    $msg  = "[Nombre]           : ".$row[9].chr(10).chr(13);
					$title .= "<tr><td class='subtitle'>Nombre</td><td>".$row[9]."</td></tr>";
					$msg .= "[Fecha Nac.]     : ".$row[10].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Fecha</td><td>".$row[10]."</td></tr>";
					$msg .= "[Telefono]         : ".$row[11].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Telefono</td><td>".$row[11]."</td></tr>";
					$msg .= "[Cirugia]            : ".$row[6].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Cirugia</td><td>".$row[6]."</td></tr>";
					$msg .= "[Medicos]          : ".$row[16].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Medicos</td><td>".$row[16]."</td></tr>";
					$msg .= "[Equipos]          : ".$row[17].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Equipos</td><td>".$row[17]."</td></tr>";
					$msg .= "[Responsable]  : ".$row[24].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Responsable</td><td>".$row[24]."</td></tr>";
					$query = "SELECT Seldes from ".$empresa."_000013 ";
					$query .= " where Seltip = '02' ";
					$query .= "   and Selcod = '".$row[23]."' ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$msg .= "[Instrum.]         : ".$row1[0].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Instrum</td><td>".$row1[0]."</td></tr>";
					$query = "SELECT Seldes from ".$empresa."_000013 ";
					$query .= " where Seltip = '03' ";
					$query .= "   and Selcod = '".$row[12]."' ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$msg .= "[Tipo]                : ".$row1[0].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Tipo</td><td>".$row1[0]."</td></tr>";
					$query = "SELECT Seldes from ".$empresa."_000013 ";
					$query .= " where Seltip = '04' ";
					$query .= "   and Selcod = '".$row[13]."' ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$msg .= "[Programacion] : ".$row1[0].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Programacion</td><td>".$row1[0]."</td></tr>";
					$query = "SELECT Seldes from ".$empresa."_000013 ";
					$query .= " where Seltip = '05' ";
					$query .= "   and Selcod = '".$row[14]."' ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$msg .= "[Anestesia]       : ".$row1[0].chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Anestesia</td><td>".$row1[0]."</td></tr>";
					$bool="NO";
					if($row[18] == "on")
						$bool="SI";
					$msg .= "[Uci]                  : ".$bool.chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Uci</td><td>".$bool."</td></tr>";
					$bool="NO";
					if($row[19] == "on")
						$bool="SI";
					$msg .= "[Biops.]             : ".$bool.chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Biops</td><td>".$bool."</td></tr>";
					$bool="NO";
					if($row[20] == "on")
						$bool="SI";
					$msg .= "[Infect.]            : ".$bool.chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Infect</td><td>".$bool."</td></tr>";
					$bool="NO";
					if($row[21] == "on")
						$bool="SI";
					$msg .= "[Material]          : ".$bool.chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Material</td><td>".$bool."</td></tr>";
					$bool="NO";
					if($row[22] == "on")
						$bool="SI";
					$msg .= "[Banco]             : ".$bool.chr(10).chr(13);
                    $title .= "<tr><td class='subtitle'>Banco</td><td>".$bool."</td></tr>";
					
					// --> Mostrar las causas de demora en el title
					$msjCausas = "";
					if($row['Turcdi'] != "")
						$msjCausas.= "<tr><td class='subtitle'>Demora para iniciar:</td><td>".$arrCausas[$row['Turcdi']]."</td></tr>";
					
					if($row['Turcdt'] != "")
						$msjCausas.= "<tr><td class='subtitle'>Demora para finalizar:</td><td>".$arrCausas[$row['Turcdt']]."</td></tr>";
					
					if($row['Turcdr'] != "")
						$msjCausas.= "<tr><td class='subtitle'>Reprogramación:</td><td>".$arrCausas[$row['Turcdr']]."</td></tr>";					
					
					if($msjCausas != ""){
						$title .= "
						<tr><td colspan='2' align='center' style='background-color:#FFFFA8'>
							<table width='100%'>
								<tr><td align='center' class='subtitle' colspan='2'>Causas</td></tr>
								".$msjCausas."
							</table>
						</td></tr>
						";
					}
					
                    $title .= "</table></div>";
					
					
					
					For ($k=((integer)(substr($row[2], 0, 2)) * 2 + 1 + (1 * (integer)(substr($row[2], 3, 2)) / 30));$k<=(((integer)(substr($row[3], 0, 2)) * 2 + 1 + (1 * (integer)(substr($row[3], 3, 2)) / 30)) - 1);$k++)
					{
						$Grid[$k][$row[1]]["esUrgente"] = "off";
						
						switch ($row[4]) // VERIFICACION ESTADO
						{
							case "off":
								$Grid[$k][$row[1]]["tur"]=$row[0];
								$Grid[$k][$row[1]]["tex"]="FUERA DE USO";
								$Grid[$k][$row[1]]["bac"]=2;
								$Grid[$k][$row[1]]["for"]=1;
                                $Grid[$k][$row[1]]["alt"]=$msg;
								$Grid[$k][$row[1]]["title"]=$title;
								$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
							break;
							case "on":
								switch ($row[5]) // VERIFICACION ORDEN
								{
									case "off":
										switch ($row[25]) // VERIFICACION PREADMISION
										{
											case "on":
												if($row[8] == "U") // VERIFICACION URGENTE
												{
													$Grid[$k][$row[1]]["tur"]=$row[0];
													$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
													$Grid[$k][$row[1]]["bac"]=5;
													$Grid[$k][$row[1]]["for"]=1;
                                                    $Grid[$k][$row[1]]["alt"]=$msg;
													$Grid[$k][$row[1]]["title"]=$title;
													$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													$Grid[$k][$row[1]]["esUrgente"] = "on";
												}
												else
													if($row[7] == "E") // VERIFICACION ESPECIAL
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=5;
														$Grid[$k][$row[1]]["for"]=2;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
													else
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=5;
														$Grid[$k][$row[1]]["for"]=3;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
											break;
											case "off":
												if($row[8] == "U") // VERIFICACION URGENTE
												{
													$Grid[$k][$row[1]]["tur"]=$row[0];
													$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
													$Grid[$k][$row[1]]["bac"]=3;
													$Grid[$k][$row[1]]["for"]=2;
                                                    $Grid[$k][$row[1]]["alt"]=$msg;
													$Grid[$k][$row[1]]["title"]=$title;
													$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													$Grid[$k][$row[1]]["esUrgente"] = "on";
												}
												else
													if($row[7] == "E")
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=3;
														$Grid[$k][$row[1]]["for"]=3;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
													else
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=3;
														$Grid[$k][$row[1]]["for"]=4;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
											break;
										}
									break;
									case "on":
										switch ($row[26]) // VERIFICACION DE MODIFICACNON DESPUES DE ORDEN
										{
											case "on":
												if($row[8] == "U") // VERIFICACION URGENTE
												{
													$Grid[$k][$row[1]]["tur"]=$row[0];
													$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
													$Grid[$k][$row[1]]["bac"]=6;
													$Grid[$k][$row[1]]["for"]=1;
                                                    $Grid[$k][$row[1]]["alt"]=$msg;
													$Grid[$k][$row[1]]["title"]=$title;
													$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													$Grid[$k][$row[1]]["esUrgente"] = "on";
												}
												else
													if($row[7] == "E") // VERIFICACION ESPECIAL
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=6;
														$Grid[$k][$row[1]]["for"]=2;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
													else
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=6;
														$Grid[$k][$row[1]]["for"]=3;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
											break;
											case "off":
												if($row[8] == "U") // VERIFICACION URGENTE
												{
													$Grid[$k][$row[1]]["tur"]=$row[0];
													$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
													$Grid[$k][$row[1]]["bac"]=4;
													$Grid[$k][$row[1]]["for"]=2;
                                                    $Grid[$k][$row[1]]["alt"]=$msg;
													$Grid[$k][$row[1]]["title"]=$title;
													$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													$Grid[$k][$row[1]]["esUrgente"] = "on";
												}
												else
													if($row[7] == "E") // VERIFICACION ESPECIAL
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=4;
														$Grid[$k][$row[1]]["for"]=1;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
													else
													{
														$Grid[$k][$row[1]]["tur"]=$row[0];
														$Grid[$k][$row[1]]["tex"]=substr($row[6], 0, 25);
														$Grid[$k][$row[1]]["bac"]=4;
														$Grid[$k][$row[1]]["for"]=4;
                                                        $Grid[$k][$row[1]]["alt"]=$msg;
														$Grid[$k][$row[1]]["title"]=$title;
														$Grid[$k][$row[1]]["url"]="turnos.php?ok=3&wfecha=".$wfecha."&wnci=".$row[0]."&empresa=".$empresa."&origen=".$origen."&wnew=0";
													}
											break;
										}
									break;
								}
						}
						
						// --> Estado del proceso operativo (P = En proceso, T = Terminada)
						$Grid[$k][$row[1]]["estProcesoOper"] = $row['Turepc'];
					}
				}
			}
			echo "<br><center><table border=0 align=center id=tipoG00 tablaPrincipal>";
			for ($i=0;$i<=48;$i++)
			{
				echo "<tr>";
				for ($j=0;$j<=$nroqui;$j++)
				{
					$tipo="tipoG".$Grid[$i][$j]["bac"].$Grid[$i][$j]["for"];
					if($Grid[$i][$j]["url"] != "")
					{
						echo '
						<td id="'.$tipo.'" estProcesoOper="'.$Grid[$i][$j]["estProcesoOper"].'" esUrgente="'.$Grid[$i][$j]["esUrgente"].'" numQuirofano="'.$j.'" idTurno="'.$Grid[$i][$j]["tur"].'" name="'.$tipo.'" ondblclick="ejecutar(\'/MATRIX/tcx/Procesos/InfoTur.php?wemp_pmla='.$origen.'&empresa='.$empresa.'&MENSAGE='.$Grid[$i][$j]["tur"].'\',1)" onKeyPress="ejecutar(\'/MATRIX/tcx/Reportes/sticker_TCX.php?wnci='.$Grid[$i][$j]["tur"].'\',1)" tieneTitle="on" title="'.$Grid[$i][$j]["title"].'">
							<A HREF="'.$Grid[$i][$j]["url"].'">
							<img src="/matrix/images/medical/TCX/tic.png" alt="'.$Grid[$i][$j]["alt"].'" ></A><br>'.$Grid[$i][$j]["tex"].'
						</td>'; //atl='".$Grid[$i][$j]["alt"]."'
						//echo "<td id=".$tipo." ondblclick='ejecutar(".chr(34)."/MATRIX/tcx/Procesos/InfoTur.php?empresa=".$empresa."&MENSAGE=".$Grid[$i][$j]["tur"].chr(34).",1)'><A HREF='".$Grid[$i][$j]["url"]."'><img src=/matrix/images/medical/TCX/tic.png alt='".$Grid[$i][$j]["alt"]."'></A><br>".$Grid[$i][$j]["tex"]."</td>'; //atl='".$Grid[$i][$j]["alt"]."'
					}
					else
						if($tipo == "tipoG11")
							if($wniv == 1)
								echo "<td id=".$tipo." sinTurno ><A HREF='/MATRIX/tcx/Procesos/turnos.php?ok=9&empresa=".$empresa."&origen=".$origen."&wfecha=".$wfecha."&wnew=1&wqui=".$j."&whin=".substr($Grid[$i][0]["tex"],0,strpos($Grid[$i][0]["tex"],"-"))."'>".$j."</A></td>";
							else
								echo "<td id=".$tipo." sinTurno >".$j."</td>";
						else
							if($tipo == "tipoG54")
								echo "<th class='tipoG54W'  id='ALERT[".$j."]' title='".$Grid[$i][$j]["alt"]."' onMouseMove='tooltipAlertas(".$j.")'>".$Grid[$i][$j]["tex"]."</th>";
							else
								echo "<td id=".$tipo.">".$Grid[$i][$j]["tex"]."</td>";
				}
				echo "</tr>";
			}
			echo "</table><br><br>";
			echo "<table id='tablaColores' border=0 align=left id=tipo2>";
			echo "<tr><td  colspan=2 id=tipoG001>CODIGO DE COLORES</td></tr>";
			echo "<tr><td id=tipoG11>AZUL</td><td id=tipoG000>HORARIO DISPONIBLE</td></tr>";
			echo "<tr><td id=tipoG21>BLANCO</td><td id=tipoG000>FUERA DE USO</td></tr>";
			echo "<tr><td id=tipoG32>ROJO</td><td id=tipoG000>URGENTE - SIN ORDEN - SIN PREADMISION</td></tr>";
			echo "<tr><td id=tipoG33>VERDE</td><td id=tipoG000>ESPECIAL - SIN ORDEN - SIN PREADMISION</td></tr>";
			echo "<tr><td id=tipoG34>AZUL</td><td id=tipoG000>NORMAL - SIN ORDEN - SIN PREADMISION</td></tr>";
			echo "<tr><td id=tipoG51>ROJO</td><td id=tipoG000>URGENTE - SIN ORDEN - CON PREADMISION</td></tr>";
			echo "<tr><td id=tipoG52>BLANCO</td><td id=tipoG000>ESPECIAL - SIN ORDEN - CON PREADMISION</td></tr>";
			echo "<tr><td id=tipoG53>AZUL</td><td id=tipoG000>NORMAL - SIN ORDEN - CON PREADMISION</td></tr>";
			echo "<tr><td id=tipoG42>ROJO</td><td id=tipoG000>URGENTE - CON ORDEN - SIN MODIFICACIONES DESPUES DE LA ORDEN</td></tr>";
			echo "<tr><td id=tipoG41>BLANCO</td><td id=tipoG000>ESPECIAL - CON ORDEN - SIN MODIFICACIONES DESPUES DE LA ORDEN</td></tr>";
			echo "<tr><td id=tipoG44>AZUL</td><td id=tipoG000>NORMAL - CON ORDEN - SIN MODIFICACIONES DESPUES DE LA ORDEN</td></tr>";
			echo "<tr><td id=tipoG61>ROJO</td><td id=tipoG000>URGENTE - CON ORDEN - CON MODIFICACIONES DESPUES DE LA ORDEN</td></tr>";
			echo "<tr><td id=tipoG62>BLANCO</td><td id=tipoG000>ESPECIAL - CON ORDEN - CON MODIFICACIONES DESPUES DE LA ORDEN</td></tr>";
			echo "<tr><td id=tipoG63>AZUL</td><td id=tipoG000>NORMAL - CON ORDEN - CON MODIFICACIONES DESPUES DE LA ORDEN</td></tr>";
			echo "</table></td>";
		}
		else
		{
			echo "<input type='HIDDEN' name= 'wfecha' value='".$wfecha."'>";
			echo "<input type='HIDDEN' name= 'wnew' value='".$wnew."'>";
			echo "<input type='HIDDEN' name= 'wniv' value='".$wniv."'>";
			echo "<table border=0 align=center id=tipo2>";
			//echo "<tr><td align=center colspan=5><A HREF='#Abajo'><IMG SRC='../../images/medical/root/clinica.jpg' heigth='76' with='120'></A></td></tr>";
			//******* INICIALIZACION DEL SISTEMA *********
			if(isset($ok) and $ok == 9)
				$ok=0;

			//******* GRABACION DE INFORMACION *********
			if(isset($ok) and ($ok == 2 OR $ok == 4))
			{
				$werr=array();
				$e=-1;
				if($NC < 0)
					$dataC=array();
				if($NM < 0)
					$dataM=array();
				if($NE < 0)
					$dataE=array();
				if(isset($west))
				{
					$westx="off";
					$wtdo="";
					$wdoc="0";
					$whis="0";
					$wnom="FUERA DE USO";
					$wfna="0000-00-00";
					$weps="NO-APLICA";
					$wtel=".";
					$NC=-1;
					$NM=-1;
					$NE=-1;
					$dataC=array();
					$dataM=array();
					$dataE=array();
					$wturcir="";
					$wturmed="";
					$wturequ="";
				}
				else
					$westx="on";
				if(valgen($ok,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$weps,$wtel,$westx,$wubi,$dataC,$NC,$dataM,$NM,$dataE,$NE,$werr,$e))
				{
					$wucix="off";
					if(isset($wuci))
						$wucix="on";
					$wbiox="off";
					if(isset($wbio))
						$wbiox="on";
					$winfx="off";
					if(isset($winf))
						$winfx="on";
					$wmatx="off";
					if(isset($wmat))
						$wmatx="on";
					$wbanx="off";
					if(isset($wban))
						$wbanx="on";
					$wprex="off";
					if(isset($wpre))
						$wprex="on";
					$wpesx="off";
					if(isset($wpes))
						$wpesx="on";
					$wpeqx="off";
					if(isset($wpeq))
						$wpeqx="on";
					$wperx="off";
					if(isset($wper))
						$wperx="on";
					$wpepx="off";
					if(isset($wpep))
						$wpepx="on";
					$wpeax="off";
					if(isset($wpea))
						$wpeax="on";
					$wordx="off";
					if(isset($word))
						$wordx="on";
					$query = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE, ".$empresa."_000009 LOW_PRIORITY WRITE, ";
					$query .= $empresa."_000010 LOW_PRIORITY WRITE , ".$empresa."_000011 LOW_PRIORITY WRITE, ".$empresa."_000014 LOW_PRIORITY WRITE   ";
					$query .= " ,".$wmovhos."_000001 LOW_PRIORITY WRITE , ".$wmovhos."_000021 LOW_PRIORITY WRITE  ";
					$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE ARCHIVOS : ".mysql_errno().":".mysql_error());
					if($ok == 4)
					{
						if(MOD_TUR($key,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$wsex,$wins,$wtci,$wtip,$wtan,$weps,$wucix,$wbiox,$winfx,$wmatx,$wbanx,$wprex,$wpesx,$wpepx,$wpeqx,$wperx,$wpeax,$wubi,$wtel,$wordx,$wcom,$wcups,$wmata,$wcupsa,$wmataa,$westx,$wcoma,$wturcir,$wturmed,$wturequ,$dataC,$NC,$dataM,$NM,$dataE,$NE,$werr,$e,$regcups))
						{
							$ok=0;
							unset($wqui);
							unset($whin);
						}
					}
					else
					{
						if(ING_TUR($key,$conex,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$wsex,$wins,$wtci,$wtip,$wtan,$weps,$wucix,$wbiox,$winfx,$wmatx,$wbanx,$wprex,$wpesx,$wpepx,$wpeqx,$wperx,$wpeax,$wubi,$wtel,$wordx,$wcom,$wcups,$wmata,$westx,$wturcir,$wturmed,$wturequ,$dataC,$NC,$dataM,$NM,$dataE,$NE,$werr,$e,$regcups))
						{
							$ok=0;
							unset($wqui);
							unset($whin);
						}
					}
					$query = " UNLOCK TABLES";
					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
					if($ok != 0)
						$ok = 1;
				}
				else
					$ok=1;
			}

			//******* CAMBIO DE TURNO*********
			if(isset($ok) and $ok == 5)
			{
				$werr=array();
				$e=-1;
				if($NC < 0)
					$dataC=array();
				if($NM < 0)
					$dataM=array();
				if($NE < 0)
					$dataE=array();
				if(valcam($ok,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wquix,$whinx,$whfix,$wfecx,$dataC,$NC,$dataM,$NM,$dataE,$NE,$werr,$e,$causaModificacion))
				{
					$query = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE, ".$empresa."_000009 LOW_PRIORITY WRITE,".$empresa."_000024 LOW_PRIORITY WRITE, ";
					$query .= $empresa."_000010 LOW_PRIORITY WRITE , ".$empresa."_000011 LOW_PRIORITY WRITE, ".$empresa."_000014 LOW_PRIORITY WRITE   ";
					$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE ARCHIVOS : ".mysql_errno().":".mysql_error());
					if(CAM_TUR($key,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wquix,$whinx,$whfix,$wfecx,$wcom,$wcups,$wmata,$wcupsa,$wmataa,$wcoma,$dataC,$NC,$dataM,$NM,$dataE,$NE,$werr,$e,$causaModificacion))
						$ok=0;
					$query = " UNLOCK TABLES";
					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
					if($ok != 0)
						$ok = 1;
				}
				else
					$ok=1;
			}

			//******* CANCELACION DEL TURNO*********
			if(isset($ok) and $ok == 6)
			{
				$werr=array();
				$e=-1;
				if($NC < 0)
					$dataC=array();
				if($NM < 0)
					$dataM=array();
				if($NE < 0)
					$dataE=array();
				$wucix="off";
				if(isset($wuci))
					$wucix="on";
				$wbiox="off";
				if(isset($wbio))
					$wbiox="on";
				$winfx="off";
				if(isset($winf))
					$winfx="on";
				$wmatx="off";
				if(isset($wmat))
					$wmatx="on";
				$wbanx="off";
				if(isset($wban))
					$wbanx="on";
				$wprex="off";
				if(isset($wpre))
					$wprex="on";
				$wpesx="off";
				if(isset($wpes))
					$wpesx="on";
				$wpeqx="off";
				if(isset($wpeq))
					$wpeqx="on";
				$wperx="off";
				if(isset($wper))
					$wperx="on";
				$wpepx="off";
				if(isset($wpep))
					$wpepx="on";
				$wpeax="off";
				if(isset($wpea))
					$wpeax="on";
				$wordx="off";
				if(isset($word))
					$wordx="on";
				$west="off";
				$query = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE, ".$empresa."_000009 LOW_PRIORITY WRITE, ";
				$query .= $empresa."_000010 LOW_PRIORITY WRITE , ".$empresa."_000011 LOW_PRIORITY WRITE, ".$empresa."_000014 LOW_PRIORITY WRITE, ".$empresa."_000007 LOW_PRIORITY WRITE    ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE ARCHIVOS : ".mysql_errno().":".mysql_error());
				if(MCA_TUR($key,$conex,$wnci,$wqui,$whin,$whfi,$wfec,$wndt,$wtdo,$wdoc,$whis,$wnin,$wnom,$wfna,$wsex,$wins,$wtci,$wtip,$wtan,$weps,$wucix,$wbiox,$winfx,$wmatx,$wbanx,$wprex,$wpesx,$wpepx,$wpeqx,$wperx,$wpeax,$wubi,$wtel,$wordx,$wcom,$wcups,$wmata,$wcupsa,$wmataa,$wcoma,$west,$wturcir,$wturmed,$wturequ,$wcac,$dataC,$NC,$dataM,$NM,$dataE,$NE,$werr,$e,$wpcan,$regcups))
					$ok=0;
				$query = " UNLOCK TABLES";
				$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
				if($ok != 0)
					$ok = 1;
			}

			//******* INICIALIZACION DE CAMPOS *********
			if(isset($ok) and $ok == 0)
			{
				$wnci=0;
				if(!isset($wqui) or $wnew == 0)
					$wqui="0-SELECCIONE";
				else
					$wqui=$wqui."-QUIROFANO ".$wqui;
				$wquix="";
				if(!isset($whin) or $wnew == 0)
				{
					$whin="SELECCIONE";
					$whfi="SELECCIONE";
				}
				else
				{
					if(substr($whin,3,2) == "30")
					{
						$hor1=(integer)substr($whin,0,2) + 1;
						if($hor1 < 10)
							$hor1="0".$hor1;
						$min1="00";
					}
					else
					{
						$hor1=substr($whin,0,2);
						$min1="30";
					}
					$whfi=$hor1.":".$min1;
				}
				if(isset($wfecha))
					$wfec=$wfecha;
				else
					$wfec=date("Y-m-d");
				$whinx="";
				$whfix="";
				$wfecx=date("Y-m-d");
				$wndt=date("Y-m-d");
				$wdoc="";
				$wtdo="";
				$whis="0";
				$wnin=0;
				$wnom="";
				$wfna=date("Y-m-d");
				$weda=0;
				$wsex="";
				$wins="";
				$wtci="";
				$wtip="";
				$wtan="";
				$weps="";
				$wepsw="";
				unset($wuci);
				unset($wbio);
				unset($winf);
				unset($wmat);
				unset($wban);
				unset($wpre);
				unset($wpes);
				unset($wpeq);
				unset($wper);
				$wtel="";
				unset($word);
				unset($west);
				unset($wpep);
				unset($wpea);
				$wubi="";
				$wcom="";
				$wcoma="";
				$wcups="";
				$wmata="";
				$wcupsa="";
				$wmataa="";
				$regcups="";
				unset($qa);
				$wpos=0;
				$ok=1;
				$querys="";
				$NC=-1;
				$NM=-1;
				$NE=-1;
				$dataC=array();
				$dataM=array();
				$dataE=array();
			}

			//*******CONSULTA DE INFORMACION *********
			global $wcliame;
			if(isset($ok)  and $ok == 3)
			{
				if(!isset($whin))
					$whin = "";
				if(!isset($whfi))
					$whfi = "";
				if(!isset($wfec))
					$wfec = "";
				if(!isset($wtdo))
					$wtdo = "";
				if(!isset($wdoc))
					$wdoc = "";
				if(!isset($whis))
					$whis = "";
				if(!isset($wnom))
					$wnom = "";
				if(!isset($wtel))
					$wtel = "";
				if(isset($querys) and $querys != "")
				{
					$querys=stripslashes($querys);
					$qa=$querys;
				}
				else
				{
					if(isset($wcom) and strtoupper(substr($wcom,0,4)) == "EPS:")
					{
						//$query = "SELECT Entcod, Entdes  from ".$empresa."_000003 where Entdes like '%".substr($wcom,4)."%' and Entest='on'  order by Entdes";
						$query = "SELECT Empcod, Empnom  from ".$wcliame."_000024 where Empnom like '%".substr($wcom,4)."%' and Empest='on'  order by Empnom";

						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						if ($num>0)
						{
							$row = mysql_fetch_array($err);
							$win="(".chr(34).$row[0].chr(34);
							for ($i=1;$i<$num;$i++)
							{
								$row = mysql_fetch_array($err);
								$win .= ",".chr(34).$row[0].chr(34);
							}
							$win .= ")";
						}
					}
					            //       0       1       2       3       4       5       6      7       8       9       10      11      12      13       14     15      16      17      18      19      20      21      22      23      24      25      26      27      28      29       30     31      32      33      34      35      36      37      38      39
		            $querys = "select Turtur, Turqui, Turhin, Turhfi, Turfec, Turndt, Turdoc, Turhis, Turnin, Turnom, Turfna, Tursex, Turins, Turtcx, Turtip, Turtan, Tureps, Turuci, Turbio, Turinf, Turmat, Turban, Turbok, Turpre, Turpan, Turpes, Turpeq, Turper, Turmdo, Turtel, Turord, Turcom, Turest, Turpep, Turpea, Turubi, Turcup, Turmaa, Turtdo, Turrcu from  ".$empresa."_000011 ";
					$querys .= " where Turtur != 0 ";
					if($wnci != 0)
						$querys .= "     and Turtur=".$wnci."  ";
					if($whin != "SELECCIONE" and $whin != "")
						$querys .= "     and Turhin>='".$whin."'";
					if($whfi != "SELECCIONE" and $whfi != "")
						$querys .= "     and Turhfi<='".$whfi."'";
					if($wfec != "")
						if(strpos($wfec,":") !== false)
							$querys .= "     and Turfec between '".substr($wfec,0,strpos($wfec,":"))."' and '".substr($wfec,strpos($wfec,":") + 1)."'";
						else
							$querys .= "     and Turfec='".$wfec."'";
					//if($wtdo != "")
						//$querys .= "     and Turtdo='".$wtdo."'";
					if($wdoc != "")
						$querys .= "     and Turdoc='".$wdoc."'";
					if($whis != 0)
						$querys .= "     and Turhis='".$whis."'";
					if($wnom != "")
						$querys .= "     and Turnom like '%".$wnom."%'";
					if($wtel != "")
						$querys .= "     and Turtel like '%".$wtel."%'";
					if(isset($wcom) and $wcom != "" and !isset($x) and strtoupper(substr($wcom,0,4)) != "EPS:" and strtoupper(substr($wcom,0,4)) != "CIR:" and strtoupper(substr($wcom,0,4)) != "MED:" and strtoupper(substr($wcom,0,4)) != "EQU:")
						$querys .= "     and Turcom like '%".$wcom."%'";
					if(isset($x) and $x == 0 and isset($wcom) and strtoupper(substr($wcom,0,4)) != "EPS:" and strtoupper(substr($wcom,0,4)) != "CIR:" and strtoupper(substr($wcom,0,4)) != "MED:" and strtoupper(substr($wcom,0,4)) != "EQU:")
						$querys .= "     and Turcir like '%".$wcom."%'";
					if(isset($wcom) and strtoupper(substr($wcom,0,4)) == "CIR:")
						$querys .= "     and Turcir like '%".substr($wcom,4)."%'";
					if(isset($x) and $x == 1 and isset($wcom) and strtoupper(substr($wcom,0,4)) != "EPS:" and strtoupper(substr($wcom,0,4)) != "CIR:" and strtoupper(substr($wcom,0,4)) != "MED:" and strtoupper(substr($wcom,0,4)) != "EQU:")
						$querys .= "     and Turmed like '%".$wcom."%'";
					if(isset($wcom) and strtoupper(substr($wcom,0,4)) == "MED:")
						$querys .= "     and Turmed like '%".substr($wcom,4)."%'";
					if(isset($x) and $x == 2 and isset($wcom) and strtoupper(substr($wcom,0,4)) != "EPS:" and strtoupper(substr($wcom,0,4)) != "CIR:" and strtoupper(substr($wcom,0,4)) != "MED:" and strtoupper(substr($wcom,0,4)) != "EQU:")
						$querys .= "     and Turequ like '%".$wcom."%'";
					if(isset($wcom) and strtoupper(substr($wcom,0,4)) == "EQU:")
						$querys .= "     and Turequ like '%".substr($wcom,4)."%'";
					if(isset($win))
						$querys .= "     and Tureps in ".$win." ";
					$querys .=" Order by  Turfec, Turqui, Turhin  ";
					$err = mysql_query($querys,$conex);
					$numero = mysql_num_rows($err);
					$numero=$numero - 1;
				}
				if ($numero>=0)
				{
					if(isset($wposs) and $wposs != 0)
					{
						$wpos = $wposs - 1;
						if ($wpos < 0)
							$wpos=0;
						if ($wpos > $numero)
							$wpos=$numero;
						$wposs=0;
					}
					if(isset($qa))
					{
						$qa=str_replace(chr(34),chr(39),$qa);
						$qa=substr($qa,0,strpos($qa," limit "));
						$querys=$qa;
					}
					if(isset($qa) and $qa == $querys)
					{
						if(isset($wb) and $wb == 1)
						{
							unset($querysR);
							$wpos = $wpos  + 1;
							if ($wpos > $numero)
								$wpos=$numero;
						}
						elseif(isset($wb) and $wb == 2)
						{
							unset($querysR);
							$wpos = $wpos - 1;
							if ($wpos < 0)
								$wpos=0;
						}
					}
					else
						$wpos=0;
					$wp=$wpos+1;
					//echo "Registro Nro : ".$wpos."<br>";
					$querys .=  " limit ".$wpos.",1";
					$err = mysql_query($querys,$conex);
					$querys=str_replace(chr(39),chr(34),$querys);
					echo "<input type='HIDDEN' name= 'querys' value='".$querys."'>";
					echo "<input type='HIDDEN' name= 'wpos' value='".$wpos."'>";
					echo "<input type='HIDDEN' name= 'numero' value='".$numero."'>";
					$row = mysql_fetch_array($err);
					$wnci=$row[0];
					$query = "SELECT Quicod, Quides from ".$empresa."_000012 where Quicod ='".$row[1]."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$wqui=$row1[0]."-".$row1[1];
					$wquix=$wqui;
					$whin=$row[2];
					$whfi=$row[3];
					$wfec=$row[4];
					$whinx=$whin;
					$whfix=$whfi;
					$wfecx=$wfec;
					$wndt=$row[5];
					$wtdo=$row[38];
					$wdoc=$row[6];
					$whis=$row[7];
					$wnin=$row[8];
					$wnom=$row[9];
					$wfna=$row[10];
					$ann=(integer)substr($wfna,0,4)*360 +(integer)substr($wfna,5,2)*30 + (integer)substr($wfna,8,2);
					$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
					$weda=(integer)(($aa - $ann)/360);
					$weda=number_format((double)$weda,0,'.','');
					$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='01' and Selcod ='".$row[11]."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$wsex=$row1[0]."-".$row1[1];
					$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='02' and Selcod ='".$row[12]."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$wins=$row1[0]."-".$row1[1];
					$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='03' and Selcod ='".$row[13]."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$wtci=$row1[0]."-".$row1[1];
					$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='04' and Selcod ='".$row[14]."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$wtip=$row1[0]."-".$row1[1];
					$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='05' and Selcod ='".$row[15]."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$wtan=$row1[0]."-".$row1[1];
					//$query = "SELECT Entcod, Entdes  from ".$empresa."_000003 where Entcod ='".$row[16]."' ";
					$query = "SELECT Empcod, Empnom  from ".$wcliame."_000024 where Empcod ='".$row[16]."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$weps=$row1[0]."-".$row1[1];
					$wepsw="";
					if($row[17] == "on")
						$wuci="on";
					else
						unset($wuci);
					if($row[18] == "on")
						$wbio="on";
					else
						unset($wbio);
					if($row[19] == "on")
						$winf="on";
					else
						unset($winf);
					if($row[20] == "on")
						$wmat="on";
					else
						unset($wmat);
					if($row[21] == "on")
						$wban="on";
					else
						unset($wban);
					if($row[23] == "on")
						$wpre="on";
					else
						unset($wpre);
					if($row[25] == "on")
						$wpes="on";
					else
						unset($wpes);
					if($row[26] == "on")
						$wpeq="on";
					else
						unset($wpeq);
					if($row[27] == "on")
						$wper="on";
					else
						unset($wper);
					$wtel=$row[29];
					if($row[30] == "on")
						$word="on";
					else
						unset($word);
					$wcoma=$row[31];
					$wcom="";
					$wcupsa=$row[36];
					$wmataa=$row[37];
					$wcups="";
					$regcups=$row[39];
					$wmata="";
					if($row[32] == "off")
						$west="on";
					else
						unset($west);
					if($row[33] == "on")
						$wpep="on";
					else
						unset($wpep);
					if($row[34] == "on")
						$wpea="on";
					else
						unset($wpea);
					$wubi=$row[35];
					$NC=-1;
					$NM=-1;
					$NE=-1;
					$dataC=array();
					$dataM=array();
					$dataE=array();
					$query = "SELECT Mcicod  from ".$empresa."_000008 where Mcitur=".$wnci;
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$query = "SELECT Cirdes, Circpr, Gcides  from ".$empresa."_000002,".$empresa."_000017 where Circod = '".$row[0]."' and Circpr = Gcicod ";
							$err1 = mysql_query($query,$conex);
							$row1 = mysql_fetch_array($err1);
							$NC=$NC+1;
							$dataC[$NC][0]=$row[0];
							$dataC[$NC][1]=$row1[0];
							$dataC[$NC][2]=$row1[1]."-".$row1[2];
						}
					}
					$query = "SELECT Mmemed from ".$empresa."_000010 where Mmetur=".$wnci;
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$query = "SELECT Mednom, Medesp, Espdet  from ".$empresa."_000006, ".$empresa."_000005 where Medcod = '".$row[0]."' and Medesp = Espcod";
							$err1 = mysql_query($query,$conex);
							$row1 = mysql_fetch_array($err1);
							$NM=$NM+1;
							$dataM[$NM][0]=$row[0];
							$dataM[$NM][1]=$row1[0];
							$dataM[$NM][2]=$row1[1]."-".$row1[2];
						}
					}
					$query = "SELECT Meqequ from ".$empresa."_000009 where Meqtur=".$wnci;
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$query = "SELECT Equdes, Geqcod, Geqdes  from ".$empresa."_000004,".$empresa."_000016 where Equcod = '".$row[0]."' and Equgru = Geqcod";
							$err1 = mysql_query($query,$conex);
							$row1 = mysql_fetch_array($err1);
							$NE=$NE+1;
							$dataE[$NE][0]=$row[0];
							$dataE[$NE][1]=$row1[0];
							$dataE[$NE][2]=$row1[1]."-".$row1[2];
						}
					}
					for ($i=0;$i<=$NC;$i++)
					{
						$query = "SELECT Excequ FROM ".$empresa."_000018 ";
						$query .= " where Exccir = '".ver($dataC[$i][2])."'";
						$query .= "   and Exctip = 'R'";
						$query .= "   and Excest = 'on'";
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						if($num > 0)
						{
							for ($j=0;$j<$num;$j++)
							{
								$row = mysql_fetch_array($err);
								$WPOS=-1;
								for ($w=0;$w<=$NE;$w++)
									if(ver($dataE[$w][2]) == $row[0])
										$WPOS=$w;
								if($WPOS > -1)
									if(strpos($dataE[$WPOS][1],"-") === false)
										$dataE[$WPOS][1]="R-".$dataE[$WPOS][1];
							}
						}
					}
					for ($w=0;$w<=$NE;$w++)
						if(strpos($dataE[$w][1],"-") === false)
							$dataE[$w][1]="S-".$dataE[$w][1];
				}
				else
				{
					$wnci=0;
					$wqui="0-SELECCIONE";
					$wquix="";
					$whin="SELECCIONE";
					$whfi="SELECCIONE";
					$wfec=date("Y-m-d");
					$whinx="";
					$whfix="";
					$wfecx=date("Y-m-d");
					$wndt=date("Y-m-d");
					$wdoc="";
					$wtdo="";
					$whis="0";
					$wnin=0;
					$wnom="";
					$wfna=date("Y-m-d");
					$weda=0;
					$wsex="";
					$wins="";
					$wtci="";
					$wtip="";
					$wtan="";
					$weps="";
					$wepsw="";
					unset($wuci);
					unset($wbio);
					unset($winf);
					unset($wmat);
					unset($wban);
					unset($wpre);
					unset($wpes);
					unset($wpeq);
					unset($wper);
					$wtel="";
					unset($word);
					unset($west);
					unset($wpep);
					unset($wpea);
					$wubi="";
					$wcom="";
					$wcoma="";
					$wcups="";
					$wmata="";
					$wcupsa="";
					$wmataa="";
					$regcups="";
					unset($qa);
					$wpos=0;
					$ok=1;
					$querys="";
					$NC=-1;
					$NM=-1;
					$NE=-1;
					$dataC=array();
					$dataM=array();
					$dataE=array();
				}
				if(isset($wp))
				{
					$n=$numero +1 ;
					echo "<tr><td align=right colspan=5><font size=2><b>Registro Nro. ".$wp." De ".$n."</b></font></td></tr>";
				}
				else
					echo "<tr><td align=right colspan=5><font size=2 color='#CC0000'><b>Consulta Sin Registros</b></font></td></tr>";
			}

			//*******PROCESO DE INFORMACION *********

			//********************************************************************************************************
			//*                                         DATOS DEL TURNO                                              *
			//********************************************************************************************************


			if(isset($wqui) and $wqui != "0-SELECCIONE" and isset($whin) and $whin != "SELECCIONE" and isset($whfi) and $whfi != "SELECCIONE" and isset($ok) and $ok == 1)
			{
				if(!isset($werr))
				{
					$werr=array();
					$e=-1;
				}
				validar_quirofano($conex,$wqui,$whin,$whfi,$wfec,$werr,$e);
			}

			if(isset($x1) and isset($ok) and $ok == 1)
			{
				if(!isset($wdat))
					$wdat="";
				switch ($x1)
				{
					case 0:
						//VALIDACION DE CIRUGIAS
						if($NE < 0)
							$dataE=array();
						if(!validar_cirugias($conex,$wqui,$whin,$whfi,$wfec,$dataE,$NE,$wdat,$werr,$e))
							$wdat="NULL-NULL";
					break;
					case 1:
						//VALIDACION DE MEDICOS
						if($NM < 0)
							$dataM=array();
						if(!validar_medicos($conex,$wqui,$whin,$whfi,$wfec,$dataM,$NM,$wdat,$werr,$e))
							$wdat="NULL-NULL";
					break;
					case 2:
						//VALIDACION DE EQUIPOS
						if($NE < 0)
							$dataE=array();
						if(!validar_equipos($conex,$wqui,$whin,$whfi,$wfec,$dataE,$NE,$wdat,$werr,$e))
							$wdat="NULL-NULL";
					break;
					break;
				}
			}
			if(isset($su))
			{
				//VALIDACION DE EQUIPOS
				for ($i=0;$i<=$NES;$i++)
				{
					if(isset($agrS[$i]))
					{
						$wdat=$sugeridos[$i][0];
						if($NE < 0)
							$dataE=array();
						if(!validar_equipos_sugeridos($conex,$wqui,$whin,$whfi,$wfec,$dataE,$NE,$wdat,$werr,$e))
							$wdat="NULL-NULL";
					}
				}
			}
			echo "<tr><td align=center colspan=5 id=tipo19><A HREF='/MATRIX/root/Reportes/DOC.php?files=../../tcx/procesos/turnos.php?origen=".$origen."'";// target='_blank'>Ver. 2020-05-04</A></td></tr>";
			//echo "<tr><td align=center colspan=5 id=tipo14>TURNOS EN CIRUGIA </td></tr>";
			$color="#dddddd";
			$color1="#000099";
			$color2="#006600";
			$color3="#cc0000";
			$color4="#CC99FF";
			$color5="#99CCFF";
			$color6="#FF9966";
			$color7="#cccccc";

			echo "<tr><td align=center bgcolor=#999999 colspan=5><b>DATOS DEL TURNO</b></td></tr>";
			//PRIMERA LINEA
			echo "<tr>";
			if($wnci == 0)
				echo "<td bgcolor=".$color7." align=center>*Cirugia Nro. :<br><input type='TEXT' name='wnci' size=6 maxlength=12 value='".$wnci."' class=tipo3></td>";
			else
				echo "<td bgcolor=".$color7." align=center>*Cirugia Nro. :<br><input type='TEXT' name='wnci' readonly='readonly' size=6 maxlength=12 value='".$wnci."' class=tipo3></td>";
			echo "<td bgcolor=".$color7." align=center>Quirofano : <br>";
			$query = "SELECT Quicod, Quides  from ".$empresa."_000012 where Quiest='on' order by Quicod";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			echo "<select name='wqui' id=tipo1>";
			echo "<option>0-SELECCIONE</option>";
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wqui=ver($wqui);
					if($wqui == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
			echo "</select>";
			echo"</td>";
			echo "<td bgcolor=".$color7." align=center>&nbsp*Hora Inicial - *Hora Final  <br> ";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp<select name='whin' id=tipo1>";
			echo "<option>SELECCIONE</option>";
			$fase="00:00";
			for ($i=1;$i<49;$i++)
			{
				if($whin == $fase)
					echo "<option selected>".$fase."</option>";
				else
					echo "<option>".$fase."</option>";
				if(substr($fase,3,2) == "30")
				{
					$hor1=(integer)substr($fase,0,2) + 1;
					if($hor1 < 10)
						$hor1="0".$hor1;
					$min1="00";
				}
				else
				{
					$hor1=substr($fase,0,2);
					$min1="30";
				}
				$fase=$hor1.":".$min1;
			}
			echo "</select>&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "<select name='whfi' id=tipo1 OnChange='enter()'>";
			echo "<option>SELECCIONE</option>";
			$fase="00:30";
			for ($i=1;$i<49;$i++)
			{
				if($whfi == $fase)
					echo "<option selected>".$fase."</option>";
				else
					echo "<option>".$fase."</option>";
				if(substr($fase,3,2) == "30")
				{
					$hor1=(integer)substr($fase,0,2) + 1;
					if($hor1 < 10)
						$hor1="0".$hor1;
					$min1="00";
				}
				else
				{
					$hor1=substr($fase,0,2);
					$min1="30";
				}
				$fase=$hor1.":".$min1;
			}
			echo "</select>";
			echo"</td>";
			echo "<td bgcolor=".$color7." align=center valign=center>*Fecha Cirugia : <br><input type='TEXT' name='wfec' size=10 maxlength=21 id='wfec' value='".$wfec."' class=tipo3>&nbsp&nbsp<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td>";
			?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
			//]]></script>
			<?php
			echo "<td bgcolor=".$color7." align=center>Fecha Pedido CX : <br><input type='TEXT' name='wndt' size=10 maxlength=10 value='".$wndt."' class=tipo3>&nbsp&nbsp<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger4'></td>";
			?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wndt',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
			//]]></script>
			<?php
			echo "</tr>";
			//SEGUNDA LINEA
			echo "<tr>";
			echo "<td bgcolor=".$color." align=center>*Documento : <br>";
			$query = "SELECT Selcod, Seldes  from clisur_000105 where Seltip='01' and Selest='on'  order by Selpri";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			echo "<select name='wtdo' id=tipo1>";
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wtdo=ver($wtdo);
					if(isset($wtdo) and $wtdo == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
			echo "<br><input type='TEXT' name='wdoc' size=12 maxlength=12  value='".$wdoc."' class=tipo3 Onblur='enter()'></td>";
			//                  0       1       2       3       4       5      6       7

			if(!isset($wtdo))
				$query = "SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Orihis, Oriing  from root_000036, root_000037 where Pacced='".$wdoc."' and Pacced=Oriced and Oriori='".$origen."' order by Oriori";
			else
				$query = "SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Orihis, Oriing  from root_000036, root_000037 where Pacced='".$wdoc."' and Pactid='".$wtdo."' and Pacced=Oriced and Pactid=Oritid and Oriori='".$origen."' order by Oriori";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			global $wmovhos;
			if ($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wnom=$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
				if(isset($wfna) and ($wfna == $row[4] or $wfna == date("Y-m-d")))
					$wfna=$row[4];
				if($row[5] == "M")
					$wsex="M-MASCULINO";
				else
					$wsex="F-FEMENINO";
				$query = "SELECT Ubihis, Ubiing from ".$wmovhos."_000018 where Ubihis='".$row[6]."' and Ubiing='".$row[7]."' and Ubiald = 'off' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$whis=$row1[0];
					$wnin=$row1[1];
				}
				else
				{
					$whis=$row[6];
					$wnin=$row[7];
				}
			}
			else
			{
				$whis=0;
				$wnin=0;
			}

			if($wqui == "0-SELECCIONE")
			{
				?>
				<script>
					function ira(){document.turnos.wnci.focus();}
				</script>
				<?php
			}
			else
				if($whfi != "SELECCIONE" and $wdoc == 0)
				{
					?>
					<script>
						function ira(){document.turnos.wndt.focus();}
					</script>
					<?php
				}
				else
					if($wdoc != 0 and $wnom != "")
					{
						?>
						<script>
							function ira(){document.turnos.wtel.focus();}
						</script>
						<?php
					}
					else
						if($wdoc != 0 and $wnom == "")
						{
							?>
							<script>
								function ira(){document.turnos.wnom.focus();}
							</script>
							<?php
						}
						else
						{
							?>
							<script>
								function ira(){document.turnos.wnci.focus();}
							</script>
							<?php
						}
			
			global $wcliame;
			echo "<td bgcolor=".$color." align=center>*Historia &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp- Nro Ing.<br><input type='TEXT' name='whis' size=9 maxlength=9  readonly='readonly' value='".$whis."' class=tipo3> - <input type='TEXT' name='wnin' size=4 maxlength=4  readonly='readonly' value='".$wnin."' class=tipo3></td>";
			echo "<td bgcolor=".$color." align=center>*Nombre : <br><input type='TEXT' name='wnom' size=40 maxlength=40 value='".$wnom."' class=tipo3></td>";
			if($weda > 0 and ($wfna == date("Y-m-d") or $wfna == ""))
				$wfna=(string)((integer)date("Y") - $weda)."-".date("m")."-".date("d");
			else
			{
				$ann=(integer)substr($wfna,0,4)*360 +(integer)substr($wfna,5,2)*30 + (integer)substr($wfna,8,2);
				$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				$weda=(integer)(($aa - $ann)/360);
				$weda=number_format((double)$weda,0,'.','');
			}
			echo "<td bgcolor=".$color." align=center>Edad &nbsp&nbsp&nbsp-&nbsp F. Nacimiento<br><input type='TEXT' name='weda' size=3 maxlength=3 value='".$weda."' class=tipo3> - <input type='TEXT' name='wfna' size=10 maxlength=10 value='".$wfna."' class=tipo3 OnBlur='enter()'></td>";
			echo "<td bgcolor=".$color." align=center>*Telefonos : <br><input type='TEXT' name='wtel' size=25 maxlength=40  value='".$wtel."' class=tipo3></td>";

			echo "</tr>";
			//TERCERA LINEA
			echo "<tr>";
			echo "<td bgcolor=".$color." align=center>Sexo : <br>";
			$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='01' and Selest='on'  order by Selpri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<select name='wsex' id=tipo1>";
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wsex=ver($wsex);
					if($wsex == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
			echo "</select>";
			echo "</td>";
			echo "<td bgcolor=".$color." align=center>Instrumentadora : <br>";
			$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='02' and Selest='on'  order by Selpri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<select name='wins' id=tipo1>";
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wins=ver($wins);
					if($wins == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
			echo "</select>";
			echo "</td>";
			echo "<td bgcolor=".$color." align=center>Tipo Cirugia : <br>";
			$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='03' and Selest='on'  order by Selpri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<select name='wtci' id=tipo1>";
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wtci=ver($wtci);
					if($wtci == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
			echo "</select>";
			echo "</td>";
			echo "<td bgcolor=".$color." align=center>Tipo de Programacion : <br>";
			$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='04' and Selest='on'  order by Selpri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<select name='wtip' id=tipo1>";
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wtip=ver($wtip);
					if($wtip == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
			echo "</select>";
			echo "</td>";
			echo "<td bgcolor=".$color." align=center>Tipo Anestesia : <br>";
			$query = "SELECT Selcod, Seldes  from ".$empresa."_000013 where Seltip='05' and Selest='on'  order by Selpri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<select name='wtan' id=tipo1>";
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wtan=ver($wtan);
					if($wtan == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
			echo "</select>";
			echo "</td>";
			echo "</tr>";
			//CUARTA LINEA
			echo "<tr>";
			if(isset($wuci))
				echo "<td bgcolor=".$color." align=center>UCI : <br><input type='checkbox' name='wuci' ".$CHECKBOX[0]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color." align=center>UCI : <br><input type='checkbox' name='wuci' ".$CHECKBOX[0]." class=tipo4></td>";
			echo "</td>";
			if(isset($wbio))
				echo "<td bgcolor=".$color." align=center>Biopsia<br>x Congelacion : <br><input type='checkbox' name='wbio' ".$CHECKBOX[1]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color." align=center>Biopsia<br>x Congelacion : <br><input type='checkbox' name='wbio' ".$CHECKBOX[1]." class=tipo4></td>";
			echo "</td>";
			if(isset($winf))
				echo "<td bgcolor=".$color." align=center>Cirugia<br>Infectada : <br><input type='checkbox' name='winf' ".$CHECKBOX[2]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color." align=center>Cirugia<br>Infectada : <br><input type='checkbox' name='winf' ".$CHECKBOX[2]." class=tipo4></td>";
			echo "</td>";
			if(isset($wmat))
				echo "<td bgcolor=".$color." align=center>Material : <br><input type='checkbox' name='wmat' ".$CHECKBOX[3]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color." align=center>Material : <br><input type='checkbox' name='wmat' ".$CHECKBOX[3]." class=tipo4></td>";
			echo "</td>";
			if(isset($wban))
				echo "<td bgcolor=".$color." align=center>Componentes<br>Sanguineos : <br><input type='checkbox' name='wban' ".$CHECKBOX[4]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color." align=center>Componentes<br>Sanguineos : <br><input type='checkbox' name='wban' ".$CHECKBOX[4]." class=tipo4></td>";
			echo "</td>";
			echo "</tr>";
			//QUINTA LINEA
			echo "<tr>";
			if(isset($wpre))
				echo "<td bgcolor=".$color." align=center>Preadmision : <br><input type='checkbox' name='wpre' ".$CHECKBOX[5]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color." align=center>Preadmision : <br><input type='checkbox' name='wpre' ".$CHECKBOX[5]."  class=tipo4></td>";
			echo "</td>";
			echo "<td bgcolor=".$color." align=center colspan=2>Responsable : <br><input type='TEXT' name='wepsw' size=10 maxlength=30 OnBlur='enter()' class=tipo3><br>";
			echo "<select name='weps' id=tipo1>";
			if(isset($wepsw) and $wepsw != "")
			{
				//$query = "SELECT Entcod, Entdes from ".$empresa."_000003 where Entcod = '".$wepsw."' and Entest='on'  order by Entdes";
				$query = "SELECT Empcod, Empnom from ".$wcliame."_000024 where Empcod = '".$wepsw."' and Empest='on'  order by Empest";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num == 0)
				{
					//$query = "SELECT Entcod, Entdes  from ".$empresa."_000003 where Entdes like '%".$wepsw."%' and Entest='on'  order by Entdes";
					$query = "SELECT Empcod, Empnom  from ".$wcliame."_000024 where Empnom like '%".$wepsw."%' and Empest='on'  order by Empest";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
				}
				if ($num>0)
				{
					echo "<option>SELECCIONE</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$weps=ver1($weps);
						if($weps == $row[0])
							echo "<option>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
				}
				else
					echo "<option>0-NO APLICA</option>";
			}
			else
				if(isset($weps))
				{
					$weps=ver1($weps);
					//$query = "SELECT Entcod, Entdes  from ".$empresa."_000003 where Entcod = '".$weps."' and Entest='on' order by Entdes";
					$query = "SELECT Empcod, Empnom  from ".$wcliame."_000024 where Empcod = '".$weps."' and Empest='on' order by Empest";
					$err = mysql_query($query,$conex);
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				else
					echo "<option>0-NO APLICA</option>";
			echo "</select>";
			echo "</td>";
			if(isset($west))
				echo "<td id=tipo9>FUERA DE SERVICIO : <br><input type='checkbox' name='west' ".$CHECKBOX[10]." checked class=tipo4></td>";
			else
				echo "<td id=tipo9>FUERA DE SERVICIO : <br><input type='checkbox' name='west' ".$CHECKBOX[10]." class=tipo4></td>";
			if(isset($word))
				echo "<td id=tipo9>ORDENES : <br><input type='checkbox' name='word' ".$CHECKBOX[9]." checked class=tipo4></td>";
			else
				echo "<td id=tipo9>ORDENES : <br><input type='checkbox' name='word' ".$CHECKBOX[9]." class=tipo4></td>";
			echo "</td>";
			echo "</tr>";
			//SEXTA LINEA
			echo "<tr>";
			if(isset($wpes))
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Espera : <br><input type='checkbox' name='wpes' ".$CHECKBOX[6]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Espera : <br><input type='checkbox' name='wpes' ".$CHECKBOX[6]." class=tipo4></td>";
			echo "</td>";
			if(isset($wpep))
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Preparacion : <br><input type='checkbox' name='wpep' ".$CHECKBOX[11]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Preparacion : <br><input type='checkbox' name='wpep' ".$CHECKBOX[11]." class=tipo4></td>";
			echo "</td>";
			if(isset($wpeq))
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Quirofano : <br><input type='checkbox' name='wpeq' ".$CHECKBOX[7]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Quirofano : <br><input type='checkbox' name='wpeq' ".$CHECKBOX[7]." class=tipo4></td>";
			echo "</td>";
			if(isset($wper))
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Recuperacion : <br><input type='checkbox' name='wper' ".$CHECKBOX[8]." checked class=tipo4></td>";
			else
				echo "<td bgcolor=".$color7." align=center>Paciente<br>En Recuperacion : <br><input type='checkbox' name='wper' ".$CHECKBOX[8]." class=tipo4></td>";
			echo "</td>";
			if(isset($wpea))
				echo "<td bgcolor=".$color7." align=center>Paciente En Alta : <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ubicacion : <br><input type='checkbox' name='wpea' ".$CHECKBOX[12]." checked class=tipo4>&nbsp;&nbsp;&nbsp;<input type='TEXT' name='wubi' size=20 maxlength=30 value='".$wubi."' class=tipo3></td>";
			else
				echo "<td bgcolor=".$color7." align=center>Paciente En Alta : <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ubicacion : <br><input type='checkbox' name='wpea' ".$CHECKBOX[12]." class=tipo4>&nbsp;&nbsp;&nbsp;<input type='TEXT' name='wubi' size=20 maxlength=30 value='".$wubi."' class=tipo3></td>";
			echo "</td>";
			echo "</tr>";
			//SEPTIMA LINEA
			echo "<tr><td bgcolor=".$color." align=center colspan=5>Codigos CUPS Parametrizados : <br><input type='TEXT' name='wcupsw' size=60 maxlength=80 onBlur='enter()' class=tipo3><br><br>";
			echo "<select name='wcupsp' class='tipoTAB' multiple=multiple size=10 onClick='enter()' style='vertical-align:middle;'>";
			if(isset($wcupsw) and $wcupsw != "")
			{
				$wepscups=$weps;
				$query  = "SELECT CONCAT(Procod,'(',Procup,')'),Pronom from ".$wcliame."_000103 where Procod like '%".$wcupsw."%' and Proest='on' ";
				$query .= " UNION ALL ";
				$query .= "SELECT CONCAT(Procod,'(',Procup,')'),Pronom from ".$wcliame."_000103 where Pronom like '%".$wcupsw."%' and Proest='on'  ";
				$query .= " UNION ALL ";
				$query .= "SELECT CONCAT(Proemppro,'(',Proempcod,')'),Proempnom from ".$wcliame."_000070 where Proemppro like '%".$wcupsw."%' and Proempest='on' and Proempemp = '".$wepscups."' ";
				$query .= " UNION ALL ";
				$query .= "SELECT CONCAT(Proemppro,'(',Proempcod,')'),Proempnom from ".$wcliame."_000070 where Proempnom like '%".$wcupsw."%' and Proempest='on' and Proempemp = '".$wepscups."' ";
				$query .= " UNION ALL ";
				$query .= "SELECT CONCAT(Cprcod,'(',Cprcod,')'),Cprnom from ".$wcliame."_000254 where Cprcod like '%".$wcupsw."%' and Cprest='on' and Cprnem = '".$wepscups."' ";
				$query .= " UNION ALL ";
				$query .= "SELECT CONCAT(Cprcod,'(',Cprcod,')'),Cprnom from ".$wcliame."_000254 where Cprnom like '%".$wcupsw."%' and Cprest='on' and Cprnem = '".$wepscups."' ";
				$query .= " order by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<option>SELECCIONE</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($i % 2 == 0)
							echo "<option class='tipoLIN1'>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option class='tipoLIN2'>".$row[0]."-".$row[1]."</option>";
					}
				}
				else
					echo "<option>SELECCIONE</option>";
			}
			else
				echo "<option>SELECCIONE</option>";
			echo "</select>&nbsp;&nbsp;";
			if($wcupsp != "0" and $wcupsp != "SELECCIONE" and strpos($regcups,$wcupsp) === false)
				$regcups .= $wcupsp.chr(10);
				$regcups = str_replace(chr(10).chr(10),chr(10),$regcups);
			echo "<textarea name='regcups' cols=80 rows=5 class='tipoTAB' onKeypress='teclado6(event)' style='vertical-align:middle;'>".$regcups."</textarea>";
			echo "</td></tr>";
			//OCTAVA LINEA
			echo "<tr>";
			echo "<td bgcolor=".$color." valign=center colspan=3 align=center>Comentarios Anteriores <br><textarea name='wcoma' cols=80 readonly='readonly' rows=5 class='tipoCOM'>".$wcoma."</textarea></td><td bgcolor=".$color." valign=center colspan=2 align=center>*Comentario Nuevo : <br><textarea name='wcom' cols=80 rows=5 class=tipo3A>".$wcom."</textarea>";
			echo "</tr>";
			//NOVENA LINEA
			echo "<tr>";
			echo "<td bgcolor=".$color." valign=center colspan=3 align=center>Otros Codigos Autorizados y de Estancia  Grabados : <br><textarea name='wcupsa' cols=80 readonly='readonly' rows=5 class='tipoCOM'>".$wcupsa."</textarea></td><td bgcolor=".$color." valign=center colspan=2 align=center>Otros Codigos Autorizados y de Estancia : <br><textarea name='wcups' cols=80 rows=5 class='tipo3A'>".$wcups."</textarea></td>";
			echo "</tr>";
			//DECIMA LINEA
			echo "<tr>";
			echo "<td bgcolor=".$color." valign=center colspan=3 align=center>Insumos y Equipos Autorizados Grabados : <br><textarea name='wmataa' cols=80 rows=5 readonly='readonly' class='tipoCOM'>".$wmataa."</textarea></td><td bgcolor=".$color." valign=center colspan=2 align=center>Insumos y Equipos Autorizados : <br><textarea name='wmata' cols=80 rows=5 class='tipo3A'>".$wmata."</textarea></td>";
			echo "</tr>";
			//UNDECIMA LINEA
			if($wniv == 1)
				echo "<tr><td bgcolor=".$color." colspan=2 align=center><input type='RADIO' name=x value=0><b>Cirugias</b>&nbsp&nbsp<input type='RADIO' name=x value=1><b>Medico</b>&nbsp&nbsp<input type='RADIO' name=x value=2><b>Equipos</b></td>";
			else
				echo "<tr><td bgcolor=".$color." colspan=2 align=center><input type='RADIO' name=x disabled value=0><b>Cirugias</b>&nbsp&nbsp<input type='RADIO' name=x disabled value=1><b>Medico</b>&nbsp&nbsp<input type='RADIO' name=x disabled value=2><b>Equipos</b></td>";
			if($wniv == 1)
				echo "<td bgcolor=".$color." align=center>Criterio de Busqueda : <br><input type='TEXT' name='wcri' size=40 maxlength=40 class=tipo3 OnBlur='enter()'></td>";
			else
				echo "<td bgcolor=".$color." align=center>Criterio de Busqueda : <br><input type='TEXT' name='wcri' disabled size=40 maxlength=40 class=tipo3 OnBlur='enter()'></td>";
			echo "<td bgcolor=".$color." colspan=2 align=center>Resultado : <br>";
			if(isset($x))
				echo "<input type='HIDDEN' name= 'x1' value='".$x."'>";
			echo "<select name='wdat' id=tipo1 OnChange='enter()'>";
			if(isset($wcri) and $wcri != "" and isset($x))
			{
				switch ($x)
				{
					case 0:
						$query = "SELECT Circod, Cirdes  from ".$empresa."_000002 where Circod = '".$wcri."' order by Cirdes";
					break;
					case 1:
						$query = "SELECT Medcod, Mednom  from ".$empresa."_000006 where Medcod = '".$wcri."' order by Mednom";
					break;
					case 2:
						$query = "SELECT Equcod, Equdes  from ".$empresa."_000004 where Equcod = '".$wcri."' order by Equdes";
					break;
				}
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num == 0)
				{
					switch ($x)
					{
						case 0:
							$query = "SELECT Circod, Cirdes  from ".$empresa."_000002 where Cirdes like '%".$wcri."%' order by Cirdes";
						break;
						case 1:
							$query = "SELECT Medcod, Mednom  from ".$empresa."_000006 where Mednom like '%".$wcri."%' order by Mednom";
						break;
						case 2:
							$query = "SELECT Equcod, Equdes  from ".$empresa."_000004 where Equdes like '%".$wcri."%' order by Equdes";
						break;
					}
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
				}
				if ($num>0)
				{
					echo "<option>SELECCIONE</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
				}
				else
					echo "<option>0-NO APLICA</option>";
			}
			else
				if(!isset($x) and isset($wdat))
				{
					$wtex=substr($wdat,strpos($wdat,"-")+1);
					$wdat=ver($wdat);
					if($wdat != "SELECCIONE" and $wdat != "0")
						switch ($x1)
						{
							case 0:
								$query = "SELECT Circpr, Gcides   from ".$empresa."_000002,".$empresa."_000017 where Circod = '".$wdat."' and Circpr = Gcicod  ";
								$err = mysql_query($query,$conex);
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									$row = mysql_fetch_array($err);
									$NC=$NC+1;
									$dataC[$NC][0]=$wdat;
									$dataC[$NC][1]=$wtex;
									$dataC[$NC][2]=$row[0]."-".$row[1];
								}
							break;
							case 1:
								$query = "SELECT Mednom, Medesp, Espdet   from ".$empresa."_000006, ".$empresa."_000005 where Medcod = '".$wdat."' and Medesp = Espcod ";
								$err = mysql_query($query,$conex);
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									$row = mysql_fetch_array($err);
									$NM=$NM+1;
									$dataM[$NM][0]=$wdat;
									$dataM[$NM][1]=$wtex;
									$dataM[$NM][2]=$row[1]."-".$row[2];
								}
							break;
							case 2:
								$query = "SELECT Geqcod, Geqdes  from ".$empresa."_000004,".$empresa."_000016 where Equcod = '".$wdat."' and Equgru = Geqcod";
								$err = mysql_query($query,$conex);
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									$row = mysql_fetch_array($err);
									$NE=$NE+1;
									$dataE[$NE][0]=$wdat;
									$dataE[$NE][1]="S-".$wtex;
									$dataE[$NE][2]=$row[0]."-".$row[1];
								}
							break;
						}
				}
			echo "</select>";
			echo "</td>";
			echo "</tr>";

			//PARTE CENTRAL DE LA PANTALLA
			switch ($ok)
			{
				case 1:
					if($wnci != 0)
					{
						echo "<tr><td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=4 onclick='enter()'><b>MODIFICAR CONTENIDO</b></td>";
						if($wniv == 1)
							echo "<td bgcolor=#cccccc colspan=2 valign=center>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspCAMBIO DE TURNO &nbsp&nbsp  Quirofano &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp- Hora Inicial - Hora Final &nbsp- &nbsp&nbspFecha<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='RADIO' name=ok value=5 onclick='enter()'><b></b>";
						else
							echo "<td bgcolor=#cccccc colspan=2 valign=center>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspCAMBIO DE TURNO &nbsp&nbsp  Quirofano &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp- Hora Inicial - Hora Final &nbsp- &nbsp&nbspFecha<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='RADIO' name=ok disabled value=5 onclick='enter()'><b></b>";
						echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
						$query = "SELECT Quicod, Quides  from ".$empresa."_000012 where Quiest='on' order by Quicod";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num = mysql_num_rows($err);
						echo "<select name='wquix' id=tipo1 style='width:100px'>";
						if ($num>0)
						{
							for ($i=0;$i<$num;$i++)
							{
								$row = mysql_fetch_array($err);
								$wquix=ver($wquix);
								if($wquix == $row[0])
									echo "<option selected>".$row[0]."-".$row[1]."</option>";
								else
									echo "<option>".$row[0]."-".$row[1]."</option>";
							}
						}
						echo "</select>";
						echo " - <select name='whinx' id=tipo1>";
						$fase="00:00";
						for ($i=1;$i<49;$i++)
						{
							if($whinx == $fase)
								echo "<option selected>".$fase."</option>";
							else
								echo "<option>".$fase."</option>";
							if(substr($fase,3,2) == "30")
							{
								$hor1=(integer)substr($fase,0,2) + 1;
								if($hor1 < 10)
									$hor1="0".$hor1;
								$min1="00";
							}
							else
							{
								$hor1=substr($fase,0,2);
								$min1="30";
							}
							$fase=$hor1.":".$min1;
						}
						echo "</select>&nbsp- ";
						echo "<select name='whfix' id=tipo1>";
						$fase="00:30";
						for ($i=1;$i<49;$i++)
						{
							if($whfix == $fase)
								echo "<option selected>".$fase."</option>";
							else
								echo "<option>".$fase."</option>";
							if(substr($fase,3,2) == "30")
							{
								$hor1=(integer)substr($fase,0,2) + 1;
								if($hor1 < 10)
									$hor1="0".$hor1;
								$min1="00";
							}
							else
							{
								$hor1=substr($fase,0,2);
								$min1="30";
							}
							$fase=$hor1.":".$min1;
						}
						echo "</select> - ";
						echo "&nbsp<input type='TEXT' name='wfecx' size=10 maxlength=10 id='wfecx' readonly='readonly' value='".$wfecx."' class=tipo3>&nbsp&nbsp<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger3'>";
						
						
						// --> Consultar las causas de demora o retraso
						$arrCausas = array();				
						$sqlCausas = "
						SELECT Caucod, Caunom
						  FROM ".$empresa."_000024
						 WHERE Cauest = 'on'
						   AND (Cautip = 'R' OR Cautip = '*' )						 
						";
						$resCausas = mysql_query($sqlCausas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausas):</b><br>".mysql_error());
						while($rowCausas = mysql_fetch_array($resCausas))
							$arrCausas[$rowCausas['Caucod']] = utf8_encode($rowCausas['Caunom']);
							
						echo "
							<table style='".(($operativo != 'on') ? "display:none;" : "")."'>
								<tr>
									<td align=center style='color:#000066;font-size: 7pt;font-family: Tahoma;font-weight: bold;'>
										Causa de modificación: <br>
										<select id='causaModificacion' name='causaModificacion' style='background-color:#FFFFA8;'>
											<option value=''>Seleccione..</option>";
											foreach($arrCausas as $codCau => $nomCau)
												echo "<option ".(($causaModificacion == $codCau) ? "selected" : "")." value='".$codCau."'>".$nomCau."</option>"; 
						echo "					
										</select>
									</td>
								</tr>
							</table>
						</td>";
			
						?>
						<script type="text/javascript">//<![CDATA[
							Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecx',button:'trigger3',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
						//]]></script>
						<?php
						if($wniv == 1)
							echo "<td bgcolor=#cccccc align=center colspan=2><b>CANCELACION DE TURNO</b><input type='RADIO' name=ok value=6 onclick='enter()'><br>";
						else
							echo "<td bgcolor=#cccccc align=center colspan=2><b>CANCELACION DE TURNO</b><input type='RADIO' name=ok disabled value=6 onclick='enter()'><br>";
						echo "<input type='TEXT' name='wcacw' size=10 maxlength=30 OnBlur='enter()' class=tipo3> - ";
						echo "<select name='wcac' id=tipo1>";
						if(isset($wcacw) and $wcacw != "")
						{
							$query = "SELECT Cancod, Candes from ".$empresa."_000001 where Cancod = '".$wcacw."' and Canest='on' order by Candes";
							$err = mysql_query($query,$conex);
							$num = mysql_num_rows($err);
							if ($num == 0)
							{
								$query = "SELECT Cancod, Candes from ".$empresa."_000001 where Candes like '%".$wcacw."%' and Canest='on' order by Candes";
								$err = mysql_query($query,$conex);
								$num = mysql_num_rows($err);
							}
							if ($num>0)
							{
								echo "<option>SELECCIONE</option>";
								for ($i=0;$i<$num;$i++)
								{
									$row = mysql_fetch_array($err);
									$wcac=ver($wcac);
									if($wcac == $row[0])
										echo "<option>".$row[0]."-".$row[1]."</option>";
									else
										echo "<option>".$row[0]."-".$row[1]."</option>";
								}
							}
							else
								echo "<option>0-NO APLICA</option>";
						}
						else
							if(isset($wcac))
							{
								$wcac=ver($wcac);
								$query = "SELECT Cancod, Candes  from ".$empresa."_000001 where Cancod = '".$wcac."' and Canest='on' order by Candes";
								$err = mysql_query($query,$conex);
								$num = mysql_num_rows($err);
								if ($num>0)
								{
									$row = mysql_fetch_array($err);
									echo "<option>".$row[0]."-".$row[1]."</option>";
								}
								else
									echo "<option>0-NO APLICA</option>";
							}
							else
								echo "<option>0-NO APLICA</option>";
						echo "</select>";
						if(isset($wpcan) and strlen($wpcan) > 0)
							echo "<br>PERSONA QUE CANCELA :&nbsp;<input type='TEXT' name='wpcan' size=30 maxlength=30 value='".$wpcan."'  class=tipo3></td></tr>";
						else
							echo "<br>PERSONA QUE CANCELA :&nbsp;<input type='TEXT' name='wpcan' size=30 maxlength=30  class=tipo3></td></tr>";
					}
					echo "<tr><td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=0 onclick='enter()'><b>INICIAR</b></td>";
					echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=1 checked onclick='enter()'><b>PROCESO</b></td>";
					if($wnew == 1)
						echo "<td bgcolor=#cccccc align=center colspan=2><input type='RADIO' name=ok value=3 disabled onclick='enter()'><b>CONSULTAR</b>";
					else
						echo "<td bgcolor=#cccccc align=center colspan=2><input type='RADIO' name=ok value=3 onclick='enter()'><b>CONSULTAR</b>";
				break;
				case 3:
					if($wnci != 0)
					{
						echo "<input type='HIDDEN' name= 'wquix' value='".$wqui."'>";
						echo "<input type='HIDDEN' name= 'whinx' value='".$whin."'>";
						echo "<input type='HIDDEN' name= 'whfix' value='".$whfi."'>";
						echo "<input type='HIDDEN' name= 'wfecx' value='".$wfec."'>";
					}
					echo "<tr><td bgcolor=#999999 align=center colspan=5 valign=center><IMG SRC='/matrix/images/medical/root/warning.gif'>&nbsp&nbsp<b>PARA MODIFICACION - CAMBIO - CANCELACION DE TURNOS HAGA CLICK EN PROCESO</b></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=0 onclick='enter()'><b>INICIAR</b></td>";
					echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=1 onclick='enter()'><b>PROCESO</b></td>";
					echo "<td bgcolor=#cccccc align=center colspan=2><input type='RADIO' name=ok value=3 checked onclick='enter()'><b>CONSULTAR</b>";
				break;
			}
			if(isset($ok) and $ok == 3)
			{

				echo "<br><input type='RADIO' name=wb value=1  onclick='enter()'> Adelante <input type='RADIO' name=wb value=2 onclick='enter()'> Atras";
				if(!isset($wposs))
					$wposs=0;
				echo "&nbsp Registro Nro. : <input type='TEXT' name='wposs' size=5 maxlength=10  value='".$wposs."' OnBlur='enter()' class=tipo3>";
				if($CHECKBOX[5] == "enabled")
					echo "&nbsp <IMG SRC='/matrix/images/medical/tcx/comentario.jpg'  alt='ACTUALIZACION A COMENTARIOS' OnClick='ejecutar(".chr(34)."/MATRIX/tcx/Procesos/Cambcom.php?wemp_pmla=".$origen."&empresa=".$empresa."&wndt=".$wnci."&ok=9".chr(34).",2)'>";
			}
			echo "</td>";
			if($wnew == 1)
				echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=2 onclick='enter()'><b>GRABAR</b></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=2 disabled onclick='enter()'><b>GRABAR</b></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=5 align=center><input type='submit' value='OK'></td></tr>";
			echo "<tr><td bgcolor=#ffffff colspan=5 align=center><A HREF='/MATRIX/tcx/Procesos/turnos.php?ok=99&empresa=".$empresa."&wfecha=".$wfecha."&origen=".$origen."'><IMG SRC='/matrix/images/medical/TCX/icono.png' alt='Planilla de Turnos'><br>Planilla de Turnos</A></td></tr>";
			echo "<tr><td align=center bgcolor=#ffffff colspan=5><b><A NAME='Abajo'>LA CONSULTA DE PACIENTES PUEDE HACERSE POR LOS CAMPOS MARCADOS CON ASTERISCO (*)</A></b></td></tr></table><br><br></center>";
			if(isset($werr) and isset($e) and $e > -1)
			{
				echo "<br><br><center><table border=0 aling=center id=tipo2>";
				for ($i=0;$i<=$e;$i++)
					if(substr($werr[$i],0,3) == "OK!")
						echo "<tr><td align=center bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color5."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
					else
						echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
				echo "</table><br><br></center>";
			}

			//********************************************************************************************************
			//*                                         DATOS ASOCIADOS AL TURNO                                     *
			//********************************************************************************************************
			if($NC > 0)
			{
				usort($dataC,'comparacion');
				for ($i=0;$i<$NC;$i++)
				{
					if($dataC[$i][0] == $dataC[$i+1][0])
					{
						for ($j=$i;$j<$NC;$j++)
						{
							$dataC[$j][0]=$dataC[$j+1][0];
							$dataC[$j][1]=$dataC[$j+1][1];
							$dataC[$j][2]=$dataC[$j+1][2];
						}
						$NC=$NC-1;
					}
				}
			}
			if($NM > 0)
			{
				usort($dataM,'comparacion');
				for ($i=0;$i<$NM;$i++)
				{
					if($dataM[$i][0] == $dataM[$i+1][0])
					{
						for ($j=$i;$j<$NM;$j++)
						{
							$dataM[$j][0]=$dataM[$j+1][0];
							$dataM[$j][1]=$dataM[$j+1][1];
							$dataM[$j][2]=$dataM[$j+1][2];
						}
						$NM=$NM-1;
					}
				}
			}
			if($NE > 0)
			{
				usort($dataE,'comparacion');
				for ($i=0;$i<$NE;$i++)
				{
					if($dataE[$i][0] == $dataE[$i+1][0])
					{
						for ($j=$i;$j<$NE;$j++)
						{
							$dataE[$j][0]=$dataE[$j+1][0];
							$dataE[$j][1]=$dataE[$j+1][1];
							$dataE[$j][2]=$dataE[$j+1][2];
						}
						$NE=$NE-1;
					}
				}
			}
			for ($i=0;$i<=$NC;$i++)
			{
				if(isset($delC[$i]))
				{
					for ($j=$i;$j<$NC;$j++)
					{
						$dataC[$j][0]=$dataC[$j+1][0];
						$dataC[$j][1]=$dataC[$j+1][1];
						$dataC[$j][2]=$dataC[$j+1][2];
					}
					$NC=$NC-1;
				}
			}
			for ($i=0;$i<=$NM;$i++)
			{
				if(isset($delM[$i]))
				{
					for ($j=$i;$j<$NM;$j++)
					{
						$dataM[$j][0]=$dataM[$j+1][0];
						$dataM[$j][1]=$dataM[$j+1][1];
						$dataM[$j][2]=$dataM[$j+1][2];
					}
					$NM=$NM-1;
				}
			}
			for ($i=0;$i<=$NE;$i++)
			{
				if(isset($delE[$i]))
				{
					for ($j=$i;$j<$NE;$j++)
					{
						$dataE[$j][0]=$dataE[$j+1][0];
						$dataE[$j][1]=$dataE[$j+1][1];
						$dataE[$j][2]=$dataE[$j+1][2];
					}
					$NE=$NE-1;
				}
			}
			echo "<input type='HIDDEN' name= 'NC' value='".$NC."'>";
			echo "<input type='HIDDEN' name= 'NM' value='".$NM."'>";
			echo "<input type='HIDDEN' name= 'NE' value='".$NE."'>";
			echo "<table border=0 align=center id=tipo2>";
			$NES=-1;
			$sugeridos=array();
			if($NC >= 0)
			{
				echo "<tr><td colspan=4 id=tipo10>CIRUGIAS</td></tr>";
				echo "<tr><td id=tipo11>Borrar</td><td id=tipo11>Codigo</td><td id=tipo11>Descripcion</td><td id=tipo11>Grupo Principal</td></tr>";
				$wturcir="";
				$factor=(integer)(80 / ($NC + 1));
				for ($i=0;$i<=$NC;$i++)
				{
					if($i % 2 == 0)
					{
						$tipo="tipo12";
						$tipoa="tipo15";
					}
					else
					{
						$tipo="tipo13";
						$tipoa="tipo16";
					}
					$wturcir .= substr($dataC[$i][1],0,$factor)."-";
					if($wniv == 1)
						echo "<tr><td id=".$tipoa."><input type='checkbox' name='delC[".$i."]'  class=".$tipoa." onclick='enter()'></td><td id=".$tipo.">".$dataC[$i][0]."</td><td id=".$tipo.">".$dataC[$i][1]."</td><td id=".$tipo.">".$dataC[$i][2]."</td></tr>";
					else
						echo "<tr><td id=".$tipoa."><input type='checkbox' name='delC[".$i."]' disabled class=".$tipoa." onclick='enter()'></td><td id=".$tipo.">".$dataC[$i][0]."</td><td id=".$tipo.">".$dataC[$i][1]."</td><td id=".$tipo.">".$dataC[$i][2]."</td></tr>";
					echo "<input type='HIDDEN' name= 'dataC[".$i."][0]' value='".$dataC[$i][0]."'>";
					echo "<input type='HIDDEN' name= 'dataC[".$i."][1]' value='".$dataC[$i][1]."'>";
					echo "<input type='HIDDEN' name= 'dataC[".$i."][2]' value='".$dataC[$i][2]."'>";
					$query = "SELECT Excequ, Geqdes  from ".$empresa."_000018, ".$empresa."_000016 where Exccir = '".ver($dataC[$i][2])."' and Exctip = 'S' and Excequ = Geqcod ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num > 0)
					{
						for ($j=0;$j<$num;$j++)
						{
							$row = mysql_fetch_array($err);
							$viable="NO";
							for ($w=0;$w<=$NE;$w++)
							{
								if(ver($dataE[$w][2]) == $row[0])
								{
									$viable="SI";
									$w=$NE+1;
								}
							}
							if($viable == "NO")
							{
								if($NES == -1 or ($NES > -1 and $i > 0 and $j==0))
								{
									echo "<tr><td colspan=4 id=tipo17>EQUIPOS SUGERIDOS</td></tr>";
									echo "<tr><td id=tipo11>Adicionar</td><td id=tipo11>Grupo</td><td colspan=2 id=tipo11>Descripcion</td></tr>";
								}
								$NES=$NES+1;
								$sugeridos[$NES][0]=$row[0];
								$sugeridos[$NES][1]=$row[1];
								$su=1;
								echo "<input type='HIDDEN' name= 'su' value='".$su."'>";
								echo "<input type='HIDDEN' name= 'sugeridos[".$NES."][0]' value='".$sugeridos[$NES][0]."'>";
								echo "<input type='HIDDEN' name= 'sugeridos[".$NES."][1]' value='".$sugeridos[$NES][1]."'>";
								echo "<tr><td id=tipo18><input type='checkbox' name='agrS[".$NES."]'  class=".$tipoa." onclick='enter()'></td><td colspan=1 id=tipo18>".$row[0]."</td><td colspan=2 id=tipo18>".$row[1]."</td></tr>";
							}

						}
					}
					if($NES > -1 and $i < $NC)
					{
						echo "<tr><td id=tipo11>Borrar</td><td id=tipo11>Codigo</td><td id=tipo11>Descripcion</td><td id=tipo11>Grupo Principal</td></tr>";
					}
				}
				echo "<input type='HIDDEN' name= 'wturcir' value='".$wturcir."'>";
				echo "<input type='HIDDEN' name= 'NES' value='".$NES."'>";
			}
			if($NM >= 0)
			{
				echo "<tr><td colspan=4 id=tipo10>MEDICOS</td></tr>";
				echo "<tr><td id=tipo11>Borrar</td><td id=tipo11>Codigo</td><td id=tipo11>Nombre</td><td id=tipo11>Especialidad</td></tr>";
				$wturmed="";
				$factor=(integer)(80 / ($NM + 1));
				for ($i=0;$i<=$NM;$i++)
				{
					if($i % 2 == 0)
					{
						$tipo="tipo12";
						$tipoa="tipo15";
					}
					else
					{
						$tipo="tipo13";
						$tipoa="tipo16";
					}
					$wturmed .= substr($dataM[$i][1],0,$factor)."-";
					if($wniv == 1)
						echo "<tr><td id=".$tipoa."><input type='checkbox' name='delM[".$i."]'  class=".$tipoa." onclick='enter()'></td><td id=".$tipo.">".$dataM[$i][0]."</td><td id=".$tipo.">".$dataM[$i][1]."</td><td id=".$tipo.">".$dataM[$i][2]."</td></tr>";
					else
						echo "<tr><td id=".$tipoa."><input type='checkbox' name='delM[".$i."]' disabled class=".$tipoa." onclick='enter()'></td><td id=".$tipo.">".$dataM[$i][0]."</td><td id=".$tipo.">".$dataM[$i][1]."</td><td id=".$tipo.">".$dataM[$i][2]."</td></tr>";
					echo "<input type='HIDDEN' name= 'dataM[".$i."][0]' value='".$dataM[$i][0]."'>";
					echo "<input type='HIDDEN' name= 'dataM[".$i."][1]' value='".$dataM[$i][1]."'>";
					echo "<input type='HIDDEN' name= 'dataM[".$i."][2]' value='".$dataM[$i][2]."'>";
				}
				echo "<input type='HIDDEN' name= 'wturmed' value='".$wturmed."'>";
			}
			$wturequ="";
			if($NE >= 0)
			{
				echo "<tr><td colspan=4 id=tipo10>EQUIPOS</td></tr>";
				echo "<tr><td id=tipo11>Borrar</td><td id=tipo11>Codigo</td><td id=tipo11>Descripcion</td><td id=tipo11>Grupo Principal</td></tr>";
				$factor=(integer)(80 / ($NE + 1));
				for ($i=0;$i<=$NE;$i++)
				{
					if($i % 2 == 0)
					{
						$tipo="tipo12";
						$tipoa="tipo15";
					}
					else
					{
						$tipo="tipo13";
						$tipoa="tipo16";
					}
					$wturequ .= substr($dataE[$i][1],0,$factor)."-";
					if($wniv == 1)
						echo "<tr><td id=".$tipoa."><input type='checkbox' name='delE[".$i."]'  class=".$tipoa." onclick='enter()'></td><td id=".$tipo.">".$dataE[$i][0]."</td><td id=".$tipo.">".$dataE[$i][1]."</td><td id=".$tipo.">".$dataE[$i][2]."</td></tr>";
					else
						echo "<tr><td id=".$tipoa."><input type='checkbox' name='delE[".$i."]' disabled class=".$tipoa." onclick='enter()'></td><td id=".$tipo.">".$dataE[$i][0]."</td><td id=".$tipo.">".$dataE[$i][1]."</td><td id=".$tipo.">".$dataE[$i][2]."</td></tr>";
					echo "<input type='HIDDEN' name= 'dataE[".$i."][0]' value='".$dataE[$i][0]."'>";
					echo "<input type='HIDDEN' name= 'dataE[".$i."][1]' value='".$dataE[$i][1]."'>";
					echo "<input type='HIDDEN' name= 'dataE[".$i."][2]' value='".$dataE[$i][2]."'>";
				}
			}
			echo "<input type='HIDDEN' name= 'wturequ' value='".$wturequ."'>";
			echo "</table>";
			echo"</form>";
		}
	}
	else
	{
		$color4="#CC99FF";
		echo "<table border=0 align=center>";
		echo "<td align=center><table border=0 align=center id=tipo5>";
		?>
		<script>
			function ira(){document.turnos.wfecha.focus();}
		</script>
		<?php
		//echo "<tr><td align=center colspan=5><IMG SRC'../../images/medical/root/clinica.jpg' heigth='76' with='120'></td></tr>";
		echo "<tr><td align=center colspan=5 id=tipo19><A HREF='/MATRIX/root/Reportes/DOC.php?files=../../tcx/procesos/turnos.php?origen=".$origen."' target='_blank'>Ver. 2020-04-27</A></td></tr>";
		//echo "<tr><td align=center colspan=5 id=tipo14>TURNOS EN CIRUGIA</td></tr>";
		if (!isset($wfecha))
			$wfecha=date("Y-m-d");
		$year = (integer)substr($wfecha,0,4);
		$month = (integer)substr($wfecha,5,2);
		$day = (integer)substr($wfecha,8,2);
		$nomdia=mktime(0,0,0,$month,$day,$year);
		$nomdia = strftime("%w",$nomdia);
		$wsw=0;
		switch ($nomdia)
		{
			case 0:
				$diasem = "DOMINGO";
				break;
			case 1:
				$diasem = "LUNES";
				break;
			case 2:
				$diasem = "MARTES";
				break;
			case 3:
				$diasem = "MIERCOLES";
				break;
			case 4:
				$diasem = "JUEVES";
				break;
			case 5:
				$diasem = "VIERNES";
				break;
			case 6:
				$diasem = "SABADO";
				break;
		}
		echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA :</b></td>";
		echo "<td bgcolor='#cccccc' align=center>Dia de la Semana<br><b>".$diasem."</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center>Año - Mes - Dia<br><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6></td>";
		echo "<td bgcolor='#cccccc'><input type='submit' value='IR'></td></tr>";
		echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4." colspan=3><font color=#000000 face='tahoma'><b>USUARIO NO AUTORIZADO PARA UTILIZAR ESTE PROGRAMA. LLAME A INFORMATICA!!!</b></font></td></tr>";
		echo "</table><br>";
	}
}
?>
</body>
</html>
