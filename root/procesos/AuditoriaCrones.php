<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AUDIRORIA EJECUCION CRONES UNIX - MATRIX</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="facSer_style.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <style>
        .Ntooltip
        {
        }

        a.Ntooltip {
            position: relative; /* es la posición normal */
            text-decoration: none !important; /* forzar sin subrayado */
            color:#0080C0 !important; /* forzar color del texto */
            font-weight:bold !important; /* forzar negritas */
        }

        a.Ntooltip:hover {
            z-index:999; /* va a estar por encima de todos */
            background-color:#000000; /* DEBE haber un color de fondo */
        }

        a.Ntooltip span {
            display: none; /* el elemento va a estar oculto */
        }

        a.Ntooltip:hover span {
            display: block; /* se fuerza a mostrar el bloque */
            position: absolute; /* se fuerza a que se ubique en un lugar de la pantalla */
            top:2em; left:2em; /* donde va a estar */
            width:250px; /* el ancho por defecto que va a tener */
            padding:5px; /* la separación entre el contenido y los bordes */
            background-color: #0080C0; /* el color de fondo por defecto */
            color: #FFFFFF; /* el color de los textos por defecto */
        }
    </style>
    <?php

//==============================================================================================================================
	//Mayo 28 de 2021 Leandro Meneses
	//Al campo godetcla (clasificada) se lleva l valor 'N' para que permita clasificar la glosa
	//==============================================================================================================================
    include("conex.php");
    include("root/comun.php");

	
	
	

	$user_session = explode('-', $_SESSION['user']);
	$wuse = $user_session[1];
	mysql_select_db("matrix");
	$conex = obtenerConexionBD("matrix");
	$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");

	
    $fecha_Actual = date('Y-m-d');  
	$hora_Actual = date('H:m:s');
    $ano_Actual = date('Y');        
	$mes_Actual = date('m');
?>
</head>

<body>

<?php
$actualiz = "2021-08-21";
encabezado( "AUDITORIA CRONES UNIX-MATRIX", $actualiz ,"clinica" );
?>

<div class="form-group col-md-6">
	<table class="table">
		<thead class="thead-dark">
			<tr>		

				<th scope="col">Tabla</th>
				<th scope="col" class="col text-center">Cco</th>				
				<th scope="col" class="col text-right">#Reg Unix</th>
				<th scope="col" class="col text-right">#Reg Matrix</th>
				<th scope="col" class="col text-right">Diferencia</th>		
			</tr>
		</thead>
		<tbody>
<?php

	$query1 = "select count(*) as cantTarifas from  IVARTTAR";
	$commit1 = odbc_do($conex_o, $query1);
	$conteoTarifasUnix = odbc_result($commit1,'cantTarifas');
	
	$query1 = "select count(*) as cantTarifas from  cliame_000026";
	$res = mysql_query($query1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryApliMed . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$conteoTarifasMatrix = $row[0];
	$diferencia = $conteoTarifasUnix - $conteoTarifasMatrix;
	

?>
		<tr>
		  <td><b>Tarifas</b></td>
		  <td class="col text-center">N/A</td>								  
		  <td class="col text-right"><?php echo $conteoTarifasUnix  ?></td>								  
		  <td class="col text-right" ><?php echo $conteoTarifasMatrix  ?></td>
		  <td class="col text-right"><b><?php echo $diferencia ?></b></td>								  
								  
		</tr>	

		
<?php

	$sql = "SELECT Ccoori
			  FROM movhos_000058
			 WHERE Ccoest = 'on'
		  GROUP BY 1
			";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$ccos = "('')";
	for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		if( $i == 0 )
			$ccos = "('".$rows['Ccoori']."'";
		else
			$ccos .= ",'".$rows['Ccoori']."'";
	}
	$ccos .= ")";

	$q= "SELECT salser,count (*) as cantart"
	."     FROM ivsal "
	."    WHERE salano = '".date('Y')."' "
	."      and salmes = '".date('m')."' "
	."      and salser NOT IN ".$ccos
	."      group by salser";
	
	//echo($q);
	$err_o= odbc_do($conex_o,$q);

	while(odbc_fetch_row($err_o))
	{
		
		$sql = "select count(*) as cantart from movhos_000141 "
			."  WHERE Salser = '".odbc_result($err_o,1). "'";
		//echo $sql;
		$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryApliMed . " - " . mysql_error());
		$rowmatrix = mysql_fetch_array($res);
		$conteoSaldosMatrix = $rowmatrix[0];
		$conteoSaldosUnix = odbc_result($err_o,2);
		$diferencia = $conteoSaldosUnix - $conteoSaldosMatrix;
			
					
			
?>			



		<tr>
		  <td><b>Saldos</b></td>
		  <td class="col text-center"><?php echo odbc_result($err_o,1)  ?></td>								  
		  <td class="col text-right"><?php echo $conteoSaldosUnix  ?></td>								  
		  <td class="col text-right"><?php echo $conteoSaldosMatrix  ?></td>
		  <td class="col text-right"><b><?php echo $diferencia ?></b></td>								  
								  
		</tr>	

<?php

	}
	
	$query1 = "select count(*) as cantDisgnosticos from  india, ingdx, insec, OUTER insub"
	." where diacie = 'C10' AND gdxcod = diagdx AND seccod = diasec AND subcod = diasub";
	
	$commit1 = odbc_do($conex_o, $query1);
	$conteoDiagUnix = odbc_result($commit1,'cantDisgnosticos');
	
	$query1 = "select count(*) as cantDisgnosticos from root_000011";
	$res = mysql_query($query1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryApliMed . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$conteoDiagMatrix = $row[0];
	$diferencia = $conteoDiagUnix - $conteoDiagMatrix;
		

?>		

		<tr>
		  <td><b>Diagnósticos</b></td>
		  <td class="col text-center">N/A</td>								  
		  <td class="col text-right"><?php echo $conteoDiagUnix  ?></td>								  
		  <td class="col text-right" ><?php echo $conteoDiagMatrix  ?></td>
		  <td class="col text-right"><b><?php echo $diferencia ?></b></td>								  
								  
		</tr>	


		</tbody>
	</table>
</div>

</body>
</html>