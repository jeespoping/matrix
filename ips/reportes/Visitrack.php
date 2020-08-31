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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Archivo Visitrack</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Visitrack.php Ver. 2016-02-15</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='Visitrack.php' method=post>";
	

	

	if(!isset($wfecha1) or !isset($wfecha2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=3><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=3>ARCHIVO VISITRACK</td></tr>";
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
	}
	else
	{
		//                    0      1        2      3       4       5       6       7       8       9       10      11      12     13      14
		$query = " SELECT  pactdo, pacdoc, Pachis, ingnin, pacap1, pacap2, pacno1, pacno2, pacdir, pacbar, 'N/A', pactel, Pacfna, Ingcem, ingent ";
	    $query .= "  FROM  clisur_000100, clisur_000101,mhoscs_000018  ";
	    $query .= "  WHERE  pacact = 'on' ";
		$query .= "    AND  ingfei between '".$wfecha1."' AND  '".$wfecha2."' ";
		$query .= "    AND  pachis = inghis ";
		$query .= "    AND  inghis = Ubihis ";
		$query .= "    AND  Ingnin = Ubiing ";
		$query .= "    AND  Ubiald = 'off' ";
		$query .= " ORDER BY 3,4 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../../planos/ips/visitrack.txt"; 
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = " SELECT  Bardes ";
				$query .= "  FROM  root_000034  ";
				$query .= "  WHERE  Barcod = '".$row[9]."' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$row[10] = $row1[0];
				}
				$query  = " select CONCAT(mhoscs_000018.Ubisac,'-',mhoscs_000011.Cconom,' Serv. ',mhoscs_000018.Ubihac) from mhoscs_000018,mhoscs_000011 ";
				$query .= "   where mhoscs_000018.ubihis = '".$row[2]."'";  
				$query .= "     and mhoscs_000018.ubiing = '".$row[3]."'";   
				$query .= "     and mhoscs_000018.ubisac = mhoscs_000011.ccocod ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$row[15] = $row1[0];
				}
				else
					$row[15] = "NO DISPONIBLE";
				$ann=(integer)substr($row[12],0,4)*360 +(integer)substr($row[12],5,2)*30 + (integer)substr($row[12],8,2);
				$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				$ann1=($aa - $ann)/360;
				$edad=round($ann1,0);
				$registro = "";
				$registro = $row[0].chr(44).$row[1].chr(44).$row[2].chr(44).$row[3].chr(44).$row[4].chr(44).$row[5].chr(44).$row[6].chr(44).$row[7].chr(44).$row[8].chr(44).$row[9]."-".$row[10].chr(44).$row[11].chr(44).$edad.chr(44).$row[13]."-".$row[14].chr(44).$row[15];
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
