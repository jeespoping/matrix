<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TRIAGES POR ENFERMERO
//=========================================================================================================================================\\
//DESCRIPCION:			Este programa reporta la cantidad de triages realizados por usuario, teniendo en cuenta la información
//						de la tabla del turnero de urgencias movhos_000178 y la tabla de usuarios     
//AUTOR:				Carolina Londono A.
//FECHA DE CREACION: 	2017-06-13
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES                                                                                          
//--------------------------------------------------------------------------------------------------------------------------------------------
//Si no se ha iniciado sesion correctamente
if(!isset($_SESSION['user']))
	{
		?>
		<label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix; Inicie sesion nuevamente.</label>
		<?php
		return;
	}
	//Si se inicio sesion correctamente
	else
	{
		//almacena las variables de usuario y se conecta a la base de datos de UNIX
		$user_session = explode('-', $_SESSION['user']);
		$wuse = $user_session[1];
		

		include_once("root/comun.php");
		

		$conex = obtenerConexionBD("matrix");
	}

	//Cuando ya se han validado las fechasa en los inputs y se ha presionado el boton generar
	if(isset($operacion) && $operacion == 'consultar_triages')
	{
		$datamensaje= array('mensaje' => '', 'error'=>0 , 'html'=>'', );
		
		/*Se busca la cantidad de triages hechos por usuario en la tabla movhos 178 y se agrupa por usuario que 
		hizo el triage (campo Atuutr), del cual se conoce el nombre habiendo un join con la tabla de
		usuarios*/
		$query="SELECT T.Atuutr AS Codigo , U.Descripcion AS Descripcion, count(*) AS Pacientes
				FROM movhos_000178  T
				LEFT JOIN usuarios U
				ON T.Atuutr=U.Codigo
				WHERE T.Fecha_data  BETWEEN '".$fecha_inicial."' AND '".$fecha_final."'
				AND T.Atuest='on'
				GROUP BY Codigo ,Descripcion ORDER BY 3 DESC";
		
		$resultado = mysql_query($query,$conex) or die("ERROR EN QUERY");   
		$num_regis = mysql_num_rows($resultado);

		//Se crea la tabla donde van a ir los datos de las consultas
		$datamensaje['html'].= "<table>";
		$datamensaje['html'].= "	<tr>";
		$datamensaje['html'].= "		<th>CODIGO</th>";
		$datamensaje['html'].= "		<th>NOMBRE</th>";
		$datamensaje['html'].= "		<th>PACIENTES</th>";
		$datamensaje['html'].= "	</tr>";		

		$total_pac = 0;
		//Mientras el query traiga registros
		while($registro = mysql_fetch_array($resultado))
		{
	    	//Cantidad de pacientes totales
	     	$total_pac=$total_pac+$registro['Pacientes'];
	     	//Muestra la informacion de la consulta en la tabla
	     	//La funciòn utf8_encode habilita que la información con caracteres extraños 
	     	//puedan ser mostrados en la interfaz.
	     	$datamensaje['html'].= "<tr>";
			$datamensaje['html'].= "<td>".utf8_encode($registro['Codigo'])."</td>";
         	$datamensaje['html'].= "<td>".utf8_encode($registro['Descripcion'])."</td>";
		 	$datamensaje['html'].= "<td>".utf8_encode($registro['Pacientes'])."</td>";
		 	$datamensaje['html'].= "</tr>";

		}
		//Tabla que muestra la sumatorias de los datos
		$datamensaje['html'].= "</table>";
		$datamensaje['html'].= "<br>";
		$datamensaje['html'].= "<div>";
		$datamensaje['html'].= "	<tr><td><b>Total Pacientes: </b>".$total_pac."<br>";
		$datamensaje['html'].= "	<tr><td><b>Total Enfermeros: </b>".$num_regis."<br>";
		$datamensaje['html'].= "</div>";
		$datamensaje['html'].= "<br>";

	   	echo json_encode($datamensaje);
	   	return;
	}	

?>
<html lang="es-ES">
	<head>
		<title>Triages realizados por Enfermeros</title>
		<link href="Style1.css" rel="stylesheet">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<!--<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />-->        <!-- Nucleo jquery -->
		<!--<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />--> <!-- Tooltip -->
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script src="datepicker-es.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script>

		 //Se les asigna el tipo datepicker a los inputs de las fechas
			$(function() {
				$( "#datepicker1" ).datepicker();
			    $( "#datepicker2" ).datepicker();
			});

			//No permite escribir en los inputs, ya que solo debe dejar seleccionar
			function no_input(e)
			{
				key = e.keyCode || e.which;
				tecla = String.fromCharCode(key).toLowerCase();
				letras = "";
			 	especiales = [8];      // El 8 es para que la tecla <backspace> tambien la deje digitar (Se pueden incluir otros ascii separados por coma)
		 	 	tecla_especial = false
			 
			 	for(var i in especiales)
			 	{
			  		if(key == especiales[i])
			  		{
				 	tecla_especial = true;
				 	break;
			  		}
				}		 
				if(letras.indexOf(tecla)==-1 && !tecla_especial)
				{
					return false;
				}	
				
				var code;
			    if (!e) var e = window.event; 
			    if (e.keyCode) code = e.keyCode; 
			    else if (e.which) code = e.which; 
			    //Para que no permita borrar
			    if (code == 8 || code == 46)
			     return false;			
			}	 

			//Funcion para cuando se haga clic en el boton generar
			function consultar_triages()
			{
				//verifica los valores de las fechas
				var fecha_inicial = $("#datepicker1").val();			
				var fecha_final   = $("#datepicker2").val();

				//Si alguna de las fechsa esta vacia
				if( fecha_inicial == null || fecha_final == null || fecha_inicial == "" || fecha_final == "")
				{
					jAlert("Seleccione ambos campos.");
				}
				//Verifica si la fecha final es mayor a la inicial
				else if(fecha_final<fecha_inicial)
				{
					jAlert("La fecha final debe ser superior a la fecha inicial.");
				}
				//Si las fechas son iguales o la fecha final es mayor a la fecha inicial, permite hacer la consulta
				else
				{	
					$.ajax
						({
							url: "triagexenfermero.php",
							type: "GET",
							dataType: "json",
							data:
							{
								consultaAjax 	: '',
								operacion 		: 'consultar_triages',
								fecha_inicial   : fecha_inicial,
								fecha_final		: fecha_final

							},
											
							async: false,
							success:function(data_json) 
							{						
								if (data_json.error == 1)
								{
									jAlert(data_json.mensaje);
									return;
								}
								else
								{
									$("#resultado").html(data_json.html);
									$("#resultado").show();
									return;
								}
							}
						});	
				}
			}

		</script> 
	</head>
	<body>
	<div class="container">
		<div class="box">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title" align="center">TRIAGES REALIZADOS POR ENFERMEROS</div>
				</div>
				<div class="panel-body" >
					<form>
						<p> 
							Fecha Inicial: <input type="text" id="datepicker1" class="datepicker1" onkeypress='return no_input(event)' required>
						</p>
						<p>
							Fecha Final:   <input type="text" id="datepicker2" class="datepicker2" onkeypress='return no_input(event)' required>
						</p>
					</form>	
				</div>		
				<div>			
					<button onclick="consultar_triages()">Generar</button> 			
				</div>
				<div id="resultado" class="resultado" align="center" style="display:none">
				</div>
				</div>
			</div>
		</div>
	</div>			
	</body>
</html>