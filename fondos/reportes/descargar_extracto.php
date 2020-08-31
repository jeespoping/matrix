<html>
<script type="text/javascript">

    // Abre el archivo PDF
  function abrir(docdpf)
	{
		location.href = docdpf;
		
	}
 
</script>

<?php
/**
 PROGRAMA                   : descargar_extracto.php
 AUTOR                      : Freddy Saenz .
 FECHA CREACION             : 25 Enero de 2019

 DESCRIPCION:
  Permite descargar o visualizar el pdf del extracto del fondo(mutuo o de empleados) seleccionado correspondiente al usuario actual 
  
  Pasos:
  Buscar la cedula basado en el codigo del empleado , aqui es necesario buscar por todas las unidades (empresas)  afiliadas
   a los fondos (talumas)
  Buscar el pdf del extracto en la carpeta del fondo(Mutuo o de Empleados ) correspondiente
  Mostrar el pdf
  
 ACTUALIZACIONES:
	Modificacion : 21 de Febrero 2019, Freddy Saenz, a la funcion encriptarnombrearchivo , se le agrega el parametro
	 aplicacion para que dos archivos del mismo afiliado(cedula) tengan nombres diferentes .
	Modificacion : 25 de Febrero 2019 , se agrega la busqueda codigo-codigo de empresa ($key2."-".$empresa_tabla_usuarios, ej 12345-01)
	 en la busqueda por codigo de usuario para obtener la cedula del afiliado al fondo .
	Modificación : 20 de Agosto de 2019, Jessica Madrid M - Se agrega la función empresaEmpleado() utilizada en talhuma para obtener la 
	 empresa a la que pertenece el usuario y se modifica la función buscarCedulaEmpleadoXCodigo() para que realice la búsqueda en la tabla
	 de talhuma que le corresponda de acuerdo a la empresa, si no encuentra al usuario en dicha tabla lo busca en talhuma_000013 con el 
	 código de empresa que le corresponda de esta forma se soluciona que muestre la cédula de otro usuario.
 **/

include_once("conex.php");
include_once("root/comun.php");
$MSJ_ERROR_ARCHIVO_NO_EXISTE = "ERROR EL ARCHIVO NO EXISTE";

$wactualiz    = "2019-08-20";

function traer_talumas($conex, $aplicacion)
{
	global $conex;

//Busca todas las tablas asociadas a talento humano .

//Si $aplicacion = "informacion_empleados"  en la tabla root_000051,son las diferentes
//tablas de talento humano que existen (talumas).

	$qSql =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($qSql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qSql." - ".mysql_error());

	$array_talhumas = array();

	while($row = mysql_fetch_array($res))
	{
		//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row['Detemp'], $array_talhumas))
		{
			$array_talhumas[$row['Detemp']] = $row;
		}
	}
	return $array_talhumas;
}//function traer_talumas


function fDirectorio_fondo($wemp_pmla,$aplicacion){
	global $conex;
	$querySql =  " SELECT Detdes, Detemp  "
			   ."   FROM root_000051"
			   ."  WHERE Detapl = 'CarpetaDePublicacion'"//ejemplo CarpetaDePublicacion
			   ."	 AND Detemp = '".$wemp_pmla."'"//01
			   ."	 AND Detval = '".$aplicacion."'";//fondo_mutuo

	    //ej. Detemp = 01 , Detapl = CarpetaDePublicacion , Detval = fondo_mutuo y Desdes = ../../fondos/publicaciones/fondo_mutuo
		//OJO , aqui se usa Desdes para el valor y no Detval como se hace normalmente .
		//en root_000021 se crea un registro de la forma:
		//Descripcion : Cargar Extractos , Programa = publicar_archivos.php?wemp_pmla=01&grupo=fondos&aplicacion=fondo_mutuo

			   
	$res = mysql_query($querySql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$querySql." - ".mysql_error());
	$num = mysql_num_rows($res);
	$dir = "";
	if ($num>0)
	{
		$rows = mysql_fetch_array($res);
		$dir = $rows['Detdes'];
	}
	return $dir;
		   
	
}

function empresaEmpleado($conex, $cod_use_emp)
{
	$arrayUsuario = array();
    $use_emp = '';
    $empresa = '';

    $user_session = explode('-',$cod_use_emp);
    $user_session = (count($user_session) > 1) ? $user_session[1] : $user_session[0];

    $q = "  SELECT  Codigo, Empresa
            FROM    usuarios
            WHERE   codigo = '".$user_session."'
                    AND Activo = 'A'";
    $res = mysql_query($q,$conex);
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

        $use_emp = $user_session.'-'.$row['Empresa']; // concatena los últimos 5 digitos del código del usuario con el código de la empresa a la que pertenece.
        $empresa = $row['Empresa'];
    }
	
	$arrayUsuario['usuario'] = $use_emp;
	$arrayUsuario['empresa'] = $empresa;
	
    return $arrayUsuario;
}

function buscarCedulaEmpleadoXCodigo($wemp_pmla , $codigo  )
{
	global $conex;
	
	$user_session = explode('-',$codigo);
	$wuse = $user_session[1];
	
	$arrayCodUsuario = empresaEmpleado($conex, $codigo);

	$wtalhumas = traer_talumas($conex, "informacion_empleados"); //Trae todas las empresas que tengan tablas de talento humano.

	$cc = "";
	foreach($wtalhumas as $key_a => $value)
	{
		if($value['Detemp'] == $arrayCodUsuario['empresa'])
		{
			$querySql = "SELECT Ideced 
						   FROM ".$value['Detval']."
						  WHERE (Ideuse = '".$arrayCodUsuario['usuario']."'
							 OR  Ideuse = '".$wuse."')						  
						    AND Ideest = 'on'  
							AND Ideced != '';";
			$res = mysql_query($querySql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$querySql." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			if ($num>0)
			{
				$rows = mysql_fetch_array($res);
				$cc = $rows['Ideced'];
				break;
			}				
		}
	}

	if($cc=="")
	{
		$querySql = "SELECT Ideced 
					   FROM ".$wtalhumas[$wemp_pmla]['Detval']."
					  WHERE (Ideuse = '".$arrayCodUsuario['usuario']."'  
					     OR  Ideuse = '".$wuse."')
						AND Ideest = 'on'  
						AND Ideced != '';";
		$res = mysql_query($querySql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$querySql." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		if ($num>0)
		{
			$rows = mysql_fetch_array($res);
			$cc = $rows['Ideced'];
		}	
	}

	return $cc;
}

// function buscarCedulaEmpleadoXCodigo($wemp_pmla , $codigo  )
// {
	// global $conex;

	// $wusuario = substr($codigo,2,7);
	// $key = substr($codigo, 2, strlen($codigo)); //se eliminan los dos primeros digitos

	// if(is_numeric($key))
	// {
		// if(strlen($key) == 7 AND "'".substr($key, 2)."'" !== "'".$wemp_pmla."'")
		// {

			// $wemp_pmla1=(substr($key, 0,2)); //el wemp_pmla son los dos primeros digitos
			// $key2 = substr( $key, -5 );
		// }
		// else
		// {
			// $wemp_pmla1=$wemp_pmla;
			// $key2 = substr( $key, -5 );
		// }

	// }
	// else
	// {
		// $key2=$key;
		// $wemp_pmla1=$wemp_pmla;
	// }


	
	
	
	
	// //Buscar en la tabla de usuario que cco le pertenece al usuario.
	// $q_user = "SELECT Descripcion, Empresa
				 // FROM usuarios
				// WHERE Codigo = '".$wusuario."'			
				  // AND Activo = 'A'";
	// $res_user = mysql_query($q_user, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_user . " - " . mysql_error());
	// $row_user = mysql_fetch_array($res_user);

	// $empresa_tabla_usuarios = $row_user['Empresa'];

// //con el codigo buscar la cedula del empleado
// //Pero como hay varias empresas asociadas el fondo de empleados , se debe buscar en cada
// //una de las tablas asociadas a talento humano en estas empresas.
	// $wtalhumas = traer_talumas($conex, "informacion_empleados"); //Trae todas las empresas que tengan tablas de talento humano.

	// foreach($wtalhumas as $key_a => $value)
	// {
	// //Consulto la informacion del usuario por el codigo en cada una de las tablas de talento humano
	// //algunas tablas son :   thidc_000013 , latalhum_000013 , talhuma_000013 ,thvlaser_000013 ,thfondo_000013, etc
		// $warrquerys[] =  " SELECT Ideced  "
						// ." FROM ".$value['Detval'].""
	
						// ."	 WHERE (Ideuse = '".$key2."-".$wemp_pmla1."'"
						// ."     OR Ideuse = '".$key."' OR Ideuse = '".$key2."-".$empresa_tabla_usuarios."')"
							
						
						// ."  AND Ideest = 'on'"
						// ."  AND Ideced != ''  ";

	// }


	// //Con los querys asociados a las tablas de talento humano , se genera una UNION , para buscar en el codigo
	// //del empleado en todas la tablas de talento humano
	// $querySql = implode(" UNION ", $warrquerys);
	
	// $res = mysql_query($querySql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$querySql." - ".mysql_error());
	// $num = mysql_num_rows($res);
	// $cc = "";
	// if ($num>0)
	// {
		// $rows = mysql_fetch_array($res);
		// $cc = $rows['Ideced'];
	// }
	// return $cc;
	
	
// }//buscarCedulaEmpleadoXCodigo

//12 de Febrero 2019 , se agrega la funcion ocultarnombre
//Al dejar el nombre del pdf con la cedula del afiliado , cualquiera puede leer su extracto
//facilmente (desde un navegador), el usuario afiliado es el unico que puede ver su pdf
// esto se hace ocultando el nombre del archivo a todos los demas .
// Igual funcion en descargar_extracto.pdf y publicar_archivos.pdf
function encriptarnombrearchivo ($nombre,$aplicacion) 
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
	//Muestra el acta seleccionada en la lista.


@session_start();

if(!$_SESSION['user'])
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."user");
}

if( !isset($wemp_pmla) )
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$fondo="";//pude ser fondo_mutuo  o fondo_empleados 
//&aplicacion=fondo_mutuo o &aplicacion=fondo_empleados
if(!isset($aplicacion))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."--aplicacion");
}else{
	$fondo=$aplicacion;//$_SESSION['aplicacion'];
}

$conex = obtenerConexionBD("matrix");
$wusuario = substr($user,2,7);


//Buscar la cedula usando el codigo del usuario
$cedula = buscarCedulaEmpleadoXCodigo($wemp_pmla,$user);//$wusuario

if ($cedula == ""){
?>
	<script type="text/javascript"> alert(<?php echo '"' . "cedula no encontrada" . '"'; ?>);
<?php
}

//Ubicación del pdf del extracto

//Buscar el directorio donde esta el extracto
$dir = fDirectorio_fondo($wemp_pmla,$aplicacion);
//y seleccionar el documento (cedula del usuario)
$cedula = encriptarnombrearchivo($cedula.".pdf", $aplicacion);

$archivo_dir = $dir."/".$cedula.".pdf";//

//echo " el archivo es $archivo_dir ";

//Mostrar el pdf
if(file_exists($archivo_dir))
{

 ?>
<script type="text/javascript"> abrir(<?php echo '"'.$archivo_dir.'"'; ?>);
//las comillas son necesarias , en caso contrario no abre el documento .
 </script>  

<?php

}else
{
	echo "<center>
	<div><br><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";

	terminarEjecucion($MSJ_ERROR_ARCHIVO_NO_EXISTE."cedula");	
}


?>
