<?php
include_once("conex.php");
//=========================================================================================================================================\\
//
//						REPORTE GESTION DE REQUERIMEINTOS
//
//=========================================================================================================================================\\
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JUNIO 4 DE 2007.                                                                                                     \\
//=========================================================================================================================================\\
//OBJETIVO: Este reporte sirve para ver los requerimientos por centro de costos y los responsables de cada requerimiento.:                                                                                                                                                                                                                                   \\
//==========================================================================================================================================\\	
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//2012-10-03:	Se implementa el nuevo estilo y la funcionalidad para ver el detalle desplegable.                                                                                                                                             \\
//           	JERSON ANDRES TRUJILLO                                                                                                                              \\
//=========================================================================================================================================\\
//=====================================
//		INICIO DE SESSION
//=====================================
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
{
	echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
	$wactualiz="(Agosto 20 de 2012)";                      // ultima fecha de actualizacion               
	$wfecha	=	date("Y-m-d");   
	$whora 	= 	(string)date("H:i:s");                                                         
  
	$wbasedato = 'root';
	//====================================================================================================================================    
	// F U N C I O N E S   G E N E R A L E S
	//====================================================================================================================================
	 
	function consultar_respnsable($tipreq, $select='')
	{
		global $conex;
		$q_resp = "   SELECT Perusu, descripcion"
					."  FROM root_000042, usuarios"
					." WHERE pertip LIKE '%".$tipreq."%' "
					."	 AND perest = 'on'"
					."   AND perusu = codigo"
					." ORDER BY 2 ";
		$options.="<option value=''>TODOS</option>";
		
		$res_resp = mysql_query($q_resp,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_resp." - ".mysql_error());
			if ((mysql_num_rows($res_resp))>0)
			{
				while ($row_resp = mysql_fetch_array ($res_resp))
				{
					if ($select!='' && $select==$row_resp['Perusu'])
						$options.="<option value='".$row_resp['Perusu']."' selected>".$row_resp['descripcion']."</option>";
					else
						$options.="<option value='".$row_resp['Perusu']."'>".$row_resp['descripcion']."</option>";
				}
			}
		return $options;
	}
	function consultar_cco($empresa, $select='defecto')
	{
		global $conex;
		
		$q = " SELECT Emptcc"
			."   FROM root_000050 "
			."  WHERE Empcod LIKE '%".$empresa."%' "
			."    AND Empest = 'on' ";	
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query(): ".$q." - ".mysql_error());
		$row_emp = mysql_fetch_array($res);
		$options ="<option value=''>TODOS</option>";
		if ($row_emp['Emptcc'] !='NO APLICA')
		{
			if($row_emp['Emptcc'] == 'costosyp_000005')
				$campo="Cconom";	
			else
				$campo="Ccodes";
			
			$q_cco = " SELECT Ccocod AS codigo, ".$campo." AS nombre "
					." FROM ".$row_emp['Emptcc'].""
					." WHERE Ccoest = 'on' "
					." ORDER BY 2 ";
					
			$res_cco = mysql_query($q_cco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cco." - ".mysql_error());
			if ((mysql_num_rows($res_cco))>0)
			{
				while ($row_cco = mysql_fetch_array ($res_cco))
				{
					if ($select!='defecto' && $select==$row_cco['codigo'])
						$options.="<option value='".$row_cco['codigo']."' selected='selected'>".$row_cco['nombre']."</option>";
					else
						$options.="<option value='".$row_cco['codigo']."'>".$row_cco['nombre']."</option>";
				}
			}
		}
		return $options;
	}
	//====================================================================================================================================
	//FIN FUNCIONES
	//====================================================================================================================================	
	
	//=========================
	// FILTROS AJAX
	//=========================
	if ( isset($consultaAjax) && isset($accion))
	{
		switch ($accion)
		{
			case 'select_cco':
							{
								echo $options= consultar_cco($empresa);
								break;
							}
			case 'select_respnsable':
							{
								echo $options2= consultar_respnsable($tipreq);
								break;
							}
		}
		return;
		
	}	
	//=========================
	//FIN FILTROS
	//=========================
	//===============================
	// EJECUCION NORMAL DEL PROGRAMA
	//===============================
	else
	{	
		?>
		<html>
		<head>
		  <title>Reporte de Gestion de Requerimientos</title>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		  
		<script type="text/javascript">
			function enter()
			{
			 document.forms.mondietas.submit();
			}
			
			function cerrarVentana()
			{
			  window.close();		  
			}
			function cargar_centros_de_costos(empresa, select)
			{
				var empresa = $("#"+empresa).val();
				empresa = empresa.split('-');
				empresa = empresa[0];
				$('#'+select).load("rep_gesreque.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&empresa="+empresa+"&accion=select_cco");
			}
			function cargar_responsables(tipreq, select)
			{	
				var tipreq = $("#"+tipreq).val();
				tipreq = tipreq.split('-');
				tipreq = tipreq[0];
				$('#'+select).load("rep_gesreque.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&tipreq="+tipreq+"&accion=select_respnsable");
			}
			function intercalar(idElemento)
			{
				var $mostrar;
				if(document.getElementById(idElemento).style.display=='')
				{
					$mostrar='no';
				}
				else
				{
					$mostrar='si';
				}
					
				//ocultar todos los mensajes que esten pintados
				var todos_Tr = document.getElementById("CuadroPrincipal").getElementsByTagName("tr");
				var num = todos_Tr.length;
				for (y=0; y<num; y++)
				{
					if ( todos_Tr[y].id != '' )
					{
						todos_Tr[y].style.display='none';
					}
				}
				 //fin ocultar
				if ($mostrar=='si')
				{
					//console.log();
					$('#'+idElemento).show();
				}
			}
		</script>	
		</head>
		<body>
		<?php         
		encabezado("Reporte de Gestion de Requerimientos", $wactualiz, 'clinica');
		//================================================================
		//	FORMA 
		//================================================================
		echo "<form name='AuditoriaDietas' action='' method=post>";
		echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";     
		if (strpos($user,"-") > 0)
			$wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
		 
		//======================================================================================================================================
		//	FILTROS DE CONSULTA PARA EL REPORTE   
		//======================================================================================================================================
		echo "
		<div align=center>
		<table width='1000px;'>";
		//=================================
		// SELECCIONAR EMPRESA
		//=================================
		$q = " SELECT Empcod, Empdes"
			."   FROM ".$wbasedato."_000050"
			."  WHERE Empest = 'on' "
			."	ORDER BY Empcod ";
			
			  
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		echo "
			<tr class='Fila1'>";
			echo "
				<td align=center style='width:50%;'><b>EMPRESA:<br> </b>&nbsp;
				 <SELECT name='wempresa' id='wempresa' onchange=' cargar_centros_de_costos(\"wempresa\", \"wcentrocostos\");' >";
			if (isset($wempresa))
			{
				echo "
					<OPTION SELECTED>".$wempresa."</OPTION>";
			} 
			echo "
				<option>%-TODOS</option>";
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res); 
				echo "
					<OPTION>".$row['Empcod']."-".trim($row['Empdes'])."</OPTION>";
			}
			echo "
				</SELECT>
				</td>";
		//=================================
		// SELECCIONAR CENTRO DE COSTOS
		//=================================
			if (isset($wcentrocostos))
			{
				$wemp = explode ('-',$wempresa);
				$wemp = $wemp[0];
				$optionsx = consultar_cco($wemp, $wcentrocostos);
			}
			else
			{
				$optionsx = consultar_cco('%');
			}
			echo "
				<td align='center' style='width:50%;'>
					<b>CENTRO DE COSTOS:</b>&nbsp;<br/>
					<SELECT name='wcentrocostos' id='wcentrocostos' style='width:300px;'>			
						<option value=''>TODOS</option>
						$optionsx
					 </SELECT>
				</td>";
		echo "
			</tr>";
		
		//===================================
		// SELECCIONAR TIPO DE REQUERIMIENTO
		//===================================
		$query_tr = " SELECT mtrcod,mtrdes "
	     	   ."       FROM ".$wbasedato."_000041 "
			   ."      WHERE Mtrest='on'"
			   ."      ORDER BY 1,2 " ;
		$res_tr = mysql_query($query_tr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_tr." - ".mysql_error());
		$num_tr = mysql_num_rows($res_tr);
		echo "<tr class='Fila1'>";
			echo "<td align=center><b>TIPO DE REQUERIMIENTO:<br> </b>&nbsp;
				 <SELECT name='tipreq' id='tipreq' onchange=' cargar_responsables(\"tipreq\", \"wresponsable\");'>";
			if (isset($tipreq))
			{
				echo "<OPTION SELECTED>".$tipreq."</OPTION>";
			} 
			echo "<option>%-TODOS</option>";
			for ($i=1;$i<=$num_tr;$i++)
			{
				$row_tr = mysql_fetch_array($res_tr); 
				echo "<OPTION>".$row_tr['mtrcod']."-".trim($row_tr['mtrdes'])."</OPTION>";
			}
			echo "</SELECT></td>";
		//=================================
		// SELECCIONAR RESPONSABLE
		//=================================
			echo "<td align='center'><b>RESPONSABLE: <br></b>&nbsp;
				 <SELECT name='wresponsable' id='wresponsable' >";
			if (isset($wresponsable))
			{
				$req = explode ('-',$tipreq);
				$req = $req[0];
				$options2x=consultar_respnsable($req, $wresponsable);
			}
			else
			{
				$options2x=consultar_respnsable('%');
			}			
			echo "	<option value=''>TODOS</option>
					$options2x
				</SELECT>
				</td>";
		echo "</tr>";
		//===================================
		// SELECCIONAR ESTADO
		//===================================
		$q_estados = " SELECT Estcod, Estnom "
	     	   ."        FROM ".$wbasedato."_000049 "
			   ."       WHERE Estest='on'"
			   ."       ORDER BY 1 " ;
		$res_estados = mysql_query($q_estados,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_estados." - ".mysql_error());
		$num_estados = mysql_num_rows($res_estados);
		echo "<tr class='Fila1'>";
			echo "<td colspan=2 align=center><b>ESTADO:</b>&nbsp;
				 <SELECT name='westado' id='westado' >";
			if (isset($westado))
			{
				echo "<OPTION SELECTED>".$westado."</OPTION>";
			} 
			echo "<option>%-TODOS</option>";
			for ($i=1;$i<=$num_estados;$i++)
			{
				$row_estados = mysql_fetch_array($res_estados); 
				echo "<OPTION>".$row_estados['Estcod']."-".trim($row_estados['Estnom'])."</OPTION>";
			}
			echo "</SELECT></td>";
		echo "</tr>";
		//=================================
		// SELECCIONAR FECHAS A CONSULTAR
		//=================================
		echo "<tr class='Fila1'>
			<td align=center><b>FECHA INICIAL: </b>";  
			if(isset($wfec_i) && isset($wfec_f))
			{
				campoFechaDefecto("wfec_i", $wfec_i);
			}
			else
			{
				campoFechaDefecto("wfec_i", date("Y-m-d"));
			}
			echo "</td>";
			echo "<td align=center><b>FECHA FINAL: </b>"; 
			if(isset($wfec_i) && isset($wfec_f))
			{
				campoFechaDefecto("wfec_f", $wfec_f);
			}
			else
			{
				campoFechaDefecto("wfec_f", date("Y-m-d"));
			}
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td align=center  colspan=2 bgcolor=cccccc><b><input type='submit' value='CONSULTAR'></b></td>";
		echo "</tr>";
		echo "</table><br>";
		echo "</form>";  
		
		//====================================================================================
		//	REPORTE
		//====================================================================================
		if (isset($wempresa) && isset($wcentrocostos) && isset($tipreq) && isset($wresponsable) && isset($westado) && isset($wfec_i) && isset($wfec_f)  && $wfec_f>=$wfec_i ) // si ya seleccionaron los parametros para consultar
		{
			$tipo_requerimiento = explode ('-', $tipreq);
			$tipo_requerimiento = $tipo_requerimiento[0];
			$westado_seleccionado = explode ('-', $westado);
			$westado_seleccionado = $westado_seleccionado[0];
			$empresa_seleccionada = explode ('-', $wempresa);
			$empresa_seleccionada = $empresa_seleccionada[0];
			
			//-----------------------
			// Consulta de datos
			//-----------------------
			$q_eliminar_temporal = "DROP TABLE IF EXISTS tem_consulta_reque".date('Ymd')." ";
			mysql_query($q_eliminar_temporal,$conex) or die ("Error: ".mysql_errno()." - en el query(eliminar temporal): ".$q_eliminar_temporal." - ".mysql_error());
			
			$q_principal = " CREATE TEMPORARY TABLE tem_consulta_reque".date('Ymd')."
							 SELECT ".$wbasedato."_000040.*, Descripcion
							   FROM ".$wbasedato."_000040, usuarios 
							  WHERE	reqfec between '".$wfec_i."' and '".$wfec_f."' 
								AND Requso = Codigo	";
			if ($wcentrocostos !='')
			{
				$q_principal.=" AND Ccostos = '".$wcentrocostos."' ";
				if ($empresa_seleccionada != '%')
				{
					$q_principal.="AND Empresa = '".$empresa_seleccionada."' ";
				}
			}
			else
			{
				if ($empresa_seleccionada != '%')
				{
					$q_principal.="AND Empresa = '".$empresa_seleccionada."' ";
				}				
			}
			if ($tipo_requerimiento != '%')
			{
				$q_principal.=" AND Reqtip = '".$tipo_requerimiento."' ";
			}
			if ($wresponsable !='')
			{
				$q_principal.=" AND Reqpurs = '".$wresponsable."' ";
			}
			if ($westado_seleccionado!='%')
			{
				$q_principal.=" AND Reqest = '".$westado_seleccionado."' ";
			}
			
			$res_principal = mysql_query($q_principal,$conex) or die ("Error: ".mysql_errno()." - en el query:(principal) ".$q_principal." - ".mysql_error());
			
			//-----------------------
			// Fin Consulta de datos
			//-----------------------
			
			//------------------------------------
			//	Consultar datos sobre la temporal
			//------------------------------------
			$q_pintar = "  SELECT B.descripcion, A.Reqpurs, A.Reqtip, C.Mtrdes, A.Reqest, D.Estnom, count(A.id) as cantidad, E.Clades  
							 FROM tem_consulta_reque".date('Ymd')." as A, usuarios as B, ".$wbasedato."_000041 as C, ".$wbasedato."_000049 as D, ".$wbasedato."_000043 as E  
							WHERE A.Reqpurs = B.Codigo
							  AND A.Reqtip  = C.Mtrcod
							  AND A.Reqest 	= D.Estcod
							  AND A.Reqcla	= E.Clacod
							GROUP BY A.Reqpurs, A.Reqtip, A.Reqest
							ORDER BY B.descripcion
						";
			$res_pintar = mysql_query($q_pintar,$conex) or die ("Error: ".mysql_errno()." - en el query:(q_pintar) ".$q_pintar." - ".mysql_error());
			$num_pintar = mysql_num_rows($res_pintar);
			
			if($num_pintar > 0)
			{
				//------------------------------------
				//	Pintar
				//------------------------------------
				echo "<br><table id='CuadroPrincipal' width=90%>
					<tr class='encabezadoTabla'>
						<td align='center'>Responsable</td>
						<td align='center'>Clase</td>
						<td align='center'>Tipo</td>
						<td align='center'>Estado</td>
						<td align='center'>Total</td>
					</tr>
					";
				$color="Fila1";
				$consecutivo=1;
				$total_estados = array();
				$total_tipos = array();
				while($row_pintar = mysql_fetch_array($res_pintar))
				{
					if ($color=="Fila1")
						$color="Fila2";
					else
						$color="Fila1";
					
					$usuario_res = $row_pintar['Reqpurs'];
					$reqtip  	 = $row_pintar['Reqtip'];
					$estado 	 = $row_pintar['Reqest'];
					$nom_estado  = $row_pintar['Estnom'];
					$nom_tipo 	 = $row_pintar['Mtrdes'];
					
					echo"
					<tr class='".$color."' style='cursor:pointer;'  onclick='intercalar(\"".$consecutivo."\");'>
						<td>".$row_pintar['descripcion']."</td>
						<td>".$row_pintar['Clades']."</td>
						<td>".$nom_tipo."</td>
						<td align='center'>".$nom_estado."</td>
						<td align='center'>".$row_pintar['cantidad']."</td>
					</tr>
					<tr id='".$consecutivo."' name='".$consecutivo."' style='Display:none'>";
					
					$q_detalle = 	" SELECT A.Reqtip, A.Reqnum, A.Fecha_data, A.Hora_data, A.Requso, A.Reqdes
										FROM tem_consulta_reque".date('Ymd')." as A
									   WHERE Reqpurs = '".$usuario_res."'
										 AND Reqtip	 = '".$reqtip."'
										 AND Reqest	 = '".$estado."'
									   Order by A.Fecha_data
									";
					$res_detalle = mysql_query($q_detalle,$conex) or die ("Error: ".mysql_errno()." - en el query:(res_detalle) ".$q_detalle." - ".mysql_error());
					$num_detalle = mysql_num_rows($res_detalle);
					if($num_detalle>0)
					{
						echo "	<td colspan=5 align=center>
									<table width=90%>
										<tr class='fondoAmarillo'>
											<td colspan=6 align='center' ><b>Detalle</b><td>
										</tr>
										<tr align=center class='encabezadoTabla' style='font-weight:bold;font-size: 10pt;'>
											<td width='10%' >Requerimiento</td>
											<td width='10%'>Fecha</td>
											<td width='10%'>Hora</td>
											<td width='10%'>Centro Costos</td>
											<td width='20%'>Usuario</td>
											<td width='40%'>Descripcion</td>
										</tr>
						";
						$color2 = 'fila1';
						while($row_detalle=mysql_fetch_array($res_detalle))
						{
							if($color2=='fila1')
							{
								$color2='fila2';
								$estilo="background-color: #C3D9FF;color: #676767;font-size: 10pt;";
							}
							else
							{
								$color2='fila1';
								$estilo="background-color: #E8EEF7;color: #676767;font-size: 10pt;";
							}
							//Consultar nombre del cc y nombre del usuario que realizo el requerimiento
								$q_usuario= "SELECT  descripcion, Ccostos, Emptcc
											   FROM  usuarios, root_000050
											  WHERE  codigo = '".$row_detalle['Requso']."'
												AND	 Empresa = Empcod
											";
								$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query:(consultar nombre de usuario) ".$q_usuario." - ".mysql_error());
								$row_usuario = mysql_fetch_array($res_usuario);
								$nom_usuario = $row_usuario['descripcion'];
								if($row_usuario['Emptcc'] != 'NO APLICA')
								{
									if($row_usuario['Emptcc'] == 'costosyp_000005')
										$campo="Cconom";	
									else
										$campo="Ccodes";
									$q_cco= "SELECT  ".$campo." as cco
											   FROM  ".$row_usuario['Emptcc']."
										      WHERE  Ccocod = '".$row_usuario['Ccostos']."'
											";
									$res_cco = mysql_query($q_cco,$conex) or die ("Error: ".mysql_errno()." - en el query:(q_cco) ".$q_cco." - ".mysql_error());
									$row_cco = mysql_fetch_array($res_cco);
									$nom_cco = $row_cco['cco'];
								}
								else
								{
									$nom_cco = 'NO APLICA';
								}
								
							//fin consultar
							echo	"<tr style='".$estilo."'>
											<td align=center>".$row_detalle['Reqtip']."-".$row_detalle['Reqnum']."</td>
											<td align=center>".$row_detalle['Fecha_data']."</td>
											<td align=center>".$row_detalle['Hora_data']."</td>
											<td>".$nom_cco."</td>
											<td>".$nom_usuario."</td>
											<td width=45% align='justify'>".utf8_encode(ucwords(strtolower($row_detalle['Reqdes'])))."</td>";
						}
						echo	"</table><br>
									</td>
						";
					}	
					echo "</tr>";
					
					$consecutivo++;
					//---------------------------------
					//	Totalizar para el consolidado
					//---------------------------------
					$total_estados[$nom_estado]+=$row_pintar['cantidad'];
					$total_tipos[$nom_tipo]+=$row_pintar['cantidad'];
					
				}
				echo "</table><br>";
				//------------------------------------
				//	Fin Pintar
				//------------------------------------
				//------------------------------------
				//	Pintar consolidado
				//------------------------------------
				echo "<table width=40%>
					<tr class='Fondoamarillo'>
						<td colspan=2 align='center'><b>Consolidados</b></td>
					</tr>
					<tr class='encabezadoTabla'>
						<td align='center' colspan='2'>Total requerimientos por estado</td>
					</tr>
					<tr class='encabezadoTabla'>
						<td align='center' width=50% >Estado</td>
						<td align='center' >Total</td>
					</tr>";
					$color_con = "Fila1";
					foreach ($total_estados as $indice => $valor)
					{
						if ($color_con == "Fila2")
							$color_con = "Fila1";
						else
							$color_con = "Fila2";
							
						echo " <tr class='".$color_con."'><td>".$indice."</td>
								<td align=center>".$valor."</td></tr>";
					}
					echo "
					<tr class='encabezadoTabla'>
						<td align='center' colspan='2'>Total requerimientos por tipo</td>
					</tr>
					<tr class='encabezadoTabla'>
						<td align='center' width=50% >Estado</td>
						<td align='center' >Total</td>
					</tr>";
					foreach ($total_tipos as $indice => $valor)
					{
						if ($color_con == "Fila2")
							$color_con = "Fila1";
						else
							$color_con = "Fila2";
							
						echo " <tr class='".$color_con."'><td>".$indice."</td>
								<td align=center>".$valor."</td></tr>";
					}
				echo"</table><br>";
				//------------------------------------
				//	Fin consolidado
				//------------------------------------
			}
			else
			{
				echo "<br><div style='color: #676767;font-family: verdana;background-color: #E4E4E4;width:55%;' >
					No se encontraron resultados.<br />Intente con otros datos de consulta.
				</div><br>";
			}
		
		}//if parametros 
		else
		{
			if($wfec_i>$wfec_f)
			{
				echo "<script type='text/javascript'>
				alert ('La fecha inicial NO puede ser mayor a la final');
				</script>	";
			}
			
		}
		// echo "</form>";  
		echo "<br>";
		echo "<table>"; 
		echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		echo "</table>
		</div >
		";
	?>
	</body>
	</html>
	<?php
	}// FIN EJECUCION NORMAL DEL PROGRAMA
} // if de register