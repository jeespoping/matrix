<html>
<head>
<title>Asociados sin foto y sin resgistros en talento humano</title>
</head>
<?php
include_once("conex.php");
session_start();

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");

//PROGRAMA				      :Este reporte muestra los asociados que posiblemente no tienen usuario en matrix y que no tiene foto en la carpeta de fotos de talhuma.                                                                
//AUTOR				          :Jonatan Lopez Aguirre.                                                                        
//FECHA CREACION			  :Enero 31 de 2014
//FECHA ULTIMA ACTUALIZACION  :Enero 31 de 2014   

include_once("root/comun.php");


$conex = obtenerConexionBD("matrix");
$wbasedato = aplicacion($conex, $wemp_pmla, "fondos" ); //Trae el nombre para el contro de la base de datos del fondos de empleados.
$wactualiz = '2014-02-04';

function aplicacion($conex, $wemp_pmla, $aplicacion){

	
	 $q =  " SELECT Detval "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$waplicacion = $row['Detval'];

	return $waplicacion;
}

function traer_talumas($conex, $aplicacion){

	
	$q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
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
}

function empresas(){

	
	global $conex;
	
	 $q =  " SELECT Empcod, Empdes "
		  ."   FROM root_000050";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$array_empresas = array();
	
	while($row = mysql_fetch_array($res))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Empcod'], $array_empresas))
        {
            $array_empresas[$row['Empcod']] = $row;
        }
		
    }
	
	return $array_empresas;
}

$q_fotos_ced =  "  SELECT Fotced "
				."   FROM ".$wbasedato."_000008";
$res_fotos_ced = mysql_query($q_fotos_ced,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_fotos_ced." - ".mysql_error());	
	
$array_fotos_cedulas = array();

while($row_fotos_cedulas = mysql_fetch_array($res_fotos_ced))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_fotos_cedulas['cedula'], $array_fotos_cedulas))
        {
            $array_fotos_cedulas[$row_fotos_cedulas['cedula']] = $row_fotos_cedulas;
        }
		
    }

	// echo "<pre>";
	// print_r($array_fotos_cedulas);
	// echo "<pre>";

$path="../../images/medical/tal_huma/";
$directorio=dir($path);

$wfecha=date("Y-m-d");
$whora =(string)date("H:i:s");

//echo "Directorio ".$path.":<br><br>";
while ($archivo = $directorio->read()){

	$array_cedula = explode(".",$archivo);	
   // echo $array_cedula[0]."<br>";
   
	if(!$array_fotos_cedulas[$array_cedula[0]] and is_numeric($array_cedula[0])){
	$q = " INSERT INTO ".$wbasedato."_000008 (      Medico   , Fecha_data   ,   Hora_data  ,       Fotced          , Fotest,  Seguridad  ) "
			."  VALUES 						('".$wbasedato."', '".$wfecha."',  '".$whora."', '".$array_cedula[0]."',  'on' ,   'C-".$wbasedato."')";	
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	}	

}

$directorio->close();


$wtalhumas = traer_talumas($conex, "informacion_empleados"); //Trae todas las empresas que tengan tablas de talento humano.
//Busco todos los asociados inscritos como principales, teniendo en cuenta las tablas de talhuma, la variable $wtalhumas se encuentra declarada como global en la parte superior del codigo.
foreach($wtalhumas as $key => $value){

$query_todos[]= "     SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Ideest
					    FROM ".$value['Detval']."
				       WHERE Ideest = 'on'";

}
//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
$query_todos = implode(" UNION ", $query_todos);

//Ejecuto la consulta final construida de forma dinamica.
$res_todos = mysql_query($query_todos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_todos." - ".mysql_error());
$array_todos = array();
while($row_todos = mysql_fetch_assoc($res_todos))
    {
       
	   //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_todos['Ideced'], $array_todos))
        {
            $array_todos[$row_todos['Ideced']] = $row_todos;
        }
		
    }
	
	
//Todos aux
foreach($wtalhumas as $key => $value){

$query_todos_aux[]= " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Ideest
					    FROM ".$value['Detval']."";

}
//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
$query_todos_aux = implode(" UNION ", $query_todos_aux);

//Ejecuto la consulta final construida de forma dinamica.
$res_todos = mysql_query($query_todos_aux,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_todos_aux." - ".mysql_error());
$array_todos_aux = array();
while($row_todos = mysql_fetch_assoc($res_todos))
    {
       
	   //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_todos['Ideced'], $array_todos_aux))
        {
            $array_todos_aux[$row_todos['Ideced']] = $row_todos;
        }
		
    }
	
// echo "<pre>";
// print_r($array_todos_aux);
// echo "<pre>";

$query_asociados = "  SELECT *
					    FROM ".$wbasedato."_000006 order by asoemp";
$res_asociados = mysql_query($query_asociados,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_asociados." - ".mysql_error());

$array_asociados = array();

while($row_asociados = mysql_fetch_assoc($res_asociados))
    {
       
	   //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_asociados['Asoced'], $array_asociados))
        {
            $array_asociados[$row_asociados['Asoced']] = $row_asociados;
        }
		
    }	
	
	
//echo count($array_asociados);
// echo "<pre>";
// print_r($array_asociados);
// echo "<pre>";

// echo "<pre>";
// print_r($array_todos);
// echo "<pre>";
$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wlogoempresa = strtolower( $institucion->baseDeDatos );
encabezado("Asociados sin usuario y sin foto en matrix",$wactualiz, $wlogoempresa);

$wempresas = empresas();
$array_sin_usuario = array_diff_key($array_asociados, $array_todos);


// echo "<pre>";
// print_r($array_sin_usuario);
// echo "<pre>";



echo "<br><br><br>";
echo "<center>";
echo "<table>";
echo "<tr class=encabezadotabla><td align=center colspan=4>".count($array_sin_usuario)." asociados sin usuario en matrix (posiblemente)</td></tr>";
echo "<tr class=encabezadotabla>";
echo "<td align=center>Cedula</td>";
echo "<td align=center>Nombre</td>";
echo "<td align=center>Empresa</td>";
echo "<td align=center>Estado en <br>talento humano</td>";
echo "</tr>";

$class='fila1';				
foreach($array_sin_usuario as $key_sin_usu => $value_sin_usu){

($class == "fila2" )? $class = "fila1" : $class = "fila2";

$westado = $array_todos_aux[$key_sin_usu]['Ideest'];

//Estado del usuario en talento humano.
$westado = ($westado == 'off') ? "<font color=red>Inactivo</>" : "Sin datos";

echo "<tr class=".$class.">";
echo "<td>".$key_sin_usu."</td> ";  //Cedula
echo "<td>".$value_sin_usu['Asonom']."</td>"; //Nombre desde la tabla de asociados.
echo "<td>".$wempresas[$value_sin_usu['Asoemp']]['Empdes']."</td>"; //Empresa desde la tabla de asociados.
echo "<td align=center>".$westado."</td>"; //Estado en talento humano.

}

echo "</tr>";
echo "</table>";
echo "</center>";

// echo "<pre>";
// print_r($array_sin_usuario);
// echo "<pre>";

//////////////////

$query_sin_foto = "  SELECT Asoced FROM ".$wbasedato."_000006 
					  WHERE NOT(Asoced in 
							   (SELECT Fotced FROM ".$wbasedato."_000008)) 
							   ORDER BY Asoemp;";
$res_sin_foto = mysql_query($query_sin_foto,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_sin_foto." - ".mysql_error());

$array_sin_foto = array();

while($row_sin_foto = mysql_fetch_assoc($res_sin_foto))
    {
       
	   //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_sin_foto['Asoced'], $array_sin_foto))
        {
            $array_sin_foto[$row_sin_foto['Asoced']] = $row_sin_foto;
        }
		
    }

echo "<br><br>";
echo "<center>";
echo "<table>";
echo "<tr class=encabezadotabla><td align=center colspan=3>".count($array_sin_foto)." Asociados sin foto en matrix</td></tr>";
echo "<tr class=encabezadotabla>";
echo "<td align=center>Cedula</td>";
echo "<td align=center>Nombre</td>";
echo "<td align=center>Empresa</td>";
echo "</tr>";

$class='fila1';				
foreach($array_sin_foto as $key_sin_foto => $value_sin_foto){

	($class == "fila2" )? $class = "fila1" : $class = "fila2";

	$wnombre = trim($array_todos[$key_sin_foto]['Ideno1']." ".$array_todos[$key_sin_foto]['Ideno2']." ".$array_todos[$key_sin_foto]['Ideap1']." ".$array_todos[$key_sin_foto]['Ideap2']);
	
	$wnombre = ($wnombre == '') ? $array_asociados[$key_sin_foto]['Asonom'] : $wnombre;
	
echo "<tr class=".$class.">";
echo "<td>".$key_sin_foto."</td> ";
echo "<td>".$wnombre."</td>";
echo "<td>".$wempresas[$array_asociados[$key_sin_foto]['Asoemp']]['Empdes']."</td>";

}

echo "</tr>";
echo "</table>";
echo "</center>";

echo "<br><br>";
echo "<center>
	<div><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";

echo "<br><br>";


?>