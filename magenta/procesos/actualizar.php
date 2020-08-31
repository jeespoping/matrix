<html>
<head>
<title>AFINIDAD</title>
</head>
<body >
<?php
include_once("conex.php");

/**
 * PAGINA DE ACTUALIZACIONDE AFINIDAD
 * 
 * Muestra en pantalla la información del cliente para su posterior actualizacion mediante el include proceso_actualizar.php, utilizada por todo la clínica
 * Primero pide la contraseña del usuario, despues la valida y pinta el formulario que despliega los datos existentes y permite modificar los mismos.
 * 
 * @name matrix\magenta\procesos\actualizar.php
 * @author Ing. Ana María Betancur Vargas
 * @created 2005-12-07
 * @version 2007-01-22
 * 
 * @modified 2006-01-27  refinamiento del script, Carolina Castaño
 * @modified 2007-05-19  se adecua para que muestre un alert al usuario tra presionar el boton recargar, Carolina Castaño
 * @modified 2007-01-22  se adecua para que permita almacenar los usuarios nacionales e internacionales, Carolina Castaño
 * 
 * @table magenta_00007 select
 * 
 * @wvar $acompa , acompañante del paciente en la ultima visita
 * @wvar $afiliado, entidad responsable del ultimo ingreso 
 * @wvar $afiliado1, permite ingresar parte del afiliado para facilitar el select
 * @wvar $afiliadoSelect, select de empresas responsables de factura
 * @wvar $anoNac, año de nacimiento del paciente
 * @wvar $ape1, apellido del paciente
 * @wvar $ape1Fam, vector de apellido uno de los familiares del afin
 * @wvar $ape2, segundo apellido del paciente
 * @wvar $ape2Fam, vector de apellido dos de los familiares del afin
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
 * @wvar $chk, esta señelado un familiar para ser ingresado
 * @wvar $codigo, clave del usuario para realizar una actualizacion
 * @wvar $color, color para deplegar mensajes y permitir acciones dependiendo del tipo de usuario que es el paciente
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
 * @wvar $diaNac, dia de nacimiento del paciente
 * @wvar $dir, direccion del paciente
 * @wvar $doc, documento de identidad del paciente
 * @wvar $docFam, vector de documentos de los familiares ingresados
 * @wvar $egr, indica de donde sale la informacion mas actualizada, de que base de datos
 * @evar $egr1, guarda a matrix por defecto
 * @wvar $email1, email 1 del paciente
 * @wvar $email2, email 2 del paicente
 * @wvar $estrato, estrato del paciente
 * @wvar $estCivil, estado civil del paciente
 * @wvar $estCivilSelect, select para el estado civil del paciente
 * @wvar $exp, para llenar los explodes
 * @wvar $fecAct la fecha con datos mas recientes en las diferentes fuentes (matrix, aymov, impac, inpaci)
 * @wvar $fecFam,vector de fecha de nacimiento de familiares
 * @wvar $fecNac, fecha de nacimiento			
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
 * @wvar $lugNac1, para poner parte del lugar de nacimiento para facilitar el select
 * @wvar $lugNacSelect, select de lugar de nacimiento
 * @wvar $mesNac, mes de nacimeinto del paciente
 * @wvar $miembros, numero de familiares del paciente
 * @wvar $movil, movil del paciente
 * @wvar $municipio,  municipio donde vive el pacinete
 * @wvar $municipio1, para poner parte del municipio donde vive el pacinete para facilitar el select
 * @wvar $municipioSelect, select de municipio donde vive el pacinete
 * @wvar $nHijos, numero de hijos
 * @wvar $nom1Fam vector de nombre 1 de los familiares del paciente
 * @wvar $nom2Fam vector de nombre 2 de los familiares del paciente
 * @wvar $nombres, nombre del paciente
 * @wvar $pais, pais del paciente
 * @wvar $planC, para poner parte de la eps del paciente para facilitar el select
 * @wvar $planC1, eps del paciente
 * @wvar $planCSelect, select eps del paciente
 * @wvar $prof, profesion del paciente
 * @wvar $prof1,  para poner parte de la profesion del paciente para facilitar el select
 * @wvar $profSelect, select de profesion del paciente
 * @wvar $rel vector de relaciones con los familiares ingresados
 * @wvar $requer, requerimientos
 * @wvar $requerAnt, requerimientos anteriormente ingresados
 * @wvar $servicio, servicio mas utilizado por el paciene
 * @wvar $servicioSelect, select de servicio mas utilizado por el paciene
 * @wvar $sexo, sexo del paciente
 * @wvar $sexoSelect, select del sexo del paciente
 * @wvar $tel1, telefono 1
 * @wvar $tel2, telefono 2
 * @wvar $tipDoc, tipo de documento del paciente
 * @wvar $tipDocFamSelect, select del tipo de documento de los familiares del paciente
 * @wvar $tipDocSelect, select del tipo de documento 
 * @wvar $tipUsu, tipo de usuario
 * @wvar $tipUsua, tipo de usuario al que se quiere convertir
 * @wvar $tipUsuSelect, select del tipo de usuario 
 * @wvar $usuario, codigo de la persona que esta diligenciado el sistema, 
 * @wvar $zona, zona donde vive el paciente
 * @wvar $zona, Select de zonas  donde vive el paciente
 * 
 */
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
						-Se cambia encabezado con ultimo formato.
*************************************************************************************************************************/
$wautor="Ana Maria Betancur";
$wmodificado="Carolina Castaño";
$wversion='2007-01-22';
$wactualiz='2016-05-06';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if (strpos($user, "-") > 0)
	$usuario = substr($user, (strpos($user, "-") + 1), strlen($user));
	
	//muestra la version del programa
/*	echo "<table align='center'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Modificado por: ".$wmodificado."</font></td>" ;
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br>" ;
	//echo "usuario:",$usuario;
*/
	include_once("root/comun.php"); 	
	////////////////////////////////////////////////encabezado general///////////////////////////////////
    $titulo = "SISTEMA AAA";
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz,"clinica");  

	if (!isset($usuario))
	{
		echo"<form action='' method='post' name='form1'><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
		echo "<tr><td colspan='2'><font size=3 color='#000080' face='arial'><b>Por favor ingrese su usuario y contraseña para autorizar la transacción</td><tr>";
		echo "<tr><td colspan='2'><font size=3 color='#000080' face='arial'><b>Usuario:<input type='text' name='usuario'></td></tr>";
		echo "<tr><td colspan='2'><font size=3 color='#000080' face='arial'><b>Contraseña:<input type='password' name='codigo'></td></tr>";

		echo "<input type='hidden' name='doc' value='$doc'>";
		echo "<input type='hidden' name='tipUsu' value='$tipUsu'>";
		echo "<input type='hidden' name='tipDoc' value='$tipDoc'>";
		echo "<input type='hidden' name='fecAct' value='$fecAct'>";
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
		echo "<input type='hidden' name='requerAnt' value='$requerAnt'>";
		echo "<input type='hidden' name='egr' value='$egr'>";
		echo "<input type='hidden' name='egr1' value='$egr1'>";
		echo "<input type='hidden' name='color' value='$color'>";
		echo "<input type='hidden' name='cco' value='$cco'>";

		echo "<tr><td colspan='2' align='center'><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";
		echo "</table></fieldset></form>";

	}
	else
	{

		/**
		 * conexion con matrix
		 */
		

		

		$query="Select tipusua from magenta_000007 where Codigo_nomina='$usuario'";
		//echo $query;

		$err=mysql_query($query);
		//echo mysql_error();
		$num=mysql_num_rows($err);
		if  ($num <=0)
		{
			echo"<form action='Magenta.php' method='post' name='form1' enctype='multipart/form-data'><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
			echo "<tr><td colspan='2'><font size=3 color='#000080' face='arial'><b>Según la información ingresada, usted no tiene autorización para actualizar los datos del cliente, por favor vuelva e intentelo de nuevo.</td><tr>";
			echo "<input type='hidden' name='doc' value='$doc'>";
			echo "<input type='hidden' name='tipDoc' value='$tipDoc'>";

			echo "<tr><td colspan='2' align='center'><input type='submit' name='aceptar' value='VOLVER'></td><tr>";
			echo "</table></fieldset></form>";

		}
		else
		{
			$resulta = mysql_fetch_row($err);
			$tipUsua = $resulta[0];

			//echo "tipusu:",$tipUsu;

			if (($tipUsua== '03-normal' and ((substr($tipUsu,0,2)=='00')or(substr($tipUsu,0,2)=='01') )) or ($tipUsua!='01-comunic' and substr($tipUsu,0,3)=='VIP') or ($tipUsua!='04-internacionales' and (substr($tipUsu,0,3)=='NAL' or substr($tipUsu,0,3)=='INT')))
			{
				echo"<form action='Magenta.php' method='post' name='form1' enctype='multipart/form-data'><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
				echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
				echo "<tr><td colspan='2'><font size=3 color='#000080' face='arial'><b>Según la información ingresada, usted no tiene autorización para actualizar los datos del cliente.</td><tr>";
				echo "<input type='hidden' name='doc' value='$doc'>";
				echo "<input type='hidden' name='tipDoc' value='$tipDoc'>";

				echo "<tr><td colspan='2' align='center'><input type='submit' name='aceptar' value='VOLVER'></td><tr>";
				echo "</table></fieldset></form>";
			}else
			{


				/**
				 * include que procesa la info para actualizarla
				 */
				include_once("magenta/proceso_actualizar.php");
				/*	//////////////////////////////////////////////////////////////////
				*   	IMPRESIÓN DE LA INFORMACIÓN DEL 	*
				*	CLIENTE EN PANTALLA			*
				*********************************************************************/

				/*Impresión de la Información personal básica*/


				//echo "<form action='' method='post' name='actualizar'>";
				echo "<p><center>";//<fieldset style='border:solid;border-color:$color; width=550' ; color=#000080>
				echo "<table border='0' width='550' align='center'>";
				echo "<form method='post' action='Magenta.php'>";
				echo "<input type='hidden' name='doc' value='$doc'>";
				echo "<input type='hidden' name='cco' value='$cco'>";
				echo "<input type='hidden' name='tipDoc' value='$tipDoc'>";
				echo  "<tr><td align='center'><font size='2'  face='arial'><input type='submit' name='volver' value='VOLVER' ></td></form>";
				echo "<form method='post' action='Magenta.php'>";
				echo "<td align='center'><font size='2'  face='arial'><input type='submit' name='inicio' value='INICIO' ></td></tr></form>";

				echo "</table></p>";


				echo "<form action='actualizar.php' enctype='multipart/form-data' method='post'>";
				echo "<input type='hidden' name='usuario' value='$usuario'>";
				echo "<p><center><fieldset style='border:solid;border-color:$color; width=550' ; color=#000080><table border='1' width='550' align='center'>";
				echo "<tr><td colspan='2'><font size=3  face='arial' ><b>INFORMACIÓN BÁSICA DEL CLIENTE</b></TD></TR>";
				echo "<tr><td colspan='2'><font size=2  face='arial'><b>Documento:</b>&nbsp;<input type='text' name='doc' value='$doc'></td></tr>";

				echo "<tr><td colspan='2'><font size=2  face='arial'><b>Tipo Documento:</b>&nbsp;<select name='tipDoc'>".$tipDocSelect."</select></td></tr>";

				echo "<tr><td colspan='2'><font size=2  face='arial'><b>Nombres:</b>&nbsp;<input type='text' name='nombres' value='$nombres' size=15 maxlength=80></td></tr>";
				echo "<tr><td ><font size=2  face='arial'><b>Primer Apellido:&nbsp;</b><input type='text' name='ape1' value='$ape1' size=20 maxlength=80></td>";
				echo "<td><font size=2  face='arial'><b> Segundo Apellido:</b>&nbsp;<input type='text' name='ape2' value='$ape2' size=20 maxlength=80></td></tr>";

				echo "<tr><td colspan='2'><font size=2  face='arial'><b>Fecha Nacimiento:&nbsp;</b>";
				echo "<input type='text' name='anoNac' value='$anoNac'  size=4 maxlength=4>AAAA&nbsp;&nbsp;";
				echo " <input type='text' name='mesNac' value='$mesNac' size=2 maxlength=2>MM &nbsp;&nbsp;";
				echo "<input type='text' name='diaNac' value='$diaNac' size=2 maxlength=2>DD</td></tr>";

				echo "<tr><td  colspan='2'><font size=2  face='arial'><b>Lugar Nacimiento:&nbsp;</b><select name='lugNac'>$lugNacSelect</select>";
				echo "<input type='text' name='lugNac1' value='$lugNac1' size=10 maxlength=10></td></tr>";

				echo  "<tr><td><font size=2  face='arial'><b>Sexo:</b>&nbsp;<select name='sexo'>$sexoSelect</select></td>";
				echo "<td ><font size=2  face='arial'><b>N hijos:&nbsp;</b><input type='text' name='nHijos' value='$nHijos' size=2 maxlength=2></td></tr>";
				echo  "<td colspan='2'><font size=2  face='arial'><b>Estado Civil:</b>&nbsp;<select name='estCivil'>$estCivilSelect</select></td>";

				echo "<tr><td colspan='2' ><font size=2  face='arial'><b>Profesión:&nbsp;</b><select name='prof'>$profSelect</select>";
				echo "<input type='text' name='prof1' value='$prof1' size=10 maxlength=10></td></tr>";

				echo  "<tr><td colspan='2'><font size=2  face='arial'><b>Acompañante:</b>&nbsp;<input type='text' name='acompa' value='$acompa' size=30 maxlength=80></td></tr>";

				echo "<tr><td  colspan='2'><font size=2  face='arial'><b>Entidad Resp.:&nbsp;</b><select name='afiliado'>$afiliadoSelect</select>";
				echo "<input type='text' name='afiliado1' value='$afiliado1' size=10 maxlength=10></td></tr>";//</table></fieldset>";

				echo "<tr><td  colspan='2'><font size=2  face='arial'><b>EPS:&nbsp;</b><select name='planC'>$planCSelect</select>";
				echo "<input type='text' name='planC1' value='$planC1' size=10 maxlength=10></td></tr>";//</table></fieldset>";

				echo "</table></fieldset></p>";

				echo "<p><center><fieldset style='border:solid;border-color:$color; width=550' ; color=#000080><table border='1' width='550' align='center'>";
				echo "<tr><td  colspan='3'><font size='3'  face='arial'><b>INFORMACIÓN DE CONTACTO</b></td></tr>";
				echo  "<tr><td colspan='2'><font size=2  face='arial'><b>Tel. casa:</b>&nbsp;<input type='text' name='tel1' value='$tel1' size=15 maxlength=80></select></td>";
				echo "<td ><font size=2  face='arial'><b>Tel oficina:&nbsp;</b><input type='text' name='tel2' value='$tel2' size=15 maxlength=80></td></tr>";
				echo "<tr><td  colspan='3'><font size=2  face='arial'><b>Móvil:&nbsp;</b><input type='text' name='movil' value='$movil' size=30 maxlength=80></td></tr>";
				echo "<tr><td colspan='2' ><font size=2  face='arial'><b>email 1:&nbsp;</b><input type='text' name='email1' value='$email1' size=30 maxlength=80></td>";
				echo "<td colspan='1' ><font size=2  face='arial'><b>email 2:&nbsp;</b><input type='text' name='email2' value='$email2' size=30 maxlength=80></td></tr>";

				echo  "<tr><td colspan='2'><font size=2  face='arial'><b>Estrato:</b>&nbsp;<input type='text' name='estrato' value='$estrato' size=3 maxlength=1></td>";
				echo  "<td><font size=2  face='arial'><b>Zona:</b>&nbsp;<select name='zona'>$zonaSelect</select></td></tr>";

				echo "<tr><td  colspan='2'><font size=2  face='arial'><b>Dirección:&nbsp;</b><input type='text' name='dir' value='$dir' size=30 maxlength=80></td>";
				echo  "<td><font size=2  face='arial'><b>País:</b>&nbsp;<select name='pais'>$paisSelect</select></td></tr>";

				echo "<tr><td  colspan='3'><font size=2  face='arial'><b>Lugar De Residencia:&nbsp;</b><select name='municipio'>$municipioSelect</select>";
				echo "<input type='text' name='municipio1' value='$municipio1' size=10 maxlength=10></td></tr>";

				echo "<tr><td colspan='3'><font size=2  face='arial'><b>PREFERENCIA DE CONTACTO</b> Donde 1 es la forma preferida de contacto.</td></tr>";

				echo "<tr><td ><font size='2'  face='arial'><b>Tel 1</b> <input type='text' name='llamtel1' value='$llamtel1' size='2' maxlength='1'></td>";
				echo "<td ><font size='2' face='arial'><b>Tel 2</b> <input type='text' name='llamtel2' value='$llamtel2' size='2' maxlength='1'></td>";
				echo "<td ><font size='2' face='arial'><b>Móvil</b> <input type='text' name='llammovi' value='$llammovi' size='2' maxlength='1'></td></tr>";

				echo "<tr><td colspan='2'><font size='2'  face='arial'><b>Email</b> <input type='text' name='llamemai' value='$llamemai' size='2' maxlength='1'</td>";
				echo "<td ><font size='2' face='arial'><b>Correo Directo</b> <input type='text' name='llamdire' value='$llamdire' size='2' maxlength='1'></td></tr>";


				echo "</table></fieldset></p>";

				if (substr ($tipUsu,-1)!='2' or substr ($tipUsu,0,3)=='VIP')
				{
					echo "<p><fieldset  style='border:solid;border-color:$color; width=550'><table border=1 width=550";
					echo "<tr><td colspan='5'><font size='3'  face='arial'><b>GUSTOS</b></td></tr>";

					echo "<tr><td colspan='2'><font size='2'  face='arial' ><b>Lectura</b> <input type='checkbox' name='gusLec' $cgusLec></td>";
					echo "<td colspan='3'><font size='2'  face='arial' ><b>Artes plasticas</b> (pintura, escultura...)<input type='checkbox' name='gusApl' $cgusApl></td></tr>";

					echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Cine</b> <input type='checkbox' name='gusCin' $cgusCin></td>";
					echo "<td colspan='1'><font size=2  face='arial' ><b>Música</b> <input type='checkbox' name='gusMus' $cgusMus></td>";
					echo "<td colspan='2')><font size=2  face='arial' ><b>Artes Dramáticas</b> (Teatro y sus variaciones...)<input type='checkbox' name='gusAdr' $cgusAdr></td></tr>";

					echo "<tr><td colspan='5'><font size=2  face='arial'><b>DEPORTE</b></td></tr>";

					echo "<tr><td size=100><font size=2  face='arial' ><b>Fútbol</b> <input type='checkbox' name='depFut' $cdepFut></td>";
					echo "<td size=100><font size=2  face='arial' ><b>Tennis</b> <input type='checkbox' name='depTen' $cdepTen></td>";
					echo "<td size=100><font size=2  face='arial' ><b>GYM</b> <input type='checkbox' name='depGym' $cdepGym></td>";
					echo "<td size=100><font size=2  face='arial' ><b>Golf</b> <input type='checkbox' name='depGol' $cdepGol></td>";
					echo "<td size=100><font size=2  face='arial' ><b>Equitación</b> <input type='checkbox' name='depEqu' $cdepEqu></td></tr>";
					echo "<td colspan=150><font size=2  face='arial'><b>Otros Gustos/Deportes: </b> <input type='text' name='depOtr' value='$depOtr' size=50 maxlength=80></td></tr>";
					echo "</table></fieldset></p>";


					echo "<p><fieldset  style='border:solid;border-color:$color; width=550'><table border=1 width=550>";
					echo "<tr><td colspan='3'><font size='3'  face='arial'><b>INFORMACIÓN MAGENTA</b></td></tr>";

					echo "<tr><td colspan='4'><font size='2'  face='arial'><b>COMUNIDADES</b></td></tr>";
					echo "<tr><td ><font size='2'  face='arial'><b>Bebes</b> <input type='checkbox' name='comBeb' $ccomBeb></td>";
					echo "<td ><font size='2' face='arial'><b>Familias</b> <input type='checkbox' name='comFam' $ccomFam></td>";
					echo "<tr><td><font size='2'  face='arial'><b>Adolecentes</b> <input type='checkbox' name='comAdo' $ccomAdo></td>";
					echo "<td ><font size='2' face='arial'><b>Adultos</b> <input type='checkbox' name='comAdu' $ccomAdu></td></tr>";
					echo "<tr><td colspan='4'><font size=2  face='arial'><b>PRINCIPALES CAUSAS DE ENFERMEDAD</b></td></tr>";
					echo "<tr><td ><font size='2' face='arial' ><b>Sistema Nervioso</b> <input type='checkbox' name='comSnc' $ccomSnc></td>";
					echo "<td colspan='2'><font size='2' face='arial' ><b>Sistema Respiratorio</b> <input type='checkbox' name='comSrs' $ccomSrs></td></tr>";
					echo "<tr><td ><font size='2' face='arial' ><b>Sistema Renal</b> <input type='checkbox' name='comSrn' $ccomSrn></td>";
					echo "<td colspan='2'><font size='2' face='arial' ><b>Sistema Cardiovascular</b> <input type='checkbox' name='comCar' $ccomCar></td></tr>";
					echo "<tr><td colspan='4'><font size='2' face='arial'><b>Sistema Musculoesquelético</b> <input type='checkbox' name='comSmu' $ccomSmu></td></tr>";
					echo "<tr><td colspan='4'><font size='2' face='arial'><b>Otras Enfermedades:</b> <input type='text' name='comOtr' value='$comOtr' size=30 maxlength=80></td></tr>";

				}else
				{
					echo "<input type='hidden' name='gusLec' value='$cgusLec'>";
					echo "<input type='hidden' name='gusApl' value='$cgusApl>";
					echo "<input type='hidden' name='gusCin' value='$cgusCin'>";
					echo "<input type='hidden' name='gusMus' value='$cgusMus'>";
					echo "<input type='hidden' name='gusAdr' value='$cgusAdr'>";
					echo "<input type='hidden' name='depFut' value='$cdepFut'>";
					echo "<input type='hidden' name='depTen' value='$cdepTen'>";
					echo "<input type='hidden' name='depGol' value='$cdepGol'>";
					echo "<input type='hidden' name='depGym' value='$cdepGym'>";
					echo "<input type='hidden' name='depEqu' value='$cdepEqu'>";
					echo "<input type='hidden' name='depOtr' value='$depOtr'>";
					echo "<input type='hidden' name='comBeb' value='$ccomBeb'>";
					echo "<input type='hidden' name='comFam' value='$ccomFam'>";
					echo "<input type='hidden' name='comAdo' value='$ccomAdo'>";
					echo "<input type='hidden' name='comAdu' value='$ccomAdu'>";
					echo "<input type='hidden' name='comSnc' value='$ccomSnc'>";
					echo "<input type='hidden' name='comSrn' value='$ccomSrn'>";
					echo "<input type='hidden' name='comSrs' value='$ccomSrs'>";
					echo "<input type='hidden' name='comCar' value='$ccomCar'>";
					echo "<input type='hidden' name='comSmu' value='$ccomSmu'>";
					echo "<input type='hidden' name='comOtr' value='$comOtr'>";

					echo "<p><fieldset  style='border:solid;border-color:$color; width=550'><table border=1 width=550>";
				}

				echo "<tr><td colspan='3'><font size='2' face='arial'><b>INFORMACIÓN ESPECIAL</b></td></tr>";
				echo "<tr><td colspan='3'><font size='2'  face='arial'><b>Servicio mas usado:</b>&nbsp;<select name='servicio'>$servicioSelect</select></td></tr>";

				if ($tipUsua=='02-magenta' or $tipUsua=='01-comunic' )
				{
					echo  "<tr><td ><font size='2' face='arial'><b>Tipo de Usuario:</b>&nbsp;<select name='tipUsu'>$tipUsuSelect</select></td>";

				}else if ($tipUsua!='04-internacionales')
				{
					echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Tipo de Usuario:</b> $tipUsu</td></tr>";
					echo "<input type='hidden' name='tipUsu' value='".$tipUsu."'>";
				}

				if ($tipUsua=='04-internacionales')
				{
					if ( substr ($tipUsu,-1)=='1' or substr ($tipUsu,-1)=='2' )
					{
						echo "<tr><td colspan='2'><font size=2  face='arial' ><b>Tipo de Usuario:</b> $tipUsu</td></tr>";
						echo "<input type='hidden' name='tipUsu' value='".$tipUsu."'>";
					}
					else 
					{
						echo  "<tr><td ><font size='2' face='arial'><b>Tipo de Usuario:</b>&nbsp;<select name='tipUsu'>$tipUsuSelect2</select></td>";
					}
				}
				echo "<td colspan='2' Rowspan='2'><font size=2  face='arial' ><b>Nuevos Requerimientos</b><br>";
				echo '<textarea cols="40" name="requer" style="font-family: Arial; font-size:14">'.$requer.'</textarea></td>';
				echo "<tr><td colspan='1'><font size=2  face='arial' ><b>Requerimientos Anteriores:</b><br>";
				echo "<font size=2  face='arial'>".$requerAnt."</td>";
				echo "<input type='hidden' name='requerAnt' value='".$requerAnt."'>";
				echo "</table></fieldset></p>";


				echo "<p><fieldset  style='border:solid;border-color:$color; width=550'><table border=1 width=550";
				echo "</table></fieldset></p>";


				/*Impresión de los datos familiares*/
				$exp2=explode('-', $tipUsu);
				if ($exp2[0]!='NAL' and $exp2[0]!='INT')
				{
					echo "<p><center><fieldset style='border:solid;border-color:$color; width=550' ; color=#000080><table border='1' width='550' align='center'>";
					echo "<tr><td colspan='2'><font size=3  face='arial' ><b>FAMILIARES RELACIONADOS</b></TD></TR>";
					for($i=0;$i<$miembros;$i++)
					{
						$exp=explode('-', $fecFam[$i]);
						$paro=0;

						if (isset ($exp[0]) and isset ($exp[1]) and isset ($exp[2]))
						{

							if (strlen($exp[0])>4 or $exp[0]>date('Y') or strlen($exp[1])>2 or $exp[1]>12 or strlen($exp[2])>2 or $exp[2]>31)
							{
								$paro=1;
							}
						}
						else
						{
							$paro=1;
						}


						if(isset($chk[$i]) and  $chk[$i]== "on" and $docFam[$i]!='' and $paro!=1)
						{
							echo "<tr><td colspan='2'><font size='2'  face='arial' ><input type='checkbox' name='chk[$i]' checked><b>FAMILIAR  $i.&nbsp; Parentesco:&nbsp</b>";
							echo "<input type='text' name='rel[$i]' value=".ucfirst($rel[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Nombre1:&nbsp;";
							echo "<input type='text' name='nom1Fam[$i]' value=".ucfirst($nom1Fam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Nombre2:&nbsp;<input type='text' name='nom2Fam[$i]' value=".ucfirst($nom2Fam[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Apellido1:&nbsp;";
							echo "<input type='text' name='ape1Fam[$i]' value=".ucfirst($ape1Fam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Apellido2:&nbsp;<input type='text' name='ape2Fam[$i]' value=".ucfirst($ape2Fam[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Documento:&nbsp;";
							echo "<input type='text' name='docFam[$i]' value=".ucfirst($docFam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Tipo Doc:&nbsp;<select name='tipDocFam[$i]'>".$tipDocFamSelect[$i]."</select></td></tr>";

							echo "<tr><td colspan=2><font size='2'  face='arial' ><b>Fecha de nacimiento (aaaa-mm-dd):&nbsp;";
							echo "<input type='text' name='fecFam[$i]' value=".$fecFam[$i]."></td></tr>";

						}
						else if(isset($chk[$i]) and  $chk[$i]== "on" and $docFam[$i]=='')
						{
							echo '<script language="Javascript">';
							echo 'alert ("El familiar ingresado, no puede ser almacenado sin Documento de Identidad")';
							echo '</script>';

							echo "<tr><td colspan='2'><font size='2'  face='arial' ><input type='checkbox' name='chk[$i]' checked><b>FAMILIAR  $i.&nbsp; Parentesco:&nbsp</b>";
							echo "<input type='text' name='rel[$i]' value=".ucfirst($rel[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Nombre1:&nbsp;";
							echo "<input type='text' name='nom1Fam[$i]' value=".ucfirst($nom1Fam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Nombre2:&nbsp;<input type='text' name='nom2Fam[$i]' value=".ucfirst($nom2Fam[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Apellido1:&nbsp;";
							echo "<input type='text' name='ape1Fam[$i]' value=".ucfirst($ape1Fam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Apellido2:&nbsp;<input type='text' name='ape2Fam[$i]' value=".ucfirst($ape2Fam[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Documento:&nbsp;";
							echo "<input type='text' name='docFam[$i]' value=".ucfirst($docFam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Tipo Doc:&nbsp;<select name='tipDocFam[$i]'>".$tipDocFamSelect[$i]."</select></td></tr>";

							echo "<tr><td colspan=2><font size='2'  face='arial' ><b>Fecha de nacimiento (aaaa-mm-dd):&nbsp;";
							echo "<input type='text' name='fecFam[$i]' value=".$fecFam[$i]."></td></tr>";
							$paro=1;

						}
						else if(isset($chk[$i]) and  $chk[$i]== "on" and $docFam[$i]!='' and $paro==1)
						{
							echo '<script language="Javascript">';
							echo 'alert ("La fecha de nacimiento del familiar no cumple con el fotmato: aaa-mm-dd, por lo que no ha sido ingresado")';
							echo '</script>';

							echo "<tr><td colspan='2'><font size='2'  face='arial' ><input type='checkbox' name='chk[$i]' checked><b>FAMILIAR  $i.&nbsp; Parentesco:&nbsp</b>";
							echo "<input type='text' name='rel[$i]' value=".ucfirst($rel[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Nombre1:&nbsp;";
							echo "<input type='text' name='nom1Fam[$i]' value=".ucfirst($nom1Fam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Nombre2:&nbsp;<input type='text' name='nom2Fam[$i]' value=".ucfirst($nom2Fam[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Apellido1:&nbsp;";
							echo "<input type='text' name='ape1Fam[$i]' value=".ucfirst($ape1Fam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Apellido2:&nbsp;<input type='text' name='ape2Fam[$i]' value=".ucfirst($ape2Fam[$i])."></td>";

							echo "<tr><td><font size='2'  face='arial' ><b>Documento:&nbsp;";
							echo "<input type='text' name='docFam[$i]' value=".ucfirst($docFam[$i])."></td>";
							echo "<td><font size='2'  face='arial' ><b>Tipo Doc:&nbsp;<select name='tipDocFam[$i]'>".$tipDocFamSelect[$i]."</select></td></tr>";

							echo "<tr><td colspan=2><font size='2'  face='arial' ><b>Fecha de nacimiento (aaaa-mm-dd):&nbsp;";
							echo "<input type='text' name='fecFam[$i]' value=".$fecFam[$i]."></td></tr>";

						}

					}
				}

				$exp=explode("-",$tipUsu);
				if ($exp[2]!='2' and $exp[0]!='NAL' and $exp[0]!='INT' and (!isset($paro) or $paro!=1))
				{

					/*Fin Información familia*/
					echo "<tr><td colspan='2'><font size='2'  face='arial' ><input type='checkbox' name='chk[$miembros]' ><b>FAMILIAR  $miembros.&nbsp; Relación:&nbsp</b>";
					echo "<input type='text' name='rel[$miembros]' ></td>";

					echo "<tr><td><font size='2'  face='arial' ><b>Nombre1:&nbsp;";
					echo "<input type='text' name='nom1Fam[$miembros]' ></td>";
					echo "<td><font size='2'  face='arial' ><b>Nombre2:&nbsp;<input type='text' name='nom2Fam[$miembros]' ></td>";

					echo "<tr><td><font size='2'  face='arial' ><b>Apellido1:&nbsp;";
					echo "<input type='text' name='ape1Fam[$miembros]' ></td>";
					echo "<td><font size='2'  face='arial' ><b>Apellido2:&nbsp;<input type='text' name='ape2Fam[$miembros]' ></td>";

					echo "<tr><td><font size='2'  face='arial' ><b>Documento:&nbsp;";
					echo "<input type='text' name='docFam[$miembros]' ></td>";
					echo "<td><font size='2'  face='arial' ><b>Tipo Doc:&nbsp;<select name='tipDocFam[".$miembros."]'>".$tipDocSelect."</select></td>";

					echo "<tr><td colspan=2><font size='2'  face='arial' ><b>Fecha de nacimiento (aaaa-mm-dd):&nbsp;";
					echo "<input type='text' name='fecFam[$miembros]' ></td></tr>";

				}
				echo "<input type='hidden' name='miembros' value='$miembros'>";
				echo "<input type='hidden' name='egr' value='$egr'>";
				echo "<input type='hidden' name='egr1' value='$egr1'>";
				echo "<input type='hidden' name='cco' value='$cco'>";

				echo  "<tr><td><font size='2'  face='arial'><b>Datos Completos:</b>&nbsp;<input type='checkbox' name='actualizar' ></td>";
				echo "<td ><font size='2'  face='arial'><input type='submit' name='aceptar' value='ACTUALIZAR' ></td></tr></form>";

				echo "</table></fieldset></p>";

				echo "<p><center>";//<fieldset style='border:solid;border-color:$color; width=550' ; color=#000080>
				echo "<table border='0' width='550' align='center'>";
				echo "<form method='post' action='Magenta.php'>";
				echo "<input type='hidden' name='doc' value='$doc'>";
				echo "<input type='hidden' name='tipDoc' value='$tipDoc'>";
				echo "<input type='hidden' name='cco' value='$cco'>";

				echo  "<tr><td align='center'><font size='2'  face='arial'><input type='submit' name='aceptar' value='VOLVER' ></td></form>";
				echo "<form method='post' action='Magenta.php'>";
				echo "<td align='center'><font size='2'  face='arial'><input type='submit' name='aceptar' value='INICIO' ></td></tr></form>";

				echo "</table></p>";

			}
		}
	}
}
?>
</body>

</html>
