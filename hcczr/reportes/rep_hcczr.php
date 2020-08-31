<html>

<head>
  <title>HISTORIA ORTOPEDIA V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*		  PARA ORTOPEDIA	V.1.00	 			 *
	*				CONEX, FREE => OK                *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	


	
		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha1) or !isset($year))
	{
		echo "<form action='rep_hcczr.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA LAS AMÉRICAS </font></b></td></tr>";
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
		
		echo"<tr><td bgcolor=#cccccc colspan=2>PLAN Y OBSERVACIONES:</td><td align=center bgcolor=#cccccc ><input type='checkbox' name='wtip1' value='s'></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr>";		
		echo "</form>";
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
		$pacn=$n1." ".$n2; // nombre
		$paca=$ap1." ".$ap2;// apellido
		
		$fecha2=$year."-".$month."-".$day;
		$query="select * from ".$empresa."_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$query=$query."Apellido1='".$ap1."' and Apellido2='".$ap2."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		
		$telefonos=$row['Telefono']; //telefono
		$tdoc=explode('-',$row['Tip_documento']);// tipo documento
		$estciv=explode('-',$row['Estado_civil']);// estado civil
		$fecnac=$row['Fecha_nacimiento']; //Fecha de nacimiento
		//$fn=explode('-',$fecnac);
		//$fecdat=$row['Fecha_data']; //Fecha del registro
		//$fd=explode('-',$fecdat);
		$fecdat= date("Y")."-".date("m")."-".date("d");
		$edad=$fecdat-$fecnac;
		$ocupacion=$row['Ocupacion']; //Ocupacion
		$direccion=$row['Direccion']; //Direccion
		$lugar_residencia=$row['Lugar_residencia']; // lugar de residencia
		$ent=explode('-',$row['Entidad']);// Entidad
		$tel=$row['Telefono'];// Telefono del acompañante
		$respo=$row['Persona_responsable'];// Persona responsable
		
		echo "<table align=center border=1 width=725 ><tr><td rowspan=4 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/pediatra/logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td></tr>";
		echo "<tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>ORTOPEDIA Y TRAUMATOLOGIA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><b>Dr.</b><font size=2 color='#000080' face='arial'><B>".$nomed."";
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
					echo "<td colspan=1><font size=3  face='arial'><b>Lugar residencia:</b> ".$lugar_residencia."</td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>Ocupacion:</b> ".$ocupacion." </td>";
					echo "<td><font size=3  face='arial'><b>Entidad:</b> ".$ent[1]." </td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>Direccion:</b> ".$direccion." </td>";
					echo "<td><font size=3  face='arial'><b>Telefono:</b> ".$tel." </td></tr>";
					echo "<td colspan=2><font size=3  face='arial'><b>Persona Responsable:</b> ".$respo."</td></tr></table>";
					
					$primero="OK";
				}
				/*HC*/
				echo "<table align=center border=1 width=725 >";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA ".$fec[$y]."</b></font></td></tr>";
				
				if ($row['Motivo_consulta']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Motivo de Consulta: </b>".$row['Motivo_consulta']."</td></tr>";
				if ($row['Enfermedad_actual']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Enfermedad Actual: </b>".$row['Enfermedad_actual']."</td></tr>";
				if ($row['Revision_sistemas']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Revision de Sistemas: </b>".$row['Revision_sistemas']."</td></tr>";
				if ($row['Antec_pers_quirurgico']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Antecedentes Personales y Quirurgicos: </b>".$row['Antec_pers_quirurgico']."</td></tr>";
				if ($row['Antec_familiar']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Antecedentes Familiares: </b>".$row['Antec_familiar']."</td></tr>";
				if ($row['Examen_fisico']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Examen Fisico: </b>".$row['Examen_fisico']."</td></tr>";
				if ($row['Paraclinicos']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Paraclinicos: </b>".$row['Paraclinicos']."</td></tr>";
				if ($row['Diagnostico']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Diagnostico: </b>".$row['Diagnostico']."</td></tr>";
				if ($row['Diagnostico_cie']!='NO APLICA')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Diagnostico cie: </b>".$row['Diagnostico_cie']."</td></tr>";
				if ($row['Conducta']!='.')
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Conducta: </b>".$row['Conducta']."</td></tr>";
				
				if ($wtip1=='s')
				{	
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Plan: </b>".$row['Plan']."</td></tr>";
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Observaciones: </b>".$row['Observaciones']."</td></tr>";
				}
				echo "</table>";
								
				if($y==($nfechas-1))
			
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
			
				else
				{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
			
				}
						
				$queryseg="select * from	".$empresa."_000003 where Paciente='".$pac."' and  Cirujano='".$medico."' and ".$pre." order by Fecha";
		
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						/*SEGUIMIENTO*/
						echo "<table align=center border=1 width=725 >";
						echo "<tr><td align=left colspan='4' bgcolor='#81DAF5' height='15'><font size='3'  face='arial'><b>SEGUIMIENTO ".$seg['Fecha']."</b></font></td></tr>";
				//		echo "<td  align=left colspan='4'  ><font size='3' face='arial'>";
						
						if ($seg['Motivo_consulta1']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Motivo de Consulta: </b>".$seg['Motivo_consulta1']."</td></tr>";
						if ($seg['Enfermedad_actual1']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Enfermedad Actual: </b>".$seg['Enfermedad_actual1']."</td></tr>";
						if ($seg['Revision_sistemas1']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Revision de Sistemas: </b>".$seg['Revision_sistemas1']."</td></tr>";
						if ($seg['Examen_fisico1']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Examen Fisico: </b>".$seg['Examen_fisico1']."</td></tr>";
						if ($seg['Paraclinicos1']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Paraclinicos: </b>".$seg['Paraclinicos1']."</td></tr>";
						if ($seg['Diagnostico1']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Diagnostico: </b>".$seg['Diagnostico1']."</td></tr>";
						if ($seg['Diagnostico_cie1']!='NO APLICA')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Diagnostico cie: </b>".$seg['Diagnostico_cie1']."</td></tr>";
						if ($seg['Conducta1']!='.')
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Conducta: </b>".$seg['Conducta1']."</td></tr>";
						
						if ($wtip1=='s')
						{	
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Plan: </b>".$seg['Plan1']."</td></tr>";
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Observaciones: </b>".$seg['Observaciones1']."</td></tr>";
						}
						
						echo "</table>";
				
					}
				}
			
			}
		}
		echo "<table align=center border=1 width=725>";
		//echo "<tr><td colspan='4'><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:<b>DR. CAMILO ZULUAGA RUIZ &nbsp&nbsp&nbsp&nbsp REGISTRO:1193884</B></td></tr>";
		echo "<tr><td colspan=4><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:</B>Dr.".$nomed."&nbsp&nbsp&nbsp&nbsp<B>REGISTRO:".$reg."</B></td></tr>";
		//echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800089.png ' width='140' height='90'></td></tr>";
		if ($reg == "1193884")
			$firma="0800089.png";
		if ($reg == "2785_80")
			$firma="0800359.png";
		if ($reg == "05_1275_15")
			$firma="9800602.png";
		echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/hce/Firmas/".$firma."' ></td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>