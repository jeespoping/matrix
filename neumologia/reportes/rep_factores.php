<html>
<head>
<title>MATRIX - [REPORTE FACTORES RIESGO GENERALES]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_factores.php'; 
	}
	
	function enter()
	{
		document.forms.rep_pendientes.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE FACTORES DE RIESGO PARA EL PROCEDIMIENTO                                             *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los factores de riesgo para el procedimiento.                                               |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JUNIO 30 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :30 de Junio de 2010.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//neumo_000003      : Tabla de registro de atencion unidad de neumologia.                                                                   |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 30-Junio-2010";

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
encabezado("Factores Riesgo Generales",$wactualiz,"clinica");

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
 echo "<form name='rep_factores' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_factores' action='' method=post>";
  
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
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";

    echo "<br>";
    echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
  }
 else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>FACTORES DE RIESGO GENERALES</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   $query1 =" SELECT siceedad65,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceedad65='on'"
           ."  GROUP BY siceedad65";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query1."<br>";
      
   $query2 =" SELECT siceepoc,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceepoc='on'"
           ."  GROUP BY siceepoc";
           
   $err2 = mysql_query($query2,$conex);
   $num2 = mysql_num_rows($err2);
   
   //echo $query1."<br>";
   
   $query3 =" SELECT siceasma,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceasma='on'"
           ."  GROUP BY siceasma";
           
   $err3 = mysql_query($query3,$conex);
   $num3 = mysql_num_rows($err3);
   
   //echo $query1."<br>";
   $query4 =" SELECT siceobsfibro,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceobsfibro='on'"
           ."  GROUP BY siceobsfibro";
           
   $err4 = mysql_query($query4,$conex);
   $num4 = mysql_num_rows($err4);
   
   //echo $query1."<br>";
   $query5 =" SELECT Sicehepabc,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND Sicehepabc='on'"
           ."  GROUP BY Sicehepabc";
           
   $err5 = mysql_query($query5,$conex);
   $num5 = mysql_num_rows($err5);
   
   //echo $query1."<br>";
   $query6 =" SELECT sicevih,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicevih='on'"
           ."  GROUP BY sicevih";
           
   $err6 = mysql_query($query6,$conex);
   $num6 = mysql_num_rows($err6);
   
   //echo $query1."<br>";
   $query7 =" SELECT sicebronco,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicebronco='on'"
           ."  GROUP BY sicebronco";
           
   $err7 = mysql_query($query7,$conex);
   $num7 = mysql_num_rows($err7);
   
   //echo $query1."<br>";
   $query8 =" SELECT sicemehoma,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicemehoma='on'"
           ."  GROUP BY sicemehoma";
           
   $err8 = mysql_query($query8,$conex);
   $num8 = mysql_num_rows($err8);
   
   //echo $query1."<br>";
   $query9 =" SELECT sicefalla,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicefalla='on'"
           ."  GROUP BY sicefalla";
           
   $err9 = mysql_query($query9,$conex);
   $num9 = mysql_num_rows($err9);
   
   //echo $query1."<br>";
   $query10 =" SELECT sicehtano,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicehtano='on'"
           ."  GROUP BY sicehtano";
           
   $err10 = mysql_query($query10,$conex);
   $num10 = mysql_num_rows($err10);
   
   //echo $query1."<br>";
   $query11 =" SELECT siceevento,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceevento='on'"
           ."  GROUP BY siceevento";
           
   $err11 = mysql_query($query11,$conex);
   $num11 = mysql_num_rows($err11);
   
   //echo $query1."<br>";
   $query12 =" SELECT Siceineshemo,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND Siceineshemo='on'"
           ."  GROUP BY Siceineshemo";
           
   $err12 = mysql_query($query12,$conex);
   $num12 = mysql_num_rows($err12);
   
   //echo $query1."<br>";
   $query13=" SELECT sicetroseve,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicetroseve='on'"
           ."  GROUP BY sicetroseve";
           
   $err13 = mysql_query($query13,$conex);
   $num13 = mysql_num_rows($err13);
   
   //echo $query1."<br>";
   $query14 =" SELECT sicehemdis,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicehemdis='on'"
           ."  GROUP BY sicehemdis";
           
   $err14 = mysql_query($query14,$conex);
   $num14 = mysql_num_rows($err14);
   
   //echo $query1."<br>";
   $query15 =" SELECT siceantico,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceantico='on'"
           ."  GROUP BY siceantico";
           
   $err15 = mysql_query($query15,$conex);
   $num15 = mysql_num_rows($err15);
   
   //echo $query1."<br>";
   $query16 =" SELECT siceproanti,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceproanti='on'"
           ." GROUP BY siceproanti";
           
   $err16 = mysql_query($query16,$conex);
   $num16 = mysql_num_rows($err16);
   
   //echo $query1."<br>";
   $query17=" SELECT Siceasaaines,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND Siceasaaines='on'"
           ."  GROUP BY Siceasaaines";
           
   $err17 = mysql_query($query17,$conex);
   $num17 = mysql_num_rows($err17);
   
   //echo $query1."<br>";
   $query18=" SELECT siceinmunosu,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND siceinmunosu='on'"
           ."  GROUP BY siceinmunosu";
           
   $err18 = mysql_query($query18,$conex);
   $num18 = mysql_num_rows($err18);
   
   //echo $query1."<br>";
   
   echo "<br>";
   
   echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='100'>";
   echo "<tr> ";
   echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>FACTORES DE RIESGO</b></td>";
   echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>TOTAL</b></td>";
   echo "</tr>";

   IF ($num1<>0)
   {
    $row1 = mysql_fetch_array($err1);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>EDAD > 65 AÑOS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>EDAD > 65 AÑOS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >";
   }
   
   IF ($num2<>0)
   {
    $row1 = mysql_fetch_array($err2);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>EPOC</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";
   }
   ELSE
   {
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>EPOC</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >";
   }
   
   IF ($num3<>0)
   {
    $row1 = mysql_fetch_array($err3);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>ASMA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>ASMA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num4<>0)
   {
    $row1 = mysql_fetch_array($err4);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>OBSTRUCCION-FIBROSIS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>OBSTRUCCION-FIBROSIS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num5<>0)
   {
    $row1 = mysql_fetch_array($err5);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HEPATITIS B ó C</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HEPATITIS B ó C</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }   
   
   IF ($num6<>0)
   {
    $row1 = mysql_fetch_array($err6);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>VIH-SIDA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>VIH-SIDA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num7<>0)
   {
    $row1 = mysql_fetch_array($err7);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>BRONCOESPASMO SEVERO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>BRONCOESPASMO SEVERO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num8<>0)
   {
    $row1 = mysql_fetch_array($err8);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HEMOPTISIS MASIVA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HEMOPTISIS MASIVA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num9<>0)
   {
    $row1 = mysql_fetch_array($err9);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>FALLA RESPIRATORIA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>FALLA RESPIRATORIA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
      
   IF ($num10<>0)
   {
    $row1 = mysql_fetch_array($err10);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HTA NO CONTROLADA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HTA NO CONTROLADA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num11<>0)
   {
    $row1 = mysql_fetch_array($err11);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>EVENTO CORONARIO < 3 SEMANAS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>EVENTO CORONARIO < 3 SEMANAS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }   
   
   IF ($num12<>0)
   {
    $row1 = mysql_fetch_array($err12);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>INESTABILIDAD HEMODINAMICA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>INESTABILIDAD HEMODINAMICA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num13<>0)
   {
    $row1 = mysql_fetch_array($err13);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>TROMBOCITOPENIA SEVERA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>TROMBOCITOPENIA SEVERA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num14<>0)
   {
    $row1 = mysql_fetch_array($err14);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HEMOFILIA - DISCRASIAS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>HEMOFILIA - DISCRASIAS</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }

   IF ($num15<>0)
   {
    $row1 = mysql_fetch_array($err15);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>ANTICOAGULACION</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>ANTICOAGULACION</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num16<>0)
   {
    $row1 = mysql_fetch_array($err16);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>PROFILAXIS ANTITROMBOTICA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>PROFILAXIS ANTITROMBOTICA</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }   
   
   IF ($num17<>0)
   {
    $row1 = mysql_fetch_array($err17);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>ASA - AINES</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>ASA - AINES</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }   
   
   IF ($num18<>0)
   {
    $row1 = mysql_fetch_array($err18);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>INMUNOSUPRESION</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>INMUNOSUPRESION</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }   
   echo "</table>";   

   echo "<br>";
   
   
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>