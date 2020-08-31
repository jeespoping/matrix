<html>
<head>
  <title>REPORTE DE EVALUACION HISTORIAS CLINICAS</title>
<script type="text/javascript">

	//Redirecciona a la pagina inicial
	function inicioReporte(wfecini,wfecfin,wcco,wemp_pmla,bandera){
	 	document.location.href='rep_evahcini.php?wfecini='+wfecini+'&wfecfin='+wfecfin+'&wccocod='+wcco+'&wemp_pmla='+wemp_pmla+'&bandera='+bandera;
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
	| DEDSCRIPCI�N: Reporte de evaluacion de las historias clinicas Hospita-    |
	| lizacion con base en fecha inicial y fecha final	     		            |
	| AUTOR: Gabriel Agudelo												    |
	| FECHA DE CREACI�N: Marzo 22 de 2016										|
	| FECHA DE ACTUALIZACI�N: 													|
	----------------------------------------------------------------------------*/

	/*--------------------------------------------------------------------------
	| ACTUALIZACIONES															|
	|---------------------------------------------------------------------------|
	| FECHA:  																	|
	| AUTOR: 																	|
	| DESCRIPCI�N:																|
	----------------------------------------------------------------------------*/

$wactualiz="1.0 | Marzo 22 de 2016";
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

//Validaci�n de usuario
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

//Llamo a la funci�n para formar el encabezado del reporte llev�ndole T�tulo, Fecha e Imagen o logo
encabezado("FORMULARIO EVALUACION HISTORIA CLINICA HOSPITALIZACION",$wactualiz,"clinica");

//Si el usuario no es v�lido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es v�lido
else //Si el usuario es v�lido comenzamos con el reporte
{  //Inicio ELSE reporte
	
  //Conexion base de datos
  


  echo "<form name='forma' action='rep_evahchos.php' method=post onSubmit='return valida_enviar(this);'>";
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
 	
 	//Petici�n de ingreso de parametros
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
 	       ."   FROM jcievahc_000004 "
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
 	
	// Subt�tulo del reporte
 	echo "<tr>";
	echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Formulario Evaluacion Historia Clinica Hospitalizacion&nbsp;  &nbsp; </strong></p></td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";

	//Muestro los par�metros que se ingresaron en la consulta
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
		."	FROM jcievahc_000004 "
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

$crite6="Existe nota m�dica de ingreso, que describe las condiciones del paciente";
$crite7="Existen notas m�dicas cronol�gicas y describen la evoluci�n del pte";
$crite8="Existe una nota de egreso que describe las condiciones de salida";
$crite9="Existe resumen de egreso completa y correctamente diligenciado";
$crite10="Se puede identificar el autor de cada nota";
$crite11="Existe descripci�n operatoria completa y correctamente diligenciada";
$crite12="Existen �rdenes m�dicas cronol�gicas";
$crite13="En cada orden se identifica el autor";
$crite14="Los medicamentos registrados corresponden a las �rdenes m�dicas";
$crite15="Los eventos adversos se registran en Historia Cl�nica";
$crite16="En la documentaci�n m�dica se encuentran abreviaturas";
$crite17="Existe nota de ingreso de Enfermeria";
$crite18="Existen notas de enfermeria cronol�gicas y describen la evoluci�n pte";
$crite19="La nota de egreso de enfermeria describe condiciones de salida e indicaciones";
$crite20="Se registran indicaciones educacionales seg�n la patolog�a";
$crite21="En la documentaci�n de enfermer�a se encuentran abreviaturas";

$color="#eeeeee";
$color1="#cccccc";
$color2="#bbbbbb";
$color3="#aaaaaa";
	// Creaci�n de tabla donde se muestra el resultado de la consulta
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
							  $wtotalc16+$wtotalc17+$wtotalc18+$wtotalc19+$wtotalc20+$wtotalc21;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9+$wtotalnc10+$wtotalnc11+$wtotalnc12+$wtotalnc13+$wtotalnc14+
							   $wtotalnc15+$wtotalnc16+$wtotalnc17+$wtotalnc18+$wtotalnc19+$wtotalnc20+$wtotalnc21;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9+$wtotalna10+$wtotalna11+$wtotalna12+$wtotalna13+$wtotalna14+$wtotalna15+
							   $wtotalna16+$wtotalna17+$wtotalna18+$wtotalna19+$wtotalna20+$wtotalna21;
							   
					
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
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite18."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna18."</td>";
							$por18=(($wtotalc18+$wtotalna18)/$cont)*100;
							$por18=ROUND($por18,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por18."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna19."</td>";
							$por19=(($wtotalc19+$wtotalna19)/$cont)*100;
							$por19=ROUND($por19,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por19."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite20."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna20."</td>";
							$por20=(($wtotalc20+$wtotalna20)/$cont)*100;
							$por20=ROUND($por20,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por20."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna21."</td>";
							$por21=(($wtotalc21+$wtotalna21)/$cont)*100;
							$por21=ROUND($por21,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por21."%</td></tr>";
						
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
			// Se define si se muestra el t�tulo del centro de costos
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
				
					if ($row[P1]=="C-CUMPLE")
						$wtotalc6=$wtotalc6 + 1;
					if ($row[P1]=="NA-NO APLICA")
						$wtotalna6=$wtotalna6 + 1;
					if ($row[P1]=="NC-NO CUMPLE")
						$wtotalnc6=$wtotalnc6 + 1;
					if ($row[P2]=="C-CUMPLE")
						$wtotalc7=$wtotalc7 + 1;
					if ($row[P2]=="NA-NO APLICA")
						$wtotalna7=$wtotalna7 + 1;
					if ($row[P2]=="NC-NO CUMPLE")
						$wtotalnc7=$wtotalnc7 + 1;
					if ($row[P3]=="C-CUMPLE")
						$wtotalc8=$wtotalc8 + 1;
					if ($row[P3]=="NA-NO APLICA")
						$wtotalna8=$wtotalna8 + 1;
					if ($row[P3]=="NC-NO CUMPLE")
						$wtotalnc8=$wtotalnc8 + 1;
					if ($row[P4]=="C-CUMPLE")
						$wtotalc9=$wtotalc9 + 1;
					if ($row[P4]=="NA-NO APLICA")
						$wtotalna9=$wtotalna9 + 1;
					if ($row[P4]=="NC-NO CUMPLE")
						$wtotalnc9=$wtotalnc9 + 1;
					if ($row[P5]=="C-CUMPLE")
						$wtotalc10=$wtotalc10 + 1;
					if ($row[P5]=="NA-NO APLICA")
						$wtotalna10=$wtotalna10 + 1;
					if ($row[P5]=="NC-NO CUMPLE")
						$wtotalnc10=$wtotalnc10 + 1;
					if ($row[P6]=="C-CUMPLE")
						$wtotalc11=$wtotalc11 + 1;
					if ($row[P6]=="NA-NO APLICA")
						$wtotalna11=$wtotalna11 + 1;
					if ($row[P6]=="NC-NO CUMPLE")
						$wtotalnc11=$wtotalnc11 + 1;
					if ($row[P7]=="C-CUMPLE")
						$wtotalc12=$wtotalc12 + 1;
					if ($row[P7]=="NA-NO APLICA")
						$wtotalna12=$wtotalna12 + 1;
					if ($row[P7]=="NC-NO CUMPLE")
						$wtotalnc12=$wtotalnc12 + 1;
					if ($row[P8]=="C-CUMPLE")
						$wtotalc13=$wtotalc13 + 1;
					if ($row[P8]=="NA-NO APLICA")
						$wtotalna13=$wtotalna13 + 1;
					if ($row[P8]=="NC-NO CUMPLE")
						$wtotalnc13=$wtotalnc13 + 1;
					if ($row[P9]=="C-CUMPLE")
						$wtotalc14=$wtotalc14 + 1;
					if ($row[P9]=="NA-NO APLICA")
						$wtotalna14=$wtotalna14 + 1;
					if ($row[P9]=="NC-NO CUMPLE")
						$wtotalnc14=$wtotalnc14 + 1;
					if ($row[P10]=="C-CUMPLE")
						$wtotalc15=$wtotalc15 + 1;
					if ($row[P10]=="NA-NO APLICA")
						$wtotalna15=$wtotalna15 + 1;
					if ($row[P10]=="NC-NO CUMPLE")
						$wtotalnc15=$wtotalnc15 + 1;
					if ($row[P11]=="C-CUMPLE")
						$wtotalc16=$wtotalc16 + 1;
					if ($row[P11]=="NA-NO APLICA")
						$wtotalna16=$wtotalna16 + 1;
					if ($row[P11]=="NC-NO CUMPLE")
						$wtotalnc16=$wtotalnc16 + 1;
					if ($row[P12]=="C-CUMPLE")
						$wtotalc17=$wtotalc17 + 1;
					if ($row[P12]=="NA-NO APLICA")
						$wtotalna17=$wtotalna17 + 1;
					if ($row[P12]=="NC-NO CUMPLE")
						$wtotalnc17=$wtotalnc17 + 1;
					if ($row[P13]=="C-CUMPLE")
						$wtotalc18=$wtotalc18 + 1;
					if ($row[P13]=="NA-NO APLICA")
						$wtotalna18=$wtotalna18 + 1;
					if ($row[P13]=="NC-NO CUMPLE")
						$wtotalnc18=$wtotalnc18 + 1;
					if ($row[P14]=="C-CUMPLE")
						$wtotalc19=$wtotalc19 + 1;
					if ($row[P14]=="NA-NO APLICA")
						$wtotalna19=$wtotalna19 + 1;
					if ($row[P14]=="NC-NO CUMPLE")
						$wtotalnc19=$wtotalnc19 + 1;
					if ($row[P15]=="C-CUMPLE")
						$wtotalc20=$wtotalc20 + 1;
					if ($row[P15]=="NA-NO APLICA")
						$wtotalna20=$wtotalna20 + 1;
					if ($row[P15]=="NC-NO CUMPLE")
						$wtotalnc20=$wtotalnc20 + 1;
					if ($row[P16]=="C-CUMPLE")
						$wtotalc21=$wtotalc21 + 1;
					if ($row[P16]=="NA-NO APLICA")
						$wtotalna21=$wtotalna21 + 1;
					if ($row[P16]=="NC-NO CUMPLE")
						$wtotalnc21=$wtotalnc21 + 1;
												
				$i= $i + 1;
				$cont=$cont + 1;
				
		}
		
		// Si no se tienen resultados se muestra el mensaje correspondiente
		if ($cont==0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=570 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><b>Sin ning�n documento en el rango de fechas seleccionado</b></td><tr>";
			}
		// Muestra el total del responsable o caja y del centro de costos
		// Esto debido a que en el �ltimo ciclo estos no se muestran
		else
			{	
			
				if ($wccocod==$row['Ccosto'])
				{	
					$wtotgenc=$wtotalc6+$wtotalc7+$wtotalc8+$wtotalc9+$wtotalc10+$wtotalc11+$wtotalc12+$wtotalc13+$wtotalc14+$wtotalc15+
							  $wtotalc16+$wtotalc17+$wtotalc18+$wtotalc19+$wtotalc20+$wtotalc21;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9+$wtotalnc10+$wtotalnc11+$wtotalnc12+$wtotalnc13+$wtotalnc14+
							   $wtotalnc15+$wtotalnc16+$wtotalnc17+$wtotalnc18+$wtotalnc19+$wtotalnc20+$wtotalnc21;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9+$wtotalna10+$wtotalna11+$wtotalna12+$wtotalna13+$wtotalna14+$wtotalna15+
							   $wtotalna16+$wtotalna17+$wtotalna18+$wtotalna19+$wtotalna20+$wtotalna21;
							   
					
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
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite18."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc18."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna18."</td>";
							$por18=(($wtotalc18+$wtotalna18)/$cont)*100;
							$por18=ROUND($por18,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por18."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc19."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna19."</td>";
							$por19=(($wtotalc19+$wtotalna19)/$cont)*100;
							$por19=ROUND($por19,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por19."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite20."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc20."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna20."</td>";
							$por20=(($wtotalc20+$wtotalna20)/$cont)*100;
							$por20=ROUND($por20,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por20."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc21."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna21."</td>";
							$por21=(($wtotalc21+$wtotalna21)/$cont)*100;
							$por21=ROUND($por21,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por21."%</td></tr>";
						
												
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