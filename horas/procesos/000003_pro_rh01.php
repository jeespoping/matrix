<head>
  <title>GENERAR NOVEDADES DEL REPORTE DE HORAS</title>
</head>
<body >
<BODY >
<script type="text/javascript">
function cerrar()
{
window.close();
}
</script>
<?php
include_once("conex.php");
  /***************************************************
	*  GENERACION DE NOVEDADES DEL REPORTE DE HORAS  *
	*	                PARA NOMINA                  *
	*			     CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{

    

	include_once("root/comun.php");
	

	
	$key = substr($user,2,strlen($user));
	$wodbc=consultarAliasPorAplicacion($conex, $wemp_pmla, 'odbc_nomina');
	$conexunix = odbc_connect($wodbc,'informix','sco')
  					    or die("No se ralizo Conexion con el Unix");
	
	
	
	$wactualiz="(Versión Abril 24 de 2008)";
	
	//=====================================================================================================================================//
	//M O D I F I C A C I O N E S :                                                                                                        //
	//=====================================================================================================================================//
	//                                                                                                                                     //
	//=====================================================================================================================================//
	//Abril 24 de 2008:                                                                                                                    //
	//Se modifica para que salga por defecto el año y el mes actual                                                                        //
	//=====================================================================================================================================//
	
	$titulo = "REPORTE DE RECARGOS Y HORAS EXTRAS DEL PERSONAL";
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica"); 
    //$wemp_pmla='02';
    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
	echo "<form action='000003_pro_rh01.php' method=post>";
    echo "<center><table  width=400>";
    echo "<tr class=encabezadoTabla><td align=center colspan=240><b>GENERACION AUTOMATICA DE NOVEDADES DEL REPORTE DE HORAS PARA NOMINA</b></td></tr>";
    echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";  
	$wano1=date("Y");
	$wmes1=date("m");
	
    
	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(Año, Mes, Quincena)
	echo "<tr class=fila1>";
	if(!isset($wano)  or !isset($wmes) or !isset($wqui) or !isset($wcco))
	  {
	     //AÑO
         echo "<td><font size=4><b>Año:</b></font><select name='wano'>";
            echo "<option selected>".$wano1."</option>";
	        for($f=2004;$f<2051;$f++)
	           echo "<option>".$f."</option>";
	     echo "</select>";
	  
		 //MES
	     echo "<td><font size=4><b>Mes :</b></font><select name='wmes'>";
         if($mes1 < 10)
            echo "<option selected>".$wmes1."</option>";
           else 
              echo "<option selected>".$wmes1."</option>";
	     for($f=1;$f<13;$f++)
	        if($f < 10)
	           echo "<option>0".$f."</option>";
	          else
	             echo "<option>".$f."</option>";
		 echo "</select>";
		   
	     //QUINCENA
	     echo "<td><font size=4><b>Quincena :</b></font><select name='wqui'>";
	     for($f=1;$f<3;$f++)
	       {
	        echo "<option>".$f."</option>";
	       } 
		   echo "</td></select></td></tr>";
	    echo "<tr class=fila2>";
		 //CENTROS DE COSTO
	     echo "<td colspan = 3><font size=4><b>Centro de Costo :</b></font><select name='wcco'>";
	     $query = " SELECT ccocod, cconom "
                 ."   FROM cocco "
                 ."  ORDER BY ccocod ";
                        
    	 $res = odbc_do($conexunix,$query);
	             
    	 echo "<option selected>*- Todos los centros de costo </option>";
	     while(odbc_fetch_row($res))
	         {
		      echo "<option value>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	         }      
	         echo "</SELECT></td></tr></table><br><br>";  
	     //////// Aca se termina la captura de las variables    
		   
		     
	     echo"<tr><td align=center colspan=3 ><input type='submit' value='ACEPTAR'><input type='button' value='CERRAR VENTANA' onclick='cerrar()'></td></tr></form>";
	  } 
	else 
	 /******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	  {
	   echo "<tr><td align=center colspan=240 ><font size=4 ><b>CENTRO DE COSTO : ".$wcco." </b></font></td></tr>";  
			
	   //Averiguo si la quincena en Nomina esta abierta en UNIX
	   $q= "       SELECT count(*) AS can ";
	   $q= $q."      FROM nocse ";
	   $q= $q."     WHERE cseano = '".$wano."'";
	   $q= $q."       AND csemes = '".$wmes."'";
	   $q= $q."       AND csenpm = '".$wqui."'";
	   $q= $q."       AND csefec is null ";             //si es nulo es porque esta abierta
	   
	   $res = odbc_do($conexunix,$q);
             
       if (odbc_result($res,1) >= 1)                    //If de quincena abierta
         {
			//Averiguo si la quincena en Nomina ya fue liquidada
	        $q= "       SELECT count(*) AS can ";
	        $q= $q."      FROM nocse ";
	        $q= $q."     WHERE cseano = '".$wano."'";
	        $q= $q."       AND csemes = '".$wmes."'";
	        $q= $q."       AND csenpm = '".$wqui."'";
	        $q= $q."       AND cseind = 'N' ";          //si NO esta liquidada este indicador esta eb 'N'
	   
	        $res = odbc_do($conexunix,$q);
             
            if (odbc_result($res,1) >= 1)               //If de quincena liquidada
		      { 
			    $wwcco = substr($wcco,0,strpos($wcco,"-")); 
				if ($wwcco == "*") 
				    $wwcco = "";
				    
				//Traigo el numero de la secuencia de la quincena
				$q = "    SELECT csesec "
				    ."      FROM nocse "
				    ."     WHERE cseano = '".$wano."'"
				    ."       AND csemes = '".$wmes."'"
				    ."       AND csenpm = '".$wqui."'";
				$res = odbc_do($conexunix,$q);
				$wsec= odbc_result($res,1);
				   
			    /*query al reporte de horas*/
				$querya = " SELECT Cco, Empleado, substr(Tipo_hora_dia,1,instr(Tipo_hora_dia,'-')-1) AS conc, sum(Cantidad) AS cant "
				         ."   FROM ".$wbasedato."_000003 "
				         ."  WHERE Ano      = '".$wano."'"
				         ."    AND Mes      = '".$wmes."'"
				         ."    AND Quincena = '".$wqui."'"
				         ."    AND Cco      like '%".$wwcco."%'"
				         ."  GROUP BY Empleado, conc "
				         ."  ORDER BY Empleado ";
				   
				$err = mysql_query($querya,$conex);
				$num = mysql_num_rows($err);
								
				if($num>0)
				  {
				    		
					for ($l=0;$l<=$num;$l++)
					   {				
						 $row = mysql_fetch_row($err);  
						 $fields=mysql_num_fields($err);
								
						 if ($row[1] <> "")
						   { 
						     //Borro el registro del empleado en el unix
						     $q=  "     DELETE FROM nonov "
						         ."      WHERE novano = '".$wano."'"
						         ."        AND novmes = '".$wmes."'"
						         ."        AND novnpm = '".$wqui."'"
						         ."        AND novcod = '".$row[1]."'"   // Codigo del empleado
						         ."        AND novcon = '".$row[2]."'";  // Concepto 
							 $res = odbc_do($conexunix,$q);         // Borro el registro 
						     						  	  
						     //Inserto el registro del empleado
						     $q= "      INSERT INTO nonov (   novano,     novmes  , novtip,   novsec  ,   novnpm  ,   novcod     ,   novcco  ,   novcon  , novcoi, novaju,  novhor, novval, novitg, novips, novfin, novaut) "
						        ."                VALUES ('".$wano."','".$wmes."', 'Q'   ,'".$wsec."','".$wqui."','".$row[1]."','".$row[0]."','".$row[2]."', ''    , 1     ,".$row[3].",0.0   , 'M'   , 'N'   , ''    , '' )";
						    $res = odbc_do($conexunix,$q);
					       }  
						}
				    echo "<tr><td align=center colspan=240 ><font size=4 ><b>TERMINO DE GENERAR LAS NOVEDADES</b></font></td></tr>";  
				    echo "</table>";
				  }
		      }  //Fin then del if de nomina o quincena liquidada
		     else
			    {
			     echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>LA QUINCENA DIGITADA YA HA SIDO LIQUIDADA NO SE PUEDEN GENERAR LAS NOVEDADES</font></b></TD></TR></TABLE>";   
			    } 
         } //Fin then del if de nomina abierta en UNIX
		else
		   {
		    echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>LA QUINCENA DIGITADA YA ESTA CERRADA EN NOMINA o NO EXISTE</font></b></TD></TR></TABLE>";   
		   } 
	   echo "<table><td Width=300 aling=left><font size=3><A href=000003_pro_rh01.php?wemp_pmla=".$wemp_pmla."> Retornar</A></td><td align=right><A href='javascript:onClick=window.close();' target='_top' > Cerrar</A></font></td><table>";
	  }  //Fin del else de todos los parametros setiados
	odbc_close($conexunix);
	odbc_close_all();
}

?>