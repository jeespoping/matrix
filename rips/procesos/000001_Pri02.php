<head>
  <title>FACTURAR CONSULTORIOS</title>
</head>

<?php
include_once("conex.php");

/*****************************************************************************
 * 
 * Actualizaciones:
 * 
 * Mayo 6 de 2009
 * Por: Edwin Molina Grisales
 * 
 * - La selección de fechas se hace por calendario
 * - Se agrega al informe el campo de concepto
 * 
 *****************************************************************************/

  include_once("root/comun.php");
  chdir ("../..");
  //$ruta="./matrix/images/medical/rips/";
  $ruta="./images/medical/rips";
  $dh=opendir($ruta);
     
  if(readdir($dh) == false) 
    {
	echo "<body BACKGROUND=".$ruta."fondo de bebida de limón.gif>";
    
    //echo "<tr><td colspan=4 align=center><font size=5><b><A href=".$ruta.">Haga Click Para Bajar los Archivos</A></b></font></td></tr>";
    
    }
  
  /***************************************************
	*	          FACTURAR CONSULTORIOS              *
	*	  PARA LA TORRE MEDICA LAS AMERICAS	V.1.00	 *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		            
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	
	
	                                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	$wactualiz = "(Actualizado a Mayo 06 de 2009)";                // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	
	$usuario = substr($user,strpos("-",$user)+2,strlen($user));
		
	$query = "select codigo,prioridad,grupo from usuarios where codigo='".$usuario."' and activo = 'A'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	$prioridad=$row[1];
	$usuario=$row[0];
	$grupo=$row[2];
		
	
	//echo "Consultorio : ".$consul."...Medico : ".$medico.".....Empresa : ".$emp.".....Añoi : ".$anoi.".....Mesi : ".$mesi.".....Diai : ".$diai.".....Añof : ".$anof.".....Mesf : ".$mesf.".....Diaf : ".$diaf.".....tipate : ".$tipate.".....factura : ".$Fact;
	
//	if(!isset($medico)  or !isset($emp)  or !isset($consul) or !isset($anoi) or !isset($mesi) or !isset($diai) or !isset($anof) or !isset($mesf) or !isset($diaf) or !isset($tipate) or !isset($Fact))
	if(!isset($medico)  or !isset($emp)  or !isset($consul) or !isset($wfechai) or !isset($wfechaf) or !isset($tipate) or !isset($Fact))
	  {
 		echo "<br>";				
		echo "<br>";
		echo "<br>";
		/*echo "<br>";
		echo "<br>";
		echo "<br>";*/
		
		echo "<form action='000001_Pri02.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=6 text color=#CC0000><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=6 text color=#CC0000><b>TORRE MEDICA LAS AMERICAS </b></font></td></tr>";
		echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=6 text color=#CC0000><b>FACTURAR EMPRESAS</b></font></td></tr>";
		echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";
		echo "<tr></tr>";
		
/*		
		echo ".....Consultorio : ".$consul."<br>";
        echo ".....Medico : ".$medico."<br>";
        echo ".....Empresa : ".$emp."<br>";
        echo ".....Añoi : ".$anoi."<br>";
        echo ".....Mesi : ".$mesi."<br>";
        echo ".....Diai : ".$diai."<br>";
        echo ".....Añof : ".$anof."<br>";
        echo ".....Mesf : ".$mesf."<br>";
        echo ".....Diaf : ".$diaf."<br>";
        echo ".....tipate : ".$tipate."<br>";
        echo ".....factura : ".$Fact."<br>"; 
*/		
		
		/*=============================================================================*/
		/*===== A C A   S E   S E L E C C I O N A   E L   C O N S U L T O R I O =======*/
		/*=============================================================================*/
		/*=============================================================================*/
		if (!isset($consul))
		 {
	       echo "<tr><td bgcolor=#cccccc colspan=1><font size=6><b>Seleccione el CONSULTORIO: </b></font></td>";	
	 		    	   
		  
   	       /* Si el medico no ha sido escogido Buscar a los medico registrados para 
	 	   construir el drop down*/
		   echo "<td bgcolor=#cccccc colspan=2><SELECT name='consul'>";
		   $query = "        SELECT rips_000005.Consultorio ";
		   $query = $query."   FROM root_000017, rips_000005 ";
		   $query = $query."  WHERE rips_000005.usuario = '".$usuario."'";
		   $query = $query."    AND rips_000005.consultorio = root_000017.consultorio ";
		   $query = $query."  GROUP BY Consultorio";
		   $err = mysql_query($query,$conex);
		   $num = mysql_num_rows($err);
					
		   echo "Query : ".$query;
		   
	       if($num>0)
		     {
			  for ($j=0;$j<$num;$j++)
			     {	
				  $row = mysql_fetch_array($err);
				  $consul=$row[0];
				  				  										
				  echo "<option selected>".$row[0]."</option>";
				  
				 }
			 }  	// fin del if $num>0
		     echo"</select></td></tr><tr><td align=center bgcolor=#cccccc></td>";
		     //echo"<td align=center bgcolor=#cccccc>Todo <input type='checkbox' name='cert1'></td>";
		     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
		     exit;   //Este exit hace que no muestre el resto campos $medico y $emp, pero luego de seleccionar el consultorio si los pide
	      }  
		
		
	    /*===========================================================================================*/
		/*===== A C A   E S T A   Y A   S E L E C C I O N A D O   E L   C O N S U L T O R I O =======*/
		/*===========================================================================================*/
		/*===========================================================================================*/
		echo "<tr><td bgcolor=#cccccc colspan=1><b>CONSULTORIO: </b></td>";	
	 	
   	    /* Si el medico no ha sido escogido Buscar a los medico registrados para 
	 	   construir el drop down*/
	 	echo "<td bgcolor=#cccccc colspan=2><SELECT name='consul'>";
		$query = "SELECT Consultorio FROM root_000017 WHERE Consultorio = '".$consul."' GROUP BY Consultorio";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
				   		   			
	    if ($num>0)
		  {
		   for ($j=0;$j<$num;$j++)
		     {	
		      $row = mysql_fetch_array($err);
			  $consul=$row[0];
											
			  echo "<option selected>".$row[0]."</option>";
			 }
		  }  // fin del if $num>0  
	         		
		
		/*====================================================================*/
		/*====== A C A   S E   S E L E C C I O N A   E L   M E D I C O =======*/
		/*====================================================================*/
		/*====================================================================*/
		echo "<tr><td bgcolor=#cccccc colspan=1><b>MEDICO: </b></td>";	
			
		/* Si el medico no ha sido escogido Buscar a los medico registrados para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><SELECT name='medico'>";
		$query = "SELECT Consultorio, Documento, Nombre FROM root_000017 WHERE Consultorio='".$consul."' ORDER BY Consultorio";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
				
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$medico=$row[0]."-".$row[1]."-".$row[2];
															
				echo "<option selected>".$row[0]."-".$row[1]."-".$row[2]."</option>";
			}
		}	// fin del if $num>0
			
			
		/*===================================================================*/
		/*===== A C A   S E   S E L E C C I O N A   L A    E M P R E S A ====*/
		/*===================================================================*/
		/*===================================================================*/
		echo "<tr><td bgcolor=#cccccc colspan=1><b>EMPRESA: </b></td>";	
		
		/* Si la empresa no ha sido escogida Buscar a las empresas registradas para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><SELECT name='emp'>";
		$query = "SELECT Codigo, Nit, Nombre, Maximo_de_pacientes "
		        ."  FROM rips_000002 "
		        ." WHERE seguridad like '%".$usuario."%' "
		        ." ORDER BY Codigo ";
					
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
					
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				//$emp=$row[0]."-".$row[1]."-".$row[2]."-".$row[3];
											
				echo "<option selected>".$row[0]."-".$row[1]."-".$row[2]."-".$row[3]."</option>";
								
			}
		}	// fin del if $num>0
		echo "</TABLE></BR>";
				
		/*====================================================================================================================*/
		/*======================================   F E C H A   I N I C I A L   ===============================================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
          
        echo "<center><table border=0>";  
        echo "<tr><center><td bgcolor=#cccccc><b>FECHA INICIAL DE LAS ATENCIONES </b><BR>";
		// inicialización de la ayuda (drop down de fecha)
		echo "<center>";
		campoFechaDefecto("wfechai",date("Y-m-d"));	//Selecciona fecha inicial con calendario
		echo "</center></td>";
		
		  ///echo "</TABLE></BR>";
		/*====================================================================================================================*/
		
		
		/*====================================================================================================================*/
		/*======================================     F E C H A   F I N A L     ===============================================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
          
        ///echo "<center><table border=2 width=400>";  
        ///echo "<tr><td bgcolor=#cccccc ><b>FECHA FINAL DE LOS RIPS:  </b>";
        echo "<td bgcolor=#cccccc><b>FECHA FINAL DE LAS ATENCIONES </b><BR>";
		// inicialización de la ayuda (drop down de fecha)
		echo "<center>";
		campoFechaDefecto("wfechaf",date("Y-m-d"));	//Selecciona fecha final con calendario
		echo "</center></td>";
        
	    echo "</TABLE></BR>";
		  
		/*====================================================================================================================*/
		
		 
		/*====================================================================================================================*/
		/*========== A C A   S E   S E L E C C I O N A   E L   T I P O   D E   A T E N C I O N   A   F A C T U R A R =========*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		echo "<center><table border=0 width=300>"; 
		echo "<tr><td bgcolor=#cccccc><b><center>TIPO DE ATENCION: </b>";	
			
		/* Si el medico no ha sido escogido Buscar a los medico registrados para 
		construir el drop down*/
		echo "<SELECT name='tipate'>";
		$query = "       SELECT Subcodigo, Descripcion ";
		$query = $query."  FROM det_selecciones ";
		$query = $query." WHERE Medico = 'rips' ";
		$query = $query."   AND Codigo = '06' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
				
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$tipate=$row[0]."-".$row[1];
															
				echo "<option selected>".$row[0]."-".$row[1];
			}
			echo "<option selected>Todos</option></td></center>";
		}	// fin del if $num>0	
		echo "</TABLE></BR>";
		
		
		/*====================================================================================================================*/
		/*====================================     N U M E R O   D E   F A C T U R A     =====================================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		echo "<center><table border=0 width=300>"; 
		echo "<tr><td bgcolor=#cccccc><center><b>FACTURA : </b><INPUT TYPE='text' NAME=Fact></center></td></tr>";
		
		echo "</TABLE></BR>";
		/*====================================================================================================================*/
		
		
		echo"</select></td></tr><tr><td align=center bgcolor=#cccccc></td>";
		//echo"<td align=center bgcolor=#cccccc>Todo <input type='checkbox' name='cert'></td>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
 	}
	else 
	 /********************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  **********************************/
	  {
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		/*=============  A C A   C O M I E N Z A   L A   F A C T U R A C I O N   D E   L O S   U S U A R I O S   =============*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
						
		//Medico
		
		$wmed = explode("-",$medico);
		$wconsul=$wmed[0];
		$wmedico=$wmed[1];
		$wnommed=$wmed[2];
		
		/*
		$pos1=strpos($medico,"-");
		$wconsul = substr($medico,0,$pos1);
		$pos2=strpos($medico,"-",$pos1+1);
		$wmedico = substr($medico,$pos1+1,$pos2-$pos1-1);
		$wnommed = substr($medico,$pos2+1,strlen($medico));
		*/
		
		//Empresa
		$wemp = explode("-",$emp);
		$wnitemp=$wemp[1];
		$wnomemp=$wemp[2];
		$wmaxpac=$wemp[3];
		/*
		$pos1=strpos($emp,"-");
		$wconsul = substr($emp,0,$pos1);
		$pos2=strpos($emp,"-",$pos1+1);
		$wnitemp = substr($emp,$pos1+1,$pos2-$pos1-1);
		$wnomemp = substr($emp,$pos2+1,strlen($emp));
		*/					
		
		if ($tipate == "Todos")
		   $tipate = "";
		
		/*============================================================================================================================*/
		/*======================================  A C A   F A C T U R O   L O S   U S U A R I O S  ===================================*/
		/*============================================================================================================================*/
		
		$canfac=0;
		//Busco si ya hay usuarios facturados con la factura que se digito en pantalla
		$q = "    SELECT count(*) as canfac ";                                    // Cantidad facturados
		$q = $q."   FROM rips_000001 ";
		$q = $q."  WHERE rips_000001.Empresa = '".$wemp[0]."-".$wemp[1]."-".$wemp[2]."'";
		$q = $q."    AND rips_000001.Profesional = '".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Tipo_de_atencion like '%".$tipate."%'";
		$q = $q."    AND rips_000001.Nro_factura = '".$Fact."'";
		$err=mysql_query($q,$conex);
		$row = mysql_fetch_array($err);
		$canfac=$row[0];
		
		
		$sinfac=0;
		//Busco si ya hay usuarios por facturar en el rango de fecha digitado
		$q = "    SELECT count(*) as sinfac ";                                    // Cantidad facturados
		$q = $q."   FROM rips_000001 ";
		$q = $q."  WHERE rips_000001.Empresa = '".$wemp[0]."-".$wemp[1]."-".$wemp[2]."'";
		$q = $q."    AND rips_000001.Profesional = '".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Tipo_de_atencion like '%".$tipate."%'";
		$q = $q."    AND rips_000001.Nro_factura = 'NO APLICA' ";
		$err=mysql_query($q,$conex);
		$row = mysql_fetch_array($err);
		$sinfac = $row[0];
				
		if ($canfac > 0)      // Hay usuarios que ya tienen la factura digitada
		   {
			if ($sinfac > 0)  // Hay usuarios sin facturar con los parametros dados, pero existen otros que tienen la factura digitada 
			   {
				echo "<CENTER><font size=5 text color=#CC0000><B>=================================================================================</B></FONT></CENTER>";
				echo "<CENTER><font size=5 text color=#CC0000><B>!!! LA FACTURA ".$Fact." YA FUE GENERADA ¡¡¡</B></FONT></CENTER>";
				echo "<CENTER><font size=4 text color=#CC0000><B>!!! Y EXISTEN USUARIOS SIN FACTURAR EN EL MISMO RANGO DE FECHAS PARA EL MEDICO Y EMPRESA SELECCIONADA ¡¡¡</B></FONT></CENTER>";
				echo "<CENTER><font size=4 text color=#CC0000><B>!!! DEBE FACTURAR CON OTRO NUMERO DE FACTURA ¡¡¡</B></FONT></CENTER>";
				echo "<CENTER><font size=5 text color=#CC0000><B>=================================================================================</B></FONT></CENTER>";
				echo "<br>";
				echo "<br>";
		       }	
		   }	
		  else
		     { 
			  if ($wmaxpac > 0 )
			    {
				 //Traigo los usuarios pendientes por facturar, para ir facturandolos uno a uno por documento y tipo de atencion, no por procedimiento   
				 $q = " SELECT Documento, Tipo_de_atencion "                                    
			         ."   FROM rips_000001 "
			         ."  WHERE Empresa          = '".$wemp[0]."-".$wemp[1]."-".$wemp[2]."'"
			         ."    AND Profesional      = '".$medico."'"
			         ."    AND Fecha_servicio   BETWEEN '".$wfechai."' AND '".$wfechaf."'"
		             ."    AND Tipo_de_atencion LIKE '%".$tipate."%'"             //Tipo de atencion
			         ."    AND Nro_factura      = 'NO APLICA' "
			         ."  GROUP BY 1,2 ";
			     $res=mysql_query($q,$conex);
				 $num = mysql_num_rows($res);
				  
				 for ($i=1;$i<=$num;$i++)
				    {
					 $row = mysql_fetch_array($res);
					    
					 if ($i<=$wmaxpac)
					   {
						$q = " UPDATE rips_000001 "                             
				            ."    SET Nro_factura      = '".$Fact."'"
				            ."  WHERE Empresa          = '".$wemp[0]."-".$wemp[1]."-".$wemp[2]."'"
				            ."    AND Profesional      = '".$medico."'"
				            ."    AND Fecha_servicio   BETWEEN '".$wfechai."' AND '".$wfechaf."'"
				            ."    AND Tipo_de_atencion = '".$row[1]."'"           //Tipo de atencion
				            ."    AND Documento        = '".$row[0]."'"           //Documento
				            ."    AND Nro_factura      = 'NO APLICA' ";         
				        mysql_query($q,$conex);	   
				       }	       
				    }
				    
				  if ($num > $wmaxpac)
				     {
					  ?>
					    <script>
					      alert ("QUEDAN PENDIENTES USUARIOS POR FACTURAR DE ESTA EMPRESA EN EL RANGO DE FECHAS DADO");
			            </script>
					  <?php  
				     }          
			    }       
			   else
			     {  
				  //Hay usuarios por facturar con los parametros dados y NO hay otros que tengan la factura
			      //Aca se actualizan los RIPS con el numero de factura digitado en pantalla
			      $q = " UPDATE rips_000001 "                             
			          ."    SET Nro_factura = '".$Fact."'"
			          ."  WHERE rips_000001.Empresa = '".$wemp[0]."-".$wemp[1]."-".$wemp[2]."'"
			          ."    AND rips_000001.Profesional = '".$medico."'"
			          ."    AND rips_000001.Fecha_servicio BETWEEN '".$wfechai."' AND '".$wfechaf."'"
			          ."    AND rips_000001.Tipo_de_atencion like '%".$tipate."%'"
			          ."    AND rips_000001.Nro_factura = 'NO APLICA' ";
				  mysql_query($q,$conex);
	             }  
	         } 
	         
		 //Select para poder generar el listado de lo facturado
		 $q = "    SELECT mid(Documento,1,locate('-',Documento)-1), ";                  // Documento de identificacion
		 $q = $q."		  mid(Documento,locate('-',Documento)+1,length(Documento)), ";  // Nombre del usuario
		 $q = $q."        Fecha_servicio, ";                                            // Fecha servicio
 		 $q = $q."        mid(tipo_de_atencion,locate('-',tipo_de_atencion)+1,length(tipo_de_atencion)) ,";											// Concepto
		 $q = $q."        Nro_autorizacion, ";                                          // Nro de autorizacion
		 $q = $q."        Valor_atencion ";                                             // Valor atencion
		 $q = $q."   FROM rips_000001 ";
		 $q = $q."  WHERE rips_000001.Empresa = '".$wemp[0]."-".$wemp[1]."-".$wemp[2]."'";
		 $q = $q."    AND rips_000001.Profesional = '".$medico."'";
		 $q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		 $q = $q."    AND '".$wfechaf."'";
		 $q = $q."    AND rips_000001.Tipo_de_atencion like '%".$tipate."%'";
		 $q = $q."    AND rips_000001.Nro_factura = '".$Fact."'";
				
		 //echo $q;			
		 $err = mysql_query($q,$conex);
		 $num = mysql_num_rows($err);
		 $canus = $num;
		  
		
	 if ($canus > 0)   //Si hay usuarios entro a generar la factura para imprimirla 
		{							
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
			
			include_once("rips/titulosfact.php");
			             
			$wtotal=0;
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  echo "<tr>";  
			  for ($i=0; $i <= ($fields-1); $i++)
			     {
				  if ($i == ($fields-1))   //Se hace este if porque solo se alinea a derecha la informacion del ultimo campo $i == ($fields-1)
				     echo "<td align=right>".number_format($row[$i],0,"",",")."</td>"; 
				  else if($i == 1)
				  	 echo "<td>".$row[$i]."</td>";
//				  else if ($i==0)			//Este if es para el formato de numero para la cedula
//				  	echo "<td align=center>".number_format($row[$i],0,"",",")."</td>";
				  else 
				  	 echo "<td align=center>".$row[$i]."</td>"; 
				  if ($i == ($fields-1))        //Si es el ultimo campo lo sumo porque corresponde al valor atencion
				     $wtotal = $wtotal+$row[$i];
				 }
			  echo "</tr>";
			  
			  //////////////////////////////////////////////////////////////////////////////////////////////////////
			  // ACA ROMPO PAGINA SEGUN UN NUMERO DE LINEAS COLOCADO COMO MAXIMO, El numero de lineas esta calculado
			  // para un tamaño de letra pequeño en el browser y con una margen izquierda y derecha de 9.05 milimetros
			  // y 19.05 milimetros de margen superior e inferior, esta parte se ajusta por la opcion configuracion
			  // de pagina en el browser y el tamaño de la letra por la opcion <ver> <tamaño de txto>.
			  //////////////////////////////////////////////////////////////////////////////////////////////////////
			 /* if ($j == 40)
			     {
				  echo "</table>";
				  echo "<div style='page-break-before: always'>";      //Aca parto pagina
			      include_once("/rips/titulosfact.php");
			     }
			 */
			 }
             echo "<tr><td colspan=5><B>TOTAL FACTURADO</B></td>";
//             echo "<td>&nbsp............</td>";
//             echo "<td>&nbsp............</td>";
//             echo "<td>&nbsp............</td>";
//             echo "<td>&nbsp............</td>";
             echo "<td align=right><B>".number_format($wtotal,0,"",",")."</B></td></tr>";
        } 
             
   	  }  // then del If de $canus	
    else //else del if de $canus
       {
	    echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<br>"; 
        echo "<center><font size=5 text color=#CC0000><b>!! NO HAY REGISTROS EN EL RANGO DE FECHAS PARA EL MEDICO Y EMPRESA SELECCIONADOS ¡¡</b></font></center><br>"; 	
       }
   	
   		
    }  // else del if de si esta setiado el $medico y la $emp
} // if de register
include_once("free.php");
?>