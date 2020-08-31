<html>

<head>
  <title>HISTORIAL DE DIENTES</title>
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
	

	

	
	if(!isset($tipo)  or !isset($diente)  )
	{
		echo "<form action='' method='post'>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=2><b><font color=#000066 font size=3 face='arial' >PROMOTORA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=2><font color=#000066 font size=3 face='arial' >UNIDAD ODONTOLOGICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#99CCFF colspan=1><b><font color=#000066 font size=3 face='arial' >ODONTOLOGO: </font></td>";	
		echo "<td colspan= '1' bgcolor='#99CCFF'align=center><font size=3  face='arial'><select name='tipo'>";
		$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'soe1' AND codigo = '014' order by Subcodigo ";
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
echo "</select></td></tr>";
			
	echo "<tr><td bgcolor=#99CCFF colspan=1><b><font color=#000066 font size=3 face='arial' >DIENTE: </font></td>";	
	echo "<td colspan= '1' bgcolor='#99CCFF' align=center><font size=3  face='arial'><input type='text' name='diente' size=3></td></tr>";
		
	echo"<tr><td align=center bgcolor=#99CCFF colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";

	}// if de los parametros no estan set
	else
	/*****************************************
		  APARTIR DE AQUI EMPIEZA LA IMPRESION 		*/
		  
	{
		$exp=explode("-",$pac);
		//$exp[0]='71364443';
		//$medico='02-Ana Maria';
		$query="select Modificacion from soe1_000005 where Identificacion='".$exp[0]."' and Odontologo='".$medico."' and  Examen='".$tipo."' and Num_diente= '".$diente."'";		
		$err = mysql_query($query,$conex);
		$mod = mysql_fetch_array($err);
		//echo$mod[0];
		
		$exp1=explode("ANTERIOR MODIFICACION:",$mod[0]);
		
		$con=count($exp1);
		
		echo "<center><table border=0 width=900>";
		echo "<tr><td  align=center bgcolor=#99CCFF><b><font color=#000066 font size=3 face='arial' > ACTUALIZACION NUMERO</font></td>";
		echo "<td align=center bgcolor=#99CCFF><b><font color=#000066 font size=3 face='arial' >DATOS ACTUALIZADOS</font></td></tr>";
		
		for ($j=0;$j<$con;$j++)
		{
			$w=$con-($j+1);
			
			echo "<tr><td align=center bgcolor=#99FFFF><font color=#000066 font size=3 face='arial' >$w</font></td>";
			echo "<td align=left bgcolor=#99FFFF><font color=#000066 font size=3 face='arial' > $exp1[$j]</font></td></tr>";
			
			}
			
		echo "</table>"; 
		$hyper="<A HREF='/matrix/soe/procesos/regperiodontal_1.php?medico=".$medico."&amp;pac=".$pac."'>Volver a Registro Periodontal</a>";	
		echo "<tr><td  colspan= '4' align=center><font size=3 face='arial' >$hyper</td></tr>";
		}	

	}



	include_once("free.php");
?>