<html>
<head>
  <title>REPORTE DE HISTORIAS SIN FACTURAR POR FECHA INGRESO</title>
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
function Seleccionar()
{
	document.forma.submit();
}
</SCRIPT>
</head>
<?php
include_once("conex.php");
 /*************************************************************************************
   *     REPORTE DE HISTORIAS SIN FACTURAR POR FECHA                                  *
   *               DE CLINICA DEL SUR                                                 *  
 *************************************************************************************/
//=================================================================================================================================
//PROGRAMA: RepHisNoFac.php
//AUTOR: Gabriel Agudelo.
  $wautor="Gabriel Agudelo.";
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\RepCar.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
		//-------------------I------------------------I---------------------------------------------------------------------
		//	  FECHA           I     AUTOR              I   MODIFICACION
		//-------------------I------------------------I------------------------------------------------------------------------
		//  2006-10-27       I Gabriel Agudelo        I creación del script.
		//-------------------I------------------------I-----------------------------------------------------------------------
		//  2008-03-12		   Juan Esteban Lopez 		Se amplio la consulta con el fin de mostrar el nombre del paciente y la empresa responsable
		//-------------------I------------------------I-----------------------------------------------------------------------
	
//FECHA ULTIMA ACTUALIZACION 	: 2006-10-27 2:00 pm
  $wactualiz="(Versión octubre 27 de 2006)"; 

/*DESCRIPCION:Este reporte presenta las historias no facturadas por fecha de ingreso 

TABLAS QUE UTILIZA:
 $wbasedato."_000101: Archivo de ingreso de pacientes
 $wbasedato."_000106: Cargos de facturacion

 INCLUDES: 
  conex.php = include para conexión mysql            

 VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");    
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 =================================================================================================================================*/


 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 {
 	
//$wbasedato='clisur';

$key = substr($user,2,strlen($user));


//$conex=Conectarse(); 


echo "<form action='RepHisNoFac.php' method=post name='forma'>";

  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

  //Traigo la base de datos Principal de la empresa 
  $q = " SELECT empbda "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wbasedato=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp "; 
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res); 
  
  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {   
	      $row = mysql_fetch_array($res);
	      
	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];
	         
	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];
	         
	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];
	         
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
	         
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];   
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";



  $wentidad=$winstitucion;
  
  $hora = (string)date("H:i:s");
  $wnomprog="RepHisNoFac.php";  //nombre del reporte
  $wcf1="003366";  // color del fondo   -- Azul mas claro
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf3="006699";  //COLOR DEL FONDO 2  -- Azul OSCURO
  $wcf2="003366";  //COLOR DEL FONDO 3  -- AZUL
  $wcf4="99CCFF";  //COLOR DEL FONDO 4  -- AZUL
  $wcf5="00CCFF";
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
  $wfecha=date("Y-m-d");   
 
  
  
if (!isset($wfecini) or !isset($wfecfin) or !isset($resultado))  
{
	  echo "<center><table border=2>";
	  echo "<tr><td align=center rowspan=2 COLSPAN=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=490 HEIGHT=100></td></tr>";
	  echo "<tr><td align=center bgcolor=".$wcf3."><font size=5 text color=#FFFFFF><b>REPORTE DE HISTORIAS SIN FACTURAR POR FECHA DE INGRESO</b></font></td></tr>";
	  
	//INGRESO DE VARIABLES PARA EL REPORTE//
	if (!isset ($bandera))
	{
		 $wfecini=$wfecha;
		 $wfecfin=$wfecha;
	}
	
	echo "<tr>";  
	echo "<td bgcolor=".$wcf." align=center COLSPAN=2><b><font text color=".$wclfg.">FECHA INICIAL DE FACTURACION (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10></td>";
	echo "<td bgcolor=".$wcf." align=center ><b><font text color=".$wclfg.">FECHA FINAL DE FACTURACION  (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10></td>";
//	echo "<td bgcolor=".$wcf." align=center  COLSPAN=2><b><font text color=".$wclfg.">FECHA INICIAL DE FACTURACION (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10><HR><b><font text color=".$wclfg.">FECHA FINAL DE FACTURACION  (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10></td>";
	echo "</tr>";
// seleccionar tipo de fuente
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";	
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    echo "<tr><td align=center bgcolor=".$wcf." COLSPAN='4'><font text color=".$wclfg." ><b>";
	echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE ";      						
	echo "</font></b></td></tr></table></br>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";	   
} 

//MUESTRA DE DATOS DEL REPORTE
else
  {
	echo "<table border=0 align=center width=100%>";
    echo "<h6><tr><td align=left><B>Facturacion:</B>$wentidad</td>";
    echo "<td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
    echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>";  
    echo "<td align=right><B>Hora :</B> ".$hora."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
    echo "</table></br>";
	echo "<table border=0 align=center width=100%";
	echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";
	echo "<tr><td align=center><B>REPORTE DE HISTORIAS SIN FACTURAR POR FECHA DE INGRESO</B></td></tr>";
	echo "<tr><td align=center><B>Fecha inicial:</B> ".$wfecini."</td></tr>";
	echo "<tr><td align=center><B>Fecha final:</B> ".$wfecfin."</td></tr></h6>";
	echo "</table></br>";
		
    echo "<A href='RepHisNoFac.php?wfecini=".$wfecini."&wfecfin=".$wfecfin."&wemp_pmla=".$wemp_pmla."&bandera='1'><center>VOLVER</center></A><br>";
  	echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  	echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

/***********************************Consulto lo pedido ********************/
// SE HACE LA SELECCION DE LOS DATOS QUE NECESITO
	
	$q =  " SELECT ".$wbasedato."_000106.id, inghis, tcaring, tcarfec, tcarser, tcarconcod, tcarconnom, tcarprocod, tcarpronom, tcarcan, tcarvto,tcarfex, tcarfre, tcarusu, concat_ws(' ',tcarno1,tcarno2,tcarap1,tcarap2) as tcarnom, tcarres"
        ."  FROM  ".$wbasedato."_000101,".$wbasedato."_000106 "
        . " WHERE  ingfei between '".$wfecini."'"
        . " and '".$wfecfin."'"
        . " and tcarvto != tcarfex + tcarfre"
        //. " and tcarconcod != '6102' "
        //. " and tcarconcod != '9301' "
        //. " and tcarconcod != '9302' "
        . " and inghis = tcarhis"
        . " and ingnin = tcaring"
        . " and tcarfac = 'S'"
        . " and tcarest = 'on'"
        . " order by inghis,".$wbasedato."_000106.id, tcaring,tcarfec";
        
        $err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
	
	// inicializo las variables
	$bandera1=0;
	$j=1;
	$i=1;
	$wtotfac = 0;
	$wtotexe = 0;
	$wtotrec = 0;
	$whisfac=0;
	$whisexe=0;
	$whisrec=0;
	$wtcarhis=0;
	$wtcaring=0;	
	$pinto = 0;
	echo "<table border=0 >";
//ESTE CICLO MUESTRA LOS DATOS DE LA SELECCION
	while ($i <= $num) 
		{
			$row = mysql_fetch_array($err);
			
			if ($pinto==0)
			 	{
				 		$wtcarhis=$row[1];
						$wtcaring=$row[2];	
						echo '<tr>'; 
	  					echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>NRO REGISTRO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>HISTORIA</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>INGRESO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>FECHA CARGO</font></th>";
						echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>CENTRO COSTOS</font></th>";	        						               echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>NOMBRE DE PACIENTE</font></th>"	;			
          				echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>EMPRESA RESPONSABLE</font></th>";		
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>CONCEPTO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>DESCRIPCION</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>PROCEDIMIENTO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>DESCRIPCION</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>CANTIDAD</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>VALOR TOTAL</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>RECONOCIDO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>EXEDENTE</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>USUARIO</font></th>";
	        			echo '</tr>';
	        			$pinto=1;
   				}		
   				if (($wtcarhis!=$row[1]) or ($wtcaring!=$row[2]))	
					{
						echo '<tr>';	
						echo "<th align=left bgcolor=$wcf3 colspan='10'><font size=2 color='FFFFFF'>TOTAL HISTORIA</font></th>";
						echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
						echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
						echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
						echo "<th align=left bgcolor=$wcf3><font size=2 color='FFFFFF'>SALDO X FACTURAR</font></th>";
						echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format(($whisfac-$whisexe-$whisrec),0,'.',',')."</font></th>";
						echo "<th align=left bgcolor=$wcf3><font size=2 color='FFFFFF'> </font></th>";
						echo '</tr>';									 		
						$whisfac=0;
						$whisexe=0;
						$whisrec=0;
						$wtcarhis=$row[1];
						$wtcaring=$row[2];	
					}   				
   				$whisfac=$whisfac + $row[10];
				$whisexe=$whisexe + $row[11];
				$whisrec=$whisrec + $row[12];	 														
             	if (is_int ($j/2))
		 			{
						$coloresumido='FFFFFF';
						$j=$j+1;
					}
				else
					{	
						$coloresumido='DDDDDD';
						$j=$j+1;
					}
			   		echo '<tr>';
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[0]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[1]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[2]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[3]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[4]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[14]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[15]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[5]."</font></th>";
					echo "<th align=left bgcolor=$coloresumido><font size=2>".$row[6]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[7]."</font></th>";
					echo "<th align=left bgcolor=$coloresumido><font size=2>".$row[8]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[9]."</font></th>";
					echo "<th align=right bgcolor=$coloresumido><font size=2>".number_format($row[10],0,'.',',')."</font></th>";
					echo "<th align=right bgcolor=$coloresumido><font size=2>".number_format($row[11],0,'.',',')."</font></th>";
					echo "<th align=right bgcolor=$coloresumido><font size=2>".number_format($row[12],0,'.',',')."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[13]."</font></th>";
					echo '</tr>';
				
			$wtotfac = $wtotfac+$row[10];
			$wtotexe = $wtotexe+$row[11];
			$wtotrec = $wtotrec+$row[12];
			$i= $i + 1;
		}
	if ($num==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningún documento en el rango de fechas seleccionado</td><tr>";
		}
	else
		{
			echo '<tr>';	
			echo "<th align=left bgcolor=$wcf3 colspan='10'><font size=2 color='FFFFFF'>TOTAL HISTORIA</font></th>";
			echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
			echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
			echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
			echo "<th align=left bgcolor=$wcf3 colspan='10'><font size=2 color='FFFFFF'> </font></th>";
			echo '</tr>';									 		
			echo '<tr>';	
			echo "<th align=left bgcolor=$wcf2 colspan='10'><font size=2 color='FFFFFF'>TOTAL GENERAL</font></th>";
			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotexe,0,'.',',')."</font></th>";
			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotrec,0,'.',',')."</font></th>";
			echo "<th align=left bgcolor=$wcf2 colspan='10'><font size=2 color='FFFFFF'> </font></th>";
			echo '</tr>';									 	
		}
    echo "</table>";
	echo "</br><center><A href='RepHisNoFac.php?wfecini=".$wfecini."&wfecfin=".$wfecfin."&wemp_pmla=".$wemp_pmla."&bandera='1'>VOLVER</A></center>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";   
}
}// del fin de sesion
?>
</body>
</html>