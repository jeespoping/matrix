<html>
<head>
  <title>REPORTE DE EVALUACION HISTORIAS CLINICAS</title>
<script type="text/javascript">

	//Redirecciona a la pagina inicial
	function inicioReporte(wfecini,wfecfin,wcco,wemp_pmla,bandera){
	 	document.location.href='rep_segpac01.php?wfecini='+wfecini+'&wfecfin='+wfecfin+'&wccocod='+wcco+'&wemp_pmla='+wemp_pmla+'&bandera='+bandera;
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
   *     REPORTE DE EVALUACION HISTORIAS CLINICAS SEGURIDAD DEL PACIENTE							    *
   ******************************************************************************/
   
	/*--------------------------------------------------------------------------
	| DEDSCRIPCIÓN: Reporte de evaluacion de las historias clinicas Inicial     |
	| Urgencias/Emergencias con base en fecha inicial y fecha final	     		|
	| AUTOR: Gabriel Agudelo												    |
	| FECHA DE CREACIÓN: Diciembre 26 de 2017										|
	| FECHA DE ACTUALIZACIÓN: 													|
	----------------------------------------------------------------------------*/

	/*--------------------------------------------------------------------------
	| ACTUALIZACIONES															|
	|---------------------------------------------------------------------------|
	| FECHA:  																	|
	| AUTOR: 																	|
	| DESCRIPCIÓN:																|
	----------------------------------------------------------------------------*/

$wactualiz="1.0 | Diciembre 26 de 2017";
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
encabezado("FORMULARIO EVALUACION HISTORIA CLINICA SEGURIDAD DEL PACIENTE",$wactualiz,"clinica");

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
  


  echo "<form name='forma' action='rep_segpac01.php' method=post onSubmit='return valida_enviar(this);'>";
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
  		$q= "   SELECT Segpaccco "
 	       ."   FROM segpac_000001 "
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
	echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Formulario Seguridad del Paciente&nbsp;  &nbsp; </strong></p></td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";

	//Muestro los parámetros que se ingresaron en la consulta
    echo "<tr class='fila2'>";
    echo "<td align=left><strong> &nbsp; Fecha inicial: </strong>&nbsp;".$wfecini." &nbsp; </td>";
    echo "<td align=left><strong> &nbsp; Fecha final: </strong>&nbsp;".$wfecfin." &nbsp; </td>";
    echo "</tr>";
    /*echo "<tr class='fila2'>";
    echo "<td align=left colspan=2><strong> &nbsp; Centro de costos: </strong>&nbsp;".$wccocod." &nbsp; </td>";
    echo "</tr>";*/
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>";

	// Botones de "Retornar" y "Cerrar ventana"
	echo "<p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
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
		."	FROM segpac_000001 "
		."	WHERE Fecha BETWEEN '".$wfecini."' "
		."    AND '".$wfecfin."'"
		."	  AND Segpaccco LIKE '".$wccostos[0]."%' "
		."	  ORDER BY Segpaccco ";
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

$crite6="Todos los medicamentos parenterales administrados estan debidamente rotulado";
$crite7="Se cumplen mínimo los 5 correctos: pacte, medicamento, hora, dosis y vía corr";
$crite8="Se brindó educación al pcte/ flia acerca medicamentos administrados";
$crite9="Se evalúa el estado hemodinamico antes de administrar medicamentos";
$crite10="Se verifica el efecto terapeútico del paciente";
$crite11="Se Realiza conciliación medicamentosa";

$crite12="Se realizó la valoracion delriesgo con escala de NORTON";
$crite13="Se realizó registro en la historia clínica";
$crite14="Educación al pacte/ flia sobre cuidados prevecion lesiones piel";
$crite15="El paciente con riesgo cumple las recomendaciones dadas";
$crite16="Se verifica con pcte / flia sobre vigilancia continua,cambios posicion";
$crite17="Se utilizan dispositivos adicionales para prevención de UPP";

$crite18="Se realizó la identifiación del riesgo con la manilla de identificación";
$crite19="La manilla esta bien diligenciada con los datos correctos y completos";
$crite20="Se le brindó educación al pacte / flia sobre la correcta identificación.";
$crite21="Se realiza verifición cruzada con procedimientos/ adminstrar med.";
$crite22="El tablero de ident de cabecera esta correctamente diligenciado";
$crite23="Los datos de identificación corresponden al paciente";

$crite24="Se realizó valoración el riesgo del Paciente al ingreso";
$crite25="Se identificó al paciente con riesgo con manilla amarilla RIESGO DE CAÍDAS";
$crite26="Se brindó educación al pacte/flia sobre medidas de prevención";
$crite27="El pacte con riesgo de caídas, sigue las recomendaciones dadas";
$crite28="Verificacion con pacte/flia sobre vig, control,educación de factores extrins";
$crite29="El baño del paciente es seguro (antideslizante, barandas, timbre)";

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
					$wccocod=$row['Segpaccco'];
				}
					 	
			// Si cambia el centro de costos muestre el total del anterior
			if ($wccocod!=$row['Segpaccco'])
				{	
					$wtotgenc=$wtotalc6+$wtotalc7+$wtotalc8+$wtotalc9+$wtotalc10+$wtotalc11+$wtotalc12+$wtotalc13+$wtotalc14+$wtotalc15+
							  $wtotalc16+$wtotalc17+$wtotalc18+$wtotalc19+$wtotalc20+$wtotalc21+$wtotalc22+$wtotalc23+$wtotalc24+
							  $wtotalc25+$wtotalc26+$wtotalc27+$wtotalc28+$wtotalc29;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9+$wtotalnc10+$wtotalnc11+$wtotalnc12+$wtotalnc13+$wtotalnc14+
							   $wtotalnc15+$wtotalnc16+$wtotalnc17+$wtotalnc18+$wtotalnc19+$wtotalnc20+$wtotalnc21+$wtotalnc22+$wtotalnc23+
							   $wtotalnc24+$wtotalnc25+$wtotalnc26+$wtotalnc27+$wtotalnc28+$wtotalnc29;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9+$wtotalna10+$wtotalna11+$wtotalna12+$wtotalna13+$wtotalna14+$wtotalna15+
							   $wtotalna16+$wtotalna17+$wtotalna18+$wtotalna19+$wtotalna20+$wtotalna21+$wtotalna22+$wtotalna23+$wtotalna24+
							   $wtotalna25+$wtotalna26+$wtotalna27+$wtotalna28+$wtotalna29;
							   
					
					echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>"; 
					echo "<tr class='encabezadoTabla' height='31'>";
						echo"<td align=center border=1 colspan='5'>CRITERIOS</td>";
						echo "<td align=center border=1 >CUMPLE</td>";
						echo "<td align=center border=1>NO CUMPLE</td>";
						echo "<td align=center border=1 >NO APLICA</td>";
						echo "<td align=center border=1>% DE CUMPLIMIENTO</td></tr>";
					echo "<tr><td align='left' colspan='5'>&nbsp;</td></tr>";
					
					
						echo"<tr><td align=left border=1 colspan='9' bgcolor=".$color2.">ADMINISTRACION CORRECTA DE MEDICAMENTOS</td></tr>";
												
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
						
						echo"<tr><td align=left border=1 colspan='9' bgcolor=".$color2.">PREVENCION DE UPP</td></tr>";
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
			if (($bandera1==0) or ($wccocod!=$row['Segpaccco']))
				{   
					$waux=$wccocod;
					$wccocod=$row['Segpaccco'];
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
				
					if ($row[Acm1]=="01-SI")
						$wtotalc6=$wtotalc6 + 1;
					if ($row[Acm1]=="03-NO APLICA")
						$wtotalna6=$wtotalna6 + 1;
					if ($row[Acm1]=="02-NO")
						$wtotalnc6=$wtotalnc6 + 1;
					if ($row[Acm2]=="01-SI")
						$wtotalc7=$wtotalc7 + 1;
					if ($row[Acm2]=="03-NO APLICA")
						$wtotalna7=$wtotalna7 + 1;
					if ($row[Acm2]=="02-NO")
						$wtotalnc7=$wtotalnc7 + 1;
					if ($row[Acm3]=="01-SI")
						$wtotalc8=$wtotalc8 + 1;
					if ($row[Acm3]=="03-NO APLICA")
						$wtotalna8=$wtotalna8 + 1;
					if ($row[Acm3]=="02-NO")
						$wtotalnc8=$wtotalnc8 + 1;
					if ($row[Acm4]=="01-SI")
						$wtotalc9=$wtotalc9 + 1;
					if ($row[Acm4]=="03-NO APLICA")
						$wtotalna9=$wtotalna9 + 1;
					if ($row[Acm4]=="02-NO")
						$wtotalnc9=$wtotalnc9 + 1;
					if ($row[Acm5]=="01-SI")
						$wtotalc10=$wtotalc10 + 1;
					if ($row[Acm5]=="03-NO APLICA")
						$wtotalna10=$wtotalna10 + 1;
					if ($row[Acm5]=="02-NO")
						$wtotalnc10=$wtotalnc10 + 1;
					if ($row[Acm6]=="01-SI")
						$wtotalc11=$wtotalc11 + 1;
					if ($row[Acm6]=="03-NO APLICA")
						$wtotalna11=$wtotalna11 + 1;
					if ($row[Acm6]=="02-NO")
						$wtotalnc11=$wtotalnc11 + 1;
					if ($row[Pupp1]=="01-SI")
						$wtotalc12=$wtotalc12 + 1;
					if ($row[Pupp1]=="03-NO APLICA")
						$wtotalna12=$wtotalna12 + 1;
					if ($row[Pupp1]=="02-NO")
						$wtotalnc12=$wtotalnc12 + 1;
					if ($row[Pupp2]=="01-SI")
						$wtotalc13=$wtotalc13 + 1;
					if ($row[Pupp2]=="03-NO APLICA")
						$wtotalna13=$wtotalna13 + 1;
					if ($row[Pupp2]=="02-NO")
						$wtotalnc13=$wtotalnc13 + 1;
					if ($row[Pupp3]=="01-SI")
						$wtotalc14=$wtotalc14 + 1;
					if ($row[Pupp3]=="03-NO APLICA")
						$wtotalna14=$wtotalna14 + 1;
					if ($row[Pupp3]=="02-NO")
						$wtotalnc14=$wtotalnc14 + 1;
					if ($row[Pupp4]=="01-SI")
						$wtotalc15=$wtotalc15 + 1;
					if ($row[Pupp4]=="03-NO APLICA")
						$wtotalna15=$wtotalna15 + 1;
					if ($row[Pupp4]=="02-NO")
						$wtotalnc15=$wtotalnc15 + 1;
					if ($row[Pupp5]=="01-SI")
						$wtotalc16=$wtotalc16 + 1;
					if ($row[Pupp5]=="03-NO APLICA")
						$wtotalna16=$wtotalna16 + 1;
					if ($row[Pupp5]=="02-NO")
						$wtotalnc16=$wtotalnc16 + 1;
					if ($row[Pupp6]=="01-SI")
						$wtotalc17=$wtotalc17 + 1;
					if ($row[Pupp6]=="03-NO APLICA")
						$wtotalna17=$wtotalna17 + 1;
					if ($row[Pupp6]=="02-NO")
						$wtotalnc17=$wtotalnc17 + 1;
					if ($row[Icp1]=="01-SI")
						$wtotalc18=$wtotalc18 + 1;
					if ($row[Icp1]=="03-NO APLICA")
						$wtotalna18=$wtotalna18 + 1;
					if ($row[Icp1]=="02-NO")
						$wtotalnc18=$wtotalnc18 + 1;
					if ($row[Icp2]=="01-SI")
						$wtotalc19=$wtotalc19 + 1;
					if ($row[Icp2]=="03-NO APLICA")
						$wtotalna19=$wtotalna19 + 1;
					if ($row[Icp2]=="02-NO")
						$wtotalnc19=$wtotalnc19 + 1;
					if ($row[Icp3]=="01-SI")
						$wtotalc20=$wtotalc20 + 1;
					if ($row[Icp3]=="03-NO APLICA")
						$wtotalna20=$wtotalna20 + 1;
					if ($row[Icp3]=="02-NO")
						$wtotalnc20=$wtotalnc20 + 1;
					if ($row[Icp4]=="01-SI")
						$wtotalc21=$wtotalc21 + 1;
					if ($row[Icp4]=="03-NO APLICA")
						$wtotalna21=$wtotalna21 + 1;
					if ($row[Icp4]=="02-NO")
						$wtotalnc21=$wtotalnc21 + 1;
					if ($row[Icp5]=="01-SI")
						$wtotalc22=$wtotalc22 + 1;
					if ($row[Icp5]=="03-NO APLICA")
						$wtotalna22=$wtotalna22 + 1;
					if ($row[Icp5]=="02-NO")
						$wtotalnc22=$wtotalnc22 + 1;
					if ($row[Icp6]=="01-SI")
						$wtotalc23=$wtotalc23 + 1;
					if ($row[Icp6]=="03-NO APLICA")
						$wtotalna23=$wtotalna23 + 1;
					if ($row[Icp6]=="02-NO")
						$wtotalnc23=$wtotalnc23 + 1;
					if ($row[Rc1]=="01-SI")
						$wtotalc24=$wtotalc24 + 1;
					if ($row[Rc1]=="03-NO APLICA")
						$wtotalna24=$wtotalna24 + 1;
					if ($row[Rc1]=="02-NO")
						$wtotalnc24=$wtotalnc24 + 1;
					if ($row[Rc2]=="01-SI")
						$wtotalc25=$wtotalc25 + 1;
					if ($row[Rc2]=="03-NO APLICA")
						$wtotalna25=$wtotalna25 + 1;
					if ($row[Rc2]=="02-NO")
						$wtotalnc25=$wtotalnc25 + 1;
					if ($row[Rc3]=="01-SI")
						$wtotalc26=$wtotalc26 + 1;
					if ($row[Rc3]=="03-NO APLICA")
						$wtotalna26=$wtotalna26 + 1;
					if ($row[Rc3]=="02-NO")
						$wtotalnc26=$wtotalnc26 + 1;
					if ($row[Rc4]=="01-SI")
						$wtotalc27=$wtotalc27 + 1;
					if ($row[Rc4]=="03-NO APLICA")
						$wtotalna27=$wtotalna27 + 1;
					if ($row[Rc4]=="02-NO")
						$wtotalnc27=$wtotalnc27 + 1;
					if ($row[Rc5]=="01-SI")
						$wtotalc28=$wtotalc28 + 1;
					if ($row[Rc5]=="03-NO APLICA")
						$wtotalna28=$wtotalna28 + 1;
					if ($row[Rc5]=="02-NO")
						$wtotalnc28=$wtotalnc28 + 1;
					if ($row[Rc6]=="01-SI")
						$wtotalc29=$wtotalc29 + 1;
					if ($row[Rc6]=="03-NO APLICA")
						$wtotalna29=$wtotalna29 + 1;
					if ($row[Rc6]=="02-NO")
						$wtotalnc29=$wtotalnc29 + 1;
										
				$i= $i + 1;
				$cont=$cont + 1;
				
		}
		
		// Si no se tienen resultados se muestra el mensaje correspondiente
		if ($cont==0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=570 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><b>Sin ningún dato en el rango de fechas seleccionado</b></td><tr>";
			}
		// Muestra el total del responsable o caja y del centro de costos
		// Esto debido a que en el último ciclo estos no se muestran
		else
			{	
			
				if ($wccocod==$row['Segpaccco'])
				{	
					$wtotgenc=$wtotalc6+$wtotalc7+$wtotalc8+$wtotalc9+$wtotalc10+$wtotalc11+$wtotalc12+$wtotalc13+$wtotalc14+$wtotalc15+
							  $wtotalc16+$wtotalc17+$wtotalc18+$wtotalc19+$wtotalc20+$wtotalc21+$wtotalc22+$wtotalc23+$wtotalc24+
							  $wtotalc25+$wtotalc26+$wtotalc27+$wtotalc28+$wtotalc29;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9+$wtotalnc10+$wtotalnc11+$wtotalnc12+$wtotalnc13+$wtotalnc14+
							   $wtotalnc15+$wtotalnc16+$wtotalnc17+$wtotalnc18+$wtotalnc19+$wtotalnc20+$wtotalnc21+$wtotalnc22+$wtotalnc23+
							   $wtotalnc24+$wtotalnc25+$wtotalnc26+$wtotalnc27+$wtotalnc28+$wtotalnc29;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9+$wtotalna10+$wtotalna11+$wtotalna12+$wtotalna13+$wtotalna14+$wtotalna15+
							   $wtotalna16+$wtotalna17+$wtotalna18+$wtotalna19+$wtotalna20+$wtotalna21+$wtotalna22+$wtotalna23+$wtotalna24+
							   $wtotalna25+$wtotalna26+$wtotalna27+$wtotalna28+$wtotalna29;
							   							   
					
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