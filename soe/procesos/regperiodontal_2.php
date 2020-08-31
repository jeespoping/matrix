<html>

<head>
  <title>REGISTRO PERIODONTAL</title>
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
	

	

	
	
	function grabar ($arriba,$tipo,$diente,$conex,$key,$exp,$medico)
	{
		
		$query = "SELECT * FROM soe1_000005 WHERE Identificacion ='".$exp[0]."' and Examen='".$tipo."' and Num_diente='".$diente."'";
		$err = mysql_query($query,$conex);
		//echo $query."<br>".mysql_errno().'='.mysql_error();
		$num = mysql_num_rows($err);
		if ($num>0)
		{
		$row = mysql_fetch_array($err);	
		$fecha=date("Y-m-d");
		$hora=date("H:i:s");	
		$query= "UPDATE soe1_000005 SET Medico='soe1', Fecha_data='".$fecha."', Hora_data='".$hora."',  Fecha='".$fecha."',  Identificacion='".$exp[0]."', Odontologo='".$medico."',  Examen='".$tipo."', Num_diente='".$diente."', Arriba='".$arriba."', Abajo='.', Izquierda='.', Derecha='.', Modificacion='MODIFICADO (DATOS ANTERIORES)  Odontologo: ".$row["Odontologo"].", Fecha: ".$row["Fecha_data"].",  Hora: ".$row["Hora_data"].", Arriba: ".$row["Arriba"].", ANTERIOR MODIFICACION: ".$row["Modificacion"]."', Seguridad='A-".$key."'  WHERE id = '".$row["id"]."'";
		$err = mysql_query($query,$conex);
		//echo $query."<br>".mysql_errno().'='.mysql_error();
		echo "<font color='#FF0000'font size=3  face='arial'><b>HAN SIDO ACTUALIZADOS LOS DATOS DEL DIENTE: $diente</b>";
		}
		
		else{
		$fecha=date("Y-m-d");
		$hora=date("H:i:s");
		$query="INSERT INTO soe1_000005 (Medico, Fecha_data, Hora_data,  Fecha,  Identificacion, Odontologo,  Examen, Num_diente, Arriba, Abajo, Izquierda, Derecha, Modificacion, Seguridad) VALUES ('soe1','".$fecha."','".$hora."','".$fecha."' , '".$exp[0]."','".$medico."','".$tipo."','".$diente."','".$arriba."','.' ,'.','.','Grabado por Odontologo: ".$medico."','A-".$key."')";
		$err = mysql_query($query,$conex);
		//echo $query."<br>".mysql_errno().'='.mysql_error();
		echo "<font color='#009900'font size=3  face='arial'><b>HAN SIDO RECIBIDOS LOS DATOS DEL DIENTE: $diente</b>";
		}
		
		
	}
	
	if(!isset($medico)  or !isset($pac)  )
	{
		echo "<form action='' method='post'>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>PROMOTORA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>UNIDAD ODONTOLOGICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ODONTOLOGO: </font></td>";	
			/* Si el ODONTOLOGO no ha sido escogido Buscar a los odontologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'soe1' AND codigo = '008'  order by Subcodigo ";
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
	
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
	
		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			//$query="select DISTINCT Identificacion from soe1_000004 where  Identificacion like '%".$pac1."%' order by Identificacion";V1.03
			$query="select DISTINCT Identificacion from soe1_000008 where Identificacion like '%".$pac1."%' and Odontologo='$medico' order by Identificacion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
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
//		echo "<input type='hidden' name='medico' value='".$medico."'>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/*****************************************
		  APARTIR DE AQUI EMPIEZA LA IMPRESION 		*/
		  
	{	$ini1=strpos($medico,"-");
		$nomed=substr($medico,$ini1+1);
		
		$exp=explode("-",$pac);
		
		$query="select Fecha from soe1_000008 where Identificacion='".$pac."' and Odontologo='$medico'";
			$err = mysql_query($query,$conex);
			$fec = mysql_fetch_row($err);
			
		echo "<form action='' method='post'>";	
		echo "<table align=center width=680 >";
		echo "<tr><td  bgcolor='#99FFFF' colspan= '2'><font size=3 face='arial' ><B>PACIENTE: </b>".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."</td>"; 
		echo "<td bgcolor='#99FFFF' colspan= '2'><font size=3  face='arial'><b>ODONTOLOGO:</b> ".$nomed."</td></tr>";
		echo "<tr><td bgcolor='#99FFFF' colspan= '2' ><font size=3  face='arial'><b>FECHA:</b> ".$fec[0]." </td>";
		echo "<td bgcolor='#99FFFF' colspan= '2' ><font size=3  face='arial'><b>ID:</b> ".$exp[0]." </td></tr>";
		echo "<tr><td colspan= '1' bgcolor='#99CCFF'align=center><font size=3  face='arial'><b>Tipo</b></td>";
		echo "<td colspan= '1' bgcolor='#99CCFF' align=center><font size=3  face='arial'><b>Diente</b></td>";
		echo "<td colspan= '2' bgcolor='#99CCFF' align=center><font size=3  face='arial'><b>Esquema</b></td></tr>";
		echo "<tr><td colspan= '1' bgcolor='#99CCFF'align=center><font size=3  face='arial'><select name='tipo'>";
		$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'soe1' AND codigo = '014'  and subcodigo>'04' order by Subcodigo ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
		$hyper1="<A HREF='/matrix/soe/procesos/histodiente.php?medico=".$medico."&amp;pac=".$pac."'>Historial de dientes</a>";
		echo "</select><br>$hyper1</td>";
		echo "<td colspan= '1' bgcolor='#99CCFF' align=center><font size=3  face='arial'><input type='text' name='diente' size=3></td>";
		echo "<td colspan= '2' bgcolor='#99CCFF' align=center><font size=3  face='arial'><b><input type='text' name='arriba' size=4></b></td>";
		echo "<tr><td  colspan= '2' align=center><font size=3 face='arial' ><input type=radio name='opcion' value='grabar' checked>GRABAR</td>"; 
		echo "<td  colspan= '4' align=center><font size=3 face='arial' ><input type=submit  value='ACEPTAR'></td></tr>";
		echo "<tr><td  ><BR></td>";	
		if (isset($opcion) and $opcion=="grabar")
		{
			grabar ($arriba,$tipo,$diente,$conex,$key,$exp,$medico);
		}else{
		}

		echo "<input type='hidden' name='medico' value='".$medico."'>";
		echo "<input type='hidden' name='pac' value='".$pac."'>";
		$hyper="<A HREF='/matrix/soe/procesos/regperiodontal_1.php?medico=".$medico."&amp;pac=".$pac."'>Volver</a>";
		echo "<tr><td  colspan= '4' align=center><font size=3 face='arial' >$hyper</td></tr>";
		echo "</table>";
		echo "</form>";
	
	}

	}

	include_once("free.php");
?>