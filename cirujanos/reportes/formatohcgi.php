<html>

<head>
  <title>IMPRESION</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
  /**************************************************
   * 			IMPRESION DE						*
   *       FORMULARIO MEDICAMENTOS HC		        *
   *				CONEX, FREE => OK               *
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
		echo "<form action='formatohcgi.php?empresa=$empresa&tipo=$tipo' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </td>";	
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
					if (($row[0]."-".$row[1])==$medico)
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}	// fin del if $num>0
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </td>";	

		/* Si el paciente no esta set construir el drop down */
		
		
		if(isset($pac1))
		{
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			if ($tipo=="F")
			{
				$query="select Paciente from ".$empresa."_000007 where doctor='".$medico."' and Paciente like '%".$pac1."%' order by Paciente";
			}
			else if ($tipo=="P")
			{
				$query="select Paciente from ".$empresa."_000005 where doctor='".$medico."' and Paciente like '%".$pac1."%' order by Paciente";
			}
			else if ($tipo=="E")
			{
				$query="select Paciente from ".$empresa."_000004 where doctor='".$medico."' and Paciente like '%".$pac1."%' order by Paciente";
			}
			else if ($tipo=="I")
			{
				$query="select Paciente from ".$empresa."_000006 where doctor='".$medico."' and Paciente like '%".$pac1."%' order by Paciente";
			}
			
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
			echo "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </td>";	
		    echo "</td><td bgcolor=#cccccc colspan=2><select name='fecha'>";
		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */
			
			if ($tipo=="F")
			{
				$query = "select Fecha  from ".$empresa."_000007 where Paciente='".$pac."' and doctor='".$medico."' ";
			}
			else if ($tipo=="P")
			{
				$query = "select Fecha  from ".$empresa."_000005 where Paciente='".$pac."' and doctor='".$medico."' ";
			}
			else if ($tipo=="E")
			{
				$query = "select Fecha  from ".$empresa."_000004 where Paciente='".$pac."' and doctor='".$medico."' ";
			}
			else if ($tipo=="I")
			{
	//			$query = "select Fecha  from ".$empresa."_000006 where Paciente='".$pac."' and doctor='".$medico."' ";
				$query = "select Fecha,Id from ".$empresa."_000006 where Paciente='".$pac."' and doctor='".$medico."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<=$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					//	echo "<option>".$row[0]."</option>";
					}
				}
			}
			if ($tipo!="I")
				{
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
		}
		echo"</select></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	{
		if ($tipo=="F")
		{
			$query="select * from ".$empresa."_000007 where Paciente='".$pac."' and doctor='".$medico."' and Fecha='".$fecha."' ";
		}
		else if ($tipo=="P")
		{
			$query="select * from ".$empresa."_000005 where Paciente='".$pac."' and doctor='".$medico."' and Fecha='".$fecha."' ";
		}
		else if ($tipo=="E")
		{
			$query="select * from ".$empresa."_000004 where Paciente='".$pac."' and doctor='".$medico."' and Fecha='".$fecha."' ";
		}
		else if ($tipo=="I")
		{
			/*	$query="select * from ".$empresa."_000006 where Paciente='".$pac."' and doctor='".$medico."' and Fecha='".$fecha."' ";
			 Se cambia la Fecha por Id, ya que cuando tienen varias interconsultas de la misma fecha siempre trae el primer registro, se deja la misma variable($fecha)
			 ya que este programa sirve para sacar varios reportes dependiendo del tipo.*/
			$query="select * from ".$empresa."_000006 where Paciente='".$pac."' and doctor='".$medico."' and Id=substr('".$fecha."',12,13) ";
		}
		
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
			
		
		echo "<table border=0 width=400><br><br><br><br><br><br><br><br><tr><td >&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$row['Paciente']."</td></tr><tr><td >&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$row['Fecha']."</td></tr><tr><td><br><br></td></tr>";
		if ($tipo=="F")
		{
			echo "</td><tr><td ><b>FORMULA MEDICA:</b></td></tr>";
			echo "<tr><td ><br>".$row['Formula']."</td></tr>";
		}
		else if ($tipo=="P")
		{
			echo "</td><tr><td ><b>PROCEDIMIENTOS:</b></td></tr>";
			echo "<tr><td ><br>".$row['Proced']."</td></tr>";
		}
		else if ($tipo=="E")
		{
			echo "</td><tr><td ><b>ESTUDIOS:</b></td></tr>";
			echo "<tr><td ><br>".$row['Estudios']."</td></tr>";
		}
		else if ($tipo=="I")
		{
			echo "</td><tr><td ><b>INTERCONSULTAS:</b></td></tr>";
			echo "<tr><td ><br>".$row['Intercon']."</td></tr>";
		}
		echo"</table>";
	}
	include_once("free.php");
}
?></HTML>