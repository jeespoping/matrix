<html>

<head>
  <title>ORDEN CX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000"><?php
include_once("conex.php");
   /*************************************************
	* 			IMPRESION DE FORMULAS PARA LENTES	*
	*       FORMULARIO MEDICAMENTOS OFTALMOLOGIA    *
	*					CONEX, FREE => OK			*
	*************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha) )
	{
		echo "<form action='000008_of01.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'oftalmo' AND codigo = '002'  order by Descripcion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if (($row[0]."-".$row[1])==$medico)
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
			$query="select Paciente from oftalmo_000008 where Oftalmologo='".$medico."' and Paciente like '%".$pac1."%' order by Paciente ";
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
			
		}	//fin isset pac1
		else
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
		echo "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </font></td>";	
		echo "</td><td bgcolor=#cccccc colspan=2><select name='fecha'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select Fecha_actual  from oftalmo_000008 where Paciente='".$pac."' and Oftalmologo='".$medico."' ";
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
		$pacn="<b>".substr($pac,$ini1+1,$ini2-$ini1-1)." ".substr($pac,$ini2+1,$ini3-$ini2-1)." ".substr($pac,$ini3+1,$ini4-$ini3-1)." ".substr($pac,$ini4+1,$ini5-$ini4-1)."</b>";
		$pacn=$pacn."<br><b>DOCUMENTO: </b>".substr($pac,$ini5+1);
		$query="select Diagnostico,tratamiento from oftalmo_000008 where Paciente='".$pac."' and Oftalmologo='".$medico."' and Fecha_actual='".$fecha."' ";
		$err = mysql_query($query,$conex);
		if($err)
			$row = mysql_fetch_array($err);
		else
			echo "NO EXISTE FORMULA PARA ESE PACIENTE EN ESA FECHA";
			
		echo "<table border=0 width=800><tr><td ><b>FECHA: </b>".$fecha."</td></tr><tr><td >".$pacn."</td></tr><tr><td><br><br></td></tr>";
		echo "</td><tr><td ><b>DIAGNOSTICO:</b></td></tr>";
		echo "<tr><td ><br>".$row[0]."</td></tr><tr></tr><td><br></td><tr><td ><b><br>TRATAMIENTO:</b></td></tr>";
		echo "<tr><td ><br>".$row[1]."</td></tr>";
		echo"</table>";
	}
	include_once("free.php");
}
?></html>