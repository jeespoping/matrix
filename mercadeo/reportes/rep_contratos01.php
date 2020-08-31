<html>
<head>
<title>MATRIX - [REPORTE DE CONTRATOS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_contratos01.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_contratos01.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

function segundos_tiempo($segundos){  
$minutos=$segundos/60;  
$horas=floor($minutos/60);  
$minutos2=$minutos%60;  
$segundos_2=$segundos%60%60%60;  
if($minutos2<10)$minutos2='0'.$minutos2;  
if($segundos_2<10)$segundos_2='0'.$segundos_2;  

if($segundos<60){ /* segundos */  
$resultado= round($segundos).' Segundos';  
}elseif($segundos>60 && $segundos<3600){/* minutos */  
$resultado= $minutos2.':'.$segundos_2.' Minutos';  
}else{/* horas */  
$resultado= $horas.':'.$minutos2.':'.$segundos_2.' Horas';  
}  
return $resultado;  
} 

/*******************************************************************************************************************************************
*                     REPORTE DE CONTRATOS X ENTIDAD                                                                    *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte contratos x entidad                                                             |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Septiembre 16 2015.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : Septiembre 16 2015.                                                                                             |
//DESCRIPCION			      : Este reporte sirve para traer el contrato por entidad                         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//urgen_000003      : Tabla de Referencia y Contrareferencia.                                                                               |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 16-Sep-2015";

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
encabezado("REPORTE DE CONTRATOS POR ENTIDAD",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 //Conexion base de datos
 

 


 //Forma
 echo "<form name='forma' action='rep_contratos01.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($pp) or $pp=='-' or $pp == '' )
  {
  	echo "<form name='rep_contratos01' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de los datos para el reporte
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Entidad:</B><br></font></b><select name='pp' id='searchinput'>";

    $query = " SELECT Entidad,Num_consecutivo "
            ."   FROM mercadeo_000004 " 
			//." WHERE Fecha > '2012-05-29' "
            ." GROUP BY 1,2 "
            ." ORDER BY 1,2 ";
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tpp=$pp;
   
    if (!isset($pp))
    { 
     echo "<option></option>";
    }
    else 
    {
     echo "<option>".$tpp[0]."|".$tpp[1]."</option>";
    } 
   
    for ($i=1;$i<=$num3;$i++)
	 {
	 $row3 = mysql_fetch_array($err3);
	 echo "<option>".$row3[0]."|".$row3[1]."</option>";
	 }
	echo "<option></option>";
    echo "</select></td></tr>";
 	
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
  	$tpp=explode('|',$pp);
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>CONTRATOS MERCADEO</b></font></td>";
    echo "</tr>";
	echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>ENTIDAD: <i>".$tpp[0]."</i>&nbsp&nbsp&nbspCONSECUTIVO: <i>".$tpp[1]."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
    echo "<br>";
    
 	  $query = " SELECT	Numero_contrato,Fec_firma_contrato,Prorroga_automatica,Contrato_vigente,Monto,Fec_inicio,Ficha_tecnica,Fec_firma_ficha,Fec_vence_ficha,Observaciones_ficha,Fechas_radicacion,Pagos_despues_radicado,Descuento_financiero,Porcentaje_desc_financiero,Fec_entrega_juridica,Notas_generales,Pdf1,Pdf2,Pdf3,Pdf4,Pdf5,Fec_terminacion_contrato,Observaciones,Acta_liquidacion"
	  ."   FROM  mercadeo_000004 "
	  ."   WHERE Entidad = '".$tpp[0]."'" 
	  ."    and  Num_consecutivo = '".$tpp[1]."'";
   
  	 $err1 = mysql_query($query,$conex);
     $num1 = mysql_num_rows($err1);
	
	for ($i=1;$i<=$num1;$i++)
	{
	 if (is_int ($i/2))
	  {
	    $wcf="F8FBFC";  // color de fondo
	  }
	 else
	  {
	    $wcf="DFF8FF"; // color de fondo
	  }
	  
		$row1 = mysql_fetch_array($err1);
		echo "<table align=center border=1 width=725 >";
		echo "<tr><td><font size=3 face='arial' text color=#003366 ><B>NUMERO DE CONTRATO:</b>".$row1['Numero_contrato']."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366 ><B>FECHA FIRMA CONTRATO:</b>".$row1['Fec_firma_contrato']."</td>"; 
		if ($row1['Prorroga_automatica']=='on')
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>PRORROGA AUTOMATICA:</b>SI</td>"; 
		else
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>PRORROGA AUTOMATICA:</b>NO</td>"; 
		if ($row1['Contrato_vigente']=='on')
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>CONTRATO VIGENTE:</b>SI</td>"; 
		else
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>CONTRATO VIGENTE:</b>NO</td>"; 
		$monto=$row1['Monto'];
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>MONTO:</b>".number_format($monto,0,'.',',')."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>FECHA INICIO:</b>".$row1['Fec_inicio']."</td>"; 
		if ($row1['Ficha_tecnica']=='on')
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>FICHA TECNICA:</b>SI</td>"; 
		else
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>FICHA TECNICA:</b>NO</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>FECHA FIRMA FICHA:</b>".$row1['Fec_firma_ficha']."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>FECHA VENCE FICHA:</b>".$row1['Fec_vence_ficha']."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>OBSERVACIONES FICHA:</b>".str_replace( "\n", "<br>", ($row1['Observaciones_ficha'])  )."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>FECHAS RADICACION:</b>".$row1['Fechas_radicacion']."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>PAGOS DESPUES DE RADICADO:</b>".$row1['Pagos_despues_radicado']."</td>"; 
		if ($row1['Descuento_financiero']=='on')
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>DESCUENTO FINANCIERO:</b>SI</td>"; 
		else
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>DESCUENTO FINANCIERO:</b>NO</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>PORCENTAJE DESCUENTO FINANCIERO:</b>".$row1['Porcentaje_desc_financiero']."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>FECHA ENTREGA JURIDICA:</b>".$row1['Fec_entrega_juridica']."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>NOTAS GENERALES:</b>".str_replace( "\n", "<br>", ($row1['Notas_generales'])  )."</td>"; 
		
		$grupo="mercadeo";
		$pdf1=explode("/",$row1['Pdf1']);
		$pdf2=explode("/",$row1['Pdf2']);
		$pdf3=explode("/",$row1['Pdf3']);
		$pdf4=explode("/",$row1['Pdf4']);
		$pdf5=explode("/",$row1['Pdf5']);
		$grupo="mercadeo";
		if ($row1['Pdf1']!='.')
			echo "<tr><td align=CENTER><A HREF='/matrix/images/medical/".$grupo."/".$pdf1[3]."' target = '_blank'>".$pdf1[3]."</a></td>";
		if ($row1['Pdf2']!='.')
			echo "<tr><td align=CENTER><A HREF='/matrix/images/medical/".$grupo."/".$pdf2[3]."' target = '_blank'>".$pdf2[3]."</a></td>";
		if ($row1['Pdf3']!='.')
			echo "<tr><td align=CENTER><A HREF='/matrix/images/medical/".$grupo."/".$pdf3[3]."' target = '_blank'>".$pdf3[3]."</a></td>";
		if ($row1['Pdf4']!='.')
			echo "<tr><td align=CENTER><A HREF='/matrix/images/medical/".$grupo."/".$pdf4[3]."' target = '_blank'>".$pdf4[3]."</a></td>";
		if ($row1['Pdf5']!='.')
			echo "<tr><td align=CENTER><A HREF='/matrix/images/medical/".$grupo."/".$pdf5[3]."' target = '_blank'>".$pdf5[3]."</a></td>";
		if ($row1['Contrato_vigente']=='off')
			echo "<tr><td><font size=3 face='arial' text color=#003366><B>FECHA TERMINACION CONTRATO:</b>".$row1['Fec_terminacion_contrato']."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>OBSERVACIONES:</b>".str_replace( "\n", "<br>", ($row1['Observaciones'])  )."</td>"; 
		echo "<tr><td><font size=3 face='arial' text color=#003366><B>ACTA LIQUIDACION:</b>".str_replace( "\n", "<br>", ($row1['Acta_liquidacion'])  )."</td></table>"; 
	
	   echo "<br>";
	   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
	   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
	   echo "</table>";
	}
  } // cierre del else donde empieza la impresión

}
?>