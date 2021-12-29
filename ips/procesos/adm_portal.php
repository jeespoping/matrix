<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tipos de turnero</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="facSer_style.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="facSer_js.js" type="text/javascript"></script>
	<!-- PESTAÑAS 
    <script>
        $( function() {
            $( "#tabs" ).tabs();
        } );
    </script> 
	-->
    <script>
	
	    function onChange(nomObj)
        {
			document.getElementById("accion")
			document.getElementById("accion").value = 'crear';
			document.getElementById("codNom").value = "Nuevo portal";
			document.getElementById("lblMsg").innerHTML  = '';
		}
		
		// Activa modo de creación de portal
        function activarNueva()
        {
			document.getElementById("accion").value = 'crear';
			document.getElementById("porNom").value = "Nuevo portal";
			// Eliminar datos que no sean útiles para el turnero que se va a crear.
			document.getElementById("lblMsg").innerHTML  = '';
			document.getElementById("lblId").innerHTML = '';
		}

        function cargarPortal()
        {
		
			var i;
			//var txt = document.getElementById("txTem" + tema.value).value;
			var txt = document.getElementById("selTema").value;
			//alert (txt);
			var tema = JSON.parse(txt);
			document.getElementById("accion").value = 'grabar';
			document.getElementById("lblMsg").innerHTML  = '';
			document.getElementById("lblId").innerHTML  = tema['Porcod'] + "-";
			// Textos
			document.getElementById("porCod").value = tema['Porcod'];
			document.getElementById("porNom").value = tema['Pornom'];
			document.getElementById("txtMbv").value = tema['Pormbv'];
			document.getElementById("txtMsl").value = tema['Pormsl'];

			// Desmarcar turneros.
			var arrChkTur = document.getElementsByClassName('chkTur');			
			for (i = 0; i < arrChkTur.length; i++) {
				var cod = arrChkTur[i].id.replace("lblchktur", ""); 
				document.getElementById("chktur" + cod).checked = false ;
			}
			
			// Marcar turneros del portal.
			var trd = tema['turneros'];
			// alert (trd);
			var arrTrd = trd.split(',');
			arrTrd.forEach( function(codtem, indice, array) {
				if (codtem!='')
				{
					//alert("En el índice " + indice + " hay este valor: chktur" + codtem);
					document.getElementById("chktur" + codtem).checked = true ;
				}
			});
		
        }
		
    </script> 
	
    <script type="text/javascript">
        $(document).ready(function(){
        });
    </script> 
    <style type="text/css">
		.chkTur {
		}

		input[type="checkbox"]:disabled {
		  background: #dddddd;
		}
		
		.lblMsgTxt {
			width:100%;
			color:DimGray;
			font-size:22px; 
			font-weight:normal; 
			text-align:center;
			background-color:white;
		}
		
		.inputTxt {
			border-radius:10px;
			border:1px solid #AFAFAF;
			width:100%;
			color:DimGray;
			font-size:22px; 
			font-weight:normal; 
			text-align:center;
			background-color:Lavender;
		}

		.btnAzul {
			-moz-border-radius: 12px;
			-webkit-border-radius: 12px;
			border-radius: 12px;
			color:white;
			font-size:18px; 
			font-weight:normal; 
			text-align:center;
			background-color:#428bca;
		}	

		.btnRojo {
			-moz-border-radius: 12px;
			-webkit-border-radius: 12px;
			border-radius: 12px;
			color:white;
			font-size:18px; 
			font-weight:normal; 
			text-align:center;
			background-color:red;
		}	

		.btnOk {
			-moz-border-radius: 12px;
			-webkit-border-radius: 12px;
			border-radius: 12px;
			color: rgba(0, 0, 0, 0);
			font-size:18px; 
			font-weight:normal; 
			text-align:center;
			background-color:green;
			background-image: url("ok.jpg");
			background-repeat: no-repeat;
			background-size: 30px 25px; /* contain */
		}	
		
		.container {
		  display: block;
		  position: relative;
		  padding-left: 35px;
		  margin-bottom: 12px;
		  cursor: pointer;
		  font-size: 20px;
		  font-weight: normal ; 
		  -webkit-user-select: none;
		  -moz-user-select: none;
		  -ms-user-select: none;
		  user-select: none;
		}
		
		.fondoGris {
			width:100%;
			color:DimGray !important;
			font-weight:bold; 
			text-align:center;
			background-color:Lavender !important;
		}
		
		/* Hide the browser's default checkbox */
		.container input {
		  position: absolute;
		  opacity: 0;
		  cursor: pointer;
		  height: 0;
		  width: 0;
		}

		/* Create a custom checkbox */
		.checkmark {
			 position: absolute;
			 top: 0;
			 left: 0;
			 height: 25px;
			 width: 25px;
			 background-color: #eee;
			 border-style: solid;
			 border-width: 1px;
			 border-color: #2196F3;
		}

		/* On mouse-over, add a grey background color */
		.container:hover input ~ .checkmark {
		  background-color: #ccc;
		}

		/* When the checkbox is checked, add a blue background */
		.container input:checked ~ .checkmark {
		  background-color: #2196F3;
		}

		/* Create the checkmark/indicator (hidden when not checked) */
		.checkmark:after {
		  content: "";
		  position: absolute;
		  display: none;
		}

		
		/* Show the checkmark when checked */
		.container input:checked ~ .checkmark:after {
		  display: block;
		}

		/* Style the checkmark/indicator */
		.container .checkmark:after {
		  left: 9px;
		  top: 5px;
		  width: 5px;
		  height: 10px;
		  border: solid white;
		  border-width: 0 3px 3px 0;
		  -webkit-transform: rotate(45deg);
		  -ms-transform: rotate(45deg);
		  transform: rotate(45deg);
		}
		
		.fila1
		{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2
		{
			background-color: 	#E8EEF7;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		.titulopagina2
		{
			border-bottom-width: 1px;
			/*border-color: <?=$bordemenu?>;*/
			border-left-width: 1px;
			border-top-width: 1px;
			font-family: verdana;
			font-size: 18pt;
			font-weight: bold;
			height: 30px;
			margin: 2pt;
			overflow: hidden;
			text-transform: uppercase;
		}
		.wn
		{
			font-weight: normal;
		}
    </style>
    <?php

    include("conex.php");
    include("root/comun.php");

    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }

    $user_session = explode('-', $_SESSION['user']);
    $wuse = $user_session[1];
    mysql_select_db("matrix");
    $conex = obtenerConexionBD("matrix");

	$wemp = "01";
	if (isset($wemp_pmla))
		$wemp = $wemp_pmla;
	$wemp_pmla = $wemp;
	
	$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp, 'cliame');

	$tblEnc = $wbasedatoCliame . "_000356"; // encabezado de portales de turneros
	$tblDet = $wbasedatoCliame . "_000357"; // detalle de portales de turneros

    $fecha_Actual = date('Y-m-d');  $hora_Actual = date('H:m:s');
    $ano_Actual = date('Y');        $mes_Actual = date('m');

	txtLog("", true);
	
	//txtLog("load FacSer_01-php: wuse $wuse, cCostos $cCostos", true);
	//$nuevaFactura = obtenerNumFactura($fuente,$cCostos ,$conex_o);  // para pruebas.
	
    $accion = $_POST['accion']; 
    $cod = $_POST['porCod']; 
	
	$nom = mysql_real_escape_string($_POST['porNom']); 
    $txtMbv = mysql_real_escape_string($_POST['txtMbv']); 
    $txtMlc = mysql_real_escape_string($_POST['txtMsl']); 
	
	txtLog("datos: accion $accion");
	if($accion == 'crear') {
		$sgte = getSgtePortal();
		$cod = str_pad($sgte, 2, '0', STR_PAD_LEFT);
		$sql = "insert into $tblEnc
					( Medico, Fecha_data, Hora_data, Seguridad, Porcod )
			values	( 'cliame', curdate(), curtime(), 'C-cliame', '$cod' )
		";
		txtLog($sql);
		mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());	
	}
	
	if($accion == 'grabar' || $accion == 'crear') {
		
		$sql = "update " . $tblEnc . "
			set Pornom = '$nom',
				Pormbv = '$txtMbv',
				Pormsl = '$txtMsl'
			where Porcod = '$cod'
		";
		txtLog($sql);
		mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
		
		// Turneros marcados.
		// Marcar como eliminados a todos los turneros asignados.
		$sql = "update $tblDet set Medico = 'x' where Porcod = '$cod'";
		txtLog($sql);
		mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());	
		$arr = getMaeTurneros();
		foreach ($arr as $reg) {
			$chk = 'chktur' . $reg['Codtem'];
			$chk = (strtolower($_POST[$chk])=='on'?'on':'off');
			if ($chk=='on') {
				// insertar si no existe
				$sql = "INSERT INTO $tblDet
							(Medico,Fecha_data,Hora_data,Porcod,Portur,Porord,Porest,Seguridad)
						SELECT * FROM (
							SELECT 'cliame',date(NOW()),time(NOW()),'$cod' AS Porcod,
									'".$reg['Codtem']."' as Portur, 99 AS Porord,
									'on' AS serest, 'C-cliame' AS Seguridad
						) AS tmp
						WHERE NOT EXISTS (
							SELECT Portur FROM $tblDet as c WHERE c.Porcod='$cod' AND c.Portur = '".$reg['Codtem']."'
						) LIMIT 1;
				";
				mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());	
				// Activar si ya estaba
				$sql = "update $tblDet set Medico = 'cliame' where Porcod = '$cod' AND Portur = '".$reg['Codtem']."'";
				txtLog($sql);
				mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());	
			}
		}
		// Eliminar los que quedan si asignar.
		$sql = "delete from $tblDet where Medico = 'x' and Porcod = '$cod'";
		txtLog($sql);
		mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());	
		
				
	}


    ?>
</head>

	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">

	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->

<body>
<?php
	// -->	ENCABEZADO
	encabezado("<div class='titulopagina2'>PORTAL TURNEROS</div>", $wactualiz, 'clinica');
?>
<div class="panel panel-info contenido" style="width: 98%; padding:20px;">

	<!--
    <div style="border-radius:10px;border:1px solid #B0D201">
        <div style="background-color:#B0D201;color:white;font-size:30px; font-weight:bold; text-align:center">
			TIPOS DE TURNERO
		</div>
    </div>
	-->
	
    <div align="center" class="panel panel-info divGeneral">
            <div class="divDatos" style="margin-top: 20px">
                    <form id="formHome" name="formHome" method="post" action="adm_portal.php" style="margin-top: 10px">
						<div id="divTemas">
							<!-- <label for="selTema"><h4>TURNERO</h4></label> -->
							<input type="hidden" id="accion" name="accion" value="grabar">
							<input type="hidden" id="porCod" name="porCod" value="">
							<input type="hidden" id="arrCat" name="arrCat">

							<label id="lblMsg" name="lblMsg" style="width:100%;color:DimGray;font-size:22px; font-weight:normal; text-align:center;
											background-color:white;">
											<?php 
												if ($accion=="crear") 
													echo"Tema creado correctamente"; 
												else if ($accion=="grabar") 
													echo"Tema actualizado correctamente";
												else
													echo"";
											?>
							</label>
							<table style="width: 100%;" cellspacing="20">
								<colgroup>
									<col span="1" style="width: 80%;">
									<col span="1" style="width: 20%;">
								</colgroup>
								<tr>
									<td>
										<select id="selTema" name="selTema" 
											style="border-radius:10px;border:1px solid #AFAFAF;width:90%;color:DimGray;
													font-size:25px; font-weight:bold; text-align:center;
													background-color:white; margin:0 10px 0 50px" 
													onchange="cargarPortal()"
										>
											<option disabled selected value> -- Seleccione portal -- </option>
											<?php
											// Llenar las opciones
											$arr = getOptPortales();
											foreach ($arr as $reg) {
													$cod = $reg['Porcod'];
													$nom = $reg['Porcod'] .'-'. $reg['Pornom'];
													$json = json_encode($reg);
													echo "<option value='$json' style='text-align:center;text-align-last: center;'>$nom</option>";
											}
											?>
										</select>
									</td>
									<td>
										<input type="button" id="btnAdd" style="border-radius:10px;border:1px solid #428bca;color:white;
													font-size:20px; font-weight:normal; text-align:center; width:80%;
													padding:0 5px 0 5px;  
													background-color:#428bca;"  value="Adicionar" onclick="activarNueva()">
									</td>
								</tr>
							</table>
							
						</div>
						<br>
						<table style="width:90%;" cellspacing="5">
							<colgroup>
								<col span="1" style="width: 20%;">
								<col span="1" style="width: 5%;">
								<col span="1" style="width: 75%;">
							</colgroup>
							<tr>
								<td>
								<label style="width:100%;color:DimGray;font-size:22px; font-weight:normal; text-align:center;
											background-color:white;">
										 Nombre
								</label>
								</td>
								<td>
								<label id="lblId" name="lblId" style="width:100%;color:DimGray;font-size:22px; font-weight:normal; text-align:right;
											background-color:white;">
										 00-
								</label>
								</td>
								<td>
								<input type="text" id="porNom" name="porNom" class="inputTxt" value="">
								</td>
							</tr>
						</table>
						<br>
						
						<label class="fondoGris" style="font-size:20px;">Personalizaci&oacute;n de mensajes</label>
						<br><br>
						<table style="width:90%;" cellspacing="5">
							<colgroup>
								<col span="1" style="width: 100%;">
							</colgroup>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Bienvenida</label>
								<br>
								<textarea id="txtMbv" name="txtMbv" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Selección</label>
								<br>
								<textarea id="txtMsl" name="txtMsl" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>

							<tr><td><br></td></tr>

						</table>
						<br>
						
						<label class="fondoGris" style="font-size:25px;">Turneros
						</label>
						<table style="width:35%">
							<colgroup>
								<col span="1" style="width: 100%;">
							</colgroup>
							<tr><td>
							</td></tr>
							
							<?php
								// Checkbox con los turneros disponibles
								$arr = getMaeTurneros('on');
								// Contar los que se podrán mostrar
								$cant = 0;
								foreach ($arr as $reg) {
									if (trim($reg['Codnom'])!='') $cant++;
								}

								//echo "cant arr $cant mitad " . ceil($cant/2);
								foreach ($arr as $reg) {
									$nom = trim($reg['Codnom']);
									//echo "$nom,";
									if ($nom!='') {
										$cod = $reg['Codtem'];
										$json = json_encode($reg);
										echo "<tr><td>
											<label id='lblchktur$cod' name='lblchktur$cod' class='container chkTur'>$nom
												<input id='chktur$cod' name='chktur$cod' type='checkbox' class='chk'>
												<span class='checkmark'></span>
											</label>
										</td></tr>";
									}
								}
							?>

								
						</table>
						<br>
						<input type="submit" style="border-radius:10px;border:1px solid #428bca;color:white;
										font-size:20px; font-weight:normal; text-align:center; 
										padding:8px; background-color:#428bca;width:20%;display:block;margin:auto;" value="GRABAR">
                    </form>
            </div>
			
    </div>
</div>

<?php
////////////FUNCIONES:

// Busca el siguiente código disponible de portal
function getSgtePortal()
{
	global $conex, $wemp, $wbasedatoCliame, $tblEnc;
	
	$sql = "select (Porcod * 1) as maxcod
		from $tblEnc
		order by (Porcod * 1) desc
	";
	txtLog ("$sql");
	$max = 0;
	$rs = mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
	if($rowDoc = mysql_fetch_array($rs)){
		txtLog (json_encode($rowDoc));
		$max = $rowDoc['maxcod'] + 1;
	}
	txtLog ("max $max");
	return $max;
}

// Carga los turneros disponibles
function getMaeTurneros($estado='')
{
	global $conex, $wemp, $wbasedatoCliame; // Codsgt, Codtrd
	
	$sql = "select *
		from " . $wbasedatoCliame . "_000305 
	" . ($estado==''?'':" where Codest='$estado'") 
	. " order by Codnom asc";
	//txtLog ("$sql");
	$arr = array();
	$rs = mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
	while($rowDoc = mysql_fetch_array($rs)){
		//txtLog (json_encode($rowDoc));
		//txtLog ("SGT: " . $rowDoc['Codsgt']);
		//$arr[] = $rowDoc;
		$arr[] = array (
			"Codtem" => utf8_encode($rowDoc['Codtem']),
			"Codnom" => utf8_encode(rplcSpecialChar($rowDoc['Codnom'])),
		);
	}
	return $arr;

}

// Carga los turneros de un portal y retorna string con los códigos
function getTurneros($portal,$estado='')
{
	global $conex, $tblDet; // Codsgt, Codtrd
	
	$sql = "select * from " . $tblDet . " where Porcod ='$portal' order by Porcod asc";
	// txtLog ("$sql");
	$turneros="";
	$rs = mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
	while($rowDoc = mysql_fetch_array($rs)){
		$turneros .= "," . utf8_encode($rowDoc['Portur']) ;
	}
	if (strlen($turneros) > 1)
		$turneros = substr($turneros,1);
	return $turneros;
}


// Carga los temas activos y retorna arreglo con los campos
function getOptPortales($estado='')
{
	global $conex, $wemp, $wbasedatoCliame, $tblEnc, $tblDet; // Codsgt, Codtrd
	
	$sql = "select * from " . $tblEnc . " order by Pornom asc";
	//echo "<br>$sql";
	txtLog ("$sql");
	$arr = array();
	$rs = mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
	while($rowDoc = mysql_fetch_array($rs)){
		//txtLog (json_encode($rowDoc));
		//txtLog ("SGT: " . $rowDoc['Codsgt']);
		//$arr[] = $rowDoc;
		$turneros = getTurneros($rowDoc['Porcod']);
		$arr[] = array (
			"Porcod" => utf8_encode($rowDoc['Porcod']),
			"Pornom" => utf8_encode(rplcSpecialChar($rowDoc['Pornom'])),
			"Porlog" => utf8_encode($rowDoc['Porlog']),
			"Pormbv" => utf8_encode(rplcSpecialChar($rowDoc['Pormbv'])),
			"Pormsl" => utf8_encode(rplcSpecialChar($rowDoc['Pormsl'])),
			"Porest" => utf8_encode($rowDoc['Porest']),
			"turneros" => $turneros,
		);
		//echo "<br>tem " . utf8_encode($rowDoc['Codnom']) . " url: " . utf8_encode($rowDoc['Codsgt']);
	}
	// echo "<br>" . json_encode($arr, JSON_PRETTY_PRINT);
	// txtLog (json_encode($arr, JSON_PRETTY_PRINT));
	return $arr;
}

// Obtiene de $cadena, la linea $num, de $longitud máxima
function lineaDeString ($cadena, $num, $longitud)
{
	$linea = 1;
	$str = "";
	$arr = explode(' ',$cadena);
	foreach($arr as $palabra) {
		if (strlen(trim($str . " " . $palabra)) > $longitud) {
			if ($linea == $num) {  // retornar la línea construída
				break;
			}
			$linea++;
			$str = $palabra;
		}
		else
			$str = $str . ' ' . $palabra;
		//echo("<br>$str");
	}
	if ($linea < $num)
		$str = "";
	return $str;
}

function obtenerDatosUsuario($parametro,$wuse,$conex)
{
    switch($parametro)
    {
        case 1:
            $query1 = "select * from usuarios WHERE Codigo = '$wuse'";
            $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
            $dato1 = mysql_fetch_array($commit1);   $cCostosUsuario = $dato1['Ccostos'];
            return $cCostosUsuario;
            break;
    }
}


function txtLog($txt, $inicializar=false)
{
        try {
                $l = date('H:i:s', time()) . ' ' . $txt . "\n";
				if ($inicializar)
					file_put_contents('log_la.txt', $l, LOCK_EX);
				else
					file_put_contents('log_la.txt', $l, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
        }
}

function rplcSpecialChar($txt)
{
    $t = str_replace("Ñ", "&Ntilde;", $txt);
    $t = str_replace("ñ", "&ntilde;", $t);
    $t = str_replace("Á", "&Aacute;", $t);
    $t = str_replace("á", "&aacute;", $t);
    $t = str_replace("É", "&Eacute;", $t);
    $t = str_replace("é", "&eacute;", $t);
    $t = str_replace("Í", "&Iacute;", $t);
    $t = str_replace("í", "&iacute;", $t);
    $t = str_replace("Ó", "&Oacute;", $t);
    $t = str_replace("ó", "&oacute;", $t);
    $t = str_replace("Ú", "&Uacute;", $t);
    $t = str_replace("ú", "&uacute;", $t);
    return $t;
}
?>
<script>
    const number = document.querySelector('.tvc2');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number.addEventListener('focus', (e) => {
        const element = e.target;
    const value = element.value;
    element.value = formatNumber(value);
    })

    const number2 = document.querySelector('.tvd2');
    function formatNumber2 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number2.addEventListener('focus', (e) => {
        const element = e.target;
    const value2 = element.value;
    element.value = formatNumber2(value2);
    })

    const number3 = document.querySelector('.tvn2');
    function formatNumber3 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number3.addEventListener('focus', (e) => {
        const element = e.target;
    const value3 = element.value;
    element.value = formatNumber3(value3);
    })
</script>
</body>
</html>