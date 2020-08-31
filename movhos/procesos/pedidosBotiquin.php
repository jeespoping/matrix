<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Lista los responsables de insumos con pedidos pendientes por entregar de cada botiquín, al hacer clic en 
// 						Ver pedido abre el botiquín de dispensación de insumos con el pedido listo para dispensar.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-06-30
//MODIFICACIONES:
// Octubre 30 de 2017	Jessica Madrid   	- Se muestra la opción Dispensar insumos sin pedido para los botiquines de ayudas diagnosticas
// Octubre 11 de 2017	Jessica Madrid   	- Se modifica en la funcion consultarPedidos() el query para que tenga en cuenta los pacientes de 
// 											  movhos_000018 y no movhos_000020 ya que deben salir los pacientes de urgencias que no tienen 
// 											  ubicación y los pacientes de ayudas diagnosticas.
// 											- Se oculta la opción Dispensar insumos sin pedido ya que  en el programa BOTIQUIN - DISPENSACION DE INSUMOS
// 											  no se permitirá dispensar insumos sin haber realizado un pedido.
// Julio 4 de 2017.				Edwin MG. 	- Cuando no hay pedidos el enlace Dispensar insumos abre en una ventana aparte y no en una pestaña.
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2017-10-30';
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
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	
	include_once("root/comun.php");
	include_once("movhos/botiquin.inc.php");
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarBotiquines()
	{
		global $conex;
		global $wbasedato;
		
		$queryBotiquines =  " SELECT Ccoori,Cconom  
								FROM ".$wbasedato."_000058 a,".$wbasedato."_000011 b
							   WHERE a.Ccoest='on' 
							     AND Ccocod=Ccoori
								 AND b.Ccoest='on' 
							GROUP BY Ccoori;";
		
		$resBotiquines = mysql_query($queryBotiquines, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryBotiquines . " - " . mysql_error());		   
		$numBotiquines = mysql_num_rows($resBotiquines);
		
		$arrayBotiquines = array();
		if($numBotiquines>0)
		{
			while($rowBotiquines = mysql_fetch_array($resBotiquines))
			{
				$arrayBotiquines[$rowBotiquines['Ccoori']] = $rowBotiquines['Cconom'];
			}
		}
		
		return $arrayBotiquines;
	}
	
	function pintarFiltroBotiquin($codBotiquin)
	{
		$botiquines = consultarBotiquines();
		
		$filtroFecha = "";
		$filtroFecha .= "	<div id='divFiltroCco' align='center'>
								<fieldset align='center' style='padding:5px;margin:5px;border: 2px solid #2a5db0;width:33%'>
									<legend style='border: 2px solid #2a5db0;border-top: 0px;font-family: Verdana;color: #ffffff;background-color: #2a5db0;font-size:8pt;font-weight:bold;'> Seleccione un botiqu&iacute;n </legend>
									<table>
										<tr>
											<td  align='center' colspan='2'>
												<select id='filtroBotiquin' name='filtroBotiquin' onChange='seleccionarBotiquin();'>
													<option>Seleccione un botiquin</option>";
													foreach($botiquines as $keyBotiquin => $valueBotiquin)
													{
		$filtroFecha .= "								<option value='".$keyBotiquin."'>".$keyBotiquin."-".$valueBotiquin."</option>";												
													}
		$filtroFecha .= "						</select>
											</td>
										</tr>
									</table>
									
								</fieldset>
							</div>
							
							<br>";
		
		echo $filtroFecha;
	}
	
	function consultarPedidos($codBotiquin)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoHce;
		
		$queryPedidos = "  SELECT Pedaux,Descripcion,Empresa,Cconom,Pedcco,Pedcod
							 FROM ".$wbasedato."_000230,".$wbasedato."_000231,".$wbasedato."_000018,usuarios,".$wbasedato."_000011
							WHERE Pedbot='".$codBotiquin."' 
							  AND Pedent='off' 
							  AND Pedest='on'
							  AND Dpecod=Pedcod
							  AND Dpeped>0
							  AND Dpeest='on'
							  AND Ubihis=Dpehis
							  AND Ubiing=Dpeing
							  AND Ubiald!='on'
							  AND Codigo=Pedaux
							  AND Activo='A'
							  AND Ccocod=Pedbot
							  AND Ccoest='on'
						 GROUP BY Pedaux
						 ORDER BY Pedcco,Pedcod,Descripcion;";
						 
			// echo "<pre>".print_r($queryPedidos,true)."</pre>";			 
						 
			
			
			
			// $queryPedidos = "  SELECT Pedaux,Descripcion,Empresa,Cconom
							 // FROM ".$wbasedato."_000230,".$wbasedato."_000231,".$wbasedato."_000020,usuarios,".$wbasedato."_000011
							// WHERE Pedbot='".$codBotiquin."' 
							  // AND Pedent='off' 
							  // AND Pedest='on'
							  // AND Dpecod=Pedcod
							  // AND Dpeped>0
							  // AND Dpeest='on'
							  // AND Habhis=Dpehis
							  // AND Habing=Dpeing
							  // AND Habcco=Pedcco
							  // AND Codigo=Pedaux
							  // AND Activo='A'
							  // AND Ccocod=Pedbot
							  // AND Ccoest='on'
							  // AND Ccoest='on'
						 // GROUP BY Pedaux	  
						 // ORDER BY Pedcco,Pedcod,Descripcion;";
		
		$resPedidos=  mysql_query($queryPedidos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryPedidos." - ".mysql_error());
		$numPedidos = mysql_num_rows($resPedidos);	
		
		$arrayPedidos = array();
		if($numPedidos > 0)
		{
			while($rowPedidos = mysql_fetch_array($resPedidos))
			{
				$arrayPedidos[$rowPedidos['Pedaux']]['codAuxiliar'] = $rowPedidos['Pedaux'];
				$arrayPedidos[$rowPedidos['Pedaux']]['nombreAuxiliar'] = $rowPedidos['Descripcion'];
				$arrayPedidos[$rowPedidos['Pedaux']]['empresa'] = $rowPedidos['Empresa'];
			}
		}
		
		return $arrayPedidos;	
	}

	function pintarPedidosBotiquin($codBotiquin)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wuse;
		
		
		$fotoAuxiliares = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fotoAuxiliares');
		
		$colspanResponsable=2;
		if($fotoAuxiliares=="on")
		{
			$colspanResponsable=3;
		}
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codBotiquin);
		
		
		
		
		
		
		$arrayPedidos = consultarPedidos($codBotiquin);
		$arrayBotiquines = consultarBotiquines();
		
		
		$html = "";
		$html .= "<div id='divPedidos' width='45%' ><br><br>";
		if(count($arrayPedidos)>0)
		{
			$dispensarSinPedido = "";
			if($ccoTipo=="ayudaDiagnostica")
			{
				$dispensarSinPedido = " <td align='right' onclick='abrirBotiquinDispensacion(\"./botiquinDispensacionInsumos.php?wemp_pmla=".$wemp_pmla."&slBotiquin=".$codBotiquin."\");'  style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>
											Dispensar insumos sin pedido
										</td>";
			}
			
			$html .= "	<table width='45%' align='center'>
							<tr>
								<td id='tdBuscarResponsable' style='font-family: verdana;font-weight:bold;font-size: 10pt;' align='left'>
									Buscar responsable:&nbsp;&nbsp;</b><input id='buscarPedido' type='text' placeholder='Nombre responsable' style='border-radius: 4px;border:1px solid #AFAFAF;'>
								</td>
								".$dispensarSinPedido."
							</tr>
							
							<table id='tablePedidos' width='45%' align='center'>
								<tr class='encabezadoTabla' align='center'>
									<td colspan='".$colspanResponsable."'>Responsable</td>
									<td rowspan='2'></td>
								</tr>
								<tr class='encabezadoTabla' align='center'>";
								if($fotoAuxiliares=="on")
								{
			$html .= "				<td>Foto</td>";								
								}
			$html .= "				<td>C&oacute;digo</td>
									<td>Nombre</td>
								</tr>
								";
								foreach($arrayPedidos as $keyPedidos => $valuePedidos)
								{
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
										
										
				$html .= "			<tr class='".$fila_lista." find' align='center'>";
										if($fotoAuxiliares=="on")
										{
											$longitud = strlen($valuePedidos['codAuxiliar']);
											$codigoAuxiliar = $valuePedidos['codAuxiliar'];
											if($longitud>5)
											{
												$codigoAuxiliar = substr($valuePedidos['codAuxiliar'],$longitud-5,$longitud);
											}
											
											$urlFotoAuxiliar = consultarFoto($conex,$wemp_pmla,$wbasedato,$codigoAuxiliar,$valuePedidos['empresa']);
											
				$html .= "					<td align='center' rowspan='".$rowspanAuxiliar."' width='65px'>
												<img class='lightbox' id='fotoAuxiliar_".$valuePedidos['codAuxiliar']."' src='".$urlFotoAuxiliar."' width=65px height=75px>
												<img class='fotoAuxiliar_".$valuePedidos['codAuxiliar']."' id='fotoAuxiliar_Apliada".$valuePedidos['codAuxiliar']."' src='".$urlFotoAuxiliar."' style='display:none'  height='700px' />
											</td>";								
										}
			$html .= "				
										<td>".$valuePedidos['codAuxiliar']."</td>
										<td>".$valuePedidos['nombreAuxiliar']."</td>
										<td onclick='abrirBotiquinDispensacion(\"./botiquinDispensacionInsumos.php?wemp_pmla=".$wemp_pmla."&slBotiquin=".$codBotiquin."&auxiliar=".$valuePedidos['codAuxiliar']."\");' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>Dispensar</td>
									</tr>";	
									
								}
			$html .= "			
							</table>
						</table>
						<br>";
			
		}
		else
		{
			$dispensarSinPedido = "";
			if($ccoTipo=="ayudaDiagnostica")
			{
				$dispensarSinPedido = " <a  href=# onclick='abrirBotiquinDispensacion(\"./botiquinDispensacionInsumos.php?wemp_pmla=".$wemp_pmla."&slBotiquin=".$codBotiquin."&auxiliar=".$valuePedidos['codAuxiliar']."\");' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>Dispensar insumos sin pedido</a>";
			}
			
			$html .= "<p align='center'>
						<b>El botiqu&iacuten no tiene pedidos pendientes por entregar.</b>
						<br><br>
						".$dispensarSinPedido."
					 </p>";
		}
		
		$html .= "";
		$html .= "</div>";
		
		return $html;		
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
		case 'pintarPedidosBotiquin':
		{	
			$data = pintarPedidosBotiquin($codBotiquin);
			$data = utf8_encode($data);
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
	  <title>DISPENSAR PEDIDOS</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		
		
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
	//Actualizar cada dos minutos
	setInterval(function()
	{
		seleccionarBotiquin();
	}, 120000);
	
	
	function seleccionarBotiquin()
	{
		
		$.post("pedidosBotiquin.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarPedidosBotiquin',
			codBotiquin		: $('#filtroBotiquin').val(),
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {
			
			$("#divPedidosBotiquin").html(data);
			
			$('#buscarPedido').quicksearch('#tablePedidos .find');
			
			//Permite que la foto de la auxiliar se vea grande.
			$('.lightbox').click(function() {
				
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');
											
				$.blockUI({ 
					message: $('#'+id), 
					css: { 
						top:  ($(window).height() - 700) /2 + 'px', 
						left: ($(window).width() - 700) /2 + 'px', 
						width: 'auto'
					} 
				}); 
				
				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});
			
		},'json');
		
	}
	
	function abrirBotiquinDispensacion(ruta)			
	{				
		window.open(ruta,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');			
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
	encabezado("DISPENSAR PEDIDOS", $wactualiz, 'clinica');
	
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	
	
	$codBotiquin = "";
	
	$filtroBotiquin = pintarFiltroBotiquin($codBotiquin);
	echo $filtroBotiquin;
	
	
	echo "<div id='divPedidosBotiquin'></div>";
	echo "	<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";
	
	
	?>
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
