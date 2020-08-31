<html>
<head>
  <title>MATRIX - [REPORTE PACIENTES ADMITIDOS]</title>
  
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script>   

<script type="text/javascript">
function consultar() {
	document.forma.submit();
}

var celda_ant="";
var celda_ant_clase="";
	 
$(document).ready( function (){	
	$("#tblpaciente").stickyTableHeaders();
});

	function ilumina(celda,clase){
		if (celda_ant=="") {
			   celda_ant = celda;
			   celda_ant_clase = clase;
		}
		celda_ant.className = celda_ant_clase;
		celda.className = 'fondoAmarillo';
		celda_ant = celda;
		celda_ant_clase = clase;
	}

	function iluminacolumna(celda,columna)
	{
		$("td.fondoAmarillo").removeClass('fondoAmarillo');
		$("."+columna).addClass("fondoAmarillo");      
	}

</script>
<style type="text/css">
#global {
	height: 45%;
	width: 100%;
	border: 1px solid #ddd;
	background: #f1f1f1;
	overflow-y: scroll;
}
</style>
</head>
<body style='width: 95%'>

<?php
include_once("conex.php");

/**
 * NOMBRE:  REPORTE PACIENTES ADMITIDOS
 *
 * PROGRAMA: RepPacAdm.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION: Este reporte presenta un detalle de pacientes admitidos en un rango de fechas con informacion general.
 *
 * HISTORIAL DE ACTAULIZACIONES:
 * 2017-05-12 Verónica Arismendy se modifica el archivo para modificar la consulta para dejar una sola consulta donde solo se cambia el where dependiendo del estado y se agregaron también
   los campos: Tipo de servicio,  Número de telefono Móvil, Correo electrónico, Municipio Residencia, Zona Geográfica, Destino, Codigo servicio autorizado , Cobertura en salud 
   Se modifica también el diseño de la presentación de la tabla y se agrega color a la fila cuando le dan click
 * 2016-07-14 Camilo Zapata, corrección de script para que muestre el código de responsable para los particulares sin importar si se consultan, activos, inactivos o todos
 * 2016-06-23 Camilo Zapata, se agrega la columna de destino para facilitar la localizacion de aquellos pacientes que no tengan factura. buscar "consultar_destino_admision" de ser necesario
 * 2016-04-01 Camilo zapata, modificación para que escriba "PARTICULAR" cuando el código de la empresa de la tabla 101 sea igual al código para particulares(root-51)
 * 2006-12-12 Juan David Jaramillo, creacion del script
 * 2007-04-10 Juan David Londoño, se agrego en el servicio la descripcion del centro de costo y se alinearon a la izquierda
 * 			  los campos de texto.
 * 2009-11-24 MSanchez:: Se actualiza segun el patron de la libreria comun.php, se reforma la vista del reporte y se añaden las siguientes columnas:
 * 				1.  Usuario que admite, Numero de factura, Fecha factura, valor factura y usuario que factura.
 * 2010-01-20 Edwin Molina Grisales.  Se cambia el query para el usuario que admite el ingreso (ingusu)
 * 2015-11-06 Jessica Madrid.  Se agrega la fecha del egreso y quien lo realiza.
 *
 * Tablas que utiliza:
 * $wbasedato."_000100: Maestro de Historias Clinicas
 * $wbasedato."_000101: Maestro de Ingresos de HC
 * $wbasedato."_000018: Facturación
 *
 * @author jjaramillo
 * @package defaultPackage
 */
//=================================================================================================================================
include_once("root/comun.php");
session_start();

if(!isset($_SESSION['user'])){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} else {
	$wactualiz = " 2017-05-12 ";

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$key 		 = substr($user,2,strlen($user));
	$conex		 = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$wbasedato 	 = strtolower($institucion->baseDeDatos);
	$wentidad 	 = $institucion->nombre;
	$wfecha		 = date("Y-m-d");
	
	echo "<form action='RepPacAdm.php' method=post name='forma'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	
	encabezado("Reporte pacientes admitidos",$wactualiz,"logo_".$wbasedato);//Encabezado

	if (!isset($wfecini) or !isset($wfecfin) or !isset ($resultado)) {
		//Cuerpo de la pagina
		echo "<table align='center' border=0>";

		//Ingreso de fecha de consulta
		echo '<span class="subtituloPagina2">';
		echo 'Par&aacute;metros de consulta';
		echo "</span>";
		echo "<br>";
		echo "<br>";

		//INGRESO DE VARIABLES PARA EL REPORTE//
		if (!isset ($bandera)) {
			$wfecini=$wfecha;
			$wfecfin=$wfecha;
		}

		//Fecha inicial
		echo "<tr><td class='fila1' width=150>Fecha inicial</td>";
		echo "<td class='fila2' align='center' width=150>";
		if(isset($wfecini) && !empty($wfecini)){
			campoFechaDefecto("wfecini",$wfecini);
		} else {
			campoFecha("wfecini");
		}
		echo "</td></tr>";

		//Fecha final
		echo "<tr><td class='fila1'>Fecha final</td>";
		echo "<td class='fila2' align='center'>";
		
		if(isset($wfecfin) && !empty($wfecfin)){
			campoFechaDefecto("wfecfin",$wfecfin);
		} else {
			campoFecha("wfecfin");
		}
		
		echo "</td></tr>";

		//SELECCIONAR estado a consultar
		echo "<tr><td class='fila1'>Estado del paciente</td>";
		echo "<td class='fila2' align='center'>";
		echo "<select name='west' class=seleccionNormal>";

		if (isset ($west)) {
			if ($west=='ACTIVO') {
				echo "<option>ACTIVO</option>";
				echo "<option>INACTIVO</option>";
				echo "<option>TODOS</option>";
			}
			if ($west=='INACTIVO') {
				echo "<option>INACTIVO</option>";
				echo "<option>ACTIVO</option>";
				echo "<option>TODOS</option>";
			}
			if ($west=='TODOS') {
				echo "<option>TODOS</option>";
				echo "<option>ACTIVO</option>";
				echo "<option>INACTIVO</option>";
			}
		} else {			
			echo "<option>ACTIVO</option>";
			echo "<option>INACTIVO</option>";
			echo "<option>TODOS</option>";
		}
		
		echo "</select></td></tr>";
		
		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

		echo "<tr><td colspan=2 align='center'>";
		echo "<br><input type='button' name='comprobante' value='Generar' onClick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'>";
		echo "</b></td></tr></table></br>";
		
	} else {
		echo "<table align=center width='60%'>";		
		echo "<tr><td colspan='3' align='center'><A href='RepPacAdm.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;west=".$west."&amp;bandera='1'>Volver</A>&nbsp;|&nbsp;
		<A href='javascript:cerrarVentana();'>Cerrar</A>
		<td>";		
		echo "</tr>";

		echo "<tr><td><tr><td><b>Fecha inicial:</b> ".$wfecini."</td>";
		echo "<td><b>Fecha final:</b> ".$wfecfin."</td>";
		echo "<td><b>Estado:</b> ".$west."</td>";
		echo "</table></br><br>";

		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
		echo "<input type='HIDDEN' NAME= 'west' value='".$west."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		if($west=="ACTIVO"){
			$wcest = "on";
		} else {
			if($west=="INACTIVO")
				$wcest="off";
			else $wcest="T";
		}
		
		if($wcest=="T") {
			//todos
			$sqlWhere = "ingfei BETWEEN '".$wfecini."' AND  '".$wfecfin."'";
			
		} else {			
			//--> busca egresados
			if( $wcest == 'off' ){
				$sqlWhere = "ingfei BETWEEN '".$wfecini."' AND  '".$wfecfin."' 
				AND pacact ='".$wcest."'";				
			}

			//--> busca pacientes activos
			if( $wcest == 'on' ){				
				$sqlWhere = "ingfei BETWEEN '".$wfecini."' AND  '".$wfecfin."' 
				AND pacact ='".$wcest."' 
				AND d.id is null";	
			}
		}
		
		//consulta general para los tres tipos de estados filtrada por el where dependiendo de cada uno de ellos
		$q = "  SELECT  pacdoc , concat(pacno1,' ',pacno2, ' ',pacap1,' ',pacap2 ) as nombrePac , pacdir ,pactel 
						, ingnin ,ingent ,ingfei ,ingsei ,pacact ,Ccodes ,Pactdo ,inghis
						, (SELECT Descripcion FROM usuarios WHERE codigo = ".$wbasedato."_000101.ingusu) as usuario
						, (SELECT Descripcion FROM usuarios WHERE codigo = SUBSTR(d.seguridad,3)) usuegreso
						, ingcem codigoEmpresa
						, pacmov as numcel, paccor as email, rg.Seldes as regimen , z.Zogdes as zona, mn.Nombre as municipio
						, ds.Seldes as destino, ingcac, egrfee, ts.Seides as tipoServ
		      	FROM  ".$wbasedato."_000100
		      	INNER JOIN " . $wbasedato . "_000101 on ( pachis = inghis)
				INNER JOIN " . $wbasedato . "_000003 on ( ingsei = Ccocod )
		      	LEFT JOIN " . $wbasedato . "_000108 d on ( Egrhis = inghis AND Egring = Ingnin )
				LEFT JOIN " . $wbasedato . "_000256  z ON z.Zogcod = paczog 
				LEFT JOIN " . $wbasedato . "_000105 rg ON rg.Selcod = pactus AND rg.Seltip = '06'
				LEFT JOIN root_000006 mn ON mn.Codigo = pacmuh
				LEFT JOIN " . $wbasedato . "_000105 ds ON ds.Selcod = ingdes and ds.Seltip = '17' and ds.Selest='on'
				LEFT JOIN " . $wbasedato . "_000174 ts on ts.Seiuni = pactam
		      	WHERE 	" . $sqlWhere . "						
				ORDER BY ingfei, ingnin ";
		
		$err = mysql_query($q,$conex)or die(mysql_error());
		$num1 = mysql_num_rows($err);

		$wtotpac = 0;

		$consultarDestino =  consultarAliasPorAplicacion($conex, $wemp_pmla, "consultar_destino_admision");

		$clase="fila2";
		if ($num1>0) {
			echo "<table align=center border=0 id='tblpaciente'>";
			echo "<thead>";
				echo "<tr class=encabezadoTabla>";
				echo "<th align=CENTER width=20>TIPO Documento</th>";
				echo "<th align=CENTER width=100>Documento</th>";
				echo "<th align=CENTER>Paciente</th>";				
				echo "<th align=CENTER>Direccion</th>";					
				echo "<th align=CENTER>Municipio</th>";					//agregado 2017-05-12
				echo "<th align=CENTER>Zona</th>";						//agregado 2017-05-12
				echo "<th align=CENTER>Telefono</th>";
				echo "<th align=CENTER>No Celular</th>";				//agregado 2017-05-12
				echo "<th align=CENTER>Correo electrónico</th>";		//agregado 2017-05-12
				echo "<th align=CENTER>Historia e ingreso</th>";
				echo "<th align=CENTER>Responsable</th>";
				echo "<th align=CENTER>Regímen</th>";					//agregado 2017-05-12	
				echo "<th align=CENTER width=80>Fecha ingreso</th>";
				echo "<th align=CENTER>Servicio</th>";
				echo "<th align=CENTER>Tipo de Servicio</th>";			//agregado 2017-05-12
				echo "<th align=CENTER>Admisi&oacute;n realizada por</th>";		
				
				if( $wcest != "on" ){				
					echo "<th align=CENTER>Fecha Egreso</th>";
					echo "<th align=CENTER>Egreso realizado por</th>";
				}
					
				echo "<th align=CENTER>Factura</th>";
				echo "<th align=CENTER>Fecha Factura</th>";
				echo "<th align=CENTER>Valor</th>";
				echo "<th align=CENTER>Usuario Factura</th>";					
				echo "<th align=CENTER>Destino</th>";					//agregado 2017-05-12 visible siempre
				echo "<th align=CENTER>CUPS</th>";						//agregado 2017-05-12
				echo "</thead>";
				
				
				echo "<tbody>";
				for ($j=0;$j<$num1;$j++) {
					
					$row = mysql_fetch_array($err);

					if($clase == "fila1"){
						$clase = "fila2";
					} else {
						$clase = "fila1";
					}

					if($row[8]=="on")
						$west="ACTIVO";
					else
						$west="INACTIVO";

					//Consulta de las facturas asociadas al paciente en el rango de fechas seleccionado
					$q = "SELECT
								Fenfac, Fenfec, Fenval, (SELECT Descripcion FROM usuarios WHERE codigo = SUBSTRING(Seguridad FROM INSTR(Seguridad,'-')+1) ) usrFactura
						FROM
								clisur_000018
						WHERE
								Fenest = 'on'
								AND Fenhis = '" . $row["inghis"] . "'
								AND Fening = '" . $row["ingnin"] . "'
								AND Fenfec >= '".$wfecini."'";

					$err2 = mysql_query($q,$conex);
					$num2 = mysql_num_rows($err2);

					/*******************************************************
					 * TODO: PENDIENTE PENDIENTE
					 *******************************************************/
					
					$facturacion    = "";
					$fechaFactura   = "";
					$valorFactura   = "";
					$usuarioFactura = "";

					if($num2 > 0){
						for ($l=0;$l<$num2;$l++) {
							$row2 = mysql_fetch_array($err2);

							//Conformacion del string de la facturacion
							//$facturacion .= $row2['Fenfac']." ".$row2['Fenfec']." $".number_format($row2['Fenval'],0,'.',',')." ".$row2['usrFactura']."<br>";
							if( $l > 0 ){
								$facturacion    = $facturacion."<br>----------------------<br>".$row2['Fenfac'];
								$fechaFactura   = $fechaFactura."<br>----------------------<br>".$row2['Fenfec'];
								$valorFactura   = $valorFactura."<br>----------------------<br> $".number_format($row2['Fenval'],0,'.',',');
								$usuarioFactura = $usuarioFactura."<br>----------------------<br>".$row2['usrFactura'];
							}else{
								$facturacion    = $row2['Fenfac'];
								$fechaFactura   = $row2['Fenfec'];
								$valorFactura   = " $".number_format($row2['Fenval'],0,'.',',');
								$usuarioFactura = $row2['usrFactura'];
							}
						}
					}

					/*
					 * SE ENCUENTRAN PENDIENTES DOS COSAS EN ESTE REPORTE:
					 *
					 * 1.  Que se actualice el campo seguridad en la tabla clisur_000101 con el codigo del usuario que hace la admision.
					 * 2.  Averiguar si las facturas se muestran agrupadas o una por una.
					 */
					$codigoParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, "codigoempresaparticular");
					if(  trim($row[5]) == "" and trim($row['codigoEmpresa']) == $codigoParticular ){
						$row[5] = "PARTICULAR";
					}
					
					echo "<tr class='$clase' onclick='ilumina(this,\"".$clase."\")'</tr>";
						echo "<td align='center'>" . $row[10] . "</td>";
						echo "<td align='center'>" . $row["pacdoc"] . "</td>";		 					//Número de documento
						echo "<td align=left>"	   . $row["nombrePac"]."</td>";		 					//Nombre del paciente			
						echo "<td align=left>"	   . $row["pacdir"]."</td>";		 					//Dirección del paciente
						echo "<td align=left>"	   . $row["municipio"]."</td>";		 					//Municipio residencia
						echo "<td align=left>"     . $row["zona"]."</td>";			 					//Zona residencia
						echo "<td align=center>"   . $row["pactel"]."</td>";	    					//Telefono paciente
						echo "<td align=center>"   . $row["numcel"]."</td>";		 					//Número Celular agregado 2017-05-12
						echo "<td align=center>"   . strtolower($row["email"])."</td>";					//Correo Electronico  agregado 2017-05-12
						echo "<td align=center>"   . $row["inghis"] . "-" . $row["ingnin"] . "</td>";	//Historia-Ingreso				
						echo "<td align=left>"	   . $row["ingent"]."</td>";							//Entidad responsable
						echo "<td align=left>"	   . $row["regimen"]."</td>";							//Regimen
						echo "<td align=center>"   . $row["ingfei"]."</td>";							//Fecha de ingreso
						echo "<td align=left>"	   . $row["ingsei"]."-".$row["Ccodes"]."</td>";			//servicio 
						echo "<td align=left>"	   . $row["tipoServ"] ."</td>";			//Tipo de 
						echo "<td align=left>"	   . strtoupper($row["usuario"])."</td>";				//Realizó admisión					
						if( $wcest != "on" ){
						// if( $wcest == "off" ){
							// echo "<td align=CENTER>Fecha Egreso</td>";
							echo "<td align=CENTER>".$row["egrfee"]."</td>";
							echo "<td align=CENTER>".strtoupper($row["usuegreso"])."</td>";
						}
						//echo "<td align=left>$facturacion</td>";
						echo "<td align=CENTER>$facturacion</td>";
						echo "<td align=CENTER>$fechaFactura</td>";
						echo "<td align=CENTER>$valorFactura</td>";
						echo "<td align=CENTER>".strtoupper($usuarioFactura)."</td>";
						//if( $consultarDestino == "on" ){
							echo "<td align=left>"	   . $row["destino"]."</td>";									
						//}
						
					///////////Se consultan los codigos cups asociados por registro
						$sqlCups = "SELECT r.Codigo, r.nombre 
						FROM " . $wbasedato . "_000209 c
						INNER JOIN root_000012 r ON r.Codigo = c.Cprcup
						WHERE Cprhis = ".$row["inghis"] . "
						AND Cpring = " . $row["ingnin"];
						
						$resCups = mysql_query($sqlCups,$conex);
						
						$strCups = "";
						while ($rowCups = mysql_fetch_assoc($resCups)){
							//$strCups .= $rowCups["Codigo"] . "<br>";
							$strCups .= $rowCups["Codigo"] . " - ". utf8_encode($rowCups["nombre"])  . "<br>";
						}
						
						echo "<td align=left>"	   . $strCups ."</td>";		
						
					///////fin consulta cups
											
					echo "</tr>";
					$wtotpac=$wtotpac+1;
				} //End for
				
				echo "</tbody>";
		}

		if ($wtotpac==0) {
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin Pacientes Admitidos en el rango de fechas seleccionado</td><tr>";
		} else if ($wtotpac>0) {
			echo "<tr class=encabezadoTabla><td align=CENTER colspan=4>TOTAL PACIENTES</th>";
			echo "<td align=center>".number_format($wtotpac,0,'.',',')."</td>";
			echo "<td colspan=19></td></tr>";

		}
		echo "</table>";
	
		echo "</br><center>";
		echo "<A href='RepPacAdm.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;west=".$west."&amp;bandera='1'>Volver</A>&nbsp;|&nbsp;";
		echo "<A href='javascript:cerrarVentana();'>Cerrar</A>";
		echo "</center>";
	}
}
liberarConexionBD($conex);
?>
</body>
</html>
