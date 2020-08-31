<html>
<head>
<title>MATRIX - [REPORTE PARA LOS INFORMES MENSUALES]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_informes.php'; 
	}
	
	function enter()
	{
		document.forms.rep_informes.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");


/*******************************************************************************************************************************************
*                                             REPORTE PARA LOS INFORMES MENSUALES	                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los informes mensuales.                                                        |
//AUTOR				          :Ing. Juan David Londoño.                                                                        |
//FECHA CREACION			  :MAYO 18 DE 2011.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :18 de Mayo de 2011.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//citasfi_000009      : Tabla de Citas.
//citasfi_000002      : Tabla de Empresas.                                                                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");
$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
$wactualiz="1.0 18-Mayo-2011";

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
encabezado("INFORMES MENSUALES",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_informes.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_informes' action='' method=post>";
  
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
 	
   /////////////////////////////////////////////////////////////////////////// seleccion para el medico
   echo "<td class='fila2'colspan=2 width=190 >TIPO DE INFORME:<select name='ti' id='searchinput'</td>"; 
  	
   
   echo "<option></option>";
   echo "<option>01-INGRESOS Y ACTIVIDADES</option>";
   echo "<option>02-ACTIVIDADES X MEDICO</option>";
   echo "<option>03-ENTIDADES X EXAMEN</option>";
   echo "</select></td>";

 	echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>"; //submit osea el boton de OK o Aceptar
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
   
    echo "<input type='hidden' name='empresa' value='".$empresa."'>";
  }
 else // Cuando ya estan todos los datos escogidos
  {
   
   // informe de ingresos y actividades
   if ($ti=='01-INGRESOS Y ACTIVIDADES')
   {
   	   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>INFORME DE INGRESOS Y ACTIVIDADES</b></font></td>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
	   echo "</tr>";
	   echo "<tr><td><br></td></tr>";
	   echo "</table>";
	   echo "<br>";
	   
	   echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	   echo "<tr>";
	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO</font></td>"; 
	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE</font></td>";
	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TIPO</font></td>"; 
	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANTIDAD</font></td>";
	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR</font></td>";
	   echo "</tr>";
	  
	   /*$query = " SELECT Cod_exa, ".$empresa."_000011.Descripcion, Codunix, Conuni, Prouni, count(*) "
		           ."   FROM ".$empresa."_000009, ".$empresa."_000011, ".$empresa."_000002, ".$empresa."_000019 "
		           ."  WHERE Fecha between '".$fec1."' and '".$fec2."'"
		           ."    AND Asistida='on'"
		           ."    AND Codigo=Cod_exa"
		           ."    AND Cod_equipo=Cod_equ"
		           ."    AND Nit=Nit_res"
		           ."    AND Codmat=Cod_exa"
		           ."    AND Taruni=Codunix"
		           ."  GROUP BY 1, 2, 3, 4, 5"
		           ."  ORDER BY 1";
	           
	   $err = mysql_query($query,$conex);
	   $num = mysql_num_rows($err);
//echo mysql_errno() ."=". mysql_error();*/

		   	$query_o1="SELECT conm,nomc,'HOSPITALIZADOS' h,sum(cardetcan) can,sum(cardettot) tot "
					  ."FROM  facardet,inmegr,outer amerelfisi "
					 ."WHERE cardetfec between '".$fec1."' and '".$fec2."'"
					 ."  AND cardetanu='0'"
		           ."    AND cardetcco='1075'"
		           ."    AND cardetcon=conc"
		           ."    AND cardetcod=proce"
		           ."    AND cardethis=egrhis"
		           ."    AND cardetnum=egrnum"
		           ."    AND egrhos='H'"
		           ."  GROUP BY 1, 2, 3"
		           ."  union all"
		           ." SELECT conm,nomc,'AMBULATORIO' h,sum(cardetcan) can,sum(cardettot) tot "
					  ."FROM  facardet,inmegr,outer amerelfisi "
					 ."WHERE cardetfec between '".$fec1."' and '".$fec2."'"
					 ."  AND cardetanu='0'"
		           ."    AND cardetcco='1075'"
		           ."    AND cardetcon=conc"
		           ."    AND cardetcod=proce"
		           ."    AND cardethis=egrhis"
		           ."    AND cardetnum=egrnum"
		           ."    AND egrhos<>'H'"
		           ."  GROUP BY 1, 2, 3"
		           ."  union all"
		           ." SELECT conm,nomc,'HOSPITALIZADOS' h,sum(cardetcan) can,sum(cardettot) tot "
					  ."FROM  facardet,inpac,outer amerelfisi "
					 ."WHERE cardetfec between '".$fec1."' and '".$fec2."'"
					 ."  AND cardetanu='0'"
		           ."    AND cardetcco='1075'"
		           ."    AND cardetcon=conc"
		           ."    AND cardetcod=proce"
		           ."    AND cardethis=pachis"
		           ."    AND cardetnum=pacnum"
		           ."    AND pachos='H'"
		           ."  GROUP BY 1, 2, 3"
		           ."  union all"
		           ." SELECT conm,nomc,'AMBULATORIO' h,sum(cardetcan) can,sum(cardettot) tot "
					  ."FROM  facardet,inpac,outer amerelfisi "
					 ."WHERE cardetfec between '".$fec1."' and '".$fec2."'"
					 ."  AND cardetanu='0'"
		           ."    AND cardetcco='1075'"
		           ."    AND cardetcon=conc"
		           ."    AND cardetcod=proce"
		           ."    AND cardethis=pachis"
		           ."    AND cardetnum=pacnum"
		           ."    AND pachos<>'H'"
		           ."  GROUP BY 1, 2, 3"
		           ."  ORDER BY 1,2, 3"
		           ."  into temp tmp";

		           $err_o1 = odbc_do($conex_o,$query_o1);
		            //echo $query_o1;
		           
		     $query_o="SELECT conm,nomc,h,sum(can),sum(tot) "
					  ." FROM  tmp "
					//  ."WHERE conm not in ('06','07','08','10')"
					  ."group by 1,2,3 "
					  ."order by 1,2,3 "; 
		           
			//echo $query_o."<br>";
			$err_o = odbc_do($conex_o,$query_o);
			
   $Num_Filas = 0;
   $valtot=0;
   while (odbc_fetch_row($err_o))
	  {
	  	$Num_Filas++;
	  	
	  	
	  	$concepto=odbc_result($err_o,1);//concepto
		$nombre=odbc_result($err_o,2);//nombre
		$ha=odbc_result($err_o,3);//hosp-amb
		$cantidad=odbc_result($err_o,4);//cantidad
		$valor=odbc_result($err_o,5);//valor
	
		$valtot=$valtot+$valor;
		   echo "<tr>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$concepto."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$ha."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($cantidad,0,'.',',')."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valor,0,'.',',')."</font></td>";
		   echo "</tr>";
	  }
	  	   echo "<tr>";
	  	   echo "<td align=LEFT colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>VALOR TOTAL</b></font></td>";
		   echo "<td align=RIGHT colspan='4' bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".number_format($valtot,0,'.',',')."</font></td>";
		   echo "</tr>";

   }
   else if ($ti=='02-ACTIVIDADES X MEDICO')
   {
   	   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>INFORME DE ACTIVIDADES X MEDICO</b></font></td>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
	   echo "</tr>";
	   echo "<tr><td><br></td></tr>";
	   echo "</table>";
	   echo "<br>";
	   
	   //empieza la tabla
	   echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	   echo "<tr>";
	  
		$querym = "CREATE TEMPORARY TABLE if not exists medico as "
		         ." SELECT distinct Codigo, Descripcion  "
			     ."   FROM ".$empresa."_000010"
			     ."  WHERE Activo='A'";
		
		$errm = mysql_query($querym,$conex) or die("ERROR EN QUERY");  
	
		$querye = "CREATE TEMPORARY TABLE if not exists examen as "
		         ." SELECT distinct Codigo as codexa, Descripcion as nomexa  "
			     ."   FROM ".$empresa."_000011"
			     ."  WHERE Activo='A'"
			     ."  ORDER BY 1 ";
		
		$erre = mysql_query($querye,$conex) or die("ERROR EN QUERY");
		
		
		$queryep = " SELECT distinct Codigo as codexa, Descripcion as nomexa  "
			     ."   FROM ".$empresa."_000011"
			     ."  WHERE Activo='A'"
			     ."  ORDER BY 1 ";
		
	    $errep = mysql_query($queryep,$conex) or die("ERROR EN QUERY");
		$numep = mysql_num_rows($errep);
		//echo mysql_errno() ."=". mysql_error();
		
		echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>MEDICO</font></td>";
		$examen=Array();
		$cantexa=Array();
		$totexa=Array();
		
		for ($i=1;$i<=$numep;$i++)
		   	{
		   	   $rowep = mysql_fetch_array($errep);
		   	   $examen[$i]=$rowep[1];
		   	   $cantexa[$i]=0;
		   	   $totexa[$i]=0;
		   	   
		   	   
		   	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$rowep[1]."</font></td>"; 
			  
		   	}
		 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL</font></td>";
		 echo "</tr>";
		  
		   
	    $query = " SELECT Cod_equ, Descripcion,  Cod_exa , nomexa, count(*)  "
	           ."   FROM medico, examen, ".$empresa."_000009 "
	           ."  WHERE Fecha between '".$fec1."' and '".$fec2."'"
	           ."    AND Asistida='on'"
	           ."    AND Codigo=Cod_equ"
	           ."    AND Codexa=Cod_exa"
	           ."  GROUP BY 1, 3 "
	           ."  ORDER BY 1";
		           
		   $err = mysql_query($query,$conex);
		   $num = mysql_num_rows($err);
	//echo mysql_errno() ."=". mysql_error();
		$nommed=Array();
		
		
		$medant='';
		$swtitulo='SI';
		$k=0;
		
		
		
		   for ($i=1;$i<=$num;$i++)
		   	{
		   	
			  $row = mysql_fetch_array($err);
			   	
			 IF ($swtitulo=='SI')
			  {
			   $medant=$row[0];
			   $swtitulo='NO';
				 
			   $nommed[$k]=$row[1];
			   $k=$k+1;
			  }
			 
			IF ($medant==$row[0])
			{
			   	
			 for ($j=1;$j<=$numep;$j++)
			  {
			   		if ($row[3]==$examen[$j])
			   		{
			   			$cantexa[$j]=$row[4];
			   			$totexa[$j]=$row[4]+$totexa[$j];
			   			
			   		}
			  }
			} 
			ELSE
			{
				$totemp=0;
			 	echo "<tr>";
				echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nommed[0]."</font></td>";
				for ($i=1;$i<=$numep;$i++)
			   	{
			   	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cantexa[$i]."</font></td>"; 
			   	  $totemp=$totemp+$cantexa[$i];
				  
			   	}
			   	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>".$totemp."</font></td>";
			    
			   	echo "</tr>";
			    
			    //$swtitulo='SI';	
			 	
				for ($i=1;$i<=$numep;$i++)
			   	{
			 	   $cantexa[$i]=0;
			   	}
			    $k=0;
			    
			    $medant=$row[0];
			    $nommed[$k]=$row[1];
			    IF ($medant==$row[0])
			     {
			   	
			      for ($j=1;$j<=$numep;$j++)
			      {
			   		if ($row[3]==$examen[$j])
			   		{
			   			$cantexa[$j]=$row[4];
			   			$totexa[$j]=$row[4]+$totexa[$j];
			   		}
			      }
			     } 
			     
				}
			
			 }

			 
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>TOTALES</font></td>";
		      	 
   			$totemp=0;
			for ($j=1;$j<=$numep;$j++)
		      {
		   		echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>".$totexa[$j]."</font></td>"; 
		      	$totemp=$totemp+$totexa[$j];
		      }
			
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>".$totemp."</font></td>";
			   //echo "</tr>";
			
	   	  
	  
   }
   else if ($ti=='03-ENTIDADES X EXAMEN')
   {
   	   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>INFORME DE ENTIDADES X EXAMEN</b></font></td>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
	   echo "</tr>";
	   echo "<tr><td><br></td></tr>";
	   echo "</table>";
	   echo "<br>";
	   
	   //empieza la tabla
	   echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	   echo "<tr>";
	  
		$querym = "CREATE TEMPORARY TABLE if not exists entidad as "
		         ." SELECT distinct Nit, Descripcion  "
			     ."   FROM ".$empresa."_000002";
		
		$errm = mysql_query($querym,$conex) or die("ERROR EN QUERY");  
	
		$querye = "CREATE TEMPORARY TABLE if not exists examen as "
		         ." SELECT distinct Codigo as codexa, Descripcion as nomexa  "
			     ."   FROM ".$empresa."_000011"
			     ."  WHERE Activo='A'"
			     ."  ORDER BY 1 ";
		 //echo $querye;
		$erre = mysql_query($querye,$conex) or die("ERROR EN QUERY");
		
		
		$queryep = " SELECT distinct Codigo as codexa, Descripcion as nomexa  "
			     ."   FROM ".$empresa."_000011"
			     ."  WHERE Activo='A'"
			     ."  ORDER BY 1 ";
		
	    $errep = mysql_query($queryep,$conex) or die("ERROR EN QUERY");
		$numep = mysql_num_rows($errep);
		//echo mysql_errno() ."=". mysql_error();
		
		echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>EMPRESA</font></td>";
		$examen=Array();
		$cantexa=Array();
		$totexa=Array();
		
		for ($i=1;$i<=$numep;$i++)
		   	{
		   	   $rowep = mysql_fetch_array($errep);
		   	   $examen[$i]=$rowep[1];
		   	   $cantexa[$i]=0;
		   	   $totexa[$i]=0;
		   	   
		   	   
		   	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$rowep[1]."</font></td>"; 
			  
		   	}
		 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL</font></td>";
		 echo "</tr>";
		

	    $query = " SELECT Nit_res, Descripcion,  Cod_exa , nomexa, count(*)  "
	           ."   FROM entidad, examen, ".$empresa."_000009 "
	           ."  WHERE Fecha between '".$fec1."' and '".$fec2."'"
	           ."    AND Asistida='on'"
	           ."    AND Nit=Nit_res"
	           ."    AND Codexa=Cod_exa"
	           ."  GROUP BY 1,2, 3, 4 "
	           ."  ORDER BY 1";
		           
		   $err = mysql_query($query,$conex);
		   $num = mysql_num_rows($err);
	//echo mysql_errno() ."=". mysql_error();
		$nommed=Array();
		
	
		$medant='';
		$swtitulo='SI';
		$k=0;
		
		
		
		   for ($i=1;$i<=$num;$i++)
		   	{
		   	
			  $row = mysql_fetch_array($err);
			   	
			 IF ($swtitulo=='SI')
			  {
			   $medant=$row[0];
			   $swtitulo='NO';
				 
			   $nommed[$k]=$row[1];
			   $k=$k+1;
			  }
			 
			IF ($medant==$row[0])
			{
			   	
			 for ($j=1;$j<=$numep;$j++)
			  {
			   		if ($row[3]==$examen[$j])
			   		{
			   			$cantexa[$j]=$row[4];
			   			$totexa[$j]=$row[4]+$totexa[$j];
			   			
			   		}
			  }
			  
			} 
			ELSE
			{
			 	echo "<tr>";
				echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nommed[0]."</font></td>";
				$totemp=0;
				for ($i=1;$i<=$numep;$i++)
			   	{
			   	   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cantexa[$i]."</font></td>"; 
			   	   $totemp=$totemp+$cantexa[$i];
				  
			   	}
			    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>".$totemp."</font></td>";
			    echo "</tr>";
			    //$swtitulo='SI';	
			 	
				for ($i=1;$i<=$numep;$i++)
			   	{
			 	   $cantexa[$i]=0;
			   	}
			    $k=0;
			    
			    $nommed[$k]=$row[1];
			    $medant=$row[0];
			    
			    IF ($medant==$row[0])
			     {
			   	
			      for ($j=1;$j<=$numep;$j++)
			      {
			   		if ($row[3]==$examen[$j])
			   		{
			   			$cantexa[$j]=$row[4];
			   			$totexa[$j]=$row[4]+$totexa[$j];
			   		}
			      }
			     } 
			     
				}
			
			 }

			 
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>TOTALES</font></td>";

			$totemp=0;
   			for ($j=1;$j<=$numep;$j++)
		      {
		   		echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>".$totexa[$j]."</font></td>";
		   		$totemp=$totemp+$totexa[$j]; 
		      }
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><B>".$totemp."</font></td>";
			   //echo "</tr>";
	  
   }
	 echo "<br>"; 
	 echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
	 echo "<br>";
	 echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
	 echo "</table>";
   } 
  
  
}

?>