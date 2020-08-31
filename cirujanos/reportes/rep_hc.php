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
	*		  PARA MEDICOS CIRUJANOS	V.1.00		 *
	*				CONEX, FREE => OK                *
	**************************************************/
$empresa='ciruhc';
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha1) or !isset($year) )
	{
		echo "<form action='rep_hc.php' method=post>";
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
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			$query="select distinct Paciente from ".$empresa."_000003 where Cirujano='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";
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
			$query = "select Fecha  from ".$empresa."_000003 where Paciente='".$pac."' and Cirujano='".$medico."' ";
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
				/*<select name='fecha2'>";
				for ($j=0;$j<=$num;$j++)
				{	
					echo "<option>".$fec[$j]."</option>";
				}*/
			}
			
		}
		//echo"</select>
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	{
		$ini1=strpos($medico,"-");
		$reg=substr($medico,0,$ini1);
		$nomed=substr($medico,$ini1+1);
		$ini1=strpos($pac,"-");
		$nrohist=substr($pac,0,$ini1);
		$ini2=strpos($pac,"-",$ini1+1);
		$n1=substr($pac,$ini1+1,$ini2-$ini1-1);
		$ini3=strpos($pac,"-",$ini2+1);
		$n2=substr($pac,$ini2+1,$ini3-$ini2-1);
		$ini4=strpos($pac,"-",$ini3+1);
		$ini5=strpos($pac,"-",$ini4+1);
		$ap1=substr($pac,$ini3+1,$ini4-$ini3-1);
		$ap2=substr($pac,$ini4+1,$ini5-$ini4-1);
		$doc=substr($pac,$ini5+1);
		$pacn=$n1." ".$n2." ".$ap1." ".$ap2;
		//echo $pacn;
		$fecha2=$year."-".$month."-".$day;
		$query="select Telefonos from ".$empresa."_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$query=$query."Apellido1='".$ap1."' and Apellido2='".$ap2."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			$row = mysql_fetch_row($err);
			$telefonos=$row[0];
		}
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='\MATRIX\images\medical\pediatra\logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br>MEDICO CIRUJANO<BR>Reg.:</b>".$reg."</b></font>";
		echo "</tr></table>";
		$query="select fecha from	".$empresa."_000003 where Paciente='".$pac."' and Cirujano='".$medico."' and ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fecha1."') order by Fecha";
		$err = mysql_query($query,$conex);
		$nfechas = mysql_num_rows($err);
		if($nfechas>0)
		{
			for ($j=0;$j<$nfechas;$j++)
			{			
				$row = mysql_fetch_array($err);
						$fec[$j]=substr($row[0],0,10);
						//echo "fec[".$j."]=".$fec[$j]."<br>";
			}
		}
		
		for ($y=0;$y<$nfechas;$y++)
		{
			$querya="select * from	".$empresa."_000003 where Paciente='".$pac."' and Cirujano='".$medico."' and Fecha='".$fec[$y]."' order by Fecha";
			$err = mysql_query($querya,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				
				$row = mysql_fetch_array($err);
				for ($l=0;$l<=23;$l ++)
				//echo "<br>row[".$l."]= ".$row[$l];
					if($row[$l] == "NO APLICA" or $row[$l] == ".")
				   		$row[$l]="";
				if(!isset($primero))
				{
					/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
					echo "<table align=center border=1 width=725 >";
					echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>DATOS GENERALES</b></font></td></tr>";
					echo "<tr><td colspan=2 nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$pacn."</td>"; 
					echo "<td><font size=3  face='arial'><b>D.I:</b> ".substr($row['Tipo_Documento'],0,2)."-".$doc."</td>";
					echo "<td><font size=3  face='arial'><b>N° HISTORIA:</b> ".$nrohist."</td></tr>";
					echo "<tr><td><font size=3  face='arial'><b>FECHA NACIMIENTO:</b>".$row['Fecha_nacimiento']." </td>";
					echo "<td><font size=3  face='arial'><b>SEXO:</b> ".STRTOLOWER(SUBSTR($row['Sexo'],3))." </td>";
					echo "<td><font size=3  face='arial'><b>OCUPACION:</b> ".$row['Ocupacion']." </td>";
					echo "<td><font size=3  face='arial'><b>TELEFONOS:</b>".$telefonos." </td></table>";
					$primero="OK";
				}
				$ent=strpos($row['Entidad'],"-");
				echo "<table align=center border=1 width=725 >";
				echo "<tr><td align=left colspan='4' bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>HISTORIA CLINICA ".$fec[$y]."</b></font></td></tr>";
				echo "<tr><td><font size=3 face='arial' ><B>ENTIDAD: </b>".substr($row['Entidad'],$ent+1)."</td>"; 
				echo "<td><font size=3  face='arial'><b>PA:</b>".$row['Pa']." </td>";
				echo "<td><font size=3  face='arial'><b>PULSO:</b>".$row['Pulso']." </td>";
				echo "<td><font size=3  face='arial'><b>PESO:</b>".$row['Peso']." </td></tr>";
				echo "<tr><td><font size=3  face='arial'><b>CABEZA Y CUELLO:</b>".$row['Cabeza_cuello']." </td>";
				echo "<td  align=left rowspan='3' colspan='3' bgcolor='#AADDFF'><font size='3' face='arial'><b>ABDOMEN</b>";
				echo "<fieldset style='background-color: #FFFFFF; '>".$row['Abdomen']."</fieldset>";
				echo "<tr><td><font size=3  face='arial'><b>TORAX:</b>".$row['Torax']."</td>";
				echo "<tr><td><font size=3  face='arial'><b>EXTREMIDADES:</b>".$row['Extremidades']."</td></tr>";
				echo "<tr><td colspan='4'><font size=3  face='arial'><b>DIAGNOSTICO:</b>".$row['Diagnostico']."</td></tr>";
				echo "<td  align=left colspan='4'  bgcolor='#AADDFF'><font size='3' face='arial'><b>CONDUCTA</b>";
				echo "<fieldset style='background-color: #FFFFFF; '>".$row['Conducta']."</fieldset></TR></table>";
				
				if($y==($nfechas-1))
			//$pre="(Fecha >= ".$fec[$y]." or Fecha = ".$fecha2.")";	
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."'or Fecha = '".$fec[$y]."')";
			//$fecfin=$fecha2;
				else
				{
			//$fecfin=$fec[$y+1];
			//$pre= "(Fecha >= ".$fec[$y]." or Fecha < ".$fec[$y+1].")";
					$pre="((Fecha > '".$fec[$y]."' and Fecha < '".$fec[$y+1]."') or Fecha = '".$fec[$y]."')";
			//echo "y!= nfechas<br> *y=".$y."*  nfechas=".$nfechas."*  fec[y]=".$fec[$y]."*<br>";	
				}
				$queryseg="select * from	".$empresa."_000004 where Paciente='".$pac."' and Cirujano='".$medico."' and ".$pre." order by Fecha";
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