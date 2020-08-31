<html>
<?php
include_once("conex.php");
echo "<head>";
echo "<title>MATRIX</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<form action='000001_prx4.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	$key = substr($user,2,strlen($user));
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	$year = (integer)substr($par2,0,4);
	$month = (integer)substr($par2,5,2);
	$day = (integer)substr($par2,8,2);
	$nomdia=mktime(0,0,0,$month,$day,$year);
	$nomdia = strftime("%w",$nomdia);
	switch ($nomdia)
	{
		case 0:
			$diasem = "DOMINGO";
			break;
		case 1:
			$diasem = "LUNES";
			break;
		case 2:
			$diasem = "MARTES";
			break;
		case 3:
			$diasem = "MIERCOLES";
			break;
		case 4:
			$diasem = "JUEVES";
			break;
		case 5:
			$diasem = "VIERNES";
			break;
		case 6:
			$diasem = "SABADO";
			break;
	}
	echo "<input type='HIDDEN' name= 'par1' value='".$par1."'>";
	echo "<input type='HIDDEN' name= 'par2' value='".$par2."'>";
	echo "<input type='HIDDEN' name= 'par3' value='".$par3."'>";
	echo "<input type='HIDDEN' name= 'Diasem' value='".$diasem."'>";
	$query = "select cod_med,".$empresa."_000008.nombre,cod_equ,".$empresa."_000003.descripcion,cod_exa,".$empresa."_000006.descripcion,fecha,".$empresa."_000001.hi,".$empresa."_000001.hf,nom_pac,nit_resp,".$empresa."_000002.descripcion,telefono,edad,comentarios from ".$empresa."_000001,".$empresa."_000008,".$empresa."_000003,".$empresa."_000006,".$empresa."_000002 ";
	$query .= "   where  cod_equ='".$par1."' ";
	$query .= "        and  fecha='".$par2."' ";
	$query .= "        and ".$empresa."_000001.hi='".$par3."' ";
	$query .= "        and cod_med=".$empresa."_000008.codigo ";
	$query .= "        and cod_equ=".$empresa."_000003.codigo ";
	$query .= "        and cod_exa=".$empresa."_000006.codigo ";
	$query .= "        and nit_resp=".$empresa."_000002.nit ";
	$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<table border=0 align=left>";
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
		echo "<tr><td><font size=2>Medico : ".$row[1]."</font></td></tr>";
		echo "<tr><td><font size=2>Examen : ".$row[5]."</font></td></tr>";
		echo "<tr><td><font size=2><b><u>Fecha : ".$diasem." ".substr($row[6],8,2)."-".substr($row[6],5,2)."-".substr($row[6],0,4)."";
		if(substr($row[7],0,2) > "12")
		{
			$row[7] ="". (string)((integer)substr($row[7],0,2) - 12).":".substr($row[7],2,2). " pm ";
			echo "&nbsp &nbsp".$row[7]."</u></b></font></td></tr>";
		}
		else
		echo "&nbsp &nbsp".substr($row[7],0,2).":".substr($row[7],2,2)." am</u></b></font></td></tr>";
		echo "<tr><td><font size=2>Paciente : ".$row[9]."</font></td></tr>";
		echo "<tr><td><font size=2>Responsable : ".$row[11]."</font></td></tr>";
		echo "<tr><td><font size=2>Telefono : ".$row[12]."</font></td></tr>";
		echo "<tr><td><font size=2>Edad : ".$row[13]."</font></td></tr>";
		echo "<tr><td><font size=2><u>COMENTARIOS</u> : ".$row[14]."</font></td></tr>";
	}
	else
		echo "<tr><td>ERROR EN DATOS</td></tr>";
	?>
	<script type="text/javascript" language="javascript1.2">
	<!--
	function printPage()
	{
	    document.all.print.style.visibility = 'hidden';
	    // Do print the page
	    if (typeof(window.print) != 'undefined') {
	        window.print();
	    }
	    document.all.print.style.visibility = '';
	}
	//-->
	</script>
	<?php
	echo "<tr><td><input type='button' style='visibility: ; width: 100px; height: 25px' name='print' value='IMPRIMIR' onclick='printPage()'></td></tr>";
	echo "</table>";
	include_once("free.php");
}
?>
</body>
</html>