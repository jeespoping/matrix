<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=3><b>Generacion de Archivos de Facturacion Imagenologos UNIX</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Imagenologos.php Ver. 2015-09-29</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	include_once("root/comun.php");
	$conex_o = odbc_connect('facturacion','','');
	
	$key = substr($user,2,strlen($user));
	echo "<form action='Imagenologos.php' method=post>";
	if((!isset($wtip) || $wtip == '')  || (!isset($centroCostos) || $centroCostos=='') || !isset($para) || $para == '')
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE ARCHIVOS DE FACTURACION IMAGENOLOGOS UNIX</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Tipo de  Proceso:</td><td bgcolor=#cccccc align=center>";
		echo "<select name='wtip'>";
		echo "<option value=''>Seleccione</option>";
		echo "<option value='1'>INTERNOS</option>";
		echo "<option value='2'>AMBULATORIOS</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "
			<tr bgcolor='#cccccc' align='center'>
				<td>Para:</td>
				<td>
					<select name='para' id='para'>
						<option value=''>Seleccione</option>
						<option value='R'>Remisiones</option>
						<option value='P'>Pedidos</option>
					</select>
				</td>
			</tr>
			<tr bgcolor='#cccccc' align='center'>
				<td>Centro de costos:</td>
				<td>";
		
		// --> Obtener los centros de costos que registran movimientos en las tablas
		$arrayCco = array();
		$sqlCco = "		
		SELECT ccosto
		  FROM ameradiol3
		 GROUP BY ccosto
		 UNION
		SELECT ccost AS ccosto
		  FROM ameradiol2
		 GROUP BY ccost
		";		
		$resCco = odbc_do($conex_o,$sqlCco);
		while(odbc_fetch_row($resCco))
			$arrayCco[] = odbc_result($resCco,1);
		
		// --> Pintar seleccionador de centros de costos
		echo "		<select name='centroCostos' id='centroCostos'>
						<option value=''>Seleccione</option>";
			foreach($arrayCco as $cco)
				echo "	<option value='".$cco."'>".$cco."</option>";
				
		echo "		</select>
				</td>
			</tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$textoEmpre		= "";
		$textoParti		= "";
		
		// --> Query para remisiones
		if($para == 'R')
		{
			if($wtip == "1")
				$query = "select * from ameradiol3 where ccosto = '".$centroCostos."' and tippac = 'I' order by nrofac, tipemp,fecfac";
			else
				$query = "select * from ameradiol3 where ccosto = '".$centroCostos."' and tippac = 'A' order by nrofac, tipemp,fecfac";
		}
		// --> Query para pedidos
		else
		{
			if($wtip == "1")
				$query = "select * from ameradiol2 where ccost = '".$centroCostos."' and tipoc = 'I' order by docum, tipod, fecha";
			else
				$query = "select * from ameradiol2 where ccost = '".$centroCostos."' and tipoc = 'A' order by docum, tipod, fecha";
		}
		$err_o = odbc_do($conex_o,$query);
		while(odbc_fetch_row($err_o))
		{
			$texto = "";
			
			// --> Fecha factura
			$fecha = trim(odbc_result($err_o,1));
			$fecha = substr($fecha,8,2)."/".substr($fecha,5,2)."/".substr($fecha,0,4);
			$texto.= $fecha.',';
			// --> Nro Factura
			$texto.= trim(odbc_result($err_o,2)).',';
			// --> Entidad o particular
			$texto.= trim(odbc_result($err_o,3)).',';
			// --> Vendedor I, A
			if($para == 'R')
				$texto.= trim(odbc_result($err_o,12)).',';
			else
				$texto.= trim(odbc_result($err_o,11)).',';
			// --> Columna 5, si el campo es igual a I entonces 5, si es igual a A entonces 2 
			if($para == 'R')
				$texto.= ((trim(odbc_result($err_o,12)) == 'I') ? '5' : '2').',';
			else
				$texto.= ((trim(odbc_result($err_o,11)) == 'I') ? '5' : '2').',';
			
			// --> Codigo responsable, si es particular va 0
			$texto.= ((trim(odbc_result($err_o,3)) == 'P') ? 0 : trim(odbc_result($err_o,4))).',';
			// --> Nombre responsable
			$texto.= trim(odbc_result($err_o,5)).',';
			// --> Valor 
			$texto.= round(trim(odbc_result($err_o,6))).',';
			// --> Codigo Concepto 
			$texto.= (trim(odbc_result($err_o,7))*1).',';
			// --> Nombre Concepto 
			$texto.= trim(odbc_result($err_o,8)).',';
			// --> Centro costos 
			$texto.= trim(odbc_result($err_o,9)).',';
			// --> Cedula 
			$texto.= trim(odbc_result($err_o,10)).',';
			// --> Nombre de paciente 
			if($para == 'R')
				$texto.= trim(odbc_result($err_o,11));
			else
				$texto.= trim(odbc_result($err_o,12));
			
			// --> Salto de linea
			$texto.= chr(13).chr(10);
			
			// --> Archivo para empresa
			if(trim(odbc_result($err_o,3)) == 'E')
				$textoEmpre.= $texto;
			// --> Archivo para particulares
			else	
				$textoParti.= $texto;
		}
		
		$feArchivoPlano = explode('/', $fecha);
		$feArchivoPlano = $feArchivoPlano[0].$feArchivoPlano[1].substr($feArchivoPlano[2],2,2);
		
		echo "<center><br><br><table border=1>";
		echo "<tr><td bgcolor=#cccccc align=center colspan='2'><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center  colspan='2' class='encabezadoTabla'>GENERACION DE ARCHIVOS DE FACTURACION IMAGENOLOGOS UNIX</td></tr>";
		
		// --> Archivo de remisiones para empresa
		if($para == 'R')
		{
			if($textoEmpre != '')
			{
				$nombreArchivoEmp 	= "R".$feArchivoPlano.$centroCostos."E.csv";
				$archivoRutaEmp		= "../../planos/".$nombreArchivoEmp; 
				$archivo 			= fopen($archivoRutaEmp,"w+");
				fwrite($archivo, $textoEmpre);
				fclose($archivo);
				
				echo "	
				<tr>
					<td class='fila1' align=center><b>Archivo remisiones, para el ".$centroCostos.", para empresa:</b><br><span style='font-size:11px'>(Este es para facturar)</span></td>
					<td class='fila2'>Click para descargar:<A href=".$archivoRutaEmp."><b>".$nombreArchivoEmp."</b></A></td>
				</tr>";
			}
			
			// --> Archivo de remisiones para particulares
			if($textoParti != '')
			{
				$nombreArchivoPar 	= "R".$feArchivoPlano.$centroCostos."P.csv";
				$archivoRutaPar		= "../../planos/".$nombreArchivoPar; 
				$archivo 			= fopen($archivoRutaPar,"w+");
				fwrite($archivo, $textoParti);
				fclose($archivo);
				
				echo "	
				<tr>
					<td class='fila1' align=center><b>Archivo remisiones, para el ".$centroCostos.", para particulares:</b><br><span style='font-size:11px'>(Este es para facturar)</span></td>
					<td class='fila2'>Click para descargar:<A href=".$archivoRutaPar."><b>".$nombreArchivoPar."</b></A></td>
				</tr>";
			}
		}
		else
		{
			if($textoEmpre != '' || $textoParti != '')
			{
				// --> Archivo de pedidos		
				$nombreArchivoPedidos 	= "P".$feArchivoPlano.$centroCostos.".csv";
				$archivoRutaPedidos	  	= "../../planos/".$nombreArchivoPedidos; 
				$archivo 				= fopen($archivoRutaPedidos,"w+");
				fwrite($archivo, $textoEmpre.$textoParti);
				fclose($archivo);
				
				echo "
				<tr>
					<td class='fila1' align=center><b>Archivo pedidos, para el ".$centroCostos.":</b><br><span style='font-size:11px'>(Este es para fines informativos o de soporte)</span></td>
					<td class='fila2'>Click para descargar:<A href=".$archivoRutaPedidos."><b>".$nombreArchivoPedidos."</b></A></td>
				</tr>
				";
			}
		}

		echo "				
			<tr><td bgcolor=#cccccc colspan='3' align='center'><A href='Imagenologos.php'>RETORNAR</A></td></tr>
		</table>";
	}
}
?>
</body>
</html>
