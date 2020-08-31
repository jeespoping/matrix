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
	*		  PARA MEDICOS INTERNISTAS	V.1.00		 *
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
		echo "<form action='rep_hcmint.php?empresa=$empresa' method=post>";
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
		echo "<img SRC='\MATRIX\images\medical\pediatra\logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br>MEDICO INTERNISTA<BR>Reg.:</b>".$reg."</b></font>";
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
					echo "<tr><td colspan=2 nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$pacn."</td>"; 
					$td=explode('-',$row[6]);
					echo "<td><font size=3  face='arial'><b>D.I:</b> ".$tdoc[0]."-".$doc."</td>";
					echo "<td><font size=3  face='arial'><b>N° HISTORIA:</b> ".$nrohist."</td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>FECHA NACIMIENTO:</b> ".$fecnac." (EDAD: ".$edad.")</td>";
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
				if ($row['Remitido_por']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>REMITIDO POR: </b>".$row['Remitido_por']."</td></tr>";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>FECHA HISTORIA CLINICA ".$fec[$y]."</b></font></td></tr>";
				if ($row['Antec_pers_quirurgico']!='.') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES PERSONALES Y QUIRURGICOS: </b>".$row['Antec_pers_quirurgico']."</td></tr>";
				if ($row['Antec_familiar']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES FAMILIARES: </b>".$row['Antec_familiar']."</td></tr>";
				if ($row['Antec_toxicoalergicos']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES TOXICOALERGICOS: </b>".$row['Antec_toxicoalergicos']."</td></tr>";
				if ($row['Antec_gineco']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ANTECEDENTES GINECOBSTETRICOS: </b>".$row['Antec_gineco']."</td></tr>";
				if ($row['Motivo_consulta_Enfactual']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>MOTIVO DE CONSULTA Y ENFERMEDAD ACTUAL: </b>".$row['Motivo_consulta_Enfactual']."</td></tr>";
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>REVISION POR SISTEMAS</b></td></tr>";
				if ($row['Revsis_cardipulm']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>CARDIOPULMONAR: </b>".$row['Revsis_cardipulm']."</td></tr>";
				if ($row['Revsis_gastrointest']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>GASTROINTESTINAL: </b>".$row['Revsis_gastrointest']."</td></tr>";
				if ($row['Revsis_genitourinario']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>GENITOURINARIO: </b>".$row['Revsis_genitourinario']."</td></tr>";
				if ($row['Revsis_neurologico']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>NEUROLOGICO: </b>".$row['Revsis_neurologico']."</td></tr>";
				if ($row['Revsis_otros']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>REVISION SISTEMAS OTROS: </b>".$row['Revsis_otros']."</td></tr>";
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>EXAMEN FISICO</b></td></tr>";
				if ($row['Condicion_general']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>CONDICIONES GENERALES: </b>".$row['Condicion_general']."</td></tr>";
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>SIGNOS VITALES Y MEDIDAS ANTROPOMETRICAS</b></td></tr>";
				if ($row['Pa']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>PRESION ARTERIAL: </b>".$row['Pa']."</td></tr>";
				if ($row['Pulso']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>PULSO: </b>".$row['Pulso']."</td></tr>";
				if ($row['Peso']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>PESO: </b>".$row['Peso']."</td></tr>";
				if ($row['Talla']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>TALLA: </b>".$row['Talla']."</td></tr>";
				if ($row['Imc']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>IMC: </b>".$row['Imc']."</td></tr>";
				if ($row['Saturacion_o2']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>SATURACION OXIGENO: </b>".$row['Saturacion_o2']."</td></tr>";
				if ($row['Frecuencia_resp']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>FRECUENCIA RESPIRATORIA: </b>".$row['Frecuencia_resp']."</td></tr>";
				echo "<tr><td align=center colspan=4 bgcolor='#cccccc' height='15'><font size=3 face='arial' ><B>HALLAZGOS: </b></td></tr>";
				if ($row['Hallazgo_cabeza']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>CABEZA: </b>".$row['Hallazgo_cabeza']."</td></tr>";
				if ($row['Hallazgo_cuello']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>CUELLO: </b>".$row['Hallazgo_cuello']."</td></tr>";
				if ($row['Hallazgo_cardipulmonar']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>CARDIOPULMONAR: </b>".$row['Hallazgo_cardipulmonar']."</td></tr>";
				if ($row['Hallazgo_abdomen']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>ABDOMEN: </b>".$row['Hallazgo_abdomen']."</td></tr>";
				if ($row['Hallazgo_genitourinario']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>GENITOURINARIO: </b>".$row['Hallazgo_genitourinario']."</td></tr>";
				if ($row['Hallazgo_extremidades']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>EXTREMIDADES: </b>".$row['Hallazgo_extremidades']."</td></tr>";
				if ($row['Hallazgo_neurologico']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>NEUROLOGICO: </b>".$row['Hallazgo_neurologico']."</td></tr>";
				if ($row['Hallazgo_piel']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>PIEL: </b>".$row['Hallazgo_piel']."</td></tr>";
				if ($row['Hallazgo_otros']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>OTROS HALLAZGOS: </b>".$row['Hallazgo_otros']."</td></tr>";
				if ($row['Diagnostico']!='NO APLICA')
					echo "<tr><td colspan='4'><font size=3  face='arial'><b>DIAGNOSTICO:</b> ".$row['Diagnostico']."</td></tr>";
				if ($row['Conducta']!='.')
					echo "<tr><td colspan='4'><font size=3  face='arial'><b>CONDUCTA:</b> ".$row['Conducta']."</td></tr></table>";
				
				
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
						echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SEGIMIENTO ".$seg[3]."</b></font></td></tr>";
						echo "<td  align=left colspan='4'  bgcolor='#AADDFF'><font size='3' face='arial'><b>CONDUCTA</b>";
						echo "<fieldset style='background-color: #FFFFFF; '>".$seg[6]."</fieldset></TR></table>";		
					}
				}
			}
		}
	}
	include_once("free.php");
}
?>