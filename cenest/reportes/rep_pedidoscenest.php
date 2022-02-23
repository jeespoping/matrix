<?php
/*******************************************************************************************************************************************
*                                             REPORTE PEDIDOS A LA CENTRAL DE ESTERILIZACION                                               *
*******************************************************************************************************************************************/

//==========================================================================================================================================|
//PROGRAMA				      : REPORTE PEDIDOS A LA CENTRAL DE ESTERILIZACION                                                              |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Noviembre 13 de 2019.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  : Noviembre 13 de 2019.                                                                                       |
//DESCRIPCION			      : Trae las cantidades solicitadas y despachadas por parte de la central y centro de costos					|
//																																			|
//TABLAS UTILIZADAS :																														|
//root_000040                 : Requerimientos																								|
//cenmat_000001               : Maestro de Articulos de la central                                                                          | 
//cenmat_000002               : Maestro de Metodos de esterilizacion                                                                        |                                                                                                                                          |
//cenmat_000004               : Movimientos de solicitados y despachados                                                                    |                                                                                                                                          |
//==========================================================================================================================================|
//                  ACTUALIZACIONES
//==========================================================================================================================================|
// 2022-02-16		- Se realiza validacion para que cuando se escoja la sede se filtren los centros de costos correspondientes.
//					se agrego un onchange, un script src para traernos una version de jquery en especifico para que nos sirviera el onchange
//					posteriormente creamos dos input oculto uno con el del wemp_pmla y el otro con el valor del selector de sede
//          		luego se realizo validacion de que si el parametro esta encendido o no para llevarnos los cambios tambien para CPA
//					por ultimo le pasamos a la consulta que trae los centros de costo la sede de cada una.
//					Linea de codigo: 41(script src), 64-68(onchange), 126(fecha de actualizacion), 163(encabezado lo pusimos despues del <form>)
//					207-239 (validacion de sede en la consulta que nos trae los centros de costo).
//==========================================================================================================================================|
// 2021-04-27		-	Se realiza modificación al código del reporte de central de esterilización, agregando a la tabla, una nueva columna
//						con los totales de las cantidades despachadas y una nueva fila para totalizar las cantidades solicitadas y
//						despachadas al igual que el total del costos.
// @autor			:	Joel Payares Hernández 
//==========================================================================================================================================|

include_once("conex.php");

$wemp_pmla = $_GET["wemp_pmla"];
?>

<html>
<head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<title>MATRIX - [REPORTE PEDIDOS A LA CENTRAL DE ESTERILIZACION]</title>

<script type="text/javascript">
	function inicio()
	{ 
		document.location.href='rep_pedidoscenest.php'; 
	}
	
	function enter()
	{
		document.forms.rep_pedidoscenest.submit();
	}
	
	function cerrarVentana()
	{
		window.close()
	}
	
	function VolverAtras()
	{
		history.back(1)
	}

	$(document).on('change','#selectsede',function(){
        window.location.href = "rep_pedidoscenest.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val()
    });

</script>

<?php

/**
 * Este metodo permite consultar los costos de insumos por código en la base de datos
 * matrix financiera. Se construye un array con los costos de los insumos y su respectivo
 * código como indice.
 *
 * @table costosyp_000097 de servidor de base de datos financiera
 *
 * @param Array			$arrayInsumos	Array con códigos de arcticulos o insumos
 * 										despachados.
 *
 * @return arrayCostos					Array con costos de articulos despachados.
 */
function consultarCostosInsumos( $arrayInsumos )
{
	global $conex;
	global $wemp_pmla;

	$ipMatrixFinanciero = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ipMatrixFinanciero');
	$conexMatrixFinanciero = auna_connectdb('Financiero') or die("No se realizo Conexion");

	$arrayCostos = array();
	if( count($arrayInsumos) > 0 )
	{
		$stringInsumos = implode("','",$arrayInsumos);

		$queryCostos = "SELECT Pcacod, Pcapro,CONCAT_WS('-',Pcaano,LPAD(Pcames,2,'0'),'01') AS Fecha 
						FROM costosyp_000097 a
						WHERE Pcacco='1082' 
							AND Pcaemp = '01' 
							AND Pcacod IN ('".$stringInsumos."')
							AND CONCAT_WS('-',Pcaano,LPAD(Pcames,2,'0'),'01') = (SELECT MAX(CONCAT_WS('-',Pcaano,LPAD(Pcames,2,'0'),'01')) 
																					FROM costosyp_000097 b 
																				WHERE b.Pcacod = a.Pcacod);";

		$resCostos = mysql_query($queryCostos,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$queryCostos." - ".mysql_error());
		$numCostos = mysql_num_rows($resCostos);

		if( $numCostos > 0 )
		{
			while($rowCostos = mysql_fetch_array($resCostos))
			{
				$arrayCostos[$rowCostos['Pcacod']] = round($rowCostos['Pcapro']);
			}
		}
	}

	return $arrayCostos;
}

/** 
 * By: Sebastian Alvarez Barona
 * Date: 22-02-2022
 * Descripcion: Se crea funcion para obtener los centros de costos de cada sede
 */
function obtenerCentrosCostos($sCodigoSede = NULL)
{
	global $wemp_pmla;
	global $conex;
	global $bdMovhos;

	$sFiltroSede='';
	

	if(isset($wemp_pmla) && !empty($wemp_pmla))
	{
		$estadosede=consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");

		if($estadosede=='on')
		{
			$codigoSede = (is_null($sCodigoSede)) ? consultarsedeFiltro() : $sCodigoSede;
			$sFiltroSede = (isset($codigoSede) && ($codigoSede !='')) ? " AND Ccosed = '{$codigoSede}' " : "";
		}
	}

	$query = "SELECT DISTINCT r40.Reqccs,c5.Cconom,SUBSTRING(r40.Reqccs,5,8) 
	FROM   root_000040 r40
				left join 
				costosyp_000005 c5 on (SUBSTRING(r40.Reqccs,5,8) = c5.Ccocod ) 
				left join ".$bdMovhos."_000011 c6 on (SUBSTRING(r40.Reqccs, 5, 8) = c6.Ccocod )
	WHERE Reqcco = '(01)1082' 
		AND   Reqtip = '13' 
		AND   (Reqcla='42' OR Reqcla='43')
		AND   Reqest='05'
		AND   Reqccs !=''
		".$sFiltroSede.";";

	$err3 = mysql_query($query,$conex);
	$num3 = mysql_num_rows($err3);

	echo "<select name='pp' id='searchinput'>";
		echo "<option>TODOS</option>";

			$tpp=$pp;

			if (isset($pp))
			{
				echo "<option>".$tpp[0]."-".$tpp[1]."</option>";
			} 

			for ($i=1;$i<=$num3;$i++)
			{
				$row3 = mysql_fetch_array($err3);
				echo "<option>".$row3[0]."-".$row3[1]."</option>";
			}

		echo "</select></td>";

}

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$bdMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

$wactualiz="Febrero 16 de 2022";

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();
//Encabezado

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	$empre1='cenest';
	//Conexion base de datos

	//Forma
	echo "<form name='forma' action='rep_pedidoscenest.php?wemp_pmla={$wemp_pmla}' method='post'>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
	echo "<input type='HIDDEN' NAME= 'tabla' value='".$tabla."'/>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' id= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'sede' id='sede' value='".$selectsede."'/>";

	encabezado("REGISTRO DE PEDIDOS A LA CENTRAL DE ESTERILIZACION", $wactualiz, "clinica", TRUE);
	
	if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
	{
		echo "<form name='rep_pedidoscenest' action='' method=post>";
	
		//Cuerpo de la pagina
		echo "<table align='center' border=0>";

		//Titulo encabezado tabla
		echo "<tr colspan='2' class='encabezadoTabla'>
				<td align='center' style='font-size: large;' colspan='2'>
					<span>Ingrese los parámetros de consulta</span>
				</td>
			</tr>";

		//Fecha inicial
		echo "<tr>";
		echo "<td class='fila1' width=190>Fecha Inicial</td>";
		echo "<td class='fila2' align='center' width=150>";
		campoFecha("fec1");
		echo "</td></tr>";
			
		//Fecha final
		echo "<tr>";
		echo "<td class='fila1'>Fecha Final</td>";
		echo "<td class='fila2' align='center'>";
		campoFecha("fec2");
		echo "</td></tr>";
	
		///Clasificacion
		echo "<tr>";
		echo "<td align='CENTER' colspan='2' class='fila1'><b><font text color=#003366 size=3><B>Clasificacion:</B></font></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=Left colspan='2' class='fila2'><INPUT TYPE='RADIO' NAME='clas' VALUE='1' CHECKED>Todos<br>";
		echo "<INPUT TYPE='RADIO' NAME='clas' VALUE='2'>Material Medico Quirurgico y Ropa<br>";
		echo "<INPUT TYPE='RADIO' NAME='clas' VALUE='3'>Solicitud Instrumental y Equipos</td>";
		echo "</td></tr>";
	
		// seleccion para los Responsables
		echo "<td align='CENTER' colspan='2' class='fila1'><b><font text color=#003366 size=3><B>Ccostos Solicita:</B><br></font></b>";

		/** Llamamos la función para mostrar el select con los centros de costos */
		obtenerCentrosCostos($selectsede);

		echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
		echo "</table>";
		echo '</div>';
		echo '</div>';
		echo '</div>';
	
	}
	else // Cuando ya estan todos los datos escogidos
	{
		$tpp = explode('-',$pp);
		// Abro el archivo
		$archivo = fopen("rep_pedidoscenest.txt","w");
		
		if ($clas == 1)
		{
			$clas1 = "%";  //Todos
			$clas2 = "Todas Las Clasificaciones";
		}

		if ($clas == 2)
		{
			$clas1 = "42";  //Material Medico Quirurgico y Ropa
			$clas2 = "42 - Material Medico Quirurgico y Ropa";
		}

		if ($clas == 3)
		{
			$clas1 = "43";  //Solicitud Instrumental y Equipos
			$clas2 = "43 - Solicitud Instrumental y Equipos";
		}

		// ACA COMIENZA LA IMPRESION
		echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>REPORTE DE PEDIDOS A LA CENTRAL DE ESTERILIZACION</b></font></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>CLASIFICACION: <i>".$clas2."</i></b></font></b></font></td>";
		echo "</tr>";
		echo "<tr><br><td></td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<table cellspacing='1' cellpadding='2' align='center' size='300'>
				<tr class='encabezadoTabla'>
					<td align='center' style='width:7%'><font size='2'><b>Fecha</b></font></td>
					<td align='center'><font size='2'><b>Numero Requerimiento</b></font></td>
					<td align='center'><font size='2'><b>Descripcion</b></font></td>
					<td align='center'><font size='2'><b>Observacion</b></font></td>
					<td align='center'><font size='2'><b>Recibe a Satisfaccion</b></font></td>
					<td align='center'><font size='2'><b>Ccostos Solicita</b></font></td>
					<td align='center'><font size='2'><b>Clasificacion</b></font></td>
					<td align='center'><font size='2'><b>Cod Producto</b></font></td>
					<td align='center'><font size='2'><b>Descripcion</b></font></td>
					<td align='center' style='width:10%'><font size='2'><b>Cantidad Solicitada</b></font></td>
					<td align='center' style='width:10%'><font size='2'><b>Cantidad Despachada</b></font></td>
					<td align='center'><font size='2'><b>Cod Metodo</b></font></td>
					<td align='center'><font size='2'><b>Descripcion</b></font></td>
					<td align='center' style='width:10%'><font size='2'><b>Costo Despacho</b></font></td>
				</tr>";

		if ($tpp[0]=="TODOS")
		{
			$query = " select r40.Reqfec,r40.Reqnum,r40.Reqdes,r40.Reqobe,r40.Reqsat,r40.Reqccs,r40.Reqcla,c4.Reqpro,c1.Prodes,
							c4.Reqcas,c4.Reqcad,c4.Reqmet,c2.Metdes  
					from   root_000040 r40,cenmat_000004 c4,cenmat_000001 c1,cenmat_000002 c2  
					where  r40.Reqcco = '(01)1082' 
						and  r40.Reqfec between '".$fec1."' and '".$fec2."' 
						and  r40.Reqcla like '".$clas1."' 
						and  r40.Reqest = '05' 
						and  r40.Reqnum = c4.Reqnum 
						and  r40.Reqcla = c4.Reqcla 
						and  c4.Reqcla = c1.Procla 
						and  c4.Reqpro = c1.Procod 
						and  c4.Reqmet = c2.Metcod 
						order by r40.Reqccs,r40.Reqnum ";
		}
		else
		{
			$query = " select r40.Reqfec,r40.Reqnum,r40.Reqdes,r40.Reqobe,r40.Reqsat,r40.Reqccs,r40.Reqcla,c4.Reqpro,c1.Prodes,
							c4.Reqcas,c4.Reqcad,c4.Reqmet,c2.Metdes  
					from   root_000040 r40,cenmat_000004 c4,cenmat_000001 c1,cenmat_000002 c2  
					where  r40.Reqcco = '(01)1082' 
						and  r40.Reqfec between '".$fec1."' and '".$fec2."' 
						and  r40.Reqcla in ('42','43') 
						and  r40.Reqccs like '%".$tpp[0]."%'  
						and  r40.Reqest = '05' 
						and  r40.Reqnum = c4.Reqnum 
						and  r40.Reqcla = c4.Reqcla 
						and  c4.Reqcla = c1.Procla 
						and  c4.Reqpro = c1.Procod 
						and  c4.Reqmet = c2.Metcod 
						order by r40.Reqccs,r40.Reqnum ";
		}

		// $err1 = mysql_query($query,$conex);
		$resultado_query = mysqli_query($conex, $query) or die(mysqli_error($conex));
		// $num1 = mysql_num_rows($err1);
		$num1 = mysqli_num_rows($resultado_query);

		$arrayInsumos = array();
		$arrayCostos = array();
		if( !$num1 || $num1 > 0 ) {
			$arrayArticulos = mysqli_fetch_all($resultado_query);
			foreach( $arrayArticulos as $fila)
			{
				array_push($arrayInsumos, $fila[11].$fila[7]);
			}
		}

		$arrayCostos = consultarCostosInsumos( $arrayInsumos );

		echo "<li><A href='rep_pedidoscenest.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
		echo "<br>";
		echo "<li>Registros generados: ".$num1;

		// Detalle o titulos de los campos de la tabla
		fwrite($archivo, "Fecha|Numero Requerimiento|Descripcion|Observacion|Recibe a Satisfaccion|Ccostos|Clasificacion|Cod Producto|Descripcion|Cantidad Solicitada|Cantidad Despachada|Metodo|Descripcion|" ); 
		fwrite($archivo, chr(13).chr(10) );   

		$swtitulo='SI';
		$canDes = 0;
		$canSol = 0;
		$costoTotal = 0;
		$j = 0;

		foreach( $arrayArticulos as $fila)
		{
			if ( is_int($j/2) )
			{
				// $wcf="DDDDDD";  // color de fondo
				$wcf="fila1";  // color de fondo
			}
			else
			{
				//$wcf="CCFFFF"; // color de fondo 
				$wcf="fila2"; // color de fondo
			}

			echo "<tr  class=".$wcf.">";

			for ( $i = 0; $i < count( $fila ); $i++ )
			{
				echo "<td align=center><font size=1>$fila[$i]</font></td>";
			}

				echo "<td align=center><font size=1>$ " . number_format( ( $fila[10] * $arrayCostos[ $fila[11].$fila[7] ] ) ) . "</font></td>
			</tr>";
			
			$canSol += $fila[9];
			$canDes += $fila[10];
			$costoTotal += ( $fila[10] * $arrayCostos[ $fila[11].$fila[7] ] );

			$j++;
		}

		echo "
			<tr class='encabezadoTabla'>
				<td colspan='9'> <font size='2'><b> Totales </b></font> </td>
				<td align='center'> <font size='2'><b> Cant. Soli: </b> {$canSol} </font> </td>
				<td align='center'> <font size='2'><b> Cant. Desp: </b> {$canDes} </font> </td>
				<td colspan='2'></td>
				<td align='center'> <font size='2'><b> Costo Total: </br>$</b> " . number_format( $costoTotal ) . " </font> </td>
			</tr>
		";

		echo "</table>"; 
		echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
		fclose($archivo);
		echo "<li><A href='rep_transcardio.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
		echo "<br>";
		echo "<li>Registros generados: ".$num1;
		echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
		echo "</table>";
  
	} // cierre del else donde empieza la impresión

}
?>