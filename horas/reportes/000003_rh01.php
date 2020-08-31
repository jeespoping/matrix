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
    /*************************************************************
	*	             LISTADO DEL REPORTE DE HORAS                *
	*	                     PARA NOMINA                         *
	*			        	CONEX, FREE => OK				     *
	**************************************************************
	* 2017-09-04 -Arleyda Insignares C. Se cambia la consulta a la 
	*             tabla 'noper' para agilizar tiempo de procesamiento.  
	*             
	* 2017-05-03 -Arleyda Insignares C. Cambio ODBC
	* 
	* 2017-08-30 -Arleyda Insignares C. Se cambia la forma en 
	*             que consulta el nombre utilizando un array
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

session_start();

if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	include_once("root/comun.php");	 
    
	
	$key = substr($user,2,strlen($user));
	
	//$wodbc=consultarAliasPorAplicacion($conex, $wemp_pmla, 'odbc_nomina');
    
    $wodbc=consultarAliasPorAplicacion($conex, $wemp_pmla, 'q7_odbc_nomina');

	$conexunix = odbc_connect($wodbc,'','') or die("No se ralizo Conexion con Q7");
	
	$wactualiz ="2017-09-04";
	
	$titulo = "LISTADO REPORTE DE HORAS";
	
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica"); 
	
	//llamo a la funcion que me trae el prefijo de las tablas a utilizar para cada una de las empresas
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
	
	echo "<form action='000003_rh01.php' method=post>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
    echo "<center><table  width=400>";
    echo "<tr class=encabezadoTabla><td align=center colspan=300><b>LISTADO DEL REPORTE DE HORAS DETALLADO</b></td></tr>";
    
	$wfecha_actual = date("Y-m-d");
    $wano_actual  = date("Y",strtotime($wfecha_actual)); 
    $wmes_actual = date("m",strtotime($wfecha_actual)); 
	
	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(Año, Mes, Quincena)
	if(!isset($wano)  or !isset($wmes) or !isset($wqui) or !isset($wcco))
	  {
	    //AÑO
        echo "<tr class=fila1><td ><b>Año:</b><select name='wano'>";
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
         
		 echo "<tr class=fila2>";
		 //traigo si es Usuario de Reporte de horas de las diferentes empresas
		 $UsuarioHoras  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'UsuarioRepHor');
		 
		 
		 //
		 if ($wusuario == "rephor" || $UsuarioHoras==$wusuario )
	        {  
		     
				 //CENTROS DE COSTO
				 echo "<td colspan = 3 nowrap='nowrap'><b>Centro de Costo :</b><select name='wcco'>";
				 $query = " SELECT ccocod, cconom "  
						 ."   FROM cocco "
						 ."  ORDER BY ccocod ";
							
				 $res = odbc_do($conexunix,$query);
				 echo "<option selected>*- Todos los centros de costo </option>";
    	    } 
    	   else
    	    {  
				
		         //CENTROS DE COSTO
			     echo "<td colspan = 3 nowrap='nowrap'><b>Centro de Costo :</b><select name='wcco' id='wcco'>";
			     $query = " SELECT ccocod, cconom "  
				  	     ."   FROM cocco "
					     ."  WHERE ccocod IN ".$nwcco." "
					     ."  ORDER BY ccocod ";
						
			     $res = odbc_do($conexunix,$query);
				 echo $query;
    	    }
	             
    	 //echo "<option selected>*- Todos los centros de costo </option>";
	     while(odbc_fetch_row($res))
	         {
		      echo "<option value ='".odbc_result($res,1)."-".odbc_result($res,2)."'>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	         }      
	      
	     echo "</SELECT></td></tr></table><br><br>";  
	     //////// Aca se termina la captura de las variables    
		 	     
	     echo"<tr class=fila1><td align=center  ><input type='submit' value='ACEPTAR'><input type='button' value='CERRAR VENTANA' onclick='cerrar()'></td></tr></form>";
	  } 
	else 
	 /******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	  {
		echo "<tr class=fila1><td align=center colspan=300><b>AÑO : ".$wano." - MES : ".$wmes." - QUINCENA : ".$wqui."</b></td></tr>";    
		echo "<tr class=fila2><td align=center colspan=300><b>CENTRO DE COSTO : ".$wcco." </b></td></tr>";  
		$wwcco = substr($wcco,0,strpos($wcco,"-")); 
		if ($wwcco == "*") 
		    $wwcco = "";

        $arr_empleado = array();


		// Llenar array de empleados para obtener el nombre

        $q = "    SELECT percod, perno1, perno2, perap1, perap2 "
            ."     FROM noper ";
            
       	   					            
        $res = odbc_do($conexunix,$q);

        while ( odbc_fetch_row($res) ){
                
                $arr_empleado[odbc_result($res,1)]= odbc_result($res,2)." ".odbc_result($res,3)." ".odbc_result($res,4)." ".odbc_result($res,5);
              
        }
    
	    /*query al reporte de horas*/
		$querya = "         SELECT Cco, Empleado, substr(Tipo_hora_dia,1,instr(Tipo_hora_dia,'-')-1) AS conc, sum(Cantidad) AS cant "
		                 ."   FROM ".$wbasedato."_000003"
		                 ."  WHERE Ano      = '".$wano."'"
		                 ."    AND Mes      = '".$wmes."'"
		                 ."    AND Quincena = '".$wqui."'"
		                 ."    AND Cco      like '%".$wwcco."%'"
		                 ."  GROUP BY Cco, Empleado, conc "
		                 ."  ORDER BY Cco, Empleado ";
		   
		$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);
						
		if($num>0)
		  {
			
		    echo "<tr>";
		    echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
		    echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "</tr>";
						
			echo "<tr class=encabezadoTabla>";
		    echo "<th >C.Costo</th>";
			echo "<th >Codigo</th>";
			echo "<th nowrap='nowrap'>Nombre Empleado</th>";
			echo "<th >Concepto</th>";
			echo "<th >Cantidad</th>";
			echo "</tr>";
		
			for ($l=0;$l<=$num;$l++)
			   {	

				 $row = mysql_fetch_row($err);  
				 $fields=mysql_num_fields($err);

				 if (is_int ($l/2))
				 {
				   	$wcf="fila1";  // color de fondo de la fila
				 }
				 else
				 { 
			       	$wcf="fila2"; // color de fondo de la fila
				 }	

				 echo "<tr class=".$wcf.">";

				 for ($j=0;$j<=$fields-1;$j++)
				   {
					   if ($j==2 or $j==3)      //Columna de concepto y cantidad
					   {
						 if ($j==2) echo "<td ALIGN=CENTER>".$row[$j]."</td>";   //Esta columna va centrada
						 if ($j==3) echo "<td ALIGN=RIGHT>".$row[$j]."</td>";   //Esta columna va justificada a la derecha 
					   }
					   else  
				          echo "<td>".$row[$j]."</td>";

				      // Buscar nombre del empleado en el array
				      if ($j==1) 
				         {
					       
					       $nomemp = $arr_empleado[$row[$j]];
				       
					       echo "<td nowrap='nowrap' >".$nomemp."</td>";
				         }   
					}
			     echo "</tr>";   
		       }
		  }
		 else
		    echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>NO EXISTEN DATOS EN MATRIX PARA LA QUINCENA DIGITADA</font></b></TD></TR></TABLE>";  
		echo "<table><td Width=300 aling=left><font size=3><A href=000003_rh01.php?wemp_pmla=".$wemp_pmla."> Retornar</A></td><td align=right><A href='javascript:onClick=window.close();' target='_top' > Cerrar</A></font></td><table>";
       }
	   
	odbc_close($conexunix);
	odbc_close_all();
}

?>