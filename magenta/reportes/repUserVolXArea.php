<html>

<head>
<title>REPORTE DE USUARIOS INSATISFECHOS</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
<script src="efecto.php"></script>
<script>
        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
	        $(document).ready(function() {
			$("#fecInicial, #fecFinal").datepicker({
		       showOn: "button",
		       buttonImage: "../../images/medical/root/calendar.gif",
		       buttonImageOnly: true,
		       maxDate:"+1D"
		    });
		});
</script>
<SCRIPT LANGUAGE="JavaScript1.2">
</SCRIPT>

</head>

<body>
<?php
include_once("conex.php");
$wemp_pmla = $_REQUEST['wemp_pmla'];
/****************************************************************************************************************
 * Tipo:	Reporte
 * Por:		Edwin Molina Grisales	
 * Fecha:	2009-09-10
 * Detalla por area los usuarios que volveran, no volveran, cambiaran o no responden
 * por formatos
 
/****************************************************************************************************************
  Actualizaciones:
		    2022-04-28  Daniel CB.
						-Se realiza corrección de parametros 01 quemados.	

 			2016-05-06  Arleyda Insignares C.
 						-Se Modifican los campos de calendario fecha inicial y fecha final configurados con
 						 la funcion 'campoFechaDeFecto' por calendario jQuery
						-Se cambia encabezado, tablas y titulos con ultimo formato.

/****************************************************************************************************************
 *                                            FUNCIONES
 ****************************************************************************************************************/

function pintarInformacion( $tabla ){
	
	if( !empty($tabla) || count($tabla) > 0 ){
	
		$i =0;
		
		$totsi = 0;
		$totno = 0;
		$totnores = 0;
		$totcamb = 0;
		$tot = 0;
		
		echo "<table align='center'>";
		
		//Encabezado de la tabla
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td width='90'>Codigo<br>del Area</td>";
		echo "<td>Nombre del Area</td>";
		echo "<td width='60'>SI</td>";
		echo "<td width='60'>NO</td>";
		echo "<td width='100'>CAMBIO DE<br>OPINION</td>";
		echo "<td width='100'>NO RESPONDE</td>";
		echo "<td>TOTAL</td>";
		echo "</tr>";
		
		echo "";
		
		foreach( $tabla as $fila ){
			
			$classfila = "fila".(($i%2)+1);
			
			echo "<tr class='$classfila' align='center'>";
			echo "<td>{$fila[0]}</td>";
			echo "<td align='left'>{$fila[1]}</td>";
			echo "<td>{$fila[2]}</td>";
			echo "<td>{$fila[3]}</td>";
			echo "<td>{$fila[5]}</td>";
			echo "<td>{$fila[4]}</td>";
			echo "<td>{$fila[6]}</td>";
			echo "</tr>";
			
			$totsi += $fila[2];
			$totno += $fila[3];
			$totnores += $fila[5];
			$totcamb += $fila[4];
			$tot += $fila[2]+$fila[3]+$fila[4]+$fila[5];
			
			$i++;
		}
		
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td colspan='2'>Totales</td>";
		echo "<td>$totsi</td>";
		echo "<td>$totno</td>";
		echo "<td>$totnores</td>";
		echo "<td>$totcamb</td>";
		echo "<td>$tot</td>";
		echo "</tr>";
		
		echo "</table>";
	}
	else{
		echo "<br><p align='center'><b>No se genero ningun resultado</b></p>";
	}
}

//=================================================================================================================================
include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user'])){
	echo "error";
}
else{
	// Se muestra el encabezado del programa
	$wactualiz = "2022-04-28";
	$titulo    = "REPORTE DE USUARIOS INSATISFECHOS"; 
    encabezado($titulo,$wactualiz, "clinica");  

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	
	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;
	
	//encabezado("REPORTE DE USUARIOS INSATISFECHOS", "1.0 Mayo 22 de 2009" ,"clinica");
	
	$q = " SELECT detapl, detval "
		. "   FROM root_000050, root_000051 "
		. "  WHERE empcod = '" . $wemp_pmla . "'"
		. "    AND empest = 'on' "
		. "    AND empcod = detemp ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($row[0] == "cenmez")
				$wcenmez = $row[1];

			if ($row[0] == "afinidad")
				$wafinidad = $row[1];

			if ($row[0] == "movhos")
				$wbasedato = $row[1];

			if ($row[0] == "tabcco")
				$wtabcco = $row[1];
				
		}
	}
	else
	{
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	}
	
	if( !isset($action) || empty($action) 
		|| ( ( !isset($fecInicial) || !isset($fecFinal) ) || $fecFinal < $fecInicial )  ){

		if( !isset($fecInicial) ){
			$fecInicial = date("Y-m-01");
		}	
			
		if( !isset($fecFinal) ){
			$fecFinal = date("Y-m-t");			
		}
		
		if( $fecFinal < $fecInicial ){
			mensajeEmergente("La fecha inicial debe ser menor que la fecha final");
		}
		echo "<form name='menuinicial' action='repUserVolXArea.php?wemp_pmla=".$wemp_pmla."' method='post'>";
		
		//Eligiendo centro de Costos
		$sql = "SELECT
					carcod, carnom
				FROM
					{$wafinidad}_000019
				WHERE
					carest='on'";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()."- Error en la consulta $sql -".mysql_error()  );
		
		echo "<br><br>";
		echo "<table align='CENTER'>";
		echo "<tr class='encabezadotabla'><td>Centro de Costos</td></tr>";
		echo "<tr><td>";
		
		echo "<SELECT name='area'><option value='Todos'>% - Todos</option>";
		for(; $rows = mysql_fetch_array( $res );){
			echo "<option>{$rows['carcod']}-{$rows['carnom']}</option>";
		}
		echo "</SELECT>";
		
		echo "</td></tr>";
		echo "</table>";
		
		//Eligiendo rango de fechas
		//Se cambia la funcion campoFechaDeFecto po calendario jQuery
		//<td>";campoFechaDefecto("fecInicial", $fecInicial ); echo"</td>
		//<td>";campoFechaDefecto("fecFinal", $fecFinal ); echo"</td>
		
		echo "<table align='CENTER'>
			<tr class='encabezadotabla'>
				<td>Fecha inicial</td>
				<td>Fecha final</td>
			</tr>
			<tr class='fila1'>
			    <td><input type='text' readonly='readonly' id='fecInicial' name='fecInicial' value='".$fecInicial."' class=tipo3></td>
			    <td><input type='text' readonly='readonly' id='fecFinal' name='fecFinal' value='".$fecFinal."' class=tipo3></td>			
			</tr>
		</table>";
		
		echo "<br><br>";		
		//Botones de Generar o Cerrar
		echo "<table align='CENTER'>
			<tr>
				<td align='center'><INPUT type='submit' value='Generar' style='width:100'></td>
				<td align='center'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript: cerrarVentana();'></td>
			</tr>
		</table>";
		
		echo "<INPUT type='hidden' name='action' value='1'>";
		
		echo"</form>";
		
	}
	else{
		
//		$aux = false;
		$tabla = array();
//		$tabla[0] = array();
//		$tabla[0][0] = "";
//		$tabla[0][1] = "";
//		$tabla[0][2] = 0;	//Lleva la cuenta de SI
//		$tabla[0][3] = 0;	//LLeva la cuenta de NO
//		$tabla[0][4] = 0;	//Lleva la cuenta de NO RESPONDE
//		$tabla[0][5] = 0;	//Lleva la cuenta de CAMB
//		$tabla[0][6] = 0;	//Lleva la cuenta total
		
		echo "<form action='repUserVolXArea.php?wemp_pmla=".$wemp_pmla."' method='post'>";
		
		//Encabezado del informe
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Desde</td>";
		echo "<td class='fila1' width='75'>$fecInicial</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla' width='75'>Hasta</td>";
		echo "<td class='fila1'>$fecFinal</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Area</td>";
		echo "<td class='fila1'>$area</td>";
		echo "</tr>";
		echo "</table><br><br>";
		
		if( $area == "Todos" ){
			$area = "%";
		}
		else{
			$exp = explode( "-", $area );
			$area = $exp[0]."-%";
		}
		
		$tagArea = "";	//Guarda la ultima area consultada
		
		$sql = "SELECT
   					carcod, carnom, ccovol
				FROM
				    {$wafinidad}_000017 a,
				   	{$wafinidad}_000019 b
				WHERE
				   	a.fecha_data BETWEEN '$fecInicial' AND '$fecFinal'
				   	AND ccoori like '$area'
				   	AND carcod = SUBSTRING_INDEX( ccoori, '-', 1 )
				   	AND carest='on'
				GROUP BY ccoid
				ORDER BY carcod, ccovol"; 
				   	
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()."- Error en la consulta $sql -".mysql_error()  );
		
		for( $i = 0, $j = -1; $rows = mysql_fetch_array($res) ; $i++ ){
			
			if( $j == -1 || $tabla[$j][0] != $rows['carcod'] ){
				
				$j++;
//				$aux = true;
				
				$tabla[$j][0] = $rows['carcod'];
				$tabla[$j][1] = $rows['carnom'];
				
				$tabla[$j][2] = 0;	//Lleva la cuenta de SI
				$tabla[$j][3] = 0;	//LLeva la cuenta de NO
				$tabla[$j][4] = 0;	//Lleva la cuenta de NO RESPONDE
				$tabla[$j][5] = 0;	//Lleva la cuenta de CAMB
				$tabla[$j][6] = 0;	//Lleva la cuenta total
			}		
			
			switch( $rows['ccovol'] ){
				case 'SI':
					$tabla[$j][2]++;
					break;
					
				case 'NO':
					$tabla[$j][3]++;
					break;
					
				case 'NO RESPONDE':
					$tabla[$j][4]++;
					break;
					
				case 'camb':
					$tabla[$j][5]++;
					break;
			}
			
			$tabla[$j][6]++;
			
		}
		
		pintarInformacion( $tabla );
		
		echo "<br><table align=center>
			<tr align=center>
			<td colspan=5>
				<INPUT type='submit' value='Retornar' style='width:100'> | 
			<td>
				<INPUT type='button' value='Cerrar' onClick='javascript: cerrarVentana();' style='width:100'></td>
		</tr>
		</table>";
		
		echo "<INPUT type='hidden' name='fecInicial' value='$fecInicial'>";
		echo "<INPUT type='hidden' name='fecFinal' value='$fecFinal'>";		
		echo "</form>";	
	}
}
?>
</body>
</html>
