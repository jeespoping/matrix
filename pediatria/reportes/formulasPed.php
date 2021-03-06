<html>
<head>
  <title>FORMULAS Y CERTFICADOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/****************************************************************
*																*
*			    GENERACI?N DE FORMULAS Y CERTIFICADOS	 		*
*		    COMPLEMENTO A LA HISTORIA CL?NICA PEDIATRICA		*
*																*
*****************************************************************/

//==================================================================================================================================
//GRUPO						:pediatria
//AUTOR						:Ana Mar?a Betancur V.
$wautor="Ana Mar?a Betancur V.";
//FECHA CREACI?N			:2004-04-15
//FECHA ULTIMA ACTUALIZACI?N 	:
$wactualiz="(Versi?n 2006-03-27)";
//DESCRIPCI?N					: Hace formulas de medicamentos y certificados para el colegio de los pacientes a partir de el seguimiento.
//								  Pide como par?metros el m?dico, el paciente, la fecha del seguimiento y si es certificado o formula.
//--------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//
//	2006-03-25
//		Se modifica el nombre de 000002_ped1.php a formulasPed.php
//		Se Cambia Cl?nica M?dica Las americas por Clinica las Americas.
//		Se hace encabezado.
//		El proceso del para imprimir el nombre de los pacientes se cambia para que sea por el explode.
//		Se documenta.
//--------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	det_selecciones
//	pediatra_000002


session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha) or isset($control))
	{
		echo "<center>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5>Formulas y Certificados</font></a></tr></td>";
		echo "<tr><td align=center bgcolor='#cccccc'><font size='2'> <b>formulasPed.php Ver.2006-03-27</b></font></tr></td></table><br><br>";
		echo "</center>";
		echo "<form action='' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b>CL?NICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>TORRE M?DICA</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1>MEDICO: </td>";	

		/* Si el medico no ha sido escogido Buscar a los pediatras registrados para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
		$query = "select Subcodigo,Descripcion from det_selecciones where medico='pediatra' and codigo='004'order by Descripcion";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$med=$row[0]."-".$row[1];
				if($med==$medico)
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";				}
		}	// fin del if $num>0
		
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1>PACIENTE: </td>";	
		
			/* Si el paciente no esta set construir el drop down */
			echo "</td><td bgcolor=#cccccc colspan=2>";
			if(isset($peso))
				echo "<input type='text' name='pac' value='".$pac."'>";
			else
			{	
				echo "<select name='pac'>";
				if(isset($medico))
				{
					/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
					$query = "select Paciente from pediatra_000002 where Pediatra='".$medico."' order by Paciente ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num>0)
					{
						for ($j=0;$j<$num;$j++)
						{	
							$row = mysql_fetch_array($err);
							if($pac==$row[0])
								echo "<option selected>".$row[0]."</option>";
							else
								echo "<option>".$row[0]."</option>";
						}
					}	// fin $num>0
				}	//fin isset medico
				echo "</select>";
			}
			echo "</td></tr><tr><td bgcolor=#cccccc colspan=1>FECHA: </td>";	
		echo "</td><td bgcolor=#cccccc colspan=2>";

		if(isset($pac) and !isset($peso))
		{
			echo "<select name='fecha'>";
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select Fecha from pediatra_000002 where Paciente='".$pac."' and Pediatra='".$medico."' order by Fecha ";
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
		
			echo"</select>";
		}
		else if	(isset($pac) and isset($peso))
			echo "<input type='text' name='fecha' value='".$fecha."'>";
		echo "</td></tr><tr><td align=center bgcolor=#cccccc></td>";
		if(isset($peso))
		{
			echo "<input type='hidden' name='cta' value='".$cta."'>";
			echo "<input type='hidden' name='peso' value='".$peso."'>";
			echo "<input type='hidden' name='talla' value='".$talla."'>";
			echo "<input type='hidden' name='per' value='".$per."'>";
		}
		echo"<td align=center bgcolor=#cccccc>Certificado <input type='checkbox' name='cert'></td>";
		echo"<td align=center bgcolor=#cccccc> Formula <input type='checkbox' name='form'></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else 
	/******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	{
		if(!isset($cert))
			$cert="";
		if(!isset($form))
			$form="";
		$ini1=explode('-',$medico);
		$reg=$ini1[0];
		$nom=$ini1[1];
		$ini1=explode('-',$pac);
		$pacn=$ini1[2]." ".$ini1[3]." ".$ini1[0]." ".$ini1[1];
		echo "<table width=450 heigth=5000  border=0>";
		echo "<tr><td colspan=3><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
		echo"<tr><td width=50></td><td><font color='black'>FECHA ".$fecha."<br></font></td><td width=50></td></tr>";

		if(strcmp($cert,'on')==0)
		{
			echo"<tr><td width=40></td><td><font color='black'>CERTIFICO QUE EL NI?O ".$pacn."</font></td><td width=40></td></tr>";
			echo"<tr><td width=40></td><td><font color='black'>Se encuebtra fisicamente sin ninguna enfermedad infectocontagiosa.<br>Vacunaci?n actualizada.</font></td><td width=40></td></tr>";
			echo "<tr><td><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
		}
		else if(strcmp($form,'on')==0)
		{
			if(!isset($peso))
			{				$query = "select Cta,Peso_kilos,Talla,Perimetro_cefalico  from pediatra_000002 where Paciente='".$pac."' and Fecha='".$fecha."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					$row = mysql_fetch_array($err);
					$cta=$row[0];
					$peso=$row[1];
					$talla=$row[2];
					$per=$row[3];
				}
			}	
			$pos=0;
			$br=1;
			$posul=strrpos($cta,"<BR>");
			$low=strtolower($cta);
			while($pos<$posul )
			{
				$pos=strpos($low,"<br>",$pos);
				$br++;
				$pos=$pos+4;
			}
			$br=26-$br;
			if($peso !=0 or $talla !=0 or $per !=0 )
			{
				echo"<tr><td width=50></td><td><font color='black'>";
				if($peso !=0)
					echo"<b>PESO: </b>".$peso."			";
				if($talla !=0)
					echo "<b>TALLA: </b>".$talla."			";
				if($per !=0)	
					echo"<b>P.C: </b>".$per;
				echo"<br></td></font><td width=50></td></tr>";
				$br++;
			}
			echo"<tr><td width=50></td><td><font color='black'><b>PACIENTE ".$pacn.":</b><br></td></font><td width=50></td></tr>";
			echo"<tr><td width=40 heigth=2000></td><td><BR><font color='black'>".$cta."</font>";
			
			for($i=1;$i<$br;$i++)
				echo"<br>";
			echo "</font></td><td width=40></td></tr>";
		}
		else
			echo"<tr><td><font color='#FF3399' size=5>Por favor escoja uno de los dos modos de reporte.</font></td></tr>";
		echo "<tr><td align=center colspan=3></tr></table>";

	}// fin del else los parametros estan set
	include_once("free.php");
}


?>