<html>

<head>
  <title>CONSENTIMIENTO V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
   /**************************************************
	*			CONSENTIMIENTO INFORMADO 	 		 *
	*		  PARA MEDICOS CIRUJANOS	V.1.00		 *
	*				CONEX, FREE => OK                *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)

		echo "<form action='000001_cx05.php' method=post>";
		echo "<center><table border=0 width=380>";
		echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=2><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'cirugia' AND codigo = '002'  order by Descripcion ";
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
		echo "</select></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DOCUMENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='idmed'</td></tr>";		
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='paciente'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DOCUMENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='idpac'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>RESPONSABLE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='responsable'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DOCUMENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='idresp'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DIAGNOSTICO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='4' name='diagnostico' cols='30' ></textarea></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PROCEDIMIENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='4' name='procedimiento' cols='30' ></textarea></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ANESTESIA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='anestesia'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>EXAMENES PREQUIRURGICOS: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='examen'</td></tr>";
		echo "<tr><td align='center' bgcolor=#cccccc colspan=2><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";	
		echo "<tr></tr>";
	

include_once("free.php");
}
?>
</body>
</html>