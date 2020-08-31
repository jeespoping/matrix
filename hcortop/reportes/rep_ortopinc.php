<html>

<head>
  <title>HISTORIA ORTOPEDIA V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	        REPORTE DE INCAPACIDAD               *
	*		    PARA ORTOPEDIA	V.1.00	 			 *
	*				CONEX, FREE => OK                *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	


	
		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha1) )
	{
		echo "<form action='rep_ortopinc.php?empresa=$empresa' method=post>";
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
			$query="select distinct Paciente from ".$empresa."_000005 where Cirujano='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";
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
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>Fecha: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='fecha1'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de las incapacidadespara construir el drop down */	
			$query = "select Fecha_inicio  from ".$empresa."_000005 where Paciente='".$pac."' and Cirujano='".$medico."' ";
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
			}
			
		}
		echo"</select></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
		
	}// if de los parametros no estan set
	else
	{	
		$ini1=strpos($medico,"-");
		$reg=substr($medico,0,$ini1);// registro medico
		$nomed=substr($medico,$ini1+1);// nombre medico
		$ini1=explode("-",$pac);
		$td=$ini1[0];
		$doc=$ini1[2];
		$n1=$ini1[3];
		$n2=$ini1[4];
		$ap1=$ini1[5];
		$ap2=$ini1[6];
		$nrohist=$ini1[7];
		$pacn=$n1." ".$n2." ".$ap1." ".$ap2;
		//$pacn=$pacn."<br><b>DOCUMENTO: </b>".$id;		
		
		$query1 = "select Diagnostico_cie  from ".$empresa."_000002 where Paciente like '%".$doc."%' and Cirujano='".$medico."' ";
		$err1 = mysql_query($query1,$conex);
		$row1 = mysql_fetch_array($err1);
					
		echo "<table align=left border=1 width=725 ><tr><td rowspan=4 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/pediatra/logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td></tr>";
		echo "<tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>ORTOPEDIA Y TRAUMATOLOGIA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		if ($reg=='8279_91')
			echo "<br>CIRUGIA DE PIE Y TOBILLO<BR>Reg.:</b>".$reg."</b></font>";
		else
			echo "<br>CIRUGIA DE MANO<BR>Reg.:</b>".$reg."</b></font>";
		echo "</tr></table>";
		//               0          1              2            3          4          5
		$query="select fecha,Tipo_incapacidad,Tipo_riesgo,Fecha_inicio,Numero_dias,Prorroga	 from	".$empresa."_000005 where Paciente='".$pac."' and Cirujano='".$medico."' and Fecha_inicio = '".$fecha1."' order by Fecha";
		$err = mysql_query($query,$conex);
		if($err)
		{
			$row = mysql_fetch_array($err);
			echo "<table border=1 width=725>";
			echo "<tr><td ><b>Nombre: </b>".$pacn."</td></tr>";
			echo "<tr><td ><b>Identificacion: </b>".$td."&nbsp&nbsp".$doc."</td></tr>";
			echo "<tr><td ><b>Historia Clinica: </b>".$nrohist."</td></tr>";
			echo "<tr><td ><b>Diagnostico: </b>".$row1[0]."</td></tr>";
			$tincap=explode("-",$row[1]);
			echo "<tr><td ><b>Tipo Incapacidad: </b>".$tincap[1]."</td>";
			$triesgo=explode("-",$row[2]);
			echo "<tr><td ><b>Tipo riesgo: </b>".$triesgo[1]."</td>";
			echo "<tr><td ><b>Fecha inicio: </b>".$row[3]."</td>";
			echo "<tr><td ><b>Numero dias: </b>".$row[4]."</td>";
			$dias=$row[4] - 1;
			$fecfinal= date("Y-m-d", strtotime("$row[3] + $dias days")); 
			echo "<tr><td ><b>Fecha Final: </b>".$fecfinal."</td>";
			$prorroga=explode("-",$row[5]);
			echo "<tr><td ><b>Prorroga: </b>".$prorroga[1]."</td>";
			
			echo"</table>";
						
			
		}
				
		echo "<table align=left border=1 width=725 >";
			echo "<tr><td colspan=4><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:</B>Dr.".$nomed."&nbsp&nbsp&nbsp&nbsp<B>REGISTRO:".$reg."</B></td></tr>";
			if ($reg=='8279_91')
				echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800022.png ' width='140' height='90'></td></tr>";
			if ($reg=='3649')
				echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800081.png ' width='140' height='90'></td></tr>";
			if ($reg=='86255_02')
				echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800031.png ' width='140' height='90'></td></tr>";
			if ($reg=='9262_91')
				echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800096.png ' width='140' height='90'></td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>	