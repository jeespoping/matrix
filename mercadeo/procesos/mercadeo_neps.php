<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION: Programa para la homologacion de articulos de la nueva eps, para la modificaion, adicion y eliminacion de esos articulos en
// 						la tabla ivartneps.
//AUTOR:				Carolina Londono A.
//FECHA DE CREACION: 	2016-10-12
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
// 2017-12-26 Se agregan funciones para descargar el archivo tanto en php, javascript/ajax
//--------------------------------------------------------------------------------------------------------------------------------------------
	session_start();
	$wactualiz="2017-12-26";

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
		//includes
		

		include_once("root/comun.php");
		//almacena las variables de usuario y se conecta a la base de datos de UNIX
		$user_session = explode('-', $_SESSION['user']);
		$wuse = $user_session[1];
		$archivo = $_SERVER["SCRIPT_NAME"];

		//Conexion con matrix y unix
		

		$conex = obtenerConexionBD("matrix");
		$conex_o = @odbc_connect('facturacion','','');

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

		//Funcion que consulta toda la base de datos de articulos de la nueva eps para descargar el archivo de excel
		if(isset($operacion) && $operacion == 'descargar_archivo')
		{
			// $datamensaje = array('url'=>'');
			$arr_all_arts = array();
			// Variables para ejecutar el query en Unix
			$camposNulos  = "codcli, descli, clasi, codneps, nombreneps";
			$valresDefecto  = "'', '', '', '', ''";
			$select = "codcli, descli, clasi, codneps, nombreneps";
			$from  = "ivartneps";
			$where  = "";

			//Se construye y ejecuta el query
			$sql = construirQueryUnix($from, $camposNulos, $select, $where , $valresDefecto);
			$resultado = odbc_do($conex_o,$sql);

			//agregar a un array cada fila de la consulta sql
		  while ($fila = odbc_fetch_row($resultado))
		  {
						 $arr_all_arts[] =  array(
							 													"codcli"   => utf8_encode(trim(odbc_result($resultado, 'codcli'))),
																				"descli"   => utf8_encode(trim(odbc_result($resultado, 'descli'))),
																				"clasi"   => utf8_encode(trim(odbc_result($resultado, 'clasi'))),
																				"codneps"   => utf8_encode(trim(odbc_result($resultado, 'codneps'))),
																				"nombreneps"   => utf8_encode(trim(odbc_result($resultado, 'nombreneps')))
																			);
			}

			$dir = '../../planos/Nueva_Eps'; //Carpeta donde queda el archivo a descargar
			//Si no existe el directorio, se crea
			if(!is_dir($dir))
			{
				mkdir($dir,0777);
			}

			$file_name = 'articulos_neps.csv';//Se nombra el archivo
			$dir_file = $dir.'/'.$file_name; // Directorio archivos planos
			$file = fopen($dir_file,"w"); //Se abre el archivo para escribir sobre el
			//Se escribe el encabezado al inicio del archivo plano
			fputcsv($file, array('Codigo Clinica', 'Nombre Generico', 'Clasificacion', 'Codigo NEPS', 'Nombre NEPS'));
			//Se escribe en el archivo todos los datos del array
			foreach ($arr_all_arts as $line)
			{
			  fputcsv($file,$line);
			}
			//Se cierra el archivo
			fclose($file);

			//Se envia el archivo
			echo $dir_file;
			return;
		}

		//Funcion que consulta todos los articulos activos de la clínica, estén o no en la nueva EPS
		function consultarCodigo($conex_o)
		{
			$arrcodnps = array();

			//Variables para ejecutar el query en Unix
			$camposNulos  = "artcod, artnom";
			$valresDefecto  = "'', ''";
			$select = "artcod, artnom";
			$from  = "ivart";
			$where  = "artact='S'";

			//Se construye y ejecuta el query
			$sql = construirQueryUnix($from, $camposNulos, $select, $where , $valresDefecto);
			$err1 = odbc_do($conex_o,$sql);

			//Para cada fila del query se crea un array, en el que la llave es el codigo de clínica
			//y su valor es el nombre de la clínica (se hace una limpieza de estos datos primero antes de relacionarlos)
			while (odbc_fetch_row($err1))
			{
				$codcli = odbc_result($err1, 'artcod');
				$nomcod = utf8_encode(odbc_result($err1, 'artnom'));
				$nomcod = str_replace("'", "", $nomcod);
				$nomcod = str_replace('"', "", $nomcod);
				$arrcodnps[$codcli] = ($nomcod);
			}

      return $arrcodnps;
		}

		//Funcion que consulta las diferentes clasificaciones de los articulos de la nueva eps
		function consultar_clasificacion($conex_o)
		{
			$arr_clasi = array();
			//Variables para ejecutar el query en Unix
			$query_clasi = "SELECT DISTINCT clasi FROM ivartneps ORDER BY clasi";
			$resultado = odbc_do($conex_o, $query_clasi);

		  //agregar a un array cada fila de la consulta sql
			while ($fila = odbc_fetch_row($resultado)){
							$arr_dato = odbc_result($resultado,"clasi");
							array_push($arr_clasi,$arr_dato);
				}
      return $arr_clasi;
		}

		//Extrae los datos de los articulos de la nueva EPS
		if(isset($operacion) && $operacion == 'extraerInfoCodigo')
		{
			//Elementos del mensaje json
			$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'codcli'=>'',
			'clasi'=>'', 'codneps'=>'', 'nombreneps'=>'', 'existente'=>'');

			//Verifico si existe en la tabla ivartneps primero
			$check = odbc_do($conex_o,"SELECT count(*) as counter FROM ivartneps WHERE codcli='".$dato_codigo."'");
			$arr = odbc_fetch_array($check);
			//Si el artículo ya está en la base de datos de artículos de la nuevaeps
			if($arr['counter'] > 0)
			{
				$camposNulos  = "codneps, nombreneps";
				$valresDefecto  = "'', ''";
				$select = "codcli, clasi, codneps, nombreneps";
				$from  = "ivartneps";
				$where  = "codcli='".$dato_codigo."'";

				//Se ejecuta el query en Unix
				$sql = construirQueryUnix($from, $camposNulos, $select, $where , $valresDefecto);
				$res = odbc_do($conex_o,$sql);
			  $arr = odbc_fetch_array($res);

			  //Se envian los resultados de la consulta a cada posision del datamensaje
			  $datamensaje['codcli']  	= trim($arr['codcli']);
				$datamensaje['clasi']  	= trim(utf8_encode($arr['clasi']));
			  $datamensaje['codneps'] 	= trim($arr['codneps']);
			  $datamensaje['nombreneps'] 	= trim(utf8_encode($arr['nombreneps']));
			  $datamensaje['existente'] 	= 'Si';
			}
		   	echo json_encode($datamensaje);
		   	return;
		}

		//Funcion que inserta, modifica y elimina datos en la tabla ivartneps, y realiza un log en la tabla ivlog
		if(isset($operacion) && $operacion == 'modificar_articulo')
		{
			//Fecha de guardado o actualizacion de un artiuclo
			$fecha = date("Y-m-d H:i:s",time());
			$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'');

			//Si la accion del datamensaje es guardar, la consulta es un insert a la base de datos
			if($dato_accion == 'guardar')
			{
				//verifica si el articulo ya existe en la tabla ivartneps (para evitar hacer el insert)
				$check = odbc_do($conex_o,"SELECT count(*) as counter FROM ivartneps WHERE codcli='".$dato_codcli."'");
				$arr = odbc_fetch_array($check);
					//Si ya existe el artículo a insertar se agrega en el datamensaje Repetido: Si para el mensaje de error
					if($arr['counter'] > 0)
					{
						$datamensaje['Repetidoneps'] = 'Si';
					}
					//Si no existe el artículo en la base de datos, se inserta
					else
					{
						$sql="INSERT INTO ivartneps(codcli, descli, clasi, codneps, nombreneps)
							VALUES
							('".$dato_codcli."','".utf8_decode($dato_descli)."','".utf8_decode($dato_clasi)."','".$dato_codneps."','".utf8_decode($dato_nombreneps)."')";

						$resultado = odbc_do($conex_o,$sql);

						$datamensaje['Guardar'] = 'Si';
					}
			}

			//Si el articulo ya existe en la bd de los articulos de la nueva EPS y el usuario desea actualizar los datos
			else if($dato_accion =='actualizar')
			{
				$sql="UPDATE ivartneps
				SET descli = '".utf8_decode($dato_descli)."', clasi = '".utf8_decode($dato_clasi)."', codneps='".$dato_codneps."', nombreneps='".utf8_decode($dato_nombreneps)."'
				WHERE
				codcli='".$dato_codcli."'";

				$resultado = odbc_do($conex_o,$sql);
			}

			//Si el articulo ya existe en la bd de los articulos de la nueva EPS y el usuario desea eliminar los datos
			else if($dato_accion =='eliminar')
			{
					$sql="DELETE FROM ivartneps	WHERE codcli='".$dato_codcli."'";

					$resultado = odbc_do($conex_o,$sql);
			}

			//Si se eliminó, guardó o actualizó correctamente el artículo se guarda en la tabla de logs ivlog
			if($resultado)
			{
				$sql2="INSERT INTO ivlog(logusu, logter, logpro, logope, logde1,
				logva1, logde2, logva2, logde3, logva3, logtip, logtab, logfec)
						VALUES
						('".$wuse."','".$dato_terminal."','".$dato_programa."',
						'".$dato_operacion."','".$dato_labelcodigo."',
						'".$dato_codcli."','".$dato_labelneps."',
						'".$dato_codneps."','".$dato_labelncorto."',
						'".$dato_nombrecorto."','".$dato_tipoconsulta."',
						'".$dato_tabla."','".$fecha."')";

						$resultado2 = odbc_do($conex_o,$sql2);

				//Si se guardó el log exitosamente
				if($resultado2)
				{
					$datamensaje['check'] = 'Acci&oacute;n realizada satisfactoriamente.';
				}
				else
				{
					$datamensaje['check'] = 'Fall&oacute; grabado log.';
				}
			}

			//Si el dato ya existe (No es probable)
			else if($datamensaje['Repetidoneps'] = 'Si')
			{
				$datamensaje['check'] = 'El art&iacute;culo ya existe en la base de datos de art&iacute;culos de la Nueva EPS.';
			}

			//Si no se pudo ejecutar ningun query
			else
			{
				$datamensaje['check'] = "Fall&oacute; el grabado del art&iacute;culo, por favor verifique.";
			}
			//envia la respuesta del datamensaje
			echo json_encode($datamensaje);
			return;
		}
	}
?>
<html>
	<head>
		<link href="Style4.css" rel="stylesheet">
		<title>Articulos Nueva EPS - Mercadeo</title>
	</head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript">
		//Document ready
		$(function()
		{
			cargar_autocomplete();
	  });//Finaliza el ready

		//Funcion que llena el autocomplete con todos los articulos de la clínica de la tabla ivart
		function cargar_autocomplete()
		{
			//Autocompletar para la busqueda de articulos en la tabla ivart, para el ingreso de informacion nueva
			var arr_cod  = eval('(' + $('#arr_cod').val() + ')');
      var codigos = new Array();
			var index   = -1;

	      for (var cod_cen in arr_cod)
	        {
	              index++;
	              codigos[index]                = {};
	              codigos[index].value          = cod_cen;
	              codigos[index].label          = cod_cen+'-'+arr_cod[cod_cen];
	              codigos[index].codigo         = cod_cen;
	              codigos[index].nombre         = arr_cod[cod_cen];
	        }
	        //Se le da el autocomplete al input
	        $("#item_ivart").autocomplete
	        ({
	        	minLength:  5,
		        source: codigos,
		        autoFocus: true,
		        select:     function( event, ui )
		        {
		            var cod_sel = ui.item.codigo;
		            var nom_sel = ui.item.nombre;
		            $("#item_ivart").attr("codigo",cod_sel);
		            $("#nombre_clinica").val(nom_sel);
		            extraerInfoCodigo(cod_sel, nom_sel);
	        	},
						//En caso de que cambie el input donde està el autocomplete
	        	change: function( event, ui)
	           	{
	         			var a = $('#item_ivart').val();
	         			var b = $('#codcli').val();

         			if( b != a)
         			{
         				$('#codneps').val('');
								$('#nombreneps').val('');
								$('#codcli').val('');
								//se recarga la página en caso de que borren todo el còdigo
								if( a.length < 1 )
								{
										$('#nombre_clinica').val('');
								}
         			}

      			}
	        });
		}
		//Extrae la info del dato seleccionado en el autocomplete de articulos de la clinica
    function extraerInfoCodigo(cod_sel, nom_sel)
    {
      	//Asigno a una variable, el codigo del item buscado
      	var dato_codigo = cod_sel;

      		$.ajax
      		({
      			url: 	"mercadeo_neps.php",
      			type: 	"POST",
      			data:
      			{
      				consultaAjax	: '',
      				operacion		: 'extraerInfoCodigo',
      				dato_codigo		: dato_codigo
      			},
      			dataType : "json",
      			async    : false,
      			success :function(data_json)
      			{
      				$('#codneps').attr('readonly', false);
							$('#nombreneps').attr('readonly', false);

      				if (data_json.error == 1)
					{
						jAlert("Se present&oacute; un error, comun&iacute;quese con Soporte de Matrix.", "Error");
						return;
					}
							//Muestra la información del artículo que ya existe en la tabla ivartneps (o esté homologado y necesiten modificarle algo)
      				else if(data_json.existente == 'Si')
      				{
      					jAlert("El art&iacute;culo es de la Nueva EPS, puede actualizarlo o eliminarlo.", "Alerta");
      					$("#iGuardar").hide();
								$("#iActualizar").show();
								$("#iEliminar").show();
								$("#codcli").val(data_json.codcli);
	      				$("#codneps").val(data_json.codneps);
	      				$("#nombreneps").val(data_json.nombreneps);
								$("#clasi").attr("disabled", false);
								$("#clasi").val(data_json.clasi);
								$("#.",data_json.clasi).attr("selected", true);

      				}

							// En caso de que el artículo no esté en la tabla ivartneps (es decir, que no está homologado)
      				else
      				{
      					jAlert("El art&iacute;culo no pertenece a los art&iacute;culos de la Nueva EPS, puede guardarlo como uno nuevo.", "Alerta");
      					$("#iGuardar").show();
								$("#iActualizar").hide();
								$("#iEliminar").hide();
								$("#codcli").val(dato_codigo);
								$("#nombreneps").val(nom_sel);
								$("#clasi").attr("disabled", false);
      				}
      			}
           	});
      	}

		//Permitir solo en ingreso de letras o números
		function caracteres(e)
		{
			//key = e.keyCode || e.which;
			var key = window.Event ? e.which : e.keyCode

			return ((key >= 48 && key <= 57) || (key >64 && key <91) || (key >96 && key<123) || (key == 8));

		}

		//Funcion para actualizar el autocomplete luego de alguna actualización en la bd
		function actualizarConfirmado(msg, title)
		{
			jAlert(msg, title);
			$("#popup_ok").click(
			  function () {
			    location.reload();
			  });
		}

		//Funcion que envia los datos para la actualizacion, grabado o eliminado de datos
		function modificar_datos(accion)
		{
			var dato_codcli	 	 = $("#codcli").val().toUpperCase();
			var dato_codneps 	 = $("#codneps").val().toUpperCase();
			var dato_descli		 = $("#nombre_clinica").val().toUpperCase();
			var dato_clasi		 = $('#clasi :selected').text().toUpperCase();
			var dato_nombreneps  = $("#nombreneps").val().toUpperCase();
			var dato_terminal	 = "MATRIX";
			var dato_programa	 = "mercadeo_neps.php";
			var dato_operacion	 = accion.toUpperCase();
			var dato_labelcodigo = "CODIGO";
			var dato_tipoconsulta = "";
			var dato_labelneps	 = "CODIGO NEPS";
			var dato_labelncorto = "CARACTER NEPS";
			var dato_nombrecorto = dato_nombreneps.substring(0, 13);
			var dato_tabla		 = "ivartneps";

			// Los datos que se ingresan no pueden ser nulos, ya que pueden afectar el funcionamiento de otros programas en matrix.
			if (dato_codneps.length < 1)
			{
				dato_codneps = " ";
			}

			if (dato_nombreneps.length < 1)
			{
				dato_nombreneps = " ";
			}

			if (dato_clasi.length < 1)
			{
				dato_clasi = " ";
			}
			//Se determina el tipo de consulta para grabarla en la tabla de logs
			switch(accion)
			{
				    case "eliminar":
				        dato_tipoconsulta = "D";
				        break;
				    case "guardar":
				        dato_tipoconsulta = "I";
				        break;
				     case "actualizar":
				        dato_tipoconsulta = "U";
				        break;
				    default:
				        dato_tipoconsulta = "";
				        break;
			}

			//Se verifica que si se tenga un codigo de clinica válido para la consulta
			if(dato_codcli.length < 6)
			{
				jAlert("Aseg&uacute;rese de haber ingresado correctamente el codigo del art&iacute;culo.", "Seleccione un art&iacute;culo");
			}
			// Si los parámetros son correctos para realizar cambios en la bd, se le pide que confirme la acción, y luego se ejecute a través
			// de ajax
			else
			{
				jConfirm("Est&aacute; seguro de que desea " + accion + " el art&iacute;culo " + dato_codcli + "?", "Confirmar", function (respuesta)
				{
					if(respuesta == true)
					{
						$.ajax
						 ({
						 	url: "mercadeo_neps.php",
						 	type: "POST",
							data:
							{
								consultaAjax 	 : '',
								operacion		 : 'modificar_articulo',
								dato_codcli		 : dato_codcli,
								dato_codneps 	 : dato_codneps,
								dato_descli		:  dato_descli,
								dato_clasi		:  dato_clasi,
								dato_nombreneps  : dato_nombreneps,
								dato_terminal 	 : dato_terminal,
								dato_programa 	 : dato_programa,
								dato_accion 	 : accion,
								dato_operacion   : dato_operacion,
								dato_labelcodigo : dato_labelcodigo,
								dato_tipoconsulta: dato_tipoconsulta,
								dato_labelncorto : dato_labelncorto,
								dato_nombrecorto : dato_nombrecorto,
								dato_labelneps	 : dato_labelneps,
								dato_tabla		 : dato_tabla
							},
							dataType: "json",
							async: false,
							success: function (data_json)
							{
								if(data_json.Repetidoneps != 'Si')
								{
									actualizarConfirmado(data_json.check, "Alerta");
								}
								else
								{
									jAlert(data_json.check);
								}
							}
						});
					}
					else
					{
						jAlert("Acci&oacute;n cancelada.");
						return false;
					}
				});
			}
		}

	//Funcion para descargar el arvhivo plano, para la respuesta de ajax
	function downloadURI(uri, name)
	{
			 var link = document.createElement("a");
			 link.download = name;
			 link.href = uri; link.click();
	 }

		//Funcion que Descarga el arvchivo de excel a traves de ajax
		function descargar_archivo()
		{
			 $.ajax({
			 url:"mercadeo_neps.php",
			 type: "POST",
			 data: {
				 				consultaAjax 	 : '',
				 				operacion		: 'descargar_archivo'
							},
			 success:function(data)
			 {
				  // window.open(data);
					downloadURI(data,"articulos_neps.csv");
			 }
			 });
		}

	</script>
	<body>
	<?php
		//Array para cargar el autocomplete de la tabla ivart de unix
		$arr_cod = array();
		//Array para el dropdownlist de clasificacion
		$arr_clasi = array();
		$arr_clasi = consultar_clasificacion($conex_o);
		$arr_cod = consultarCodigo($conex_o);
		//Datos para el encabezado de matrix
		$wtitulo = "ARTICULOS NUEVA EPS - MERCADEO";
		encabezado($wtitulo, $wactualiz, 'clinica');
	?>
	<div class="container">
		<div class="formbox">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">ART&Iacute;CULOS NUEVA EPS</div>
				</div>
				<div class="panel-body" >
					<form action="mercadeo_neps.php" method="post" align=center  name="menuNeps" id="menuNeps">
								<label for="funcion">
										<div>
											<label for="opcionusuarioguardar">Digite el nombre o el c&oacute;digo de cl&iacute;nica del art&iacute;culo:</label>
						                	<input type="text" name="item_ivart" id="item_ivart"  size="40" autocomplete="onkeypress" required>
												<ul id="artneps_list_id"></ul>
											<br />
												<label for="codant">Por favor ingrese los valores para el art&iacute;culo nuevo:</label>
												</br>
												<label for="codant">*Solamente se puede editar la clasificaci&oacute;n, el c&oacute;digo y nombre de los campos pertenecientes a la nueva EPS.</label>
										</div>
										<div class="div_formulario" id="div_formato_porcentaje">

										</div>
								</label>

                                <div class="div_codclinica" id="div_codclinica">
                                    <b>C&oacute;digo Cl&iacute;nica:</b>
                                    <input type="text" name="codcli" id="codcli" align=left onkeypress='return caracteres(event)' readonly required/>
                                </div>
                                <br />

																<div class="div_genericlinica" id="div_genericlinica">
                                    <b>Nombre Gen&eacute;rico:</b>
																		<input type="text" name="nombre_clinica" id="nombre_clinica" size="50"  maxlength="60" readonly/>
                                </div>
                                <br />

																<div class="div_clasi" id="div_clasi">
                                    <b>Clasificaci&oacute;n del art&iacute;culo:</b>
																		<select name='clasi' id='clasi' disabled>
																			<?php foreach($arr_clasi as $key => $value) { ?>
																		    <option value="<?php echo $value ?>" id="<?php echo $key ?>" ><?php echo $value ?></option>
																		  <?php }?>
																		</select></td>
																</div>
																<br />

																<input type="HIDDEN" name="arr_art" id="arr_art" value='<?=json_encode($arr_clasi)?>'>
                                <input type="HIDDEN" name="arr_cod" id="arr_cod"	 value='<?=json_encode($arr_cod)?>'>

                                <div class="div_codneps" id="div_codneps">
                                    <b>C&oacute;digo Nueva EPS:</b>
                                    <input type="text" name="codneps" id="codneps" maxlength="8"  onkeypress='return caracteres(event)' onpaste="return false;"readonly/>
                                </div>
                                <br />

                                <div class="div_nombreart" id="div_nombreart">
                                    <label for="div_nombreart"><b>Nombre Nueva EPS:</b></label>
                                    <input type="text" name="nombreneps" id="nombreneps" size="50"  maxlength="144" readonly/>
                                </div>
                                <br />

                                <input type="button" id="iGuardar" name="iGuardar" value="GUARDAR" style="display:none" onclick="modificar_datos('guardar')">
                                <input type="button" id="iActualizar" name="iActualizar" value="ACTUALIZAR" style="display:none" onclick="modificar_datos('actualizar')">
                                <input type="button" id="iEliminar" name="iEliminar" value="ELIMINAR" style="display:none" onclick="modificar_datos('eliminar')">
																<input type="button" id="iDescargar" name="iDescargar" value="DESCARGAR EXCEL"  onclick="descargar_archivo()">
                        <div>
                        	<br />
							<input type="radio" name="opcionusuario" id="opcionusuarioreset" required onchange='location.reload()'>
							<label for="opcionusuario_reset">Restaurar Valores.</label>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	</body>
</html>
