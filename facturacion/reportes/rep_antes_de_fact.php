<html>
<head>
<title>MATRIX - [CONSULTA ANTES DE GENERAR UNA FACTURA]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_antes_de_fact.php'; 
	}
	
	function enter()
	{
		document.forms.rep_antes_de_fact.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");


/*******************************************************************************************************************************************
*                                             REPORTE PARA VER ANTES DE GENERAR UNA FACTURA                                                *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver el valor de los cargos antes de generar la factura en unix.                                |
//AUTOR				          :Ing. Gustavo Alberto Avendaño Rivera.                                                                       |
//FECHA CREACION			  :JULIO 23 DE 2012.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :23 de Julio de 2012.                                                                                        |
//TABLAS UTILIZADAS   :                                                                                                                    | 
//                                                                                                                                         |
//facardet  en unix   : Tabla de cargos.                                                                                                   |
//inpacmre  en unix   : Tabla de Empresa por historia.                                                                                     |
//==========================================================================================================================================
include_once("root/comun.php");
$conex     = obtenerConexionBD("matrix");
$conex_o   = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
$wactualiz = "1.0 23-Julio-2012";

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
encabezado("CONSULTA VALOR DE LOS CARGOS ANTES DE FACTURAR DETALLADO O RESUMIDO",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_antes_de_fact.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($emp) or $emp=='' or !isset($com) or $com=='' or !isset($fec1) or !isset($fec2))
  {
  	
  	echo "<form name='rep_antes_de_fact' action='' method=post>";
  
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
 	echo "<td class='fila1' width=190>Fecha Inicial de los cargos</td>";
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final de los cargos</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";
 	
 	 	
   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Fuente Factura
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD colspan=3><b><font text color=#003366 size=2><i>EMPRESA<i><br></font><bgcolor='#dddddd' aling=center><input style='text-transform: uppercase;' type='input' size=11 maxlength=15 name='emp'></td>";
   echo "</Tr>";
   
   if (isset($emp))
   {
    $emp=strtoupper($emp);
   }
   else 
   {
    $emp='';	
   }

   
   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Comercial o Generico
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD colspan=3><b><font text color=#003366 size=2><i>(D)etallado o (R)esumido<i><br></font><bgcolor='#dddddd' aling=center><input style='text-transform: uppercase;' type='TEXT' name='com' size=2 maxlength=2 id='com'></td>";
   echo "</Tr>";
   
   if (isset($com))
   {
    $com=strtoupper($com);
   }
   else 
   {
    $com='';	
   }
  
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>"; //submit osea el boton de Generar o Aceptar
   echo "</tr>";
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
   
   $emp=strtoupper($emp);
   $com=strtoupper($com);
  	
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>VALOR X FACTURAR DE ESTA EMPRESA</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>EMPRESA : <i>".$emp."</i></b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL DE LOS CARGOS: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL DE LOS CARGOS: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
  		
	IF ( $com=='D')
	{
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>HISTORIA</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>INGRESO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR X FACTURAR</font></td>";
	echo "</tr>";
	}
	ELSE
	{
	  echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	  echo "<tr>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR X FACTURAR</font></td>"; 
	  echo "</tr>";	
	}
	
	
    $query_o1="SELECT pacmrehis his,pacmrenum num,pacmretar tar"
             ."  FROM inpacmre"
             ." WHERE pacmrecer='$emp'"
             ."   AND pacmreind='P'"
             ."  INTO temp taremp";
             
     //echo $query_o."<br>";
	$err_o1 = odbc_do($conex_o,$query_o1);
	
	$query_o2="SELECT cardethis his,cardetnum num,sum(cardettot) tot"
             ."  FROM taremp,facardet"
             ." WHERE cardetfec between '".$fec1."' and '".$fec2."'" 
             ."   AND cardethis=his"
             ."   AND cardetnum=num"
             ."   AND tar=cardettar"
             ."   AND cardetvfa<>cardettot"
             ."   AND cardetanu='0'"
             ."   AND cardetfac='S'"
             ." GROUP by 1,2"
             ." ORDER by 1,2";
   
	$err_o2 = odbc_do($conex_o,$query_o2);
	
   	
   $tottal=0;
   
   while (odbc_fetch_row($err_o2))
	{
	 $Num_Filas++;
	 	
	 $his     = odbc_result($err_o2,1);//historia
	 $num     = odbc_result($err_o2,2);//ingreso
	 $val     = odbc_result($err_o2,3);//valor x facturar

	 IF ($com=="D")
	 {
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$his."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$num."</b></font></td>";
      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($val)."</font></td>";
	  echo "</tr>";
	 }
	 $tottal=$tottal+$val;
	 
   }
	
   IF ($com=="D")
   {
	
	 echo "<tr>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL GENERAL</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
     echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($tottal)."</font></td>";
	 echo "</tr>";
   }
   ELSE
   {
    echo "<tr>";
    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($tottal)."</font></td>";
	echo "</tr>";		
   }
   
   echo "<br>"; 

   echo "</table>";
  
 // Acá empieza el query para saber de esas historias que anticipos tienen en el rango de fechas dadas.
   
 IF ( $com=='D')
  {
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>HISTORIA</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>INGRESO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>FTE</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>ANTICIPO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>FECHA_ANTICIPO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR ANTICIPO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>DOCUMENTO_IDENT</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRES</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>APELLIDO_1</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>APELLIDO_2</font></td>";
	echo "</tr>";
  }
  ELSE
  {	
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR TOTAL ANTICIPOS</font></td>"; 
	echo "</tr>";	
  }

 $query_o3="SELECT cardethis his,cardetnum num"
          ."  FROM taremp,facardet"
          ." WHERE cardetfec between '".$fec1."' and '".$fec2."'"
          ."   AND cardethis=his"
          ."   AND cardetnum=num"
          ."   AND tar=cardettar"
          ."   AND cardetvfa<>cardettot"
          ."   AND cardetanu='0'"
          ."   AND cardetfac='S'"
          ." GROUP by 1,2"
          ." INTO temp tmpant1";

 $err_o3 = odbc_do($conex_o,$query_o3);

 $query_o4="SELECT his,num,pacced ced"
          ."  FROM tmpant1,inpac"
          ." WHERE his=pachis"
          ."   AND num=pacnum"
          ." UNION ALL"
          ." SELECT his,num,pacced ced"
          ."   FROM tmpant1,inpaci"
          ."  WHERE his=pachis"
          ."    AND num=pacnum"
          ."   INTO temp tmpant";

 $err_o4 = odbc_do($conex_o,$query_o4);
          
 $query_o5="SELECT his,max(num) num,ced"
          ."  FROM tmpant"
          ." GROUP BY 1,3"
          ." INTO TEMP tmpantced";
          
 $err_o5 = odbc_do($conex_o,$query_o5);         

 $query_o6="SELECT his,num,ced,sallinfue,sallindoc,sallinfec,sallinval,max(antpacsec) sec"
          ."  FROM tmpantced,casallin,anantpac"
          ." WHERE antpacced=ced"
          ."   AND antpacfue=sallinfue"
          ."   AND antpacdoc=sallindoc"
          ."   AND sallinced='$emp'"
          ."   AND sallinfec between '".$fec1."' and '".$fec2."'"
          ." GROUP BY 1,2,3,4,5,6,7"
          ." ORDER BY 1,2,3,4"
          ." INTO TEMP tmpanti";
          
  $err_o6 = odbc_do($conex_o,$query_o6);         

 $query_o7="SELECT his,num,sallinfue,sallindoc,sallinfec,sallinval,antpacced,antpacnom,antpacap1,antpacap2"
         ."   FROM tmpanti,anantpac,inpac"
         ."  WHERE sallinfue=antpacfue"
         ."    AND sallindoc=antpacdoc"
         ."    AND sec=antpacsec"
         ."    AND his=pachis"
         ."    AND num=pacnum"
         ."    AND antpacap2 is not null"
         ."  UNION ALL"
         ." SELECT his,num,sallinfue,sallindoc,sallinfec,sallinval,antpacced,antpacnom,antpacap1,'' antpacap2"
         ."   FROM tmpanti,anantpac,inpac"
         ."  WHERE sallinfue=antpacfue"
         ."    AND sallindoc=antpacdoc"
         ."    AND sec=antpacsec"
         ."    AND his=pachis"
         ."    AND num=pacnum"
         ."    AND antpacap2 is null"
         ."  UNION ALL"
         ." SELECT his,num,sallinfue,sallindoc,sallinfec,sallinval,antpacced,antpacnom,antpacap1,antpacap2"
         ."   FROM tmpanti,anantpac,inpaci"
         ."  WHERE sallinfue=antpacfue"
         ."    AND sallindoc=antpacdoc"
         ."    AND sec=antpacsec"
         ."    AND his=pachis"
         ."    AND num=pacnum"
         ."    AND antpacap2 is not null"
         ."  UNION ALL"
         ." SELECT his,num,sallinfue,sallindoc,sallinfec,sallinval,antpacced,antpacnom,antpacap1,'' antpacap2"
         ."   FROM tmpanti,anantpac,inpaci"
         ."  WHERE sallinfue=antpacfue"
         ."    AND sallindoc=antpacdoc"
         ."    AND sec=antpacsec"
         ."    AND his=pachis"
         ."    AND num=pacnum"
         ."    AND antpacap2 is null"
         ." ORDER BY 1,2,3,4";
   
  $err_o7 = odbc_do($conex_o,$query_o7); 
	
  
  $tottal=0;
   
   while (odbc_fetch_row($err_o7))
	{
	 $Num_Filas++;
	 	
	 $his = odbc_result($err_o7,1); //historia
	 $num = odbc_result($err_o7,2); //ingreso
	 $fue = odbc_result($err_o7,3); //fte
	 $ant = odbc_result($err_o7,4); //anticipo
	 $fec = odbc_result($err_o7,5); //fecha anticipo
	 $val = odbc_result($err_o7,6); //valor anticipo
	 $ced = odbc_result($err_o7,7); //cedula
	 $nom = odbc_result($err_o7,8); //nombre_paciente
	 $ap1 = odbc_result($err_o7,9); //apellido1_paciente
	 $ap2 = odbc_result($err_o7,10);//apellido2_paciente

	 IF ($com=="D")
	 {
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$his."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$num."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fue."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ant."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fec."</b></font></td>";
      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($val)."</font></td>";
      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ced."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$nom."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ap1."</b></font></td>";
	  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ap2."</b></font></td>";
      echo "</tr>";
	 }
	 $tottal=$tottal+$val;
	 
   }
	
   IF ($com=="D")
   {
	
	 echo "<tr>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL GENERAL</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
     echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($tottal)."</font></td>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
	 echo "</tr>";
   }
   ELSE
   {
    echo "<tr>";
    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($tottal)."</font></td>";
	echo "</tr>";		
   }
   
   echo "<br>"; 

   echo "</table>";
      
 }
 
}
odbc_close($conex_o);
odbc_close_all();
?>