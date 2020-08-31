<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td rowspan=2 align=center><IMG SRC="/matrix/images/medical/root/clinica.jpg"></td>
<td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Consulta de Informacion de Pacientes Hospitalizados (SERVINTE)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> llamadas.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='llamadas.php' method=post>";
	//$conex = mysql_pconnect('localhost','root','')
		//or die("No se ralizo Conexion");
	//

	if(isset($radio))
	{
		$total=($duracion[$radio] * $tarifa[$radio]) * 1.16;
		echo "SE FACTURO EL PACIENTE:<b> ".$historias[$radio]."</b> DURACION DE LA LLAMADA :<b> ".$duracion[$radio]." MINUTOS</b> POR UN VALOR DE : <B>$".number_format((double)$total,2,'.',',')." </B>INCLUIDO EL IVA<BR><BR>";
	}
	if(!isset($historia) or !isset($cedula) or !isset($nombre) or !isset($apellido1) or !isset($apellido2))
	{
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q1></td><td bgcolor=#cccccc>HISTORIA</td><td bgcolor=#cccccc><input type='TEXT' name='historia' size=12 maxlength=12></td></tr> "; 
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q2></td><td bgcolor=#cccccc>CEDULA</td><td bgcolor=#cccccc><input type='TEXT' name='cedula' size=12 maxlength=12></td></tr> "; 
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q3></td><td bgcolor=#cccccc>NOMBRE</td><td bgcolor=#cccccc><input type='TEXT' name='nombre' size=20 maxlength=20></td></tr> "; 
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q4></td><td bgcolor=#cccccc>PRIMER APELLIDO</td><td bgcolor=#cccccc><input type='TEXT' name='apellido1' size=15 maxlength=15></td></tr> "; 
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q5></td><td bgcolor=#cccccc>SEGUDO APELLIDO</td><td bgcolor=#cccccc><input type='TEXT' name='apellido2' size=15 maxlength=15></td></tr>"; 
		echo "<tr><td bgcolor='#cccccc' align=center colspan=3><input type='submit' value='IR'></td></tr></table> ";
	}
	else
	{
		$conex_o = odbc_connect('facturacion','','');
		$query = "select pachis,pacced,pacap1,pacap2,pacnom,paccer,pacres,pacsex,pacnac,pachab from inpac where ";
		$var=0;
		if (isset($q1))
		{
			$var++;
			$query=$query." pachis = '".$historia."' ";
		}
		if (isset($q2))
		{
			if($var > 0)
				$query=$query." and pacced = '".$cedula."' ";
			else
				$query=$query." pacced = '".$cedula."' ";
			$var++;
		}
		if (isset($q3))
		{
			$nombre=strtoupper($nombre);
			if($var > 0)
				$query=$query." and pacnom MATCHES '".$nombre."' ";
			else
				$query=$query." pacnom MATCHES '".$nombre."' ";
			$var++;
		}
		if (isset($q4))
		{
			$apellido1=strtoupper($apellido1);
			if($var > 0)
				$query=$query." and pacap1 MATCHES '".$apellido1."' ";
			else
				$query=$query." pacap1 MATCHES '".$apellido1."' ";
			$var++;
		}
		if (isset($q5))
		{
			$apellido2=strtoupper($apellido2);
			if($var > 0)
				$query=$query." and pacap2 MATCHES '".$apellido2."' ";
			else
				$query=$query." pacap2 MATCHES '".$apellido2."' ";
			$var++;
		}
		if($var > 0)
		{
			$err_o = odbc_do($conex_o,$query);
			$campos= odbc_num_fields($err_o);
			$i=0;
			$color="#999999";
			echo "<table border=0 align=center>";
  			echo "<tr><td bgcolor=".$color."><b>Historia</b></td>";
  			echo "<td bgcolor=".$color."><b>Cedula</b></td>";
  			echo "<td bgcolor=".$color."><b>1er Apellido</b></td>";
  			echo "<td bgcolor=".$color."><b>2do Apellido</b></td>";
  			echo "<td bgcolor=".$color."><b>Nombres</b></td>";
			echo "<td bgcolor=".$color."><b>Nit-Responsable</b></td>";
			echo "<td bgcolor=".$color."><b>Responsable</b></td>";
			echo "<td bgcolor=".$color."><b>Duracion<br>LLamada</b></td>";
			echo "<td bgcolor=".$color."><b>Valor <br>Minuto</b></td>";
  			echo "<td bgcolor=".$color."><b>Ciudad</b></td>";
  			echo "<td bgcolor=".$color."><b>Facturar</b></td></tr>";
  			$historias=array();
  			$k=0;
			while (odbc_fetch_row($err_o))
			{
				$odbc=array();
				for($m=1;$m<=$campos;$m++)
				{
					$odbc[$m-1]=odbc_result($err_o,$m);
				}
				$r = $i/2;
				if ($r*2 === $i)
					$color="#CCCCCC";
				else
					$color="#999999";
				$k++;
				$historias[$k]=$odbc[0]."-".$odbc[4]." ".$odbc[2]." ".$odbc[3];
				echo "<tr>";
				echo "<td bgcolor=".$color.">".$odbc[0]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[1]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[2]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[3]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[4]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[5]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[6]."</td>";
				echo "<td bgcolor=".$color." align=center><input type='TEXT' name='duracion[".$k."]' size=5 maxlength=5></td>";
				echo "<td bgcolor=".$color." align=center><input type='TEXT' name='tarifa[".$k."]' size=6 maxlength=6></td>";
				echo "<td bgcolor=".$color." align=center><input type='TEXT' name='ciudad[".$k."]' size=20 maxlength=20></td>";
				echo "<td bgcolor=".$color." align=center><input type='RADIO' name=radio value=".$k."></td>";
				echo "</tr>";
				$i++;
			}
			for ($w=1;$w<=$k;$w++)
				echo "<input type='HIDDEN' name= 'historias[".$w."]' value='".$historias[$w]."'>";
			echo  "<tr><td bgcolor=#cccccc  colspan=12 align=center><input type='submit' value='ENTER'></td></tr></tabla>";
			echo "<li><A HREF='/matrix/SALIDA.php'>Salir del Programa</A>";
		}
	}
}
?>
</body>
</html>