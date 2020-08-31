<html>
<head>
<title>MATRIX</title>
     <title>Zapatec DHTML Calendar</title>

	<!-- Loading Theme file(s) -->
		<link rel="stylesheet" href="../../zpcal/themes/winter.css" />

	<!-- Loading Calendar JavaScript files -->
		<script type="text/javascript" src="../../zpcal/src/utils.js"></script>
		<script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
		<script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
		<!-- Loading language definition file -->
		<script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>CIRUGIAS PROGRAMADAS X FECHA X TIPO DE ENTIDAD</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Repcirxent.php Ver. 2015-07-17</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
 function buscar($item,$data)
 {
	 for ($i=0;$i<8;$i++)
	 {
		 if($data[$i][0] == $item)
			return $data[$i][1];
	 }
 }
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Repcirxent.php' method=post>";
	if(!isset($v0) or !isset($v1))
	{
		if(!isset($v0))
			$v0 = date("Y-m-d");
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>CIRUGIAS PROGRAMADAS X FECHA X TIPO DE ENTIDAD</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' id='v0' readonly='readonly' value=".$v0." size=10 maxlength=10>&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v0',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo  "<tr><td bgcolor=#cccccc align=left>Tipos de Empresa (EPS-PRE-ASE-PAR-SOA-REG-ARP-VIN)</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$TE = array();
		$TE[0][0] = "EPS";
		$TE[0][1] = "09";
		$TE[1][0] = "PRE";
		$TE[1][1] = "03";
		$TE[2][0] = "ASE";
		$TE[2][1] = "11";
		$TE[3][0] = "PAR";
		$TE[3][1] = "02";
		$TE[4][0] = "SOA";
		$TE[4][1] = "12";
		$TE[5][0] = "REG";
		$TE[5][1] = "01";
		$TE[6][0] = "ARP";
		$TE[6][1] = "10";
		$TE[7][0] = "VIN";
		$TE[7][1] = "07";
		$vx=explode("-",$v1);
		$win1="(".chr(34).$vx[0].chr(34);	
		$win2="(".chr(34).buscar($vx[0],$TE).chr(34);	
		for ($i=1;$i<count($vx);$i++)
		{
			$win1 .= ",".chr(34).$vx[$i].chr(34);
			$win2 .= ",".chr(34).buscar($vx[$i],$TE).chr(34);
		}
		$win1 .= ")"; 
		$win2 .= ")"; 
		$query  = "select Turtur,Turqui,Turhin,Turhfi,Turfec,Turdoc,Turnom,Empnom,Turcir,Turmed,Turequ,Turtel from tcx_000011,cliame_000024 ";
		$query .= "where Turfec = '".$v0."' ";
		$query .= "  and Tureps = Empcod ";
		$query .= "  and Emptem in ".$win2." ";
		$query .= "  and tcx_000011.Fecha_data >= '2015-02-24' ";
		$query .= "  UNION ALL ";
		$query .= "select Turtur,Turqui,Turhin,Turhfi,Turfec,Turdoc,Turnom,Entdes,Turcir,Turmed,Turequ,Turtel from tcx_000011,tcx_000003 ";
		$query .= "where Turfec = '".$v0."' ";
		$query .= "  and Tureps = Entcod ";
		$query .= "  and Enttip in ".$win1." ";
		$query .= "  and tcx_000011.Fecha_data < '2015-02-24' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=1>";
		echo "<tr><td colspan=12 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=12 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=12 align=center><b>CIRUGIAS PROGRAMADAS X FECHA X TIPO DE ENTIDAD</b></td></tr>";
		echo "<tr><td colspan=12 align=center><b>X FECHA : ".$v0."</b></td></tr>";
		echo "<tr><td colspan=12 align=center><b>SELECCION : ".$v1."</b></td></tr>";
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
		echo "<td bgcolor=#cccccc><b>Telefonos</b></td>";
		echo "</tr>"; 
		$t=array();
		$t[0] = 0;
		$t[1] = 0;
		$t[2] = 0;
		$t[3] = 0;
		$t[4] = 0;
		$t[5] = 0;
		$t[6] = 0;
		$t[7] = 0;
		$t[8] = 0;
		$t[9] = 0;
		$t[10] = 0;
		$t[11] = 0;
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
			echo "</tr>"; 
		}
		echo "</table>"; 
	}
}
?>
</body>
</html>
