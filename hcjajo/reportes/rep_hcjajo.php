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
	*PARA MEDICOS DIFERENTES ESPECIALIDADES	V.1.00	 *
	**************************************************/
//include_once("root/comun.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	//if(!isset($medico)  or !isset($pac) or !isset($fecha1) or !isset($year) )
	if(!isset($medico)  or !isset($pac) or !isset($year1) or !isset($year) )
	{
		echo "<form action='rep_hcjajo.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los medicos de la seleccion para 
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
		

		if(isset($pac))
		{
			echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";	
			echo "<td bgcolor=#cccccc colspan=2>";
			//Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			// construir el drop down 	
			$query = "select Fecha  from ".$empresa."_000002 where Paciente='".$pac."' and Cirujano='".$medico."' order by Fecha asc ";
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
				if(!isset($year1))
				{
					$year1=date('Y');
					$month1=date('m');
					$day1=date('d');
				}
				echo "<select name='year1'>";
				for($f1=1980;$f1<2051;$f1++)
				{
					if($f1 == $year1)
						echo "<option selected>".$f1."</option>";
					else
						echo "<option>".$f1."</option>";
				}
				echo "</select><select name='month1'>";
				for($f1=1;$f1<13;$f1++)
				{
					if($f1 == $month1)
						if($f1 < 10)
							echo "<option selected>0".$f1."</option>";
						else
							echo "<option selected>".$f1."</option>";
					else
						if($f1 < 10)
							echo "<option>0".$f1."</option>";
						else
							echo "<option>".$f1."</option>";
				}
				echo "</select><select name='day1'>";
				for($f1=1;$f1<32;$f1++)
				{
				if($f1 == $day1)
					if($f1 < 10)
						echo "<option selected>0".$f1."</option>";
					else
						echo "<option selected>".$f1."</option>";
				else
					if($f1 < 10)
						echo "<option>0".$f1."</option>";
					else
							echo "<option>".$f1."</option>";
				}
				echo "</select></td></tr>";
				$fecha1=$year1."-".$month1."-".$day1; //fecha inicial
								
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
		$pacn=$n1." ".$n2." ".$ap1." ".$ap2;// nombre completo
		$fecha1=$year1."-".$month1."-".$day1;
		$fecha2=$year."-".$month."-".$day;
		
		$query="select * from ".$empresa."_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$query=$query."Apellido1='".$ap1."' and Apellido2='".$ap2."' and Documento='".$doc."' ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		
		$telefonos=$row['Telefono']; //telefono
		$tdoc=explode('-',$row['Tip_documento']);// tipo documento
		$fecnac=$row['Fecha_nacimiento']; //Fecha de nacimiento
		
		$fecdat=$row['Fecha_data']; //Fecha del registro
	
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
		echo "<img SRC='/MATRIX/images/medical/hcjajo/logoidc.JPG' width='120' height='90'>";
		echo "<img SRC='/MATRIX/images/medical/pediatra/logotorre.JPG' width='120' height='90'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>Alejo Jiménez Orozco</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>Oncología Clínica</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=1 color='#000080' face='arial'><B>Diag. 75B No 2 A- 80 Torre Médica Las Américas Cons:319";
		echo "<br>Teléfono: 3459105 Fax: 3459135 Medellín - Colombia</b></font>";
		
		echo "</tr></table>";
		if(!isset($primero))
			{
					/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
					echo "<table align=center border=1 width=725 >";
					echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS GENERALES</b></font></td></tr>";
					echo "<tr><td colspan=2 nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$pacn."</td>"; 
					echo "<td><font size=3  face='arial'><b>D.I:</b> ".$tdoc[0]."-".$doc."</td>";
					echo "<td><font size=3  face='arial'><b>N° HISTORIA:</b> ".$nrohist."</td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>FECHA NACIMIENTO:</b> ".$fecnac." </td>";
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
		
		$querya="select * from	".$empresa."_000002 where Paciente='".$pac."' and Cirujano='".$medico."' and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
		$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($y=0;$y<$num;$y++)
			{
				$row = mysql_fetch_array($err);
				for ($l=0;$l<=$num;$l ++)
				//echo "<br>row[".$l."]= ".$row[$l];
					if($row[$l] == "NO APLICA" or $row[$l] == ".")
				   		$row[$l]="";
				
				echo "<table align=center border=1 width=725 >";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA ".$row['Fecha']."</b></font></td></tr>";
				if ($row['Motivo_consulta']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>MOTIVO DE CONSULTA: </b>".$row['Motivo_consulta']."</td></tr>";
				if ($row['Antecedentes_personales']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES PERSONALES: </b>".$row['Antecedentes_personales']."</td></tr>";
				echo "<tr><td><font size=3  face='arial'><b>PA:</b>".$row['Pa']." </td>";
				echo "<td><font size=3  face='arial'><b>PULSO:</b>".$row['Pulso']." </td>";
				echo "<td><font size=3  face='arial'><b>PESO:</b>".$row['Peso']." </td></tr>";
				echo "<tr><td colspan=2><font size=3  face='arial'><b>TALLA:</b> ".$row['Talla']." </td>";
				echo "<td colspan=2><font size=3  face='arial'><b>SUPERFICIE CORPORAL:</b> ".$row['Superficie_corporal']." </td></tr>";
				if ($row['Antecedentes_familiares']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES FAMILIARES: </b>".$row['Antecedentes_familiares']."</td></tr>";
				if ($row['Examen_fisico']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>EXAMEN FISICO: </b>".$row['Examen_fisico']."</td></tr>";
				if ($row['Diagnostico_cie']!='NO APLICA')		
				echo "<tr><td colspan='4'><font size=3  face='arial'><b>DIAGNOSTICO CIE10:</b> ".$row['Diagnostico_cie']."</td></tr>";
				if ($row['Diagnostico']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>DIAGNOSTICO: </b>".$row['Diagnostico']."</td></tr>";
				echo "<td  align=left colspan='4'  bgcolor='#AADDFF'><font size='3' face='arial'><b>CONDUCTA</b>";
				echo "<fieldset style='background-color: #FFFFFF; '>".$row['Conducta']."</fieldset></TR></table>";
				
			}
			
		}
		$queryseg="select * from ".$empresa."_000003 where Paciente='".$pac."' and Cirujano='".$medico."' and Fecha between '".$fecha1."' and  '".$fecha2."'  order by Fecha";
		//ECHO $queryseg;
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						echo "<table align=center border=1 width=725 >";
						echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SEGUIMIENTO ".$seg[3]."</b></font></td></tr>";
						echo "<td  align=left colspan='4'  bgcolor='#AADDFF'><font size='3' face='arial'><b>CONDUCTA</b>";
						echo "<fieldset style='background-color: #FFFFFF; '>".$seg[6]."</fieldset></TR></table>";		
					}
				}
		if ($num < 1 and  $num1 < 1)
		{
			echo "<table align=center border=1 width=725 >";
			echo "<td  align=left colspan='4'  bgcolor='#AADDFF'><font size='3' face='arial'><b>SIN DATOS EN LA HISTORIA O SEGUIMIENTOS EN ESTE RANGO DE FECHAS <BR>INICIAL ".$fecha1." Y FINAL ".$fecha2."   </b>";
			echo "</td></table>";	
		}
		echo "<table align=center border=1 width=725>";
		echo "<tr><td colspan='4'><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:<b>DR. ALEJO JIMENEZ OROZCO &nbsp&nbsp&nbsp&nbspCC.&nbsp71597266&nbsp&nbsp&nbsp&nbsp REGISTRO:6205</B></td></tr>";
		echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800199.png ' width='140' height='90'></td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>
