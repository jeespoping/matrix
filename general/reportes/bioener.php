<html>

<head>
  <title>HISTORIA V1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#5E2F00">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*			PARA MEDICOS BIOENERGETICOS	V.1.00	 *
	*					CONEX, FREE => OK			 *
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
		echo "<form action='bioener.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#AAAAAA colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#AAAAAA colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'energia' AND codigo = '003'  order by Descripcion ";
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
		echo "</select></tr><tr><td bgcolor=#AAAAAA colspan=1><font color=#000066>PACIENTE: </font></td>";	
	
		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($doc1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			//$query="select DISTINCT Paciente from oftalmo_000003 where Oftalmologo='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";V1.03
			$query="select Documento,Nombre1,Nombre2,Apellido1,Apellido2 from energia_000001 where  Documento like '%".$doc1."%' or Nombre1 like '%".$doc1."%' or";
			$query= $query." Nombre2 like '%".$doc1."%' or Apellido1  like '%".$doc1."%' or Apellido2  like '%".$doc1."%' order by Nombre1";
			echo "</td><td bgcolor=#AAAAAA colspan=2><select name='pac'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					$paci=$row["Documento"]."-".$row["Nombre1"]."-".$row["Nombre2"]."-".$row["Apellido1"]."-".$row["Apellido2"];
					if($paci==$pac)
						echo "<option selected>".$paci."</option>";
					else
						echo "<option>".$paci."</option>";
				}
			}	// fin $num>0
			echo "</select></td></tr>";
			if(isset($pac))
			{
				$ini=strpos($pac,"-");
				echo "</td></tr><input type='hidden' name='doc1' value='".substr($pac,0,$ini)."'>";
			}
			else
				echo "</td></tr><input type='hidden' name='doc1' value='".$doc1."'>";
		}	//fin isset medico
		else 
		{
			echo "</td><td bgcolor=#AAAAAA colspan=2><input type='text' name='doc1'>";
			echo "</td></tr>";
		}
		
		

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			echo "<tr><td bgcolor=#AAAAAA colspan=1><font color=#000066>DESDE: </font></td>";	
			$ini=strpos($pac,"-");
			$query = "select Fecha_consulta from energia_000001 where Documento='".substr($pac,0,$ini)."' and Bioenergetico='".$medico."' ";//and Oftalmologo='".$medico."' "; V 1.03
			//echo $query;
			echo "<td bgcolor=#AAAAAA colspan=2><select name='fecha1'>";
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
				echo "</select></td></tr><tr><td bgcolor=#AAAAAA colspan=1><font color=#000066>HASTA: </font></td>";	
				echo "<td bgcolor=#AAAAAA colspan=2>";
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
		echo"<tr><td align=center bgcolor=#AAAAAA colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/**/
	{
		$ini1=strpos($medico,"-");
		$reg=substr($medico,0,$ini1);
		$nomed=substr($medico,$ini1+1);		
		$query= "select * from energia_000001 where Documento='".$doc1."' and Bioenergetico='".$medico."'";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		if($num > 0)
		{
			$row=mysql_fetch_array($err);
			/*Calculo de la edad*/
			$da=date('Y');
			$dm=date('m');
			$dd=date('d');
		/*	$da=substr($registro[2],0,4);
			$dm=substr($registro[2],5,2);
			$dd=substr($registro[2],8,2); Si es con la fecha*/
			$dias=((integer)$da - (integer)substr($row["Fecha_nacimiento"],0,4) );
			$ann=(integer)substr($row["Fecha_nacimiento"],0,4)*360 +(integer)substr($row["Fecha_nacimiento"],5,2)*30 + (integer)substr($row["Fecha_nacimiento"],8,2);
			$aa=(integer)$da*360 +(integer)$dm*30 + (integer)$dd;
			$ann1=($aa - $ann)/360;			// ESTA ES LA EDAD DECIMAL
			$meses=(($aa - $ann) % 360)/30;
			$edad_pac=(string)(integer)$ann1." Años ".(string)(integer)$meses." Meses ";
			/*Termina el calculo d la edad*/
			echo "<table align=center border=1 width=650 ><tr><td rowspan=3 align='center' colspan='0'>";
			echo "<img SRC='\MATRIX\images\medical\pediatra\logotorre.JPG' width='180' height='117'></td>";
			echo "<td align=center colspan='4' ><font size=4 color='#3C0D00' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
			echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#3C0D00' face='arial'><B>TORRE MEDICA</b></font>";
			echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#3C0D00' face='arial'><B>".$nomed."";
			echo "<br>MEDICO BIOENERGETICO<BR>Reg.:</b>".$reg."</b></font>";
			echo "</tr></table>";
			
			echo "<table align=center border=1 width=650 ><tr><td colspan=5 bgcolor='#AAAAAA' ><font size=2 color='#3C0D00' face='arial'><B>PERSONAL</TD></TR>";
			ECHO "<tr><td colspan=3 width=350><font size=2 color='#3C0D00' face='arial'><b>NOMBRES Y APELLIDOS: ".$row["Nombre1"]." ".$row["Nombre2"]." ".$row["Apellido1"]." ".$row["Apellido2"]."</td>";
			echo "<td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><b>DOCUMENTO: ".$row["Documento"]." de ".$row["Lugar_identificacion"]."</td></tr>";
			
			ECHO "<tr><td COLSPAN=3 width=350><font size=2 color='#3C0D00' face='arial'><b>FECHA Y LUGAR DE NACIMIENTO:</b>".$row["Fecha_nacimiento"]." en ".$row["Lugar_nacimiento"]."</td>";
			echo "<td><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$edad_pac."</td>";
			echo "<td><font size=2 color='#3C0D00' face='arial'><b>SEXO:</B> ".substr($row["Sexo"],3)."</td></tr>";
			
			ECHO "<tr><td COLSPAN=3 width=350><font size=2 color='#3C0D00' face='arial'><b>TELEFONOS: Res.</b>".$row["Telefono_casa"]." <b>Oficina</b> ".$row["Telefono_oficina"]."</td>";
			echo "<td COLSPAN=2 width=350><font size=2 color='#3C0D00' face='arial'><B>BEEPER/CEL:</B> ".$row["Telefono_cel_beeper"]."</td>";
			
			echo "<tr><td COLSPAN=3 width=350><font size=2 color='#3C0D00' face='arial'><b>EMAIL:</B> ".$row["Email"]."</td>";
			echo "<td COLSPAN=2 width=350><font size=2 color='#3C0D00' face='arial'><b>DIRECCION:</B> ".$row["Direccion"]."</td>";
			
			echo "<tr><td COLSPAN=3 width=350><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row["Profesion"]."</td>";
			echo "<td COLSPAN=2 width=350><font size=2 color='#3C0D00' face='arial'><b>OCUPACION:</B> ".$row["Ocupacion"]."</td></tr>";
			
			echo "<tr><td colspan=1 ><font size=2 color='#3C0D00' face='arial'><B>ESTADO CIVIL: ";
			echo "<td><font size=2 color='#3C0D00' face='arial'><b>Soltero(</b>".$row["Soltero"]."<b>)</td>";
			echo "<td><font size=2 color='#3C0D00' face='arial'><b>Casado(</b>".$row["Casado"]."<b>)</td>";
			
			echo "<td COLSPAN=2 rowspan=2 bgcolor=#FFECCE><font size=2 color='#3C0D00' face='arial'><B>COMENTARIO</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Comentario"]."</fieldset></td></tr>";
			
			echo "<tr><td><font size=2 color='#3C0D00' face='arial'><b>Separado(</b>".$row["Separado"]."<b>)</td>";
			echo "<td><font size=2 color='#3C0D00' face='arial'><b>Viudo(</b>".$row["Viudo"]."<b>)</td>";
			echo "<td><font size=2 color='#3C0D00' face='arial'><b>Union Libre(</b>".$row["Union_libre"]."<b>)</td></TABLE>";
			$i=1;
			if ($row["Conyuge_nombres"] != "NO APLICA")
			{
				$esp=1;
				$tit[$i]="CONYUGE ";
				$nom[$i]="Conyuge_nombres";
				$edad[$i]="Conyuge_edad";
				$prof[$i]="Conyuge_profesion";
				$ocup[$i]="Conyuge_ocupacion";
				$i++;
			}
			if ($row["Hijo1_nombre"] != "NO APLICA")
			{
				$tit[$i]="HIJO ";
				$nom[$i]="Hijo1_nombre";
				$edad[$i]="Hijo1_edad";//echo "edad[$i]=".$edad[$i]."<br>";
				$prof[$i]="Hijo1_profesion";
				$ocup[$i]="Hijo1_ocupacion";
				$i++;
			}
			if ($row["Hijo2_nombre"] != "NO APLICA")
			{
				$tit[$i]="HIJO ";
				$nom[$i]="Hijo2_nombre";
				$edad[$i]="Hijo2_edad";
				$prof[$i]="Hijo2_profesion";
				$ocup[$i]="Hijo2_ocupacion";
				$i++;
			}
			if ($row["Hijo3_nombre"] != "NO APLICA")
			{
				$tit[$i]="HIJO ";
				$nom[$i]="Hijo3_nombre";
				$edad[$i]="Hijo3_edad";
				$prof[$i]="Hijo3_profesion";
				$ocup[$i]="Hijo3_ocupacion";
				$i++;
			}
			echo "<table align=center border=1 width=650 >";
			$a=1;
			$c=1;
			$color="";
			$color='#DCD2C6';
			$color1='#FFECCE';
			while($i != 1 and $i != 2 )
			{
				if($c % 2 == 0)
				{
					$color='#DCD2C6';
					$color1='#FFECCE';
					$c++;
				}
				else
				{
					$color1='#DCD2C6';
					$color='#FFECCE';
					$c++;
				}
				echo "<tr><td colspan=2 bgcolor=".$color." width=350><font size=2 color='#3C0D00' face='arial'><B>".$tit[$a].": ".$row[$nom[$a]]."</TD>";
				echo "<td colspan=2 bgcolor=".$color1." width=350><font size=2 color='#3C0D00' face='arial'><B>".$tit[$a+1].": ".$row[$nom[$a+1]]."</TD>";
				
				echo "<tr><td colspan=1 ><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row[$edad[$a]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row[$prof[$a]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row[$edad[$a+1]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row[$prof[$a+1]]."</TD>";
				
				echo "<tr><td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row[$ocup[$a]]."</TD>";
				echo "<td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row[$ocup[$a+1]]."</TD>";
				
				$a+=2;
				$i=$i-2;
			}
			if( $i == 2)
			{
				if($c % 2 == 0)
				{
					$color='#DCD2C6';
					$color1='#FFECCE';
					$c++;
				}
				else
				{
					$color1='#DCD2C6';
					$color='#FFECCE';
					$c++;
				}
				echo "<tr><td bgcolor=".$color." colspan=2 width=350 ><font size=2 color='#3C0D00' face='arial'><B>".$tit[$a].": ".$row[$nom[$a]]."</TD>";
				
				echo "<td  rowspan=3 bgcolor=".$color1." colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>COMENTARIO</B>";
				echo "<fieldset style='background-color: #FFFFFF'>".$row["Comentario1"]."</fieldset></td></tr>";
				
				echo "<tr><td colspan=1><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row[$edad[$a]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row[$prof[$a]]."</TD>";
				
				echo "<tr><td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row[$ocup[$a]]."</TD>";
				
			}
			if($i == 1)
			{
				echo "<tr><td  colspan=4 bgcolor=#AACCCC><font size=2 color='#3C0D00' face='arial'><B>COMENTARIO</B>";
				echo "<fieldset style='background-color: #FFFFFF'>".$row["Comentario1"]."</fieldset></td></tr>";
			}
			
			echo "<tr><td  colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>ANTECEDENTES PATOLOGICOS Y Qx</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Antecedentes"]."</fieldset></td>";
			echo "<td  colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>TRATAMIENTO ACTUAL</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Tratamiento"]."</fieldset></td>";
			
			echo "<tr><td colspan=4 bgcolor=#AAAAAA><font size=2 color='#3C0D00' face='arial'><B>EVENTOS IMPORTANTES QUE MARCARON SU VIDA</TD></TR>";
			for ($i=1;$i<=10;$i++)
			{
				if($row["Evento".$i] != "NO APLICA")
					echo "<tr><td colspan=4><font size=2 color='#3C0D00' face='arial'><b>Evento".$i.": </b>".$row["Evento".$i]."</TD></TR>";
			}
			echo "<tr><td colspan=5 bgcolor='#AAAAAA'><font size=2 color='#3C0D00' face='arial'><B>AMBIENTE FAMILIAR</TD></TR>";
			echo "<tr><td colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>PADRE: ".$row["Padre_nombre"]."</TD>";
			echo "<td colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>MADRE: ".$row["Madre_nombre"]."</TD>";
			
			echo "<tr><td colspan=1><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row["Padre_edad"]."</TD>";
			echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row["Padre_profesion"]."</TD>";
			echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row["Madre_edad"]."</TD>";
			echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row["Madre_profesion"]."</TD>";
			
			echo "<tr><td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row["Padre_ocupacion"]."</TD>";
			echo "<td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row["Madre_ocupacion"]."</TD>";
			
			echo "<tr><td  colspan=4 bgcolor=#AACCCC><font size=2 color='#3C0D00' face='arial'><B>COMENTARIO</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Comentario_padres"]."</fieldset></td></tr>";
			
			$i=1;
			if ($row["Hermano1_nombre"] != "NO APLICA")
			{
				$tit[$i]="HERMANO ";
				$nom[$i]="Hermano1_nombre";
				$edad[$i]="Hermano1_edad";
				$prof[$i]="Hermano1_profesion";
				$ocup[$i]="Hermano1_ocupacion";
				$desc[$i]="Hermano1_descripcion";
				$i++;
			}
			if ($row["Hermano2_nombre"] != "NO APLICA")
			{
				$tit[$i]="HERMANO ";
				$nom[$i]="Hermano2_nombre";
				$edad[$i]="Hermano2_edad";
				$prof[$i]="Hermano2_profesion";
				$ocup[$i]="Hermano2_ocupacion";
				$desc[$i]="Hermano2_descripcion";
				$i++;
			}
			if ($row["Hermano3_nombre"] != "NO APLICA")
			{
				$tit[$i]="HERMANO ";
				$nom[$i]="Hermano3_nombre";
				$edad[$i]="Hermano3_edad";
				$prof[$i]="Hermano3_profesion";
				$ocup[$i]="Hermano3_ocupacion";
				$desc[$i]="Hermano3_descripcion";
				$i++;
			}
			$a=1;
			while($i != 1 and $i != 2 )
			{
				if($c % 2 == 0)
				{
					$color='#DCD2C6';
					$color1='#FFECCE';
					$c++;
				}
				else
				{
					$color1='#DCD2C6';
					$color='#FFECCE';
					$c++;
				}
				echo "<tr><td colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>".$tit[$a].": ".$row[$nom[$a]]."</TD>";
				echo "<td colspan=2 width=350 bgcolor=".$color1." ><font size=2 color='#3C0D00' face='arial'><B>".$tit[$a+1].": ".$row[$nom[$a+1]]."</TD>";
				
				echo "<tr><td colspan=1><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row[$edad[$a]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row[$prof[$a]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row[$edad[$a+1]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row[$prof[$a+1]]."</TD>";
				
				echo "<tr><td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row[$ocup[$a]]."</TD>";
				echo "<td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row[$ocup[$a+1]]."</TD>";
				
				echo "<tr><td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>DESCRIPCION:</B> ".$row[$desc[$a]]."</TD>";
				echo "<td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>DESCRIPCION:</B> ".$row[$desc[$a+1]]."</TD>";
				
				$a+=2;
				$i=$i-2;
			}
			if( $i == 2)
			{
				if($c % 2 == 0)
				{
					$color='#DCD2C6';
					$color1='#FFECCE';
					$c++;
				}
				else
				{
					$color1='#DCD2C6';
					$color='#FFECCE';
					$c++;
				}
				echo "<tr><td colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>".$tit[$a].": ".$row[$nom[$a]]."</TD>";
				
				echo "<td  rowspan=4 bgcolor=".$color1." colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>COMENTARIO</B>";
				echo "<fieldset style='background-color: #FFFFFF'><b>Lugar en el grupo familiar:</b> ".$row["Lugar_grupo_fam"]."<br>".$row["Comentarios_hermanos"]."</fieldset></td></tr>";
				
				echo "<tr><td colspan=1><font size=2 color='#3C0D00' face='arial'><B>EDAD:</B> ".$row[$edad[$a]]."</TD>";
				echo "<td colspan=1><font size=2 color='#3C0D00' face='arial'><B>PROFESION:</B> ".$row[$prof[$a]]."</TD>";
				
				echo "<tr><td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>OCUPACION:</B> ".$row[$ocup[$a]]."</TD>";
				echo "<tr><td colspan=2 width=350><font size=2 color='#3C0D00' face='arial'><B>DESCRIPCION:</B> ".$row[$desc[$a]]."</TD>";
				
			}
			if($i == 1)
			{
				echo "<tr><td  colspan=4 bgcolor=#AACCCC><font size=2 color='#3C0D00' face='arial'><B>COMENTARIO</B>";
				echo "<fieldset style='background-color: #FFFFFF'><b>Lugar en el grupo familiar:</b> ".$row["Lugar_grupo_fam"]."<br>".$row["Comentarios_hermanos"]."</fieldset></td></tr>";
			}
			echo "<tr><td  colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>ANTECEDENTES FAMILIARES<BR>";
			echo "(eventos importantes,enfermedades,fallecimientos,etc.)</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Antecedentes_familiares"]."</fieldset></td>";
			echo "<td  colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>AMBIENTE SOCIAL<br>";
			echo "(actividades, descanso, grupos, deportes, relaciones afectivas, etc.)</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Ambiente_social"]."</fieldset></td>";
			
			echo "<tr><td  colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>AMBIENTE LABORAL";
			echo "(descripción, sentimientos hacia, aspiraciones, metas)</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Ambiente_laboral"]."</fieldset></td>";
			echo "<td  colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>AMBIENTE ESPIRITUAL";
			echo "¿Como vive su espiritualidad?Su relación con Dios?</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Ambiente_espiritual"]."</fieldset></td>";
			
			echo "<tr><td colspan=4 bgcolor='#AAAAAA'><font size=2 color='#3C0D00' face='arial'><B>CONSULTA ".$row["Fecha_consulta"]."</TD></TR>";
			echo "<tr><td colspan=4><font size=2 color='#3C0D00' face='arial'><b>¿COMO SE ENTERO DE LA CONSULTA? (familiares y/o amigos que vienen o han venido a consulta)</b></tr>";
			echo "<tr><td colspan=4><font size=2 color='#3C0D00' face='arial'>".$row["Entero_consulta"]."</TD></TR>";
			
			echo "<tr><td  colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>MOTIVO DE CONSULTA ¿Que piensa de su enfermedad?</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Motivo_consulta"]."</fieldset></td>";
			echo "<td  colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>EVALUACION MEDICA</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Evaluacion_medica"]."</fieldset></td></tr>";
			
			echo "<tr><td  colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>TRATAMIENTO</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Tratamiento"]."</fieldset></td>";
			echo "<td  colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>PLAN DE TRABAJO</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Plan_trabajo"]."</fieldset></td></tr>";
			
			echo "<td  colspan=4 bgcolor=#AACCCC><font size=2 color='#3C0D00' face='arial'><B>FORMULACION</B>";
			echo "<fieldset style='background-color: #FFFFFF'>".$row["Formulacion"]."</fieldset></td></tr>";
			
			/*Buscar los seguimientos del paciente*/
			$query= "select * from energia_000002 where Paciente ='".$row["Documento"]."-".$row["Nombre1"]."-".$row["Nombre2"]."-".$row["Apellido1"]."-".$row["Apellido2"]."'";
			$query= $query." and Fecha between '".$fecha1."' and '".$year."-".$month."-".$day."' and Bioenergetico='".$medico."'";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			if($num > 0)
			{
				for($j=0;$j<$num;$j++)
				{
					$row=mysql_fetch_array($err);
					echo "<tr><td  colspan=4 bgcolor='#AAAAAA'><font size=2 color='#3C0D00' face='arial'><B>SEGUIMIENTO ".$row["Fecha"]."</TD></TR>";
					echo "<tr><td  colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>MOTIVO DE CONSULTA ¿Que piensa de su enfermedad?</B>";
					echo "<fieldset style='background-color: #FFFFFF'>".$row["Motivo_consulta"]."</fieldset></td>";
					echo "<td  colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>TRATAMIENTO</B>";
					echo "<fieldset style='background-color: #FFFFFF'>".$row["Tratamiento"]."</fieldset></td>";
					
					echo "<tr><td  colspan=2 width=350 bgcolor=".$color."><font size=2 color='#3C0D00' face='arial'><B>PLAN DE TRABAJO</B>";
					echo "<fieldset style='background-color: #FFFFFF'>".$row["Plan_trabajo"]."</fieldset></td>";					
					echo "<td  colspan=2 width=350 bgcolor=".$color1."><font size=2 color='#3C0D00' face='arial'><B>FORMULACION</B>";
					echo "<fieldset style='background-color: #FFFFFF'>".$row["Formulacion"]."</fieldset></td></tr>";
					
					echo "<td  colspan=4 bgcolor=#AACCCC><font size=2 color='#3C0D00' face='arial'><B>COMENTARIOS</B>";
					echo "<fieldset style='background-color: #FFFFFF'>".$row["Comentarios"]."</fieldset></td></tr>";
					
					
					
				}//fin del for de impresion de seguimientos
			}//fin de $num>0 en query de seguimientos
		}
		
	}
}
include_once("free.php");
?>
</body>
</html>
