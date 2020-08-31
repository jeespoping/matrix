<html>
<head>
  <title>REPORTE DE EVALUACION HISTORIAS CLINICAS</title>
<script type="text/javascript">

	//Redirecciona a la pagina inicial
	function inicioReporte(wfecini,wfecfin,wcco,wemp_pmla,bandera){
	 	document.location.href='rep_evahc.php?wfecini='+wfecini+'&wfecfin='+wfecfin+'&wccocod='+wcco+'&wemp_pmla='+wemp_pmla+'&bandera='+bandera;
	}

	function Seleccionar()
	{
		var fecini = document.forma.wfecini.value;
		var fecfin = document.forma.wfecfin.value;
	 
		//Valida que la fecha final sea mayor o igual a la incial
		if(!esFechaMenorIgual(fecini,fecfin))
		{
		   alert("La fecha inicial no puede ser mayor que la fecha final");
		   form.wfecini.focus();
		   return false;
		}

		document.forma.submit();
	}
	function cerrarVentana()
	{
	 window.close()
	}
</script>

</head>
<?php
include_once("conex.php");

  /******************************************************************************
   *     REPORTE DE EVALUACION HISTORIAS CLINICAS							    *
   ******************************************************************************/
   
	/*--------------------------------------------------------------------------
	| DEDSCRIPCIÓN: Reporte de evaluacion de las historias clinicas por centro  |
	| de costos con base en fecha inicial y fecha final			|
	| AUTOR: Gabriel Agudelo														|
	| FECHA DE CREACIÓN: Octubre 20 de 2014										|
	| FECHA DE ACTUALIZACIÓN: 													|
	----------------------------------------------------------------------------*/

	/*--------------------------------------------------------------------------
	| ACTUALIZACIONES															|
	|---------------------------------------------------------------------------|
	| FECHA:  																	|
	| AUTOR: 																	|
	| DESCRIPCIÓN:																|
	----------------------------------------------------------------------------*/

$wactualiz="1.0 | Noviembre 10 de 2014";
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

//Validación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user']))
{
	$usuarioValidado = false;
}
else 
{
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == "")
{
	$usuarioValidado = false;
}

session_start();

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("FORMULARIO EVALUACION HISTORIA CLINICA",$wactualiz,"clinica");

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte
	
  //Conexion base de datos
  


  /*$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "farpmla");
  //$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "jcievahc");
	
  $q = " SELECT empdes "
      ."  FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wentidad=$row[0];*/
  
  echo "<form name='forma' action='rep_evahc.php' method=post onSubmit='return valida_enviar(this);'>";
  $wfecha=date("Y-m-d");   
  
 /* echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";*/
  echo "<input type='HIDDEN' NAME= 'form' value='forma'>";

  // Si no se han enviado datos por el formulario
  if (!isset($form) or $form == '')
  {
	// Trae los datos de consultas anteriores
	if (!isset ($bandera))
	{
		 $wfecini=$wfecha;
		 $wfecfin=$wfecha;
		 $wccocod="%-Todos los centros de costos";
	}
	
	//Inicio tabla de ingreso de parametros
 	echo "<table align='center' border='0' cellspacing='4' bordercolor='ffffff'>";
 	
 	//Petición de ingreso de parametros
 	echo "<tr>";
 	echo "<td height='37' colspan='5'>";
 	echo '<p align="left" class="titulo"><strong> &nbsp; Seleccione los datos a consultar &nbsp;  &nbsp; </strong></p>';
 	echo "</td></tr>";
		
 	//Solicitud fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=221 align=right> &nbsp; Fecha inicial &nbsp; </td>";
 	echo "<td class='fila2' align='left' width=171>";
 	campoFechaDefecto("wfecini",$wfecini);
 	echo "</td>";
	echo "</tr>";
 		
 	//Solicitud fecha final
 	echo "<tr>";
 	echo "<td class='fila1' align=right> &nbsp; Fecha final &nbsp; </td>";
 	echo "<td class='fila2' align='left' width='141'>";
 	campoFechaDefecto("wfecfin",$wfecfin);
 	echo "</td>";
 	echo "</tr>";
	
	echo "<tr>";  

	//SELECCIONAR CENTRO DE COSTOS
	if (isset($wccocod))
	{
		echo "<td class=fila1 align=right> &nbsp; Centro de costos &nbsp; </td>";
		echo "<td class=fila2>";
		echo "<select name='wccocod'>";   
  		// Consulto los centros de costos donde se generan facturas
  		$q= "   SELECT Ccosto "
 	       ."   FROM jcievahc_000001 "
 	       ."    GROUP BY 1 "
 	       ."    ORDER by 1 "; 	
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);  
		
  		if ($num1 > 0 )
      	  {
      		if (isset($wccocod) && $wccocod=='%')
			   echo "<option selected>%-Todos los centros de costos</option>";
			else
			   echo "<option>%-Todos los centros de costos</option>";
		    for ($i=1;$i<=$num1;$i++)
	           {
	            $row1 = mysql_fetch_array($res1); 
				if(isset($wccocod) && $row1[0]==$wccocod)
					echo "<option selected>".$row1[0]."</option>"; 
	            else
					echo "<option>".$row1[0]."</option>";
	           } 
          }
     	echo "</select></td></tr>"; 
	}
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";	
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
	

	echo "</table></br>";	   

	echo "<p align='center'><input type='button' id='searchsubmit' value='Consultar' onclick='Seleccionar()'> &nbsp; | &nbsp; <input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
	
  } 

//RESULTADO DE CONSULTA DEL REPORTE
else
  {
	
	// si el centro de costos es diferente a todas los centros de costos, la meto en el vector solo
	// si son todos los costos los meto todos en un vector para luego preguntarlos en un for 
		
	if ($wccocod !='%-Todos los centros de costos')
	{
		$wcco=explode('-', $wccocod);
		$wccostos[0]=trim ($wcco[0]);
		$num1=1;
    }	
	else
    { 
	   $wccostos='%';
	   $num1=2;
	}
	$aux1=$wccostos[0];

	//Inicio tabla de resultados
    echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
 	
	// Subtítulo del reporte
 	echo "<tr>";
	echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Formulario Evaluacion Historia Clinica &nbsp;  &nbsp; </strong></p></td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";

	//Muestro los parámetros que se ingresaron en la consulta
    echo "<tr class='fila2'>";
    echo "<td align=left><strong> &nbsp; Fecha inicial: </strong>&nbsp;".$wfecini." &nbsp; </td>";
    echo "<td align=left><strong> &nbsp; Fecha final: </strong>&nbsp;".$wfecfin." &nbsp; </td>";
    echo "</tr>";
    echo "<tr class='fila2'>";
    echo "<td align=left colspan=2><strong> &nbsp; Centro de costos: </strong>&nbsp;".$wccocod." &nbsp; </td>";
    echo "</tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>";

	// Botones de "Retornar" y "Cerrar ventana"
	echo "<p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$aux1\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
 	echo "</td></tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td>";
 	echo "</tr>";
    echo "</table>";

    echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
    echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
    echo "<input type='HIDDEN' NAME= 'wccocod' value='".$wccocod."'>";
    echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	/***********************************Consulto lo pedido ********************/

	// QUERY PRINCIPAL DEL REPORTE
	$q = "	SELECT * "
		."	FROM jcievahc_000001 "
		."	WHERE Fecha BETWEEN '".$wfecini."' "
		."    AND '".$wfecfin."'"
		."	  AND Ccosto LIKE '".$wccostos[0]."%' "
		."	  ORDER BY Ccosto ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	$wglobalc=0;
	$wglobalnc=0;
	$wglobalna=0;
	$wtotgenc=0;
	$wtotgennc=0;
	$wtotgenna=0;
	$wtotpor=0;
	$wglobalpor=0;
	$bandera1=0;
	$bandera2=0;
	
	$clase='fila1';
	$k=1;
	$i=1;
	$j=6;
	$cont=0;
	$contgen=0;
	
$wtotalc6=0;	$wtotalnc6=0;	$wtotalna6=0;	$por6=0;
$wtotalc7=0;	$wtotalnc7=0;	$wtotalna7=0;	$por7=0;
$wtotalc8=0;	$wtotalnc8=0;	$wtotalna8=0;	$por8=0;
$wtotalc9=0;	$wtotalnc9=0;	$wtotalna9=0;	$por9=0;
$wtotalc10=0;	$wtotalnc10=0;	$wtotalna10=0;	$por10=0;
$wtotalc11=0;	$wtotalnc11=0;	$wtotalna11=0;	$por11=0;
$wtotalc12=0;	$wtotalnc12=0;	$wtotalna12=0;	$por12=0;
$wtotalc13=0;	$wtotalnc13=0;	$wtotalna13=0;	$por13=0;
$wtotalc14=0;	$wtotalnc14=0;	$wtotalna14=0;	$por14=0;
$wtotalc15=0;	$wtotalnc15=0;	$wtotalna15=0;	$por15=0;
$wtotalc16=0;	$wtotalnc16=0;	$wtotalna16=0;	$por16=0;
$wtotalc17=0;	$wtotalnc17=0;	$wtotalna17=0;	$por17=0;
$wtotalc18=0;	$wtotalnc18=0;	$wtotalna18=0;	$por18=0;
$wtotalc19=0;	$wtotalnc19=0;	$wtotalna19=0;	$por19=0;
$wtotalc20=0;	$wtotalnc20=0;	$wtotalna20=0;	$por20=0;
$wtotalc21=0;	$wtotalnc21=0;	$wtotalna21=0;	$por21=0;
$wtotalc22=0;	$wtotalnc22=0;	$wtotalna22=0;	$por22=0;
$wtotalc23=0;	$wtotalnc23=0;	$wtotalna23=0;	$por23=0;
$wtotalc24=0;	$wtotalnc24=0;	$wtotalna24=0;	$por24=0;
$wtotalc25=0;	$wtotalnc25=0;	$wtotalna25=0;	$por25=0;
$wtotalc26=0;	$wtotalnc26=0;	$wtotalna26=0;	$por26=0;
$wtotalc27=0;	$wtotalnc27=0;	$wtotalna27=0;	$por27=0;
$wtotalc28=0;	$wtotalnc28=0;	$wtotalna28=0;	$por28=0;
$wtotalc29=0;	$wtotalnc29=0;	$wtotalna29=0;	$por29=0;
$wtotalc30=0;	$wtotalnc30=0;	$wtotalna30=0;	$por30=0;
$wtotalc31=0;	$wtotalnc31=0;	$wtotalna31=0;	$por31=0;
$wtotalc32=0;	$wtotalnc32=0;	$wtotalna32=0;	$por32=0;
$wtotalc33=0;	$wtotalnc33=0;	$wtotalna33=0;	$por33=0;
$wtotalc34=0;	$wtotalnc34=0;	$wtotalna34=0;	$por34=0;
$wtotalc35=0;	$wtotalnc35=0;	$wtotalna35=0;	$por35=0;
$wtotalc36=0;	$wtotalnc36=0;	$wtotalna36=0;	$por36=0;
$wtotalc37=0;	$wtotalnc37=0;	$wtotalna37=0;	$por37=0;
$wtotalc38=0;	$wtotalnc38=0;	$wtotalna38=0;	$por38=0;
$wtotalc39=0;	$wtotalnc39=0;	$wtotalna39=0;	$por39=0;
$wtotalc40=0;	$wtotalnc40=0;	$wtotalna40=0;	$por40=0;
$wtotalc41=0;	$wtotalnc41=0;	$wtotalna41=0;	$por41=0;
$wtotalc42=0;	$wtotalnc42=0;	$wtotalna42=0;	$por42=0;
$wtotalc43=0;	$wtotalnc43=0;	$wtotalna43=0;	$por43=0;
$wtotalc44=0;	$wtotalnc44=0;	$wtotalna44=0;	$por44=0;
$wtotalc45=0;	$wtotalnc45=0;	$wtotalna45=0;	$por45=0;
$wtotalc46=0;	$wtotalnc46=0;	$wtotalna46=0;	$por46=0;
$wtotalc47=0;	$wtotalnc47=0;	$wtotalna47=0;	$por47=0;
$wtotalc48=0;	$wtotalnc48=0;	$wtotalna48=0;	$por48=0;
$wtotalc49=0;	$wtotalnc49=0;	$wtotalna49=0;	$por49=0;
$wtotalc50=0;	$wtotalnc50=0;	$wtotalna50=0;	$por50=0;
$wtotalc51=0;	$wtotalnc51=0;	$wtotalna51=0;	$por51=0;
$wtotalc52=0;	$wtotalnc52=0;	$wtotalna52=0;	$por52=0;
$wtotalc53=0;	$wtotalnc53=0;	$wtotalna53=0;	$por53=0;
$wtotalc54=0;	$wtotalnc54=0;	$wtotalna54=0;	$por54=0;
$wtotalc55=0;	$wtotalnc55=0;	$wtotalna55=0;	$por55=0;
$wtotalc56=0;	$wtotalnc56=0;	$wtotalna56=0;	$por56=0;
$wtotalc57=0;	$wtotalnc57=0;	$wtotalna57=0;	$por57=0;
$wtotalc58=0;	$wtotalnc58=0;	$wtotalna58=0;	$por58=0;
$wtotalc59=0;	$wtotalnc59=0;	$wtotalna59=0;	$por59=0;
$wtotalc60=0;	$wtotalnc60=0;	$wtotalna60=0;	$por60=0;
$wtotalc61=0;	$wtotalnc61=0;	$wtotalna61=0;	$por61=0;
$wtotalc62=0;	$wtotalnc62=0;	$wtotalna62=0;	$por62=0;
$wtotalc63=0;	$wtotalnc63=0;	$wtotalna63=0;	$por63=0;
$wtotalc64=0;	$wtotalnc64=0;	$wtotalna64=0;	$por64=0;
$wtotalc65=0;	$wtotalnc65=0;	$wtotalna65=0;	$por65=0;
$wtotalc66=0;	$wtotalnc66=0;	$wtotalna66=0;	$por66=0;
$wtotalc67=0;	$wtotalnc67=0;	$wtotalna67=0;	$por67=0;
$wtotalc68=0;	$wtotalnc68=0;	$wtotalna68=0;	$por68=0;
$wtotalc69=0;	$wtotalnc69=0;	$wtotalna69=0;	$por69=0;
$wtotalc70=0;	$wtotalnc70=0;	$wtotalna70=0;	$por70=0;
$wtotalc71=0;	$wtotalnc71=0;	$wtotalna71=0;	$por71=0;
$wtotalc72=0;	$wtotalnc72=0;	$wtotalna72=0;	$por72=0;
$wtotalc73=0;	$wtotalnc73=0;	$wtotalna73=0;	$por73=0;
$wtotalc74=0;	$wtotalnc74=0;	$wtotalna74=0;	$por74=0;
$wtotalc75=0;	$wtotalnc75=0;	$wtotalna75=0;	$por75=0;

$crite6="Se registra en la HC informacion sobre espera en la atencion";
$crite7="Se registra   en H.C. criterios para atencion en UCI y otros serv.";
$crite8="Se evidencian en la H.C. criterios para dar de alta o transferir el paciente";
$crite9="La H.C.  contiene una copia del informe de alta. ";
$crite10="El informe de alta contiene instrucciones de seguimiento. (Conducta)";
$crite11="El informe de alta  esta completo";
$crite12="El Resumen Egreso contiene: DX, alergias, medicacion y  procedimientos.";
$crite13="El proceso de traslado se documenta en la H.C. ";
$crite14="La H.C. de ptes trasladados describe institución  y persona que recibe.";
$crite15="La H.C. de  ptes trasladados describe razones de traslado";
$crite16="La identidad de quien facilitan la inf.  en el consentimiento se anota en H. C.";
$crite17="Las necesidades del pte. se documentan en la H. C.";
$crite18="Las necesidades de cuidados de enfermeria se documentan en la H. C. ";
$crite19="Se registra en la H.C. una nota y un DX. Preoperatorio.";
$crite20="Los hallazgos de las evaluaciones se documentan en la H. C. ";
$crite21="La evaluacion medica se documenta antes de la cirugia.";
$crite22="Las evaluaciones de pte terminal se documentan en la H. C.";
$crite23="Las evaluaciones por especialistas se documentan en la H.C.";
$crite24="Se reevalua el pte.  para planificar el tratamiento o el alta.";
$crite25="Se registran evaluaciones al menos una vez al dia, incluso los fines de semana.";
$crite26="Los resultados de reuniones del equipo de atencion del pte. se registran la H. C";
$crite27="El medico revisa y verifica la atencion, registrando una nota en la H. C.";
$crite28="Las ordenes se registran de manera uniforme en la H. C.";
$crite29="Los procedimientos realizados se anotan en la H.C.";
$crite30="Los resultados de los procedimientos se registran en la H. C.";
$crite31="Se tienen una orden de alimentacion en la H. C.";
$crite32="Se registra la respuesta del pte a la terapia nutricional en la H.C.";
$crite33="Las evaluaciones previas a la anestesia y a la induccion, se documentan en la HC";
$crite34="Se anota la anestesia utilizada en el registros de anestesia del pte.";
$crite35="Se anota la tecnica anestésica utilizada, en el registros de anestesia del pte";
$crite36="Se anotan nombres del anetesiologo, enfer. y  auxiliar de anestesia en la H.C.";
$crite37="Los hallazgos de la monitorizacion se documentan en la H.C.";
$crite38="Se registra la hora de inicio y terminación de recuperacion del pte en la H.C.";
$crite39="Se  registra información en la H.C. previa a procedimiento invasivo.";
$crite40="Se registra DX preoperatorio previo al procedimiento, en la H.C. del pte.";
$crite41="El inf. quirurgico o la nota quirurgica incluye dx postoperatorio";
$crite42="El informe quirurgico o la nota incluye nombres del cirujano y los ayudantes";
$crite43="El informe quirurgico o la nota incluye nombre del procedimiento.";
$crite44="El informe quirurgico o la nota incluye muestras  enviadas a analizar.";
$crite45="El informe quirurgico incluye complicaciones  y cantidad de sangre perdida.";
$crite46="El informe quirurgico o la nota incluye fecha, hora y firma del medico responsab";
$crite47="El informe quirurgico o nota, estan disponibles en la H.C.";
$crite48="Los hallazgos se documentan en la H.C. pte.";
$crite49="El plan quirurgico se documenta y firma en la H.C. por el cirujano";
$crite50="El plan de cuidados de enfermeria se documentan en la H.C.";
$crite51="El plan postquirurgico de otros profesionales se documenta en la H.C.";
$crite52="Se registran los medicamentos ordenados al pte.";
$crite53="Se registra dosis y hora de los medicamentos administrados.";
$crite54="La informacion sobre los medicamentos se conserva en la H.C.";
$crite55="Los medicamentos se administran tal como se recetan y se anotan en la H.C";
$crite56="Los efectos adversos se documentan en la H.C.";
$crite57="Los hallazgos de las evaluaciones educativas se documentan en la H.C.";
$crite58="Todo el personal registra la educacion del paciente de manera uniforme.";
$crite59="El resumen de H.C. del pte incluye el motivo de admision.";
$crite60="El resumen de H.C. del pte incluye los hallazgos relevantes";
$crite61="El resumen de H.C. del pte incluye los diagnosticos";
$crite62="El resumen de H.C. incluye los procedimientos realizados.";
$crite63="El resumen de H.C. incluye medicamentos y  tratamientos administrados.";
$crite64="El resumen de H.C. incluye el estado del pte al momento de la transferencia.";
$crite65="La H.C. contiene informacion para identificar al pte.";
$crite66="La H.C.contiene informacion para apoyar el diagnostico. ";
$crite67="La H.C. contienen informacion para justificar la atencion y el tratamiento.";
$crite68="La H.C. contienen informacion sobre el curso y los resultados del tratamiento.";
$crite69="La H.C. de los ptes de urgencias incluye la hora de llegada.";
$crite70="La H.C. de urgencias incluye las conclusiones al terminar el tto.";
$crite71="La H.C. de urgencias incluye el estado del pte al alta.";
$crite72="La H.C. de urgencias incluye instrucciones para la atencion del seguimiento.";
$crite73="Se puede identificar el autor de cada ingreso de informacion en la H.C.";
$crite74="Se puede identificar la fecha  de cada ingreso de informacion en la H.C.";
$crite75="Se puede identificar la hora de cada ingreso de informacion en la H.C.";
$color="#eeeeee";
$color1="#cccccc";
$color2="#bbbbbb";
$color3="#aaaaaa";
	// Creación de tabla donde se muestra el resultado de la consulta
    echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
   
	// Inicio del ciclo general de los resultados de la consulta
	while ($i <= $num) 
		{
			$row = mysql_fetch_array($err);
			if ($bandera1==0) 
				{
					$wccocod=$row['Ccosto'];
				}
					 	
			// Si cambia el centro de costos muestre el total del anterior
			if ($wccocod!=$row['Ccosto'])
				{	
					$wtotgenc=$wtotalc6+$wtotalc7+$wtotalc8+$wtotalc9+$wtotalc10+$wtotalc11+$wtotalc12+$wtotalc13+$wtotalc14+$wtotalc15+
							  $wtotalc16+$wtotalc17+$wtotalc18+$wtotalc19+$wtotalc20+$wtotalc21+$wtotalc22+$wtotalc23+$wtotalc24+$wtotalc25+
							  $wtotalc26+$wtotalc27+$wtotalc28+$wtotalc29+$wtotalc30+$wtotalc31+$wtotalc32+$wtotalc33+$wtotalc34+$wtotalc35+
							  $wtotalc36+$wtotalc37+$wtotalc38+$wtotalc39+$wtotalc40+$wtotalc41+$wtotalc42+$wtotalc43+$wtotalc44+$wtotalc45+
							  $wtotalc46+$wtotalc47+$wtotalc48+$wtotalc49+$wtotalc50+$wtotalc51+$wtotalc52+$wtotalc53+$wtotalc54+$wtotalc55+
							  $wtotalc56+$wtotalc57+$wtotalc58+$wtotalc59+$wtotalc60+$wtotalc61+$wtotalc62+$wtotalc63+$wtotalc64+$wtotalc65+
							  $wtotalc66+$wtotalc67+$wtotalc68+$wtotalc69+$wtotalc70+$wtotalc71+$wtotalc72+$wtotalc73+$wtotalc74+$wtotalc75;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9+$wtotalnc10+$wtotalnc11+$wtotalnc12+$wtotalnc13+$wtotalnc14+
							   $wtotalnc15+$wtotalnc16+$wtotalnc17+$wtotalnc18+$wtotalnc19+$wtotalnc20+$wtotalnc21+$wtotalnc22+$wtotalnc23+
							   $wtotalnc24+$wtotalnc25+$wtotalnc26+$wtotalnc27+$wtotalnc28+$wtotalnc29+$wtotalnc30+$wtotalnc31+$wtotalnc32+
							   $wtotalnc33+$wtotalnc34+$wtotalnc35+$wtotalnc36+$wtotalnc37+$wtotalnc38+$wtotalnc39+$wtotalnc40+$wtotalnc41+
							   $wtotalnc42+$wtotalnc43+$wtotalnc44+$wtotalnc45+$wtotalnc46+$wtotalnc47+$wtotalnc48+$wtotalnc49+$wtotalnc50+
							   $wtotalnc51+$wtotalnc52+$wtotalnc53+$wtotalnc54+$wtotalnc55+$wtotalnc56+$wtotalnc57+$wtotalnc58+$wtotalnc59+
							   $wtotalnc60+$wtotalnc61+$wtotalnc62+$wtotalnc63+$wtotalnc64+$wtotalnc65+$wtotalnc66+$wtotalnc67+$wtotalnc68+
							   $wtotalnc69+$wtotalnc70+$wtotalnc71+$wtotalnc72+$wtotalnc73+$wtotalnc74+$wtotalnc75;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9+$wtotalna10+$wtotalna11+$wtotalna12+$wtotalna13+$wtotalna14+$wtotalna15+
							   $wtotalna16+$wtotalna17+$wtotalna18+$wtotalna19+$wtotalna20+$wtotalna21+$wtotalna22+$wtotalna23+$wtotalna24+$wtotalna25+
							   $wtotalna26+$wtotalna27+$wtotalna28+$wtotalna29+$wtotalna30+$wtotalna31+$wtotalna32+$wtotalna33+$wtotalna34+$wtotalna35+
							   $wtotalna36+$wtotalna37+$wtotalna38+$wtotalna39+$wtotalna40+$wtotalna41+$wtotalna42+$wtotalna43+$wtotalna44+$wtotalna45+
							   $wtotalna46+$wtotalna47+$wtotalna48+$wtotalna49+$wtotalna50+$wtotalna51+$wtotalna52+$wtotalna53+$wtotalna54+$wtotalna55+
							   $wtotalna56+$wtotalna57+$wtotalna58+$wtotalna59+$wtotalna60+$wtotalna61+$wtotalna62+$wtotalna63+$wtotalna64+$wtotalna65+
							   $wtotalna66+$wtotalna67+$wtotalna68+$wtotalna69+$wtotalna70+$wtotalna71+$wtotalna72+$wtotalna73+$wtotalna74+$wtotalna75;
							   
					
					echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>"; 
					echo "<tr class='encabezadoTabla' height='31'>";
						echo"<td align=center border=1 colspan='5'>CRITERIOS</td>";
						echo "<td align=center border=1 >CUMPLE</td>";
						echo "<td align=center border=1>NO CUMPLE</td>";
						echo "<td align=center border=1 >NO APLICA</td>";
						echo "<td align=center border=1>% DE CUMPLIMIENTO</td></tr>";
					echo "<tr><td align='left' colspan='5'>&nbsp;</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna6."</td>";
							$por6=(($wtotalc6+$wtotalna6)/$cont)*100;
							$por6=ROUND($por6,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por6."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna7."</td>";
							$por7=(($wtotalc7+$wtotalna7)/$cont)*100;
							$por7=ROUND($por7,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por7."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna8."</td>";
							$por8=(($wtotalc8+$wtotalna8)/$cont)*100;
							$por8=ROUND($por8,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por8."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna9."</td>";
							$por9=(($wtotalc9+$wtotalna9)/$cont)*100;
							$por9=ROUND($por9,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por9."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite10."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc10."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc10."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna10."</td>";
							$por10=(($wtotalc10+$wtotalna10)/$cont)*100;
							$por10=ROUND($por10,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por10."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite11."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc11."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc11."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna11."</td>";
							$por11=(($wtotalc11+$wtotalna11)/$cont)*100;
							$por11=ROUND($por11,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por11."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite12."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc12."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc12."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna12."</td>";
							$por12=(($wtotalc12+$wtotalna12)/$cont)*100;
							$por12=ROUND($por12,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por12."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite13."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc13."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc13."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna13."</td>";
							$por13=(($wtotalc13+$wtotalna13)/$cont)*100;
							$por13=ROUND($por13,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por13."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite14."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc14."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc14."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna14."</td>";
							$por14=(($wtotalc14+$wtotalna14)/$cont)*100;
							$por14=ROUND($por14,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por14."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite15."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc15."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc15."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna15."</td>";
							$por15=(($wtotalc15+$wtotalna15)/$cont)*100;
							$por15=ROUND($por15,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por15."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite16."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc16."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc16."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna16."</td>";
							$por16=(($wtotalc16+$wtotalna16)/$cont)*100;
							$por16=ROUND($por16,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por16."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite17."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc17."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc17."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna17."</td>";
							$por17=(($wtotalc17+$wtotalna17)/$cont)*100;
							$por17=ROUND($por17,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por17."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite18."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna18."</td>";
							$por18=(($wtotalc18+$wtotalna18)/$cont)*100;
							$por18=ROUND($por18,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por18."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna19."</td>";
							$por19=(($wtotalc19+$wtotalna19)/$cont)*100;
							$por19=ROUND($por19,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por19."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite20."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna20."</td>";
							$por20=(($wtotalc20+$wtotalna20)/$cont)*100;
							$por20=ROUND($por20,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por20."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna21."</td>";
							$por21=(($wtotalc21+$wtotalna21)/$cont)*100;
							$por21=ROUND($por21,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por21."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite22."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc22."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc22."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna22."</td>";
							$por22=(($wtotalc22+$wtotalna22)/$cont)*100;
							$por22=ROUND($por22,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por22."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite23."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc23."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc23."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna23."</td>";
							$por23=(($wtotalc23+$wtotalna23)/$cont)*100;
							$por23=ROUND($por23,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por23."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite24."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc24."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc24."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna24."</td>";
							$por24=(($wtotalc24+$wtotalna24)/$cont)*100;
							$por24=ROUND($por24,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por24."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite25."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc25."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc25."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna25."</td>";
							$por25=(($wtotalc25+$wtotalna25)/$cont)*100;
							$por25=ROUND($por25,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por25."%</td></tr>";	
					
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite26."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc26."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc26."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna26."</td>";
							$por26=(($wtotalc26+$wtotalna26)/$cont)*100;
							$por26=ROUND($por26,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por26."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite27."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc27."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc27."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna27."</td>";
							$por27=(($wtotalc27+$wtotalna27)/$cont)*100;
							$por27=ROUND($por27,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por27."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite28."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc28."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc28."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna28."</td>";
							$por28=(($wtotalc28+$wtotalna28)/$cont)*100;
							$por28=ROUND($por28,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por28."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite29."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc29."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc29."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna29."</td>";
							$por29=(($wtotalc29+$wtotalna29)/$cont)*100;
							$por29=ROUND($por29,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por29."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite30."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc30."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc30."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna30."</td>";
							$por30=(($wtotalc30+$wtotalna30)/$cont)*100;
							$por30=ROUND($por30,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por30."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite31."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc31."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc31."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna31."</td>";
							$por31=(($wtotalc31+$wtotalna31)/$cont)*100;
							$por31=ROUND($por31,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por31."%</td></tr>";	
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite32."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc32."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc32."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna32."</td>";
							$por32=(($wtotalc32+$wtotalna32)/$cont)*100;
							$por32=ROUND($por32,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por32."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite33."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc33."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc33."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna33."</td>";
							$por33=(($wtotalc33+$wtotalna33)/$cont)*100;
							$por33=ROUND($por33,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por33."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite34."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc34."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc34."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna34."</td>";
							$por34=(($wtotalc34+$wtotalna34)/$cont)*100;
							$por34=ROUND($por34,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por34."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite35."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc35."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc35."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna35."</td>";
							$por35=(($wtotalc35+$wtotalna35)/$cont)*100;
							$por35=ROUND($por35,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por35."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite36."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc36."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc36."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna36."</td>";
							$por36=(($wtotalc36+$wtotalna36)/$cont)*100;
							$por36=ROUND($por36,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por36."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite37."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc37."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc37."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna37."</td>";
							$por37=(($wtotalc37+$wtotalna37)/$cont)*100;
							$por37=ROUND($por37,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por37."%</td></tr>";	
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite38."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc38."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc38."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna38."</td>";
							$por38=(($wtotalc38+$wtotalna38)/$cont)*100;
							$por38=ROUND($por38,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por38."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite39."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc39."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc39."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna39."</td>";
							$por39=(($wtotalc39+$wtotalna39)/$cont)*100;
							$por39=ROUND($por39,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por39."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite40."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc40."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc40."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna40."</td>";
							$por40=(($wtotalc40+$wtotalna40)/$cont)*100;
							$por40=ROUND($por40,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por40."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite41."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc41."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc41."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna41."</td>";
							$por41=(($wtotalc41+$wtotalna41)/$cont)*100;
							$por41=ROUND($por41,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por41."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite42."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc42."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc42."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna42."</td>";
							$por42=(($wtotalc42+$wtotalna42)/$cont)*100;
							$por42=ROUND($por42,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por42."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite43."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc43."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc43."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna43."</td>";
							$por43=(($wtotalc43+$wtotalna43)/$cont)*100;
							$por43=ROUND($por43,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por43."%</td></tr>";
	
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite44."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc44."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc44."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna44."</td>";
							$por44=(($wtotalc44+$wtotalna44)/$cont)*100;
							$por44=ROUND($por44,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por44."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite45."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc45."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc45."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna45."</td>";
							$por45=(($wtotalc45+$wtotalna45)/$cont)*100;
							$por45=ROUND($por45,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por45."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite46."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc46."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc46."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna46."</td>";
							$por46=(($wtotalc46+$wtotalna46)/$cont)*100;
							$por46=ROUND($por46,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por46."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite47."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc47."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc47."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna47."</td>";
							$por47=(($wtotalc47+$wtotalna47)/$cont)*100;
							$por47=ROUND($por47,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por47."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite48."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc48."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc48."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna48."</td>";
							$por48=(($wtotalc48+$wtotalna48)/$cont)*100;
							$por48=ROUND($por48,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por48."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite49."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc49."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc49."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna49."</td>";
							$por49=(($wtotalc49+$wtotalna49)/$cont)*100;
							$por49=ROUND($por49,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por49."%</td></tr>";

						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite50."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc50."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc50."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna50."</td>";
							$por50=(($wtotalc50+$wtotalna50)/$cont)*100;
							$por50=ROUND($por50,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por50."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite51."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc51."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc51."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna51."</td>";
							$por51=(($wtotalc51+$wtotalna51)/$cont)*100;
							$por51=ROUND($por51,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por51."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite52."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc52."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc52."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna52."</td>";
							$por52=(($wtotalc52+$wtotalna52)/$cont)*100;
							$por52=ROUND($por52,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por52."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite53."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc53."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc53."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna53."</td>";
							$por53=(($wtotalc53+$wtotalna53)/$cont)*100;
							$por53=ROUND($por53,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por53."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite54."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc54."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc54."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna54."</td>";
							$por54=(($wtotalc54+$wtotalna54)/$cont)*100;
							$por54=ROUND($por54,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por54."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite55."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc55."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc55."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna55."</td>";
							$por55=(($wtotalc55+$wtotalna55)/$cont)*100;
							$por55=ROUND($por55,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por55."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite56."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc56."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc56."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna56."</td>";
							$por56=(($wtotalc56+$wtotalna56)/$cont)*100;
							$por56=ROUND($por56,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por56."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite57."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc57."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc57."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna57."</td>";
							$por57=(($wtotalc57+$wtotalna57)/$cont)*100;
							$por57=ROUND($por57,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por57."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite58."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc58."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc58."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna58."</td>";
							$por58=(($wtotalc58+$wtotalna58)/$cont)*100;
							$por58=ROUND($por58,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por58."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite59."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc59."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc59."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna59."</td>";
							$por59=(($wtotalc59+$wtotalna59)/$cont)*100;
							$por59=ROUND($por59,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por59."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite60."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc60."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc60."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna60."</td>";
							$por60=(($wtotalc60+$wtotalna60)/$cont)*100;
							$por60=ROUND($por60,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por60."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite61."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc61."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc61."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna61."</td>";
							$por61=(($wtotalc61+$wtotalna61)/$cont)*100;
							$por61=ROUND($por61,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por61."%</td></tr>";
							
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite62."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc62."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc62."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna62."</td>";
							$por62=(($wtotalc62+$wtotalna62)/$cont)*100;
							$por62=ROUND($por62,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por62."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite63."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc63."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc63."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna63."</td>";
							$por63=(($wtotalc63+$wtotalna63)/$cont)*100;
							$por63=ROUND($por63,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por63."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite64."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc64."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc64."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna64."</td>";
							$por64=(($wtotalc64+$wtotalna64)/$cont)*100;
							$por64=ROUND($por64,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por64."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite65."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc65."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc65."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna65."</td>";
							$por65=(($wtotalc65+$wtotalna65)/$cont)*100;
							$por65=ROUND($por65,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por65."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite66."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc66."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc66."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna66."</td>";
							$por66=(($wtotalc66+$wtotalna66)/$cont)*100;
							$por66=ROUND($por66,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por66."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite67."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc67."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc67."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna67."</td>";
							$por67=(($wtotalc67+$wtotalna67)/$cont)*100;
							$por67=ROUND($por67,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por67."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite68."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc68."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc68."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna68."</td>";
							$por68=(($wtotalc68+$wtotalna68)/$cont)*100;
							$por68=ROUND($por68,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por68."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite69."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc69."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc69."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna69."</td>";
							$por69=(($wtotalc69+$wtotalna69)/$cont)*100;
							$por69=ROUND($por69,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por69."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite70."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc70."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc70."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna70."</td>";
							$por70=(($wtotalc70+$wtotalna70)/$cont)*100;
							$por70=ROUND($por70,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por70."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite71."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc71."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc71."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna71."</td>";
							$por71=(($wtotalc71+$wtotalna71)/$cont)*100;
							$por71=ROUND($por71,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por71."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite72."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc72."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc72."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna72."</td>";
							$por72=(($wtotalc72+$wtotalna72)/$cont)*100;
							$por72=ROUND($por72,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por72."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite73."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc73."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc73."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna73."</td>";
							$por73=(($wtotalc73+$wtotalna73)/$cont)*100;
							$por73=ROUND($por73,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por73."%</td></tr>";

						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite74."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc74."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc74."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna74."</td>";
							$por74=(($wtotalc74+$wtotalna74)/$cont)*100;
							$por74=ROUND($por74,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por74."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite75."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc75."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc75."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna75."</td>";
							$por75=(($wtotalc75+$wtotalna75)/$cont)*100;
							$por75=ROUND($por75,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por75."%</td></tr>";
							
						
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";
						echo"<td align=left colspan='5' bgcolor=".$color2."><strong>TOTAL GENERAL CENTRO DE COSTOS      (Historias Evaluadas:".$cont.")</strong></td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgennc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenna."</td>";
							$wtotpor=(($wtotgenc+$wtotgenna)/($wtotgenc+$wtotgenna+$wtotgennc))*100;
							$wtotpor=ROUND($wtotpor,2);
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotpor."%</td></tr>";
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr></table>";   
						
					$wtotalc6=0;	$wtotalnc6=0;	$wtotalna6=0;	$por6=0;
					$wtotalc7=0;	$wtotalnc7=0;	$wtotalna7=0;	$por7=0;
					$wtotalc8=0;	$wtotalnc8=0;	$wtotalna8=0;	$por8=0;
					$wtotalc9=0;	$wtotalnc9=0;	$wtotalna9=0;	$por9=0;
					$wtotalc10=0;	$wtotalnc10=0;	$wtotalna10=0;	$por10=0;
					$wtotalc11=0;	$wtotalnc11=0;	$wtotalna11=0;	$por11=0;
					$wtotalc12=0;	$wtotalnc12=0;	$wtotalna12=0;	$por12=0;
					$wtotalc13=0;	$wtotalnc13=0;	$wtotalna13=0;	$por13=0;
					$wtotalc14=0;	$wtotalnc14=0;	$wtotalna14=0;	$por14=0;
					$wtotalc15=0;	$wtotalnc15=0;	$wtotalna15=0;	$por15=0;
					$wtotalc16=0;	$wtotalnc16=0;	$wtotalna16=0;	$por16=0;
					$wtotalc17=0;	$wtotalnc17=0;	$wtotalna17=0;	$por17=0;
					$wtotalc18=0;	$wtotalnc18=0;	$wtotalna18=0;	$por18=0;
					$wtotalc19=0;	$wtotalnc19=0;	$wtotalna19=0;	$por19=0;
					$wtotalc20=0;	$wtotalnc20=0;	$wtotalna20=0;	$por20=0;
					$wtotalc21=0;	$wtotalnc21=0;	$wtotalna21=0;	$por21=0;
					$wtotalc22=0;	$wtotalnc22=0;	$wtotalna22=0;	$por22=0;
					$wtotalc23=0;	$wtotalnc23=0;	$wtotalna23=0;	$por23=0;
					$wtotalc24=0;	$wtotalnc24=0;	$wtotalna24=0;	$por24=0;
					$wtotalc25=0;	$wtotalnc25=0;	$wtotalna25=0;	$por25=0;
					$wtotalc26=0;	$wtotalnc26=0;	$wtotalna26=0;	$por26=0;
					$wtotalc27=0;	$wtotalnc27=0;	$wtotalna27=0;	$por27=0;
					$wtotalc28=0;	$wtotalnc28=0;	$wtotalna28=0;	$por28=0;
					$wtotalc29=0;	$wtotalnc29=0;	$wtotalna29=0;	$por29=0;
					$wtotalc30=0;	$wtotalnc30=0;	$wtotalna30=0;	$por30=0;
					$wtotalc31=0;	$wtotalnc31=0;	$wtotalna31=0;	$por31=0;
					$wtotalc32=0;	$wtotalnc32=0;	$wtotalna32=0;	$por32=0;
					$wtotalc33=0;	$wtotalnc33=0;	$wtotalna33=0;	$por33=0;
					$wtotalc34=0;	$wtotalnc34=0;	$wtotalna34=0;	$por34=0;
					$wtotalc35=0;	$wtotalnc35=0;	$wtotalna35=0;	$por35=0;
					$wtotalc36=0;	$wtotalnc36=0;	$wtotalna36=0;	$por36=0;
					$wtotalc37=0;	$wtotalnc37=0;	$wtotalna37=0;	$por37=0;
					$wtotalc38=0;	$wtotalnc38=0;	$wtotalna38=0;	$por38=0;
					$wtotalc39=0;	$wtotalnc39=0;	$wtotalna39=0;	$por39=0;
					$wtotalc40=0;	$wtotalnc40=0;	$wtotalna40=0;	$por40=0;
					$wtotalc41=0;	$wtotalnc41=0;	$wtotalna41=0;	$por41=0;
					$wtotalc42=0;	$wtotalnc42=0;	$wtotalna42=0;	$por42=0;
					$wtotalc43=0;	$wtotalnc43=0;	$wtotalna43=0;	$por43=0;
					$wtotalc44=0;	$wtotalnc44=0;	$wtotalna44=0;	$por44=0;
					$wtotalc45=0;	$wtotalnc45=0;	$wtotalna45=0;	$por45=0;
					$wtotalc46=0;	$wtotalnc46=0;	$wtotalna46=0;	$por46=0;
					$wtotalc47=0;	$wtotalnc47=0;	$wtotalna47=0;	$por47=0;
					$wtotalc48=0;	$wtotalnc48=0;	$wtotalna48=0;	$por48=0;
					$wtotalc49=0;	$wtotalnc49=0;	$wtotalna49=0;	$por49=0;
					$wtotalc50=0;	$wtotalnc50=0;	$wtotalna50=0;	$por50=0;
					$wtotalc51=0;	$wtotalnc51=0;	$wtotalna51=0;	$por51=0;
					$wtotalc52=0;	$wtotalnc52=0;	$wtotalna52=0;	$por52=0;
					$wtotalc53=0;	$wtotalnc53=0;	$wtotalna53=0;	$por53=0;
					$wtotalc54=0;	$wtotalnc54=0;	$wtotalna54=0;	$por54=0;
					$wtotalc55=0;	$wtotalnc55=0;	$wtotalna55=0;	$por55=0;
					$wtotalc56=0;	$wtotalnc56=0;	$wtotalna56=0;	$por56=0;
					$wtotalc57=0;	$wtotalnc57=0;	$wtotalna57=0;	$por57=0;
					$wtotalc58=0;	$wtotalnc58=0;	$wtotalna58=0;	$por58=0;
					$wtotalc59=0;	$wtotalnc59=0;	$wtotalna59=0;	$por59=0;
					$wtotalc60=0;	$wtotalnc60=0;	$wtotalna60=0;	$por60=0;
					$wtotalc61=0;	$wtotalnc61=0;	$wtotalna61=0;	$por61=0;
					$wtotalc62=0;	$wtotalnc62=0;	$wtotalna62=0;	$por62=0;
					$wtotalc63=0;	$wtotalnc63=0;	$wtotalna63=0;	$por63=0;
					$wtotalc64=0;	$wtotalnc64=0;	$wtotalna64=0;	$por64=0;
					$wtotalc65=0;	$wtotalnc65=0;	$wtotalna65=0;	$por65=0;
					$wtotalc66=0;	$wtotalnc66=0;	$wtotalna66=0;	$por66=0;
					$wtotalc67=0;	$wtotalnc67=0;	$wtotalna67=0;	$por67=0;
					$wtotalc68=0;	$wtotalnc68=0;	$wtotalna68=0;	$por68=0;
					$wtotalc69=0;	$wtotalnc69=0;	$wtotalna69=0;	$por69=0;
					$wtotalc70=0;	$wtotalnc70=0;	$wtotalna70=0;	$por70=0;
					$wtotalc71=0;	$wtotalnc71=0;	$wtotalna71=0;	$por71=0;
					$wtotalc72=0;	$wtotalnc72=0;	$wtotalna72=0;	$por72=0;
					$wtotalc73=0;	$wtotalnc73=0;	$wtotalna73=0;	$por73=0;
					$wtotalc74=0;	$wtotalnc74=0;	$wtotalna74=0;	$por74=0;
					$wtotalc75=0;	$wtotalnc75=0;	$wtotalna75=0;	$por75=0;
					
					$wglobalc=$wglobalc+$wtotgenc;
					$wglobalnc=$wglobalnc+$wtotgennc;
					$wglobalna=$wglobalna+$wtotgenna;
					$contgen=$contgen+$cont;
					
					$wtotgenc=0;
					$wtotgennc=0;
					$wtotgenna=0;
					$cont=0;
					$wtotpor=0;
					
				}
			// Se define si se muestra el título del centro de costos
			if (($bandera1==0) or ($wccocod!=$row['Ccosto']))
				{   
					$waux=$wccocod;
					$wccocod=$row['Ccosto'];
					$bandera1=1;
					$pinto=0;	// Define si muestro el encabezado de la tabla
					echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";   
					echo "<tr><td align='left' class='encabezadoTabla' colspan='9' height='31'>&nbsp;CENTRO DE COSTOS: ".$wccocod."</td></tr></table>";   
				}
			 
		 	
				// Se establece la clase para la fila en el ciclo actual
				if (is_int ($k/2))
					$clase='fila1';
				else
					$clase='fila2';
				$k=$k+1;

				// Asigno los totales para responsable, centro de costos y total general
				
					if ($row[ACC_1_1_3]=="C-CUMPLE")
						$wtotalc6=$wtotalc6 + 1;
					if ($row[ACC_1_1_3]=="NA-NO APLICA")
						$wtotalna6=$wtotalna6 + 1;
					if ($row[ACC_1_1_3]=="NC-NO CUMPLE")
						$wtotalnc6=$wtotalnc6 + 1;
					if ($row[ACC_1_4EM5]=="C-CUMPLE")
						$wtotalc7=$wtotalc7 + 1;
					if ($row[ACC_1_4EM5]=="NA-NO APLICA")
						$wtotalna7=$wtotalna7 + 1;
					if ($row[ACC_1_4EM5]=="NC-NO CUMPLE")
						$wtotalnc7=$wtotalnc7 + 1;
					if ($row[ACC_1_4EM6]=="C-CUMPLE")
						$wtotalc8=$wtotalc8 + 1;
					if ($row[ACC_1_4EM6]=="NA-NO APLICA")
						$wtotalna8=$wtotalna8 + 1;
					if ($row[ACC_1_4EM6]=="NC-NO CUMPLE")
						$wtotalnc8=$wtotalnc8 + 1;
					if ($row[ACC_3_2]=="C-CUMPLE")
						$wtotalc9=$wtotalc9 + 1;
					if ($row[ACC_3_2]=="NA-NO APLICA")
						$wtotalna9=$wtotalna9 + 1;
					if ($row[ACC_3_2]=="NC-NO CUMPLE")
						$wtotalnc9=$wtotalnc9 + 1;
					if ($row[ACC_3_2EM2]=="C-CUMPLE")
						$wtotalc10=$wtotalc10 + 1;
					if ($row[ACC_3_2EM2]=="NA-NO APLICA")
						$wtotalna10=$wtotalna10 + 1;
					if ($row[ACC_3_2EM2]=="NC-NO CUMPLE")
						$wtotalnc10=$wtotalnc10 + 1;
					if ($row[ACC_3_2_1]=="C-CUMPLE")
						$wtotalc11=$wtotalc11 + 1;
					if ($row[ACC_3_2_1]=="NA-NO APLICA")
						$wtotalna11=$wtotalna11 + 1;
					if ($row[ACC_3_2_1]=="NC-NO CUMPLE")
						$wtotalnc11=$wtotalnc11 + 1;
					if ($row[ACC_3_3]=="C-CUMPLE")
						$wtotalc12=$wtotalc12 + 1;
					if ($row[ACC_3_3]=="NA-NO APLICA")
						$wtotalna12=$wtotalna12 + 1;
					if ($row[ACC_3_3]=="NC-NO CUMPLE")
						$wtotalnc12=$wtotalnc12 + 1;
					if ($row[ACC_4_4]=="C-CUMPLE")
						$wtotalc13=$wtotalc13 + 1;
					if ($row[ACC_4_4]=="NA-NO APLICA")
						$wtotalna13=$wtotalna13 + 1;
					if ($row[ACC_4_4]=="NC-NO CUMPLE")
						$wtotalnc13=$wtotalnc13 + 1;
					if ($row[ACC_4_4EM1]=="C-CUMPLE")
						$wtotalc14=$wtotalc14 + 1;
					if ($row[ACC_4_4EM1]=="NA-NO APLICA")
						$wtotalna14=$wtotalna14 + 1;
					if ($row[ACC_4_4EM1]=="NC-NO CUMPLE")
						$wtotalnc14=$wtotalnc14 + 1;
					if ($row[ACC_4_4EM3]=="C-CUMPLE")
						$wtotalc15=$wtotalc15 + 1;
					if ($row[ACC_4_4EM3]=="NA-NO APLICA")
						$wtotalna15=$wtotalna15 + 1;
					if ($row[ACC_4_4EM3]=="NC-NO CUMPLE")
						$wtotalnc15=$wtotalnc15 + 1;
					if ($row[PFR_8]=="C-CUMPLE")
						$wtotalc16=$wtotalc16 + 1;
					if ($row[PFR_8]=="NA-NO APLICA")
						$wtotalna16=$wtotalna16 + 1;
					if ($row[PFR_8]=="NC-NO CUMPLE")
						$wtotalnc16=$wtotalnc16 + 1;
					if ($row[AOP_1_3EM3]=="C-CUMPLE")
						$wtotalc17=$wtotalc17 + 1;
					if ($row[AOP_1_3EM3]=="NA-NO APLICA")
						$wtotalna17=$wtotalna17 + 1;
					if ($row[AOP_1_3EM3]=="NC-NO CUMPLE")
						$wtotalnc17=$wtotalnc17 + 1;
					if ($row[AOP_1_3EM4]=="C-CUMPLE")
						$wtotalc18=$wtotalc18 + 1;
					if ($row[AOP_1_3EM4]=="NA-NO APLICA")
						$wtotalna18=$wtotalna18 + 1;
					if ($row[AOP_1_3EM4]=="NC-NO CUMPLE")
						$wtotalnc18=$wtotalnc18 + 1;
					if ($row[AOP_1_3_1EM3]=="C-CUMPLE")
						$wtotalc19=$wtotalc19 + 1;
					if ($row[AOP_1_3_1EM3]=="NA-NO APLICA")
						$wtotalna19=$wtotalna19 + 1;
					if ($row[AOP_1_3_1EM3]=="NC-NO CUMPLE")
						$wtotalnc19=$wtotalnc19 + 1;
					if ($row[AOP_1_5EM1]=="C-CUMPLE")
						$wtotalc20=$wtotalc20 + 1;
					if ($row[AOP_1_5EM1]=="NA-NO APLICA")
						$wtotalna20=$wtotalna20 + 1;
					if ($row[AOP_1_5EM1]=="NC-NO CUMPLE")
						$wtotalnc20=$wtotalnc20 + 1;
					if ($row[AOP_1_5_1EM2]=="C-CUMPLE")
						$wtotalc21=$wtotalc21 + 1;
					if ($row[AOP_1_5_1EM2]=="NA-NO APLICA")
						$wtotalna21=$wtotalna21 + 1;
					if ($row[AOP_1_5_1EM2]=="NC-NO CUMPLE")
						$wtotalnc21=$wtotalnc21 + 1;
					if ($row[AOP_1_9]=="C-CUMPLE")
						$wtotalc22=$wtotalc22 + 1;
					if ($row[AOP_1_9]=="NA-NO APLICA")
						$wtotalna22=$wtotalna22 + 1;
					if ($row[AOP_1_9]=="NC-NO CUMPLE")
						$wtotalnc22=$wtotalnc22 + 1;
					if ($row[AOP_1_10]=="C-CUMPLE")
						$wtotalc23=$wtotalc23 + 1;
					if ($row[AOP_1_10]=="NA-NO APLICA")
						$wtotalna23=$wtotalna23 + 1;
					if ($row[AOP_1_10]=="NC-NO CUMPLE")
						$wtotalnc23=$wtotalnc23 + 1;
					if ($row[AOP_2]=="C-CUMPLE")
						$wtotalc24=$wtotalc24 + 1;
					if ($row[AOP_2]=="NA-NO APLICA")
						$wtotalna24=$wtotalna24 + 1;
					if ($row[AOP_2]=="NC-NO CUMPLE")
						$wtotalnc24=$wtotalnc24 + 1;
					if ($row[AOP_2EM4]=="C-CUMPLE")
						$wtotalc25=$wtotalc25 + 1;
					if ($row[AOP_2EM4]=="NA-NO APLICA")
						$wtotalna25=$wtotalna25 + 1;
					if ($row[AOP_2EM4]=="NC-NO CUMPLE")
						$wtotalnc25=$wtotalnc25 + 1;
					if ($row[COP_2EM3]=="C-CUMPLE")
						$wtotalc26=$wtotalc26 + 1;
					if ($row[COP_2EM3]=="NA-NO APLICA")
						$wtotalna26=$wtotalna26 + 1;
					if ($row[COP_2EM3]=="NC-NO CUMPLE")
						$wtotalnc26=$wtotalnc26 + 1;
					if ($row[COP_2_1EM5]=="C-CUMPLE")
						$wtotalc27=$wtotalc27 + 1;
					if ($row[COP_2_1EM5]=="NA-NO APLICA")
						$wtotalna27=$wtotalna27 + 1;
					if ($row[COP_2_1EM5]=="NC-NO CUMPLE")
						$wtotalnc27=$wtotalnc27 + 1;
					if ($row[COP_2_2EM4]=="C-CUMPLE")
						$wtotalc28=$wtotalc28 + 1;
					if ($row[COP_2_2EM4]=="NA-NO APLICA")
						$wtotalna28=$wtotalna28 + 1;
					if ($row[COP_2_2EM4]=="NC-NO CUMPLE")
						$wtotalnc28=$wtotalnc28 + 1;
					if ($row[COP_2_3EM1]=="C-CUMPLE")
						$wtotalc29=$wtotalc29 + 1;
					if ($row[COP_2_3EM1]=="NA-NO APLICA")
						$wtotalna29=$wtotalna29 + 1;
					if ($row[COP_2_3EM1]=="NC-NO CUMPLE")
						$wtotalnc29=$wtotalnc29 + 1;
					if ($row[COP_2_3EM2]=="C-CUMPLE")
						$wtotalc30=$wtotalc30 + 1;
					if ($row[COP_2_3EM2]=="NA-NO APLICA")
						$wtotalna30=$wtotalna30 + 1;
					if ($row[COP_2_3EM2]=="NC-NO CUMPLE")
						$wtotalnc30=$wtotalnc30 + 1;
					if ($row[COP_4EM2]=="C-CUMPLE")
						$wtotalc31=$wtotalc31 + 1;
					if ($row[COP_4EM2]=="NA-NO APLICA")
						$wtotalna31=$wtotalna31 + 1;
					if ($row[COP_4EM2]=="NC-NO CUMPLE")
						$wtotalnc31=$wtotalnc31 + 1;
					if ($row[COP_5EM4]=="C-CUMPLE")
						$wtotalc32=$wtotalc32 + 1;
					if ($row[COP_5EM4]=="NA-NO APLICA")
						$wtotalna32=$wtotalna32 + 1;
					if ($row[COP_5EM4]=="NC-NO CUMPLE")
						$wtotalnc32=$wtotalnc32 + 1;
					if ($row[ASC_4EM4]=="C-CUMPLE")
						$wtotalc33=$wtotalc33 + 1;
					if ($row[ASC_4EM4]=="NA-NO APLICA")
						$wtotalna33=$wtotalna33 + 1;
					if ($row[ASC_4EM4]=="NC-NO CUMPLE")
						$wtotalnc33=$wtotalnc33 + 1;
					if ($row[ASC_5_2EM1]=="C-CUMPLE")
						$wtotalc34=$wtotalc34 + 1;
					if ($row[ASC_5_2EM1]=="NA-NO APLICA")
						$wtotalna34=$wtotalna34 + 1;
					if ($row[ASC_5_2EM1]=="NC-NO CUMPLE")
						$wtotalnc34=$wtotalnc34 + 1;
					if ($row[ASC_5_2EM2]=="C-CUMPLE")
						$wtotalc35=$wtotalc35 + 1;
					if ($row[ASC_5_2EM2]=="NA-NO APLICA")
						$wtotalna35=$wtotalna35 + 1;
					if ($row[ASC_5_2EM2]=="NC-NO CUMPLE")
						$wtotalnc35=$wtotalnc35 + 1;
					if ($row[ASC_5_2EM3]=="C-CUMPLE")
						$wtotalc36=$wtotalc36 + 1;
					if ($row[ASC_5_2EM3]=="NA-NO APLICA")
						$wtotalna36=$wtotalna36 + 1;
					if ($row[ASC_5_2EM3]=="NC-NO CUMPLE")
						$wtotalnc36=$wtotalnc36 + 1;
					if ($row[ASC_6EM2]=="C-CUMPLE")
						$wtotalc37=$wtotalc37 + 1;
					if ($row[ASC_6EM2]=="NA-NO APLICA")
						$wtotalna37=$wtotalna37 + 1;
					if ($row[ASC_6EM2]=="NC-NO CUMPLE")
						$wtotalnc37=$wtotalnc37 + 1;
					if ($row[ASC_6EM4]=="C-CUMPLE")
						$wtotalc38=$wtotalc38 + 1;
					if ($row[ASC_6EM4]=="NA-NO APLICA")
						$wtotalna38=$wtotalna38 + 1;
					if ($row[ASC_6EM4]=="NC-NO CUMPLE")
						$wtotalnc38=$wtotalnc38 + 1;
					if ($row[ASC_7EM1]=="C-CUMPLE")
						$wtotalc39=$wtotalc39 + 1;
					if ($row[ASC_7EM1]=="NA-NO APLICA")
						$wtotalna39=$wtotalna39 + 1;
					if ($row[ASC_7EM1]=="NC-NO CUMPLE")
						$wtotalnc39=$wtotalnc39 + 1;
					if ($row[ASC_7EM3]=="C-CUMPLE")
						$wtotalc40=$wtotalc40 + 1;
					if ($row[ASC_7EM3]=="NA-NO APLICA")
						$wtotalna40=$wtotalna40 + 1;
					if ($row[ASC_7EM3]=="NC-NO CUMPLE")
						$wtotalnc40=$wtotalnc40 + 1;
					if ($row[ASC_7_2EM1_1]=="C-CUMPLE")
						$wtotalc41=$wtotalc41 + 1;
					if ($row[ASC_7_2EM1_1]=="NA-NO APLICA")
						$wtotalna41=$wtotalna41 + 1;
					if ($row[ASC_7_2EM1_1]=="NC-NO CUMPLE")
						$wtotalnc41=$wtotalnc41 + 1;
					if ($row[ASC_7_2EM1_2]=="C-CUMPLE")
						$wtotalc42=$wtotalc42 + 1;
					if ($row[ASC_7_2EM1_2]=="NA-NO APLICA")
						$wtotalna42=$wtotalna42 + 1;
					if ($row[ASC_7_2EM1_2]=="NC-NO CUMPLE")
						$wtotalnc42=$wtotalnc42 + 1;
					if ($row[ASC_7_2EM1_3]=="C-CUMPLE")
						$wtotalc43=$wtotalc43 + 1;
					if ($row[ASC_7_2EM1_3]=="NA-NO APLICA")
						$wtotalna43=$wtotalna43 + 1;
					if ($row[ASC_7_2EM1_3]=="NC-NO CUMPLE")
						$wtotalnc43=$wtotalnc43 + 1;
					if ($row[ASC_7_2EM1_4]=="C-CUMPLE")
						$wtotalc44=$wtotalc44 + 1;
					if ($row[ASC_7_2EM1_4]=="NA-NO APLICA")
						$wtotalna44=$wtotalna44 + 1;
					if ($row[ASC_7_2EM1_4]=="NC-NO CUMPLE")
						$wtotalnc44=$wtotalnc44 + 1;
					if ($row[ASC_7_2EM1_5]=="C-CUMPLE")
						$wtotalc45=$wtotalc45 + 1;
					if ($row[ASC_7_2EM1_5]=="NA-NO APLICA")
						$wtotalna45=$wtotalna45 + 1;
					if ($row[ASC_7_2EM1_5]=="NC-NO CUMPLE")
						$wtotalnc45=$wtotalnc45 + 1;
					if ($row[ASC_7_2EM1_6]=="C-CUMPLE")
						$wtotalc46=$wtotalc46 + 1;
					if ($row[ASC_7_2EM1_6]=="NA-NO APLICA")
						$wtotalna46=$wtotalna46 + 1;
					if ($row[ASC_7_2EM1_6]=="NC-NO CUMPLE")
						$wtotalnc46=$wtotalnc46 + 1;
					if ($row[ASC_7_2EM4]=="C-CUMPLE")
						$wtotalc47=$wtotalc47 + 1;
					if ($row[ASC_7_2EM4]=="NA-NO APLICA")
						$wtotalna47=$wtotalna47 + 1;
					if ($row[ASC_7_2EM4]=="NC-NO CUMPLE")
						$wtotalnc47=$wtotalnc47 + 1;
					if ($row[ASC_7_3EM2]=="C-CUMPLE")
						$wtotalc48=$wtotalc48 + 1;
					if ($row[ASC_7_3EM2]=="NA-NO APLICA")
						$wtotalna48=$wtotalna48 + 1;
					if ($row[ASC_7_3EM2]=="NC-NO CUMPLE")
						$wtotalnc48=$wtotalnc48 + 1;
					if ($row[ASC_7_4EM2]=="C-CUMPLE")
						$wtotalc49=$wtotalc49 + 1;
					if ($row[ASC_7_4EM2]=="NA-NO APLICA")
						$wtotalna49=$wtotalna49 + 1;
					if ($row[ASC_7_4EM2]=="NC-NO CUMPLE")
						$wtotalnc49=$wtotalnc49 + 1;
					if ($row[ASC_7_4EM3]=="C-CUMPLE")
						$wtotalc50=$wtotalc50 + 1;
					if ($row[ASC_7_4EM3]=="NA-NO APLICA")
						$wtotalna50=$wtotalna50 + 1;
					if ($row[ASC_7_4EM3]=="NC-NO CUMPLE")
						$wtotalnc50=$wtotalnc50 + 1;
					if ($row[ASC_7_4EM4]=="C-CUMPLE")
						$wtotalc51=$wtotalc51 + 1;
					if ($row[ASC_7_4EM4]=="NA-NO APLICA")
						$wtotalna51=$wtotalna51 + 1;
					if ($row[ASC_7_4EM4]=="NC-NO CUMPLE")
						$wtotalnc51=$wtotalnc51 + 1;
					if ($row[MMU_4_3EM1]=="C-CUMPLE")
						$wtotalc52=$wtotalc52 + 1;
					if ($row[MMU_4_3EM1]=="NA-NO APLICA")
						$wtotalna52=$wtotalna52 + 1;
					if ($row[MMU_4_3EM1]=="NC-NO CUMPLE")
						$wtotalnc52=$wtotalnc52 + 1;
					if ($row[MMU_4_3EM2]=="C-CUMPLE")
						$wtotalc53=$wtotalc53 + 1;
					if ($row[MMU_4_3EM2]=="NA-NO APLICA")
						$wtotalna53=$wtotalna53 + 1;
					if ($row[MMU_4_3EM2]=="NC-NO CUMPLE")
						$wtotalnc53=$wtotalnc53 + 1;
					if ($row[MMU_4_3EM3]=="C-CUMPLE")
						$wtotalc54=$wtotalc54 + 1;
					if ($row[MMU_4_3EM3]=="NA-NO APLICA")
						$wtotalna54=$wtotalna54 + 1;
					if ($row[MMU_4_3EM3]=="NC-NO CUMPLE")
						$wtotalnc54=$wtotalnc54 + 1;
					if ($row[MMU_6_1EM5]=="C-CUMPLE")
						$wtotalc55=$wtotalc55 + 1;
					if ($row[MMU_6_1EM5]=="NA-NO APLICA")
						$wtotalna55=$wtotalna55 + 1;
					if ($row[MMU_6_1EM5]=="NC-NO CUMPLE")
						$wtotalnc55=$wtotalnc55 + 1;
					if ($row[MMU_7EM4]=="C-CUMPLE")
						$wtotalc56=$wtotalc56 + 1;
					if ($row[MMU_7EM4]=="NA-NO APLICA")
						$wtotalna56=$wtotalna56 + 1;
					if ($row[MMU_7EM4]=="NC-NO CUMPLE")
						$wtotalnc56=$wtotalnc56 + 1;
					if ($row[PFE_2EM2]=="C-CUMPLE")
						$wtotalc57=$wtotalc57 + 1;
					if ($row[PFE_2EM2]=="NA-NO APLICA")
						$wtotalna57=$wtotalna57 + 1;
					if ($row[PFE_2EM2]=="NC-NO CUMPLE")
						$wtotalnc57=$wtotalnc57 + 1;
					if ($row[PFE_2EM3]=="C-CUMPLE")
						$wtotalc58=$wtotalc58 + 1;
					if ($row[PFE_2EM3]=="NA-NO APLICA")
						$wtotalna58=$wtotalna58 + 1;
					if ($row[PFE_2EM3]=="NC-NO CUMPLE")
						$wtotalnc58=$wtotalnc58 + 1;
					if ($row[MCI_8EM2]=="C-CUMPLE")
						$wtotalc59=$wtotalc59 + 1;
					if ($row[MCI_8EM2]=="NA-NO APLICA")
						$wtotalna59=$wtotalna59 + 1;
					if ($row[MCI_8EM2]=="NC-NO CUMPLE")
						$wtotalnc59=$wtotalnc59 + 1;
					if ($row[MCI_8EM3]=="C-CUMPLE")
						$wtotalc60=$wtotalc60 + 1;
					if ($row[MCI_8EM3]=="NA-NO APLICA")
						$wtotalna60=$wtotalna60 + 1;
					if ($row[MCI_8EM3]=="NC-NO CUMPLE")
						$wtotalnc60=$wtotalnc60 + 1;
					if ($row[MCI_8EM4]=="C-CUMPLE")
						$wtotalc61=$wtotalc61 + 1;
					if ($row[MCI_8EM4]=="NA-NO APLICA")
						$wtotalna61=$wtotalna61 + 1;
					if ($row[MCI_8EM4]=="NC-NO CUMPLE")
						$wtotalnc61=$wtotalnc61 + 1;
					if ($row[MCI_8EM5]=="C-CUMPLE")
						$wtotalc62=$wtotalc62 + 1;
					if ($row[MCI_8EM5]=="NA-NO APLICA")
						$wtotalna62=$wtotalna62 + 1;
					if ($row[MCI_8EM5]=="NC-NO CUMPLE")
						$wtotalnc62=$wtotalnc62 + 1;
					if ($row[MCI_8EM6]=="C-CUMPLE")
						$wtotalc63=$wtotalc63 + 1;
					if ($row[MCI_8EM6]=="NA-NO APLICA")
						$wtotalna63=$wtotalna63 + 1;
					if ($row[MCI_8EM6]=="NC-NO CUMPLE")
						$wtotalnc63=$wtotalnc63 + 1;
					if ($row[MCI_8EM7]=="C-CUMPLE")
						$wtotalc64=$wtotalc64 + 1;
					if ($row[MCI_8EM7]=="NA-NO APLICA")
						$wtotalna64=$wtotalna64 + 1;
					if ($row[MCI_8EM7]=="NC-NO CUMPLE")
						$wtotalnc64=$wtotalnc64 + 1;
					if ($row[MCI_19_1EM2]=="C-CUMPLE")
						$wtotalc65=$wtotalc65 + 1;
					if ($row[MCI_19_1EM2]=="NA-NO APLICA")
						$wtotalna65=$wtotalna65 + 1;
					if ($row[MCI_19_1EM2]=="NC-NO CUMPLE")
						$wtotalnc65=$wtotalnc65 + 1;
					if ($row[MCI_19_1EM3]=="C-CUMPLE")
						$wtotalc66=$wtotalc66 + 1;
					if ($row[MCI_19_1EM3]=="NA-NO APLICA")
						$wtotalna66=$wtotalna66 + 1;
					if ($row[MCI_19_1EM3]=="NC-NO CUMPLE")
						$wtotalnc66=$wtotalnc66 + 1;
					if ($row[MCI_19_1EM4]=="C-CUMPLE")
						$wtotalc67=$wtotalc67 + 1;
					if ($row[MCI_19_1EM4]=="NA-NO APLICA")
						$wtotalna67=$wtotalna67 + 1;
					if ($row[MCI_19_1EM4]=="NC-NO CUMPLE")
						$wtotalnc67=$wtotalnc67 + 1;
					if ($row[MCI_19_1EM5]=="C-CUMPLE")
						$wtotalc68=$wtotalc68 + 1;
					if ($row[MCI_19_1EM5]=="NA-NO APLICA")
						$wtotalna68=$wtotalna68 + 1;
					if ($row[MCI_19_1EM5]=="NC-NO CUMPLE")
						$wtotalnc68=$wtotalnc68 + 1;
					if ($row[MCI_19_1_1EM1]=="C-CUMPLE")
						$wtotalc69=$wtotalc69 + 1;
					if ($row[MCI_19_1_1EM1]=="NA-NO APLICA")
						$wtotalna69=$wtotalna69 + 1;
					if ($row[MCI_19_1_1EM1]=="NC-NO CUMPLE")
						$wtotalnc69=$wtotalnc69 + 1;
					if ($row[MCI_19_1_1EM2]=="C-CUMPLE")
						$wtotalc70=$wtotalc70 + 1;
					if ($row[MCI_19_1_1EM2]=="NA-NO APLICA")
						$wtotalna70=$wtotalna70 + 1;
					if ($row[MCI_19_1_1EM2]=="NC-NO CUMPLE")
						$wtotalnc70=$wtotalnc70 + 1;
					if ($row[MCI_19_1_1EM3]=="C-CUMPLE")
						$wtotalc71=$wtotalc71 + 1;
					if ($row[MCI_19_1_1EM3]=="NA-NO APLICA")
						$wtotalna71=$wtotalna71 + 1;
					if ($row[MCI_19_1_1EM3]=="NC-NO CUMPLE")
						$wtotalnc71=$wtotalnc71 + 1;
					if ($row[MCI_19_1_1EM4]=="C-CUMPLE")
						$wtotalc72=$wtotalc72 + 1;
					if ($row[MCI_19_1_1EM4]=="NA-NO APLICA")
						$wtotalna72=$wtotalna72 + 1;
					if ($row[MCI_19_1_1EM4]=="NC-NO CUMPLE")
						$wtotalnc72=$wtotalnc72 + 1;
					if ($row[MCI_19_3EM1]=="C-CUMPLE")
						$wtotalc73=$wtotalc73 + 1;
					if ($row[MCI_19_3EM1]=="NA-NO APLICA")
						$wtotalna73=$wtotalna73 + 1;
					if ($row[MCI_19_3EM1]=="NC-NO CUMPLE")
						$wtotalnc73=$wtotalnc73 + 1;
					if ($row[MCI_19_3EM2]=="C-CUMPLE")
						$wtotalc74=$wtotalc74 + 1;
					if ($row[MCI_19_3EM2]=="NA-NO APLICA")
						$wtotalna74=$wtotalna74 + 1;
					if ($row[MCI_19_3EM2]=="NC-NO CUMPLE")
						$wtotalnc74=$wtotalnc74 + 1;
					if ($row[MCI_19_3EM3]=="C-CUMPLE")
						$wtotalc75=$wtotalc75 + 1;
					if ($row[MCI_19_3EM3]=="NA-NO APLICA")
						$wtotalna75=$wtotalna75 + 1;
					if ($row[MCI_19_3EM3]=="NC-NO CUMPLE")
						$wtotalnc75=$wtotalnc75 + 1;
					
				$i= $i + 1;
				$cont=$cont + 1;
				
		}
		
		// Si no se tienen resultados se muestra el mensaje correspondiente
		if ($cont==0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=570 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><b>Sin ningún documento en el rango de fechas seleccionado</b></td><tr>";
			}
		// Muestra el total del responsable o caja y del centro de costos
		// Esto debido a que en el último ciclo estos no se muestran
		else
			{	
			
				if ($wccocod==$row['Ccosto'])
				{	
					$wtotgenc=$wtotalc6+$wtotalc7+$wtotalc8+$wtotalc9+$wtotalc10+$wtotalc11+$wtotalc12+$wtotalc13+$wtotalc14+$wtotalc15+
							  $wtotalc16+$wtotalc17+$wtotalc18+$wtotalc19+$wtotalc20+$wtotalc21+$wtotalc22+$wtotalc23+$wtotalc24+$wtotalc25+
							  $wtotalc26+$wtotalc27+$wtotalc28+$wtotalc29+$wtotalc30+$wtotalc31+$wtotalc32+$wtotalc33+$wtotalc34+$wtotalc35+
							  $wtotalc36+$wtotalc37+$wtotalc38+$wtotalc39+$wtotalc40+$wtotalc41+$wtotalc42+$wtotalc43+$wtotalc44+$wtotalc45+
							  $wtotalc46+$wtotalc47+$wtotalc48+$wtotalc49+$wtotalc50+$wtotalc51+$wtotalc52+$wtotalc53+$wtotalc54+$wtotalc55+
							  $wtotalc56+$wtotalc57+$wtotalc58+$wtotalc59+$wtotalc60+$wtotalc61+$wtotalc62+$wtotalc63+$wtotalc64+$wtotalc65+
							  $wtotalc66+$wtotalc67+$wtotalc68+$wtotalc69+$wtotalc70+$wtotalc71+$wtotalc72+$wtotalc73+$wtotalc74+$wtotalc75;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9+$wtotalnc10+$wtotalnc11+$wtotalnc12+$wtotalnc13+$wtotalnc14+
							   $wtotalnc15+$wtotalnc16+$wtotalnc17+$wtotalnc18+$wtotalnc19+$wtotalnc20+$wtotalnc21+$wtotalnc22+$wtotalnc23+
							   $wtotalnc24+$wtotalnc25+$wtotalnc26+$wtotalnc27+$wtotalnc28+$wtotalnc29+$wtotalnc30+$wtotalnc31+$wtotalnc32+
							   $wtotalnc33+$wtotalnc34+$wtotalnc35+$wtotalnc36+$wtotalnc37+$wtotalnc38+$wtotalnc39+$wtotalnc40+$wtotalnc41+
							   $wtotalnc42+$wtotalnc43+$wtotalnc44+$wtotalnc45+$wtotalnc46+$wtotalnc47+$wtotalnc48+$wtotalnc49+$wtotalnc50+
							   $wtotalnc51+$wtotalnc52+$wtotalnc53+$wtotalnc54+$wtotalnc55+$wtotalnc56+$wtotalnc57+$wtotalnc58+$wtotalnc59+
							   $wtotalnc60+$wtotalnc61+$wtotalnc62+$wtotalnc63+$wtotalnc64+$wtotalnc65+$wtotalnc66+$wtotalnc67+$wtotalnc68+
							   $wtotalnc69+$wtotalnc70+$wtotalnc71+$wtotalnc72+$wtotalnc73+$wtotalnc74+$wtotalnc75;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9+$wtotalna10+$wtotalna11+$wtotalna12+$wtotalna13+$wtotalna14+$wtotalna15+
							   $wtotalna16+$wtotalna17+$wtotalna18+$wtotalna19+$wtotalna20+$wtotalna21+$wtotalna22+$wtotalna23+$wtotalna24+$wtotalna25+
							   $wtotalna26+$wtotalna27+$wtotalna28+$wtotalna29+$wtotalna30+$wtotalna31+$wtotalna32+$wtotalna33+$wtotalna34+$wtotalna35+
							   $wtotalna36+$wtotalna37+$wtotalna38+$wtotalna39+$wtotalna40+$wtotalna41+$wtotalna42+$wtotalna43+$wtotalna44+$wtotalna45+
							   $wtotalna46+$wtotalna47+$wtotalna48+$wtotalna49+$wtotalna50+$wtotalna51+$wtotalna52+$wtotalna53+$wtotalna54+$wtotalna55+
							   $wtotalna56+$wtotalna57+$wtotalna58+$wtotalna59+$wtotalna60+$wtotalna61+$wtotalna62+$wtotalna63+$wtotalna64+$wtotalna65+
							   $wtotalna66+$wtotalna67+$wtotalna68+$wtotalna69+$wtotalna70+$wtotalna71+$wtotalna72+$wtotalna73+$wtotalna74+$wtotalna75;
							   
					
					echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>"; 
					echo "<tr class='encabezadoTabla' height='31'>";
						echo"<td align=center border=1 colspan='5'>CRITERIOS</td>";
						echo "<td align=center border=1 >CUMPLE</td>";
						echo "<td align=center border=1>NO CUMPLE</td>";
						echo "<td align=center border=1 >NO APLICA</td>";
						echo "<td align=center border=1>% DE CUMPLIMIENTO</td></tr>";
					echo "<tr><td align='left' colspan='5'>&nbsp;</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna6."</td>";
							$por6=(($wtotalc6+$wtotalna6)/$cont)*100;
							$por6=ROUND($por6,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por6."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna7."</td>";
							$por7=(($wtotalc7+$wtotalna7)/$cont)*100;
							$por7=ROUND($por7,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por7."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna8."</td>";
							$por8=(($wtotalc8+$wtotalna8)/$cont)*100;
							$por8=ROUND($por8,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por8."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna9."</td>";
							$por9=(($wtotalc9+$wtotalna9)/$cont)*100;
							$por9=ROUND($por9,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por9."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite10."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc10."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc10."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna10."</td>";
							$por10=(($wtotalc10+$wtotalna10)/$cont)*100;
							$por10=ROUND($por10,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por10."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite11."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc11."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc11."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna11."</td>";
							$por11=(($wtotalc11+$wtotalna11)/$cont)*100;
							$por11=ROUND($por11,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por11."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite12."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc12."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc12."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna12."</td>";
							$por12=(($wtotalc12+$wtotalna12)/$cont)*100;
							$por12=ROUND($por12,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por12."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite13."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc13."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc13."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna13."</td>";
							$por13=(($wtotalc13+$wtotalna13)/$cont)*100;
							$por13=ROUND($por13,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por13."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite14."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc14."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc14."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna14."</td>";
							$por14=(($wtotalc14+$wtotalna14)/$cont)*100;
							$por14=ROUND($por14,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por14."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite15."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc15."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc15."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna15."</td>";
							$por15=(($wtotalc15+$wtotalna15)/$cont)*100;
							$por15=ROUND($por15,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por15."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite16."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc16."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc16."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna16."</td>";
							$por16=(($wtotalc16+$wtotalna16)/$cont)*100;
							$por16=ROUND($por16,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por16."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite17."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc17."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc17."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna17."</td>";
							$por17=(($wtotalc17+$wtotalna17)/$cont)*100;
							$por17=ROUND($por17,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por17."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite18."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna18."</td>";
							$por18=(($wtotalc18+$wtotalna18)/$cont)*100;
							$por18=ROUND($por18,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por18."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna19."</td>";
							$por19=(($wtotalc19+$wtotalna19)/$cont)*100;
							$por19=ROUND($por19,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por19."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite20."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna20."</td>";
							$por20=(($wtotalc20+$wtotalna20)/$cont)*100;
							$por20=ROUND($por20,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por20."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna21."</td>";
							$por21=(($wtotalc21+$wtotalna21)/$cont)*100;
							$por21=ROUND($por21,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por21."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite22."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc22."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc22."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna22."</td>";
							$por22=(($wtotalc22+$wtotalna22)/$cont)*100;
							$por22=ROUND($por22,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por22."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite23."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc23."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc23."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna23."</td>";
							$por23=(($wtotalc23+$wtotalna23)/$cont)*100;
							$por23=ROUND($por23,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por23."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite24."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc24."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc24."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna24."</td>";
							$por24=(($wtotalc24+$wtotalna24)/$cont)*100;
							$por24=ROUND($por24,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por24."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite25."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc25."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc25."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna25."</td>";
							$por25=(($wtotalc25+$wtotalna25)/$cont)*100;
							$por25=ROUND($por25,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por25."%</td></tr>";	
					
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite26."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc26."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc26."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna26."</td>";
							$por26=(($wtotalc26+$wtotalna26)/$cont)*100;
							$por26=ROUND($por26,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por26."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite27."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc27."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc27."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna27."</td>";
							$por27=(($wtotalc27+$wtotalna27)/$cont)*100;
							$por27=ROUND($por27,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por27."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite28."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc28."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc28."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna28."</td>";
							$por28=(($wtotalc28+$wtotalna28)/$cont)*100;
							$por28=ROUND($por28,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por28."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite29."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc29."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc29."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna29."</td>";
							$por29=(($wtotalc29+$wtotalna29)/$cont)*100;
							$por29=ROUND($por29,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por29."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite30."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc30."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc30."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna30."</td>";
							$por30=(($wtotalc30+$wtotalna30)/$cont)*100;
							$por30=ROUND($por30,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por30."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite31."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc31."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc31."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna31."</td>";
							$por31=(($wtotalc31+$wtotalna31)/$cont)*100;
							$por31=ROUND($por31,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por31."%</td></tr>";	
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite32."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc32."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc32."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna32."</td>";
							$por32=(($wtotalc32+$wtotalna32)/$cont)*100;
							$por32=ROUND($por32,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por32."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite33."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc33."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc33."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna33."</td>";
							$por33=(($wtotalc33+$wtotalna33)/$cont)*100;
							$por33=ROUND($por33,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por33."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite34."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc34."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc34."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna34."</td>";
							$por34=(($wtotalc34+$wtotalna34)/$cont)*100;
							$por34=ROUND($por34,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por34."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite35."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc35."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc35."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna35."</td>";
							$por35=(($wtotalc35+$wtotalna35)/$cont)*100;
							$por35=ROUND($por35,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por35."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite36."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc36."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc36."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna36."</td>";
							$por36=(($wtotalc36+$wtotalna36)/$cont)*100;
							$por36=ROUND($por36,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por36."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite37."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc37."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc37."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna37."</td>";
							$por37=(($wtotalc37+$wtotalna37)/$cont)*100;
							$por37=ROUND($por37,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por37."%</td></tr>";	
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite38."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc38."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc38."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna38."</td>";
							$por38=(($wtotalc38+$wtotalna38)/$cont)*100;
							$por38=ROUND($por38,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por38."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite39."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc39."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc39."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna39."</td>";
							$por39=(($wtotalc39+$wtotalna39)/$cont)*100;
							$por39=ROUND($por39,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por39."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite40."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc40."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc40."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna40."</td>";
							$por40=(($wtotalc40+$wtotalna40)/$cont)*100;
							$por40=ROUND($por40,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por40."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite41."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc41."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc41."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna41."</td>";
							$por41=(($wtotalc41+$wtotalna41)/$cont)*100;
							$por41=ROUND($por41,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por41."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite42."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc42."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc42."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna42."</td>";
							$por42=(($wtotalc42+$wtotalna42)/$cont)*100;
							$por42=ROUND($por42,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por42."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite43."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc43."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc43."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna43."</td>";
							$por43=(($wtotalc43+$wtotalna43)/$cont)*100;
							$por43=ROUND($por43,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por43."%</td></tr>";
	
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite44."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc44."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc44."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna44."</td>";
							$por44=(($wtotalc44+$wtotalna44)/$cont)*100;
							$por44=ROUND($por44,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por44."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite45."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc45."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc45."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna45."</td>";
							$por45=(($wtotalc45+$wtotalna45)/$cont)*100;
							$por45=ROUND($por45,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por45."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite46."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc46."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc46."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna46."</td>";
							$por46=(($wtotalc46+$wtotalna46)/$cont)*100;
							$por46=ROUND($por46,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por46."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite47."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc47."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc47."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna47."</td>";
							$por47=(($wtotalc47+$wtotalna47)/$cont)*100;
							$por47=ROUND($por47,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por47."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite48."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc48."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc48."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna48."</td>";
							$por48=(($wtotalc48+$wtotalna48)/$cont)*100;
							$por48=ROUND($por48,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por48."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite49."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc49."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc49."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna49."</td>";
							$por49=(($wtotalc49+$wtotalna49)/$cont)*100;
							$por49=ROUND($por49,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por49."%</td></tr>";

						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite50."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc50."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc50."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna50."</td>";
							$por50=(($wtotalc50+$wtotalna50)/$cont)*100;
							$por50=ROUND($por50,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por50."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite51."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc51."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc51."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna51."</td>";
							$por51=(($wtotalc51+$wtotalna51)/$cont)*100;
							$por51=ROUND($por51,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por51."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite52."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc52."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc52."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna52."</td>";
							$por52=(($wtotalc52+$wtotalna52)/$cont)*100;
							$por52=ROUND($por52,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por52."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite53."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc53."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc53."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna53."</td>";
							$por53=(($wtotalc53+$wtotalna53)/$cont)*100;
							$por53=ROUND($por53,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por53."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite54."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc54."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc54."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna54."</td>";
							$por54=(($wtotalc54+$wtotalna54)/$cont)*100;
							$por54=ROUND($por54,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por54."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite55."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc55."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc55."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna55."</td>";
							$por55=(($wtotalc55+$wtotalna55)/$cont)*100;
							$por55=ROUND($por55,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por55."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite56."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc56."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc56."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna56."</td>";
							$por56=(($wtotalc56+$wtotalna56)/$cont)*100;
							$por56=ROUND($por56,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por56."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite57."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc57."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc57."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna57."</td>";
							$por57=(($wtotalc57+$wtotalna57)/$cont)*100;
							$por57=ROUND($por57,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por57."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite58."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc58."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc58."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna58."</td>";
							$por58=(($wtotalc58+$wtotalna58)/$cont)*100;
							$por58=ROUND($por58,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por58."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite59."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc59."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc59."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna59."</td>";
							$por59=(($wtotalc59+$wtotalna59)/$cont)*100;
							$por59=ROUND($por59,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por59."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite60."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc60."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc60."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna60."</td>";
							$por60=(($wtotalc60+$wtotalna60)/$cont)*100;
							$por60=ROUND($por60,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por60."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite61."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc61."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc61."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna61."</td>";
							$por61=(($wtotalc61+$wtotalna61)/$cont)*100;
							$por61=ROUND($por61,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por61."%</td></tr>";
							
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite62."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc62."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc62."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna62."</td>";
							$por62=(($wtotalc62+$wtotalna62)/$cont)*100;
							$por62=ROUND($por62,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por62."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite63."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc63."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc63."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna63."</td>";
							$por63=(($wtotalc63+$wtotalna63)/$cont)*100;
							$por63=ROUND($por63,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por63."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite64."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc64."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc64."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna64."</td>";
							$por64=(($wtotalc64+$wtotalna64)/$cont)*100;
							$por64=ROUND($por64,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por64."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite65."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc65."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc65."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna65."</td>";
							$por65=(($wtotalc65+$wtotalna65)/$cont)*100;
							$por65=ROUND($por65,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por65."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite66."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc66."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc66."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna66."</td>";
							$por66=(($wtotalc66+$wtotalna66)/$cont)*100;
							$por66=ROUND($por66,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por66."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite67."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc67."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc67."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna67."</td>";
							$por67=(($wtotalc67+$wtotalna67)/$cont)*100;
							$por67=ROUND($por67,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por67."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite68."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc68."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc68."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna68."</td>";
							$por68=(($wtotalc68+$wtotalna68)/$cont)*100;
							$por68=ROUND($por68,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por68."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite69."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc69."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc69."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna69."</td>";
							$por69=(($wtotalc69+$wtotalna69)/$cont)*100;
							$por69=ROUND($por69,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por69."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite70."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc70."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc70."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna70."</td>";
							$por70=(($wtotalc70+$wtotalna70)/$cont)*100;
							$por70=ROUND($por70,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por70."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite71."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc71."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc71."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna71."</td>";
							$por71=(($wtotalc71+$wtotalna71)/$cont)*100;
							$por71=ROUND($por71,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por71."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite72."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc72."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc72."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna72."</td>";
							$por72=(($wtotalc72+$wtotalna72)/$cont)*100;
							$por72=ROUND($por72,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por72."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite73."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc73."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc73."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna73."</td>";
							$por73=(($wtotalc73+$wtotalna73)/$cont)*100;
							$por73=ROUND($por73,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por73."%</td></tr>";

						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite74."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc74."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc74."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna74."</td>";
							$por74=(($wtotalc74+$wtotalna74)/$cont)*100;
							$por74=ROUND($por74,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por74."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite75."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc75."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc75."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna75."</td>";
							$por75=(($wtotalc75+$wtotalna75)/$cont)*100;
							$por75=ROUND($por75,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por75."%</td></tr>";
							
						
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";
						echo"<td align=left colspan='5' bgcolor=".$color2."><strong>TOTAL GENERAL CENTRO DE COSTOS      (Historias Evaluadas:".$cont.")</strong></td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgennc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenna."</td>";
							$wtotpor=(($wtotgenc+$wtotgenna)/($wtotgenc+$wtotgenna+$wtotgennc))*100;
							$wtotpor=ROUND($wtotpor,2);
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotpor."%</td></tr>";
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";   
					
					$wglobalc=$wglobalc+$wtotgenc;
					$wglobalnc=$wglobalnc+$wtotgennc;
					$wglobalna=$wglobalna+$wtotgenna;
					$contgen=$contgen+$cont;
					
					if ($wccostos=='%')
					{
						echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";
							echo"<td align=left border=2 colspan='5' bgcolor=".$color3."><strong>TOTAL GENERAL TODOS LOS CENTRO DE COSTOS   (Historias Evaluadas:".$contgen.")</strong></td>";
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalc."</td>";
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalnc."</td>";
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalna."</td>";
								$wglobalpor=(($wglobalc+$wglobalna)/($wglobalc+$wglobalnc+$wglobalna))*100;
								$wglobalpor=ROUND($wglobalpor,2);
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalpor."%</td></tr>";
						echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";   
					}
				}			
			}
		echo "</table>";
		$bandera=1;
		
		// Botones de "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$aux1\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
  }
}
?>
</body>
</html>