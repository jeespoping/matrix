<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Este programa permite consultar los requerimientos realizados al centro de costos 1082 - Central de 
// 						materiales y esterilización que ya fueron entregados para que el usuario pueda registrar el recibo 
// 						satisfactorio de los requerimientos.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2019-09-30
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2019-09-30';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//                
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	
	include_once("root/comun.php");

	$wbasedato = "root";
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");

	$tipoRequerimiento = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ReqCentralEsterilizacion');


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarCentroCostos()
	{
		global $conex;
		global $wbasedato;
			
		$query = "SELECT Reqccs 
					FROM root_000040
				   WHERE Reqcco='(01)1082'
				     AND Reqccs!='' 
					 AND Reqccs NOT LIKE '%NO%'
				GROUP BY Reqccs;";
			
		// $query = "SELECT Reqccs 
					// FROM root_000040
				   // WHERE Reqcco='(01)1082'
				     // AND Reqccs!='' 
				     // AND Reqsat!='on' 
					 // AND Reqccs NOT LIKE '%NO%'
				// GROUP BY Reqccs;";
					 
		$resultado = mysql_query($query,$conex);
		$cantCcos = mysql_num_rows($resultado);
		
		$centros = array();
		while ($row = mysql_fetch_array($resultado)) 
		{
			$empcco=explode(')',$row['Reqccs']);

			$emp=substr($empcco[0], 1, strlen($empcco[0]));
			
			$queryCco = " SELECT Cconom 
							FROM costosyp_000005 
						   WHERE Ccoemp='".$emp."' 
							 AND Ccocod='".$empcco[1]."';";
												 
			$resultadoCco = mysql_query($queryCco,$conex);
			$cantCco = mysql_num_rows($resultadoCco);
			
			if($cantCco>0)
			{
				$rowCco = mysql_fetch_array($resultadoCco);
				$centros[$row['Reqccs']]=$row['Reqccs']."-".$rowCco['Cconom'];
			}
		}

		return $centros;
	}

	function consultarCcoUsuario($usuario)
	{
		global $conex;
		global $wbasedato;
		
		$query = "SELECT Usucco
					FROM ".$wbasedato."_000039
				   WHERE Usucod = '".$usuario."'
					 AND Usuest = 'on';";
					 
		$resultado = mysql_query($query,$conex);
		$num = mysql_num_rows($resultado);
		
		$ccoUsuario = "";
		if($num>0)
		{
			$row = mysql_fetch_array($resultado);
			
			$cco =  explode("-",$row['Usucco']);
			$ccoUsuario = $cco[0];
		}
		
		return $ccoUsuario;
	}

	function pintarFiltroCco()
	{
		global $wuse;
		
		$ccos = consultarCentroCostos();
		$ccoUsuario = consultarCcoUsuario($wuse);
		
		$html = "";
		foreach($ccos as $cco => $valueCco)
		{
			$ccoSeleccionado = "";
			if($ccoUsuario==$cco)
			{
				$ccoSeleccionado = "selected";
			}
			$html .= "<option value='".$cco."' ".$ccoSeleccionado.">".$valueCco."</option>";					
		}
		
		return $html;
	}
	
	function consultarClaseRequerimiento($tipoRequerimiento)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		
		$queryClaseReq = "SELECT Clacod,Clades 
							FROM root_000043,root_000044 
							WHERE Clacod=Rctcla 
							  AND Rctest='on' 
							  AND Rcttip='".$tipoRequerimiento."';";
		
		$resultadoClaseReq = mysql_query($queryClaseReq,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryClaseReq." - ".mysql_error());
		$num_rows = mysql_num_rows($resultadoClaseReq);
		
		$ClasesRequerimiento = array();
		if($num_rows>0)
		{
			while ($rowClases = mysql_fetch_array($resultadoClaseReq)) 
			{
				$ClasesRequerimiento[$rowClases['Clacod']] = $rowClases['Clades'];
			}
		}
		
		return $ClasesRequerimiento;
	}
	
	function pintarFiltroRequerimiento()
	{
		global $conex;
		global $wemp_pmla;
		global $wuse;
		global $tipoRequerimiento;
		
		$ClasesRequerimiento = consultarClaseRequerimiento($tipoRequerimiento);// Mostrar las clases de requerimiento segun el tipo
	
		$html = "";
		if(count($ClasesRequerimiento)>0)
		{
			foreach($ClasesRequerimiento as $keyClaseReq => $valueClaseReq)
			{
				$html .= "<option value='".$keyClaseReq."'>".$valueClaseReq."</option>";
			}
		}
		
		return $html;
	}
	
	function consultarRequerimientos($wemp_pmla,$cco,$claseRequerimiento,$estado)
	{
		global $conex;
		global $wuse;
		global $tipoRequerimiento;
		
		$filtroClase = "";
		if($claseRequerimiento!="")
		{
			$filtroClase = "AND Reqcla='".$claseRequerimiento."'";
		}
		
		$filtroCco = "";
		if($cco!="")
		{
			$filtroCco = "AND Reqccs='".$cco."'";
		}
		
		$filtroEstado = "";
		if($estado!="")
		{
			$filtroEstado = "AND Reqsat='".$estado."'";
		}
		
		$queryRequerimientos = "SELECT Reqcco, Reqnum, Reqfec, Requso, Descripcion AS solicitante, Reqccs, Reqcla, Reqdes, Reqest,Estnom,Estcol, Reqtpn, Reqsat, r40.id AS id_req 
								  FROM root_000040 AS r40, usuarios,root_000049
								 WHERE Reqtip='".$tipoRequerimiento."' 
								   AND Reqest IN ( SELECT Estcod FROM root_000049 WHERE Estfin='on')
								   ".$filtroClase."
								   ".$filtroCco."
								   ".$filtroEstado."
								   AND Codigo = Requso
								   AND Estcod=Reqest;";
								   
		$resultado = mysql_query($queryRequerimientos,$conex);
		$num = mysql_num_rows($resultado);
		
		$arrayRequerimientos = array();
		if($num>0)
		{
			$arrayCcos = consultarCentroCostos();
			$arrayClasesReq = consultarClaseRequerimiento($tipoRequerimiento);
			while($row = mysql_fetch_array($resultado))
			{
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['numero'] = $row['Reqcco']."-".$row['Reqnum'];
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['fecha'] = $row['Reqfec'];
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['solicitante'] = strtoupper(utf8_encode($row['solicitante']));
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['unidad'] = utf8_encode($arrayCcos[$row['Reqccs']]);
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['clase'] = utf8_encode($arrayClasesReq[$row['Reqcla']]);
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['descripcion'] = utf8_encode($row['Reqdes']);
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['estado'] = utf8_encode($row['Estnom']);
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['url'] = "seguimiento.php?cco=".$row['Reqcco']."&id_req=".$row['id_req']."&req=".$row['Reqnum']."&id=".$tipoRequerimiento;
				$arrayRequerimientos[$row['Reqcco']."-".$row['Reqnum']]['colorEstado'] = $row['Estcol'];
			}
		}

		return $arrayRequerimientos;
	}
//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case 'consultarRequerimientos':
		{	
			$data = consultarRequerimientos($wemp_pmla,$cco,$claseRequerimiento,$estado);
			echo json_encode($data);
			break;
			return;
		}
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	?>
	<html>
	<head>
	  <title>Requerimientos central de esterilizaci&oacute;n</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>		
		
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	function consultarRequerimientos()
	{
		$.post("reciboRequerimientosEsterilizacion.php",
		{
			consultaAjax 		: '',
			accion				: 'consultarRequerimientos',
			wemp_pmla			: $('#wemp_pmla').val(),
			cco					: $('#cco').val(),
			claseRequerimiento	: $('#claseRequerimiento').val(),
			estado				: $('#estado').val(),
		}
		, function(datos) {
			
			$('#spanBuscador').show();
			
			
			$("#divRequerimientos").show();
			$("#listaRequerimientos").html("");
			
			fila_lista="Fila1";
			if(Object.keys(datos).length>0)
			{
				for(dato in datos)
				{
					if (fila_lista=="Fila1")
						fila_lista = "Fila2";
					else
						fila_lista = "Fila1";
					
					fila = "<tr class='"+fila_lista+" find'>"
						  +"	<td><a href='#' width='80%' onclick='abrirRequerimiento(\""+datos[dato].url+"\")'>"+datos[dato].numero+"</a></td>"
						  +"	<td>"+datos[dato].fecha+"</td>"
						  +"	<td>"+datos[dato].solicitante+"</td>"
						  +"	<td>"+datos[dato].unidad+"</td>"
						  +"	<td>"+datos[dato].clase+"</td>"
						  +"	<td>"+datos[dato].descripcion+"</td>"
						  +"	<td style='background-color:"+datos[dato].colorEstado+"'>"+datos[dato].estado+"</td>"
						  +"</tr>";
					
					$("#listaRequerimientos").append(fila);
				}
				
				$('#buscar').quicksearch('#tablaRequerimientos .find');
			}
			else
			{
				fila = "<tr class='"+fila_lista+"'>"
					  +"	<td colspan='7' align='center'>No se encontraron requerimientos con los criterios de b&uacute;squeda</td>"
					  +"</tr>";
				
				
				$("#listaRequerimientos").append(fila);
			}
		},'json');
	}
	
	function abrirRequerimiento(url)
	{
		$("#iframeRequerimiento").attr("src",url);
		
		$.blockUI({ message: $("#divAbrirRequerimiento") ,
		css: {
			cursor	: 'auto',
			width	: "90%",
			height	: "95%",
			left	: "5%",
			top		: '15px',
		} });
	}
	
	function cerrarDivIframe()
	{
		$.unblockUI();
		consultarRequerimientos();
	}
	
	function cerrarVentana()
	{
		top.close();		  
    }
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
	
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY>
	<?php
	// -->	ENCABEZADO
	encabezado("RECIBO REQUERIMIENTOS CENTRAL ESTERILIZACION", $wactualiz, 'clinica');
	
	$htmlFiltroCco = pintarFiltroCco();
	$htmlFiltroRequerimiento = pintarFiltroRequerimiento();
	
	?>
		<input type='hidden' id='wbasedato' value='<?php echo $wbasedato; ?>'>
		<input type='hidden' id='wemp_pmla' value='<?php echo $wemp_pmla; ?>'>
		
		<div id='divFiltros' align='center'>
			<table>
				<tr class='encabezadoTabla'>
					<td colspan='2' align='center'>Requerimientos enviados</td>
				</tr>
				<tr>
					<td class='fila1'>Centro de costos:</td>
					<td class='fila2'>
						<select id='cco'>
							<option value=''>Todos</option>
							<?php echo $htmlFiltroCco; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class='fila1'>Clase de requerimiento:</td>
					<td class='fila2'>
						<select id='claseRequerimiento'>
							<option value=''>Todos</option>
							<?php echo $htmlFiltroRequerimiento; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class='fila1'>Estado:</td>
					<td class='fila2'>
						<select id='estado'>
							<option value=''>Todos</option>
							<option value='off' selected>Pendientes por recibir</option>
							<option value='on'>Recibidos</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='encabezadoTabla' colspan='2' align='center'><input type='button' value='Consultar' onclick='consultarRequerimientos()'></td>
				</tr>
			</table>
		</div>
		<br>
		<br>
		<div id='divRequerimientos' style='display:none;'>
			<table id='tablaRequerimientos' align='center' width='96%'>
				<thead>
					<tr class='encabezadoTabla' align='center'>
						<td>NUMERO</td>
						<td>FECHA</td>
						<td>SOLICITANTE</td>
						<td>UNIDAD</td>
						<td>CLASE</td>
						<td>DESCRIPCION</td>
						<td>ESTADO</td>
					</tr>
				</thead>
				<span id='spanBuscador' style='font-family: verdana;font-weight:bold;font-size: 10pt;float: left;padding-left:2%;'>
					Buscar:&nbsp;&nbsp;</b><input id='buscar' type='text' placeholder='Buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
				</span>
				<tbody id='listaRequerimientos'>
				</tbody>
			</table>
		</div>
		<br>
		<br>
		<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>
		
		<div id='divAbrirRequerimiento' style='width:100%;height:100%;display:none;' >
			<iframe id='iframeRequerimiento'  scrolling='yes' src='' style='width:99.9%;height:92%;right:0px;left:0px;top:0px;padding:0px;background-color:white;overflow-y: auto;'></iframe>
			<span id='botonCerrarRequerimiento'>
				<br>
				<input type='button' value='Cerrar ventana' onclick='cerrarDivIframe();'>
				<br>
			</span>
		</div>
	</BODY>

<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
