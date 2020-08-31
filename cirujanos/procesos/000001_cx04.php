<BODY TEXT="#000066">
<?php
include_once("conex.php");
   /**************************************************
	*		INFORME ENDOSCOPIA DIGESTIVA SUPERIOR	 *
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
	echo "<form action='000001_cx06.php' method=post>";
	echo "<center><table border=0 width=600>";
	echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
	echo "<tr><td align=center colspan=2><font color=#000066>TORRE MÉDICA</font></td></tr>";
	echo "<tr></tr>";
	echo "<tr><td bgcolor=#cccccc><font color=#000066>MEDICO: </font></td>";	
	/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
		construir el drop down*/
	echo "<td bgcolor=#cccccc colspan=1><select name='medico'>";
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
	echo "</select></td></tr>";
	/*echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ENTIDAD: </font></td>";	
	/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
			construir el drop down*
	echo "<td bgcolor=#cccccc colspan=1><select name='entidad'>";
	$query = "SELECT Descripcion FROM `det_selecciones` WHERE medico = 'cirugia' AND codigo = '004'  order by Descripcion ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		for ($j=0;$j<$num;$j++)
		{	
			$row = mysql_fetch_array($err);
			if ($row[0] == $entidad)
				echo "<option selected>".$row[0]."</option>";
			else
				echo "<option>".$row[0]."</option>";
		}
	}	// fin del if $num>0	*/
	echo "<tr><td bgcolor=#cccccc  colspan=1><font color=#000066>PACIENTE: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><input type='text' name='pac'</td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DOCUMENTO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><input type='text' name='idpac'</td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO REMITENTE: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><input type='text' name='remite' ></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ESOFAGO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='5' name='esofago' cols='50' >Mucosa, calibre y peristaltismo";
	echo " normales. Cardias a los 37 cm, cierra bien. Linea Z nítida, a la altura de la impronta diafragmática. No hay esofagitis, úlcera ni estenosis.";
	echo "No hay varices, divertículos ni tumor. </textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ESTOMAGO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER' cols='50' ROWS='6' name='estomago' >Insufla fácil. ";
	echo "Pliegues y peristaltismo de caracteristicas normales. Lago gástrico de aspecto y volumen normales. Retrovisión no muestra hernia hiatal. ";
	echo "Mucosa de fundus y cuerpo sin inflamación, úlcera ni tumor. Mucosa antral normal. Piloro regular, activo.</textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DUODENO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='3' name='duodeno' cols='50' >Normal en sus tres porciones vistas. ";
	echo "No hay inflamación, úlcera ni estenosis. Baño biliar normal. </textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DIAGNOSTICO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='1' name='diagnostico' cols='50' ></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ESOFAGO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='1' name='esofago1' cols='50' ></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ESTOMAGO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='1' name='estomago1' cols='50' ></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DUODENO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='1' name='duodeno1' cols='50' ></textarea></td></tr>";
	echo "<tr><td align='center' bgcolor=#cccccc colspan=2><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";	
	echo "<tr></tr>";
	
	include_once("free.php");
}
?>
</body>
</html>