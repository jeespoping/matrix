<?php
include_once("conex.php");
if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	/***********************************************
	*      REPORTE FACTURACION DIETAS             *
	*     		  CONEX, FREE => OK		         *
	***********************************************/            
	include_once("root/magenta.php");
	include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
	$wactualiz="(22 de marzo de 2022)"; 
	$wfecha=date("Y-m-d");   
	$whora = (string)date("H:i:s");                                                         
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
            
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//FECHA CREACION:       Julio 06 de 2012.
//AUTOR:                Jerson Andres Trujillo - Jonatan Lopez.                                                                                                        \\
//=========================================================================================================================================\\
//OBJETIVO: Consultar el costo de la alimentacion suministrada a los pacientes                                                             \\
//Descripcion: Basados en un centro de costos, servicio y rango de fechas seleccionados por el usuario, se pintan los costos de los 
//patrones (dietas) agrupados por centro de costos de los pacientes activos en ese determinado periodo, al dar click en cualquier costo
//se visualizara el detalle de ese patron, y al dar click en un centro de costos se visualizara el detalle de todos los patrones 
//de los pacientes, pertenecientes a ese centro de costos.                                                                                                                                                                                                                                    \\
//==========================================================================================================================================\\	
//ACTUALIZACIONES
//=========================================================================================================================================\\
//22 de marzo de 2022: Sebastian Alvarez Barona - Se realiza filtro de sede a los centros de costos que se encuentran en el selector
//												  por otro lado la información ofrecida una vez se consulte debera corresponder
//												  solo a la sede que se tiene seleccionada.
//=========================================================================================================================================\\
//2014-04-24: Jonatan 
//Se excluyen de la estadistica los patrones que tengan el parametros diespf en off en la tabla movhos_000041. (NVO es uno de ellos)                                                                                                                     \\
//=========================================================================================================================================\\
//2014-04-15: Jonatan
//Se muestra de nuevo el promedio de costos de los patrones, este dato ya estaba desde antes y se solicito que no se mostrara.
//2013-12-05:                                                                                                                                             \\
//Se comenta este filtro del inging para que la consulta del detalle no dependa de ese campo, ya que este muestra el último ingreso del 
//paciente y la tabla movhos_000077 no siempre tiene este ingreso igual. (Jonatan Lopez 05 Dic)                                                                                                                                       \\
//=========================================================================================================================================\\
	               
  

  //=====================================================================================================================================================================     
  // F U N C I O N E S
  //=====================================================================================================================================================================
  
	function query_principal($wcco, $wser, $query, $sCodigoSede = NULL)
	{
		global $wfec_i;
		global $wfec_f;
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

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
	
		switch($query)
		{
			case 1:
					$q= " SELECT dieord, movcco, movser, diecod, sum(movval) as valor, sum(movcan) as cantidad, diedes, Cconom, diespf "
						."  FROM ".$wbasedato."_000077, ".$wbasedato."_000041, ".$wbasedato."_000011  "
						." WHERE movfec between '".$wfec_i."' AND '".$wfec_f."' "
						."   AND movest='on' "
						."   AND movcco LIKE '%".trim($wcco)."%' "
						."   AND movser LIKE '".$wser."' "
						."   AND movpco!='' "
						."   AND movpco = diecod "
						."   AND movcco = Ccocod"
						." {$sFiltroSede} "
						." GROUP BY dieord, movcco, movser, diecod "
						." ORDER BY dieord, movcco, movser "
						."";
					break;
			case 2:
					$q= " SELECT dieord, movcco, movser, movdie, movpco, diecod, diedes, Cconom, diespf "
						."  FROM ".$wbasedato."_000077, ".$wbasedato."_000041, ".$wbasedato."_000011  "
						." WHERE movfec between '".$wfec_i."' AND '".$wfec_f."' "
						."   AND movest='on' "
						."   AND movcco LIKE '%".trim($wcco)."%' "
						."   AND movser LIKE '".$wser."' "
						."   AND movpco!='' "
						."	 AND Movpqu = 'on'"
						."   AND movpco = diecod "
						."   AND movcco = Ccocod "
						." {$sFiltroSede} "
						." ORDER BY dieord, movcco, movser "
						."";
					break;
		}
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  
		return $res;
	}
  
	function servicio_individual($pk_nom_patron)
	{
		global $wbasedato;
		global $conex;
		$q_servicio=" SELECT Dieind
					  FROM ".$wbasedato."_000041
					  WHERE Diecod='".$pk_nom_patron."'
					  AND 	Dieest='on'
					";
		$res_servicio= mysql_query($q_servicio,$conex) or die ("Error: ".mysql_errno()." - en el query (Servicio individual): ".$q_servicio." - ".mysql_error());
		$row_servicio= mysql_fetch_array($res_servicio);
		if ($row_servicio[0]=='on')
			return true;
		else
			return false;
	}
	//FUNCION QUE ME RETORNA EL NOMBRE DE UN PATRON
	function nombre_patron($valor_patr)
    {
        global $wbasedato;
        global $conex;
        $query_nom_pat="SELECT Diedes, Dieord "
                      ."FROM ".$wbasedato."_000041 "
                      ."WHERE Diecod='".$valor_patr."' ";
        $resnp = mysql_query($query_nom_pat, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_nom_pat." - ".mysql_error());
        $rownp=mysql_fetch_array($resnp);
        return $rownp; 
    }
  
	//funcion que pinta el detalle de los pacientes es decir son los datos que aparecen en la ventanas emergentes en jquery
	function mostrar_detalle($cco, $patron, $servi_consultar, $nom_cco, $patron_posq='', $wfec_i, $wfec_f)
    {
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		
		if ($servi_consultar!='%')
		{
			$q = " SELECT sernom "
			  ."   FROM ".$wbasedato."_000076 "
			  ."  WHERE sercod = '".$servi_consultar."' ";
			$resser = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numser = mysql_fetch_array($resser);
			$colspan=9;
		}
		else
		{
			$numser[0]="TODOS"; 
			$colspan=10;
		}
		
		echo "<div align='center' style='cursor:default;background:none repeat scroll 0 0; "
			."position:relative;width:100 %;height:550px;overflow:auto;'><center><br>";
			
		echo "<table  Width=100%>";
		echo "<tr class='fondoamarillo'>";
		echo "<td align=center colspan=".$colspan.">";
		echo "<b>CENTRO DE COSTOS: </b>".$nom_cco;
		
		if ($patron!='%')
			echo "<b><br>PATRON: </b>".$patron;
		else
			echo "<b><br>PATRON: </b>TODOS";

		echo "<b><br>SERVICIO: </b>".$numser[0];
		echo "</td>";
		//=============================================================================================================
		//C O N V E N C I O N E S
		//=============================================================================================================
		echo "<td colspan=4><table  Width=100% height=100% border=1 align=center class=fila1>";        
		echo "<caption class=fila2><b>CONVENCIONES</b></caption>";
		echo "<tr class=fila1><td Width=50% align=center><font size=1><b>&nbsp PEDIDO</b></font></td><td bgcolor='007FFF' align=center><font size=1><b>&nbsp MODIFICADO</b></font></td></tr>";         
		echo "<tr class=fila2><td Width=50% align=center><font size=1><b>&nbsp PEDIDO</b></font></td><td bgcolor='70DB93' align=center colspan=2><font size=1><b>&nbsp ADICION</b></font></td></tr>"; 
		echo "<tr bgcolor='E9C2A6'><td Width=50% align=center><font size=1><b>&nbsp P O S</b></font></td><td align=center bgcolor='FF7F00'><font size=1><b>&nbsp SERVICIO INDIVIDUAL</b></font></td></tr>";
		echo "</table></td>";
		//=============================================================================================================
		echo "</tr>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th rowspan=2>Fecha</th>";
		echo "<th rowspan=2>Habitacion</th>";
		echo "<th rowspan=2>Edad</th>";
		echo "<th rowspan=2>Historia</th>";
		echo "<th rowspan=2>Paciente</th>";
		echo "<th rowspan=2>Responsable</th>";
		if ($servi_consultar=='%')
			echo "<th rowspan=2>Servicio</th>";
		echo "<th rowspan=2>Patron</th>";
		echo "<th rowspan=2>Estado</th>";	
		echo "<th rowspan=2>Cantidad</th>";
		echo "<th colspan=3>DETALLE S.I</th>";
		echo "<th rowspan=2>Costo Total</th>";	  
		echo "</tr>";
		echo "<tr class='encabezadoTabla'><th>Cant.</th><th>Producto</th><th>Costo</th></tr>";
		$wclass2="fila1";
		  
		//================================
		// Consultar pacientes a detallar
		//================================
		$q_pacien="   SELECT movser, movpco, movval, movcan, movhab, movhis, moving, movdie, movpqu, pacno1, pacno2, pacap1, pacap2, pacnac, movfec, sernom, ingtip "
					."  FROM ".$wbasedato."_000077, root_000036, root_000037, ".$wbasedato."_000076, ".$wbasedato."_000016 "
					." WHERE movfec between '".$wfec_i."' AND '".$wfec_f."' "
					."   AND movest='on' "
					."   AND movcco = '".$cco."' "
					."   AND movpco !='' ";
		if	($patron_posq != '')
		{
		$q_pacien.=	 "   AND movpco LIKE '".$patron_posq."' ";
		}
		else
		{
		$q_pacien.=	 "   AND movpco LIKE '".$patron."' ";
		}
		
		$q_pacien.=	 "   AND movser LIKE '".$servi_consultar."' "
					."   AND movhis = orihis "
//					."   AND moving = oriing "  //Se comenta este filtro para que no dependa de el, ya que este muestra el ultiom ingreso del paciente y la tabla movhos_000077 no siempre tiene este ingreso registrado. (Jonatan Lopez 05 Dic)
					."   AND oriori = '".$wemp_pmla."'"  //Empresa Origen de la historia
					."   AND oritid = pactid "
					."   AND oriced = pacced "					
					."   AND Movser = Sercod "
					."	 AND Movhis = inghis"
					."	 AND Moving = inging"
					." ORDER BY movfec, movser, movhab "
					." ";		
		$respacien = mysql_query($q_pacien,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_pacien." - ".mysql_error());
		$numpacien = mysql_num_rows($respacien);
		if ($numpacien>0)
		{
			$total_costos=0;
			$total_detalles='';
			$total_cant_det='';
			$wcantidad=0;
			
			for($x=0; $x<$numpacien; $x++)
			{
				$rowpacien = mysql_fetch_array($respacien);
				$fecha_pedido = $rowpacien['movfec']; 
				$whistoria=$rowpacien['movhis']; 
				$wingreso=$rowpacien['moving'];
				$wpat_cobr=$rowpacien['movpco'];
				$wservi_nom=$rowpacien['sernom'];
				$wcosto=$rowpacien['movval'];
				$total_costos+=$wcosto;
				$wcantidad=$rowpacien['movcan'];
				$total_cant+=$wcantidad;
				$whabitacion=$rowpacien['movhab'];
				$wpac = $rowpacien['pacno1']." ".$rowpacien['pacno2']." ".$rowpacien['pacap1']." ".$rowpacien['pacap2'];    
			    $wnac = $rowpacien['pacnac'];
				$pos_quirurg = $rowpacien['movpqu'];
				//----------------
			    // Calculo la edad
				//----------------
			    $wfnac=(integer)substr($wnac,0,4)*365 +(integer)substr($wnac,5,2)*30 + (integer)substr($wnac,8,2);
			    $wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
			    $wedad=(($wfhoy - $wfnac)/365);				
			    if ($wedad < 1)
				 $wedad = number_format(($wedad*12),0,'.',',')." Meses";
				else
				 $wedad=number_format($wedad,0,'.',',')." Años";
				//Fin calculo edad
				
				//-------------------------
				// Consultar si es POS o no
				//-------------------------
				$q = " SELECT COUNT(*) "
					."  FROM ".$wbasedato."_000076 "
				    ." WHERE sertpo LIKE '%".$rowpacien['ingtip']."%'"
				    ."  AND sercod = '".$rowpacien['movser']."'"
				    ."  AND serest = 'on' ";
				$restpo = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$rowtpo=mysql_fetch_array($restpo);
				if ($rowtpo[0] > 0)
				   $wcolor_pos="E9C2A6";
				else
					$wcolor_pos="";
				//Fin consultar	
				
				//-------------------------
				// Consultar el Detalle
				//-------------------------
				$wdetalle_can[0]="";
				$wdetalle_pro[0]="";
				$wdetalle_cos[0]='';
				$wcolor_detalle="";
				$patron_con_productos='';
				// Si al paciente le programaron mas de un patron, debo mirar si entre ellos existe alguno 
				// que sea de servicio inidvidual para mostrarlo en el detalle.				
				if (strpos($rowpacien['movdie'],","))   
				{
					$wpatron=explode(",",$rowpacien['movdie']);
					foreach($wpatron as $valor_patr)	//recorro todos los patrones
					{
						if (servicio_individual($valor_patr))
							$patron_con_productos=$valor_patr;
					}
				}
				else
				{
					if (servicio_individual($rowpacien['movdie']))
						$patron_con_productos=$rowpacien['movdie'];
					
				}
				
				if ($patron_con_productos!='')
				{
					$q = " SELECT Prodes, Detcan, Detcos "
						."   FROM ".$wbasedato."_000084, ".$wbasedato."_000082 "
						."  WHERE detfec = '".$rowpacien['movfec']."'"
						."    AND dethis = '".$whistoria."'"
						."    AND deting = '".$wingreso."'"
						."    AND detser = '".$rowpacien['movser']."'"
						."    AND detpat = '".$patron_con_productos."'"
						."	  AND detcco = '".$cco."' "
						."    AND detest = 'on' "
						."    AND Procod = detpro";
					$rescbi = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$numcbi = mysql_num_rows($rescbi);
					if ($numcbi > 0)
					{
						$wcolor_detalle="FF7F00";  //Naranja
						for ($k=0;$k<$numcbi;$k++)
						{
							$rowcbi=mysql_fetch_array($rescbi);
							$total_cant_det+=$wdetalle_can[$k]=$rowcbi[1];
							$wdetalle_pro[$k]=$rowcbi[0];
							$wdetalle_cos[$k]=number_format($rowcbi[2],0,'.',',');
							$total_detalles+=$rowcbi[2];
						}
					}
					
					if($wpat_cobr!=$patron_con_productos)
					{
						if ($pos_quirurg!='on')
							$wpat_cobr=$wpat_cobr."-".$patron_con_productos;
						else
							$wpat_cobr=$rowpacien['movdie'];
					}
				}
				//Fin detalle
				
				//--------------------------------
				// Consultar el estado del patron
				//--------------------------------
				//Busco la ultima accion en la tabla de auditoria
				$q = "   SELECT MAX(hora_data), audacc "
		              ."   FROM ".$wbasedato."_000078 "
		              ."  WHERE audhis     = '".$whistoria."'"
		              ."    AND auding     = '".$wingreso."'"
		              ."    AND audser     = '".$rowpacien['movser']."'"
		              ."    AND fecha_data = '".$rowpacien['movfec']."'"
		              ."  GROUP BY 2 "
		              ."  ORDER BY 1 DESC ";
				$resaud = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$numaud = mysql_num_rows($resaud);
				$rowaud = mysql_fetch_array($resaud);
				$wcolor_acc="";
				if ($numaud > 0)
			        {
						if ($rowaud[1]=='ADICION')
							$wcolor_acc="70DB93";
						if(substr_count($rowaud[1], 'MODIFIC')>0)
							$wcolor_acc="007FFF";
					}
				//fin estado
				
				//-------------------------
				// Consultar Responsable
				//-------------------------
				$q_responsable = " SELECT Ingnre "
							  ."     FROM ".$wbasedato."_000016 "
							  ."    WHERE Inghis = '".$whistoria."'"
							  ."      AND Inging = '".$wingreso."'";
				$res_responsable = mysql_query($q_responsable,$conex) or die ("Error: ".mysql_errno()." - en el query (Consultar responsable): ".$q_responsable." - ".mysql_error());
				$row_responsable = mysql_fetch_array($res_responsable);
				$responsable=$row_responsable[0];	  
					  
				//-------------------------
				// Pintar
				//-------------------------	
				
				if ($pos_quirurg=='on' && $patron_con_productos=='')//$pos_quirurg=='on'
				{
					$wrecorrer = explode(",",$rowpacien['movdie']);
				}
				else
				{
					$wrecorrer[0]=$wpat_cobr;
				}
				foreach($wrecorrer as $patron_a_pintar )
				{
					if($patron_a_pintar!=$wpat_cobr)
					{
						//echo 'entro-'.$patron_a_pintar.'-'.$wpac;//$wpat_cobr=$patron_a_pintar;
						$wcosto_pintar=0;						
					}
					else
					{
						$wcosto_pintar=$wcosto;						
					}
					
					$rowspan=count($wdetalle_can);
					
					if ($wclass2=="fila1")
						$wclass2="fila2";
					else
						$wclass2="fila1";
						
					echo "<tr class='".$wclass2."'>";
					echo "<td rowspan='".$rowspan."' align=center >".$fecha_pedido."</td>";												//** FECHA PEDIDO
					echo "<td rowspan='".$rowspan."' align=center bgcolor=".$wcolor_pos.">".$whabitacion."</td>";						//** HABITACION
					echo "<td rowspan='".$rowspan."' align=center>".htmlentities($wedad)."</td>";                                       //** Edad
					echo "<td rowspan='".$rowspan."' align=center>".$whistoria."-".$wingreso."</td>";                                                 //** Historia
					echo "<td align=left rowspan='".$rowspan."' align=center>".$wpac."</td>";                                           //** Paciente
					echo "<td rowspan='".$rowspan."' align=center >".utf8_encode($responsable)."</td>";                            					//** responsable
					if ($servi_consultar=='%')
						echo "<td rowspan='".$rowspan."' align=center>".$wservi_nom."</td>";                                            //** Nombre del servicio 
					echo "<td rowspan='".$rowspan."' align=center>".$patron_a_pintar."</td>";                                                 //** Patrones
					echo "<td rowspan='".$rowspan."' align=center bgcolor=".$wcolor_acc.">".$rowaud[1]."</td>";							//** Acciones
					echo "<td rowspan='".$rowspan."'  align=center>".$wcantidad."</td>";                                                //** Cantidad
					echo "<td align=center bgcolor=".$wcolor_detalle.">".$wdetalle_can[0]."</td>";  									//** Cantidad del Detalle
					echo "<td align=center bgcolor=".$wcolor_detalle.">".utf8_encode($wdetalle_pro[0])."</td>";  									//** Nombre Del Detalle											//** DETALLE 
					echo "<td align=center bgcolor=".$wcolor_detalle.">".$wdetalle_cos[0]."</td>";  									//** Costo  Del Detalle
					echo "<td rowspan='".$rowspan."' align=center >";
						echo number_format($wcosto_pintar,0,'.',',');
						//if($pos_quirurg=='on')
							//echo "<font size=4><b>*</font>";
					echo "</td>";                          																				//** COSTO TOTAL
					echo "</tr>";
					
					//pintar varias filas dentro de la misma casilla correspondiente al detalle
					if ($rowspan>1)	//si hay detalle
					{
						for ($zz=1;$zz<$rowspan;$zz++)
						{
							echo "<tr class='".$wclass2."'>";
							echo "<td align=center bgcolor=".$wcolor_detalle.">".$wdetalle_can[$zz]."</td>";
							echo "<td align=center bgcolor=".$wcolor_detalle.">".$wdetalle_pro[$zz]."</td>";
							echo "<td align=center bgcolor=".$wcolor_detalle.">".$wdetalle_cos[$zz]."</td>";
							echo "</tr>";
						}
					}
					unset($wdetalle_can);
					unset($wdetalle_pro);
					unset($wdetalle_cos);
					unset($wrecorrer);
				}
			}
			
			if ($servi_consultar=='%')
				$colspan=6;
			else
				$colspan=5;
			echo "<tr>";
			echo "<td colspan='".$colspan."'></td>";
			echo "<td colspan=3 align='center' class='encabezadoTabla'>TOTAL</td>";
			echo "<td align='center' class='encabezadoTabla'>".$total_cant."</td>";
			echo "<td align='center' class='encabezadoTabla'>".$total_cant_det."</td>";
			echo "<td class='encabezadoTabla'></td>";
			if($total_detalles!='')
				$total_detalles=number_format($total_detalles,0,'.',',');
			echo "<td align='center' class='encabezadoTabla'>".$total_detalles."</td>";
			echo "<td align='center' class='encabezadoTabla'>".number_format($total_costos,0,'.',',')."</td>";
		  
		}
		echo "</table><br>";
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' $.unblockUI();' style='width:100'><br><br>";
		echo "</center></div>";
    }
   
  //====================================================================================================================================
  //FIN FUNCIONES
  //====================================================================================================================================
  //====================================================================================================================================
  //	FUNCIONES LLAMADAS POR JQUERY
  //====================================================================================================================================
	if(isset($consultaAjax) && isset($accion))
	{
		switch ($accion)
		{
			case 'mostrar_detalle':
			{
				mostrar_detalle($cco, $patron, $servi_consultar, $nombre_cco, $patron_posq='', $wfec_i, $wfec_f);
				break;
			}
						
		}
		return;
	}
  //====================================================================================================================================
  //	FIN FUNCIONES LLAMADAS POR JQUERY
  //====================================================================================================================================
  //====================================================================================================================================
  //	EJECUCION NORMAL DEL PROGRAMA
  //====================================================================================================================================
	else{
?>
<html>
<head>
  <title>REPORTE FACTURACION DIETAS</title>
</head>
<body>
<script type="text/javascript" src="../../../include/movhos/mensajeriaDietas.js"></script>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript">

    function enter()
	{
	 document.forms.mondietas.submit();
	}
	
	function cerrarVentana()
	{
      window.close();		  
    }
    
    //ventanana emergente
    function fnMostrar(td, cco, patron, servi_consultar, nombre_cco, patron_posq, wfec_i, wfec_f)
    {
        $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >', 
						css: 	{
									width: 	'auto',
									height: 'auto'                                    
								}
				 });
				 
		$.post("Rep_facturacion_dietas.php",
				{
					consultaAjax:   	'',
					accion:   			'mostrar_detalle',
					wemp_pmla:      	$('#wemp_pmla').val(),
					cco:           		cco,
					patron:           	patron,
					servi_consultar:    servi_consultar,
					nombre_cco:         nombre_cco,
					patron_posq:        patron_posq,
					wfec_i:				wfec_i,
					wfec_f:				wfec_f
				}
				,function(data) {
					$.blockUI({ message: data, 
							css: {  left: 	'5%', 
								    top: 	'10%',
								    width: 	'90%',
                                    height: 'auto'                                    
								 } 
					  });
				}
			);
	}
    
    //mostrar los title
    $(document).ready(function()
	 {
		var cont1 = 1;
	    while(document.getElementById("wdie"+cont1))
	      {
	    	 $('#wdie'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	    	 $('#wcol'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 			 
	    	 cont1++;
          }; 
          
        var cont1 = 1;
		
		var cont1_aux = 1;
	    while(document.getElementById("wdie_cos"+cont1_aux))
	      {
		  
	    	 $('#wdie_cos'+cont1_aux).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 $('#wdie_can'+cont1_aux).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 $('#wdie_prom'+cont1_aux).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 $('#wdie_prom1'+cont1_aux).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 // $('#wcco_tooltip_can'+cont1_aux).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 // $('#wcco_tooltip_costo'+cont1_aux).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 // $('#wcco_tooltip_prom'+cont1_aux).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			 
	    	 cont1_aux++;
          };
		
		//En este segmento se controlan los tooltip de cada cajon.
		var cont1_aux1 = 1;
		
		while(document.getElementById("nombre_patron"+cont1_aux1)){
		
		var cont1_aux2 = 1;
		
			while(document.getElementById("wcco_tooltip_can"+document.getElementById("nombre_patron"+cont1_aux1).value+cont1_aux2))	      {
				
				
				 $('#wcco_tooltip_can'+document.getElementById("nombre_patron"+cont1_aux1).value+cont1_aux2).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 $('#wcco_tooltip_costo'+document.getElementById("nombre_patron"+cont1_aux1).value+cont1_aux2).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 $('#wcco_tooltip_prom'+document.getElementById("nombre_patron"+cont1_aux1).value+cont1_aux2).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 
				 cont1_aux2++;
			  }
		cont1_aux1++;
		};
		
		
		var selectorSede = document.getElementById("selectsede");
	
		if(selectorSede !== null)
		{
			selectorSede.addEventListener('change', () => {
				window.location.href = "Rep_facturacion_dietas.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val();
			});
		}
		
	  });
      //fin mostrar	 

</script>	
<?php	
  //================================================================
  //	FORMA 
  //================================================================
  echo "<form name='mondietas' action='' method=post>";

  encabezado("Reporte facturación de dietas", $wactualiz, 'clinica', TRUE);

  
  echo "<input type='HIDDEN' name='wemp_pmla' id= 'wemp_pmla' value='".$wemp_pmla."'>";   
  echo "<input type='HIDDEN' name='sede' id= 'sede' value='".$wemp_pmla."'>";     
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
     
  //======================================================================================================================================
  //	ACA COMIENZA EL MAIN DEL PROGRAMA   
  //======================================================================================================================================
      
	  //=================================
	  // SELECCIONAR CENTRO DE COSTOS
	  //=================================

	  	$estadosede=consultarAliasPorAplicacion($conexion, $wemp_pmla, "filtrarSede");
		$sFiltroSede="";
		$codigoSede = '';
		if($estadosede=='on')
		{	  
			$codigoSede = (isset($selectsede)) ? $selectsede : consultarsedeFiltro();
			$sFiltroSede = (isset($codigoSede) && ($codigoSede != '')) ? " AND Ccosed = '{$codigoSede}' " : "";
		}

	  echo "<center><table>";
	  //echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
	  echo "<tr class=titulo>";
	  //Traigo los centros de costos
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ccohos  = 'on' 
		  {$sFiltroSede}";
		  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
      echo "</tr>";
      echo "<tr class=seccion1>";
      echo "<td align=center colspan=5><b>SELECCIONE EL CENTRO DE COSTOS: </b></td>";
      echo "<td align=center colspan=5><SELECT name='wcco' id='wcco' >";
	  if (isset($wcco))
	     {
	      echo "<OPTION SELECTED>".$wcco."</OPTION>";
         } 
	  echo "<option>% - TODOS</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<OPTION>".$row[0]." - ".$row[1]."</OPTION>";
	     }
      echo "</SELECT></td>";
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
      echo "<td align=center colspan=5><b>SELECCIONE EL SERVICIO DE ALIMENTACION: </b></td>";
	  echo "<td align=center colspan=5><SELECT name='wser' id='wser'>";
	  if (isset($wser))
	     echo "<OPTION SELECTED value=".$wser.">".$nom_ser_selec[0]."</OPTION>";
	 
	  echo "<option value='%' >% - TODOS</option>";	 
	  for ($i=1;$i<=$numser;$i++)
	     {
	      $rowser = mysql_fetch_array($resser); 
	      echo "<OPTION value=".$rowser[3].">".$rowser[0]."</OPTION>";
	     } 
      echo "</SELECT></td>";
      echo "</tr>";
	  //=================================
	  // SELECCIONAR FECHAS A CONSULTAR
	  //=================================
	  echo "<tr class='seccion1'><td colspan=5 align=center><b>FECHA INICIAL: </b>";  
	  //echo "<td colspan=2 align=center >";
	  if(isset($wfec_i) && isset($wfec_f))
	  {
		campoFechaDefecto("wfec_i", $wfec_i);
      }
	  else
	  {
		campoFechaDefecto("wfec_i", date("Y-m-d"));
	  }
      echo "</td>";
	  echo "<td colspan=5 align=center><b>FECHA FINAL: </b>"; 
	  //echo "<td colspan=2 align=center >";
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
	  
    if (isset($wcco) && isset($wser) && isset($wfec_i) && isset($wfec_f) && $wfec_f>=$wfec_i ) // si ya seleccionaron los parametros para consultar
	{
		$wcco=explode("-",$wcco);
		global $wfec_i;
		global $wfec_f;
		
		$res= query_principal($wcco[0], $wser, $query=1, $selectsede); //consultar informacion de la matriz principal
		$num = mysql_num_rows($res);
		$wpatrones_nopromedia = array();
		if ($num > 0)
		{
			//========================
			// Agrupar la informacion
			//========================
			for ($i=1;$i<=$num;$i++)
			{
			  $row = mysql_fetch_array($res);
			  $centro_costos=trim($row['movcco']);
			  $patron=$row['diecod'];
			  $servicio=$row['movser'];
			  $costos[$centro_costos][$patron][$servicio]=$row['valor'];
			  $cantidades[$centro_costos][$patron][$servicio]=$row['cantidad'];
			  $todos_patrones[$patron]=$row['diedes'];
			  $nom_cco[$centro_costos]=$row['Cconom'];
			  $sepromedia = $row['diespf'];
			  $cantidad_nopromedia = 0;
			  
			  //Se verifica si el patrón no se promedia, si es asi acumulará las cantidades y los valores para luego restarlos al resultado total. Jonatan 25 Abril 2014
			  if($sepromedia == 'off'){
			  
			  $costonosuma = $row['valor'];
			  $cantidad_nopromedia = $cantidad_nopromedia+$row['cantidad'];
			  
				  //se crea un array con los patrones que no se tienen en cuenta en la estadistica. Jonatan 25 Abril 2014
				  if(!array_key_exists($patron, $wpatrones_nopromedia )){
				  $wpatrones_nopromedia[$patron] = $patron;
				  }
			  
			  
			  }
			  
			  //Asigna los valores resultante a un arreglo.
			  $costos_aux[$centro_costos][$patron][$servicio] = $costonosuma;
			  $cantidades_aux[$centro_costos][$patron][$servicio]['nopromedia']=$cantidad_nopromedia;
			  
			}
					
			$patron_posquirur= array();
			//=========================================================
			// Agregar a las cantidades los Posquirurgicos que existan
			//=========================================================
			$res2= query_principal($wcco[0], $wser, $query=2, $selectsede);//dieord, movcco, movser, diecod, diedes, Cconom
			$num2 = mysql_num_rows($res2);
			if ($num2 > 0)
			{
				//========================
				// Agrupar la informacion
				//========================
				for ($ii=1;$ii<=$num2;$ii++)
				{
					$row2 			= mysql_fetch_array($res2);
					$centro_costos	= trim($row2['movcco']);
					$servicio		= $row2['movser'];
					$patron_cobrado	= $row2['movpco'];
					$patrones		= explode(",",$row2['movdie']);
					
					foreach($patrones as $valor_patr)	//recorro todos los patrones
					{
						if($valor_patr != $patron_cobrado && servicio_individual($valor_patr) == false )
						{
							$patron_posquirur[$valor_patr]=$patron_cobrado;
							
							if (isset($costos[$centro_costos][$valor_patr][$servicio]))
							{
								//$costos[$centro_costos][$valor_patr][$servicio]+=;
								$cantidades[$centro_costos][$valor_patr][$servicio]+=1;
							}
							else
							{
								$costos[$centro_costos][$valor_patr][$servicio]=0;
								$cantidades[$centro_costos][$valor_patr][$servicio]=1;
								$todos_patrones[$valor_patr]=nombre_patron($valor_patr);
								$nom_cco[$centro_costos]=$row2['Cconom'];
							}
						}
					}
				}
			}
			
			//============================
			// Pintar la matriz principal
			//============================
			echo "<br><center>";
            echo"<table width = 95%>";  
            echo "<tr class=encabezadoTabla>"; 
            echo "<td rowspan='3' align='center' width='300px' ><FONT SIZE=3><b>CENTRO DE COSTOS</b></FONT></td>";
            $rowspan=count($todos_patrones);
            echo "<td colspan='".($rowspan*3)."' align='center'><FONT SIZE=3>PATRONES</FONT></td>";
            echo "<td rowspan='3' align='center' ><FONT SIZE=2><b>TOTAL<br>CANTIDADES<b></FONT></td>";
			echo "<td rowspan='3' align='center' ><FONT SIZE=2><b>TOTAL<BR>COSTOS<b></FONT></td>";
			echo "<td rowspan='3' align='center' ><FONT SIZE=2><b>COSTOS<br>PROMEDIO<b></FONT></td>";
            echo "</tr>";
			
            echo "<tr class=encabezadoTabla>";    
            $i=1; //variable incrementadora para los tooltip
            foreach($todos_patrones as $nomb_patr => $valor )   //pintar el nombre de los patrones 
			{
				echo "<td colspan=3 align='center'><span id='wdie".$i."' title='".$valor."' ><font size=3>".$nomb_patr."</font></span></td><input type=hidden id='nombre_patron".$i."' value='".$nomb_patr."'>";                     
				$i++;
			}
            echo "</tr>";
			
			echo "<tr>";    
            foreach($todos_patrones as $valor ) //pintar encabezados que van debajo del nombre de los patrones
			{
				echo "<td class=fila2 style='border-width:3px;border-left-style:inset;'><b>Cant.<b></td>";                
				echo "<td class=fila2 style='border-width:3px;'><b>Costo Total<b></td>";
				echo "<td class=fila2 style='border-width:3px;border-right-style:outset;'><b>Prom.<b></td>";
			}   
            echo "</tr>";
			
			$wclass=="fila1";
			$k = 1;
			foreach($cantidades as $cco => $arr_patrones) //pintar el contenido, recorre por centro de costos
				{ 
                  $servi_consultar=$wser;	
				  if ($wclass=="fila1")
                   $wclass="fila2";
                  else
                     $wclass="fila1";
				
				  $nombre_cco=$nom_cco[$cco];	
                  echo "<tr width=80% class='".$wclass."'>";
                  echo "<td id='".$cco."' align='center' style='cursor:pointer;' onClick='fnMostrar(\"".$cco.$patron."\", \"".$cco."\", \"%\", \"".$servi_consultar."\", \"".$nombre_cco."\", \"".$patron_posq."\", \"".$wfec_i."\", \"".$wfec_f."\")'><b>".$nom_cco[$cco]."</b>"; //pintar nombre del centro de costos                                 
                  //mostrar_detalle($cco, '%', $servi_consultar, $nom_cco[$cco]);
				  
				  $total_cco=0;  
                  $total_cco_cant=0;
				  $total_cco_cant_aux=0;
				  $total_cco_aux = 0;
				  
                  foreach($todos_patrones as $patron => $nombre)  //se recorre las columnas de la tabla, para poder pintar los resultados en la casilla correspondiente 
                    {
                        if(array_key_exists($patron, $arr_patrones))                 
                        {
							//calcular cantidad y costos; cuando en el filtro se ha seleccionado 'todos' los servicios se hacen varios ciclos, 
							//porque cuando se selecciono un servicio especifico el foreach interno hara un solo ciclo 
							$total_cantidad=0;
							$total_costos=0;
							$total_cantidad_aux=0;
							$total_costos_aux=0;
							
							foreach($arr_patrones as $patron2 => $arr_servicios) //recorre patrones
							{
								if( $patron2==$patron)
								{
									foreach ($arr_servicios as $nom_ser =>$valor) //recorre servicios
									{
										$total_cantidad=$total_cantidad+$cantidades[$cco][$patron][$nom_ser];
										$total_costos=$total_costos+$costos[$cco][$patron][$nom_ser];
										
										$total_cantidad_aux = $total_cantidad_aux+$cantidades_aux[$cco][$patron][$nom_ser]['nopromedia'];
										$total_costos_aux=$total_costos_aux+$costos_aux[$cco][$patron][$nom_ser];
									}
								}
							}
							//fin calcular
							
							// Si el patron esta en el siguiente array es porque fue un pedido posquirurgico
							if (array_key_exists($patron, $patron_posquirur))
								$patron_posq=$patron_posquirur[$patron];
							else
								$patron_posq='';
							//fin posquirurgico
							//Pintar cantidad
							echo "<td id='".$cco.$patron."' align='center' style='cursor:pointer;border-width:3px;border-left-style:inset;' onClick='fnMostrar(\"".$cco.$patron."\", \"".$cco."\", \"".$patron."\", \"".$servi_consultar."\", \"".$nombre_cco."\", \"".$patron_posq."\", \"".$wfec_i."\", \"".$wfec_f."\")' ><span id='wcco_tooltip_can".$patron.$k."' title='".$nom_cco[$cco]."' >";
								echo $total_cantidad;
								//mostrar_detalle($cco, $patron, $servi_consultar, $nom_cco[$cco], $patron_posq);
							echo "</span></td>";
							
							//Pintar costo
							echo "<td id='".$cco.$patron."' align='center' style='cursor:pointer;border-width:3px;' onClick='fnMostrar(\"".$cco.$patron."\", \"".$cco."\", \"".$patron."\", \"".$servi_consultar."\", \"".$nombre_cco."\", \"".$patron_posq."\", \"".$wfec_i."\", \"".$wfec_f."\")'><span id='wcco_tooltip_costo".$patron.$k."' title='".$nom_cco[$cco]."' >";
								echo number_format($total_costos,0,'.',',');
								//mostrar_detalle($cco, $patron, $servi_consultar, $nom_cco[$cco], $patron_posq);
							echo "</span></td>";
							
							//Pintar promedio
							@$promedio=$total_costos/$total_cantidad;
							echo "<td id='".$cco.$patron."' align='center' style='cursor:pointer;border-width:3px;border-right-style:outset;' onClick='fnMostrar(\"".$cco.$patron."\", \"".$cco."\", \"".$patron."\", \"".$servi_consultar."\", \"".$nombre_cco."\", \"".$patron_posq."\", \"".$wfec_i."\", \"".$wfec_f."\")'><span id='wcco_tooltip_prom".$patron.$k."' title='".$nom_cco[$cco]."' >";
								echo number_format($promedio,0,'.',',');
								//mostrar_detalle($cco, $patron, $servi_consultar, $nom_cco[$cco], $patron_posq);
							echo "</span></td>";
							
							$total_cco+=$total_costos;
							$total_cco_aux+=$total_costos_aux;							
							$total_cco_cant+=$total_cantidad;
							$total_cco_cant_aux+=$total_cantidad_aux;
							
							if (!isset ($total_general_cantidad[$patron]) && !isset ($total_general_costos[$patron]))
							{
								$total_general_cantidad[$patron]=$total_cantidad;
								$total_general_costos[$patron]=$total_costos;
								$total_general_cantidad_aux[$patron]['nopromedia']=$total_cantidad_aux;
								$total_general_costos_aux[$patron]['nosuma']=$total_costos_aux;
							}   
							else
							{
								$total_general_cantidad[$patron]=$total_general_cantidad[$patron]+$total_cantidad;
								$total_general_costos[$patron]=$total_general_costos[$patron]+$total_costos;
								$total_general_cantidad_aux[$patron]['nopromedia']=$total_cantidad_aux+$total_general_cantidad_aux[$patron]['nopromedia'];
								$total_general_costos_aux[$patron]['nosuma']=$total_costos_aux+$total_general_costos_aux[$patron]['nosuma'];
							}
							
						}
						else
					    {
							echo "<td  align='center' style='border-width:3px;border-left-style:inset;'><span id='wcco_tooltip_can".$patron.$k."' title='".$nom_cco[$cco]."' > - </span></td>";
							echo "<td  align='center' style='border-width:3px;'><span id='wcco_tooltip_costo".$patron.$k."' title='".$nom_cco[$cco]."' > - </span></td>";
							echo "<td  align='center' style='border-width:3px;border-right-style:outset;'><span id='wcco_tooltip_prom".$patron.$k."' title='".$nom_cco[$cco]."' > - </span></td>";
					    }
					}
					
					$wpatrones_aux = implode(" y ",$wpatrones_nopromedia); //Patrones que no se tienen en cuenta en la estadistica. Jonatan 25 Abril 2014
					
					echo "<td align='center' ><b><span id='wdie_cos".$k."' title='No se promedio el patrón ".$wpatrones_aux."' >".number_format($total_cco_cant-$total_cco_cant_aux)."</span></b></td>";  //TOTAL CANTIDAD CENTRO DE COSTOS	
					echo "<td align='center' ><b><span id='wdie_can".$k."' title='No se tiene en cuenta el valor de ".$wpatrones_aux."' >".number_format($total_cco-$total_cco_aux)."</span></b></td>";  //TOTAL COSTOS CENTRO DE COSTOS	
					echo "<td align='center' ><b><span id='wdie_prom".$k."' title='No se tiene en cuenta el valor ni la cantidad de ".$wpatrones_aux."' >".@number_format($total_cco/($total_cco_cant-$total_cantidad_aux))."</span></b></td>"; //Total promedio
				
				$k++;
				
				}
				
			//===========================
			// Pintar totales generales
			//===========================
			echo "<tr class=encabezadoTabla>";
			echo "<td align='center'>";
			echo "TOTAL PATRONES";
			echo "</td>";

			$total_total_costos = 0;
			$total_total_cantid = 0;
			foreach($todos_patrones as $patron => $nombre)  //se recorre las columnas de la tabla, para poder pintar los resultados en la casilla correspondiente 
			{
				//$wtotal_cantidad=$wtotal_cantidad+$num_patrones1[$pk_wccocod2][$pk_nom_patron]; 								   
				if(array_key_exists($patron, $total_general_cantidad))
				{
					echo "<td  align='center' style='border-width:3px;border-left-style:inset;'> ".$total_general_cantidad[$patron]."</td>"; 
					echo "<td align='center' style='border-width:3px;'>";
					echo number_format($total_general_costos[$patron],0,'.',','); 
					echo "</td>";
					$total_total_costos+=$total_general_costos[$patron];
					$total_total_cantid+=$total_general_cantidad[$patron];
					
					$total_total_costos_aux+=$total_general_costos_aux[$patron]['nosuma'];
					$total_total_cantid_aux+=$total_general_cantidad_aux[$patron]['nopromedia'];
				}
				echo "<td  align='center' style='border-width:3px;border-right-style:outset;' >".@number_format($total_general_costos[$patron]/$total_general_cantidad[$patron])."</td>";  
			}
			echo "<td  align='center' >".number_format($total_total_cantid-$total_total_cantid_aux)."</td>";
			echo "<td  align='center' >".number_format($total_total_costos-$total_total_costos_aux)."</td>";    
			echo "<td  align='center' ><span id='wdie_prom1' title='No se tiene en cuenta el valor ni la cantidad de ".$wpatrones_aux."' >".number_format($total_total_costos/($total_total_cantid))."</td>"; 
			echo "</tr>";
			//===========================
			// Pintar Promedios
			//===========================
			echo "<tr class=encabezadoTabla>";
			echo "<td align='center'>";
			echo "PROMEDIOS";
			echo "</td>";
			foreach($todos_patrones as $patron => $nombre)  //se recorre las columnas de la tabla, para poder pintar los resultados en la casilla correspondiente 
			{							   
				if(array_key_exists($patron, $total_general_cantidad))
				{
					$promedio_patron=$total_general_cantidad[$patron]/$total_total_cantid;
					$promedio_total+=$promedio_patron;
					echo "<td  align='center' style='border-width:3px;border-left-style:inset;'> ".number_format($promedio_patron*100,2,'.',',')."%</td>"; 
					echo "<td align='center' style='border-width:3px;'>";
					$promedio_costos=$total_general_costos[$patron]/$total_total_costos;
					$promedio_total_costos+=$promedio_costos;
					echo number_format($promedio_costos*100,0,'.',','); 
					echo "%</td>";
				}
				echo "<td  align='center' style='border-width:3px;border-right-style:outset;' >".@number_format($total_general_costos[$patron]/$total_general_cantidad[$patron])."</td>";  
			}
			echo "<td  align='center' >".number_format($promedio_total*100,2,'.',',')."%</td>";
			echo "<td  align='center' >".number_format($promedio_total_costos*100,2,'.',',')."%</td>"; 
			echo "<td  align='center' >".number_format($promedio_total_costos*100,2,'.',',')."%</td>"; 			
			echo "</tr>";
			echo "</table></center>"; //cierro tabla principal
			echo "<br>";
		}
		else
		{
			echo "<BR>NO SE ENCONTRARON RESULTADOS";
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
	echo "</form>";  
    echo "<table>"; 
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
} //FIN EJECUCION NORMAL DEL PROGRAMA
} // if de register
?>