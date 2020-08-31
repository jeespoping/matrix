<html>

<head>
  <title>ORDENES</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
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
//DESCRIPCIÓN					: Tiene como parámetro el médico, el paciente y la fecha del examen. 
//								  Despliega la información básica contenida en la tabla oftalmo_000006.
//
//--------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//
//	2006-04-01
//		Se Cambia Clínica Médica Las americas por Clinica las Americas.
//		Se reemplaza <form action='000003_of01.php' method=post> por <form action='' method=post>
//		Se borra el procesimiento a los examenes para que imprima directamente lo que esta escrito en el registro
//
//--------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	det_selecciones
//	oftalmo_000006


session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha) ) 	{
		echo "<center>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5>Ordenes Historia Oftalmologica</font></a></tr></td>";
		echo "<tr><td align=center bgcolor='#cccccc'><font size='2'> <b> ordenesOf.php Ver.2006-04-01</b></font></tr></td></table><br><br>";
		echo "</center>";
		echo "<form action='' method=post>";
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