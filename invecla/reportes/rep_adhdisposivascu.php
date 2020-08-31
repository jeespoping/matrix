<html>
<head>
<title>MATRIX - [Formulario de Evaluación Adherencias Dispositivos Vasculares]</title>

<script language="JavaScript"> 

  function abrirVentana( url )
  {
   var ancho=screen.width;
   var alto=screen.availHeight;
   var v = window.open( url, '', 'scrollbars=1, width='+ancho+', height='+alto );
	   v.moveTo(0,0);
		
  }

  function inicio()
	{ 
	 document.location.href='rep_adhdisposivascu.php'; 
	}
	
  function enter()
	{
		document.forms.rep_adhdisposivascu.submit();
	}
	
  function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                     REPORTE FORMULARIO DE ADHERENCIAS DISPOSITIVOS VASCULARES PROCESOS PRIORITARIOS CLINICA                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver el formulario de adherencias dispositivos vasculares procesos prioritarios de la clinica.  |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : FEBRERO 14 DE 2014.                                                                                         |
//FECHA ULTIMA ACTUALIZACION  : 14 de Febrero de 2014.                                                                                      |
//TABLAS UTILIZADAS :                                                                                                                       |
//cominf_000051     : Tabla de adherencias dispositivos vasculares procesos prioritarios de clinica-nuevos.                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 14-Febrero-2014";

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
encabezado("Formulario de Adherencias Dispositivos Vasculares",$wactualiz,"Clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 $empre1='cominf';
 
 //Forma
 echo "<form name='forma' action='rep_adhdisposivascu.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 $hoy=date("Y-m-d");	
  	
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>EVALUACIÓN ADHERENCIAS DISPOSITIVOS VASCULARES CLINICA</b></font></td>";
   echo "</tr>";
   echo "</table>";
   
   $cod=explode('-',$user);
   
   $codi=substr($cod[1],0);

   $query = " SELECT ccostos"
           ."   FROM usuarios "
           ."  WHERE codigo= '".$codi."' "; 
           
   $err = mysql_query($query,$conex);
   $num = mysql_num_rows($err);
    //echo $query."<br>";

   $row = mysql_fetch_array($err);
                    
   $query1=" SELECT Ppadvfecha,Ppadvhistoria,Ppadvcco,Ppadvtipdis,Ppadvdetcri2,Ppadvcrite2,Ppadvdetcri3,Ppadvcrite3,Ppadvdetcri4,Ppadvcrite4,Ppadvdetcri5,Ppadvcrite5,Ppadvdetcrite6,Ppadvcrite6,Ppadvtotal,Ppadvporce,Ppadvevalua,Ppadvcargoeva,Ppadvobserva ,`id` " 
          ."   FROM ".$empre1."_000051" 
          ."  WHERE `Ppadvcco` LIKE '$row[0]%'";
          
    //echo $query1."<br>";       
          
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);       
  
     echo "<table border=0>";
     echo "<Tr>";
	 echo "<td>";
     echo "<align=left><INPUT type=button value='NUEVO' onclick='javascript: abrirVentana(\"/matrix/det_registro.php?id=0&pos1=cominf&pos2=0&pos3=0&pos4=000051&pos5=0&pos6=cominf&tipo=P&Valor=&Form=000051-cominf-C-Procesos%20Prioritarios%20AdhDispositivos%20Vasculares&call=0&change=0&key=cominf&Pagina=1\")'>";
     echo "</td>";
	 
	 echo "</Tr >";
	 echo "</table>";
	 
     echo "<table border=1 cellpadding='1' cellspacing='1' size='1350'>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA EVALUACION</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>HISTORIA CLINICA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>UNIDAD - CENTRO DE COSTOS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>TIPO_DISPOSITIVO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO_2</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO_2</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO_3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO_3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO_4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO_4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO_5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO_5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO_6</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO_6</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>TOTAL</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PORCENTAJE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PERSONA QUE REALIZO LA EVALUACION</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CARGO DEL EVALUADOR</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>OBSERVACIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1></td>";
     echo "</tr>";      
       
	   
	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);
	  
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[1]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[2]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[3]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[4]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[5]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[6]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[7]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[8]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[9]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[10]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[11]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[12]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[13]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[14]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[15]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[16]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[17]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[18]</font></td>";
  	   $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[19]."&pos1=cominf&pos2=0&pos3=0&pos4=000051&pos5=0&pos6=cominf&tipo=P&Valor=&Form=000051-cominf-C-Procesos%20Prioritarios%20AdhDispositivos%20Vasculares&call=0&change=0&key=cominf&Pagina=1' target='new' >Editar</a>";
	   echo "<td align=left><font size=1>".$hyper."</td>";
       echo "</Tr >";
	  
    } // cierre del for
   echo "</table>";
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
}
?>