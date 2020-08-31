<html>

<head>
  <title>HISTORIA CIRUGIA V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
   /**********************************************************
	*REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS V.1.00	 *
	*********************************************************/
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
		echo "<form action='rep_hcdlgm.php?empresa=$empresa' method=post>";
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
			$query="select distinct Paciente from ".$empresa."_000002 where Doctor='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";
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
			$query = "select Fecha  from ".$empresa."_000002 where Paciente='".$pac."' and Doctor='".$medico."' order by Fecha asc ";
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
		echo"<tr><td bgcolor=#cccccc colspan=2>ENCABEZADO CLINICA LAS AMERICAS:</td><td align=center bgcolor=#cccccc ><input type='checkbox' name='wtip1' value='s'></td></tr>";
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
		if ($wtip1=='s')
			{	
				echo "<img SRC='/MATRIX/images/medical/hcdlgm/logoclinica.jpg' ></td>";
				echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>DIEGO LEON GAVIRIA MENDEZ</b></font></td>";
				echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>CLINICA LAS AMERICAS</b></font>";
				echo "</tr><tr><td  align=center colspan='4'><font size=1 color='#000080' face='arial'><B>DG 75 B # 2 A 80 PI 3 INST. DE LA MUJER";
				echo "<br>Teléfono: 3458343  Medellín - Colombia</b></font>";
			}
		else
			{
				echo "<img SRC='/MATRIX/images/medical/hcdlgm/logocedimujer.jpg' >";
				echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>DIEGO LEON GAVIRIA MENDEZ</b></font></td>";
				echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>CEDIMUJER</b></font>";
				echo "</tr><tr><td  align=center colspan='4'><font size=1 color='#000080' face='arial'><B>Cra. 43b #70S-20, Sabaneta, Antioquia";
				echo "<br>Teléfono: 3458343  Medellín - Colombia</b></font>";
			}
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
		
		$querya="select * from	".$empresa."_000002 where Paciente='".$pac."' and Doctor='".$medico."' and Fecha between '".$fecha1."' and '".$fecha2."' order by Fecha";
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
				if ($row['Mc_ea']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>MOTIVO CONSULTA y ENFERMEDAD ACTUAL: </b>".$row['Mc_ea']."</td></tr>";
				if ($row['Revision_sistemas']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>REVISION SISTEMAS: </b>".$row['Revision_sistemas']."</td></tr>";
				echo "<tr><td align=center colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'>ANTECEDENTES GINECOSBTETRICOS</font></td></tr>";
				echo "<tr><td><font size=3  face='arial'><b>MENARQUIA:</b>".$row['Menarquia']." </td>";
				echo "<td><font size=3  face='arial'><b>GRAVIDECES:</b>".$row['Gravideces']." </td></tr>";
				echo "<tr><td><font size=3  face='arial'><b>PARIDAD:</b>".$row['Paridad']." </td>";
				echo "<td><font size=3  face='arial'><b>ABORTOS:</b>".$row['Abortos']." </td></tr>";
				echo "<tr><td><font size=3  face='arial'><b>CESAREAS:</b>".$row['Cesareas']." </td>";
				echo "<td><font size=3  face='arial'><b>VIVOS:</b>".$row['Vivos']." </td></tr>";
				echo "<tr><td><font size=3  face='arial'><b>FUM:</b>".$row['Fum']." </td>";
				echo "<td><font size=3  face='arial'><b>FPP:</b>".$row['Fpp']." </td></tr>";
				echo "<tr><td><font size=3  face='arial'><b>CICLOS:</b>".$row['Ciclos']." </td>";
				echo "<td><font size=3  face='arial'><b>EDAD GESTACIONAL:</b>".$row['Edad_gestacional']." </td></tr>";
				if ($row['Ag_otros']!='.')
					echo "<tr><td colspan=4 align=left><font size=3 face='arial' ><B>OTROS ANTECEDENTES GINECOBSTETRICOS: </b>".$row['Ag_otros']."</td></tr>";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'></font></td></tr>";
				if ($row['Antec_qx']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES QUIRURGICOS: </b>".$row['Antec_qx']."</td></tr>";
				if ($row['Antec_familiar']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES FAMILIARES: </b>".$row['Antec_familiar']."</td></tr>";
				if ($row['Hallazgos_clinicos']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>HALLAZGOS CLINICOS: </b>".$row['Hallazgos_clinicos']."</td></tr>";
				if ($row['Diagnostico_cie']!='NO APLICA')		
				echo "<tr><td colspan='4'><font size=3  face='arial'><b>DIAGNOSTICO CIE10:</b> ".$row['Diagnostico_cie']."</td></tr>";
				if ($row['Plan']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>PLAN: </b>".$row['Plan']."</td></tr>";
				if ($row['Tratamiento']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>TRATAMIENTO: </b>".$row['Tratamiento']."</td></tr>";
				echo "<tr><td colspan=4><font size=3 face='arial' ><B>PROXIMA CITA: </b>".$row['Proxima_cita']."</td></tr>";		
				echo "</table>";
				
			}
			
		}
		$queryseg="select * from ".$empresa."_000003 where Paciente='".$pac."' and Doctor='".$medico."' and Fecha between '".$fecha1."' and  '".$fecha2."'  order by Fecha";
		//ECHO $queryseg;
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						echo "<table align=center border=1 width=725 >";
						echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SEGUIMIENTO:".$seg['Fecha']."</b></font></td></tr>";
						echo "<tr><td colspan=4><font size=3 face='arial' ><B>SEGUIMIENTO: </b>".$seg['Seguimiento']."</td></tr>";
						echo "<tr><td colspan=4><font size=3 face='arial' ><B>PROXIMA CITA:</b>".$seg['Proxima_citaseg']."</td></tr>";
						echo "</table>";		
					}
				}
		if ($num < 1 and  $num1 < 1)
		{
			echo "<table align=center border=1 width=725 >";
			echo "<td  align=left colspan='4'  bgcolor='#AADDFF'><font size='3' face='arial'><b>SIN DATOS EN LA HISTORIA O SEGUIMIENTOS EN ESTE RANGO DE FECHAS <BR>INICIAL ".$fecha1." Y FINAL ".$fecha2."   </b>";
			echo "</td></table>";	
		}
		echo "<table align=center border=1 width=725>";
		echo "<tr><td colspan='4'><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:<b>DR. DIEGO LEON GAVIRIA MENDEZ&nbsp&nbsp&nbsp&nbspCC.&nbsp3347992&nbsp&nbsp&nbsp&nbsp REGISTRO:5327-87</B></td></tr>";
		echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800327.png ' width='140' height='90'></td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>
