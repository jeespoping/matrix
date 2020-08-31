<html>

<head>
  <title>HISTORIA VISUAL LASER</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Historia Clínica Oftalmologica</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size='2'> <b> repHistOfvisual.php Ver.2011-10-25</b></font></tr></td></table><br><br>
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
//GRUPO						:231 VLASER
//AUTOR						:Gabriel Agudelo.
$wautor="Gabriel Agudelo.";
//FECHA CREACIÓN			:2011-10-25
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2011-10-25)";
//DESCRIPCIÓN					: Despliega toda la información referente a la historia clínica de un paciente, 
//								  Tiene como parámetro el médico, el paciente y las fechas de inicio y de corte, por defecto muestra 
//								  como fecha inicial la fecha de la primera historia clínica y como fecha final la fecha actual. 
//								  Despliega la información básica contenida en la tabla vlaser_000001, la historia clínica (vlaser_000003) y todos los 
//								  seguimientos (vlaser_000009) hechos a un paciente, inmediatamente despúes de cada evento 
//								  muestra todos los otros elementos asociados, contenidos el las tablas vlaser_000004 a la 
//								  vlaser_000008.
//--------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//
// Nov 10/2011 Se agregan campos en las tablas 000001 - 000003 - 000004 - 000009 y se organiza para que salgan estos campos en el reporte  
//--------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	det_selecciones
//	vlaser_000001
//	vlaser_000003
//	vlaser_000004
//	vlaser_000005
//	vlaser_000006
//	vlaser_000008
//	vlaser_000009


/**
 * Imprime en pantalla en encabezado de toda la historia
 *
 * @param String $nomed	Nombre del Médico		
 * @param String $reg	Registro del médico
 * @param String $pacn	Nombre del Paciente
 * @param String $id
 * @param Int	 $nrohist Número de la historia dl paciente
 * @param [][] 	 $row	Información aimprimir en pantalla, sacada del la tabla vlaser_000001
 */
function Encabezado($nomed,$reg,$pacn,$id,$nrohist,$row){
	echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
	echo "<img SRC='\MATRIX\images\medical\vlaser\logovisual.JPG' width='180' height='117'></td>";
	echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
	echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>VISUAL LASER S.A.S.</b></font>";
	echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
	echo "<br>MEDICO OFTALMOLOGO<BR>Reg.:</b>".$reg."</b></font>";
	echo "</td></tr></table>";

	/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
	echo "<table align=center border=1 width=725 >";
	echo "<tr><td align=center colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS GENERALES</b></font></td></tr>";
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
	echo "<tr><td colspan='4'><font size=3 face='arial' ><B>ORIGEN: </b>".$row['Origen']."</td>";
	echo "</tr></table>";
}

/**
 * Despliega en pantalla toda la información de la historia clínica, sacada de la tabla vlaser_000003
 *
 * @param [][] $row Información de la historia clínica.
 */
function Historia ($row){
	echo "<table align=center border=1 width=725 >";
	echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA  ".$row['Fecha']."</b></font></td></tr>";	
	//MOTIVO DE CONSULTA Y ENFERMEDAD ACTUAL
	echo "<tr><td align=center colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>MOTIVO DE CONSULTA Y ENFERMEDAD ACTUAL</b></font></td></tr>";
	if ($row['Motivo_consulta_y_enf']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' >".$row['Motivo_consulta_y_enf']."</td></tr>";
	// ANTECEDENTES PERSONALES 
	echo "<tr><td align=center colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES PERSONALES</b></font></td></tr>";
	if ($row['Antec_personales']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Ojos: </b>".$row['Antec_personales']."</td></tr>";
	// AGUDEZA VISUAL
	echo "<tr><td align=center colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>AGUDEZA VISUAL</b></font></td></tr>";
	if ($row['Sc_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Agudeza Visual SC OD: </b>".$row['Sc_od']."</td></tr>";
    if ($row['Sc_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Agudeza Visual SC OS: </b>".$row['Sc_os']."</td></tr>";
	if ($row['Cc_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Agudeza Visual CC OD: </b>".$row['Cc_od']."</td></tr>";
    if ($row['Cc_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Agudeza Visual CC OS: </b>".$row['Cc_os']."</td></tr>";
	if ($row['Reflejos']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Reflejos: </b>".$row['Reflejos']."</td></tr>";
	// REFRACCION
	echo "<tr><td align=center colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>REFRACCION</b></font></td></tr>";
	if ($row['Ref_aut_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Automatizada OD: </b>".$row['Ref_aut_od']."</td></tr>";
    if ($row['Ref_aut_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Automatizada OS: </b>".$row['Ref_aut_os']."</td></tr>";	
	if ($row['Sin_d_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Subjetiva OD: </b>".$row['Sin_d_od']."</td></tr>";
    if ($row['Sin_d_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Subjetiva OS: </b>".$row['Sin_d_os']."</td></tr>";
	if ($row['Dilatado_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Dilatado OD: </b>".$row['Dilatado_od']."</td></tr>";
    if ($row['Dilatado_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Dilatado OS: </b>".$row['Dilatado_os']."</td></tr>";	
	if ($row['Usa_actual_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Usa Actualmente OD: </b>".$row['Usa_actual_od']."</td></tr>";
    if ($row['Usa_actual_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Usa Actualmente OS: </b>".$row['Usa_actual_os']."</td></tr>";
	if ($row['Queratom_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Queratometria OD: </b>".$row['Queratom_od']."</td></tr>";
    if ($row['Queratom_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Queratometria OS: </b>".$row['Queratom_os']."</td></tr>";	
	//GENERAL
	echo "<tr><td align=center colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>GENERAL</b></font></td></tr>";
	if ($row['Biomicro_od']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Biomicroscopia OD: </b>".$row['Biomicro_od']."</td></tr>";	
	if ($row['Biomicro_os']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Biomicroscopia OS: </b>".$row['Biomicro_os']."</td></tr>";	
	
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Presion Intraocular OD (mmHg): </b>".$row['Pi_od_mmhg']."</td></tr>";	
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Presion Intraocular OS (mmHg): </b>".$row['Pi_os_mmhg']."</td></tr>";	
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Gonioscopia OD: </b>".substr($row['Gonios_od'],4)."</td></tr>";	
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Gonioscopia OS: </b>".substr($row['Gonios_os'],4)."</td></tr>";	
	
	if ($row['Fondo_od']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fondo de Ojo OD: </b>".$row['Fondo_od']."</td></tr>";	
	if ($row['Fondo_os']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fondo de Ojo OS: </b>".$row['Fondo_os']."</td></tr>";	
	// OTROS
	echo "<tr><td align=center colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>OTROS</b></font></td></tr>";
	if ($row['Motilidad']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Motilidad: </b>".$row['Motilidad']."</td></tr>";	
	if ($row['Observaciones']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones: </b>".$row['Observaciones']."</td></tr>";	
	if ($row['Diagnosticos']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico: </b>".$row['Diagnosticos']."</td></tr>";	
	if ($row['Tratamiento']!='.') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Tratamiento: </b>".$row['Tratamiento']."</td></tr>";	
	if ($row['Plan_qx_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Plan Quirurgico OD </b>".$row['Plan_qx_od']."</td></tr>";
    if ($row['Plan_qx_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Plan Quirurgico OS: </b>".$row['Plan_qx_os']."</td></tr>";	
	if ($row['Paquimetria_od']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Paquimetria OD </b>".$row['Paquimetria_od']."</td></tr>";
    if ($row['Paquimetria_os']!='NO APLICA') 
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Paquimetria OS: </b>".$row['Paquimetria_os']."</td></tr>";	
	echo "</table>";
}

/**
 * Despliega en pantalla toda la información de un seguimiento, sacada de la tabla vlaser_000009
*/
function Seguimiento($seg){

	echo "</table><table align=center border=1 width=725 >";
	echo "<tr><td align=center colspan='2' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SEGUIMIENTO ".$seg['Fecha']."</b></font></td></tr>";
	echo "<tr><td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>EVOLUCION</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Evolucion']."</fieldset></TR>";
	echo "<td><font size=3 face='arial' >";
	echo "<table width=345 ALIGN=CENTER border=1 ><td width=60 align=center bgcolor='#FFEECC'><b>A.V</b></td>";
	echo "<td align=center width=142 bgcolor='#FFEECC'><b>OD</b></td><td align=center width=142 bgcolor='#FFEECC'><b>OS</b></td></tr>";
	echo "<tr><td align=center bgcolor='#FFEECC'><b>SC</b></td><td align=center>".$seg['Sc_od']."</td><td align=center>".$seg['Sc_os']."</td>";
	echo "<tr><td align=center bgcolor='#FFEECC'><b>CC</b></td><td align=center>".$seg['Cc_od']."</td><td align=center>".$seg['Cc_os']."</td></tr></table></tr>";
	echo "<tr><td colspan=2><font size=3 face='arial' ><B>Refraccion Subjetiva OD - AV: </b>".$seg['Ref_sub_od_av']."</td></tr>";
	echo "<tr><td colspan=2><font size=3 face='arial' ><B>Refraccion Subjetiva OS - AV: </b>".$seg['Ref_sub_os_av']."</td></tr>";
	echo "<tr><td colspan=2><font size=3 face='arial' ><B>Dx: </b>".$seg['Dx']."</td></tr>";
	echo "<tr><td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>EXAMEN FISICO</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Examen_fisico']."</fieldset>";
	echo "<td  align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>CONDUCTA</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Conducta']."</fieldset></TR>";
	echo "<tr><td width=362 align=left  bgcolor='#AADDFF'><font size='3' face='arial'><b>Presion OD mmHg</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Pr_od_mmhg']."</fieldset>";
	echo "<td  align=left   bgcolor='#AADDFF'><font size='3' face='arial'><b>Presion OI mmHg</b>";
	echo "<fieldset style='background-color: #FFFFFF; '>".$seg['Pr_oi_mmhg']."</fieldset></TR>";
}

/**
 * Busca en la BD la información de lentes recetados al paciente en la tabla vlaser_000004 segun el query
*/
function Lentes($lent){
		echo "</table><table align=center border=1 width=725 >";
		echo "<tr><td align=center colspan='2' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>LENTES ".$lent['Fecha']."</b></font></td></tr>";
		if ($lent['Esfera_od']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Esfera OD </b>".$lent['Esfera_od']."</td></tr>";
		if ($lent['Esfera_os']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Esfera OS </b>".$lent['Esfera_os']."</td></tr>";
	   	if ($lent['Cilindro_od']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cilindro OD </b>".$lent['Cilindro_od']."</td></tr>";
		if ($lent['Cilindro_os']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cilindro OS </b>".$lent['Cilindro_os']."</td></tr>";
		if ($lent['Eje_od']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Eje OD </b>".$lent['Eje_od']."</td></tr>";
		if ($lent['Eje_os']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Eje OS </b>".$lent['Eje_os']."</td></tr>";
	   	if ($lent['Add_od']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Add OD </b>".$lent['Add_od']."</td></tr>";
		if ($lent['Add_os']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Add OS </b>".$lent['Add_os']."</td></tr>";
		if ($lent['Especificaciones']!='.') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Especificaciones: </b>".$lent['Especificaciones']."</td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>PRUEBAS OPTOMETRICAS</b></font></td></tr>";
		if ($lent['Ref_sub_od']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Subjetiva OD </b>".$lent['Ref_sub_od']."</td></tr>";
		if ($lent['Ref_sub_os']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Refraccion Subjetiva OS </b>".$lent['Ref_sub_os']."</td></tr>";
	   	if ($lent['Lc_od']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>LC OD </b>".$lent['Lc_od']."</td></tr>";
		if ($lent['Lc_os']!='NO APLICA') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>LC OS </b>".$lent['Lc_os']."</td></tr>";
		if ($lent['Observaciones']!='.') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones: </b>".$lent['Observaciones']."</td></tr>";
		
}

/**
 * Busca en la BD la información de los medicamentos recetados al paciente en la tabla vlaser_ 000005 segun el query
*/
function Medicamentos($medic){
		echo "</table><table align=center border=1 width=725 >";
		echo "<tr><td align=center colspan='2' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>MEDICAMENTOS ".$medic['Fecha']."</b></font></td></tr>";
		if ($medic['Formula']!='.') 
			echo "<tr><td colspan=12><font size=3 face='arial' > ".$medic['Formula']."</td></tr>";	
}


/**
 * Busca en la BD la información de los EXAMENES ORDENADOS al paciente en la tabla vlaser_ 000006 segun el query
*/
 
function Examenes($examen){
		echo "</table><table align=center border=1 width=725 >";
		echo "<tr><td align=center colspan='2' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>EXAMENES ".$examen['Fecha']."</b></font></td></tr>";
		if ($examen['Examenes']!='.') 
			echo "<tr><td colspan=12><font size=3 face='arial' > ".$examen['Examenes']."</td></tr>";	
}


/**
 * Busca en la BD la información de las CIRUGIAS ORDENADAS al paciente en la tabla vlaser_ 000008 segun el query
*/
function Cirugia($cirug){
		echo "</table><table align=center border=1 width=725 >";
		echo "<tr><td align=center colspan='2' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ORDENES DE CIRUGIA ".$cirug['Fecha_actual']."</b></font></td></tr>";
		echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fecha de la Cirugia: </b>".$cirug['Fecha_cx']."</td></tr>";
		if ($cirug['Diagnostico']!='.') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico: </b>".$cirug['Diagnostico']."</td></tr>";	
		if ($cirug['Tratamiento']!='.') 
			echo "<tr><td colspan=12><font size=3 face='arial' ><B>Tratamiento: </b>".$cirug['Tratamiento']."</td></tr>";	
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
		echo "<tr><td align=center colspan=3><font color=#000066>VISUAL LASER S.A.S.</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";
		/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
		$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'vlaser' AND codigo = '002'  order by Descripcion ";
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
			//$query="select DISTINCT Paciente from vlaser_000003 where Oftalmologo='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";V1.03
			$query="select DISTINCT Paciente from vlaser_000003 where  Paciente like '%".$pac1."%' order by Paciente";
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
			$query = "select Fecha  from vlaser_000003 where Paciente='".$pac."' ";//and Oftalmologo='".$medico."' "; V 1.03
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
		$querya="select * from vlaser_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
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
	    
		// Traer historia
			
		    $query="select * from vlaser_000003 where Paciente='".$pac."'  and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
			$err = mysql_query($query,$conex);
			$numy = mysql_num_rows($err);
			if($numy>0)	{
				$row = mysql_fetch_array($err);
				Historia($row);
			}
				//Busqueda de los seguimientos entre la fecha de una historia y otra o entre la fecha de la historia y la fecha2
				//Definición del query de busqueda para los elementos asociados a la historia
			

		//INFORMACIÓN DE LOS SEGUIMIENTOS
									
			$query="select * from	vlaser_000009 where Paciente='".$pac."' and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
			$err2 = mysql_query($query,$conex);
			$nums = mysql_num_rows($err2);
				if($nums>0)	{
					for ($x=0;$x<$nums;$x++) {
						$seg = mysql_fetch_array($err2);
						Seguimiento($seg);
						}
				   }
						

			
						//Impresión en Pantalla
					//	Impresion($A,$p);
					
				
				
				echo "</table>";
				
		//INFORMACIÓN DE LOS LENTES
									
			$query1="select * from	vlaser_000004 where Paciente='".$pac."' and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
			$err3 = mysql_query($query1,$conex);
			$nums1 = mysql_num_rows($err3);
				if($nums1>0)	{
					for ($x=0;$x<$nums1;$x++) {
						$lent = mysql_fetch_array($err3);
						Lentes($lent);
						}
				   }
						

			echo "</table>";
	
		//INFORMACIÓN DE LOS MEDICAMENTOS
									
			$query2="select * from	vlaser_000005 where Paciente='".$pac."' and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
			$err4 = mysql_query($query2,$conex);
			$nums2 = mysql_num_rows($err4);
				if($nums2>0)	{
					for ($x=0;$x<$nums2;$x++) {
						$medic = mysql_fetch_array($err4);
						Medicamentos($medic);
						}
				   }
						

			echo "</table>";
	
		//INFORMACIÓN DE LOS EXAMENES
									
			$query3="select * from	vlaser_000006 where Paciente='".$pac."' and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
			$err5 = mysql_query($query3,$conex);
			$nums3 = mysql_num_rows($err5);
				if($nums3>0)	{
					for ($x=0;$x<$nums3;$x++) {
						$examen = mysql_fetch_array($err5);
						Examenes($examen);
						}
				   }
						

			echo "</table>";
			
	//INFORMACIÓN DE LAS ORDENES DE CIRUGIA
									
			$query4="select * from	vlaser_000008 where Paciente='".$pac."' and Fecha_actual between '".$fecha1."' and '".$fecha2."' order by Fecha_actual";
			$err6 = mysql_query($query4,$conex);
			$nums4 = mysql_num_rows($err6);
				if($nums4>0)	{
					for ($x=0;$x<$nums4;$x++) {
						$cirug = mysql_fetch_array($err6);
						Cirugia($cirug);
						}
				   }
						

			echo "</table>";
	} // todos los datos estan set

	include_once("free.php");
}?>