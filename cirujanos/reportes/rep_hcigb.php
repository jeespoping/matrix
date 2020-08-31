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
	*	 PARA EL DOCTOR IGNACIO GONZALEZ	V.1.00	 *
	*				CONEX, FREE => OK                *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente y fecha)
	if(!isset($pac) or !isset($fecha1) or !isset($year) )
	{
		echo "<form action='rep_hcigb.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>TORRE MÉDICA LAS AMERICAS</font></b></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
		/* Si el paciente no esta set construir el drop down */
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes */
			$query="select distinct Paciente from ".$empresa."_000002 where Paciente like '%".$pac1."%' order by Paciente";
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
		//echo "</select></td></tr>";
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
			$query = "select Fecha  from ".$empresa."_000002 where Paciente='".$pac."'";
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
		$ocupacion=$row['Ocupacion']; //Ocupacion
		$direccion=$row['Direccion']; //Direccion
		$ent=explode('-',$row['Entidad']);// Entidad
		$tel=$row['Telefono'];// Telefono del acompañante
		$respo=$row['Persona_responsable'];// Persona responsable
		
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='/matrix/images/medical/pediatra/logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>Dr. Ignacio Gonzalez Borrero";
		echo "<br>Especialista Neurocirugia<BR>Medico Neurocirujano<BR>Reg.:</b> 17352-88</b></font>";
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
					echo "<td colspan=1><font size=3  face='arial'><b>Estado Civil:</b> ".$estciv[1]."</td></tr>";
					echo "<tr><td colspan=1><font size=3  face='arial'><b>Edad:</b> ".$edad."</td>";
					echo "<td><font size=3  face='arial'><b>Fecha:</b> ".$fecdat."</td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>Ocupacion:</b> ".$ocupacion." </td>";
					echo "<td><font size=3  face='arial'><b>Entidad:</b> ".$ent[1]." </td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>Direccion:</b> ".$direccion." </td>";
					echo "<td><font size=3  face='arial'><b>Telefono:</b> ".$tel." </td></tr>";
					echo "<td colspan=2><font size=3  face='arial'><b>Persona Responsable:</b> ".$respo."</td></tr>";
					
					$primero="OK";
				}
				/*HC*/
				echo "<table align=center border=1 width=725 >";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA ".$fec[$y]."</b></font></td></tr>";
				
				if ($row['Motivo_consulta']!='.') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Motivo de Consulta: </b>".$row['Motivo_consulta']."</td></tr>";
				if ($row['Desc_hist']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Enfermedad Actual: </b>".$row['Desc_hist']."</td></tr>";
				if ($row['Revision_sitemas']!='.') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Revision de Sitemas: </b>".$row['Revision_sitemas']."</td></tr>";
				if ($row['Antecedentes']!='.') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Antecedentes: </b>".$row['Antecedentes']."</td></tr>";
				// EXAMEN FISICO
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>EXAMEN FISICO</b></font></td></tr>";
				if ($row['Estado_general']!='.') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Estado General: </b>".$row['Estado_general']."</td></tr>";
				if ($row['Signos_vitales']!='.') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Signos Vitales: </b>".$row['Signos_vitales']."</td></tr>";
				if ($row['Cardiovascular']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Cardiovascular: </b>".$row['Cardiovascular']."</td></tr>";
				if ($row['Ventilatorio']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Ventilatorio: </b>".$row['Ventilatorio']."</td></tr>";
				if ($row['Digestivo']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Digestivo: </b>".$row['Digestivo']."</td></tr>";
				// NEUROLOGICO
					echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>NEUROLOGICO</b></font></td></tr>";
				if ($row['Conciencia']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Conciencia: </b>".$row['Conciencia']."</td></tr>";
				if ($row['Fx_cogn_leng']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Funcion cognitiva y lenguaje: </b>".$row['Fx_cogn_leng']."</td></tr>";
				if ($row['Apa_motor']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Aparato Motor: </b>".$row['Apa_motor']."</td></tr>";
				if ($row['Marcha_estatica']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Marcha Estatica: </b>".$row['Marcha_estatica']."</td></tr>";
				if ($row['Sensibilidad']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Sensitivo: </b>".$row['Sensibilidad']."</td></tr>";
				if ($row['Reflejos']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Reflejos: </b>".$row['Reflejos']."</td></tr>";
				if ($row['Cabezar']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Reflejos Patologicos: </b>".$row['Cabezar']."</td></tr>";
				if ($row['N_cranianos']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Nervios Cranianos: </b>".$row['N_cranianos']."</td></tr>";
				if ($row['Musculoesqueletico_dolor']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Musculoesqueletico y Dolor: </b>".$row['Musculoesqueletico_dolor']."</td></tr>";
				if ($row['Exam_previos']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Examenes Previos: </b>".$row['Exam_previos']."</td></tr>";	
				
				// DIAGNOSTICO Y PLAN DE MANEJO
					echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DIAGNOSTICO Y PLAN DE MANEJO</b></font></td></tr>";
				if ($row['Diagnostico1']!='NO APLICA')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Diagnostico 1: </b>".$row['Diagnostico1']."</td></tr>";
				if ($row['Diagnostico2']!='NO APLICA')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Diagnostico 2: </b>".$row['Diagnostico2']."</td></tr>";
				if ($row['Plan1']!='00-NINGUNO')
				{
					$plan1=explode('-',$row['Plan1']);
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Plan1: </b>".$plan1[1]."</td></tr>";
				}
				if ($row['Plan2']!='00-NINGUNO')
				{
					$plan2=explode('-',$row['Plan2']);
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Plan2: </b>".$plan2[1]."</td></tr>";
				}
				if ($row['Esp_plan']!='.')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Especificacion Plan: </b>".$row['Esp_plan']."</td></tr>";
				if ($row['Cups']!='NO APLICA')
					echo "<tr><td colspan=4 bgcolor='#F2F2F2'><font size=3 face='arial' ><B>Cups: </b>".$row['Cups']."</td></tr>";
				echo "</table>";
								
				if($y==($nfechas-1))
			
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
			
				else
				{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
			
				}
				
				$queryseg="select * from	".$empresa."_000003 where Paciente='".$pac."' and ".$pre." order by Fecha";
		
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
						if ($seg['Segclinico']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Seguimiento Clinico: </b>".$seg['Segclinico']."</td></tr>";
						//if ((file_exists("/var/www/matrix/images/medical/neurohc/".$seg['Exam6']))==1)
						//	echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/neurohc/".$seg['Exam6']."' width='300' height='200'></td></tr>";
						if ($seg['Diagnostico1']!='NO APLICA')
						{
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Diagnostico 1: </b>".$seg['Diagnostico1']."</td></tr>";
						}
						if ($seg['Diagnostico2']!='NO APLICA')
						{
							echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Diagnostico 2: </b>".$seg['Diagnostico2']."</td></tr>";
						}
						if ($seg['Plan1']!='00-NINGUNO')
						{
							$plan1=explode('-',$seg['Plan1']);
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Plan 1: </b>".$plan1[1]."</td></tr>";
						}
						if ($seg['Plan2']!='00-NINGUNO')
						{
							$plan2=explode('-',$seg['Plan2']);
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Plan 2: </b>".$plan2[1]."</td></tr>";
						}
						if ($seg['Esp_plan']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Especificacion Plan: </b>".$seg['Esp_plan']."</td></tr>";
						if ($seg['Cups']!='NO APLICA')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Cups: </b>".$seg['Cups']."</td></tr>";
						echo "</table>";
					}
				}
			}
		}
		echo "<table align=center border=1 width=725>";
		echo "<tr><td colspan='4'><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:<b>DR. IGNACIO GONZALEZ BORRERO &nbsp&nbsp&nbsp&nbsp REGISTRO:17352-88</B></td></tr>";
		//echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/".$key.".png ' width='140' height='90'></td></tr>";
		echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800147.png ' width='140' height='90'></td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>
