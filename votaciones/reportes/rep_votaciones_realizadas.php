<?php
include_once("conex.php");



include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
$wbasedato_votaciones = consultarAliasPorAplicacion($conex, $wemp_pmla, "votaciones");

//Trae las empresas registradas en la tabla 50 de root.
function empresas(){

	
	global $conex;
	
	 $q =  " SELECT Empcod, Empdes "
		  ."   FROM root_000050";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$array_empresas = array();
	
	while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Empcod'], $array_empresas))
        {
            $array_empresas[$row['Empcod']] = $row;
        }
		
    }
	
	return $array_empresas;
}

//Trae las tablas que contienen los centros de costos de las empresas.
function traer_tablas_cco($conex, $aplicacion){

	
	$q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	$array_ccos = array();

while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Detval'], $array_ccos))
        {
            $array_ccos[$row['Detval']] = $row;
        }
		
    }	
	
	return $array_ccos;
}

function datos_votacion($aplicacion){
	
	global $wbasedato;
	global $wbasedato_votaciones;
	global $conex;
	
	//Configuracion de la votacion
	$q_conf_vot =    " SELECT * "
					."   FROM ".$wbasedato_votaciones."_000001 "
					."	WHERE Votapl = '".$aplicacion."'"
					."	  AND Votact = 'on'";
	$res_conf_vot = mysql_query($q_conf_vot,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_conf_vot." - ".mysql_error());

	$array_conf_vot = array();

		while($row_conf_vot = mysql_fetch_assoc($res_conf_vot))
		{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_conf_vot['Votcod'], $array_conf_vot))
			{
				$array_conf_vot[$row_conf_vot['Votcod']] = $row_conf_vot;
			}

		}

	return $array_conf_vot;
}


//Trae todas las tablas que contiene la informacion de talento humano de las empresas.
function traer_talumas($conex, $informacion_empleados, $aplicacion, $wbasedato){

	
	global $conex;
	
	$q =  " SELECT Tabtab "
		  ."   FROM ".$wbasedato."_000008"
		  ."  WHERE Tabemp = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$empresas_votacion_a = $row['Tabtab'];
	$empresas_votacion_b = explode(",",$empresas_votacion_a);
	$empresas_votacion = implode("','", $empresas_votacion_b);	
	
	$q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detemp in ('".$empresas_votacion."')"
		  ."    AND Detapl = '".$informacion_empleados."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_talhumas = array();

	while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Detemp'], $array_talhumas))
        {
            $array_talhumas[$row['Detemp']] = $row;
        }

    }

	return $array_talhumas;
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


function relacionarUsuariosSeccionCco($usuariosEnSeccion, $empleados, $centrosCostos){	
	
		global $conex;
		global $wbasedato;
		
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



function consultar_votaciones_unidad(){
	
	global $conex;
	global $wbasedato;
		
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
   
		
	
	
}



?>

<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link rel='stylesheet' href='../../../include/root/matrix.css'/>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>

<script type="text/javascript">

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

</script>

<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title></title>
  <?php
  
    $actualiz = "2016-10-27";
    encabezado( "REPORTE CUMPLIMIENTO VOTACIONES", $actualiz ,"clinica" );
	
	/*
	2016-10-27 Jonatan Lopez: Programa que genera el reporte de cumplimiento en porcentaje para las votaciones por empresa y centro de costos.
	*/
	
	
	//Declaración de variables.
    $secciones = Array();  //array que contendrá la información de cada sección .
    $centrosCostos = Array(); //array que contendrá la información de cada centro de costos(código, nombre, y cantidad total de empleados)
    $empleados = Array();  //array que contendrá la información de cada empleado(código, nombre y centro de costos al que pertenece)
    $tablasSeccion =  Array();  //array que contendrá la información de secciones con las tablas y campos que la componen, ademas de los usuarios que los han llenado.
	
	$array_conf_vot = datos_votacion($aplicacion);
	
	foreach($array_conf_vot as $key => $value){
		
		$cod_vot = $key;
		
	}
	
	$wano_votacion = $array_conf_vot[$cod_vot]['Votano'];
	$wconsecutivo_votacion = $array_conf_vot[$cod_vot]['Votcod'];
	$westado_votaciones = $array_conf_vot[$cod_vot]['Votest'];
	$wcontrol_voto_blanco_est = $array_conf_vot[$cod_vot]['Votbla'];
	$wcontrol_suplente_est = $array_conf_vot[$cod_vot]['Votsup'];
	$westado_inscripciones = $array_conf_vot[$cod_vot]['Votein'];
	$wcon_inscripcion = $array_conf_vot[$cod_vot]['Vothin'];
	$wempresas = empresas();	

	$wtalhumas = traer_talumas($conex, "informacion_empleados", $aplicacion, $wbasedato_votaciones); //Trae todas las empresas que tengan tablas de talento humano.
	
	
	//============================= Arreglo de centros de costo por empresa ============================
	$wtabla_cco = traer_tablas_cco($conex, "costoscer");

	
	//Consultar el los centros de costos de las empresas.
	$array_nombres_cco = array();

	foreach($wtabla_cco as $key_tablas_cco => $value_tablas_cco){	
	
		//Consulto en las tablas los datos de los centros de costos.
		$q_cco1 =   " SELECT * "
				  ."   FROM ".$value_tablas_cco['Detval']."";
		$res_ccos1 = mysql_query($q_cco1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cco1." - ".mysql_error());	 	
		
		//Creo un arreglo inicial de empresas.
		if(!array_key_exists($value_tablas_cco['Detemp'], $array_nombres_cco))
		{
			$array_nombres_cco[$value_tablas_cco['Detemp']] = array();
		}
		
		//A cada empresa le relaciono sus centros de costos.
		while($row_ccos1 = mysql_fetch_assoc($res_ccos1))
		{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_ccos1['Ccocod'], $array_nombres_cco[$value_tablas_cco['Detemp']]))
			{
				
				$array_nombres_cco[$value_tablas_cco['Detemp']][$row_ccos1['Ccocod']] = $row_ccos1;
			}
			
		}		

	}
		
	foreach($wtalhumas as $key => $value){
		
		$q_cco[] = "  SELECT Idecco AS codigo, '".$value['Detemp']."' AS empresa, '".$value['Detval']."' as bdempresa
						FROM ".$value['Detval']." 
					   WHERE Ideest = 'on'";
	
	}
	
	$query_cco = implode(" UNION ", $q_cco);	
	$query_cco = "SELECT * FROM ( $query_cco ) AS t ORDER BY empresa ;";
	// echo "<pre>";
	// print_r($query_cco);
	// echo "</pre>";
	$res_cco = mysql_query($query_cco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_cco." - ".mysql_error());		
		
	//A cada empresa le relaciono sus centros de costos.
	while($row_ccos = mysql_fetch_assoc($res_cco))
	{		
		$array_empresas_cco[$row_ccos['empresa']][$row_ccos['codigo']] = $row_ccos;		
	}	

	foreach($array_empresas_cco as $key_emp => $cco){
		
		foreach($cco as $key => $datos_cco){
			
		 //consultamos la totalidad de usuarios en cada centro de costos;
       $q2 = "SELECT count(DISTINCT(Ideuse)) as empleados
                 FROM ".$datos_cco['bdempresa']." 
                WHERE Idecco='".$datos_cco['codigo']."'
                  AND Ideest='on'";			  
        $rs2 = mysql_query($q2, $conex);
        $row1=mysql_fetch_array($rs2);
		
		$qvotos = "SELECT count(DISTINCT(Ideuse)) as votos_cco
                 FROM ".$datos_cco['bdempresa'].", ".$wbasedato_votaciones."_000004
                WHERE Idecco = '".$datos_cco['codigo']."'
                  AND Ideest = 'on'
				  AND Rvoapl = '".$aplicacion."'
				  AND Ideced = Rvoced";			  
        $rs_votos = mysql_query($qvotos, $conex);
        $row_votos = mysql_fetch_array($rs_votos);
		
		$wtext_cco = $array_nombres_cco[$key_emp][$datos_cco['codigo']]['Cconom'];			
		//Si no trae el centro de costos en la posicion Cconom, entonces lo busco con Ccodes
		if(trim($wtext_cco) == ''){
			
			$wtext_cco = $array_nombres_cco[$key_emp][$datos_cco['codigo']]['Ccodes'];
		}
		
		//Si no esta con Ccodes, se imprime la empresa.
		if(trim($wtext_cco) == ''){			
			$wtext_cco = $wempresas[$key_emp]['Empdes']." - ".$datos_cco['codigo'];
		}
		
		$array_empresas_cco[$datos_cco['empresa']][$datos_cco['codigo']]['nombre'] = $wtext_cco;
		$array_empresas_cco[$datos_cco['empresa']][$datos_cco['codigo']]['cant_empleados'] = $row1['empleados'];
		$array_empresas_cco[$datos_cco['empresa']][$datos_cco['codigo']]['cant_votos'] = $row_votos['votos_cco'];
		
		$porcentajeEmpresa = 0;
		if($row_votos['votos_cco']>0 && $row1['empleados']>0)
		{
			$porcentajeEmpresa = round(@(($row_votos['votos_cco']*100)/$row1['empleados']), 2);
		}
		
		// $array_empresas_cco[$datos_cco['empresa']][$datos_cco['codigo']]['porcentaje'] = round(@(($row_votos['votos_cco']*100)/$row1['empleados']), 2);
		$array_empresas_cco[$datos_cco['empresa']][$datos_cco['codigo']]['porcentaje'] = $porcentajeEmpresa;
		
		}
	}

  
	// echo "<pre>";
	// print_r($array_empresas_cco);
	// echo "</pre>";
	
	    $class='fila1'; //Variable para controlar el estilo de las filas en el reporte.		
		echo "<center>";
		//Recorro el $array_votos_total_emp_aux[empresa][cco] => cantidad de votos, y luego ordeno los votos de mayor a menor por empresa.
		foreach($array_empresas_cco as $key_emp => $cco){
			
			$total_empleados = 0;
			$total_votos_realizados = 0;
			$total_votos_faltantes = 0;
			$total_porcentaje = 0;
			
			echo "<br>";
			echo "<table width='1000px'>";
			echo "<tr class='encabezadoTabla' align=center><td colspan=5>".$wempresas[$key_emp]['Empdes']."</td></tr>";
			echo "<tr class='encabezadoTabla' align=center>";			
			echo "<td width='500px'>Centro de costos</td><td>Total Empleados</td><td>Votos realizados</td><td>Votos faltantes</td><td>Porcentaje cumplimiento</td>";
			echo "</tr>";			
			foreach($cco as $key => $datos_cco){
				
				($class == "fila2" )? $class = "fila1" : $class = "fila2";	
				echo "<tr class=".$class.">";
				echo "<td>".$datos_cco['nombre']."</td><td align=center>".$datos_cco['cant_empleados']."</td><td align=center>".$datos_cco['cant_votos']."</td><td align=center>".($datos_cco['cant_empleados']-$datos_cco['cant_votos'])."</td><td align=right>".$datos_cco['porcentaje']." %</td>";
				echo "</tr>";
				
				$total_empleados = $total_empleados + $datos_cco['cant_empleados'];
				$total_votos_realizados = $total_votos_realizados + $datos_cco['cant_votos'];
				$total_votos_faltantes = $total_votos_faltantes + ($datos_cco['cant_empleados']-$datos_cco['cant_votos']);
				$total_porcentaje = round(@(($total_votos_realizados*100)/$total_empleados), 2);
				
			}
			echo "<tr class='encabezadoTabla' align=center><td>Total</td><td>".$total_empleados."</td><td>".$total_votos_realizados."</td><td>".$total_votos_faltantes."</td><td align=right>".$total_porcentaje." %</td></tr>";
			echo "</table>";
		}
		echo "</center>";
		
		
  ?>
</head>
<body>

<br>
</body>
</html>
