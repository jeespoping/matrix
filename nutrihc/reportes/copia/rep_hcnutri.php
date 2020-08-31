<html>

<head>
  <title>HISTORIA NUTRICION V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*		  PARA NUTRICION	V.1.00	 			 *
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
		echo "<form action='rep_hcnutri.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a las nutricionistas de la seleccion para 
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
		echo "<img SRC='/MATRIX/images/medical/pediatra/logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br>NUTRICIONISTA<BR>Reg.:</b>".$reg."</b></font>";
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
				//HABITOS
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HABITOS</b></font></td></tr>";
				echo "<td><font size=3  face='arial'><b>Fuma:</b>".$row['Fuma']." </td>";
				if ($row['Licor']!='off') 
					echo "<td><font size=3  face='arial'><b>Licor:</b>".$row['Tipolicor']." </td>";
				if ($row['Licor_otro']!='NO APLICA') 
					echo "<td><font size=3  face='arial'><b>Otro Licor:</b>".$row['Licor_otro']." </td></tr>";
				if ($row['Licorsem']!=0) 
					echo "<tr><td colspan=2><font size=3 face='arial' ><B>Licor semanal: </b>".$row['Licorsem']."</td>";
				if ($row['Licormen']!=0) 
					echo "<tr><td colspan=2><font size=3 face='arial' ><B>Licor mensual: </b>".$row['Licormen']."</td>";
				if ($row['Licor_ocacional']!=0) 
					echo "<tr><td colspan=2><font size=3 face='arial' ><B>Licor ocacional: </b>".$row['Licor_ocacional']."</td>";
				if ($row['Deporte']!='off') 
					echo "<td colspan=4><font size=3 face='arial' ><B>Deporte: </b>".$row['Tipodeporte']."</td>";
				if ($row['Deporsem']!=0) 
					echo "<td colspan=6><font size=3 face='arial' ><B>Deporte semanal: </b>".$row['Deporsem']."</td></tr>";
				if ($row['Medicamentos']!='.') 
					echo "<tr><td colspan=2><font size=3 face='arial' ><B>Medicamentos: </b>".$row['Medicamentos']."</td>";
				//ANTECEDENTES FAMILIARES
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES FAMILIARES</b></font></td></tr>";
				if ($row['Sobrepeso']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Sobrepeso: SI </b></td></tr>";
				if ($row['Bajopeso']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Bajopeso: SI </b></td></tr>";
				if ($row['Hta']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>HTA: SI </b></td></tr>";
				if ($row['Cardiopatias']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cardiopatias: SI </b></td></tr>";
				if ($row['Diabetes']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diabetes: SI</b></td></tr>";
				if ($row['Hipoglicemia']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hipoglicemia: SI </b></td></tr>";
				if ($row['Digestivos']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Digestivos: SI</b></td></tr>";
				if ($row['Cancer']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cancer: SI</b></td></tr>";
				if ($row['Dislipidemia']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Dislipidemia: SI</b></td></tr>";
				if ($row['Sii']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>SII: SI </b></td></tr>";
				if ($row['Obesidad']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Obesidad: SI </b></td></tr>";
				if ($row['Hipotiroidismo']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hipotiroidismo: SI</b></td></tr>";
				if ($row['Otros_anteced']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otros Antecedentes Familiares: </b>".$row['Otros_anteced']."</td></tr>";
				if ($row['Antec_personal']!='.') 
					{
						echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES PERSONALES</b></font></td></tr>";
						echo "<tr><td colspan=12><font size=3 face='arial' ><B>Antecedentes Personales: </b>".$row['Antec_personal']."</td></tr>";
					}
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS DE SALUD</b></font></td></tr>";
				if ($row['Diagnostico1']!='001-.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico Principal: </b>".$row['Diagnostico1']."</td></tr>";
				if ($row['Diagnostico2']!='001-.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico 2: </b>".$row['Diagnostico2']."</td></tr>";
				if ($row['Diagnostico3']!='001-.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico3: </b>".$row['Diagnostico3']."</td></tr>";
				if ($row['Diagnostico4']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico 4: </b>".$row['Diagnostico4']."</td></tr>";
				if ($row['Exa_laboratorio']!='0000-00-00') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fecha Examenes Laboratorio: </b>".$row['Exa_laboratorio']."</td></tr>";
				if ($row['Acido_urico']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Acido_urico: </b>".$row['Acido_urico']."</td></tr>";
				if ($row['Albumina']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Albumina: </b>".$row['Albumina']."</td></tr>";
				if ($row['Colesterol_total']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Colesterol_total: </b>".$row['Colesterol_total']."</td></tr>";
				if ($row['Hdl']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>HDL: </b>".$row['Hdl']."</td></tr>";
				if ($row['Ldl']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>LDL: </b>".$row['Ldl']."</td></tr>";
				if ($row['Vldl']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>VLDL: </b>".$row['Vldl']."</td></tr>";
				if ($row['Trigliceridos']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Trigliceridos: </b>".$row['Trigliceridos']."</td></tr>";
				if ($row['Creatinina']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Creatinina: </b>".$row['Creatinina']."</td></tr>";
				if ($row['Glicemia']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Glicemia: </b>".$row['Glicemia']."</td></tr>";
				if ($row['Hemoglobina_glicosilada']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hemoglobina glicosilada: </b>".$row['Hemoglobina_glicosilada']."</td></tr>";
				if ($row['Hemoglobina']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hemoglobina: </b>".$row['Hemoglobina']."</td></tr>";
				if ($row['Hematocrito']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hematocrito: </b>".$row['Hematocrito']."</td></tr>";
				if ($row['Tsh']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>TSH: </b>".$row['Tsh']."</td></tr>";
				if ($row['Otros_lab']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otros Laboratorios: </b>".$row['Otros_lab']."</td></tr>";
				
				//SINTOMAS
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SINTOMAS</b></font></td></tr>";
				if ($row['Ansiedad']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Ansiedad </b></td></tr>";
				if ($row['Cefalea']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Cefalea </b></td></tr>";
				if ($row['Distension']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Distension </b></td></tr>";
				if ($row['Flatulencia']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Flatulencia </b></td></tr>";
				if ($row['Reflujo']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Reflujo </b></td></tr>";
				if ($row['Otros_sintomas']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otros sintomas: </b>".$row['Otros_sintomas']."</td></tr>";
				
				//SUPLEMENTOS NUTRITIVOS
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>SUPLEMENTOS NUTRITIVOS</b></font></td></tr>";
				if ($row['Suplem_nutritivo']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Suplementos nutritivos: </b>".$row['Suplem_nutritivo']."</td></tr>";
				if ($row['Fibras']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fibras: </b>".$row['Fibras']."</td></tr>";
				if ($row['Come_rapido']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Come rapido: X </b></td></tr>";
				if ($row['Come_despacio']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Come despacio: X</b></td></tr>";
				echo "<tr><td colspan=12><font size=3 face='arial' ><B>Intolerancia alimento: </b>".$row['Intolerancia_alimentoAfam']."</td></tr>";
				if ($row['Intolerancia_otros']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones Suplementos nutritivos:</b>".$row['Intolerancia_otros']."</td></tr>";
				
				//DATOS ANTROPOMETRICOS
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS ANTROPOMETRICOS</b></font></td></tr>";
				if ($row['Peso_actual']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Peso actual: </b>".$row['Peso_actual']."</td></tr>";
				if ($row['Peso_usual']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Peso usual: </b>".$row['Peso_usual']."</td></tr>";
				if ($row['Peso_saludable']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Peso saludable: </b>".$row['Peso_saludable']."</td></tr>";
				if ($row['Peso_embarazo']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Peso inicio embarazo: </b>".$row['Peso_embarazo']."</td></tr>";
				if ($row['Talla']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Talla: </b>".$row['Talla']."</td></tr>";
				echo "<tr><td colspan=12><font size=3 face='arial' ><B>IMC: </b>".$row['Imc']."</td></tr>";
				echo "<tr><td colspan=12><font size=3 face='arial' ><B>Clasificacion: </b>".$row['Imc_desc']."</td></tr>";
				if ($row['Perimetro_cintura']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Perimetro cintura: </b>".$row['Perimetro_cintura']."</td></tr>";
				if ($row['Perimetro_abdominal']!=0) 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Perimetro_abdominal: </b>".$row['Perimetro_abdominal']."</td></tr>";
				if ($row['Diag_nutricional']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico nutricional:</b>".$row['Diag_nutricional']."</td></tr>";
				
				//CONSUMO HABITUAL DE ALIMENTOS
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>CONSUMO HABITUAL DE ALIMENTOS</b></font></td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DESAYUNO</b></font></td></tr>";
				echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hora desayuno: </b>".$row['Desayuno_hora']."</td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>BEBIDAS</b></font></td></tr>";
				if ($row['Desay_jugo']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Jugo: X </b></td></tr>";
				if ($row['Desay_bebidaleche']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Bebida con leche: X </b></td></tr>";
				if ($row['Desay_bebidasinleche']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Bebida sin leche: X </b></td></tr>";
				if ($row['Desay_otrobeb']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Otras Bebidas:</b>".$row['Desay_otrobeb']."</td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>QUESO O SUSTITUTO </b></font></td></tr>";
				if ($row['Desay_queso']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Queso: </b>".$row['Desay_queso']."</td></tr>";
				if ($row['Desay_otrosust']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones queso o sustituto: </b>".$row['Desay_otrosust']."</td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HARINAS </b></font></td></tr>";
				if ($row['Desay_harina']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Harina: </b>".$row['Desay_harina']."</td></tr>";
				if ($row['Desay_otroharina']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones Harinas: </b>".$row['Desay_otroharina']."</td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>GRASAS </b></font></td></tr>";
				if ($row['Desay_grasas']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Grasas: </b>".$row['Desay_grasas']."</td></tr>";
				if ($row['Desay_otrograsa']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones Grasas: </b>".$row['Desay_otrograsa']."</td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>AZUCAR  </b></font></td></tr>";
				if ($row['Desay_azucar']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Azucar: </b>".$row['Desay_azucar']."</td></tr>";
				if ($row['Desay_endulzante']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Endulzante: </b>".$row['Desay_endulzante']."</td></tr>";
				if ($row['Desay_observ']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones Azucar: </b>".$row['Desay_observ']."</td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>MEDIA MAÑANA, ALGO, MERIENDA  </b></font></td></tr>";
				if ($row['Mediamanana']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Mediamañana: X</b></td></tr>";
				if ($row['Algo']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Algo: X </b></td></tr>";
				if ($row['Merienda']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Merienda: X</b></td></tr>";
				if ($row['Med_observ']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones Mediamañana - Algo - Merienda: </b>".$row['Med_observ']."</td></tr>";
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ALMUERZO, COMIDA</b></font></td></tr>";
				if ($row['Almuerzo']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Almuerzo: X </b></td></tr>";
				if ($row['Comida']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Comida: X </b></td></tr>";
				if ($row['Alm_sopa']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Sopa: </b>".$row['Alm_sopa']."</td></tr>";
				if ($row['Alm_carne']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Carne: </b>".$row['Alm_carne']."</td></tr>";
				if ($row['Alm_harinas']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Harinas: </b>".$row['Alm_harinas']."</td></tr>";
				if ($row['Alm_verduras']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Verduras: </b>".$row['Alm_verduras']."</td></tr>";
				if ($row['Alm_leguminosas']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Leguminosas: </b>".$row['Alm_leguminosas']."</td></tr>";
				if ($row['Alm_grasas']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Grasas: </b>".$row['Alm_grasas']."</td></tr>";
				if ($row['Alm_dulces']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Dulces: </b>".$row['Alm_dulces']."</td></tr>";
				if ($row['Alm_jugo']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Jugos: </b>".$row['Alm_jugo']."</td></tr>";
				if ($row['Alm_leche']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Leche: </b>".$row['Alm_leche']."</td></tr>";
				if ($row['Alm_observ']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Observaciones Almuerzo - Comida: </b>".$row['Alm_observ']."</td></tr>";
				
				
				//TRATAMIENTO NUTRICIONAL
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>TRATAMIENTO NUTRICIONAL</b></font></td></tr>";
				if ($row['Normocalorico']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Normocalorico: X </b></td></tr>";
				if ($row['Hipocalorico']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hipocalorico: X </b></td></tr>";
				if ($row['Hipercalorico']!='off') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Hipercalorico: X </b></td></tr>";
				if ($row['Tratam_otros']!='.')
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>OTROS TRATAMIENTOS NUTRICIONALES: </b>".$row['Tratam_otros']."</td></tr>";
				if ($row['Calculo']!='.')
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Calculo: </b>".$row['Calculo']."</td></tr>";
				if ($row['Recomendaciones']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Recomendaciones: </b>".$row['Recomendaciones']."</td></tr>";
				echo "</table>";
				
				//SEGUIMIENTO
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
							if ($seg['Seguimiento']!='.')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Seguimiento Clinico: </b>".$seg['Seguimiento']."</td></tr>";
							if ($seg['Seg_pesoactual']!=0)
								echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Peso actual: </b>".$seg['Seg_pesoactual']."</td></tr>";
							if ($seg['Seg_talla']!=0)
								echo "<tr><td colspan=4 ><font size=3 face='arial' ><B>Talla: </b>".$seg['Seg_talla']."</td></tr>";
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>IMC: </b>".$seg['Imc']."</td></tr>";
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Clasificacion: </b>".$seg['Imc_desc']."</td></tr>";
							if ($seg['Segperimetro_cintura']!=0)
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Perimetro Cintura: </b>".$seg['Segperimetro_cintura']."</td></tr>";
							if ($seg['Segperimetro_abdominal']!=0)
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Perimetro Abdominal: </b>".$seg['Segperimetro_abdominal']."</td></tr>";
							if ($seg['Seg_laboratorio']!='.')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Examenes de Laboratorio: </b>".$seg['Seg_laboratorio']."</td></tr>";
							if ($seg['Seg_tratamiento']!='.')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Tolerancia o aceptacion al tratamiento: </b>".$seg['Seg_tratamiento']."</td></tr>";
							if ($seg['Seg_calculo']!='.')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Calculo: </b>".$seg['Seg_calculo']."</td></tr>";
							if ($seg['Seg_recomendaciones']!='.')
								echo "<tr><td colspan=4><font size=3 face='arial' ><B>Recomendaciones: </b>".$seg['Seg_recomendaciones']."</td></tr>";
						
						echo "</table>";		
					}
				}
				
				
			}
		}
	}
	include_once("free.php");
}
?>