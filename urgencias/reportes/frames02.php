<?php
include_once("conex.php");
// session_start();
// if(!isset($_SESSION['user']))
    // die ("<br>\n<br>\n".
        // " <H1>Para entrar correctamente a la aplicacion debe".
        // " hacerlo por la pagina <FONT COLOR='RED'>" .
        // " index.php</FONT></H1>\n</CENTER>");
      
    // echo "<frameset rows='50%,50%' frameborder=1 framespacing=2 bordercolor='#FF0000'>";
      // echo "<frame src='coomeva01.php' name='prog2' marginwidth=0 marginheiht=0>";
	  // echo "<frame src='coomeva02.php' name='prog1' marginwidth=0 marginheiht=0>";
    // echo "</frameset>";
	
	
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Genera grafico en Odometro para control de un tope de valor de cargos
//AUTOR:				Jair Saldarriaga orozco
//FECHA DE CREACION: 	2015-03-11
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-01-13';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2020-01-13	Jessica Madrid Mejía:	- Se modifica la estructura del script y se agrega un filtro de fecha para generar el 
// 										  reporte de acuerdo al año y mes seleccionado.
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	
	include_once("root/comun.php");
	mysql_select_db("matrix");
	$conex = obtenerConexionBD("matrix");
	// $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, '');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	// function ()
	// {
		
	// }
	
	

//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case '':
		{	
			break;
			return;
		}
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
	<!doctype html>
	<head>
	  <title>CONTROL TOPE COOMEVA EPS Y SEGUIMIENTO POR DIA</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
		<script src="../../../include/root/jquery.min.js"></script>
		<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>
		
		<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

		<script src="../../../include/root/bootstrap.min.js"></script>
		
		
		<!-- Bootstrap -->
		<link href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		
		<!-- Bootstrap -->
		<script src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>


	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$(document).ready(function() {
		pintarFiltros();
		pintarGraficas();
	});
	
	function pintarFiltros()
	{
		var date = new Date(); 
		currentYear = date.getFullYear();
		currentMonth = date.getMonth();
		
		html = "";
		for(var year=currentYear;year>=1993;year--)
		{
			html += "<option value='"+year+"'> "+year+"</option>";
			
		}
		$("#year").append(html);
		
		months = {0: '01',1: '02',2: '03',3: '04',4: '05',5: '06',6: '07',7: '08',8: '09',9: '10',10: '11',11: '12'};
		monthsNames = {0: 'Enero',1: 'Febrero',2: 'Marzo',3: 'Abril',4: 'Mayo',5: 'Junio',6: 'Julio',7: 'Agosto',8: 'Septiembre',9: 'Octubre',10: 'Noviembre',11: 'Diciembre'};
		
		html = "";
		for(month in months)
		{
			mesSeleccionado = "";
			if(currentMonth==month)
			{
				mesSeleccionado = "selected";
			}
			html += "<option value='"+months[month]+"' "+mesSeleccionado+"> "+monthsNames[month]+"</option>";
		}
		$("#month").append(html);
	}
	
	function pintarGraficas()
	{
		year = $("#year").val();
		month = $("#month").val();
		
		var date = new Date(); 
		currentYear = date.getFullYear();
		currentMonth = date.getMonth();
		
		// validar si el mes y año a consultar es mayor a el actual y mostrar mensaje
		if(year==currentYear && parseInt(month)>(currentMonth+1))
		{
			$("#divGraficos").html("");
			// Mostrar alerta
			$("#mensajeAlerta").html("El a&ntilde;o y mes seleccionado a&uacute;n no tiene registros.");
			$("#divAlerta").modal("show");
		}
		else
		{
			var html = "";
			html = 	"<div class='col-lg-8 col-md-8 col-sm-8 col-xs-8 ' style='text-align:center;'>"+
					"	<div id='mensajeEspere1'><img src='../../images/medical/ajax-loader5.gif'/>&nbsp;&nbsp;&nbsp;Generando gr&aacute;fico por favor espere un momento.</p></div>"+
					"	<br>"+
					"	<div id='mensajeEspere2'><img src='../../images/medical/ajax-loader5.gif'/>&nbsp;&nbsp;&nbsp;Generando gr&aacute;fico por favor espere un momento.</p></div>"+
					"</div>"+
					"<img id='grafico1' src='coomeva01.php?wano="+year+"&wmes="+month+"' style='display:none;' onload='generarGrafico1();'></img>"+
					"<br>"+
					"<img id='grafico2' src='coomeva02.php?wano="+year+"&wmes="+month+"' style='display:none;' onload='generarGrafico2();'>";
			
			$("#divGraficos").html(html);
		}
	}
	
	function generarGrafico1()
	{
		$("#mensajeEspere1").hide();
		$("#grafico1").show();
	}
	function generarGrafico2()
	{
		$("#mensajeEspere2").hide();
		$("#grafico2").show();
	}
	
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		body
		{
			width: auto;
			height: auto;
			background-color: #FFFFFF;
			color: #000000;
		}
		
		.ui-datepicker-calendar {
			display: none;
		}
		
		.panel-primary {
			border-color: #2A5DB0;
		}
		
		.panel-primary > .panel-heading {
			color: #fff;
			background-color: #2A5DB0;
			border-color: #2A5DB0;
		}
		
		.label-primary{
			background-color: #2A5DB0;
		}
		.btnMatrix{
			background-color: #2A5DB0;
			color: #FFFFFF;
		}
		
		.btnMatrix:hover {
			background-color: #234d90;
			color: #FFFFFF;
		}
		.center-block {
			float: none;
			margin: 0 auto;
		}
		.col-center{
		  float: none;
		  margin-left: auto;
		  margin-right: auto;
		}
		
		.modal-header {
			background-color: #2A5DB0;
			padding:1px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
		}
		
		.modal-Alerta {
			background-color: #2A5DB0;
			padding:16px 16px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
			font-size: 10pt;
		}
		
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<body>
	<?php
	// -->	ENCABEZADO
	encabezado("CONTROL TOPE COOMEVA EPS Y SEGUIMIENTO POR DIA", $wactualiz, 'clinica');
	
	$fecha = explode("-",$wfecha);
	?>
		<div id="divContenedor" class="col-lg-6 col-md-6 col-sm-6 col-xs-6 center-block">
			<div class="">
				<div id="divFiltro" class="col-lg-8 col-md-8 col-sm-8 col-xs-8 " style="text-align:center;">
					<div class="panel panel-primary">
						
						<div class="panel-heading">Seleccione el a&ntilde;o y mes a graficar</div>
						<div class="panel-body">
						<div class="form-group row">
								<div class="col-lg-4">
									<select id="year" class="form-control">
									</select>
								</div>
								<div class="col-lg-4">
									<select id="month" class="form-control">
									</select>
								</div>
								<div class="col-lg-4">
									<button type="button" id="btnConsultar" class="btn btnMatrix" onclick="pintarGraficas();">Consultar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>	
				
			<br>
			
			<div id="divGraficos">
			</div>
			
			<div id='divAlerta' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm'>
					<div class='modal-content'>
						<div class='modal-Alerta'>ALERTA</div>
						<div class='modal-body' id='mensajeAlerta'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>
			
			<div id='divMensajeEspere' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm' style='display: block;'>
					<div class='modal-content'>
						<div class='modal-body' id='mensajeEspere'><br><p align='center'><img src='../../images/medical/ajax-loader5.gif'/>&nbsp;&nbsp;&nbsp;Por favor espere un momento...</p><br></div>
					</div>
				</div>
			</div>
			
		</div>
	
	</body>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</html>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session	
?>