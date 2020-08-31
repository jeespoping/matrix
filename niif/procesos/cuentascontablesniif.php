<?php
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Carolina Londono A.
//FECHA DE CREACION: 	2016-09-22
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES                                                                                          
// 2016-09-22 Se agrega enzabezado y Manual de Usuario             
//	se reemplaza la metodologia de grabado y actualizando de submit por javascript
//--------------------------------------------------------------------------------------------------------------------------------------------
	session_start();                
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
		include("conex.php");
		include("root/comun.php");
		mysql_select_db("matrix");
		$conex = obtenerConexionBD("matrix");
		//Conexion con bd de UNIX
		$conex_o = odbc_connect('connif','','')  or die("No se realizo conexion con la BD connif");
	
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
		
		//Se consulta la informacion de una cuenta existente en la base de datos (tabla amecocue UNIX)
		if(isset($operacion) && $operacion == 'consultar_cuenta')
		{
			$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'',
			'cuenom'=>'',
			'cueniv'=>'',
			'cueicc'=>'',
			'cuescc'=>'',
			'cueini'=>'',
			'cuesni'=>'',
			'cuebas'=>'',
			'cuecon'=>'',
			'cuenat'=>'',
			'cuecie'=>'',
			'cueaju'=>'',
			'cueact'=>'',	
			'cuecba'=>'',
			'cueori'=>'');

			//Variables para la consulta UNIX
			$camposNulos  = "cuecon, cuecba, cueori";
			$valresDefecto  = "'', '', ''";
			$select = "cuecod, cuenom, cueniv, cueicc, cuescc, cueini, cuesni, cuebas, cuecon, cuenat, cuecie, cueaju, cueact, cuecba, cueori ";
			$from  = "amecocue";
			$where  = "cuecod='".$dato_cuenta."'";

			//Se construye y ejecuta la 
			$sql = construirQueryUnix($from, $camposNulos, $select, $where , $valresDefecto);
			$res = odbc_do($conex_o,$sql);			
		   	$arr = odbc_fetch_array($res);

		   	//Se envian los resultados de la consulta a cada posisicon del datamensaje
		   	$datamensaje['cuecod'] = trim($arr['cuecod']);
			$datamensaje['cuenom'] = trim($arr['cuenom']);
			$datamensaje['cueniv'] = $arr['cueniv'];
			$datamensaje['cueicc'] = $arr['cueicc'];
			$datamensaje['cuescc'] = $arr['cuescc'];
			$datamensaje['cueini'] = $arr['cueini'];
			$datamensaje['cuesni'] = $arr['cuesni'];
			$datamensaje['cuebas'] = $arr['cuebas'];
			$datamensaje['cuecon'] = $arr['cuecon'];
			$datamensaje['cuenat'] = $arr['cuenat'];
			$datamensaje['cuecie'] = $arr['cuecie'];
			$datamensaje['cueaju'] = $arr['cueaju'];
			$datamensaje['cueact'] = $arr['cueact'];
			$datamensaje['cuecba'] = trim($arr['cuecba']);
			$datamensaje['cueori'] = $arr['cueori'];
						
			echo json_encode($datamensaje);		
			return;
		}

		//Se extraen los codigos de las cuentas con el nivel seleccionado
		if(isset($operacion) && $operacion == 'extraer_codigos')
		{			
			$sql="SELECT cuecod FROM amecocue 
				WHERE cueniv='".$data_opcion."' ORDER BY cuecod";

			$datamensaje=array();
            
			$err1 = odbc_do($conex_o,$sql);	

			$empty = "";

			echo "<option>".$empty."</option>";

			while (odbc_fetch_row($err1))
			{
				echo "<option>".odbc_result($err1,1)."</option>";
			}

            return;
		}

		//Funcion para el guardado de una cuenta nueva o actualizar una cuenta nueva
		if(isset($operacion) && $operacion == 'registrar_cuenta')
		{
			//Fecha de guardado o actualizacion de una cuenta
			$fecha = date("Y-m-d H:i:s",time());

			$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'');

			//Se verifica si en la base de datos ya existe la cuenta a guardar o actualiar
			$check = odbc_do($conex_o,"SELECT count(*) as counter FROM amecocue WHERE cuecod='".$dato_cuecod."'");
			$arr = odbc_fetch_array($check);

			//Si se va a actualizar la cuenta, virifica que no haga un update sobre una cuenta existente que no sea la misma, ya que permite modificar el codigo
			if ($dato_accion =='actualizar' && ($dato_cuecod==$dato_oldcod || $arr['counter'] == 0))
			{
					$sql = "UPDATE amecocue
						SET cuecod='".$dato_cuecod."', cuenom='".$dato_cuenom."',cueniv='".$dato_cueniv."',
						cueicc='".$dato_cueicc."',cuescc='".$dato_cuescc."',cueini='".$dato_cueini."',
						cuesni='".$dato_cuesni."',cuebas='".$dato_cuebas."',cuecon='".$dato_cuecon."',
						cuenat='".$dato_cuenat."',cuecie='".$dato_cuecie."',cueaju='".$dato_cueaju."',
						cueact='".$dato_cueact."',cueumo='".$dato_usuario."',cuefmo='".$fecha."',
						cuecba='".$dato_cuecba."',cueori='".$dato_cueori."'
						WHERE
						cuecod='".$dato_oldcod."'";

						$resultado = odbc_do($conex_o,$sql);

						if($resultado)
						{
							$datamensaje['check'] = 'ok';
						}	
						//Si no se ejecuto correctamente la consulta
						else
						{
							$datamensaje['check'] = 'exist';
						}
			}

			//Si se va a guardar una cuenta nueva en la base de datos
			else if($dato_accion =='guardar')
			{
				//Si Ya existe la cuenta, no debe dejar ingresarla a la base de datos
				if($arr['counter']>0)
				{
					 $datamensaje['check'] = 'exist';						 
				}
				//Si no existe la cuenta, permite ingresarla en la base de datos
				else
				{
					$sql = "INSERT INTO amecocue(cuecod,cuenom,cueniv,cueicc,
							cuescc,cueini,cuesni,cuebas,cuecon,cuenat,cuecie
							,cueaju,cueact,cueuad,cuefad,cuecba,cueori) 
							VALUES 
							('".$dato_cuecod."','".$dato_cuenom."','".$dato_cueniv."',
							'".$dato_cueicc."','".$dato_cuescc."','".$dato_cueini."',
							'".$dato_cuesni."','".$dato_cuebas."','".$dato_cuecon."',
							'".$dato_cuenat."','".$dato_cuecie."','".$dato_cueaju."',
							'".$dato_cueact."','".$dato_usuario."','".$fecha."',
							'".$dato_cuecba."','".$dato_cueori."')";

					$resultado = odbc_do($conex_o,$sql);

					if($resultado)
					{
						$datamensaje['check'] = 'ok';
					}	
					//Si no se ejecuto correctamente la consulta
					else
					{
						$datamensaje['check'] = 'exist';
					}
				}
			}	

			//envia la respuesta del datamensaje	
			echo json_encode($datamensaje);					
			return;
		}
	}
?>
<html>
	<head>
		<link href="Style.css" rel="stylesheet">			
		<title>Cuentas Contables</title>
	</head>
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?php md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>	
		<script type="text/javascript">
		//Muestra un boton basado en la necesidad del usuario (Guardar o Actualizar), en caso de restaurar valores, los oculta y recarga el formulario
		function activarBoton()
		{			
			if($("#opcionusuarioguardar").is(":checked"))
			{
				$("#iGuardar").show();
				$("#iActualizar").hide();
				$("#oldcod").removeAttr("required");				
			}

			if($("#opcionusuarioactualizar").is(":checked"))
			{
				level = $("#cueniv").val();
				$("#iGuardar").hide();
				$("#iActualizar").show();
				$("#oldcod").attr("required", "");
				llenar_cuentaslvl(level);

			}	
		
			if($("#opcionusuarioreset").is(":checked"))
			{
				$("#iGuardar").hide();
				$("#iActualizar").hide();
				$("#oldcod").removeAttr("required");
				location.reload();
			}					
		}

		//Modifica la longitud del codigo de la cuenta basado en el nivel
		function longitudCodigo()
		{
			var e = $("#cueniv").val();
			llenar_cuentaslvl(e);
			var pos = '1|2|4|6|9';
			var opcion = pos.split('|');

			document.getElementById("cuecod").maxLength = opcion[e-1];
			var a = document.getElementById("cuecod").value.substr(0,opcion[e-1]);
			document.getElementById("cuecod").value = a;
		}

		//No permite ingresar caracteres diferentes a numeros en el campo de codigos
		function numeros(e)
		{
			key = e.keyCode || e.which;
			tecla = String.fromCharCode(key).toLowerCase();
			letras = "0123456789";
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
				return false;
		}

		//Solo permite ingresar numeros y guiones en la cuenta bancaria
		function numeros_bancarios(e)
		{
			key = e.keyCode || e.which;
			tecla = String.fromCharCode(key).toLowerCase();
			letras = "0123456789-";
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
				return false;
		}

		//Se llena el select con los codigos de las cuentas existentes basado en el nivel
		function llenar_cuentaslvl(val)
		{			
			$.ajax
			 ({
			 	url: "cuentascontablesniif.php",
			 	type: "POST",
				data: 
				{
					consultaAjax 	: '',
					operacion		: 'extraer_codigos',
					data_opcion		: val					
				},
				success: function (respuesta) 
				{
				 	document.getElementById("oldcod").innerHTML=respuesta; 
				}
			});
		}

		//Se consultan y extraen los valores de la cuenta a actualizar seleccionada
		function consultar_cuenta()
		{
			var dato_cuenta = $("#oldcod").val();							 	
			
				$.ajax
					({
						url: "cuentascontablesniif.php",
						type: "POST",
						data:
						{
							consultaAjax 	: '',
							operacion 		: 'consultar_cuenta',
							dato_cuenta     : dato_cuenta							
						},
						dataType: "json",			
						async: false,
						success:function(data_json) 
						{						
							if (data_json.error == 1)
							{
								alert(data_json.mensaje);
								return;
							}
							else
							{								
								$("#cuecod").val(data_json.cuecod);
								$("#cuenom").val(data_json.cuenom);
								$("#cueniv").val(data_json.cueniv);
								$("#cueicc").val(data_json.cueicc);
								$("#cuescc").val(data_json.cuescc);
								$("#cueini").val(data_json.cueini);
								$("#cuesni").val(data_json.cuesni);
								$("#cuebas").val(data_json.cuebas);
								$("#cuecon").val(data_json.cuecon);
								$("#cuenat").val(data_json.cuenat);
								$("#cuecie").val(data_json.cuecie);
								$("#cueaju").val(data_json.cueaju);
								$("#cueact").val(data_json.cueact);
								$("#cuecba").val(data_json.cuecba);
								$("#cueori").val(data_json.cueori);		
												
								if(data_json.cueicc == 'S')
								{					
									$("#cueicc_si").attr('checked', true);
								}
								else
								{									
									$("#cueicc_no").attr('checked', true);
								}
								
								if(data_json.cuescc == 'S')
								{					
									$("#cuescc_si").attr('checked', true);
								}
								else
								{									
									$("#cuescc_no").attr('checked', true);
								}
								
								if(data_json.cueini == 'S')
								{					
									$("#cueini_si").attr('checked', true);
								}
								else
								{									
									$("#cueini_no").attr('checked', true);
								}
								
								if(data_json.cuesni == 'S')
								{					
									$("#cuesni_si").attr('checked', true);
								}
								else
								{									
									$("#cuesni_no").attr('checked', true);
								}
								
								if(data_json.cuesni == 'S')
								{					
									$("#cuesni_si").attr('checked', true);
								}
								else
								{									
									$("#cuesni_no").attr('checked', true);
								}
								
								if(data_json.cuebas == 'S')
								{					
									$("#cuebas_si").attr('checked', true);
								}
								else
								{									
									$("#cuebas_no").attr('checked', true);
								}
								
								if(data_json.cuenat == '1')
								{					
									$("#cuenat_debito").attr('checked', true);
								}
								else
								{									
									$("#cuenat_credito").attr('checked', true);
								}
								
								if(data_json.cuecie == 'S')
								{					
									$("#cuecie_si").attr('checked', true);
								}
								else
								{									
									$("#cuecie_no").attr('checked', true);
								}
								
								if(data_json.cueaju == 'I')
								{					
									$("#cueaju_i").attr('checked', true);
								}
								else if(data_json.cueaju == 'G')
								{									
									$("#cueaju_g").attr('checked', true);
								}
								else
								{									
									$("#cueaju_n").attr('checked', true);
								}
								
								if(data_json.cueori == 'C')
								{					
									$("#cueori_c").attr('checked', true);
								}
								else if(data_json.cueaju == 'A')
								{									
									$("#cueori_a").attr('checked', true);
								}
								else if(data_json.cueori == 'F')
								{									
									$("#cueori_f").attr('checked', true);
								}
								else
								{
									$("#cueori_ninguno").attr('checked', true);
								}
								
								if(data_json.cueact == 'S')
								{					
									$("#cueact_si").attr('checked', true);
								}
								else
								{									
									$("#cueact_no").attr('checked', true);
								}								
								return;								
							}
						}	
					});					
		}

		//Guarda o actualiza una cuenta basado en los valores del formulario
		function grabar_datos(accion)
		{
			//asigna variables para el datamensaje
			var dato_oldcod = $("#oldcod").val();
			var dato_cuecod = $("#cuecod").val();
			var dato_cuenom = $("#cuenom").val().toUpperCase();
			var dato_cueniv = $("#cueniv").val();
			var dato_cueicc = $("input[name='cueicc']:checked").val();
			var dato_cuescc	= $("input[name='cuescc']:checked").val();
			var dato_cuesni	= $("input[name='cuesni']:checked").val();
			var dato_cueini = $("input[name='cueini']:checked").val();
			var dato_cuebas = $("input[name='cuebas']:checked").val();
			var dato_cuecon = $("#cuecon").val();
			var dato_cuenat = $("input[name='cuenat']:checked").val();
			var dato_cuecie = $("input[name='cuecie']:checked").val();
			var dato_cueaju = $("input[name='cueaju']:checked").val();
			var dato_cuecba = $("#cuecba").val();
			var dato_cueori = $("input[name='cueori']:checked").val();
			var dato_cueact = $("input[name='cueact']:checked").val();
			var dato_usuario = '<?php echo $wuse ;?>';

			//verifica que los campos necesarios no sean nulos
			if(dato_cuecod == "")
			{
				jAlert("Asegurese de haber ingresado el numero de la cuenta.");				
			}

			else if (dato_cuenom == "")
			{
				jAlert("Asegurese de haber ingresado el nombre de la cuenta.");
			}

			else if (accion == "actualizar" && dato_oldcod == "")
			{
				jAlert("Asegurese de haber seleccionado la cuenta a actualizar.");
			}
			//Se almacena del nuevo porcentaje, en el mensaje se especifica si es guardar o actualizar	
			else{
				$.ajax
					({
						url: "cuentascontablesniif.php",
						type: "POST",
						data:
						{
							consultaAjax 	: '',
							operacion 		: 'registrar_cuenta',
							dato_oldcod		: dato_oldcod,
							dato_cuecod		: dato_cuecod,
							dato_cuenom 	: dato_cuenom,
							dato_cueniv 	: dato_cueniv,
							dato_cueicc 	: dato_cueicc,
							dato_cuescc		: dato_cuescc,
							dato_cuesni		: dato_cuesni,
							dato_cueini 	: dato_cueini,
							dato_cuebas		: dato_cuebas,
							dato_cuecon 	: dato_cuecon,
							dato_cuenat 	: dato_cuenat,
							dato_cuecie 	: dato_cuecie,
							dato_cueaju 	: dato_cueaju,
							dato_cuecba 	: dato_cuecba,
							dato_cueori 	: dato_cueori,
							dato_cueact 	: dato_cueact,
							dato_usuario	: dato_usuario,
							dato_usuario	: dato_usuario,
							dato_accion		: accion  		
						},
						dataType: "json",			
						async: false,
						success:function(data_json) 
						{	
							//Se guarda o actualiza satisfactoriamente
							if (data_json.check == "ok")
							{
								jAlert("Datos ingresados satisfactoriamente.");	
								$('#picturegallery').trigger("chosen:updated");	
								$("#iActualizar").hide();
								$("#iGuardar").hide();							
							}
							//Ya existe la cuenta a guardar o a actualizar (en caso de que se actualice un numero de cuenta diferente a ella misma)
							else if(data_json.check != "ok")
							{																	
								jAlert("Ya existe una cuenta con el codigo ingresado, por favor verificar.");
							}							
						}	
							
					})				
				}		
		}
		
	</script>
	<body onload="javascript:longitudCodigo()">
	<?php 
		$wactualiz="2016-09-27";
		//$wtitulo="CUENTAS CONTABLES NIIF";
		$wtitulo="Nuevas Cuentas Norma Colombiana Con Parametro Niif";
		encabezado($wtitulo, $wactualiz, 'clinica');
	?>
	<div class="container">
		<div class="formbox">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">CUENTAS CONTABLES</div>
				</div>
				<div class="panel-body" >
					<form action="cuentascontablesniif.php" method="post" align=center  name="menuNiif" id="menuNiif">	
								<label for="funcion">															
									<b>&#191;Qu&#233; desea hacer?</b> 						
										<div>
											<div>
												<input type="radio" name="opcionusuario" id="opcionusuarioguardar" class="requerido" required onChange='activarBoton()'>
													<label for="opcionusuarioguardar">Guardar una cuenta nueva</label>
											</div>
										</div>
										<div>
											<input type="radio" name="opcionusuario" id="opcionusuarioactualizar" onChange='activarBoton()' >
												<label for="opcionusuarioactualizar">Actualizar una Cuenta Existente</label>
												<!--Div para el select de codigos de actualizacion -->
													<div class="reveal-if-active">
														<label for="codant">Seleccione el nivel y el c&oacute;digo de la cuenta a actualizar:</label>
															<select name='oldcod' id='oldcod' onChange='consultar_cuenta();'>
															<option selected="selected" value=""/>
															</select>	
															<br />
															<label for="codant">Por favor ingrese los nuevos valores de la cuenta:</label>
													</div>
										</div>	
										<div>
												<input type="radio" name="opcionusuario" id="opcionusuarioreset" required onChange='activarBoton()'>
													<label for="opcionusuario_reset">Restaurar Valores</label>
											</div>												
								</label>									
								</label>
								<div id="select_box">
									<label for="Nivel">
										<b>Nivel:</b>
										<select name='cueniv' id='cueniv' onchange="longitudCodigo();">
											<?php
												$query_niv="SELECT nivniv FROM coniv";
												$err2 = odbc_do($conex_o,$query_niv);

												while(odbc_fetch_row($err2))
												{
													echo "<option>".odbc_result($err2,1)."</option>";
												}
											?>
										</select>																										  
								</div>										
								<br />
								<div class="div_CodigoCuenta" id="div_CodigoCuenta">
									<b>C&oacute;digo de la Cuenta:</b> 					
									<input type="text" name="cuecod" id="cuecod" align=left onkeypress='return numeros(event)' required 
									value="<?php if(isset($_POST['cuecod'])) {echo $_POST['cuecod'];}?>"/>
								</div>
								<br />
								</label>
								<div class="div_NombreCuenta" id="div_NombreCuenta">
									<b>Nombre de la Cuenta:</b>							
									<input type="text" name="cuenom" id="cuenom" size="40" maxlength="30" required
									value="<?php if(isset($_POST['cuenom'])) {echo $_POST['cuenom'];}?>"/>
								</div>
								<br />
								</label>																
								<div class="div_ContabilizaCC" id="div_ContabilizaCC">
									<label for="div_ContabilizaCC"><b>&#191;Se contabiliza por CC?:</b></label>
										<input type="Radio" name="cueicc" value="S" id="cueicc_si" checked="checked" required>S&#237;
										<input type="Radio" name="cueicc" value="N" id="cueicc_no">No
								</div>	
								<br />
								<div class="div_SaldosCC" id="div_SaldosCC">
									<label for="div_SaldosCC"><b>&#191;Lleva Saldos por CC?:</b></label>
										<input type="Radio" name="cuescc" value="S" id="cuescc_si" checked="checked" required>S&#237;
										<input type="Radio" name="cuescc" value="N" id="cuescc_no">No
								</div>	
								<br />
								<div class="div_ContabilizaTerceros" id="div_ContabilizaTerceros">
									<label for="div_ContabilizaTerceros"><b>&#191;Se contabiliza por terceros?:</b></label>
										<input type="Radio" name="cueini" value="S" id="cueini_si" checked="checked" required>S&#237;
										<input type="Radio" name="cueini" value="N" id="cueini_no">No
								</div>	
								<br />
								<div class="div_SaldosTerceros" id="div_SaldosTerceros">
									<label for="div_SaldosTerceros"><b>&#191;Lleva Saldos por terceros?:</b></label>
										<input type="Radio" name="cuesni" value="S" id="cuesni_si" checked="checked" required>S&#237;
										<input type="Radio" name="cuesni" value="N" id="cuesni_no">No
								</div>	
								<br />
								<div class="div_InformacionBase" id="div_InformacionBase">
									<label for="div_InformacionBase"><b>&#191;Lleva informaci&#243;n base?:</b></label>
										<input type="Radio" name="cuebas" value="S" id="cuebas_si" checked="checked" required>S&#237;
										<input type="Radio" name="cuebas" value="N" id="cuebas_no">No
								</div>	
								<br />	
								<div class="div_Concepto" id="div_Concepto">
									<label for="div_Concepto"><b>C&oacute;digo del Concepto:</b></label>				
										<?php
											echo "<select name='cuecon' id='cuecon'>";

											$query_con = " SELECT concod FROM cocon";
											$err3 = odbc_do($conex_o,$query_con);		

											while (odbc_fetch_row($err3))
											{
												echo "<option>".odbc_result($err3,1)."</option>";
											}
											
											echo "</select></td>";
										?>
								</div>
								<br />
								</label>
								<div class="div_Naturaleza" id="div_Naturaleza">
									<label for="div_Naturaleza"><b>Naturaleza:</b></label>
										<input type="Radio" name="cuenat" value="1" id="cuenat_debito" checked="checked" required>D&#233;bito
										<input type="Radio" name="cuenat" value="2" id="cuenat_credito">Cr&#233;dito
								</div>	
								<br />
								<div class="div_CuentaCierre" id="div_CuentaCierre">
									<label for="div_CuentaCierre"><b>&#191;Es cuenta de cierre?:</b></label>
										<input type="Radio" name="cuecie" value="S" id="cuecie_si" checked="checked" required>Si
										<input type="Radio" name="cuecie" value="N" id="cuecie_no">No
								</div>	
								<br />
								<div class="div_CuentaAjuste" id="div_CuentaAjuste">
									<label for="div_CuentaAjuste"><b>&#191;Es cuenta de ajuste?:</b></label>
										<input type="Radio" name="cueaju" value="I" id="cueaju_i" checked="checked" required>Individual (I)
										<input type="Radio" name="cueaju" value="G" id="cueaju_g">Por Grupo (G)
										<input type="Radio" name="cueaju" value="N" id="cueaju_n" >No se Ajusta (N)
								</div>	
								<br />							
								<div class="div_CuentaBancaria" id="div_CuentaBancaria">
									<label for="div_CuentaBancaria"><b>N&uacute;mero de la Cuenta Bancaria:</b></label>
									<input type="text" name="cuecba" id="cuecba" onkeypress='return numeros_bancarios(event)' value="<?php if(isset($_POST['cuecba'])) {echo $_POST['cuecba'];}?>"/>
								</div>
								<br />
								</label>
								<div class="div_AnexosTributarios" id="div_AnexosTributarios">
									<label for="div_AnexosTributarios"><b>Origen de anexos tributarios:</b></label>
										<input type="Radio" name="cueori" value="C" id="cueori_c" requiered>Contabilidad (C)
										<input type="Radio" name="cueori" value="A" id="cueori_a">Cartera (A)
										<input type="Radio" name="cueori" value="F" id="cueori_f">Facturaci&oacute;n (F)
										<input type="Radio" name="cueori" value="" id="cueori_ninguno" checked="checked">Ninguno
								</div>	
								<br />
								<div class="div_Activo" id="div_Activo">
									<label for="div_Activo"><b>Activo:</b></label>
										<input type="Radio" name="cueact" value="S" id="cueact_si" checked="checked" required>S&#237;
										<input type="Radio" name="cueact" value="N" id="cueact_no">No
								</div>	
								<br />
								
								<input type="button" id="iGuardar" name="iGuardar" value="GUARDAR" style="display:none" onclick="grabar_datos('guardar')">
								<input type="button" id="iActualizar" name="iActualizar" value="ACTUALIZAR" style="display:none" onclick="grabar_datos('actualizar')">
					</form>           
				</div>
			</div>
		</div>
	</div>			
	</body>
</html>

