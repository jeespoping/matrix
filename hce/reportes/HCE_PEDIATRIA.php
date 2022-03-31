<html>
<head>
<title>MATRIX</title>
<title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
   <!-- <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <!--<script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
   <!-- <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>-->
	<style type="text/css">
		#tipo1{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-width: 1px;}
		#tipo2{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-width: 1px;}
		#tipo3{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-width: 1px;}
		#tipo4{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-width: 1px;}
		#tipo5{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipo6{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipo7{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:right;}
		.tipoTABLE{font-family:Arial;table-layout:fixed;border-style:solid;border-collapse:collapse;border-width: 1px;}
	</style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<!-- <center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>REGISTRO DE ATENCIONES DE PEDIATRIA</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> HCE_PEDIATRIA.php Ver. 2015-03-10</b></font></tr></td></table><br><br><br>
</center> -->
<?php
include_once("conex.php");
include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	
	$user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        //include_once("conex.php");
        mysql_select_db("matrix");
		$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce"); 
		

        $conex = obtenerConexionBD("matrix");

	$key = substr($user,2,strlen($user));
	
	
	$wactualiz = '2021-11-09';

	echo "<form action='HCE_PEDIATRIA.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	encabezado( "REGISTRO DE ATENCIONES DE PEDIATRIA", $wactualiz, $institucion->baseDeDatos );
	
	if(!isset($v0) or !isset($v1))
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>REGISTRO DE ATENCIONES DE PEDIATRIA</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' id='v0' readonly='readonly' size=10 maxlength=10 value='".date("Y-m-d")."'>&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v0',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' id='v1' readonly='readonly' size=10 maxlength=10 value='".date("Y-m-d")."'>&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v1',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		//                             0                 1                       2                    3                  4                 5                 6                 7                   8                 9                  10                 11                12                 13                14                    15
		$query  = "select ".$wbasedatohce."_000036.Firusu,usuarios.descripcion,".$wbasedatohce."_000036.Fecha_data,".$wbasedatohce."_000036.Hora_data,".$wbasedatohce."_000036.Firpro,".$wbasedatohce."_000001.Encdes,".$wbasedatohce."_000036.Firhis,".$wbasedatohce."_000036.Firing,root_000036.Pactid,root_000036.Pacced,root_000036.Pacno1,root_000036.Pacno2,root_000036.Pacap1,root_000036.Pacap2,".$wbasedatohce."_000036.Firrol,".$wbasedatomovhos."_000044.Espnom ";
		$query .= "   from ".$wbasedatohce."_000036,".$wbasedatohce."_000001,root_000037,root_000036,usuarios,".$wbasedatomovhos."_000044 ";
		$query .= "    where ".$wbasedatohce."_000036.fecha_data between '".$v0."' and '".$v1."' "; 
		$query .= " 	 and ".$wbasedatohce."_000036.firpro = ".$wbasedatohce."_000001.encpro  ";
		$query .= " 	 and ".$wbasedatohce."_000036.firhis = root_000037.orihis "; 
		$query .= " 	 and root_000037.oriori = '".$wemp_pmla."'  ";
		$query .= " 	 and root_000037.oritid = root_000036.pactid "; 
		$query .= " 	 and root_000037.oriced = root_000036.pacced "; 
		$query .= " 	 and  ".$wbasedatohce."_000036.firusu = usuarios.codigo  ";
		$query .= " 	 and ".$wbasedatohce."_000036.firrol = ".$wbasedatomovhos."_000044.espcod "; 
		$query .= " 	 and  ".$wbasedatohce."_000036.firrol in ('100101','100232','100238','100251','100262','100263')  ";
		$query .= "   order by 1,3,4  ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<center><table id='tipoTABLE' CELLSPACING=0 CELLPADDING=2>";
		echo "<tr><td colspan=9 id='tipo4'><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=9 id='tipo4'><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=9 id='tipo4'><b>REGISTRO DE ATENCIONES DE PEDIATRIA</b></td></tr>";
		echo "<tr><td colspan=9 id='tipo4'><b>DESDE : ".$v0." HASTA : ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td id='tipo3'><b>Medico</b></td>";
		echo "<td id='tipo3'><b>Fecha</b></td>";
		echo "<td id='tipo3'><b>Hora</b></td>";
		echo "<td id='tipo3'><b>Formulario</b></td>";
		echo "<td id='tipo3'><b>Historia</b></td>";
		echo "<td id='tipo3'><b>Ingreso</b></td>";
		echo "<td id='tipo3'><b>Identificaci&oacute;n<br>Paciente</b></td>";
		echo "<td id='tipo3'><b>Nombre<br>Paciente</b></td>";
		echo "<td id='tipo3'><b>Especialidad</b></td>";
		echo "</tr>"; 
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$tipo = "tipo1";
			else
				$tipo = "tipo2";
			echo "<tr>";
			echo "<td id='".$tipo."'>".$row[1]."</td>";
			echo "<td id='".$tipo."'>".$row[2]."</td>";
			echo "<td id='".$tipo."'>".$row[3]."</td>";
			echo "<td id='".$tipo."'>".$row[5]."</td>";
			echo "<td id='".$tipo."'>".$row[6]."</td>";
			echo "<td id='".$tipo."'>".$row[7]."</td>";
			echo "<td id='".$tipo."'>".$row[8]." ".$row[9]."</td>";
			echo "<td id='".$tipo."'>".$row[10]." ".$row[11]." ".$row[12]." ".$row[13]."</td>";
			echo "<td id='".$tipo."'>".$row[15]."</td>";
			echo "</tr>"; 
		}
		echo "</table></center>"; 
	}
}
?>
</body>
</html>
