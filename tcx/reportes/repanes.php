<html>
<head>
<title>MATRIX</title>
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files --
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script> -->
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>

</center>
<?php
include_once("conex.php");
include_once("root/comun.php");

$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
$wactualiz = "2011-03-07";
encabezado( "Anestesiologos Asignados Por Fecha", $wactualiz, $institucion->baseDeDatos );
 @session_start();
 if(!isset($_SESSION['user']))
	echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='repanes.php?wemp_pmla=".$wemp_pmla."' method=post>";
	if(!isset($v0) or !isset($v1))
	{
		echo  "<center><table border=0>";
		//echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		//echo "<tr><td colspan=2 align=center><b>ANESTESIOLOGOS ASIGNADOS POR FECHA</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha de Cirugia</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' id='v0' readonly='readonly' size=10 maxlength=10>&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v0',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc align=center>Anestesiologo Asignado</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Medcod, Mednom from tcx_000020, tcx_000006 ";
		$query = $query." where Rciusu = Medcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='v1'>";
			echo "<option>*-Todos</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wanes=explode("-",$v1);
		//                   0      1      2      3      4      5      6      7      8      9    10      11     12     13     14     15     16
		$query =  "select Turtur,Turqui,Turhin,Turhfi,Turnom,Turfna,Tursex,Turtcx,Turtip,Turtan,Tureps,Entdes,Turpre,Turpan,Turcir,Mednom,Turmed from tcx_000011,tcx_000010,tcx_000020,tcx_000003,tcx_000006 ";
		$query .= " where turfec = '".$v0."' ";
		$query .= "   and turtur = mmetur ";
		$query .= "   and mmemed = rciusu ";
		if($wanes[0] != "*")
			$query .= "   and rciusu = '".$wanes[0]."' ";
		$query .= "   and mmemed = Medcod ";
		$query .= "   and Medane = 'on' ";
		$query .= "   and Tureps = Entcod ";
		$query .= "  Order by 16,1 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=0>";
		//echo "<tr><td colspan=16 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		//echo "<tr><td colspan=16 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		//echo "<tr><td colspan=16 align=center><b>ANESTESIOLOGOS ASIGNADOS POR FECHA</b></td></tr>";
		echo "<tr><td colspan=16 align=center><b>FECHA DE CONTROL : ".$v0."</b></td></tr>";
		echo "<tr><td colspan=16 align=center><b>ANESTESIOLOGO : ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>Codigo<br>Turno</b></td>";
		echo "<td bgcolor=#cccccc><b>Quirofano</b></td>";
		echo "<td bgcolor=#cccccc><b>Hora<br>Inicio</b></td>";
		echo "<td bgcolor=#cccccc><b>Hora<br>Finalizacion</b></td>";
		echo "<td bgcolor=#cccccc><b>Paciente</b></td>";
		echo "<td bgcolor=#cccccc><b>Edad</b></td>";
		echo "<td bgcolor=#cccccc><b>Sexo</b></td>";
		echo "<td bgcolor=#cccccc><b>Tipo<br>Cirugia</b></td>";
		echo "<td bgcolor=#cccccc><b>Tipo<br>Programacion</b></td>";
		echo "<td bgcolor=#cccccc><b>Tipo<br>Anestesia</b></td>";
		echo "<td bgcolor=#cccccc><b>Responsable</b></td>";
		echo "<td bgcolor=#cccccc><b>Preadmision</b></td>";
		echo "<td bgcolor=#cccccc><b>Preanestesia</b></td>";
		echo "<td bgcolor=#cccccc><b>Cirugia</b></td>";
		echo "<td bgcolor=#cccccc><b>Anestesiologo</b></td>";
		echo "<td bgcolor=#cccccc><b>Medicos</b></td>";
		echo "</tr>"; 
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$ann=(integer)substr($row[5],0,4)*360 +(integer)substr($row[5],5,2)*30 + (integer)substr($row[5],8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$ann1=($aa - $ann)/360;
			$meses=(($aa - $ann) % 360)/30;
			if ($ann1<1)
			{
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
			}
			else
			{
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
			}
			if($row[6] == "F")
				$sexo="Femenino";
			else
				$sexo="Masculino";
			switch($row[7])
			{
				case "H":
					$wtcx="Hospitalizada";
				break;
				case "A":
					$wtcx="Ambulatoria";
				break;
				case "E":
					$wtcx="Especial";
				break;
			}
			switch($row[8])
			{
				case "E":
					$wtip="Electiva";
				break;
				case "U":
					$wtip="Urgente";
				break;
			}
			switch($row[9])
			{
				case "R":
					$wtan="Regional";
				break;
				case "L":
					$wtan="Local";
				break;
				case "C":
					$wtan="Conductiva";
				break;
				case "G":
					$wtan="General";
				break;
				case "Q":
					$wtan="Raquidea";
				break;
				case "E":
					$wtan="Epidural";
				break;
				case "A":
					$wtan="Local Asistida";
				break;
			}
			switch($row[12])
			{
				case "on":
					$wpre="Si";
				break;
				case "off":
					$wpre="No";
				break;	
			}
			switch($row[13])
			{
				case "on":
					$wpan="Si";
				break;
				case "off":
					$wpan="No";
				break;	
			}
			if($i % 2 == 0)
				$wcolor="#99CCFF";
			else
				$wcolor="#FFFFFF";
			echo "<tr>";
			echo "<td bgcolor=".$wcolor.">".$row[0]."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$row[1]."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$row[2]."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$row[3]."</td>";
			echo "<td bgcolor=".$wcolor.">".$row[4]."</td>";
			echo "<td bgcolor=".$wcolor.">".$wedad."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$sexo."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$wtcx."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$wtip."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$wtan."</td>";
			echo "<td bgcolor=".$wcolor.">".$row[11]."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$wpre."</td>";
			echo "<td bgcolor=".$wcolor." align=center>".$wpan."</td>";
			echo "<td bgcolor=".$wcolor.">".$row[14]."</td>";
			echo "<td bgcolor=".$wcolor.">".$row[15]."</td>";
			echo "<td bgcolor=".$wcolor.">".$row[16]."</td>";
			echo "</tr>"; 
		}
		echo "</table>"; 
	}
}
?>
</body>
</html>
