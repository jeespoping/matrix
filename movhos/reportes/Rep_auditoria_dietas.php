<?php
include_once("conex.php");
//=========================================================================================================================================\\
//
//						REPORTE AUDITORIA DIETAS
//
//=========================================================================================================================================\\
//FECHA CREACION:       Agosto 21 de 2012.
//AUTOR:                Jerson Andres Trujillo.                                                                                                        \\
//=========================================================================================================================================\\
//OBJETIVO: Permitir realizarle auditoria al movimiento de los pedidos de alimentacion                                                            \\
//Descripcion:                                                                                                                                                                                                                                    \\
//==========================================================================================================================================\\	
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//2018-05-28: (Jonatan) Se agrega la columna Fecha de Registro (Audfre) para que muestre el dia exacto de registro, ademas se ordena por este registro.
//2017-04-05: (Jonatan) Se agrega el estado del articulo en la funcion consultarProductos.
//2013-10-29: Se modifica la consulta principal par aque no haga relacion con las tablas root_000036 y root_000037, ya que la tabla 37 contiene
//			  el ultimo ingreso del paciente y es posible que ese numero de ingreso no coincida con la tabla de auditoria y movimiento de dietas.
//2013-05-22: Se agrega la columna productos que muestra el detalle cuando el patron es TMO-SI-DSN
//2013-05-16: Se agregan las columnas "Nutricionista" y "Observacion nutricionista"                                                         \\
//			  Se agrega blockUI cuando esta consultando
//                                                                                                                                         \\
//=========================================================================================================================================\\
//=====================================
//		INICIO DE SESSION
//=====================================
@session_start();

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
	$wactualiz="Mayo 28 de 2017";                      // ultima fecha de actualizacion               
	$wfecha	=	date("Y-m-d");   
	$whora 	= 	(string)date("H:i:s");                                                         
  
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	//====================================================================================================================================    
	// F U N C I O N E S   G E N E R A L E S
	//====================================================================================================================================
	function nombre_usuario($codigo)
	{	
		global $conex;
		$q_usuario= "SELECT  descripcion
					   FROM  usuarios
					  WHERE  codigo = '".$codigo."'
					";
		$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query:(consultar nombre de usuario) ".$q_usuario." - ".mysql_error());
		$row_usuario = mysql_fetch_array($res_usuario);
		return $row_usuario['descripcion'];
	}
	
	function consultarNombreUsuario( $wcodigo ){
	
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
	
		$q = " SELECT descripcion "
			."   FROM usuarios "
			."  WHERE codigo = '".$wcodigo."' ";
	
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
			$row = mysql_fetch_array($res);
			return $row[0];
		}
		
		return "";
	}	

	//Funcion que retorna un array con los productos designados a un paciente en una fecha-servicio-patron-piso
	function consultarProductos($wfecha, $hora_data, $whis, $wing, $wser, $wpatron, $west, $wcal ){
		
		global $wbasedato;
		global $conex;

		$and_cal = "    AND detcal = '".$wcal."' ";
		if( $wcal == -1 )
			$and_cal = "";
		//Busco si esta opcion esta grabada para el paciente en la tabla 000084
		$q = " SELECT Prodes as producto, Detcan as cantidad, detest "
			."   FROM ".$wbasedato."_000084 a, ".$wbasedato."_000082 b, ".$wbasedato."_000078 c "
			."  WHERE  detfec = '".$wfecha."'"
			."    AND a.Hora_data = '".$hora_data."'"
			."    AND dethis = '".$whis."'"
			."    AND deting = '".$wing."'"
			."    AND dethis = audhis "
			."    AND deting = auding "
			."    AND a.Fecha_data = c.Fecha_data "
			."    AND a.Hora_data = c.Hora_data "
			."    AND detser = '".$wser."'"
			."    AND detpat = '".$wpatron."'"
			.$and_cal
			."    AND detpro = procod "
			."GROUP BY procod ";

		$respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num_pro = mysql_num_rows($respro);
		$resultados = "";
		while( $rowser = mysql_fetch_assoc($respro) ){
			$resultados.= $rowser['cantidad']."-";
			$resultados.= $rowser['producto']."<br>";
			
			$estado = "Solicitado";
			
			if($rowser['detest'] == 'off'){
				$estado = "Cancelado";
			}
			
			$resultados.= "<b>(".$estado.")</b><br>";
		}
		return $resultados;   
	}
	//====================================================================================================================================
	//FIN FUNCIONES
	//====================================================================================================================================	
	
	//=========================
	// FILTROS AJAX
	//=========================
	if ( isset($consultaAjax) && isset($accion) && $accion=='VerMensajes')
	{
		$q_mensajes = 	" SELECT Menfec, Menhor, Menprg, Menmsg, descripcion, Menule, Menfle, Menhle
							FROM ".$wbasedato."_000127 as A, usuarios
					       WHERE  A.Fecha_data = '".$fecha_data."'
							 AND  Mencco  = '".$centrocostos."'
							 AND  Menser  = '".$servicio."'
							 AND  Menusu  = codigo
						   ORDER BY Menhor
						";
		$res_mensajes = mysql_query($q_mensajes,$conex) or die ("Error: ".mysql_errno()." - en el query:(mensajes chat) ".$q_mensajes." - ".mysql_error());
		$num_mensajes = mysql_num_rows($res_mensajes);
		if($num_mensajes>0)
		{
			$options="	<td colspan=12 align=center>
							<table width=90%>
								<tr align=center style='color: #676767;font-family: verdana;background-color: #E4E4E4;font-weight:bold;font-size: 10pt;'>
									<td>Fecha</td>
									<td>Hora</td>
									<td>Origen</td>
									<td>Usuario</td>
									<td>Mensaje</td>
									<td>Fecha Lectura</td>
									<td>Hora Lectura</td>
									<td>Usuario</td>
								</tr>
								";
				$color='fila1';
				while($row_mensajes=mysql_fetch_array($res_mensajes))
				{
					if($color=='fila1')
					{
						$color='fila2';
						$estilo="background-color: #C3D9FF;color: #676767;font-size: 10pt;";
					}
					else
					{
						$color='fila1';
						$estilo="background-color: #E8EEF7;color: #676767;font-size: 10pt;";
					}
					$options.=	"<tr style='".$estilo."'>
									<td>".$row_mensajes['Menfec']."</td>
									<td>".$row_mensajes['Menhor']."</td>
									<td>".$row_mensajes['Menprg']."</td>
									<td>".$row_mensajes['descripcion']."</td>
									<td>".$row_mensajes['Menmsg']."</td>
									<td>".$row_mensajes['Menfle']."</td>
									<td>".$row_mensajes['Menhle']."</td>";
									
					//Consultar el nombre del usuario que realizo la lectura
					if ($row_mensajes['Menule']!='')
					{
					$nombre_usuario = nombre_usuario($row_mensajes['Menule']);
					$options.=	"	<td>".$nombre_usuario."</td>
								</tr>
								";
					}
					else
					{
					$options.=	"	<td></td>
								</tr>
								";
					}
				}
				$options.=	"</table><br>
						</td>
					";
		}
		else
		{
			$options='  <td colspan=12 align=center> <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;width:55%;font-weight:bold;font-size: 10pt;" >
							No existen mensajes
						</div><br></td>';
		}
		
		echo $options;
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
		<title>REPORTE AUDITORIA DIETAS</title>
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<!--<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.editable.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.datapicker.js" type="text/javascript"></script>  -->
		  
		<script type="text/javascript">

			function enter()
			{
			 document.forms.mondietas.submit();
			}
			
			function cerrarVentana()
			{
			  window.close();		  
			}
			function intercalar(idElemento, fecha_data, centrocostos, servicio)
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
				var todos_Tr = document.getElementById("matriz").getElementsByTagName("tr");
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
					//val = $("#"+id_padre.id).val();
					$('#'+idElemento).load("Rep_auditoria_dietas.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion=VerMensajes&fecha_data="+fecha_data+"&centrocostos="+centrocostos+"&servicio="+servicio);
					document.getElementById(idElemento).style.display='';
				}
			} 
		</script>	
		</head>
		<body>
		<?php   
		
			//Mensaje de espera
			echo "<div id='msjEspere' style='display:none;'>";
			echo '<br>';
			echo "<img src='../../images/medical/ajax-loader5.gif'/>";
			echo "<br><br> Por favor espere un momento ... <br><br>";
			echo '</div>';
			echo "<script>";
			echo "$.blockUI({ message: $('#msjEspere') });";
			echo "</script>";
		
		encabezado("Reporte Auditoria Dietas", $wactualiz, 'clinica');
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
		echo "<center><table width='65%'>";
		//=================================
		// SELECCIONAR CENTRO DE COSTOS
		//=================================
		
		//Traigo los centros de costos
		$q = " SELECT Ccoord, Ccocod, Cconom "
			."   FROM ".$wbasedato."_000011"
			."  WHERE Ccoest = 'on' "
			."    AND ccohos  = 'on' "
			."	ORDER BY Ccoord, Ccocod ";
			
			  
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		echo "<tr class=seccion1>";
			echo "<td colspan=2 align=center><b>CENTRO DE COSTOS: </b>&nbsp;
				 <SELECT name='wcco' id='wcco' >";
			if (isset($wcco))
			{
				echo "<OPTION SELECTED>".$wcco."</OPTION>";
			} 
			echo "<option>% - TODOS</option>";
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res); 
				echo "<OPTION>".$row['Ccocod']." - ".$row['Cconom']."</OPTION>";
			}
			echo "</SELECT></td>";
		echo "</tr>";
		//=================================
		// SELECCIONAR EL SERVICIO
		//=================================
		if (isset($wser))
		{
			if($wser=='%')
				$nom_ser_selec[0]='% - TODOS';
			else
			{
			  $q1 = " SELECT sernom "
				  ."   FROM ".$wbasedato."_000076 "
				  ."  WHERE sercod = '".$wser."' ";
			  $resser1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
			  $nom_ser_selec = mysql_fetch_array($resser1);
			}
		}
		//Consultar los servicios del maestro
		$q = " SELECT sernom, serhin, serhfi, sercod "
			."   FROM ".$wbasedato."_000076 "
			."  WHERE serest = 'on' ";
		$resser = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numser = mysql_num_rows($resser);
		  
		echo "<tr class='seccion1'>";
			echo "<td align=center ><b>SERVICIO DE ALIMENTACIÓN:</b>&nbsp;
				<SELECT name='wser' id='wser'>";
			if (isset($wser))
				echo "<OPTION SELECTED value=".$wser.">".$nom_ser_selec[0]."</OPTION>";
		 
			echo "<option value='%' >% - TODOS</option>";	 
			for ($i=1;$i<=$numser;$i++)
			{
				$rowser = mysql_fetch_array($resser); 
				echo "<OPTION value=".$rowser[3].">".$rowser[0]."</OPTION>";
			} 
			echo "</SELECT></td>";
		//=================================
		// SELECCIONAR ACCION
		//=================================
		
		//Traigo los centros de costos
		$q = " SELECT Estdes, Estcod "
			."   FROM ".$wbasedato."_000129"
			."  WHERE Estest = 'on' "
			."	ORDER BY Estcod ";
			  
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
			echo "<td align=center><b>ACCIÓN:</b>&nbsp;
				 <SELECT name='wacc' id='wacc' >";
			if (isset($wacc))
			{
				echo "<OPTION SELECTED>".$wacc."</OPTION>";
			} 
			echo "<option>% - TODAS</option>";
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res); 
				echo "<OPTION>".$row['Estdes']."</OPTION>";
			}
			echo "</SELECT></td>";
		echo "</tr>";
		//=================================
		// HISTORIA
		//=================================
		echo "<tr class=seccion1>";
			echo "<td align=center><b>HISTORIA: </b>&nbsp;
					<input type='text'size=20 name='whistoria' id='whistoria' ";
			if (isset($whistoria))
			{
				echo "value='".$whistoria."' >";
			}
			else	
				echo "value='' >";	
			echo "</td>";
		//=================================
		// HABITACION
		//=================================
			echo "<td align=center><b>HABITACIÓN: </b>&nbsp;
					<input type='text'size=10 name='whabitacion' id='whabitacion' ";
			if (isset($whabitacion))
			{
				echo "value='".$whabitacion."' >";
			}
			else	
				echo "value='' >";	
			echo "</td>";
		echo "</tr>";
		//=================================
		// SELECCIONAR FECHAS A CONSULTAR
		//=================================
		echo "<tr class='seccion1'>
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
			echo "<td align=center colspan=10 bgcolor=cccccc><b><input type='submit' value='CONSULTAR'></b></td>";
		echo "</tr>";
		echo "</table><br>";
		//cerrar ventana
		echo "<table>"; 
		echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		echo "</table><br>";
		
		//====================================================================================
		//	REPORTE
		//====================================================================================
		if (isset($wcco) && isset($wser) && isset($wacc) && isset($whistoria) && isset($whabitacion) && isset($wfec_i) && isset($wfec_f) && $wfec_f>=$wfec_i ) // si ya seleccionaron los parametros para consultar
		{
		
		
			//Buscar todos los patrones que por lo generan tienen productos
			$q = "SELECT diecod as patron "
			   ."	FROM ".$wbasedato."_000041 "
			   ."  WHERE Dieind = 'on' ";
			$resp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());		
			$patrones_con_productos = array();
			while($rowp = mysql_fetch_assoc($resp)){
				array_push( $patrones_con_productos, $rowp['patron'] );
			}
		
			//--------------------------
			// Consulta de datos
			//--------------------------
			if($whistoria=='')
				$whistoria='%';
			$wcco=explode("-",$wcco);
			$wcco=trim($wcco[0]);
			if ($wacc=='% - TODAS')
				$wacc="%";
			$whabitacion=trim($whabitacion);
			if ($whabitacion==null)
				$whabitacion='%';
				
			$wser = trim($wser);
			$and_ser = " AND	Audser = '".$wser."'";
			if( $wser == "%" )
				$and_ser = "";
			
			$wacc = trim($wacc);
			$and_acc = " AND	Audacc = '".$wacc."'";
			if( $wacc == "%" )
				$and_acc = "";
				
			$wcco = trim($wcco);
			$and_cco = " AND	Movcco = '".$wcco."'";
			if( $wcco == "%" )
				$and_cco = "";
			
			$whabitacion = trim($whabitacion);
			$and_hab = " AND	movhab = '".$whabitacion."'";
			if( $whabitacion == "%" )
				$and_hab = "";
				
			$whistoria = trim($whistoria);
			$and_his = " AND	Audhis = '".$whistoria."'";
			if( $whistoria == "%" )
				$and_his = "";
				
			$q_principal = "SELECT  Audusu, Descripcion, A.Fecha_data, A.Hora_data, movcco, Cconom, movhab, movhis, movnut, movods, movdsn, moving, Sernom, Movser, audacc, auddie,  audfle, audhle, audule, Movobs, Movint, movest, Audser, Audfre
							  FROM ".$wbasedato."_000078 as A, ".$wbasedato."_000077 B, usuarios, ".$wbasedato."_000011, ".$wbasedato."_000076 
							 WHERE	A.Fecha_data between '".$wfec_i."' AND '".$wfec_f."' 
							   ".$and_ser."
							   ".$and_his."							  
							   ".$and_cco."
							   ".$and_hab."
							   ".$and_acc."
							   AND  A.Fecha_data = B.Fecha_data
							   AND  Audhis = Movhis
							   AND  Auding = Moving
							   AND  Audser = Movser
							   AND  Audcco = Movcco
							   AND  Audser = Sercod							   
							   AND  Movcco = Ccocod	
							   AND 	Audusu = Codigo 
						  ORDER BY A.Audfre DESC, A.Fecha_data DESC, A.Hora_data DESC, movcco, movhab, movser 
							";
			$res_principal = mysql_query($q_principal,$conex) or die ("Error: ".mysql_errno()." - en el query(Principal): ".$q_principal." - ".mysql_error());
			$num_principal = mysql_num_rows($res_principal);	
			//--------------------------
			// Fin Consulta de datos
			//--------------------------		
			//------------------------
			// Pinta reporte
			//------------------------
			if ($num_principal>0)
			{
				echo "<table width=100% id='matriz'>";
					//--------------------------
					// Encabezado de la tabla
					//--------------------------
					echo "<tr class='encabezadoTabla' align='center'>";
						echo "<td rowspan='2'>Usuario</td>";
						echo "<td rowspan='2'>Fecha y Hora Registro</td>";
						echo "<td rowspan='2'>Fecha Envío</td>";
						echo "<td rowspan='2'>Centro de costos</td>";
						echo "<td rowspan='2'>Habitación</td>";
						echo "<td rowspan='2'>Historia</td>";
						echo "<td rowspan='2'>Paciente</td>";
						echo "<td rowspan='2'>Servicio</td>";
						echo "<td rowspan='2'>Acción</td>";						
						echo "<td rowspan='2'>Patrones</td>";
						echo "<td rowspan='2'>Productos</td>";
						echo "<td colspan='2'>Lectura de Alertas CPA</td>";
						echo "<td rowspan='2'>Observación<br>Enfermería</td>";
						echo "<td rowspan='2'>Intolerancia</td>";
						echo "<td rowspan='2'>Mensajería</td>";
						echo "<td rowspan='2'>Observación<br>Nutricionista</td>";
						echo "<td rowspan='2'>Nutricionista</td>";
					echo "</tr>";
					echo "<tr class='encabezadoTabla' align='center'>";
						echo "<td>Fecha y Hora</td>";
						echo "<td>Usuario</td>";
					echo "</tr>";
					//--------------------------------
					// Fin Encabezado de la tabla
					//--------------------------------
					$color="Fila1";
					$consecutivo=1;
					while ($row_principal = mysql_fetch_array($res_principal))
					{
						
						$row_principal['productos'] = "";
						$pos = strrpos($row_principal['auddie'] , ",");	
						$wdetcal = -1;
						if( $row_principal['movest'] == 'off' && ($row_principal['audacc'] =='CANCELACION X ALTA' || $row_principal['audacc'] == 'CANCELACION X MUERTE') ){
								$wdetcal = 'on';									
						}									
						//TIENE PATRONES COMBINADOS
						if ($pos != ''){							
							$pats = explode(",", $row_principal['auddie'] );	
							foreach( $pats as $pos=>$patron ){
								if( in_array( $patron, $patrones_con_productos )) { //Uno de los patrones de la combinacion es de productos									
									$lista_productos = consultarProductos($row_principal['Fecha_data'], $row_principal['Hora_data'], $row_principal['movhis'], $row_principal['moving'], $row_principal['Audser'],$patron, $row_principal['movest'], $wdetcal );
									$row_principal['productos'] = $lista_productos;
								}
							}
						}else{
							if( in_array( $row_principal['auddie'], $patrones_con_productos )) { //Uno de los patrones de la combinacion es de productos
								$lista_productos = consultarProductos($row_principal['Fecha_data'], $row_principal['Hora_data'], $row_principal['movhis'], $row_principal['moving'], $row_principal['Audser'],$row_principal['auddie'],  $row_principal['movest'], $wdetcal );
								$row_principal['productos'] = $lista_productos;
							}
						}
						
						if ($color=='Fila1')
							$color='Fila2';
						else
							$color='Fila1';
							
							if( $row_principal['movdsn'] != '' )
								$row_principal['movdsn'] = "<br>(".$row_principal['movdsn'].")";
							
						    echo "<tr class='".$color."'>";
							echo "<td >".$row_principal['Audusu']." - ".$row_principal['Descripcion']."</td>";																			//Usuario
							echo "<td align='center'>".$row_principal['Audfre']."</td>";															//Fecha y Hora
							echo "<td align='center'>".$row_principal['Fecha_data']."</td>";															//Fecha y Hora
							echo "<td align='left'>".$row_principal['movcco']." - ".$row_principal['Cconom']."</td>";																	//Centro de costos
							echo "<td align='center'>".$row_principal['movhab']."</td>";																								//Habitacion
							echo "<td>".$row_principal['movhis']."-".$row_principal['moving']."</td>";																					//Historia e Ingreso
							//Datos del paciente
							$wnombre_paciente = consultarInfoPacientePorHistoria($conex, $row_principal['movhis'], $wemp_pmla);							
							echo "<td align='left'>".$wnombre_paciente->nombre1." ".$wnombre_paciente->nombre2." ".$wnombre_paciente->apellido1." ".$wnombre_paciente->apellido2."</td>";			//Paciente
							echo "<td>".$row_principal['Sernom']."</td>";																												//Servicio
							echo "<td>".$row_principal['audacc']."</td>";																												//Accion
							echo "<td align='center'>".$row_principal['auddie']."".$row_principal['movdsn']." </td>";																												//Patrones
							echo "<td align='center'>".$row_principal['productos']."</td>";																												//Patrones
							echo "<td align='center'>".$row_principal['audfle']."<br>".$row_principal['audhle']."</td>";																//Hora y Fecha de Lectura
							echo "<td>".$nombre_usuario = nombre_usuario($row_principal['audule'])."</td>";																				//Usuario Lectura
							echo "<td style='text-align:justify' ><textarea rows=2 cols=22 readonly='readonly'>".$row_principal['Movobs']."</textarea></td>";							//Observacion
							echo "<td style='text-align:justify' ><textarea rows=2 cols=22 readonly='readonly'>".$row_principal['Movint']."</textarea></td>";							//Observacion
							
							$fecha_mensaje=$row_principal['Fecha_data'];
							$servic_mensajes=$row_principal['Movser'];
							//Consultar si existen mensajes del chat, para mostrar o no la opcion de 'ver'
							$q_mensajes = 	" SELECT Menfec
												FROM ".$wbasedato."_000127 as A
											   WHERE  A.Fecha_data = '".$fecha_mensaje."'
												 AND  Mencco  = '".trim($row_principal['movcco'])."'
												 AND  Menser  = '".$servic_mensajes."'
											";
							$res_mensajes = mysql_query($q_mensajes,$conex) or die ("Error: ".mysql_errno()." - en el query:(mensajes chat) ".$q_mensajes." - ".mysql_error());
							$num_mensajes = mysql_num_rows($res_mensajes);
							if($num_mensajes>0)
							{
								echo "<td align='center' style='cursor:pointer;' onclick='intercalar(".$consecutivo.",\"$fecha_mensaje\",".trim($row_principal['movcco']).",\"$servic_mensajes\");'><b>Ver</b></td>";		//Mensajeria
							}
							else
							{
								echo "<td></td>";		
							}							
								
							if( $row_principal['movnut'] != "" ){
								$row_principal['movnut'] = consultarNombreUsuario( $row_principal['movnut'] );				
							}
							echo "<td style='text-align:justify' ><textarea rows=2 cols=22 readonly='readonly'>".$row_principal['movods']."</textarea></td>";							//Patron DSN y Observacion DSN
							echo "<td  align='left'>".$row_principal['movnut']."</td>";							//Nombre de la nutricionisa
						echo "</tr>";
						if( isset( $options ) == false )
							$options = "";
						echo "<tr id='".$consecutivo."' name='".$consecutivo."' style='display:none'>
							$options
							</tr>";
							
						$consecutivo++;
						
					}
					
				echo "</table>";
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
			if( isset( $wfec_i ) && isset( $wfec_f ) ){
				if($wfec_i>$wfec_f)
				{
					echo "<script type='text/javascript'>
					alert ('La fecha inicial NO puede ser mayor a la final');
					</script>	";
				}
			}
		}
		echo "</form>";  
		echo "<br>";
		echo "<table>"; 
		echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		echo "</table>";
		echo "<script>";
		echo "$.unblockUI();";
		echo "</script>";
	?>
	</body>
	</html>
	<?php
	}// FIN EJECUCION NORMAL DEL PROGRAMA
} // if de register
