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
	if(!isset($pac1))
	{
		echo "<form action='000003_hd01.php' method=post>";
		echo "<center><table border=0 width=380>";
		echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='pac1'></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1>";
		
		echo "<tr><td align='center' bgcolor=#cccccc colspan=2><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";	
	}
	else
	{
		echo "<form action='000003_hd02.php' method=post>";
		echo "<center><table border=0 width=380>";
		echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";
		$query="select DISTINCT Paciente from hemo_000003 where  Paciente like '%".$pac1."%' order by Paciente";
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
}
?>
</body>
</html>