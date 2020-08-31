<html>

<head>
  <title>HISTORIA GINECOLOGIA V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*		  PARA GINECOLOGIA	V.1.00	 			 *
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
		echo "<form action='rep_hcgi.php?empresa=$empresa' method=post>";
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
		echo "<br>MEDICO GINECOLOGO<BR>Reg.:</b>".$reg."</b></font>";
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
				
				
				//ANTECEDENTES GINECOOBTESTRICOS
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES GINECOOBTESTRICOS</b></font></td></tr>";
				
				echo "<tr><td colspan=2><font size=3 face='arial' ><B>Menarquia: </b>".$row['Menarquia']."</td>";
				if ($row['Ciclos']!='NO APLICA') 
					echo "<td colspan=4><font size=3 face='arial' ><B>Ciclos: </b>".$row['Ciclos']."</td>";
				if ($row['Fum']!='NO APLICA') 
					echo "<td colspan=6><font size=3 face='arial' ><B>Fecha Ultima Mestruacion: </b>".$row['Fum']."</td></tr>";
				
				echo "<tr><td colspan=2><font size=3 face='arial' ><B>Gravideces: </b>".$row['Gravideces']."</td>";
				echo "<td colspan=4><font size=3 face='arial' ><B>Paridad: </b>".$row['Paridad']."</td>";
				echo "<td colspan=6><font size=3 face='arial' ><B>Abortos: </b>".$row['Abortos']."</td></tr>";
				echo "<tr><td colspan=2><font size=3 face='arial' ><B>Cesareas: </b>".$row['Cesareas']."</td>";
				if ($row['Pf']!='NO APLICA')
					echo "<td colspan=4><font size=3 face='arial' ><B>Planificacion: </b>".$row['Pf']."</td>";
				echo "<td colspan=6><font size=3 face='arial' ><B>Edad Primer Embarazo: </b>".$row['Pemb']."</td></tr>";
				if ($row['Hijme']!=0)
					echo "<tr><td colspan=2><font size=3 face='arial' ><B>Edad Hijo Menor: </b>".$row['Hijme']."</td>";
				if ($row['Lact']!=0)
					echo "<td colspan=4><font size=3 face='arial' ><B>Meses Lactancia: </b>".$row['Lact']."</td>";
				if ($row['Trh']!='NO APLICA') 
					echo "<td colspan=6><font size=3 face='arial' ><B>Terapia Reemplazo Hormonal: </b>".$row['Trh']."</td></tr>";
				if ($row['Fuc']!='NO APLICA') 
					echo "<tr><td colspan=4><font size=3 face='arial' ><B>Ultima Citologia: </b>".$row['Fuc']."</td>";
				if ($row['Fumam']!='NO APLICA') 
					echo "<td colspan=8><font size=3 face='arial' ><B>Cantidad y Ultima Mamografia: </b>".$row['Fumam']."</td></tr>";
				
				
				//ANTECEDENTES PERSONALES
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ANTECEDENTES PERSONALES</b></font></td></tr>";
				if ($row['Apat']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Antecedentes Patologicos: </b>".$row['Apat']."</td></tr>";
				if ($row['Aqx']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Antecedentes Quirurgicos: </b>".$row['Aqx']."</td></tr>";
				if ($row['Aaler']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Antecedentes Alergicos: </b>".$row['Aaler']."</td></tr>";
				if ($row['Fuma']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Tabaquismo: </b>".$row['Fuma']."</td></tr>";
				if ($row['Afam']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Antecedentes Familiares: </b>".$row['Afam']."</td></tr>";
				if ($row['Revsist']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Revision de Sistemas: </b>".$row['Revsist']."</td></tr>";
				if ($row['Mc_ea']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Motivo Consulta y Enfermedad Actual: </b>".$row['Mc_ea']."</td></tr>";
					
				//EXAMEN FISICO
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>EXAMEN FISICO</b></font></td></tr>";
				if ($row['Des_ef']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Descripcion Examen Fisico: </b>".$row['Des_ef']."</td></tr>";
				if ($row['Peso']!=0)
					echo "<tr><td colspan=2><font size=3 face='arial' ><B>Peso: </b>".$row['Peso']."</td>";
				if ($row['Pulso']!='NO APLICA')
					echo "<td colspan=4><font size=3 face='arial' ><B>Pulso: </b>".$row['Pulso']."</td>";
				if ($row['Pa']!='NO APLICA') 
					echo "<td colspan=4><font size=3 face='arial' ><B>Presion Arterial: </b>".$row['Pa']."</td>";
				if ($row['Tall']!='NO APLICA') 
					echo "<td colspan=2><font size=3 face='arial' ><B>Talla: </b>".$row['Tall']."</td></tr>";
				if ((file_exists("C:/Inetpub/wwwroot/matrix/images/medical/ginehc/".$row['Imagen']))==1)
					echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/ginehc/".$row['Imagen']."' width='300' height='200'></td></tr>";
				if ($row['Fsci']!='.')
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fosa Supraclavicular Izquierda: </b>".$row['Fsci']."</td></tr>";
				if ($row['Fscd']!='.')
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Fosa Supraclavicular Derecha: </b>".$row['Fscd']."</td></tr>";
				if ($row['Axi']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Axila Izquierda: </b>".$row['Axi']."</td></tr>";
				if ($row['Axd']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Axila Derecha: </b>".$row['Axd']."</td></tr>";
				if ($row['Mami']!='NO APLICA')
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Mama Izquierda: </b>".$row['Mami']."</td></tr>";
				if ($row['Mamd']!='NO APLICA')
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Mama Derecha: </b>".$row['Mamd']."</td></tr>";
				if ($row['Abd']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Abdomen: </b>".$row['Abd']."</td></tr>";
				if ($row['Genitales']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Genitales: </b>".$row['Genitales']."</td></tr>";
				if ($row['Diag']!='NO APLICA') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Diagnostico: <i>".$row['Diag']."</b></td></tr>";
				if ($row['Esp_diag']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B>Especificacion Diagnostico: <i>".$row['Esp_diag']."</b></td></tr>";
								
				//CONDUCTA
				echo "<tr><td align=left colspan='12' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>CONDUCTA</b></font></td></tr>";
				
				// ESTUDIOS
				if($y==($nfechas-1))
			
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
			
				else
				{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
			
				}
				
				$queryseg="select * from	".$empresa."_000004 where Paciente='".$pac."' and  Doctor='".$medico."' and ".$pre." order by Fecha";
		
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						/*ESTUDIOS*/
						echo "<table align=center border=1 width=725 >";
						echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>ESTUDIOS ".$seg['Fecha']."</b></font></td></tr>";
						echo "<td  align=left colspan='4'  ><font size='3' face='arial'>";
						
						if ($seg['Estudios']!='.')
						echo "<tr><td colspan=4><font size=3 face='arial' >".$seg['Estudios']."</td></tr>";
						
					}
				}
				
				// PROCEDIMIENTOS
				if($y==($nfechas-1))
			
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
			
				else
				{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
			
				}
				
				$queryseg="select * from	".$empresa."_000005 where Paciente='".$pac."' and  Doctor='".$medico."' and ".$pre." order by Fecha";
		
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						/*PROCEDIMIENTOS*/
						echo "<table align=center border=1 width=725 >";
						echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>PROCEDIMIENTOS ".$seg['Fecha']."</b></font></td></tr>";
						echo "<td  align=left colspan='4'  ><font size='3' face='arial'>";
						
						if ($seg['Proced']!='.')
						echo "<tr><td colspan=4><font size=3 face='arial' >".$seg['Proced']."</td></tr>";
						
					}
				}
				
				// INTERCONSULTAS
				if($y==($nfechas-1))
			
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
			
				else
				{
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
			
				}
				
				$queryseg="select * from	".$empresa."_000006 where Paciente='".$pac."' and  Doctor='".$medico."' and ".$pre." order by Fecha";
		
				$err1 = mysql_query($queryseg,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					for ($x=0;$x<$num1;$x++)
					{
						$seg = mysql_fetch_array($err1);
						/*PROCEDIMIENTOS*/
						echo "<table align=center border=1 width=725 >";
						echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>INTERCONSULTAS ".$seg['Fecha']."</b></font></td></tr>";
						echo "<td  align=left colspan='4'  ><font size='3' face='arial'>";
						
						if ($seg['Intercon']!='.')
						echo "<tr><td colspan=4><font size=3 face='arial' >".$seg['Intercon']."</td></tr>";
						
					}
				}
				
				if ($row['Cons']!='.') 
					echo "<tr><td colspan=12><font size=3 face='arial' ><B> </b>".$row['Cons']."</td></tr>";
					if ((file_exists("C:/Inetpub/wwwroot/matrix/images/medical/ginehc/".$row['Imagen1']))==1)
						echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/ginehc/".$row['Imagen1']."' width='300' height='200'></td></tr>";
				//echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/ginehc/".$row['Imagen1']."' width='300' height='150'></td></tr>";
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
						{
							echo "<tr><td colspan=4><font size=3 face='arial' ><B>Seguimiento: </b>".$seg['Seguimiento']."</td></tr>";
							if ((file_exists("C:/Inetpub/wwwroot/matrix/images/medical/ginehc/".$seg['Imagen2']))==1)
										echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/ginehc/".$seg['Imagen2']."' width='300' height='200'></td></tr>";
						//	echo "<tr><td colspan=12 align='center'><img SRC='/matrix/images/medical/ginehc/".$seg['Imagen2']."' width='300' height='150'></td></tr>";
						}
						echo "</table>";
				
						}
					}
			
			}
		}
	}
	include_once("free.php");
}
?>