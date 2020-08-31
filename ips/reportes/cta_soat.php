<html>
<head>
  <title>MATRIX Certificado de Atencion Medica SOAT</title>
   <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Courier New;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo5{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo6{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo7{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='cta_soat.php' method=post>";
		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($numero))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>CERTIFICADO DE ATENCION MEDICA SOAT</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Digite el Numero del Accidente de Transito</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='numero' size=8 maxlength=8></td></tr></table>";
		}
		else
		{
			$query = "select Amstdo, Amsdoc, Amsexp, Amsap1, Amsap2, Amsno1, Amsno2, Amsdir, Amsmun, Amsdep, Amsdec, Amside, Amsexd, Amsfat, Amshat, Amstel, Amsfin, Amshin from ".$empresa."_000123 where Amsnum=".$numero;
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				echo "<center>";
				echo "<table border=1 id=tipo2>";
				echo "<tr><td colspan=4 align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png' width=20%></td></tr>";
				echo "<tr><td colspan=4 align=center><b>REPUBLICA DE COLOMBIA</b></td></tr>";
				echo "<tr><td colspan=4 align=center><b>MINISTERIO DE SALUD</b><td></tr>";
				echo "<tr><td colspan=4 align=center><b>CERTIFICADO DE ATENCION MEDICA PARA VICTIMAS DE ACCIDENTES DE TRANSITO</b></td></tr>";
				echo "<tr><td colspan=4 align=center><b>EXPEDIDO POR LA INSTITUCION PRESTADORA DE SERVICIOS DE SALUD</b></td></tr>";
				echo "<tr><td colspan=4>El suscrito medico del Servicio de Urgencias de la Institucion Prestadora de Servicios :<b>LAS AMERICAS CLINICA DEL SUR</b></td></tr>";
				echo "<tr><td align=center>Con domicilio en :<b> Carrera 43 A Nro. 27 a Sur 44</b></td><td align=center>Ciudad :<b> Envigado</b></td><td align=center>Departamento :<b> Antioquia</b></td><td align=center>Telefono : <b>3310600</b></td></tr>";
				echo "<tr><td colspan=4>CERTIFICA que atendio en el Servicion de Urgencias al Señor(a) :</td></tr>";
				echo "<tr><td align=center><b>".$row[5]." ".$row[6]." ".$row[3]." ".$row[4]."</b></td><td align=center>Identificado con :<b>".$row[0]."</b></td><td align=center>No. <b>".$row[1]."</b></td><td align=center>De : <b>".$row[2]."</b></td></tr>";
				echo "<tr><td align=center>Residente en : <b>".$row[7]."</b></td><td align=center>Ciudad : <b>".substr($row[8],strpos($row[8],"-")+1)."</b></td><td align=center>Departamento : <b>".substr($row[9],strpos($row[9],"-")+1)."</b></td><td align=center>Telefono :  <b>".$row[15]."</b></td></tr>";
				echo "<tr><td colspan=2>Quien segun declaracion de : <b>".$row[10]."</b></td><td align=center>con CC Nro. : <b>".$row[11]."</b></td><td align=center>Expedida en : <b>".$row[12]."</b></td></tr>";
				echo "<tr><td colspan=4>Fue victima del Accidente de Transito ocurrido el dia :<b>".$row[13]."</b> a las : <b>".$row[14]."</b> Horas</td></tr>";
				echo "<tr><td colspan=4>Ingresando al Servicio de Urgencias de esta Institucion el Dia <b>".$row[16]."</b> a las : <b>".$row[17]."</b> Horas</td></tr>";
				
				echo "<tr><td colspan=4>Con los siguientes hallazgos : </td></tr>";
				echo "<tr><td colspan=4 align=center bgcolor=#cccccc><b>SIGNOS VITALES</b></td></tr>";
				echo "<tr><td align=center>TA : <input type='TEXT' name='s1a' size=3 maxlength=3 class=tipo3>/<input type='TEXT' name='s1b' size=3 maxlength=3 class=tipo3> mmHg</td><td align=center>Fc: <input type='TEXT' name='s2' size=3 maxlength=3 class=tipo3> x min</td><td align=center>Fr : <input type='TEXT' name='s3' size=3 maxlength=3 class=tipo3> x min</td><td align=center>T : <input type='TEXT' name='s4' size=3 maxlength=3 class=tipo3> C</td></tr>";
				echo "<tr><td colspan=4 align=center>Estado de Conciencia : <b><input type='checkbox' name='con1'> Alerta <input type='checkbox' name='con2'> Obnubilado <input type='checkbox' name='con3'> Estupuroso <input type='checkbox' name='con4'> Coma <input type='checkbox' name='con5'> Glasgow(7) </b></td></tr>";
				echo "<tr><td colspan=4 align=center>Estado de Embriaguez : <b><input type='checkbox' name='emb1'> SI <input type='checkbox' name='emb2'> NO </b></td></tr>";
				echo "<tr><td colspan=4 align=center>(En caso positivo tomar muestra de sangre para alcoholemia u otras drogas)</td></tr>";
				echo "</table>";
				
				echo "<table border=1 id=tipo1>";
				echo "<tr><td colspan=2 align=center bgcolor=#cccccc><b>--------------------------------------------------------DATOS POSITIVOS--------------------------------------------------------</b></td></tr>";
				echo "<tr><td bgcolor=#DDDDDD>Cabeza y Organos de los Sentidos : </td><td bgcolor=#DDDDDD>Cuello : .........................</td></tr>";
				echo "<tr><td>&nbsp<br><br><br><br><br></td><td>&nbsp<br><br><br><br><br></td></tr>";
				echo "<tr><td bgcolor=#DDDDDD>Torax y Cardiopulmonar : </td><td bgcolor=#DDDDDD>Abdomen : </td></tr>";
				echo "<tr><td>&nbsp&nbsp&nbsp<br><br><br><br><br></td><td>&nbsp&nbsp&nbsp</td></tr>";
				echo "<tr><td bgcolor=#DDDDDD>Genitourinario : </td><td bgcolor=#DDDDDD>Pelvis : </td></tr>";
				echo "<tr><td>&nbsp&nbsp&nbsp<br><br><br><br><br></td><td>&nbsp&nbsp&nbsp</td></tr>";
				echo "<tr><td bgcolor=#DDDDDD>Dorso y Extremidades : </td><td bgcolor=#DDDDDD>Neurologico : </td></tr>";
				echo "<tr><td>&nbsp&nbsp&nbsp<br><br><br><br><br></td><td>&nbsp&nbsp&nbsp</td></tr>";
				echo "<tr><td bgcolor=#DDDDDD>Impresion Diagnostica : </td><td bgcolor=#DDDDDD>Diagnostico Definitivo : </td></tr>";
				echo "<tr><td>&nbsp&nbsp&nbsp<br><br><br><br><br></td><td>&nbsp&nbsp&nbsp</td></tr>";
				echo "<tr><td colspan=2 bgcolor=#DDDDDD>Conducta : </td></tr>";
				echo "<tr><td colspan=2>&nbsp&nbsp&nbsp<br><br><br><br><br></td></tr>";
				echo "<tr><td>Monbres y Apellidos del Medico</td><td>Firma y Sello</td></tr>";
				echo "<tr><td>&nbsp<br>&nbsp<br>&nbsp<br>&nbsp</td><td>&nbsp<br>&nbsp<br>&nbsp<br>&nbsp</td></tr>";
				echo "<tr><td colspan=2>Registro Medico Nro.<br><br></td></tr>";
				echo "</table>";
				echo "</center>";		
			}
			else
			{
				echo "<center><table border=0 align=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE ESE NUMERO DE ACCIDENTE DE TRANSITO !!!!</MARQUEE>";
				echo "<br><br>";
			}
		}
	}
?>
</body>
</html>