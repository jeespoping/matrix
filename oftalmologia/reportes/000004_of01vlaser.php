<html>

<head>
  <title>LENTES 1.01</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000"><?php
include_once("conex.php");
  /**************************************************
	* 			IMPRESION DE FORMULAS PARA LENTES						  *
	*               FORMULARIO LENTES OFTALMOLOGIA                           *
				CONEX, FREE => OK
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha) )
	{
		echo "<form action='000004_of01vlaser.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>VISUAL LASER S.A.S.</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'vlaser' AND codigo = '002'  order by Descripcion ";
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
		
		
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			$query="select Paciente from vlaser_000004 where Oftalmologo='".$medico."' and Paciente like '%".$pac1."%' order by Paciente";
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
		echo "</select><input type='hidden' name='pac1' value='".$pac1."'>";
		
		}	//fin isset medico
		else
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
		
		ECHO "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='fecha'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select Fecha  from vlaser_000004 where Paciente='".$pac."' and Oftalmologo='".$medico."' ";
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
		echo"</select></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	{
		$ini1=strpos($pac,"-");
		$ini2=strpos($pac,"-",$ini1+1);
		$ini3=strpos($pac,"-",$ini2+1);
		$ini4=strpos($pac,"-",$ini3+1);
		$ini5=strpos($pac,"-",$ini4+1);
		$ini6=strpos($pac,"-",$ini5+1);
		$pacn="<b>".substr($pac,$ini1+1,$ini2-$ini1-1)." ".substr($pac,$ini2+1,$ini3-$ini2-1)." ".substr($pac,$ini3+1,$ini4-$ini3-1)." ".substr($pac,$ini4+1,$ini5-$ini4-1)."</b>";
		$pacn=$pacn."<br><b>DOCUMENTO:</b>".substr($pac,$ini5+1);//,$ini6-$ini5-1);
		$query="select * from vlaser_000004 where Paciente='".$pac."' and Oftalmologo='".$medico."' and Fecha='".$fecha."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		$row = mysql_fetch_array($err);
		for ($j=1;$j<=13;$j++)
		{	
			if ($row[$j] == "NO APLICA")
				$row[$j]= "&nbsp";
		}
		echo "<table border=0 width=800><tr><td ><b>FECHA: </b>".$row[1]."</td></tr><tr><td >".$pacn."</td></tr><tr><td><br><br></td></tr>";
		echo "<tr><td><table  border=1 width=400><td width=80>.</td><td width=80 align=center><b>ESFERA</b></td>";
		echo "<td align=center width=80><b>CILINDRO</b></td><td align=center width=80><b>EJE</b></td><td  width=80 align=center><b>Add</b></td></tr>";
		echo "<tr><td align=center><b>OD</b></td><td align=center>".$row[6]."</td><td align=center>".$row[8]."</td><td align=center>".$row[10]."</td><td align=center>".$row[12]."</td></tr>";
		echo "<tr><td align=center><b>OS</b></td><td align=center>".$row[7]."</td><td align=center>".$row[9]."</td><td align=center>".$row[11]."</td><td align=center>".$row[13]."</td></tr>";
		echo "</table></tr><tr><td><br><br></td></tr></td><tr><td ><b>ESPECIFICACIONES</b></td></tr>";
		echo "<tr><td >".$row[14]."</td></tr>";
		echo"</table>";
	}
}
?>
</BODY>
</HTML>