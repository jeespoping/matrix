<?php 

include_once("movhos/otros.php");
include_once("conex.php");
include_once("root/comun.php");
include_once("./../../interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php");
    global $conex_o;
	global $conex;
	global $bd;
	
    function datosUNIX($conex){
		ini_set('memory_limit', '-1');
		conexionOdbc($conexion,'movhos', $conex_o, 'inventarios');
		$cdate = date('Y-m-d');
		$date = date('Y/m/d', strtotime($cdate. ' - 3 days'));
		$q= "SELECT 
				drodoc,
				drofue,
				drofec,
				drocco,
				droccc,
				(SELECT cconom FROM cocco WHERE ccocod = drocco) AS cconom,
				(SELECT cconom FROM cocco WHERE ccocod = droccc) AS cccnom,
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
			FROM ivdro, ivdrodet, ivart
			WHERE 	drodoc = drodetdoc AND
					drofue = drodetfue AND
					drodetart = artcod AND
					drofue IN ('11','12') AND 
					droanu = '0'
					AND drodoc IN 
			GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
			ORDER BY drofec, drodoc DESC";
			//(SELECT FIRST 1 logfec FROM ivlog WHERE logva1 = drofue AND logva2 = drodoc||'') AS logfec
   
        $i = 0;
        $res= odbc_do($conex_o, $q);
        
			if($i < 50){
				while($arr =odbc_fetch_array($res))
				{
					$datos[]= $arr;
					$i++;
				}	
			}

        liberarConexionOdbc($conex_o);
        odbc_close_all();
		
        return $datos;
    }
	
	function datos($conex){
		//Se captura el día anterior a la fecha actual, que es siempre el que se procesa
		$cdate = date('Y-m-d');
		$date = date('Y/m/d', strtotime($cdate. ' - 1 days'));
		//1. En la primer consulta se obtienen los pedidos de medicamentos que son todos los registros en cargos (movhos 2 y 3)
		//   y se la valida en el maestro de articlos de matrix el articulo esté como medicamento ( Artesm = 'on').
		//2. En la seguna consulta se obtienen los pedidos de medicamentos registrados en las tablas movhos 230 y 231.
		//3. En la tercera cosulta se obtienen las devoluciones de medicamentoos que se encuantran en las tablas de cargos (movhos 2 y 3)
		// con fuente 12 (Fenfue = '12') que indica que son .
		//4. en la cuarta consulta  se obtienen las devoluciones de insumos que se graban en la tabla movhos 227 (Carcde > 0)
		//NOTA: En las primeras cuatro consultas encontraremos todos los pedido y devoluciones de centros de costo diferentes de CIRUGIA
		//5. En la quinta consulta se obtienen los pedidos de medicamentos e insumos de Cirugía los cuales están en la tabla cliame_000207
		//('2001', '2005','1501','1502','1503','1504','1505','1506','1511','1512','1513','1514','1515','1599','3504')
		$sql = 	"SELECT fennum AS drodoc, fenfue AS drodetfue, a.fecha_data AS drofec, fencco AS droccc, fencco AS drocco, 
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fencco) LIMIT 1) AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fencco) LIMIT 1) AS cconom,
					fenhis AS drohis, fening AS dronum, YEAR(a.fecha_data) AS droano, fdeart AS drodetart, substring_index(b.seguridad,'-',-1) AS logusu, 
					unidad_medida_tercero AS drodetuni, SUM(fdecan) AS drodetcan, nombre AS artnom, codigo_tercero, REPLACE(substring_index(b.hora_data,':',2), ':', '') AS hora
					FROM movhos_000002 AS a, movhos_000003 AS b, articulos_terceros, movhos_000026
					WHERE Fdenum = Fennum AND Fdeart = codigo_unix AND Fdeart = Artcod AND Artesm = 'on' AND Fenfue = '11'
						AND a.fecha_data = '".$date."' AND fencco NOT IN ('2001', '2005')
					GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
				UNION ALL
				SELECT REPLACE(Pedcod, '-', '') AS drodoc, '11' AS drodetfue, a.Fecha_data AS drofec, Pedcco AS droccc, Pedcco AS drocco, 
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', Pedcco)) AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', Pedcco)) AS cconom,
					Dpehis AS drohis, Dpeing AS dronum, YEAR(a.fecha_data) AS droano, dpeins AS drodetart, Pedaux AS logusu,
					unidad_medida_tercero AS drodetuni, SUM(Dpedis) AS drodetcan, Nombre AS artnom, codigo_tercero, REPLACE(substring_index(a.hora_data,':',2), ':', '') AS hora
				FROM movhos_000230 AS a, movhos_000231, articulos_terceros
				WHERE Pedcod = Dpecod AND Dpeins = codigo_unix AND dpedis > 0 AND a.fecha_data = '".$date."' AND Pedcco NOT IN ('2001', '2005')
				GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
				UNION ALL
				SELECT fennum AS drodoc, fenfue AS drodetfue, a.fecha_data AS drofec, fencco AS droccc, fencco AS drocco, 
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fencco) LIMIT 1) AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', fencco) LIMIT 1) AS cconom,
					fenhis AS drohis, fening AS dronum, YEAR(a.fecha_data) AS droano, fdeart AS drodetart, substring_index(b.seguridad,'-',-1) AS logusu, 
					unidad_medida_tercero AS drodetuni, SUM(fdecan) AS drodetcan, nombre AS artnom, codigo_tercero, REPLACE(substring_index(b.hora_data,':',2), ':', '') AS hora
				FROM movhos_000002 AS a, movhos_000003 AS b, articulos_terceros
				WHERE Fdenum = Fennum AND Fdeart = codigo_unix AND Fenfue = '12'
					AND a.fecha_data = '".$date."' AND fencco NOT IN ('2001', '2005')
				GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17
				UNION ALL
				SELECT CONCAT(Carhis,REPLACE(substring_index(fecha_data,'-',-2), '-', '')) AS drodoc, '12' AS drodetfue, fecha_data AS drofec, Carbot AS droccc, Carbot AS drocco, 
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', Carbot) LIMIT 1) AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', Carbot) LIMIT 1) AS cconom,
					Carhis AS drohis, Caring AS dronum, YEAR(fecha_data) AS droano, Carins AS drodetart, Caraux AS logusu, 
					unidad_medida_tercero AS drodetuni, Carcde AS drodetcan, nombre AS artnom, codigo_tercero, REPLACE(substring_index(hora_data,':',2), ':', '') AS hora
				FROM movhos_000227, articulos_terceros
				WHERE Carins = codigo_unix AND Carcde > 0 AND fecha_data = '".$date."' AND Carbot NOT IN ('2001', '2005')
				UNION ALL
				SELECT Mpatur AS drodoc, '11' AS drodetfue, fecha_data AS drofec, '2001' AS droccc, '2001' AS drocco, 
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', '2001') LIMIT 1) AS cccnom,
					(SELECT Nomcco FROM movhos_000296 WHERE Codcco LIKE CONCAT('%', '2001') LIMIT 1) AS cconom,
					Mpahis AS drohis, Mpaing AS dronum, YEAR(fecha_data) AS droano, Mpacom AS drodetart, substring_index(seguridad,'-',-1) AS logusu, 
					unidad_medida_tercero AS drodetuni, SUM(Mpacan) AS drodetcan, nombre AS artnom, codigo_tercero, REPLACE(substring_index(Mpahcm,':',2), ':', '') AS hora
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
	
	function tip_adm($cconom){
		//Homologación del campo TIPADM requerido en los archivos planos
		$ret = "H";
		if(stristr(strtoupper(trim($cconom)), "CIRUGIA") !== false){
			$ret = "A";
		}else if(stristr(strtoupper(trim($cconom)), "URGENCIA") !== false){
			$ret = "U";
		}else if(stristr(strtoupper(trim($cconom)), "CENTRAL") !== false){
			$ret = "H";
		}
		return $ret;
	}
	
	function cod_org($cconom){
		//homologación del campo CODORG requerido en los archivos planos
		$ret = "237";
		if(stristr(strtoupper(trim($cconom)), "CIRUGIA") !== false){
			$ret = "240";
		}else if(stristr(strtoupper(trim($cconom)), "URGENCIA") !== false){
			$ret = "239";
		}else if(stristr(strtoupper(trim($cconom)), "CENTRAL") !== false){
			$ret = "237";
		}
		return $ret;
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
	
	//Función para enviar los archivos planos al servidor FTP requerido
	function send_file_to_ftp_server($fileName){
		$remote_file = 'Farmasanitas/'.$fileName;
		$ftp_server = '10.30.131.22';
		$ftp_user_name = 'bcamargo';
		$ftp_user_pass = 'Porto@zul2021.';

		// set up basic connection
		$conn_id = ftp_connect($ftp_server);

		// login with username and password
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

		// upload a file
		ftp_put($conn_id, $remote_file, 'planos_cruzverde/'.$fileName, FTP_ASCII);

		// close the connection
		ftp_close($conn_id);
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
				   <th>NOMBRE_DEL_ARCHIVO</th>
				   <th>TIP_DOC</th>
				   <th>NUM_DOC</th>
				   <th>TIP_ADM</th>
				   <th>ANO_ADM</th>
				   <th>NUM_ADM</th>
				   <th>CON_COM</th>
				   <th>FEC_DOC</th>
				   <th>HOR_DOC</th>
				   <th>LOG_USU</th>
				   <th>COD_ORG</th>
				   <th>DIR_ENV</th>
				   <th>PRO_CLI</th>
				   <th>PRO_FAR</th>
				   <th>COD_BAR</th>
				   <th>DES_PRO</th>
				   <th>UDM_PRO</th>
				   <th>CAN_PRO</th>
                </tr>
                <?php 
                    $datos = datos($conex);
                    $text= '';
					$writeTxt = "";
					$nombrePlano = "";
					$drodoc = 0;
					if(!is_dir('planos_cruzverde')){
						mkdir('planos_cruzverde', 0700); 
					}
					$pos = 0;
					foreach ($datos as $value) {
						$pos++;
						if($drodoc != $value['drodoc']){
							if($drodoc != 0){
								$fp = fopen('planos_cruzverde/'.$nombrePlano, 'w');
								fwrite($fp, $writeTxt);
								fclose($fp);
								send_file_to_ftp_server($nombrePlano);
							}
							$drodoc = $value['drodoc'];
							$writeTxt = "";
						}
						$nombrePlano = cod_cco_cruz_verde($value['cconom']).date("dmY", strtotime($value['drofec'])).'_'.$concepto_mov.str_pad($value['drodoc'], 10, "0", STR_PAD_LEFT);
						$cco_cruzverde = cod_cco_cruz_verde($value['cconom']);
						$concepto_mov = $value['drodetfue'] == '11' ? 'V' : 'D';
						$paciente = informacionPaciente($conex, "01", $value['drohis'], $value['dronum']);
						$tip_adm = tip_adm($value['cconom']);
						$cod_org = cod_org($value['cconom']);
						$ebs = consultar_ebs($value['drodetart'], $conex);
						$anio = explode("-", $value['drofec']);
						if($ebs[0]){
							if($ebs[0] != '' && $ebs[0] != '0'){
								$text = ' <tr style="font-size: 15px;">';
								$text2 = "";
								$text2 .= '<td>'.$nombrePlano.'</td>';
								$text2 .= '<td>'.str_pad($paciente['tipoDocumento'], 2, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($paciente['nroDocumento'], 13, "0", STR_PAD_LEFT) .'</td>';
								$text2 .= '<td>'.str_pad($tip_adm, 1, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($anio[0], 4, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($value['drodoc'], 12, "0", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($value['drodoc'], 12, "0", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.date("d/m/Y", strtotime($value['drofec'])).'</td>';
								$text2 .= '<td>'.str_pad($value['hora'], 4, "0", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($value['logusu'], 12, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($cod_org, 3, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($cco_cruzverde, 8, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($value['drodetart'], 10, "0", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($ebs[0], 10, "0", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad("", 13, "0", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad(trim($value['artnom']), 100, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad($ebs[1], 3, " ", STR_PAD_LEFT).'</td>';
								$text2 .= '<td>'.str_pad(intval($value['drodetcan']), 6, " ", STR_PAD_LEFT).'</td>';
								$text .= $text2;
								$text .= ' </tr>';
								echo $text;
								$writeTxt .= str_pad($paciente['tipoDocumento'], 2, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad($paciente['nroDocumento'], 13, "0", STR_PAD_LEFT);
								$writeTxt .= str_pad($tip_adm, 1, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad($anio[0], 4, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad($value['drodoc'], 12, "0", STR_PAD_LEFT);
								$writeTxt .= str_pad($value['drodoc'], 12, "0", STR_PAD_LEFT);
								$writeTxt .= date("d/m/Y", strtotime($value['drofec']));
								$writeTxt .= str_pad($value['hora'], 4, "0", STR_PAD_LEFT);
								$writeTxt .= str_pad($value['logusu'], 12, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad($cod_org, 3, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad($cco_cruzverde, 8, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad($value['drodetart'], 10, "0", STR_PAD_LEFT);
								$writeTxt .= str_pad($ebs[0], 10, "0", STR_PAD_LEFT);
								$writeTxt .= str_pad("", 13, "0", STR_PAD_LEFT);
								$writeTxt .= str_pad(trim($value['artnom']), 100, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad($ebs[1], 3, " ", STR_PAD_LEFT);
								$writeTxt .= str_pad(intval($value['drodetcan']), 6, " ", STR_PAD_LEFT);
								$writeTxt .= "\n";
							}
						}
						if(count($datos) == $pos){
							$fp = fopen('planos_cruzverde/'.$nombrePlano, 'w');
							fwrite($fp, $writeTxt);
							fclose($fp);
							send_file_to_ftp_server($nombrePlano);
						}
					}
					//Eliminar todos los archivos planos generados del servidor
					$files = glob('planos_cruzverde/*'); // get all file names
					foreach($files as $file){ // iterate files
						if(is_file($file)) {
							unlink($file); // delete file
						}
					}					
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