 <title>REPORTE REPOSICION</title>
</head>
<body BGCOLOR="">
<BODY>
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");
/**
 * Reporte de reposición de los artículos consumidos durante uno o varios turnos en un centro de costos.
 * Los turnos existentes son 7 am a 7 pm y de 7 pm a 7 am. 
 * 
 * @table 000002 SELECT
 * @table 000003 SELECT
 * @table 000026 SELECT
 * @table 000009 SELECT
 * @table 000001 SELECT
 * @table 000001 SELECT
 * 
 * @wvar $fuente		
 * @wvar $fuenteCarga	
 * @wvar $fuenteDev		
 */
 //---------------------------------------
 //		ACTUALIZACIONES
 //---------------------------------------
 // Noviembre 7 de 2013: Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 para que traiga los     
 //						datos de contingencia (tabla movhos_00143) con estado activo. Jonatan Lopez	 	
 //-----------------------------------------------------------------------------------------------------------------------
 //	Jerson trujillo: 2013-07-11
 //	Descripcion: Se modifican los turnos de 6 am a 6 pm	y 6 pm a 6 am.			
 //---------------------------------------

/****************************************************************************************************************
 *						 								FUNCIONES
 ****************************************************************************************************************/
 
//Se crea esta funcion igual a la funcion rangoFechas, pero esta funciona con la tabla 000143 que es la de contingencia.
function rangoFechas_143( $campo, $fechaInicial, $fechaFinal, $horaInicial, $horaFinal ){
	
	if( $fechaInicial != $fechaFinal ){
		
		$fecini = date("Y-m-d", strtotime( $fechaInicial )+24*60*60 );
		$fecfin = date("Y-m-d", strtotime( $fechaFinal )-24*60*60 );

		if( $fecini > $fecfin ){
			$fechas = "";
		}
		else{
			$fechas = "or ( ".$campo."_000002.Fecha_data BETWEEN '$fecini' AND '$fecfin')";
		}
		
		$rango="((".$campo."_000002.Fecha_data='".$fechaInicial."'  and ".$campo."_000143.Hora_data between '".$horaInicial."' and '24:00:00') or (".$campo."_000002.Fecha_data='".$fechaFinal."'  and ".$campo."_000143.Hora_data between '00:00:00' and '".$horaFinal."') ".$fechas.") ";	
	}
	else{
		$rango="".$campo."_000002.Fecha_data='".$fechaInicial."' and ".$campo."_000143.Hora_data between '".$horaInicial."' and '".$horaFinal."' ";
	}
	
	return $rango;
} 
 
 
 
function rangoFechas( $campo, $fechaInicial, $fechaFinal, $horaInicial, $horaFinal ){
	
	if( $fechaInicial != $fechaFinal ){
		
		$fecini = date("Y-m-d", strtotime( $fechaInicial )+24*60*60 );
		$fecfin = date("Y-m-d", strtotime( $fechaFinal )-24*60*60 );

		if( $fecini > $fecfin ){
			$fechas = "";
		}
		else{
			$fechas = "or ( ".$campo."_000002.Fecha_data BETWEEN '$fecini' AND '$fecfin')";
		}
		
		$rango="((".$campo."_000002.Fecha_data='".$fechaInicial."'  and ".$campo."_000003.Hora_data between '".$horaInicial."' and '24:00:00') or (".$campo."_000002.Fecha_data='".$fechaFinal."'  and ".$campo."_000003.Hora_data between '00:00:00' and '".$horaFinal."') ".$fechas.") ";	
	}
	else{
		$rango="".$campo."_000002.Fecha_data='".$fechaInicial."' and ".$campo."_000003.Hora_data between '".$horaInicial."' and '".$horaFinal."' ";
	}
	
	return $rango;
}

function buscarBodega( $art, $cco ){

	global $conex_unix;

	$bodega = '';
	
	$sql = "SELECT
				artubiubi 
			FROM
				ivartubi
			WHERE
				artubiart = '$art'
				and artubiser = '$cco'";
	
	$res = odbc_do( $conex_unix, $sql );
	
	if( $rows = odbc_fetch_row( $res ) ){
		$bodega = odbc_result( $res, 1 );
	}
	
	return $bodega;
}

function actualizarUbicacion( $tabla, $art, $cco ){
	
	global $conex;
	
	$ubicacion = "";
	
	$ubicacion = buscarBodega( $art, $cco );
	
	$sql = "UPDATE 
				$tabla
			SET 
				ubi = '$ubicacion'
			WHERE
				art = '$art'";
				
	$res = mysql_query( $sql, $conex );
	
	if( mysql_affected_rows() > 0 ){
		return true;	
	}
	else{
		return false;
	}
}

function actualizarTabla( $tabla, $cco ){
	
	global $conex;
	
	$sql = "SELECT 
				art, ubi
			FROM
				$tabla";
				
	$res = mysql_query( $sql, $conex );
	
	for( ;$rows = mysql_fetch_array( $res ); ){
		
		actualizarUbicacion( $tabla, $rows[0], $cco );
		
	}
}
/****************************************************************************************************************
 *						 							FIN DE FUNCIONES
 ****************************************************************************************************************/

if(!isset($_SESSION['user']))
echo "error";
else
{

	

    

    include_once("root/comun.php");
    include_once("movhos/otros.php");
    
	if(!isset($fecini)  or !isset($cc))
	{
		/**
		 * Se piden los parametros, es decir el centro de costos, las fechas y las horas.
		 */

		if(isset($cc))
		{
			$ini1=strpos($cc,"-");
			$cc=substr($cc,0,$ini1);
		}
		echo "<form action='' method=post>";
		
		$wactualiz='Noviembre 7 de 2013';
		$wemp_pmla = $emp;
		encabezado("REPOSICION DE MEDICAMENTOS Y MATERIAL",$wactualiz, "clinica");
		
		echo "<center><table>";
		
		echo "<tr class=seccion1>";
		echo "<td><b>CC que factura:</b></td>";
		echo "<td><select name='cc' >";
		$q = " SELECT Ccocod, Cconom  "
		    ."   FROM ".$bd."_000011 "
		    ."  WHERE Ccofac = 'on' "
		    ."    AND Ccoest = 'on'";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				if (($row[0]) == $cc)
				echo "<option selected>".$row[0]."</option>";
				else
				echo "<option>".$row[0]."</option>";
			}
		}	// fin del if $num>0
		echo "</select></td>";
		echo "<tr class=seccion1>";
		echo "<td><b>CC de origen (Bodega):</b></td>";
		echo "<td><select name='ccorigen' >";
		
		conexionOdbc($conex, $bd, $conex_unix, 'inventarios');
		
		$sql = "SELECT 
					artubiser
				FROM 
					ivartubi
				GROUP BY artubiser
				";
		
		$res = odbc_do($conex_unix, $sql ) or die( odbc_error() );
		
//		$num = odbc_num_rows( $res );

//		if($num>0)
//		{
			for ($j=0;$rows = odbc_fetch_row( $res );$j++)
			{
//				$rows = odbc_fetch_row( $res );
				$row = odbc_result( $res, 1 );
				echo "<option>".$row."</option>";
			}
//		}	// fin del if $num>0
		
		liberarConexionOdbc($conex_unix);
		
		echo "</select></td>";
		echo "</tr>";
		echo "<tr class=seccion1>";
		echo "<td><b>DESDE: </b></td>";
		echo "<td>";campoFechaDefecto( "fecini", date("Y-m-d"));echo "</td>";
		echo "<tr class=seccion1><td><b>HORA: </b></td>";
		echo "<td><select name='hora1'>";
		echo "<option>06:00:00</option>";
		echo "<option>18:00:00</option>";
		echo "<option>00:00:00</option>";
		echo "</select></td></tr>";

		echo "<tr class=seccion1><td><b>HASTA: </b></td>";
		echo "<td>";campoFechaDefecto( "fecfin", date("Y-m-d"));echo "</td>";
		/**
		 * Select de la hora final
		 */
		echo "<tr class=seccion1><td><b>HORA: </b></td>";
		echo "<td><select name='hora2'>";
		echo "<option>05:59:59</option>";
		echo "<option>17:59:59</option>";
		echo "</select></td></tr>";
		echo "<tr><td align=center colspan=3 ><input type='submit' value='ACEPTAR'></td></tr>";
		echo "</form>";
	}
	else
	{
		conexionOdbc($conex, $bd, $conex_unix, 'inventarios');
		
		$cco['cod']=$cc;
		getCco($cco,"C", $emp);
		$fuenteCarga=$cco['fue'];

		getCco($cco,"D", $emp);
		$fuenteDev=$cco['fue'];

		$hora = rangoFechas( $bd, $fecini, $fecfin, $hora1, $hora2 );
		$hora_143 = rangoFechas_143( $bd, $fecini, $fecfin, $hora1, $hora2 );
		
		if( $fecini ==  $fecfin ){
			$desde="DESDE ".$hora1." HASTA LAS ".$hora2." DE ".$fecfin;
		}else{
			$desde="<b>DESDE </b>".$hora1." <b>DE </b>".$fecini." <b>HASTA LAS </b>".$hora2." <b>DE </b>".$fecfin;
		}
			

		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table align='center'>";
		echo "<tr class=seccion1><td align=center colspan='3'><B>REPOSICION DE MATERIAL Y MEDICAMENTOS INGRESADOS</b>";
		echo "</tr><tr class=encabezadoTabla><td align=center colspan='3'><b>CENTRO DE COSTOS QUE FACTURA: ".$cc."</b></font>";
		echo "</tr><tr class=encabezadoTabla><td align=center colspan='3'><b>CENTRO DE COSTOS ORIGEN (BODEGA): ".$ccorigen."</b></font>";
		echo "</tr><tr class=seccion1><td align=center colspan='3'>".$desde."";

		echo "</table>";
		echo "<table align='center'>";

		
		/**
		 * Busca los registros que corresponden a los encabezados ya seleccionados
		 * y que tambien esten dentro de las fechas y horas estipuladas
		 */
		$facfde="repRepFacfde".date("Yis");
		$q = " CREATE TEMPORARY TABLE ".$facfde." "
		."     SELECT Fenfue, Fdeart, SUM(Fdecan) as Fdecan  "
		."       FROM ".$bd."_000002, ".$bd."_000003 "
		."      WHERE Fenfue in ('".$fuenteCarga."', '".$fuenteDev."' )"
		."        AND Fencco = '".$cco['cod']."' "
		."        AND Fdenum = Fennum "
		."        AND ".$bd."_000002.Fecha_data= ".$bd."_000003.Fecha_data "
		."        AND ".$hora." "
		."        AND Fdeest = 'on' "
		." GROUP BY Fenfue, Fdeart    "
		/*********************************************************************************************************************/
		/* Noviembre 07 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		." 	    UNION "
		."     SELECT Fenfue, Fdeart, SUM(Fdecan) as Fdecan  "
		."       FROM ".$bd."_000002, ".$bd."_000143 "
		."      WHERE Fenfue in ('".$fuenteCarga."', '".$fuenteDev."' )"
		."        AND Fencco = '".$cco['cod']."' "
		."        AND Fdenum = Fennum "
		."        AND ".$bd."_000002.Fecha_data= ".$bd."_000143.Fecha_data "
		."        AND ".$hora_143." "
		."        AND Fdeest = 'on' "
		." GROUP BY Fenfue, Fdeart    "; 
		
		$err = mysql_query($q,$conex);
		echo mysql_error();	
		/**
		 * Se el grupo y las unidades del artículo en el maestro de artículos
		 * para todos los registros cuya fuente sea de carga y se almacenan en otra tabla
		 */
		$facfdeFin="repRepFacfdeFin".date("Yis");
		$q = " CREATE TEMPORARY TABLE ".$facfdeFin." "
		."     SELECT Fdeart as art, Fdecan as can, Artcom as nom, Artgru as gru, Artuni as uni, Artubi as ubi "
		."       FROM ".$facfde.", ".$bd."_000026 "
		."      WHERE Fenfue = '".$fuenteCarga."' "
		."        AND Artcod =  Fdeart "
		."        AND Artest = 'on' "
		."    ORDER BY Artubi, Artcom ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		
		/**
		 * Se restan las cantidades de los registros que hayan sido cargados con fuentes de devolución.
		 */
		$q = "UPDATE ".$facfdeFin.", ".$facfde." "
		."       SET can = can - Fdecan "
		."     WHERE Fdeart = art "
		."       AND Fenfue ='".$fuenteDev."' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();

		actualizarTabla( $facfdeFin, $ccorigen );
		liberarConexionOdbc($conex_unix);
		$q = " SELECT *  "
		."       FROM ".$facfdeFin." "
		."      WHERE can > 0 "
		."      ORDER BY ubi, nom ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			$grupo="0";

			echo "<tr>&nbsp</tr>";
			for($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($err);
//				if(substr($row['gru'],0,3) != "RUS"  and substr($row['gru'],0,3) != "FRA")
//				if( true || $i == 1 )
//				{
					if($grupo != $row['ubi'])
					{
							
						$grupo = $row['ubi'];
						
						if( empty($row['ubi']) )
							$row['ubi'] = "SIN UBICACION";
						
						echo "<tr class=seccion1>";
						echo "<td colspan=4 align=center><b>Ubicacion: ".$row['ubi']."</b></td>";
						echo "</tr>";
						
						echo "<tr class=encabezadoTabla align='center'>";
						echo "<td>COD.</td>";
						echo "<td>DESCRIPCION</td>";
						echo "<td>UNID.</td>";
						echo "<td>CANT.</td>";
//						echo "<td>UBI.</td>";
						echo "</tr>";
						
					}
//				}
				
				if (is_integer($i / 2))
				   $wclass = "fila1";
				  else
				     $wclass = "fila2";

				echo "<tr class=".$wclass.">";
				echo "<td width='70' align='center'>".$row['art']."</td>";
				echo "<td>".$row['nom']."</td>";
				echo "<td width='50' align='center'>".$row['uni']."</td>";
				echo "<td width='50' align='right'>".$row['can']."</td>";
//				echo "<td width='50' laign='center'>".$row['ubi']."</td>";
				echo "</tr>";

			}//fin del for
		}// fin del num
	}
	echo "</table>";
	
	echo "<br>";
	echo "<center><table>"; 
    echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
   
}
?>
</body>
</html>