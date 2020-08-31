<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td rowspan=2 align=center><IMG SRC="/matrix/images/medical/laboratorio/labmed.gif"></td>
<td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Consulta de Informacion de Pacientes Hospitalizados (SERVINTE)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> inpac.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();

// Modificaciones
///////////////////////////
// Julio 30 de 2012 - Jonatan Lopez.
// Se agregan varios UNION a la consulta sql para que no genere errores ya que esta consulta extrae la informacion de UNIX y al generar nulos no responde el script.
///////////////////////////



if(!isset($_SESSION['user']))
	echo "error";
else
{
	include_once("root/comun.php");

	$key = substr($user,2,strlen($user));
	$conex = obtenerConexionBD("matrix");
	$basedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$conex = obtenerConexionBD("matrix");
	conexionOdbc($conex, $basedato, $conex_o, 'facturacion');
	echo "<form action='inpac.php' method=post>";

	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' name='basedato' value='".$basedato."'>";
	//$conex = mysql_pconnect('localhost','root','')
		//or die("No se ralizo Conexion");
	//

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
		//$conex_o = odbc_connect('facturacion','','');
		//$query = "select pachis,pacced,pacap1,pacap2,pacnom,paccer,pacres,pacsex,pacnac,pachab from inpac where ";

        $var=0;

		if (isset($q1))
		{
			$var++;
			$filtros = $filtros." pachis = '".$historia."' ";
		}
		if (isset($q2))
		{
			if($var > 0)
				$filtros=$filtros." and pacced = '".$cedula."' ";
			else
				$filtros=$filtros." pacced = '".$cedula."' ";
			$var++;
		}
		if (isset($q3))
		{
			$nombre=strtoupper($nombre);
			if($var > 0)
				$filtros=$filtros." and pacnom MATCHES '".$nombre."' ";
			else
				$filtros=$filtros." pacnom MATCHES '".$nombre."' ";
			$var++;
		}
		if (isset($q4))
		{
			$apellido1=strtoupper($apellido1);
			if($var > 0)
				$filtros=$filtros." and pacap1 MATCHES '".$apellido1."' ";
			else
				$filtros=$filtros." pacap1 MATCHES '".$apellido1."' ";
			$var++;
		}
		if (isset($q5))
		{
			$apellido2=strtoupper($apellido2);
			if($var > 0)
				$filtros=$filtros." and pacap2 MATCHES '".$apellido2."' ";
			else
				$filtros=$filtros." pacap2 MATCHES '".$apellido2."' ";
			$var++;
		}

        $query = " select pachis,pacced,pacap1,'' AS pacap2,pacnom,'' AS paccer,pacres,pacsex,pacnac,'' AS pachab from inpac where ".$filtros." and pacap2 is null and paccer is null and pachab is null" ;
        $query.= " UNION";
        $query.= " select pachis,pacced,pacap1,'' as pacap2,pacnom,'' AS paccer,pacres,pacsex,pacnac,pachab from inpac where ".$filtros." and pacap2 is null and paccer is null and pachab is not null" ;
        $query.= " UNION";
        $query.= " select pachis,pacced,pacap1,'' as pacap2,pacnom,paccer,pacres,pacsex,pacnac,'' AS pachab from inpac where ".$filtros." and pacap2 is null and paccer is not null and pachab is null" ;
        $query.= " UNION";
        $query.= " select pachis,pacced,pacap1,'' as pacap2,pacnom,paccer,pacres,pacsex,pacnac,pachab from inpac where ".$filtros." and pacap2 is null and paccer is not null and pachab is not null" ;
        $query.= " UNION";
        $query.= " select pachis,pacced,pacap1,pacap2,pacnom,'' AS paccer,pacres,pacsex,pacnac,'' AS pachab from inpac where ".$filtros." and pacap2 is not null and paccer is null and pachab is null" ;
        $query.= " UNION";
        $query.= " select pachis,pacced,pacap1,pacap2,pacnom,'' AS paccer,pacres,pacsex,pacnac,pachab from inpac where ".$filtros." and pacap2 is not null and paccer is null and pachab is not null" ;
        $query.= " UNION";
        $query.= " select pachis,pacced,pacap1,pacap2,pacnom,paccer,pacres,pacsex,pacnac,'' AS pachab from inpac where ".$filtros." and pacap2 is not null and paccer is not null and pachab is null" ;
        $query.= " UNION";
        $query.= " select pachis,pacced,pacap1,pacap2,pacnom,paccer,pacres,pacsex,pacnac,pachab from inpac where ".$filtros." and pacap2 is not null and paccer is not null and pachab is not null" ;


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
			echo "<td bgcolor=".$color."><b>Sexo</b></td>";
			echo "<td bgcolor=".$color."><b>Fecha Nacimiento</b></td>";
  			echo "<td bgcolor=".$color."><b>Habitacion</b></td></tr>";
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
				echo "<tr>";
				echo "<td bgcolor=".$color.">".$odbc[0]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[1]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[2]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[3]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[4]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[5]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[6]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[7]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[8]."</td>";
				echo "<td bgcolor=".$color.">".$odbc[9]."</td>";
				echo "</tr>";
				$i++;
			}
			echo "</tabla>";
			echo "<li><A HREF='SALIDA.php'>Salir del Programa</A>";
		}
	}
}
?>
</body>
</html>