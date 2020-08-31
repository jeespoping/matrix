<html>
<head>
<title>ASIGNACION DE TURNOS X MEDICO</title>
	<!-- UTF-8 is the recommended encoding for your pages -->
	    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />

	<!-- Loading Theme file(s) -->
	    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

	<!-- Loading Calendar JavaScript files -->
	    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
	    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
	    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

	<!-- Loading language definition file -->
	    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
</head>
<?php
include_once("conex.php");
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<form action='000001_rrx6.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if (!isset($wfec) or !isset($wmed))
	{
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor='#cccccc' colspan=2 align=center>INFORME DE CITAS X MEDICO</td></tr>";
		echo "<tr><td bgcolor=#cccccc>Medico</td><td bgcolor=#cccccc><select name='wmed'>";
		$query = "select codigo,nombre,oficio,tipo,edad_pac,activo from ".$empresa."_000008 where oficio='1-MEDICO'  order by codigo";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		for ($i=0;$i<$num1;$i++)
		{	
			$row1 = mysql_fetch_array($err1);
			if ($row1[0] == substr($Medico,0,5))
				echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
			else
				echo "<option>".$row1[0]."-".$row1[1]."</option>";
		}
		echo "</td></tr>";
		echo "<td bgcolor='#cccccc'>Fecha :</td><td bgcolor='#cccccc'><input type='TEXT' name='wfec' size=10 maxlength=10 id='wfec' value='".date("Y-m-d")."' disable><button id='trigger1'>...</button>";
		?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
			//]]></script>
		<?php
		echo "</td></tr>";
		echo "<td bgcolor='#cccccc'>Turnos Opcionales :</td><td bgcolor='#cccccc'><input type='TEXT' name='wtur' size=5 maxlength=5 id='wtur' value=0></td></tr>";
		echo "<tr><td bgcolor='#cccccc' colspan=2 align=center><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where fecha='".$wfec."' and cod_med='".substr($wmed,0,strpos($wmed,"-"))."' order by hi";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$color="#CCCCCC";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=8><b>AGENDA DE TRABAJO DIARIA</b></td></tr>";
		echo "<tr><td align=center colspan=8><b>UNIDAD DE CARDIOLOGIA NO INVASIVA</b></td></tr>";
		echo "<tr><td align=center colspan=8><b>MEDICO : ".substr($wmed,strpos($wmed,"-")+1)."</b></td></tr>";
		echo "<tr><td align=center colspan=8><b>FECHA  : ".$wfec."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Equipo</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";	
	  	echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";	
	  	echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Usuario</b></font></td>";
		echo "</tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if ($i%2 == 0)
				$color="#ffffff";
			else
				$color="#dddddd";
			echo "<tr>";
			echo "<td bgcolor=".$color." align=center><font size=2>".substr($row[4],0,2).":".substr($row[4],2,2)."</font></td>";
			$query = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".$row[1]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row1 = mysql_fetch_array($err1);		
			echo "<td bgcolor=".$color."><font size=2>".$row1[1]."</font></td>";
			$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from ".$empresa."_000006 where codigo='".$row[2]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row2 = mysql_fetch_array($err1);
			echo "<td bgcolor=".$color."><font size=2>".$row2[1]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[6]."</font></td>";
			$query = "select nit,descripcion,activo from ".$empresa."_000002 where nit='".$row[7]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row3 = mysql_fetch_array($err1);
			echo "<td bgcolor=".$color."><font size=2>".$row3[1]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[8]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[9]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[11]."</font></td>";
		}
		echo "</table><br>";
		$color="#CCCCCC";
		echo "<table border=1 align=center>";
		echo "<tr><td align=center colspan=8><b>TURNOS OPCIONALES</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Equipo</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";	
	  	echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";	
	  	echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Usuario</b></font></td>";
		echo "</tr>";
		$spaces = "";
		for ($i=0;$i<35;$i++)
		{
			$spaces = $spaces."&nbsp";
		}
		for ($i=0;$i<$wtur;$i++)
		{
			echo "<tr>";
			echo "<td><br><br>&nbsp</td>";
		  	echo "<td><br><br>&nbsp</td>";
		  	echo "<td><br><br>".$spaces."</td>";
		  	echo "<td><br><br>".$spaces."</td>";	
		  	echo "<td><br><br>".$spaces."</td>";	
		  	echo "<td><br><br>&nbsp</td>";
		  	echo "<td><br><br>&nbsp</td>";
		  	echo "<td><br><br>&nbsp</td>";
			echo "</tr>";
		}
		include_once("free.php");
	}
}
?>
</body>
</html>