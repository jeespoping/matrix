<head>
  <title>HISTORIA </title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Historia Clínica</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size='2'> <b> rep_historia.php Ver.2006-03-22</b></font></tr></td></table><br><br>
</center>
<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS 		*
*			PARA MEDICOS PEDIATRAS						*
*														*
*********************************************************/

//==================================================================================================================================
//GRUPO						:pediatria
//AUTOR						:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN			:2004-04-15
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2008-12-04)";
//DESCRIPCIÓN					: Recibe como parámetros el medico y parte del nombre del paciente, a partir de la parte del nombre 
//								  se crea una lista de sonde el usuario escoje el paciente. Apartir de ahi empieza el verdadero 
//								  reporte, busca en las tablas pediatra_000001 y pediatra_000002 y despliega la información en pantalla						
//------------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//
//	2008-12-04
//		Se agrega el campo de historia fisica

//	2006-03-22
//		Se Cambia Clínica Médica Las americas por Clinica las Americas
//		Se borran todos los registros de vacunación para que solo quede un campo llamado vacunación.
//
//	2006-03-16 
//		Se modifica para que los nuevos campos  exigidos por la seccional apliquen
// 
//	2005-05-31 
//		Se modifica el orden de aparicion de los nombres en el drop down.

//	2007-02-06 
//		Se agregaron los campos de descripcion del examen fisico.		
//--------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	pediatra_000001	Historia clínica
//	pediatra_000002 seguimiento, información del paciente.
//	det_selecciones
 
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac)  )
	{
		echo "<form action='' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>TORRE MÉDICA</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1>MEDICO: </td>";

		/* Si el medico no ha sido escogido 
		Buscar a los pediatras registrados para	construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
		$query = "select Subcodigo,Descripcion from det_selecciones where medico='pediatra' and codigo='004'order by Descripcion";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				$med= $row[0]."-".$row[1];
				if($med == $medico)
				echo "<option selected>".$med."</option>";
				else
				echo "<option>".$med."</option>";				}
		}	// fin del if $num>0

		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1>PACIENTE: </td>";

		/* Si el paciente no esta set construir el drop down */
		
		if(isset($pac1) and isset($medico))
			{
				
				/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
				$query = "select Nombre1,Nombre2,Apellido1,Apellido2,Identificacion from pediatra_000001 where Pediatra='".$medico."' and (Apellido1 like '%$pac1%' or Apellido2 like '%$pac1%' or Nombre1 like '%$pac1%' or Nombre2 like '%$pac1%') order by Nombre1,Nombre2,Apellido1,Apellido2 ";
				echo "<td bgcolor=#cccccc colspan=2><select name='pac'>";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<$num;$j++)
					{
						$row = mysql_fetch_array($err);
						$paci=$row[0]."-".$row[1]."-".$row[2]."-".$row[3]."-".$row[4];
						if($pac==$paci)
						echo "<option selected>".$paci."</option>";
						else
						echo "<option>".$paci."</option>";
					}
				}	// fin $num>0
				echo "</select></td></tr>";
				echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
			}	//fin isset medico
			else
			{
				echo "<td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
				echo "</td></tr>";
			}



			// fin $num>0
		//fin isset medico

		echo"</td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	/********************************
	* TODOS LOS PARAMETROS ESTAN SET *
	********************************/
	{
		$ini1=explode("-",$medico);
		$reg=$ini1[0];
		$nomed=$ini1[1];
		$ini=explode("-",$pac);
		$n1=$ini[0];
		$n2=$ini[1];
		$ap1=$ini[2];
		$ap2=$ini[3];
		$id=$ini[4];
		$pac=$ap1."-".$ap2."-".$n1."-".$n2."-".$id;
		/*query a la historia clinica*/
		$querya="select * from pediatra_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$querya=$querya."Apellido1='".$ap1."' and Apellido2='".$ap2."' and Identificacion='".$id."' and Pediatra='".$medico."' ";
		//echo $querya;
		$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			$row = mysql_fetch_array($err);
			for ($l=0;$l<=29;$l ++)
			//echo "row[".$l."]=".$row[$l]."<br>";
			if($row[$l] == "NO APLICA" or $row[$l] == ".")
			$row[$l]="";
			$p=0;	$vac=array();	 	 /*arreglo deonde se ingresan las fechas en que fueron aplicadas las vacunas

			/*INFORMACION PROVENIENTE DE LA HISTORIA CLÍNICA*/
			echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
			echo "<img SRC='\MATRIX\images\medical\pediatra\logotorre.JPG' width='180' height='117'></td>";
			echo "<td align=center colspan='4' ><font size='3' color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
			echo "</tr><tr><td  align=center colspan='4'><font size='2' color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
			echo "</tr><tr><td  align=center colspan='4'><font size='2' color='#000080' face='arial'><B>".$nomed."";
			echo "<br>MEDICO PEDIATRA<BR>Reg.:</b>".$reg."</b></font>";
			echo "</tr></table>";
			echo "<center><table width=725 border=1 cellpadding=3>";
			echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='2'  face='arial'><b>DATOS GENERALES</b></font></td></tr>";
			echo "<td nowrap><font size='2' face='arial' ><B>PACIENTE:<br> </b>".$n1."  ".$n2." ".$ap1." ".$ap2." </td>";
			echo "<td><font size='2'  face='arial'><b>D.I:</b> ".$id."</td>";
			echo "<td><font size='2'  face='arial'><B>FECHA NACIMIENTO: </b>".$row['Fecha_nacimiento']."</td>";
			echo "<td><font size='2'  face='arial'><B>SEXO: </b>".strtolower(substr($row['Sexo'],3))."</td>";
			echo "<tr><td ><font size='2' face='arial'><B>PADRE:</B> ".$row['Padre']." ";
			echo "<td ><font size='2' face='arial'><b>OCUPACION: </b>".strtolower($row['Oficio_padre'])."</B>";
			echo "<td colspan=2 rowspan=2><font size='2' face='arial'><b>HERMANOS:<br></b> ".$row['Hermanos']."</td></tr>";
			echo "<tr><td><font size='2' face='arial'><B>MADRE:</B> ".$row['Madre']." ";
			echo "<td ><font size='2' face='arial'><b>OCUPACION:</b> ".strtolower($row['Oficio_madre'])."</B></tr>";
			echo "<tr><td nowrap><font size='2' face='arial' ><B>TEL.: </b>".$row['Telefonos']." </td>";
			echo "<td><font size='2'  face='arial'><b>CEL.:</b> ".$row['Celular']."</td>";
			echo "<td colspan='2'><font size='2'  face='arial'><B>E-MAIL: </b>".$row['Email']."</td></tr>";
			echo "<tr><td colspan='4' nowrap><font size='2' face='arial' ><B>DOMICILIO: </b>".$row['Domicilio']." </td>";
			
			echo "<tr><td colspan='2' nowrap><font size='2' face='arial' ><B>RESPONSABLE: </b>".$row['Responsable']." </td>";
			echo "<td colspan='2'><font size='2'  face='arial'><B>TELEFONO: </b>".$row['Tel_resp']."</td></tr>";
			echo "<tr><td colspan='2' nowrap><font size='2' face='arial' ><B>ACOMPAÑANTE: </b>".$row['Acompanante']." </td>";
			echo "<td colspan='2'><font size='2'  face='arial'><B>TELEFONO: </b>".$row['Tel_acom']."</td></tr>";

			echo "<tr><td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>ANTECEDENTES GINECOBSTETRICOS</b>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row['Antecedentes_ginecobs']."</fieldset>";
			echo "<td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>ANTECEDENTES PERSONALES</b>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row['Antecedentes_personales']."</fieldset></TR>";
			echo "<tr><td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>ANTECEDENTES ALERGICOS</b>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row['Antecedentes_alergicos']."</fieldset>";
			echo "<td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>ANTECEDENTES QUIRURGICOS</b>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row['Antecedentes_quirurgicos']."</fieldset></TR>";
			echo "<tr><td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>ANTECEDENTES FAMILIARES</b>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row['Antecedentes_familiares']."</fieldset>";
			echo "<td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>OBSERVACIONES</b>";
			echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row['Observaciones']."</fieldset></td></TR>";
			//echo "<table width=330 border=1 width=95% height=80><tr><td bgcolor='#FFFFFF'>".$row[26]."</td></tr></table></td>";


			/*Apartir de aqui empieza la tabla de vacunación*/
			echo "</table><table width=725 border=1>";
			echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='2'  face='arial'><b>VACUNACIÓN</b></font></td></tr>";
			echo "<tr><td align=left  bgcolor='#FFEECC' height='15'><font size='2'  face='arial'><b>REEGISTRO DE VACUNAS</b>";
			echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row['Vacunacion']."</fieldset></TR>";
			echo "</table>";
		}
		/*EMPEZAMOS A BUSCAR LOS SEGUIMIENTOS*/
		$querya="select * from pediatra_000002 where Paciente='".$pac."' and Pediatra='".$medico."'  order by Fecha";
//		echo $querya;
		$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_row($err);
				for ($l=0;$l<=21;$l ++)
				if($row[$l]=="NO APLICA" or $row[$l]=="0")
				$row[$l]="";

				echo "<center><table width=725 border=1 CELLPADDING=3>";
				echo "<tr><td align=left colspan='5' bgcolor='#cccccc' height='15'><font size='2'  face='arial'><b>SEGUIMIENTO ".$row[5]."</b></font></td></tr>";
				echo "<td colspan=2><font size='2' face='arial' ><B>LUGAR: </b>".substr($row[6],3,1).strtolower(substr($row[6],4))." </td>";
				echo "<td colspan=3><font size='2'  face='arial'><b>ENTIDAD:</b> ".substr($row[7],3,1).strtolower(substr($row[7],4))."</td>";
				echo "<tr><td><font size='2'  face='arial'><B>EDAD: </b>".$row[8]."</td>";
				echo "<td><font size='2'  face='arial'><B>PESO (kg): </b>".$row[9]."</td>";
				echo "<td><font size='2'  face='arial'><B>TALLA: </b>".$row[10]."</td>";
				echo "<td><font size='2'  face='arial'><B>P. CEFALICO: </b>".$row[11]."</td>";
				echo "<td><font size='2'  face='arial'><B>TEMPERATURA: </b>".$row[12]."</td>";
				//2008-12-04
				echo "<tr><td align=left  colspan='5' bgcolor='#99CCFF'><font size='2' face='arial'><b>PRESENCIA DE HISTORIA FISICA</b>";
				echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row[30]."</fieldset></TR>";
				echo "<tr><td width=375 align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>MOTIVO CONSULTA</b>";
				echo "<fieldset style='background-color: #FFFFFF'>".$row[13]."</fieldset>";
				echo "<td width=375 align=left  colspan='3' bgcolor='#99CCFF'><font size='2' face='arial'><b>ENFERMEDAD ACTUAL</b>";
				echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row[14]."</fieldset></TR>";
				echo "<tr><td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>REVISION POR SISTEMAS</b>";
				echo "<fieldset style='background-color: #FFFFFF'>".$row[15]."</fieldset>";
				echo "<td align=left  colspan='3' bgcolor='#99CCFF'><font size='2' face='arial'><b>TRATAMIENTOS</b>";
				echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row[16]."</fieldset></TR>";
				echo "<tr><td align=center  colspan='5' bgcolor='#99CCFF'><font size='2' face='arial'><b>EXAMEN FISICO</b></TR>";
				
				////////////////////////////////////esto es para colocar la descripcion del examen medico
				$arr[17]='Cabeza';
				$arr[18]='Neurologico';
				$arr[19]='Oidos';
				$arr[20]='Orofaringe';
				$arr[21]='Cardiopulmonar';
				$arr[22]='Abdomen';
				$arr[23]='Genitales';
				$arr[24]='Extremidades';
				$arr[25]='Piel';
				$arr[26]='Otros';
					
				for ($l=17;$l<=26;$l ++){	
					
						if( $row[$l]!='' and $row[$l]!="NO APLICA" ){
							
						
							echo "<tr><td align=left  colspan='5' bgcolor='#99CCFF'><font size='2' face='arial'><b>".$arr[$l]."</b>";
							echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row[$l]."</fieldset></TR>";
							
						}	else{
							
						$row[$l]='Normal';
							
							echo "<tr><td align=left  colspan='5' bgcolor='#99CCFF'><font size='2' face='arial'><b>".$arr[$l]."</b>";
							echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row[$l]."</fieldset></TR>";
						}
						
					
				}
				////////////////////////////////////
				echo "<tr><td align=left  colspan='5' bgcolor='#99CCFF'><font size='2' face='arial'><b>DIAGNOSTICO</b>";
				echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row[27]."</fieldset></TR>";
				echo "<tr><td align=left  colspan='2' bgcolor='#99CCFF'><font size='2' face='arial'><b>CONDUCTA</b>";
				echo "<fieldset style='background-color: #FFFFFF'>".$row[28]."</fieldset>";
				echo "<td align=left  colspan='3' bgcolor='#99CCFF'><font size='2' face='arial'><b>OBSERVACIONES</b>";
				echo "<fieldset style='background-color: #FFFFFF; height:50%'>".$row[29]."</fieldset></TR>";
				
			}
		}
	}// fin del else los parametros estan set
	include_once("free.php");
}	?>