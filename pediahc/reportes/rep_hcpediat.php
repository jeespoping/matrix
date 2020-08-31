<html>

<head>
  <title>HISTORIA CIRUGIA V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*   PARA EL DOCTOR JUAN CAMILO RESTREPO V.1.00	 *
	*				CONEX, FREE => OK                *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha1) or !isset($year) )
	{
		echo "<form action='rep_hcpediat.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = '".$empresa."' AND codigo = '002'  order by Descripcion ";
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
		if(isset($medico) and isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes */
			$query="select distinct Paciente from ".$empresa."_000002 where Pediatra='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";
			//echo mysql_errno() ."=". mysql_error();
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
			$query = "select Fecha  from ".$empresa."_000002 where Paciente='".$pac."' and Pediatra='".$medico."' ";
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
				
			}
			
		}
		//echo"</select>
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	
	else
	{
		$ini1=strpos($pac,"-");
		$doc=substr($pac,0,$ini1);// documento
		$ini2=strpos($pac,"-",$ini1+1);
		$n1=substr($pac,$ini1+1,$ini2-$ini1-1);// nombre 1
		$ini3=strpos($pac,"-",$ini2+1);
		$n2=substr($pac,$ini2+1,$ini3-$ini2-1);// nombre 2
		$ini4=strpos($pac,"-",$ini3+1);
		$ini5=strpos($pac,"-",$ini4+1);
		$ap1=substr($pac,$ini3+1,$ini4-$ini3-1);// apellido 1
		$ap2=substr($pac,$ini4+1,$ini5-$ini4-1);// apellido 2
		$nrohist=substr($pac,$ini5+1);// numero de historia
		$pacn=$n1." ".$n2;// nombre
		$paca=$ap1." ".$ap2;// apellidos 
		
		$fecha2=$year."-".$month."-".$day;
		$query="select * from ".$empresa."_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$query=$query."Apellido1='".$ap1."' and Apellido2='".$ap2."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		
		$telefonos=$row['Telefono']; //telefono
		$tdoc=explode('-',$row['Tip_documento']);// tipo documento
		$estciv=explode('-',$row['Estado_civil']);// estado civil
		$fecnac=$row['Fecha_nacimiento']; //Fecha de nacimiento
		$fn=explode('-',$fecnac);
		$fecdat=$row['Fecha_data']; //Fecha del registro
		$fd=explode('-',$fecdat);
		$edad=$fecdat-$fecnac;
		$ocupacion=$row['Ocup_padre']; //Ocupacion
		$direccion=$row['Direccion']; //Direccion
		$ent=explode('-',$row['Entidad']);// Entidad
		$tel=$row['Telefono'];// Telefono del acompañante
		$respo=$row['Nom_madre'];// Persona responsable
		$ocupacion1=$row['Ocup_madre']; //Ocupacion Madre
		
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='/matrix/images/medical/pediatra/logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>Dr. Juan Camilo Restrepo";
		echo "<br>Medico Especialista<BR>Pediatra CES<BR>Reg.:</b> 10503_95</b></font>";
		echo "</tr></table>";
		$query="select fecha from	".$empresa."_000002 where Paciente='".$pac."' and ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fecha1."') order by Fecha";
		$err = mysql_query($query,$conex);
		$nfechas = mysql_num_rows($err);
		if($nfechas>0)
		{
			for ($j=0;$j<$nfechas;$j++)
			{			
				$row = mysql_fetch_array($err);
						$fec[$j]=substr($row[0],0,10);
						
			}
		}
		
		for ($y=0;$y<$nfechas;$y++)
		{
			$querya="select * from	".$empresa."_000002 where Paciente='".$pac."' and Fecha='".$fec[$y]."' order by Fecha";
			$err = mysql_query($querya,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				
				$row = mysql_fetch_array($err);
				for ($l=0;$l<=$num;$l ++)
				
					if($row[$l] == "NO APLICA" or $row[$l] == ".")
				   		$row[$l]="";
				if(!isset($primero))
				{
					/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
					echo "<table align=center border=1 width=725 >";
					echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS GENERALES</b></font></td></tr>";
					echo "<td colspan=2><font size=3  face='arial'><b>N° Historia Clinica:</b> ".$nrohist."</td></tr>";
					echo "<tr><td colspan=1 ><font size=3 face='arial' ><B>NOMBRE: </b>".$pacn."</td>";
					echo "<td><font size=3  face='arial'><b>APELLIDOS:</b> ".$paca."</td></tr>";
					echo "<tr><td colspan=1><font size=3  face='arial'><b>Identificacion:</b> ".$tdoc[0]."-".$doc."</td>";
					echo "<td colspan=1><font size=3  face='arial'><b>Estado Civil Padres:</b> ".$estciv[1]."</td></tr>";
					echo "<tr><td colspan=1><font size=3  face='arial'><b>Edad:</b> ".$edad."</td>";
					echo "<td><font size=3  face='arial'><b>Fecha:</b> ".$fecdat."</td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>Ocupacion Padre:</b> ".$ocupacion." </td>";
					echo "<td><font size=3  face='arial'><b>Entidad:</b> ".$ent[1]." </td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>Direccion:</b> ".$direccion." </td>";
					echo "<td><font size=3  face='arial'><b>Telefono:</b> ".$tel." </td></tr>";
					echo "<td colspan=2><font size=3  face='arial'><b>Persona Responsable:</b> ".$respo."</td></tr>";
					echo "<td colspan=2><font size=3  face='arial'><b>Ocupacion Madre:</b> ".$ocupacion1."</td></tr>";
					
					$primero="OK";
				}
				/*HC*/
				echo "<table align=center border=1 width=725 >";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA ".$fec[$y]."</b></font></td></tr>";
				if ($row['Mc_ea']!='.') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>M.C y E.A: </b>".$row['Mc_ea']."</td></tr>";
				if ($row['Revision_sist']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Revision por Sistemas: </b>".$row['Revision_sist']."</td></tr>";
				
				//ANTECEDENTES PERSONALES
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES PERSONALES</b></font></td></tr>";
				if ($row['Prod_embarazo']!=0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Producto del Embarazo Numero: </b>".$row['Prod_embarazo']."</td></tr>";
				if ($row['Pve']!='off') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Parto Vaginal: X</b></td></tr>";
				if ($row['Cesarea']!='off') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Parto por Cesarea: X</b></td></tr>";
				if ($row['Problema_preparto']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Problemas Preparto: </b>".$row['Problema_preparto']."</td></tr>";
				if ($row['Semanas_embarazo']!=0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Semanas de Embarazo: </b>".$row['Semanas_embarazo']."</td></tr>";
				if ($row['Peso_nacimiento']!=0.0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Peso Nacimiento: </b>".$row['Peso_nacimiento']."</td></tr>";
				if ($row['Talla_nacimiento']!=0.0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Talla Nacimiento: </b>".$row['Talla_nacimiento']."</td></tr>";
				if ($row['Alim_complement']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Alimento Complementario: </b>".$row['Alim_complement']."</td></tr>";
				if ($row['Formula']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Formula: </b>".$row['Formula']."</td></tr>";
				if ($row['Denticion']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Denticion: </b>".$row['Denticion']."</td></tr>";
				if ($row['Cirugias']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Cirugias: </b>".$row['Cirugias']."</td></tr>";
				if ($row['Alergias']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Alergias: </b>".$row['Alergias']."</td></tr>";
				if ($row['Hospitalizaciones']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Hospitalizaciones: </b>".$row['Hospitalizaciones']."</td></tr>";
				if ($row['Fracturas']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Fracturas: </b>".$row['Fracturas']."</td></tr>";
				if ($row['Transfusiones']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Transfusiones: </b>".$row['Transfusiones']."</td></tr>";
	
				//DESARROLLO NEUROLOGICO
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DESARROLLO NEUROLOGICO</b></font></td></tr>";
				if ($row['Sosten_cefalico']!=0.0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Sosten Cefalico: </b>".$row['Sosten_cefalico']."</td></tr>";
				if ($row['Se_sento']!=0.0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Se sento: </b>".$row['Se_sento']."</td></tr>";
				if ($row['Camino']!=0.0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Camino: </b>".$row['Camino']."</td></tr>";
				if ($row['Lenguaje_palabra']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Lenguaje Palabras: </b>".$row['Lenguaje_palabra']."</td></tr>";
				if ($row['Oraciones']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Oraciones: </b>".$row['Oraciones']."</td></tr>";
				if ($row['Control_esfinteres']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Control Esfinteres: </b>".$row['Control_esfinteres']."</td></tr>";
				if ($row['Guarderia']!='NO APLICA')
					{
						echo "<tr><td colspan=4><font size=3 face='arial' ><B>Guarderia: </b>".$row['Guarderia']."</td></tr>";
						echo "<tr><td colspan=4><font size=3 face='arial' ><B>Fecha guarderia: </b>".$row['Fecha_guarderia']."</td></tr>";
					}
				if ($row['Colegio']!='NO APLICA')
					{
						echo "<tr><td colspan=4><font size=3 face='arial' ><B>Colegio: </b>".$row['Colegio']."</td></tr>";
						echo "<tr><td colspan=4><font size=3 face='arial' ><B>Fecha colegio: </b>".$row['Fecha_colegio']."</td></tr>";
					}
				if ($row['Nivel_guarderia']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Nivel : </b>".$row['Nivel_guarderia']."</td></tr>";
								
				// ANTECEDENTES FAMILIARES
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES FAMILIARES</b></font></td></tr>";
				if ($row['Antefam_fumadores']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Fumadores: </b>".$row['Antefam_fumadores']."</td></tr>";
				if ($row['Antefam_convivientes']!=0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Convivientes: </b>".$row['Antefam_convivientes']."</td></tr>";
				if ($row['Antefam_hermanos']!=0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Hermanos: </b>".$row['Antefam_hermanos']."</td></tr>";
				if ($row['Antefam_enfermedades']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Enfermedades: </b>".$row['Antefam_enfermedades']."</td></tr>";	
				if ($row['Antefam_asma']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Asma: X</b></td></tr>";
				if ($row['Antefam_cancer']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Cancer: X</b></td></tr>";
				if ($row['Antefam_convulsiones']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Convulsiones: X</b></td></tr>";
				if ($row['Antefam_diabetes']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Diabetes: X</b></td></tr>";
				if ($row['Antefam_hta']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>H.T.A.: X</b></td></tr>";
				if ($row['Antefam_enfcardiacas']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Enfermedades Cardiacas: X</b></td></tr>";
				if ($row['Antefam_probrenales']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Problemas Renales: X</b></td></tr>";
				if ($row['Antefam_retardo']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Retardo: X</b></td></tr>";
				if ($row['Antefam_animales']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Animales: X</b></td></tr>";
				
				// EXAMEN FISICO
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>EXAMEN FISICO</b></font></td></tr>";
				echo "<tr><td colspan=4><font size=3 face='arial' ><B>Edad: </b>".$row['Exafisico_edad']."</td></tr>";
				if ($row['Exafisico_peso']!=0.0) 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Peso (Kg): </b>".$row['Exafisico_peso']."</td></tr>";
				if ($row['Exafisico_talla']!=0.0) 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Talla (cms): </b>".$row['Exafisico_talla']."</td></tr>";
				if ($row['Exafisico_pcefalico']!=0.0) 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Perimetro Cefalico (cms): </b>".$row['Exafisico_pcefalico']."</td></tr>";
				if ($row['Exafisico_temp']!=0.0) 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Temperatura °C: </b>".$row['Exafisico_temp']."</td></tr>";
				if ($row['Exafisico_fresp']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Frecuencia Respiratoria: </b>".$row['Exafisico_fresp']."</td></tr>";
				if ($row['Exafisico_fcdca']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Frecuencia Cardiaca: </b>".$row['Exafisico_fcdca']."</td></tr>";
				if ($row['Exafisico_pa']!=0.0)
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Presion Arterial: </b>".$row['Exafisico_pa']."</td></tr>";
				if ($row['Exafisico_cabeza']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Cabeza: </b>".$row['Exafisico_cabeza']."</td></tr>";
				if ($row['Exafisico_ojos']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Ojos: </b>".$row['Exafisico_ojos']."</td></tr>";	
				if ($row['Exafisico_oidos']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Oidos: </b>".$row['Exafisico_oidos']."</td></tr>";
				if ($row['Exafisico_boca']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Boca: </b>".$row['Exafisico_boca']."</td></tr>";	
				
				if ($row['Exafisico_corazon']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Corazon: </b>".$row['Exafisico_corazon']."</td></tr>";
				if ($row['Exafisico_pulmones']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Pulmones: </b>".$row['Exafisico_pulmones']."</td></tr>";	
				if ($row['Exafisico_abdomen']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Abdomen: </b>".$row['Exafisico_abdomen']."</td></tr>";
				if ($row['Exafisico_genitales']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Genitales: </b>".$row['Exafisico_genitales']."</td></tr>";	
				if ($row['Exafisico_cadera']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Cadera: </b>".$row['Exafisico_cadera']."</td></tr>";
				if ($row['Exafisico_Extremidades']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Extremidades: </b>".$row['Exafisico_Extremidades']."</td></tr>";	
				if ($row['Exafisico_piel']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Piel: </b>".$row['Exafisico_piel']."</td></tr>";
				
				// DIAGNOSTICO Y PLAN DE MANEJO
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DIAGNOSTICO Y CONDUCTA</b></font></td></tr>";
				if ($row['Diagnostico']!='NO APLICA')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Diagnostico: </b>".$row['Diagnostico']."</td></tr>";
				if ($row['Conducta']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Conducta: </b>".$row['Conducta']."</td></tr>";
				
				echo "</table>";
								
				if($y==($nfechas-1))
			
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
			
				else
				{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
			
				}
				
				$queryseg="select * from	".$empresa."_000006 where Paciente='".$pac."' and ".$pre." order by Fecha";
		
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						/*SEGUIMIENTO*/
						echo "<table align=center border=1 width=725 >";
							echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SEGUIMIENTO ".$seg['Fecha']."</b></font></td></tr>";
							echo "<td  align=left colspan='4'  ><font size='3' face='arial'>";
						if ($seg['Seguimiento']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Seguimiento Clinico: </b>".$seg['Seguimiento']."</td></tr>";
						if ($seg['Seg_peso']!=0.0)
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Peso (Kg): </b>".$seg['Seg_peso']."</td></tr>";
						if ($seg['Seg_talla']!=0.0)
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Talla (cms): </b>".$seg['Seg_talla']."</td></tr>";
						if ($seg['Seg_pcefalico']!=0.0)
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Perimetro cefalico (cms): </b>".$seg['Seg_pcefalico']."</td></tr>";
						if ($seg['Seg_temp']!=0.0)
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Temperatura °C: </b>".$seg['Seg_temp']."</td></tr>";
						if ($seg['Seg_fresp']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Frecuencia Respiratoria: </b>".$seg['Seg_fresp']."</td></tr>";
						if ($seg['Seg_fcdca']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Frecuencia Cardiaca: </b>".$seg['Seg_fcdca']."</td></tr>";
						if ($seg['Seg_pa']!=0.0)
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>P.A.: </b>".$seg['Seg_pa']."</td></tr>";
						if ($seg['Seg_cabeza']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Cabeza: </b>".$seg['Seg_cabeza']."</td></tr>";
						if ($seg['Seg_ojos']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Ojos: </b>".$seg['Seg_ojos']."</td></tr>";
						if ($seg['Seg_oidos']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Oidos: </b>".$seg['Seg_oidos']."</td></tr>";
						if ($seg['Seg_boca']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Boca: </b>".$seg['Seg_boca']."</td></tr>";
						if ($seg['Seg_corazon']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Corazon: </b>".$seg['Seg_corazon']."</td></tr>";
						if ($seg['Seg_pulmones']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Pulmones: </b>".$seg['Seg_pulmones']."</td></tr>";
						if ($seg['Seg_abdomen']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Abdomen: </b>".$seg['Seg_abdomen']."</td></tr>";
						if ($seg['Seg_genitales']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Genitales: </b>".$seg['Seg_genitales']."</td></tr>";
						if ($seg['Seg_cadera']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Cadera: </b>".$seg['Seg_cadera']."</td></tr>";
						if ($seg['Seg_Extremidades']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Extremidades: </b>".$seg['Seg_Extremidades']."</td></tr>";
						if ($seg['Seg_piel']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Piel: </b>".$seg['Seg_piel']."</td></tr>";
						if ($seg['Diagnostico']!='NO APLICA')
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Diagnostico: </b>".$seg['Diagnostico']."</td></tr>";
						if ($seg['Conducta']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Conducta: </b>".$seg['Conducta']."</td></tr>";
						if ($seg['Observaciones']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Observaciones: </b>".$seg['Observaciones']."</td></tr>";
						
						echo "</table>";		
					}
				}
				$queryseg="select * from	".$empresa."_000005 where Paciente='".$pac."' and ".$pre." order by Fecha";
		
				$err2 = mysql_query($queryseg,$conex);
				$num2 = mysql_num_rows($err2);
				if($num2>0)
				{
					for ($x=0;$x<$num2;$x++)
					{
						$vac = mysql_fetch_array($err2);
						/*VACUNAS*/
						echo "<table align=center border=1 width=725 >";
							echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>VACUNAS ".$vac['Fecha']."</b></font></td></tr>";
							echo "<td  align=left colspan='4'  ><font size='3' face='arial'>";
							if ($vac['Bcg']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Bcg </b></td></tr>";
							if ($vac['Hepatitis_b']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Hepatitis B </b> </td></tr>";
							if ($vac['Polio_pvi']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Polio o PVI </b> </td></tr>";
							if ($vac['Haemophilus']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Haemophilus Influenzae </b> </td></tr>";
							if ($vac['Dtp']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>DTP o DTPa </b> </td></tr>";
							if ($vac['Neumococo']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Neumococo (Prevenar) </b> </td></tr>";
							if ($vac['Rotavirus']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Rotavirus </b> </td></tr>";
							if ($vac['Mmr']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Papera, Sarampion, Rubeola (MMR) </b> </td></tr>";	
							if ($vac['Fiebre_amarilla']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Fiebre Amarilla </b> </td></tr>";
							if ($vac['Varicela']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Varicela </b> </td></tr>";
							if ($vac['Hepatitis_a']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Hepatitis A </b> </td></tr>";
							if ($vac['Influenza']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Influenza </b> </td></tr>";	
							if ($vac['Neumo_23']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Neumo 23 </b> </td></tr>";
							if ($vac['Meningococo']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Meningococo </b> </td></tr>";
							if ($vac['Pvh']!='off')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Papiloma virus humano </b> </td></tr>";
							if ($vac['Influenza']!='.')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Observaciones: </b>".$vac['Observaciones']."</td></tr>";
								
						echo "</table>";		
					}
				}
			}
		}
	}
	include_once("free.php");
}
?>
