<head>
  <title>HISTORIA UNIADO V.1.04</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	      REPORTE DE HISTORIAS CLINICAS          *
	*	  PARA LA UNIDAD DE ADOLESCENTES	V.1.04	 *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	
	///header( "Content-type: application/msword" );
    ///header( "Content-Disposition: inline, filename=HISTORIA.rtf");
            
      
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac)  )
	{
				
		echo "<form action='000001_uniado1.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>UNIDAD DE ADOLESCENTES</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1>MEDICO: </td>";	
		
		/* Si el medico no ha sido escogido Buscar a los pediatras registrados para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
		$query = "select Subcodigo,Descripcion from det_selecciones where medico='uniado' and codigo='002'order by Descripcion";
		
			
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		
			
		if($num>0)
		  {
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$med=$row[0]."-".$row[1];
											
				if($med==$medico)
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";				
			}
		  }	// fin del if $num>0
		
		  echo "</select></tr><tr><td bgcolor=#cccccc colspan=1>PACIENTE: </td>";	
		
				
			/* Si el paciente no esta set construir el drop down */
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			if(isset($med))
			{
				/* Si el medico ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
				$query = "SELECT Paciente, Fecha, Hora_data FROM uniado_000003 WHERE Profesional_Que_Atendio='".$medico."' ORDER BY Paciente ";
												
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						$paci=$row[0]."-".$row[1]."-".$row[2]."-".$row[3]."-".$row[4]."-".$row[5]."-".$row[6];
						if($pac==$paci)
						  echo "<option selected>".$paci."</option>";
						else
							echo "<option>".$paci."</option>";
					}
				}	// fin $num>0
			}	//fin isset medico

		echo"</select></td></tr><tr><td align=center bgcolor=#cccccc></td>";
		echo"<td align=center bgcolor=#cccccc>Todo <input type='checkbox' name='cert'></td>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
			
	}
	else 
	 /******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	{
	
		$ini1=strpos($medico,"-");
		$reg=substr($medico,0,$ini1);
		$nomed=substr($medico,$ini1+1);
		
		
		$ini1=strpos($pac,"-");
		$ap1=substr($pac,0,$ini1);
		$ini2=strpos($pac,"-",$ini1+1);
		$ap2=substr($pac,$ini1+1,$ini2-$ini1-1);
		$ini3=strpos($pac,"-",$ini2+1);
		$n1=substr($pac,$ini2+1,$ini3-$ini2-1);
		$ini4=strpos($pac,"-",$ini3+1);
		$n2=substr($pac,$ini3+1,$ini4-$ini3-1);
		$ini5=strpos($pac,"-",$ini4+1);
		$id=substr($pac,$ini4+1,$ini5-$ini4-1);
		//$pacn=$n1."-".$n2."-".$ap1."-".$ap2."-".$id;
		$pacn=$ap1."-".$ap2."-".$n1."-".$n2."-".$id;
		
		$ini6=strpos($pac,"-",$ini5+1);
		$wfecha=substr($pac,$ini5+1,10);
		$ini7=strpos($pac,"-",$ini6+1);
		$whora=substr($pac,$ini7+4,8);
					
		/*query a la historia clinica*/
		$querya = "         SELECT * ";
		$querya = $querya."   FROM uniado_000003 ";
		$querya = $querya."  WHERE Paciente='".$pacn."'";
		$querya = $querya."    AND Profesional_Que_Atendio='".$medico."'";
		$querya = $querya."    AND Fecha = '".$wfecha."'";
		$querya = $querya."    AND Hora_data = '".$whora."'";
		$querya = $querya."  ORDER BY Paciente ";
		
		//echo "Query : ".$querya;
		
		$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);
						
		if($num>0)
		{
			$row = mysql_fetch_row($err);
			for ($l=0;$l<=$num;$l ++)
				if($row[$l] == "NO APLICA" or $row[$l] == ".")
			   		$row[$l]="";
			
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
     		
            $wcol = 1;
                    
			for ($j=1; $j <= ($num); $j++)
			 {
              echo "<table align=center border=1 width=670 >";
              
              //echo "<img SRC='/MATRIX/images/medical/uniado/uniado.jpg' width='100' height='137' align=left>";
              echo "</BR>";
              echo "</BR>";
              echo "</BR>";
              
              
              echo "&nbsp"; 
                                         
              echo "<BR><B><font size=5 color='#000080' face='book antiqua' align=left>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;HISTORIA CLINICA ADOLESCENTES</font></B></BR>";
              echo "</BR>";
              echo "</BR>";
              echo "</BR>";
                                                        
              for ($f=2; $f <= ($fields-3); $f++) 
                {
	                switch ($f)
		                {
			             // ESTOS SON LOS CAMPOS TIPO TITULO
			             case 18:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Revisión por Sistemas</B></td></tr>";
			                break;
			             case 36:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Antecedentes Personales</B></td></tr>";
			                break;
			             case 52:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Antecedentes Familiares</B></td></tr>";
			                break;
			             case 66:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Familia</B></td></tr>";
			                break;   
		                 case 83:
		                    $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Educación</B></td></tr>";
			                break;
			             case 92:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Vida Social</B></td></tr>";
			                break;
			             case 103:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Trabajo</B></td></tr>";
			                break;
			             case 111:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Habitos</B></td></tr>";
			                break;
			             case 125:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Situación Psicoemocional</B></td></tr>";
			                break;
			             case 132:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Gineco-Urológico</B></td></tr>";
			                break;
			             case 149:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Sexualidad</B></td></tr>";
			                break;
			             case 164:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Examen Físico</B></td></tr>";
			                break;
			             case 202:
			                $wcol=1;
			                echo "<tr><td colspan=4 bgcolor=#cccccc><font size=4><B>Diagnóstico Integral</B></td></tr>";
			                break;
			            
			             default: 
				            { 	
					         /* OJO OJO OJO OJO OJO OJO
					         /* Si se cambia algun numero del if que sigue, se debe cambiar tambien en el if de abajo que debe tener los mismos numeros   			             
				             /* Desde aca hago el if para los campos tipo Observacion, para que abarquen cuatro columnas y los imprima aparte */
			                 if (($f==14) or ($f==15) or ($f==16) or ($f==17) or ($f==35) or ($f==51) or ($f==65) or ($f==80) or ($f==82) or ($f==91) or ($f==102) or ($f==110) or ($f==124) or ($f==131) or ($f==148) or ($f==163) or ($f==166) or ($f==174) or ($f==176) or ($f==178) or ($f==180) or ($f==182) or ($f==184) or ($f==186) or ($f==188) or ($f==190) or ($f==197) or ($f==199) or ($f==201) or ($f==205) or ($f==206) or ($f==207) or ($f==208) or ($f==209) or ($f==210) or ($f==211))
			                   {
			                    $wcol=1;
			                    echo "</tr>";
			                    
			                    /// Busco si el dato tiene un guion, si lo tiene tomo el dato de ese punto en adelante. los campos 136 y 142 son tipo fecha, por esta razon deben conservar los guiones
			                    if (strpos($row[$f],"-",0) > 0 )      //Busco el guion que esta separando el codigo del dato (codigo, descripcion) para quitarlo en la impresion
         	                          {
	         	                       $wc=strpos($row[$f],"-",0);
         	                           $dato = substr($row[$f],$wc+1,strlen($row[$f]));
         	                          }
					                 else 
					                    $dato = $row[$f];
					                    
			                    if (strpos(mysql_field_name($err, $f),"_",0) > 0)                                     //Busco si el nombre del campo en la tabla tiene underline
         	                       // Aca imprimo los campos de observaciones
         	                       echo "<td colspan=4><B>".str_replace("_"," ",mysql_field_name($err, $f)).":   "."</B>".$dato."</td>";  //Reemplazo los underline por un espacio
                                  else
                                    {
	                                 // Aca imprimo los campos de observaciones
                                     echo "<td colspan=4><B>".mysql_field_name($err, $f).":   "."</B>".$dato."</B></td>";
                                    }
			                    echo "</tr>";
		                       }
				              else             
				                 //////////////////////////////////////////////////////////////////////////////////////
				                 /// echo "Campo : ".mysql_field_name($err, $f)." Columna : ".$f." wcol = ".$wcol;  ///
				                 //////////////////////////////////////////////////////////////////////////////////////
				             	 {		
					              if ($f < 14)       // 14 es la fila donde esta el 1er motivo de consulta
					                 $columnas = 3;  // Aca se define la cantidad de columnas del reporte
					                else 
					                   if ($f==14)
					                      {
						                   echo "</table><p>";
					                       echo "<table align=center border=1 width=725 >";
				                          } 
					                      
					                    	                
				                  if ($wcol > $columnas)    //Máximo de columnas por fila
				                     {
				                      echo "</tr>";
				                      $wcol = 1; 
				                     }
			                        else 
			                           { 
				                        ////////////////////////////////////////////////////////////////////////////////
					                    ///    echo $row[$f]." Columna : ".$f."Campo : ".mysql_field_name($err, $f); ///
					                    ///    if ($row[$f] == 40)                                                   ///
					                    ///       echo mysql_field_name($err, $f);                                   ///
					                    ////////////////////////////////////////////////////////////////////////////////  
					                    if ($f > 5)
					                       $wc = strpos($row[$f],"-",0);   //Busco el guion que esta separando el codigo del dato (codigo, descripcion) para quitarlo en la impresion
					                      else
					                         $wc=0; 
			                                
				                          
					                    if ($wc == 0)  
					                      {                //Si es igual a cero es porque no tiene guiones
					                       if ($row[$f] == "on")       //si es booleano
					                          $dato = "Si";
					                      
                                           if ($row[$f] == "off")
					                          $dato = "No";
					                         else 
					                            if ($row[$f] != "on")   //No tiene guion y no es booleano, entonces lo paso completo
					                               $dato = str_replace("-"," ",$row[$f]);
					                      }     
				                          else
				                             {
				                              if ($f == 136 or $f == 142)   //Estos son datos tipo fecha y no se les puede quitar los guiones
				                                $wc=-1;
				                              $dato = substr($row[$f],$wc+1,strlen($row[$f]));
			                                 }
				                 						                        		                        
				                        //Si entra al siguiente if es porque las columnas terminan antes de 4 y el campo que sigue es de texto o de observaciones            
				                        if ((($f+1==14) or ($f+1==15) or ($f+1==16) or ($f+1==17) or ($f+1==35) or ($f+1==51) or ($f+1==65) or ($f+1==80) or ($f+1==82) or ($f+1==91) or ($f+1==102) or ($f+1==110) or ($f+1==124) or ($f+1==131) or ($f+1==148) or ($f+1==163) or ($f+1==166) or ($f+1==174) or ($f+1==176) or ($f+1==178) or ($f+1==180) or ($f+1==182) or ($f+1==184) or ($f+1==186) or ($f+1==188) or ($f+1==190) or ($f+1==197) or ($f+1==199) or ($f+1==201) or ($f+1==205) or ($f+1==206) or ($f+1==207) or ($f+1==208) or ($f+1==209) or ($f+1==210)) and $wcol < 4)
				                          { 
				                           if (strpos(mysql_field_name($err, $f),"_",0) > 0)                                               //Busco si el nombre del campo en la tabla tiene underline
                                                echo "<td colspan=".(4-$wcol+1).">"."<B>".str_replace("_"," ",mysql_field_name($err, $f)).":   "."</B>".$dato."</td>";  //Reemplazo los underline por un espacio
                                               else
                                                  echo "<td colspan=".(4-$wcol+1).">"."<B>".mysql_field_name($err, $f).":   "."</B>".$dato."</td>";
                                          }
				                          else                              
				                             if (strpos(mysql_field_name($err, $f),"_",0) > 0)                                               //Busco si el nombre del campo en la tabla tiene underline
                                                echo "<td><B>".str_replace("_"," ",mysql_field_name($err, $f)).":   "."</B>".$dato."</td>";  //Reemplazo los underline por un espacio
                                               else
                                                  echo "<td><B>".mysql_field_name($err, $f).":   "."</B>".$dato."</td>";
                                                  
                                        $wcol = $wcol +1; 
                                        if ($wcol > $columnas)
                                          {
                                           echo "</tr>";
                                           $wcol = 1; 
                                          }
                                       }
                                 }  
                             }
                                 
			                }
		           }
		           
		           //echo "<TR><TD colspan=".$columnas.">&nbsp</TR>";
		           //echo "<TR align=center><TD colspan=".$columnas."><B>Dirección: Carrera 47 No. 15 Sur 51.</B></TR>";
		           //echo "<TR align=center><TD colspan=".$columnas."><B>Telefono: 313-76-34</B></TR>";
		           
		           
		           //echo "<BR><B><font size=3 color='#000080' face='book antiqua' align=center>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Dirección: Carrera 47 No. 15 Sur 51.</font></B></BR>";
                   //echo "<BR><B><font size=3 color='#000080' face='book antiqua' align=center>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Telefono: 313-76-34</font></B>";
              
                }
                
                
                
                
                ///////////////////////////////////////////////////////////////////////////////////////////
                //// PRUEBA DE IMPRESION EN WORD                                                       ////
                ///////////////////////////////////////////////////////////////////////////////////////////
                ///////
///                 header( "Content-type: application/msword" );
///                 header( "Content-Disposition: inline, filename=HISTORIA.rtf");
/*
                 $date = date( "F d, Y" );
  
                 // open our template file
                 $filename = "HISTORIA.rtf";
                 $fp = fopen ( $filename, "r");

                 //read our template into a variable
                 $output = fread( $fp, filesize( $filename ) );
  
                 fclose ( $fp );
  
                 /// replace the place holders in the template with our data
                 //$output = str_replace( "<<NAME>>", strtoupper( $name ), $output );
                 //$output = str_replace( "<<Name>>", $name, $output );
                 //$output = str_replace( "<<score>>", $score, $output );
                 //$output = str_replace( "<<mm/dd/yyyy>>", $date, $output );
   
                 // send the generated document to the browser
                 ///echo $output;
                 fputs($filename,$output);
                 
*/    
                ///////////////////////////////////////////////////////////////////////////////////////////
                 
                
                
                
                
              ///echo "</table><p>";
             } 
    }
}
include_once("free.php");
?>