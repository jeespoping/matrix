<html>
<head>
  	<title></title>
	<link type='text/css' href='../../hce/procesos/HCE.css' rel='stylesheet'>
    <script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>
	<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
    <script type="text/javascript">
    </script>
</head>
<body onLoad= 'pintardivs();' BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

//====================================================================================
//	--> Programa creado para realizar la impresion del formulario de triage de la HCE
//====================================================================================
/*
	Fecha Creacion:	2016-07-05
	Autor:			Jerson Andres Trujillo
	Descripcion:	Este programa realiza la impresion del formulario de triage de la hce, para una historia e
					ingreso temporal, en el momento en que un paciente de urgencias tiene triage realizado pero
					aun no le han hecho la admision, es decir aun no tiene historia e ingreso asignado, ya que el
					triage de un paciente se realiza (movhos/procesos/triage.php) antes de la admision lo que implica
					que se guarda el formulario hce con una historia e ingreso temporal, ya despues de que se la haga 
					la admision, dicho formulario es actualizado con la historia e ingreso definitivo.
					Las funciones y el query principal es copiado del programa HCE_Impresion.php

*/


include_once("hce/HCE_print_function.php");

@session_start();
if(!isset($_SESSION['user']))
{
	 echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$key = substr($user,2,strlen($user));
	

	include_once("root/comun.php");
	

	
	$color="#dddddd";
	$color1="#C3D9FF";
	$color2="#E8EEF7";
	$color3="#CC99FF";
	$color4="#99CCFF";
	
	$Hgraficas=" |";
	
	echo "
	<table border=1 width='712' class=tipoTABLE1>
		<tr>
			<td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$origen.".jpg' id='logo'></td>
			<td id=tipoL01C>Paciente</td>
			<td colspan=4 id=tipoL04>".$wtipodoc." ".$wcedula."<br>".$nombrePaciente."</td>
			<td id=tipoL01C>P&aacute;gina 1</td>
		</tr>
		<tr>
			<td id=tipoL01C>Servicio</td>
			<td id=tipoL02C colspan='4'>Urgencias</td>
			<td id=tipoL01C></td>
		</tr>
	</table><br>";
	
	$en					="";
	$queryI				="";
	$nrofor				=-1;
	$wfechaf 			= $wfechai;
	
	$forYcampoTriage	= consultarAliasPorAplicacion($conex, $origen, "formularioYcampoTriage");
	$forYcampoTriage	= explode("-", $forYcampoTriage);
	$formularioTriage 	= $forYcampoTriage[0];
	$en 				= $forYcampoTriage[0];

	//                                        0                                              1                          2                                                3                                                  4                           5                          6                           7                          8                          9                          10                       11                                              12                         13                         14                         15                         16                         17                         18                         19
	$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".$formularioTriage.".movdat,".$empresa."_000002.Detorp,".$empresa."_".$formularioTriage.".fecha_data,".$empresa."_".$formularioTriage.".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$formularioTriage.".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir,".$empresa."_000002.Detimc,".$empresa."_000002.Detccu from ".$empresa."_".$formularioTriage.",".$empresa."_000002,".$empresa."_000001 ";
	$queryI .= " where ".$empresa."_".$formularioTriage.".movpro='".$formularioTriage."' "; 
	$queryI .= "   and ".$empresa."_".$formularioTriage.".movhis='".$whis."' ";
	$queryI .= "   and ".$empresa."_".$formularioTriage.".moving='".$wing."' ";
	$queryI .= "   and ".$empresa."_".$formularioTriage.".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
	$queryI .= "   and ".$empresa."_".$formularioTriage.".movpro=".$empresa."_000002.detpro ";
	$queryI .= "   and ".$empresa."_".$formularioTriage.".movcon = ".$empresa."_000002.detcon ";
	//$queryI .= "   and ".$empresa."_000002.detest='on' "; 
	if($CLASE == "C")
		$queryI .= "   and ".$empresa."_000002.detvim in ('A','C') "; 
	else
		$queryI .= "   and ".$empresa."_000002.detvim in ('A','I') "; 
	$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' "; 
	$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
				
	if($CLASE == "C")
		imprimir($conex,$empresa,$wdbmhos,$origen,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,0);
}
?>
