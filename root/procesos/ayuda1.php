<html>
<head>
<title>MATRIX Ayuda en Linea</title>
</head>
<?php
include_once("conex.php");
	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");
	

	mysql_select_db("MATRIX");
	$key = substr($user,2,strlen($user));
	$query = "select codigo,prioridad,grupo from usuarios where codigo='".$key."' and activo = 'A'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	$prioridad=$row[1];
	$codigo=$row[0];
	$grupo=$row[2];
	$query = "insert root_000004 (medico,fecha_data,hora_data,usuario,seguridad) values ('root','".$fecha."','".$hora."','".$codigo."','C-root')";
	$err1 = mysql_query($query,$conex);
	mysql_close($conex);
	echo "<BODY TEXT='#000066'>";
	echo "<font size=9>";
	echo "<center><table border=0>";
	echo "<tr><td align=center><h1>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
	echo "<tr><td align=center><h2>SISTEMA DE AYUDA EN LINEA PARA FACTURACION</td></tr>";
	echo "<tr><td align=center><h2>DIRECCION DE MERCADEO</td></tr>";
	echo "<tr><td align=center><h2>ADVERTENCIA</td></tr>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/prohibido.gif'></td></tr>";
	echo "<tr><td>TODA  ACTIVIDAD QUE &nbsp&nbspUSTED REALICE &nbsp&nbspEN ESTA  PAGINA  SERA  REGISTRADA EN EL &nbsp&nbspSISTEMA</td></tr>";
	echo "<tr><td>QUEDA TOTALMENTE PROHIBIDO LA  IMPRESION,  COPIA O  REPRODUCCION PARCIAL O TOTAL</td></tr>";
	echo "<tr><td>DE LA INFORMACION  QUE  AQUI SE  DESPLIEGA SIN LA AUTORIZACION  DE LA GERENCIA DE LA</td></tr>";
	echo "<tr><td>CLINICA. </td></tr>";
	echo "<tr><td>CUALQUIER TRANSGRECION DE ESTA NORMA SERA SANCIONADA &nbsp&nbspSEGUN LO CONTEMPLADO </td></tr>";
	echo "<tr><td>EN EL REGLAMENTO INTERNO DE TRABAJO DE LA INSTITUCION.</td></tr>";
	echo "<tr><td align=center><A HREF='/ayuda/index.htm' target='_blank'><b>HAGA CLICK PARA COMENZAR AYUDA EN LINEA</b></A></td></tr></table>";
	?>	
</html>