<?php 

include_once("movhos/otros.php");
include_once("conex.php");
include_once("root/comun.php");
include_once("./../../interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php");
    global $conex_o;
	global $conex;
	global $bd;
	
	function datos($date, $conex){				
		//1. En la primer consulta se obtienen los pedidos de medicamentos que son todos los registros en cargos (movhos 2 y 3)
		//   y se la valida en el maestro de articlos de matrix el articulo esté como medicamento ( Artesm = 'on').
		//2. En la seguna consulta se obtienen los pedidos de insumos registrados en las tablas movhos 230 y 231.
		//3. En la tercera cosulta se obtienen las devoluciones de medicamentoos que se encuantran en las tablas de cargos (movhos 2 y 3)
		// con fuente 12 (Fenfue = '12') que indica que son .
		//4. en la cuarta consulta  se obtienen las devoluciones de insumos que se graban en la tabla movhos 227 (Carcde > 0)
		//NOTA: En las primeras cuatro consultas encontraremos todos los pedido y devoluciones de centros de costo diferentes de CIRUGIA
		//5. En la quinta consulta se obtienen los pedidos de medicamentos e insumos de Cirugía los cuales están en la tabla cliame_000207
		$sql = 	"SELECT fennum AS drodoc, fenfue AS drodetfue, a.fecha_data AS drofec, fdeser AS droccc, fencco AS drocco, 
					CASE WHEN fdeser = '' THEN '' ELSE (SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fdeser) LIMIT 1) END AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fencco) LIMIT 1) AS cconom,
					fenhis AS drohis, fening AS dronum, YEAR(a.fecha_data) AS droano, fdeart AS drodetart, substring_index(b.seguridad,'-',-1) AS logusu, 
					unidad_medida_tercero AS drodetuni, SUM(fdecan) AS drodetcan, nombre AS artnom, codigo_tercero, substring_index(b.hora_data,':',2) AS hora
					FROM movhos_000002 AS a, movhos_000003 AS b, articulos_terceros, movhos_000026
					WHERE Fdenum = Fennum AND Fdeart = codigo_unix AND Fdeart = Artcod AND Artesm = 'on' AND Fenfue = '11'
						AND a.fecha_data = '".$date."' AND fencco NOT IN ('2001', '2005', '2503')
					GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
				UNION 
				SELECT REPLACE(Pedcod, '-', '') AS drodoc, '11' AS drodetfue, a.Fecha_data AS drofec, Pedcco AS droccc, Pedcco AS drocco, 
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', Pedcco)) AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', Pedcco)) AS cconom,
					Dpehis AS drohis, Dpeing AS dronum, YEAR(a.fecha_data) AS droano, dpeins AS drodetart, Pedaux AS logusu,
					unidad_medida_tercero AS drodetuni, SUM(dpedis) AS drodetcan, Nombre AS artnom, codigo_tercero, substring_index(a.hora_data,':',2) AS hora
				FROM movhos_000230 AS a, movhos_000231, articulos_terceros
				WHERE Pedcod = Dpecod AND Dpeins = codigo_unix AND a.fecha_data = '".$date."' AND dpedis > 0 AND Pedcco NOT IN ('2001', '2005')
				GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
				UNION ALL	
                    SELECT fennum AS drodoc, fenfue AS drodetfue, a.fecha_data AS drofec, (SELECT ubisac  FROM movhos_000018 WHERE fenhis =ubihis AND fening=ubiing LIMIT 1) AS droccc, fencco AS drocco, 
					CASE WHEN fdeser = '' THEN (	SELECT nomcco from movhos_000296 WHERE unxcco =(SELECT ubisac  FROM movhos_000018 WHERE fenhis =ubihis AND fening=ubiing LIMIT 1) )  ELSE (SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fdeser) LIMIT 1) END AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fencco) LIMIT 1) AS cconom,
					fenhis AS drohis, fening AS dronum, YEAR(a.fecha_data) AS droano, fdeart AS drodetart, substring_index(b.seguridad,'-',-1) AS logusu, 
					unidad_medida_tercero AS drodetuni, SUM(fdecan) AS drodetcan, nombre AS artnom, codigo_tercero, substring_index(b.hora_data,':',2) AS hora
				FROM movhos_000002 AS a, movhos_000003 AS b, articulos_terceros
				WHERE Fdenum = Fennum AND Fdeart = codigo_unix AND Fenfue = '12'
					AND a.fecha_data = '".$date."' AND fencco NOT IN ('2001', '2005', '2503') 
				GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
				UNION ALL
				SELECT CONCAT(Carhis,REPLACE(substring_index(fecha_data,'-',-2), '-', '')) AS drodoc, '12' AS drodetfue, fecha_data AS drofec,CASE WHEN carbot = '3504' THEN (	SELECT unxcco from movhos_000296 WHERE unxcco =(SELECT ubisac  FROM movhos_000018 a WHERE ubihis =carhis AND ubiing=caring LIMIT 1) )  ELSE (carbot) END AS droccc, Carbot AS drocco, 
                   CASE WHEN carbot = '3504' THEN (	SELECT nomcco from movhos_000296 WHERE unxcco =(SELECT ubisac  FROM movhos_000018 a WHERE ubihis =carhis AND ubiing=caring LIMIT 1) )  ELSE (carbot) END AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', Carbot) LIMIT 1) AS cconom,
					Carhis AS drohis, Caring AS dronum, YEAR(fecha_data) AS droano, Carins AS drodetart, Caraux AS logusu, 
					unidad_medida_tercero AS drodetuni, Carcde AS drodetcan, nombre AS artnom, codigo_tercero, substring_index(hora_data,':',2) AS hora
				FROM movhos_000227, articulos_terceros
				WHERE Carins = codigo_unix AND Carcde > 0 AND fecha_data =  '".$date."' AND Carbot NOT IN ('2001', '2005')
				UNION ALL
				SELECT Mpatur AS drodoc, '11' AS drodetfue, fecha_data AS drofec, '2001' AS droccc, '2001' AS drocco, 
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', '2001') LIMIT 1) AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', '2001') LIMIT 1) AS cconom,
					Mpahis AS drohis, Mpaing AS dronum, YEAR(fecha_data) AS droano, Mpacom AS drodetart, substring_index(seguridad,'-',-1) AS logusu, 
					unidad_medida_tercero AS drodetuni, SUM(Mpacan) AS drodetcan, nombre AS artnom, codigo_tercero, substring_index(Mpahcm,':',2) AS hora
				FROM cliame_000207, articulos_terceros
				WHERE Mpacom = codigo_unix AND fecha_data = '".$date."' AND Mpacco IN ('2001', '2005')
				GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
				ORDER BY 3,1";
				
		$res = mysql_query($sql, $conex);
		
		while($row = mysql_fetch_array($res)){
			$datos[] = $row;
		}
		
        return $datos;
	}
	
	function cod_cco_cruz_verde($cconom){
		//Homologación de los centros de costo de cruz verde
		$ret = "42196425";
		if(stristr(strtoupper(trim($cconom)), "CIRUGIA") !== false){
			$ret = "42196419";
		}else if(stristr(strtoupper(trim($cconom)), "URGENCIA") !== false){
			$ret = "42196432";
		}else if(stristr(strtoupper(trim($cconom)), "CENTRAL") !== false){
			$ret = "42196425";
		}
		return $ret;
	}
	
	function secuencia($cconom){
		//Homologación del campo IDSecuecia requerido en el reporte
		$ret = "1";
		if(stristr(strtoupper(trim($cconom)), "CENTRAL") !== false){
			$ret = "1";
		}else if(stristr(strtoupper(trim($cconom)), "URGENCIA") !== false){
			$ret = "2";
		}else if(stristr(strtoupper(trim($cconom)), "CIRUGIA") !== false){
			$ret = "3";
		}
		return $ret;
	}
	
	function consultar_ebs($art, $conex){
		//Homologación de los código de articulos y unidades de cruz verde
		$ret = "";
		$sql = "SELECT codigo_tercero, unidad_medida_tercero
            FROM articulos_terceros
            WHERE codigo_unix = '".trim($art)."'";
    
		$res = mysql_query($sql, $conex);
		
		if($row = mysql_fetch_row($res)){
			$ret = $row;
		}
		
		return $ret;
	}
	
	function consultar_bodega($cco){
		//Homologación del centro de costos que se envía a SAP  y nombre de la bodega
		$bodega = "13113504";
		$nombre_bodega = "BOD FARMACIA CENTRAL";
		$hospitalizacion = array("13111501","13111502","13111503","13111504","13111505","13111506","13111511","13111512","13111513","13111514","13111515","13111599");
		$urgencias = array("13110501","13110502","13110503","13110504","13110599");
		$cirugia = array("13112001","13112002","13112003","13112004","13112005","13112006","13112008","13112099");
		if(in_array($cco, $hospitalizacion)){
			$bodega = "13113504";
			$nombre_bodega = "BOD FARMACIA CENTRAL";
		}else if(in_array($cco, $urgencias)){
			$bodega = "13113502";
			$nombre_bodega = "BOD FARMACIA URGENCIA";
		}else if(in_array($cco, $cirugia)){
			$bodega = "13113503";
			$nombre_bodega = "BOD FARMACIA SALAS DE CIRUGIA";
		}
		return array($bodega, $nombre_bodega);
	}
	
	function get_cod_unidad($cco, $cco2){
		//Homologación código de unidad de SAP
		$ret = [];
		
		$bodega = consultar_bodega($cco);
		
		$sql = "SELECT cod_unidad 
				FROM gen_unidad_portoazul 
				WHERE cneg_unidad = '".trim($bodega[0])."'
				UNION all
				SELECT cod_unidad 
				FROM gen_unidad_portoazul 
				WHERE cneg_unidad = '".trim($cco2)."'";
    
		$res = mysql_query($sql, $conex);
		
		while($row = mysql_fetch_array($res))
		{
            $ret[] = $row;
        }
		
		//var_dump($ret);
		//exit;
		
		return $ret;
	}
	
	function get_cco_portoazul($cconom){
		//Homologación de centros de costos de SAP
		$ret = "";
		if($cconom != ""){
			$sql = "SELECT Codcco 
				FROM movhos_000296 
				WHERE UPPER(Nomcco) LIKE '%".strtoupper(trim($cconom))."%' LIMIT 1";
    
			$res = mysql_query($sql, $conex);
			
			if($row = mysql_fetch_row($res))
			{
				$ret = $row[0];
			}
		}
		
		return $ret;
	}
?>
<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

</head>

<body>
    <div class="row" style='padding: 10px;'>
          <div class="col-md-12">
          <h1>Datos de la tabla FARMACIA</h1>
            <table border="1">
                <tr>
					<th>FECHA_ATENCION_SOLICITUD</th>	
					<th>NOMBRE_DEL_ARCHIVO</th>	
					<th>FOLIO_ABASTECIMIENTO</th>
					<th>ID_SECUENCIA</th>
					<th>UNIDAD_BODEGA</th>
					<th>CODIGO_PRODUCTO</th>
					<th>EBS</th>
					<th>UNIDAD_MEDIDA</th>
					<th>NOMBRE_PRODUCTO</th>
					<th>DESC_BODEGA</th>
					<th>ENTIDAD</th>
					<th>CODIGO_UNIDAD</th>
					<th>CANTIDAD_PRODUCTO</th>
					<th>CONCEPTO_MOV</th>
					<th>CENTRO_COSTO_SOLI</th>
					<th>CENTRO_COSTO_SOLI_NOM</th>
					<th>HISTORIA_PACIENTE</th>
					<th>USUARIO</th>
                </tr>
                <?php
					//Se captura el día anterior a la fecha actual, que es siempre el que se procesa
					$cdate = date('Y-m-d');
					$date = date('Y-m-d', strtotime($cdate. ' - 1 days'));
					if(isset($_GET["date"])){
						$date=$_GET["date"];
					}
					//$nombreReporte = 'reporte_farmacia_'.$date.'.xlsx';
					$nombreReporteCsv = 'reporte_farmacia_'.$date.'.csv';
                    $datos = datos($date, $conex);
					var_dump(count($datos));
                    $text= '';
					//$fp = fopen($nombreReporte, 'w');
					$fpCsv = fopen($nombreReporteCsv, 'w');
					//Creamos la cabecera del reporte
					$cabecera = array(
						'FECHA_ATENCION_SOLICITUD',
						'NOMBRE_DEL_ARCHIVO',
						'FOLIO_ABASTECIMIENTO',
						'ID_SECUENCIA',
						'UNIDAD_BODEGA',
						'CODIGO_PRODUCTO',
						'EBS',
						'UNIDAD_MEDIDA',
						'NOMBRE_PRODUCTO',
						'DESC_BODEGA',
						'ENTIDAD',
						'CODIGO_UNIDAD',
						'CANTIDAD_PRODUCTO',
						'CONCEPTO_MOV',
						'CENTRO_COSTO_SOLI',
						'CENTRO_COSTO_SOLI_NOM',
						'HISTORIA_PACIENTE',
						'USUARIO'
					);
					require 'enviarNotificacion.php';
					//fputcsv($fp, $cabecera, "\t");
					fputcsv($fpCsv, $cabecera, "\t");
					foreach ($datos as $value) {
						//Se recorren los datos y se llaman la funciones de homologación para luego escribir las lineas en el archivo del reporte
						//Fuente 11 pedidos y fuente 12 Devoluciones
						$concepto_mov = $value['drodetfue'] == '11' ? 'C' : 'D';
						$cantidad = $concepto_mov == 'C' ? intval($value['drodetcan']) : intval($value['drodetcan']) * -1;
						$nombrePlano = cod_cco_cruz_verde($value['cccnom']).date("dmY", strtotime($value['drofec'])).'_'.str_replace("C", "V", $concepto_mov).str_pad($value['drodoc'], 10, "0", STR_PAD_LEFT);
						$paciente = informacionPaciente($conex, "01", $value['drohis'], $value['dronum']);
						$ebs = consultar_ebs($value['drodetart'], $conex);
						$id_secuencia = secuencia($value['cccnom']);
						$cco_portoazul = get_cco_portoazul($value['cconom']);
						$ccc_portoazul = get_cco_portoazul($value['cccnom']);
						$unidad = get_cod_unidad($cco_portoazul,$ccc_portoazul);
						$bodega = consultar_bodega($cco_portoazul);
						//Si no viene el nombre del responsable es porque el paciente ingresó como particular
						$responsable = $paciente['nombreResponsable'];
						if(!$paciente['nombreResponsable']){
							$responsable = "PARTICULAR";
						}
						if($ebs[0]){
							if($ebs[0] != '' && $ebs[0] != '0' && $ebs[0] != 'JP'){
								$text = ' <tr style="font-size: 15px;">';
								$text2 = "";
								$text2 .= '<td>'.$value['drofec'].' '.$value['hora'].'</td>';
								$text2 .= '<td>'.$nombrePlano.'</td>';
								$text2 .= '<td>'.$value['drodoc'].'</td>';
								$text2 .= '<td>'.$id_secuencia.'</td>';
								$text2 .= '<td>'.$unidad[0]['cod_unidad'].'</td>';
								$text2 .= '<td>'.$value['drodetart'].'</td>';
								$text2 .= '<td>'.$ebs[0].'</td>';
								$text2 .= '<td>'.$ebs[1].'</td>';
								$text2 .= '<td>'.$value['artnom'].'</td>';
								$text2 .= '<td>'.$bodega[1].'</td>';
								$text2 .= '<td>'.$responsable.'</td>';
								$text2 .= '<td>'.$unidad[1]['cod_unidad'].'</td>';
								$text2 .= '<td>'.$cantidad.'</td>';
								$text2 .= '<td>'.$concepto_mov.'</td>';
								$text2 .= '<td>'.$ccc_portoazul.'</td>';
								$text2 .= '<td>'.$value['cccnom'].'</td>';
								$text2 .= '<td>'.$value['drohis'].'-'.$value['dronum'].'</td>';
								$text2 .= '<td>'.$value['logusu'].'</td>';
								$text .= $text2;
								$text .= ' </tr>';
								echo $text;
								//Escribimos la linea en el reporte
								$arr = array(
									$value['drofec'].' '.$value['hora'],
									$nombrePlano,
									$value['drodoc'],
									$id_secuencia,
									$unidad[0]['cod_unidad'],
									$value['drodetart'],
									$ebs[0],
									$ebs[1],
									$value['artnom'],
									$bodega[1],
									$responsable,
									$unidad[1]['cod_unidad'],
									$cantidad,
									$concepto_mov,
									$ccc_portoazul,
									$value['cccnom'],
									$value['drohis'].'-'.$value['dronum'],
									$value['logusu']
								);
								//fputcsv($fp, $arr, "\t");
								fputcsv($fpCsv, $arr, "\t");
							}
						}
					}
					//fclose($fp); 
					fclose($fpCsv);
				    $mail=new enviarNotificacion();
					$mail->enviarnotificacion($nombreReporteCsv,'jperez@intap.com.co');
					$mail1=new enviarNotificacion();
					$mail1->enviarnotificacion($nombreReporteCsv,'brayan.camargo@clinicaportoazul.com');
					$mail2=new enviarNotificacion();
					$mail2->enviarnotificacion($nombreReporteCsv,'mayra.muttis@clinicaportoazul.com');
					$mail3=new enviarNotificacion();
					$mail3->enviarnotificacion($nombreReporteCsv,'marjorie.vieira@cruzverde.com.co');
					$mail4=new enviarNotificacion();
					$mail4->enviarnotificacion($nombreReporteCsv,'alexander.herrera@cruzverde.com.co');
					$mail5=new enviarNotificacion();
					$mail5->enviarnotificacion($nombreReporteCsv,'regente.cx@clinicaportoazul.com');
					$mail6=new enviarNotificacion();
					$mail6->enviarnotificacion($nombreReporteCsv,'auxiliar.nopbs@clinicaportoazul.com');
					$mail7=new enviarNotificacion();
					$mail7->enviarnotificacion($nombreReporteCsv,'tramites.nopbs@clinicaportoazul.com');
					$mail8=new enviarNotificacion();
					$mail8->enviarnotificacion($nombreReporteCsv,'yorcely.avila@clinicaportoazul.com');
					$dateDelete = date('Y-m-d', strtotime($cdate. ' - 2 days'));
					//$nombreReporteDelete = 'reporte_farmacia_'.$dateDelete.'.xlsx';
					$nombreReporteDeleteCsv = 'reporte_farmacia_'.$dateDelete.'.csv';
					//unlink($nombreReporteDelete);
					unlink($nombreReporteDeleteCsv);
                ?>
            </table>
        </div>
	</div>
	<footer>
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script
	</footer>
	<script>
	$( document ).ready(function() {
    var heights = $(".well").map(function() {
        return $(this).height();
    }).get(),

    maxHeight = Math.max.apply(null, heights);

    $(".well").height(maxHeight);
});
	</script>

</body>

</html>