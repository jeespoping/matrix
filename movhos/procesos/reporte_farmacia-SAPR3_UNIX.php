<?php 

include_once("movhos/otros.php");
include_once("conex.php");
include_once("root/comun.php");
include_once("./../../interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php");
    global $conex_o;
	global $conex;
	global $bd;
	
    function datosUNIX($date, $conex){
		ini_set('memory_limit', '-1');
		conexionodbc($conexion,'movhos', $conex_o, 'inventarios');
		$q= "select 
				drodoc,
				drofue,
				drofec,
				drocco,
				droccc,
				(select cconom from cocco where ccocod = drocco) as cconom,
				(select cconom from cocco where ccocod = droccc) as cccnom,
				drohis,
				dronum,
				droano,
				drodetart,
				drodetuni,
				drodetfue,
				sum(drodetcan) as drodetcan,
				artnom,
				artgen,
				artcge,
				'00:00' as hora,
				'faradm' as logusu
			from ivdro, ivdrodet, ivart
			where 	drodoc = drodetdoc and
					drofue = drodetfue and
					drodetart = artcod and
					drofue in ('11','12') and 
					droanu = '0' and 
					drofec = '".$date."'
			group by 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
			order by drofec, drodoc desc";
        $i = 0;
        $res= odbc_do($conex_o, $q);
        
            while($arr =odbc_fetch_array($res))
            {
               $datos[]= $arr;
            }

        liberarconexionodbc($conex_o);
        odbc_close_all();
		
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
		$sql = "SELECT Codcco 
				FROM movhos_000296 
				WHERE UPPER(Nomcco) LIKE '%".strtoupper(trim($cconom))."%' LIMIT 1";
    
		$res = mysql_query($sql, $conex);
		
		if($row = mysql_fetch_row($res))
		{
            $ret = $row[0];
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
					$nombreReporte = 'reporte_farmacia_'.$date.'_UNIX.csv';
                    $datos = datosUNIX($date, $conex);
					var_dump(count($datos));
                    $text= '';
					$fp = fopen($nombreReporte, 'w');
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
					fputcsv($fp, $cabecera, "\t");
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
									$cco_portoazul,
									$value['cccnom'],
									$value['drohis'].'-'.$value['dronum'],
									$value['logusu']
								);
								fputcsv($fp, $arr, "\t");
							}
						}
					}
					fclose($fp); 
					$mail1=new enviarNotificacion();
					$mail1->enviarnotificacion($nombreReporte,'brayan.camargo@clinicaportoazul.com');
					$mail2=new enviarNotificacion();
					$mail2->enviarnotificacion($nombreReporte,'yorcely.avila@clinicaportoazul.com');
					$mail3=new enviarNotificacion();
					$mail3->enviarnotificacion($nombreReporte,'jperez@intap.com.co');
					$dateDelete = date('Y-m-d', strtotime($cdate. ' - 2 days'));
					$nombreReporteDelete = 'reporte_farmacia_'.$dateDelete.'_UNIX.csv';
					unlink($nombreReporteDelete);
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