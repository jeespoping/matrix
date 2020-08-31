
<?php
include_once("conex.php");
/***************************************************
 PROGRAMA                   : rep_PacientesEgresadosActivosOrdenes.php
 AUTOR                      : Jonatan Lopez.
 FECHA CREACION             : 08 de Noviembre de 2014

 DESCRIPCION:
 Muestra los pacientes que egresan para un servicio seleccionado o todos, permitiendo imprimir las ordenes de medicamentos y examenes.

 CAMBIOS:
   //================================================================================================================================================
	  *** Abril 2 de 2020 Jessica Madrid Mejía
	  Se agregan parámetros al llamado de ordenes_imp.php para envío de las ordenes por correo
   //================================================================================================================================================
	  *** Febrero 17 de 2016 Veronica Arismendy 
	  Se modifica la consulta para agregar las variables de movhos y hce y quitar los valores quemados.
   //================================================================================================================================================
	  *** Febrero 12 de 2016 Veronica Arismendy 
	  Se modifica el archivo para agregar una ventana modal que muestre las opciones para configurar la impresión permitiendole al usuario seleccionar 
	  que tipo de ordenes desea imprimir y el orden cronólogico en el cual desea que se muestren.
   //================================================================================================================================
   // *** 2015-09-14 Edwin MG
   // En la consulta principal se agrega el filtro de Oriori para subconsulta de examenes y procedimientos
   //================================================================================================================================
   // *** 2015-09-14
   // Se agrega a la consulta de la tabla 53 de movhos, la consulta a la tabla 27 y 28 de hce, esto porque el paciente puede que no tenga
   // ordenes pero si tenga ayudas diagnosticas.
   //================================================================================================================================
   // *** 2015-06-16 Jonatan
   // Se agrega la variable $origen, si esta en "on" imprimira las ordenes en pdf.
   //================================================================================================================================
   // *** 2015-04-08 Camilo
   // Se modifico el reporte para que use la tabla 18, consultando pacientes activos en un centro de costos consultado o los ingresos de una
   // historia sin importar si está egresado o no. Ya no se consulta en rango de fechas, sino simplemente estado actual( pacientes actualmente en el cco y 
   // no ingresados en el rango de fechas)
   //================================================================================================================================
   //================================================================================================================================
   // *** Marzo 25 de 2015 Jonatan
   // Se resta un dia a la fecha de inicio en el formulario.
   //================================================================================================================================
   // *** Marzo 12 de 2015 Jonatan
   // Se cambia e filtro de fecha inicial y final en urgencias ya que consultaba sobre la hce_000022, debe cosnultar sobre la movhos_000053.
   //================================================================================================================================
   // *** Enero 22 de 2015 : Jonatan
   // Se agrega campo de historia para que puedan buscar por una historia en especifico sin tener en cuenta las fechas.
   //================================================================================================================================
   // *** Dic. 22 de 2014 : Juan C. Hernández :
   // Se modifica para que tome los ingresos hechos por Urgencias sumados a los ingresos de hospitalización tanto por admisiones como
   // de traslados entre servicios
   //================================================================================================================================

*////////////////////

if(!isset($_SESSION['user'])){
echo "error";
return;
}




include_once("root/comun.php");



$wactualiz = "2020-04-02";
$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$whce =  consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
echo "</head>";
echo '<body BGCOLOR="" TEXT="#000000">';

function consultar_ordenes($wfecha_i,$wfecha_f,$wcco0,$whis, $origen){
	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $whce;

	$whis = trim($whis);

	if($whis != '' ){

		$filtro_historia = "AND Karhis='".$whis."'";
		$filtro_historia_orden = "AND Ordhis='".$whis."'";		

	}

	//echo "<center class=titulo >PACIENTES INGRESADOS </center>";
	echo "<br/>";
	$wcco1 = explode("-",$wcco0);

	//Busco si el centro de costos es de Urgencias
	$q = " SELECT COUNT(*) "
	    ."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$wcco1[0]."'"
		."    AND ccourg = 'on' ";
	$res= mysql_query($q, $conex);
	$row = mysql_fetch_row($res);
	if ($row[0] > 0)
	   $wurg="on";
	   else
          $wurg="off";

		$condicionHistoria       = "";
		$condicionAltaDefinitiva = " Ubiald = 'off' AND Ubifad = '0000-00-00'";
		$orderBy                 = "ORDER BY ccocod, fecha_ingreso, hora_ingreso";

		if($whis != ''){
			$condicionHistoria       = " F.ubihis = '{$whis}'";
			$condicionAltaDefinitiva = "";
			$orderBy                 = "ORDER BY fecha_ingreso desc, hora_ingreso desc";
		}

		($wcco0 != "todos" or $wurg == "on") ?  $condicionCco = " AND F.ubisac = '{$wcco1[0]}'" : $condicionCco = "";

		$temp18 = "temp18".date("Y_m_d_His");
		$qaux = "DROP TABLE IF EXISTS $temp18";
		$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
		$qtemp = "CREATE TEMPORARY TABLE IF NOT EXISTS {$temp18}
						(INDEX idx(Ubihis, Ubiing))";
		$qtemp .= " SELECT Fecha_data, Hora_data, Ubihis, ubiing, ubisac
		             FROM {$wbasedato}_000018 F
		            WHERE {$condicionAltaDefinitiva}
		            	  {$condicionHistoria}
		                  {$condicionCco}
		            GROUP BY 1, 2, 3, 4 ";
		$rstemp = mysql_query( $qtemp );
		
		// Auxiliar identica a la anterior para unirla en la consulta.
		$temp18_aux = "temp18_aux".date("Y_m_d_His");
		$qaux1 = "DROP TABLE IF EXISTS $temp18_aux";
		$resdr1 = mysql_query($qaux1,$conex) or die (mysql_errno().":".mysql_error());
		$qtemp1 = "CREATE TEMPORARY TABLE IF NOT EXISTS {$temp18_aux}
						(INDEX idx(Ubihis, Ubiing))";
		$qtemp1 .= " SELECT Fecha_data, Hora_data, Ubihis, ubiing, ubisac
		             FROM {$wbasedato}_000018 F
		            WHERE {$condicionAltaDefinitiva}
		            	  {$condicionHistoria}
		                  {$condicionCco}
		            GROUP BY 1, 2, 3, 4 ";
		$rstemp1 = mysql_query( $qtemp1 );

		//---> cambio, se une para que busque en la tabla 27 y 28 de hce ya que es posible que no tenga encabezado de medicamentos, tabla movhos_00005354
		$q = " SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.Ubisac as ccocod, G.cconom,
		                F.Ubihis as historia, F.ubiing as ingreso, F.Fecha_data as fecha_ingreso,
		          	 	F.Hora_data as hora_ingreso
		         FROM  	{$temp18} F, {$wbasedato}_000011 G,  root_000036 A, root_000037 B, {$wbasedato}_000053
		        WHERE 	F.Ubihis = B.Orihis
						{$condicionCco}
		          AND 	F.Ubisac = G.Ccocod
		          AND 	A.Pacced = B.Oriced
	         	  AND 	A.Pactid = B.Oritid
	              AND 	B.Oriori = '{$wemp_pmla}'
		     	  AND 	F.Ubihis = Karhis
		     	  AND 	F.Ubiing = Karing
						{$filtro_historia}
		     	  AND   Karord   = 'on' 
				  UNION 
			 SELECT A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.Ubisac as ccocod, G.cconom,
		                F.Ubihis as historia, F.ubiing as ingreso, F.Fecha_data as fecha_ingreso,
		          	 	F.Hora_data as hora_ingreso 
			   FROM {$whce}_000027, {$whce}_000028, {$wbasedato}_000011 G,  root_000036 A, root_000037 B, {$temp18_aux} F
			  WHERE Ordest = 'on'
			    AND ordtor = dettor 
			    AND ordnro = detnro 
			    AND detest = 'on' 
			    AND ordhis = ubihis
			   {$filtro_historia_orden}      
			    AND ubihis = ordhis 
			    AND ubiing = ording
			    AND pacced = oriced        
			    AND pactid = oritid
			    AND orihis = ordhis
				AND B.Oriori = '{$wemp_pmla}'
			    AND ubisac = ccocod ";
		$q.=" GROUP BY historia, ingreso";
		$q.=" {$orderBy }";

	 //IMPRIMIMOS LA TABLA POR CENTRO DE COSTO DE LOS PACIENTES EGRESADOS
	 echo "<table align=center>";

	$cco_mostrado = "";
	 $i=0;
	 $wtotal = 0;
	 $wgtotal = 0;
	 $res= mysql_query($q, $conex) or die( mysql_error()." <br> ".print_r( $q )  );


	while($row = mysql_fetch_assoc($res)) {

		if($cco_mostrado != $row['ccocod'] ){
			if( $i != 0 and $whis == "" ){
				echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";
				echo "<tr><td colspan=10>&nbsp;</td></tr>";
			}
			if( $whis == "" ){
				echo "<tr class=titulo><td colspan=7>";
				echo $row['ccocod']." - ".$row["cconom"];
				echo "</td></tr>";
			}
			if( ($whis != "" and $i == 0) or ( $whis=="" ) ){
				echo "<tr class=encabezadoTabla>";
				echo "<td align=center>Historia</td>";
				echo "<td align=center>Ingreso</td>";
				echo "<td align=center>Paciente</td>";
				( $whis != "") ? $letrero = "" : $letrero = "Ingreso";
				echo "<td align=center>Servicio <br/>".$letrero."</td>";
				echo "<td align=center >Fecha de<br/> Ingreso</td>";
				echo "<td align=center >Hora<br/>de Ingreso</td>";
				echo "<td align=center>Ordenes</td>";
				echo "</tr>";
			}
			$cco_mostrado = $row['ccocod'];
			$wtotal = 0;
		}


		if($i % 2 == 0)
			$wclass="fila1";
		else
			$wclass="fila2";

		$wtotal++;
		$wgtotal++;
		$i++;
		
		$row['hora_ingreso']= substr_replace( $row['hora_ingreso'] ,"",-3 );

		$nombreCompleto = $row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"];
		
		echo "<tr class=".$wclass.">";
		echo "<td align=center>".$row["historia"]."</td>"; //historia
		echo "<td align=center>".$row["ingreso"]."</td>"; //ingreso
		echo "<td align=left nowrap='nowrap'>".$nombreCompleto."</td>"; //paciente
		if( $whis == "")
			echo "<td align=center>".$row["procede"]."</td>"; //servicio ingreso
		else
			echo "<td align=center>".$row["cconom"]."</td>"; //servicio ingreso
		
		echo "<td align=center nowrap='nowrap'>".$row['fecha_ingreso']."</td>"; //fecha ingreso
		echo "<td align=center>".$row['hora_ingreso']."</td>"; //hora ingreso
		echo "<td style='cursor:pointer;' align='center'><A id='ingreso".$row["ingreso"]."'onClick='ImprimirOrden(\"".$row["historia"]."\", \"".$row["ingreso"]."\", \"".$nombreCompleto."\", \"imp\", \"off\", \"off\", \"on\", \"". $origen."\")'><b>Imprimir</b></A></td>";
		echo "</tr>";		
	 }
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";
	 echo "<tr><td colspan=10>&nbsp;</td></tr>";
	 if($wgtotal == 0)
		$wgtotal=$wtotal;
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right>Gran Total de Servicios : ".$wgtotal."</td></tr>";
	 echo "</table>";
	 echo "<br/>";
}


if(isset($accion)){
	switch($accion)	{		
		//Si llega por ajax la solicitud de configurar impresión de ordenes
		case 'configurarImpresion' : {			
			echo "<div>";				
			echo "<table align='center' width='100%'>";
			echo "<tr class='encabezadoTabla'><td align='center'>Historia</td><td align='center'>Ingreso</td><td align='center'>Paciente</td></tr>";
			echo "<tr class='fila1'><td align='center'>" . $numHistoria . "</td><td align='center'>" . $ingreso . "</td><td align='center'>" . $nombrePac . "</td></tr>";
			echo "</table>";
			echo "<br>";
			echo "<table align='center' width='100%'>";
			echo "<tr class='encabezadoTabla'><td align='center' colspan='2'>Seleccione el o los tipos de orden que desea imprimir</td></tr>";
		
			if(isset($numHistoria)){				
				//Se consulta si el paciente tiene medicamentos para mostrarle la opción
				$sqlContarOrdMedic = "SELECT count(0) AS cantidad 
						FROM ".$wbasedato."_000054 mov 
						WHERE mov.Kadhis = '".$numHistoria."'
						AND mov.Kading = '".$ingreso."';";
				
				$ordMedicamentos = mysql_query($sqlContarOrdMedic, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerTiposOrden):</b><br>".mysql_error());
				$ordMedicamentos = mysql_fetch_row($ordMedicamentos);
				
				echo "<tr class='fila1'><td width='10%'><input type='checkbox' id='checktodos' onClick=seleccionarTodo()></td><td><label> <b>TODAS LAS ORDENES</b> </label></td><tr>";	
				$fila = 2;				
				
				if(isset($ordMedicamentos[0]) && $ordMedicamentos[0] > 0){
					echo "<tr class='fila".$fila."'><td width='10%'><input type='checkbox' name='ordenes[]' value='medtos'></td><td><label> MEDICAMENTOS </label></td><tr>";
					$fila = $fila === 1 ? 2 : 1;;
				}			
				
				$sqlObtenerTiposOrden = "SELECT DISTINCT hcq.Codigo, hcq.Descripcion 
						FROM ".$whce."_000015 hcq
						INNER JOIN ".$whce."_000027  hcv on hcq.Codigo = hcv.Ordtor
						WHERE Ordhis = '".$numHistoria."'
						AND Ording = '".$ingreso."'
						ORDER BY 2 ASC;";
						
				$tiposOrdenes = mysql_query($sqlObtenerTiposOrden, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerTiposOrden):</b><br>".mysql_error());
				while($rowTiposOrdenes = mysql_fetch_array($tiposOrdenes)){
					echo "<tr class='fila".$fila."'><td><input type='checkbox' name='ordenes[]' value='".$rowTiposOrdenes["Codigo"]."'></td><td><label> ". $rowTiposOrdenes["Descripcion"] ." </label></td><tr>";
					$fila = $fila === 1 ? 2 : 1;
				}
				echo "</table>";
				echo "<br>";
				echo "<table align='center' width='100%'>";
				echo "<tr class='encabezadoTabla'><td align='center' colspan='2'>Seleccione el orden cronl&oacute;gico</td></tr>";
				echo "<tr class='fila1'><td width='10%'><input type='radio' name='order' id='orderasc' value='asc' checked></td><td><label> Ascendente </label></td><tr>";
				echo "<tr class='fila2'><td width='10%'><input type='radio' name='order' id='orderdesc' value='desc'></td><td><label> Descendente </label></td><tr>";			
				echo "</div>";		
			}else{
				
			}
			break;
			return;			
		}		
	}
}
else
{
	?>
	<html>
	<head>
	<title>REPORTE PACIENTES EGRESADOS Y ACTIVOS</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />	  
	<style type="text/css">
		fieldset{
			border: 2px solid #e0e0e0;
		}
	</style>
	  
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

	<script type="text/javascript">
	function retornar(wemp_pmla,wfecha_i,wfecha_f,bandera,wcco0,origen)
		{
			location.href = "rep_PacientesEgresadosActivosOrdenes.php?wemp_pmla="+wemp_pmla+"&bandera="+bandera+"&wcco0="+wcco0+"&origen="+origen;
		}

	function cerrar_ventana(cant_inic)
		{
			window.close();
		}

	function enter()
		{
		 document.historias.submit();
		}


	function ejecutar(path)
		{
			
			if($("#conservarVentana").val()=="on")
			{
				window.location.replace(path);
			}
			else
			{
				window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');	
			}
		}

	function quitarCcoSeleccionado(){
		$("#wcco0>option[value='todos']").attr("selected", true);
	}

	//Para seleccionar o quitar todos los checkbox de tipos de ordenes a imprimir
	function seleccionarTodo(){				
		$('input[type=checkbox]').each( function() {			
			if($("input[id=checktodos]:checked").length == 1){
				this.checked = true;
			} else {
				this.checked = false;
			}
		});			
	}

	//Función que consulta y retorna los tipos de ordenes que tiene el paciente para imprimir y muestra la vista para seleccionar cuales imprimir.
	function ImprimirOrden(whistoria, wingreso, nombrePac, tipoimp, alt, pacEps, wtodos_ordenes, origen)
	{	
		var wemp_pmla = $('#wemp_pmla').val();	
		$.post("rep_PacientesEgresadosActivosOrdenes.php",
			{
				consultaAjax:   		'',
				accion:         		'configurarImpresion',
				wemp_pmla:        		wemp_pmla,
				numHistoria:			whistoria,
				ingreso:				wingreso,
				nombrePac:				nombrePac
			}, function(respuesta){
				$("#divConfigurarImpresion").html(respuesta).dialog({
					title: "Configurar impresi&oacute;n de ordenes",
					width: 600,
					modal: true,				
					buttons: {
						"Imprimir": function() {							
							//Se obtienen los tipos de ordenes seleccionados
							var checkboxOrdenes = "";
							$('input[name="ordenes[]"]:checked').each(function() {
								checkboxOrdenes += $(this).val() + ",";
							});
							//eliminamos la última coma.
							checkboxOrdenes = checkboxOrdenes.substring(0, checkboxOrdenes.length-1);																						
							var orden = $('input:radio[name=order]:checked').val();
							
							var adicionPath = "";
							if($("#enviarCorreo").val()=="on")
							{
								adicionPath += "&enviarCorreo="+$("#enviarCorreo").val()+"&emailEnviarCorreo="+$("#emailEnviarCorreo").val()+"&envioPaciente="+$("#envioPaciente").val();
							}
							
							var path = "/matrix/HCE/procesos/ordenes_imp.php?wemp_pmla="+ wemp_pmla +"&whistoria=" + whistoria + "&wingreso=" +wingreso+ "&tipoimp=imp&alt=off&pacEps=off&wtodos_ordenes=on&orden=" +orden+ "&origen=" +origen+ ""+"&arrOrden="+checkboxOrdenes+"&desdeImpOrden=on"+adicionPath; 
						
							ejecutar(path);
							$(this).dialog("close");
						},
						"Cancelar": function() {
							$(this).dialog("close");
						}
					}
				});
			});
	}
	</script>
	
	<?php
	vistaInicial();
	
	//Div para ventana modal que permita seleccionar que tipo de ordenes quiero imprimir.
	echo "<div id='divConfigurarImpresion'> </div>";
}	 
	

function vistaInicial(){

	global $conex;
	global $whce;
	global $wbasedato;
	global $wemp_pmla;
	global $wactualiz;
	global $wfecha_i;
	global $wfecha_f;
	global $wcco0;
	global $bandera;
	global $tipo;
	global $conex;
	global $conservarVentana;
	global $enviarCorreo;
	global $emailEnviarCorreo;
	global $envioPaciente;
	
	 $titulo = "Impresi&oacute;n de ordenes Pacientes Egresados y Activos";

	 encabezado($titulo, $wactualiz, "clinica");
	 echo "<br/>";

	 echo "
	 <input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
	 <form action='rep_PacientesEgresadosActivosOrdenes.php' name='historias' method='post'>";
	 echo "<input type='hidden' id='conservarVentana' name='conservarVentana' value='".$conservarVentana."'>";
	 echo "<input type='hidden' id='enviarCorreo' name='enviarCorreo' value='".$enviarCorreo."'>";
	 echo "<input type='hidden' id='emailEnviarCorreo' name='emailEnviarCorreo' value='".$emailEnviarCorreo."'>";
	 echo "<input type='hidden' id='envioPaciente' name='envioPaciente' value='".$envioPaciente."'>";
     if(!isset($wfecha_i ) or !isset($wfecha_i ) or !isset($wcco0) or isset($bandera) )
		{
			if(!isset($wfecha_i ) && !isset($wfecha_i ))
			   {
					$wfecha_i = date("Y-m-d");
					$wfecha_i = strtotime ( '-1 day' , strtotime ( $wfecha_i ) ) ;
					$wfecha_i = date ( 'Y-m-d' , $wfecha_i );
					$wfecha_f = date("Y-m-d");
					echo "<input type='hidden' name='wfecha_i' value='".$wfecha_i."'>";
					echo "<input type='hidden' name='wfecha_f' value='".$wfecha_f."'>";
			   }

			$q = "( SELECT Ccocod, Cconom"
				."    FROM ".$wbasedato."_000011 G "
				."   WHERE Ccohos = 'on' ) "
				." UNION "
				."( SELECT Ccocod, Cconom"
				."    FROM ".$wbasedato."_000011 G "
				."   WHERE Ccoing = 'on' ) ";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

			echo "<center><table border=0>";
			/*echo "<tr><td class=fila1 align=center><b>Fecha Inicial</b></td>";
			echo "<td class=fila1 align=left colspan=2>";
			campoFechaDefecto("wfecha_i", $wfecha_i);
			echo "</td></tr>";
			echo "<tr><td class=fila1 align=center><b>Fecha Final</b></td>";
			echo "<td class=fila1 align=left colspan=2>";
			campoFechaDefecto("wfecha_f", $wfecha_f);
			echo "</td></tr>";*/
			echo "<tr><td colspan=2 class=fila1 align=center><b> Servicio</b></td></tr>";
			echo "<tr><td colspan=2 align =center class=fila1>";
			echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
			echo "<select name='wcco0' id='wcco0'>";
			echo "<option value ='todos'>todos</option>";

			for($i = 1; $i <= $num; $i++)
			{
			 $row = mysql_fetch_array($res);

			 if(isset($wcco0) && $row[0]==$wcco0)
				echo "<option selected value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
			 else
				echo "<option value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
			}

			echo "</select>";
			echo "</td></tr>";
			//echo "<tr><td class = fila1  align= center><input type=radio name=tipo value=ingreso onclick='enter()' /> <b>Ingreso</b></td> <td class = fila1  align= center> <input type=radio name=tipo value=egreso onclick='enter()' /> <b>Egreso</b> </td></tr>";
			echo "<tr><td class = fila1  align= center colspan=2><b>Historia:</b><input type='text' name='historia' id='historia' onkeypress='quitarCcoSeleccionado()'></td></tr>";

			echo "</table>";
			
			$origen = $_GET['origen'];
			echo "<input type=hidden id=origen name=origen value='".$origen."'>";
			echo "</center>";
			echo "</form>";
			echo "<center><input type=button id='btnConsultar' name ='btn_cerrar2' value='CONSULTAR' onclick='enter()'></center><br>";
			echo "<center><input type=button name ='btn_cerrar2' value='Cerrar Ventana' onclick='cerrarVentana()'></center>";

		}
		else
		{

			echo "<center>";
			/*echo "<table border=0>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Centro Costo </td>";
			echo "<td  class=fila1 align='left'>".$wcco0."</td>";
			echo "</tr>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Fecha Inicial </td>";
			echo "<td  class=fila1 align='left'>".$wfecha_i."</td>";
			echo "</tr>";
			echo "<tr class=encabezadoTabla	>";
			echo "<td align=center >Fecha Final </td>";
			echo "<td  class=fila1 align='left'>".$wfecha_f."</td>";
			echo "</tr>";
			echo "</table>";*/
			echo "</center>";
			echo "<br/>";

			$wcco1 = explode("-",$wcco0);
			$bandera=1;
			$historia = $_POST['historia'];
			$origen = $_POST['origen'];

			echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\", \"".$wcco1[0]."\",\"".$origen."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
			echo "<br/>";
			
			consultar_ordenes($wfecha_i,$wfecha_f,$wcco0,$historia,$origen);

			echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\", \"".$wcco1[0]."\",\"".$origen."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";

        }			
}
?>
</body>
</html>
