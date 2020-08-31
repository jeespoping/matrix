<html>
<head>
  <title>MATRIX ACTUALIZACION DE INDICADORES</title>
</head>
<body BGCOLOR="">

<SCRIPT>

function reloj() 
  {
   var fObj = new Date(); 
   var horas = fObj.getHours() ; 
   var minutos = fObj.getMinutes() ; 
   var segundos = fObj.getSeconds() ; 
   if (horas <= 9) horas = "0" + horas; if (minutos <= 9) 
      minutos = "0" + minutos; 
   if (segundos <= 9) 
      segundos = "0" + segundos; 
      
   window.status = horas+":"+minutos+":"+segundos;
   document.title = horas+":"+minutos+":"+segundos;
   }


  function Cerrar() 
    {
    var objFecha = new Date();

    if(objFecha.getHours() == '23' && objFecha.getMinutes() == '59' && parseInt(objFecha.getSeconds()) > 0 && parseInt(objFecha.getSeconds()) < 10){
    	setTimeout("ejecutar();",50000);
    	setTimeout("close();",51000);
    }  else {
    	setTimeout("close();",30);
    }
    }
    
    function ejecutar(){
    	document.location.href = 'cierre_camas.php';
    }
</SCRIPT>
<body BGCOLOR="#FFFFFF">

<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

echo "<form action='cierre_camas.php' method=post>";
echo "<center><table>";
echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/movhos/logo_movhos.png'></td></tr>";
echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>MATRIX ACTUALIZACION DE INDICADORES</font></b></font></td></tr>";
echo "</table>";

if(!isset($wfecha)){
	$wfecha = date("Y-m-d");
	$whora = (string)date("H:i:s");
}

$wusuario = 'movhos';

//Ciclo por rango de fechas.  Pide fecha inicial hasta la actual
$qFecha = "	SELECT DISTINCT
	   					fecha_data
					FROM 
						`movhos_000038`					 
					ORDER BY 
						fecha_data DESC 
	   	";
$rsFecha = mysql_query($qFecha,$conex);
$numFecha = mysql_num_rows($rsFecha);

$q = " SELECT habcco, COUNT(*) "
."   FROM movhos_000020 "
."  WHERE habest = 'on' "
."  GROUP BY 1 ";

$res = mysql_query($q,$conex);
$num = mysql_num_rows($res);

for($cont1=1;$cont1<=$num;$cont1++){
	$row = mysql_fetch_array($res);
	$servicios[] = $row[0];	
}
 

if($numFecha > 0){
	
	for($cont1=1;$cont1<=$numFecha;$cont1++){

		echo "<br>Actualizando fecha: " . $wfecha;
		$colFechas = mysql_fetch_array($rsFecha);
		$wfecha = $colFechas[0];
		$cont2 = 0;
			
		while($cont2 < sizeof($servicios)){
			
			$servicio = $servicios[$cont2];

			/**
			  * Ingresos por urgencias
			  */
			 $ingU = 0;
		     $q = "SELECT 
		     			COUNT(*)
					FROM 
						movhos_000032  
					WHERE 
						Fecha_data = '".$wfecha."' 
					    AND Procedencia='1130'
					    AND Servicio = '".$servicio."'";
		     
			 $rs = mysql_query($q,$conex);		 
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);
		 		
		 		$ingU = $fila[0];
			 }
			 
		    /**
			  * Ingresos por admisiones
			  */
			 $ingA = 0;
		     $q = "SELECT 
		     			COUNT(*)
					FROM 
						movhos_000032  
					WHERE 
						Fecha_data = '".$wfecha."' 
					    AND Procedencia='1800'
					    AND Servicio = '".$servicio."'";
		     
			 $rs = mysql_query($q,$conex);		 
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);
		 		
		 		$ingA = $fila[0];
			 }
			 
		    /**
			  * Ingresos por cirugía
			  */
			 $ingC = 0;
		     $q = "SELECT 
		     			COUNT(*)
					FROM 
						movhos_000032  
					WHERE 
						Fecha_data = '".$wfecha."' 
					    AND Procedencia='1016'
					    AND Servicio = '".$servicio."'";
		     
			 $rs = mysql_query($q,$conex);		 
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);
		 		
		 		$ingC = $fila[0];
			 }
			 
		    /**
			  * Ingresos por traslado
			  */
			 $ingT = 0;
		     $q = "SELECT 
		     			COUNT(*) 
					FROM 
						movhos_000032  
					WHERE 
						Fecha_data = '".$wfecha."' 
					 	AND Procedencia in (SELECT Ccocod
											FROM costosyp_000005 
					   						WHERE Ccouni = '2H' 
					   						ORDER by 1)
					    AND Servicio = '".$servicio."'";
		     
			 $rs = mysql_query($q,$conex);		 
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);
		 		
		 		$ingT = $fila[0];
			 }
			 
			 /*
			  * Egresos por traslado
			  */
		    $egrT = 0;
		    $diasTraslado = 0;
					       
		     $q = "SELECT 
		     			COUNT(*), IFNULL(SUM(Dias_estan_serv),0)  
					FROM 
						movhos_000033  
					WHERE 
						Fecha_egre_serv = '".$wfecha."' 
					 	AND Tipo_egre_serv in (SELECT Ccocod
											FROM costosyp_000005 
					   						WHERE Ccouni = '2H' 
					   						ORDER by 1)
					    AND Servicio = '".$servicio."'";
		     
			 $rs = mysql_query($q,$conex);		 
			 if(mysql_num_rows($rs) > 0)
			 {
		 		$fila = mysql_fetch_row($rs);
		 		
		 		$egrT = $fila[0];
		 		$diasTraslado= $fila[1];
			 }
			 
			 /*
			  * Egresos y dias de estancia altas
			  */
			 $diasAlta = 0;
			 
			 $q2 = " SELECT IFNULL(SUM(Dias_estan_serv),0), COUNT(*) "
			 ."   FROM movhos_000033 "
			 ."  WHERE Fecha_data = '".$wfecha."'"
			 ."    AND servicio = '".trim($servicio)."'"
			 ."    AND Tipo_egre_serv = 'ALTA' ";
			  
			 $rs = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());

			 if (mysql_num_rows($rs) > 0){
			 	$consulta = mysql_fetch_array($rs);
			 	$diasAlta=$consulta[0];
			 	$wegal = $consulta[1];
			 }

			 /*
			  * Cantidad de muertes menores a 48 horas 
			  */
			 $wmen48 = 0;
			 $diasMen48 = 0;
			 $q2 = " SELECT COUNT(*), IFNULL(SUM(Dias_estan_serv),0) "
			 ."   FROM movhos_000018, movhos_000033 "
			 ."  WHERE movhos_000033.Fecha_data = '".$wfecha."'"
			 ."    AND ubisac = '".trim($servicio)."'"
			 ."    AND ubimue = 'on' "
			 ."    AND ubihis = historia_clinica "
			 ."    AND ubiing = num_ingreso "
			 ."    AND Tipo_egre_serv = 'MUERTE MENOR A 48 HORAS' ";
			  
			 $rs = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			 $num2 = mysql_num_rows($rs);

			 if ($num2 > 0){
			 	$consulta = mysql_fetch_array($rs);
			 	$wmen48=$consulta[0];
			 	$diasMen48=$consulta[1];
			 }
			 
			 /*
			  * Cantidad de muertes mayores a 48 horas
			  */
			 $wmay48 = 0;
			 $diasMay48 = 0;
			 
			 $q2 = " SELECT COUNT(*), IFNULL(SUM(Dias_estan_serv),0) "
			 ."   FROM movhos_000018, movhos_000033 "
			 ."  WHERE movhos_000033.Fecha_data = '".$wfecha."'"
			 ."    AND ubisac = '".trim($servicio)."'"
			 ."    AND ubimue = 'on' "
			 ."    AND ubihis = historia_clinica "
			 ."    AND ubiing = num_ingreso "
			 ."    AND Tipo_egre_serv = 'MUERTE MAYOR A 48 HORAS' ";
			  
			 $rs = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			 $num2 = mysql_num_rows($rs);

			 if ($num2 > 0){
			 	$consulta = mysql_fetch_array($rs);
			 	$wmay48=$consulta[0];
			 	$diasMay48=$consulta[1];
			 }
			 
			/* --------------------------
			 *  Update de los campos
			 * --------------------------
			 */
			echo "<br>Servicio: " . $servicio." >48: ".$wmay48." <48: ".$wmen48;

			$q = " UPDATE
						movhos_000038 
					SET
						Cieinu = '".$ingU."',	
						Cieinc = '".$ingC."',
						Cieina = '".$ingA."',
						Cieint = '".$ingT."',
						Ciegrt = '".$egrT."',
						Ciedit = '".$diasTraslado."',
						Ciediam = '".($diasAlta+$diasMay48+$diasMen48)."',
						Cieeal = '".$wegal."'
					WHERE 
						Fecha_data = '".$wfecha."'
						AND Cieser = '".trim($servicio)."'";
			$res1 = mysql_query($q,$conex) or die("ERROR GRABANDO CIERRE DIARIO DE CAMAS : ".mysql_errno().":".mysql_error());

			$cont2++;
		}
	}
}
echo "<br><br><br>";
echo "<center><table>";
echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>TERMINO DE ACTUALIZAR INDICADORES POR FECHA</font></b></font></td></tr>";
echo "</table>";

liberarConexionBD($conex);
?>
</body>
</html>