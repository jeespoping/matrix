<html>
<head>
<title>PLANTILLA ÚNICA ESTADOS DE CUENTA - CARTERA</title>
<style type="text/css">
BODY           
{   
    font-family: Verdana;
    font-size: 10pt;
    margin: 0px;
}
</style>
</body>
</html>

<?php
include_once("conex.php");
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inaviñitado el teclado.
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                          PLANTILLA ÚNICA ESTADOS  DE CUENTA - CARTERA                                                    *
********************************************************************************************************************************************/
//==========================================================================================================================================
//PROGRAMA				      :rep_plantillaUEC.                                                                                            |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Agosto 14 DE 2014.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :14 de Agosto de 2014.                                                                                        |
//DESCRIPCION			      :Este programa sirve para imprimir la plantilla única estados de cuenta de los saldos de cartera x responsable|
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//amepuec           : Tabla de movimiento del rsalenveda, programa de unix que llena esta tabla.                                            | 
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 21-Agosto-2014";

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

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 
 if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
 
 $q =  " SELECT Descripcion  "
	  ."   FROM  usuarios "
	  ."  WHERE  Codigo = '".$wuser."' ";
	  
  //echo $q. "<br>";	  
	  
  $err3 = mysql_query($q,$conex);
  $num3 = mysql_num_rows($err3);
   
  $row =mysql_fetch_array($err3);

  $emple = $row[0]; //nombre empleado
 
  
 //Conexion base de datos
 $conexi=odbc_connect('facturacion','','')
	       or die("No se realizo conexión con la BD de Facturación");

	       //Forma
 echo "<form name='forma' action='rep_plantillaUEC.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($carg) or $carg == '' or !isset($tip) or $tip == '')
  {
  	echo "<form name='rep_plantillaUEC' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

 	//FUENTE Y CARGO DE AYUDAS
 	echo "<tr><td align='CENTER' colspan=2><b>CLÍNICA LAS AMERICAS - CARTERA </b></td></tr>";
	echo "<tr><td align='CENTER' colspan=2>PLANTILLA ÚNICA ESTADOS DE CUENTA</td></tr>";
	echo"<tr><td align='CENTER' bgcolor=#cccccc >(C)ODIGO o (N)IT :</td><td align='CENTER' bgcolor=#cccccc ><input type='input' name='tip'></td></tr>";
	echo"<tr><td align='CENTER' bgcolor=#cccccc >NIT PLANTILLA :</td><td align='CENTER' bgcolor=#cccccc ><input type='input' name='carg'></td></tr>";
	echo "<tr><td align='CENTER' colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>"; //submit osea el boton de GENERAR o Aceptar
    echo "</table>";
   
   
  }
 else // Cuando ya estan todos los datos escogidos
 {	
   $carg = strtoupper($carg);
   $tip  = strtoupper($tip);
  	
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
  
  IF ($tip=="N")
  {
      
   $query1=" SELECT empnom res,feccorte "
		  ."  FROM inemp,amepuec"
		  ." WHERE empcod='".$carg."'"
		  ."   AND empnit=nit"
		  ." GROUP BY 1,2";
		    
   //echo $query1."<br>";
   $err_o1 = odbc_do($conexi,$query1);
  }
  ELSE
  {
   $query1=" SELECT empnom res,feccorte "
	 	  ."  FROM amepuec,inemp"
		  ." WHERE empcod='".$carg."'"
		  ."   AND empcod=nit"
		  ." GROUP BY 1,2";
		    
    //echo $query1."<br>";
    $err_o1 = odbc_do($conexi,$query1);
  }   
			
			
  $Num_Filas = 0;	
  	
  echo "<table border=1 align=left size='100' cellpadding='0'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";	
  echo "<td align='CENTER' colspan='2'><IMG SRC='/MATRIX/images/medical/calidad/clnicanuevo.gif' WIDTH=140 HEIGHT=50></td>"; //trae el logo de la clinica.
  echo "<td align='CENTER' colspan='9' bgcolor='#FFFFFF' ><font size='3' text color='#003366'><b>PLANTILLA ÚNICA ESTADOS DE CUENTA</b></font></td>";
  echo "</tr>";
  
  echo "<tr>";	
  echo "<td align='CENTER' colspan='2' bgcolor='#FFFFFF' ><font size='1' text color='#003366'><b>Gestión de la Calidad</b></font></td>";
  echo "<td align='CENTER' colspan='9' bgcolor='#FFFFFF' ><font size='1' text color='#003366'><b>Cartera</b></font></td>";
  echo "</tr>"; 
 

  
  while (odbc_fetch_row($err_o1))
   {
	 $Num_Filas++;
	  	
	 $res1  = odbc_result($err_o1,1);//ENTIDAD
	 $fecc1 = odbc_result($err_o1,2);//FECHA CORTE
	 
	 echo "<tr>";
     echo "<td colspan='11'>&nbsp;</td>";
     echo "</tr>";

	 
	 echo "<tr>";
	 echo "<td align=LEFT colspan='2' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>ENTIDAD :</b></font></td>";
	 echo "<td align=LEFT colspan='9' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>".$res1."</b></font></td>";
	 echo "</tr>";
	 	 
	 echo "<tr>";
     echo "<td align=LEFT colspan='2' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>FECHA CORTE :</b></font></td>";
	 echo "<td align=LEFT colspan='9' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>".$fecc1."</b></font></td>";
	 echo "</tr>";
	 echo "<tr>";
     echo "<td colspan='11'>&nbsp;</td>";
     echo "</tr>";
	 	
   }

   
   IF ($tip=="N")
   {
   
   $querynit="SELECT empcod,empnit,empnom"
            ."  FROM inemp"
			." WHERE empnit='".$carg."'"
			." GROUP BY 1,2,3"
			." INTO TEMP tmpemp";
			
   $err = odbc_do($conexi,$querynit);
   }
   ELSE
   {
    $querynit="SELECT empcod,empcod empnit,empnom"
             ."  FROM inemp"
			 ." WHERE empcod='".$carg."'"
			 ." GROUP BY 1,2,3"
			 ." INTO TEMP tmpemp";
			
    $err = odbc_do($conexi,$querynit);
   } 
   
   $querycan="SELECT count(*)"
            ."   FROM amepuec,tmpemp"
            ."  WHERE estado in ('RD','RV','RC','GT','GL')"
            ."    AND dias between -300 and 30"
		    ."    AND nit=empcod";
            
   
   $errcan = odbc_do($conexi,$querycan);
   $canti  = odbc_result($errcan,1);//cantidad para saber que no traiga nulos			
   
  IF ($canti > 0) 
  {
   //==============================
   // TRAE EL VALOR DE 1 A 30 DIAS 
   //==============================
   
  $query2="SELECT empnit nit1_30,empnom res1_30,sum(vlrsaldo) vlr1_30"
         ."   FROM amepuec,tmpemp"
         ."  WHERE estado in ('RD','RV','RC','GT','GL')"
         ."    AND dias between -300 and 30"
		 ."    AND nit=empcod"
         ."  GROUP by 1,2"
         ."  into temp tmp1_301";
		    
  //echo $query2."<br>";
  $err_o2 = odbc_do($conexi,$query2);

  $query21="SELECT nit1_30,sum(vlr1_30)"
          ."  FROM tmp1_301"
		  ." GROUP by 1";
		  
  //echo $query21."<br>";
  $err_o21 = odbc_do($conexi,$query21);		  
          
  $Num_Filas = 0;	
	
  while (odbc_fetch_row($err_o21))
   {
	 $Num_Filas++;
	  	
	 $nit1_30   = odbc_result($err_o21,1);//Entidad
	 $vlr1_30   = odbc_result($err_o21,2);//Vlr 1 A 30
   }
  }
  ELSE
  {
   $nit1_30 = $carg;
   $vlr1_30 = 0;
  }
   
   
    $querycan="SELECT count(*)"
             ."   FROM amepuec,tmpemp"
             ."  WHERE estado in ('RD','RV','RC','GT','GL')"
             ."    AND dias between 31 and 60"
		     ."    AND nit=empcod";
       
   
   $errcan = odbc_do($conexi,$querycan);
   $canti  = odbc_result($errcan,1);//cantidad para saber que no traiga nulos			
   
  IF ($canti > 0) 
  {
   //==============================
   // TRAE EL VALOR DE 31 A 60 DIAS 
   //==============================
   
  $query2="SELECT empnit,empnom,sum(vlrsaldo) vlr31_60"
         ."   FROM amepuec,tmpemp"
         ."  WHERE estado in ('RD','RV','RC','GT','GL')"
         ."    AND dias between 31 and 60"
		 ."    AND nit=empcod"
         ."  GROUP by 1,2"
         ."  into temp tmp31_601";
		    
  //echo $query2."<br>";
  $err_o2 = odbc_do($conexi,$query2);

  $query21="SELECT sum(vlr31_60)"
          ."  FROM tmp31_601";
				  
  //echo $query21."<br>";
  $err_o21 = odbc_do($conexi,$query21);		  
          
  $Num_Filas = 0;	
	
    while (odbc_fetch_row($err_o21))
    {
	 $Num_Filas++;
	  	
	 $vlr31_60   = odbc_result($err_o21,1);//Vlr 31 A 60
    }
   }
   ELSE
   {
     $vlr31_60 = 0;
   }
   
   $querycan="SELECT count(*)"
            ."   FROM amepuec,tmpemp"
            ."  WHERE estado in ('RD','RV','RC','GT','GL')"
            ."    AND dias between 61 and 90"
		    ."    AND nit=empcod";
   
   
   $errcan = odbc_do($conexi,$querycan);
   $canti  = odbc_result($errcan,1);//cantidad para saber que no traiga nulos			
   
  IF ($canti > 0) 
  {
   //==============================
   // TRAE EL VALOR DE 61 A 90 DIAS 
   //==============================
   
  $query2="SELECT empnit nit,empnom res,sum(vlrsaldo) vlr61_90"
         ."   FROM amepuec,tmpemp"
         ."  WHERE estado in ('RD','RV','RC','GT','GL')"
         ."    AND dias between 61 and 90"
		 ."    AND nit=empcod"
         ."  GROUP by 1,2"
         ."  into temp tmp61_901";
		    
  //echo $query2."<br>";
  $err_o2 = odbc_do($conexi,$query2);

  $query21="SELECT sum(vlr61_90)"
          ."  FROM tmp61_901";
		  
  //echo $query21."<br>";
  $err_o21 = odbc_do($conexi,$query21);		  
          
  $Num_Filas = 0;	
	
  while (odbc_fetch_row($err_o21))
   {
	 $Num_Filas++;
	  	
	 $vlr61_90   = odbc_result($err_o21,1);//Vlr 61 A 90
   }
   }
   ELSE
   {
     $vlr61_90   = 0;
   }
   
   
   $querycan="SELECT count(*)"
            ."   FROM amepuec,tmpemp"
            ."  WHERE estado in ('RD','RV','RC','GT','GL')"
            ."    AND dias between 91 and 120"
		    ."    AND nit=empcod";
 
   
   $errcan = odbc_do($conexi,$querycan);
   $canti  = odbc_result($errcan,1);//cantidad para saber que no traiga nulos			
   
  IF ($canti > 0) 
  {
   //===============================
   // TRAE EL VALOR DE 91 A 120 DIAS 
   //===============================
   
  $query2="SELECT empnit nit,empnom res,sum(vlrsaldo) vlr91_120"
         ."   FROM amepuec,tmpemp"
         ."  WHERE estado in ('RD','RV','RC','GT','GL')"
         ."    AND dias between 91 and 120"
		 ."    AND nit=empcod"
         ."  GROUP by 1,2"
         ."  into temp tmp91_1201";
		    
  //echo $query2."<br>";
  $err_o2 = odbc_do($conexi,$query2);

  $query21="SELECT sum(vlr91_120)"
          ."  FROM tmp91_1201";

		  
  //echo $query21."<br>";
  $err_o21 = odbc_do($conexi,$query21);		  
          
  $Num_Filas = 0;	
	
    while (odbc_fetch_row($err_o21))
    {
	 $Num_Filas++;
	  	
	 $vlr91_120   = odbc_result($err_o21,1);//Vlr 91 A 120
    }
   }
   ELSE
   {
     $vlr91_120   = 0;
   }
   
   
   $querycan="SELECT count(*)"
            ."   FROM amepuec,tmpemp"
            ."  WHERE estado in ('RD','RV','RC','GT','GL')"
            ."    AND dias > 120"
		    ."    AND nit=empcod";

   
   $errcan = odbc_do($conexi,$querycan);
   $canti  = odbc_result($errcan,1);//cantidad para saber que no traiga nulos			
   
  IF ($canti > 0) 
  {
   //============================
   // TRAE EL VALOR > A 120 DIAS 
   //============================
   
  $query2="SELECT empnit nit,empnom res,sum(vlrsaldo) vlr120"
         ."   FROM amepuec,tmpemp"
         ."  WHERE estado in ('RD','RV','RC','GT','GL')"
         ."    AND dias > 120"
		 ."    AND nit=empcod"
         ."  GROUP by 1,2"
         ."  into temp tmp1201";
		    
  //echo $query2."<br>";
  $err_o2 = odbc_do($conexi,$query2);

  $query21="SELECT sum(vlr120)"
          ."  FROM tmp1201";
		  
  //echo $query21."<br>";
  $err_o21 = odbc_do($conexi,$query21);		  
          
  $Num_Filas = 0;	
	
    while (odbc_fetch_row($err_o21))
    {
	 $Num_Filas++;
	  	
	 $vlr120   = odbc_result($err_o21,1);//Vlr > A 120
    }
   }
   ELSE
   {
     $vlr120   = 0;
   }
   
   //======================
   // TRAE EL VALOR ABONOS
   //======================
   
   $query29="SELECT vlrabono"
         ."  FROM amepuec,tmpemp"
         ." WHERE nit=empcod"
         ." GROUP BY 1"
		 ." INTO TEMP tmpabono";		 
	         
  //echo $query29."<br>";
  
  $err_o29 = odbc_do($conexi,$query29);	
   
  $query2="SELECT sum(vlrabono)"
         ."  FROM tmpabono";
         
  //echo $query2."<br>";
  
  $err_o21 = odbc_do($conexi,$query2);		  
          
  $Num_Filas = 0;	
	
  while (odbc_fetch_row($err_o21))
   {
	 $Num_Filas++;
	  	
	 $vlrabonos   = odbc_result($err_o21,1);//Vlr abonos
   }
   
   //==================================
   // TRAE EL VALOR EN PROCESO DE ENVIO
   //==================================
   
  $query2="SELECT sum(proenv)"
         ."  FROM amepuec,tmpemp"
         ." WHERE nit=empcod";      
		
  //echo $query2."<br>";
  
  $err_o21 = odbc_do($conexi,$query2);		  
          
  $Num_Filas = 0;	
	
  while (odbc_fetch_row($err_o21))
   {
	 $Num_Filas++;
	  	
	 $vlrproenv   = odbc_result($err_o21,1);//Vlr en procesos de envio
   }
   
   $totalradi=$vlr1_30+$vlr31_60+$vlr61_90+$vlr91_120+$vlr120;
   $subtotal=$totalradi+$vlrabonos;
   $totgeneral=$subtotal+$vlrproenv;
   
    echo "<tr>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>ENTIDAD</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>0-30 DIAS</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>31-60 DIAS</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>61-90 DIAS</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>91-120 DIAS</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>> 120 DIAS</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>TOTAL RADICADO</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>ABONOS POR APLICAR</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>SUBTOTAL CARTERA</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>EN PROCESO DE ENVIO</b></font></td>";
	echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>TOTAL GENERAL</b></font></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td bgcolor='#FFFFFF' align='CENTER' ><font size='2' text color='#003366'><b>".$nit1_30."</b></font></td>";
    echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($vlr1_30,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($vlr31_60,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($vlr61_90,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($vlr91_120,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($vlr120,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($totalradi,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($vlrabonos,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($subtotal,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($vlrproenv,0,'.',',')."</font></td>";
	echo "<td bgcolor='#FFFFFF' align='CENTER'><font size='2' text color='#003366'>".number_format($totgeneral,0,'.',',')."</font></td>";
	echo "</tr>";
	 
  
  echo "<tr>";
  echo "<td colspan='11'>&nbsp;</td>";
  echo "</tr>";
  

  echo "<tr>";
  echo "<td align=LEFT colspan='2' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>REALIZADO POR :</b></font></td>";
  echo "<td align=LEFT colspan='9' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>".$emple."</b></font></td>";
  echo "</tr>";
	 	 
  echo "<tr>";
  echo "<td align=LEFT colspan='2' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VoBo COORDINADOR :</b></font></td>";
  echo "<td align=LEFT colspan='9' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>HECTOR ARIAS RICO</b></font></td>";
  echo "</tr>";
  
  echo "</table>";
  
  // para cerrar la conexion con UNIX.
  liberarConexionOdbc( $conexi );
  odbc_close_all();
 } 
  
}

?>

