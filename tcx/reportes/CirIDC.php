<html>
<head>
<title>MATRIX</title>
	<!-- UTF-8 is the recommended encoding for your pages -->
 
    <title>Zapatec DHTML Calendar</title>
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
 <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#CCCCCC;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo3{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:normal;}
    	#tipo4{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:normal;}
    	#tipo5{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:normal;}
    	#tipo6{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:normal;}
    	
    	
    </style>    
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Control de Cirugias del IDC</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>CirIDC.php Ver 2015-03-17</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='CirIDC.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfecha1) or !isset($wfecha2) or $wfecha1 > $wfecha2)
	{
		if (!isset($wfecha1) or !isset($wfecha2))
		{
			$wfecha1=date("Y-m-d");
			$wfecha2=date("Y-m-d");
		}
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=3><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>CONTROL DE CIRUGIAS DEL IDC</b></td></tr>";
		echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA INICIAL:</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha1' size=10 maxlength=10 id='wfecha1' readonly='readonly' value=".$wfecha1." class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA FINAL:</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha2' size=10 maxlength=10 id='wfecha2' readonly='readonly' value=".$wfecha2." class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc  colspan=3 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<center><table border=0>";
		echo "<tr><td colspan=18 id=tipo2>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
		echo "<tr><td colspan=18 id=tipo2>DIRECCION DE INFORMATICA</td></tr>";
		echo "<tr><td colspan=18 id=tipo2>CONTROL DE CIRUGIAS DEL IDC</td></tr>";
		echo "<tr><td colspan=18 id=tipo2>Desde : ".$wfecha1." Hasta ".$wfecha2."</td></tr>";
		echo "<tr>";
		echo "<td id=tipo1>Nro</td>";
		echo "<td id=tipo1>Codigo<br>Cirugia</td>";
		echo "<td id=tipo1>Fecha</td>";
		echo "<td id=tipo1>Hora<br>Inicio</td>";
		echo "<td id=tipo1>Hora<br>Final</td>";
		echo "<td id=tipo1>Quirofano</td>";
		echo "<td id=tipo1>Identificacion</td>";
		echo "<td id=tipo1>Historia</td>";
		echo "<td id=tipo1>Nombre<br>Paciente</td>";
		echo "<td id=tipo1>Fecha<br>Nacimiento</td>";
		echo "<td id=tipo1>Sexo</td>";
		echo "<td id=tipo1>Tipo<br>Anestesia</td>";
		echo "<td id=tipo1>Nit<br>Responsable</td>";
		echo "<td id=tipo1>Nombre<br>Responsable</td>";
		echo "<td id=tipo1>Tipo<br>Programacion</td>";
		echo "<td id=tipo1>Tipo<br>Cirugia</td>";
		echo "<td id=tipo1>Cirugias<br>Realizadas</td>";
		echo "<td id=tipo1>Medicos y <br>Especialidades</td>";
		echo "</tr>"; 
		//                 0       1      2      3     4      5      6      7      8       9     10   11    12      13    14     15    16     17     18      19     20
		$query = "select Turtur,Turfec,Turhin,Turqui,Turdoc,Turhis,Turnom,Turfna,Tursex,Turtan,Tureps,'E',Turtcx,Turtip,Mcicod,Cirdes,Medcod,Mednom,Medesp,Espdet,Turhfi "; 
		$query .= "  from ".$empresa."_000011,".$empresa."_000008,".$empresa."_000002,".$empresa."_000010,".$empresa."_000006,".$empresa."_000005 "; 
		$query .= "  where turfec between '".$wfecha1."' and '".$wfecha2."'";
		$query .= "    and turtur = mcitur ";
		$query .= "    and mcicod = circod ";
		$query .= "    and turtur = Mmetur ";
		$query .= "    and Mmemed = Medcod ";
		$query .= "    and Medesp = Espcod ";
		$query .= "    Order by 2 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$data=array();
			$turant="";
			$k=-1;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($turant != $row[0])
				{
					$k = $k + 1;
					$data[$k][0]=$row[0];
					$data[$k][1]=$row[1];
					$data[$k][2]=$row[2];
					$data[$k][3]=$row[3];
					$data[$k][4]=$row[4];
					$data[$k][5]=$row[5];
					$data[$k][6]=$row[6];
					$data[$k][7]=$row[7];
					$data[$k][8]=$row[8];
					switch ($row[9])
					{
						case "G":
							$data[$k][9]="GENERAL";
						break;
						case "R":
							$data[$k][9]="REGIONAL";
						break;
						case "L":
							$data[$k][9]="LOCAL";
						break;
						case "C":
							$data[$k][9]="CONDUCTIVA";
						break;
						case "Q":
							$data[$k][9]="RAQUIDEA";
						break;
						case "E":
							$data[$k][9]="EPIDURAL";
						break;
						case "A":
							$data[$k][9]="LOCAL ASISTIDA";
						break;
					}
					
					$consultaAjax = '';
					include_once("root/comun.php");
					
					$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
					
					$data[$k][10]=$row[10];
					$query = "SELECT Empnom FROM ".$wcliame."_000024  ";
					$query .= " where Empcod = '".$row[10]."'";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
						$row1 = mysql_fetch_array($err1);
					else
					{
						$query = "SELECT Entdes FROM ".$empresa."_000003  ";
						$query .= " where Entcod = '".$row[10]."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row1 = mysql_fetch_array($err1);
					}
					$data[$k][11]=$row1[0];
					$data[$k][16]=$row[20];
					switch ($row[12])
					{
						case "A":
							$data[$k][13]="AMBULATORIA";
						break;
						case "H":
							$data[$k][13]="HOSPITALIZADA";
						break;
						case "E":
							$data[$k][13]="ESPECIAL";
						break;
					}
					switch ($row[13])
					{
						case "E":
							$data[$k][12]="ELECTIVA";
						break;
						case "U":
							$data[$k][12]="URGENTE";
						break;
					}
					$data[$k][14]="";
					$data[$k][15]="";
					$turant=$row[0];
				}
				if(strpos($data[$k][14],$row[15]) === false)
					$data[$k][14] .= $row[15]."<br>";
				if(strpos($data[$k][15],$row[16]) === false)
					$data[$k][15] .= $row[16]." ".$row[17]."-".$row[19]."<br>";
			}
			for ($i=0;$i<=$k;$i++)
			{
				$j=$i + 1;
				if($i % 2 == 0)
				{
					$tipo1="tipo3";
					$tipo2="tipo5";
				}
				else
				{
					$tipo1="tipo4";
					$tipo2="tipo6";
				}
				echo "<tr>";
				echo "<td id=tipo1>".$j."</td>";
				echo "<td id=".$tipo1.">".$data[$i][0]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][1]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][2]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][16]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][3]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][4]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][5]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][6]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][7]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][8]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][9]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][10]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][11]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][12]."</td>";
				echo "<td id=".$tipo1.">".$data[$i][13]."</td>";
				echo "<td id=".$tipo2.">".$data[$i][14]."</td>";
				echo "<td id=".$tipo2.">".$data[$i][15]."</td>";
				echo "</tr>"; 
			}
		}
		echo "</table></center>"; 
	}
}
?>
</body>
</html>
