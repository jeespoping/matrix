<html>
<head>
<title>AFINIDAD</title>
</head>
<body >
<?php
include_once("conex.php");

/**
 * PAGINA DE IDENTIFICACION DE AFINIDAD
 * 
 * Muestra en pantalla la información del cliente que trae el include busqueda_info.php es más que todo una vista de la información del paciente, utilizada por todo la clínica
 * para identificar los pacientes si pertence a afinidad. Permite mediante el hipervinculo recargar tomar automaticamente los datos mas recientes en unix para actualizar la base de datos
 * en matrix del paciente, o mediante el boton actualizar modificar datos propios del programa de afinidad (llamando al programa actualizar.php).
 * 
 * @name matrix\magenta\procesos\persona.php
 * @author Ing. Ana María Betancur Vargas
 * @created 2005-12-07
 * @version 2007-01-19
 * 
 * @modified 2006-01-11  refinamiento del script, Carolina Castaño
 * @modified 2006-05-11  cambios en la interfaz de usuario, Carolina Castaño
 * @modified 2007-01-19  documentacion, Carolina Castaño
 * 
 * 
 * @wvar $acompa , acompañante del paciente en la ultima visita
 * @wvar $afiliado, entidad responsable del ultimo ingreso 
 * @wvar $ape1, apellido del paciente
 * @wvar $ape2, segundo apellido del paciente
 * @wvar $ccomAdo, indica si el cuadro de comunidad adolecentes esta seleccionado en el checkbox
 * @wvar $ccomAdu, indica si el cuadro de comunidad adultos esta seleccionado en el checkbox
 * @wvar $ccomBeb, indica si el cuadro de comunidad bebes esta seleccionado en el checkbox
 * @wvar $ccomCar, indica si el cuadro de enfermedades cardiovasculares esta seleccionado en el checkbox
 * @wvar $ccomFam, indica si el cuadro de comunidad familias esta seleccionado en el checkbox
 * @wvar $ccomSmu, indica si el cuadro de enfermedades musculares esta seleccionado en el checkbox
 * @wvar $ccomSnc, indica si el cuadro de enfermedades del sistema nervioso esta seleccionado en el checkbox
 * @wvar $ccomSrn, indica si el cuadro de enfermedades del sistema renal esta seleccionado en el checkbox
 * @wvar $ccomSrs, indica si el cuadro de enfermedades del sistema respiratorio esta seleccionado en el checkbox
 * @wvar $cdepEqu, indica si el cuadro de deporte equitacion esta seleccionado en el checkbox
 * @wvar $cdepFut, indica si el cuadro de deporte futbol esta seleccionado en el checkbox
 * @wvar $cdepGol, indica si el cuadro de deporte golf esta seleccionado en el checkbox
 * @wvar $cdepGym, indica si el cuadro de deporte gym esta seleccionado en el checkbox
 * @wvar $cdepTen, indica si el cuadro de deporte tennis esta seleccionado en el checkbox
 * @wvar $cgusAdr, indica si el cuadro de gusto artes dramaticas esta seleccionado en el checkbox
 * @wvar $cgusApl, indica si el cuadro de gusto artes aplicadas esta seleccionado en el checkbox
 * @wvar $cgusCin, indica si el cuadro de gusto cine esta seleccionado en el checkbox
 * @wvar $cgusLec, indica si el cuadro de gusto lectura esta seleccionado en el checkbox
 * @wvar $cgusMus, indica si el cuadro de gusto musica esta seleccionado en el checkbox
 * @wvar $color, color para deplegar mensajes dependiendo del tipo de usuario que es el paciente
 * @wvar $comAdo, checkbox para comunidad adolecentes
 * @wvar $comAdu, checkbox para comunidad adultos
 * @wvar $comBeb, checkbox para comunidad beges
 * @wvar $comCan, checkbox para comunidad enfermedad cancer
 * @wvar $comCar, checkbox para comunidad enfermedades cardiacas
 * @wvar $comFam, checkbox para comunidad familias
 * @wvar $comOtr, checkbox para otras enfermedades
 * @wvar $comSmu, checkbox para enfermedades musculares
 * @wvar $comSnc, checkbox para enfermedades del sistema nervioso
 * @wvar $comSrn, checkbox para enfermedades del sistema renal
 * @wvar $comSrs, checkbox para enfermedades respiratorias
 * @wvar $depEqu, checkbox para deporte equitacion
 * @wvar $depFut, checkbox para deporte futbol
 * @wvar $depGol, checkbox para deporte golf
 * @wvar $depGym, checkbox para deporte gym
 * @wvar $depOtr, input para otro deporte 
 * @wvar $dept, departamento
 * @wvar $depTen, checkbox para deporte tennis
 * @wvar $dir, direccion del paciente
 * @wvar $doc, documento de identidad del paciente
 * @wvar $egr, indica de donde sale la informacion mas actualizada, de que base de datos
 * @evar $egr1, guarda a matrix por defecto
 * @wvar $email1, email 1 del paciente
 * @wvar $email2, email 2 del paicente
 * @wvar $estado, indica si hay alguna variable mal ingresada que necesite ser actualizada
 * @wvar $estrato, estrato del paciente
 * @wvar $estCivil, estado civil del paciente
 * @wvar $exp, para llenar los explodes
 * @wvar $fam
 * @wvar $fecAc guarda la fecha de actualizacion en matrix
 * @wvar $fecAct la fecha con datos mas recientes en las diferentes fuentes (matrix, aymov, impac, inpaci)
 * @wvar $fecEgr, fecha de egreso
 * @wvar $fecIng, fecha de ingreso
 * @wvar $fecNac, fecha de nacimiento			
 * @wvar $frecVis, frecuencia de visitas
 * @wvar $gif, nombre de la imagen para mostrar segun el tipo de usuario
 * @wvar $gusAdr, checkbox para gusto artes drmaticas
 * @wvar $gusApl, checkbox para gusto artes aplicadas
 * @wvar $gusCin, checkbox para gusto cine
 * @wvar $gusLec, checkbox para gusto lectura
 * @wvar $gusMus, checkbox para gusto musica
 * @wvar $llamdire, preferencia de contacto correo directo
 * @wvar $llamemai, preferencia de contacto mail
 * @wvar $llammovi, preferencia de contacto movil
 * @wvar $llamtel1, preferencia de contacto telefono 1
 * @wvar $llamtel2, preferencia de contacto telefono 2
 * @wvar $lugNac, lugar de nacimiento
 * @wvar $movil, movil del paciente
 * @wvar $municipio, municipio donde vive el pacinete
 * @wvar $nHijos, numero de hijos
 * @wvar $niv, si es 1 es AAA, si es 2 es BBB
 * @wvar $nombres, nombre del paciente
 * @wvar $pais, pais del paciente
 * @wvar $planC, eps del paciente
 * @wvar $prof, profesion del paciente
 * @wvar $reqAct, requiere actualizacion o no por la fecha de la ultima hace mas de un año
 * @wvar $requer, requerimientos
 * @wvar $serEgr, servicio de egreso 
 * @wvar $serIng, servicio de ingreso 
 * @wvar $servicio, servicio mas utilizado por el paciene
 * @wvar $sexo, sexo del paciente
 * @wvar $tel1, telefono 1
 * @wvar $tel2, telefono 2
 * @wvar $tipDoc, tipo de documento del paciente
 * @wvar $tipDocImpr, tipo de documento modo corto
 * @wvar $tipUsu, tipo de usuario
 * @wvar $vector, lista de variables que estan mal llenadas y deben ser actualizadas
 * @wvar $zona, zona donde vive el paciente
 * 
**/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
						-Se cambia encabezado con ultimo formato.
*************************************************************************************************************************/
$wversion='2007-11-08';
$wactualiz='2016-05-06';
//=================================================================================================================================

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	include_once("root/comun.php"); 	
	include_once("magenta/busqueda_info.php");
	

	

	$q= " SELECT Cconom "
	."       FROM costosyp_000005 "
	."    WHERE Ccocod = '".$cco ."' ";

	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);
	$nom=$row[0];

	/////////////////////////////////////////////////encabezado general///////////////////////////////////
    $titulo = "SISTEMA AAA";
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz,"clinica");  

	//muestra la version del programa
	echo "<table align='center'>" ;
/*	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>";
	echo "</tr>" ;*/
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='4'>CENTRO DE COTOS: ".$cco."-".$nom."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br>" ;

	/**
	 * Busca toda la informacion del paciente para identificarlo y desplegarlo en este formulario
	 *
	 */

	if (substr ($tipUsu,0,3)!='VIP')  //si el paciente no es VIP se realiza la presentacion de afinidad
	{

		echo "<table border='0' width='700' align='center'><tr><td align='center'>";
		echo "<table align='center' border=1 bordercolor=#000080 width=340 style='border:solid;'>";
		echo "<tr><td align='center' colspan='0'>";
		//echo "<img SRC='/MATRIX/images/medical/root/clinica.JPG'>";// width=150 high=43></td>";
		echo "<img SRC='/MATRIX/images/medical/root/clinica.JPG' width=160 high=160></td>";
		
		echo "<td align=center colspan='1' ><font size=3 color='#000080' face='arial'>";
		echo "<B>PROGRAMA AFINIDAD<br>";
		echo "</table></td>";

		echo "<td align='center' colspan='2'>";
		echo "<img SRC='/MATRIX/images/medical/Magenta/$gif' >";

		//Si la actualización es más vija de un año
		if( $reqAct and  $niv!=99)
		//echo 'mostrar mensaje';
		echo "<br><font size=3 color='$color' face='arial'>DEBE ACTUALIZAR LA INFORMACIÓN</FONT>";
		if ($estado<4) //quiere decir que hay datos incompletos, entonces se debe mostrar cuales son esos datos
		{
			for($i=1;$i<=$estado;$i++)
			{
				echo "<br><font size=3 color='$color' face='arial'>&nbsp*</FONT>";
				echo "<font size=3 color='$color' face='arial'>$vector[$i]</FONT>";
				echo "<font size=3 color='$color' face='arial'>*</FONT>";
			}
		}

		echo "</td></tr>";
		echo "<tr><td>&nbsp;</td></tr>";

		// Primera tabla de datos
		echo "<tr><td align='center' rowspan='4'><fieldset style='border:solid;border-color:$color; width=330' ; color=#000080><table width='330' border=1>";
		echo "<tr>";
		echo "<td align='center' colspan='2'><font size=3 color='#000080' face='arial'><b>";
		echo ucwords(strtolower($nombres))." ".ucwords(strtolower($ape1))." ".ucwords(strtolower($ape2))."</b><br>$tipDocImpr.$doc</td>";
		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Frecuencia de Visita: </b>$frecVis</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>ÚLTIMA VISITA </b></td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>Estancia: </b>de $fecIng hasta $fecEgr</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>Unidad Ingreso: </b>".ucwords(strtolower($serIng))."</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>Unidad Egreso: </b>".ucwords(strtolower($serEgr))."</td></tr>";
		$afiliado=ucwords(strtolower($afiliado));
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>Entidad Resp.: </b>".$afiliado."</td></tr>";
		$exp=explode("-",$planC);
		$exp[1]=ucwords(strtolower($exp[1]));
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>EPS: </b>".$exp[0]."-".$exp[1]."</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>Acompañante: </b>".$acompa."</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial'><b>Serviciomas usado: </b>".$servicio."</td></tr>";


		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>DATOS PERSONALES</b></td></tr>";
		/*$exp=explode("-",$lugNac);
		$exp[1]=ucwords(strtolower($exp[1]));*/
		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Fecha y lugar de Nacimiento:<br></b> $fecNac en $lugNac</td></tr>";
		//$exp=explode("-",$sexo);
		echo "<tr><td colspan='1'><font size=2  face='arial' ><b>Sexo:</b> ".ucwords(strtolower($sexo))."</td>";
		echo "<td colspan='1'><font size=2  face='arial' ><b>N. Hijos:</b> $nHijos</td></tr>";
		//$exp=explode("-",$estCivil);
		echo "<tr><td colspan='1'><font size=2  face='arial' ><b>Estado Civil:</b> ".ucwords(strtolower($estCivil))."</td>";
		echo "<td colspan='1'><font size=2  face='arial' ><b>Estrato:</b> $estrato</td></tr>";
		$exp=explode("-",$prof);
		$exp[1]=ucwords(strtolower($exp[1]));
		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Profesión:</b> $exp[0]-$exp[1]</td></tr>";

		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>INFORMACIÓN DE CONTACTO</b></td></tr>";
		echo "<tr><td colspan='1'><font size=2  face='arial' ><b>Tel casa:</b> $tel1</td>";
		echo "<td colspan='2'><font size=2  face='arial' ><b>Tel oficina:</b> $tel2</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Movil:</b> $movil</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>email 1:</b> $email1</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>email 2:</b> $email2</td></tr>";
		echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Dirección:</b> $dir</td>";
		echo "<tr><td colspan='1'><font size=2  face='arial' ><b>Zona:</b> ".$zona."</td>";
		echo "<td colspan='1'><font size=2  face='arial' ><b>Municipio:</b> ".$municipio."</td></tr>";
		echo "<tr><td colspan='1'><font size=2  face='arial' ><b>Departamento:</b> ".$dept."</td>";
		echo "<td colspan='1'><font size=2  face='arial' ><b>Pais:</b> ".$pais."</td></tr>";

		//botón volver

		echo "</table></fieldset></td>";
		echo "<form method='post' action='Magenta.php' target='_parent'>";
		echo "<td align='center'><input type='submit' name='volver' value='VOLVER'></form></TD>";

		//botón actualizar
		echo "<form method='post' action='actualizar.php' target='_parent'>";
		echo "<input type='hidden' name='tipUsu' value='$tipUsu'>";
		echo "<input type='hidden' name='doc' value='$doc'>";
		echo "<input type='hidden' name='tipDoc' value='$tipDoc'>";
		echo "<input type='hidden' name='fecAct' value='$fecAc'>";
		echo "<input type='hidden' name='gif' value='$gif'>";
		echo "<input type='hidden' name='nombres' value='$nombres'>";
		echo "<input type='hidden' name='ape1' value='$ape1'>";
		echo "<input type='hidden' name='ape2' value='$ape2'>";
		echo "<input type='hidden' name='fecNac' value='$fecNac'>";
		echo "<input type='hidden' name='lugNac' value='$lugNac'>";
		echo "<input type='hidden' name='sexo' value='$sexo'>";
		echo "<input type='hidden' name='estCivil' value='$estCivil'>";
		echo "<input type='hidden' name='nHijos' value='$nHijos'>";
		echo "<input type='hidden' name='prof' value='$prof'>";
		echo "<input type='hidden' name='tel1' value='$tel1'>";
		echo "<input type='hidden' name='tel2' value='$tel2'>";
		echo "<input type='hidden' name='movil' value='$movil'>";
		echo "<input type='hidden' name='email1' value='$email1'>";
		echo "<input type='hidden' name='email2' value='$email2'>";
		echo "<input type='hidden' name='dir' value='$dir'>";
		echo "<input type='hidden' name='estrato' value='$estrato'>";
		echo "<input type='hidden' name='zona' value='$zona'>";
		echo "<input type='hidden' name='municipio' value='$municipio'>";
		echo "<input type='hidden' name='dept' value='$dept'>";
		echo "<input type='hidden' name='pais' value='$pais'>";
		echo "<input type='hidden' name='acompa' value='$acompa'>";
		echo "<input type='hidden' name='fam' value='$fam'>";
		echo "<input type='hidden' name='afiliado' value='$afiliado'>";
		echo "<input type='hidden' name='planC' value='$planC'>";
		echo "<input type='hidden' name='llamtel1' value='$llamtel1'>";
		echo "<input type='hidden' name='llamtel2' value='$llamtel2'>";
		echo "<input type='hidden' name='llamemai' value='$llamemai'>";
		echo "<input type='hidden' name='llammovi' value='$llammovi'>";
		echo "<input type='hidden' name='llamdire' value='$llamdire'>";
		echo "<input type='hidden' name='servicio' value='$servicio'>";
		echo "<input type='hidden' name='comBeb' value='$comBeb'>";
		echo "<input type='hidden' name='comFam' value='$comFam'>";
		echo "<input type='hidden' name='comAdo' value='$comAdo'>";
		echo "<input type='hidden' name='comAdu' value='$comAdu'>";
		echo "<input type='hidden' name='comSnc' value='$comSnc'>";
		echo "<input type='hidden' name='comSrs' value='$comSrs'>";
		echo "<input type='hidden' name='comSrn' value='$comSrn'>";
		echo "<input type='hidden' name='comCan' value='$comCan'>";
		echo "<input type='hidden' name='comCar' value='$comCar'>";
		echo "<input type='hidden' name='comSmu' value='$comSmu'>";
		echo "<input type='hidden' name='comOtr' value='$comOtr'>";
		echo "<input type='hidden' name='gusLec' value='$gusLec'>";
		echo "<input type='hidden' name='gusApl' value='$gusApl'>";
		echo "<input type='hidden' name='gusAdr' value='$gusAdr'>";
		echo "<input type='hidden' name='gusCin' value='$gusCin'>";
		echo "<input type='hidden' name='gusMus' value='$gusMus'>";
		echo "<input type='hidden' name='depFut' value='$depFut'>";
		echo "<input type='hidden' name='depGol' value='$depGol'>";
		echo "<input type='hidden' name='depTen' value='$depTen'>";
		echo "<input type='hidden' name='depEqu' value='$depEqu'>";
		echo "<input type='hidden' name='depGym' value='$depGym'>";
		echo "<input type='hidden' name='depOtr' value='$depOtr'>";
		echo "<input type='hidden' name='requerAnt' value='$requer'>";
		echo "<input type='hidden' name='egr' value='$egr'>";
		echo "<input type='hidden' name='egr1' value='$egr1'>";
		echo "<input type='hidden' name='color' value='$color'>";
		echo "<input type='hidden' name='cco' value='$cco'>";
		echo "<td align='center'><input type='submit' name='actualizar' value='ACTUALIZAR'></form></TD>";

		echo "<td ><font size=2><A href='persona.php?doc=$doc&tipDoc=$tipDoc&act=1&cco=$cco'>RECARGAR</A></font></td>&nbsp;&nbsp;&nbsp;";
		echo "<td ><font size=2>&nbsp;&nbsp;&nbsp;</font></td>&nbsp;&nbsp;&nbsp;";
		//echo "<td ><font size=2><A href='index_magenta.htm' TARGET='_new'>AYUDA</A></font></td>";
		echo "<tr><td align='center' colspan='3'><fieldset  style='border:solid;border-color:$color; width=330'><table border=1  width=330>";


		//tabla requerimiento
		echo "<fieldset style='border:solid;border-color:$color; width=330' ; color=#000080><table width='330' border=1>";
		echo "<tr><td><font size=2  face='arial' ><b>REQUERIMIENTOS (realizados durante las visitas)</b><br>";
		echo $requer;
		echo " </td><tr>";
		echo "</table></fieldset>";

		if (substr ($tipUsu,-1)!='2') //esta parte se muestra para AAA, pero no para BBB, es decir no para los de nivel 2
		{
			//Segunda tabla de datos

			echo "<tr><td align='center' colspan='3'><fieldset  style='border:solid;border-color:$color; width=330'><table border=1 width=330>";
			echo "<tr><td colspan='2'><font size=2  face='arial'><b>Fecha Actualización: $fecAc</b></td></tr>";
			echo "<tr><td colspan='2'><font size=2  face='arial'><b>COMUNIDADES</b></td></tr>";
			echo "<tr><td ><font size=2  face='arial'><b>Bebes</b> <input type='checkbox' name='comBeb' $ccomBeb></td>";
			echo "<td ><font size=2  face='arial'><b>Familias</b> <input type='checkbox' name='comFam' $ccomFam></td>";
			echo "<tr><td ><font size=2  face='arial'><b>Adolecentes</b> <input type='checkbox' name='comAdo' $ccomAdo></td>";
			echo "<td ><font size=2  face='arial'><b>Adultos</b> <input type='checkbox' name='comAdu' $ccomAdu></td></tr>";
			echo "<tr><td colspan='2'><font size=2  face='arial'><b>PRINCIPALES CAUSAS DE ENFERMEDAD</b></td></tr>";
			echo "<tr><td ><font size=2  face='arial' ><b>Sistema Nervioso</b> <input type='checkbox' name='comSnc' $ccomSnc></td>";
			echo "<td ><font size=2  face='arial' ><b>Sistema Respiratorio</b> <input type='checkbox' name='comSrs' $ccomSrs></td></tr>";
			echo "<tr><td ><font size=2  face='arial' ><b>Sistema Renal</b> <input type='checkbox' name='comSrn' $ccomSrn></td>";
			echo "<td ><font size=2  face='arial' ><b>Sistema Cardiovascular</b> <input type='checkbox' name='comCar' $ccomCar></td></tr>";
			echo "<tr><td colspan='4'><font size='2' face='arial'><b> Sistema Musculoequelético</b> <input type='checkbox' name='comSmu' $ccomSmu></td></tr>";
			echo "<tr><td colspan='4'><font size='2' face='arial'><b>Otras:</b> $comOtr</td></tr>";

			echo "</table></fieldset>";

			//tercera tabla de  datos
			echo "<tr><td align='center' colspan='3'><fieldset  style='border:solid;border-color:$color; width=330'><table border=1 width=330>";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>Fecha Actualización: $fecAc</b></td></tr>";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>GUSTOS</b></td></tr>";

			echo "<tr><td ><font size=2  face='arial' ><b>Lectura</b> <input type='checkbox' name='gusLec' $cgusLec></td>";
			echo "<td ><font size=2  face='arial' ><b>Música</b> <input type='checkbox' name='gusMus' $cgusMus></td>";
			echo "<td ><font size=2  face='arial' ><b>Artes plasticas</b> <input type='checkbox' name='gusApl' $cgusApl></td></tr>";

			echo "<tr><td ><font size=2  face='arial' ><b>Cine</b> <input type='checkbox' name='gusCin' $cgusCin></td>";
			echo "<td colspan='2'><font size=2  face='arial' ><b>Artes Dramáticas</b> <input type='checkbox' name='guaAdr' $cgusAdr></td></tr>";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>DEPORTE</b></td></tr>";
			echo "<tr><td ><font size=2  face='arial' ><b>Fútbol</b> <input type='checkbox' name='depFut' $cdepFut></td>";
			echo "<td ><font size=2  face='arial' ><b>Tennis</b> <input type='checkbox' name='depTen' $cdepTen></td>";
			echo "<td ><font size=2  face='arial' ><b>Golf</b> <input type='checkbox' name='depGol' $cdepGol></td></tr>";
			echo "<tr><td ><font size=2  face='arial' ><b>GYM</b> <input type='checkbox' name='depGYM' $cdepGym></td>";
			echo "<td colspan='2'><font size=2  face='arial' ><b>Equitación</b> <input type='checkbox' name='depEqu' $cdepEqu></td></tr>";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>Otros Gustos/Deportes:</b> $depOtr</td></tr>";
			echo "</table></fieldset>";



			//Cuarta tabla preferencia de contacto
			echo "<tr><td align='center' colspan='4'><fieldset  style='border:solid;border-color:$color; width=700'><table border=1 width='700' >";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>Fecha Actualización: $fecAct</b></td></tr>";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>Preferencias de contacto</b></td></tr>";
			echo "<tr><td ><font size=2  face='arial' ><b>Tel1: </b>$llamtel1</td>";
			echo "<td ><font size=2  face='arial' ><b>Tel2: </b>$llamtel2</td>";
			echo "<td ><font size=2  face='arial' ><b>Móvil: </b>$llammovi</td></tr>";

			echo "<tr><td ><font size=2  face='arial' ><b>Email1: </b>$llamemai</td>";
			echo "<td colspan='2'><font size=2  face='arial' ><b>Correo directo: </b>$llamdire</td></tr>";

			echo "</table></fieldset>";

			echo "<td></td>";
			echo "</table>";

		}
		else // a los BBB o nivel dos se les muestran las preferencias de contqacto solamente
		{
			//Cuarta tabla preferencia de contacto
			echo "<tr><td align='center' colspan='3'><fieldset  style='border:solid;border-color:$color; width=330'><table border=1 width=330 >";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>Fecha Actualización: $fecAct</b></td></tr>";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>Preferencias de contacto</b></td></tr>";
			echo "<tr><td ><font size=2  face='arial' ><b>Tel1: </b>$llamtel1</td>";
			echo "<td ><font size=2  face='arial' ><b>Tel2: </b>$llamtel2</td>";
			echo "<td ><font size=2  face='arial' ><b>Móvil: </b>$llammovi</td></tr>";

			echo "<tr><td ><font size=2  face='arial' ><b>Email1: </b>$llamemai</td>";
			echo "<td colspan='2'><font size=2  face='arial' ><b>Correo directo: </b>$llamdire</td></tr>";

			echo "</table></fieldset>";



			echo "<tr><td align='center' colspan='3'><table border=0 width=330 >";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b> &nbsp; </b></td></tr>";
			echo "<tr><td colspan='3'><font size=2  face='arial'><b>  &nbsp;</b></td></tr>";
			echo "<tr><td ><font size=2  face='arial' ><b>&nbsp;</b></td>";
			echo "<td ><font size=2  face='arial' ><b> &nbsp;</b></td>";
			echo "<td ><font size=2  face='arial' ><b>&nbsp; </b></td></tr>";

			echo "<tr><td ><font size=2  face='arial' ><b> &nbsp;</b></td>";
			echo "<td colspan='2'><font size=2  face='arial' ><b> &nbsp;</b></td></tr>";

			echo "</table>";

		}
	}else /// vista para VIP, se muestra la presentación tarjeta VIP
	{
		echo "<table align='center' >";
		echo "<tr><td align='center' colspan='0'><table align='center' border=1 bordercolor=#000080 width=340 style='border:solid;'>";
		//echo "<td align='center' colspan='0'><img SRC='/MATRIX/images/medical/root/clinica.JPG'>";// width=150 high=43></td>";
		echo "<td align='center' colspan='0'><img SRC='/MATRIX/images/medical/root/clinica.JPG' width=160 high=160></td>";
		echo "<td align=center colspan='1' ><font size=3 color='#000080' face='arial'>";
		echo "<B>PROGRAMA AFINIDAD<br>";
		echo "</table>";

		//botón volver
		echo "<form method='post' action='Magenta.php' target='_parent'>";
		echo "<td align='center'><input type='submit' name='volver' value='VOLVER'></form></TD>";

		//botón actualizar
		echo "<form method='post' action='actualizar.php' target='_parent'>";
		echo "<input type='hidden' name='tipUsu' value='$tipUsu'>";
		echo "<input type='hidden' name='doc' value='$doc'>";
		echo "<input type='hidden' name='tipDoc' value='$tipDoc'>";
		echo "<input type='hidden' name='fecAct' value='$fecAc'>";
		echo "<input type='hidden' name='gif' value='$gif'>";
		echo "<input type='hidden' name='nombres' value='$nombres'>";
		echo "<input type='hidden' name='ape1' value='$ape1'>";
		echo "<input type='hidden' name='ape2' value='$ape2'>";
		echo "<input type='hidden' name='fecNac' value='$fecNac'>";
		echo "<input type='hidden' name='lugNac' value='$lugNac'>";
		echo "<input type='hidden' name='sexo' value='$sexo'>";
		echo "<input type='hidden' name='estCivil' value='$estCivil'>";
		echo "<input type='hidden' name='nHijos' value='$nHijos'>";
		echo "<input type='hidden' name='prof' value='$prof'>";
		echo "<input type='hidden' name='tel1' value='$tel1'>";
		echo "<input type='hidden' name='tel2' value='$tel2'>";
		echo "<input type='hidden' name='movil' value='$movil'>";
		echo "<input type='hidden' name='email1' value='$email1'>";
		echo "<input type='hidden' name='email2' value='$email2'>";
		echo "<input type='hidden' name='dir' value='$dir'>";
		echo "<input type='hidden' name='estrato' value='$estrato'>";
		echo "<input type='hidden' name='zona' value='$zona'>";
		echo "<input type='hidden' name='municipio' value='$municipio'>";
		echo "<input type='hidden' name='dept' value='$dept'>";
		echo "<input type='hidden' name='pais' value='$pais'>";
		echo "<input type='hidden' name='acompa' value='$acompa'>";
		echo "<input type='hidden' name='fam' value='$fam'>";
		echo "<input type='hidden' name='afiliado' value='$afiliado'>";
		echo "<input type='hidden' name='planC' value='$planC'>";
		echo "<input type='hidden' name='llamtel1' value='$llamtel1'>";
		echo "<input type='hidden' name='llamtel2' value='$llamtel2'>";
		echo "<input type='hidden' name='llamemai' value='$llamemai'>";
		echo "<input type='hidden' name='llammovi' value='$llammovi'>";
		echo "<input type='hidden' name='llamdire' value='$llamdire'>";
		echo "<input type='hidden' name='servicio' value='$servicio'>";
		echo "<input type='hidden' name='comBeb' value='$comBeb'>";
		echo "<input type='hidden' name='comFam' value='$comFam'>";
		echo "<input type='hidden' name='comAdo' value='$comAdo'>";
		echo "<input type='hidden' name='comAdu' value='$comAdu'>";
		echo "<input type='hidden' name='comSnc' value='$comSnc'>";
		echo "<input type='hidden' name='comSrs' value='$comSrs'>";
		echo "<input type='hidden' name='comSrn' value='$comSrn'>";
		echo "<input type='hidden' name='comCan' value='$comCan'>";
		echo "<input type='hidden' name='comCar' value='$comCar'>";
		echo "<input type='hidden' name='comSmu' value='$comSmu'>";
		echo "<input type='hidden' name='comOtr' value='$comOtr'>";
		echo "<input type='hidden' name='gusLec' value='$gusLec'>";
		echo "<input type='hidden' name='gusApl' value='$gusApl'>";
		echo "<input type='hidden' name='gusAdr' value='$gusAdr'>";
		echo "<input type='hidden' name='gusCin' value='$gusCin'>";
		echo "<input type='hidden' name='gusMus' value='$gusMus'>";
		echo "<input type='hidden' name='depFut' value='$depFut'>";
		echo "<input type='hidden' name='depGol' value='$depGol'>";
		echo "<input type='hidden' name='depTen' value='$depTen'>";
		echo "<input type='hidden' name='depEqu' value='$depEqu'>";
		echo "<input type='hidden' name='depGym' value='$depGym'>";
		echo "<input type='hidden' name='depOtr' value='$depOtr'>";
		echo "<input type='hidden' name='requerAnt' value='$requer'>";
		echo "<input type='hidden' name='egr' value='$egr'>";
		echo "<input type='hidden' name='egr1' value='$egr1'>";
		echo "<input type='hidden' name='color' value='$color'>";
		echo "<input type='hidden' name='cco' value='$cco'>";
		echo "<td align='center'><input type='submit' name='actualizar' value='ACTUALIZAR'></form></TD>";
		echo "<td ><font size=2><A href='persona.php?doc=$doc&tipDoc=$tipDoc&cco=$cco'>RECARGAR</A></font></td>&nbsp;&nbsp;&nbsp;";
		echo "<td ><font size=2>&nbsp;&nbsp;&nbsp;</font></td>&nbsp;&nbsp;&nbsp;";

		echo "<td ><font size=2><A href='index_magenta.htm' TARGET='_new'>AYUDA</A></font></td>";



		echo "</table>";


		//echo "<table width='448' height='285' border='0' background='\matrix\images\medical\Magenta\\$gif' align='center'>"
		echo "<table width='448' height='285' border='0' background='/matrix/images/medical/Magenta/$gif' align='center'>"
		?>
  			
    		<tr> 
      			<td width="1%" height="10%">&nbsp;</td>
      			<td width="24%">&nbsp;</td>
      			<td width="46%">&nbsp;</td>
      			<td width="26%">&nbsp;</td>
      			<td width="3%">&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="26%">&nbsp;</td>
      			<td colspan="3"><font color="#0033FF" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>&nbsp;<br>
        		&nbsp;</strong></font></td>
      			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="14%">&nbsp;</td>
      			<td colspan="3"><font color="#003399" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
      			&nbsp;</strong></font></td>
      			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="14%">&nbsp;</td>
      			<td colspan="3"><font color="#003399" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
      			&nbsp;</strong></font></td>
      			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="14%">&nbsp;</td>
      			<td colspan="3"><font color="#003399" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
      			&nbsp;</strong></font></td>
     			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="10%">&nbsp;</td>
      			<td >&nbsp;</td>
      			<?php
      			echo "<td colspan=2><font size='3'>".ucwords(strtolower($nombres))." ".ucwords(strtolower($ape1))." ".ucwords(strtolower($ape2))."</font></td>";
         		 ?>
      	
    		</tr>
    		<tr> 
      			<td height="10%">&nbsp;</td>
      			<td>&nbsp;</td>
      			<?php
      			echo "<td><font size='3'><center>$tipDocImpr.$doc</center></font></td>";
         		 ?>
      			<td>&nbsp;</td>
     			<td>&nbsp;</td>
    		</tr>
  			</table>
  			
  			<?php
  			//tabla requerimiento
  			echo "</br></br><font size=2  face='arial' ><b><center>REQUERIMIENTOS (realizados durante las visitas)</center></b>";
  			echo "</br><fieldset style='border:solid;border-color:#000080 ; width=400' ; align=center>";

  			echo $requer;
  			echo "</fieldset>";
	}
}
?>
</body>

</html>
