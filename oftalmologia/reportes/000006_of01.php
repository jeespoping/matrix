<html>

<head>
  <title>EXAMENES</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000"><center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Historia Clínica Oftalmologica</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size='2'> <b> repHistOf.php Ver.2006-03-29</b></font></tr></td></table><br><br>
</center>
<?php
include_once("conex.php");

/********************************************************
*														*
*    REPORTE DE ORDENES DE EXAMENES PARA OFTALMOLOGICA 	*
*														*
*********************************************************/

//==================================================================================================================================
//GRUPO						:OFTALMOLOGIA
//AUTOR						:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN			:2004-04-15
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2006-04-01)";
//DESCRIPCIÓN					: Tiene como parámetro el médico, el paciente y las fechas de inicio y de corte, por defecto muestra 
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

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha) )
	{
		echo "<form action='000006_of01.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA LAS AMERICAS </font></b></td></tr>";
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
				if (($row[0]."-".$row[1]) ==$medico)
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}	// fin del if $num>0
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	

		/* Si el paciente no esta set construir el drop down */
		
		
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			$query="select Paciente  from oftalmo_000006 where Oftalmologo='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";
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
			echo "</select><input type='hidden' name='pac1' value='".$pac1."'>";
		}	//fin isset pac1
		else
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
		echo "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </font></td>";	
		echo "</td><td bgcolor=#cccccc colspan=2><select name='fecha'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select Fecha  from oftalmo_000006 where Paciente='".$pac."' and Oftalmologo='".$medico."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<=$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
			}
		}
		echo"</select></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	{
		$ini1=explode("-",$pac);
		$nrohist=$ini1[0];
		$n1=$ini1[1];
		$n2=$ini1[2];
		$ap1=$ini1[3];
		$ap2=$ini1[4];
		$id=$ini1[5];
		$pacn=$n1." ".$n2." ".$ap1." ".$ap2;
		$pacn=$pacn."<br><b>DOCUMENTO: </b>".$id;
		$query="select Examenes from oftalmo_000006 where Paciente='".$pac."' and Oftalmologo='".$medico."' and Fecha='".$fecha."' ";
		$err = mysql_query($query,$conex);
		if($err)
		{
			$row = mysql_fetch_array($err);
			$examenes=$row[0];
			echo "<table border=0 width=800><tr><td ><b>FECHA: </b>".$fecha."</td></tr><tr><td >".$pacn."</td></tr><tr><td><br><br></td></tr>";
			echo "</td><tr><td ><b>EXAMENES:</b></td></tr>";
			echo "<tr><td ><br>".$examenes."</td></tr>";
			echo"</table>";
		}	
		else
			echo "NO EXISTEN ORDENES PARA ESE PACIENTE EN ESA FECHA";	
		
	}
	include_once("free.php");
}
?>
</HTML>