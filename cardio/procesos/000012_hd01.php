<html>

<head>
  <title>HOSPITALIZACION V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
   /**************************************************
	*       		ORDEN POST PROCEDIMIENTO		 *
	*		  			HEMODINAMIA      			 *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($pac) or !isset($year))
	{
		echo "<form action='000012_hd01.php' method=post>";
		echo "<center><table border=0 width=380>";
		echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		//echo "<tr><td align=center colspan=2><font color=#000066>TORRE MÉDICA</font></td></tr>";
		//echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='pac'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1>";
		if(!isset($year))
		{
			$year=date('Y');
			$month=date('m');
			$day=date('d');
		}
		echo "<select name='year'>";
		for($f=2004;$f<2051;$f++)
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
		echo "<tr><td align='center' bgcolor=#cccccc colspan=2><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";	
	}
	else
	{
		echo "<form action='000012_hd02.php' method=post>";
		$fecha=$year."-".$month."-".$day;
		echo "<input type='hidden' name='fecha' value='".$fecha."'>";
		echo "<center><table border=0 width=600>";
		echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";
		echo "<td bgcolor=#cccccc colspan=1><select name='paciente'>";
		$query="select Paciente from hemo_000012 where Paciente like '%".$pac."%' and Fecha='".$fecha."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."</option>";
			}
		}	// fin $num>0
		echo "</select></td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>TRANSLADAR A: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><select name='traslado'>";
			echo "<option>UCE</option>";
			echo "<option>UCI</option>";
			echo "<option>Unidad Pediatria</option>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>Dieta baja en grasas: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><select name='dieta'>";
			echo "<option>Hiposodica</option>";
			echo "<option>Hipoglucida</option>";
			echo "<option>Hiposodica e Hipoglucida</option>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>REPOSO MIEMBRO INFERIOR: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><select name='miembro'>";
			echo "<option>Izquierdo</option>";
			echo "<option>Derecho</option>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>O2 NASAL (lt/min): </font></td>";
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='o2'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2><font color=#000066>SOLUCION SALINA ENDOVENOSA</font></td>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>* CENTIMETROS CUBICOS: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='liquidos'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>* PORCENTAJE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='valorliq'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>* INFUSIÓN (cc/hora): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='infusionliq'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>NITROGLICERINA INFUSION CONTINUA (cc/hora):</font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='nitro'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>NUBAINE O MORFINA I.V (mg): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='morfina'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>NUBAINE O MORFINA I.V (intervalo horas): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='horamor'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>RANITIDINA (mg): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='ranitidina'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>RANITIDINA (intervalo horas): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='horaran'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CPK-MB EN (horas): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='cpk'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>RETIRAR INTRODUCTOR EN (horas): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='introductor'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HACER TPT ANTES DE (horas): </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='tpt'></td></tr>";
		echo "<tr><td align='center' bgcolor=#cccccc colspan=2><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";	
		echo "<tr></tr>";
		
	}
}
?>
</body>