<html>
<head>
<title>MATRIX - [REPORTE PARA VER LOS EMPLEADOS QUE CUMPLEN 5,10,15 O 20 AÑOS]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_infanosemp.php'; 
	}
	
	function enter()
	{
		document.forms.rep_infanosemp.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");


/*******************************************************************************************************************************************
*                                             REPORTE PARA LOS EMPLEADOS QUE CUMPLEN 5,10,15,20 AÑOS                                       *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los empleados que cumplen 5,10,15,20 años.                                                 |
//AUTOR				          :Ing. Gustavo Alberto Avendaño Rivera.                                                                       |
//FECHA CREACION			  :JUNIO 22 DE 2011.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :22 de Mayo de 2011.                                                                                         |
//TABLAS UTILIZADAS   :                                                                                                                      |
//noper en unix       : Tabla de Empleados.                                                                                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex     = obtenerConexionBD("matrix");
$conex_o   = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
$wactualiz = "1.0 22-Junio-2011";

$empresa='root';

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
encabezado("EMPLEADOS QUE CUMPLEN AÑOS EN LA INSTITUCIÓN CADA 5 AÑOS",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{



 //Forma
 echo "<form name='forma' action='rep_infanosemp.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($empre) or $empre=='' or !isset($fec1) or $fec1 == '')
  {
  	
  	echo "<form name='rep_infanosemp' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

 	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

   /////////////////////////////////////////////////////////////////////////////////////// seleccion para Empresa para saber de donde es el empleado
   echo "<td align=CENTER colspan=3 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Empresa: <br></font></b><select name='empre' onchange='enter()'>";

   $query = " SELECT Empcod,Empdes"
	       ."   FROM ".$empresa."_000050"
	       ."  WHERE Empest='on'"
	       ."  ORDER BY Empcod,Empdes";

   $err = mysql_query($query,$conex);
   $num = mysql_num_rows($err);
   $emp=explode('-',$empre); 
   
   $codemp = $emp[0];
      
   if ($codemp == "")
    { 
     echo "<option></option>";
     $codemp = "";   	 
    }
   else 
    {
 	 echo "<option>".$emp[0]."-".$emp[1]."</option>";
   	} 
   

   for ($i=1;$i<=$num;$i++)
	{
	 $row = mysql_fetch_array($err);
	 echo "<option>".$row[0]."-".$row[1]."</option>";
	}
   
   echo "</select></td>";
    
   
   	//Fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=150>Fecha Corte</td>";
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
   
   
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
   
  $anoa=SUBSTR(".$fec1.",1,4);	
  	
  $emp=explode('-',$empre); 
   
  $codemp = $emp[0];

  switch ($codemp)  //Para hacer la conexion por ODBC, dependiendo de la empresa.
   {
	case "01":    // Clinica Las Americas
	 {
	   $conexi=odbc_connect('nomina','','')	
	       or die("No se realizo conexión con la BD Nomina - Clinica las Americas");
	 	   
	   break;
	 }   
	case "02":    // Clinica Del Sur
	 {
	   $conexi=odbc_connect('nomsur','','')
           or die("No se realizo conexión con la BD Nomina - Clinica del Sur");
           	 	
       break;
	 }
	case "03":    // FarmaStore
	 {
	   $conexi=odbc_connect('nomsto','','')
	       or die("No se realizo conexión con la BD Nomina - FarmaStore");
	   
       break;
	 }
	case "04":    // Patologia las Americas
	 {
	   $conexi=odbc_connect('nompat','','')
	       or die("No se realizo conexión con la BD Nomina - Patologia");
	   
       break;
	 }
   }
  	
  	
   	echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>EMPLEADOS QUE CUMPLEN AÑOS EN LA CLINICA</b></font></td>";
	echo "</tr>";
	echo "<tr><td><br></td></tr>";
	echo "</table>";
	echo "<br>";
	   
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>NOMBRE1_EMPLEADO</font></td>"; 
	echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>NOMBRE2_EMPLEADO</font></td>";
	echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>APELLIDO1_EMPLEADO</font></td>"; 
	echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>APELLIDO2_EMPLEADO</font></td>";
	echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>CARGO</font></td>";
	echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>TIEMPO_LABORADO</font></td>";
	echo "</tr>";
	  
    $query_o1="SELECT perno1,perno2,perap1,perap2,ofinom,year(perfin)  "
			  ." FROM noper,noofi "
			  ."WHERE perofi = oficod"
		      ."  AND peretr = 'A' "
			  ."  GROUP BY 1,2,3,4,5,6"
		      ."  ORDER BY 6,1,2,3";
		    
		           
	//echo $query_o."<br>";
	$err_o = odbc_do($conex_o,$query_o1);
			
   $Num_Filas = 0;
   
   while (odbc_fetch_row($err_o))
	  {
	  	$Num_Filas++;
	  	
	  	$nom1 = odbc_result($err_o,1);//nombre1
		$nom2 = odbc_result($err_o,2);//nombre2
		$ap1  = odbc_result($err_o,3);//apellido1
		$ap2  = odbc_result($err_o,4);//apellido2
		$ofi  = odbc_result($err_o,5);//oficio
		$anoi = odbc_result($err_o,6);//año fecha ingreso
	
		$tiempo=$anoa-$anoi;
		
	   IF ($tiempo>0)
	   {
		$tiemp=substr($tiempo,-1,1);
		
		IF(($tiemp==0) or ($tiempo==5)) //para saber que es divisible por 5
		{
		 echo "<tr>";
		 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nom1."</font></td>";
		 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nom2."</font></td>";
		 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$ap1."</font></td>";
		 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$ap2."</font></td>";
		 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$ofi."</font></td>";
		 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$tiempo."</font></td>";
		 echo "</tr>";
		}
	   }
	  }
	  	odbc_close($conexi);
		odbc_close_all();
   }
   
	 echo "<br>"; 
	 echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
	 echo "<br>";
	 echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
	 echo "</table>";
	 

  
 }
	odbc_close($conex_o);
	odbc_close_all();
?>