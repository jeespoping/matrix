<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	solimp.ph
 * Fecha		:	2013-08-12
 * Por			:	Camilo Zapata, Frederick Aguirre
 * Descripcion	:	este programa tiene como propósito contribuir a la centralización de las impresiones de los formularios de hce, mediante la realización de solcitudes de impresión.
 					Para realizar una solicitud se tienen encuenta aspectos como:
 					- La modalidad: Tipo de solicitud, facturación, hospitalaria, registros médicos, etc.
 					- estado de la estancia del paciente: activo o egresado.
 					Adicionalmente permite generar pdf directamente para ser impresos desde la terminal propia en caso de que un centro de costos los solicite previamente y el envío
 					via correo electrónico de pdf en las modalidades que se configuren para funcionar de esta manera.
 * Condiciones  :   1. El usuario debe estar registrado previamente y poseer un rol asignado dentro de la configuración hce.
 					2. las solicitudes masivas solo están permitidas para la modalidad de facturación.
 *********************************************************************************************************

 Actualizaciones:
	2019-10-09: Jessica Madrid, - Se modifica el texto Edad por Edad actual en la información demográfica.
	2019-08-13: Jessica Madrid, - Se agrega el include a funcionesHCE.php con la función calcularEdadPaciente() y se reemplaza en el 
								  script el cálculo de la edad del paciente por dicha función, ya que el cálculo se realizaba con 360 
								  días, es decir, no se tenían en cuenta los meses de 31 días y para los pacientes neonatos este dato 
								  es fundamental.
	2017-01-25: Jessica Madrid, - Se agrega la impresion de reportes configurados como programas anexos en hce_000023 y hce_000024, teniendo en cuenta que
								  primero se debe validar que dicho reporte aplique para el paciente con esa historia e ingreso. Se hace una consulta Ajax
								  a cada reporte que retorna un html con el que se forma una cadena que es enviada a la funcion construirPDF en (HCE_print_function.php)
								- Tambien se permite configurar programas anexos como paquetes.  
								- En la funcion generarPdfModalidadPaquetes se modifica el nombre del pdf para que no tenga en cuenta el codigo del paquete 
								  (como una solicitud de formularios) ya que al mostrarlo solo busca por el identificador y dicho pdf no existe.
 	2016-06-21: Camilo zapata, se permite que se permita imprimir los formularios de aquellos pacientes que tienen alta definitiva menor o igual a 6 horas.
    2015-08-06:	Camilo zapata,se modifica el software para que notifique al usuario cuando el paciente no tiene formularios firmados. evitando
    			tambien ejecutar querys que fallen por esta razón
    2015-07-22:	Jonatan Lopez, se cambia la palabra quemada movhos de la funcion consultarRol por la variable {$wmovhos}.
	2015-01-14: Frederick Aguirre, se cambia la condicion detvim=on por detvim IN ('A','I')
	2014-11-27: Frederick Aguirre, se pone el select de especialidades para imprimir los formularios que hayan firmado los medicos de dicha especialidad
	2014-11-26: Frederick Aguirre, se quita el subquery en la funcion que muestra el arbol de formularios, se pone aparte y el resultado se usa en un IN
	2014-10-02: Frederick Aguirre, Se pone la condición detvim=on en el query para generar formularios
	2014-09-16: Frederick Aguirre, Se quita la condicion detest=on en el query para generar formularios
 	2014-05-15: Camilo Zapata,     sé agregó en el encabezado de los pdf la fecha de atención, solo para el pdf resultante de la consulta de un solo dia.
	2014-05-07: Frederick Aguirre, Se adiciona el campo Detccu en el query de la variable QueryI que se envia al a funcion contruirPDF
	2014-04-14: Frederick Aguirre, Se cambia el programa para que en caso de que le sea enviado el $wservicio lo implemente en la construcción del árbol de formularios
	2014-01-21: Frederick Aguirre, No anular si ya esta impreso/descargado, mejoras en el panel de consultas, uso de Modsfo para salto de pagina.
	2014-01-13: Frederick Aguirre, para que aparezcan los formularios externos de HCE en la lista de paquetes que los contengan.
 	2013-12-31: Camilo Zapata, se agregó un parámetro de la 51 que habilita la busqueda por cédula en la modalidad de pacientes activos.
	2013-12-10: Frederick Aguirre, Se cambia el query para consultar solicitudes realizadas porque estaba ralentizando el programa.
	2013-12-01: Frederick Aguirre, Al cerrar el pdf, se cambia para que no cierre todo el documento y permita generar otra solicitud.

 **********************************************************************************************************/
if( !isset($_SESSION['user']) && isset($peticionAjax) )//session muerta en una petición ajax
{
  if( $tipoPeticion == "json" ){
    $data = array( 'error'=>"error" );
    echo json_encode($data);
    return;
  }
    echo 'error';
    return;
}

$wactualiz = "2019-10-09";

if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
$pos                   = strpos($user,"-");
$wuser                 = substr($user,$pos+1,strlen($user));
$LIMITE_DE_FORMULARIOS_A_IMPRIMIR = 150;

if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";
	echo "<title>Solicitud impresion</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo ' <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/toJson.js" type="text/javascript"></script>';
	echo "<link type='text/css' href='../../hce/procesos/HCE.css' rel='stylesheet'> ";
}
require_once("conex.php");
include_once("hce/HCE_print_function.php");
include_once("hce/funcionesHCE.php");


DEFINE( "ORIGEN","Solicitudes" );
DEFINE( "LOG","000007" );
$caracteres2               = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
$caracteres                = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$paquetesSolicitud         = array(); //arreglo para el manejo de paquetes en solicitudes que se van a editar
$formulariosIndependientes = array(); //arreglo para el manejo de formularios individuales en solicitudes que se van a editar

	function todoslosFormularios(){

		global $conex, $whcebasedato, $wcenimp;
		$formularios = array();
		$query = " SELECT Preurl, Predes"
				 ."  FROM ".$whcebasedato."_000009 "
				 ." WHERE prenod = 'off' "
				 ."   AND preest = 'on' "
				 ." UNION "
				 ."SELECT Fexcod, Fexdes
				     FROM ".$wcenimp."_000009
				    WHERE Fexest = 'on'";
		$rs = mysql_query( $query, $conex );
		while( $row = mysql_fetch_array( $rs ) ){
			$row['Preurl'] = str_replace( "F=", "", $row['Preurl']);
			$formularios[trim($row['Preurl'])] = trim($row['Predes']);
		}

		return( $formularios );
	}

	function insertLog( $conex, $wcenimp, $user_session, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error = "", $wmodalidad ){
		$descripcion = str_replace("'",'"',$descripcion);
		$sql_error = ereg_replace('([ ]+)',' ',$sql_error);

		$insert = " INSERT INTO ".$wcenimp."_".LOG."
						(Medico, Fecha_data, Hora_data, logori, Logcdu, Logmod, Logacc, Logtab, Logerr, Logsqe, Logdes, Loguse, Logest, Seguridad)
					VALUES
						('".$wcenimp."','".date("Y-m-d")."','".date("H:i:s")."', '".ORIGEN."', '".utf8_decode($identificacion)."', '".$wmodalidad."', '".utf8_decode($accion)."','".$tabla."','".$err."', '".$sql_error."','".$descripcion."','".$user_session."','on','C-".$user_session."')";

		$res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());
	}

	//** FUNCIONES DE MANEJO DE FORMULARIOS  PARA PACIENTES**//
	function existenFormulariosPaciente($whis, $wing){

		global $conex;
        global $wcenimp;
		global $whcebasedato, $wmovhos;
		global $wemp_pmla;
		global $wsoloActivos;
		$formulariosPaciente = array();

		$query= "  SELECT Firpro "
			." 	     FROM ".$whcebasedato."_000036 "
			."      WHERE Firhis = '".$whis."' "
			."        AND Firing = '".$wing."'"
			."        AND Firfir = 'on' "
			."   GROUP BY Firpro ";

		$err = mysql_query($query,$conex) or die("aqui ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if( $num > 0 )
			$formulariosPaciente['formulariosDiligenciados'] = array();

		while( $row = mysql_fetch_array( $err ) ){
			array_push( $formulariosPaciente['formulariosDiligenciados'], $row['Firpro']);
		}

		/* se consulta si el usuario tiene formularios Externos */

		$query = "SELECT Fexcod, Fexgru, fextas, fexcas, fexadi
		            FROM {$wcenimp}_000009
		           WHERE fexest = 'on'";
		$rs    = mysql_query( $query, $conex );
		while( $row = mysql_fetch_array( $rs ) ){

			$tablas = $row['fextas'];
			$tablas = explode( ",", $tablas );
			$from   = "";

			foreach( $tablas as $i=>$tabla ){
				( $i == 0 ) ? $from = $row['Fexgru']."_".$tabla : $from .= ",".$row['Fexgru']."_".$tabla;
			}

			$where = str_replace("<HIS>", "'{$whis}'", $row['fexcas']);
			$where = str_replace("<ING>", "'{$wing}'", $where);

			( trim( $row['fexadi']) != "" ) ? $adicional = $row['fexadi'] : $adicional = "";


			$query2 = " SELECT count(*) total
						  FROM {$from}
						 WHERE {$where}
						 	   {$adicional}
						  LIMIT 1";
			$rs2   = mysql_query( $query2, $conex );
			while( $row2 = mysql_fetch_array( $rs2 ) ){
				if( $row2['total'] > 0 ){

					if( !isset( $formulariosPaciente['formulariosDiligenciados'] ) ){
						$formulariosPaciente['formulariosDiligenciados'] = array();
					}
					array_push( $formulariosPaciente['formulariosDiligenciados'], $row['Fexcod']);
				}
			}
		}

		return $formulariosPaciente;
	}

	function bi($d,$n,$k){
		$n--;
		if($n > 0)
		{
			$li=0;
			$ls=$n;
			while ($ls - $li > 1)
			{
				$lm=(integer)(($li + $ls) / 2);
				$val=strncmp(strtoupper($k),strtoupper($d[$lm]),20);
				if($val == 0)
					return $lm;
				elseif($val < 0)
						$ls=$lm;
					else
						$li=$lm;
			}
			if(strtoupper($k) == strtoupper($d[$li]))
				return $li;
			elseif(strtoupper($k) == strtoupper($d[$ls]))
						return $ls;
					else
						return -1;
		}
		else
			return -1;
	}

	function mostrarPaquetes( $wmodalidad, $wcco, $wgrupo ){

		global $conex,  $wcenimp, $whcebasedato, $wemp_pmla;
		global $paquetesSolicitud, $formulariosIndependientes;

		$color        ="tipoTI04";
		$color1       ="tipoTI06";
		$color2       ="tipoTI03";
		$data         =array();
		$htmlPaquetes = "";

		$query  = "select id,Paqdes,Paqfor,Paqrpa from ".$wcenimp."_000004 "
				."  where Paqmod = '".$wmodalidad."' "
				."    and ( Paqgru = '".$wgrupo."' or Paqgru = '%')"
				."    and ( Paqcco = '".$wcco."' or Paqcco = '%' )"
				."    and Paqest= 'on' "
				."  order by 1";
		$err = mysql_query($query,$conex) or die("aca ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$htmlPaquetes .= "<table border=0 align=center id='tabla_paquetes'>";
			$htmlPaquetes .= "<tr><td id=tipoTI01 colspan=2>PAQUETES DE IMPRESION HISTORIA CLINICA ELECTRONICA<td></tr>";
			$htmlPaquetes .= "<tr><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCRIPCION</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row         = mysql_fetch_array($err);
				$data[$i][0] = $row[0];
				$data[$i][1] = $row[2];
				
				// cambio
				if($row[2]!="")
				{
					$formas      = explode(",",$row[2]);
					$en          = "";

					for ($j=0;$j<count($formas);$j++)
					{
						if($j > 0)
							$en .= ",";
						$en .= "'".$formas[$j]."'";
					}
					$query  = "select Encpro,Encdes from ".$whcebasedato."_000001 "
							."  where Encpro in (".$en.") "
							."  order by 1";
	
					$err1 = mysql_query($query,$conex) or die("aca ".mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);

					( isset( $paquetesSolicitud[$row[0]] ) ) ? $checkPaquete = "checked" : $checkPaquete = "";
					$htmlPaquetes .= "<tr class='fila_paquete' style='cursor: pointer;'><td id=".$color."><input type='checkbox' {$checkPaquete} onclick='marcarHijosPaquete(this, \"".$row[0]."\")' value=".$row[0]." ></td><td onclick='mostrarHijos(\"".$row[0]."\")' id=".$color.">".$row[1]."</td></tr>";
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$w = $j + 1;
						( isset( $paquetesSolicitud[$row[0]][$row1[0]] ) ) ? $checkhijo = "checked" : $checkhijo = "";
						$htmlPaquetes .= "<tr class='trhijopaquete".$row[0]."' style='display:none;'><td id=".$color2.">".$w."<input type='checkbox' {$checkhijo} paquete='".$row[0]."' class='formulario_de_paquete hijopaquete".$row[0]."' value='".$row1[0]."' onclick='clickHijoPaquete(this)' /></td><td id=".$color1.">".$row1[1]."</td></tr>";
					}

					/*2014-01-13 Para mostrar los formularios externos en la lista de paquetes*/
					$forms_externos = array();
					$forms = explode(",",$en);
					foreach($formas as $formu ){
						if(preg_match('/EXT/i',$formu))
							array_push( $forms_externos, "'".$formu."'" );
					}

					if( count($forms_externos) > 0 ){
						$forms_externos = implode(",",$forms_externos);
						$q = " SELECT Fexcod as codigo,Fexdes as nombre, Fexurl as url
								 FROM {$wcenimp}_000009
								WHERE Fexcod IN (".$forms_externos.")
								  AND Fexest = 'on'
								ORDER BY 1 ";
						$resext = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$numext = mysql_num_rows($resext);

						if( $numext > 0 ){
							while( $rowext = mysql_fetch_array($resext) ){
								$w++;
								( isset( $paquetesSolicitud[$row[0]][$rowext[0]] ) ) ? $checkhijo = "checked" : $checkhijo = "";
								$htmlPaquetes .= "<tr class='trhijopaquete".$row[0]."' style='display:none;'><td id=".$color2.">".$w."<input type='checkbox' {$checkhijo} paquete='".$row[0]."' class='formulario_de_paquete hijopaquete".$row[0]."' value='".$rowext[0]."' onclick='clickHijoPaquete(this)' /></td><td id=".$color1.">".$rowext[1]."</td></tr>";

							}
						}
					}
					
					if($row[3]!="" && $row[3]!="NO APLICA")
					{
						$programasAnexos = explode(",",$row[3]);
						
						for($s=0;$s<count($programasAnexos);$s++)
						{
							$progAnexo = explode("-",$programasAnexos[$s]);
							
							$queryAnexos = "  SELECT Oprpro,Oprnop,Oprdop,Oprscr,Oprqva 
												FROM ".$whcebasedato."_000023,".$whcebasedato."_000024 
											   WHERE Oprpro='".$progAnexo[0]."' 
												 AND Oprnop='".$progAnexo[1]."'
												 AND Pronom=Oprpro 
												 AND Proest='on' 
												 AND Oprest='on' 
												 AND Oprscr!='' 
												 AND Oprqva!='' 
											ORDER BY Oproim,Oprpro,Oprnop;";
							
							$resAnexos = mysql_query($queryAnexos,$conex) or die (mysql_errno()." - en el query: ".$queryAnexos." - ".mysql_error());
							$numAnexos = mysql_num_rows($resAnexos);

							if( $numAnexos > 0 ){
								while( $rowAnexos = mysql_fetch_array($resAnexos) ){
									
									$urlScript = explode("?",$rowAnexos[3]);
									
									$w++;
									( isset( $paquetesSolicitud[$row[0]][$rowext[0]] ) ) ? $checkhijo = "checked" : $checkhijo = "";
									$htmlPaquetes .= "<tr class='trhijopaquete".$row[0]."' style='display:none;'><td id=".$color2.">".$w."<input type='checkbox' {$checkhijo} paquete='".$row[0]."' class='formulario_de_paquete hijopaquete".$row[0]."' value='".str_replace(" ", "_",$rowAnexos[2])."' onclick='clickHijoPaquete(this)' /></td><td id=".$color1.">".$rowAnexos[2]."</td></tr>";

								}
							}							
						}
					}
					/*2014-01-13 Para mostrar los formularios externos en la lista de paquetes*/
				}
				else
				{
					// solo reportes
					
					if($row[3]!="" && $row[3]!="NO APLICA")
					{
						( isset( $paquetesSolicitud[$row[0]] ) ) ? $checkPaquete = "checked" : $checkPaquete = "";
						$htmlPaquetes .= "<tr class='fila_paquete' style='cursor: pointer;'><td id=".$color."><input type='checkbox' {$checkPaquete} onclick='marcarHijosPaquete(this, \"".$row[0]."\")' value=".$row[0]." ></td><td onclick='mostrarHijos(\"".$row[0]."\")' id=".$color.">".$row[1]."</td></tr>";
						
						$programasAnexos = explode(",",$row[3]);
						
						for($s=0;$s<count($programasAnexos);$s++)
						{
							$progAnexo = explode("-",$programasAnexos[$s]);
							
							$queryAnexos = "  SELECT Oprpro,Oprnop,Oprdop,Oprscr,Oprqva 
												FROM ".$whcebasedato."_000023,".$whcebasedato."_000024 
											   WHERE Oprpro='".$progAnexo[0]."' 
												 AND Oprnop='".$progAnexo[1]."'
												 AND Pronom=Oprpro 
												 AND Proest='on' 
												 AND Oprest='on' 
												 AND Oprscr!='' 
												 AND Oprqva!='' 
											ORDER BY Oproim,Oprpro,Oprnop;";
							
							$resAnexos = mysql_query($queryAnexos,$conex) or die (mysql_errno()." - en el query: ".$queryAnexos." - ".mysql_error());
							$numAnexos = mysql_num_rows($resAnexos);

							if( $numAnexos > 0 ){
								while( $rowAnexos = mysql_fetch_array($resAnexos) ){
									
									$urlScript = explode("?",$rowAnexos[3]);
									
									$w=$s+1;
									( isset( $paquetesSolicitud[$row[0]][$rowext[0]] ) ) ? $checkhijo = "checked" : $checkhijo = "";
									$htmlPaquetes .= "<tr class='trhijopaquete".$row[0]."' style='display:none;'><td id=".$color2.">".$w."<input type='checkbox' {$checkhijo} paquete='".$row[0]."' class='formulario_de_paquete hijopaquete".$row[0]."' value='".str_replace(" ", "_",$rowAnexos[2])."' onclick='clickHijoPaquete(this)' /></td><td id=".$color1.">".$rowAnexos[2]."</td></tr>";
								}
							}							
						}
					}
				}
				

			}
			$htmlPaquetes .= "<tr><td id=tipoTI07 colspan=2><td></tr>";
			$htmlPaquetes .= "</table>";
		}else{
			$htmlPaquetes .= "<table border=0 align=center id='tabla_paquetes'>";
			$htmlPaquetes .= "<tr><td id='tipoTI01'>NO HAY PAQUETES DE IMPRESION PARA LA MODALIDAD Y CCO</td></tr>";
			$htmlPaquetes .= "</table>";
		}

		return( $htmlPaquetes );
	}

	function mostrarArbolImpresion($whis, $wing){

		global $conex;
        global $wcenimp;
		global $whcebasedato;
		global $wemp_pmla;
		global $wuser;
		global $formulariosIndependientes;
		global $wservicio;
		global $wmovhos;
		global $cadenaProgramasAnexos;
		
		$wtipodoc="";
		$wcedula="";

		/*Consultar tipo y numero de documento 2014-11-28*/
		$query  = " SELECT Pactid as tipo, Pacced as documento
					  FROM root_000037 as ori
					 INNER JOIN
						   root_000036 as pac on ( ori.Oriori='{$wemp_pmla}' AND pac.Pactid = ori.Oritid AND pac.Pacced = ori.Oriced AND ori.Orihis = '{$whis}' )";

		$rs = mysql_query( $query, $conex );
		if( $rs ){
			$num1 = mysql_num_rows($rs);
			if($num1 > 0){
				$row = mysql_fetch_assoc( $rs );
				$wtipodoc = $row['tipo'];
				$wcedula = $row['documento'];
			}
		}


		//$wservicio         ='*';
		$key               = $wuser;
		$htmlArbolCompleto = "";

		$htmlArbolCompleto .= "<div id='div_arbol_impresion'>";
		$htmlArbolCompleto .= "<table border=0 align=center id='tabla_arbol_formularios'>";
		$htmlArbolCompleto .= "<tr><td id=tipoTI01 colspan=4>FORMULARIOS A IMPRIMIR DE HISTORIA CLINICA ELECTRONICA</td></tr>";

		$htmlArbolCompleto .= "	<tr>
									<td id=tipoTI01 colspan=4>";
		$htmlArbolCompleto .=  "		<select name='wespecial' id='wespecial'>";
		$htmlArbolCompleto .=  "			<option>TODAS</option>";
		$query = "	 SELECT Espcod, Espnom
					   FROM ".$whcebasedato."_000036, ".$wmovhos."_000048, ".$wmovhos."_000044
					  WHERE Firhis = '".$whis."'
						AND Firing = '".$wing."'
						AND Firusu = Meduma
						AND Medesp = Espcod
				  GROUP BY 1,2 ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);

		if($num1 > 0){
			while( $row1 = mysql_fetch_array($err1) ){
				$htmlArbolCompleto .=  "	<option value='".$row1[0]."'>".$row1[1]."</option>";
			}
		}
		$htmlArbolCompleto .=  "		</select>
									</td>
								</tr>";

		$htmlArbolCompleto .= "<tr><td id=tipoTI05 colspan=4>Marcar Todos<input type='checkbox' name='all'  onclick='marcarTodos(this)'></td></tr>";

		if(!isset($wservicio) || $wservicio == '')
			$wservicio="*";
		$vistas=array();
		$numvistas=0;
		$query  = "  SELECT Rararb "
					." FROM ".$whcebasedato."_000020,".$whcebasedato."_000021,".$whcebasedato."_000009,".$whcebasedato."_000037 "
					."WHERE Usucod = '".$key."' "
					." 	AND Usurol = Rarcod "
					." 	AND Rararb = precod "
					."	AND precod = Forcod "
					."	AND Forser = '".$wservicio."' "
					."ORDER BY 1";

		$err = mysql_query($query,$conex) or die("aca ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$vistas[$i] = $row[0];
			}
		}
		$numvistas=$num;

		$dta=0;

		//2014-11-26
		$arr_firpro = array();
		$querysub = "  SELECT Firpro "
				." 	     FROM ".$whcebasedato."_000036 "
				."      WHERE Firhis = '".$whis."' "
				."        AND Firing = '".$wing."'"
				."        AND Firfir = 'on' "
				."   GROUP BY Firpro ";
		$err = mysql_query($querysub,$conex) or die("aqui ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			while( $row = mysql_fetch_array($err) ){
				array_push( $arr_firpro, "'".$row[0]."'" );
			}
		}

		$query = "    SELECT Precod,Preurl,Predes,prenod, '' as Encpro "
					."  FROM ".$whcebasedato."_000009 "
					." WHERE prenod = 'on' "
					."   AND preest = 'on' "
					."UNION ALL "
					."SELECT Precod,Preurl,Predes,prenod, Encpro "
					." 	FROM ".$whcebasedato."_000009,".$whcebasedato."_000020,".$whcebasedato."_000021,".$whcebasedato."_000037, ".$whcebasedato."_000001 "
					." WHERE prenod = 'off' "
					."   AND mid(Preurl,1,1) = 'F' "
					."   AND Preurl = CONCAT( 'F=', Encpro ) "
					."   AND preest = 'on' "
					."   AND Usucod = '".$key."' "
					."   AND Usurol = Rarcod "
					."   AND Rararb = precod "
					."	 AND precod = Forcod "
					."   AND Rarimp = 'on'"
					."	 AND Forser = '".$wservicio."' "
					."   AND Encpro IN (".implode(",",$arr_firpro).") order by 1";

		if( count( $arr_firpro ) > 0 ){//2015-08-06
			$err = mysql_query($query,$conex) or die("aqui ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
		}else{
			$num = 0;
		}

		include_once("hce/especial.php");

		if($num > 0)
		{
			$fil=ceil($num / 4);
			$data=array();
			$dta=1;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$data[$i][0]=$row[1];
				$data[$i][1]=$row[2];
				$data[$i][2]=$row[3];
				$pos=bi($vistas,$numvistas,$row[0]);
				if($pos != -1)
					$data[$i][3]=1;
				else
					$data[$i][3]=0;
				$data[$i][4]=$row[0];
				$data[$i][5]=$row[4];
			}
			for ($i=0;$i<$num;$i++)
			{
				$wb = $num - ($i + 1);
				$wbaux = $wb;
				if($data[$wb][2] == "on")
				{
					while($data[$wbaux][2] == "on" and $wbaux < ($num -1 ))
						$wbaux++;
					if(($wbaux < ($num - 1) and strpos($data[$wbaux][4],$data[$wb][4]) === false) or $wbaux == ($num - 1))
						$data[$wb][0] = "NO";
				}
			}
			$numFinal=-1;
			$dataaux=array();
			for ($i=0;$i<$num;$i++)
			{
				if($data[$i][0] != "NO")
				{
					$numFinal++;
					$dataaux[$numFinal][0]=$data[$i][0];
					$dataaux[$numFinal][1]=$data[$i][1];
					$dataaux[$numFinal][2]=$data[$i][2];
					$dataaux[$numFinal][3]=$data[$i][3];
					$dataaux[$numFinal][4]=$data[$i][4];
					$dataaux[$numFinal][5]=$data[$i][5];
				}
			}
			$fil=ceil(($numFinal+1) / 4);
			$data=array();
			for ($i=0;$i<=$numFinal;$i++)
			{
				$data[$i][0]=$dataaux[$i][0];
				$data[$i][1]=$dataaux[$i][1];
				$data[$i][2]=$dataaux[$i][2];
				$data[$i][3]=$dataaux[$i][3];
				$data[$i][4]=$dataaux[$i][4];
				$data[$i][5]=$dataaux[$i][5];
			}
		}

		$programasAnexos = consultarScripts($conex,$whcebasedato,$whis,$wing);
		
		if(count($programasAnexos)>0)
		{
			if(count($data)>0)
			{
				$dta = 1;
				$data = array_merge($data, $programasAnexos);
				$numFinal = $numFinal+count($programasAnexos);
				$fil=ceil(($numFinal+1) / 4);
			}
			else
			{
				$dta = 1;
				$data = $programasAnexos;
				$numFinal = $numFinal+count($programasAnexos);
				$fil=ceil(($numFinal+1) / 4);
			}
		}
		
		
		$debeLlenarCadenaProgAnex = false;
		if($cadenaProgramasAnexos=="")
		{
			$debeLlenarCadenaProgAnex = true;
		}
		
		if($dta == 1)
		{
			for ($i=0;$i<$fil;$i++)
			{
				$htmlArbolCompleto .= "<tr>";
				for ($j=0;$j<4;$j++)
				{
					$exp=$i+($fil*$j);
					if(isset($data[$exp][0]))
					{
						if($data[$exp][2] == "off")
						{
							//$color="tipoTI04";
							( is_int($j/2) ) ? $color="tipoTI06" : $color ="tipoTI04";
							if($data[$exp][3] == 1)
							{
								( isset( $formulariosIndependientes[$data[$exp][5]] ) ) ? $checkFormulario = "checked" : $checkFormulario = "";
								//$htmlArbolCompleto .= "<td id=".$color."><input class='formulario_arbol_impresion' {$checkFormulario} type='checkbox' value='".$data[$exp][5]."' name='imp[".$exp."]'></td>";
								//$htmlArbolCompleto .= "<td id=".$color.">".$data[$exp][1]."</td>";
								
								
								
								$progAnex = "";
								$valueCheckbox=$data[$exp][5];
								if(substr($data[$exp][0],0,2)!="F=")
								{
									$valueCheckbox=str_replace(" ","_",$data[$exp][1]);
									
									if($debeLlenarCadenaProgAnex)
									{
										$cadenaProgramasAnexos .= $data[$exp][0]."|"; //Jessica!!!!!!
									}
									
									$progAnex = "progAnex='".$data[$exp][0]."'";
								}
								
								$htmlArbolCompleto .= "<td id=".$color."><span style='float:left;'><input class='formulario_arbol_impresion' {$checkFormulario} type='checkbox' value='".$valueCheckbox."' name='imp[".$exp."]' ".$progAnex."></span>";
								$htmlArbolCompleto .= "".$data[$exp][1]."</td>";
							}
							else
							{
								//$htmlArbolCompleto .= "<td id=".$color."></td>";
								//$htmlArbolCompleto .= "<td id=".$color."></td>";
								$htmlArbolCompleto .= "<td id=".$color."></td>";
							}

						}
						else
						{
							//$color="tipoTI03";
							$color="tipoTI02";
							$class=" class='botona' ";
							//$htmlArbolCompleto .= "<td id=".$color."></td>";
							//$htmlArbolCompleto .= "<td id=".$color.">".$data[$exp][1]."</td>";
							$htmlArbolCompleto .= "<td {$class}>";
							$htmlArbolCompleto .= "".$data[$exp][1]."</td>";
						}
					}
					else
					{
						//$color="tipoTI04";
						( is_int($j/2) ) ? $color="tipoTI06" : $color ="tipoTI04";
						//$htmlArbolCompleto .= "<td id=".$color."></td>";
						//$htmlArbolCompleto .= "<td id=".$color."></td>";
						//$htmlArbolCompleto .= "<td id=".$color."></td>";
						$htmlArbolCompleto .= "<td id=".$color."></td>";
					}
				}
				$htmlArbolCompleto .= "</tr>";
			}
		}
		$htmlArbolCompleto .= "</table>";

		$htmlArbolCompleto .= "</div>";

		if( $num == 0 ){//2015-08-06
			$htmlArbolCompleto .= "<br /><br /><br /><br />
            <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' >
                [?] El usuario no tiene formularios firmados.
            </div>";
		}

		return( $htmlArbolCompleto );
	}

	//** final ---  FUNCIONES DE MANEJO DE FORMULARIOS PARA PACIENTES **//

	//**FORMULARIOS DE CONSULTA PARA LAS DIFERENTES MODALIDADES** PRESENTACION INICIAL**/
	function formularioConsultaPacientesEgresados(){

		$pantalla .= '<br><br>';
		//------------TABLA DE PARAMETROS-------------
		$pantalla .= '<table align="center">';
		$pantalla .= "<tr>";
			$pantalla .= '<td class="encabezadotabla" colspan="4" align="center">PAR&Aacute;METROS DE BUSQUEDA</td>';
		$pantalla .= "</tr>";
		$pantalla .= "<tr>";
			$pantalla .= '<td class="fila1" width="80px">Tipo de Documento</td>';
			$pantalla .= '<td class="fila2" width="auto">';
				$pantalla .= "<select type='text' id='tipdoc' >";
					$tiposDocumento = consultarTiposDocumento();
					$pantalla.= "<option value='' selected>--</option>";
					foreach ($tiposDocumento as $tipo){
						$pantalla .= "<option value='$tipo->codigo'>$tipo->descripcion</option>";
					}
				$pantalla .= "</select>";
		$pantalla .= "</td>";
		$pantalla .= '<td class="fila1" width="80px">N&uacute;mero Documento</td>';
		$pantalla .= '<td class="fila2" width="auto">';
			$pantalla .= "<input type='text' id='numdoc' class='solofloat' />";
		$pantalla .= "</td>";
		$pantalla .= "</tr>";

		$pantalla .= "<tr>";
			$pantalla .= '<td class="fila1" width="80px">Historia Cl&iacute;nica</td>';
			$pantalla .= '<td class="fila2" width="auto">';
				$pantalla .= "<input type='text' id='whis' />";
			$pantalla .= "</td>";
			$pantalla .= '<td class="fila1">Ingreso</td>';
			$pantalla .= '<td class="fila2" id="td_padre_ingreso">';
				$pantalla .= "<input type='text' id='wing' />";
			$pantalla .= "</td>";
		$pantalla .= "</tr>";
		$pantalla .= "<input type='hidden' id='wcco' name='wcco' value=''>";
		$pantalla .= "<input type='hidden' id='wSolicitaCenimp' name='wSolicitaCenimp' value=''>";

		$pantalla .= "</table>";
		$pantalla .= "<br><center><input type='button' buscaHistoria='on' onclick='consultarPacientes( this )' value='Consultar' id='btn_consultar'/></center>";
		//------------FIN TABLA DE PARAMETROS-------------

		return( $pantalla );
	}

	function formularioConsultaPacientesActivos(){

		global $buscaXcedulaActivos; //2013-12-31
		$pantalla = "<div align='center' id='formulario_consulta'>";
		$pantalla .= '<br><br>';
		//------------TABLA DE PARAMETROS-------------

		( $buscaXcedulaActivos == "si" ) ? $colspan  = "4" : $colspan  = "2"; //2013-12-31
		( $buscaXcedulaActivos == "si" ) ? $colspan2 = "1" : $colspan2 = "1"; //2013-12-31

		$pantalla .= "<table align='center'>";
		$pantalla .= "<tr>";
			$pantalla .= '<td class="encabezadotabla" colspan="'.$colspan.'">P&Aacute;RAMETROS DE BUSQUEDA</td>';
		$pantalla .= "</tr>";
		$pantalla .= "<tr>";
		if( $buscaXcedulaActivos == "si" ){
			$pantalla .= "<tr>";
			$pantalla .= '<td class="fila1" width="80px">Tipo de Documento</td>';
			$pantalla .= '<td class="fila2" width="auto">';
				$pantalla .= "<select type='text' id='tipdoc' >";
					$tiposDocumento = consultarTiposDocumento();
						$pantalla.= "<option value='' selected>--</option>";
						foreach ($tiposDocumento as $tipo){
							$pantalla .= "<option value='$tipo->codigo'>$tipo->descripcion</option>";
						}
					$pantalla .= "</select>";
			$pantalla .= "</td>";
			$pantalla .= "</tr><tr>";
			$pantalla .= '<td class="fila1"  width="80px">N&uacute;mero Documento</td>';
			$pantalla .= '<td class="fila2"  width="auto">';
				$pantalla .= "<input type='text' id='numdoc' class='solofloat' />";
			$pantalla .= "</td>";
			$pantalla .= "</tr>";
		}
			$pantalla .= '<td class="fila1" colspan="'.$colspan2.'" width="80px">Historia Cl&iacute;nica</td>';
			$pantalla .= '<td class="fila2" colspan="'.$colspan2.'" width="auto">';
				$pantalla .= "<input type='text' id='whis' />";
			$pantalla .= "</td>";
		$pantalla .= "</tr>";
		$pantalla .= "<tr>";
			$pantalla .= '<td class="fila1" colspan="'.$colspan2.'">Ingreso</td>';
			$pantalla .= '<td class="fila2" colspan="'.$colspan2.'" id="td_padre_ingreso">';
				$pantalla .= "<input type='text' id='wing' />";
			$pantalla .= "</td>";
		$pantalla .= "</tr>";
		$pantalla .= "<tr>";
			$pantalla .= "<td class='fila1' colspan='".$colspan2."'>PISO: </td>";
			$pantalla .= "<td class='fila2' colspan='".$colspan2."' id='td_padre_ingreso'>";
				$pantalla .= selectCentrosCostos();
			$pantalla .= "</td>";
		$pantalla .= "</tr>";
		$pantalla .= "</table>";
		$pantalla .= "<input type='hidden' id='wcco' name='wcco' value=''>";
		$pantalla .= "<input type='hidden' id='wSolicitaCenimp' name='wSolicitaCenimp' value=''>";
		( $buscaXcedulaActivos == "si" ) ? $buscarHistoria = "on" : $buscarHistoria = "off";
		$pantalla .= "<br><center><input type='button' buscaHistoria='{$buscarHistoria}' onclick='consultarPacientes( this )' value='Consultar' id='btn_consultar' /></center>";
		$pantalla .= "</div>";
		//------------FIN TABLA DE PARAMETROS-------------

		return( $pantalla );
	}

	function formularioConsultaFacturacion(){

		$pantalla = '<br><br>';
		//------------TABLA DE PARAMETROS-------------
		$pantalla .= '<table align="center" width="700px">';
		$pantalla .= "<tr>";
			$pantalla .= '<td class="encabezadotabla" colspan="4" align="center">PAR&Aacute;METROS DE BUSQUEDA</td>';
		$pantalla .= "</tr>";

		$pantalla .= "<tr>";
			$pantalla .= '<td class="fila1" width="20%">Historia Cl&iacute;nica:</td>';
			$pantalla .= '<td class="fila2" width="auto">';
				$pantalla .= "<input type='text' id='whis' />";
			$pantalla .= "</td>";
			$pantalla .= '<td class="fila1">Ingreso: </td>';
			$pantalla .= '<td class="fila2" id="td_padre_ingreso">';
				$pantalla .= "<input type='text' id='wing' />";
			$pantalla .= "</td>";
		$pantalla .= "</tr>";

		$pantalla .= "<tr>";
			$pantalla .= '<td class="fila1" width="80px">Grupo Responsable:</td>';
			$pantalla .= '<td class="fila2" width="auto" colspan="3" align="center">';
				$pantalla .= "<table width='100%' height='100%'>";
				$pantalla .= "<tr><td align='center'>";
					$pantalla .= gruposEmpresas();
				$pantalla .= "</td></tr>";
				$pantalla .= "<tr style='display:none;'>";
					$pantalla.="<td class='fila2' id='td_entidades' align='center'> &nbsp </td>";
				$pantalla .= "</tr>";
			$pantalla .= "</table></td>";
		$pantalla .= "</tr>";

		$pantalla .= "<tr>";
			$pantalla .= '<td class="fila1">Pacientes:</td>';
			$pantalla .= '<td class="fila2" colspan="3" align="center"><input type="radio" checked name="chk_criterio" value="activos">Activos &nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="chk_criterio"  value="egreso">Egresados</td>';
		$pantalla .= "</tr>";

		$pantalla .= "<tr>";
			$pantalla .= "<td class='fila1'>Fecha inicial:</td>";
			$pantalla .= "<td class='fila2' align='center'>";
				$pantalla.=  "<input type='text' value='".date('Y-m-d')."' id='wfecini' /> ";
			$pantalla .= "</td>";
			$pantalla .= "<td class='fila1'>Fecha final:</td><td class='fila2' align='center'>";
			    $pantalla .= "<input type='text' value='".date('Y-m-d')."' id='wfecfin' />";
			$pantalla .= "</td>";
		$pantalla .= "</tr>";

		$pantalla .= "</table>";
		$pantalla .= "<input type='hidden' id='wcco' name='wcco' value=''>";
		$pantalla .= "<input type='hidden' id='wSolicitaCenimp' name='wSolicitaCenimp' value=''>";
		$pantalla .= "<br><center><input type='button' buscaHistoria='on' onclick='consultarPacientes( this )' value='Consultar' id='btn_consultar'/></center>";
		$pantalla .= '<script>
						$("#wfecini").datepicker({
						 showOn: "button",
						 buttonImage: "../../images/medical/root/calendar.gif",
						 buttonImageOnly: true,
						 maxDate:"'.date('Y-m-d').'"
						});
						$("#wfecfin").datepicker({
						 showOn: "button",
						 buttonImage: "../../images/medical/root/calendar.gif",
						 buttonImageOnly: true,
						 maxDate:"'.date('Y-m-d').'"
						});
					</script>';
		//------------FIN TABLA DE PARAMETROS-------------

		return( $pantalla );
	}

	/** final --- DE FORMULARIOS DE CONSULTA **/


	/** FUNCIONES USADAS EN LAS PETICIONES AJAX **/
	function guardarSolicitudImpresion( $whis, $wing, $wdatos, $wmonitor, $wmodalidad, $wfecha_i, $wfecha_f, $weditar, $fecIngreso, $fecEgreso, $wcco, $identificador, $wSolicitaCenimp, $wenviaEmail, $wimpresionDirecta,$htmlProgramasAnexos ){

		global $conex;
        global $wcenimp;
        global $whcebasedato;
		global $wemp_pmla;
		global $wuser;
		global $formulariosElegidos;

		$wdatos = str_replace("\\", "", $wdatos);
		$wdatos = json_decode( $wdatos, true );

		$cadenaPaquetes            = "";
		$cadenaFormulariosPaquetes = "";
		$cadenaFormulariosManual   = $wdatos['formularios_arbol'];
		$numFormularios            = 0;
		$arregloAuxiliarFormuls    = array();
		$arregloAuxiliarFormulsEXT = array();
		$respuesta                 = array();

		$cadenaFormulariosYanexos = $cadenaFormulariosManual;
		$cadenaFormulariosYanexos = explode(",",$cadenaFormulariosYanexos);

		$cadenaSoloFormularios = "";
		$cadenaSoloProgrAnexos = "";
		for($i=0;$i<count($cadenaFormulariosYanexos);$i++)
		{
			if(strlen($cadenaFormulariosYanexos[$i])==6)
			{
				$cadenaSoloFormularios .= $cadenaFormulariosYanexos[$i].",";
			}
			else
			{
				if($htmlProgramasAnexos!="")
				{
					$cadenaSoloProgrAnexos .= $cadenaFormulariosYanexos[$i].",";
				}
			}
		}
		
		$cadenaSoloFormularios = substr($cadenaSoloFormularios, 0, -1);
		$cadenaSoloProgrAnexos = substr($cadenaSoloProgrAnexos, 0, -1);

		$cadenaFormulariosManual = $cadenaSoloFormularios;
		
		if( $wfecha_i == "" or !isset($wfecha_i) or $wfecha_i == "0000-00-00" ) $wfecha_i = $fecIngreso;
		if( $wfecha_f == "" or !isset($wfecha_f) or $wfecha_f == "0000-00-00" ) $wfecha_f = $fecEgreso;

		$paquetesFormulario  = array();
		$formulariosElegidos = explode( ",", $formulariosElegidos );

		foreach ( $wdatos['paquetes'] as $codPaquete => $forms ) {

			foreach ( $forms as $ind => $codform ){
				if( !isset( $paquetesFormulario[$codform] ) ){
					if( !in_array( $codform, $arregloAuxiliarFormuls) and !in_array( $codform, $arregloAuxiliarFormulsEXT) ){
						if(preg_match('/EXT/i',$codform)){
							array_push( $arregloAuxiliarFormulsEXT, $codform );
						}else{
								array_push( $arregloAuxiliarFormuls, $codform );
							}
					}
				}
				$paquetesFormulario[$codform][$codPaquete] = "";
			}
		}

		$auxFormsManuales = explode( ",", $cadenaFormulariosManual);
		if( $cadenaFormulariosManual != '' ){
			foreach ($auxFormsManuales as $i => $value) {
					if( !in_array( $value, $arregloAuxiliarFormuls) and !in_array( $value, $arregloAuxiliarFormulsEXT) ){

						if(preg_match('/EXT/i',$codform)){
								array_push( $arregloAuxiliarFormulsEXT, $value );
							}else{
									array_push( $arregloAuxiliarFormuls, $value );
								}
				}
			}
		}

		$numProAnexos=0;
		foreach($arregloAuxiliarFormuls as &$forx ){
			
			// if(strlen($forx)>6)
			if(strlen($forx)>6 && $htmlProgramasAnexos!="")
			{
				$numProAnexos += 1;
			}
			
			$forx = "'".$forx."'";
		}

		$formsBuscados = implode( ",", $arregloAuxiliarFormuls );
		//query que consulta cuantos formularios hay de cada formulario
		if( trim( $formsBuscados ) != "" ){
			$query= "  SELECT Firpro, count(*) cantidad "
				." 	     FROM ".$whcebasedato."_000036 "
				."      WHERE Firhis = '".$whis."' "
				."        AND Firing = '".$wing."'"
				."        AND Fecha_data BETWEEN '{$wfecha_i}' and '{$wfecha_f}' "
				."		  AND Firpro IN( {$formsBuscados} )"
				."        AND Firfir = 'on' "
				."   GROUP BY Firpro "
				."	 ORDER BY cantidad asc";


			$rs = mysql_query( $query, $conex ) or die( mysql_error()."---- este error");

			while( $row = mysql_fetch_array( $rs ) ){

				$numFormularios += $row['cantidad'];
			}
		}

		foreach( $arregloAuxiliarFormulsEXT as $k => $formularioExterno ){
			if( in_array( $formularioExterno, $formulariosElegidos) )
				$numFormularios += 1;
		}

		$cantProgAnexosPaquetes = 0;
		// if( $numFormularios*1 > 0 ){
		if( $numFormularios*1 > 0 || $cadenaSoloProgrAnexos!="" || $numProAnexos>0){
				$indicePaquetes = 0;
				foreach( $wdatos['paquetes'] as $codPaquete=>$forms ){
					if( $indicePaquetes > 0 ){
						$cadenaPaquetes.="|";
						$cadenaFormulariosPaquetes.="|";
					}
					
					$cantPaquetes = 0;
					$cadenaPaquetes.= $codPaquete;
					$indiceForms = 0;
					foreach($forms as $ind=>$codform){
						
						if(strlen($codform)==6)
						{
							if( $indiceForms > 0 )
								$cadenaFormulariosPaquetes.=",";
							$cadenaFormulariosPaquetes.=$codform;
							$indiceForms++;
							$cantPaquetes++;
						}
						else
						{
							$cadenaSoloProgrAnexos .= $codform.","; // cambiar por la url del anexo
							$cantProgAnexosPaquetes++;
						}
						
						// $cadenaFormulariosPaquetes.=$codform;
						// $indiceForms++;
					}
					
					if($cantPaquetes>0)
					{
						$indicePaquetes++;
					}
					
				}
				
				if($cantProgAnexosPaquetes>0)
				{
					$cadenaSoloProgrAnexos = substr($cadenaSoloProgrAnexos, 0, -1);
				}
				
				$cadenatotal = $cadenaFormulariosPaquetes.",".$cadenaFormulariosManual;
				$totalforms = explode(",", $cadenatotal );
				if( trim( $wcco ) == ""  or !isset($wcco) or trim( $wSolicitaCenimp ) == "" or !isset( $wSolicitaCenimp ) ){
					$datosCco        = consultarCcoPaciente( $whis, $wing );
					$wcco            = $datosCco['codigoCco'];
					$wSolicitaCenimp = $datosCco['solicitaCenimp'];
				}


				if( $weditar == "si" ){
					( ($wSolicitaCenimp == "off" and $wimpresionDirecta == 'on' ) or $wenviaEmail=='on') ? $solter = " Solter = 'on', " :  $solter = ""; //si no hace solicitud al centro de impresion se da por impreso automáticamente
					$query = "UPDATE {$wcenimp}_000005
								 SET Fecha_data = '".date('Y-m-d')."',
									  Hora_data = '".date('H:i:s')."',
									     solcco = '".$wcco."',
										 solpaq = '".$cadenaPaquetes."',
										 solfpa = '".$cadenaFormulariosPaquetes."',
										 solfor = '".$cadenaFormulariosManual."',
										 solfei = '".$wfecha_i."',
										 solfef = '".$wfecha_f."',
										 solnuf = '".$numFormularios."',
										 {$solter}
										 solgen = 'on',
										 Solpan = '".$cadenaSoloProgrAnexos."'
							   WHERE id = '{$identificador}'";
					$rs    = mysql_query( $query, $conex ) or die( mysql_error() );

					$accion 	    = "update";
					$tabla		    = "000005";
					$descripcion    = "Cambio de Datos";
					insertLog( $conex, $wcenimp, $wuser, $accion, $tabla, $err, $descripcion, $identificador, $sql_error, $wmodalidad );


				}else{
						( ($wSolicitaCenimp == "off" and $wimpresionDirecta == 'on' ) or $wenviaEmail=='on') ? $solter = "on" :  $solter = "off"; //si no hace solicitud al centro de impresion se da por impreso automáticamente
						$q= " INSERT ".$wcenimp."_000005 (   Medico   ,   fecha_data,                hora_data,  Solhis,	 Soling,    Solcco,  Solmon      ,    Solmod       ,    	Solpaq    	,    				Solfpa    ,    	 			 	Solfor    , 			Solfei, 		     Solfef,		 Solnuf,	  	      Solgen, 		 Solter, Solest,   Solpan					,		   Seguridad  ) "
									 ."         VALUES ('".$wcenimp."','".date('Y-m-d')."','".date('H:i:s')."','".$whis."','".$wing."', '".$wcco."', '".$wmonitor."', '".$wmodalidad."', '".$cadenaPaquetes."',	'".$cadenaFormulariosPaquetes."', '".$cadenaFormulariosManual."', '".$wfecha_i."'	,'".$wfecha_f."',  '".$numFormularios."',  'on', '{$solter}',	'on','".$cadenaSoloProgrAnexos."', 		'C-".$wuser."') ";
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

						$identificador = mysql_insert_id();

						if( $solter ==  "on" && $wenviaEmail != 'on'){
							$q= " INSERT ".$wcenimp."_000006 (   Medico       ,   fecha_data,   		hora_data,    		Impsol      , impest,     Seguridad  ) "
						 	                 ."         VALUES ('".$wcenimp."','".date('Y-m-d')."','".date('H:i:s')."','".$identificador."', 'on', 'C-".$wuser."') ";
							$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						}
				}
		}

		$respuesta['identificador']   = $identificador;
		$respuesta['wSolicitaCenimp'] = $wSolicitaCenimp;
		$respuesta['wnumformularios'] = $numFormularios;
		$respuesta['wCadProgramasAnexos'] = $cadenaSoloProgrAnexos;

		return( $respuesta );
	}

	function htmllistadoPacientes( $pacientes, $wfact, $clasificacion, $wmodal, $formulariosHce ){
		global $wenviaEmail;
		global $whcebasedato;
		global $conex;
		$menu  = "<div id='padre_listadoPacientes' style='width:100%;'>";
		$menu .= "<br><br><div align='left' onclick='mostrarocultarDiv( \"div_listadopacientes\")' style=' cursor:pointer; width:90%; font-size: 10pt;color:#2A5DB0;font-weight:bold;'>";
		$menu .= " <img id='img_flecha' width='10' border='0' height='10' src='../../images/medical/iconos/gifs/i.p.next[1].gif'>";
		$menu .= " ver Resultados ";
		$menu .= " </div> ";
		$menu .= "<div align='center' id='div_listadopacientes' style='width:90%;'>";
		if( $wfact == "on" ){
			$menu  .= "<center><input type='button' name='btn_guardar' value='Generar Solicitud'  bloquear='si' /></center><br>";
			if( count($pacientes) > 1 ){
				$menu .= "<input type='checkbox' id='chk_todos_global' onclick='checkearTodoGlobal( this );' /><span class='subtituloPagina2'><b>Seleccionar Todo en los grupos </b></span><br>";
			}
		}
		$menu .= "<div class='BordeGris' style='width:90%;'><table width='100%' style='border:0;' id='tabla_pac_facturacion'>";
		foreach( $pacientes as $keyCco=>$historias ){

				$i = 0;
				$menu .= "<tr class='encabezadotabla'><td colspan='10' style='height:30;'>( {$keyCco} )  ".$clasificacion->$keyCco." </td><tr>";

				( $wfact == "off" ) ? $aux           = "<td align='center'> HABITACION </td>" 			 :  $aux           = "<td align='center'>TODOS<br><input type='checkbox' tieneFormularios='si' name='chk_todos' origen='{$keyCco}' onclick='cambiarEstadoChecks( this );'></td>";
				( $wfact == "off" ) ? $ultimaColumna = "<td align='center'> SOLICITUD </td>" :  $ultimaColumna = "<td align='center'>&nbsp;</td>";

				$menu .= "<tr class='botona'>{$aux}<td align='center'>HISTORIA</td><td align='center'>INGRESO</td><td align='center'>NOMBRE</td><td align='center'>FECHA INGRESO</td><td align='center'>TIENE FORMULARIOS<br> HCE</td><td> IMPRESI&Oacute;N<br> PENDIENTE </td><td> NUM. HOJAS </td>{$ultimaColumna}<tr>";

				foreach( $historias as $keyHistoria => $datos ){

					$programasAnexos = consultarScripts($conex,$whcebasedato,$keyHistoria,$datos['ingreso']);
		
					$datosSolicitud           = tieneSolicitudPendiente( $keyHistoria, $datos['ingreso'], $wmodal );
					$solicitudPendiente       = $datosSolicitud['existe'];
					$formulariosDelPaciente   = existenFormulariosPaciente( $keyHistoria, $datos['ingreso'] );
					// ( count( $formulariosDelPaciente ) > 0 ) ? $tieneFormulariosImprimir = true : $tieneFormulariosImprimir = false;
					( count( $formulariosDelPaciente ) > 0 || count( $programasAnexos ) > 0 ) ? $tieneFormulariosImprimir = true : $tieneFormulariosImprimir = false;
					/* Esto se agrega con el propósito de saber los formularios que tiene un paciente en el momento de generación.
					*/
					$divFormularios  = "<div name='dialogo' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' style='display:none;'>";
					$divFormularios .= "<div align='left'><span class='subtituloPagina2' style='font-size:16px;''>Formularios Diligenciados:</span></div>";
					$divFormularios .= "<table name='tbl_diligenciados' style='font-size:14px;' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' >";
					$divFormularios .= "<tr class='encabezadotabla'><td>SE IMPRIME</td><td align='left'>DESCRIPCI&Oacute;N</td></tr>";
					$formulariosDiligenciados = "";
					foreach( $formulariosDelPaciente as $wtipoFormularios=>$formularios ){ //2014-17-01 este for recorre los tipos de formularios, por ahora: formulariosInternos, formulariosExternos
						$formulariosDiligenciados .= " {$wtipoFormularios}='";
						$j = 0;
						foreach ($formularios as $j => $codigo ){
							( is_int($j/2) ) ? $wclase = "fila1" : $wclase = "fila2";
							( $j == 0 ) ? $formulariosDiligenciados .= $codigo : $formulariosDiligenciados .= ",".$codigo;
							$divFormularios .= "<tr class='{$wclase}'><td align='center'><input type='checkbox' disabled='disabled' name='elegidoParaImprimir' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' codigo='{$codigo}' ></td><td align='left'>{$formulariosHce[$codigo]}</td></tr>";
						}
						$formulariosDiligenciados .= "' ";
					}
					$divFormularios .= "</table>";
					$divFormularios .= "<table name='tbl_a_imprimir' style='font-size:14px;' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' >";
					$divFormularios .= "</table>";
					$divFormularios .= "</div>";

					if( $wfact == 'off' ){
						$divFormularios = "";
					}

					( $solicitudPendiente )       ? $display 		     = "" 		: $display              = " style='display:none;' ";
					( $wfact == "off" )           ? $display2 		     = "" 		: $display2             = " display:none; ";
					( $wfact == "off" )           ? $mostrarDetalleEvento= "" 		: $mostrarDetalleEvento = " onClick='mostrarDetalleFormularios( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\" )' ";
					( $solicitudPendiente )       ? $txtBoton 		     = "Editar" : $txtBoton             = " Solicitar Imp. ";
					( $solicitudPendiente )       ? $chk_anular_visible  = ""  	    :  $chk_anular_visible  = "style='display:none;' ";
					( $solicitudPendiente )       ? $hojas			     = $datosSolicitud['numHojas'] :  $hojas = "&nbsp;";
					( $solicitudPendiente )       ? $identificador	     = $datosSolicitud['id'] :  $identificador = "";
					( $tieneFormulariosImprimir ) ? $tieneFormularios    = " <div class='formulariosElegidos' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' style='{$display2} cursor:pointer;' {$mostrarDetalleEvento}><img src='/matrix/images/medical/movhos/checkmrk.ico'></div> " : $tieneFormularios = " &nbsp; ";
					( $tieneFormulariosImprimir ) ? $disabled  			 = ""  	    :  $disabled = "disabled ";
					( $tieneFormulariosImprimir ) ? $atributoChk		 = " tieneFormularios='si' "  	    : $atributoChk		 = " tieneFormularios='no' ";

					if( !isset($datos['centroCostos']) )
						$datos['centroCostos'] = "";

					( $wfact == "off" ) 		  ? $primeraColumna      = $datos['habitacionActual']."<input type='hidden' este='siiiii2' class='contenedor_formularios' historia='{$keyHistoria}' identificador='{$identificador}' ingreso='{$datos['ingreso']}' {$formulariosDiligenciados} formulariosElegidos=''>" : $primeraColumna = "<input type='checkbox' class='contenedor_formularios' name='chk_pacientes' {$atributoChk} {$disabled} historia='{$keyHistoria}' identificador='{$identificador}' ingreso='{$datos['ingreso']}' origen='{$keyCco}' centroCostos='{$datos['centroCostos']}' fechaIngreso='{$datos['fechaIngreso']}' fechaEgreso='{$datos['fechaEgreso']}' {$formulariosDiligenciados} formulariosElegidos='' onclick='cambiarEstadoBoton( this );'>";

					if( $wenviaEmail == 'on' )  $txtBoton = "Enviar correo";

					$i++;
					( is_int($i/2) ) ? $wclass = 'fila1' : $wclass = 'fila2';

					if( $datos['altaProceso'] == "on" && $wfact == "off" ) //si está en alta en proceso y la modalidad no es de facturación
						$wclass = 'fondoamarillo';

					$impresionPendiente = " <div name='div_impresion_pendiente' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' {$display}><img src='/matrix/images/medical/movhos/checkmrk.ico'></div> ";

					$menu .= "<tr class='{$wclass}' >";
					$menu .= "<td style='display:none;'>{$divFormularios}</td>";
					$menu .= "<td align='center' style='height:30;'> {$primeraColumna}</td>";
					$menu .= "<td align='center' style='height:30;'> {$keyHistoria} </td>";
					$menu .= "<td align='center'>{$datos['ingreso']}</td>";
					$menu .= "<td align='left'  >{$datos['nombre']}</td>";
					$menu .= "<td align='center'>{$datos['fechaIngreso']}</td>";
					$menu .= "<td align='center'>{$tieneFormularios}</td>";
					$menu .= "<td align='center'>{$impresionPendiente}</td>";
					$menu .= "<td align='center' name='numHojas' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}'>{$hojas}</td>";
					( $solicitudPendiente ) ? $editar = "si" : $editar = "no";
					$menu .= "<td align='center'><input type='button' $disabled class='btnConsulta' {$atributoChk} historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' value='{$txtBoton}' origenDatos='{$keyCco}' onclick='consultarHistoriaIngresoActivo(  {$keyHistoria}, {$datos['ingreso']}, \"{$editar}\", {$datos['fechaIngreso']}, {$datos['fechaEgreso']}, \"{$identificador}\" )'></td>";
					//$menu .= "<td align='center'><input type='checkbox' name='chk_anular' historia='{$keyHistoria}' ingreso='{$datos['ingreso']}' {$chk_anular_visible} origen='ext' onclick=' anularSolicitud( this ); '></td>";
					$menu .= "<tr>";
				}
			}
		$menu .= "</table></div></div></div>";
		return( $menu );
	}

	function buscarPacientesActivosCco( $wcco, $clasificacion, $wmodal ){
		global $wmovhos, $conex, $wfact;
		global $caracteres;
		global $caracteres2;
		$formulariosHce = array();

		$query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Ubisac ccoActual, Ubihac habitacionActual, b.fecha_data fechaIngreso, b.ubialp altaProceso, b.ubifad fechaEgreso, c.Ccocim solicitaCenimp
					   FROM {$wmovhos}_000018 b
					   INNER JOIN
							 root_000037 on (ubihis = orihis AND ubiing = oriing AND ubiald != 'on' AND ubisac = '{$wcco}')
					   INNER JOIN
							 root_000036 on ( Pactid = Oritid AND Pacced = Oriced )
					   INNER JOIN
					        ".$wmovhos."_000011 c on ( Ccocod = Ubisac )
					   LEFT JOIN
							".$wmovhos."_000020 h on ( habcod = ubihac ) ORDER BY habord, habcod ";


		$rs  = mysql_query( $query, $conex ) or die( mysql_error() );
		$num = mysql_num_rows($rs);
		$pacientes = array();
		while( $row = mysql_fetch_array( $rs ) ){

				$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
				$nombre = str_replace( $caracteres, $caracteres2, $nombre );

				$pacientes[$row['ccoActual']][$row['historia']]['ingreso']          = $row['ingreso'];
				$pacientes[$row['ccoActual']][$row['historia']]['nombre']           = $nombre;
				$pacientes[$row['ccoActual']][$row['historia']]['cedula']           = $row['Pacced'];
				$pacientes[$row['ccoActual']][$row['historia']]['tipoDocu']         = $row['Pactid'];
				$pacientes[$row['ccoActual']][$row['historia']]['fechaIngreso']     = $row['fechaIngreso'];
				$pacientes[$row['ccoActual']][$row['historia']]['fechaEgreso']      = date("Y-m-d");
				$pacientes[$row['ccoActual']][$row['historia']]['habitacionActual'] = $row['habitacionActual'];
				$pacientes[$row['ccoActual']][$row['historia']]['altaProceso']      = $row['altaProceso'];
				$pacientes[$row['ccoActual']][$row['historia']]['centroCostos']     = $row['ccoActual'];
				$pacientes[$row['ccoActual']][$row['historia']]['solicitaCenimp']   = $row['solicitaCenimp'];
		}

		$menu = "";
		if( count( $pacientes ) > 0 ){
			$formulariosHce = todoslosFormularios();
			$menu .= htmllistadoPacientes( $pacientes, $wfact, $clasificacion, $wmodal,$formulariosHce );
		}

		return( $menu );
	}

	function datosPaciente( $whis, $wing, $consulta, $enviaEmail ){
		global $conex;
	    global $wcenimp;
		global $wemp_pmla;
		global $wuser;
		global $whcebasedato;
		global $wmovhos;
		global $wcliame;

		$datosPaciente = array();

		if( $consulta == "altaDefinitiva" ){
			$horasDesdeAlta = 0;
			$query = " SELECT ubiald altaDefinitiva, ubifad fechaAltaDefinitiva, ubihad horaAltaDefinitiva
				 		 FROM {$wmovhos}_000018
				 		WHERE ubihis = '{$whis}'
				 		  AND ubiing = '{$wing}'";
			$rs    = mysql_query( $query, $conex );
			$row   = mysql_fetch_array( $rs );
			if( $row['altaDefinitiva'] == "on"){//si tiene alta definitiva, miro si lleva mas de 6 horas
				$horaAlta   = strtotime( $row['fechaAltaDefinitiva']." ".$row['horaAltaDefinitiva'] );
				$horaActual = strtotime( date('Y-m-d H:i:s') );
				$diferencia = $horaActual - $horaAlta;
				$horasDesdeAlta = $diferencia / 3600;//-->2016-06-21
				//echo "<br>horas desde el alta: $horasDesdeELAlta <br>";
			}

			$datosPaciente['altaDefinitiva'] = $row['altaDefinitiva'];
			$datosPaciente['horasDesdeAlta'] = $horasDesdeAlta;
		}

		if( $consulta == "nombrePaciente" ){
			$query  = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Pacced
						  FROM root_000037 as ori
					     INNER JOIN
							   root_000036 as pac on ( ori.Oriori='{$wemp_pmla}' AND pac.Pactid = ori.Oritid AND pac.Pacced = ori.Oriced AND ori.Orihis = '{$whis}' )";

			$rs  	= mysql_query( $query, $conex );
			$row    = mysql_fetch_array( $rs );
			$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
			$nombre = str_replace( $caracteres, $caracteres2, $nombre );

			$datosPaciente['nombre']   = $nombre;
			$datosPaciente['whis']     = $whis;
			$datosPaciente['wing']     = $wing;
			$datosPaciente['tipo_doc'] = $row['Pactid'];
			$datosPaciente['doc']      = $row['Pacced'];

		}

		return( $datosPaciente );
	}

	function mostrarEncabezadoPaciente( $whis, $wing, $wempresa ){

		global $conex;
		global $wmovhos;
		global $whcebasedato;
		global $wemp_pmla;
		global $wcliame;

		$datosPaciente = array();

		$query = " SELECT CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente ,Pacnac as fecha_nacimiento,Pacsex as genero,$whis as historia,$wing as ingreso,Pactid as tipo_documento,Pacced as documento"
				."   FROM root_000036, root_000037 "
				. " WHERE orihis = '".$whis."'"
				. "   AND pacced = oriced "
				. "   AND pactid = oritid "
				. "   AND oriori = '".$wemp_pmla."' ";

		$query2 = "SELECT Ingnre,Ubisac,Ubihac,Cconom ,ING.Fecha_data, UBI.ubifad, Empmai "
				. "  FROM ".$wmovhos."_000018 UBI, ".$wmovhos."_000011, ".$wmovhos."_000016 ING LEFT JOIN ".$wcliame."_000024 ON (empcod = Ingres)"
				. " WHERE Ubihis = '".$whis."'"
				. "   AND Ubiing = '".$wing."'"
				. "   AND ubihis = inghis "
				. "   AND ubiing = inging "
				. "   AND ccocod = ubisac ";

		$err  = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num  = mysql_num_rows($err);
		$err2 = mysql_query($query2,$conex) or die(mysql_errno().":".mysql_error());
		$num2 = mysql_num_rows($err2);

		if ($num>0 && $num2>0){
			$row = mysql_fetch_assoc($err);
			$row2 = mysql_fetch_array($err2);

			$sexo="MASCULINO";
			if($row['genero'] == "F")
				$sexo="FEMENINO";

			// $ann   =(integer)substr($row['fecha_nacimiento'],0,4)*360 +(integer)substr($row['fecha_nacimiento'],5,2)*30 + (integer)substr($row['fecha_nacimiento'],8,2);
			// $aa    =(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			// $ann1  =($aa - $ann)/360;
			// $meses =(($aa - $ann) % 360)/30;

			// if ($ann1<1){
				// $dias1=(($aa - $ann) % 360) % 30;
				// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";
			// }else{
				// $dias1=(($aa - $ann) % 360) % 30;
				// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
			// }
			$wedad = calcularEdadPaciente($row['fecha_nacimiento']);
			$wpac = $row['tipo_documento']." ".$row['documento']."<br>".$row['paciente'];

			$datosPaciente['nombre'] = $row['paciente'];

			$datosPaciente['email']  = $row2['Empmai'];
 			if(!isset($wing))
				$wing=$row['ingreso'];
			if( $row2[5] == '0000-00-00' ) $row2[5] = date('Y-m-d');
				$color="#dddddd";

			$color1="#C3D9FF";
			$color2="#E8EEF7";
			$color3="#CC99FF";
			$color4="#99CCFF";
			$encabezadoPaciente = "<center><table border=1 width='712' class=tipoTABLE1>";
			$encabezadoPaciente .= "<tr><td rowspan=4 align=center><IMG SRC='/MATRIX/images/medical/root/".$wempresa.".jpg' id='logo'></td>";
			$encabezadoPaciente .= "<td id=tipoL01C>Paciente</td><td colspan=5 id=tipoL04>".$wpac."</td></tr>";
			$encabezadoPaciente .= "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row['historia']."-".$wing."</td><td id=tipoL01>Edad actual</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
			$encabezadoPaciente .= "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row2[3]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row2[2]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row2[0]."</td></tr>";
			$encabezadoPaciente .= "<tr><td id='tipoL01C'>Fecha <br>Inicial</td><td id=tipoL02C colspan=2 ><input type='text' value='".$row2[4]."' id='fecha_inicial' /></td><td id=tipoL01C>Fecha <br>Final</td><td id=tipoL02C colspan=2><input type='text' value='".$row2[5]."' id='fecha_final' /></td></tr>";
			$encabezadoPaciente .= "</table><br></center>";
			$encabezadoPaciente .= '
			<script>
				$("#fecha_inicial").datepicker({
				 showOn: "button",
				 buttonImage: "../../images/medical/root/calendar.gif",
				 buttonImageOnly: true,
				 minDate:"'.$row2[4].'",
				 maxDate:"'.$row2[5].'"
				});
				$("#fecha_final").datepicker({
				 showOn: "button",
				 buttonImage: "../../images/medical/root/calendar.gif",
				 buttonImageOnly: true,
				 minDate:"'.$row2[4].'",
				 maxDate:"'.$row2[5].'"
				});
			</script>';
		}

		$datosPaciente['encabezadoPaciente'] = $encabezadoPaciente;
		return( $datosPaciente );
	}

	function buscarHistoria( $tipo, $numero ){

		global $conex;
		global $wemp_pmla;
		global $wmovhos;
		$datos = array();

		$query = " SELECT Inghis historia, Inging ingreso, b.id id
					 FROM root_000037 a
					INNER JOIN
						  {$wmovhos}_000016 b ON (Inghis = Orihis AND Oritid = '{$tipo}' AND Oriced='{$numero}')
					WHERE Oriori = '{$wemp_pmla}'
					ORDER BY id desc";
		$rs    = mysql_query( $query, $conex ) or die;

		while( $row = mysql_fetch_array( $rs ) ){
			$datos['historia'] = $row['historia'];
			$datos['ingresos'][$row['ingreso']] = "";
		}

		return( $datos );
	}

	function consultarRol(){
		global $conex;
        global $wcenimp;
		global $wemp_pmla;
		global $wuser;
		global $whcebasedato;
		global $wmovhos;

		$query = " SELECT Usurol rol, Ccocim tieneCenimp
					 FROM {$whcebasedato}_000020, usuarios LEFT JOIN {$wmovhos}_000011 b ON ( ccocod = Ccostos )
					WHERE Usucod = '{$wuser}'
					  AND Usuest = 'on'
					  AND codigo = Usucod ";

		$rs  = mysql_query( $query, $conex ) or die( mysql_error() );
		$row = mysql_fetch_array( $rs );
		if( $row['tieneCenimp'] == '' ) $row['tieneCenimp'] = 'on';

		return( $row );
	}

	function consultarModalidadMonitor( $rol ){

		global $conex;
        global $wcenimp;
		global $wemp_pmla;
		global $wuser;

		$query = " SELECT rmmmod modalidad, rmmmon monitor, moddes descripcion, modfac esFacturacion, modpaa soloPacientesActivos, modlog incluyeLogo, Modtap incluyeTapa, Modidi impresionDirecta, Modcor enviaEmail
					 FROM {$wcenimp}_000001
			   INNER JOIN {$wcenimp}_000003 on ( modcod = rmmmod )
					WHERE rmmrol = '{$rol}'
 					  AND rmmest = 'on'";

		$rs  = mysql_query( $query, $conex ) or die( mysql_error());
		$row = mysql_fetch_array( $rs );
		return( $row );
	}

	function selectCentrosCostos(){

		global $conex;
		global $wemp_pmla;
		global $wuser;
		global $wmovhos;
		global $wbasedato;

		//$centrosCostos = centrosCostosHospitalariosTodos( $conex, $wmovhos );
		$centrosCostos = consultaCentrosCostos("ccohos, ccourg, ccocir");

		$select = "<select id='wcco'>";
		$select .= "<option value=''> ------ </option>";
			foreach ($centrosCostos as $i => $cco) {
				$select .= "<option value='{$cco->codigo}'>{$cco->nombre}</option>";
			}
		$select .= "</select>";

		return( $select );
	}

	function gruposEmpresas(){

		global $conex, $wemp_pmla, $whcebasedato, $wcenimp;
		$selectEmpresas = "";
		$query  = " SELECT Empcod codigoGrupo, Empdes nombreGrupo
					  FROM {$wcenimp}_000008
					 WHERE Empest = 'on'
				     ORDER BY nombreGrupo";
		$rs 	= mysql_query( $query, $conex );
		$num 	= mysql_num_rows( $rs );
		if( $num > 0 ){
			$selectEmpresas .= "<select id='wgrupo' name='wgrupo' style='width:80%;' onchange='busarEntidadesEnGrupo( this )'>";
			$selectEmpresas .= "<option selected value='%'>---</option>";
			while( $row = mysql_fetch_array( $rs ) ){
				//( $row['codigoGrupo'] == "*" ) ? $selected =  "selected" : $selected = "";
				$selectEmpresas .= "<option  value='{$row['codigoGrupo']}'>{$row['nombreGrupo']}</option>";
			}
			$selectEmpresas .= "</select>";
		}
		return( $selectEmpresas );
	}

	function buscarEntidadesGrupo( $codigoGrupo ){

		global $whcebasedato, $wmovhos, $conex, $wcenimp, $wcliame;
		global $caracteres, $caracteres2;
		$listaEntidades = "";

		( $codigoGrupo == "*" ) ? $buscarCodigo = "" : $buscarCodigo = " Empcod = '{$codigoGrupo}' AND ";
		$query = " SELECT Empemp empresas, Empcod codigoGrupo, Empdes nombreGrupo
					 FROM {$wcenimp}_000008
					WHERE {$buscarCodigo}
					      Empest = 'on'";
		
		$rs     = mysql_query( $query, $conex );
		$numGru = mysql_num_rows( $rs );

		while( $row  = mysql_fetch_array( $rs ) ){

			$aux   = explode( ",", $row['empresas'] );

			foreach ($aux as $i => $empresa) {
				$aux[$i] = "'".$aux[$i]."'";
			}
			$condicionEmpresas = implode( ",", $aux );

			$query  = " SELECT Empcod codigo, Empnit nit, Empnom nombre
						  FROM {$wcliame}_000024
						 WHERE Empcod in ({$condicionEmpresas})
					     GROUP BY codigo, nombre";
			$rs2    = mysql_query( $query, $conex );
			$num    = mysql_num_rows( $rs );
			if( $num > 0 ){
				$listaEntidades .= "<div align='left' style=' cursor:pointer; width:70%; font-size: 7pt;color:#2A5DB0;font-weight:bold; '>";
				$listaEntidades .= "<div style=' cursor:pointer; ' onclick='mostrarocultarDiv( \"div_empresas_{$row['codigoGrupo']}\")'><img id='img_flecha' width='10' border='0' height='10' src='../../images/medical/iconos/gifs/i.p.next[1].gif'>";
				$listaEntidades .= $row['codigoGrupo'].", ".$row['nombreGrupo'];
				$listaEntidades .= "</div></div>";
				( $numGru > 1 ) ? $displayGrupo = " display:none;" : $displayGrupo = "";
				$listaEntidades .= "<div id='div_empresas_{$row['codigoGrupo']}' style='width:70%; {$displayGrupo} '><table style='border: 1px solid; border-color:#2A5DB0; font-size:10; width:100%;'>";
				if( ( $numGru > 1 ) ) $listaEntidades .= "<tr class='encabezadotabla'><td colspan='2'><input type='checkbox' checked id='chk_{$row['codigoGrupo']}' onclick='elegirTodos( \"{$row['codigoGrupo']}\" , this )' />&nbsp;&nbsp;TODOS</td>";

				while( $row2 = mysql_fetch_array( $rs2 ) ){

					$nombre = str_replace($caracteres, $caracteres2, $row2['nombre']);
					$listaEntidades .= "<tr><td> <input type='checkbox' checked name='entidadesGrupo' nomEntidad='{$nombre}' value='".trim($row2['codigo'])."'>{$row2['codigo']},  {$nombre} </td></tr>";
				}

				$listaEntidades .= "</table></div><br>";
			}
		}

		return( $listaEntidades );
	}

	function buscarPacientesFacturacion( $wmovhos, $whcebasedato, $wentidades, $westPac, $wfecini, $wfecfin, $clasificacion, $wmodal){

		global $conex;
		global $caracteres, $caracteres2, $wfact;
		$aux  = explode( ",", $wentidades );

		foreach ($aux as $i => $codigoEntidad) {
			$aux[$i] = "'".trim($aux[$i])."'";
		}

		$aux = implode( ",", $aux );
		$condicionResponsables = "Ingres IN ( {$aux} )";

		if( $westPac == "activos" ){
			$condiciones = " ubiing = oriing AND Ubiald != 'on' AND b.fecha_data BETWEEN '{$wfecini}' and '{$wfecfin}' ";
		}

		if( $westPac == "egreso" ){
			$condiciones = " Ubiald = 'on' AND b.ubifad BETWEEN '{$wfecini}' and '{$wfecfin}' ";
		}

		$query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Ubisac ccoActual, Ubihac habitacionActual, b.fecha_data fechaIngreso, b.ubialp altaProceso, c.ingres responsable, b.ubifad fechaEgreso
				    FROM {$wmovhos}_000018 b
				   INNER JOIN
				   		 {$wmovhos}_000016 c on ( inghis = ubihis AND inging = ubiing AND {$condicionResponsables})
				   INNER JOIN
						 root_000037 on (ubihis = orihis AND {$condiciones} )
				   INNER JOIN
						 root_000036 on ( Pactid = Oritid AND Pacced = Oriced )
				   INNER JOIN
					     movhos_000011 p on ( Ccocod = Ubisac )
				   GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12";

		$rs  = mysql_query( $query, $conex ) or die( mysql_error() );
		$num = mysql_num_rows($rs);
		$pacientes = array();
		while( $row = mysql_fetch_array( $rs ) ){

				$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
				$nombre = str_replace( $caracteres, $caracteres2, $nombre );
				( $row['fechaEgreso'] == "0000-00-00" ) ? $fechaEgreso = date("Y-m-d") : $fechaEgreso = $row['fechaEgreso'];

				$pacientes[$row['responsable']][$row['historia']]['ingreso']          = $row['ingreso'];
				$pacientes[$row['responsable']][$row['historia']]['nombre']           = $nombre;
				$pacientes[$row['responsable']][$row['historia']]['cedula']           = $row['Pacced'];
				$pacientes[$row['responsable']][$row['historia']]['tipoDocu']         = $row['Pactid'];
				$pacientes[$row['responsable']][$row['historia']]['fechaIngreso']     = $row['fechaIngreso'];
				$pacientes[$row['responsable']][$row['historia']]['fechaEgreso']      = $fechaEgreso;
				$pacientes[$row['responsable']][$row['historia']]['habitacionActual'] = $row['habitacionActual'];
				$pacientes[$row['responsable']][$row['historia']]['altaProceso']      = $row['altaProceso'];
				$pacientes[$row['responsable']][$row['historia']]['centrosCostos']    = $row['ccoActual'];
				//$pacientes[$row['responsable']][$row['historia']]['solicitaCenimp']   = $row['solicitaCenimp'];
		}

		$listado = "";
		if( count($pacientes) > 0 ){
			$formulariosHce = todoslosFormularios();
			$listado .= htmllistadoPacientes( $pacientes, $wfact, $clasificacion, $wmodal, $formulariosHce );
		}
		return( $listado );
	}

	function existePaciente( $whis, $wing ){
		global $wsoloActivos, $conex, $wmovhos, $wusuCenimp, $wenviaEmail;

		$condicionAlta =  "";
		if ( ( $wsoloActivos == "on" and trim( $wusuCenimp ) =="on") or ( $wsoloActivos == "on" and trim( $wusuCenimp ) =="off" and $wenviaEmail=="on" ) )//verifica que sean pacientes activos solo cuando sea necesario según las condiciones necesarias
			$condicionAlta = " AND ubiald != 'on' ";

		$query = "SELECT id
					FROM {$wmovhos}_000018
				   WHERE ubihis  = '{$whis}'
				     AND ubiing  = '{$wing}'
				     	 {$condicionAlta}";
		$rs    = mysql_query( $query, $conex );
		$num   = mysql_num_rows( $rs );
		if( $num < 1)
			return false;
		else
			return true;
	}

	function tieneSolicitudPendiente( $whis, $wing, $wmodal ){

		global $wcenimp, $conex;

		$query = " SELECT id, solnuh numHojas
				     FROM {$wcenimp}_000005
				    WHERE solhis = '{$whis}'
				      AND soling = '{$wing}'
				      AND Solmod = '{$wmodal}'
					  AND solgen = 'off'
					  AND solest = 'on'
					  AND solter = 'off'";

		$rs        = mysql_query( $query, $conex );
		$row       = mysql_fetch_array( $rs );
		$respuesta = array();

		if( mysql_num_rows( $rs ) > 0 ){
			$respuesta['numHojas'] = $row['numHojas'];
			$respuesta['id']	   = $row['id'];
			$respuesta['existe']   = true;
		}else{
			$respuesta['existe'] = false;
		}
		return $respuesta;
	}

	function consultarDatosSolicitud( $whis, $wing, $modal ){

		global $conex, $wcenimp;
		global $paquetesSolicitud, $formulariosIndependientes;

		$query = " SELECT Solpaq paquetes, solfpa formusPaquete, Solfor formusIndependientes
					 FROM {$wcenimp}_000005
					WHERE Solhis = '{$whis}'
					  AND Soling = '{$wing}'
					  AND Solmod = '{$modal}'
					  AND Solter != 'on'
					  AND Solest = 'on'";

		$rs    = mysql_query( $query, $conex );
		if( mysql_num_rows( $rs ) > 0 ){

			$row =  mysql_fetch_array( $rs );
			$paquetes       = explode( "|", $row['paquetes'] );
			$formusPaquetes = explode( "|", $row['formusPaquete'] );

			foreach ( $paquetes as $i => $codPaquete ) {

				$formularios = explode( ",", $formusPaquetes[$i] );
				foreach( $formularios as $j => $codFormulario ){

					$paquetesSolicitud[$codPaquete][ $codFormulario ] = "";

				}
			}

			$codsFormusIndep = explode( ",", $row['formusIndependientes']);
			foreach ($codsFormusIndep as $i => $value) {
				$formulariosIndependientes[$value]="";
			}
		}
		return;
	}

	function generarArchivosHtml_Pdf( $identificador, $incluyeLogo, $incluyeTapa, $htmlProgramasAnexos ){
		global $conex;
        global $wcenimp;
        global $whcebasedato;
		global $wemp_pmla;
		global $wuser;
		global $wmovhos;
		global $wmodalidad;
		global $agregarFecha;
		global $fechaConsultada;

		$wseparaFormularios = 'off';

		$query = " SELECT Modref, Modsfo
					 FROM {$wcenimp}_000001
				    WHERE Modcod = '".$wmodalidad."' ";
		$err   = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num   = mysql_num_rows($err);

		if($num > 0){
			$row = mysql_fetch_array($err);
			$wseparaFormularios = $row[1];
			if( $row[0] == 'on' ){
				generarPdfModalidadPaquetes($identificador, $incluyeLogo, $incluyeTapa, $wseparaFormularios,$htmlProgramasAnexos);
				return;
			}
		}

		$formulariosBuscados = array();
		$query = " SELECT A.id as codigo_solicitud, A.Fecha_data as fecha, A.Hora_data as hora, Solhis as historia, Soling as ingreso, Solmon as monitor, "
				."		  Solfpa as forms_paquetes, Solfor as forms_manual, Solfei as fecha_i, Solfef as fecha_f"
				."   FROM ".$wcenimp."_000005 A "
				."  WHERE id = '".$identificador."' ";

		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

		$num = mysql_num_rows($err);
		if($num > 0){
			$row = mysql_fetch_assoc($err);

			$paquetesAux = explode( "|", $row['forms_paquetes'] );

			foreach( $paquetesAux as $i => $formulariosPaquete ){
				$formulariosPaqueteAux = explode( ",", $paquetesAux[$i] );
				foreach( $formulariosPaqueteAux as $j => $codigoFormulario ){
					if( !in_array( $formulariosPaqueteAux[$j], $formulariosBuscados ) and ( trim( $codigoFormulario) != "") )
						array_push( $formulariosBuscados, $formulariosPaqueteAux[$j] );
				}
			}

			$formulariosAux = explode( ",", $row['forms_manual'] );

			foreach( $formulariosAux as $k => $codigoFormulario ){
					if( !in_array( $codigoFormulario, $formulariosBuscados ) and ( trim( $codigoFormulario) != "") )
						array_push( $formulariosBuscados, $codigoFormulario );
				}

			$formularios = implode( ",", $formulariosBuscados );

			generarCodigoHtmlFormularios( $formularios, $row['historia'], $row['ingreso'], $row['fecha_i'], $row['fecha_f'], $row['codigo_solicitud'], $incluyeTapa, $incluyeLogo, $wseparaFormularios,$htmlProgramasAnexos );
		}
	}

	function generarCodigoHtmlFormularios( $lista_formularios, $whis, $wing, $wfechai, $wfechaf, $wcodigo_solicitud, $wllevaTapa, $wllevaLogo, $wseparaFormularios,$htmlProgramasAnexos ){

		global $whcebasedato;
		global $conex;
		global $wemp_pmla;
		global $wmodalidad;
		global $wmonitor;
		global $wcenimp;
		global $wmovhos;
		global $agregarFecha;
		global $fechaConsultada;
		global $wespecial;

		$codigo_html = "";
		$usuario     = $_SESSION['user'];
		$pos         = strpos($usuario,"-");
		$usuario     = substr($usuario,$pos+1,strlen($usuario));

		$empresa     = $whcebasedato;
		$i           = 0;
		$paquetes    = array();
		$paquetes    = explode(",", $lista_formularios);
		$arr_aux     = array();

		//2014-01-13
		foreach($paquetes as $formu ){
			if(!preg_match('/EXT/i',$formu)){
				array_push( $arr_aux, $formu );
			}
		}

		$paquetes  = $arr_aux;
		$en        = $lista_formularios;
		$key       = $usuario;
		$wintitulo = "Historia: ".$whis." Ingreso: ".$wing;
		$Hgraficas = " |";
		$CLASE     = "I";
		$whtml     = 0;
		$queryI    = "";

		if($wespecial != "TODAS" && $wespecial != "" )
		{
			$query = "DROP TABLE IF EXISTS TESPECIAL".$key.";";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$query = "CREATE TABLE if not exists TESPECIAL".$key." as ";
			$query .= " select Firusu as usuario from ".$empresa."_000036, ".$wmovhos."_000048  ";
			$query .= "   where Firhis = '".$whis."' ";
			$query .= " 	and Firing = '".$wing."' ";
			$query .= " 	and Firusu = Meduma ";
			$query .= " 	and Medesp = '".$wespecial."'";
			$query .= "  group by 1 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

			$query = "CREATE UNIQUE INDEX claveE on TESPECIAL".$key." (usuario(8))";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		}

		for ($i=0; $i < count($paquetes); $i++){
			if( $i>0 )
				$queryI.=" UNION ALL ";
			if($wespecial != "TODAS" && $wespecial != "" )
			{
				//						        		 0                                      1                          2        						       3                                          4                           5                          6                           7                          8                          9                         10                       11                                      12                       13                         14                         15                         16                         17							18							19
				$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".$paquetes[$i].".movdat,".$empresa."_000002.Detorp,".$empresa."_".$paquetes[$i].".fecha_data,".$empresa."_".$paquetes[$i].".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$paquetes[$i].".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir, ".$empresa."_000002.Detimc, ".$empresa."_000002.Detccu from ".$empresa."_".$paquetes[$i].",".$empresa."_000002,".$empresa."_000001,TESPECIAL".$key;
				$queryI .= " where ".$empresa."_".$paquetes[$i].".movpro='".$paquetes[$i]."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movhis='".$whis."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".moving='".$wing."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".fecha_data between '".$wfechai."' and '".$wfechaf."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movpro=".$empresa."_000002.detpro ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movcon = ".$empresa."_000002.detcon ";
				//$queryI .= "   and ".$empresa."_000002.detest='on' ";
				$queryI .= "   and ".$empresa."_000002.detvim IN ('A','I') ";
				$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' ";
				$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movusu = TESPECIAL".$key.".usuario ";
			}else{
				//						        		 0                                      1                          2        						       3                                          4                           5                          6                           7                          8                          9                         10                       11                                      12                       13                         14                         15                         16                         17							18							19
				$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".$paquetes[$i].".movdat,".$empresa."_000002.Detorp,".$empresa."_".$paquetes[$i].".fecha_data,".$empresa."_".$paquetes[$i].".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$paquetes[$i].".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir, ".$empresa."_000002.Detimc, ".$empresa."_000002.Detccu from ".$empresa."_".$paquetes[$i].",".$empresa."_000002,".$empresa."_000001 ";
				$queryI .= " where ".$empresa."_".$paquetes[$i].".movpro='".$paquetes[$i]."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movhis='".$whis."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".moving='".$wing."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".fecha_data between '".$wfechai."' and '".$wfechaf."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movpro=".$empresa."_000002.detpro ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movcon = ".$empresa."_000002.detcon ";
				//$queryI .= "   and ".$empresa."_000002.detest='on' ";
				$queryI .= "   and ".$empresa."_000002.detvim IN ('A','I') ";
				$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' ";
				$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro ";
			}
		}
		$wnombrePDF       = $wemp_pmla."Solicitud_".$wcodigo_solicitud;
		$mostrarObjectPdf = 'off';
		$respuesta        = construirPDF($conex,$empresa,$wmovhos, $wemp_pmla, $queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,$whtml,$wnombrePDF, $wllevaTapa, $wllevaLogo, $mostrarObjectPdf, $wseparaFormularios, $agregarFecha, $fechaConsultada, $htmlProgramasAnexos );
		
		$aux              = explode( "||||||", $respuesta );
		$paginas          = trim( $aux[1] );

		if( isset($paginas) and (float)$paginas > 0 ){
			$query = "UPDATE {$wcenimp}_000005
						 SET Solnuh = '{$paginas}', Solgen='off'
					   WHERE id = '$wcodigo_solicitud'";
			$rsUp  = mysql_query( $query, $conex );
			echo "{$paginas}|";
		}
		$query = "DROP TABLE IF EXISTS TESPECIAL".$key.";";
		$err = mysql_query($query,$conex);
	}

	function generarPdfModalidadPaquetes( $identificador, $incluyeLogo, $incluyeTapa, $wseparaFormularios,$htmlProgramasAnexos ){

		global $conex;
        global $wcenimp;
        global $whcebasedato;
		global $wemp_pmla;
		global $wuser;
		global $wmovhos;
		global $wmodalidad;
		global $wmonitor;
		global $wespecial;

		$whis    = "";
		$wing    = "";
		$wfechai = "";
		$wfechaf = "";

		$paquetesdeformularios = array();
		$paqsDefecto           = array();

		$query = " SELECT A.id as codigo_solicitud, A.Fecha_data as fecha, A.Hora_data as hora, Solhis as historia, Soling as ingreso, Solmon as monitor, "
				."		  Solpaq as paquetes, Solfpa as forms_paquetes, Solfor as forms_manual, Solfei as fecha_i, Solfef as fecha_f, Solpan as progAnexosPaq"
				. "  FROM ".$wcenimp."_000005 A "
				. " WHERE id = '".$identificador."' ";

		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		if($num > 0){

			$row         = mysql_fetch_assoc($err);
			$whis        = $row['historia'];
			$wing        = $row['ingreso'];
			$wfechai     = $row['fecha_i'];
			$wfechaf     = $row['fecha_f'];
			$paqsDefecto = explode("|", $row['paquetes'] );
			$paquetesAux = explode( "|", $row['forms_paquetes'] );
			
			$programasAnex     = $row['progAnexosPaq'];

			foreach( $paquetesAux as $i => $formulariosPaquete ){
				array_push( $paquetesdeformularios, $paquetesAux[$i] );
			}

			if( $row['forms_manual'] != '' ){
				array_push( $paquetesdeformularios, $row['forms_manual'] );
				array_push( $paqsDefecto, 0 );
			}
		}

		//SE CONSULTAN TODOS LOS FORMULARIOS FIRMADOS PARA EL PACIENTE, PARA SABER SI SE CREA EL PDF DEL PAQUETE
		$formularios_diligenciados = array();
		$query= "  SELECT Firpro "
			." 	     FROM ".$whcebasedato."_000036 "
			."      WHERE Firhis = '".$whis."' "
			."        AND Firing = '".$wing."'"
			."        AND Firfir = 'on' "
			."   GROUP BY Firpro ";
		$err = mysql_query($query,$conex) or die("Error query ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		if($num > 0){
			while( $row = mysql_fetch_array($err) )
				array_push( $formularios_diligenciados, $row[0] );
		}

		$sumaPaginas = 0;
		$wcadenaUnir = "";
		$usuario     = $_SESSION['user'];
		$pos         = strpos($usuario,"-");
		$usuario     = substr($usuario,$pos+1,strlen($usuario));
		$key         = $usuario;
		$wintitulo   = "Historia: ".$whis." Ingreso: ".$wing;
		$Hgraficas   = " |";
		$CLASE       = "I";
		$whtml       = 0;
		$empresa     = $whcebasedato;
		$countlista  = 0;
		
		if(($paquetesdeformularios[0]=="" || count($formularios_diligenciados)==0 ) && $programasAnex!="" )
		{
			$queryI  = "";
			$en  = "";
			// $wnombrePDF = $wemp_pmla."Solicitud_".$identificador."P".$paqsDefecto[$countlista];
			$wnombrePDF = $wemp_pmla."Solicitud_".$identificador;
			$mostrarObjectPdf = 'off';
			
			$respuesta = construirPDF($conex,$empresa,$wmovhos, $wemp_pmla,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,$whtml,$wnombrePDF, $wllevaTapa, $wllevaLogo, $mostrarObjectPdf, $wseparaFormularios,"","",$htmlProgramasAnexos);
			
			$aux = explode( "||||||", $respuesta );
			$paginas = trim( $aux[1] );
			if( isset($paginas) and $paginas*1 > 0 ){
				$sumaPaginas = $sumaPaginas + $paginas;
			}
		}
		else
		{
			foreach( $paquetesdeformularios as $listaForms ){
				
				if( $listaForms == '' )
					continue;
				$i=0;
				$paquetes = array();
				$paquetes = explode(",", $listaForms);
	
				//2014-01-13
				$arr_aux = array();
				$hayExternos = false;
				foreach($paquetes as $formu ){
					if(!preg_match('/EXT/i',$formu)){
						array_push( $arr_aux, $formu );
					}else{
						$hayExternos = true;
					}
				}

				$paquetes = $arr_aux;
				$en       = $listaForms;

				//Si ningun formulario del paquete esta en la lista de formularios diligenciados, no se debe generar un pdf para el paquete
				$interseccion = array_intersect($paquetes, $formularios_diligenciados);
				if( count($interseccion) == 0 && $hayExternos == false){
					$countlista++;
					continue;
				}
				$queryI  = "";

				if($wespecial != "TODAS" && $wespecial != "" )
				{
					$query = "DROP TABLE IF EXISTS TESPECIAL".$key.";";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$query = "CREATE TABLE if not exists TESPECIAL".$key." as ";
					$query .= " select Firusu as usuario from ".$empresa."_000036, ".$wmovhos."_000048  ";
					$query .= "   where Firhis = '".$whis."' ";
					$query .= " 	and Firing = '".$wing."' ";
					$query .= " 	and Firusu = Meduma ";
					$query .= " 	and Medesp = '".$wespecial."'";
					$query .= "  group by 1 ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

					$query = "CREATE UNIQUE INDEX claveE on TESPECIAL".$key." (usuario(8))";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}

				for ($i=0; $i < count($paquetes); $i++){
					if( $i>0 )
						$queryI.=" UNION ALL ";
					if($wespecial != "TODAS" && $wespecial != "" )
					{
						//						        		 0                                      1                          2        						       3                                          4                           5                          6                           7                          8                          9                         10                       11                                      12                       13                         14                         15                         16                         17							18							19
						$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".$paquetes[$i].".movdat,".$empresa."_000002.Detorp,".$empresa."_".$paquetes[$i].".fecha_data,".$empresa."_".$paquetes[$i].".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$paquetes[$i].".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir, ".$empresa."_000002.Detimc, ".$empresa."_000002.Detccu from ".$empresa."_".$paquetes[$i].",".$empresa."_000002,".$empresa."_000001,TESPECIAL".$key;
						$queryI .= " where ".$empresa."_".$paquetes[$i].".movpro='".$paquetes[$i]."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".movhis='".$whis."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".moving='".$wing."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".fecha_data between '".$wfechai."' and '".$wfechaf."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".movpro=".$empresa."_000002.detpro ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".movcon = ".$empresa."_000002.detcon ";
						//$queryI .= "   and ".$empresa."_000002.detest='on' ";
						$queryI .= "   and ".$empresa."_000002.detvim IN ('A','I') ";
						$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' ";
						$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".movusu = TESPECIAL".$key.".usuario ";
					}else{
						//						        		 0                                      1                          2        						       3                                          4                           5                          6                           7                          8                          9                         10                       11                                      12                       13                         14                         15                         16                         17							18
						$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".$paquetes[$i].".movdat,".$empresa."_000002.Detorp,".$empresa."_".$paquetes[$i].".fecha_data,".$empresa."_".$paquetes[$i].".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$paquetes[$i].".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir, ".$empresa."_000002.Detimc from ".$empresa."_".$paquetes[$i].",".$empresa."_000002,".$empresa."_000001 ";
						$queryI .= " where ".$empresa."_".$paquetes[$i].".movpro='".$paquetes[$i]."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".movhis='".$whis."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".moving='".$wing."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".fecha_data between '".$wfechai."' and '".$wfechaf."' ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".movpro=".$empresa."_000002.detpro ";
						$queryI .= "   and ".$empresa."_".$paquetes[$i].".movcon = ".$empresa."_000002.detcon ";
						//$queryI .= "   and ".$empresa."_000002.detest='on' ";
						$queryI .= "   and ".$empresa."_000002.detvim IN ('A','I') ";
						$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' ";
						$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro ";
					}
				}
				
				// $wnombrePDF = $wemp_pmla."Solicitud_".$identificador."P".$paqsDefecto[$countlista];
				$wnombrePDF = $wemp_pmla."Solicitud_".$identificador;
				$wcadenaUnir.= $wnombrePDF.".pdf ";
	
				$mostrarObjectPdf = 'off';
	
				$respuesta = construirPDF($conex,$empresa,$wmovhos, $wemp_pmla, $queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,$whtml,$wnombrePDF, $wllevaTapa, $wllevaLogo, $mostrarObjectPdf, $wseparaFormularios,"","",$htmlProgramasAnexos);

				$aux = explode( "||||||", $respuesta );
				$paginas = trim( $aux[1] );
				if( isset($paginas) and $paginas*1 > 0 ){
					$sumaPaginas = $sumaPaginas + $paginas;
				}
				$countlista++;
			}
		}

		$query = "UPDATE {$wcenimp}_000005
					 SET Solnuh = '{$sumaPaginas}', Solgen='off'
				   WHERE id = '$identificador'";
		$rsUp  = mysql_query( $query, $conex );
		echo "{$sumaPaginas}|";

		$query = "DROP TABLE IF EXISTS TESPECIAL".$key.";";
		$err = mysql_query($query,$conex);
	}

	function consultarCcoPaciente( $whis, $wing ){

		global $conex, $wmovhos;

		$respuesta                   = array();
		$respuesta['codigoCco']      = "";
		$respuesta['solicitaCenimp'] = "";

		$query = " SELECT Ubisac ccoActual
					 FROM {$wmovhos}_000018
					WHERE ubihis = '{$whis}'
					  AND ubiing = '{$wing}'";

		$rs    = mysql_query( $query, $conex );
		$row   = mysql_fetch_array( $rs );

		$respuesta['codigoCco'] =  $row['ccoActual'];

		if( trim( $respuesta['codigoCco'] ) != "" ){
			$query = "SELECT Ccocim solicitaCenimp
						 FROM {$wmovhos}_000011
						WHERE Ccocod = '{$respuesta['codigoCco']}' ";
			$rs    = mysql_query( $query, $conex );
			$row   = mysql_fetch_array( $rs );

			$respuesta['solicitaCenimp'] =  $row['solicitaCenimp'];
		}

		return( $respuesta );
	}

	function consultarUltimasSolicitudes($wfecha='', $whis='', $wing='', $wcedula=''){

		global $conex;
		global $wcenimp;
		global $wmovhos;
		global $wemp_pmla;
		global $whcebasedato;
		global $wuser;

		if( $wfecha == '' )
			$wfecha = date('Y-m-d');

		$where = " 	A.fecha_data = '".$wfecha."'";

		if( $whis != "" ){
			$where = " Solhis = '".$whis."'";
			if( $wing != '' ) $where.=" AND Soling = '".$wing."'";
		}

		if( $wcedula != '' )
			$where = " Pacced = '".$wcedula."'";

		$wauxrol = consultarRol();
		if(isset( $wauxrol['rol'] ) )
		$wrol    = $wauxrol['rol'];

		$query = " SELECT A.id as codigo_solicitud, A.Fecha_data as fecha, A.Hora_data as hora, Solhis as historia, Soling as ingreso, A.Seguridad as cod_user,"
				."    	  CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2) AS paciente, oritid as tipodoc, oriced as doc, ubihac as habitacion, ubiald as alta, "
				."        Solnuf as numero_formularios, Solnuh as paginas, Solter as terminado, B.fecha_data as fecha_imp, B.hora_data as hora_imp, Logdes as email, Solest estado,
						  L.fecha_data as fecha_log, L.Hora_data as hora_log "
				. "  FROM root_000036, root_000037 , ".$wmovhos."_000018, ".$wcenimp."_000005 A LEFT JOIN ".$wcenimp."_000006 B ON (A.id = Impsol) LEFT JOIN cenimp_000007 L ON (Logcdu=A.id)"
				. " WHERE ".$where
				."    AND orihis = Solhis "
				." 	  AND oriced = pacced "
				."    AND oritid = pactid "
				." 	  AND oriori = '".$wemp_pmla."'"
				."    AND ubihis = Solhis "
				."    AND ubiing = Soling "
				."    GROUP BY A.id, Solhis, Soling, A.Seguridad "
				."    ORDER BY A.Fecha_data desc, A.Hora_data desc ";

		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		$arr_datos    = array();//2013-12-10
		$arr_usuarios = array(); //2013-12-10

		if( $num > 0 ){
			while ( $row2 = mysql_fetch_assoc($err) ){

				$row2['ok']       = "off";
				$row2['cod_user'] = str_replace("C-", "", $row2['cod_user']);
				$row2['cod_user'] = str_replace("c-", "", $row2['cod_user']);
				if( in_array( "'".$row2['cod_user']."'", $arr_usuarios ) == false ) //Si el codigo del usuario no existe en arr_usuarios se guarda
					array_push( $arr_usuarios, "'".$row2['cod_user']."'" );

				array_push ( $arr_datos, $row2 ); //Se guarda la fila en arr_datos
			}


			$and_rol = "";
			if( $wrol != "" ) $and_rol = "	  AND usurol = '".$wrol."'";

			$chain_usuarios = implode(",", $arr_usuarios );
			$q_usuarios = "SELECT  rolcod as cod_rol, roldes as nom_rol, codigo as cod_usu, descripcion as nom_usu
								 FROM ".$whcebasedato."_000019, ".$whcebasedato."_000020 U, usuarios
								WHERE codigo IN (".$chain_usuarios.")
								  AND usurol = rolcod
								  AND codigo = usucod "
								  .$and_rol;
			$err1 = mysql_query($q_usuarios,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuarios." - ".mysql_error());
			$num1 = mysql_num_rows($err1);
			if( $num1 > 0 ){
				while( $row1 = mysql_fetch_assoc($err1) ){
					foreach( $arr_datos as $pos=>&$dato ){
						if( $dato['cod_user'] == $row1['cod_usu'] ){
							$dato['cod_usuario']  = $row1['cod_usu'];
							$dato['usuario']  = $row1['nom_usu'];
							$dato['ok'] = "on";
						}
					}
				}
			}
		}
		//---2013-12-10

		$mensaje="";
		if( $whis == '')
			$mensaje="SOLICITUDES DEL DIA ".$wfecha;
		else{
			$mensaje="SOLICITUDES DE LA HISTORIA ".$whis;
			if( $wing != '' ) $mensaje.="-".$wing;
		}

		echo "<table align='center' id='tabla_solicitudes' width='96%' >";
		echo "<tr class='encabezadotabla'><td align='center' colspan=13><font size=4><b>".$mensaje."</b></font></td></tr>";
		echo "<tr class='fila1'><td align='center' colspan=13>";

		echo "<table>";
		echo "<tr><td class='fila2'><b>Documento</b></td><td class='fila2'><input type='text' class='solonumeros' id='documento_consulta' value='".$wcedula."' /></td></tr>";
		echo "<tr><td class='fila2'><b>Historia</b></td><td class='fila2'><input type='text' class='solonumeros' id='historia_consulta' value='".$whis."' /></td></tr>";
		echo "<tr><td class='fila2'><b>Ingreso</b></td><td class='fila2'><input type='text' class='solonumeros' id='ingreso_consulta' value='".$wing."' /></td></tr>";
		echo "<tr><td class='fila2'><b>Fecha</b></td><td class='fila2'><input type='text' id='fecha_consulta' value='".$wfecha."' /></td></tr>";
		echo "<tr><td class='fila2' colspan=2 align='center'><input type='button' id='btn_consultar_fechas' value='Consultar' onclick='consultarSolicitudesAnt()'/></td></tr>";
		echo "</table>";
		echo "<span style='float:left;'><select id='contenedorFiltroUsuarios' onchange='filtrarUsuario()'><option value=''>Filtrar Usuario</option></select></span>";
		echo "<span style='float:right;'>Ver Anulados: <input type='checkbox' onclick='verOcultarAnulados(this)' /></span>";
		echo "</td></tr>";

		if($num > 0){
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center' rowspan=2>Codigo</td>";
			echo "<td align='center' colspan=2>Solicitud</td>";
			echo "<td align='center' rowspan=2>Historia<br>Ingreso</td>";
			echo "<td align='center' rowspan=2>Documento</td>";
			echo "<td align='center' rowspan=2>Paciente</td>";
			echo "<td align='center' rowspan=2>Usuario</td>";
			echo "<td align='center' rowspan=2>Habitación</td>";
			echo "<td align='center' rowspan=2>Páginas</td>";
			echo "<td align='center' colspan=2>Impreso/Descargado/Enviado</td>";
			echo "<td align='center' rowspan=2>Descarga/Email enviado</td>";
			echo "<td align='center' rowspan=2>Anular</td>";
			echo "</tr>";
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center'>Fecha</td>";
			echo "<td align='center'>Hora</td>";
			echo "<td align='center'>Fecha</td>";
			echo "<td align='center'>Hora</td>";
			echo "</tr>";

			$arrUsuarios    = array();
			$arrdesUsuarios = array();
			$ind            = 0;
			$indice_generar = 0;
			$class          ="fila1";
			//2013-12-10
			foreach( $arr_datos as $row ){

				if( $row['ok'] != "on" )
					continue;

				$fecha_mostrar = $row['fecha_imp'];
				$hora_mostrar  = $row['hora_imp'];

				if( in_array( $row['cod_usuario'], $arrUsuarios ) == false ){
					array_push( $arrUsuarios, $row['cod_usuario'] );
					array_push( $arrdesUsuarios, $row['usuario'] );
				}

				( ($ind % 2) == 0 ) ? $class='fila1' : $class='fila2';
				$pendiente = false;
				$wemail    = "";
				$desc      = explode(" ", $row['email'] );
				$desc      = array_reverse( $desc );

				foreach( $desc as $des ){
					if( preg_match('|^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$|i', $des) ){
						$wemail        = $des;
						$fecha_mostrar = $row['fecha_log'];
						$hora_mostrar  = $row['hora_log'];
						break;
					}
				}

				if($row['email'] == "DESCARGADO"){
					$wemail        = 'DESCARGADO';
					$fecha_mostrar = $row['fecha_log'];
					$hora_mostrar  = $row['hora_log'];
				}

				if( $fecha_mostrar == '' && $wemail == ''){
					$class     ='fondorojo';
					$pendiente = true;
				}
				$claseanulado = "";
				if($row['estado']=='off'){
					$claseanulado = "clsanulado";
				}
				echo "<tr class='".$class." ".$claseanulado." filtrouser' usuario='".$row['cod_usuario']."'>";
				echo "<td align='center'>".$row['codigo_solicitud']."</td>";
				echo "<td>".$row['fecha']."</td>";
				echo "<td>".$row['hora']."</td>";
				echo "<td>".$row['historia']."-".$row['ingreso']."</td>";
				echo "<td nowrap='nowrap'>".$row['tipodoc']." ".$row['doc']."</td>";
				echo "<td>".$row['paciente']."</td>";
				echo "<td>".$row['usuario']."</td>";
				echo "<td>".$row['habitacion']."</td>";
				echo "<td align='center'>".$row['paginas']."</td>";
				echo "<td>".$fecha_mostrar."</td>";
				echo "<td>".$hora_mostrar."</td>";
				echo "<td>".$wemail."</td>";
				echo "<td align='center'>";
				if($row['estado']=='on' && $row['terminado']=='off'){
					if( $pendiente==true && $wuser == $row['cod_usuario'] ){
						echo "<input type='checkbox' name='chk_anular' historia='".$row['historia']."' ingreso='".$row['ingreso']."' origen='consultas' onclick='anularSolicitud( this,\"".$row['codigo_solicitud']."\" ); ' />";
					}else
						echo "&nbsp;";
				}else if($row['terminado']=='on'){
					echo "&nbsp;";
				}else{
					echo "ANULADO";
				}
				echo "</td>";
				echo "</tr>";
				$ind++;
			}
			echo "</table>";
			echo "<input type='hidden' id='hideCodUsuarios' value='".json_encode($arrUsuarios)."' />";
			echo "<input type='hidden' id='hideDesUsuarios' value='".json_encode($arrdesUsuarios)."' />";
		}else{
				echo "</table>";
				echo "<br>No hay solicitudes para mostrar";
		}
		echo  '<script>
					$("#fecha_consulta").datepicker({
					 showOn: "button",
					 buttonImage: "../../images/medical/root/calendar.gif",
					 buttonImageOnly: true,
					 maxDate:"'.date('Y-m-d').'"
					});
					$(".clsanulado").hide();
					$(".solonumeros").keyup(function(){
						if ($(this).val() !="")
							$(this).val($(this).val().replace(/[^0-9]/g, ""));
					});
					//Construir opciones del select filtro usuarios
					var codUsuarios = $("#hideCodUsuarios").val();
					var desUsuarios = $("#hideDesUsuarios").val();
					codUsuarios = eval(codUsuarios);
					desUsuarios = eval(desUsuarios);
					var i=0;
					var opciones = "<option value=\"\">Filtrar Usuario</option>";
					for(i in codUsuarios){
						opciones+="<option value=\""+codUsuarios[i]+"\">"+desUsuarios[i]+"</option>";
					}
					$("#contenedorFiltroUsuarios").html(opciones);

					function verOcultarAnulados(obj){
						obj = jQuery(obj);
						if(obj.is(":checked") )
							$(".clsanulado").show();
						else
							$(".clsanulado").hide();
					}

					function filtrarUsuario(){
						var usuario = $("#contenedorFiltroUsuarios").val();
						if( usuario == "" ){
							$(".filtrouser").show();
							return;
						}
						$(".filtrouser").each(function(){
							if( $(this).attr("usuario") == usuario )
								$(this).show();
							else
								$(this).hide();
						});
					}
			   </script>';
	}

	function enviarEmail( $wemail_destino, $wnombre_destino, $wasunto, $wmensaje, $wnombrepdf, $widentificador, $wmod, $wcorreopmla ){

		include_once("root/PHPMailer/class.phpmailer.php");
		global $wuser, $wcenimp, $conex, $wmovhos, $wemp_pmla;
		if( $wemail_destino == '' || $wnombre_destino == '' || $wnombrepdf == '' ){
			echo "NO puede enviarse el email";
			return;
		}

		$wnombrepdf  = $wemp_pmla."".$wnombrepdf;
		$wcorreopmla = explode("--", $wcorreopmla );
		$wpassword   = $wcorreopmla[1];
		$wcorreopmla = $wcorreopmla[0];

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->Username = $wcorreopmla;
		$mail->Password = $wpassword;

		$mail->From = $wcorreopmla;
		$mail->FromName = "Clínica las Américas";
		$mail->Subject = $wasunto; 	//O un asunto fijo => Historia Clinica del paciente xxx

		$altbody = 	"Cordial saludo, \n\nLa Clinica Las Americas se permite enviarle la historia clinica del paciente ".$wnombre_destino."."
					."Le recordamos que debe tener instalado algun lector de archivos PDF, tal como Adobe Reader. "
					."Si no cuenta con alguno puede descargarlo en la siguiente direccion http://get.adobe.com/es/reader/ "
					."\n\nLa informacion adjunta es confidencial segun los principios de la proteccion de datos de la ley 41 de 2002.  ";

		$msghtml = 	"Cordial saludo, <br><br>La Clinica Las Americas se permite enviarle la historia clinica del paciente ".$wnombre_destino."."
					."Le recordamos que debe tener instalado algun lector de archivos PDF, tal como Adobe Reader. "
					."Si no cuenta con alguno puede descargarlo en la siguiente direccion http://get.adobe.com/es/reader/ "
					."<br><br>La informacion adjunta es confidencial segun los principios de la proteccion de datos de la ley 41 de 2002.  ";

		$mail->AltBody = $altbody;
		$mail->MsgHTML( $msghtml );
		$mail->AddAttachment("../reportes/cenimp/".$wnombrepdf.".pdf", "HCE_pmla.pdf");
		$mail->AddAddress($wemail_destino, $wnombre_destino);
		$mail->IsHTML(true);

		if(!$mail->Send()) {
			echo "Error";
		} else {
			$accion 	    = "envio email";
			$tabla		    = "";
			$descripcion    = "Se envio un correo Electronico a: {$wemail_destino}";
			$identificacion = $widentificador;
			insertLog( $conex, $wcenimp, $wuser, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error, $wmod );
			echo "OK";
		}
	}

	function imprimirArbolCompleto(){
        global $conex;
        global $wbasedato;
        global $whcebasedato;
        global $caracteres, $caracteres2;
               $formularios = "";
               $padreActual = "";

        $q = " SELECT Precod ,Predes, prenod, '' as Encpro
                 FROM {$whcebasedato}_000009
                WHERE prenod = 'on'
                  AND preest = 'on'
                UNION ALL
               SELECT Precod,Predes,prenod, Encpro
                 FROM {$whcebasedato}_000009, {$whcebasedato}_000001
                WHERE prenod = 'off'
                  AND mid(Preurl,1,1) = 'F'
                  AND Preurl = CONCAT( 'F=', Encpro )
                  AND Preest = 'on'
                ORDER BY 1 ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        $k=round(($num/4),0);

        //=======================================================================================================================================
        //Lleno una matriz tal como se debe de mostrar, es decir se debe mostrar por columnas no por filas
        ///=======================================================================================================================================
        for ($j=1; $j<=4; $j++)
        {
            for ($i=1; $i<=$k+2; $i++)
            {
                $row = mysql_fetch_array($res);

                $matriz[$i][$j][1]=$row[0];
                $matriz[$i][$j][2]=$row[1];
                $matriz[$i][$j][3]=$row[2];
                $matriz[$i][$j][4]=$row[3];
            }
        }
		
		
		$progAnex = consultarScriptsParaArbolCompleto($conex,$whcebasedato);
		
		if(count($progAnex)>0)
		{
			$k=$k+2+count($progAnex);
			
			
			for ($j=1; $j<=4; $j++)
			{
				for ($m=$i; $m<=$k; $m++)
				{
					$rowProgAnex = array_shift($progAnex);
					
					$matriz[$m][$j][1]=$rowProgAnex[0];
					$matriz[$m][$j][2]=$rowProgAnex[1];
					$matriz[$m][$j][3]=$rowProgAnex[2];
					$matriz[$m][$j][4]=$rowProgAnex[3];
				}
			}
		}	
		
       $formularios .= "<div id='div_arbol_completo'>";
       $formularios .= "<table style='border: 1px solid blue' id='tabla_arbol_completo'>";
       $formularios .= "<tr class=fila1><td align=center colspan=8><b><font size=5>ARBOL DE FORMULARIOS HCE</font></b></td></tr>";

       $formularios .= "<tr class=encabezadoTabla>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opción</th>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opción</th>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opción</th>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opción</th>";
       $formularios .= "</tr>";

       $wini = 'on';

        if ($wini=="on")
        {
            $wcolor="";
            $wini="off";
        }
        else
            $wcolor="FFFF99";

       $fila_color = false;
        for ($i=1; $i<=($k); $i++)
        {
            $formularios .= "<tr class=fila2>";
            for ($j=1; $j<=4; $j++)
            {
                if ($matriz[$i][$j][3]=='on')  //Si es un nodo
                {
                    //Sel.  Graba   Imp.
                    $filaa   = "";
                    $colspan = "2";
                    if( strlen( $matriz[$i][$j][1] ) > 1 ){
                        $filaa.="<td class='botona' width=24><input class='nodo_arbol_completo' esPadre='on' codigoRelacion='{$matriz[$i][$j][1]}' type='checkbox' value='".$matriz[$i][$j][1]."' onclick='checkearColumna( this )' /></td>";
                        $colspan = "1";
                    }
                    $nombre      =  $matriz[$i][$j][2];
                    $formularios .= $filaa."<td class='botona' colspan='{$colspan}'><b>".$nombre."</b></td>";
                }
                else
                {
                    $nombre      =  $matriz[$i][$j][2];
                    ( is_int($j/2) ) ? $wclass = "fila1" : $wclass="fila2";
                    $formularios .= "<td class='{$wclass}'><input class='formulario_arbol_impresion' esPadre='off' codigoRelacion='{$matriz[$i][$j][1]}' type='checkbox' value='".$matriz[$i][$j][4]."' onclick='chequearFormulario( this );' /></td>";
                    $formularios .= "<td class='{$wclass}'>". $nombre."</td>";
                }
            }
            $formularios .= "</tr>";
        }
        $formularios .= "</table>";
        $formularios .= "</div>";
        return( $formularios );
	}
//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "consultarParaPaciente"){

		$weditar    = $_REQUEST['weditar'];
		$whis       = $_REQUEST['whis'];
		$wing       = $_REQUEST['wing'];
		$wmodalidad = $_REQUEST['wmodal'];
		$wcliame    = $_REQUEST['wcliame'];
		$wusuCenimp = $_REQUEST['wusuCenimp'];
		$wempresa   = $_REQUEST['wempresa'];

		if( $weditar == "no" ){
			$tieneSolicitud = tieneSolicitudPendiente( $whis, $wing, $wmodal );
			$tieneSolicitud = $tieneSolicitud['existe'];
			( $tieneSolicitud ) ? $weditar = "si" : $weditar = "no";
		}

		if( $weditar == "si" )
			consultarDatosSolicitud( $whis, $wing, $wmodal );

		$formulariosDelPaciente   = existenFormulariosPaciente( $whis, $wing );
		( count( $formulariosDelPaciente ) > 0 ) ? $tieneFormulariosImprimir = true : $tieneFormulariosImprimir = false;

		 /*si es modalidad de facturación se muestran los formularios diligenciados a modo informativo,
		  ya que solo se permite seleccionar formularios por paquetes. se hace necesario mostrarle al usuario cuales formularios tiene disponibles
		  el paciente*/
		$formulariosHce  = todoslosFormularios();

		$divFormularios  = "<div name='dialogo' historia='{$whis}' ingreso='{$wing}'>";
		$divFormularios .= "<div align='center'><span class='subtituloPagina2' style='font-size:16px; cursor:pointer;' onclick='mostrarocultarDiv(\"tbl_diligenciados\")'>Formularios Diligenciados:</span></div>";
				$divFormularios .= "<table id='tbl_diligenciados' style='font-size:14px;' historia='{$whis}' ingreso='{$wing}' >";
		$divFormularios .= "<tr class='encabezadotabla'><td>SE IMPRIME</td><td align='left'>DESCRIPCI&Oacute;N</td></tr>";
		$formulariosDiligenciados = "";
		foreach( $formulariosDelPaciente as $wtipoFormularios=>$formularios ){ //2014-17-01 este for recorre los tipos de formularios, por ahora: formulariosInternos, formulariosExternos
			$formulariosDiligenciados .= " {$wtipoFormularios}='";
			$j = 0;
			foreach ($formularios as $j => $codigo ){
				( is_int($j/2) ) ? $wclase = "fila1" : $wclase = "fila2";
				( $j == 0 ) ? $formulariosDiligenciados .= $codigo : $formulariosDiligenciados .= ",".$codigo;
				$divFormularios .= "<tr class='{$wclase}'><td align='center'><input type='checkbox' disabled='disabled' name='elegidoParaImprimir' historia='{$whis}' ingreso='{$wing}' codigo='{$codigo}' ></td><td align='left'>{$formulariosHce[$codigo]}</td></tr>";
			}
			$formulariosDiligenciados .= "' ";
		}
		
		// programas anexos
		$cadenaProgramasAnexos = "";
		$programasAnexos = consultarScripts($conex,$whcebasedato,$whis,$wing);
		
		if(count($programasAnexos)>0)
		{
			for($i=0;$i<count($programasAnexos);$i++)
			{
				if($programasAnexos[$i][0]!="")
				{
					if ($wclase=='fila2')
							$wclase = "fila1";
						else
							$wclase = "fila2";
					$cadenaProgramasAnexos .= $programasAnexos[$i][0]."|";
					$cadProgramasAnexos .= str_replace(" ","_",$programasAnexos[$i][1])."|";
					
					$divFormularios .= "<tr class='".$wclase."'><td align='center'><input type='checkbox' disabled='disabled' name='elegidoParaImprimir' historia='".$whis."' ingreso='".$wing."' codigo='".str_replace(" ", "_",$programasAnexos[$i][1])."' progAnex='".$programasAnexos[$i][0]."' ></td><td align='left'>".$programasAnexos[$i][1]."</td></tr>";
				}
			}
			
		}
		
		$divFormularios .= "</table>";
		$divFormularios .= "</div><br>";
		$contenedorFormularios = "<input type='hidden' este='sii' class='contenedor_formularios' historia='{$whis}'  ingreso='{$wing}' ".$formulariosDiligenciados." formulariosElegidos='' cadAnexos='".$cadProgramasAnexos."'>";

		// if( existePaciente( $whis, $wing ) AND $tieneFormulariosImprimir ){
		if( (existePaciente( $whis, $wing ) AND $tieneFormulariosImprimir) || (existePaciente( $whis, $wing ) AND $cadenaProgramasAnexos!="")  ){

			( trim( $wcco ) == "" )   ? $wcco   = '%' : $wcco   = $wcco;
			( trim( $wgrupo ) == "" ) ? $wgrupo = '%' : $wgrupo = $wgrupo;

			$datosPaciente = mostrarEncabezadoPaciente( $whis, $wing, $wempresa );

			echo $datosPaciente['encabezadoPaciente']."<br><br>";
			echo mostrarPaquetes( $wmodal, $wcco, $wgrupo );
			if( $wfact == "off"){
				echo "<br>";
				echo mostrarArbolImpresion($_REQUEST['whis'], $_REQUEST['wing'] );
			}else{
				// acá va el botón que muestra los formularios del paciente.
				echo $divFormularios;
			}
			echo $contenedorFormularios;

			( trim( $datosPaciente['email'] ) == "" ) ? $email="" : $email = $datosPaciente['email'];

			echo "<input type='hidden' name ='cadenaProgramasAnexos' id='cadenaProgramasAnexos'  value='".$cadenaProgramasAnexos."'>";
			
			echo "<center><div style='width:400px; display:none;' id='div_formularioEnvio' align='center'>";
				echo "<table align='center' width='100%'>";
				echo "<tr>";
				echo "<td colspan=2 align='center'><font size=4><b>El documento será enviado por correo</b><br>Ingrese los datos para continuar</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td class='encabezadotabla' width='30%'>Nombre Paciente:</td>";
				echo "<td class='fila2' width='70%'><input type='text' id='nombre_paciente' style='width:99%;' value='".$datosPaciente['nombre']."' disabled /></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td class='encabezadotabla' width='30%'>Email Responsable:</td>";
				echo "<td class='fila2' width='70%'><input type='text' id='emailpaciente' style='width:99%;' value='".$email."' placeholder='paciente@email.com' /></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td class='encabezadotabla' width='30%'>Asunto:</td>";
				echo "<td class='fila2' width='70%'><input type='text' id='emailasunto' style='width:99%;' value='' placeholder='Asunto' /></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td class='encabezadotabla' width='30%'>Mensaje adicional:</td>";
				echo "<td class='fila2' width='70%'><textarea cols=31 rows=4 id='emailmensaje' placeholder='Opcional' ></textarea></td>";
				echo "</tr>";
				echo "</table>";
				echo "<br><input type='button' value='Enviar PDF' accion='enviarPdf' onclick='enviarPdf()' style='display:none;' />";
			echo "</div></center>";
		}else{
			echo "NO";
		}
		return;
	}

	if( $action == 'guardarSolicitud' ){

		$weditar       = $_REQUEST['weditar'];
		$identificador = "";
		$agregarFecha  = false;
		$fechaConsultada = "";

		if( $weditar == "no" ){
			$datosSolicitud = tieneSolicitudPendiente( $_REQUEST['historia'], $wing = $_REQUEST['ingreso'], $wmodalidad );
			$tieneSolicitud = $datosSolicitud['existe'];
			( $tieneSolicitud ) ? $weditar = "si" : $weditar = "no";
			( $tieneSolicitud ) ? $identificador = $datosSolicitud['id'] : $identificador = "";
		}else{
			$identificador = $_REQUEST['widenti'];
		}

		$datos           =  guardarSolicitudImpresion( $_REQUEST['historia'], $_REQUEST['ingreso'], $_REQUEST['datos'], @$_REQUEST['monitor'], $wmodalidad, $_REQUEST['fecha_i'], $_REQUEST['fecha_f'], $weditar, $_REQUEST['fecIngreso'], $_REQUEST['fecEgreso'], $_REQUEST['wcco'], $identificador, $wSolicitaCenimp, $wenviaEmail, $_REQUEST['wimpresionDirecta'],$htmlProgramasAnexos );
		$identificador   = $datos['identificador'];
		$wSolicitaCenimp = $datos['wSolicitaCenimp'];
		$numFormularios  = $datos['wnumformularios'];
		$cadenaProgramasAnexos  = $datos['wCadProgramasAnexos'];

		if( $_REQUEST['fecha_i'] == $_REQUEST['fecha_f'] ){
			$agregarFecha    = true;
			$fechaConsultada = $_REQUEST['fecha_i'];
		}

		// if( $numFormularios > 0 )
		if( $numFormularios > 0 || $cadenaProgramasAnexos!="")
			generarArchivosHtml_Pdf( $identificador, $_REQUEST['wlogo'], $_REQUEST['wtapa'],$htmlProgramasAnexos );
		if( $identificador ){
			echo "OK|".$wSolicitaCenimp."|".$identificador;
		}
		return;
	}

	if( $action == 'buscarHistoria' ){

		$resultado = buscarHistoria( $_REQUEST['wtdoc'], $_REQUEST['wndoc'] );
		$i         = 1;
		$ingresos  = "<select id='wing'>";
		$ingresos  .= "<option value=''>---</option>";
			foreach ( $resultado['ingresos'] as $keyIngreso => $value) {
				$ingresos .= "<option value='{$keyIngreso}'>{$keyIngreso}</option>";
				$i++;
			}
		$ingresos .= "</select>";

		$data = array( 'historia' => $resultado['historia'], 'ingresos'=>$ingresos );
		echo json_encode( $data );
		return;
	}

	if( $action == "consultarPacientesCco" ){
		$wdatos           = str_replace("\\", "", $_REQUEST['wnombreCco'] );
		$arrayCco   	  = json_decode( $wdatos );
		$pacientes = buscarPacientesActivosCco( $wcco, $arrayCco, $wmodalidad );
		( $pacientes == "" ) ? $error = "1" : $error = "0";
		$data = array( "listadoPacientes"=>$pacientes, "error"=>$error );
		echo json_encode( $data );
		return;
	}

	if( $action == "buscarEntidadesEnGrupo" ){

		$entidades = buscarEntidadesGrupo( $_REQUEST['wcodgru'] );
		($entidades == "") ? $error = 1 : $error = 0;
		$data = array( "entidades"=>$entidades, "error"=>$error );
		echo json_encode( $data );
		return;
	}

	if( $action == "buscarPacientesFacturacion" ){

		$wdatos           = str_replace("\\", "", $_REQUEST['wnomsEntis'] );
		$arrayEntidades   = json_decode( $wdatos );
		$listadoPacientes = buscarPacientesFacturacion( @$_REQUEST['wmovhos'], $_REQUEST['whcebasedato'], $_REQUEST['wentidades'], $_REQUEST['westPac'], $_REQUEST['wfecini'], $_REQUEST['wfecfin'], $arrayEntidades, $wmodalidad );

		if( $listadoPacientes == "" ){
			echo "NO";
			return;
		}

		( trim( $wgrupo ) == "---" ) ? $wgrupo = '%' : $wgrupo = $wgrupo;
		$paquetes              = mostrarPaquetes( $wmodalidad, "*", $wgrupo );
		$listadoPacientesFinal = $paquetes;
		$listadoPacientesFinal .= "<br><div align='center' style='width: 90%; cursor:pointer;' onclick='mostrarModalFormularios()'><span class='subtituloPagina2'><font size='3px'> Adicionar Formularios </font></span><img id='img_esperar_plan' src='../../images/medical/ips/plus.gif' /></div>";
		$listadoPacientesFinal .= "<br>".$listadoPacientes;


		echo $listadoPacientesFinal;
		return;
	}

	if( $action == "anularSolicitud" ){

		$query = "SELECT Solter, Solest FROM {$_REQUEST['wcenimp']}_000005 WHERE id='".$_REQUEST['wsolicitud']."'";
		$err   = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num2  = mysql_num_rows($err);

		if($num2 > 0){
			$rowx = mysql_fetch_array($err);
			if($rowx[0] == 'on' || $rowx[1] == 'off'){
				echo "La solicitud ya fue impresa/descargada o anulada";
				return;
			}
		}

		$query = "UPDATE {$_REQUEST['wcenimp']}_000005
					 SET solest = 'off'
				   WHERE id='".$_REQUEST['wsolicitud']."'";

		$rs    = mysql_query( $query, $conex );
		$num   = mysql_affected_rows();

		if( $num*1 > 0 ){
			$accion 	    = "update";
			$tabla		    = "000005";
			$descripcion    = "Anulacion";
			$identificacion = $whis."-".$wing;
			insertLog( $conex, $wcenimp, $user, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error, $wmod );
			echo "OK";
		}else
			echo "La solicitud no pudo ser anulada";
		return;
	}

	if( $action == "enviarCorreo" ){
		enviarEmail( $wemail_destino, $wnombre_destino, $wasunto, $wmensaje, $wnombrepdf, $widentificador, $wmod, $wcorreopmla );
		return;
	}

	if( $action == 'consultarSolicitudes' ){
		consultarUltimasSolicitudes(@$_REQUEST['wfechac'],@$_REQUEST['whistoriac'],@$_REQUEST['wingresoc'],@$_REQUEST['wdoc']);
		return;
	}

	if( $action == 'buscarIngresos' ){

		$wsoloActivos = $_REQUEST['wsoloActivos'];
		$wmovhos      = $_REQUEST['wmovhos'];
		$whis         = $_REQUEST['whis'];
		$wenviaEmail  = $_REQUEST['wenviaEmail'];
		$wusuCenimp   = $_REQUEST['wusuCenimp'];
		$query        = "";
		$error        = 0;

		if( ( $wsoloActivos == "on" and trim( $wusuCenimp ) =="on") or ( $wsoloActivos == "on" and trim( $wusuCenimp ) =="off" and $wenviaEmail=="on" ) ){
			$query = "SELECT ubiing ingreso
						FROM {$wmovhos}_000018
					   WHERE ubihis  = '{$whis}'
					     AND ubiald != 'on'";
		}else{
			$query = " SELECT Inging ingreso
						 FROM {$wmovhos}_000016 a
					    WHERE Inghis = '{$whis}'
					    ORDER BY ingreso desc";
		}
		$rs    = mysql_query( $query, $conex ) or die( mysql_error() );
		$num   = mysql_num_rows( $rs );

		if( $num*1 > 0 ){

			if( $num*1 > 1){
				$elemento  = "<select id='wing' style='width:100%'>";
				$elemento .= "<option value=''>--</option>";
			}

			while ( $row = mysql_fetch_array( $rs ) ){

				if( $num*1 == 1 ){ //acá lo que se retorna es un input lleno
					$elemento = "<input type='text' id='wing' value='{$row['ingreso']}' />";
				}

				if( $num*1 > 1 ){//aca se retorna un select con todos los posibles ingresos ordenados desde el mas reciente
					$elemento .= "<option value='{$row['ingreso']}'>{$row['ingreso']}</option>";
				}
			}

			if( $num*1 > 1){
				$elemento  .= "</select>";
			}

		}else{
			$error = 1;
		}

		$data = array( 'elemento'=>$elemento, 'error'=> $error );
		echo json_encode( $data );
		return;
	}
}
//FIN*LLAMADOS*AJAX******************************************************************

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//

include_once("root/comun.php");

$wcenimp             = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenimp");
$whcebasedato        = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce");
$wmovhos             = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos");
$wbasedato           = $wmovhos;
$wcliame             = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliameCenimp");
$wcorreopmla         = consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailpmla");
$buscaXcedulaActivos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cedulaPacientesActivos");

$wauxrol    = consultarRol();
$wrol       = $wauxrol['rol'];
$wusuCenimp = $wauxrol['tieneCenimp'];// verifica si el usuario logueado pertenece a un centro de costos que no tiene cenimp, para permitirle consultar historias sin importar su estado.

$weditar               = "no";
$modMon                = array();
$modMon                = consultarModalidadMonitor( $wrol );
$wmodalidad            = $modMon[ 'modalidad' ];
$wincluyeLogo          = $modMon[ 'incluyeLogo' ];
$wincluyeTapa          = $modMon[ 'incluyeTapa' ];
$wimpresionDirecta     = $modMon[ 'impresionDirecta' ]; //esta variable verifica si la modalidad
														//permite impresion directa... para que funcione debe combinarse con el centro de costos que no vaya al centro de impresion

$wenviaEmail           = $modMon[ 'enviaEmail' ]; //esta variable verifica si la modalidad
														//envia los pdfs via correo electrónico.
$wmonitor              = $modMon[ 'monitor' ];
$wnombreModalidad      = $modMon[ 'descripcion' ];
$wfacturacion          = $modMon[ 'esFacturacion' ];
$wsoloPacientesActivos = $modMon[ 'soloPacientesActivos' ];
$errorEnBusqueda       = false;

$cadenaProgramasAnexos = "";
?>
<style>
	.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			height:30px;
			margin-left: 1%;
			cursor: pointer;
		 }

	/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
	.ui-datepicker {font-size:12px;}

	/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
	.ui-datepicker-cover {
	    display: none; /*sorry for IE5*/
	    display/**/: block; /*sorry for IE5*/
	    position: absolute; /*must have*/
	    z-index: -1; /*must have*/
	    filter: mask(); /*must have*/
	    top: -4px; /*must have*/
	    left: -4px; /*must have*/
	    width: 200px; /*must have*/
	    height: 200px; /*must have*/
	}

	.Titulo_azul{
		color:#3399ff;
		font-weight: bold;
		font-family: verdana;
		font-size: 10pt;
	}

	.BordeGris{
		border: 1px solid #999999;
	}

	.BordeNaranja{
		border: 1px solid orange;
	}

	.modal{
            display:none;
            cursor:default;
            background:none;
            repeat scroll 0 0;
            position:relative;
            width:98%;
            height:98%;
            overflow:auto;
        }
</style>

<script>
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);
</script>

<script>

//************cuando la pagina este lista...**********//
	var wemp_pmla;
	var wcenimp;
	var wcliame;
	var whcebasedato;
	var wmovhos;
	var wmodalidad;
	var wincluyeTapa;
	var wincluyeLogo;
	var esFacturacion;
	var wsoloActivos;
	var desbloquear;
	var wimpresionDirecta;
	var wenviaEmail;
	var codigoSolicitud = "";
	var wusuCenimp;
	var wingresoHce;

	$(document).ready(function(){

		if( $.trim( $("#whis").val() ) == "" &&  $.trim( $("#wing").val() ) == "" ){
			$("#btn_retornar1").hide();
			$("#btn_retornar2").hide();
		}else{
			addEventBtnGuardar( "si" );
		}

		wemp_pmla         = $("#wemp_pmla").val();
		wcenimp           = $("#wcenimp").val();
		wcliame           = $("#wcliame").val();
		whcebasedato      = $("#whcebasedato").val();
		wmovhos           = $("#wmovhos").val();
		wmodalidad        = $("#wmodalidad").val();
		wincluyeLogo      = $("#wincluyeLogo").val();
		wincluyeTapa      = $("#wincluyeTapa").val();
		wimpresionDirecta = $("#wimpresionDirecta").val();
		wenviaEmail       = $("#wenviaEmail").val();
		esFacturacion     = $("#wfacturacion").val();
		wsoloActivos      = $("#wsoloActivos").val();
		wusuCenimp        = $("#wusuCenimp").val();
		wingresoHce       = $("#wingresoHce").val();
		desbloquear       =  false;

		$(".solofloat").keyup(function(){
			if ($(this).val() !="")
				{
					$(this).val($(this).val().replace(/^(0)|[^0-9]/g, ""));
				}
			});
	});

	function validar(e) {
		var esIE  = (document.all);
		var esNS  = (document.layers);
		var tecla = (esIE) ? event.keyCode : e.which;
	   if ( tecla == 13 ){
		return true;
	   }
	   else return false;
	}

	//Funcion que con los parametros ingresados consulta en el servidor e imprime la lista de paquetes y la lista de formularios del paciente que el usuario logueado puede imprimir
	function consultarPacientes( btn ){

		var boton        = jQuery( btn );
		var historia     = $("#whis").val();
		var ingreso      = $("#wing").val();
		var weditar      = $("#weditar").val();
		var wemp_pmla    = $("#wemp_pmla").val();
		var wgrupo       = $("#wgrupo").val();
		var wfacturacion = $("#wfacturacion").val();
		cerrarDetalleErrores();

		//buscar todos los ingresos asociados a una historia conocida
		if( wfacturacion == "off"  && ( $.trim( historia ) != "" && $.trim( ingreso ) == "" ) ){
			buscarIngresos( historia );
			return;
		}

		//se ejecuta cuando la modalidad busca una historia a partir de un documento de identificación.
		if( wfacturacion == "off" && boton.attr( "buscaHistoria" ) == "on"  && ( $.trim( historia ) == "" && $.trim( ingreso ) == "" ) && (  ( $("#tipdoc") != undefined && $("#tipdoc").val()!="" )  &&  ( $("#numdoc") != undefined && $("#numdoc").val()!="" ) ) ){
			buscarHistoria();
			return;
		}

		//se ejecuta cuando la modalidad busca una historia a partir de un documento de identificación.
		if( wfacturacion == "off" && boton.attr( "buscaHistoria" ) == "on" && ( $.trim( historia ) == "" && $.trim( ingreso ) == "" )  ){
			return;
		}

		//se ejecuta siempre que hayan datos en historia e ingreso, para buscar los paquetes y formularios asociados a estos
		if( ( $.trim( historia ) != "" && $.trim( ingreso ) != "" ) ){
			var wcco         = $("#wcco").val();
			buscarPaquetesFormularios( historia, ingreso, wemp_pmla, wgrupo, wcco, weditar, wmodalidad);
			return;
		}

		//se ejecuta siempre que se esté buscando los pacientes de todo un centro de costos
		var wcco = $("#wcco").val();
		if( wfacturacion == "off" && $.trim( wcco ) != "" &&  ( $.trim( historia ) == "" || $.trim( ingreso ) == "" ) ){

			buscarPacientesCco( wemp_pmla, wcco, wmovhos );
		}

		//busqueda cuando es facturación.
		if( wfacturacion == "on" && ( $.trim( historia ) == "" || $.trim( ingreso ) == "" ) ){
			if( wgrupo != "*" ){
				buscarPacientesFacturacion();
			}
		}
	}

	//Esta funcion hace las peticiones para buscar los formularios asociados a una historia y un ingreso
	function  buscarPaquetesFormularios( historia, ingreso, wemp_pmla, wgrupo, wcco, weditar, wmodalidad ){

		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		var empresa = $("#wempresa").val();
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		//Realiza el llamado ajax con los parametros de busqueda

		$.post('solimp.php', { wemp_pmla: wemp_pmla, action: "consultarParaPaciente", wempresa: empresa, whis:historia, wcliame:wcliame, wing:ingreso, weditar:weditar, wgrupo:wgrupo, wcco:wcco, consultaAjax: aleatorio, wcenimp: wcenimp, wmovhos: wmovhos, whcebasedato: whcebasedato, wmodal: wmodalidad, wfact: esFacturacion, wsoloActivos: wsoloActivos, wusuCenimp: wusuCenimp, wenviaEmail:wenviaEmail,tipoPeticion: "normal" } ,
			function(data) {
				$.unblockUI();
				if( data == "error" ){
                  validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                  return;
                }
				if( data == "NO" ){
					alerta("No hay datos con los parametros ingresados");
					retornar();
				}else{
						$("#div_resultados").html(data);
						addEventBtnGuardar( "si" );
						$("#div_consulta").hide();
						$("#div_resultados").show();
						$("#contenedor_reporte").hide();
						$("input[type='button'][name='btn_guardar']").show();
						$("#btn_retornar1").show();
						$("#btn_retornar2").show();
					}

			});
	}

	//busca los pacientes activos en un centro de costos
	function buscarPacientesCco( wemp_pmla, wcco, wmovhos ){

		var nombreCco      = $("#wcco").find("option:selected").text();
		var nombresCco     = new Object();
		nombresCco[wcco]   = nombreCco;
		var JSONnombresCco = $.toJSON( nombresCco );
		var enviaEmail     = $("#wenviaEmail").val();

		$.ajax({
                url: "solimp.php",
               type: "POST",
              async: false,
             before: $.blockUI({ message: $('#msjEspere') }),
               data: {
                     	   action: "consultarPacientesCco",
                     	   	 wcco: wcco,
                          wmovhos: wmovhos,
                          wcenimp: wcenimp,
                     whcebasedato: whcebasedato,
                       wmodalidad: wmodalidad,
                 			wfact: esFacturacion,
                       wnombreCco: JSONnombresCco,
                     		wfact: esFacturacion,
					   wenviaEmail: enviaEmail,
                      tipoPeticion: "json"
                      },
                success: function(data)
                {
                	if( data.error == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                    }

                    if( data.error == "0" ){
                    	if( $("#padre_listadoPacientes") != undefined )
                    		$("#padre_listadoPacientes").remove();

                    	$("#div_consulta").append( data.listadoPacientes );
                    }
                    $.unblockUI();
                    if( data.error == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                    }
                },
                dataType: "json"
            });
	}

	//busca los pacientes bajo las condiciones predeterminadas en el formulario de facturación
	function buscarPacientesFacturacion(){

		var stringEntidades  = "";
		var nombresEntidades = new Object();
		var estadoPacientes  = $("input[type='radio'][name='chk_criterio']:checked").val();

		var i = 0;
		$( "input[type='checkbox'][name='entidadesGrupo']:checked" ).each(function(){
			if(i == 0)
				stringEntidades = $(this).val();
				else
					stringEntidades += "," + $(this).val();

			nombresEntidades[$(this).val()] = $(this).attr( "nomEntidad" );
			i++;
		});

		if( $.trim( stringEntidades ) == "" ){
			alerta( " debe seleccionar por lo menos un responsable ");
			return;
		}

		var JSONnombresEntidades = $.toJSON( nombresEntidades );
		var wfecini = $("#wfecini").val();
		var wfecfin = $("#wfecfin").val();
		var wgrupo  = $("#wgrupo").val();

		$.ajax({
                url: "solimp.php",
               type: "POST",
             before: $.blockUI({ message: $('#msjEspere') }),
               data: {
                     	   action: "buscarPacientesFacturacion",
                          wmovhos: wmovhos,
                     whcebasedato: whcebasedato,
                          wcenimp: wcenimp,
                       wmodalidad: wmodalidad,
                       wnomsEntis: JSONnombresEntidades,
                            wfact: esFacturacion,
                       wentidades: stringEntidades,
                          westPac: estadoPacientes,
                          wfecini: wfecini,
                          wfecfin: wfecfin,
						   wgrupo: wgrupo,
                     tipoPeticion: "normal"
                      },
                success: function(data)
                {
                	$.unblockUI();
                	if( data == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                    }
                    if( data == "NO" ){
                    	alerta( "Sin pacientes Asociados" );
                    }else{

                    	$("#div_resultados").html( data );
                    	addEventBtnGuardar( "no" );
                    	$("#div_consulta").hide();
                    	$("#contenedor_reporte").hide();
						$("#div_resultados").show();
						$("input[type='button'][name='btn_guardar']").show();
						$("#btn_retornar1").show();
						$("#btn_retornar2").show();
						$("#tabla_pac_facturacion :checkbox").attr("disabled",true);
                    }
                }
            });
	}

	function mostrarHijos( codigo_paquete ){
		$(".trhijopaquete"+codigo_paquete).toggle();
	}

	function marcarHijosPaquete( obj, codigo_paquete ){ //2014-01-17 esta funcion se modifica para que solo habilite historias que tengan formularios dentro de los paquetes seleccionados.

		$.blockUI({ message: $('#msjEspere') });
		setTimeout(function(){
			obj            = jQuery(obj);
			var formulario = "";
			var chekeado   = obj.is(":checked");

			// aca se verifican los formularios diligenciados de cada paquete para habilitar los que tengan formularios correspondientes a los formularios seleccionados.
			verificarFormulariosSeleccionados( codigo_paquete, chekeado );
			$(".hijopaquete"+codigo_paquete).each(function(){
				formulario = $(this).val();
				if( chekeado ){
					$("#tabla_arbol_formularios input[value="+formulario+"]").attr("checked",false).attr("disabled",true);
					$("#tabla_arbol_completo input[value="+formulario+"]").attr("checked",false).attr("disabled",true);
				}else{
					if( $("#tabla_paquetes input[value="+formulario+"]:checked").length == 0 ){
						$("#tabla_arbol_formularios input[value="+formulario+"]").attr("disabled",false);
						$("#tabla_arbol_completo input[value="+formulario+"]").attr("disabled",false);
					}
				}
			});
			$.unblockUI();
		}, 500);
	}

	function marcarTodos( obj ){
		obj = jQuery(obj);
		$(".formulario_arbol_impresion").attr("checked", obj.is(":checked") );
	}

	function solicitudEnMasa(){
		var huboError           = 0;
		var historiaBuscada     = "";
		var ingresoBuscada      = "";
		var fechaIngresoBuscada = "";
		var fechaEgresoBuscada  = "";

		if( $("input[type='checkbox'][name='chk_pacientes']:checked").size() == 0 ){
			return;
		}

		$("#div_resultados").hide();
		$("#msjEspereSolicitud").show();
		$("input[type='button'][name='btn_guardar']").hide();
		$("#btn_retornar1").hide();
		$("#btn_retornar2").hide();

		$("input[type='checkbox'][name='chk_pacientes']:checked").each(function(){
			if ( huboError != 2 ){

				historiaBuscada     = $(this).attr("historia");
				ingresoBuscada      = $(this).attr("ingreso");
				identificador       = $(this).attr("identificador");
				centroCostosBuscado = $(this).attr("centroCostos");
				fechaIngresoBuscada = $(this).attr("fechaIngreso");
				fechaEgresoBuscada  = $(this).attr("fechaEgreso");

				$("#whis").val( historiaBuscada );
				$("#wing").val( ingresoBuscada );
				$("#wcco").val( centroCostosBuscado );
				$("#wfecini_def").val( fechaIngresoBuscada );
				$("#wfecfin_def").val( fechaEgresoBuscada );
				$("#widenti").val( identificador );
				respuesta = enviarSolicitud( "no" );
				if( respuesta == "error" ){
					validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
					return false;
				}

				if( respuesta == 2 ){
					huboError = 2;
					return false;
				}

				if( respuesta == 1 ){
					agregarErrorDetalle( historiaBuscada, ingresoBuscada, " Los formularios del paquete no aplican para las fechas " );
					huboError = 1;
				}
				if ( respuesta == 0){
					if( wimpresionDirecta == "off" )
						$("div[name='div_impresion_pendiente'][historia='"+historiaBuscada+"'][ingreso='"+ingresoBuscada+"']").show();
					$(this).removeAttr( "checked" );
					$(this).attr( "disabled", "disabled" );
				}
			}
		});
		consultarSolicitudesAnt(); //para que actualice el panel de consultas
		if( huboError == 1 ){
			alerta(' algunas solicitudes no se realizaron. ');
			retornar();
			$("#contenedor_detalles_masivos").show();
		}
		if( huboError == 0 ){
			$.unblockUI();
			$("#div_resultados").show();
			$("input[type='button'][name='btn_guardar']").show();
			$("#btn_retornar1").show();
			$("#btn_retornar2").show();
			$("#msjEspereSolicitud").hide();
			$("input[type='button'][name='btn_guardar']").attr( "disabled", "disabled" );
			alerta('Exito al guardar');
		}
		if( huboError == 2 ){
			alerta("Por favor seleccione los formularios que desea solicitar imprimir");
			$.unblockUI();
			$("#div_resultados").show();
			$("input[type='button'][name='btn_guardar']").show();
			$("#btn_retornar1").show();
			$("#btn_retornar2").show();
			$("#msjEspereSolicitud").hide();
		}
		$("#tabla_arbol_completo input[type=checkbox]").each(function(){
	              codigoAuxiliar = $(this).val();
	              $("#tabla_arbol_completo input[type=checkbox][value='"+codigoAuxiliar+"']").each(function(){
		                  $(this).attr("checked", false);
		                  $(this).attr("enabled", false);
		                  $(this).parent().next().removeClass("fondoAmarillo");
	              });

		});
	}

	//Funcion que luego de seleccionar los formularios(de paquetes y del arbol) registra una solicitud en la bd
	function enviarSolicitud( bloquear ){

		var formularios         = "";
		var formularios_paq     = "";
		var historia            = $("#whis").val();
		var ingreso             = $("#wing").val();
		var centroCostos        = $("#wcco").val();
		var wSolicitaCenimp     = $("#wSolicitaCenimp").val();
		var monitor             = $("#wmonitor").val();
		var fecha_i             = $("#fecha_inicial").val();
		var fecha_f             = $("#fecha_final").val();
		var error               = 0;
		var weditar             = $("#weditar").val();
		var widenti             = $("#widenti").val();
		var wespecial           = $("#wespecial").val();
		var fechaIni_def        = "";
		var fechaFin_def        = "";
		var formulariosElegidos = $(".contenedor_formularios[historia='"+historia+"'][ingreso='"+ingreso+"']").attr("formulariosElegidos");
		var cadenaProgramasAnexos = $("#cadenaProgramasAnexos").val();
		
		var htmlProgramasAnexos = "";
		if(cadenaProgramasAnexos!="")
		{
			htmlProgramasAnexos = consultarHtmlPorProgramaAnexo(cadenaProgramasAnexos);
		}
		
		if( bloquear == "no" ){
			fechaIni_def = $("#wfecini_def").val();
			fechaFin_def = $("#wfecfin_def").val();
		}

		var hayFormularios = false;
		var hayPaquetes    = false;

		var j=0;
		if( bloquear=="no" ){
			$("#tabla_arbol_completo").find(".formulario_arbol_impresion:checked").each( function(){
				if(j>0)
					formularios+=",";
				formularios+= $(this).val();
				j++;
				hayFormularios=true;
			});
		}else{
			$("#tabla_arbol_formularios").find(".formulario_arbol_impresion:checked").each( function(){
				if(j>0)
					formularios+=",";
				formularios+= $(this).val();
				j++;
				hayFormularios=true;
			});
		}

		var datos               = new Object();
		datos.formularios_arbol = formularios;
		datos.paquetes          = new Object();

		j=0;
		$("#tabla_paquetes").find(".formulario_de_paquete:checked").each( function(){

			var paquete = $(this).attr("paquete");
			if( datos.paquetes[ paquete ] == undefined )
				datos.paquetes[ paquete ] = new Array();

			datos.paquetes[ paquete ].push( $(this).val() );
			hayPaquetes=true;
		});

		var datosJson = $.toJSON( datos ); //convertir el arreglo de objetos en una variable json

		if( hayPaquetes == false && hayFormularios==false ){
			alerta("Por favor seleccione los formularios que desea solicitar imprimir");
			error = 2;
			return( error );
		}

		if( bloquear == "si" ){
			$("#msjEspereSolicitud").show();
			$("#div_resultados").hide();
			$("input[type='button'][name='btn_guardar']").hide();
			$("#btn_retornar1").hide();
			$("#btn_retornar2").hide();
		}
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio      = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.ajax({
				  url:'solimp.php',
				  type: "POST",
				 data: {
				 	 	wemp_pmla: wemp_pmla,
				 	 	  fecha_i: fecha_i,
				 	 	  fecha_f: fecha_f,
				 	   fecIngreso: fechaIni_def,
				 	    fecEgreso: fechaFin_def,
				 	 	  monitor: monitor,
				 	 	    datos: datosJson,
				 	 	 historia: historia,
				 	 	  ingreso: ingreso,
				 	 	     wcco: centroCostos,
				  wSolicitaCenimp: wSolicitaCenimp,
				      wenviaEmail: wenviaEmail,
				wimpresionDirecta: wimpresionDirecta,
				 	 	   action: "guardarSolicitud",
				 	 consultaAjax: aleatorio,
				 	 	  wcenimp: wcenimp,
				 	 	  wmovhos: wmovhos,
				 	 whcebasedato: whcebasedato,
				 	   wmodalidad: wmodalidad,
				 	        wlogo: wincluyeLogo,
				 	        wtapa: wincluyeTapa,
				 	 	  weditar: weditar,
						  widenti: widenti,
						wespecial: wespecial,
                     tipoPeticion: "normal",
              htmlProgramasAnexos: htmlProgramasAnexos,
              formulariosElegidos: formulariosElegidos} ,
				success: function(data) {
						if( data == "error" ){
							if( bloquear == "no" )
		                   		return("error");
		                   	else
		                   		validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
		                }
							control         = data.split("|");
							paginas         = control[0];
							//alert('control'+control[1]);
							respuesta       = control[1];
							solicitaCenimp  = control[2];
							codigoSolicitud =  control[3];
							td		= $("td[name='numHojas'][historia='"+historia+"'][ingreso='"+ingreso+"']");
							if( td != undefined )
								td.html( paginas );
						if( bloquear == "si" ){
							$("#msjEspereSolicitud").hide();
							$("#div_resultados").show();
							$("input[type='button'][name='btn_guardar']").show();
							$("#btn_retornar1").show();
							$("#btn_retornar2").show();
						}

						if( $.trim(respuesta) == "OK" ){
							error = 0;
							consultarSolicitudesAnt(); //Para que actualizce el panel de consultas
							if( bloquear == "si" ){
								if( ( $("#wimpresionDirecta").val() == 'off' && $("#wenviaEmail").val() == 'off' ) || (  $("#wimpresionDirecta").val() == 'on' && wusuCenimp=='on' && $("#wenviaEmail").val() == 'off' )){
									if( $("#wimpresionDirecta").val() == 'off' )
										$("div[name='div_impresion_pendiente'][historia='"+historia+"'][ingreso='"+ingreso+"']").show();
									$("input[type='checkbox'][name='chk_anular'][historia='"+historia+"'][ingreso='"+ingreso+"']").show();
									$(".btnConsulta[historia='"+historia+"'][ingreso='"+ingreso+"']").val("Editar");
								}

								if( ( solicitaCenimp == "off" && wimpresionDirecta =="on" ) || ( wenviaEmail == "on" )  ){
									$("#div_resultados").find("input[type='checkbox']").attr( "disabled", true );
									$("input[type='button'][bloquear='si']").attr( "disabled", true );
									mostrarPDF( codigoSolicitud );
									if( wingresoHce == "si" ){
										$("#btn_retornar1").hide();
										$("#btn_retornar2").hide();
									}
									if( wenviaEmail == "on" ){
										$("#div_formularioEnvio").show();
										$("input[type='button'][accion='enviarPdf']").show();
									}else{
										$("input[type='button'][accion='cerrarPdf']").show();
									}
								}else{
									retornar();
									alerta("Exito al guardar");
								}
							}
						}else{
								error = 1;
								if( bloquear == "si" ){
								alerta('Error al realizar la solicitud, no existen formularios que apliquen en la solicitud.');
								$("#resultados_error").html( respuesta );
							}
						}
					}
				});
		return( error );
	}

	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 1600 );
	}

	//busca historia e ingresos cuando se busca por identificación
	function buscarHistoria(){

		var input_documento   = jQuery( $("#numdoc") );
		var numero_documento  = input_documento.val();
		var tipo_documento    = $("#tipdoc").val();

		if( $.trim( numero_documento ) == "" )
			return;

		if( $.trim( tipo_documento ) == "" ){
			alerta( "Elija el tipo de documento " );
			return;
		}

		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		$.ajax({
                url: "solimp.php",
                type: "POST",
                async: false,
                before: $.blockUI({ message: $('#msjEspere') }),
                data: {
                     		action: "buscarHistoria",
				   		 wemp_pmla: wemp_pmla,
			           		 wtdoc: tipo_documento,
			           		 wndoc: numero_documento,
			           	   wcenimp: wcenimp,
			           	   wmovhos: wmovhos,
			          whcebasedato: whcebasedato,
                      tipoPeticion: "json"
                      },
                success: function(data)
                {
                  $.unblockUI();
                  if( data.error == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                   }
                   if( $.trim( data.historia ) != "" ){
			 			$("#whis").val( data.historia );
			 			$("#td_padre_ingreso").html( data.ingresos );
			 		}else{
			 			alerta( " No hay Historia Asociada " );
			 		}
                },
                dataType: "json"
            });
		  $.unblockUI();
	}

	function retornar(){
		$("#whis").val( "" );
		var contenedor = $("#wing").parent();
		$("#wing").remove();
		$( contenedor ).html( "<input type='text' id='wing' value='' />" );
		if( wingresoHce == "si" ){
			$("#wing").hide();
		}
		$("#wing").val( "" );
		$("#weditar").val( "no" );
		$("#div_consulta").show();
		$("#contenedor_reporte").show();
		$("#div_resultados").html("");
		$("#div_resultados").hide();
		$("#div_contenedor_pdf").html('');
		$("#div_contenedor_pdf").hide();
		$("#msjEspereSolicitud").hide();
		$("#msjEspereEmail").hide();
		$("input[type='button'][name='btn_guardar']").hide();
		$("#btn_retornar1").hide();
		$("#btn_retornar2").hide();
		$("input[type='button'][bloquear='si']").attr( "disabled", false );
		$("#div_formularioEnvio").hide();
		$("input[type='button'][accion='enviarPdf']").hide();
		$("#emailpaciente").val('');
		$("#emailmensaje").val('');
	}

	function consultarHistoriaIngresoActivo( historia, ingreso, solicitudExistente, fechaIngreso, fechaEgreso, widenti, $wformulariosDiligenciados ){

		$("#whis").val( historia );
		$("#wing").val( ingreso );
		$("#wfecini_def").val( fechaIngreso );
		$("#wfecfin_def").val( fechaEgreso );
		$("#weditar").val( solicitudExistente );
		$("#widenti").val( widenti );
		$("#btn_consultar").click();
	}

	function busarEntidadesEnGrupo( slt_grupo ){

		var codigoGrupo = $(slt_grupo).val();
		if( codigoGrupo == "---" ){
			$( "#td_entidades" ).html("");
			return;
		}

		$.ajax({
                  url: "solimp.php",
                 type: "POST",
                async: false,
               before: $.blockUI({ message: $('#msjEspere') }),
                 data: {
                     		action: "buscarEntidadesEnGrupo",
				   		 wemp_pmla: wemp_pmla,
			           	   wmovhos: wmovhos,
			           	   wcodgru: codigoGrupo,
			          whcebasedato: whcebasedato,
			               wcenimp: wcenimp,
			               wcliame: wcliame,
                      tipoPeticion: "json"
                      },
                success: function(data)
                {
                	$.unblockUI();
                	if( data.error == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                    }
                	if( ( data.error )*1 == 0 ){
                		$("#td_entidades").html( data.entidades );
                		$("#td_entidades").parent().show();
                	}else{
                		$( "#td_entidades" ).html("");
                		$( "#td_entidades" ).parent().hide();
                		alerta( "No hay Entidades en el grupo seleccionado" );
                	}
                },
                dataType: "json"
            });
	}

	function addEventBtnGuardar( directo ){

		$("input[type='button'][name='btn_guardar']").removeAttr("disabled");
		$("input[type='button'][name='btn_guardar']").unbind("click");
		if( directo == "si" ){
			$("input[type='button'][name='btn_guardar']").click(function(event){
				enviarSolicitud( "si" );
			});
			return;
		}
		if( directo == "no" ){
			$("input[type='button'][name='btn_guardar']").click(function(event){
				solicitudEnMasa();
			});
			return;
		}
	}

	function cambiarEstadoChecks( chk ){
		padre  = jQuery(chk);
		origen = padre.attr("origen");
		if( padre.is(":checked")){
			$("input[type='checkbox'][name='chk_pacientes'][origen='"+origen+"'][tieneFormularios='si']:not(:disabled):not(:checked)").attr( "checked", true );
			$("input[type='button'][class='btnConsulta'][tieneFormularios='si'][origenDatos='"+origen+"']").attr( "disabled", true );
		}else{
			$("input[type='checkbox'][name='chk_pacientes'][origen='"+origen+"'][tieneFormularios='si']:not(:disabled):checked").attr( "checked", false );
			$("input[type='button'][class='btnConsulta'][tieneFormularios='si'][origenDatos='"+origen+"']").attr( "disabled", false );
		}
	}

	function clickHijoPaquete( ele ){
		ele            = jQuery(ele);
		var formulario = ele.val();
		if( ele.is(":checked") ){
			$("#tabla_arbol_formularios input[value="+formulario+"]").attr("checked",false).attr("disabled",true);
			$("#tabla_arbol_completo input[value="+formulario+"]").attr("checked",false).attr("disabled",true);
		}else{
			if( $("#tabla_paquetes input[value="+formulario+"]:checked").length == 0 ){
				$("#tabla_arbol_formularios input[value="+formulario+"]").attr("disabled",false);
				$("#tabla_arbol_completo input[value="+encodeURIComponent(formulario)+"]").attr("disabled",false);
			}
		}
		activarHistoriasConFormulario(ele.val(), ele.is(":checked"));
	}

	function anularSolicitud( chk, cod_solicitud ){

		objeto = jQuery(chk);
		if( !confirm( "Está seguro que quiere cancelar la solicitud " ) ){
			objeto.attr( "checked", false );
			return;
		}
		historia = objeto.attr("historia");
		ingreso  = objeto.attr("ingreso");
		$.ajax({
				  url:'solimp.php',
				 data: {
				 	       action: "anularSolicitud",
				 	 	wemp_pmla: wemp_pmla,
				 	 	 	 whis: historia,
				 	 	     wing: ingreso,
					   wsolicitud: cod_solicitud,
				 	 	  wcenimp: wcenimp,
				 	   	     wmod: wmodalidad,
                     tipoPeticion: "normal"} ,
				success: function(data) {
					    if( data == "error" ){
	                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
	                      return;
		                }
						if( data == "OK" ){
								if( objeto.attr( "origen" ) == "int" ){
									retornar();
								}
								$("div[name='div_impresion_pendiente'][historia='"+historia+"'][ingreso='"+ingreso+"']").hide();
								objeto.hide();
								$(".btnConsulta[historia='"+historia+"'][ingreso='"+ingreso+"']").val("Solicitar Imp.");
								$("td[name='numHojas'][historia='"+historia+"'][ingreso='"+ingreso+"']").html("&nbsp;");
								if( objeto.attr( "origen" ) == "consultas" ){
									contenedor = objeto.parent();
									contenedor.html("ANULADO");
								}
						}else{
							alert("Error: "+data);
						}
					}
				});
	}

	function mostrarPDF( codigo_solicitud ){
		var wemp_pmla = $("#wemp_pmla").val();
		var object    = '<br><br><br><font size=5 color="#2A5DB0">Solicitud '+codigo_solicitud+'</font>'
					+'<br><br>';
		if( wenviaEmail == "on" ){
			object+= '<object width="900" height="700" data="../../../include/root/pdfjs/visorweb/visor.php?wnombrepdf='+wemp_pmla+'Solicitud_'+codigo_solicitud+'&wimprimir=off">';
					+'</object>';
		}else{
			object+= '<object type="application/pdf" data="../reportes/cenimp/'+wemp_pmla+'Solicitud_'+codigo_solicitud+'.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" width="900" height="700">'
						+'<param name="src" value="../reportes/cenimp/'+wemp_pmla+'Solicitud_'+codigo_solicitud+'.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" />'
						+'<p style="text-align:center; width: 60%;">'
							+'Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />'
							+'<a href="http://get.adobe.com/es/reader/" onclick="this.target=\'_blank\'">'
								+'<img src="../../images/medical/root/prohibido.gif" alt="Descargar Adobe Reader" width="32" height="32" style="border: none;" />'
							+'</a>'
						+'</p>'
					+'</object>';
		}
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

	function cerrarPDF(){
		$("#div_contenedor_pdf").html('');
		$("#div_contenedor_pdf").hide();
		$("#div_resultados").find("input[type='checkbox']").attr( "disabled", false );
		$("input[type='button'][bloquear='si']").attr( "disabled", false );
	}

	function enviarPdf(){

		var wemail_destino  = $("#emailpaciente").val();
		var wnombre_destino = $("#nombre_paciente").val();
		var wasunto         = $("#emailasunto").val();
		var wmensaje        = $("#emailmensaje").val();
		var wnombrepdf      = "Solicitud_"+codigoSolicitud;
		var wcorreopmla     = $("#wcorreopmla").val();

		if( !IsEmail( wemail_destino ) ){
			alerta("Correo Inválido");
			return;
		}

		$("#div_resultados").hide();
		$("#div_contenedor_pdf").hide();
		$("#div_formularioEnvio").hide();
		$("input[type='button'][name='btn_guardar']").hide();
		$("#btn_retornar1").hide();
		$("#btn_retornar2").hide();
		$("#msjEspereEmail").show();

		$.ajax({
				  url:'solimp.php',
				 data: {
							action          : "enviarCorreo",
							wemp_pmla       : wemp_pmla,
							wcenimp         : wcenimp,
							wmovhos         : wmovhos,
							wemail_destino  : wemail_destino,
							wnombre_destino : wnombre_destino,
							widentificador  : codigoSolicitud,
							wmod            : wmodalidad,
							wasunto         : wasunto,
							wmensaje        : wmensaje,
							wcorreopmla     : wcorreopmla,
							wnombrepdf      : wnombrepdf,
                            tipoPeticion    : "normal"} ,
				success: function(data) {
					    if( data == "error" ){
	                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
	                      return;
	                    }
						if( data == "OK" ){
							alerta( " Correo enviado correctamente " );
							retornar();
						}else{
							alerta( " Error al enviar el email, intente mas tarde " );
						}
					}
				});
	}

	function IsEmail(email) {
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}

	function mostrarocultarDiv( id ){
		$("#"+id ).toggle();
	}

	function consultarSolicitudesAnt(primeraVez){
		if( primeraVez == true && $("#div_consultas").html() != "" )
			return;
		var whistoriac  = $("#historia_consulta").val();
		var wingresoc 	= $("#ingreso_consulta").val();
		var wfechac     = $("#fecha_consulta").val();
		var wdoc        = $("#documento_consulta").val();

		$.ajax({
				  url:'solimp.php',
				 data: {
							action          : "consultarSolicitudes",
							wemp_pmla       : wemp_pmla,
							wcenimp         : wcenimp,
							whcebasedato	: whcebasedato,
							wmovhos         : wmovhos,
							wdoc         	: wdoc,
							whistoriac  	: whistoriac,
							wingresoc		: wingresoc,
							wfechac         : wfechac,
                            tipoPeticion    : "normal"} ,
				success: function(data) {
					    if( data == "error" ){
	                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
	                      return;
	                    }
						$("#div_consultas").html("<br>"+data+"<br>");
					}
				});
	}

	function elegirTodos( codigoGrupo, todos ){
		$("#div_empresas_"+codigoGrupo+" input[type='checkbox'] ").attr( "checked", $(todos).is(":checked") );
	}

	function buscarIngresos( historia ){

		$.blockUI({ message: $('#msjEspere') });
		$.ajax({
				  url:'solimp.php',
				 data: {
				 	       action: "buscarIngresos",
				 	 	wemp_pmla: wemp_pmla,
				 	 	 	 whis: historia,
				 	 	  wmovhos: wmovhos,
				 	 wsoloActivos: wsoloActivos,
				 	  wenviaEmail: wenviaEmail,
				 	   wusuCenimp: wusuCenimp,
                     tipoPeticion: "json"
				 	 	} ,
				success: function(data){
						$.unblockUI();
						if( data.error == "error" ){
	                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
	                      return;
	                    }
						if( data.error*1 != 1 ){
							var contenedor = $("#wing").parent();
							$("#wing").remove();
							$( contenedor ).html( data.elemento );
						}else{
							alerta( "No hay ingresos registrados a la historia: "+historia );
						}
					},
			dataType: "json"
			});
	}

	function agregarErrorDetalle( historia, ingreso, mensaje ){

		tr = "<tr class='fila2' tipo='info'><td align='center'> "+historia+"-"+ingreso+" </td><td>"+mensaje+"</td></tr>";
		$("#tbl_solicitudes_fallidas").append( tr );
	}

	function cerrarDetalleErrores(){
		$("#tbl_solicitudes_fallidas tr[tipo!='encabezado']").remove();
		$("#contenedor_detalles_masivos").hide();
	}

	function cambiarEstadoBoton( obj ){

		histo  = $( obj ).attr('historia');
		ingre  = $( obj ).attr('ingreso');
		origen = $( obj ).attr('origen');
		if( $(obj).is( ":checked" ) ){
			$("input[type='button'][class='btnConsulta'][tieneFormularios='si'][origenDatos='"+origen+"']").attr( "disabled",  $(obj).is(":checked") );
			checados = $( "#tabla_pac_facturacion input[type='checkbox'][name!='chk_todos'][origen='"+origen+"']:checked" );
			if( checados.size() == $( "#tabla_pac_facturacion input[type='checkbox'][name!='chk_todos'][origen='"+origen+"']" ).size() ){
				$("input[type='checkbox'][name='chk_todos'][origen='"+origen+"']").attr( "checked",  true );
			}
		}else{
				seleccionados = $( "#tabla_pac_facturacion input[type='checkbox'][name!='chk_todos'][origen='"+origen+"']:checked" );
				if( seleccionados.size() == 0 ){
					$("input[type='button'][class='btnConsulta'][tieneFormularios='si'][origenDatos='"+origen+"']").attr( "disabled",  $(obj).is(":checked") );
					$("input[type='checkbox'][name='chk_todos'][origen='"+origen+"']").attr( "checked",  false );
				}
			}
	}

	function checkearTodoGlobal( obj ){
		if( $( obj ).is(":checked") ){
			$("input[type='checkbox'][name='chk_todos']:not(:checked):not(:disabled)").attr( "checked", true );
			$("input[type='checkbox'][name='chk_todos']:checked:not(:disabled)").click();
			$("input[type='checkbox'][name='chk_todos']:not(:checked):not(:disabled)").attr( "checked", true );
		}else{
			$("input[type='checkbox'][name='chk_todos']:checked:not(:disabled)").attr( "checked", false );
			$("input[type='checkbox'][name='chk_todos']:not(:checked):not(:disabled)").click();
			$("input[type='checkbox'][name='chk_todos']:checked:not(:disabled)").attr( "checked", false );
		}
	}

	function mostrarModalFormularios(){
		div = "div_formularios_completos";
            $.blockUI({
                        message: $("#"+div),
                        css: { left: '5%',
                                top: '5%',
                              width: '90%',
                             height: '90%'
                            }
              });
	}

	function cerrarModalFormularios(){
    	$.unblockUI();
    }

    function checkearColumna( check ){

            check  = jQuery( check );
            codigo = check.val();

            if( check.is( ":checked" ) ){
                $("#tabla_arbol_completo input[type=checkbox][codigoRelacion^="+codigo+"]:not(:checked)").each(function(){

                    codigoAuxiliar = $(this).val();
                    $("#tabla_arbol_completo input[type=checkbox][value='"+codigoAuxiliar+"']:not(:checked)").each(function(){

                       if( $(this) != check )
                         $(this).attr("checked", true);

                       if( $(this).attr( "esPadre" ) != "on" )
                        $(this).parent().next().addClass("fondoAmarillo");

                    });
                });
            }else{
                  $("#tabla_arbol_completo input[type=checkbox][codigoRelacion^="+codigo+"]:checked").each(function(){

                      codigoAuxiliar = $(this).val();
                      $("#tabla_arbol_completo input[type=checkbox][value='"+codigoAuxiliar+"']:checked").each(function(){
                        if( $(this) != check )
                          $(this).attr("checked", false);
                          $(this).parent().next().removeClass("fondoAmarillo");
                      });

                  })
            }
    }

    function chequearFormulario( check ){
		   var chk              = jQuery( check );
		   var codigoFormulario = chk.val();
           if( chk.attr("checked") )
           {
             chk.parent().next().addClass("fondoAmarillo");
             $("#tabla_arbol_completo").find("input[type=checkbox][value='"+codigoFormulario+"']:not(:checked)").each(function(){

                if( $(this) != chk )
                    $(this).attr("checked", true);
                $(this).parent().next().addClass("fondoAmarillo");

              });
           }else
              {

                chk.parent().next().removeClass("fondoAmarillo");
                $("#tabla_arbol_completo").find("input[type=checkbox][value='"+codigoFormulario+"']:checked").each(function(){
                  if( $(this) != chk )
                    $(this).attr("checked", false);
                     $(this).parent().next().removeClass("fondoAmarillo");
                });

              }
    }

    function validarExistenciaParametros( txt ){
      $("div [id!='div_sesion_muerta']").hide();
      $("#div_sesion_muerta").show();
    }

    function verificarFormulariosSeleccionados( codigo_paquete, checkear ){

    	if( checkear ){
	    	$(".hijopaquete"+codigo_paquete+":not(:checked)").each(function(){
	    		var codigoFormulario = $(this).val();
	    		activarHistoriasConFormulario(codigoFormulario, checkear );
	    		$(this).attr("checked", checkear );
	    	});
    	}else{
    		$(".hijopaquete"+codigo_paquete+":checked").each(function(){
	    		var codigoFormulario = $(this).val();
	    		activarHistoriasConFormulario(codigoFormulario, checkear );
	    		$(this).attr("checked", checkear );
	    	});
    	}
    }

    /** si se checkea un formulario, se busca en todos las filas(historias-ingresos) si lo tienen diligenciado,
     en caso de que lo tenga, el codigo de este se agrega al atributo de formularios elegidos, para facilitar la activación o desactivación
     de las solicitudes masivas. Dependiendo de si la solicitud incluye formularios elegidos para la solicitud masiva o no se realiza una solicitud para
     ese paciente **/
    function activarHistoriasConFormulario( codigoFormulario, checkear ){

		var posicionEncontrada;

		// $(".contenedor_formularios[formulariosDiligenciados^='"+codigoFormulario+",'],[formulariosDiligenciados*=',"+codigoFormulario+",'],[formulariosDiligenciados$=',"+codigoFormulario+"'],[formulariosDiligenciados='"+codigoFormulario+"']").each(function(){
		$(".contenedor_formularios[formulariosDiligenciados^='"+codigoFormulario+",'],[formulariosDiligenciados*=',"+codigoFormulario+",'],[formulariosDiligenciados$=',"+codigoFormulario+"'],[formulariosDiligenciados='"+codigoFormulario+"'],[cadAnexos*='"+codigoFormulario+"']").each(function(){
			var aux                 = $.trim($(this).attr("formulariosElegidos"));
			var formulariosElegidos = aux.split(",");
			var historiaActual      = $(this).attr("historia");
			var ingresoActual       = $(this).attr("ingreso");
    		encontrado = false; // variable que me indica si el formulario ha sido seleccionado previamente.
    		posicionEncontrada = 0;

    		for(var i = 0; i <= formulariosElegidos.length ; i ++ ){
    			if( formulariosElegidos[i] == codigoFormulario ){
    				posicionEncontrada = i;
    				encontrado = true;
    				i = formulariosElegidos.length;
    			}
    		}

    		if( checkear ){
    			if( formulariosElegidos[0] == "" )
    				formulariosElegidos.splice(0,1);
    			formulariosElegidos.push( codigoFormulario );
    			$(this).attr("disabled", false);
    			$(".formulariosElegidos[historia='"+historiaActual+"'][ingreso='"+ingresoActual+"']").show();
    			$("input[type='checkbox'][name='elegidoParaImprimir'][historia='"+historiaActual+"'][ingreso='"+ingresoActual+"'][codigo='"+codigoFormulario+"']").attr("checked", true);
    		}

    		if(checkear == false || checkear == ""){

    			if(encontrado){
    				formulariosElegidos.splice(posicionEncontrada,1);
    			}
    			encontrado2 = false;
    			for(var i = 0; i <= formulariosElegidos.length ; i ++ ){//aca lo volvemos a buscar para ver si se deselecciona de los formularios a imprimir que se ven en pantalla
	    			if( formulariosElegidos[i] == codigoFormulario ){
	    				posicionEncontrada = i;
	    				encontrado2 = true;
	    				i = formulariosElegidos.length;
	    			}
	    		}
	    		if( !encontrado2 ){
    				$("input[type='checkbox'][name='elegidoParaImprimir'][historia='"+historiaActual+"'][ingreso='"+ingresoActual+"'][codigo='"+codigoFormulario+"']").attr("checked", false);
    			}
    		}
    		elegidosFinal = formulariosElegidos.join(",");
    		$(this).attr("formulariosElegidos", elegidosFinal );
    		if( $.trim(elegidosFinal) == ""){
    			$(this).attr("disabled", true);
    			$(".formulariosElegidos[historia='"+historiaActual+"'][ingreso='"+ingresoActual+"']").hide();
    		}
    	});
    }

    function mostrarDetalleFormularios( historia, ingreso, nombre ){

    	$("#div_dialogo").html( $("div[name='dialogo'][historia='"+historia+"'][ingreso='"+ingreso+"']").html());
    	$("#div_dialogo").dialog({
    		 title: "<font size='1'>Hitoria: "+historia+" - Ingreso: "+ingreso+"</font><br><font size='1'>"+nombre+"</font>",
			 modal: true,
			 buttons: {
	            Ok: function() {
					$( this ).dialog( "close" );
				}
			},
			 show: {
			 	effect: "blind",
			 	duration: 500
			 },
			 hide: {
				effect: "blind",
				duration: 500
			},
			height: 400,
			width: 400,
			rezisable: true
		});

    }
	
	function consultarHtmlPorProgramaAnexo(cadenaProgramasAnexos)
	{
		var historia = $("#whis").val();
		var ingreso = $("#wing").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		programasAnexos = cadenaProgramasAnexos.split("|");
		
		// hacer ajax a cada programa anexo para obtener el html utlizado en la construccion del pdf
		htmlProgramasAnexos = "";
		for(var i=0;i<(programasAnexos.length)-1;i++)
		{
			
			var formularioSimple = "";
			
			var programaAnexoChecked = false;
						
			$("#tabla_arbol_formularios").find(".formulario_arbol_impresion").each( function(){
				
				if($(this).attr("checked")=="checked" || $(this).attr("disabled")=="disabled")
				{
					formularioSimple = "on";
					
					if($(this).attr("progAnex")==programasAnexos[i])
					{
						programaAnexoChecked = true;
					}
				}
			});
			
			
			// es de paquetes
			if(formularioSimple == "")
			{
				$("#tbl_diligenciados").find("[name='elegidoParaImprimir']:checked").each( function(){
					formularioSimple = "on";
					if($(this).attr("progAnex")==programasAnexos[i])
					{
						programaAnexoChecked = true;
					}
				});
				
			}
			
			if(programaAnexoChecked)
			{
				programaAnexo = programasAnexos[i];
				programaAnexo = programaAnexo.split("?");
				programaAnexo = programaAnexo[0];
				
				programaAnexo = "../../../"+programaAnexo;
				
				$.ajax({
					url:programaAnexo,
					type: "POST",
					async: false,
					data: 	{
								consultaAjax 	: '',
								action			: 'consultarHtmlImpresionHCE',
								wemp_pmla		: wemp_pmla,
								historia		: historia,
								ingreso			: ingreso
							} ,
					success: function(data) {
						// console.log(data)
						htmlProgramasAnexos += data.html +"|||||-----*****";
					},
					dataType: "json"
				});
			}
		}
		
		return htmlProgramasAnexos;
	}
</script>

</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
				$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
				$empresa     = strtolower($institucion->baseDeDatos);
				encabezado("SOLICITUD DE IMPRESION", $wactualiz, $empresa );
				if( isset($whis) and isset($wing) ){
					$ingresoHce =  'si';
					$verCerrar  =  "display:none;";
				}else{
						$ingresoHce = 'no';
						$verCerrar  = "";
					 }

				echo '<center>';
				/** VARIABLES HTML QUE SE VAN A USAR COMO GLOBALES EN JAVASCRIPT **/
				echo "<input type='hidden' name ='wemp_pmla'    id ='wemp_pmla'    value='".$wemp_pmla."'/>";
				echo "<input type='hidden' name ='wempresa'     id ='wempresa'    value='".$empresa."'/>";
				echo "<input type='hidden' name ='wcliame'      id ='wcliame'    value='".$wcliame."'/>";
				echo "<input type='hidden' name ='wcenimp'      id ='wcenimp'      value='".$wcenimp."'/>";
				echo "<input type='hidden' name ='whcebasedato' id ='whcebasedato' value='".$whcebasedato."'/>";
				echo "<input type='hidden' name ='wmovhos'      id ='wmovhos'      value='".$wmovhos."'/>";
				echo "<input type='hidden' name ='wmonitor'     id ='wmonitor'     value='".$wmonitor."'/>";
				echo "<input type='hidden' name ='wcorreopmla'  id ='wcorreopmla'  value='".$wcorreopmla."'/>";
				echo "<input type='hidden' name ='wmodalidad'   id ='wmodalidad'   value='{$wmodalidad}'/>";
				echo "<input type='hidden' name ='wenviaEmail'  id ='wenviaEmail'   value='{$wenviaEmail}'/>";
				echo "<input type='hidden' name ='wincluyeLogo' id ='wincluyeLogo' value='{$wincluyeLogo}'/>";
				echo "<input type='hidden' name ='wincluyeTapa' id ='wincluyeTapa' value='{$wincluyeTapa}'/>";
				echo "<input type='hidden' name ='wfacturacion' id ='wfacturacion' value='{$wfacturacion}'/>";
				echo "<input type='hidden' name ='wsoloActivos' id ='wsoloActivos' value='{$wsoloPacientesActivos}'/>";
				echo "<input type='hidden' name ='wusuCenimp'   id ='wusuCenimp' value='{$wusuCenimp}'/>";
				echo "<input type='hidden' name ='wfecini_def'  id ='wfecini_def'  value=''/>"; //variables para almacenar las fechas de estancia de un paciente y usarlas cuando sea necesario.
				echo "<input type='hidden' name ='wfecfin_def'  id ='wfecfin_def'  value=''/>"; //variables para almacenar las fechas de estancia de un paciente y usarlas cuando sea necesario.
				echo "<input type='hidden' name ='wimpresionDirecta'   id ='wimpresionDirecta'   value='{$wimpresionDirecta}'/>";
				echo "<input type='hidden' name ='wingresoHce'   id ='wingresoHce'   value='{$ingresoHce}'/>"; //esta variable indica al programa si se ingreso directamente desde la historia clinica electrónica

				/** FINAL DE VARIABLES GLOBALES **/

				echo "<div align='left' style='width:80%;'><span class='subtituloPagina2' style='font-size:20;'>{$wnombreModalidad}</span></div><br>";
				echo "<div id='div_consulta' align='center' style='width: 90%'>";

				/**++++++++++++++++++++++++++++++++++++++++++++++ FORMULARIOS HOSPITALARIOS+++++++++++++++++++++++++++++++++++++++++++**/
				/** ACCESO DIRECTO DESDE HCE
					MUESTRA EN PANTALLA LOS QUE SE PUEDE IMPRIMIR DE UN PACIENTE ACTIVO **/
					if( $wfacturacion == "off" and isset( $whis ) and isset( $wing ) AND $wsoloPacientesActivos == "on" ){
						echo "<input type='hidden' name ='wgrupo'       id ='wgrupo' 	   value='%'/>";
						echo "<input type='hidden' name ='whis' id='whis'  value='{$whis}'>";
						echo "<input type='hidden' name ='wing' id='wing'  value='{$wing}'>";
						echo "<input type='hidden' name ='wcco' id='wcco'  value='{$wcco}'>";
						echo "<input type='hidden' name ='wSolicitaCenimp' id='wSolicitaCenimp'  value='{$wSolicitaCenimp}'>";

						$paciente       = array();
						$paciente       = datosPaciente( $whis, $wing, "altaDefinitiva", "off" );
						if( $paciente['altaDefinitiva'] == "off" or  ( $paciente['altaDefinitiva'] == "on" and $paciente['horasDesdeAlta']*1 <= 6) ){//-->2016-06-21

							$tieneSolicitud = tieneSolicitudPendiente( $whis, $wing, $wmodalidad );
							$tieneSolicitud = $tieneSolicitud['existe'];
						    ( $tieneSolicitud ) ? $weditar = "si" : $weditar = "no";

						    if( $weditar == "si" )
								consultarDatosSolicitud( $whis, $wing, $wmodalidad );

							$formulariosDelPaciente   = existenFormulariosPaciente( $whis, $wing );
							( count( $formulariosDelPaciente ) > 0 ) ? $tieneFormulariosImprimir = true : $tieneFormulariosImprimir = false;
							
							$progAnexos = consultarScripts($conex,$whcebasedato,$whis,$wing);
							
							// if( $tieneFormulariosImprimir ){
							if( $tieneFormulariosImprimir || (count($progAnexos)>0)){
								echo '<center><div id="div_resultados" align="center" style="width:90%;">';
								$datosPaciente = mostrarEncabezadoPaciente( $whis, $wing, $empresa );
								echo $datosPaciente['encabezadoPaciente'];
								echo "<br>";
								echo mostrarPaquetes( $wmodalidad, '%', '%' );
								echo "<br><br><br>";
								echo mostrarArbolImpresion( $whis,  $wing );
								echo "<input type='hidden' name ='cadenaProgramasAnexos' id='cadenaProgramasAnexos'  value='".$cadenaProgramasAnexos."'>";
								echo "<br>";
								echo '</div></center>';
							}else{
									echo "<div>";
									echo '<br>';
									echo "<img src='../../images/medical/root/Advertencia.png'/>";
									echo "<br><br><div>NO EXISTEN FORMULARIOS DILIGENCIADOS PARA EL PACIENTE.</div><br><br>";
									echo '</div>';
									$errorEnBusqueda = true;
									return;
								}
						}else if( $paciente['altaDefinitiva'] == "" or !isset($paciente['altaDefinitiva']) or ($paciente['altaDefinitiva'] == "on") ){
							$errorEnBusqueda = true;
							echo "<div>";
							echo '<br>';
							echo "<img src='../../images/medical/root/Advertencia.png'/>";
							echo "<br><br><div>NO EXISTEN FORMULARIOS DILIGENCIADOS PARA EL PACIENTE.</div><br><br>";
							echo '</div>';
							return;
						}
					}else if( $wfacturacion == "off" and !isset( $whis ) and !isset( $wing ) ){ /** BUSQUEDA UNA HISTORIA Y UN INGRESO  **/
									echo "<input type='hidden' name ='wgrupo'       id ='wgrupo' 	   value='%'/>";
									if( $wsoloPacientesActivos =='off' or ( $wusuCenimp=='off' and $wenviaEmail=="off") )
										echo formularioConsultaPacientesEgresados();

									if( $wsoloPacientesActivos == "on" and ( trim( $wusuCenimp ) =="on" or $wenviaEmail == "on" ) )
										echo formularioConsultaPacientesActivos();

				}else if( $wfacturacion == "on" ){
					echo formularioConsultaFacturacion();
				}else{ /** espacio para desarrollar otras modalidades, de manera posterior **/

				}
				echo "</div>";//div de consulta
				echo '<center><div id="resultados_error" align="center" style="width: 712px"></div></center>';
				echo "<center><input type='button' id='btn_retornar1' onclick='retornar()' value='Retornar' bloquear='no' style='display:none;' /></center><br>";

				if( !isset( $whis ) and !isset($wing) )
					echo '<center><div id="div_resultados" align="center" style="width:90%; display:none;"></div></center>';
				//echo "<center><input type='button' name='btn_guardar' value='Generar Solicitud' {$mostrarBoton} bloquear='si' /></center><br>";

				( $wfacturacion == "off" and isset( $whis ) and isset( $wing ) AND $wsoloPacientesActivos == "on" and !$errorEnBusqueda) ? $mostrarBoton = "" : $mostrarBoton = "style='display:none;'";
				echo "<center><input type='button' name='btn_guardar' value='Generar Solicitud' {$mostrarBoton} bloquear='si' /></center><br><br>";
				echo "<div id='div_contenedor_pdf' align='center'></div>";
				echo "<center><input type='button' id='btn_retornar2' onclick='retornar()' value='Retornar' bloquear='no' style='display:none' /></center>";

				//echo '<center>';
				echo "<div style='width:90%; display:none;' id='contenedor_detalles_masivos'>";
				echo "<div align='left' style='cursor:pointer; font-size: 10pt;color:#2A5DB0;font-weight:bold;' onclick='mostrarocultarDiv( \"div_detalles\" )'>";
				echo "<img width='10' height='10' id='img_flecha' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;";
				echo "Solicitudes no generadas";
				echo "</div>";
				echo "<div align='center' class='BordeGris fila1'  id='div_detalles'>
						<table id='tbl_solicitudes_fallidas'>
							<tr class='encabezadotabla' tipo='encabezado'><td colspan='2'> SOLICITUDES NO GENERADAS </td></tr>
							<tr class='encabezadotabla' tipo='encabezado'><td> HISTORIA - INGRESO </td><td> CAUSA </td></tr>
						</table>
					 <br>
					 <center><input type='button' value='Aceptar' onclick='cerrarDetalleErrores();'></center>
					 <br>
					 </div>";
				echo "</div>";

				echo "<div style='width:90%;  id='contenedor_reporte'>";
				echo "<div align='left' style='cursor:pointer; font-size: 10pt;color:#2A5DB0;font-weight:bold;' onclick='mostrarocultarDiv( \"div_consultas\" ); consultarSolicitudesAnt(true);'>";
				echo "<br>";
				echo "<img width='10' height='10' id='img_flecha' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;";
				echo "Consultar Solicitudes";
				echo "</div>";
				echo "<div align='center' class='BordeGris' style='display:none;'  id='div_consultas'>";
				/*echo "<br>";
				consultarUltimasSolicitudes( date('Y-m-d') );
				echo "<br>";*/
				echo "</div>";
				echo "</div>";

				echo "<br>";

				echo "<div align='center' style='width:90%; {$verCerrar}'><input type=button id='cerrar_ventana' value='Cerrar Ventana' bloquear='no' onClick='javascript:cerrarVentana()' /></div>";
				echo "<br><br>";
				echo "<br><br>";
				//Mensaje de espera para generacion
				echo "<div id='msjEspereSolicitud' style='display:none;'>";
				echo "<br /><br />
					<img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Generando la solicitud (Espere un momento por favor, la operación puede tardar)...</font>
					<br /><br /><br />";
				echo "</div>";
				//Mensaje de espera para envio de correo.
				echo "<div id='msjEspereEmail' style='display:none;'>";
				echo "<br /><br />
					<img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Enviando el correo electr&oacute;nico (Espere un momento por favor, la operación puede tardar)...</font>
					<br /><br /><br />";
				echo "</div>";
				echo "<div id='msjEspere' style='display:none;'>";
				echo '<br>';
				echo "<img src='../../images/medical/ajax-loader5.gif'/>";
				echo "<br><br> Por favor espere un momento ... <br><br>";
				echo '</div>';
				//Mensaje de alertas
				echo "<div id='msjAlerta' style='display:none;'>";
				echo '<br>';
				echo "<img src='../../images/medical/root/Advertencia.png'/>";
				echo "<br><br><div id='textoAlerta'></div><br><br>";
				echo '</div>';
				echo '</center>';
				echo "<br><br><br>";
				echo "<input type='hidden' name ='weditar' 	 id='weditar' value='no'>";
				echo "<input type='hidden' name ='widenti' 	 id='widenti' value=''>";

				echo "<div id='div_formularios_completos' class='modal' style='width:100%; height:100%' class='fila1' align='center'>";
				echo "<br>
                         <input type='button' name='btn_cerrar_modal' value='Cerrar' onclick='cerrarModalFormularios();'>
                      <br>";
				echo imprimirArbolCompleto();
				echo "<br>
                         <input type='button' name='btn_cerrar_modal' value='Cerrar' onclick='cerrarModalFormularios();'>
                      <br>";
				echo "</div>";
				echo "<br /><br /><br /><br />
				        <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center; display:none;' id='div_sesion_muerta'>
				            [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
				        </div>";
				echo " <div id='div_dialogo' align='center' style='display:none;'>
				       </div>";
			?>
    </body>
</html>
