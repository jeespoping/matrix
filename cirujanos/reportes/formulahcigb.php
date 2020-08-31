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
			
	if(!isset($pac) or !isset($fecha) )
	{
		echo "<form action='formulahcigb.php?empresa=$empresa' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>DR. IGNACIO GONZALEZ BORRERO</b></td></tr>";
		echo "<tr></tr>";
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </td>";	

		/* Si el paciente no esta set construir el drop down */
				
		if(isset($pac1))
		{
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			$query="select Paciente from ".$empresa."_000004 where Paciente like '%".$pac1."%' order by Paciente";
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
			$query = "select Fecha,Hora_data  from ".$empresa."_000004 where Paciente='".$pac."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<=$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."/".$row[1]."</option>";
					
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
		$fec = explode("/", $fecha);
		$query=" select * from ".$empresa."_000004 where Paciente = '".$pac."' and Fecha='".$fec[0]."' and Hora_data = '".$fec[1]."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$pacien = explode("-", $pac);
		//nuevo cambio 2014-09-18
		$query1 = " SELECT Fecha_nacimiento,Documento,Direccion,Entidad ".
			     " FROM hcigb_000001 ".
			     " WHERE Nro_historia = '".$pacien[5]."'";
		$err1 = mysql_query($query1,$conex);
		$row1 = mysql_fetch_array($err1);
		
		$dias = explode("-",$row1[0]);
		$dia = date(j);
		$mes = date(n);
		$ano = date(Y);
		
		//Si el mes es el mismo pero el dia inferior aun no ha cumplido anos, le quitaremos un ano al actual
		
		if (($dias[1] == $mes) and ($dias[2] > $dia)){
			$dias[0] = ($dias[0] + 1);
			}
		
		//Si el mes es superior al actual tampoco habra cumplido anos, por eso le quitamos un ano al actual
		
		If ($dias[1] > $mes ){
			$dias[0] = ($dias[0] + 1);
			}
		
		// Ya no habria mas condiciones, ahora restamos loa anos y muestro el resultado
		
		$edad1 = ($ano - $dias[0]); //hasta aca cambio 2014-09-18
		echo "<table align='left' border=1 width=725 ><tr><td rowspan=3 align='left' colspan='0'>";
		echo "<img SRC='/matrix/images/medical/pediatra/logotorre.JPG' width='180' height='117'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>Dr. Ignacio Gonzalez Borrero";
		echo "<br>Especialista Neurocirugia<BR>Medico Neurocirujano<BR>Reg.:</b> 17352-88</b></font>";
		echo "</tr></table><br>";	
		
			
			echo "<table align=left border=1 width=725 >";
			echo "<tr><td ><B>Nro HISTORIA:</B>".$pacien[5]."</td>";
			echo "<td ><B>DOCUMENTO:</B>".$pacien[0]."</td></tr>";
			echo "<tr><td colspan='2'><B>PACIENTE:</B>".$pacien[1]."&nbsp".$pacien[2]."&nbsp".$pacien[3]."&nbsp".$pacien[4]."</td></tr>";
			echo "<tr><td ><B>EDAD:</B>".$edad1."</td>";
			echo "<td ><B>DIRECCION:</B>".$row1['Direccion']."</td></tr>";
			echo "<tr><td ><B>ENTIDAD:</B>".$row1['Entidad']."</td>";
			echo "<td ><B>FECHA:</B>".$row['Fecha']."</td></tr><tr><td></td></tr>";
			echo "<br><tr><td colspan='2'><font size=3  face='arial'> ".str_replace( "\n", "<br>", htmlentities( trim($row['Formula']) ) )."</td></tr><table>";
			echo "<table align=left border=1 width=725>";
			echo "<tr><td colspan='2'><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:<b>DR. IGNACIO GONZALEZ BORRRERO &nbsp&nbsp&nbsp&nbsp REGISTRO:17352-88</B></td></tr>";
		    //echo "<tr><td colspan='2'><img SRC='/matrix/images/medical/hce/Firmas/".$key.".png ' width='140' height='90'></td></tr>";
			echo "<tr><td colspan='2'><img SRC='/matrix/images/medical/hce/Firmas/0800147.png ' width='140' height='90'></td></tr>";
			echo "</table>";
					
	}
	include_once("free.php");
}
?></HTML>