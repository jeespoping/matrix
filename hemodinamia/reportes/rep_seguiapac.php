<html>
<head>
<title>MATRIX - [REPORTE SEGUIMIENTO A PACIENTES]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_seguiapac.php'; 
	}
	
	function enter()
	{
		document.forms.rep_seguiapac.submit();
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


/*******************************************************************************************************************************************
 *                                             REPORTE PARA GRAFICAR PROCESOS PRIORITARIOS                                                  *
 ********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : INFORME DE SEGUIMIENTO A PACIENTES POR MES                                                                  |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : MAYO 10 DE 2011.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : MAYO 10 DE 2011.                                                                                            |
//DESCRIPCION			      : Este reporte sirve para el seguimiento a pacientes por mes.                                                 |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//hemodi_000009    : Tabla de Seguimiento a Pacientes.                                                                                      |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz=" 1.0 05-Mayo-2011";

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
encabezado("Seguimiento A Pacientes x Mes",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{

	$empre1='hemodi';

	//Forma
	echo "<form name='forma' action='rep_seguiapac.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

	if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
	{
		echo "<form name='rep_seguiapac' action='' method=post>";

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

		echo "<tr><td align=center colspan=2><br><input type='submit' id='searchsubmit' value='Generar'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar

		echo "</table>";
		echo '</div>';
		echo '</div>';
		echo '</div>';
			
	}
	else
	{

		echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
		echo "</tr>";
		echo "</table>";
		 
		echo "<table border=1 cellspacing=0 cellpadding=0 size='1000'>";
		echo "<tr>";
		echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>MES</b></td>";
        echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>PACIENTES AMBULATORIOS LLAMADOS</b></td>";
        echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>TOTAL PACIENTES AMBULATORIOS</b></td>";
        echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>% CUMPLIMIENTO</b></td>";
        echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>PACIENTES HOSPITALIZADOS LLAMADOS</b></td>";
        echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>TOTAL PACIENTES HOSPITALIZADOS</b></td>";
        echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>% CUMPLIMIENTO</b></td>";
        echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>TOTAL PACIENTES</b></td>";
		echo "</tr>";
		
		$mesi=SUBSTR(".$fec1.",6,2);
		$mesf=SUBSTR(".$fec2.",6,2);
		
		$arremes=Array();
		
		$nombremes='ENERO';
		$arremes[1]=$nombremes;
						
		$nombremes='FEBRERO';
		$arremes[2]=$nombremes;
		
		$nombremes='MARZO';
		$arremes[3]=$nombremes;
		
		$nombremes='ABRIL';
		$arremes[4]=$nombremes;
		
		$nombremes='MAYO';
		$arremes[5]=$nombremes;

		$nombremes='JUNIO';
		$arremes[6]=$nombremes;
		
		$nombremes='JULIO';
		$arremes[7]=$nombremes;
		
		$nombremes='AGOSTO';
		$arremes[8]=$nombremes;
		
		$nombremes='SEPTIEMBRE';
		$arremes[9]=$nombremes;
		
		$nombremes='OCTUBRE';
		$arremes[10]=$nombremes;
		
		$nombremes='NOVIEMBRE';
		$arremes[11]=$nombremes;
		
		$nombremes='DICIEMBRE';
		$arremes[12]=$nombremes;
		
		
        $sw=0;
		$arrepacamb  = Array();
		$arrepactamb = Array();
		$arrepachos  = Array();
		$arrepacthos = Array();

		for ($i=1;$i<=12;$i++)
		{
		 $arrepacamb[$i]  = 0;
		 $arrepactamb[$i] = 0;
		 $arrepachos[$i]  = 0;
		 $arrepacthos[$i] = 0;	
		}
		
		
		// Query para traer los pacientes ambulatorios llamados
		$query1 ="SELECT SUBSTRING(spfechapro,6,2) as mes,count(*) as cant"
		       ."   FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spllamadaefec = '2-SI'"
		        ."   AND sptippac <> 'H' "
		        ." GROUP by 1 "
		        ." ORDER by 1 ";

		//echo $query1."<br>";
			
		$err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);
		
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($err1);
			
		    switch ($row1[0])
			{
			 case '01':
				$sw=1;
				break;
			 case '02':
				$sw=2;
				break;
			 case '03':
				$sw=3;
				break;
			 case '04':
				$sw=4;
				break;
			 case '05':
				$sw=5;
				break;
			 case '06':
				$sw=6;
				break;
			 case '07':
				$sw=7;
				break;
			 case '08':
				$sw=8;
				break;
			 case '09':
				$sw=9;
				break;
			 case '10':
				$sw=10;
				break;
			 case '11':
				$sw=11;
				break;
			 case '12':
				$sw=12;
				break;
			}
			
			$arrepacamb[$sw]=$row1[1];
			
		}// fin del for
		
		
		//Query para traer el total de pacientes ambulatorios
		$query2 ="SELECT SUBSTRING(spfechapro,6,2) as mes,count(*) as cant"
		       ."   FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND sptippac <> 'H' "
		        ." GROUP by 1"
		        ." ORDER by 1";

		//echo $query1."<br>";
			
		$err2 = mysql_query($query2,$conex);
		$num2 = mysql_num_rows($err2);
		
		for ($i=1;$i<=$num2;$i++)
		{
			$row2 = mysql_fetch_array($err2);
		    
			switch ($row2[0])
			{
			 case '01':
				$sw=1;
				break;
			 case '02':
				$sw=2;
				break;
			 case '03':
				$sw=3;
				break;
			 case '04':
				$sw=4;
				break;
			 case '05':
				$sw=5;
				break;
			 case '06':
				$sw=6;
				break;
			 case '07':
				$sw=7;
				break;
			 case '08':
				$sw=8;
				break;
			 case '09':
				$sw=9;
				break;
			 case '10':
				$sw=10;
				break;
			 case '11':
				$sw=11;
				break;
			 case '12':
				$sw=12;
				break;
			}
			$arrepactamb[$sw]=$row2[1];
		}// fin del for
		
		//Query para traer los pacientes hospitalizados llamados
        $query3 ="SELECT SUBSTRING(spfechapro,6,2) as mes,count(*) as cant"
		       ."   FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND Spllamadaefec='2-SI'"
		        ."   AND sptippac = 'H' "
		        ." GROUP by 1"
		        ." ORDER by 1";

		//echo $query3."<br>";
			
		$err3 = mysql_query($query3,$conex);
		$num3 = mysql_num_rows($err3);
		
		for ($i=1;$i<=$num3;$i++)
		{
			$row3 = mysql_fetch_array($err3);

			switch ($row3[0])
			{
			 case '01':
				$sw=1;
				break;
			 case '02':
				$sw=2;
				break;
			 case '03':
				$sw=3;
				break;
			 case '04':
				$sw=4;
				break;
			 case '05':
				$sw=5;
				break;
			 case '06':
				$sw=6;
				break;
			 case '07':
				$sw=7;
				break;
			 case '08':
				$sw=8;
				break;
			 case '09':
				$sw=9;
				break;
			 case '10':
				$sw=10;
				break;
			 case '11':
				$sw=11;
				break;
			 case '12':
				$sw=12;
				break;
			}
			
			$arrepachos[$sw]=$row3[1];
		}// fin del for
		
		
		//Query para traer el total de pacientes hospitalizados
		$query4 ="SELECT SUBSTRING(spfechapro,6,2) as mes,count(*) as cant"
		       ."   FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND sptippac = 'H' "
		        ." GROUP by 1"
		        ." ORDER by 1";

		//echo $query1."<br>";
			
		$err4 = mysql_query($query4,$conex);
		$num4 = mysql_num_rows($err4);
		
		for ($i=1;$i<=$num4;$i++)
		{
			$row4 = mysql_fetch_array($err4);
			
		    switch ($row4[0])
			{
			 case '01':
				$sw=1;
				break;
			 case '02':
				$sw=2;
				break;
			 case '03':
				$sw=3;
				break;
			 case '04':
				$sw=4;
				break;
			 case '05':
				$sw=5;
				break;
			 case '06':
				$sw=6;
				break;
			 case '07':
				$sw=7;
				break;
			 case '08':
				$sw=8;
				break;
			 case '09':
				$sw=9;
				break;
			 case '10':
				$sw=10;
				break;
			 case '11':
				$sw=11;
				break;
			 case '12':
				$sw=12;
				break;
			}
			$arrepacthos[$sw]=$row4[1];
		}// fin del for
		
		
		//Query para traer el total de pacientes
		$query5 ="SELECT count(*) as cant"
		        ."  FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'";
		        
		//echo $query1."<br>";
			
		$err5 = mysql_query($query5,$conex);
		$num5 = mysql_num_rows($err5);
				
		//Query para traer el total de procedimientos
		$query6 ="CREATE TEMPORARY TABLE if not exists temp as "
		       ."SELECT count(*) as cant"
		       ."   FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spproce1 <> '00-NINGUNO'"
		        ." UNION ALL "
		        ."SELECT count(*) as cant"
		        ."  FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spproce2 <> '00-NINGUNO'"
		        ." UNION ALL "
                ."SELECT count(*) as cant"
		       ."   FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spproce3 <> '00-NINGUNO'"
		        ." UNION ALL "
		        ."SELECT count(*) as cant"
		        ."  FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spproce4 <> '00-NINGUNO'";
		         
		//echo $query1."<br>";
			
		$err6 = mysql_query($query6,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query6 . " - " . mysql_error());
		
		$query7 ="SELECT sum(cant)"
		        ."  FROM temp";
		
		$err7 = mysql_query($query7,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query7 . " - " . mysql_error());        
		$num7 = mysql_num_rows($err7);

		//Query para traer el total de pacientes
		$query8 ="CREATE TEMPORARY TABLE if not exists tempo as "
		        ."SELECT spcomplica1 as compli,count(*) as cant"
		        ."  FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spcomplica1 <> '00-NINGUNO'"
		        ." GROUP BY 1 "
		        ." UNION ALL "
		        ."SELECT spcomplica2 as compli,count(*) as cant"
		        ."  FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spcomplica2 <> '00-NINGUNO'"
                ." GROUP BY 1 "
		        ." UNION ALL "
		        ."SELECT spcomplica3 as compli,count(*) as cant"
		        ."  FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spcomplica3 <> '00-NINGUNO'"
                ." GROUP BY 1 "
		        ." UNION ALL "
		        ."SELECT spcomplica4 as compli,count(*) as cant"
		        ."  FROM ".$empre1."_000009"
		        ." WHERE spfechapro between '".$fec1."' and '".$fec2."'"
		        ."   AND spcomplica4 <> '00-NINGUNO'"
		        ." GROUP BY 1 ";
		
		//echo $query8."<br>";
			
		$err8 = mysql_query($query8,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query8 . " - " . mysql_error());

		$query9="SELECT compli,sum(cant)"
		        ." FROM tempo"
		        ." GROUP BY 1"
		        ." ORDER BY 1";
		
		$err9 = mysql_query($query9,$conex);
		$num9 = mysql_num_rows($err9);				
		
		$tot1=0;
		$tot2=0;
		$tot3=0;
		
		for ($i=1;$i<=12;$i++)
		{
         IF ($arrepactamb[$i]==0)
         {
          $tot1= number_format(($arrepacamb[$i]/1)*100);
         }
         else
         {
		  $tot1= number_format(($arrepacamb[$i]/$arrepactamb[$i])*100);
         }
         
         IF ($arrepacthos[$i]==0)
         {
         	$tot2= number_format(($arrepachos[$i]/1)*100);
         }
         ELSE
         {	
		  $tot2= number_format(($arrepachos[$i]/$arrepacthos[$i])*100);
         }
		 
         $tot3= $arrepactamb[$i]+$arrepacthos[$i];
		 
		 echo "<tr>";
		 echo "<td width=100><font size='1' color='#000000'><b>$arremes[$i]</b></font></td>";
		 echo "<td align=center width=100><font size='1' color='#000000'><b>$arrepacamb[$i]</b></font></td>";
		 echo "<td align=center width=100><font size='1' color='#000000'><b>$arrepactamb[$i]</b></font></td>";
		 echo "<td align=center width=100><font size='1' color='#000000'><b>$tot1</b></font></td>";
		 echo "<td align=center width=100><font size='1' color='#000000'><b>$arrepachos[$i]</b></font></td>";
		 echo "<td align=center width=100><font size='1' color='#000000'><b>$arrepacthos[$i]</b></font></td>";
   	     echo "<td align=center width=100><font size='1' color='#000000'><b>$tot2</b></font></td>";
		 echo "<td align=center width=100><font size='1' color='#000000'><b>$tot3</b></font></td>";
   	     echo "</tr>";
		
        }// fin del for
        echo "</table>";
        echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "</tr>";
        
        $row5 = mysql_fetch_array($err5);
        $row7 = mysql_fetch_array($err7);
        
        $totpro = $row7[0];
        
        echo "<table border=0 size='100'>";  //border=0 no muestra la cuadricula en 1 si.
		echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "<tr>";
		echo "<td align=left colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TOTAL PACIENTES: <i>".$row5[0]."</i></b></font></td>";
		echo "</tr>";
		echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "</tr>";
        echo "<tr>";
		echo "<td align=left colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TOTAL PROCEDIMIENTOS: <i>".$totpro."</i></b></font></td>";
		echo "</tr>";
		echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "</tr>";
        echo "<tr>";
		echo "</table>";
		
		echo "<table border=1 cellspacing=0 cellpadding=0 size='500'>";
		echo "<tr>";
		echo "<td bgcolor=#006699 align=center width=250><font text color=#FFFFFF size=2><b>COMPLICACIONES</b></td>";
        echo "<td bgcolor=#006699 align=center width=125><font text color=#FFFFFF size=2><b>CANTIDAD</b></td>";
        echo "<td bgcolor=#006699 align=center width=125><font text color=#FFFFFF size=2><b>% PROCEDIMIENTO</b></td>";
        echo "</tr>";
		
		for ($i=1;$i<=$num9;$i++)
		{
			$row9 = mysql_fetch_array($err9);
			$totporp=number_format((($row9[1]/$totpro)*100),2);
			
			echo "<tr>";
		    echo "<td width=250><font size='1' color='#000000'><b>$row9[0]</b></font></td>";
		    echo "<td align=CENTER width=125><font size='1' color='#000000'><b>$row9[1]</b></font></td>";
		    echo "<td align=CENTER width=125><font size='1' color='#000000'><b>$totporp</b></font></td>";
		    echo "</tr>";
		}

		echo "</table>";
		
        echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
		echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
		echo "</table>";

	}// cierre del else donde empieza la impresión

}
?>