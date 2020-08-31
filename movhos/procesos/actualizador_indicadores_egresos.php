<html>
<head>
  <title>MATRIX CIERRE DIARIO DE CAMAS</title>
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





echo "<form action='cierre_camas.php' method=post>";
echo "<center><table>";
echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/movhos/logo_movhos.png'></td></tr>";
echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>ACTUALIZACION CANTIDAD DE MUERTES MAYORES Y MENORES POR FECHA</font></b></font></td></tr>";
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

			/*
			 * Cantidad de muertes menores a 48 horas
			 */
			$wmen48 = 0;
			$q2 = " SELECT COUNT(*) "
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
			}

			/*
			 * Cantidad de muertes menores a 48 horas
			 */
			$wmay48 = 0;
			$q2 = " SELECT COUNT(*) "
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
			}

			echo "<br>Servicio: " . $servicio." >48: ".$wmay48." <48: ".$wmen48;

			$q = " UPDATE
						movhos_000038 
					SET
						Ciemmen = '".$wmen48."',	
						Ciemmay = '".$wmay48."'
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
echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>TERMINO DE ACTUALIZAR CANTIDAD DE MUERTES MAYORES Y MENORES POR FECHA</font></b></font></td></tr>";
echo "</table>";
?>
</body>
</html>