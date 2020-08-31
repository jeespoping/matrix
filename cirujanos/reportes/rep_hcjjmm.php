<html>

<head>
  <title>HISTORIA TORRE MEDICA V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*		  PARA MEDICOS TORRE MEDICA  	V.1.00	 *
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
		echo "<form action='rep_hcjjmm.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
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
			$query="select distinct Paciente from ".$empresa."_000002 where Cirujano='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";
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
			$query = "select Fecha  from ".$empresa."_000002 where Paciente='".$pac."' and Cirujano='".$medico."' ";
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
		$ini1=strpos($medico,"-");
		$reg=substr($medico,0,$ini1);// registro medico
		$nomed=substr($medico,$ini1+1);// nombre medico
		$paciente=explode("-",$pac);
		$doc=$paciente[0];// documento
		$n1=$paciente[1];// nombre 1
		$n2=$paciente[2];// nombre 2
		$ap1=$paciente[3];// apellido 1
		$ap2=$paciente[4];// apellido 2
		$nrohist=$paciente[5];// numero de historia
		//$pacn=$n1." ".$n2." ".$ap1." ".$ap2;// nombre completo
		
		$fecha2=$year."-".$month."-".$day;
		$query="select * from ".$empresa."_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$query=$query."Apellido1='".$ap1."' and Apellido2='".$ap2."' and Documento='".$doc."' ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		//echo $query;
		$telefonos=$row['Telefono']; //telefono
		$tdoc=explode('-',$row['Tip_documento']);// tipo documento
		$fecnac=$row['Fecha_nacimiento']; //Fecha de nacimiento
		//$fn=explode('-',$fecnac);
		$fecdat=$row['Fecha_data']; //Fecha del registro
		//$fd=explode('-',$fecdat);
		$edad=$fecdat-$fecnac;
		$sex=explode('-',$row['Sexo']);// sexo
		$ocupacion=$row['Ocupacion']; //Ocupacion
		$ent=explode('-',$row['Entidad']);// Entidad
		$cb=explode('-',$row['Cotizante_beneficiario']);// Cotizante o beneficiario
		$nomacom=$row['Nom_acompanante'];// Nombre del acompañante
		$telacom=$row['Tel_acompanante'];// Telefono del acompañante
		$respo=$row['Persona_responsable'];// Persona responsable
		$telres=$row['Tel_responsable'];// Telefono responsable
		$parent=$row['Parentesco'];// Parentesco
		$direccion=$row['Direccion'];// Direccion
		$lugres=$row['Lugar_residencia'];// Lugar de residencia
	
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/pediatra/logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br>MEDICO CIRUJANO PLASTICO<BR>Reg.:</b>".$reg."</b></font>";
		echo "</tr></table>";
		$query="select fecha from	".$empresa."_000002 where Paciente='".$pac."' and Cirujano='".$medico."' and ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fecha1."') order by Fecha";
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
			$querya="select * from	".$empresa."_000002 where Paciente='".$pac."' and Cirujano='".$medico."' and Fecha='".$fec[$y]."' order by Fecha";
			$err = mysql_query($querya,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				
				$row = mysql_fetch_array($err);
				for ($l=0;$l<=$num;$l ++)
				//echo "<br>row[".$l."]= ".$row[$l];
					if($row[$l] == "NO APLICA" or $row[$l] == ".")
				   		$row[$l]="";
				if(!isset($primero))
				{
					/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
					echo "<table align=center border=1 width=725 >";
					echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS GENERALES</b></font></td></tr>";
					if ($n2 == "NO APLICA")
		                $n2 = " ";
					$pacn=$n1." ".$n2." ".$ap1." ".$ap2;// nombre completo
					echo "<tr><td colspan=2 nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$pacn."</td>"; 
					$td=explode('-',$row[6]);
					echo "<td><font size=3  face='arial'><b>D.I:</b> ".$tdoc[0]."-".$doc."</td>";
					echo "<td><font size=3  face='arial'><b>N° HISTORIA:</b> ".$nrohist."</td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>FECHA NACIMIENTO:</b> ".$fecnac." (EDAD AL MOMENTO DE CONSULTA: ".$edad.")</td>";
					echo "<td><font size=3  face='arial'><b>SEXO:</b> ".$sex[1]." </td>";
					echo "<td><font size=3  face='arial'><b>OCUPACION:</b> ".$ocupacion." </td>";
					echo "<td><font size=3  face='arial'><b>TELEFONO:</b> ".$telefonos." </td></tr>";
					echo "<tr><td colspan=2><font size=3  face='arial'><b>DIRECCION:</b> ".$direccion." </td>";
					echo "<td colspan=2><font size=3  face='arial'><b>LUGAR DE RESIDENCIA:</b> ".$lugres." </td></tr>";
					echo "</tr><td><font size=3  face='arial'><b>ENTIDAD:</b> ".$ent[1]." </td>";
					echo "<td><font size=3  face='arial'><b>COTIZANTE / BENEFICIARIO:</b> ".$cb[1]." </td>";
					echo "<td><font size=3  face='arial'><b>NOMBRE ACOMPAÑANTE:</b> ".$nomacom." </td>";
					echo "<td><font size=3  face='arial'><b>TELEFONO ACOMPAÑANTE:</b> ".$telacom." </td></tr>";
					echo "<tr><td colspan=2><font size=3  face='arial'><b>PERSONA RESPONSABLE:</b> ".$respo." </td>";
					echo "<td><font size=3  face='arial'><b>TELEFONO RESPONSABLE:</b> ".$telres." </td>";
					echo "<td><font size=3  face='arial'><b>PARENTESCO:</b> ".$parent." </td></tr></table>";
					$primero="OK";
				}
				echo "<table align=center border=1 width=725 >";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>FECHA HISTORIA CLINICA ".$fec[$y]."</b></font></td></tr>";
				//MOTIVO CONSULTA
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>MOTIVO DE CONSULTA</b></td></tr>";
				if ($row['Motivo_consulta']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>MC:</b>".str_replace( "\n", "<br>", ($row['Motivo_consulta'])  )."</td></tr>";
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>ENFERMEDAD ACTUAL</b></td></tr>";
				if ($row['Enfermedad_actual']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>EA:</b>".str_replace( "\n", "<br>", ($row['Enfermedad_actual'])  )."</td></tr>";
				//ANTECEDENTES PERSONALES
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>ANTECEDENTES PERSONALES</b></td></tr>";
				if ($row['Cirugias']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Cirugias:<B>x</b></td></tr>";
				if ($row['Alergias']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Alergias:<B>x</b></td></tr>";
				if ($row['Traumas']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Traumas:<B>x</b></td></tr>";
				if ($row['Enfermedad']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Enfermedad:<B>x</b></td></tr>";
				if ($row['Hospitalizaciones']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Hospitalizaciones:<B>x</b></td></tr>";
				if ($row['Sangrado']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Sangrado:<B>x</b></td></tr>";
				if ($row['Cigarrillo']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Cigarrillo:<B>x</b></td></tr>";
				if ($row['Alcohol']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Alcohol:<B>x</b></td></tr>";
				if ($row['Drogas']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Drogas:<B>x</b></td></tr>";
				if ($row['Cicatrizacion']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Cicatrizacion:<B>x</b></td></tr>";
				if ($row['Coagulopatias']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Coagulopatias:<B>x</b></td></tr>";
				if ($row['Otras']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Otros:<B>x</b></td></tr>";
				if ($row['Antper_ampliado']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Antecedentes personales ampliado:</b>".str_replace( "\n", "<br>", ($row['Antper_ampliado'])  )."</td></tr>";
				//ANTECEDENTES FAMILIARES
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>ANTECEDENTES FAMILIARES</b></td></tr>";
				if ($row['Diabetes']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Diabetes:<B>x</b></td></tr>";
				if ($row['Cancer']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Cancer:<B>x</b></td></tr>";
				if ($row['Hipertension']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Hipertension:<B>x</b></td></tr>";
				if ($row['Alergias']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Alergias:<B>x</b></td></tr>";
				if ($row['Congenitas']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Congenitas:<B>x</b></td></tr>";
				if ($row['Sangrado1']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Sangrado:<B>x</b></td></tr>";
				if ($row['Otras1']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Otras:<B>x</b></td></tr>";
				if ($row['Antfam_ampliado']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Antecedentes familiares ampliado:</b>".str_replace( "\n", "<br>", ($row['Antfam_ampliado'])  )."</td></tr>";
				//ANTECEDENTES GINECOBSTETRICOS
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>ANTECEDENTES GINECOBSTETRICOS</b></td></tr>";
				if ($row['Menarca']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Menarca:</b>".str_replace( "\n", "<br>", ($row['Menarca'])  )."</td></tr>";
				if ($row['Gestas']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Gestas:</b>".str_replace( "\n", "<br>", ($row['Gestas'])  )."</td></tr>";
				if ($row['Abortos']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Abortos:</b>".str_replace( "\n", "<br>", ($row['Abortos'])  )."</td></tr>";
				if ($row['Partos']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Partos:</b>".str_replace( "\n", "<br>", ($row['Partos'])  )."</td></tr>";
				if ($row['Cesareas']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Cesareas:</b>".str_replace( "\n", "<br>", ($row['Cesareas'])  )."</td></tr>";
				if ($row['Fum']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>FUM:</b>".str_replace( "\n", "<br>", ($row['Fum'])  )."</td></tr>";
				if ($row['Fup']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>FUP:</b>".str_replace( "\n", "<br>", ($row['Fup'])  )."</td></tr>";
				if ($row['Otros']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Otros:</b>".str_replace( "\n", "<br>", ($row['Otros'])  )."</td></tr>";
				if ($row['Antgine_ampliado']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Antecedentes ginecobstericos ampliado:</b>".str_replace( "\n", "<br>", ($row['Antgine_ampliado'])  )."</td></tr>";
				//REVISION DE SISTEMAS
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>REVISION DE SISTEMAS</b></td></tr>";
				if ($row['Neurologico']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Neurologico:<B>x</b></td></tr>";
				if ($row['Circulatorio']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Circulatorio:<B>x</b></td></tr>";
				if ($row['Respiratorio']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Respiratorio:<B>x</b></td></tr>";
				if ($row['Cardiaco']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Cardiaco:<B>x</b></td></tr>";
				if ($row['Endocrino']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Endocrino:<B>x</b></td></tr>";
				if ($row['Urinario']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Urinario:<B>x</b></td></tr>";
				if ($row['Piel']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Piel:<B>x</b></td></tr>";
				if ($row['Osteoarticular']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Osteoarticular:<B>x</b></td></tr>";
				if ($row['Organo_sentidos']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Organos de los sentidos:<B>x</b></td></tr>";
				if ($row['Muscular']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Muscular:<B>x</b></td></tr>";
				if ($row['Digestivo']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Digestivo:<B>x</b></td></tr>";
				if ($row['Inmunologico']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Inmunologico:<B>x</b></td></tr>";
				if ($row['Otro2']!='off')
					echo "<tr><td colspan='4'><font size=3  face='arial'>Otro2:<B>x</b></td></tr>";
				if ($row['Revsis_ampliado']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Revision por sistemas ampliado:</b>".str_replace( "\n", "<br>", ($row['Revsis_ampliado'])  )."</td></tr>";
				//EXAMEN FISICO
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>EXAMEN FISICO</b></td></tr>";
				if ($row['Exafisico_ampliado']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'>".str_replace( "\n", "<br>", ($row['Exafisico_ampliado'])  )."</td></tr>";
				//DIAGNOSTICO
				if ($row['Diagnostico']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>DIAGNOSTICO:</b>".str_replace( "\n", "<br>", ($row['Diagnostico'])  )."</td></tr>";
				if ($row['Otros_diagnosticos']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><B>Otros diagnosticos:</b>".str_replace( "\n", "<br>", ($row['Otros_diagnosticos'])  )."</td></tr>";
				//TRATAMIENTO
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>TRATAMIENTO</b></td></tr>";
				if ($row['Tratamiento']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'>".str_replace( "\n", "<br>", ($row['Tratamiento'])  )."</td></tr>";
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>EXAMENES</b></td></tr>";
				if ($row['Examenes']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'>".str_replace( "\n", "<br>", ($row['Examenes'])  )."</td></tr>";
				
				if($y==($nfechas-1))
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
				else
				{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
				}
				$queryseg="select * from	".$empresa."_000003 where Paciente='".$pac."' and Cirujano='".$medico."' and ".$pre." order by Fecha";
		//ECHO $queryseg;
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						echo "<table align=center border=1 width=725 >";
							echo "<tr><td align=center colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SEGUIMIENTO ".$seg[3]."</b></font></td></tr>";
							echo "<tr><td colspan='4'><font size=3  face='arial'>".str_replace( "\n", "<br>", ($seg['Seguimiento'])  )."</td></tr></table>";
							
						
					}
				}
			}
		}
		echo "<table align=center border=1 width=725 >";
			echo "<tr><td colspan=4><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:</B>Dr.".$nomed."&nbsp&nbsp&nbsp&nbsp<B>REGISTRO:".$reg."</B></td></tr>";
		if ($reg == "4954_85")
			$firma="0800443.png";
		if ($reg == "4613_89")
			$firma="0800210.png";
		if ($reg == "9789_87")
			$firma="0800212.png";
		//if ((file_exists("/var/www/matrix/images/medical/hce/firmas/".$firma))==1)
			//echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/hce/Firmas/".$firma."' width='300' height='200'></td></tr>";
			echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/hce/Firmas/".$firma."' ></td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>