<html> 
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Cirugias Programadas Canceladas Entre Fechas</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Repcircan.php Ver. 2009-07-10</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Repcircan.php' method=post>";
	if(!isset($v0) or !isset($v1))
	{
	echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>CIRUGIAS PROGRAMADAS CANCELADAS ENTRE FECHAS</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Incial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query  = " select Mcatur,Mcaqui,Mcahin,Mcahfi,Mcafec,Mcadoc,Mcanom,Mcaeps,Mcacir,Mcamed,Mcaequ,Mcausg,Mcacom from tcx_000007 ";
		$query .= " where Mcafec between '".$v0."' and '".$v1."' ";
		$query .= "   order by 4,1 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=1>";
		echo "<tr><td colspan=14 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>CIRUGIAS PROGRAMADAS CANCELADAS ENTRE FECHAS</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>X FECHAS - ENTRE : ".$v0." y ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>Codigo<BR>Turno</b></td>";
		echo "<td bgcolor=#cccccc><b>Quirofano</b></td>";
		echo "<td bgcolor=#cccccc><b>Hora<BR>Inicio</b></td>";
		echo "<td bgcolor=#cccccc><b>Hora<BR>Final</b></td>";
		echo "<td bgcolor=#cccccc><b>Fecha</b></td>";
		echo "<td bgcolor=#cccccc><b>Documento</b></td>";
		echo "<td bgcolor=#cccccc><b>Paciente</b></td>";
		echo "<td bgcolor=#cccccc><b>Responsable</b></td>";
		echo "<td bgcolor=#cccccc><b>Cirugias</b></td>";
		echo "<td bgcolor=#cccccc><b>Medicos</b></td>";
		echo "<td bgcolor=#cccccc><b>Equipos</b></td>";
		echo "<td bgcolor=#cccccc><b>Usuario</b></td>";
		echo "<td bgcolor=#cccccc><b>Comentario</b></td>";
		echo "</tr>"; 
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<tr>";
			echo "<td>".$row[0]."</td>";
			echo "<td>".$row[1]."</td>";
			echo "<td>".$row[2]."</td>";
			echo "<td>".$row[3]."</td>";
			echo "<td>".$row[4]."</td>";
			echo "<td>".$row[5]."</td>";
			echo "<td>".$row[6]."</td>";
			echo "<td>".$row[7]."</td>";
			echo "<td>".$row[8]."</td>";
			echo "<td>".$row[9]."</td>";
			echo "<td>".$row[10]."</td>";
			echo "<td>".$row[11]."</td>";
			echo "<td>".$row[12]."</td>";
			echo "</tr>"; 
		}
		echo "</table>"; 
	}
}
?>
</body>
</html>
