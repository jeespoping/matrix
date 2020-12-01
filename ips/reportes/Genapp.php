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
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion Archivo Plano de Pacientes</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Genapp.php Ver. 2017-02-09</b></font></tr></td></table>
</center>
<?php

$consultaAjax = 'Esto solo es para que no cargue jquery y otras cosas más';

include_once("conex.php");
include_once("root/comun.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='Genapp.php' method=post>";
	
	$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	

	if(!isset($wfecha1) or !isset($wfecha2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=3><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=3>GENERACION ARCHIVO PLANO DE PACIENTES</td></tr>";
		echo "<tr><td bgcolor='#cccccc' align=center valign=center>Fecha Inicial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wfecha1' size=10 maxlength=10 id='wfecha1' readonly='readonly' class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor='#cccccc' align=center valign=center>Fecha Final</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wfecha2' size=10 maxlength=10 id='wfecha2' readonly='readonly'  class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc  colspan=3 align=center><input type='submit' value='ENTER'></td></tr></table>";
		
		echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
	}
	else
	{
		//                    0      1        2      3       4       5       6       7       8       9      10      11      12     13      14       15      16      17         18
		$query = " SELECT  pactdo, pacdoc, Pachis, ingnin, pacap1, pacap2, pacno1, pacno2, pacdir, pacmuh, Nombre, pactel, Pacmov, Ingcem, ingent, Paccor, pactam, ingdig, Descripcion ";
	    $query .= "  FROM  ".$wcliame."_000100, ".$wcliame."_000101, ".$wmovhos."_000018,root_000006,root_000011, ".$wmovhos."_000011  ";
	    $query .= "  WHERE  pacact = 'on' ";
		$query .= "    AND  ingfei between '".$wfecha1."' AND  '".$wfecha2."' ";
		$query .= "    AND  pachis = inghis ";
		$query .= "    AND  inghis = Ubihis ";
		$query .= "    AND  Ingnin = Ubiing ";
		$query .= "    AND  Ubiald = 'off' ";
		$query .= "    AND  pacmuh = root_000006.codigo ";
		$query .= "    AND  ingdig = root_000011.codigo ";
		$query .= "    AND  ccocod = ubisac ";
		$query .= "    AND  ccodom = 'on' ";
		echo $query .= " ORDER BY 3,4 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../../planos/ips/visitrack.txt"; 
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				switch($row[16])
				{
					case "MC":
						$row[16] = "CRONICO";
					break; 
					case "MA":
						$row[16] = "AGUDO";
					break;
					case "MH":
						$row[16] = "HOSPITALIZADO";
					break;
				}
				$registro = "";
				$registro = $row[0].chr(94).$row[1].chr(94).$row[2].chr(94).$row[3].chr(94).$row[4].chr(94).$row[5].chr(94).$row[6].chr(94).$row[7].chr(94).$row[8].chr(94).$row[9]."-".$row[10].chr(94).$row[11].chr(94).$row[12].chr(94).$row[13]."-".$row[14].chr(94).$row[15].chr(94).$row[16].chr(94).$row[17]."-".$row[18];
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				echo "REGISTRO GRABADO NRO : ".$i."<br>";
			}
			echo "<b>TOTAL REGISTROS GRABADOS : ".$num."</b><br>";
			fclose ($file);
			$ruta="../../planos/ips/visitrack.txt";
			echo "<A href=".$ruta.">Haga Click Para Bajar el Archivo de VISITRACK</A>";
		}
	}
}
?>
</body>
</html>
