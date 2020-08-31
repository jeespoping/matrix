<html>

<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
 
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha1)  )
	{
		echo "<form action='' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>PROMOTORA MEDICA LAS AMERICAS S.A. </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>EVENTO (Porcentaje de asistencia)</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>EVENTO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT DISTINCT nombre_evento  FROM evento_000002 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if (($row[0]."-".$row[1]) == $medico)
						echo "<option selected>".$row[0]."</option>";
					else
						echo "<option>".$row[0]."</option>";
				}
			}	// fin del if $num>0
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>UNIDAD: </font></td>";	
	
		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			//$query="select DISTINCT Paciente from oftalmo_000003 where Oftalmologo='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";V1.03
			$query="select DISTINCT unidad from evento_000001 ";
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
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='fecha1'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select DISTINCT Fecha  from EVENTO_000001 ";//and Oftalmologo='".$medico."' "; V 1.03
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=1;$j<=$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					$fec[$j]=$row[0];
					echo "<option>".$row[0]."</option>";
				}
				echo "</select></td></tr>";
			
				}
			
			}
		//echo"</select>
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/***************************************************************
		  APARTIR DE AQUI EMPIEZA LA IMPRESION DE LA HISTORIA		*/
	{
		
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='\MATRIX\images\medical\general\logo_promo.GIF' width='416' height='125'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>EVENTO</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$medico."";
		echo "</tr></table>";
		
		
	
		$query="select * "
		                ."    from evento_000001 "
		                ."  where Unidad='".$pac."' and fecha='".$fecha1."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		
		$query="select * "
		                ."    from evento_000001 "
		                ."  where Unidad='".$pac."' and fecha='".$fecha1."' and asistencia='on'";
		$err = mysql_query($query,$conex);
		$asis = mysql_num_rows($err);
		
		$porcen=($asis*100)/$num;
		

		
		echo "<table align=center border=1 width=725 >";
		echo "<tr><th align=center colspan=5  bgcolor='#cccccc' height='15'><font size='3'  face='arial'><b>INFORME</b></font></th></tr>";
	    echo "<th><font size=3  face='arial'><b>Fecha</b> </th>";
	    echo "<th><font size=3  face='arial'><b>Unidad</b></th>";
		echo "<th><font size=3  face='arial'><b>Numero de Inscritos</b></th>";
		echo "<th><font size=3  face='arial'><b>Numero de asistentes</b></th>";
		echo "<th><font size=3  face='arial'><b>Porcenteje de Asistencia</b></th>";
		echo "<tr><td align=center><font size=3  face='arial'> ".$fecha1."</td>";
		echo "<td align=center><font size=3  face='arial'> ".$pac."</td>";
		echo "<td align=center><font size=3  face='arial'> ".$num."</td>";
		echo "<td align=center><font size=3  face='arial'> ".$asis."</td>";
		echo "<td align=center ><font size=3  face='arial'> ".$porcen."%</td>";
}			
}?>