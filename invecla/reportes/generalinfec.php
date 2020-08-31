

  <title>INDICADORES DE INFECCION INTRAHOSPITALARIA</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*			PARA MEDICOS PEDIATRAS	V.1.00		 *
	*					CONEX, FREE =>	OK			 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($pachis)  or !isset($pacingr) or !isset($fecha))
	{
		echo "<form action='000006_ci01.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>APACHE II</td></tr>";
		echo "<tr></tr>";
		if(!isset($pachis))
			$pachis="";
		echo "<tr><td bgcolor=#cccccc colspan=1>HISTORIA: </td>";	
		/* Si el medico no ha sido escogido Buscar a los pediatras registrados para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><input type='text' name='pachis' value='".$pachis."'>";
		/*echo "<select name='pachis'>";
		$query = "select Nro_historia from 'cominf_000006' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				if($pachis==$row[0])
					echo "<option selected>".$row[0]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";				}
		}	// fin del if $num>0
		
		echo "</select>";*/
		echo "</tr><tr><td bgcolor=#cccccc colspan=1>INGRESO: </td>";	
		
			/* Si el paciente no esta set construir el drop down */
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pacingr'>";
			if(isset($pachis))
			{
				/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
				$query = "select Nro_ingreso from cominf_000006 where Nro_historia='".$pachis."' order by Nro_ingreso ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						if($pac==$row[0])
							echo "<option selected>".$row[0]."</option>";
						else
							echo "<option>".$row[0]."</option>";
					}
				}	// fin $num>0
			}	//fin isset medico
		echo"</select></td></tr><tr><td align=center bgcolor=#cccccc></td>";
		echo "</td><td bgcolor=#cccccc colspan=2><select name='fecha'>";

		if(isset($pachis) and isset($pacingr))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */
			$query = "select Fecha from cominf_000006 where Nro_historia='".$pachis."' and Nro_ingreso='".$pacingr."' ";
			echo $query;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<=$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
			}
		}
		//echo"<td align=center bgcolor=#cccccc>Todo <input type='checkbox' name='cert'></td>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		if(!isset($glas)  or !isset($pacnom))
		{
			$query = "select Datos_pac,Glasgow,Apache,Rata_muerte,Rata_muerte_ajustada from cominf_000006 where Nro_historia='".$pachis."' and Nro_ingreso='".$pacingr."' and Fecha='".$fecha."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				$dat= mysql_fetch_array($err);	
				$uno=strpos($dat[0],"Nombre:");
				$fin=strpos($dat[0],"<",$uno);
				$pacnom=substr($dat[0],$uno+7,$fin-$uno-7);
				$uno=strpos($dat[0],"Documento:");
				$fin=strpos($dat[0],"<",$uno);
				$pacdoc=substr($dat[0],$uno+10,$fin-$uno-10);
				$glas=$dat[1];
				$apache=$dat[2];
				$rata=$dat[3];
				$ratajus=$dat[4];
			}
		}
		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table border=1 width=500><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/root/americas10.jpg'  size=150 width=128></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>MEDICIÓN DEL APACHE II</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$fecha;//".substr($medico,$ini1+1,strlen($medico))."";
		echo "</tr></table>";	
		echo "<table border=1 width=500><td colspan=2><font size=3 face='arial'><b>PACIENTE: </b>".$pacnom." </td>"; 
		echo "<td><font size=3  face='arial'><b>DOCUMENTO:</b> ".$pacdoc."</td></tr>";
		echo "<tr><td><font size=3 face='arial'><b>HISTORIA: </b>".$pachis."-".$pacingr." </td>"; 
		echo "<td><font size=3  face='arial'><b>GLASGOW: </b>".$glas."</td>";
		echo "<td><font size=3  face='arial'><B>APACHE II: </b>".$apache."</td>";
		echo "<tr><td><font size=3  face='arial'><B>RATA DE MUERTE:</b> ".$rata."</td>";
		echo "<td colspan=3><font size=3  face='arial'><B>RATA DE MUERTE AJUSTADA: </b>".$ratajus."</td></tr>";
	}
	include_once("free.php");
}
?>