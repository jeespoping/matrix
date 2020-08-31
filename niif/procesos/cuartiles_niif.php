<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Carolina Londono A.
//FECHA DE CREACION: 	2016-09-22
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//
//--------------------------------------------------------------------------------------------------------------------------------------------
	session_start();
	                 
	//$wactualiz="2016-09-22";
	//encabezado("Cuartiles Contables", $wactualiz, 'clinica');
	//Se verifica si el usuario inicio sesión en matrix
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
		//Conexion con bd de UNIX
		$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexion con la BD connif");
		
		//Constructor de Queries UNIX no se pueden mas de 9 campos para verificar si son nulos o no
		function construirQueryUnix( $tablas, $campos_nulos, $campos_todos='', $condicionesWhere='',$defecto_campos_nulos='')
		{
			$condicionesWhere = trim($condicionesWhere);

			if( $campos_nulos == NULL || $campos_nulos == "" )
			{
			  $campos_nulos = array("");
			}

			if( $tablas == "" ){ //Debe existir al menos una tabla
			  return false;
			}

			if(gettype($tablas) == "array"){
			  $tablas = implode(",",$tablas);
			}

			$pos = strpos($tablas, ",");
			if( $pos !== false && $condicionesWhere == ""){ //Si hay mas de una tabla, debe mandar condicioneswhere
			  return false;
			}

			//Si recibe un string, convertirlo a un array
			if( gettype($campos_nulos) == "string" )
			  $campos_nulos = explode(",",$campos_nulos);

			$campos_todos_arr = array();

			//Por cual string se reemplazan los campos nulos en el query
			if( $defecto_campos_nulos == "" ){
			  $defecto_campos_nulos = array();
			  foreach( $campos_nulos as $posxy=>$valorxy ){
				array_push($defecto_campos_nulos, "''");
			  }
			}else{
			  if(gettype($defecto_campos_nulos) == "string"){
				$defecto_campos_nulos = explode(",",$defecto_campos_nulos);
			  }
			  if(  count( $defecto_campos_nulos ) == 1 ){ //Significa que todos los campos nulos van a ser reemplazados con el mismo valor
				$defecto_campos_nulos_aux = array();
				foreach( $campos_nulos as $posxyc=>$valorxyc ){
				  array_push($defecto_campos_nulos_aux, $defecto_campos_nulos[0]);
				}
				$defecto_campos_nulos = $defecto_campos_nulos_aux;
			  }else if(  count( $defecto_campos_nulos ) != count( $campos_nulos ) ){
				return false;
			  }
			}

			if( gettype($campos_todos) == "string" ){
			  $campos_todos_arr = explode(",",trim($campos_todos));
			}else if(gettype($campos_todos) == "array"){
			  $campos_todos_arr = $campos_todos;
			  $campos_todos = implode(",",$campos_todos);
			}
			foreach( $campos_todos_arr as $pos22=>$valor ){ //quitar espacios a cada valor
			  $campos_todos_arr[$pos22] = trim($valor);
			}
			foreach( $campos_nulos as $pos221=>$valor1 ){ //quitar espacios a cada valor
			  $campos_nulos[$pos221] = trim($valor1);

			  //Si el campo nulo no existe en el arreglo de todos los campos, agregarlo al final
			  $clavex = array_search(trim($valor1), $campos_todos_arr);
			  if( $clavex === false ){
				array_push($campos_todos_arr,trim($valor1));
			  }
			}
			//Quitar la palabra and, si las condiciones empiezan asi.
			if( substr($condicionesWhere, 0, 3)  == "AND" || substr($condicionesWhere, 0, 3) == "and" ){
			  $condicionesWhere = substr($condicionesWhere, 3);
			}
			$condicionesWhere = str_replace("WHERE", "", $condicionesWhere); //Que no tenga la palabra WHERE
			$condicionesWhere = str_replace("where", "", $condicionesWhere); //Que no tenga la palabra WHERE

			$query = "";

			$bits = count( $campos_nulos );
			if( $bits >= 10 ){ //No pueden haber más de 10 campos nulos
			  return false;
			}

			if( $bits == 1 && $campos_nulos[0] == "" ){ //retornar el query normal
			  $query = "SELECT ".$campos_todos ." FROM ".$tablas;
			  if( $condicionesWhere != "" )
				$query.= " WHERE ".$condicionesWhere;
			  return $query;
			}

			$max = (1 << $bits);
			$fila_bits = array();
			
			for ($i = 0; $i < $max; $i++){
			  /*-->decbin Entrega el valor binario del decimal $i,
				-->str_pad Rellena el string hasta una longitud $bits con el caracter 0 por la izquierda:
				 EJEMPLO $input = "Alien" str_pad($input, 10, "-=", STR_PAD_LEFT);  // produce "-=-=-Alien", rellena por la izquierda hasta juntar 10 caracteres
				-->str_split Convierte un string (el entregado por str_pad) en un array, asi tengo el arreglo con el codigo binario generado
			  */
			  $campos_todos_arr_copia = array();
			  $campos_todos_arr_copia = $campos_todos_arr;

			  $fila_bits = str_split( str_pad(decbin($i), $bits, '0', STR_PAD_LEFT) );
			  $select = "SELECT ";
			  $where = " WHERE ";
			  if( $condicionesWhere != "" )
				$where.= $condicionesWhere." AND ";

			  for($pos = 0; $pos < count($fila_bits); $pos++ ){
				if($pos!=0) $where.= " AND ";
				if( $fila_bits[$pos] == 0 ){
				  $clave = array_search($campos_nulos[$pos], $campos_todos_arr_copia);
				  //if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = "'.' as ".$campos_nulos[$pos];
				  if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = $defecto_campos_nulos[$pos]." as ".$campos_nulos[$pos];
				  $where.= $campos_nulos[$pos]." IS NULL ";
				}else{
				  $where.= $campos_nulos[$pos]." IS NOT NULL ";
				}
			  }

			  $select.= implode(",",$campos_todos_arr_copia);
			  $query.= $select." FROM ".$tablas.$where;
			  if( ($i+1) < $max ) $query.= " UNION ";
			}	
			return $query;	
		}
		
		//trae la informacion al consultar una cuenta
		if(isset($operacion) && $operacion == 'consultar_porcentaje')
		{
			//Elementos del mensaje json
			$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'',
			'vpnano'=>'',
			'vpnmes'=>'',
			'vpnpdes'=>'');

			//Se crean las variables y se le asignan valores 
			$select = "vpnpdes";
			$from  = "amedesvpn";
			$where  = "vpnano='".$dato_ano."' AND vpnmes='".$dato_mes."'";

			//Se crea la consulta con los parametros anteriores, se ejecuta la consulta y extrae su resultado
			$sql = construirQueryUnix($from, $camposNulos, $select, $where , $valresDefecto);
			$res = odbc_do($conex_o,$sql);			
		   	$arr = odbc_fetch_array($res);

			$datamensaje['vpnpdes'] = trim($arr['vpnpdes']);

			//Si no extrae nada de la base de datos, la accion sera guardar
			if(trim($arr['vpnpdes']) == '')
			{
				$datamensaje['accion'] = 'guardar';
			}
			//Si extrae un valor de la base de datos, la accion sera actualizar
			else
			{
				$datamensaje['accion'] = 'actualizar';				
			}
			/*envia la respuesta del datamensaje, incluyendo la accion si va a ser guardar o actualizar
			*/			
			echo json_encode($datamensaje);					
			return;
		}


		//Guarda o actualiza el porcentaje de una cuenta, dependiendo de la accion
		if(isset($operacion) && $operacion == 'registrar_porcentaje')
		{
			$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'');

			//Si la accion del datamensaje es guardar, la consulta es un insert a la base de datos
			if($dato_accion=='guardar')
			{
				$sql="INSERT INTO amedesvpn(vpnano, vpnmes, vpnpdes)
						VALUES 
						('".$dato_ano."','".$dato_mes."','".$dato_porcentaje."')";
			}

			//Si la accion del datamensaje no era guardar,  la consulta es un update al valor del porcentaje de ese Ano y Mes
			else
			{
				$sql="UPDATE amedesvpn
					SET vpnpdes='".$dato_porcentaje."'
					WHERE
					vpnano='".$dato_ano."' AND vpnmes='".$dato_mes."'";
			}			

			//Se ejecuta la consulta bien sea guardar o actualizar, dependiendo dle caso
			$res = odbc_do($conex_o,$sql);			

			//Si se ejecuto correctamente la consulta
		   	if($resultado)
			{
				$datamensaje['check'] = 'ok';
			}	
			//Si no se ejecuto correctamente la consulta
			else
			{
				$datamensaje['check'] = 'failed';
			}
			//envia la respuesta del datamensaje	
			echo json_encode($datamensaje);					
			return;
		}

	}
	
?>
<html>
	<head>
		<link href="Style3.css" rel="stylesheet">
	</head>		
		<title>Cuartiles Contables</title>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript">

		//Oculta boton iGuardar (boton que guarda o actualiza) y se elimina cualquier valor en el input vpnpdes	
		function restaurar_valores()
		{
			$("#iGuardar").hide();
			$("#vpnpdes").val('');
			$("#porcentaje_final").val('');
			$("#vpnpdes").removeAttr("required");
		}

		/*Muestra en la interfaz el formato de porcentaje que siempre sera el valor dividido 100
		tambien evita que cambie el valor cuando no hay datos en el input o cuando los valores no son permitidos*/
		function cambiar_porcentaje(e) 
		{ 
			var a = $("#vpnpdes").val();
			//Si el valor esta entre 0 y 1000, le da un formato de porcentaje
			if(a >= 0 || a <= 1000)
			{
				$("#porcentaje_final").val(a/100);
			}
			//El valor debe ser vacio para valores que no sean números entre 0 y 1000
			else if(a=='')
			{
				$("#porcentaje_final").val('');
			}

			else if(a==' ')
			{
				$("#porcentaje_final").val('');
			}

			else
			{
				$("#porcentaje_final").val('');
			}

		}

		//Crea un vector de años desde el 2013 hasta el año presente y los envía al select para que solo puedan ingresar valores desde el 2013 al año presente
		function cargar_parametros()
		{
			//Crea las variables 
			var ano_inicial=2013;
			var ano_actual= (new Date).getFullYear();
			var anos_niif= [];

			//Se encían las variables al vector y al select (lista desplegable) de años 
			for (i=ano_inicial; i<=ano_actual;i++)
			{
				anos_niif.push(i);
				$('#vpnano').append($('<option>', {value: i,  text: i}));
			}
		}

		//Verifica que el porcentaje a guardar o a actualizar esté dentro de un rango permitido y si lo esta, guardarlo a la base de datos
		function verificar(accion)
		{
			//asigna variables para el datamensaje
			var dato_ano = $("#vpnano").val();
			var dato_mes = $("#vpnmes").val();		
			var porcentaje = $("#vpnpdes").val();	
			var dato_porcentaje = porcentaje/100;
			
			//Verifica que el porcentaje no sea vacío
			if(porcentaje.length === 0)
			{
				jAlert("Debe ingresar un porcentaje");
			}		
			//Si el porcenjate no esta dentro del rango
			else if (porcentaje < 0 || porcentaje > 1000)
			{
				jAlert("Debe ingresar un porcentaje entre 0 y 1000");
			}
			//Si el porcenatje esta dentro del rango crea y en via datamensaje
			else  
			{
			 	//Se almacena del nuevo porcentaje, en el mensaje se especifica si es guardar o actualizar	
				$.ajax
					({
						url: "cuartiles_niif.php",
						type: "POST",
						data:
						{
							consultaAjax 	: '',
							operacion 		: 'registrar_porcentaje',
							dato_ano	    : dato_ano,
							dato_mes		: dato_mes,
							dato_porcentaje : dato_porcentaje,
							dato_accion		: accion  
		
						},
						dataType: "json",			
						async: false,
						success:function(data_json) 
						{						
							if (data_json.error == 1)
							{
								jAlert(data_json.mensaje);
								jAlert("Se ha presentado un error, por favor verifique nuevamente los valores!");
								return;
							}
							//Se guardo o actualizo satisfactoriamente porcentaje para el ano y mes seleccionado
							else
							{
								jAlert("Datos ingresados satisfactoriamente");	
							}
						}	
					});	

				restaurar_valores();	
			}	
		}

		//Se consulta y extrae el porcentaje del Ano y mes seleccionado
		function consultar_porcentaje()
		{
			var dato_ano = $("#vpnano").val();
			var dato_mes = $("#vpnmes").val();			 	
			
				$.ajax
					({
						url: "cuartiles_niif.php",
						type: "POST",
						data:
						{
							consultaAjax 	: '',
							operacion 		: 'consultar_porcentaje',
							dato_ano	    : dato_ano,
							dato_mes		: dato_mes
		
						},
						dataType: "json",			
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
								//Si no existe porcentaje para el ano y mes seleccionado
								if(data_json.vpnpdes.length == 0)
								{
									$("#vpnpdes").val(data_json.vpnpdes);
									jAlert("No existe un porcentaje para este periodo, si desea puede asignar un valor en el campo Porcentaje");
								}

								//Si ya existe un porcentaje para el ano y mes seleccionado
								else
								{
									/*Debido al coma flotante, al multiplicar por 100, el cálculo no es exacto, por lo tanto, se debe cambiar el resultado del cálculo a coma
									flotante y posteriormente a entero, de modo que no se pierda ninguna cifra decimal*/
									var por = data_json.vpnpdes*100;
									var por_float = parseFloat(por).toPrecision(12); 
									var por_int = parseInt(por_float);
									jAlert("Ya existe un valor de "+ por_int +"% asignado para ese periodo, si desea puede ingresar un valor nuevo en el campo Porcentaje");						
									$("#vpnpdes").val(por_int);
									$("#porcentaje_final").val(data_json.vpnpdes);
								}															
								/*la accion se define en la operacion consultar_porcentaje
								Se cambian los valores del boton dependiendo de la accion*/
								if(data_json.accion == 'actualizar')
								{
									$("#iGuardar").show();
									$("#iGuardar").attr("onclick","verificar('actualizar')");
									$("#iGuardar").attr("value","ACTUALIZAR")
									$("#vpnpdes").attr("required", "");
								}
								else
								{
									$("#iGuardar").show();
									$("#iGuardar").attr("onclick","verificar('guardar')");
									$("#iGuardar").attr("value","GUARDAR")
									$("#vpnpdes").attr("required", "");
								}
							}
						}	
					});				
		}

		//Evitar los caracteres diferentes a numeros en los inputs
		function soloNumeros(e)
		{
			//Solo permite ingresar números o la tecla backspace apra borrar
			var key = window.Event ? e.which : e.keyCode
			return (key >= 48 && key <= 57 || key == 8);
		}
	
	</script>	
	<body onload="javascript:cargar_parametros()">
	<?php 
		$wactualiz="2016-09-27";
		$wtitulo="CUARTILES CONTABLES";
		encabezado($wtitulo, $wactualiz, 'clinica');
	?>
	<div class="container" align="center">
		<div class="formbox" align="center">
			<div class="panel panel-info" align="center">
				<div class="panel-heading" align="center">
					<div class="panel-title">CUARTILES CONTABLES</div>
				</div>
				<div class="panel-body" align="center">
					<form action="cuartiles_niif.php" method="post" align=center  name="menuNiif">					
						</label>
						<div class="div_formulario" id="div_ano">
							<label for="vpnano"><b>A&ntilde;o:</b></label>
							<select name="vpnano" id="vpnano" onchange='restaurar_valores()'> 
							</select> 
						</div>	
						<br />
						<div class="div_formulario" id="div_mes">
							<label for="vpnmes"><b>Mes:</b></label>
							<select name="vpnmes" id="vpnmes" onchange='restaurar_valores()'> 
							 	<option value="01">Enero</option>
								<option value="02">Febrero</option>
								<option value="03">Marzo</option>
								<option value="04">Abril</option>
								<option value="05">Mayo</option>
								<option value="06">Junio</option>
								<option value="07">Julio</option>
								<option value="08">Agosto</option>
								<option value="09">Septiembre</option>
								<option value="10">Octubre</option>
								<option value="11">Noviembre</option>
								<option value="12">Diciembre</option>
							</select> 
						</div>	
						<br />							
						<div class="div_formulario" id="div_porcentaje">
							<b>Porcentaje:</b> 		
							<input type="text" name="vpnpdes" id="vpnpdes" maxLength="4" onkeypress='return soloNumeros(event)' onkeyup='cambiar_porcentaje(event)' onchange='cambiar_porcentaje()' />
							<b>%</b> 
							<br /><br />
						</div>	
						<div class="div_formulario" id="div_formato_porcentaje">
							<b>Formato final del porcentaje:</b> 		
							<input  name="porcentaje_final" id="porcentaje_final"  size="3" disabled/>
							<b></b> 
							<br /><br />
						</div>
						<div class="div_formulario" id="div_boton">
							<input type="button" name="bConsultar" id="bConsultar" value="CONSULTAR" required onclick="consultar_porcentaje()">
						</div>	
						<br />							
						<input type="button" id="iGuardar" name="iGuardar" value="GUARDAR" onclick="verificar()" style="display:none" >
					</form>           
				</div>
			</div>
		</div>
	</div>			
	</body>
</html>

