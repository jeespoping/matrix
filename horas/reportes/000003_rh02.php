<head>
  <title>LISTADO REPORTE DE HORAS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
function cerrar()
{
window.close();
}
</script>

<?php
include_once("conex.php");
  /***************************************************
	*	       LISTADO DEL REPORTE DE HORAS          *
	*	                PARA NOMINA                  *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
    

	include_once("root/comun.php");
	
	
	
	$key = substr($user,2,strlen($user));

	$wodbc     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'q7_odbc_nomina');
	//$wodbc=consultarAliasPorAplicacion($conex, $wemp_pmla, 'odbc_nomina');
	//$conexunix = odbc_connect($wodbc,'informix','sco')
	
	$conexunix = odbc_connect($wodbc,'','') or die("No se ralizo Conexion con el Unix");
		
	$wactualiz="2017-09-04";
	
	$titulo = "LISTADO REPORTE DE HORAS";
    
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
	// ******************   Modificaciones  **********************************************
	// 2017-09-04  - Arleyda Insignares C. Se cambia la consulta a la tabla noper para agilizar
	//               tiempo de procesamiento.  
	//               
	// 2017-05-17  - Arleyda Insignares C. Se modifica consulta a la tabla det_selecciones
	//               para generar la información multiempresa.
	//               
	// 2017-05-02  - Arleyda Insignares C. Se modifica ODBC. Nuevo software Nomina.
	//
	// ENERO 19 DE 2006
	// Se modifico el query principal para no traigo registros que tengan al gun campo nulo, esto causaba que el reporte no terminara.
	// Los datos que quedan nulos se graban en momentos que sucede algo extraño eln el sistema, por ejemplo algo que sucedio en la UCI, 
	// en donde los pantallazos mostraban algo que no se habia digitado, esto lo provoco un virus o el spyware que tenia el equipo.
	
	echo "<form action='000003_rh02.php' method=post>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
    echo "<center><table  width=400>";
    echo "<tr><td align=center colspan=300 class=encabezadoTabla ><b>LISTADO DEL REPORTE DE HORAS </b></td></tr>";
    
	$wfecha_actual = date("Y-m-d");
    $wano_actual   = date("Y",strtotime($wfecha_actual)); 
    $wmes_actual   = date("m",strtotime($wfecha_actual)); 
	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(Año, Mes, Quincena)
	if(!isset($wano)  or !isset($wmes) or !isset($wqui) or !isset($wcco))
	  {
	    //AÑO
        echo "<tr class=fila2><td><b>Año:</b><select name='wano'>";
        for($f=2004;$f<2051;$f++)
	       {
	        if($f == $wano_actual)
	          echo "<option selected>".$f."</option>";
	         else
	            echo "<option>".$f."</option>";
	       }
		   echo "</select>";
	  
		 //MES
	     echo "<td><b>Mes :</b><select name='wmes'>";
	     for($f=1;$f<13;$f++)
	       {
	        if($f == $wmes_actual)
	          if($f < 10)
	            echo "<option selected>0".$f."</option>";
	           else 
	              echo "<option selected>".$f."</option>";
		     else
		        if($f < 10)
		          echo "<option>0".$f."</option>";
		         else
		            echo "<option>".$f."</option>";
		   }
		   echo "</select>";
		   
	     //QUINCENA
	     echo "<td><b>Quincena :</b><select name='wqui'>";
	     for($f=1;$f<3;$f++)
	       {
	        echo "<option>".$f."</option>";
	       } 
		   echo "</td></select></td></tr>";
	    
		   
		 //Aca traigo el usuario, para poder saber ma adelante a que centro de costo pertenece  
		 $pos = strpos($user,"-");
		 $wusuario = substr($user,$pos+1,strlen($user)); 
                
		 //Aca selecciono los empleados que pertenecen al centro de costo de acuerdo al centro de costo que tiene asignado el usuario
		 //autorizado para ingresar a este proceso.
		 $q = "         SELECT Carne_nomina ";
		 $q = $q."        FROM ".$wbasedato."_000001 ";
		 $q = $q."       WHERE Usuario_matrix = '".$wusuario."'";
		  
		 $res = mysql_query($q,$conex);
         $row = mysql_fetch_array($res);
              
         if ($row[0] <> "" )   //Si es diferente de null, es porque el usuario esta autorizado a ingresar al proceso  
	       {	//If de usuario autorizado a entrar
	         $wcco = $row[0];
			 $arr_cco = explode(",",$wcco);
			 $nwcco= "('".implode("','",$arr_cco)."')";	  
	       }
         $UsuarioHoras  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'UsuarioRepHor');
		 
	     if ($wusuario == "rephor" || $UsuarioHoras==$wusuario )
	        {  
		     //CENTROS DE COSTO
	         echo "<tr class=fila1><td colspan = 3 nowrap='nowrap' ><b>Centro de Costo :</b><select name='wcco'>";
	         $query = " SELECT ccocod, cconom "  
                     ."   FROM cocco "
                     ."  ORDER BY ccocod ";
                        
    	     $res = odbc_do($conexunix,$query);
    	     echo "<option selected>*- Todos los centros de costo </option>";
    	    } 
    	   else
    	      {  
		       //CENTROS DE COSTO
	           echo "<tr class=fila1><td colspan=3 nowrap='nowrap' ><b>Centro de Costo :</b><select id='wcco' name='wcco'>";
	           $query = " SELECT ccocod, cconom "  
                       ."   FROM cocco "
                       ."  WHERE ccocod IN ".$nwcco.""
                       ."  ORDER BY ccocod ";
                        
    	       $res = odbc_do($conexunix,$query);
    	      }
	             
    	 ///echo "<option selected>*- Todos los centros de costo </option>";
	     while(odbc_fetch_row($res))
	         {
		      echo "<option value ='".odbc_result($res,1)."-".odbc_result($res,2)."'>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	         }
        			 
	     echo "</SELECT></td></tr>";
		 
		 //echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
		 echo"</table><br><br>";  
	     //////// Aca se termina la captura de las variables    
		 
	     
		 echo"<tr class=fila1><td align=center colspan=3 ><input type='submit' value='ACEPTAR'><input type='button' value='CERRAR VENTANA' onclick='cerrar()'></td></tr></form>";
	  } 
	else 
	 /******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	  {
		
		$arr_empleado = array();

		// Llenar array de empleados para obtener el nombre

        $q = "    SELECT percod, perno1, perno2, perap1, perap2 
                  FROM noper 
                  WHERE peretr='A' ";

        $res = odbc_do($conexunix,$q);

        while ( odbc_fetch_row($res) ){
                
                $arr_empleado[odbc_result($res,1)]= odbc_result($res,2)." ".odbc_result($res,3)." ".odbc_result($res,4)." ".odbc_result($res,5);
              
        }
		
		echo "<tr class=fila2><td align=center colspan=300><b>AÑO : ".$wano." - MES : ".$wmes." - QUINCENA : ".$wqui."</b></td></tr>";    
		echo "<tr class=fila1><td align=center colspan=300><b>CENTRO DE COSTO : ".$wcco." </b></td></tr>";  
		$wwcco = substr($wcco,0,strpos($wcco,"-")); 
		
		if ($wwcco == "*") 
		    $wwcco = "";
		   
        		   
		/*query al reporte de horas*/
		$querya = " SELECT Cco, Empleado, substr(Tipo_hora_dia,1,instr(Tipo_hora_dia,'-')-1) AS conc, sum(Cantidad) AS cant "
		         ."   FROM ".$wbasedato."_000003 "
		         ."  WHERE Ano      = '".$wano."'"
		         ."    AND Mes      = '".$wmes."'"
		         ."    AND Quincena = '".$wqui."'"
		         ."    AND Cco      like '%".$wwcco."%'"
		         ."    AND Cco      != '' "
		         ."    AND Empleado != '' "
		         ."  GROUP BY Cco, Empleado, conc "
		         ."  ORDER BY Cco, Empleado ";

		         		   
	 	$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);


		if($num>0)  //Si hay empleados con hora reportadas
		  {
			//Aca traigo todos los conceptos que existen
		    $q="  SELECT subcodigo "
		      ."    FROM det_selecciones "
		      ."   WHERE lcase(medico) = 'rephor' "     //Nombre del usuario dueño de la seleccion
		      ."     AND codigo  = '".$wemp_pmla."' "   //Codigo de la seleccion en la tabla det_selecciones
		      ."     AND activo = 'A' "
		      ."   ORDER BY subcodigo ";

			$res1 = mysql_query($q,$conex);
		    $num1 = mysql_num_rows($res1);
	   		   		  
			echo "<th class=encabezadoTabla>C.Costo</th>";
			echo "<th class=encabezadoTabla>Codigo</th>";
			echo "<th class=encabezadoTabla>Nombre Empleado</th>";
			
			if ($num1 > 0)  //Si hay conceptos
			  {
				for ($l=0;$l<=$num1;$l++)  //For de los conceptos
			       {
				    $row = mysql_fetch_row($res1);   
				    if ($row[0] <> "")
				       echo "<th class=encabezadoTabla>".$row[0]."</th>";
				    $conceptos[$l] = $row[0];
				    $indi = $l;
			       }
		      }
						
			//Inicializo la MATRIZ
		    for ($j=0;$j<=$num;$j++)
			   {
			    for ($l=0;$l<=$indi+4;$l++)
			       {
				    $matriz[$j][$l]=0;
			       }
		       }
			        
		
			//For de la consulta de todas las horas digitadas
			$row = mysql_fetch_row($err);   //Esta es una fila de la consulta que tiene CCo, CodEmp, Concepto, Cantidad
			$fields=mysql_num_fields($err); //Aca segun el query el valor debe ser 4
			$l1=0;
								
			for ($l=0;$l<=$num;$l++)        //$num es el numero de registros que tiene el query
			   {
				$wcodemp = $row[1];
				while ($row[1]==$wcodemp and $l<=$num and $wcodemp <> "" and $row[1] <> "" ) 
				    { 	
					 $l1=($l1+1);	
					 for ($j=0;$j<=$fields-2;$j++)                       //Aca es -2 porque empiezo desde 0 y mas adelante avanzo al otro campo forzado $row[$j+1]
					    {
						 if ($j==2)                                      //Campo correspondiente al concepto
					        { 
						     for ($i=$j+1;$i<=$indi+2;$i++)   
						        {
							     if ($conceptos[$i-3] == $row[$j])
								    {
									 $matriz[$l][$i] = $row[$j+1];       //Le paso la cantidad a la matriz
									}
							       else
							          {
								       if ($matriz[$l][$i] == "")
								         {   
									      $matriz[$l][$i] = "-";
						                 }
						              }
					            }
					        }
					       else
					         {
						      //Traigo el nombre del codigo del empleado   
						      if ($j==1) 
					             {

						          $nomemp = $arr_empleado[$row[$j]];
						       
						          $matriz[$l][$j] = $row[$j];
						          $matriz[$l][$j+1] = $nomemp;
						         }   
					            else 
					               {
						            $matriz[$l][$j] = $row[$j];
				                   }
				             } 
			            }
			         $row = mysql_fetch_row($err);                       //Avanzo un registro del query
			        }
	           }
				           
	        for ($l=0;$l<=$l1;$l++)
			   {
			    if (is_int ($l/2))
				{
				 $wcf="fila1";  // color de fondo de la fila
				}
				else
				{ 
			      $wcf="fila2"; // color de fondo de la fila
				}
				
				if ($matriz[$l][0] <> "0")    
				  { 
				   echo "<tr class=".$wcf.">";  
				   for ($j=0;$j<=($fields+$indi-2);$j++)
				      {
				       if ($j < 3)
				          echo "<td ALIGN=LEFT nowrap='nowrap'>".$matriz[$l][$j]."</td>";
				         else 
				            echo "<td ALIGN=RIGHT nowrap='nowrap'>".$matriz[$l][$j]."</td>";
			           }
				   echo "</tr>";
			      }
			   }	   
	      }
         else
            {
             echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>NO EXISTEN DATOS EN MATRIX PARA LA QUINCENA DIGITADA</font></b></TD></TR></TABLE>";  
             //echo "<font size=3><A href=000003_rh02.php?wemp_pmla=".$wemp_pmla."> Retornar</A></font>";
            }
       echo "<table><td Width=300 aling=left><font size=3><A href=000003_rh02.php?wemp_pmla=".$wemp_pmla."> Retornar</A></td><td align=right><A href='javascript:onClick=window.close();' target='_top' > Cerrar</A></font></td><table>";     
    }
	
	odbc_close($conexunix);
	odbc_close_all();
}

?>