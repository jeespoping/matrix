 <title>REPORTE MEDICAMENTOS Y MATERIAL</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

  /***************************************************
	*	REPORTE DE medicamentos y material grabado por la pda *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($year1)  or !isset($year2) )
	{
		echo "<form action='000001_fp01.php' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=3><b>CL?NICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>MEDICAMENTOS Y MATERIAL</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CC: </font></td>";	
		echo "<td bgcolor=#cccccc><select name='cc'>";
		$query = "SELECT Ccocod FROM costosyp_000005 where Ccoclas='PR' order by Ccocod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				if (($row[0]) == $cc)
					echo "<option selected>".$row[0]."</option>";
				else
					echo "<option>".$row[0]."</option>";
			}
			echo "<option>Todos</option>";
		}	// fin del if $num>0
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";	
		echo "<td bgcolor=#cccccc  >";
		if(!isset($year1))
		{
			$year=date('Y');
			$month=date('m');
			$day=date('d');
		}
		echo " <select name='year1'>";
		for($f=2004;$f<2051;$f++)
		{
			if($f == $year1)
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		}
		echo "</select> <select name='month1'>";
		for($f=1;$f<13;$f++)
		{
			if($f == $month1)
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
		echo "</select> <select name='day1'>";
		for($f=1;$f<32;$f++)
		{
		if($f == $day1)
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

		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HASTA: </font></td>";	
		echo "<td bgcolor=#cccccc >";
		if(!isset($year2))
		{
			$year=date('Y');
			$month=date('m');
			$day=date('d');
		}
		echo "<select name='year2'>";
		for($f=2004;$f<2051;$f++)
		{
			if($f == $year2)
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		}
		echo "</select> <select name='month2'>";
		for($f=1;$f<13;$f++)
		{
			if($f == $month2)
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
		echo "</select> <select name='day2'>";
		for($f=1;$f<32;$f++)
		{
		if($f == $day2)
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
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		$fecha2=$year2."-".$month2."-".$day2;
		$fecha1=$year1."-".$month1."-".$day1;
		if($cc=="Todos")
		{
			$cc="";
			$span=3;
			$name="";
			//$w=750;
		}
		else
		{
			$name="</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>CENTRO DE COSTOS: ".$cc."</b></font>";
			$cc="Cc='".$cc."' and ";
			$span=4;
			//$w=700;
		}
		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table border=1 align='center' width=750><tr><td rowspan=4 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/root/americas10.jpg'  size=150 width=128></td>";
		echo "<td align=center colspan='4' ><font size=3 color='#000080' face='arial'><B>CL?NICA LAS AM?RICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>MATERIAL Y MEDICAMENTOS INGRESADOS</b></font>";
		echo $name."</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>DESDE ".$fecha1." HASTA ".$fecha2;//".substr($medico,$ini1+1,strlen($medico))."";
		echo "</tr></table>";

		if($cc != "")
		{
			echo "<table border=1 align='center' width=750>";
			echo "<TR><td width=75><font size=2 face='arial'><b>FECHA</td>";
			echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
			echo "<td ><font size=2 face='arial'><b>REG.</td>";
			echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
			echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
			echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
			echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
			echo "<td ><font size=2 face='arial'><b>CANT.</td></TR>";
			$query = "select fecha_data,hora_data,reg_num,historia,paciente,cod_articulo,descripcion_art,cantidad from farmpda_000001 where ".$cc." ((Fecha_data > '".$fecha1."' and Fecha_data < '".$fecha2."') or Fecha_data = '".$fecha2."' or Fecha_data = '".$fecha1."') order by Fecha_data,historia";
			//echo $query;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					echo "<TR><td><font size=2 face='arial'>".$row[0]."</td>";
					echo "<td ><font size=2 face='arial'>".substr($row[1],0,5)."</td>"; 
					echo "<td ><font size=2 face='arial'>".$row[2]."</td>";
					echo "<td ><font size=2 face='arial'>".$row[3]."</td>";
					echo "<td ><font size=1 face='arial'>".$row[4]."</td>";
					echo "<td ><font size=2 face='arial'>".$row[5]."</td>";
					echo "<td ><font size=1 face='arial'>".$row[6]."</td>";
					echo "<td ><font size=2 face='arial'>".$row[7]."</td></TR>";
				}
			}
		}
		else
		{
			echo "<table border=1 align='center' width=750>";
			echo "<TR><td ><font size=2 face='arial'><b>CC</td>";
			echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
			echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
			echo "<td ><font size=2 face='arial'><b>REG.</td>";
			echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
			echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
			echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
			echo "<td ><font size=2 face='arial'><b>CANT.</td></TR>";
			$query = "select Cc,fecha_data,hora_data,reg_num,historia,cod_articulo,descripcion_art,cantidad from farmpda_000001 where ".$cc." ((Fecha_data > '".$fecha1."' and Fecha_data < '".$fecha2."') or Fecha_data = '".$fecha2."' or Fecha_data = '".$fecha1."') order by Cc,Fecha_data,historia";
			//echo $query;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					echo "<TR><td width=75><font size=2 face='arial'><b>".$row[0]."</b></td>";
					echo "<td ><font size=2 face='arial'>".$row[1]."</td>"; 
					echo "<td ><font size=2 face='arial'>".substr($row[2],0,5)."</td>";
					echo "<td ><font size=2 face='arial'>".$row[3]."</td>";
					echo "<td ><font size=2 face='arial'>".$row[4]."</td>";
					echo "<td ><font size=2 face='arial'>".$row[5]."</td>";
					echo "<td ><font size=2 face='arial'>".$row[6]."</td>";
					echo "<td ><font size=2 face='arial'>".$row[7]."</td></TR>";
				}
			}
		}
	}
}
?>
</body>
</html>