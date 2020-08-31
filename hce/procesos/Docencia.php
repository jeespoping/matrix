<?php
include_once("conex.php");
/***********************************************************************************************************
 *
 * Programa		:	Configuracion Docencia: Asociar los alumnos (Usuario Recidente) a los Médicos Docentes
 * Fecha		:	2016-05-31
 * Por			:	Arleyda Insignares Ceballos
 * Descripcion	:	Pestaña 'Docencia' en la configuracion de la Historia Electronica. 
 * 					Su funcion en grabar y/o adicionar el listado de Alumnos a un grupo de Médicos
 * 					Teniendo en Cuenta la Especialidad, vigencia y si es Recidente o no.
 *	
 *****************************************  Modificaciones ************************************************
 * 2019-12-05 Arleyda Insignares Ceballos
 *            -Se desactiva la función eliminarDocentes, la cual se utiliza para retirar los alumnos de los
 *             docentes que no sean seleccionados
 * 2018-08-17 Arleyda Insignares Ceballos
 *            -Se llama la función de consulta, posterior a la ejecución del grabado para que el usuario
 *             visualicé las modificaciones.
 *            -Se llama una función de confirmación en el momento de salir, solo en caso de que existan
 *             movimientos realizados.
 **********************************************************************************************************/
 
 $wactualiz = "2019-12-05";
 // ***************** Para que las respuestas ajax acepten tildes y caracteres especiales ******************
 if(!isset($_SESSION['user'])){
	echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

    //********************************** Inicio  ***********************************************************

	include_once("root/comun.php");
	

	$conex        = obtenerConexionBD("matrix");
	$wbasedato    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasemovhos  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha       = date("Y-m-d");
	$whora        = (string)date("H:i:s");
	$pos          = strpos($user,"-");
	$wusuario     = substr($user,$pos+1,strlen($user));
	$wcodusu      = '';
    $wnomusu      = '';
    $wclausu      = '';
    $wfecven      = '';
    $usuario_exis = '';

      // ***************************************    FUNCIONES AJAX    **********************************************
       
        if (isset($_POST["accion"]) && $_POST["accion"] == "ActualizarUsuario"){
            $q=" UPDATE ".$wbasedato."_000020
			     SET usures = '".$residente."',
			         usudep = '".$validacion."'
			     WHERE usucod = '".$codigo_usu."'";

			$resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			echo $resp;
			return;        
        }

        if (isset($_POST["accion"]) && $_POST["accion"] == "validarResidente"){

        	$q = " SELECT Meduma, Medesp, concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) as nombre, B.Usualu, C.Espnom 
		       		   FROM ".$wbasemovhos."_000048 A INNER JOIN ".$wbasedato."_000020 B
			       		   ON A.Meduma = B.Usucod
			       		   INNER JOIN ".$wbasemovhos."_000044 C
	                       ON A.Medesp = C.Espcod   
		       		   WHERE A.Meduma != '' AND B.Usures != 'on' AND B.Usualu like '%".$codigo_usu."%'      		
		       		   ORDER BY nombre ";

				    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    $num = mysql_num_rows($res);
				    $cont1 = 0;

				    if ($num > 0){	 
			 			 while($row = mysql_fetch_assoc($res)){	
			 			 	    $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
				                $cont1++;				                
				                $asignado='<input type="checkbox" id="chkasignado" name="chkasignado" CHECKED>';
			            	    
			            	    $vresultado .= '<tr class="'.$clase.'"><td>'.$row["Meduma"].'</td><td>'.$row['nombre'].'</td><td align="center">'.$row['Espnom'].'</td><td align="center">'.$asignado.'</td>
				         			<td><input type="hidden" id="txtcodalumno" name="txtcodalumno" value="'.$row["Meduma"].'"></td></tr>';
			 			 }
			        }
			       echo $vresultado;
		           return;     
        }
      
		if (isset($_POST["accion"]) && $_POST["accion"] == "SeleccionarUsuario"){
           
			$q = " SELECT Usucod, Descripcion, Usucla, Usurol, Usufve, Usures, Usuest, Usualu, Usudep, Roldes, Espnom
		            FROM ".$wbasedato."_000020 A Inner Join usuarios B on A.usucod = B.codigo 
		                Inner Join ".$wbasedato."_000019 C on A.usurol = C.rolcod 
		                Inner Join ".$wbasemovhos."_000048 D on A.usucod = D.Meduma 
		                Inner Join ".$wbasemovhos."_000044 E on D.Medesp = E.Espcod 
		            WHERE A.usucod = '".$codigo_doc."' ";


		    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $num = mysql_num_rows($res);
		    if ($num > 0)
		       {
			       	 $row         = mysql_fetch_assoc($res);      
			       	 $vresultado  = $row['Descripcion'] . "," . $row['Usurol'] . "," . $row['Roldes'] . "," . $row['Usufve'] . "," . $row['Usures'] . "," . $row['Usuest'] . "," . $row['Usudep'] . "," . $row['Espnom'];
		       }
		       echo $vresultado;
		       return;	 
			}


        // Retira un Alumno de la tabla de Alumnos del Docente Seleccionado
        if (isset($_POST["accion"]) && $_POST["accion"] == "eliminarAlumnos"){			
			//Actualizo el campo usualu del usuario ingresado
		    $q=" UPDATE ".$wbasedato."_000020
			     SET usualu = '".$cod_alu."'
			     WHERE usucod = '".$cod_usu."'";
			$resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			echo $resp;
			return;
        }

        // --> 2019-11-14 Código eliminación Inactivo
/*		// Eliminar un Docentes de la tabla de Docentes Asignados
        if (isset($_POST["accion"]) && $_POST["accion"] == "eliminarDocentes"){
           
            //Actualizo el campo usualu del usuario ingresado
		    $q=" UPDATE ".$wbasedato."_000020
			     SET usualu = ''
			     WHERE usucod = '".$cod_doc."'";
			$resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			echo $resp;
			return;
        }


        // Retirar todos los Docentes de la tabla de Docentes Asignados
        if (isset($_POST["accion"]) && $_POST["accion"] == "eliminarDocentesall"){

			  $docentes = explode(",", $lis_doc);
			  foreach($docentes as $doc) {
				  $qq=" UPDATE ".$wbasedato."_000020 
					    SET usualu = ''
					    WHERE usucod = '".$doc."'";
              
				  $resp= mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
			  }
            echo $resp;
			return;
        }*/
   
        //Actualizo el campo usualu en la lista de Medicos seleccionados
        if (isset($_POST["accion"]) && $_POST["accion"] == "grabarAlumnos"){
        	
        	if ($residente =='on' or $validacion=='on')
        	{
				// Actualizo el grupo de alumnos a los Docentes Seleccionados
	            $docentes = explode(",", $cadena_doc);
	            $wgrupoalumno = '';
				foreach($docentes as $doc) {
						
						// Consultar el campo usualu para verificar si existe el codigo
						$q = " SELECT Usucod,Usualu 
					           FROM ".$wbasedato."_000020 B
					           WHERE Usucod = '".$doc."' ";
				        		        
						$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			   			$num2 = mysql_num_rows($res2);
					    if ($num2 > 0){
						    $row2 = mysql_fetch_assoc($res2);
						    $wgrupoalumno = $row2['Usualu'];				      
					    }  
                        
                        //  En caso de que no tenga asignado el alumno lo concatena al campo usualu
                        if (strpos($wgrupoalumno, $codigo_usu) == false) {
                            
	    					// Adicionar el alumno al campo usualu en caso de que no lo tenga	
	    					if (strlen($wgrupoalumno)>0)
	    					    $wcampo1 = ",".$codigo_usu;
	    					else
	    					    $wcampo1 = $codigo_usu;

						    $qq=" UPDATE ".$wbasedato."_000020 
							      SET Usualu = CONCAT(Usualu, '".$wcampo1."') 
							      WHERE usucod = '".$doc."' ";

							$resp2= mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
                        }
				}
				echo $resp2;

	            // Retiro el grupo de alumnos a los Docentes no seleccionados
			    $docentesret = explode(",", $cadena_ret);
				foreach($docentesret as $doc) {

					    // Consultar el campo usualu para verificar si existe el codigo
						$q = " SELECT Usucod,Usualu 
					           FROM ".$wbasedato."_000020 B
					           WHERE Usucod = '".$doc."' ";
				
						$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			   			$num2 = mysql_num_rows($res2);

					    if ($num2 > 0){

						    $row2 = mysql_fetch_assoc($res2);
						    $wgrupoalumno = $row2['Usualu'];				      
						    $docentestr   = explode(",", $wgrupoalumno);
						    $cadenanue    = '';
				            foreach($docentestr as $docret) {
					                if ($docret != $codigo_usu)
					                   { 
					                   	 if (strlen($cadenanue)<=1)
					                   	 	$cadenanue .= $docret;    
					                   	 else 
					                   	    $cadenanue .= ",".$docret;
					                   }					                   
					        
					        }
				  		
						    $qq=" UPDATE ".$wbasedato."_000020 
								     SET usualu = '".$cadenanue."' 
								  WHERE usucod = '".$doc."' " ;

							$resp2= mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
                           
						    }
					     }
					     echo $resp2;

        	}
            else
            {
	        	//Actualizo el campo usualu del usuario ingresado
			    $q=" UPDATE ".$wbasedato."_000020 
				     SET Usualu = '".$cadena_alu."'
				     WHERE Usucod = '".$codigo_usu."'";
				$resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				// Actualizo el grupo de alumnos a los Docentes Seleccionados
	            $docentes = explode(",", $cadena_doc);
				foreach($docentes as $doc) {
				  		
					    $qq=" UPDATE ".$wbasedato."_000020 
						     SET Usualu = '".$cadena_alu."'
						     WHERE Usucod = '".$doc."'";

						$resp2= mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
				}
				echo $resp;

				// --> 2019-11-14 Código eliminación Inactivo
/*	            // Retiro el grupo de alumnos a los Docentes no seleccionados
				$docentesret = explode(",", $cadena_ret);
				foreach($docentesret as $doc) {
				  		
					    $qq=" UPDATE ".$wbasedato."_000020 
						     SET Usualu = ''
						     WHERE Usucod = '".$doc."'";

						$resp2= mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
				}*/
            }
            return;
        }

        // Consulta la Especialidad, luego adiciona a la tabla Docentes, todos los Medicos de la misma especialidad
		if (isset($_POST["accion"]) && $_POST["accion"] == "validarEspecialidad"){
   
               $vresultado = '';
  			   // Se Consulta la Especialidad para el grabado de lista alumnos
			   $q = " SELECT Meddoc, Medesp, Usualu 
			          FROM ".$wbasemovhos."_000048 A Inner join ".$wbasedato."_000020 B
			          on A.Meduma = B.Usucod 
			          WHERE Meduma = '".$codigo_usu."' ";

			   $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			   $num2 = mysql_num_rows($res2);
			   
			   if ( $num2 > 0 ){
			        $row2 = mysql_fetch_assoc($res2);
			        $wespecialidad = $row2['Medesp'];
			        $wgrupoalumno  = $row2['Usualu'];
		       }else{
		            $wespecialidad = "";
		            $wgrupoalumno  = "";
		       }  
                
				// Consultar todos los Medicos con esta Especialidad
				if ($wespecialidad != '')
				{
		   			$q = " SELECT Meduma, Medesp, concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) as nombre, B.Usualu, C.Espnom 
			       		   FROM ".$wbasemovhos."_000048 A INNER JOIN ".$wbasedato."_000020 B
				       		   ON A.Meduma = B.Usucod
				       		   INNER JOIN ".$wbasemovhos."_000044 C
		                       ON A.Medesp = C.Espcod
			       		   WHERE A.Medesp = ".$wespecialidad." AND A.Meduma != '' AND B.Usures != 'on' 
			       		   ORDER BY nombre ";
	                
				    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    $num = mysql_num_rows($res);
				    $cont1 = 0;
				    if ($num > 0){	 
			 			while($row = mysql_fetch_assoc($res)){	
			 			 	    $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
				                $cont1++;
				                if ($row['Usualu']==$wgrupoalumno && $wgrupoalumno !='')
				                {
				                	$asignado='<input type="checkbox" id="chkasignado" name="chkasignado" CHECKED>';
			            	    }
			            	    else
			            	    {
			            	    	$asignado='<input type="checkbox" id="chkasignado" name="chkasignado">';
			            	    	
			            	    }
			            	    $vresultado .= '<tr class="'.$clase.'"><td>'.$row["Meduma"].'</td><td>'.$row['nombre'].'</td><td align="center">'.$row['Espnom'].'</td><td align="center">'.$asignado.'</td>
				         			<td><input type="hidden" id="txtcodalumno" name="txtcodalumno" value="'.$row["Meduma"].'"></td></tr>';
			 			}
			        }

                    if ($wgrupoalumno != '')
                    {
						// Seleccionar Docentes con Distinta especialidad pero incluidos en el grupo de Docentes
						$q = " SELECT Meduma, Medesp, concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) as nombre, B.Usualu, C.Espnom
							   FROM ".$wbasemovhos."_000048 A INNER JOIN ".$wbasedato."_000020 B
								   ON A.Meduma = B.Usucod
								   INNER JOIN ".$wbasemovhos."_000044 C
								   ON A.Medesp = C.Espcod
							   WHERE A.Medesp != '".$wespecialidad."' AND B.Usualu='".$wgrupoalumno."' AND Meduma != ''
							   ORDER BY nombre ";
						
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);
						$cont1 = 0;

						if ($num > 0){	 
							 while($row = mysql_fetch_assoc($res)){	
									$cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
									$cont1++;
									if ($row['Usualu']==$wgrupoalumno && $wgrupoalumno !='')
									{
										$asignado='<input type="checkbox" id="chkasignado" name="chkasignado" CHECKED>';
									}
									else
									{
										$asignado='<input type="checkbox" id="chkasignado" name="chkasignado">';
										
									}
									$vresultado .= '<tr class="'.$clase.'"><td>'.$row["Meduma"].'</td><td>'.$row['nombre'].'</td><td align="center">'.$row['Espnom'].'</td><td align="center">'.$asignado.'</td>
										<td><input type="hidden" id="txtcodalumno" name="txtcodalumno" value="'.$row["Meduma"].'"></td></tr>';
							 }
							
						}
			       }
			       echo $vresultado;

		        } 
		        return;
      }

      //Consulto Docente por codigo y devuelvo un tr para adicionar a la tabla de Docentes
      if (isset($_POST["accion"]) && $_POST["accion"] == "consultaDocente"){
      		 $vresultado='';
			 $q = " SELECT Meddoc, Meduma, Medesp, concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) as nombre, Espnom  
			          FROM ".$wbasemovhos."_000048 A
						  INNER JOIN ".$wbasemovhos."_000044 B
						  ON A.Medesp = B.Espcod
			          WHERE Meduma = '".$codigo_doc."' ";
		
			 $res   = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			 $num   = mysql_num_rows($res);   
		     $cont1 = 0;
			 if ($num > 0 ){
				while( $row= mysql_fetch_assoc($res) ){
				  $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
				  $cont1++;	

				  $vresultado .= '<tr class="'.$clase.'"><td>'.$row["Meduma"].'</td><td>'.$row['nombre'].'</td><td align="center">'.$row['Espnom'].'</td><td align="center"><input type="checkbox" id="chkasignado" name="chkasignado"></td><td><input type="hidden" id="txtcodalumno" name="txtcodalumno" value="'.$row["Meduma"].'"></td></tr>';
			      }
             }
		     echo $vresultado;			 	
		return;
      }

      //Consulto Alumno por codigo y devuelvo tr para adicionar a la tabla de Alumnos
      if (isset($_POST["accion"]) && $_POST["accion"] == "consultarAlumno"){

      		 $vresultado='';
			 $q = " SELECT codigo, descripcion as nombre, usufve, usuest, Espnom 
					FROM usuarios A INNER JOIN ".$wbasedato."_000020 B 
						   ON A.Codigo = B.Usucod
						   INNER JOIN ".$wbasemovhos."_000048 C 
						   on A.Codigo = C.Meduma 
						   INNER JOIN ".$wbasemovhos."_000044 D 
						   on C.Medesp = D.Espcod 
					WHERE A.codigo = '".$codigo_alu."' 
					   AND A.activo = 'A' ";
		
			 $res   = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			 $num   = mysql_num_rows($res);   
		     $cont1 = 0;
			 if ($num > 0 ){
				while( $row= mysql_fetch_assoc($res) ){
				  $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
				  $cont1++;	

			      $vresultado .= '<tr class="'.$clase.'"><td>'.$row['codigo'].'</td><td>'.$row['nombre'].'</td><td align="center">'.$row['Espnom'].'</td><td align="center">'.$row['usufve'].'</td>
			            <td align="center"><input type="button" name="btnretirar" value="Eliminar" onclick="Retiraralumno(this,\'1\',\''.$row["codigo"].'\')"></td><td><input type="hidden" id="txtcodalumno" name="txtcodalumno" value="'.$row['codigo'].'"></td></tr>';	
				}
             }
		     echo $vresultado;			 	
		     return;
      }

      // Consultar los datos del Usuario y construir el body de la tabla tbllistadoalumno
      if (isset($_POST["accion"]) && $_POST["accion"] == "validarUsuario"){
        
		$q = " SELECT Usucod, Descripcion, Usucla, Usurol, Usufve, Usures, Usuest, Usualu, Usudep, Roldes, Espnom
	           FROM ".$wbasedato."_000020 A Inner Join usuarios B on A.usucod = B.codigo 
				  Inner Join ".$wbasedato."_000019 C on A.usurol = C.rolcod 
				  Inner Join ".$wbasemovhos."_000048 D on A.usucod = D.Meduma 
				  Inner Join ".$wbasemovhos."_000044 E on D.Medesp = E.Espcod 
	           WHERE A.usucod = '".$codigo_usu."' ";
	
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num = mysql_num_rows($res);
		
	    if ($num > 0)
	    {
	       	 $row          = mysql_fetch_assoc($res);      
	       	 $vresultado   = $row['Descripcion'] . "|" . $row['Usurol'] . "|" . $row['Roldes'] . "|" . $row['Usufve'] . "|" . $row['Usures'] . "|" . $row['Usuest'] . "|" . $row['Usualu'] . "|" . $row['Usudep'] . "|" . $row['Espnom'];

	       	 $vresultado   .= ";";
             $valumnos     = $row['Usualu'] ;
		     $usuario_exis = true;
			 
			 if (strlen($valumnos) > 1)
			 {	
					 // Consultar el grupo de los Alumnos asignados al medico
					 $q = " SELECT codigo, descripcion as nombre, usufve, usuest, Espnom "
							."   FROM usuarios A Inner join ".$wbasedato."_000020 B "
							."   on A.Codigo = B.Usucod "
							."   INNER JOIN ".$wbasemovhos."_000048 C "
							."   on A.Codigo = C.Meduma "
							."   INNER JOIN ".$wbasemovhos."_000044 D "
							."   on C.Medesp = D.Espcod "
							."   WHERE B.usucod in (".$valumnos.") "
							."   AND (B.usures = 'on' or B.usudep = 'on') "
							."   AND A.activo = 'A' ";

					 $res   = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					 $num   = mysql_num_rows($res);   
				     $cont1 = 0;
					 if ($num > 0 ){
						 while( $row= mysql_fetch_assoc($res) ){
							  $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
							  $cont1++;	

							  $vresultado .= '<tr class="'.$clase.'"><td class="campoalu">'.$row['codigo'].'</td><td>'.$row['nombre'].'</td><td align="center">'.$row['Espnom'].'</td><td align="center">'.$row['usufve'].'</td><td align="center"><input type="button" name="btnretirar" value="Eliminar" onclick="Retiraralumno(this,\'1\',\''.$row["codigo"].'\')"></td><td><input type="hidden" id="txtcodalumno" name="txtcodalumno" value="'.$row['codigo'].'"></td></tr>';
					  	 }
					      	
					 }
	         }else{
			    	$vresultado .='va';
			 }
		}
		else{	$vresultado .='no';}
		echo $vresultado;
		return;
	}	
	
	// **********************************  TODAS LAS FUNCIONES DE PHP  *******************************************
	
	function consultarDocentes($wbasemovhos,$wbasedato,$conex,$wemp_pmla){	    
	    $strtipvar = array();		
		$q = " SELECT Meduma, Medesp, concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) as nombre 
       		   FROM ".$wbasemovhos."_000048 A 
				   Inner join ".$wbasedato."_000020 B 
				   on A.Meduma = B.Usucod 
       		   WHERE A.Meduma !='' AND (B.Usures='off' or B.Usures='') ";
         
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		$cont1 = 0;
		if ($num > 0)
		{	 
	 	    while($row = mysql_fetch_assoc($res)){	
	 			    $strtipvar[$row['Meduma']] = utf8_encode($row['nombre']);
	 		}
	    }
		return $strtipvar;
	}
	
	function consultarUsuarios($wbasedato,$conex,$wemp_pmla){	    
	    $strtipvar = array();
		$q = " SELECT codigo, descripcion
			   From usuarios A, ".$wbasedato."_000020 B 
			   Where A.codigo = B.usucod 		
			   And activo ='A'";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
		 	     $strtipvar[$row['codigo']] = utf8_encode($row['descripcion']);
		}
		return $strtipvar;
	}

	// **********************************  Consultar los Medicos Residentes  ****************************************
	function consultarResidentes($wbasedato,$conex,$wemp_pmla){	    
	    $strtipvar = array();
		$q = " SELECT codigo, descripcion
			   From usuarios A, ".$wbasedato."_000020 B 
			   Where A.codigo = B.usucod 
				   And A.Activo ='A'
				   And (B.Usures ='on' or B.Usudep='on') ";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
		 	  $strtipvar[$row['codigo']] = utf8_encode($row['descripcion']);
		}
		return $strtipvar;
	}

    // *****************************************         FIN PHP         ********************************************
	?>
	<html>
	<head>
		<title>Configuracion Docencia</title>
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>	
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
		<script type="text/javascript">
		  $(document).ready(function(){

            $("#tbllistaalumno").hide();
            $("#tbllistadocente").hide();
            $("#tblagregaralumno").hide();
            $("#tblagregardocente").hide();
            $("#tblbotones").hide();
            $("#tblmensaje").hide();
            $('#wcodusu').focus();			          	 	

			//  *****************************      Asignar busqueda Autocompletar Usuario      ************************
			var arr_usu  = eval('(' + $('#arr_usu').val() + ')');
            var usuarios = new Array();
			var index   = -1;
            for (var cod_usu in arr_usu)
            {
                index++;
                usuarios[index]                = {};
                usuarios[index].value          = cod_usu;
                usuarios[index].label          = cod_usu+'-'+arr_usu[cod_usu];
                usuarios[index].codigo         = cod_usu;
            }            

            $("#wcodusu").autocomplete({
		      source: usuarios,		      
              autoFocus: true,			      
		      select:     function( event, ui ){
                    var cod_sel = ui.item.codigo;
                    $("#wcodusu").attr("codigo",cod_sel);
                    SeleccionDocente(cod_sel);
                }
		    });   

            //  *****************************      Asignar busqueda Autocompletar Recidentes    ************************
		    var arr_res    = eval('(' + $('#arr_res').val() + ')');
            var residentes = new Array();
			var index      = -1;
            for (var cod_res in arr_res)
            {
                index++;
                residentes[index]                = {};
                residentes[index].value          = cod_res;
                residentes[index].label          = cod_res+'-'+arr_res[cod_res];
                residentes[index].codigo         = cod_res;
            }            

            $("#wcodres").autocomplete({
		      source: residentes,
		      select:     function( event, ui ){
                    var cod_sel = ui.item.codigo;
                    $("#wcodres").attr("codigo",cod_sel);
                    Agregaralumno(cod_sel);
                }
		    });

            //  *****************************      Asignar busqueda Autocompletar Docentes    ************************
          	var arr_doc   = eval('(' + $('#arr_doc').val() + ')');
            var docentes  = new Array();
			var index     = -1;
            for (var cod_doc in arr_doc)
            {
                index++;
                docentes[index]                = {};
                docentes[index].value          = cod_doc;
                docentes[index].label          = cod_doc+'-'+arr_doc[cod_doc];
                docentes[index].codigo         = cod_doc;
            }            

            $("#wcoddoc").autocomplete({
		      source: docentes,
		      select:     function( event, ui ){
                    var cod_sel = ui.item.codigo;
                    $("#wcoddoc").attr("codigo",cod_sel);
                    Agregardocente(cod_sel);
                }
		    });  

	      });  

	      // **************************************   Funciones Javascript   ************************************************
	      
	      // Llenar el array de docentes
	      function iniciardocentes()
          {
          	var arr_doc   = eval('(' + $('#arr_doc').val() + ')');
            var docentes  = new Array();
			var index     = -1;
            for (var cod_doc in arr_doc)
            {
                index++;
                docentes[index]                = {};
                docentes[index].value          = cod_doc;
                docentes[index].label          = cod_doc+'-'+arr_doc[cod_doc];
                docentes[index].codigo         = cod_doc;
            }            

            $("#wcoddoc").autocomplete({
		      source: docentes,
		      select:     function( event, ui ){
                    var cod_sel = ui.item.codigo;
                    $("#wcoddoc").attr("codigo",cod_sel);
                    Agregardocente(cod_sel);
              }
		    });  
          }

          function Ampliarventana()
		  { 
		      window.open(window.location.href, "TIPO","width=2000,height=1000,scrollbars=YES") ;
		      $( "#Cerrarventana" ).prop( "disabled", false );    
		  }


          function cerrarVentana()
		  {
		    if ($("#wedicion").val()=='1'){ 
			  	jConfirm("Esta seguro de salir?","Confirmar", function(respuesta){				
					if (respuesta == true) 
	                   window.close();
	                else
	                   return false;  			  
	            });
		    }else{
		    	window.close();
		    }
	      }


          //Limpiar los campos del formulario y ocultar las tablas
          function Iniciar()
          {
          	$('input:checkbox[name=alumnosall]').attr('checked',false);
          	$("input[type=text]").val('');
          	$("input[type=password]").val('');
          	$('input:checkbox').attr('checked',false);
          	$("#tbllistaalumno").hide();
          	$("#tbllistadocente").hide();    	
	        $("#tblagregaralumno").hide();   
			$("#tblagregardocente").hide();   
			$("#tblbotones").hide();	
			$('#wcodusu').focus();			          	 	
          }

          function Activarvalidacion()
          { 
            if ($('input:checkbox[name=wresidente]:checked').val()=='on'){
				$('input:checkbox[name=wvalidacion]').attr('checked',true);
			 }
          }

          function Verificarvalidacion(objeto)
          {
          	if ($('input:checkbox[name=wvalidacion]:checked').val()=='on')
          	{
	            var vrolnom = $("#wrol").val();
	            var vrolcod = vrolnom.split('-');
	            // Se desactiva por ser varias las especialidades permitidas
/*	            if  (objeto =='on' && (vrolcod[0] != '026' && vrolcod[0] != '036') )
	            	{ 
	            		alerta('El Rol no permite activar el campo Validacion');
	            		$("#wvalidacion").attr("checked", false);
	            	}*/
            }
            else 
            {
            	alerta('Recuerde retirar los Docentes');
            }
          }

          function Seleccionarmedicosall(obj)
          {
          	if ($('input:checkbox[name=alumnosall]:checked').val()=='on'){
				$('input:checkbox[name=chkasignado]').attr('checked',true);
			 }
			else {
				$('input:checkbox[name=chkasignado]').attr('checked',false);
			}
          }
 
          //Seleccionar los datos del usuario digitado o seleccionado en el campo wcodusu
          function SeleccionDocente(codigodoc)
          {
          	var wemp_pmla = $("#wemp_pmla").val();
          	if (codigodoc != '')
			 {
			 	$.post("Docencia.php",
				   {
						consultaAjax:   true,
						accion:         'SeleccionarUsuario',
						wemp_pmla:      wemp_pmla,
						codigo_doc:     codigodoc
					}, function(respuesta){
						if (respuesta.length>2)
						{
		                    var vusuario = respuesta.split(',');
	                        $("#wnomusu").val(vusuario[0]);
	                        $("#wrol").val(vusuario[1]+'-'+vusuario[2]);
	                        $("#fechaven").val(vusuario[3]);
	                        $("#wespecialidad").val(vusuario[7]);
	                       
							if (vusuario[4] == 'on')
		                        $("#wresidente").attr("checked", true);
		                    else
		                      	$("#wresidente").attr("checked", false);

							if (vusuario[5] == 'on')
		                        $("#wactivo").attr("checked", true);
		                    else
		                      	$("#wactivo").attr("checked", false);

							if (vusuario[6] == 'on')
		                        $("#wvalidacion").attr("checked", true);
		                    else
		                      	$("#wvalidacion").attr("checked", false);

	                      	$("#tbllistaalumno").hide();
				          	$("#tbllistadocente").hide();
					        $("#tblagregaralumno").hide();
							$("#tblagregardocente").hide();
							$("#tblbotones").hide();	
					    }
					});
			 }
          }

          //agregar alumno seleccionado a la tabla de alumnos a grabar
          function Agregaralumno(codigoalu)
          {
			 var wemp_pmla = $("#wemp_pmla").val();
			 var wcod_usu  = $("#wcodusu").val();
			 var vencoalu  = '0';

			 $("#wedicion").val('1');

			 // Seleccionar la lista de Alumnos para verificar que no exista
 			 $('#tbllistaalumno tbody tr').each(function() {
                var cadena = $(this).find("#txtcodalumno").val();
                if (cadena == codigoalu)
                {
                	vencoalu = '1';
                }
             });

             //Agregar Alumno a la tabla tbllistaalumno
             if (vencoalu=='0')
			 {
				$.post("Docencia.php",
				   {
						consultaAjax:   true,
						accion:         'consultarAlumno',
						wemp_pmla:      wemp_pmla,
						codigo_alu:     codigoalu
					}, function(respuesta){
	                    $("#tbllistaalumno").append(respuesta);
	                    $("#wcodres").val('');
	                    if (document.getElementById('wresidente').checked == false){
							  
							  document.getElementById("tblbotones").style.display   = "";
							  // Buscar Medicos con misma Especialidad
					          $.post("Docencia.php",
								    {
										consultaAjax:   true,
						                async :         false,							
										accion:         'validarEspecialidad',
										wemp_pmla:      wemp_pmla,
										codigo_usu:     wcod_usu
									}, function(respuesta){
					                   $("#tblmensaje").hide();
					                   $("#tbllistadocente tbody ").remove(0);
									   $("#tbllistadocente").append(respuesta);
									}); 
						}
					});
			  }else{
			  	alerta('El Alumno ya se encuentra seleccionado');
			  }
          }

          //agregar docente seleccionado a la tabla de docentes a grabar
		  function Agregardocente(codigodoc)
          {

          	 $("#wedicion").val('1');
			 var wemp_pmla = $("#wemp_pmla").val();
			 var vencodoce = '0';

          	 $('#tbllistadocente tbody tr').each(function() {
                
                var cadenadoc = $(this).find("#txtcodalumno").val();   
                if (cadenadoc == codigodoc)
                  	vencodoce = '1';
                             
             });

  			 if (vencodoce=='0')
			 {
				 $.post("Docencia.php",
				 {
						consultaAjax:   true,
						accion:         'consultaDocente',
						wemp_pmla:      wemp_pmla,
						codigo_doc:     codigodoc
				 }, function(respuesta){
	                    $("#tbllistadocente").append(respuesta);
	                    $("#wcoddoc").val('');
				 });
			  }
			  else 
			  {
			  	alerta('El Docente ya se encuentra seleccionado');
			  	$("#wcoddoc").html('');
			  }
          }


          // Eliminar Registros de la tabla tbllistadoalumno o tbllistadodocente
          function Retiraralumno(obj,tipo,wcod_fila)
          {
          	var wemp_pmla = $("#wemp_pmla").val();
			jConfirm("Esta seguro de Eliminar el Registro?","Confirmar", function(respuesta){				
				if (respuesta == true) {
					 var cadenalu ='';
		             var row  = obj.parentNode.parentNode;
		             row.parentNode.removeChild(row);			 			
	                 var wcod_usu  = $("#wcodusu").val();
				    if (tipo==1) // Retirar de la tabla de Alumnos
			           {
			           	  // Seleccionar la lista de Alumnos
			 			 $('#tbllistaalumno tbody tr').each(function() {
			                var cadena = $(this).find("#txtcodalumno").val();
			                cadenalu   = cadenalu+cadena + ',';
			             });
			             if( cadenalu.length > 0 ){
							var calumno = cadenalu.substring(0, cadenalu.length - 1);
						 }

				 		 $.post("Docencia.php",
							 {
								consultaAjax:   true,
				                async :         false,							
								accion:         'eliminarAlumnos',
								wemp_pmla:      wemp_pmla,
								cod_alu:        calumno,
								cod_usu:        wcod_usu
								}, function(respuesta){
									
									if(respuesta==1)									
									   alerta('El Alumno ha sido Retirado');									
				                }); 
				       }					

				} else {
				    alerta('El Registro no fue Eliminado');
				}
			});

          
          }

          //Retira los alumnos de la lista de docentes tabla tbllistadodocente
          function Retiraralumnoall(obj)
          {
          	var wemp_pmla = $("#wemp_pmla").val();

          	jConfirm("Esta seguro de Eliminar el listado de docentes?","Confirmar", function(respuesta){				
				if (respuesta == true) {
		          	     vdocente ='';
		          	     // Seleccionar la lista de Docentes
			 			 $('#tbllistadocente tbody tr').each(function() {
			                var cadenadoc = $(this).find("#txtcodalumno").val();
			                vdocente      = vdocente+cadenadoc + ',';
			             });
		                 
		                 if( vdocente.length > 0 )
								var cdocente = vdocente.substring(0, vdocente.length - 1);
							 
			             $("#tbllistadocente tbody ").remove(0);
		                
		            }
	           });
          }


          // Realizar dos grabados del campo usualu: para el usuario seleccionado y para el grupo de medicos de la tabla
          function Actualizar()
          {

             $("#wedicion").val('0');
          	 var totfilasalu = document.getElementById('tbllistaalumno').rows.length; 
          	 var totfilasdoc = document.getElementById('tbllistadocente').rows.length; 

          	 //alert(totfilasalu);
          	 //alert(totfilasdoc);
            
                 if (totfilasdoc>2 && (document.getElementById('alumnosall').checked == false) ){

             	 jConfirm("Esta seguro de grabar en la lista de docentes?","Confirmar", function(respuesta){				
					if (respuesta == true) 
	                   GrabarFormulario();
	                else
	                   return false		  
	             });
             }
             else
             {
             	GrabarFormulario();
             }

          }


          function GrabarFormulario()
          {

             var wemp_pmla   = $("#wemp_pmla").val();
             var wcod_usu    = $("#wcodusu").val();

             var cadenalu    = '';
             var calumno     = '';
             var vdocente    = '';
             var gradocente  = '';
             var retdocente  = '';
 			 var cadenadoc   = '';
 			 var nocadenadoc = '';
 			 var novdocente  = '';

 			 if(document.getElementById('wresidente').checked == true)
                var wresiden = 'on';
             else
             	var wresiden = 'off';

             if(document.getElementById('wvalidacion').checked == true)
                var wvalidacion = 'on';
             else
             	var wvalidacion = 'off';

 			 // Seleccionar la lista de Alumnos
 			 $('#tbllistaalumno tbody tr').each(function() {
                var cadena  = $(this).find("#txtcodalumno").val();
                cadenalu    = cadenalu+cadena + ',';
             });

             if( cadenalu.length > 0 ){
				var calumno = cadenalu.substring(0, cadenalu.length - 1);
			 }

			 // Seleccionar la lista de Docentes
		    $('#tbllistadocente tbody tr').each(function() {
	            var chk  = $(this).find("#chkasignado");
	            if (chk !== null) {
		            if (chk.prop('checked')) {                                            
			            cadenadoc = $(this).find("#txtcodalumno").val();
			            vdocente  = vdocente+cadenadoc + ',';
		            }
		            else
		            {
		                nocadenadoc = $(this).find("#txtcodalumno").val();
		                if (nocadenadoc != wcod_usu)
			               novdocente  = novdocente + nocadenadoc + ',';
		            }
	            }
             });

             // Retirar el ultimo caracter ','
             if( vdocente.length > 0 )
				gradocente = vdocente.substring(0, vdocente.length - 1);
			 
			 if( novdocente.length > 0 )
				retdocente = novdocente.substring(0, novdocente.length - 1);
			 

             vgrabado =0;
             // Actualizar los campos residente y validacion en Usuario
             $.post("Docencia.php",
			 {
				consultaAjax:   true,
                async :         false,						
				accion:         'ActualizarUsuario',
				wemp_pmla:      wemp_pmla,
				codigo_usu:     wcod_usu,
				residente:      wresiden,
				validacion:     wvalidacion
				}, function(respuesta){

					  vgrabado = 1;
					  alerta('La informacion ha sido actualizada');
				});


             // Grabar el alumno(s) al usuario diligenciado y los Docentes seleccionado en dicha tabla
			 $.post("Docencia.php",
			 {
				consultaAjax:   true,
                async :         false,						
				accion:         'grabarAlumnos',
				wemp_pmla:      wemp_pmla,
				codigo_usu:     wcod_usu,
				cadena_alu:     calumno,
				cadena_doc:     gradocente,
				cadena_ret:     retdocente,
				residente:      wresiden,
				validacion:     wvalidacion
				}, function(respuesta){

					if(respuesta>0 && vgrabado==0){
					   alerta('La informacion ha sido actualizada');
					   Consultar();
					}						
                }); 	

          }


          // Consultar los alumnos del usuario y los medicos de la misma especialidad
	      function Consultar()
	      {

			var wemp_pmla = $("#wemp_pmla").val();
			var wcod_usu  = $("#wcodusu").val();
			$.post("Docencia.php",
			{
				consultaAjax:   true,
                async :         false,							
				accion:         'validarUsuario',
				wemp_pmla:      wemp_pmla,
				codigo_usu:     wcod_usu
			}, function(respuesta){
   					  var vfila    = respuesta.split(';');
					  if (vfila[1]=='no' || vfila[0]=='no' || respuesta.substring(0,2)=='no'){ 
							$("input[type=text]").val('');
				          	$("input[type=password]").val('');
				          	$("#tbllistaalumno").hide();
				          	$("#tbllistadocente").hide();   
							$("#tblagregaralumno").hide();   
							$("#tblagregardocente").hide(); 
							$("#tblbotones").hide();  				          	 	
					      	document.getElementById("tblmensaje").style.display = "";
					        $("#tbllistadocente tbody").remove(0);
					        $("#tbllistaalumno tbody").remove(0);
					  }else{

					  	  // Cargar información Encabezado
	                      var vusuario = vfila[0].split('|');
	                      $("#wnomusu").val(vusuario[0]);
	                      $("#wrol").val(vusuario[1]+'-'+vusuario[2]);
	                      $("#fechaven").val(vusuario[3]);
                          $("#wespecialidad").val(vusuario[8]);
                          $("#wvalidacion").attr("disabled", false);
                          $("#wresidente").attr("disabled", false);	                      
	                      if (vusuario[4] == 'on')
	                          $("#wresidente").attr("checked", true);
	                      else
	                      	  $("#wresidente").attr("checked", false); 
	                      if (vusuario[5] == 'on')
	                          $("#wactivo").attr("checked", true); 
	                      else
	                      	  $("#wactivo").attr("checked", false); 
                          if (vusuario[7] == 'on')
	                          $("#wvalidacion").attr("checked", true); 
	                      else
	                      	  $("#wvalidacion").attr("checked", false); 

                          // Cargar información Detalle
						  $("#tblmensaje").hide();	                      	                      
	                      $("#tbllistadocente tbody ").remove(0);
	                      $("#tbllistaalumno tbody ").remove(0);  
	                      if (vfila[1] !='va' && document.getElementById('wresidente').checked == false) 
						     {$("#tbllistaalumno").append(vfila[1]);}
						  
						  document.getElementById("tbllistaalumno").style.display    = "";
						  document.getElementById("tblagregaralumno").style.display  = "";
						  document.getElementById("tbllistadocente").style.display   = "";
					      document.getElementById("tblagregardocente").style.display = "";
						  
						  if (vfila[1] != ''){
							      if (document.getElementById('wresidente').checked == true || document.getElementById('wvalidacion').checked == true)
							      {
							      	  $("#tblagregaralumno").hide();
							      	  $("#tbllistaalumno").hide();
							      	  // Buscar Docentes en caso de que sea residente o requiera Validacion
							          $.post("Docencia.php",
										    {
												consultaAjax:   true,
								                async :         false,							
												accion:         'validarResidente',
												wemp_pmla:      wemp_pmla,
												codigo_usu:     wcod_usu
											}, function(respuesta){
							                   $("#tblmensaje").hide();
							                   $("#tbllistadocente tbody ").remove(0); 
											   $("#tbllistadocente").append(respuesta);											   
											}); 
							      }else{

								      if (vfila[1] !='va' && document.getElementById('wresidente').checked == false){								      	  
										  document.getElementById("tblbotones").style.display     = "";

										  // Buscar Medicos con misma Especialidad
								          $.post("Docencia.php",
											    {
													consultaAjax:   true,
									                async :         false,							
													accion:         'validarEspecialidad',
													wemp_pmla:      wemp_pmla,
													codigo_usu:     wcod_usu
												}, function(respuesta){
								                   $("#tblmensaje").hide();
								                   $("#tbllistadocente tbody ").remove(0);
												   $("#tbllistadocente").append(respuesta);
												}); 
							           }
						          }
							}	  			
						 }
			});  
                              
	      }

	        // ********************************  FUNCION Sacar un mensaje de alerta con formato predeterminado  *************************
			function alerta(txt){
				$("#textoAlerta").text( txt );
				$.blockUI({ message: $('#msjAlerta') });
					setTimeout( function(){
								   $.unblockUI();
								}, 1800 );
			}

          // **************************************   Fin Funciones Javascript   ********************************************
	    </script>		
   </head>
	<body>		
		<?php 
		  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
		  $wtitulo="ADMINISTRACION DE SEGURIDAD Y ACCESO HCE";
		  encabezado($wtitulo, $wactualiz, 'clinica');
		  $arr_usu  = consultarUsuarios ($wbasedato,$conex,$wemp_pmla);
		  $arr_res  = consultarResidentes ($wbasedato,$conex,$wemp_pmla);	
		  $arr_doc  = consultarDocentes ($wbasemovhos,$wbasedato,$conex,$wemp_pmla);	
		?>
        <CENTER><table width='1000px' style='border: 1px solid blue'>
        <tr class=fila1>
		<td ><b>C&oacute;digo Usuario: </b></td>		
		<td width="20px"><input type='text' id='wcodusu' name='wcodusu' size=20 value="<?=$wcodusu?>" OnChange='SeleccionDocente(this.value)'></td>
        <td colspan=2><input type='text' id='wnomusu' name='wnomusu' readonly size=60 value="<?=$wnomusu?>"></td>
        </tr>
        <tr class=fila1>
        <td><b>Especialidad : </b></td><td> <input name='wespecialidad' id='wespecialidad' type='text' readonly size='47'></td>        
        <td width="300px" ><b>Fecha de Vigencia: </b>
        <input type='text' readonly='readonly' id='fechaven' name='fechaven' class=tipo3 >
		<td><b>Activo: </b><input type='checkbox' id='wactivo' name='wactivo' disabled='disabled'></td>
        </tr>
        <tr class=fila1>
        <td >
        <b>ROL:</b></td><td> <input name='wrol' id='wrol' type='text' readonly size='47' value="<?=$wclausu?>"></td>
        <td><b>Residente:</b><input type='checkbox' id='wresidente' name='wresidente' disabled='disabled' ></td>
        <td><b>Requiere Validaci&oacute;n:</b><input type='checkbox' id='wvalidacion' name='wvalidacion' disabled='disabled' onclick='Verificarvalidacion(this.value)'></td>
        </tr>
        </table>
        </CENTER>
        <table id='agregaralumno' name='agregaralumno' style='border: 1px solid blue'>
        </table>
        <br><br>
	    <center><table>
	    <tr>
	    <td>&nbsp;&nbsp;<input type='submit' name='Consultar' value='Consultar' onclick='Consultar()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Actualizar' value='Actualizar' onclick='Actualizar()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Iniciar' value='Iniciar' onclick='Iniciar()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Ampliar' value='Ampliar' onclick='Ampliarventana()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Salir' value='Salir' onclick='cerrarVentana()'></td>
	    </tr>
	    </table>
        </br></br>
        <table id='tblagregaralumno' name='tblagregaralumno'><tr class=fila1>
		<td width="495px" align='center'><b>Agregar Alumnos</b></td><td width="495px"><input type='text' id='wcodres' name='wcodres' size='50'></td>
		</tr>	
		</table>
        <table id='tbllistaalumno' name='tbllistaalumno' class='tbllistaalumno' style='border: 1px solid blue;visibility:none;'>
        <thead><tr class=fila1>
		<td colspan=5 align='center'><b>Lista de Alumnos</b></td>
		</tr>		
		<tr class=encabezadotabla>
		<td width="100px" align="center">Codigo</td><td width="470px" align="center">Nombre</td><td width="200px" align="center">Especialidad</td><td width="100px" align="center">Fecha Vigencia</td><td width="100px" align="center">Retirar</td>
		</tr>
		</thead>
		<tbody>
		</tbody>
		</table>
		</br></br>
		<center><table id='tblbotones' name='tblbotones'>
	    <tr>
	    <td>&nbsp;&nbsp;<input type='submit' name='Consultar' value='Consultar' onclick='Consultar()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Actualizar' value='Actualizar' onclick='Actualizar()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Iniciar' value='Iniciar' onclick='Iniciar()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Ampliar' value='Ampliar' onclick='Ampliarventana()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Salir' value='Salir' onclick='cerrarVentana()'></td>
	    </tr>
	    </table>
	    </br></br>
        <table  id='tblagregardocente' name='tblagregardocente'><tr class=fila1>        
		<td width="490px" align='center'><b> Todos los Medicos de la Especialidad</b><input type='checkbox' id='alumnosall' name='alumnosall' onclick="Seleccionarmedicosall(this);"></td><td width="494px" align='center'> Docente: <input type='text' id='wcoddoc' name='wcoddoc' size='40'><input type='button' name='btnretirar3' value='Eliminar todas las Filas' onclick='Retiraralumnoall(this)'></td>
		</tr>	
		</table>
		<table id='tbllistadocente' name='tbllistadocente' class='tbllistadocente' style='border: 1px solid blue;visibility:none;'>
        <thead><tr class=fila1>
		<td colspan=5 align='center'><b>Lista de Docentes</b></td>
		</tr>		
		<tr class=encabezadotabla>
		<td width="100px" align="center">Codigo</td><td width="466px" align="center">Nombre</td><td width="300px" align="center">Especialidad</td><td width="100px" align="center">Docente Asignado</td>
		</tr>
		</thead>
		<tbody>
		</tbody>     
        </table>
		<table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>No hay usuarios con el codigo ingresado</td></tr>
		</table>
		<div id='msjAlerta' style='display:none;'>
		<br><img src='../../images/medical/root/Advertencia.png'/>
		<br><br><div id='textoAlerta'></div><br><br>
		</div>
        <input type="HIDDEN" name="arr_usu" id="arr_usu" value='<?=json_encode($arr_usu)?>'>
        <input type="HIDDEN" name="arr_res" id="arr_res" value='<?=json_encode($arr_res)?>'>
        <input type="HIDDEN" name="arr_doc" id="arr_doc" value='<?=json_encode($arr_doc)?>'>
        <input type="HIDDEN" name="wedicion" id="wedicion" value='0'>
	</body>
	</html>