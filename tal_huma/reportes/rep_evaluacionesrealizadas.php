<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
//include_once("root/comun.php");
if($operacion=='traeresultdos')
{
   
	include_once("../procesos/funciones_talhuma.php");
	

	
 

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	function relacionarUsuariosSeccionCco($usuariosEnSeccion, $empleados, $centrosCostos)
    {	
	
        $consolidado=array();
        foreach($usuariosEnSeccion as $keySeccion => $usuariosSeccion)
        {
            foreach($usuariosSeccion['usuarios'] as $keyUsuario=>$dato)
            {
				
                $cco = $empleados[$keyUsuario]['cco'];
				
				if($consolidado[$keySeccion][$cco]['total']=='' or !isset($consolidado[$keySeccion][$cco]['total']))
					$consolidado[$keySeccion][$cco]['total']=0;
                    
				$consolidado[$keySeccion][$cco]['total']++;
				
				if($consolidado[$keySeccion][$cco]['usuarios']=='' or !isset($consolidado[$keySeccion][$cco]['usuarios']))
				{
					$consolidado[$keySeccion][$cco]['usuarios']=$keyUsuario."|".$empleados[$keyUsuario]['nombre']."|S";
				}
				else
				{
					$consolidado[$keySeccion][$cco]['usuarios'].=",".$keyUsuario."|".$empleados[$keyUsuario]['nombre']."|S";
				}
            }
        }
        return(@$consolidado);
    }

    //funcion que busca los usuarios que faltan en registrar las secciones. organizados por centros de costos.
    function usuariosFalatantesPorSeccion($consolidado, $empleados, $secciones)
    {
        //buscamos cada usuario en el arreglo de usuarios por sección del arreglo "secciones", y de esta manera sabemos si exíste o no
        foreach($secciones as $keySeccion=>$datosSeccion)
        {
            foreach($empleados as $keyEmpleado=>$datosEmpleado)//recorro cada empleado
            {
                if(!(array_key_exists($keyEmpleado, $datosSeccion['usuarios'])))
                {
                    //echo "<br>".$keyEmpleado." no existe en: ".$keySeccion." centro costos: ".$datosEmpleado['cco'];
                    if(($consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios']=='') or !(isset($consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios'])))
                         $consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios']=$keyEmpleado."|".$datosEmpleado['nombre']."|N";
                    else
                         $consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios'].=",".$keyEmpleado."|".$datosEmpleado['nombre']."|N";
                }
            }
        }
        return $consolidado;
    }

    
    function mostrarResultados($secciones, $ccos, $consolidado)
    {
        $i=0;
        $numSecciones = sizeof($secciones);
        $numCcos = sizeof($ccos);
        $empleadosOkSeccion = array();
        $totalEmpleados = 0;
		$datosparamostrar = 0;
		
		
		$html  = "<br>";
		$html .= "<br>";
		
		$html .= "<table>";
		$html .= "<tr><td><div align='Left' class='Titulo_azul' >Resultado de la Consulta</div></td></tr>";
		$html .= "<tr><td>";
		$html .= "<div class='BordeNaranja'  style='padding: 25px;' >";
		$html .= "<table>";
		$html .=  "<tr  aling='center'>";
		$html .= "<td align='center'  class='encabezadoTabla' colspan=2>CENTRO<br>DE COSTOS</td><td class='encabezadoTabla' align='center' >TOTAL <br>EMPLEADOS</td>";
		
		foreach($secciones as $keySeccion=>$datosSeccion)
		{
			$html .=  "<td class='encabezadoTabla' width=25 align='center' style='display: none' nowrap='nowrap'>".$datosSeccion['nombre']."</td>";
		}

		$html .= "<td class='encabezadoTabla'>EVALUACIONES REALIZADAS</td><td class='encabezadoTabla'>EVALUACIONES FALTANTES</td><td class='encabezadoTabla'>CUMPLIMENTO</td><td class='encabezadoTabla'>RESUMEN</td>";
		$html .= "</tr>";

		foreach($ccos as $keycco=>$datosCco)
        {
			if($datosCco['totalEmpleados'] == 0)
			{}
			else
			{
				$datosparamostrar++;
				if(is_integer($i/2))
					$wclass='fila1';
				else
					$wclass='fila2';
				
				$html .= "<tr class=".$wclass.">
							<td align='left'>".$keycco."</td>
							<td nowrap='nowrap' align='left'>".$datosCco['nombre']."</td>
							<td align='center'>".$datosCco['totalEmpleados']."</td>";
							
				$totalEmpleados += $datosCco['totalEmpleados'];
			   
				foreach($secciones as $keySeccion=>$datosSeccion)
				{
						$empleadosOkSeccion[$keySeccion] += $consolidado[$keySeccion][$keycco]['total'];
						$faltantes=$datosCco['totalEmpleados']-$consolidado[$keySeccion][$keycco]['total'];
					   
					   if($faltantes==0)
							$html .= "<td style='display: none' align='center' onclick='mostrarDiv(\"".$keySeccion."\",\"".$keycco."\")'><img src='../../images/medical/root/CHECKMRK.ICO'></td>";
						else if($faltantes==$datosCco['totalEmpleados'])
							$html .= "<td style='display: none'  align='center' onclick='mostrarDiv(\"".$keySeccion."\",\"".$keycco."\")'><span title='falta(n): ".$faltantes." empleado(s)'><img src='../../images/medical/root/borrar.png'></span></td>";
						else
							$html .= "<td  style='display: none'  align='center' onclick='mostrarDiv(\"".$keySeccion."\",\"".$keycco."\")'><span title='falta(n): ".$faltantes." empleado(s)'><img src='../../images/medical/root/borrarAmarillo.png'></span></td>";

						$onclick ="mostrarDiv(\"".$keySeccion."\",\"".$keycco."\")";
				}
				
				$totalxcco = (($datosCco['totalEmpleados'])*1) - (($faltantes)*1);
				@$cumplimento= (($totalxcco*100)/(($datosCco['totalEmpleados'])*1));
				$cumplimento = Round($cumplimento,2);
				$html .= "<td  align='center'>".$totalxcco."</td><td align='center'>".$faltantes."</td><td align='center'>".$cumplimento."%</td><td onclick='".$onclick."' style='cursor: pointer'>ver resumen</td>";
				$html .= "</tr>";
				$i++;
			}
        }
        $html .= "<tr><td colspan=".(3 + $numSecciones).">&nbsp</td></tr>";
        $html .= "<tr class='encabezadoTabla'>";
        $html .= "<td colspan=2>TOTAL EVOLUCI&Oacute;N EVALUACI&Oacute;N - Num&eacute;rico</td>";
        $html .= "<td align='center'>".$totalEmpleados."</td>";
       
		foreach ($empleadosOkSeccion as $keySeccion=>$totales)
		{
			$html .= "<td align='center'>".$totales."</td>";
		}
		
        $html .= "</tr>";
        $html .= "<tr class='encabezadoTabla'>";
        $html .= "<td colspan=2>TOTAL EVOLUCI&Oacute;N EVALUACI&Oacute;N - Porcentual</td>";
        $html .= "<td align='center'>100%</td>";
        
		//echo "<td>".$totalEmpleados."</td>";
        foreach ($empleadosOkSeccion as $keySeccion=>$totales)
        {
            @$porcentaje = ($totales/$totalEmpleados) *100;
            $html .= "<td align='center'>".number_format($porcentaje,2,",","")."%</td>";
        }
        
		$html .= "</tr>";
        $html .= "</table>";
		$html .= "</div>";
		$html .= "</td></tr></table>";
		
		if ($datosparamostrar==0)
		{
		
			echo   "<br><br><br>
					<table>
						<tr>
							<td>
								<div align='Left' class='Titulo_azul' >Resultado de la Consulta</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class='BordeNaranja'  style='padding: 25px;' >
									<table width='900'>
										<tr>
											<td align=center>
												<div class='alerta' ><img src='../../images/medical/root/alerta.gif'  width='40' height='40'><br>No hay datos para mostrar</div>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					</table>";
		}
		else
		{
			echo $html;
		}
    }
    
	//funcion que construye los divs con el detalle de la relación de centros de costos
    function crearDivs($consolidado, $secciones)
    {
		

        foreach($consolidado as $keySeccion=>$datosSeccion)
        {
            foreach ($datosSeccion as $keyCco=>$datosCco)
            {

                $tabla='';
                $aux = 0;
                $empleados = explode(",", $datosCco['usuarios']);
                $numEmpleados = sizeof($empleados);
                $numTablas = 1;
                if($numEmpleados>30)
                    $numTablas=2;

                $sobrante = $numEmpleados % $numTablas;
                $mitad = round($numEmpleados/ $numTablas);

                $arregloEmpleados = array_chunk($empleados, $numTablas, true);
                $td_abre = "<td align='center' valign='top'	>";
                for ($j = 1; $j <= $numTablas; $j++)
                {
                    if($tabla=='')
                        {
                        $tabla = $td_abre."<center><table>";
                        }else
                            {
                                $tabla .= $td_abre."<center><table style='border-left:2px solid #999999;'>";
                            }
							
				$query=	"SELECT Ccocod , Cconom 
						   FROM costosyp_000005 
						  WHERE Ccocod = '".$keyCco."' ";
				$resquery = mysql_query($query,$conex) ;
				$row2 = mysql_fetch_array($resquery);
									
						$nombrecentro= $row2['Cconom']; 			

                    $tabla .= "<tr class='encabezadoTabla'>";
                    $tabla .= "<td colspan=6 align='center'>Detalle de Evaluaciones del Centro de costos:".$keyCco." <br> (".$nombrecentro.")</td>";
                    $tabla .= "</tr>";
                    $tabla .= "<tr class='encabezadoTabla'>";
					$tabla .= "<td>C&oacute;digo</td>";
					$tabla .= "<td>Nombre</td>";
					$tabla .= "<td>Estado</td>";
					$tabla .= "<td>Cargo</td>";
					
						
						
                    $tabla .= "</tr>";

                    if($j==$numTablas && $sobrante>0)
                        $limite = ($mitad*2)-1;
                        else
                            $limite = $mitad*$j;

                    for($i=$aux; $i < $limite; $i++)
                        {
                            if(is_integer($i/2))
                                $wclass='fila1';
                                else
                                    $wclass='fila2';

                            $empleado = explode("|", $empleados[$i]);
                            $tabla .= "<tr class='".$wclass."'>";
                                $tabla .= "<td nowrap='nowrap'>".$empleado[0]." </td>";
                                $tabla .= "<td>".$empleado[1]."</td>";
                                if($empleado[4]=='S')
                                    $tabla .= "<td align='center'><img src='../../images/medical/root/CHECKMRK.ICO'></td>";
                                 else
                                     $tabla .= "<td align='center'><img src='../../images/medical/root/borrar.png'></td>";
									 
								$tabla .= "<td>".$empleado[2]."</td>";
							
								
                            $tabla .="</tr>";
                            $aux++;
                        }
                        $tabla .= "</table></center>";
                        $tabla .= "</td>";
                    }
                $ppal = "<table><tr>$tabla</tr></table>";

                echo "<div id='div".$keySeccion."_".$keyCco."' align='center' style='display:none; cursor:default; background:none repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
                    echo $ppal;
                    echo "<center><table>";
                    echo "<tr><td>&nbsp;";
                    echo "</td></tr>";
                    echo "<tr><td>";
                    echo "<input type='button' id='btn_div".$keySeccion."_".$keyCco."' value='Cerrar' onclick='ocultarDiv(\"".$keySeccion."\",\"".$keycco."\")'>";
                    echo "</td></tr>";
                echo"</table></center>";
                echo "</div>";
            }
        }
    }

	//Declaración de variables.
    $secciones = Array();  //array que contendrá la información de cada sección .
    $centrosCostos = Array(); //array que contendrá la información de cada centro de costos(código, nombre, y cantidad total de empleados)
    $empleados = Array();  //array que contendrá la información de cada empleado(código, nombre y centro de costos al que pertenece)
    $tablasSeccion =  Array();  //array que contendrá la información de secciones con las tablas y campos que la componen, ademas de los usuarios que los han llenado.
	
   
	$secciones['01']['nombre']='RESUMEN';
	
    $filtroCco='';
    if($wcco!='*')
    {
        $filtroCco = "AND tal.idecco = '".$wcco."'";
    }
    //consulto y armo el arreglo con los centros de costos que existen en la tabla 13
    $q = "SELECT tb1.Ccocod AS codigo, tb1.Cconom AS nombre
            FROM costosyp_000005 AS tb1
           INNER JOIN ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
           WHERE tal.ideest = 'on'
                 ".$filtroCco."
           GROUP BY  tb1.Ccocod
           ORDER BY  tb1.Cconom";
    $rs = mysql_query($q, $conex);
    $numCcos = mysql_num_rows($rs);
    for($i = 1; $i <= $numCcos; $i++)
    {
        $rowCcos = mysql_fetch_array($rs);
        $centrosCostos[$rowCcos['codigo']]['nombre']=$rowCcos['nombre'];

        //consultamos la totalidad de usuarios en cada centro de costos;
        $q2 = "SELECT count(DISTINCT(Ideuse)) as empleados
                 FROM ".$wbasedato."_000013, ".$wbasedato."_000058 
                WHERE Idecco='".$rowCcos['codigo']."'
                  AND Ideest='on'  
				  AND Ideuse = Arecdo 
				  AND Areper = '".$wperiodo."' 
				  AND Areano = '".$wano."' 
				  AND Aretem = '".$wtemareportes."' ";
		/*	  
	   $q2 = "SELECT count(*) as empleados
			 FROM ".$wbasedato."_000013
			WHERE Idecco='".$rowCcos['codigo']."'
			  AND Ideest='on' " ;
		*/
			  
        $rs2 = mysql_query($q2, $conex);
        $row1=mysql_fetch_array($rs2);
        $centrosCostos[$rowCcos['codigo']]['totalEmpleados']=$row1['empleados'];
    }
	
    //consulto los usuarios organizados por centros de costos.
    $filtroCco='';
    if($wcco!='*')
    {
        $filtroCco = "AND idecco = '".$wcco."'";
    }
	
	$q2 = "SELECT count(*) as empleados
                 FROM ".$wbasedato."_000013, ".$wbasedato."_000058 
                WHERE Idecco='".$rowCcos['codigo']."'
                  AND Ideest='on'  
				  AND Ideuse = Arecdo 
				  AND Areper = '".$wperiodo."' 
				  AND Areano = '".$wano."' 
				  AND Aretem = '".$wtemareportes."'  ";
/*   
   $q = "SELECT DISTINCT ideuse as codigo, idecco as cco, ideno1, ideno2, ideap1, ideap2,Cardes
            FROM  root_000079, ".$wbasedato."_000013 
           WHERE Ideest = 'on'
		     AND Ideccg = Carcod 
           ".$filtroCco."";
		   
	  */ 
		   				  
    $q = "SELECT DISTINCT ideuse as codigo, idecco as cco, ideno1, ideno2, ideap1, ideap2,Cardes
            FROM   ".$wbasedato."_000013 , ".$wbasedato."_000058 ,root_000079
           WHERE Ideest = 'on'
		     AND Ideccg = Carcod 
			  AND Ideuse = Arecdo 
				  AND Areper = '".$wperiodo."' 
				  AND Areano = '".$wano."' 
				  AND Aretem = '".$wtemareportes."' ".$filtroCco."
				  ORDER BY  ideno1,ideno2,ideap1,ideap2 ";
				  
				  
	  
    $rs = mysql_query($q, $conex) or die(mysql_error());
    $numEmpleados = mysql_num_rows($rs);

    for($i = 1; $i<=$numEmpleados; $i++)
    {
        $rowEmpleado = mysql_fetch_array($rs);
        $empleados[$rowEmpleado['codigo']]['cco']=$rowEmpleado['cco'];
        $empleados[$rowEmpleado['codigo']]['nombre']=$rowEmpleado[2]." ".$rowEmpleado[3]." ".$rowEmpleado[4]." ".$rowEmpleado[5]."|".$rowEmpleado[6]."|".$rowEmpleado[7];
		$empleados[$rowEmpleado['codigo']]['nombre']= str_replace(',' , '  ' , $empleados[$rowEmpleado['codigo']]['nombre'] );
    }
    
	//consulto las tablas que hay que consultar por cada opcion.
	 $tablasSeccion['01']['000032']['iduser']="Mcauco";
	
	 $tablasSeccion['01']['000032']['campos']="Mcauco <> '' AND Ideest ='on' AND Mcaano= '".$wano."' AND Mcaper='".$wperiodo."' AND Mcafor = Forcod AND Fortip = '".$wtemareportes."'";
	// echo '<pre>';print_r($tablasSeccion);echo '</pre>';
    //Se consultan todos aquellos usuarios que hayan llenado algo en cada sección.
    $arrrr = array();
    foreach ($tablasSeccion as $keySeccion=>$tablas)
    {
        foreach($tablas as $keyTabla=>$campos)
        {
            $tabla13='';
            $join13='';
            $filtroCco='';
            if($wcco!='*')
            {
                $filtroCco = "AND idecco = '".$wcco."'";
            }
            if($keyTabla!="000013")
            {
                $tabla13=", ".$wbasedato."_000013, ".$wbasedato."_000002";
                $join13= "AND Ideuse = ".$tablasSeccion[$keySeccion][$keyTabla]['iduser']."";
				// aqui iria lo otro para cambiar 
            }
            $queryUsuarios = "SELECT DISTINCT ".$tablasSeccion[$keySeccion][$keyTabla]['iduser']."
                              FROM ".$wbasedato."_".$keyTabla."".$tabla13."
                             WHERE (".$tablasSeccion[$keySeccion][$keyTabla]['campos'].")
                             ".$join13."
                             ".$filtroCco."
                               AND Ideest='on' ORDER BY ideno1,ideno2,ideap1,ideap2";
			
            //echo "<br>query usuarios".$queryUsuarios;
            $rsUsuarios = mysql_query($queryUsuarios, $conex) or die(mysql_error().'AQUIIIIIIIIIIII  '.$queryUsuarios);
            $numUsuarios = mysql_num_rows($rsUsuarios);
            $secciones[$keySeccion]['usuarios']=array();
            while($rowUsuario = mysql_fetch_array($rsUsuarios))
            {

                $secciones[$keySeccion]['usuarios'][$rowUsuario[0]]='s';
                // echo $keySeccion.' '.$secciones[$keySeccion]['usuarios'][$rowUsuario[0]].' '.$rowUsuario[0].'<br/>';
            }
            $arrrr[] = $secciones;
          
        }
    }
   

    $consolidado = relacionarUsuariosSeccionCco(@$secciones, @$empleados, @$centrosCostos );
    $consolidado = usuariosFalatantesPorSeccion(@$consolidado, @$empleados, @$secciones);
    
    mostrarResultados(@$secciones, @$centrosCostos, @$consolidado);
    crearDivs(@$consolidado, @$secciones);
    echo "<center><table>";
    echo "<tr><td>&nbsp;</td></tr>";
    //echo "<tr><td align='center'><a href='rep_evaluacionesrealizadas.php?wemp_pmla=".$wemp_pmla."'>Retornar</a></td></tr>";
    echo "<tr><td>&nbsp;</td></tr>";
    // echo "<tr>";
    // echo "<td align='center'><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td>";
    // echo"</tr>";
    echo "</table></center>";
	return;
}

if ($operacion=='mostrarperiodos' )
{
	include_once("../procesos/funciones_talhuma.php");
	

	
 
	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	
	$q=  "SELECT Perano, Perper "
		."  FROM ".$wbasedato."_000009 "
		." WHERE Perfor = '".$wtemareportes."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	if($wtemareportes=='nada')
	{
		echo "<option value='nada||nada'> Seleccione un tema </option>";
	}
	else
	{
		echo "<option value='nada||nada'> Seleccione </option>";
		while($row = mysql_fetch_array($res))
		{
			echo "<option value='".$row['Perano']."||".$row['Perper']."'>".$row['Perano']."-".$row['Perper']."</option>";
		}
	}

	return;
	
}

if ($operacion=='inicioprograma')
{
	include_once("../procesos/funciones_talhuma.php");
	

	
 
	cargar_hiddens_para_autocomplete();
	return;
}

function cargar_hiddens_para_autocomplete()
{
	// --> CCOS
	global $conex;
	global $wbasedato;
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

	
	$arr_cco = array();
	
	$q_cco = " SELECT Ccocod AS codigo, Cconom AS nombre 
				 FROM costosyp_000005
				WHERE Ccoest = 'on' 	
				ORDER BY nombre ";
			
	$r_cco = mysql_query($q_cco,$conex) or die("Error en el query: ".$q_cco."<br>Tipo Error:".mysql_error());
	if(mysql_num_rows($r_cco)>0)
	{
		$arr_cco['*']	= 'Todos';
	}
	while($row_cco = mysql_fetch_array($r_cco))
	{
		$row_cco['nombre'] = str_replace($caracter_ma, $caracter_ok, $row_cco['nombre']);
		$arr_cco[trim($row_cco['codigo'])] = trim($row_cco['nombre']);
	}
	
	echo json_encode($arr_cco);
	
}
?>
<html>
<head>
<title>Evoluci&oacute;n registro de evaluaci&oacute;n</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<!--<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>-->
<script>

$(document).ready(function() {

	//alert("hola");
  $.post("../reportes/rep_evaluacionesrealizadas.php",
		{
			consultaAjax 	: '',
			inicial			: 'no',
			operacion		: 'inicioprograma',
			wemp_pmla		: $('#wemp_pmla').val(),
			wtema           : $('#wtema').val(),
			wuse			: $('#wuse').val()
		}
		, function(data) {
			
			cargar_cco (eval('(' + data + ')'));
		});

});

function cargar_cco (ArrayValores)
{
	var ccos	= new Array();
	var index		= -1;
	var tr ;
	var n = 1;
	tr ="";
	var wfc ;
	for (var cod_ccos in ArrayValores)
	{
		index++;
		ccos[index] = {};
		ccos[index].value  = cod_ccos;
		ccos[index].label  = cod_ccos+'-'+ArrayValores[cod_ccos];
		ccos[index].nombre = ArrayValores[cod_ccos];
	}
	
	$( "#buscador_cco" ).autocomplete({
		
		minLength: 	0,
		source: 	ccos,
		select: 	function( event, ui ){					
			$( "#buscador_cco" ).val(ui.item.nombre);
			$( "#buscador_cco" ).attr('valor', ui.item.value);
			return false;
		}
		
		
		
	});	
	
	$("#buscador_cco").val("Todos");
	$("#buscador_cco").attr("valor","*");
}
function mostrarDiv(seccion, cco)
{
    div="div"+seccion+"_"+cco;
    $.blockUI({ message: $("#"+div),
                            css: { left: '1%',
                                    top: '1%',
                                  width: '100%',
                                  height: '100%'
                                 }
                      });

}
function ocultarDiv(seccion, cco)
{
    $.unblockUI();
}

function traeresultado()
{
if($('#select_tema').val()=='nada||nada'){ 
	alert ("debe seleccionar tema");
	return;
} else if($('#selectperiodos').val() =='nada||nada' ){
   alert ("debe seleccionar un periodo");
   return;
}

var temareportes =  $('#select_tema').val().split("||");  
var tiporeporte = temareportes[1];
var wanoperiodo = $('#selectperiodos').val().split("||");
var ano = wanoperiodo[0];
var periodo = wanoperiodo[1];
esperar();
	$.get("../reportes/rep_evaluacionesrealizadas.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'traeresultdos',
					wemp_pmla		: $('#wemp_pmla').val(),
					wcco			: $('#buscador_cco').attr('valor'),
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wtemareportes	: temareportes[0],
					wtiporeportes	: tiporeporte,
					wperiodo		: periodo,
					wano			: ano
					
				}
				, function(data) {
					$('#resultados').html(data);
					$.unblockUI();
				});

}

function esperar (  )
{
$.blockUI({ message:        '<img src="../../images/medical/ajax-loader.gif" >',
                               css:         {
                                           width:         'auto',
                                           height: 'auto'
                                       }
                       });
}

function mostrarperiodos()
{
	if($('#select_tema').val()=='nada||nada')
	{
		$('#div_resultados').html('');
	}
	else
	{
		
		var temareportes =  $('#select_tema').val().split("||");  
		var tiporeporte = temareportes[1];
		$.get("../reportes/rep_evaluacionesrealizadas.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'mostrarperiodos',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: temareportes[0],
				wtiporeportes	: tiporeporte
				
			}
			, function(data) {
		
			$('#selectperiodos').html(data);
			
			});
			
			
	}
}
//


</script>

<style type="text/css">

.BordeNaranja{
	border: 1px solid orange;
}

.Titulo_azul{
	color:#000000; 
	font-weight: bold; 
	font-family: Times;
	font-size: 14pt;
}

.alerta{
	color:#000000; 
	font-family: arial;
	font-size: 14pt;
}

.ui-autocomplete{
	max-width: 	300px;
	max-height: 150px;
	overflow-y: auto;
	overflow-x: hidden;
	font-size: 	9pt;
}

</style>
<?php
/*
*********************************************************************************************************************************************************************
*Fecha de creación: 2013-02-05
*FELIPE ALVAREZ
*Este reporte muestra cuantos usuarios de cada centro de costos han llenado los campos de cada sección .
*/


//include_once("root/comun.php");
include_once("../procesos/funciones_talhuma.php");




echo "<input type='hidden' name='wtema' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' name='wuse' id='wuse' value='".$wuse."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";



session_start();
if (!isset($_SESSION['user']))
    echo "error";
else
{
    $wbasedato = consultarPrefijo($conex, $wemp_pmla, '01');
    //FUNCIONES.
	
 
		// datos select 
	$q = "	SELECT  Forcod, Fordes,Fortip "
		."	  FROM  ".$wbasedato."_000042 "
		."   WHERE Fortip='01' "
		."      OR Fortip='03' "
		."      OR Fortip='04' ";
	// Nota: solo esta funcionando para evaluaciones internas Fortip=01 y para encuestas usuarios registrados Fortip=03
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo"<div id='div_tema' align='center'>";
	echo"<table id='tableparametros' width='600' align='center'  border='0' cellspacing='1' cellpadding='1'>";
	echo"<tr align='Center'><td colspan='2' class='encabezadoTabla'>Datos para la busqueda</td></tr>";
	echo"<tr align='Left'>";
	echo"<td width='300' class='encabezadoTabla' ><b>Seleccione Tema</b></td>";
	echo"<td  width='300' class='fila1' >";
	echo"&nbsp;&nbsp;<select id='select_tema' onchange='mostrarperiodos()'>";
	echo"<option value='nada||nada' selected >Seleccione</option>";
	while($row = mysql_fetch_array($res))
	{
		echo"<option value='".$row['Forcod']."||".$row['Fortip']." '>".$row['Fordes']."</option>";
	}
	echo"</select>";
	echo"</td>";
	echo"</tr>";


    $q = "SELECT tb1.Ccocod AS codigo, tb1.Cconom AS nombre
            FROM costosyp_000005 AS tb1
           INNER JOIN ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
           WHERE tal.ideest = 'on'
           GROUP BY  tb1.Ccocod
           ORDER BY  tb1.Cconom";
    $rs = mysql_query($q, $conex);
    $numCcos = mysql_num_rows($rs);
   
    echo "<tr align='left' ><td class='encabezadoTabla' ><b>Centro de costos</b></td>";
    echo "<td class='fila2'>";	
	echo "&nbsp;&nbsp;<input type='text'  id='buscador_cco' size='40' >
		   <img width='12'  border='0' height='12' title='Busque el nombre o parte del nombre del centro de costo' src='../../images/medical/HCE/lupa.PNG'>";
    echo "</td></tr>";
	echo "<tr  id='trperiodo' align='left'>";
	echo "<td class='encabezadoTabla'><b>Periodo</b></td>";
	echo "<td class='fila1' ><div id='selectperiodo' >&nbsp;&nbsp;<Select id='selectperiodos'>";
	echo "<option value='nada||nada'> Seleccione un tema </option>";
	echo "</select></div></td>";
	echo "</tr>";
    echo "<tr><td align='center' colspan='2'><br><input type='button' value='Buscar' onclick='traeresultado()'></input></td></tr>";
    echo "</table>";
    echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<div id='resultados'></div>";
	echo "<br><br><br><br>"; 
}
?>
</html>