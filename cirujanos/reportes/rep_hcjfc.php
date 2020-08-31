<html>

<head>
  <title>HISTORIA NEUROLOGIA V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*		  PARA NEUROLOGIA	V.1.00	 			 *
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
		echo "<form action='rep_hcjfc.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
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
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='fecha1'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select Fecha  from ".$empresa."_000002 where Paciente='".$pac."' and Doctor='".$medico."' ";
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
		echo "<img SRC='\MATRIX\images\medical\pediatra\logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br>MEDICO NEUROLOGO <BR>Reg.:</b>".$reg."</b></font>";
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
					echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA ".$fec[$y]."</b></font></td></tr>";
								
				//MOTIVO DE CONSULTA Y ENFERMEDAD ACTUAL
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>MOTIVO DE CONSULTA Y ENFERMEDAD ACTUAL</b></font></td></tr>";
					if ($row['Mc']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B> </b>".$row['Mc']."</td></tr>";
									
				// REVISION DE SISTEMAS 
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>REVISION DE SISTEMAS</b></font></td></tr>";
					if ($row['Ojosd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Ojos: </b>".$row['Ojosd']."</td></tr>";
					if ($row['Oidosd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Oidos: </b>".$row['Oidosd']."</td></tr>";
					if ($row['Narizd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Nariz: </b>".$row['Narizd']."</td></tr>";							
					if ($row['Bocad']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Boca: </b>".$row['Bocad']."</td></tr>";						
					if ($row['Extremd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Extremidades: </b>".$row['Extremd']."</td></tr>";							
					if ($row['Articd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Articulaciones: </b>".$row['Articd']."</td></tr>";		
					if ($row['Gastrod']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Gastrointestinal: </b>".$row['Gastrod']."</td></tr>";	
					if ($row['Geniurid']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Genitourinario: </b>".$row['Geniurid']."</td></tr>";	
					if ($row['Cardiopd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cardiopulmonar: </b>".$row['Cardiopd']."</td></tr>";	
					if ($row['Pield']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Piel: </b>".$row['Pield']."</td></tr>";	
					if ($row['Columd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Columna: </b>".$row['Columd']."</td></tr>";	
					if ($row['Vascd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Vascular: </b>".$row['Vascd']."</td></tr>";	
					if ($row['Otrosistd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otros sistemas: </b>".$row['Otrosistd']."</td></tr>";	
						
				// ANTECEDENTES PERSONALES		
					echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES PERSONALES</b></font></td></tr>";
					if ($row['Hospd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hospitalizacion: </b>".$row['Hospd']."</td></tr>";
					if ($row['Htad']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hipertension Arterial: </b>".$row['Htad']."</td></tr>";
					if ($row['Diabd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diabetes: </b>".$row['Diabd']."</td></tr>";							
					if ($row['Asmad']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Asma: </b>".$row['Asmad']."</td></tr>";						
					if ($row['Hepatd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hepatitis: </b>".$row['Hepatd']."</td></tr>";							
					if ($row['Dislipd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Dislipidemia: </b>".$row['Dislipd']."</td></tr>";		
					if ($row['Enfacpepd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Enf. Acido-peptica: </b>".$row['Enfacpepd']."</td></tr>";	
					if ($row['Convuld']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Convulsiones: </b>".$row['Convuld']."</td></tr>";	
					if ($row['Tecd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Trauma Encefalocraneano: </b>".$row['Tecd']."</td></tr>";	
					if ($row['Tiroid']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Tiroides: </b>".$row['Tiroid']."</td></tr>";	
					if ($row['Alergid']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Alergias: </b>".$row['Alergid']."</td></tr>";	
					if ($row['Mdxd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Medicamentos: </b>".$row['Mdxd']."</td></tr>";	
					if ($row['Cxd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cirugias: </b>".$row['Cxd']."</td></tr>";	
					if ($row['Embd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Embarazo: </b>".$row['Embd']."</td></tr>";	
					if ($row['Fumad']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cigarrillos: </b>".$row['Fumad']."</td></tr>";	
					if ($row['Licord']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Licor: </b>".$row['Licord']."</td></tr>";	
					if ($row['Otroantpd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otros Antecedente personal: </b>".$row['Otroantpd']."</td></tr>";	
								
				// ANTECEDENTES FAMILIARES		
					echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES FAMILIARES</b></font></td></tr>";
					if ($row['Diabfd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diabetes: </b>".$row['Diabfd']."</td></tr>";							
					if ($row['Corofd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Coronariopatia: </b>".$row['Corofd']."</td></tr>";						
					if ($row['Enfcerefd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Enfermedad Cerebrovascular: </b>".$row['Enfcerefd']."</td></tr>";							
					if ($row['Migrafd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Migraña: </b>".$row['Migrafd']."</td></tr>";		
					if ($row['Movanorfd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Movimientos Anormales: </b>".$row['Movanorfd']."</td></tr>";	
					if ($row['Htafd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hipertension Arterial: </b>".$row['Htafd']."</td></tr>";	
					if ($row['Otroantfd']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otros Antecedentes Familiares: </b>".$row['Otroantfd']."</td></tr>";	
					
				// EXAMEN FISICO
					echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>EXAMEN FISICO</b></font></td></tr>";
					if ($row['Pa']!='NO APLICA') 
						echo "<td colspan=4><font size=3 face='arial' ><B>Presion Arterial: </b>".$row['Pa']."</td>";
					if ($row['Pulso']!='NO APLICA')
						echo "<td colspan=4><font size=3 face='arial' ><B>Pulso: </b>".$row['Pulso']."</td>";
					if ($row['Conciente']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Conciente: </b>".$row['Conciente']."</td></tr>";
					if ($row['Cardiopulmonar']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cardiopulmonar: </b>".$row['Cardiopulmonar']."</td></tr>";
					if ($row['Fo']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fondo de Ojo: </b>".$row['Fo']."</td></tr>";							
					if ($row['Pinr']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Pinr: </b>".$row['Pinr']."</td></tr>";						
					if ($row['Oculomotri']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Oculomotricidad: </b>".$row['Oculomotri']."</td></tr>";							
					if ($row['Fuerza']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fuerza: </b>".$row['Fuerza']."</td></tr>";		
					if ($row['Refle']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Reflejos: </b>".$row['Refle']."</td></tr>";	
					if ($row['Tono']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Tono: </b>".$row['Tono']."</td></tr>";	
					if ($row['Parcran']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Pares Craneanos: </b>".$row['Parcran']."</td></tr>";	
					if ($row['Sensi']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Sensibilidad: </b>".$row['Sensi']."</td></tr>";	
					if ($row['Cerebelo']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cerebelo: </b>".$row['Cerebelo']."</td></tr>";	
					if ($row['Marcha']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Marcha: </b>".$row['Marcha']."</td></tr>";	
					if ($row['Sigmenin']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Signos Meningeos: </b>".$row['Sigmenin']."</td></tr>";	
					if ($row['Babin']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Babinski: </b>".$row['Babin']."</td></tr>";	
					if ($row['Funsup']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Funciones Superiores: </b>".$row['Funsup']."</td></tr>";
					if ($row['Otroexf']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otros Examen Fisico: </b>".$row['Otroexf']."</td></tr>";	
					if ($row['Idx']!='NO APLICA') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Impresion Diagnostica: </b>".$row['Idx']."</td></tr>";	
					
					//CONDUCTA
					echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>CONDUCTA</b></font></td></tr>";
					if ($row['Ciru']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Conducta: </b>".$row['Ciru']."</td></tr>";					
				    if ($row['Seguim']!='.') 
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Seguimiento HC: </b>".$row['Seguim']."</td></tr>";	
					
													

				echo "</table>";
					
					
					if($y==($nfechas-1))
				
						$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
				
					else
					{
						$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
				
					}
					
					$queryseg="select * from	".$empresa."_000003 where Paciente='".$pac."' and  Doctor='".$medico."' and ".$pre." order by Fecha";
			
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
					//		echo "<td  align=left colspan='4'  ><font size='3' face='arial'>";
							
							if ($seg['Seguimiento']!='.')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Seguimiento: </b>".$seg['Seguimiento']."</td></tr>";
								
							
							echo "</table>";
				
						}
					}
			
			}
		}
	}
	include_once("free.php");
}
?>