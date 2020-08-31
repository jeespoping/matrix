<head>
  	<title>MATRIX  Estadisticas Generales</title>
</head>

<body onload=ira()>

<script type="text/javascript">
function enter()
{
	document.forms.est_infgnl.submit();
}
</script>

<?php
include_once("conex.php");

/* **********************************************************
   *     PROGRAMA PARA LA GESTION DE INFORMACION            *
   *        ESTADISTICA DE INFORMACION GENERAL              *
   **********************************************************/

//==================================================================================================================================
//PROGRAMA                   : est_infgnl.php
//AUTOR                      : Juan David Jaramillo R.
$wautor="Juan D. Jaramillo R.";
//FECHA CREACION             : Enero 30 de 2007
//FECHA ULTIMA ACTUALIZACION :
$wactualiz="(Version Enero 30 de 2007)";
//DESCRIPCION
//================================================================================================================================\\
//Este programa permite consultar a la direccion medica de clinica del sur, los indicadores semestrales de numero de consultas,   \\
//cirugias e imagenologia que se deben presentar a la seccional del salud.
//================================================================================================================================\\

//================================================================================================================================\\
//================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                 \\
//================================================================================================================================\\
//                                                                                                                                \\
//________________________________________________________________________________________________________________________________\\
//10 De Diciembre DE 2007: Se modifico el origen de los datos de las estadisticas y se modifico los codigos con  los cuales se 
// hacia la consulta. Ahora la consulta se hace con los codigos de los procedimientos por ejemplo: El condigo de una consulta con \\
//Ginecologo es '004' en vez de ser el codigo de la especilidad como se hacia anteriormente  la consulta que para este caso era el 009//
//________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\\
//                                                                                                                                \\
//________________________________________________________________________________________________________________________________\\

// COLORES
$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
$wclcy="#A4E1E8"; //COLOR DE TITULOS  -- Cyan
$color="#999999";


// INICIALIZACION DE VARIABLES

$valido=1;
session_start();
if (!isset($user))
{
	if(!isset($_SESSION['user']))
		session_register("user");
}

if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{
	echo "<form name='est_infgnl' action='est_infgnl.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";

	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));


    $wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");
    $wbd=$wbasedato;

    if (!isset($wfecini) or !isset($resultado))
	{
		// Inicio de captura de datos en formulario

    	echo "<p align=right><font size=2><b>Autor: ".$wautor."</b></font></p>";
    	echo "<table border=0 ALIGN=CENTER width=70%>";
		echo "<tr><td align=center><img src='/matrix/images/medical/citas/logo_".$wbasedato.".png' height='100' width='350'></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=3 bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>ESTADISTICAS INFORMACION CONSULTAS</b></font></td></tr>";
		echo "</table>";

		echo "<table border=0 ALIGN=CENTER width=50%>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td colspan=2 align=center bgcolor=".$wcf."><b><font text color=".$wclfg.">Rango de la Consulta</font></b></td></tr>";
		echo "<tr><td><br></td></tr>";

		if (!isset($wfecini))
		{
			$wfecini=$wfecha;
			$wfecfin=$wfecha;
		}
		echo "<tr><td align=right><font text color=".$wclfg." size=2><b>Fecha Inicial:</b></font></td>";
		echo "<td align=center><font text color=".$wclfg." size=2><INPUT TYPE='text' NAME='wfecini' VALUE='".$wfecini."' size=20 maxlength=10></font></td></tr>";
		echo "<tr><td align=right><font text color=".$wclfg." size=2><b>Fecha Final:</b></font></td>";
		echo "<td align=center><font text color=".$wclfg." size=2><INPUT TYPE='text' NAME='wfecfin' VALUE='".$wfecfin."' size=20 maxlength=10></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

		echo "<tr><td colspan=2 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=2>
				<input type='radio' name='wopc' value=G onclick='enter()'><b>Generar &nbsp&nbsp&nbsp </font></td></tr>";
		echo "</table>";
	}
	else
	{
		echo "<p align=right><font size=2><b>Autor: ".$wautor."</b></font></p>";
		echo "<table align=center width='65%'>";
		echo "<tr><td align=CENTER><img src='/matrix/images/medical/citas/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td align=left bgcolor=".$wcf."><b>Estadisticas Informacion General de Consultas</b></td></tr>";
		echo "<tr><td align=right><font size=2><A href='est_infgnl.php?wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></font></td></tr>";
		echo "<tr><td><font text size=2>Fecha Inicial: ".$wfecini."</font></td></tr>";
		echo "<tr><td><font text size=2>Fecha Final  :   ".$wfecfin."</font></td></tr>";
		echo "</table>";

		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		if(!isset($wfecini) or ($wfecini == ""))
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>No Ingreso un Rango de Fechas</td><tr>";
			echo "</table>";
		}
		else
		{
			$wnumcx=cal_usuurg($wfecini,$wfecfin);
			list ($wdiascx,$wtotcx,$wprocx)=cal_procx($wfecini,$wfecfin);

			echo "<table border=0 align=center width='65%'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td colspan=3 align=left bgcolor=".$wcf."><font text size=2><b>Resumen de Indicadores:</b></font></td></tr>";
			echo "<tr><td colspan=3 bgcolor='".$wcf."' align=center><b><font text color=".$wclfg." size=2>Oportunidad En La Atencion</b></font></td></tr>";
			list ($wnumdias,$wtotdias,$wproesp)=cal_proesp($wfecini,$wfecfin,'004');
			echo "<tr>";
			echo "<td bgcolor='".$wcf."' align=center><b><font text color=".$wclfg." size=2>Ginecologia</b><br>
			       Dias Asignados:&nbsp&nbsp".$wnumdias."<br>
     			   -----------------------------------<br>
     			   Total Consultas:&nbsp&nbsp".$wtotdias."<br>
     			   Indicador=".number_format($wproesp,2,'.',',')."</font></td>";
			list ($wnumdias,$wtotdias,$wproesp)=cal_proesp($wfecini,$wfecfin,'006');
			echo "<td bgcolor='".$wcf."' align=center><b><font text color=".$wclfg." size=2>Pediatria</b><br>
			       Dias Asignados:&nbsp&nbsp".$wnumdias."<br>
     			   -----------------------------------<br>
     			   Total Consultas:&nbsp&nbsp".$wtotdias."<br>
     			   Indicador=".number_format($wproesp,2,'.',',')."</font></td>";
			list ($wnumdias,$wtotdias,$wproesp)=cal_proesp($wfecini,$wfecfin,'054');
			echo "<td bgcolor='".$wcf."' align=center><b><font text color=".$wclfg." size=2>Cx. General</b><br>
			       Dias Asignados:&nbsp&nbsp".$wnumdias."<br>
     			   -----------------------------------<br>
     			   Total Consultas:&nbsp&nbsp".$wtotdias."<br>
     			   Indicador=".number_format($wproesp,2,'.',',')."</font></td>";
			echo "</tr>";
			echo "<tr>";
			list ($wnumdias,$wtotdias,$wproesp)=cal_proesp($wfecini,$wfecfin,'057');
			echo "<td bgcolor='".$wcf."' align=center><b><font text color=".$wclfg." size=2>Medicina Interna</b><br>
			       Dias Asignados:&nbsp&nbsp".$wnumdias."<br>
     			   -----------------------------------<br>
     			   Total Consultas:&nbsp&nbsp".$wtotdias."<br>
     			   Indicador=".number_format($wproesp,2,'.',',')."</font></td>";
			echo "<td bgcolor='".$wcf."' align=center><b><font text color=".$wclfg." size=2>Usuarios Consul.<br>Urgencias.</b><br><br>
     			   Total Usuarios:&nbsp&nbsp".$wnumcx."</font></td>";
			echo "<td bgcolor='".$wcf."' align=center><b><font text color=".$wclfg." size=2>Oportunidad en Cirugias</b><br>
     			   Dias Asignados:&nbsp&nbsp".$wdiascx."<br>
     			   -----------------------------------<br>
     			   Total Cx:&nbsp&nbsp".$wtotcx."<br>
     			   Indicador=".number_format($wprocx,2,'.',',')."</font></td>";
			echo "</tr>";

			echo "<tr><td><br></td></tr>";
			echo "<tr><td colspan=3 align=center><font size=2><A href='est_infgnl.php?wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></font></td></tr>";
			echo "</table>";

		}
	}


}//Quitar cuando vamos a montar en matrix

// FUNCIONES


//			$wtoturg=cal_toturg();
//			$wdateco=cal_proeco();

function cal_proesp($fecini,$fecfin,$wesp)
{
	global $wbasedato;
	global $conex;

	$q= " SELECT sum(datediff(fecha,fecha_data)) ".
		"   FROM ".$wbasedato."_000009 ".
		"  WHERE fecha_data BETWEEN '".$fecini."'".
		"    AND '".$fecfin."'".
		"    AND datediff(fecha,fecha_data) >= 0 ".
		"    AND cod_exa = '".$wesp."'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num != "" )//and $wesp!="006"
	{
		$row = mysql_fetch_array($res);
		$wnumdias=$row[0];

		$q1= " SELECT count(*) ".
			 "   FROM ".$wbasedato."_000009 ".
			 "  WHERE fecha BETWEEN'".$fecini."'".
			 "    AND '".$fecfin."'".
	  		 "    AND cod_exa= '".$wesp."'";

		$res1 = mysql_query($q1,$conex);
		$num1 = mysql_num_rows($res1);

		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($res1);
			$wtotdias=$row1[0];

			if($wtotdias > 0)
				$wproesp=$wnumdias/$wtotdias;
			else
				$wproesp=$wnumdias/1;
		}
	}
	if($wesp=="006")
	{
		$wnumdias=0;
		
		$waplica="clisur";
		
		$q1= " SELECT count(*) ".
			 "   FROM ".$waplica."_000106 ".
			 "  WHERE fecha_data BETWEEN '".$fecini."'".
			 "    AND '".$fecfin."'".
	  		 "    AND Tcarprocod in ('cs0269') ".
	  		 "    AND tcarconcod='5203'";
		
		$res1 = mysql_query($q1,$conex);
		$num1 = mysql_num_rows($res1);

		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($res1);
			$wtotdias=$wtotdias + $row1[0];

			$wproesp=0;
		}

				
	}//

	return array($wnumdias,$wtotdias,$wproesp);
}

function cal_usuurg($fecini,$fecfin)
{
	global $conex;
	$waplica="clisur";

	$q= " SELECT count(*) ".
		"   FROM ".$waplica."_000101 ".
		"  WHERE Ingfei BETWEEN '".$fecini."'".
		"    AND '".$fecfin."'".
		"    AND ingtin='U'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$wnumcx=$row[0];
	}

	return $wnumcx;
}// Fin Funcion cal_usuurg();


function cal_procx($fecini,$fecfin)
{
	global $conex;
	$waplica="citascs";

	$q= " SELECT sum(datediff(fecha,fecha_data)) ".
		"   FROM ".$waplica."_000001 ".
		"  WHERE fecha BETWEEN '".$fecini."'".
		"    AND '".$fecfin."'".
		"    AND datediff(fecha,fecha_data) >= 0 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$wdiascx=$row[0];

		$q1= " SELECT count(*) ".
			 "   FROM ".$waplica."_000001 ".
			 "  WHERE fecha BETWEEN '".$fecini."'".
			 "    AND '".$fecfin."'".
	  		 "    AND datediff(fecha,fecha_data) >= 0 ";

	  	$res1 = mysql_query($q1,$conex);
		$num1 = mysql_num_rows($res1);

		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($res1);
			$wtotcx=$row1[0];

			if($wtotcx > 0)
				$wprocx=$wdiascx/$wtotcx;
			else
				$wprocx=$wdiascx/1;
		}
	}

	return array($wdiascx,$wtotcx,$wprocx);
}

?>