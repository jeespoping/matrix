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
<!--<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>CIRUGIAS PROGRAMADAS X MEDICO</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Repxmed.php Ver. 2011-02-23</b></font></td></tr></table>
</center>-->
<?php
include_once("conex.php");
include_once("root/comun.php");
$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
$wactualiz = "2011-02-23";
encabezado( "CIRUGIAS PROGRAMADAS X MEDICO", $wactualiz, $institucion->baseDeDatos );
 @session_start();
 if(!isset($_SESSION['user']))
	echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Repxmed.php' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	if(!isset($v0) or !isset($v1) or !isset($v2))
	{
		if (!isset($v0))
			$v0=date("Y-m-d");
		if (!isset($v1))
			$v1=date("Y-m-d");
		echo  "<center><table border=0>";
		//echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		//echo "<tr><td colspan=2 align=center><b>CIRUGIAS PROGRAMADAS X MEDICO</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10 id='v0' readonly='readonly' value=".$v0.">&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v0',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10 id='v1' readonly='readonly' value=".$v1.">&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v1',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo  "<tr><td bgcolor=#cccccc align=center>Medico</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v2' size=30 maxlength=30></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		//                             0                  1                 2                 3                4                 5                 6                  7                8                 9                 10                11           
		$query  = "select tcx_000011.Turtur,tcx_000011.Turqui,tcx_000011.Turhin,tcx_000011.Turhfi,tcx_000011.Turfec,tcx_000011.Turnom,tcx_000011.Turmed,tcx_000011.Turusg,tcx_000011.Turcir,tcx_000011.Tureps,tcx_000011.Turhis,tcx_000011.Turnin  ";
		$query .= " from tcx_000011 ";
		$query .= " where tcx_000011.turfec between  '".$v0."' and  '".$v1."' ";
		$query .= "   and tcx_000011.turmed like '%".$v2."%' ";
		$query .= " ORDER BY tcx_000011.Turfec";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=1>";
		//echo "<tr><td colspan=11 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		//echo "<tr><td colspan=11 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		//echo "<tr><td colspan=11 align=center><b>CIRUGIAS PROGRAMADAS X MEDICO</b></td></tr>";
		echo "<tr><td colspan=11 align=center><b>ENTRE FECHAS ".$v0." - ".$v1."</b></td></tr>";
		echo "<tr><td colspan=11 align=center><b>MEDICO ".$v2."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>Codigo</b></td>";
		echo "<td bgcolor=#cccccc><b>Quirofano</b></td>";
		echo "<td bgcolor=#cccccc><b>Hora<BR>Inicio</b></td>";
		echo "<td bgcolor=#cccccc><b>Hora<BR>Final</b></td>";
		echo "<td bgcolor=#cccccc><b>Fecha</b></td>";
		echo "<td bgcolor=#cccccc><b>Paciente</b></td>";
		echo "<td bgcolor=#cccccc><b>Historia-Ingreso</b></td>";
		echo "<td bgcolor=#cccccc><b>Medicos</b></td>";
		echo "<td bgcolor=#cccccc><b>Usuario</b></td>";
		echo "<td bgcolor=#cccccc><b>Cirgias</b></td>";
		echo "<td bgcolor=#cccccc><b>Responsable</b></td>";
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
			echo "<td>".$row[10]."-".$row[11]."</td>";
			echo "<td>".$row[6]."</td>";
			echo "<td>".$row[7]."</td>";
			echo "<td>".$row[8]."</td>";
			echo "<td>".$row[9]."</td>";
			echo "</tr>"; 
		}
	 echo "</table>"; 
	}
}
?>
</body>
</html>
