<html>

<head>
  <title>HISTORIA OFTALMO</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Historia Clínica Oftalmologica</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size='2'> <b> repHistOf.php Ver.2006-03-29</b></font></tr></td></table><br><br>
</center>
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=1,scrollbars =1,titlebar=0,width=900,height=425');
	}
//-->
</script>
<?php
include_once("conex.php");

/********************************************************
*														*
*     REPORTE DE LA HISTORIA CLINICA OFTALMOLOGICA 		*
*														*
*********************************************************/

//==================================================================================================================================
//GRUPO						:OFTALMOLOGIA
//AUTOR						:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN			:2004-04-15
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2010-02-19)";
//DESCRIPCIÓN					: Despliega toda la información referente a la historia clínica de un paciente, sin importar el
//								  oftalmologo, esto último por petición del Dr Santiago Hurtado.
//								  Tiene como parámetro el médico, el paciente y las fechas de inicio y de corte, por defecto muestra
//								  como fecha inicial la fecha de la primera historia clínica y como fecha final la fecha actual.
//								  Despliega la información básica contenida en la tabla oftalmo_000001, todos los eventos de consulta
//			 					  en su orden cronologico, es decir todas las historias clínicas (oftalmo_000003) y todos los
//								  seguimientos (oftalmo_000009) hechos a un paciente, inmediatamente despúes de cada evento
//								  muestra todos los otros elementos asociados, contenidos el las tablas oftalmo_000003 a la
//								  oftalmo_000008.
//								  Se dice que un elemento esta asociado a un evento de consulta cuando la fecha que le fue asignada,
//								  en el campo fecha o fecha_actual en el caso especifico de cirugía, es igual o mayor a la fecha
//								  del evento y menor a la fecha del evento siguiente.
//
//--------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//  2010-02-19
//		Se coloco un Hipervinculo al programa InfoHis.php  para acceder a informacion historica de presiones y lentes por cada ojo.
//
//	2006-03-29
//		Se trasladan los campos referentes al acompañante, el responsable, la entidad y el tipo de vinculación de Historia a Encabezado
//		pues se cambian de la tabla oftalmo_000003 a oftalmo_000001.
//		Se borran los campos Pulso y Peso de Historia pues desaparecen de la tabla oftalmo_000003.
//
//	2006-03-28
//		Se crean las funciones: lentes, Medicamentos, Cirugia, Imagenes, Impresion, Examenes. Y se reemplaza donde debe.
//		Se modifican todos los queries para que no traigan solo la información de los otros formularios si fueron el día de la consulta
//		si no tambien en el intervalo hasta la siguiente consulta.
//
//
//	2006-03-27
//		Modificacion en el llamado a las imagenes, se cambia user=..., por usuario=1-oftalmo, con el fin de qu encuenbtre el grupo al
//		que estan asociadas las imagenes.
//		Se Cambia Clínica Médica Las americas por Clinica las Americas.
//		Se reemplaza <form action='000003_of01.php' method=post> por <form action='' method=post>
//		Se crea la función seguimiento
//
//--------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	det_selecciones
//	oftalmo_000001
//	oftalmo_000003
//	oftalmo_000004
//	oftalmo_000005
//	oftalmo_000006
//	oftalmo_000007
//	oftalmo_000008
//	oftalmo_000009


/**
 * Imprime en pantalla en encabezado de toda la historia
 *
 * @param String $nomed	Nombre del Médico
 * @param String $reg	Registro del médico
 * @param String $pacn	Nombre del Paciente
 * @param String $id
 * @param Int	 $nrohist Número de la historia dl paciente
 * @param [][] 	 $row	Información aimprimir en pantalla, sacada del la tabla oftalmo_000001
 */
function Encabezado($nomed,$reg,$pacn,$id,$nrohist,$row){
	echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
	echo "<img SRC='\MATRIX\images\medical\pediatra\logotorre.JPG' width='180' height='117'></td>";
	echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
	echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
	echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
	echo "<br>MEDICO OFTALMOLOGO<BR>Reg.:</b>".$reg."</b></font>";
	echo "</tr></table>";

	/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
	echo "<table align=center border=1 width=725 >";
	echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS GENERALES</b></font></td></tr>";
	echo "<tr><td nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$pacn."</td>";
	echo "<td><font size=3  face='arial'><b>D.I:</b> ".$id."</td>";
	echo "<td><font size=3  face='arial'><b>N° HISTORIA:</b> ".$nrohist."</td></tr>";
	echo "<tr><td><font size=3  face='arial'><b>FECHA NACIMIENTO:</b>".$row[9]." </td>";
	echo "<td><font size=3  face='arial'><b>SEXO:</b> ".STRTOLOWER(SUBSTR($row[10],3))." </td>";
	echo "<td><font size=3  face='arial'><b>OCUPACION:</b> ".$row[11]." </td></tr>";
	echo "<tr><td ><font size=3  face='arial'><b>DIRECCION:</b>".$row[14]." </td>";
	echo "<td><font size=3  face='arial'><b>CIUDAD:</b> ".$row[13]." </td>";
	echo "<td><font size=3  face='arial'><b>TELEFONOS:</b>".$row[12]." </td>";
	echo "<tr><td ><font size=3 face='arial' ><B>ACOMPAÑANTE: </b>".$row['Anom']."</td>";
	echo "<td colspan='2'><font size=3  face='arial'><b>TELEFONO:</b> ".$row['Atel']."</td>";
	echo "<tr><td ><font size=3 face='arial' ><B>RESPONSABLE: </b>".$row['Resn']."</td>";
	echo "<td colspan='2'><font size=3  face='arial'><b>TELEFONO:</b> ".$row['Restel']."</td>";
	echo "<tr><td ><font size=3 face='arial' ><B>PARENTESCO RESPONSABLE: </b>".$row['Respar']."</td>";
	echo "<td colspan='2'><font size=3  face='arial'><b>VINCULACION:</b> ".$row['Vinc']."</td>";
	echo "<tr><td colspan='4'><font size=3 face='arial' ><B>ENTIDAD: </b>".$row['Entidad']."</td>";
}

/**
 * Despliega en pantalla toda la información de la historia clínica, sacada de la tabla oftalmo_000003
 *
 * @param [][] $row Información de la historia clínica.
 */
function Historia ($row){
	echo "<table align=center border=1 width=725 >";
	echo "<tr><td align=left colspan='6' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA ".$row['Fecha']."</b></font></td></tr>";
	echo "<tr><td width=375 align=left  colspan='3' bgcolor='#AADDFF'><font size='3' face='arial'><b>MOTIVO CONSULTA Y ENFERMEDAD ACTUAL</b>";
	echo "<fieldset style='background-color: #FFFFFF'>".$row['Motivo_consulta_y_enf']."</fieldset>";
	echo "<td width=375 align=left  colspan='3' bgcolor='#AADDFF'><font size='3' face='arial'><b>ANTECEDENTES PERSONALES</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$row['Antec_personales']."</fieldset></TR>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'></td>";
	echo "<td bgcolor='#FFEECC' width='123'><font size=3  face='arial'><b>AV SC</b></td>";
	echo "<td bgcolor='#FFEECC' width='96'><font size=3  face='arial'><b>AV CC</b></td>";
	echo "<td bgcolor='#FFEECC' width='101'><font size=3  face='arial'><b>AV ST</b></td>";
	echo "<td bgcolor='#FFEECC' width='100'><font size=3  face='arial'><b>PI mmHg</b></td>";
	echo "<td bgcolor='#AADDFF' ROWSPAN='3' width='162'><font size=3  face='arial'><b>REFLEJOS</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$row['Reflejos']."</fieldset></TR>";
	echo "<tr ondblclick='ejecutar(".chr(34)."/MATRIX/Oftalmologia/Reportes/InfoHis.php?pac=".$row['Paciente'].chr(34).")'><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>OD</b></td>";
	echo "<td width='123'><font size=3  face='arial'><b></b>".$row['Sc_od']."</td>";
	echo "<td width='96'><font size=3  face='arial'><b></b>".$row['Cc_od']."</td>";
	echo "<td width='101'><font size=3  face='arial'><b></b>".$row['St_od']."</td>";
	echo "<td width='100'><font size=3  face='arial'><b></b>".$row['Pi_od_mmhg']."</td>";
	echo "<tr ondblclick='ejecutar(".chr(34)."/MATRIX/Oftalmologia/Reportes/InfoHis.php?pac=".$row['Paciente'].chr(34).")'><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>OS</b></td>";
	echo "<td width='123'><font size=3  face='arial'><b></b>".$row['Sc_os']."</td>";
	echo "<td width='96'><font size=3  face='arial'><b></b>".$row['Cc_os']."</td>";
	echo "<td width='101'><font size=3  face='arial'><b></b>".$row['St_os']."</td>";
	echo "<td width='100'><font size=3  face='arial'><b></b>".$row['Pi_os_mmhg']."</td></tr>";

	echo "<TR><td colspan=6 width='756'><font size=3  face='arial'><b>COVER TEST CERCA:</b> ".$row['Cover_t_cerca']."</td></TR>";
	echo "<TR><td colspan=6 width='756'><font size=3  face='arial'><b>COVER TEST LEJOS:</b> ".$row['Cover_t_lejos']."</td></TR>";


	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><B>PARAMETRO</B></td>";
	echo "<td bgcolor='#FFEECC' colspan=2 width='225'><font size=3  face='arial'><b>OD</b></td>";
	echo "<td bgcolor='#FFEECC'colspan=3 width='375'><font size=3  face='arial'><b>OS</b></td></tr>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>BIOMICROSCOPIA</b></td>";
	echo "<td colspan=2 width='225'><font size=3  face='arial'><b></b>".$row['Biomicro_od']."</td>";
	echo "<td colspan=3 width='375'><font size=3  face='arial'><b></b>".$row['Biomicro_os']."</td></tr>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>GONIOSCOPIA</b></td>";
	echo "<td colspan=2 width='225'><font size=3  face='arial'><b></b>".$row['Gonios_od']."</td>";
	echo "<td colspan=3 width='375'><font size=3  face='arial'><b></b>".$row['Gonios_os']."</td></tr>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>FONDO DE OJO</b></td>";
	echo "<td colspan=2 width='225'><font size=3  face='arial'><b></b>".$row['Fondo_od']."</td>";
	echo "<td colspan=3 width='375'><font size=3  face='arial'><b></b>".$row['Fondo_os']."</td></tr>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>R. SIN DILATAR</b></td>";
	echo "<td colspan=2 width='225'><font size=3  face='arial'><b></b>".$row['Sin_d_od']."</td>";
	echo "<td colspan=3 width='375'><font size=3  face='arial'><b></b>".$row['Sin_d_os']."</td></tr>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>R. DILATADO</b></td>";
	echo "<td colspan=2 width='225'><font size=3  face='arial'><b></b>".$row['Dilatado_od']."</td>";
	echo "<td colspan=3 width='375'><font size=3  face='arial'><b></b>".$row['Dilatado_os']."</td></tr>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>USA ACTUALMENTE</b></td>";
	echo "<td colspan=2 width='225'><font size=3  face='arial'><b></b>".$row['Usa_actual_od']."</td>";
	echo "<td colspan=3 width='375'><font size=3  face='arial'><b></b>".$row['Usa_actual_os']."</td></tr>";
	echo "<tr><td bgcolor='#FFEECC' width='144'><font size=3  face='arial'><b>QUERATOMETRIA</b></td>";
	echo "<td colspan=2 width='225'><font size=3  face='arial'><b></b>".$row['Queratom_od']."</td>";
	echo "<td colspan=3 width='375'><font size=3  face='arial'><b></b>".$row['Queratom_os']."</td></tr>";
	echo "<tr><td width=375 align=left  colspan='3' bgcolor='#AADDFF'><font size='3' face='arial'><b>MOTILIDAD</b>";
	echo "<fieldset style='background-color: #FFFFFF'>".$row['Motilidad']."</fieldset>";
	echo "<td width=375 align=left  colspan='3' bgcolor='#AADDFF'><font size='3' face='arial'><b>OBSERVACIONES</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$row['Observaciones']."</fieldset></TR>";
	echo "<tr><td width=375 align=left  colspan='3' bgcolor='#AADDFF'><font size='3' face='arial'><b>DIAGNOSTICOS</b>";
	echo "<fieldset style='background-color: #FFFFFF'>".$row['Diagnosticos']."</fieldset>";
	echo "<td width=375 align=left  colspan='3' bgcolor='#AADDFF'><font size='3' face='arial'><b>TRATAMIENTO</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$row['Tratamiento']."</fieldset></TR>";
	echo "</table>";
}

/**
 * Despliega en pantalla toda la información de un seguimiento, sacada de la tabla oftalmo_000009
 *
 * @param [][] $seg Información referente a un seguimirnto.
 */
function Seguimiento($seg){


	echo "</table><table align=center border=1 width=725 >";
	echo "<tr><td align=left colspan='2' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SEGUIMIENTO ".$seg['Fecha']."</b></font></td></tr>";
	echo "<tr><td colspan=2><font size=3 face='arial' ><B>Dx INICIAL: </b>".$seg['Dx_inicial']."</td></tr>";
	echo "<tr><td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>EVOLUCION</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Evolucion']."</fieldset></TR>";
	echo "<td><font size=3 face='arial' >";
	echo "<table width=345 ALIGN=CENTER border=1 ><td width=60 align=center bgcolor='#FFEECC'><b>A.V</b></td>";
	echo "<td align=center width=142 bgcolor='#FFEECC'><b>OD</b></td><td align=center width=142 bgcolor='#FFEECC'><b>OS</b></td></tr>";
	echo "<tr><td align=center bgcolor='#FFEECC'><b>SC</b></td><td align=center>".$seg['Sc_od']."</td><td align=center>".$seg['Sc_os']."</td>";
	echo "<tr><td align=center bgcolor='#FFEECC'><b>CC</b></td><td align=center>".$seg['Cc_od']."</td><td align=center>".$seg['Cc_os']."</td></tr></table></tr>";
	echo "<tr><td colspan=2><font size=3 face='arial' ><B>Dx: </b>".$seg['Dx']."</td>";
	echo "<tr><td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>EXAMEN FISICO</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Examen_fisico']."</fieldset>";
	echo "<td  align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>CONDUCTA</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Conducta']."</fieldset></TR>";
	echo "<tr  ondblclick='ejecutar(".chr(34)."/MATRIX/Oftalmologia/Reportes/InfoHis.php?pac=".$seg['Paciente'].chr(34).")'><td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>Presion OD mmHg</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Pr_od_mmhg']."</fieldset>";
	echo "<td  align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>Presion OI mmHg</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Pr_oi_mmhg']."</fieldset></TR>";
}

/**
 * Busca en la BD la información de lentes recetados al paciente en la tabla oftalmo_000004 segun el query
 * aumenta el contador de elmentos e ingresa el formato de impresión para lentes a la matriz de impresión .
 *
 * @param String[][] $A Matriz que contiene todo los datos a imprimir en pantalla
 * @param Int $p Elementos asociados a un evento de consulta
 * @param String $query Query que se debe hacer en la BD para ytraer la información
 * @param Int $conex Identificador de conexión con la Base de Datos
 */
function Lentes(&$A,&$p,$query,$conex){
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			$A[$p][1]="<fieldset style='background-color: #FFFFFF; '><table ALIGN=CENTER border=1 width=360><td width=60>.</td><td width=70 align=center><b>ESFERA</b></td>";
			$A[$p][1]=$A[$p][1]."<td align=center width=70 ><b>CILINDRO</b></td><td width=70 align=center><b>EJE</b></td><td width=70 align=center><b>Add</b></td></tr>";
			$A[$p][1]=$A[$p][1]."<tr><td align=center><b>OD</b></td><td align=center>".$row[6]."</td><td align=center>".$row[8]."</td><td align=center>".$row[10]."</td><td align=center>".$row[12]."</td></tr>";
			$A[$p][1]=$A[$p][1]."<tr><td align=center><b>OS</b></td><td align=center>".$row[7]."</td><td align=center>".$row[9]."</td><td align=center>".$row[11]."</td><td align=center>".$row[13]."</td></tr>";
			$A[$p][1]=$A[$p][1]."</table><BR><CENTER><b>ESPECIFICACIONES:</b> ".$row[14]."</fieldset>";
			$A[$p][0]=$row[5];
			$p++;

		}
	}
}

/**
 * Busca en la BD la información de los medicamentos recetados al paciente en la tabla oftalmo_ 000005 segun el query
 * aumenta el contador de elmentos e ingresa el formato de impresión para medicamentos a la matriz de impresión .
 *
 * @param String[][] $A Matriz que contiene todo los datos a imprimir en pantalla
 * @param Int $p Elementos asociados a un evento de consulta
 * @param String $query Query que se debe hacer en la BD para ytraer la información
 * @param Int $conex Identificador de conexión con la Base de Datos
 */
function Medicamentos (&$A,&$p,$query,$conex) {
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			$med=array();
			$q=0;
			$medicamentos="";
			$poso=strpos($row[0],">");
			do
			{
				$med[$q]=substr($row[0],0,$poso+1);	// el primer medicamento con su dosificación
				$poso2=strpos($med[$q],"-");
				$med[$q]=substr($med[$q],$poso2+1);	//le recorlamos el numero
				$poso3=strpos($med[$q],"*");
				$med[$q]=substr($med[$q],0,$poso3)."<br>".substr($med[$q],$poso3+1);	//armamos el medicamento con la dosificacion
				$poso2=strpos($med[$q],"-");
				$row[0]=substr($row[0],$poso+1,strlen($row[0]));
				$poso=strpos($row[0],">");
				$medicamentos=$medicamentos.$med[$q]."<br>";
				$q++;
			}while(is_int($poso));
			$A[$p][1]="MEDICAMENTOS".$medicamentos;
			$A[$p][0]=$row['Fecha'];
			$p++;
		}
	}
}

/**
 * Busca en la BD la información de los EXAMENES ORDENADOS al paciente en la tabla oftalmo_ 000006 segun el query
 * aumenta el contador de elmentos e ingresa el formato de impresión para EXAMENES a la matriz de impresión .
 *
 * @param String[][] $A Matriz que contiene todo los datos a imprimir en pantalla
 * @param Int $p Elementos asociados a un evento de consulta
 * @param String $query Query que se debe hacer en la BD para ytraer la información
 * @param Int $conex Identificador de conexión con la Base de Datos
 */
function Examenes (&$A,&$p,$query,$conex) {

	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			$exa=array();
			$k=0;
		/*	$examenes="";
			$posk=strpos($row[0],">");
			do
			{
				$exa[$k]=substr($row[0],0,$posk+1);
				$posk2=strpos($exa[$k],"-");
				$exa[$k]=substr($exa[$k],$posk2+1);
				$posk2=strpos($exa[$k],"-");
				$row[0]=substr($row[0],$posk+1,strlen($row[0]));
				$examenes=$examenes.$exa[$k]."<br>";
				$posk=strpos($row[0],">");
				$k++;
			}while(is_int($posk));
			*/

			//$A[$p][1]="EXAMENES".$examenes;
			$A[$p][1]="EXAMENES".$row['Examenes'];
			$A[$p][0]=$row['Fecha'];
			$p++;
		}// end del for
	}
}

/**
 * Busca en la BD la información de las CIRUGIAS ORDENADAS al paciente en la tabla oftalmo_ 000008 segun el query
 * aumenta el contador de elmentos e ingresa el formato de impresión para ORDENES DE CIRUGIA a la matriz de impresión .
 *
 * @param String[][] $A Matriz que contiene todo los datos a imprimir en pantalla
 * @param Int $p Elementos asociados a un evento de consulta
 * @param String $query Query que se debe hacer en la BD para ytraer la información
 * @param Int $conex Identificador de conexión con la Base de Datos
 */
function Cirugia (&$A,&$p,$query,$conex) {
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			$A[$p][1]="CX<B>DIAGNOSTICO:</B> ".$row[0]."<br><B>TRATAMIENTO: </B>".$row[1];
			$A[$p][0]=$row[3];
			$p++;
		}
	}
}

/**
 * Busca en la BD la información de las IMAGENES de los resultados de losexamenes, consentimientos, etc  al paciente en la tabla
 * oftalmo_ 000007 segun el query.
 * Aumenta el contador de elmentos e ingresa el formato de impresión para hypervinculos a las imagenes la matriz de impresión .
 *
 * @param String[][] $A Matriz que contiene todo los datos a imprimir en pantalla
 * @param Int $p Elementos asociados a un evento de consulta
 * @param String $query Query que se debe hacer en la BD para ytraer la información
 * @param Int $conex Identificador de conexión con la Base de Datos
 */
function Imagenes (&$A,&$p,$query,$conex) {
	$err = mysql_query(str_replace('*', 'Descripcion,Imagen,Fecha',$query),$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);

			//$A[$p][1]="IMAGENES".$row['Descripcion']."<br><b><A HREF='/matrix/graficas.php?Graph=".$row['Imagen']."&amp;usuario=1-oftalmo' target = '_blank'>Ver imagen";
			$A[$p][1]="IMAGENES".$row['Descripcion']."<br><b><A HREF='/matrix/images/medical/oftalmologia/".$row['Imagen']."'>Ver imagen";
			$A[$p][0]=$row['Fecha'];
			$p++;
		}
	}
}

/**
 * Imprime todos los elementos asociados a un evento de consulta que se encuentran en la matriz A según cuantos P diga que hay.
 *
 * @param String[][] $A Matriz que contiene todo los datos a imprimir en pantalla
 * @param Int $p Elementos asociados a un evento de consulta
 */
function Impresion ($A,$p) {


	If($p % 2 !=0 )
	{
		$e=$p-1; //HAY QUE HACER UN CAMBIO NO NECESARIAMENTE ES SEGUIMIENTO

		if(is_int(strpos($A[$p-1][1],"table")))	//es una formula de lentes
		{
			$ultimo= "<tr><td  align=center  colspan='2' bgcolor='#AADDFF'><font size='3' face='arial'><b>LENTES ".$A[$p-1][0]."</b>".$A[$p-1][1]."</TR>";
		}
		else if(is_int(strpos($A[$p-1][1],"MEDICAMENTOS")))		// es una formula de medicamentos
		{
			$ultimo= "<tr><td  align=center  colspan='2' bgcolor='#AADDFF'><font size='3' face='arial'><b>MEDICAMENTOS ".$A[$p-1][0]."</b><fieldset style='background-color: #FFFFFF; '>".substr($A[$p-1][1],12,strlen($A[$p-1][1])-16)."</fieldset>";
		}
		else if(is_int(strpos($A[$p-1][1],"EXAMENES")))		// es una formula de examenes
		{
			$ultimo="<tr><td  align=center  colspan='2' bgcolor='#AADDFF'><font size='3' face='arial'><b>EXAMENES ".$A[$p-1][0]."</b>";
			$ultimo=$ultimo."<fieldset style='background-color: #FFFFFF; '>".substr($A[$p-1][1],8,strlen($A[$p-1][1])-12)."</fieldset>";
		}
		else if(is_int(strpos($A[$p-1][1],"IMAGENES")))		// es una formula de imagenes
		{
			$ultimo= "<tr><td  colspan='2' bgcolor='#AADDFF'><font size='3' face='arial'><b>IMAGENES ".$A[$p-1][0]."</b></left>";
			$ultimo=$ultimo."<fieldset style='background-color: #FFFFFF; '>".substr($A[$p-1][1],8,strlen($A[$p-1][1])-8)."</fieldset>";
		}
		else if(is_int(strpos($A[$p-1][1],"CX")))		// es una formula de ORDEN Cx
		{
			$ultimo="<tr><td width=375 align=center  colspan='2' bgcolor='#AADDFF'><font size='3' face='arial'><b>ORDEN DE Cx".$A[$p-1][0]."</b>";
			$ultimo=$ultimo."<center><fieldset style='background-color: #FFFFFF; '>".substr($A[$p-1][1],2,strlen($A[$p-1][1])-2)."</fieldset>";
		}
		/*
		echo "<td align=left  colspan='6' bgcolor='#AADDFF'><font size='3' face='arial'><b>SEGUIMIENTO ".$A[0][0]."</b>";
		echo "<fieldset style='background-color: #FFFFFF; '>".$A[0][1]."</fieldset></TR>";*/
	}
	else
	{
		$e=$p;
		$ultimo="";
	}
	for ($m=0;$m<$e;$m+=2)
	{
		if(is_int(strpos($A[$m][1],"table")))	//es una formula de lentes
		{
			echo "<tr><td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>LENTES ".$A[$m][0]."</b>";
			echo $A[$m][1]."</TR>";
		}
		else if(is_int(strpos($A[$m][1],"MEDICAMENTOS")))		// es una formula de medicamentos
		{
			echo "<tr><td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>MEDICAMENTOS ".$A[$m][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m][1],12,strlen($A[$m][1])-16)."</fieldset>";
		}
		else if(is_int(strpos($A[$m][1],"EXAMENES")))		// es una formula de examenes
		{
			echo "<tr><td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>EXAMENES ".$A[$m][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m][1],8,strlen($A[$m][1])-12)."</fieldset>";
		}
		else if(is_int(strpos($A[$m][1],"IMAGENES")))		// es una formula de imagenes
		{
			echo "<tr><td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>IMAGENES ".$A[$m][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m][1],8,strlen($A[$m][1])-8)."</fieldset>";
		}
		else if(is_int(strpos($A[$m][1],"CX")))		// es una formula de ORDEN Cx
		{
			echo "<tr><td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>ORDEN DE Cx".$A[$m][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m][1],2,strlen($A[$m][1])-2)."</fieldset>";
		}

		if(is_int(strpos($A[$m+1][1],"table")))		//es una formula de lentes
		{
			echo "<td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>LENTES ".$A[$m+1][0]."</b>";
			echo $A[$m+1][1]."</TR>";
		}
		else if(is_int(strpos($A[$m+1][1],"MEDICAMENTOS")))		//es una formula de medicamentos
		{
			echo "<td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>MEDICAMENTOS ".$A[$m+1][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m+1][1],12,strlen($A[$m+1][1])-16)."</fieldset></TR>";
		}
		else if(is_int(strpos($A[$m+1][1],"EXAMENES")))		// es una formula de examenes
		{
			echo "<td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>EXAMENES ".$A[$m+1][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m+1][1],8,strlen($A[$m+1][1])-12)."</fieldset></TR>";
		}
		else if(is_int(strpos($A[$m+1][1],"IMAGENES")))		// es una formula de imagenes
		{
			echo "<td width=362 align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>IMAGENES ".$A[$m+1][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m+1][1],8,strlen($A[$m+1][1])-8)."</fieldset></TR>";
		}
		else if(is_int(strpos($A[$m+1][1],"CX")))		// es una formula de cx
		{
			echo "<td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>OREDEN DE Cx ".$A[$m+1][0]."</b>";
			echo "<fieldset style='background-color: #FFFFFF; '>".substr($A[$m+1][1],2,strlen($A[$m+1][1])-2)."</fieldset></TR>";
		}

	}
	echo $ultimo;
}


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));




	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha1) or !isset($year) )
	{
		echo "<form action='' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";
		/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
		$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'oftalmo' AND codigo = '002'  order by Descripcion ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				if (($row[0]."-".$row[1]) == $medico)
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}	// fin del if $num>0
		//}	//fin del else
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";

		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			//$query="select DISTINCT Paciente from oftalmo_000003 where Oftalmologo='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";V1.03
			$query="select DISTINCT Paciente from oftalmo_000003 where  Paciente like '%".$pac1."%' order by Paciente";
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$pac)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."</option>";
				}
			}	// fin $num>0
			echo "</select></td></tr>";
			echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
		}	//fin isset medico
		else
		{
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
			echo "</td></tr>";
		}
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";
		echo "<td bgcolor=#cccccc colspan=2><select name='fecha1'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			construir el drop down */
			$query = "select Fecha  from oftalmo_000003 where Paciente='".$pac."' ";//and Oftalmologo='".$medico."' "; V 1.03
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<=$num;$j++)
				{
					$row = mysql_fetch_array($err);
					$fec[$j]=$row[0];
					echo "<option>".$row[0]."</option>";
				}
				echo "</select></td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>HASTA: </font></td>";
				echo "<td bgcolor=#cccccc colspan=2>";
				if(!isset($year))
				{
					$year=date('Y');
					$month=date('m');
					$day=date('d');
				}
				echo "<select name='year'>";
				for($f=1980;$f<2051;$f++)
				{
					if($f == $year)
					echo "<option selected>".$f."</option>";
					else
					echo "<option>".$f."</option>";
				}
				echo "</select><select name='month'>";
				for($f=1;$f<13;$f++)
				{
					if($f == $month)
					if($f < 10)
					echo "<option selected>0".$f."</option>";
					else
					echo "<option selected>".$f."</option>";
					else
					if($f < 10)
					echo "<option>0".$f."</option>";
					else
					echo "<option>".$f."</option>";
				}
				echo "</select><select name='day'>";
				for($f=1;$f<32;$f++)
				{
					if($f == $day)
					if($f < 10)
					echo "<option selected>0".$f."</option>";
					else
					echo "<option selected>".$f."</option>";
					else
					if($f < 10)
					echo "<option>0".$f."</option>";
					else
					echo "<option>".$f."</option>";
				}
				echo "</select></td></tr>";
				/*<select name='fecha2'>";
				for ($j=0;$j<=$num;$j++)
				{
				echo "<option>".$fec[$j]."</option>";
				}*/
			}

		}
		//echo"</select>
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/***************************************************************
	APARTIR DE AQUI EMPIEZA LA IMPRESION DE LA HISTORIA		*/
	{
		$ini1=explode("-",$medico);
		$reg=$ini1[0];
		$nomed=$ini1[1];

		$ini1=explode("-",$pac);
		$nrohist=$ini1[0];
		$n1=$ini1[1];
		$n2=$ini1[2];
		$ap1=$ini1[3];
		$ap2=$ini1[4];
		$id=$ini1[5];
		$pacn=$n1." ".$n2." ".$ap1." ".$ap2;
		$fecha2=$year."-".$month."-".$day;
		/*Busqueda de los datos generales en la tabla paciente*/
		$querya="select * from oftalmo_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$querya=$querya."Apellido1='".$ap1."' and Apellido2='".$ap2."' and Documento='".$id."' ";
		$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			$row = mysql_fetch_array($err);
		}

		/*IMPRIMIR EL ENCABEZADO*/
		Encabezado ($nomed,$reg,$pacn,$id,$nrohist,$row);



		/*BUSQUEDA DE HISTORIAS CLINICAS QUE CUMPLAN CON LA FECHA*/

		$query="select fecha from oftalmo_000003 where Paciente='".$pac."'  and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
		$err = mysql_query($query,$conex);
		$nfechas = mysql_num_rows($err);
		if($nfechas>0)
		{
			for ($j=0;$j<$nfechas;$j++)	{										// se llena un arreglo con las fechas que cumplen
				$row = mysql_fetch_array($err);
				//Matriz de fechas de las historias
				$fec[$j]=substr($row[0],0,10);
			}
		}

		for ($y=0;$y<$nfechas;$y++)	{

			//Trer cada historia

			$query="select * from	oftalmo_000003 where Paciente='".$pac."' and  Fecha='".$fec[$y]."' order by Fecha";
			$err = mysql_query($query,$conex);
			$numy = mysql_num_rows($err);
			if($numy>0)	{
				$row = mysql_fetch_array($err);

				Historia($row);

				//Generación de límites de fechas para los querys que buscan elmentos y seguimientos
				if($y==($nfechas-1)) {
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
					$fecFin=$fecha2;
				}else{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
					$fecFin=$fec[$y+1];
				}

				//Busqueda de los seguimientos entre la fecha de una historia y otra o entre la fecha de la historia y la fecha2
				//Definición del query de busqueda para los elementos asociados a la historia
				$query="select Fecha from	oftalmo_000009 where Paciente='".$pac."' and ".$pre." order by Fecha";
				$err2 = mysql_query($query,$conex);
				$nseg = mysql_num_rows($err2);
				if($nseg>0) {
					for ($j=0;$j<$nseg;$j++)	{										// se llena un arreglo con las fechas que cumplen
						$row = mysql_fetch_array($err2);
						$fecSeg[$j]=$row[0];
					}
					//Despues de eta historia y antes de la siguiente hay un seguimiento
					//la fecha límite para la busqueda de elementos el la fecha del seguimiento
					$fecHisQ= "Fecha = '".$fec[$y]."' or (Fecha > '".$fec[$y]."' and Fecha < '".$fecSeg[0]."')";
				}else{

					//Despues de eta historia y antes de la siguiente NO hay un seguimiento
					//la fecha límite para la busqueda de elementos el la $fecFin, que puede ser la fecha de la siguiente historia $fec[$y+1]
					//o la fecha límite de busqueda $fecha2
					$fecHisQ= "Fecha = '".$fec[$y]."' or (Fecha > '".$fec[$y]."' and Fecha < '".$fecFin."')";
				}

				unset($A);
				$A=array();
				$p=0;

				$query="select * from tabla where Paciente='".$pac."'  and (".$fecHisQ.")";

				//Lentes
				Lentes($A,$p,str_replace('tabla','oftalmo_000004',$query),$conex);

				/*MEDICAMENTOS*/
				Medicamentos($A,$p,str_replace('tabla','oftalmo_000005',$query),$conex);


				//EXAMENES
				Examenes($A,$p,str_replace('tabla','oftalmo_000006',$query),$conex);


				// ORDEN DE CIRUGIA
				Cirugia ($A,$p,str_replace('tabla','oftalmo_000008',str_replace('Fecha','Fecha_actual',$query)),$conex);

				//IMAGENES
				Imagenes ($A,$p,str_replace('tabla','oftalmo_000007',$query),$conex);


				//Impresión en Pantalla
				if($p>0){
					echo "<table align=center border=1 width=725 >";
					echo "<tr><td align=left colspan='6' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>INFORMACION ADICIONAL RELACIONADA CON LA HISTORIA</b></font></td></tr>";
				}
				Impresion($A,$p);

				//INFORMACIÓN DE LOS SEGUIMIENTOS
				if($nseg>0)	{
					for ($x=0;$x<$nseg;$x++) {
						$query="select * from	oftalmo_000009 where Paciente='".$pac."' and Fecha='".$fecSeg[$x]."'";
						$err2 = mysql_query($query,$conex);
						$seg = mysql_fetch_array($err2);
						//echo "x=$x<br>query=$query<br>";
						Seguimiento($seg);

						/*Fechas para la busqueda*/
						$fecSegQ = "Fecha = '".$fecSeg[$x]."' or (Fecha > '".$fecSeg[$x]."' and Fecha < ";
						if($x == ($nseg-1)) {
							/*Es el último seguimiento entre esas historias o el último en general*/
							$fecSegQ = $fecSegQ."'".$fecFin."')";
						}else{
							$fecSegQ = $fecSegQ."'".$fecSeg[$x+1]."')";
						}

						unset($A);
						$A=array();
						$p=0;
						$query="select * from tabla where Paciente='".$pac."'  and (".$fecSegQ.")";

						//Lentes
						Lentes($A,$p,str_replace('tabla','oftalmo_000004',$query),$conex);

						/*MEDICAMENTOS*/
						Medicamentos($A,$p,str_replace('tabla','oftalmo_000005',$query),$conex);


						//EXAMENES
						Examenes($A,$p,str_replace('tabla','oftalmo_000006',$query),$conex);


						// ORDEN DE CIRUGIA
						Cirugia ($A,$p,str_replace('tabla','oftalmo_000008',str_replace('Fecha','Fecha_actual',$query)),$conex);

						//IMAGENES
						Imagenes ($A,$p,str_replace('tabla','oftalmo_000007',$query),$conex);


						//Impresión en Pantalla
						Impresion($A,$p);
					}
				}

				echo "</table>";
			} //si num y es mayor que cero es decir si hay historias
		}// el for que recorre las fechas del arreglo fec

		$query="select Fecha, Descripcion, Imagen  from	oftalmo_000013 where Paciente='".$pac."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			echo "<table align=center border=1 width=725 >";
			echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>IMAGENES EXTERNAS</b></font></td></tr>";
			echo "<tr><td align=left bgcolor='#AADDFF' height='15'><font size='3'  face='arial'><b>FECHA</b></font></td><td align=left bgcolor='#AADDFF' height='15'><font size='3'  face='arial'><b>DESCRIPCION</b></font></td><td align=left bgcolor='#AADDFF' height='15' colspan=2><font size='3'  face='arial'><b>IMAGEN</b></font></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<tr>";
				echo "<td align=left bgcolor='#FFFFFF'><font size='3'  face='arial'>".$row[0]."</td>";
				echo "<td align=left bgcolor='#FFFFFF'><font size='3'  face='arial'>".$row[1]."</td>";
				echo "<td align=left bgcolor='#FFFFFF'><font size='3'  face='arial'><A HREF='".$row[2]."' target = '_blank'>Ver imagen</a></td>";
			}
			echo "</table>";
		}
	}// todos los datos estan set
	include_once("free.php");
}?>