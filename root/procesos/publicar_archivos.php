<?php
include_once("conex.php");
session_start();

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");
      
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");


if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wusuario = substr($user, 2, strlen($user)); //Se eliminan los dos primeros digitos

//Modificacion 11 de Julio 2019 Freddy Saenz , ordenar alfabeticamente o por fechas segun configuracion en root51Buscar
$q_orden =  " SELECT Detval, Detemp "
       ."   FROM root_000051"
	   ."  WHERE Detapl = 'OrdenParaPublicarArchivos'"
	   ."	 AND Detemp = '".$wemp_pmla."'"
	   ."	 AND Detval LIKE '%".$grupo."%'";


	   
$res_orden = mysql_query($q_orden,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_orden." - ".mysql_error());	
$row_orden = mysql_fetch_array($res_orden);
$ordengrupo = $row_orden['Detval'];
$posalfabetico = strpos($ordengrupo, "alfabetico");



$wordenxfecha = 1;//por defecto o si no tiene registro , se ordena por fechas.
if ($posalfabetico === false) {//No es por orden alfabetico , entonces es por orden de fechas 
	
}else{
	$wordenxfecha = 0;//es por orden alfabetico = NO x Fecha, solo existen estos dos ordenamientos 
}



//Tamaño maximo para publicacion de archivos.
$q_t =  " SELECT Detval, Detemp "
       ."   FROM root_000051"
	   ."  WHERE Detapl = 'TamanoMaximoPublicacionArchivos'"
	   ."	 AND Detemp = '".$wemp_pmla."'";
$res_t = mysql_query($q_t,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_t." - ".mysql_error());	
$row_t = mysql_fetch_array($res_t);
$maxFileSize = $row_t['Detval'];

//Buscar las extensiones habilitadas para el grupo y la empresa.
$q_a =  " SELECT Detval, Detemp "
       ."   FROM root_000051"
	   ."  WHERE Detapl = 'ExtHabilitadasPublicacion'"
	   ."	 AND Detemp = '".$wemp_pmla."'";
$res_a = mysql_query($q_a,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_a." - ".mysql_error());	
$row_a = mysql_fetch_array($res_a);
$numExtensiones = mysql_num_rows($res_a);

$wext_emp_grupo = $row_a['Detval'];
$array_grupos_emp = explode("-",$wext_emp_grupo);


//Busca la carpeta definida en la variable $carpeta que viene en la url
$q_c =  " SELECT Detval, Detemp "
       ."   FROM root_000051"
	   ."  WHERE Detapl = '".$carpeta."'"
	   ."	 AND Detemp = '".$wemp_pmla."'";
$res_c = mysql_query($q_c,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_c." - ".mysql_error());	
$row_c = mysql_fetch_array($res_c);
$wcarpeta_asociada = $row_c['Detval'];
//11 de febrero 2019 Si no tiene carpeta asociada , ver si tiene una configuracion en la tabal root_000051 
$wcarpeta_root51 = "";

$num = mysql_num_rows($res_c);
if ($num == 0)
{
	//MODIFICACION : 8 DE FEBRERO 2019
	//AUTOR : Freddy Saenz
	//IMPORTANTE , el valor que se va a usar de la consulta no es Detval , sino  Detdes
	//esto debido a que se necesita un nivel mas , ejemplo CarpetaDePublicacion = nivel 1
	// pero hay que identificar 2 fondos : mutuo y de empleados que hace necesario otro nivel ,
	// y ahora necesitamos especificar el valor en si , 
	// que en este caso el la carpeta donde estararn ubicados los archivos
	// y se usa entonces el campo Detdes  (ej. ../../fondos/extractos/fondo_mutuo)
	
	$q_c2 =  " SELECT Detdes, Detemp  "
		   ."   FROM root_000051"
		   ."  WHERE Detapl = 'CarpetaDePublicacion'"//ejemplo CarpetaDePublicacion
		   ."	 AND Detemp = '".$wemp_pmla."'"//01
		   ."	 AND Detval = '".$aplicacion."'";//fondo_mutuo
		   //Detapl = '".$aplicacion."'"
		  // aplicacion=fondo_mutuo
		  // FONDO_MUTUO

		  
	$res_c2 = mysql_query($q_c2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_c2." - ".mysql_error());	
	$num2 = mysql_num_rows($res_c2);
	if ($num2 > 0){
		$row_c2 = mysql_fetch_array($res_c2);
		$wcarpeta_asociada = $row_c2['Detdes'];
		
		$wcarpeta_root51 = $wcarpeta_asociada;
	}elseif ($aplicacion != "")//se especifico una aplicacion , pero no se creo el registro en la tabla root_000051
	//donde se especifique la ruta asociada
	{
		//ej. Detemp = 01 , Detapl = CarpetaDePublicacion , Detval = fondo_mutuo y Desdes = ../../fondos/extractos/fondo_mutuo
		//OJO , aqui se usa Desdes para el valor y no Detval como se hace normalmente .
		//en root_000021 se crea un registro de la forma:
		//Descripcion : Cargar Extractos , Programa = publicar_archivos.php?wemp_pmla=01&grupo=fondos&aplicacion=fondo_mutuo
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO." no hay registro en root_000051  para CarpetaDePublicacion ");
		
	}
	
}else{
	$row_c = mysql_fetch_array($res_c);
	$wcarpeta_asociada = $row_c['Detval'];	


 
}


//Crear las carpetas intermedias en donde van a publicarse los archivos.
$carpetaTmp = $wcarpeta_root51;

$arrCarpetas = explode("/",$carpetaTmp);
$pathCompleto ="";
foreach ($arrCarpetas as $key => $value )
{
	$pathCompleto = $pathCompleto.$value."/";
	if ($value == ""){
		
	}elseif ($value == ".."){
	
	}elseif ($value == "."){
		
	}elseif (!file_exists($pathCompleto)){
		mkdir($pathCompleto,0777);
		//echo " no existe ".$pathCompleto;
	}else{
//		echo "  existe ".$pathCompleto;
	}
}


$array_final = array();

//Recorro el array grupos por empresa
foreach($array_grupos_emp as $key => $value){
	//separo el valor por dos puntos, en la primera posicion esta el grupo y en la segunda las extensiones
	$array_grupo_extension = explode(":",$value);
	//Creo un nuevo arreglo donde la clave es el grupo y el valor son las extensiones habilitadas.
	if(!array_key_exists($array_grupo_extension[0], $array_final)){
	
		$array_final[$array_grupo_extension[0]] = $array_grupo_extension[1];
	}

}
//Para saber las extensiones busco dentro de la clave el nombre del grupo.
$wextensiones = $array_final[$grupo];

//Modificacion 19 Febrero 2019 , Freddy Saenz
//Se comenta la siguiente linea porque no hace validacion de la existencia del registro
// funciona correctamente en desarrollo , pero en produccion no funciona.
//if($row_a['Detval'] == '') $wextensiones = 'pdf' ;


if ($numExtensiones == 0){
	if ($wextensiones == ''){
		$wextensiones = 'pdf' ;
	}	
}

$array_extensiones1 = explode(",", $wextensiones);

//Buscar las extensiones habilitadas para el grupo y la empresa.
$q_h =  " SELECT Detval, Detemp "
       ."   FROM root_000051"
	   ."  WHERE Detapl = 'HabilitadosPublicacionArchivos'"
	   ."	 AND Detemp = '".$wemp_pmla."'";
$res_h = mysql_query($q_h,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_h." - ".mysql_error());	
$row_h = mysql_fetch_array($res_h);
$wusuario_habilitados = $row_h['Detval'];
$wbuscar_usuario_habilitado = strpos($wusuario_habilitados, $wusuario);


if($carpeta != ''){

	if ($wcarpeta_asociada != ''){
		$wconf_publicacion = explode(":", $wcarpeta_asociada);
		$wextensiones = $wconf_publicacion[1];
	}

}

if(!isset($consultaAjax)){
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
<title>Publicaciones</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 


</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/ajaxupload-min.js" type="text/javascript"></script>	
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

<link type="text/css" href="../../../include/root/upload.css" rel="stylesheet" />
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />

<body>
<?php

$wactualiz="2019-07-15";
/*
//==========================================================================================================================================
//PROGRAMA				      :Programa que permite subir publicaciones a una carpeta definida en la url con el parametro grupo, 
//							   ademas lista en el lado derecho las publicaciones que estan en ese grupo y al seleccionar una de ellas se muestra al lado derecho de la interfaz.                                                            
//AUTOR				          :Jonatan Lopez Aguirre.                                                                        
//FECHA CREACION			  :Marzo 10 de 2014

//Modificaciones
//===========================================================
 14 de Abril de 2014 (Jonatan Lopez): Se agrega control del usuario que mueve (elimina) el archivo de la lista.
//===========================================================
 11 de Abril de 2014 (Jonatan Lopez): Se controla la carpeta a la cual se hacen las publicaciones.
//===========================================================
 25 Marzo de 2014 Jonatan Lopez: Se parametriza el tamaño maximo de publicacion de archivos en la root_000051 con el parametro TamanoMaximoPublicacionArchivos.   
//===========================================================
  5 de Julio 2017 Jonatan Lopez: Se separan los archivos por año en acordeon para tener mejor visualizacion.
//===========================================================
11 de Febrero 2019 Freddy Saenz : Los archivos de los fondos (mutuo y de empleados ), deben ir a un directorio especifico independiente
//del grupo que los esta publicando o a una subcarpeta dentro del grupo .
//===========================================================
// 15 de Julio 2019 Freddy Saenz : Se ordenan los archivos de COPASO  por fecha y no por nombre , para que aparezcan ordenados por año de publicacion
//  se crean registros en root_000051 ( Detapl =  OrdenParaPublicarArchivos ) para configurar dinamicamente  este ordenamiento
// los posibles valores son : fecha y alfabetico , ej: fondos,orden=alfabetico , copaso,orden=fecha
//===========================================================
*/

if($grupo != ''){

	$texto = "Publicaciones ".$grupo;
	//Modificacion : 19 Febrero 2018 , Freddy Saenz, para diferenciar el programa usado no se usa el grupo sino 
	//el parametro aplicacion .
	if ($aplicacion != ''){
		$vaplicacion = str_replace('_', ' ', $aplicacion);
		$vaplicacion = str_replace('-', ' ', $vaplicacion);
		$texto = "Publicaciones " . $vaplicacion; //el titulo de despliegue ahora es la aplicacion, 
		// ej. fondo mutuo

	}
}else{

 	$texto = "Banner ".$carpeta;

} 

encabezado("$texto",$wactualiz, $wlogoempresa);

?>


<div align="center">	
	<table class="options">
		<tr>
			<th style="width:50%"></th>				
		</tr>				
		<tr>
			<td>
				<input type='hidden' id='grupo' value='<?php echo $grupo; ?>'>
				<input type='hidden' id='usuario' value='<?php echo $wusuario; ?>'>
				<input type='hidden' id='wemp_pmla' value='<?php echo $wemp_pmla; ?>'>				
				<input type='hidden' id='extensiones' value="<?php echo $wextensiones; ?>">
				<input type='hidden' id='extensiones_html' value="<?php echo $wextensiones; ?>">
				<input type='hidden' id='limite_max_archivo' value="<?php echo $maxFileSize; ?>">
				<input type='hidden' id='carpeta' value="<?php echo $wcarpeta_asociada; ?>">

				<input type='hidden' id='carpetaroot51' value="<?php echo $wcarpeta_root51; ?>">
				<input type='hidden' id='aplicacion' value="<?php echo $aplicacion; ?>">
				
				<?php
				if($carpeta != ''){
				
				?>
					<div id="uploader_div" align="center"></div><div align="center" ><img width="15" height="15" border="0" onclick="limpiarbusqueda();" title="Actualizar lista de publicaciones" style="cursor:pointer" src="../../images/medical/sgc/Refresh-128.png"></div>							
				<?php 
				
				}else{
				
				//Si el grupo en la url tiene datos, tiene extensiones definidas y el usuario puede subir archivos, se le mostraran los botones para la publicacion.
				if($grupo != ''){ 				
					if($wextensiones != ''){					
						if($wbuscar_usuario_habilitado !== false){						
							?>
							<div id="uploader_div" align="center"></div><div align="center" ><img width="15" height="15" border="0" onclick="limpiarbusqueda();" title="Actualizar lista de publicaciones" style="cursor:pointer" src="../../images/medical/sgc/Refresh-128.png"></div>							
							<?php 
					
						}else{
						echo "<br><br><div align=center></div>";
					}					
					}else{
					echo "<br><br><div align=center>No hay extensiones definidas para la empresa y el grupo.</div>";
					}				
				}else{
					echo "<br><br><div align=center>No hay grupo definido en la url</div>";
					}
				}
				
				?>
			</td>
			
		</tr>
	</table>
	
	
	


	
	
	
	
	
	<script type="text/javascript">				
	

	//Actualiza el listado de publicaciones.
	function limpiarbusqueda(){


	 $.blockUI({ message:	'Cargando...',
							css: 	{
										width: 	'auto',
										height: 'auto'
									}
					 });
	
	location.reload();


	}
	
	
	var grupo = $('#grupo').val();
	var carpeta = $('#carpeta').val();	
	var usuario = $('#usuario').val();
	var extensiones = $('#extensiones').val();

	var carpetaroot51 = $('#carpetaroot51').val();	
	
	//Muestra el extracto seleccionado de la lista.
	function mostrarPDF( nombre_doc ){
		
		var codigo_solicitud ='';
		var grupo = $('#grupo').val();
		var wemp_pmla = $("#wemp_pmla").val();

		var wcarpetaroot51 = $('#carpetaroot51').val();	

		
		if (wcarpetaroot51 != ""){
			var dirdata = carpetaroot51+"/"+nombre_doc;
		}else{
			var dirdata = "../../"+grupo+"/publicaciones/"+nombre_doc;
		}

		var object = '<object type="application/pdf" data="'+dirdata+'#toolbar=1&amp;navpanes=0&amp;scrollbar=1" width="800" height="600">'
						+'<param name="src" value="'+dirdata+'#toolbar=1&amp;navpanes=0&amp;scrollbar=1" />'
						+'<p style="text-align:center; width: 60%;">'
							+'Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />'
							+'<a href="http://get.adobe.com/es/reader/" onclick="this.target=\'_blank\'">'
								+'<img src="../../images/medical/root/prohibido.gif" alt="Descargar Adobe Reader" width="32" height="32" style="border: none;" />'
							+'</a>'
						+'</p>'
					+'</object>';
					


		//console.log(object);
		
		
		
		
		var boton ="<br><input type='button' value='Cerrar PDF' onclick='cerrarPDF()' accion='cerrarPdf' style='display:none;' />";
		object    = boton + object;
		$("#div_contenedor_pdf").html(object);
		$("#div_contenedor_pdf").show();
		//Llevar la pantalla hasta el div para evitar que el usuario haga scroll
		var posicion = $('#div_contenedor_pdf').offset();
		ejeY         = posicion.top;

		$('html, body').animate({
			scrollTop: ejeY+'px',
			scrollLeft: '0px'
		},0);
	}	
	
	var array_extensiones = extensiones.split(','); 
	var limite_max_archivo = $("#limite_max_archivo").val();
	var aplicacion = $("#aplicacion").val();
	if (carpetaroot51 != ""){//11 de febrero 2019 , el directorio es una ruta especifica y puede no depender del grupo.
		var ruta =carpetaroot51;
		
	}else
	{

		if(grupo != ""){	
			var ruta = '../../'+grupo+'/publicaciones/';
		}else{
		
			var array_carpeta = carpeta.split(":");
			
			var ruta = array_carpeta[0];
		
		}
		
	}
	//http://www.advocaciavalneibarbosa.com.br/upload/?example=8
	//http://chineseintoronto.ca/js/ajaxuploader/documentation/options.html
	
	if (carpetaroot51 != ""){
		var grupoxaplicacion = grupo + "-" + aplicacion;
	}else{
		var grupoxaplicacion = grupo;
	}
	$('#uploader_div').ajaxupload({
		url:'upload.php?grupo='+grupoxaplicacion+'&wusuario='+usuario,//url:'upload.php?grupo='+grupo+'&wusuario='+usuario,
		remotePath: ruta,
		maxFileSize:''+limite_max_archivo+'',
		allowExt: array_extensiones,
		checkFileExists:true,
		editFilename:true,
		async: true,


		onSelect:function(files)
		{	
			len = files.length;
			if (len <= 5 ){
				//files.length
				//alert(files[0].name);//type , size
				//alert(files[0].name.length);
				if (files[0].name.length >= 60){
					//no encontre limite por longitud , puede ser por espacios incluidos
					//alert("El nombre ("+files[0].name+" ) es muy largo y no se publicará")
				}
				//$('input[type=file]')[0].files[0].name;
			}else{
				alert('Se seleccionaron ' + len + ' archivos ');
			}
			
		},
	
		success:function(file)
		{
			//alert('Archivo ' + file + ' publicado correctamente ');
			//sale una alerta por cada archivo .
		},

		finish:function(files, filesObj)
		{
			len = files.length;

			if (len == 1)
			{
				alert('Se publicó: '+files  ) ;
			}else
			{
				alert('Se publicaron ' + len +'  archivos '  ) ;
			}
			limpiarbusqueda();
		},
		

		 
		error:function(txt, obj)
		{
			alert(' Error: publicar_archivos.php  '+ txt+" obj "+obj);
			//SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data
			//puede ocurrir si se hace echo en el archivo upload.php
			//porque la respuesta de este archivo es un archivo JSON y al hacer echo(s), la respuesta 
			//no genera un JSON correcto.

		}		
			
			
			
	//		finish: function(fn){ location.reload() ; } //location.reload(); recarga la pagina al terminar la subida de archivos.
	});
	
	


	function moverarchivo(grupo, nombre_archivo , nombre_visualarchivo){
	//Modificacion 14 Febrero 2019 , Freddy Saenz
	//nombre_archivo y nombre_visualarchivo archivo pueden ser diferentes
	//cuando se hace codificacion del nombre nombre_archivo es el nombre que aparece en el disco duro , pero
	//nombre_visualarchivo es el que se le muestra el usuario en a pantalla
	
	
	  var r = confirm("Desea borrar el archivo "+nombre_visualarchivo+" ?");
	  if (r == true) {
		
	  } else {
		return;
	  }
  
		var wemp_pmla = $("#wemp_pmla").val();	
	
		$.post("publicar_archivos.php",
				{
					consultaAjax:       	'moverarchivo',
					wemp_pmla:				wemp_pmla,
					grupo:					grupo,
					nombre_archivo: 		nombre_archivo

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					
					}
					else
					{      
						alert(data_json.mensaje);
				
					}

			},
			"json"
		);
		
		
	    limpiarbusqueda();
		
	}
	
	
	$(document).ready( function () {
			
		var extensiones_html = $("#extensiones_html").val();
		
		$('.ax-main-title').html('Seleccionar archivos '+extensiones_html);
		
		$( "#accordion" ).accordion();
		
		var ultimoano = $("#ultimoano").val();
		
		$( "#"+ultimoano).trigger( "click" );
		
	});
	
	</script>

<?php
}

//Funciones

//12 de Febrero 2019 , se agrega la funcion encriptarnombrearchivo
//Al dejar el nombre del pdf con la cedula del afiliado , cualquiera puede leer su extracto
//facilmente (desde un navegador), el usuario afiliado es el unico que puede ver su pdf
// esto se hace ocultando el nombre del archivo a todos los demas .
// Igual funcion en descargar_extracto.pdf y publicar_archivos.pdf
function encriptarnombrearchivo ($nombre, $aplicacion) 
{
	$pospunto = strripos($nombre, ".");//ver si el nombre tiene la extension incluida.
	if ($pospunto === false){
		$subcad = $nombre;
	}else{
		$subcad = substr($nombre,0, $pospunto);
	}
	
	$len = strlen( $subcad );
	$maxlencad2 = 32 - $len;
	if ($maxlencad2 < 0){
		$maxlencad2 = 0;
	}
	//Modificacion 21 de Febrero 2019 , Freddy Saenz
	//$aplicacion , para diferenciar nombres iguales , para fondos diferentes.
	$res =  $subcad  . "-" . substr ( sha1(  md5 ( strrev ( $aplicacion . $subcad ) ) ) , 0 ,$maxlencad2 )  ;
	return $res;
}

function extensionarchivo ($nombre)
{
	$arrpartesnombre = explode(".", $nombre);
	$extension = "";
	if(count($arrpartesnombre) <= 1)//si no tiene al menos un punto , se supone que no tiene extension.
	{
		return "";
	}else{
		$extension =  strtolower( end( $arrpartesnombre) );
	}
	return $extension; 
	
}



function moverarchivo($wgrupo, $wnombre_archivo){

	global $conex;
	global $wusuario;
	
	$wfecha = date("Y-m-d");
	$whora  = date("H:i:s");
	
	$datamensaje = array('mensaje'=>'', 'error'=>0);
	
	$nombre_fichero = "".$wgrupo."/".$wnombre_archivo.""; //Carpeta de publicaciones
	$nombre_fichero_copia = "".$wgrupo."/copia/".$wnombre_archivo.""; //Archivo dentro de la carpeta de copia de publicaciones.
	$carpeta_copia = "".$wgrupo."/copia/"; //Carpeta de copia de las publicaciones
	
	//Si la carpera de copia no existe la crea.
	if (!file_exists($carpeta_copia)) {
		mkdir($carpeta_copia,0777);
	}
	
	$vmensajedel = "";
	
	
	//Si el archivo ya fue movido, elimina el anterior y mueve el archivo de nuevo a la carpeta.
	if (file_exists($nombre_fichero_copia)) {//ya existe la copia
		
		//Elimino el archivo
		unlink($nombre_fichero_copia);
		
		//Muevo el archivo
		rename("".$wgrupo."/".$wnombre_archivo."","".$wgrupo."/copia/".$wnombre_archivo."");
		
		//Actualizo el registro de movimiento de archivo y cambio el estado.
		$q = " UPDATE root_000102"
		    ."    SET Actest = 'off', Actfmo = '".$wfecha."', Acthmo = '".$whora."'"
		    ."  WHERE Actnom = '".$wnombre_archivo."'";
		$res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		
		$datamensaje['mensaje'] = 'El archivo ha sido eliminado';
		
		//$vmensajedel = $datamensaje['mensaje'] . " ";
		
	} else {	
	
		//Muevo el archivo
		rename("".$wgrupo."/".$wnombre_archivo."","".$wgrupo."/copia/".$wnombre_archivo."");	
		 
			//Actualizo el registro de movimiento de archivo y cambio el estado.
		 $q = " UPDATE root_000102 "
			 ."	   SET Actest = 'off', Actfmo = '".$wfecha."', Acthmo = '".$whora."',  Actumo = '".$wusuario."'"
			 ."  WHERE Actnom = '".$wnombre_archivo."'";
		 $res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		
		$datamensaje['mensaje'] = 'El archivo ha sido eliminado';
	
	}	
	
	echo $vmensajedel;//json_encode($datamensaje);
	
}
 
 
function leer_archivos_y_directorios($grupo, $nombre_grupo, $wbuscar_usuario_habilitado, $vaplicacion)
{
	
	global $conex;
	global $wordenxfecha;
	
	
	$q =  "   SELECT * 
		      FROM root_000102
		      WHERE Actgru = '".$nombre_grupo."'";
	if ($vaplicacion == ""){
		$q .= " AND Actest = 'on'";
	}
	
//Modificacion Freddy Saenz ,2 de julio 2019 , ordenar por fecha , para que los archivos aparezcan ordenados por año u ordenar por nombre para los del fondo de empleados.	
	//$wordenxfecha
	if ( ($wordenxfecha == 1 ) )// || ( $vaplicacion == "" ) ) //para copaso
	{
		$q .= "  ORDER BY Fecha_data , Actnom ";
	}else{//para el fondo de empleados
		$q .= "  ORDER BY Actnom ";		
	}
/*	
	if ( ($wordenxfecha == 0 ) || ( $vaplicacion != "" ) ){//{//para los fondos de empleados
		$q .= "  ORDER BY Actnom ";		
	}else{//para los demas usuarios , como COPASO.
		$q .= "  ORDER BY Fecha_data , Actnom ";		
	}
	*/
	
//	$q .= "  ORDER BY Actnom ";
	
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	$array_datos = array();
	while($row = mysql_fetch_array($res)){
		
		if ($vaplicacion != ""){
				$array_datos[$vaplicacion][] = $row['Actnom'];
			
		}else{
			if(!array_key_exists(date("Y",strtotime($row['Fecha_data'])), $array_datos)){
				
				$array_datos[date("Y",strtotime($row['Fecha_data']))][] = $row['Actnom'];
				
			}else{
				
				$array_datos[date("Y",strtotime($row['Fecha_data']))][] = $row['Actnom'];
			}
			
		}
		
	}
	
	// echo "<pre>";
	// print_r($array_datos);
	// echo "</pre>";
	
	
	 // comprobamos si lo que nos pasan es un directorio
    if (is_dir ($grupo))
    {
		echo "<div id='accordion' style='width:450px;'>";
		
		foreach($array_datos as $key => $value_docs){
		
		echo "<h3 id='".$key."'>"  . $key . "</h3>";
		echo " <table height='100px' width='450px'>";
			$numdocs  = 0;
			foreach($value_docs as $key1 => $value){
				$clave = $value;
				
				$grupo_completa = $grupo . '/' . $value;
				$vrenombrar = 0;		 
				if ($vaplicacion != ""){
					//Modificacion 21 Febrero 2019, Freddy Saenz . Algunos archivos son de la forma
					//cedula seguidos por una fecha. xxxx-20190101
					$nomsolocedula = $value;
					$arrparesnombre = explode("-", $value);
					if (count($arrparesnombre)>1){
						$nomsolocedula = $arrparesnombre[0];//despues hay que ponerle la extension.
					} 
					
					$nomEncriptado = encriptarnombrearchivo($nomsolocedula,$vaplicacion);//21 Febrero 2019 $value
					$extension = extensionarchivo($value);//No se modifica porque se necesita la ultima parte del nombre
					if (count($arrparesnombre)>1){
						$nomsolocedula .= "." . $extension;
					}
					
					$nomCompletoEncriptado =  $grupo . '/' . $nomEncriptado . "." . $extension;//".pdf" ;//pendiente quitar este .pdf
					
					if (!file_exists( $nomCompletoEncriptado)){
						if (file_exists($grupo_completa)){
								rename("$grupo_completa","$nomCompletoEncriptado");	
								$vrenombrar = 1;//se debe renombrar el documento en la base de datos
								
								$grupo_completa = $nomCompletoEncriptado;
								$clave =  $nomEncriptado . "." . $extension;
						}else{
							//echo " no existe encriptado pero existe completo ";
						}
					}else {
						if (file_exists($grupo_completa)){//los dos archivos existen , se debe mover el actual a la copia
							//y el otro se debe renombrar por el nombre encriptado.
							moverarchivo($grupo,$nomEncriptado . "." . $extension);
							
							rename("$grupo_completa","$nomCompletoEncriptado");
							$vrenombrar = 2;//se debe renombrar el documento en la base de datos
						}else {
							//echo "  existe encriptado pero no existe completo ";
						}
						
						$grupo_completa = $nomCompletoEncriptado;
						$clave =  $nomEncriptado . "." . $extension;

						
						
					}	
					
				
					if ($vrenombrar != 0){//renombrar en la base de datos
						$qn = "SELECT count(*) 
							  FROM root_000102 
							  WHERE Actgru = '" . $nombre_grupo . "' "
							. " AND Actnom = '" . $nomsolocedula . "' " ;
							//echo "el query " . $qn;
						$res_qn = mysql_query($qn,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qn." - ".mysql_error());	
						$numqn = mysql_num_rows($res_qn);
						
						
						$rowqn = mysql_fetch_array($res_qn);
 				 
						if ($rowqn[0] == 0){//no esta registrado en la base de datos
							//si no existe se debe renombrar el  actual					
							$qup = "UPDATE   root_000102 
									SET Actnom = '" . $nomsolocedula . "' "
									. " WHERE Actgru = '" . $nombre_grupo . "' "
									. " AND Actnom = '" . $value . "' " ;
									//echo " extension $extension ";
							
							
							$resup = mysql_query($qup,$conex) or die (mysql_errno().$qup." - ".mysql_error());

							
						}else{//ya existe ,hay que borrar uno de los dos.
							//$qup = " DELETE FROM   root_000102 
								//	. " WHERE Actgru = '" . $nombre_grupo . "' "
								//	. " AND Actnom = '" . $value . "' "
								//	. " AND Actnom <> '" . $nomsolocedula . "' " ;
							//$resup = mysql_query($qup,$conex) or die (mysql_errno().$qup." - ".mysql_error());
							
							
							$vrenombrar = 0;//no se debe renombrar ya existe en la bd
						}

						
					}
					


					
				}

				if (file_exists($grupo_completa)){  
						
						($class == "fila2" )? $class = "fila1" : $class = "fila2";
						echo "<tr class='".$class."'>";				
						
						if($wbuscar_usuario_habilitado !== false){
							//14 Febrero 2019 Freddy Saenz , se agrega un nuevo parametro al metodo de javascript moverarchivo
							// $value
							$vitemdoc  = "<td><img width='15' height='15' border='0' onclick='moverarchivo(\"".$grupo."\", \"".$clave;
							switch($vrenombrar){
								case 2:
									$vitemdoc = "";//no agregar nada, ya esta creado .
									//$vitemdoc  .= "\", \"".$nomsolocedula."\");' title='Eliminar' style='cursor:pointer' src='../../images/medical/root/borrar.png'></td>";

									break;
								case 1:
									$vitemdoc  .= "\", \"".$nomsolocedula."\");' title='Eliminar' style='cursor:pointer' src='../../images/medical/root/borrar.png'></td>";

									break;
									
								default :
									$vitemdoc  .= "\", \"".$value."\");' title='Eliminar' style='cursor:pointer' src='../../images/medical/root/borrar.png'></td>";
									break;
							}
							if ($vrenombrar != 0){
							}else{
								
							}
							echo $vitemdoc;
							//"<td><img width='15' height='15' border='0' onclick='moverarchivo(\"".$grupo."\", \"".$clave."\", \"".$value."\");' title='Eliminar' style='cursor:pointer' src='../../images/medical/root/borrar.png'></td>";
							
						}
						echo "<td>";
						
						$numdocs++;
						switch ($vrenombrar) {
							case 2://el nombre ya esta
								//echo '<a href="javascript:" onclick="mostrarPDF(\''.$clave.'\')">' . $nomsolocedula ." # $vrenombrar ".  '</a><br />';
							
								break;
							case 1:
								echo '<a href="javascript:" onclick="mostrarPDF(\''.$clave.'\')">' . $nomsolocedula .  '</a><br />';
								break;
							default:
								echo '<a href="javascript:" onclick="mostrarPDF(\''.$clave.'\')">' . $value . '</a><br />';
								break;
							
						}
						
						if ( ($numdocs%10) == 0)
						{
							//echo '<a href="javascript:" onclick="mostrarPDF(\''.$clave.'\')">' . '#[' . $numdocs . '] ' . $value . '</a><br />';
						}
						
						
						echo "</td>";
						echo "</tr>";
				}
			}
		echo " </table>";
		}
				
	echo "</div>";	

		echo "<input type='hidden' id='ultimoano' value='".$key."'>";
	}
	
}

if(isset($consultaAjax))
{
	
	switch($consultaAjax){
	
		case 'moverarchivo':  
					{
					echo moverarchivo($grupo, $nombre_archivo);
					}					
		break;

		default: break;
		
		}
	return;
}

if ($wcarpeta_root51 != ""){
	$nombre_grupo = $grupo . "-" . $aplicacion ;
}else{
	$nombre_grupo = $grupo;
	
}
echo "<br>";
echo "<br>";
echo "<br>";
echo "<table width=100%>";
	echo "<tr>";	
		echo "<td width=25% align=left valign=top>";
		
		//Si no hay grupo definido no permitira la subida de publicaciones y por lo tanto no se listarán, ademas si no hay extensiones definidas tampoco le dejara subir archivos.
		if($grupo != '' and $wextensiones != ''){
			//11 de FEBRERO 2019 , es necesario diferenciar cuando se va a una carpeta especifica o cuando es el grupo el que indica la ubicacion.
			if ($wcarpeta_root51 != ""){
				$directorio = leer_archivos_y_directorios($wcarpeta_root51, $nombre_grupo, $wbuscar_usuario_habilitado,$aplicacion); //carpeta especifica
				
			}else{
				$directorio = leer_archivos_y_directorios("../../$grupo/publicaciones", $nombre_grupo, $wbuscar_usuario_habilitado,$aplicacion); //grupo actual	
				
			}
		}
		echo "</td>";
		echo "<td width=75% valign=top>";
		
		//11 de FEBRERO 2019, el calculo de numero de documentos cambia debido a que no siempre es la misma carpeta.
		if ($wcarpeta_root51!=""){
			$total_documentos = count(glob($wcarpeta_root51."/{*.*}",GLOB_BRACE));
			
		}else{
			$total_documentos = count(glob("../../".$grupo."/publicaciones/{*.*}",GLOB_BRACE));
			
		}		
		//Si hay documentos en la carpeta del grupo y hay extensiones habilitadas, le mostrara el mensaje para ver los archivos.
		if($total_documentos > 0 and $wextensiones != ''){
			$linea = "<div id='div_contenedor_pdf'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=5>" ;
			$linea = $linea . "Seleccionar archivo a visualizar, <br>";
			$linea = $linea .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;se mostrará en esta área. <br>";
			$linea = $linea .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Archivos disponibles = [ $total_documentos ]</font></div>" ;
			echo $linea;
		}
		echo "</td>";
	echo "</tr>";
echo "</table>";
echo "</center>";

?>
	
</div>

<?php

echo "<center>
	<div><br><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";

?>
</BODY>
</html>
