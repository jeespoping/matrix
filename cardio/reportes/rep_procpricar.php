<html>
<head>
<title>MATRIX - [REPORTE PROCESOS PRIORITARIOS]</title>

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_procpricar.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PROCESOS PRIORITARIOS                                                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver en general los procesos prioritarios                                                       |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : MARZO 18 DE 2010.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : MARZO 18 DE 2010.                                                                                           |
//DESCRIPCION			      : Este reporte sirve para observar en general los procesos generados de la unidad por año mes.                |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000050       : Tabla de Bases de datos por Empresa.                                                                                  |
//clisur_000106     : Tabla de Detalle de cargos                                                                                            |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 18-Marzo-2010";

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
encabezado("Procesos Prioritarios",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='cardio';

 

 

 
 //Forma
 echo "<form name='forma' action='rep_procpricar.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
   	echo "<form name='rep_procpricar' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

 	//Fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Fecha Inicial</td>";
 	echo "<td class='fila2' align='center' width=150>";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";

 	echo "<tr><td align=center colspan=2><br><input type='submit' id='searchsubmit' value='OK'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar

 	echo "</table>";
 	echo '</div>';
 	echo '</div>';
 	echo '</div>';
    
  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
	
    echo "<center><table border=1>";
    echo "<tr><td align=center colspan=4 bgcolor=#FFFFFF><font text color=#003366><b>CARDIOLOGIA NO INVASIVA</b></font></td></tr>";
    echo "<tr><td align=center colspan=4 bgcolor=#FFFFFF><font text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
    echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>PROCESO PRIORITARIO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>EVALUADOS</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>PROGRAMADOS</b></font></td>" .
			 "<td align=center bgcolor=#006699><font text color=#FFFFFF><b>CUMPLIMIENTO %</b></font></td></tr>";

    $mesi=SUBSTR(".$fec1.",6,2);
    $mesf=SUBSTR(".$fec2.",6,2);
    
    $quer1 = "CREATE TEMPORARY TABLE if not exists tempora1 as "
            ."SELECT pames as mes,papp as pp,ppproce as proc,patotal as total"
            ."  FROM ".$empre1."_000006 left join ".$empre1."_000005"
            ."    ON papp=ppproce"
            ."   AND pames=SUBSTRING(ppfecha,6,2)" 
            ."   AND paano=SUBSTRING(ppfecha,1,4)" 
            ." WHERE pames between '".$mesi."' and '".$mesf."'"
            ."   AND paano = SUBSTRING('".$fec2."',1,4)"
            ." GROUP BY 1,2,3,4"
            ." ORDER by 1,2";  
    
    //echo $quer1."<br>";         
            
    $err4 = mysql_query($quer1, $conex) or die("ERROR EN QUERY");
    
    
	$query1 = "SELECT SUBSTRING(ppfecha,6,2) as mes,ppproce as pp,count(*) as cant,patotal as total"
            ."   FROM ".$empre1."_000005 left join ".$empre1."_000006"
            ."     ON ppproce=papp" 
            ." AND SUBSTRING(ppfecha,6,2)=pames" 
            ." AND SUBSTRING(ppfecha,1,4)=paano" 
            ." WHERE ppfecha between '".$fec1."' and '".$fec2."'"
            ." GROUP by 1,2,4"
            ." UNION ALL"
            ." SELECT mes,pp,0 as cant,total"
            ."   FROM tempora1"
            ."  WHERE proc IS NULL"
            ." ORDER by 1,2";  
				
	//echo $query1."<br>"; 
				 
	$err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
	
	//echo mysql_errno() ."=". mysql_error();

    $swtitulo='SI';
    $tmesant='';
    
    $proceant=0;
    $mesant='';
    $ppant='';
    $evaant=0;
    $paant=0;
    $porcant=0;
    $porcenf=0;
    
	$toteva=0;
	$totpro=0;
	$totevaf=0;
	$totprogf=0;

    $wcfant='';
	
	for ($i=1;$i<=$num1;$i++)
	 {
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

    $row1 = mysql_fetch_array($err1);
	   
   	if ($swtitulo=='SI')
	  {
       $tmesant = $row1[0];

	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>MES PROCESO : </b></font></td><td align=center colspan=3>".$tmesant."</td></tr>"; 
	   $swtitulo='NO';
 	   
	   if ($proceant<>0)
	    {
	    if ($evaant==0)
	     {
	      $porcant=0;
	     }
	     else
	     {
	     if ($paant=='')
	   	 {
	   	  $paant=0;
	      $porcant=100;
	   	 }
	   	 else
	   	 {
	   	  $porcant=($evaant/$paant)*100;		
	   	 }
	    }	
	     echo "<tr  bgcolor=".$wcfant."><td align=center>".$ppant."</td><td align=center>".number_format($evaant)."</td><td align=center>".number_format($paant)."</td></td><td align=center>".number_format($porcant)."</td></tr>"; 
	     $proceant=0;
	    }
	  } 
	    	  
	 if ($tmesant==$row1[0] )
	  {
	  	
	  if ($row1[2]==0)
	  {
	  	$porcen=0;
	  }
	  else
	  {		
	   if ($row1[3]==0)
	   {
	   	$row1[3]=1;
	    $porcen=100;
	    $row1[3]=0;
	   }
	   else
	   {
	   	if ($row1[3]=='')
	   	{
	   	 $row1[3]=0;
	     $porcen=100;
	   	}
	   	else
	   	{
	   	$porcen=($row1[2]/$row1[3])*100;	
	   	}	   	
	   }
	  } 	 
	   echo "<tr  bgcolor=".$wcf."><td align=center>".$row1[1]."</td><td align=center>".number_format($row1[2])."</td><td align=center>".number_format($row1[3])."</td><td align=center>".number_format($porcen)."</td></tr>"; 
	   $toteva=$toteva+$row1[2];
	   $totpro=$totpro+$row1[3];
	   $totevaf=$totevaf+$row1[2];
	   $totprogf=$totprogf+$row1[3];
	  }
	 else 
	  {
	   
	   if ($totpro==0)
	   {
	   	$porcenm=100;
	   }	
	   else
	   {	
	   $porcenm=($toteva/$totpro)*100;
	   }	
	  	
	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>TOTAL MES : </b></font></td><td align=center>".number_format($toteva)."</td><td align=center>".number_format($totpro)."</td><td align=center>".number_format($porcenm)."</td></tr>";
	   echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	   $toteva=0;
	   $totpro=0;
	   $swtitulo='SI';
	   $ppant=$row1[1];
	   $evaant=$row1[2];
	   $proceant=1;
	   $paant=$row1[3];
	   $wcfant=$wcf;
	   $toteva=$toteva+$row1[2];
	   $totpro=$totpro+$row1[3];
	   $totevaf=$totevaf+$row1[2];
	   $totprogf=$totprogf+$row1[3];
	  }
	} //fin del for
	if ($totpro==0)
	{
	$porcenm=100;	
	$porcenf=100;
	}	
	else
	{ 	
	$porcenm=($toteva/$totpro)*100;
	$porcenf=($totevaf/$totprogf)*100;
	}
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>TOTAL MES : </b></font></td><td align=center>".number_format($toteva)."</td><td align=center>".number_format($totpro)."</td><td align=center>".number_format($porcenm)."</td></tr>";
	echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>"; 
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>TOTAL GENERAL MESES : </b></font></td><td align=center colspan=1>".number_format($totevaf)."</td><td align=center>".number_format($totprogf)."</td><td align=center>".number_format($porcenf)."</td></tr>";
	
	echo "</table>"; // cierra la tabla o cuadricula de la impresión

	echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>"; 
    echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";	
    echo "</table>";
	
  } // cierre del else donde empieza la impresión

}
?>